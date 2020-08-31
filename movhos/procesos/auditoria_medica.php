<?php
include_once("conex.php");
/************************************************************************************************************
 * Reporte		:	Auditoria Medica concurrente
 * Fecha		:	2012-11-28
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	El objetivo del programa es crear una auditoria diaria para los pacientes de hospitalizacion.
					Un usuario no puede realizar varias auditorias para un mismo paciente el mismo dia, por lo que
					si ingresa y ya le habia hecho una auditoria, solo se ofrece la opcion de actualizar la auditoria realizada previamente.
 *********************************************************************************************************

 Actualizaciones:
			2017-12-18 (Jessica Madrid)
						Se adicionan la funciones consultarUltimaEvolucionHCE() y consultarNotasAclaratoriasHCE(), se comenta el 
						llamado a la funcion consultarUltimoDiagnosticoHCE() ya que en el campo evoluciones se estaba mostrando 
						el diagnóstico y debe traer la última evolución con las notas aclaratorias del último formulario con 
						evolución diligenciado.
			2017-05-05 (Jonatan Lopez)
						Se agrega la funcion consultarUltimoDiagnosticoHCE para que consulte la ultima evolucion del paciente.
 			2014-08-04 (Camilo Zapata)
						se cambio el tipo de consulta ajax en la función "guardarAuditoria", se cambió de $.get a $.post para aumentar la capacidad de envio de caracteres.y guardar
						grandes textos en las auditorias
			2014-08-04 (Camilo Zapata)
						se agregó el signo @ en el llamado a la función Strip_tags para evitar el warning cuando el parámetro no lleva la forma requerida
			2014-05-05 (Frederick Aguirre)
						Se adjunta un mensaje aclarando el rango de fechas que pacientes consulta, se quita el codigo
						html en las observaciones del panel de medicamentos.
			2013-05-20 (Frederick Aguirre)
						Se muestra una nueva columna que indica si el paciente tiene o no auditoria.
						Se crea la opcion de consultar en un rango de fechas una lista de los pacientes a los que se les ha guardado auditoria.

			2013-02-06: (Frederick Aguirre Sanchez)
						Se modifica para que compruebe si realmente guardo o actualizo la auditoria, dado que muestra que el
						proceso fue exitoso cuando en realidad no lo fue
			2012-12-28: (Frederick Aguirre Sanchez)
						Se modifica el programa para que busque el ultimo kardex del paciente, y
						se agrega la posibilidad de consultar la información y las auditorias medicas para los pacientes egresados
						digitando el codigo de la historia.
		    2012-12-19: (Frederick Aguirre Sanchez)
						Se quita el campo fecha, se condiciona para que muestre pacientes de urgencias y cirugia
						Se quita la opcion de editar una auditoria.
			2012-12-14: (Frederick Aguirre Sanchez)
						Se modifica para que al seleccionar una fecha de la lista de auditorias muestre
						todas las auditorias realizadas por cualquier usuario.
						Se cambia la manera de editar una auditoria realizada en un dia por el mismo usuario.
 **********************************************************************************************************/

 $wactualiz = "2017-12-18";

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
	echo "<title>Auditoria Medica Concurrente</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo '<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>';
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//



