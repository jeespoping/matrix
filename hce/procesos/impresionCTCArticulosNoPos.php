<?php
include_once("conex.php");  
header("Content-Type: text/html;charset=ISO-8859-1"); 

include_once("root/comun.php");



$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

$wuser1=explode("-",$user);
$wusuario=trim($wuser1[1]);

$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );

$wfecha = date('Y-m-d');
$whora = date("H:i:s");


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

function consultarFormasFarmaceuticas( $conex, $wbasedato, $cod ){

	$val = "";

	$q = "SELECT
			Ffanom 
		  FROM 
			  ".$wbasedato."_000046
		  WHERE
			  ffacod = '$cod'
			";

	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);
	
	if( $rows = mysql_fetch_array( $res ) )
	{
		$val = $rows[ 'Ffanom' ];
	}

	return $val;
}

function consultarEmpresaConEquivalencia( $conex, $wemp_pmla, $wbasedato, $historia, $ingreso)
{
	$esEmpConEquivalentes = false;
	$empresaEquivalentes = consultarAliasPorAplicacion( $conex, $wemp_pmla, "empresaConEquivalenciaMedEInsumos" );
	
	$empEquivalentes = explode(",",$empresaEquivalentes);
	
	$sql = "SELECT Ingres  
			  FROM ".$wbasedato."_000016
			 WHERE Inghis='".$historia."' 
			   AND Inging='".$ingreso."'; ";
		 
	$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	
	if( $rows = mysql_fetch_array($res) ){
		
		for($i=0;$i<count($empEquivalentes);$i++)
		{
			if($empEquivalentes[$i] == $rows['Ingres'])
			{
				$esEmpConEquivalentes = true;
				break;
			}
		}			
	}
	
	return $esEmpConEquivalentes;
}

function consultarMedicamentoEquivalenteCTC( $wbasedato, $codMedicamento )
{
	global $conex;
	global $wemp_pmla;
	
	$cenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
	
	$reemplazo = array();
	
	$sql = "SELECT e.Artcod, e.Artcom, e.Artgen, e.Artreg, e.Artuni, Unides, e.Artfar, h.Deffra, i.Deffra as Appcnv
			  FROM ".$cenmez."_000001 a, ".$cenmez."_000002 b, ".$cenmez."_000003 c, ".$cenmez."_000009 d, ".$wbasedato."_000026 e, ".$cenmez."_000002 f, ".$wbasedato."_000027 g, ".$wbasedato."_000059 h, ".$wbasedato."_000059 i
			 WHERE tipcdo =  'on'
				AND tipest =  'on'
				AND tipcod = b.arttip
				AND b.artcod =  '".$codMedicamento."'
				AND pdepro = b.artcod
				AND pdeest =  'on'
				AND pdeins = appcod
				AND apppre = e.artcod
				AND appest =  'on'
				AND e.artest =  'on'
				AND f.artcod = appcod
				AND artpos = 'N'
				AND e.artuni = Unicod
				AND h.defart = b.artcod
				AND i.defart = e.artcod
				AND h.deffru = i.deffru
			ORDER BY Appcod";
			
	$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num == 1 ){
		
		if( $rows = mysql_fetch_array($res) ){
			$reemplazo['Areaeq'] = $rows['Artcod'];
			$reemplazo['Areceq'] = $rows['Deffra']/$rows['Appcnv'];
			$reemplazo['Artcom'] = $rows['Artcom'];
			$reemplazo['Artgen'] = $rows['Artgen'];
			$reemplazo['Artreg'] = $rows['Artreg'];
			$reemplazo['Artuni'] = $rows['Artuni'];
			$reemplazo['Unides'] = $rows['Unides'];
			$reemplazo['Artfar'] = $rows['Artfar'];
			
		}
	}
	else{
		// -- AND Areceq > '1'
		$ccoSF=ccoUnificadoSF();		   
		$sql = "SELECT Areaeq,Areceq,Artcom,Artgen,Artreg,Artuni,Unides, Artfar 
				  FROM ".$wbasedato."_000008, ".$wbasedato."_000026, ".$wbasedato."_000027
				 WHERE Arecco='{$ccoSF}' 
				   AND Areces='".$codMedicamento."'
				   AND Areaeq = Artcod
				   AND Artest = 'on'
				   AND Artpos = 'N'
				   AND Artuni = Unicod; ";
			 
		$res = mysql_query ($sql, $conex ) or die ( "Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error() );
		
		if( $rows = mysql_fetch_array($res) ){
			$reemplazo['Areaeq'] = $rows['Areaeq'];
			$reemplazo['Areceq'] = $rows['Areceq'];
			$reemplazo['Artcom'] = $rows['Artcom'];
			$reemplazo['Artgen'] = $rows['Artgen'];
			$reemplazo['Artreg'] = $rows['Artreg'];
			$reemplazo['Artuni'] = $rows['Artuni'];
			$reemplazo['Unides'] = $rows['Unides'];
			$reemplazo['Artfar'] = $rows['Artfar'];
			
		}
	}
	
	
	return $reemplazo;
}


function consultarCodigoCUM( $conex, $wemp_pmla, $articulo ){

	$val = "";

	$sql = "SELECT Cumcod 
			  FROM root_000064 
			 WHERE Cumemp='".$wemp_pmla."' 
			   AND Cumint='".$articulo."';";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row = mysql_fetch_assoc($res);
	$num = mysql_num_rows( $res );
	
	if($num > 0)
	{
		$val = $row['Cumcod'];
	}
	
	return $val;
}

function consultarParametrosCTC($historia,$ingreso,$articulo,$medico,$usuario,$wemp_pmla,$accion)
{
	global $conex;
	global $wbasedato;
	global $wusuario;
	global $wfecha;
	global $whora;
	
	$sqlParametros = "  SELECT Kadper,Kadcfr,Kadcma,Kaddma,kaddia,Kadori,Kadfin,Kadhin,Kadido,min(Kadfec) as Kadfec
						  FROM ".$wbasedato."_000054 
						 WHERE Kadhis = '".$historia."'
						   AND Kading = '".$ingreso."'
						   AND Kadart = '".$articulo."'
					  GROUP BY Kadhis,Kading,Kadart,kadido
					  
						 UNION
					  
						SELECT Kadper,Kadcfr,Kadcma,Kaddma,kaddia,Kadori,Kadfin,Kadhin,Kadido,min(Kadfec) as Kadfec
						  FROM ".$wbasedato."_000060
						 WHERE Kadhis = '".$historia."'
						   AND Kading = '".$ingreso."'
						   AND Kadart = '".$articulo."'
					  GROUP BY Kadhis,Kading,Kadart,kadido;";
		
	$resParametros = mysql_query( $sqlParametros, $conex ) or die( mysql_errno()." - Error en el query $sqlParametros - ".mysql_error() );
	$numParametros = mysql_num_rows( $resParametros );
	
	if($numParametros > 0)
	{
		$posParametro=0;
		
		while ($rowParametros = mysql_fetch_array($resParametros)) 
		{
			$inf['inf'][$posParametro][ 'frecuencia' ]=$rowParametros['Kadper'];
			$inf['inf'][$posParametro][ 'dosis' ]=$rowParametros['Kadcfr'];
			$inf['inf'][$posParametro][ 'canManejo' ]=$rowParametros['Kadcma'];
			$inf['inf'][$posParametro][ 'dosisMaxima' ]=$rowParametros['Kaddma'];
			$inf['inf'][$posParametro][ 'diasTto' ]=$rowParametros['kaddia'];
			$inf['inf'][$posParametro][ 'origen' ]=$rowParametros['Kadori'];
			$inf['inf'][$posParametro][ 'fin' ]=$rowParametros['Kadfin'];
			$inf['inf'][$posParametro][ 'hin' ]=$rowParametros['Kadhin'];
			
			$inf['inf'][$posParametro][ 'fechaKardex' ]=$rowParametros['Kadfec'];
			$inf['inf'][$posParametro][ 'ido' ]=$rowParametros['Kadido'];
			
			$posParametro++;
		}
	}
	
	if($accion=="R")
	{
		echo json_encode($inf);
	}
	else
	{
		return $inf;
	}
		
}

function grabarRegistroCTCAccion($historia,$ingreso,$articulo,$medico,$usuario,$wemp_pmla,$accion)
{
	global $conex;
	global $wbasedato;
	global $wusuario;
	global $wfecha;
	global $whora;
	
	echo $ido = "";
	if($accion=="N")
	{
		$parametros = consultarParametrosCTC($historia,$ingreso,$articulo,$medico,$usuario,$wemp_pmla,$accion);
		
		foreach($parametros as $key	=> $value)
		{
			foreach($value as $key2	=> $value2)
			{
				$sqlIdoExiste = "SELECT *
								  FROM ".$wbasedato."_000134 
								 WHERE Ctchis='".$historia."' 
								   AND Ctcing='".$ingreso."' 
								   AND Ctcart='".$articulo."'
								   AND Ctcest='on'
								   AND FIND_IN_SET( ".$value2['ido'].",Ctcido ) > 0; ";		

				$resIdoExiste = mysql_query($sqlIdoExiste, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sqlIdoExiste . " - " . mysql_error());		   
				$numIdoExiste = mysql_num_rows( $resIdoExiste );
				
				if( $numIdoExiste == 0 )
				{
					$ido .=  $value2['ido'].",";
				}
			}
			
		}
		
		$ido = substr($ido, 0, -1);
	}
	
	$sql = "SELECT *
			  FROM ".$wbasedato."_000134 
			 WHERE Ctchis='".$historia."' 
			   AND Ctcing='".$ingreso."' 
			   AND Ctcart='".$articulo."'
			   AND Ctcest='on' 
			   AND Ctcfkx='0000-00-00'
			   AND Ctcido='".$ido."'; 
			";

	$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 )
	{
		$sql = "UPDATE ".$wbasedato."_000134 
				   SET Ctcacc = '".$accion."', 
						Ctcacu = '".$usuario."',
						Ctcacf = '".$wfecha."',
						Ctcach = '".$whora."'
				 WHERE Ctchis='".$historia."' 
				   AND Ctcing='".$ingreso."' 
				   AND Ctcart='".$articulo."'
				   AND Ctcest='on' 
				   AND Ctcfkx='0000-00-00'
				   AND Ctcido='".$ido."';";		
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$filas_actualizadas = mysql_affected_rows();
		
		if( $filas_actualizadas > 0 ){
			$Mensaje= "Se ha actualizado correctamente la accion para el CTC";
		}
		else{
			$Mensaje= "Error actualizando la accion para el CTC: ".mysql_errno();
			echo "<script> alert(".$Mensaje."); </script>";
		}
	}
	else
	{
		$realizado="off";
				
		$queryInsert = " INSERT INTO ".$wbasedato."_000134 
								(Medico,Fecha_data,Hora_data,Ctchis,Ctcing,Ctcart,Ctcfkx,Ctcido,Ctcest,Ctcmed,Ctcacc,Ctcacr,Ctcacu,Ctcacf,Ctcach,Seguridad) 
							 VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$historia."','".$ingreso."','".$articulo."','0000-00-00','".$ido."','on','".$medico."','".$accion."','".$realizado."','".$usuario."','".$wfecha."','".$whora."','C-".$wbasedato."');";
							 
		$resultado2 = mysql_query($queryInsert,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsert." - ".mysql_error());

		if(mysql_affected_rows()==1)
		{
			$Mensaje= "Se ha registrado correctamente la accion para el CTC";
		}
		else
		{
			$Mensaje= "Error registrando la accion para el CTC: ".mysql_errno();
			echo "<script> alert(".$Mensaje."); </script>";
		}
		
		
	}
	
	echo $Mensaje;
	// echo "<script>timer = setInterval('recargar()', 1000);</script>";
	
}

function esArticuloLactario( $conex, $wbasedato, $art ){

	$val = false;
	
	$sql = "SELECT *
			  FROM {$wbasedato}_000026, movhos_000011
			 WHERE artcod = '$art' 
			   AND FIND_IN_SET( SUBSTRING(artgru, 1 , 3 ), ccogka ) > 0
			   AND artest = 'on'
			   AND ccolac = 'on'
			   AND ccoest = 'on'";			
	$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}
	else{
		$sql = "SELECT *
				  FROM {$wbasedato}_000068
				 WHERE arkcod = '$art' 
				   AND arktip = 'LC'
				   AND arkest = 'on'
				";
			
		$res = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			$val = true;
		}
	}
	
	return $val;
}



function obtenerMensaje($clave){
	$texto = 'No encontrado';

	switch ($clave) {
		case 'MSJ_KARDEX_CREADO':
			$texto = "Kardex creado";
			break;
		case 'MSJ_KARDEX_ACTUALIZADO':
			$texto = "Kardex actualizado";
			break;
		case 'MSJ_ARTICULO_CREADO':
			$texto = "Articulo creado";
			break;
		case 'MSJ_ARTICULO_ACTUALIZADO':
			$texto = "Articulo actualizado";
			break;
		case 'MSJ_ARTICULO_ELIMINADO':
			$texto = "Articulo eliminado";
			break;
		case 'MSJ_ARTICULO_NO_CREADO':
			$texto = "No se pudo crear articulo";
			break;
		case 'MSJ_EXAMEN_CREADO':
			$texto = "Examen de laboratorio creado";
			break;
		case 'MSJ_EXAMEN_ACTUALIZADO':
			$texto = "Examen de laboratorio actualizado";
			break;
		case 'MSJ_EXAMEN_ELIMINADO':
			$texto = "Examen de laboratorio eliminado";
			break;
		case 'MSJ_EXAMEN_NO_CREADO':
			$texto = "No se pudo crear el examen";
			break;
		case 'MSJ_INFUSION_CREADA':
			$texto = "Líquido endovenoso creado";
			break;
		case 'MSJ_INFUSION_ACTUALIZADA':
			$texto = "Liquido endovenoso actualizado";
			break;
		case 'MSJ_INFUSION_ELIMINADA':
			$texto = "Liquido endovenoso eliminado";
			break;
		case 'MSJ_INFUSION_NO_CREADA':
			$texto = "No se pudo crear el liquido endovenoso";
			break;
		case 'MSJ_MEDICO_ASOCIADO':
			$texto = "Medico asociado";
			break;
		case 'MSJ_MEDICO_RETIRADO':
			$texto = "Medico retirado";
			break;
		case 'MSJ_DIETA_ASOCIADA':
			$texto = "Dieta asociada";
			break;
		case 'MSJ_DIETA_RETIRADA':
			$texto = "Dieta retirada";
			break;
		case 'MSJ_ARCHIVO_CARGADO':
			$texto = "Archivo cargado";
			break;
		case 'MSJ_SUSPENDER_MEDICAMENTO':
			$texto = "Articulo suspendido";
			break;
		case 'MSJ_ACTIVAR_MEDICAMENTO':
			$texto = "Articulo activado";
			break;
		case 'MSJ_SUPENSION_NO_MODIFICADA':
			$texto = "Estado de suspension no modificado";
			break;
		case 'MSJ_ARTICULO_MODIFICADO_DESDE_PERFIL':
			$texto = "Articulo modificado desde el perfil farmacologico";
			break;
		case 'MSJ_ARTICULO_NO_MODIFICADO_DESDE_PERFIL':
			$texto = "Articulo no pudo ser modificado desde el perfil farmacologico";
			break;
		case 'MSJ_ARTICULO_REEMPLAZADO_DESDE_PERFIL':
			$texto = "Articulo ha sido reemplazado desde el perfil farmacologico";
			break;
		case 'MSJ_KARDEX_DESAPROBADO':
			$texto = "Kardex no aprobado por parte del regente";
			break;
		case 'MSJ_KARDEX_APROBADO':
			$texto = "Kardex aprobado por parte del regente";
			break;
		case 'MSJ_ALERGIA_MODIFICADA':
			$texto = "Alergia modificada";
			break;
		case 'MSJ_ESQUEMA_GRABADO':
			$texto = "Esquema de insulina grabado";
			break;
		case 'MSJ_ESQUEMA_ELIMINADO':
			$texto = "Esquema de insulina eliminado";
			break;
		case 'MSJ_CANTIDAD_CTC_MODIFICADA':
			$texto = "Cantidad CTC modificada";
			break;
		case 'MSJ_CANTIDAD_CTC_CREADA':
			$texto = "Cantidad CTC creada";
			break;
		case 'MSJ_CANTIDAD_CTC_NO_ALTERADA':
			$texto = "No se pudo crear cantidad CTC";
			break;
		case 'MSJ_ARTICULO_APROBADO':
			$texto = "Articulo aprobado";
			break;
		case 'MSJ_ARTICULO_NO_APROBADO':
			$texto = "No se pudo aprobar el articulo";
			break;
		case 'MSJ_SUSPENDIDO_AUTOMATICAMENTE_PERFIL':
			$texto = "Suspendido automaticamente por reemplazo desde el perfil";
			break;
		case 'MSJ_KARDEX_RECUPERADO':
			$texto = "Kardex recuperado";
			break;
		case 'MSJ_ARTICULO_DESAPROBADO':
			$texto = "Articulo desaprobado";
			break;
		case 'MSJ_CANTIDAD_CTC_MODIFICADA_DESDE_PROGRAMA':
			$texto = "Cantidad CTC modificada desde el programa de impresionCTCArticulosNoPos";
			break;
		case 'MSJ_MODIFICACION_TIEMPO_TTO':
			$texto = "Tiempo de tratamiento modificado para el CTC desde el programa de impresionCTCArticulosNoPos";
			break;
		default:
			$texto = "Mensaje no especificado";
		break;
	}
	return $texto;
}


function act_tto($wemp_pmla,$historia,$ingreso,$tiempo_tto,$cod_articulo,$presentacion,$id_registro, $usuario){


	global $conex;
	global $wbasedato;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

	$q_apr =     " UPDATE ".$wbasedato."_000134"
				."    SET Ctcttn = '".$tiempo_tto."'"
			    ."  WHERE id = '".$id_registro."'";
	$res_apr = mysql_query($q_apr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_apr." - ".mysql_error());
	$actualizado = mysql_affected_rows();

	if($actualizado){

	$datamensaje['mensaje'] = "Ha sido actualizado el tiempo de tratamiento CTC";
	
	$mensajeAuditoria = obtenerMensaje('MSJ_MODIFICACION_TIEMPO_TTO');
	
	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	//$auditoria->fechaKardex = $fechaKardex;
	$auditoria->descripcion = "Modificacion del tiempo de tratamiento ordenado para el registro numero $id_registro en la tabla movhos_000134, ya que el medico esta registrando un valor incorrecto";
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($auditoria);

	}else{
	
	$datamensaje['error'] = 1;
	$datamensaje['mensaje'] = "No ha sido actualizado la cantidad total del CTC";

	}

	echo json_encode($datamensaje);
    return;

}

function act_cant_ordenada($wemp_pmla,$historia,$ingreso,$cantidad_ord,$cod_articulo,$presentacion,$id_registro,$usuario){


	global $conex;
	global $wbasedato;
	
	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

	$q_apr =     " UPDATE ".$wbasedato."_000134"
				."    SET Ctccan = '".$cantidad_ord."'"
			    ."  WHERE id = '".$id_registro."'";
	$res_apr = mysql_query($q_apr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_apr." - ".mysql_error());
	$actualizado = mysql_affected_rows();

	if($actualizado){

	$datamensaje['mensaje'] = "Ha sido actualizado la cantidad total del CTC";
	
	$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_MODIFICADA_DESDE_PROGRAMA');
	
	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	//$auditoria->fechaKardex = $fechaKardex;
	$auditoria->descripcion = "Modificacion de la cantidad ordenada para el registro numero $id_registro en la tabla movhos_000134, ya que el medico esta registrando un valor incorrecto";
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $usuario;

	registrarAuditoriaKardex($auditoria);

	}else{
	
	$datamensaje['error'] = 1;
	$datamensaje['mensaje'] = "No ha sido actualizado la cantidad total del CTC";

	}
	
	echo json_encode($datamensaje);
    return;

}

//Consulta cantidades en CTC para el paciente y articulo.
function consultarCantidadesCTC($historia,$ingreso,$codigoArticulo){

	global $conex;
	global $wbasedato;

	$q = " SELECT fecha_data, ctccau, ctccus, ctcuca "                //cau: Cantidad Autorizada, cus: Cantidad Usuada, uca: Unidad de medida
	   ."   FROM ".$wbasedato."_000095 "
	   ."  WHERE ctchis = '".$historia."'"                            //Historia
	   ."    AND ctcing = '".$ingreso."'"                             //Ingreso
	   ."    AND ctcart = '".$codigoArticulo."'";                     //Código Articulo
   $resctc = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $rowctc = mysql_fetch_array($resctc);

	return $rowctc;

}


function registrarAuditoriaKardex($auditoria){

	global $conex;
	global $wbasedato;
	global $wusuario;

	$q = "INSERT INTO ".$wbasedato."_000055
				(Medico, Fecha_data, Hora_data, Kauhis, Kauing, Kaudes, Kaufec, Kaumen, Kauido, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".date("H:i:s")."','$auditoria->historia','$auditoria->ingreso','$auditoria->descripcion','".date("Y-m-d")."','$auditoria->mensaje','$auditoria->idOriginal','A-$auditoria->seguridad')";

	$res = mysql_query($q, $conex); // or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
}

//Consulta la cantidad aplicada para un articulo.
function consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$codigoArticulo){

	global $conex;
	global $wbasedato;

	$cantidad = "";

	$q = "SELECT IFNULL(SUM(Aplcan),0) canAcumulada
		    FROM {$wbasedato}_000015
		   WHERE Aplhis 		= '$historia'
			 AND Apling  = '$ingreso'
			 AND Aplart  = '$codigoArticulo'
			 AND Aplest  = 'on'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q - " . mysql_error());
	$num = mysql_num_rows($res);

	while($info = mysql_fetch_array($res)){
		$cantidad = $info['canAcumulada'];
	}

	return $cantidad;
}

