<?php
include_once("conex.php");
include_once("root/comun.php");
include_once("root/barcod.php");
include_once( "movhos/movhos.inc.php" );
include_once( "movhos/movhos.inc.php" );
include_once("./../../interoperabilidad/procesos/funcionesGeneralesEnvioHL7.php");

/************************************************************************************************
 * Consulto el estado detallado (perteneciente a un estudio) por codigo
 ************************************************************************************************/
function detalleEstado( $conex, $wbasedato, $codigo ){
	
	$val = [];
	
	$sql = "SELECT Eexcod, Eexdes, Eexord, Eexaut, Eexest, Eexmeh, Eexpen, Eexenf, Eexcpe, Eexpnd, Eexrea, Eexcan, Eexgen, Eexapa, Eexepe, Eexrpe, Eexere, Eexeau, Eexrno, Eexhor, Eexpin
			  FROM ".$wbasedato."_000045
		     WHERE Eexcod = '".$codigo."'";
	
	$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	if( $row = mysql_fetch_array($res) ){
	
		$val = [
				'codigo' 					=> $row['Eexcod'],
				'descripcion' 				=> $row['Eexdes'],
				'estado' 					=> $row['Eexest'] == 'on',
				'esPendiente' 				=> $row['Eexpnd'] == 'on',
				'esRealizado' 				=> $row['Eexrea'] == 'on',
				'esCancelado' 				=> $row['Eexcan'] == 'on',
				'esEstadoPendiente' 		=> $row['Eexepe'] == 'on',
				'esEstadoResultadoPendiente'=> $row['Eexrpe'] == 'on',
				'esEstadoRealizado' 		=> $row['Eexere'] == 'on',
				'esEstadoAutorizado' 		=> $row['Eexeau'] == 'on',
				'esEstadoReazliadoNocturno' => $row['Eexrno'] == 'on',
				'permiteInteroperabilidad' 	=> $row['Eexpin'] == 'on',
			];
	}
	
	return $val;
}

function pacienteAEspecialidadUrgencias( $conex, $wemp_pmla, $historia, $ingreso )
{
	$val = false;
	
	$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
	
	$sql = "UPDATE ".$whce."_000022
			   SET mtrcua = '',
				   mtrsal = '',
				   mtrccu = '',
				   mtrmed = '',
				   mtrcon = ''
			 WHERE mtrhis='".$historia."'
			   AND mtring='".$ingreso."';
			";

	$res = mysql_query( $sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	
	// if( mysql_affected_rows() > 0)
	// {
		$val = true;
	// }
	
	return $val;
}

function consultarComentarioPorInteroperabilidadInc( $conex, $wbasedato, $tipoOrden, $nroOrden, $item ){
	
	$val = [];
	
	$sql = "SELECT Fecha_data, Hora_data, Logtxt
			  FROM ".$wbasedato."_000273
			 WHERE Logtor = '".$tipoOrden."'
			   AND Lognro = '".$nroOrden."'
			   AND Logite = '".$item."'
			   AND Logest = 'on'
			   AND Logcla = 'Comentario Asignado'
		  ORDER BY Fecha_data DESC, hora_data DESC
			  ";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	while( $rows = mysql_fetch_array($res) ){
		$val[] = [
				'fecha' 		=> $rows['Fecha_data'],
				'hora' 			=> $rows['Hora_data'],
				'comentario' 	=> $rows['Logtxt'],
			];
	}
	
	return $val;
	
}

function tipoDeOrdenConTomaMuestra( $conex, $whce, $tor ){
	
	$val = false;
	
	//Busco si se permite cancelar el examen
	$sql = "SELECT Tiputm
			  FROM ".$whce."_000015 a
			 WHERE codigo = '".$tor."'
			   AND tiputm = 'on'
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

function estadoPermiteTomaMuestra( $conex, $wmovhos, $westado_externo ){
	
	$val = false;
	
	if( !empty($westado_externo) ){
	
		//Busco si se permite cancelar el examen
		$sql = "SELECT Estptm
				  FROM ".$wmovhos."_000257 a
				 WHERE Esthl7 = '".$westado_externo."'
				   AND Estest = 'on'
				   AND Estptm = 'on'
				";
				
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
				
		if( $num > 0 ){
			$val = true;
		}
	}
	
	return $val;
}

/************************************************************************
 * Indica si el cco puede realizar procedimientos o no
 ************************************************************************/
function ccoRealizaEstudios( $conex, $wbasedato, $cco ){
	
	$val = false;
	
	//Tiene interoperabilidad si tipo de orden se encuentra en la tabla de sedes (movhos_000264 para Sistema HIRUKO de IMEXH)
	//o en la tabla de tipos ofertados (movhos_000267 para laboratorio)
	$sql = "SELECT Ccoerl
			  FROM ".$wbasedato."_000011
			 WHERE ccocod = '".$cco."'
			   AND ccoest = 'on'
			   AND Ccoerl = 'on'
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}

function consultarImpresoraGA_inc( $conex, $wbasedato, $cco ){
	
	$val = '';
	
	//Consulto encabezado de kardex del dia
	$sql = "SELECT Ccoism
			  FROM ".$wbasedato."_000011 b
			 WHERE ccocod = '".$cco."'";

	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Ccoism' ];
	}
	
	return $val;
}

function estudioRealizaUnidad( $conex, $wbasedato, $tor, $cups ){
	
	$val = false;
	
	//Consulto si existe cups ofertados por tipo de orden
	$sql = "SELECT Valtoc, Valcoc, Valeoc, Valerl
			  FROM ".$wbasedato."_000267
			 WHERE valtor = '".$tor."'
			   AND valest = 'on'
		  GROUP BY 1,2,3";
	
	$resToOfertado 	= mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$numToOfertado	= mysql_num_rows($resToOfertado);
	
	if( $numToOfertado > 0 ){
		
		if( $rowsToOfertado = mysql_fetch_array( $resToOfertado ) )
		{
			$tablaOfertas 		= $rowsToOfertado['Valtoc'];
			$campoOferta		= $rowsToOfertado['Valcoc'];
			$campoEstado		= $rowsToOfertado['Valeoc'];
			$campoRealizaUnidad	= trim( $rowsToOfertado['Valerl'] );
			
			$sql = "SELECT *
					  FROM ".$tablaOfertas."
					 WHERE ".$campoOferta." = '".$cups."'
					   AND ".$campoEstado." = 'on'";
			
			$resConOferta = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$numConOferta = mysql_num_rows($resConOferta);
			
			if( $numConOferta > 0 ){
				
				$rowCupOfertado = mysql_fetch_array( $resConOferta );
				
				if( $campoRealizaUnidad != '' && isset( $rowCupOfertado[ $campoRealizaUnidad ] ) )
					if( strtolower( $rowCupOfertado[ $campoRealizaUnidad ] ) == 'on' )
						$val = true;
			}
		}
	}
	
	return $val;
}

function consultarUsuarioTomaMuestra( $conex, $whce, $tor, $nro, $item ){
	
	$val = [
			'usuario'	=> '',
			'fecha' 	=> '',
			'hora'		=> '',
		];
	
	//Consulto encabezado de kardex del dia
	$sql = "SELECT Detutm, Detftm, Dethtm
			  FROM ".$whce."_000028 a
			 WHERE a.dettor = '".$tor."' 
			   AND a.detnro = '".$nro."'
			   AND a.detite = '".$item."'";

	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rowsTM = mysql_fetch_array( $res );
		
		//Por defecto es el campo, ya que si no es un usario matrix es el nombre del personal de laboratorio
		$usuarioTomaMuestras = $rowsTM['Detutm'];
		
		//Se busca el nombre del usuario en matrix
		$q_user = "SELECT Descripcion
					 FROM usuarios
					WHERE Codigo = '".$rowsTM['Detutm']."'";
					
		$res_user = mysql_query($q_user, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_user . " - " . mysql_error());
		
		if( $rows = mysql_fetch_array( $res_user ) ){
			$usuarioTomaMuestras = $rows['Descripcion'];
		}
		
		$val = [
				'usuario'	=> $usuarioTomaMuestras,
				'fecha' 	=> ( $rowsTM['Detftm'] == '0000-00-00' ) ? '' : $rowsTM['Detftm'],
				'hora'		=> ( $rowsTM['Detftm'] == '0000-00-00' ) ? '' : $rowsTM['Dethtm'],
			];
	}
	
	return $val;
}

// function ccoConInteroperabilidadPorEstudio( $conex, $wbasedato, $his, $ing, $tor, $cup ){
	
	// $val = [];
	
	// //Consulto encabezado de kardex del dia
	// $sql = "SELECT Ccocod, Ccoerl
			  // FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
			 // WHERE ubihis = '".$his."' 
			   // AND ubiing = '".$ing."'
			   // AND ubisac = ccocod";

	// $res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	// $num = mysql_num_rows( $res );
	
	// if( $num > 0 ){
		
		// $rows = mysql_fetch_array( $res );
		
		// $interoperabilidadPorEstudio = $rows[ 'Ccoerl' ] == 'on';
		
		// if( $interoperabilidadPorEstudio ){
		
			// $tablaOfertas 	= "";
			// $campoOferta	= "";
			// $campoEstado	= "";
			
			// //Consulto si existe cups ofertados por tipo de orden
			// $sql = "SELECT Valtoc, Valcoc, Valeoc, Valerl
					  // FROM ".$wbasedato."_000267
					 // WHERE valtor = '".$tor."'
					   // AND valest = 'on'
				  // GROUP BY 1,2,3";
			
			// $resToOfertado 	= mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			// $numToOfertado	= mysql_num_rows($resToOfertado);
			
			// if( $numToOfertado > 0 ){
				
				// if( $rowsToOfertado = mysql_fetch_array( $resToOfertado ) ){
					
					// $tablaOfertas 		= $rowsToOfertado['Valtoc'];
					// $campoOferta		= $rowsToOfertado['Valcoc'];
					// $campoEstado		= $rowsToOfertado['Valeoc'];
					// $campoRealizaUnidad	= $rowsToOfertado['Valerl'];
					
					// //Consulto encabezado de kardex del dia
					// $sql = "SELECT *
							  // FROM ".$tablaOfertas."
							 // WHERE ".$campoOferta." = '".$cup."' 
							   // AND ".$campoEstado." = 'on'";

					// $res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
					// $num = mysql_num_rows( $res );
					
					// if( $rows = mysql_fetch_array( $res ) ){
						// if( isset( $rows[ $campoRealizaUnidad ] ) ){
							// $val = $rows['Proerl'] == 'on' ? [ $tor ] : [];
						// }
					// }
				// }
			// }
		// }
	// }
	
	// return $val;
// }

// function ccoConInteroperabilidadLaboratorio( $conex, $wbasedato, $his, $ing ){
	
	// $val = [];
	
	// //Consulto encabezado de kardex del dia
	// $sql = "SELECT Ccotio
			  // FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
			 // WHERE ubihis = '".$his."' 
			   // AND ubiing = '".$ing."'
			   // AND ubisac = ccocod";

	// $res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: $sql - " . mysql_error());
	// $num = mysql_num_rows( $res );
	
	// if( $num > 0 ){
		// $rows = mysql_fetch_array( $res );
		
		// $val = explode( "-", $rows[ 'Ccotio' ] );
	// }
	
	// return $val;
// }
 

function articulosKardexPorPaciente( $conex, $wbasedato, $wcenmez, $whis, $wing, $wfecha, $cco = '%' )
{

		  //Traigo los Kardex GENERADOS con articulos de DISPENSACION que sean del CCo="*" y que sean de la RONDA especificada
	 $q = " SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2) as kadhin, perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Karale, Kadper, Kadess, Kaddma, Kaddia, Kaddan, Kadfum, Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf, Karhco,Kadfcn,Kadhcn,Kaducn,Karcon,Kadcon,Kadori "
	      ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND D.fecha_data = kadfec "
	      ."    AND karcco  LIKE '$cco' "
		  ." UNION "
	      //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS y que sean de la RONDA especificada
		  ." SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2) as kadhin, perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Karale, Kadper, Kadess, Kaddma, Kaddia, Kaddan, Kadfum, Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf, Karhco,Kadfcn,Kadhcn,Kaducn,Karcon,Kadcon,Kadori  "
	      ."   FROM ".$wbasedato."_000054 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'CM' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND D.fecha_data = kadfec "
		  ."    AND karcco  LIKE '$cco' "
		  ." UNION "
		  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que SEAN del CCO="*" y que sean de la RONDA especificada
	      ." SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2) as kadhin, perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Karale, Kadper, Kadess, Kaddma, Kaddia, Kaddan, Kadfum, Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf, Karhco,Kadfcn,Kadhcn,Kaducn,Karcon,Kadcon,Kadori  "
	      ."   FROM ".$wbasedato."_000060 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND D.fecha_data = kadfec "
	      ."    AND karcco  LIKE '$cco' "
		  ." UNION "
		  //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS y que sean de la RONDA especificada
	      ." SELECT A.fecha_data, kadart, artcom, kaduma, kadfin, substr(kadhin,1,2) as kadhin, perequ, Kadcfr, Kadufr, Kadsus, Kadcnd, Kadvia, Kadcma, Kadobs, Karale, Kadper, Kadess, Kaddma, Kaddia, Kaddan, Kadfum, Kadhum, Kadido, Kadfra, Kadfcf, Kadhcf, Karhco,Kadfcn,Kadhcn,Kaducn,Karcon,Kadcon,Kadori  "
	      ."   FROM ".$wbasedato."_000060 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'CM' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      // ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND D.fecha_data = kadfec "
		  ."    AND karcco  LIKE '$cco' "

		  ."  ORDER BY 6 ";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return $res;
}

//La variable parametros se usa para las pantallas de los pisos.
if(isset($parametros) and $parametros != ''){

	$parametros = explode("_",$parametros);

	$wemp_pmla = $parametros[0];
	$wsp = $parametros[1];
	$mostrar = $parametros[2];
	$slCcoDestino = $parametros[3];

}


//************INICIO LOG::: Log de todas las admisiones
$debug = true;
if($debug){
	$fechaLog = date("Y-m-d");
	$horaLog = date("H:i:s");

	//Creacion de un archivo plano para tomar una imagen de la informacion de las camas en ese momento
	$nombreArchivo = "admisionPreentrega.txt";

	//Apuntador en modo de adicion si no existe el archivo se intenta crear...
	$archivo = fopen($nombreArchivo, "a");
	if(!$archivo){
		$archivo = fopen($nombreArchivo, "w");
	}

	@$contenidoLog = "****Admision PREENTREGA..$fechaLog - $horaLog. Para historia: $whistoria. Usuario:$usuario->codigo \r\n";
}
//************FIN LOG::: de admisiones

if(isset($operacion) && $operacion == 'grabar_leido_ordenes'){

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'rol_diferente'=>'off' );
	$fecha = date('Y-m-d');
	$hora = date('H-i-s');
	$usuario = consultar_rol($wuser);

	if( permiteLecturaOrdenesPendientes( $conex, "01", $usuario->codigoRolHCE ) ){

		//Marco los campos como leídos de examenes
		$sql = "UPDATE
					".$whce."_000027, ".$whce."_000028
				SET
					Detpen = 'off',
					Detule = '$wuser',
					Detfle = '$fecha',
					Dethle = '$hora'
				WHERE
					Ordhis = '$whis'
					AND Ording = '$wing'
					AND Ordtor = Dettor
					AND Ordnro = Detnro
					AND Ordest = 'on'
					AND Detpen = 'on'
					AND Detest = 'on'";
		$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_errno() );

	}else{

		$datamensaje['rol_diferente'] = 'on';
	}

	echo json_encode($datamensaje);

	return;

}



if(isset($operacion) && $operacion == 'validar_clave'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 );

	$q = " SELECT *
		     FROM usuarios
		    WHERE Codigo = '".$usuario."'
		      AND Password = '".$contrasena."'
		      AND Activo = 'A'  ";
	$res =  mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	$num =  mysql_num_rows($res);

	if($num == 0){

		$datamensaje['error'] = 1;
		$datamensaje['mensaje'] = "La contrase&ntilde;a no corresponde con el usuario activo en el sistema.";
	}

	echo json_encode($datamensaje);

	return;

}
if(isset($operacion) && $operacion == 'registro_log_contingencia'){

	$wfecha = date("Y-m-d");
	$whora  = date("H:i:s");

	$valor_rango = control_rango_contingencia($wemp_pmla);

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'nombreccoayuda'=>'');

	 $q = " INSERT INTO ".$wbasedato."_000233 (   Medico     ,   Fecha_data,   Hora_data,    Concco ,     Conusu     , Conran,  Conest,  Seguridad        ) "
				 ."                  VALUES ('".$wbasedato."',  '".$wfecha."','".$whora."', '".$cco."', '".$wuser."', '".$valor_rango."', 'on', 'C-".$wuser."')";
	 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());

	echo json_encode($datamensaje);
	return;

}


if(isset($operacion) && $operacion == 'trasladarayudapac'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'nombreccoayuda'=>'');

	$datos_cco_ayuda = explode("-",$cco_ayuda);
	$cod_cco_ayuda = trim($datos_cco_ayuda[0]);
	$nom_cco_ayuda = $datos_cco_ayuda[1];

	$q = " UPDATE ".$wbasedato."_000018 "
			 ."    SET Ubiste  = '".$cod_cco_ayuda."' "
			 ."  WHERE Ubihis  = '".$whis."'"
			 ."    AND Ubiing  = '".$wing."'";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$update = mysql_affected_rows();

	//Si se actualizo la muerte para el paciente se muestra el mensaje.
	if( $update > 0){
		$datamensaje['error'] =  0;
		$datamensaje['mensaje'] =  "El paciente ".$nombre." ha sido trasladado al servicio $nom_cco_ayuda.";
		$datamensaje['nombreccoayuda'] =  $nom_cco_ayuda;
	}else{
		$datamensaje['error'] =  1;
		$datamensaje['mensaje'] =  "No se traslado el paciente al servicio, favor comunicarse con soporte de enfermeria.";
	}

	echo json_encode($datamensaje);
	return;

}


if(isset($operacion) && $operacion == 'liberarpacienteservicioayuda'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

	$q = " UPDATE ".$wbasedatos."_000018 "
			 ."    SET Ubiste  = '' "
			 ."  WHERE Ubihis  = '".$whis."'"
			 ."    AND Ubiing  = '".$wing."'";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$update = mysql_affected_rows();

	//Si se actualizo la muerte para el paciente se muestra el mensaje.
	if( $update > 0){
		$datamensaje['error'] =  0;
		$datamensaje['mensaje'] =  "El paciente ".$nombre." ha sido liberado de este servicio.";
	}else{
		$datamensaje['error'] =  1;
		$datamensaje['mensaje'] =  "No se liberó el paciente de este servicio, favor comunicarse con soporte de enfermeria.";
	}

	echo json_encode($datamensaje);
	return;
}


if(isset($operacion) && $operacion == 'marcarmuerte_hospitalizacion'){

	 $datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

	 $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

	 $wfecha = date("Y-m-d");
	 $whora  = date("H:i:s");
	 /************************************************************************************************************************
	  * Octubre 08 de 2012
	  *
	  * Si se da muerte procedo a cancelar todas las aplicaciones posteriores a la ronda en que se da la muerte
	  ************************************************************************************************************************/
	 //Consulto la hora de corte de dispensacion
	 $horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );

	 //consulta la ronda actual
	 $rondaSiguiente = gmdate( "H:i:s", ( floor( date( "H" )/2 )*2 )*3600 );
	 $fechorRondaSiguiente = strtotime( date( "Y-m-d" )." ".$rondaSiguiente ) + 2*3600;	//Suma dos horas a la ronda actual

	 //Consulto la ronda de corte de dispensacion
	 $fechorCorte = strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" ) + 24*3600;

	 //Cancelo las aplicaciones realizadas entre la siguiente ronda en que se le da muerte al paciente hasta la hora de corte de dispensacion
	 cancelarAplicacionesPorRangoInc( $conex, $wbasedato, $whis, $wing, $fechorRondaSiguiente, $fechorCorte );
	 /************************************************************************************************************************/

	 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2 "
		."   FROM root_000036, root_000037, ".$wbasedato."_000020 "
		."  WHERE Habhis = '".$whis."'"
		."    AND Habing = '".$wing."' "
		."    AND Habhis = Orihis "
		."    AND Habing = Oriing "
		."    AND Oriori = '".$wemp_pmla."'"
		."    AND Oriced = Pacced "
		."    AND Oritid = Pactid ";
	$reshab = mysql_query($q,$conex);
	$rowhab = mysql_fetch_array($reshab);

	$numhab = mysql_num_rows($reshab);

	if ($numhab > 0)
	$whabpac="<b>".$whabpac."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3];

	 //=======================================================================================================================================================
	 //Aca libero la habitación, porque el cadaver lo trasladan a transición y puedeque se halla o pueda facturar, pero hay que liberar la habitación
	$q = " UPDATE ".$wbasedato."_000020 "
		 ."    SET Habali = 'on', "
		 ."        Habdis = 'off', "
		 ."        Habhis = '', "
		 ."        Habing = '', "
		 ."        Habfal = '".$wfecha."', "
		 ."        Habhal = '".$whora."'"
		 ."  WHERE Habhis = '".$whis."'"
		 ."    AND Habing = '".$wing."'"
		 ."    AND Habali = 'off' ";
	$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	 //=======================================================================================================================================================
	 //Aca saco el paciente de la habitación pero lo dejo en el servicio para puedan hacer la factura si es que no la han hecho y ponerlo en proceso
	 //de alta si es que no la han colocado.

	 $ccoCirugia = esCirugia($conex, $wcco);

	 //Si el centro de costos que marca la muerte no es de cirugia tiene en cuenta si esta ocupando habitacion, en caso contrario no se tiene en cuenta ese filtro.
	 if (!$ccoCirugia){

		 $q = " UPDATE ".$wbasedato."_000018 "
			 ."    SET Ubihan  = ubihac, "
			 ."        Ubihac  = '', "
			 ."        Ubimue  = 'on',"
			 ."		   Ubialp  = 'on', " //(Jonatan 2013-08-16)
			 ."        Ubifap  = '".$wfecha."'," //(Jonatan 2013-08-16)
			 ."        Ubihap  = '".$whora."'" //(Jonatan 2013-08-16)
			 ."  WHERE Ubihis  = '".$whis."'"
			 ."    AND Ubiing  = '".$wing."'"
			 ."    AND Ubihac != '' "
			 ."    AND Ubiald != 'on'";

	 }else{

		  $q = " UPDATE ".$wbasedato."_000018 "
		 ."    SET Ubihan  = ubihac, "
		 ."        Ubihac  = '', "
		 ."        Ubimue  = 'on',"
		 ."		   Ubialp  = 'on', " //(Jonatan 2013-08-16)
		 ."        Ubifap  = '".$wfecha."'," //(Jonatan 2013-08-16)
		 ."        Ubihap  = '".$whora."'" //(Jonatan 2013-08-16)
		 ."  WHERE Ubihis  = '".$whis."'"
		 ."    AND Ubiing  = '".$wing."'"
		 ."    AND Ubiald != 'on'";

	 }


	 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $update = mysql_affected_rows();

	 //=======================================================================================================================================================
	 //Pido el servicio de Camillero con Camilla
	 //Traigo el nombre del Origen de la tabla 000004 de la base de datos de camilleros con el centro de costos actual
	 $q = " SELECT Nombre "
		 ."   FROM ".$wcencam."_000004 "
		 ."  WHERE mid(Cco,1,instr(Cco,'-')-1) = '".$wcco."'"
		 ."  GROUP BY 1 ";
	 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	 $rowori = mysql_fetch_array($err);
	 $worigen=$rowori[0];
	 //$wcentral="CAMILLEROS";
	 //=======================================================================================================================================================

	 //Traigo el Tipo de Central
	 $q = " SELECT Tip_central "
		 ."   FROM ".$wcencam."_000001 "
		 ."  WHERE Descripcion = 'PACIENTE DE ALTA'"
		 ."    AND Estado = 'on' ";
	 $restce = mysql_query($q,$conex);
	 $rowcen = mysql_fetch_array($restce);
	 $wtipcen = $rowcen[0];

	 //Traigo la Central asignada para el Centro de Costos según el Tipo de Central //13 marzo
	$q = " SELECT Rcccen "
		."   FROM ".$wcencam."_000009 "
		."  WHERE Rcccco = '".$wcco."'"
		."    AND Rcctic = '".$wtipcen."'";
	$rescen = mysql_query($q,$conex);
	$rowcen = mysql_fetch_array($rescen);
	$wcentral=$rowcen[0];

	if ($wcentral == FALSE)
		{
		$q = " SELECT Rcccen "
			."   FROM ".$wcencam."_000009 "
			."  WHERE Rcccco = '*'"
			."    AND Rcctic = '".$wtipcen."'";
		$rescen = mysql_query($q,$conex);
		$rowcen = mysql_fetch_array($rescen);
		$wcentral=$rowcen[0];
		}
	else
		{
		$wcentral=$wcentral;
	}



	 //=======================================================================================================================================================
	 //Grabo el registro solicitud del camillero
	 $q = " INSERT INTO ".$wcencam."_000003 (   Medico     ,   Fecha_data,   Hora_data,   Origen     ,             Motivo             ,               Habitacion                 ,                      Observacion                                                                 , Destino             ,    Solicito    ,    Ccosto  , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,    Historia ,    Central    , Seguridad        ) "
				 ."                  VALUES ('".$wcencam."','".$wfecha."','".$whora."','".$worigen."','TRASLADO SALA TRANSICION (680)','<b>".$whab_actual."</b><br>".$whabpac."' , 'Se registro muerte en el sistema de altas a la Historia: ".$whis."-".$wing." de las ".$whora."' , 'SALA DE TRANSICION', '".$wusuario."', '".$wcco."', ''       , '00:00:00'    , '00:00:00'  , '00:00:00'       , 'No'   , ''            , '".$whis."' ,'".$wcentral."', 'C-".$wusuario."')";
	 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	 //=======================================================================================================================================================



	 //=======================================================================================================================================================
	 //Cancelo el servicio de Alimentacion
	 //=======================================================================================================================================================
	 cancelar_pedido_alimentacion($whis, $wing, $wcco,"Muerte", $wusuario);


	 //Grabo el egreso en el servicio
	 //=======================================================================================================================================================
	 //Calculo los días de estancia en el servicio actual
	 $q=" SELECT ROUND(TIMESTAMPDIFF(MINUTE,CONCAT( Fecha_ing,' ', Hora_ing ),now())/(24*60),2), Num_ing_Serv "
	   ."   FROM ".$wbasedato."_000032 "
	   ."  WHERE Historia_clinica = '".$whis."'"
	   ."    AND Num_ingreso      = '".$wing."'"
	   ."    AND Servicio         = '".$wcco."'"
	   ."  GROUP BY 2 ";
	 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	 $rowdia = mysql_fetch_array($err);
	 $wdiastan=$rowdia[0];
	 $wnuming=$rowdia[1];

	 if ($wdiastan=="" or $wdiastan==0)
		$wdiastan=0;

	 if ($wnuming=="" or $wnuming==0)
		$wnuming=1;

	//CALCULAR SI LA MUERTE ES MAYOR O MENOR DE 48 DESDE LA FECHA DE INGRESO A LA CLINICA    2013-01-23
	$query = "	SELECT 	ROUND(TIMESTAMPDIFF(MINUTE,(CONCAT ( Fecha_data, ' ', Hora_data )), now() )/(24*60),2) AS diferencia  "
			 ."	  FROM 	".$wbasedato."_000016  "
			 ."  WHERE	Inghis = '".$whis."'"
			 ."    AND	Inging = '".$wing."'";
	$reso = mysql_query($query, $conex);
	$numo = mysql_num_rows($reso);
	$diferencia = 0;
	if($numo > 0){
		$rowo = mysql_fetch_assoc($reso);
		$diferencia = $rowo['diferencia'];
	}
	 $wmotivo='';
	 if ($diferencia>=2)
		$wmotivo="MUERTE MAYOR A 48 HORAS";
	 else
		$wmotivo="MUERTE MENOR A 48 HORAS";

	BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, 'Nueva muerte');

	 //Grabo el registro de egreso del paciente por Muerte
	 $q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
				 ."                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'      ,'".$wing."' ,'".$wcco."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";
	 $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	 //echo $q;
	 //=======================================================================================================================================================

	//Si se actualizo la muerte para el paciente se muestra el mensaje.
	if( $update > 0){
		$datamensaje['error'] =  0;
		$datamensaje['mensaje'] =  "Se ha marcado al paciente ".$nombre." con muerte.";
	}else{
		$datamensaje['error'] =  1;
		$datamensaje['mensaje'] =  "No se marco la muerte para el paciente, favor comunicarse con soporte de enfermeria.";
	}

	echo json_encode($datamensaje);
	return;
}


if(isset($operacion) && $operacion == 'marcaraltadef_hospitalizacion'){

	 $datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'justificacion'=>'', 'htmljusti'=>'');

	 $wfecha = date("Y-m-d");
	 $whora  = date("H:i:s");

	 $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
	 $continuar = false; //esta variable me va a controlar el proceso de cancelado de almimentación, limpieza de habitación etc...

	 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2 "
			."   FROM root_000036, root_000037, ".$wbasedato."_000020 "
			."  WHERE Habhis = '".$whis."'"
			."    AND Habing = '".$wing."' "
			."    AND Habhis = Orihis "
			."    AND Habing = Oriing "
			."    AND Oriori = '".$wemp_pmla."'"
			."    AND Oriced = Pacced "
			."    AND Oritid = Pactid ";
	$reshab = mysql_query($q,$conex);
	$rowhab = mysql_fetch_array($reshab);

	$numhab = mysql_num_rows($reshab);

	if ($numhab > 0)
	$whabpac="<b>".$whabpac."</b><br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3];


	 $wreqjust = false;
	 $wreqjust = requiere_justificacion($wid18);

	 //Si no tiene justificacion valida que la necesite.
	 if($wjust == ''){

		//Si la requiere lleva el arreglo de htmljustificion para pintarlo con el listado de justificaciones.
		if($wreqjust){

			$datamensaje['error'] =  1;

			$query = "SELECT Juscod, Jusdes
						FROM `".$wbasedato."_000023`
					   WHERE Justip = 'R'"
					   ."AND Jusest = 'on'";
			$rs = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());

			$datamensaje['htmljusti'] .= "<center><table border=0>";
			$datamensaje['htmljusti'] .= "<tr><td class='fila2' align=center colspan=4><b>CAUSA DEMORA EN EL ALTA</b></td></tr>";
			$datamensaje['htmljusti'] .= "<tr>";
			$datamensaje['htmljusti'] .= "<td class='fila2' align=center>";
			$datamensaje['htmljusti'] .= "<select id='mjust'>";
			$datamensaje['htmljusti'] .="<option value = '--' selected>--</option>";
			$num = mysql_num_rows($rs);
			 for($i = 0; $i <$num; $i++)
				{
				$row = mysql_fetch_array($rs);
				$datamensaje['htmljusti'] .= "<option value ='".$row['Juscod']."'>".$row['Juscod']." - ".utf8_encode($row['Jusdes'])."</option>";
				}
			$datamensaje['htmljusti'] .= "</select></td></tr>";
			$datamensaje['htmljusti'] .= "</table>";

			$datamensaje['htmljusti'] .= "<br><center><input type=button value='Justificar/Marcar alta definitiva' onclick='marcaraltadef_hospitalizacion(\"$whis\",\"$wing\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$nombre\", \"$wbasedato\", \"$whce\", \"$wusuario\", \"$wid18\", \"$whab_actual\", \"on\", \"$wcco\");'><br><br></center>";

			$datamensaje['justificacion'] = $wreqjust;
			echo json_encode($datamensaje);
			return;

		}

	 }else{
		 $wreqjust = true;
	 }


	if($wreqjust == false)//sino requiere justificación ejecuta el query sin la justificación
		{
			$q = " UPDATE ".$wbasedato."_000018 "
				."    SET Ubiald  = 'on', "
				."        Ubifad  = '".$wfecha."',"
				."        Ubihad  = '".$whora."', "
				."        Ubiuad  = '".$wusuario."' "
				."  WHERE Ubihis  = '".$whis."'"
				."    AND Ubiing  = '".$wing."'"
				."    AND Ubialp  = 'on' "
				."    AND Ubiald != 'on' "
				."    AND Ubiptr != 'on' ";
			$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$update = mysql_affected_rows();
			$continuar = true;

		}else{
				if(isset($wjust))
				{
					$q = " UPDATE ".$wbasedato."_000018 "
						."    SET Ubiald  = 'on', "
						."        Ubifad  = '".$wfecha."',"
						."        Ubihad  = '".$whora."', "
						."        Ubiuad  = '".$wusuario."', "
						."		  Ubijus  = '".$wjust."'"
						."  WHERE Ubihis  = '".$whis."'"
						."    AND Ubiing  = '".$wing."'"
						."    AND Ubialp  = 'on' "
						."    AND Ubiald != 'on' "
						."    AND Ubiptr != 'on' ";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$update = mysql_affected_rows();
					$continuar = true;
				}

			 }


	//Si se genero una actualizacion realiza estas acciones
	if($update > 0)
	{
		cancelar_pedido_alimentacion($whis, $wing, $wcco, "Cancelar", $wusuario);          //Febrero 10 2010

		cancelarPedidoInsumos($conex, $wbasedato, $whis, $wing); //Noviembre 1 de 2017 Jonatan

		$wfecha = date("Y-m-d");
		$whora = (string)date("H:i:s");


		//=======================================================================================================================================================
		//Actualizo o pongo en modo de limpieza la habitación en la que estaba el paciente
		$q = " UPDATE ".$wbasedato."_000020 "
			."    SET Habali = 'on', "
			."        Habdis = 'off', "
			."        Habhis = '', "
			."        Habing = '', "
			."        Habfal = '".$wfecha."', "
			."        Habhal = '".$whora."', "
			."        Habprg = ''"    //Aca va la misma habitacion, si fue programada
			."  WHERE Habcod = '".$whab_actual."'"
			."    AND Habhis = '".$whis."'"
			."    AND Habing = '".$wing."'";
		$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		//=======================================================================================================================================================


		//=======================================================================================================================================================
		//Calculo los días de estancia en el servicio actual
		$q=  " SELECT ROUND(TIMESTAMPDIFF(MINUTE,CONCAT( Fecha_ing,' ', Hora_ing ),now())/(24*60),2), Num_ing_Serv "
			."   FROM ".$wbasedato."_000032 "
			."  WHERE Historia_clinica = '".$whis."'"
			."    AND Num_ingreso      = '".$wing."'"
			."    AND Servicio         = '".$wcco."'"
			."  GROUP BY 2 ";
		$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		$rowdia = mysql_fetch_array($err);
		$wdiastan=$rowdia[0];
		$wnuming=$rowdia[1];

		if ($wdiastan=="" or $wdiastan==0)
			$wdiastan=0;

		if ($wnuming=="" or $wnuming==0)
			$wnuming=1;


		//BUSCO SI EL ALTA ES POR MUERTE O NO
		$q = " SELECT Ubimue "
			."   FROM ".$wbasedato."_000018 "
			."  WHERE Ubihis = '".$whis."'"
			."    AND Ubiing = '".$wing."'";
		$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		$rowmue = mysql_fetch_array($err);

		if ($rowmue[0]!="on"){

			BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, 'Nueva alta');

			$wmotivo="ALTA";
			//Grabo el registro de egreso del paciente del servicio
			$q = " INSERT INTO ".$wbasedato."_000033 (   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,   Servicio ,  Num_ing_Serv,   Fecha_Egre_Serv ,   Hora_egr_Serv ,    Tipo_Egre_Serv,  Dias_estan_Serv, Seguridad        ) "
						."                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$whis."'      ,'".$wing."' ,'".$wcco."' ,".$wnuming."  ,'".$wfecha."'      ,'".$whora."'     , '".$wmotivo."'   ,".$wdiastan."    , 'C-".$wusuario."')";
			$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());


		}
		//=======================================================================================================================================================


		//=======================================================================================================================================================
		//Pido el servicio de Camillero
		//Traigo el nombre del Origen de la tabla 000004 de la base de datos de camilleros con el centro de costos actual
		$q = " SELECT Nombre "
			."   FROM ".$wcencam."_000004 "
			."  WHERE mid(Cco,1,instr(Cco,'-')-1) = '".$wcco."'"
			."  GROUP BY 1 ";
		$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
		$rowori = mysql_fetch_array($err);
		$worigen=$rowori[0];

		//=======================================
		//Traigo el Tipo de Central
		$q = " SELECT Tip_central "
			."   FROM ".$wcencam."_000001 "
			."  WHERE Descripcion = 'PACIENTE DE ALTA'"
			."    AND Estado = 'on' ";
		$restce = mysql_query($q,$conex);
		$rowcen = mysql_fetch_array($restce);
		$wtipcen = $rowcen[0];

		//=======================================

		//=============================================================================
		//Traigo la Central asignada para el Centro de Costos según el Tipo de Central
		$q = " SELECT Rcccen "
			."   FROM ".$wcencam."_000009 "
			."  WHERE Rcccco = '".$wcco."'"
			."    AND Rcctic = '".$wtipcen."'";
		$rescen = mysql_query($q,$conex);
		$rowcen = mysql_fetch_array($rescen);
		$wcentral=$rowcen[0];

		if ($wcentral == FALSE)
			{

			$q = " SELECT Rcccen "
				."   FROM ".$wcencam."_000009 "
				."  WHERE Rcccco = '*'"
				."    AND Rcctic = '".$wtipcen."'";
			$rescen = mysql_query($q,$conex);
			$rowcen = mysql_fetch_array($rescen);
			$wcentral=$rowcen[0];
			}
		else
			{
			$wcentral=$wcentral;
			}

	//$wcentral="CAMILLEROS";
		//=======================================================================================================================================================

		if ($rowmue[0]!="on")  //No pide el camillero si el paciente Murio, porque se pidio cuando marco la muerte
			{
			//=======================================================================================================================================================
			//Grabo el registro solicitud del camillero
			$q = " INSERT INTO ".$wcencam."_000003 (     Medico     ,   Fecha_data,   Hora_data,   Origen     , Motivo           ,        Habitacion                        ,                       Observacion                                                                        , Destino ,    Solicito    ,    Ccosto  , Camillero, Hora_respuesta, Hora_llegada, Hora_Cumplimiento, Anulada, Observ_central,   Historia  ,    Central     , Seguridad        ) "
						."                  VALUES ('".$wcencam."','".$wfecha."'  ,'".$whora."','".$worigen."','PACIENTE DE ALTA','<b>".$whab_actual."</b><br>".$whabpac."' , 'Se dio alta definitiva desde gestion de enfermeria a la Historia: ".$whis."-".$wing." a las ".$whora."' , 'ALTA'  , '".$wusuario."', '".$wcco."', ''       , '00:00:00'    , '00:00:00'  , '00:00:00'       , 'No'   , ''            , '".$whis."' ,'".$wcentral."', 'C-".$wusuario."')";
			$err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
			//=======================================================================================================================================================
			}
	}

	//Si se actualizo el alta definitva para el paciente se muestra el mensaje.
	if( $update > 0){
		$datamensaje['error'] =  0;
		$datamensaje['mensaje'] =  "Se ha marcado al paciente ".$nombre." con alta definitiva.";
	}else{
		$datamensaje['error'] =  1;
		$datamensaje['mensaje'] =  "No se realizo el alta definitiva para el paciente, favor comunicarse con soporte de enfermeria.";
	}

	echo json_encode($datamensaje);
	return;
}


//Marca proceso de traslado para el paciente.
if(isset($operacion) && $operacion == 'marcaraltaenproc_hospitalizacion'){


		$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'');

		$activarAplicacionesCanceladas = false;
		$wfecha = date("Y-m-d");
		$whora  = date("H:i:s");

		if($alta_proceso == 'on'){

		 $q = " UPDATE ".$wbasedato."_000018 "
			 ."    SET Ubialp  = 'on', "
			 ."        Ubifap  = '".$wfecha."',"
			 ."        Ubihap  = '".$whora."'"
			 ."  WHERE Ubihis  = '".$whis."'"
			 ."    AND Ubiing  = '".$wing."'"
			 ."    AND Ubiald != 'on' "
			 ."    AND Ubiptr != 'on' "
			 ."    AND Ubialp  = 'off' ";
		 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $update = mysql_affected_rows();

		if( $update > 0){
			$datamensaje['error'] =  0;
			$datamensaje['mensaje'] =  "Se ha marcado al paciente ".$nombre." con alta en proceso.";
		}else{
			$datamensaje['error'] =  1;
			$datamensaje['mensaje'] =  "No se realizo el alta en proceso para el paciente, favor comunicarse con soporte de enfermeria.";
		}
		 /************************************************************************************************************************
		  * Octubre 08 de 2012
		  *
		  * Si se da muerte procedo a cancelar todas las aplicaciones posteriores a la ronda en que se da la muerte
		  ************************************************************************************************************************/
		 //Consulto la hora de corte de dispensacion
		 $horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );

		 //consulta la ronda actual
		 $rondaSiguiente = gmdate( "H:i:s", ( floor( date( "H" )/2 )*2 )*3600 );
		 $fechorRondaSiguiente = strtotime( date( "Y-m-d" )." ".$rondaSiguiente ) + 2*3600;	//Suma dos horas a la ronda actual

		 //Consulto la ronda de corte de dispensacion
		 $fechorCorte = strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" ) + 24*3600;

		 //Cancelo las aplicaciones realizadas entre la siguiente ronda en que se le da muerte al paciente hasta la hora de corte de dispensacion
		 cancelarAplicacionesPorRangoInc( $conex, $wbasedato, $whis, $wing, $fechorRondaSiguiente, $fechorCorte );

		}else{

			/********************************************************************************
			  * Octubre 08 de 2012
			  *
			  * Consulto la hora de alta en proceso
			  ********************************************************************************/
			  $q = "SELECT * "
				 ."   FROM {$wbasedato}_000018 "
				 ."  WHERE Ubihis  = '".$whis."'"
				 ."    AND Ubiing  = '".$wing."'"
				 ."    AND Ubiptr != 'on' "
				 ."    AND Ubiald = 'off' ";
			 $resFecHorAltProc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 if( $rowsFecHorAltProc = mysql_fetch_array( $resFecHorAltProc ) ){
				$fechorAltaEnProceso = strtotime( $rowsFecHorAltProc['Ubifap']." ".$rowsFecHorAltProc['Ubihap'] );	//fecha y hora de alta en proceso
			 }
			 /********************************************************************************/

			 /********************************************************************************
			  * Octubre 08 de 2012
			  *
			  * Consulto la hora en que dieron muerte al paciente
			  ********************************************************************************/
			 $sql = "SELECT *
					   FROM {$wbasedato}_000033
					  WHERE Historia_clinica  = '".$whis."'
						AND Num_ingreso		  = '".$wing."'
						AND Servicio         = '$wcco'
					 ORDER BY fecha_data desc, hora_data desc";
			 $resUltMuerte = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

			 if( $rowUltMuerte = mysql_fetch_array( $resUltMuerte ) ){
				$fechorMuerte = strtotime( $rowUltMuerte[ 'Fecha_data' ]." ".$rowUltMuerte[ 'Hora_data' ] );
				$fechaMuerte = $rowUltMuerte['Fecha_data']; // 2013-02-12
			 }
			 /********************************************************************************/

			 $q = " UPDATE ".$wbasedato."_000018 "
				 ."    SET Ubialp  = 'off', "
				 ."        Ubifap  = '0000-00-00',"
				 ."        Ubihap  = '00:00:00', "
				 ."        Ubifho  = '0000-00-00',"
				 ."        Ubihho  = '00:00:00', "
				 ."        Ubihot  = '', "
				 ."        Ubiuad  = '' "
				 ."  WHERE Ubihis  = '".$whis."'"
				 ."    AND Ubiing  = '".$wing."'"
				 ."    AND Ubialp = 'on' "
				 ."    AND Ubiptr != 'on' "
				 ."    AND Ubiald = 'off' ";
			 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $update = mysql_affected_rows();

			if( $update > 0){
				$datamensaje['error'] =  0;
				$datamensaje['mensaje'] =  "Se cancel&oacute el alta en proceso para el paciente ".$nombre.".";
			}else{
				$datamensaje['error'] =  1;
				$datamensaje['mensaje'] =  "No se realizo el alta en proceso para el paciente, favor comunicarse con soporte de enfermeria.";
			}

			 //Borro el registro de facturacion si ya habia chequeado que no tuviera devolucion pendiente
			 $q = " DELETE FROM ".$wbasedato."_000022 "
				 ."  WHERE Cuehis  = '".$whis."'"
				 ."    AND Cueing  = '".$wing."'";
			 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $activarAplicacionesCanceladas = true;

		}

		if( $activarAplicacionesCanceladas )
		{

			if( !($fechorAltaEnProceso > 0 && $fechorAltaEnProceso > 0 ) )
			{
			 /*********************************************************************************************
			  * Octubre 08 de 2012
			  *
			  * Procedo a activar las aplicaciones que hallan sido canceladas al momento de dar la muerte
			  *********************************************************************************************/
			 //Consulto la hora de corte de dispensacion
			 $horaCorteDispensacion = consultarAliasPorAplicacion( $conex, $wemp_pmla, "horaCorteDispensacion" );

			 //consulta la ronda en que se realizó el alta en proceso o la muerte
			 //Puede que se de que den alta en proceso y muerte, por tanto hay que verificar cual de los dos se dió primero
			 $rondaSiguiente = gmdate( "H:i:s", ( floor( date( "H", max( $fechorAltaEnProceso, $fechorMuerte ) )/2 )*2 )*3600 );

			 $fechorRondaSiguiente = strtotime( date( "Y-m-d" )." ".$rondaSiguiente ) + 2*3600;	//Suma dos horas a la ronda actual

			 //Consulto la ronda de corte de dispensacion
			 $fechorCorte = strtotime( date( "Y-m-d" )." $horaCorteDispensacion:00:00" ) + 24*3600;

			 activarAplicacionesPorRangoInc( $conex, $wbasedato, $whis, $wing, $fechorRondaSiguiente, $fechorCorte );
			 /*********************************************************************************************/
			}
		}

		 echo json_encode($datamensaje);
		 return;

}


if(isset($operacion) && $operacion == 'cancelarEntregaACirugia'){

	$datamensaje = array('mensaje'=>'', 'notificacion'=>0 , 'formulario'=>'');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$fechaLog = date("Y-m-d");
	$horaLog = date("H:i:s");

	$usuario = $_SESSION['user'];

	@$contenidoLog = "****Sala de Espera..$fechaLog - $horaLog. Para historia: $whis. Usuario:$usuario \r\n";

	$contenidoLog = $contenidoLog."---->Accion: Cancelando admision... \r\n";
	$contenidoLog = $contenidoLog."PARAMETROS:::whistoria: '$whis'  \r\n";

	/* La cancelación consiste en llevar a cabo lo siguiente:
	 * 1. Cancela la solicitud de cama.
	 * 2. Modifica el registro del movimiento hospitalario tabla 17.
	 * 3. Modifica el ingreso en la tabla 18.
	 */

	//Funcion que cancela la solicitud de la cama.
	//Paso 1.
	cancelar_solicitud_cama($wemp_pmla, $whis);
	$paciente = new pacienteDTO();
	$paciente = consultarInfoPacientePorHistoria($conex,$whis);
	$datospacienteMatrix = consultarPacienteMatrix($paciente);

	//Datos del paciente
	$ubicacionPaciente = new ingresoPacientesDTO();
	$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);

	$contenidoLog = $contenidoLog."Paciente: $ubicacionPaciente->historiaClinica-$ubicacionPaciente->ingresoHistoriaClinica:: Ubisan: $ubicacionPaciente->servicioAnterior Ubisac: $ubicacionPaciente->servicioActual Ubihan: $ubicacionPaciente->habitacionAnterior Ubihac: $ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado  \r\n";

	//Paso 2.
	//Modifica el registro del movimiento hospitalario tabla 17.
	deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica);
	$contenidoLog = $contenidoLog."Inactivando movimiento hospitalario \r\n";

	//Paso 3.
	//Modifica el ingreso en la tabla 18.
	modificarIngresoPaciente($conex, $wbasedato, $paciente->historiaClinica, $paciente->ingresoHistoriaClinica, $ubicacionPaciente->servicioActual);
	$contenidoLog = $contenidoLog."Modificando ubicacion del paciente \r\n";
	$contenidoLog = $contenidoLog."La cancelación de la admisión del paciente $paciente->nombre1 $paciente->nombre2 $paciente->apellido1 $paciente->apellido2 con historia clínica $paciente->historiaClinica-$paciente->ingresoHistoriaClinica<br/>se realizó con éxito. \r\n";

	$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $datospacienteMatrix->historiaClinica, $datospacienteMatrix->ingresoHistoriaClinica);

	$contenidoLog = $contenidoLog."Ubicacion final del paciente cancelación: ";
	if(isset($ubicacionPaciente->servicioAnterior)){
		$contenidoLog = $contenidoLog."Ubisan: $ubicacionPaciente->servicioAnterior";
	}

	if(isset($ubicacionPaciente->servicioActual)){
		$contenidoLog = $contenidoLog.". Ubisac: $ubicacionPaciente->servicioActual";
	}

	if(isset($ubicacionPaciente->habitacionAnterior)){
		$contenidoLog = $contenidoLog.". Ubihan:$ubicacionPaciente->habitacionAnterior";
	}

	if(isset($ubicacionPaciente->habitacionActual)){
		$contenidoLog = $contenidoLog.". Ubihac:$ubicacionPaciente->habitacionActual";
	}

	if(isset($ubicacionPaciente->enProcesoTraslado)){
		$contenidoLog = $contenidoLog.". Ubiptr: $ubicacionPaciente->enProcesoTraslado";
	}
	$contenidoLog = $contenidoLog."\r\n";


	if($archivo){
		// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
		if (is_writable($nombreArchivo)) {
			// Escribir $contenido a nuestro arcivo abierto.
			fwrite($archivo, $contenidoLog);
			fclose($archivo);
		}
	}

	$datamensaje['mensaje'] = "Traslado a cirugia cancelado.";
	echo json_encode($datamensaje);
	return;
}

//Graba la solicitud de habitacion.
if(isset($operacion) && $operacion == 'validar_movimiento'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'id'=>'');

	$q_validar = "SELECT id "
				."  FROM ".$wbasedato."_000017 "
				." WHERE Eyrids = '".$wid."' "
				."	 AND Eyrtip = '".$wtipo_mov."'"
				."	 AND Eyrest = 'on'";
	$err_validar = mysql_query($q_validar, $conex) or die (mysql_errno() . $q_validar . " - " . mysql_error());
	$num_validar = mysql_num_rows($err_validar);

	if($num_validar > 0){

		if($wtipo_mov == "Recibo"){
			$text_mov = "recibido";
		}else{
			$text_mov = "entregado";
		}

		$datamensaje['mensaje'] = "El paciente ya fue ".$text_mov.".";
		$datamensaje['error'] = 1;

	}

	echo json_encode($datamensaje);

	return;

}

//Graba la solicitud de habitacion.
if(isset($operacion) && $operacion == 'grabar_solicitarHab'){

	$datamensaje = array('mensaje'=>'', 'error'=>0 , 'formulario'=>'', 'id'=>'');

	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

	$wfecha_actual = date('Y-m-d');
	$fecha_inicial = date("Y-m-d", strtotime("$wfecha_actual -7 day"));

	$q2 = " SELECT Motivo, Observacion, Destino, Solicito, Camillero, A.Hora_data, Hora_respuesta, Habitacion, A.Id, observ_central, A.Fecha_data ,Origen"
		."    FROM ".$wcencam."_000003 A"
		."   WHERE Fecha_llegada     = '0000-00-00' "
		."     AND Fecha_cumplimiento = '0000-00-00' "
		."     AND Hora_llegada      = '00:00:00' "
		."     AND Hora_Cumplimiento = '00:00:00' "
		."     AND Anulada           = 'No' "
		."     AND Historia          != ''"
		."     AND A.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$wfecha_actual."'"
		."     AND Historia          = '".$whistoria."'"
		."     AND Central           = '".$wcentral_camas."'";
	$res2 = mysql_query($q2,$conex);
	$numsolicitudes = mysql_num_rows($res2);
	$row3 = mysql_fetch_array($res2);

	if ($numsolicitudes > 0)
	{

		$datamensaje['mensaje'] = "Hay una solicitud de cama activa para este paciente hecha en ".$row3['Origen'].".";
		$datamensaje['error'] = 1;

	}else{

		$wid = solicitarCamillero($centroCosto, $wemp_pmla, $whistoria, $tipo_habitacion, $obs_soli_cama, $hab_actual);

		if($wid != ''){

			$datamensaje['mensaje'] = "Solicitud de cama realizada.";
			$datamensaje['id'] = $wid;
			$datamensaje['error'] = 0;

		}else{

			$datamensaje['mensaje'] = "No se realizo la solicitud de cama.";
			$datamensaje['error'] = 1;
		}
	}

	echo json_encode($datamensaje);

	return;
}

//Formulario para asociar a un paciente un centro de costos de ayuda diagnostica.
if(isset($operacion) && $operacion == 'trasladoayuda'){


       $datamensaje = array('mensaje'=>'', 'notificacion'=>0 , 'formulario'=>'');


		$q = "  SELECT ccocod, cconom, ccocir, ccoayu, ccourg, ccosst "
			."    FROM ".$wbasedato."_000011 "
			."   WHERE ccoitr = 'on'"
			."   ORDER BY cconom " ;
		$res_cco = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		$select_tipo_cama .= "<SELECT id='cco_ayuda' onchange='consultarSaldosServicios( this, \"$whistoria\",\"$wingreso\", \"$hab_actual\")'>";
		$select_tipo_cama .= "<option></option>";

		while($row_cco = mysql_fetch_array($res_cco))
		   {
			$select_tipo_cama .= "<option data-saldos='".( $row_cco['ccosst'] == 'on' ? 'on' : 'off' )."' value='".$row_cco['ccocod']." - ".$row_cco['cconom']."'>".$row_cco['ccocod']." - ".$row_cco['cconom']."</option>";
		   }
		$select_tipo_cama .= "</SELECT>";


		//Formulario que solicita la historia e ingreso para el paciente.
		$datamensaje['formulario'] .= "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:600px;overflow:auto;'><center><br><br><br>";
		$datamensaje['formulario'] .= "<table style='text-align: center; width: 100%;' border='0'>";
		$datamensaje['formulario'] .= "<tr class = fila1>
										<td><b>Traslado a otros servicios<b></td>
									   </tr>";
	    $datamensaje['formulario'] .= "<tr>
										<td><b>Paciente: $nombre <br>Hab. $hab_actual<b></td>
										</tr>";
		$datamensaje['formulario'] .= "</table>";
		$datamensaje['formulario'] .= "<br>";
		$datamensaje['formulario'] .= "<table style='text-align: center; width: 100px;' border='0'>
										<tr class = fila1>
										<td colspan=2><b>Seleccionar el servicio<b></td>
										</tr>
										<tr class = fila1>
										<td>$select_tipo_cama</td>
										</tr>
									</table>";
		$datamensaje['formulario'] .= "<div id='dvMostrarSaladosServicios'></div>";
		$datamensaje['formulario'] .= "<br><INPUT TYPE='button' value='Grabar' id='insumos' onClick='trasladarayudapac(\"$centro_costo\",\"$wemp_pmla\",\"$whistoria\",\"$wingreso\", \"$hab_actual\")'><br>";
		$datamensaje['formulario'] .= "<br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'><br><br>";
		$datamensaje['formulario'] .= "</center></div>";

       echo json_encode($datamensaje);
	   return;

    }
//--

//Formulario para la solicitud de habitacion.
if(isset($operacion) && $operacion == 'solicitarHab'){


       $datamensaje = array('mensaje'=>'', 'notificacion'=>0 , 'formulario'=>'');

	   $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	   $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

		$q = "  SELECT tipcod, tipdes, 2 AS Tip "
			."    FROM ".$wcencam."_000007 "
			."   WHERE tipcen = '".$wcentral_camas."'"
			."   ORDER BY Tipcod, Tipdes " ;
		$rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$numcam = mysql_num_rows($rescam) or die (mysql_errno()." - ".mysql_error());

		$select_tipo_cama .= "<SELECT id='tipo_hab'>";
		if (trim($row[6]) == "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
		   $select_tipo_cama .= "<option> </option>";
		for($j=0;$j<$numcam;$j++)
		   {
			$rowcam = mysql_fetch_array($rescam);
			$select_tipo_cama .= "<option>".$rowcam[0]." - ".$rowcam[1]."</option>";
		   }
		if (trim($row[6]) != "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
		   $select_tipo_cama .= "<option> </option>";
		$select_tipo_cama .= "</SELECT>";


		//Formulario que solicita la historia e ingreso para el paciente.
		$datamensaje['formulario'] .= "<div align='center' style='cursor:default;background:none repeat scroll 0 0; position:relative;width:100%;height:400px;overflow:auto;'><center><br><br><br>";
		$datamensaje['formulario'] .= "<table style='text-align: center; width: 100%;' border='0'>";
		$datamensaje['formulario'] .= "<tr class = fila1>
										<td><b>Solicitud de habitaci&oacuten<b></td>
									   </tr>";
	    $datamensaje['formulario'] .= "<tr>
										<td><b>Paciente: $nombre<b> <br>Hab. $hab_actual</td>
										</tr>";
		$datamensaje['formulario'] .= "</table>";
		$datamensaje['formulario'] .= "<br>";
		$datamensaje['formulario'] .= "<table style='text-align: center; width: 100px;' border='0'>
										<tr class = fila1>
										<td colspan=2><b>Seleccionar Tipo de Habitaci&oacuten<b></td>
										</tr>
										<tr class = fila1>
										<td>$select_tipo_cama</td>
										</tr>
										<tr>
										<td class='fila1'><b>Observaciones:</b><textarea id='wobs_soli_cama' rows='3' cols='20'></textarea></td>
										</tr>
									</table>";
		$datamensaje['formulario'] .= "<br><INPUT TYPE='button' value='Grabar' id='insumos' onClick='grabar_solicitud(\"$centro_costo\",\"$wemp_pmla\",\"$whistoria\",\"$wingreso\", \"$hab_actual\")'><br>";
		$datamensaje['formulario'] .= "<br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'><br><br>";
		$datamensaje['formulario'] .= "</center></div>";

       echo json_encode($datamensaje);
	   return;

    }
//--


if(isset($operacion) && $operacion == 'registrarDescarte'){

	$datamensaje = array('mensaje'=>'', 'error'=>0);

	$user_session = explode('-',$_SESSION['user']);
	$wuser = $user_session[1];

	$consecutivo = devCons($ccoorigen, $whis, $wing, $wuser, $wbasedato, $id, $cantidad);
	$destino = consultarAliasPorAplicacion($conex, $wemp_pmla, "justificacionDescarteUrgencias");	//Justificacion del descarte

	$q = " INSERT INTO ".$wbasedato."_000031 (    medico,      Fecha_data     ,     Hora_data      ,  Descon   ,     Descco       , Desapv ,  Desapl,    Desart    ,   Descan     ,    Desdes,       Seguridad ) "
	."                        VALUES (  '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$consecutivo."', '".$ccoorigen."', 'off'  ,   'off', '".$codart."', '".$cantidad."', '".$destino."', 'A-".$wuser."')";
	$err = mysql_query($q,$conex);
	echo mysql_error();
	$num=mysql_affected_rows();
	if($num<1)
	{
		$error['color']="#ff0000";
		$error['codInt']="1023";
		$error['codSis']=mysql_errno();
		$error['descSis']=mysql_error();
		$datamensaje['error'] = 1;
		$datamensaje['mensaje'] = "No se pudo descartar el articulo";
	}
	else
	{

		$datamensaje['mensaje'] = "El articulo se descarto con exito.";

	}

	echo json_encode($datamensaje);
	return;
}


//Entregar de urgencias a piso.
if(isset($operacion) && $operacion == 'EntregarUrgAPiso'){

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');
	$wuser = explode('-',$_SESSION['user']);
	$wusuario = $wuser[1];
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$codCcoUrg = consultarCcoUrgencias();

	if($wusuario != ''){

		$contenidoLog = $contenidoLog."--->Accion: Entregando desde urgencias \r\n";
		//Se consulta el registro de la habitación en la cual fue preentregado el paciente
		$infoUbicacion = consultarHabitacionAsignada($whistoria,$wingreso);

		$contenidoLog = $contenidoLog."Ubicacion inicial del paciente: Ubisan: $infoUbicacion->servicioAnterior. Ubisac: $infoUbicacion->servicioActual. Ubihan:$infoUbicacion->habitacionAnterior. Ubihac:$infoUbicacion->habitacionActual Ubiptr: $infoUbicacion->enProcesoTraslado \r\n";
		$habitacion = consultarHabitacion($conex,$infoUbicacion->habitacionDestino);

		if($habitacion->disponible == 'on'){
			//Marcar el servicio y la historia con los servicios seleccionados en la admision
			modificarUbicacionPaciente($whistoria, $wingreso, $codCcoUrg, $infoUbicacion->habitacionOrigen,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino);
			$contenidoLog = $contenidoLog."Ubicacion actual paciente \r\n";

			$ir_a_ordenes = ir_a_ordenes($wemp_pmla, $infoUbicacion->servicioOrigen);

			if($ir_a_ordenes == 'on'){
				entregarArticulos($whistoria, $wingreso, $codCcoUrg, $infoUbicacion->habitacionOrigen,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino,$wnum_art,$warr_art);
				$contenidoLog = $contenidoLog."Articulos entregados \r\n";
			}

			//Marcar usuario que entrega
			registrarEntregaPac($whistoria,$wingreso,$wusuario,$codCcoUrg,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino, $wid);
			$contenidoLog = $contenidoLog."Usuario que entrega: $wusuario \r\n";

			actualizarregistros($conex, $wemp_pmla, $wid);

			$datamensaje['mensaje'] = "El paciente ha sido entregado con exito a la habitacion $infoUbicacion->habitacionDestino";
			$datamensaje['error'] = 0;

			$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $whistoria, $wingreso);
			$contenidoLog = $contenidoLog."Ubicacion final del paciente: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";

			//**************GRABA LOG**************
				if($debug){
					if($archivo){
						// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
						if (is_writable($nombreArchivo)) {
							// Escribir $contenido a nuestro arcivo abierto.
							fwrite($archivo, $contenidoLog);
							fclose($archivo);
						}
					}
				}
				//************************************

		} else {
			$datamensaje['mensaje'] = "El paciente no puede ser entregado ya que la habitaci&oacuten $habitacion->codigo se encuentra ocupada por otro paciente con historia $habitacion->historiaClinica-$habitacion->ingresoHistoriaClinica";
			$datamensaje['error'] = 1;
		}

	} else {
		$datamensaje['mensaje'] = "Su sesion no esta activa.";
		$datamensaje['error'] = 1;
	}


	echo json_encode($datamensaje);
	return;
	}


//Entrega de cirugia piso
if(isset($operacion) && $operacion == 'EntregaDesdeCirugiaAPiso'){

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$codCcoCirugia = consultarCcoCirugia();
	$wuser = explode('-',$_SESSION['user']);
	$wusuario = $wuser[1];

	if($wusuario != ''){

		$contenidoLog = $contenidoLog."--->Accion: Entregando desde cirugia a piso\r\n";

		//Se consulta el registro de la habitación asignada al paciente.
		$infoUbicacion = consultarHabitacionAsignada($whistoria,$wingreso);

		@$contenidoLog = $contenidoLog."Ubicacion inicial del paciente: Ubisan:  Ubisac: $codCcoCirugia. Ubihan: . Ubihac: $infoUbicacion->habitacionDestino. Ubiptr: $infoUbicacion->enProcesoTraslado \r\n";
		$habitacion = consultarHabitacion($conex,$infoUbicacion->habitacionDestino);

		if($habitacion->disponible == 'on'){
			//Marcar el servicio y la historia con los servicios seleccionados en la admision
			modificarUbicacionPaciente($whistoria, $wingreso, $codCcoCirugia, '',$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino);
			$contenidoLog = $contenidoLog."Ubicacion actual paciente \r\n";

			$ir_a_ordenes = ir_a_ordenes($wemp_pmla, $codCcoCirugia);

			if($ir_a_ordenes == 'on'){
			entregarArticulos($whistoria, $wingreso, $codCcoCirugia, $infoUbicacion->habitacionOrigen,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino,$wnum_art,$warr_art);
			$contenidoLog = $contenidoLog."Articulos entregados \r\n";
			}

			//Marcar usuario que entrega
			registrarEntregaPac($whistoria,$wingreso,$wusuario,$codCcoCirugia,$infoUbicacion->servicioDestino, $hab_destino, $wid);
			$contenidoLog = $contenidoLog."Usuario que entrega: $wusuario \r\n";

			actualizarregistros($conex, $wemp_pmla, $wid);

			$datamensaje['mensaje'] = "El paciente ha sido entregado con exito a la habitacion $infoUbicacion->habitacionDestino";
			$datamensaje['error'] = 0;

			$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $whistoria, $wingreso);
			$contenidoLog = $contenidoLog."Ubicacion final del paciente: Ubisan: $codCcoCirugia. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:. Ubihac:. Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";

			//**************GRABA LOG**************
			if($debug){
				if($archivo){
					// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
					if (is_writable($nombreArchivo)) {
						// Escribir $contenido a nuestro arcivo abierto.
						fwrite($archivo, $contenidoLog);
						fclose($archivo);
					}
				}
			}
			//************************************
		} else {
			$datamensaje['mensaje'] = "El paciente no puede ser entregado ya que la habitación $habitacion->codigo se encuentra ocupada por otro paciente con historia $habitacion->historiaClinica-$habitacion->ingresoHistoriaClinica";
			$datamensaje['error'] = 1;
	 }

	} else {
			$datamensaje['mensaje'] = "Su sesion no esta activa.";
			$datamensaje['error'] = 1;
	}


	echo json_encode($datamensaje);
	return;
}

//Funcion que pone a un paciente con alta en proceso, solamente para urgencias.
if(isset($operacion) && $operacion == 'EntPacACir'){

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$wuser = explode('-',$_SESSION['user']);
	$wusuario = $wuser[1];

	if($wusuario != ''){

		$contenidoLog = $contenidoLog."--->Accion: Entregando desde urgencias a cirugia\r\n";
		//Se consulta el registro de la habitación en la cual fue preentregado el paciente
		$infoUbicacion = consultarUltimoMovimientoPaciente($whistoria,$wingreso);

		@$contenidoLog = $contenidoLog."Ubicacion inicial del paciente: Ubisan: $infoUbicacion->servicioOrigen Ubisac: $infoUbicacion->servicioOrigen. Ubihan:$infoUbicacion->habitacionAnterior. Ubihac:$infoUbicacion->habitacionOrigen Ubiptr: on \r\n";

		//Marcar el servicio y la historia con los servicios seleccionados en la admision
		modificarUbicacionActualPaciente($whistoria, $wingreso, '', '',$infoUbicacion->servicioDestino, '');
		$contenidoLog = $contenidoLog."Ubicacion actual paciente \r\n";

		$ir_a_ordenes = ir_a_ordenes($wemp_pmla, $infoUbicacion->servicioOrigen);

		if($ir_a_ordenes == 'on'){
			entregarArticulos($whistoria, $wingreso, $infoUbicacion->servicioOrigen, $infoUbicacion->habitacionOrigen,$infoUbicacion->servicioDestino, $infoUbicacion->habitacionDestino,$wnum_art,$warr_art);
			$contenidoLog = $contenidoLog."Articulos entregados \r\n";
		}

		//Marcar usuario que entrega
		modificarUsuarioMovimientoHospitalario($whistoria,$wingreso,$wusuario,$infoUbicacion->servicioOrigen,$infoUbicacion->servicioDestino);
		$contenidoLog = $contenidoLog."Usuario que entrega: $wusuario \r\n";

		$infoSolCama = consultarHabitacionAsignada($whistoria,$wingreso); //Consulto el identificador de la ultima solicitud de cama para el paciente.
		actualizarregistros($conex, $wemp_pmla, $infoSolCama->id_solicitud); //Aqui se actualiza el registro de la tabla cencam_000003 para la llegada, el cumplimiento y el realizado

		$datamensaje['mensaje'] = "El paciente ha sido entregado con exito a cirugia.";
		$datamensaje['error'] = 0;

		$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $whistoria, $wingreso);
		$contenidoLog = $contenidoLog."Ubicacion final del paciente: Ubisan: $ubicacionPaciente->servicioAnterior. Ubisac: $ubicacionPaciente->servicioActual. Ubihan:$ubicacionPaciente->habitacionAnterior. Ubihac:$ubicacionPaciente->habitacionActual Ubiptr: $ubicacionPaciente->enProcesoTraslado \r\n";

		//**************GRABA LOG**************
			if($debug){
				if($archivo){
					// Asegurarse primero de que el archivo existe y puede escribirse sobre él.
					if (is_writable($nombreArchivo)) {
						// Escribir $contenido a nuestro arcivo abierto.
						fwrite($archivo, $contenidoLog);
						fclose($archivo);
					}
				}
			}
			//************************************

	} else {
		$datamensaje['mensaje'] = "Su sesion no esta activa.";
		$datamensaje['error'] = 1;
	}

	echo json_encode($datamensaje);
	return;


}

//Funcion que pone a un paciente con alta en proceso, solamente para urgencias.
if(isset($operacion) && $operacion == 'marcaraltaenproceso')
{

	if (strpos($user, "-") > 0)
	   $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	else
	   $wuser=$user;

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	$wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

	//Se libera el paciente de cualquier ubicacion en la que se encuentre.
	$q = " UPDATE ".$wbasedato."_000018 "
		."    SET ubialp = 'on', "
		."        ubifap = '".$wfecha."',"
		."        ubihap = '".$whora."'"
		."  WHERE ubihis = '".$whis."'"
		."    AND ubiing = '".$wing."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	 $q1 = " SELECT concod
              FROM ".$whce."_000035
             WHERE conalt = 'on'";
    $res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
    $row1 = mysql_fetch_array($res1);
	$wcond_alta = $row1['concod'];

	$qlog = " SELECT *
				FROM ".$whce."_000022
			   WHERE Mtrhis = '".$whis."'
				 AND Mtring = '".$wing."'; ";
	$registrosFila = obtenerRegistrosFila($qlog);

	//Asigno la conducta de alta al paciente.
	$q = 	 " UPDATE ".$whce."_000022 "
			."    SET mtrcon = '".$wcond_alta."', mtrcur='off', Mtrftc = '".$wfecha."', Mtrhtc = '".$whora."' "
			."  WHERE mtrhis = '".$whis."'"
			."	  AND mtring = '".$wing."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	$num_affect = mysql_affected_rows();
	if($num_affect>0)
	{
		//Guardo LOG de actualización en tabla root_000036
		$q = "	INSERT INTO log_agenda
								  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
						   VALUES
								  ('".$wfecha."', '".$whora."', '".$whis."', '".$wing."', 'Alta gestion enfermeria', '".$wuser."', 'Alta en Proceso Manual', '".$registrosFila."')";
		$resl1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	}


	echo json_encode($datamensaje);
	return;


}


//Funcion que permite reasignarle sala a un paciente en urgencias, al seleccionar una sala diferente a la actual se liberara el cubiculo que este ocupando el paciente
//en caso de tenerlo.
if(isset($operacion) && $operacion == 'AsignarZona')
{

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'', 'sala_actual'=>$SalaActual);

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	//Se libera el paciente de cualquier ubicacion en la que se encuentre.
	$q = " UPDATE ".$wbasedato."_000020 "
		."    SET habhis = '', "
		."        habing = '',"
		."        habdis = 'on'"
		."  WHERE habhis = '".$whis."'"
		."    AND habing = '".$wing."'";
	$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Busco los cubiculos asociados a la sala y luego devuelvo el html con el dropdown para pintarlo.
	$q_cub =   " SELECT Habcod, Habcpa "
			 . "   FROM ".$wbasedato."_000020 "
			 . "  WHERE habcub = 'on'"
			 . "	AND habdis = 'on'"
			 . "	AND habhis = '' "
			 . "	AND habest = 'on' "
			 . "	AND habvir != 'on' "
			 . "	AND habzon = '".$wsala."' "
			." ORDER BY habord, habcpa ";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());
	$num_cub = mysql_num_rows($res_cub);

	if($num_cub == 0){

		//Si ya estan ocupadas todas las fisicas muestra las virtuales.
		$q_cub =   " SELECT Habcod, Habcpa, Habzon, Habhis, Habing  "
				 . "   FROM ".$wbasedato."_000020 "
				 . "  WHERE habcub = 'on'"
				 . "	AND habest = 'on' "
				 . "	AND habdis = 'on' "
				 . "	AND habvir = 'on' "
				 . "	AND habzon = '".$wsala."' "
				." ORDER BY habord, habcpa ";
		$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());
		$num_cub = mysql_num_rows($res_cub);
	}

	if($num_cub > 0){

		//Asigno la sala al paciente para que al recargar deje seleccionada esa opcion en el dropdown y despues podra seleccionar el cubiculo donde desea ubicar al paciente.
		$q = 	 " UPDATE ".$whce."_000022 "
				."    SET mtrsal = '".$wsala."' "
				."  WHERE mtrhis = '".$whis."'"
				."	  AND mtring = '".$wing."'";
		$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		$datamensaje['html'] .= "<select id='cubiculo$posicion' name='wcubiculo$posicion' onchange='reasignarCubiculo($whis, $wing, $posicion, \"\")'>";
		$datamensaje['html'] .= "<option value=''>&nbsp</option>";

		 while($row_cub = mysql_fetch_array($res_cub)){

			$datamensaje['html'] .= "<option value='".$row_cub['Habcod']."'>".$row_cub['Habcpa']."</option>";

			}

		$datamensaje['html'] .= "</select>";
	}
	else{

	$datamensaje['error'] = 1;
	$datamensaje['sala_actual'] = $SalaActual;
	$datamensaje['mensaje'] = "No hay cubiculos disponibles para esta sala.";

	}

	echo json_encode($datamensaje);
	return;


}

//Funcion que permite la reasignacion de ubicacion para un paciente que esta en urgencias.
//2016-marzo-22 Felipe Alvarez  Se le agregra una validacion mas a reasignar cubiculo , para que tenga encuenta si hay movimientos
// en la movhos_000018
if(isset($operacion) && $operacion == 'reasignarCubiculo')
{

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'' , 'sql'=>'');
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	$num_hab_his = 0;

	//Evaluo si el paciente tiene alta definitva y si tiene movimientos en la tabla movhos_000018
	$q_ald = " SELECT ubiald
                 FROM ".$wbasedato."_000018
                WHERE ubihis = '$whis'
                  AND ubiing = '$wing'";
    $res_ald = mysql_query($q_ald, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_ald . " - " . mysql_error());

	$num_ald = mysql_num_rows($res_ald); //  Este num_ald tiene el numero de registros que hay en movhos_000018 , si tiene al menos uno
										 //  Quiere decir que para esta historia e ingreso hay movimientos
	$row_ald = mysql_fetch_array($res_ald);
	if ($num_ald > 0)
	{

		if($row_ald['ubiald'] == 'on'){

			$datamensaje['error'] = 1;
			$datamensaje['mensaje'] = "El paciente tiene alta definitiva, la ubicacion selecionada no fue asignada.";

		}else{

			//Evaluo el estado actual del cubiculo.
			$q_hab = " SELECT habdis
						 FROM ".$wbasedato."_000020
						WHERE habcod = '$wcubiculoNuevo'";
			$res_hab = mysql_query($q_hab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab . " - " . mysql_error());
			$row_hab = mysql_fetch_array($res_hab);

			//Si la historia no tiene cubiculo asignado permite la asignacion.
			if($num_hab_his == 0){

				if($row_hab['habdis'] == "on"){

				//Se libera al paciente de cualquier otro cubiculo que este ocupando.
				$q = " UPDATE ".$wbasedato."_000020 "
					."    SET habhis = '', "
					."        habing = '', "
					."        habdis = 'on' "
					."  WHERE habcod != '".$wcubiculoNuevo."'"
					."    AND habhis = '".$whis."'"
					."    AND habing = '".$wing."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				//Se actualiza el cubiculo para el paciente.
				$q = " UPDATE ".$wbasedato."_000020 "
					."    SET habhis = '".$whis."', "
					."        habing = '".$wing."',"
					."        habdis = 'off' "
					."  WHERE habcod = '".$wcubiculoNuevo."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


				//Terminar la consulta y marcar el paciente con cubiculo asignado.
				$q = " UPDATE ".$whce."_000022 "
					."    SET mtrcua = 'on', mtrcur = 'off',
							  Mtrccu = '".$wcubiculoNuevo."' "
					."  WHERE mtrhis = '".$whis."'"
					."	  AND mtring = '".$wing."'";
				$res1 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					// --> Registrar fecha y hora de asignación, solo si aun no se han registrado.
					// --> Jerson trujillo, 2015-04-21
					$sqlUpdFecHor = "
						UPDATE ".$whce."_000022
						   SET Mtrfac = '".date("Y-m-d")."',
							   Mtrhac = '".date("H:i:s")."'
						 WHERE Mtrhis = '".$whis."'
						   AND Mtring = '".$wing."'
						   AND Mtrfac = '0000-00-00'
						   AND Mtrhac = '00:00:00'
					";
					mysql_query($sqlUpdFecHor, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdFecHor):</b><br>".mysql_error());

				}else{

					$datamensaje['error'] = 1;
					$datamensaje['mensaje'] = "El cubiculo ya se encuentra asignado.";

				}

			}else{

				$datamensaje['error'] = 1;
				$datamensaje['mensaje'] = "El paciente ya tiene ubicacion asignada.";

			}
		}
	}
	else
	{
			$datamensaje['error'] = 1;
			$datamensaje['mensaje'] = "El paciente no esta ingresado con este numero de ingreso. La ubicacion selecionada no fue asignada.";

	}
	echo json_encode($datamensaje);
	return;

}

//Accion que permite mostrar el listado de zonas si el cco las tiene.
if(isset($operacion) && $operacion == 'filtrarzonas')
{

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'html'=>'');

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$array_cco = explode("-", $wcco);

	$q_sala =      "  SELECT Arecod, Aredes  "
				 . "    FROM ".$wbasedato."_000020, ".$wbasedato."_000169 "
				 ."	   WHERE habcco = '".$array_cco[0]."'"
				 ."		 AND habzon = Arecod "
				 ." GROUP BY habzon, habcco ";
	$res_sala = mysql_query($q_sala, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_sala." - ".mysql_error());
	$num_salas = mysql_num_rows($res_sala);

	$datamensaje['nro_zonas'] = $num_salas;

	$array_salas = array();

	while( $row_salas = mysql_fetch_assoc($res_sala)) {

		if(!array_key_exists($row_salas['Arecod'], $array_salas )){

			$array_salas[$row_salas['Arecod']] = $row_salas;

		}

	}

	$datamensaje['html'].= "<select id='sala' name='sala' >";
	$datamensaje['html'].= "<option value='%'>Todas</option>";

	if(is_array($array_salas)){
		foreach($array_salas as $key => $row_sala){

			$datamensaje['html'].= "<option value='".$row_sala['Arecod']."' $sala_seleccionada>".$row_sala['Aredes']."</option>";
		}
	}

	$datamensaje['html'] .= "</select>";

	echo json_encode($datamensaje);
	return;
}



if(isset($operacion) && $operacion == 'grabar_tipo_hab')
{

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$whis = $historia;
	$wing = $ingreso;
	$wcco_destino = $cco;
	$wcco_origen = $cco;
	$whab_origen  =$hab;
	$whab_destino =$hab;

	if (strpos($user, "-") > 0)
	   $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	else
	   $wuser=$user;

    $went_rec = 'Ent';  // Se declara esta variable ya que estamos simulando una entrega.
    $wfecha = date("Y-m-d");
    $whora  = date("H:i:s");

	//--Analisis  de la entrega y recibo
	//
	//  1 - Condicion :
	//  Antes de hacer una entrega y recibo se debe buscar si  tiene una y solo una entrega y recibo anterior
	//  para ese ingreso  en el mismo centro de costos
	//

	//  Si encuentra solo una ,
	//  Se Registra la entrega y recibo normalmente
	//
	//  Si no,
	//  2  - Condicion :
	//  Antes de hacer una entrega y recibo
	//  mirar si tiene una entrega y recibo  el mismo dia
	//	Si tiene  el mismo dia
	//  Se debe guardar en una variable la habitacion de origen de la entrega y recibo anterior luego de esto
	//  anularla  y a la nueva entrega y recibo ponerle esta habitacion y tipo de habitacion que se almaceno en la
	//  variable.
	//
	//  Si no ,
	//	Se Registra la entrega y recibo normalmente
	//---------------------------------------------------------------
	//
	//--Analisis  de la entrega y recibo
	//
	//  1 - Condicion :
	//  Antes de hacer una entrega y recibo se debe buscar si  tiene una y solo una entrega y recibo anterior
	//  para ese ingreso  en el mismo centro de costos

		$q= "  SELECT  Count(Eyrtip) as cuantos "
			."   FROM  ".$wbasedato."_000017 "
			."  WHERE Eyrtip ='Recibo' "
			."    AND Eyrhis ='".$historia."' "
			."    AND Eyring ='".$ingreso."' "
			."	  AND Eyrsde ='".$cco."' "
			."	  AND Eyrhor ='".$hab."' "
			."    AND Eyrhde ='".$hab."' "
			."    AND Eyrest != 'off' ";

		$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

		$row = mysql_fetch_array($res);

		$num_movimientos = $row['cuantos'];


	//  Si encuentra solo una ,
	//  Se Registra la entrega y recibo normalmente

		if ( ( $num_movimientos * 1 )  == 1 )
		{
			// no se hace nada se graba la entrega y recibo normalmente
			$var = 'Solo hay una entrega y recibo';
		}
		else //  Si no,
		{
			//  2  - Condicion :
			//  Antes de hacer una entrega y recibo
			//  mirar si tiene una entrega y recibo  el mismo dia
			//	Si tiene se debe guardar en una variable la habitacion de origen de la entrega y recibo anterior luego de esto
			//  anularla  y a la nueva entrega y recibo ponerle esta habitacion y tipo de habitacion que se almaceno en la
			//  variable.

			$q= "  SELECT  Count(Eyrtip) as cuantos "
				."   FROM  ".$wbasedato."_000017 "
				."  WHERE Eyrtip ='Entrega' "
				."    AND Eyrhis ='".$historia."' "
				."    AND Eyring ='".$ingreso."' "
				."    AND Eyrsde ='".$cco."' "
				."	  AND Eyrhor ='".$hab."' "
				."    AND Eyrhde ='".$hab."' "
				."	  AND Fecha_data='".$wfecha."'"
				."    AND Eyrest !='off' ";

				$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

				$row = mysql_fetch_array($res);

				$num_movimientos_diarios = $row['cuantos'];

				if( ($num_movimientos_diarios * 1) == 1)
				{

					$q= "  SELECT  Eyrhor, Eyrthe "
						."   FROM  ".$wbasedato."_000017 "
						."  WHERE Eyrtip ='Entrega' "
						."    AND Eyrhis ='".$historia."' "
						."    AND Eyring ='".$ingreso."' "
						."    AND Eyrsde ='".$cco."' "
						."	  AND Fecha_data='".$wfecha."'"
						."	  AND Eyrhor ='".$hab."' "
						."    AND Eyrhde ='".$hab."' "
						."    AND Eyrest !='off' ";

						$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

						$row = mysql_fetch_array($res);

					// Guardo la habitacion de origen
					// y el tipo de habitacion
					$aux_hab_origen		=$row['Eyrhor'];
					$aux_habtip_origen	=$row['Eyrthe'];

					// Anulo la entrega
					$q= " UPDATE  ".$wbasedato."_000017 "
						."   SET  Eyrest ='off'"
						."  WHERE Eyrtip ='Entrega' "
						."    AND Eyrhis ='".$historia."' "
						."    AND Eyring ='".$ingreso."' "
						."    AND Eyrsde ='".$cco."' "
						."	  AND Fecha_data='".$wfecha."'"
						."	  AND Eyrhor ='".$hab."' "
						."    AND Eyrhde ='".$hab."' "
						."    AND Eyrest !='off' ";

					$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

					// Anulo el recibo
					$q= " UPDATE  ".$wbasedato."_000017 "
						."   SET  Eyrest ='off'"
						."  WHERE Eyrtip ='Recibo' "
						."    AND Eyrhis ='".$historia."' "
						."    AND Eyring ='".$ingreso."' "
						."    AND Eyrsde ='".$cco."' "
						."	  AND Fecha_data='".$wfecha."'"
						."	  AND Eyrhor ='".$hab."' "
						."    AND Eyrhde ='".$hab."' "
						."    AND Eyrest !='off' ";

					$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());

					// Se asigna el valor de la habitacion de origen = al de la auxiliar
					// Se asigna el valor del tipo de habitacion de origen = al tipo de la auxiliar
					$whab_origen = $aux_hab_origen;
					$thab_ant = $aux_habtip_origen;

					$var ='grabo cambiando valores';
				}
				else
				{

					$var ='grabo normalmente';
				}

		}



	//
	//  Si no ,
	//	Se Registra la entrega y recibo normalmente
	//---------------------------------------------------------------
	//
	//
	//---------------
	//--Entrega del paciente
    $q = "lock table " . $wbasedato . "_000001 LOW_PRIORITY WRITE";
    $err = mysql_query($q, $conex);

    //Generamos el consecutivo
     $q = " UPDATE " . $wbasedato . "_000001 "
        . "    SET connum=connum + 1 "
        . "  WHERE contip='entyrec' ";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    $q = " SELECT connum "
		. "  FROM " . $wbasedato . "_000001 "
		. " WHERE contip='entyrec' ";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    $row = mysql_fetch_array($err);
    $wconsec = $row['connum'];

    $q = " UNLOCK TABLES";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    //Se registra la entrega del paciente.
    $q = " INSERT INTO " . $wbasedato . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,   Eyrhor  ,   Eyrhde         ,   Eyrtip   , Eyrest, Eyrids, Eyrint, Seguridad,Eyrthe , Eyrthr    ) "
        ."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whis . "','" . $wing . "','" .$wcco_destino . "','" .$wcco_origen. "','" .$whab_origen . "','" .$whab_destino . "','Entrega', 'on', '" . $wid . "' , 'on'  , 'C-" . $wuser . "', '".$thab_ant."' , '".$thab_act."')";
    $err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());

	//---------------------------
	//---------------------------

	//--Recibo del paciente------------------
	//---------------------------------------

	$q = "lock table " . $wbasedato . "_000001 LOW_PRIORITY WRITE";
    $err = mysql_query($q, $conex);

    //Generamos el consecutivo
    $q = " UPDATE " . $wbasedato . "_000001 "
       . "    SET connum=connum + 1 "
       . "  WHERE contip='entyrec' ";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    $q = " SELECT connum "
		. "  FROM " . $wbasedato . "_000001 "
		. " WHERE contip='entyrec' ";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    $row = mysql_fetch_array($err);
    $wconsec = $row['connum'];

    $q = " UNLOCK TABLES";
    $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    //Se registra la entrega del paciente.
    $q = " INSERT INTO " . $wbasedato . "_000017 (   Medico       ,   Fecha_data,   Hora_data,   Eyrnum     ,   Eyrhis  ,   Eyring  ,   Eyrsor  ,   Eyrsde         ,   Eyrhor  ,   Eyrhde         ,   Eyrtip   , Eyrest, Eyrids, Eyrint, Seguridad,Eyrthe , Eyrthr    ) "
        ."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whis . "','" . $wing . "','" .$wcco_destino . "','" .$wcco_origen. "','" .$whab_origen . "','" .$whab_destino . "','Recibo', 'on', '' , 'on'  , 'C-" . $wuser . "', '".$thab_act."' , '".$thab_act."')";
    $err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());

	//----------------------------------------------
	//----------------------------------------------

	// ---cambiar el tipo de habitacion en   el maestro de tablas
	//--------------------------------------------------------------
	$q_select = " SELECT Habcod , Habtfa"
				."  FROM ".$wbasedato."_000020"
				." WHERE   Habcod = '".$hab."' ";

	$res_select = mysql_query($q_select,$conex) or die("Error en el query: ".$q_tarifas."<br>Tipo Error:".mysql_error());
	$row_select = mysql_fetch_array($res_select);


	$q  = 		"UPDATE  ".$wbasedato."_000020"
				 ." SET Habtfa = '".$thab_act."' "
			   ." WHERE  Habcod = '".$hab."' ";

	//$res = mysql_query($q,$conex) or die("Error en el query: ".$q."<br>Tipo Error:".mysql_error());


	$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	$q_log = "INSERT INTO ".$wbasedatocliame."_000179 "
			."		   (					Cloant								,			Clomov				,			Cloope				,		Clousu	,	Cloest,		Seguridad		,		Medico		,Fecha_data		,Hora_data)"
			."  VALUES ('".$row_select['Habcod']."-".$row_select['Habtfa']."'	,'".$hab."-".$thab_act."','Modificacion: Cambio de tipo facturacion','".$wuser."',	'on',	'C-".$wuser."'	,'".$wbasedatocliame."'	,'".$wfecha."'	,'".$whora."')";

	$res = mysql_query($q_log,$conex) or die("Error en el query: ".$q_log."<br>Tipo Error:".mysql_error());
	//----------------------------

	echo $var;
	return;
}



if(isset($operacion) && $operacion == 'marcar_prioritario'){

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'medico'=>'');

	$audNuevo = "N:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",,,".$wfechadataexamen.",".$westado_prioritario;
	$audAnterior = "A:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",,,".$wfechadataexamen.",".$anterior;


	  $query1 ="  	UPDATE ".$whce."_000027 A, ".$whce."_000028 B
					   SET Detpri = '".$westado_prioritario."', Detfmo = '".date('Y-m-d')."', Dethmo = '".date( "H:i:s" )."'
					 WHERE Ordtor = Dettor
					   AND Ordnro = Detnro
					   AND A.Ordhis = '".$whis."'
					   AND A.Ording = '".$wing."'
					   AND B.Detnro = '".$wordennro."'
					   AND B.Detite = '".$wordite."'
					   AND B.Detest = 'on'";
	  $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());

	   // echo $mensaje;
		$datamensaje['mensaje'] = $mensaje;

		$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_ACTUALIZADO');

		$wuser_datos = explode("-",$_SESSION['user']);

		//Registro de auditoria
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $whis;
		$auditoria->ingreso = $wing;
		$auditoria->descripcion = "$audAnterior $audNuevo";
		$auditoria->fechaKardex = $wfec;
		$auditoria->mensaje = $mensajeAuditoria;
		$auditoria->seguridad = $wuser_datos[1];

		registrarAuditoriaKardex($conex,$wbasedato,$auditoria);

		echo json_encode($datamensaje);
		return;
}


if(isset($operacion) && $operacion == 'cambiar_estado_examen'){


	$datamensaje = array('mensaje'=>'', 'error'=>0, 'medico'=>'');


	$medAsociado = consultarTablaMedAsociado($wbasedato,$whis,$wing,$wexam,$wordennro,$wordite);

	// var_dump($medAsociado);
	if($medAsociado == "")
	{
		$wenviar_hl7 = "";

		$sql = "SELECT *
				  FROM ".$wbasedato."_000267 a
				 WHERE Valtor = '".$wexam."'
				   AND Valest = 'on'
				";

		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			$sql = "SELECT Eexcan
					  FROM ".$wbasedato."_000045
					 WHERE Eexcod = '".$westado."'
					   AND Eexest = 'on'
					   AND Eexcan = 'on'
					";

			$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );

			if( $num > 0 ){
				$wenviar_hl7 = ",Detenv = 'on'";
			}
		}

		$audNuevo = "N:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",".$westado.",,".$wfechadataexamen;
		$audAnterior = "A:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",P,,".$wfechadataexamen;


		 $query1 ="  	UPDATE ".$whce."_000027 A, ".$whce."_000028 B
						   SET Detesi = '".$westado."', Detfmo = '".date('Y-m-d')."', Dethmo = '".date( "H:i:s" )."'".$wenviar_hl7."
                         WHERE Ordtor = Dettor
                           AND Ordnro = Detnro
                           AND A.Ordhis = '".$whis."'
                           AND A.Ording = '".$wing."'
                           AND B.Detnro = '".$wordennro."'
                           AND B.Detite = '".$wordite."'
                           AND B.Detest = 'on'";
          $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $query1 - ".mysql_error());

		$mensaje = "";
		//Suspender medicamentos asociados al procedimiento
		// if($accionMed == "SUSPENDER")
		if($accionMed == "SUSPENDER" && $tieneMedicamentos=="on")
		{
			// suspenderMedicamentos
			$mensaje = suspenderMedicamentoAsociadoAProcedimiento($whis,$wing,$wordennro,$wordite,$wexam);
		}
		elseif($accionMed != "SUSPENDER" && $tieneMedicamentos=="on")
		{
			//Validar si el procedimiento tiene medicamentos asociados, si los tiene debe mostrar mensaje indicando que los medicamentos continuan suspendidos y debe hablar con el medico

			$qRelMedExiste = " SELECT Relmed,Relido,Kadsus,Artgen
								 FROM ".$wbasedato."_000195,".$wbasedato."_000054,".$wbasedato."_000026
								WHERE Relhis = '".$whis."'
								  AND Reling = '".$wing."'
								  AND Reltor = '".$wexam."'
								  AND Relnro = '".$wordennro."'
								  AND Relite = '".$wordite."'
								  AND Kadhis = Relhis
								  AND Kading = Reling
								  AND Kadfec = '".date("Y-m-d")."'
								  AND Kadart = Relmed
								  AND Kadido = Relido
								  AND Kadsus = 'on'
								  AND Kadest = 'on'
								  AND Kadart = Artcod
								  AND Artest = 'on';";

			$resRelMedExiste = mysql_query($qRelMedExiste, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qRelMedExiste . " - " . mysql_error());
			$numRelMedExiste = mysql_num_rows($resRelMedExiste);
			$rowRelMedExiste = mysql_fetch_array($resRelMedExiste);

			if($numRelMedExiste > 0)
			{
				// mensajeMedicamentosSuspendidos
				$mensaje = "Los medicamentos asociados al procedimiento estan suspendidos, para reactivarlos informe al medico";
			}

			// $mensaje = "Los medicamentos asociados al procedimiento estan suspendidos, para reactivarlos informe al medico";
		}

		// echo $mensaje;
		$datamensaje['mensaje'] = $mensaje;

		$mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_ACTUALIZADO');

		$wuser_datos = explode("-",$_SESSION['user']);

		//Registro de auditoria
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $whis;
		$auditoria->ingreso = $wing;
		$auditoria->descripcion = "$audAnterior $audNuevo";
		$auditoria->fechaKardex = $wfec;
		$auditoria->mensaje = $mensajeAuditoria;
		$auditoria->seguridad = $wuser_datos[1];

		registrarAuditoriaKardex($conex,$wbasedato,$auditoria);

		if( !empty( $wenviar_hl7 ) ){

			//curl para enviar el mensaje a laboratorio
		}

        // return;
	}
	else
	{
		$datamensaje['error']= 1;
		$datamensaje['medico']= $medAsociado;
	}

	echo json_encode($datamensaje);
	return;

		  // $audNuevo = "N:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",".$westado.",,".$wfechadataexamen;
		  // $audAnterior = "A:".$wexam.",".$wordennro.",".$wordite.",".str_replace( "_", " ", trim($wtexto_examen) ).",P,,".$wfechadataexamen;


		 // $query1 ="  	UPDATE ".$whce."_000027 A, ".$whce."_000028 B
						   // SET Detesi = '".$westado."', Detfmo = '".date('Y-m-d')."', Dethmo = '".date( "H:i:s" )."'
                         // WHERE Ordtor = Dettor
                           // AND Ordnro = Detnro
                           // AND A.Ordhis = '".$whis."'
                           // AND A.Ording = '".$wing."'
                           // AND B.Detnro = '".$wordennro."'
                           // AND B.Detite = '".$wordite."'
                           // AND B.Detest = 'on'";
          // $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());

		// $mensaje = "";
		// //Suspender medicamentos asociados al procedimiento
		// // if($accionMed == "SUSPENDER")
		// if($accionMed == "SUSPENDER" && $tieneMedicamentos=="on")
		// {
			// // suspenderMedicamentos
			// $mensaje = suspenderMedicamentoAsociadoAProcedimiento($whis,$wing,$wordennro,$wordite,$wexam);
		// }
		// elseif($accionMed != "SUSPENDER" && $tieneMedicamentos=="on")
		// {
			// //Validar si el procedimiento tiene medicamentos asociados, si los tiene debe mostrar mensaje indicando que los medicamentos continuan suspendidos y debe hablar con el medico

			// $qRelMedExiste = " SELECT Relmed,Relido,Kadsus,Artgen
								 // FROM ".$wbasedato."_000195,".$wbasedato."_000054,".$wbasedato."_000026
								// WHERE Relhis = '".$whis."'
								  // AND Reling = '".$wing."'
								  // AND Reltor = '".$wexam."'
								  // AND Relnro = '".$wordennro."'
								  // AND Relite = '".$wordite."'
								  // AND Kadhis = Relhis
								  // AND Kading = Reling
								  // AND Kadfec = '".date("Y-m-d")."'
								  // AND Kadart = Relmed
								  // AND Kadido = Relido
								  // AND Kadsus = 'on'
								  // AND Kadest = 'on'
								  // AND Kadart = Artcod
								  // AND Artest = 'on';";

			// $resRelMedExiste = mysql_query($qRelMedExiste, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qRelMedExiste . " - " . mysql_error());
			// $numRelMedExiste = mysql_num_rows($resRelMedExiste);
			// $rowRelMedExiste = mysql_fetch_array($resRelMedExiste);

			// if($numRelMedExiste > 0)
			// {
				// // mensajeMedicamentosSuspendidos
				// $mensaje = "Los medicamentos asociados al procedimiento estan suspendidos, para reactivarlos informe al medico";
			// }

			// // $mensaje = "Los medicamentos asociados al procedimiento estan suspendidos, para reactivarlos informe al medico";
		// }

		// echo $mensaje;


		// $mensajeAuditoria = obtenerMensaje('MSJ_EXAMEN_ACTUALIZADO');

		// $wuser_datos = explode("-",$_SESSION['user']);

		// //Registro de auditoria
		// $auditoria = new AuditoriaDTO();

		// $auditoria->historia = $whis;
		// $auditoria->ingreso = $wing;
		// $auditoria->descripcion = "$audAnterior $audNuevo";
		// $auditoria->fechaKardex = $wfec;
		// $auditoria->mensaje = $mensajeAuditoria;
		// $auditoria->seguridad = $wuser_datos[1];

		// registrarAuditoriaKardex($conex,$wbasedato,$auditoria);


        // return;

}


if(isset($operacion) && $operacion == 'aplicarMedicamentosPorProced'){

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$cantidad = $dosis/$cantidadDeManejo;
	$aplicado = aplicarMedicamento($wbasedato,$historia,$ingreso,$codMed,$cantidad,$dosis,$ronda,$descMedCom,$cco,$cco,$usuario,$fechaAplicacion,$noEnviar,$unidad,$ido,$codvia);

	echo $aplicado;
	return $aplicado;

}


if(isset($operacion) && $operacion == 'anularAplicarMedicamentosPorProced'){

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$conex = $conex;
	$wusuario = $usuario;

	// anular($historia, $ingreso, $codMed, $cco, $wwdosis, $wwcanfra, $rondaNum, $fechaAplicacion, $noEnviar, $stock, $ido);
	$anularAplicacion = anularAplicacionMovhos($historia, $ingreso, $codMed, $cco, "", "" , $rondaNum, $fechaAplicacion, $noEnviar, $stock, $ido);

}

if(isset($operacion) && $operacion == 'consultarSaldoAntesDeAplicar'){

	global $conex;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$cantidad = $dosis/$cantidadDeManejo;

	// $datamensaje = array('tieneSaldoSuficiente'=>'', 'error'=>0, 'html'=>'');
	$datamensaje = array('tieneSaldoSuficiente'=>'', 'error'=>0);

	$querySaldMed = " SELECT SUM(Spauen),SUM(Spausa)
						FROM ".$wbasedato."_000004
					   WHERE Spahis='".$historia."'
						 AND Spaing='".$ingreso."'
						 AND Spaart='".$codMed."'
						 ;";

	$resSaldMed = mysql_query($querySaldMed, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $querySaldMed . " - " . mysql_error());
	$numSaldMed = mysql_num_rows($resSaldMed);

	$tieneSaldoSuficiente = "off";
	if($numSaldMed > 0)
	{
		$rowSaldMed = mysql_fetch_array($resSaldMed);
		$saldo = $rowSaldMed[0] - $rowSaldMed[1];

		if( $saldo > 0 && $saldo >= $cantidad )
		{
			$tieneSaldoSuficiente = "on";
		}
	}


	$datamensaje['error'] = 0;
	$datamensaje['tieneSaldoSuficiente'] = $tieneSaldoSuficiente;

	echo json_encode($datamensaje);
	return;

}

if(isset($operacion) && $operacion == 'validarAplicacion'){

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$existe = buscarsiExiste( $conex, $wbasedato, $wfecha, $whora_a_grabar, $whis, $wing, $wart, $wido );

	//Si no existe busco por que no fue aplicado
	if( !$existe ){
		echo "2";
		//validar_aplicacion( $conex, $wbasedato, $whis, $wing, $wart, $wcco, $cantidadCargada, &$wcant_aplicar, $filaArticulos[ 'Kaduma' ], $info[ 'esStock' ], $info[ 'noEnviar' ], &$saldo, &$saldoSinRecibir, &$wmensaje );;
	}
	else{
		echo "1";
	}

	exit;
}

if(isset($operacion) && $operacion == 'consultarUsuarioHabilitado'){
	
	global $conex;
	
	// Funciones definidas en include/movhos/movhos.inc.php
	$usuarioHabilitado = true;
	$ccoConRestriccion = consultarCcoRestriccion($conex, $wbasedato, $cco);
	if($ccoConRestriccion)
	{
		$usuarioHabilitado = consultarUsuarioPermitido($conex, $wbasedato, $cco, $usuario);
	}
	
	echo json_encode($usuarioHabilitado);
	return;
}

if(isset($operacion) && $operacion == 'consultarSaldosServicios'){
	
	$paciente 			= '';
	$habitacion 		= '';
	$ccoOrigen 			= ''; 
	$wccohos 			= ''; 
	$id_solicitud_cama 	= ''; 
	$wccoapl 			= ''; 
	$hab_asignada 		= ''; 
	$whab_actual 		= ''; 
	$wser_destino 		= ''; 
	$tipo_mov 			= ''; 
	$wservicio_anterior = '';
	
	$datos 		= Detalle_ent_rec_hospi('NoApl', $historia, $ingreso, $paciente, $habitacion, $ccoOrigen, $wccohos, $id_solicitud_cama, $wccoapl, $hab_asignada, $whab_actual, $wser_destino, $tipo_mov, $wservicio_anterior);
	$datos_ins 	= traer_insumos_enfermeria( $historia, $ingreso, $paciente, $habitacion );
	
	$datos[ 'permitirTraslado' ] = '1';
	
	if( empty( $datos['wnum_art'] ) )
		$datos['wnum_art'] = 0;
	
	if( $datos['wnum_art'] > 0 ){
		$datos['html'] = "<br><div style='font-weight: bold;'>Saldo de medicamentos<span><img src='/matrix/images/medical/root/pac_con_med.png' heigth='32' width='32' style='vertical-align: middle;'></span></div>".$datos['html'];
	}
	
	if( $datos_ins['saldo_insumos'] > 0 ){
		$datos['html'] 		.= "<br><div style='font-weight: bold;'>Saldo de insumos<span><img src='/matrix/images/medical/root/saldo_insumos.png' heigth='32' width='32' style='vertical-align: middle;'></span></div>".$datos_ins['html'];
		$datos['wnum_art']	+= $datos_ins['saldo_insumos'];
	}
	
	if( $datos['wnum_art'] > 0 ){
		$datos['html'] = "<br><b>Hay saldos pendientes.<br>Para poder trasladar al paciente no debe haber saldos de insumos y medicamentos pendientes</b><br>".$datos['html'];
	}
	
	$pacientesPorRecibir 	= listaPacientesRecibirHospitalizacion( $cco_origen );
	$pacientePorEntregar 	= listaPacientesEntregarHospitalizacion( $cco_origen );
	$pacienteSolicitudCama 	= listaPacientesEntregarCcoAyuda( $cco_origen );
	
	if( array_key_exists( $historia."-".$ingreso, $pacientePorEntregar ) || array_key_exists( $historia."-".$ingreso, $pacientesPorRecibir ) || array_key_exists( $historia."-".$ingreso, $pacienteSolicitudCama ) ){
		$datos['wnum_art'] = 1;
		$datos[ 'permitirTraslado' ] = '0';
		$datos['html'] = "<br><div style='font-weight:bold;'>El paciente no debe tener una solicitud de cama, estar pediente de recibir o estar en proceso de entrega a otra habitación para trasladarlo al servicio ".$cco_nombre." </div>";
	}
	
	$datos['html'] = utf8_encode( $datos['html'] );
	
	echo json_encode( $datos );
	
	exit();
}

if(isset($operacion) && $operacion == 'pacienteAEspecialidadUrgencias'){
	
	$data = [ 'actualizo' => false ];
	
	$data['actualizo'] = pacienteAEspecialidadUrgencias( $conex, $wemp_pmla, $whis, $wing );
	
	echo json_encode( $data );
	
	exit();
}
?>
<html>
<head>

<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css">
 <!-- PNotify -->
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.css" rel="stylesheet">
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.css" rel="stylesheet">
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.css" rel="stylesheet">
<link href="../../../include/gentelella/vendors/pnotify/dist/pnotify.brighttheme.css" rel="stylesheet">

<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<!-- <script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script> -->
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>

<!-- PNotify -->
<script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.js" type="text/rocketscript"></script>
<script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.buttons.js" type="text/rocketscript"></script>
<script type="text/javascript" src="../../../include/gentelella/vendors/pnotify/dist/pnotify.nonblock.js" type="text/rocketscript"></script>



<style>
	*#col1 { border: 3px solid black; }

	.textoNomenclatura
	{
		 font-size: 11px;
		 font-weight: bold;
	}

	.fondoAlertaConfirmar
	{
		 background-color: #8181F7;
		 color: #000000;
		 font-size: 10pt;
	}

	.fondoAlertaEliminar
	{
		 background-color: #F5D0A9;
		 color: #000000;
		 font-size: 10pt;
	}

	#popup_content.confirm {
		background-image: url(../../../include/root/alerta_confirm.gif);
	}

	.textoborde {
			font-size: 15px;
			font-weight: bold;
			border-radius: 5px;
			border: 2px solid #56c8f7;
			background-color: #FFFFCC;
			width: auto;
			height: auto
		}
		
		
	.btnEnvioTomaMuestras_aceptar{
		text-decoration: none;
		padding: 10px;
		font-weight: 600;
		font-size: 15px;
		color: #ffffff;
		background-color: #ffbb33;
		border-radius: 6px;
		border: 1px solid #ffbb33;
	}
   .btnEnvioTomaMuestras_aceptar:hover{
		color: #ffbb33;
		background-color: #ffffff;
	}
	
	.btnEnvioTomaMuestras_cancelar{
		text-decoration: none;
		padding: 10px;
		font-weight: 600;
		font-size: 15px;
		color: #ffffff;
		background-color: #ff4444;
		border-radius: 6px;
		border: 1px solid #ff4444;
	}
   .btnEnvioTomaMuestras_cancelar:hover{
		color: #ff4444;
		background-color: #ffffff;
	}


<?php
//Si tiene estos parametros en la url, la tabla se extandira de forma automatica.
if($slCcoDestino != '' and $wsp == 'on' and $mostrar == 'on') { ?>

	BODY {
    font-family: verdana;
    font-size: 10pt;
	width: auto;
	height:auto;
}

<?php }?>

</style>
<title>Gestión de Enfermeria</title>
<script type="text/javascript">

function realizarEnServicio( cmp, enServcio, externo, tipoOrden, numeroOrden, item, historia, ingreso, estudio ){
	
	// var msg = "El estudio <b>"+estudio+"</b> no se realizará en la ayuda diagnóstica por uno de los siguientes motivos?";
	
	// if( enServcio ){
		// msg += "<br><br>- Por que se realizará en la unidad hospitalaria ";
		
	// }
	
	// if( externo ){
		// msg += "<br><br>- Por que el equipo requerido no se encuentra disponible ";
	// }
	
	var msg = "En donde se realizará el estudio <b>"+estudio+"</b>?<br><br>En el servicio dónde se encuentra el paciente puede realizarse por uno de los siguientes motivos: ";
	
	if( enServcio ){
		msg += "<br><br>- Por que se realizará en la unidad hospitalaria ";
	}
	
	if( externo ){
		msg += "<br><br>- Por que el equipo requerido no se encuentra disponible ";
	}
	
	function enviarRespuestaAOrdenes( resp ){
		
		$.post("../../hce/procesos/ordenes.inc.php",
            {
                consultaAjax		: '',
                consultaAjaxKardex	: 'seRealizaEnUnidadAmbulatoria',
                wemp_pmla			: $("#wemp_pmla").val(),
                whistoria			: historia,
                wingreso			: ingreso,
                tipoOrden			: tipoOrden,
				numeroOrden			: numeroOrden,
				item				: item,
				realizarEnPiso		: resp,
				wusuario		  	: $('#user').val(),
            }
            ,function(data) {
				console.log(data);
				$( cmp ).css({display:"none"});
            },"json" );
	}
	
	$( "<div style='color: black;font-size:12pt;height: 250px;' title='REALIZAR EN SERVICIO?' class='dvRealizarEnServicio'>"+msg+"</div>" ).dialog({
		width		: 700,
		height		: 350,
		modal		: true,
		resizable	: false,
		buttons	: {
			"Relizar en servicio o externo": function() {
					cmp.checked = true;
					cmp.value = 'on';
					enviarRespuestaAOrdenes( 'on' );
					$( this ).dialog( "close" );
				},
			"Realizar en Ayuda diagnóstica": function() {
					cmp.checked = true;
					cmp.value = 'off';
					enviarRespuestaAOrdenes( 'off' );
					$( this ).dialog( "close" );
				},
			"Cancelar": function() {
					cmp.checked = false;
					cmp.value = '';
					$( this ).dialog( "close" );
				},
		},
	});
	
	$( ".dvRealizarEnServicio" ).parent().css({
		left: ( $( window ).width() - 700 )/2,
		top : ( $( window ).height() - 350 )/2,
	});
	
	$( ".ui-dialog-titlebar-close" ).css({
		display : "none",
	});
}

function consultarSaldosServicios( cmp, historia, ingreso, habitacion ){
	
	if( $( "option:selected", cmp ).data("saldos").toLowerCase() == 'on' ){
		
		$( "#insumos" ).attr({disabled:true});
		
		$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{
				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'consultarSaldosServicios',
				wbasedato 		: $("#wbasedato").val(),
				wemp_pmla    	: $( "#wemp_pmla" ).val(),
				historia		: historia,
				ingreso			: ingreso,
				habitacion    	: habitacion,
				cco_servicio	: $.trim( $( cmp ).val().split("-")[0] ),
				cco_nombre		: $.trim( $( cmp ).val().split("-")[1] ),
				cco_origen		: $.trim( $( "[name=slCcoDestino]" ).val().split("-")[0] ),
			},
			dataType: "json",
			async: false,
			success:function(data_json) {
				if( data_json.wnum_art > 0 ){
					$( dvMostrarSaladosServicios ).html( data_json.html );
					$( "#insumos" ).attr({disabled:true});
				}
				else{
					$( "#insumos" ).attr({disabled:false});
				}
			}
		});
	}
	else{
		$( dvMostrarSaladosServicios ).html( '' );
		$( "#insumos" ).attr({disabled:false});
	}
}


function grabar_leido_ordenes(wbasedato,whce,wuser,whis,wing){

	$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{

					wemp_pmla		: $("#wemp_pmla").val(),
					consultaAjax 	: '',
					operacion 		: 'grabar_leido_ordenes',
					wbasedato 		: wbasedato,
					whce			: whce,
					whis			: whis,
					wing			: wing,
					wuser	    	: wuser

				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{

					}
					else{

						if(data_json.rol_diferente == 'off'){

							$(".pen_leido_ord_"+whis+"-"+wing).removeAttr('style');
							$(".tooltip_proc_exa_"+whis+"_"+wing).tooltip('disable');
							$(".tooltip_proc_exa_"+whis+"_"+wing).css("background-color", "");

						}

						$.unblockUI();

					}
				}

			});


}


function mostrar_mensaje_contingencia(wemp_pmla, cco, fecha) {

	var cod_cco = cco.split("-");
	var ruta = "Contingencia_Kardex_de_Enfermeria.php?wemp_pmla="+wemp_pmla+"&wcco="+cod_cco[0]+"&wfec="+fecha;
	var wbasedato = $("#wbasedato").val();
	var usuario = $("#usuario").val();
	var url_raiz = $("#url_raiz").val();
	var fecha_sistema  = $("#fecha_sistema").val();
	var ultima_hora = $("#ultima_hora").val();
	var ultimo_minuto = $("#ultimo_minuto").val();
	var ultimo_segundo = $("#ultimo_segundo").val();

	var myStack = {"dir1":"down", "dir2":"right", "push":"top"};

    new PNotify({
        title: "Contingencia",
        text: 'No ha generado la contingencia ingrese <a href="#" onclick="ejecutar(\''+ruta+'\'); registro_log_contingencia(\''+wemp_pmla+'\', \''+wbasedato+'\', \''+cod_cco[0]+'\', \''+usuario+'\', \''+fecha_sistema+'\', \''+ultima_hora+'\', \''+ultimo_minuto+'\', \''+ultimo_segundo+'\', \''+url_raiz+'\', \'off\')">aqui</a> para generarla.',
        type: "error",
        icon: "fa fa-bars",
        hide: false,
        history: {
            history: false
        },
        addclass: "stack-modal",
        stack: {
            "dir1": "down",
            "dir2": "right",
            "modal": true,
            "overlay_close": true
        }
    });

}

function volver(wemp_pmla, waux, wsp)
{
	var extra = "";

	if( waux != '' ){
		extra += '&waux='+waux;
	}

	if(wsp != '' ){
		extra += '&wsp='+wsp;
	}

	location.href = 'gestionEnfermeria.php?wemp_pmla='+wemp_pmla+extra;
}

//Funcion que llama al programa modificacion de traslados para que cancele la entrega de una paciente.
function cancelarReciboHospitalizacion( wemp_pmla, historia, ingreso, ser_anterior){

	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

	jConfirm('¿Esta seguro de cancelar el traslado del paciente?', 'Cancelar traslado', function(r) {

		if(r){

			$("#td_traslado_"+historia+"-"+ingreso).html("<img src='../../images/medical/sgc/Refresh-128.png' width='30px' height='30px' style='cursor:pointer;' onclick='location.reload();'><br>Recargar");
			ejecutar("Modificacion_traslados.php?wproceso=D&wccoIngreso=UN."+ser_anterior+"&whistoria="+historia);

		}else{
			$.unblockUI();
		}

	});



}

function marcarmuerte_hospitalizacion( whis,wing,wproctraslado,waltaEnProceso,nombre, basedatos, basedatoshce, usuario, id_tabla18, whab_actual, control_just, cco_actual ){


	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

	jConfirm('¿Marcará el paciente '+nombre+' con muerte, ¿Esta seguro?', 'Muerte', function(r) {

		if(r){

		$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{

					wemp_pmla		: $("#wemp_pmla").val(),
					consultaAjax 	: '',
					operacion 		: 'marcarmuerte_hospitalizacion',
					wbasedato 		: basedatos,
					whce			: basedatoshce,
					whis			: whis,
					wing			: wing,
					wusuario	    : usuario,
					wid18			: id_tabla18,
					whab_actual		: whab_actual,
					westado_altaEnProceso	: waltaEnProceso,
					nombre			: nombre,
					wid18			: id_tabla18,
					wcco     		: cco_actual


				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{
						jAlert(data_json.mensaje);
						$('#muerte_'+whis+"-"+wing).prop('checked', false);
						$.unblockUI();
						return;
					}
					else{

						jAlert(data_json.mensaje);
						$('#'+whis+"-"+wing).remove();
						$.unblockUI();
						return;
					}
				}

			});

		}else{
				$('#muerte_'+whis+"-"+wing).prop('checked', false);
				$.unblockUI();
				return;
			}
	});
}



//Funcion que traslada-entrega el paciente de un piso a otro.
function marcaraltadef_hospitalizacion( whis,wing,wproctraslado,waltaEnProceso,nombre, basedatos, basedatoshce, usuario, id_tabla18, whab_actual, control_just, cco_actual){

	if(control_just != 'on'){

		$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	}


	var justifi = "";

	if(control_just == 'on'){

		justifi = $("#mjust").val();

		if(justifi == "--"){
			jAlert("Debe seleccionar una justificacion");
			return;
		}

	}

	jConfirm('¿Pondrá el paciente '+nombre+' en alta definitiva, ¿Esta seguro?', 'Alta definitiva', function(r) {

		if(r){

		$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{

					wemp_pmla		: $("#wemp_pmla").val(),
					consultaAjax 	: '',
					operacion 		: 'marcaraltadef_hospitalizacion',
					wbasedato 		: basedatos,
					whce			: basedatoshce,
					whis			: whis,
					wing			: wing,
					wusuario	    : usuario,
					wid18			: id_tabla18,
					whab_actual		: whab_actual,
					westado_altaEnProceso	: waltaEnProceso,
					nombre			: nombre,
					wid18			: id_tabla18,
					wjust			: justifi,
					wcco     		: cco_actual

				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{

						if(data_json.justificacion == 1){

							$.blockUI({
									message: data_json.htmljusti,
									css: { 		left: 	'10%',
												top: 	'10%',
												width: 	'80%',
												height: 'auto',
												cursor: ''
										 }

							  });
							  return;
						}

						jAlert( data_json.mensaje, 'ALERTA' );
						$('#alta_definitiva_'+whis+"-"+wing).prop('checked', false);
						$.unblockUI();
						return;
					}
					else{

						jAlert( data_json.mensaje, 'ALERTA' );
						$('#'+whis+"-"+wing).remove();
						$.unblockUI();
						return;
					}
				}

			});

		}else{
				$('#alta_definitiva_'+whis+"-"+wing).prop('checked', false);
				$.unblockUI();
				return;
				}
	});

}

function marcaraltaenproc_hospitalizacion(whis,wing,wproctraslado,waltaEnProceso,nombre, basedatos, basedatoshce, usuario, id_tabla18){

	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

	//Verifica en que estado se encuentra el checkbox de alta el proceso.
	if($("#alta_proceso_"+whis+"-"+wing).is(':checked')) {

		//Esta accion pondra el paciente en proceso de traslado
		jConfirm('Pondrá en proceso de alta al paciente '+nombre+', ¿Esta seguro?', 'Alta en proceso', function(r) {

			if(r){

				var alta_proceso = 'on';
				var enviar_datos = true;

				$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'marcaraltaenproc_hospitalizacion',
						wbasedato 		: basedatos,
						basedatoshce	: basedatoshce,
						whis			: whis,
						wing			: wing,
						seguridad		: usuario,
						alta_proceso	: alta_proceso,
						id_tabla18		: id_tabla18,
						nombre			: nombre

					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							$( "#"+whis+"-"+wing ) .addClass( "fondoAmarillo" );
							$.unblockUI();
							return;
						}
						else{
							jAlert( data_json.mensaje , 'ALERTA' );
							if(alta_proceso == 'on'){
								$( "#"+whis+"-"+wing ).addClass( "fondoAmarillo" ); //Se agrega el fondo amarillo de proceso de alta
							}else{
								$( "#"+whis+"-"+wing ).removeClass( "fondoAmarillo" ); //Se quita el fondo amarillo de proceso de alta
							}

							$( "#pedir_cama_"+whis+"_"+wing ).remove(); //Se quita el icono de solicitud de cama
							$( "#cajon_pedir_cama_"+whis+"_"+wing ).removeAttr('onclick'); //Se quita el onclick de pedir cama
							$( "#cajon_pedir_cama_"+whis+"_"+wing ).attr('title', ''); //Se inicia en vacio el title
							$( "#cajon_pedir_cama_"+whis+"_"+wing).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
							$.unblockUI();

						}
					}

				});


			}else{

				$('#alta_proceso_'+whis+"-"+wing).prop('checked', false);
				$.unblockUI();
				return;
				}
			});
	}else{

		//Esta acccion cancelara el proceso de traslado para el paciente.
		jConfirm('Cancelará el proceso de alta para el paciente '+nombre+' ¿Esta seguro?', 'Cancelar alta en proceso', function(r) {
			if(r){

				var alta_proceso = 'off';
				var enviar_datos = true;

				$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'marcaraltaenproc_hospitalizacion',
						wbasedato 		: basedatos,
						basedatoshce	: basedatoshce,
						whis			: whis,
						wing			: wing,
						seguridad		: usuario,
						alta_proceso	: alta_proceso,
						id_tabla18		: id_tabla18,
						nombre			: nombre

					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							$( "#"+whis+"-"+wing ) .addClass( "fondoAmarillo" );
							$.unblockUI();
							return;
						}
						else{
							jAlert( data_json.mensaje , 'ALERTA' );

							$( "#"+whis+"-"+wing ).removeClass( "fondoAmarillo" ); //Se quita el fondo amarillo de proceso de alta
							$( "#"+whis+"-"+wing ).addClass( "fila1" ); //Se agrega estilo.

							$.unblockUI();

						}
					}

				});


			}else{

				$('#alta_proceso_'+whis+"-"+wing).prop('checked', true);
				$.unblockUI();
				return;
				}


			});
		}

	}

//Funcion que traslada-entrega el paciente de un piso a otro.
function moverPacHosp( wemp_pmla, historia, ingreso, nombre, wcco, wccohos, wid, wccoapl, whab_destino, whab_actual, wser_destino, tipo_mov, wnum_art,  warr_art ){


	 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

	if(tipo_mov == 'Ent'){
		var tipo_mov_aux = "Entrega";
		var texto_tipo = "Entregará";
	}else{
		var tipo_mov_aux = "Recibo";
		var texto_tipo = "Recibirá";
	}

	$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{

					consultaAjax 	: '',
					operacion 		: 'validar_movimiento',
					wemp_pmla		: wemp_pmla,
					wbasedato		: $("#wbasedato").val(),
					wid				: wid,
					wtipo_mov		: tipo_mov_aux

				},
				dataType: "json",
				async: false,
				success:function(data_json) {


					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						location.reload();
						return;
					}
					else{

						jConfirm('¿'+texto_tipo+' el paciente '+nombre+', ¿Esta seguro?', 'Mover paciente', function(r) {

						if(r){

								$.ajax({
										url: "Ent_y_Rec_Pac_gestion.php",
										type: "POST",
										data:{
											consultaAjax 	: 	'',
											operacion 		: 	'moverPacHosp',
											wemp_pmla		: 	wemp_pmla,
											wbasedato		: 	$("#wbasedato").val(),
											whis			: 	historia,
											wing			: 	ingreso,
											wcco			: 	wcco,
											went_rec		:	tipo_mov,
											wccohos			:	wccohos,
											wid				:	wid,
											wccoapl			:	wccoapl,
											wgrabar			:	'ok',
											whab			:	whab_actual,
											wccodes1		:	wser_destino,
											whabdes1		:	whab_destino,
											wnum_art		:	wnum_art,
											warr_art		:	warr_art


										},
										dataType: "json",
										async: false,
										success:function(data_json) {

											if (data_json.error == 1)
											{
												jAlert(data_json.mensaje);
												$.unblockUI();
												return;
											}
											else{

												if(tipo_mov == 'Ent'){

													$('#'+historia+"-"+ingreso).remove();

												}else{

													if(tipo_mov == 'Rec'){

														$("#"+historia+"-"+ingreso).removeClass("colorAzul4");  //Se remueve el estilo de proceso de traslado
														$("#"+historia+"-"+ingreso).addClass("fila2");	//Se agrega la clase fila2
														$("#td_traslado_"+historia+"-"+ingreso).html("<div id='msg_recibido_"+historia+"-"+ingreso+"'><b>Paciente Recibido</b></div>"); //Se muestra el mensaje de paciente recibido.
														$("#msg_recibido_"+historia+"-"+ingreso).fadeOut(10000); //Se remueve le mensaje en 10 segundos
														$("#td_traslado_"+historia+"-"+ingreso).removeAttr('title'); //Se inicia en vacio el title
														$("#td_traslado_"+historia+"-"+ingreso).removeAttr('onclick'); //Se remueve el atributo onclick para que no reciban de nuevo.
														$("#td_traslado_"+historia+"-"+ingreso).removeAttr('style'); //Se elimina el atributo style para que el cursor no demuestre accion.
														$("#td_traslado_"+historia+"-"+ingreso).attr('title', 'Paciente recibido'); //Se inicia en vacio el title
														$("#td_traslado_"+historia+"-"+ingreso).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });  //Se inicia de nuevo el title del td.
														$("#alta_proceso_"+historia+"-"+ingreso).removeAttr('disabled'); //Se activa el cajon de alta en proceso
														$("#muerte_"+historia+"-"+ingreso).removeAttr('disabled'); //Se activa el cajon de muerte


													}

												}

												$.unblockUI();
												return;
											}
										}

									});

								}else{

										$.unblockUI();
										return;
										}
							});
					}
				}

			});


}


function cancelarEntregaACirugia(wemp_pmla, historia, ingreso, nombre){

	 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

	jConfirm('¿Cancelará el traslado a cirugia del paciente '+nombre+', ¿Esta seguro?', 'Cancelar traslado a Cirugia', function(r) {

		if(r){

		$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{
					consultaAjax 	: '',
					operacion 		: 'cancelarEntregaACirugia',
					wemp_pmla		: wemp_pmla,
					whis			: historia


				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{
						jAlert(data_json.mensaje);
						$.unblockUI();
						return;
					}
					else{

						jAlert(data_json.mensaje, "Cancelar traslado");
						$("#"+historia+"-"+ingreso).removeClass("colorAzul4");
						$("#"+historia+"-"+ingreso).addClass("fila2");
						$("#img_espe_"+historia+"-"+ingreso).remove();
						$("#canc_traslado_"+historia+"-"+ingreso).remove();
						$.unblockUI();
					}
				}

			});

	}else{
		$.unblockUI();
	}
	});
}


function trasladarayudapac(wcco, wemp_pmla, historia, ingreso, hab_actual){

	 var cco_ayuda = $( "#cco_ayuda" ).val();
	 var wbasedato = $( "#wbasedato" ).val();

	 if(cco_ayuda != ''){

		if( $( "option:selected", $( "#cco_ayuda" ) ).data("saldos").toLowerCase() == 'on' ){
			
			//Entregar paciente a urgencias
			$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{
					consultaAjax 	: 	'',
					operacion 		: 	'pacienteAEspecialidadUrgencias',
					wemp_pmla		: 	wemp_pmla,
					whis			: 	historia,
					wing			: 	ingreso,
					wcco			: 	wcco,
				},
				dataType: "json",
				async: false,
				success:function(data_json) {
					
					if( !data_json.actualizo ){
						jAlert("No se puedo realizar el traslado correctamente", "Alerta");
					}
					else{
						//Entregar paciente a urgencias
						$.ajax({
							url: "Ent_y_Rec_Pac_gestion.php",
							type: "POST",
							data:{
								consultaAjax 	: 	'',
								operacion 		: 	'moverPacHosp',
								wemp_pmla		: 	wemp_pmla,
								wbasedato		: 	wbasedato,
								whis			: 	historia,
								wing			: 	ingreso,
								wcco			: 	wcco,
								went_rec		:	'Ent',
								wccohos			:	"on",
								wid				:	'',	//un id de cencam3
								wccoapl			:	"off",
								wgrabar			:	'ok',
								whab			:	hab_actual,	//habitacion actual
								wccodes1		:	$.trim( cco_ayuda.split("-")[0] ),
								whabdes1		:	"",
								wnum_art		:	"",
								warr_art		:	"",
							},
							dataType: "json",
							async: false,
							success:function(data_json) {
								
								//Recibir paciente en urgencias
								$.ajax({
									url: "Ent_y_Rec_Pac_gestion.php",
									type: "POST",
									data:{
										consultaAjax 	: 	'',
										operacion 		: 	'moverPacHosp',
										wemp_pmla		: 	wemp_pmla,
										wbasedato		: 	wbasedato,
										whis			: 	historia,
										wing			: 	ingreso,
										wcco			: 	wcco,
										went_rec		:	'Rec',
										wccohos			:	"on",
										wid				:	'',	//un id de cencam3
										wccoapl			:	"off",
										wgrabar			:	'ok',
										whab			:	hab_actual,	//habitacion actual
										wccodes1		:	$.trim( cco_ayuda.split("-")[0] ),
										whabdes1		:	"",
										wnum_art		:	"",
										warr_art		:	"",
									},
									dataType: "json",
									async: false,
									success:function(data_json) {
										jAlert("Paciente trasladado correctamente.", "Alerta");
										$( "#"+historia+"-"+ingreso ).css({display:"none"});
										$.unblockUI();
									}
								});
							}
						});
					}
				}
			});
		}
		else{
			
			$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						consultaAjax 	: '',
						operacion 		: 'trasladarayudapac',
						wemp_pmla		: wemp_pmla,
						centroCosto		: wcco,
						whis			: historia,
						wing			: ingreso,
						cco_ayuda		: cco_ayuda,
						wbasedato		: wbasedato,
					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							alert(data_json.mensaje);
							location.reload();
							return;
						}
						else{

							$("#cajon_trasl_ayuda_"+historia+"_"+ingreso).html("<font size=1><b>"+data_json.nombreccoayuda+"</b></font>");
							$("#cajon_trasl_ayuda_"+historia+"_"+ingreso).removeAttr("onclick");
							$("#cajon_trasl_ayuda_"+historia+"_"+ingreso).removeAttr("title");
							$("#cajon_trasl_ayuda_"+historia+"_"+ingreso).attr("title","Paciente en "+data_json.nombreccoayuda);
							$("#cajon_trasl_ayuda_"+historia+"_"+ingreso).removeAttr("style");
							$("#cajon_trasl_ayuda_"+historia+"_"+ingreso).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
							alert(data_json.mensaje);
							$.unblockUI();
						}
					}

				});
		}
	}
	else{
		jAlert("Debe seleccionar un centro de costos de ayuda.", "Tipo de habitacion");
	}

}


function grabar_solicitud(wcco, wemp_pmla, historia, ingreso, hab_actual){

	 var tipo_hab = $( "#tipo_hab" ).val();
	 var obs_soli_cama = $( "#wobs_soli_cama" ).val();
	 var ccoCirugia = $("#ccoCirugia").val();

	 if(tipo_hab != ''){

		$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{
					consultaAjax 	: '',
					operacion 		: 'grabar_solicitarHab',
					wemp_pmla		: wemp_pmla,
					centroCosto		: wcco,
					whistoria		: historia,
					tipo_habitacion	: tipo_hab,
					obs_soli_cama   : obs_soli_cama,
					hab_actual		: hab_actual

				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{
						alert(data_json.mensaje);
						location.reload();
						return;
					}
					else{

						jAlert(data_json.mensaje, "Solicitud de cama");
						$("#pedir_cama_"+historia+"_"+ingreso).attr("src","../../../matrix/images/medical/root/pac_con_sol_cama.png");
						$("#cajon_pedir_cama_"+historia+"_"+ingreso).attr("title","Con solicitud de habitación Nro. "+data_json.id+" <br>sin cama asignada");
						$('#alta_proceso_'+historia+"-"+ingreso).prop('disabled', true); //Se inactiva el alta en proceso

						//Si el centro de costos que solicita es de cirugia no inactiva el cajon de alta definitiva.
						if(ccoCirugia != wcco){
							$('#alta_'+historia+"-"+ingreso).prop('disabled', true); //Se inactiva el alta en proceso
						}

						$('#muerte_'+historia+"-"+ingreso).prop('disabled', true); //se inactiva la muerte
						$("#cajon_pedir_cama_"+historia+"_"+ingreso).removeAttr("onclick"); //Se elimina el atributo onclick para solicitar cama
						$("#cajon_pedir_cama_"+historia+"_"+ingreso).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });

						$.unblockUI();
					}
				}

			});
	}else{

			 jAlert("Debe seleccionar un tipo de habitacion", "Tipo de habitacion");
		 }

}

function trasladoayuda( historia, ingreso, nombre, centro_costo, hab_actual, basedatos )
{

            $.blockUI({ message:    '<img src="../../images/medical/ajax-loader.gif" >',
                css:    {
                    width:  'auto',
                    height: 'auto'
                }
            });
            $.post("gestionEnfermeria.php",
            {
                consultaAjax:       '',
                operacion:       	'trasladoayuda',
                wemp_pmla:          $("#wemp_pmla").val(),
                whistoria:          historia,
                wingreso:           ingreso,
                nombre:             nombre,
				centro_costo:		centro_costo,
				hab_actual:			hab_actual,
				wbasedato:			basedatos
            }
            ,function(data_json) {
                if(data_json.notificacion == 1)
                {

                }
                else
                {
                    $.blockUI({ message: data_json.formulario,
                        css: {  left:   '25%',
                            top:    '10%',
                            width:  '50%',
                            height: 'auto'
                        }
                    });
                }
            },
            "json"
            );

	}


function solicitar_hab( historia, ingreso, nombre, centro_costo, hab_actual )
    {

            $.blockUI({ message:    '<img src="../../images/medical/ajax-loader.gif" >',
                css:    {
                    width:  'auto',
                    height: 'auto'
                }
            });
            $.post("gestionEnfermeria.php",  //whabitacion$wid
            {
                consultaAjax:       '',
                operacion:       	'solicitarHab',
                wemp_pmla:          $("#wemp_pmla").val(),
                whistoria:          historia,
                wingreso:           ingreso,
                nombre:             nombre,
				centro_costo:		centro_costo,
				hab_actual:			hab_actual
            }
            ,function(data_json) {
                if(data_json.notificacion == 1)
                {
                    //
                }
                else
                {
                    $.blockUI({ message: data_json.formulario,
                        css: {  left:   '30%',
                            top:    '10%',
                            width:  '40%',
                            height: 'auto'
                        }
                    });
                }
            },
            "json"
            );

	}


function salidaservicioayuda( whis,wing,wproctraslado,waltaEnProceso,nombre, basedatos, basedatoshce, usuario, id_tabla18, whab_actual, control_just, cco_actual){

	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

	jConfirm('¿Liberará al paciente '+nombre+' de este servicio, ¿Esta seguro?', 'Alta servicio ayuda diagnostica', function(r) {

		if(r){

			$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'liberarpacienteservicioayuda',
						wbasedatos 		: basedatos,
						basedatoshce	: basedatoshce,
						whis			: whis,
						wing			: wing,
						seguridad		: usuario,
						nombre			: nombre

					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							$('#alta_'+whis+"-"+wing).prop('checked', false);
							$.unblockUI();
							return;
						}
						else{
							jAlert( data_json.mensaje, 'ALERTA' );
							$('#'+whis+"-"+wing).remove();
							$.unblockUI();

						}
					}

				});

		}else{

				$('#alta_definitiva_'+whis+"-"+wing).prop('checked', false);
				$.unblockUI();

			}
		});

}

function marcaraltadefinitiva(whis,wing,i,wproctraslado,nombre, basedatos, basedatoshce, usuario){

	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
					css: 	{
								width: 	'auto',
								height: 'auto'
							}
			 });

	jConfirm('¿Dará de alta definitiva a '+nombre+', ¿Esta seguro?', 'Alta definitiva', function(r) {

		if(r){


				 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

				$.ajax({
					url: "../../hce/procesos/agenda_urgencias_por_especialidad.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '11',
						operacion 		: 'marcaraltadefinitiva',
						basedatos 		: basedatos,
						basedatoshce	: basedatoshce,
						paciente		: whis,
						ingreso			: wing,
						seguridad		: usuario

					},
					dataType: "html",
					async: false,
					success:function(data_json) {

						if (data_json != 'ok')
						{
							jAlert( data_json, 'ALERTA' );
							$('#alta_'+whis+"-"+wing).prop('checked', false);
							$.unblockUI();
							return;
						}
						else{
							jAlert( "El paciente "+nombre+" ha sido dado de alta definitiva.", 'ALERTA' );
							$('#'+whis+"-"+wing).remove();
							$.unblockUI();

						}
					}

				});

			}else{

				$('#alta_'+whis+"-"+wing).prop('checked', false);
				$.unblockUI();

			}
		});

	}


function descartar(wemp_pmla, wbasedato, codart, cantidad, whis, wing, ccoorigen, id, obj){

	$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{
				consultaAjax 	: '',
				operacion 		: 'registrarDescarte',
				wemp_pmla		: wemp_pmla,
				wbasedato		: wbasedato,
				codart			: codart,
				whis 			: whis,
				wing			: wing,
				ccoorigen		: ccoorigen,
				cantidad		: cantidad,
				id				: id

			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{

					$(obj).css({'display' : 'none'});


				}
			}

		});

}


//Muestra los medicamnetos pendientes o por descartar del paciente.
function mostrar_med_pend_hosp( celda ){


		if( $("#"+celda) ){

		$.blockUI({
						message: $("#"+celda),
						css: { 		left: 	'10%',
								    top: 	'10%',
								    width: 	'80%',
                                    height: 'auto',
									cursor: ''
							 }

				  });

		}
	}

//Muestra los medicamnetos pendientes o por descartar del paciente.
function mostrar_insumos_pend_hosp( celda ){


		if( $("#"+celda) ){

		$.blockUI({
						message: $("#"+celda),
						css: { 		left: 	'10%',
								    top: 	'10%',
								    width: 	'80%',
                                    height: 'auto',
									cursor: ''
							 }

				  });

		}
	}

//Muestra los medicamnetos pendientes o por descartar del paciente.
function mostrar_med_pend( celda ){


		if( $("#"+celda) ){

		$.blockUI({
						message: $("#"+celda),
						css: { 		left: 	'10%',
								    top: 	'10%',
								    width: 	'80%',
                                    height: 'auto',
									cursor: ''
							 }

				  });

		}
	}


//Muestra los medicamnetos pendientes o por descartar del paciente.
function mostrar_med_pend_saldo( celda ){

		if( $("#"+celda) ){

			$.blockUI({
							message: $("#"+celda),
							css: { 		left: 	'10%',
										top: 	'10%',
										width: 	'80%',
										height: 'auto',
										cursor: ''
								 }

					  });

			}


	}

function EntregaDesdeCirugiaAPiso(whis, wing, nombre, hab_destino, id_solicitud, med_pend){


	if(med_pend == 'off'){

		 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

		}

	jConfirm('¿Entregará el paciente '+nombre+' a la habitación '+hab_destino+', ¿Esta seguro?', 'Entregar a Piso', function(r) {

		if(r){

				 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

				$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'EntregaDesdeCirugiaAPiso',
						whistoria 		: whis,
						wingreso		: wing,
						nombre			: nombre,
						wid 			: id_solicitud,
						hab_destino		: hab_destino
					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							$.unblockUI();
							return;
						}
						else{

							jAlert( data_json.mensaje, 'ALERTA' );
							$("#"+whis+"-"+wing).remove();
							$.unblockUI();

						}
					}

				});
			}else{
				$.unblockUI();
			}
		});

	}

	function EntregarUrgAPiso(whis, wing, nombre, hab_destino, id_solicitud, med_pend){

		if(med_pend == 'off'){

		 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

		}

		jConfirm('¿Entregará el paciente '+nombre+' a la habitación '+hab_destino+', ¿Esta seguro?', 'Entregar a Piso', function(r) {

			if(r){

				 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

				$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'EntregarUrgAPiso',
						whistoria 		: whis,
						wingreso		: wing,
						nombre			: nombre,
						wid 			: id_solicitud

					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							$.unblockUI();
							return;
						}
						else{
							jAlert( data_json.mensaje, 'ALERTA' );
							$("#"+whis+"-"+wing).remove();
							$.unblockUI();

						}
					}

				});


			}else{

				$.unblockUI();
			}
		});

	}


	function EntPacACir(whis, wing, nombre, med_pend){

	if(med_pend == 'off'){

		 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
								css: 	{
											width: 	'auto',
											height: 'auto'
										}
						 });

		}

	jConfirm('¿Entregará el paciente '+nombre+' a cirugia, ¿Esta seguro?', 'Entregar a Cirugia', function(r) {

			if(r){

				$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
									css: 	{
												width: 	'auto',
												height: 'auto'
											}
							 });

				$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'EntPacACir',
						whistoria 		: whis,
						wingreso		: wing,
						nombre			: nombre
					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							$.unblockUI();
							return;
						}
						else{

							jAlert( data_json.mensaje, 'ALERTA' );
							$("#"+whis+"-"+wing).remove();
							$.unblockUI();

						}
					}

				});

			}else{

				$.unblockUI();
			}

		});


	}

	function actualizarTablaMedPorPro(codMed,ido,rondaApl,accion)
	{
		if(accion=="aplicar")
		{
			$("#checkMed_"+codMed+"_"+ido+"_"+rondaApl).prop("disabled", true);
			$("#checkMed_"+codMed+"_"+ido+"_"+rondaApl).prop("checked", true);

			var fila = $("#checkMed_"+codMed+"_"+ido+"_"+rondaApl).parent().parent();

			var celdaAccion = $( "td[id^=tdAccMedPro_]", fila );

			cantAplic = $( "input", celdaAccion ).attr("cantAplicMed");
			$( "input", celdaAccion ).attr("cantAplicMed",parseInt(cantAplic)+1);


			if($( "span", celdaAccion ).html( "Sin aplicar" )!="Aplicado")
			{
				$( "span", celdaAccion ).html( "Aplicado" );
				$( "input", celdaAccion ).val("Aplicado");

				// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";
			}
			$( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";
		}
		else if(accion=="anular")
		{
			//Poner fondo amarillo habilitados
			$('input[id^=checkMed_'+id[1]+'_'+id[2]).each(function(){

				if($(this).parent().hasClass( "fondoAmarillo" ))
				{
					$(this).prop("disabled", false);
				}
			});

			$("#checkMed_"+id[1]+"_"+id[2]+"_"+id[3]).prop("checked", false);

			var fila = $("#checkMed_"+codMed+"_"+ido+"_"+rondaApl).parent().parent();

			var celdaAccion = $( "td[id^=tdAccMedPro_]", fila );
			var accAnterior = $( "input", celdaAccion ).attr("accAnterior");
			var cantAplicac = $( "input", celdaAccion ).attr("cantAplicMed");

			$( "input", celdaAccion ).attr("cantAplicMed",parseInt(cantAplicac)-1);

			var cantAplicac = $( "input", celdaAccion ).attr("cantAplicMed");

			if(cantAplicac == 0)
			{
				$( "span", celdaAccion ).html("Sin aplicar");
				$( "input", celdaAccion ).val("Sin aplicar");

				$( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Sin aplicar";
			}
			else
			{
				$( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";
			}

			// if(accAnterior == "Aplicado")
			// {
				// if(cantAplicac == 0)
				// {
					// $( "span", celdaAccion ).html("Sin aplicar");
					// $( "input", celdaAccion ).val("Sin aplicar");

					// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Sin aplicar";
				// }
				// else
				// {
					// $( "span", celdaAccion ).html( "Aplicado" );
					// $( "input", celdaAccion ).val("Aplicado");

					// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";
				// }
			// }
			// else
			// {
				// $( "span", celdaAccion ).html(accAnterior);
				// $( "input", celdaAccion ).val(accAnterior);

				// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = accAnterior;
			// }


			tablaMed = fila.parent().parent();

			algunMedAplicado = false;
			$(tablaMed).find( "[id^=accMedPro_]").each(function(){

				if($(this).val()=="Aplicado")
				{
					algunMedAplicado = true;
				}
			});


			if(algunMedAplicado == false)
			{
				trPrc = tablaMed.parent().parent();

				var selectEstados = $( "select[id^=westadoexamen_]", trPrc );

				$("option",selectEstados).each(function(){

					if($(this).attr('estcancelado')=="on")
					{
						$(this).prop("disabled", false);
					}
				});
			}
		}

	}

	function aplicarMedPorProced(wemp_pmla,historia,ingreso,ronda,codMed,descMedCom,cantidadDeManejo,cco,fechaAplicacion,noEnviar,unidad,dosis,ido,via,usuario,horaRonda,stock,rondaNum,idSelect,cantAplicaciones)
	{
		aplicar = $( "#checkMed_"+codMed+"_"+ido+"_"+horaRonda ).is(':checked');

		if(aplicar)
		{

			//Consultar saldo antes de aplicar
			$.post("gestionEnfermeria.php",
			{
				operacion:   		'consultarSaldoAntesDeAplicar',
				consultaAjax : 		'',
				wemp_pmla:			wemp_pmla,
				historia:           historia,
				ingreso:           	ingreso,
				codMed:    			codMed,
				cantidadDeManejo : 	cantidadDeManejo,
				dosis : 			dosis

			}
			,function(data) {

				if( data.tieneSaldoSuficiente  == "on")
				{
					// alert("Si tiene saldo suficiente");

					$.post("gestionEnfermeria.php",
					{
						operacion:   		'aplicarMedicamentosPorProced',
						consultaAjax : 		'',
						wemp_pmla:			wemp_pmla,
						historia:           historia,
						ingreso:           	ingreso,
						ronda : 			ronda,
						codMed:    			codMed,
						descMedCom:        	descMedCom,
						cantidadDeManejo : 	cantidadDeManejo,
						cco : 				cco,
						fechaAplicacion : 	fechaAplicacion,
						noEnviar : 			noEnviar,
						unidad : 			unidad,
						dosis : 			dosis,
						ido : 				ido,
						codvia :			via,
						usuario :			usuario,
						stock:				stock,
						rondaNum:			rondaNum

					}
					,function(data) {

						if(data.error == 0)
						{
							$( "#checkMed_"+codMed+"_"+ido+"_"+horaRonda ).attr('medAplicado','on');

							$("option","#westadoexamen_"+idSelect).each(function(){

								if($(this).attr('estcancelado')=="on")
								{
									$(this).prop("disabled", true);
								}
							});

							//Inhabilitar checkMed de la regleta de medicamentos
							cmp = $("#chkAplicarMed_"+codMed+"_"+ido+"_"+horaRonda)[0];
							if(cmp != undefined)
							{
								cambiarVistaEstado( cmp, "aplicar" );
								cambiarEstado( cmp, historia, ingreso, horaRonda );
							}

							var fila = $("#checkMed_"+codMed+"_"+ido+"_"+horaRonda).parent().parent();

							var celdaAccion = $( "td[id^=tdAccMedPro_]", fila );

							$( "span", celdaAccion ).html( "Aplicado" );
							$( "input", celdaAccion ).val("Aplicado");

							cantAplic = $( "input", celdaAccion ).attr("cantAplicMed");
							$( "input", celdaAccion ).attr("cantAplicMed",parseInt(cantAplic)+1);

							$( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";

						}
						else{
							jAlert("Error al aplicar el medicamento", "ALERTA" );
						}
					},"json"
					);
				}
				else
				{
					jAlert("No tiene saldo suficiente", "ALERTA" );
					$( "#checkMed_"+codMed+"_"+ido+"_"+horaRonda ).prop("checked", false);
				}
			},"json"
			);

		}
		else
		{
			$.post("gestionEnfermeria.php",
			{
				operacion:   		'anularAplicarMedicamentosPorProced',
				consultaAjax : 		'',
				wemp_pmla:			wemp_pmla,
				historia:           historia,
				ingreso:           	ingreso,
				ronda : 			ronda,
				codMed:    			codMed,
				descMedCom:        	descMedCom,
				cantidadDeManejo : 	cantidadDeManejo,
				cco : 				cco,
				fechaAplicacion : 	fechaAplicacion,
				noEnviar : 			noEnviar,
				unidad : 			unidad,
				dosis : 			dosis,
				ido : 				ido,
				codvia :			via,
				usuario :			usuario,
				stock:				stock,
				rondaNum:			rondaNum

			}
			,function(data) {
				if(data != "")
				{
					$( "#checkMed_"+codMed+"_"+ido+"_"+horaRonda ).attr('medAplicado','');

					// if(cantAplicaciones==0)
					// {
						// $("option","#westadoexamen_"+idSelect).each(function(){

							// if($(this).attr('estcancelado')=="on")
							// {
								// // $(this).prop("disabled", false);
							// }
						// });
					// }



					//Inhabilitar checkMed de la regleta de medicamentos
					cmp = $("#chkAnularMed_"+codMed+"_"+ido+"_"+horaRonda)[0];

					if(cmp != undefined)
					{
						cambiarVistaEstado( cmp, "anular" );
						cambiarEstado( cmp, historia, ingreso, horaRonda );
					}


					var fila = $("#checkMed_"+codMed+"_"+ido+"_"+horaRonda).parent().parent();

					var celdaAccion = $( "td[id^=tdAccMedPro_]", fila );
					var accAnterior = $( "input", celdaAccion ).attr("accAnterior");
					var cantAplicac = $( "input", celdaAccion ).attr("cantAplicMed");

					$( "input", celdaAccion ).attr("cantAplicMed",parseInt(cantAplicac)-1);

					var cantAplicac = $( "input", celdaAccion ).attr("cantAplicMed");

					if(cantAplicac == 0)
					{
						$( "span", celdaAccion ).html("Sin aplicar");
						$( "input", celdaAccion ).val("Sin aplicar");

						$( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Sin aplicar";
					}
					else
					{
						$( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";
					}


					tablaMed = fila.parent().parent();

					algunMedAplicado = false;
					$(tablaMed).find( "[id^=accMedPro_]").each(function(){

						if($(this).val()=="Aplicado")
						{
							algunMedAplicado = true;
						}
					});


					if(algunMedAplicado == false)
					{
						if(cantAplicaciones==0)
						{
							$("option","#westadoexamen_"+idSelect).each(function(){

								if($(this).attr('estcancelado')=="on")
								{
									$(this).prop("disabled", false);
								}
							});
						}

					}





					// if(accAnterior == "Aplicado")
					// {
						// if(cantAplicac == 0)
						// {
							// $( "span", celdaAccion ).html("Sin aplicar");
							// $( "input", celdaAccion ).val("Sin aplicar");

							// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Sin aplicar";
						// }
						// else
						// {
							// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = "Aplicado";
						// }
					// }
					// else
					// {
						// $( "span", celdaAccion ).html(accAnterior);
						// $( "input", celdaAccion ).val(accAnterior);

						// $( "td[id^=MedAgrupado_]", fila )[0].tooltipText = accAnterior;
					// }


				}
			});
		}
	}

	function marcaraltaenproceso(whis,wing,i,wproctraslado){

		 $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

		$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{
				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'marcaraltaenproceso',
				posicion		: i,
				whis 			: whis,
				wing			: wing,
				wproctraslado	: wproctraslado
			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					jAlert( data_json.mensaje, 'ALERTA' );
					return;
				}
				else{

					$('#'+whis+"-"+wing).addClass("fondoAmarillo");
					$('#alta_'+whis+"-"+wing).prop("disabled",true);
					$('#cajon_pedir_cama_'+whis+"_"+wing).removeAttr("onclick"); //Se inactiva la solicitud de cama porque esta marcando el paciente con alta en proceso.
					$("#cajon_pedir_cama_"+whis+"_"+wing).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 }); //tooltip vacio.
					$.unblockUI();

				}
			}

		});

	}



	function AsignarZona(whis, wing, i, SalaActual, cubiculoActual){

		$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{
				wemp_pmla		: $("#wemp_pmla").val(),
				wsala			: $("#sala"+i).val(),
				consultaAjax 	: '',
				operacion 		: 'AsignarZona',
				posicion		: i,
				whis 			: whis,
				wing			: wing,
				wcubiculoActual	: cubiculoActual,
				SalaActual 		: SalaActual
			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					$('#sala'+i).val(data_json.sala_actual);
					jAlert( data_json.mensaje, 'ALERTA' );
					return;
				}
				else{

					$("#dato_cubiculos"+i).html(data_json.html);

				}
			}

		});

	}

	//Funcion que reasigna ubicacion paar el paciente.
	function reasignarCubiculo(whis, wing, i, cubiculoActual){


		var cubiculoNuevo = $("#cubiculo"+i).val();

		$.post("gestionEnfermeria.php",
		{
				wemp_pmla		: $("#wemp_pmla").val(),
				wcubiculoNuevo	: cubiculoNuevo,
				consultaAjax 	: '',
				operacion 		: 'reasignarCubiculo',
				posicion		: i,
				whis 			: whis,
				wing			: wing,
				wcubiculoActual	: cubiculoActual
			},function(data_json) {

				if (data_json.error == 1)
				{
					jAlert( data_json.mensaje, 'ALERTA' );
					recargar();
					return;
				}
				else{

					recargar();


				}
			},"json"

		);

	}


	function buscar_zonas(){

		var wcco = $('#slCcoDestino').val();

		$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{
				wemp_pmla		: $("#wemp_pmla").val(),
				operacion 		: 'filtrarzonas',
				consultaAjax 	: '',
				wcco			: wcco
			},
			dataType: "json",
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					jAlert( data_json.mensaje, 'ALERTA' );
					return;
				}
				else{

					if(data_json.nro_zonas > 0){
					$("#tabla_zonas").show();
					$("#select_zonas").html(data_json.html);
					}else{

					$("#tabla_zonas").hide();
					$("#select_zonas").html("");
					}

				}
			}

		});

	}



	function cambiarEstado( cmp, his, ing, ronda ){

		var	tabla = cmp.parentNode.parentNode.parentNode;
		var aNecesidad = 0;
		var aplicados = 0;
		var justificados = 0;
		var totalMedicamentos = 0;

		$( "tr", tabla ).each(function(i){

			totalMedicamentos++;

			if( $( "[name=chkAnular]", this )[0] && $( "[name=chkAnular]", this )[0].style.display == '' ){
				aplicados++;
			}
			else if( $( "[name=slJus]", this ).val() != "" ){
				justificados++;
			}
			else if( $( "[name=tdANecesidad]", this ).html() != "No" ){
				aNecesidad++;
			}
		});

		var CLR_VERDE = 1;
		var CLR_AMARILLO = 2;
		var CLR_ROJO = 3;
		var valor = 0;

		//Si el total de medicamentos para una ronda es igual a la cantidad de medicamentos a necesidad entonces no deben salir nada
		if( totalMedicamentos == aNecesidad + justificados + aplicados ){

			if( aplicados > 0 ){
				valor = CLR_VERDE;
			}
			else if( totalMedicamentos == aNecesidad + justificados ){
				//Si los medicamentos no fueron aplicados pero todos son a necesidad o tiene justificacion
				valor = CLR_AMARILLO;
			}
			else{
				valor = CLR_ROJO;
			}
		}
		//Si por lo menos un medicamento obligatorio no fue aplicado
		else{
			valor = CLR_ROJO;
		}

		ronda = ronda*1;

		switch( valor ){

			case CLR_VERDE:
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondoamarillo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondorojo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondoRojo" );
				$( "img", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).eq(0).attr( "src", "/matrix/images/medical/movhos/checkmrk.ico" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ).prepend( $( "img", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).eq(0) );
				$( "span", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).remove();
			break;

			case CLR_AMARILLO:
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondorojo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondoRojo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondoamarillo" );
				$( "img", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).eq(0).attr( "src", "/matrix/images/medical/root/borrarAmarillo.png" );
				// $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .addClass( "fondoamarillo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ).prepend( $( "img", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).eq(0) );
			break;

			case CLR_ROJO:
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondoamarillo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondorojo" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .removeClass( "fondoRojo" );
				$( "img", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).eq(0).attr( "src", "/matrix/images/medical/root/borrar.png" );
				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) .addClass( "fondorojo" );

				var span = document.createElement( "span" );

				$( "#"+his+"-"+ing + " [ronda="+ronda+"]" ).prepend( $( span ) );
				$( span ).addClass( "blink" );
				$( span ).prepend( $( "img", $( "#"+his+"-"+ing + " [ronda="+ronda+"]" ) ).eq(0) );

			break;
		}
	}

	function cambiarJustificacion( cmp, whis, wing, wfecha_actual, whora_par_actual, wpac, wido, idx, servicio_temporal ){

		$.ajax({
			url: "Aplicacion_ipods.php?wido["+idx+"]="+wido+"&wjus["+idx+"]="+$( cmp ).val(),
			type: "POST",
			data:{
				wemp_pmla		: $( "#wemp_pmla" ).val(),
				wcco			: "<?=$ccoayuda?>" == "" ? $( "[name=slCcoDestino]" ).val() : servicio_temporal,
				whis			: whis,
				wing			: wing,
				wfecha_actual	: wfecha_actual,
				whora_par_actual: whora_par_actual,
				wpac			: wpac,
				ccoayuda		: "<?=$ccoayuda?>",
			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					jAlert( data_json.mensaje, 'ALERTA' );
					return;
				}
				else{


				}
			}
		});

		cambiarEstado( cmp, whis, wing, whora_par_actual );
		cambiarVistaEstado( cmp, 'justificar' );
	}



	function cambiarVistaEstado( cmp, estado ){

		var fila = cmp.parentNode.parentNode;

		if( estado == 'aplicar' ){

			$( "[name=chkAnular]", fila ).css({ display: '' });
			$( cmp ).css({ display: 'none' });

			$( "img", fila ).eq(0).attr( "src", "/matrix/images/medical/movhos/checkmrk.ico" );
			$( "img", fila ).eq(0).parent().removeClass( "fondorojo" );
			$( "img", fila ).eq(0).parent().removeClass( "fondoamarillo" );
		}
		else{

			if( estado == 'anular' )
				$( "[name=chkAplicar]", fila ).css({ display: '' });

			if( cmp.tagName.toLowerCase() == 'input' )
				$( cmp ).css({ display: 'none' });

			if( $( "[name=chkAnular]", fila ).length > 0 && $( "[name=chkAnular]", fila ).is(":visible") == true
			){
				$( "img", fila ).eq(0).parent().removeClass( "fondorojo" );
				$( "img", fila ).eq(0).parent().removeClass( "fondoRojo" );
				$( "img", fila ).eq(0).parent().removeClass( "fondoamarillo" );
				$( "img", fila ).eq(0).attr( "src", "/matrix/images/medical/movhos/checkmrk.ico" );
			}
			else if( $( "[name=tdANecesidad]", fila ).html() != 'No' || $( "[name=slJus]", fila ).val() != '' ){
				$( "img", fila ).eq(0).parent().removeClass( "fondorojo" );
				$( "img", fila ).eq(0).parent().removeClass( "fondoRojo" );
				$( "img", fila ).eq(0).attr( "src", "/matrix/images/medical/root/borrarAmarillo.png" );
			}
			else{
				$( "img", fila ).eq(0).attr( "src", "/matrix/images/medical/root/borrar.png" );
				$( "img", fila ).eq(0).parent().addClass( "fondorojo" );
			}
		}
	}

	function aplicarArticulo( cmp, whis, wing, wfecha_actual, whora_par_actual, whab, wpac, wido, idx, art, servicio_temporal, cvi ){

		servicio_temporal = servicio_temporal ? servicio_temporal : "";

		var addUrl = "";
		if( cvi ){
			addUrl = "&dosisIpd["+idx+"]=" +$( cmp ).val();
		}

		$.ajax({
			url: "Aplicacion_ipods.php?wido["+idx+"]="+wido+"&wapl["+idx+"]=on"+addUrl,
			type: "POST",
			data:{
				wemp_pmla		: $( "#wemp_pmla" ).val(),
				wcco			: "<?=$ccoayuda?>" == "" ? $( "[name=slCcoDestino]" ).val() : servicio_temporal,
				whis			: whis,
				wing			: wing,
				wfecha_actual	: wfecha_actual,
				whora_par_actual: whora_par_actual,
				whab			: whab,
				wpac			: wpac,
				ccoayuda		: "<?=$ccoayuda?>",
				// "wido["+idx+"]"	: wido,
				// "wapl["+idx+"]"	: 'on'
			},
			async: false,
			success:function(data_json) {

				//Valido si fue aplicado correctamente
				$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $( "#wemp_pmla" ).val(),
						wcco			: "<?=$ccoayuda?>" == "" ? $( "[name=slCcoDestino]" ).val() : servicio_temporal,
						whis			: whis,
						wing			: wing,
						wfecha			: wfecha_actual,
						whora_a_grabar	: whora_par_actual,
						wart			: art,
						operacion		: 'validarAplicacion',
						wido			: wido,
						consultaAjax	: ''
					},
					async: false,
					success:function(data_json) {

						if( $.trim( data_json ) == "2" ){
							jAlert( "No se pudo realizar la aplicación.\n-El medicamento se encuentra sin saldo.", 'ALERTA' );
						}
						else{
							cambiarVistaEstado( cmp, "aplicar" );
							cambiarEstado( cmp, whis, wing, whora_par_actual );

							//Inhabilitar checkMed de la modal de procedimientos
							id = $( cmp ).attr('id');
							id = id.split("_");

							if($("#checkMed_"+id[1]+"_"+id[2]+"_"+id[3])[0] != undefined)
							{
								actualizarTablaMedPorPro(id[1],id[2],id[3],"aplicar");
							}
						}
					}
				});

			}
		});

		cmp.checked = false;
	}

	function anularAplicacion( cmp, whis, wing, wfecha_actual, whora_par_actual, whab, wpac, wido, idx, art, wdosis, wcanfra, wnoenviar, wStock, servicio_temporal ){

		$.ajax({
			url: "Aplicacion_ipods.php?wido["+idx+"]="+wido+"&wapl["+idx+"]=off&wanular["+idx+"]=on&wart["+idx+"]="+art+"&wdosis["+idx+"]="+wdosis+"&wcanfra["+idx+"]="+wcanfra+"&wnoenviar["+idx+"]="+wnoenviar+"&wStock["+idx+"]="+wStock,
			type: "POST",
			data:{
				wemp_pmla		: $( "#wemp_pmla" ).val(),
				wcco			: "<?=$ccoayuda?>" == "" ? $( "[name=slCcoDestino]" ).val() : servicio_temporal,
				whis			: whis,
				wing			: wing,
				wfecha_actual	: wfecha_actual,
				whora_par_actual: whora_par_actual,
				whab			: whab,
				wpac			: wpac,
				ccoayuda		: "<?=$ccoayuda?>",
			},
			async: false,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
					return;
				}
				else{

					//Habilitar checkMed de la modal de procedimientos
					id = $( cmp ).attr('id');
					id = id.split("_");

					if($("#checkMed_"+id[1]+"_"+id[2]+"_"+id[3])[0] != undefined)
					{
						actualizarTablaMedPorPro(id[1],id[2],id[3],"anular");
					}

				}
			}
		});

		cmp.checked = false;
		cambiarVistaEstado( cmp, "anular" );
		cambiarEstado( cmp, whis, wing, whora_par_actual );
	}


	function marcar_prioritario(wemp_pmla, wfec,wexam,wing,whis,wordennro,wordite,campo, wid,whce,wtexto_examen,westado_registro,wbasedato, wfechadataexamen,tieneMedicamentos){


		if ($('#prioritario_'+wordennro+'_'+wordite).is(':checked')) {

			var westado_prioritario = 'on';
			var anterior = 'off';

		}else{

			var westado_prioritario = 'off';
			var anterior = 'on';
		}

		$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{

					operacion:   			'marcar_prioritario',
					consultaAjax : 			'',
					wemp_pmla:				wemp_pmla,
					wexam:           		wexam,
					wfec:           		wfec,
					wing:    				wing,
					whis:         			whis,
					wordennro : 			wordennro,
					wordite : 				wordite,
					westado_prioritario: 	westado_prioritario,
					wid : 					wid,
					whce :					whce,
					wbasedato :				wbasedato,
					wtexto_examen:			wtexto_examen,
					wfechadataexamen:		wfechadataexamen,
					anterior:				anterior

				},
				dataType: "json",
				async: false,
				success:function(data_json) {

					if (data_json.error == 1)
					{

					}
					else{

						if(westado_prioritario == 'on'){
							$('#cajonprioritario_'+wordennro+'_'+wordite).addClass("fondoNaranja");
						}else{
							$('#cajonprioritario_'+wordennro+'_'+wordite).removeClass("fondoNaranja");
						}

					}
				}

			});


	}


	function cambiar_estado_examen(wemp_pmla, wfec,wexam,wing,whis,wordennro,wordite,campo, wid,whce,wtexto_examen,westado_registro,wbasedato, wfechadataexamen,anterior,tieneMedicamentos)
    {

        var westado = $("#westadoexamen_"+wid).val();
		var accionMed = $('option:selected',"#westadoexamen_"+wid).attr('accmed');
		var medAsociado = $("#westadoexamen_"+wid).attr('medAsociado');

		// if(medAsociado!="" && tieneMedicamentos=="on")
		if(medAsociado!="")
		{
			$("#westadoexamen_"+wid).val(anterior);
			jAlert("No puede modificar el estado ya que el procedimiento tiene medicamentos asociados y  Ordenes esta abierto por "+medAsociado+" intente de nuevo en un momento.","ALERTA" );
		}
		else
		{
			var estRealizado = $('option:selected',"#westadoexamen_"+wid).attr('estrealizado');

			if(estRealizado=="on")
			{
				var cadenaSinAplicar = "";
				var cadenaSinDispensar = "";
				$('table[id=medPorProc_'+wexam+'_'+wordennro+'_'+wordite+'] input[id^=accMedPro_').each(function(){

					if($(this).val() == "Sin aplicar")
					{
						id = $(this).attr('id');
						id = id.split("_");


						med = $.trim($("#MedAgrupado_"+id[1]+"_"+id[2]+"_"+id[3]+"_"+id[4]+"_"+id[5]).html());
						cadenaSinAplicar += med+",";


						// aplic = "off";
						// $('input[id^=checkMed_'+id[4]+'_'+id[5]).each(function(){

							// if($(this).attr('medaplicado')=="on")
							// {
								// aplic = "on";
							// }
						// });

						// if(aplic == "off")
						// {console.log("hey1")
							// med = $.trim($("#MedAgrupado_"+id[1]+"_"+id[2]+"_"+id[3]+"_"+id[4]+"_"+id[5]).html());
							// cadenaSinAplicar += med+",";
						// }
					}

					if($(this).val() == "Sin dispensar")
					{

						id = $(this).attr('id');
						id = id.split("_");


						med = $.trim($("#MedAgrupado_"+id[1]+"_"+id[2]+"_"+id[3]+"_"+id[4]+"_"+id[5]).html());
						cadenaSinDispensar += med+",";


					}
				});

				// if(cadenaSinAplicar=="")
				if(cadenaSinAplicar=="" && cadenaSinDispensar=="")
				{
					//Si cambia el estado
					$.post("gestionEnfermeria.php",
						{
							operacion:   		'cambiar_estado_examen',
							consultaAjax : 		'',
							wemp_pmla:			wemp_pmla,
							wexam:           	wexam,
							wfec:           	wfec,
							wing:    			wing,
							whis:         		whis,
							wordennro : 		wordennro,
							wordite : 			wordite,
							westado : 			westado,
							wid : 				wid,
							whce :				whce,
							wbasedato :			wbasedato,
							wtexto_examen:		wtexto_examen,
							wfechadataexamen:	wfechadataexamen,
							accionMed:			accionMed,
							tieneMedicamentos:	tieneMedicamentos

						}
						,function(data) {

							// console.log(data);

							if(data.error == 0)
							{
								// if(data.length > 2)
								if(data.mensaje != "")
								{
									// jAlert(data, "ALERTA" );
									jAlert(data.mensaje, "ALERTA" );

									$('table[id=medPorProc_'+wexam+'_'+wordennro+'_'+wordite+'] input[id^=checkMed').each(function(){
										$(this).prop("disabled", true);
									});

									$('table[id=medPorProc_'+wexam+'_'+wordennro+'_'+wordite+'] input[id^=accMedPro_').each(function(){

										id = $(this).attr("id");
										id = id.split("_");

										$("input[id^='chkAnularMed_"+id[4]+"_"+id[5]+"_").prop("disabled", true);
										$("input[id^='chkAnularMed_"+id[4]+"_"+id[5]+"_").attr("deshabilitadoPorRealizado", "on");

									});

									// $("#chkAnularMed_"+codMed+"_"+ido+"_"+horaRonda)
								}
							}
							else
							{
								$("#westadoexamen_"+wid).val(anterior);
								jAlert("No puede modificar el estado ya que el procedimiento tiene medicamentos asociados y  Ordenes esta abierto por "+data.medico+" intente de nuevo en un momento.","ALERTA" );
							}


							//Primero se envia los mensajes de interoperabilidad con HIRUKO - IMEXHS
							$.ajax({
								url	: "../../hce/procesos/ordenes.inc.php",
								type: "POST",
								data:{
									historia			: whis,
									ingreso				: wing,
									consultaAjaxKardex	: 'imagenologiaHiruko',
									consultaAjax		: '' ,
									wemp_pmla			: wemp_pmla,
								},
								async: false,
								success: function(data){

									//Una vez terminado los proceso de envio de datos de HIRUKO - IMEX se hace los de laboratorio
									//Esto es para crear el msgHL para realizar la orden de trabajo para laboratorio
									$.ajax({
											url: "../../hce/procesos/ordenes.inc.php",
											type: "POST",
											data:{
												consultaAjaxKardex	: 'ordenTrabajoLaboratorio',
												wemp_pmla			: wemp_pmla,
												wusuario		  	: $('#usuario').val(),
												historia		  	: whis,
												ingreso			  	: wing,
											},
											async: false,
											success:function(data_json) {

											}
										}
									);
								}
							});


						},"json"
						);
				}
				else
				{
					var mensAlert = "";
					var mensAlertSinAplicar = "";
					var mensAlertSinDispensar = "";

					//Si no cambia el estado, vuelve al estado anterior y muestra alert mensaje
					$("#westadoexamen_"+wid).val(anterior);

					if(cadenaSinAplicar != "")
					{
						mensAlertSinAplicar = "- Los medicamentos "+cadenaSinAplicar.substr(0,cadenaSinAplicar.length-1)+" dispensados (entregados) están sin aplicar.\n";
					}
					if(cadenaSinDispensar != "")
					{
						mensAlertSinDispensar = "- Los medicamentos "+cadenaSinDispensar.substr(0,cadenaSinDispensar.length-1)+" no han sido dispensados (entregados) y debe aplicarlos para cambiar el estado del procedimiento.\n";
					}

					mensAlert = mensAlertSinAplicar + mensAlertSinDispensar;
					if(mensAlert != "")
					{
						jAlert(mensAlert,"ALERTA" );
					}
				}
			}
			else
			{
				$.post("gestionEnfermeria.php",
					{
						operacion:   		'cambiar_estado_examen',
						consultaAjax : 		'',
						wemp_pmla:			wemp_pmla,
						wexam:           	wexam,
						wfec:           	wfec,
						wing:    			wing,
						whis:         		whis,
						wordennro : 		wordennro,
						wordite : 			wordite,
						westado : 			westado,
						wid : 				wid,
						whce :				whce,
						wbasedato :			wbasedato,
						wtexto_examen:		wtexto_examen,
						wfechadataexamen:	wfechadataexamen,
						accionMed:			accionMed,
						tieneMedicamentos:	tieneMedicamentos

					}
					,function(data) {
						// console.log(data);
						if(data.error == 0)
						{
							// if(data.length > 2)
							if(data.mensaje != "")
							{
								jAlert(data.mensaje, "ALERTA" );

								$('table[id=medPorProc_'+wexam+'_'+wordennro+'_'+wordite+'] input[id^=checkMed').each(function(){
									$(this).prop("disabled", true);
								});

								$('table[id=medPorProc_'+wexam+'_'+wordennro+'_'+wordite+'] input[id^=accMedPro_').each(function(){

									id = $(this).attr("id");
									id = id.split("_");

									if($("input[id^='chkAnularMed_"+id[4]+"_"+id[5]+"_").attr("deshabilitadoPorRealizado")=="on")
									{
										$("input[id^='chkAnularMed_"+id[4]+"_"+id[5]+"_").prop("disabled", false);
										$("input[id^='chkAnularMed_"+id[4]+"_"+id[5]+"_").removeAttr("deshabilitadoPorRealizado");

									}

								});
							}


							//Primero se envia los mensajes de interoperabilidad con HIRUKO - IMEXHS
							$.ajax({
								url	: "../../hce/procesos/ordenes.inc.php",
								type: "POST",
								data:{
									historia			: whis,
									ingreso				: wing,
									consultaAjaxKardex	: 'imagenologiaHiruko',
									consultaAjax		: '' ,
									wemp_pmla			: wemp_pmla,
								},
								async: false,
								success: function(data){

									//Una vez terminado los proceso de envio de datos de HIRUKO - IMEX se hace los de laboratorio
									//Esto es para crear el msgHL para realizar la orden de trabajo para laboratorio
									$.ajax({
											url: "../../hce/procesos/ordenes.inc.php",
											type: "POST",
											data:{
												consultaAjaxKardex	: 'ordenTrabajoLaboratorio',
												wemp_pmla			: wemp_pmla,
												wusuario		  	: $('#usuario').val(),
												historia		  	: whis,
												ingreso			  	: wing,
											},
											async: false,
											success:function(data_json) {

											}
										}
									);
								}
							});

						}
						else
						{
							$("#westadoexamen_"+wid).val(anterior);
							jAlert("No puede modificar el estado ya que el procedimiento tiene medicamentos asociados y  Ordenes esta abierto por "+data.medico+" intente de nuevo en un momento.","ALERTA" );
						}




					},"json"
				);
			}
		}
	}

	function fnMostrarProcedimientos( historia, ingreso ){

		celda = historia+"_"+ingreso;

		if( $("#div_proc_"+celda) ){

		$.blockUI({
						message: $("#div_proc_"+celda),
						css: { 		left: 	'10%',
								    top: 	'10%',
								    width: 	'80%',
                                    height: 'auto',
									cursor: ''
							 }

				  });

		}
	}


	function fnMostrar( keyHoras, historia, ingreso ){



			$.blockUI({ message: "<div id='mostrar_login'><center><table><tr><td>&nbsp;</td><td>&nbsp;</td></tr><tr><td class='fila1'><b>Digite su clave personal:</b></td><td><input type='password' id='contrasena' onkeypress='enter(event)'></td></tr><tr><td rowspan=2><td><input type='button' value='Enviar' id='validar_contrasena' onclick='validar_clave(\""+keyHoras+"\",\""+historia+"\",\""+ingreso+"\", event);'></td></tr></table><br><input value='Cerrar' onclick='$.unblockUI();' style='width:100' type='button'><br><center></div>",
							css: { left: ( $(window).width() - 1200 )/2 +'px',

								  width: '1000px',
								  cursor: ''
								 }
					  });


	}

	function enter(e){

		tecla = (document.all) ? e.keyCode : e.which;
		if (tecla==13){

			$("#validar_contrasena").click();
		}
	}


	function validar_clave(keyHoras,historia,ingreso,e){

		var usuario = $("#user").val();
		var contrasena = $("#contrasena").val();

		if(contrasena == ''){

			jAlert( "Dede ingresar su contraseña.", 'ALERTA' );
			return;
		}


		$.ajax({
					url: "gestionEnfermeria.php",
					type: "POST",
					data:{
						wemp_pmla		: $("#wemp_pmla").val(),
						consultaAjax 	: '',
						operacion 		: 'validar_clave',
						usuario 		: usuario,
						contrasena 		: contrasena
					},
					dataType: "json",
					async: false,
					success:function(data_json) {

						if (data_json.error == 1)
						{
							jAlert( data_json.mensaje, 'ALERTA' );
							return;
						}
						else{

							fnMostrarArticulos( keyHoras,historia,ingreso );

						}
					}

				});


	}

	function fnMostrarArticulos( keyHoras,historia,ingreso ){

		var celda = $("#lista_articulos_"+keyHoras+"_"+historia+"_"+ingreso);

		if( $("div", celda ) ){

			$.blockUI({ message: celda,
							css: { left: ( $(window).width() - 1200 )/2 +'px',
								    top: ( $(window).height() - celda.height() )/2 +'px',
								  width: '1200px',
								  cursor: ''
								 }
					  });

		}
	}

	function recargar(){
		document.getElementById( "mostrar" ).value = "on";
		document.forms[0].submit();
	}

	function abrirNuevaVentana( path ){
		window.open(path,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
	}

	/****************************************************************************************
	 * Esta funcion muestra un tooltip para las leyendas
	 ****************************************************************************************/
	function mostrarTooltip( celda ){

		if( !celda.tieneTooltip ){
			$( "*", celda ).tooltip();
			celda.tieneTooltip = 1;
		}
	}

	//Abre las ordenes en una nueva ventana
	function abrir_ordenes(path)
	{
		$.ajax({
			  type: "POST",
			  url:  '../../hce/procesos/ordenes_close.php',
			  data: {"wordenes" : "1"},
			  async:false,
			  success: function(data){wordenes = data;}
		  });

		if(wordenes == 1)
		{
			alert("Ya están abiertas las ordenes de este u otro paciente, debe cerrarlas para acceder a otras ordenes.");
		}
		else
		{

			window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');

		}
	}

	function registro_log_contingencia(wemp_pmla, wbasedato, cco, wuser, wfecha, whora, wminutos, wsegundos, raiz_url, control_contingencia ){

		PNotify.removeAll()

		$("#contingencia_"+cco).removeClass();
		$("#contingencia_"+cco).addClass('msg');
		$("#contingencia_"+cco).prop('title', '');

		$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });

		$.ajax({
			url: "gestionEnfermeria.php",
			type: "POST",
			data:{

				operacion:   		'registro_log_contingencia',
				consultaAjax : 		'',
				wemp_pmla:			wemp_pmla,
				cco:				cco,
				wbasedato:			wbasedato,
				wuser:				wuser

			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{

				}
				else{
					if(control_contingencia == 'off'){
						document.location.href='descargar_contingencia.php?wraiz_url='+raiz_url+'&wcco='+cco+'&wemp_pmla='+wemp_pmla+'&wfecha='+wfecha+'&whora='+whora+'&wminutos='+wminutos+'&wsegundos='+wsegundos;
					}
				}
			}

		});

	}

	//FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path)
    {

	$.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');

	setTimeout(function(){
		  $.unblockUI();
		}, 3000);


    }

	/****************************************************************************************************
	 * Envento que se ejecuta al cargar la pagina, inicia un blink sobre las etiquetas con blink y
	 * la cuenta para recargar la pagina
	 ****************************************************************************************************/
	window.onload = function(){

		if($("#control_tiempo").length > 0){
			var tiempo = $("#control_tiempo").val();
			timer = setTimeout( "recargar();", tiempo );
		}else{

			if( document.getElementById( "inRecargar" ).value == "on" ){
				timer = setTimeout( "recargar();", 5*60000 );	//Recarga la pagina cada 5 minutos
			}
		}

		setInterval(function() {
     
		$('.blink').effect("pulsate", {}, 5000);

	}, 1000);


	}
	//------------------
	//--------------------------
	function grabar_tipo_habitacion_facturacion(hab,historia,ingreso,cco)
	{
		$.post("gestionEnfermeria.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'grabar_tipo_hab',
					wemp_pmla		: $('#wemp_pmla').val(),
					historia		: historia,
					ingreso			: ingreso,
					thab_ant		: $("#h_thab_ant_"+hab).val(),
					thab_act		: $("#tipo_hab_facturacion_"+hab).val(),
					hab				: hab,
					cco				: cco

				}
				, function(data) {
					 $("#h_thab_ant_"+hab).val($("#tipo_hab_facturacion_"+hab).val());
				});

	}

	function grabar_tipo_habitacion_facturacion_2(hab,historia,ingreso,cco,habitacioncambio)
	{
		$.post("gestionEnfermeria.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'grabar_tipo_hab',
					wemp_pmla		: $('#wemp_pmla').val(),
					historia		: historia,
					ingreso			: ingreso,
					thab_ant		: $("#h_thab_ant_"+hab).val(),
					thab_act		: habitacioncambio,
					hab				: hab,
					cco				: cco

				}
				, function(data) {
					 $("#h_thab_ant_"+hab).val(habitacioncambio);
				});

	}


	$(document).ready(function()
	 {

		var alerta_contingencia = $("#alerta_contingencia").val();
		var mostrar_alerta_contingencia = $("#mostrar_alerta_contingencia").val();

		if(alerta_contingencia == 'on' &&  mostrar_alerta_contingencia == 'on'){

			var wemp_pmla = $('#wemp_pmla').val();
			var cco = $( "[name=slCcoDestino]" ).val();
			var fecha = $( "[name=fecha]" ).val();

			mostrar_mensaje_contingencia(wemp_pmla, cco, fecha);
		}

		var texto_nombre_cco = $("#texto_nombre_cco").val();

		$(".titulopagina").text(texto_nombre_cco);

		$('#slCcoDestino').change(function(){

			buscar_zonas();

		});

		//Marca en azul los pacientes con habitacoin asignada en urgencias.
		$(".asignar_clase_azul").each(function(){
			$('#'+$(this).val()).removeClass("fila1");
			$('#'+$(this).val()).removeClass("fila2");
			$('#'+$(this).val()).addClass("colorAzul4");
		});

		$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".msg_alta_proceso").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".msg_muerte").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".msg_alta_definitiva").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".pendientes").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".mostrarentidad").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".mover_pac").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
		$(".cancelar_recibo").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });

		//Tooltip medicamentos relacionados con procedimientos (procedimientos agrupados)
		cadenaMedProc = $( "#cadenaProceConMed" ).val();
		ttProcMed = cadenaMedProc.split("|")

		for(var y=0;y<ttProcMed.length-1;y++)
		{
			//Tooltip por medicamento
			$( "#MedAgrupado_"+ttProcMed[y] ).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: 15, top: 25 });
		}

		// se agrega esta parte para cambios de habitacion automaticos
		$(".cambio_habitacion_automatico").each(function(){
			//alert($(this).attr('tipo_habitacion_cambio'));
			grabar_tipo_habitacion_facturacion_2($(this).attr('habitacion'),$(this).attr('historia'),$(this).attr('ingreso'),$(this).attr('cco'),$(this).attr('tipo_habitacion_cambio'));
			$("#tipo_hab_facturacion_"+$(this).attr('habitacion')).val($(this).attr('tipo_habitacion_cambio'));
			//alert("cambio");
		});

		//Aqui se ecribe el centro de costos en la interfaz cuando la variable ccoyuda tiene datos
		if($("#texto_td_cco").val() != undefined){
			$("#td_cco").html("<b>"+$("#texto_td_cco").val()+"</b>");
		}
		
		$( "[data_show_title] [title]" ).tooltip({showURL:false})

	 });

	window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
    }
	
	function tomarMuestra( cmp, whis, wing ){
		
		let contenedor = $( "#div_proc_"+whis+"_"+wing );
		
		if( $( "input:checked", contenedor ).length > 0 ){
			$( ".btnCerrar", contenedor ).css({ display: "none" });
			$( ".btnEnvioTomaMuestras", contenedor ).css({ display: "" });
		}
		else{
			$( ".btnCerrar", contenedor ).css({ display: "" });
			$( ".btnEnvioTomaMuestras", contenedor ).css({ display: "none" });
		}
	}
	
	
	function envioTomaMuestras( contenedor, whce, wmovhos, whis, wing ){
		
		var data = [];
		
		$( "input:checked", contenedor ).each(function(){
			data.push({
				tor	: $( this ).data( "tor" ),
				nor	: $( this ).data( "nor" ),
				item: $( this ).data( "item" ),
			});
		});
		
		console.log(data);
		
		jConfirm( "Usted será responsable de la toma de muestra.<br><br>Desea tomar la muestra?","TOMA DE MUESTRAS",function(resp){
			
			if( resp ){
				
				$.post("../../hce/procesos/ordenes.inc.php",
				{
					consultaAjax 		: '',
					consultaAjaxKardex 	: 'tomarMuestra',
					wemp_pmla			: $('#wemp_pmla').val(),
					whce				: whce,
					wmovhos				: wmovhos,
					wuser				: $('#usuario').val().split("-")[1],
					ordenes				: data,
				},
				function(data_resp) {
					
					let msgTomaMuestras = '';
					
					$( data_resp ).each(function(){
						
						let data = this;
					
						if( $.trim(data) ){
							
							let cmp = $("input[data-tor='"+data.tor+"'][data-nor='"+data.nor+"'][data-item='"+data.item+"']");
							
							let _parent = $( cmp ).parent();
							
							$( cmp ).remove();
							
							let info_toma_muestra = data.desc+"<br>"+data.fecha+" "+data.hora;
							
							if( data.msg != '' ){
								msgTomaMuestras += "<br>"+data.msg;
							}
							
							if( $( cmp ).data( "realizaestudio" ) == 1 ){
								
								_parent.html( 
									"<span onclick='imprimirSticker( \""+$( cmp ).data( "estudio" )+"\",\""+data.tor+"\",\""+data.nor+"\",\""+data.item+"\",\""+whis+"\",\""+wing+"\" );'>"
									+'<img src="/matrix/images/medical/movhos/checkmrk.ico" width="25px" style="cursor:pointer;" title="<span style=font-size:10pt;>'+info_toma_muestra+'</span>">'
									+'<img src="../../images/medical/hce/icono_imprimir.png" width="25px" style="cursor:pointer;">'
									+'</span>'
								);
							}
							else{
								_parent.html( "<img src='/matrix/images/medical/movhos/checkmrk.ico' width='25px' style='cursor:pointer;' title='<span style=font-size:10pt;>"+info_toma_muestra+"</span>' />" );
							}
							
							$( "[title]", _parent ).tooltip({showURL:false});
						}
						else{
							jAlert( "No se logró tomar la muestra, intentelo de nuevo más tarde", "ALERTA" );
							$( cmp )[0].checked = false; 
						}
					});
					
					if( $( "input:checked", contenedor ).length == 0 ){
						$( ".btnCerrar", contenedor ).css({ display: "" });
						$( ".btnEnvioTomaMuestras", contenedor ).css({ display: "none" });
						
						if( msgTomaMuestras == '' ){
							jAlert( "Muestras tomadas correctamente", "ALERTA" );
						}
						else{
							jAlert( msgTomaMuestras, "ALERTA" );
						}
					}
					
					//Primero se envia los mensajes de interoperabilidad con HIRUKO - IMEXHS
					$.ajax({
						url	: "../../hce/procesos/ordenes.inc.php",
						type: "POST",
						data:{
							historia			: whis,
							ingreso				: wing,
							consultaAjaxKardex	: 'imagenologiaHiruko',
							consultaAjax		: '' ,
							wemp_pmla			: $('#wemp_pmla').val(),
						},
						async: true,
						success: function(data){

							//Una vez terminado los proceso de envio de datos de HIRUKO - IMEX se hace los de laboratorio
							//Esto es para crear el msgHL para realizar la orden de trabajo para laboratorio
							$.ajax({
									url: "../../hce/procesos/ordenes.inc.php",
									type: "POST",
									data:{
										consultaAjaxKardex	: 'ordenTrabajoLaboratorio',
										wemp_pmla			: $('#wemp_pmla').val(),
										wusuario		  	: $('#usuario').val(),
										historia		  	: whis,
										ingreso			  	: wing,
									},
									async: true,
									success:function(data_json) {

									}
								}
							);
						}
					});
					
				}, "json" )
			}
			else{
				$( cmp )[0].checked = false; 
			}
		});
		
	}
	
	function cancelarMuestras( contenedor ){
		
		$( "input:checked", contenedor ).prop({ checked: false });
		
		$( ".btnCerrar", contenedor ).css({ display: "" });
		$( ".btnEnvioTomaMuestras", contenedor ).css({ display: "none" });
	}
	
	function imprimirSticker( estudio, tor, nor, item, whis, wing ){
	
		jConfirm( "Desea imprimir el sticker para el estudio <b>" + estudio + "</b>?", "ALERTA", function( resp ){
			
			if( resp ){
				
				$.post("../../hce/reportes/HCE_Sticker_GA.php",
					{
						consultaAjax: '',
						whis		: whis,
						wing		: wing,
						wip			: $("#wipimpresoraga").val(),
						wtor		: tor,	//tipo de orden
						wnor		: nor,	//numero de orden
						witem		: item,	//item de orden
					}, 
					function(data){},
				);
			}
		});
	}
	
	function ingresoGestion()
	{
		let ccoGestion = $("#slCcoDestino").val();
		
		let cco = ccoGestion.split("-");
		
		let usuarioHabilitado = consultarUsuarioHabilitado(cco[0]);
		
		// usuarioHabilitado = false;
		if(usuarioHabilitado)
		{
			document.forms[0].submit();
		}
		else
		{
			jAlert( "El usuario no esta habilitado para acceder a pacientes de este centro de costos.", 'ALERTA' );
		}
	}
	
	function consultarUsuarioHabilitado(cco)
	{
		let usuarioHabilitado = true;
		$.ajax({
				url: "gestionEnfermeria.php",
				type: "POST",
				data:{

					consultaAjax 	: '',
					operacion 		: 'consultarUsuarioHabilitado',
					wbasedato		: $("#wbasedato").val(),
					wemp_pmla		: $("#wemp_pmla").val(),
					usuario			: $("#user").val(),
					cco				: cco,
				},
				dataType: "json",
				async: false,
				success:function(resultado) {
					usuarioHabilitado = resultado;
				}

			});
		
		return usuarioHabilitado;
	}

</script>
</head>
<body>
<?php

$actualiz="2020-05-15";
//TABLA TEMPORAL Y CONSULTANDO TODOS LOS MEDICAMENTOS DE LA 15 EN UN SOLO PASO

/**********************************************************************************************************************************************************
 * Fecha de creacion:	Diciembre 19 de 2011
 * Por:					Edwin Molina Grisales
 * Descripción general:	Mostrar las aplicaciones faltantes por cama por cada ronda, basada en el kardex
 **********************************************************************************************************************************************************/

/**********************************************************************************************************************************************************
 * Especificaciones:
 *
 * - Por cada cama mostrar en que rondas no se ha aplicado medicamentos, estas estarán de color rojo
 * - Por cada cama que se encuentre parcialmente aplicada, se encontrará de color amarillo
 * - Por cada cama que se encuentre totalmente aplicada, se encontrará de color verde
 * - Al dar click sobre una hora, mostrar un pequeño detalle de como se encuentra las aplicaciones del paciente para la hora determinada
 **********************************************************************************************************************************************************/

/**********************************************************************************************************************************************************

 * Modificaciones:
 * 2020-08-12: Edwin MG				- Se hacen cambios varios para poder trasladar pacientes a otros servicio (ejemplo, urgencias) y se parametriza para por
 *									  cco si el cco de costo al cual va a ser traladado el paciente debe tener saldo 0 (movhos 11 campo ccosst = on)
 *									  Si este campo está inactivo (movhos 11 campo ccosst = off) se asuma que es un traslado temporal
 * 2020-08-11: Edwin MG				- Se cambia el calculo de días de estancia desde la fecha y hora de ingreso al servicio a la fecha y hora actual, antes
 *									  era de la fecha de ingreso al servicio hasta la fecha y hora actual
 * 2020-07-01: Edwin MG				- Se corrige la función consultarUsuarioTomaMuestra, para que muestre el nombre de quién toma del personal de laboratorio
 *									  esto debido a que cuando era laboratorio no se estaba mostrando quién toma la muestra
 * 2020-06-30: Edwin MG				- Se muestra la fecha y hora de toma de muestra y se agrega la columna del número de orden
 * 2020-06-19: Edwin MG				- Se muestra la justificación recibida en laboratorio cuando es anulado un estudio desde laboratorio por interoperabilidad
 * 2020-05-21: Edwin MG				- Se permite toma de muestra por cco según interoperabilidad (movhos 11 campo Ccotio)
 * 2020-05-20: Jessica Madrid Mejía - Se valida si en la tabla movhos_000282 el centro de costos tiene 
 *									  restricción y de ser así si el usuario esta habilitado para acceder 
 *									  a los pacientes de dicho centro de costos.
 * 2020-05-15: Edwin MG - Se cambia el nombre de del boton Tomar muestras por Grabar
 * 2020-05-13: Edwin MG - Se corrige la impresion de stickers para el proceso de POCT de UCI
 * 2020-05-12: Edwin MG - Se hacen modfiicaciones varias:
 *							* Se muestra hora de la orden
 *							* Se muestra código cups
 *							* Las enfermeras pueden tomar muestra
 * 2020-02-14: Edwin MG - Si un tipo de orden ya tiene interoperabilidad y es ofertado no deja mover estados por parte de enfermería
 * 2020-02-13: Edwin MG - Si una orden de estudios (laboratorio, radiología, etc) tiene interoperabilidad no se puede cambiar el estado de la orden
 *						- Se corrige la validación del tipo de orden para que las enfermeras no puedan modificar el estado
 * 2019-10-10: Edwin MG - En gestión de enfermería de ayuda dx, no se valida cuando el paciente es recibido en piso para mostrar medicamentos
 * 2019-09-19: Camilo Zapata - Modificaciones en los links que dirigen al programa de dietas, para que manden la zona seleccionada desde gestión de enfermería, al
 *                             programa de dietas, con el propósito de filtrar eficientemente en el último, facilitando la gestión del funcionario.
 * Agosto 06 de 2019: Edwin MG - En ayudas dx, se valida que solo los medicamentos que NO son a necesidad deben estar aplicados o justificados antes de pasarlos a piso
 * Julio 31 de 2019: Edwin MG - Se corrige anulación de una aplicación en cco de ayuda dx, no se enviaba correctamente el cco
 * Julio 29 de 2019: Edwin MG - Se mueve funcion esArticuloDeAyudaDiagnosticaPorCco al script movhos.inc.php
 *							  - Se hacen cambios varios para que los articulos ordenados en ayudas dx puedan ser aplicados desde este programa igual cómo lo hace urgencias
 *							  - Se corrige validacion de mensaje Tiene medicamentos por aplicar o justificar
 * Julio 9 de 2019: Edwin MG - Se valida mensaje Hay medicamentos sin aplicar, solo sale este mensaje cuando el parametro ccoasc (de movhos 11) este desactivadao
 * Julio 8 de 2019: Edwin MG - Si hay saldos en pacientes en cco de ayudas dx no se deja trasladar el paciente a piso o dar alta según el caso.
 *							 - Para pacientes que se encuentran en ayudas dx se puede descartar los saldos correspondientes y las devoluciones se debe hacer por el
 *								programa de cargos de PDA(cargoscpx.php)
 *							 - Se pueden aplicar medicamentos cargados desde ayudas dx
 * Febrero 28 de 2019: Arleyda I.C. -Migración realizada.
 * Agosto 30 de 2018: Jonatan - Se filtra el acceso a las dietas desde urgencias, ayuda y cirugia para que solo muestre el paciente que seleccionan.
 * Agosto 23 de 2018: Jonatan - Se agrega el campo de historia en el insert de la cencam_000003.
 * Julio 5 de 2018: Jonatan - Se muestran los pacientes que tiene marcada muerte y que ademas no tienen saldo en insumos, esto permite darles alta definitiva cumpliendo con las validaciones.
 * Mayo 29 de 2018: Jonatan	- Se corrige el centro de costos asociado a las Dietas, para que en los centros de costos de ayuda si muestre los pacientes de ese cco.
 * Mayo 7 de 2018: Edwin 	 - No se muestran los articulos MMQ a excepción de urgencias
 * Abril 09 de 2018: Edwin 	 - Se cambia el query de consulta de medicamentos
 * Marzo 16 de 2018: Jonatan - Se agrega la columna Traslado ayuda para el centro de costos de Urgencias.
 * Febrero 16 de 2018: Edwin - Se corrige query para mostrar los articulos recien ordenados por el médico
 * Febrero 15 de 2018: Edwin - Los articulos de hemodinamia no se ven para piso
 * Febrero 5 de 2018: Edwin - A la url que abre el programa de ordenes, se le agrega parametro para identificar si se abre desde gestión de enfermería de un piso o de una ayuda dx
 * Diciembre 7 de 2017: Jonatan - Se muestra un tooltip con la cantidad de procedimientos nuevos y modificados, cuando la enfermera(o) ingrese al listado puede ver cual es nuevo o modificado
									y al cerrar la modal quedaran leidos.
 * Noviembre 7 de 2017: Jonatan - Se agrega la funcion cancelarPedidoInsumos para que al momento de dar alta definitiva al paciente se cancele el pedido de Insumos.
 * Octubre 3 de 2017: Jonatan - Se crean dos arreglos uno que contiene las ubicaciones fisicas y otra las virtuales, luego se valida por zona y si estan ocupadas todas
								las fisicas muestra el listado de las ubicaciones virtuales.
 * Septiembre 12 de 2017: Jonatan - Se elimina la opcion de cubiculo vacio.
 * Septiembre 7 de 2017: Jonatan - Para gestion de enfermeria pantalla pisos se quitan algunas columnas, ademas se envia notificacion de pantalla inactiva.
 * Agosto 10 2017: Jonatan - Se muestra mensaje avisando al usuario que la firma electronica esta vencida.
 * Julio 28 de 2017: Jonatan - Se muestra la ventana modal de saldo en insumos para urgencias.
 * Julio 13 de 2017: Jonatan - Se muestra la contingencia a las auxiliares pero son el cajon rojo parpadeando.
 * Julio 04 de 2017: Jonatan  - Utilizar el campo de terminacion de la consulta y no la fecha y hora de registro en la tabla 18 de movhos.
							  - Solicitar clave en gestion de enfermeria para urgencias cada vez que ingresen al listado de medicamentos.
							  - Se agrega medico tratante.
							  - Permitir traslado de dosis adaptadas desde urgencias al piso.
 * Junio 06 de 2017: Jonatan - Se valida que el paciente no tenga insumos pendientes por aplicar o devolver para realizar la entrega a otro piso, si es en el mismo piso
								no valida estos saldos.
							- Se controlan los estados de las ordenes para que no pueda pasar de pendiente a realizado, y se agrega un nuevo estado llamada realizado nocturno
								el cual solo estara activo en un horario determinado por el campo Eexhor de la tabla movhos_000045.
							- Se agregan los iconos nuevos a las convenciones.
							- Se agrega validacion de procedimiento requiere autorizacion para que se pueden cambiar los estados dependiendo de la tabla cliame_000260.
 * Abril 17 de 2017: Jonatan - Se muestra la presentacion comercial cuando se abre la modal de descarte o devolucion.
 * Abril 11 de 2017: Jonatan - Se agrega ventana modal que muestra los articulos con saldo asociados al paciente y asi puedan saber que deben descartar o devolver.
 * Marzo 21 de 2017: Jonatan - Se agrega una nueva consulta en la funcion validar_unidad() para que tenga en cuenta la unidad de medidad registrada en la tabla movhos_000008.
 * Febrero 13 de 2017: Edwin MG - Al dar click en retornar, se organiza las variables globales waux y wsp para que no se envien al dar submit si no tiene ninguna información
 *								  Esto se hace para que despues de consultar un cco se muestre los encabezados correctamente.
 * Enero 23 de 2017: Jonatan - Felipe - Se agregan validaciones con el parametro wsp para que no muestre algunos elementos y asi pueda ser usado este programa
										en las pantallas de hospitalizacion, ademas felipe repara el regsitro de cambio de habitacion (facturacion)
 * Diciembre 12 de 2016: Jessica Madrid	- Se modifica el nombre de la funcion anular() por anularAplicacionMovhos(), cambio realizado en movhos.inc.php
										  para evitar conflictos con otros scripts.
 * Octubre 13 de 2016: Felipe Alvarez - se generan traslados automaticos desde la evolucion medica de neonatos, esto es que si el medico  en una de sus evoluciones
										registra el paciente en una habitacion Uci, Uce, Incubadora, automaticamente se hace el cambio en los movimientos hospitalarios
										de movhos_000017
 * Octubre 3 de 2016: Jonatan Lopez - Se corrige la consulta de aplicaciones para pacientes que estan en centros de costos de ayuda.
 * Septiembre 29 de 2016: Jonatan Lopez - Se pueden trasladar pacientes de un centro de costos hospitalario a uno de ayuda, en la columna
											traslado ayuda se puede seleccionar el centro de costos donde se quiere ver el paciente.
										- Desde ese centro de costos el paciente sera liberado para que no aparezca o dado de alta definitiva si el paciente
											ingresó por ese centro de costos de ayuda.
 * Septiembre 9 de 2016: Jonatan Lopez - Se mejoran los mensajes tooltip cuando los pacientes son recibidos en el piso. (Proveniente de.)
 * Septiembre 7 de 2016: Jonatan Lopez - Se agrega cancelacion de traslado cuando el paciente esta por recibir.
 * Septiembre 2 de 2016: Jonatan Lopez - Se marcan en azul los pacientes que tienen habitacion asignada.
 * Agosto 30 de 2016: Jonatan Lopez - Se marca en proceso de traslado los pacientes que tienen habitacion asignada en la central de camas y solo para urgencias.
 * Agosto 22 de 2016: Jonatan Lopez	- Se muestra el mensaje de articulos pendientes en las ultimas dos rondas correctamente.
 * Agosto 16 de 2016: Jonatan Lopez	- Se valida que las solicitudes de cama sean maximo de 8 dias antes.
 * Agosto 7 de 2016: Jonatan Lopez	- Se agrega la habitacion del paciente cuando se marca el alta definitiva.
 * Julio 25 de 2016: Jonatan Lopez	-	Se modifica el programa para que permita trasladar pacientes desde urgencias a cirugia, de cirugia a hospitalizacion y
										de piso a piso, se puede dar de alta definitva, alta en proceso y muerte, ademas se puede solicitar habitacion.
 * julio 13 de 2016: Felipe Alvarez Sanchez - Se agrega este parametro para limitar  las habitaciones en el select, por las que traiga este parametro y sea mas facil para el usuario
 * Mayo 25  de 2016: Jonatan				- Se permite el acceso a los pacientes de cirugia en las ordenes, solo mostrará los pacientes que tengan turno de cirugia para el dia anterior y el actual
											  en la tabla tcx_000011, sean hospitalizados o ambulatorios, ademas en esta publicacion se encuentran los cambios de Jessica y Edwin del dia 11 de mayo.
 * Mayo 11 de 2016: Jessica Madrid	En la modal de Examenes y procedimientos pendientes:
 *									- Se agrega la cantidad.
 *									- Se muestran las justificaciones en un textarea.
 *									- Si es urgencias y los procedimientos tienen medicamento relacionados y no estas suspendidos permite aplicarlos
 *										para las ultimas 24 horas y si por lo menos uno de los medicamentos asociados a un procedimiento tinen minimo
 *										una aplicación no podrá cancelar el procedimiento.
 *									- Al cambiar el estado de un procedimiento a realizado, pendiente de resultado o cancelado se suspenden los medicamentos
 * 										asociados en caso de tenerlos.
									- Los medicamentos de dosis unica seguiran saliendo hasta que sean aplicados (Edwin).
 * Mayo 6 de 2016 Jonatan Lopez: Se ordenan los pacientes por ubicacion, por peticion de carmenza ya no se ordenara por tiempo de atencion.
 * Mayo 5 de 2016 Jonatan Lopez: Se agrega el tiempo transcurrido desde el ingreso del paciente hasta el momento actual del sistema, en la columna del semáforo.
 * Mayo 3 de 2016 Jonatan Lopez: Se agrega la columna semaforo cuando el centro de costos de es de urgencias y se valida si el paciente lleva mas de dos horas de atencion
								si es asi, se pinta de color amarillo, si lleva mas de 6 horas se pinta de color rojo.
 * Abril 12 de 2016 Arleyda Insignares - Mostrar Tooltip con codigo y nombre de la entidad cuando el usuario se posicione con el mouse en la columna nombre buscar ingres o ingnre en la 									 tabla movhos_000016
 * Marzo 22 de 2016 Felipe Alvarez  - reasignarCubiculo Se le agregra una validacion mas  para que tenga encuenta si hay movimientos
 * en la movhos_000018 con el fin de asegurar la integridad de los datos
 * Marzo 09 de 2016:   Edwin	- Si es urgencias, el medicamento se considera suspendido sin mirar las 4 horas antes para aplicar
 * Febrero 02 de 2016: Edwin	- Se muestran los cco de costos con ccoior(inicio de ordenes)
 * octubre 20 de 2015: Edwin	- Cuando se pasa de un día a otro en caso de mostrarse el día cual, la fecha cambia automaticamente
 * Sept 4 de 2015: Jonatan	- En el tooltip de pendientes, ya no se muestran los genericos.
 * Julio 2 de 2015 Jonatan	- Se agrega la funcion estados_por_rol_enf para inactivar los estados que no tenga relacionados la enfermera en el campo Eexenf.
 * Junio 25 de 2015: - Se muestran los estados de los medicamentos en el tooltip donde se muestra la cantidad de pendientes.
 * Junio 1 de 2015: - Se controla el acceso a las ordenes por medio de sesion, no permitiendo que puedan ingresar a varias ordenes al mismo tiempo, se reemplaza
						la modal hecha el 26 de mayo. Jonatan
 * Mayo 26 de 2015: - Se abren las ordenes en una ventana modal para evitar que abran las ordenes del paciente o de cualquier otro en la misma ventana. Jonatan
 * Mayo 22 de 2015: - Al ingregar a las ordenes del paciente se bloqueara la pantalla y pasados 3 segundos se desbloquea. Jonatan
 * Mayo 21 de 2015: - Se quita el filtro de articulo suspendido para que lo cuente en las notificaciones. Jonatan
 * Abril 6 de 2015: - Se agrega checkbox para que la enfermera pueda dar de alta al paciente, solo para urgencias. Jonatan
 * Marzo 31 de 2015: - En la función estaAplicadoCcoPorRonda se corrige los parametros de la función strtotime
 * Marzo 19 de 2015: - Se muestran los pacientes que han sido dados de alta desde la sala de espera por parte del paciente. Jonatan
 * Enero 28 de 2015: - Se valida que solo verifique la aplicacion de medicamentos si el centro de costos es de urgencias. Jonatan
 * Enero 26 de 2015: - Se agregan los medicamentos que se encuentran pendiente por revisar por parte de la enfermera jefe (rol=027)
 * Enero 22 de 2015: - Se muestran los medicamentos nuevos y los mensajes del chat pendientes de leer como un tooltip.
 * Enero 20 de 2015: 	-Se controla la variable $waux cuando se retorna al listado de centros de costos, para que esta se mantenga en caso de tener datos.
 * Diciembre 30 de 2014: - Se evalua si el paciente tiene alta definitiva antes de asignarle ubicacion, en caso de tenerla no se debe asignar. (Jonatan)
 * Diciembre 17 de 2014: - Se agrega fecha de modificacion de la orden medica para controlar el listado y que solo salgan los que tengan menos de 24 horas de modificacion.
 * Diciembre 4 de 2014: - Se corrige la busqueda en el array de cubiculos, para que el key del array sea historia
							e ingreso juntos y haga la busqueda correctamente.
 * Diciembre 3 de 2014: - Se muestran los pacientes que no tienen ubicacion en los filtros de las zonas, ademas se mostrara si esta en consulta o en la sala de espera si no tiene conducta.
 * Diciembre 2 de 2014: - Se agrega la conducta asociada al paciente en la sala de espera de los medicos, ademas de la via de administracion de los medicamentos.
 * Diciembre 1 de 2014: - Se libera al paciente de cualquier otro cubiculo que este ocupando cuando se hace reasignacion de ubicacion. (Jonatan)
 * Noviembre 28 de 2014: - Se agrega modificacion de sala y cubiculo para los pacientes. (Jonatan)
 * Noviembre 12 de 2014: - Se agrega filtro de zonas si el centro de costos las maneja. (Jonatan)
 * Noviembre 05 de 2014: - Ya no se mostraran los pendientes por leer si el paciente tiene ordenes. (Jonatan)
 * Octubre 31 de 2014:  - No se cuentas los medicamentos que estan suspendidos en los pendientes por leer. (Jonatan)
 * Octubre 29 de 2014:  - Se agrega cambio de estado en los procedimientos y examenes del paciente. (Jonatan)
						- Se agrega aplicacion, anulacion o justificacion de los medicamentos. (Edwin)
 * Octubre 22 de 2014: Jonatan Lopez
						- Se muestra con color verde los pacientes que tienen ordenes realizadas.
						- Se valida que el paciente tenga kardex en el dia actual o anterior, si es asi mostrara la informacion de aplicacione para el paciente.
 * Octubre 20 de 2014: Jonatan Lopez
						- Se comenta el mensaje de los pendientes en informacion demografica y medidas generales.
 * Octubre 16 de 2014: Jonatan Lopez
						- Se agrega acceso de solo consulta a la auxiliar de enfermeria, si el paciente tiene ordenes, ira a las ordenes de consulta.
 * Septiembre 08 de 2014:	Jonatan Lopez
						- Se valida si el centro de costos que ingresa es de urgencias para mostrar todos lo pacientes de urgencias
						- Se muestran las notificaciones de los pendientes de leer de ordenes.
						- Se valida si el centro de costos debe ir a ordenes o a kardex, segun la aplicacion ir_a_ordenes de la tabla root_000051.
 * Mayo.06.2014 		Felipe Alvarez Sanchez
						Se agrega parametro de traslados automaticos , esto con el fin de que en uci neonatal puedan hacer un cambio de tipo de habitacion
 * Diciembre 26 de 2013	Edwin MG 	 Se corrige la url que abre el kardex
 * Nov. 27 de 2013:     Jonatan Lopez Se reemplaza el uso del tag <blink> por una funcion jquery que cumple con la misma funcion, esto para que las alertas del chat
									 parpadeen el todos los navegadores.
 * Mayo 28 de 2013      Juan C. Hdez Se crea un parametro de consulta, para crear una nueva opción en Matrix con este parametro con acceso a las auxiliares
                                     de enfermería, las cuales no pueden acceder a las opciones de las enfermeras(os). paramentro aux='on'
 * Mayo 22 de 2013		Edwin MG.	Se módifica el programa para que tenga en cuenta el cambio de condición a necesidad a una condición que no lo sea.
 * Abril 1 2013         Juan C. Hdez Se adiciona un link para las consultar las disponibilidades de las especialidades
 * Febrero 11 2013      Juan C. Hdez Se adiciona un link para las consultas de cirugias programadas en el programa de turnos de cirugia
 * Enero 29 de 2013		Edwin MG.	Se agrega función para consultar las rondas para el día aunque el kardex este sin confirmar.
 * Junio 27 de 2012     Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos
 * 									de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera *                                  funcion.
 * Mayo 11 de 2012		Edwin MG.	Se corrige el titulo del minireporte, oculto en la celdas con alguna imagen.
 * Mayo 10 de 2012		Edwin MG.	Se cambia la funcion consultarJustificacion para que detecte la hora de aplicacion como {Horamilitar} - {AM|PM}
 * Febrero 28 de 2012	Edwin MG.	Se toma en cuenta los cambios de frecuencia para el medicamento, esto para evitar que salgan x rojas por los cambios de frecuencia.
 * Enero 07 de 2012		Edwin MG.	Para tomar los medicamentos sin confirmar, se basa en el día que se coja como base, es decir, si el kardex esta sin confirmar
 *									se basa en el día anterior, por tanto parar determinar los medicamentos sin confirmar se basa en el día anterior, si tiene
 *									kardex y esta confirmado se basa en el día actual.
 * Enero 06 de 2012		Edwin MG.	Si un articulo reemplaza a otro no se muestra las rondas anteriores
 *									Si un articulo generico esta sin confimar se muestra
 * Enero 31 de 2012		Edwin MG.	Se coloca el mensaje de kardex sin generar y kardex sin confirmar bajo, las imagenes, y no se quita el enlace al kardex
 * Enero 24 de 2012.	Edwin MG.	Se muestra una x amarillo si el medicamento es a necesisdad o tiene justificacion, o los medicamentos son todos a
 * 									necesidad o tienen justificacion y no se aplico nada
 * Enero 10 de 2012. 	Santiago.	se agregó un campo para ver la justificación por que el medicamento no fue aplicado a la ronda correspondiente
 **********************************************************************************************************************************************************/

//Contiene la información básica de una habitación
class habitacion{

	var $codigo;
	var $cco;
	var $historia;
	var $ingreso;
	var $nombre;
	var $horas;		//horas de aplicación,
	var $ccoNombre;
	var $enProcesoTraslado;
	var $altaEnProceso;
	var $fechaHoraAltaEnProceso;	//Fecha y hora de alta en proceso, solo la ronda, en formato Unix(segundos desde 1970-01-01 a las 00:00:00

	var $esTrasladoUrgencia;		//Indica si fue trasladado
	var $fechaHoraTraslado;			//Indica tiene y fecha de traslado

	var $kardexConfirmado;			//Array con indice de fecha. Indica si el kardex para el día esta confirmado
	var $tieneKardex;

	var $artSinConfirmar = "";

	var $totalArticulos;
	var $articulosNecesidadSinRonda;	//cuenta el total de articulos a necesidad sin ronda

	var $consultoArticulosSinConfirmar;

	var $documento;
	var $tid;

	var $numMedicamentos;
	var $codigoEntidad;
	var $nombreEntidad;
	var $altaPorDosisUnica = true;
	var $dejarDataAltaPorDosisUnica = true;
	var $tieneMedicamentosSinAplicarOJusitificarAyudaDxCx = false;
	var $dejarAplicarAyudadxCx = false;

	function consultarViasAdministracion(){

		global $wbasedato;
		global $conex;
		$coleccion = array();

		$q = "SELECT Viacod, Viades
			    FROM ".$wbasedato."_000040";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		while($info = mysql_fetch_assoc($res))
		{
			if(!array_key_exists($info['Viacod'],$coleccion)){

				$coleccion[$info['Viacod']] = $info;
			}

		}

		return $coleccion;
	}

	//constructor de clase
	function __construct( $cco, $ccoNombre, $hab, $historia, $ingreso, $nombre, $enTraslado, $altaProceso, $fechaAltaProceso, $horaAltaProceso, $pacced, $pactid, $codigoEnti, $nombreEnti ){

		global $wbasedato;
		global $conex;

		global $hora;
		global $tiempoAMostrar;
		global $fechaFinal;
		global $ccoayuda;

		$this->numMedicamentos = Array();;

		$fechaFinal++;

		$this->codigo = $hab;
		$this->cco = $cco;
		$this->ccoNombre = $ccoNombre;
		$this->historia = $historia;
		$this->ingreso = $ingreso;
		$this->nombre = $nombre;
		$this->documento = $pacced;
		$this->tid = $pactid;
		$this->codigoEntidad = $codigoEnti;
		$this->nombreEntidad = $nombreEnti;


		$this->enProcesoTraslado = ( $enTraslado == 'on' )? true: false;
		$this->altaEnProceso  = ( $altaProceso == 'on' )? true: false;

		$this->consultoArticulosSinConfirmar = false;

		$this->dejarAplicarAyudadxCx = false;

		/*********************************************************
		 * Solo para ayudas dx o cirugía
		 *********************************************************/
		$sql = "SELECT Ccoadu, Ccoasc
				  FROM ".$wbasedato."_000018 a, ".$wbasedato."_000011 b
				 WHERE ubihis = '".$historia."'
				   AND ubiing = '".$ingreso."'
				   AND ( ( ubisac = ccocod AND ccocir = 'on' )
				    OR ( ubisac = ccocod AND ccoayu = 'on' )
				    OR ( ubiste = ccocod AND ccoayu = 'on' ) )";

		$resHal = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		if( $rowhal = mysql_fetch_array($resHal) ){
			if( $rowhal['Ccoadu'] == 'on' && $rowhal['Ccoasc'] != 'on' ){
				$this->dejarDataAltaPorDosisUnica = true;
			}
			else{
				$this->dejarDataAltaPorDosisUnica = false;
			}

			if( $rowhal['Ccoasc'] != 'on' ){
				$this->dejarAplicarAyudadxCx = true;
			}
			else{
				$this->dejarDataAltaPorDosisUnica = true;
			}
		}
		/*********************************************************/

		if( $this->altaEnProceso ){
			//ronda en que se hizo el proceso de alta definitiva
			$this->fechaHoraAltaEnProceso = strtotime( $fechaAltaProceso." ".$horaAltaProceso )-strtotime( "1970-01-01 00:00:00" );
			$this->fechaHoraAltaEnProceso = intval( $this->fechaHoraAltaEnProceso/(2*3600) )*2*3600+strtotime( "1970-01-01 00:00:00" );
		}
		else{
			$this->fechaHoraAltaEnProceso = 0;
		}

		$pacConOrdenes = buscar_paciente_ordenes($conex, $wbasedato, $historia, $ingreso) == 'on' ? true : false;
		$ccoConOrdenes = ir_a_ordenes( "", $cco ) == 'on' ? true: false;

		//creo el array con objeto vacio hora
		for( $i = date( "G", $fechaFinal-$tiempoAMostrar*3600 ); $i < date( "G", $fechaFinal-$tiempoAMostrar*3600 )+$tiempoAMostrar; $i += 2 ){

			$this->horas[ gmdate( "G", $i*3600 ) ] = new hora();

			//Busco si la ronda se puede aplicar
			$puedeAplicar = false;
			if( $pacConOrdenes && $ccoConOrdenes && ( esUrgencias( $conex, $cco ) || ( ( esCirugia( $conex, $cco ) || !empty( $ccoayuda ) ) && $this->dejarAplicarAyudadxCx ) ) ){
				// if( $i >= date( "G", $fechaFinal-$tiempoAMostrar*3600 )+$tiempoAMostrar - 4 ){

					$puedeAplicar = true;
					// echo "<br>$i - ".gmdate( "Y-m-d H:i:s", $i*3600 ); echo "<br>";
					// $puedeAplicar = estaAplicadoCcoPorRonda( trim($cco), date( "Y-m-d", strtotime( date( "Y-m-d 00:00:00" ) )-24*3600 + $i*3600 ), $i-2, &$habitacionesFaltantes, '*' );
				// }
			}

			$this->horas[ gmdate( "G", $i*3600 ) ]->puedeAplicar = $puedeAplicar;
			$this->numMedicamentos[ gmdate( "G", $i*3600 ) ] = 0;
		}

		$fechaFinal--;

		$this->tieneMedicamentosSinAplicarOJusitificarAyudaDxCx = false;
	}

	/********************************************************************************
	 * Consulta Justificación Medicamento
	 ********************************************************************************/
	function consultarJustificacion( $ronda, $codigoArticulo, $idOriginal, &$codJus ){

	    global $wbasedato;
	    global $fecha;
        global $conex;

		$codJus = "";

	    $sql = "SELECT Jusjus  "
              ."  From ".$wbasedato."_000113 "
			  ." where Jushis = '".$this->historia ."' and "
              ."  Jusing = '".$this->ingreso ."' and ";

		$sql .= "  Jusron ='".gmdate("H:00 - A",$ronda*3600)."' and ";

        $sql .="  Jusart = '".$codigoArticulo."' and ";
		$sql .="  Jusfec	 = '".$fecha."' and ";
        $sql .="  Jusido	 = '".$idOriginal."'";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

    	if( $row = mysql_fetch_array($res) ){
			list( $codJus,$Jusjus ) = explode( "-", $row['Jusjus'] );
		}

		return $Jusjus;
	}

	/********************************************************************************
	 * Agrega un medicamento a una hora
	 *
	 * $ronda						Ronda, de 0 - 22, solo pares
	 * $codigoArticulo				Codigo del articulo
	 * $nombre						Nombre del medicamento
	 * $cantidadCargada				Cantidad a aplicar al paciente
	 * $unidad						Unidad de fraccion a aplicar al paciente
	 * $suspendido					Indica si esta suspendido o no
	 * $aNecesidad					Condicion de aplicacion
	 * $sinConfirmarPreparacion		Indica si el articulo es sin confirmar preparaciones, tipo bool
	 * $fechaInicio					Fecha y hora de inicio en formato unix (segundos)
	 * $frecuencia					Frecuencia en horas
	 * $fechaBase					Fecha en que se esta basando el kardex
	 * $dosisMaximas				Total de dosis maximas
	 * $diasTrtatamiento			Total de dias de tratamiento
	 ********************************************************************************/
	function procesarMedicamento( $ronda, $codigoArticulo, $nombre, $cantidadCargada, $unidad, $suspendido, $aNecesidad, $sinConfirmarPreparacion, $fechaInicio, $frecuencia, $fechaBase, $dosisMaximas, $diasTratatamiento, $filaArticulos, $ccoCodigo ){

		global $conex;
		global $wbasedato;
		global $aplicaciones;			//Array con todas la aplicaciones realizadas
		global $fecha;					//Fecha de la ronda
		global $informacionArticulos;	//Array con la información basica de los articulos
		global $ccoayuda;				//Array con la información basica de los articulos

		global $wemp_pmla;
		$ccoDispensaInsumos = consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );
		$ccoDispensaInsumos = explode( ",", $ccoDispensaInsumos );
		$dispensaInsumos 	= ( in_array( trim( $ccoCodigo ), $ccoDispensaInsumos ) || in_array( '*', $ccoDispensaInsumos ) ) ? true : false;

		$idOriginal = $filaArticulos[ 'Kadido' ];

		$this->esTrasladoUrgencia = consultarUltimoTraslado( $conex, $wbasedato, $this->historia, $this->ingreso, $this->fechaHoraTraslado );

		//2019-10-10. Si es un cco de ayuda dx no se valida el traslado de pacientes desde urgencias
		if( empty($ccoayuda) ){
			if( $this->esTrasladoUrgencia ){
				if( $this->fechaHoraTraslado >= strtotime( $fecha." ".$ronda.":00:00" ) ){
					return;
				}
			}
		}

		$via = $this->consultarViasAdministracion(); //Array que contiene las vias de administracion.

		$dosisLevIC = consultarDosisAAplicarLevInc( $conex, $wbasedato, $codigoArticulo, $idOriginal, $this->historia, $this->ingreso );
		$cantidadCargadaOriginalLevIC = $cantidadCargada;
		if( $dosisLevIC !== false ){
			$cantidadCargada = $dosisLevIC;
		}

		$info = Array();
		$info[ 'codigo' ] = $codigoArticulo;
		$info[ 'nombre' ] = $nombre;
		$info[ 'cantidadAAplicar' ] = $cantidadCargada;
		$info[ 'unidad' ] = $unidad;
		$info[ 'cantidadAplicada' ] = @$aplicaciones[ $this->historia."-".$this->ingreso ][ strtoupper( $codigoArticulo ) ][ $idOriginal ][$ronda];
		$info[ 'suspendido' ] = ( $suspendido == 'on' )? "S&iacute;" : "No";
		$info[ 'fechaHoraIncio' ] = $fechaInicio;
		$info[ 'frecuencia' ] = $frecuencia*3600;
		$info[ 'cantDosisAnterior' ] = $filaArticulos[ 'Kaddan' ];
		$info[ 'fechaUltimaModificacion' ] = $filaArticulos[ 'Kadfum' ];
		$info[ 'horaUltimaModificacion' ] = $filaArticulos[ 'Kadhum' ];
		$info[ 'cantidadFraccion' ] = $filaArticulos[ 'Kadcma' ];
		$info[ 'noEnviar' ] = $filaArticulos[ 'Kadess' ];
		$info[ 'via' ] = $via[$filaArticulos[ 'Kadvia' ]]['Viades'];
		$info[ 'codVia' ] = $via[$filaArticulos[ 'Kadvia' ]]['Viacod'];
		$info[ 'observacion' ] = strip_tags( substr( $filaArticulos[ 'Kadobs' ], 0, strpos($filaArticulos[ 'Kadobs' ], "<span" ) ) );
		$info[ 'esStock' ] = esStock( $conex, $wbasedato, $codigoArticulo, $this->cco ) ? "on": "off";
		$info[ 'puedeAplicar' ] = $filaArticulos['puedeAplicar'];

		$esUrgencias = esUrgencias($conex, $ccoCodigo);

		if( $esUrgencias ){
			$info[ 'puedeAplicar' ] = true;
		}

		$esMmq = esMMQInc( $conex, $wbasedato, $codigoArticulo );
		if( !$esUrgencias && $dispensaInsumos && $esMmq )
			return;

		$dispensable = esDispensableInc( $conex, $wbasedato, $codigoArticulo, $idOriginal, $this->historia, $this->ingreso );
		//Si es un medicamento que no es dispensable y está cómo no enviar no se muestra
		if( ( $info[ 'noEnviar' ] == 'on' && $dispensable == 'on' ) || ( esArticuloGenerico( $codigoArticulo ) && esArticuloGenericoLevIC( $conex, $wbasedato, $codigoArticulo, $idOriginal, $this->historia, $this->ingreso ) ) ){
			return;
		}

		if($esUrgencias || !empty( $ccoayuda ) ){
			$info[ 'saldo' ] = validar_aplicacion( $conex, $wbasedato, $this->historia, $this->ingreso, $codigoArticulo, $this->cco, $cantidadCargada, $wcant_aplicar, $filaArticulos[ 'Kadcma' ], $info[ 'esStock' ], $info[ 'noEnviar' ], $saldo, $saldoSinRecibir, $wmensaje );;
		}else{
			$info[ 'saldo' ] = "";
		}
		$info[ 'msgSaldo' ] = $wmensaje;
		$info[ 'idOriginal' ] = $idOriginal;

		$info[ 'dosisVariable' ] = consultarDosisVariable( $conex, $wbasedato, $codigoArticulo, '1050', $this->cco );

		$info[ 'indexMed' ] = $this->numMedicamentos[ $ronda ];

		//Busco si este articulo reemplazo otro
		$fechaHoraReemplazo = fechaHoraReemplazoArticuloNuevo( $this->historia, $this->ingreso, $fechaBase, $codigoArticulo, $artAnterior );

		//Si el articulo reemplazo a otro antes de la ronda, no se muestra este articulo
		//																	 	   *       Este calculo da la ronda en que fue reemplazado       *
		if( !empty( $fechaHoraReemplazo ) && strtotime( "$fecha $ronda:00:00" ) <= ceil( strtotime( $fechaHoraReemplazo )/(2*3600) )*2*3600-2*3600 ){

			// if( !empty( $aplicaciones[ $this->historia."-".$this->ingreso ][ $artAnterior ][ $idOriginal ][ $ronda ] ) ){
			if( existeAplicacionInc( $aplicaciones, $this->historia, $this->ingreso, $artAnterior, $ronda ) ){
				return;
			}
		}

		if( $info[ 'suspendido' ] != "No" ){

			if( !$esUrgencias ){
				//Busco si el articulo fue reemplazado desde el perfil
				$reemplazado = fueReemplazado( $this->historia, $this->ingreso, $fechaBase, $codigoArticulo );

				if( !$reemplazado ){	//Si no fue reemplazado

					$estaSuspendido = buscarSiEstaSuspendidoInc( $this->historia, $this->ingreso, $codigoArticulo, $ronda, $fecha, $idOriginal );

					if( $estaSuspendido != 'on' ){
						$info[ 'suspendido' ] = "No";
					}
					else{
						return;
					}
				}
				else{	//Si fue reemplazado
					return;
				}
			}
			else{
				return;
			}
		}

		//verifico si tiene dosis máxima
		//Si tienes dosis máxima verifico que pertenezca a la ronda
		$dosisMaximas = trim( $dosisMaximas );
		if( !empty( $dosisMaximas ) ){

			$fueAplicadoEfectiva = false;
			if( $dosisLevIC ){

				$totalAplicaciones = consultarTotalAplicacionesEfectivasEnDosisInc( $conex, $wbasedato, $this->historia, $this->ingreso, $codigoArticulo, $info[ 'fechaHoraIncio' ], strtotime( "$fecha $ronda:00:00" )-2*3600, $idOriginal );

				if( $totalAplicaciones >= $cantidadCargadaOriginalLevIC ){
					$fueAplicadoEfectiva = true;
				}
			}
			else{
				$totalAplicaciones = consultarTotalAplicacionesEfectivasInc( $conex, $wbasedato, $this->historia, $this->ingreso, $codigoArticulo, $info[ 'fechaHoraIncio' ], strtotime( "$fecha $ronda:00:00" )-2*3600, $idOriginal );

				if( $totalAplicaciones >= $dosisMaximas ){
					$fueAplicadoEfectiva = true;
				}
			}

			//Si es dosis única y la aplicación existe, se permite dar alta definitiva o alta para traslado para ayudas dx o cx
			if( !$this->dejarDataAltaPorDosisUnica && $suspendido != 'on' ){

				if( $dosisMaximas == 1 )
				{
					if( !isset( $aplicaciones[ $this->historia."-".$this->ingreso ][ strtoupper( $codigoArticulo ) ][ $idOriginal ] ) ){
						$this->altaPorDosisUnica = false;
					}
				}
			}

			if( $fueAplicadoEfectiva ){
				return;
			}

			// if( strtotime( "$fecha $ronda:00:00" ) > $info[ 'fechaHoraIncio' ]+( $dosisMaximas-1 )*$info[ 'frecuencia' ] ){
				// return;
			// }
		}

		//Si tiene dias de tratamiento
		//Si tiene dias de trtamiento, miro que la ronda no supere los dias de tratamiento
		$diasTratatamiento = trim( $diasTratatamiento );
		if( !empty( $diasTratatamiento ) ){
			if( strtotime( "$fecha 23:59:59" ) > strtotime( date( "Y-m-d 23:59:59", $info[ 'fechaHoraIncio' ]+( $diasTratatamiento-1 )*24*3600 ) ) ){
				return;
			}
		}

		$expCondicion = explode( " - ", estaANecesidad( $aNecesidad ) );

		$esANecesidad = ( $expCondicion[1] == "AN" )? true : false;

		//Si el medicamento fue cambiado de una condición a necesidad a una que no lo es
		//reviso desde cuando fue cambiado, si fue cambiado antes o igual a la ronda actual
		//Salgo de la función por que no se tiene en cuenta el medicamento
		if( !$esANecesidad && $filaArticulos[ 'Kadfcn' ] != "0000-00-00" ){

			//Si la ronda actual es menor o igual a la hora de cambio, el medicamento n se tiene en cuenta
			if( strtotime( "$fecha $ronda:00:00" ) <= strtotime( $filaArticulos[ 'Kadfcn' ]." ".$filaArticulos[ 'Kadhcn' ] ) ){
				$expCondicion = explode( " - ", estaANecesidad( $filaArticulos[ 'Kaducn' ] ) );
				// return;
			}
		}

		//Verifico si la cantidad de dosis cambio
		if( !empty( $info[ 'cantDosisAnterior' ] ) ){

			if( $info[ 'cantDosisAnterior' ] <= $info[ 'cantidadAAplicar' ] ){

				if( !empty( $info[ 'cantidadAplicada' ] ) && $info[ 'cantidadAplicada' ] >= $info[ 'cantDosisAnterior' ] ){

					//Calculo la ronda de modificación en formato unix
					$rondaModificacionUnix = intval( ( strtotime( $info[ 'fechaUltimaModificacion' ]." ".$info[ 'horaUltimaModificacion' ] ) - strtotime( "1970-01-01 00:00:00" ) )/(2*3600) )*2*3600 + strtotime( "1970-01-01 00:00:00" );

					//Calculo el tiempo de hora de confirmacion del kardex
					$fcConfirmacionKardex = strtotime( $fechaBase." ".$filaArticulos['Karhco'] );

					//Si la fehca de modificaciones es m
					if( $rondaModificacionUnix >= strtotime( "$fecha $ronda:00:00" ) || $fcConfirmacionKardex >= strtotime( "$fecha $ronda:00:00" ) ){
						$info[ 'cantidadAAplicar' ] = $info[ 'cantDosisAnterior' ];
					}
				}
			}
		}

		//Averiguo si el articulo pertenece a la ronda, esto para saber si es a necesidad mostrarlo
		if( ( strtotime( "$fecha $ronda:00:00" ) - $fechaInicio )%$info[ 'frecuencia' ] == 0 ){
			$info[ 'perteneceRonda' ] = true;
		}
		else{
			$info[ 'perteneceRonda' ] = false;
		}



		@$info[ 'aNecesidad' ] = ( $expCondicion[1] == "AN" )? "S&iacute;" : "No"; //( esANecesidad( $aNecesidad ) )? "S&iacute;" : "No";
		$info[ 'condicion' ] = $expCondicion[0];

		//Si es de traslado o tiene alta se considera con justificacion
		//esto para que salga con x amarilla
		//																								*                hora par anterior                    *
		if( $this->enProcesoTraslado && !esUrgencias( $conex, $this->cco ) ) { //|| ( $this->altaEnProceso && strtotime( "$fecha $ronda:00:00" ) >= intval( $this->fechaHoraAltaEnProceso/(2*3600) )*2*3600 ) ){
			//if( $this->enProcesoTraslado ){
				$Jusjus = "En proceso de traslado";
			//}
			//else{
			//	$Jusjus = "Alta en proceso";
			//}
		}
		else{
			$Jusjus = $this->consultarJustificacion( $ronda, $codigoArticulo, $idOriginal, $codJus );
			$info['codJus'] = $codJus;
		}


		$info['Jusjus'] = trim( $Jusjus );

		$this->horas[ $ronda ]->agregarMedicamento( $codigoArticulo."-".$idOriginal, $info, $sinConfirmarPreparacion );
		@$this->totalArticulos[ $fecha ]++;	//cuento el total de articulos que hay para el paciente
		@$this->totalArticulos[ 'total' ]++;

		if( !$info[ 'perteneceRonda' ] ){
			@$this->articulosNecesidadSinRonda[ $fecha ]++;
			@$this->articulosNecesidadSinRonda[ 'total' ]++;
		}

		if( !empty($ccoayuda) && $info[ 'puedeAplicar' ] && !$this->tieneMedicamentosSinAplicarOJusitificarAyudaDxCx ){
			if( !$esANecesidad ){
				$this->tieneMedicamentosSinAplicarOJusitificarAyudaDxCx = empty( $info[ 'cantidadAplicada' ] ) && empty( $info['Jusjus'] );
			}
		}
	}
};

class hora{

	var $medicamentos = Array();	//lista de medicamentos, solo el codigo
	var $color = 0;			//Solo el numero, ya hay array global que indica el color
	var $descripcion = '';	//Si es total, parcial o no aplicada

	var $totalAplicados = 0;
	var $totalMedicamentos = 0;
	var $totalMedicamentosNecesidad = 0;	//Indica cuantos medicamentos a Necesidad hay
	var $totalMedicamentosJustificados = 0;	//Indica cuantos medicamentos a Necesidad hay
	var $totalMedicamentosObligatorios = 0;
	var $totalMedicamentosObligatoriosAplicados = 0;

	var $esKardexConfirmado;
	var $tieneKardex;

	var $tieneArticulosSinConfirmar = false;

	var $agregadoMedSinConfirmar = false;

	var $totalMedicamentosNecesidadPertenecientesRonda = 0;

	var $puedeAplicar;

	/********************************************************************************
	 * Agrega la información pertinente para un articulo
	 ********************************************************************************/
	function agregarMedicamento( $codigo, $infoArticulo, $sinConfirmarPreparacion ){

		@$infoArticulo[ 'confirmado' ] = $sinConfirmarPreparacion;

		//Si ya existe, sumo la cantidad a aplicar nuevamente
		//Y dejo que todo continue normal
		if( isset( $this->medicamentos[ $codigo ][ 'codigo' ] ) ){
			$infoArticulo[ 'cantidadAAplicar' ] += $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ];
		}

		if( true || !isset( $this->medicamentos[ $codigo ][ 'codigo' ] ) ){

			if( !$sinConfirmarPreparacion ){
				$this->tieneArticulosSinConfirmar = !$infoArticulo[ 'confirmado' ];
			}

			$this->medicamentos[ $codigo ] = $infoArticulo;

			$this->totalMedicamentos++;

			if( $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ] == $this->medicamentos[ $codigo ][ 'cantidadAplicada' ] ){
				$this->totalAplicados++;
			}

			if( @$infoArticulo[ 'aNecesidad' ] != "No" ){
				$this->totalMedicamentosNecesidad++;

				if( @$infoArticulo[ 'perteneceRonda' ] ){
					$this->totalMedicamentosNecesidadPertenecientesRonda++;
				}
			}


			if( !empty( $infoArticulo[ 'Jusjus' ] ) ){
				$this->totalMedicamentosJustificados++;
			}

			//Son medicamentos Obligatorios aquellos que no tienen justificacion, no son a necesidad y no estan suspendidos
			if( $infoArticulo[ 'aNecesidad' ] == "No" && empty( $infoArticulo[ 'Jusjus' ] ) && $infoArticulo[ 'suspendido' ] == "No" ){

				$this->totalMedicamentosObligatorios++;

				if( $this->medicamentos[ $codigo ][ 'cantidadAAplicar' ] <= $this->medicamentos[ $codigo ][ 'cantidadAplicada' ] ){
					$this->totalMedicamentosObligatoriosAplicados++;
				}
			}

		}
	}

	/**********************************************************************
	 * Define el color a mostrar para la celda
	 **********************************************************************/
	function estado(){

		$val = '';

		if( true || $this->tieneKardex == 'on' ){
			if( true || $this->esKardexConfirmado == 'on' ){

				//Si el total de medicamentos para una ronda es igual a la cantidad de medicamentos a necesidad entonces no deben salir nada
				if( $this->totalMedicamentos > 0 /*&& $this->totalMedicamentos != $this->totalMedicamentosNecesidad*/ ){

					//Si no hay medicamentos aplicados o los medicamentos obligatorios no fueron todos aplicados
					if( $this->totalAplicados == 0 || $this->totalMedicamentosObligatorios > $this->totalMedicamentosObligatoriosAplicados ){

						if( $this->totalMedicamentosObligatorios == 0 && $this->totalMedicamentos <= $this->totalMedicamentosNecesidad + $this->totalMedicamentosJustificados ){

							if( $this->totalMedicamentos == $this->totalMedicamentosNecesidad && $this->totalAplicados > 0 ){

								$this->color = CLR_VERDE;
							}
							elseif( $this->totalMedicamentos != $this->totalMedicamentosNecesidad || $this->totalMedicamentos == $this->totalMedicamentosNecesidadPertenecientesRonda ){
								//Si los medicamentos no fueron aplicados pero todos son a necesidad o tiene justificacion
								$this->color = CLR_AMARILLO;
							}
							//Si puede aplicar es de urgencias y este cambio aplica solo para urgencias
							//
							elseif( $this->puedeAplicar && $this->totalMedicamentos == $this->totalMedicamentosNecesidad ){
								$this->color = CLR_AMARILLO;
							}
						}
						//Si por lo menos un medicamento obligatorio no fue aplicado
						elseif( $this->totalMedicamentosObligatorios > $this->totalMedicamentosObligatoriosAplicados ){

							$this->color = CLR_ROJO;
						}
					}
					elseif( $this->totalAplicados > 0 ){
						//Si todo los medicamentos obligatorios fueron aplicados
						$this->color = CLR_VERDE;
					}
				}
			}
			else{
				$this->color = CLR_KSC;
			}
		}
		else{
			$this->color = CLR_SK;
		}

		switch( $this->color ){

			case CLR_VERDE:
				$val = "<img  src='/matrix/images/medical/movhos/checkmrk.ico'>";
				break;

			case CLR_AMARILLO:
				$val = "<img  src='/matrix/images/medical/root/borrarAmarillo.png'>";
				break;

			case CLR_ROJO:
				$val = "<span class='blink'><img  src='/matrix/images/medical/root/borrar.png'></span>";
				break;

			default: $val = '';
				break;
		}

		$this->descripcion = $val;
	}
};

class AuditoriaDTO{
	var $fechaRegistro = "";
	var $horaRegistro = "";
	var $historia = "";
	var $ingreso = "";
	var $fechaKardex = "";
	var $descripcion = "";
	var $mensaje = "";
	var $seguridad = "";

	//Anexo para reporte de cambios por tiempo
	var $servicio = "";
	var $confirmadoKardex = "";

	var $idOriginal = 0;
}

class AccionPestanaDTO{
	var $nroPestana = "";
	var $codigoAccion = "";
	var $crear = "";
	var $leer = "";
	var $borrar = "";
	var $actualizar = "";
}

class movimientoHospitalarioDTO {
	var $consecutivo;
	var $historia;
	var $ingreso;
	var $servicioOrigen;
	var $servicioDestino;
	var $habitacionOrigen;
	var $habitacionDestino;
	var $tipoMovimiento;
}

//----------------------------------------------------------
//			        FUNCIONES TRASLADO DE PACIENTES
//----------------------------------------------------------

 /************************************************************************
  * Consulta los roles que pueden leer las ordenes pendientes
  ************************************************************************/
function permiteLecturaOrdenesPendientes( $conex, $wemp_pmla, $rol ){

	$val = false;

	$conRoles = consultarAliasPorAplicacion( $conex, $wemp_pmla, "lecturaOrdenesPendientes" );

	$roles = explode( ",", $conRoles );

	foreach( $roles as $key => $value ){
		if( $value == $rol ){
			$val = true;
			$break;
		}
	}

	return $val;
}

function log_pantallas_pisos($slCcoDestino){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$hora_actual = time();
	$tiemponotificaciongestionpisos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'TiempoNotificacionGestionPisos');
	$tiemponotificaciongestionpisos = explode("-",$tiemponotificaciongestionpisos);

	$tiempo1 = $tiemponotificaciongestionpisos[0]; //Tiempo en segundo inicial para validacion de pantalla inactiva.
	$tiempo2 = $tiemponotificaciongestionpisos[1]; //Tiempo maximo para la segunda notificacion de pantalla inactiva.

	$q = "UPDATE ".$wbasedato."_000011
		     SET Ccofpa = '".date("Y-m-d")."', Ccohpa = '".date("H:i:s")."', Cconen = ''
		   WHERE Ccocod = '".$slCcoDestino."'
		     AND Ccohos = 'on'";
	$err = mysql_query($q,$conex);

	if(isset($_SESSION['inactiva_mas_a_12_horas_'.$slCcoDestino])){

		unset($_SESSION['inactiva_mas_a_12_horas_'.$slCcoDestino]);

	}

	//Evaluar si no se actualizo el registro en 3 minutos
	//Revisa todos los centros de costos y el que tenga un tiempo mayor a 3 minutos en el campo Ccohpa enviara un mensaje avisando.
	$q = "SELECT Ccofpa, Ccohpa, Ccocod, Cconom, Cconen
		 	FROM ".$wbasedato."_000011
		   WHERE Ccohos = 'on'
		     AND Ccopan = 'on'";
	$err = mysql_query($q,$conex);

	$array_cco_pantallas = array();

	while($row = mysql_fetch_assoc($err)){

		$ultima_recarga = $row['Ccofpa']." ".$row['Ccohpa'];
		$hora_ultima_recarga = $row['Ccohpa'];

		$tiempo = strtotime(date("Y-m-d H:i:s")) - strtotime( $ultima_recarga );

		if($tiempo > $tiempo1 and $hora_ultima_recarga != '00:00:00' ){

			$array_cco_pantallas[$row['Ccocod']] = $row;
		}

		if($tiempo > $tiempo2 and $row['Cconen']){

			$array_cco_pantallas[$row['Ccocod']] = $row;
			$array_cco_pantallas[$row['Ccocod']]['tiempo'] = $tiempo;
		}

	}

	if(count($array_cco_pantallas) > 0){

		$wcorreopmla = consultarAliasPorAplicacion( $conex, $wemp_pmla, "emailpmla");
		$wcorreopmla = explode("--", $wcorreopmla );
		$wpassword   = $wcorreopmla[1];
		$wremitente  = $wcorreopmla[0];
		$datos_remitente = array();
		$datos_remitente['email']	= $wremitente;
		$datos_remitente['password']= $wpassword;
		$datos_remitente['from'] = $wremitente;
		$datos_remitente['fromName'] = $wremitente;


		//Envio certificacion correo
		//ConfirmacionEnvioIncapacitados
		$CorreoAlertaPantallaInactiva = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CorreoAlertaPantallaInactiva');
		$CorreoAlertaPantallaInactiva = explode(",", $CorreoAlertaPantallaInactiva);
		$wasuntologi = "Pantallas gestion de enfermeria inactivas";
		$altbodylogi = "";
		$mensajelogi .= "<thead>
							<tr height='80' align='left'>
								<th colspan='4' style='background-color:#ffffff; border-bottom:solid 1px #bdbdbd; font-family:Verdana, Geneva, sans-serif; color:#333; font-size:20px;' >
								<img width='110px' height='70px' src='../../images/medical/root/clinica.JPG'><br><br>Notificacion de pantalla gestion de enfermeria inactiva en los siguientes pisos: <br><br> ".$value['Ccocod']." ".$value['Cconom']." </th>
							</tr>
						 </thead><br><br> ";

		$mensajelogi .= "</table>";

		$mensajelogi .= "<table border='1' width='600px' cellpadding='0' cellspacing='0' border='0'>";
		$mensajelogi .= "<tr class='encabezadoTabla'>";
		$mensajelogi .= "<td align=center><b>Centro de Costos</b></td><td align=center><b>Descripcion</b></td>";
		$mensajelogi .= "</tr>";

		$enviar_correo = "off";

			foreach($array_cco_pantallas as $key => $value){


				if($value['Cconen'] != 'on'){

					$q = "UPDATE ".$wbasedato."_000011
							 SET Cconen = 'on'
						   WHERE Ccocod = '".$value['Ccocod']."'
							 AND Ccohos = 'on'";
					$err = mysql_query($q,$conex);

					$mensajelogi .= "<tr>";
					$mensajelogi .= "<td align=center>".$value['Ccocod']."</td><td align=center>".$value['Cconom']."</td>";
					$mensajelogi .= "</tr>";

					$enviar_correo = "on";

				}elseif($value['tiempo'] > $tiempo2 and (!isset($_SESSION['inactiva_mas_a_12_horas_'.$value['Ccocod']]))){

					$mensajelogi .= "<tr>";
					$mensajelogi .= "<td align=center>".$value['Ccocod']." (Mas de 12 horas inactiva)</td><td align=center>".$value['Cconom']."</td>";
					$mensajelogi .= "</tr>";

					$_SESSION['inactiva_mas_a_12_horas_'.$value['Ccocod']] = "ok";
					$enviar_correo = "on";
				}

			}

		$mensajelogi .= "</table>";

		if($enviar_correo == 'on'){
			$sendToEmail = sendToEmail($wasuntologi,$mensajelogi,$altbodylogi, $datos_remitente, $CorreoAlertaPantallaInactiva );
		}

	}


}

function consultarCentroCostoAyuda($wselcco){

	global $wbasedato;
	global $conex;
	$es = "";

	$q = "SELECT Ccoayu
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$wselcco."'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccoayu'] == 'on') ? $es = true : $es = false;
	}

	return $es;


}

function ccoAdmisiones(){

	global $wbasedato;
	global $conex;

	$es = "";

	$q = "SELECT Ccocod
		 	FROM ".$wbasedato."_000011
		   WHERE Ccoadm = 'on'
		   LIMIT 1";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		$es = $rs['Ccocod'];
	}

	return $es;


}


function consultarCentroCostoIngreso($wselcco){

	global $wbasedato;
	global $conex;
	$es = "";

	$q = "SELECT Ccoing
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$wselcco."'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccoing'] == 'on') ? $es = true : $es = false;
	}

	return $es;


}


//Verifica si el mercado de cirugia esta liquidado paraq un turno.
function validar_mercado_liq($wturno_cir){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	$estado_liq_mercado = 'on';

	$q = "  SELECT Aueliq
			  FROM ".$wbasedatocliame."_000252
			 WHERE Auetur = '".$wturno_cir."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){

		$row = mysql_fetch_array($res);
		$estado_liq_mercado = $row['Aueliq'];
	}

	return $estado_liq_mercado;
}


/****************************************************************************************
	* Agregada en 2013-02-22,
	ANTES DE INSERTAR UNA ALTA O UNA MUERTE PARA UN PACIENTE SE CONSULTA SI YA TUVO ALTA O MUERTE Y SE ELIMINAN, Y ACTUALIZA EN INDICADOR
 ****************************************************************************************/
function BorrarAltasMuertesAntesDeAgregarNueva($conex, $wbasedato, $whis, $wing, $bandera)
{
	$user_session = explode('-',$_SESSION['user']);
	$seguridad = $user_session[1];

	if( !isset( $bandera ) ){
		$bandera = "";
	}

	$q = "    SELECT *
			FROM ".$wbasedato."_000033
			WHERE Historia_clinica = '".$whis."'
			AND Num_ingreso = '".$wing."'
			AND Tipo_egre_serv REGEXP 'MUERTE MAYOR A 48 HORAS|MUERTE MENOR A 48 HORAS|ALTA' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	$arregloDatos = array();

	if ($num > 0)
	{
		while($row = mysql_fetch_assoc($res))
		{
			$result = array();
			$result['fecha'] = $row['Fecha_data'];
			$result['cco'] = $row['Servicio'];
			$result['egreso'] = $row['Tipo_egre_serv'];
			array_push( $arregloDatos, $result );
		}
	}

	if( count( $arregloDatos )  > 0 )
	{

		foreach( $arregloDatos as $dato )
		{

			$wfecha = $dato['fecha'];
			$wcco = $dato['cco'];
			$wtipoEgresoABorrar = $dato['egreso'];

			$q = " SELECT * "
				."   FROM ".$wbasedato."_000038 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Cieser = '".$wcco."'";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_assoc($res);


			$existe_en_la_67 = false;
			$q67 = " SELECT * "
				."   FROM ".$wbasedato."_000067 "
				."  WHERE Fecha_data = '".$wfecha."'"
				."    AND Habhis = '".$whis."'"
				."    AND Habing = '".$wing."'";

			$res67 = mysql_query($q67,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num67 = mysql_num_rows($res67);
			if( $num67 > 0 ){
				$existe_en_la_67 = true;
			}

			$cant_egresos = $row['Cieegr'];
			$cant_camas_ocupadas = $row['Cieocu'];
			$cant_camas_disponibles = $row['Ciedis'];
			$muerteMayor = $row['Ciemmay'];
			$muerteMenor = $row['Ciemmen'];
			$egresosAlta = $row['Cieeal'];
			//Restamos uno al motivo de egreso que tenia el paciente

			if(preg_match('/ALTA/i',$wtipoEgresoABorrar))
			{
				$egresosAlta--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MAYOR/i',$wtipoEgresoABorrar)) //Muerte mayor
			{
				$muerteMayor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}
			else if(preg_match('/MENOR/i',$wtipoEgresoABorrar))
			{ // Muerte menor
				$muerteMenor--;
				$cant_egresos--;
				if( $existe_en_la_67 === false )
				{
					$cant_camas_ocupadas++;
					$cant_camas_disponibles--;
				}
			}

			$query_para_log = "    SELECT *
				FROM ".$wbasedato."_000033
				WHERE Historia_clinica = '".$whis."'
				AND Num_ingreso = '".$wing."'
				AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$registrosFila = obtenerRegistrosFila($query_para_log);

			$q ="    DELETE FROM ".$wbasedato."_000033
					 WHERE Historia_clinica = '".$whis."'
					   AND Num_ingreso = '".$wing."'
					   AND Tipo_egre_serv = '".$wtipoEgresoABorrar."'";
			$res = mysql_query($q,$conex);

			$num_affect = mysql_affected_rows();
			if($num_affect>0)
			{

			$q = "   UPDATE ".$wbasedato."_000038 "
					."  SET Ciemmay = '".$muerteMayor."',"
					."      Ciemmen = '".$muerteMenor."',"
					."      Cieeal = '".$egresosAlta."',"
					."      Cieegr = '".$cant_egresos."',"
					."      Cieocu = '".$cant_camas_ocupadas."',"
					."      Ciedis = '".$cant_camas_disponibles."'"
					." WHERE Fecha_data = '".$wfecha."'"
					."  AND Cieser = '".$wcco."'"
					." LIMIT 1 ";

				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				//Guardo LOG de borrado en tabla movhos 33 - Activacion paciente
				$q = "    INSERT INTO log_agenda
										  (Fecha, Hora, Historia, Ingreso, Accion, Seguridad, Bandera, Registros)
								   VALUES
										  ('".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."', '".$wing."', 'Borrado tabla movhos_000033', '".$seguridad."', '".$bandera."', '".$registrosFila."')";
				$resl2 = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
	}
}


function requiere_justificacion($widreg)
{

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	//acá traemos la hora de alta en proceso.
	$q = "SELECT Ubifap, Ubihap, Ubisac"
		."  FROM ".$wbasedato."_000018"
		." WHERE id = '".$widreg."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	//verificamos si el cco necesita promediación de alta
	$q2 = "SELECT Ccopal"
		."  FROM ".$wbasedato."_000011"
		." WHERE Ccocod = '".$row[2]."'";
	$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
	$row2 = mysql_fetch_array($res2);

	if($row2[0]=="off")
		return false;

	if(($row[0]=="0000-00-00")or($row[1]=="00:00:00"))
		return false;

	$htiap = strtotime($row[0]." ".$row[1]); //tiempo alta en proceso en segundos por UNIX

	//acá tengo que consultar la meta de la empresa.

	$q = "SELECT Empmsa "
		."  FROM root_000050 "
		." WHERE Empcod = '".$wemp_pmla."'";
	$res = $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);


	$segholgura = explode(":", $row[0]);
	$segholgura = ((integer)$segholgura[0]*60*60) + ((integer)$segholgura[1]*60) + ((integer)$segholgura[2]);


	//acá se consulta el porcentaje de holgura en la meta de la empresa
	$q = "SELECT Detval"
		."  FROM root_000051 "
		." WHERE Detemp = '".$wemp_pmla."'"
		."   AND Detapl = 'HolguraMetaAltas'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);


	//convertimos a un valor decimal la holgura en la meta de la empresa.
	$aux = explode("%",$row[0]);
	$wholgura = (integer)$aux[0];
	$wholgura = $wholgura/100; //ya está como decimal.
	$holgtotal = $segholgura + ($segholgura*$wholgura); // tiempo máximo esperado para dar el alta definitiva


	$horaADE = $htiap + $holgtotal; //hora alta definitiva esperada


	//aca vamos a hacer la resta con la hora actual.
	//$rs = $horaADE - time(); //calculamos la diferencia entre la hora esperada de salida y la hora en la que se efectua la salida Definitiva

	if(time()<$horaADE)
	{
		return false;
	}
	return true;


}

//Verifica los pacientes que tienen una solicitud de cama sin llegada y cumplimiento y estan disponibles para entregar de piso a piso.
function listaPacientesEntregarCcoAyuda($ccoayuda){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$todoslashab = todaslashab();

	$wfecha_actual = date('Y-m-d');
	$fecha_inicial = date("Y-m-d", strtotime("$wfecha_actual -7 day"));

	$codigosccoayuda_aux = explode(",",$ccoayuda);
	$codigosccoayuda = implode("','",$codigosccoayuda_aux);

	// Aca trae los pacientes que estan hospitalizados en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado
	$q = " SELECT ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ".$wcencam."_000003.id as idsolicitud, hab_asignada "
		."   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wcencam."_000003"
		."  WHERE oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
		."    AND oriced  = pacced "
		."    AND oritid  = pactid "
		."    AND orihis  = ubihis "
		."    AND oriing  = ubiing "
		."    AND ubiptr  != 'on' " // Solo los pacientes que no esten siendo trasladados
		."    AND ubisac  in ('" . $codigosccoayuda . "')" // Servicio Actual
		."    AND Anulada = 'No' "
		."	  AND historia = ubihis "
		."    AND central = '".$wcentral_camas."'"
		."	  AND Fecha_cumplimiento =  '0000-00-00'"
		."	  AND Hora_cumplimiento =  '00:00:00'"
		."    AND ".$wcencam."_000003.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$wfecha_actual."'"
		."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
		."  ORDER BY ubihis ";  //se agrega el campo orden
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$coleccion = array();

	if($num > 0){

	while($fila = mysql_fetch_assoc($res)){

			$whis_ing = $fila['ubihis']."-".$fila['ubiing'];
			$coleccion[$whis_ing] = $fila;

		}
	}

	return $coleccion;


}

//Verifica los pacientes que tienen una solicitud de cama sin llegada y cumplimiento y estan disponibles para entregar de piso a piso.
function listaPacientesEntregarHospitalizacion($wcco){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$todoslashab = todaslashab();

	$wfecha_actual = date('Y-m-d');
	$fecha_inicial = date("Y-m-d", strtotime("$wfecha_actual -7 day"));

	// Aca trae los pacientes que estan hospitalizados en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado
	$q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, ".$wcencam."_000003.id as idsolicitud, hab_asignada "
		."   FROM " . $wbasedato . "_000020, root_000036, root_000037, " . $wbasedato . "_000018, ".$wcencam."_000003"
		."  WHERE habcco  = '" . $wcco . "'"
		."    AND habali != 'on' " // Que no este para alistar
		."    AND habdis != 'on' " // Que no este disponible, osea que este ocupada
		."    AND habhis  = orihis "
		."    AND habing  = oriing "
		."    AND oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
		."    AND oriced  = pacced "
		."    AND oritid  = pactid "
		."    AND habhis  = ubihis "
		."    AND habing  = ubiing "
		."    AND ubiptr  != 'on' " // Solo los pacientes que no esten siendo trasladados
		."    AND ubisac  = '" . $wcco . "'" // Servicio Actual
		."    AND Anulada = 'No' "
		."	  AND historia = ubihis "
		."    AND central = '".$wcentral_camas."'"
		."	  AND Fecha_cumplimiento =  '0000-00-00'"
		."	  AND Hora_cumplimiento =  '00:00:00'"
		."    AND ".$wcencam."_000003.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$wfecha_actual."'"
		."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
		."  ORDER BY Habord, Habcod ";  //se agrega el campo orden
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$coleccion = array();

	if($num > 0){

	while($fila = mysql_fetch_assoc($res)){

			$whis_ing = $fila['habhis']."-".$fila['habing'];
			$coleccion[$whis_ing] = $fila;

		}
	}


	//print_r($coleccion);
	return $coleccion;


}

//Verifica los pacientes que estan pendientes de recibir.
function listaPacientesRecibirHospitalizacion($wcco){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$todoslashab = todaslashab();

	// $wfecha_actual = date('Y-m-d');
	// $fecha_inicial = date("Y-m-d", strtotime("$wfecha_actual -7 day"));

	//Pacientes que tienen solicitud de cama y que aun estan con habitacion asignada desde urgencias, estos pacientes aun estan en el centro de costos de cirugia.
	// Aca trae los pacientes que estan hospitalizados en el servicio (ubisac) del usuario matrix y que no esten en proceso de traslado
	$q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, pactid, pacced, MAX(".$wcencam."_000003.id) as idsolicitud, hab_asignada "
		."   FROM " . $wbasedato . "_000020, root_000036, root_000037, " . $wbasedato . "_000018 ,  ".$wcencam."_000003"
		."  WHERE habcco  = '" . $wcco . "'"
		."    AND habali  = 'off' " // Que no este para alistar
		."    AND habhis  = orihis "
		."    AND habing  = oriing "
		."    AND oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
		."    AND oriced  = pacced "
		."    AND oritid  = pactid "
		."    AND habhis  = ubihis "
		."    AND habing  = ubiing "
		."    AND ubiptr  = 'on' " // Solo los pacientes que esten siendo trasladados
		."    AND ubisac  = '" . $wcco . "'" // Servicio Anterior
		."    AND hab_asignada = ubihac"
		."	  AND historia = ubihis "
		."    AND Anulada = 'No'"
		."    AND central = '".$wcentral_camas."'"
		//."    AND ".$wcencam."_000003.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$wfecha_actual."'"
		."  GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
		."  ORDER BY Habord, Habcod ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$coleccion = array();

	if($num > 0){

	while($fila = mysql_fetch_assoc($res)){

			$whis_ing = $fila['habhis']."-".$fila['habing'];
			$coleccion[$whis_ing] = $fila;

		}
	}


	//print_r($coleccion);
	return $coleccion;


}


function modificarIngresoPaciente($conex, $wbasedato, $historia, $ingreso, $servicio)
{
	$q = "	SELECT Ubisac, Ubihac, Ubisan, Ubihan
			  FROM ".$wbasedato."_000018
			 WHERE Ubihis = '".$historia."'
			   AND Ubiing = '".$ingreso."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	$ubisac = $row['Ubisac'];
	$ubihac = $row['Ubihac'];
	$ubiptr = 'off';

	if($row['Ubisan']!='' && $row['Ubisan']!='NO APLICA' && $row['Ubisan']!='.')
	{
		$ubisac = $row['Ubisan'];
		$ubihac = $row['Ubihan'];
	}

	$q = "UPDATE ".$wbasedato."_000018
			 SET Ubiptr = '".$ubiptr."', Ubisac = '".$ubisac."', Ubihac = '".$ubihac."', Ubisan = '', Ubihan = ''
		   WHERE Ubihis = '".$historia."'
			 AND Ubiing = '".$ingreso."';";
	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}


function deshabilitarUltimoMovimientoHospitalario($conex, $wbasedato, $whistoria, $wingreso){
	$q = "UPDATE
			".$wbasedato."_000017
		SET
			Eyrest = 'off'
		WHERE
			Eyrhis = '".$whistoria."'
			AND Eyrtip = 'Entrega'
			AND Eyring = '".$wingreso."';";

	$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();
}

//Esta funcion reemplaza la funcion consultarPacienteUnix, ya que este programa no debe depender de la conexion con este sistema. (25 Noviembre 2013 Jonatan)
//Funcion que consulta todos los datos de un paciente, verificando que el alta definitiva sea off, osea que este activo en la clinica.
function consultarPacienteMatrix($pacienteConsulta){


	global $wemp_pmla;
	global $conex;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$paciente = new pacienteDTO();

	$ingreso = consultarUltimoIngresoHistoria($conex, $pacienteConsulta->historiaClinica, $wemp_pmla);

	$q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, pacnac, pacsex, Ubisac, Ubihac, Ubisan, Ubihan,
				 d.fecha_data as fechaIngreso, d.Hora_data as horaIngreso, Ingres, Ingnre, Ingtip
		    FROM root_000036 as a, root_000037 as b, ".$wbasedato."_000018 as c, ".$wbasedato."_000016 as d
		   WHERE oriced = pacced
			 AND oritid = pactid
			 AND Ubihis = Orihis
			 AND Ubiing = Oriing
			 AND Ubihis = Inghis
			 AND Ubiing = Inging
			 AND Ubiald = 'off'
			 AND orihis = '".$pacienteConsulta->historiaClinica."'
			 AND oriing = '".$ingreso."'
			 AND oriori = '".$wemp_pmla."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$info = mysql_fetch_array($res);

		$paciente->historiaClinica = $pacienteConsulta->historiaClinica;
		$paciente->ingresoHistoriaClinica = $ingreso;
		$paciente->nombre1 = $info['pacno1'];
		$paciente->nombre2 = $info['pacno2'];
		$paciente->apellido1 = $info['pacap1'];
		$paciente->apellido2 = $info['pacap2'];
		$paciente->documentoIdentidad = $info['pacced'];
		$paciente->tipoDocumentoIdentidad = $info['pactid'];
		$paciente->fechaNacimiento = $info['pacnac'];
		$paciente->genero = $info['pacsex'];
		$paciente->fechaIngreso = $info['fechaIngreso'];
		$paciente->horaIngreso = $info['horaIngreso'];
		$paciente->habitacionActual = $info['Ubihac'];
		$paciente->numeroIdentificacionResponsable = $info['Ingres'];
		$paciente->nombreResponsable = $info['Ingnre'];
		$paciente->servicioActual = $info['Ubisac'];
		$paciente->tipoResponsable = $info['Ingtip'];

	}

	return $paciente;
}


//Funcion que cancela la solicitud de cama si cancelan la admision de un paciente
function cancelar_solicitud_cama($wemp_pmla, $whistoria)
{

	global $conex;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

    $q2 =        "  SELECT max(id) as id "
                ."    FROM ".$wcencam."_000003 A"
                ."   WHERE Hora_llegada      = '00:00:00' "
                ."     AND Hora_Cumplimiento = '00:00:00' "
                ."     AND Anulada           = 'No' "
                ."     AND Historia          != ''"
                ."     AND Historia          = '".$whistoria."'"
                ."     AND Central           = '".$wcentral_camas."'";
    $res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q2 . "-" . mysql_error());
    $row = mysql_fetch_array($res2);
    $wid = $row['id'];

    $q3 =        "  SELECT Hab_asignada "
                ."    FROM ".$wcencam."_000003"
                ."   WHERE id      = '".$wid."' ";
    $res3 = mysql_query($q3, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q3 . "-" . mysql_error());
    $row_hab = mysql_fetch_array($res3);
    $whab_pro = $row_hab['Hab_asignada'];

	//La habitacion se pone en proceso de ocupacion
    $q_hab =  " UPDATE ".$wbasedato."_000020 "
        . "    SET Habpro = 'off'"
        . "  WHERE Habcod = '".$whab_pro."'";
    $err = mysql_query($q_hab, $conex) or die (mysql_errno() . $q_hab . " - " . mysql_error());


    $q = "UPDATE ".$wcencam."_000003
		     SET Anulada = 'Si'
		   WHERE id     = '".$wid."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());

}

//Consulta si el paceinte tiene turno de cirugia entre ayer y hoy.
function consultar_turno($whistoria, $wingreso){

	global $conex;
	global $wemp_pmla;

	$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");

	$wfecha_actual = date('Y-m-d');
	$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));

	$q = "  SELECT turtur
			  FROM ".$wtcx."_000011
			 WHERE turhis = '".$whistoria."'
			   AND turnin = '".$wingreso."'
			   AND turfec BETWEEN '".$dia_anterior."' AND '".$wfecha_actual."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	return $row['turtur'];

}

// Funcion que permite extraer la edad del paciente en años, meses y dias.
function calcularAnioMesesDiasTranscurridos($fecha_inicio, $fecha_fin = '')
    {
        $datos = array('anios'=>0,'meses'=>0,'dias'=>0);

        if($fecha_inicio != '' && $fecha_inicio != '0000-00-00')
        {
            $fecha_de_nacimiento = $fecha_inicio;

            $fecha_actual = date ("Y-m-d");
            if($fecha_fin != '' && $fecha_fin != '0000-00-00')
            {
                $fecha_actual = $fecha_fin;
            }
            // echo "<br>Fecha final: $fecha_actual";
            // echo "<br>Fecha inicio: $fecha_de_nacimiento";

            // separamos en partes las fechas
            $array_nacimiento = explode ( "-", $fecha_de_nacimiento );
            $array_actual = explode ( "-", $fecha_actual );

            $anos =  $array_actual[0] - $array_nacimiento[0]; // calculamos años
            $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
            $dias =  $array_actual[2] - $array_nacimiento[2]; // calculamos días

            //ajuste de posible negativo en $días
            if ($dias < 0)
            {
                --$meses;

                //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
                switch ($array_actual[1]) {
                    case 1:     $dias_mes_anterior=31; break;
                    case 2:     $dias_mes_anterior=31; break;
                    case 3:
                            if (checkdate(2,29,$array_actual[0]))
                            {
                                $dias_mes_anterior=29; break;
                            } else {
                                $dias_mes_anterior=28; break;
                            }
                    case 4:     $dias_mes_anterior=31; break;
                    case 5:     $dias_mes_anterior=30; break;
                    case 6:     $dias_mes_anterior=31; break;
                    case 7:     $dias_mes_anterior=30; break;
                    case 8:     $dias_mes_anterior=31; break;
                    case 9:     $dias_mes_anterior=31; break;
                    case 10:     $dias_mes_anterior=30; break;
                    case 11:     $dias_mes_anterior=31; break;
                    case 12:     $dias_mes_anterior=30; break;
                }
                $dias=$dias + $dias_mes_anterior;
            }

            //ajuste de posible negativo en $meses
            if ($meses < 0)
            {
                --$anos;
                $meses=$meses + 12;
            }
            //echo "<br>Tu edad es: $anos años con $meses meses y $dias días";
            $datos['anios'] = $anos;

        }

        return $datos;
    }


function traerresponsable($whis, $tipo_consulta)
    {

         global $conex;
		 global $wemp_pmla;

         $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');;


         switch ($tipo_consulta) {

              case 'historia':

							$q = " SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced, ingnre"
								."   FROM root_000036, root_000037, ".$wmovhos."_000018, ".$wmovhos."_000016"
								."  WHERE Inghis = '".$whis."'"
								."    AND ubihis  = Inghis"
								."    AND ubiing  = Inging"
								."    AND ubihis  = orihis "
								."    AND ubiing  = oriing "
								."    AND oriori  = '".$wemp_pmla."'" // Empresa Origen de la historia,
								."    AND oriced  = pacced "
								."    AND oritid  = pactid "
								."  GROUP BY 1, 2, 3, 4, 5, 6, 7 "
								."  ORDER BY Inghis, Inging ";
							$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
							$row = mysql_fetch_array($res);

                 break;

             default:
                 break;
         }


        $wresponsable = $row['ingnre'];

        return $wresponsable;
    }


function registrarAsignacion($wemp_pmla, $wid)
{
    global $conex;
    global $wusuario;

    $wfecha = date("Y-m-d");
    $whora =(string)date("H:i:s");


	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');

    $q =     "  SELECT Acaids "
            ."    FROM ".$wcencam."_000010"
            ."   WHERE Acaids   = '".$wid."'"
            ."     AND Acaest   = 'on'";
    $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
    $row = mysql_fetch_array($res);

    //Si el id ya tiene registro en on en la tabla 10 de cencam, no hara registro de datos.
    if ($row['Acaids'] == '')
        {
        $q =  " INSERT INTO ".$wcencam."_000010(   Medico   ,   Fecha_data,   Hora_data,    Acaids,  Acaest, Acarea, Seguridad     ) "
                                . "    VALUES('".$wcencam."','".$wfecha."','".$whora."','".$wid."',   'on' ,  'off', 'C-" . $wusuario . "')";
        $err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
        }


}


function solicitarCamillero($centroCosto, $wemp_pmla, $whistoria, $tipo_habitacion = '', $obs_soli_cama, $hab_actual){

		global $conex;

        //Con el ccentro de conto de origen consulta en cencam 4 el nombre
        $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros');
		$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

		$ccostos_temp = $centroCosto;

        $sqlRoot = "SELECT r.Ccaorg, r.Ccamot, r.Ccades, r.Ccaobs, cn.central
					  FROM root_000107 r
                INNER JOIN ".$wcencam."_000001 cn ON cn.Descripcion = r.Ccamot
                WHERE r.Ccaorg = '".$centroCosto."'";
        $result = mysql_query($sqlRoot, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlRoot."):</b><br>".mysql_error());
        $row = mysql_fetch_assoc($result);
		$wcentro_costos_solicita = $row["Ccaorg"];

		if($wcentro_costos_solicita == '')  {

			$sqlRoot = "SELECT r.Ccaorg, r.Ccamot, r.Ccades, r.Ccaobs, cn.central
						  FROM root_000107 r
					INNER JOIN ".$wcencam."_000001 cn ON cn.Descripcion = r.Ccamot
						 WHERE r.Ccaorg = '*'";
			$result = mysql_query($sqlRoot, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlRoot."):</b><br>".mysql_error());
			$row = mysql_fetch_assoc($result);
			$wcentro_costos_solicita = $row["Ccaorg"];

		}

        if($wcentro_costos_solicita != ""){

                $sqlCencam = "SELECT Nombre
                                FROM ".$wcencam."_000004
                               WHERE Cco LIKE '".$ccostos_temp."%'
                               LIMIT 1";
                $resultCen= mysql_query($sqlCencam, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlCencam."):</b><br>".mysql_error());
                $rowCen = mysql_fetch_assoc($resultCen);

                if(isset($rowCen["Nombre"]) && $rowCen["Nombre"] != ""){

					//---- Datos del paciente

							//Busco si lo digitado es la historia y con ese dato traigo el nombre del paciente
							 //si no busco si es la cedula y con el dato busco por cedula
							 $q = " SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex "
								."   FROM root_000036, root_000037 "
								."  WHERE orihis = '".$whistoria."'"       //Como Historia
								."    AND oriori = '".$wemp_pmla."'"
								."    AND oriced = pacced "
								."    AND oritid = pactid ";
							$reshab = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
							$rowhab = mysql_fetch_array($reshab);
							$numhab = mysql_num_rows($reshab);
							$whis = $whab; // En este caso se guardara la historia para el paciente en al tabla 3 de cencam.
							$wedad = calcularAnioMesesDiasTranscurridos($rowhab[4], $fecha_fin = '');
							$wresponsable = traerresponsable($whistoria,'historia');

						  switch ($rowhab[5]) {
							case 'M':
									$wgenero = "Masculino";
							break;

							case 'F':
									$wgenero = "Femenino";
							break;


							default:
								break;
							}
							if ($numhab > 0){
							   $whab="<b>Historia: ".$whistoria."</b><br><b>Hab:".$hab_actual."</b></br>Pac: ".$rowhab[0]." ".$rowhab[1]." ".$rowhab[2]." ".$rowhab[3]."<br>Edad:".$wedad['anios']."<br>Genero:".$wgenero."<br>Responsable:".$wresponsable;
							}else{
								$whab="<b>".$whistoria."</b><br>El dato No existe en la Base de Datos";
							}

					///------

                    //Variables necesarias para solicitar un camillero
                    $origen = $rowCen["Nombre"];
                    $motivo = $row["Ccamot"];
                    $destino = $row["Ccades"];
                    $solicito = isset($_SESSION["usera"]) ? $_SESSION["usera"] : "";
                    $ccosto = $row["Ccades"];
                    $observacion = $row["Ccaobs"];
					$observacion = utf8_decode($obs_soli_cama)."<br><br>".$observacion;
                    $fecha = date("Y-m-d");
                    $hora = date("H:i:s");
                    $central = $row["central"];

                    $sqlSolcitudCamillero = "INSERT INTO ".$wcencam."_000003
                                                (Medico, Fecha_data, Hora_data, Origen, Motivo, Habitacion, Historia, Observacion, Destino, Solicito, Ccosto, Camillero,  Anulada, Central, Seguridad)
                                            VALUES
                                                ('".$wcencam."', '".$fecha."', '".$hora."', '".$origen."', '".$motivo."','".$whab."', '".$whistoria."', '".$observacion."',
                                                '".$destino."', '".$solicito."', '".$ccosto."', '".$tipo_habitacion."', 'No', '".$central."','C-cencam')";
                    $respSolicitud = mysql_query($sqlSolcitudCamillero, $conex) or die("<b>ERROR EN QUERY MATRIX(".$sqlSolcitudCamillero."):</b><br>".mysql_error());
                    $wid = mysql_insert_id();

                    registrarAsignacion($wemp_pmla, $wid);

				}

		}

		return $wid;
}


//Funcion que actualiza la solicitud a realizada en la tabla 10 de cencam, y actualiza el identificador con la fecha y hora de llegada
//si no la tiene, ademas de la fecha y hora de cumplimiento si no la tiene.
function actualizarregistros($conex, $wemp_pmla, $wid)
{

	$wfecha = date("Y-m-d");
	$whora  = date("H:i:s");

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");

    //La solicitud se cambia a realizado en la tabla 10 de cencam.
    $q=  " UPDATE ".$wcencam."_000010 "
        ."    SET Acarea = 'on' "
        ."  WHERE Acaids ='".$wid."'"
        ."    AND Acaest = 'on'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

    //Se consultan los datos para la solicitud
    $sql = "SELECT Fecha_llegada, Hora_llegada, Fecha_Cumplimiento, Hora_cumplimiento
			  FROM ".$wcencam."_000003
			 WHERE id = '".$wid."'";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_array($res);

    $wfecha_llegada = $row['Fecha_llegada'];
    $wfecha_cumplimiento = $row['Fecha_Cumplimiento'];

    //Aqui actualizamos la fecha de llegada si no tiene.
    if ($wfecha_llegada == '0000-00-00')
    {
    $q=  " UPDATE ".$wcencam."_000003 "
        ."    SET Fecha_llegada = '".$wfecha."', Hora_llegada = '".$whora."' "
        ."  WHERE id ='".$wid."'"
        ."    AND Anulada = 'No'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    }

    //Aqui actualizamos la fecha de cumplimiento si no tiene.
    if ($wfecha_cumplimiento == '0000-00-00')
    {
    $q=  " UPDATE ".$wcencam."_000003 "
        ."    SET Fecha_cumplimiento = '".$wfecha."', Hora_cumplimiento = '".$whora."' "
        ."  WHERE id ='".$wid."'"
        ."    AND Anulada = 'No'";
    mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
    }


}

function registrarSaldosNoApl($wbasedato, $usuario, $tipTrans, $id, $cantidad){

		$conex = obtenerConexionBD("matrix");

		/**
		 * Existe saldo para el artículo, para esa historia en ese ingreso en ese centro de costos.
		 * Por lo cual se hace un update del saldo, no es necesario crear un registro nuevo.
		 */

		// Si es un aprovechamiento, hay que aumentar el contenido de los aprovechamientos.
		$campoAprov="Spaa";
		$campo = "Spau"	;

		/**
		 * Sí NO es una inactivación debe SUMAR la cantidad de $art['can'] en
		 * las entradas o las salidas según sea un cargo o una devoluciónr, respectivamente.
		 */
		//Es una entrada para el paciente
		$campoEn = $campo."en"." = ".$campo."en + ".$cantidad	;
		$campoAprovEn = $campoAprov."en = ".$campoAprov."en + ".$cantidad;

		//Es una salida para el paciente osea una devolución.
		$campoSa = $campo."sa = ".$campo."sa + ".$cantidad;
		$campoAprovSa = $campoAprov."sa = ".$campoAprov."sa + ".$cantidad;

		if($tipTrans == 'C')
		{
			$set=$campoEn;
			if($aprov)
			{
				$set=$set.", ".$campoAprovEn;
			}
		}
		else
		{
			$set=$campoSa;
			if($aprov)
			{
				$set=$set.", ".$campoAprovSa;
			}
		}


		/**
		 * Se realiza el update en la tabla de saldos así:
		 * *Si se esta cargando a la cuenta del paciente la cantidad de artículo se suma a Spauen.
		 *  Si además es un aprovechamiento entonces tambien se suma la cantidad a Spaaen.
		 * *Si se esta devolviendo a la cuenta del paciente la cantidad de artículo se suma a Spausa.
		 *  Si además es un aprovechamiento entonces tambien se suma la cantidad a Spaasa.
		 * *Sí es una incactivación funciona igual que en los dos pasos anteriores solo que en vez de sumar resta.
		 */
		$q = " UPDATE ".$wbasedato."_000004 "
			."    SET ".$set." "
			."  WHERE id = ".$id." ";
		$err1 = mysql_query($q,$conex);
		echo mysql_error();
		$num=mysql_affected_rows();
		if($num<1)
		{
			$error['ok']	 ="NO INGRESADO A MATRIX";
			$error['color']  ="#ff0000";
			$error['codInt'] ="1007";
			$error['codSis'] =mysql_errno();
			$error['descSis']=mysql_error();
			return (false);
		}
		else
		{
			return (true);
		}
}


function devCons($cco, $whis, $wing, $usuario, $wbasedato, $id, $cantidad){

	global $conex;

	if(registrarSaldosNoApl($wbasedato, $usuario, "D", $id, $cantidad )){

		$q = "LOCK TABLE ".$wbasedato."_000001 WRITE";
		$err = mysql_query($q,$conex);

		$q = " UPDATE ".$wbasedato."_000001 "
				."SET   Connum = Connum +1 "
				."WHERE Contip = 'devcon' ";
		$err = mysql_query($q,$conex);
		echo mysql_error();
		if($err == "") {
			return(false);
			$error['codInt']="1008";
			$error['codSis']=mysql_errno();
			$error['descSis']=mysql_error();

		}
		else
		{

			$q = "  SELECT Connum "
					."FROM  ".$wbasedato."_000001 "
					."WHERE Contip = 'devcon' ";
			$err = mysql_query($q,$conex);
			$num = mysql_num_rows($err);
			echo mysql_error();
			if($num>0)
			{
				$row= mysql_fetch_array($err);
				$devCons= $row['Connum'];

				$q = "UNLOCK TABLES";
				$err = mysql_query($q,$conex);

				/**
				 * Crear el encabezado de la la devolución en 000035
				 */
				$q= " INSERT INTO ".$wbasedato."_000035 (    medico,           Fecha_data,           Hora_data,       Dencon,         Denhis,              Dening,            Denori,         Denusu, Seguridad) "
				."                        VALUES ( '".$wbasedato."',  '".date("Y-m-d")."', '".date('H:i:s')."', ".$devCons.", '".$whis."','".$wing."', '".$cco."', '".$usuario."','A-".$usuario."') ";
				$err=mysql_query($q,$conex);
				echo mysql_error();
				$num=mysql_affected_rows();
				if($num==1)
				{
					return $devCons;
				}
				else
				{
					$error['codInt']="1021";
					$error['codSis']=mysql_errno();
					$error['descSis']=mysql_error();
					return(false);
				}

			}
			else
			{
				/*Error no existe consecutivo*/
				$error['codInt']="";
				$error['codSis']=mysql_errno();
				$error['descSis']=mysql_error();
				return(false);
			}
		}
	}
}


function modificarHistoriaHabitacion($whistoria, $wingreso, $wcodigoHabitacion){

	global $wbasedato;
	global $conex;


	//Desocupar el cubiculo
	$q_cub = " UPDATE ".$wbasedato."_000020
		          SET Habhis = '', Habing = '', habdis = 'on'
		        WHERE Habhis = '".$whistoria."'
		          AND Habing = '".$wingreso."'";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cub . " - " . mysql_error());

	//Ocupa la habitacion asignada.
	$q = "UPDATE ".$wbasedato."_000020
			 SET Habhis = '".$whistoria."',
			     Habing = '".$wingreso."',
			     Habdis = 'off'
		   WHERE Habcod = '".$wcodigoHabitacion."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_affected_rows();

}

function buscar_articulo(&$wcodart){
	global $wbasedato;
	global $wcenmez;
	global $conex;
	global $wok;
	global $wartnom;
	global $wartuni;
	global $wunides;
	global $wemp_pmla;

	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	// Busco el nombre del articulo en el maestro de  articulos de movhos
	$q = " SELECT artcom, artpco, b.unides as unipre, artuni, c.unides "
	. "   FROM " . $wbasedato . "_000026 a LEFT JOIN " . $wbasedato . "_000027 b ON a.artpco = b.unicod, " . $wbasedato . "_000027 c"
	. "  WHERE artcod = '" . $wcodart . "'"
	. "    AND a.artuni = c.unicod ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		$wartnom = $row[0];
		$wartuni = empty($row[1]) ? $row[3]: $row[1];
		$wunides = empty($row[1]) ? $row[4]: $row[2];
		$wok = "on";
	}
	else
	{
		// Busco el nombre del articulo en la base de datos de central de mezclas
		$q = " SELECT artcom, artuni, unides "
		. "   FROM " . $wcenmez . "_000002, " . $wbasedato . "_000027 "
		. "  WHERE artcod = '" . $wcodart . "'"
		. "    AND artuni = unicod ";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
			$wartnom = $row[0];
			$wartuni = $row[1];
			$wunides = $row[2];
			$wok = "on";
		}
		else
		{
			// Busco el nombre del articulo en la base de datos de central de 'movhos', pero buscando con el
			// codigo del proveedor en la tabla movhos_000009
			$wcodart=BARCOD($wcodart);
			$q = " SELECT artcom, artpco as artpre, b.unides, " . $wbasedato . "_000009.artcod, artuni, c.unides "
			. "   FROM " . $wbasedato . "_000009, " . $wbasedato . "_000026 a LEFT JOIN " . $wbasedato . "_000027 b ON a.artpco = b.unicod, " . $wbasedato . "_000027 c "
			. "  WHERE artcba                       = '" . $wcodart . "'"
			. "    AND " . $wbasedato . "_000009.artcod = a.artcod "
			. "    AND a.artuni                       = c.unicod ";

			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$wartnom = $row[0];
				$wartuni = empty( $row[1] ) ? $row[4]: $row[1];
				$wunides = empty( $row[1] ) ? $row[5]: $row[2];
				$wcodart = $row[3];
				$wok = "on";
			}
			else
			{
				$wartnom = "Codigo no existe";
				$wartuni = "";
				$wunides = "";
				$wok = "off";
			}
		}
	}
}

function validar_unidad($art){

	global $wbasedato;
	global $conex;

	$dividir = 1;

	//Si la unidad de la tabla 26 es igual a la unidad de la tabla 115 entonces tomara la concentracion de la tabla 115.
	$q = "  SELECT Relcon
			  FROM ".$wbasedato."_000026, ".$wbasedato."_000115
			 WHERE Relart = Artcod
			   AND Reluni = Artuni
			   AND Relart = '".$art."'" ;
	$res = mysql_query($q, $conex);
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);

	if($num > 0){

		$dividir = $row['Relcon'];

	}else{

		//Revisar si la unidad de presentacion es diferente de la unidad de fracccion, ademas se revisa si la fraccion es igual a 1,
		//en este caso se tomara la concentracion (Ej: un PUFF es igual a una DO)
		$q = "  SELECT Relcon
			      FROM ".$wbasedato."_000059, ".$wbasedato."_000115
				 WHERE Relart = Defart
			       AND Relpre != Deffru
			       AND Relart = '".$art."'
			       AND Deffra = '1'" ;
		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);

		if($num > 0){

			$dividir = $row['Relcon'];

		}else{

			$cco_urg = consultarCcoUrgencias();

			// $q = "  SELECT Arecde
					  // FROM ".$wbasedato."_000008, ".$wbasedato."_000115
					 // WHERE Relart = Areces
					   // AND Relart = '".$art."'
					   // AND Arecco = '".$cco_urg."'" ;

			$q = "  SELECT Arecde
					  FROM ".$wbasedato."_000008
					 WHERE Areces = '".$art."'
					   AND Arecco = '".$cco_urg."'" ;
			$res = mysql_query($q, $conex);
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);

			if($num > 0){

				$dividir = $row['Arecde'];

			}


		}
	}

	return $dividir;

}

// Funcion que validar si el paciente tiene saldo de insumos de enfermeria // 22 mayo de 2017
function traer_insumos_enfermeria($whis, $wing, $nombre_pac, $cod_hab)
{

	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $wartnom;

	$datos_array = array('saldo_insumos'=>'','html'=>'');


	$query =    "SELECT Carins, SUM(Carcca - Carcap - Carcde) as saldo_insumos "
				 ."FROM ".$wbasedato."_000227 "
				."WHERE Carhis = '".$whis."'
					AND Caring = '".$wing."'
					AND Carcca - Carcap - Carcde > 0
					AND Carest = 'on'
			   GROUP BY Carins";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ". $query ." - ".mysql_error());
	$num = mysql_num_rows($res);


	if( !empty($nombre_pac) )
	{
		$html .= "<table width=100%>";
		$class3 = "class='fila".((($i+1)%2)+1)."'";
		$html .= "<tr $class3 align='center'>";
		$html .= "<td style='font-size:14pt'><b>Insumos con saldo asociados al paciente</b></td>";
		$html .= "</tr>";

		$html .= "<tr class='fila2'>";
		$html .= "<td>";
		$html .= "<b>".$whis." - ".$wing;
		$html .= "<br>".$nombre_pac."(".$cod_hab.")";
		$html .= "</td>";
		$html .= "<tr>";
		$html .= "</table>";
		$html .= "<br><br><br>";
	}

	if ($num > 0)
	{
		$html .= "<table border=0>";

		$html .= "<tr class=encabezadoTabla>";
		$html .= "<td>Código</td>";
		$html .= "<td>Insumo</td>";
		$html .= "<td title='Cantidades pendientes de utilizar o devolver'>Saldo</font></td>";
		$html .= "</tr>";
		$sumatoria_saldo = 0;
		while ($row = mysql_fetch_array($res))
		{

			$saldo_insumos_enf = $row['saldo_insumos'];

			if($saldo_insumos_enf > 0){

				if ($i % 2 == 0)
				    $wclass = "fila1";
				  else
					$wclass = "fila2";

				buscar_articulo($row['Carins']);

				$html .= "<tr class=".$wclass.">";
				$html .= "<td>".$row['Carins']."</td>";
				$html .= "<td>".$wartnom."</td>";
				$html .= "<td align=center>".$saldo_insumos_enf."</td>";
				$html .= "</tr>";

				$array_saldos[$row['Carins']] = $saldo_insumos_enf;
				$sumatoria_saldo = $sumatoria_saldo + $saldo_insumos_enf;

			}
		}

		$html .= "</table>";
	}

	
	if( !empty($nombre_pac) )
	{
		$html .= "<br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>";
	}

	$datos_array['saldo_insumos'] = $sumatoria_saldo;
	$datos_array['html'] = $html;



	return $datos_array;

	}


//Funcion que devuelve una lista de medicamntos pendientes de aplicar.
function Detalle_ent_rec_hospi($wtip, $whis, $wing, $nombre_pac, $cod_hab, $ccoOrigen, $wccohos, $id_solicitud_cama, $wccoapl, $hab_asignada, $whab_actual, $wser_destino, $tipo_mov, $wservicio_anterior){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;

	global $wnum_art;
	global $warr_art;

	$array_datos = array('wnum_art'=>'','warr_art'=>'','wunidad_completa'=>'','html_med'=>'');
	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	if($wtip=='NoApl')
	{
	 $q = " SELECT spaart, spauen-spausa, id, spacco "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),3) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	else
	{
		// 2008-03-13
		$q = " SELECT Eyrnum, Fecha_data, Hora_data "
		. "   FROM " . $wbasedato . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' "
		. "  ORDER BY 2 desc, 3 desc";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " SELECT Detart, sum(Detcan) "
		. "   FROM " . $wbasedato . "_000019 "
		. "  WHERE detnum                        = '" . $row[0]  . "'"
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$texto_tipo = ($tipo_mov == 'Ent') ? "entregados" : "recibidos";

	$class3 = "class='fila".((($i+1)%2)+1)."'";
	
	if( !empty( $nombre_pac ) ){
		$html .= "<table width=100%>";
		$html .= "<tr $class3 align='center'>";
		$html .= "<td style='font-size:14pt'><b>Medicamentos asociados al paciente que serán $texto_tipo a la habitación $hab_asignada</b></td>";
		$html .= "</tr>";
		$html .= "<tr class='fila2'>";
		$html .= "<td>";
		$html .= "<b>".$whis." - ".$wing;
		$html .= "<br>".$nombre_pac."(".$cod_hab.")";
		$html .= "</td>";
		$html .= "<tr>";
		$html .= "</table>";
		$html .= "<br><br><br>";
	}
	

	if ($num >= 1)
	{
		$html .= "<table border=0>";
		
		$wnum_art = $num;

		$array_datos['wnum_art'] = $wnum_art;

		$html .= "<input type='HIDDEN' name='wnum_art' value='" . $wnum_art . "'>";
		$html .= "<tr class=encabezadoTabla>";
		$html .= "<td>Grabado desde</font></td>";
		$html .= "<td>Articulo</font></td>";
		$html .= "<td>Descripción</font></td>";
		$html .= "<td>Presentación</font></td>";
		$html .= "<td title='Cantidades pendientes de aplicar o cantidad a trasladar'>Cantidad</font></td>";
		$html .= "</tr>";

		$wunidad_completa = array();

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);
			$cajon_descarte = "";
			$color_cajon = "";
			$id = $row['id'];

			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			    $wclass = "fila2";

			$validar_unidad = validar_unidad($row[0]);

			buscar_articulo($row[0]);

			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente.
			// if(floor($row[1]/$validar_unidad) >= 1){

				// array_push($wunidad_completa,$row[0]); // Agrego los articulos con unidad completa al arreglo.

				// $color_cajon = "style='background-color:red'";

			// }
			// elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){

				// $cajon_descarte = "<input type=checkbox id='check_descarte' onclick='descartar(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$row[0]."\",\"".$row[1]."\",\"".$whis."\",\"".$wing."\",\"".$ccoOrigen."\",\"".$id."\", this)'>";
			// }

			$html .= "<tr class=".$wclass.">";
			$html .= "<td align=center>".$row['spacco']."</td>";
			$html .= "<td>".$row['spaart']."</td>";
			$html .= "<td>".$wartnom."</td>";
			$html .= "<td>".$wunides."</td>";
			$html .= "<td align=center>".$row[1]."</td>";

			$html .= "</tr>";

			$warr_art[$i][0] = $row[0];
			$warr_art[$i][1] = $row[1];

			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";

			$array_datos['warr_art'] = $warr_art;

		}

		$html .= "</table>";
	}


	$warreglo_articulos = base64_encode(serialize($array_datos));

	if( !empty( $tipo_mov ) ){
		
		if($tipo_mov == 'Ent'){
			$html .= "<br><INPUT TYPE='button' value='Entregar' onclick='moverPacHosp(\"".$wemp_pmla."\", \"".$whis."\",\"".$wing."\",\"".$nombre_pac."\",\"".$ccoOrigen."\", \"".$wccohos."\", \"".$id_solicitud_cama."\", \"".$wccoapl."\", \"".$hab_asignada."\", \"".$whab_actual."\", \"".$wser_destino."\", \"".$tipo_mov."\", \"".$wnum_art."\", \"".$warreglo_articulos."\")' style='width:100'>";
		}else{
			$html .= "<br><INPUT TYPE='button' value='Recibir' onclick='moverPacHosp(\"".$wemp_pmla."\", \"".$whis."\",\"".$wing."\",\"".$nombre_pac."\",\"".$wservicio_anterior."\", \"".$wccohos."\", \"".$id_solicitud_cama."\", \"".$wccoapl."\", \"".$hab_asignada."\", \"".$whab_actual."\", \"".$wser_destino."\", \"".$tipo_mov."\", \"".$wnum_art."\", \"".$warreglo_articulos."\")' style='width:100'>";
		}
		
		$html .= "<br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>";
	}

	$array_datos['html'] = $html;
	return $array_datos;
}

/**********************************************************************
* Indica si un articulo es una Dosis adaptada
**********************************************************************/
function esDosisAdaptada( $art ){


		global $wcenmez;
		global $conex;

		$val = false;

		$sql = "  SELECT * FROM {$wcenmez}_000002 a, {$wcenmez}_000001 b
				   WHERE tippro = 'on'
					 AND tipcdo = 'off'
					 AND tipnco = 'on'
					 AND Tipqui != 'on'
					 AND tiptpr LIKE 'D%'
					 AND artcod = '$art'
					 AND artest = 'on'
					 AND arttip = tipcod
				 	 AND tipest = 'on'";
		$res = mysql_query( $sql, $conex );

		if( $rows = mysql_fetch_array($res) ){
			$val = true;
		}

		return $val;
}

//Funcion que devuelve una lista de medicamntos pendientes de aplicar.
function Detalle_ent_rec($wtip, $whis, $wing, $nombre_pac, $cod_hab, $ccoOrigen, $hab_destino, $id_solicitud, $control_cco, $control_origen_destino){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;

	global $wnum_art;
	global $warr_art;

	$codCcoUrg = consultarCcoUrgencias();
	$codCcoCirugia = consultarCcoCirugia();

	$array_datos = array('wnum_art'=>'','warr_art'=>'','wunidad_completa'=>'','html_med'=>'');
	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	if($wtip=='NoApl')
	{
	 $q = " SELECT spaart, spauen-spausa, id, spacco "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),3) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	else
	{
		// 2008-03-13
		$q = " SELECT Eyrnum, Fecha_data, Hora_data "
		. "   FROM " . $wbasedato . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' "
		. "  ORDER BY 2 desc, 3 desc";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " SELECT Detart, sum(Detcan) "
		. "   FROM " . $wbasedato . "_000019 "
		. "  WHERE detnum                        = '" . $row[0]  . "'"
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$html .= "<table width=100%>";
	$class3 = "class='fila".((($i+1)%2)+1)."'";
	$html .= "<tr $class3 align='center'>";
	$html .= "<td style='font-size:14pt'><b>Medicamentos pendiente por aplicar, descartar o devolver. </b></td>";
	$html .= "</tr>";

	$html .= "<tr class='fila2'>";
	$html .= "<td>";
	$html .= "<b>".$whis." - ".$wing;
	$html .= "<br>".$nombre_pac."(".$cod_hab.")";
	$html .= "</td>";
	$html .= "<tr>";
	$html .= "</table>";
	$html .= "<br><br><br>";

	$html .= "<table border=0>";

	if ($num >= 1)
	{
		$wnum_art = $num;

		$array_datos['wnum_art'] = $wnum_art;

		$html .= "<input type='HIDDEN' name='wnum_art' value='" . $wnum_art . "'>";
		$html .= "<tr class=encabezadoTabla>";
		$html .= "<td>Pendiente</font></td>";
		$html .= "<td>Grabado desde</font></td>";
		$html .= "<td>Articulo</font></td>";
		$html .= "<td>Descripción</font></td>";
		$html .= "<td>Presentación</font></td>";
		$html .= "<td title='Cantidades pendientes de aplicar o cantidad a trasladar'>Cantidad</font></td>";
		$html .= "<td>Descartar</font></td>";
		$html .= "</tr>";

		$wunidad_completa = array();

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);
			$cajon_descarte = "";
			$color_cajon = "";
			$texto_cajon = "Descartar";
			$id = $row['id'];

			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			    $wclass = "fila2";

			$validar_unidad = validar_unidad($row[0]);

			buscar_articulo($row[0]);

			//Valida si el articulo es dosis adaptada, en ese caso pondra en mensaje de devolder.
			if(esDosisAdaptada( $row[0] )){
				$texto_cajon = "Devolver";
			}

			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente, ademas valida si el
			//articulo es dosis adapatada, si lo es permitira el traslado del paciente asi el saldo de esa dosis sea mayor a 1.
			if(floor($row[1]/$validar_unidad) >= 1 and !esDosisAdaptada( $row[0] )){

				array_push($wunidad_completa,$row[0]); // Agrego los articulos con unidad completa al arreglo.

				$color_cajon = "style='background-color:red;'";
				$texto_cajon = "Devolver";

			}
			elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){

				$cajon_descarte = "<input type=checkbox id='check_descarte' onclick='descartar(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$row[0]."\",\"".$row[1]."\",\"".$whis."\",\"".$wing."\",\"".$ccoOrigen."\",\"".$id."\", this)'>";
			}

			$html .= "<tr class=".$wclass.">";
			$html .= "<td $color_cajon align=center >".$texto_cajon."</td>";
			$html .= "<td align=center>".$row['spacco']."</td>";
			$html .= "<td>".$row['spaart']."</td>";
			$html .= "<td>".$wartnom."</td>";
			$html .= "<td>".$wunides."</td>";
			$html .= "<td align=center>".round(($row[1]/$validar_unidad),3)."</td>";
			$html .= "<td align=center>".$cajon_descarte."</td>";
			$html .= "</tr>";

			$warr_art[$i][0] = $row[0];
			$warr_art[$i][1] = $row[1];

			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";

			$array_datos['warr_art'] = $warr_art;
			$array_datos['wunidad_completa'] = $wunidad_completa;

		}


	}

	$html .= "</table>";
	//Si tiene almenos un articulo con unidad completa no mostrara el boton de entregar.
	if(count($wunidad_completa) == 0 and trim($hab_destino) != ''){
		//Caso en el paciente sera traslado de urgencias a piso con medicamentos por descartar.
		if($codCcoUrg == $control_cco){
			$html .= "<br>El paciente se puede entregar a la habitación <font size=4><b>$hab_destino</b></font><br>";
			$html .= "<br><INPUT TYPE='button' value='Entregar' onclick='EntregarUrgAPiso(\"".$whis."\",\"".$wing."\",\"".$nombre_pac."\", \"".$hab_destino."\", \"".$id_solicitud."\", \"on\");' style='width:100'>";
		}else{
			//Caso en el que el paciente este en cirugia con medicamentos por descartar pero que no tiene habitacion asignada.
			if($ccoOrigen != $control_cco){
					if($control_origen_destino !=  'EntregaDesdeCirugiaAPiso'){
						$html .= "<br>El paciente se puede entregar a <font size=4><b>Cirugia</b></font><br>";
						$html .= "<br><INPUT TYPE='button' value='Entregar' onclick='EntPacACir(\"".$whis."\",\"".$wing."\",\"".$nombre_pac."\", \"on\");' style='width:100'>";
					}
			}else{
				if($control_origen_destino ==  'EntregaDesdeCirugiaAPiso'){
					//Caso en el que el paciente sera trasladado desde cirugia a piso pero con medicamentos por descartar.
					$html .= "<br>El paciente se puede entregar a la habitación <font size=4><b>$hab_destino</b></font><br>";
					$html .= "<br><INPUT TYPE='button' value='Entregar' onclick='EntregaDesdeCirugiaAPiso(\"".$whis."\",\"".$wing."\",\"".$nombre_pac."\", \"".$hab_destino."\", \"".$id_solicitud."\", \"on\");' style='width:100'>";
				}
			}
		}
	}

	$html .= "<br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>";
	$html .= "<br><br><br><div align=justify style='font-size:10px;color:#FF0000;width:900px; '>
				<b><u>Condiciones traslado articulos</u></b><br><br>
				1. Los pacientes que en el momento del traslado tienen medicamentos para descartar se debe tener en cuenta si son multidosis como: inhaladores, jeringas de insulinas, jarabes en frascos,  para que estos sean traslados con el paciente y <u><b>no generar el descarte</b></u>, en el caso que se requiera descartar se da clic en el botón correspondiente y así no se trasladara el saldo al servicio destino.<br><br>
				2. El programa sólo permite realizar traslados a los  pacientes que no tengan saldos de medicamentos e insumos en cantidades enteras, <b>EXCEPTO SI TIENE DOSIS ADAPTADA</b>, las cuales son las únicas que se permitirá trasladar en cantidades enteras.<br>
				<br><br></div>";
	$array_datos['html'] = $html;
	return $array_datos;
}


//Funcion que devuelve una lista de medicamntos pendientes de aplicar.
function Detalle_ent_rec_saldos_ayuda_dx( $whis, $wing, $nombre_pac, $cod_hab, $ccoOrigen ){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;

	global $wnum_art;
	global $warr_art;

	$array_datos = array('wnum_art'=>'','warr_art'=>'','wunidad_completa'=>'','html_med'=>'');
	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	$q = " SELECT spaart, spauen-spausa, id, spacco "
	   . "   FROM " . $wbasedato . "_000004 "
	   . "  WHERE spahis                            = '" . $whis . "'"
	   . "    AND spaing                            = '" . $wing . "'"
	   . "    AND ROUND((spauen-spausa),3) > 0 "
	   . "    AND spacco = '".$ccoOrigen."' "
	   . "  ORDER BY 1 ";


	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$html .= "<table width=100%>";
	$class3 = "class='fila".((($i+1)%2)+1)."'";
	$html .= "<tr $class3 align='center'>";
	$html .= "<td style='font-size:14pt'><b>Medicamentos pendiente por aplicar, descartar o devolver. </b></td>";
	$html .= "</tr>";

	$html .= "<tr class='fila2'>";
	$html .= "<td>";
	$html .= "<b>".$whis." - ".$wing;
	$html .= "<br>".$nombre_pac."(".$cod_hab.")";
	$html .= "</td>";
	$html .= "<tr>";
	$html .= "</table>";
	$html .= "<br><br><br>";

	$html .= "<table border=0>";

	if ($num >= 1)
	{
		$wnum_art = $num;

		$array_datos['wnum_art'] = $wnum_art;

		$html .= "<input type='HIDDEN' name='wnum_art' value='" . $wnum_art . "'>";
		$html .= "<tr class=encabezadoTabla>";
		$html .= "<td>Pendiente</font></td>";
		$html .= "<td>Grabado desde</font></td>";
		$html .= "<td>Articulo</font></td>";
		$html .= "<td>Descripción</font></td>";
		$html .= "<td>Presentación</font></td>";
		$html .= "<td title='Cantidades pendientes de aplicar o cantidad a trasladar'>Cantidad</font></td>";
		$html .= "<td>Descartar</font></td>";
		$html .= "</tr>";

		$wunidad_completa = array();

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);
			$cajon_descarte = "";
			$color_cajon = "";
			$id = $row['id'];

			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			    $wclass = "fila2";

			$validar_unidad = validar_unidad($row[0]);

			buscar_articulo($row[0]);

			$texto_cajon = "Descartar";

			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente.
			if(floor($row[1]/$validar_unidad) >= 1){

				array_push($wunidad_completa,$row[0]); // Agrego los articulos con unidad completa al arreglo.

				$color_cajon = "style='background-color:red;'";
				$texto_cajon = "Devolver";

			}
			elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){

				$cajon_descarte = "<input type=checkbox id='check_descarte' onclick='descartar(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$row[0]."\",\"".$row[1]."\",\"".$whis."\",\"".$wing."\",\"".$ccoOrigen."\",\"".$id."\", this)'>";
			}

			$html .= "<tr class=".$wclass.">";
			$html .= "<td $color_cajon align=center>".$texto_cajon."</td>";
			$html .= "<td align=center>".$row['spacco']."</td>";
			$html .= "<td>".$row['spaart']."</td>";
			$html .= "<td>".$wartnom."</td>";
			$html .= "<td>".$wunides."</td>";
			$html .= "<td align=center>".($row[1]/$validar_unidad)."</td>";
			$html .= "<td align=center>".$cajon_descarte."</td>";
			$html .= "</tr>";

			$warr_art[$i][0] = $row[0];
			$warr_art[$i][1] = $row[1];

			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";

			$array_datos['warr_art'] = $warr_art;
			$array_datos['wunidad_completa'] = $wunidad_completa;

		}


	}

	$html .= "</table>";

	$html .= "<br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>";

	$array_datos['html'] = $html;
	return $array_datos;
}

//Funcion que devuelve una lista de medicamntos pendientes de aplicar.
function Detalle_ent_rec_saldos($wtip, $whis, $wing, $nombre_pac, $cod_hab, $ccoOrigen, $hab_destino, $id_solicitud, $control_cco, $control_origen_destino){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	global $wartnom;
	global $wartuni;
	global $wunides;

	global $wnum_art;
	global $warr_art;

	$codCcoUrg = consultarCcoUrgencias();
	$codCcoCirugia = consultarCcoCirugia();

	$array_datos = array('wnum_art'=>'','warr_art'=>'','wunidad_completa'=>'','html_med'=>'');
	// ================================================================================================
	// Aca traigo los articulos del paciente que tienen saldo, osea que falta Aplicarselos

	if($wtip=='NoApl')
	{
	 $q = " SELECT spaart, spauen-spausa, id, spacco "
		. "   FROM " . $wbasedato . "_000004 "
		. "  WHERE spahis                            = '" . $whis . "'"
		. "    AND spaing                            = '" . $wing . "'"
		. "    AND ROUND((spauen-spausa),3) > 0 "
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	else
	{
		// 2008-03-13
		$q = " SELECT Eyrnum, Fecha_data, Hora_data "
		. "   FROM " . $wbasedato . "_000017 "
		. "  WHERE eyrhis                            = '" . $whis . "'"
		. "    AND eyring                            = '" . $wing . "'"
		. "    AND eyrtip= 'Entrega' "
		. "  ORDER BY 2 desc, 3 desc";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$q = " SELECT Detart, sum(Detcan) "
		. "   FROM " . $wbasedato . "_000019 "
		. "  WHERE detnum                        = '" . $row[0]  . "'"
		. "  GROUP BY 1 "
		. "  ORDER BY 1 ";

	}
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$html .= "<table width=100%>";
	$class3 = "class='fila".((($i+1)%2)+1)."'";
	$html .= "<tr $class3 align='center'>";
	$html .= "<td style='font-size:14pt'><b>Medicamentos pendiente por aplicar, descartar o devolver. </b></td>";
	$html .= "</tr>";

	$html .= "<tr class='fila2'>";
	$html .= "<td>";
	$html .= "<b>".$whis." - ".$wing;
	$html .= "<br>".$nombre_pac."(".$cod_hab.")";
	$html .= "</td>";
	$html .= "<tr>";
	$html .= "</table>";
	$html .= "<br><br><br>";

	$html .= "<table border=0>";

	if ($num >= 1)
	{
		$wnum_art = $num;

		$array_datos['wnum_art'] = $wnum_art;

		$html .= "<input type='HIDDEN' name='wnum_art' value='" . $wnum_art . "'>";
		$html .= "<tr class=encabezadoTabla>";
		$html .= "<td>Pendiente</font></td>";
		$html .= "<td>Grabado desde</font></td>";
		$html .= "<td>Articulo</font></td>";
		$html .= "<td>Descripción</font></td>";
		$html .= "<td>Presentación</font></td>";
		$html .= "<td title='Cantidades pendientes de aplicar o cantidad a trasladar'>Cantidad</font></td>";
		$html .= "<td>Descartar</font></td>";
		$html .= "</tr>";

		$wunidad_completa = array();

		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);
			$cajon_descarte = "";
			$color_cajon = "";
			$id = $row['id'];

			if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			    $wclass = "fila2";

			$validar_unidad = validar_unidad($row[0]);

			buscar_articulo($row[0]);

			$texto_cajon = "Descartar";

			//Validar si el saldo actual tiene unidades completas, en ese caso no permite la entrega del paciente.
			if(floor($row[1]/$validar_unidad) >= 1){

				array_push($wunidad_completa,$row[0]); // Agrego los articulos con unidad completa al arreglo.

				$color_cajon = "style='background-color:red;'";
				$texto_cajon = "Devolver";

			}
			elseif( ($row[1]/$validar_unidad - floor($row[1]/$validar_unidad)) > 0){

				$cajon_descarte = "<input type=checkbox id='check_descarte' onclick='descartar(\"".$wemp_pmla."\",\"".$wbasedato."\",\"".$row[0]."\",\"".$row[1]."\",\"".$whis."\",\"".$wing."\",\"".$ccoOrigen."\",\"".$id."\", this)'>";
			}

			$html .= "<tr class=".$wclass.">";
			$html .= "<td $color_cajon align=center>".$texto_cajon."</td>";
			$html .= "<td align=center>".$row['spacco']."</td>";
			$html .= "<td>".$row['spaart']."</td>";
			$html .= "<td>".$wartnom."</td>";
			$html .= "<td>".$wunides."</td>";
			$html .= "<td align=center>".($row[1]/$validar_unidad)."</td>";
			$html .= "<td align=center>".$cajon_descarte."</td>";
			$html .= "</tr>";

			$warr_art[$i][0] = $row[0];
			$warr_art[$i][1] = $row[1];

			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][0]' value='" . $warr_art[$i][0] . "'>";
			$html .= "<input type='HIDDEN' name='warr_art[" . $i . "][1]' value='" . $warr_art[$i][1] . "'>";

			$array_datos['warr_art'] = $warr_art;
			$array_datos['wunidad_completa'] = $wunidad_completa;

		}


	}

	$html .= "</table>";

	$html .= "<br><br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>";

	$array_datos['html'] = $html;
	return $array_datos;
}

//Modifica la ubicacion del paciente que sera trasladado desde cirugia a piso.
 function modificarUbicacionPaciente($whistoria, $wingreso, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino) {

	global $wbasedato;
	global $conex;

	$q = "UPDATE ".$wbasedato."_000018
		     SET Ubisac = '".$servicioDestino."',
				 Ubihac = '".$habitacionDestino."',
				 Ubisan = '".$servicioOrigen."',
				 Ubihan = '".$habitacionOrigen."',
				 Ubiptr = 'on'
		   WHERE Ubihis = '".$whistoria."'
			 AND Ubiing = '".$wingreso."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());

	//Registro en la tabla 20
	$q = "UPDATE ".$wbasedato."_000020
		     SET Habhis = '',
				 Habing = '',
				 Habdis = 'on',
				 Habpro = 'off'
		   WHERE habhis = '".$whistoria."'
		     AND habing = '".$wingreso."' ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());//Registro en la tabla 20


	$q = "UPDATE ".$wbasedato."_000020
		     SET Habhis = '".$whistoria."',
				 Habing = '".$wingreso."',
				 Habdis = 'off',
				 Habali = 'off',
				 Habpro = 'off'
		   WHERE habcod = '".$habitacionDestino."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());



}


// Funcion que crea un arreglo con todas las habitacoin y se le agrega el nombre del centro de costos.
function todaslashab(){


	global $wbasedato;
	global $conex;

	//Consulto todas las habitaciones
	$q_hab = "SELECT *
			FROM ".$wbasedato."_000020 ";
	$res_hab = mysql_query($q_hab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab . " - " . mysql_error());

	//Consulto todos los centros de costo
	$q_cco = "SELECT *
			FROM ".$wbasedato."_000011 ";
	$res_cco = mysql_query($q_cco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_cco . " - " . mysql_error());

	$cco = array();

	//Creo un arreglo con la informacion de los centros de costo.
	while($fila_cco = mysql_fetch_array($res_cco)){

		if(!array_key_exists($fila_cco['Ccocod'], $cco)){

			$cco[$fila_cco['Ccocod']] = $fila_cco;
		}

	}

	$hab = array();
	//Creo un arreglo con la informacion de las habitacion y le agrego el nombre del centro de costos al que pertenece.
	while($fila_hab = mysql_fetch_array($res_hab)){

		if(!array_key_exists($fila_hab['Habcod'], $hab)){

			$cod_hab = trim($fila_hab['Habcod']);
			$hab[$cod_hab] = $fila_hab;
			$hab[$cod_hab]['nombre_cco'] = $cco[$fila_hab['Habcco']]['Cconom'];

		}

	}

	return $hab;
}


//Permite registrar el movimiento de traslado de un paciente de cirugia a piso (entrega)
function registrarEntregaPac($whistoria,$wingreso,$wusuario,$ccorigen,$ccodestino, $hab_destino, $wid){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wfecha = date('Y-m-d');
	$whora = date('H:i:s');

	$q = " UPDATE " . $wbasedato . "_000001 "
	   . "    SET connum=connum + 1 "
	   . "  WHERE contip='entyrec' ";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

	$q = " SELECT connum "
		. "  FROM " . $wbasedato . "_000001 "
		. " WHERE contip='entyrec' ";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	$row = mysql_fetch_array($err);
	$wconsec = $row[0];

	// Aca grabo el encabezado del recibo
	$q = " INSERT INTO " . $wbasedato . "_000017 (       Medico       ,   Fecha_data,   Hora_data,               Eyrnum     ,       Eyrhis  ,             Eyring  ,           Eyrsor  ,           Eyrsde         ,   Eyrtip   ,       Eyrhde     , Eyrest,   Eyrids  ,   Seguridad     ) "
		."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whistoria . "','" . $wingreso . "',    '".$ccorigen."','" . $ccodestino . "',    'Entrega', '".$hab_destino."',  'on' , '".$wid."','C-" . $wusuario . "')";
	$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());

}

function consultarHabitacion($conex,$cdHabitacion){

	global $wbasedato;

	$q = "SELECT Habcod,Habcco,Habhis,Habing,Habdis,Habest
			FROM ".$wbasedato."_000020
		   WHERE Habcod = '".$cdHabitacion."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$fila = mysql_fetch_array($res);

		$info = new habitacionDTO();

		$info->codigo = $fila['Habcod'];
		$info->disponible = $fila['Habdis'];
		$info->historiaClinica = $fila['Habhis'];
		$info->ingresoHistoriaClinica = $fila['Habing'];
		$info->servicio = $fila['Habcco'];
		$info->estado = $fila['Habest'];
	}
	return $info;
}

//Informacion de la habitacion asignada a un paciente de cirugia que sera hospitalizado.
function consultarHabitacionAsignada($whistoria,$wingreso){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;
	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

	//Consulta la ultima habitacion asignada al paciente en el programa de asignacion de camas.
	$q_hab = "SELECT Hab_asignada, id
				FROM ".$wcencam."_000003
			   WHERE Historia = '$whistoria'
			     AND central = '$wcentral_camas'
				 AND Fecha_cumplimiento =  '0000-00-00'
			     AND Hora_cumplimiento =  '00:00:00'
				 AND Anulada = 'No'
			ORDER BY id DESC LIMIT 1";
	$res_hab = mysql_query($q_hab, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_hab . " - " . mysql_error());
	$row = mysql_fetch_array($res_hab);
	$hab_asig = $row['Hab_asignada'];

	$info_hab = todaslashab();

	$info = new movimientoHospitalarioDTO();

	$info->historia = $whistoria;
	$info->ingreso = $wingreso;
	$info->servicioDestino = $info_hab[$hab_asig]['Habcco'];
	$info->habitacionOrigen = '';
	$info->habitacionDestino = $hab_asig;
	$info->enProcesoTraslado = 'on';
	$info->tipoMovimiento = '';
	$info->id_solicitud = $row['id'];

	return $info;

}

function consultartipohab($conex, $whab, $wbasedato){

	$q = " SELECT Habtfa "
		."   FROM ".$wbasedato."_000020 "
		."  WHERE habcod = '".$whab."'";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	return $row['Habtfa'];

}



function entregarArticulos($whis, $wing, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino, &$wnum_art, $warr_art){

	global $wbasedato;
	global $conex;

	$dato_user = explode("-",$_SESSION['user']);
	$wuser = $dato_user[1];
	$warr_art = unserialize(base64_decode($warr_art));

	$wfecha = date('Y-m-d');
	$whora = date('H:i:s');

	$wtipo_hab_fac_e = consultartipohab($conex, $habitacionOrigen, $wbasedato);
	$wtipo_hab_fac_r = consultartipohab($conex, $habitacionDestino ,$wbasedato);

	$q = " SELECT Eyrnum "
		. "  FROM ".$wbasedato."_000017 "
		. " WHERE Eyrtip = 'Entrega' "
		. "	  AND Eyrsor = '".$servicioOrigen."'"
		. "	  AND Eyrhde = '".$habitacionDestino."'"
		. "   AND Eyrhis = '".$whis."'"
		. "   AND Eyring = '".$wing."'"
		. "	  AND Eyrest = 'on' ";
	$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
	$row = mysql_fetch_array($err);
	$wconsec = $row[0];


if (isset($wnum_art))
{

	$wcan_art = $wnum_art;

	//averiguo que clase de centro de costos es el destino
	// Traigo el INDICADOR de si el centro de costo es hospitalario o No
	$q = " SELECT ccoapl "
		."   FROM " . $wbasedato . "_000011 "
		."  WHERE ccocod = '".$servicioDestino."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	{
		$row = mysql_fetch_array($res);
		if ($row[0] == "on")
		$wdesapl= "on";
		else
		$wdesapl = "off";
	}
	else
	{
		$wdesapl = "off";
	}


	//Averiguo que clase de centro de costos es el destino
	//Traigo el INDICADOR de si el centro de costo es hospitalario o No
	$q = " SELECT ccoapl "
		."   FROM ".$wbasedato."_000011 "
		."  WHERE ccocod = '".$servicioOrigen."'";
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);
	if ($num > 0)
	   {
		$row = mysql_fetch_array($res);
		if ($row[0] == "on")
		   $woriapl= "on";
		  else
			$woriapl = "off";
	   }
	  else
		 {
		  $woriapl = "off";
		 }

	if(count($warr_art) == 0 or !isset($warr_art)){
		$warr_art = array();
	}

	if(is_array($warr_art)){

		foreach ($warr_art as $key => $value)
		{
			$q = " INSERT INTO ".$wbasedato."_000019 (   Medico       ,   Fecha_data,   Hora_data,   Detnum     ,   Detart             ,  Detcan            , Detest, Seguridad     ) "
				."                            VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wconsec."','".$value[0]."',".$value[1].", 'on'  , 'C-".$wuser."')";
			$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

			$wctr = $warr_art[$i][1];     //Cantidad a trasladar
			$wronda = (string)date("H");
			buscar_articulo($value[0]);

			if($wdesapl=='off' and $woriapl=='on')
				{
					// =========================================================================================================================================
					// Aca hago el traslado de los saldos de la tabla 000030 a la 000004, si el centro de costo aplica automaticamente cuando se factura. Ej:UCI
					// =========================================================================================================================================
					$q = " SELECT spluen, splusa, splaen, splasa, splcco, Ccopap "
						."   FROM ".$wbasedato."_000030, ".$wbasedato."_000011 "
						."  WHERE splhis = '".$whis."'"
						."    AND spling = '".$wing."'"
						."    AND splart = '".$value[0]."'"
						."    AND (splcco = '".$servicioOrigen."'"      //2 de Mayo de 2008
						."     OR  splcco = ccocod "                 //2 de Mayo de 2008
						."    AND  ccotra = 'on') "                  //2 de Mayo de 2008
						."    Order by 6";
					$rest = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
					$num = mysql_num_rows($rest);

					for ($j = 1;$j <= $num;$j++)
					{
						$row = mysql_fetch_array($rest);

						$wuen = $row['spluen']; //Unix entradas
						$wusa = $row['splusa']; //Unix salidas
						$waen = $row['splaen']; //Aprovechamientos entradas
						$wasa = $row['splasa']; //Aprovechamientos salidas
						$wscc = $row['splcco']; //centro de costos que grabo

						if(($wuen-$wusa)>0)
						{
							if(($wuen-$wusa)<$wctr)
							  {
								$wcta=$wuen-$wusa;
								$wctr=$wctr-$wcta;
							  }
							 else
							   {
								$wcta=$wctr;
								$wctr=0;
							   }

							if($wctr<=0)
							  {
								$j=$num+1;
								$j=$num+1;
							  }

							if($wcta != ''){

								if (($wuen-$wusa-$waen+$wasa) >= $wcta) // La cantidad en la 000030 es mayor a lo que se va a trasladar
								  {
									$q=  " SELECT id "
										."   FROM ".$wbasedato."_000004 "
										."  WHERE Spahis = '".$whis."' "
										."    AND Spaing = '".$wing."' "
										."    AND Spacco = '".$wscc."' "
										."    AND Spaart = '".$value[0]."' ";
									$errs = mysql_query($q,$conex);
									$nums = mysql_num_rows($errs);

									if ($nums > 0)
									   {
										$q = " UPDATE ".$wbasedato."_000004 "
											."    SET spauen = spauen+ ".$wcta
											."  WHERE spahis = '".$whis."'"
											."    AND spaing = '".$wing."'"
											."    AND spacco = '".$wscc."'"
											."    AND spaart = '".$value[0]."'";
										$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
									   }
									  else
										{
										 $q=  " INSERT INTO ".$wbasedato."_000004 (   medico       ,    Fecha_data      ,    Hora_data       ,    Spahis  ,    Spaing ,    Spacco  ,    Spaart              ,   Spauen , Spausa, Spaaen, Spaasa, Seguridad     ) "
											 ."                            VALUES ('".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$value[0]."', ".$wcta.", 0     , 0     , 0     , 'A-".$wuser."') ";

										 $res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										}

									$q = " UPDATE ".$wbasedato."_000030 "
										."    SET spluen = spluen-".$wcta
										."  WHERE splhis = '".$whis."'"
										."    AND spling = '".$wing."'"
										."    AND splcco = '".$wscc."'"
										."    AND splart = '".$value[0]. "'";
									$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									AnularAplicacion($whis, $wing, $wcco, strtoupper($value[0]), $wartnom, $wcta, $wuser, 'off');

								  }

								if (($wuen - $wusa - $waen) < $wcta) // La cantidad en la 000030 es menor a lo que se va a trasladar
								{
									$q= " SELECT id "
									."      FROM ".$wbasedato."_000004 "
									."     WHERE Spahis	= '".$whis."' "
									."       AND Spaing	= '".$wing."' "
									."       AND Spacco	= '".$wscc."' "
									."       AND Spaart	= '".$value[0]."' ";
									$errs = mysql_query($q,$conex);
									$nums = mysql_num_rows($errs);

									if ($nums > 0)
									  {
										$q = " UPDATE ".$wbasedato."_000004 "
											."    SET spauen = spauen+".$wcta.", "
											."        spaaen = spaaen+".($wcta - ($wuen - $wusa - $waen+ $wasa))
											."  WHERE spahis = '".$whis."'"
											."    AND spaing = '".$wing."'"
											."    AND spacco = '".$wscc."'"
											."    AND spaart = '".$value[0]."'";
										$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									  }
									else
									   {
										$q=  " INSERT INTO ".$wbasedato."_000004 (    medico,          Fecha_data,           Hora_data,            Spahis,          Spaing,             Spacco,            Spaart,   Spauen,   Spausa,   Spaaen,  Spaasa,         Seguridad) "
											."                            VALUES ( '".$wbasedato."', '".date('Y-m-d')."', '".date('H:i:s')."', '".$whis."',  ".$wing.", '".$wscc."', '".$value[0]."', ".$wcta.", 0, ".($wcta - ($wuen - $wusa - $waen + $wasa)).", 0, 'A-".$wuser."')";

										$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
									   }


									$q = " UPDATE ".$wbasedato."_000030 "
										."    SET spluen = spluen-".$wcta.", "
										."        splaen = splaen-".($wcta - ($wuen - $wusa - $waen + $wasa))
										."  WHERE splhis = '".$whis."'"
										."    AND spling = '".$wing."'"
										."    AND splcco = '".$wscc."'"
										."    AND splart = '".$value[0]. "'";
									$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

									if (($wuen - $wusa - $waen + $wasa)>0)
									{
										AnularAplicacion($whis, $wing, $wcco, strtoupper($value[0]), $wartnom, ($wuen - $wusa - $waen + $wasa), $wuser, 'off');
									}

									if (($wcta - ($wuen - $wusa - $waen + $wasa))>0)
									{
										AnularAplicacion($whis, $wing, $wcco, strtoupper($value[0]), $wartnom, ($wcta - ($wuen - $wusa - $waen + $wasa)), $wuser, 'on');
									}
								}
							}
						}
					}
				}
			}
		}
	}	// Fin grabación de detalle


}


function modificarUsuarioMovimientoHospitalario($whistoria,$wingreso,$wusuario,$cco_origen, $ccodest_paciente){

	global $wbasedato;
	global $conex;

	$wfecha = date('Y-m-d');
	$whora = date('H:i:s');
	$codCcoCirugia = consultarCcoCirugia();
	$ccoOrigenUrg = consultarCcoUrgencias();

	$q = "UPDATE ".$wbasedato."_000017
		     SET Seguridad = 'C-".$wusuario."'
		   WHERE Eyrhis = '".$whistoria."'
			 AND Eyring = '".$wingreso."'
			 AND Eyrest = 'on'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	//Este caso es solo para los pacientes de urgencias que seran trasladados a cirugia
	//if($codCcoCirugia == $ccodest_paciente){

		$q = " UPDATE " . $wbasedato . "_000001 "
		   . "    SET connum=connum + 1 "
		   . "  WHERE contip='entyrec' ";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

		$q = " SELECT connum "
			. "  FROM " . $wbasedato . "_000001 "
			. " WHERE contip='entyrec' ";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
		$row = mysql_fetch_array($err);
		$wconsec = $row[0];

		// Aca grabo el encabezado del recibo
		$q = " INSERT INTO " . $wbasedato . "_000017 (       Medico       ,   Fecha_data,   Hora_data,               Eyrnum     ,       Eyrhis  ,             Eyring  ,           Eyrsor  ,           Eyrsde         ,   Eyrtip   , Eyrest, Seguridad     ) "
			."                                VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $whora . "','" . $wconsec . "','" . $whistoria . "','" . $wingreso . "','".$ccodest_paciente."','" . $ccodest_paciente . "','Recibo', 'on', 'C-" . $wusuario . "')";
		$err = mysql_query($q, $conex) or die (mysql_errno().$q." - ".mysql_error());

		//Se actualiza el proceso de traslado.
		$q = "UPDATE ".$wbasedato."_000018
				 SET ubiptr = 'off'
			   WHERE ubihis = '".$whistoria."'
				 AND ubiing = '".$wingreso."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

		//Se desocupa la habitacion actual del paciente y quedara disponible.
		$q = "UPDATE ".$wbasedato."_000020
				 SET habhis = '', habing='', habdis='on'
			   WHERE habhis = '".$whistoria."'
				 AND habing = '".$wingreso."'";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_affected_rows();//Se actualiza el proceso de traslado.

		//================================== ESTADISTICA DE INGRESO DEL PACIENTE =========================
		// Si el paciente a estado antes en el servicio para el mismo ingreso, traigo cuantas veces para sumarle una
		$q_32 = " SELECT COUNT(*) "
			   ."   FROM ".$wbasedato."_000032 "
			   ."  WHERE Historia_clinica = '".$whistoria."'"
			   ."    AND Num_ingreso      = '".$wingreso."'"
			   ."    AND Servicio         = '".$codCcoCirugia."'";
		$err_32 = mysql_query($q_32, $conex) or die (mysql_errno().$q_32." - ".mysql_error());
		$row_32 = mysql_fetch_array($err_32);

		$wingser = $row_32[0] + 1; //Sumo un ingreso a lo que traigo el query

		// Aca calculo los días de estancia en el servicio  ************************
		$q =  " SELECT ROUND(TIMESTAMPDIFF(MINUTE,Fecha_data,now())/(24*60),2) "
			. "   FROM ".$wbasedato."_000016 "
			. "  WHERE inghis = '".$whistoria."'"
			. "    AND inging = '".$wingreso."'";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
		$row = mysql_fetch_array($err);
		$wdiastan = $row[0];


		if ($wdiastan == "" or $wdiastan == 0)
		   $wdiastan = 0;

		$q =  " INSERT INTO ".$wbasedato."_000033(   Medico       ,   Fecha_data,   Hora_data,   Historia_clinica,   Num_ingreso,              Servicio ,   Num_ing_Serv,   Fecha_Egre_Serv ,    Hora_egr_Serv ,      Tipo_Egre_Serv ,  Dias_estan_Serv,    Seguridad     ) "
			. "                            VALUES('".$wbasedato."','".$wfecha."','".$whora."','".$whistoria."'        ,'".$wingreso."'   ,'".$ccoOrigenUrg."' ,".$wingser."  ,'".$wfecha."'      ,'".$whora."'     ,'".$codCcoCirugia."',".$wdiastan."    , 'C-" . $wusuario . "')";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());

		//==================================

	//}

}


function modificarUbicacionActualPaciente($whistoria, $wingreso, $servicioOrigen, $habitacionOrigen, $servicioDestino, $habitacionDestino) {

	global $wbasedato;
	global $conex;

	$q = "UPDATE ".$wbasedato."_000018
		     SET Ubisac = '".$servicioDestino."', Ubihac = '".$habitacionDestino."', Ubisan = '".$servicioOrigen."', Ubihan = '".$habitacionOrigen."'
		   WHERE Ubihis = '".$whistoria."'
			 AND Ubiing = '".$wingreso."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$num = mysql_affected_rows();
}


function consultarUltimoMovimientoPaciente($whistoria,$wingreso){

	global $wbasedato;
	global $conex;

	$q = "SELECT Eyrhis, Eyring, Eyrsor, Eyrsde, Eyrhor, Eyrhde, Eyrtip, Eyrest
			FROM ".$wbasedato."_000017
		   WHERE Eyrhis = '".$whistoria."'
			 AND Eyring = '".$wingreso."'
			 AND Eyrtip = 'Entrega'
			 AND Eyrest = 'on'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . "-en el query: " . $q . "-" . mysql_error());
	$filas = mysql_num_rows($res);

	if($filas > 0){
		$fila = mysql_fetch_array($res);

		$info = new movimientoHospitalarioDTO();

		$info->historia = $fila['Eyrhis'];
		$info->ingreso = $fila['Eyring'];
		$info->servicioDestino = $fila['Eyrsde'];
		$info->servicioOrigen = $fila['Eyrsor'];
		$info->habitacionOrigen = $fila['Eyrhor'];
		$info->habitacionDestino = $fila['Eyrhde'];
		$info->tipoMovimiento = $fila['Eyrtip'];
	}
	return $info;
}

// Función que permite consultar el código actual de el centro de costos de Urgencias
function consultarCcoUrgencias(){

	global $wbasedato;
	global $conex;

	$q = "SELECT Ccocod
		    FROM ".$wbasedato."_000011
		   WHERE Ccourg = 'on'
		     AND Ccoest = 'on'; ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){

		$fila = mysql_fetch_row($res);
		$cco = $fila[0];
	}

	return $cco;
}



// Función que permite consultar el código actual de el centro de costos de Cirugia
function consultarCcoCirugia(){

	global $wbasedato;
	global $conex;

	$q = "SELECT Ccocod
		    FROM ".$wbasedato."_000011
		   WHERE Ccocir = 'on'
		     AND Ccoest = 'on'; ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$cco = "";

	if($filas > 0){

		$fila = mysql_fetch_row($res);
		$cco = $fila[0];
	}

	return $cco;
}

//Pacientes pendientes de entregar desde cirugia a hospitalizacion.
function listaPacientesPreentregadosCir($codCcoUrgencias, $codCcoCirugia){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");
	$todoslashab = todaslashab();
	$wfecha_actual = date('Y-m-d');
	$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));
	$fecha_inicial = date("Y-m-d", strtotime("$wfecha_actual -7 day"));

	//Pacientes que tienen solicitud de cama y que aun estan con habitacion asignada desde urgencias, estos pacientes aun estan en el centro de costos de cirugia.
	$q = " SELECT Ubihis, Ubiing, Ubisac, CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) as Nombre, Fec_asigcama, Hora_asigcama, Hab_asignada, Cconom, ".$wcencam."_000003.id as id_solicitud_cama"
		."   FROM root_000036, root_000037, " . $wbasedato . "_000018, ".$wcencam."_000003, " . $wbasedato . "_000011, ".$wtcx."_000011 "
		."  WHERE oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
		."    AND oriced  = pacced "
		."    AND oritid  = pactid "
		."	  AND ccocod = ubisac "
		."	  AND ubihis = orihis "
		."	  AND ubiing = oriing "
		."	  AND ubihis = turhis "
		."	  AND ubiing = turnin "
		."    AND historia = ubihis  "
		."    AND Fecha_Cumplimiento = '0000-00-00'  "
		."    AND Hora_cumplimiento =  '00:00:00'  "
		."    AND Anulada = 'No'"
		."    AND turfec BETWEEN '".$dia_anterior."' AND '".$wfecha_actual."'"
		."    AND ".$wcencam."_000003.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$wfecha_actual."'"
		."    AND central = '".$wcentral_camas."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){

	while($fila = mysql_fetch_array($res)){

		$hising = $fila['Ubihis']."-".$fila['Ubiing'];

		if(!array_key_exists($hising, $coleccion )){

				$hab_asignada = trim($fila['Hab_asignada']);
				$coleccion[$hising] = array('historiaPaciente'=>$fila['Ubihis'],
											'ingresoHistoriaPaciente'=>$fila['Ubiing'],
											'nombrePaciente'=>$fila['Nombre'],
											'ccoActual'=>$fila['Ubisac'],
											'ccoDestino'=>$todoslashab[$hab_asignada]['Habcco']." - ".$todoslashab[$hab_asignada]['nombre_cco'],
											'habitacionDestino'=>$fila['Hab_asignada'],
											'fechaEntrega'=>$fila['fechaEntrega'],
											'HoraEntrega'=>$fila['HoraEntrega'],
											'id_solicitud_cama'=>$fila['id_solicitud_cama']);

			}
		}
	}

	return $coleccion;


}

//Lista de pacientes con solicitud de cama, con cama asignada y que son de urgencias.
function listaPacientesPreentregados($centroCostos, $codCcoCirugia){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
	$wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');
	$todoslashab = todaslashab();

	$wfecha_actual = date('Y-m-d');
	$fecha_inicial = date("Y-m-d", strtotime("$wfecha_actual -7 day"));

	$q = " SELECT Ubihis, Ubiing, Ubisac, CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) as Nombre, Fec_asigcama, Hora_asigcama, Hab_asignada, Cconom, ".$wcencam."_000003.id as id_solicitud_cama "
		."   FROM root_000036, root_000037, " . $wbasedato . "_000018, ".$wcencam."_000003, " . $wbasedato . "_000011 "
		."  WHERE oriori  = '" . $wemp_pmla . "'" // Empresa Origen de la historia,
		."    AND oriced  = pacced "
		."    AND oritid  = pactid "
		."	  AND ccocod = ubisac "
		."	  AND ubihis = orihis "
		."	  AND ubiing = oriing "
		."    AND ubisac  = '" . $centroCostos . "'" // Servicio Actual
		."    AND ubisac  != '" . $codCcoCirugia . "'"
		."    AND ".$wcencam."_000003.historia = ubihis  "
		."    AND Fecha_Cumplimiento = '0000-00-00'"
		."    AND Hora_cumplimiento =  '00:00:00'"
		."    AND Anulada = 'No'"
		."    AND ".$wcencam."_000003.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$wfecha_actual."'"
		."    AND central = '".$wcentral_camas."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){

		while($fila = mysql_fetch_array($res)){

			$hising = $fila['Ubihis']."-".$fila['Ubiing'];

			if(!array_key_exists($hising, $coleccion )){

				$coleccion[$hising] = array('historiaPaciente'=>$fila['Ubihis'],
											'ingresoHistoriaPaciente'=>$fila['Ubiing'],
											'nombrePaciente'=>$fila['Nombre'],
											'ccoActual'=>$fila['Ubisac'],
											'ccoDestino'=>$todoslashab[$hab_asignada]['Habcco']." - ".$todoslashab[$hab_asignada]['nombre_cco'],
											'habitacionDestino'=>$fila['Hab_asignada'],
											'id_solicitud_cama'=>$fila['id_solicitud_cama']);

			}
		}
	}

	return $coleccion;
}

//Consulta los pacientes con solicitud de traslado de urgencias a cirugia.
function listaPacientesPreentregadosCirUrg($codCcoUrgencias, $codCcoCirugia){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");

	$wfecha_actual = date('Y-m-d');
	$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));


	$q = " SELECT
				 b.Fecha_data fechaEntrega, b.Hora_data HoraEntrega, Ubihis, Ubiing, (SELECT CONCAT(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2) FROM root_000036 WHERE Pacced = Oriced AND Pactid = Oritid ) Nombre,
				Ubisac, Eyrsde, (SELECT Cconom FROM ".$wbasedato."_000011 WHERE Ccocod = Eyrsde) Cconom, Eyrhde
		FROM
				".$wbasedato."_000018, ".$wbasedato."_000017 b, root_000037
		WHERE   Ubihis = Eyrhis
				AND Ubiing = Eyring
				AND Eyrhis = Orihis
				AND Eyring = Oriing
				AND Ubihac = ''
				AND Ubiptr = 'on'
				AND Ubiald != 'on'
				AND Eyrest = 'on'
				AND Eyrtip = 'Entrega'
				AND Ubisac = '".$codCcoUrgencias."'
				AND Eyrsor = '".$codCcoUrgencias."'
				AND Eyrsde = '".$codCcoCirugia."'
				AND Oriori = '".$wemp_pmla."'
		GROUP BY Ubihis, Ubiing
		ORDER BY 1 asc, 2 asc";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$filas = mysql_num_rows($res);

	$coleccion = array();

	if($filas > 0){

		while($fila = mysql_fetch_array($res)){

			$hising = $fila['Ubihis']."-".$fila['Ubiing'];

			if(!array_key_exists($hising, $coleccion )){

				$coleccion[$hising] = array('historiaPaciente'=>$fila['Ubihis'],
											'ingresoHistoriaPaciente'=>$fila['Ubiing'],
											'nombrePaciente'=>$fila['Nombre'],
											'ccoActual'=>$fila['Ubisac'],
											'ccoDestino'=>$fila['Eyrsde']." - ".$fila['Cconom'],
											'habitacionDestino'=>$fila['Eyrhde'],
											'fechaEntrega'=>$fila['fechaEntrega'],
											'HoraEntrega'=>$fila['HoraEntrega']);

			}
		}
	}

	return $coleccion;
}

//Funcion que verifica si un paciente esta en proceso de traslado desde urgencias a cirugia y desde cirugia a piso.
function procesos_traslado_pac_cir($conex, $wbasedato, $ccoCodigo){

	$codCcoUrgencias = consultarCcoUrgencias();
	$codCcoCirugia = consultarCcoCirugia();

	switch ($ccoCodigo) {

			case $codCcoUrgencias:
				$pac_urg_a_cir = listaPacientesPreentregadosCirUrg($codCcoUrgencias, $codCcoCirugia);
				break;

			case $codCcoCirugia:
				$pac_urg_a_cir = listaPacientesPreentregadosCir($codCcoUrgencias, $codCcoCirugia);
				break;

		}

	return $pac_urg_a_cir;

}

//Funcion que controla los estados en el proceso de traslado de un paciente.
function procesos_traslado_pac_urg($conex, $wbasedato, $ccoCodigo){

	$codCcoUrgencias = consultarCcoUrgencias();
	$codCcoCirugia = consultarCcoCirugia();

	switch ($ccoCodigo) {

			case $codCcoUrgencias:
				$pac_urg_a_cir = listaPacientesPreentregados($codCcoUrgencias, $codCcoCirugia);
				break;

		}

	return $pac_urg_a_cir;

}


//------------------ FIN DE FUNCIONES PARA EL TRASLADO DE PACIENTES ------------------------



//-----------------------------------------------------------
// 					PROCEDIMIENTOS AGRUPADOS
//-----------------------------------------------------------

function consultarSiPuedeAnular( $historia, $ingreso, $medicamento, $ido ){

	global $conex;
	global $wbasedato;
	global $whce;

	$qMedDeProc = "SELECT Reltor,Relnro,Relpro,Relite,Detesi
					 FROM ".$wbasedato."_000195,".$whce."_000028,".$wbasedato."_000045
					WHERE Relhis='".$historia."'
					  AND Reling='".$ingreso."'
					  AND Relmed='".$medicamento."'
					  AND Relido='".$ido."'
					  AND Reltor=Dettor
					  AND Relnro=Detnro
					  AND Relpro=Detcod
					  AND Relite=Detite
					  AND Eexcod=Detesi
					  AND Eexrea='on';";

	$resMedDeProc = mysql_query($qMedDeProc, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qMedDeProc . " - " . mysql_error());
	$numMedDeProc = mysql_num_rows($resMedDeProc);

	$noPuedeAnular = false;

	if($numMedDeProc > 0)
	{
		$noPuedeAnular = true;
	}

	return $noPuedeAnular;
}

function consultarMedicamentosPorProcedimiento( $historia,$ingreso,$tipoOrden,$numOrden,$codPro)
{
	global $conex;
	global $wbasedato;

	$contador = 0;
	$arrayMedPorProc = array();



	$queryEncabezado= "  SELECT *
						   FROM ".$wbasedato."_000053
						  WHERE Karhis = '".$historia."'
							AND Karing = '".$ingreso."'
							AND Fecha_data = '".date("Y-m-d")."'
							;";

	$resEncabezado = mysql_query($queryEncabezado, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEncabezado . " - " . mysql_error());
	$numEncabezado = mysql_num_rows($resEncabezado);

	$fechaKardexActual="";
	if($numEncabezado > 0)
	{
		$fechaKardexActual = date("Y-m-d");
	}
	else
	{
		$queryEncabezado= "  SELECT *
							   FROM ".$wbasedato."_000053
							  WHERE Karhis = '".$historia."'
								AND Karing = '".$ingreso."'
								AND Fecha_data = '".date("Y-m-d", strtotime("-1 day"))."'
								;";

		$resEncabezado = mysql_query($queryEncabezado, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEncabezado . " - " . mysql_error());
		$numEncabezado = mysql_num_rows($resEncabezado);

		if($numEncabezado > 0)
		{
			$fechaKardexActual = date("Y-m-d", strtotime("-1 day"));
		}

	}

	$queryMedPorProc = " SELECT Relmed,Relido,Kadsus,Kadcfr,Kadufr,Kadfin,Kadhin,Kadper,Percan,Peruni,Artcom,Artgen,Artcod, '54' AS Tabla
						   FROM ".$wbasedato."_000195,".$wbasedato."_000054,".$wbasedato."_000026,".$wbasedato."_000043
						  WHERE Relhis = '".$historia."'
							AND Reling = '".$ingreso."'
							AND Reltor = '".$tipoOrden."'
							AND Relnro = '".$numOrden."'
							AND Relpro = '".$codPro."'
							AND Kadhis = Relhis
							AND Kading = Reling
							AND Kadfec = '".$fechaKardexActual."'
							AND Kadart = Relmed
							AND Kadido = Relido
							AND Kadest = 'on'
							AND Kadart = Artcod
							AND Artest = 'on'
							AND Percod = Kadper
							AND Perest = 'on'

							UNION

						 SELECT Relmed,Relido,Kadsus,Kadcfr,Kadufr,Kadfin,Kadhin,Kadper,Percan,Peruni,Artcom,Artgen,Artcod, '60' AS Tabla
						   FROM ".$wbasedato."_000195,".$wbasedato."_000060,".$wbasedato."_000026,".$wbasedato."_000043
						  WHERE Relhis = '".$historia."'
							AND Reling = '".$ingreso."'
							AND Reltor = '".$tipoOrden."'
							AND Relnro = '".$numOrden."'
							AND Relpro = '".$codPro."'
							AND Kadhis = Relhis
							AND Kading = Reling
							AND Kadfec = '".$fechaKardexActual."'
							AND Kadart = Relmed
							AND Kadido = Relido
							AND Kadest = 'on'
							AND Kadart = Artcod
							AND Artest = 'on'
							AND Percod = Kadper
							AND Perest = 'on'
						;";

	$resMedPorProc = mysql_query($queryMedPorProc, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMedPorProc . " - " . mysql_error());
	$numMedPorProc = mysql_num_rows($resMedPorProc);

	if($numMedPorProc > 0)
	{
		while ($rowMedPorProc = mysql_fetch_array($resMedPorProc) )
		{
			if($rowMedPorProc['Kadsus']=="off")
			{
				$arrayMedPorProc[$contador]['Relmed'] = $rowMedPorProc['Relmed'];
				$arrayMedPorProc[$contador]['Relido'] = $rowMedPorProc['Relido'];
				$arrayMedPorProc[$contador]['Kadsus'] = $rowMedPorProc['Kadsus'];
				$arrayMedPorProc[$contador]['Kadcfr'] = $rowMedPorProc['Kadcfr'];
				$arrayMedPorProc[$contador]['Kadufr'] = $rowMedPorProc['Kadufr'];
				$arrayMedPorProc[$contador]['Kadfin'] = $rowMedPorProc['Kadfin'];
				$arrayMedPorProc[$contador]['Kadhin'] = $rowMedPorProc['Kadhin'];
				$arrayMedPorProc[$contador]['Kadper'] = $rowMedPorProc['Kadper'];
				$arrayMedPorProc[$contador]['Percan'] = $rowMedPorProc['Percan'];
				$arrayMedPorProc[$contador]['Peruni'] = $rowMedPorProc['Peruni'];
				$arrayMedPorProc[$contador]['Artcom'] = $rowMedPorProc['Artcom'];
				$arrayMedPorProc[$contador]['Artgen'] = $rowMedPorProc['Artgen'];
				$arrayMedPorProc[$contador]['Artcod'] = $rowMedPorProc['Artcod'];
				$arrayMedPorProc[$contador]['Tabla'] = $rowMedPorProc['Tabla'];

				$contador++;
			}
		}
	}

	foreach($arrayMedPorProc as $keyMedPorProc => $valueMedPorProc)
	{
		$querySaldMed = " SELECT SUM(Spauen),SUM(Spausa)
							FROM ".$wbasedato."_000004
						   WHERE Spahis='".$historia."'
							 AND Spaing='".$ingreso."'
							 AND Spaart='".$valueMedPorProc['Relmed']."';";

		$resSaldMed = mysql_query($querySaldMed, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $querySaldMed . " - " . mysql_error());
		$numSaldMed = mysql_num_rows($resSaldMed);

		if($numSaldMed == 0)
		{
			$arrayMedPorProc[$keyMedPorProc]['Saldo'] = 0;
		}
		else
		{
			$rowSaldMed = mysql_fetch_array($resSaldMed);
			$arrayMedPorProc[$keyMedPorProc]['Saldo'] = $rowSaldMed[0] - $rowSaldMed[1];

			$queryApliMed = " SELECT COUNT(*),Aplfec,Aplron
								FROM ".$wbasedato."_000015
							   WHERE Aplhis='".$historia."'
								 AND Apling='".$ingreso."'
								 AND Aplart='".$valueMedPorProc['Relmed']."'
								 AND Aplido='".$valueMedPorProc['Relido']."'
								 AND Aplest='on'
							ORDER BY Aplfec DESC, Aplron DESC
							   LIMIT 1
								 ;";

			$resApliMed = mysql_query($queryApliMed, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryApliMed . " - " . mysql_error());
			$numApliMed = mysql_num_rows($resApliMed);

			if($numApliMed > 0)
			{
				$rowApliMed = mysql_fetch_array($resApliMed);
				$arrayMedPorProc[$keyMedPorProc]['Aplicacion'] = $rowApliMed[0];
				$arrayMedPorProc[$keyMedPorProc]['fecUltimaAplic'] = $rowApliMed[1];
				$arrayMedPorProc[$keyMedPorProc]['ronUltimaAplic'] = $rowApliMed[2];
			}
			else
			{
				$arrayMedPorProc[$keyMedPorProc]['Aplicacion'] = 0;
				$arrayMedPorProc[$keyMedPorProc]['fecUltimaAplic'] = "";
				$arrayMedPorProc[$keyMedPorProc]['ronUltimaAplic'] = "";
			}
		}

		if($arrayMedPorProc[$keyMedPorProc]['Saldo']==0 && $arrayMedPorProc[$keyMedPorProc]['Aplicacion']==0)
		{
			$arrayMedPorProc[$keyMedPorProc]['mensMed'] = "Sin dispensar";
			$arrayMedPorProc[$keyMedPorProc]['casoAplicMed'] = 1;
		}
		else if($arrayMedPorProc[$keyMedPorProc]['Saldo']!=0 && $arrayMedPorProc[$keyMedPorProc]['Aplicacion']==0)
		{
			$arrayMedPorProc[$keyMedPorProc]['mensMed'] = "Sin aplicar";
			$arrayMedPorProc[$keyMedPorProc]['casoAplicMed'] = 3;
		}
		// else if($arrayMedPorProc[$keyMedPorProc]['Saldo']==0 && $arrayMedPorProc[$keyMedPorProc]['Aplicacion']!=0)
		else if($arrayMedPorProc[$keyMedPorProc]['Aplicacion']!=0)
		{
			$arrayMedPorProc[$keyMedPorProc]['mensMed'] = "Aplicado";
			$arrayMedPorProc[$keyMedPorProc]['casoAplicMed'] = 2;
		}
	}


	return $arrayMedPorProc;
}

function consultarTablaMedAsociado($wbasedato,$historia,$ingreso,$tipoOrden,$numOrden,$numItem)
{
	global $conex;

	$usuarioOrdAbierta="";

	$qRelMedExiste = "   SELECT Kargra,karusu,Descripcion
						   FROM ".$wbasedato."_000195,".$wbasedato."_000053 b,usuarios
						  WHERE Relhis = '".$historia."'
							AND Reling = '".$ingreso."'
							AND Reltor = '".$tipoOrden."'
							AND Relnro = '".$numOrden."'
							AND Relite = '".$numItem."'
							AND Karhis = Relhis
							AND Karing = Reling
							AND b.Fecha_data= '".date("Y-m-d")."'
							AND Karusu= Codigo
							AND Activo= 'A';";

	$resRelMedExiste = mysql_query($qRelMedExiste, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qRelMedExiste . " - " . mysql_error());
	$numRelMedExiste = mysql_num_rows($resRelMedExiste);

	if($numRelMedExiste > 0)
	{
		$rowRelMedExiste = mysql_fetch_array($resRelMedExiste);

		// Ordenes abiertas
		if($rowRelMedExiste['Kargra'] == "off")
		{
			$usuarioOrdAbierta = $rowRelMedExiste['Descripcion'];
		}
	}
	return $usuarioOrdAbierta;
}

function suspenderMedicamentoAsociadoAProcedimiento($historia,$ingreso,$numOrden,$numItem,$tipoOrden)
{
	global $conex;
	global $wbasedato;
	$estadoSuspension = "on";

	$MedSuspendido = "";
	$MedError = "";
	$MensajeSuspendido = "";
	$MensajeError = "";
	$MedYaSuspendido = "";
	$MensajeSinSuspender = "";
	$Mensaje = "";

	$codMed= "";

	$qRelMedExiste = " SELECT Relmed,Relido,Kadsus,Artgen,Artcod,Artcom
						 FROM ".$wbasedato."_000195,".$wbasedato."_000054,".$wbasedato."_000026
						WHERE Relhis = '".$historia."'
							AND Reling = '".$ingreso."'
							AND Reltor = '".$tipoOrden."'
							AND Relnro = '".$numOrden."'
							AND Relite = '".$numItem."'
							AND Kadhis = Relhis
							AND Kading = Reling
							AND Kadfec = '".date("Y-m-d")."'
							AND Kadart = Relmed
							AND Kadido = Relido
							AND Kadest = 'on'
							AND Kadart = Artcod
							AND Artest = 'on';";

	$resRelMedExiste = mysql_query($qRelMedExiste, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qRelMedExiste . " - " . mysql_error());
	$numRelMedExiste = mysql_num_rows($resRelMedExiste);

	if($numRelMedExiste > 0)
	{
		while ($rowRelMedExiste = mysql_fetch_array($resRelMedExiste))
		{
			if($rowRelMedExiste['Kadsus'] != "on")
			{
				$qSuspender = "  UPDATE ".$wbasedato."_000054
									SET Kadsus = 'on'
								  WHERE Kadhis = '".$historia."'
									AND Kading = '".$ingreso."'
									AND Kadfec = '".date("Y-m-d")."'
									AND Kadart = '".$rowRelMedExiste['Relmed']."'
									AND Kadido = '".$rowRelMedExiste['Relido']."'
									AND Kadest = 'on';";

				$resultadoUpdateSuspender = mysql_query($qSuspender,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qSuspender." - ".mysql_error());

				if(mysql_affected_rows()==1)
				{
					$mensajeAuditoria = "Articulo suspendido";

					// $MedSuspendido .= $rowRelMedExiste['Artgen'].",";
					$MedSuspendido .= $rowRelMedExiste['Artcom'].",";
					$codMed .= $rowRelMedExiste['Artcod'].",";
				}
				else
				{
					// $MedError .= $rowRelMedExiste['Artgen'].",";
					$MedError .= $rowRelMedExiste['Artcom'].",";
				}
			}
			else
			{
				// $MedYaSuspendido .= $rowRelMedExiste['Artgen'].",";
				$MedYaSuspendido .= $rowRelMedExiste['Artcom'].",";
			}

		}
	}

	if($MedSuspendido!="")
	{
		$MensajeSuspendido = "- Se suspendieron los medicamentos ".trim(substr($MedSuspendido, 0, -1))." asociados al procedimiento \n";
	}
	if($MedError!="")
	{
		$MensajeError = "- Error al  suspender los medicamentos ".trim(substr($MedError, 0, -1))." asociados al procedimiento \n";
	}
	if($MedYaSuspendido!="")
	{
		$MensajeSinSuspender = "- Los medicamentos ".trim(substr($MedYaSuspendido, 0, -1))." asociados al procedimiento ya estaban suspendidos\n";
	}

	$Mensaje = $MensajeSuspendido.$MensajeError.$MensajeSinSuspender;

	//Auditoria
	$referencia = "";
	if($codMed!="")
	{
		$referencia="Medicamentos asociados a un procedimiento agrupado: ".trim(substr($codMed, 0, -1))." <br>";

		$mensajeAuditoria = "Articulo suspendido";

		$wuser_datos = explode("-",$_SESSION['user']);

		//Registro de auditoria
		$auditoria = new AuditoriaDTO();

		$auditoria->historia = $historia;
		$auditoria->ingreso = $ingreso;
		$auditoria->descripcion = $referencia;
		$auditoria->fechaKardex = date("Y-m-d");
		$auditoria->mensaje = $mensajeAuditoria;
		$auditoria->seguridad = $wuser_datos[1];

		registrarAuditoriaKardex($conex,$wbasedato,$auditoria);
	}

	return	$Mensaje;
}

function consultarCantidades( $his, $ing, $tip, $nro, $pro){

	global $conex;
	global $wbasedato;

	$queryCantidades = "SELECT Ordtor,Ordnro,Dettor,Detnro,Detcod,Detcnt
						FROM ".$wbasedato."_000188, ".$wbasedato."_000189
						WHERE Ordhis='".$his."'
						AND Ording='".$ing."'
						AND Ordest='on'
						AND Ordtor=Dettoa
						AND Ordnro=Detnra
						AND Dettor='".$tip."'
						AND Detnro='".$nro."'
						AND Detcod='".$pro."';";

	$resCantidades = mysql_query( $queryCantidades, $conex ) or die( mysql_errno()." - Error en el query $queryCantidades - ".mysql_error() );
	$numCantidades = mysql_num_rows( $resCantidades );

	if( $numCantidades > 0 ){
		$rows = mysql_fetch_array( $resCantidades );
		$cantidad = $rows['Detcnt'];
	}
	else
	{
		$cantidad = "1";
	}

	return $cantidad;
}

//-----------------------------------------------------------

//Funcion que calcula el tiempo en horas y minutos con un tiempo definido en segundos.
function tiempo_transcurrido($tiempo_en_segundos){

	$horas = floor($tiempo_en_segundos / 3600);
	$minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
	$segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);

	if($horas > 0){
		return $horas . ' hrs <br>' . $minutos . " min ";
	}else{
		return $minutos . " min ";
	}

}


function mostrarMensajeAlertaDmax( $conex, $wbasedato, $his, $ing, $art, $ido, &$nombreAlterno ){

	$val = true;

	//Busco el insumo ya sea para IC o LQ
	$sql = "SELECT *
			  FROM ".$wbasedato."_000171
			 WHERE levhis = '".$his."'
			   AND leving = '".$ing."'
			   AND levins = '".$art."'
			   AND levidi = '".$ido."'
			";

	$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());

	if( $rows = mysql_fetch_array($res) ){

		//Si es una infusión continua siempre debo mostrar el medicamento
		if( $rows['Levinf'] == 'on' ){

			//Si tiene componentes asociados en la tabla de componentes por tipo, mostrará los tipos
			$qComp = "SELECT Carnal
						FROM {$wbasedato}_000098
					   WHERE Cartip = 'IC'
					     AND Carcod = '$art'
					   ;";

			$resComp = mysql_query($qComp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qComp . " - " . mysql_error());

			if( $rowsComp = mysql_fetch_array($resComp) ){
				if( !empty($rowsComp['Carnal']) )
					$nombreAlterno = $rowsComp['Carnal'];
			}

			if( $rows['Levele'] != 'on' ){
				$val = false;
			}
		}
		else{
			//Si es un LQ debo mostrar el electrolito, en caso de no tenerlo debo mostrar
			//la solución

			//Si tiene componentes asociados en la tabla de componentes por tipo, mostrará los tipos
			$qComp = "SELECT Carnal
						FROM {$wbasedato}_000098
					   WHERE Cartip = 'LQ'
					     AND Carcod = '$art'
					   ;";

			$resComp = mysql_query($qComp, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qComp . " - " . mysql_error());

			if( $rowsComp = mysql_fetch_array($resComp) ){
				if( !empty($rowsComp['Carnal']) )
					$nombreAlterno = $rowsComp['Carnal'];
			}

			//Si es LQ miro si tiene más de un articulo
			$sql = "SELECT *
					  FROM ".$wbasedato."_000171
					 WHERE levhis = '".$his."'
					   AND leving = '".$ing."'
					   AND levido = '".$rows['Levido']."'
					";

			$res2 = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$num = mysql_num_rows( $res2 );

			if( $num > 1 ){
				if( $rows['Levele'] != 'on' ){
					$val = false;
				}
			}
		}
	}

	return $val;
}


//Array que contiene el log de cambios para los medicamentos.
function array_log(){

	global $conex;
	global $wbasedato;

	$array_log = array();

	$q_log = "SELECT Logcod, Logdes
			    FROM ".$wbasedato."_000174
			   WHERE logest = 'on'" ;
	$res_log = mysql_query($q_log);

	while($row_log = mysql_fetch_assoc($res_log)){

		if(!array_key_exists($row_log['Logcod'], $array_log)){

			$array_log[$row_log['Logcod']] =  $row_log;

		}

	}

	return $array_log;

}

// Consulta los datos de una fila según el query $qlog y convierte esta fila en un String
// separando cada campo por el caracter |
function obtenerRegistrosFila($qlog)
{
	global $conex;

	$reslog = mysql_query($qlog, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qlog . " - " . mysql_error());
	$rowlog = mysql_fetch_row($reslog);
	$datosFila = implode("|", $rowlog);
	return $datosFila;
}



//En esta funcion se busca si la el articulo ya ha sido aplicado en la ronda, para que no queden duplicados
//porque la tabla 000015 no tiene indice unico y ya es muy dificil crearselo
function buscarsiExiste($conex, $wbasedato, $wfecha, $whora_a_grabar, $whis, $wing, $wart, $wido)
{
	$q = " SELECT count(*) "
		 ."   FROM ".$wbasedato."_000015 "
		 ."  WHERE aplhis = '".$whis."'"
		 ."    AND apling = '".$wing."'"
		 ."    AND aplfec = '".$wfecha."'"
		 ."    AND SUBSTRING( aplron, 1, 2 ) = '".$whora_a_grabar."'"
		 ."    AND aplart = '".$wart."'"
		 ."    AND aplest = 'on' "
		 ."    AND aplido = ".$wido;

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);

	if ($row[0] > 0)
		return true;       //Si devuelve este valor es porque si existe
	else
		return false;    //No existe, entonces si se puede grabar
}


/************************************************************************************
 * Consulta si un medicamento tiene dosis variable o no y devuelve toda la inforamcion necesaria
 * en un arreglo
 ************************************************************************************/
function consultarDosisVariable( $conex, $wbasedato, $articulo, $cco, $ccoPac )
{

  $val = false;

  $sql = "SELECT
			Defrci, Defcai, Defcas, Defesc, Defavc
		  FROM
			{$wbasedato}_000059
		  WHERE
			defcco = '$cco'
			AND defart = '$articulo'
			AND defest = 'on'
			AND Defcai > 0
			AND Defcas > 0
			AND Defesc > 0
		";

  $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
  $num = mysql_num_rows( $res );

  if( $num > 0 ){
	$val = mysql_fetch_array( $res );

	$val[ 'Defavc' ] = trim( $val[ 'Defavc' ] );

	$val[ 'Defrci' ] = ( $val[ 'Defrci' ] == 'on' )? true : false;

	//Solo si es de cantidad variable
	if( $val[ 'Defrci' ] )
	  {
		$estaCco = false;

		/************************************************************************
		 * Diciembre 20 de 2012
		 ************************************************************************/
		//Si el cco de aplicacion es diferente a * tengo que verificar que
		//el cco del paciente se pueda aplicar cantidad variable
		if( $val[ 'Defavc' ] != '*' ){

			$ccosApl = explode( ",", $val[ 'Defavc' ] );

			foreach( $ccosApl as $keyCcoApl => $valueCcoApl ){

				if( $valueCcoApl == $ccoPac[0] ){
					$estaCco = true;
					break;
				}
			}

			if( !$estaCco ){
				$val[ 'Defrci' ] = false;
			}
		}
		/************************************************************************/
	  }
  }
  else{
	$val[ 'Defrci' ] = false;
	$val[ 'Defcai' ] = 0;
	$val[ 'Defcas' ] = 0;
	$val[ 'Defesc' ] = 1;
  }

  return $val;
}


function validar_aplicacion($conex, $wbasedato, $whis, $wing, $wart, $wcco, $wdosis, &$wcant_aplicar, $wuniman, $warticulodelStock, $noenviar, &$saldo, &$saldoSinRecibir, &$wmensaje )
    {
	 global $wusuario;
	 global $wcenmez;
	 global $wafinidad;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz;
	 global $wemp_pmla;
	 global $ccoayuda;

	 //Si se perdio la session NO DEJO GRABAR
	 if ( true || ( isset($wusuario) and trim($wusuario)!="") )
	    {
		 $wsalart = 0;
		 $wsalsre = 0;

		 //=======================================================================================================
		 //Traigo el saldo del articulo y el Cco
		 //=======================================================================================================
		 if( empty($ccoayuda) ){

			 $q = " SELECT SUM(spauen-spausa), spacco "
				 ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				 ."  WHERE spahis  = '".$whis."'"
				 ."    AND spaing  = '".$wing."'"
				 ."    AND spaart  = '".$wart."'"
				 ."    AND ((spacco = '".trim($wcco)."' "
				 ."    AND  spacco = ccocod ) "
				 ."     OR (spacco  = ccocod "
				 ."    AND  ccotra  = 'on' "             //Permite hacer traslados
				 ."    AND  ccofac  = 'on')) "            //Puede facturar (cargos)
				 ."  GROUP BY 2 "
				 ."  ORDER BY 1 DESC ";
		 }
		 else{

			 $noenviar = 'off';

			 $q = " SELECT SUM(spauen-spausa), spacco "
				 ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				 ."  WHERE spahis  = '".$whis."'"
				 ."    AND spaing  = '".$wing."'"
				 ."    AND spaart  = '".$wart."'"
				 ."    AND FIND_IN_SET( spacco, '".$ccoayuda."') > 0 "            //Puede facturar (cargos)
				 ."  GROUP BY 2 "
				 ."  ORDER BY 1 DESC ";
		 }
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row = mysql_fetch_array($res);

		 if ($row[0] > 0)
			{
			 $wsalart=round( $row[0], 3 );   //Saldo del articulo
			 $wccoapl=$row[1];   //C. Costo que grabo
			 $saldo = $wsalart;	 //Julio 24 de 2012
			}
		 //=======================================================================================================

		$wartfra=1;


		 $wcant_aplicar = ($wdosis/$wuniman)*$wartfra;

		 if ($warticulodelStock=="on")
			{
			 //Mayo 25 de 2012. La cantidad a aplicar siempre es ($wdosis/$wuniman)*$wartfra;
			 // $wcant_aplicar=$wdosis;        //Si es del stock o sea que no se factura por las PDA's entonces aplico la cantidad que dice en el Kardex
			}
		 //=======================================================================================================

		 if ($warticulodelStock=="off")   //Si entra es porque el articulo NO es del STOCK  ojo
			{
			 //Traigo la cantidad SIN RECIBIR de este articulo
			 // $q = " SELECT COUNT(*) "
				 // ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
				 // ."  WHERE fenhis = '".$whis."'"
				 // ."    AND fening = '".$wing."'"
				 // ."    AND ((fencco = '".trim($wcco)."'"
				 // ."    AND   fencco = ccocod ) "
				 // ."     OR  (fencco = ccocod "
				 // ."    AND   ccotra = 'on' "
				 // ."    AND   ccofac = 'on')) "
				 // ."    AND   fennum = fdenum "
				 // ."    AND   fdeart = '".$wart."'"
				 // ."    AND   fdedis = 'on' "
				 // ."    AND   fdeest = 'on' ";

			//Traigo la cantidad SIN RECIBIR de este articulo
			//Este query es para los de SF

             $q  = "SELECT SUM( fdecan )
                      FROM
                (   SELECT SUM(fdecan - fdecar) fdecan"
				 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
				 ."  WHERE fenhis = '".$whis."'"
				 ."    AND fening = '".$wing."'"
				 ."    AND ((fencco = '".trim($wcco)."'"
				 ."    AND   fencco = ccocod ) "
				 ."     OR  (fencco = ccocod "
				 ."    AND   ccotra = 'on' "
				 ."    AND   ccoima != 'on' "		//Mayo 31 de 2012
				 ."    AND   ccofac = 'on')) "
				 ."    AND   fennum = fdenum "
				 ."    AND   fdeart = '".$wart."'"
				 ."    AND   fdedis = 'on' "
				 ."    AND   fdeest = 'on' "
				 ."    AND   fenest = 'on' "		//Marzo 5 de 2013
				 ." HAVING COUNT(*) > 0 ";
            $q .= "  UNION ALL"
                 ." SELECT SUM(fdecan - fdecar) fdecan"
                 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011 "
                 ."  WHERE fenhis = '".$whis."'"
                 ."    AND fening = '".$wing."'"
                 ."    AND ((fencco = '".trim($wcco)."'"
                 ."    AND   fencco = ccocod ) "
                 ."     OR  (fencco = ccocod "
                 ."    AND   ccotra = 'on' "
                 ."    AND   ccoima != 'on' "       //Mayo 31 de 2012
                 ."    AND   ccofac = 'on')) "
                 ."    AND   fennum = fdenum "
                 ."    AND   fdeart = '".$wart."'"
                 ."    AND   fdedis = 'on' "
                 ."    AND   fdeest = 'on' "
                 ."    AND   fenest = 'on' "        //Marzo 5 de 2013
                 ." HAVING COUNT(*) > 0 ";
			//Este segundo query es para los de CM
			 $q .="  UNION ALL"
				 ." SELECT COUNT( DISTINCT fdenum ) fdecan"
				 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
				 ."  WHERE fenhis = '".$whis."'"
				 ."    AND fening = '".$wing."'"
				 ."    AND ((fencco = '".trim($wcco)."'"
				 ."    AND   fencco = ccocod ) "
				 ."     OR  (fencco = ccocod "
				 ."    AND   ccotra = 'on' "
				 ."    AND   ccoima = 'on' "		//Mayo 31 de 2012
				 ."    AND   ccofac = 'on')) "
				 ."    AND   fennum = fdenum "
				 ."    AND   fdeari = '".$wart."'"
				 ."    AND   fdedis = 'on' "
				 ."    AND   fdeest = 'on' "
				 ."    AND   fdelot != '' "
				 ."    AND   fenest = 'on' "		//Marzo 5 de 2013
				 ." HAVING COUNT(*) > 0 ";
             $q .="  UNION ALL"
                 ." SELECT COUNT( DISTINCT fdenum ) fdecan "
                 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000143, ".$wbasedato."_000011 "
                 ."  WHERE fenhis = '".$whis."'"
                 ."    AND fening = '".$wing."'"
                 ."    AND ((fencco = '".trim($wcco)."'"
                 ."    AND   fencco = ccocod ) "
                 ."     OR  (fencco = ccocod "
                 ."    AND   ccotra = 'on' "
                 ."    AND   ccoima = 'on' "        //Mayo 31 de 2012
                 ."    AND   ccofac = 'on')) "
                 ."    AND   fennum = fdenum "
                 ."    AND   fdeari = '".$wart."'"
                 ."    AND   fdedis = 'on' "
                 ."    AND   fdeest = 'on' "
                 ."    AND   fdelot != '' "
                 ."    AND   fenest = 'on' "        //Marzo 5 de 2013
                 ." HAVING COUNT(*) > 0 ) AS cantidades";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			 $row = mysql_fetch_array($res);

			 $wsalsre = $row[0] or 0;  //Cantidad SIN RECIBIR		//Mayo 31 de 2012.	Si la consulta no arroja resulta entonces es 0
			 $saldoSinRecibir = $wsalsre;

			 if ($wsalart >= $wcant_aplicar and $wsalart > 0)
				{
				 if ($wsalart > $wsalsre)                   //Saldo del articulo es mayor a la cantidad que falta por recibir PUEDE APLICARSE maximo la diferencia
					 {
					  if (($wsalart-$wsalsre) >= $wcant_aplicar)   //Si la diferencia es mayor o igual a la dosis PUEDE APLICARSE
						 {
						  return true;
						 }
						else
						   {
							if ($wsalsre > 0)
							   $wmensaje = "No hay SALDO suficiente para aplicar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";
							  else
								 $wmensaje = "No hay SALDO suficiente para aplicar";

							return false;
						   }
					 }
					else
					   {
						if ($wsalsre > 0)
						   $wmensaje = "No hay SALDO suficiente para aplicar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";
						  else
							 {
							  if ($wsalart > 0)
								 $wmensaje = "No hay DOSIS pendientes de aplicar<br>o es del Stock";
							 }
						return false;
					   }
				}
			   else
				 {
				  //Si no tiene saldo suficiente, pero dice NO ENVIAR dejo de todas maneras registrar la aplicación.
				  if ($noenviar=="on")      //Febrero 8 de 2011
					 {
					  return true;
					 }
					else
					  {
					   $wmensaje = "No hay SALDO suficiente para aplicar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";
					   return false;
					  }
				 }
			}
		   else     //Si entra es porque ES del STOCK
			  {
			   // ==============================================================================
			   // Mayo 25 de 2011
			   // ==============================================================================
			   // $wNoIpod = noAplicaConIPOD($wart, trim($wcco));    ///// No Aplica con Ipod /////
				$wNoIpod = 'off';
			   if ($wNoIpod == "off")
				  return true;                                    //Antes del Mayo 25 de 2011 solo estaba esta linea en el else
				 else
					{
					 $wmensaje = "Del Stock, al Facturarlo queda aplicado";
					 return false;
					}
			   // ==============================================================================
			  }
		}
	}



/**********************************************************************************************************************************
 * Indica si un articulo peretenece al stock
 *
 * @param $conex
 * @param $wbasedato
 * @param $articulo
 * @param $cco
 * @return unknown_type
 **********************************************************************************************************************************/
function esStock( $conex, $wbasedato, $articulo, $cco ){

	$val = false;

	$sql = "SELECT
				Arscod
			FROM
				{$wbasedato}_000091
			WHERE
				arscco = '$cco'
				AND arscod = '$articulo'
				AND arsest = 'on'
			"; //echo $sql;

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".msyql_error() );
	$numrows = mysql_num_rows( $res );

	if( $numrows > 0 ){
		$val = true;
	}

	return $val;
}



function registrarAuditoriaKardex($conex,$wbasedato, $auditoria){

	global $wbasedato;

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".date("H:i:s")."','$auditoria->historia','$auditoria->ingreso','$auditoria->descripcion','$auditoria->fechaKardex','$auditoria->mensaje','$auditoria->idOriginal','A-$auditoria->seguridad')";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	}

//Texto para el mensaje de la auditoria.
function obtenerMensaje($clave){

	$texto = 'No encontrado';

	switch ($clave) {

		case 'MSJ_EXAMEN_ACTUALIZADO':
			$texto = "Examen de laboratorio actualizado";
			break;

		default:
			$texto = "Mensaje no especificado";
			break;
	}

	return $texto;
}

//Trae el nombre de un procedimiento o examen dependiendo de un codigo.
function traer_nombre_examen($wcodexam) {

	global $conex;
	global $whce;


	$query =   "SELECT Codigo, Descripcion "
		   ."   FROM ".$whce."_000047"
		   ."  WHERE Codigo = '".$wcodexam."'"
		   ."    AND Estado = 'on'";

	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	return $row['Descripcion'];


}

//Consulta la informacion del usuario, especialmente el rol.
function consultar_rol($codigo_usuario){

	global $conex;
	global $whce;

	$consulta = new stdClass();

	//Rolcod  Roldes  Rolatr  Rolemp  Rolest
	$q2 = "SELECT Usucla,Usurol,Roldes,Rolemp, Rolenf, Rolmed
			 FROM {$whce}_000020,{$whce}_000019
			WHERE Usucod = '".$codigo_usuario."'
			  AND Rolcod = Usurol
			  AND Usuest = 'on'
			  AND Rolest = Usuest
			  AND Usufve > '".date( "Y-m-d" )."'";
	$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
	$num2 = mysql_num_rows($res2);

	if($num2 > 0)
	{
		$rs2 = mysql_fetch_array($res2);

		$consulta->codigoRolHCE = $rs2['Usurol'];
		$consulta->nombreRolHCE = $rs2['Roldes'];
		$consulta->esEnfermeraRolHCE = $rs2['Rolenf'] == 'on' ? true: false;
		$consulta->esMedicoRolHCE = $rs2['Rolmed'] == 'on' ? true: false ;

		if($rs2['Rolemp'] != '' && $rs2['Rolemp'] != 'NO APLICA'){
			$consulta->codigoEmpresaAgrupada = $rs2['Rolemp'];
		} else {
			$consulta->codigoEmpresaAgrupada = "*";
		}
	}


	return $consulta;
}

/************************************************************************************************************************
 * CONSULTA DE ACCIONES POR CADA CAMPO DE UNA PESTAÑA
 *
 * Precedencia de operaciones:
 *
 * 1.Lectura.   Inhibe o permite el resto de operaciones
 ************************************************************************************************************************/
function consultarAccionesPestana($indicePestana){


	global $conex;
	global $whce;

	$wbasedatohce = $whce;
	$wuser_datos = explode("-",$_SESSION['user']);

	//Busca el rol del usuario.
	$datos_rol_usuario = consultar_rol($wuser_datos[1]);

	$acciones = array();

	//Dias de tratamiento
	$q = "SELECT
				Accpma,Accpes,Accopc,Accrol,Acccre,Accrea,Accupd,Accdel,Accest
			FROM
				".$wbasedatohce."_000029
			WHERE
				Accpma = 'ordenes'
				AND Accpes = '$indicePestana'
				AND Accrol = '$datos_rol_usuario->codigoRolHCE'
				AND Accest = 'on';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	while($info = mysql_fetch_array($res)){
		$accion = inicializarAccionesPestana();

		$accion->nroPestana = $indicePestana;
		$accion->codigoAccion = $info['Accopc'];
		$accion->actualizar = $info['Accupd'] == "on" ? true : false;
		$accion->borrar = $info['Accdel'] == "on" ? true : false;
		$accion->crear = $info['Acccre'] == "on" ? true : false;
		$accion->leer = $info['Accrea'] == "on" ? true : false;

		//Forma de indizacion de acciones
		$acciones[$indicePestana.".".$accion->codigoAccion] = $accion;
	}

	return $acciones;
}


function inicializarAccionesPestana(){
	$acciones = new AccionPestanaDTO();

	//Inicializacion con todas las acciones
	$acciones->nroPestana = "";
	$acciones->codigoAccion = "";
	$acciones->nombreAccion = "";
	$acciones->crear = false;
	$acciones->leer = false;
	$acciones->borrar = false;
	$acciones->actualizar = false;

	return $acciones;
}

function crearCampo($tipo,$id,$acciones,$atributos,$valor){
	$salida = "";

	//SI no hay accion por BD, se otorgan todos los permisos
	if(empty($acciones)){
		$acciones = inicializarAccionesPestana();
	}

	switch ($tipo){
		case '6':		//Select
			$salida = "<select name='$id' id='$id' ";

			//Lectura
			if(!$acciones->leer){
				$salida .= " disabled ";
			}

			foreach ($atributos as $clave => $vr){
				if(!empty($vr)){
					$salida .= " $clave=\"$vr\" ";
				} else {
					$salida .= " $clave ";
				}
			}
			$salida .= ">";
			// 2012-07-09
			//Valor
			$salida .= " <option>$valor</option> ";
			$salida .= "</select>";
			break;

		default:
			break;
	}
	echo $salida;
}

//-----------------------------------------------------------------------------------------------------
//	--> FUNCION QUE GENERA UN QUERY CON TODAS LA POSIBLES COMBINACIONES, BAJO LA MODALIDAD QUE PRIMERO
//		SE BUSCA POR UN VALOR ESPECIFICO Y SI NO POR EL VALOR *
//  	Autor: 					Edward jaramillo, Jerson trujillo, felipe alvarez.
//		Ultima Modificacion:	2014-07-21.
//								Los filtros "comodin_izq" "comodin_der" "operador" si son enviados en el array de variables serán tenidas en cuenta para
//								manipular el query por ejemplo cambiando el operador "=" (por defecto) por un "LIKE" por ejemplo y los comodines pueden
//								ser usados para indicar "%xx" a la izquierda o "xx%" a las derecha incluso ambos "%xx%"
//------------------------------------------------------------------------------------------------------
function generarQueryCombinado($variables, $tabla)
{
	global $conex;

	$selectQuery 	= "SELECT id";
	$fromQuery 		= "  FROM ".$tabla;
	$whereQuery 	= " WHERE";
	$orderByQuery 	= " ORDER BY";

	foreach($variables as $campo => $valores)
	{
		// --> SQL fijo en el query
		if(array_key_exists('SQL', $valores) && $valores['SQL'])
		{
			if($whereQuery == " WHERE")
				$whereQuery.= $valores['valor'];
			else
				$whereQuery.= "AND ".$valores['valor'];
		}
		else
		{
			$comodin_izq 	= (array_key_exists("comodin_izq", $valores)) ? $valores['comodin_izq'] : '';
			$comodin_der 	= (array_key_exists("comodin_der", $valores)) ? $valores['comodin_der'] : '';
			$operador 		= (array_key_exists("operador", $valores) && !empty($valores['operador'])) ? $valores['operador'] : '=';

			// --> Agregar un OR al filtro con busqueda por valor *
			if($valores['combinar'])
			{
				// --> Armar el select del query
				$selectQuery.=	", ".$campo;

				// --> Armar el where del query
				$whereQueryTemp	=  " (".$campo." ".$operador." '".$comodin_izq.$valores['valor'].$comodin_der."' OR ".$campo." ".$operador." '".$comodin_izq."*".$comodin_der."')";

				if($whereQuery == " WHERE")
					$whereQuery.= $whereQueryTemp;
				else
					$whereQuery.= " AND ".$whereQueryTemp;

				// --> Armar el order by del query
				if($orderByQuery == " ORDER BY")
					$orderByQuery.= ' '.$campo.' DESC';
				else
					$orderByQuery.= ', '.$campo.' DESC';
			}
			else
			{
				// --> Armar el where del query
				$whereQueryTemp	=  " ".$campo." ".$operador." '".$comodin_izq.$valores['valor'].$comodin_der."'";

				if($whereQuery == " WHERE")
					$whereQuery.= $whereQueryTemp;
				else
					$whereQuery.= " AND".$whereQueryTemp;
			}
		}
	}

	return $queryGeneral = $selectQuery.$fromQuery.$whereQuery.$orderByQuery;
}

function consultarEstadosExamenesRol(){

	global $wbasedato;
	global $conex;

	$coleccion = array();

	$q = "SELECT Eexcod,Eexdes,Eexapa,Eexcan,Eexrea,Eexpen,Eexaut,Eexenf,Eexere,Eexepe,Eexhor,Eexrno,Eexrpe,Eexeau
		    FROM ".$wbasedato."_000045
		   WHERE Eexord = 'on'
		     AND Eexest = 'on'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$cont1 = 0;
	while($cont1 < $num)
	{
		$info = mysql_fetch_array($res);

		$reg = new RegistroGenericoDTO();

		$reg->codigo = $info['Eexcod'];
		$reg->descripcion = $info['Eexdes'];
		$reg->accion_med_proc_agrup = $info['Eexapa'];
		$reg->est_cancelada = $info['Eexcan'];
		$reg->est_realizado = $info['Eexrea'];
		$reg->est_pendiente = $info['Eexpen'];
		$reg->est_autorizado = $info['Eexaut'];
		$reg->estado_realizado = $info['Eexere'];
		$reg->estado_pendiente = $info['Eexepe'];
		$reg->horario = $info['Eexhor'];
		$reg->realizado_nocturno = $info['Eexrno'];
		$reg->enfermeria = $info['Eexenf'];
		$reg->resultado_pendiente = $info['Eexrpe'];
		$reg->estado_autorizado = $info['Eexeau'];

		$cont1++;

		$coleccion[$info['Eexcod']] = $reg;
	}

	return $coleccion;
}

function estados_por_rol_enf(){

	global $wbasedato;
	global $conex;
	global $wemp_pmla;

	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');

	$coleccion_estados = array();

	$wuser_datos = explode("-",$_SESSION['user']);

	//Busca el rol del usuario.
	$datos_rol_usuario = consultar_rol($wuser_datos[1]);
	$dato_rol = $datos_rol_usuario->codigoRolHCE;

	$q = "SELECT *
		    FROM {$wbasedato}_000045, {$whce}_000019
		   WHERE Eexest = 'on'
		     AND Eexord = 'on'
			 AND Eexenf = Rolenf
			 AND Rolcod = '".$dato_rol."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

	while($info = mysql_fetch_assoc($res))
	{
		if(!array_key_exists($info['Eexcod'], $coleccion_estados)){

			$coleccion_estados[$info['Eexcod']] = $info;
		}

	}

	return $coleccion_estados;
}

//Verifica si el paciente ha tenido registros por el programa de ordenes.
function buscar_paciente_ordenes($conex, $wbasedato, $historia, $ingreso){

	$wfecha_actual = date('Y-m-d');
	$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));

	//Consulta el kardex del dia actual, si hay kardex utilizo la fecha actual, sino, la de la fecha un dia antes.
	$q = "SELECT karord
		   	FROM ".$wbasedato."_000053
		   WHERE Karest = 'on'
		     AND Karhis = '".$historia."'
		     AND Karing = '".$ingreso."'
		     AND Fecha_data = '".$wfecha_actual."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	$ordenes_activo = $row['karord'];

	if($ordenes_activo != 'on'){

	//Consulta el kardex del dia actual, si hay kardex utilizo la fecha actual, sino, la de la fecha un dia antes.
	$q = "SELECT karord
		   	FROM ".$wbasedato."_000053
		   WHERE Karest = 'on'
		     AND Karhis = '".$historia."'
		     AND Karing = '".$ingreso."'
		     AND Fecha_data = '".$dia_anterior."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	$ordenes_activo = $row['karord'];

	}

	return $ordenes_activo;

}

//Consulta si un centro de costos es de hemodinamia.
function esHemodinamia($conex, $servicio){

	global $wbasedato;

	$es = false;

	$q = "SELECT Ccocod
			FROM ".$wbasedato."_000011
			WHERE Ccoest =  'on'
			  AND Ccohos !=  'on'
			  AND Ccoing =  'on'
			  AND Ccohib !=  'on'
			  AND Ccocir !=  'on'
			  AND Ccourg !=  'on'
			  AND Ccoadm !=  'on'
			  AND Ccocod = '".$servicio."'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$es = true;
	}

	return $es;
}


//Consulta si un centro de costos es de urgencias
function esUrgencias($conex, $servicio){

	global $wbasedato;

	$es = false;

	$q = "SELECT Ccourg
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$servicio."' ";

	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccourg'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

//Consulta si un centro de costos es de cirugia
function esCirugia($conex, $servicio){

	global $wbasedato;

	$es = false;

	$q = "SELECT Ccocir
		 	FROM ".$wbasedato."_000011
		   WHERE Ccocod = '".$servicio."'
			 AND Ccourg != 'on'";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	if($num>0)
	{
		$rs = mysql_fetch_array($err);

		($rs['Ccocir'] == 'on') ? $es = true : $es = false;
	}

	return $es;
}

function interconsultas($whistoria, $wingreso){

	global $wemp_pmla;
	global $whce;
	global $conex;
	global $wbasedato;

	$array_interconsultas = array();

	$cod_tipo_orden = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CodInterconsultas');;

	$q_inter = "  SELECT
				Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp, Tiprju, Codcups as Codigo_cup, NoPos, {$whce}_000028.id as id_detalle, Detpri, {$whce}_000028.Hora_data, {$whce}_000028.Fecha_data
			FROM
				{$whce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$whce}_000028, {$whce}_000047 c, {$whce}_000015 d,".$wbasedato."_000045
			WHERE
				Ordhis = '$whistoria'
				AND Ording = '$wingreso'
				AND Ordest = 'on'
				AND Ordtor = Dettor
				AND Ordnro = Detnro
				AND Detest = 'on'
				AND c.Codigo = Detcod
				AND Tipoestudio = d.Codigo
				AND Detesi = Eexcod
				AND Eexpen = 'on'
				AND Detalt = 'off'
				AND Ordtor = '".$cod_tipo_orden."'
				AND Dettor = '".$cod_tipo_orden."'
			UNION
			SELECT
				Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp, Tiprju, c.Codigo as Codigo_cup, NoPos, {$whce}_000028.id as id_detalle, Detpri, {$whce}_000028.Hora_data, {$whce}_000028.Fecha_data
			FROM
				{$whce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$whce}_000028, {$whce}_000017 c,  {$whce}_000015 d, ".$wbasedato."_000045
			WHERE
				Ordhis = '$whistoria'
				AND Ording = '$wingreso'
				AND Ordest = 'on'
				AND Ordtor = Dettor
				AND Ordnro = Detnro
				AND Detest = 'on'
				AND c.Codigo = Detcod
				AND Tipoestudio = d.Codigo
				AND nuevo = 'on'
				AND Detesi = Eexcod
				AND Eexmeh != 'on'
				AND Detalt = 'off'
				AND Ordtor = '".$cod_tipo_orden."'
				AND Dettor = '".$cod_tipo_orden."'
			ORDER BY
				Cconom, Ordtor,Ordnro";
		$res_inter = mysql_query($q_inter,$conex) or die (mysql_errno()." - $q_inter ".mysql_error());
		$num_inter = mysql_num_rows($res_inter);

		if($num_inter > 0){

			while($row_inter = mysql_fetch_array($res_inter)){

				$desc_inter = str_replace("INTERCONSULTA","",$row_inter['Descripcion']);

				$array_interconsultas[$row_inter['id_detalle']] = $desc_inter."<br>";

			}

		}else{
			$array_interconsultas = "";
		}

		return $array_interconsultas;

}


function examenes_procedim_pend($whistoria, $wingreso){

	global $whce;
	global $conex;
	global $wbasedato;
	global $estados_examenes;

	$array_datos_procedimientos = array();
	$arrayCita = [ 'conCita' => false, 'hoy' => false  ];

	$q = "  SELECT
				Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp, Tiprju, Codcups as Codigo_cup, NoPos, {$whce}_000028.id as id_detalle, Detpri, Detlog, Detpen, Deteex, {$whce}_000028.Hora_data, {$whce}_000028.Fecha_data, Detjoc, Dethci, Detotr, Detrse, Detrex, Detaut, Detnof
			FROM
				{$whce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$whce}_000028, {$whce}_000047 c, {$whce}_000015 d,".$wbasedato."_000045
			WHERE
				Ordhis = '$whistoria'
				AND Ording = '$wingreso'
				AND Ordest = 'on'
				AND Ordtor = Dettor
				AND Ordnro = Detnro
				AND Detest = 'on'
				AND c.Codigo = Detcod
				AND Tipoestudio = d.Codigo
				AND Detesi = Eexcod
				AND Eexpen = 'on'
				AND Detalt = 'off'
			UNION
			SELECT
				Ordhis,Ording,Ordtor,Ordnro,Ordobs,Ordesp,Ordest,Ordfir,Dettor,Detnro,Detcod,Detesi,Detrdo,Detest,Detfec,Detjus,d.descripcion as Cconom,c.Descripcion,Protocolo, Detite, Tipoestudio,Ordusu,Detusu,Detalt,Detimp, Tiprju, c.Codigo as Codigo_cup, NoPos, {$whce}_000028.id as id_detalle, Detpri, Detlog, Detpen, Deteex, {$whce}_000028.Hora_data, {$whce}_000028.Fecha_data, Detjoc, Dethci, Detotr, Detrse, Detrex, Detaut, Detnof
			FROM
				{$whce}_000027 LEFT JOIN ".$wbasedato."_000011 ON Ccocod = Ordtor, {$whce}_000028, {$whce}_000017 c,  {$whce}_000015 d, ".$wbasedato."_000045
			WHERE
				Ordhis = '$whistoria'
				AND Ording = '$wingreso'
				AND Ordest = 'on'
				AND Ordtor = Dettor
				AND Ordnro = Detnro
				AND Detest = 'on'
				AND c.Codigo = Detcod
				AND Tipoestudio = d.Codigo
				AND nuevo = 'on'
				AND Detesi = Eexcod
				AND Eexmeh != 'on'
				AND Detalt = 'off'
			ORDER BY
				Cconom, Ordtor,Ordnro";
		$res_pye = mysql_query($q,$conex) or die (mysql_errno()." - $q ".mysql_error());
		$num_pye = mysql_num_rows($res_pye);

		$array_ordenes = array();
		$array_log_proc_exa = array();

		// Si se encontraron ordenes
		if($num_pye > 0){

			while($row_pye = mysql_fetch_assoc($res_pye)){

				if($row_pye['Detpen'] == 'on'){
					$array_log_proc_exa[$row_pye['Detlog']][] = $row_pye['Detlog'];
				}

				if(!array_key_exists($row_pye['Dettor'], $array_ordenes)){

					$array_ordenes[$row_pye['Dettor']] = array();
				}

				$array_ordenes[$row_pye['Dettor']][] = $row_pye;

				// if( $estados_examenes[ $row_pye['Detesi'] ]->est_pendiente == 'on' and $estados_examenes[ $row_pye['Detesi'] ]->enfermeria == 'on' ){

					// $pendiente++;

				// }

				if( $estados_examenes[ $row_pye['Detesi'] ]->estado_pendiente == 'on' and $estados_examenes[ $row_pye['Detesi'] ]->enfermeria == 'on' and $row_pye['Detpri'] == 'on' ){

					$pendiente_prioritario++;

				}

				if( $estados_examenes[ $row_pye['Detesi'] ]->est_autorizado == 'on' and $estados_examenes[ $row_pye['Detesi'] ]->enfermeria == 'off' ){

					$autorizado++;

				}
				
				
				if( $estados_examenes[ $row_pye['Detesi'] ]->est_pendiente == 'on' && $row_pye['Dethci'] != '00:00:00' ){
					$arrayCita[ 'conCita'] = true;
					
					if( $row_pye['Detfec'] == date("Y-m-d") ){
						$arrayCita[ 'hoy' ] = true;
					}
				}
			}
		}

		if($pendiente_prioritario > 0){

			$clase_estado_examenes = "fondoNaranja";

		}else{

			if($autorizado == $num_pye ){

				$clase_estado_examenes = "articuloNuevoPerfil";

			}
		}

		$array_datos_procedimientos = array(
				'array_procedimientos'	=>  $array_ordenes, 
				'cantidad_pendientes'	=>  $num_pye, 
				'clase_estado_examenes'	=>  $clase_estado_examenes, 
				'array_log_proc_exa'	=>  $array_log_proc_exa,
				'conCitaAsignada'		=>  $arrayCita,
			);

		return $array_datos_procedimientos;

}

//Pendientes de lectura en ordenes.
function pendientes_ordenes($conex, $wbasedato, $programa, $historia, $ingreso, $whce){

	global $wemp_pmla;
	global $ccoayuda;

	$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	$array_datos = array();
	$total_pendientes = 0;
	$wfecha = date('Y-m-d');

	//Consulta el kardex del dia actual, si hay kardex utilizo la fecha actual, sino, la de la fecha un dia antes.
	$q = "SELECT *
		   	FROM ".$wbasedato."_000053
		  WHERE Karest = 'on'
		    AND Karhis = '".$historia."'
		    AND Karing = '".$ingreso."'
		    AND Fecha_data = '".$wfecha."'
	   ORDER BY hora_data asc";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if($num == 0){

		//Consulta el ultimo kardex para el paciente.
		$q = "SELECT MAX(Fecha_data) as ult_kardex
				FROM ".$wbasedato."_000053
			  WHERE Karest = 'on'
				AND Karhis = '".$historia."'
				AND Karing = '".$ingreso."'
		   ORDER BY hora_data asc";
		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$row = mysql_fetch_array($res);

		$wfecha = $row['ult_kardex'];

	}

	//Medidas generales
	$q_med_gen = "SELECT id
		            FROM ".$wbasedato."_000053
		           WHERE Karhis = '".$historia."'
			         AND Karing = '".$ingreso."'
					 AND Fecha_data = '".$wfecha."'
			         AND Karpen = 'on';";
	$res_med_gen = mysql_query($q_med_gen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_med_gen. " - " . mysql_error());

	if($row_med_gen = mysql_fetch_array($res_med_gen)){

		$wpendientes_med_gen = "Información Demográfica / Medidas Generales";
		//$wcantidad_med_gen = mysql_num_rows($res_med_gen);
		//$array_datos["medidas_generales"] = array("origen"=>$wpendientes_med_gen, "cuantos"=>$wcantidad_med_gen);

	}

	if( empty( $ccoayuda ) ){
		// $validacionExtra = " AND ( Ekxart = NULL OR ( Ekxayu != '' AND Kadess != 'on' ) )";
		$validacionExtra = "  AND ( Ekxart = NULL OR ( Ekxayu = '' OR ( Ekxayu != '' AND Kadess != 'on' ) ) )";
	}

	//Consulta pendientes de medicamentos sin tener en cuenta los genericos.
	$q_med = "  SELECT * FROM (SELECT Kadart, Kadlog, Kadido
				  FROM ".$wbasedato."_000054
			 LEFT JOIN ".$wbasedato."_000208
					ON Ekxhis = Kadhis
				   AND Ekxing = Kading
				   AND Ekxfec = Kadfec
				   AND Ekxart = Kadart
				   AND Ekxido = Kadido
				 WHERE Kadhis ='".$historia."'
				   AND Kading = '".$ingreso."'
				   AND Kadfec = '".$wfecha."'
				   AND Kadpen = 'on'
				   $validacionExtra
				   AND Kadart NOT IN(SELECT artcod FROM	{$wbasedato}_000068,{$wcenmez}_000002,{$wcenmez}_000001
									  WHERE	artcod = arkcod
										AND arttip = tipcod
										AND tiptpr = arktip
										AND artest = 'on'
										AND arkest = 'on'
										AND tipest = 'on' )
			  GROUP BY Kadart
				 UNION
			    SELECT Kadart, Kadlog, Kadido
				  FROM ".$wbasedato."_000060
			 LEFT JOIN ".$wbasedato."_000209
				    ON Ekxhis = Kadhis
				   AND Ekxing = Kading
				   AND Ekxfec = Kadfec
				   AND Ekxart = Kadart
				   AND Ekxido = Kadido
				 WHERE Kadhis ='".$historia."'
				   AND Kading = '".$ingreso."'
				   AND Kadfec = '".$wfecha."'
				   AND Kadpen = 'on'
				   $validacionExtra
				   AND Kadart NOT IN(SELECT artcod FROM	{$wbasedato}_000068,{$wcenmez}_000002,{$wcenmez}_000001,{$wbasedato}_000054
									  WHERE	artcod = arkcod
										AND arttip = tipcod
										AND tiptpr = arktip
										AND artest = 'on'
										AND arkest = 'on'
										AND tipest = 'on')
			  GROUP BY Kadart, Kadido) as t
			  ORDER BY Kadlog ";
	$res_med = mysql_query($q_med,$conex) or die("Error en el query: ".$q_med."<br>Tipo Error:".mysql_error());
	$wcantidad_pendientes_med = mysql_num_rows($res_med);
	$med_paciente = array();

	if($wcantidad_pendientes_med > 0){

		while($row_med = mysql_fetch_array($res_med)){

			$datos_med = consultarInformacionProducto( $row_med['Kadart'], $articulos );

			$mostarArticulo = mostrarMensajeAlertaDmax( $conex, $wbasedato, $historia, $ingreso, $row_med['Kadart'], $row_med['Kadido'], $articulos[$row_med['Kadart']]['nombreComercial'] );

			if( $mostarArticulo ){

				if(!array_key_exists($row_med['Kadlog'], $med_paciente)){


					$med_paciente[$row_med['Kadlog']][] = $articulos[$row_med['Kadart']]['nombreComercial'];

				}else{

					$med_paciente[$row_med['Kadlog']][] = $articulos[$row_med['Kadart']]['nombreComercial'];

				}
			}
			else{
				$wcantidad_pendientes_med--;
			}
		}

		$wpendientes_med = "Medicamentos";

		$array_datos["medicamentos"] = array("origen"=>$wpendientes_med, "cuantos"=>$wcantidad_pendientes_med, "cuales"=>$med_paciente);

	}

	//Consulta pendientes de procedimientos y examenes
	$q_p_ex ="  SELECT A.Medico, A.Fecha_data, A.Hora_data, A.Ordfec, A.Ordhor, A.Ordhis, A.Ording, A.Ordtor, A.Ordnro, A.Ordobs, A.Ordesp,
					   A.Ordest, A.Ordusu, A.Ordfir, A.Seguridad, A.id as id_encabezado, B.Medico, B.Fecha_data, B.Hora_data, B.Dettor,
					   B.Detnro, B.Detcod, B.Detesi, B.Detrdo, B.Detfec, B.Detjus, B.Detest, B.Detite, B.Detusu, B.Detfir, B.Deture,
					   B.Seguridad, B.id as id_detalle
				  FROM ".$whce."_000027 A, ".$whce."_000028 B
				 WHERE Ordtor = Dettor
				   AND Ordnro = Detnro
				   AND A.Ordhis = '".$historia."'
				   AND A.Ording = '".$ingreso."'
				   AND B.Detpen = 'on'
				   AND B.Detest = 'on'
				   AND B.Detalt = 'off'
			  ORDER BY B.Detfec DESC";
	$res_p_ex = mysql_query($q_p_ex, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_p_ex. " - " . mysql_error());


	if($row_p_ex = mysql_fetch_array($res_p_ex)){

		$wpendientes_exam = "Procedimientos y Examenes";
		// $wcantidad_pendientes_exam = mysql_num_rows($res_p_ex);
		// $array_datos["procedimientos_examenes"] = array("origen"=>$wpendientes_exam, "cuantos"=>$wcantidad_pendientes_exam);

	}


	//Dextrometer
	$q_dextro = "SELECT Infade, Inffde, Infcde
		           FROM ".$wbasedato."_000070 a, ".$wbasedato."_000071 b
		          WHERE Infhis = '".$historia."'
			        AND Infing = '".$ingreso."'
					AND a.Fecha_data = b.Fecha_data
					AND a.Hora_data = b.Hora_data
					AND Infhis = Indhis
					AND Inding = Infing
					AND Inffec = '".$wfecha."'
			        AND Infpen = 'on';";
	$res_dextro = mysql_query($q_dextro, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_dextro. " - " . mysql_error());

	if($row_dextro = mysql_fetch_array($res_dextro)){

		$wpendientes_dextro = "Dextrometer";
		// $wcantidad_dextrometer = mysql_num_rows($res_dextro);
		// $array_datos["dextrometer"] = array("origen"=>$wpendientes_dextro, "cuantos"=>$wcantidad_dextrometer);

	}


	//Dietas
	$q_dietas = "  SELECT id
				     FROM ".$wbasedato."_000052
				    WHERE Dikhis ='".$historia."'
				      AND Diking = '".$ingreso."'
					  AND Dikfec = '".$wfecha."'
				      AND Dikpen = 'on'";
	$res_dietas = mysql_query($q_dietas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_dietas. " - " . mysql_error());

	if($row_dietas = mysql_fetch_array($res_dietas)){

		$wpendientes_dietas = "Dietas";
		// $wcantidad_dietas = mysql_num_rows($res_dietas);
		// $array_datos["dietas"] = array("origen"=>$wpendientes_dietas, "cuantos"=>$wcantidad_dietas);

	}


	$wmsjkardex  = consultarMensajesSinLeer( $conex, $wbasedato, "ordenes", $historia, $ingreso );

	if($wmsjkardex > 0){

		$wpendientes_chat_kardex = "Mensaje(s) del chat";
		$array_datos["chat"] = array("origen"=>$wpendientes_chat_kardex, "cuantos"=>$wmsjkardex);

	}


	$total_pendientes = $wcantidad_pendientes_med + $wcantidad_pendientes_exam + $wcantidad_dietas + $wcantidad_dextrometer + $wmsjkardex;

	if($total_pendientes > 0){
	$array_datos["total_pendientes"] = array("origen"=>"Total pendientes", "cuantos"=>$total_pendientes);
	}

	return $array_datos;

}


//Verifica si el centro de costos debe ir a ordenes.
function ir_a_ordenes($wemp_pmla, $wcco){


	global $wbasedato;
	global $conex;

	$q = "  SELECT Ccoior
			  FROM ".$wbasedato."_000011
			 WHERE Ccocod = '".$wcco."'" ;
	$res = mysql_query($q, $conex);
	$row = mysql_fetch_array($res);

	return $row['Ccoior'];

}
/******************************************************************************************************************
 * Consulta la frecuencia de acuerdo al codigo
 ******************************************************************************************************************/
function consultarFrecuencia( $codigoFrecuencia ){

	global $conex;
	global $wbasedato;

	$val = false;

	$sql = "SELECT
				Perequ
			FROM
				{$wbasedato}_000043 b
			WHERE
				percod = '$codigoFrecuencia'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows['Perequ'];
	}

	return $val;
}

/******************************************************************************************
 * Indica si un paciente tiene articulos en el kardex para una fecha
 ******************************************************************************************/
function tieneArticulosPorFecha( $historia, $ingreso, $fecha ){

	global $conex;
	global $wbasedato;

	$val = false;

	$sql = "SELECT
				Kadart, Kadfin, Kadhin, Perequ, Kadcfr, Kadufr, Kadsus, Kadcnd
			FROM
				{$wbasedato}_000054 a, {$wbasedato}_000043 b
			WHERE
				Kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfin = '$fecha'
				AND percod = kadper
				AND kadori = 'CM'
				AND kadcon = 'off'
				AND kadsus != 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0){
		$val = true;
	}

	return $val;
}

/**************************************************************************************************
 * Devuelve un arreglo con los articulos sin confirmar para un paciente y fecha dados
 *
 * El array devuelto esta compuesto así
 *
 * La primera posicion es asocitativo con indice igual al codigo del articulo
 * el valor igual al tiempo unix
 * [ art ] = tiempo unix
 **************************************************************************************************/
function articulosSinConfirmar( $historia, $ingreso, $fecha ){

	global $conex;
	global $wbasedato;
	global $informacionArticulos;

	$val = array();

	$sql = "SELECT
				Kadart, Kadfin, Kadhin, Perequ, Kadcfr, Kadufr, Kadsus, Kadcnd
			FROM
				{$wbasedato}_000054 a, {$wbasedato}_000043 b
			WHERE
				Kadhis = '$historia'
				AND kading = '$ingreso'
				AND kadfec = '$fecha'
				AND percod = kadper
				AND kadori = 'CM'
				AND kadcon = 'off'
				AND kadsus != 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){

		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$val[ $rows['Kadart'] ][ 'inicio' ] = strtotime( $rows['Kadfin']." ".$rows['Kadhin'] );
			$val[ $rows['Kadart'] ][ 'frecuencia' ] = $rows['Perequ'];
			$val[ $rows['Kadart'] ][ 'unidadDeFraccion' ] = $rows['Kadufr'];
			$val[ $rows['Kadart'] ][ 'cantidadDeFraccion' ] = $rows['Kadcfr'];
			$val[ $rows['Kadart'] ][ 'suspendido' ] = $rows['Kadsus'];
			$val[ $rows['Kadart'] ][ 'condicion' ] = $rows['Kadcnd'];

			consultarInformacionProducto( $rows['Kadart'], $informacionArticulos );
		}
	}

	return $val;
}

/****************************************************************************************************************
 * Valida si para una ronda hay un articulo sin confirmar
 *
 * $ronda		Numero entero de 0-23
 * $articulo	array de articulos
 * $art			medicamentos sin confirmar
 ****************************************************************************************************************/
function rondaConArticuloSinConfirmar( $articulos, $ronda, &$art, &$arts2 ){

	$val = false;

	if( !empty($articulos) && count($articulos) > 0 ){

		foreach( $articulos as $keyArt => $valArt ){

			if( strtotime( date( "Y-m-d" )." $ronda:00:00" ) >= $valArt[ 'inicio' ] ){

				if( ( strtotime( date( "Y-m-d" )." $ronda:00:00" ) - $valArt[ 'inicio' ] )%( $valArt[ 'frecuencia' ]*3600 ) == 0 ){
					$art[] = $keyArt;
					$arts2[ $keyArt ] = 1;
					$val = true;
				}
			}
		}
	}

	return $val;
}


/************************************************************************************
 * Segun la condicion de suministro del articulo, dice si dicha condición
 * es considerada a necesidad o no
 *
 * Septiembre 2011-09-11
 ************************************************************************************/
function estaANecesidad( $condicion ){

	global $wbasedato;
	global $conex;

	$val = false;

	if( !empty( $condicion ) ){

		$sql = "SELECT
					Contip, Condes
				FROM
					{$wbasedato}_000042
				WHERE
					concod = '$condicion'
				";

		$resAN = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrowsAN = mysql_num_rows( $resAN );

		if( $numrowsAN > 0 ){

			$rowsAN = mysql_fetch_array( $resAN );

			$val = $rowsAN[ 'Condes' ];

			if( $rowsAN[ 'Contip' ] == 'AN' ){
				$val .= " - AN";
			}
		}
	}

	return $val;
}


/********************************************************************************************************************************************
 * Consulto la información básica del producto (nombre comercial y generico, unidad de medida y codigo
 *
 * Guardo la información en una variable global, esto para no consultar la información de un articulo repetitivamente
 ********************************************************************************************************************************************/
function consultarInformacionProducto( $producto, &$articulos ){

	global $conex;
	global $wcenmez;
	global $wbasedato;

	if( isset( $articulos[$producto] ) ){
		return;
	}

	$sql = "SELECT *
			  FROM {$wbasedato}_000026
			 WHERE artcod = upper('$producto')";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );

	if( $num > 0 ){

		$rows = mysql_fetch_array( $res );

		$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
		$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
		$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
		$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
	}
	else{

		$sql = "SELECT *
				  FROM {$wcenmez}_000002
				 WHERE artcod = '$producto'";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			$rows = mysql_fetch_array( $res );

			$articulos[ strtoupper($rows['Artcod']) ][ 'codigo' ] = $rows['Artcod'];
			$articulos[ strtoupper($rows['Artcod']) ][ 'nombreComercial' ] = $rows['Artcom'];
			$articulos[ strtoupper($rows['Artcod'])][ 'nombreGenerico' ] = $rows['Artgen'];
			$articulos[ strtoupper($rows['Artcod']) ][ 'unidadMinima' ] = $rows['Artuni'];
		}
	}

}


function consultarMensajesSinLeerCPA( $conex, $wbasedato, $programa, $wcco, $wser, $wfecha,&$res ){

	$sql = "SELECT sernom, sercod, COUNT(*)
			  FROM {$wbasedato}_000127, {$wbasedato}_000076
			 WHERE Mencco    = '$wcco'
			   AND Menprg    = '$programa'
			   AND Menlei   != 'on'
			   AND Menfec    = '$wfecha'
			   AND Menser+0  = sercod+0
			 GROUP BY 1,2
			 ORDER BY 2
			";
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

}

function consultarProcedimientosSinLeer( $conex, $wbasedato, $historia, $ingreso ){

	$val = false;

	$sql = "SELECT
				count(*)
			FROM
				{$wbasedato}_000121
			WHERE
				Dmohis = '$historia'
				AND Dmoing = '$ingreso'
				AND Dmolei != 'on'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array($res) ){
		$val = $rows[ 0 ];
	}

	return $val;
}
function Consultar_ServicioSiguiente($word)
 {
  global $conex;
  global $wbasedato;

  $whora  = (string)date("H:i:s");

  if ($word == 7)
     {
	  $q = " SELECT sercod, sernom, MIN(serord) "
		  ."   FROM ".$wbasedato."_000076 "
		  ."  WHERE serhin <= '".$whora."'"
		  ."    AND serhca >= '".$whora."'"
		  ."    AND serest  = 'on' ";
	 }
	else
       {
		$q = " SELECT sercod, sernom, serord "
		    ."   FROM ".$wbasedato."_000076 "
			."  WHERE serord = ".($word+1)
			."    AND serhin <= '".$whora."'"
		    ."    AND serhca >= '".$whora."'"
			."    AND serest  = 'on' ";
	   }
  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
  $num = mysql_num_rows( $res );

  if ($num > 0)
     {
	  $row = mysql_fetch_array( $res );
	  $wcodser   = $row[0];
	  $wservicio = $row[1];
	 }
    else
      {
	   $wcodser   = "";
	   $wservicio = "";
	  }

  return $wcodser."-".$wservicio;
 }

function Consultar_servicioActual()
 {
  global $conex;
  global $wbasedato;

  $whora  = (string)date("H:i:s");

  $q = " SELECT sercod, sernom, serord "
      ."   FROM ".$wbasedato."_000076 "
	  ."  WHERE serhin <= '".$whora."'"
	  ."    AND serhad >= '".$whora."'"
	  ."    AND serest  = 'on' ";
  $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
  $num = mysql_num_rows( $res );

  if ($num > 0)
     {
	  $row = mysql_fetch_array( $res );
	  $wcodser   = $row[0];
	  $wservicio = $row[1];
	  $worden    = $row[2];
	 }
    else
       $wservicio="";

  //return $wcodser."-".$wservicio."-".$wcodser_ant."-".$wservicio_ant;
  return $wcodser."-".$wservicio."-".$worden;
 }

function Consultar_dietas($whis,$wing,$wser)
 {
  global $conex;
  global $wbasedato;

  $wfecha = date( "Y-m-d" );
  $whaypedido = 0;
  $array_datos_dieta = array();

  if (isset($wser) and trim($wser) != "")
    {
	 $q = " SELECT movdie "
	     ."   FROM ".$wbasedato."_000077 "
		 ."  WHERE movfec  = '".$wfecha."'"
		 ."    AND movhis  = '".$whis."'"
		 ."    AND moving  = '".$wing."'"
		 ."    AND movest  = 'on' "
		 ."    AND movser  = '".$wser."'"
		 ."    AND movdie != '' "
		 ."  UNION "
		 ." SELECT movdie "
	     ."   FROM ".$wbasedato."_000077 "
		 ."  WHERE movfec  = '".$wfecha."'"
		 ."    AND movhis  = '".$whis."'"
		 ."    AND moving  = '".$wing."'"
		 ."    AND movest  = 'on' "
		 ."    AND movser  = '".$wser."'"
		 ."    AND movdie  = '' "
		 ."    AND TRIM(movobs) != '' "
		 ."  ORDER BY 1 DESC ";
	 $res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
	 $row = mysql_fetch_array( $res );
	 $whaypedido = $row['movdie'];
    }

   if ($whaypedido != ''){
	   $array_datos_dieta['tiene_dieta'] = true;
	   $array_datos_dieta['patron_dieta'] = $whaypedido;
   }else{
	   $array_datos_dieta['tiene_dieta'] = false;
   }

   return $array_datos_dieta;
 }


 //Array de zonas.
function array_zonas(){

	global $conex;
	global $wbasedato;

	$array_zonas = array();

	$q_zon = "SELECT Aredes, Arecod
			    FROM ".$wbasedato."_000020, ".$wbasedato."_000169
			   WHERE Areest = 'on'
			     AND Habzon = Arecod
			     AND Habcub = 'on'
		    GROUP BY Arecod" ;
	$res_zon = mysql_query($q_zon);

	while($row_zon = mysql_fetch_assoc($res_zon)){

		if(!array_key_exists($row_zon['Arecod'], $array_zonas)){

			$array_zonas[$row_zon['Arecod']] =  $row_zon;

		}

	}

	return $array_zonas;

}

//Array de historias que tienen asignado cubiculos.
function array_his_cub(){

	global $conex;
	global $wbasedato;

	$datos_his_cub = array();

	$q_cub = "SELECT habcod, habhis, habing, habcpa, habzon
			    FROM ".$wbasedato."_000020
			   WHERE Habcub = 'on'" ;
	$res_cub = mysql_query($q_cub, $conex);

	while($row_cub = mysql_fetch_assoc($res_cub)){

		if(!array_key_exists(trim($row_cub['habhis'].$row_cub['habing']), $datos_his_cub)){

			$datos_his_cub[trim($row_cub['habhis'].$row_cub['habing'])] =  $row_cub;

		}

	}

	return $datos_his_cub;


}

//Pacientes que se encuentran activos en urgencias y que tienen conducta asociada.
function pacientes_atendidos_activos($ccoCodigo){

	global $conex;
	global $wbasedato;
	global $whce;
	global $wemp_pmla;
	global $ccoCodigo;

	$wpacientes_atendidos_act = array();
	$array_conductas = array();

	//consulta de las conductas de urgencias
	$q =   " SELECT concod, condes "
		  ."   FROM ".$whce."_000035 ";
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	while($row = mysql_fetch_assoc($res)){

		if(!array_key_exists($row['concod'], $array_conductas)){

			$array_conductas[$row['concod']] =  $row;

		}

	}

	//Pacientes que se encuentran activos en urgencias y que tienen conducta asociada.
	$q =   " SELECT ubihis, ubiing, mtrcur, mtrmed, mtrcon, consca "
		  ."   FROM root_000036 A, root_000037 B, ".$wbasedato."_000018 C, ".$whce."_000022 D, ".$whce."_000035 E "
		  ."  WHERE ubihis  = orihis "
		  ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de las historias.
		  ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
		  ."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
		  ."    AND ubisac  = '".$ccoCodigo."'"  //Servicio urgencias
		  ."    AND ubihis  = mtrhis "
		  ."    AND ubiing  = mtring "
		  ."    AND mtrcon  = concod "
		  ."  GROUP BY 1,2 ";
	$res = mysql_query($q, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	while($row = mysql_fetch_assoc($res)){

		if(!array_key_exists($row['ubihis'].$row['ubiing'], $wpacientes_atendidos_act)){

			$wpacientes_atendidos_act[trim($row['ubihis'].$row['ubiing'])] =  array('conducta'=>$row['mtrcon'],
																	 'desc_conducta'=>$array_conductas[$row['mtrcon']]['condes'],
																	 'enconsulta'=>$row['mtrcur'],
																	 'cond_sol_cama'=>$row['consca']);

		}

	}


	return $wpacientes_atendidos_act;

}

//---------------------------------------------------------------------------------------------------------------------
//	--> Funcion que valida si un procedimiento requiere autorizacion
//		Jerson trujillo, 2016-03-30
//---------------------------------------------------------------------------------------------------------------------
function procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifProc, $procedimiento)
{
	global $conex;
	global $wemp_pmla;

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

	// --> Generar query combinado para saber si alguna regla le aplica al articulo, para que este deba ser autorizado
	$variables = array();
	// --> Procedimiento
	$variables['Paucop']['combinar'] 	= true;
	$variables['Paucop']['valor'] 		= $procedimiento;
	// --> Clasificacion
	$variables['Paucla']['combinar'] 	= true;
	$variables['Paucla']['valor'] 		= $clasifProc;
	// --> Plan de empresa
	$variables['Paupla']['combinar'] 	= true;
	$variables['Paupla']['valor'] 		= $planEmp;
	// --> Entidad
	$variables['Paucem']['combinar'] 	= true;
	$variables['Paucem']['valor'] 		= $codEnt;
	// --> Nit Entidad
	$variables['Paunit']['combinar'] 	= true;
	$variables['Paunit']['valor'] 		= $nitEnt;
	// --> Tipo de empresa
	$variables['Pautem']['combinar'] 	= true;
	$variables['Pautem']['valor'] 		= $tipEnt;
	// --> Estado
	$variables['Pauest']['combinar'] 	= false;
	$variables['Pauest']['valor'] 		= 'on';

	// --> Obtener query
	$sqlDebeAuto = generarQueryCombinado($variables, $wbasedato."_000260");
	$resDebeAuto = mysql_query($sqlDebeAuto, $conex) or die("ERROR EN QUERY MATRIX (sqlDebeAuto): ".mysql_error());

	if($rowDebeAuto = mysql_fetch_array($resDebeAuto))
	{
		$sqlAut = "SELECT Paupau
					  FROM ".$wbasedato."_000260
					 WHERE id = '".$rowDebeAuto['id']."'";
		$resAut = mysql_query($sqlAut, $conex) or die("ERROR EN QUERY MATRIX (sqlAut): ".mysql_error());
		$rowAut = mysql_fetch_array($resAut);
		if($rowAut['Paupau'] == 'on')
			return true;
		else
			return false;
	}
	else
		return false;
}

function check_in_range($start_date, $end_date, $evaluame) {
       $start_ts = $start_date;
       $end_ts = $end_date;
       $user_ts = $evaluame;
       return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
   }

function control_rango_contingencia($wemp_pmla){

	global $conex;

	$HorasGeneracionContingencia = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HorasGeneracionContingencia');
	$datos_horas = explode("-", $HorasGeneracionContingencia);

	$a = strtotime(''.$datos_horas[0].':00');
	$b = strtotime(''.$datos_horas[1].':00');
	$c = strtotime(''.$datos_horas[2].':00');
	$d = strtotime(''.$datos_horas[3].':00');

	$hora_actual = strtotime(date("H:i:s"));

if( check_in_range($a, $b, $hora_actual) ){
		$rango = $datos_horas[0];
  	} elseif(check_in_range($b, $c, $hora_actual)){
		$rango = $datos_horas[1];
	}elseif(check_in_range($c, $d, $hora_actual)){
			$rango = $datos_horas[2];
	}elseif(check_in_range($d, $e, $hora_actual)){
			 $rango = $datos_horas[3];
		}


	return $rango;

}


function control_contingencia($conex, $wemp_pmla, $wbasedato,  $cco){


	$valor_rango = control_rango_contingencia($wemp_pmla);
	$contingencia = 'off';

	$sql = " SELECT Hora_data
			   FROM ".$wbasedato."_000233
			  WHERE Concco = '".$cco."'
				AND Fecha_data = '".date('Y-m-d')."'
				AND Conran = '".$valor_rango."'";
	$res = mysql_query($sql, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error());
	$num = mysql_num_rows($res);

	if($num > 0){

		$contingencia = 'on';

	}

	return $contingencia;

}

function fecha_hora_terminacion_consulta($whis, $wing){

	global $wemp_pmla;
	global $conex;

	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

	$q = " SELECT Mtrftc, Mtrhtc
		     FROM ".$whce."_000022
		    WHERE Mtrhis = '".$whis."'
			  AND Mtring = '".$wing."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	return $row['Mtrftc']." ".$row['Mtrhtc'];

}

function dameURL()
  {
	$url="http://".$_SERVER['HTTP_HOST'];
	return $url;
  }


//Funcion que busca el medico tratante por paciente.
function traer_medico_tte($whis, $wing, $wfecha, &$cuantos, $wsp, $mostrar)
	 {
		global $conex;
		global $wbasedato;

		$query = " SELECT Medno1, Medno2, Medap1, Medap2  "
				."   FROM ".$wbasedato."_000047, ".$wbasedato."_000048 "
				."  WHERE methis = '".$whis."'"
				."    AND meting = '".$wing."'"
				."    AND metest = 'on' "
				."    AND metfek = '".$wfecha."'"
				."    AND mettdo = medtdo "
				."    AND metdoc = meddoc "
				."    GROUP BY meddoc ";
		$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$wnum = mysql_num_rows($res);

		$cuantos = $wnum;

		if ($wnum > 0)
			{
				$wmed = "";

				while($row = mysql_fetch_array($res)){

					$wmed .= "<li>".$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2']."</li>";

				}


				return $wmed;
			}
			else{
				return "Sin Médico";
			}
	}

/********************************************************************************
 * Pinta los datos necesarios en pantalla
 ********************************************************************************/
function pintarDatosFila( $datos ){

	global $colores;
	global $fecha;
	global $hora;
	global $fechaFinal;
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $ccoCodigo;
	global $whce;
	global $wsp;       //Indica que no se debe mostrar el paciente (WSP)==> Sin Paciente
	global $waux;       //Si viene setiado en 'on' es porque se ingreso por la opción de auxiliar de enfermeria
	global $tiempoAMostrar;
	global $ccoayuda;
	global $mostrar;
	global $slCcoDestino;
	global $registro_inicial;
	global $registro_final;
	global $actualiz;
	global $wuser;
	global $estados_examenes;
	global $sala;

	$fechaFinal++;

	$wir_a_ordenes = ir_a_ordenes($wemp_pmla, $ccoCodigo);

	$wuser_datos = explode("-",$_SESSION['user']);

	//Busca el rol del usuario.
	$datos_rol_usuario = consultar_rol($wuser_datos[1]);

	//Si en la url esta declarada la variable $ccoayuda, permte que los pacientes con ordenes activas puedan entrar a ordenes.
	if($ccoayuda != ''){
		$wir_a_ordenes = 'on';
	}

	$origen = "Kardex";

	$wurgencias = esUrgencias($conex,$ccoCodigo);
	$wcirugia = esCirugia($conex,$ccoCodigo);

	$colEstadosExamenRol = consultarEstadosExamenesRol();

	$array_his_cub = array_his_cub();
	$array_zonas = array_zonas();
	$array_log = array_log();
	$pacientes_atendidos_activos = pacientes_atendidos_activos($ccoCodigo);
	$wtiempos_urgencias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tiempos_urgencias');
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
	$rolesJefesEnf = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolesEnfermeraJefe');
	
	$ccoRealizaEstudios = ccoRealizaEstudios( $conex, $wbasedato, $ccoCodigo );


	$ccoHabilitaAltaPorRol = false;
	$sql = "SELECT *
			  FROM ".$wbasedato."_000011
			 WHERE ccocod = '".$ccoCodigo."'
			   AND ccohal = 'on' ";

	$resHal = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	if( $rowhal = mysql_fetch_array($resHal) ){
		$ccoHabilitaAltaPorRol = true;
	}




	//Se consulta que roles pueden dar de alta
	$esRolParaDarAlta = false;
	if( $ccoHabilitaAltaPorRol ){
		$rolesParaDarAlta = consultarAliasPorAplicacion($conex, $wemp_pmla, 'rolesParaDarAlta');
		$rolesParaDarAlta = explode( ",", $rolesParaDarAlta );
		$esRolParaDarAlta = in_array( $datos_rol_usuario->codigoRolHCE, $rolesParaDarAlta );
	}

	if(strpos($rolesJefesEnf, $datos_rol_usuario->codigoRolHCE)){
		$path_monitor_insumos = "monitorInsumosJefes.php?wemp_pmla=".$wemp_pmla."&wcco=".$ccoCodigo;
		$acceso_monitor_insumos = "<td colspan=3 style='cursor: pointer' align='center' onclick='ejecutar(".chr(34).$path_monitor_insumos.chr(34).")'><font size=1><b>Monitor Insumos</b></td>";
	}

	$wprocesos_traslado_pac_urg = array();
	$wprocesos_traslado_pac_cir = array();
	$wprocesos_traslado_pac_ayuda = array();

	$todoslashab = todaslashab(); //Array con la informacion de todas las habitaciones de la clinica.

	//Evaluar si el centro de costos cargo la pagina en los pisos
	if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){

		log_pantallas_pisos($ccoCodigo);
	}


	//Crea arreglos solo si el centro de costos seleccionado es de cirugia o de urgencias.
	switch(1){

		case ($ccoayuda != ''):
				$wprocesos_traslado_pac_ayuda = listaPacientesEntregarCcoAyuda($ccoayuda);
				// echo "<pre>";
				// print_r($wprocesos_traslado_pac_ayuda);
				// echo "</pre>";
		break;

		case ($wurgencias or $wcirugia):
				$wprocesos_traslado_pac_cir = procesos_traslado_pac_cir($conex, $wbasedato, $ccoCodigo);
				$wprocesos_traslado_pac_urg = procesos_traslado_pac_urg($conex, $wbasedato, $ccoCodigo);
		break;

		default:
				$wpacientes_por_recibir = listaPacientesRecibirHospitalizacion($ccoCodigo);
				$wpacientes_por_entrega = listaPacientesEntregarHospitalizacion($ccoCodigo);
		break;
	}

	$user_session = explode('-',$_SESSION['user']);
	$wuser = $user_session[1];

	$wtiempos_urgencias_aux = explode("-", $wtiempos_urgencias);
	$wtiempo_minimo = $wtiempos_urgencias_aux[0];
	$wtiempo_maximo = $wtiempos_urgencias_aux[1];

	$q = " SELECT ccohos, ccoapl "
		."   FROM " . $wbasedato."_000011 "
		."  WHERE ccocod = '".$ccoCodigo."'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);

	//Centro de costos de hospitalizacion
	if ($row['ccohos'] == "on")
		$wccohos = "on";
	else
		$wccohos = "off";

	//Centro de costos de aplicacion.
	if ($row['ccoapl'] == "on")
		$wccoapl = "on";
	else
		$wccoapl = "off";

	//Array de cubiculos con los datos de historia e ingreso.
	$q_cub =   " SELECT Habcod, Habcpa, Habzon, Habvir  "
			 . "   FROM ".$wbasedato."_000020 "
			 . "  WHERE habcub = 'on'"
			 . "	AND habdis = 'on'"
			 . "	AND habhis = '' "
			 . "	AND habest = 'on' "
			." ORDER BY habord, habcpa ";
	$res_cub = mysql_query($q_cub, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cub." - ".mysql_error());

	$array_cubiculos_fisicos = array();
	$array_cubiculos_virtuales = array();

	while( $row_cubiculos = mysql_fetch_assoc($res_cub)) {
		//Se crean dos arreglos uno que contiene las ubicaciones fisicas y otra las virtuales, luego se valida por zona y si estan ocupadas todas las fisicas muestra el listado de las virtuales. Jonatan 21 sept 2017
		if($row_cubiculos['Habvir'] != 'on'){
			$array_cubiculos_fisicos[$row_cubiculos['Habzon']][$row_cubiculos['Habcod']] = $row_cubiculos;
		}elseif($row_cubiculos['Habvir'] == 'on'){
			$array_cubiculos_virtuales[$row_cubiculos['Habzon']][$row_cubiculos['Habcod']] = $row_cubiculos;
		}
	}

	$cadenaMed="";
	$ocultar = "";
	$datos_cco = consultarCentroCosto($conex, $ccoCodigo, $wbasedato);

	//Convenciones
	$convenciones .="<table align='center' style='border-radius: 35px;' >";
	$convenciones .="<tr style='opacity:90'>";
	$convenciones .="<td class='fondoAlertaConfirmar' align='center' colspan='1'><p class='msg textoNomenclatura' title='Mezclas sin confirmar'>Mezclas sin<br>confirmar</p></td>";
	$convenciones .="<td class='fila1' align='center' colspan='1' ><p class='msg textoNomenclatura' title='Sin Generar Kardex Hoy'><img src='/matrix/images/medical/movhos/NOTE16.ico'></p></td>";
	$convenciones .="<td class='fila1' align='center' colspan='1' ><p class='msg textoNomenclatura' title='Kardex Sin <br>Confirmar Hoy'><img src='/matrix/images/medical/movhos/Key04.ico'></p></td>";
	$convenciones .="<td class='fila1' align='center' colspan='2' ><p class='msg textoNomenclatura' title='Todos los medicamentos <br>obligatorios han sido aplicados'><img src='/matrix/images/medical/movhos/checkmrk.ico'><br>Aplicado</p></td>";
	$convenciones .="<td class='fila1' align='center' colspan='2' ><p class='msg textoNomenclatura' title='Al menos un medicamento <br>obligatorio no ha sido aplicado'><img src='/matrix/images/medical/root/borrar.png'><br>Sin Aplicar <br>o Sin Dieta</p></td>";
	$convenciones .="<td class='fila1' align='center' colspan='2'><p class='msg textoNomenclatura' title='Los medicamentos no han sido aplicados <br>pero tienen justificaci&oacute;n o son a necesidad'><img src='/matrix/images/medical/root/borrarAmarillo.png'><br>Sin Aplicar</p></td>";
	$convenciones .="<td style='background-color:#3CB648;' class='fondoVerde' align='center' colspan='2'><p class='msg textoNomenclatura' title='Órdenes Electrónicas'>Órdenes <br>Electrónicas</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fila2'><p class='msg textoNomenclatura'><img src='/matrix/images/medical/root/pac_con_sol_cama.png'><br>Solicitud<br>de cama</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fila1'><p class='msg textoNomenclatura'><img src='/matrix/images/medical/root/pac_con_cama.png'><br>Cama<br>asignada</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fila2'><p class='msg textoNomenclatura'><img src='/matrix/images/medical/root/pac_con_med.png'><br>Saldo<br>en medicamentos</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fila1'><p class='msg textoNomenclatura'><img src='/matrix/images/medical/root/saldo_insumos.png'><br>Saldo <br>en insumos</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fondoamarillo'><p class='msg textoNomenclatura'><br>Alta<br>en proceso</p></td>";
	$convenciones .="<td align='center' colspan='2' class='colorAzul4'><p class='msg textoNomenclatura'><br>En proceso<br>de traslado</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fondorojo'><p class='msg textoNomenclatura'><br>Insumos <br>pendientes <br>para <br>alta<br> definitiva</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fondoNaranja'><p class='msg textoNomenclatura'><br>Ordenes<br>Prioritarias<br>sin autorizar</p></td>";
	$convenciones .="<td align='center' colspan='2' class='fondoVerde'><p class='msg textoNomenclatura'><br>Ordenes<br>Autorizadas</p></td>";
	$convenciones .="</tr>";
	$convenciones .="</table>";


	//Valida si muestra el encabezado
	if($wsp == ''){
		encabezado( "SISTEMA DE GESTION DE ENFERMERIA", $actualiz ,"clinica" );
		echo $convenciones;
	}

	if( count($datos) > 0 ){

		//No muestra la nomenclatura si tiene este parametro existe.
		if(isset($wsp)){

			$ocultar = 'display:none;';
			echo "<input type=hidden id='texto_nombre_cco' value='".$datos_cco->nombre."'>";
		}

		$i = 0;

		//Control paginación
		if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){

			$datos_cco = consultarCentroCosto($conex, $slCcoDestino, $wbasedato);

			$q = "SELECT Ccorpp
					FROM ".$wbasedato."_000011
				   WHERE Ccocod = '".$slCcoDestino."'";
			$err = mysql_query($q,$conex);
			$row = mysql_fetch_array($err);
			$consultar_registros_por_cco = $row['Ccorpp'];

			$tiempo_recargar_gestion_ingenia = consultarAliasPorAplicacion($conex, $wemp_pmla, "recargarGestionIngenia");

			echo "<input type=hidden id='control_tiempo' value='".$tiempo_recargar_gestion_ingenia."'>";

			if(!isset($registro_inicial)){
				$registro_inicial = 0;
			}else{
				$registro_inicial = $registro_inicial+$consultar_registros_por_cco;
			}

			$canti_pacientes = count($datos);

			if($registro_inicial >= $canti_pacientes){
				$registro_inicial = 0;
			}

			$datos = array_slice($datos, $registro_inicial, $consultar_registros_por_cco);
			$paginas = floor($canti_pacientes / $consultar_registros_por_cco)+((($canti_pacientes % $consultar_registros_por_cco) > 0) ? 1 : 0);
			echo "<table width='100%'><tr><td width='30%'><font size=5>Pág. ".(($registro_inicial/$consultar_registros_por_cco)+1)." de ".$paginas."</font></td>";
			echo "<td width='40%'><table><tr><td><img src='../../images/medical/root/clinica.jpg' heigth='36' width='80'></td><td class=fila1  nowrap><font size=5>".$datos_cco->codigo." - ".$datos_cco->nombre."</font></td><td><img src='../../images/medical/root/fmatrix.jpg' heigth='36' width='80'></td></tr></table></td>";
			echo "<td width='30%'></td>";
			echo "</tr></table>";
			echo $convenciones;

		}

		foreach( $datos as $keyDatos => $valueDatos ){

			$wtraslado      = $valueDatos->enProcesoTraslado;
			$waltaEnProceso = $valueDatos->altaEnProceso;
			$wmuerte 		= $valueDatos->muerte;
			$whab           = $valueDatos->codigo;
			$wconsala       = $valueDatos->consala;
			$wfecha_hora_term_consul = $valueDatos->fecha_hora_term_consul;
			$disabled_sala = "";
			$disabled_cubiculo = "";
			$westado_checkbox = "";
			$hab_destino = "";
			$campo_oculto_estilo= "";
			$texto_hab_destino = "";
			$checked_muerte = "";
			$mensaje_muerte = "";
			$cajon_muerte = "";
			$estado_checked_alta_proc = "";
			$mensaje_saldo_insumos = "";

			if($waltaEnProceso){
				$westado_altaEnProceso  = 'on';
			}else{
				$westado_altaEnProceso  = 'off';
			}

			$wmsjkardex = 0;
			$wmsjSecret = 0;

			//Verifica si el paciente tiene ordenes electronicas el dia actual.
			$paciente_ordenes = buscar_paciente_ordenes($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);

			if( $i == 0 ){
				echo "<table align='center'>";

				//echo "<tr style='opacity:90'>";

				if( $tiempoAMostrar == 24 ){
					echo "<td colspan='8' style='font-size:10pt'>";
				}
				else{
					echo "<td colspan='2' style='font-size:10pt'>";
				}

		        //Consulto el servicio de alimentacion correspondiente a la hora en que se ejecuta este script
		        $wser      = Consultar_ServicioActual();
				$wserv     = explode("-",$wser);
				$wcodser   = $wserv[0];
				$wservicio = $wserv[1];

				//Consulto el servicio de alimentacion Siguiente al actual a la hora en que se ejecuta este script
		        $wser          = Consultar_ServicioSiguiente($wserv[2]);

				if (trim($wser)!="")
				   {
					$wserv         = explode("-",$wser);
					$wcodser_Sig   = $wserv[0];
					$wservicio_Sig = $wserv[1];
				   }


				//*** Link a Turnos de Cirugia
				$path = "/matrix/tcx/reportes/ListaG.php?empresa=tcx&TIP=0";



				// echo "<tr class='fila1' style='$ocultar'>";
				// echo "<td colspan=4 style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'><font size=2>Consultar Turnos de Cirugia</td>";
				// $path = "/matrix/movhos/procesos/Consul_disponibilidad_especialidad.php?wemp_pmla=01";
                // echo "<td colspan=3 style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'><font size=2>Consultar Disponibilidad de Especialidades</td>";
                // echo "</tr>";


				echo "<tr class='fila2' style='$ocultar'>";

				$wc=1;
				if (!isset($wsp) or $wsp!="on")
				   $wc=0;

				echo "<td colspan=2 style='cursor: pointer' align='center' onclick='ejecutar(".chr(34).$path.chr(34).")'><font size=1><b>Consultar Turnos de Cirugia</b></td>";
				$path_disponibilidad = "/matrix/movhos/procesos/Consul_disponibilidad_especialidad.php?wemp_pmla=01";
				echo "<td colspan=3 style='cursor: pointer' align='center' onclick='ejecutar(".chr(34).$path_disponibilidad.chr(34).")'><font size=1><b>Consultar Disponibilidad de Especialidades</b></td>";
				echo $acceso_monitor_insumos;

				$control_contingencia = control_contingencia($conex, $wemp_pmla, $wbasedato, $ccoCodigo);

				if($control_contingencia == 'off' and $waux != 'on'){

					$alerta_contingencia = "fondoRojo blink msg";
					$title_alerta_contingencia = "No ha generado la contingencia";
					echo "<input type='hidden' id='alerta_contingencia' value='on'>";
				}

				$mostrarAlertaCont = consultarAliasPorAplicacion($conex, $wemp_pmla, 'MostarAlertaContingencia');
				echo "<input type='hidden' id='mostrar_alerta_contingencia' value='".$mostrarAlertaCont."'>";

				$url = dameURL();
				echo "<input type='hidden' id='url_raiz' value='".$url."'>";
				echo "<input type='hidden' id='fecha_sistema' value='".date('Y-m-d')."'>";
				echo "<input type='hidden' id='ultima_hora' value='".date('H')."'>";
				echo "<input type='hidden' id='ultimo_minuto' value='".date('i')."'>";
				echo "<input type='hidden' id='ultimo_segundo' value='".date('s')."'>";

				$path_contingencia = "Contingencia_Kardex_de_Enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$ccoCodigo."&wfec=".$fecha;

				echo "<td colspan=3 id='contingencia_".$ccoCodigo."' style='cursor: pointer' class='".$alerta_contingencia."' title='".$title_alerta_contingencia."' align='center' onclick='ejecutar(".chr(34).$path_contingencia.chr(34)."); registro_log_contingencia(\"".$wemp_pmla."\", \"".$wbasedato."\", \"".$ccoCodigo."\", \"".$wuser."\", \"".date('Y-m-d')."\", \"".date('H')."\", \"".date('i')."\", \"".date('s')."\", \"".$url."\", \"".$control_contingencia."\")'><font size=1><b>Contingencia</b></td>";


				/*if( $tiempoAMostrar == 24 )
				  {
				    $wcol=16-$wc;
				    //Este path es para poder accesar a la lista de dietas desde cualquier punto del TR
		            echo "<td colspan=".$wcol."><font size=4>";
				  }
				 else
				   {
				    $wcol=10-$wc;
				    //Este path es para poder accesar a la lista de dietas desde cualquier punto del TR
		            echo "<td colspan=".$wcol."><font size=4>";
				   }*/


				$path_dietas_aux = "";
				$path_dietas_sersgte_aux = "";

				if($wurgencias){

					$ruta_dietas_aux = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser."&wcco=".$valueDatos->cco."&wfec=".$fecha."&wzona=".$sala;
					$path_dietas_seractual_aux = " style='cursor: pointer' onclick='ejecutar(".chr(34).$ruta_dietas_aux.chr(34).")";

					$ruta_dietas_aux_sgte = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser_Sig."&wcco=".$valueDatos->cco."&wfec=".$fecha."&wzona=".$sala;
					$path_dietas_sersgte_aux = " style='cursor: pointer' onclick='ejecutar(".chr(34).$ruta_dietas_aux_sgte.chr(34).")";

					}

				if($wir_a_ordenes == 'on'){
					$colspan = "30";
				}else{
					$colspan = "28";
					}

				consultarMensajesSinLeerCPA( $conex, $wbasedato, "cpa", $valueDatos->cco, $wcodser, $fecha, $wmsjCPA );

				$wnum = mysql_num_rows($wmsjCPA);



				if ($wnum > 0)
				   {
				    $path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser."&wcco=".$valueDatos->cco."&wfec=".$fecha."&wzona=".$sala;
					echo "<td colspan=6 align='center' id=td_cco><font size=4><b style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>".$datos_cco->codigo." - ".$datos_cco->nombre."  ====>  </b></font></td>";

				    echo "<td colspan=$colspan><table>";
					for ($i=1;$i<=$wnum;$i++)
					   {
					    $row = mysql_fetch_array($wmsjCPA);
						$path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$row[1]."&wcco=".$valueDatos->cco."&wfec=".$fecha."&wzona=".$sala;
						echo "<tr>";
						if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
						   echo "<td><b style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>Msj Sin leer en ".$row[0]."  :  <span class='blink'>".$row[2]."</span></b></td>";
						  else
                             echo "<td $path_dietas_aux><b ".$row[0]."  :  ".$row[2]."</b></td>";
						echo "</tr>";
					   }
					echo "</table></td>";
				   }
				   else{
				      echo "<td colspan=$colspan align='center' id=td_cco><font size=4><b>".$datos_cco->codigo." - ".$datos_cco->nombre."</b></font></td>";
				   }
				echo "</font></td>";
				echo "</tr>";

				echo "<tr class='encabezadotabla' align='center'>";
				//------------

				/*$q_consultaalias = "SELECT  detval
									  FROM root_000051
									WHERE detapl = 'traslados_automaticos'
									  AND detemp = '".$wemp_pmla."'" ;
				$res_consultaalias = mysql_query($q_consultaalias);
				$row_consultaalias = mysql_fetch_array($res_consultaalias);

				$traslados_automaticos = $row_consultaalias['detval'];

				if($valueDatos->cco == $traslados_automaticos)
				{
					echo "<td rowspan=2 nowrap='nowrap'>Tipo de Habitación</td>";

				}*/

				//------------
				//Antes se iba a las tabla root 51 para saber si los traslados automaticos estan habilitados en este centro de costo
				//se creo una tabla la cliame _000266.
				/*$q_consultaalias = "SELECT  detval
									  FROM root_000051
									WHERE detapl = 'traslados_automaticos'
									  AND detemp = '".$wemp_pmla."'" ;
				$res_consultaalias = mysql_query($q_consultaalias);
				$row_consultaalias = mysql_fetch_array($res_consultaalias);

				$traslados_automaticos = $row_consultaalias['detval'];
				*/
				//Array de cubiculos con los datos de historia e ingreso.

				//----En la tabla 266 se especifica  las condiciones para cambiar el tipo de habitacion
				$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
				$qccotrasautomaticos =   "	 SELECT Gescco   , Gesvis , Gesfhc , Gescam , Gesdhc ,Geshab,Gesesp
											   FROM ".$wbasedatocliame."_000266
											  WHERE Gescco='".$valueDatos->cco."'
											    AND Gesest='on' ";
				$res_ccotrasautomaticos = mysql_query($qccotrasautomaticos, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$qccotrasautomaticos." - ".mysql_error());

				/*
				Variables donde se especifica  si en este centro de costos se puede trasladar el paciente de tipo de habitacion
				Hay dos formas de trasladarlo, que lo haga la enfermera manualmente, o que  se haga automaticamente desde la historia clinica
				*/
				$cco_trasautomaticos = 'off'; // especifica si los traslados se hacen desde la historica clinica
				$cco_puedecambiar = 'off';    // especifica si el usuario (enfermeras) pueden cambiar manual  el tipo de habitacion
				$cco_esvisible = 'off';		  // especifica si es visible el campo de cambio de habitacion
				$cco_activoDesdeHce = 'off';  // especifica si esta activo los traslados desde hce
				$cco_Habitaciones = '';
				$parametroespecialidades='todas';

				$cco_formularioHce ='off';	 // si esta activo los cambios automaticos desde hce , este es el formalario al que tiene que ir para saber a que tipo de habitacion debe cambiar
				$cco_campoHce ='off';        // campo en hce que se tiene que consultar para saber a que habitacion debe cambiar


				if( $row_ccotrasautomaticos = mysql_fetch_array($res_ccotrasautomaticos))
				{
					$cco_trasautomaticos =  $row_ccotrasautomaticos['Gescco'];
					$cco_esvisible = $row_ccotrasautomaticos['Gesvis'];
					$cco_puedecambiar = $row_ccotrasautomaticos['Gesdhc'];

					if($cco_puedecambiar=='on')
					{
						$cco_puedecambiar='off';
					}
					else
					{
						$cco_puedecambiar ='on';
					}
					$cco_formularioHce =$row_ccotrasautomaticos['Gesfhc'];
					$cco_campoHce =$row_ccotrasautomaticos['Gescam'];
					$cco_activoDesdeHce =$row_ccotrasautomaticos['Gesdhc'];
					$cco_Habitaciones =$row_ccotrasautomaticos['Geshab'];
					$parametroespecialidades =$row_ccotrasautomaticos['Gesesp'];
				}

				if($valueDatos->cco == $cco_trasautomaticos)
				{
					if($cco_puedecambiar =='off')
					{
						$cco_puedecambiar="disabled='true'";
					}

					if($cco_esvisible =='off')
					{
						$cco_esvisible="style= 'display : none;'";
					}
					else
					{
						$cco_esvisible ='';
					}
					echo "<td rowspan=2 nowrap='nowrap' ".$cco_esvisible.">Tipo de Habitación</td>";

				}
				//--------------
				if($wir_a_ordenes == 'on'){

					$origen = "Ordenes";
					}

				// Columna que muestra el semaforo de horas de atencion, solo para urgencias.
				if($wurgencias){
				echo "<td rowspan=2 width=200px title='Tiempo basado en la hora de terminación <br>de la consulta hasta la hora actual.' class='msg'>Tiempo atención
					<table>
					<tr>
					<td style='background-color:#FCEF69;font-size: 7pt;border-radius: 4px;border:1px solid #999999;padding:2px' nowrap>&nbsp;> $wtiempo_minimo horas&nbsp;</td>
					</tr>
					<tr>
					<td style='background-color:#FDCB7F;font-size: 7pt;border-radius: 4px;border:1px solid #999999;padding:2px' nowrap>&nbsp;> $wtiempo_maximo horas&nbsp;</td>
					</tr>
					</table>
					</td>";
				}

				echo "<td rowspan=2>Servicio<br>Actual</td>";
				echo "<td rowspan=2>Servicio<br>Siguiente</td>";
				echo "<td rowspan=2>Msj $origen</td>";

				if (!isset($wsp) or $wsp!="on"){
					echo "<td rowspan=2>Msj Secre.</td>";
				}

				//Pinta la columna de alta para los pacientes de urgencias o cirugia, en el caso de cirugia sera alta definitiva.
				if($wurgencias){
					echo "<td rowspan=2>Dar Alta</td>";
				}
				elseif($wcirugia){
					echo "<td rowspan=2>Dar Alta Definitva</td>";
					echo "<td rowspan=2>Muerte</td>";
				}
				//Si es hospitalario y está no está en gestion de enfermería auxiliares
				elseif($wccohos == 'on' and $waux !='on'){
					echo "<td rowspan=2>Alta Proceso</td>";
					if (!isset($wsp) or $wsp!="on"){
						echo "<td rowspan=2>Alta Definitiva</td>";
						echo "<td rowspan=2>Muerte</td>";
					}
				}
				//Si es hosptialario y es gestion de enfermería auxiliares
				elseif($wccohos == 'on' and $waux =='on'){
					if( $esRolParaDarAlta ){
						echo "<td rowspan=2>Alta Proceso</td>";
						if (!isset($wsp) or $wsp!="on"){
							echo "<td rowspan=2>Alta Definitiva</td>";
							echo "<td rowspan=2>Muerte</td>";
						}
					}
				}
				// Si es un cco de ayuda se deja solo el boton de alta (Alta defintiva o alta para dejar en piso según el caso)
				elseif($ccoayuda !=''){ //Para centros de costos de ayuda sera alta definitiva.
					echo "<td rowspan=2>Alta</td>";
				}

				//Si el centro de costos seleccionado es de urgencias muestra la conducta.
				if($wurgencias){
					echo "<td rowspan=2>Conducta<hr>Ubicación</td>";
				}else{
					echo "<td rowspan=2>Habitaci&oacute;n</td>";
				}

				//Si es auxiliar y el centro de costos es de urgencias o cirugia se muestra la columna de traslado de paciente.
				if($waux == 'on'){
					if($wcirugia or $wurgencias){
						echo "<td rowspan=2>Solicitud de cama <br>Ent. y Rec. Pacientes</td>";
						echo "<td rowspan=2>Traslado a otros servicios</td>";
					}
				}else{
					echo "<td rowspan=2>Solicitud de cama <br>Ent. y Rec. Pacientes</td>";
					if($wurgencias){
						echo "<td rowspan=2>Traslado a otros servicios</td>";
					}
					if($ccoayuda == '' and !$wurgencias and !$wcirugia){
						echo "<td rowspan=2>Traslado a otros servicios</td>";
					}
				}


				echo "<td rowspan=2>Historia</td>";
				if (!isset($wsp) or $wsp!="on")
				   {
					echo "<td rowspan=2 colspan=4>Nombre</td>";
				   }

				//Procedimientos y examenes pendientes.
				if($wir_a_ordenes == 'on'){

					echo "<td rowspan=2 colspan=2>Ordenes<br>Pendientes</td>";

					}

				//Interconsultas
				if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){

					echo "<td rowspan=2>Interconsultas</td>";

				}

				//Medico tratante
				echo "<td rowspan=2 colspan=2>Médico<br>Tratante</td>";

				if( true || date( "Y-m-d", $fechaFinal ) == date( "Y-m-d" ) ){

					if( date( "Y-m-d", $fechaFinal - $tiempoAMostrar*3600 ) == date( "Y-m-d", time() - 24*3600 ) ){	//Si al restar 12 horas es el dia anterior
						//Hago el rowspan correspondiente para el dia anterior
						$rowspanDiaAnterior = ( 24 - date( "G", $fechaFinal-$tiempoAMostrar*3600 ) )/2;

						echo "<td colspan='".$rowspanDiaAnterior."' class='fila1'>".date( "Y-m-d", time()-$tiempoAMostrar*3600 )."</td>";
					}
					else{
						$rowspanDiaAnterior = 0;
					}

					if( $rowspanDiaAnterior*2 != $tiempoAMostrar ){

						if( $rowspanDiaAnterior == 0 ){
							echo "<td colspan='".( ( $tiempoAMostrar - $rowspanDiaAnterior*2 )/2 )."' class='fila1'>".date( "Y-m-d", $fechaFinal-1 )."</td>";
						}
						else{
							echo "<td colspan='".( ( $tiempoAMostrar - $rowspanDiaAnterior*2 )/2 )."'>".date( "Y-m-d", $fechaFinal-1 )."</td>";
						}
					}

					echo "<tr class='encabezadotabla'>";
					$wcentro_costos = $valueDatos->cco."-".$valueDatos->ccoNombre;
					//Pinto las horas correspondientes
					for( $j = date( "G", $fechaFinal - $tiempoAMostrar*3600 ); $j < date( "G", $fechaFinal - $tiempoAMostrar*3600 )+$tiempoAMostrar; $j += 2 ){
						if( $j < 24){

							//Si es menor a 24 horas se declara la variable con el dia anterior y que el reporte trabaje con ese dato.
							$wfecha_ayer1 = time()-(1*24*60*60);
							$wfecha_ayer = date('Y-m-d', $wfecha_ayer1);

							//Solo si las ordenes estan activas para ese centro de costos permitira ir al reporte.
							if($wir_a_ordenes == 'on'){

								$reporte_med = "onclick='ejecutar(\"../../movhos/reportes/RepEntregaMedicamentosHorario.php?wemp_pmla=$wemp_pmla&waccion=a&wservicio=$wcentro_costos&whabitacion=%&whoraini=".gmdate( "G", $j*3600 )."&whorafin=".gmdate( "G", $j*3600 )."&wdiasdif=0&whorario=&fecha=$wfecha_ayer\");'";
								$style_cursor = "cursor:pointer;";

							}


							echo "<td align='center' style='width:50;border:10; $style_cursor' class='fila1' $reporte_med >".gmdate( "G", $j*3600 )."</td>";

						}
						else{

							//Si es menor a 24 horas se declara la variable con el dia actual y que el reporte trabaje con ese dato.
							$wfecha_hoy = date("Y-m-d");

							//Solo si las ordenes estan activas para ese centro de costos permitira ir al reporte.
							if($wir_a_ordenes == 'on'){

							$reporte_med = "onclick='ejecutar(\"../../movhos/reportes/RepEntregaMedicamentosHorario.php?wemp_pmla=$wemp_pmla&waccion=a&wservicio=$wcentro_costos&whabitacion=%&whoraini=".gmdate( "G", $j*3600 )."&whorafin=".gmdate( "G", $j*3600 )."&wdiasdif=0&whorario=&fecha=$wfecha_hoy\");'";
							$style_cursor = "cursor:pointer;";

							}

							echo "<td align='center' style='width:50;border:10; $style_cursor' $reporte_med>".gmdate( "G", $j*3600 )."</td>";
						}
					}
					echo "</tr>";
				}
				echo "</tr>";
			}

			$class = "class='fila".(($i%2)+1)."'";
			$class3 = "class='fila".((($i+1)%2)+1)."'";
			$i++;

			//Consulto si la historia esta en proceso de traslado
		    if ($wtraslado)
			   {
                echo "<tr class='colorAzul4' id='{$valueDatos->historia}-{$valueDatos->ingreso}'>";

				if ($waltaEnProceso)
				   {
					echo "<tr class='fondoAmarillo' id='{$valueDatos->historia}-{$valueDatos->ingreso}'>";
				   }
			   }
			  else
			    if ($waltaEnProceso)
				   {
					echo "<tr class='fondoAmarillo' id='{$valueDatos->historia}-{$valueDatos->ingreso}'>";
				   }
                  else
			        echo "<tr $class id='{$valueDatos->historia}-{$valueDatos->ingreso}'>";

			//Consulto si hay dietas o mjs del perfil o secretaria sin leer
		    $wdietas     = Consultar_dietas($valueDatos->historia,$valueDatos->ingreso, $wcodser);
			$wdietas_Sig = Consultar_dietas($valueDatos->historia,$valueDatos->ingreso, $wcodser_Sig);

			//----------------------
			//------------
			$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

			//----------------------
			//------------
			$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
			$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$hab_select = '';
			$q_hab="SELECT Habcod,Habcco,Habtip,Habtfa
					  FROM	".$wbasedato_movhos."_000020
					 WHERE Habcod='".$valueDatos->codigo."' ";
			$res_hab = mysql_query($q_hab,$conex) or die("Error en el query: ".$q_hab."<br>Tipo Error:".mysql_error());
			$row_hab = mysql_fetch_array($res_hab);
			$hab_select = $row_hab['Habtfa'];

			// Felipe Alvarez Sanchez
			// Valida que el centro de costos permita traslados automaticos
			// Mayo/06/2014

			//-------------------------
			//--Traslados automaticos
			//--Esta parte del codigo consulta la evolucion en la hce y mira el tipo de habitacion donde esta acostado el paciente
			//--y si el tipo es distinto al que esta en movhos , cambia automaticamente el paciente

			/*
			if($valueDatos->cco == $traslados_automaticos){

				// Felipe Alvarez Sanchez
				// julio 13 2016 Se agrega este parametro para limitar  las habitaciones en el select, por las que traiga este parametro y sea mas facil para el usuario
				$habitacionestraladosautomaticos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Hab_TrasladosGestionEnfermeria');

				$html	="<input type='hidden' id='h_thab_ant_".$valueDatos->codigo."' value='".$hab_select."'><select   id='tipo_hab_facturacion_".$valueDatos->codigo."'  onchange='grabar_tipo_habitacion_facturacion(\"".$valueDatos->codigo."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->cco."\")'  >";
				$q_tip_hab  = "SELECT Procod,Pronom  "
							 ."  FROM  ".$wbasedatocliame."_000103"
							 ." WHERE  Protip='H'"
							 ."   AND  Procod IN ('".str_replace(",","','",$habitacionestraladosautomaticos)."')"
                             ." ORDER BY Pronom";
				$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
				if(mysql_num_rows($res_tip_hab)>0)
				{
					$html   .="<option selected value='' >Seleccione un tipo</option>";
				}
				while($row_tip_hab = mysql_fetch_array($res_tip_hab))
				{
					if($row_tip_hab['Procod'] == $hab_select)
						$html   .="<option selected value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
					else
						$html   .="<option  value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
				}
				$html   .="</select>";

			}*/

			if($valueDatos->cco == $cco_trasautomaticos)
			{

				//parametro que indica si los traslados automaticos estan desde hce
				//$activos_desde_hce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tras_thab_hce');
				if ($cco_puedecambiar == 'on')
				{
					// variable que contiene la propiedad del select de tipo de habitacion para la facturacion y hace que se inhabilite
					$condiciondelselect = "disabled='true'";
					$condiciondelselect = '';
				}
				else
				{
					$condiciondelselect ='';
				}



				// Felipe Alvarez Sanchez
				// julio 13 2016 Se agrega este parametro para limitar  las habitaciones en el select, por las que traiga este parametro y sea mas facil para el usuario
				//- Construccion del select de tipo de habitacion para la facturacion.
				// $habitacionestraladosautomaticos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Hab_TrasladosGestionEnfermeria');
				//$habitacionestraladosautomaticos = $cco_Habitaciones;
				$html	="<input type='hidden' id='h_thab_ant_".$valueDatos->codigo."' value='".$hab_select."'>
							<select ".$cco_puedecambiar."    ".$condiciondelselect."  id='tipo_hab_facturacion_".$valueDatos->codigo."'  onchange='grabar_tipo_habitacion_facturacion(\"".$valueDatos->codigo."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->cco."\")'  >";

				$q_tip_hab  = "SELECT Procod,Pronom  "
							 ."  FROM  ".$wbasedatocliame."_000103"
							 ." WHERE  Protip='H'"
							 ."   AND  Procod IN ('".str_replace(",","','",$cco_Habitaciones)."')"
							 ." ORDER BY Pronom";
				$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
				if(mysql_num_rows($res_tip_hab)>0)
				{
					$html   .="<option selected value='' >Seleccione un tipo</option>";
				}
				while($row_tip_hab = mysql_fetch_array($res_tip_hab))
				{
					if($row_tip_hab['Procod'] == $hab_select)
						$html   .="<option selected value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
					else
						$html   .="<option  value='".$row_tip_hab['Procod']."' >".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";
				}
				$html   .="</select>";
				//--------------------------------------------------------------------------
				//--------------------------------------------------------------------------


				//----acontinuacion armo un vector con  los tipos de habitacion facturacion y con los tipos de habitacion de evolucion medica en la historica clinica
				$sql = "SELECT Tiphce,Tipfac
						  FROM ".$wbasedatocliame."_000267
						 WHERE Tipest='on'
						 AND Tipcco='".$valueDatos->cco."'";
				$res_sql = mysql_query($sql,$conex) or die("Error en el query: ".$sql."<br>Tipo Error:".mysql_error());
				$vectorhabitacioneshce = array();
				while($row_sql = mysql_fetch_array($res_sql))
				{
						$vectorhabitacioneshce[$row_sql['Tiphce']] = $row_sql['Tipfac'];
				}
				//--------------------------------------------------------------------------


				//---- parametros para traer la evolucion medica del paciente
				$whcehab='';
				/*
				$hce_formulario 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'formulario_honorarios_neonatos');
				$campoRondasHce 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'campoRondasHce');
				*/


				$hce_formulario   = $cco_formularioHce ;
				$campoRondasHce   = $cco_campoHce;

				// $hce_formulario 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'formulario_honorarios_neonatos');
				// $campoRondasHce 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'campoRondasHce');


				//---------- SELECT DEL TIPO DE HABITACION EN EL QUE ESTA EN MOVHOS 17
				$select_17 = "SELECT Eyrthr
								FROM ".$wbasedato_movhos."_000017
							   WHERE Eyrhis = '".$valueDatos->historia."'
								 AND Eyring = '".$valueDatos->ingreso."'
								 AND Eyrsde = '".$valueDatos->cco."'
								 AND Eyrest !='off'
								 ORDER BY id DESC  LIMIT 1";

				$res_sql_17 = mysql_query($select_17,$conex) or die("Error en el query: ".$select_17."<br>Tipo Error:".mysql_error());
				if($row_sql_17 = mysql_fetch_array($res_sql_17))
				{
					$habitacion_17 = $row_sql_17['Eyrthr'];
				}
				else
				{
					$habitacion_17 ='';
				}

				//----------
				//-- Consulto en hce , el tipo de habitacion que ha puesto el medico y lo comparo con el tipo de habitacion facturacion.
				//-- Para esto se hace una busquda en la tabla hce_00069 y busco el campo referente a la ronda(campo 79 en el formulario hce 000069)  y miro el resultado de esta
				//-- Este resultdo es muy distinto pues las habitaciones estan en tipos de letra O,L,N y las rondas estan en 01,02,03 , pero con ayuda del vector
				//-- construido anteriormente se soluciona este problema
				/*

				*/
				if ($parametroespecialidades =='todas')
				{
					$parametrosqlIn ='';
				}
				else
				{
					$codigosespcialidad = explode(",",$parametroespecialidades);
					$codigosespcialidadfinal = implode("','",$codigosespcialidad);

					$parametrosqlIn =" AND Firrol IN ('".trim($codigosespcialidadfinal)."')  ";
				}

				$sql = "SELECT movdat
						  FROM ".$whce."_".$hce_formulario." as A , ".$whce."_000036 as B
						 WHERE movhis 	= '".$valueDatos->historia."'
						   AND moving 	= '".$valueDatos->ingreso."'
						   AND movpro 	='".$hce_formulario."'
						   AND movcon	='".$campoRondasHce."'
						   AND A.Fecha_data = '".$fecha."'
						   AND movhis     =  Firhis
						   AND moving 	  =  Firing
						   AND Firpro	  =  '".$hce_formulario."'
						   AND A.Fecha_data = B.Fecha_data
						   AND A.Hora_data  = B.Hora_data
						   AND Firfir 		= 'on'
						    ".$parametrosqlIn."
						   ORDER BY A.Hora_data DESC" ;
				$res_sql = mysql_query($sql,$conex) or die("Error en el query: ".$sql."<br>Tipo Error:".mysql_error());

				//$es_diferente ='No entro';
				//-- solo entro si el dia actual hay un movimiento en hce
				if($row_sql = mysql_fetch_array($res_sql))
				{
					$whcehab = explode("-",$row_sql['movdat']);
					$whcehab = $whcehab[0];

					if($fecha == date( "Y-m-d" ))
					{

						//-----------------------
						//----------------------


						if($cco_activoDesdeHce =='on')
						{
							//---hab_select es el tipo de habitacion de la tabla 20
							if($habitacion_17 == $vectorhabitacioneshce[$whcehab])
							{
								//$es_diferente = "nohago";
								$es_diferente = " ";
							}
							else
							{
								if($vectorhabitacioneshce[$whcehab]=='*' )
								{
								   $selecttipohabitacion = "SELECT Temhpd
								                              FROM ".$wbasedatocliame."_000101 , ".$wbasedatocliame."_000024 , ".$wbasedatocliame."_000029
															 WHERE Inghis='".$valueDatos->historia."'
															   AND Ingnin='".$valueDatos->ingreso."'
															   AND Ingcem = Empcod
															   AND Emptem = Temcod	" ;
								   $res_select = mysql_query($selecttipohabitacion,$conex) or die("Error en el query: ".$selecttipohabitacion."<br>Tipo Error:".mysql_error());

								   $habitacionseleccion ='';
								   if($row_sql = mysql_fetch_array($res_select))
								   {
									   // la habitacion seleccion sale
									   $habitacionseleccion = $row_sql['Temhpd'];
									   if($habitacion_17 == $habitacionseleccion)
									   {
										  // $es_diferente = "habitacion no tengo que hacer".$hab_select."----".$habitacionseleccion."<br>Habitacion 17: ".$habitacion_17."<div  class='cambio_habitacion_automatico' historia='".$valueDatos->historia."' ingreso='".$valueDatos->ingreso."' habitacion='".$valueDatos->codigo."'  cco='".$valueDatos->cco."' tipo_habitacion_cambio='".$vectorhabitacioneshce[$whcehab]."' >";
										  $es_diferente = " ";
									   }
										else
									   {
											if($habitacionseleccion=='')
											{

											}
											else
												$es_diferente = "<div  class='cambio_habitacion_automatico' historia='".$valueDatos->historia."' ingreso='".$valueDatos->ingreso."' habitacion='".$valueDatos->codigo."'  cco='".$valueDatos->cco."' tipo_habitacion_cambio='".$habitacionseleccion."' >";
									   }
								   }


								}
								else
								{
									$es_diferente = "<div  class='cambio_habitacion_automatico' historia='".$valueDatos->historia."' ingreso='".$valueDatos->ingreso."' habitacion='".$valueDatos->codigo."'  cco='".$valueDatos->cco."' tipo_habitacion_cambio='".$vectorhabitacioneshce[$whcehab]."' >";
								}
							}
						}

					}

					// }
				}
				echo "<td ".$cco_esvisible.">".$html." ".$es_diferente."</td>";
			}
			//---------------


				//Si es centor de costos de urgencias mostrara la columna semaforo.
				if($wurgencias)
				{
					$color_celda = "";

					if($wfecha_hora_term_consul != '0000-00-00 00:00:00'){

						//Verifica cuantos minutos han transcurrido entre la hora de llegada del paciente de la tabla 18 y la fecha y hora actual
						$minutos = (strtotime(date("Y-m-d H:i:s")) - strtotime($wfecha_hora_term_consul)) / 60;
						$minutos = abs($minutos);
						$minutos = floor($minutos);
						$tiempo_en_segundos = $minutos * 60;
						$formato_horas = tiempo_transcurrido($tiempo_en_segundos);

						$wtiempo_minimo_aux = $wtiempo_minimo * 60;
						$wtiempo_maximo_aux = $wtiempo_maximo * 60;

						//Si es mayor a 2 horas pinta el fondo amarillo
						if($minutos >= $wtiempo_minimo_aux){
							$amarillo = "amarillo";
							$color_celda = 'style="background-color: #FCEF69; " ';

						}

						if($minutos >= $wtiempo_maximo_aux){  //Si es mayor a 6 horas pinta el fondo rojo


							$color_celda = 'style="background-color: #FDCB7F; "';

						}

					}else{

						$formato_horas = '';

					}

					echo "<td $color_celda>$formato_horas</td>";

				}


			//====================================================================================================================
			//Servicio de Alimentación
			//====================================================================================================================

			if ($wdietas['tiene_dieta'])
			   {
			    $path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser."&wcco=".$valueDatos->cco."&wfec=".$fecha."&whistoria_c=".$valueDatos->historia."&wingreso_c=".$valueDatos->ingreso."&wzona=".$sala;;

				if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
				   echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
				  else
                     echo "<td align='center' '".$path_dietas_seractual_aux."'>";

				echo "<span class='textoborde'>".$wdietas['patron_dieta']."</span>";
				echo "<br><b><font size=1>".$wservicio."</font></b>";
				echo "</td>";
			   }
			  else
			     {
				  $path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser."&wcco=".$valueDatos->cco."&wfec=".$fecha."&whistoria_c=".$valueDatos->historia."&wingreso_c=".$valueDatos->ingreso."&wzona=".$sala;;
				  if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
				     echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
					else
                      echo "<td align='center' '".$path_dietas_seractual_aux."'>";

				  //Valida si el servicio es de urgencias, si es asi, no pinta la equis roja que le dice a la enfermera que la dieta no esta solicitada. Jonatan 14 Agosto 2014
				  if($wurgencias == '1'){
				  $imagen_sin_dieta = "";
				  }else{
				  $imagen_sin_dieta = "<img src='/matrix/images/medical/root/borrar.png'>";
				  }

				  echo "<span class='blink'>$imagen_sin_dieta</span>";
				  echo "<br><b><font size=1>".$wservicio."</font></b>";
				  echo "</td>";
                 }

            if ($wdietas_Sig['tiene_dieta'])
			   {

				$path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser_Sig."&wcco=".$valueDatos->cco."&wfec=".$fecha."&whistoria_c=".$valueDatos->historia."&wingreso_c=".$valueDatos->ingreso."&wzona=".$sala;
				if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
				   echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
				  else
                   echo "<td align='center' '".$path_dietas_seractual_aux."'>";

				echo "<span class='textoborde'>".$wdietas_Sig['patron_dieta']."</span>";
				echo "<br><b><font size=1>".$wservicio_Sig."</font></b>";
				echo "</td>";

			   }
              else
				 {
				  $path = "Dietas.php?wemp_pmla=".$wemp_pmla."&wser=".$wcodser_Sig."&wcco=".$valueDatos->cco."&wfec=".$fecha."&whistoria_c=".$valueDatos->historia."&wingreso_c=".$valueDatos->ingreso."&wzona=".$sala;
				  if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
				     echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
					else
                      echo "<td align='center' '".$path_dietas_sersgte_aux."'>";

				  //Valida si el servicio es de urgencias, si es asi, no pinta la equis roja que le dice a la enfermera que la dieta no esta solicitada. Jonatan 14 Agosto 2014
				  if($wurgencias == '1'){
				  $imagen_sin_dieta = "";
				  }else{
				  $imagen_sin_dieta = "<img src='/matrix/images/medical/root/borrar.png'>";
				  }

				  echo "<span class='blink'>$imagen_sin_dieta</span>";
				  echo "<br><b><font size=1>".$wservicio_Sig."</font></b>";
				  echo "</td>";
				 }
			//====================================================================================================================


				$path_destino = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valueDatos->historia."&wingreso=".$valueDatos->ingreso."&wfecha=".$fecha."&editable=on&et=on";
				$path_destino_consulta = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valueDatos->historia."&wingreso=".$valueDatos->ingreso."&wfecha=".$fecha."&editable=off&et=on";
				$wmsjSecret  = consultarProcedimientosSinLeer( $conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso );
				$tooltipkardex = "";
				$wmsjkardex  = consultarMensajesSinLeer( $conex, $wbasedato, "kardex", $valueDatos->historia, $valueDatos->ingreso );
				$funcion_javascript = "ejecutar";
				$fondo = "";

				//Verifica si el centro de costos va a ordenes, y ademas si el paciente ha iniciado con el programa de ordenes electronicas.
				if($wir_a_ordenes == 'on' and $paciente_ordenes == 'on'){

					$origenGestion = "&esDeAyuda=on";
					if( empty( $ccoayuda ) ){
						$origenGestion = "&esDeAyuda=off";
					}

					$funcion_javascript = "abrir_ordenes";
					$origen = "Ordenes";
					$path_destino = "/matrix/hce/procesos/ordenes.php?wemp_pmla=".$wemp_pmla."&wcedula=".$valueDatos->documento."&wtipodoc=".$valueDatos->tid."&hce=on&programa=gestionEnfermeria&et=on&pgr_origen=gestionEnfermeria".$origenGestion;
					$path_destino_consulta = "/matrix/hce/procesos/ordenes.php?wemp_pmla=".$wemp_pmla."&wcedula=".$valueDatos->documento."&wtipodoc=".$valueDatos->tid."&hce=on&programa=gestionEnfermeria&et=on&editable=off&pgr_origen=gestionEnfermeria".$origenGestion;
					$wmsjkardex_info = pendientes_ordenes($conex, $wbasedato, "kardex", $valueDatos->historia, $valueDatos->ingreso, $whce);
					$fondo = "background-color:#3CB648;";

					$wmsjkardex = $wmsjkardex_info['total_pendientes']['cuantos'];
					unset($wmsjkardex_info['total_pendientes']);

					foreach($wmsjkardex_info as $key => $value){
						$nombre_med = "";
						if(count($value['cuales']) > 0){
							foreach($value['cuales'] as $key1 => $value1){

								$estado_log = $array_log[$key1];

								$nombre_med .= "<u>".$estado_log['Logdes']."</u><br>";
								foreach($value1 as $key2 => $value2){

								$nombre_med .= $value2."<br>";

								}
							}
						}

						$tooltipkardex .= $value['origen']."<br>".$nombre_med."<br>";
					}

				}

			if (!isset($wsp) or $wsp!="on"){

				//Mensajes generados desde el Perfil (Servicio Farmaceutico) Sin Leer
				//$path = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valueDatos->historia."&wingreso=".$valueDatos->ingreso."&wfecha=".$fecha."&editable=on&et=on";
				if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
				   echo "<td align='center' style='cursor: pointer; $fondo' onclick='".$funcion_javascript."(".chr(34).$path_destino.chr(34).")'>";
				  else
					 echo "<td align='center' style='cursor: pointer; $fondo' onclick='".$funcion_javascript."(".chr(34).$path_destino_consulta.chr(34).")'>";

				if ((int)$wmsjkardex > 0)
				   {
					echo "<b><span class='blink pendientes' title='".$tooltipkardex."'><class=tipo3V>(".$wmsjkardex.")</span></b>";
				   }
				   else
					  {
					   echo " ";
					  }
				echo "</td>";

			}
			//Procedimientos o gestiones de la secretaria a los procedimientos, sin leer
			//$path = "entrega_turnos_secretaria.php?wemp_pmla=".$wemp_pmla."&key=".$key."&whis=".$valueDatos->historia."&wing=".$valueDatos->ingreso."&wcco=".$valueDatos->cco."&wfec=".$fecha;
			//$path = "/matrix/movhos/procesos/generarKardex.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$valueDatos->historia."&wingreso=".$valueDatos->ingreso."&wfecha=".$fecha."&editable=on&et=on";
			if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
			   echo "<td align='center' style='cursor: pointer' onclick='ejecutar(".chr(34).$path_destino.chr(34).")'>";
			  else
                 echo "<td align='center'>";
			if ($wmsjSecret > 0)
			   {
			    echo "<b><span class='blink'>".$wmsjSecret."</span></b>";
			   }
			   else
			      {
                   echo " ";
				  }
			echo "</td>";

			//Si el centro de costos es de urgencias mostrara checkbox para poner en alta en proceso al paciente.
			if($wurgencias){

				$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;

				//Si el paciente de urgencias tiene solicitud de cama, se inactiva el cajon de alta en proceso.
				//Si el paciente esta en proceso de traslado el checkbox de alta en proceso estara inactivo.
				if($wtraslado or $waltaEnProceso or array_key_exists($his_ing, $wprocesos_traslado_pac_urg)){

					$westado_checkbox = "disabled";
				}

				echo "<td align=center><input type=checkbox class='msg_alta_proceso' title='Poner de Alta' id='alta_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcaraltaenproceso($valueDatos->historia,$valueDatos->ingreso,$i, \"$wtraslado\")'></td>";

			}
			elseif($wcirugia){

				//Cajon de alta definitiva
				//Si el paciente es esta ubicado en un centro de costos ambulatorio el codigo de la habitacion es Cx (Cirugia)
				if($valueDatos->codigo == 'Cx' ){

					$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;
					$mensaje_alta_def = "";

					if(is_array($wprocesos_traslado_pac_cir)){

						//Verifico si el paciente tiene una solicitud de cama, si es asi no imprime el cajon de alta definitiva.
						if(array_key_exists($his_ing, $wprocesos_traslado_pac_cir)){

							$codCcoCirugia = consultarCcoCirugia();
							$hab_destino = $wprocesos_traslado_pac_cir[$his_ing]['habitacionDestino'];
							$id_solicitud = $wprocesos_traslado_pac_cir[$his_ing]['id_solicitud_cama'];

							//Si tiene habitacion asignada no imprime el cajon de alta definitiva.
							if($hab_destino != ''){
								echo "<td></td>"; //Cajon alta definitiva cirugia
								echo "<td></td>"; //Cajon muerte cirugia
							}else{

								//Busca si tiene medicamentos pendientes por aplicar, descartar o devolver.
								$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, $hab_destino, $id_solicitud, $codCcoCirugia, ''); //En esta funcion se muestran todos los articulos que tiene saldo

								//Si tiene medicamentos pendientes por aplicar o devolver pero no tiene habitacion asignada, no imprime el cajon de alta definitiva.
								if($datos_med_pac['wnum_art'] > 0){
									echo "<td></td>";
									echo "<td></td>";
								}else{

									$validar_mercado_liq = validar_mercado_liq($valueDatos->turno_cir);

									if($validar_mercado_liq == 'off'){
										$mensaje_alta_def = "<div style='white-space:nowrap'>Mercado sin liquidar</div>";
									}

									echo "<td align=center>$mensaje_alta_def<input type=checkbox class='msg_alta_definitiva' title='Poner alta definitiva' id='alta_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcaraltadefinitiva($valueDatos->historia,$valueDatos->ingreso,$i, \"$wtraslado\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\")'></td>";
									echo "<td align=center><input type=checkbox disabled class='msg_muerte' title='Muerte' id='muerte_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcarmuerte_hospitalizacion(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\", \"$whab_actual\", \"\", \"$valueDatos->cco\")'></td>";
								}
							}
						//Si no tiene solicitud de cama se imprime le cajon de alta definitiva.
						}else{

							$codCcoCirugia = consultarCcoCirugia();

							//Busca si tiene medicamentos pendientes por aplicar, descartar o devolver.
							$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, '', '', $codCcoCirugia, ''); //En esta funcion se muestran todos los articulos que tiene saldo

							//Si tiene medicamentos pendientes por aplicar o devolver, no imprime el cajon de alta definitiva.
							if($datos_med_pac['wnum_art'] > 0){
								echo "<td></td>";
								echo "<td></td>";
							}else{

								$validar_mercado_liq = validar_mercado_liq($valueDatos->turno_cir);

								if($validar_mercado_liq == 'off'){
									$mensaje_alta_def = "<div style='font-size:10px; color: yellow;'>Mercado sin liquidar</div>";
								}

								echo "<td align=center>$mensaje_alta_def<input type=checkbox class='msg_alta_definitiva' title='Poner alta definitiva' id='alta_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcaraltadefinitiva($valueDatos->historia,$valueDatos->ingreso,$i, \"$wtraslado\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\")'></td>";
								echo "<td align=center><input type=checkbox class='msg_muerte' title='Muerte' id='muerte_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcarmuerte_hospitalizacion(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\", \"$whab_actual\", \"\", \"$valueDatos->cco\")'></td>";
							}

						}
					}else{

						//Busca si tiene medicamentos pendientes por aplicar, descartar o devolver.
						$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, '', '', $codCcoCirugia, ''); //En esta funcion se muestran todos los articulos que tiene saldo

						//Si tiene medicamentos pendientes por aplicar o devolver, no imprime el cajon de alta definitiva.
						if($datos_med_pac['wnum_art'] > 0){
							echo "<td></td>";
							echo "<td></td>";
						}else{
							echo "<td align=center><input type=checkbox class='msg_alta_definitiva' title='Poner alta definitiva' id='alta_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcaraltadefinitiva($valueDatos->historia,$valueDatos->ingreso,$i, \"$wtraslado\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\")'></td>";
							echo "<td align=center><input type=checkbox $checked_proc_tras class='msg_muerte' title='Muerte' id='muerte_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcarmuerte_hospitalizacion(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\", \"$whab_actual\", \"\", \"$valueDatos->cco\")'></td>";
						}
					}
				//Si el paciente esta en una habitacion no se imprime el cajon de alta definitiva.
				}else{

					echo "<td></td>"; //Cajon vacio de alta definitiva.
					echo "<td></td>"; //Cajon vacio de muerte.

				}
			}
			elseif($wccohos=='on'){

				$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;
				$ubicacionPaciente = new ingresoPacientesDTO();
				$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);
				$whab_actual = $ubicacionPaciente->habitacionActual;
				$solicitud_hab = $wpacientes_por_entrega[$his_ing]['hab_asignada']; //Habitacion asignada

				$checked_alta_proc = "";
				$checked_proc_tras = "";
				$whabilitar_altadef = "";

				if($ubicacionPaciente->altaEnProceso == 'on'){
					$checked_alta_proc = 'checked';
				}

				if($ubicacionPaciente->enProcesoTraslado == 'on'){
					$checked_proc_tras = 'disabled';
				}

				//Si tiene solicitud de habitacion los cajones de alta en proceso y muerte deben estar inactivos.
				// if(count($solicitud_hab) > 0){
				if($solicitud_hab != ""){
					$checked_proc_tras = 'disabled';
				}

				if($waux != 'on' ||  $esRolParaDarAlta ){

					$saldo_insumos_auxiliar = traer_insumos_enfermeria($valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo);

					$fondo_saldo_insumos = "";
					$title_saldos = "";

					$horario_alerta_saldo_insumos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HorarioAlertaSaldoInsumos');

					$hora_actual = time();
					$datos_hora_saldos = explode(",",$horario_alerta_saldo_insumos);

					$manana = explode("-",$datos_hora_saldos[0]);
					$hora_dia_inicial = $manana[0].":00:00";
					$hora_dia_final = $manana[1].":00:00";
					$hora_dia_inicial_unix = strtotime($hora_dia_inicial);
					$hora_dia_final_unix = strtotime($hora_dia_final);

					$tarde = explode("-",$datos_hora_saldos[1]);
					$hora_tarde_inicial = $tarde[0].":00:00";
					$hora_tarde_final = $tarde[1].":00:00";
					$hora_tarde_inicial_unix = strtotime($hora_tarde_inicial);
					$hora_tarde_final_unix = strtotime($hora_tarde_final);

					//Si el paciente tiene saldo en insumos y esta dentro de la hora para el parametro HorarioAlertaSaldoInsumos mostrara el cajon de fondo rojo y con parpadeo.
					if(($hora_actual > $hora_dia_inicial_unix and $hora_actual < $hora_dia_final_unix) or ($hora_actual > $hora_tarde_inicial_unix and $hora_actual < $hora_tarde_final_unix) ){

						if($saldo_insumos_auxiliar['saldo_insumos'] > 0){
							$fondo_saldo_insumos = "fondoRojo blink msg_alta_definitiva";
							$title_saldos = "Paciente con saldo en el botiquín.";
						}

					}

					//Si el paciente esta en proceso de alta y el paciente tiene saldo en insumos muestra el cajon rojo.
					if($valueDatos->altaEnProceso){
						if($saldo_insumos_auxiliar['saldo_insumos'] > 0){
							$fondo_saldo_insumos = "fondoRojo blink msg_alta_definitiva";
							$title_saldos = "Paciente con saldo en el botiquín.";
							$whabilitar_altadef = "disabled"; //No se activa el alta definitiva hasta que los saldos de insumos esten en cero.
						}else{
							$whabilitar_altadef = "";
						}
					}

					//Si el paciente tiene muerte activa no se puede quitar el alta en proceso y el cajon de muerte estará inactivo.
					if($wmuerte == 'on'){
							//Si tiene muerte y saldo en insumo se inactiva el alta definitiva
							if($saldo_insumos_auxiliar['saldo_insumos'] > 0){
								$whabilitar_altadef = "disabled"; //No puede dar alta definitiva si aun tiene saldo en insumos.
								$mensaje_saldo_insumos = "<font size=1 color=red>con saldo en insumos</font>";
							}

							$estado_checked_alta_proc = "disabled";
							$cajon_muerte = "checked";
							$checked_muerte = "disabled";
							$mensaje_muerte = "<font size=1 color=red> Fallecido </font>".$mensaje_saldo_insumos;
						}

					//Verifico si se puede colocar el cajon de alta definitiva activo
					  $q = " SELECT COUNT(*) "
						  ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022 "
						  ."  WHERE Ubihis = '".$valueDatos->historia."'"
						  ."    AND Ubiing = '".$valueDatos->ingreso."'"
						  ."    AND Ubihis = Cuehis "
						  ."    AND Ubiing = Cueing "
						  ."    AND Ubialp = 'on' "
						  ."    AND Cuegen = 'on' "
						  ."    AND Cuepag = 'on' "
						  ."    AND Cuecok = 'on' ";
					  $resalt = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					  $rowalt = mysql_fetch_array($resalt);

						//Si es mayor a cero es porque el paciente puede ser dado de alta
					  if ($rowalt[0] == 0){
						  $whabilitar_altadef = 'disabled';
						 }

					//Alta en proceso
					echo "<td align=center id='td_alta_proceso_$valueDatos->historia-$valueDatos->ingreso'><input type=checkbox $checked_proc_tras $checked_alta_proc $estado_checked_alta_proc class='msg_alta_proceso' title='Poner de Alta' id='alta_proceso_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcaraltaenproc_hospitalizacion(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\")'></td>";

					if (!isset($wsp) or $wsp!="on"){
						//Alta definitiva
						echo "<td align=center class='$fondo_saldo_insumos' title='$title_saldos'><input type=checkbox $checked_proc_tras $whabilitar_altadef class='msg_alta_proceso' title='Poner de Alta Definitiva' id='alta_definitiva_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcaraltadef_hospitalizacion(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\", \"$whab_actual\", \"\", \"$valueDatos->cco\")'></td>";
						//Muerte
						echo "<td align=center><input type=checkbox $cajon_muerte $checked_muerte class='msg_muerte' title='Muerte' id='muerte_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='marcarmuerte_hospitalizacion(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\", \"$whab_actual\", \"\", \"$valueDatos->cco\")'></td>";
					}
				}

			}
			elseif($ccoayuda != ''){

				//Alta definitiva
				$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);
				$wtipoccoayuda = consultarCentroCostoAyuda($ubicacionPaciente->servicioActual);
				$wser_actual = $ubicacionPaciente->servicioActual;
				if( !empty( $ubicacionPaciente->servicioTemporal ) ){
					$wser_actual = $ubicacionPaciente->servicioTemporal;
				}

				$whab_actual = $ubicacionPaciente->habitacionActual;





				//Busca si tiene medicamentos pendientes por aplicar, descartar o devolver.
				$datos_med_pac_saldos_ayu = Detalle_ent_rec_saldos_ayuda_dx($valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $wser_actual ); //En esta funcion se muestran todos los articulos que tiene saldo




				//Valida si el paciente esta hospitalizado o esta en un centro de costos de ayuda diagnostica.
				if( $valueDatos->altaPorDosisUnica )
				{
					if( $datos_med_pac_saldos_ayu['wnum_art'] == 0 && $wtipoccoayuda != 'on' ){
						if( $valueDatos->tieneMedicamentosSinAplicarOJusitificarAyudaDxCx ){
							echo "<td class='fondorojo' style='text-align:center;'>Tiene medicamentos por aplicar o justificar</td>";
						}
						else{
							echo "<td align=center><input type=checkbox $checked_proc_tras $whabilitar_altadef class='msg_alta_proceso' title='Liberar paciente' id='alta_definitiva_$valueDatos->historia-$valueDatos->ingreso' $westado_checkbox onclick='salidaservicioayuda(\"$valueDatos->historia\",\"$valueDatos->ingreso\", \"$wtraslado\", \"$westado_altaEnProceso\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\", \"$valueDatos->id_tabla18\", \"$whab_actual\", \"\", \"$wser_actual\")'></td>";
						}
					}
					else{

						if($datos_med_pac_saldos_ayu['wnum_art'] > 0 ){

							echo "<td align=center>";
							echo $articulos_pendientes = "<a style='font-size:6pt;cursor: pointer;' id='saldos_med_$his_ing' onclick='mostrar_med_pend_saldo(\"medicamentos_pendientes_".$his_ing."_saldo\");'><img src='/matrix/images/medical/root/pac_con_med.png' width='20' height='20'  class='mover_pac' title='Articulos con saldo asociados al paciente'></a>";
							echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_".$his_ing."_saldo' title='Pendientes por aplicar'>";
							echo $datos_med_pac_saldos_ayu['html'];
							echo "</div>";
							echo "</td>";
						}
						else{
							if( $valueDatos->tieneMedicamentosSinAplicarOJusitificarAyudaDxCx ){
								echo "<td class='fondorojo' style='text-align:center;'>Tiene medicamentos por aplicar o justificar</td>";
							}
							else{
								echo "<td align=center><input type=checkbox class='msg_alta_definitiva' title='Poner alta definitiva' id='alta_$valueDatos->historia-$valueDatos->ingreso' onclick='marcaraltadefinitiva($valueDatos->historia,$valueDatos->ingreso,$i, \"$wtraslado\", \"$valueDatos->nombre\", \"$wbasedato\", \"$whce\", \"$wuser\")'></td>";
							}
						}
					}
				}
				else{
					echo "<td class='fondorojo' style='text-align:center;'>Hay medicamentos<br>sin aplicar</td>";
				}
			}


			//Sala
			echo "<td align=left style='height:40px;'><b>";

			//Se verifica si el centro de costos que se quiere ver es urgencias.
			if($wurgencias){

				$articulos_pendientes = "";

				//Del arreglo de pacientes atendidos y activos, extraigo la descripcion de la conducta y si esta en consulta osea tomado por un medico.
				$wconducta = $pacientes_atendidos_activos[$valueDatos->historia.$valueDatos->ingreso]['desc_conducta'];	//Descripcion de la zona.
				$wen_consulta = $pacientes_atendidos_activos[$valueDatos->historia.$valueDatos->ingreso]['enconsulta'];	//Si esta variable esta en on el paciente esta en consulta, osea esta siendo visto por un medico.

				//Si el paciente esta siendo visto por un medico se mostrara que el paciente esta en consulta, sino en sala de espera.
				$estado_atencion_paciente = ($wen_consulta == 'on') ? "En Consulta" : "En sala de espera";

				//Si el paciente no tiene conducta, la variable estado_atencion_paciente le dara valor.
				if($wconducta == '') {
					$wconducta = $estado_atencion_paciente;
				}

				//Si el paciente no tiene conducta los seleccionadores se mostraran inactivos.
				$disabled_sala = (trim($wconducta) != '' and trim($wconducta) != 'NO APLICA') ? "" : "disabled";

				//Busca si tiene medicamentos pendientes por aplicar, descartar o devolver.
				$datos_med_pac_saldos = Detalle_ent_rec_saldos('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, $hab_destino, $id_solicitud, $codCcoCirugia, ''); //En esta funcion se muestran todos los articulos que tiene saldo

				if($datos_med_pac_saldos['wnum_art'] > 0 ){

					$articulos_pendientes = "<a style='font-size:6pt;cursor: pointer;' id='saldos_med_$his_ing' onclick='mostrar_med_pend_saldo(\"medicamentos_pendientes_".$his_ing."_saldo\");'><img src='/matrix/images/medical/root/pac_con_med.png' width='20' height='20'  class='mover_pac' title='Articulos con saldo asociados al paciente'></a>";
					echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_".$his_ing."_saldo' title='Pendientes por aplicar'>";
					echo $datos_med_pac_saldos['html'];
					echo "</div>";
				}

				echo "<div align=center>".$wconducta." ".$articulos_pendientes."<hr></div>";

				$ubicacion_actual = $array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habcod']; //Busca en el array de pacientes con cubiculo si tiene ubicacion actual.

				//Si el paciente tiene sala asignada en el arreglo o la variable $wconsala tiene informacion muestra el seleccionador de salas.
				if($array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod'] != "" or $wconsala != ""){

					//Si el dato de ubicacion que viene de la consulta es vacio quiere decir que este paciente si tiene sala y cubiculo asignado.
					if($wconsala == ''){
					 $wconsala = $array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod'];
					}

					echo "<select id='sala$i' name='sala$i' $disabled_sala onchange='AsignarZona($valueDatos->historia, $valueDatos->ingreso, $i, \"$wconsala\", \"$ubicacion_actual\")'>";

						if(is_array($array_zonas)){
							foreach($array_zonas as $key => $row_sala){

								$sala_seleccionada = "";
								//Esta validacion permite seleccionar la sala asignada al paciente, en el listado de salas.
								if($array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod'] == $row_sala['Arecod']){

									$sala_seleccionada = "selected";
								}

								//Si el dato es de la variable $wconsala entonces seleccionara ese.
								if($wconsala == $row_sala['Arecod']){

									$sala_seleccionada = "selected";
								}

								echo "<option value='".$row_sala['Arecod']."' $sala_seleccionada>".$row_sala['Aredes']."</option>";
							}
						}
					echo "</select>";

					}else{
						//En caso de no tener sala ni cubiculo asignado en la tabla 20 de movhos mostrara la palabra Urg
						echo "<div align=center>".$valueDatos->codigo."</div>";

					}
			}
			else{

				if($wcirugia){

					$ubi_pac = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);
					if($ubi_pac->habitacionActual != ''){
						echo "<div align=center>".$ubi_pac->habitacionActual."</div>";
					}else{
						echo "<div align=center>".$valueDatos->codigo."</div>";
					}
				}else{
					//Si el paciente no es de urgencias mostrara la ubicacion actual en el centro de costos seleccionado.
					echo "<div align=center>".$valueDatos->codigo." <br> ".$mensaje_muerte."</div>";
				}
			}

			//Cubiculo.
			if($wurgencias){

				$array_cubiculos_aux = array();
				$wconducta = $pacientes_atendidos_activos[$valueDatos->historia.$valueDatos->ingreso]['desc_conducta'];	//Descripcion de la zona.
				$disabled_cubiculo = (trim($wconducta) != '' and trim($wconducta) != 'NO APLICA') ? "" : "disabled";

				//Si el paceinte tiene sala asignada en el arreglo o la variable $wconsala tiene informacion muestra el seleccionador de salas.
				if($array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod'] != "" or $wconsala != ""){

				echo "<span id='dato_cubiculos$i'>";
				echo "<select id='cubiculo$i' name='wconducta$i' $disabled_cubiculo onchange='reasignarCubiculo($valueDatos->historia, $valueDatos->ingreso, $i, \"$ubicacion_actual\" )'>";

				//Si la sala esta asignada al paciente entonces tomo ese dato y solo muestro en el select los que tengan esa sala asignada de acuerdo al key del array.

				if($array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod'] != ""){

					 echo "<option value='".$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habcod']."' selected>".$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habcpa']."</option>";

					$array_cubiculos_aux = $array_cubiculos_fisicos[$array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod']];

					//Si todas las ubicaciones fisicas estan ocupadas tomara el arreglo de las virtuales.
					if(count($array_cubiculos_aux) == 0){

						$array_cubiculos_aux = @$array_cubiculos_virtuales[$array_zonas[$array_his_cub[$valueDatos->historia.$valueDatos->ingreso]['habzon']]['Arecod']];
					}

					 if(is_array($array_cubiculos_aux)){

						 foreach ($array_cubiculos_aux as $key_cub => $row_cub){

							 echo "<option value='".$row_cub['Habcod']."'>".$row_cub['Habcpa']."</option>";

							}
					 }

					//Si el paciente tiene sala asignada se muestra todo el listado de los cubiculos disponibles de esa sala.
					}elseif($wconsala != ""){

						if(is_array($array_cubiculos_aux)){

							$array_cubiculos_aux = $array_cubiculos_fisicos[$wconsala];

							//Si todas las ubicaciones fisicas estan ocupadas tomara el arreglo de las virtuales.
							if(count($array_cubiculos_aux) == 0){

								$array_cubiculos_aux = $array_cubiculos_virtuales[$wconsala];

							}

							echo "<option value=''></option>";

							foreach($array_cubiculos_aux as $key_cub => $row_cub){

								 echo "<option value='".$row_cub['Habcod']."'>".$row_cub['Habcpa']."</option>";

								 }

						}
					}

				echo "</select>";
				echo "</span>";
				echo "</b>";

				}

			}

			echo "</td>";

			//Impresion de informacion en el cajon de traslado - solicitud de cama.
			switch(1){

				case($ccoayuda != ''):

					$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;

					if(is_array($wprocesos_traslado_pac_ayuda)){

						if(array_key_exists($his_ing, $wprocesos_traslado_pac_ayuda)){

							$hab_destino = $wprocesos_traslado_pac_ayuda[$his_ing]['hab_asignada'];
							$id_solicitud = $wprocesos_traslado_pac_ayuda[$his_ing]['idsolicitud'];
							$wser_destino = $todoslashab[$hab_destino]['Habcco']; //Centro de costos de la habitacion asignada

							$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);
							$wser_actual = $ubicacionPaciente->servicioActual;

							$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, $hab_destino, $id_solicitud, $codCcoCirugia, 'EntregaDesdeCirugiaAPiso'); //En esta funcion se muestran todos los articulos que tiene saldo

							//Si hay mas de un medicamento pinta en un div oculto la informacion de los articulos para aplicar o descartar.
							if($datos_med_pac['wnum_art'] > 0){

								echo "<td align=center class='mover_pac' title='Medicamentos pendientes <br>por aplicar o devolver' onclick='mostrar_med_pend(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'>";
								echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'></a>";
								echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
								echo $datos_med_pac['html'];
								echo "</div>";
								echo "</td>";

							}else{

								if(trim($hab_destino) != ''){
									echo "<td align=center id='td_traslado_".$valueDatos->historia."-".$valueDatos->ingreso."' class='mover_pac' title='Entregar a la habitaci&oacuten $hab_destino' onclick='moverPacHosp( \"".$wemp_pmla."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"".$wser_actual."\", \"".$wccohos."\", \"".$id_solicitud."\", \"".$wccoapl."\", \"".$hab_destino."\", \"\", \"".$wser_destino."\", \"Ent\", \"\", \"\")' style='cursor:pointer;'><b>Entregar</b><br><img src='/matrix/images/medical/root/pac_con_cama.png'><br><b>Hab. $hab_destino</b></td>";
								}else{
									echo "<td align=center class='mover_pac' title='Con solicitud de habitación Nro. $id_solicitud <br>sin cama asignada' style='cursor:pointer;'><img src='/matrix/images/medical/root/pac_con_sol_cama.png'></td>";
								}

							}

						}else{
								//Solicitud de cama
								$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);
								$wtipoccoayuda = consultarCentroCostoAyuda($ubicacionPaciente->servicioActual);
								$wser_actual = $ubicacionPaciente->servicioActual;

								if($wtipoccoayuda != 'on' ){
									echo "<td align=center></td>";
								}else{
									echo "<td align=center class='mover_pac' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Solicitar Cama' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$wser_actual\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";

								}


							}

					}


				break;

				case($wcirugia):

					$codCcoCirugia = consultarCcoCirugia();
					$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;

					if(is_array($wprocesos_traslado_pac_cir)){

						if(array_key_exists($his_ing, $wprocesos_traslado_pac_cir)){

							$hab_destino = $wprocesos_traslado_pac_cir[$his_ing]['habitacionDestino'];
							$id_solicitud = $wprocesos_traslado_pac_cir[$his_ing]['id_solicitud_cama'];

							$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, $hab_destino, $id_solicitud, $codCcoCirugia, 'EntregaDesdeCirugiaAPiso'); //En esta funcion se muestran todos los articulos que tiene saldo

							//Si hay mas de un medicamento pinta en un div oculto la informacion de los articulos para aplicar o descartar.
							if($datos_med_pac['wnum_art'] > 0){

								echo "<td align=center class='mover_pac' title='Medicamentos pendientes <br>por aplicar o devolver' onclick='mostrar_med_pend(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'>";
								echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'></a>";
								echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
								echo $datos_med_pac['html'];
								echo "</div>";
								echo "</td>";

							}else{

								$hab_destino = $wprocesos_traslado_pac_cir[$his_ing]['habitacionDestino'];
								$id_solicitud = $wprocesos_traslado_pac_cir[$his_ing]['id_solicitud_cama'];

								if(trim($hab_destino) != ''){
									if($valueDatos->codigo == 'Cx'){
										echo "<td align=center class='mover_pac' title='Entregar a la habitaci&oacuten $hab_destino' onclick='EntregaDesdeCirugiaAPiso(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\",\"$hab_destino\",\"$id_solicitud\");' style='cursor:pointer;'><b>Entregar</b><br><img src='/matrix/images/medical/root/pac_con_cama.png'><br><b>Hab. $hab_destino</b></td>";
									}else{
										echo "<td></td>";
									}
								}else{
									echo "<td align=center class='mover_pac' title='Con solicitud de habitación Nro. $id_solicitud <br>sin cama asignada' style='cursor:pointer;'><img src='/matrix/images/medical/root/pac_con_sol_cama.png'></td>";
								}

							}
						}else{

							//Valida que solo pueda solicitar habitacion para pacientes que no estan hospitalizados.
							if($valueDatos->codigo == 'Cx' ){

								$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, '', '', $codCcoCirugia, ''); //En esta funcion se muestran todos los articulos que tiene saldo

								if($datos_med_pac['wnum_art'] > 0){

									echo "<td align=center class='mover_pac' title='Medicamentos pendientes <br>por aplicar o devolver' onclick='mostrar_med_pend(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'>";
									echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'></a>";
									echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
									echo $datos_med_pac['html'];
									echo "</div>";
									echo "</td>";

								}else{
									//Solicitud de cama
									echo "<td align=center class='mover_pac' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Solicitar Cama' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
								}
							}else{

								//Si esta hospitalizado y no tiene medicamentos pendientes no imprime nada.
								echo "<td></td>";
							}

						}
					//En caso de que en arreglo wprocesos_traslado_pac_cir este vacio hace esto.
					}else{
							//Valida que solo pueda solicitar habitacion para pacientes que no estan hospitalizados.
							if($valueDatos->codigo == 'Cx' ){

								//Si tiene medicamntos pendientes pero no hay ninguna solicitud de cama permite ver los medicamentos pendientes pero no le permite dar de alta definitiva.
								if($datos_med_pac['wnum_art'] > 0){

									echo "<td align=center class='mover_pac' title='Medicamentos pendientes <br>por aplicar o devolver' onclick='mostrar_med_pend(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'>";
									echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'></a>";
									echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
									echo $datos_med_pac['html'];
									echo "</div>";
									echo "</td>";

								}else{

									//Solicitud de cama
									echo "<td align=center class='mover_pac' title='Solicitar Cama' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
								}

							}else{
								echo "<td></td>";
							}
						}

				break;

				case($wurgencias):

						$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;

						if(is_array($wprocesos_traslado_pac_cir)){

							//Verifica si hay pacientes de urgencias que van para cirugia.
							if(array_key_exists($his_ing, $wprocesos_traslado_pac_cir)){

								$codCcoCirugia = consultarCcoCirugia();
								$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $valueDatos->cco, '', '', $codCcoCirugia, ''); //En esta funcion se muestran todos los articulos que tiene saldo
								$turno_cirugia = consultar_turno($valueDatos->historia, $valueDatos->ingreso);


								if($turno_cirugia != ''){
									//Si hay mas de un medicamento pinta en un div oculto la informacion de los articulos para aplicar o descartar.
									if($datos_med_pac['wnum_art'] > 0){

										echo "<td align=center class='mover_pac' title='Medicamentos pendientes <br>por aplicar o devolver' onclick='mostrar_med_pend(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'>";
										echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'></a>";
										echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
										echo $datos_med_pac['html'];
										echo "</div>";
										echo "</td>";
									}else{

										echo "<td align=center style='cursor: pointer' class='mover_pac' title='Entregar a cirugia con turno $turno_cirugia' onclick='EntPacACir(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"off\");'><img src='/matrix/images/medical/root/traslado_a_cx.png'></td>";

									}
									}else{

										echo "<td align=center style='cursor: pointer'><img src='/matrix/images/medical/root/pac_sin_turno_cx.png' id='img_espe_".$valueDatos->historia."-".$valueDatos->ingreso."' class='mover_pac' title='Pendiente de entregar a cirugia <br>sin turno asignado'><br><br><span style='color:#000000;border-radius: 4px;border:1px solid #999999;padding:2px;background-color:#ffffff;font-size:10px;white-space:nowrap;' class='mover_pac' title='Cancelar entrega a cirugia' id='canc_traslado_".$valueDatos->historia."-".$valueDatos->ingreso."' onclick='cancelarEntregaACirugia( \"".$wemp_pmla."\", \"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\")'>Cancelar Entrega</span></td>";
									}
							}else{

								//Verifica si hay pacientes de urgencias que seran trasladados a un piso.
								if(array_key_exists($his_ing, $wprocesos_traslado_pac_urg)){

									$codCcoUrg = consultarCcoUrgencias();
									$hab_destino = $wprocesos_traslado_pac_urg[$his_ing]['habitacionDestino'];
									$id_solicitud = $wprocesos_traslado_pac_urg[$his_ing]['id_solicitud_cama'];

									$datos_med_pac = Detalle_ent_rec('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $wcco, $hab_destino, $id_solicitud, $codCcoUrg, ''); //En esta funcion se muestran todos los articulos que tiene saldo
									$saldo_insumos_auxiliar = traer_insumos_enfermeria($valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo);



									if($hab_destino != ''){

										if($saldo_insumos_auxiliar['saldo_insumos'] > 0){

												echo "<td align=center onclick='mostrar_insumos_pend_hosp(\"insumos_pendientes_$his_ing\");' style='cursor: pointer'><br>";
												echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/saldo_insumos.png'  class='mover_pac' title='Paciente con saldo en insumos' ></a><b><br>Hab. $hab_destino</b>";
												echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='insumos_pendientes_$his_ing' title='Insumos con saldo'>";
												echo $saldo_insumos_auxiliar['html'];
												echo "</div>";
												echo "</td>";

											}else{

												//Si hay mas de un medicamento pinta en un div oculto la informacion de los articulos para aplicar o descartar.
												if($datos_med_pac['wnum_art'] > 0){

													if(trim($hab_destino) != ''){

														$campo_oculto_estilo = "<input type=hidden class='asignar_clase_azul' value='".$valueDatos->historia."-".$valueDatos->ingreso."'>";
														$texto_hab_destino = "<br><b><font size=2pt>Hab. Asignada $hab_destino</font>";
													}

													echo "<td align=center onclick='mostrar_med_pend(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'>$campo_oculto_estilo";
													echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'  class='mover_pac' title='Medicamentos pendientes <br>por aplicar o devolver <br> y con solicitud de cama.'></a> $texto_hab_destino";
													echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
													echo $datos_med_pac['html'];
													echo "</div>";
													echo "</td>";
												}else{

													if(trim($hab_destino) != ''){
														echo "<td align=center style='cursor: pointer' class='mover_pac' title='Entregar a la habitaci&oacuten $hab_destino' onclick='EntregarUrgAPiso(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"".$hab_destino."\", \"$id_solicitud\", \"off\");'><b>Entregar</b><br><img src='/matrix/images/medical/root/pac_con_cama.png'><br><b>Hab. $hab_destino</b><input type=hidden class='asignar_clase_azul' value='".$valueDatos->historia."-".$valueDatos->ingreso."'></td>";
													}else{
														echo "<td align=center style='cursor:pointer;' class='mover_pac' title='Con solicitud de habitación Nro. $id_solicitud <br> sin cama asignada'><img src='/matrix/images/medical/root/pac_con_sol_cama.png' ></td>";
													}
												}
											}
										}else{

											echo "<td align=center style='cursor:pointer;' class='mover_pac' title='Con solicitud de habitación Nro. $id_solicitud <br> sin cama asignada'><img src='/matrix/images/medical/root/pac_con_sol_cama.png' ></td>";
										}

								}else{
									//Solicitud de cama
									//Si la conducta asociada al paciente permite solicitud de cama, mostrara la opcion para realizar la solicitud.
									$wconducta_cama = $pacientes_atendidos_activos[$valueDatos->historia.$valueDatos->ingreso]['cond_sol_cama'];	//Conducta asociada al paciente si puede solicitar cama.

									if($wconducta_cama != 'on'){
										echo "<td></td>";
									}else{
										echo "<td align=center class='mover_pac' title='Solicitar Cama' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
									}
								}
							}
						}else{
								//Solicitud de cama
								echo "<td align=center class='mover_pac' title='Solicitar Cama' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
							}

						$ubi_pac = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso); //Consultar informacion sobre la ubicacion del paciente

						//Traslado ayuda
							if($ubi_pac->servicioTemporal != ''){
								$datos_cco = consultarCentroCosto($conex, $ubi_pac->servicioTemporal, $wbasedato);
								echo "<td align=center class='mover_pac' id='cajon_trasl_ayuda_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Paciente en ".$datos_cco->nombre."'><font size=1><b>".$datos_cco->nombre."</b></font></td>";
							}else{

								//Si el paciente esta pendiente de recibir no permite trasladar a centro de costos de ayuda.
								if($ubi_pac->enProcesoTraslado == 'on'){
									echo "<td></td>";
								}else{
									//Si el paciente tiene alta en proceso no permite el traslado a cco de ayuda.
									if($ubi_pac->altaEnProceso != 'off'){
										echo "<td></td>";
									}else{
										echo "<td align=center class='mover_pac' id='cajon_trasl_ayuda_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Trasladar a otro servicio' onclick='trasladoayuda(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\", \"".$wbasedato."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
									}
								}
							}
				break;

				default:

						$his_ing = $valueDatos->historia."-".$valueDatos->ingreso;
						$ubi_pac = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso); //Consultar informacion sobre la ubicacion del paciente

						//Si no es auxiliar no cargan las opciones de solicitud de cama, entrega y recibo.
						if($waux != 'on'){

						//Control de iconos para la entrega de paciente a otro piso
						if(is_array($wpacientes_por_entrega)){

							//Verifica si hay pacientes de urgencias que van para cirugia.
							if(array_key_exists($his_ing, $wpacientes_por_entrega)){

								$id_solicitud_cama = $wpacientes_por_entrega[$his_ing]['idsolicitud']; //Identificador de la solicitud de cama
								$hab_asignada = $wpacientes_por_entrega[$his_ing]['hab_asignada']; //Habitacion asignada

								$wser_destino = $todoslashab[$hab_asignada]['Habcco']; //Centro de costos de la habitacion asignada

								$whab_actual = $ubi_pac->habitacionActual; //Habitacion actual del paciente.
								$datos_med_pac = Detalle_ent_rec_hospi('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $ccoCodigo, $wccohos, $id_solicitud_cama, $wccoapl, $hab_asignada, $whab_actual, $wser_destino, 'Ent', $valueDatos->servicio_anterior); //En esta funcion se muestran todos los articulos que tiene saldo
								$saldo_insumos_auxiliar = traer_insumos_enfermeria($valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo);

								if($hab_asignada != ''){

										//Si el paciente tiene saldo en insumos (movhos_000227) y el centro de costos origen es diferente al cco destino no permite el traslado y muestra una modal con los insumos que tienen saldo pendiente.
										if($saldo_insumos_auxiliar['saldo_insumos'] > 0 and $ccoCodigo != $wser_destino){

											echo "<td align=center onclick='mostrar_insumos_pend_hosp(\"insumos_pendientes_$his_ing\");' style='cursor: pointer'><br>";
											echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/saldo_insumos.png'  class='mover_pac' title='Paciente con saldo en insumos' ></a><b><br>Hab. $hab_asignada</b>";
											echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='insumos_pendientes_$his_ing' title='Insumos con saldo'>";
											echo $saldo_insumos_auxiliar['html'];
											echo "</div>";
											echo "</td>";

										}else{

											//Si hay mas de un medicamento pinta en un div oculto la informacion de los articulos para aplicar o descartar.
											if($datos_med_pac['wnum_art'] > 0){
												echo "<td align=center onclick='mostrar_med_pend_hosp(\"medicamentos_pendientes_$his_ing\");' style='cursor: pointer'><b>Entregar</b><br>";
												echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png'  class='mover_pac' title='Entregar paciente con medicamentos a la hab. $hab_asignada'></a><b><br>Hab. $hab_asignada</b>";
												echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
												echo $datos_med_pac['html'];
												echo "</div>";
												echo "</td>";

											}else{

												echo "<td align=center id='td_traslado_".$valueDatos->historia."-".$valueDatos->ingreso."' class='mover_pac' title='Entregar a la habitaci&oacuten $hab_asignada' onclick='moverPacHosp( \"".$wemp_pmla."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"".$ccoCodigo."\", \"".$wccohos."\", \"".$id_solicitud_cama."\", \"".$wccoapl."\", \"".$hab_asignada."\", \"".$whab_actual."\", \"".$wser_destino."\", \"Ent\", \"\", \"\")' style='cursor:pointer;'><b>Entregar</b><br><img src='/matrix/images/medical/root/pac_con_cama.png'><br><b>Hab. $hab_asignada</b></td>";
											}

										}

									}else{
										echo "<td align=center class='mover_pac' title='Con solicitud de habitación Nro. $id_solicitud_cama <br>sin cama asignada' style='cursor:pointer;'><img src='/matrix/images/medical/root/pac_con_sol_cama.png'></td>";
									}

								}else{

									if(is_array($wpacientes_por_recibir)){

											//Verifica si hay pacientes de urgencias que van para cirugia.
											if(array_key_exists($his_ing, $wpacientes_por_recibir)){

											$ubicacionPaciente = new ingresoPacientesDTO();
											$ubicacionPaciente = consultarUbicacionPaciente($conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso);

											$id_solicitud_cama = $wpacientes_por_recibir[$his_ing]['idsolicitud']; //Identificador de la solicitud de cama
											$hab_asignada = $wpacientes_por_recibir[$his_ing]['hab_asignada']; //Habitacion asignada

											$wser_destino = $todoslashab[$hab_asignada]['Habcco']; //Centro de costos de la habitacion asignada

											$whab_actual = $ubi_pac->habitacionActual; //Habitacion actual del paciente.
											$wser_anterior = $ubi_pac->servicioAnterior; //Servicio Anterior.
											$datos_cco = consultarCentroCosto($conex, $wser_anterior, $wbasedato);
											$whab_anterior = $ubi_pac->habitacionAnterior; //Habitacion actual del paciente.
											$whab_anterior = ($whab_anterior == '') ? $datos_cco->nombre : $ubi_pac->habitacionAnterior;

											$datos_med_pac = Detalle_ent_rec_hospi('NoApl', $valueDatos->historia, $valueDatos->ingreso, $valueDatos->nombre, $valueDatos->codigo, $ccoCodigo, $wccohos, $id_solicitud_cama, $wccoapl, $hab_asignada, $whab_actual, $wser_destino, 'Rec', $valueDatos->servicio_anterior); //En esta funcion se muestran todos los articulos que tiene saldo

											$no_recibir = "<div style='color:#000000;border-radius: 4px;border:1px solid #999999;padding:2px;background-color:#ffffff;font-size:10px;white-space:nowrap;' class='cancelar_recibo' title='Cancelar recibo' id='canc_traslado_".$valueDatos->historia."-".$valueDatos->ingreso."' onclick='cancelarReciboHospitalizacion( \"".$wemp_pmla."\", \"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$wser_anterior."\")'>Cancelar traslado</div>";

											if($hab_asignada != ''){

													//Si hay mas de un medicamento pinta en un div oculto la informacion de los articulos para aplicar o descartar.
													if($datos_med_pac['wnum_art'] > 0){
														echo "<td align=center id='td_traslado_".$valueDatos->historia."-".$valueDatos->ingreso."'><b>Recibir</b><br>";
														echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/root/pac_con_med.png' style='cursor: pointer' class='mover_pac' title='Recibir paciente con medicamentos a la hab. $hab_asignada <br>proveniente de $whab_anterior' onclick='mostrar_med_pend_hosp(\"medicamentos_pendientes_$his_ing\");'></a><b><br>Hab. $hab_asignada</b><br><hr>";
														echo $no_recibir;
														echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:450px;overflow:auto;' id='medicamentos_pendientes_$his_ing' title='Pendientes por aplicar'>";
														echo $datos_med_pac['html'];
														echo "</div>";
														echo "</td>";
													}else{

														//Si no tiene medicamentos pendientes recibira el paciente directamente.
														echo "<td align=center id='td_traslado_".$valueDatos->historia."-".$valueDatos->ingreso."'><b>Recibir</b><br><img src='/matrix/images/medical/root/pac_con_cama.png' style='cursor:pointer;' class='mover_pac' title='Recibir en la habitaci&oacuten $hab_asignada proveniente de $whab_anterior' onclick='moverPacHosp( \"".$wemp_pmla."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"".$valueDatos->servicio_anterior."\", \"".$wccohos."\", \"".$id_solicitud_cama."\", \"".$wccoapl."\", \"".$hab_asignada."\", \"".$whab_actual."\", \"".$wser_destino."\", \"Rec\", \"\", \"\")'><br><b>Hab. $hab_asignada</b><br><hr>";
														echo $no_recibir;
														echo "</td>";
													}

												}else{
													//Si no tiene habitacion asignada muestra el icono de cama solicitada pero sin habitacion asignada.
													echo "<td align=center class='mover_pac' title='Con solicitud de habitación Nro. $id_solicitud_cama <br>sin cama asignada' style='cursor:pointer;'><img src='/matrix/images/medical/root/pac_con_sol_cama.png'></td>";
												}
										}else{

											//Si el paciente esta en proceso de alta no debe poder solicitar cama.
											if($ubicacionPaciente->altaEnProceso == 'off'){
												//Si no hay pacientes por recibir o entregar muestra el icono de solicitud de cama.
												echo "<td align=center class='mover_pac' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Solicitar Cama' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
											}else{
												echo "<td></td>";
											}
										}

									}else{

										//Si el arreglo wpacientes_por_recibir no es un arreglo imprime el icono de solicitud de cama.
										echo "<td align=center class='mover_pac' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Solicitar Cama' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
									}
								}

							}else{
								//Si el arreglo wpacientes_por_recibir no es un arreglo imprime el icono de solicitud de cama.
								echo "<td align=center class='mover_pac' id='cajon_pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Solicitar Cama' onclick='solicitar_hab(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
							}

							//Traslado ayuda
							if($ubi_pac->servicioTemporal != ''){
								$datos_cco = consultarCentroCosto($conex, $ubi_pac->servicioTemporal, $wbasedato);
								echo "<td align=center class='mover_pac' id='cajon_trasl_ayuda_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Paciente en ".$datos_cco->nombre."'><font size=1><b>".$datos_cco->nombre."</b></font></td>";
							}else{

								//Si el paciente esta pendiente de recibir no permite trasladar a centro de costos de ayuda.
								if($ubi_pac->enProcesoTraslado == 'on'){
									echo "<td></td>";
								}else{
									//Si el paciente tiene alta en proceso no permite el traslado a cco de ayuda.
									if($ubi_pac->altaEnProceso != 'off'){
										echo "<td></td>";
									}else{
										echo "<td align=center class='mover_pac' id='cajon_trasl_ayuda_".$valueDatos->historia."_".$valueDatos->ingreso."' title='Trasladar a otro servicio' onclick='trasladoayuda(\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueDatos->nombre."\", \"$ccoCodigo\", \"".$ubicacionPaciente->habitacionActual."\", \"".$wbasedato."\");' style='cursor:pointer;'><img id='pedir_cama_".$valueDatos->historia."_".$valueDatos->ingreso."'></td>";
									}
								}
							}
						}
				break;
			}



			//Link a HCE
			$path_hce_aux = "";
			$path="../../hce/procesos/HCE_iFrames.php?empresa=".$whce."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato."&whis=".$valueDatos->historia."&wing=".$valueDatos->ingreso."&accion=F&ok=0&wcedula=".$valueDatos->documento."&wtipodoc=".$valueDatos->tid;

			if($wurgencias){

				$ruta_hce_aux = "../../hce/procesos/HCE_iFrames.php?empresa=".$whce."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato."&whis=".$valueDatos->historia."&wing=".$valueDatos->ingreso."&accion=F&ok=0&wcedula=".$valueDatos->documento."&wtipodoc=".$valueDatos->tid;
				$path_hce_aux = "style='cursor: pointer' onclick='ejecutar(".chr(34).$ruta_hce_aux.chr(34).")'";
			}


			if ($waux != 'on') //Si es diferente a Auxiliar de enfermeria
			   echo "<td nowrap='nowrap' align=center style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>";
			  else
                 echo "<td align='center' nowrap='nowrap' $path_hce_aux>";

			echo $valueDatos->historia." - ".$valueDatos->ingreso;
			echo "</td>";

			if (!isset($wsp) or $wsp!="on")
			   {
			   	$varEntidad = "<div class='filaent'><span class='subtituloEntidad'><font size='2'>{$valueDatos->codigoEntidad}->{$valueDatos->nombreEntidad}</font></span><br></div>";
				echo '<td colspan="4" class="mostrarentidad" title="'.$varEntidad.'">';
				echo $valueDatos->nombre;
				echo "</td>";
			   }


			//Procedimientos y examenes pendientes
			if($wir_a_ordenes == 'on'){

				if($paciente_ordenes == 'on'){
				//Busca examenes y procedimientos pendientes
				$examenes_procedim_pend = examenes_procedim_pend($valueDatos->historia, $valueDatos->ingreso);

				//'array_procedimientos'=>$array_ordenes, 'cantidad_pendientes'=
				$texto_estado = "";
				$fondo_pendientes = "";
				$tooltip_pex = "";

				foreach($examenes_procedim_pend['array_log_proc_exa'] as $key_pex => $value_pex){

					if($key_pex == 'N'){
						$texto_estado = "Orden(es) Nueva(s)";
					}elseif($key_pex == 'M'){
						$texto_estado = "Orden(es) Modificada(s)";
					}

					$tooltip_pex .= count($value_pex)." ".$texto_estado."<br>";
					$fondo_pendientes = 'background-color:#3CB648';

				}

				$cantidad_total_pend = ($examenes_procedim_pend['cantidad_pendientes'] > 0) ? "(".$examenes_procedim_pend['cantidad_pendientes'].")" : "";
				if($examenes_procedim_pend['cantidad_pendientes'] > 0){
					echo "<td colspan=2 align=center style='cursor:pointer; $fondo_pendientes' class='tooltip_proc_exa_".$valueDatos->historia."_".$valueDatos->ingreso." ".$examenes_procedim_pend['clase_estado_examenes']." msg' title='".$tooltip_pex."' onclick='fnMostrarProcedimientos(\"$valueDatos->historia\", \"$valueDatos->ingreso\");'>";
				}
				else{
					echo "<td colspan=2 align=center>";
				}

				if( $examenes_procedim_pend['conCitaAsignada']['conCita']  )
				{
					if( $examenes_procedim_pend['conCitaAsignada']['hoy']  )
					{
						echo $relojSvg = '<span title="Cita pendiente para hoy"><svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="28px" height="28px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet">
											<g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="red" stroke="none">
											<path d="M6145 12790 c-44 -4 -150 -13 -235 -20 -2036 -155 -3876 -1277 -4952 -3020 -528 -855 -853 -1856 -928 -2855 -6 -82 -15 -197 -20 -255 -13 -134 -13 -346 0 -480 5 -58 14 -172 20 -255 90 -1196 527 -2361 1252 -3330 1101 -1473 2788 -2403 4618 -2545 80 -6 195 -15 256 -20 141 -13 350 -13 484 0 58 5 173 14 255 20 1830 137 3531 1076 4635 2560 715 961 1151 2127 1240 3315 6 83 15 197 20 255 6 58 10 166 10 240 0 74 -4 182 -10 240 -5 58 -14 173 -20 255 -137 1831 -1076 3531 -2560 4635 -961 715 -2127 1151 -3315 1240 -82 6 -197 15 -255 20 -119 11 -376 11 -495 0z m551 -520 c417 -24 767 -77 1158 -177 798 -202 1575 -589 2221 -1107 668 -535 1196 -1191 1584 -1966 628 -1258 783 -2683 440 -4051 -330 -1319 -1110 -2484 -2204 -3294 -382 -283 -882 -561 -1317 -734 -1165 -462 -2417 -542 -3632 -234 -402 101 -762 236 -1151 428 -954 473 -1728 1158 -2311 2045 -510 777 -823 1653 -929 2600 -119 1067 79 2201 559 3186 464 952 1143 1731 2026 2324 294 198 749 437 1082 569 788 312 1661 457 2474 411z"/>
											<path d="M6160 9268 c0 -1157 -4 -2108 -8 -2115 -4 -6 -33 -23 -65 -37 -196 -86 -364 -267 -431 -462 -35 -103 -49 -203 -45 -321 l4 -105 -1088 -628 c-599 -346 -1091 -631 -1094 -634 -3 -3 48 -98 113 -211 86 -150 123 -205 134 -202 9 2 500 284 1093 626 592 341 1082 621 1089 621 8 0 38 -19 68 -42 30 -22 93 -59 139 -81 385 -181 842 -22 1035 361 63 125 81 207 80 362 -1 123 -3 143 -31 228 -72 215 -229 393 -430 483 -37 17 -71 36 -75 43 -4 6 -8 957 -8 2114 l0 2102 -240 0 -240 0 0 -2102z"/>
											</g>
											</svg></span><br>';;
					}
					else{
						echo $relojSvg = '<span title="Cita pendiente"><svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="28px" height="28px" viewBox="0 0 1280.000000 1280.000000" preserveAspectRatio="xMidYMid meet">
											<g transform="translate(0.000000,1280.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
											<path d="M6145 12790 c-44 -4 -150 -13 -235 -20 -2036 -155 -3876 -1277 -4952 -3020 -528 -855 -853 -1856 -928 -2855 -6 -82 -15 -197 -20 -255 -13 -134 -13 -346 0 -480 5 -58 14 -172 20 -255 90 -1196 527 -2361 1252 -3330 1101 -1473 2788 -2403 4618 -2545 80 -6 195 -15 256 -20 141 -13 350 -13 484 0 58 5 173 14 255 20 1830 137 3531 1076 4635 2560 715 961 1151 2127 1240 3315 6 83 15 197 20 255 6 58 10 166 10 240 0 74 -4 182 -10 240 -5 58 -14 173 -20 255 -137 1831 -1076 3531 -2560 4635 -961 715 -2127 1151 -3315 1240 -82 6 -197 15 -255 20 -119 11 -376 11 -495 0z m551 -520 c417 -24 767 -77 1158 -177 798 -202 1575 -589 2221 -1107 668 -535 1196 -1191 1584 -1966 628 -1258 783 -2683 440 -4051 -330 -1319 -1110 -2484 -2204 -3294 -382 -283 -882 -561 -1317 -734 -1165 -462 -2417 -542 -3632 -234 -402 101 -762 236 -1151 428 -954 473 -1728 1158 -2311 2045 -510 777 -823 1653 -929 2600 -119 1067 79 2201 559 3186 464 952 1143 1731 2026 2324 294 198 749 437 1082 569 788 312 1661 457 2474 411z"/>
											<path d="M6160 9268 c0 -1157 -4 -2108 -8 -2115 -4 -6 -33 -23 -65 -37 -196 -86 -364 -267 -431 -462 -35 -103 -49 -203 -45 -321 l4 -105 -1088 -628 c-599 -346 -1091 -631 -1094 -634 -3 -3 48 -98 113 -211 86 -150 123 -205 134 -202 9 2 500 284 1093 626 592 341 1082 621 1089 621 8 0 38 -19 68 -42 30 -22 93 -59 139 -81 385 -181 842 -22 1035 361 63 125 81 207 80 362 -1 123 -3 143 -31 228 -72 215 -229 393 -430 483 -37 17 -71 36 -75 43 -4 6 -8 957 -8 2114 l0 2102 -240 0 -240 0 0 -2102z"/>
											</g>
											</svg></span><br>';;
					}
				}
				
				echo "<b><span class='blink' onMouseover='mostrarTooltip( this );'><class=tipo3V>".$cantidad_total_pend."</span></b>";
				echo "<div id='div_proc_".$valueDatos->historia."_".$valueDatos->ingreso."' align='center' style='display:none;cursor:default;background:none repeat scroll 0 0; "
						."position:relative;width:100 %;height:600px;overflow:auto;'>
						<center><br>";
				//Aqui se pinta el minireporte para mostrar


				echo "<table width=100%>";

				// echo "<tr class='fondo".$colores[ $valueHoras->color ]."' align='center'>";
				echo "<tr $class3 align='center'>";

				echo "<td style='font-size:14pt'><b>Examenes y procedimientos pendientes</b></td>";

				echo "</tr>";

				echo "<tr class='fila2'>";
				echo "<td>";
				echo "<b>".$valueDatos->historia." - ".$valueDatos->ingreso;
				echo "<br>".$valueDatos->nombre."(".$valueDatos->codigo.")";
				echo "</td>";
				echo "<tr>";

				if( $valueDatos->altaEnProceso && $valueDatos->fechaHoraAltaEnProceso <= $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 ){
					echo "<tr>";
					echo "<td class='fondoAmarillo' align='center'><span class='blink'><b>EN PROCESO DE ALTA</span></b></td>";
					echo "</tr>";
				}
				/************************************************************************************************************************/


				echo "</table>";
				echo "<br><br>";

				// --> 	Obtener el plan actual del paciente
				// 		Jerson trujillo, 2015-04-16.
				$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

				$sqlInfoSegRes = "SELECT Empcod, Empnit, Emptem, Empnom, Tardes, Placod, Plades
									  FROM ".$wbasedatoCliame."_000205 AS A INNER JOIN ".$wbasedatoCliame."_000024 AS B ON(A.Resnit = B.Empcod)
											INNER JOIN ".$wbasedatoCliame."_000025 AS C ON(Emptar = Tarcod) LEFT JOIN ".$wbasedatoCliame."_000153 AS D ON (Respla = Placod)
									 WHERE Reshis = '".$valueDatos->historia."'
									   AND Resing = '".$valueDatos->ingreso."'
									   AND Resnit = '".$valueDatos->codigoEntidad."'";
				$resInfoSegRes = mysql_query($sqlInfoSegRes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoSegRes):</b><br>".mysql_error());
				if($rowInfoSegRes = mysql_fetch_array($resInfoSegRes))
				{
					$infoSegRes['codEntidad'] 	= $rowInfoSegRes['Empcod'];
					$infoSegRes['entidad']		= $rowInfoSegRes['Empnom'];
					$infoSegRes['nitEntidad'] 	= $rowInfoSegRes['Empnit'];
					$infoSegRes['tarifa'] 		= $rowInfoSegRes['Tardes'];
					$infoSegRes['tipoEmp']		= $rowInfoSegRes['Emptem'];
					$infoSegRes['plan']			= $rowInfoSegRes['Placod'];
					$infoSegRes['descripPlan']	= $rowInfoSegRes['Plades'];

					// --> Variables para obtener si un insumo o procedimiento requiere autorizacion
					$codEnt 	= $infoSegRes['codEntidad'];
					$nitEnt 	= $infoSegRes['nitEntidad'];
					$tipEnt 	= $infoSegRes['tipoEmp'];
					$planEmp 	= $infoSegRes['plan'];
				}



				echo "<INPUT TYPE='button' value='Cerrar' class='btnCerrar'  onClick='grabar_leido_ordenes(\"".$wbasedato."\",\"".$whce."\",\"".$wuser."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\");' style='width:100'><br><br>";
				echo "<table><tr class=fila1><td style='background-color:#3CB648'>&nbsp;&nbsp;</td><td><b>Nuevo</b></td><td style='background-color:#CCFFCC'>&nbsp;&nbsp;</td><td><b>Modificado</b></td></tr></table>";
				$q2 = "SELECT Usufve
						 FROM {$whce}_000020
						WHERE Usucod = '".$wuser."'
						  AND Usuest = 'on'";
				$res2 = mysql_query($q2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());
				$row2 = mysql_fetch_array($res2);

				if(date( "Y-m-d" ) > $row2['Usufve']){

					echo "<span class='fondoAmarillo'><b>Su firma electrónica esta vencida, haga click <a href='/matrix/hce/procesos/PassHCE.php?empresa=hce' target='_blank' >aquí</a> para actualizarla, <br>si ya hizo el cambio favor actualizar esta página.</b></span><br><br>";

				}

				echo "<table align='center' width='72%'>";

				echo "<tr align='center' class='encabezadotabla'>";
				//Solo el rol de enfermera jefe puede priorizar.
				if(strpos($rolesJefesEnf, $datos_rol_usuario->codigoRolHCE)){
					echo "<td>Prioritario Autorizar</td>";
				}
				echo "<td>Fecha y hora<br>de Ordenado</td>";
				echo "<td>Fecha<br>a realizar</td>";
				echo "<td nowrap>Tipo de orden</td>";
				echo "<td>Nro Orden</td>";
				echo "<td>Procedimiento</td>";
				echo "<td>Cantidad</td>";
				echo "<td>Justificación</td>";
				echo "<td>Estado</td>";
				echo "<td>Realizar en Servicio?</td>";
				echo "<td>Toma de Muestra</td>";
				echo "<td style='display:none;'>Bitacora de Gestiones</td>";
				echo "</tr>";

				// $estadoPorTipoOrden = consultarAliasPorAplicacion( $conex, $wemp_pmla, "permitirCambiarEstadoInteroperabilidadPorTipoOrden" );
				// $estadoPorTipoOrden = explode( "-", $estadoPorTipoOrden );

				$estadoPorTipoOrden = ccoConInteroperabilidadLaboratorio( $conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso );

				$j = 0;

				foreach( $examenes_procedim_pend['array_procedimientos'] as $keyProcedimientos => $allProcedimientos ){

					foreach($allProcedimientos as $key_p => $valueProcedimientos){
						$opcionesSeleccion = "";
						$class_prioritario = "";
						$prioritario_checked = "";

						$class2 = "class='fila".(($j%2)+1)."'";
						$j++;

						$cantidad = consultarCantidades($valueProcedimientos[ 'Ordhis' ],$valueProcedimientos[ 'Ording' ],$valueProcedimientos[ 'Dettor' ],$valueProcedimientos[ 'Detnro' ],$valueProcedimientos[ 'Detcod' ]);
						
						$wexam = $valueProcedimientos['Dettor'];
						$wfechadataexamen = $valueProcedimientos['Detfec'];
						$wordennro = $valueProcedimientos['Detnro'];
						$wordite = $valueProcedimientos['Detite'];
						$wordite = $valueProcedimientos['Detite'];
						$wordid_detalle = $valueProcedimientos['id_detalle'];
						$wfecha1 = date('Y-m-d');
						$wnombre_examen = traer_nombre_examen($valueProcedimientos['Detcod']);
						$wproc_pendiente = $valueProcedimientos['Detpen'];
						$westado_orden = $valueProcedimientos['Detlog'];

						$wnombre_examen  = ($wnombre_examen != "") ? $wnombre_examen : $valueProcedimientos[ 'Descripcion' ];
						$westado_registro = $valueProcedimientos[ 'Detesi' ];
						$westado_externo = $valueProcedimientos[ 'Deteex' ];
						$codigo_examen = $valueProcedimientos[ 'Codigo_cup' ];
						
						$hora_ordenado = $valueProcedimientos['Hora_data'];
						
						$justificacionInteroperabilidad = $valueProcedimientos['Detjoc'];
						
						$hora_cita_estudio = $valueProcedimientos['Dethci'] != '00:00:00' ? $valueProcedimientos['Dethci']: '' ;
						
						$estadoPorTipoOrden2 = array_merge( $estadoPorTipoOrden, ccoConInteroperabilidadPorEstudio( $conex, $wbasedato, $valueDatos->historia, $valueDatos->ingreso, $wexam, $codigo_examen ) );
						
						
						$wrealizadoEnPiso 		= $valueProcedimientos['Detnof'] == 'on' ? true : false;
						$wrealizarEnServicio 	= $valueProcedimientos['Detrse'] == 'on' ? true : false;
						$wrealizarExterno 		= $valueProcedimientos['Detrex'] == 'on' ? true : false;
						$wrequiereAutorizacion 	= $valueProcedimientos['Detaut'] == 'on' ? true : false;
						
						$wdetalleEstado = detalleEstado( $conex, $wbasedato, $westado_registro );
						
						//Si el esado externo (Estado que viene por interoperabilidad) es diferente a vacio significa que ya se ha enviado los mensajes hl7 correspondientes y por tanto
						//no requiere autorizacion ni preguntar a la enfermera si se realiza en piso o no
						//por que ya se ha enviado el estudio a realizarse
						if( !empty( $westado_externo ) || ( !$wdetalleEstado['esEstadoPendiente'] && !$wdetalleEstado['permiteInteroperabilidad'] ) ){
							$wrealizarEnServicio 	= false;
							$wrealizarExterno 		= false;
							$wrequiereAutorizacion 	= false;
						}
						
						if( empty( $westado_externo ) && $wdetalleEstado['permiteInteroperabilidad'] ){
							$wrequiereAutorizacion 	= false;
						}
						
						$wpreguntarRealizaEnservicio = false;
						if( !$wrequiereAutorizacion && ( $wrealizarEnServicio || $wrealizarExterno ) ){
							$wpreguntarRealizaEnservicio = true;
						}

						// $permiteModEst = in_array( $wexam, $estadoPorTipoOrden ) ? true : false;
						$permiteModEst = in_array( $wexam, $estadoPorTipoOrden2 ) ? false : true;
						// if( !$permiteModEst ){
							// $westado_externo = '';
						// }
						
						
						
						
						
						
						
						
						if( !$permiteModEst ){
							
							$txtComentario  = "";
							$textCita 		= "";
							
							$comentarios = consultarComentarioPorInteroperabilidadInc( $conex, $wbasedato, $wexam, $wordennro, $wordite  );
							
							if( count($comentarios) > 0 ){
					
								$txtComentario .= "<b>Comentarios:</b>";
								
								foreach( $comentarios as $datoComenario ){
									$txtComentario .= "<br><b>".$datoComenario['fecha']." ".$datoComenario['hora']."</b><br>".$datoComenario['comentario'];
								}
							}
							
							if( !empty( $hora_cita_estudio ) && $hora_cita_estudio != '00:00:00' ){
					
								$sala = '';
								
								//Consultando la sala en que se realizará la cita
								//Buscar en la tabla de usuario que cco le pertenece al usuario.
								$sql = "SELECT b.Saldes
											 FROM ".$wbasedato."_000268 a, ".$wbasedato."_000263 b
											WHERE a.mvctor = '".$wexam."'
											  AND a.mvcnro = '".$wordennro."'
											  AND a.mvcite = '".$wordite."'
											  AND b.salcod = a.mvcsal";
								$resSala = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
								if( $rowsSala = mysql_fetch_array( $resSala ) ){
									$sala = $rowsSala['Saldes'];
								}
								
								
								$textCita .= "<tr>
												<td>Fecha</td><td><b>".$wfechadataexamen."</b></td>
											</tr>
											<tr>
												<td>Hora</td><td><b>".$hora_cita_estudio."</b></td>
											</tr>
											<tr>
												<td>Sala</td><td><b>".$sala."</b></td>
											</tr>
										";
							}
							
							if( !empty($txtComentario) ){
								$textCita .= "<tr><td colspan=2>$txtComentario</td></tr>";
							}
							
							if( !empty($textCita) ){
								$txtComentario = "<table>$textCita</table>";
							}
							
							$justificacionInteroperabilidad .= $txtComentario;
						}
						
						
						
						
						
						

						if($wproc_pendiente == 'on'){

							if($westado_orden == 'M'){
								$color_fondo = "CCFFCC";
							}elseif($westado_orden == 'N'){
								$color_fondo = "3CB648";
							}

							$class2 = 'class="fila1 pen_leido_ord_'.$valueDatos->historia.'-'.$valueDatos->ingreso.'" style="background-color:#'.$color_fondo.';"';
						}

						//Si el procedimiento es prioritario se marca naranja.
						if($valueProcedimientos[ 'Detpri' ] == 'on'){
							if($estados_examenes[$westado_registro]->estado_autorizado == 'on'){
								$class_prioritario = "class='articuloNuevoPerfil'";
							}else{
								$class_prioritario = "class='fondoNaranja'";
							}

							$prioritario_checked = "checked";
						}

						echo "<tr data_show_title $class2>";

						$anterior = $valueProcedimientos[ 'Detpri' ];
						//Solo el rol de enfermera jefe puede priorizar.
						if(strpos($rolesJefesEnf, $datos_rol_usuario->codigoRolHCE)){
							echo "<td align=center $class_prioritario id='cajonprioritario_".$wordennro."_".$wordite."'>";
							echo "<input type='checkbox' id='prioritario_".$wordennro."_".$wordite."' $prioritario_checked onClick='marcar_prioritario(\"".$wemp_pmla."\",\"".$wfecha1."\",\"".$wexam."\",\"".$valueProcedimientos[ 'Ording' ]."\",\"".$valueProcedimientos[ 'Ordhis' ]."\", \"$wordennro\", \"$wordite\", this  , \"$wordid_detalle\", \"$whce\",\"$wnombre_examen\",\"$westado_registro\",\"$wbasedato\",\"$wfechadataexamen\",\"$tieneMedicamentos\")'>";
							echo "</td>";
						}
						
						//consultarUsuario es una función de comun.php
						$medicoOrdena = consultarUsuario( $conex, $valueProcedimientos['Detusu'] );
						
						$tooltipOrdenadoPor = "Ordenado por: ".$medicoOrdena->codigo."-".$medicoOrdena->descripcion.".<br>Orden clínica: ".$wexam."-".$wordennro."-".$wordite.( !empty( $valueProcedimientos['Detotr'] ) ? "<br>OT asociada: ".$valueProcedimientos['Detotr'] : '' );
						
						echo "<td style='text-align:center;' title='".$tooltipOrdenadoPor."'>";
						echo $valueProcedimientos[ 'Fecha_data' ];
						echo "<br>".$hora_ordenado;
						echo "</td>";
						
						echo "<td style='text-align:center;'>";
						echo $valueProcedimientos[ 'Detfec' ];
						
						if( !empty($hora_cita_estudio) )
							echo "<br><b>".$hora_cita_estudio."</b>";
						
						echo "</td>";

						echo "<td>";
						echo $valueProcedimientos[ 'Cconom' ];
						echo "</td>";
						
						echo "<td>";
						echo $wordennro;
						echo "</td>";

						//Agregando title con la información del código cups del procedimiento y codigo del lenguaje américas
						$title_descripcion_procedimiento = "CUP: <b>".$codigo_examen."</b>";
						$title_descripcion_procedimiento .= "<br>CODIGO PRPOPIO: <b>".$valueProcedimientos['Detcod']."</b>";
						
						echo "<td title='".$title_descripcion_procedimiento."'>";
						echo $valueProcedimientos[ 'Descripcion' ];

						echo "<br>";
						echo "<br>";

						$medPorProcedimiento = consultarMedicamentosPorProcedimiento($valueProcedimientos[ 'Ordhis' ],$valueProcedimientos[ 'Ording' ],$valueProcedimientos[ 'Dettor' ],$valueProcedimientos[ 'Detnro' ],$valueProcedimientos[ 'Detcod' ]);

						if($wurgencias)
						{
							$oculto = "";
						}
						else
						{
							$oculto = "display:none;";
						}

						// var_dump($medPorProcedimiento);
						if(count($medPorProcedimiento) > 0)
						{
							echo "<table  id='medPorProc_".$valueProcedimientos[ 'Dettor' ]."_".$valueProcedimientos[ 'Detnro' ]."_".$valueProcedimientos['Detite']."' align='center' width='95%' style='".$oculto."font-size:10pt;'>";
								echo "<tr class='encabezadoTabla'>";
									echo "<td rowspan='2'></td>";
									echo "<td rowspan='2' align='center'>Medicamento</td>";
									echo "<td rowspan='2' align='center'>Frecuencia</td>";
									echo "<td rowspan='2' align='center'>Dosis</td>";


									if( date( "Y-m-d", $fechaFinal - $tiempoAMostrar*3600 ) == date( "Y-m-d", time() - 24*3600 ) ){	//Si al restar 12 horas es el dia anterior
										//Hago el rowspan correspondiente para el dia anterior
										$rowspanDiaAnterior = ( 24 - date( "G", $fechaFinal-$tiempoAMostrar*3600 ) )/2;

										echo "<td colspan='".$rowspanDiaAnterior."'  align='center'>".date( "Y-m-d", time()-$tiempoAMostrar*3600 )."</td>";
									}
									else{
										$rowspanDiaAnterior = 0;
									}

									if( $rowspanDiaAnterior*2 != $tiempoAMostrar ){

										if( $rowspanDiaAnterior == 0 ){
											echo "<td  colspan='".( ( $tiempoAMostrar - $rowspanDiaAnterior*2 )/2 )."' align='center'>".date( "Y-m-d", $fechaFinal-1 )."</td>";
										}
										else{
											echo "<td colspan='".( ( $tiempoAMostrar - $rowspanDiaAnterior*2 )/2 )."' align='center'>".date( "Y-m-d", $fechaFinal-1 )."</td>";
										}
									}

									echo "<tr class='encabezadotabla'>";

									//Pinto las horas correspondientes
									for( $h = date( "G", $fechaFinal - $tiempoAMostrar*3600 ); $h < date( "G", $fechaFinal - $tiempoAMostrar*3600 )+$tiempoAMostrar; $h += 2 ){
										if( $h < 24){

											//Si es menor a 24 horas se declara la variable con el dia anterior y que el reporte trabaje con ese dato.
											$wfecha_ayer1 = time()-(1*24*60*60);
											$wfecha_ayer = date('Y-m-d', $wfecha_ayer1);

											echo "<td align='center' style='width:50;border:10;' >".gmdate( "G", $h*3600 )."</td>";

										}
										else
										{
											//Si es menor a 24 horas se declara la variable con el dia actual y que el reporte trabaje con ese dato.
											$wfecha_hoy = date("Y-m-d");

											echo "<td align='center' style='width:50;border:10;'>".gmdate( "G", $h*3600 )."</td>";
										}
									}
									echo "</tr>";
								echo "</tr>";

							for($t=0;$t < count($medPorProcedimiento); $t++)
							{
								$idTooltip = $valueProcedimientos[ 'Dettor' ]."_".$valueProcedimientos[ 'Detnro' ]."_".$valueProcedimientos[ 'Detcod' ]."_".$medPorProcedimiento[$t]['Relmed']."_".$medPorProcedimiento[$t]['Relido'];

								//Tooltip
								if($medPorProcedimiento[$t]['fecUltimaAplic']!="" && $medPorProcedimiento[$t]['ronUltimaAplic']!="")
								{
									$ronMe =explode("-",$medPorProcedimiento[$t]['ronUltimaAplic']);
									$InfoApl = " - <div id=\"dvMedAgrupado_".$idTooltip."\" style=\"font-family:verdana;font-size:10pt\">";
									$InfoApl .= "<b>Última aplicación:</b> " . $medPorProcedimiento[$t]['fecUltimaAplic']." ".trim($ronMe[0])." ".trim($ronMe[1]);
									$InfoApl .= "</div>";
								}
								else
								{
									$InfoApl = " - <div id=\"dvMedAgrupado_".$idTooltip."\" style=\"font-family:verdana;font-size:10pt\">";
									$InfoApl .= "<b>".$medPorProcedimiento[$t]['mensMed']."</b> ";
									$InfoApl .= "</div>";
								}

								echo "<tr style='background-color:9DC5FC'>";
									echo "<td id=\"tdAccMedPro_".$idTooltip."\" align='center'><span>".$medPorProcedimiento[$t]['mensMed']."</span><input type='hidden' id='accMedPro_".$idTooltip."' name='accMedPro_".$idTooltip."' value='".$medPorProcedimiento[$t]['mensMed']."' accAnterior='".$medPorProcedimiento[$t]['mensMed']."' cantAplicMed='".$medPorProcedimiento[$t]['Aplicacion']."'></td>";
									echo "<td  id=\"MedAgrupado_".$idTooltip."\" title='".$InfoApl."'>".$medPorProcedimiento[$t]['Artcom']."</td>";
									echo "<td align='center'>".$medPorProcedimiento[$t]['Percan']." ".$medPorProcedimiento[$t]['Peruni']."</td>";
									echo "<td align='center'>".$medPorProcedimiento[$t]['Kadcfr']." ".$medPorProcedimiento[$t]['Kadufr']."</td>";

									for( $r = date( "G", $fechaFinal - $tiempoAMostrar*3600 ); $r < date( "G", $fechaFinal - $tiempoAMostrar*3600 )+$tiempoAMostrar; $r += 2 )
									{
										if( $r < 24){

											//Si es menor a 24 horas se declara la variable con el dia anterior y que el reporte trabaje con ese dato.
											$fecha_aplica = time()-(1*24*60*60);
											$fechaConsultaAplicacion = date('Y-m-d', $fecha_aplica);
										}
										else
										{
											//Si es menor a 24 horas se declara la variable con el dia actual y que el reporte trabaje con ese dato.
											$fechaConsultaAplicacion = date("Y-m-d");
										}

										$rondaActual = gmdate( "H", $r*3600 ).":00 - ".gmdate( "A", $r*3600 );

										$fechaHoraRondas = $fechaConsultaAplicacion." ".gmdate( "H", $r*3600 ).":00:00";;

										$medChecked = "";
										$medDisabled= "disabled='disabled'";
										$onclickReadOnly = "";
										$classMedAplicar = "";
										$classMedChecked = "medAplicado=''";

										foreach($valueDatos->horas as $keyRondas => $valueRondas)
										{
											foreach( $valueRondas->medicamentos as $keyMedicamentos => $valueMedicamentos )
											{
												if(gmdate( "G", $r*3600 ) == $keyRondas && $medPorProcedimiento[$t]['Relmed']."-".$medPorProcedimiento[$t]['Relido']==$keyMedicamentos)
												{
													// var_dump($valueMedicamentos);
													if($medPorProcedimiento[$t]['casoAplicMed'] == 1) //Sin dispensar
													{
														$medChecked = "";
														$classMedAplicar = "class='fondoamarillo'";

													}
													else if($medPorProcedimiento[$t]['casoAplicMed'] == 2) //Aplicado
													{
														$classMedAplicar = "class='fondoamarillo'";

														//--------------
														$wuser_datos = explode("-",$_SESSION['user']);

														$onclickReadOnly = "onClick='aplicarMedPorProced(\"".$wemp_pmla."\",\"".$valueProcedimientos[ 'Ordhis' ]."\",\"".$valueProcedimientos[ 'Ording' ]."\",\"".$rondaActual."\",\"".$valueMedicamentos['codigo']."\",\"".$valueMedicamentos['nombre']."\",\"".$valueMedicamentos['cantidadFraccion']."\",\"".$valueDatos->cco."\",\"".$fechaConsultaAplicacion."\",\"".$valueMedicamentos['noEnviar']."\",\"".$valueMedicamentos['unidad']."\",\"".$valueMedicamentos['cantidadAAplicar']."\",\"".$valueMedicamentos['idOriginal']."\",\"".$valueMedicamentos['codVia']."\",\"".$wuser_datos[1]."\",\"".gmdate( "G", $r*3600 )."\",\"".$valueMedicamentos['esStock']."\",\"".gmdate( "H", $r*3600 )."\",\"".$valueProcedimientos['id_detalle']."\",\"".$medPorProcedimiento[$t]['Aplicacion']."\");'";
														//--------------

														if($valueMedicamentos['cantidadAplicada']!=null)
														{
															$medChecked = "checked='checked'";
															$classMedChecked = "medAplicado='on'";
														}


													}
													else if($medPorProcedimiento[$t]['casoAplicMed'] == 3) //Sin aplicar
													{
														//Consultar aplicaciones y deshabilitado

														$medDisabled= "";
														$classMedAplicar = "class='fondoamarillo'";

														$wuser_datos = explode("-",$_SESSION['user']);

														$onclickReadOnly = "onClick='aplicarMedPorProced(\"".$wemp_pmla."\",\"".$valueProcedimientos[ 'Ordhis' ]."\",\"".$valueProcedimientos[ 'Ording' ]."\",\"".$rondaActual."\",\"".$valueMedicamentos['codigo']."\",\"".$valueMedicamentos['nombre']."\",\"".$valueMedicamentos['cantidadFraccion']."\",\"".$valueDatos->cco."\",\"".$fechaConsultaAplicacion."\",\"".$valueMedicamentos['noEnviar']."\",\"".$valueMedicamentos['unidad']."\",\"".$valueMedicamentos['cantidadAAplicar']."\",\"".$valueMedicamentos['idOriginal']."\",\"".$valueMedicamentos['codVia']."\",\"".$wuser_datos[1]."\",\"".gmdate( "G", $r*3600 )."\",\"".$valueMedicamentos['esStock']."\",\"".gmdate( "H", $r*3600 )."\",\"".$valueProcedimientos['id_detalle']."\",\"".$medPorProcedimiento[$t]['Aplicacion']."\");'";


														if($valueMedicamentos['cantidadAplicada']!=null)
														{
															$medChecked = "checked='checked'";
															$classMedChecked = "medAplicado='on'";
														}
													}

													break 2;
												}


											}
										}

										// echo "<td ".$classMedAplicar."><input type='checkbox' id='checkMed_".$medPorProcedimiento[$t]['Relmed']."_".$medPorProcedimiento[$t]['Relido']."_".gmdate( "G", $r*3600 )."' name='checkMed_".$medPorProcedimiento[$t]['Relmed']."_".$medPorProcedimiento[$t]['Relido']."_".gmdate( "G", $r*3600 )."' ".$medChecked."  ".$classMedChecked."  ".$classSinAplicar." ".$medDisabled." ".$onclickReadOnly."></td>";
										echo "<td ".$classMedAplicar." align='center'><input type='checkbox' id='checkMed_".$medPorProcedimiento[$t]['Relmed']."_".$medPorProcedimiento[$t]['Relido']."_".gmdate( "G", $r*3600 )."' name='checkMed_".$medPorProcedimiento[$t]['Relmed']."_".$medPorProcedimiento[$t]['Relido']."_".gmdate( "G", $r*3600 )."' ".$medChecked."  ".$classMedChecked." ".$medDisabled." ".$onclickReadOnly."></td>";
									}

								$cadenaMed .= $idTooltip."|";

								echo "</tr>";
							}

							echo "</table>";
						}


						echo "</td>";

						echo "<td align='center'>";
						echo $cantidad;
						echo "</td>";

						// echo "<td>";
						// echo $valueProcedimientos[ 'Detjus' ];
						// echo "</td>";

						echo "<td>";
						echo "<textarea cols='10' rows='1' readOnly='readOnly'>".$valueProcedimientos[ 'Detjus' ]."</textarea>";
						echo "</td>";

						$id_select = $valueProcedimientos[ 'Ordhis' ]."-".$valueProcedimientos[ 'Ording' ];

						echo "<td style='text-align:center;'>";

						$tieneMedicamentos = "off";
						if(count($medPorProcedimiento) > 0)
						{
							$tieneMedicamentos = "on";
						}

						if(count($medPorProcedimiento) > 0)
						{
							$tieneUnMedAplicado = false;

							if(count($arrayEstadoAplMed) == 0)

							foreach($medPorProcedimiento as $keyMed => $valueMed)
							{
								if($valueMed['casoAplicMed'] == 2)
								{
									$tieneUnMedAplicado = true;
									break;
								}
							}
						}

						$indicePestana = "4";
						$accionesPestana = consultarAccionesPestana($indicePestana);
						$estados_por_rol_enf = estados_por_rol_enf();
						$anterior = "";
						$noOfertado = "";
						$descripcionPantallaEStadoExterno = "";
						foreach ($colEstadosExamenRol as $estadoExamen){

							$disabled = "";
							//Si el estado del arreglo es igual al estado inactivo de las enfermeras, se inhabilita en el seleccionador.
							if($estados_por_rol_enf[$estadoExamen->codigo] == ''){
								$disabled = "disabled";
							}

							if($estadoExamen->est_cancelada == "on" && $tieneUnMedAplicado)
							{
								$disabled = "disabled";
							}


							// procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifProc, $procedimiento)
							$pideAutorizacion 	= procedimientoSeDebeAutorizar($codEnt, $nitEnt, $tipEnt, $planEmp, $clasifiPro, $valueProcedimientos['Detcod']);

							if(!$pideAutorizacion){

								$q_est = "SELECT Eexcod
											FROM ".$wbasedato."_000045
										   WHERE Eexord = 'on'
											 AND Eexest = 'on'
											 AND Eexepe = 'on'";
								$res_est = mysql_query($q_est, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q_est . " - " . mysql_error());
								$row_est = mysql_fetch_array($res_est);
								$cod_est_pend = $row_est['Eexcod'];

								if($valueProcedimientos[ 'Detesi' ] == $cod_est_pend and $estadoExamen->estado_realizado == "on"){

									$disabled = "disabled";

								}

								if($valueProcedimientos[ 'Detesi' ] == $cod_est_pend and $estadoExamen->resultado_pendiente == "on"){

									$disabled = "disabled";

								}
							}

							$hora_actual = time();
							$datos_hora_examen = explode(",",$estadoExamen->horario);

							$manana = explode("-",$datos_hora_examen[0]);
							$hora_dia_inicial = $manana[0].":00:00";
							$hora_dia_final = $manana[1].":00:00";
							$hora_dia_inicial_unix = strtotime($hora_dia_inicial);
							$hora_dia_final_unix = strtotime($hora_dia_final);

							$tarde = explode("-",$datos_hora_examen[1]);
							$hora_tarde_inicial = $tarde[0].":00:00";
							$hora_tarde_final = $tarde[1].":00:00";
							$hora_tarde_inicial_unix = strtotime($hora_tarde_inicial);
							$hora_tarde_final_unix = strtotime($hora_tarde_final);

							//echo "hora_actual:".$hora_actual."- Hora_dia_inicial:".$hora_dia_inicial_unix."- hora_dia_final_unix:".$hora_dia_final_unix."- hora_tarde_inicial_unix:".$hora_tarde_inicial_unix."- hora_tarde_final_unix:".$hora_tarde_final_unix."<br>";

							//Si el paciente tiene saldo en insumos y esta dentro de la hora para el parametro HorarioAlertaSaldoInsumos mostrara el cajon de fondo rojo y con parpadeo.
							if(($hora_actual > $hora_dia_inicial_unix and $hora_actual < $hora_dia_final_unix) or ($hora_actual > $hora_tarde_inicial_unix and $hora_actual < $hora_tarde_final_unix) ){

									if($estadoExamen->realizado_nocturno == "on"){
											$disabled = "disabled";
									}
							}

							/****************************************************************************************************
							 * Si no permite cancelar examen, se impide modificar el estado cancelar
							 ****************************************************************************************************/
							//Busco si el examen permite modificar estado
							//Solo ocurre si el tipo de la orden es ofertado por Laboratorio
							$permiteModificarEstado = true;

							if( !$permiteModEst  )
							{
								$sql = "SELECT Valtoc, Valcoc, Valeoc
										  FROM ".$wbasedato."_000267 a
										 WHERE Valtor = '".$wexam."'
										   AND Valest = 'on'
										";

								$resPME = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
								$numPME = mysql_num_rows( $resPME );
								
								if( $numPME > 0 ){
									
									if( $rowsToOfertado = mysql_fetch_array( $resPME ) )
									{
										$tablaOfertas 	= $rowsToOfertado['Valtoc'];
										$campoOferta	= $rowsToOfertado['Valcoc'];
										$campoEstado	= $rowsToOfertado['Valeoc'];
										
										$sql = "SELECT *
												  FROM ".$tablaOfertas."
												 WHERE ".$campoOferta." = '".$codigo_examen."'
												   AND ".$campoEstado." = 'on'";
										
										$resConOferta = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
										$numConOferta = mysql_num_rows($resConOferta);
										
										if( $numConOferta > 0 ){
											$permiteModificarEstado = false;
											// $detalle->descripcionEstadoExterno = "No ofertado";
										}
										else{
											$noOfertado = "<br><b>No ofertado</b>";
										}
									}
								}
							}
							
							// if( empty( $westado_externo ) ){
							if( strlen( $westado_externo ) == 0 ){
								$permiteModificarEstado = true;
							}
							
							$permiteCancelar = false;
							//Si no permite modificar la orden, se busca si se puede cancelar la orden
							if(!$permiteModificarEstado){

								//Busco si se permite cancelar el examen
								$sql = "SELECT Estpme, Estdpa
										  FROM ".$wbasedato."_000257 a
										 WHERE Esthl7 = '".$westado_externo."'
										   AND Estest = 'on'
										";

								$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

								$permiteCancelar = true;
								if( $row = mysql_fetch_array($res) ){
									$permiteCancelar = strtolower( $row['Estpme'] ) == 'on';
									$descripcionPantallaEStadoExterno = "<br><b>".$row['Estdpa']."</b>";
								}
							}

							//Si no se permite modificar estado, se verifica si puede cancelar la orden
							if( !$permiteModificarEstado ){

								if( $estadoExamen->est_cancelada ){

									if( !$permiteCancelar ){
										$disabled = "disabled";
									}
								}
								else{
									$disabled = "disabled";
								}
							}
							/***************************************************************************************************/


							if($estadoExamen->codigo!=$valueProcedimientos[ 'Detesi' ]){
								$opcionesSeleccion .= "<option value='$estadoExamen->codigo' accMed='$estadoExamen->accion_med_proc_agrup' estCancelado='$estadoExamen->est_cancelada' estRealizado='$estadoExamen->est_realizado' $disabled>$estadoExamen->descripcion</option>";
							}
							else{
								$opcionesSeleccion .= "<option value='$estadoExamen->codigo' accMed='$estadoExamen->accion_med_proc_agrup' estCancelado='$estadoExamen->est_cancelada' estRealizado='$estadoExamen->est_realizado' selected $disabled>$estadoExamen->descripcion</option>";
								$anterior = $estadoExamen->codigo;

							}
						}

						$wexam = $valueProcedimientos['Dettor'];
						$wfechadataexamen = $valueProcedimientos['Detfec'];
						$wordennro = $valueProcedimientos['Detnro'];
						$wordite = $valueProcedimientos['Detite'];
						$wordite = $valueProcedimientos['Detite'];
						$wordid_detalle = $valueProcedimientos['id_detalle'];
						$wfecha1 = date('Y-m-d');
						$wnombre_examen = traer_nombre_examen($valueProcedimientos['Detcod']);

						$wnombre_examen  = ($wnombre_examen != "") ? $wnombre_examen : $valueProcedimientos[ 'Descripcion' ];
						$westado_registro = $valueProcedimientos[ 'Detesi' ];


						$medAsociado=consultarTablaMedAsociado($wbasedato,$valueProcedimientos[ 'Ordhis' ],$valueProcedimientos[ 'Ording' ],$wexam,$wordennro,$wordite);


						//Campo select que se crea de igual forma que en las ordenes, esto para controlar los permisos desde hce_000029 por rol.
						crearCampo("6","westadoexamen_$wordid_detalle",@$accionesPestana[$indicePestana.".9"],array("class"=>"campo2 select_ordenes","medAsociado"=>$medAsociado,"onChange"=>"cambiar_estado_examen('$wemp_pmla','$wfecha1','$wexam','".$valueProcedimientos[ 'Ording' ]."','".$valueProcedimientos[ 'Ordhis' ]."', '$wordennro', '$wordite',this  , '$wordid_detalle', '$whce','$wnombre_examen','$westado_registro','$wbasedato','$wfechadataexamen','$anterior','$tieneMedicamentos');"),"$opcionesSeleccion");
						echo $noOfertado;
						echo $descripcionPantallaEStadoExterno;
						
						if( !empty($justificacionInteroperabilidad) ){
							
							$estadoPorInteroperabilidadEsCancelado = estadoPorInteroperabilidadEsCancelado( $conex, $wbasedato, $westado_externo );
							
							$icon_jus 	= '../../images/medical/root/info.png';
							$jus_add	= '';
							
							if( $estadoPorInteroperabilidadEsCancelado ){
								$icon_jus 	= '../../images/medical/sgc/Mensaje_alerta.png';
								$jus_add 	= "Cancelado en ".$valueProcedimientos[ 'Cconom' ]."<br>";
							}
							
							echo "<span style='display:block;font-weight:bold;' class='msg_tooltip'><img src='".$icon_jus."' width='20px' title='".$jus_add.$justificacionInteroperabilidad."'></span>";
							
						}
						echo "</td>";
						
						
						/*******************************************************************************************************************
						 * Realizar en servicio
						 *******************************************************************************************************************/
						echo "<td style='text-align:center;'>";
						
						$puedeMarcarRealizarServicio = false;
						//Si tiene toma de muestra
						if( isset($accionesPestana[$indicePestana.".17"]) )
						{	
							//Se verifica que tenga el permismo
							if( $accionesPestana[$indicePestana.".17"]->leer ){
								$puedeMarcarRealizarServicio = true;
							}
						}

						$preuntaPorRealizarEnServicio = false;
						if( $puedeMarcarRealizarServicio )
						{
							if( $wpreguntarRealizaEnservicio && !$wrealizadoEnPiso )
							{	
								$preuntaPorRealizarEnServicio = true;
								echo "<input type='checkbox' value='' onclick='realizarEnServicio( this, ".( $wrealizarEnServicio ? 'true' : 'false' ).",".( $wrealizarExterno ? 'true' : 'false' ).",\"".$wexam."\",\"".$wordennro."\",\"".$wordite."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\",\"".$valueProcedimientos[ 'Descripcion' ]."\" )'>";
							}
						}

						echo "</td>";
						 /*******************************************************************************************************************
						 * Toma de muestra
						 *******************************************************************************************************************/
						echo "<td style='text-align:center;'>";
						
						
						$tomaMuestraPorOrden = tipoDeOrdenConTomaMuestra( $conex, $whce, $wexam );
						
						if( $tomaMuestraPorOrden ){
							
							$usuario_toma_muestra 	= consultarUsuarioTomaMuestra( $conex, $whce, $wexam, $wordennro, $wordite );
							$realizaUnidad 			= estudioRealizaUnidad( $conex, $wbasedato, $wexam, $codigo_examen );
							
							if( empty($usuario_toma_muestra['usuario']) )
							{
								if( !$permiteModEst ){
									
									if( time() > strtotime( $valueProcedimientos[ 'Detfec' ]." 00:00:00" ) ){
										
										if( empty($noOfertado) ){
											$estadoPermiteTomaMuestra = estadoPermiteTomaMuestra( $conex, $wbasedato, $westado_externo );
										}
										else{
											$estadoPermiteTomaMuestra = true;
										}
										
										if( $estadoPermiteTomaMuestra ){
											
											$disabled_toma_muestra = "disabled";
											
											//Si tiene toma de muestra
											if( isset($accionesPestana[$indicePestana.".16"]) ){
												
												//Se verifica que tenga el permismo
												if( $accionesPestana[$indicePestana.".16"]->leer ){
													$disabled_toma_muestra = "";
												}
											}

											// echo "<input type='checkbox' $disabled_toma_muestra onclick=\"tomarMuestra( this, '".$whce."', '".$wbasedato."', '".$valueDatos->historia."', '".$valueDatos->ingreso."', '".$wexam."','".$wordennro."','".$wordite."','".$usuario."', '".( $realizaUnidad && $ccoRealizaEstudios ? '1' : '0' )."' , '".$valueProcedimientos[ 'Descripcion' ]."' );\" />";
											echo "<input type='checkbox' $disabled_toma_muestra data-tor='".$wexam."' data-nor='".$wordennro."' data-item='".$wordite."' data-realizaestudio='".( $realizaUnidad && $ccoRealizaEstudios ? '1' : '0' )."' data-estudio='".$valueProcedimientos[ 'Descripcion' ]."' onclick=\"tomarMuestra( this, '".$valueDatos->historia."', '".$valueDatos->ingreso."', '".$wexam."','".$wordennro."','".$wordite."','".$usuario."', '".( $realizaUnidad && $ccoRealizaEstudios ? '1' : '0' )."' , '".$valueProcedimientos[ 'Descripcion' ]."' );\" />";
										}
									}
									else{
										
										$datetime1 			= new DateTime( date("Y-m-d 00:00:00") );
										$datetime2 			= new DateTime( $valueProcedimientos[ 'Detfec' ] );
										$dias_toma_muestra 	= $datetime1->diff($datetime2);
										
										if( $dias_toma_muestra->d == 1 ){
											echo "<b>En ".$dias_toma_muestra->d." d&iacute;a</b>";
										}
										else{
											echo "<b>En ".$dias_toma_muestra->d." d&iacute;as</b>";
										}
									}
								}
							}
							else{
								
								$informacionTomaMuestra = $usuario_toma_muestra['usuario']."<br>".$usuario_toma_muestra['fecha']." ".$usuario_toma_muestra['hora'];
								
								if( $realizaUnidad && $ccoRealizaEstudios ){
									// echo $examen->usuarioTomaMuestra;
									echo "<span onclick=\"imprimirSticker( '".$valueProcedimientos[ 'Descripcion' ]."','".$wexam."','".$wordennro."','".$wordite."', '".$valueDatos->historia."', '".$valueDatos->ingreso."' );\">";
									echo '<img src="/matrix/images/medical/movhos/checkmrk.ico" width="25px" style="cursor:pointer;" title="<span style=font-size:10pt;>'.$informacionTomaMuestra.'</span>">';
									echo '<img src="../../images/medical/hce/icono_imprimir.png" width="25px" style="cursor:pointer;">';
									echo '</span>';
								}
								else{
									echo '<img src="/matrix/images/medical/movhos/checkmrk.ico" width="25px" style="cursor:pointer;" title="<span style=font-size:10pt;>'.$informacionTomaMuestra.'</span>">';
								}
							}
						}
						
						echo "</td>";
						/*******************************************************************************************************************/

						echo "</tr>";
					}
				}

				echo "</table>";

				echo "<br><INPUT TYPE='button' value='Cerrar' class='btnCerrar' onClick='grabar_leido_ordenes(\"".$wbasedato."\",\"".$whce."\",\"".$wuser."\",\"".$valueDatos->historia."\",\"".$valueDatos->ingreso."\");' style='width:100'>";
				echo "<br><INPUT TYPE='button' value='Grabar' class='btnEnvioTomaMuestras btnEnvioTomaMuestras_aceptar' onClick='envioTomaMuestras( div_proc_".$valueDatos->historia."_".$valueDatos->ingreso.",\"".$whce."\",\"".$wbasedato."\", ".$valueDatos->historia.", ".$valueDatos->ingreso." )' style='width:300;display:none;'>";
				echo "<INPUT TYPE='button' value='Cancelar' class='btnEnvioTomaMuestras btnEnvioTomaMuestras_cancelar' onClick='cancelarMuestras( div_proc_".$valueDatos->historia."_".$valueDatos->ingreso." )' style='width:120;display:none;'>";


				echo "</center>
					  </div>";
				//fin de pintar minireporte de examenes y procedimientos

				echo "</td>";

				}else{

					echo "<td colspan=2></td>";
				}

			}

			//Datos interconsultas
			if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){
				$datos_interconsultas = interconsultas($valueDatos->historia, $valueDatos->ingreso);
				@$datos_interconsultas = implode("\n",$datos_interconsultas);
				echo "<td align=center nowrap>".$datos_interconsultas."</td>";
			}

			$wfecha_actual = date("Y-m-d");
			$wmed = "";
			$wmed = traer_medico_tte($valueDatos->historia, $valueDatos->ingreso, $wfecha_actual, $cuantos, $wsp, $mostrar);

			if ($wmed == "Sin Médico")
				{         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
					$dia = time() - (1 * 24 * 60 * 60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
					$wayer = date('Y-m-d', $dia); //Formatea dia

					$wmed = traer_medico_tte($valueDatos->historia,$valueDatos->ingreso, $wayer, $cuantos, $wsp, $mostrar);
				}

			if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){

				echo "<td colspan=2 nowrap>".$wmed."</td>";

			}else{
				if($cuantos > 1){
					 echo "<td colspan=2 align=center><a class='msg' title='".$wmed."'><p style='cursor:pointer;'><b>Ver</b></p></a></td>";
				}else{
					echo "<td colspan=2>".$wmed."</td>";
				}
			}

			//imprimo lo que hay por horas

			for( $k = date( "G", $fechaFinal-$tiempoAMostrar*3600 ), $l = 0; $k < date( "G", $fechaFinal-$tiempoAMostrar*3600 )+$tiempoAMostrar; $k += 2, $l += 2 ){
			// foreach( $valueDatos->horas as $keyHoras => $valueHoras ){

				//Si el articulo no tiene kardex para el día actual y no tiene articulos a mostrar, se muestra toda la fila en colspan con la imagen correspondiente a Kardex Sin Generar
				if( date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) == date( "Y-m-d" ) && $valueDatos->tieneKardex[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] != 'on' && empty( $valueDatos->totalArticulos[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] ) ){

					echo "<td $class colspan='".( $tiempoAMostrar-$l/2 )."' align='center'>";
					echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/movhos/NOTE16.ico'></a>";
					echo "</td>";

					$k = $k + ( $tiempoAMostrar - $l );
					$l = $tiempoAMostrar*2;
				}
				elseif( ( @$valueDatos->totalArticulos[ 'total' ] == 0 || @$valueDatos->totalArticulos[ 'total' ] == @$valueDatos->articulosNecesidadSinRonda['total'] ) ){
					echo "<td $class colspan='".( $tiempoAMostrar-$l/2 )."' align='center'>";
					echo "<a style='font-size:8pt;'>SIN MEDICACI&Oacute;N</a>";
					echo "</td>";

					$k = $k + ( $tiempoAMostrar - $l );
					$l = $tiempoAMostrar*2;
				}
				else{
					$keyHoras = gmdate( "G",$k*3600 )*1;
					$valueHoras = $valueDatos->horas[ $keyHoras ];

					@$valueHoras->estado();

					if( !$valueHoras->tieneArticulosSinConfirmar ){
						if( $colores[ $valueHoras->color ] != "NA" ){
							$classCelda = "class='fondo".$colores[ $valueHoras->color ]."'";
						}
						else{
							if( $k >= 24 ){
								// $classCelda = $class3;
								$classCelda = $class;
							}
							else{
								$classCelda = $class;
							}
						}
					}
					else{
						$classCelda = "class='fondoAlertaConfirmar'";
					}

					//Validar si el centro de costos es de urgencias
					$esUrgencias = esUrgencias($conex, $ccoCodigo);

					//Se declara la variable pide_clave para saber si el centro de costos debe pedir clave para ingresar al listado de articulos.
					$pide_clave = "fnMostrarArticulos( \"".$keyHoras."\", \"".$valueDatos->historia."\", \"".$valueDatos->ingreso."\" )";

					//Si el cco es de urgencias la funcion de mostrar solicitara la clave.
					if($esUrgencias){

						$pide_clave = "fnMostrar( \"".$keyHoras."\", \"".$valueDatos->historia."\", \"".$valueDatos->ingreso."\" );";

					}

					echo "<td $classCelda align='center' onClick='".$pide_clave."' ronda='$keyHoras'>";

					echo $valueHoras->descripcion;

					if( !empty($valueHoras->descripcion) ){
						if( date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) == date( "Y-m-d" ) ){
							if( $valueDatos->tieneKardex[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] != 'on' ){
								echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/movhos/NOTE16.ico'></a>";
							}
							if( $valueDatos->kardexConfirmado[ date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 ) ] != 'on' ){
								echo "<a style='font-size:6pt;'><img src='/matrix/images/medical/movhos/Key04.ico'></a>";	//Sin confirmar
							}
						}
					}

					if( count($valueHoras->medicamentos) > 0 && !empty($valueHoras->color) ){

						//Aqui se pinta el minireporte para mostrar
						echo "<div style='display:none;width:100%' title='Informaci&oacute;n para las $keyHoras:00:00' id='lista_articulos_".$keyHoras."_".$valueDatos->historia."_".$valueDatos->ingreso."'>";

						echo "<table width=100%>";

						// echo "<tr class='fondo".$colores[ $valueHoras->color ]."' align='center'>";
						echo "<tr $class3 align='center'>";

						echo "<td style='font-size:14pt'><b>Informaci&oacute;n para las ".date( "H:00:00 \d\\e\l Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."</b></td>";

						echo "</tr>";

						echo "<tr class='fila2'>";
						echo "<td>";
						echo "<b>".$valueDatos->historia." - ".$valueDatos->ingreso;
						echo "<br>".$valueDatos->nombre."(".$valueDatos->codigo.")";
						echo "</td>";
						echo "<tr>";

						/************************************************************************************************************************
						 * Julio 24 de 2012
						 ************************************************************************************************************************/
						if( $valueDatos->altaEnProceso && $valueDatos->fechaHoraAltaEnProceso <= $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 ){
							echo "<tr>";
							echo "<td class='fondoAmarillo' align='center'><span class='blink'><b>EN PROCESO DE ALTA</span></b></td>";
							echo "</tr>";
						}
						/************************************************************************************************************************/


						echo "</table>";
						echo "<br><br>";

						$classNombre = "";
						$mostrarAplicaciones = $valueHoras->puedeAplicar;
						if( !empty($ccoayuda) && $mostrarAplicaciones )
						{
							echo "<table style='margin:5px;'>";
							echo "<tr>";
							echo "<td class=fila1 style='font-weight:bold;text-align:center;padding:5px;'>Ordenado<br>en Piso</td>";
							echo "<td class=fila2 style='font-weight:bold;text-align:center;padding:5px;'>Ordenado<br>en Piso</td>";
							echo "<td class=fondoamarillo style='font-weight:bold;text-align:center;padding:5px;'>Ordenado en<br>ayuda dx/Cx</td>";
							echo "</tr>";
							echo "</table>";
						}




						echo "<table align='center'>";

						echo "<tr align='center' class='encabezadotabla'>";
						echo "<td>C&oacute;digo</td>";
						echo "<td>Nombre</td>";
						echo "<td>Vía</td>";
						echo "<td>Cantidad a Aplicar</td>";
						echo "<td>Cantidad Aplicada</td>";
						echo "<td>Aplicaci&oacute;n</td>";
						echo "<td>Condici&oacute;n</td>";
						echo "<td>A necesidad</td>";
						echo "<td>Justificación</td>";

						if( $mostrarAplicaciones ){
							echo "<td>Aplicar</td>";
							echo "<td>Anular</td>";
						}

						echo "</tr>";

						$j = 0;
						foreach( $valueHoras->medicamentos as $keyMedicamentos => $valueMedicamentos ){

							$class2 = "class='fila".(($j%2)+1)."'";
							$j++;

							$classNombre = "";
							if( !empty($ccoayuda) && $mostrarAplicaciones )
							{
								if( $valueMedicamentos['puedeAplicar'] )
								{
									$classNombre = "class=fondoamarillo";
								}
							}

							echo "<tr $class2>";

							echo "<td $classNombre>";
							echo $valueMedicamentos[ 'codigo' ];
							echo "</td>";

							echo "<td style='width:300px' $classNombre>";
							echo $valueMedicamentos['nombre'];
							echo "</td>";

							echo "<td align=center style='width:50px'>";
							echo $valueMedicamentos['via'];
							echo "</td>";

							echo "<td align='center'>";
							echo $valueMedicamentos['cantidadAAplicar']." ".$valueMedicamentos['unidad'];
							echo "</td>";

							//Muestro la cantidad aplicada
							echo "<td align='center'>";
							if( !empty( $valueMedicamentos['cantidadAplicada'] ) ){

								echo $valueMedicamentos['cantidadAplicada']." ".$valueMedicamentos[ 'unidad' ];
							}
							else{
								echo "0 ".$valueMedicamentos['unidad'];
							}
							echo "</td>";


							$mostrarAplicar = "";
							$mostrarAnular = "";
							if( $valueMedicamentos['cantidadAplicada'] >= $valueMedicamentos['cantidadAAplicar'] ){

								if( $valueMedicamentos['confirmado'] === true ){
									echo "<td align='center'>";
								}
								else{
									echo "<td align='center' class='fondoAlertaConfirmar'>";
								}
								echo "<img src='/matrix/images/medical/movhos/checkmrk.ico'>";
								echo "<INPUT type='hidden' name='incanapl' value='".$valueMedicamentos['cantidadAplicada']."'>";

								$mostrarAplicar = "style='display:none'";
							}
							else{

								if( !empty( $valueMedicamentos['Jusjus'] ) || $valueMedicamentos['aNecesidad'] != "No" ){

									if( $valueMedicamentos['confirmado'] === true ){
										echo "<td align='center'>";
									}
									else{
										echo "<td align='center' class='fondoAlertaConfirmar'>";
									}
									echo "<img src='/matrix/images/medical/root/borrarAmarillo.png'>";
									echo "<INPUT type='hidden' name='incanapl' value='".$valueMedicamentos['cantidadAplicada']."'>";
								}
								else{
									if( $valueMedicamentos['confirmado'] === true ){
										echo "<td align='center' class='fondorojo'>";
									}
									else{
										echo "<td align='center' class='fondoAlertaConfirmar'>";
									}
									echo "<img src='/matrix/images/medical/root/borrar.png'>";
									echo "<INPUT type='hidden' name='incanapl' value='".$valueMedicamentos['cantidadAplicada']."'>";
								}

								$mostrarAnular = "style='display:none'";
							}

							$deshabilitarAnular="";
							$noPuedeAnular = consultarSiPuedeAnular($valueDatos->historia,$valueDatos->ingreso,$valueMedicamentos[ 'codigo' ],$valueMedicamentos[ 'idOriginal' ]);
							if($noPuedeAnular)
							{
								$deshabilitarAnular = "disabled='disabled'";
							}

							echo "</td>";

							echo "<td align='center'>";
							echo @$valueMedicamentos['condicion'];
							echo "</td>";

							echo "<td align='center' name='tdANecesidad'>";
							echo @$valueMedicamentos['aNecesidad'];
							echo "</td>";

							//Justificación
							if( $mostrarAplicaciones ){

								if( $valueMedicamentos['puedeAplicar'] )
								{
									/****************************************************************************************
									 * Octubre 28 de 2014
									 ***************************************************************************************/
									//Justificación
									 //Seleccionar JUSTIFICACIONES
									 echo "<td align='center'>";

									$q = " SELECT juscod, jusdes "
										."   FROM ".$wbasedato."_000023"
										."  WHERE justip = 'A' "       //A: Aplicacion en Ipod
										."    AND jusest = 'on' ";

									$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									$num = mysql_num_rows($res);

																				// cambiarJustificacion( cmp ,    whis                , wing                  ,                   wfecha_actual                                       ,         whora_par_actual                                          , wpac    , wido                                , idx )
									echo "<select name='slJus' onchange='cambiarJustificacion( this, {$valueDatos->historia}, {$valueDatos->ingreso}, \"".date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"".date( "H", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"pppp\", {$valueMedicamentos[ 'idOriginal' ]}, {$valueMedicamentos[ 'indexMed' ]}, \"$ubicacionPaciente->servicioTemporal\" )'>";


									echo "<option> </option>";
									while( $row = mysql_fetch_array($res) )
									{
										if( trim( $valueMedicamentos['codJus'] ) == trim( $row[0] ) )
											echo "<option selected>".$row[0]." - ".$row[1]."</option>";
										else
											echo "<option>".$row[0]." - ".$row[1]."</option>";
									}
									echo "</select>";

									 /**************************************************************************************/
								}
								else{
									echo "<td></td>";
								}
							}
							else{
								//Justificación
								echo "<td align='center'>";
								echo $valueMedicamentos['Jusjus'];
								echo "</td>";
							}


							if( $mostrarAplicaciones ){

								if( $valueMedicamentos['puedeAplicar'] ){

									$servicioPcienteAyudaDx = "";
									if( !empty( $ccoayuda ) ){
										$servicioPcienteAyudaDx = !empty( $ubicacionPaciente->servicioTemporal ) ? $ubicacionPaciente->servicioTemporal : $ubicacionPaciente->servicioActual;
									}

									//Aplicar
									if( empty($mostrarAnular) || $valueMedicamentos[ 'saldo' ] ){


										if( !$valueMedicamentos['dosisVariable'][ 'Defrci' ] ){
											echo "<td align='center'>";
											echo "<INPUT type='checkbox' id='chkAplicarMed_".$valueMedicamentos[ 'codigo' ]."_".$valueMedicamentos[ 'idOriginal' ]."_".date( "G", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."' name='chkAplicar' onclick='aplicarArticulo( this, {$valueDatos->historia}, {$valueDatos->ingreso}, \"".date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"".date( "H", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"\", \"pppp\", {$valueMedicamentos[ 'idOriginal' ]}, {$valueMedicamentos[ 'indexMed' ]}, \"{$valueMedicamentos[ 'codigo' ]}\", \"$servicioPcienteAyudaDx\" );' $mostrarAplicar>";
											echo "</td>";
										}
										else{
											echo "<td align='center'>";
											echo "<select id='chkAplicarMed_".$valueMedicamentos[ 'codigo' ]."_".$valueMedicamentos[ 'idOriginal' ]."_".date( "G", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."' name='chkAplicar' onChange='aplicarArticulo( this, {$valueDatos->historia}, {$valueDatos->ingreso}, \"".date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"".date( "H", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"\", \"pppp\", {$valueMedicamentos[ 'idOriginal' ]}, {$valueMedicamentos[ 'indexMed' ]}, \"{$valueMedicamentos[ 'codigo' ]}\", \"$servicioPcienteAyudaDx\", \"on\" );' $mostrarAplicar>";
											echo "<option></option>";

											for( $inc = $valueMedicamentos['dosisVariable'][ 'Defcai' ]; $inc <= $valueMedicamentos['dosisVariable'][ 'Defcas' ]; $inc += $valueMedicamentos['dosisVariable'][ 'Defesc' ] )
											 {
											   echo "<option>$inc</option>";
											 }

											echo "</select>";
											echo "</td>";
										}
									}
									else{
										echo "<td align='center' style='background-color:#3CB648;' onMouseover='mostrarTooltip( this );'>";
										echo "<a name='aTitle' title='{$valueMedicamentos[ 'msgSaldo' ]}'>NA</a>";
										echo "</td>";
									}

									//Anular
									echo "<td align='center'>";
									echo "<INPUT type='checkbox' id='chkAnularMed_".$valueMedicamentos[ 'codigo' ]."_".$valueMedicamentos[ 'idOriginal' ]."_".date( "G", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."' name='chkAnular' onclick='anularAplicacion( this, {$valueDatos->historia}, {$valueDatos->ingreso}, \"".date( "Y-m-d", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"".date( "H", $fechaFinal-$tiempoAMostrar*3600 + $l*3600 +1 )."\", \"\", \"pppp\", {$valueMedicamentos[ 'idOriginal' ]}, {$valueMedicamentos[ 'indexMed' ]}, \"{$valueMedicamentos[ 'codigo' ]}\", {$valueMedicamentos['cantidadAAplicar']}, {$valueMedicamentos['cantidadFraccion']}, \"{$valueMedicamentos['noEnviar']}\", \"{$valueMedicamentos['esStock']}\", \"$servicioPcienteAyudaDx\" );' $mostrarAnular $deshabilitarAnular>";
									echo "</td>";
								}
								else{
									echo "<td></td><td></td>";
								}
							}

							echo "</tr>";

							//Si el medicamento tiene observaciones relacionadas se imprimiran.
							if(trim($valueMedicamentos['observacion'])!="")
								{
									echo "<tr class=fila1><td colspan=11><b>Observación:</b>&nbsp;".$valueMedicamentos['observacion']."</td></tr>";

								}


						}

						echo "</table>";

						echo "<br><INPUT TYPE='button' value='Cerrar' onClick='$.unblockUI();' style='width:100'>";

						echo "</div>";
						//fin de pintar minireporte

					}

					echo "</td>";
				}
			}

			echo "</tr>";
		}
		echo "<input type='hidden' value='".$cadenaMed."' name='cadenaProceConMed' id='cadenaProceConMed'>";
		echo "</table>";
	}
	else{
		echo "<center><b>NO SE ENCONTRARON DATOS</b></center>";
	}
}

/************************************************************************************************************************************
 * 															FIN DE FUNCIONES
 ************************************************************************************************************************************/

// include_once( "root/comun.php" );
// include_once( "movhos/movhos.inc.php" );

//Si esta abierta la pagina de gestion de enfermeria para las pantallas de los pisos hara que la session user sea true y siempre se pueda abrir este programa.
if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){

	$_SESSION["user"] = true;

}

if(!$_SESSION["user"]){
	exit("<b>Usuario no registrado</b>");
}
else{

	$conex = obtenerConexionBD("matrix");

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}



	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");
	$codCcoCirugia = consultarCcoCirugia();
	$codCcoUrgencias = consultarCcoUrgencias();
	$estados_examenes = consultarEstadosExamenesRol();

	echo "<form method=post>";

	if( empty($fecha) ){
		$fecha = date( "Y-m-d" );
	}

	if( isset( $mostrarFechaActual ) && $mostrarFechaActual == 'on' ){
		$fecha = date( "Y-m-d" );
	}

	if( empty($mostrar) ){
		$mostrar = "off";
	}

	echo "<INPUT type='hidden' id='wemp_pmla' name='wemp_pmla' value='$wemp_pmla'>";
	echo "<INPUT type='hidden' id='ccoCirugia' name='ccoCirugia' value='$codCcoCirugia'>";
	echo "<INPUT type='hidden' id='ccoUrgencias' name='ccoCirugia' value='$codCcoUrgencias'>";
	echo "<INPUT type='hidden' id='wbasedato' name='wbasedato' value='$wbasedato'>";
	echo "<INPUT type='hidden' id='usuario' value='".$_SESSION["user"]."'>";
	$user_session = explode('-',$_SESSION['user']);
	$wuser = $user_session[1];
	echo "<INPUT type='hidden' id='user' value='".$wuser."'>";


	echo "<INPUT type='hidden' name='waux' value='$waux'>";

	if( !empty( $ccoayuda ) && empty( $ccoayudaori ) ){
		$ccoayudaori = $ccoayuda;
	}

	if( $mostrar == "off"  ){

		encabezado( "SISTEMA DE GESTION DE ENFERMERIA", $actualiz ,"clinica" );

		//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		if( empty( $ccoayudaori ) ){

			$nameSelect = "slCcoDestino";
			$cco="Ccoipd,Ccourg,Ccoior,Ccocir";	//Febrero 02 de 2016:
			$sub="off";
			$tod="";
			$ipod="off";

			//$cco=" ";
			$centrosCostos = consultaCentrosCostos($cco);
		}
		else{
			$nameSelect = "ccoayuda";
			$cco="Ccoayu";	//Febrero 02 de 2016:
			$sub="off";
			$tod="";
			$ipod="off";

			$centrosCostos = consultaCentrosCostos($cco);

			//Solo saco los ccos necesarios
			$ccs = array();
			$ccsAyuda = explode( ",", $ccoayudaori );

			foreach( $centrosCostos as $key => $value ){
				if( in_array( $value->codigo, $ccsAyuda ) ){
					$ccs[] = $value;
				}
			}

			$centrosCostos = $ccs;
		}

		echo "<table align='center' border=0>";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod,$nameSelect);

		echo $dib;

		echo "</table>";

		echo "<table align=center id='tabla_zonas' style='display:none;'>";
		echo "<tr><td class=fila1>Seleccionar Zona:</td><td class=fila2><span id='select_zonas'></div></td></tr>";
		echo "<tr><td> &nbsp; </td></tr>";
		echo "</table>";


		echo "<table align='center' width=370>";
		//echo "</tr>";
		echo "<tr>";

		echo "<td class='fila1' align='center' width=73>Fecha</td>";

		echo "<td class='fila2'>";
		campoFechaDefecto( "fecha", $fecha );
		echo "</td>";
		echo "</tr>";


		//echo "</tr>";

		echo "</table>";

		echo "<br><table align='center'>";

		echo "<tr><td>";
		// echo "<center><INPUT type='button' value='Aceptar' onclick='document.forms[0].submit();'></center>";
		echo "<center><INPUT type='button' value='Aceptar' onclick='ingresoGestion();'></center>";
		echo "</td>";

		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";

		echo "</table>";

		echo "<INPUT type='hidden' name='mostrar' value='on'>";

		if( !empty( $ccoayudaori ) ){
			echo "<INPUT type='hidden' name='ccoayudaori' id='ccoayudaori' value='$ccoayudaori'>";
		}
	}
	else{
		list( $ccoayuda ) = explode( "-", $ccoayuda );
		echo "<INPUT TYPE='hidden' id='inRecargar' value='on'>";

		//Defino constantes para los colores y poder usarlos en el programa
		//Esto permite que lo pueda cambiar los colores sin problemas
		define("CLR_VERDE", 1 );
		define("CLR_AMARILLO", 2 );
		define("CLR_ROJO", 3 );
		define("CLR_KSC", 4 );
		define("CLR_SK", 5 );

		//Creo Array de colores, sirven para pintar las celdas de los distintos colores
		$colores = Array();
		$colores[ 0 ] = "NA";
		$colores[ CLR_VERDE ] = "NA";
		$colores[ CLR_AMARILLO ] = "NA";
		$colores[ CLR_ROJO ] = "Rojo";
		$colores[ CLR_KSC ] = "NA";
		$colores[ CLR_SK ] = "NA";

		$horasRondasMedGestion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'horasRondasMedGestion');
		$horasRondasMedGestion = explode("-",$horasRondasMedGestion);

		$gestion_enfermeria = $horasRondasMedGestion[0]; //Tiempo en segundo inicial para validacion de pantalla inactiva.
		$gestion_pisos = $horasRondasMedGestion[1]; //Tiempo maximo para la segunda notificacion de pantalla inactiva.

		$mostrarFechaActual = 'off';
		if( $fecha == date( "Y-m-d" ) ){
			//Si la fecha es la actual el tiempo es de 12 horas
			$tiempoAMostrar = $gestion_enfermeria;	//Indica cuanto es el tiempo a mostrar en horas en el reporte
			$mostrarFechaActual = 'on';

			//Gestion de enfermeria pisos
			if($wsp == 'on' and $mostrar == 'on' and $slCcoDestino != ''){
				$tiempoAMostrar = $gestion_pisos;
			}
		}
		else{
			//Si la fecha de consulta es diferente a la actual se debe mostrar las 24 horas
			$tiempoAMostrar = $gestion_enfermeria;	//Indica cuanto es el tiempo a mostrar en horas en el reporte
		}


		//Separo la información del cco
		list( $ccoCodigo, $ccoDescipcion ) = explode( "-", $slCcoDestino );
		
		
		$wipimpresoraga = consultarImpresoraGA_inc( $conex, $wbasedato, $ccoCodigo );
		echo "<input type='hidden' name='wipimpresoraga' id='wipimpresoraga' value='".$wipimpresoraga."'/>";
		
		echo "<INPUT TYPE='hidden' name='slCcoDestino' value='".( empty( $slCcoDestino ) ? $ccoayuda : $slCcoDestino )."'>";
		echo "<INPUT TYPE='hidden' name='fecha' value='$fecha'>";
		echo "<INPUT TYPE='hidden' name='mostrarFechaActual' value='$mostrarFechaActual'>";

		if(!isset($sala)){

			$sala = "%";
		}

		echo "<INPUT TYPE='hidden' name='sala' value='$sala'>";

		//Se debe mostrar las ultimas 24 horas si la fecha es la actual
		if( $fecha == date( "Y-m-d" ) ){

			//Busco la hora par mas cercana
			$hora = intval( date( "G" )/2 )*2+2;

			$fechaFinal = strtotime( date( "Y-m-d 00:00:00" ) ) + $hora*3600 - 1;

			$fechaInicial = $fechaFinal - $tiempoAMostrar*3600 + 1;

			$hora = gmdate( "G", $hora*3600 );
		}
		else{
			$hora = 0;

			$fechaInicial = strtotime( $fecha." 00:00:00" );
			$fechaFinal = $fechaInicial + $tiempoAMostrar*3600-1;
		}

		$informacionArticulos = Array();

		/************************************************************************************************************************
		 * Consulto todas las aplicaciones pacientes y la guardo en un arreglo para consultar las aplicaciones
		 * de cada medicamento por variable
		 ************************************************************************************************************************/
		 //AND SUBSTRING_INDEX( aplron, ':', 1 ) % 2 = 0			Se quita esta linea para que salgan todos los articulos asi no se encuentren en hora par

		$wcirugia = esCirugia($conex,$ccoCodigo);
		$wtcx = consultarAliasPorAplicacion($conex, $wemp_pmla, "tcx");


		switch(1){

			case ($ccoayuda != ''):

				$codigosccoayuda_aux = explode(",",$ccoayuda);
				$codigosccoayuda = implode("','",$codigosccoayuda_aux);

				$sql = "SELECT * FROM
						(SELECT Aplfec, Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos, Aplido
						  FROM {$wbasedato}_000018 b, {$wbasedato}_000015 a, ".$wbasedato."_000011
						 WHERE  aplhis = ubihis
							AND apling = ubiing
							AND ubisac = Ccocod
							AND ubiste in ('".trim($codigosccoayuda)."')
							AND aplfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
							AND aplest = 'on'
					   GROUP BY 1,2,3,4,5,6,8
						UNION
						SELECT Aplfec, Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos, Aplido
						  FROM {$wbasedato}_000018 b, {$wbasedato}_000015 a, ".$wbasedato."_000011
						 WHERE  aplhis = ubihis
							AND apling = ubiing
							AND ubisac = Ccocod
							AND ubisac in ('".trim($codigosccoayuda)."')
							AND aplfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
							AND aplest = 'on'
					   GROUP BY 1,2,3,4,5,6,8) as t";

			break;


			case $wcirugia:

				$sql = "SELECT Aplfec, Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos, Aplido
					  FROM {$wbasedato}_000018 b, {$wbasedato}_000015 a, {$wtcx}_000011, ".$wbasedato."_000011
				     WHERE  aplhis = ubihis
						AND apling = ubiing
						AND ubisac = Ccocod
						AND turhis = aplhis
						AND turnin = apling
						AND aplfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
						AND turfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
						AND aplest = 'on'
					   GROUP BY 1,2,3,4,5,6,8";
				break;

			default:

				$sql = "SELECT
						Aplfec, Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos, Aplido
					FROM
						{$wbasedato}_000020 b, {$wbasedato}_000015 a
					WHERE
						habcco = '$ccoCodigo'
						AND habhis != ''
						AND aplhis = habhis
						AND apling = habing
						AND aplfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
						AND aplest = 'on'
					GROUP BY
						1,2,3,4,5,6,8
					UNION
					SELECT
						Aplfec, Aplhis, Apling, Aplart, SUBSTRING_INDEX( aplron, ':', 1 ) as Ronda, SUBSTRING_INDEX( aplron, '-', -1 ) as Meridiano, sum(Apldos) as Apldos, Aplido
					FROM
						{$wbasedato}_000018 b, {$wbasedato}_000015 a
					WHERE
						ubisac = '1130'
						AND aplhis = ubihis
						AND apling = ubiing
						AND aplfec BETWEEN '".date( "Y-m-d", $fechaInicial )."' AND '".date( "Y-m-d", $fechaFinal )."'
						AND aplest = 'on'
					GROUP BY
						1,2,3,4,5,6,8";

			break;
		}
		//echo $sql."<br>";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		while( $rows = mysql_fetch_array($res) ){

			//Conviero la ronda en la hora par mas cercano igual o anterior la hora
			$rows[ 'Ronda' ] = intval( $rows[ 'Ronda' ]/2 )*2;

			if( strtoupper( trim( $rows['Meridiano'] ) ) == "PM" ){	//Si la ronda es PM
				if( $rows[ 'Ronda' ]*1 < 12 ){
					$rows[ 'Ronda' ] += 12;
				}
			}
			else{	//Si la ronda es AM
				if( $rows[ 'Ronda' ]*1 == 12 ){
					$rows[ 'Ronda' ] = 0;
				}
				else{
					$rows[ 'Ronda' ] = $rows[ 'Ronda' ]*1;
				}
			}

			if( $rows[ 'Aplfec' ] == date( "Y-m-d", $fechaInicial ) ){
				if( $rows[ 'Ronda' ] >= date( "G", $fechaInicial ) ){
					@$aplicaciones[ $rows['Aplhis']."-".$rows['Apling'] ][ strtoupper( $rows['Aplart'] ) ][ $rows['Aplido'] ][ $rows['Ronda']*1 ] = $rows['Apldos']*1;
				}
			}
			else{
				if( $rows[ 'Ronda' ] <= date( "G", $fechaInicial ) ){
					@$aplicaciones[ $rows['Aplhis']."-".$rows['Apling'] ][ strtoupper( $rows['Aplart'] ) ][ $rows['Aplido'] ][ $rows['Ronda']*1 ] = $rows['Apldos']*1;
				}
			}
		}
		/************************************************************************************************************************/
		$wurgencias = esUrgencias($conex,$ccoCodigo);
		$wcirugia = esCirugia($conex,$ccoCodigo);
		$whemodinamia = esHemodinamia($conex,$ccoCodigo);
		$wfecha_actual = date('Y-m-d');
		$dia_anterior = date("Y-m-d", strtotime("$wfecha_actual -1 day"));

		$relacion_pacientes = "";
		$filtro_zonas = "";
		$campo_sala = "''";
		$relacion_tabla_urgencias = "";
		$tabla_urgencias = "";
		$pacientes_sin_ubicacion = "";
		$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
		$historiasconOrdenesUrgencias = array();

		//Consulto los pacientes que tienen ordene electronicas, con el resultado se forma un arreglo con las historias e ingresos concatenadas y luego se
		//agrega un NOT IN a tercer union para que no los tenga en cuenta, ya que estas historias saldran de primero en la lista.
		$q_pacientes_ordenes = " SELECT concat( ubihis,ubiing ) as hising
								   FROM root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016, ".$wbasedato."_000011, ".$wbasedato."_000053 f
								  WHERE ubihis = orihis
									AND ubiing = oriing
									AND oriori = '$wemp_pmla'
									AND oriced = pacced
									AND oritid = pactid
									AND ubiald != 'on'
									AND ubisac = '".trim($ccoCodigo)."'
									AND ubihis = inghis
									AND ubiing = inging
									AND ccocod = ubisac
									AND ubihis = karhis
									AND ubiing = karing
									AND karord in ('on','off')
									AND f.Fecha_data BETWEEN '".$dia_anterior."' AND '".$wfecha_actual."'
									AND ccoest = 'on'";
		$res_pacientes_ordenes = mysql_query($q_pacientes_ordenes);

		while( $row_pac_ordenes = mysql_fetch_assoc( $res_pacientes_ordenes ) ){
	      array_push( $historiasconOrdenesUrgencias, "'".$row_pac_ordenes['hising']."'" );
		}

		//Concateno los datos para realizar el NOT IN.
		if( count( $historiasconOrdenesUrgencias ) > 0 ){
			$condicionPacientesOrdenes = " AND concat( ubihis,ubiing ) NOT IN (".implode(",", $historiasconOrdenesUrgencias).") ";
		  }

		//Pacientes con muerte en on pero con saldo en insumos
		$q_pac_con_saldo_insum = "     SELECT Ubisac as Habcco,Ubihan as Habcod,Ubihis as Habhis, Ubiing as Habing, CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre,
										Cconom, Ubihis, Ubiing, Ubiptr, Ubialp, Ubifap, Ubihap, pacced, pactid, Ingres, Ingnre, tabla18.id as id_tabla18, Ubisan, '2000' as Habord, Ubimue
										   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016, ".$wbasedato."_000011, ".$wbasedato."_000227
										  WHERE ubihis = orihis
											AND ubiing = oriing
											AND oriori = '$wemp_pmla'
											AND oriced = pacced
											AND oritid = pactid
											AND ubisac = '".trim($ccoCodigo)."'
											AND ubihis = inghis
											AND ubiing = inging
											AND ccocod = ubisac
											AND ccoest = 'on'
											AND Carhis = ubihis
											AND Caring = ubiing
											AND ubimue = 'on'
											AND (Carcca - Carcap - Carcde) > 0
									   GROUP BY Ubihis";

		 //Pacientes marcados con muerte.
	     $pacientes_muerte = 	 "SELECT Ubisac as Habcco,'' as Habcod,Ubihis as Habhis,Ubiing as Habing, CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre,
										 Cconom, Ubihis, Ubiing, Ubiptr, Ubialp, Ubifap, Ubihap, pacced, pactid, Ingres, Ingnre, {$wbasedato}_000018.id as id_tabla18, Ubisan, '3000' as Habord, Ubimue
									FROM ".$wbasedato."_000018, root_000036, root_000037, ".$wbasedato."_000011, ".$wbasedato."_000016
								   WHERE ubihis = orihis
									 AND Ubiing = Oriing
									 AND oriori  = '".$wemp_pmla."'
									 AND ubisac = '".trim($ccoCodigo)."'
									 AND oriced  = pacced
									 AND oritid  = pactid
									 AND ubisac  = ccocod
									 AND ubihis = inghis
									 AND ubiing = inging
									 AND ccohos  = 'on'
									 AND ubimue  = 'on'
									 AND ccourg != 'on'
									 AND ubiald != 'on'
									 AND ubiptr != 'on'
								   ORDER BY 2 ";

		switch(1){

			//Centro de costos de hemodinamia.
			case ($ccoayuda != ''):

				$codigosccoayuda_aux = explode(",",$ccoayuda);
				$codigosccoayuda = implode("','",$codigosccoayuda_aux);

				foreach($codigosccoayuda_aux as $key => $value){

					$datos_cco = consultarCentroCosto($conex, $value, $wbasedato);
					$texto_cco .= $datos_cco->codigo." - ".$datos_cco->nombre."<br>";
				}

				echo "<input type=hidden id=texto_td_cco value='".$texto_cco."'>";

				$sql = "SELECT * FROM
						(SELECT Ubisac as Habcco, Habcod, Habhis, Habing,
									CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
									Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes, Habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan, '2' as orden
						   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016, ".$wbasedato."_000020
						  WHERE ubiald = 'off'
							AND Ccocir != 'on'
							AND ubisac = Ccocod
							AND oriori = '".$wemp_pmla."'
							AND ubihis = orihis
							AND ubiing = oriing
							AND oriced = pacced
							AND oritid = pactid
							AND orihis = inghis
							AND oriing = inging
							AND habhis = inghis
							AND habing = inging
							AND ubimue != 'on'
							AND Ubiste in ('".trim($codigosccoayuda)."')
							UNION
						SELECT Ubisac as Habcco, (SELECT Cconom FROM ".$wbasedato."_000011 WHERE Ccocod = Ubisac) as Habcod, ubihis as Habhis, ubiing as Habing,
									CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
									Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes,'2000' as habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan, '1' as orden
						   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016
						  WHERE ubiald = 'off'
							AND ubisac = Ccocod
							AND oriori = '".$wemp_pmla."'
							AND ubihis = orihis
							AND ubiing = oriing
							AND oriced = pacced
							AND oritid = pactid
							AND orihis = inghis
							AND oriing = inging
							AND ubimue != 'on'
							AND Ubisac in ('".trim($codigosccoayuda)."')) as t
					   GROUP BY habhis, habing
					   ORDER BY orden DESC, Habord*1, Habcod";

				break;

			//Centro de costos de urgencias
			case ($wurgencias):

				if($sala != '%'){

					$filtro_zonas = "	AND mtrsal = '".$sala."'";

				}


				//Muestra primero los que tienen ordenes electronicas.
				$sql = 	 "SELECT * FROM
						  (SELECT Ubisac as Habcco, 'Urg' as Habcod, ubihis as 'Habhis', ubiing as Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes, Mtrsal as consala,
								habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan "
						."   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016,
								".$wbasedato."_000011, ".$wbasedato."_000053 f, ".$whce."_000022 , ".$wbasedato."_000020 "
						."  WHERE ubihis = orihis "
						."    AND ubiing = oriing "
						."    AND oriori = '".$wemp_pmla."'"       //Empresa Origen de la historia,
						."    AND oriced = pacced "
						."    AND oritid = pactid "
						."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
						."    AND ubisac = '".trim($ccoCodigo)."'"      //Servicio Actual
						."    AND ubihis = inghis "
						."    AND ubiing = inging "
						."	  AND ccocod = ubisac "
						."	  AND ubihis = karhis "
						."	  AND ubiing = karing "
						."	  AND karord in ('on','off') "
						."	  AND f.Fecha_data BETWEEN '".$dia_anterior."' AND '".$wfecha_actual."'"
						."    AND ccoest = 'on'"
						."	  AND mtrhis = karhis "
						."	  AND mtring = karing "
						."	  AND ubihis = habhis "
						."    AND ubiing = habing "
						.$filtro_zonas
						//Consulta que contiene los pacientes sin ubicacion, quiere decir sin sala y sin cubiculo, se agregara al resultado de cualquier zona.
						." UNION
						 SELECT Ubisac as Habcco, 'Urg' as Habcod, ubihis as 'Habhis', ubiing as Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '2' as ordenes,
								Mtrsal as consala,'3000' as habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan
						   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016,
								".$wbasedato."_000011, ".$whce."_000022
						  WHERE ubihis = orihis
							AND ubiing = oriing
							AND oriori = '".$wemp_pmla."'
							AND oriced = pacced
							AND oritid = pactid
							AND ubiald != 'on'
							AND ubisac = '".trim($ccoCodigo)."'
							AND ubihis = inghis
							AND ubiing = inging
							AND ccocod = ubisac
							AND ccoest = 'on'
							AND mtrcua = ''
							$filtro_zonas
							AND mtrhis = ubihis
							AND mtring = ubiing"
						//Paciente con cubiculo asignado pero sin ubicacion, muestra los pacientes que tienen alta en proceso liberados por el medico
						." UNION
						 SELECT Ubisac as Habcco, 'Urg' as Habcod, ubihis as 'Habhis', ubiing as Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '2' as ordenes,
								Mtrsal as consala,habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan
						   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016,
								 ".$wbasedato."_000011, ".$whce."_000022, ".$wbasedato."_000020
						  WHERE ubihis = orihis
							AND ubiing = oriing
							AND oriori = '".$wemp_pmla."'
							AND oriced = pacced
							AND oritid = pactid
							AND ubiald != 'on'
							AND ubisac = '".trim($ccoCodigo)."'
							AND ubihis = inghis
							AND ubiing = inging
							AND ccocod = ubisac
							AND ccoest = 'on'
							AND mtrcua = 'on'
							$filtro_zonas
							AND mtrhis = ubihis
							AND mtring = ubiing
							AND ubihis = habhis
							AND ubiing = habing "
						." UNION
						 SELECT Ubisac as Habcco, 'Urg' as Habcod, ubihis as 'Habhis', ubiing as Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '2' as ordenes,
								Mtrsal as consala,'5000' as habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan
						   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016,
								 ".$wbasedato."_000011, ".$whce."_000022
						  WHERE ubihis = orihis
							AND ubiing = oriing
							AND oriori = '".$wemp_pmla."'
							AND oriced = pacced
							AND oritid = pactid
							AND ubiald != 'on'
							AND ubisac = '".trim($ccoCodigo)."'
							AND ubihis = inghis
							AND ubiing = inging
							AND ccocod = ubisac
							AND ccoest = 'on'
							AND mtrcua = ''
							AND mtrsal = ''
							AND mtrhis = ubihis
							AND mtring = ubiing "
						//Este union muestra los pacientes que no tiene ordenes electronicas.
						."	UNION "
						." SELECT Ubisac as Habcco, 'Urg' as Habcod, ubihis as 'Habhis', ubiing as Habing,
								 CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								 Ubialp, Ubifap, Ubihap, pacced, pactid, '2' as ordenes,
								 Mtrsal as consala, habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan "
						."   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016,
								 ".$wbasedato."_000011, ".$whce."_000022, ".$wbasedato."_000020 "
						."  WHERE ubihis = orihis "
						."    AND ubiing = oriing "
						."    AND oriori = '".$wemp_pmla."'"       //Empresa Origen de la historia,
						."    AND oriced = pacced "
						."    AND oritid = pactid "
						."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
						."    AND ubisac = '".trim($ccoCodigo)."'"      //Servicio Actual
						."    AND ubihis = inghis "
						."    AND ubiing = inging "
						."	  AND ccocod = ubisac "
						."    AND ccoest = 'on' "
						."	  AND mtrhis = ubihis "
						."	  AND mtring = ubiing "
						."    AND mtrhis = ubihis "
						."    AND mtring = ubiing "
						."	  AND ubihis = habhis "
						."    AND ubiing = habing "
						.$filtro_zonas
						.$condicionPacientesOrdenes		//Pacientes con ordenes electronicas, esta variable excluye con un NOT IN los pacientes ya que se mostraran en el primer UNION.
					//En este union estan los pacientes con sala asignada y que no tienen cubiculo relacionado.
						."	UNION "
						." SELECT Ubisac as Habcco, 'Urg' as Habcod, ubihis as 'Habhis', ubiing as Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '2' as ordenes,
								Mtrsal as consala,'2000' as habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, Ubisan  "
						."   FROM root_000036, root_000037, ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000016,
								 ".$wbasedato."_000011, ".$whce."_000022"
						."  WHERE ubihis = orihis "
						."    AND ubiing = oriing "
						."    AND oriori = '".$wemp_pmla."'"       //Empresa Origen de la historia,
						."    AND oriced = pacced "
						."    AND oritid = pactid "
						."    AND ubiald != 'on' "             //Que no este en Alta Definitiva
						."    AND ubisac = '".trim($ccoCodigo)."'"      //Servicio Actual
						."    AND ubihis = inghis "
						."    AND ubiing = inging "
						."	  AND ccocod = ubisac "
						."    AND ccoest = 'on' "
						."	  AND ubihis = mtrhis "
						."	  AND ubiing = mtring "
						."	  AND mtrtra = 'on' "
						."	  AND mtrest = 'on' "
						."	  AND mtrcur = 'off' "
						."	  AND mtrsal LIKE '%".$sala."%'"
						." ) AS t GROUP BY 2,3"
						."  ORDER BY CONVERT(habord,UNSIGNED INTEGER), consala DESC";

				break;
			//Centro de  costos de cirugia.
			case ($wcirugia):

					$sql = " SELECT * FROM
					(SELECT Ubisac as Habcco, Habcod, Habhis, Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes, Habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, turtur, Ubisan
					   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016, ".$wtcx."_000011, ".$wbasedato."_000020
					  WHERE ubiald = 'off'
					    AND Ccocir != 'on'
						AND ubisac = Ccocod
						AND oriori = '".$wemp_pmla."'
						AND ubihis = orihis
						AND ubiing = oriing
						AND oriced = pacced
						AND oritid = pactid
						AND orihis = inghis
						AND oriing = inging
						AND habhis = inghis
						AND habing = inging
						AND turhis = inghis
						AND turnin = inging
						AND ubimue != 'on'
						AND turfec BETWEEN '".$dia_anterior."' AND '".$wfecha_actual."'
						UNION
					SELECT Ubisac as Habcco, 'Cx' as Habcod, ubihis as Habhis, ubiing as Habing,
								CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre, Cconom, Ubiptr,
								Ubialp, Ubifap, Ubihap, pacced, pactid, '1' as ordenes,'800' as habord, Ingres, Ingnre, tabla18.Fecha_data as 18_fecha_data, tabla18.Hora_data as 18_hora_data, tabla18.id as id_tabla18, turtur, Ubisan
					   FROM ".$wbasedato."_000018 as tabla18, ".$wbasedato."_000011, root_000037, root_000036,".$wbasedato."_000016, ".$wtcx."_000011
					  WHERE ubiald = 'off'
						AND ubisac = Ccocod
						AND oriori = '".$wemp_pmla."'
						AND ubihis = orihis
						AND ubiing = oriing
						AND oriced = pacced
						AND oritid = pactid
						AND orihis = inghis
						AND oriing = inging
						AND turhis = inghis
						AND turnin = inging
						AND ubimue != 'on'
						AND turfec BETWEEN '".$dia_anterior."' AND '".$wfecha_actual."') AS t
				   GROUP BY habhis, habing
				   ORDER BY habcod, habord";

				   //echo '<pre> $sql '.$sql.'</pre>';

				break;
			default:
					//Centro de costos diferente de urgencias, cirugia y hemodinamia.
					if($sala != '%'){

						$filtro_zonas = "	AND habzon = '".$sala."'";
					}

					$sql = "SELECT * FROM (
								SELECT
									Habcco,Habcod,Habhis,Habing, CONCAT(pacno1,' ',pacno2,' ',pacap1,' ',pacap2) as Nombre,
									Cconom, Ubihis, Ubiing, Ubiptr, Ubialp, Ubifap, Ubihap, pacced, pactid, Ingres, Ingnre, {$wbasedato}_000018.id as id_tabla18, Ubisan, Habord, Ubimue
								FROM
									{$wbasedato}_000020,{$wbasedato}_000018,{$wbasedato}_000011,{$wbasedato}_000016,
									root_000036, root_000037
								WHERE
									habcco = '$ccoCodigo'
									AND habest = 'on'
									AND ubihis = habhis
									AND ubiing = habing
									AND orihis = habhis
									AND ccocod = habcco
									AND ccoipd = 'on'
									AND ccoest = 'on'
									AND oriori = '$wemp_pmla'
									AND pacced = Oriced
									AND pactid = Oritid
									AND ubihis = inghis
									AND ubiing = inging
									$filtro_zonas
									UNION
									$q_pac_con_saldo_insum
									UNION
									$pacientes_muerte

									) AS t
							GROUP BY Ubihis, Ubiing
							ORDER BY Habord*1, Habcod";

				break;
		}
		//echo $sql;
		$resHabitaciones = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql -".mysql_error() );
		$numHabitaciones = mysql_num_rows( $resHabitaciones );

		if( $numHabitaciones > 0 ){

			//Recorro los pacientes encontrados
			for( $i = 0; $rowsHabitaciones = mysql_fetch_array( $resHabitaciones ); $i++ ){

				$datosHabitaciones[ $i ] = new habitacion( $rowsHabitaciones['Habcco'], $rowsHabitaciones['Cconom'], $rowsHabitaciones['Habcod'], $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $rowsHabitaciones['Nombre'], $rowsHabitaciones['Ubiptr'], $rowsHabitaciones['Ubialp'], $rowsHabitaciones['Ubifap'], $rowsHabitaciones['Ubihap'], $rowsHabitaciones['pacced'], $rowsHabitaciones['pactid'], $rowsHabitaciones['Ingres'], $rowsHabitaciones['Ingnre']);

				//Verfica si tiene kardex hoy o ayer(maximo), en caso de tenerlo creara el arreglo de informacion para el paciente.
				$pac_tieneKardex_hoy = tieneKardex( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, $wfecha_actual );
				$pac_tieneKardex_ayer = tieneKardex( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, $dia_anterior );
				$datosHabitaciones[ $i ]->consala = $rowsHabitaciones['consala'];
				$datosHabitaciones[ $i ]->fecha_hora_term_consul = fecha_hora_terminacion_consulta($datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso); //Fecha y hora terminacion de la consulta //Cambio po req 4182 para usar el campo Mtrhtc
				$datosHabitaciones[ $i ]->id_tabla18 = $rowsHabitaciones['id_tabla18'];
				$datosHabitaciones[ $i ]->turno_cir = $rowsHabitaciones['turtur'];
				$datosHabitaciones[ $i ]->servicio_anterior = $rowsHabitaciones['Ubisan'];
				$datosHabitaciones[ $i ]->muerte = $rowsHabitaciones['Ubimue'];

				if( $pac_tieneKardex_hoy == 'on' or $pac_tieneKardex_ayer == 'on'){

					$tiempoDiaSiguiente = 0;

					//Ciclo por fechas
					for( $fIni = $fechaFinal-$tiempoAMostrar*3600+1; $fIni <= $fechaFinal; $fIni += $tiempoDiaSiguiente*3600 ){



						//Traigo la fecha y hora sin ceros iniciales y pares
						list( $fecha, $horaRonda ) = explode( "|", date( "Y-m-d|G", $fIni) );

						$fechaBaseKardex = $fecha;

						$tiempoDiaSiguiente = 24 - $horaRonda;

						$datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ] = consultarKardexConfirmado( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, $fecha );

						$datosHabitaciones[ $i ]->tieneKardex[ $fecha ] = tieneKardex( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, $fecha );

						//Consultando articulos del kardex
						if( $datosHabitaciones[ $i ]->tieneKardex[ $fecha ] != 'on' ){
							//Consulto todos los articulos del paciente para la fecha correspondiente
							$resArticulos = articulosKardexPorPaciente( $conex, $wbasedato, $wcenmez, $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], date( "Y-m-d", $fIni-24*3600 ) );

							$datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ] = consultarKardexConfirmado( $datosHabitaciones[ $i ]->historia, $datosHabitaciones[ $i ]->ingreso, date( "Y-m-d", $fIni-24*3600 ) );

							$fechaBaseKardex = date( "Y-m-d", $fIni-24*3600 );
						}
						else{
							//Consulto todos los articulos del paciente para la fecha correspondiente
							$resArticulos = articulosKardexPorPaciente( $conex, $wbasedato, $wcenmez, $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $fecha );
						}

						$numArticulos = mysql_num_rows( $resArticulos );

						if( $numArticulos > 0 ){

							//Ciclo por articulo
							for( $j = 0; $rowsArticulos = mysql_fetch_array($resArticulos); $j++ ){
								//La horas nunca superan las 24 horas
								$incremento = 2;
								for( $fechaInicial = $fIni, $whora_par_actual = $horaRonda; $whora_par_actual < 24 && $fechaInicial <= $fechaFinal; $whora_par_actual += $incremento, $fechaInicial +=$incremento*3600 ){

									$esArticuloAyudaDx = false;
									// $rowsArticulos['puedeAplicar'] = $ccoCirugia ? true : false;
									$rowsArticulos['puedeAplicar'] = false;
									if( empty( $ccoayuda ) ){
										$esArticuloAyudaDx = esArticuloDeAyudaDiagnostica( $conex, $wbasedato, $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $fecha, $rowsArticulos['kadart'], $rowsArticulos['Kadido'] );
									}
									else{
										if( $datosHabitaciones[ $i ]->dejarAplicarAyudadxCx )
											$rowsArticulos['puedeAplicar'] = esArticuloDeAyudaDiagnosticaPorCco( $conex, $wbasedato, $rowsHabitaciones['Habhis'], $rowsHabitaciones['Habing'], $fecha, $rowsArticulos['kadart'], $rowsArticulos['Kadido'], $ccoayuda );
									}

									$esDeRonda = false;
									if( !$esArticuloAyudaDx ){

										//Kardex confirmado?
										if( $fecha == date( "Y-m-d" ) ){
											$datosHabitaciones[ $i ]->horas[ $whora_par_actual ]->esKardexConfirmado = $datosHabitaciones[ $i ]->kardexConfirmado[ $fecha ];
										}
										else{
											$datosHabitaciones[ $i ]->horas[ $whora_par_actual ]->esKardexConfirmado = 'on';
										}


										//debo verificar que la ronda si pertenece al dia, el query no lo valida
										$articuloPertenceRonda = false;

										//Si la fecha y hora de inicio del medicamento es menor a la ronda verifico si pertence a la ronda
										$fechoriniUnix = strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos['kadhin'].":00:00" );
										if( $fechoriniUnix <= $fechaInicial ){

											//Se verifica si el articulo es a necesidad
											$expCondicion = explode( " - ", estaANecesidad( $rowsArticulos[ 'Kadcnd' ] ) );
											$esArticuloANecesidad = ( $expCondicion[1] == "AN" )? true : false;

											if( !$esArticuloANecesidad ){

												//Si no es a necesidad verifico qué el articulo pertenece a la ronda
												if( ( $fechaInicial - $fechoriniUnix )%( $rowsArticulos['perequ']*3600 ) == 0 ){
													$articuloPertenceRonda = true;
												}
											}
											else{
												//Si es a necesidad se considera que pertence a la ronda
												$articuloPertenceRonda = true;
												if( ( $fechaInicial - $fechoriniUnix )%( $rowsArticulos['perequ']*3600 ) == 0 ){
													$esDeRonda = true;
												}
											}
										}


										if( $articuloPertenceRonda ){

											// $incremento = $rowsArticulos['perequ'];

											/******************************************************************************
											 * Esto ya existía anteriormente
											 *****************************************************************************/

											$datosHabitaciones[ $i ]->numMedicamentos[ $whora_par_actual ]++;
											/********************************************************************************************************
											 * Febrero 24 de 2011
											 ********************************************************************************************************/
											$cambioFrecuencia = false;
											//Si hay cambio de frecuencia y el cambio fue realizado posterior a la confirmacion del kardex, no muesto el articulo
											if( !empty( $rowsArticulos['Kadfra'] ) ){

												$fhCambioFrecuencia = strtotime( $rowsArticulos['Kadfcf']." ".$rowsArticulos['Kadhcf'] );

												if( $fhCambioFrecuencia > $fechaInicial || ( $rowsArticulos['Karhco'] != "00:00:00" && $fechaInicial < strtotime( $fechaBaseKardex." ".$rowsArticulos['Karhco'] ) ) ){
													$rowsArticulos['perequ'] = consultarFrecuencia( $rowsArticulos['Kadfra'] );

													//Marzo 31 de 2015. Se corrige los parametros de la función strtotime
													//Averiguo si no pertenece a la ronda
													if( ( strtotime( $rowsArticulos['kadfin']." ".substr( $rowsArticulos[5],0,2 ).":00:00" ) - $fechaInicial )%($rowsArticulos['perequ']*3600) != 0 ){
														$cambioFrecuencia = true;
													}
												}
											}
											/********************************************************************************************************/

											if( !$cambioFrecuencia ){

												//debo verificar que la ronda si pertenece al dia, el query no lo valida
												// if( strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ) <= strtotime( "$fecha ".gmdate( "H:00:00", $whora_par_actual*3600 ) ) ){
													consultarInformacionProducto( $rowsArticulos['kadart'], $informacionArticulos );

													// if( empty( $arts2[ $rowsArticulos['kadart'] ] ) ){	//Si es vacio, esta confirmado
													if( strtoupper( $rowsArticulos['Kadori'] ) == 'SF' || $rowsArticulos['Kadsus'] == 'on' || strtolower( $rowsArticulos['Kadcon'] ) == 'on' || !$esDeRonda ){	//Si es vacio, esta confirmado

														if( !esArticuloGenerico( $rowsArticulos['kadart'] ) ){	//Si es articulo generico se agrega, si no no
															//proceso la informacion
															$datosHabitaciones[ $i ]->procesarMedicamento( $whora_par_actual, $rowsArticulos['kadart'], $informacionArticulos[ strtoupper($rowsArticulos['kadart']) ]['nombreComercial'],$rowsArticulos['Kadcfr'], $rowsArticulos['Kadufr'], $rowsArticulos['Kadsus'], $rowsArticulos['Kadcnd'], true, strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ), $rowsArticulos['perequ'], $fechaBaseKardex, $rowsArticulos['Kaddma'], $rowsArticulos['Kaddia'], $rowsArticulos, $ccoCodigo );
														}
													}
													else{	//El articulo esta sin confirmar
														//proceso la informacion
														$datosHabitaciones[ $i ]->procesarMedicamento( $whora_par_actual, $rowsArticulos['kadart'], $informacionArticulos[ strtoupper($rowsArticulos['kadart']) ]['nombreComercial'],$rowsArticulos['Kadcfr'], $rowsArticulos['Kadufr'], $rowsArticulos['Kadsus'], $rowsArticulos['Kadcnd'], false, strtotime( $rowsArticulos['kadfin']." ".$rowsArticulos[5].":00:00" ), $rowsArticulos['perequ'], $fechaBaseKardex, $rowsArticulos['Kaddma'], $rowsArticulos['Kaddia'], $rowsArticulos, $ccoCodigo );
													}
												// }
											}

											/**************************************Fin de lo anterior ***************************************/
										}
									}

								}
							}
						}
					}
				}	//fin habitacion
			}	//Fin for pacientes encontrados
		}	//Fin habitaciones encontradas

		// echo "<pre>";
		// print_r($datosHabitaciones);
		// echo "</pre>";

		if( !empty( $datosHabitaciones ) ){
			pintarDatosFila( $datosHabitaciones );
		}
		else{
			encabezado( "SISTEMA DE GESTION DE ENFERMERIA", $actualiz ,"clinica" );
			echo "<center><b>No se encontraron datos</b></center>";
		}

		echo "<br><table align='center'>";

		echo "<tr>";

		//No se muestra el boton retornar cuando el centro costos de ayuda tiene datos ya que ellos no deben listar los centros de costos
		if(/*$ccoayuda == '' and */$wsp == ''){
			if( !empty( $ccoayuda ) )
				$wsp .= "&ccoayuda=".$ccoayudaori;

			echo "<td>";
			echo "<center><INPUT type='button' value='Retornar' onclick='volver(\"$wemp_pmla\", \"$waux\", \"$wsp\" )' style='width:100px'></center>";
			echo "</td>";
		}

		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();' style='width:100px'></center>";
		echo "</td>";
		echo "</tr>";

		echo "<INPUT type='hidden' name='mostrar' id='mostrar' value=''>";

		//
		if( !empty( $ccoayuda ) ){
			echo "<INPUT type='hidden' name='ccoayuda' id='ccoayuda' value='$ccoayuda'>";
		}

		if( !empty( $ccoayudaori ) ){
			echo "<INPUT type='hidden' name='ccoayudaori' id='ccoayudaori' value='$ccoayudaori'>";
		}

		echo "</table>";
	}

	echo "<input type='hidden' name='registro_inicial' value='" . $registro_inicial . "'>";
	echo "<input type='hidden' name='registro_final' value='" . $registro_final . "'>";
	echo "</form>";
}
?>
</body>