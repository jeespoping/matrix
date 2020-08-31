<?php
include_once("conex.php");



if(isset($operacion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($operacion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
   	
  

  include_once("root/comun.php");

  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wcliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
  $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	
  $actualiz="Abril 5 de 2018";
/*
	Descripcion
	==============================================================================
	Este programa permite que por medio de una configuracion en la tabla movhos_000240 se generen reportes extrayendo datos de la HCE, genera reportes por:
	Entidad = Pacientes con evolucion por entidad.
	Centro de costos = Pacientes con evolucion por centro de costos.
	Especialista que atiende al paciente = En este reporte se muetran las evolucion que han hechos los especialistas.
	Especialidad que solicita la interconsulta = en este reporte se muestran las especialidades que han solicitado intenconsulta con la especialidad configurada.
	Basados en configuracion = En este reporte se muestran datos estadisticos con respecto a un consecutivo del formulario configurado, solo de tipo seleccion.
	Pacientes sin atender = Pacientes que tienen orden medica y no tienen evolucion.
	==============================================================================
	ACTUALIZACIONES
	==============================================================================
	Abril 5 de 2018: Jonatan: Se agrega al reporte los estudiantes asociados a los medicos de la especialidad.
	==============================================================================
	Enero 16 de 2018: Jonatan: Se agrega reporte por centro de costos en la pestaña de basado en registros hce.
	==============================================================================
	Noviembre 22 de 2017 Jonatan: Se corrige la impresion correcta de las tildes y se cambia el texto de la ultima pestaña aclarando que el reportes es con las evoluciones hasta hoy.
	==============================================================================
	Noviembre 9 de 2017 Jonatan: Se corrigen los centros de costos vacios, se muestra el nombre del formulario asociado, se corrigen los textos en la pestaña
									sin atender para que aclare que las consultas son hasta la fecha final seleccionada, se agregan columnas de alta y muerte con la fecha y hora,
									para el listado de entidad tambien se pone el formulario si no tiene entidad asociada.
	==============================================================================
	Octubre 17 de 2017 Jonatan: Se agregan validaciones para los arreglos vacios y no genere error al responder, ademas validar algunas tildes en la respuesta ajax.
	==============================================================================
	Octubre 10 de 2017 Jonatan: Se muestran correctamente las horas en el la columna tiempo promedio de la pestaña  Especialista.
								Se agregan al listado de pacientes en tiempo promedio los que tiene evolociion sin orden medica.
								En pacientes sin atender se agrega la especialidad y el especialista que solicita la interconsulta.
	==============================================================================
*/


if(isset($operacion) && $operacion == 'ver_detalle_atendidos_tiempo'){

	$datos_array_esp_tiempo = unserialize(base64_decode($datos_array_esp_tiempo));
	
	asort($datos_array_esp_tiempo); //Se ordena por el arreglo por la fecha.
	
	unset($datos_array_esp_tiempo['tiempo_promedio']);
	
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_tiempos'>";
	$html_detalle_pacientes.= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";	
	$html_detalle_pacientes.= "<table align=center><tr><td bgcolor='#3CB648'>&nbsp;&nbsp;&nbsp;</td><td><font size=2>Con Ordenes Electrónicas</font></td><td>&nbsp;&nbsp;&nbsp;</td><td class=fila1>&nbsp;&nbsp;&nbsp;</td><td class=fila2>&nbsp;&nbsp;&nbsp;</td><td><font size=2>Sin orden electrónica</font></td></tr></table>";
	$html_detalle_pacientes.= "<div align=center><b>".count($datos_array_esp_tiempo)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 class='table-bordered' width='90%' id='detalle_esp_solicita_interconsulta'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td><td align=center>Fecha Hora Orden</td><td align=center>Fecha Hora Evolución</td><td align=center>Tiempo atención</td><td>Historia</td><td>Ingreso</td><td align=center>Paciente</td><td align=center>Edad(Años)</td><td align=center>Especialidad solicitante</td><td align=center>Especialista que solicita</td></tr>";
	
	$i = 1;
	
	foreach($datos_array_esp_tiempo as $key => $value){
		
		$datos_paciente = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		
		if($value['FechaHora28'] != ''){
		
			$datos_paciente = traer_datos_paciente($value['Ordhis'],$value['Ording'] );			
			$tiempo_transcurrido = calcula_tiempo($value['FechaHora28'], $value['FechaHora36']);
			
			$html_detalle_pacientes.= "<tr $class>";
			$html_detalle_pacientes.= "<td align=center>".$i."</td>";
			$html_detalle_pacientes.= "<td bgcolor='#3CB648'>".$value['FechaHora28']."</td>";
			$html_detalle_pacientes.= "<td>".$value['FechaHora36']."</td>";
			$html_detalle_pacientes.= "<td>".$tiempo_transcurrido."</td>";
			$html_detalle_pacientes.= "<td>".$value['Ordhis']."</td>";
			$html_detalle_pacientes.= "<td align=center>".$value['Ording']."</td>";
			$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
			$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
			$html_detalle_pacientes.= "<td align=left>".$value['Espnom']."</td>";
			$html_detalle_pacientes.= "<td align=left>".utf8_encode($value['Descripcion'])."</td>";
			$html_detalle_pacientes.= "</tr>";
		
		}else{
			
			$datos_paciente = traer_datos_paciente($value['Firhis'],$value['Firing'] );
			
			$html_detalle_pacientes.= "<tr $class>";
			$html_detalle_pacientes.= "<td align=center>".$i."</td>";
			$html_detalle_pacientes.= "<td></td>";
			$html_detalle_pacientes.= "<td>".$value['Fecha36']." ".$value['Hora36']."</td>";
			$html_detalle_pacientes.= "<td></td>";
			$html_detalle_pacientes.= "<td>".$value['Firhis']."</td>";
			$html_detalle_pacientes.= "<td align=center>".$value['Firing']."</td>";
			$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
			$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
			$html_detalle_pacientes.= "<td align=center></td>";
			$html_detalle_pacientes.= "<td align=center></td>";
			$html_detalle_pacientes.= "</tr>";
			
		}
		
		$i++;
		$nombre_especialidad = traer_datos_usuario($key_medico);
		$datamensaje['nombre_especialista'] = utf8_encode($nombre_especialidad['Descripcion']);
		
	}
		
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	$datamensaje['datos_detalle_atendidos_tiempo'] = $html_detalle_pacientes;
	
	
	echo json_encode($datamensaje);	
	return;
	
}


if(isset($operacion) && $operacion == 'ver_detalle_interconsulta'){
	
	$array_inter_especialidad = unserialize(base64_decode($datos_inter_especialidad));
	$nombre_especialidad = unserialize(base64_decode($nombre_especialidad));
	$datos_usuario = traer_datos_usuario($value['Firusu']);
		
	asort($array_inter_especialidad); //Se ordena por el arreglo por la fecha.
	
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_especialidad' >";
	$html_detalle_pacientes .= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";
	$html_detalle_pacientes.= "<div align=center><b>".count($array_inter_especialidad)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 class='table-bordered' width='100%' id='detalle_esp_solicita_interconsulta'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td><td>Historia</td><td>Ingreso</td><td align=center>Paciente</td><td align=center>Edad(Años)</td><td align=center>Interconsulta solicitada</td><td align=center>Especialista que solicita.</td></tr>";
	
	$i = 1;
	
	foreach($array_inter_especialidad as $key => $value){
		
		$datos_paciente = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		$datos_paciente = traer_datos_paciente($value['Ordhis'],$value['Ording'] );
		
		$html_detalle_pacientes.= "<tr $class>";
		$html_detalle_pacientes.= "<td align=center>".$i."</td>";
		$html_detalle_pacientes.= "<td>".$value['Ordhis']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Ording']."</td>";
		$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
		$html_detalle_pacientes.= "<td align=left>".$value['DesInter']."</td>";
		$html_detalle_pacientes.= "<td align=left>".utf8_encode($value['Descripcion'])."</td>";	
		$html_detalle_pacientes.= "</tr>";		
		
		$datamensaje['nombre_especialista'] = $datos_usuario['Descripcion'];
		
		$i++;
	}
		
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	
	$datamensaje['nombre_especialidad'] = $nombre_especialidad;
	$datamensaje['datos_detalle_inter_espec'] = $html_detalle_pacientes;
	
	
	echo json_encode($datamensaje);	
	return;
	
}


if(isset($operacion) && $operacion == 'ver_detalle_sin_atender'){
	
	$array_pacientes_sin_atender = unserialize(base64_decode($datos_sin_atender));
	$nom_centro_costos = unserialize(base64_decode($nom_centro_costos));
		
	asort($array_pacientes_sin_atender); //Se ordena por el arreglo por la fecha.
	
	//print_r($array_pacientes_sin_atender);
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_especialidad' >";
	$html_detalle_pacientes .= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";
	$html_detalle_pacientes.= "<div align=center><b>".count($array_pacientes_sin_atender)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 class='table-bordered' width='90%' id='detalle_pacientes_sin_atender'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td><td>Historia</td><td>Ingreso</td><td align=center>Paciente</td><td align=center>Edad(Años)</td><td align=center>Habitación</td><td align=center>Fecha Hora Orden</td><td align=center>Fecha Hora Alta</td><td align=center>Fecha Hora Muerte</td><td align=center>Especialista</td><td align=center>Especialidad</td></tr>";
	
	$i = 1;
	
	foreach($array_pacientes_sin_atender as $key => $value){
		
		$datos_paciente = "";
		$muerte = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		$datos_paciente = traer_datos_paciente($value['Ordhis'],$value['Ording'] );
		$datos_usuario = traer_datos_usuario($value['Detusu']);
		
		$html_detalle_pacientes.= "<tr $class>";
		$html_detalle_pacientes.= "<td align=center>".$i."</td>";
		$html_detalle_pacientes.= "<td>".$value['Ordhis']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Ording']."</td>";
		$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
		
		$ubicacion_pac = $value['Ubihac'];
		
		//Si el paciente no tiene registro en la ubicacion actual, mostrara la ubicacion anterior.
		if($ubicacion_pac == ''){
			$ubicacion_pac = $value['Ubihan'];
		}
		
		$html_detalle_pacientes.= "<td align=center>".$ubicacion_pac."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['FechaHora28']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Ubifad']." ".$value['Ubihad']."</td>";
		
		if($value['Ubimue'] == 'on'){
			
			$muerte = $value['Ubifad']." ".$value['Ubihad'];
		}
		
		$html_detalle_pacientes.= "<td align=center>".$muerte."</td>";
		$html_detalle_pacientes.= "<td align=left>".utf8_encode($datos_usuario['Descripcion'])."</td>";
		$html_detalle_pacientes.= "<td align=left>".$value['Espnom']."</td>";
		$html_detalle_pacientes.= "</tr>";
		
		$i++;
		
	}
		
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	//$datamensaje['datos_generales'] = $datos_generales;
	$datamensaje['des_centro_costos'] = $nom_centro_costos;
	$datamensaje['datos_detalle_sin_atender'] = $html_detalle_pacientes;
	
	
	echo json_encode($datamensaje);	
	return;
	
}


if(isset($operacion) && $operacion == 'ver_detalle_especialista'){
	
	$array_pacientes_esp = unserialize(base64_decode($datos_paciente_especialista));
		
	asort($array_pacientes_esp); //Se ordena por el arreglo por la fecha.
	
	if($tipo == 'todos'){		
		$titulo_fecha = "<td align=center>Fecha</td>";	
	}
	
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_especialidad' >";
	$html_detalle_pacientes .= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";
	$html_detalle_pacientes.= "<div align=center><b>".count($array_pacientes_esp)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 class='table-bordered' width='90%' id='tabla_detalle_especialistas'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td>".$titulo_fecha."<td align=center>Historia</td><td align=center>Ingreso</td><td align=center>Paciente</td><td align=center>Edad(Años)</td></tr>";
	
	$i = 1;
	
	foreach($array_pacientes_esp as $key => $value){
		
		$datos_paciente = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		$datos_paciente = traer_datos_paciente($value['Firhis'],$value['Firing'] );
		$datos_usuario = traer_datos_usuario($value['Firusu']);
		
		$html_detalle_pacientes.= "<tr $class>";
		
		if($tipo == 'todos'){		
			$html_detalle_pacientes.= "<td align=center>".$i."</td><td>".$value['Fecha36']."</td>";
		}else{
			$html_detalle_pacientes.= "<td align=center>".$i."</td>";
		}
		
		$html_detalle_pacientes.= "<td>".$value['Firhis']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Firing']."</td>";
		$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
		$html_detalle_pacientes.= "</tr>";
		
		$datamensaje['nombre_especialista'] = utf8_encode($datos_usuario['Descripcion']);
		
		$i++;
		
	}
		
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	//$datamensaje['datos_generales'] = $datos_generales;
	$datamensaje['datos_detalle_esp'] = $html_detalle_pacientes;
	
	
	echo json_encode($datamensaje);	
	return;
	
}


if(isset($operacion) && $operacion == 'ver_detalle_hce'){
	
	$array_pacientes_hce = unserialize(base64_decode($datos_paciente_hce));
	$titulo = unserialize(base64_decode($titulo));
		
	asort($array_pacientes_hce); //Se ordena por el arreglo por la fecha.
	
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_entidad' >";
	$html_detalle_pacientes .= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";
	$html_detalle_pacientes.= "<div align=center><b>".count($array_pacientes_hce)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 id='tabla_registros_hce' class='table-bordered' width='100%'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td><td align=center>Historia</td><td align=center>Ingreso</td><td align=center>Paciente</td><td align=center>Edad(Años)</td><td align=center>Especialista</td></tr>";
		
	$i = 1;	
		
	foreach($array_pacientes_hce as $key => $value){
		
		$datos_paciente = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		$datos_paciente = traer_datos_paciente($value['Firhis'],$value['Firing'] );
		$datos_usuario = traer_datos_usuario($value['Firusu']);
		
		$html_detalle_pacientes.= "<tr $class>";
		$html_detalle_pacientes.= "<td align=center>".$i."</td>";
		$html_detalle_pacientes.= "<td>".$value['Firhis']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Firing']."</td>";
		$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
		$html_detalle_pacientes.= "<td align=left>".utf8_encode($datos_usuario['Descripcion'])."</td>";
		$html_detalle_pacientes.= "</tr>";
		
		$i++;
		
	}
	
		
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	//$datamensaje['datos_generales'] = $datos_generales;
	$datamensaje['titulo_conf'] = $titulo;
	$datamensaje['datos_detalle_hce'] = $html_detalle_pacientes;
	
	echo json_encode($datamensaje);	
	return;
	
}

if(isset($operacion) && $operacion == 'ver_detalle_cco'){
	
	
	$array_datos_cco = unserialize(base64_decode($datos_centro_costos));
	$nombre_cco = unserialize(base64_decode($nombre_cco));
	
	asort($array_datos_cco); //Se ordena por el arreglo por la fecha.
	
	if($tipo == 'todos'){		
		$titulo_fecha = "<td align=center>Fecha</td>";	
	}
	
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_entidad' >";
	$html_detalle_pacientes .= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";
	$html_detalle_pacientes.= "<div align=center><b>".count($array_datos_cco)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 class='table-bordered' width='100%' id='tabla_detalle_centro_costos'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td>".$titulo_fecha."<td>Historia</td><td>Ingreso</td><td align=center>Paciente</td><td>Edad(Años)</td><td align=center>Especialista</td></tr>";
		
	$i = 1;
		
	foreach($array_datos_cco as $key => $value){
		
		$datos_paciente = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		$datos_paciente = traer_datos_paciente($value['Firhis'],$value['Firing'] );
		$datos_usuario = traer_datos_usuario($value['Firusu']);
		
		$html_detalle_pacientes.= "<tr $class>";
		
		if($tipo == 'todos'){	
			$html_detalle_pacientes.= "<td align=center>".$i."</td><td>".$value['Fecha36']."</td>";
		}else{
			$html_detalle_pacientes.= "<td align=center>".$i."</td>";
		}
				
		$html_detalle_pacientes.= "<td>".$value['Firhis']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Firing']."</td>";
		$html_detalle_pacientes.= "<td>".$datos_paciente['nombre_paciente']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
		$html_detalle_pacientes.= "<td align=center>".utf8_encode($datos_usuario['Descripcion'])."</td>";
		$html_detalle_pacientes.= "</tr>";
		
		$i++;
		
	}
	
		
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	//$datamensaje['datos_generales'] = $datos_generales;
	$datamensaje['descripcion_cco'] = $nombre_cco;
	$datamensaje['datos_detalle_cco'] = $html_detalle_pacientes;
	
	echo json_encode($datamensaje);	
	return;
}


if(isset($operacion) && $operacion == 'ver_detalle_entidad'){
	
	
	$array_por_entidades = unserialize(base64_decode($datos_por_entidades));
	$nombre_entidad = unserialize(base64_decode($entidad));
	
	asort($array_por_entidades); //Se ordena por el arreglo por la fecha.
	
	if($tipo == 'todos'){		
		$titulo_fecha = "<td align=center>Fecha</td>";	
	}
	
	$html_detalle_pacientes.= "<div id='ver_datos_detalle_entidad' >";
	$html_detalle_pacientes .= "<div align=center><br><b>Buscar:</b><input id='buscador' class='bordeRed' placeholder='Buscar' type='text'></div><br>";
	$html_detalle_pacientes.= "<div align=center><b>".count($array_por_entidades)."</b> pacientes.</div>";
	$html_detalle_pacientes.= "<center><table border=0 id='tabla_detalle_entidades' class='table-bordered' width='100%'>";
	$html_detalle_pacientes.= "<tr class='encabezadoTabla'><td>Conteo</td>".$titulo_fecha."<td align=center>Historia</td><td align=center>Ingreso</td><td align=center>Paciente</td><td align=center>Edad(Años)</td><td align=center>Especialista</td></tr>";
	
	$i = 1;
	
	foreach($array_por_entidades as $key => $value){
		
		$datos_paciente = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		
		$datos_paciente = traer_datos_paciente($value['Firhis'],$value['Firing'] );
		$datos_usuario = traer_datos_usuario($value['Firusu']);
		
		$html_detalle_pacientes.= "<tr $class>";
		
		if($tipo == 'todos'){		
			$html_detalle_pacientes.= "<td align=center>".$i."</td><td>".$value['Fecha36']."</td>";	
		}else{
			$html_detalle_pacientes.= "<td align=center>".$i."</td>";
		}
		
		$html_detalle_pacientes.= "<td>".$value['Firhis']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$value['Firing']."</td>";
		$html_detalle_pacientes.= "<td align=left>".$datos_paciente['nombre_paciente']."</td>";
		$html_detalle_pacientes.= "<td align=center>".$datos_paciente['edad']."</td>";
		$html_detalle_pacientes.= "<td align=left>".utf8_encode($datos_usuario['Descripcion'])."</td>";
		$html_detalle_pacientes.= "</tr>";
		
		$i++;
	}
	
	//$datos_generales.= "<br><div align=center style='cursor:pointer;' onclick='ver_informacion(\"html_detalle_entidad\")'> <b>".count($array_entidades)."</b> entidades</div>";
	
	$html_detalle_pacientes.= "</table></center>";
	$html_detalle_pacientes.= "</div>";
	
	$datamensaje['nombre_entidad'] = $nombre_entidad;
	$datamensaje['datos_detalle_entidad'] = $html_detalle_pacientes;
	
	echo json_encode($datamensaje);	
	return;
}

if(isset($operacion) && $operacion == 'generar_reporte'){
		
	$datamensaje = array('mensaje'=>'', 'error'=>0 );	
		
	$cod_tipo_orden_interconsulta = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CodInterconsultas');
	$tipoempresaparticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresaparticular');
	
	//Buscamos los datos de configuracion basados en la especialidad asociada y la especialidad.
	$query_datos = "SELECT *
					  FROM ".$wbasedato."_000240
				     WHERE id = '".$id_reporte."'
					   AND Repest = 'on'";
	$res_datos = mysql_query($query_datos, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_datos." - ".mysql_error());
	$row_datos = mysql_fetch_array($res_datos);
	
	//Consecutivos asociados al formulario para generar reportes con campos tipo seleccion.
	$especialidad = $row_datos['Repesp'];
	$wformulario = $row_datos['Repfor'];
	$campos_formulario = $row_datos['Repcon'];
	$desc_cups = $row_datos['Repcup'];		
	$cod_tipo_orden_interconsulta = $row_datos['Reptio'];		
	$form_oportunidad_atencion = trim($row_datos['Repfoa']);
	
	$array_form_oportunidad_atencion = explode(",",$form_oportunidad_atencion); //Formularios para tiempos de atencion oportunidad en la atencion	.
	$form_oportunidad_atencion = implode("','",$array_form_oportunidad_atencion);	
	
	$nombre_formularios.= "Información basada en los siguientes formularios HCE:<br>";
	foreach($array_form_oportunidad_atencion as $key => $value){
		
		$sql = " SELECT Encdes
				   FROM ".$whce."_000001
				  WHERE Encpro = '".$value."'";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(Form):</b><br>".mysql_error());
		$row = mysql_fetch_array($res);
		
		$nombre_formularios .= "<b>Formulario ".utf8_encode($row['Encdes'])."</b><br>";
		
	}
	
	$array_cups = array();
	$array_cups_datos = array();
	
	if(trim($desc_cups) != '' and trim($cod_tipo_orden_interconsulta) != ''){
	
		$sqlCups = " SELECT Codigo, Descripcion
					   FROM ".$whce."_000047
					  WHERE Tipoestudio = '".$cod_tipo_orden_interconsulta."'
						AND Descripcion LIKE '%".$desc_cups."%'";
		$resCups = mysql_query($sqlCups, $conex) or die("<b>ERROR EN QUERY MATRIX(Cups):</b><br>".mysql_error());
		
		while($rowCups = mysql_fetch_array($resCups)){
		
			$array_cups[$rowCups['Codigo']] = $rowCups['Codigo'];
			$array_cups_datos[$rowCups['Codigo']] = $rowCups;
			
		}
			
		$arreglo_cups = implode("','",$array_cups);	
	
	}
	
	//Consulta para saber que especialidades han solicitado interconsultas para la especialidad que esta generando el reporte. 	
	$query_ord = " SELECT CONCAT(".$whce."_000028.Fecha_Data,' ',".$whce."_000028.Hora_Data) as FechaHora28,Ordhis,Ording,Detusu,Ubisac,Ubihac,Ubihan, Detcod, ".$whce."_000047.Descripcion as DesInter, Ubifad, Ubihad, Ubimue
					 FROM ".$whce."_000027, ".$whce."_000028, ".$whce."_000047, ".$wbasedato."_000018
					WHERE Ordest = 'on' 
					  AND Ordtor = Dettor 
					  AND Ordnro = Detnro 
					  AND Detest = 'on'
					  AND Detesi != 'C'
					  AND Ordhis = Ubihis
					  AND Ording = Ubiing
					  AND Detcod = Codigo
					  AND Detcod in ('".$arreglo_cups."')  
					  AND ".$whce."_000027.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'
			     GROUP BY Ordhis, Ording";
	$res_ord = mysql_query($query_ord, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_ord." - ".mysql_error());
	
	$array_especilidades_interc = array();
	$array_ordenes = array();
	$array_ordenes_interconsulta = array();
	$array_ordenes_interconsulta_por_esplista = array();
	
	if($desc_cups != ''){
		
		while($row_ord = mysql_fetch_assoc($res_ord)){			
			
			$consultar_especialidad = consultar_especialidad($row_ord['Detusu']);
			$row_ord['Espnom'] = $consultar_especialidad['Espnom'];		
			$row_ord['Descripcion'] = $consultar_especialidad['Descripcion'];		
			$array_especilidades_interc[$consultar_especialidad['Espnom']][] = $row_ord;
			$array_ordenes_interconsulta[$row_ord['Ordhis']."-".$row_ord['Ording']] = $row_ord;
			$array_ordenes_interconsulta_por_esplista[$row_ord['Ordhis']."-".$row_ord['Ording']] = $row_ord;
			$array_cups_conteo[$row_ord['Detcod']][] = $row_ord['Detcod'];
			
		}
		
	}
	
	if($array_cups_datos > 0){
		$des_cups .= "<font size=3><b>INTERCONSULTAS SOLICITADAS</b></font><br>";	
		foreach($array_cups_datos as $keycups => $valuecups){
			
			$cuantos_cups = $array_cups_conteo[$valuecups['Codigo']];
			$des_cups .= "<font size=3>(Cups:".$keycups.") ".ucwords(strtolower($valuecups['Descripcion']))." <b>(".count($cuantos_cups).")</b></font><br>";
			$des_cups_ordenados .= "<font size=3>(Cups:".$keycups.") ".ucwords(strtolower($valuecups['Descripcion']))."</font><br>";	
		}
	}else{
		
		$mensaje_error = "<br>No es posible consultar interconsultas ordenadas porque la palabra clave ".$desc_cups." con tipo de orden ".$cod_tipo_orden_interconsulta." no tiene resultados.";
	}
	
	//Si el hay formularios de oportunidad de atencion hara las consultas.
	if(count($form_oportunidad_atencion) > 0){
	
		//Pacientes con registro de evolucion especialidad desde la fecha inicial en adelante.
		$query_atend = "  SELECT CONCAT(A.Fecha_data,' ',A.Hora_data) AS FechaHora36, Firhis, Firing, Firusu
							FROM ".$whce."_000036 as A
						   WHERE Firpro in ('".$form_oportunidad_atencion."')					     
							 AND Firfir = 'on'
							 AND Firrol = '".$especialidad."'
							 AND A.Fecha_data >= '".$fecha_inicial."'";
		$res_atend = mysql_query($query_atend, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_atend." - ".mysql_error());
			
		$array_pacientes_atendidos = array();
		$array_pacientes_atendidos_por_especialista = array();
		
		while($row_aten = mysql_fetch_array($res_atend)){
			
			$key_his_ing = $row_aten['Firhis']."-".$row_aten['Firing'];
			$array_pacientes_atendidos[$key_his_ing] = $row_aten;
			
			if(!array_key_exists($key_his_ing,$array_pacientes_atendidos_por_especialista)){
				
				$array_pacientes_atendidos_por_especialista[$key_his_ing] = $row_aten;
				
			}		
		}
		
		//Array que compara los pacientes con interconsultas solicitadas con respecto a los que tienen registros de evolucion.
		$array_pacientes_sin_atender = array();
		
		foreach($array_ordenes_interconsulta as $key => $val1) {
			
			if(count($array_pacientes_atendidos[$key]) == 0) {
				
				   $array_pacientes_sin_atender[$val1['Ubisac']][$key] = $val1;
			}
			
		}
		
		$array_pacientes_orden_evolucion = array();
		
		foreach($array_ordenes_interconsulta_por_esplista as $key1 => $value1 ){		
			
			if(count($array_pacientes_atendidos_por_especialista[$key1]) > 0){
				
				$value1['FechaHora36'] = $array_pacientes_atendidos_por_especialista[$key1]['FechaHora36'];
				
				$total_seconds = strtotime($value1['FechaHora36']) - strtotime($value1['FechaHora28']); 
				
				//Si la fecha y hora de la orden es mayor a la fecha y hora de la evolucion lo agrega.
				if($total_seconds > 0){
				
					$array_pacientes_orden_evolucion[$array_pacientes_atendidos_por_especialista[$key1]['Firusu']][$key1] = $value1;
				
				}
				
			}
			
		}
			
		//Analisis de tiempo transcurrido entre la solicitud de la orden y el registro de evolucion en la historia clinica	
		
		foreach($array_pacientes_orden_evolucion as $key_oe => $array_especialistas){
			
			$resultado = 0;
			$conteo_espe = 0;
			
			foreach($array_especialistas as $key_hi => $value_hii){		
				
				$resultado += strtotime($value_hii['FechaHora36']) - strtotime($value_hii['FechaHora28']);
				$array_esp_tiempo[$key_oe][$key_hi] = $value_hii;
				
				if($resultado > 0){
					
					$conteo_espe++;
				}
			
			}
			
			$resultado = $resultado / $conteo_espe;
			$array_esp_tiempo[$key_oe]['tiempo_promedio'] = $resultado;
			
		}
		
		//Busco los alumnos asociados a una especialidad
		$query =     " SELECT Usualu
						 FROM ".$whce."_000020, ".$wbasedato."_000048
						WHERE Meduma = Usucod
						  AND Usualu != '' 
						  AND Medesp = '".$especialidad."'";
		$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());		
		
		while($row = mysql_fetch_assoc($res)){
			
			//Uno todos loa alumnos	que tenga la respuesta de la consulta por especialidad.
			$todos_los_estudiantes .= $row['Usualu'].",";
		}
		
		$array_alumnos_esp = array();
		//Separo los alumnos por "," para crear un arreglo.
		$datos_alumnos_esp = explode(",",$todos_los_estudiantes);
		
		// echo "<pre>";
		// print_r($datos_alumnos_esp);
		// echo "</pre>";
		
		foreach($datos_alumnos_esp as $key => $value){
			
			if(!array_key_exists($value, $array_alumnos_esp) and $value != ''){
				
				$array_alumnos_esp[$value] = $value;
			}
		}
		
		// echo "<pre>";
		// print_r($array_alumnos_esp);
		// echo "</pre>";
		
		$array_final_estudiantes_esp = implode("','",$array_alumnos_esp);
		
		//Consulta para saber que entidades, centros de costo y especialistas tiene asociado este formulario.	
		$query =     " SELECT * FROM(
						SELECT *, ".$whce."_000036.Fecha_data as Fecha36, ".$whce."_000036.Hora_data as Hora36, '' as estudiante
						 FROM ".$whce."_000036
						WHERE Firpro in ('".$form_oportunidad_atencion."')
						  AND Firfir = 'on'
						  AND Firrol = '".$especialidad."'	  
						  AND ".$whce."_000036.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'
						  UNION
						SELECT *, ".$whce."_000036.Fecha_data as Fecha36, ".$whce."_000036.Hora_data as Hora36, 'on' as estudiante
						 FROM ".$whce."_000036
						WHERE Firusu in ('".$array_final_estudiantes_esp."')
						  AND Firpro in ('".$form_oportunidad_atencion."')
						  AND Firfir = 'on'  
						  AND ".$whce."_000036.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$fecha_final."') as t
					 ORDER BY Fecha36";
		$res= mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		
		$array_datos = array();
		$array_datos_entidad = array();
		$array_datos_cco = array();
		$array_datos_especialista = array();
			
		while($row = mysql_fetch_assoc($res)){
			
			$key_todos = $row['Firhis']."-".$row['Firing']."-".$row['Fecha36']."-".$row['Hora36'];
			$array_datos[$key_todos] = $row;
			
			$key_his_ing = $row['Firhis']."-".$row['Firing'];
			$array_datos_historia[$key_his_ing] = $row['Firhis']."-".$row['Firing'];
			
			//Entidad
			$query_ent = " SELECT Ingnre, Inghis, Inging, Ingres, Ingtip
							 FROM ".$wbasedato."_000016
							WHERE Inghis = '".$row['Firhis']."'
							  AND Inging = '".$row['Firing']."'
						 ORDER BY Ingnre ";
			$res_ent = mysql_query($query_ent, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_ent." - ".mysql_error());
			$row_ent = mysql_fetch_array($res_ent);
			
			//echo $query_ent." ".$row_ent['Ingnre']."<br>";
			if($row_ent['Ingtip'] == $tipoempresaparticular){
				
				$row_ent['Ingres'] = $tipoempresaparticular;
				
			}
			
			if($row_ent['Ingres'] == ''){
				$row_ent['Ingres'] = $row['Firpro'];
			}
			
			$array_datos_entidad[$row_ent['Ingres']][$row['Firhis']."-".$row['Firing']."-".$row['Firusu']] = $row;	
			$array_datos_entidad_todos[$row_ent['Ingres']][$key_todos] = $row;	
						
			$array_datos_cco[$row['Fircco']][$key_his_ing] = $row;
			$array_datos_cco_todos[$row['Fircco']][$key_todos] = $row;
			
			//Especialista
			$array_datos_especialista[$row['Firusu']][$key_his_ing] = $row;
			$array_datos_especialista_total[$row['Firusu']][$key_todos] = $row;
			
		}
		
		
		 foreach($array_datos_especialista as $cod_especialista => $array_datos_his_ing){
			
			foreach($array_datos_his_ing as $key1 => $value_his_ing){
				
				if( isset( $array_esp_tiempo[$cod_especialista] ) && !array_key_exists( $key1, $array_esp_tiempo[$cod_especialista] ) ){
					
					$array_esp_tiempo[$cod_especialista][$key1] = $value_his_ing;
					
				}
					
			}
			
		 }
	 
	}
	//*************** Creacion de las tablas con los datos para el reporte segun el arreglo **********************
	
	//======= Entidad (Responsable) =================	
	$html_entidades .= "<br><table width='50%' class='table-bordered' id='tabla_entidades'>";
	$html_entidades .= "<tr class=encabezadoTabla><td align=center><img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico(\"tabla_entidades\",\"Entidades\")' src='../../images/medical/root/chart.png'> Entidad</td><td align=center>Pacientes</td><td align=center>Registros HCE</td></tr>";
	
	$total_cuantos_entidad = 0;
	
	arsort($array_datos_entidad);
	
	
	foreach($array_datos_entidad as $key_enti => $value_ent){
		
		$class = "class='fila".(($i%2)+1)."'";
	
		$cuantos_entidad = count($array_datos_entidad_formulario[$key_enti]);
		$total_cuantos_entidad = $total_cuantos_entidad + $cuantos_entidad;		
		
		$value_entidades_datos = base64_encode(serialize($value_ent));
		$value_entidades_datos_todos = base64_encode(serialize($array_datos_entidad_todos[$key_enti]));
		$total_entidad = count($array_datos_entidad_todos[$key_enti]);
		$total_ent_final = $total_ent_final + $total_entidad;
		
		$nombre_entidad = consultar_entidad($key_enti);
		$nombre_entidad_texto = consultar_entidad($key_enti); 
		$nombre_entidad_texto = base64_encode(serialize($nombre_entidad_texto));
		
		if($key_enti == $tipoempresaparticular){
			$nombre_entidad = "PARTICULAR";
		}

		//Si no hay entidad buscara el nombre del formulario hce.
		if($nombre_entidad == ''){
			
			$query_ent = " SELECT Encdes
							 FROM ".$whce."_000001
							WHERE Encpro = '".$key_enti."'";
			$res_ent = mysql_query($query_ent, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_ent." - ".mysql_error());
			$row_ent = mysql_fetch_array($res_ent);
			$nombre_entidad = "* ".$row_ent['Encdes'];
			
		}			
		// var_dump($nombre_entidad);
		$html_entidades .= "<tr $class>";
		$html_entidades .= "<td>".strtoupper(utf8_encode($nombre_entidad))."</td><td align=center style='cursor:pointer;' onclick='ver_detalle_entidad(\"$value_entidades_datos\",\"".utf8_encode($nombre_entidad_texto)."\", \"pacientes\")'>".count($value_ent)."</td><td style='cursor:pointer;' align=center onclick='ver_detalle_entidad(\"$value_entidades_datos_todos\",\"".utf8_encode($nombre_entidad)."\", \"todos\")'>".$total_entidad."</td>";
		$html_entidades .= "</tr>";
		
		$suma_pac_ent = $suma_pac_ent + count($value_ent);
		$i++;
		
	}
	
	$html_entidades .= "<tr class=encabezadoTabla><td>Total</td><td align=center>".$suma_pac_ent."</td><td align=center>".$total_ent_final."</td></tr>";
	$html_entidades .= "</table>";
	$html_entidades .= "* <font size=2>Pacientes atendidos por preanestesia sin ingreso.</font>";
	
	$html_entidades .= "<center>
							<div id='contenedor_graf_tabla_entidades' style=' display:none;' align='center'>
							<div id='div_grafica_tabla_entidades' style='border: 1px solid #999999; width:700px; height:400px;'>
							</div>
							</div>
						</center>";
	
	
	if($suma_pac_ent == 0){
		
		$html_entidades = "No hay estadistica de entidades en esta fecha.";
		
	}
	
	//======= Centro de costos =================	
	$html_centros_costo .= "<br><table width='40%' class='table-bordered' id='tabla_centro_costos'>";
	$html_centros_costo .= "<tr class=encabezadoTabla><td align=center><img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico(\"tabla_centro_costos\",\"Centros de costo\")' src='../../images/medical/root/chart.png'>Centro de costos</td><td align=center>Pacientes</td><td align=center>Registros HCE</td></tr>";
	arsort($array_datos_cco);
	
	foreach($array_datos_cco as $key_cco => $value_cco){
		
		$total_cco = "";
		$class = "class='fila".(($i%2)+1)." findDatos'";
		
		$query_cco = " SELECT Cconom
					     FROM ".$wbasedato."_000011
				        WHERE Ccocod = '".$key_cco."'";
		$res_cco = mysql_query($query_cco, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_cco." - ".mysql_error());
		$row_cco = mysql_fetch_array($res_cco);
		$nombre_cco = utf8_encode($row_cco['Cconom']);
		
		//Si no hay centro de costos buscara el nombre del formulario.
		if($nombre_cco == ''){
			
			$query_cco = " SELECT Encdes
							 FROM ".$whce."_000001
							WHERE Encpro = '".$key_cco."'";
			$res_cco = mysql_query($query_cco, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_cco." - ".mysql_error());
			$row_cco = mysql_fetch_array($res_cco);
			$nombre_cco = "* ".utf8_encode($row_cco['Encdes']);
			
		}		
		
		$nombre_centro_costos = base64_encode(serialize($nombre_cco));
		
		$value_cco_datos = base64_encode(serialize($value_cco));
		$value_cco_datos_todos = base64_encode(serialize($array_datos_cco_todos[$key_cco]));
		$total_cco = count($array_datos_cco_todos[$key_cco]);
		$total_cco_final = $total_cco_final + $total_cco;
		
		$html_centros_costo .= "<tr $class>";
		$html_centros_costo .= "<td>".strtoupper($nombre_cco)."</td><td align=center style='cursor:pointer;' onclick='ver_detalle_cco(\"$value_cco_datos\",\"".$nombre_centro_costos."\", \"pacientes\")'>".count($value_cco)."</td><td align=center style='cursor:pointer;' onclick='ver_detalle_cco(\"$value_cco_datos_todos\",\"".$nombre_cco."\", \"todos\")'>".$total_cco."</td>";
		$html_centros_costo .= "</tr>";
		
		$suma_pac_cco = $suma_pac_cco + count($value_cco);
		$i++;
		
	}
	
	$html_centros_costo .= "<tr class=encabezadoTabla><td>Total</td><td align=center>".$suma_pac_cco."</td><td align=center>".$total_cco_final."</td></tr>";
	$html_centros_costo .= "</table>";
	$html_centros_costo .= "* <font size=2>Pacientes atendidos por preanestesia sin ingreso.</font>";
	$html_centros_costo .= "<center>
								<div id='contenedor_graf_tabla_centro_costos' style=' display:none;' align='center'>
								<div id='div_grafica_tabla_centro_costos' style='border: 1px solid #999999; width:700px; height:400px;'>
								</div>
								</div>
							</center>";
	
	if($suma_pac_cco == 0){
		
		$html_centros_costo = "No hay estadistica de centros de costos en esta fecha.";
		
	}
	
	
	//======= Especialista =================
	
	$html_especialista .= "<br><table width='40%' class='table-bordered' id='tabla_especialistas'>";
	$html_especialista .= "<tr class=encabezadoTabla><td align=center> <img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico(\"tabla_especialistas\",\"Especialistas\")' src='../../images/medical/root/chart.png'>Especialista</td><td align=center>Pacientes</td><td align=center>Registros HCE</td><td align=center class='msg' title='Tiempo de atención desde la orden <br>médica hasta la primera evolución, <br>con formato Horas:Minutos.'>Tiempo Promedio (HH:mm)</td></tr>";

	arsort($array_datos_especialista);
	
	$i = 0;
	
	foreach($array_datos_especialista as $key_esp => $value_esp){
				
		$class = "class='fila".(($i%2)+1)." findDatos'";
		$style_js = "";
		
		$query_esp = " SELECT Descripcion
					     FROM usuarios
				        WHERE Codigo = '".$key_esp."'";
		$res_esp = mysql_query($query_esp, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_esp." - ".mysql_error());
		$row_esp = mysql_fetch_array($res_esp);
		
		$value_esp_datos = base64_encode(serialize($value_esp));
		$value_esp_datos_total = base64_encode(serialize($array_datos_especialista_total[$key_esp]));
		
		$tiempo_promedio = $array_esp_tiempo[$key_esp]['tiempo_promedio'];
		$tiempo_promedio = floor($tiempo_promedio/3600).gmdate(':i', $tiempo_promedio );
		$tiempo_promedio_final = $tiempo_promedio_final + $array_esp_tiempo[$key_esp]['tiempo_promedio'];
		
		$total_esp = count($array_datos_especialista_total[$key_esp]);
		$total_esp_final = $total_esp_final + $total_esp;
		
		$array_esp_tiempo_datos = base64_encode(serialize($array_esp_tiempo[$key_esp]));
		if(count($array_esp_tiempo[$key_esp]) > 0){
		
			$style_js = "style='cursor:pointer' onclick='ver_detalle_atendidos_tiempo(\"".$array_esp_tiempo_datos."\", \"".$key_esp."\")'";
		}
		
		$html_especialista .= "<tr $class>";
		$html_especialista .= "<td>".strtoupper(utf8_encode($row_esp['Descripcion']))."</td><td align=center style='cursor:pointer;' onclick='ver_detalle_especialista(\"".$value_esp_datos."\", \"pacientes\")'>".count($value_esp)."</td><td align=center style='cursor:pointer;' onclick='ver_detalle_especialista(\"".$value_esp_datos_total."\", \"todos\")'>".$total_esp."</td><td align=center $style_js >".$tiempo_promedio."</td>";
		$html_especialista .= "</tr>";
		
		$suma_pac_esp = $suma_pac_esp + count($value_esp);
		$i++;
		
	}
	
	@$t = $tiempo_promedio_final/$i;
	@$t1 = floor($t/3600).gmdate(':i', $t );
	
	$html_especialista .= "<tr class=encabezadoTabla><td>Total</td><td align=center>".$suma_pac_esp."</td><td align=center>".$total_esp_final."</td><td align=center>".$t1."</td></tr>";
	$html_especialista .= "</table>";
	
	$html_especialista .= "<center>
										<div id='contenedor_graf_tabla_especialistas' style=' display:none;' align='center'>
										<div id='div_grafica_tabla_especialistas' style='border: 1px solid #999999; width:700px; height:400px;'>
										</div>
										</div>
									</center>";
	
	if($suma_pac_esp == 0){
		
		$html_especialista = "No hay estadistica de especialistas en esta fecha.";
		
	}
	
	
	//======= Sin atender =================
	
	if(count($array_pacientes_sin_atender) > 0){
	
		$html_pacientes_sin_atender_text = "<div><b>Pacientes con las siguientes ordenes de interconsulta y que no tienen evolución hasta hoy:</b></div>";
		$html_pacientes_sin_atender .= "<br><table width='40%' class='table-bordered' id='pacientes_sin_atender'>";
		$html_pacientes_sin_atender .= "<tr class=encabezadoTabla><td align=center><img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico(\"pacientes_sin_atender\",\"Pacientes sin atender\")' src='../../images/medical/root/chart.png'>Especialista</td><td align=center>Pacientes</td></tr>";
		
		arsort($array_pacientes_sin_atender);
		
		foreach($array_pacientes_sin_atender as $key_pacientes_sin_atender => $value_pacientes_sin_atender){
			
			$class = "class='fila".(($i%2)+1)."'";	
			
			$query_cco = " SELECT Cconom
							 FROM ".$wbasedato."_000011
							WHERE Ccocod = '".$key_pacientes_sin_atender."'";
			$res_cco = mysql_query($query_cco, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_cco." - ".mysql_error());
			$row_cco = mysql_fetch_array($res_cco);
			$nombre_cco = utf8_encode($row_cco['Cconom']);
			$nombre_cco = base64_encode(serialize($nombre_cco));
			
			$value_sin_atender = base64_encode(serialize($value_pacientes_sin_atender));
			
			$html_pacientes_sin_atender .= "<tr $class>";
			$html_pacientes_sin_atender .= "<td>".utf8_encode($row_cco['Cconom'])."</td><td align=center style='cursor:pointer;' onclick=ver_detalle_sin_atender(\"$value_sin_atender\",\"".$nombre_cco."\")>".count($value_pacientes_sin_atender)."</td>";
			$html_pacientes_sin_atender .= "</tr>";
			
			$suma_pac_pacientes_sin_atender = $suma_pac_pacientes_sin_atender + count($value_pacientes_sin_atender);
			$i++;
			
		}
		
		$html_pacientes_sin_atender .= "<tr class=encabezadoTabla><td>Total</td><td align=center>".$suma_pac_pacientes_sin_atender."</td></tr>";
		$html_pacientes_sin_atender .= "</table>";
		
		$html_pacientes_sin_atender .= "<center>
											<div id='contenedor_graf_pacientes_sin_atender' style=' display:none;' align='center'>
											<div id='div_grafica_pacientes_sin_atender' style='border: 1px solid #999999; width:700px; height:400px;'>
											</div>
											</div>
										</center>";		
	
	}else{
		
		$html_pacientes_sin_atender = "No hay estadistica de pacientes sin atender hasta la fecha final seleccionada.";
		
	}
	
	//======= Especialidad que solicita la interconsulta =================
	
	if(count($array_especilidades_interc) > 0){
	
		$html_especialidad_solicita_inter .= "<br><table width='30%' class='table-bordered' id='esp_solicita_interconsulta'>";
		$html_especialidad_solicita_inter .= "<tr class=encabezadoTabla><td align=center><img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico(\"esp_solicita_interconsulta\",\"Especialidad que solicita interconsulta\")' src='../../images/medical/root/chart.png'>Especialista</td><td align=center style='cursor:pointer;' class='msg' title='".$des_cups."'>Pacientes</td></tr>";
		
		arsort($array_especilidades_interc);
		
		foreach($array_especilidades_interc as $key_esp_ordenes => $value_ordenes){
			
			$class = "class='fila".(($i%2)+1)."'";	
			
			$value_datos_ordenes = base64_encode(serialize($value_ordenes));
			$nombre_especialidad = base64_encode(serialize($key_esp_ordenes));
			
			$html_especialidad_solicita_inter .= "<tr $class>";
			$html_especialidad_solicita_inter .= "<td>".utf8_encode($key_esp_ordenes)."</td><td align=center style='cursor:pointer;' onclick=ver_detalle_interconsulta(\"$value_datos_ordenes\",\"$nombre_especialidad\")>".count($value_ordenes)."</td>";
			$html_especialidad_solicita_inter .= "</tr>";
			
			$suma_pac_ordenes = $suma_pac_ordenes + count($value_ordenes);
			$i++;
			
		}
		
		$html_especialidad_solicita_inter .= "<center>
							<div id='contenedor_graf_esp_solicita_interconsulta' style=' display:none;' align='center'>
							<div id='div_grafica_esp_solicita_interconsulta' style='border: 1px solid #999999; width:700px; height:400px;'>
							</div>
							</div>
						</center>";
		$html_especialidad_solicita_inter .= "<tr class=encabezadoTabla><td>Total</td><td align=center>".$suma_pac_ordenes."</td></tr>";
		$html_especialidad_solicita_inter .= "</table>";
	
	
	}else{
		
		$html_especialidad_solicita_inter = "No hay estadistica de pacientes con solicitud de interconsultas para esta especialidad en esta fecha.";
		
	}
	
	//============== Basados en configuracion ==================
	
	
	$campos_formulario = explode(",",$campos_formulario);
	$array_datos_formulario = array();
	
	$query_conf = "SELECT *, ".$whce."_000036.Fecha_data as Fecha36, ".$whce."_000036.Hora_data as Hora36, ".$whce."_".$wformulario.".id as id_form
					 FROM ".$whce."_000036, ".$whce."_".$wformulario."
					WHERE Firpro = '".$wformulario."'
					  AND Firfir = 'on'
					  AND Firrol = '".$especialidad."'
					  AND ".$whce."_000036.Fecha_data = ".$whce."_".$wformulario.".Fecha_data
					  AND ".$whce."_000036.Hora_data = ".$whce."_".$wformulario.".Hora_data
					  AND ".$whce."_000036.Fecha_data BETWEEN '".$fecha_inicial."' AND '".$fecha_final."'					 
				 ORDER BY ".$whce."_000036.Fecha_data";
	$res_conf = mysql_query($query_conf, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_conf." - ".mysql_error());
	$k = 0;
	//Creamos un arreglo de dos posiciones $array_datos_formulario[historia ingreso fecha hora][consecutivo]
	while($row_conf = mysql_fetch_assoc($res_conf)){
		
		$key_conf = $row_conf['id_form'];
		$array_datos_formulario[$key_conf][$row_conf['movcon']] = $row_conf;
		$k++;
	}
	
	$array_informe = array();
	$array_movdat_cco_ppal = array();
	$array_movdat_ppal = array();	
	$i	= 0;
	
	//Recorremos los consecutivos que necesitamos reportar
	foreach($campos_formulario as $key_consec => $value_consec){
		
		//Luego recorremos el arreglo creado con los datos de los pacientes
		foreach($array_datos_formulario as $key_datos => $val_consecutivo){
				
			//Recorremos todos los consecutivos de formulario por separado
			foreach($val_consecutivo as $key_movcon => $value_movcon){
				 
				//Validamos si el consecutivo del formulario corresponde a uno de los consecutivos solicitados 
				if($key_movcon == $value_consec){
				
					//Si es asi crea un arreglo de 3 dimensiones $array_informe[consecutivo][valor del registro][historia e ingreso]
					$array_informe[$key_movcon][$value_movcon['movdat']][$value_movcon['Firhis']."-".$value_movcon['Firing']] = $value_movcon;					
					$array_movdat_ppal[$key_movcon][$value_movcon['movdat']][$value_movcon['Fircco']] += 1; //Arreglo que cuenta la cantidad de registros por dato por centro de costos.
					$array_movdat_cco_ppal[$key_movcon][$value_movcon['Fircco']] = $value_movcon['Fircco']; //Arreglo de centros de costo.
				}
			
			}
			
		}
				
	}

	//Recorremos el arreglo para crear el informe separado por cada uno de los consecutivos solicitados.
	foreach($array_informe as $key_inf => $array_cons){
			
		$consultar_titulo_reporte = consultar_titulo_reporte($wformulario, $key_inf);
		
		//Si el consecutivo solicitado no es tipo seleccion muestra un mensaje avisando que no se puedne sacar estadisticas de ese tipo de dato.
		if($consultar_titulo_reporte['Dettip'] == 'Seleccion'){
						
			$html_registros_hce .= "<table width='70%' align='center'><tr><td align=center>";				
			$html_registros_hce .= "<table width='30%' class='table-bordered' id='tabla_contenedora_".$key_inf."'>";
			$html_registros_hce .= "<tr class=encabezadoTabla><td align=center colspan=2>".$consultar_titulo_reporte['Detdes']."<img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico_hce(\"".$key_inf."\",\"".$consultar_titulo_reporte['Detdes']."\")' src='../../images/medical/root/chart.png'></td></tr>";
			$html_registros_hce .= "<tr class=encabezadoTabla><td align=center>Procedimiento</td><td align=center>Pacientes</td></tr>";
			
			$suma_pac_estadistica = 0;
			
			arsort($array_cons);
			
			$html_centros_costo_reg = "";
			
			foreach($array_cons as $key_dato_con => $value_resultado){
				
				$suma_pac_estadistica = $suma_pac_estadistica + count($value_resultado);
				
				$value_resultado_datos = base64_encode(serialize($value_resultado));
				$consultar_titulo_detalle = base64_encode(serialize($key_dato_con));
				
				$class = "class='fila".(($i%2)+1)."'";						
				$html_registros_hce .= "<tr $class>";
				$html_registros_hce .= "<td>".utf8_encode($key_dato_con)."</td><td align=center style='cursor:pointer;' onclick='ver_detalle_hce(\"".$value_resultado_datos."\",\"".$consultar_titulo_detalle."\")'>".count($value_resultado)."</td>";
				$html_registros_hce .= "</tr>";			
				$i++;
				
			}
			
			//Se inicia el arreglo 
			$array_movdat_cco = $array_movdat_cco_ppal[$key_inf];
			asort($array_movdat_cco);
			$sumatotal = 0;
			//Reporte por centro de costos por consecutivo del formulario.
			$html_centros_costo_reg .= "<table class='table-bordered' id='tabla_contenedora_".$key_inf."' align='center'>";
			$html_centros_costo_reg .= "<tr class=encabezadoTabla><td align=center colspan=".(count($array_movdat_cco)+1).">".$consultar_titulo_reporte['Detdes']." por CCO<img width='20' height='19' style='float:left; cursor:pointer;' onclick='dibujarGrafico_hce(\"".$key_inf."\",\"".$consultar_titulo_reporte['Detdes']."\")' src='../../images/medical/root/chart.png'></td><td rowspan=3>&nbsp;Total&nbsp;</td></tr>";
			$html_centros_costo_reg .= "<tr class=encabezadoTabla><td align=center colspan=".(count($array_movdat_cco)+1).">Centro de costos</td></tr>";
			
			$html_centros_costo_reg .= "<tr><td class=fila2 align=center><b>Item</b></td>";
			
			foreach($array_movdat_cco as $key_cco => $value){
				
				$html_centros_costo_reg .= "<td class='encabezadoTabla'>&nbsp;".$key_cco."&nbsp;</td>";
			}
			
			$html_centros_costo_reg .= "</tr>";
			
			$array_movdat = $array_movdat_ppal[$key_inf];
			ksort($array_movdat);
			
			$array_cco_can = array();
			$array_dat = array();
			
			foreach($array_movdat as $key_movdat => $value_movdat){				
				
				$class = "class='fila".(($i%2)+1)."'";
				$html_centros_costo_reg .= "<tr $class><td>".$key_movdat."</td>";				
				$cant_cc_dat_total = 0;
				
				foreach($array_movdat_cco as $key_c => $v_c ){
					
					$cant_cc_dat = 0;
					$cantidad = $value_movdat[$key_c];
					$html_centros_costo_reg .= "<td align=right>".$cantidad."</td>";
					
					$cant_cc_dat = $cant_cc_dat + $cantidad;
					$cant_cc_dat_total = $cant_cc_dat_total + $cantidad;					
					$array_dat[$key_movdat] = $cant_cc_dat;
					$array_cco_can[$key_c] += $cant_cc_dat;
					
				}
				
				$html_centros_costo_reg .= "<td align=right>".$cant_cc_dat_total."</td>";
				$html_centros_costo_reg .= "</tr>";
				$i++;
			}
			
			//Totales por centro de costos
			$html_centros_costo_reg .= "<tr class=encabezadoTabla><td>Total</td>";
			
			foreach($array_cco_can as $key_cctotal => $v_cantidad){
				
				$html_centros_costo_reg .= "<td align=right>".$v_cantidad."</td>";
				$sumatotal = $sumatotal + $v_cantidad;
				
			}

			$html_centros_costo_reg .= "<td align=right>".$sumatotal."</td>";			
			$html_centros_costo_reg .= "</tr>";			
			
			$html_centros_costo_reg .= "</table>";
			
			$html_registros_hce .= "<tr class=encabezadoTabla><td>Total</td><td align=center class='msg' title='Este valor puede corresponder a mas de una opcion por paciente, el otro valor corresponde a todos los registros en el formulario HCE por centro de costos'>".$suma_pac_estadistica."</td></tr>";
			$html_registros_hce .= "</table></td></tr><tr><td><br><br>".$html_centros_costo_reg."<br><br><hr style='height:5px;background-color:grey;'></td></tr></table><br><br>";
			$html_registros_hce .= "<center>
									<div id='contenedor_graf_".$key_inf."' style=' display:none;' align='center'>
									<div id='div_grafica_".$key_inf."' style='border: 1px solid #999999; width:700px; height:400px;'>
									</div>
									</div>
									</center>";
		}else{
			
			$html_registros_hce .= "<hr>El campo ".$consultar_titulo_reporte['Detdes']." del reporte ".strtoupper($nombre_reporte)." no permite realizar estadistica.<hr>";
		}
	}
	
	$sql = " SELECT Encdes
			   FROM ".$whce."_000001
			  WHERE Encpro = '".$wformulario."'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(Form):</b><br>".mysql_error());
	$row = mysql_fetch_array($res);
	
	if(count($html_registros_hce) > 0){
			
		$texto_formulario_basado = "Informaci&oacute;n basada en registros del formulario HCE: <br><b>Formulario ".$row['Encdes']."</b><br><br>";
				
	}else{
		
		$html_registros_hce = "Sin campos del formulario ".$row['Encdes']." configurados para mostrar informaci&oacute;n.";
	}
	
	
	//===========================================================
	
	
	$datamensaje['html_pestanas'].= '<fieldset><legend><b>'.strtoupper($nombre_reporte).'</b></legend>';
	$datamensaje['html_pestanas'].= 
		'<div id="tabs">
		  <ul>
			<li><a href="#tabs-1">Entidad</a></li>
			<li><a href="#tabs-2">Centro de costos</a></li>
			<li><a href="#tabs-3">Especialista</a></li>
			<li><a href="#tabs-4">Especialidad que solicita interconsulta</a></li>
			<li><a href="#tabs-5">Basado en registros HCE</a></li>
			<li><a href="#tabs-6">Sin atender</a></li>			
		  </ul>
		  <div id="tabs-1">
		    '.$nombre_formularios.'
			'.$html_entidades.'
		  </div> 
		  <div id="tabs-2">
			'.$nombre_formularios.'
			'.$html_centros_costo.'
		  </div> 
		  <div id="tabs-3">
			'.$nombre_formularios.'
			'.$html_especialista.'
		  </div>
		  <div id="tabs-4">		    
		    '.$des_cups.'
			'.$html_especialidad_solicita_inter.'
			'.$mensaje_error.'
		  </div>
		  <div id="tabs-5">
			'.utf8_encode($texto_formulario_basado).'
			'.utf8_encode($html_registros_hce).'
		  </div>
		  <div id="tabs-6">		    
		    '.$html_pacientes_sin_atender_text.'
		    '.$des_cups_ordenados.'
			'.$html_pacientes_sin_atender.'
			'.$mensaje_error.'
		  </div>
		</div>';
		
	$datamensaje['html_pestanas'].= '</fieldset>';
	
	echo json_encode($datamensaje);	
	return;
	
}

function traer_datos_usuario($cod_usu){

	global $conex;
	global $wemp_pmla;

	$query = " SELECT Descripcion
				 FROM usuarios
				WHERE Codigo = '".$cod_usu."'";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
	$row = mysql_fetch_array($res);
	
	return $row;
		
}

function consultar_titulo_reporte($wformulario, $consecutivo){
	
	global $conex;
	global $wemp_pmla;
	global $whce;
	
	$query = "SELECT Detdes, Dettip 
			    FROM ".$whce."_000002
			   WHERE Detpro = '".$wformulario."'
				 AND Detcon = '".$consecutivo."'";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_conf." - ".mysql_error());
	$row = mysql_fetch_array($res);
	
	
	return $row;
}

function consultar_entidad($entidad){
	
	global $conex;
	global $wemp_pmla;
	global $wbasedato;
	global $wcliame;
	
	$query = "SELECT Empnom
			    FROM ".$wcliame."_000024
			   WHERE Empcod = '".$entidad."'";
	$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$query_conf." - ".mysql_error());
	$row = mysql_fetch_array($res);	
	
	return $row['Empnom'];
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
            $datos['meses'] = $meses;
            $datos['dias'] = $dias;
        }

        return $datos;
    }

