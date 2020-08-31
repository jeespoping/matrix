<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	monitor_cenimp.php
 * Fecha		:	2013-08-12
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Programa para administrar las solicitudes de impresion
 * Condiciones  :   
 *********************************************************************************************************
 
 Actualizaciones:
	2014-01-21 (Frederick Aguirre S) Se agrega la opcion de descargar el archivo, Se verifica si fue anulado antes de generar impresion, Se mejoran los querys de consulta.
	2014-01-15 (Frederick Aguirre S) Se cambia el query para consultar solicitudes realizadas porque estaba ralentizando el programa.
			
 **********************************************************************************************************/
 
$wactualiz = "2014-01-21";
 
if(!isset($_SESSION['user'])){
	echo "error";
	return;
}

if( isset($_REQUEST['action'] ) && $_REQUEST['action'] == "descargar" ){
	header("Content-Type: application/octet-stream");
}else{
	//Para que las respuestas ajax acepten tildes y caracteres especiales
	header('Content-type: text/html;charset=ISO-8859-1');
}
//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>Centro impresion</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/toJson.js" type="text/javascript"></script>';
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("hce/HCE_print_function.php");
require_once('root/tcpdf/tcpdf.php');



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenimp");
$whcebasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wmovhosbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wtiempomonitor = consultarAliasPorAplicacion($conex, $wemp_pmla, "time_monitor_cenimp");
$wmaximoHojas = consultarAliasPorAplicacion($conex, $wemp_pmla, "maximo_hojas_cenimp");

