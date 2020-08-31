<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Reporte de seguimiento de gestión de atención de Arkadia
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2020-03-09
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-03-18';
//--------------------------------------------------------------------------------------------------------------------------------------------
// 2020-03-18 - Jessica Madrid Mejía:	- Se modifica el query en la función consultarInformacionReporte() para obtener el centro de costos 
// 										  donde ser realiza el procedimiento de hce_000015 y no de hce_000047 y hce_000017.
// 2020-03-16 - Jessica Madrid Mejía:	- Se inmoviliza el encabezado de la tabla
// 										- Se limita el rango de fechas a seleccionar a un mes
// 2020-03-11 - Jessica Madrid Mejía:	- Se formatea la hora de la cita y se agrega el estado "Pendiente" si la cita no ha ocurrido.
// 										- Se agrega utf8_encode en las observaciones de la bitácora de gestiones

//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	include_once("root/comun.php");
	
	$wbasedatoMovhos = consultarAliasPorAplicacion($conex,$wemp_pmla , 'movhos');
	$wbasedatoHCE = consultarAliasPorAplicacion($conex,$wemp_pmla , 'hce');
	$wbasedatoCliame = consultarAliasPorAplicacion($conex,$wemp_pmla , 'facturacion');
	
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");
	
//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	function consultarUnidadesCco($conex, $wbasedatoMovhos)
	{
		$query = "SELECT Unicod,Unides,Ccocod,Cconom 
					FROM root_000113
			  INNER JOIN ".$wbasedatoMovhos."_000011 
					  ON Ccocun=Unicod
				   WHERE Uniamb='on';";
				   
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$array = array(); 
		if($num>0)
		{
			while($row = mysql_fetch_array($res))
			{
				$array[$row['Unicod']]['codigoUnidad'] = $row['Unicod'];
				$array[$row['Unicod']]['descripcionUnidad'] = utf8_encode($row['Unides']);
				$array[$row['Unicod']]['ccosUnidad'][$row['Ccocod']]['codigoCco'] = $row['Ccocod'];
				$array[$row['Unicod']]['ccosUnidad'][$row['Ccocod']]['descripcionCco'] = utf8_encode($row['Cconom']);
			}	
		}
		
		return $array;		
	}
	
	function consultarEspecialidades($conex, $wbasedatoMovhos)
	{
		$query = "SELECT Espcod,Espnom 
					FROM ".$wbasedatoMovhos."_000044 
				ORDER BY Espnom;";
				   
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$arrayEspecialidades = array(); 
		if($num>0)
		{
			while($row = mysql_fetch_array($res))
			{
				$arrayEspecialidades[$row['Espcod']] = utf8_encode($row['Espnom']);
			}	
		}
		
		return $arrayEspecialidades;		
	}
	
	function consultarDetalleGestiones($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item)
	{
		$query = "SELECT Fecha_data, Hora_data, Bitusu, Descripcion 
					FROM ".$wbasedatoCliame."_000334
			  INNER JOIN usuarios
					  ON Codigo=Bitusu 
				   WHERE Bithis='".$historia."' 
					 AND Biting='".$ingreso."' 
					 AND Bittor='".$tipoOrden."' 
					 AND Bitnro='".$nroOrden."' 
					 AND Bitite='".$item."' 
					 AND Bitest='on'
				ORDER BY Fecha_data, Hora_data;";
					
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$array = array(); 
		if($num>0)
		{
			$contGestiones = 0;
			while($row = mysql_fetch_array($res))
			{
				$array[$contGestiones]['fechaHora'] = $row['Fecha_data']." ".$row['Hora_data'];
				$array[$contGestiones]['codigoUsuario'] = $row['Bitusu'];
				$array[$contGestiones]['nombreUsuario'] = $row['Descripcion'];
				$contGestiones++;
			}
		}
		
		return $array;
	}
	
	function consultarGestionAutorizacion($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item)
	{
		$query = "SELECT cliame_333.Gesesa,cliame_335.Eaudes
					FROM ".$wbasedatoCliame."_000333 AS cliame_333 
			   LEFT JOIN ".$wbasedatoCliame."_000335 AS cliame_335
					  ON cliame_335.Eaucod = cliame_333.Gesesa
				   WHERE Geshis='".$historia."' 
					 AND Gesing='".$ingreso."' 
					 AND Gestor='".$tipoOrden."' 
					 AND Gesnro='".$nroOrden."' 
					 AND Gesite='".$item."' 
					 AND Gesest='on'
				ORDER BY cliame_333.Fecha_data, cliame_333.Hora_data;";
					
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$array = array('codigo' => '','descripcion' => ''); 
		if($num>0)
		{
			$row = mysql_fetch_array($res);
			
			$array['codigo'] = $row['Gesesa'];
			$array['descripcion'] = utf8_encode($row['Eaudes']);
		}
		
		return $array;
	}
	
	function consultarGestionNoProgramada($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item)
	{
		$query = "SELECT cliame_333.Gesmot,cliame_335.Eaudes
					FROM ".$wbasedatoCliame."_000333 AS cliame_333 
			   LEFT JOIN ".$wbasedatoCliame."_000335 AS cliame_335
					  ON cliame_335.Eaucod = cliame_333.Gesmot
				   WHERE Geshis='".$historia."' 
					 AND Gesing='".$ingreso."' 
					 AND Gestor='".$tipoOrden."' 
					 AND Gesnro='".$nroOrden."' 
					 AND Gesite='".$item."' 
					 AND Gesest='on'
					 AND Gesmot!=''
				ORDER BY cliame_333.Fecha_data, cliame_333.Hora_data;";
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$array = array('codigo' => '','descripcion' => ''); 
		if($num>0)
		{
			$row = mysql_fetch_array($res);
			
			$array['codigo'] = $row['Gesmot'];
			$array['descripcion'] = utf8_encode($row['Eaudes']);
		}
		
		return $array;
	}
	function consultarGestionEstadoProceso($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item)
	{
		$query = "SELECT cliame_333.Gesesp,cliame_335.Eaudes, cliame_335.Eauter
					FROM ".$wbasedatoCliame."_000333 AS cliame_333 
			   LEFT JOIN ".$wbasedatoCliame."_000335 AS cliame_335
					  ON cliame_335.Eaucod = cliame_333.Gesesp
				   WHERE Geshis='".$historia."' 
					 AND Gesing='".$ingreso."' 
					 AND Gestor='".$tipoOrden."' 
					 AND Gesnro='".$nroOrden."' 
					 AND Gesite='".$item."' 
					 AND Gesest='on'
				ORDER BY cliame_333.Fecha_data, cliame_333.Hora_data;";
					
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$array = array('codigo' => '','descripcion' => '','terminado' => ''); 
		if($num>0)
		{
			$row = mysql_fetch_array($res);
			$array['codigo'] = $row['Gesesp'];
			$array['descripcion'] = utf8_encode($row['Eaudes']);
			$array['terminado'] = $row['Eauter'];
		}
		
		return $array;
	}
	
	function consultarCitas($conex, $wbasedatoMovhos, $wbasedatoHCE, $historia, $ingreso, $tipoOrden, $nroOrden, $item, $servicio, $documento, $fechaIngreso, $horaIngreso)
	{
		$query = "SELECT Ccocip,Ccococ 
					FROM ".$wbasedatoMovhos."_000011 
				   WHERE Ccocod='".$servicio."' 
					 AND Ccocip!='' 
					 AND Ccococ!='';";
					 
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$arrayCita = array('fechaProgramacion' => '','horaProgramacion' => '', 'fechaRealizacion' => '','horaRealizacion' => '','asistida' => ''); 
		if($num>0)
		{
			$row = mysql_fetch_array($res);
			
			$queryCita = "SELECT Fecha_data, Hora_data,Fecha, Hi, Asistida 
							FROM ".$row['Ccocip']."_".$row['Ccococ']."
						   WHERE Cedula='".$documento."' 
							 AND Fecha_data='".$fechaIngreso."' 
							 AND Hora_data>='".$horaIngreso."' 
							 AND Activo='A';";
			$resCita = mysql_query($queryCita, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCita . " - " . mysql_error());
			$numCita = mysql_num_rows($resCita);
			
			if($numCita>0)
			{
				$rowCita = mysql_fetch_array($resCita);
				
				$horaCita = substr($rowCita['Hi'], 0, 2).":".substr($rowCita['Hi'], 2, 2).":00";
				$fechaHoraActual = strtotime("now");
				$fechaHoraCita = strtotime($rowCita['Fecha']." ".$horaCita);
				
				$citaAsistida = "PENDIENTE";
				if($fechaHoraActual>$fechaHoraCita)
				{
					$citaAsistida = "NO";
					if($rowCita['Asistida']=="on")
					{
						$citaAsistida = "SI";
					}
				}
				
				$arrayCita['fechaProgramacion'] = $rowCita['Fecha_data'];
				$arrayCita['horaProgramacion'] = $rowCita['Hora_data'];
				$arrayCita['fechaRealizacion'] = $rowCita['Fecha'];
				$arrayCita['horaRealizacion'] = $horaCita;
				$arrayCita['asistida'] = $citaAsistida;
			}
		}
		
		return $arrayCita;
		
	}
	
	function consultarGestion($conex, $wbasedatoMovhos, $wbasedatoHCE, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item, $servicio, $documento, $fechaIngreso, $horaIngreso)
	{
		$arrayGestion = array();
		$gestiones = consultarDetalleGestiones($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item);
		$autorizacion = consultarGestionAutorizacion($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item);
		$motivoNoProgramada = consultarGestionNoProgramada($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item);
		$estadoProceso = consultarGestionEstadoProceso($conex, $wbasedatoCliame, $historia, $ingreso, $tipoOrden, $nroOrden, $item);
		$cita = consultarCitas($conex, $wbasedatoMovhos, $wbasedatoHCE, $historia, $ingreso, $tipoOrden, $nroOrden, $item, $servicio, $documento, $fechaIngreso, $horaIngreso);
		
		$fechaFinGestionAutorizacion = "";
		if($estadoProceso['terminado']=="on")
		{
			$fechaFinGestionAutorizacion = $gestiones[0];
		}
		$arrayGestion['fechaInicioGestionAutorizacion'] = $gestiones[0]['fechaHora'];
		$arrayGestion['fechaFinGestionAutorizacion'] = $gestiones[count($gestiones)-1]['fechaHora'];
		$arrayGestion['cantidadGestiones'] = count($gestiones);
		$arrayGestion['gestiones'] = $gestiones;
		$arrayGestion['estadoAutorizacion'] = $autorizacion['descripcion'];
		$arrayGestion['estadoNoProgramado'] = $motivoNoProgramada['descripcion'];
		$arrayGestion['estadoProceso'] = $estadoProceso['descripcion'];
		$arrayGestion['fechaProgramacionCita'] = $cita['fechaProgramacion'];
		$arrayGestion['horaProgramacionCita'] = $cita['horaProgramacion'];
		$arrayGestion['fechaRealizacionCita'] = $cita['fechaRealizacion'];
		$arrayGestion['horaRealizacionCita'] = $cita['horaRealizacion'];
		$arrayGestion['asistida'] = $cita['asistida'];
		// echo "<pre>".print_r($arrayGestion,true)."</pre>";
		
		return $arrayGestion;
	}
	
	function consultarInformacionReporte($conex, $wbasedatoMovhos, $wbasedatoHCE, $wbasedatoCliame, $wemp_pmla, $fechaInicial, $fechaFinal, $cco, $especialidad, $historia, $ingreso)
	{
		$condicionCco = "";
		if($cco!="" && count($cco)>0)
		{
			$listaCco = implode("','",$cco);
			$condicionCco = "AND cliame_101.Ingsei IN ('".$listaCco."')";
		}
		
		$condicionEspecialidad = "";
		if($especialidad!="" && count($especialidad)>0)
		{
			$listaEspecialidad = implode("','",$especialidad);
			$condicionEspecialidad = "AND movhos_44.Espcod IN ('".$listaEspecialidad."')";
		}
		
		$condicionHistoria = "";
		$condicionIngreso = "";
		if($historia!="")
		{
			$condicionHistoria = "AND cliame_101.Inghis = '".$historia."'";
			if($ingreso!="")
			{
				$condicionIngreso = "AND cliame_101.Ingnin = '".$ingreso."'";;
			}
		}
		
		$query = "SELECT cliame_101.Inghis,cliame_101.Ingnin,cliame_101.Ingfei,cliame_101.Inghin,cliame_101.Ingsei,cliame_101.Ingcem,cliame_101.Ingent,cliame_101.Ingmei,CONCAT_WS(' ',Pacno1,Pacno2,Pacap1,Pacap2) AS Paciente,cliame_100.Pacdoc, movhos_11.Cconom, CONCAT_WS(' ',Medno1,Medno2,Medap1,Medap2) AS Medico, movhos_48.Medesp, movhos_44.Espnom,hce_27.Ordtor,hce_27.Ordnro,hce_27.Fecha_data,hce_27.Hora_data, hce_28.Detcod, hce_28.Detite, hce_15.Descripcion, hce_47.Descripcion AS procedimiento47, hce_17.Descripcion AS procedimiento17, hce_15.Tipcco
					FROM ".$wbasedatoCliame."_000101 AS cliame_101
			  INNER JOIN cliame_000100 AS cliame_100
					  ON ".$wbasedatoCliame."_100.Pachis=cliame_101.Inghis 
			  INNER JOIN ".$wbasedatoMovhos."_000011 AS movhos_11
					  ON movhos_11.Ccocod=cliame_101.Ingsei
			  INNER JOIN ".$wbasedatoMovhos."_000048 AS movhos_48
					  ON movhos_48.Meddoc=cliame_101.Ingmei
					 AND movhos_48.Medest='on'
			  INNER JOIN ".$wbasedatoMovhos."_000044 AS movhos_44
					  ON movhos_44.Espcod=movhos_48.Medesp
					  ".$condicionEspecialidad."
			   LEFT JOIN ".$wbasedatoHCE."_000027 AS hce_27
					  ON hce_27.Ordhis=cliame_101.Inghis
					 AND hce_27.Ording=cliame_101.Ingnin
			   LEFT JOIN ".$wbasedatoHCE."_000028 AS hce_28
					  ON hce_28.Dettor=hce_27.Ordtor
					 AND hce_28.Detnro=hce_27.Ordnro
					 AND hce_28.Detest='on'
			   LEFT JOIN ".$wbasedatoHCE."_000047 AS hce_47
					  ON hce_47.Codigo=hce_28.Detcod
			   LEFT JOIN ".$wbasedatoHCE."_000017 AS hce_17
					  ON hce_17.Codigo=hce_28.Detcod
			   LEFT JOIN ".$wbasedatoHCE."_000015 AS hce_15
					  ON hce_15.Codigo=hce_27.Ordtor
				   WHERE cliame_101.Ingfei BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
					 ".$condicionCco."
					 ".$condicionHistoria."
					 ".$condicionIngreso."
				ORDER BY cliame_101.Ingfei,cliame_101.Inghin;";
				
		$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
		$num = mysql_num_rows($res);
		// echo "<pre>".print_r($query,true)."</pre>";
		$arrayPacientes = array(); 
		if($num>0)
		{
			while($row = mysql_fetch_array($res))
			{
				$responsable = "PARTICULAR";
				if($row['Ingent']!="")
				{
					$responsable = explode("-->",$row['Ingent']);
					$responsable = utf8_encode($responsable[0]);
				}
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['historia'] = $row['Inghis'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ingreso'] = $row['Ingnin'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['fechaIngreso'] = $row['Ingfei'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['horaIngreso'] = $row['Inghin'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['codigoCcoIngreso'] = $row['Ingsei'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['codigoResponsable'] = $row['Ingcem'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['descripcionResponsable'] = $responsable;
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['docMedicoIngreso'] = $row['Ingmei'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['paciente'] = utf8_encode($row['Paciente']);
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['descripcionCcoIngreso'] = $row['Cconom'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['medicoIngreso'] = utf8_encode($row['Medico']);
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['codigoEspecialidad'] = $row['Medesp'];
				$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['descripcionEspecialidad'] = utf8_encode($row['Espnom']);
				
				if(!isset($arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['cantidadTotalOrdenes']))
				{
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['cantidadTotalOrdenes'] = 0;
				}
				
				if($row['Ordtor']!="")
				{
					$procedimiento = $row['procedimiento47'];
					if(trim($row['procedimiento47'])=="")
					{
						$procedimiento = $row['procedimiento17'];
					}
					$servicio = $row['Tipcco'];
					
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['cantidadTotalOrdenes'] += 1;
					
					if(!isset($arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['cantidadOrdenes']))
					{
						$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['cantidadOrdenes'] = 0;
					}
					
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['fechaOrden'] = $row['Fecha_data'];
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['horaOrden'] = $row['Hora_data'];
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['tipoOrden'] = $row['Ordtor'];
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['nroOrden'] = $row['Ordnro'];
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['desTipoOrden'] = utf8_encode($row['Descripcion']);
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['cantidadOrdenes'] += 1;
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['detalleOrden'][$row['Detite']]['item'] = $row['Detite'];
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['detalleOrden'][$row['Detite']]['codProcedimiento'] = $row['Detcod'];
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['detalleOrden'][$row['Detite']]['desProcedimiento'] = utf8_encode($procedimiento);
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][$row['Ordtor']."-".$row['Ordnro']]['detalleOrden'][$row['Detite']]['gestion'] = consultarGestion($conex, $wbasedatoMovhos, $wbasedatoHCE, $wbasedatoCliame, $row['Inghis'], $row['Ingnin'], $row['Ordtor'], $row['Ordnro'], $row['Detite'],$servicio, $row['Pacdoc'],$row['Ingfei'],$row['Inghin']);
				}
				else
				{
					$arrayPacientes[$row['Inghis']."-".$row['Ingnin']]['ordenes'][] = array();
				}
			}	
		}
		// echo "<pre>".print_r($arrayPacientes,true)."</pre>";
		return $arrayPacientes;	
	}
	
	function consultarDetalleBitacoraGestion($conex, $wbasedatoCliame, $wemp_pmla, $historia, $ingreso, $tipoOrden, $nroOrden, $item)
	{
		$queryGestion = "SELECT Fecha_data, Hora_data, Bitusu, Bitobs, Descripcion
						   FROM ".$wbasedatoCliame."_000334
					 INNER JOIN usuarios
							 ON Codigo=Bitusu 
						  WHERE Bithis='".$historia."' 
							AND Biting='".$ingreso."' 
							AND Bittor='".$tipoOrden."' 
							AND Bitnro='".$nroOrden."' 
							AND Bitite='".$item."' 
							AND Bitest='on'
					   ORDER BY Fecha_data DESC, Hora_data DESC;";
							
		$res = mysql_query($queryGestion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryGestion . " - " . mysql_error());
		$num = mysql_num_rows($res);
		
		$array = array(); 
		if($num>0)
		{
			$registro = 0;
			while($row = mysql_fetch_array($res))
			{
				$array[$registro]['fecha'] = $row['Fecha_data'];
				$array[$registro]['hora'] = $row['Hora_data'];
				$array[$registro]['codigoUsuario'] = $row['Bitusu'];
				$array[$registro]['observacion'] = utf8_encode($row['Bitobs']);
				$array[$registro]['nombreUsuario'] = utf8_encode($row['Descripcion']);
				
				$registro++;
			}
			
		}
		
		return $array;
	}
	
//=======================================================================================================================================================	
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'consultarUnidadesCco':
		{	
			$data = consultarUnidadesCco($conex, $wbasedatoMovhos);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarEspecialidades':
		{	
			$data = consultarEspecialidades($conex, $wbasedatoMovhos);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarInformacionReporte':
		{	
			$data = consultarInformacionReporte($conex, $wbasedatoMovhos, $wbasedatoHCE, $wbasedatoCliame, $wemp_pmla, $fechaInicial, $fechaFinal, $cco, $especialidad, $historia, $ingreso);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarDetalleGestion':
		{	
			$data = consultarDetalleBitacoraGestion($conex, $wbasedatoCliame, $wemp_pmla, $historia, $ingreso, $tipoOrden, $nroOrden, $item);
			echo json_encode($data);
			break;
			return;
		}
		
	}
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	?>
	<html>
	<head>
	  <title>Seguimiento gesti&oacute;n de autorizaciones</title>
	  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>
	
	<script src="../../../include/root/jquery.min.js"></script>
	<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
	<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
	
	
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
	<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
	<link rel='stylesheet' href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css'>
	<script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
		

	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" >
    <link rel="stylesheet" href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">
    
    <script   src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js" ></script>
	

	<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
	
	
	<!-- Bootstrap -->
	<link href="../../../include/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Font Awesome -->
	<link href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	
	
	<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

	<script src="../../../include/root/bootstrap.min.js"></script>
	
	
	<!-- Bootstrap -->
	<script src="../../../include/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>

	
	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
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
	
	$(document).ready(function() {
		
		pintarFiltros();
		generarReporte();
	});
	
	function pintarFiltros()
	{
		inicializarDatepicker();
		unidadesCco = consultarUnidadesCco();
		pintarUnidadesCco(unidadesCco);
		especialidades = consultarEspecialidades();
		pintarEspecialidades(especialidades);
		inicializarMultiselect();
	}
	
	function inicializarDatepicker()
	{
		fechaActual = $("#fechaActual").val();
		$("#txtFechaInicial").val(fechaActual);
		$("#txtFechaFinal").val(fechaActual);
		
		var dateFechaActual = new Date(fechaActual+" 00:00:00");

		$("#txtFechaInicial").datepicker({
		
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			maxDate: fechaActual,
			onSelect: function(fechaInicial){
				
				var fecIni = fechaInicial.split( "-" );
				
				var yearIni = fecIni[0];
				var monthIni = fecIni[1]-1;
				var dateIni = fecIni[2];
				
				var fecha_inicial = new Date(yearIni,monthIni,dateIni);
				var fechaFinal =  new Date(fecha_inicial.getFullYear(), fecha_inicial.getMonth() +1, fecha_inicial.getDate());
				
				if(fechaFinal>dateFechaActual)
				{
					fechaFinal = dateFechaActual;
				}
				
				$("#txtFechaFinal").datepicker("option", "minDate", fechaInicial);//bloquear que no esconjan en la fecha final una fecha antes de la del inicio
				$("#txtFechaFinal").datepicker("option", "maxDate", fechaFinal);//bloquear que no esconjan en la fecha final una fecha antes de la del inicio
			}
		});
		
		$("#txtFechaFinal").datepicker({
		
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			maxDate: fechaActual,
			onSelect: function(fechaFinal){
				
				var fecFin = fechaFinal.split( "-" );
				
				var yearFin = fecFin[0];
				var monthFin = fecFin[1]-1;
				var dateFin = fecFin[2];
				
				var fecha_final = new Date(yearFin,monthFin,dateFin);
				var fechaInicial =  new Date(fecha_final.getFullYear(), fecha_final.getMonth()-1, fecha_final.getDate());
				
				$("#txtFechaInicial").datepicker("option", "minDate", fechaInicial);//bloquear que no esconjan en la fecha final una fecha antes de la del inicio
				$("#txtFechaInicial").datepicker("option", "maxDate", fechaFinal);//bloquear que no esconjan en la fecha final una fecha antes de la del inicio
			}
		});
	}
	
	function pintarUnidadesCco(unidadesCco)
	{
		for(unidades in unidadesCco)
		{
			htmlCco = "<optgroup label="+unidadesCco[unidades].descripcionUnidad+">";
			for(ccos in unidadesCco[unidades].ccosUnidad)
			{
				htmlCco += "<option value='"+unidadesCco[unidades].ccosUnidad[ccos].codigoCco+"' unidad='"+unidades+"'> "+unidadesCco[unidades].ccosUnidad[ccos].descripcionCco+"</option>";
			}
			htmlCco += "</optgroup>";
			$("#cco").append(htmlCco);
		}
	}
	
	function pintarEspecialidades(especialidades)
	{
		for(especialidad in especialidades)
		{
			htmlEspecialidades = "<option value='"+especialidad+"'> "+especialidades[especialidad]+"</option>";
			
			$("#especialidad").append(htmlEspecialidades);
		}
	}
	
	function inicializarMultiselect()
	{
		$('#cco').multiselect({
			checkAllText : 'Todos',
			uncheckAllText : 'Ninguno',
			selectedText: "# de # seleccionados",
			beforeclose: function(event, ui) { 
			},
			menuHeight: 600,
        }).multiselectfilter();
		$("#cco").multiselect("refresh");
		$("#cco").multiselect("checkAll");
		
		$('#especialidad').multiselect({
			checkAllText : 'Todos',
			uncheckAllText : 'Ninguno',
			selectedText: "# de # seleccionados",
			beforeclose: function(event, ui) { 
			},
			menuHeight: 600,
        }).multiselectfilter();
		$("#especialidad").multiselect("refresh");
	}
	
	function consultarUnidadesCco()
	{
		var unidadesCco = {};
		$.ajax({
			url: "reporteSeguimientoArkadia.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarUnidadesCco',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoMovhos	: $('#wbasedatoMovhos').val(),
				},
				async: false,
				success:function(servicios) {
					unidadesCco = servicios;
				}
		});
		
		return unidadesCco;
	}
	
	function consultarEspecialidades()
	{
		var especialidades = {};
		$.ajax({
			url: "reporteSeguimientoArkadia.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarEspecialidades',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoMovhos	: $('#wbasedatoMovhos').val(),
				},
				async: false,
				success:function(resultado) {
					especialidades = resultado;
				}
		});
		
		return especialidades;
	}
	
	function consultarInformacionReporte()
	{
		$("#divMensajeEspere").modal("show");
		$.ajax({
			url: "reporteSeguimientoArkadia.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarInformacionReporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoHCE	: $('#wbasedatoHCE').val(),
				wbasedatoMovhos	: $('#wbasedatoMovhos').val(),
				wbasedatoCliame	: $('#wbasedatoCliame').val(),
				fechaInicial	: $('#txtFechaInicial').val(),
				fechaFinal		: $('#txtFechaFinal').val(),
				cco				: $('#cco').val(),
				especialidad	: $('#especialidad').val(),
				historia		: $('#txtHistoria').val(),
				ingreso			: $('#txtIngreso').val(),
				},
				async: false,
				success:function(resultado) {
					pacientesAtendidos = resultado;
					pintarPacientesAtendidos(pacientesAtendidos);
				}
		});
	}
	
	function pintarPacientesAtendidos(pacientesAtendidos)
	{
		fila_lista = "Fila1";
		html = 	"<thead>"+
					"<tr class='EncabezadoTabla' style='align:center;'>"+
						"<td style='text-align:center;vertical-align:middle;'>Fecha de la<br>admisi&oacute;n</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Hora de la<br>admisi&oacute;n</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Historia</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Nombre del paciente</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Entidad responsable</td>"+
						"<td style='text-align:center;vertical-align:middle;'>M&eacute;dico tratante</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Especialidad</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Servicio de origen</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Fecha en la<br>que se genera<br>la orden</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Hora en la<br>que se genera<br>la orden</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Tipo de orden</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Ordenamiento</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Fecha inicio de<br>gesti&oacute;n de<br>autorizaci&oacute;n</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Fecha final de<br>gesti&oacute;n de<br>autorizaci&oacute;n</td>"+
						"<td style='text-align:center;vertical-align:middle;'>N&uacute;mero<br>gestiones<br>realizadas</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Estado de la autorizaci&oacute;n</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Motivo de 'No programado'</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Estado del<br>proceso</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Fecha de asignaci&oacute;n<br>del procedimiento<br>o cita ordenado</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Hora de asignaci&oacute;n<br>del procedimiento<br>o cita ordenado</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Fecha de programaci&oacute;n<br>del procedimiento<br>o cita ordenado</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Hora de programaci&oacute;n<br>del procedimiento<br>o cita ordenado</td>"+
						"<td style='text-align:center;vertical-align:middle;'>Asisti&oacute;<br>a la cita</td>"+
					"</tr>"+
				"</thead>";
		
		var resumen = {	'pacientes':{},
						'ordenes':{},
						'gestiones':{},
					};
		if(Object.keys(pacientesAtendidos).length > 0)
		{
			for(paciente in pacientesAtendidos)
			{
				if(resumen.pacientes[pacientesAtendidos[paciente].descripcionCcoIngreso]==undefined)
				{
					resumen.pacientes[pacientesAtendidos[paciente].descripcionCcoIngreso] = 0;
				}
				
				resumen.pacientes[pacientesAtendidos[paciente].descripcionCcoIngreso] += 1;
				
				if (fila_lista=='Fila1')
					fila_lista = "Fila2";
				else
					fila_lista = "Fila1";
				
				var rowspanPaciente = 1;
				if(pacientesAtendidos[paciente].cantidadTotalOrdenes>0)
				{
					rowspanPaciente = pacientesAtendidos[paciente].cantidadTotalOrdenes;
				}
				
				html += "<tbody class='find'>"+
							"<tr>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;text-align:center;'>"+pacientesAtendidos[paciente].fechaIngreso+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;text-align:center;'>"+pacientesAtendidos[paciente].horaIngreso+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;text-align:center;'>"+pacientesAtendidos[paciente].historia+"-"+pacientesAtendidos[paciente].ingreso+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].paciente+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].descripcionResponsable+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].medicoIngreso+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].descripcionEspecialidad+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanPaciente+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].descripcionCcoIngreso+"</td>";
				
				if(pacientesAtendidos[paciente].cantidadTotalOrdenes>0)
				{
					for(orden in pacientesAtendidos[paciente].ordenes)
					{
						var rowspanTipoOrden = 1;
						if(pacientesAtendidos[paciente].ordenes[orden].cantidadOrdenes>0)
						{
							rowspanTipoOrden = pacientesAtendidos[paciente].ordenes[orden].cantidadOrdenes;
						}
						
						html += "<td class='"+fila_lista+"' rowspan='"+rowspanTipoOrden+"' style='vertical-align:middle;text-align:center;'>"+pacientesAtendidos[paciente].ordenes[orden].fechaOrden+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanTipoOrden+"' style='vertical-align:middle;text-align:center;'>"+pacientesAtendidos[paciente].ordenes[orden].horaOrden+"</td>"+
								"<td class='"+fila_lista+"' rowspan='"+rowspanTipoOrden+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].desTipoOrden+"</td>";
						
						var cantProcedimientos = 1;
						for(procedimiento in pacientesAtendidos[paciente].ordenes[orden].detalleOrden)
						{
							if(pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion!==undefined)
							{
								if(resumen.ordenes[pacientesAtendidos[paciente].ordenes[orden].desTipoOrden]==undefined)
								{
									resumen.ordenes[pacientesAtendidos[paciente].ordenes[orden].desTipoOrden] = 0;
								}
								
								resumen.ordenes[pacientesAtendidos[paciente].ordenes[orden].desTipoOrden] += 1;
								
								for(detalleGestion in pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.gestiones)
								{
									if(resumen.gestiones[pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.gestiones[detalleGestion].nombreUsuario]==undefined)
									{
										resumen.gestiones[pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.gestiones[detalleGestion].nombreUsuario] = 0;
									}
									
									resumen.gestiones[pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.gestiones[detalleGestion].nombreUsuario] += 1;
								}
								
								var fechaInicioGestionAutorizacion = pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.fechaInicioGestionAutorizacion;
								if(fechaInicioGestionAutorizacion==null)
								{
									fechaInicioGestionAutorizacion = "";
								}
								
								var fechaFinGestionAutorizacion = pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.fechaFinGestionAutorizacion;
								if(fechaFinGestionAutorizacion==null)
								{
									fechaFinGestionAutorizacion = "";
								}
								
								var cantidadGestiones = pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.cantidadGestiones;
								
								if(cantidadGestiones==0)
								{
									cantidadGestiones = "";
								}
									
								if(cantProcedimientos>1)
								{
									html += "<tr>";
								}
								
								var detalleGestion = "onclick='verDetalleGestion(\""+pacientesAtendidos[paciente].historia+"\",\""+pacientesAtendidos[paciente].ingreso+"\",\""+pacientesAtendidos[paciente].ordenes[orden].tipoOrden+"\",\""+pacientesAtendidos[paciente].ordenes[orden].nroOrden+"\",\""+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].item+"\")'";
								
								html += "<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].desProcedimiento+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;text-align:center;'>"+fechaInicioGestionAutorizacion+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;text-align:center;'>"+fechaFinGestionAutorizacion+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;text-align:center;cursor:pointer;' title='ver detalle' "+detalleGestion+">"+cantidadGestiones+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.estadoAutorizacion+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.estadoNoProgramado+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.estadoProceso+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.fechaProgramacionCita+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.horaProgramacionCita+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.fechaRealizacionCita+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.horaRealizacionCita+"</td>"+
										"<td class='"+fila_lista+"' style='vertical-align:middle;'>"+pacientesAtendidos[paciente].ordenes[orden].detalleOrden[procedimiento].gestion.asistida+"</td>";
										
										cantProcedimientos += 1;
							}
						}
						html += 	"</tr>";
					}
					html += 	"</tbody>";
				}
				else
				{
					html += "<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>"+
							"<td class='"+fila_lista+"' style='vertical-align:middle;'></td>";
				html += "</tr>"+
						"</tbody>";
				}
			}
			console.log(resumen)
			pintarResumenReporte(resumen);
		}
		else
		{
			html += "<tbody>"+
						"<tr style='align:center;'>"+
							"<td class='"+fila_lista+"' style='text-align:center;vertical-align:middle;' colspan=23>No se encontraron resultados con los filtros de b&uacute;squeda seleccionados.</td>"+
						"</tr>"+
					"</tbody>";
		}
		
		
		  
		$(".fechaHoraGeneracion").html(obtenerFechaHora());
		$("#tablePacientesAtendidos").html(html);
		$('#buscar').quicksearch('#tablePacientesAtendidos .find');
		$("#divMensajeEspere").modal("hide");
	}
	
	function pintarResumenReporte(resumen)
	{
		
		var html =  "<thead>"+
						"<tr class='EncabezadoTabla'>"+
							"<td colspan=4 style='text-align:center'>Resumen</td>"+
						"</tr>"+
						"<tr class='EncabezadoTabla' style='text-align:center'>"+
							"<td colspan=2>Descripci&oacute;n</td>"+
							"<td>Cantidad</td>"+
							"<td>Total</td>"+
						"</tr>"+
					"</thead>"+
					"<tbody>";
						
						cantidadCco = Object.keys(resumen.pacientes).length;						
						var primerRegistro = true;
						var totalPacientes = 0;
						for(cco in resumen.pacientes)
						{
			html +=  		"<tr class='fila1'>";
							if(primerRegistro)
							{
			html +=  			"<td rowspan="+cantidadCco+" style='vertical-align:middle;'>PACIENTES ATENDIDOS</td>";				
							}
								
			html +=  			"<td>"+cco+"</td>"+
								"<td style='text-align:center;vertical-align:middle;'>"+resumen.pacientes[cco]+"</td>";
							if(primerRegistro)
							{
			html +=  			"<td id='totalPacientes' rowspan="+cantidadCco+" style='text-align:center;vertical-align:middle;'></td>";
								primerRegistro = false;
							}
			html +=  		"</tr>";
								
							totalPacientes += resumen.pacientes[cco];
						}
						
						cantidadOrdenes = Object.keys(resumen.ordenes).length;			
						var primerRegistro = true;
						var totalOrdenes = 0;
						for(tipoOrden in resumen.ordenes)
						{
			html +=  		"<tr class='fila2'>";
							if(primerRegistro)
							{
			html +=  			"<td rowspan="+cantidadOrdenes+" style='vertical-align:middle;'>ORDENES PRESCRITAS</td>";				
							}
								
			html +=  			"<td>"+tipoOrden+"</td>"+
								"<td style='text-align:center;vertical-align:middle;'>"+resumen.ordenes[tipoOrden]+"</td>";
							if(primerRegistro)
							{
			html +=  			"<td id='totalOrdenes' rowspan="+cantidadOrdenes+" style='text-align:center;vertical-align:middle;'></td>";
									primerRegistro = false;
							}
			html +=  		"</tr>";
			
							totalOrdenes += resumen.ordenes[tipoOrden];	
						}
						
						cantidadGestores = Object.keys(resumen.gestiones).length;		
						var primerRegistro = true;
						var totalGestores = 0;
						for(gestor in resumen.gestiones)
						{
			html +=  		"<tr class='fila1'>";
							if(primerRegistro)
							{
			html +=  			"<td rowspan="+cantidadGestores+" style='vertical-align:middle;'>GESTIONES REALIZADAS</td>";				
							}
								
			html +=  			"<td>"+gestor+"</td>"+
								"<td style='text-align:center;vertical-align:middle;'>"+resumen.gestiones[gestor]+"</td>";
							if(primerRegistro)
							{
			html +=  			"<td id='totalGestores' rowspan="+cantidadGestores+"  style='text-align:center;vertical-align:middle;'></td>";
									primerRegistro = false;
							}
			html +=  		"</tr>";
							totalGestores += resumen.gestiones[gestor];	
						}
			html += "</tbody>";
			
			
		$("#tableResumen").html(html);
		$("#totalPacientes").html(totalPacientes);
		$("#totalOrdenes").html(totalOrdenes);
		$("#totalGestores").html(totalGestores);
		$("#tableResumen").show();
	}
	function generarReporte()
	{
		// validar que tenga cco seleccionados
		if($("#cco").val()!=null)
		{
			pacientesAtendidos = consultarInformacionReporte();
			$("#divSeguimiento").show();
		}
		else
		{
			// Mostrar alerta
			$("#mensajeAlerta").html("Debe seleccionar al menos un centro de costos");
			$("#divAlerta").modal("show");
			$("#divSeguimiento").hide();
		}
		
	}
	
	function obtenerFechaHora()
	{
		date = new Date(Date.now());
		year = date.getFullYear();
		month = date.getMonth()+1;
		dt = date.getDate();

		if(dt < 10) {
			dt = '0' + dt;
		}
		
		if(month < 10) {
			month = '0' + month;
		}

		hour = date.getHours();
		minute = date.getMinutes();
		second = date.getSeconds();
		
		if(hour < 10) {
			hour = '0' + hour;
		}
		
		if(minute < 10) {
			minute = '0' + minute;
		}
		if(second < 10) {
			second = '0' + second;
		}
		
		fechaHora = year+'-'+month+'-'+dt+' '+hour+':'+minute+':'+second;
		
		return fechaHora;
	}
	
	function verDetalleGestion(historia, ingreso, tipoOrden, nroOrden, item)
	{
		var detalleGestion = {};
		$.ajax({
			url: "reporteSeguimientoArkadia.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'consultarDetalleGestion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wbasedatoCliame	: $('#wbasedatoCliame').val(),
				historia		: historia,
				ingreso			: ingreso,
				tipoOrden		: tipoOrden,
				nroOrden		: nroOrden,
				item			: item,
				},
				async: false,
				success:function(resultado) {
					detalleGestion = resultado;
				}
		});
		
		pintarDetalleGestion(detalleGestion);
	}
	
	function pintarDetalleGestion(detalleGestion)
	{
		var html = 	"<table align='center' class='table table-bordered' style='width:80%'>"+
						"<tr class='EncabezadoTabla'>"+
							"<td colspan=4 style='text-align:center;'>Detalle</td>"+
						"</tr>"+
						"<tr class='EncabezadoTabla' style='text-align:center;'>"+
							"<td>Fecha</td>"+
							"<td>Hora</td>"+
							"<td>Usuario</td>"+
							"<td>Observacion</td>"+
						"</tr>";
		if(Object.keys(detalleGestion).length>0)
		{
			fila_lista = "Fila1";
			
			for(detalle in detalleGestion)
			{
				if (fila_lista=='Fila1')
					fila_lista = "Fila2";
				else
					fila_lista = "Fila1";
				
				html +=	"	<tr class='"+fila_lista+"'>"+
								"<td style='text-align:center;'>"+detalleGestion[detalle].fecha+"</td>"+
								"<td style='text-align:center;'>"+detalleGestion[detalle].hora+"</td>"+
								"<td>"+detalleGestion[detalle].nombreUsuario+"</td>"+
								"<td>"+detalleGestion[detalle].observacion+"</td>"+
							"</tr>";
			}
		}
		else
		{
			html +=	"	<tr class='"+fila_lista+"'>"+
							"<td colspan=4 style='text-align:center;'>No se encontraron resultados</td>"+
						"</tr>";
		}
			html+= 	"</table>";
		
		$("#divVerDetalleGestion").html(html);
		$("#modalDetalleGestion").modal("show");
	}
	
	function iniciarCampos()
	{
		$("#divSeguimiento").hide();
		
		inicializarDatepicker();
		
		$("#txtHistoria").val("");
		$("#txtIngreso").val("");
	}
	
	function cerrarVentana()
	{
		top.close();		
	}
	
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		body
		{
			width: auto;
			height: auto;
			background-color: #FFFFFF;
			color: #000000;
		}
		
		.ui-multiselect { 
            background:white; 
            color: gray; 
            font-weight: normal; 
            width:100%; 
			max-width: 100%;
			overflow-x:hidden; 
            border-radius: 1px;
        }
		.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header
		{
			font-weight: normal; 
		}
		.ui-corner-all
		{
			font-weight: normal; 
		}
		
		.fila1 {
			font-size: 8pt;
		}
		.fila2 {
			font-size: 8pt;
		}
		
		th
		{
			text-align:center;
			vertical-align:middle;
		}
		
		.panel-primary {
			border-color: #2A5DB0;
		}
		
		.panel-primary > .panel-heading {
			color: #fff;
			background-color: #2A5DB0;
			border-color: #2A5DB0;
		}
		
		.label-primary{
			background-color: #2A5DB0;
		}
		.btnMatrix{
			background-color: #2A5DB0;
			color: #FFFFFF;
		}
		
		.btnMatrix:hover {
			background-color: #234d90;
			color: #FFFFFF;
		}
		
		.modal-header {
			background-color: #2A5DB0;
			padding:1px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
		}
		
		.modal-Alerta {
			background-color: #2A5DB0;
			padding:16px 16px;
			color:#FFF;
			border-bottom:2px dashed #2A5DB0;
			font-weight: bold;
			font-size: 10pt;
		}
		
		.encabezadoTablaCentrado
		{
			text-align:center;
			vertical-align:middle;
		}
		
		.panel-body {
			padding: 8px;
		}
		
		#tablePacientesAtendidos{
			width:100%;
			border-collapse: separate;
		}
		
		#tablePacientesAtendidos thead {
			position: sticky;
			top: 0;
		}
		
		#tablePacientesAtendidos td{
			white-space: nowrap;
		}
		
		#tableResumen td{
			white-space: nowrap;
		}
		

	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body class="nav-md">
		<?php
		
		// -->	ENCABEZADO
		
		encabezado("Seguimiento gestion de autorizaciones", $wactualiz, "HCE".$wemp_pmla);
		
		?>
		
		<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
		<input type='hidden' id='wbasedatoHCE' name='wbasedatoHCE' value='<?php echo $wbasedatoHCE ?>'>
		<input type='hidden' id='wbasedatoMovhos' name='wbasedatoMovhos' value='<?php echo $wbasedatoMovhos ?>'>
		<input type='hidden' id='wbasedatoCliame' name='wbasedatoCliame' value='<?php echo $wbasedatoCliame ?>'>
		<input type='hidden' id='fechaActual' name='fechaActual' value='<?php echo date("Y-m-d") ?>'>
		
		
		<div class="container-fluid">
			<div id="divFiltros" class="col-lg-12 col-md-8 col-sm-8 col-xs-8 " style="text-align:center;">
				<div class="panel panel-primary">
					<div class="panel-heading">Consultar pacientes atendidos</div>
					<div class="panel-body">
						<form>
							<div class="col-lg-12">
								<div class="form-group col-lg-1" class="control-label" style="width:150px">
									<label for="labelFechaInicial" class="control-label">Fecha inicial</label>
									<input id='txtFechaInicial' class='form-control' type='text' value='<?php echo date("Y-m-d") ?>' fechaActual='<?php echo date("Y-m-d") ?>' style='height:32px' readOnly='readOnly'>
								</div>
								<div class="form-group col-lg-1" class="control-label"  style="width:150px">
									<label for="labelFechaFinal" class="control-label">Fecha final</label>
									<input id='txtFechaFinal' class='form-control' type='text' value='<?php echo date("Y-m-d") ?>' fechaActual='<?php echo date("Y-m-d") ?>'  style='height:32px' readOnly='readOnly'>
								</div>
								<div class="form-group col-lg-1" class="control-label"  style="width:150px">
									<label for="labelHistoria" class="control-label">Historia</label>
									<input id='txtHistoria' class='form-control' type='text' style='height:32px'>
								</div>
								<div class="form-group col-lg-1" class="control-label"  style="width:100px">
									<label for="labelIngreso" class="control-label">Ingreso</label>
									<input id='txtIngreso' class='form-control' type='text' style='height:32px;'>
								</div>
								<div class="form-group col-lg-3" style="width:300px">
									<label for="labelCco" class="control-label">Servicio</label>
									<select id="cco" class="form-control" multiple="multiple">
									</select>
								</div>
								<div class="form-group col-lg-3" style="width:300px">
									<label for="labelEspecialidad" class="control-label">Especialidad</label>
									<select id="especialidad" class="form-control" multiple="multiple">
									</select>
								</div>
								<div class="form-group col-lg-2" style="top:17px;width:280px">
									<button type="button" id="btnConsultar" class="btn btnMatrix" onclick="generarReporte();">Consultar</button>
									<button type="button" id="btnIniciar" class="btn btn-default" onclick="iniciarCampos();">Iniciar</button>
									<button type="button" id="btnCerrar" class="btn btn-default" onclick="cerrarVentana();">Cerrar</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			
			<div id="divSeguimiento" class="col-md-12 col-sm-12 col-xs-12" style="display:none;">
				<p align='center' style='font-family: verdana;font-weight:bold;font-size: 10pt;'>&nbsp;Reporte generado:&nbsp;&nbsp;<span class='fechaHoraGeneracion' style='font-weight:normal'></span></p>
				<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
					&nbsp;Buscar:&nbsp;&nbsp;<input id='buscar' type='text' style='border-radius: 4px;border:1px solid #AFAFAF;font-weight:normal'>
				</span>
				<br>
				<table id="tablePacientesAtendidos" class='table table-striped table-bordered'>
				</table>
				<p align='center' style='font-family: verdana;font-weight:bold;font-size: 10pt;'>&nbsp;Reporte generado:&nbsp;&nbsp;<span class='fechaHoraGeneracion' style='font-weight:normal'></span></p>
				
				<table id="tableResumen" class='table table-bordered' style='display:none;width:20%;' align='center'>
					
					
				</table>
			</div>
			
			<div id='divAlerta' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm'>
					<div class='modal-content'>
						<div class='modal-Alerta'>ALERTA</div>
						<div class='modal-body' id='mensajeAlerta'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			
			<div id='divMensajeEspere' class='modal fade bs-example-modal-sm' role='dialog'>
				<div class='modal-dialog modal-sm' style='display: block;'>
					<div class='modal-content'>
						<div class='modal-body' id='mensajeEspere'><br><p align='center'><img src='../../images/medical/ajax-loader5.gif'/>&nbsp;&nbsp;&nbsp;Por favor espere un momento...</p><br></div>
					</div>
				</div>
			</div>
			
			<div id='modalDetalleGestion' class='modal fade bs-example-modal-lg' role='dialog'>
				<div class='modal-dialog modal-lg' style='display: block;'>
					<div class='modal-content'>
						<div class='modal-Alerta'>
							Detalle gesti&oacute;n
							<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#FFFFFF; font-weight:bold;">
							 &times;
							</button>
						</div>
						
						<div class='modal-body' id='divVerDetalleGestion' style='overflow: auto;'></div>
						<div class='modal-footer'>
							<button type='button' class='btn btnMatrix' data-dismiss='modal'>Cerrar</button>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<!-- /page content -->

	</body>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
