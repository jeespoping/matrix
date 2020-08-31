<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Reporte detallado de insumos dispensados y aplicados.
//AUTOR:				Veronica Arismendy
//FECHA DE CREACION: 	2017-07-14
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2019-12-30';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2019-12-30 - Edwin MG:				Se modifican queries referentes a la tabla turnero (000023) para que se tenga en cuenta la configuración 
//										del centro de costos de ayuda diagnosticas (ccotct de movhos_000011)
// 	2017-10-11 - Jessica Madrid Mejía:	- En la función consultarCentrosDeCosto() se tienen en cuenta los centros de costos de ayudas 
// 										diagnosticas que están configurados en root_000117 y la tabla de citas 000023 existe
// 										- Se quita el filtro Activo='A' en las consultas de la tabla de usuarios para que siempre muestre 
// 										el responsable del movimiento sin importar si está inactivo. 
// 										- Se hace el include a botiquin.inc.php para evitar tener funciones repetidas, por tal motivo se 
// 										elimina la funcion consultarFoto().
// 	2017-08-01 - Jessica Madrid Mejía:	Se agrega el filtro botiquin, historia e ingreso y se quita el filtro por zona.
// 										Se modifican el reporte para mostrar las cantidades pedidas, dispensadas, aplicadas y devueltas
// 										y se muestra el detalle por paciente.
// 	2017-07-26 - Jessica Madrid Mejía:	Se modifican el reporte para mostrar las cantidades utilizadas por los auxiliares (Dispensado
// 										menos lo devuelto)
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

	session_start();
	
	include_once("root/comun.php");
	include_once("movhos/botiquin.inc.php");
	include_once("citas/funcionesAgendaCitas.php");
	$wemp_pmla = isset($_GET["wemp_pmla"]) ? $_GET["wemp_pmla"] : $_POST["wemp_pmla"]  ;
	
	$conex = obtenerConexionBD("matrix");
	$wbasedatoHce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wbasedatoMov = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	function pintarAuxiliares($conex){
		global $wemp_pmla;
		global $wbasedatoHce;

		$rolesAuxiliar = consultarAliasPorAplicacion( $conex, $wemp_pmla, "rolesAuxiliarEnfermeria" );
		$rolesAuxiliar = explode(",",$rolesAuxiliar);
		$arrAuxiliares = "";

		foreach($rolesAuxiliar as $value){
			$arrAuxiliares .= "'".$value."' ,";
		}

		$arrAuxiliares = substr ($arrAuxiliares, 0, -1);

		$queryRolUsuario =  "SELECT usucod, descripcion
							FROM ".$wbasedatoHce."_000020 u
							INNER JOIN ".$wbasedatoHce."_000019 r ON r.Rolcod = u.Usurol
							INNER JOIN usuarios us ON us.Codigo = u.Usucod
							WHERE rolcod  IN (" . $arrAuxiliares .  ")
							AND us.Activo = 'A'
							AND rolest = 'on'
							AND usuest = 'on'
							ORDER BY 2 ASC";
		$resRolAuxiliar = mysql_query($queryRolUsuario, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryRolUsuario . " - " . mysql_error());
		$html = "";

		while($row = mysql_fetch_assoc($resRolAuxiliar)){
			$html .= "<option value='" . $row["usucod"] . "'>" . $row["usucod"] . "-" . utf8_encode($row["descripcion"]) . "</option>";
		}

		return $html;
		exit();
	}

	function pintarInsumos($conex)
	{
		global $wemp_pmla;
		global $wbasedatoMov;

		$sql = "SELECT artcod, artgen, unides, artcom
			FROM ".$wbasedatoMov."_000026
			LEFT OUTER JOIN ".$wbasedatoMov."_000066
						ON melgru = SUBSTRING_INDEX( artgru, '-', 1 )
						AND meltip = 'M'
						AND melest = 'on'
			LEFT OUTER JOIN ".$wbasedatoMov."_000232
						ON Arscod = artcod
						AND (Arscco='1186' OR Arscco='*')
						AND Arsfre = 'on'
						AND Arsest = 'on',
						".$wbasedatoMov."_000027,
						".$wbasedatoMov."_000141
			WHERE ( artcod = '%'
			    OR artcom LIKE '%%%'
			    OR artgen LIKE '%%%' )
				AND artest = 'on'
				AND unicod = artuni
				AND salart = artcod
				AND salser = '1186'
			ORDER BY Artgen ASC ";

		$res = mysql_query( $sql, $conex );
		$html = "";
		while($row = mysql_fetch_assoc($res)){
			$html .= "<option value='" . $row["artcod"] . "'>" . $row["artcod"]."-".utf8_encode($row["artgen"]) . "-" . $row["unides"] . "</option>";
		}

		return $html;
		exit();
	}
	
	function consultarBotiquines()
	{
		global $conex;
		global $wbasedatoMov;
		
		$queryBotiquines =  " SELECT Ccoori,Cconom  
								FROM ".$wbasedatoMov."_000058 a,".$wbasedatoMov."_000011 b
							   WHERE a.Ccoest='on' 
							     AND Ccocod=Ccoori
								 AND b.Ccoest='on' 
							GROUP BY Ccoori;";
		
		$resBotiquines = mysql_query($queryBotiquines, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryBotiquines . " - " . mysql_error());		   
		$numBotiquines = mysql_num_rows($resBotiquines);
		
		$arrayBotiquines = array();
		if($numBotiquines>0)
		{
			while($rowBotiquines = mysql_fetch_array($resBotiquines))
			{
				$arrayBotiquines[$rowBotiquines['Ccoori']] = $rowBotiquines['Cconom'];
			}
		}
		
		return $arrayBotiquines;
	}
	
	function consultarCentrosDeCosto()
	{
		global $conex;
		global $wbasedatoMov;
		global $wemp_pmla;

		$ccoHabilitados	= consultarAliasPorAplicacion( $conex, $wemp_pmla, "ccoHabilitadosDispensacionInsumos" );

		$ccoHabilitados = explode(",",$ccoHabilitados);

		$queryCCo = " SELECT Ccocod,Cconom,Ccoayu
						FROM ".$wbasedatoMov."_000011
					   WHERE (Ccohos='on' OR Ccourg='on' OR  Ccocir='on'  OR  Ccoayu='on' )
					     AND Ccoest='on'
					ORDER BY Ccocod,Cconom;";

		$resCCo=  mysql_query($queryCCo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryCCo." - ".mysql_error());

		$arrayCco = array();
		while($rowCCo = mysql_fetch_array($resCCo)) {
			
			$mostrarCco = true;
			if($rowCCo['Ccoayu']=="on")
			{
				$mostrarCco = consultarConfiguracionAyudaDiagnostica($conex,$wbasedatoMov,$rowCCo['Ccocod']);
			}
			
			
			// if(in_array("*",$ccoHabilitados) || in_array(trim($rowCCo['Ccocod']),$ccoHabilitados))  {
			if((in_array("*",$ccoHabilitados) || in_array(trim($rowCCo['Ccocod']),$ccoHabilitados)) && $mostrarCco)
			{
					$arrayCco[trim($rowCCo['Ccocod'])] = trim($rowCCo['Cconom']);
			}
		}

		return $arrayCco;
	}


	function pintarFiltroBotiquin()
	{
		$botiquines = consultarBotiquines();
		
		$filtroBotiquin .= "";

		foreach($botiquines as $keyBotiquin => $valueBotiquin) 
		{
			$filtroBotiquin .= "	<option value='".$keyBotiquin."'>".$keyBotiquin."-".$valueBotiquin."</option>";
		}

		echo $filtroBotiquin;
	}

	function pintarFiltroCco()
	{
		$ccoDispensacion = consultarCentrosDeCosto();
		$filtroFecha .= "";

		foreach($ccoDispensacion as $keycco => $valuecco) {
			$filtroFecha .= "	<option value='".$keycco."'>".$keycco."-".$valuecco."</option>";
		}

		echo $filtroFecha;
	}

	function detallePedidosInsumo($codMovimiento,$insumo, $valueForm)
	{
		global $conex;
		global $wbasedatoMov;
		global $wemp_pmla;

		$params = array();
		parse_str($valueForm, $params);
		
		
		
		$arrayCondiciones = condiciones($params);

			
		$rangoFecha = $arrayCondiciones['rangoFecha'];
		$rangoFechaPedido = $arrayCondiciones['rangoFechaPedido'];
		$condHistoria = $arrayCondiciones['condHistoria'];
		$condHistoriaPed = $arrayCondiciones['condHistoriaPed'];
		$condIngreso = $arrayCondiciones['condIngreso'];
		$condIngresoPed = $arrayCondiciones['condIngresoPed'];
		$condicionBot = $arrayCondiciones['condicionBot'];
		$condBotPed = $arrayCondiciones['condBotPed'];
		$cco = $arrayCondiciones['cco'];
		$ccoPed = $arrayCondiciones['ccoPed'];
		$conInsumo = $arrayCondiciones['conInsumo'];
		$conInsumoPed  = $arrayCondiciones['conInsumoPed'];
		$conAux = $arrayCondiciones['conAux'];
		$conAuxPed = $arrayCondiciones['conAuxPed'];

		
		if($codMovimiento=="PD")
		{
			$queryInsumos = " SELECT Pedbot AS botiquin,Pedaux AS auxiliar,SUM(Dpeped) AS cantidad,Descripcion,Empresa,Cconom
								FROM ".$wbasedatoMov."_000231 a,".$wbasedatoMov."_000230,usuarios,".$wbasedatoMov."_000011
							   WHERE Dpeins='".$insumo."'
								 ".$condBotPed."  
								 ".$ccoPed."  
								 ".$conAuxPed."							   
								 ".$condHistoriaPed."							   
								 ".$condIngresoPed."							   
								 AND Dpeped>0 
								 AND Dpeest='on'
								 ".$rangoFechaPedido."
								 AND Pedcod=Dpecod
								 AND Pedest='on'
								 AND Codigo=Pedaux
								 AND Ccocod=Pedbot
								 AND Ccoest='on'
							GROUP BY Pedbot,Pedaux
							ORDER BY Pedbot,Descripcion;";
							
			
		}
		else
		{
			$ccoTipo = consultarTipoCco($conex,$wbasedatoMov,$params['cco']);
		
			if($ccoTipo=="ayudaDiagnostica")
			{
				// $prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedatoMov,$params['cco'],false);
				// $prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
				
				// $queryInsumos = " SELECT Movbot AS botiquin,Movaux AS auxiliar,SUM(Movcmo) AS cantidad,Descripcion,Empresa,Cconom
									// FROM ".$wbasedatoMov."_000228,usuarios,".$wbasedatoMov."_000011,root_000037,".$prefijoTablaAyudasDiagnosticas."_000023 
								   // WHERE Movins='".$insumo."'
									 // ".$condicionBot."   
									 // ".$conAux."
									 // ".$condHistoria."
									 // ".$condIngreso."
									 // AND Movmov='".$codMovimiento."'
									 // AND Movest='on'
									 // ".$rangoFecha."
									 // AND Codigo=Movaux
									 // AND Ccocod=Movbot
									 // AND Ccoest='on'
									 // AND Orihis=Movhis
									 // AND Oriing=Moving
									 // AND Oriori='01'
									 // AND ".$prefijoCampos."tip=Oritid
									 // AND ".$prefijoCampos."doc=Oriced
									 // AND ".$prefijoCampos."est='on'
									 // AND ".$prefijoCampos."fpr!='on'
									 // AND ".$prefijoCampos."acp!=''
								// GROUP BY Movbot,Movaux

								// ORDER BY botiquin,Descripcion;";
								
				$prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedatoMov,$params['cco'],false);
				$prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
										
				$sql = "SELECT Ccotct 
						  FROM ".$wbasedatoMov."_000011 
						 WHERE ccocod = '".$params['cco']."'";
				 
				$resTablasCitas = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());		   
				$resNum 		= mysql_num_rows($resTablasCitas);
										  
				$rowsTablasCitas = mysql_fetch_array( $resTablasCitas );
				
				list( $tablaTurnero, $tipoDocumento, $numeroDocumento, $datosCondicionesExtras ) = explode( ",", $rowsTablasCitas['Ccotct'] );
				
				if( !empty($datosCondicionesExtras) ){
					$arrayCondicionesExtras = explode( "-", $datosCondicionesExtras );
					
					$condicionesExtrasTurnero = " AND ".implode( " AND ", $arrayCondicionesExtras );
				}
				
				$queryInsumos = " SELECT Movbot AS botiquin,Movaux AS auxiliar,SUM(Movcmo) AS cantidad,Descripcion,Empresa,Cconom
									FROM ".$wbasedatoMov."_000228,usuarios,".$wbasedatoMov."_000011,root_000037,".$prefijoTablaAyudasDiagnosticas." 
								   WHERE Movins='".$insumo."'
									 ".$condicionBot."   
									 ".$conAux."
									 ".$condHistoria."
									 ".$condIngreso."
									 AND Movmov='".$codMovimiento."'
									 AND Movest='on'
									 ".$rangoFecha."
									 AND Codigo=Movaux
									 AND Ccocod=Movbot
									 AND Ccoest='on'
									 AND Orihis=Movhis
									 AND Oriing=Moving
									 AND Oriori='01'
									 AND ".$tipoDocumento."=Oritid
									 AND ".$numeroDocumento."=Oriced
									$condicionesExtrasTurnero
								GROUP BY Movbot,Movaux

								ORDER BY botiquin,Descripcion;";
			}
			else
			{
				$queryInsumos = " SELECT Movbot AS botiquin,Movaux AS auxiliar,SUM(Movcmo) AS cantidad,Descripcion,Empresa,Cconom
									FROM ".$wbasedatoMov."_000228,usuarios,".$wbasedatoMov."_000011
								   WHERE Movins='".$insumo."'
									 ".$condicionBot."  
									 ".$cco."  
									 ".$conAux."
									 ".$condHistoria."
									 ".$condIngreso."
									 AND Movmov='".$codMovimiento."'
									 AND Movest='on'
									 ".$rangoFecha."
									 AND Codigo=Movaux
									 AND Ccocod=Movbot
									 AND Ccoest='on'
								GROUP BY Movbot,Movaux

								ORDER BY botiquin,Descripcion;";
			}
			
		
			
		}
		
		$resInsumos = mysql_query($queryInsumos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryInsumos . " - " . mysql_error());
		$numInsumos = mysql_num_rows($resInsumos);

		$arrayInsumos = array();
		$idArray = "";
		while($rowInsumos = mysql_fetch_array($resInsumos)) {
			
			$idArray = $rowInsumos['botiquin']."-".$rowInsumos['auxiliar'];
			
			$arrayInsumos[$idArray]['botiquin']=$rowInsumos['botiquin'];
			$arrayInsumos[$idArray]['auxiliar']=$rowInsumos['auxiliar'];
			$arrayInsumos[$idArray]['cantidad']+=$rowInsumos['cantidad'];
			$arrayInsumos[$idArray]['nombre']=utf8_encode($rowInsumos['Descripcion']);
			$arrayInsumos[$idArray]['empresa']=$rowInsumos['Empresa'];
			$arrayInsumos[$idArray]['desBotiquin']=utf8_encode($rowInsumos['Cconom']);
			
			$contInsumos++;
		}
		
		$colspanTabla = 4;

		$fotoAuxiliares = consultarAliasPorAplicacion($conex, $wemp_pmla, 'fotoAuxiliares');
		$colspanResponsable=1;
		if($fotoAuxiliares=="on") {
			$colspanResponsable++;
			$colspanTabla++;
		}

		
		$descripcionMovimiento = "";
		$descMov = "";
		if($codMovimiento=="PD")
		{
			$descripcionMovimiento = "cargados";
			$descMov = "PEDIDAS";
		}
		elseif($codMovimiento=="CR")
		{
			$descripcionMovimiento = "cargados";
			$descMov = "DISPENSADAS";
		}
		elseif($codMovimiento=="AP")
		{
			$descripcionMovimiento = "aplicados";
			$descMov = "APLICADAS";
		}
		elseif($codMovimiento=="DV")
		{
			$descripcionMovimiento = "devueltos";
			$descMov = "DEVUELTAS";
		}
		
		
		
		$html = "";
		$html .= "<div id='divResumenInsumosPedidos'>";
		if(count($arrayInsumos)>0) {

			$html .= "
			<table id='tableDetalleInsumo' align='center' width='70%'>
				<tr class='encabezadoTabla'>
					<td colspan='".$colspanTabla."' align='center'>CANTIDADES ".$descMov."</td>
				</tr>
				<tr class='encabezadoTabla' align='center'>
					<td>Botiqu&iacute;n</td>
					<td colspan='".$colspanResponsable."'>Responsable</td>
					<td>Cantidad</td>
					<td></td>
				</tr>";

			$cantTotal = 0;
			foreach($arrayInsumos as $keyInsumo => $valueInsumo) {
				
				if($valueInsumo['cantidad']>0)
				{
					if ($fila_lista=='Fila1')
						$fila_lista = "Fila2";
					else
						$fila_lista = "Fila1";

					$cantTotal += $valueInsumo['cantidad'];

					$html .= "
						<tr class='".$fila_lista."'>
							<td class='botiquin_".$valueInsumo['botiquin']."' id='botiquin_".$valueInsumo['botiquin']."_".$valueInsumo['auxiliar']."' align='center'>".$valueInsumo['botiquin']."-".$valueInsumo['desBotiquin']."</td>";
					if($fotoAuxiliares=="on") {
						$longitud = strlen($valueInsumo['auxiliar']);
						$codigoAuxiliar = $valueInsumo['auxiliar'];
						if($longitud>5) {
							$codigoAuxiliar = substr($valueInsumo['auxiliar'],$longitud-5,$longitud);
						}
						$urlFotoAuxiliar = consultarFoto($conex,$wemp_pmla,$wbasedato,$codigoAuxiliar,$valueInsumo['empresa']);

						$html .= "<td align='center' width='65px'>
								<img class='lightbox' id='fotoAuxiliar_".$valueInsumo['auxiliar']."' src='".$urlFotoAuxiliar."'  width=65px height=75px>
								<img class='fotoAuxiliar_".$valueInsumo['auxiliar']."' id='fotoAuxiliar_Apliada".$valueInsumo['auxiliar']."' nombre='".utf8_encode($valueInsumo['nombre'])."' src='".$urlFotoAuxiliar."' style='display:none' height='700px'/>
							  </td>";
					}
					
					$html .= "<td>".$valueInsumo['auxiliar']."-".utf8_encode($valueInsumo['nombre'])."</td>
							  <td align='center'>".$valueInsumo['cantidad']."</td>
							  <td align='center' onClick='pintarDetalleMovimiento(this,\"".$codMovimiento."\",\"".$insumo."\",\"".$valueInsumo['auxiliar']."\",\"".utf8_encode($valueInsumo['nombre'])."\");' style='cursor:pointer;'>Ver detalle</td>
							  </tr>";
				}
				
			}

			$html .= "<tr class='encabezadoTabla' align='center'>
						<td colspan='".($colspanTabla-1)."' align='right'>Total</td>
						<td>".$cantTotal."</td>
						</tr>
			</table>";
		} else {
			$html .= "	<br>
						<p align='center'>
							<b>No hay pedidos de insumos pendientes.</b>
						</p>";
		}
		$html .= "</div>";

		return $html;
	}
	
	function consultarDetallado($codMovimiento,$insumo,$codAuxiliar,$nombreAuxiliar,$valueForm)
	{
		global $conex;
		global $wbasedatoMov;
		global $wemp_pmla;
		
		$wbasedato_tcx    = consultarAliasPorAplicacion( $conex, $wemp_pmla, "tcx");
		$wbasedato_cliame = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion");
		
		$params = array();
		parse_str($valueForm, $params);
		
		
		$arrayCondiciones = condiciones($params);

		
		$rangoFecha = $arrayCondiciones['rangoFecha'];
		$rangoFechaPedido = $arrayCondiciones['rangoFechaPedido'];
		$condHistoria = $arrayCondiciones['condHistoria'];
		$condHistoriaPed = $arrayCondiciones['condHistoriaPed'];
		$condIngreso = $arrayCondiciones['condIngreso'];
		$condIngresoPed = $arrayCondiciones['condIngresoPed'];
		$condicionBot = $arrayCondiciones['condicionBot'];
		$condBotPed = $arrayCondiciones['condBotPed'];
		$cco = $arrayCondiciones['cco'];
		$ccoPed = $arrayCondiciones['ccoPed'];
		$conInsumo = $arrayCondiciones['conInsumo'];
		$conInsumoPed  = $arrayCondiciones['conInsumoPed'];
		$conAux = $arrayCondiciones['conAux'];
		$conAuxPed = $arrayCondiciones['conAuxPed'];
		
		
		if($codMovimiento=="PD")
		{
			$botiquin = $params["botiquin"] != "" ? " AND Pedbot = '" .$params["botiquin"]. "'" : "";
			$cco = $params["cco"] != "" ? " AND Pedcco = '" .$params["cco"]. "'" : "";
			
			
							
			$queryDetalle = " SELECT Dpehis AS historia,Dpeing AS ingreso,Pedcco AS cco,Dpehab AS habitacion,Dpeped AS cantidad,a.Fecha_data AS fecha,a.Hora_data AS hora,".$codAuxiliar." AS usuario,'' AS justificacion,Pacno1,Pacno2,Pacap1,Pacap2,Cconom,'' AS Descripcion
								FROM ".$wbasedatoMov."_000231 a,".$wbasedatoMov."_000230,root_000037,root_000036,".$wbasedatoMov."_000011
							   WHERE Dpeins='".$insumo."'
								 AND Dpeped>0 
								 ".$condBotPed."
								 ".$ccoPed."
								 ".$rangoFechaPedido."
								 ".$condHistoriaPed."
								 ".$condIngresoPed."
								 AND Dpeest='on'
								 AND Pedcod=Dpecod
								 AND Pedaux='".$codAuxiliar."'
								 AND Pedest='on'
								 AND Orihis=Dpehis
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
								 AND Ccocod=Pedcco
								 AND Ccoest='on'
							ORDER BY Pacno1,Pacno2,Pacap1,Pacap2,habitacion,historia,ingreso,fecha,hora;";
		}
		else
		{
			$botiquin = $params["botiquin"] != "" ? " AND Movbot = '" .$params["botiquin"]. "'" : "";
			$cco = $params["cco"] != "" ? " AND Movcco = '" .$params["cco"]. "'" : "";
			
			$ccoTipo = consultarTipoCco($conex,$wbasedatoMov,$params['cco']);
		
			if($ccoTipo=="ayudaDiagnostica")
			{
				// $prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedatoMov,$params['cco'],false);
				// $prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
				
				// $qDetalle = " SELECT Movhis AS historia,Moving AS ingreso,Movcco AS cco,Movhab AS habitacion,Movcmo AS cantidad,Movfmo AS fecha,Movhmo AS hora,Movumo AS usuario,Movjmo AS justificacion,Pacno1,Pacno2,Pacap1,Pacap2,Cconom,Descripcion,'' AS Turnom
								// FROM ".$wbasedatoMov."_000228,root_000037,root_000036,".$wbasedatoMov."_000011,usuarios,".$prefijoTablaAyudasDiagnosticas."_000023 
							   // WHERE Movins='".$insumo."'
								 // AND Movaux='".$codAuxiliar."'
								 // AND Movmov='".$codMovimiento."'
								 // ".$condicionBot."
								 // ".$rangoFecha."
								 // ".$condHistoria."
								 // ".$condIngreso."
								 // AND Movtur='0'
								 // AND Movest='on'
								 // AND Orihis=Movhis
								 // AND Oriori='".$wemp_pmla."'
								 // AND Pacced=Oriced
								 // AND Pactid=Oritid
								 // AND Ccocod=Movcco
								 // AND Ccoest='on'
								 // AND Codigo=Movumo
								 // AND ".$prefijoCampos."tip=Oritid
								 // AND ".$prefijoCampos."doc=Oriced
								 // AND ".$prefijoCampos."est='on'
								 // AND ".$prefijoCampos."fpr!='on'
								 // AND ".$prefijoCampos."acp!=''";
								 
				$prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedatoMov,$params['cco'],false);
				$prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
										
				$sql = "SELECT Ccotct 
						  FROM ".$wbasedatoMov."_000011 
						 WHERE ccocod = '".$params['cco']."'";
				 
				$resTablasCitas = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());		   
				$resNum 		= mysql_num_rows($resTablasCitas);
										  
				$rowsTablasCitas = mysql_fetch_array( $resTablasCitas );
				
				list( $tablaTurnero, $tipoDocumento, $numeroDocumento, $datosCondicionesExtras ) = explode( ",", $rowsTablasCitas['Ccotct'] );
				
				if( !empty($datosCondicionesExtras) ){
					$arrayCondicionesExtras = explode( "-", $datosCondicionesExtras );
					
					$condicionesExtrasTurnero = " AND ".implode( " AND ", $arrayCondicionesExtras );
				}
				
				$qDetalle = " SELECT Movhis AS historia,Moving AS ingreso,Movcco AS cco,Movhab AS habitacion,Movcmo AS cantidad,Movfmo AS fecha,Movhmo AS hora,Movumo AS usuario,Movjmo AS justificacion,Pacno1,Pacno2,Pacap1,Pacap2,Cconom,Descripcion,'' AS Turnom
							FROM ".$wbasedatoMov."_000228,root_000037,root_000036,".$wbasedatoMov."_000011,usuarios,".$prefijoTablaAyudasDiagnosticas." 
						   WHERE Movins='".$insumo."'
							 AND Movaux='".$codAuxiliar."'
							 AND Movmov='".$codMovimiento."'
							 ".$condicionBot."
							 ".$rangoFecha."
							 ".$condHistoria."
							 ".$condIngreso."
							 AND Movtur='0'
							 AND Movest='on'
							 AND Orihis=Movhis
							 AND Oriori='".$wemp_pmla."'
							 AND Pacced=Oriced
							 AND Pactid=Oritid
							 AND Ccocod=Movcco
							 AND Ccoest='on'
							 AND Codigo=Movumo
							 AND ".$tipoDocumento."=Oritid
							 AND ".$numeroDocumento."=Oriced
							 $condicionesExtrasTurnero";
			}
			else
			{
				$qDetalle = " SELECT Movhis AS historia,Moving AS ingreso,Movcco AS cco,Movhab AS habitacion,Movcmo AS cantidad,Movfmo AS fecha,Movhmo AS hora,Movumo AS usuario,Movjmo AS justificacion,Pacno1,Pacno2,Pacap1,Pacap2,Cconom,Descripcion,'' AS Turnom
								FROM ".$wbasedatoMov."_000228,root_000037,root_000036,".$wbasedatoMov."_000011,usuarios
							   WHERE Movins='".$insumo."'
								 AND Movaux='".$codAuxiliar."'
								 AND Movmov='".$codMovimiento."'
								 ".$condicionBot."
								 ".$cco."
								 ".$rangoFecha."
								 ".$condHistoria."
								 ".$condIngreso."
								 AND Movtur='0'
								 AND Movest='on'
								 AND Orihis=Movhis
								 AND Oriori='".$wemp_pmla."'
								 AND Pacced=Oriced
								 AND Pactid=Oritid
								 AND Ccocod=Movcco
								 AND Ccoest='on'
								 AND Codigo=Movumo";
			}
			
			
			$queryDetalle = "   ".$qDetalle."
								 
							   UNION
								 
							  SELECT  c100.Pachis AS historia, c101.Ingnin AS ingreso, m18.Ubisac AS cco, CONCAT( 'Qx ', Turqui ) as habitacion,Movcmo AS cantidad,Movfmo AS fecha,Movhmo AS hora,Movumo AS usuario,Movjmo AS justificacion, c100.Pacno1, c100.Pacno2, c100.Pacap1, c100.Pacap2, mx11.Cconom,Descripcion, Turnom
								FROM    ".$wbasedato_tcx."_000011 AS tcx11
						   LEFT JOIN ".$wbasedato_cliame."_000100 AS c100 
								  ON (tcx11.Turhis=c100.Pachis)
						   LEFT JOIN ".$wbasedato_cliame."_000101 AS c101 
								  ON (c100.Pachis=c101.Inghis and tcx11.Turnin=c101.Ingnin)
						   LEFT JOIN ".$wbasedatoMov."_000018 as m18 
								  ON (Ubihis=c101.Inghis and m18.Ubiing=c101.Ingnin and m18.Ubihis != '')
						   LEFT JOIN ".$wbasedato_tcx."_000012 AS tc12 
								  ON (tcx11.Turqui=tc12.Quicod)
						   LEFT JOIN ".$wbasedatoMov."_000011 AS mx11 
								  ON (tc12.Quicco=mx11.Ccocod),
									 ".$wbasedatoMov."_000228,
									 usuarios
								WHERE tcx11.Turtur=Movtur
								 AND Movtur!='0' 
								 AND Movins='".$insumo."'
								 AND Movaux='".$codAuxiliar."'
								 AND Movmov='".$codMovimiento."'
								 ".$botiquin."
								 ".$cco."
								 ".$rangoFecha."
								 ".$condHistoria."
								 ".$condIngreso."
								 AND Movest='on'
								 AND Codigo = Movumo
								 
							ORDER BY Pacno1,Pacno2,Pacap1,Pacap2,habitacion,historia,ingreso,fecha,hora;";
							
							
			
		}
		
		// echo $queryDetalle;
		
		$resDetalle = mysql_query($queryDetalle, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDetalle . " - " . mysql_error());		   
		$numDetalle = mysql_num_rows($resDetalle);
		
		$arrayDetalle = array();
		$contDetalle = 0;
		if($numDetalle>0)
		{
			while($rowDetalle = mysql_fetch_array($resDetalle))
			{
				$arrayDetalle[$contDetalle]['historia'] = $rowDetalle['historia'];
				$arrayDetalle[$contDetalle]['ingreso'] = $rowDetalle['ingreso'];
				$arrayDetalle[$contDetalle]['cco'] = $rowDetalle['cco'];
				$arrayDetalle[$contDetalle]['habitacion'] = $rowDetalle['habitacion'];
				$arrayDetalle[$contDetalle]['cantidad'] = $rowDetalle['cantidad'];
				$arrayDetalle[$contDetalle]['fecha'] = $rowDetalle['fecha'];
				$arrayDetalle[$contDetalle]['hora'] = $rowDetalle['hora'];
				$arrayDetalle[$contDetalle]['usuario'] = $rowDetalle['usuario'];
				$arrayDetalle[$contDetalle]['justificacion'] = utf8_encode($rowDetalle['justificacion']);
				$arrayDetalle[$contDetalle]['nombre'] = utf8_encode($rowDetalle['Pacno1']." ".$rowDetalle['Pacno2']." ".$rowDetalle['Pacap1']." ".$rowDetalle['Pacap2']);
				$arrayDetalle[$contDetalle]['descCco'] = utf8_encode($rowDetalle['Cconom']);
				$arrayDetalle[$contDetalle]['nombreUsuario'] = utf8_encode($rowDetalle['Descripcion']);
				
				if(trim($arrayDetalle[$contDetalle]['nombre'])=="")
				{
					$arrayDetalle[$contDetalle]['nombre'] = $rowDetalle['Turnom'];
				}
				
				
				$contDetalle++;
			}
		}
		// var_dump($arrayDetalle);
		
		
		// $arrayDetalle = utf8_encode($arrayDetalle);
		
		
		$descripcionMovimiento = "";
		$descMov = "";
		if($codMovimiento=="PD")
		{
			$descripcionMovimiento = "cargados";
			$descMov = "DETALLE CANTIDADES PEDIDAS por <br>  ".$nombreAuxiliar;
		}
		elseif($codMovimiento=="CR")
		{
			$descripcionMovimiento = "cargados";
			$descMov = "DETALLE CANTIDADES DISPENSADAS a  <br> ".$nombreAuxiliar;
		}
		elseif($codMovimiento=="AP")
		{
			$descripcionMovimiento = "aplicados";
			$descMov = "DETALLE CANTIDADES APLICADAS por  <br> ".$nombreAuxiliar;
		}
		elseif($codMovimiento=="DV")
		{
			$descripcionMovimiento = "devueltos";
			$descMov = "DETALLE CANTIDADES DEVUELTAS por <br> ".$nombreAuxiliar;
		}
		
		
		
		
		$colspanTabla = 6;
		$encabezadoJustificacion = "";
		if($codMovimiento=="AP")
		{
			$colspanTabla++;
			$encabezadoJustificacion = "<td>Justificaci&oacute;n</td>";
		}
		
		$encabezadoUsuario= "";
		if($codMovimiento=="CR")
		{
			$colspanTabla++;
			$encabezadoUsuario = "<td>Dispensado por</td>";
		}
		else if($codMovimiento=="DV")
		{
			$colspanTabla++;
			$encabezadoUsuario = "<td>Registra la devoluci&oacute;n</td>";
		}
		
		
		
		$html = "";
		$html .= "<br><br><table align='center'>
					<tr class='encabezadoTabla'>
						<td colspan='".$colspanTabla."' align='center'>".$descMov."</td>
					</tr>
					<tr class='encabezadoTabla' align='center'>
						<td>Paciente</td>
						<td>Historia</td>
						<td>Cantidad</td>
						<td>Fecha y hora</td>
						".$encabezadoUsuario."
						".$encabezadoJustificacion."
						<td>Habitaci&oacute;n</td>
						<td>Centro de costos</td>
					</tr>";
		foreach($arrayDetalle as $keyDetalle => $valueDetalle)
		{
			if ($fila_lista=='Fila1')
				$fila_lista = "Fila2";
			else
				$fila_lista = "Fila1";
			
			$detalleJustificacion = "";
			if($codMovimiento=="AP")
			{
				$detalleJustificacion = "<td><textarea readOnly='readOnly'>".$valueDetalle['justificacion']."</textarea></td>";
			}
			
			$detalleUsuario = "";
			if($codMovimiento=="CR" || $codMovimiento=="DV")
			{
				$detalleUsuario = "<td>".$valueDetalle['usuario']."-".$valueDetalle['nombreUsuario']."</td>";
			}
			
			$html .= "<tr class='".$fila_lista."'>
						<td>".$valueDetalle['nombre']."</td>
						<td align='center'>".$valueDetalle['historia']."-".$valueDetalle['ingreso']."</td>
						<td align='center'>".$valueDetalle['cantidad']."</td>
						<td align='center'>".$valueDetalle['fecha']." ".$valueDetalle['hora']."</td>
						".$detalleUsuario."
						".$detalleJustificacion."
						<td align='center'>".$valueDetalle['habitacion']."</td>
						<td>".$valueDetalle['cco']."-".$valueDetalle['descCco']."</td>
					</tr>";
		}
		
		$html .= "</table><br><br>";
		
		return $html;
		
	}
	
	function condiciones($params)
	{
		$rangoFecha = "";
		$rangoFechaPedido = "";
		if($params["fechini"] != "" && $params["fechfin"]!="")
		{
			$rangoFecha = " AND Movfec BETWEEN '".$params["fechini"]."' AND '".$params["fechfin"]."'";
			$rangoFechaPedido = " AND a.Fecha_data BETWEEN '".$params["fechini"]."' AND '".$params["fechfin"]."'";
		}
		else if($params["fechini"] != "")
		{
			$rangoFecha = " AND Movfec >= '".$params["fechini"]."'";
			$rangoFechaPedido = " AND a.Fecha_data >= '".$params["fechini"]."'";
		}
		else if($params["fechfin"] != "")
		{
			$rangoFecha = " AND Movfec <= '".$params["fechfin"]."'";
			$rangoFechaPedido = " AND a.Fecha_data <= '".$params["fechfin"]."'";
		}
		
		
		
		$condHistoria = "";
		$condHistoriaPed = "";
		$condIngreso = "";
		$condIngresoPed = "";
		if($params["historia"] != "")
		{
			$condHistoria = " AND Movhis = '".$params["historia"]."'";
			$condHistoriaPed = " AND Dpehis = '".$params["historia"]."'";
			
			if($params["ingreso"] != "")
			{
				$condIngreso = " AND Moving = '".$params["ingreso"]."'";
				$condIngresoPed = " AND Dpeing = '".$params["ingreso"]."'";
			
			}
		}
		
		
		
		$condicionBot  = $params["botiquin"] != "" ? " AND Movbot = '" .$params["botiquin"]. "'" : "";
		$cco           = $params["cco"] != "" ? " AND Movcco = '" .$params["cco"]. "'" : "";
		$conInsumo     = $params["insumo"] != "" ? " AND Movins = '" .$params["insumo"]. "'" : "";
		$conAux        = $params["auxiliar"] != "" ? " AND Movaux = '" .$params["auxiliar"]. "'" : "";
		
		
		$condBotPed    = $params["botiquin"] != "" ? " AND Pedbot = '" .$params["botiquin"]. "'" : "";
		$ccoPed        = $params["cco"] != "" ? " AND Pedcco = '" .$params["cco"]. "'" : "";
		$conInsumoPed  = $params["insumo"] != "" ? " AND Dpeins = '" .$params["insumo"]. "'" : "";
		$conAuxPed     = $params["auxiliar"] != "" ? " AND Pedaux = '" .$params["auxiliar"]. "'" : "";
		
		
		
		$arrayCondiciones = array();
		
		$arrayCondiciones['rangoFecha'] = $rangoFecha;
		$arrayCondiciones['rangoFechaPedido'] = $rangoFechaPedido;
		$arrayCondiciones['condHistoria'] = $condHistoria;
		$arrayCondiciones['condHistoriaPed'] = $condHistoriaPed;
		$arrayCondiciones['condIngreso'] = $condIngreso;
		$arrayCondiciones['condIngresoPed'] = $condIngresoPed;
		$arrayCondiciones['condicionBot'] = $condicionBot;
		$arrayCondiciones['condBotPed'] = $condBotPed;
		$arrayCondiciones['cco'] = $cco;
		$arrayCondiciones['ccoPed'] = $ccoPed;
		$arrayCondiciones['conInsumo'] = $conInsumo;
		$arrayCondiciones['conInsumoPed'] = $conInsumoPed;
		$arrayCondiciones['conAux'] = $conAux;
		$arrayCondiciones['conAuxPed'] = $conAuxPed;
		
		return $arrayCondiciones;
	}
	
	
	//////////////////////////////////////////////////////////FIN FUNCIONES //////////////////////////////////////////


	///////////////////////////////////////       CONSULTAS AJAX          //////////////////////////////////////////////////
	if(isset($consultaAjax)){
		if($action == "buscarInsumos"){
			$params = array();
			parse_str($valueForm, $params);			
			$arrayCondiciones = condiciones($params);
			
			$rangoFecha = $arrayCondiciones['rangoFecha'];
			$rangoFechaPedido = $arrayCondiciones['rangoFechaPedido'];
			$condHistoria = $arrayCondiciones['condHistoria'];
			$condHistoriaPed = $arrayCondiciones['condHistoriaPed'];
			$condIngreso = $arrayCondiciones['condIngreso'];
			$condIngresoPed = $arrayCondiciones['condIngresoPed'];
			$condicionBot = $arrayCondiciones['condicionBot'];
			$condBotPed = $arrayCondiciones['condBotPed'];
			$cco = $arrayCondiciones['cco'];
			$ccoPed = $arrayCondiciones['ccoPed'];
			$conInsumo = $arrayCondiciones['conInsumo'];
			$conInsumoPed  = $arrayCondiciones['conInsumoPed'];
			$conAux = $arrayCondiciones['conAux'];
			$conAuxPed = $arrayCondiciones['conAuxPed'];
			
					
			// -----------------------------		
			
			$queryDetalleCantidades = "SELECT Movins AS codInsumo,Movmov AS Movimiento,SUM(Movcmo) AS cantidad,Artgen,Unides
										FROM ".$wbasedatoMov."_000228 a,".$wbasedatoMov."_000026,".$wbasedatoMov."_000027
									   WHERE Movest='on'
										 AND Movmov!='AA'
										 AND Movaux!=''
										 ".$condicionBot."  
										 ".$cco."  
										 ".$conAux."
										 ".$conInsumo."
										 ".$rangoFecha."
										 ".$condHistoria."
										 ".$condIngreso."
										 AND Artcod=Movins
										 AND Artest='on'
										 AND Unicod=Artuni
										 AND Uniest='on'
									GROUP BY Movins,Movmov";
			if($params['cco']!="")
			{
				$ccoTipo = consultarTipoCco($conex,$wbasedatoMov,$params['cco']);
		
				if($ccoTipo=="ayudaDiagnostica")
				{
					// $prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedatoMov,$params['cco'],false);
					// $prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
				
					// $queryDetalleCantidades = "SELECT Movins AS codInsumo,Movmov AS Movimiento,SUM(Movcmo) AS cantidad,Artgen,Unides
												// FROM ".$wbasedatoMov."_000228 a,".$wbasedatoMov."_000026,".$wbasedatoMov."_000027,root_000037,".$prefijoTablaAyudasDiagnosticas."_000023 
											   // WHERE Movest='on'
												 // AND Movmov!='AA'
												 // AND Movaux!=''
												 // ".$condicionBot."  
												 // ".$conAux."
												 // ".$conInsumo."
												 // ".$rangoFecha."
												 // ".$condHistoria."
												 // ".$condIngreso."
												 // AND Artcod=Movins
												 // AND Artest='on'
												 // AND Unicod=Artuni
												 // AND Uniest='on'
												 // AND Orihis=Movhis
												 // AND Oriing=Moving
												 // AND Oriori='01'
												 // AND ".$prefijoCampos."tip=Oritid
												 // AND ".$prefijoCampos."doc=Oriced
												 // AND ".$prefijoCampos."est='on'
												 // AND ".$prefijoCampos."fpr!='on'
												 // AND ".$prefijoCampos."acp!=''
											// GROUP BY Movins,Movmov";
											
					$prefijoTablaAyudasDiagnosticas = consultarTablaPacientesAyudasDiagnosticas($conex,$wbasedatoMov,$params['cco'],false);
					$prefijoCampos = getPrefixTables($prefijoTablaAyudasDiagnosticas);
											
					$sql = "SELECT Ccotct 
							  FROM ".$wbasedatoMov."_000011 
							 WHERE ccocod = '".$params['cco']."'";
					 
					$resTablasCitas = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());		   
					// $resNum 		= mysql_num_rows($resTablasCitas);
											  
					$rowsTablasCitas = mysql_fetch_array( $resTablasCitas );
					
					list( $tablaTurnero, $tipoDocumento, $numeroDocumento, $datosCondicionesExtras ) = explode( ",", $rowsTablasCitas['Ccotct'] );
					
					if( !empty($datosCondicionesExtras) ){
						$arrayCondicionesExtras = explode( "-", $datosCondicionesExtras );
						
						$condicionesExtrasTurnero = " AND ".implode( " AND ", $arrayCondicionesExtras );
					}
					
					$queryDetalleCantidades = "SELECT Movins AS codInsumo,Movmov AS Movimiento,SUM(Movcmo) AS cantidad,Artgen,Unides
												FROM ".$wbasedatoMov."_000228 a,".$wbasedatoMov."_000026,".$wbasedatoMov."_000027,root_000037,".$prefijoTablaAyudasDiagnosticas." 
											   WHERE Movest='on'
												 AND Movmov!='AA'
												 AND Movaux!=''
												 ".$condicionBot."  
												 ".$conAux."
												 ".$conInsumo."
												 ".$rangoFecha."
												 ".$condHistoria."
												 ".$condIngreso."
												 AND Artcod=Movins
												 AND Artest='on'
												 AND Unicod=Artuni
												 AND Uniest='on'
												 AND Orihis=Movhis
												 AND Oriing=Moving
												 AND Oriori='01'
												 AND ".$tipoDocumento."=Oritid
												 AND ".$numeroDocumento."=Oriced
												 $condicionesExtrasTurnero
											GROUP BY Movins,Movmov";
				}
			}
			
			$sql  =" ".$queryDetalleCantidades."
					   
					   UNION

					  SELECT Dpeins AS codInsumo,'PD' AS Movimiento,SUM(Dpeped) AS cantidad,Artgen,Unides 
						FROM ".$wbasedatoMov."_000231 a,".$wbasedatoMov."_000230,".$wbasedatoMov."_000026,".$wbasedatoMov."_000027
					   WHERE Dpeped>0
						 AND Dpeest='on'
						 AND Pedcod=Dpecod
						 ".$condBotPed."  
						 ".$ccoPed."  
						 ".$conAuxPed."
						 ".$conInsumoPed."
						 ".$rangoFechaPedido."
						 ".$condHistoriaPed."
						 ".$condIngresoPed."
						 AND Pedest='on'
						 AND Artcod=Dpeins
						 AND Artest='on'
						 AND Unicod=Artuni
						 AND Uniest='on'
					GROUP BY Dpeins

					ORDER BY Artgen;";
			
			
			// $sql  ="  SELECT Movins AS codInsumo,Movmov AS Movimiento,SUM(Movcmo) AS cantidad,Artgen,Unides
						// FROM ".$wbasedatoMov."_000228 a,".$wbasedatoMov."_000026,".$wbasedatoMov."_000027
					   // WHERE Movest='on'
						 // AND Movmov!='AA'
						 // AND Movaux!=''
						 // ".$condicionBot."  
						 // ".$cco."  
						 // ".$conAux."
						 // ".$conInsumo."
						 // ".$rangoFecha."
						 // ".$condHistoria."
						 // ".$condIngreso."
						 // AND Artcod=Movins
						 // AND Artest='on'
						 // AND Unicod=Artuni
						 // AND Uniest='on'
					// GROUP BY Movins,Movmov

					   
					   // UNION

					  // SELECT Dpeins AS codInsumo,'PD' AS Movimiento,SUM(Dpeped) AS cantidad,Artgen,Unides 
						// FROM ".$wbasedatoMov."_000231 a,".$wbasedatoMov."_000230,".$wbasedatoMov."_000026,".$wbasedatoMov."_000027
					   // WHERE Dpeped>0
						 // AND Dpeest='on'
						 // AND Pedcod=Dpecod
						 // ".$condBotPed."  
						 // ".$ccoPed."  
						 // ".$conAuxPed."
						 // ".$conInsumoPed."
						 // ".$rangoFechaPedido."
						 // ".$condHistoriaPed."
						 // ".$condIngresoPed."
						 // AND Pedest='on'
						 // AND Artcod=Dpeins
						 // AND Artest='on'
						 // AND Unicod=Artuni
						 // AND Uniest='on'
					// GROUP BY Dpeins

					// ORDER BY Artgen;";
					
			$res = mysql_query($sql, $conex);
			$num = mysql_num_rows($res);
			$newArray = array();
			
			if($num>0)
			{
				while($row = mysql_fetch_assoc($res)){
				
					if(!$newArray[$row["codInsumo"]])
					{
						$newArray[$row["codInsumo"]]['cantidadPedida'] = 0;
						$newArray[$row["codInsumo"]]['cantidadDispensada'] = 0;
						$newArray[$row["codInsumo"]]['cantidadAplicada'] = 0;
						$newArray[$row["codInsumo"]]['cantidadDevuelta'] = 0;
					}
				
				
					$newArray[$row["codInsumo"]]['codigo'] = $row["codInsumo"];
					$newArray[$row["codInsumo"]]['nombre'] = utf8_encode($row["Artgen"]);
					$newArray[$row["codInsumo"]]['presentacion'] = utf8_encode($row["Unides"]);
					
					
					if($row["Movimiento"]=="PD")
					{
						$newArray[$row["codInsumo"]]['cantidadPedida'] += utf8_encode($row["cantidad"]);
					}
					else if($row["Movimiento"]=="CR")
					{
						$newArray[$row["codInsumo"]]['cantidadDispensada'] += utf8_encode($row["cantidad"]);
					}
					else if($row["Movimiento"]=="AP")
					{
						$newArray[$row["codInsumo"]]['cantidadAplicada'] += utf8_encode($row["cantidad"]);
					}
					else if($row["Movimiento"]=="DV")
					{
						$newArray[$row["codInsumo"]]['cantidadDevuelta'] += utf8_encode($row["cantidad"]);
					}
				}
			}
			
			
			$newArr = array();
			
			if(count($newArray)>0)
			{
				foreach($newArray as $key =>$value)
				{
					
					$newArr[] = array("codigo" => $value["codigo"],
									  "nombre" => utf8_encode($value["nombre"]),
									  "presentacion" => utf8_encode($value["presentacion"]),
									  "cantidadPedida" => $value["cantidadPedida"],
									  "cantidadDispensada" => $value["cantidadDispensada"],
									  "cantidadAplicada" => $value["cantidadAplicada"],
									  "cantidadDevuelta" => $value["cantidadDevuelta"]
					);
					
					
				}
			}

			echo json_encode($newArr);
			exit();
		}

		if($action == "detallePedidosInsumo"){
			$data = detallePedidosInsumo($movimiento,$insumo, $valueForm);
			echo json_encode($data);
			return;
		}

		if($action == "detalleMovimientoAuxiliar"){
			$data = consultarDetallado($movimiento,$insumo,$codAuxiliar,$nombreAuxiliar, $valueForm);
			echo json_encode($data);
			return;
		}
		
			
		
		
	} else {

		if(!isset($_SESSION['user']) ){
			echo "<br /><br /><br /><br />
					  <div style='color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;' id='div_sesion_muerta'>
						  [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
					 </div>";
			return;
		}

		$wtitulo = "REPORTE DE INSUMOS PEDIDOS";
		encabezado($wtitulo, $wactualiz, 'clinica');

		echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";
?>

<html>
<head>
<title>REPORTE DE INSUMOS DETALLADO</title>
<script src="../../../include/root/jquery.min.js"></script>
<script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>

<link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

<script src="../../../include/root/bootstrap.min.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script>

<link type="text/css" href="../../../include/root/select2/select2.min.css" rel="stylesheet"/>
<script type='text/javascript' src='../../../include/root/select2/select2.min.js'></script>

<!-- Inicio estilos css -->
<style type="text/css">

	body {
		 font-family: verdana;
		 font-size: 10pt;
	}
	.modal-header, h4, .close {
		background-color: #004b8e; /*#5cb85c;*/
		color:white !important;
		text-align: center;
		font-size: 24px;
	}
	.modal-footer {
		background-color: #f9f9f9;
	}
	.obligatorio {
		color: red
	}
	.modal-body {
		max-height: calc(100vh - 210px);
		overflow-y: auto;
		background-color: InactiveBorder
	}

	table {
		border-collapse: separate;
		border-spacing: 2px;
	}
</style>
<!-- fin estilos css -->

<script type="text/javascript">
$(document).ready( function (){

	$('#botiquin').select2();
	$('#cco').select2();
	$('#insumo').select2();
	$('#auxiliar').select2();
	
	$("#tblInsumos").stickyTableHeaders();

    $( "#fechini" ).datepicker({
        showOn: "button",
        buttonImage: "../../images/medical/root/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+0',
        onSelect: function( selectedDate ) {
            $( "#fechfin" ).datepicker( "option", "minDate", selectedDate );
        }
    });

    $( "#fechfin" ).datepicker({
        showOn: "button",
        buttonImage: "../../images/medical/root/calendar.gif",
        buttonImageOnly: true,
        changeMonth: true,
        changeYear: true,
        yearRange: '-10:+0',
        onSelect: function( selectedDate ) {
            $( "#fechini" ).datepicker( "option", "maxDate", selectedDate );
        }
    });

     
	$('#btnBuscar').click(function(){
		
		$("#msjEspere").show();
		
		var valueForm = $("#formBusqueda").serialize();

		$.post("reportePedidosInsumosDetallado.php",
		{
			consultaAjax:   		'on',
			action:         		'buscarInsumos',
			valueForm:         		valueForm,
			wemp_pmla:        		$("#wemp_pmla").val()
			}, function(respuesta){
				
				$("#resultado").empty();
				
				$("#msjEspere").hide();
				
				var cont = 0;

				var fila = "fila1";

				jQuery.each(respuesta, function(){
					
					var onclickPedido = '';
					if(this.cantidadPedida>0)
					{
						var onclickPedido = 'onClick="verDetalleInsumo(\'PD\',\''+this.codigo+'\',\''+this.nombre+'\')"';
					}
					
					var onclickDispensado = '';
					if(this.cantidadDispensada>0)
					{
						var onclickDispensado = 'onClick="verDetalleInsumo(\'CR\',\''+this.codigo+'\',\''+this.nombre+'\')"';
					}
					
					var onclickAplicado = '';
					if(this.cantidadAplicada>0)
					{
						var onclickAplicado = 'onClick="verDetalleInsumo(\'AP\',\''+this.codigo+'\',\''+this.nombre+'\')"';
					}
					else
					{
						// this.cantidadAplicada=0;
					}
					
					var onclickDevuelto = '';
					if(this.cantidadDevuelta>0)
					{
						var onclickDevuelto = 'onClick="verDetalleInsumo(\'DV\',\''+this.codigo+'\',\''+this.nombre+'\')"';
					}
									

					var stringTr = '<tr class="'+fila+'"><td  align="center">'+this.codigo+'</td><td>'+this.nombre+'</td><td align="center">'+this.presentacion+'</td><td align="center" '+onclickPedido+' style="cursor:pointer" title="Ver detalle"><b>'+this.cantidadPedida+'</b></td><td align="center" '+onclickDispensado+' style="cursor:pointer" title="Ver detalle"><b>'+this.cantidadDispensada+'</b></td><td align="center" '+onclickAplicado+' style="cursor:pointer" title="Ver detalle"><b>'+this.cantidadAplicada+'</b></td><td align="center" '+onclickDevuelto+' style="cursor:pointer" title="Ver detalle"><b>'+this.cantidadDevuelta+'</b></td></tr>'
					$('#tblInsumos > tbody:last').append(stringTr);

					fila = fila == "fila1" ? "fila2" : "fila1";
					cont++;
				});

				if(cont == 0){
					var stringTr = '<tr class='+fila+'><td colspan="7" align="center"> No hay resultados para la búsqueda</td></tr>'
					$('#tblInsumos > tbody:last').append(stringTr);
				}
		}, 'json');
	});
	
});

	function verDetalleInsumo(movimiento,insumo,descInsumo)
	{
		$.post("reportePedidosInsumosDetallado.php",
		{
			consultaAjax 	: 'on',
			action			: 'detallePedidosInsumo',
			wemp_pmla		: $('#wemp_pmla').val(),
			movimiento		: movimiento,
			insumo			: insumo,
			valueForm      : $("#formBusqueda").serialize()
		}
		, function(data) {

			$("#tituloDetallado").html("Insumo: " +descInsumo);
			$("#bodyDetallado").html(data);
			$("#verDetallado").modal();

			$('.lightbox').click(function() {
				var imagen = $(this).attr('id');
				var src    = $('.'+imagen).attr('src');
				var name   = $('.'+imagen).attr('nombre');
				var img    = '<img src="'+src+'" >';

				$("#tituloFoto").html(name);
				$("#bodyFoto").html(img);
				$('#verFoto').css('width', "auto");
				$('#verFoto').css('height', "100%");
				$("#verFoto").modal();
			});
			
			$( ".tooltipFechaCantidad" ).tooltip();

		},'json');
	}
	
	function pintarDetalleMovimiento(campo,movimiento,insumo,codAuxiliar,nombreAuxiliar)
	{
		$.post("reportePedidosInsumosDetallado.php",
		{
			consultaAjax 	: 'on',
			action			: 'detalleMovimientoAuxiliar',
			wemp_pmla		: $('#wemp_pmla').val(),
			movimiento		: movimiento,
			insumo			: insumo,
			codAuxiliar		: codAuxiliar,
			nombreAuxiliar	: nombreAuxiliar,
			valueForm       : $("#formBusqueda").serialize()
		}
		, function(data) {

			$("#tituloMovimientoDetallado").html($("#tituloDetallado").html());
			$("#bodyMovimientoDetallado").html(data);
			$("#verMovimientoDetallado").modal();

		},'json');
		
	}
	
	function soloNumeros(e){
		var key = window.Event ? e.which : e.keyCode;
		
		if(key == 13)
		{
			$("#txtInsumo").focus();
			return false;
		}
		
		return ((key >= 48 && key <= 57) || key<= 8);
	}

    $.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'], //['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        yearSuffix: '',
        changeYear: true,
        changeMonth: true,
        yearRange: '-10:+0'
    };
    $.datepicker.setDefaults($.datepicker.regional['esp']);

</script>
</head>

<body style='width: 97%'>

	<div style="text-align:right; padding-right:8%"><input name="button" style="width:100" onclick="window.close();" value="Cerrar" type="button"></div>

	<form id="formBusqueda">
		<table align="center" width="65%">
			<tbody>
				<tr class="encabezadotabla">
					<td colspan="4" align="center"><font size="4">Parámetros de búsqueda</font></td>
				</tr>
				<tr>
					<td class="fila1" width="20%">
						<b>Fecha inicial:</b>
					</td>
					<td class="fila2" width="30%">
						<input type="text" id="fechini" name="fechini" placeholder="AAAA-MM-DD" readOnly="readOnly">
						<?php //campoFechaDefecto("fechini", date("Y-m-d")); ?>
					</td>
					<td class="fila1"  width="20%">
						<b>Fecha final:</b>
					</td>
					<td class="fila2"  width="30%">
						<input type="text" id="fechfin" name="fechfin" placeholder="AAAA-MM-DD" readOnly="readOnly">
						<?php //campoFechaDefecto("fechfin", date("Y-m-d")); ?>
					</td>
				</tr>
				<tr>
					<td class="fila1" width="80px">
						<b>Botiqu&iacute;n:</b>
					</td>
					<td class="fila2">
						<select id='botiquin' name='botiquin' style='width:90%' >
							<option value=''>VER TODOS</option>
							<?php echo pintarFiltroBotiquin($conex); ?>
						</select>
					</td>
					<td class="fila1" width="80px">
						<b>Centro de costo:</b>
					</td>
					<td class="fila2" colspan="3">
						<select id='cco' name='cco' style='width:90%' >
							<option value=''>VER TODOS</option>
							<?php echo pintarFiltroCco($conex); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fila1" width="80px">
						<b>Insumo:</b>
					</td>
					<td class="fila2">
						<select name='insumo' id='insumo' style='width:90%'>
							<option value=''>VER TODOS</option>
							<?php echo pintarInsumos($conex); ?>
						</select>
					</td>
					<td class="fila1" width="80px">
						<b>Responsable:</b>
					</td>
					<td class="fila2" colspan="3">
						<select name='auxiliar' id='auxiliar'  style='width:90%'>
							<option value=''>VER TODOS</option>
							<?php echo pintarAuxiliares($conex, $wemp_pmla); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="fila1" width="80px">
						<b>Historia:</b>
					</td>
					<td class="fila2">
						<input type="text" class="form-control" id="historia" name="historia" onkeypress="return soloNumeros(event);">
					</td>
					<td class="fila1" width="80px">
						<b>Ingreso:</b>
					</td>
					<td class="fila2" colspan="3">
						<input type="text" class="form-control" id="ingreso" name="ingreso" onkeypress="return soloNumeros(event);">
					</td>
				</tr>
				<tr>
					<td class="encabezadotabla" align="center" colspan="4">
						<input type="button" id="btnBuscar" value="Buscar"  style="color:black">
					</td>
				</tr>
			</tbody>
		</table>
	</form>

	<br><br>
	
	<table align="center" border="0" id="tblInsumos" width="55%">
		<thead>
			<tr class="encabezadoTabla">
				<th width="20" rowspan="2" style="text-align: center;">Código</th>
				<th width="20" rowspan="2" style="text-align: center;">Descripción</th>
				<th width="20" rowspan="2" style="text-align: center;">Presentación</th>
				<th width="20" colspan="4" style="text-align: center;">Cantidad</th>
			</tr>
			<tr class="encabezadoTabla">
				<th width="20" style="text-align: center;">Pedida</th>
				<th width="20" style="text-align: center;">Dispensada</th>
				<th width="20" style="text-align: center;">Aplicada</th>
				<th width="20" style="text-align: center;">Devuelta</th>
			</tr>
		</thead>
		<tbody id="resultado">
		</tbody>
	</table>
	<br><br>
	
	<div id='msjEspere' style='display:none;' align='center'>
		<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...
	</div>


	<div class="modal fade" id="verDetallado" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloDetallado"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyDetallado">

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
   </div>

   <div class="modal fade" id="verFoto" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloFoto"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyFoto">

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
   </div>


	<div class="modal fade" id="verMovimientoDetallado" role="dialog">
		<div class="modal-dialog modal-lg ">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal">&times;</button>
				  <h4 id="tituloMovimientoDetallado"></h4>
				</div>
				<div class="modal-body" style="padding:10px 40px;" id="bodyMovimientoDetallado">

				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger btn-default pull-right" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
   </div>
</body>
</html>
<?php } 