include_once("root/comun.php");
include_once("movhos/movhos.inc.php");



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
	}else if($action=="guardarAuditoria"){
		$auditoria['historia'] = @$_REQUEST['historia'];
		$auditoria['ingreso'] = @$_REQUEST['ingreso'];
		$auditoria['estancia'] = @$_REQUEST['estancia'];
		$auditoria['medicamentos'] = @$_REQUEST['medicamentos'];
		$auditoria['ayudas_diagnosticas'] = @$_REQUEST['ayudas_diagnosticas'];
		$auditoria['posibles_objeciones'] = @$_REQUEST['posibles_objeciones'];
		$auditoria['valor_objeciones'] = @$_REQUEST['valor_objeciones'];
		$auditoria['eventos_adversos'] = @$_REQUEST['eventos_adversos'];
		$auditoria['reingreso'] = @$_REQUEST['reingreso'];
		$auditoria['alta_temprana'] = @$_REQUEST['alta_temprana'];
		$auditoria['observaciones'] = @$_REQUEST['observaciones'];
		$auditoria['guardarBitacora'] = @$_REQUEST['guardarBitacora'];
		guardarAuditoria( $auditoria );
	}else if ($action=="consultandoAuditoria"){
		mostrarAuditoria( @$_REQUEST['historia'], @$_REQUEST['ingreso'], @$_REQUEST['fecha'] );
	}else if($action=="consultarListaAuditorias"){
		cargarListaDeAuditorias( @$_REQUEST['historia'], @$_REQUEST['ingreso'] );
	}else if ( $action == "consultarListaEgresados" ){
		consultarListaEgresados(@$_REQUEST['fecha_i'], @$_REQUEST['fecha_f']);
	}

	return;
}
//FIN*LLAMADOS*AJAX**************************************************************************************************************//

	function consultarNotasAclaratoriasHCE( $whistoria, $wingreso, $fechaFormulario, $horaFormulario, $formularioEvolucion )
	{
		global $conex;
        global $wbasedato;
        global $wbaseHce;
		global $wemp_pmla;
		
		$consecutivoNotasAclaratorias = consultarAliasPorAplicacion($conex, $wemp_pmla, 'consecutivoInicialNotasAclaratoriasHCE');
		
		$queryNotasAclaratorias = "SELECT Fecha_data,Hora_data,movdat 
									 FROM ".$wbaseHce."_".$formularioEvolucion." 
									WHERE Fecha_data='".$fechaFormulario."' 
									  AND Hora_data='".$horaFormulario."' 
									  AND movhis='".$whistoria."' 
									  AND moving='".$wingreso."'
									  AND movcon>='".$consecutivoNotasAclaratorias."' 
									  AND movdat!=''
								 ORDER BY movcon;";
		
		$resNotasAclaratorias =  mysql_query($queryNotasAclaratorias,$conex) or die ("Error: ".mysql_errno()." - en el query consultar notas aclaratorias: ".$queryNotasAclaratorias." - ".mysql_error());
		$numNotasAclaratorias = mysql_num_rows($resNotasAclaratorias);	
		
		$notasAclaratorias = "";
		if($numNotasAclaratorias > 0)
		{
			$notasAclaratorias .= "NOTAS ACLARATORIAS:\n\n";
			while($rowNotasAclaratorias = mysql_fetch_array($resNotasAclaratorias))
			{
				$notaAclaratoria = $rowNotasAclaratorias['movdat'];
				$notaAclaratoria = str_replace("<b><u>","- ",$notaAclaratoria);
				$notaAclaratoria = str_replace("</u></b> <br>",": ",$notaAclaratoria);
				$notaAclaratoria = str_replace("</u></b><br>",": ",$notaAclaratoria);
				
				$notasAclaratorias .= $notaAclaratoria."\n\n";
			}
		}
		
		return $notasAclaratorias;
	}
	
	function consultarUltimaEvolucionHCE( $whistoria, $wingreso )
	{
		global $conex;
        global $wbasedato;
        global $wbaseHce;
		global $wemp_pmla;
		
		$formulariosEvoluciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'formulariosHceEvolucion');
		
		$formularios = explode(",",$formulariosEvoluciones);
		$arrayFormularios = array();
		for($i=0;$i<count($formularios);$i++)
		{
			$formulario = explode("-",$formularios[$i]);
			
			$arrayFormularios[$formulario[0]."-".$formulario[1]]['formulario'] = $formulario[0];
			$arrayFormularios[$formulario[0]."-".$formulario[1]]['consecutivo'] = $formulario[1];
		}
		
		$ultimaEvolucion = "";
		$formularioEvolucion = "";
		$fechaUltimaEvolucion = 0;
		foreach($arrayFormularios as $keyFormulario => $valueFormulario)
		{
			$queryEvolucionPaciente = "SELECT Fecha_data,Hora_data,movdat 
										   FROM ".$wbaseHce."_".$valueFormulario['formulario']." 
										  WHERE movhis='".$whistoria."' 
										    AND moving='".$wingreso."'
										    AND movcon='".$valueFormulario['consecutivo']."'
										    AND movdat != ''
									   ORDER BY Fecha_data DESC,Hora_data DESC
										  LIMIT 1;";
			
			$resEvolucionPaciente =  mysql_query($queryEvolucionPaciente,$conex) or die ("Error: ".mysql_errno()." - en el query consultar evolucion en formulario: ".$queryEvolucionPaciente." - ".mysql_error());
			$numEvolucionPaciente = mysql_num_rows($resEvolucionPaciente);	
			
			if($numEvolucionPaciente > 0)
			{
				$rowEvolucionPaciente = mysql_fetch_array($resEvolucionPaciente);
				
				$fechaEvolucion = strtotime($rowEvolucionPaciente['Fecha_data']." ".$rowEvolucionPaciente['Hora_data']);
				
				if($fechaEvolucion > $fechaUltimaEvolucion)
				{
					// $ultimaEvolucion = trim($rowEvolucionPaciente['movdat']);
					// $ultimaEvolucion = "EVOLUCIÓN: \n".trim($rowEvolucionPaciente['movdat']);
					
					$ultimaEvolucion = "EVOLUCIÓN: \n\n".$rowEvolucionPaciente['Fecha_data']." ".$rowEvolucionPaciente['Hora_data']." - ".trim($rowEvolucionPaciente['movdat']);
					$formularioEvolucion = $valueFormulario['formulario'];
					$fechaUltimaEvolucion = $fechaEvolucion;
				}
			}
		}
		
		if($ultimaEvolucion!="")
		{
			$fechaFormulario = date("Y-m-d",$fechaUltimaEvolucion);
			$horaFormulario = date("H:i:s",$fechaUltimaEvolucion);
			
			$notasAclaratorias = consultarNotasAclaratoriasHCE( $whistoria, $wingreso, $fechaFormulario, $horaFormulario, $formularioEvolucion );
			$ultimaEvolucion .= "\n\n".$notasAclaratorias;
		}
		
		return $ultimaEvolucion;
		
	}
	
	//Funcion que imprime una tabla con las auditorias realizadas a un paciente para una historia ingreso (Parametros: historia, ingreso)
	function cargarListaDeAuditorias( $whis, $wing ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$lista_auditorias = "<table border=0 align='center' width='100%' >";
		$lista_auditorias.= "<tr><td align='right'><input type='button' value='Crear nueva' id='add_auditoria' /></td></tr>";
		$lista_auditorias.= "<tr class='encabezadoTabla'><td>Auditarias del paciente</td></tr>";
		$query = "	SELECT 		Fecha_data as fecha "
				 ."	  FROM 		".$wbasedato."_000136 "
				 ."	 WHERE 		Audhis = '".$whis."'"
				 ."    AND  	Auding = '".$wing."'"
			."	  GROUP BY      Fecha_data "
			 ."	  ORDER BY 		Fecha_data";
		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
				$ii=0;
				while( $row = mysql_fetch_assoc($res) ){
						$wclass="fila2";
					$lista_auditorias.="<tr class='".$wclass."'><td align=center><A HREF='#formulario_auditoria' onClick= \" javascript:consultarAuditorias('".$whis."','".$wing."','".$row['fecha']."')  \" class=tipo3V>".$row['fecha']."</A></td></tr>";
				}
		}
		$lista_auditorias.= "</table>";
		echo  $lista_auditorias;
	}

	function consultarListaEgresados($wfecha_ini, $wfecha_fin){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		echo "<table>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th colspan=9>Auditorias realizadas entre el dia ".$wfecha_ini." y ".$wfecha_fin."<br>(Si no aparece la historia buscada es porque no tiene registros)</th>";
		echo "</tr>";
		echo "<tr class='encabezadoTabla'>";
		echo "<th>Historia</th>";
		echo "<th>Ingreso</th>";
		echo "<th>Paciente</th>";
		echo "<th>Médico(s) Tratante(s)</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>";


			 //PARA LA INFORMACION DEL PACIENTE
		 $q = "SELECT Audhis as historia, Auding as ingreso, CONCAT( pacno1,' ', pacno2,' ', pacap1,' ', pacap2) as paciente, "
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento "
            ."   FROM root_000036, root_000037, ".$wbasedato."_000136 A  "
            ."  WHERE A.Fecha_data BETWEEN '".$wfecha_ini."' AND '".$wfecha_fin."'"
			."    AND orihis = Audhis "
			."    AND oriing = Auding "
            ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia,
            ."    AND oriced  = pacced "
            ."    AND oritid  = pactid "
		."   GROUP BY Audhis, Auding "
		."   ORDER BY Audhis ";

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
	function mostrarAuditoria($whis, $wing, $wfec){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$usuario_session = $_SESSION['user'];
		$pos = strpos($usuario_session,"-");
		$usuario_session = substr($usuario_session,$pos+1,strlen($usuario_session));

		$fecha_hoy = date("Y-m-d");

		$query = "		SELECT 		Seguridad as usuario, Fecha_data as fecha, Hora_data as hora, Audest as estancia, Audmed as medicamentos, Audayd as ayudas_diagnosticas, "
					 ."   			Audpob as posibles_objeciones, Audvob as valor_objeciones, Audead as eventos_adversos, "
					 ."				Audrei as reingreso, Audalt as alta_temprana, Audobs as observaciones "
					 ."	  FROM 		".$wbasedato."_000136 "
					 ."	 WHERE 		Audhis = '".$whis."'"
					 ."    AND  	Auding = '".$wing."'"
					 ."    AND 		Fecha_data = '".$wfec."'"
					 ." ORDER BY    Fecha_data DESC, Hora_data DESC";

		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		$datos = array();

		if ($num > 0 ){
				$dato['estancia'] = "";  $dato['medicamentos'] = "";   $dato['ayudas_diagnosticas'] = "";
				$dato['posibles_objeciones'] = "";  $dato['eventos_adversos'] = "";   $dato['reingreso'] = "";
				$dato['alta_temprana'] = "";   $dato['observaciones'] = "";

				$i = 1;
				while( $row = mysql_fetch_assoc($res) ){
					$pos = strpos($row['usuario'],"-");
					$usuario_buscado = substr($row['usuario'],$pos+1,strlen($row['usuario']));

					//Si la auditoria fue hecha el dia de hoy por el usuario logueado, se puede editar
					if( $row['fecha'] == $fecha_hoy ){
						$row['editable'] = true;
						$datos['auditoria_editable'] = $row;
					}
					//Agregando todas las auditorias
					$user_name = buscarUsuario( $usuario_buscado );
					$row['usuario'] = $user_name;
					$cadena_al_final = "\n".$user_name."  ".$row['fecha']."  ".$row['hora'];
					if( $i < $num ) $cadena_al_final.=" \n \n================================================= \n \n";

					if( $row['estancia'] != "" ) $dato['estancia'] .= $row['estancia']."\n---------------------------------------------".$cadena_al_final;
					if( $row['medicamentos'] != "" ) $dato['medicamentos'] .= $row['medicamentos']."\n---------------------------------------------".$cadena_al_final;
					if( $row['ayudas_diagnosticas'] != "" ) $dato['ayudas_diagnosticas'] .= $row['ayudas_diagnosticas']."\n---------------------------------------------".$cadena_al_final;
					if( $row['valor_objeciones'] != "" ) $dato['posibles_objeciones'] .= "Valor: ".$row['valor_objeciones']." \n";
					if( $row['posibles_objeciones'] != "" ) $dato['posibles_objeciones'] .= $row['posibles_objeciones']."\n---------------------------------------------".$cadena_al_final;
					if( $row['eventos_adversos'] != "" ) $dato['eventos_adversos'] .= $row['eventos_adversos']."\n---------------------------------------------".$cadena_al_final;
					if( $row['reingreso'] != "" ) $dato['reingreso'] .= $row['reingreso']."\n---------------------------------------------".$cadena_al_final;
					if( $row['alta_temprana'] != "" ) $dato['alta_temprana'] .= $row['alta_temprana']."\n---------------------------------------------".$cadena_al_final;
					if( $row['observaciones'] != "" ) $dato['observaciones'] .= $row['observaciones']."\n---------------------------------------------".$cadena_al_final;
					$i++;
				}
				$datos['auditorias_todas'] = $dato;
		}
		echo json_encode( $datos );
	}

	//Funcion que almacena en matrix una auditoria ( El parámetro es un arreglo asociativo con los datos de una auditoria )
	function guardarAuditoria( $auditoria ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$actualizar = false;
		$usuario = $_SESSION['user'];
		$pos = strpos($usuario,"-");
		$usuario = substr($usuario,$pos+1,strlen($usuario));
		//Verificar que no hayan datos vacios
		if( empty($auditoria['estancia']) && empty($auditoria['medicamentos']) && empty($auditoria['ayudas_diagnosticas']) && empty($auditoria['posibles_objeciones'])
			&& empty($auditoria['valor_objeciones']) && empty($auditoria['eventos_adversos']) && empty($auditoria['reingreso']) && empty($auditoria['alta_temprana'])
			&& empty($auditoria['observaciones']) ){
			echo "V";
			return;
		}


		if($auditoria['guardarBitacora'] != "")
			guardarObsEnBitacora( $auditoria );

		//SE CONSULTA SI EL USUARIO YA REALIZO UNA AUDITORIA PARA EL PACIENTE EL DIA DE HOY, SI ES ASI SE ACTUALIZA CONCATENANDO EL NUEVO TEXTO
		//EN CASO CONTRARIO SE INSERTA EL REGISTRO
		$query = "		SELECT 		* "
					 ."	  FROM 		".$wbasedato."_000136 "
					 ."	 WHERE 		Audhis = '".$auditoria['historia']."'"
					 ."    AND  	Auding = '".$auditoria['ingreso']."'"
					 ."    AND 		Fecha_data = '".date("Y-m-d")."'"
					 ."    AND      Seguridad = 'C-".$usuario."'";

		$num = 0;
		$res = mysql_query($query, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 ){
			$row = mysql_fetch_assoc($res);
				$actualizar = true;
				( $auditoria['estancia'] != "" )? $auditoria['estancia'] = $auditoria['estancia']."\n".$row['Audest'] : $auditoria['estancia'] = $row['Audest'];
				( $auditoria['medicamentos'] != "" )? $auditoria['medicamentos'] = $auditoria['medicamentos']."\n".$row['Audmed'] : $auditoria['medicamentos'] = $row['Audmed'];
				( $auditoria['ayudas_diagnosticas'] != "" )? $auditoria['ayudas_diagnosticas'] = $auditoria['ayudas_diagnosticas']."\n".$row['Audayd'] : $auditoria['ayudas_diagnosticas'] = $row['Audayd'];
				( $auditoria['posibles_objeciones'] != "" )? $auditoria['posibles_objeciones'] = $auditoria['posibles_objeciones']."\n".$row['Audpob'] : $auditoria['posibles_objeciones'] = $row['Audpob'];
				( $auditoria['valor_objeciones'] != "" )? $auditoria['valor_objeciones'] = $auditoria['valor_objeciones']."\n".$row['Audvob'] : $auditoria['valor_objeciones'] = $row['Audvob'];
				( $auditoria['eventos_adversos'] != "" )? $auditoria['eventos_adversos'] = $auditoria['eventos_adversos']."\n".$row['Audead'] : $auditoria['eventos_adversos'] = $row['Audead'];
				( $auditoria['alta_temprana'] != "" )? $auditoria['alta_temprana'] = $auditoria['alta_temprana']."\n".$row['Audalt'] :  $auditoria['alta_temprana'] = $row['Audalt'];
				( $auditoria['reingreso'] != "" )? $auditoria['reingreso'] = $auditoria['reingreso']." \n".$row['Audrei'] : $auditoria['reingreso'] = $row['Audrei'];
				( $auditoria['observaciones'] != "" )? $auditoria['observaciones'] = $auditoria['observaciones']."\n".$row['Audobs'] : $auditoria['observaciones'] = $row['Audobs'];
		}
		if( $actualizar == false){
			//Insertar registro
			$q="INSERT INTO
				".$wbasedato."_000136 (medico, Fecha_data, Hora_data, Audhis, Auding, Audest, Audmed, Audayd, Audpob, Audvob, Audead, Audrei, Audalt, Audobs, Seguridad)
			VALUES
				('movhos','".date("Y-m-d")."','".(string)date("H:i:s")."','".$auditoria['historia']."','".$auditoria['ingreso']."','".$auditoria['estancia']."','".$auditoria['medicamentos']."','".$auditoria['ayudas_diagnosticas']."','".	$auditoria['posibles_objeciones']."','".$auditoria['valor_objeciones']."','".$auditoria['eventos_adversos']."','".$auditoria['reingreso']."','".$auditoria['alta_temprana']."','".$auditoria['observaciones']."', 'C-".$usuario."');";
			$res = mysql_query($q, $conex);
			$guardo = mysql_insert_id();
			if( $guardo ){
				echo "I";
			}else{
				echo "V";
			}
		}else{
			$user_aux = "C-".$usuario;
			$q="UPDATE
				".$wbasedato."_000136 SET	Hora_data = '".date("H:i:s")."',
											Audest = '".$auditoria['estancia']."',
											Audmed = '".$auditoria['medicamentos']."',
											Audayd = '".$auditoria['ayudas_diagnosticas']."',
											Audpob = '".$auditoria['posibles_objeciones']."',
											Audvob = '".$auditoria['valor_objeciones']."',
											Audead = '".$auditoria['eventos_adversos']."',
											Audrei = '".$auditoria['reingreso']."',
											Audalt = '".$auditoria['alta_temprana']."',
											Audobs = '".$auditoria['observaciones']."'
									WHERE   Audhis = '".$auditoria['historia']."'
									  AND   Auding = '".$auditoria['ingreso']."'
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

	function guardarObsEnBitacora( $auditoria ){
		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		if( $auditoria['observaciones'] == "" )
			return;

		$auditoria['observaciones'] = utf8_decode($auditoria['observaciones']);
		$wtema = consultarAliasPorAplicacion($conex, $wemp_pmla, "tema_bitacora_auditoria_medica");
		$usuario = $_SESSION['user'];
		$pos = strpos($usuario,"-");
		$usuario = substr($usuario,$pos+1,strlen($usuario));

		$query = "SELECT  Connum "
		        ."  FROM  ".$wbasedato."_000001 "
                ." WHERE  Contip='Bitacora'";

		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
		$row = mysql_fetch_array($err);
		$wnci=$row[0] + 1;

		$query =  " UPDATE ".$wbasedato."_000001 SET Connum = Connum + 1 WHERE Contip='Bitacora'";
		$err = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");

		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");

		$query = " INSERT INTO ".$wbasedato."_000021 (medico,fecha_data,hora_data, Bithis, Biting, Bitnum, Bitusr, Bittem, Bitobs, Seguridad) "
                 ."  VALUES ('".$wbasedato."','".$fecha."','".$hora."','".$auditoria['historia']."','".$auditoria['ingreso']."',".$wnci.",'".$usuario."','".$wtema."','".$auditoria['observaciones']."','C-".$wbasedato."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO BITACORA : ".mysql_errno().":".mysql_error());
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
			$medi['observaciones'] = @strip_tags($wobserv);
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
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento, Audhis as tiene_necesidad "
            ."   FROM ".$wbasedato."_000018, root_000036, root_000037, "
			.$wbasedato."_000020 A LEFT JOIN ".$wbasedato."_000136 B ON (habhis=audhis AND habing=auding) "
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
			."        pacnac as nacimiento, pactid as tipo_identificacion, pacced as numero_documento, Audhis as tiene_necesidad "
            ."   FROM root_000036, root_000037, ".$wbasedato."_000011, "
			.$wbasedato."_000018 LEFT JOIN ".$wbasedato."_000136 ON (ubihis=audhis AND ubiing=auding) "
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
			echo "<th>Tiene<br>Auditoría</th>";
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

	//Al elegir (boton ver) un paciente de la lista se muestran todos los datos relacionados al paciente
	//(Parametros: historia, ingreso, servicio, fecha, nombre del paciente, documento del paciente, codigo habitacion, fecha nacimiento y medicos tratantes)
	function mostrarDetallePaciente($whistoria, $wingreso, $wservicio, $wfecha, $wpaciente, $wdoc_paciente, $whabitacion, $wnacimiento, $wmedico){

		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wbaseHce;

		$wfecha_actual = date("Y-m-d");
		//XXX$wfec_con = $wfecha;                              //Fecha a consultar para todas los datos del kardex
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
			/*echo "<tr class=encabezadoTabla>";
			echo "<th><font size=4 color='FFFF33'><b>".$wfecha."</b></font></th>";
			echo "</tr>";*/
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
					echo "<td align=center><textarea row=3 col=30 readonly>".$wobserv[$k]."</textarea></td>";                //Observacion
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
			// $evolucion = consultarUltimoDiagnosticoHCE( $conex, $wemp_pmla, $wbaseHce, $whistoria, $wingreso );
			$evolucion = consultarUltimaEvolucionHCE($whistoria, $wingreso );
			if ( is_null( $evolucion ) == false ){
				echo "<tr class='encabezadoTabla'>";
				echo "<td colspan=7 align=center><font size=4><b>EVOLUCION MEDICA</b></font></td>";
				echo "</tr>";
				echo "<tr class=fila1>";
				echo "<td align=center colspan=7><textarea rows=3 cols=120 readonly >".$evolucion."</textarea></td>";
				echo "</tr>";
			}
		}else{  //del 2do if ($num > 0)
			 echo "<br><br>";
			 echo "<center><table>";
			 echo "<tr class=encabezadoTabla><td>No existen datos de kardex para el paciente con la historia ".$whistoria." ".$wpaciente.", ingreso ".$wingreso."</td></tr>";
			 echo "</table></center><br><br>";
			 echo "<table width='95%'><tr><td>&nbsp;</td><td>&nbsp;</td></tr>";
		}
			//********************AUDITORIAS***************************

			$crear_auditorias = true;
			//SI ESTA HOSPITALIZADO PERMITE CREAR NUEVA AUDITORIA
			$q = " SELECT  ubihis "
				."   FROM  ".$wbasedato."_000018 "
				."  WHERE  ubihis  = '".$whistoria."' "
				."    AND  ubiing = '".$wingreso."'"
				."    AND  ubiald != 'on' "
				."    AND  ubiptr != 'on' ";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);
			if( $num <= 0 )
				$crear_auditorias = false;


			$titulo_auditoria = "Crear nueva auditoria";

			$formulario_auditoria ="<div><table id='formulario_auditoria' bgcolor='#ffffff' border=0 align='center' width='95%'>";
			$formulario_auditoria.="<tr class='encabezadoTabla'><td colspan=3><b>Pertinencia</b></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td class='fila2' colspan=3>Estancia</td></tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2><input type='radio' name='au_estancia' value='Si' checked='checked' />Si  <input type='radio' name='au_estancia' value='No' />No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_estancia') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea id='val_estancia' rows=4 cols=120 disabled='disabled'></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>Medicamentos</td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=2><input type='radio' name='au_medicamentos' value='Si' checked='checked' />Si  <input type='radio' name='au_medicamentos' value='No' />No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_medicamentos') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_medicamentos' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>Ayudas diagnosticas</td></tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2><input type='radio' name='au_ay_diag' value='Si' checked='checked' />Si  <input type='radio' name='au_ay_diag' value='No' />No</td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_ayu_diag') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_ayu_diag' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
			$formulario_auditoria.="<tr class='encabezadoTabla'>";
			$formulario_auditoria.="<td colspan=3><b>Posibles Objeciones</b></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2><input type='radio' name='au_pos_obj' value='Si'>Si  <input type='radio' name='au_pos_obj' value='No' checked='checked'>No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_pos_obj') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_pos_obj' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3 align='left'>Valor<input type='text' disabled='disabled' id='valor_objeciones' /></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";
			$formulario_auditoria.="<tr class='encabezadoTabla'>";
			$formulario_auditoria.="<td colspan=3><b>Eventos Adversos</b></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2><input type='radio' name='au_even_adv' value='Si'>Si  <input type='radio' name='au_even_adv' value='No' checked='checked'>No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_even_adv') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea  disabled='disabled' id='val_even_adv' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";
			$formulario_auditoria.="<tr class='encabezadoTabla'>";
			$formulario_auditoria.="<td colspan=3><b>Reingreso</b></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2><input type='radio' name='au_reingreso' value='Si'>Si  <input type='radio' name='au_reingreso' value='No' checked='checked'>No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_reingreso') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_reingreso' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";
			$formulario_auditoria.="<tr class='encabezadoTabla'>";
			$formulario_auditoria.="<td colspan=3><b>Alta Temprana</b></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2><input type='radio' name='au_alt_temp' value='Si'>Si  <input type='radio' name='au_alt_temp' value='No'>No </td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('val_alt_temp') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3><textarea disabled='disabled' id='val_alt_temp' rows=4 cols=120 ></textarea></td></tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";
			$formulario_auditoria.="<tr class='encabezadoTabla'>";
			$formulario_auditoria.="<td colspan=3><b>Observaciones</b></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=2>&nbsp;</td><td align='right'><input type='button' class='boton_editar' value='Editar' onClick=\" javascript:editarCampo('au_obs') \" /></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=3><textarea disabled='disabled' id='au_obs' rows=4 cols=120 ></textarea></td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'>";
			$formulario_auditoria.="<td colspan=3><input type='checkbox' id='guardar_en_bitacora' />Guardar observaciones en bitacora</td>";
			$formulario_auditoria.="</tr>";
			$formulario_auditoria.="<tr class='fila2'><td colspan=3>&nbsp;</td></tr>";

			$tit_bot = "Guardar";
			if( $crear_auditorias == true )
				$formulario_auditoria.="<tr class='fila2'><td colspan=3 align='center'><input type='button' value='".$tit_bot."' id='boton_guardar_auditoria'/></td></tr>";
			$formulario_auditoria.="</table></div>";

			$lista_auditorias = "<div id='listado_auditoria'><table border=0 align='center' width='100%' >";
			if( $crear_auditorias == true )
				$lista_auditorias.= "<tr><td align='right'><input type='button' value='Crear nueva' id='add_auditoria' /></td></tr>";
			$lista_auditorias.= "<tr class='encabezadoTabla'><td>Auditarias del paciente</td></tr>";

			//traer lista de auditorias para el paciente
			$query = "	SELECT 		Fecha_data as fecha"
					 ."	  FROM 		".$wbasedato."_000136 "
					 ."	 WHERE 		Audhis = '".$whistoria."'"
					 ."    AND  	Auding = '".$wingreso."'"
				."	  GROUP BY      Fecha_data "
				 ."	  ORDER BY 		Fecha_data";
			$num = 0;
			$res = mysql_query($query, $conex);
			$num = mysql_num_rows($res);
			if ($num > 0 ){
					$ii=0;
					while( $row = mysql_fetch_assoc($res) ){
						$wclass="fila2";
						$lista_auditorias.="<tr class='".$wclass."'><td align=center><A HREF='#formulario_auditoria' onClick= \" javascript:consultarAuditorias('".$whistoria."','".$wingreso."','".$row['fecha']."')  \" class=tipo3V>".$row['fecha']."</A></td></tr>";
					}
			}
			$lista_auditorias.= "</table></div>";

			echo "<tr class='fila2'>";
			echo "<td colspan=7 align=center>&nbsp;</td>";
			echo "</tr>";
			echo "<tr class='fila1'>";
			echo "<td colspan=7 align=center>&nbsp;</td>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla'>";
			echo "<td colspan=7 align=center><font size=4><b>AUDITORIAS</b></font></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center><b>Listado</b></td>";
			echo "<td align=center colspan=6 id='titulo_formulario_auditoria'><b>".$titulo_auditoria."</b></td>";
			echo "</tr>";
			echo "<tr class=fila1>";
			echo "<td align=center valign=top>".$lista_auditorias."</td>";
			echo "<td align=center colspan=6>".$formulario_auditoria."</td>";
			echo "</tr>";
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

		encabezado("AUDITORIA MEDICA CONCURRENTE", $wactualiz, "clinica");

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
		echo "<td colspan=4 class='encabezadotabla' align='center'>Rango de fechas de Auditorias realizadas</td>";
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
		echo '<input type="button" class="botona" id="boton_consultar" value="Consultar"></input>';
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
	#formulario_auditoria input[type="text"], #formulario_auditoria textarea{
	background-color:#DAFFE6
	}

	#formulario_auditoria input[type="text"]:disabled, #formulario_auditoria textarea:disabled{
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
	var auditoria_editable_global;
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

		$("#wfec_i, #wfec_f").datepicker({
		  showOn: "button",
		  buttonImage: "../../images/medical/root/calendar.gif",
		  buttonImageOnly: true,
		  maxDate:"+1D"
		});
		$(".filaoculta").hide();

		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});

		$("#boton_consultar").click(function() {
			realizarConsultaLista();
		});

		$("#input_historia").focusout(function(){
			if ($(this).val() !=""){
				$(this).val($(this).val().replace(/(\.\.)|(,,)|(,\.)|(\.,)|(^[\.|,])|([\.|,]$)|([^\d][^\d])|([^\d]$)/gi, ""));
			}
		});

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
	function validaciones_formulario_auditoria(){

		//Quitar las acciones que tengan asignadas al darle click a todos los input tipo radio
		$("#formulario_auditoria input[type='radio']").attr('onclick','').unbind('click');

		//PERTINENCIA ESTANCIA	//Si: inhabilitar input text  --- No: habilitar input text
		$('input[name=au_estancia]').click(function(){
			if( $(this).val() == "Si" ){
				(! $.browser.msie ) ? $("#val_estancia").attr("disabled", "disabled") : $("#val_estancia").attr("readonly", true).cssie();
				$("#val_estancia").val("");
			}else
				(! $.browser.msie ) ? $("#val_estancia").attr("disabled", false).focus() : $("#val_estancia").attr("readonly", false).cssie().focus()
		});

		//PERTINENCIA MEDICAMENTOS	//Si: inhabilitar input text  --- No: habilitar input text
		$('input[name=au_medicamentos]').click(function(){
			if( $(this).val() == "Si" ){
				(! $.browser.msie ) ? $("#val_medicamentos").attr("disabled", "disabled") : $("#val_medicamentos").attr("readonly", true).cssie();
				$("#val_medicamentos").val("");
			}else
				(! $.browser.msie ) ? $("#val_medicamentos").attr("disabled", false).focus() : $("#val_medicamentos").attr("readonly", false).cssie().focus();
		});

		//PERTINENCIA AYUDAS DIAGNOSTICAS	//Si: inhabilitar input text  --- No: habilitar input text
		$('input[name=au_ay_diag]').click(function(){
			if( $(this).val() == "Si" ){
				(! $.browser.msie ) ? $("#val_ayu_diag").attr("disabled", "disabled") : $("#val_ayu_diag").attr("readonly", true).cssie();
				$("#val_ayu_diag").val("");
			}else
				(! $.browser.msie ) ? $("#val_ayu_diag").attr("disabled", false).focus() : $("#val_ayu_diag").attr("readonly", false).cssie().focus();
		});

		//POSIBLES OBJECIONES	//Si: habilitar los 2 input text  --- No: inhabilitar los 2 input text
		$('input[name=au_pos_obj]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_pos_obj").attr("disabled", "disabled") : $("#val_pos_obj").attr("readonly", true).cssie();
				$("#valor_objeciones").attr("disabled", "disabled").cssie();
				$("#val_pos_obj").val("");
				$("#valor_objeciones").val("");
			}else{
				(! $.browser.msie ) ? $("#val_pos_obj").attr("disabled", false).focus() : $("#val_pos_obj").attr("readonly", false).cssie().focus();
				$("#valor_objeciones").attr("disabled", false).cssie();
				$("#valor_objeciones").attr("readonly", false);
			}
		});

		//EVENTOS ADVERSOS	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=au_even_adv]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_even_adv").attr("disabled", "disabled") :  $("#val_even_adv").attr("readonly", true).cssie();
				$("#val_even_adv").val("");
			}else
				(! $.browser.msie ) ? $("#val_even_adv").attr("disabled", false).focus() : $("#val_even_adv").attr("readonly", false).cssie().focus();
		});

		//REINGRESO	//Si: habilitar input text  --- No: inhabilitar input text
		$('input[name=au_reingreso]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_reingreso").attr("disabled", "disabled") : $("#val_reingreso").attr("readonly", true).cssie();
				$("#val_reingreso").val("");
			}else
				(! $.browser.msie ) ? $("#val_reingreso").attr("disabled", false).focus() :  $("#val_reingreso").attr("readonly", false).cssie().focus();
		});

		//ALTA TEMPRANA	//Habilitar si le da click a cualquiera
		$('input[name=au_alt_temp]').click(function(){
			(! $.browser.msie ) ? $("#val_alt_temp").attr("disabled", false).focus() : $("#val_alt_temp").attr("readonly", false).cssie().focus();
		});

		//Solo valores numericos
		$("#valor_objeciones").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9\.]/g, ""));
		});

		//Si es internet explorer: readonly; Si es otro explorador: disabled
		(! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true);
		(! $.browser.msie ) ? $("#valor_objeciones").attr("disabled", "disabled") : $("#valor_objeciones").attr("readonly", true);
		(! $.browser.msie ) ? $("#au_obs").attr("disabled", false).cssie() :  $("#au_obs").attr("readonly", false).cssie();
	}

	//funcion que deja el formulario de auditoria con las opciones predeterminadas.
	function restablecer_formulario(){
		validaciones_formulario_auditoria();

		$('input[name=au_estancia][value=Si]').prop('checked', true);
		$('input[name=au_medicamentos][value=Si]').prop('checked', true);
		$('input[name=au_ay_diag][value=Si]').prop('checked', true);
		$('input[name=au_pos_obj][value=No]').prop('checked', true);
		$('input[name=au_even_adv][value=No]').prop('checked', true);
		$('input[name=au_reingreso][value=No]').prop('checked', true);
		$("#val_estancia").val("");
		$("#val_medicamentos").val("");
		$("#val_ayu_diag").val("");
		$("#val_pos_obj").val("");
		$("#valor_objeciones").val("");
		$("#val_even_adv").val("");
		$("#val_reingreso").val("");
		$("#val_alt_temp").val("");
		$("#au_obs").val("");

		$("#titulo_formulario_auditoria").html("");
		$("#boton_guardar_auditoria").show('slow');
		$("#formulario_auditoria input").attr("disabled", false);
		$("#valor_objeciones").attr("disabled", "disabled");

		auditoria_editable_global = "";
		editando_global = false;
		$(".boton_editar").hide();
		$(".campos_editables").remove();
		$("#formulario_auditoria textarea").attr('cols',120);
		$("#guardar_en_bitacora").attr('checked', false );
		if( $.browser.msie ) $("#formulario_auditoria textarea").attr("disabled", false);
		(! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true);
		if( $.browser.msie ) $("#valor_objeciones").css('background-color','#ffffff');
		$("#au_obs").attr("disabled", false);
		$('#au_obs').attr('readonly', false );
	}

	//funcion que se activa cuando se da click al boton retornar, restaura la pagina
	function restablecer_pagina(){
		if( mostrando_paciente == true ){
			$('#resultados_lista').show('slow');
			$('#resultados_paciente').hide('slow');
			mostrando_paciente = false;
		}else{
			$('#listado_auditoria').html("");
			if (! $.browser.msie ) {
				$(".rep_parametros").fadeIn('slow');
				$('#resultados_paciente').hide('slow');
				$('#resultados_lista').hide('slow');
				$("#enlace_retornar").hide('slow');
				$(".parametros").show('slow');
			}else{
				$(".rep_parametros").fadeIn();
				$('#resultados_paciente').hide();
				$('#resultados_lista').hide();
				$("#enlace_retornar").hide();
				$(".parametros").show();
			}
			mostrando_paciente = false;
			historia_global = 0;
			ingreso_global = 0;
		}
	}

	//Funcion que trae del servidor las auditorias en esa fecha realizadas a un paciente
	function consultarAuditorias(historia, ingreso, fecha){
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();
	    $.blockUI({ message: $('#msjEspere') });
		$.get('auditoria_medica.php', { wemp_pmla: wemp_pmla, action: "consultandoAuditoria", historia: historia, ingreso: ingreso, fecha: fecha, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				llenarFormulario( data, fecha );
			}, 'json');
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

	//Funcion que agrega el nuevo textarea cuando puedo agregar una auditoria y visualizar las otras
	function validaciones_nuevos_textarea(){

		$('input[name=au_estancia]').click(function(){
			if( $(this).val() == "Si" ){
				(! $.browser.msie ) ? $("#val_estanciax").attr("disabled", "disabled") : $("#val_estanciax").attr("readonly", true).cssie();
				$("#val_estanciax").val("");
			}else
				(! $.browser.msie ) ? $("#val_estanciax").attr("disabled", false).cssie().focus() : $("#val_estanciax").attr("readonly", false).cssie().focus();
		});
		$('input[name=au_medicamentos]').click(function(){
			if( $(this).val() == "Si" ){
				(! $.browser.msie ) ? $("#val_medicamentosx").attr("disabled", "disabled") : $("#val_medicamentosx").attr("readonly", true).cssie();
				$("#val_medicamentosx").val("");
			}else
				(! $.browser.msie ) ? $("#val_medicamentosx").attr("disabled", false).focus() : $("#val_medicamentosx").attr("readonly", false).cssie().focus();
		});

		$('input[name=au_ay_diag]').click(function(){
			if( $(this).val() == "Si" ){
				(! $.browser.msie ) ? $("#val_ayu_diagx").attr("disabled", "disabled") : $("#val_ayu_diagx").attr("readonly", true).cssie();
				$("#val_ayu_diagx").val("");
			}else
				(! $.browser.msie ) ? $("#val_ayu_diagx").attr("disabled", false).focus() :  $("#val_ayu_diagx").attr("readonly", false).cssie().focus();
		});

		$('input[name=au_pos_obj]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_pos_objx").attr("disabled", "disabled") : $("#val_pos_objx").attr("readonly", true).cssie();
				$("#valor_objeciones").attr("disabled", "disabled").cssie();
				$("#val_pos_objx").val("");
				$("#valor_objeciones").val("");
			}else{
				(! $.browser.msie ) ? $("#val_pos_objx").attr("disabled", false).focus() : $("#val_pos_objx").attr("readonly", false).cssie().focus();
				$("#valor_objeciones").attr("disabled", false).cssie();
				$("#valor_objeciones").attr("readonly", false).cssie();
			}
		});

		$('input[name=au_even_adv]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_even_advx").attr("disabled", "disabled") : $("#val_even_advx").attr("readonly", true).cssie();
				$("#val_even_advx").val("");
			}else
				(! $.browser.msie ) ? $("#val_even_advx").attr("disabled", false).focus() :  $("#val_even_advx").attr("readonly", false).cssie().focus();
		});

		$('input[name=au_reingreso]').click(function(){
			if( $(this).val() == "No" ){
				(! $.browser.msie ) ? $("#val_reingresox").attr("disabled", "disabled") : $("#val_reingresox").attr("readonly", true).cssie();
				$("#val_reingresox").val("");
			}else
				(! $.browser.msie ) ? $("#val_reingresox").attr("disabled", false).focus() : $("#val_reingresox").attr("readonly", false).cssie().focus();
		});

		$('input[name=au_alt_temp]').click(function(){
			(! $.browser.msie ) ? $("#val_alt_tempx").attr("disabled", false).focus() : $("#val_alt_tempx").attr("readonly", false).focus().cssie();
		});

		if( $.browser.msie ) $("#au_obsx").cssie();

		var campos_array = new Array('val_estancia','val_medicamentos','val_ayu_diag','val_pos_obj','val_even_adv','val_reingreso','val_alt_temp','au_obs');
		for( i in campos_array ){
			//Agregando textarea
			campo = jQuery("#"+campos_array[i]);
			cols = campo.attr("cols");
			cols=cols-26;
			cols = parseInt( cols/2 );
			campo.attr("cols", cols);
			campo.before("<textarea id='"+campos_array[i]+"x' rows=4 class='campos_editables' cols="+cols+"></textarea>");
		}
	}

	//Luego de traer los datos de la auditoria esta funcion se encarga de mostralos en el formulario
	function llenarFormulario( auditorias, fecha ){
		restablecer_formulario();
		var auditoria = auditorias['auditorias_todas'];
		var auditoria_editable = auditorias['auditoria_editable'];

		if ( auditoria_editable != undefined ){
			auditoria_editable_global = auditoria_editable;
			editando_global = true;
		}

		$("#formulario_auditoria textarea").removeAttr("disabled");
		$("#valor_objeciones").removeAttr("disabled");
		//PERTINENCIA ESTANCIA
		if( auditoria.estancia == ""){
			$('input[name=au_estancia][value=Si]').prop('checked', true);
			$("#val_estancia").attr("disabled", "disabled");
		}else{
			$('input[name=au_estancia][value=No]').prop('checked', true);
			$("#val_estancia").val( auditoria.estancia );
		}
		//PERTINENCIA MEDICAMENTOS
		if( auditoria.medicamentos == ""){
			$('input[name=au_medicamentos][value=Si]').prop('checked', true);
			$("#val_medicamentos").attr("disabled", "disabled");
		}else{
			$('input[name=au_medicamentos][value=No]').prop('checked', true);
			$("#val_medicamentos").val( auditoria.medicamentos );
		}
		//PERTINENCIA AYUDAS DIAGNOSTICAS
		if( auditoria.ayudas_diagnosticas == ""){
			$('input[name=au_ay_diag][value=Si]').prop('checked', true);
			$("#val_ayu_diag").attr("disabled", "disabled");
		}else{
			$('input[name=au_ay_diag][value=No]').prop('checked', true);
			$("#val_ayu_diag").val( auditoria.ayudas_diagnosticas );
		}
		//POSIBLES OBJECIONES
		if( auditoria.valor_objeciones == ""){
			$('input[name=au_pos_obj][value=No]').prop('checked', true);
			$("#val_pos_obj").attr("disabled", "disabled");
			$("#valor_objeciones").attr("disabled", "disabled");
		}else{
			$('input[name=au_pos_obj][value=Si]').prop('checked', true);
			$("#val_pos_obj").val( auditoria.posibles_objeciones );
			$("#valor_objeciones").val(auditoria.valor_objeciones);
		}
		//EVENTOS ADVERSOS
		if( auditoria.eventos_adversos == ""){
			$('input[name=au_even_adv][value=No]').prop('checked', true);
			$("#val_even_adv").attr("disabled", "disabled");
		}else{
			$('input[name=au_even_adv][value=Si]').prop('checked', true);
			$("#val_even_adv").val( auditoria.eventos_adversos );
		}
		//REINGRESO
		if( auditoria.reingreso == ""){
			$('input[name=au_reingreso][value=No]').prop('checked', true);
			$("#val_reingreso").attr("disabled", "disabled");
		}else{
			$('input[name=au_reingreso][value=Si]').prop('checked', true);
			$("#val_reingreso").val( auditoria.reingreso );
		}
		//ALTA TEMPRANA
		if( auditoria.alta_temprana == ""){
			$('input[name=au_alt_temp][value=Si]').prop('checked', true);
			$("#val_alt_temp").attr("disabled", "disabled");
		}else{
			$('input[name=au_alt_temp][value=No]').prop('checked', true);
			$("#val_alt_temp").val( auditoria.alta_temprana );
		}
		//OBSERVACIONES
		var observaciones = $("#au_obs").val( auditoria.observaciones );

		$("#titulo_formulario_auditoria").html("<b>Auditoria(s) realizada(s) el dia "+fecha+"</b>");

		if( $.browser.msie ) $("#formulario_auditoria textarea").attr("disabled", false);
		$("#formulario_auditoria input").attr("disabled", "disabled");
		(! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true)

		if( $.browser.msie ) $("#formulario_auditoria textarea").css('background-color','#ffffff');
		if( $.browser.msie ) $("#formulario_auditoria input[type='text']").css('background-color','#ffffff');

		if ( auditoria_editable != undefined ){
			$("#formulario_auditoria input[type='radio']").attr('onclick','').unbind('click');
			validaciones_nuevos_textarea();

			$('input[name=au_estancia][value=Si]').prop('checked', true);
			$('input[name=au_medicamentos][value=Si]').prop('checked', true);
			$('input[name=au_ay_diag][value=Si]').prop('checked', true);
			$('input[name=au_pos_obj][value=No]').prop('checked', true);
			$('input[name=au_even_adv][value=No]').prop('checked', true);
			$('input[name=au_reingreso][value=No]').prop('checked', true);
			//ACTIVA LA FUNCION QUE MUESTRA EL OTRO TEXTAREA PARA CADA CAMPO DEL FORMULARIO
			if( $.browser.msie ) $("#formulario_auditoria textarea").attr("disabled", false);
			$("#formulario_auditoria input").attr("disabled", "disabled");
			(! $.browser.msie ) ? $("#formulario_auditoria textarea").attr("disabled", "disabled") : $("#formulario_auditoria textarea").attr("readonly", true);
			//habilita todos los input tipo radio
			$("#formulario_auditoria input[type='radio']").attr("disabled", false);
			$("#formulario_auditoria input[type='checkbox']").attr("disabled", false);
			$("#boton_guardar_auditoria").attr("disabled", false);
			$("#boton_guardar_auditoria").show();
			$("#add_auditoria").hide();
			$('#au_obsx').attr('disabled', false );
			$('#au_obsx').attr('readonly', false );

			if( $.browser.msie ) $("#formulario_auditoria textarea").css('background-color','#ffffff');
			if( $.browser.msie ) $("#valor_objeciones").css('background-color','#ffffff');
			if ( $.browser.msie )  $("#au_obsx").cssie();
		}
	}

	//Funcion que manda al servidor los datos ingresados en una auditoria
	function guardarAuditoria(){
		var text_estancia="";
		var text_medicamentos = "";
		var text_ay_diag = "";
		var text_pos_obj = "";
		var valor_pos_obj = "";
		var text_even_adv = "";
		var text_reingreso = "";
		var text_alt_temp = "";
		var observaciones = "";

		//SI NO SE ESTA EDITANDO
		if( editando_global == false ){
			text_estancia = $("#val_estancia").val();
			text_medicamentos = $("#val_medicamentos").val();
			text_ay_diag = $("#val_ayu_diag").val();
			text_pos_obj = $("#val_pos_obj").val();
			valor_pos_obj = $("#valor_objeciones").val();
			if(text_pos_obj != "" && valor_pos_obj == ""){
				alerta("Debe seleccionar un valor para posibles objeciones");
				return;
			}
			text_even_adv = $("#val_even_adv").val();
			text_reingreso = $("#val_reingreso").val();
			text_alt_temp = $("#val_alt_temp").val();
			observaciones = $("#au_obs").val();
			//SI SE ESTA EDITANDO
		}else{
				text_estancia = $("#val_estanciax").val();
				text_medicamentos = $("#val_medicamentosx").val();
				text_ay_diag = $("#val_ayu_diagx").val();
				text_even_adv = $("#val_even_advx").val();
				text_reingreso = $("#val_reingresox").val();
				text_alt_temp = $("#val_alt_tempx").val();
				observaciones = $("#au_obsx").val();
				text_pos_obj = $("#val_pos_objx").val();
				valor_pos_obj = $("#valor_objeciones").val();
		}
		var result = text_estancia + text_medicamentos + text_ay_diag + text_pos_obj + valor_pos_obj + text_even_adv + text_reingreso + text_alt_temp + observaciones;
		result=result.replace(/ /g,"");
		result=result.replace(/\n/g,"");
		result=result.replace(/\r/g,"");
		if( result == "" ){
			alerta("No ha escrito nada para guardar");
			return;
		}
		var guardarBitacora = "";
		//DATOS CORRECTOS
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		var wemp_pmla = $("#wemp_pmla").val();

		if ( $("#guardar_en_bitacora").is(":checked") == true )
			guardarBitacora = true;

		if( guardarBitacora == true && observaciones == ""){
			var seguir = confirm("Ha seleccionado guardar las observaciones en bitacora, pero no ha ingresado la observacion.¿Continuar de todos modos?");
			if( !seguir ) return;
		}
		$.blockUI({ message: $('#msjEspere') });

		//Realiza el llamado ajax con los parametros de busqueda
		$.post('auditoria_medica.php', { wemp_pmla: wemp_pmla, action: "guardarAuditoria", consultaAjax: aleatorio,
										historia: historia_global,
										ingreso: ingreso_global,
										estancia: text_estancia,
										medicamentos: text_medicamentos,
										ayudas_diagnosticas: text_ay_diag,
										posibles_objeciones: text_pos_obj,
										valor_objeciones: valor_pos_obj,
										eventos_adversos: text_even_adv,
										reingreso: text_reingreso,
										alta_temprana: text_alt_temp,
										observaciones: observaciones,
										guardarBitacora: guardarBitacora
										} ,
			function(data) {
				//oculta el mensaje de cargando
				$.unblockUI();
				if( data == "A" ){
					alerta("Actualizacion realizada con exito");
					restablecer_formulario();
				}else if(data == "I"){
					setTimeout( function(){
							$("#add_auditoria").hide();
						}, 500 );
					alerta("Guardado realizado con exito");
					restablecer_formulario();
					cargarListaAuditorias();
				}else if(data == "V"){
					alert('No se ha guardado, por intente nuevamente');
				}else{
					alerta("Ha ocurrido un error, por favor intente nuevamente");
				}
			});
	}

	//Funcion que trae del servidor la lista de auditorias del paciente
	function cargarListaAuditorias(){
		var wemp_pmla = $("#wemp_pmla").val();
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		$.get('auditoria_medica.php', { wemp_pmla: wemp_pmla, action: "consultarListaAuditorias", historia: historia_global, ingreso: ingreso_global, consultaAjax: aleatorio} ,
			function(data) {
				$('#listado_auditoria').html(data);
				restablecer_formulario();
			});
	}

	//funcion que luego de elegir el centro de costos, me trae los pacientes que se encuentran en el
	function realizarConsultaLista(){

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
			restablecer_pagina();
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
		$.get('auditoria_medica.php', { wemp_pmla: wemp_pmla, action: accion, responsable: responsable, servicio: servicio, fecha: fecha, historia:historia, consultaAjax: aleatorio} ,
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
		$.post('auditoria_medica.php', { wemp_pmla: wemp_pmla, action: 'consultarListaEgresados', fecha_i: fecha_i, fecha_f: fecha_f, consultaAjax: aleatorio} ,
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
		$.get('auditoria_medica.php', { wemp_pmla: wemp_pmla, action: "mostrarPaciente", historia: historia, ingreso: ingreso, servicio: servicio, fecha: fecha,
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
				$("#boton_guardar_auditoria").click(function() {
					guardarAuditoria();
				});
				$("#formulario_auditoria textarea").cssie();
				$("#add_auditoria").click(function() {
					restablecer_formulario();
					$("#titulo_formulario_auditoria").html("<b>Crear nueva auditoria</b>");
				});
				$(".boton_editar").hide();
				mostrarUltimaAuditoria();
			});
	}

	//Funcion que da click al ultimo enlace de la lista de auditorias
	function mostrarUltimaAuditoria(){
		setTimeout(function(){
			$("#listado_auditoria a:last").trigger('click');
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