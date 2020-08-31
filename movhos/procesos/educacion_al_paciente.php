<?php
include_once("conex.php");
/************************************************************************************************************
 * Reporte		:	Necesidades detectadas de educacion
 * Fecha		:	2012-11-28
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	El objetivo del programa es
 *********************************************************************************************************

 Actualizaciones:
	2016-05-05 (Jonatan Lopez)
		* Se agrega la funcion consultarUltimoDiagnosticoHCE para que consulte la ultima evolucion del paciente.
	2014-08-12 (Edwar Jaramillo)
		* Se comentan líneas de blockUI porque al presionar el boton "Guardar y salir", se genera un error javascript cuando se da clic en el boton ver en la lista de historias.
		* Se crea función javascript "guardarYSalir" Siempre que se habra un registro, se edite o se guarden parámetros por primer vez, se retornará a la lista de
			historias, se hace ese retorno para evitar confundir al usuario del programa puesto que en ocaciones
			cuando guarda una observación, actividad y responsable, el formulario queda con campos en blanco lo que hace pensar que
								la información no fué guardada dando la posibilidad a reescribir y repetir información.
		* La función retorna a la vista anterior y genera de nuevo la consulta para marcar que la historia ya tiene educación.

	2014-08-11 (Edwar Jaramillo)
		* Se eliminan las etiquetas html que se muestran en las observaciones de los mendicamentos.
		* Se mejora la validación de vacío para objetivos, actividades y responsable. En una variable temporal elimina todos los
			"\r" "\n" "\t" no usando   / /g   sino / /gi  para verificar completamente el campo vacío.

	2013-05-20 (Frederick Aguirre)
	Se corrige que al tratar de guardar varios compromisos, solo guardaba el primero

	2013-05-17 (Frederick Aguirre)
	Se muestra la impresion al momento de consultar el formulario de ingreso de enfermeria de HCE.
	Se muestra una nueva columna que indica si el paciente tiene o no educacion.

	2013-05-06 (Frederick Aguirre)
	Se crea la opcion de consultar en un rango de fechas una lista de los pacientes a los que se les ha guardado información de necesidades.
	Se habilita la posibilidad de guardar necesidades aunque el paciente este egresado.
 **********************************************************************************************************/

 $wactualiz = "2017-04-05";

 if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>Educacion al Paciente</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	//echo ' <link rel="stylesheet" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" />';
	echo '<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/toJson.js" type="text/javascript"></script>';

	echo "<link type='text/css' href='../../hce/procesos/HCE.css' rel='stylesheet'> ";
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//



include_once("root/comun.php");
include_once("movhos/movhos.inc.php");
include_once("hce/HCE_print_function.php");