function consultar_especialidad($cod_medico){
	
	global $conex;
	global $wbasedato;	
	
	$q = "SELECT Espnom, CONCAT(Medno1,' ',Medno2,' ',Medap1,' ', Medap2) as Descripcion
		    FROM ".$wbasedato."_000044, ".$wbasedato."_000048
		   WHERE Medesp = Espcod
		     AND Meduma = '".$cod_medico."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$info = mysql_fetch_assoc($res);
	
	return $info;
	
	
}

function traer_datos_paciente($historia,$ingreso){
	
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $whce;
	
	
	$q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, Ingnre, pacnac
		    FROM root_000036, root_000037, ".$wbasedato."_000016
		   WHERE oriced = pacced
			 AND Inghis = orihis
			 AND oritid = pactid
			 AND oriori = '".$wemp_pmla."'
			 AND inghis = '".$historia."'
			 AND inging = '".$ingreso."';";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$datos = array();
	
	while($info = mysql_fetch_array($res)){
		
		$datos['nombre_paciente'] = utf8_encode($info['pacno1'])." ".utf8_encode($info['pacno2'])." ".utf8_encode($info['pacap1'])." ".utf8_encode($info['pacap2']);
		$datos['responsable'] = utf8_encode($info['Ingnre']);
		$edad = calcularAnioMesesDiasTranscurridos($info['pacnac']);
		$datos['edad'] = $edad['anios'];
		
	}
	
	return $datos;
	
	
}

