<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Lista los insumos y habitaciones con pedidos de insumos.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-06-30
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2017-12-04';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2017-12-04 - Jessica Madrid Mejía: 	- Se modifica el filtro  Mtrcua!='on' para evitar que no se muestren los pacientes en caso de que 
// 										  este campo tenga NO APLICA.
//  2017-12-01 - Jessica Madrid Mejía: 	- Se quita el filtro Mtrsal!='' para que en la opcion de pacientes de urgencias sin ubicación muestre 
// 										  los pacientes sin cubiculo sin importar si tienen sala o no, esta modificación se hace para 
// 										  que puedan visualizar los pacientes de ortopedia.
//  2017-10-12 - Jessica Madrid Mejía: 	- Se agrega el filtro Mtrsal!='' para que en la opcion de pacientes de urgencias sin ubicación solo 
// 										  muestre los pacientes que ya fueron atendidos, tienen sala y no tienen cubiculo.
// 	2017-10-11 - Jessica Madrid Mejía:	- Se tienen en cuenta los pacientes de urgencias sin ubicación y los pacientes de ayudas diagnosticas.
// 										- Se quita el filtro Activo='A' en las consultas de la tabla de usuarios para que siempre muestre 
// 										el responsable del movimiento sin importar si está inactivo. 
// 	2017-07-26 - Jessica Madrid Mejía:	Se agrega filtro con movhos_000020 en la funcion pintarDetalleInsumo().
//
//--------------------------------------------------------------------------------------------------------------------------------------------


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
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");
	
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");

	include_once("movhos/botiquin.inc.php");

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	
	function consultarCentrosDeCosto()
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;

		$ccoHabilitados	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );
		
		$ccoHabilitados = explode(",",$ccoHabilitados);
		
		
		$queryCCo = " SELECT Ccocod,Cconom,Ccohos,Ccourg,Ccoayu 
						FROM ".$wbasedato."_000011 
					   WHERE (Ccohos='on' OR Ccourg='on' OR Ccoayu='on') 
					     AND Ccoest='on' 
					ORDER BY Ccocod,Cconom;";
		
		$resCCo=  mysql_query($queryCCo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryCCo." - ".mysql_error());
		$numCCo = mysql_num_rows($resCCo);	
		
		$arrayCco = array();
		if($numCCo > 0)
		{
			while($rowCCo = mysql_fetch_array($resCCo))
			{
				$mostrarCco = true;
				if($rowCCo['Ccoayu']=="on")
				{
					$mostrarCco = consultarConfiguracionAyudaDiagnostica($conex,$wbasedato,$rowCCo['Ccocod']);
				}
				
				// if(in_array("*",$ccoHabilitados) || in_array(trim($rowCCo['Ccocod']),$ccoHabilitados))
				if((in_array("*",$ccoHabilitados) || in_array(trim($rowCCo['Ccocod']),$ccoHabilitados)) && $mostrarCco)
				{
					$arrayCco[trim($rowCCo['Ccocod'])] = trim($rowCCo['Cconom']);
					
				}
			}
		}
		
		return $arrayCco;			
	}
	
	function pintarFiltroCco()
	{
		$ccoDispensacion = consultarCentrosDeCosto();
		
		$filtroFecha = "";
		$filtroFecha .= "	<div id='divFiltroCco' align='center'>
								<table>
									<tr class='encabezadoTabla'>
										<td colspan='2' align='center'>Consultar pedidos por centro de costos</td>
									</tr>
									<tr id='trCco'>
										<td class='fila1'>
											<span style='font-size:10pt;'>Centro de costos:</span>
										</td>
										
										<td class='fila2' id='tdFiltroCco'>";
		$filtroFecha .= "					<select id='filtroCcoDispensacion' name='filtroCcoDispensacion' onChange='validarZona();'>
												<option value=''>Seleccione...</option>";
												foreach($ccoDispensacion as $keycco => $valuecco)
												{
		$filtroFecha .= "							<option value='".$keycco."'>".$keycco."-".$valuecco."</option>";												
												}
		$filtroFecha .= "					</select>";											
		$filtroFecha .= "				</td>
									</tr>
									<tr id='trZona' style='display:none;'>
										<td class='fila1'>
											<span style='font-size:10pt;'>Zona:</span>
										</td>
										<td class='fila2' id='tdFiltroZona'>
										</td>
									</tr>
								</table>
								
							</div>
							
							<br>";
		
		echo $filtroFecha;
	}

	function consultarZona($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$queryZonas ="SELECT Habzon,Aredes 
						FROM ".$wbasedato."_000020,".$wbasedato."_000169 
					   WHERE Habcco='".$codigoCco."' 
						 AND Habest='on' 
						 AND Habzon=Arecod
						 AND Areest='on' 
					GROUP BY Habzon;";
		
		$resZonas = mysql_query($queryZonas, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryZonas . " - " . mysql_error());		   
		$numZonas = mysql_num_rows($resZonas);
		
		$zonas = array();
		if($numZonas>0)
		{
			while($rowZonas = mysql_fetch_array($resZonas))
			{
				$zonas[$rowZonas['Habzon']] = $rowZonas['Aredes'];
			}
		}
		
		return $zonas;
	}
	
	function pintarZona($codigoCco)
	{
		global $conex;
		global $wbasedato;
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		$opcionUrgPacSinUbicacion = "";
		if($ccoTipo=="urgencias")
		{
			$opcionUrgPacSinUbicacion = "<option value='SinUbicacion'>SIN UBICACI&Oacute;N</option>";
		}
		
		$zonas = consultarZona($codigoCco);
		
		$html = "";
		
		if(count($zonas)>0)
		{
			$html .= "<select id='filtroZonas' name='filtroZonas' onChange='seleccionarCco();'>
						<option value=''>Seleccione...</option>
						<option value='0'>TODAS</option>"
						.$opcionUrgPacSinUbicacion;
						foreach($zonas as $keyZona => $valueZona)
						{
			$html .= "		<option value='".$keyZona."'>".$valueZona."</option>";												
						}
			$html .= "</select>";
		}
		
		
		return $html;
	}
	
	function consultarPacientesPedido($codigoCco,$zona,$mostrarTodos)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoHce;
		global $wemp_pmla;
		
		$condicionMostrarTodos = "AND Pedent='off' ";
		if($mostrarTodos=="on")
		{
			
			$fecha = date( "Y-m-d", time()-24*3600 );
			$hora = date( "H:i:s", time()-24*3600 );
			
			$condicionMostrarTodos = "AND (Pedent='off' OR (Pedent='on' AND ( a.Fecha_data > '".$fecha."' OR (a.Fecha_data >= '".$fecha."' AND a.Hora_data >= '".$hora."' ))))";
		}
		
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		if($ccoTipo=="ayudaDiagnostica")
		{
			$queryPacientes ="SELECT Dpehis,Dpeing,Dpehab,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,CONCAT_WS('-',Dpehis,Dpeing) AS Habcod,CONCAT_WS('-',Dpehis,Dpeing) AS Habcpa,Pedent 
								FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000018,root_000037,root_000036
							   WHERE Pedcco='".$codigoCco."' 
								 ".$condicionMostrarTodos."
								 AND Pedest='on'
								 AND Dpecod=Pedcod
								 AND Dpeped>0
								 AND Dpeest='on'
								 AND Ubihis=Dpehis
							     AND Ubiing=Dpeing
							     AND Ubiald!='on'
								 AND Orihis=Dpehis
								 AND Oriing=Dpeing
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
							GROUP BY Dpehis,Dpeing,Dpehab
							ORDER BY Habcod;";
		}
		else
		{
			$condicionZona = "";
			if($zona!="" && $zona!="0")
			{
				$condicionZona = "AND Habzon='".$zona."'";
			}
			
		
			$queryUrgPacSinUbicacion = "";
			if($ccoTipo=="urgencias" && ($zona=="0" || $zona=="SinUbicacion"))
			{
				$queryUrgPacSinUbicacion = "   UNION
				
											  SELECT Dpehis,Dpeing,Dpehab,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,CONCAT_WS('-',Dpehis,Dpeing) AS Habcod,CONCAT_WS('-',Dpehis,Dpeing) AS Habcpa,Pedent 
												FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000018,root_000037,root_000036,".$wbasedatoHce."_000022
											   WHERE Pedcco='".$codigoCco."' 
												 ".$condicionMostrarTodos."
												 AND Pedest='on'
												 AND Dpecod=Pedcod
												 AND Dpeped>0
												 AND Dpeest='on'
												 AND Ubihis=Dpehis
												 AND Ubiing=Dpeing
												 AND Ubisac=Pedcco
												 AND Orihis=Dpehis
												 AND Oriing=Dpeing
												 AND Oriori='".$wemp_pmla."'
												 AND Pacced=Oriced
												 AND Pactid=Oritid
												 AND Mtrhis=Ubihis
												 AND Mtring=Ubiing
												 AND Mtrcua!='on'
											GROUP BY Dpehis,Dpeing,Dpehab";
			}
			
			$queryPacientes ="SELECT Dpehis,Dpeing,Dpehab,Pacno1,Pacno2,Pacap1,Pacap2,Pacced,Pactid,Habcod,Habcpa,Pedent 
								FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000020,root_000037,root_000036
							   WHERE Pedcco='".$codigoCco."' 
								 ".$condicionMostrarTodos."
								 AND Pedest='on'
								 AND Dpecod=Pedcod
								 AND Dpeped>0
								 AND Dpeest='on'
								 AND Habhis=Dpehis
								 AND Habing=Dpeing
								 AND Habcco=Pedcco
								 ".$condicionZona."
								 AND Orihis=Dpehis
								 AND Oriing=Dpeing
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
							GROUP BY Dpehis,Dpeing,Dpehab
							
							".$queryUrgPacSinUbicacion."
							
							ORDER BY Habcod;";
		}
		
		// echo "<pre>".print_r($queryPacientes,true)."</pre>";
		
		$resPacientes = mysql_query($queryPacientes, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPacientes . " - " . mysql_error());		   
		$numPacientes = mysql_num_rows($resPacientes);
		
		$pacientes = array();
		if($numPacientes>0)
		{
			while($rowPacientes = mysql_fetch_array($resPacientes))
			{
				$habitacion = trim($rowPacientes['Habcpa']);
				if(trim($habitacion)=="")
				{
					$habitacion = trim($rowPacientes['Habcod']);
				}
				
				$pacientes[$habitacion]['historia'] = $rowPacientes['Dpehis'];
				$pacientes[$habitacion]['ingreso'] = $rowPacientes['Dpeing'];
				$pacientes[$habitacion]['habitacion'] = $habitacion;
				$pacientes[$habitacion]['codHabitacion'] = trim($rowPacientes['Habcod']);
				// $pacientes[$habitacion]['descHabitacion'] = $rowPacientes['Habcpa'];
				$pacientes[$habitacion]['nombre'] = $rowPacientes['Pacno1']." ".$rowPacientes['Pacno2']." ".$rowPacientes['Pacap1']." ".$rowPacientes['Pacap2'];
				$pacientes[$habitacion]['tipoDocumento'] = $rowPacientes['Pacced'];
				$pacientes[$habitacion]['documento'] = $rowPacientes['Pactid'];
				$pacientes[$habitacion]['entregado'] = $rowPacientes['Pedent'];
			}
		}
		
		return $pacientes;
		
	}
	
	function consultarInsumosPedido($codigoCco,$zona,$mostrarTodos)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoHce;
		global $wemp_pmla;
		
		
		
		$condicionMostrarTodos = "AND Pedent='off' ";
		if($mostrarTodos=="on")
		{
			
			$fecha = date( "Y-m-d", time()-24*3600 );
			$hora = date( "H:i:s", time()-24*3600 );
			
			$condicionMostrarTodos = "AND (Pedent='off' OR (Pedent='on' AND ( a.Fecha_data > '".$fecha."' OR (a.Fecha_data >= '".$fecha."' AND a.Hora_data >= '".$hora."' ))))";
		}
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		
		if($ccoTipo=="ayudaDiagnostica")
		{
			$queryInsumos =" SELECT Dpeins,SUM(Dpeped) as cantidad,Artgen,Artuni,Unides,Pedent
							   FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000018,".$wbasedato."_000026,".$wbasedato."_000027
							  WHERE Pedcco='".$codigoCco."' 
								".$condicionMostrarTodos." 
								AND Pedest='on'
								AND Dpecod=Pedcod
								AND Dpeped>0
								AND Dpeest='on'
								AND Ubihis=Dpehis
							    AND Ubiing=Dpeing
							    AND Ubiald!='on'
								AND Artcod=Dpeins
								AND Artest='on'
								AND Unicod=Artuni
								AND Uniest='on'
						   GROUP BY Dpeins
						   ORDER BY Artgen;";
		}
		else
		{
			$condicionZona = "";
			if($zona!="" && $zona!="0")
			{
				$condicionZona = "AND Habzon='".$zona."'";
			}
			
			$queryUrgPacSinUbicacion = "";
			if($ccoTipo=="urgencias" && ($zona=="0" || $zona=="SinUbicacion"))
			{
				$queryUrgPacSinUbicacion = "  UNION ALL
				
											 SELECT Dpeins,SUM(Dpeped) as cantidad,Artgen,Artuni,Unides,Pedent
											   FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000018,".$wbasedato."_000026,".$wbasedato."_000027,".$wbasedatoHce."_000022
											  WHERE Pedcco='".$codigoCco."' 
												".$condicionMostrarTodos." 
												AND Pedest='on'
												AND Dpecod=Pedcod
												AND Dpeped>0
												AND Dpeest='on'
												AND Ubihis=Dpehis
												AND Ubiing=Dpeing
												AND Ubisac=Pedcco
												AND Artcod=Dpeins
												AND Artest='on'
												AND Unicod=Artuni
												AND Uniest='on'
												AND Mtrhis=Ubihis
												AND Mtring=Ubiing
												AND Mtrcua!='on'
										   GROUP BY Dpeins";
			}
			
			$queryInsumos =" SELECT Dpeins,SUM(Dpeped) as cantidad,Artgen,Artuni,Unides,Pedent
							   FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000020,".$wbasedato."_000026,".$wbasedato."_000027
							  WHERE Pedcco='".$codigoCco."' 
								".$condicionMostrarTodos." 
								AND Pedest='on'
								AND Dpecod=Pedcod
								AND Dpeped>0
								AND Dpeest='on'
								AND Habhis=Dpehis
								AND Habing=Dpeing
								AND Habcco=Pedcco
								".$condicionZona."
								AND Artcod=Dpeins
								AND Artest='on'
								AND Unicod=Artuni
								AND Uniest='on'
						   GROUP BY Dpeins
						   ".$queryUrgPacSinUbicacion."
						   ORDER BY Artgen;";
		}
		
		
		// echo "<pre>".print_r($queryInsumos,true)."</pre>";
		
		
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());		   
		$numInsumos = mysql_num_rows($resInsumos);
		
		$insumos = array();
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				$insumos[$rowInsumos['Dpeins']]['codigo'] = $rowInsumos['Dpeins'];
				$insumos[$rowInsumos['Dpeins']]['cantidad'] += $rowInsumos['cantidad'];
				$insumos[$rowInsumos['Dpeins']]['descripcion'] = $rowInsumos['Artgen'];
				$insumos[$rowInsumos['Dpeins']]['codUnidad'] = $rowInsumos['Artuni'];
				$insumos[$rowInsumos['Dpeins']]['desUnidad'] = $rowInsumos['Unides'];
				$insumos[$rowInsumos['Dpeins']]['entregado'] = $rowInsumos['Pedent'];
			}
		}
		// var_dump($insumos);
		return $insumos;
		
	}
	
	function consultarCantidadesPedido($codigoCco,$zona,$mostrarTodos)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoHce;
		global $wemp_pmla;
		
		
		$condicionMostrarTodos = "AND Pedent='off' ";
		if($mostrarTodos=="on")
		{
			
			$fecha = date( "Y-m-d", time()-24*3600 );
			$hora = date( "H:i:s", time()-24*3600 );
			
			$condicionMostrarTodos = "AND (Pedent='off' OR (Pedent='on' AND ( a.Fecha_data > '".$fecha."' OR (a.Fecha_data >= '".$fecha."' AND a.Hora_data >= '".$hora."' ))))";
		}
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		if($ccoTipo == "ayudaDiagnostica")
		{
			$queryInsumos =" SELECT Dpehis,Dpeing,Dpehab,Dpeins,Dpeped
							   FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000018
							  WHERE Pedcco='".$codigoCco."' 
								".$condicionMostrarTodos." 
								AND Pedest='on'
								AND Dpecod=Pedcod
								AND Dpeped>0
								AND Dpeest='on'
								AND Ubihis=Dpehis
								AND Ubiing=Dpeing
								AND Ubiald!='on'
								
						   ORDER BY Dpeins;";
		}
		else
		{
			$condicionZona = "";
			if($zona!="" && $zona!="SinUbicacion" && $zona!="0")
			{
				$condicionZona = "AND Habzon='".$zona."'";
			}
			
			$queryUrgPacSinUbicacion = "";
			if($ccoTipo=="urgencias" && ($zona=="0" || $zona=="SinUbicacion"))
			{
				if($zona=="0")
				{
					$queryUrgPacSinUbicacion .= "  UNION ALL ";
				}
				
				$queryUrgPacSinUbicacion .= "SELECT Dpehis,Dpeing,CONCAT_WS('-',Dpehis,Dpeing) AS Dpehab,Dpeins,Dpeped
											   FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000018,".$wbasedatoHce."_000022
											  WHERE Pedcco='".$codigoCco."' 
												".$condicionMostrarTodos." 
												AND Pedest='on'
												AND Dpecod=Pedcod
												AND Dpeped>0
												AND Dpeest='on'
												AND Ubihis=Dpehis
												AND Ubiing=Dpeing
												AND Ubisac=Pedcco
												AND Mtrhis=Ubihis
												AND Mtring=Ubiing
												AND Mtrcua!='on'";
			}
			
			if($ccoTipo=="urgencias" && $zona=="SinUbicacion")
			{
				$queryInsumos = $queryUrgPacSinUbicacion;
			}
			else
			{
				$queryInsumos =" SELECT Dpehis,Dpeing,Dpehab,Dpeins,Dpeped
								   FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,".$wbasedato."_000020
								  WHERE Pedcco='".$codigoCco."' 
									".$condicionMostrarTodos." 
									AND Pedest='on'
									AND Dpecod=Pedcod
									AND Dpeped>0
									AND Dpeest='on'
									AND Habhis=Dpehis
									AND Habing=Dpeing
									AND Habcco=Pedcco
									".$condicionZona."
									
									".$queryUrgPacSinUbicacion."
									
							   ORDER BY Dpeins;";
			}
			
		}
		
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());		   
		$numInsumos = mysql_num_rows($resInsumos);
		
		$insumos = array();
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				$insumos[$rowInsumos['Dpeins']."_".$rowInsumos['Dpehis']."-".$rowInsumos['Dpeing']] += $rowInsumos['Dpeped'];
			}
		}
		// var_dump($insumos);
		return $insumos;
		
	}
	
	function pintarInsumosBotiquin($codBotiquin,$codigoCco,$zona,$mostrarTodos)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		
		$pacientes= consultarPacientesPedido($codigoCco,$zona,$mostrarTodos);
		
		$cantPacientes = count($pacientes);

		$colspanTotal = 4+$cantPacientes;

		$cadenaTooltipPacientes = "";
		$cadenaTooltipInsPac = "";
		
		
		if($mostrarTodos=="on")
		{
			$linkMostrarTodos = "<span  id='trasladarInsumosAuxiliar' onclick='seleccionarCco(\"off\");' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>
									Ver pedidos pendientes<br>por entregar
								</span>";
		}
		else
		{
			$linkMostrarTodos = "<span  id='trasladarInsumosAuxiliar' onclick='seleccionarCco(\"on\");' style='font-family: verdana;font-weight:bold;font-size: 10pt;color: #0033FF;text-decoration: underline;cursor:pointer;'>
									Ver todos los pedidos<br>de las &uacute;ltimas 24 horas
								</span>";
		}
		
		
		$insumosBotiquin = consultarInsumosPedido($codigoCco,$zona,$mostrarTodos);
		
		
		$cantidadesPedidas = consultarCantidadesPedido($codigoCco,$zona,$mostrarTodos);
		
		$arrayCco = consultarCentrosDeCosto();
		
		
		$html = "";
		if($cantPacientes>0)
		{
			$html .= "	<div id='divInsumosPedido'>
		
							<p align='center' style='font-size:18;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$codigoCco." - ".$arrayCco[$codigoCco]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
							
							<table id='tableInsumosBotiquin' align='center'>
							<thead>
								<tr style='background-color:#FFFFFF'>
									<td colspan='".round($colspanTotal/2)."'>
										<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
											Buscar insumo:&nbsp;&nbsp;</b><input id='buscarInsumo' type='text' placeholder='Insumo a buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
										</span>
									</td>
									<td colspan='".round($colspanTotal/2)."' align='right'>
										".$linkMostrarTodos."
									</td>
								</tr>
								
								<tr class='encabezadoTabla'  align='center'>
									<td colspan='3'>INSUMOS</td>
									<td colspan='".$cantPacientes."'>HABITACIONES</td>
									<td rowspan='2'>Cant<br>total<br>ordenada</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td>C&oacute;digo</td>
									<td>Descripci&oacute;n</td>
									<td>Presentaci&oacute;n</td>
								";
								
								foreach($pacientes as $keyPaciente => $valuePaciente)
								{
									// ------------------------------------------
									// Tooltip
									// ------------------------------------------	
									
									$infoTooltip = "Historia: ".$valuePaciente['historia']."-".$valuePaciente['ingreso']."<br>";
									$infoTooltip .= "Paciente: ".$valuePaciente['nombre']."<br>";
									$infoTooltip .= "Documento: ".$valuePaciente['tipoDocumento']." ".$valuePaciente['documento'];
									
									
									$tooltipPacientes = "<div id=\"dvTooltipPacientes\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltip."</div>";
									
									$cadenaTooltipPacientes .= "tooltipPacientes_".$valuePaciente['codHabitacion']."|";
									
			$html .= "				<td id='tooltipPacientes_".$valuePaciente['codHabitacion']."'  title='".$tooltipPacientes."' style='cursor:pointer;'>&nbsp;".$valuePaciente['habitacion']."&nbsp;</td>";						
								}
								
			$html .= "			</tr>
								</thead>
								";		
								
								$cantFrecuentes = 0;
								$cantNoFrecuentes = 0;
								
								foreach($insumosBotiquin as $keyInsumo => $valueInsumo)
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
			$html .= "				<tr class='".$fila_lista." find'>
										<td>".$valueInsumo['codigo']."</td>
										<td>".$valueInsumo['descripcion']."</td>
										<td align='center'>".$valueInsumo['desUnidad']."</td>
										";
									
									
									$cantOrdenada=0;
									foreach($pacientes as $keyPaciente => $valuePaciente)
									{
										$idCampo = $valueInsumo['codigo']."_".$valuePaciente['historia']."-".$valuePaciente['ingreso'];
										
										$valorInput = "-";
										
										if(isset($cantidadesPedidas[$idCampo]) && $cantidadesPedidas[$idCampo]!="0")
										{
											$valorInput = "<b>".$cantidadesPedidas[$idCampo]."</b>";
											
											$cantOrdenada += $cantidadesPedidas[$idCampo];
										}
										
										
										// ------------------------------------------
										// Tooltip
										// ------------------------------------------	
										
										$infoTooltipInsumoPaciente = "Habitaci&oacute;n: ".$valuePaciente['habitacion']."<br>";
										$infoTooltipInsumoPaciente .= "Historia: ".$valuePaciente['historia']."-".$valuePaciente['ingreso']."<br>";
										$infoTooltipInsumoPaciente .= "Paciente: ".$valuePaciente['nombre']."<br>";
										$infoTooltipInsumoPaciente .= "Documento: ".$valuePaciente['tipoDocumento']." ".$valuePaciente['documento']."<br>";
										$infoTooltipInsumoPaciente .= "Insumo: ".$valueInsumo['codigo']." - ".$valueInsumo['descripcion'];
										
										$tooltipInsPac = "<div id=\"dvTooltipInsPac\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipInsumoPaciente."</div>";
										
										$cadenaTooltipInsPac .= "tooltipInsPac_".$idCampo."|";
										
										$detalle = "";
										if($valorInput != "-")
										{
											$detalle = "style='cursor:pointer;' onclick='verDetalleInsumoPaciente(\"".$valuePaciente['historia']."\",\"".$valuePaciente['ingreso']."\",\"".$valueInsumo['codigo']."\",\"".$valueInsumo['descripcion']."\",\"".$valuePaciente['habitacion']."\",\"".$mostrarTodos."\");'";
										}
										
										
			$html .= "					<td align='center' id='tooltipInsPac_".$idCampo."' title='".$tooltipInsPac."' ".$detalle.">".$valorInput."</td>";	
			
			
			
									}
									
			$html .= "				<td align='center'><span id='cantTotalOrdenada_".$valueInsumo['codigo']."' name='cantTotalOrdenada_".$valueInsumo['codigo']."' style='font-weight:bold;cursor:pointer;' onclick='verDetalleInsumo(\"".$valueInsumo['codigo']."\",\"".$valueInsumo['descripcion']."\",\"".$mostrarTodos."\",\"".$zona."\");'>".$cantOrdenada."</span></td>";								
			
								}
								
			$html .= "				<input type='hidden' id='cadenaTooltipPacientes' name='cadenaTooltipPacientes' value='".$cadenaTooltipPacientes."'>";						
			$html .= "				<input type='hidden' id='cadenaTooltipInsPac' name='cadenaTooltipInsPac' value='".$cadenaTooltipInsPac."'>";						
								
								
								
			$html .= "		</table>
						</div>";
			$data['error']=0;			
		}
		else
		{
			$html = "<p align='center'>
						<b style='font-size:12pt'>No hay pedidos de insumos pendientes.</b>
					</p>";
			$data['error']=1;		
		}
		
		
		
		
		// $data['error']=0;
		$data['html']=utf8_encode($html);	
		
		
		return $data;
	}
	
	function consultarNombrePaciente($historia)
	{
		global $conex;
		global $wemp_pmla;
		
		$queryNombre =  " SELECT Pacno1,Pacno2,Pacap1,Pacap2 
							FROM root_000037,root_000036
						   WHERE Orihis='".$historia."' 
							 AND Oriori='".$wemp_pmla."'
							 AND Pacced=Oriced
							 AND Pactid=Oritid;";
		
		$resNombre = mysql_query($queryNombre, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNombre . " - " . mysql_error());		   
		$numNombre = mysql_num_rows($resNombre);
		
		$nombrePaciente = "";
		if($numNombre>0)
		{
			$rowNombre = mysql_fetch_array($resNombre);
			
			if($rowNombre['Pacno1']!="NO APLICA" || $rowNombre['Pacno1']!="")
			{
				$nombrePaciente .= $rowNombre['Pacno1']." ";
			}
			if($rowNombre['Pacno2']!="NO APLICA" || $rowNombre['Pacno2']!="")
			{
				$nombrePaciente .= $rowNombre['Pacno2']." ";
			}
			if($rowNombre['Pacap1']!="NO APLICA" || $rowNombre['Pacap1']!="")
			{
				$nombrePaciente .= $rowNombre['Pacap1']." ";
			}
			if($rowNombre['Pacap2']!="NO APLICA" || $rowNombre['Pacap2']!="")
			{
				$nombrePaciente .= $rowNombre['Pacap2'];
			}
			
		}
		
		return $nombrePaciente;
	}
	
	function pintarDetalleInsumoPaciente($codigoCco,$historia,$ingreso,$insumo,$descInsumo,$habitacion,$mostrarTodos)
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		
		$condicionPaciente = "";
		if($historia!="" && $ingreso!="")
		{
			$condicionPaciente = "AND Dpehis='".$historia."'
								  AND Dpeing='".$ingreso."'";
		}
		
		
		$condicionMostrarTodos = "AND Pedent='off' ";
		if($mostrarTodos=="on")
		{
			
			$fecha = date( "Y-m-d", time()-24*3600 );
			$hora = date( "H:i:s", time()-24*3600 );
			
			$condicionMostrarTodos = "AND (Pedent='off' OR (Pedent='on' AND ( a.Fecha_data > '".$fecha."' OR (a.Fecha_data >= '".$fecha."' AND a.Hora_data >= '".$hora."' ))))";
		}
		
		$queryInsumos = " SELECT Pedbot,Pedaux,SUM(Dpeped) as Cantidad,Descripcion,Cconom,Empresa
							FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,usuarios,".$wbasedato."_000011
						   WHERE Pedcco='".$codigoCco."' 
							 ".$condicionMostrarTodos." 
							 AND Pedest='on'
							 AND Dpecod=Pedcod
							 ".$condicionPaciente."
							 AND Dpeins='".$insumo."'
							 AND Dpeped>0
							 AND Dpeest='on'
							 AND Pedaux=Codigo
							 AND Ccocod=Pedbot
							 AND Ccoest='on'
						GROUP BY Pedbot,Pedaux
						ORDER BY Descripcion;";
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());		   
		$numInsumos = mysql_num_rows($resInsumos);
		
		$arrayInsumos = array();
		$contInsumos = 0;
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				$arrayInsumos[$contInsumos]['botiquin']=$rowInsumos['Pedbot'];
				$arrayInsumos[$contInsumos]['auxiliar']=$rowInsumos['Pedaux'];
				$arrayInsumos[$contInsumos]['cantidad']=$rowInsumos['Cantidad'];
				$arrayInsumos[$contInsumos]['nombre']=$rowInsumos['Descripcion'];
				$arrayInsumos[$contInsumos]['desBotiquin']=$rowInsumos['Cconom'];
				$arrayInsumos[$contInsumos]['empresa']=$rowInsumos['Empresa'];
				
				$contInsumos++;
			}
		}
		
		
		$colspanTabla = 3;
		
		$fotoAuxiliares = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fotoAuxiliares');
		$colspanResponsable=1;
		if($fotoAuxiliares=="on")
		{
			$colspanResponsable++;
			$colspanTabla++;
		}
		
		$html = "";
		$html .= "	<div id='divResumenInsumosPedidosPaciente'>
						<p align='center' style='font-size:18;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$descInsumo."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>
							";
		if(count($arrayInsumos)>0)
		{
			
		
			$arrayCco = consultarCentrosDeCosto();
		
			
			$html .= "		<table align='center' width='70%'>
								<tr class='encabezadoTabla'>
									<td colspan='4' align='center'>DATOS DEL PACIENTE</td>
								</tr>
								
								<tr>
									<td class='fila1'>Historia</td>
									<td class='fila2'>".$historia."-".$ingreso."</td>
									<td class='fila1'>Nombre</td>
									<td class='fila2'>".consultarNombrePaciente($historia)."</td>
								</tr>
								<tr>
									<td class='fila1'>Habitaci&oacute;n</td>
									<td class='fila2'>".$habitacion."</td>
									<td class='fila1'>Centro de costos</td>
									<td class='fila2'>".$codigoCco."-".$arrayCco[$codigoCco]."</td>
								</tr>
							</table>";	
		
			
							
			$html .= "		<table id='tableDetalleInsumoPaciente' align='center' width='70%'>	
								<tr class='encabezadoTabla'>
									<td colspan='".$colspanTabla."' align='center'>CANTIDADES PEDIDAS</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td>Botiqu&iacute;n</td>
									<td colspan='".$colspanResponsable."'>Responsable</td>
									<td>Cantidad</td>
								</tr>";
								
								$cantTotal = 0;
								
								foreach($arrayInsumos as $keyInsumo => $valueInsumo)
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
									$cantTotal += $valueInsumo['cantidad'];
									
			$html .= "				<tr class='".$fila_lista."'>
										<td class='botiquin_".$valueInsumo['botiquin']."' id='botiquin_".$valueInsumo['botiquin']."_".$valueInsumo['auxiliar']."' align='center'>".$valueInsumo['botiquin']."-".$valueInsumo['desBotiquin']."</td>";
										if($fotoAuxiliares=="on")
										{
											$longitud = strlen($valueInsumo['auxiliar']);
											$codigoAuxiliar = $valueInsumo['auxiliar'];
											if($longitud>5)
											{
												$codigoAuxiliar = substr($valueInsumo['auxiliar'],$longitud-5,$longitud);
											}
											
											$urlFotoAuxiliar = consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valueInsumo['empresa']);
											
										
			$html .= "						<td align='center' width='65px'>
												<img class='lightbox' id='fotoAuxiliar_".$valueInsumo['auxiliar']."' src='".$urlFotoAuxiliar."' width=65px height=75px>
												<img class='fotoAuxiliar_".$valueInsumo['auxiliar']."' id='fotoAuxiliar_Apliada".$valueInsumo['auxiliar']."' src='".$urlFotoAuxiliar."' style='display:none' height='700px'/>
											</td>";								
										}
			$html .= "				
									
										<td>".$valueInsumo['auxiliar']."-".$valueInsumo['nombre']."</td>
										<td align='center'>".$valueInsumo['cantidad']."</td>
									</tr>";						
								}
								
			$html .= "			<tr class='encabezadoTabla' align='center'>
									<td colspan='".($colspanTabla-1)."' align='right'>Total</td>
									<td>".$cantTotal."</td>
								</tr>
							</table>";
		}
		else
		{
			$html .= "	<br>
						<p align='center'>
							<b>No hay pedidos de insumos pendientes.</b>
						</p>";
		}
		
		$html .= "		<br><br>
							<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
							<br><br>
						</div>";
					
		return $html;			
	}
	
	function pintarDetalleInsumo($codigoCco,$insumo,$descInsumo,$mostrarTodos,$zona)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoHce;
		global $wemp_pmla;
		
		
		
		$condicionMostrarTodos = "AND Pedent='off' ";
		if($mostrarTodos=="on")
		{
			
			$fecha = date( "Y-m-d", time()-24*3600 );
			$hora = date( "H:i:s", time()-24*3600 );
			
			$condicionMostrarTodos = "AND (Pedent='off' OR (Pedent='on' AND ( a.Fecha_data > '".$fecha."' OR (a.Fecha_data >= '".$fecha."' AND a.Hora_data >= '".$hora."' ))))";
		}
		
		$ccoTipo = consultarTipoCco($conex,$wbasedato,$codigoCco);
		
		if($ccoTipo=="ayudaDiagnostica")
		{
			$queryInsumos = " SELECT Pedbot,Pedaux,SUM(Dpeped) as Cantidad,Descripcion,Cconom,Empresa
								FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,usuarios,".$wbasedato."_000018,".$wbasedato."_000011
							   WHERE Pedcco='".$codigoCco."' 
								 ".$condicionMostrarTodos." 
								 AND Pedest='on'
								 AND Dpecod=Pedcod
								 AND Dpeins='".$insumo."'
								 AND Dpeped>0
								 AND Dpeest='on'
								 AND Ubihis=Dpehis
								 AND Ubiing=Dpeing
							     AND Ubiald!='on'
								 AND Pedaux=Codigo
								 AND Ccocod=Pedbot
								 AND Ccoest='on'
							GROUP BY Pedbot,Pedaux
							ORDER BY Descripcion;";
		}
		else
		{
			$condicionZona = "";
			if($zona!="" && $zona!="0")
			{
				$condicionZona = "AND Habzon='".$zona."'";
			}
			
			$queryUrgPacSinUbicacion = "";
			if($ccoTipo=="urgencias" && ($zona=="0" || $zona=="SinUbicacion"))
			{
				$queryUrgPacSinUbicacion = "   UNION ALL
				
											  SELECT Pedbot,Pedaux,SUM(Dpeped) as Cantidad,Descripcion,Cconom,Empresa
												FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,usuarios,".$wbasedato."_000018,".$wbasedato."_000011,".$wbasedatoHce."_000022
											   WHERE Pedcco='".$codigoCco."' 
												 ".$condicionMostrarTodos." 
												 AND Pedest='on'
												 AND Dpecod=Pedcod
												 AND Dpeins='".$insumo."'
												 AND Dpeped>0
												 AND Dpeest='on'
												 AND Ubihis=Dpehis
												 AND Ubiing=Dpeing
												 AND Ubisac=Pedcco
												 AND Pedaux=Codigo
												 AND Ccocod=Pedbot
												 AND Ccoest='on'
												 AND Mtrhis=Ubihis
												 AND Mtring=Ubiing
												 AND Mtrcua!='on'
											GROUP BY Pedbot,Pedaux";
			}

			
			$queryInsumos = " SELECT Pedbot,Pedaux,SUM(Dpeped) as Cantidad,Descripcion,Cconom,Empresa
								FROM ".$wbasedato."_000230 a,".$wbasedato."_000231,usuarios,".$wbasedato."_000020,".$wbasedato."_000011
							   WHERE Pedcco='".$codigoCco."' 
								 ".$condicionMostrarTodos." 
								 AND Pedest='on'
								 AND Dpecod=Pedcod
								 AND Dpeins='".$insumo."'
								 AND Dpeped>0
								 AND Dpeest='on'
								 AND Habhis=Dpehis
								 AND Habing=Dpeing
								 AND Habcco=Pedcco
								 ".$condicionZona."
								 AND Pedaux=Codigo
								 AND Ccocod=Pedbot
								 AND Ccoest='on'
							GROUP BY Pedbot,Pedaux
							
								".$queryUrgPacSinUbicacion."
							
							ORDER BY Descripcion;";
		}
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());		   
		$numInsumos = mysql_num_rows($resInsumos);
		
		$arrayInsumos = array();
		$contInsumos = 0;
		if($numInsumos>0)
		{
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				$arrayInsumos[$rowInsumos['Pedaux']]['botiquin']=$rowInsumos['Pedbot'];
				$arrayInsumos[$rowInsumos['Pedaux']]['auxiliar']=$rowInsumos['Pedaux'];
				$arrayInsumos[$rowInsumos['Pedaux']]['cantidad']+=$rowInsumos['Cantidad'];
				$arrayInsumos[$rowInsumos['Pedaux']]['nombre']=$rowInsumos['Descripcion'];
				$arrayInsumos[$rowInsumos['Pedaux']]['desBotiquin']=$rowInsumos['Cconom'];
				$arrayInsumos[$rowInsumos['Pedaux']]['empresa']=$rowInsumos['Empresa'];
				
			}
		}
		
		$colspanTabla = 3;
		
		$fotoAuxiliares = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fotoAuxiliares');
		$colspanResponsable=1;
		if($fotoAuxiliares=="on")
		{
			$colspanResponsable++;
			$colspanTabla++;
		}
		
		
		$html = "";
		$html .= "		<div id='divResumenInsumosPedidos'>
							<p align='center' style='font-size:18;font-weight:bold;'><span style='background-color:#2a5db0;background-color:#2a5db0;color:#FFFFFF;border-radius:5px;font-size:20px;'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$descInsumo."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></span></p>";
		if(count($arrayInsumos)>0)
		{
			$html .= "		<table id='tableDetalleInsumo' align='center' width='70%'>	
								<tr class='encabezadoTabla'>
									<td colspan='".$colspanTabla."' align='center'>CANTIDADES PEDIDAS</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td>Botiqu&iacute;n</td>
									<td colspan='".$colspanResponsable."'>Responsable</td>
									<td>Cantidad</td>
								</tr>";
								$cantTotal = 0;
								foreach($arrayInsumos as $keyInsumo => $valueInsumo)
								{
									if ($fila_lista=='Fila1')
										$fila_lista = "Fila2";
									else
										$fila_lista = "Fila1";
									
									$cantTotal += $valueInsumo['cantidad'];
									
			$html .= "				<tr class='".$fila_lista."'>
										<td class='botiquin_".$valueInsumo['botiquin']."' id='botiquin_".$valueInsumo['botiquin']."_".$valueInsumo['auxiliar']."' align='center'>".$valueInsumo['botiquin']."-".$valueInsumo['desBotiquin']."</td>";
										if($fotoAuxiliares=="on")
										{
											$longitud = strlen($valueInsumo['auxiliar']);
											$codigoAuxiliar = $valueInsumo['auxiliar'];
											if($longitud>5)
											{
												$codigoAuxiliar = substr($valueInsumo['auxiliar'],$longitud-5,$longitud);
											}
											
											$urlFotoAuxiliar = consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valueInsumo['empresa']);
											
										
			$html .= "						<td align='center' width='65px'>
												<img class='lightbox' id='fotoAuxiliar_".$valueInsumo['auxiliar']."' src='".$urlFotoAuxiliar."' width=65px height=75px>
												<img class='fotoAuxiliar_".$valueInsumo['auxiliar']."' id='fotoAuxiliar_Apliada".$valueInsumo['auxiliar']."' src='".$urlFotoAuxiliar."' style='display:none' height='700px'/>
											</td>";								
										}
			$html .= "				
										<td>".$valueInsumo['auxiliar']."-".$valueInsumo['nombre']."</td>
										<td align='center'>".$valueInsumo['cantidad']."</td>
									</tr>";						
								}
								
			$html .= "			<tr class='encabezadoTabla' align='center'>
									<td colspan='".($colspanTabla-1)."' align='right'>Total</td>
									<td>".$cantTotal."</td>
								</tr>
							</table>";
		}
		else
		{
			$html .= "	<br>
						<p align='center'>
							<b>No hay pedidos de insumos pendientes.</b>
						</p>";
		}
		$html .= "			<br><br>
							<span><input type='button' value='Cerrar ventana' onclick='cerrarModal();'></span>
							<br><br>
						</div>";
					
		return $html;
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
		case 'pintarZona':
		{	
			$data = pintarZona($codigoCco);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarInsumosBotiquin':
		{	
			$data = pintarInsumosBotiquin($codBotiquin,$codigoCco,$zona,$mostrarTodos);
			echo json_encode($data);
			break;
			return;
		}
		case 'detallePedidosInsumoPaciente':
		{	
		
			$data = pintarDetalleInsumoPaciente($codigoCco,$historia,$ingreso,$insumo,$descInsumo,$habitacion,$mostrarTodos);
			echo json_encode($data);
			break;
			return;
		}
		case 'detallePedidosInsumo':
		{	
		
			$data = pintarDetalleInsumo($codigoCco,$insumo,$descInsumo,$mostrarTodos,$zona);
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
	  <title>Reporte pedidos</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		
		
		<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		
		 <script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script> 

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	
	function validarZona()
	{
		$("#trZona").hide();
		$("#tdFiltroZona").html("");
		
		$("#divInsumosPedido").html("");
		
		$.post("reportePedidosInsumos.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarZona',
			codigoCco		: $('#filtroCcoDispensacion').val(),
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {
			
			if(data=="")
			{
				seleccionarCco();
			}
			else
			{
				$("#tdFiltroZona").html(data);
				$("#trZona").show();
			}
			
		},'json');
	}
	
	function seleccionarCco(mostrarTodos)
	{
		if(mostrarTodos==undefined)
		{
			mostrarTodos=$('#mostrarTodos').val();
		}
		
		var zona = "";
		if($("#filtroZonas").val()=="")
		{
			jAlert("Debe seleccionar una zona","ALERTA");
			return;
		}
		else if($("#filtroZonas").val()!==undefined)
		{
			zona = $("#filtroZonas").val();
		}
		
		
		if($('#filtroCcoDispensacion').val()!="")
		{
			$.post("reportePedidosInsumos.php",
			{
				consultaAjax 	: '',
				accion			: 'pintarInsumosBotiquin',
				codBotiquin		: $("#filtroCcoDispensacion option:selected").attr("botiquin"),
				codigoCco		: $('#filtroCcoDispensacion').val(),
				zona			: zona,
				mostrarTodos	: mostrarTodos,
				wemp_pmla		: $('#wemp_pmla').val()
			}
			, function(data) {
				
				$("#divInsumosPedido").html(data.html);
				// $("#divInsumosPedido").show();
				
				if(data.error==0)
				{
					// incializar tooltip
					
					$( ".frecuente").tooltip();
					
					//Tooltip
					var cadenaTooltipPacientes = $("#cadenaTooltipPacientes").val();
					
					cadenaTooltipPacientes = cadenaTooltipPacientes.split("|");
					
					for(var i = 0; i < cadenaTooltipPacientes.length-1;i++)
					{
						$( "#"+cadenaTooltipPacientes[i] ).tooltip();
					}
					
					
					
					// incializar tooltip
					
					//Tooltip
					var cadenaTooltipInsPac = $("#cadenaTooltipInsPac").val();
					
					cadenaTooltipInsPac = cadenaTooltipInsPac.split("|");
					
					for(var i = 0; i < cadenaTooltipInsPac.length-1;i++)
					{
						$( "#"+cadenaTooltipInsPac[i] ).tooltip();
					}
					
					$('#buscarInsumo').quicksearch('#tableInsumosBotiquin .find');
				}
				
				
				
			},'json');
		}
		else
		{
			jAlert("Debe seleccionar el centro de costos a dispensar","ALERTA");
		}
	}
	
	function verDetalleInsumoPaciente(historia,ingreso,insumo,descInsumo,habitacion,mostrarTodos)
	{
		$.post("reportePedidosInsumos.php",
		{
			consultaAjax 	: '',
			accion			: 'detallePedidosInsumoPaciente',
			wemp_pmla		: $('#wemp_pmla').val(),
			codigoCco		: $('#filtroCcoDispensacion').val(),
			historia		: historia,
			ingreso			: ingreso,
			insumo			: insumo,
			descInsumo		: descInsumo,
			habitacion		: habitacion,
			mostrarTodos	: mostrarTodos
		}
		, function(data) {
			
			$( "#dvAuxModalResumen" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalResumen" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalResumen" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalResumen" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalResumen" ).height();
			
			$.blockUI({ message: $('#divResumenInsumosPedidosPaciente'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });
			
			ocultarTdBotiquin("tableDetalleInsumoPaciente");
			
			
			
			//Permite que la foto de la auxiliar se vea grande.
			$('.lightbox').click(function() {
				
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');
											
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: 'auto'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});
			
		},'json');
	}
	
	
	function verDetalleInsumo(insumo,descInsumo,mostrarTodos,zona)
	{
		$.post("reportePedidosInsumos.php",
		{
			consultaAjax 	: '',
			accion			: 'detallePedidosInsumo',
			wemp_pmla		: $('#wemp_pmla').val(),
			codigoCco		: $('#filtroCcoDispensacion').val(),
			insumo			: insumo,
			descInsumo		: descInsumo,
			mostrarTodos	: mostrarTodos,
			zona			: zona
		}
		, function(data) {
			
			$( "#dvAuxModalResumenInsumo" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalResumenInsumo" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalResumenInsumo" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalResumenInsumo" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalResumenInsumo" ).height();
			
			$.blockUI({ message: $('#divResumenInsumosPedidos'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });
			
			ocultarTdBotiquin("tableDetalleInsumo");
			
			
			//Permite que la foto de la auxiliar se vea grande.
			$('.lightbox').click(function() {
				
				var imagen = $(this).attr('id');
				var id = $('.'+imagen).attr('id');
											
				$.blockUI({ 
					message: $('#'+id), 
					css: { 
						top:  ($(window).height() - 700) /2 + 'px', 
						left: ($(window).width() - 700) /2 + 'px', 
						width: 'auto'
					} 
				}); 
				
				$('.blockOverlay').attr('title','Click to unblock').click($.unblockUI);
			});
			
		},'json');
	}
	
	function ocultarTdBotiquin(idtabla)
	{
		var filaAnterior = "";
		var textoFilaAnterior = "";
		var cantFilas = 1;
		$('table[id='+idtabla+'] [class^=botiquin_]').each(function(){
			
			if($(this).html()==textoFilaAnterior)
			{
				cantFilas++;
				filaAnterior.attr("rowspan",cantFilas);
				$(this).hide();
			}
			else
			{
				cantFilas = 1;
				filaAnterior = $(this);
				textoFilaAnterior = $(this).html();
			}
			
		});
	}
	
	function cerrarModal()
	{
		$.unblockUI();
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
	
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	// -->	ENCABEZADO
	encabezado("REPORTE DE PEDIDOS", $wactualiz, 'clinica');
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='mostrarTodos' name='mostrarTodos' value='off'>";
	
	// $filtroBotiquin = pintarFiltroCco();
	// echo $filtroBotiquin;
	
	
	
	
	
	$filtroBotiquin = pintarFiltroCco();
	echo $filtroBotiquin;
	
	
	
	echo "<div id='divInsumosPedido'></div>";
	
	echo "<div id='dvAuxModalResumen' style='display:none'></div>";
	echo "<div id='dvAuxModalResumenInsumo' style='display:none'></div>";
	
	echo "<p align=center><span><input type='button' value='Cerrar ventana' onclick='cerrarVentana();'></span></p>";
	
	
	?>
	</BODY>
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