//Actualiza la cantidad aplicada para un paciente, para un medicamento.
function actualizacionCantidadAplicada($conex,$wbasedato,$historia,$ingreso,$codigoArticulo){

	global $conex;
	global $wbasedato;

	$cantidad = "";

	$q = "SELECT IFNULL(SUM(Aplcan),0) canAcumulada
		    FROM {$wbasedato}_000015
		   WHERE Aplhis 		= '$historia'
			 AND Apling  = '$ingreso'
			 AND Aplart  = '$codigoArticulo'
			 AND Aplest  = 'on'";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: $q - " . mysql_error());
	$num = mysql_num_rows($res);
	$info = mysql_fetch_array($res);
	$cantidad = $info['canAcumulada'];

	if($cantidad > 0){

	$q2 = "UPDATE ".$wbasedato."_000095
			  SET Ctccus = '$cantidadUsadaCtc'
			WHERE Ctchis = '$historia'
			  AND Ctcing = '$ingreso'
			  AND Ctcart = '$codigoArticulo'";
	$res2 = mysql_query($q2, $conex) or die ("Error: ".mysql_errno()." - en el query: $q2 - ".mysql_error());

	}

}

function actualizarctc($wemp_pmla,$historia,$ingreso,$autorizadoCtc, $cod_articulo, $presentacion){

	global $conex;
	global $wbasedato;
	global $wusuario;

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

	$cantidadUsadaCtc = consultarCantidadAcumuladaDispensada($conex,$wbasedato,$historia,$ingreso,$cod_articulo);

	$q4 = "SELECT *
			 FROM ".$wbasedato."_000095
			WHERE Ctchis = '$historia'
			  AND Ctcing = '$ingreso'
			  AND Ctcart = '$cod_articulo';";
	$res4 = mysql_query($q4, $conex) or die ("Error: ".mysql_errno()." - en el query: $q4 - ".mysql_error());
	$num4 = mysql_num_rows($res4);

	if($num4 > 0){
		$q2 = "UPDATE ".$wbasedato."_000095
		          SET Ctccau = '$autorizadoCtc', Ctccus = '$cantidadUsadaCtc'
			    WHERE Ctchis = '$historia'
				  AND Ctcing = '$ingreso'
				  AND Ctcart = '$cod_articulo'";
		$res2 = mysql_query($q2, $conex) or die ("Error: ".mysql_errno()." - en el query: $q2 - ".mysql_error());
		$estado2 = "2";
		$datamensaje['mensaje'] = "La cantidad autoriza para el CTC ha sido actualizada";

	} else {
		$q3 = "INSERT INTO ".$wbasedato."_000095(Medico,Fecha_data,Hora_data,Ctchis,Ctcing,Ctcart,Ctccau,Ctccus,Ctcuca,Seguridad)
		                                  VALUES('movhos','".date("Y-m-d")."','".date("H:i:s")."','$historia','$ingreso','$cod_articulo','$autorizadoCtc','$cantidadUsadaCtc','$presentacion','A-$wusuario')";
		$res3 = mysql_query($q3, $conex) or die ("Error: ".mysql_errno()." - en el query: $q3 - ".mysql_error());
		$estado2 = "1";
		$datamensaje['mensaje'] = "La cantidad autoriza para el CTC ha sido creada";
	}

	switch ($estado2){
		case "1":
			$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_CREADA');
			break;
		case "2":
			$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_MODIFICADA');
			break;
		default:
			$mensajeAuditoria = obtenerMensaje('MSJ_CANTIDAD_CTC_NO_ALTERADA');
			break;
	}

	//Registro de auditoria
	$auditoria = new AuditoriaDTO();

	$auditoria->historia = $historia;
	$auditoria->ingreso = $ingreso;
	//$auditoria->fechaKardex = $fechaKardex;
	$auditoria->descripcion = "Cantidad registrada desde impresionCTCArticulosNoPos para el articulo $codArticulo";
	$auditoria->mensaje = $mensajeAuditoria;
	$auditoria->seguridad = $wusuario;

	registrarAuditoriaKardex($auditoria);

	echo json_encode($datamensaje);
    return;


}


//Aprobacion de ctc
function aprobar_ctc_articulos($wemp_pmla, $wbasedato, $wid_ctc, $westado){

	global $conex;
	global $wbasedato;
	global $wusuario;
	global $wfecha;
	global $whora;

	$datamensaje = array('mensaje'=>'', 'error'=>0, 'actualizado'=>'');

	$q_apr =    " UPDATE ".$wbasedato."_000134"
				."    SET Ctcapr = '".$westado."', Ctcuap = '".$wusuario."', Ctcfap = '".$wfecha."', Ctchap = '".$whora."'"
			    ."  WHERE id = '".$wid_ctc."'";
	$res_apr = mysql_query($q_apr,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_apr." - ".mysql_error());
	$actualizado = mysql_affected_rows();

	if($actualizado){

	$datamensaje['mensaje'] = "Ha sido aprobado el CTC";

	}else{
	
	$datamensaje['error'] = 1;
	$datamensaje['mensaje'] = "No ha sido aprobado el CTC";

	}

	echo json_encode($datamensaje);
    return;

}


if(isset($consultaAjax))
	{

	switch($consultaAjax){

		case 'aprobar_ctc_articulos':
					{
					echo aprobar_ctc_articulos($wemp_pmla, $wbasedato, $wid_ctc, $westado);
					}
		break;

		case 'actualizarctc':
					{
					echo  actualizarctc($wemp_pmla,$historia,$ingreso,$autorizadoCtc, $cod_articulo, $presentacion);
					}
		break;
		
		case 'act_cant_ordenada':
					{
					echo  act_cant_ordenada($wemp_pmla,$historia,$ingreso,$cantidad_ord,$cod_articulo,$presentacion,$id_registro, $usuario);
					}
		break;
		
		case 'act_tto':
					{
					echo  act_tto($wemp_pmla,$historia,$ingreso,$tiempo_tto,$cod_articulo,$presentacion,$id_registro, $usuario);
					}
		break;
		
		case 'MarcarCTCAccion':
					{
					echo  grabarRegistroCTCAccion($historia,$ingreso,$articulo,$medico,$usuario,$wemp_pmla,$accion);
					}
		break;
		
		case 'consultarParametrosCTC':
					{
					echo  consultarParametrosCTC($historia,$ingreso,$articulo,$medico,$usuario,$wemp_pmla,$accion);
					}
		break;

		default: break;

		}
	return;
	}
?>
<html>
<head>
<title>IMPRESION ARTICULOS NO POS</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js"     type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
 <script src="../../../include/root/print.js" type="text/javascript"></script>
 
 
 
 
 <script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<!-- <script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script> -->	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>
<!-- <script type="text/javascript" src="../../../include/root/ui.datepicker.js"></script>-->
<script type="text/javascript" src="../../../include/root/burbuja.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>

<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
 
  <script src="../../hce/procesos/generarCTCOrdenes.js?v=<?=md5_file('generarCTCOrdenes.js');?>" type="text/javascript"></script>
 <script src="../../hce/procesos/ordenes.js?v=<?=md5_file('ordenes.js');?>" type="text/javascript"></script>
 
 
 

 

<style type="text/css">

    .medSuspendido{background:#FFB5B5;font-size: 10pt;}
	.detalles{
		font-family: verdana;
		font-size: 7pt;
		background-color: #bfbfbf;
		border-radius:3px;
		color: #0033FF;
		font-weight: bold;
		text-decoration: underline;
		cursor:pointer;
	}
	
	.presentacionMipres
	{
		font-family: verdana;
		font-weight: bold;
		color:#0033ff;
	}
      	
</style>
  

<script>

timer = setInterval('recargar()', 120000);

var tiempoRecarga;


document.oncontextmenu = function(){return false}


window.onunload=function(){
    window.name=self.pageYOffset || (document.documentElement.scrollTop+document.body.scrollTop);
    }

// $(document).ready(function () {
	// $("body").keypress(function (e) {
		
		// //Enter
		// if (e.which == 13) {
		 // return false;
		// }
		
		// //F10
		// if (e.which == 0) {
		 // return false;
		// }
		
		// //Tecla p
		// if (e.which == 112) {
		 // return false;
		// }
		
		// //Tecla s
		// if (e.which == 115) {
		 // return false;
		// }
		
		// //Tecla a
		// if (e.which == 97) {
		 // return false;
		// }
		
		// //Tecla c
		// if (e.which == 99) {
		 // return false;
		// }
	// });
// });

function iniciarTooltip(tooltip)
{
	//Tooltip
	var cadenaTooltip = $("#"+tooltip).val();
	
	cadenaTooltip = cadenaTooltip.split("|");
	
	for(var i = 0; i < cadenaTooltip.length-1;i++)
	{
		$( "#"+cadenaTooltip[i] ).tooltip();
	}
	
}

function abrirModalMipres(historia,ingreso,tipoDocumento,documento,fecha,codPrescMipres)
{
	$.post("CTCmipres.php",
	{
		consultaAjax 	: '',
		accion			: 'pintarPrescripcionMipres',
		wemp_pmla:				$('#wemp_pmla').val(),
		historia: 				historia,
		ingreso: 				ingreso,
		tipoDocumento: 			tipoDocumento,
		documento: 				documento,
		fechaMipres: 			fecha,
		general: 				"off",
		codPrescMipres: 		codPrescMipres,
		reporte:				"ctcMedicamentos"
	}
	, function(data) {
		
		clearInterval(timer);
		
		$( "#dvAuxModalMipres" ).html( data );
		
				
		var canWidth = $(window).width()*0.8;
		if( $( "#dvAuxModalMipres" ).width()-50 < canWidth )
			canWidth = $( "#dvAuxModalMipres" ).width();

		var canHeight = $(window).height()*0.8;;
		if( $( "#dvAuxModalMipres" ).height()-50 < canHeight )
			canHeight = $( "#dvAuxModalMipres" ).height();

	
		$.blockUI({ message: $('#modalMipres'),
		css: {
			overflow: 'auto',
			cursor	: 'auto',
			width	: "95%",
			height	: "80%",
			left	: "2.5%",
			top		: '100px',
		} });
		
		
		
		iniciarTooltip("tooltipEstadoPrescripcion");
		iniciarTooltip("tooltipJMMed");
		iniciarTooltip("tooltipJMNut");
		iniciarTooltip("tooltipPrincipiosActivos");
		iniciarTooltip("tooltipMedicamentosUtilizados");
		iniciarTooltip("tooltipMedicamentosDescartados");
		iniciarTooltip("tooltipMedicamentosDetalles");
		iniciarTooltip("tooltipProdNutricionalesUtilizados");
		iniciarTooltip("tooltipProdNutricionalesDescartados");
		iniciarTooltip("tooltipProdNutricionalesDetalles");
		
		
		
	},'json');
	
}


function cerrarModal()
{
	$.unblockUI();
	timer = setInterval('recargar()', 120000);
}
	
	
function verPrescripcionPorPacienteFec(historia,ingreso,tipoDocumento,documento,fecha)
{
	$.blockUI({ message: $('#msjEspere'),
		css: {
			top:  '35%', 
			left: '40%', 
			width: '30%',
			height: '20%',
			overflow: 'auto',
			cursor: 'auto'
		}
	});

	$.post("CTCmipres.php",
	{
		consultaAjax 	: '',
		accion			: 'consultarPrescripcionPacFec',
		historia		: historia,
		ingreso			: ingreso,
		tipoDocumento	: tipoDocumento,
		documento		: documento,
		fechaMipres		: fecha,
		general			: "off",
		wemp_pmla		: $('#wemp_pmla').val(),
		hora			: "",
		origen			: "reporteCTC"
	}
	, function(data) {
		
		$.unblockUI();
		
		if(data.length>0)
		{
			arrayCodPrescripcionMipres = data;
			
			abrirModalMipres(historia,ingreso,tipoDocumento,documento,fecha,arrayCodPrescripcionMipres)
		}
		else
		{
			jAlert("No se encontraron prescripciones en Mipres","ALERTA");
		}
		

	},'json');
	
}

function consultarMipresFecha(){
	
	fechaIniMipres = $("#wfecha_inicialMipres").val();
	fechaFinMipres = $("#wfecha_finalMipres").val();
	
	if(fechaIniMipres > fechaFinMipres)
	{
		alert("La fecha final debe ser mayor a la fecha inicial");
	}
	else
	{
		clearInterval(timer);
		
		$("#ctcarticulos").submit();
		timer = setInterval('recargar()', 120000);
		
	}
}

function llenarCTC(historia,ingreso,codArticulo,medico,usuario,wemp_pmla,accion,protocolo,id,wbasedatohce){
	clearInterval(timer);
	
	if(accion=="R")
	{
		Marcar = confirm( "¿Confirma que realizará el CTC?" );
	}
	
	
	if(Marcar==true)
	{
		$.post("impresionCTCArticulosNoPos.php",
		{
			consultaAjax:   		'consultarParametrosCTC',
			historia:         		historia,
			ingreso:      	    	ingreso,
			articulo: 	 			codArticulo,
			medico: 	   			medico,
			usuario:    			usuario,
			wemp_pmla:    			wemp_pmla,
			accion:    				accion
			
			
			
		}, function(respuesta){
			
			inCodArtsCTC = respuesta;
			
			//Ahora creo una url con los parametros encontrados
			//Para ello recorro todo el objeto que requiero
			var parametros = "";
			
			if( inCodArtsCTC.inf.length > 0 ){
				
				for( var i = 0; i < inCodArtsCTC.inf.length; i++ ){
					
					for( var x in inCodArtsCTC.inf[i] ){
						parametros += "&" + x + "[" + i + "]=" + inCodArtsCTC.inf[ i ][ x ];
					}
				}
			}
			
			idx=protocolo+id;
			$.post("generarCTCArticulos.php?"+parametros,
			{
				wemp_pmla:    			wemp_pmla,
				historia:         		historia,
				ingreso:      	    	ingreso,
				codArticulo: 	 		codArticulo,
				idx: 	 				idx,
				protocolo: 	 			protocolo,
				id: 		 			id,
				cadenaArtSinCTC: 	 	'',
				medico: 	   			medico,
				usuario:    			usuario,
				wbasedatohce:    		wbasedatohce,
				
				accion:    				accion
				
				
				
			}, function(respuesta){
				
				if( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) ){
						document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ).parentNode.removeChild( document.getElementById( "dv"+codArticulo+"-"+idx+"Mostrar" ) );
					}
				
				// Creo el div que contendrá todos los ctc de procedimientos
				if( !document.getElementById('ctcArticulos') ){
					var divAux = document.createElement( "div" );
					
					divAux.innerHTML = "<div id='ctcArticulos' style='display:none'>"
									 + "<INPUT TYPE='hidden' name='hiArtsNoPos' id='hiArtsNoPos' value=''>"
									 + "</div>";
					
					document.forms[0].appendChild(divAux.firstChild);
				}
				
				document.getElementById('hiArtsNoPos').value += ',' + codArticulo + '-' +idx;
				
				if( !document.getElementById('ctcArtsTemp') ){
				
					var divAux = document.createElement( "div" );
					
					divAux.style.display = 'none';
					divAux.style.width = '80%';
					divAux.id = 'ctcArtsTemp';
				}
				else{
					var divAux = document.getElementById('ctcArtsTemp');
				}
				
				divAux.innerHTML = respuesta;
			
				//Busco el campo Tiempo de tratamiento para agregar el objeto con el que se calcula la cantidad
				$( "[name=tiempoTratamientoNoPos]", divAux )[0].objArts = inCodArtsCTC;
				$( "input[value='Salir sin guardar']", divAux )[0].objArts = inCodArtsCTC;
				
				document.forms[0].appendChild(divAux);
			
				$.blockUI({ message: $('#ctcArtsTemp'),
					css: {
						top:  '5%', 
						left: '10%', 
						width: '80%',
						height: '90%',
						overflow: 'auto',
						cursor: 'auto'
					}
				});
			});
		},
		"json"
		);
	}
}

function marcarAccion(historia,ingreso,articulo,medico,usuario,wemp_pmla,accion){
	
	if(accion=="N")
	{
		Marcar = confirm( "¿Confirma que no se llenará el CTC?" );
	}
	else if(accion=="M")
	{
		Marcar = confirm( "¿Confirma que el medico llenará el CTC?" );
	}
	
	
	
	if(Marcar==true)
	{
		$.post("impresionCTCArticulosNoPos.php",
		{
			consultaAjax:   		'MarcarCTCAccion',
			historia:         		historia,
			ingreso:      	    	ingreso,
			articulo: 	 			articulo,
			medico: 	   			medico,
			usuario:    			usuario,
			wemp_pmla:    			wemp_pmla,
			accion:    				accion
			
			
			
		}, function(respuesta){
			timer = setInterval('recargar()', 4000);
		});
	}
	
}

function consultarSinCTCFecha(){
	
	fechaIniSinCTC = $("#wfecha_inicialSinCTC").val();
	fechaFinSinCTC = $("#wfecha_finalSinCTC").val();
	
	if(fechaIniSinCTC > fechaFinSinCTC)
	{
		alert("La fecha final debe ser mayor a la fecha inicial");
	}
	else
	{
		$("#fecSinCTCInicial").val(fechaIniSinCTC);
		$("#fecSinCTCFinal").val(fechaFinSinCTC);
		$("#ctcarticulos").submit();
	}
}


function boton_imp(){

	$(".printer").bind("click",function()
		{
		
			$(".areaimprimirCTC").printArea({			
				
				popClose: false,
				popTitle : 'CTCArticulosNoPos',
				popHt    : 500,
				popWd    : 1200,
				popX     : 200,
				popY     : 200,
				
				});
				
			
		});

}

function cerrarVentana(){

	window.close();

}
	
function consultar_fecha(tipo){
	
	if($("#wfecha_consulta").val() != ""){
		$("#tipo_consulta").val(tipo);
		$("#ctcarticulos").submit();
		
	}else{
		
		if(tipo == 'fgen'){
		
			var texto = 'fecha de generación';
		}else{
		
			var texto = 'fecha de impresión';
		}
		alert("Debe seleccionar una "+texto+".");
	}
	
	
	
}

function ver_ctc_imp_hoy(hoy){

	$("#wfecha_consulta").val(hoy);
	$("#btn_consultar_fecha").click();
	
	
}


function act_tto(wemp_pmla, cod_articulo, historia, ingreso, presentacion, id, id_registro, cantidad_autorizada, usuario, prefijo){

	var tiempo_tto = $("#"+prefijo+id).val();
	console.log($("#"+prefijo+id));
	var reg = new RegExp("^[1-9]+[0-9]*");

	if(!reg.test(tiempo_tto))
		{
			mensaje = 'La cantidad debe ser un número diferente de cero. \n\r';
			alert(mensaje);
			$("#input_cant_ord"+id).val('');
			return false;
    	}

	$.post("impresionCTCArticulosNoPos.php",
				{
					consultaAjax:		'act_tto',
					wemp_pmla:			wemp_pmla,
					tiempo_tto:			tiempo_tto,
					historia:			historia,
					ingreso:			ingreso,
					cod_articulo:		cod_articulo,
					presentacion:		presentacion,
					id_registro:		id_registro,
					usuario:			usuario

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert("Error: "+data_json);
						return;
					}
					else
					{
																

					}

			},
			"json"
		);

	}
	
function act_cant_ordenada(wemp_pmla, cod_articulo, historia, ingreso, presentacion, id, id_registro, cantidad_autorizada, usuario, prefijo, pref_cant_total){

	var cantidad_ord = $("#"+prefijo+id).val();
	
	var reg = new RegExp("^[1-9]+[0-9]*");

	if(!reg.test(cantidad_ord))
		{
			mensaje = 'La cantidad debe ser un número diferente de cero. \n\r';
			alert(mensaje);
			$("#input_cant_ord"+id).val('');
			return false;
    	}

	//Oculto el input y luego muestro la imagen tipo gif que simula el cargar.
	// $('#cant_aut_'+id).hide();

	// //Imagen que muestra el dato cargando
	// $('#div_carga_'+id).html( "<img width='auto' height='auto' border='0' src='../../images/medical/cargando.gif'>" );

	$.post("impresionCTCArticulosNoPos.php",
				{
					consultaAjax:		'act_cant_ordenada',
					wemp_pmla:			wemp_pmla,
					cantidad_ord:		cantidad_ord,
					historia:			historia,
					ingreso:			ingreso,
					cod_articulo:		cod_articulo,
					presentacion:		presentacion,
					id_registro:		id_registro,
					usuario:			usuario

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert("Error: "+data_json);
						return;
					}
					else
					{
					
						 $('#'+pref_cant_total+id).html(cantidad_ord);
						// $('#div_carga_'+id).html('');
						// $('#cant_aut_'+id).show();							

					}

			},
			"json"
		);

	}
	