function calcula_tiempo($start_time, $end_time) { 
		$total_seconds = strtotime($end_time) - strtotime($start_time); 
		$horas              = floor ( $total_seconds / 3600 );
		$minutes            = ( ( $total_seconds / 60 ) % 60 );
		$seconds            = ( $total_seconds % 60 );
		 
		$time['horas']      = str_pad( $horas, 2, "0", STR_PAD_LEFT );
		$time['minutes']    = str_pad( $minutes, 2, "0", STR_PAD_LEFT );
		$time['seconds']    = str_pad( $seconds, 2, "0", STR_PAD_LEFT );
		 
		$time               = implode( ':', $time );
		 
		return $time;
	}
	
	
function traer_lista_formlarios_reportes(){
	
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	global $whce;
	
	$wuser = explode('-',$_SESSION['user']);
	$wusuario = $wuser[1];
	
	$q = " SELECT Medesp
             FROM ".$wbasedato."_000048
            WHERE Meduma = '".$wusuario."'
			  AND Medest = 'on'";
    $res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$row = mysql_fetch_array($res);
	
	$q1 = " SELECT * FROM (
			SELECT *
              FROM ".$wbasedato."_000240
             WHERE Repesp = '".$row['Medesp']."'
			   AND Repest = 'on'
			 UNION
			SELECT *
              FROM ".$wbasedato."_000240
             WHERE Repusu like '%".$wusuario."%'
			   AND Repest = 'on') AS t ORDER BY Repnom";
    $res1 = mysql_query($q1, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
    
	$datos = array();
	while($row1 = mysql_fetch_assoc($res1)){
		
		$datos[$row1['id']] = $row1;
		
	}	
	
	return $datos;	
	
	
}