$pos = strpos($user,"-");
$wuser = substr($user,$pos+1,strlen($user)); 
DEFINE( "ORIGEN","Solicitudes" );
DEFINE( "LOG","000007" );

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "imprimioSolicitud" ){
		marcarImprimirSolicitud( $_REQUEST['solicitud'] );
	}else if( $action == 'consultarultimas' ){
		consultarUltimasImpresiones();
	}else if( $action == 'consultarultimasfechas' ){
		consultarUltimasImpresiones( $_REQUEST['fecha_i'], $_REQUEST['fecha_f'] );
	}else if( $action == 'elegirmonitor' ){
		vistaInicial();
	}else if( $action == 'agruparsolicitudes' ){
		agruparSolicitudes($_REQUEST['solicitudes'], $_REQUEST['usuarios'], $_REQUEST['paquetes']);
	}else if( $action == 'concatenarPdf' ){
		concatenarPDF( $_REQUEST['solicitud'], $_REQUEST['paquetes'] );		
	}else if( $action == 'quitarSolicitud' ){
		quitarSolicitud( $_REQUEST['solicitud'] );
	}else if( $action == "descargar" ){
		descargarPDF( $_REQUEST['solicitud'], @$_REQUEST['paquetes'] );
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************
	function insertLog( $conex, $wcenimp, $user_session, $accion, $tabla, $err, $descripcion, $identificacion, $sql_error = "", $wmodalidad ){
		$descripcion = str_replace("'",'"',$descripcion);
		$sql_error = ereg_replace('([ ]+)',' ',$sql_error);

		$insert = " INSERT INTO ".$wcenimp."_".LOG."
						(Medico, Fecha_data, Hora_data, logori, Logcdu, Logmod, Logacc, Logtab, Logerr, Logsqe, Logdes, Loguse, Logest, Seguridad)
					VALUES
						('".$wcenimp."','".date("Y-m-d")."','".date("H:i:s")."', '".ORIGEN."', '".utf8_decode($identificacion)."', '".$wmodalidad."', '".utf8_decode($accion)."','".$tabla."','".$err."', '".$sql_error."','".$descripcion."','".$user_session."','on','C-".$user_session."')";

		$res = mysql_query($insert,$conex) or die("Error: " . mysql_errno() . " - en el query (Insertar En Log): " . $insert . " - " . mysql_error());
	}
	
	function descargarPDF($wsolicitud,$wpaquetes=''){
		global $wemp_pmla;
		global $wbasedato;
		global $conex;
		global $wuser;
		
		
		if( $wpaquetes != '' ){
			concatenarPDF( $wsolicitud, $wpaquetes, 'on' );
		}

		$file = "../reportes/cenimp/".$wemp_pmla."Solicitud_".$wsolicitud.".pdf";
		header("Content-Disposition: attachment; filename=Solicitud_".$wsolicitud.".pdf");   
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");
		header("Content-Length: " . filesize($file));
		flush(); // this doesn't really matter.
		$fp = fopen($file, "r");
		while (!feof($fp))
		{
			echo fread($fp, 65536);
			flush(); // this is essential for large downloads
		}
		fclose($fp);
		
		$q= " UPDATE ".$wbasedato."_000005 SET Solter='on' WHERE id='".$wsolicitud."' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$accion 	    = "Descargar";
		$tabla		    = "";
		$descripcion    = "DESCARGADO";
		$identificacion = $wsolicitud;
		insertLog( $conex, $wbasedato, $wuser, $accion, $tabla, "", $descripcion, $identificacion, "", "" );		
	}
	
	function quitarSolicitud( $wsolicitud ){
		global $conex;
		global $wbasedato;
		
		$q= " UPDATE ".$wbasedato."_000005 SET Solest='off' WHERE id='".$wsolicitud."' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		echo "OK";
	}
	
	function getEstadoSolicitud($wsolicitud){
		global $wbasedato;
		global $conex;
		
		$west = "on";
		$query = " SELECT Solest
					 FROM ".$wbasedato."_000005 
					WHERE id ='".$wsolicitud."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0){
			$row = mysql_fetch_array( $err );
			$west = $row[0];			
		}
		return $west;
	}
	
	function concatenarPDF( $wsolicitud, $wpaquetes, $descargar='off' ){
		global $wemp_pmla;
		global $wbasedato;
		
		//2014-01-16 Verificar el estado de la solicitud
		$westadoSol = getEstadoSolicitud( $wsolicitud );
		if( $westadoSol == "off" ){
			echo "La solicitud ya fue anulada";
			return;
		}
	
		$wcadenaUnir = "";
		$wpaquetes = explode("|", $wpaquetes);
		$kk=0;
		foreach( $wpaquetes as $paquet ){
			$nombrePDF = "".$wemp_pmla."Solicitud_".$wsolicitud."P".$paquet.".pdf";
			if(file_exists("../reportes/cenimp/".$nombrePDF)){
				if( $kk > 0 ) $wcadenaUnir.= " ";
				$wcadenaUnir.= $nombrePDF;
				$kk++;
			}
		}	
		$respuesta = shell_exec( "./unirPdf.sh '".$wcadenaUnir."' '".$wemp_pmla."Solicitud_".$wsolicitud.".pdf'" );
		if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$wsolicitud.".pdf")){
			if( $descargar != 'on' )
				marcarImprimirSolicitud( $wsolicitud );			
			//ELIMINAR TODOS LOS ARCHIVOS QUE FUERON PARTE DE ESTA SOLICITUD?
		}else{
			echo "Error al generar PDF de la solicitud ".$wsolicitud;
		}
	}
	
	function marcarImprimirSolicitud( $wsolicitud, $wEchoOK = true ){
		global $conex;
		global $wbasedato;
		global $whcebasedato;
		
		$westadoSol = getEstadoSolicitud( $wsolicitud );
		if( $westadoSol == "off" && $wEchoOK == true ){
			echo "ANULADO";
			return;
		}

		$q= " INSERT ".$wbasedato."_000006 (   Medico       ,   fecha_data,   		hora_data,    		Impsol      , impest,     Seguridad  ) "
						 ."         VALUES ('".$wbasedato."','".date('Y-m-d')."','".date('H:i:s')."','".$wsolicitud."', 'on', 'C-".$wbasedato."') ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		if( $wEchoOK == true ){
			if( mysql_insert_id() ){
				echo "OK";
			}
		}
		
		$q= " UPDATE ".$wbasedato."_000005 SET Solter='on' WHERE id='".$wsolicitud."' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}

	function agruparSolicitudes($wsolicitudes, $wusuarios, $wpaquetes){
		global $conex;
		global $wbasedato;
		global $wuser;
		global $whcebasedato;
		global $wemp_pmla;
		
		//El nombre del PDF de la agrupacion sera ".$wemp_pmla."Solicitud_A(codigo del usuario) ej: ".$wemp_pmla."Solicitud_A01234
		$wpdfResultado = "A".$wuser.".pdf";
		
		//Se elimina el pdf ".$wemp_pmla."Solicitud_A01234 si existe, que indica que ya se habia creado una agrupacion
		$dir = '/var/www/matrix/hce/reportes/cenimp';
		$archivo_dir = $dir."/".$wemp_pmla."Solicitud_".$wpdfResultado;
		if(file_exists($archivo_dir))
			unlink($archivo_dir);
		
		$wcadena = "";
		//Los siguientes tres arreglos deben tener la misma cantidad de posiciones
		$wsolicitudes = explode(",", $wsolicitudes);
		$wusuarios = explode(",", $wusuarios);
		$wpaquetes = explode(",", $wpaquetes);
		/*wpaquetes 17|18|0, 17|18|19|0, 17|18|19|0
		wsolicitudes	408, 409, 411
		wusuarios	0100174, 08230, 0100174*/
		
		
		//2014-01-16 VERIFICAR EL ESTADO DE LAS SOLICITUDES ANTES DE IMPRIMIR, LAS  QUE HAN SIDO ANULADAS NO SE AGRUPAN
		$indi = 0;
		$wsolicitudesaux = array();
		foreach( $wsolicitudes as $solicix ){
			$westSol = getEstadoSolicitud( $solicix );
			if( $westSol == "off" ){
				unset($wusuarios[$indi]);
				unset($wpaquetes[$indi]);
			}else
				array_push($wsolicitudesaux, $solicix);
			$indi++;
		}
		$wsolicitudes=$wsolicitudesaux;
		//Fin verificar estado
		
		$paquetes_unicos = array();
		
		//wpaquetes quedara por ejemplo asi:  array(0: array(17,18), 1: array(17,18,19), 2: array(17,18,19))
		foreach($wpaquetes as &$paqs ){
			$paqs = explode("|", $paqs );
			foreach($paqs as $wpq ){
				if( in_array("'".$wpq."'", $paquetes_unicos ) == false ){
					array_push($paquetes_unicos, "'".$wpq."'");
				}
			}
		}
		
		$wnombrePaquetes = array();
		$query = " SELECT id as codigo, Paqdes as nombre
					 FROM ".$wbasedato."_000004 
					WHERE id IN (".implode(",", $paquetes_unicos).") ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0){
			while( $row = mysql_fetch_assoc($err) ){
				if( array_key_exists( $row['codigo'], $wnombrePaquetes ) == false ){
					$wnombrePaquetes[ $row['codigo'] ] = array( 'nombre' => "Paquete<br>".$row['nombre'],
																'usuarios' => array());
				}
			}
		}
		$wnombrePaquetes[ 0 ] = array( 'nombre' => "Paquete<br>Formularios individuales",
										'usuarios' => array());

		//Se construye un arreglo con la siguiente estructura: la primera clave indica el codigo del paquetes
		/*array(
				'17': array( 'nombre' => 'paquete POS'
							 'usuarios' 	=> array( 0100174 =>array(408,411),
													  08230 =>array(409)
													)
							),
				'18': array( 'nombre' => 'paquete NO POS'
							 'usuarios' 	=> array( 0100174 =>array(408,411),
													  08230 =>array(409)
													)
							),
				'19': array( 'nombre' => 'paquete Sura'
							 'usuarios' 	=> array( 0100174 =>array(411),
													  08230 =>array(409)
													)
							)
				)*/
		$indice = 0;

		foreach($wpaquetes as $paqsx ){
			foreach( $paqsx as $cod_paquete ){
				if( array_key_exists( $wusuarios[ $indice ], $wnombrePaquetes[$cod_paquete]['usuarios'] ) == false ){
					$wnombrePaquetes[$cod_paquete]['usuarios'][ $wusuarios[ $indice ] ] = array( $wsolicitudes[ $indice ] );
				}else{
					array_push( $wnombrePaquetes[$cod_paquete]['usuarios'][ $wusuarios[ $indice ] ], $wsolicitudes[ $indice ] );
				}
			}
			$indice++;
		}
	//	echo json_encode( $wnombrePaquetes );	return;
		$k = 0;
		$cadena_unir = "";
		
		foreach( $wnombrePaquetes as $cod_paquete=>$wdatos ){
			foreach( $wdatos['usuarios'] as $cod_usuario=>$solicitudes ){
				//crear tapa con $cod_paquete, $wdatos['nombre'], $cod_usuario, implode(",",$solicitudes)
				if( $k>0 )	$cadena_unir.= " ";
				$cadena_sols = "";
				$existeParaPaquete = false;
				foreach( $solicitudes as $soll ){
					if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$soll."P".$cod_paquete.".pdf")){
						$cadena_sols.= " ".$wemp_pmla."Solicitud_".$soll."P".$cod_paquete.".pdf";
						$existeParaPaquete = true;
					}
				}
				if( $existeParaPaquete == true){
					crearTapaUsuario( $conex, $whcebasedato, $cod_paquete, $wdatos['nombre'], $wuser, $cod_usuario, $solicitudes );
					$cadena_unir.= "tapaP".$cod_paquete."_".$cod_usuario."_".$wuser.".pdf".$cadena_sols;
				}
				$k++;
			}
		}
		//echo "./unirPdf.sh '".$cadena_unir."' '".$wemp_pmla."Solicitud_".$wpdfResultado."'";		return;
		//Al SH se le mandan dos parametros, los nombres de los pdf que se quiere juntar y el nombre del pdf resultado
		$respuesta = shell_exec( "./unirPdf.sh '".$cadena_unir."' '".$wemp_pmla."Solicitud_".$wpdfResultado."'" );
		$echoOK = false;
		$dir = '/var/www/matrix/hce/reportes/cenimp';
		//Si existe el pdf de la agrupacion se envia el codigo del pdf generado
		if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$wpdfResultado."")){
			foreach( $wsolicitudes as $wsolicitud ){
				marcarImprimirSolicitud( $wsolicitud, $echoOK); //marcar la solicitud como impresa y no hacer "echo OK"
			}
			foreach( $wnombrePaquetes as $cod_paquetex=>$wdatosx ){
				foreach( $wdatosx['usuarios'] as $cod_usuariox=>$solicit ){
					$tapd = "tapaP".$cod_paquetex."_".$cod_usuariox."_".$wuser.".pdf";
					$archivo_dir = $dir."/".$tapd;
					if(file_exists($archivo_dir))
						unlink($archivo_dir);					
				}
			}
			$wpdfResultado = str_replace(".pdf", "", $wpdfResultado);
			echo $wpdfResultado;
		}else{
			echo "ERROR";
		}
	}
	
	function consultarUltimasImpresiones($wfecha_i='', $wfecha_f=''){
		global $conex;
		global $wbasedato;
		global $whcebasedato;
		global $wmovhosbasedato;
		global $wemp_pmla;
		global $wmonitor;
		global $wemp_pmla;
		
		$q_fechas = "";
		if( $wfecha_i != '' && $wfecha_f != '')
			$q_fechas = " 	AND	I.fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' ";

		if( $wfecha_i == '' )
			$wfecha_i = date('Y-m-d');
		
		if( $wfecha_f == '' )
			$wfecha_f = date('Y-m-d');
		
		$query = " SELECT I.fecha_data as fecha_imp, I.hora_data as hora_imp, A.id as codigo_solicitud, A.Fecha_data as fecha, A.Hora_data as hora, Solhis as historia, Soling as ingreso, Moddes as modalidad, "
				."		  Solpaq as paquetes, Solfpa as forms_paquetes, Solfor as forms_manual, Solfei as fecha_i, Solfef as fecha_f, Modcod as cod_modalidad, "
				."    	  A.Seguridad as cod_user, pacno1, pacno2, pacap1, pacap2, oritid as tipodoc, oriced as doc, ubihac as habitacion, ubiald as alta, "
				."        Solnuf as numero_formularios, Solnuh as numero_hojas, Solgen as generando,  Solord as ordenes, Modref as repiteformularios  "
				. "  FROM ".$wbasedato."_000006 I, ".$wbasedato."_000005 A,  ".$wbasedato."_000001 B,  root_000036, root_000037 , ".$wmovhosbasedato."_000018 "
				. " WHERE Solmon = '".$wmonitor."' "
				. "   AND Solmod = Modcod "
				.$q_fechas
				."    AND Impsol = A.id "
				."    AND orihis = Solhis "
				//."    AND oriing = Soling "
				." 	  AND oriced = pacced "
				."    AND oritid = pactid"
				." 	  AND oriori = '".$wemp_pmla."'"
				."    AND ubihis = Solhis "
				."    AND ubiing = Soling "				
				. "   AND Solest = 'on' "
				."    AND Impest = 'on' "
				. "   AND Solter = 'on' "
				."   ORDER BY Modpri, I.Fecha_data desc, I.Hora_data desc ";
			if( $q_fechas == '')
				$query.=" LIMIT 10 ";
			
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		
		$mensaje="ULTIMAS SOLICITUDES IMPRESAS";
		if( $q_fechas == '')
			$mensaje="ULTIMAS 10 SOLICITUDES IMPRESAS";
			
		$arr_usuarios = array(); //2014-01-15
		$arr_datos = array();
		$imprimir_masivo = "off";
		if($num > 0){
			while( $rowx = mysql_fetch_assoc($err) ){
				if( $rowx['repiteformularios'] == 'on' )
					$imprimir_masivo = 'on';
				$rowx['cod_user'] = str_replace("C-", "", $rowx['cod_user']);
				array_push( $arr_datos, $rowx );

				if( in_array( "'".$rowx['cod_user']."'", $arr_usuarios ) == false ) //Si el codigo del usuario no existe en arr_usuarios se guarda
					array_push( $arr_usuarios, "'".$rowx['cod_user']."'" );
			}
		
			$chain_usuarios = implode(",", $arr_usuarios );
			$q_usuarios = "SELECT  U.codigo as cod_usu, U.descripcion as nom_usu
							 FROM usuarios U
							WHERE codigo IN (".$chain_usuarios.")";
			$err1 = mysql_query($q_usuarios,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_usuarios." - ".mysql_error());
			$num1 = mysql_num_rows($err1);
			if( $num1 > 0 ){
				while( $row1 = mysql_fetch_assoc($err1) ){
					foreach( $arr_datos as $pos=>&$dato ){
						if( $dato['cod_user'] == $row1['cod_usu'] ){
							$dato['cod_usuario']  = $row1['cod_usu'];
							$dato['usuario']  = $row1['nom_usu'];
						}
					}
				}
			}
		}
		
		echo "<table align='center' id='tabla_solicitudes' >";
		echo "<tr class='encabezadotabla'><td align='center' colspan=13><font size=5><b>".$mensaje."</b></font></td></tr>";
		echo "<tr class='encabezadotabla'><td align='center' colspan=13>";
		echo "<label>Desde:</label><input type='text' id='fecha_i' value='".$wfecha_i."' /> &nbsp;&nbsp;&nbsp;";
		echo "<label>Hasta:</label><input type='text' id='fecha_f' value='".$wfecha_f."' /> ";
		echo "&nbsp;<input type='button' id='btn_consultar_fechas' value='Consultar' onclick='consultarImpresasFechas()'/> ";
		echo "</td></tr>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Codigo</td>";
		echo "<td align='center'>Modalidad</td>";
		echo "<td align='center'>Fecha<br>Impresion</td>";
		echo "<td align='center'>Hora<br>Impresion</td>";
		echo "<td align='center'>Fecha<br>Solicitud</td>";
		echo "<td align='center'>Hora<br>Solicitud</td>";
		echo "<td align='center'>Habitación</td>";
		echo "<td align='center'>Historia<br>Ingreso</td>";
		echo "<td align='center'>Documento</td>";
		echo "<td align='center'>Paciente</td>";
		echo "<td align='center'>Usuario</td>";
		echo "<td align='center'>Páginas</td>";
		echo "<td align='center'>&nbsp;</td>";
		echo "</tr>";
			
		if($num > 0){
			$ind = 0;
			$indice_generar = 0;
			$class="fila1";
			foreach($arr_datos as $row ){
				
				( ($ind % 2) == 0 ) ? $class='fila1' : $class='fila2';
				
				echo "<tr class='".$class." ocultable' modalidad='".$row['cod_modalidad']."' usuario='".$row['cod_usuario']."'>";				
				echo "<td align='center'>".$row['codigo_solicitud']."</td>";
				echo "<td>".$row['modalidad']."</td>";
				echo "<td>".$row['fecha_imp']."</td>";
				echo "<td>".$row['hora_imp']."</td>";
				echo "<td>".$row['fecha']."</td>";
				echo "<td>".$row['hora']."</td>";
				echo "<td class='parabuscar' align='center'>".$row['habitacion']."</td>";
				echo "<td class='parabuscar'>".$row['historia']."-".$row['ingreso']."</td>";
				echo "<td class='parabuscar'>".$row['tipodoc']." ".$row['doc']."</td>";
				echo "<td class='parabuscar' nowrap>".$row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']."</td>";
				echo "<td>".$row['usuario']."</td>";
				echo "<td align='center'>".$row['numero_hojas']."</td>";
				echo "<td align='center'>";
				if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$row['codigo_solicitud'].".pdf"))
					echo "<input type='button' value='Reimprimir' align='center' onclick='mostrarPDFreimprimir(\"".$row['codigo_solicitud']."\")' />";
				else{
					if( $imprimir_masivo == 'on' ){
						$paquetes = explode( "|", $row['paquetes'] );
						array_push( $paquetes, 0 ); //El paquete de codigo 0, son los formularios que escogió manualmente
						$existe = false;
						foreach( $paquetes as $paq ){
							if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$row['codigo_solicitud']."P".$paq.".pdf")){
								$existe = true;								
								echo "<input type='button' value='Reimprimir' align='center' onclick='concatenarReimprimir(\"".$row['codigo_solicitud']."\")' />";
								break;
							}
						}
						if( $existe == false ){
							echo "PDF Eliminado<br><input type='button' value='Quitar' onclick='quitarSolicitud(this, ".$row['codigo_solicitud'].")' />";
						}
					}else{
						echo "PDF Eliminado<br><input type='button' value='Quitar' onclick='quitarSolicitud(this, ".$row['codigo_solicitud'].")' />";
					}
				}

				echo "</td>";				
				echo "</tr>";		
				$ind++;
			}
			echo "<tr><td align='center' colspan=13><br><input type='button' value='Ocultar' onclick='cerrarUltimasImpresas()'></td></tr>";
			echo "</table>";			

		}else{
			if( $q_fechas != "" )
				echo "<tr><td align='center' colspan=13>No hay impresiones en el rango de fechas elegido</td></tr>";
			else
				echo "<tr><td align='center' colspan=13>No hay impresiones para mostrar</td></tr>";
				
			echo "<tr><td align='center' colspan=13><br><input type='button' value='Ocultar' onclick='cerrarUltimasImpresas()'></td></tr>";
			echo "</table>";
		}
		echo  '<script>
			$("#fecha_i").datepicker({
			 showOn: "button",
			 buttonImage: "../../images/medical/root/calendar.gif",
			 buttonImageOnly: true,
			 maxDate:"'.date('Y-m-d').'"
			});
			$("#fecha_f").datepicker({
			 showOn: "button",
			 buttonImage: "../../images/medical/root/calendar.gif",
			 buttonImageOnly: true,
			 maxDate:"'.date('Y-m-d').'"
			});
		</script>';	
	}
	
	function mostrarSolicitudesSinImprimir(){
		global $conex;
		global $wbasedato;
		global $whcebasedato;
		global $wmovhosbasedato;
		global $wemp_pmla;
		global $wmonitor;
		global $wsolicitudes_marcadas;

		$query = " SELECT A.id as codigo_solicitud, A.Fecha_data as fecha, A.Hora_data as hora, Solhis as historia, Soling as ingreso, Moddes as modalidad, "
				."		  Solpaq as paquetes, Solfpa as forms_paquetes, Solfor as forms_manual, Solfei as fecha_i, Solfef as fecha_f, Modcod as cod_modalidad, "
				."    	  U.codigo as cod_usuario, U.descripcion as usuario, pacno1, pacno2, pacap1, pacap2, oritid as tipodoc, oriced as doc, ubihac as habitacion, ubiald as alta, "
				."        Solnuf as numero_formularios, Solnuh as numero_hojas, Solgen as generando, Solord as ordenes, Modref as repiteformularios "
				. "  FROM ".$wbasedato."_000005 A, ".$wbasedato."_000001 B, usuarios U, root_000036, root_000037, ".$wmovhosbasedato."_000018 "
				. " WHERE Solmon = '".$wmonitor."' "
				. "   AND Solmod = Modcod "
				."    AND orihis = Solhis "
				//."    AND oriing = Soling "
				." 	  AND oriced = pacced "
				."    AND oritid = pactid"
				." 	  AND oriori = '".$wemp_pmla."'"
				."    AND ubihis = Solhis "
				."    AND ubiing = Soling "
				."    AND A.Seguridad = CONCAT('C-', U.codigo)"
				. "   AND Solest = 'on' "
				. "   AND Solter = 'off' "
				."   ORDER BY Modpri, A.Fecha_data, A.Hora_data ";
									
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		if($num > 0){
			$arr_datos = array();
			$imprimir_masivo = "off";
			while( $row = mysql_fetch_assoc($err) ){
				if( $row['repiteformularios'] == 'on' )
					$imprimir_masivo = 'on';
				array_push( $arr_datos, $row );
			}
			echo "<div style='height:50px'>";
			echo "<center><input type='button' class='botonAgrupar' value='Imprimir Seleccionadas' onclick='agruparSolicitudes()' /></center>";
			echo "</div>";	
			
			$ocultarBotonImprimir = false;
			if( trim($wsolicitudes_marcadas) != '' )
				$ocultarBotonImprimir = true;
			
			$wsolicitudes_marcadas = explode(",",$wsolicitudes_marcadas); //las solicitudes que estaba marcando antes de recargar
			
			$colspan = 11;
			if( $imprimir_masivo == 'on' ) $colspan = 12;
			echo "<table align='center' id='tabla_solicitudes'>";
			echo "<tr class='encabezadotabla'><td align='center' colspan=".$colspan."><font size=5><b>LISTA DE SOLICITUDES PENDIENTES</b></font></td></tr>";
			echo "<tr class='encabezadotabla'>";
			if( $imprimir_masivo == 'on' ) 
				echo "<td align='center'>Todos<br><input type='checkbox' onclick='marcarTodosAgrupar(this)' /></td>";
			echo "<td align='center'>Codigo</td>";
			echo "<td align='center'>Modalidad</td>";
			echo "<td align='center'>Fecha</td>";
			echo "<td align='center'>Hora</td>";
			echo "<td align='center'>Habitación</td>";			
			echo "<td align='center'>Historia<br>Ingreso</td>";
			echo "<td align='center'>Documento</td>";
			echo "<td align='center'>Paciente</td>";
			echo "<td align='center'>Usuario</td>";
			echo "<td align='center'>Páginas</td>";
			echo "<td align='center'>&nbsp;</td>";
			echo "</tr>";
			
			$ind = 0;
			$indice_generar = 0;
			$class="fila1";
			foreach($arr_datos as $row ){
			
				$boton_imprimir = "";
				$existebtn = false;
			
				if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$row['codigo_solicitud'].".pdf")){
					$disabled="";
					$class_oculto = "";
					if($ocultarBotonImprimir == true){
						$disabled = "disabled";
						$class_oculto = " deshabilitarporcheckbox";
					}
					$boton_imprimir = " <input type='button' value='Imprimir' align='center' class='botonimprimirsol".$class_oculto."' solicitud='".$row['codigo_solicitud']."' onclick='mostrarPDF(\"".$row['codigo_solicitud']."\", this)' ".$disabled." />&nbsp;					
										<input type='button' value='Descargar' align='center' class='botonimprimirsol".$class_oculto."' solicitud='".$row['codigo_solicitud']."' onclick='mostrarLinkDescarga(\"".$row['codigo_solicitud']."\", this)' ".$disabled." />";						
					$existebtn = true;
				}else{
					if( $imprimir_masivo == 'on' ){						
						$paquetes = explode( "|", $row['paquetes'] );
						//array_push( $paquetes, 0 ); //El paquete de codigo 0, son los formularios que escogió manualmente
						$existe = false;
						foreach( $paquetes as $paq ){
							if(file_exists("../reportes/cenimp/".$wemp_pmla."Solicitud_".$row['codigo_solicitud']."P".$paq.".pdf")){
								$disabled="";
								$class_oculto = "";
								if($ocultarBotonImprimir == true){
									$disabled = "disabled";
									$class_oculto = " deshabilitarporcheckbox";
								}
								$existe = true;
								$boton_imprimir = " <input type='button' value='Imprimir' align='center' class='botonimprimirsol".$class_oculto."' solicitud='".$row['codigo_solicitud']."' onclick='concatenarPDF(\"".$row['codigo_solicitud']."\", \"".$row['paquetes']."\", this)' ".$disabled." />&nbsp;						
													<input type='button' value='Descargar' align='center' class='botonimprimirsol".$class_oculto."' solicitud='".$row['codigo_solicitud']."' onclick='mostrarLinkDescargaConcatenar(\"".$row['codigo_solicitud']."\", \"".$row['paquetes']."\", this)' ".$disabled." />";						
								$existebtn = true;
								break;
							}
						}
						if( $existe == false ){
							$boton_imprimir = "PDF Eliminado<input type='button' value='Quitar' onclick='quitarSolicitud(this, ".$row['codigo_solicitud'].")' />";
						}
					}else{
						$boton_imprimir = "PDF Eliminado<br><input type='button' value='Quitar' onclick='quitarSolicitud(this, ".$row['codigo_solicitud'].")' />";
					}
				}

				if( $row['paquetes'] != '' ) $row['paquetes'] = $row['paquetes']."|0";
				( ($ind % 2) == 0 ) ? $class='fila1' : $class='fila2';
				echo "<tr class='".$class." ocultable' modalidad='".$row['cod_modalidad']."' usuario='".$row['cod_usuario']."'>";
				if( $imprimir_masivo == 'on' ){
					echo "<td align='center'>";
					$checked = "";
					if( in_array($row['codigo_solicitud'], $wsolicitudes_marcadas) )
						$checked = " checked ";
					if( $existebtn == true ) echo "<input type='checkbox' ".$checked." class='check_agrupar' value='".$row['codigo_solicitud']."' usuario='".$row['cod_usuario']."' paquetes='".$row['paquetes']."' hojas='".$row['numero_hojas']."' onclick='marcarSolChecked(this)'/>";
					echo "</td>";
				}
				echo "<td align='center'>".$row['codigo_solicitud']."</td>";
				echo "<td>".$row['modalidad']."</td>";
				echo "<td>".$row['fecha']."</td>";
				echo "<td>".$row['hora']."</td>";
				echo "<td class='parabuscar' align='center'>".$row['habitacion']."</td>";
				echo "<td class='parabuscar'>".$row['historia']."-".$row['ingreso']."</td>";
				echo "<td class='parabuscar'>".$row['tipodoc']." ".$row['doc']."</td>";
				echo "<td class='parabuscar' nowrap>".$row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']."</td>";
				echo "<td>".$row['usuario']."</td>";
				echo "<td align='center'>".$row['numero_hojas']."</td>";
				echo "<td align='center'>";
				echo $boton_imprimir;
				echo "</td>";
				
				echo "</tr>";
				$ind++;
			}
			
			echo "</table>";
			echo "<br>";
			echo "<center><input type='button' class='botonAgrupar'  value='Imprimir Seleccionadas' onclick='agruparSolicitudes()' /></center>";
			echo "<br>";
		}else{
			echo "<center><font size=5>No hay solicitudes pendientes</font></center>";
		}
	}
	
	function mostrarParametrosDeConsulta(){
		global $conex;
		global $wbasedato;
		global $whcebasedato;
		global $wemp_pmla;
		global $wmonitor;
		
		//Tres variables que pueden venir en la peticion
		global $select_modalidades;
		global $select_usuarios;
		global $input_buscar;
		
		$width_sel = " width: 100%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";	
		
		$query = " SELECT Modcod as cod_modalidad, Moddes as modalidad, U.codigo as cod_usuario, U.descripcion as usuario "
				. "  FROM ".$wbasedato."_000005 A, ".$wbasedato."_000001 B, usuarios U, root_000036, root_000037"
				. " WHERE Solmon = '".$wmonitor."' "
				. "   AND Solmod = Modcod "
				."    AND orihis = Solhis "
				//."    AND oriing = Soling "
				." 	  AND oriced = pacced "
				."    AND oritid = pactid "
				." 	  AND oriori = '".$wemp_pmla."'"
				."    AND A.Seguridad = CONCAT('C-', U.codigo)" 
				. "   AND Solest = 'on' "
				. "   AND Solter = 'off' "				
				."    GROUP BY Modcod, U.codigo ";
									
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());

		$modalidades = array();
		$usuarios = array();
		$num = mysql_num_rows($err);
		if($num > 0){
			$wselect_modalidades = "<select id='select_modalidades' onchange='filtrar_modalidad()' style='".$width_sel."'><option value=''>&nbsp;</option>";
			$wselect_usuarios = "<select id='select_usuarios' onchange='filtrar_usuario()' style='".$width_sel."'><option value=''>&nbsp;</option>";
		
			while ( $row = mysql_fetch_assoc($err) ){
				if( in_array( $row['cod_modalidad'], $modalidades ) == false){
					if( isset( $select_modalidades ) && $select_modalidades==$row['cod_modalidad'] )
						$wselect_modalidades.="<option value='".$row['cod_modalidad']."' selected>".$row['modalidad']."</option>";
					else
						$wselect_modalidades.="<option value='".$row['cod_modalidad']."'>".$row['modalidad']."</option>";
						
					array_push($modalidades, $row['cod_modalidad']);
				}
				if( in_array( $row['cod_usuario'], $usuarios ) == false){
					if( isset( $select_usuarios ) && $select_usuarios==$row['cod_usuario'] )
						$wselect_usuarios.="<option value='".$row['cod_usuario']."' selected>".$row['usuario']."</option>";
					else
						$wselect_usuarios.="<option value='".$row['cod_usuario']."'>".$row['usuario']."</option>";
						
					array_push($usuarios, $row['cod_usuario']);
				}
			}
			
			$wselect_usuarios.="</select>";
			$wselect_modalidades.="</select>";
			
			echo "<center><table>";
			if( count( $usuarios ) > 1 ){
				echo "<tr class='encabezadotabla'>";
				echo "<td align='center'>Filtrar por usuario</td>";
				echo "<td class='fila1'>".$wselect_usuarios."</td>";
				echo "</tr>";
			}
			if( count( $modalidades ) > 1 ){
				echo "<tr class='encabezadotabla'>";
				echo "<td align='center'>Filtrar por modalidad</td>";
				echo "<td class='fila1'>".$wselect_modalidades."</td>";
				echo "</tr>";
			}
			echo "<tr class='encabezadotabla'>";
			echo "<td align='center'>Buscar</td>";
			echo "<td class='fila1'><input type='text' id='input_buscar' value='".$input_buscar."' style='".$width_sel."' /></td>";
			echo "</tr>";
			echo "</table></center>";
			
			
			echo "<script>";
			echo " $(document).ready(function() {";
			echo " setTimeout( function(){";
			if( !empty( $input_buscar ) ){
				echo " filtrarConBusqueda('".$input_buscar."');";				
			}else if( !empty( $select_usuarios ) ){
				echo " filtrar_usuario();";				
			}else if( !empty( $select_modalidades ) ){
				echo " filtrar_modalidad();";				
			}
			echo " }, 500);";
			echo " });";
			echo "</script>";
		}	
	}
	
	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		global $wbasedato;
		global $conex;
		global $wmonitor;
		global $wuser;
		global $wconsultandoLista; 	//viene del submit, indica si se estaba consultado
		global $wejey;				//viene del submit, indica la posicion Y donde estaba el usuario
		global $wfeci_consulta;		//viene del submit, si estaba consultando es la fecha inicial de la consulta
		global $wfecf_consulta;		//viene del submit, si estaba consultando es la fecha final de la consulta
		global $wsolicitudes_marcadas; //viene del submit, solicitudes que estaba marcando
		
		if( !isset ( $wejey ) ) $wejey = 0;
		if( !isset ( $wconsultandoLista ) ) $wconsultandoLista = 'off';
		if( !isset ( $wfeci_consulta ) ) $wfeci_consulta = date('Y-m-d');
		if( !isset ( $wfecf_consulta ) ) $wfecf_consulta = date('Y-m-d');
		if( !isset ( $wsolicitudes_marcadas ) ) $wsolicitudes_marcadas = "";
		
		echo "<form name='monitor' action='' method=post>";
		echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'/>";
		echo "<input type='hidden' id='select_modalidades_2' name='select_modalidades' value=''/>";
		echo "<input type='hidden' id='select_usuarios_2' name='select_usuarios' value=''/>";
		echo "<input type='hidden' id='input_buscar_2' name='input_buscar' value=''/>";
		echo "<input type='hidden' id='wejey' name='wejey' value='".$wejey."'/>";
		echo "<input type='hidden' id='wconsultandoLista' name='wconsultandoLista' value='".$wconsultandoLista."'/>";				
		echo "<input type='hidden' id='wfeci_consulta' name='wfeci_consulta' value='".$wfeci_consulta."'/>";				
		echo "<input type='hidden' id='wfecf_consulta' name='wfecf_consulta' value='".$wfecf_consulta."'/>";				
		
		encabezado("MONITOR CENTRO DE IMPRESIONES", $wactualiz, "clinica");
		
		if( $wmonitor == '' ){
			//Buscar el monitor al que el usuario esta asignado
			$query = " SELECT Moncod as codigo, Mondes as descripcion"
					. "  FROM ".$wbasedato."_000002 "
					. " WHERE Monusu like '%".$wuser."%'"
					."    AND Monest = 'on' ORDER BY 1";
										
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num == 1){
				$row = mysql_fetch_assoc($err);
				$wmonitor = $row['codigo'];
			}else if( $num > 1 ){
				echo "<center>";
				echo "<font size=5><b>Por favor seleccione el monitor que desea consultar</b></font><br><br><br><br><br><br>";
				while( $row = mysql_fetch_assoc($err) ){					
					//echo "<a onclick='cargarMonitor(\"".$row['codigo']."\")'><font size=5><b>".$row['codigo']." - ".$row['descripcion']."</b></font></a><br><br>";
					//echo "<input type='button' onclick='cargarMonitor(\"".$row['codigo']."\")' value='".$row['codigo']." - ".$row['descripcion']."' /><br><br>";
					echo '<div class="divClass"  style="display: table; #position: relative; overflow: hidden;">';
					echo '<div style=" #position: absolute; #top: 50%;display: table-cell; vertical-align: middle;">';
					echo "<div onclick='cargarMonitor(\"".$row['codigo']."\")' align='center' ><font size=5><b>".$row['codigo']." - ".$row['descripcion']."</b></font></div>";
					echo "</div>";
					echo "</div><br><br>";
				}
				echo "</center>";
				return;
			}else{
				echo "<center><font size=5><b>El usuario no tiene permisos para utilizar el monitor</b></font></center><br>";
				return;
			}
		}
		
		echo "<input type='hidden' id='wmonitor' name='wmonitor' value='".$wmonitor."'/>";
		//A medida que voy marcando solicitudes, voy actualizando el valor de esta variable oculta
		//Para que cuando recargue el monitor, deje marcadas las solicitudes que habia seleccionado
		echo "<input type='hidden' id='wsolicitudes_marcadas' name='wsolicitudes_marcadas' value='".$wsolicitudes_marcadas."' />";
		echo "</form>";
		
		$query = " SELECT Mondes as nombre "
				. "  FROM ".$wbasedato."_000002"
				. " WHERE Moncod = '".$wmonitor."'";
									
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		
		if($num > 0){
			$row = mysql_fetch_array($err);
			echo "<center><font size=5><b>".$wmonitor." - ".$row[0]."</b></font></center><br>";
		}	
			
		echo "<center>";
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
		echo "</center>";
		echo '<br><br>';

		echo '<div style="width: 100%">';
		
		mostrarParametrosDeConsulta();

		echo "<br><br>"; 		
		//---DIV PRINCIPAL
		
		echo "<center><a id='enlace_consultar_ultimos' href='#' >CONSULTAR ULTIMAS IMPRESIONES</a></center>";
		
		if( $wconsultandoLista == 'off' ){
			echo "<center><div id='lista_ultimas' align='center'>&nbsp;</div></center>";
		}else{
			echo "<center><div id='lista_ultimas' align='center'>";
			consultarUltimasImpresiones($wfeci_consulta, $wfecf_consulta);
			echo "</div></center>";
		}
		
		echo "<br><br>"; 	


		echo '<center><div id="resultados_lista" align="center">';
		mostrarSolicitudesSinImprimir();
		echo '</div></center>';
		
		echo '<center><div id="div_contenedor_pdf" align="center" style="width: auto">';
		echo '</div></center>';
		
		echo "<br><br>";

		//------FIN FORMULARIO------
		echo "</div>";//Gran contenedor
		echo '<center>';
		
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "<br><br>"; 
		echo "<br><br>";
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo '</center>';
		
		//Div para enlace de descarga
		echo "<div id='div_enlace' style='display:none;'>";
		echo '</div>';
		
		echo "<br><br><br>";		
	}
	