function actualizarctc(wemp_pmla, cod_articulo, historia, ingreso, presentacion, id, registro, cantidad_autorizada){

	var cantidad = $("#cant_aut_"+id).val();
	var cantidad_ordenada = $("#cant_ord_"+id).val();
	var reg = new RegExp("^[1-9]+[0-9]*");

	if(!reg.test(cantidad))
		{
			mensaje = 'La cantidad debe ser un número diferente de cero. \n\r';
			alert(mensaje);
			$("#cant_aut_"+id).val('');
			return false;
    	}
	
	if(cantidad > cantidad_ordenada){
		
		alert("La cantidad autorizada es mayor a la cantidad ordenada.");
		$("#cant_aut_"+id).val(cantidad_autorizada);
		return false;
	}

	//Oculto el input y luego muestro la imagen tipo gif que simula el cargar.
	$('#cant_aut_'+id).hide();

	//Imagen que muestra el dato cargando
	$('#div_carga_'+id).html( "<img width='auto' height='auto' border='0' src='../../images/medical/cargando.gif'>" );

	$.post("impresionCTCArticulosNoPos.php",
				{
					consultaAjax:		'actualizarctc',
					wemp_pmla:			wemp_pmla,
					autorizadoCtc:		cantidad,
					historia:			historia,
					ingreso:			ingreso,
					cod_articulo:		cod_articulo,
					presentacion:		presentacion

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert("Error: "+data_json);
						return;
					}
					else
					{
					
						$('#imp_'+registro).show();
						$('#div_carga_'+id).html('');
						$('#cant_aut_'+id).show();							

					}

			},
			"json"
		);

	}


function aprobar_ctc_articulos(wemp_pmla, id, id_input_aprobado, cantidad_ordenada, cod_art, his, ing, presentacion, registro){
	
	 if($("#check_aprobar_"+id).is(':checked')) {
           var estado = 'on';
        } else {
            var estado = 'off';
        }
		
	var cantidad = $("#cant_aut_"+id_input_aprobado).val();
	
	// if(cantidad > 0){
		
		// $("#check_aprobar_"+id).attr('checked', true);
		// alert('No es posible inactivar la aprobacion porque ya hay cantidad autorizada para este medicamento.');		
		// return false;
		
		// }
	
	$.post("impresionCTCArticulosNoPos.php",
				{
					consultaAjax:		'aprobar_ctc_articulos',
					wemp_pmla:			wemp_pmla,
					wid_ctc:			id,
					westado:			estado

				}
				,function(data_json) {

					if (data_json.error == 1)
					{						
						return;
					}
					else
					{
						if(estado == 'on'){
						//alert(data_json.mensaje);
						$("#input_aut_"+id).show(); //Muestra el cajon para ingresar la cantidad autorizada.
						$('#td_imp_previa_'+id).css( "background-color","#33CC00" ); //Marca en verde el fondo de la vista previa.
						$('#td_'+id).css( "background-color","#33CC00" ); //Marca en verde el fondo del cajon.
						}else{
						
						$('#td_'+id).css( "background-color","#C3D9FF" );
						$('#td_imp_previa_'+id).css( "background-color","#C3D9FF" ); //Marca en verde el fondo de la vista previa.
						$("#input_aut_"+id).hide(); //Oculta el cajon para ingresar la cantidad autorizada.
						
						}

					}

			},
			"json"
		);
		
	
	//La cantidad autorizada se registra automaticamente.
	$.post("impresionCTCArticulosNoPos.php",
				{
					consultaAjax:		'actualizarctc',
					wemp_pmla:			wemp_pmla,
					autorizadoCtc:		cantidad_ordenada,
					historia:			his,
					ingreso:			ing,
					cod_articulo:		cod_art,
					presentacion:		presentacion

				}
				,function(data_json) {

					if (data_json.error == 1)
					{
						alert("Error: "+data_json);
						return;
					}
					else
					{
						$("#cant_aut_"+id_input_aprobado).val(cantidad_ordenada);
						$('#imp_'+registro).show();
						$('#div_carga_'+id).html('');
						$('#cant_aut_'+id).show();							

					}

			},
			"json"
		);

	}

 //FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
    function ejecutar(path, vista_previa)
    {

	if(vista_previa != 'on'){
	recargar();
	}

    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');

    }

/******************************************************************
 * AJAX
 ******************************************************************/

/******************************************************************
 * Realiza una llamada ajax a una pagina
 *
 * met:		Medtodo Post o Get
 * pag:		Página a la que se realizará la llamada
 * param:	Parametros de la consulta
 * as:		Asincronro? true para asincrono, false para sincrono
 * fn:		Función de retorno del Ajax, no requerido si el ajax es sincrono
 *
 * Nota:
 * - Si la llamada es GET las opciones deben ir con la pagina.
 * - Si el ajax es sincrono la funcion retorna la respuesta ajax (responseText)
 * - La funcion fn recibe un parametro, el cual es el objeto ajax
 ******************************************************************/
function consultasAjax( met, pag, param, as, fn ){

	this.metodo = met;
	this.parametros = param;
	this.pagina = pag;
	this.asc = as;
	this.fnchange = fn;

	try{
		this.ajax=nuevoAjax();

		this.ajax.open( this.metodo, this.pagina, this.asc );
		this.ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		this.ajax.send(this.parametros);

		if( this.asc ){
			var xajax = this.ajax;
//			this.ajax.onreadystatechange = this.fnchange;
			this.ajax.onreadystatechange = function(){ fn( xajax ) };

			if ( !estaEnProceso(this.ajax) ) {
				this.ajax.send(null);
			}
		}
		else{
			return this.ajax.responseText;
		}
	}catch(e){	}
}
/************************************************************************/

/******************************************************************************************
 * Consulto por ajax la prescripción médica del artículo
 ******************************************************************************************/
function consultarPrescripcionCTC( his, ing, art, div, id, fec, ctcNoPos ){

	var vwemp_pmla = document.getElementById( "wemp_pmla" );
	var fechaActual = new Date();
	diaActual = fechaActual.getDate();
	mesActual = fechaActual.getMonth() + 1;
	anioActual= fechaActual.getFullYear();

	fec = anioActual+"-"+mesActual+"-"+diaActual;

	if( true ){
		//Creo la url para buscar los protocolos segun los parametros ingresado
		// var parametros = "whistoria="+his+"&wingreso="+ing+"&art="+art+"&ide="+id+"&ctcNoPos="+ctcNoPos+"&alt=off";
		var parametros = "whistoria="+his+"&wingreso="+ing+"&art="+art+"&ide="+id+"&ctcNoPos="+ctcNoPos+"&alt=off"+"&impOrdCTC=on";

		//hago la grabacion por ajax del articulo
		consultasAjax( "POST", "ordenes_imp.php?wemp_pmla="+vwemp_pmla.value,
						parametros,
						true,
						function( ajax ){
							if ( ajax.readyState==4 && ajax.status==200 ){

								//Esta función llena los datos del protocolo
								document.getElementById( div ).innerHTML = ajax.responseText;
								boton_imp();
							}
						}
					);
	}
}

/**********************************************************************
 * muestra u oculta un campo segun su id
 **********************************************************************/
function mostrar( campo ){

	if( campo.style.display == 'none' ){
		campo.style.display = '';
	}
	else{
		campo.style.display = 'none';
	}
}

/************************************************************************
 * Busca la fila siguiente para mostrar o ocultar la fila
 * Por tanto campo es una Fila
 ************************************************************************/
function mostrarFila( campo ){

	var tabla = campo.parentNode;
	var index = campo.rowIndex;

	mostrar( tabla.rows[ index+1 ] );

	// for( var i = index+2;i < tabla.rows.length; i++ ){
		// mostrar( tabla.rows[ i ] );
	// }
}


function pulsar(e) {
	tecla=(document.all) ? e.keyCode : e.which;
  if(tecla==13) return false;
}

function limpiarbusqueda(){

 $.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
$('input#id_search').val('');
location.reload();

}

function recargar(){

$.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

// setTimeout(function()
	  // {

		// $.unblockUI()
		// location.reload();
	  // }, 3000);
tiempoRecarga = setTimeout(function()
	  {

		$.unblockUI()
		location.reload();
	  }, 3000);
}


$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);	


$(function() {

	$( ".desplegable" ).accordion({
			collapsible: true,
			active:0,
			heightStyle: "content",
			icons: null
	});

	//Permite que al escribir en el campo buscar, se filtre la informacion del grid
	$('input#id_search').quicksearch('div#accordion');
	
	 $("#wfecha_inicial").datepicker({
   
    });
	
	$("#wfecha_final").datepicker({
   
    });	 
	
	$("#wfecha_inicialSinCTC").datepicker({
   
    });
	
	$("#wfecha_finalSinCTC").datepicker({
   
    });	
	$("#wfecha_inicialMipres").datepicker({
		maxDate:new Date()
    });
	
	$("#wfecha_finalMipres").datepicker({
		maxDate:new Date()
    });	
	
});

$(document).ready(function()
	{
	
	boton_imp();
			
	});
</script>

</head>

<body>
<?php

if(!$_SESSION['user'])
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina de inicio de matrix<FONT COLOR='RED'>" .
        " </FONT></H1>\n</CENTER>");
/**************************************************************************************************************
 * Impresion de CTC para medicamentos No Pos.
 *
 * Fecha de creación:	2014-07-01
 * Por:					Jonatan Lopez
 **************************************************************************************************************/
/**************************************************************************************************************
 * DESCRIPCION:
 *
 * Al selecionar un medicamento No Pos en el programa de ordenes el medico debe doligenciar el formulario de CTC correspondiente,
 * este programa permite la ver la informacion CTC para el medicamento y hacer la impresion despues de haberlo aprobado y asignarle la
 * cantidad aprobada correspondiente.
 *
 **************************************************************************************************************
- Febrero 10 de 2022 Marlon osorio:
								Se parametriza el centro de costos 1050 mediante la funcion ccoUnificadoSF() del comun.php
- Enero 24 de 2018 Jessica:		Se modifica el texto del encabezado de la impresión del ctc
- Diciembre 21 de 2017 Jessica:	Se corrige el query querySinCTC ya que estaba generando lentitud por error en relacion
- Diciembre 18 de 2017 Jessica:	Se comenta el contenido de la funcion consultarDatosTablaHCE() y agrega el llamado a la función consultarUltimoDiagnosticoHCE() de comun.php
								que devuelve la lista de los diagnósticos actuales del paciente
- Agosto 14 de 2017 Jessica:	En la seccion de medicamentos diligenciados en Mipres permite visualizar la información de la prescripción
								consumiendo los web service que dispone el ministerio (https://www.minsalud.gov.co/Paginas/Mipres.aspx 
								en la sección Documentos técnicos) 
								https://www.minsalud.gov.co/Documentos%20y%20Publicaciones/MIPRES%20NoPBS%20-%20Documentaci%C3%B3n%20WEB%20SERVICES%20Versi%C3%B3n%203.1.pdf
								https://wsmipres.sispro.gov.co/WSMIPRESNOPBS/Swagger/ui/index
- Marzo 1 de 2017 Jessica:		Se agrega seccion de medicamentos diligenciados en Mipres (marcados en movhos_000134 como externos)
- Mayo 10 de 2016 Jessica:		Se corrige el query de procedimientos sin ctc por cambio de responsable para que no sea tan lento 
- Mayo 5 de 2016 Jessica:		Se valida si el responsable no sea igual a una empresa definida en empresasConfirmanCTC de root_000051
- Febrero 9 de 2016 Jessica:	Se agrega el origen (Por reemplazo / Kardex) del medicamento si no es por cambio de responsable. 
- Enero 21 de 2016 Jessica:		Se agrega al reporte la seccion Medicamentos sin ctc por cambio de responsable que permite llenar los ctc 
								o marcarlos como no realizar
 **************************************************************************************************************
 - Marzo 25 de 2015 Jonatan: Se agrega al pie de pagina "Firmado electrónicamente".
 **************************************************************************************************************
 - Febrero 03 de 2015 (Jonatan): Se comenta la validacion de cantidad aprobada mientras se inician ordenes en todos los pisos.
 **************************************************************************************************************
 - Enero 19 de 2015: Jonatan. Se premite modificar el tiempo de tratamiento y la cantidad ordenada si el dato tiene NaN.

/****************************************************************************************************************
 * 												FUNCIONES
 ****************************************************************************************************************/
function dimensionesImagen($idemed)
{
	global $altoimagen;
	global $anchoimagen;

	// Obtengo las propiedades de la imagen, ancho y alto
	@list($widthimg, $heightimg) = getimagesize('../../images/medical/hce/Firmas/'.$idemed.'.png');

	$altoimagen = '27';

	@$anchoimagen = (27 * $widthimg) / $heightimg;

	if($anchoimagen<81)
		$anchoimagen = 81;
}




/****************************************************************************************************
 * Calcula la edad del paciente de acuerdo a la fecha de nacimiento
 ****************************************************************************************************/
function calcularEdad( $fecNac ){

	$ann=(integer)substr($fecNac,0,4)*360 +(integer)substr($fecNac,5,2)*30 + (integer)substr($fecNac,8,2);
	$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($aa - $ann)/360;
	$meses=(($aa - $ann) % 360)/30;
	if ($ann1<1){
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	} else {
		$dias1=(($aa - $ann) % 360) % 30;
		$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." d&iacute;a(s)";
	}

	return floor( $ann1 );
}


// function consultarDatosTablaHCE( $conex, $whce, $tabla, $campo, $his, $ing ){

	// $val = "";

	// $sql = "SELECT Movdat
			// FROM
				// {$whce}_{$tabla}
			// WHERE
				// movpro = '$tabla'
				// AND movcon = '$campo'
				// AND movhis = '$his'
				// AND moving = '$ing'
			// ";

	// $res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	// if( $rows = mysql_fetch_array( $res ) ){
		// $val = $rows[ 'Movdat' ];
	// }

	// return $val;
// }



function consultarDatosTablaHCE( $conex, $wemp_pmla, $whce, $his, $ing ){

	return consultarUltimoDiagnosticoHCE( $conex, $wemp_pmla, $whce, $his, $ing );
	
	// $val = "";

	// $camposRoot = consultarAliasPorAplicacion( $conex, $wemp_pmla, "dxsHce" );

	// if( !empty( $camposRoot ) ){
		
		// $campos = explode( ",", $camposRoot );
		
		// for( $i = 0; $i < count( $campos ); $i++ ){
		
			// list( $tabla, $cmp ) = explode( "-", $campos[$i] );
			
			// if( $i > 0 ){
				// $sql .= " UNION ";
			// }
			
			// $sql .= "SELECT
						// *
					// FROM
						// {$whce}_{$tabla}
					// WHERE
						// movhis = '$his'
						// AND moving = '$ing'
						// AND movcon = '$cmp'
					// ";
		// }
		
		// $sql .= " ORDER BY fecha_data DESC, hora_data DESC";
		
		// $res = mysql_query( $sql , $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		// // for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		// $i = 0;
		// if( $rows = mysql_fetch_array( $res ) ){
			
			// if( trim( strip_tags( trim( $rows[ 'movdat' ] ) ) ) != '' ){
				// // echo "<br>".trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// if( $i == 0 ){
					// $val .= trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// }
				// else{
					// $val .= "\n".trim( strip_tags( trim( $rows[ 'movdat' ] ) ) );
				// }
			// }
		// }
	// }
	
	// return $val;
}

/****************************************************************************************************
 * Cambia el estado de la impresion
 ****************************************************************************************************/
function cambiarEsadoImpresionPorId( $conex, $wbasedato, $id, $usuario ){

	$val = false;

	$sql = "UPDATE
				{$wbasedato}_000134
			SET
				Ctcimp = 'on',
				Ctcuim = '$usuario',
				Ctcfim = '".date( "Y-m-d" )."',
				Ctchim = '".date( "H:i:s" )."'
			WHERE
				id = '$id'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows( ) > 0 ){
		$val = true;
	}

	return $val;
}

/**********************************************************************
 * Consulta la informacion de un medico
 **********************************************************************/
function consultarInformacionMedico( $conex, $wbasedato, $codigo ){

	$val = false;

	$sql = "SELECT
				a.*, Espcod, Espnom
			FROM
				{$wbasedato}_000048 a, {$wbasedato}_000044 b
			WHERE
				Meduma = '$codigo'
				AND Medest = 'on'
				AND SUBSTRING_INDEX( medesp, '-', 1 ) = espcod
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}

	return $val;
}

/****************************************************************************************************************
 * Cambia el estado de la impresion segun el parametro estado
 ****************************************************************************************************************/
function cambiarEstadoImpresion( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000134
			SET
				Ctrimp = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}

	return $val;
}


/****************************************************************************************************************
 * Cambia el estado del registro segun el campo estado. on para activarlo, off para desactivarlo
 ****************************************************************************************************************/
function cambiarEstadoRegistro( $conex, $wbasedato, $historia, $ingreso, $articulo, $idOriginal, $codMedico, $estado ){

	$val = false;

	$sql = "UPDATE {$wbasedato}_000134
			SET
				Ctrest = '$estado'
			WHERE
				Ctrhis = '$historia'
				AND Ctring = '$ingreso'
				AND Ctrart = '$articulo'
				AND Ctrido = '$idOriginal'
				AND Ctrmed = '$codMedico'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}

	return $val;
}

/****************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************/


