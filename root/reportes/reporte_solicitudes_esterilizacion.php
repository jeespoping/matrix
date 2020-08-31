<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA: Reporte despachos central de esterilizacion          
//=========================================================================================================================================\\
// DESCRIPCION: 			Este programa genera un reporte de las cantidades despachadas por la central de materiales y esterilizacion a los demas cco y su respectivo costo,
// 							ademas permite generar a los usuarios que pertenecen a costos y presupuestos los costos que se deben cobrar a cada uno de los cco que realizan
// 							solicitudes a la central de esterlizacion en la tabla costosyp_000072
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2015-10-13
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//-------------------------------------------------------------------------------------------------------------------------------------------- \\
// 2019-11-12			- Se modifica el reporte para que consulte todos los costos de los insumos en un solo query y así evitar 
// 						  múltiples conexiones a Matrix Financiero
// 2019-11-06			- Se agrega filtro a root_000040 para que el query tome el indice y la consulta sea más rápida
// 2019-09-30			- Se agrega la conexion a Matrix financiero donde estan todas las tablas de costosyp ya que en producción 
// 						  dichas tablas no existen
// 						- Se comenta el contenido de la función registrarEnCostos() ya que no es necesario actualizar la tabla 
// 						  costosyp_000072
// 2016-09-20			Se corrige warning array vacio por cantidades
	
	$wactualiz='2019-11-12';
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
	
	$conex = obtenerConexionBD("matrix");
	
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function consultarCentroCostos($cco,$wemp_pmla)
	{
		global $wbasedato;
		global $conex;	
		
		$ccosto = explode(")",$cco);
		$query = " SELECT Cconom 
					FROM movhos_000011 
				   WHERE Ccocod='".$ccosto[1]."';";

		$resultado = mysql_query($query, $conex);
		$numres = mysql_num_rows($resultado);
		
		if($numres>0)
		{
			$row = mysql_fetch_array($resultado);
			$CentroCosto = "(".$wemp_pmla.")".$ccosto[1]."-".strtoupper(htmlentities($row[0]));
		}
		else
		{
			$query2 = " SELECT Cconom 
					FROM costosyp_000005 
				   WHERE Ccocod='".$ccosto[1]."';";

			$resultado2 = mysql_query($query2, $conex);
			$row2 = mysql_fetch_array($resultado2);
			
			$CentroCosto = "(".$wemp_pmla.")".$ccosto[1]."-".strtoupper(htmlentities($row2[0]));
		}
		
		return $CentroCosto;
	}

	//Consulta los centros de costos de las solicitudes despachadas para mostrar en los datos de consulta del reporte
	function centroCostosSolicitudesDespachadas($wemp_pmla,$tipo)
	{
		global $wbasedato;
		global $conex;
					
		$query = "SELECT DISTINCT Reqccs 
					FROM root_000040 
					WHERE Reqcco='(01)1082' 
					  AND Reqtip='".$tipo."' 
					  AND (Reqcla='42' OR Reqcla='43')
					  AND Reqest='05'
					  AND Reqccs !=''
				 ORDER BY Reqccs;";
		
		$resultado = mysql_query($query,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$query." - ".mysql_error());
		$num_rows = mysql_num_rows($resultado);
		
		$contador=0;
		$CentrosCosto = array();
		if($num_rows>0)
		{
			while ($row = mysql_fetch_array($resultado)) 
			{
				$CentrosCostoUsuario[$contador] = consultarCentroCostos($row[0],$wemp_pmla);
				$contador++;
			}
			$CentrosCostoDesp = array_unique($CentrosCostoUsuario);
		
			$cont=0;
			foreach ($CentrosCostoDesp as $valor) 
			{
				$tablaReq=explode("-",$valor);
				$cco= substr($tablaReq[0], 4, 5); 
				
				$CentrosCosto[$cont]['ccoReq']=$tablaReq[0];
				$CentrosCosto[$cont]['cco']=$cco;
				$CentrosCosto[$cont]['des']=$tablaReq[1];
				$cont++;
			}
		}
		
		return	$CentrosCosto;	
	}
	
	function consultarClaseRequerimiento($tipoRequerimiento)
	{
		global $wbasedato;
		global $conex;
		
		$queryClaseReq = "SELECT Clacod,Clades 
							FROM root_000043,root_000044 
							WHERE Clacod=Rctcla 
							  AND Rctcco='(01)1082'
							  AND Rctest='on' 
							  AND Rcttip='".$tipoRequerimiento."';";
		$cantClases=0;
		$resultadoClaseReq = mysql_query($queryClaseReq,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryClaseReq." - ".mysql_error());
		$num_rows = mysql_num_rows($resultadoClaseReq);
		while ($rowClases = mysql_fetch_array($resultadoClaseReq)) 
		{
			$ClasesRequerimiento[$cantClases]['cod'] = $rowClases[0];
			$ClasesRequerimiento[$cantClases]['des'] = $rowClases[1];
			$cantClases++;
		}
		
		return $ClasesRequerimiento;
	}
	
	function pintarDivConsulta()
	{
		global $wemp_pmla;
		global $wbasedato;
		global $conex;	
		global $wfecha;
		
		$fechaactual = strtotime(date('Y-m-d'));
		$datePrimerDia = strtotime("first day of this month", $fechaactual);
		$fechaInicial=date('Y-m-d', $datePrimerDia);
		
		$dateUltimoDia = strtotime(date('Y-m-d'));
		$dateUltimoDia = strtotime("last day of this month", $dateUltimoDia);
		$fechaFinal=date('Y-m-d', $dateUltimoDia);
		
		$tipoRequerimiento = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ReqCentralEsterilizacion');
		
		$CentrosCosto=centroCostosSolicitudesDespachadas($wemp_pmla,$tipoRequerimiento); // Mostrar los centros de costos con solicitudes despachadas
		
		if(count($CentrosCosto)>0)
		{
			$ClasesRequerimiento = consultarClaseRequerimiento($tipoRequerimiento);// Mostrar las clases de requerimiento segun el tipo
	
			echo 
				"<div id='div_datos_reporte' align='center'>
					<table>
						<tr class='encabezadotabla'>
							<td colspan='2'>Solicitudes despachadas</td>
						</tr>
						<tr class='fila2'>
							<td size='8'>Centro de costos:</td>
							<td>
								<select id='ConsultarCentroCostos'>
									<option value=''>Todos</option>";
										for($i=1;$i<=count($CentrosCosto);$i++)
										{
											echo "<option value='".$CentrosCosto[$i-1]['ccoReq']."'>".$CentrosCosto[$i-1]['cco']." - ".$CentrosCosto[$i-1]['des']."</option>";
										}
							echo "							
								</select>
							</td>
						</tr>
						<tr class='fila2'>
							<td size='8'>Tipo de solicitud:</td>
							<td>
								<select id='ConsultarClaseRequerimiento'>
									<option value=''>Seleccione...</option>";
										for($j=1;$j<=count($ClasesRequerimiento);$j++)
										{
											echo "<option value='".$ClasesRequerimiento[$j-1]['cod']."'>".$ClasesRequerimiento[$j-1]['des']."</option>";
										}
							echo "							
								</select>
							</td>
						</tr>
						<tr class='fila2'>
							<td size='8'>Rango de fechas:</td>
							<td>
								<!-- <input type='text' id='fecha_inicial' size='10' value=".$fechaInicial."> - <input type='text' id='fecha_final' size='10' value=".$wfecha.">-->
								<input type='text' id='fecha_inicial' size='10' value=".$fechaInicial."> - <input type='text' id='fecha_final' size='10' value=".$fechaFinal.">
							</td>
						</tr>
						<tr>
							<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."' size='7'>
							<td colspan='2' align='center'><input type='button' value='Generar' onclick='generarReporte();'></td>
						</tr>
					</table>
					</br>
					</br>
				</div>
				";
		}
		else
		{
			echo "<div id='div_datos_reporte' align='center'>No hay solicitudes despachadas para generar el reporte.</div><br><br>";
		}
		
	}
	
	function consultarTablaDespachados($pClasereq,$wemp_pmla)
	{
		global $wbasedato;
		global $conex;	
		
		$query="  SELECT Clamov 
					FROM root_000043 
				   WHERE Clacod='".$pClasereq."';";
		$respuesta = mysql_query($query,$conex);
		
		$rowResultado = mysql_fetch_array($respuesta);
		
		return $rowResultado[0];
	}
	
	function consultarTablaInsumos($pClasereq,$wemp_pmla)
	{
		global $wbasedato;
		global $conex;	
		
		$query="  SELECT Cladat 
					FROM root_000043 
				   WHERE Clacod='".$pClasereq."';";
		$respuesta = mysql_query($query,$conex);
		
		$rowResultado = mysql_fetch_array($respuesta);
		
		return $rowResultado[0];
	}
	
	function consultarDespachos($pCco,$pClasereq,$pFechainicial,$pFechafinal,$wemp_pmla,&$arrayDespachos,&$arrayCcostos,$maestroInsumos,$tablaDespachos,$tipoRequerimiento,$estadoSolicitud)
	{
		global $wbasedato;
		global $conex;	
		
		$condicionCco = "";
		if($pCco!="")
		{
			$condicionCco = "AND a.Reqccs='".$pCco."'";
		}
		
		$queryDespachos = "SELECT b.Reqmet,b.Reqpro,a.Reqccs,SUM(b.Reqcad) AS Cantidad,c.Prodes
							 FROM root_000040 a, ".$tablaDespachos." b, ".$maestroInsumos." c 
							WHERE a.Reqcco='(01)1082'
							  AND a.Reqtip='".$tipoRequerimiento."' 
							  AND a.Reqcla='".$pClasereq."' 
							  AND a.Reqest='".$estadoSolicitud."' 
							  AND a.Reqfen BETWEEN '".$pFechainicial."' AND '".$pFechafinal."'
							  ".$condicionCco."
							  AND b.Reqcla=a.Reqcla
							  AND b.Reqnum=a.Reqnum
							  AND b.Reqdes='on'
							  AND c.Procla=b.Reqcla
							  AND c.Procod=b.Reqpro
						 GROUP BY b.Reqmet,b.Reqpro,a.Reqccs
						 ORDER BY c.Prodes,b.Reqmet,b.Reqpro,a.Reqccs;";
		
		$resDespachos = mysql_query($queryDespachos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryDespachos." - ".mysql_error());
		$numDespachos = mysql_num_rows($resDespachos);
		
		if($numDespachos>0)
		{
			while($rowDespachos = mysql_fetch_array($resDespachos))
			{
				$insumo = $rowDespachos['Reqmet'].$rowDespachos['Reqpro'];
				
				$arrayDespachos[$insumo][$rowDespachos['Reqccs']]['cantidad'] = $rowDespachos['Cantidad'];
				$arrayDespachos[$insumo]['descripcion'] = htmlentities(addslashes($rowDespachos['Prodes']));
				
				if(!in_array($rowDespachos['Reqccs'],$arrayCcostos))
				{
					$arrayCcostos[] = $rowDespachos['Reqccs'];
				}
			}
		}
		
	}
	
	function consultarCostosInsumos($arrayInsumos)
	{
		global $conex;	
		global $wemp_pmla;	
		
		$ipMatrixFinanciero = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ipMatrixFinanciero');
		$conexMatrixFinanciero = mysqli_connect($ipMatrixFinanciero,'root','q6@nt6m', 'matrix') or die("No se realizo Conexion");
	
		$arrayCostos = array();
		if(count($arrayInsumos)>0)
		{
			$stringInsumos = implode("','",$arrayInsumos);
		
			$queryCostos = "SELECT Pcacod, Pcapro,CONCAT_WS('-',Pcaano,LPAD(Pcames,2,'0'),'01') AS Fecha 
							FROM costosyp_000097 a
							WHERE Pcacco='1082' 
							  AND Pcaemp = '01' 
							  AND Pcacod IN ('".$stringInsumos."')
							  AND CONCAT_WS('-',Pcaano,LPAD(Pcames,2,'0'),'01') = (SELECT MAX(CONCAT_WS('-',Pcaano,LPAD(Pcames,2,'0'),'01')) 
																					 FROM costosyp_000097 b 
																				    WHERE b.Pcacod = a.Pcacod);";
		
			$resCostos = mysql_query($queryCostos,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$queryCostos." - ".mysql_error());
			$numCostos = mysql_num_rows($resCostos);
			
			if($numCostos>0)
			{
				while($rowCostos = mysql_fetch_array($resCostos))
				{
					$arrayCostos[$rowCostos['Pcacod']] = round($rowCostos['Pcapro']);
				}
			}
		}
		
		return $arrayCostos;
	}
	
	function pintarReporteDespachos($pArticulosDespachados,$pArrayCco,$pArrayCostosInsumos,$pFecha,$pClasereq,$wemp_pmla,$maestroInsumos,$tablaDespachos,$tipoRequerimiento,$estadoSolicitud,$pFechainicial,$pFechafinal)
	{
		global $wbasedato;
		global $conex;	
		
		$cantCco=count($pArrayCco);
		$cantColumn=($cantCco*2)+2;
		
		$arrayCantidades = array();
		$arrayCostos = array();
		
		$tamWidth=700+($cantCco*130);
		
		
		if(count($pArticulosDespachados)>0)
		{
			echo"
			<div id='reporteDespachados' align='center'>
				<table id='tablaArticulosDespachados' style='width:".$tamWidth."px;' align='center' cellspacing='1' cellpadding='2' >
					<tr class='encabezadoTabla' align='center' >
						<td colspan='".$cantColumn."'>ARTICULOS DESPACHADOS</td>
					</tr>
					<tr class='encabezadoTabla' align='center'>
						<td width='60px' rowspan='2'>C&oacute;digo</td>
						<td width='460px' rowspan='2'>Art&iacute;culo</td>
						";
						for($i=0;$i<count($pArrayCco);$i++)
						{
							$Descco=consultarCentroCostos($pArrayCco[$i],$wemp_pmla);
							
							$descripcionCco = explode(")",$Descco);
							
							echo"<td width='130px' colspan=2>".$descripcionCco[1]."</td>";
							
						}
			echo"	</tr>";
					for($y=0;$y<count($pArrayCco);$y++)
					{
						echo"<tr class='encabezadoTabla' align='center'>";
							for($y=0;$y<count($pArrayCco);$y++)
							{
								echo"<td width='50px'>Cantidad</td>
									<td width='80px'>Costo</td>";
							}
						echo"</tr>";
					}
					
					foreach($pArticulosDespachados as $keyArticulo => $valueArticulo)
					{
						if ($fila_lista=='Fila2')
							$fila_lista = "Fila1";
						else
							$fila_lista = "Fila2";
						
						$funcionPintarDetalle = "onclick='pintarDetalle(\"".$keyArticulo."\",\"".$valueArticulo['descripcion']."\",\"".$maestroInsumos."\",\"".$tablaDespachos."\",\"".$tipoRequerimiento."\",\"".$pClasereq."\",\"".$estadoSolicitud."\",\"\",\"".$pFechainicial."\",\"".$pFechafinal."\");'";
						echo"<tr class='".$fila_lista."' style='cursor: pointer;'>
								<td align='center'>".$keyArticulo."</td>
								<td ".$funcionPintarDetalle.">".stripslashes($valueArticulo['descripcion'])."</td>";
								
								for($y=0;$y<count($pArrayCco);$y++)
								{
									if($valueArticulo[$pArrayCco[$y]]!="")
									{
										$costoInsumo = 0;
										if($pArrayCostosInsumos[$keyArticulo]>0)
										{
											$costoInsumo = $pArrayCostosInsumos[$keyArticulo]*$valueArticulo[$pArrayCco[$y]]['cantidad'];
										}
										
										$arrayCantidades[$pArrayCco[$y]] += $valueArticulo[$pArrayCco[$y]]['cantidad'];
										$arrayCostos[$pArrayCco[$y]] += $costoInsumo;
										
										$funcionPintarDetalle = "onclick='pintarDetalle(\"".$keyArticulo."\",\"".$valueArticulo['descripcion']."\",\"".$maestroInsumos."\",\"".$tablaDespachos."\",\"".$tipoRequerimiento."\",\"".$pClasereq."\",\"".$estadoSolicitud."\",\"".$pArrayCco[$y]."\",\"".$pFechainicial."\",\"".$pFechafinal."\");'";
										echo"<td align='right' width='50px' ".$funcionPintarDetalle.">".$valueArticulo[$pArrayCco[$y]]['cantidad']."</td>";
										echo"<td align='right'width='80px' ".$funcionPintarDetalle.">$".number_format($costoInsumo)."</td>";
									}
									else
									{
										echo"<td align='right' width='50px'>&nbsp</td>";
										echo"<td align='right'width='80px'>&nbsp</td>";
									}
								}
						echo"</tr>";
						
					}
					echo"
						<tr class='encabezadoTabla' align='center'>
							<td width='60px' align='right' colspan='2'>Totales:</td>";
							for($i=0;$i<count($pArrayCco);$i++)
							{
								echo"<td width='50px'>".$arrayCantidades[$pArrayCco[$i]]."</td>";
								echo"<td width='80px'>$".number_format($arrayCostos[$pArrayCco[$i]])."</td>";
							}
					echo"
						</tr>
					<tr>
						<td></td>
					</tr>
				</table>
			</div>";
		}
		else
		{
			echo"
			<div id='reporteDespachados' align='center'>
				<p><b>No se encontraron resultados.</b></p>
			</div>";
		}
		
		
	}
	
	function generarReporteSolicitudesDespachadas($pCco,$pClasereq,$pFechainicial,$pFechafinal,$wemp_pmla)
	{
		global $wbasedato;
		global $conex;	
		
		$fecha=strtotime($pFechafinal);
		
		$maestroInsumos = consultarTablaInsumos($pClasereq,$wemp_pmla);
	    $tablaDespachos = consultarTablaDespachados($pClasereq,$wemp_pmla);
		
		$tipoRequerimiento = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ReqCentralEsterilizacion');
		$estadoSolicitud = consultarAliasPorAplicacion($conex, $wemp_pmla, 'EstadoSolicitudesDespachadas');
		
		$arrayDespachos = array();
		$arrayCcostos = array();
		consultarDespachos($pCco,$pClasereq,$pFechainicial,$pFechafinal,$wemp_pmla,$arrayDespachos,$arrayCcostos,$maestroInsumos,$tablaDespachos,$tipoRequerimiento,$estadoSolicitud);
		
		$arrayInsumos = array_keys ($arrayDespachos);
		
		$arrayCostosInsumos = consultarCostosInsumos($arrayInsumos);
		
		pintarReporteDespachos($arrayDespachos,$arrayCcostos,$arrayCostosInsumos,$fecha,$pClasereq,$wemp_pmla,$maestroInsumos,$tablaDespachos,$tipoRequerimiento,$estadoSolicitud,$pFechainicial,$pFechafinal);
	}
	
	function consultarDetalleDespachos($articulo,$descripcion,$maestroInsumos,$tablaDespachos,$tipoRequerimiento,$clasereq,$estadoSolicitud,$cco,$fechainicial,$fechafinal)
	{
		global $wbasedato;
		global $conex;	
		
		$condicionCco = "";
		if($cco!="")
		{
			$condicionCco = "AND a.Reqccs='".$cco."'";
		}
		
		$metodoEsterilizacion = substr($articulo,0,3);
		$insumo = substr($articulo,3);
		
		$queryDespachos = "SELECT a.Reqnum,a.Reqccs,b.Reqcad, a.Reqdes, a.Fecha_data,a.Reqfen
							 FROM root_000040 a, ".$tablaDespachos." b
							WHERE a.Reqcco='(01)1082'
							  AND a.Reqtip='".$tipoRequerimiento."' 
							  AND a.Reqcla='".$clasereq."' 
							  AND a.Reqest='".$estadoSolicitud."' 
							  AND a.Reqfen BETWEEN '".$fechainicial."' AND '".$fechafinal."'
							  ".$condicionCco."
							  AND b.Reqcla=a.Reqcla
							  AND b.Reqnum=a.Reqnum
							  AND b.Reqdes='on'
							  AND b.Reqpro='".$insumo."'
							  AND b.Reqmet='".$metodoEsterilizacion."'
						 ORDER BY a.Reqnum;";
		
		$resDespachos = mysql_query($queryDespachos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryDespachos." - ".mysql_error());
		$numDespachos = mysql_num_rows($resDespachos);
		
		$arrayDespachos = array();
		if($numDespachos>0)
		{
			while($rowDespachos = mysql_fetch_array($resDespachos))
			{
				$Descco=consultarCentroCostos($rowDespachos['Reqccs'],$wemp_pmla);
				$descripcionCco = explode(")",$Descco);
				
				$arrayDespachos[$rowDespachos['Reqnum']]['requerimiento'] = $rowDespachos['Reqnum'];
				$arrayDespachos[$rowDespachos['Reqnum']]['cco'] = htmlentities($descripcionCco[1]);
				$arrayDespachos[$rowDespachos['Reqnum']]['cantidad'] = $rowDespachos['Reqcad'];
				$arrayDespachos[$rowDespachos['Reqnum']]['descripcion'] = htmlentities($rowDespachos['Reqdes']);
				$arrayDespachos[$rowDespachos['Reqnum']]['fechaSolicitud'] = $rowDespachos['Fecha_data'];
				$arrayDespachos[$rowDespachos['Reqnum']]['fechaEntregado'] = $rowDespachos['Reqfen'];
			}
		}
		
		return $arrayDespachos;
	}
	
	function registrarEnCostos($month,$year,$clasereq,$centrocostos,$costos,$cantCent)
	{
		// global $conex;   
		// global $conexMatrixFinanciero;   
		// global $wfecha;   
		// global $whora;
		
		
		// $parametro = consultarAliasPorAplicacion($conex, "01", 'tiposServicioXRequerimiento');
		
		// $tipo=explode(",",$parametro);
		// for($j=0;$j<count($tipo);$j++)
		// {
			// $serv=explode("-",$tipo[$j]);
			// if($serv[0]==$clasereq)
			// {
				// $servicio=$serv[1];
				// break;
			// }
		// }
		
		// $Mensaje="";
		// if($cantCent=="Todos")
		// {
			// $contDatosCorrectos=0;
			// $contActualizados=0;
			// $ListaCC="";
			
			// for($i=0;$i<count($centrocostos);$i++)
			// {
				// $query = "SELECT Msecan 
							// FROM costosyp_000072 
						   // WHERE Msecco='1082' 
							 // AND Mseccd='".$centrocostos[$i]."' 
							 // AND Msecod='1082".$servicio."' 
							 // AND Mseano='".$year."' 
							 // AND Msemes='".(int)$month."';";
				
				// $resultado = mysql_query($query,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$query." - ".mysql_error());
				// $num_rows = mysql_num_rows($resultado);
				
				// if($num_rows>0)
				// {
					// $queryUpdate = " UPDATE costosyp_000072 
											// SET Fecha_data	= '".$wfecha."',
												// Hora_data	= '".$whora."',
												// Msecan		= ".round($costos[$i])."
										  // WHERE Msecco='1082' 
											// AND Mseccd='".$centrocostos[$i]."'
											// AND Msecod='1082".$servicio."' 										
											// AND Mseano='".$year."'
											// AND Msemes='".(int)$month."';";
											
					// $resultado1 = mysql_query($queryUpdate,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdate." - ".mysql_error());
					
							
					// if(mysql_affected_rows()==1)
					// {
						// $contActualizados++;	
					// }
					// else
					// {
						// $ListaCC.= $centrocostos[$i].", ";
					// }	
					
							
				// }
				// else
				// {
					// $queryInsert = " INSERT INTO costosyp_000072 
												// (Medico,Fecha_data,Hora_data,Mseemp,Mseano,Msemes,Msecco,Mseccd,Msecod,Msecan,Msetip,Mseusu,Seguridad) 
										 // VALUES ('costosyp','".$wfecha."','".$whora."','01','".$year."',".(int)$month.",'1082','".$centrocostos[$i]."','1082".$servicio."',".round($costos[$i]).",'R','44886','C-costosyp');";
										 
					// $resultado2 = mysql_query($queryInsert,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsert." - ".mysql_error());
					
					// if(mysql_affected_rows()==1)
					// {
						// $contDatosCorrectos++;
					// }
					// else
					// {
						// $ListaCC.= $centrocostos[$i].", ";
					// }
				// }
				
			// }
			
			// if($contDatosCorrectos+$contActualizados==count($centrocostos))
			// {
				// $Mensaje= "Se han registrado correctamente los costos correspondientes al mes: ".(int)$month." del año: ".$year." del centro de costos 1082";
			// }
			// else
			// {
				// $ListaCC=substr($ListaCC,0,-2);
				
				// $Mensaje= "Registros correctos: ".$contDatosCorrectos." de ".count($centrocostos)." - Error registrando los costos totales para los centros de costos: ".$ListaCC." del mes: ".(int)$month." y año: ".$year." del centro de costos 1082 - Error: ".mysql_errno();
			// }
		// }
		// else
		// {
			// $query = "SELECT Msecan 
						// FROM costosyp_000072 
					   // WHERE Msecco='1082' 
						 // AND Mseccd='".$centrocostos."' 
						 // AND Msecod='1082".$servicio."' 
						 // AND Mseano='".$year."' 
						 // AND Msemes='".(int)$month."';";
			
			// $resultado = mysql_query($query,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$query." - ".mysql_error());
			// $num_rows = mysql_num_rows($resultado);
			
			// if($num_rows>0)
			// {
				// $queryUpdate = " UPDATE costosyp_000072 
										// SET Fecha_data	= '".$wfecha."',
											// Hora_data	= '".$whora."',
											// Msecan		= ".round($costos)."
									  // WHERE Msecco='1082' 
										// AND Mseccd='".$centrocostos."'
										// AND Msecod='1082".$servicio."' 										
										// AND Mseano='".$year."'
										// AND Msemes='".(int)$month."';";
										
				// $resultado1 = mysql_query($queryUpdate,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdate." - ".mysql_error());
				
						
				// if(mysql_affected_rows()==1)
				// {
					// $Mensaje= "Se ha registrado correctamente el costo para el centro de costos: ".$centrocostos. " correspondiente al mes: ".(int)$month." del año: ".$year." del centro de costos 1082";
				// }
				// else
				// {
					// $Mensaje= "Error registrando el costo para el centro de costos: ".$centrocostos." del mes: ".(int)$month." y año: ".$year." del centro de costos 1082 - Error: ".mysql_errno();
				// }
			// }
			// else
			// {
				// $queryInsert = " INSERT INTO costosyp_000072 
											// (Medico,Fecha_data,Hora_data,Mseemp,Mseano,Msemes,Msecco,Mseccd,Msecod,Msecan,Msetip,Mseusu,Seguridad) 
									 // VALUES ('costosyp','".$wfecha."','".$whora."','01','".$year."',".(int)$month.",'1082','".$centrocostos."','1082".$servicio."',".round($costos).",'R','44886','C-costosyp');";
									 
				// $resultado2 = mysql_query($queryInsert,$conexMatrixFinanciero) or die("Error: " . mysql_errno() . " - en el query: ".$queryInsert." - ".mysql_error());
				
				// if(mysql_affected_rows()==1)
				// {
					// $Mensaje= "Se ha registrado correctamente el costo para el centro de costos: ".$centrocostos. " correspondiente al mes: ".(int)$month." del año: ".$year." del centro de costos 1082";
				// }
				// else
				// {
					// $Mensaje= "Error registrando el costo para el centro de costos: ".$centrocostos." del mes: ".(int)$month." y año: ".$year." del centro de costos 1082 - Error: ".mysql_errno();
				// }
			// }
		// }
		
		
		// echo"<script type=\"text/javascript\">alert('".$Mensaje."');</script>";
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
		case 'GenerarReporte':
		{	
			generarReporteSolicitudesDespachadas($cco,$clasereq,$fechainicial,$fechafinal,$wemp_pmla);
			break;
			return;
		}
		case 'GenerarACostos':
		{
			registrarEnCostos($month,$year,$clasereq,$centrocostos,$costos,"Todos");			
			break;
			return;
		}
		case 'GenerarACostosInd':
		{
			registrarEnCostos($month,$year,$clasereq,$centrocosto,$costo,"Ind");
			break;
			return;			
		}
		case 'consultarDetalle':
		{
			$data = consultarDetalleDespachos($articulo,$descripcion,$maestroInsumos,$tablaDespachos,$tipoRequerimiento,$clasereq,$estadoSolicitud,$cco,$fechainicial,$fechafinal);
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
	  <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	  <title>Reporte despachos central de esterilizacion</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
		
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

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



	$(document).ready(function(){
            
		var currentTime = new Date() ;
		var ultimoDia =  new Date(currentTime.getFullYear(), currentTime.getMonth() +1, 0); // one day before next month
		
            $("#fecha_inicial").datepicker({
				
				showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
				buttonText: "Seleccione la fecha inicial",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
				// maxDate:"+0D"
				maxDate:ultimoDia
            });
			
			$("#fecha_final").datepicker({
				
                showOn: "button",
                buttonImage: "../../images/medical/root/calendar.gif",
                buttonText: "Seleccione la fecha final",
                dateFormat: 'yy-mm-dd',
                buttonImageOnly: true,
                changeMonth: true,
                changeYear: true,
				// maxDate:"+0D"
				maxDate:ultimoDia
            });
          
        });
		
		(function($){
            $.fn.extend({
                detallar: function(){
                    this.each(function(){
                        $(this).click(function(){
                            $(this).next('tr').toggle();
                        })
                    })
                }
            });
        })(jQuery)
	
	function generarReporte()
	{
		
		var cco 	 = $("#ConsultarCentroCostos").val();
		var clasereq = $("#ConsultarClaseRequerimiento").val();
		var fecini 	 = $("#fecha_inicial").val();
		var fecfin 	 = $("#fecha_final").val();
		var wemp_pmla 	 = $("#wemp_pmla").val();
		
		var fechaI = fecini.split("-");
		var fechaF = fecfin.split("-");
		
		var fechainicial  = new Date(fecini);
		var fechafinal    = new Date(fecfin);
		
		if(clasereq=="")
		{
			alert("Debe seleccionar un tipo de solicitud para generar el reporte");
		}
		else
		{
			if(fechainicial > fechafinal)
			{
				alert("La fecha final debe ser mayor a la fecha inicial");
			}
			else
			{
				if(fechaI[0]!=fechaF[0] || fechaI[1]!=fechaF[1])
				{
					alert("Debe seleccionar un rango de fechas del mismo año y mes");
				}
				else
				{
					$("#reporteDespachados<div[id!='tablaArticulosDespachados']").hide();
					$("#msjEspere").show();
					
					$.post("reporte_solicitudes_esterilizacion.php",
					{
						consultaAjax:   		'',
						accion:         		'GenerarReporte',
						cco:         			cco,
						clasereq:         		clasereq,
						fechainicial:  			fecini,
						fechafinal:    			fecfin,
						wemp_pmla:    			wemp_pmla
					}, function(respuesta){
						
						$("#reporteDespachados").html(respuesta);
						// $(".resumen").detallar();
						$("#reporteDespachados<div[id!='table']").show();
						$("#msjEspere").hide();
					});
				}
			}
		}
	}
	
	function generarCostos(mes,anio,ccos,costos,clasereq)
	{
		$.post("reporte_solicitudes_esterilizacion.php",
		{
			consultaAjax:   		'',
			accion:         		'GenerarACostos',
			month:         			mes,
			year:      	    		anio,
			centrocostos:  			ccos,
			costos: 	   			costos,
			clasereq:    			clasereq
		}, function(respuesta){
			$("#CostosGenerados").html(respuesta);
			
			
		});
	}
	
	function generarCosto(mes,anio,cco,costo,clasereq)
	{
		$.post("reporte_solicitudes_esterilizacion.php",
		{
			consultaAjax:   		'',
			accion:         		'GenerarACostosInd',
			month:         			mes,
			year:      	    		anio,
			centrocosto:  			cco,
			costo: 	   			    costo,
			clasereq:    			clasereq
		}, function(respuesta){
			$("#CostosGenerados").html(respuesta);
			
			
		});
	}
	
	function pintarDetalle(articulo,descripcion,maestroInsumos,tablaDespachos,tipoRequerimiento,clasereq,estadoSolicitud,cco,fechainicial,fechafinal)
	{
		$("#divDetalles").html("");
		$.ajax({
			url: "reporte_solicitudes_esterilizacion.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 		: '',
				accion				: 'consultarDetalle',
				articulo			: articulo,
				descripcion			: descripcion,
				maestroInsumos		: maestroInsumos,
				tablaDespachos		: tablaDespachos,
				tipoRequerimiento	: tipoRequerimiento,
				clasereq			: clasereq,
				estadoSolicitud		: estadoSolicitud,
				cco					: cco,
				fechainicial		: fechainicial,
				fechafinal			: fechafinal
				},
				async: false,
				success:function(detalles) {
					
					var html = 	"</br>"+
								"<span class='subtituloPagina2'>"+articulo+" - "+descripcion+"</span>"+
								"</br>"+
								"</br>"+
								"<input type='button' value='Cerrar' onclick='$.unblockUI();'>"+
								"</br>"+
								"</br>"+
								"<table align='center' style='width:85%;'>"+
								"	<tr class='encabezadoTabla'>"+
								"		<td colspan='6' align='center'>DETALLE CANTIDADES DESPACHADAS</td>"+
								"	</tr>"+
								"	<tr class='encabezadoTabla' align='center'>"+
								"		<td>N&uacute;mero de Requerimiento</td>"+
								"		<td>Fecha de la solicitud</td>"+
								"		<td>Centro de costos</td>"+
								"		<td>Descripci&oacute;n</td>"+
								"		<td>Cantidad despachada</td>"+
								"		<td>Fecha despacho</td>"+
								"	</tr>";
								
								fila_lista = "Fila1";
								for(detalle in detalles)
								{
									if (fila_lista=='Fila1')
										fila_lista = "Fila2";
									else
										fila_lista = "Fila1";
								
					html += 		"<tr class='"+fila_lista+"'>"+
									"	<td align='center'>"+detalles[detalle].requerimiento+"</td>"+
									"	<td align='center'>"+detalles[detalle].fechaSolicitud+"</td>"+
									"	<td>"+detalles[detalle].cco+"</td>"+
									"	<td>"+detalles[detalle].descripcion+"</td>"+
									"	<td align='center'>"+detalles[detalle].cantidad+"</td>"+
									"	<td align='center'>"+detalles[detalle].fechaEntregado+"</td>"+
									"</tr>";
								}
								
					html += 	"</table>"+
								"</br>"+
								"<input type='button' value='Cerrar' onclick='$.unblockUI();'>"+
								"</br>"+
								"</br>";
								
					$("#divDetalles").append(html);
					
					$.blockUI({ message: $("#divDetalles") ,
					css: {
						cursor	: 'auto',
						overflow: 'auto',
						width	: "70%",
						height	: "80%",
						left	: "15%",
						top		: '10%',
					} });
				}
		});
	}
	
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-Index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:4px;opacity:1;}
		#tooltip h6, #tooltip div{margin:0; width:auto}
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
		.borderDiv2{
			border: 2px solid #2A5DB0;
			padding: 15px;
		}
		.borderDiv{
			border: 1px solid #e0e0e0;
			padding: 5px;
		}
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
		.parrafo_text{
		background-color: #666666;
		color: #FFFFFF;
		font-family: verdana;
		font-size: 10pt;
		font-weight: bold;
		}
		.fila_detalle{
		background-color: #C1DEF7;
		font-family: verdana;
		font-size: 10pt;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body>
	<?php
	// -->	ENCABEZADO
	encabezado("Reporte despachos central de esterilizacion", $wactualiz, 'clinica');
	pintarDivConsulta();
	
	?>
	<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='<?php echo $wemp_pmla ?>'>
	
	<?php
	
	//Mensaje de espera		
	echo "  <center>
				<div id='msjEspere' style='display:none;'><br>
					<img src='../../images/medical/ajax-loader5.gif'/>
					<br><br> Por favor espere un momento ... <br><br>
				</div>
			</center>";
	
	echo
	"<div id='reporteDespachados' >";
		
	echo"<br><br>
	</div>";
	
	echo"
	<div id='divDetalles' style='display:none;height:85%;'>
	
	</div>
	
	<div id='CostosGenerados'>
	
	</div>
	<div align=center>
		<input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>
	</div><br>";
	
	?>
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
