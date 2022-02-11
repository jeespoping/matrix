<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:			Monitor de preparacion de las dosis adaptadas pendientes por crear. Cuando se filtra por ronda tiene en cuenta
//						el horario de dispensacion del centro de costos o del producto segun el caso, siempre muestra todo lo anterior 
// 						a la ronda actual que este pendiente por preparar, si selecciona una ronda anterior a la actual aplica para la
// 						fecha del día siguiente.
// 						Permite marcar como realizadas o anular las preparaciones de las dosis adaptadas, si anula una dosis adaptada
// 						de la ronda actual o posterior permite cancelar la anulación y se volverá a mostrar en la lista de pendientes 
// 						por crear.
//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	Septiembre 12 de 2017
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='Febrero 09 de 2022';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2022-01-12  Marlon Osorio 		     -Se parametrizo el centro de costo de Central de Mezclas
//  2019-09-23	Jessica Madrid Mejía	- Se agrega cast para evitar warning por migración.
//  2017-11-07	Jessica Madrid Mejía	- Se modifica la función consultarHorarioDispensacion() para que tenga en cuenta el tiempo de 
// 										  dispensación mayor (entre el centro de costos movhos_000011 [ccotdi] y tipo de producto 
// 										  movhos_000099 [Tarhcd]) y no tenga la prioridad por centro de costos.
//  2017-09-20	Jessica Madrid Mejía	- Se muestra cuando un paciente tiene alta en proceso o el articulo esta suspendido
// 										- Se quita accordion por centro de costos              
// 										- Se agrega boton cerrar
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
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$wcenpro = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");
	