$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wbaseHce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if($action=="mostrarlista"){
		mostrarListaServicio( $_REQUEST['servicio'], $_REQUEST['fecha'], @$_REQUEST['responsable'], @$_REQUEST['historia'] );
	}else if ( $action == "mostrarlistaingresos"){
	    mostrarListaDeIngresos( @$_REQUEST['historia'] );
	}else if($action=="mostrarPaciente"){
		mostrarDetallePaciente( @$_REQUEST['historia'], @$_REQUEST['ingreso'], @$_REQUEST['servicio'], @$_REQUEST['fecha'], @$_REQUEST['paciente'], @$_REQUEST['doc_paciente'], @$_REQUEST['habitacion'], @$_REQUEST['nacimiento'], @$_REQUEST['medico']);
	}else if($action=="guardarNecesidad"){
		$necesidad['historia'] = @$_REQUEST['historia'];
		$necesidad['ingreso'] = @$_REQUEST['ingreso'];
		$necesidad['conoce_diagnostico'] = @$_REQUEST['conoce_diagnostico'];
		$necesidad['condicion_mejorara'] = @$_REQUEST['condicion_mejorara'];
		$necesidad['acepta_limitaciones'] = @$_REQUEST['acepta_limitaciones'];
		$necesidad['manejo_medicamentos'] = @$_REQUEST['manejo_medicamentos'];
		$necesidad['manejo_dispositivos'] = @$_REQUEST['manejo_dispositivos'];
		$necesidad['estado_animo'] = @$_REQUEST['estado_animo'];
		$necesidad['interacciones'] = @$_REQUEST['interacciones'];
		$necesidad['dieta'] = @$_REQUEST['dieta'];
		$necesidad['manejo_dolor'] = @$_REQUEST['manejo_dolor'];
		$necesidad['derechos'] = @$_REQUEST['derechos'];
		$necesidad['tecnicas_rehabilitacion'] = @$_REQUEST['tecnicas_rehabilitacion'];
		$necesidad['otras'] = @$_REQUEST['otras'];
		$necesidad['consentimientos'] = @$_REQUEST['consentimientos'];
		$necesidad['observaciones'] = @$_REQUEST['observaciones'];
		$necesidad['compromisos'] = @$_REQUEST['compromisos'];
		guardarNecesidad( $necesidad );
	}else if ($action=="consultandoNecesidad"){
		mostrarNecesidad( @$_REQUEST['historia'], @$_REQUEST['ingreso'], @$_REQUEST['fecha'] );
	}else if($action=="consultarListaNecesidades"){
		cargarListaDeNecesidades( @$_REQUEST['historia'], @$_REQUEST['ingreso'] );
	}else if ( $action == "consultarListaEgresados" ){
		consultarListaEgresados(@$_REQUEST['fecha_i'], @$_REQUEST['fecha_f']);
	}else if( $action == 'consultarIngresoEnfermeria' ){
		consultarIngresoEnfermeria(@$_REQUEST['historia'], @$_REQUEST['ingreso']);
	}

	return;
}
//FIN*LLAMADOS*AJAX**************************************************************************************************************//

	//Funcion que imprime una tabla con las auditorias realizadas a un paciente para una historia ingreso (Parametros: historia, ingreso)
	function cargarListaDeNecesidades( $whis, $wing ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$lista_necesidades = "<table border=0 align='center' width='100%' >";
		$lista_necesidades.= "<tr><td align='right'><input type='button' value='Crear nueva' id='add_necesidad' /></td></tr>";
		$lista_necesidades.= "<tr class='encabezadoTabla'><td>Auditarias del paciente</td></tr>";
		$query = "	SELECT 		Fecha_data as fecha "
				 ."	  FROM 		".$wbasedato."_000149 "
				 ."	 WHERE 		Nechis = '".$whis."'"
				 ."    AND  	Necing = '".$wing."'"
			."	  GROUP BY      Fecha_data "
			 ."	  ORDER BY 		Fecha_data";
		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
				$ii=0;
				while( $row = mysql_fetch_assoc($res) ){
						$wclass="fila2";
					$lista_necesidades.="<tr class='".$wclass."'><td align=center><A HREF='#formulario_necesidad' onClick= \" javascript:consultarNecesidades('".$whis."','".$wing."','".$row['fecha']."')  \" class=tipo3V>".$row['fecha']."</A></td></tr>";
				}
		}
		$lista_necesidades.= "</table>";
		echo  $lista_necesidades;
	}

	function consultarListaEgresados($wfecha_ini, $wfecha_fin){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		echo "<table>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th colspan=9>Resultados entre el dia ".$wfecha_ini." y ".$wfecha_fin."</th>";
		echo "</tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th>Historia</th>";
		echo "<th>Ingreso</th>";
		echo "<th>Paciente</th>";
		echo "<th>Médico(s) Tratante(s)</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>";


			 //PARA LA INFORMACION DEL PACIENTE
		 $q = "SELECT Nechis as historia, Necing as ingreso, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento "
            ."   FROM root_000036, root_000037, ".$wbasedato."_000149 A  "
            ."  WHERE A.Fecha_data BETWEEN '".$wfecha_ini."' AND '".$wfecha_fin."'"
			."    AND orihis = Nechis "
			."    AND oriing = Necing "
            ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
		."   GROUP BY Nechis, Necing "
		."   ORDER BY Nechis ";

		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
			$wclass = "fila1";
				while( $row = mysql_fetch_assoc($res) ){
					$medicos = traer_medico_tte($row['historia'], $row['ingreso'],  $num);
					$onclic = "realizarConsultaPaciente('".$row['historia']."', '".$row['ingreso']."', '', '".$row['paciente']."', '".$row['tipo_identificacion']." ".$row['numero_documento']."', '', '".$row['nacimiento']."', '".$medicos."' )";
					( $wclass == 'fila2' ) ? $wclass='fila1' : $wclass='fila2';
					echo "<tr class='".$wclass."'>";
					echo "<td>".$row['historia']."</td>";
					echo "<td align='center'>".$row['ingreso']."</td>";
					echo "<td>".$row['paciente']."</td>";
					echo "<td>".$medicos."</td>";
					echo '<td> <a href="#info_paciente" onclick="'.$onclic.'">Ver</a> </td>';
					echo "</tr>";
				}
		}
		echo "</table>";
	}

	//Funcion que retorna los datos que fueron ingresados en auditorias (Parametro: historia, ingreso, fecha)
	function mostrarNecesidad($whis, $wing, $wfec){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$usuario_session = $_SESSION['user'];
		$pos = strpos($usuario_session,"-");
		$usuario_session = substr($usuario_session,$pos+1,strlen($usuario_session));

		$fecha_hoy = date("Y-m-d");

		$query = "		SELECT 		Seguridad as usuario, Fecha_data as fecha, Hora_data as hora,
									Neccdi as conoce_diagnostico,
									Neccme as condicion_mejorara,
									Necali as acepta_limitaciones, "
					 ."   			Necmme as manejo_medicamentos,
									Necmdi as manejo_dispositivos,
									Necesa as estado_animo,
									Necint as interacciones, "
					 ."				Necdie as dieta,
									Necmdo as manejo_dolor,
									Necder as derechos,
									Necter as tecnicas_rehabilitacion,
									Necotr as otras,
									Neccon as consentimientos,
									Necobs as observaciones,
									id as codigo_id "
					 ."	  FROM 		".$wbasedato."_000149 "
					 ."	 WHERE 		Nechis = '".$whis."'"
					 ."    AND  	Necing = '".$wing."'"
					 ."    AND 		Fecha_data = '".$wfec."'"
					 ." ORDER BY    Fecha_data DESC, Hora_data DESC";

		//echo $query."<br>";
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$datos = array();

		if ($num > 0 ){
				$dato['conoce_diagnostico'] = "";  $dato['condicion_mejorara'] = "";   $dato['acepta_limitaciones'] = "";
				$dato['manejo_medicamentos'] = "";  $dato['manejo_dispositivos'] = ""; $dato['estado_animo'] = "";   $dato['interacciones'] = "";  $dato['dieta'] = "";
				$dato['manejo_dolor'] = "";   $dato['derechos'] = "";   $dato['tecnicas_rehabilitacion'] = "";   $dato['otras'] = "";
				$dato['consentimientos'] = "";   $dato['observaciones'] = "";  $dato['compromisos'] = array();

				$i = 1;
				while( $row = mysql_fetch_assoc($res) ){
					$pos = strpos($row['usuario'],"-");
					$usuario_buscado = substr($row['usuario'],$pos+1,strlen($row['usuario']));

					//Si la auditoria fue hecha el dia de hoy por el usuario logueado, se puede editar
					if( $row['fecha'] == $fecha_hoy ){
						$row['editable'] = true;
						$datos['necesidad_editable'] = $row;
					}
					//Agregando todas las auditorias
					$user_name = buscarUsuario( $usuario_buscado );
					$row['usuario'] = $user_name;
					$cadena_al_final = "\n".$user_name."  ".$row['fecha']."  ".$row['hora'];
					if( $i < $num ) $cadena_al_final.=" \n \n================================================= \n \n";

					if( $row['conoce_diagnostico'] != "" ) $dato['conoce_diagnostico'] .= $row['conoce_diagnostico']."\n---------------------------------------------".$cadena_al_final;
					if( $row['condicion_mejorara'] != "" ) $dato['condicion_mejorara'] .= $row['condicion_mejorara']."\n---------------------------------------------".$cadena_al_final;
					if( $row['acepta_limitaciones'] != "" ) $dato['acepta_limitaciones'] .= $row['acepta_limitaciones']."\n---------------------------------------------".$cadena_al_final;
					if( $row['manejo_medicamentos'] != "" ) $dato['manejo_medicamentos'] .= $row['manejo_medicamentos']."\n---------------------------------------------".$cadena_al_final;
					if( $row['manejo_dispositivos'] != "" ) $dato['manejo_dispositivos'] .= $row['manejo_dispositivos']."\n---------------------------------------------".$cadena_al_final;
					if( $row['estado_animo'] != "" ) $dato['estado_animo'] .= $row['estado_animo']."\n---------------------------------------------".$cadena_al_final;
					if( $row['interacciones'] != "" ) $dato['interacciones'] .= $row['interacciones']."\n---------------------------------------------".$cadena_al_final;
					if( $row['dieta'] != "" ) $dato['dieta'] .= $row['dieta']."\n---------------------------------------------".$cadena_al_final;
					if( $row['manejo_dolor'] != "" ) $dato['manejo_dolor'] .= $row['manejo_dolor']."\n---------------------------------------------".$cadena_al_final;
					if( $row['derechos'] != "" ) $dato['derechos'] .= $row['derechos']."\n---------------------------------------------".$cadena_al_final;
					if( $row['tecnicas_rehabilitacion'] != "" ) $dato['tecnicas_rehabilitacion'] .= $row['tecnicas_rehabilitacion']."\n---------------------------------------------".$cadena_al_final;
					if( $row['otras'] != "" ) $dato['otras'] .= $row['otras']."\n---------------------------------------------".$cadena_al_final;
					if( $row['consentimientos'] != "" ) $dato['consentimientos'] .= $row['consentimientos']."\n---------------------------------------------".$cadena_al_final;
					if( $row['observaciones'] != "" ) $dato['observaciones'] .= $row['observaciones']."\n---------------------------------------------".$cadena_al_final;

					$query2 = "	SELECT 			Fecha_data as fecha, Hora_data as hora, Ncoobj as objetivo,
												Ncoact as actividad,
												Ncores as responsable "
								 ."	  FROM 		".$wbasedato."_000150 "
								 ."	 WHERE 		Ncocod= ".$row['codigo_id']
								 ." ORDER BY id asc ";

					//echo "Compromisos: ".$query2."<br><br>";;
					$res2 = mysql_query($query2, $conex);
					$num2 = mysql_num_rows($res2);
					if ($num2 > 0 ){
						$hh = 0;
						while ( $row2 = mysql_fetch_assoc($res2) ){
							$cadena_al_final = "\n".$user_name."  ".$row2['fecha']."  ".$row2['hora'];
							if( $hh < $num ) $cadena_al_final.=" \n \n========================================= \n \n";

							$compromiso = array();
							$compromiso['objetivo'] = $row2['objetivo']."\n--------------------------------------".$cadena_al_final;
							$compromiso['actividad'] = $row2['actividad']."\n--------------------------------------".$cadena_al_final;
							$compromiso['responsable'] = $row2['responsable']."\n---------------------------------------".$cadena_al_final;
							array_push( $dato['compromisos'], $compromiso );
						}
					}

					$i++;
				}
				$datos['necesidades_todas'] = $dato;
		}

		echo json_encode( $datos );
	}

	//Funcion que almacena en matrix una auditoria ( El parámetro es un arreglo asociativo con los datos de una auditoria )
	function guardarNecesidad( $necesidad ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$actualizar = false;
		$usuario = $_SESSION['user'];
		$pos = strpos($usuario,"-");
		$usuario = substr($usuario,$pos+1,strlen($usuario));
		$codigo_id = null;

		//Verificar que no hayan datos vacios
		if( empty($necesidad['conoce_diagnostico']) && empty($necesidad['condicion_mejorara']) && empty($necesidad['acepta_limitaciones']) && empty($necesidad['manejo_medicamentos'])
			&& empty($necesidad['estado_animo']) && empty($necesidad['manejo_dispositivos']) && empty($necesidad['interacciones']) && empty($necesidad['dieta']) && empty($necesidad['manejo_dolor'])
			&& empty($necesidad['tecnicas_rehabilitacion']) && empty($necesidad['derechos']) && empty($necesidad['tecnicas_rehab']) && empty($necesidad['otras']) && empty($necesidad['consentimientos']) && empty($necesidad['observaciones']) ){
			echo "V";
			return;
		}

		//Parseamos el arreglo de objetivos
		$necesidad['compromisos'] = str_replace("\\", "", $necesidad['compromisos']);
		$necesidad['compromisos'] = str_replace("\"[", "[", $necesidad['compromisos']);
		$necesidad['compromisos'] = str_replace("]\"", "]", $necesidad['compromisos']);
		$necesidad['compromisos'] = json_decode( $necesidad['compromisos'], true );

		//SE CONSULTA SI EL USUARIO YA REALIZO UNA AUDITORIA PARA EL PACIENTE EL DIA DE HOY, SI ES ASI SE ACTUALIZA CONCATENANDO EL NUEVO TEXTO
		//EN CASO CONTRARIO SE INSERTA EL REGISTRO
		$query = "		SELECT 		* "
					 ."	  FROM 		".$wbasedato."_000149 "
					 ."	 WHERE 		Nechis = '".$necesidad['historia']."'"
					 ."    AND  	Necing = '".$necesidad['ingreso']."'"
					 ."    AND 		Fecha_data = '".date("Y-m-d")."'"
					 ."    AND      Seguridad = 'C-".$usuario."'";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
			$row = mysql_fetch_assoc($res);
				$actualizar = true;

				( $necesidad['conoce_diagnostico'] != "" )? $necesidad['conoce_diagnostico'] = $necesidad['conoce_diagnostico']."\n".$row['Neccdi'] : $necesidad['conoce_diagnostico'] = $row['Neccdi'];
				( $necesidad['condicion_mejorara'] != "" )? $necesidad['condicion_mejorara'] = $necesidad['condicion_mejorara']."\n".$row['Neccme'] : $necesidad['condicion_mejorara'] = $row['Neccme'];
				( $necesidad['acepta_limitaciones'] != "" )? $necesidad['acepta_limitaciones'] = $necesidad['acepta_limitaciones']."\n".$row['Necali'] : $necesidad['acepta_limitaciones'] = $row['Necali'];
				( $necesidad['manejo_medicamentos'] != "" )? $necesidad['manejo_medicamentos'] = $necesidad['manejo_medicamentos']."\n".$row['Necmme'] : $necesidad['manejo_medicamentos'] = $row['Necmme'];
				( $necesidad['manejo_dispositivos'] != "" )? $necesidad['manejo_dispositivos'] = $necesidad['manejo_dispositivos']."\n".$row['Necmdi'] : $necesidad['manejo_dispositivos'] = $row['Necmdi'];
				( $necesidad['estado_animo'] != "" )? $necesidad['estado_animo'] = $necesidad['estado_animo']."\n".$row['Necesa'] :  $necesidad['estado_animo'] = $row['Necesa'];
				( $necesidad['interacciones'] != "" )? $necesidad['interacciones'] = $necesidad['interacciones']."\n".$row['Necint'] : $necesidad['interacciones'] = $row['Necint'];
				( $necesidad['dieta'] != "" )? $necesidad['dieta'] = $necesidad['dieta']." \n".$row['Necdie'] : $necesidad['dieta'] = $row['Necdie'];
				( $necesidad['manejo_dolor'] != "" )? $necesidad['manejo_dolor'] = $necesidad['manejo_dolor']." \n".$row['Necmdo'] : $necesidad['manejo_dolor'] = $row['Necmdo'];
				( $necesidad['derechos'] != "" )? $necesidad['derechos'] = $necesidad['derechos']." \n".$row['Necder'] : $necesidad['derechos'] = $row['Necder'];
				( $necesidad['tecnicas_rehabilitacion'] != "" )? $necesidad['tecnicas_rehabilitacion'] = $necesidad['tecnicas_rehabilitacion']." \n".$row['Necter'] : $necesidad['tecnicas_rehabilitacion'] = $row['Necter'];
				( $necesidad['otras'] != "" )? $necesidad['otras'] = $necesidad['otras']." \n".$row['Necotr'] : $necesidad['otras'] = $row['Necotr'];
				( $necesidad['consentimientos'] != "" )? $necesidad['consentimientos'] = $necesidad['consentimientos']." \n".$row['Neccon'] : $necesidad['consentimientos'] = $row['Neccon'];
				( $necesidad['observaciones'] != "" )? $necesidad['observaciones'] = $necesidad['observaciones']."\n".$row['Necobs'] : $necesidad['observaciones'] = $row['Necobs'];

				foreach ( $necesidad['compromisos']['objetivos'] as $pos=>$obj ){
					$act = $necesidad['compromisos']['actividades'][$pos];
					$resp = $necesidad['compromisos']['responsables'][$pos];

					$qq="INSERT INTO
						".$wbasedato."_000150 (medico, Fecha_data, Hora_data, Ncocod, Ncoobj, Ncoact, Ncores, Seguridad)
					VALUES
						('movhos','".date("Y-m-d")."','".(string)date("H:i:s")."', ".$row['id'].", '".$obj."','".$act."','".$resp."', 'C-".$usuario."');";
					$resq = mysql_query($qq, $conex);
				}
		}

		/*echo "<pre>";
		print_r( $necesidad['compromisos'] );
		echo "</pre>";*/



		if( $actualizar == false){

			//Insertar registro
			$q="INSERT INTO
				".$wbasedato."_000149 (medico, Fecha_data, Hora_data, Nechis, Necing, Neccdi, Neccme, Necali, Necmme, Necmdi, Necesa, Necint, Necdie, Necmdo, Necder, Necter, Necotr, Neccon, Necobs, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".(string)date("H:i:s")."','".$necesidad['historia']."','".$necesidad['ingreso']."','".$necesidad['conoce_diagnostico']."','".$necesidad['condicion_mejorara']."','".$necesidad['acepta_limitaciones']."', '".$necesidad['manejo_medicamentos']."', '".$necesidad['manejo_dispositivos']."', '".$necesidad['estado_animo']."', '".$necesidad['interacciones']."', '".$necesidad['dieta']."', '".$necesidad['manejo_dolor']."', '".$necesidad['derechos']."', '".$necesidad['tecnicas_rehabilitacion']."', '".$necesidad['otras']."', '".$necesidad['consentimientos']."', '".$necesidad['observaciones']."', 'C-".$usuario."');";

			$res = mysql_query($q, $conex);
			$guardo = mysql_insert_id();
			if( $guardo ){
				foreach ( $necesidad['compromisos']['objetivos'] as $pos=>$obj ){
					$act = $necesidad['compromisos']['actividades'][$pos];
					$resp = $necesidad['compromisos']['responsables'][$pos];

					$qq="INSERT INTO
						".$wbasedato."_000150 (medico, Fecha_data, Hora_data, Ncocod, Ncoobj, Ncoact, Ncores, Seguridad)
					VALUES
						('movhos','".date("Y-m-d")."','".(string)date("H:i:s")."', ".$guardo.", '".$obj."','".$act."','".$resp."', 'C-".$usuario."');";
					$resq = mysql_query($qq, $conex);
					//echo $qq."<br>";
				}
				echo "I";
			}else{
				echo "V";
			}
		}else{
			$user_aux = "C-".$usuario;
			$q="UPDATE
				".$wbasedato."_000149 SET	Hora_data = '".date("H:i:s")."',
											Neccdi = '".$necesidad['conoce_diagnostico']."',
											Neccme = '".$necesidad['condicion_mejorara']."',
											Necali = '".$necesidad['acepta_limitaciones']."',
											Necmme = '".$necesidad['manejo_medicamentos']."',
											Necmdi = '".$necesidad['manejo_dispositivos']."',
											Necesa = '".$necesidad['estado_animo']."',
											Necint = '".$necesidad['interacciones']."',
											Necdie = '".$necesidad['dieta']."',
											Necmdo = '".$necesidad['manejo_dolor']."',
											Necder = '".$necesidad['derechos']."',
											Necter = '".$necesidad['tecnicas_rehabilitacion']."',
											Necotr = '".$necesidad['otras']."',
											Neccon = '".$necesidad['consentimientos']."',
											Necobs = '".$necesidad['observaciones']."'
									WHERE   Nechis = '".$necesidad['historia']."'
									  AND   Necing = '".$necesidad['ingreso']."'
									  AND   Fecha_data = '".date("Y-m-d")."'
									  AND   Seguridad = '".$user_aux ."'";
			$res = mysql_query($q, $conex);
			$actualizados = mysql_affected_rows();
			if( $actualizados > 0 ){
				echo "I";
			}else{
				echo "V";
			}
		}
	}

	//retorna el nombre de un usuario de matrix (Parametro: codigo del usuario)
	function buscarUsuario($wcod_funcionario){
		global $conex;

		if (strpos($wcod_funcionario, '-')){
			$explode = explode('-',$wcod_funcionario);
			$wcod_funcionario = $explode[1];
		}
		$wcod_funcionario = trim($wcod_funcionario);
		$usuario = "";
		$query = "  SELECT  Descripcion as nombre
					FROM    usuarios
					WHERE   Codigo = '$wcod_funcionario'";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);

		if ($num > 0){
			$row = mysql_fetch_array($res);
			$usuario = $row['nombre'];
		}
		return $usuario;
	}

	//Funcion que retorna los medicos que tratan al paciente (Parametros: historia, ingreso, fecha , contador)
    function traer_medico_tte($whis, $wing, &$i){
        global $conex;
        global $wbasedato;

        $q = " SELECT Distinct Medno1, Medno2, Medap1, Medap2  "
			."   FROM ".$wbasedato."_000047, ".$wbasedato."_000048 "
			."  WHERE methis = '".$whis."'"
			."    AND meting = '".$wing."'"
			."    AND metest = 'on' "
			//."    AND metfek = '".$wfecha."'"
			."    AND mettdo = medtdo "
			."    AND metdoc = meddoc "
			."	  AND Medno1 != ''";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0){
            $wmed="";
            for ($i=1; $i <= $wnum;$i++){
                $row = mysql_fetch_array($res);
                if ($i < $wnum)
                { $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."<br>"; }
                else
                { $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]; }
            }
            return $wmed;
        }
        else
        { return "Sin Medico"; }
    }

	//Funcion que retorna los medicamentos suministrados a un paciente (Parametros: historia, ingreso, fecha, contador)
    function traer_medicamentos($whis, $wing, $wfecha, &$i){
        global $conex;
        global $wbasedato;
        global $wcenmez;

		$medi;
        //Traigo los Kardex GENERADOS con articulos de DISPENSACION
        $q = " SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
            ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 "
            ."  WHERE kadhis  = '".$whis."'"
            ."    AND kading  = '".$wing."'"
            ."    AND kadfec  = '".$wfecha."'"
            ."    AND kadest  = 'on' "
            ."    AND kadart  = artcod "
            ."    AND kadori  = 'SF' "
            ."    AND kadper  = percod "
            ."    AND kadhis  = karhis "
            ."    AND kading  = karing "
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' "
            ." UNION "
            //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS
            ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
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
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' "
            ." UNION "
            //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION
            ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
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
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' "
            ." UNION "
            //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS
            ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
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
            ."    AND karcon  = 'on' "
            ."    AND karcco  = kadcco "
            ."    AND kadsus != 'on' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0){
            for ($i=1; $i <= $wnum;$i++){
                $row = mysql_fetch_array($res);

                $wartic[$i] = $row[0];                                 //Medicamento
                $wdosis[$i] = $row[1]." ".$row[7];                     //Dosis y fracciones de la dosis
                if ($row[5] > 1)
                { $wfrecu[$i]="Cada ".$row[5]."&nbsp;".$row[6]."S"; }  //Descripcion de la FRECUENCIA
                else
                { $wfrecu[$i]="Cada ".$row[5]."&nbsp;".$row[6]; }      //Descripcion de la FRECUENCIA
                $wfecin[$i] = $row[3];                                 //Fecha de Inicio
                $whorai[$i] = $row[4];                                 //Hora de Inicio

                if (trim($row[8]) != "")            //Tiene Condicion
                {
                    $q = " SELECT condes "
                        ."   FROM ".$wbasedato."_000042 "
                        ."  WHERE concod = '".$row[8]."'";
                    $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                    $row = mysql_fetch_array($rescon);

                    $wcondi[$i] = $row[0];          //Condicion
                }
                else
                { $wcondi[$i]=""; }
                $wobserv[$i]  = @$row[10];           //Observaciones
            }
			$medi['articulo'] = $wartic;
			$medi['dosis'] = $wdosis;
			$medi['frecuencia'] = $wfrecu;
			$medi['fecha_inicio'] = $wfecin;
			$medi['hora_inicio'] = $whorai;
			$medi['condiciones'] = $wcondi;
			$medi['observaciones'] = $wobserv;
			return $medi;
        }
        else
			return "Sin Medicamentos";
    }

	//Funcion que retorna los examenes realizados a un paciente (Parametros: historia, ingreso, fecha, contador, contador, tres parametros por referencia )
    function traer_examenes($whis, $wing, $wfecha, &$i, &$wser, &$wexa, &$wfes){
        global $conex;
        global $wbasedato;

        $q = " SELECT cconom, ekaobs, ekafes "
            ."   FROM ".$wbasedato."_000050, ".$wbasedato."_000011 "
            ."  WHERE ekahis = '".$whis."'"
            ."    AND ekaing = '".$wing."'"
            ."    AND ekafec = '".$wfecha."'"
            ."    AND ekaest = 'P' "              //Solo traigo los pendientes
            ."    AND ekacod = ccocod "
            ."  ORDER BY 1, 2, 3 ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $wnum = mysql_num_rows($res);

        if ($wnum > 0){
            for ($i=1; $i <= $wnum;$i++){
                $row = mysql_fetch_array($res);
                $wser[$i] = $row[0];
                $wexa[$i] = $row[1];
                $wfes[$i] = $row[2];
            }
        }
    }

	//Funcion que retorna la entidad responsable de un paciente (Parametros: historia, ingreso)
	function traer_entidad_responsable($whis, $wing){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		//traer lista de auditorias para el paciente
		$query = "	SELECT 		Ingnre as entidad, Ingres as codigo "
				 ."	  FROM 		".$wbasedato."_000016 "
				 ."  WHERE		Inghis = '".$whis."'"
				 ."    AND		Inging = '".$wing."'";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$entidad;
		if ($num > 0 ){
				$row = mysql_fetch_assoc($res);
				$entidad['nombre'] = $row['entidad'];
				$entidad['codigo'] = $row['codigo'];
				return $entidad;
		}
		$entidad['nombre'] = "No registra";
		$entidad['codigo'] = 0;
		return $entidad;
	}

	//Funcion que retorna la diferencia en dias entre dos fechas
	function calcularDiferenciaFechas($fecha1, $fecha2){
		global $conex;
		if( !isset($fecha1) && !isset($fecha2) ){
			return;
		}
		$query = "SELECT DATEDIFF(  '".$fecha1."',  '".$fecha2."' ) as diferencia";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$diferencia = "";
		if($num > 0){
			$row = mysql_fetch_assoc($res);
			$diferencia = abs($row['diferencia']);
		}
		return $diferencia;
	}

	//Funcion que retorna la cantidad de dias que lleva un paciente en la clinica y en el servicio (Parametros: historia, ingreso, servicio)
	function traer_dias_estancia( $whis, $wing, $wcco = '' ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$diff;
		//CALCULANDO DIAS DE ESTANCIA EN LA CLINICA
		$query = "	SELECT 		CONCAT ( ING.Fecha_data, ' ', ING.Hora_data ) as ingreso, CONCAT ( Ubifad, ' ', Ubihad ) as egreso"
				 ."	  FROM 		".$wbasedato."_000016 ING, ".$wbasedato."_000018 UBI"
				 ."  WHERE		Inghis = '".$whis."'"
				 ."    AND		Inging = '".$wing."'"
				 ."    AND 		Inghis = Ubihis "
				 ."    AND 		Inging = Ubiing";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$estancia_clinica;
		if ($num > 0 ){
				$row = mysql_fetch_assoc($res);
				$estancia_clinica['ingreso_clinica'] = $row['ingreso'];
				$estancia_clinica['egreso_clinica'] = $row['egreso'];
		}
		$pos = strpos($estancia_clinica['egreso_clinica'], 'asdf');
		//No le han dado de alta, entonces se calcula la diferencia de dias hasta hoy
		if (isset($pos)) {
			$estancia_clinica['egreso_clinica'] = date('Y-m-d H:i:s');
		}
		$diff['dias_estancia_clinica'] = calcularDiferenciaFechas( $estancia_clinica['egreso_clinica'], $estancia_clinica['ingreso_clinica'] );

		//CALCULANDO DIAS DE ESTANCIA EN EL SERVICIO
		//Buscar el ultimo ingreso y el posible ultimo egreso del paciente en el servicio
		$query ="SELECT 	CONCAT ( A.Fecha_data, ' ', A.Hora_data ) as ingreso, CONCAT ( B.Fecha_data, ' ', B.Hora_data ) as egreso
				   FROM 		".$wbasedato."_000032 A LEFT JOIN ".$wbasedato."_000033 B ON (     A.Historia_clinica = B.Historia_clinica
																			   AND A.Num_ingreso = B.Num_ingreso
																			   AND A.Servicio = B.Servicio
																			   AND A.Num_ing_serv = B.Num_ing_serv    )
				  WHERE		A.Historia_clinica = '".$whis."'
				    AND		A.Num_ingreso = '".$wing."'
				    AND     A.Servicio = '".$wcco."'
			   ORDER BY     A.Fecha_data DESC, A.Hora_data DESC LIMIT 1";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$est_servicio = array();
		if ($num > 0 ){
				$row = mysql_fetch_assoc($res);
				$est_servicio['ingreso_servicio'] = $row['ingreso'];
				$est_servicio['egreso_servicio'] = $row['egreso'];
		}
		if( empty( $est_servicio) == false ){
			//No ha salido del servicio, se calcula la diferencia de dias hasta hoy
			if ( is_null( $est_servicio['egreso_servicio'] ) ){
				$est_servicio['egreso_servicio'] = date('Y-m-d H:i:s');
			}
			$diff['dias_estancia_servicio'] = calcularDiferenciaFechas( $est_servicio['egreso_servicio'], $est_servicio['ingreso_servicio'] );
		}else{
			$diff['dias_estancia_servicio'] = 0;
		}
		return $diff;
	}

	//Funcion que retorna la evolucion diaria desde hce (Parametro: historia, ingreso)
	function traer_evolucion_hce($whis, $wing){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;
		global $wbaseHce;

		$tabla_evolucion = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce_evolucion_tabla");
		$campos_evolucion = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce_evolucion_campos");
		//TERMINAR:  Cuando se necesiten traer varios campos desde hce
		//$campos_evolucion_array = explode(",", $campos_evolucion);

		//CALCULANDO DIAS DE ESTANCIA EN LA CLINICA
		$query = "	SELECT 		movtip as tipo, movdat as dato"
				 ."	  FROM 		".$wbaseHce."_".$tabla_evolucion
				 ."  WHERE		movcon = '".$campos_evolucion."' "
				 ."    AND		movhis = '".$whis."'"
				 ."    AND		moving = '".$wing."'";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
				$row = mysql_fetch_assoc($res);
				$dato['tipo'] =  $row['tipo'];
				$dato['dato'] =  $row['dato'];
				return $dato;
		}
		return null;
	}

	function mostrarListaDeIngresos( $whis ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		//PARA LA INFORMACION DEL PACIENTE
		 $q = " SELECT CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento "
            ."   FROM root_000036, root_000037 "
            ."  WHERE orihis = '".$whis."'"
            ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$paciente = "";
		$nacimiento = "";
		$documento = "";

		if( $num > 0 ){
			$row = mysql_fetch_assoc($res);
			$paciente = $row['paciente'];
			$nacimiento = $row['nacimiento'];
			$documento = $row['tipo_identificacion']." ".$row['numero_documento'];
			echo '<div id="info_paciente">';
			echo "<table align='center'>";
			echo "<tr class='encabezadotabla'>";
			echo "<td colspan=2 align='center'>Información del paciente</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='encabezadotabla'>Historia</td><td class='fila1'>".$whis."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='encabezadotabla'>Documento</td><td class='fila1'>".$row['tipo_identificacion']." ".$row['numero_documento']."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='encabezadotabla' align='left'>F. nacimiento</td><td class='fila1'>".$row['nacimiento']."</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='encabezadotabla' align='left' nowrap=nowrap>Paciente</td><td class='fila1'>".$row['paciente']."</td>";
			echo "</tr>";


		}else{
			echo "No se encontraron datos de la historia";
			return;
		}

		$habitacion = "";
		$q = " SELECT  habcod as habitacion "
			."   FROM  ".$wbasedato."_000020 "
			."  WHERE  habhis  = '".$whis."' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		if( $num > 0 ){
			$row = mysql_fetch_assoc($res);
			$habitacion = $row['habitacion'];
		}


		//PARA LA LISTA DE INGRESOS DEL PACIENTE
		$q = " SELECT  Inging as ingreso, Fecha_data as fecha "
			."   FROM  ".$wbasedato."_000016 "
			."  WHERE  Inghis  = '".$whis."' "
			."	ORDER BY fecha";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		//echo '<div id="radios_ing" align="center">';
		if( $num > 0 ){
			echo "<tr><td colspan=2 class='encabezadotabla' align='center'>Lista de ingresos</td></tr>";
			echo "<tr class='fila2'><td colspan=2>";
				echo "<table width='100%'>";
				echo "<tr class='encabezadotabla'><td>Ingreso</td><td>Fecha</td><td>&nbsp;</td></tr>";
				$wclass = "fila1";
			$i=1;
			while($row = mysql_fetch_assoc($res)){
				($wclass=='fila1') ? $wclass='fila2' : $wclass='fila1';
				$bgcolor="";
				if( $i == $num ){
					//SI ESTA HOSPITALIZADO CAMBIA DE COLOR PARA DECIR QUE ESTA ACTIVO
					$qq = " SELECT  ubihis "
						."   FROM  ".$wbasedato."_000018 "
						."  WHERE  ubihis  = '".$whis."' "
						."    AND  ubiing = '".$row['ingreso']."'"
						."    AND  ubiald != 'on' "
						."    AND  ubiptr != 'on' ";

					$res1 = mysql_query($qq,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num1 = mysql_num_rows($res1);
					if( $num1 > 0 )
						$bgcolor=' bgcolor="#DAFFE6" ';
				}

				echo "<tr class='".$wclass."' >";
				echo "<td ".$bgcolor.">".$row['ingreso']."</td>";
				echo "<td ".$bgcolor.">".$row['fecha']."</td>";

				$medicos = traer_medico_tte($whis, $row['ingreso'],  $num);
				$onclic = "realizarConsultaPaciente('".$whis."', '".$row['ingreso']."', '', '".$paciente."', '".$documento."', '".$habitacion."', '".$nacimiento."', '".$medicos."' )";
				echo '<td '.$bgcolor.'> <a href="#info_paciente" onclick="'.$onclic.'">Ver</a> </td>';

				echo "</tr>";
				$i++;
			}
			echo "</td></tr>";
		}
		//echo '</div>';


		echo "</table>";
		echo "</div>";
	}
	//Funcion que muestra los pacientes que se encuentran en el servicio (Parametros: servicio, fecha, entidad responsable )
    function mostrarListaServicio( $wcco, $wfec, $wresponsable = '', $whistoria = '' ){

        global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$and_historia = "";
		if( $whistoria != '')
			$and_historia ="  AND ubihis= ".$whistoria;

        //Selecciono todos los pacientes del servicio seleccionado
        $q = " SELECT habcod as habitacion, habhis as historia, habing as ingreso, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento, Nechis as tiene_necesidad "
            ."   FROM ".$wbasedato."_000018, root_000036, root_000037, "
			.$wbasedato."_000020 A LEFT JOIN ".$wbasedato."_000149 B ON (habhis=nechis AND habing=necing) "
            ."  WHERE habcco  = '".$wcco."'"
            ."    AND habali != 'on' "            //Que no este para alistar
            ."    AND habdis != 'on' "            //Que no este disponible
            ."    AND habcod  = ubihac "
            ."    AND ubihis  = orihis "
			.$and_historia
            ."    AND ubiing  = oriing "
            ."    AND ubiald != 'on' "
            ."    AND ubiptr != 'on' "
            ."    AND ubisac  = '".$wcco."'"
            ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
            ."    AND habhis  = ubihis "
            ."    AND habing  = ubiing "
            ."  GROUP BY habcod, habhis, habing "
            ."  ORDER BY Habord, Habcod ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

		$whabant = "";

        if ($num <= 0){
			//Consultar si es de urgencias o cirugia
			 $q = " SELECT '' as habitacion, ubihis as historia, ubiing as ingreso, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento, Nechis as tiene_necesidad "
            ."   FROM  root_000036, root_000037, ".$wbasedato."_000011, "
			.$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000149 ON (ubihis=nechis AND ubiing=necing) "
            ."  WHERE ubihis  = orihis "
			.$and_historia
            ."    AND ubiing  = oriing "
            ."    AND ubiald != 'on' "
            ."    AND ubisac  = '".$wcco."'"
            ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
			."    AND ccocod = ubisac "
			."    AND (ccourg = 'on' OR ccocir = 'on')";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			if( $num <= 0 ){
				if( $whistoria != '' ){
					echo "El paciente no se encuentra en el servicio <br> A continuacion la lista de ingresos <br>";
					mostrarListaDeIngresos( $whistoria );
					return;
				}else{
					echo "No hay pacientes en el servicio";
					return;
				}
			}

		}

			$titulo = "Resultados para el servicio ".$wcco." el dia ".$wfec;
			if($wresponsable != "") $titulo.= " de la entidad ".$wresponsable;
			echo "<br><br><br>";
			echo "<center>";
			echo "<table>";
			echo "<tr class='encabezadoTabla'>";
			echo "<th colspan=10>".$titulo."</th>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla'>";
			echo "<th>Habitacion</th>";
			echo "<th>Historia</th>";
			echo "<th>Ingreso</th>";
			echo "<th>Paciente</th>";
			echo "<th>Médico(s) Tratante(s)</th>";
			echo "<th>Entidad Responsable</th>";
			echo "<th>Dias de Estancia<br>en la clinica</th>";
			echo "<th>Dias de Estancia<br>en el servicio</th>";
			echo "<th>Tiene<br>Educación</th>";
			echo "<th>&nbsp;</th>";
			echo "</tr>";
			$i=0;
            while($dato = mysql_fetch_assoc($res)) {
				$i++;

				$entidad_responsable = traer_entidad_responsable($dato['historia'],$dato['ingreso']);
				if( $wresponsable != '' && $entidad_responsable['codigo'] != $wresponsable)
					continue;

				$j=0;
				$dato['medico'] = traer_medico_tte($dato['historia'], $dato['ingreso'], $j);
               /* if ($dato['medico']=="Sin Medico"){         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
                    $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60)
                    $wayer = date('Y-m-d', $dia); //Formatea dia
                    $dato['medico'] = traer_medico_tte($dato['historia'], $dato['ingreso'], $wayer, &$j);
                }*/
                if (($i%2) == 0)
					$wclass="fila1";
                else
					$wclass="fila2";

				$dias_estancia = traer_dias_estancia($dato['historia'],$dato['ingreso'], $wcco);

				echo "<tr class=".$wclass.">";
                echo "<td align=center><b>".$dato['habitacion']."</b></td>"; //habitacion
                echo "<td align=center>".$dato['historia']."</td>"; //historia
				echo "<td align=center>".$dato['ingreso']."</td>"; //ingreso
                echo "<td align=left  >".$dato['paciente']."</td>"; //paciente

                echo "<td align=left  ><b>".$dato['medico']."</b></td>"; //medico tratante
                echo "<td align=left>".$entidad_responsable['nombre']."</td>"; //entidad responsable
				echo "<td align=center>".$dias_estancia['dias_estancia_clinica']."</td>"; //dias estancia clinica
				echo "<td align=center>".$dias_estancia['dias_estancia_servicio']."</td>"; //dias estancia servicio
				$imagen="&nbsp;";
				if( $dato['tiene_necesidad'] != ""){
					$imagen = "<img src='/matrix/images/medical/movhos/checkmrk.ico' />";
				}
				echo "<td align='center'>".$imagen."</td>";
                echo "<td align=center><A HREF='#resultados_lista' onClick= \" javascript:realizarConsultaPaciente('".$dato['historia']."','".$dato['ingreso']."','".$wcco."','".$dato['paciente']."','".$dato['numero_documento']."','".$dato['habitacion']."','".$dato['nacimiento']."','".$dato['medico']."')  \" class=tipo3V>Ver</A></td>";

                echo "</tr>";

            }
			echo "</table>";
			echo "</center>";

    }

	//Funcion que retorna los datos del kardex de un paciente (Parametros: historia, ingreso, fecha)
    function query_kardex($whis, $wing ){
        global $conex;
        global $wbasedato;

		$resultados = array();

        $q = " SELECT Fecha_data as fecha, karobs, kardia, kartal, karpes, karale, karcui, karter, karson, karcur, "
             ."        karint, kardie, karmez, kardem, karcip, kartef, karrec, karanp, karais "
             ."   FROM ".$wbasedato."_000053 A "
             ."  WHERE karhis = '".$whis."'"
             ."    AND karing = '".$wing."'"
             ."    AND karest = 'on' "
			 ." ORDER BY Fecha_data desc limit 2 ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row = mysql_fetch_assoc($res)){
			array_push( $resultados, $row );
		}
		return $resultados;
    }

	function consultarIngresoEnfermeria($whistoria, $wingreso){

		global $conex;
		global $wbasedato;
		global $wemp_pmla;

		$paciente = array();
		 $q = "SELECT CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as nombre, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento, pacsex as genero "
			."   FROM root_000036, root_000037 "
			."  WHERE orihis = '".$whistoria."'"
			."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
			."    AND oriced  = pacced "
			."    AND oritid  = pactid ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			if( $num > 0 ){
				$paciente = mysql_fetch_assoc($res) ;
			}

		//Buscar fecha de ingreso y egreso del paciente
		$q = "SELECT  A.Fecha_data as fec_ing, B.ubifad as fec_egr "
			."   FROM ".$wbasedato."_000016 A, ".$wbasedato."_000018 B "
			."  WHERE ubihis = '".$whistoria."'"
			."    AND ubiing = '".$wingreso."'"
			."    AND ubihis  = inghis "
			."    AND ubiing  = inging ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		if( $num > 0 ){
			$row = mysql_fetch_assoc($res);
			if( $row['fec_egr'] == '0000-00-00' )
				$row['fec_egr'] = date('Y-m-d');

			$paciente['fecha_ingreso'] = $row['fec_ing'];
			$paciente['fecha_fin'] = $row['fec_egr'];
		}

		mostrarFormularioIngresoEnfermeria($whistoria, $wingreso, $paciente['nombre'], $paciente['genero'], $paciente['fecha_ingreso'], $paciente['fecha_fin']);

	}

	function mostrarFormularioIngresoEnfermeria($whis, $wing, $wpaciente, $wsex, $wfechai, $wfechaf){

		global $conex;
		global $wbaseHce;
		global $wemp_pmla;

		$tabla_notas = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce_ingreso_enfermeria_tabla");

		$usuario = $_SESSION['user'];
		$pos = strpos($usuario,"-");
		$usuario = substr($usuario,$pos+1,strlen($usuario));


		$empresa = $wbaseHce;
		$i=0;
		$paquetes = array();
		$paquetes[$i] = $tabla_notas; //Formulario de ingreso de enfermeria
		$en = "'".$paquetes[$i]."'";
		$key = $usuario;
		$wintitulo = "Historia: ".$whis." Ingreso: ".$wing." Paciente:".$wpaciente;
		$Hgraficas = " |";
		$CLASE = "C";
		$whtml = 0;

		//                                        0                                              1                          2                                                3                                                  4                           5                          6                           7                          8                          9                          10                       11                                              12                         13                         14                         15                         16                         17
		$queryI  = " select ".$empresa."_000002.Detdes,".$empresa."_".$paquetes[$i].".movdat,".$empresa."_000002.Detorp,".$empresa."_".$paquetes[$i].".fecha_data,".$empresa."_".$paquetes[$i].".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$paquetes[$i].".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir from ".$empresa."_".$paquetes[$i].",".$empresa."_000002,".$empresa."_000001 ";
		$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' ";
		$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
		$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
		$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' ";
		$queryI .= "   and ".$empresa."_".$paquetes[$i].".movpro=".$empresa."_000002.detpro ";
		$queryI .= "   and ".$empresa."_".$paquetes[$i].".movcon = ".$empresa."_000002.detcon ";
		$queryI .= "   and ".$empresa."_000002.detest='on' ";
		$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' ";
		$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro ";

		imprimir($conex,$empresa,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,$whtml);
	}

	//Al elegir (boton ver) un paciente de la lista se muestran todos los datos relacionados al paciente
	//(Parametros: historia, ingreso, servicio, fecha, nombre del paciente, documento del paciente, codigo habitacion, fecha nacimiento y medicos tratantes)
	function mostrarDetallePaciente($whistoria, $wingreso, $wservicio, $wfecha, $wpaciente, $wdoc_paciente, $whabitacion, $wnacimiento, $wmedico){

		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wbaseHce;

		$wfecha_actual = date("Y-m-d");
		//Fecha a consultar para todas los datos del kardex
		$wfec_con = date("Y-m-d");
		$wfecha = date("Y-m-d");
		$wmensaje = "Kardex Actualizado a la fecha";

		$resul = query_kardex($whistoria, $wingreso);
		$num = sizeof( $resul );
		/*if ($num == 0){                         //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
			$dia = time()-(1*24*60*60);               //Resta un dia (2*24*60*60)
			$wayer = date('Y-m-d', $dia);             //Formatea dia
			$wfec_con=$wayer;                         //Fecha a consultar para todas los datos del kardex
			$wmensaje="Kardex SIN Actualizar a la fecha";
			$resul = query_kardex($whistoria, $wingreso, $wfec_con);
			$num = sizeof( $resul );
		}*/
		if ($num > 0){
			$row = $resul[0];
			$wfec_con = $row['fecha'];
			//Calculo la edad
			$wfnac=(integer)substr($wnacimiento,0,4)*365 +(integer)substr($wnacimiento,5,2)*30 + (integer)substr($wnacimiento,8,2);
			$wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
			$weda=(($wfhoy - $wfnac)/365);
			if ($weda < 1)
				$weda = number_format(($weda*12),0,'.',',')."<b> Meses</b>";
			else
				$weda=number_format($weda,0,'.',',')." Años";

			echo "<br><br>";

			echo "<center>";
			echo "<table>";
			echo "<tr class=encabezadoTabla>";
			echo "<th><font size=3 color='FFFF33'><b>".$wmensaje." ".$wfec_con."</b></font></th>";
			echo "</tr>";
			echo "</table>";
			echo "<table>";
			echo "<tr class=fila1>";
			echo "<th><font size=3>Habitación "."</font></th>";
			echo "<th><font size=3>Documento</font></th>";
			echo "<th><font size=3>Historía</font></th>";
			echo "<th><font size=3>Nombre</font></th>";
			echo "<th><font size=3>Edad</font></th>";
			echo "<th><font size=3>Talla</font></th>";
			echo "<th><font size=3>Peso</font></th>";
			echo "</tr>";
			echo "<tr class=fila2>";
			echo "<td bgcolor=333399 align=center><b><font size=5 color='00FF00'>".$whabitacion."</font></b></td>";
			echo "<td align=center>".$wdoc_paciente."</td>";
			echo "<td align=center>".$whistoria."</td>";
			echo "<td align=center><font size=4><b>".$wpaciente."&nbsp&nbsp</b></font></td>";
			echo "<td align=center><font size=4><b>".$weda."</b></td>";
			echo "<td align=center>".$row["kartal"]."</td>";
			echo "<td align=center>".$row["karpes"]." Kg</td>";
			echo "</tr>";
			echo "</table>";
			echo "<br>";

			echo "<table width='95%'>";

			$wdiag=traer_diagnostico($whistoria, $wingreso, $wfecha);
			if ($wdiag=="Sin Diagnostico"){    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
				$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
				$wayer = date('Y-m-d', $dia); //Formatea dia
				$wdiag=traer_diagnostico($whistoria, $wingreso, $wayer);
			}
			$wdiag = str_replace( '\'', '', $wdiag );
			$wdiag = str_replace( '\"', '', $wdiag );
			//Diagnostico y Medico tratante
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center colspan=3>Diagnostico(s)</td>";
			echo "<td align=center colspan=4>Médico(s) Tratantes</td>";
			echo "</tr>";
			echo "<tr class=fila2>";
			echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly >".$wdiag."</textarea></td>";
			echo "<td align=center colspan=4 class=tipoMx>".$wmedico."</td>";
			echo "</tr>";
			//Antecedentes Personales
			if (trim($row["karanp"]) != "" or trim($row["karale"]) != ""){
				echo "<tr class=encabezadoTabla>";
				echo "<td colspan=3 align=center><b>ANTECEDENTES PERSONALES</b></td>";
				echo "<td colspan=4 align=center><b>ANTECEDENTES ALERGICOS</b></td>";
				echo "</tr><tr class=fila2>";
				echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly >".$row["karanp"]."</textarea></td>";
				echo "<td align=center colspan=4><textarea rows=3 cols=60 readonly >".$row["karale"]."</textarea></td>";
				echo "</tr><tr class=fila2>";
				echo "</tr>";
			}

			$wser; $wexa; $wfes;
			$j=0;
			traer_examenes($whistoria, $wingreso, $wfec_con, $j, $wser, $wexa, $wfes);   //Esto lo hago aca arriba porque necesito saber si tiene examenes para sacar o no el titulo de CONTROLES
			if ($j > 0){
				//Controles ********************
				echo "<tr class=encabezadoTabla>";
				echo "<td colspan=7 align=center><font size=4>CONTROLES</font></td>";
				echo "</tr>";
				//Examenes
				//traer_examenes($whis, $wing, $wfec_con, &$j);
				echo "<tr class=encabezadoTabla>";
				echo "<td colspan=7 align=center><b>EXAMENES</b></td>";
				echo "</tr>";
				echo "<tr class=fila1>";
				echo "<td align=center colspan=2><b>Examen</b></td>";
				echo "<td align=center colspan=2><b>Observaciones</b></td>";
				echo "<td align=center><b>Fecha</b></td>";
				echo "<td align=center colspan=2><b>Estado</b></td>";
				echo "</tr>";
				if ($j > 0){
					for ($k=1; $k < $j; $k++){
						echo "<tr class=fila2>";
						echo "<td colspan=2>".$wser[$k]."</td>";
						echo "<td colspan=2><textarea rows=2 cols=60 readonly >".$wexa[$k]."</textarea></td>";
						echo "<td align=center>".$wfes[$k]."</td>";
						echo "<td align=center colspan=2>Pendiente</td>";
						echo "</tr>";
					}
				}

			}
			//Cirugias e Interconsultas
			if (trim($row["karcip"]) != "" or trim($row["karint"]) != ""){
				echo "<tr class=fila1>";
				echo "<td colspan=4 align=center><b>CIRUGIAS</b></td>";
				echo "<td colspan=3 align=center><b>INTERCONSULTAS</b></td>";
				echo "</tr>";
				echo "<tr class=fila2>";
				echo "<td align=center colspan=4><textarea rows=3 cols=60 readonly >".$row["karcip"]."</textarea></td>";
				echo "<td align=center colspan=3><textarea rows=3 cols=60 readonly >".$row["karint"]."</textarea></td>";
				echo "</tr>";
			}
			//Rehabilitacion Cardiaca y Antecedentes Personales
			if (trim($row["karter"]) != "" or trim($row["karrec"]) != "" or trim($row["kartef"]) != ""){
				echo "<tr class=fila1>";
				echo "<td colspan=3 align=center><b>TERAPIA RESPIRATORIA</b></td>";
				echo "<td colspan=2 align=center><b>REHABILITACION CARDIACA</b></td>";
				echo "<td colspan=2 align=center><b>TERAPIA FISICA</b></td>";

				$wterres=str_replace("\n","<br>",htmlentities($row["karter"],ENT_QUOTES));
				$wreacar=str_replace("\n","<br>",htmlentities($row["karrec"],ENT_QUOTES));
				$wterfis=str_replace("\n","<br>",htmlentities($row["kartef"],ENT_QUOTES));

				echo "</tr>";
				echo "<tr class=fila2>";
				echo "<td align=left colspan=3>".$wterres."</td>";
				echo "<td align=left colspan=2>".$wreacar."</td>";
				echo "<td align=left colspan=2>".$wterfis."</td>";
				echo "</tr>";
			}
			$j=0;
			//Medicamentos
			$medicamentos = traer_medicamentos($whistoria, $wingreso, $wfec_con, $j);
			if ( is_array( $medicamentos ) ){
				$wartic = $medicamentos['articulo'];
				$wdosis = $medicamentos['dosis'];
				$wfrecu = $medicamentos['frecuencia'];
				$wfecin = $medicamentos['fecha_inicio'];
				$whorai = $medicamentos['hora_inicio'];
				$wcondi = $medicamentos['condiciones'];
				$wobserv = $medicamentos['observaciones'];
			}
			echo "<tr class=encabezadoTabla>";
			echo "<td colspan=7 align=center><font size=4><b>MEDICAMENTOS</b></font></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center><b>Medicamento</b></td>";
			echo "<td align=center><b>Dosis</b></td>";
			echo "<td align=center><b>Frecuencia</b></td>";
			echo "<td align=center><b>Fecha Inicial</b></td>";
			echo "<td align=center><b>Hora de Inicio</b></td>";
			echo "<td align=center><b>Condición</b></td>";
			echo "<td align=center><b>Observaciones</b></td>";
			echo "</tr>";
			if ($j > 0){
				for ($k=1; $k < $j; $k++){
					$whora1 = explode(":",$whorai[$k]);           //Para solo mostrar el numero de la hora, sin los ceros (00:00)
					if (is_int ($k / 2))
					{$wclass = "fila1";}
					else
					{$wclass = "fila2";}
					echo "<tr class=".$wclass.">";
					echo "<td>".$wartic[$k]."</td>";               //Articulo
					echo "<td align=center>".$wdosis[$k]."</td>";  //Dosis
					echo "<td align=center>".$wfrecu[$k]."</td>";  //Frecuencia
					echo "<td align=center>".$wfecin[$k]."</td>";  //Fecha de Inicio
					echo "<td align=center>".$whora1[0]."</td>";   //Hora de Inicio
					echo "<td align=center>".$wcondi[$k]."</td>";  //Condicion
					echo "<td align=center><textarea row=3 col=30 readonly>".strip_tags($wobserv[$k])."</textarea></td>";                //Observacion
					echo "</tr>";
				}
			}
			//Observaciones Generales
			if (trim($row["karobs"]) != ""){
				echo "<tr class=fila1>";
				echo "<td colspan=7 align=center><b>OBSERVACIONES GENERALES</b></td>";
				echo "</tr>";
				echo "<tr class=fila2>";
				echo "<td align=center colspan=7><textarea rows=3 cols=120 readonly >".$row["karobs"]."</textarea></td>";
				echo "</tr>";
			}
			//EVOLUCION MEDICA
			$evolucion = consultarUltimoDiagnosticoHCE( $conex, $wemp_pmla, $wbaseHce, $whistoria, $wingreso );
			if ( is_null( $evolucion ) == false ){
				echo "<tr class='encabezadoTabla'>";
				echo "<td colspan=7 align=center><font size=4><b>EVOLUCION MEDICA</b></font></td>";
				echo "</tr>";
				echo "<tr class=fila1>";
				echo "<td align=center colspan=7><textarea rows=3 cols=120 readonly >".$evolucion."</textarea></td>";
				echo "</tr>";
			}


			echo "<tr class='encabezadoTabla'>";
			echo "<td colspan=7 align=center><font size=4><b>CONSULTAR INGRESO ENFERMERIA</b></font></td>";
			echo "</tr>";

			echo "<tr class='fila2' >";
			echo "<td align='center' colspan=7>";

			echo "<a id='enlaceveringreso' onclick='consultarIngresoEnfermeria()' href='#formulario_ingreso' >Ver Ingreso Enfermeria</a>";
			echo "<div id='formulario_ingreso'>";

			echo "</div>";
			echo "</td>";
			echo "</tr>";

		}else{  //del 2do if ($num > 0)
			 echo "<br><br>";
			 echo "<center><table>";
			 echo "<tr class=encabezadoTabla><td>No existen datos de kardex para el paciente con la historia ".$whistoria." ".$wpaciente.", ingreso ".$wingreso."</td></tr>";
			 echo "</table></center><br><br>";
			 echo "<table width='95%'><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
		}
			//********************NECESIDADES DEL PACIENTE***************************

			$crear_necesidades = true;

			//2013-05-06  -->Se solicita que permita crear necesidades a pacientes egresados
			//SI ESTA HOSPITALIZADO PERMITE CREAR NUEVA NECESIDAD
			/*$q = " SELECT  ubihis "
				."   FROM  ".$wbasedato."_000018 "
				."  WHERE  ubihis  = '".$whistoria."' "
				."    AND  ubiing = '".$wingreso."'"
				."    AND  ubiald != 'on' "
				."    AND  ubiptr != 'on' ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			if( $num <= 0 )
				$crear_necesidades = false;*/


			$titulo_necesidad = "Crear nueva necesidad";

			$formulario_necesidad ="<div><table id='formulario_necesidad' bgcolor='#ffffff' border=0 align='center' width='95%'>";
			$formulario_necesidad.="<tr class='encabezadoTabla'><td colspan=3><b>BARRERAS DEL PACIENTE Y/O FAMILIA</b></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td class='fila2' colspan=3>Conoce y acepta su diagnóstico</td></tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_conoce_diagnostico' value='Si'/>Si  <input type='radio' name='rd_conoce_diagnostico' value='No' checked='checked' />No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_conoce_diagnostico') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea id='val_conoce_diagnostico' rows=4 cols=120 disabled='disabled'></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>Considera que su condición mejorará</td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=2><input type='radio' name='rd_condicion_mejorara' value='Si' />Si  <input type='radio' name='rd_condicion_mejorara' value='No' checked='checked' />No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_condicion_mejorara') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_condicion_mejorara' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>Acepta sus limitaciones</td></tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_acepta_limitaciones' value='Si' />Si  <input type='radio' name='rd_acepta_limitaciones' value='No' checked='checked' />No</td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_acepta_limitaciones') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_acepta_limitaciones' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>Estado de ánimo predominante</td></tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_estado_animo' value='Si' />Si  <input type='radio' name='rd_estado_animo' value='No' checked='checked' />No</td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_estado_animo') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_estado_animo' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Manejo y uso de medicamentos</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_manejo_uso_medicamentos' value='Si'>Si  <input type='radio' name='rd_manejo_uso_medicamentos' value='No' checked='checked'>No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_manejo_medicamentos') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea  disabled='disabled' id='val_manejo_medicamentos' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";
			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Manejo de dispositivos y equipos médicos</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_manejo_dispositivos' value='Si'>Si  <input type='radio' name='rd_manejo_dispositivos' value='No' checked='checked'>No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_manejo_dispositivos') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_manejo_dispositivos' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";
			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Interaccion potenciales entre medicamentos y alimentos</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_interacciones_potenciales' value='Si'>Si  <input type='radio' name='rd_interacciones_potenciales' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_interacciones_potenciales') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_interacciones_potenciales' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Dieta y nutrición</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_dieta_nutricion' value='Si'>Si  <input type='radio' name='rd_dieta_nutricion' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_dieta_nutricion') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_dieta_nutricion' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Manejo del dolor</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_manejo_dolor' value='Si'>Si  <input type='radio' name='rd_manejo_dolor' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_manejo_dolor') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_manejo_dolor' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Técnicas en rehabilitación</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_tecnicas_rehabilitacion' value='Si'>Si  <input type='radio' name='rd_tecnicas_rehabilitacion' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_tecnicas_rehabilitacion') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_tecnicas_rehabilitacion' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Derechos del paciente y normas de estancia</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_derechos_paciente' value='Si'>Si  <input type='radio' name='rd_derechos_paciente' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_derechos_paciente') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_derechos_paciente' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Otras</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_otras' value='Si'>Si  <input type='radio' name='rd_otras' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_otras') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_otras' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Consentimientos informados</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2><input type='radio' name='rd_consentimientos' value='Si'>Si  <input type='radio' name='rd_consentimientos' value='No' checked='checked' >No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_consentimientos') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_consentimientos' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3><b>Observaciones</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=2>&nbsp;</td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('au_obs') \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=3><textarea disabled='disabled' id='au_obs' rows=4 cols=120 ></textarea></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";


			//Se imprime la parte del formulario que guarda los compromisos de aprendizaje-actividades de educacion-responsable
			$formulario_necesidad.="<tr class='encabezadoTabla'>";
			$formulario_necesidad.="<td colspan=3 align='center'><font size=3><b>COMPROMISOS</b></font></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr class='fila2'>";
			$formulario_necesidad.="<td colspan=3 align='right'><input type='button' id='btn_crear_compromiso' value='+' onClick=\" javascript:crearCompromiso(false) \" /></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr id='tr_compromiso_original' class='encabezadoTabla'>";
			$formulario_necesidad.="<td><b>Objetivos de aprendizaje</b></td>";
			$formulario_necesidad.="<td><b>Actividades de educación y metodología</b></td>";
			$formulario_necesidad.="<td><b>Responsable</b></td>";
			$formulario_necesidad.="</tr>";
			$formulario_necesidad.="<tr id='tr_primer_compromiso' class='fila2'>";
			$formulario_necesidad.="<td><textarea rows=4 cols=33 class='evitar objetivo'></textarea></td>";
			$formulario_necesidad.="<td><textarea rows=4 cols=33 class='evitar actividad'></textarea></td>";
			$formulario_necesidad.="<td><textarea rows=4 cols=33 class='evitar responsable'></textarea></td>";
			$formulario_necesidad.="</tr>";


			if( $crear_necesidades == true )
				$formulario_necesidad.="<tr class='fila2'><td colspan=3 align='center'><input type='button' value='Guardar y salir' id='boton_guardar_necesidad'/></td></tr>";
			$formulario_necesidad.="</table></div>";




			$lista_necesidades = "<div id='listado_necesidad'><table border=0 align='center' width='100%' >";
			if( $crear_necesidades == true )
				$lista_necesidades.= "<tr><td align='right'><input type='button' value='Crear nueva' id='add_necesidad' /></td></tr>";
			$lista_necesidades.= "<tr class='encabezadoTabla'><td>Necesidades del paciente</td></tr>";

			//traer lista de auditorias para el paciente
			$query = "	SELECT 		Fecha_data as fecha"
					 ."	  FROM 		".$wbasedato."_000149 "
					 ."	 WHERE 		Nechis = '".$whistoria."'"
					 ."    AND  	Necing = '".$wingreso."'"
				."	  GROUP BY      Fecha_data "
				 ."	  ORDER BY 		Fecha_data";
			$num = 0;
			$res = mysql_query($query, $conex);
			$num = mysql_num_rows($res);
			if ($num > 0 ){
					$ii=0;
					while( $row = mysql_fetch_assoc($res) ){
						$wclass="fila2";
						$lista_necesidades.="<tr class='".$wclass."'><td align=center><A HREF='#formulario_necesidad' onClick= \" javascript:consultarNecesidades('".$whistoria."','".$wingreso."','".$row['fecha']."')  \" class=tipo3V>".$row['fecha']."</A></td></tr>";
					}
			}
			$lista_necesidades.= "</table></div>";

			echo "<tr class='fila2'>";
			echo "<td colspan=7 align=center>&nbsp;</td>";
			echo "</tr>";
			echo "<tr class='fila1'>";
			echo "<td colspan=7 align=center>&nbsp;</td>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla'>";
			echo "<td colspan=7 align=center><font size=4><b>NECESIDADES</b></font></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center><b>Listado</b></td>";
			echo "<td align=center colspan=6 id='titulo_formulario_necesidad'><b>".$titulo_necesidad."</b></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center valign=top>".$lista_necesidades."</td>";
			echo "<td align=center colspan=6>".$formulario_necesidad."</td>";
			echo "</tr>";
			/*echo "<tr class=fila1>";
			echo "<td align=center valign=top>&nbsp;</td>";
			echo "<td align=center colspan=6>".$form_objetivos."</td>";
			echo "</tr>";	*/
			echo "<tr class=fila1>";
			echo "<td colspan=7><b>&nbsp</b></td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";

			echo "<br>";

		echo "<br><br>";
}

	//Funcion que retorna la lista de entidades responsables
	function consultarEntidades(){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$caracteres = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü");
		$caracteres2 = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U");

		//traer lista de auditorias para el paciente
		$query = "	SELECT 		Epsnit as nit, Epscod as codigo, Epsnom as nombre "
				 ."	  FROM 		".$wbasedato."_000049 "
			 ."	  ORDER BY 		Epsnom";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$arreglo = array();
		if ($num > 0 ){
				while( $row = mysql_fetch_assoc($res) ){
					$row['nombre'] = str_replace( $caracteres, $caracteres2, $row['nombre'] );
					$row['nombre'] = utf8_decode( $row['nombre'] );
					array_push($arreglo,  trim($row['codigo']).", ".trim($row['nit']).", ".trim($row['nombre']) );
				}
		}
		return $arreglo;
	}

	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;

		$fecha_hoy = date("Y-m-d");
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		//XXXecho "<input type='hidden' id ='fecha_elegida' value='' />";
		echo "<input type='hidden' id ='servicio_elegido' value='' />";

		encabezado("EDUCACIÓN AL PACIENTE", $wactualiz, "clinica");

		echo "<center>";
		echo '<span class="subtituloPagina2">Parámetros de consulta</span>';
		echo "<center>";
		echo '<br><br>';

		echo "<table align='center'>";
		echo "<tr>";
		echo "<td colspan=2 class='fila1' width='150px'>Servicio</td>";
		echo "<td colspan=2 class='fila2'  align='center'>";
		$wccos = consultaCentrosCostos("ccohos, ccourg, ccocir");
		echo "<select id='servicio'> <option value=''>Seleccione</option>";
		foreach ($wccos as $centroCostos){
			echo "<option value='".$centroCostos->codigo."'>".$centroCostos->codigo."-".$centroCostos->nombre."</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=2 class='fila1' width='150px'>Entidad</td>";
		echo "<td colspan=2 class='fila2'  align='center'>";
		echo "<input type='text' id='responsable' style='width:100%'/>";
		//Imprime un json en una variable oculta con las entidades
		$wentid = consultarEntidades();
		$ent = json_encode( $wentid );
		echo "<input type='hidden' id='entidades_json' value='".$ent."' />";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=2 class='fila1' width='150px'>Historia</td>";
		echo "<td colspan=2 class='fila2'  align='center'>";
		echo "<input type='text' id='input_historia' style='width:100%'/>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan=2 class='fila1'>Consultar egresados</td>";
		echo "<td colspan=2 class='fila2'>";
		echo "<input type='checkbox' id='check_egresados' onclick='consultarEgresados(this)' />";
		echo "</td>";
		echo "</tr>";
		echo "<tr class='filaoculta'>";
		echo "<td colspan=2 class='fila1'>Fecha Inicio</td>";
		echo "<td colspan=2 class='fila2'>";
		echo "<input type='text' id='wfec_i' value='".date("Y-m")."-01' />";
		echo "</td>";
		echo "</tr>";
		echo "<tr class='filaoculta'>";
		echo "<td colspan=2 class='fila1'>Fecha Fin</td>";
		echo "<td colspan=2 class='fila2'>";
		echo "<input type='text' id='wfec_f' value='".date("Y-m-d")."' />";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<br><br>";
		echo '<input type="button" id="boton_consultar" value="Consultar"></input>';
		echo "<br><br>";
		echo '<center>';
		echo '<div id="resultados_lista"></div>';
		echo '<div id="resultados_paciente"></div>';
		echo '<br><br>';
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";
		echo "<br><br>";
		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()'>";
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
</style>
<style>
	#formulario_necesidad input[type="text"], #formulario_necesidad textarea{
	background-color:#DAFFE6
	}

	#formulario_necesidad input[type="text"]:disabled, #formulario_necesidad textarea:disabled{
		background-color:#ffffff;
		color: black;
	}