if( $consultaAjax ){	//si hay ajax
}
else{	//si no hay ajax

	include_once("root/montoescrito.php");

	$institucion = consultarInstitucionPorCodigo($conex,$wemp_pmla);

	$whabilitado = "";

	$ccoSF=ccoUnificadoSF();

	//Verifica que usuarios pueden aprobar CTC
	$sql1 = "SELECT Ccouct
			  FROM ".$wbasedato."_000011
			 WHERE Ccocod = '{$ccoSF}'";
	$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$row1 = mysql_fetch_array($res1);
	$usuarios_ctc_apr = explode(",", $row1['Ccouct']);

	//Verifica si el usuario puede aprobar CTC.
	foreach($usuarios_ctc_apr as $key_apr => $value_apr){

		if($value_apr == $wusuario){

			$whabilitado = "on";

			}
	}

	echo "<form id='ctcarticulos'>";

	if( !isset($imprimir) ){

		$wactualiz = "Febrero 10 de 2022";

		encabezado("IMPRESION FORMULARIOS CTC DE MEDICAMENTOS",$wactualiz, "clinica");

		echo "</br>";
		echo "</br>";
		echo "<center>";
			echo "<table>";
				echo "<tr>";
					// echo "<td class=encabezadotabla><font size=5><b>FORMULARIOS CTC DE MEDICAMENTOS PENDIENTES DE IMPRIMIR</b></font></td>";
					echo "<td bgcolor='#C2C9C2'><font size=5><b>FORMULARIOS CTC DE MEDICAMENTOS PENDIENTES DE IMPRIMIR</b></font></td>";
				echo "</tr>";
			echo "</table>";
		echo "</center>";
		
		$sql = "SELECT
					Artgen, Artcom, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ctcart, Ctcprn, Ctcfge, Ctcnoa, Ctctus, Ctcapr, Ctccan, a.id as Ctcid, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f, {$wbasedato}_000060 g
				  WHERE ctcimp = 'off'
					AND ctcest = 'on'
					AND artcod = ctcart
					AND ubihis = ctchis
					AND ubiing = ctcing					
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
				UNION
				SELECT
					Artgen, Artcom, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ctcart, Ctcprn, Ctcfge, Ctcnoa, Ctctus, Ctcapr, Ctccan, a.id as Ctcid, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f, {$wbasedato}_000054 g
				  WHERE ctcimp = 'off'
					AND ctcest = 'on'
					AND artcod = ctcart
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
			   ORDER BY ubisac, ubihac";
// echo$sql."----------------";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );

		if( $num > 0 ){

			echo "<br>";
			echo "<center>";
			echo "<table>";
			echo "<tr>";
			echo "<td class=encabezadotabla>Buscar</td>";
			echo "<td class=encabezadotabla><input id='id_search' type='text' value='' onkeypress='return pulsar(event);'></td>";
			echo "<td><img width='auto' width='15' height='15' border='0' id='recargar' onclick='limpiarbusqueda();' title='Reiniciar Búsqueda' style='cursor:pointer' src='../../images/medical/sgc/Refresh-128.png'></td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";
			echo "<br>";

			$ccoAnt = '';

			$total = 0;
			$totalAImprimir = 0;


			$rows = mysql_fetch_array( $res );
			$ccoAnt = $rows[ 'Ubisac' ];
			
			for( $i = 0; ;  ){

				if( $ccoAnt == $rows[ 'Ubisac' ] ){

					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'hab' ] = $rows[ 'Ubihac' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'nom' ] = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'tot' ]++;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'his' ] = $rows[ 'Ctchis' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'ing' ] = $rows[ 'Ctcing' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'ced' ] = $rows[ 'Ctcnoa' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'resp' ] = $rows[ 'Ctctus' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'id' ] = $rows[ 'Ctcid' ];

					//Si el articulo ya existe en el arreglo quiere decir que al paciente le hicieron uno o mas CTC para el mismo medicamento, entonces
					//insertara en la posicion fecha_gen. cada fecha de generacion de CTC y sumará las cantidades de cada uno.
					if(@!array_key_exists($rows['Ctcart'], @$pacientes[$rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]]['med'])){

					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'med' ][] = array( 'his'=>$rows['Ctchis'],
																								'ing'=>$rows['Ctcing'],
																								'cod_art'=>$rows['Ctcart'],
																								'descrip_art'=>$rows[ 'Ctcpan' ],
																								'presentacion'=>$rows[ 'Ctcprn' ],
																								'fecha_gen'=>array($rows[ 'Ctcfge' ]),
																								'aprobado'=>$rows[ 'Ctcapr' ],
																								'cantidad_ordenada'=> $rows[ 'Ctccan' ],
																								'tiempo_tto'=> $rows[ 'Ctcttn' ],
																								'id_registro'=>$rows[ 'Ctcid' ]);

					}

					$cconom = $rows[ 'Cconom' ];
					$total++;
					$totalAImprimir++;
					$rows = mysql_fetch_array( $res );
				}
				elseif( $total > 0 ){

					echo "<div id='accordion' class='desplegable' style='width:1450px'>";

					echo "<h3>$cconom</h3>";

					$total = 0;
					$ccoAnt = $rows[ 'Ubisac' ];
					$i++;

					//creo una fila mas con la información del paciente que se quiere imprimir
					if( true ){

						echo "<div>";

						$k = 0;
						$checked = "";
						$style = "";
						// echo "<pre>";

						// print_r($pacientes);
						// echo "</pre>";
						echo '<table  style="width: 1200px;">';
						foreach( $pacientes as $keyPacientes => $hisPacientes ){

							$wfecha = date('Y-m-d');

							//==========================================================================================================
							//Busco si tiene el KARDEX actualizado a la fecha
							//==========================================================================================================
							$q = " SELECT COUNT(*) "
								."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000011 B"
								."  WHERE karhis       = '".$hisPacientes['his']."'"
								."    AND karing       = '".$hisPacientes['ing']."'"
								."    AND A.fecha_data = '".$wfecha."'"
								."    AND karcon       = 'on' "
								."    AND ((karcco     = ccocod "
								."    AND  ccolac     != 'on') "
								."     OR   karcco     = '*' ) ";
							$reskar = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$rowkar = mysql_fetch_array($reskar);

							if ($rowkar[0] > 0)
								 $wkardexActualizado="Actualizado";
							  else
								 $wkardexActualizado="Sin Actualizar";


						   echo 	'<tr class=encabezadotabla style="text-align: center;">
									  <td class="fondoAmarillo">Habitación<br>'.$hisPacientes[ 'hab' ].'</td>
									  <td>Historia<br>'.$keyPacientes.'</td>
									  <td nowrap>Paciente<br>'.$hisPacientes[ 'nom' ].'</td>
									  <td>Documento<br>'.$hisPacientes[ 'ced' ].'</td>
									  <td colspan="6" rowspan="1">Responsable<br>'.$hisPacientes[ 'resp' ].'</td>
									  <td colspan=2 class="fondoAmarillo">Kardex '.$wkardexActualizado.'</td>
									</tr>
									<tr class=fila1 style="text-align: center;">
									  <td><b>Codigo</b></td>
									  <td><b>Descripcion</b></td>
									  <td><b>Unidad</b></td>
									  <td><b>Fecha CTC</b></td>
									  <td><b>Aprobar</b></td>
									  <td><b>Tiempo de Tratamiento</b></td>
									  <td><b>Cantidad Ordenada</b></td>									  
									  <td><b>Cantidad Total</b></td>
									  <td><b>Cantidad Autorizada</b></td>
									  <td><b>Cantidad Aplicada</b></td>
									  <td><b>Cantidad Restante</b></td>
									  <td><b>Cantidad Dispensada</b></td>
									  <td colspan=2 style="width: 250px;"><b>Imprimir</b></td>
									</tr>';

									$j = 1;


							foreach($hisPacientes[ 'med' ] as $key => $value){

									$wfechas_gen = "";

									//==========================================================================================================
								   //Traigo la Cantidad Dispensada Real
								   //==========================================================================================================
								   $q = " CREATE TEMPORARY TABLE if not exists TEMPO1 "
									   ." SELECT spauen AS cant "
									   ."   FROM ".$wbasedato."_000004 "
									   ."  WHERE spahis = '".$value['his']."'"
									   ."    AND spaing = '".$value['ing']."'"
									   ."    AND spaart = '".$value['cod_art']."'"
									   ."  UNION "
									   ." SELECT spluen AS cant "
									   ."   FROM ".$wbasedato."_000030 "
									   ."  WHERE splhis = '".$value['his']."'"
									   ."    AND spling = '".$value['ing']."'"
									   ."    AND splart = '".$value['cod_art']."'";
								   $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								   $q = " SELECT SUM(cant) "
									   ."   FROM TEMPO1 ";
								   $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								   $rowdes = mysql_fetch_array($resdes);

								   if ($rowdes[0] > 0)
									  $wcantidadDispensada=$rowdes[0];
									 else
										$wcantidadDispensada=0;

								    // $q = " DELETE FROM TEMPO1 ";
								    $q = " DROP TABLE TEMPO1 ";
								    $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//Actualiza la cantidad aplicada para el paciente y el medicamento
									actualizacionCantidadAplicada($conex,$wbasedato,$value['his'],$value['ing'],$value['cod_art']);

									//Consulta las cantidades actuales del CTC para el articulo.
									$rowctc = consultarCantidadesCTC($value['his'],$value['ing'],$value['cod_art']);

									$path_vista_previa = "/matrix/hce/procesos/impresionCTCArticulosNoPos.php?wemp_pmla=".$wemp_pmla."&imprimir=on&vista_previa=on&historia=".$hisPacientes[ "his" ]."&art=".$value['cod_art']."&wusuario=".$wusuario."&id=".$value[ 'id_registro' ]."";
									$path_imprimir_imp = "/matrix/hce/procesos/impresionCTCArticulosNoPos.php?wemp_pmla=".$wemp_pmla."&imprimir=on&vista_previa=off&historia=".$hisPacientes[ "his" ]."&art=".$value['cod_art']."&wusuario=".$wusuario."&id=".$value[ 'id_registro' ]."";
									$class1 = "fila".($j%2+1)."";
									$color_apr = "";
									$checked = "";
									$wimprimir = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
									$input= "";
									$oculto = "";
									$id_input = $value['cod_art']."_".$value['his']."_".$value['ing']."_".$value[ 'id_registro' ];
									$sololectura = "";
									
									//Se verifica si el registro ha sido aprobado.
									if($value['aprobado'] == 'on'){

										$checked = "checked";

										//Verifica si el usuario esta habilitado para imprimir.
										if($whabilitado == 'on'){

											$color_apr = "background-color: #33CC00;";
											$input = "<input type='checkbox' $checked id='check_aprobar_".$value[ 'id_registro' ]."' onclick='aprobar_ctc_articulos(\"$wemp_pmla\", \"".$value[ 'id_registro' ]."\", \"".$id_input."\", \"".$value['cantidad_ordenada']."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$value[ 'id_registro' ]."\");'>";
											$wimprimir_aux = "<a onclick='ejecutar(".chr(34).$path_imprimir_imp.chr(34).", \"off\")'>Imprimir</a>";

											//Evalua si el articulo tiene cantidad autorizada mayor a cero, si es asi, mostrara la opcion de imprimir.
											if($rowctc['ctccau'] > 0){

												$wimprimir = "<a onclick='ejecutar(".chr(34).$path_imprimir_imp.chr(34).", \"off\")'>Imprimir</a>";

												}

										}else{

										$wimprimir = "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										$color_apr = "background-color: #33CC00;";
										$sololectura = "readonly";

										}

									}else{

										if($whabilitado == 'on'){
											$input = "<input type='checkbox' $checked id='check_aprobar_".$value[ 'id_registro' ]."' onclick='aprobar_ctc_articulos(\"$wemp_pmla\", \"".$value[ 'id_registro' ]."\", \"".$id_input."\", \"".$value['cantidad_ordenada']."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$value[ 'id_registro' ]."\");'>";
											}

										$oculto = "style='display:none'";
										$wimprimir_aux = "<a onclick='ejecutar(".chr(34).$path_imprimir.chr(34).", \"off\")'>Imprimir</a>";

									}

									echo "<tr class=".$class1.">
										  <td >".$value['cod_art']."</td>
										  <td nowrap>".$value['descrip_art']."</td>
										  <td align='center'>".$value['presentacion']."</td>";

									//La posicion fecha_gen es un array que contiene una o mas fechas de generacion de ctc, si solo contiene una fecha
									//imprime la posicion cero (0), sino recorrera el arreglo e imprimira todas las fechas.
									//---------------------------
									if(count($value['fecha_gen']) == 1){

									$wfechas_gen = $value['fecha_gen'][0];

									}else{

									$wfechas_gen .= "<table>";

									foreach($value['fecha_gen'] as $key_fg => $value_fg){
										$wfechas_gen .= "<tr class=fila1>";
										$wfechas_gen .= "<td><font size=2>$value_fg</font></td>";
										$wfechas_gen .= "<tr>";

										$x++;
										}
									$wfechas_gen .= "</table>";
									}
									
										echo "<td align='center'>".$wfechas_gen."</td>";
										echo "<td id='td_".$value[ 'id_registro' ]."' align='center' style='$color_apr'>$input</td>";
										
										//Tiempo de tratamiento.
										if($value['cantidad_ordenada'] != 'NaN'){
											echo "<td align='center'><input type=hidden size=1 value='".$value['tiempo_tto']."' id='tiempo_tto".$id_input."'>".$value['tiempo_tto']."</td>";
										}else{
											echo "<td align='center'><span id='input_tto_".$value[ 'id_registro' ]."'><input type=text size=2 value='".$value['tiempo_tto']."' id='input_tto_".$id_input."' MaxLength='5' $sololectura onchange='act_tto(\"".$wemp_pmla."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$id_input."\", \"".$value[ 'id_registro' ]."\", \"".$value['tiempo_tto']."\",\"".$wusuario."\",\"input_tto_\")'></span></td>";
										}
										
										
										//Cantidad ordenada
										if($value['cantidad_ordenada'] != 'NaN'){										
											echo "<td align='center'><input type=hidden size=1 value='".$value['cantidad_ordenada']."' id='cant_ord_".$id_input."'>".$value['cantidad_ordenada']."</td>";
										}else{
											echo "<td align='center'><span id='input_cant_ord_".$value[ 'id_registro' ]."'><input type=text size=2 value='".$value['cantidad_ordenada']."' id='input_cant_ord_".$id_input."' MaxLength='5' $sololectura onchange='act_cant_ordenada(\"".$wemp_pmla."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$id_input."\", \"".$value[ 'id_registro' ]."\", \"".$value['cantidad_ordenada']."\", \"".$wusuario."\", \"input_cant_ord_\", \"dato_cant_total_\")'></span></td>";
										}
										
										echo "<td align='center'><input type=hidden size=1 value='".$value['cantidad_ordenada']."' id='cant_total_".$id_input."'><span id='dato_cant_total_".$id_input."'>".$value['cantidad_ordenada']."</td>";
										echo "<td align='center'><span id='input_aut_".$value[ 'id_registro' ]."' $oculto><input type=text size=2 value='".$rowctc['ctccau']."' id='cant_aut_".$id_input."' MaxLength='5' $sololectura onchange='actualizarctc(\"".$wemp_pmla."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$id_input."\", \"".$value[ 'id_registro' ]."\", \"".$rowctc['ctccau']."\",\"".$wusuario."\",\"input_aut_\")'></span></td>";
										echo "<td align='center'>".$rowctc['ctccus']."</td>";
										// echo "<td align='center'>".($rowctc['ctccau']-$rowctc['ctccus'])."</td>";
										echo "<td align='center'>".((float)$rowctc['ctccau']-(float)$rowctc['ctccus'])."</td>";
										echo "<td align='center'>".$wcantidadDispensada."</td>";
										echo "<td nowrap id='td_imp_previa_".$value[ 'id_registro' ]."' align=center style='cursor: pointer; $color_apr'><a onclick='ejecutar(".chr(34).$path_vista_previa.chr(34).", \"on\")'>Vista previa</a></td>";
										echo "<td nowrap valign='middle' style='cursor: pointer;'><span id='imp_".$value[ 'id_registro' ]."' style='display:none;'>$wimprimir_aux</span>$wimprimir</td>";
										echo "</tr>";
										$j++;
										
									}


								//echo "<br>";
								$k++;

						}
						echo "</table>";

						echo "</div>";
					}

					// $pacientes = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					$pacientes = array();	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco

					echo "</div>";
					if( !$rows ){
						break;
					}
				}
			}

		}
		else{
			echo "<center><b>NO SE ENCONTRARON CTC PARA IMPRIMIR</b></center>";
		}

		
		//-----------------
		
		// MEDICAMENTOS SIN CTC POR CAMBIO DE RESPONSABLE
		
		$aplicacion = consultarAliasPorAplicacion($conex, $wemp_pmla, "facturacion" );
		
		echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<hr>";
		echo "</br>";
		echo "</br>";
		echo "<center>";
			echo "<table>";
				echo "<tr>";
					// echo "<td class=encabezadotabla><font size=5><b>MEDICAMENTOS SIN CTC POR CAMBIO DE RESPONSABLE</b></font></td>";
					echo "<td bgcolor='#F2FFA0'><font size=5><b>MEDICAMENTOS SIN CTC POR CAMBIO DE RESPONSABLE</b></font></td>";
					// echo "<td class=encabezadotabla><font size=5><b>MEDICAMENTOS SIN CTC</b></font></td>";
					echo "<input type=hidden id='fecSinCTCInicial' name='fecSinCTCInicial' value=''>";
					echo "<input type=hidden id='fecSinCTCFinal' name='fecSinCTCFinal' value=''>";
					// echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
				echo "</tr>";
			echo "</table>";
		echo "</center>";
		
		
		
		if($wfecha_inicialSinCTC == ""){
			$wfecha_inicialSinCTC = date('Y-m-d');
			
		}
		
		if($wfecha_finalSinCTC == ""){
			$wfecha_finalSinCTC = date('Y-m-d');
			
		}
		
		$rangoFechaSinCTC="";
		if($fecSinCTCInicial != "" & $fecSinCTCFinal != "")
		{
			$rangoFechaSinCTC = "'".$fecSinCTCInicial."' AND '".$fecSinCTCFinal."'";
		}
		else
		{
			$fecSinCTCInicial = date('Y-m-d');
			$fecSinCTCFinal = date('Y-m-d');
			
			$rangoFechaSinCTC = "'".$fecSinCTCInicial."' AND '".$fecSinCTCFinal."'";
		}
		
		echo "<br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";										
						echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
					echo "</tr>";					
					
					echo "<tr>";
						echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicialSinCTC' id='wfecha_inicialSinCTC' readonly='readonly' value='".$fecSinCTCInicial."'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_finalSinCTC' id='wfecha_finalSinCTC' readonly='readonly' value='".$fecSinCTCFinal."'></td>";						
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fecha' onclick='consultarSinCTCFecha();' value='Generar'></td>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		
		$tipoEmpresa= consultarAliasPorAplicacion( $conex, $wemp_pmla, "tiposEmpresasEps" );
		
		$empresasConCTC= explode("-",$tipoEmpresa);
		$cadenaEmpresasConCTC="";
		for($r=0;$r<count($empresasConCTC);$r++)
		{
			$cadenaEmpresasConCTC .= "'".$empresasConCTC[$r]."',";
		}
		$cadenaEmpresasConCTC = substr($cadenaEmpresasConCTC,0,-1);
	
		//Empresas que confirman si llenan el CTC
		$wentidades_confirmanCTC = consultarAliasPorAplicacion($conex, $wemp_pmla, "empresasConfirmanCTC");
		
		$responsableEmpresasConfirman = explode(",",$wentidades_confirmanCTC);
		
		// $queryCambioResponsable = "SELECT Kadhis,Kading,Kadart,Kadido,Ingtip, Resfir,Kadpro,Karord,Ubisac,Ccoior 	 
									// FROM ".$wbasedato."_000053 a,".$wbasedato."_000054 b,".$wbasedato."_000011,".$wbasedato."_000016 ,".$wbasedato."_000018 ,".$wbasedato."_000026, ".$aplicacion."_000205		 
								   // WHERE Kadfec BETWEEN ".$rangoFechaSinCTC."
									 // AND kadhis=Inghis 
									 // AND Kading=Inging
									 // AND Ingtip IN (".$cadenaEmpresasConCTC.")
									 // AND Kadart=Artcod
									 // AND Artpos='N'
									 // AND Reshis = kadhis								  									 
									 // AND Resing = kading								  									 
									 // AND Resnit = Ingres			
									 // AND Kadfec >= Resfir
									 
									 // AND karhis=kadhis 
									 // AND Karing=Kading 
									 // AND a.Fecha_data=Kadfec
									 // AND a.Fecha_data=b.Fecha_data
									 // AND Karord='on' 
									 // AND Karcco=Kadcco 
									 // AND Ubihis=kadhis 
									 // AND Ubiing=Kading 
									 // AND Ccocod=Ubisac 
									 // AND Ccoior='on'
									 
									 // UNION
									 
									// SELECT Kadhis,Kading,Kadart,Kadido,Ingtip, Resfir,Kadpro,Karord,Ubisac,Ccoior 	 
									// FROM ".$wbasedato."_000053 a,".$wbasedato."_000060 b,".$wbasedato."_000011,".$wbasedato."_000016 ,".$wbasedato."_000018 ,".$wbasedato."_000026, ".$aplicacion."_000205		 		 
								   // WHERE Kadfec BETWEEN ".$rangoFechaSinCTC."
									 // AND kadhis=Inghis 
									 // AND Kading=Inging
									 // AND Ingtip IN (".$cadenaEmpresasConCTC.")
									 // AND Kadart=Artcod
									 // AND Artpos='N'
									 // AND Reshis = kadhis								  									 
									 // AND Resing = kading								  									 
									 // AND Resnit = Ingres			
									 // AND Kadfec >= Resfir
									 
									 // AND karhis=kadhis 
									 // AND Karing=Kading 
									 // AND a.Fecha_data=Kadfec
									 // AND a.Fecha_data=b.Fecha_data
									 // AND Karord='on' 
									 // AND Karcco=Kadcco  
									 // AND Ubihis=kadhis 
									 // AND Ubiing=Kading 
									 // AND Ccocod=Ubisac 
									 // AND Ccoior='on'
									 
									 // GROUP BY Kadhis,Kading,Kadart;";	
		
		
			//Mayo 10 de 2016						 
			$queryCambioResponsable = "SELECT Kadhis,Kading,Kadart,Kadido,Ingtip, Resfir,Kadpro,Karord,Resnit
									FROM ".$wbasedato."_000053 a,".$wbasedato."_000054 b,".$wbasedato."_000016 ,".$wbasedato."_000026, ".$aplicacion."_000205	 
								   WHERE Kadfec BETWEEN ".$rangoFechaSinCTC."
									 AND kadhis=Inghis 
									 AND Kading=Inging
									 AND Ingtip IN (".$cadenaEmpresasConCTC.")
									 AND Kadart=Artcod
									 AND Artpos='N'
									 AND Reshis = kadhis								  									 
									 AND Resing = kading								  									 
									 AND Resnit = Ingres			
									 AND Kadfec >= Resfir
									 
									 AND karhis=kadhis 
									 AND Karing=Kading 
									 AND a.Fecha_data=Kadfec
									 AND a.Fecha_data=b.Fecha_data
									 AND Karord='on' 
									 AND Karcco=Kadcco 
																		 
									 UNION
									 
									SELECT Kadhis,Kading,Kadart,Kadido,Ingtip, Resfir,Kadpro,Karord,Resnit 
									FROM ".$wbasedato."_000053 a,".$wbasedato."_000060 b,".$wbasedato."_000016 ,".$wbasedato."_000026, ".$aplicacion."_000205
								   WHERE Kadfec BETWEEN ".$rangoFechaSinCTC."
									 AND kadhis=Inghis 
									 AND Kading=Inging
									 AND Ingtip IN (".$cadenaEmpresasConCTC.")
									 AND Kadart=Artcod
									 AND Artpos='N'
									 AND Reshis = kadhis								  									 
									 AND Resing = kading								  									 
									 AND Resnit = Ingres			
									 AND Kadfec >= Resfir
									 
									 AND karhis=kadhis 
									 AND Karing=Kading 
									 AND a.Fecha_data=Kadfec
									 AND a.Fecha_data=b.Fecha_data
									 AND Karord='on' 
									 AND Karcco=Kadcco  
									
									 GROUP BY Kadhis,Kading,Kadart;";							 
		
	
		// $queryCambioResponsable ="SELECT Kadhis,Kading,Kadart,Kadido,Ingtip, Resfir,Kadpro	 
									// FROM ".$wbasedato."_000054,".$wbasedato."_000016 ,".$wbasedato."_000026, ".$aplicacion."_000205		 
								   // WHERE Kadfec BETWEEN ".$rangoFechaSinCTC."
									 // AND kadhis=Inghis 
									 // AND Kading=Inging
									 // AND Ingtip IN (".$cadenaEmpresasConCTC.")
									 // AND Kadart=Artcod
									 // AND Artpos='N'
									 // AND Reshis = kadhis								  									 
									 // AND Resing = kading								  									 
									 // AND Resnit = Ingres			
									 // AND Kadfec >= Resfir
									 
								   // UNION
									 
								  // SELECT Kadhis,Kading,Kadart,Kadido,Ingtip, Resfir,Kadpro 
									// FROM ".$wbasedato."_000060,".$wbasedato."_000016 ,".$wbasedato."_000026, ".$aplicacion."_000205		 
								   // WHERE Kadfec BETWEEN ".$rangoFechaSinCTC."
									 // AND kadhis=Inghis 
									 // AND Kading=Inging
									 // AND Ingtip IN (".$cadenaEmpresasConCTC.")
									 // AND Kadart=Artcod
									 // AND Artpos='N'
									 // AND Reshis = kadhis								  									 
									 // AND Resing = kading								  									 
									 // AND Resnit = Ingres			
									 // AND Kadfec >= Resfir
									 
									 // GROUP BY Kadhis,Kading,Kadart;";							 
		
		// echo "<pre>".print_r($queryCambioResponsable,true)."</pre>";
		$resultadoCambioResponsable = mysql_query( $queryCambioResponsable, $conex ) or die( mysql_errno()." - Error en el query $queryCambioResponsable - ".mysql_error() );
		$cantidaRegistros = mysql_num_rows($resultadoCambioResponsable);
		
		
		echo "</br>";
		echo "</br>";
				
		echo "<center>";

		echo "<INPUT type='hidden' id='wemp_pmla' value='$wemp_pmla'>";
		
		$MedicamentosSinCTCyCambioResponsable = array();
		$posSinCTC = 0;
		if($cantidaRegistros > 0)
		{
			while ($rowCambioResponsable = mysql_fetch_array($resultadoCambioResponsable)) 
			{
				$empNoConfirma = false;				

				$queryEmp = " SELECT Empnit 
								FROM ".$aplicacion."_000024 
							   WHERE Empcod='".$rowCambioResponsable['Resnit']."';";
				
				$resEmp = mysql_query( $queryEmp, $conex ) or die( mysql_errno()." - Error en el query $queryEmp - ".mysql_error() );
				$numEmp = mysql_num_rows($resEmp);					
				
				if($numEmp > 0)
				{
					$rowEmp = mysql_fetch_array($resEmp);
					
					for($c=0;$c<count($responsableEmpresasConfirman);$c++)
					{
						if($rowEmp['Empnit']==$responsableEmpresasConfirman[$c])
						{
							$empNoConfirma = true;
							break;
						}
					}
				}

				if($empNoConfirma == false)
				{
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['his']=$rowCambioResponsable['Kadhis'];
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['ing']=$rowCambioResponsable['Kading'];
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['art']=$rowCambioResponsable['Kadart'];
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['ido']=$rowCambioResponsable['Kadido'];
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['emp']=$rowCambioResponsable['Ingtip'];
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['pro']=$rowCambioResponsable['Kadpro'];
					$MedicamentosSinCTCyCambioResponsable[$posSinCTC]['fir']=$rowCambioResponsable['Resfir'];
					
					$posSinCTC++;
				}
			}
			
			$SinCTCyCambioResponsable=array();
			$arrayCco=array();
			$cantSinCTC=0;
			for($r=0;$r<count($MedicamentosSinCTCyCambioResponsable);$r++)
			{
				$queryMedicamentoCTC = "SELECT * 
										  FROM ".$wbasedato."_000134 
										 WHERE Ctchis='".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 
										   AND Ctcing='".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."' 
										   AND Ctcart='".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
										  
										   AND FIND_IN_SET( ".$MedicamentosSinCTCyCambioResponsable[$r]['ido'].",Ctcido ) > 0
										   AND Ctcest='on';";
													   
				$resultadoMedicamentoCTC = mysql_query( $queryMedicamentoCTC, $conex ) or die( mysql_errno()." - Error en el query $queryMedicamentoCTC - ".mysql_error() );
				$cantidadRegistrosCTC = mysql_num_rows($resultadoMedicamentoCTC);
				
				
				$queryAccionNoRealizar = "SELECT * 
										  FROM ".$wbasedato."_000134 
										 WHERE Ctchis='".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 
										   AND Ctcing='".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."' 
										   AND Ctcart='".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
										   AND FIND_IN_SET( ".$MedicamentosSinCTCyCambioResponsable[$r]['ido'].",Ctcido ) > 0
										   AND Ctcacc='N'
										   AND Ctcest='on'
										   ;";
										   
									   
				$resultadoAccionNoRealizar = mysql_query( $queryAccionNoRealizar, $conex ) or die( mysql_errno()." - Error en el query $queryAccionNoRealizar - ".mysql_error() );
				$cantidadAccionNoRealizar = mysql_num_rows($resultadoAccionNoRealizar);
		
				if($cantidadRegistrosCTC == 0 && $cantidadAccionNoRealizar==0)
				{
					
					$querySinCTC = "SELECT Artgen, Artcom, Unides, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid,Ingres,Ingnre,Kadusu,Kadaan,a.Seguridad  				
									FROM ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,".$wbasedato."_000016,".$wbasedato."_000027,".$wbasedato."_000054 a,".$whce."_000019,".$whce."_000020,usuarios  	
								 WHERE Artcod = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Artest = 'on'
									 AND Artpos = 'N'
									 AND Ubihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Ubiing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Ccocod = Ubisac
									 AND Pacced = Oriced
									 AND Pactid = Oritid
									 AND Orihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Oriing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Inghis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Inging = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Artuni = Unicod
									 AND Oriori = '".$wemp_pmla."'
									 AND Kadhis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 										 
									 AND Kading = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Kadart = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Kadido = '".$MedicamentosSinCTCyCambioResponsable[$r]['ido']."'
									 
									 LIMIT 1
									 
									 UNION
									 
									 SELECT Artgen, Artcom, Unides, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid,Ingres,Ingnre,Kadusu,Kadaan,a.Seguridad  				
									FROM ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,".$wbasedato."_000016,".$wbasedato."_000027,".$wbasedato."_000060 a,".$whce."_000019,".$whce."_000020,usuarios  	
								 WHERE Artcod = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Artest = 'on'
									 AND Artpos = 'N'
									 AND Ubihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Ubiing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Ccocod = Ubisac
									 AND Pacced = Oriced
									 AND Pactid = Oritid
									 AND Orihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Oriing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Inghis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Inging = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Artuni = Unicod
									 AND Oriori = '".$wemp_pmla."'
									 AND Kadhis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 										 
									 AND Kading = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Kadart = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Kadido = '".$MedicamentosSinCTCyCambioResponsable[$r]['ido']."'
									 
									 LIMIT 1;";		
// -----------------------

				  $querySinCTC = "SELECT Artgen, Artcom, Unides, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid,Ingres,Ingnre,Kadusu,Kadaan,a.Seguridad  				
									FROM ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,".$wbasedato."_000016,".$wbasedato."_000027,".$wbasedato."_000054 a  	
								 WHERE Artcod = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Artest = 'on'
									 AND Artpos = 'N'
									 AND Ubihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Ubiing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Ccocod = Ubisac
									 AND Pacced = Oriced
									 AND Pactid = Oritid
									 AND Orihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Oriing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Inghis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Inging = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Artuni = Unicod
									 AND Oriori = '".$wemp_pmla."'
									 AND Kadhis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 										 
									 AND Kading = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Kadart = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Kadido = '".$MedicamentosSinCTCyCambioResponsable[$r]['ido']."'
									 
								GROUP BY Kadhis,Kading,Kadart,Kadido
									 
									 UNION
									 
									 SELECT Artgen, Artcom, Unides, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid,Ingres,Ingnre,Kadusu,Kadaan,a.Seguridad  				
									FROM ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,".$wbasedato."_000016,".$wbasedato."_000027,".$wbasedato."_000060 a	
								 WHERE Artcod = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Artest = 'on'
									 AND Artpos = 'N'
									 AND Ubihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Ubiing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Ccocod = Ubisac
									 AND Pacced = Oriced
									 AND Pactid = Oritid
									 AND Orihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Oriing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Inghis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 AND Inging = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Artuni = Unicod
									 AND Oriori = '".$wemp_pmla."'
									 AND Kadhis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 										 
									 AND Kading = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 AND Kadart = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 AND Kadido = '".$MedicamentosSinCTCyCambioResponsable[$r]['ido']."'
									 
								GROUP BY Kadhis,Kading,Kadart,Kadido;";	


// $querySinCTC = "SELECT Artgen, Artcom, Unides, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid,Ingres,Ingnre,Kadusu,Rolcod,Roldes,Rolmed,Descripcion,Kadaan  				
									// FROM ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,".$wbasedato."_000016,".$wbasedato."_000027,".$wbasedato."_000054,".$whce."_000019,".$whce."_000020,usuarios  	
								 // WHERE Artcod = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 // AND Artest = 'on'
									 // AND Artpos = 'N'
									 // AND Ubihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 // AND Ubiing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Ccocod = Ubisac
									 // AND Pacced = Oriced
									 // AND Pactid = Oritid
									 // AND Orihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 // AND Oriing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Inghis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 // AND Inging = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Artuni = Unicod
									 // AND Oriori = '".$wemp_pmla."'
									 // AND Kadhis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 										 
									 // AND Kading = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Kadart = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 // AND Kadido = '".$MedicamentosSinCTCyCambioResponsable[$r]['ido']."'
									 // AND Kadusu = Usucod
									 // AND Rolcod = Usurol
									 // AND Codigo = Kadusu
									 // LIMIT 1
									 
									 // UNION
									 
									 // SELECT Artgen, Artcom, Unides, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid,Ingres,Ingnre,Kadusu,Rolcod,Roldes,Rolmed,Descripcion,Kadaan  				
									// FROM ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011,root_000036,root_000037,".$wbasedato."_000016,".$wbasedato."_000027,".$wbasedato."_000060,".$whce."_000019,".$whce."_000020,usuarios  	
								 // WHERE Artcod = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 // AND Artest = 'on'
									 // AND Artpos = 'N'
									 // AND Ubihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 // AND Ubiing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Ccocod = Ubisac
									 // AND Pacced = Oriced
									 // AND Pactid = Oritid
									 // AND Orihis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 // AND Oriing = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Inghis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."'
									 // AND Inging = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Artuni = Unicod
									 // AND Oriori = '".$wemp_pmla."'
									 // AND Kadhis = '".$MedicamentosSinCTCyCambioResponsable[$r]['his']."' 										 
									 // AND Kading = '".$MedicamentosSinCTCyCambioResponsable[$r]['ing']."'
									 // AND Kadart = '".$MedicamentosSinCTCyCambioResponsable[$r]['art']."'
									 // AND Kadido = '".$MedicamentosSinCTCyCambioResponsable[$r]['ido']."'
									 // AND Kadusu = Usucod
									 // AND Rolcod = Usurol
									 // AND Codigo = Kadusu
									 // LIMIT 1;";											 
					
					$resultadoSinCTC = mysql_query( $querySinCTC, $conex ) or die( mysql_errno()." - Error en el query $querySinCTC - ".mysql_error() );
					$cantidadSinCTC = mysql_num_rows($resultadoSinCTC);
					
					if($cantidadSinCTC > 0)
					{
						while ($rowSinCTC = mysql_fetch_array($resultadoSinCTC)) 
						{
							$arrayCco[$cantSinCTC] = $rowSinCTC['Ubisac']."|".$rowSinCTC['Cconom'];
							
							$SinCTCyCambioResponsable[$cantSinCTC]['Ubisac']=$rowSinCTC['Ubisac'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Cconom']=$rowSinCTC['Cconom'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Ubihac']=$rowSinCTC['Ubihac'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kadhis']=$MedicamentosSinCTCyCambioResponsable[$r]['his'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kading']=$MedicamentosSinCTCyCambioResponsable[$r]['ing'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kadart']=$MedicamentosSinCTCyCambioResponsable[$r]['art'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kadido']=$MedicamentosSinCTCyCambioResponsable[$r]['ido'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kadpro']=$MedicamentosSinCTCyCambioResponsable[$r]['pro'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Resfir']=$MedicamentosSinCTCyCambioResponsable[$r]['fir'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Artgen']=$rowSinCTC['Artgen'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Artcom']=$rowSinCTC['Artcom'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Artfar']=$rowSinCTC['Artfar'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Unides']=$rowSinCTC['Unides'];					
							$SinCTCyCambioResponsable[$cantSinCTC]['Pacno1']=$rowSinCTC['Pacno1'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Pacno2']=$rowSinCTC['Pacno2'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Pacap1']=$rowSinCTC['Pacap1'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Pacap2']=$rowSinCTC['Pacap2'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Pacnac']=$rowSinCTC['Pacnac'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Pacced']=$rowSinCTC['Pacced'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Pactid']=$rowSinCTC['Pactid'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Ingres']=$rowSinCTC['Ingres'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Ingnre']=$rowSinCTC['Ingnre'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kadusu']=$rowSinCTC['Kadusu'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Seguridad']=$rowSinCTC['Seguridad'];
							// $SinCTCyCambioResponsable[$cantSinCTC]['Rolcod']=$rowSinCTC['Rolcod'];
							// $SinCTCyCambioResponsable[$cantSinCTC]['Roldes']=$rowSinCTC['Roldes'];
							// $SinCTCyCambioResponsable[$cantSinCTC]['Rolmed']=$rowSinCTC['Rolmed'];
							// $SinCTCyCambioResponsable[$cantSinCTC]['Medico']=$rowSinCTC['Descripcion'];
							$SinCTCyCambioResponsable[$cantSinCTC]['Kadaan']=$rowSinCTC['Kadaan'];
							
							$cantSinCTC++;
						}
					}
					
				}
			}
			
			sort($SinCTCyCambioResponsable);
			sort($arrayCco);
			
			$arrayCco=array_unique($arrayCco);
			
			$wfecha = date('Y-m-d');
			
			$UsuariosAccionesHabilitadas = consultarAliasPorAplicacion($conex, $wemp_pmla, "UsuariosRealizanCTCAcciones");
			
			$UsuariosAccHab=explode(",",$UsuariosAccionesHabilitadas);
			
			$MostrarAcciones="off";
			for($i=0;$i<count($UsuariosAccHab);$i++)
			{
				if($UsuariosAccHab[$i]==$wusuario)
				{
					$MostrarAcciones="on";
					break;
				}
			}
			
			
			foreach($arrayCco as $key2 => $cco)
			{
				$ccodes=explode("|",$cco);
				echo "<div id='accordion' class='desplegable' style='width:1450px'>";

					echo "<h3 align='left'>".$ccodes[1]."</h3>";
					echo "<div>";
						echo '<table  style="width: 1200px;">';
						
						foreach($SinCTCyCambioResponsable as $key => $value)
						{
							
							
							// var_dump($SinCTCyCambioResponsable);
							if($ccodes[0]==$value['Ubisac'])
							{
								
								$qRol="";
								if($value['Kadusu']!="")
								{
									$qRol="SELECT Rolcod,Roldes,Rolmed,Descripcion
											FROM ".$whce."_000019,".$whce."_000020,usuarios
											 WHERE Usucod = '".$value['Kadusu']."'
											 AND Rolcod = Usurol
											 AND Codigo = '".$value['Kadusu']."';";
									
								}
								else
								{
									$codUsu=explode("-",$value['Seguridad']);
									$qRol="SELECT Rolcod,Roldes,Rolmed,Descripcion
											FROM ".$whce."_000019,".$whce."_000020,usuarios
											 WHERE Usucod = '".$codUsu[1]."'
											 AND Rolcod = Usurol
											 AND Codigo = '".$codUsu[1]."';";
								}
								
								$resultadoRol = mysql_query( $qRol, $conex ) or die( mysql_errno()." - Error en el query $qRol - ".mysql_error() );
								$cantidadRol = mysql_num_rows($resultadoRol);
								
								if($cantidadRol > 0)
								{
									$rowRol = mysql_fetch_array($resultadoRol);
									
									$value['Rolcod']=$rowRol['Rolcod'];
									$value['Roldes']=$rowRol['Roldes'];
									$value['Rolmed']=$rowRol['Rolmed'];
									$value['Medico']=$rowRol['Descripcion'];
									// while ($rowRol = mysql_fetch_array($resultadoRol)) 
									// {
										// $value['Rolcod']=$rowRol['Rolcod'];
										// $value['Roldes']=$rowRol['Roldes'];
										// $value['Rolmed']=$rowRol['Rolmed'];
										// $value['Medico']=$rowRol['Descripcion'];
									// }							
								}
								
								if($value['Kadhis']."-".$value['Kading'] != $SinCTCyCambioResponsable[$key-1]['Kadhis']."-".$SinCTCyCambioResponsable[$key-1]['Kading'])
								{
									
									
									// //==========================================================================================================
									// //Busco si tiene el KARDEX actualizado a la fecha
									// //==========================================================================================================
									// $q = " SELECT COUNT(*) "
										// ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000011 B"
										// ."  WHERE karhis       = '".$value['Kadhis']."'"
										// ."    AND karing       = '".$value['Kading']."'"
										// ."    AND A.fecha_data = '".$wfecha."'"
										// ."    AND karcon       = 'on' "
										// ."    AND ((karcco     = ccocod "
										// ."    AND  ccolac     != 'on') "
										// ."     OR   karcco     = '*' ) ";
									// $reskar = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									// $rowkar = mysql_fetch_array($reskar);

									// if ($rowkar[0] > 0)
										// $wkardexActualizado="Actualizado";
									// else
										// $wkardexActualizado="Sin Actualizar";
									
									  // <td colspan="'.(($MostrarAcciones == "on") ? "3" : "1").'" class="fondoAmarillo">Kardex '.$wkardexActualizado.'</td>
									  echo 	'<tr class=encabezadotabla style="text-align: center;">
												  <td class="fondoAmarillo" colspan="1">Habitación<br>'.$value['Ubihac'].'</td>
												  <td>Historia<br>'.$value['Kadhis']."-".$value['Kading'].'</td>
												  <td nowrap>Paciente<br>'.$value[ 'Pacno1' ]." ".$value[ 'Pacno2' ]." ".$value[ 'Pacap1' ]." ".$value[ 'Pacap2' ].'</td>
												  <td colspan="1">Documento<br>'.$value['Pacced'].'</td>
												  <td colspan="'.(($MostrarAcciones == "on") ? "4" : "2").'" rowspan="1">Responsable<br>'.$value['Ingres'].' - '.$value['Ingnre'].'</td>
												  
												';
												  
												  
												  $filasRowspan=1;
												  if($MostrarAcciones == "on")
												  {
													 $filasRowspan=2; 
												  }
												  echo'
												  
												</tr>
												<tr class=fila1 style="text-align: center;">
												 
												  <td rowspan="'.$filasRowspan.'"><b>Codigo</b></td>
												  <td rowspan="'.$filasRowspan.'"><b>Descripcion</b></td>
												  <td rowspan="'.$filasRowspan.'"><b>Unidad</b></td>
												  <td rowspan="'.$filasRowspan.'"><b>Ordenado por</b></td>
												  <td rowspan="'.$filasRowspan.'"><b>Rol</b></td>
												  <td rowspan="'.$filasRowspan.'" bgcolor="#CAFFC8" style="color:#000000"><b>Fecha inicio <br> responsable / Origen <br></b></td>';
												  if($MostrarAcciones == "on")
												  {
													 echo'
													  <td colspan="2"><b>Accion</b>
														<span id="info" title="Solo podrá marcar la acción Realizar si la orden fue hecha por un medico, de lo contrario deberá marcar No realizar">
															<img src="../../images/medical/root/info.png" border="0" />
														</span>
													  </td>
													 ';
													  
												 echo	"<tr class=fila1 style='text-align: center;'>
															<td>Realizar</td>
															<td>No realizar</td>
														</tr>";
												  }
												  
								
											echo"</tr>"; 
											
											$fila_lista = "Fila1";
								}
								
								$Accion="";
								if($value['Kadhis']."-".$value['Kading']."|".$value['Kadart'] != $SinCTCyCambioResponsable[$key-1]['Kadhis']."-".$SinCTCyCambioResponsable[$key-1]['Kading']."|".$SinCTCyCambioResponsable[$key-1]['Kadart'])
								{
									$queryAccionCTC = " SELECT Ctcacc
														  FROM ".$wbasedato."_000134 
														 WHERE Ctchis='".$value['Kadhis']."' 
														   AND Ctcing='".$value['Kading']."' 
														   AND Ctcart='".$value['Kadart']."'
														   AND FIND_IN_SET( ".$value['Kadido'].",Ctcido ) > 0
														   AND Ctcacr='off' 
														   AND Ctcest='on' 
														   ;";
														   
									$resultadoAccionCTC = mysql_query( $queryAccionCTC, $conex ) or die( mysql_errno()." - Error en el query $queryAccionCTC - ".mysql_error() );
									$cantidadAccionCTC = mysql_num_rows($resultadoAccionCTC);
									
									$rowAccionCTC = mysql_fetch_array($resultadoAccionCTC);
									$Accion=$rowAccionCTC[0];
									
									$wbasedatohce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
									
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
									echo "<tr class='".$fila_lista."'>
										  
										  <td >".$value['Kadart']."</td>
										  <td colspan='1' nowrap>".$value['Artgen']."</td>
										  <td colspan='1' align='center'>".$value['Unides']."</td>
										  <td colspan='1' align='center'>".$value['Medico']."</td>
										  <td colspan='1' align='center'>".$value['Rolcod']." - ".$value['Roldes']."</td>
										  
										  ";
										  // <td align='center' bgcolor='#DDFEDC'> ".(($value['Kadaan'] == '') ? $value['Resfir'] : 'Por reemplazo' )."</td>
										  // <td align='center' bgcolor='#DDFEDC'>".$value['Resfir']."</td>
										  
										 
										  
										  if($value['Kadusu'] != '')
										  {
											 if($value['Kadaan'] == '')
											  {
												echo "<td align='center' bgcolor='#DDFEDC'>".$value['Resfir']."</td> "; 
											  }
											  else
											  {
												echo "<td align='center' bgcolor='#FADDAE'>Por reemplazo</td> ";
											  }
										  }
										  else
										  {
											echo "<td align='center' class='fondoAmarillo'>Kardex</td> ";
										  }
										  
										  if($MostrarAcciones=="on")
										  {
											  echo"
										 
												  <td colspan='1' align='center'><input type='radio' name='AccionCTC|".$value['Kadhis']."-".$value['Kading']."*".$value['Kadart']."(".$value['Kadido'].")' value='R' ".(($value['Rolmed'] != 'on') ? 'disabled="disabled"': '')." ".(($Accion == 'R') ? 'checked="checked"': '')." onClick='llenarCTC(\"".$value['Kadhis']."\",\"".$value['Kading']."\",\"".$value['Kadart']."\",\"".$value['Kadusu']."\",\"".$wusuario."\",\"".$wemp_pmla."\",\"R\",\"".$value['Kadpro']."\",\"".$key."\",\"".$wbasedatohce."\");'><br></td>
												  
												  <td colspan='1' align='center'><input type='radio' name='AccionCTC|".$value['Kadhis']."-".$value['Kading']."*".$value['Kadart']."(".$value['Kadido'].")' value='N' ".(($Accion == 'N') ? 'checked="checked"': '')." onClick='marcarAccion(\"".$value['Kadhis']."\",\"".$value['Kading']."\",\"".$value['Kadart']."\",\"".$value['Kadusu']."\",\"".$wusuario."\",\"".$wemp_pmla."\",\"N\");'><br></td>
												  
												 
												  "; 
										  }
										  
										  echo "</tr>";
										  
								}
							}
						}
						
						echo '</table>';
					echo "</div>";	
				echo "</div>";
				
			}
		}
		else
		{
			echo "<center><br><br><b>NO SE ENCONTRARON MEDICAMENTOS SIN CTC POR CAMBIO DE RESPONSABLE</b></center>";
			// echo "<center><br><br><b>NO SE ENCONTRARON MEDICAMENTOS SIN CTC</b></center>";
		}
		

		
		echo "</center>";
		
		
		
		
		//-----------------
		
		
		//========================================= SEGMENTO QUE MUESTRA LOS ARTICULOS IMPRESOS =========================================================
		
		if($wfecha_inicial == ""){
			$wfecha_inicial_aux = date('Y-m-d');			
		}else{
			$wfecha_inicial_aux = $wfecha_inicial;
		}
		
		if($wfecha_final == ""){
			$wfecha_final_aux = date('Y-m-d');			
		}else{
			$wfecha_final_aux = $wfecha_final;	
		}
		
		echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<hr>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";
						// echo "<td class=encabezadotabla><font size=5><b>FORMULARIOS CTC DE MEDICAMENTOS IMPRESOS</b></font></td>";
						echo "<td bgcolor='#C2C9C2'><font size=5><b>FORMULARIOS CTC DE MEDICAMENTOS IMPRESOS</b></font></td>";
						echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
			
			echo "<br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";										
						echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
					echo "</tr>";					
					
					echo "<tr>";
						echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicial' id='wfecha_inicial' value='".$wfecha_inicial_aux."'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_final' id='wfecha_final' value='".$wfecha_final_aux."'></td>";						
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fecha' onclick='consultar_fecha(\"fgen\");' value='Por fecha de generacion'><input type='button' onclick='consultar_fecha(\"fimp\");' value='Por fecha de impresion'></td>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		
		if($wfecha_inicial == ""){
			$wfecha_inicial = date('Y-m-d');
			
		}
		
		if($wfecha_final == ""){
			$wfecha_final = date('Y-m-d');
			
		}
			
		if(isset($tipo_consulta) and $tipo_consulta == 'fgen'){
			switch($tipo_consulta){
				case 'fgen' : $filtro_fecha = " AND Ctcfge BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
				break;
				
				case 'fimp' :  $filtro_fecha = " AND Ctcfim BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
				break;
				}
			
		}else{
		
			$filtro_fecha = " AND Ctcfim BETWEEN '".$wfecha_inicial."' AND '".$wfecha_final."'";
		}
		
		//Consulta los registros impresos de articulos con CTC.		
	    $sql = "SELECT
					Artgen, Artcom, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ctcart, Ctcprn, Ctcfge, Ctcnoa, Ctctus, Ctcapr, Ctccan, a.id as Ctcid, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f, {$wbasedato}_000060 g
				  WHERE ctcimp = 'on'
					AND ctcest = 'on'
					AND artcod = ctcart
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					$filtro_fecha					
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
				UNION
				SELECT
					Artgen, Artcom, Artfar, Ubihac, Ubisac, Cconom, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacced, Pactid, Ctcart, Ctcprn, Ctcfge, Ctcnoa, Ctctus, Ctcapr, Ctccan, a.id as Ctcid, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, {$wbasedato}_000018 c, {$wbasedato}_000011 d, root_000036 e, root_000037 f, {$wbasedato}_000054 g
				  WHERE ctcimp = 'on'
					AND ctcest = 'on'
					AND artcod = ctcart
					AND ubihis = ctchis
					AND ubiing = ctcing					
					AND ccocod = ubisac
					AND orihis = ubihis
					AND oriing = ubiing
					AND oritid = pactid
					AND oriced = pacced
					AND oriori = '$wemp_pmla'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					$filtro_fecha
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
			   ORDER BY ubisac, ubihac";
// echo "<pre>".print_r($sql,true)."</pre>";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
			
			echo "<br>";

			$ccoAnt = '';

			$total = 0;
			$totalAImprimir = 0;


			$rows = mysql_fetch_array( $res );
			$ccoAnt = $rows[ 'Ubisac' ];

			for( $i = 0; ;  ){

				if( $ccoAnt == $rows[ 'Ubisac' ] ){

					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'hab' ] = $rows[ 'Ubihac' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'nom' ] = $rows[ 'Pacno1' ]." ".$rows[ 'Pacno2' ]." ".$rows[ 'Pacap1' ]." ".$rows[ 'Pacap2' ];;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'tot' ]++;
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'his' ] = $rows[ 'Ctchis' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'ing' ] = $rows[ 'Ctcing' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'ced' ] = $rows[ 'Ctcnoa' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'resp' ] = $rows[ 'Ctctus' ];
					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'id' ] = $rows[ 'Ctcid' ];

					//Si el articulo ya existe en el arreglo quiere decir que al paciente le hicieron uno o mas CTC para el mismo medicamento, entonces
					//insertara en la posicion fecha_gen. cada fecha de generacion de CTC y sumará las cantidades de cada uno.
					if(@!array_key_exists($rows['Ctcart'], @$pacientes[$rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]]['med'])){

					@$pacientes[ $rows[ 'Ctchis' ]."-".$rows[ 'Ctcing' ]  ][ 'med' ][] = array(  'his'=>$rows['Ctchis'],
																												'ing'=>$rows['Ctcing'],
																												'cod_art'=>$rows['Ctcart'],
																												'descrip_art'=>$rows[ 'Ctcpan' ],
																												'presentacion'=>$rows[ 'Ctcprn' ],
																												'fecha_gen'=>array($rows[ 'Ctcfge' ]),
																												'aprobado'=>$rows[ 'Ctcapr' ],
																												'cantidad_ordenada'=> $rows[ 'Ctccan' ],
																												'tiempo_tto'=> $rows[ 'Ctcttn' ],
																												'id_registro'=>$rows[ 'Ctcid' ]);

					}

					$cconom = $rows[ 'Cconom' ];
					$total++;
					$totalAImprimir++;
					$rows = mysql_fetch_array( $res );
				}
				elseif( $total > 0 ){

					echo "<div id='accordion' class='desplegable' style='width:1400px'>";

					echo "<h3>$cconom</h3>";

					$total = 0;
					$ccoAnt = $rows[ 'Ubisac' ];
					$i++;

					//creo una fila mas con la información del paciente que se quiere imprimir
					if( true ){

						echo "<div>";

						$k = 0;
						$checked = "";
						$style = "";
						// echo "<pre>";

						// print_r($pacientes);
						// echo "</pre>";
						echo '<table  style="width: 1200px;">';
						foreach( $pacientes as $keyPacientes => $hisPacientes ){

							$wfecha = date('Y-m-d');

							//==========================================================================================================
							//Busco si tiene el KARDEX actualizado a la fecha
							//==========================================================================================================
							$q = " SELECT COUNT(*) "
								."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000011 B"
								."  WHERE karhis       = '".$hisPacientes['his']."'"
								."    AND karing       = '".$hisPacientes['ing']."'"
								."    AND A.fecha_data = '".$wfecha."'"
								."    AND karcon       = 'on' "
								."    AND ((karcco     = ccocod "
								."    AND  ccolac     != 'on') "
								."     OR   karcco     = '*' ) ";
							$reskar = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							$rowkar = mysql_fetch_array($reskar);

							if ($rowkar[0] > 0)
								 $wkardexActualizado="Actualizado";
							  else
								 $wkardexActualizado="Sin Actualizar";


						   echo 	'<tr class=encabezadotabla style="text-align: center;">
									  <td class="fondoAmarillo">Habitación<br>'.$hisPacientes[ 'hab' ].'</td>
									  <td>Historia<br>'.$keyPacientes.'</td>
									  <td nowrap>Paciente<br>'.$hisPacientes[ 'nom' ].'</td>
									  <td>Documento<br>'.$hisPacientes[ 'ced' ].'</td>
									  <td colspan="6" rowspan="1">Responsable<br>'.$hisPacientes[ 'resp' ].'</td>
									  <td colspan=2 class="fondoAmarillo">Kardex '.$wkardexActualizado.'</td>
									</tr>
									<tr class=fila1 style="text-align: center;">
									 <td><b>Codigo</b></td>
									  <td><b>Descripcion</b></td>
									  <td><b>Unidad</b></td>
									  <td><b>Fecha CTC</b></td>
									  <td><b>Aprobar</b></td>
									  <td><b>Tiempo de Tratamiento</b></td>
									  <td><b>Cantidad Ordenada</b></td>									  
									  <td><b>Cantidad Total</b></td>
									  <td><b>Cantidad Autorizada</b></td>
									  <td><b>Cantidad Aplicada</b></td>
									  <td><b>Cantidad Restante</b></td>
									  <td><b>Cantidad Dispensada</b></td>
									  <td><b>Imprimir</b></td>
									</tr>';

									$j = 1;


							foreach($hisPacientes[ 'med' ] as $key => $value){

									$wfechas_gen = "";

									//==========================================================================================================
								   //Traigo la Cantidad Dispensada Real
								   //==========================================================================================================
								   $q = " CREATE TEMPORARY TABLE if not exists TEMPO1 "
									   ." SELECT spauen AS cant "
									   ."   FROM ".$wbasedato."_000004 "
									   ."  WHERE spahis = '".$value['his']."'"
									   ."    AND spaing = '".$value['ing']."'"
									   ."    AND spaart = '".$value['cod_art']."'"
									   ."  UNION "
									   ." SELECT spluen AS cant "
									   ."   FROM ".$wbasedato."_000030 "
									   ."  WHERE splhis = '".$value['his']."'"
									   ."    AND spling = '".$value['ing']."'"
									   ."    AND splart = '".$value['cod_art']."'";
								   $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

								   $q = " SELECT SUM(cant) "
									   ."   FROM TEMPO1 ";
								   $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
								   $rowdes = mysql_fetch_array($resdes);

								   if ($rowdes[0] > 0)
									  $wcantidadDispensada=$rowdes[0];
									 else
										$wcantidadDispensada=0;

								    // $q = " DELETE FROM TEMPO1 ";
								    $q = " DROP TABLE TEMPO1 ";
								    $resdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									//Actualiza la cantidad aplicada para el paciente y el medicamento
									actualizacionCantidadAplicada($conex,$wbasedato,$value['his'],$value['ing'],$value['cod_art']);

									//Consulta las cantidades actuales del CTC para el articulo.
									$rowctc = consultarCantidadesCTC($value['his'],$value['ing'],$value['cod_art']);

									//$path_vista_previa = "/matrix/hce/procesos/impresionCTCArticulosNoPos.php?wemp_pmla=".$wemp_pmla."&imprimir=on&vista_previa=on&historia=".$hisPacientes[ "his" ]."&art=".$value['cod_art']."&wusuario=".$wusuario."&id=".$value[ 'id_registro' ]."";
									$path_imprimir = "/matrix/hce/procesos/impresionCTCArticulosNoPos.php?wemp_pmla=".$wemp_pmla."&imprimir=on&vista_previa=off&historia=".$hisPacientes[ "his" ]."&art=".$value['cod_art']."&wusuario=".$wusuario."&id=".$value[ 'id_registro' ]."&reimprimir=on";
									$class1 = "fila".($j%2+1)."";
									$color_apr = "";
									$checked = "";									
									$input= "";
									$oculto = "";
									$checked = "checked";
									$re_wimprimir = "<a onclick='ejecutar(".chr(34).$path_imprimir.chr(34).", \"off\")'>Imprimir</a>";
									$color_apr = "background-color: #33CC00;";										

									echo "<tr class=".$class1.">
										  <td >".$value['cod_art']."</td>
										  <td nowrap>".$value['descrip_art']."</td>
										  <td align='center'>".$value['presentacion']."</td>";

									//La posicion fecha_gen es un array que contiene una o mas fechas de generacion de ctc, si solo contiene una fecha
									//imprime la posicion cero (0), sino recorrera el arreglo e imprimira todas las fechas.
									//---------------------------
									if(count($value['fecha_gen']) == 1){

									$wfechas_gen = $value['fecha_gen'][0];

									}else{

									$wfechas_gen .= "<table>";

									foreach($value['fecha_gen'] as $key_fg => $value_fg){
										$wfechas_gen .= "<tr class=fila1>";
										$wfechas_gen .= "<td><font size=2>$value_fg</font></td>";
										$wfechas_gen .= "<tr>";

										$x++;
										}
									$wfechas_gen .= "</table>";
									}

									$id_input = $value['cod_art']."-".$value['his']."-".$value['ing'];
									
									echo "<td align='center'>".$wfechas_gen."</td>";
										echo "<td id='td_".$value[ 'id_registro' ]."' align='center' style='$color_apr'>$input</td>";
										
										//Tiempo de tratamiento.
										if($value['cantidad_ordenada'] != 'NaN'){
											echo "<td align='center'><input type=hidden size=1 value='".$value['tiempo_tto']."' id='tiempo_tto".$id_input."'>".$value['tiempo_tto']."</td>";
										}else{
											echo "<td align='center'><span id='input_tto_imp_".$value[ 'id_registro' ]."'><input type=text size=2 value='".$value['tiempo_tto']."' id='input_tto_imp_".$id_input."' MaxLength='5' $sololectura onchange='act_tto(\"".$wemp_pmla."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$id_input."\", \"".$value[ 'id_registro' ]."\", \"".$value['tiempo_tto']."\",\"".$wusuario."\",\"input_tto_imp_\")'></span></td>";
										}										
										
										//Cantidad ordenada
										if($value['cantidad_ordenada'] != 'NaN'){										
											echo "<td align='center'><input type=hidden size=1 value='".$value['cantidad_ordenada']."' id='cant_ord_".$id_input."'>".$value['cantidad_ordenada']."</td>";
										}else{
											echo "<td align='center'><span id='input_cant_ord_imp_".$value[ 'id_registro' ]."'><input type=text size=2 value='".$value['cantidad_ordenada']."' id='input_cant_ord_imp_".$id_input."' MaxLength='5' $sololectura onchange='act_cant_ordenada(\"".$wemp_pmla."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$id_input."\", \"".$value[ 'id_registro' ]."\", \"".$value['cantidad_ordenada']."\", \"".$wusuario."\", \"input_cant_ord_imp_\", \"dato_cant_total_imp_\")'></span></td>";
										}
										
										echo "<td align='center'><input type=hidden size=1 value='".$value['cantidad_ordenada']."' id='cant_total_".$id_input."'><span id='dato_cant_total_imp_".$id_input."'>".$value['cantidad_ordenada']."</td>";
										
										//Cantidad autorizada
										if($value['cantidad_ordenada'] != 'NaN'){										
											echo "<td align='center'><input type=hidden size=1 value='".$rowctc['ctccau']."' id='cant_ord_".$id_input."'>".$rowctc['ctccau']."</td>";
										}else{
											echo "<td align='center'><span id='input_aut_imp_".$value[ 'id_registro' ]."' $oculto><input type=text size=2 value='".$rowctc['ctccau']."' id='cant_aut_".$id_input."' MaxLength='5' $sololectura onchange='actualizarctc(\"".$wemp_pmla."\", \"".$value['cod_art']."\", \"".$value['his']."\", \"".$value['ing']."\", \"".$value['presentacion']."\", \"".$id_input."\", \"".$value[ 'id_registro' ]."\", \"".$rowctc['ctccau']."\",\"".$wusuario."\",\"input_aut_imp_\")'></span></td>";
										}
										
										echo "<td align='center'>".$rowctc['ctccus']."</td>";
										echo "<td align='center'>".((float)$rowctc['ctccau']-(float)$rowctc['ctccus'])."</td>";
										echo "<td align='center'>".$wcantidadDispensada."</td>";										
										echo "<td style='cursor:pointer;'>$re_wimprimir</td>";
										echo "</tr>";
										$j++;
									}


								//echo "<br>";
								$k++;

						}
						echo "</table>";

						echo "</div>";
					}

					// $pacientes = "";	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco
					$pacientes = array();	//dejo nuevamente los pacientes vacios por que se muestran solo los del cco

					echo "</div>";
					if( !$rows ){
						break;
					}
				}
			}

		}
		else{
			echo "<center><br><br><b>NO SE ENCONTRARON CTC IMPRESOS</b></center>";
		}
		
		
		
		
		// ---------------------------------------------------
		// 		CTC MEDICAMENTOS DILIGENCIADOS EN MIPRES
		// ---------------------------------------------------
		
		
		if($wfecha_inicialMipres == ""){
			$wfecha_inicialMipres = date('Y-m-d');
			
		}
		
		if($wfecha_finalMipres == ""){
			$wfecha_finalMipres = date('Y-m-d');
			
		}
		
		$rangoFechaMipres = "'".$wfecha_inicialMipres."' AND '".$wfecha_finalMipres."'";
			
		echo "<br>";
			echo "<br>";
			echo "<br>";
			echo "<hr>";
			echo "<br><br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";
						echo "<td bgcolor='#C2C9C2'><font size=5><b>CTC MEDICAMENTOS DILIGENCIADOS EN MIPRES</b></font></td>";
						echo "<input type=hidden id='tipo_consulta' name='tipo_consulta' value=''>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
			
			echo "<br>";
			echo "<center>";
				echo "<table>";
					echo "<tr>";										
						echo "<td colspan=2 class=encabezadotabla align=center><b>Buscar:</b></td>";
					echo "</tr>";					
					
					echo "<tr>";
						echo "<td class=fila1 align=left>Fecha inicial:</td><td align=left><input type=text name='wfecha_inicialMipres' id='wfecha_inicialMipres' readonly='readonly' value='".$wfecha_inicialMipres."'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td class=fila2 align=left>Fecha final:</td><td align=left><input type=text name='wfecha_finalMipres' id='wfecha_finalMipres' readonly='readonly' value='".$wfecha_finalMipres."'></td>";						
					echo "</tr>";
					echo "<tr>";
						echo "<td colspan=2 align=center><input type='button' id='btn_consultar_fechaMipres' onclick='consultarMipresFecha();' value='Generar'></td>";
					echo "</tr>";
				echo "</table>";
			echo "</center>";
		
		
		
										
		$qCTCcontributivo = " SELECT a.Fecha_data,Ctchis,Ctcing,Ctcart,Ctcido,Ctcmed,Ctcacc, Artgen,Ubihac,Ubisac,Cconom,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,Ingres,Ingnre,Kadido,Kadcfr,Kadufr,Kadsus,Kadfin,Kadhin,Descripcion,Unides, Rolcod,Roldes,Ctcmip
								FROM ".$wbasedato."_000134 a, ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011, root_000036, root_000037,".$wbasedato."_000016,".$wbasedato."_000054,usuarios,".$wbasedato."_000027,hce_000019,hce_000020 
							   WHERE Ctcacc IN ('E','EM') 
								 AND a.Fecha_data BETWEEN ".$rangoFechaMipres."
								 AND Ctcest='on' 
								 AND Ctcart=Artcod 
								 AND Artest='on' 
								 AND ubihis = ctchis 
								 AND ubiing = ctcing 
								 AND ccocod = ubisac 
								 AND orihis = ubihis 
								 AND oriing = ubiing 
								 AND oritid = pactid 
								 AND oriced = pacced 
								 AND oriori = '".$wemp_pmla."' 
								 AND Inghis = Ctchis 
								 AND Inging = Ctcing 
								 AND kadhis = ctchis 
								 AND kading = ctcing 
								 AND kadart = ctcart 
								 AND FIND_IN_SET( kadido,ctcido ) > 0 
								 AND kadfec = a.Fecha_data 
								 AND codigo = Ctcmed 
								 AND Unicod = Kaduma
								 AND Ctcmed = Usucod
								 AND Usurol = Rolcod
								 
							   UNION
							   
							  SELECT a.Fecha_data,Ctchis,Ctcing,Ctcart,Ctcido,Ctcmed,Ctcacc, Artgen,Ubihac,Ubisac,Cconom,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,Ingres,Ingnre,Kadido,Kadcfr,Kadufr,Kadsus,Kadfin,Kadhin,Descripcion,Unides, Rolcod,Roldes,Ctcmip 
								FROM ".$wbasedato."_000134 a, ".$wbasedato."_000026,".$wbasedato."_000018,".$wbasedato."_000011, root_000036, root_000037,".$wbasedato."_000016,".$wbasedato."_000060,usuarios,".$wbasedato."_000027,hce_000019,hce_000020 
							   WHERE Ctcacc IN ('E','EM') 
								 AND a.Fecha_data BETWEEN ".$rangoFechaMipres."
								 AND Ctcest='on' 
								 AND Ctcart=Artcod 
								 AND Artest='on' 
								 AND ubihis = ctchis 
								 AND ubiing = ctcing 
								 AND ccocod = ubisac 
								 AND orihis = ubihis 
								 AND oriing = ubiing 
								 AND oritid = pactid 
								 AND oriced = pacced 
								 AND oriori = '".$wemp_pmla."' 
								 AND Inghis = Ctchis 
								 AND Inging = Ctcing 
								 AND kadhis = ctchis 
								 AND kading = ctcing 
								 AND kadart = ctcart 
								 AND FIND_IN_SET( kadido,ctcido ) > 0 
								 AND kadfec = a.Fecha_data 
								 AND codigo = Ctcmed 
								 AND Unicod = Kaduma
								 AND Ctcmed = Usucod
								 AND Usurol = Rolcod;";
								
								
		$resCTCcontributivo=  mysql_query($qCTCcontributivo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qCTCcontributivo." - ".mysql_error());
		$numCTCcontributivo = mysql_num_rows($resCTCcontributivo);	
		
		$arrayCTCmipres = array();
		$arrayCcosto = array();
		if($numCTCcontributivo > 0)
		{
			echo "	<table align='right' style='border: 1px solid black;border-radius: 5px;'>
							<tr>
							<td align='center' style='font-size:8pt'><b>Convenciones</b></td>
							</tr>
							<tr>
							<td><span class='medSuspendido' style='border-radius:3px'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;Suspendido&nbsp;&nbsp;</span></td>
							</tr>
						</table>
						<br><br><br>";
			while($rowCTCcontributivo = mysql_fetch_array($resCTCcontributivo))
			{
				$arrayCcosto[$rowCTCcontributivo['Ubisac']] = $rowCTCcontributivo['Cconom'];
				
				$idPac = $rowCTCcontributivo['Ctchis']."-".$rowCTCcontributivo['Ctcing'];
				$arrayPacientes[$idPac]['historia'] = $rowCTCcontributivo['Ctchis'];
				$arrayPacientes[$idPac]['ingreso']  = $rowCTCcontributivo['Ctcing'];
				$arrayPacientes[$idPac]['nombre']   = $rowCTCcontributivo['Pacno1']." ".$rowCTCcontributivo['Pacno2']." ".$rowCTCcontributivo['Pacap1']." ".$rowCTCcontributivo['Pacap2'];
				$arrayPacientes[$idPac]['servicio'] = $rowCTCcontributivo['Cconom'];
				$arrayPacientes[$idPac]['cco']   		= $rowCTCcontributivo['Ubisac'];
				$arrayPacientes[$idPac]['habitacion']    = $rowCTCcontributivo['Ubihac'];
				$arrayPacientes[$idPac]['documento']     = $rowCTCcontributivo['Pacced'];
				$arrayPacientes[$idPac]['tipoDocumento'] = $rowCTCcontributivo['Pactid'];
				$arrayPacientes[$idPac]['codResponsable'] = $rowCTCcontributivo['Ingres'];
				$arrayPacientes[$idPac]['responsable'] = $rowCTCcontributivo['Ingnre'];
				
				$idMedicamentos = $rowCTCcontributivo['Ctchis']."-".$rowCTCcontributivo['Ctcing']."-".$rowCTCcontributivo['Ctcart']."-".$rowCTCcontributivo['Kadido'];
				$arrayCTCmipres[$idMedicamentos]['codArticulo'] = $rowCTCcontributivo['Ctcart'];
				$arrayCTCmipres[$idMedicamentos]['idoCTC'] = $rowCTCcontributivo['Ctcido'];
				$arrayCTCmipres[$idMedicamentos]['idoArticulo'] = $rowCTCcontributivo['Kadido'];
				$arrayCTCmipres[$idMedicamentos]['generico'] = $rowCTCcontributivo['Artgen'];
				$arrayCTCmipres[$idMedicamentos]['unidad'] = $rowCTCcontributivo['Unides'];
				$arrayCTCmipres[$idMedicamentos]['dosis'] = $rowCTCcontributivo['Kadcfr']." ".$rowCTCcontributivo['Kadufr'];
				$arrayCTCmipres[$idMedicamentos]['fechaCTC'] = $rowCTCcontributivo['Fecha_data'];
				$arrayCTCmipres[$idMedicamentos]['fechaInicio'] = $rowCTCcontributivo['Kadfin'];
				$arrayCTCmipres[$idMedicamentos]['horaInicio'] = $rowCTCcontributivo['Kadhin'];
				$arrayCTCmipres[$idMedicamentos]['suspendido'] = $rowCTCcontributivo['Kadsus'];
				$arrayCTCmipres[$idMedicamentos]['usuario'] = $rowCTCcontributivo['Ctcmed'];
				$arrayCTCmipres[$idMedicamentos]['nombreMedico'] = $rowCTCcontributivo['Descripcion'];
				$arrayCTCmipres[$idMedicamentos]['rolMedico'] = $rowCTCcontributivo['Rolcod']." - ".$rowCTCcontributivo['Roldes'];
				$arrayCTCmipres[$idMedicamentos]['accionCTC'] = $rowCTCcontributivo['Ctcacc'];
				$arrayCTCmipres[$idMedicamentos]['mipres'] = $rowCTCcontributivo['Ctcmip'];
				
			}
			
		}
		else
		{
			echo "<center><br><br><b>NO SE ENCONTRARON CTC DILIGENCIADOS EN MIPRES</b></center>";
		}		
				
		if(count($arrayCcosto)>0)
		{
			foreach($arrayCcosto as $cco => $ccoNombre)
			{
				echo "<div id='accordion' class='desplegable' style='width:1450px'>";

					echo "<h3 align='left'>".$ccoNombre."</h3>";
					echo "<div>";
						echo '<table  style="width: 1200px;">';
						foreach($arrayPacientes as $keyPaciente => $valuePaciente)
						{
							if($valuePaciente['cco']==$cco)
							{
								echo 	'<tr class=encabezadotabla style="text-align: center;">
											<td class="fondoAmarillo" colspan="1">Habitación<br>'.$valuePaciente['habitacion'].'</td>
											<td colspan="2">Historia<br>'.$valuePaciente['historia']."-".$valuePaciente['ingreso'].'</td>
											<td colspan="2" nowrap>Paciente<br>'.$valuePaciente[ 'nombre' ].'</td>
											<td colspan="2">Documento<br>'.$valuePaciente['tipoDocumento']." ".$valuePaciente['documento'].'</td>
											<td colspan="2">Responsable<br>'.$valuePaciente['codResponsable'].' - '.$valuePaciente['responsable'].'</td>
											<td colspan="2">MIPRES</td>
											  
											';
											  $colspanMipres = 1;
											  // $colspanMipres = count($valuePaciente['historia']."-".$valuePaciente['ingreso']."-".$valueCTCmipres['codArticulo']."-".$valueCTCmipres['idoArticulo'])+1;
											  echo'
											  
											</tr>
											<tr class=fila1 style="text-align: center;">
											 
											  <td><b>Codigo</b></td>
											  <td><b>Descripcion</b></td>
											  <td><b>Unidad</b></td>
											  <td><b>Dosis</b></td>
											  <td><b>Fecha de la orden</b></td>
											  <td><b>Fecha y hora de inicio</b></td>
											  <td><b>Ordenado por</b></td>
											  <td><b>Rol</b></td>
											  <td><b>Cambio de responsable</b></td>
											  <td colspan="'.$colspanMipres.'"><b>Prescripcion en MIPRES</b></td>
											  ';
											
											$fila_lista = "Fila1";
											foreach($arrayCTCmipres as $keyCTCmipres => $valueCTCmipres)
											{
												if($valuePaciente['historia']."-".$valuePaciente['ingreso']."-".$valueCTCmipres['codArticulo']."-".$valueCTCmipres['idoArticulo']==$keyCTCmipres)
												{
													if ($fila_lista=='Fila1')
														$fila_lista = "Fila2";
													else
														$fila_lista = "Fila1";
													
													if($valueCTCmipres['suspendido']=="on")
													{
														$fila_lista = "medSuspendido";
													}
													
													$accionCTCmipres = "NO";
													if($valueCTCmipres['accionCTC']=="EM")
													{
														$accionCTCmipres = "SI";
													}
																										
													$onclickMipres = "onclick='verPrescripcionPorPacienteFec(\"".$valuePaciente['historia']."\",\"".$valuePaciente['ingreso']."\",\"".$valuePaciente['tipoDocumento']."\",\"".$valuePaciente['documento']."\",\"".$valueCTCmipres['fechaCTC']."\");'";
													$mipres = "<span class='presentacionMipres'>Ver prescripciones en mipres</span>";
													if($valueCTCmipres['mipres']!="")
													{
														$onclickMipres = "onclick='abrirModalMipres(\"".$valuePaciente['historia']."\",\"".$valuePaciente['ingreso']."\",\"".$valuePaciente['tipoDocumento']."\",\"".$valuePaciente['documento']."\",\"".$valueCTCmipres['fechaCTC']."\",\"".$valueCTCmipres['mipres']."\");'";
														$mipres = "<span class='presentacionMipres'>".$valueCTCmipres['mipres']."</span>";
													}
													
													
													
													echo "<tr class='".$fila_lista."'>
														  
														  <td >".$valueCTCmipres['codArticulo']."</td>
														  <td colspan='1' nowrap>".$valueCTCmipres['generico']."</td>
														  <td colspan='1' align='center'>".$valueCTCmipres['unidad']."</td>
														  <td colspan='1' align='center'>".$valueCTCmipres['dosis']."</td>
														  <td colspan='1' align='center'>".$valueCTCmipres['fechaCTC']."</td>
														  <td colspan='1' align='center'>".$valueCTCmipres['fechaInicio']." ".$valueCTCmipres['horaInicio']."</td>
														  <td colspan='1' align='center'>".$valueCTCmipres['nombreMedico']."</td>
														  <td colspan='1' align='center'>".$valueCTCmipres['rolMedico']."</td>
														  <td colspan='1' align='center'>".$accionCTCmipres."</td>
														  <td colspan='1' align='center' style='cursor:pointer;' ".$onclickMipres.">".$mipres."</td>
														  
														 </tr>";
												}
											}										
										echo"</tr>";
							}
						}
						echo '</table>';
					echo "</div>";	
				echo "</div>";
			}
		}
		
		
	
		
		// echo "<div id='divReporteMipres'></div>";
		echo "<br>";
		echo "<br>";
		// ---------------------------------------------------

		echo "<INPUT type='hidden' value='on' id='imprimir'>";
		echo "<INPUT type='hidden' value='$wemp_pmla' id='wemp_pmla' name='wemp_pmla'>";

		echo "<br>";

		echo "<table align='center'>";
		echo "<tr>";
		echo "<td>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
		
		
		// Modal mipres
		echo "<div id='dvAuxModalMipres' style='display:none'></div>";
	}
	else{

	?>
	<style>
	  td {
		font-family: verdana;
		font-size: 6.5pt;
	  }

	  table{
		border-collapse: collapse;
	  }

	  .encabezado {
		font-size: 6.5pt;
		font-weight: bold;
	}
	.encabezadoExamen {
		text-align: right;
		font-size: 8pt;
	}
	.encabezadoEmpresa {
		text-align: left;
		font-size: 6.5pt;
	}
	.filaEncabezado {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.filaEncabezadoFin {
		border-bottom: 1px solid rgb(51, 51, 51);
		border-right: 1px solid rgb(51, 51, 51);
		border-left: 1px solid rgb(51, 51, 51);
	}
	.campoFirma {
		border-bottom: 1px solid rgb(51, 51, 51);
		width:208px;
		height:24px;
	}
	.descripcion
	{
		font-size: 5.5pt;
		text-align:justify;
	}
	.total
	{
		font-size: 6.5pt;
		height: 27px;
		text-align:right;
		text-valign:bottom;
	}
	</style>
	<?php

		//Si la historia es vacia, significa que imprime todo
		if( empty( $historia ) ){
			$historia = '%';
		}

		//Si el cco es vacio, significa que imprime todo
		if( empty( $cco ) ){
			$cco = '%';
		}

		//Si el articulo no se ha seteado, se imprime todos los articulos
		if( empty( $art ) ){
			$art = '%';
		}
		
		$control_impresion = "ctcimp = 'off'";
		
		if($reimprimir=='on'){
				
				//Log de reimpresion de medicamento de control.
				$sql = "INSERT INTO {$wbasedato}_000165(     Medico   ,            Fecha_data  ,      Hora_data         ,    Impusu  ,   Impori,  Impest ,  Seguridad    )
							                         VALUES (  '$wbasedato', '".date('Y-m-d')."',  '".date('H:i:s')."'  , '$wusuario',   'CTCArticulosNoPos', 'on', 'C-$wusuario' )";
							
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				
				$control_impresion = "ctcimp = 'on'";
				
			}
		
		//Consulto los articulos a imprimir
		$sql = "SELECT
					Artgen, Artcom, Artfar, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Ubisac, Ubihac, Pactid, Pacced, Cconom, Kadufr, Ctcapr, a.Fecha_data as fecCTC, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000011 g, {$wbasedato}_000060 h
				WHERE   $control_impresion
					AND artcod = ctcart
					AND orihis = ctchis
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ccocod = ubisac
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco'
					AND ctcart LIKE '$art'
					AND a.id = '$id'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
				UNION
				SELECT
					Artgen, Artcom, Artfar, Oriced, Oritid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Ubisac, Ubihac, Pactid, Pacced, Cconom, Kadufr, Ctcapr, a.Fecha_data as fecCTC, a.*
				FROM
					{$wbasedato}_000134 a, {$wbasedato}_000026 b, root_000036 d, root_000037 e, {$wbasedato}_000018 f, {$wbasedato}_000011 g, {$wbasedato}_000054 h
				WHERE   ctcest = 'on'
					AND artcod = ctcart
					AND orihis = ctchis
					AND oriori = '$wemp_pmla'
					AND oriced = pacced
					AND oritid = pactid
					AND ubihis = ctchis
					AND ubiing = ctcing
					AND ccocod = ubisac
					AND ctchis LIKE '$historia'
					AND ubisac LIKE '$cco'
					AND ctcart LIKE '$art'
					AND a.id = '$id'
					AND kadhis = ctchis
					AND kading = ctcing
					AND kadart = ctcart
					AND FIND_IN_SET( kadido,ctcido ) > 0
					AND kadfec = ctcfkx
				";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows($res);
		
		echo "<p align=center><input type='button' class='printer' value='Imprimir'><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";		
		
		echo "<div class='areaimprimirCTC'>";
		echo "<div style='width:20cm' align='center'>";

		echo "<INPUT type='hidden' id='wemp_pmla' value='$wemp_pmla'>";

		for( $i = 0; $rowsCTC = mysql_fetch_array( $res ); $i++ ){

			//Hago un salgo de linea si se va a imprimir otra factura
			if( $i > 0 ){
				echo "<div style='page-Break-After:always'></div>";
			}

			//Consulto la información del medico
			$rowsMed = consultarInformacionMedico( $conex, $wbasedato, $rowsCTC[ 'Ctcmed' ] );

			$nombreMedico = $rowsMed[ 'Medno1' ]." ".$rowsMed[ 'Medno2' ]." ".$rowsMed[ 'Medap1' ]." ".$rowsMed[ 'Medap2' ];
			$especialidadMedico = $rowsMed[ 'Espnom' ];
			$nroDocumentoMedico = $rowsMed[ 'Meddoc' ];
			$registroMedico = $rowsMed[ 'Medreg' ];

			dimensionesImagen($rowmed['Meddoc']);

			if(file_exists("../../images/medical/hce/Firmas/{$rowsCTC[ 'Ctcmed' ]}.png"))
				$firmaMedico = "<img src='../../images/medical/hce/Firmas/{$rowsCTC[ 'Ctcmed' ]}.png' width='$anchoimagen' heigth='$altoimagen'>";	//$infoMedico[ 'Firma' ]; //*****Aun no se sabe la tabla de firma
			else
				$firmaMedico = "";

			echo "<div id='dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcart' ]}'>";
			echo "</div>";
			echo "<script>";
			echo "consultarPrescripcionCTC( '{$rowsCTC[ 'Ctchis' ]}', '{$rowsCTC[ 'Ctcing' ]}', '{$rowsCTC[ 'Ctcart' ]}', 'dv{$rowsCTC[ 'Ctchis' ]}_{$rowsCTC[ 'Ctcing' ]}_{$rowsCTC[ 'Ctcart' ]}', '$nroDocumentoMedico', '{$rowsCTC[ 'Ctcfkx' ]}', 'ctcArtNoPos' );";
			echo "</script>";

			/************************************************************************************************
			 * Busco todos los datos necesarios antes de la impresión
			 ************************************************************************************************/

			list( $anoSolicitud, $mesolicitud, $diaSolicitud ) = explode( "-", $rowsCTC[ 'fecCTC' ] );	//$rowsCTC[ 'Ctcfso' ] );


			//Consultando la entidad promotora de salud
			$entidadPromotoraSalud = $rowsCTC[ '' ];



			$tipoUsuario = $rowsCTC[ 'Ctctus' ];
			$noAfiliacion = $rowsCTC[ 'Ctcnoa' ];
			$tipoSolicitud = $rowsCTC[ 'Ctctso' ];

			/********************************************************************************
			 * Tipo de atención (hospitalario, ambulatorio, hospitalario urgente)
			 ********************************************************************************/
			$hospitalario = "";
			$ambulatorio = "";
			$hospitalarioUrgente = "";

			switch( $rowsCTC[ 'Ctctat' ] ){

				case 'HOSPITALARIO':
					$hospitalario = 'X';
					break;

				case 'AMBULATORIO':
					$ambulatorio = "X";
					break;

				case 'HOSPITALARIOURGENTE':
					$hospitalarioUrgente = "X";
					break;

				default: break;
			}

			$nombrePaciente = $rowsCTC[ 'Pacno1' ]." ".$rowsCTC[ 'Pacno2' ];
			$apellidosPaciente = $rowsCTC[ 'Pacap1' ]." ".$rowsCTC[ 'Pacap2' ];

			$nroDocumento =  $rowsCTC[ 'Pactid' ]." ".$rowsCTC[ 'Pacced' ];
			$historia = $rowsCTC[ 'Ctchis' ];
			$ingreso = $rowsCTC[ 'Ctcing' ];

			$edad = calcularEdad( $rowsCTC[ 'Pacnac' ] );
			$cama = $rowsCTC[ 'Ubihac' ];
			$diagnosticoPaciente = $rowsCTC[ 'Ctcdgn' ];
			
			//Consulto los diagnosticos del paciente			
			$diagnosticos = strip_tags( consultarDatosTablaHCE( $conex, $wemp_pmla, $whce, $historia, $ingreso ) );
			
			//Si el diagnostico
			if(trim($diagnosticoPaciente) != ''){
			
				$diagnosticos = $diagnosticoPaciente; 
			}

			//Enfermedad de alto riesgo? es booleano
			if( $rowsCTC[ 'Ctcear' ] == 'on' ){
				$enfermedadAltoRiesgoSi = "X";
				$enfermedadAltoRiesgoNo = "";
			}
			else{
				$enfermedadAltoRiesgoSi = "";
				$enfermedadAltoRiesgoNo = "X";
			}

			$unidadSolicitaMedicamento = $rowsCTC[ 'Ubisac' ]." - ".$rowsCTC[ 'Cconom' ];
			//$descripcionCasoClinico = consultarDatosTablaHCE( $conex, $whce, "000051", 4, $historia, $ingreso );;
			$descripcionCasoClinico = utf8_decode($rowsCTC[ 'Ctcdcc' ]);			
			$observacionesRespuestaClinicaPos = htmlentities( $rowsCTC[ 'Ctcorp' ] );
			$principioActivoPos = htmlentities( $rowsCTC[ 'Ctcpap' ] );

			$posologiaPos = htmlentities( $rowsCTC[ 'Ctcpop' ] );
			$presentacionPos = htmlentities( $rowsCTC[ 'Ctcprp' ] );
			$dosisDiaPos = htmlentities( $rowsCTC[ 'Ctcddp' ] );
			$cantidadPos = htmlentities( $rowsCTC[ 'Ctccap' ] );

			$tiempoTratamientoPos = htmlentities( $rowsCTC[ 'Ctcttp' ] );
			$principioActivoAlternativa = htmlentities( $rowsCTC[ 'Ctcpaa' ] );
			$posologiaAlternativa = htmlentities( $rowsCTC[ 'Ctcpoa' ] );
			$presentacionAlternativa = htmlentities( $rowsCTC[ 'Ctcpra' ] );
			$dosisDiaAlternativa = htmlentities( $rowsCTC[ 'Ctcdda' ] );
			$cantidadAlternativa = $rowsCTC[ 'Ctccaa' ];
			$tiempoTratamientoAlternativa = htmlentities( $rowsCTC[ 'Ctctta' ] );

			//Respuesta clinica Pos
			$noMejoria = "";
			$reaccionAdversa = "";
			$intolerancia = "";
			$noAplica = "";

			switch( $rowsCTC[ 'Ctcrcp' ] ){

				case 'NO MEJORIA':
					$noMejoria = "X";
					break;

				case 'REACCION ADVERSA':
					$reaccionAdversa = "X";
					break;

				case 'INTOLERANCIA':
					$intolerancia = "X";
					break;

				default:
					$noAplica = "X";
					break;
			}

			//Existe riesgo, campo booleano
			if( $rowsCTC[ 'Ctcerp' ] == "on" ){
				$existeRiesgoPosSi = "X";
				$existeRiesgoPosNo = "";
			}
			else{
				$existeRiesgoPosSi = "";
				$existeRiesgoPosNo = "X";
			}

			$empresaEquivalente = consultarEmpresaConEquivalencia( $conex, $wemp_pmla, $wbasedato, $historia, $rowsCTC[ 'Ctcing' ] );

			// var_dump($empresaEquivalente);
			if($empresaEquivalente == true)
			{
				$reemplazarMedCTC = consultarMedicamentoEquivalenteCTC( $wbasedato, $art );	
				// var_dump($reemplazarMedCTC);
			}
			// -------------
			
			if(count($reemplazarMedCTC) > 0)
			{
				$principioActivoNoPos =  htmlentities( $reemplazarMedCTC['Artgen'] );
				$posologiaNoPos =  htmlentities( $rowsCTC[ 'Ctcpon' ] )." ".$rowsCTC[ 'Kadufr' ]; // No cambia
				$presentacionNoPos =  htmlentities( $reemplazarMedCTC[ 'Unides' ] );
				$dosisDiaNoPos =  htmlentities( $rowsCTC[ 'Ctcddn' ] )." ".$rowsCTC[ 'Kadufr' ]; // No cambia
				$tiempoTratamientoNoPos =  htmlentities( $rowsCTC[ 'Ctcttn' ] ); // No cambia
				$cantidadTotalNoPos = ceil($rowsCTC[ 'Ctccan' ]*$reemplazarMedCTC[ 'Areceq' ]);
				$nombreComercialNoPos = $reemplazarMedCTC[ 'Artcom' ];

				$categoriaNoPos = consultarFormasFarmaceuticas( $conex, $wbasedato, $reemplazarMedCTC[ 'Artfar' ] );
				$registroInvimaNoPos = $reemplazarMedCTC['Artreg'];
				$codigoCUM = consultarCodigoCUM( $conex, $wemp_pmla, $reemplazarMedCTC['Areaeq']);
			}
			else
			{
				$principioActivoNoPos =  htmlentities( $rowsCTC[ 'Ctcpan' ] );
				$posologiaNoPos =  htmlentities( $rowsCTC[ 'Ctcpon' ] )." ".$rowsCTC[ 'Kadufr' ];
				$presentacionNoPos =  htmlentities( $rowsCTC[ 'Ctcprn' ] );
				$dosisDiaNoPos =  htmlentities( $rowsCTC[ 'Ctcddn' ] )." ".$rowsCTC[ 'Kadufr' ];;
				$tiempoTratamientoNoPos =  htmlentities( $rowsCTC[ 'Ctcttn' ] );
				$cantidadTotalNoPos = $rowsCTC[ 'Ctccan' ];
				$nombreComercialNoPos = $rowsCTC[ 'Artcom' ];

				$categoriaNoPos = $rowsCTC[ 'Ctccfn' ];
				$registroInvimaNoPos = $rowsCTC[ 'Ctcrin' ];
				$codigoCUM = consultarCodigoCUM( $conex, $wemp_pmla, $art );
			}
			
			// $principioActivoNoPos =  htmlentities( $rowsCTC[ 'Ctcpan' ] );
			// $posologiaNoPos =  htmlentities( $rowsCTC[ 'Ctcpon' ] )." ".$rowsCTC[ 'Kadufr' ];
			// $presentacionNoPos =  htmlentities( $rowsCTC[ 'Ctcprn' ] );
			// $dosisDiaNoPos =  htmlentities( $rowsCTC[ 'Ctcddn' ] )." ".$rowsCTC[ 'Kadufr' ];;
			// $tiempoTratamientoNoPos =  htmlentities( $rowsCTC[ 'Ctcttn' ] );
			// $cantidadTotalNoPos = $rowsCTC[ 'Ctccan' ];
			// $nombreComercialNoPos = $rowsCTC[ 'Artcom' ];

			// $categoriaNoPos = $rowsCTC[ 'Ctccfn' ];
			// $registroInvimaNoPos = $rowsCTC[ 'Ctcrin' ];
			// $codigoCUM = consultarCodigoCUM( $conex, $wemp_pmla, $art );
			// -------------
			$efectoTerapeuticoDeseadoNoPos =  htmlentities( $rowsCTC[ 'Ctcedt' ] );
			$tiempoEsperadoNoPos =  htmlentities( $rowsCTC[ 'Ctctre' ] );
			$efectosSecundariosNoPos =  htmlentities( $rowsCTC[ 'Ctcert' ] );
			$grupoTerapeuticoReemplazo = $rowsCTC[ 'Ctcgte' ];

			$principioActivoReemplazo = $rowsCTC[ 'Ctcpar' ];

			$presentacionReemplazo = $rowsCTC[ 'Ctcprr' ];
			$dosisDiaReemplazo = $rowsCTC[ 'Ctcddr' ];
			$tiempoRespuestaReemplazo =  htmlentities( $rowsCTC[ 'Ctcttr' ] );
			$bibliografia =  htmlentities( $rowsCTC[ 'Ctcbbo' ] );
			$observacionesExisteRiesgoPos =  htmlentities( $rowsCTC[ 'Ctcoer' ] );

			/************************************************************************************************/
			/****************************************************************************************************
			 * Impresion del formulario
			 ****************************************************************************************************/

			?>

			<table width="712" border="1" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="708" height="70"><table width="709" height="48" border="0" cellpadding="0" cellspacing="0">
				  <tr>
					<td width="148"><img src='../../images/medical/root/<?php echo $institucion->baseDeDatos; ?>.jpg' width=148 heigth=53></td>
					<!--<td width="561" align="center"><b>JUSTIFICACIÓN DE MEDICAMENTOS NO INCLUIDOS EN EL PLAN OBLIGATORIO DE SALUD<br>SEGÚN RESOLUCIÓN 5521 DE 2013 - RESOLUCIÓN 5395 DE 2013</p>
					  <p>CNSSS - MINISTRO DE SALUD</b></td>-->
					<!--<td width="561" align="center"><b>JUSTIFICACIÓN DE USO DE TECNOLOGÍAS EN SALUD, NO INCLUIDOS EN EL PLAN OBLIGATORIO DE SALUD<br>SEGÚN RESOLUCION 5592 DE 2015</p>
					  <p>CNSS - MINISTERIO DE SALUD Y PROTECCIÓN SOCIAL</b></td>-->
					<td width="561" align="center"><b>JUSTIFICACIÓN DE USO DE TECNOLOGÍAS EN SALUD, NO INCLUIDOS EN EL PLAN DE BENIFICIOS EN SALUD<br>SEGÚN RESOLUCION 5269 DE 2017</p>
					  <p>MINISTERIO DE SALUD Y PROTECCIÓN SOCIAL</b></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td colspan="3"><div align="center">FECHA</div></td>
					<td rowspan="2"><div align="center">ENTIDAD PROMOTORA DE SALUD</div></td>
				  </tr>
				  <tr>
					<td><div align="center">DIA</div></td>
					<td><div align="center">MES</div></td>
					<td><div align="center">AÑO</div></td>
					</tr>
				  <tr>
					<td align="center"><b><?php echo $diaSolicitud;?></b></td>
					<td align="center"><b><?php echo $mesolicitud;?></b></td>
					<td align="center"><b><?php echo $anoSolicitud;?></b></td>
					<td>&nbsp;<b><?php echo $entidadPromotoraSalud;?></b></td>
				  </tr>

				  <tr>
					<td colspan="4"><div align="center">

					  <table width="661" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="90">TIPO USUARIO</td>
						  <td width="199" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $tipoUsuario;?></b></td>
						  <td width="100">No. AFILIACION</td>
						  <td width="100" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $noAfiliacion;?></b></td>
						  <td width="120">TIPO DE SOLICITUD</td>
						  <td width="58" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $tipoSolicitud;?></b></td>
						</tr>
					  </table>
					  <br>
					  <table width="683" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="124">HOSPITALARIO</td>
						  <td width="60" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $hospitalario;?></b></td>
						  <td width="122">AMBULATORIO</td>
						  <td width="64" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $ambulatorio;?></b></td>
						  <td width="193">HOSPITALARIO URGENTE</td>
						  <td width="106" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $hospitalarioUrgente;?></b></td>
						</tr>
					  </table>
					</div></td>
					</tr>
				</table></td>
			  </tr>
			  <tr>
				<td ><div align="center"><b>DATOS DEL PACIENTE</b></div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td colspan="4">NOMBRE DEL PACIENTE</td>
					<td width="227" rowspan="7">&nbsp;</td>
				  </tr>
				  <tr>
					<td colspan="4">&nbsp;<b><?php echo $nombrePaciente;?></b></td>
					</tr>
				  <tr>
					<td colspan="4">APELLIDOS DEL PACIENTE</td>
					</tr>
				  <tr>
					<td colspan="4">&nbsp;<b><?php echo $apellidosPaciente;?></b></td>
					</tr>
				  <tr>
					<td width="82"><div align="center">EDAD</div></td>
					<td width="132"><div align="center">IDENTIFICACION</div></td>
					<td width="161"><div align="center">HISTORIA CLINICA</div></td>
					<td width="94"><div align="center">CAMA</div></td>
					</tr>
				  <tr>
					<td align="center">&nbsp;<b><?php echo $edad;?></b></td>
					<td align="center">&nbsp;<b><?php echo $nroDocumento;?></b></td>
					<td align="center">&nbsp;<b><?php echo $historia."-".$ingreso;?></b></td>
					<td align="center">&nbsp;<b><?php echo $cama;?></b></td>
					</tr>
				  <tr>
					<td colspan="4">DIAGNOSTICOS
						<BR><b><?php echo $diagnosticos;?></b><BR>
					  </td>
					</tr>
				  <tr>
					<td colspan="2">ENFERMEDAD DE ALTO COSTO</td>
					<td colspan="3">UNIDAD QUE SOLICITA EL MEDICAMENTO</td>
				  </tr>
				  <tr>
					<td colspan="2"><table width="200" border="0" cellspacing="0" cellpadding="0">
						<tr>
						  <td width="20">SI</td>
						  <td width="73" style="border-bottom:solid 1px;"><b><?php echo $enfermedadAltoRiesgoSi;?></b></td>
						  <td width="33">NO</td>
						  <td width="73" style="border-bottom:solid 1px;"><b><?php echo $enfermedadAltoRiesgoNo;?></b></td>
						</tr>
					  </table>
					</td>
					<td colspan="3">&nbsp;<b><?php echo $unidadSolicitaMedicamento;?></b></td>
				  </tr>
				  <tr>
					<td colspan="5">DESCRIPCION DEL CASO CLINICO
					<BR><b><?php echo $descripcionCasoClinico;?></b><BR>
					  </td>
					</tr>
				</table></td>
			  </tr>
			  <tr>
				<td><div align="center"><b>MEDICAMENTOS POS PREVIAMENTE UTILIZADOS</b></div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td>PRINCIPIO ACTIVO: <b><?php echo $principioActivoPos;?></b></td>
					<td>POSOLOGIA: <b><?php echo $posologiaPos;?></b></td>
					<td>PRESENTACION: <b><?php echo $presentacionPos;?></b></td>
				  </tr>
				  <tr>
					<td>DOSIS/DIA: <b><?php echo $dosisDiaPos;?></b></td>
					<td>CANTIDAD: <b><?php echo $cantidadPos;?></b></td>
					<td>TIEMPO TRATAMIENTO: <b><?php echo $tiempoTratamientoPos;?></b></td>
				  </tr>

				</table></td>
			  </tr>
			  <tr>
				<td><div align="center"><b>NO EXISTEN ALTERNATIVAS EN EL POS</b></div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td>PRINCIPIO ACTIVO: <b><?php echo $principioActivoAlternativa;?></b></td>
					<td>POSOLOGIA: <b><?php echo $posologiaAlternativa;?></b></td>
					<td>PRESENTACION: <b><?php echo $presentacionAlternativa;?></b></td>
				  </tr>
				  <tr>
					<td>DOSIS/DIA: <b><?php echo $dosisDiaAlternativa;?></b></td>
					<td>CANTIDAD: <b><?php echo $cantidadAlternativa;?></b></td>
					<td>TIEMPO TRATAMIENTO: <b><?php echo $tiempoTratamientoAlternativa;?></b></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td>&nbsp;</td>
			  </tr>
			  <tr>
				<td><b>RESPUESTA CLINICA Y PARACLINICA ALCANZADA CON MEDICAMENTOS POS</b></td>
			  </tr>
			  <tr>
				<td><div align="center">
					<table width="678" border="0" cellspacing="0" cellpadding="0">
					  <tr>
						<td width="77">No mejoría</td>
						<td width="73" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $noMejoria;?></b></td>
						<td width="124">Reacción adversa</td>
						<td width="99" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $reaccionAdversa;?></b></td>
						<td width="84">Intolerancia</td>
						<td width="68" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $intolerancia;?></b></td>
						<td width="68">No aplica</td>
						<td width="67" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $noAplica;?></b></td>
					  </tr>
						  </table>
				  </div>
				  <BR>Observaciones
				  <BR><b><?php echo $observacionesRespuestaClinicaPos;?></b><BR>
				</td>
			  </tr>
			  <tr>
				<td><table width="712" border="0" cellspacing="0" cellpadding="0">
					<tr>
					  <td width="533">EXISTE RIESGO INMINENTE PARA LA SALUD Y LA VIDA DEL PACIENTE:</td>
					  <td width="29">SI</td>
					  <td width="56" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $existeRiesgoPosSi;?></b></td>
					  <td width="36">NO</td>
					  <td width="59" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $existeRiesgoPosNo;?></b></td>
					</tr>
				  </table>
				  </td>
			  </tr>
			  <tr>
				<td>&nbsp;<b><?php echo $observacionesExisteRiesgoPos;?></b></td>
			  </tr>

			  <tr>
				<td>&nbsp;</td>
			  </tr>
			<!--- </table>

			<div style='page-Break-After:always'></div>

			<table width="712" border="1" cellspacing="0" cellpadding="0"> -->
			  <tr>
				<td><div align="center">MEDICAMENTO NO POS SOLICITADO</div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td>PRINCIPIO ACTIVO</td>
					<td>POSOLOGIA</td>
					<td colspan='2'>PRESENTACION Y FORMA FARMACEUTICA</td>
				  </tr>
				  <tr>
					<td>&nbsp;<b><?php echo $principioActivoNoPos;?></b></td>
					<td>&nbsp;<b><?php echo $posologiaNoPos?></b></td>
					<td colspan='2'>&nbsp;<b><?php echo $principioActivoNoPos; //echo $presentacionNoPos;?></b></td>
				  </tr>
				  <tr>
					<td>DOSIS/DIA: <b><?php echo $dosisDiaNoPos;?></b></td>
					<td>TIEMPO DE TRATAMIENTO: <b><?php echo $tiempoTratamientoNoPos;?> días</b></td>
					<td colspan='2'>CANTIDAD TOTAL: <b><?php echo $cantidadTotalNoPos;?></b></td>
				  </tr>
				  <tr>
					<td>NOMBRE COMERCIAL</td>
					<td>CATEGORIA FARMACEUTICA</td>
					<td>REGISTRO INVIMA</td>
					<td>CUM</td>
				  </tr>
				  <tr>
					<td>&nbsp;<b><?php echo $nombreComercialNoPos;?></b></td>
					<td>&nbsp;<b><?php //echo $categoriaNoPos;?></b></td>
					<td>&nbsp;<b><?php echo $registroInvimaNoPos?></b></td>
					<td>&nbsp;<b><?php echo $codigoCUM;?></b></td>
				  </tr>
				  <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td><div align="center">INDICACIONES TERAPEÚTICAS CON MEDICAMENTOS NO POS:</div></td>
			  </tr>
			  <tr>
				<td>EFECTO TERAPEÚTICO DESEADO AL TRATAMIENTO:
				  <br>&nbsp;<b><?php echo $efectoTerapeuticoDeseadoNoPos;?></b>
				</td>
			  </tr>
			  <tr>
				<td>TIEMPO DE RESPUESTA ESPERADO:
				  <br>&nbsp;<b><?php echo $tiempoEsperadoNoPos;?></b>
				</td>
			  </tr>
			  <tr>
				<td>EFECTOS SECUNDARIOS Y POSIBLES RIESGOS AL TRATAMIENTO:
				<br>&nbsp;<b><?php echo $efectosSecundariosNoPos;?></b>
				</td>
			  </tr>
			  <tr>
				<td>MEDICAMENTOS EN EL PLAN OBLIGATORIO DE SALUD DEL MISMO GRUPO TERAPEUTICO QUE REEMPLAZA O SUSTITUYE EL MEDICAMENTO NO POS SOLICITADO</td>
			  </tr>
			  <tr>
				<td>
					<br>
				  <table width="588" border="0" cellspacing="0" cellpadding="0" align=center>
					<tr>
					  <td width="128">Grupo terapeútico:</td>
					  <td width="140" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $grupoTerapeuticoReemplazo;?></b></td>
					  <td width="117">Principio activo:</td>
					  <td width="203" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $principioActivoReemplazo;?></b></td>
					</tr>
				  </table>
				  <br>
				  <table width="692" border="0" cellspacing="0" cellpadding="0" align=center>
					<tr>
					  <td width="96">Presentacion</td>
					  <td width="134" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $presentacionReemplazo;?></b></td>
					  <td width="73">Dosis día</td>
					  <td width="132" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $dosisDiaReemplazo;?></b></td>
					  <td width="140">Tiempo de tratamiento</td>
					  <td width="117" style="border-bottom:solid 1px;">&nbsp;<b><?php echo $tiempoRespuestaReemplazo;?></b></td>
					</tr>
				  </table>
				  <br>
				</td>
			  </tr>
			  <tr>
				<td>BIBLIOGRAFIA
				<br>
				<b><?php echo $bibliografia;?></b>
				<br>
				</td>
			  </tr>
			  <tr>
				<td><div align="center">MEDICO TRATANTE</div></td>
			  </tr>
			  <tr>
				<td><table width="712" border="1" cellspacing="0" cellpadding="0" style="border-top:hidden;border-left:hidden;border-right:hidden;border-bottom:hidden">
				  <tr>
					<td><div align="center">NOMBRE COMPLETO</div></td>
					<td><div align="center">ESPECIALIDAD</div></td>
					<td><div align="center">No. CÉDULA</div></td>
					<td><div align="center">REGISTRO MEDICO</div></td>
				  </tr>
				  <tr>
					<td>&nbsp;<b><?php echo $nombreMedico;?></b></td>
					<td align="center">&nbsp;<b><?php echo $especialidadMedico;?></b></td>
					<td align="center">&nbsp;<b><?php echo $nroDocumentoMedico;?></b></td>
					<td align="center">&nbsp;<b><?php echo $registroMedico;?></b></td>
				  </tr>
				</table></td>
			  </tr>
			  <tr>
				<td><p>FIRMA Y SELLO</p>
				&nbsp;<b><?php echo $firmaMedico;?></b></td>
			  </tr>
			</table>
			<table style="border: 0px; width: 740px;" cellpadding="2" cellspacing="2">
			  <tbody>
				<tr>
				  <td style="text-align:center;" calss="descripcion">
					<b>- Firmado electrónicamente -</b>
				  </td>
				</tr>
			  </tbody>
			</table>
			<?php

			/****************************************************************************************************/

			if($vista_previa == 'off'){
			//marco como impreso el articulo
			cambiarEsadoImpresionPorId( $conex, $wbasedato, $rowsCTC[ 'id' ], $wusuario );

			}
		}

			echo "</div>";
		echo "</div>";
	}

	
	echo "	<div id='msjEspere' style='display:none;' align='center'>
				<br><br><br>
				<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...
			</div>";
	
	echo "<br>";
	echo "<br>";
	echo "<center>";
	echo "<table>";
		echo "<tr>";
			echo "<td>";
			echo "<input type='button' onclick='cerrarVentana();' value=Retornar>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "</center>";
	echo "</form>";
}
?>
</body>
</html>