//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function pintarFiltrosMonitor()
	{
		$arrayCco = consultarCentrosDeCosto();
		$arrayRondas = consultarRondas();
		
		$filtrosMonitorDA = "";
		
		$filtrosMonitorDA .= "	<div id='divFiltrosMonitor' align='center'>
									<fieldset align='center' style='padding:5px;margin:5px;border: 2px solid #2a5db0;width:65%'>
										<legend style='border: 2px solid #2a5db0;border-top: 0px;font-family: Verdana;color: #ffffff;background-color: #2a5db0;font-size:8pt;font-weight:bold;'> Filtros del Monitor de preparaci&oacute;n </legend>
										<table style='width:61%'>
											<tr>
												<td class='encabezadoTabla' align='center' colspan='2'>Consultar
												</td>
											</tr>
											<tr>
												<td class='fila1'>
													Centro de costo:
												</td>
												<td class='fila2'>
													<select id='cco' name='cco' onchange='cambiarFiltroBuscador();'>";
		$filtrosMonitorDA .= "							<option value=''>TODOS</option>";	
													if(count($arrayCco)>0)
													{
														foreach($arrayCco as $key => $value)
														{
		$filtrosMonitorDA .= "								<option value='".$key."'>".$key."-".$value."</option>";	
														}
													}
		$filtrosMonitorDA .= "						<select>
												</td>
											</tr>
											<tr>
												<td class='fila1'>
													Ronda:
												</td>
												<td class='fila2'>
													<select id='ronda' name='ronda' onchange='cambiarFiltroBuscador();'>";
		$filtrosMonitorDA .= "							<option value=''>TODAS</option>";	
		
													if(count($arrayRondas)>0)
													{
														foreach($arrayRondas as $key => $value)
														{
		$filtrosMonitorDA .= "								<option value='".$key."'>".$key."</option>";	
														}
													}
		$filtrosMonitorDA .= "						<select>
												</td>
											</tr>
										</table>
										
									</fieldset>
								</div>
								
								<br>";
		
		echo $filtrosMonitorDA;
	}
	
	function consultarCentrosDeCosto()
	{
		global $conex;
		global $wbasedato;
		
		$queryCCo = " SELECT Ccocod,Cconom,Ccotdi 
						FROM ".$wbasedato."_000011 
					   WHERE (Ccohos='on' OR Ccourg='on') 
					     AND Ccoest='on' 
					ORDER BY Ccocod,Cconom;";
		
		$resCCo=  mysql_query($queryCCo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryCCo." - ".mysql_error());
		$numCCo = mysql_num_rows($resCCo);	
		
		$arrayCco = array();
		if($numCCo > 0)
		{
			while($rowCCo = mysql_fetch_array($resCCo))
			{
				$arrayCco[$rowCCo['Ccocod']] = $rowCCo['Cconom'];
			}
			
		}
		
		return $arrayCco;			
	}
	
	function consultarRondas()
	{
		$arrayRondas = array();
		
		for($i=0;$i<=24;$i+=2)
		{
			if($i<10)
			{
				$arrayRondas["0".$i]="0".$i;
			}
			else
			{
				$arrayRondas[$i]=$i;
			}
		}
		
		return $arrayRondas;			
	}
	
	function consultarDAparaPreparar($wemp_pmla,$wbasedato,$wcenpro,$cco,$ronda)
	{
		global $conex;
		
		$condicionCco = "";
		if($cco!="")
		{
			$condicionCco = " AND Habcco='".$cco."'";
		}
		
		$queryDA = "  SELECT Prehis,Preing,Precod,Preido,Prefec,Preron,Prelot,Preno1,Preno2,Habcco,Habcpa,Cconom,Pacno1,Pacno2,Pacap1,Pacap2,Ubialp
						FROM ".$wcenpro."_000022,".$wbasedato."_000020,".$wbasedato."_000011,root_000037,root_000036,".$wbasedato."_000018 
					   WHERE Prerea='off' 
						 AND Preanu='off'
						 AND Preest='on'
						 AND Habhis=Prehis
						 AND Habing=Preing
						 AND Ccocod=Habcco
						 AND Ccoest='on'
						 AND Orihis=Prehis
						 AND Oriing=Preing
						 AND Oriori='".$wemp_pmla."'
						 AND Oriced=Pacced
						 AND Oritid=Pactid
						 ".$condicionCco."
						 AND Ubihis=Prehis
						 AND Ubiing=Preing
					ORDER BY Habcco,Habcpa,Prefec,Preron,Precod,Prelot;";
		// echo "<pre>".print_r($queryDA,true)."</pre>";
		$resDA=  mysql_query($queryDA,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryDA." - ".mysql_error());
		$numDA = mysql_num_rows($resDA);	
		
		$arrayDA = array();
		$arrayCco = array();
		if($numDA > 0)
		{
			while($rowDA = mysql_fetch_array($resDA))
			{
				$horarioDispensacion = "";
				$fechayRondaCorteDispensacionEnSegundos = "";
				
				
				$codProducto = substr($rowDA['Precod'],0,2);
				$horarioDispensacion = consultarHorarioDispensacion($rowDA['Habcco'],$codProducto);
				$horaDispensacion = explode(":",$horarioDispensacion);
				
				
				// $rondaDispen = ($horaDispensacion[0])-2;
				$rondaDispen = ($horaDispensacion[0]);
				
				
				$horaCorteDispensacion = $rondaDispen*60*60;
				
				$fechaRonda = date("Y-m-d");
				$rondaActual = floor(date("H")/2)*2;
				$fechayRondaActual = date("Y-m-d")." ".$rondaActual.":00:00";
				
				// Si tiene filtro de ronda, se consulta el horario de dispensacion para el centro de costos del paciente
				if($ronda!="")
				{
					if($ronda<$rondaActual)
					{
						$fechaRonda = date('Y-m-d',strtotime ( '+1 day' , strtotime ( date('Y-m-d') ) ));;
					}
					
					$rondaActual = $ronda;
					$fechayRondaActual = $fechaRonda." ".$rondaActual.":00:00";
				}
				
				$fechayRondaActualEnSegundos = strtotime($fechayRondaActual);
				
				$fechayRondaActualEnSegundos+$horaCorteDispensacion;
				$fechayRondaCorteDispensacionEnSegundos = $fechayRondaActualEnSegundos+$horaCorteDispensacion;
				$fechayRondaCorteDispensacion = date("Y-m-d H:i:s",$fechayRondaCorteDispensacionEnSegundos);
				
				$fechaRondaPreparacionDA = $rowDA['Prefec']." ".$rowDA['Preron'].":00:00";
				$fechaRondaPreparacionDAenSegundos = strtotime($fechaRondaPreparacionDA);
				
				// if($fechayRondaCorteDispensacionEnSegundos=="" || ($fechaRondaPreparacionDAenSegundos <= $fechayRondaCorteDispensacionEnSegundos))
				if(($fechaRondaPreparacionDAenSegundos <= $fechayRondaCorteDispensacionEnSegundos))
				{
					$idArray = $rowDA['Habcco']."|".$rowDA['Preron']."|".$rowDA['Precod']."|".$rowDA['Prehis']."|".$rowDA['Preing']."|".$rowDA['Prelot'];
					
					$arrayDA[$idArray]['historia'] = $rowDA['Prehis'];
					$arrayDA[$idArray]['ingreso'] = $rowDA['Preing'];
					$arrayDA[$idArray]['codigoDA'] = $rowDA['Precod'];
					$arrayDA[$idArray]['ido'] = $rowDA['Preido'];
					$arrayDA[$idArray]['fecha'] = $rowDA['Prefec'];
					$arrayDA[$idArray]['ronda'] = $rowDA['Preron'];
					$arrayDA[$idArray]['lote'] = $rowDA['Prelot'];
					$arrayDA[$idArray]['nombre1'] = htmlentities($rowDA['Preno1']);
					$arrayDA[$idArray]['nombre2'] = htmlentities($rowDA['Preno2']);
					$arrayDA[$idArray]['cco'] = $rowDA['Habcco'];
					$arrayDA[$idArray]['habitacion'] = $rowDA['Habcpa'];
					$arrayDA[$idArray]['nombreCco'] = htmlentities($rowDA['Cconom']);
					$arrayDA[$idArray]['nombre'] = htmlentities($rowDA['Pacno1']." ".$rowDA['Pacno2']." ".$rowDA['Pacap1']." ".$rowDA['Pacap2']);
					$arrayDA[$idArray]['altaEnProceso'] = $rowDA['Ubialp'];
					
					$arrayCco[$rowDA['Habcco']]=$rowDA['Cconom'];
				}
			}
			
		}
		
		$monitorDA = "";
		$monitorDA .= "	<div id='divDApendientes'>";
		
		// $monitorDA .= "<span class='anulados' onclick='pintarModalAnulados();'>Ver preparaciones anuladas</span><br>";
		$verAnuladas = "<span class='anulados' onclick='pintarModalAnulados();'>Ver preparaciones anuladas</span><br>";
		
		if(count($arrayDA)>0)
		{
			// convenciones
			$monitorDA .= "<br>
							<div id='divConvensiones' align='center' width='80%'>
								<table align='right' style='border: 1px solid black;border-radius: 5px;' width='15%'>
									<tr>
									<td align='center' style='font-size:8pt' colspan='6'><b>Convenciones</b></td>
									</tr>
									<tr>
									
										<td class='fondoAmarillo' style='border-radius: 5px;font-size:7pt;' >&nbsp;&nbsp;&nbsp;</td>
										<td style='font-size:7pt;vertical-align:top;'>Alta en proceso</td>
									</tr>
									<tr>	
										<td class='fondoRojo' style='border-radius: 5px;font-size:7pt;' >&nbsp;&nbsp;&nbsp;</td>
										<td style='font-size:7pt;vertical-align:top;'>Suspendido o no enviar</td>
									</tr>
								</table>
							</div>
							<br><br><br>";
			$monitorDA .= $verAnuladas;
			$monitorDA .= "	<div id='accordion' class='desplegable'>";
				$monitorDA .= "	<h3 align='left'>&nbsp;&nbsp;&nbsp;&nbsp;DOSIS ADAPTADAS PENDIENTES DE PREPARACION</h3>
								<div id='divDA'>
									<table id='tableCco".$keyCco."' width='95%'>";
			$monitorDA .= "			<tr class='encabezadoTabla' align='center'>
										<td>Habitaci&oacute;n</td>
										<td>Fecha</td>
										<td>Ronda</td>
										<td>Dosis adaptada</td>
										<td>Lote</td>
										<td>Descripci&oacute;n</td>
										<td>Historia</td>
										<td>Nombre</td>
										<td>&nbsp;&nbsp;&nbsp;</td>
									</tr>";
									foreach($arrayDA as $keyDA => $valueDA)
									{
									
										if ($fila_lista=='Fila1')
											$fila_lista = "Fila2";
										else
											$fila_lista = "Fila1";
										
										if($valueDA['altaEnProceso']=="on")
										{
											$fila_lista = "fondoAmarillo";
										}
										
										$suspendido = consultarDAsuspendida($valueDA['historia'],$valueDA['ingreso'],$valueDA['codigoDA'],$valueDA['ido']);
										
										if($suspendido)
										{
											$fila_lista = "fondoRojo";
										}
										
				$monitorDA .= "			<tr class='".$fila_lista."'>
											<td align='center'>".$valueDA['habitacion']."</td>
											<td align='center'>".$valueDA['fecha']."</td>
											<td align='center'>".$valueDA['ronda']."</td>
											<td align='center'>".$valueDA['codigoDA']."</td>
											<td align='center'>".$valueDA['lote']."</td>
											<td>".$valueDA['nombre1']."".$valueDA['nombre2']."</td>
											<td align='center'>".$valueDA['historia']."-".$valueDA['ingreso']."</td>
											<td>".$valueDA['nombre']."</td>
											<td align='center'><input type='button' value='Preparar' onclick='mostrarPreparacionDA(\"".$valueDA['codigoDA']."\",\"".$valueDA['lote']."\",\"".$valueDA['nombre1']."\",\"".$valueDA['nombre2']."\",\"".$valueDA['historia']."\",\"".$valueDA['ingreso']."\",\"".$valueDA['nombre']."\",\"".$valueDA['habitacion']."\",\"".$valueDA['nombreCco']."\",\"".$valueDA['ido']."\",\"".$valueDA['fecha']."\",\"".$valueDA['ronda']."\");'></td>
										</tr>";
										
									}
				$monitorDA .= "		</table>
								</div>";
			$monitorDA .= "	</div><br>";
		}
		else
		{
			$monitorDA .=$verAnuladas;
			$monitorDA .= "<p align='center'><b>No tiene Dosis adaptadas pendientes de crear</b></p>";
		}
		
		$monitorDA .= "	</div>";
		
		return $monitorDA;	
	
	}
	
	// function consultarHorarioDispensacion($cco,$codProducto)
	// {
		// global $conex;
		// global $wbasedato;
		
		// $qDispensacionCco = " SELECT Ccotdi
								// FROM ".$wbasedato."_000011
							   // WHERE Ccocod='".$cco."'
								 // AND Ccoest='on';";
		
		// $resDispensacionCco = mysql_query($qDispensacionCco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDispensacionCco . " - " . mysql_error());
		// $numDispensacionCco = mysql_num_rows($resDispensacionCco);
		
		// $horarioDispensacion = "";
		// if($numDispensacionCco > 0)
		// {
			// $rowsDispensacionCco = mysql_fetch_array($resDispensacionCco);
			// if($rowsDispensacionCco['Ccotdi']!="00:00:00")
			// {
				// $horarioDispensacion = $rowsDispensacionCco['Ccotdi'];
			// }
			// else
			// {
				// $qDispensacionProd = "SELECT Tarhcd
										// FROM ".$wbasedato."_000099
									   // WHERE Tarcod='".$codProducto."'
										 // AND Tarest='on';";
				
				// $resDispensacionProd = mysql_query($qDispensacionProd, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDispensacionProd . " - " . mysql_error());
				// $numDispensacionProd = mysql_num_rows($resDispensacionProd);
				
				// if($numDispensacionProd > 0)
				// {
					// $rowsDispensacionProd = mysql_fetch_array($resDispensacionProd);
					// $horarioDispensacion = $rowsDispensacionProd['Tarhcd'];
				// }
			// }
		// }		
		// // var_dump($horarioDispensacion);
		// return $horarioDispensacion;
	// }
	
	
	function consultarHorarioDispensacion($cco,$codProducto)
	{
		global $conex;
		global $wbasedato;
		
		$qDispensacionCco = " SELECT Ccotdi
								FROM ".$wbasedato."_000011
							   WHERE Ccocod='".$cco."'
								 AND Ccoest='on';";
		
		$resDispensacionCco = mysql_query($qDispensacionCco, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDispensacionCco . " - " . mysql_error());
		$numDispensacionCco = mysql_num_rows($resDispensacionCco);
		
		$horarioDispensacionCco = "00:00:00";
		if($numDispensacionCco > 0)
		{
			$rowsDispensacionCco = mysql_fetch_array($resDispensacionCco);
			
			$horarioDispensacionCco = $rowsDispensacionCco['Ccotdi'];
			
		}

		$qDispensacionProd = "SELECT Tarhcd
								FROM ".$wbasedato."_000099
							   WHERE Tarcod='".$codProducto."'
								 AND Tarest='on';";
		
		$resDispensacionProd = mysql_query($qDispensacionProd, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qDispensacionProd . " - " . mysql_error());
		$numDispensacionProd = mysql_num_rows($resDispensacionProd);
		
		$horarioDispensacionProd = "00:00:00";
		if($numDispensacionProd > 0)
		{
			$rowsDispensacionProd = mysql_fetch_array($resDispensacionProd);
			$horarioDispensacionProd = $rowsDispensacionProd['Tarhcd'];
		}
		
		$horarioDispensacion = $horarioDispensacionProd;
		
		if($horarioDispensacionCco>$horarioDispensacion)
		{
			$horarioDispensacion = $horarioDispensacionCco;
		}
		
		return $horarioDispensacion;
	}
	
	
	function consultarConcepto($conex,$wcenpro)
	{
		$queryConcepto = "SELECT Concod 
							FROM ".$wcenpro."_000008 
						   WHERE Congas='on' 
							 AND Contra='off' 
							 AND Concar='off' 
							 AND Conave='off' 
							 AND Conven='off' 
							 AND Conane='off' 
							 AND Condes='off'
							 AND Conest='on' 
							 AND Conind='-1';";
							 
						 
		$resConcepto=  mysql_query($queryConcepto,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryConcepto." - ".mysql_error());
		$numConcepto = mysql_num_rows($resConcepto);	
		
		$concepto = "";
		if($numConcepto > 0)
		{
			$rowConcepto = mysql_fetch_array($resConcepto);
			
			$concepto = $rowConcepto['Concod'];
			
		}
		
		return $concepto;
	}
	
	function consultarEdad($historia,$completa)
	{
		global $conex;
		
		global $wemp_pmla;
		
		
		$q = "SELECT Pacnac 
				FROM root_000037,root_000036 
			   WHERE Orihis='".$historia."' 
				 AND Oriori='".$wemp_pmla."' 
				 AND Oriced=Pacced;";

		$res=mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		
		$edad = "";
		$anos = "";
		if($num>0)
		{
			$row=mysql_fetch_array($res);

			$fechaNacimiento = $row['Pacnac'];
			
			//Edad
			$ann=(integer)substr($fechaNacimiento,0,4)*360 +(integer)substr($fechaNacimiento,5,2)*30 + (integer)substr($fechaNacimiento,8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$ann1=($aa - $ann)/360;
			$meses=(($aa - $ann) % 360)/30;
			if ($ann1<1){
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
				$anos = 0;
			} else {
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$ann1." a&ntilde;o(s) ".(string)(integer)$meses." mes(es) ".(string)$dias1." dia(s)";
				$anos = (integer)$ann1;
			}
			
			$edad = $wedad; 
		
		}
		
		if($completa=="on")
		{
			return $edad;
		}
		else
		{
			return $anos;
		}
		
	}
	
	function consultarConcentracionParaInfusion($conex,$wbasedato,$wcenpro,$articulo,$historia,$ingreso)
	{
		$queryArticulo = " SELECT Edainf,Edaemi,Edaema 
							 FROM ".$wcenpro."_000021
						    WHERE Edains='".$articulo."' 
						      AND Edaest='on';";
		
		$resArticulo =  mysql_query($queryArticulo,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryArticulo." - ".mysql_error());
		$numArticulo = mysql_num_rows($resArticulo);
		
		$wedad = consultarEdad($historia,"off");
		
		$concentracionParaInfusion = "";
		if($numArticulo > 0)
		{
			$cantArticulo = 0;
			while($rowArticulo = mysql_fetch_array($resArticulo))
			{
				if($wedad>=$rowArticulo['Edaemi'] && $wedad<=$rowArticulo['Edaema'])
				{
					$concentracionParaInfusion = $rowArticulo['Edainf'];
					break;
				}
			}
		}
		
		
		return $concentracionParaInfusion;
	}
	
	function consultarInsumosPreparacion($conex,$wbasedato,$wcenpro,$codigoDA,$lote,$historia,$ingreso)
	{
		
		$purga = consultarPurga($conex,$wbasedato,$historia,$ingreso);
		
		$concepto = consultarConcepto($conex,$wcenpro);
		
		$queryInsumos = " SELECT Mdeart,Mdepre,Mdecan,Artgen,Artuni,Unides,Appcon,Appvar 
							FROM ".$wcenpro."_000007, ".$wcenpro."_000002, ".$wcenpro."_000009, ".$wbasedato."_000027
						   WHERE Mdecon='".$concepto."' 
						     AND Mdenlo='".$lote."-".$codigoDA."'
							 AND Mdeart=Artcod
							 AND Artest='on'
							 AND Artuni=Unicod
							 AND Uniest='on'
							 AND Appcod=Mdeart
							 AND Apppre=Mdepre
							 AND Appest='on';";
		
		$resInsumos=  mysql_query($queryInsumos,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryInsumos." - ".mysql_error());
		$numInsumos = mysql_num_rows($resInsumos);	
		
		$arrayInsumos = array();
		if($numInsumos > 0)
		{
			$cantInsumos = 0;
			while($rowInsumos = mysql_fetch_array($resInsumos))
			{
				$concentracion = "";
				if($rowInsumos['Appcon']!="0")
				{
					$concentracion = $rowInsumos['Appcon'];
				}
				$volReconstitucion = "";
				if($rowInsumos['Appvar']!="0")
				{
					$volReconstitucion = $rowInsumos['Appvar'];
				}
				
				$concentracionParaInfusion = consultarConcentracionParaInfusion($conex,$wbasedato,$wcenpro,$rowInsumos['Mdeart'],$historia,$ingreso);
				
				$volFormaFarmaceutica = "";
				$volVehiculoDilucion = "";
				$volFinal = "";
				// if($concentracionParaInfusion!="" && $concentracionParaInfusion!="0")
				if($concentracionParaInfusion!="" && $concentracionParaInfusion!="0" && $concentracion!="")
				{
					$volFinal = round((float)$rowInsumos['Mdecan']/(float)$concentracionParaInfusion, 1);
					$volFormaFarmaceutica = round((float)$rowInsumos['Mdecan']*((float)$volReconstitucion/(float)$concentracion), 1);
					$volVehiculoDilucion = round((float)$volFinal-(float)$volFormaFarmaceutica, 1);
				}
				
				$volAdministrar = "";
				if($volFinal != "")
				{
					$volAdministrar = $volFinal-$purga;
				}
				
				$arrayInsumos[$cantInsumos]['codigoCenpro'] = $rowInsumos['Mdeart'];
				$arrayInsumos[$cantInsumos]['codigoMovhos'] = $rowInsumos['Mdepre'];
				$arrayInsumos[$cantInsumos]['cantidad'] = $rowInsumos['Mdecan'];
				$arrayInsumos[$cantInsumos]['descripcion'] = $rowInsumos['Artgen'];
				$arrayInsumos[$cantInsumos]['unidad'] = $rowInsumos['Unides'];
				$arrayInsumos[$cantInsumos]['concentracion'] = $concentracion;
				$arrayInsumos[$cantInsumos]['volReconstitucion'] = $volReconstitucion;
				$arrayInsumos[$cantInsumos]['volFormaFarmaceutica'] = $volFormaFarmaceutica;
				$arrayInsumos[$cantInsumos]['volVehiculoDilucion'] = $volVehiculoDilucion;
				$arrayInsumos[$cantInsumos]['volFinal'] = $volFinal;
				$arrayInsumos[$cantInsumos]['volAdministrar'] = $volAdministrar;
				$cantInsumos++;
			}
		}
		
		return $arrayInsumos;
	}
	
	function pintarModalPreparacionDA($wemp_pmla,$wbasedato,$wcenpro,$codigoDA,$lote,$nombre1,$nombre2,$historia,$ingreso,$nombrePaciente,$habitacion,$servicio,$ido,$fechaRonda,$ronda)
	{
		global$conex;
		
		$descripcionDA= $nombre1.$nombre2;
		
		$colspanTabla = "10";
		
		$insumosPreparacion = consultarInsumosPreparacion($conex,$wbasedato,$wcenpro,$codigoDA,$lote,$historia,$ingreso);	
		
		$modalPreparacionDA = "";
		$modalPreparacionDA .= "<div id='modalPreparacionDA'>
									<br><br><br>
									<table align='center' width='90%'>
										<tr class='encabezadoTabla'>
											<td colspan='".$colspanTabla."' align='center'>PREPARACI&Oacute;N DOSIS ADAPTADAS</td>
										</tr>
										<tr class='encabezadoTabla'>
											<td colspan='".$colspanTabla."' align='center'>Datos del paciente</td>
										</tr>
										<tr>
											<td class='fila1' colspan='2'>Historia:</td>
											<td class='fila2' colspan='2'>".$historia."-".$ingreso."</td>
											<td class='fila1' colspan='2'>Nombre:</td>
											<td class='fila2' colspan='4'>".$nombrePaciente."</td>
										</tr>
										<tr>
											<td class='fila1' colspan='2'>Habitaci&oacute;n:</td>
											<td class='fila2' colspan='2'>".$habitacion."</td>
											<td class='fila1' colspan='2'>Servicio:</td>
											<td class='fila2' colspan='4'>".$servicio."</td>
										</tr>
										<tr class='encabezadoTabla'>
											<td colspan='".$colspanTabla."' align='center'>Informaci&oacute;n del producto</td>
										</tr>
										<tr>
											<td class='fila1'>C&oacute;digo:</td>
											<td class='fila2'>".$codigoDA."</td>
											<td class='fila1'>Lote:</td>
											<td class='fila2'>".$lote."</td>
											<td class='fila1'>Descripci&oacute;n:</td>
											<td class='fila2' colspan='5'>".$descripcionDA."</td>
										</tr>
										<tr class='encabezadoTabla'>
											<td colspan='".$colspanTabla."' align='center'>Insumos de preparaci&oacute;n</td>
										</tr>
										<tr class='encabezadoTabla' align='center'>
											<td>C&oacute;digo del art&iacute;culo</td>
											<td>Descripci&oacute;n</td>
											<td>Cantidad</td>
											<td>Unidad</td>
											<td>Concentraci&oacute;n (MG)</td>
											<td>Volumen para reconstituci&oacute;n (ML)</td>
											<td>Volumen forma farmac&eacute;utica (ML)</td>
											<td>Volumen veh&iacute;culo de diluci&oacute;n (ML)</td>
											<td>Volumen final (ML)</td>
											<td>Volumen a administrar al paciente (ML)</td>
										</tr>";
										
										foreach($insumosPreparacion as $keyInsumo => $valueInsumo)
										{
											if ($fila_lista=='Fila1')
												$fila_lista = "Fila2";
											else
												$fila_lista = "Fila1";
											
		$modalPreparacionDA .= "			<tr class='".$fila_lista."'>
												<td align='center'>".$valueInsumo['codigoMovhos']."</td>
												<td>".$valueInsumo['descripcion']."</td>
												<td align='center'>".$valueInsumo['cantidad']."</td>
												<td align='center'>".$valueInsumo['unidad']."</td>
												<td align='center'>".$valueInsumo['concentracion']."</td>
												<td align='center'>".$valueInsumo['volReconstitucion']."</td>
												<td align='center'>".$valueInsumo['volFormaFarmaceutica']."</td>
												<td align='center'>".$valueInsumo['volVehiculoDilucion']."</td>
												<td align='center'>".$valueInsumo['volFinal']."</td>
												<td align='center'>".$valueInsumo['volAdministrar']."</td>
											</tr>
											
											";
										}
										
		$arrayUsuarios = consultarListaUsuarios();								
		$modalPreparacionDA .= "	</table>
									<br>
									<br>
									<table align='center'>
										<tr class='encabezadoTabla' align='center'>
											<td colspan='2'>Informaci&oacute;n sobre la preparaci&oacute;n</td>
										</tr>
										<tr class='fila1' align='center'>
											<td>Lote de la ampolla</td>
											<td><input type='text' id='loteAmpolla' name='loteAmpolla' size='42'></td>
										</tr>
										<tr class='fila2' align='center'>
											<td>RF quien realiz&oacute; el acondicionamiento</td>
											<td>
												<!--<input type='text' id='usuarioAcondicionamiento' name='usuarioAcondicionamiento'><input type='hidden' id='codUsuarioAcondicionamiento' name='codUsuarioAcondicionamiento'>-->
												<select id='usuarioAcondicionamiento' name='usuarioAcondicionamiento'>";
		$modalPreparacionDA .= "					<option value=''>Seleccione...</option>";	
													if(count($arrayUsuarios)>0)
													{
														foreach($arrayUsuarios as $key => $value)
														{
		$modalPreparacionDA .= "							<option value='".$key."'>".$value."</option>";	
														}
													}
		$modalPreparacionDA .= "				<select>			
												
											</td>
											</td>
										</tr>
										<tr class='fila1' align='center'>
											<td>QF quien aprueba la preparaci&oacute;n</td>
											<td>
												<!--<input type='text' id='usuarioAprueba' name='usuarioAprueba'><input type='hidden' id='codUsuarioAprueba' name='codUsuarioAprueba'>-->
												<select id='usuarioAprueba' name='usuarioAprueba'>";
		$modalPreparacionDA .= "					<option value=''>Seleccione...</option>";	
													if(count($arrayUsuarios)>0)
													{
														foreach($arrayUsuarios as $key => $value)
														{
		$modalPreparacionDA .= "							<option value='".$key."'>".$value."</option>";	
														}
													}
		$modalPreparacionDA .= "				<select>			
												
											</td>
											
										</tr>
									</table>";
		$modalPreparacionDA .= "	
									<br><br>
									<table width='40%' align='center'>
										<tr>
											<td style='border: 1px solid #BABABA;border-radius: 5px;font-family:verdana;font-size:8pt;font-weight: bold;width:50px;height:50px;cursor:pointer; ' align='center' onclick='registrarPreparacion(\"".$historia."\",\"".$ingreso."\",\"".$codigoDA."\",\"".$ido."\",\"".$fechaRonda."\",\"".$ronda."\",\"".$lote."\",\"".$descripcionDA."\",\"Realizar\");' title='Dosis adaptada preparada'><img src='../../images/medical/root/grabar.png' style='vertical-align:top' ><span> Dosis adaptada preparada</span></td>
											<td style='border: 1px solid #BABABA;border-radius: 5px;font-family:verdana;font-size:8pt;font-weight: bold;width:50px;height:50px;cursor:pointer; ' align='center' onclick='registrarPreparacion(\"".$historia."\",\"".$ingreso."\",\"".$codigoDA."\",\"".$ido."\",\"".$fechaRonda."\",\"".$ronda."\",\"".$lote."\",\"".$descripcionDA."\",\"Anular\");'  title='Anular preparación de dosis adaptada'><img src='../../images/medical/root/borrar.png' style='vertical-align:top' ><span> Anular preparaci&oacute;n de dosis adaptada</span></td>
										</tr>
									</table>
									<br><br>
									<span><input type='button' value='Cerrar ventana' onclick='cerrarModal()'></span>
								</div>";
								
		return $modalPreparacionDA;
	}
	
	function marcarPreparacionDA($wemp_pmla,$wbasedato,$wcenpro,$historia,$ingreso,$codigoDA,$ido,$fechaRonda,$ronda,$lote,$descripcionDA,$accion,$loteAmpolla,$usuarioAcondicionamiento,$usuarioAprueba)
	{
		global $conex;
		global $wuse;
		
		$data = array('error'=>"",'mensaje'=>"");
			
		$query = " SELECT Prerea,Preurp,Prefrp,Prehrp,Preanu,Preuap,Prefap,Prehap 
					 FROM ".$wcenpro."_000022 
					WHERE Prehis='".$historia."' 
					  AND Preing='".$ingreso."' 
					  AND Precod='".$codigoDA."' 
					  AND Preido='".$ido."' 
					  AND Prefec='".$fechaRonda."' 
					  AND Preron='".$ronda."' 
					  AND Prelot='".$lote."' 
					  AND Preest='on';";
		
		$res=  mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($res);	
		
		$arrayInsumos = array();
		if($num > 0)
		{
			$row = mysql_fetch_array($res);
			
			if($row['Prerea']=="on")
			{
				// La dosis adaptada fue realizada
				$usuario = consultarNombreUsuario($row['Preurp']);
				
				$data['error'] = "1";
				$data['mensaje'] = "La dosis adaptada: ".$codigoDA." - ".$descripcionDA." (lote: ".$lote.") fue realizada por: ".$usuario." el ".$row['Prefrp']." a las ".$row['Prehrp'];
			}
			else
			{
				if($row['Preanu']=="on")
				{
					// La dosis adaptada fue anulada
					$usuario = consultarNombreUsuario($row['Preurp']);
					$data['error'] = "1";
					$data['mensaje'] = "La dosis adaptada: ".$codigoDA." - ".$descripcionDA." (lote: ".$lote.") fue realizada por: ".$usuario." el ".$row['Prefap']." a las ".$row['Prehap'];
				}
				else
				{
					if($accion=="Realizar")
					{
						// Marcar como preparada
						$qUpdateRealizar = " UPDATE ".$wcenpro."_000022 
												SET Prerea='on',
													Preurp='".$wuse."',
													Prefrp='".date("Y-m-d")."',
													Prehrp='".date("H:i:s")."',
													Preamp='".$loteAmpolla."',
													Preura='".$usuarioAcondicionamiento."',
													Preuab='".$usuarioAprueba."'
											  WHERE Prehis='".$historia."' 
												AND Preing='".$ingreso."' 
												AND Precod='".$codigoDA."' 
												AND Preido='".$ido."' 
												AND Prefec='".$fechaRonda."' 
												AND Preron='".$ronda."' 
												AND Prelot='".$lote."' 
												AND Preest='on';";
						
						$resultadoUpdateRealizar = mysql_query($qUpdateRealizar,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateRealizar." - ".mysql_error());
						
						$data['error'] = "0";
						$data['mensaje'] = "Se registró como realizada la dosis adaptada: ".$codigoDA." - ".$descripcionDA." (lote: ".$lote.")";		
					}
					elseif($accion=="Anular")
					{
						// Marcar como Anulada
						$qUpdateAnulada = " UPDATE ".$wcenpro."_000022 
												SET Preanu='on',
													Preuap='".$wuse."',
													Prefap='".date("Y-m-d")."',
													Prehap='".date("H:i:s")."'
											  WHERE Prehis='".$historia."' 
												AND Preing='".$ingreso."' 
												AND Precod='".$codigoDA."' 
												AND Preido='".$ido."' 
												AND Prefec='".$fechaRonda."' 
												AND Preron='".$ronda."' 
												AND Prelot='".$lote."' 
												AND Preest='on';";
						
						$resultadoUpdateAnulada = mysql_query($qUpdateAnulada,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateAnulada." - ".mysql_error());
						
						$data['error'] = "0";
						$data['mensaje'] = "Se registró como anulada la dosis adaptada: ".$codigoDA." - ".$descripcionDA." (lote: ".$lote.").\n
											Debe anular el lote y realizar la devolución del producto.";		
					
					}
								
				}
			}
		}
		return $data;
	}
	
	function consultarNombreUsuario($codigoUsuario)
	{
		global $conex;
		
		$query = " SELECT Descripcion
					 FROM usuarios
					WHERE Codigo='".$codigoUsuario."' 
					  AND Activo='A';";
		
		$res=  mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
		$num = mysql_num_rows($res);	
		
		$nombreUsuario = "";
		if($num > 0)
		{
			$row = mysql_fetch_array($res);
			
			$nombreUsuario = $row['Descripcion'];
		}
		
		return $nombreUsuario;
	}

	function consultarAnulados($wemp_pmla,$wbasedato,$wcenpro)
	{
		global $conex;
		
		// validar que solo sean los anulados para fechas y rondas mayores o iguales a la actual
		
		$fechaActual = date("Y-m-d");
		$rondaActual = floor(date("H")/2)*2;
		
		if($rondaActual<10)
		{
			$rondaActual = "0".$rondaActual;
		}
		
		$queryAnulados = "SELECT Prehis,Preing,Precod,Preido,Prefec,Preron,Prelot,Preno1,Preno2,Preuap,Prefap,Prehap,Habcco,Habcpa,Cconom,Pacno1,Pacno2,Pacap1,Pacap2 
							FROM ".$wcenpro."_000022,".$wbasedato."_000020,".$wbasedato."_000011,root_000037,root_000036
						   WHERE Preest='on' 
							 AND Preanu='on'
							 AND Prefec='".$fechaActual."'
							 AND Preron >= '".$rondaActual."'
							 AND Habhis=Prehis
							 AND Habing=Preing
							 AND Ccocod=Habcco
							 AND Ccoest='on'
							 AND Orihis=Prehis
							 AND Oriing=Preing
							 AND Oriori='".$wemp_pmla."'
							 AND Oriced=Pacced
							 AND Oritid=Pactid;";
							 
		$resAnulados=  mysql_query($queryAnulados,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryAnulados." - ".mysql_error());
		$numAnulados = mysql_num_rows($resAnulados);	
		
		$contadorAnulados=0;
		$arrayAnulados = array();
		if($numAnulados > 0)
		{
			while($rowAnulados = mysql_fetch_array($resAnulados))
			{
				$arrayAnulados[$contadorAnulados]['historia'] = $rowAnulados['Prehis'];
				$arrayAnulados[$contadorAnulados]['ingreso'] = $rowAnulados['Preing'];
				$arrayAnulados[$contadorAnulados]['codigoDA'] = $rowAnulados['Precod'];
				$arrayAnulados[$contadorAnulados]['ido'] = $rowAnulados['Preido'];
				$arrayAnulados[$contadorAnulados]['fechaRonda'] = $rowAnulados['Prefec'];
				$arrayAnulados[$contadorAnulados]['ronda'] = $rowAnulados['Preron'];
				$arrayAnulados[$contadorAnulados]['lote'] = $rowAnulados['Prelot'];
				$arrayAnulados[$contadorAnulados]['descripcion'] = $rowAnulados['Preno1']."".$rowAnulados['Preno2'];
				$arrayAnulados[$contadorAnulados]['usuario'] = $rowAnulados['Preuap'];
				$arrayAnulados[$contadorAnulados]['nombreUsuario'] = consultarNombreUsuario($rowAnulados['Preuap']);
				$arrayAnulados[$contadorAnulados]['fechaAnulacion'] = $rowAnulados['Prefap'];
				$arrayAnulados[$contadorAnulados]['horaAnulacion'] = $rowAnulados['Prehap'];
				$arrayAnulados[$contadorAnulados]['cco'] = $rowAnulados['Habcco'];
				$arrayAnulados[$contadorAnulados]['habitacion'] = $rowAnulados['Habcpa'];
				$arrayAnulados[$contadorAnulados]['nombreCco'] = $rowAnulados['Cconom'];
				$arrayAnulados[$contadorAnulados]['nombrePaciente'] = $rowAnulados['Pacno1']." ".$rowAnulados['Pacno2']." ".$rowAnulados['Pacap1']." ".$rowAnulados['Pacap2'];
				
				$contadorAnulados++;
			}
		}
		
		return $arrayAnulados;					 
	}
	
	function pintarModalAnulados($wemp_pmla,$wbasedato,$wcenpro)
	{
		$rondaActual = floor(date("H")/2)*2;
		$fechayRondaActual = date("Y-m-d")." ".$rondaActual.":00:00";
		
		$arrayAnulados = consultarAnulados($wemp_pmla,$wbasedato,$wcenpro);
		
		$colspan='11';
		
		$modalAnulados = "";
		$modalAnulados .= " <div id='modalAnulados'>
								<br>
								";
									if(count($arrayAnulados)>0)
									{
		$modalAnulados .= 				"<table align='center' width='90%'>
											<tr class='encabezadoTabla'>
												<td align='center' colspan='".$colspan."'>PREPARACIONES ANULADAS</td>
											</tr>
											<tr class='encabezadoTabla' align='center'>
												<td>Fecha</td>
												<td>Ronda</td>
												<td>Dosis adaptada</td>
												<td>Lote</td>
												<td>Descripción</td>
												<td>Habitaci&oacute;n</td>
												<td>Historia</td>
												<td>Nombre del paciente</td>
												<td>Anulado por</td>
												<td>Fecha y hora de la anulación</td>
												<td>Activar preparaci&oacute;n</td>
											</tr>";
										foreach($arrayAnulados as $keyAnulados => $valueAnulados)
										{
											if ($fila_lista=='Fila1')
												$fila_lista = "Fila2";
											else
												$fila_lista = "Fila1";
											
											
		$modalAnulados .= "					<tr class='".$fila_lista."'>
												<td align='center'>".$valueAnulados['fechaRonda']."</td>
												<td align='center'>".$valueAnulados['ronda']."</td>
												<td align='center'>".$valueAnulados['codigoDA']."</td>
												<td align='center'>".$valueAnulados['lote']."</td>
												<td>".$valueAnulados['descripcion']."</td>
												<td align='center'>".$valueAnulados['habitacion']."</td>
												<td align='center'>".$valueAnulados['historia']."-".$valueAnulados['ingreso']."</td>
												<td align='center'>".$valueAnulados['nombrePaciente']."</td>
												<td align='center'>".$valueAnulados['nombreUsuario']."</td>
												<td align='center'>".$valueAnulados['fechaAnulacion']." ".$valueAnulados['horaAnulacion']."</td>
												<td align='center' style='cursor:pointer;' onclick='activarPreparacion(\"".$valueAnulados['historia']."\",\"".$valueAnulados['ingreso']."\",\"".$valueAnulados['codigoDA']."\",\"".$valueAnulados['ido']."\",\"".$valueAnulados['fechaRonda']."\",\"".$valueAnulados['ronda']."\",\"".$valueAnulados['lote']."\",\"".$valueAnulados['descripcion']."\");'><span><img src='../../images/medical/root/grabar.png' width='15px' style='cursor:pointer;vertical-align:middle' title='Activar preparaci&oacute;n' >&nbsp;Activar preparación</span></td>
											</tr>";
										}
		$modalAnulados .= "				</table>
										<p align='center'><b>Nota: Solo se muestran las dosis adaptadas anuladas para la fecha y ronda mayores o iguales a la actual (".$fechayRondaActual.")</b></p>
										";
									}
									else
									{
		$modalAnulados .= "				<p align='center'><b>No hay Dosis adaptadas anuladas para fecha y ronda mayores o iguales a la actual (".$fechayRondaActual.")</b></p>";
									}
	
		$modalAnulados .= "		
								<br><br>
								<span><input type='button' value='Cerrar ventana' onclick='cerrarModal()'></span>
							</div>";
							
		return $modalAnulados;
	}
	
	function activarPreparacionDA($wemp_pmla,$wbasedato,$wcenpro,$historia,$ingreso,$codigoDA,$ido,$fechaRonda,$ronda,$lote,$descripcionDA)
	{
		global $conex;
		
		$data = array('error'=>"",'mensaje'=>"");
		
		// Marcar como preparada
		$qUpdateActivar  = " UPDATE ".$wcenpro."_000022 
								SET Preanu='off'
							  WHERE Prehis='".$historia."' 
								AND Preing='".$ingreso."' 
								AND Precod='".$codigoDA."' 
								AND Preido='".$ido."' 
								AND Prefec='".$fechaRonda."' 
								AND Preron='".$ronda."' 
								AND Prelot='".$lote."' 
								AND Preest='on';";
		
		$resultadoUpdateActivao = mysql_query($qUpdateActivar,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateActivar." - ".mysql_error());
		
		$data['error'] = "0";
		$data['mensaje'] = "Se activó nuevamente la dosis adaptada: ".$codigoDA." - ".$descripcionDA." (lote: ".$lote.") para ser preparada";		
	
		
		return $data;
	}
	
	function consultarListaUsuarios()
	{
		global $conex;
		global $wemp_pmla;

		$ccoCM=ccoUnificadoCM();
		
		$queryUsuarios = "SELECT Codigo,Descripcion 
							FROM usuarios 
						   WHERE Ccostos='{$ccoCM}' 
						     AND Activo='A' 
							 AND Empresa='{$wemp_pmla}';";
							 
		$resUsuarios=  mysql_query($queryUsuarios,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryUsuarios." - ".mysql_error());
		$numUsuarios = mysql_num_rows($resUsuarios);	
		
		$contadorUsuarios=0;
		$arrayUsuarios = array();
		if($numUsuarios > 0)
		{
			while($rowUsuarios = mysql_fetch_array($resUsuarios))
			{
				$arrayUsuarios[$rowUsuarios['Codigo']] = $rowUsuarios['Descripcion'];
			}
		}
		
		return $arrayUsuarios;
	}
	
	function consultarPurga($conex,$wbasedato,$historia,$ingreso)
	{
		$queryPurga = "SELECT Ccopda 
						 FROM ".$wbasedato."_000020,".$wbasedato."_000011 
						WHERE Habhis='".$historia."' 
						  AND Habing='".$ingreso."'
						  AND Ccocod=Habcco
						  AND Ccoest='on';";
						  
		$resPurga=  mysql_query($queryPurga,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$queryPurga." - ".mysql_error());
		$numPurga = mysql_num_rows($resPurga);	
		
		$purga=0;
		if($numPurga > 0)
		{
			$rowPurga = mysql_fetch_array($resPurga);
			$purga=$rowPurga['Ccopda'];			
		}	
		
		return $purga;		
	}
	
	function consultarDAsuspendida($historia,$ingreso,$codigoDA,$ido)
	{
		global $conex;
		global $wbasedato;
		
		$querySuspendido = "SELECT * 
							  FROM ".$wbasedato."_000054 
							 WHERE Kadhis='".$historia."' 
							   AND Kading='".$ingreso."' 
							   AND Kadart='".$codigoDA."' 
							   AND Kadido='".$ido."'
							   AND Kadsus='off' 
							   AND Kadfec='".date("Y-m-d")."'
							   AND Kadess='off'
							   
							  UNION 
							  
							  SELECT * 
							  FROM ".$wbasedato."_000060 
							 WHERE Kadhis='".$historia."' 
							   AND Kading='".$ingreso."' 
							   AND Kadart='".$codigoDA."' 
							   AND Kadido='".$ido."'
							   AND Kadsus='off' 
							   AND Kadfec='".date("Y-m-d")."'
							   AND Kadess='off';";
							 
		$resSuspendido=  mysql_query($querySuspendido,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$querySuspendido." - ".mysql_error());
		$numSuspendido = mysql_num_rows($resSuspendido);	
		
		$suspendido = true;
		if($numSuspendido > 0)
		{
			$suspendido = false;
		}
		
		return $suspendido;
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
		case 'consultarDA':
		{	
			$data = consultarDAparaPreparar($wemp_pmla,$wbasedato,$wcenpro,$cco,$ronda);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarPreparacionDA':
		{	
			$data = pintarModalPreparacionDA($wemp_pmla,$wbasedato,$wcenpro,$codigoDA,$lote,$nombre1,$nombre2,$historia,$ingreso,$nombrePaciente,$habitacion,$servicio,$ido,$fechaRonda,$ronda);
			echo json_encode($data);
			break;
			return;
		}
		case 'registrarPreparacionDA':
		{	
			$data = marcarPreparacionDA($wemp_pmla,$wbasedato,$wcenpro,$historia,$ingreso,$codigoDA,$ido,$fechaRonda,$ronda,$lote,$descripcionDA,$accionPrep,$loteAmpolla,$usuAcond,$usuAprueba);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarModalAnulados':
		{	
			$data = pintarModalAnulados($wemp_pmla,$wbasedato,$wcenpro);
			echo json_encode($data);
			break;
			return;
		}
		case 'activarPreparacionDA':
		{	
			$data = activarPreparacionDA($wemp_pmla,$wbasedato,$wcenpro,$historia,$ingreso,$codigoDA,$ido,$fechaRonda,$ronda,$lote,$descripcionDA);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarUsuarios':
		{	
			$data = consultarListaUsuarios();
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
	  <title>MONITOR PREPARACION DOSIS ADAPTADAS</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
		
	$(document).ready(function () {
		inicializarAccordion();
	});
	
	//Actualizar cada dos minutos
	setInterval(function()
	{
		cambiarFiltroBuscador();
	}, 120000);
		
	
	function inicializarAccordion()
	{
		$( ".desplegable" ).accordion({
			collapsible: true,
			active:0,
			heightStyle: "content"
		});
	}
	
	
	function cambiarFiltroBuscador()
	{
		var cco = $("#cco").val();
		var ronda = $("#ronda").val();
		
		$.post("monitorProduccionDA.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarDA',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			wcenpro			: $('#wcenpro').val(),
			cco				: cco,
			ronda			: ronda
		}
		, function(data) {
			
			$( "#divDApendientes" ).html(data);
			
			inicializarAccordion();
		
		},'json');
	}
	
	function mostrarPreparacionDA(codigoDA,lote,nombre1,nombre2,historia,ingreso,nombrePaciente,habitacion,servicio,ido,fechaRonda,ronda)
	{
		$.post("monitorProduccionDA.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarPreparacionDA',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			wcenpro			: $('#wcenpro').val(),
			codigoDA		: codigoDA,
			lote			: lote,
			nombre1			: nombre1,
			nombre2			: nombre2,
			historia		: historia,
			ingreso			: ingreso,
			nombrePaciente	: nombrePaciente,
			habitacion		: habitacion,
			servicio		: servicio,
			ido				: ido,
			fechaRonda		: fechaRonda,
			ronda			: ronda
		}
		, function(data) {
			
			$( "#dvAuxModalPreparacionDA" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalPreparacionDA" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalPreparacionDA" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalPreparacionDA" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalPreparacionDA" ).height();
			
			$.blockUI({ message: $('#modalPreparacionDA'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "80%",
				height	: "80%",
				left	: "10%",
				top		: '100px',
			} });
			
			
		},'json');
	}
	
	function registrarPreparacion(historia,ingreso,codigoDA,ido,fechaRonda,ronda,lote,descripcionDA,accion)
	{
		var mensAccion = accion;
		
		var validaciones = true;
		var camposSinLlenar = "";
		
		if(accion == "Realizar")
		{
			mensAccion = "realizada";
			// validar seleccion de usuarios validos
			if( $.trim($("#loteAmpolla").val())=="")
			{
				validaciones = false;
				camposSinLlenar += "el lote,";
			}
			if( $.trim($("#usuarioAcondicionamiento").val())=="")
			{
				validaciones = false;
				camposSinLlenar += " el regente de farmacia quien realiz&oacute; el acondicionamiento,";
			}
			if( $.trim($("#usuarioAprueba").val())=="")
			{
				validaciones = false;
				camposSinLlenar += " el qu&iacute;mico farmace&uacute;tico quien aprueba la preparaci&oacute;n,";
			}
			
			camposSinLlenar =  camposSinLlenar.substring(0,camposSinLlenar.length-1);
			
		}
		else
		{
			mensAccion = "anulada";
		}
		
		
		if(validaciones==true)
		{
			jConfirm( "Desea registrar como "+mensAccion+" la preparaci&oacute;n?", "ALERTA", function( resp ){
				
				if(resp)
				{
					$.post("monitorProduccionDA.php",
					{
						consultaAjax 	: '',
						accion			: 'registrarPreparacionDA',
						wemp_pmla		: $('#wemp_pmla').val(),
						wbasedato		: $('#wbasedato').val(),
						wcenpro			: $('#wcenpro').val(),
						historia		: historia,
						ingreso			: ingreso,
						codigoDA		: codigoDA,
						ido				: ido,
						fechaRonda		: fechaRonda,
						ronda			: ronda,
						lote			: lote,
						descripcionDA	: descripcionDA,
						accionPrep		: accion,
						loteAmpolla		: $("#loteAmpolla").val(),
						usuAcond		: $("#usuarioAcondicionamiento").val(),
						usuAprueba		: $("#usuarioAprueba").val()
					}
					, function(data) {
						
						jAlert(data.mensaje,"ALERTA");
						cerrarModal();
						
					},'json');
					
				}
			});
		}
		else
		{
			jAlert("Debe llenar los siguientes campos: "+camposSinLlenar+".","ALERTA");
		}
	}
	
	function cerrarModal()
	{
		$.unblockUI();
		cambiarFiltroBuscador();
	}
	
	function pintarModalAnulados()
	{
		$.post("monitorProduccionDA.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarModalAnulados',
			wemp_pmla		: $('#wemp_pmla').val(),
			wbasedato		: $('#wbasedato').val(),
			wcenpro			: $('#wcenpro').val()
		}
		, function(data) {
			
			$( "#dvAuxModalAnulados" ).html( data );
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalAnulados" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalAnulados" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalAnulados" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalAnulados" ).height();
			
			$.blockUI({ message: $('#modalAnulados'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "70%",
				height	: "70%",
				left	: "15%",
				top		: '100px',
			} });
			
		
		},'json');
	}
	
	function activarPreparacion(historia,ingreso,codigoDA,ido,fechaRonda,ronda,lote,descripcionDA)
	{
		jConfirm( "Desea activar la preparaci&oacute;n?", "ALERTA", function( resp ){
			
			if(resp)
			{
				$.post("monitorProduccionDA.php",
				{
					consultaAjax 	: '',
					accion			: 'activarPreparacionDA',
					wemp_pmla		: $('#wemp_pmla').val(),
					wbasedato		: $('#wbasedato').val(),
					wcenpro			: $('#wcenpro').val(),
					historia		: historia,
					ingreso			: ingreso,
					codigoDA		: codigoDA,
					ido				: ido,
					fechaRonda		: fechaRonda,
					ronda			: ronda,
					lote			: lote,
					descripcionDA	: descripcionDA
				}
				, function(data) {
					
					jAlert(data.mensaje,"ALERTA");
					cerrarModal();
				},'json');
				
			}
		});
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
	
		.anulados{
			font-family: verdana;
			font-size: 10pt;
			color: #0033FF;
			font-weight: bold;
			text-decoration: underline;
			cursor:pointer;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<BODY>
	<?php
	// var_dump($soloconsulta);
	
	if($soloconsulta!=true)
	{
		// -->	ENCABEZADO
		encabezado("MONITOR DE PREPARACION DE DOSIS ADAPTADAS", $wactualiz, 'clinica');
		
		echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
		echo "<input type='hidden' id='wcenpro' name='wcenpro' value='".$wcenpro."'>";
		
		
		$filtrosMonitor = pintarFiltrosMonitor();
		echo $filtrosMonitor;
		
		if($cco==null)
		{
			$cco = "";
		}
		
		if($ronda==null)
		{
			$ronda = "";

		}
		
		echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";	
		
		echo "<div id='dvAuxModalPreparacionDA' style='display:none'></div>";
		echo "<div id='dvAuxModalAnulados' style='display:none'></div>";
		
		$dosisAdaptadasParaPreparar = consultarDAparaPreparar($wemp_pmla,$wbasedato,$wcenpro,$cco,$ronda);
		echo $dosisAdaptadasParaPreparar;
		
		echo "	<p align=center><input type='button' onclick='cerrarVentana();' value='Cerrar Ventana'></p>";	
		
	}
	
	
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