?>

<html>
<head>
  <meta content="text/html; charset=UTF8" http-equiv="content-type">
  <title>Reportes por especialidad</title>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

	<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqueryui_1_9_2/jquery-ui.js"></script>
	<script type="text/javascript" src="../../../include/root/modernizr.custom.js"></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type="text/javascript" src="../../../include/root/LeerTablaAmericas.js"></script>
    <script type="text/javascript" src="../../../include/root/amcharts/amcharts.js"></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script type='text/javascript' src="../../../include/root/bootstrap.min.js"></script>
	<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
	<script type="text/javascript" >
	
$(function(){
		
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
	
	$( "#fecha_inicial" ).datepicker();
	$( "#fecha_final" ).datepicker();
		
	
});	

function ver_informacion(ver){

	$("#"+ver).toggle();
	
}


function dibujarGrafico(tabla_contenedora, titulo){
              
			  
        $("#"+tabla_contenedora).LeerTablaAmericas({
            empezardesdefila : 1,
			ultimafila 		 : 'no',
			titulo           : titulo,
            tituloy          : 'Pacientes',
            rotulos          : 'si',
            tipografico      : 'torta',
            gradoderotulos   : 45,
            dimension        : '3d',
			filaencabezado 	 : [0, 1],
            divgrafica       : 'div_grafica_'+tabla_contenedora,
			
									
        });
		
		$("#contenedor_graf_"+tabla_contenedora).dialog({
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                 },
                 show: {
                     effect: "blind",
                     duration: 500
                  },
                 hide: {
                     effect: "blind",
                     duration: 500
                  },
                 height: 600,
                 width: 800,
                 rezisable: true
            });
       
    }
	
	