?>

<style>
/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
.ui-datepicker {font-size:12px;}
/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
.ui-datepicker-cover {
    display: none; /*sorry for IE5*/
    display/**/: block; /*sorry for IE5*/
    position: absolute; /*must have*/
    z-index: -1; /*must have*/
    filter: mask(); /*must have*/
    top: -4px; /*must have*/
    left: -4px; /*must have*/
    width: 200px; /*must have*/
    height: 200px; /*must have*/
}
a{
	color: #2A5DB0;
}
a:hover {
    color: #1E90FF;
    font-weight: bold;
	cursor:pointer;
}

.divClass{
	background: -webkit-gradient(linear, left top, left bottom, from(#ebeff2), to(#ccd5df)); 
	background: -moz-linear-gradient(top, #ebeff2, #ccd5df); 
	filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#ebeff2, endColorstr=#ccd5df)"; 
	-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#ebeff2, endColorstr=#ccd5df)";
	border: 1px solid #ACBECF;
	cursor: pointer;	 
	width: 600px;
	height: 90px;
	-moz-border-radius: 10.5em;
	border-radius: 0.5em;
	zoom: 1;
} 
.divClass:hover{
	-webkit-box-shadow:rgba(0,0,0,0.7) 0px 5px 15px, inset rgba(0,0,0,0.15) 0px -10px 20px;
     -khtml-box-shadow:rgba(0,0,0,0.7) 0px 5px 15px, inset rgba(0,0,0,0.15) 0px -10px 20px;
       -moz-box-shadow:rgba(0,0,0,0.7) 0px 5px 15px, inset rgba(0,0,0,0.15) 0px -10px 20px;
         -o-box-shadow:rgba(0,0,0,0.7) 0px 5px 15px, inset rgba(0,0,0,0.15) 0px -10px 20px;
            box-shadow:rgba(0,0,0,0.7) 0px 5px 15px, inset rgba(0,0,0,0.15) 0px -10px 20px;

} 
.divClass:active{
	-webkit-box-shadow:rgba(0,0,0,0.4) 0px 5px 5px, inset rgba(0,0,0,0.10) 0px -10px 10px;
     -khtml-box-shadow:rgba(0,0,0,0.4) 0px 5px 5px, inset rgba(0,0,0,0.10) 0px -10px 10px;
       -moz-box-shadow:rgba(0,0,0,0.4) 0px 5px 5px, inset rgba(0,0,0,0.10) 0px -10px 10px;
         -o-box-shadow:rgba(0,0,0,0.4) 0px 5px 5px, inset rgba(0,0,0,0.10) 0px -10px 10px;
            box-shadow:rgba(0,0,0,0.4) 0px 5px 5px, inset rgba(0,0,0,0.10) 0px -10px 10px;
}
</style>

<script>
	//from(#C3D9FF), to(#DAE8FF)
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
	
</script>

<script>
	reload = "";
	var conteo_reiniciar = 0;
	//var tiempo_refresh = 60000; //Cada cuantos milisegundos se recarga la pantalla
	var tiempo_refresh = '<?php echo $wtiempomonitor; ?>'; //Cada cuantos milisegundos se recarga la pantalla
	tiempo_refresh = parseInt( tiempo_refresh );
	if( isNaN (tiempo_refresh ) == false ){
		tiempo_refresh = tiempo_refresh*1000;	
	}else{
		tiempo_refresh = 60000;
	}
	var mostrandoPDF = false;
	var maximoHojasEnUnion = '<?php echo $wmaximoHojas; ?>';//Maxima cantidad de hojas permitidas en la agrupación de archivos pdf
	maximoHojasEnUnion = parseInt(maximoHojasEnUnion);
	
	//Funcion que detecta cuando se detiene el scroll
	$.fn.scrollStopped = function(callback) {
		$(this).scroll(function(){
			var self = this, $this = $(self);
			if ($this.data('scrollTimeout')) {
			  clearTimeout($this.data('scrollTimeout'));
			}
			$this.data('scrollTimeout', setTimeout(callback,1000,self));
			windowPosY = window.pageYOffset;
			$("#wejey").val( windowPosY );
		});
	};
	
//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		cargarInicio();
		reload = setTimeout("enter()",tiempo_refresh);
		var d = new Date();
		conteo_reiniciar = d.getTime();
		
		//Detener el reload durante el scroll
		$(window).scroll(function () {
			clearTimeout(reload);   //detener el refresh
		});
		
		//Replantear el reload a donde estaba cuando se detiene el scroll
		//Es como "detener" el tiempo mientras se hace scroll, y retomarlo cuando se detiene
		$(window).scrollStopped(function(){
			if( mostrandoPDF == false ){
				var d = new Date();
				var conteo_reiniciar2 = d.getTime();
				if( (conteo_reiniciar2 - conteo_reiniciar) > tiempo_refresh ){
					enter();
				}else{
					reload = setTimeout("enter()", (tiempo_refresh - (conteo_reiniciar2 - conteo_reiniciar)));
					conteo_reiniciar = conteo_reiniciar2;
				}
			}
		});
	});
	
	function cargarInicio(){
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		$("#enlace_consultar_ultimos").click(function() {
			consultar_ultimas_impresiones();
		});
		if( $(".check_agrupar:checked").length == 0 ){
			$(".botonAgrupar").hide();
		}
		$("#input_buscar").on("keyup", function(e) {
			if(e.which == 13){
				filtrarConBusqueda( $(this).val() );
			}
		});
		if( $("#wejey").val() != 0 ){
			$('html, body').animate({
				scrollTop: $("#wejey").val()+'px',
				scrollLeft: '0px'
			},0);
		}
	}
	
	function marcarSolChecked( obj ){
		obj = jQuery(obj);
		var gbSumaDeHojas = 0;
		var cantidad_checkeados = 0;
		
		$(".check_agrupar:checked").each(function(){
			if( $(this).is(":disabled") == false && $(this).is(":visible") == true ){
				gbSumaDeHojas= gbSumaDeHojas + parseInt($(this).attr('hojas'));				
			}
		});
		if( gbSumaDeHojas > maximoHojasEnUnion ){
			alert("La suma de las hojas de las solicitudes seleccionadas ("+gbSumaDeHojas+")\nsupera las "+maximoHojasEnUnion+". El navegador no soportaria esa impresión");
			obj.attr("checked",false);
		}
		
		//Busca todas los checkbox de agrupar que esten checkeados y los guarda en la vble oculta
		//wsolicitudes_marcadas, para que cuando se recargue la pantalla no deschekee los que eligio
		var agrupar = new Array();
		$(".check_agrupar:checked").each(function(){
			if( $(this).is(":disabled") == false && $(this).is(":visible") == true ){
				agrupar.push( $(this).val() );
				cantidad_checkeados++;
			}
		});
		$("#wsolicitudes_marcadas").val( agrupar.toString() );	
		
		//Si selecciono al menos un checkbox, todos los botones de "imprimir" que no esten deshabilitados, se deshabilitan
		if( cantidad_checkeados > 0 && $(".deshabilitarporcheckbox").length == 0){
			$(".botonimprimirsol").not(":disabled").addClass("deshabilitarporcheckbox").attr("disabled",true);		
			$(".botonAgrupar").show();
		}
		if( cantidad_checkeados == 0){
			$(".deshabilitarporcheckbox").attr("disabled",false).removeClass("deshabilitarporcheckbox");	
			$(".botonAgrupar").hide();
		}		
	}
	
	function marcarTodosAgrupar(obj){
		obj = jQuery(obj);
		var gbSumaDeHojas = 0;
		var cantidad_checkeados = 0;
		
		//se descheckean todos
		$(".check_agrupar").attr("checked",false);
		
		if( obj.is(":checked") == false){
			$(".deshabilitarporcheckbox").attr("disabled",false).removeClass("deshabilitarporcheckbox");
			$(".botonAgrupar").hide();
			return;
		}
		
		$(".check_agrupar").each(function(){
			if( $(this).is(":disabled") == false && $(this).is(":visible") == true ){
				gbSumaDeHojas= gbSumaDeHojas + parseInt($(this).attr('hojas'));			
				if( gbSumaDeHojas > maximoHojasEnUnion ){
					alert("Solo se seleccionaron la cantidad de solicitudes que alcanzaban hasta "+maximoHojasEnUnion+" hojas en total.\nDebe generar una nueva impresión para el resto de solicitudes");
					return false; //para salir del each
				}
				$(this).attr("checked",true);
			}
		});
		
		//Busca todas los checkbox de agrupar que esten checkeados y los guarda en la vble oculta
		//wsolicitudes_marcadas, para que cuando se recargue la pantalla no deschekee los que eligio
		var agrupar = new Array();
		$(".check_agrupar:checked").each(function(){
			if( $(this).is(":disabled") == false && $(this).is(":visible") == true ){
				agrupar.push( $(this).val() );
				cantidad_checkeados++;
			}
		});
		$("#wsolicitudes_marcadas").val( agrupar.toString() );	
		
		//Si selecciono al menos un checkbox, todos los botones de "imprimir" que no esten deshabilitados, se deshabilitan
		if( cantidad_checkeados > 0 && $(".deshabilitarporcheckbox").length == 0){
			$(".botonimprimirsol").not(":disabled").addClass("deshabilitarporcheckbox").attr("disabled",true);		
			$(".botonAgrupar").show();
		}				
	}
	
	function agruparSolicitudes(){
		var agrupar = new Array();		
		var usuarios = new Array();
		var paquetes = new Array();
		$(".check_agrupar:checked").each(function(){
			if( $(this).is(":disabled") == false && $(this).is(":visible") == true ){
				agrupar.push( $(this).val() );
				usuarios.push( $(this).attr("usuario") );
				paquetes.push( $(this).attr("paquetes") );
			}
		});
		if( agrupar.length == 0 ){
			alert("Por favor seleccione las solicitudes que desea agrupar para imprimir");
			return;
		}
		if( agrupar.length < 2 ){
			alert("Necesita seleccionar al menos 2 solicitudes para agrupar e imprimir");
			return;
		}		
		clearTimeout(reload); //detener el refresh
		mostrandoPDF = true;
		
		var wemp_pmla = $("#wemp_pmla").val();		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		$.blockUI({ message: $('#msjEspere') });
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { 	wemp_pmla: wemp_pmla,  action: "agruparsolicitudes", paquetes: paquetes.toString(),
										usuarios: usuarios.toString(), solicitudes: agrupar.toString(), consultaAjax: aleatorio } ,
		function(data) {
			$.unblockUI();
			cerrarPDF();
			if( (/ERROR/).test(data) == true){
				alert("No pudo generarse la agrupación");
				reload = setTimeout("enter()",tiempo_refresh);
			}else{
				mostrarPDFAgrupado( data );
				//Deshabilitar botones imprimir y checkbox de las solicitudes agrupadas
				for(var i=0;i<agrupar.length;i++){
					$(".check_agrupar[value="+agrupar[i]+"]").attr("disabled",true);
					$(".botonimprimirsol[solicitud="+agrupar[i]+"]").remove();
				}
			}
		});
	}
	
	function cargarMonitor( codigo ){
		var wemp_pmla = $("#wemp_pmla").val();
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { wemp_pmla: wemp_pmla,  wmonitor: codigo, action: "elegirmonitor", consultaAjax: aleatorio } ,
		function(data) {
			$("body").html(data);
			cargarInicio();
		});
	
	}
	
	function filtrar_modalidad(){
		var modalidad = $("#select_modalidades").val();
		if( modalidad != ""){
			$("#tabla_solicitudes tr.ocultable").hide();
			$("#tabla_solicitudes tr[modalidad="+modalidad+"]").show();
			$("#select_usuarios option:first").attr("selected",true);
			$("#input_buscar").val("");
		}else{
			$("#tabla_solicitudes tr.ocultable").show();
		}		
	}
	
	function filtrar_usuario(){
		var usuario = $("#select_usuarios").val();
		if( usuario != ""){
			$("#tabla_solicitudes tr.ocultable").hide();
			$("#tabla_solicitudes tr[usuario="+usuario+"]").show();	
			$("#select_modalidades option:first").attr("selected",true);
			$("#input_buscar").val("");
		}else{
			$("#tabla_solicitudes tr.ocultable").show();
		}
	}
	
	function filtrarConBusqueda( valor ){
		
		valor = $.trim( valor );
		valor = valor.toUpperCase();
		
		if( valor == "" ){
			$("#tabla_solicitudes tr.ocultable").show();
			return;
		}
		
		$("#select_modalidades option:first").attr("selected",true);
		$("#select_usuarios option:first").attr("selected",true);
		
		if( valor.length < 4 ){
			alerta("Ingrese al menos 4 caracteres para realizar la busqueda");
			return;
		}
		
		$("#tabla_solicitudes tr.ocultable").hide();
		
		$.blockUI({ message: $('#msjEspere') });		

		var patt1 = new RegExp( valor , "g" );

		$('.parabuscar').each(function(){
			texto = $(this).text();
			texto = $.trim(texto);		
			if ( patt1.test( texto ) ) {
				$(this).parent().show();
			}
		});
		$.unblockUI();
	}
	
	function enter(){
		//window.location = "monitor_cenimp.php?wemp_pmla="+$("#wemp_pmla").val()+"&select_modalidades="+$("#select_modalidades").val()+"&select_usuarios="+$("#select_usuarios").val()+"&input_buscar="+$("#input_buscar").val();
		$("#select_modalidades_2").val(  $("#select_modalidades").val() );
		$("#select_usuarios_2").val(  $("#select_usuarios").val() );
		$("#input_buscar_2").val(  $("#input_buscar").val() );
		
		document.forms.monitor.submit();
	}
	
	function restablecer_pagina(){
		$("#tabla_solicitudes tr.ocultable").show();
		$("#select_modalidades option:first").attr("selected",true);
		$("#select_usuarios option:first").attr("selected",true);
		$("#input_buscar").val("");
		cerrarPDF();
	}	

	//Funcion que muestra un archivo pdf que ya existe, solo debe mostrarlo y enviar una peticion para marcarlo como impreso
	function mostrarPDF( codigo_solicitud, obj ){
		var wemp_pmla = $("#wemp_pmla").val();
		
		obj = jQuery(obj);
		obj.attr("disabled", true); //Deshabilitar el boton		
		$(".check_agrupar[value="+codigo_solicitud+"]").attr("disabled", true); //Deshabilitar el checkbox
		
		crearObjectPdf( codigo_solicitud, "Solicitud "+codigo_solicitud );
		
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { wemp_pmla: wemp_pmla,  wmonitor: $("#wmonitor").val(),  solicitud: codigo_solicitud, action: "imprimioSolicitud", consultaAjax: aleatorio } ,
		function(data) {
			if( $.trim(data) == "ANULADO"){
				alert("La solicitud ha sido anulada");
				$('#div_contenedor_pdf').html("");
			}
		});
	}
	
	//Funcion que debe enviar una peticion para concatenar todas las partes del archivo en uno solo para luego mostrarlo
	function concatenarPDF( codigo_solicitud, paquetes, obj ){
		obj = jQuery(obj);
		obj.attr("disabled", true); //Deshabilitar el boton	
		$(".check_agrupar[value="+codigo_solicitud+"]").attr("disabled", true); //Deshabilitar el checkbox
		var wemp_pmla = $("#wemp_pmla").val();
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { wemp_pmla: wemp_pmla,  wmonitor: $("#wmonitor").val(),  solicitud: codigo_solicitud, paquetes: paquetes, action: "concatenarPdf", consultaAjax: aleatorio } ,
		function(data) {
			if( $.trim(data) == "OK" ){
				crearObjectPdf( codigo_solicitud, "Solicitud "+codigo_solicitud );
			}else{
				alert("Error al imprimir el pdf:\n"+data);
				obj.attr("disabled", false); //Deshabilitar el boton	
				$(".check_agrupar[value="+codigo_solicitud+"]").attr("disabled", false); //Deshabilitar el checkbox
			}
		});
	}

	function mostrarPDFreimprimir( codigo_solicitud ){
		crearObjectPdf(codigo_solicitud, "Solicitud "+codigo_solicitud);
	}
	
	function mostrarPDFAgrupado( codigo_solicitud ){		
		crearObjectPdf( codigo_solicitud, "Solicitudes agrupadas" );
	}
	
	function consultar_ultimas_impresiones(){
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		$("#wconsultandoLista").val( "on" );
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { wemp_pmla: wemp_pmla,  wmonitor: $("#wmonitor").val(),  action: "consultarultimas", consultaAjax: aleatorio } ,
		function(data) {
			$("#lista_ultimas").html( data );		
		});
	}
	
	function consultarImpresasFechas(){
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
		
		var fecha_i = $("#fecha_i").val();
		var fecha_f = $("#fecha_f").val();
		
		$("#wfeci_consulta").val(fecha_i); //para que cuando se haga el submit se muestre la misma consulta
		$("#wfecf_consulta").val(fecha_f); //para que cuando se haga el submit se muestre la misma consulta
		
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { wemp_pmla: wemp_pmla,  wmonitor: $("#wmonitor").val(),  fecha_i: fecha_i, fecha_f: fecha_f, action: "consultarultimasfechas", consultaAjax: aleatorio } ,
		function(data) {
			$("#lista_ultimas").html( data );		
		});
	}
	
	function cerrarUltimasImpresas(){
		$("#lista_ultimas").html( '' );	
		$("#wconsultandoLista").val( "off" );
	}
	
	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 1600 );
	}
	
	function crearObjectPdf( codigo_solicitud, titulo ){
		if( titulo == undefined || titulo == '' )
			titulo = "Solicitud "+codigo_solicitud;
		var prefijo = $("#wemp_pmla").val();
		
		var object='<br><br><br><font size=5 color="#2A5DB0">'+titulo+'</font>'
				+'<br><br>'
				+'<object type="application/pdf" data="../reportes/cenimp/'+prefijo+'Solicitud_'+codigo_solicitud+'.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" width="900" height="700">'
					+'<param name="src" value="../reportes/cenimp/'+prefijo+'Solicitud_'+codigo_solicitud+'.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" />'
					+'<p style="text-align:center; width: 60%;">'
						+'Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />'
						+'<a href="http://get.adobe.com/es/reader/" onclick="this.target=\'_blank\'">'
							+'<img src="../../images/medical/root/prohibido.gif" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" />'
						+'</a>'
					+'</p>'
				+'</object>';
			var boton ='<br><input type="button" value="Cerrar PDF" onclick="cerrarPDF()" />';
			object = boton + object;
		$("#div_contenedor_pdf").html(object);
		
		clearTimeout(reload);   //detener el refresh
		mostrandoPDF = true;
		var posicion = $('#div_contenedor_pdf').offset();
		ejeY = posicion.top;
		
		$('html, body').animate({
			scrollTop: ejeY+'px',
			scrollLeft: '0px'
		},0);
	}
	
	function cerrarPDF(){
		$("#div_contenedor_pdf").html("");		
		mostrandoPDF = false;
		var d = new Date();
		var conteo_reiniciar2 = d.getTime();
		if( (conteo_reiniciar2 - conteo_reiniciar) > tiempo_refresh ){
			enter();
		}else{
			reload = setTimeout("enter()", (tiempo_refresh - (conteo_reiniciar2 - conteo_reiniciar)));	
			conteo_reiniciar = conteo_reiniciar2;
		}
	}
	
	function quitarSolicitud( obj, codigo ){
		var wemp_pmla = $("#wemp_pmla").val();
		obj = jQuery(obj);
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('monitor_cenimp.php', { wemp_pmla: wemp_pmla,  solicitud: codigo,  action: "quitarSolicitud", consultaAjax: '' } ,
		function(data) {
			if( $.trim(data) == "OK" ){
				obj.parent().parent().remove(); //Eliminar la fila que contiene la solicitud
			}else{
				alert("Ha ocurrido un error \n: "+data);
			}
		});
	}
	
	function mostrarLinkDescarga(codigo_solicitud, obj ){
		var wemp_pmla = $("#wemp_pmla").val();
		
		obj = jQuery(obj);
		obj.attr("disabled", true); //Deshabilitar el boton		
		obj.prev().attr("disabled", true); //Deshabilitar el boton		
		$(".check_agrupar[value="+codigo_solicitud+"]").attr("disabled", true); //Deshabilitar el checkbox
		
		var href= "monitor_cenimp.php?action=descargar&consultaAjax=345&solicitud="+codigo_solicitud+"&wemp_pmla="+wemp_pmla;
		var enlace = "<br><br><div align='center'><a name='link_dd' onclick='cerrarDialog()' id='link_dd' href='"+href+"'>Descargar Solicitud "+codigo_solicitud+"</a></div>";
		$("#div_enlace").html(enlace);
		$("#div_enlace").dialog({
			  width: 500,
			  maxHeight: 680,
			  title: "Descargar PDF",
			  dialogClass: 'fixed-dialog',
			  modal: true
			});
	}
	
	function mostrarLinkDescargaConcatenar(codigo_solicitud, paquetes, obj){
		var wemp_pmla = $("#wemp_pmla").val();
		
		obj = jQuery(obj);
		obj.attr("disabled", true); //Deshabilitar el boton
		obj.prev().attr("disabled", true); //Deshabilitar el boton
		$(".check_agrupar[value="+codigo_solicitud+"]").attr("disabled", true); //Deshabilitar el checkbox
		
		var href= "monitor_cenimp.php?action=descargar&consultaAjax=345&solicitud="+codigo_solicitud+"&wemp_pmla="+wemp_pmla+"&paquetes="+paquetes;
		var enlace = "<a name='link_dd' id='link_dd' onclick='cerrarDialog()' href='"+href+"'>Descargar Solicitud "+codigo_solicitud+"</a>";
		$("#div_enlace").html(enlace);
		$("#div_enlace").dialog({
			  width: 500,
			  maxHeight: 680,
			  title: "Descargar PDF",
			  dialogClass: 'fixed-dialog',
			  modal: true
			});
	}
	
	function cerrarDialog(){
		$("#div_enlace").dialog( "close" );
	}
</script>
</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();	
			?>
    </body>
</html>