</style>
<script>
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
	var mostrando_paciente = false;
	var entidad_elegida = "";
	var historia_global = 0;
	var ingreso_global = 0;
	var necesidad_editable_global;
	var editando_global = false;

	//Funcion jquery para quitar o poner el color de fondo de los elementos disabled o readonly en internet explorer
	jQuery.fn.cssie = function() {
		$(this).each(function() {
			$(this)
				if ( $.browser.msie ) {
					if( $(this).css('background-color') == '#ffffff'){
						if(! $(this).attr("readonly") ){ $(this).css('background-color','#DAFFE6')};
					}else{
						if( $(this).attr("readonly") ){ $(this).css('background-color','#ffffff')};
					}
					if( $(this).is(':text') ){
						if( $(this).css('background-color') == '#ffffff'){
							if(! $(this).attr("disabled") )  $(this).css('background-color','#DAFFE6');
						}else{
							if( $(this).attr("disabled") ) $(this).css('background-color','#ffffff');
						}
					}
				}
			});
		return $(this);
	}

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#servicio").change(function() {
			realizarConsultaLista();
		});

		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});

		$("#boton_consultar").click(function() {
			realizarConsultaLista();
		});

		$("#wfec_i, #wfec_f").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+1D"
		});

		$(".filaoculta").hide();

		var entidades_array = new Array();
		//Selecciona la variable con las entidades y llena el arreglo para mostrarse en el input de "Entidad"
		var entidadesx = $("#entidades_json").val();
		datos = eval ( entidadesx );
		for( i in datos ){
			entidades_array.push( datos[i] );
		}
		//Autocompletar para las entidades responsables, cuando seleccione uno llama a buscarcodigoentidad
        $( "#responsable" ).autocomplete({
            source: entidades_array,
			minLength : 3
        });
	});

	//Busca el codigo de la entidad seleccionada en el autocomplete
	function buscarCodigoEntidad( nombre_entidad ){
		var datos = nombre_entidad.split(",");
		return datos[0];
	}

	//Funcion que asigna las acciones cuando se de click en los radios
	function validaciones_formulario_necesidad(){

		//Quitar las acciones que tengan asignadas al darle click a todos los input tipo radio
		$("#formulario_necesidad input[type='radio']").attr('onclick','').unbind('click');

		//CONOCE DIAGNOSTICO	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_conoce_diagnostico]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_conoce_diagnostico").attr("disabled", "disabled") : $("#val_conoce_diagnostico").attr("readonly", true).cssie();
				$("#val_conoce_diagnostico").val("");
			}else
				(! $.browser.msie ) ? $("#val_conoce_diagnostico").attr("disabled", false).focus() : $("#val_conoce_diagnostico").attr("readonly", false).cssie().focus()
		});

		//CONDICION MEJORARA	//Si: inhabilitar input text  --- No: habilitar input text
		$('input[name=rd_condicion_mejorara]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_condicion_mejorara").attr("disabled", "disabled") : $("#val_condicion_mejorara").attr("readonly", true).cssie();
				$("#val_condicion_mejorara").val("");
			}else
				(! $.browser.msie ) ? $("#val_condicion_mejorara").attr("disabled", false).focus() : $("#val_condicion_mejorara").attr("readonly", false).cssie().focus();
		});

		//ACEPTA LIMITACIONES	//Si: inhabilitar input text  --- No: habilitar input text
		$('input[name=rd_acepta_limitaciones]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_acepta_limitaciones").attr("disabled", "disabled") : $("#val_acepta_limitaciones").attr("readonly", true).cssie();
				$("#val_acepta_limitaciones").val("");
			}else
				(! $.browser.msie ) ? $("#val_acepta_limitaciones").attr("disabled", false).focus() : $("#val_acepta_limitaciones").attr("readonly", false).cssie().focus();
		});

		//ACEPTA LIMITACIONES	//Si: inhabilitar input text  --- No: habilitar input text
		$('input[name=rd_estado_animo]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_estado_animo").attr("disabled", "disabled") : $("#val_estado_animo").attr("readonly", true).cssie();
				$("#val_estado_animo").val("");
			}else
				(! $.browser.msie ) ? $("#val_estado_animo").attr("disabled", false).focus() : $("#val_estado_animo").attr("readonly", false).cssie().focus();
		});

		//MANEJO USO MEDICAMENTOS	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_manejo_uso_medicamentos]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_manejo_medicamentos").attr("disabled", "disabled") :  $("#val_manejo_medicamentos").attr("readonly", true).cssie();
				$("#val_manejo_medicamentos").val("");
			}else
				(! $.browser.msie ) ? $("#val_manejo_medicamentos").attr("disabled", false).focus() : $("#val_manejo_medicamentos").attr("readonly", false).cssie().focus();
		});

		//MANEJO DISPOSITIVOS	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_manejo_dispositivos]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_manejo_dispositivos").attr("disabled", "disabled") : $("#val_manejo_dispositivos").attr("readonly", true).cssie();
				$("#val_manejo_dispositivos").val("");
			}else
				(! $.browser.msie ) ? $("#val_manejo_dispositivos").attr("disabled", false).focus() :  $("#val_manejo_dispositivos").attr("readonly", false).cssie().focus();
		});

		//ALTA TEMPRANA	//Habilitar si le da click a cualquiera
		$('input[name=rd_interacciones_potenciales]').click(function(){
			(! $.browser.msie ) ? $("#val_interacciones_potenciales").attr("disabled", false).focus() : $("#val_interacciones_potenciales").attr("readonly", false).cssie().focus();
		});

		//INTERACCIONES POTENCIALES	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_interacciones_potenciales]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_interacciones_potenciales").attr("disabled", "disabled") : $("#val_interacciones_potenciales").attr("readonly", true).cssie();
				$("#val_interacciones_potenciales").val("");
			}else
				(! $.browser.msie ) ? $("#val_interacciones_potenciales").attr("disabled", false).focus() :  $("#val_interacciones_potenciales").attr("readonly", false).cssie().focus();
		});

		//DIETA Y NUTRICION //Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_dieta_nutricion]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_dieta_nutricion	").attr("disabled", "disabled") : $("#val_dieta_nutricion").attr("readonly", true).cssie();
				$("#val_dieta_nutricion").val("");
			}else
				(! $.browser.msie ) ? $("#val_dieta_nutricion").attr("disabled", false).focus() :  $("#val_dieta_nutricion").attr("readonly", false).cssie().focus();
		});

		//MANEJO DEL DOLOR	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_manejo_dolor]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_manejo_dolor").attr("disabled", "disabled") : $("#val_manejo_dolor").attr("readonly", true).cssie();
				$("#val_manejo_dolor").val("");
			}else
				(! $.browser.msie ) ? $("#val_manejo_dolor").attr("disabled", false).focus() :  $("#val_manejo_dolor").attr("readonly", false).cssie().focus();
		});

		//TECNICAS EN REHABILITACION	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_tecnicas_rehabilitacion]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_tecnicas_rehabilitacion").attr("disabled", "disabled") : $("#val_tecnicas_rehabilitacion").attr("readonly", true).cssie();
				$("#val_tecnicas_rehabilitacion").val("");
			}else
				(! $.browser.msie ) ? $("#val_tecnicas_rehabilitacion").attr("disabled", false).focus() :  $("#val_tecnicas_rehabilitacion").attr("readonly", false).cssie().focus();
		});

		//DERECHOS DEL PACIENTE	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_derechos_paciente]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_derechos_paciente").attr("disabled", "disabled") : $("#val_derechos_paciente").attr("readonly", true).cssie();
				$("#val_derechos_paciente").val("");
			}else
				(! $.browser.msie ) ? $("#val_derechos_paciente").attr("disabled", false).focus() :  $("#val_derechos_paciente").attr("readonly", false).cssie().focus();
		});

		//OTRAS	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_otras]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_otras").attr("disabled", "disabled") : $("#val_otras").attr("readonly", true).cssie();
				$("#val_otras").val("");
			}else
				(! $.browser.msie ) ? $("#val_otras").attr("disabled", false).focus() :  $("#val_otras").attr("readonly", false).cssie().focus();
		});

		//CONSENTIMIENTOS INFORMADOS	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=rd_consentimientos]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_consentimientos").attr("disabled", "disabled") : $("#val_consentimientos").attr("readonly", true).cssie();
				$("#val_consentimientos").val("");
			}else
				(! $.browser.msie ) ? $("#val_consentimientos").attr("disabled", false).focus() :  $("#val_consentimientos").attr("readonly", false).cssie().focus();
		});


		//Si es internet explorer: readonly; Si es otro explorador: disabled
		(! $.browser.msie ) ? $("#formulario_necesidad textarea").attr("disabled", "disabled") : $("#formulario_necesidad textarea").attr("readonly", true);
		(! $.browser.msie ) ? $("#au_obs").attr("disabled", false).cssie() :  $("#au_obs").attr("readonly", false).cssie();
		(! $.browser.msie ) ? $(".objetivo").attr("disabled", false).cssie() :  $(".objetivo").attr("readonly", false).cssie();
		(! $.browser.msie ) ? $(".actividad").attr("disabled", false).cssie() :  $(".actividad").attr("readonly", false).cssie();
		(! $.browser.msie ) ? $(".responsable").attr("disabled", false).cssie() :  $(".responsable").attr("readonly", false).cssie();
	}

	//funcion que deja el formulario de auditoria con las opciones predeterminadas.
	function restablecer_formulario(){
		validaciones_formulario_necesidad();

		$('input[name=rd_conoce_diagnostico][value=No]').prop('checked', true);
		$('input[name=rd_condicion_mejorara][value=No]').prop('checked', true);
		$('input[name=rd_acepta_limitaciones][value=No]').prop('checked', true);
		$('input[name=rd_estado_animo][value=No]').prop('checked', true);
		$('input[name=rd_manejo_uso_medicamentos][value=No]').prop('checked', true);
		$('input[name=rd_manejo_dispositivos][value=No]').prop('checked', true);
		$('input[name=rd_dieta_nutricion][value=No]').prop('checked', true);
		$('input[name=rd_manejo_dolor][value=No]').prop('checked', true);
		$('input[name=rd_tecnicas_rehabilitacion][value=No]').prop('checked', true);
		$('input[name=rd_derechos_paciente][value=No]').prop('checked', true);
		$('input[name=rd_otras][value=No]').prop('checked', true);
		$('input[name=rd_consentimientos][value=No]').prop('checked', true);
		$("#val_conoce_diagnostico").val("");
		$("#val_condicion_mejorara").val("");
		$("#val_estado_animo").val("");
		$("#val_acepta_limitaciones").val("");
		$("#val_manejo_medicamentos").val("");
		$("#val_manejo_dispositivos").val("");
		$("#val_interacciones_potenciales").val("");
		$("#val_dieta_nutricion").val("");
		$("#val_manejo_dolor").val("");
		$("#val_tecnicas_rehabilitacion").val("");
		$("#val_derechos_paciente").val("");
		$("#val_otras").val("");
		$("#val_consentimientos").val("");
		$("#au_obs").val("");


		$("#titulo_formulario_necesidad").html("");
		$("#boton_guardar_necesidad").show('slow');
		$("#formulario_necesidad input").attr("disabled", false);


		$("#formulario_ingreso").html("");

		necesidad_editable_global = "";
		editando_global = false;
		$(".boton_editar").hide();
		$(".campos_editables").remove();
		$("#formulario_necesidad textarea:not(.evitar)").attr('cols',120);
		if( $.browser.msie ) $("#formulario_necesidad textarea").attr("disabled", false);
		(! $.browser.msie ) ? $("#formulario_necesidad textarea").attr("disabled", "disabled") : $("#formulario_necesidad textarea").attr("readonly", true);

		$('#au_obs').attr("disabled", false);
		$('#au_obs').attr('readonly', false );

		$('.compromiso_adicional').remove();
		$('.objetivo, .actividad, .responsable').val("");
		$('.objetivo, .actividad, .responsable').attr("disabled", false);
		$('.objetivo, .actividad, .responsable').attr('readonly', false );

	}

	//funcion que se activa cuando se da click al boton retornar, restaura la pagina
	function restablecer_pagina(){

		if( mostrando_paciente == true ){
			$('#resultados_lista').show();
			$('#resultados_paciente').hide();
			mostrando_paciente = false;
		}else{
			$('#listado_necesidad').html("");
			var efecto = "";
			if (! $.browser.msie ) {
				efecto = 'fast';
			}
			$(".rep_parametros").fadeIn(efecto);
			$('#resultados_paciente').hide(efecto);
			$('#resultados_lista').hide(efecto);
			$("#enlace_retornar").hide(efecto);
			$(".parametros").show(efecto);
			mostrando_paciente = false;
			historia_global = 0;
			ingreso_global = 0;
		}
	}

	//Funcion que trae del servidor las auditorias en esa fecha realizadas a un paciente
	function consultarNecesidades(historia, ingreso, fecha){
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, action: "consultandoNecesidad", historia: historia, ingreso: ingreso, fecha: fecha, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				var aux = data;
				if( isJson( aux )){
					llenarFormulario( data, fecha );
				}else{
					alert("No se pudo cargar la(s) necesidad(es) realizada(s) el dia "+fecha);
				}
			},'json');
	}

	//Funcion que trae del servidor el formulario de ingreso de enfermeria de hce
	function consultarIngresoEnfermeria(){
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		if ( $("#formulario_ingreso").html() != "" ){
			$("#formulario_ingreso").toggle();
			return;
		}
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, historia: historia_global,	ingreso: ingreso_global,action: "consultarIngresoEnfermeria", consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				$("#formulario_ingreso").html( data );
				$("#formulario_ingreso *").attr('onclick','').unbind('click');
			});
	}

	//Funcion que agrega el nuevo textarea cuando puedo agregar una auditoria y visualizar las otras
	function validaciones_nuevos_textarea(){

		$('input[name=rd_conoce_diagnostico]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_conoce_diagnosticox").attr("disabled", "disabled") : $("#val_conoce_diagnosticox").attr("readonly", true).cssie();
				$("#val_conoce_diagnosticox").val("");
			}else
				(! $.browser.msie ) ? $("#val_conoce_diagnosticox").attr("disabled", false).cssie().focus() : $("#val_conoce_diagnosticox").attr("readonly", false).cssie().focus();
		});
		$('input[name=rd_condicion_mejorara]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_condicion_mejorarax").attr("disabled", "disabled") : $("#val_condicion_mejorarax").attr("readonly", true).cssie();
				$("#val_condicion_mejorarax").val("");
			}else
				(! $.browser.msie ) ? $("#val_condicion_mejorarax").attr("disabled", false).focus() : $("#val_condicion_mejorarax").attr("readonly", false).cssie().focus();
		});

		$('input[name=rd_acepta_limitaciones]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_acepta_limitacionesx").attr("disabled", "disabled") : $("#val_acepta_limitacionesx").attr("readonly", true).cssie();
				$("#val_acepta_limitacionesx").val("");
			}else
				(! $.browser.msie ) ? $("#val_acepta_limitacionesx").attr("disabled", false).focus() :  $("#val_acepta_limitacionesx").attr("readonly", false).cssie().focus();
		});

		$('input[name=rd_estado_animo]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_estado_animox").attr("disabled", "disabled") : $("#val_estado_animox").attr("readonly", true).cssie();
				$("#val_estado_animox").val("");
			}else
				(! $.browser.msie ) ? $("#val_estado_animox").attr("disabled", false).focus() :  $("#val_estado_animox").attr("readonly", false).cssie().focus();
		});


		$('input[name=rd_manejo_uso_medicamentos]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_manejo_medicamentosx").attr("disabled", "disabled") : $("#val_manejo_medicamentosx").attr("readonly", true).cssie();
				$("#val_manejo_medicamentosx").val("");
			}else
				(! $.browser.msie ) ? $("#val_manejo_medicamentosx").attr("disabled", false).focus() :  $("#val_manejo_medicamentosx").attr("readonly", false).cssie().focus();
		});

		$('input[name=rd_manejo_dispositivos]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_manejo_dispositivosx").attr("disabled", "disabled") : $("#val_manejo_dispositivosx").attr("readonly", true).cssie();
				$("#val_manejo_dispositivosx").val("");
			}else
				(! $.browser.msie ) ? $("#val_manejo_dispositivosx").attr("disabled", false).focus() : $("#val_manejo_dispositivosx").attr("readonly", false).cssie().focus();
		});

		//********---------
		$('input[name=rd_interacciones_potenciales]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_interacciones_potencialesx").attr("disabled", "disabled") : $("#val_interacciones_potencialesx").attr("readonly", true).cssie();
				$("#val_interacciones_potencialesx").val("");
			}else
				(! $.browser.msie ) ? $("#val_interacciones_potencialesx").attr("disabled", false).focus() : $("#val_interacciones_potencialesx").attr("readonly", false).cssie().focus();
		});

		$('input[name=rd_dieta_nutricion]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_dieta_nutricionx").attr("disabled", "disabled") : $("#val_dieta_nutricionx").attr("readonly", true).cssie();
				$("#val_dieta_nutricionx").val("");
			}else
				(! $.browser.msie ) ? $("#val_dieta_nutricionx").attr("disabled", false).focus() : $("#val_dieta_nutricionx").attr("readonly", false).cssie().focus();
		});

		$('input[name=rd_manejo_dolor]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_manejo_dolorx").attr("disabled", "disabled") : $("#val_manejo_dolorx").attr("readonly", true).cssie();
				$("#val_manejo_dolorx").val("");
			}else
				(! $.browser.msie ) ? $("#val_manejo_dolorx").attr("disabled", false).focus() : $("#val_manejo_dolorx").attr("readonly", false).cssie().focus();
		});

		$('input[name=rd_tecnicas_rehabilitacion]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_tecnicas_rehabilitacionx").attr("disabled", "disabled") : $("#val_tecnicas_rehabilitacionx").attr("readonly", true).cssie();
				$("#val_tecnicas_rehabilitacionx").val("");
			}else
				(! $.browser.msie ) ? $("#val_tecnicas_rehabilitacionx").attr("disabled", false).focus() : $("#val_tecnicas_rehabilitacionx").attr("readonly", false).cssie().focus();
		});
		$('input[name=rd_derechos_paciente]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_derechos_pacientex").attr("disabled", "disabled") : $("#val_derechos_pacientex").attr("readonly", true).cssie();
				$("#val_derechos_pacientex").val("");
			}else
				(! $.browser.msie ) ? $("#val_derechos_pacientex").attr("disabled", false).focus() : $("#val_derechos_pacientex").attr("readonly", false).cssie().focus();
		});
		$('input[name=rd_otras]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_otrasx").attr("disabled", "disabled") : $("#val_otrasx").attr("readonly", true).cssie();
				$("#val_otrasx").val("");
			}else
				(! $.browser.msie ) ? $("#val_otrasx").attr("disabled", false).focus() : $("#val_otrasx").attr("readonly", false).cssie().focus();
		});
		$('input[name=rd_consentimientos]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_consentimientosx").attr("disabled", "disabled") : $("#val_consentimientosx").attr("readonly", true).cssie();
				$("#val_consentimientosx").val("");
			}else
				(! $.browser.msie ) ? $("#val_consentimientosx").attr("disabled", false).focus() : $("#val_consentimientosx").attr("readonly", false).cssie().focus();
		});


		var campos_array = new Array('val_conoce_diagnostico','val_condicion_mejorara', 'val_estado_animo', 'val_acepta_limitaciones','val_pos_obj','val_manejo_medicamentos','val_manejo_dispositivos','val_interacciones_potenciales',
		'val_dieta_nutricion', 'val_manejo_dolor', 'val_tecnicas_rehabilitacion', 'val_derechos_paciente', 'val_otras', 'val_consentimientos','au_obs');
		for( i in campos_array ){
			//Agregando textarea
			campo = jQuery("#"+campos_array[i]);
			cols = campo.attr("cols");
			if( !$.browser.msie )
				cols=cols-24;
			cols = parseInt( cols/2 );
			campo.attr("cols", cols);
			campo.before("<textarea id='"+campos_array[i]+"x' rows=4 class='campos_editables' cols="+cols+"></textarea>");
		}
	}

	function isJson(value) {
		try {
			eval( value );
			return true;
		} catch (ex) {
			return false;
		}
	}

	//Luego de traer los datos de la necesidad esta funcion se encarga de mostralos en el formulario
	function llenarFormulario( necesidades, fecha ){
		restablecer_formulario();
		var necesidad = necesidades['necesidades_todas'];
		var necesidad_editable = necesidades['necesidad_editable'];

		if ( necesidad_editable != undefined ){
			necesidad_editable_global = necesidad_editable;
			editando_global = true;
		}

		$("#formulario_necesidad textarea").removeAttr("disabled");
		//CONOCE DIAGNOSTICO
		if( necesidad.conoce_diagnostico == ""){
			$('input[name=rd_conoce_diagnostico][value=No]').prop('checked', true);
			$("#val_conoce_diagnostico").attr("disabled", "disabled");
		}else{
			$('input[name=rd_conoce_diagnostico][value=Si]').prop('checked', true);
			$("#val_conoce_diagnostico").val( necesidad.conoce_diagnostico );
		}
		//CONDICION MEJORARA
		if( necesidad.condicion_mejorara == ""){
			$('input[name=rd_condicion_mejorara][value=No]').prop('checked', true);
			$("#val_condicion_mejorara").attr("disabled", "disabled");
		}else{
			$('input[name=rd_condicion_mejorara][value=Si]').prop('checked', true);
			$("#val_condicion_mejorara").val( necesidad.condicion_mejorara );
		}

		//ACEPTA LIMITACIONES
		if( necesidad.acepta_limitaciones == ""){
			$('input[name=rd_acepta_limitaciones][value=No]').prop('checked', true);
			$("#val_acepta_limitaciones").attr("disabled", "disabled");
		}else{
			$('input[name=rd_acepta_limitaciones][value=Si]').prop('checked', true);
			$("#val_acepta_limitaciones").val( necesidad.acepta_limitaciones );
		}
		//ESTADO DE ANIMO
		if( necesidad.estado_animo == ""){
			$('input[name=rd_estado_animo][value=No]').prop('checked', true);
			$("#val_estado_animo").attr("disabled", "disabled");
		}else{
			$('input[name=rd_estado_animo][value=Si]').prop('checked', true);
			$("#val_estado_animo").val( necesidad.estado_animo );
		}

		//MANEJO USO DE MEDICAMENTOS
		if( necesidad.manejo_medicamentos == ""){
			$('input[name=rd_manejo_uso_medicamentos][value=No]').prop('checked', true);
			$("#val_manejo_medicamentos").attr("disabled", "disabled");
		}else{
			$('input[name=rd_manejo_uso_medicamentos][value=Si]').prop('checked', true);
			$("#val_manejo_medicamentos").val( necesidad.manejo_medicamentos );
		}
		//MENEJO DE DISPOSITIVOS
		if( necesidad.manejo_dispositivos == ""){
			$('input[name=rd_manejo_dispositivos][value=No]').prop('checked', true);
			$("#val_manejo_dispositivos").attr("disabled", "disabled");
		}else{
			$('input[name=rd_manejo_dispositivos][value=Si]').prop('checked', true);
			$("#val_manejo_dispositivos").val( necesidad.manejo_dispositivos );
		}
		//INTERACCIONES POTENCIALES
		if( necesidad.interacciones == ""){
			$('input[name=rd_interacciones_potenciales][value=Si]').prop('checked', true);
			$("#val_interacciones_potenciales").attr("disabled", "disabled");
		}else{
			$('input[name=rd_interacciones_potenciales][value=No]').prop('checked', true);
			$("#val_interacciones_potenciales").val( necesidad.interacciones );
		}
		//DIETA Y NUTRICION
		if( necesidad.dieta == ""){
			$('input[name=rd_dieta_nutricion][value=No]').prop('checked', true);
			$("#val_dieta_nutricion").attr("disabled", "disabled");
		}else{
			$('input[name=rd_dieta_nutricion][value=Si]').prop('checked', true);
			$("#val_dieta_nutricion").val( necesidad.dieta );
		}
		//MANEJO DEL DOLOR
		if( necesidad.manejo_dolor == ""){
			$('input[name=rd_manejo_dolor][value=No]').prop('checked', true);
			$("#val_manejo_dolor").attr("disabled", "disabled");
		}else{
			$('input[name=rd_manejo_dolor][value=Si]').prop('checked', true);
			$("#val_manejo_dolor").val( necesidad.manejo_dolor );
		}
		//TECNICAS REHABILITACION
		if( necesidad.tecnicas_rehabilitacion == ""){
			$('input[name=rd_tecnicas_rehabilitacion][value=No]').prop('checked', true);
			$("#val_tecnicas_rehabilitacion").attr("disabled", "disabled");
		}else{
			$('input[name=rd_tecnicas_rehabilitacion][value=Si]').prop('checked', true);
			$("#val_tecnicas_rehabilitacion").val( necesidad.tecnicas_rehabilitacion );
		}
		//DERECHOS DEL PACIENTE
		if( necesidad.derechos == ""){
			$('input[name=rd_derechos_paciente][value=No]').prop('checked', true);
			$("#val_derechos_paciente").attr("disabled", "disabled");
		}else{
			$('input[name=rd_derechos_paciente][value=Si]').prop('checked', true);
			$("#val_derechos_paciente").val( necesidad.derechos );
		}
		//OTRAS
		if( necesidad.otras == ""){
			$('input[name=rd_otras][value=No]').prop('checked', true);
			$("#val_otras").attr("disabled", "disabled");
		}else{
			$('input[name=rd_otras][value=Si]').prop('checked', true);
			$("#val_otras").val( necesidad.otras );
		}
		//CONSENTIMIENTOS
		if( necesidad.consentimientos == ""){
			$('input[name=rd_consentimientos][value=No]').prop('checked', true);
			$("#val_consentimientos").attr("disabled", "disabled");
		}else{
			$('input[name=rd_consentimientos][value=Si]').prop('checked', true);
			$("#val_consentimientos").val( necesidad.consentimientos );
		}
		//OBSERVACIONES
		var observaciones = $("#au_obs").val( necesidad.observaciones );


		//COMPROMISOS
		var compromisos = necesidad.compromisos;
		for( var h=0; h<compromisos.length; h++ ){
			mostrarCompromiso(compromisos[h].objetivo, compromisos[h].actividad, compromisos[h].responsable);
		}

		$("#titulo_formulario_necesidad").html("<b>Necesidad(s) realizada(s) el dia "+fecha+"</b>");

		if( $.browser.msie ) $("#formulario_necesidad textarea").attr("disabled", false);
		$("#formulario_necesidad input").attr("disabled", "disabled");
		(! $.browser.msie ) ? $("#formulario_necesidad textarea").attr("disabled", "disabled") : $("#formulario_necesidad textarea").attr("readonly", true)

		if( $.browser.msie ) $("#formulario_necesidad textarea").css('background-color','#ffffff');
		if( $.browser.msie ) $("#formulario_necesidad input[type='text']").css('background-color','#ffffff');

		if ( necesidad_editable != undefined ){
			$("#formulario_necesidad input[type='radio']").attr('onclick','').unbind('click');
			validaciones_nuevos_textarea();

			$('input[name=rd_conoce_diagnostico][value=No]').prop('checked', true);
			$('input[name=rd_condicion_mejorara][value=No]').prop('checked', true);
			$('input[name=rd_acepta_limitaciones][value=No]').prop('checked', true);
			$('input[name=rd_estado_animo][value=No]').prop('checked', true);
			$('input[name=rd_manejo_uso_medicamentos][value=No]').prop('checked', true);
			$('input[name=rd_manejo_dispositivos][value=No]').prop('checked', true);

			$('input[name=rd_interacciones_potenciales][value=No]').prop('checked', true);
			$('input[name=rd_dieta_nutricion][value=No]').prop('checked', true);
			$('input[name=rd_manejo_dolor][value=No]').prop('checked', true);
			$('input[name=rd_tecnicas_rehabilitacion][value=No]').prop('checked', true);
			$('input[name=rd_derechos_paciente][value=No]').prop('checked', true);
			$('input[name=rd_otras][value=No]').prop('checked', true);
			$('input[name=rd_consentimientos][value=No]').prop('checked', true);

			//ACTIVA LA FUNCION QUE MUESTRA EL OTRO TEXTAREA PARA CADA CAMPO DEL FORMULARIO
			if( $.browser.msie ) $("#formulario_necesidad textarea").attr("disabled", false);
			$("#formulario_necesidad input").attr("disabled", "disabled");
			(! $.browser.msie ) ? $("#formulario_necesidad textarea").attr("disabled", "disabled") : $("#formulario_necesidad textarea").attr("readonly", true);
			//habilita todos los input tipo radio
			$("#formulario_necesidad input[type='radio']").attr("disabled", false);
			$("#formulario_necesidad input[type='checkbox']").attr("disabled", false);
			$("#boton_guardar_necesidad").attr("disabled", false);
			$("#boton_guardar_necesidad").show();
			$("#add_necesidad").hide();
			$("#btn_crear_compromiso").attr("disabled", false);
			$('#au_obsx, .objetivo, .actividad, .responsable').attr('disabled', false );
			$('#au_obsx, .objetivo, .actividad, .responsable').attr('readonly', false );

			if( $.browser.msie ) $("#formulario_necesidad textarea").css('background-color','#ffffff');
			if ( $.browser.msie )  $('#au_obsx, .objetivo, .actividad, .responsable').cssie();
		}
	}

	//Funcion que manda al servidor los datos ingresados en una auditoria
	function guardarNecesidad(){
		var text_conoce_diag="";
		var text_condicion_mejorara = "";
		var text_acepta_limitaciones = "";
		var text_estado_animo = '';
		var text_manejo_med = "";
		var text_manejo_disp = "";
		var text_interacciones = "";
		var text_dieta = "";
		var text_manejo_dolor = "";
		var text_tecnicas_rehab = "";
		var text_derechos = "";
		var text_otras = "";
		var text_consentimientos = "";
		var observaciones = "";

		//SI NO SE ESTA EDITANDO
		if( editando_global == false ){
			text_conoce_diag = $("#val_conoce_diagnostico").val();
			text_condicion_mejorara = $("#val_condicion_mejorara").val();
			text_acepta_limitaciones = $("#val_acepta_limitaciones").val();
			text_estado_animo = $("#val_estado_animo").val();
			text_manejo_med = $("#val_manejo_medicamentos").val();
			text_manejo_disp = $("#val_manejo_dispositivos").val();
			text_interacciones = $("#val_interacciones_potenciales").val();
			text_dieta = $("#val_dieta_nutricion").val();
			text_manejo_dolor = $("#val_manejo_dolor").val();
			text_tecnicas_rehab = $("#val_tecnicas_rehabilitacion").val();
			text_derechos = $("#val_derechos_paciente").val();
			text_otras = $("#val_otras").val();
			text_consentimientos = $("#val_consentimientos").val();
			observaciones = $("#au_obs").val();
			//SI SE ESTA EDITANDO
		}else{
				text_conoce_diag = $("#val_conoce_diagnosticox").val();
				text_condicion_mejorara = $("#val_condicion_mejorarax").val();
				text_acepta_limitaciones = $("#val_acepta_limitacionesx").val();
				text_manejo_med = $("#val_manejo_medicamentosx").val();
				text_estado_animo = $("#val_estado_animox").val();
				text_manejo_disp = $("#val_manejo_dispositivosx").val();
				text_interacciones = $("#val_interacciones_potencialesx").val();
				text_dieta = $("#val_dieta_nutricionx").val();
				text_manejo_dolor = $("#val_manejo_dolorx").val();
				text_tecnicas_rehab = $("#val_tecnicas_rehabilitacionx").val();
				text_derechos = $("#val_derechos_pacientex").val();
				text_otras = $("#val_otrasx").val();
				text_consentimientos = $("#val_consentimientosx").val();
				observaciones = $("#au_obsx").val();
		}

		var objetivos = new Array();
		var actividades = new Array();
		var responsables = new Array();
		//BUscar los objetivos
		$(".objetivo").each( function(){
			objetivos.push( $(this).val() );
		});
		$(".actividad").each( function(){
			actividades.push( $(this).val() );
		});
		$(".responsable").each( function(){
			responsables.push( $(this).val() );
		});
		var pasa = true;
		for( k in objetivos ){
			if( objetivos[k] == '' || actividades[k] == '' || responsables[k] == '' ){
				pasa = false;
			}
		}
		if( pasa == false){
			alert("Debe ingresar los Objetivos de aprendizaje, Actividades de educación, y Responsables antes de guardar");
			return;
		}
		dato = new Object();
		dato.objetivos = objetivos;
		dato.actividades = actividades;
		dato.responsables = responsables;
		var datosJson = $.toJSON( dato );

		var result = text_conoce_diag + text_condicion_mejorara + text_acepta_limitaciones + text_estado_animo+ text_manejo_med + text_manejo_disp + text_interacciones + observaciones + text_dieta + text_manejo_dolor + text_tecnicas_rehab + text_derechos + text_otras  + text_consentimientos;
		result=result.replace(/ /gi,"");
		result=result.replace(/\n/gi,"");
		result=result.replace(/\r/gi,"");
		if( result == "" ){
			alerta("No ha escrito nada para guardar o falta un campo por llenar");
			return;
		}
		//DATOS CORRECTOS
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();

		// $.blockUI({ message: $('#msjEspere') }); // **** [2014-08-12] Se comentan estas líneas porque al presionar el boton "Guardar y salir", se genera un error javascript cuando se da clic en el boton ver en la lista de historias.

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, action: "guardarNecesidad", consultaAjax: aleatorio,
										historia: historia_global,
										ingreso: ingreso_global,
										conoce_diagnostico: text_conoce_diag,
										condicion_mejorara: text_condicion_mejorara,
										acepta_limitaciones: text_acepta_limitaciones,
										estado_animo : text_estado_animo,
										manejo_medicamentos: text_manejo_med,
										manejo_dispositivos: text_manejo_disp,
										interacciones: text_interacciones,
										dieta: text_dieta,
										manejo_dolor: text_manejo_dolor,
										tecnicas_rehabilitacion: text_tecnicas_rehab,
										derechos: text_derechos,
										otras: text_otras,
										consentimientos: text_consentimientos,
										observaciones: observaciones,
										compromisos: datosJson
										} ,
			function(data) {
				//oculta el mensaje de cargando
				// $.unblockUI(); // **** [2014-08-12] Se comentan estas líneas porque al presionar el botona salir y guardar, se genera un error javascript cuando se da clic en el boton ver en la lista de historias.
				if( data == "A" ){
					alerta("Actualizacion realizada con exito");
					restablecer_formulario();
					guardarYSalir();
				}else if(data == "I"){
					setTimeout( function(){
							$("#add_necesidad").hide();
						}, 500 );
					alerta("Guardado realizado con exito");
					restablecer_formulario();
					cargarListaNecesidades();
					guardarYSalir();
				}else if(data == "V"){
					alert('No se ha guardado, por favor intente nuevamente');
				}else{
					alerta("Ha ocurrido un error, por favor intente nuevamente");
				}
			});
	}

	/**
	 * [guardarYSalir: Siempre que se habra un registro, se edite o se guarden parámetros por primer vez, se retornará a la lista de
	 * 					historias, se hace ese retorno para evitar confundir al usuario del programa puesto que en ocaciones
	 * 					cuando guarda una observación, actividad y responsable, el formulario queda con campos en blanco lo que hace pensar que
	 * 					la información no fué guardada dando la posibilidad a reescribir y repetir información.
	 *
	 * 					La función retorna a la vista anterior y genera de nuevo la consulta para marcar que la historia ya tiene educación]
	 * @return {[type]} [description]
	 */
	function guardarYSalir()
	{
		// restablecer_pagina();
		realizarConsultaLista();
	}

	//Funcion que trae del servidor la lista de necesidades detectadas del paciente
	function cargarListaNecesidades(){
		var wemp_pmla = $("#wemp_pmla").val();
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, action: "consultarListaNecesidades", historia: historia_global, ingreso: ingreso_global, consultaAjax: aleatorio} ,
			function(data) {
				$('#listado_necesidad').html(data);
				restablecer_formulario();
			});
	}

	//funcion que luego de elegir el centro de costos, me trae los pacientes que se encuentran en el
	function realizarConsultaLista(){
		restablecer_pagina();
		if( $("#check_egresados").is(":checked") ){
			consultarListaEgresados();
			return;
		}
		var wemp_pmla = $("#wemp_pmla").val();
		var servicio = $("#servicio").val();
		var historia = $("#input_historia").val();
		var fecha = "";//XXX$("#fecha").val();
		var responsable =  buscarCodigoEntidad ( $("#responsable").val() );
		if( servicio == '' && historia == ''){
			mostrando_paciente = false;
			//restablecer_pagina();
			return;
		}

		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$('#resultados_paciente').hide('slow');
		$("#enlace_retornar").fadeIn('slow');

		var accion = 'mostrarlista';
		if( historia != '' && servicio == '')
			accion = 'mostrarlistaingresos';

		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//XXX$("#fecha_elegida").val(fecha);
		$("#servicio_elegido").val(servicio);
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, action: accion, responsable: responsable, servicio: servicio, fecha: fecha, historia:historia, consultaAjax: aleatorio} ,
			function(data) {
				$(".parametros").hide();
				$.unblockUI();
				$('#resultados_lista').html(data);
				$('#resultados_lista').show('slow');
				$("#responsable").focus();
				mostrando_paciente = false;
				//$( "#radios_ing" ).buttonset();
			});
	}

	function consultarListaEgresados(){
		var fecha_i = $("#wfec_i").val();
		var fecha_f = $("#wfec_f").val();
		var wemp_pmla = $("#wemp_pmla").val();
		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//Realiza el llamado ajax con los parametros de busqueda
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, action: 'consultarListaEgresados', fecha_i: fecha_i, fecha_f: fecha_f, consultaAjax: aleatorio} ,
			function(data) {
				$(".parametros").hide();
				$.unblockUI();
				$('#resultados_lista').html(data);
				$('#resultados_lista').show('slow');
				mostrando_paciente = false;
			});
	}

	//funcion que trae del servidor todos los datos relacionados al paciente
	function realizarConsultaPaciente(historia, ingreso, servicio, paciente, documento_pac, habitacion, f_nacimiento, medico ){
		var wemp_pmla = $("#wemp_pmla").val();
		var fecha = "";//XXX$("#fecha_elegida").val();
		var servicio = $("#servicio_elegido").val();
		historia_global = historia;
		ingreso_global = ingreso;

		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });

		$("#enlace_retornar").fadeIn('slow');

		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('educacion_al_paciente.php', { wemp_pmla: wemp_pmla, action: "mostrarPaciente", historia: historia, ingreso: ingreso, servicio: servicio, fecha: fecha,
										paciente: paciente, doc_paciente: documento_pac, habitacion: habitacion, nacimiento: f_nacimiento, medico: medico, consultaAjax: aleatorio} ,
			function(data) {
				$(".rep_parametros").hide();
				$.unblockUI();
				$(".parametros").hide();
				$('#resultados_paciente').html(data);
				if (! $.browser.msie ) {
					$('#resultados_paciente').show('slow');
					$('#resultados_lista').hide('slow');
				}else{
					$('#resultados_paciente').show();
					$('#resultados_lista').hide();
				}
				mostrando_paciente = true;
				restablecer_formulario();
				$("#boton_guardar_necesidad").click(function() {
					guardarNecesidad();
				});
				$("#formulario_necesidad textarea").cssie();
				$("#add_necesidad").click(function() {
					restablecer_formulario();
					$("#titulo_formulario_necesidad").html("<b>Formulario de Necesidades</b>");
				});
				$(".boton_editar").hide();

				mostrarUltimaNecesidad();
			});
	}

	function crearCompromiso(){
		var salir = false;
		$(".objetivo").each( function(){
			if( $(this).val() == '' )
				salir = true;
		});
		$(".actividad").each( function(){
			if( $(this).val() == '' )
				salir = true;
		});
		$(".responsable").each( function(){
			if( $(this).val() == '' )
				salir = true;
		});
		if( salir == true ){
			alerta('Por favor ingrese el objetivo, la actividad y el responsable antes de crear un nuevo compromiso');
			return;
		}
		var codigo_html = "<tr class='fila2 compromiso_adicional'>";
		codigo_html+="<td><textarea rows=4 cols=33 class='evitar objetivo'></textarea></td>";
		codigo_html+="<td><textarea rows=4 cols=33 class='evitar actividad'></textarea></td>";
		codigo_html+="<td><textarea rows=4 cols=33 class='evitar responsable'></textarea></td>";
		codigo_html+"</tr>";
		$('#tr_compromiso_original').after( codigo_html );
		$(".objetivo, .actividad, .responsable").cssie();
	}

	function mostrarCompromiso(objetivo, actividad, responsable){
		var codigo_html = "<tr class='fila2 compromiso_adicional'>";
		codigo_html+="<td><textarea rows=4 cols=33 class='evitar' disabled='disabled' readonly='readonly'>"+objetivo+"</textarea></td>";
		codigo_html+="<td><textarea rows=4 cols=33 class='evitar' disabled='disabled' readonly='readonly'>"+actividad+"</textarea></td>";
		codigo_html+="<td><textarea rows=4 cols=33 class='evitar' disabled='disabled' readonly='readonly'>"+responsable+"</textarea></td>";
		codigo_html+"</tr>";
		$('#tr_primer_compromiso').after( codigo_html );
	}

	function consultarEgresados( ele ){
		ele = jQuery( ele );
		if( ele.is(":checked") ){
			$(".filaoculta").show();
			$("#servicio").attr("disabled",true);
			$("#responsable").attr("disabled",true);
			$("#input_historia").attr("disabled",true);
		}else{
			$(".filaoculta").hide();
			$("#servicio").attr("disabled",false);
			$("#responsable").attr("disabled",false);
			$("#input_historia").attr("disabled",false);
		}
	}

	//Funcion que da click al ultimo enlace de la lista de auditorias
	function mostrarUltimaNecesidad(){
		setTimeout(function(){
			$("#listado_necesidad a:last").trigger('click');
		},500)
	}

	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 2000 );
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