function dibujarGrafico_hce(consecutivo, titulo){
        
       
        $("#tabla_contenedora_"+consecutivo).LeerTablaAmericas({
            empezardesdefila : 2,
			ultimafila 		 : 'no',
			titulo           : titulo,
            tituloy          : 'Pacientes',
            rotulos          : 'si',
            tipografico      : 'torta',
            gradoderotulos   : 45,
            dimension        : '3d',
            divgrafica       : 'div_grafica_'+consecutivo,
			
									
        });
		
		$("#contenedor_graf_"+consecutivo).dialog({
                 modal: true,
                 buttons: {
                    Ok: function() {
                        $( this ).dialog( "close" );
                    }
                 },
                 show: {
                     effect: "blind",
                     duration: 500
                  },
                 hide: {
                     effect: "blind",
                     duration: 500
                  },
                 height: 600,
                 width: 800,
                 rezisable: true
            });
       
    }
	

function ver_detalle_sin_atender(datos_sin_atender,nom_centro_costos){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle_sin_atender',
				datos_sin_atender : datos_sin_atender,
				nom_centro_costos : nom_centro_costos
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					
					$("#ver_detalle").html(data_json.datos_detalle_sin_atender);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									 title: data_json.des_centro_costos,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
										
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#detalle_pacientes_sin_atender .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}


function ver_detalle_atendidos_tiempo(datos_array_esp_tiempo, key_medico){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle_atendidos_tiempo',
				datos_array_esp_tiempo : datos_array_esp_tiempo,
				key_medico		: key_medico
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					$("#ver_detalle").html(data_json.datos_detalle_atendidos_tiempo);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									 title: data_json.nombre_especialista,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
										
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#ver_datos_detalle_tiempos .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}



function ver_detalle_especialista(datos_paciente_especialista, tipo){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle_especialista',
				datos_paciente_especialista : datos_paciente_especialista,
				tipo			: tipo
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					
					$("#ver_detalle").html(data_json.datos_detalle_esp);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									 title: data_json.nombre_especialista,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
										
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#tabla_detalle_especialistas .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}


function ver_detalle_interconsulta(datos_inter_especialidad, nombre_especialidad){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle_interconsulta',
				datos_inter_especialidad : datos_inter_especialidad,
				nombre_especialidad : nombre_especialidad
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					
					$("#ver_detalle").html(data_json.datos_detalle_inter_espec);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									 title: data_json.nombre_especialidad,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
										
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#detalle_esp_solicita_interconsulta .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}


function ver_detalle_cco(datos_centro_costos, nombre_cco, tipo){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle_cco',
				datos_centro_costos : datos_centro_costos,
				nombre_cco 		: nombre_cco,
				tipo			: tipo
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					
					$("#ver_detalle").html(data_json.datos_detalle_cco);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									 title: data_json.descripcion_cco,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
										
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#tabla_detalle_centro_costos .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}


function ver_detalle_hce(datos_paciente_hce, titulo){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla			: $("#wemp_pmla").val(),
				consultaAjax 		: '',
				operacion 			: 'ver_detalle_hce',	
				datos_paciente_hce  : datos_paciente_hce,
				titulo				: titulo
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					
					$("#ver_detalle").html(data_json.datos_detalle_hce);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									 title: data_json.titulo_conf,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
										
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#tabla_registros_hce .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}


function ver_detalle_entidad(datos_por_entidades,entidad, tipo){
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
		
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'ver_detalle_entidad',
				datos_por_entidades : datos_por_entidades,
				tipo				: tipo,
				entidad				: entidad
				
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					$("#esperar").hide();
					
					$("#ver_detalle").html(data_json.datos_detalle_entidad);
					
					$("#ver_detalle").dialog({
									 modal: true,
									 buttons: {
										Ok: function() {
											$( this ).dialog( "close" );
										}
									 },
									 show: {
										 effect: "blind",
										 duration: 500
									  },
									 hide: {
										 effect: "blind",
										 duration: 500
									  },
									  
									 title: data_json.nombre_entidad,
									 height: 600,
									 width: 1000,
									 rezisable: true
								});						
					
					
					
				}
			}

		}).done(function(){
			
				$('#buscador').quicksearch('#tabla_detalle_entidades .findDatos');
				
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}

function generar_reporte(este){
	
	var id_reporte = $("#cod_formulario_rep").val();	
	
	if($("#cod_formulario_rep").val() == ''){
		
		alert("Debe seleccionar un reporte");
		return;
	}
	
	var finicio = new Date($("#fecha_inicial").val());
	var ffin = new Date($("#fecha_final").val());
	
	if(finicio > ffin){
		alert('La fecha de inicio debe ser mayor que la fecha final.');
		return;
	}
	
	var fecha_maxima = finicio.setMonth(finicio.getMonth() + 2);
	var fecha_maxima = new Date(fecha_maxima);
	
	if(ffin > fecha_maxima){
		alert("Solo se permiten lapsos de consulta de dos meses.");
		return;
	}
	
	
	$("#esperar").show();
	$("#esperar").html('<img src="../../images/medical/ajax-loader5.gif" >');
	
	
	
	
	$.ajax({
			url: "generador_reportes.php",
			type: "POST",
			data:{

				wemp_pmla		: $("#wemp_pmla").val(),
				consultaAjax 	: '',
				operacion 		: 'generar_reporte',
				id_reporte		: id_reporte,
				fecha_inicial	: $("#fecha_inicial").val(),
				fecha_final		: $("#fecha_final").val(),
				nombre_reporte 	: $("#cod_formulario_rep option:selected").text()
			},
			dataType: "json",
			async: true,
			success:function(data_json) {

				if (data_json.error == 1)
				{
					alert(data_json.mensaje);
				}
				else{
					
					if(data_json.html_generales != ''){
						
						$("#esperar").hide();						
						$("#resultado_reporte").html(data_json.html_pestanas);
					}
					
				}
			}

		}).done(function(){
				
				$(".msg").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });
				
				$( "#tabs" ).tabs({
					  beforeLoad: function( event, ui ) {
						ui.jqXHR.fail(function() {
						  ui.panel.html(
							"No hay informacion para mostrar ");
						});
					  }
					});
			 }).fail(function(xhr, textStatus, errorThrown) { mensajeFailAlert('', xhr, textStatus, errorThrown); });

}

function mensajeFailAlert(mensaje, xhr, textStatus, errorThrown)
    {
        var msj_extra = '';
        msj_extra = (mensaje != '') ? "<br>"+mensaje: mensaje;
        jAlert($("#failJquery").val()+msj_extra, "Mensaje");
        // $("#div_error_interno").html(xhr.responseText);
        // console.log(xhr);
        // jAlert("error interno: "+xhr.responseText, "Mensaje"); console.log("error");
        // fnModalLoading_Cerrar();
        // $(".bloquear_todo").removeAttr("disabled");
    }

	</script>
</head>
<style type="text/css">

</style>
<body>

<?php
encabezado( "REPORTES POR ESPECIALIDAD", $actualiz ,"clinica" );

?>

<input type="hidden" id="wemp_pmla" value="<?=$wemp_pmla?>">
<center>
<br>

	<?php
	
	$formularios = traer_lista_formlarios_reportes();
		
	foreach($formularios as $key => $value){
		
			$option_select .= "<option value='".$key."'>".utf8_encode($value['Repnom'])."</option>";
			
	}
	
	echo '<table border="0" cellpadding="1" cellspacing="1" class="table-bordered" width="40%">
			  <tbody>
				<tr class=fila1>
				  <td colspan="3" rowspan="1">
				  <b>Reporte:</b>
				  <select id="cod_formulario_rep" style="width:400px">
				  <option value=""></option>
				  '.$option_select.' 
				  </select>
				  </td>
				</tr>
				<tr class=fila2>
				  <td colspan="1" rowspan="1"><b>Fecha inicial:</b><input id="fecha_inicial" value="'.date("Y-m-d").'"></td>
				  <td></td>
				  <td><b>Fecha final:</b><input id="fecha_final" value="'.date("Y-m-d").'"></td>
				</tr>				
			  </tbody>
			</table>
			<center><br><input value="Generar" onclick="generar_reporte(this)" type="button"></center>';
		
		echo "<br>";
		echo "<div id=esperar></div>";
		echo "<div id='resultado_reporte'></div>";
		echo "<div id='ver_detalle'></div>";
		echo "<center><br><br><INPUT type='button' value='Cerrar' onclick='cerrarVentana();' style='width:100px'></center>";
	
	?>


</center>
<input type='hidden' name='failJquery' id='failJquery' value='El programa termino de ejecutarse pero con algunos inconvenientes <br>(El proceso no se completó correctamente)' >

</body>
</html>