<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//
//  2021-11-24.   Daniel CB. -Se realiza corrección de parametro 01 quemado 
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
$wactualiz='2021-11-24';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
/**
	2019-10-08, Jerson Trujillo:
		Se coloca utf8_encode en la observacion de la nota al consultarla desde de unix. ya que estaba generando un error a pintar.
 Enero 12 2017 Edwar Jaramillo:
 	* La consulta de facturas en unix para traer el detalle, ahora se filtra también por el estado de cartera para facturas.
	* Se realiza corrección para consultar el detalle de factura unix por concepto (facardet), diferenciando por código de empresa
		según el código de empresa de famovdet (movdetnit), pues el campo de valor facturado no estaba teniendo en cuenta cuando
		un concepto estaba para diferente entidad, solo estaba sumando una vez el valor facturado cuando ese concepto estaba para
		diferentes códigos de empresa. Con esto se soluciona que al buscar el valor facturado en unix (Menú consultas>Movimiento de cuentas por cobrar)
		corresponda con el falor facturado al traer el detalle de factura en el registro de glosas.
*/
//--------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']))
{
	if(isset($resSessionJson) && $resSessionJson)
	{
		$respuesta['msjSession'] = 'Primero recargue la p&aacute;gina principal de Matrix &oacute; inicie sesi&oacute;n nuevamente, para poder relizar esta acci&oacute;n.';
		
		echo json_encode($respuesta);
		return;		
	}
	else
	{
		echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
					[?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
				</div>';
		return;
	}
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	

	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//---------------------------------------------------------
	// -->	Dar formato a los valores
	//---------------------------------------------------------
	function formato($numero)
	{
		return number_format((double)$numero,0,'.',',');
	}
	//---------------------------------------------------------
	// -->	Pinta una lista de las glosas registradas
	//---------------------------------------------------------
	function listaGlosasRegistradas($selectEstadoGLosa = '%', $fechaBuscar1, $fechaBuscar2, $rolUsuario, $verFarmacia)
	{
		global $wbasedato;
		global $wuse;
		global $conex;

		$respuesta 			= array("Html" => "");
		$wbasedatoFar 		= consultarAliasPorAplicacion($conex, '09', 'farpmla');
		$arr_estadoCartera 	= estadosCarteraFactura($conex, $wbasedato);

		$respuesta["Html"] = "
		<table width='100%' id='tableListaGlosas'>
			<tr align='center' class='encabezadoTabla'>
				<td>Fecha Hora</td>
				<td>Historia</td>
				<td>Nro factura</td>
				<td>Entidad</td>
				<td>Nro radicado</td>
				<td>Nro relaci&oacute;n</td>
				<td>Total</td>
				<td>Respuesta</td>
				<td>Est. cartera Factura</td>
				<td>Estado regitro</td>
			</tr>";
	
		// --> Consultar glosas registradas
		
		if($verFarmacia != 'on')
		{
			$sqlGlo = "
			SELECT A.id, Glofhg, Glonfa, Gloent, Glotot, Glonrg, Glofhr, Glorad, Glohis, Gloing, Gloesg, Gloecf, Glofar, Empnom
			  FROM ".$wbasedato."_000273 AS A LEFT JOIN ".$wbasedato."_000024 AS B ON(A.Gloent = B.Empcod)
			 WHERE Gloest = 'on'
			   AND Gloesg LIKE '".$selectEstadoGLosa."'
			   AND Glofhg BETWEEN '".$fechaBuscar1." 00:00:00' AND '".$fechaBuscar2." 23:59:59'
			   AND Glorol = '".$rolUsuario."'
			   AND Glofar != 'on'
			 ORDER BY Glofhg DESC";
		}
		else
		{
			$sqlGlo = "
			SELECT A.id, Glofhg, Glonfa, Gloent, Glotot, Glonrg, Glofhr, Glorad, Glohis, Gloing, Gloesg, Gloecf, Glofar, Empnom
			  FROM ".$wbasedato."_000273 AS A LEFT JOIN ".$wbasedatoFar."_000024 AS B ON(A.Gloent = B.Empcod)
			 WHERE Gloest = 'on'
			   AND Gloesg LIKE '".$selectEstadoGLosa."'
			   AND Glofhg BETWEEN '".$fechaBuscar1." 00:00:00' AND '".$fechaBuscar2." 23:59:59'
			   AND Glorol = '".$rolUsuario."'
			   AND Glofar = 'on'
			 ORDER BY Glofhg DESC ";
		}
		
		$resGlo 					= mysql_query($sqlGlo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGlo):</b><br>".mysql_error());
		$colFila					= "fila2";
		$respuesta["numRegistros"] 	= mysql_num_rows($resGlo);
		
		while($rowGlo = mysql_fetch_array($resGlo))
		{
			switch($rowGlo['Gloesg'])
			{
				case 'GL':
					$estado = 'Glosada';
					break;
				case 'GR':
					$estado = 'Respondida';
					break;
				case 'RA':
					$estado = 'Auditada';
					break;
				case 'AP':
					$estado = 'Generada(Arc Plano)';
					break;
				case 'AN':
					$estado = 'Anulada';
					break;
			}

			$Gloecf    = "";
			$GloecfNom = "";
			if(count($rowGlo)>0)
			{
				$Gloecf    = $rowGlo['Gloecf'];
				$GloecfNom = (array_key_exists($rowGlo['Gloecf'], $arr_estadoCartera["ReqRad"])) ? $rowGlo['Gloecf'].'-'.$arr_estadoCartera["ReqRad"][$rowGlo['Gloecf']]["nombre"] : $rowGlo['Gloecf'];
			}

			$colFila = (($colFila == 'fila2') ? 'fila1' : 'fila2');
			$respuesta["Html"].= "
			<tr class='".$colFila." find'onclick='verGlosa(\"".$rowGlo['id']."\")' style='cursor:pointer'>
				<td align='center'>".$rowGlo['Glofhg']."</td>
				<td align='center'>".$rowGlo['Glohis']."-".$rowGlo['Gloing']."</td>
				<td align='center'>".$rowGlo['Glonfa']."</td>
				<td>".utf8_encode($rowGlo['Empnom'])."</td>
				<td>".$rowGlo['Glorad']."</td>
				<td>".$rowGlo['id']."</td>
				<td align='center'>".(($rowGlo['Glotot'] == 'on')? 'SI' : 'NO')."</td>
				<td align='center'>".$rowGlo['Glofhr']."</td>
				<td align='center'>".$GloecfNom."</td>
				<td align='center'>".$estado."</td>
			</tr>
			";
		}

		if(mysql_num_rows($resGlo) == 0)
		{
			$respuesta["Html"].= "
			<tr>
				<td class='fila1' colspan='10' align='center'>Sin registros</td>
			</tr>";
		}

		$respuesta["Html"].= "
		</table>";

		return $respuesta;
	}
	function seguimientoLog($texto='')
	{
		$tipoEscritura 	= ((date('d') == '01') ? 'w+' : 'a+');
		$archivo		= fopen("LogRegistroGlosas.txt", $tipoEscritura);
		$log			= PHP_EOL.'--> '.date( "Y-m-d" )." ".date("H:i:s").PHP_EOL.$texto;
		fputs($archivo, $log);
		fclose($archivo);
	}
	//---------------------------------------------------------
	// -->	Pintar formulario de una glosa
	//---------------------------------------------------------
	function detalleGlosa($idGlosa='', $factura, $facturaDeFarmacia, $hisFar, $ingFar, $rolUsuario='')
	{
		global $wbasedato;
		global $wuse;
		global $conex;
		global $wemp_pmla;

		$respuesta 				= array('Error' => FALSE, 'Mensaje' => '', 'Html' => '', 'Entidad' => '', 'Fecha' => '', 'Historia' => '', 'Ingreso' => '', 'MensajeDetalle' => '', 
										'estadoFacturaUnix' => '', 'estadoFacturaNombre' => '', 'tieneNotaCredito' => '', 'SQL');
		$conexUnix				= odbc_connect('facturacion','informix','sco');
		$rowDetalleGlosaMatrix 	= array();
		$devoluciones 			= array();
		$estadoGlosa 			= "";		
		$arrayDetalle 			= array();

		// --> Consultar numero de factura que ya fue glosada
		if($idGlosa != '')
		{
			$sqlGlo = "
			SELECT Glonfa, Gloesg, Glofar, Gdereg, Gdecgl, Gdevgl, Gdecau, Gderes, Gdevac, Glorol, Gdecre, Gdeobj, Gdeuob, B.id
			  FROM ".$wbasedato."_000273 AS A LEFT JOIN ".$wbasedato."_000274 AS B ON(A.id = B.Gdeidg)
			 WHERE A.id 	= '".$idGlosa."'
			   AND Gdeest 	= 'on'			 
			";
			$resGlo = mysql_query($sqlGlo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGlo):</b><br>".mysql_error());
			while($rowGlo = mysql_fetch_array($resGlo))
			{
				$factura 			= $rowGlo['Glonfa'];
				$estadoGlosa		= $rowGlo['Gloesg'];
				$rolGlosa			= $rowGlo['Glorol'];
				$facturaDeFarmacia 	= ($rowGlo['Glofar'] == '') ? 'off' : $rowGlo['Glofar'];

				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdecgl'] = $rowGlo['Gdecgl'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdevgl'] = $rowGlo['Gdevgl'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdecau'] = $rowGlo['Gdecau'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gderes'] = $rowGlo['Gderes'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['idReg'] 	= $rowGlo['id'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdevac'] = $rowGlo['Gdevac'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdecre'] = $rowGlo['Gdecre'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdeobj'] = $rowGlo['Gdeobj'];
				$rowDetalleGlosaMatrix[$rowGlo['Gdereg']]['Gdeuob'] = $rowGlo['Gdeuob'];
			}
		}
		
		// --> Traer maestro de causas
		$arrayCausas 	= array();
		$sqlCausas 		= "
		SELECT Caucod, Caunom
		  FROM ".$wbasedato."_000276
		 WHERE Cauest = 'on'
		";
		$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
		while($rowCausas = mysql_fetch_array($resCausas))
			$arrayCausas[$rowCausas['Caucod']] = utf8_encode(trim($rowCausas['Caunom']));
		
		//-------------------------------------------------------
		//	--> Factura de UNIX
		//-------------------------------------------------------
		if($facturaDeFarmacia == 'off')
		{
			if(trim($rolUsuario) == 'SOA')
				$validarSaldo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'validarSaldoParaRegistrarGlosasSoat');
			else
				$validarSaldo = 'on';
			
			// --> Validar que la factura si tenga saldo pendiente por pagar
			if($validarSaldo == 'on'){
				$sqlFacSal = "
				SELECT sallinval
				  FROM CASALLIN
				 WHERE sallinfue = '20'
				   AND sallindoc = '".$factura."'
				";
				$respuesta['SQL']['sqlFacSal'] = $sqlFacSal;
				
				$resFacSal = odbc_exec($conexUnix, $sqlFacSal);
				if(!odbc_fetch_row($resFacSal))
				{
					$respuesta["Error"] 	= true;
					$respuesta["Mensaje"] 	= "No se encontro saldo pendiente para la factura";
					return $respuesta;
				}
			}
			
			// --> 	Validar que la factura no tenga problema de saldos, si tiene un registro de saldo con un tercero igual cero
			//		es porque hay una inconsistencia, el unico registro que puede tener tercero 0 es el 8888
			$sqlSalInc = "
			SELECT carconcon, carconter
			  FROM cacarcon
			 WHERE carconfue = '20'
			   AND carcondoc = '".$factura."'
			   AND carconcon != '8888'
			   AND carconter = '0'
			 GROUP BY 1,2
			 ORDER BY 1,2
			";
			$respuesta['SQL']['sqlSalInc'] = $sqlSalInc;
			
			$resSalInc = odbc_exec($conexUnix, $sqlSalInc);
			if(odbc_fetch_row($resSalInc))
			{				
				$respuesta["Error"] 	= true;
				$respuesta["Mensaje"] 	= "
					<img width='15' heigth='15' src='../../images/medical/sgc/Warning-32.png'>&nbsp;Esta glosa no se puede revisar, ya que existe una inconsistencia en el valor de los saldos en UNIX. En el concepto ".odbc_result($resSalInc,'carconcon').".
					Por favor reportar esta incosistencia con soporte de sistemas.";
					
				return $respuesta;
			}				

			// --> Obtener maestro de procedimientos en matrix
			$arrayProcedimientos = array();
			$sqlPro = "
			SELECT Procod, Pronom
			  FROM ".$wbasedato."_000103
			";
			$resPro = mysql_query($sqlPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPro):</b><br>".mysql_error());
			while($rowPro = mysql_fetch_array($resPro))
				$arrayProcedimientos[$rowPro['Procod']] = utf8_encode(trim($rowPro['Pronom']));
			
			// --> Obtener maestro de conceptos de ayudas
			$arrayConceptosAyudas = array();
			$sqlAyu = "
			SELECT Congen
			  FROM ".$wbasedato."_000197
			 WHERE Consim = '0700'
			   AND Conest = 'on'
			";
			$resAyu = mysql_query($sqlAyu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAyu):</b><br>".mysql_error());
			while($rowConceptosAyudas = mysql_fetch_array($resAyu))
				$arrayConceptosAyudas[] = utf8_encode(trim($rowConceptosAyudas['Congen']));

			// --> Consultar el estado de la factura
			$sqlEstFac = "
			SELECT encest, estnom
			  FROM caenc, caest
			 WHERE encfue = '20'
			   AND encdoc = '".$factura."'
			   AND encest = estcod
			";
			$resEstFac = odbc_exec($conexUnix, $sqlEstFac);
			$respuesta['SQL']['sqlEstFac'] = $sqlEstFac;
			if(odbc_fetch_row($resEstFac))
			{
				$estado_factura_unix 				= trim(odbc_result($resEstFac,'encest'));
				$respuesta["estadoFacturaUnix"] 	= $estado_factura_unix;
				$respuesta["estadoFacturaNombre"] 	= trim(odbc_result($resEstFac,'estnom'));
				
				// --> Consultar el encabezado de la factura
				$sqlEncFac = "
				SELECT movhis, movnum, movcer, movres, movfuo
				  FROM FAMOV
				 WHERE movfue = '20'
				   AND movdoc = '".$factura."'
				";
				$respuesta['SQL']['sqlEncFac'] = $sqlEncFac;
				$resEncFac = odbc_exec($conexUnix, $sqlEncFac);
				if(odbc_fetch_row($resEncFac))
				{
					$empresasNoDetPorCco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigosEmpresasQueNoDetallanGlosaPorCco');
					$empresasNoDetPorCco = explode(",", $empresasNoDetPorCco);
					
					$historia 						= trim(odbc_result($resEncFac,'movhis'));
					$ingreso 						= trim(odbc_result($resEncFac,'movnum'));
					$movfuo 						= trim(odbc_result($resEncFac,'movfuo'));
					$codEntidad 					= trim(odbc_result($resEncFac,'movcer'));
					$nomEntidad 					= trim(odbc_result($resEncFac,'movres'));
					$respuesta["Entidad"]			= $codEntidad."-".$nomEntidad;
					$respuesta["codEntidad"]		= $codEntidad;
					$respuesta["Fecha"]				= date("Y-m-d");
					$respuesta["Historia"]			= $historia;
					$respuesta["Ingreso"]			= $ingreso;
					
					if(in_array($codEntidad, $empresasNoDetPorCco))
						$respuesta["detallarPorCco"]	= 'off';
					else
						$respuesta["detallarPorCco"]	= 'on';
					
					$respuesta["tieneNotaCredito"]	= 'off';
					$respuesta["htmlNotaCredito"]	= '<table><tr align=center class=encabezadoTabla><td>Nota cr&eacutedito</td><td>Concepto</td><td>Valor</td></tr>';
					
					// --> Validar si la factura tiene nota credito
					$sqlTieneNotaCre = "
					SELECT movdetfue, movdetdoc, movdetcon, movdetval, connom
					  FROM CACAR, CAFUE, FAMOVDET, FACON
					 WHERE carfca 		= '20'
					   AND carfac 		= '".$factura."'
					   AND caranu 		= 0
					   AND carfue 		= fuecod
					   AND carcco 		= fuecco
					   AND fuetip 		= 'NC'
					   AND movdetfue	= carfue
					   AND movdetdoc	= cardoc
					   AND movdetanu	= 0
					   AND concod		= movdetcon
					   
					";
					$total 				= 0;
					$observacionNota 	= "";
					$docuNotaCreditoANT	= "";
					
					$respuesta['SQL']['sqlTieneNotaCre'] = $sqlTieneNotaCre;
					$resTieneNotaCre = odbc_exec($conexUnix, $sqlTieneNotaCre);
					while(odbc_fetch_row($resTieneNotaCre))
					{
						$fuenteNotaCredito 	= trim(odbc_result($resTieneNotaCre,'movdetfue'));
						$docuNotaCredito 	= trim(odbc_result($resTieneNotaCre,'movdetdoc'));
						
						$total+= odbc_result($resTieneNotaCre,'movdetval');
						
						$respuesta["tieneNotaCredito"] = 'on';
						
						if($docuNotaCredito != $docuNotaCreditoANT && $docuNotaCreditoANT != "")
						{
							$observacionNota = "<b>Observaci&oacute;n:</b> ";
							// --> Consultar observaciones de la nota credito
							$sqlObs = "
							SELECT carobsdes
							  FROM CACAROBS
							 WHERE carobsfue	= ".$fuenteNotaCreditoANT."
							   AND carobsdoc 	= ".$docuNotaCreditoANT."
							";
							$resObs 			= odbc_exec($conexUnix, $sqlObs);
							while(odbc_fetch_row($resObs))
							{
								$observacionNota.= " ".trim(odbc_result($resObs,'carobsdes'));
							}
							
							$respuesta["htmlNotaCredito"].="
							<tr>
								<td colspan='2' class='fila1'>".utf8_encode($observacionNota)."</td>
								<td class='fila1' align='right'><b>$".number_format($subTotal, 0, '.', ',')."</b></td>
							</tr>";
							
							$subTotal = 0;
						}
						
						$respuesta["htmlNotaCredito"].="
						<tr class=fila2>
							<td align='center'>".trim(odbc_result($resTieneNotaCre,'movdetfue'))."-".trim(odbc_result($resTieneNotaCre,'movdetdoc'))."</td>
							<td align='left'>".trim(odbc_result($resTieneNotaCre,'movdetcon'))."-".utf8_encode(trim(odbc_result($resTieneNotaCre,'connom')))."</td>
							<td align='right'>$ ".number_format(trim(odbc_result($resTieneNotaCre,'movdetval')), 0, '.', ',')."</td>
						</tr>
						";
						
						$subTotal+= odbc_result($resTieneNotaCre,'movdetval');
						
						$docuNotaCreditoANT 	= $docuNotaCredito;
						$fuenteNotaCreditoANT 	= $fuenteNotaCredito;
					}
					
					
					// --> Consultar observacion de la ultima nota credito del ciclo
					if($respuesta["tieneNotaCredito"] == "on")
					{
						$observacionNota = "<b>Observaci&oacute;n:</b> ";
						$sqlObs = "
						SELECT carobsdes
						  FROM CACAROBS
						 WHERE carobsfue	= ".$fuenteNotaCredito."
						   AND carobsdoc 	= ".$docuNotaCredito."
						";
						$resObs 			= odbc_exec($conexUnix, $sqlObs);
						while(odbc_fetch_row($resObs))
						{
							$observacionNota.= " ".trim(odbc_result($resObs,'carobsdes'));
						}
					}
					
					$respuesta["htmlNotaCredito"].="
					<tr>
						<td colspan='2' class='fila1'>".utf8_encode($observacionNota)."</td>
						<td class='fila1' align='right'><b>$".number_format($subTotal, 0, '.', ',')."</b></td>
					</tr>";
					
					$respuesta["htmlNotaCredito"].="
						<tr style='background-color:#FFFFFF;color: #000000;font-size: 8pt;padding: 1px;font-family: verdana;'>
							<td colspan=2 align=right><b>Total:</b></td>
							<td align=right>$".number_format($total, 0, '.', ',')."</td>
						</tr>";
					
					$respuesta["htmlNotaCredito"].="
					</table>";
				}
				else
				{
					$respuesta["Error"] 	= true;
					$respuesta["Mensaje"] 	= "No se encontro informacion del encabezado de la factura";
					return $respuesta;
				}
			}
			else
			{
				$respuesta["Error"] 	= true;
				$respuesta["Mensaje"] 	= "No se encontro informacion del estado de la factura";
				return $respuesta;
			}
			
			// --> Validar que tipo de nombre de articulo maneja la empresa (Comercial, generico)
			$nomArt 	= 'Artgen';
			$sqlTipArt 	= "
			SELECT Temiac
			  FROM ".$wbasedato."_000024 INNER JOIN ".$wbasedato."_000029 ON(Emptem = Temcod)
			 WHERE Empcod = '".$codEntidad."'
			";
			$resTipArt = mysql_query($sqlTipArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipArt):</b><br>".mysql_error());
			if($rowTipArt = mysql_fetch_array($resTipArt))
				$nomArt = ($rowTipArt['Temiac'] == 'on') ? 'Artcom' : $nomArt;
			
			// --> Obtener maestro de articulos
			$arrayArticulo = array();
			$sqlArt = "
			SELECT Artcod, ".$nomArt."
			  FROM movhos_000026
			";
			$resArt = mysql_query($sqlArt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlArt):</b><br>".mysql_error());
			while($rowArt = mysql_fetch_array($resArt))
				$arrayArticulo[trim($rowArt['Artcod'])] = utf8_encode(trim($rowArt[$nomArt]));
			

			// --> Consultar si la factura es de ayudas o de hospitalizacion
			$sqlTabFac = "
			SELECT count(*) AS cant
			  FROM FACARFAC
			 WHERE carfacfue = '20'
			   AND carfacdoc = '".$factura."'
			   AND carfacanu = '0'
			";
			$respuesta['SQL']['sqlTabFac'] = $sqlTabFac;
			$resTabFac = odbc_exec($conexUnix, $sqlTabFac);
			if(odbc_fetch_row($resTabFac))
			{
				if(trim(odbc_result($resTabFac, 'cant')) > 0)
				{
					$tablaCarfac = "FACARFAC";
					$tablaCardet = "FACARDET";
				}
				else
				{
					$sqlTabFac = "
					SELECT count(*) AS cant
					  FROM AYCARFAC
					 WHERE carfacfue = '20'
					   AND carfacdoc = '".$factura."'
					   AND carfacanu = '0'
					";
					$resTabFac = odbc_exec($conexUnix, $sqlTabFac);
					if(odbc_fetch_row($resTabFac))
					{
						if(trim(odbc_result($resTabFac, 'cant')) > 0)
						{
							$tablaCarfac = "AYCARFAC";
							$tablaCardet = "AYCARDET";
						}
						else
						{
							$respuesta["Error"] 	= true;
							$respuesta["Mensaje"] 	= "No se encontro informacion del detalle de la factura";
							return $respuesta;
						}
					}
				}
			}

			$hayRes					= false;
			$conceptosParaSaldos 	= array();
			
			// --> Consultar la factura por concepto
			$sqlDetFacCon = "
			SELECT movdetcon, SUM(movdetval) as val, connom, concon, conarc
			  FROM FAMOVDET, FACON
			 WHERE movdetfue = '20'
			   AND movdetdoc = '".$factura."'
			   AND movdetanu = '0'
			   AND movdetcon = concod
			   AND conarc	 IS NOT NULL
			 GROUP BY 1,3,4,5
			 UNION
			 SELECT movdetcon, SUM(movdetval) as val, connom, concon, ' ' as conarc
			  FROM FAMOVDET, FACON
			 WHERE movdetfue = '20'
			   AND movdetdoc = '".$factura."'
			   AND movdetanu = '0'
			   AND movdetcon = concod
			   AND conarc	 IS NULL
			 GROUP BY 1,3,4,5		   
			";
			$respuesta['SQL']['sqlDetFacCon'] = $sqlDetFacCon;
			// AND movdetcon = '0026'
			$resDetFacCon = odbc_exec($conexUnix, $sqlDetFacCon);
			while(odbc_fetch_row($resDetFacCon))
			{
				$concepto 			= trim(odbc_result($resDetFacCon,'movdetcon'));
				$valorFacConcepto 	= trim(odbc_result($resDetFacCon,'val'));
				// $movdetnit 			= trim(odbc_result($resDetFacCon,'movdetnit'));
				$nomConcep 			= trim(odbc_result($resDetFacCon,'connom'));
				$conceptoParaSaldo	= trim(odbc_result($resDetFacCon,'concon'));
				$archivoTarifas		= trim(odbc_result($resDetFacCon,'conarc'));
				
				// --> Validar si el concepto y tercero tiene % 0
				// if($movdetnit != '' && $movdetnit != '0')
				// {
					// $sqlPorcentajeCero = "
					// SELECT connitpor
					  // FROM faconnit 
					 // WHERE connitcon = '".$concepto."'	
					   // AND (connittse = '*' OR connittse = 'P')
					   // AND connitnit = '".$movdetnit."'			   
					// ";
					// $respuesta['SQL']['sqlPorcentajeCero'][] = $sqlPorcentajeCero;
					// $resPorcentajeCero = odbc_exec($conexUnix, $sqlPorcentajeCero);
					// if(odbc_fetch_row($resPorcentajeCero))
					// {
						// if(trim(odbc_result($resPorcentajeCero,'connitpor')) == '0')
							// $conceptoParaSaldo = '8888';
					// }
				// }
				
				// --> 	Si el concepto para saldo tiene registros en la tabla de saldos
				$sqlConceptoParaSaldo = "
				SELECT count(*) as C
				  FROM CACARCON
				 WHERE carconfca = '20'
				   AND carconfac = '".$factura."'
				   AND carconcon = '".$conceptoParaSaldo."'
				";
				$respuesta['SQL']['sqlConceptoParaSaldo'] = $sqlConceptoParaSaldo;
				$resConceptoParaSaldo = odbc_exec($conexUnix, $sqlConceptoParaSaldo);
				if(odbc_fetch_row($resConceptoParaSaldo))
				{
					// --> sino tiene registros es porque los movimientos se hicieron con el 8888
					if(odbc_result($resConceptoParaSaldo, 'C') == 0)
						$conceptoParaSaldo = '8888';
				}			

				if(!array_key_exists($concepto, $arrayDetalle))
				{
					$arrayDetalle[$concepto] = array();
					$arrayDetalle[$concepto]['Nombre']	 			= $nomConcep;
					$arrayDetalle[$concepto]['valorFacConcepto']	= $valorFacConcepto;
					$arrayDetalle[$concepto]['conceptoParaSaldo']	= $conceptoParaSaldo;
					$arrayDetalle[$concepto]['Detalle']				= array();
				}
				else
				{
					$arrayDetalle[$concepto]['valorFacConcepto'] += $valorFacConcepto;
				}

				if(array_key_exists($conceptoParaSaldo, $conceptosParaSaldos))
					$conceptosParaSaldos[$conceptoParaSaldo]['valorFacturado']+= $valorFacConcepto;
				else
					$conceptosParaSaldos[$conceptoParaSaldo]['valorFacturado'] = $valorFacConcepto;

				// --> Consultar el detalle de la factura en unix por cada concepto
				// echo "|".$archivoTarifas."--->";
				
				if($archivoTarifas == 'IVARTTAR')
				{
					$sqlDetFac = "
					SELECT cardetcod, cardetcan, carfacval, cardetfue, cardetdoc, cardetite, cardetcco, cardetreg, cardetvun, cardetnit,
						   drodetart, drodetcan
					  FROM ".$tablaCarfac.", ".$tablaCardet.", IVDRODET
					 WHERE carfacfue = '20'
					   AND carfacdoc = '".$factura."'
					   AND carfacanu = '0'
					   AND carfacreg = cardetreg
					   AND cardetcon = '".$concepto."'
					   AND drodetfue = cardetfue
					   AND drodetdoc = cardetdoc
					   AND drodetite = cardetite
					 ORDER BY cardetcod
					";
				}
				else
				{
					$sqlDetFac = "
					SELECT cardetcod, cardetcan, carfacval, cardetfue, cardetdoc, cardetite, cardetcco, cardetreg, cardetvun, cardetnit
					  FROM ".$tablaCarfac.", ".$tablaCardet."
					 WHERE carfacfue = '20'
					   AND carfacdoc = '".$factura."'
					   AND carfacanu = '0'
					   AND carfacreg = cardetreg
					   AND cardetcon = '".$concepto."'
					 ORDER BY cardetcod
					";
				}
				//AND cardetnit = '".$entidadFacConcepto."'
				$respuesta['SQL']['sqlDetFac'][] = $sqlDetFac;
				$resDetFac 		= odbc_exec($conexUnix, $sqlDetFac);
				while(odbc_fetch_row($resDetFac))
				{				
					$hayRes       = true;
					$valorFact    = trim(odbc_result($resDetFac,'carfacval'));
					//$valorUnit    = trim(odbc_result($resDetFac,'cardetvun'));
					$fuente       = trim(odbc_result($resDetFac,'cardetfue'));
					$documento    = trim(odbc_result($resDetFac,'cardetdoc'));
					$item         = trim(odbc_result($resDetFac,'cardetite'));
					$cardetreg    = trim(odbc_result($resDetFac,'cardetreg'));
					$terceroUnix  = trim(odbc_result($resDetFac,'cardetnit'));
					$ccoUnix  	  = trim(odbc_result($resDetFac,'cardetcco'));
					$nomTercero	  = "";
					
					if($terceroUnix != '0' && $terceroUnix != '')
					{
						// --> Consultar nombre del tercero
						$sqlNomTercero = "
						SELECT tertipnom
						  FROM tetertip
						 WHERE tertipter = '".$terceroUnix."'
						";
						$resNomTercero = odbc_exec($conexUnix, $sqlNomTercero);
						if(odbc_fetch_row($resNomTercero))
						{
							$nomTercero	= trim(odbc_result($resNomTercero,'tertipnom'));
						}

						// --> Validar si el registro fue grabado con % cero
						if($conceptoParaSaldo != '8888')
						{
							$sqlTeter = "
							SELECT count(*) AS C
							  FROM TETER
							 WHERE terfue = '".$fuente."'
							   AND terdoc = '".$documento."'
							   AND ternum = '".$cardetreg."'						   
							";
							$resTeter 		= odbc_exec($conexUnix, $sqlTeter);
							if(odbc_fetch_row($resTeter))
							{
								$hayRegTeter = trim(odbc_result($resTeter,'C'));
								// --> 	Si se grabo con % cero y el valor facturado se sumó al mismo concepto, entonces debo restar ese valor facturado
								// 		y sumarselo al concepto 8888 donde van todos los conceptos propios
								if($hayRegTeter == 0)
								{
									$respuesta['SQL']['sqlTeter'][] = "->".$concepto."=".$sqlTeter;
									$conceptosParaSaldos['8888']['valorFacturado']+=$valorFact;
									$conceptosParaSaldos[$concepto]['valorFacturado']-=$valorFact;
									// if($conceptosParaSaldos[$concepto]['valorFacturado'] == 0)
										// unset($conceptosParaSaldos[$concepto]);
								}	
							}
						}
					}
					
					// --> Materiales o medicamentos
					if($archivoTarifas == 'IVARTTAR')
					{
						$codigo	 	= trim(odbc_result($resDetFac,'drodetart'));
						$cantidad 	= trim(odbc_result($resDetFac,'drodetcan'));
						$nombreCod	= (array_key_exists($codigo, $arrayArticulo)) ? $arrayArticulo[$codigo] : '';
							
						// --> Consultar detalle de los articulos
						// $sqlDetArticulos = "
						// SELECT drodetart, drodetcan
						  // FROM IVDRODET
						 // WHERE drodetfue = '".$fuente."'
						   // AND drodetdoc = '".$documento."'
						   // AND drodetite = '".$item."'
						// ";
						// $resDetArticulos 	= odbc_exec($conexUnix, $sqlDetArticulos);
						// if(odbc_fetch_row($resDetArticulos))
						// {
							// $codigo	 	= trim(odbc_result($resDetArticulos,'drodetart'));
							// $cantidad 	= trim(odbc_result($resDetArticulos,'drodetcan'));
							// $nombreCod	= (array_key_exists($codigo, $arrayArticulo)) ? $arrayArticulo[$codigo] : '';
						// }
						// else
						// {
							// $codigo	 	= "-";
							// $cantidad 	= "-";
						// }
					}
					// --> Otros conceptos
					else
					{
						$codigo	 	= trim(odbc_result($resDetFac,'cardetcod'));
						$cantidad 	= trim(odbc_result($resDetFac,'cardetcan'));
						$nombreCod	= (array_key_exists($codigo, $arrayProcedimientos)) ? $arrayProcedimientos[$codigo] : '';
						
						// --> Si no se obtuvo el nombre, lo consulto en unix
						if($nombreCod == "")
						{
							if($codigo == "0")
								$nombreCod = "";
							else
							{
								$sqlObtNomUni = "
								SELECT exanom as nombre
								  FROM inexa
								 WHERE exacod = '".$codigo."'
								 UNION
								SELECT pronom as nombre
								  FROM inpro
								 WHERE procod = '".$codigo."'						 
								";
								$resObtNomUni 	= odbc_exec($conexUnix, $sqlObtNomUni);
								if(odbc_fetch_row($resObtNomUni))
									$nombreCod = trim(odbc_result($resObtNomUni,'nombre'));
							}
						}
					}				
					
					$valorUnit = @($valorFact/$cantidad);

					// --> No incluir devoluciones
					if($valorFact >= 0)
					{				
						// --> 	Validar si el codigo del material o del medicamento ya existe, para ir agrupandolo en un solo registro
						//		Esto tambien lo hago si el concepto pertenece al grupo de ayudas diagnosticas.
						//		Para poder agrupar deben tener el mismo valor unitario.
						//		2017-02-20: Se decide agrupar todos los conceptos
						$indiceExiste = "";
						// if($archivoTarifas == 'IVARTTAR' || in_array($concepto, $arrayConceptosAyudas))
						// {						
						foreach($arrayDetalle[$concepto]['Detalle'] as $indiceTemp => $nfoDet)
						{
							
							if(trim($nfoDet['codigo']) == trim($codigo) && trim($nfoDet['valorUnit']) == trim($valorUnit) && trim($nfoDet['terceroUnix']) == trim($terceroUnix))
								$indiceExiste = $indiceTemp;
						}
						// }
						
						if($indiceExiste !== '')
						{
							$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['valorFact']+= $valorFact;
							$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['cantidad']+= $cantidad;
							$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['cardetreg'].= "-".$cardetreg;
							
							// --> Nuevo detalle por cco
							$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['detCco'][$ccoUnix]['cantCco']+= $cantidad;
							$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['detCco'][$ccoUnix]['valFacCco']+= $valorFact;
							$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['detCco'][$ccoUnix]['regCco'].= "-".$cardetreg;
						}
						else
						{					
							$indice = count($arrayDetalle[$concepto]['Detalle']);
							$arrayDetalle[$concepto]['Detalle'][$indice]['valorFact']    = $valorFact;
							$arrayDetalle[$concepto]['Detalle'][$indice]['valorUnit']    = $valorUnit;
							$arrayDetalle[$concepto]['Detalle'][$indice]['codigo']       = $codigo;
							$arrayDetalle[$concepto]['Detalle'][$indice]['cantidad']     = $cantidad;
							$arrayDetalle[$concepto]['Detalle'][$indice]['nombreCod']    = $nombreCod;
							$arrayDetalle[$concepto]['Detalle'][$indice]['cardetreg']    = "*-".$cardetreg;
							$arrayDetalle[$concepto]['Detalle'][$indice]['entidadCargo'] = $entidadCargo;
							$arrayDetalle[$concepto]['Detalle'][$indice]['terceroUnix']  = $terceroUnix;
							$arrayDetalle[$concepto]['Detalle'][$indice]['nomTercero']   = $nomTercero;
							
							// --> Nuevo detalle por cco
							$arrayDetalle[$concepto]['Detalle'][$indice]['detCco'][$ccoUnix]['cantCco'] 	= $cantidad;
							$arrayDetalle[$concepto]['Detalle'][$indice]['detCco'][$ccoUnix]['valFacCco'] 	= $valorFact;
							$arrayDetalle[$concepto]['Detalle'][$indice]['detCco'][$ccoUnix]['regCco'] 		= $cardetreg;
						}
					}
					else
					{
						if(!array_key_exists($concepto, $devoluciones))
							$devoluciones[$concepto] = array();

						if(!array_key_exists($codigo, $devoluciones[$concepto]))
							$devoluciones[$concepto][$codigo]['cantidad'] = 0;
						
						$devoluciones[$concepto][$codigo]['cantidad']+= 		$cantidad;					
						$devoluciones[$concepto][$codigo]['valorFact']+= 		$valorFact;
						$devoluciones[$concepto][$codigo]['valorUnit'] 		= 	$valorUnit;
						$devoluciones[$concepto][$codigo]['nombreCod'] 		= 	$nombreCod;
						$devoluciones[$concepto][$codigo]['cardetreg'].= 		$cardetreg."-";
						$devoluciones[$concepto][$codigo]['entidadCargo'] 	= 	$entidadCargo;
						$devoluciones[$concepto][$codigo]['terceroUnix']  	= 	$terceroUnix;
						$devoluciones[$concepto][$codigo]['nomTercero']   	= 	$nomTercero;
						$devoluciones[$concepto][$codigo]['detCco'][$ccoUnix]['cantCco']+= $cantidad;
						$devoluciones[$concepto][$codigo]['detCco'][$ccoUnix]['valFacCco']+= $valorFact;
						$devoluciones[$concepto][$codigo]['detCco'][$ccoUnix]['regCco'].= $cardetreg;
					}
				}
			}
			
			$respuesta['SQL']['arrayDetalle'] = json_encode($arrayDetalle);
			
			if($hayRes)
			{
				// --> Consultar saldos por concepto
				$sqlSalCon = "
				SELECT (carconval*tipmul) as valor, carconcon
				  FROM CACARCON, CATIP
				 WHERE carconfca = '20'
				   AND carconfac = '".$factura."'
				   AND carconfue = tipfue
				";
				$respuesta['SQL']['sqlSalCon'] = $sqlSalCon;
				$resSalCon = odbc_exec($conexUnix, $sqlSalCon);
				while(odbc_fetch_row($resSalCon))
				{
					$conSal = trim(odbc_result($resSalCon, 'carconcon'));

					if(array_key_exists($conSal, $conceptosParaSaldos))
						$conceptosParaSaldos[$conSal]['valorSaldo']+= trim(odbc_result($resSalCon,'valor'));
					else
						$conceptosParaSaldos[$conSal]['valorSaldo'] = trim(odbc_result($resSalCon,'valor'));
				}
				
				//print_r($arrayDetalle);

				// --> Calcular el valor del saldo, con base al % de proporcion.
				foreach($arrayDetalle as $concepto => $valoresCon)
				{
					$proporcion 						= ($valoresCon['valorFacConcepto']/$conceptosParaSaldos[$valoresCon['conceptoParaSaldo']]['valorFacturado']);
					$arrayDetalle[$concepto]['Saldo'] 	= $proporcion*$conceptosParaSaldos[$valoresCon['conceptoParaSaldo']]['valorSaldo'];
				}
				
				$respuesta['SQL']['ArrayConceptosParaSaldos'] = print_r($conceptosParaSaldos, true);
				// print_r($arrayDetalle);
				// print_r($devoluciones);
				
				// --> INICIO Restar devoluciones
				$arrayDetalleCopia = $arrayDetalle;
				
				$respuesta['SQL']['devoluciones1'] = json_encode($devoluciones);
				foreach($devoluciones as $concepDev => &$arrArtDev)
				{
					foreach($arrArtDev as $codArtDev => &$infoDev)
					{
						$cantDev = $infoDev['cantidad'];
						foreach($arrayDetalle as $codConcepto => $detalle)
						{						
							foreach($detalle['Detalle'] as $key => $info)
							{
								if($cantDev > 0)
								{
									if($codArtDev == $info['codigo'] && abs($infoDev['valorUnit']) == abs($info['valorUnit']))
									{
										if($cantDev >= $info['cantidad'])
										{
											$cantDev = $cantDev-$info['cantidad'];
											
											foreach($infoDev['detCco'] as $ccoDev => $infoDevCco)
											{											
												$cantDevCco = $infoDevCco['cantCco'];
												$cantTotCco = $arrayDetalleCopia[$codConcepto]['Detalle'][$key]['detCco'][$ccoDev]['cantCco'];
												$cantDevCco = $cantDevCco-$cantTotCco;
												$devoluciones[$concepDev][$codArtDev]['detCco'][$ccoDev]['cantCco'] 	= $cantDevCco;
												$devoluciones[$concepDev][$codArtDev]['detCco'][$ccoDev]['valFacCco'] 	= $cantDevCco*$info['valorUnit'];
											}
											
											$respuesta['VALORES'].= "-->".$key." COD:".$info['codigo']; 
											
											unset($arrayDetalleCopia[$codConcepto]['Detalle'][$key]);
										}
										else
										{
											$arrayDetalleCopia[$codConcepto]['Detalle'][$key]['cantidad'] 	= $info['cantidad']-$cantDev;
											$arrayDetalleCopia[$codConcepto]['Detalle'][$key]['valorFact'] 	= ($info['cantidad']-$cantDev)*$info['valorUnit'];
											
											// echo "1:";
											// print_r($infoDev['detCco']);
											// --> Restar la devolucion en el detalle por cco
											foreach($infoDev['detCco'] as $ccoDev => $infoDevCco)
											{
												// echo "2:";
												// print_r($arrayDetalleCopia[$codConcepto]['Detalle'][$key]['detCco'][$ccoDev]);
												
												$cantDevCco = $infoDevCco['cantCco'];
												$cantTotCco = $arrayDetalleCopia[$codConcepto]['Detalle'][$key]['detCco'][$ccoDev]['cantCco'];
												$cantTotCco = $cantTotCco-$cantDevCco;
												if($cantTotCco>0)
												{
													$arrayDetalleCopia[$codConcepto]['Detalle'][$key]['detCco'][$ccoDev]['cantCco'] 	= $cantTotCco;
													$arrayDetalleCopia[$codConcepto]['Detalle'][$key]['detCco'][$ccoDev]['valFacCco']	= $cantTotCco*$info['valorUnit'];
													
												}
												else
												{
													unset($arrayDetalleCopia[$codConcepto]['Detalle'][$key]['detCco'][$ccoDev]);
													$devoluciones[$concepDev][$codArtDev]['detCco'][$ccoDev]['cantCco'] 	= abs($cantTotCco);
													$devoluciones[$concepDev][$codArtDev]['detCco'][$ccoDev]['valFacCco'] 	= abs($cantTotCco)*$info['valorUnit'];
												}
											}
											
											$cantDev = 0;
										}
									}
								}
							}
						}
						$infoDev['cantidad'] 	= $cantDev;
						$infoDev['valorFact'] 	= $cantDev*$infoDev['valorUnit'];
					}
				}
				$arrayDetalle= $arrayDetalleCopia;
				
				// --> FIN Restar devoluciones
				
				// --> Si una devolución quedó con saldo, es decir aparece la devolucion pero no el cargo
				foreach($devoluciones as $concepDev => $arr2)
				{				
					foreach($arr2 as $codArt => $infoArt)
					{
						if($infoArt['cantidad'] > 0)
						{
							// --> Agrego el registro de devolucion al detalle, para mostrarlo.
							$indice = 0;
							foreach($arrayDetalle[$concepDev]['Detalle'] as $clavTempMay => $infoClav)
							{
								if($clavTempMay > $indice)
									$indice = $clavTempMay;
							}						
							$indice++;
							
							$arrayDetalle[$concepDev]['Detalle'][$indice]['valorFact']    = $infoArt['valorFact'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['valorUnit']    = $infoArt['valorUnit'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['codigo']       = $codArt;
							$arrayDetalle[$concepDev]['Detalle'][$indice]['cantidad']     = $infoArt['cantidad'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['nombreCod']    = $infoArt['nombreCod'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['cardetreg']    = $infoArt['cardetreg'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['entidadCargo'] = $infoArt['entidadCargo'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['terceroUnix']  = $infoArt['terceroUnix'];
							$arrayDetalle[$concepDev]['Detalle'][$indice]['nomTercero']   = $infoArt['nomTercero'];
							
							foreach($infoArt['detCco'] as $ccoDev => $infoDevCco)
							{							
								$arrayDetalle[$concepDev]['Detalle'][$indice]['detCco'][$ccoDev]['cantCco'] 	= $infoDevCco['cantCco'];
								$arrayDetalle[$concepDev]['Detalle'][$indice]['detCco'][$ccoDev]['valFacCco'] 	= $infoDevCco['valFacCco'];
								$arrayDetalle[$concepDev]['Detalle'][$indice]['detCco'][$ccoDev]['regCco'] 		= $infoDevCco['regCco'];
							}
						}	
					}
				}
				
				$respuesta['SQL']['arrayDetalle2'] = json_encode($arrayDetalle);
				$respuesta['SQL']['devoluciones2'] = json_encode($devoluciones);
			}
			else
			{
				$respuesta["Error"] 	= true;
				$respuesta["Mensaje"] 	= "No se encontro detalle de la factura";
				return $respuesta;
			}
		}
		//-------------------------------------------------------
		//	--> Factura de farpmla
		//-------------------------------------------------------
		else
		{
			$wbasedatoFar 	= consultarAliasPorAplicacion($conex, '09', 'farpmla');
			$fuenteFarpmla	= "";
			$numVenta		= "";
			$respuesta["detallarPorCco"] = 'off';
			
			// --> Consultar cual es el numero de fuente para las facturas
			$sqlFuente = "
			SELECT Carfue
			  FROM ".$wbasedatoFar."_000040
			 WHERE Carfac = 'on'
			   AND Carest = 'on'
			";
			$resFuente = mysql_query($sqlFuente, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFuente):</b><br>".mysql_error());
			if($rowFuente = mysql_fetch_array($resFuente))
				$fuenteFarpmla = $rowFuente['Carfue'];
			
			// --> Consultar cual es el cco del soat.
			$sqlCcoSoat = "
			SELECT Ccocod
			  FROM ".$wbasedatoFar."_000003
			 WHERE Ccotip = 'F'
			";
			$resCcoSoat = mysql_query($sqlCcoSoat, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoSoat):</b><br>".mysql_error());
			if($rowCcoSoat = mysql_fetch_array($resCcoSoat))
				$ccoSoat = $rowCcoSoat['Ccocod'];
			
			// --> Consultar el encabezado de la factura
			$sqlFacEnc = "
			SELECT Fenval, Fensal 
			  FROM ".$wbasedatoFar."_000018
			 WHERE Fenffa = '".$fuenteFarpmla."'
			   AND Fenfac = '".$factura."'
			   AND Fenest = 'on'
			   AND Fensal > 0
			"; 
			$resFacEnc = mysql_query($sqlFacEnc, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFacEnc):</b><br>".mysql_error());
			if($rowFacEnc = mysql_fetch_array($resFacEnc))
			{
				$valTotalFac = $rowFacEnc['Fenval'];
				$salTotalFac = $rowFacEnc['Fensal'];
			}
			else
			{
				$respuesta["Error"] 	= true;
				$respuesta["Mensaje"] 	= "No se encontro saldo pendiente para la factura";
				return $respuesta;
			}
						
			// --> Consultar la factura por concepto
			$sqlFacCon = "
			SELECT A.Fdecon, A.Fdevco, A.Fdecco, A.Fdeter, A.Fdesal, Grudes
			  FROM ".$wbasedatoFar."_000065 AS A INNER JOIN ".$wbasedatoFar."_000004 ON(A.Fdecon = Grucod)
			 WHERE A.Fdefue = '".$fuenteFarpmla."'
			   AND A.Fdedoc = '".$factura."'
			   AND A.Fdecco = '".$ccoSoat."'
			   AND A.Fdeest = 'on'
			"; 
			$resFacCon = mysql_query($sqlFacCon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFacCon):</b><br>".mysql_error());
			while($rowFacCon = mysql_fetch_array($resFacCon))
			{					
				$concepto = trim($rowFacCon['Fdecon']);
				$numVenta = $rowFacCon['Fdenve'];  
				$arrayDetalle[$concepto] = array();
				$arrayDetalle[$concepto]['Nombre']	 			= $rowFacCon['Grudes'];
				$arrayDetalle[$concepto]['valorFacConcepto']	= $rowFacCon['Fdevco'];
				$arrayDetalle[$concepto]['Detalle']				= array();
				// $arrayDetalle[$concepto]['Saldo'] = $rowFacCon['Fdesal'];
				
				// --> Calcular el saldo por concepto
				$proporcion = $rowFacCon['Fdevco']/$valTotalFac;
				$arrayDetalle[$concepto]['Saldo'] = $proporcion*$salTotalFac;
			}
			
			// --> Consultar encabezado de la factura
			$sqlEncFac = "
			SELECT Vennum, Vencod, Empnom
			  FROM ".$wbasedatoFar."_000016 LEFT JOIN ".$wbasedatoFar."_000024 ON(Vencod = Empcod)
			 WHERE Vennfa = '".$factura."'
			   AND Venest = 'on'
			   AND Vencco = '".$ccoSoat."'
			";
			$resEncFac = mysql_query($sqlEncFac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncFac):</b><br>".mysql_error());
			if($rowEncFac = mysql_fetch_array($resEncFac))
			{
				$respuesta["Entidad"]			= $rowEncFac['Vencod']."-".$rowEncFac['Empnom'];
				$respuesta["codEntidad"]		= $rowEncFac['Vencod'];
				$numVenta						= $rowEncFac['Vennum'];
				$respuesta["Fecha"]				= date("Y-m-d");
				$respuesta["Historia"]			= $hisFar;
				$historia						= $hisFar;
				$respuesta["Ingreso"]			= $ingFar;
				$ingreso						= $ingFar;
			}
			
			// --> Por cada concepto consultar el detalle.
			foreach($arrayDetalle as $concepto => $info)
			{
				// --> Consultar el detalle de la factura
				$sqlDet = "
				SELECT Vdeart, Vdevun, Vdecan, A.id, Artnom 
				  FROM ".$wbasedatoFar."_000017 AS A INNER JOIN ".$wbasedatoFar."_000001 ON(Vdeart = Artcod AND SUBSTRING_INDEX(Artgru, '-', 1) = '".$concepto."')
				 WHERE Vdenum = '".$numVenta."'	
				   AND Vdeest = 'on'				   
				";
				$respuesta["FARMACIA"]["sqlDet"][] = $sqlDet;
				$resDet = mysql_query($sqlDet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDet):</b><br>".mysql_error());
				while($rowDet = mysql_fetch_array($resDet))
				{				
					$codigo			= $rowDet['Vdeart'];
					$cantidad		= $rowDet['Vdecan'];
					$valorUnit		= $rowDet['Vdevun'];
					$cardetreg		= $rowDet['id'];
					$nombreCod		= $rowDet['Artnom'];
					$valorFact		= $cantidad*$valorUnit;
					$indiceExiste 	= "";
												
					foreach($arrayDetalle[$concepto]['Detalle'] as $indiceTemp => $nfoDet)
					{						
						if(trim($nfoDet['codigo']) == trim($codigo) && trim($nfoDet['valorUnit']) == trim($valorUnit))
							$indiceExiste = $indiceTemp;
					}
					
					if($indiceExiste !== '')
					{
						$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['valorFact']+= $valorFact;
						$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['cantidad']+= $cantidad;
						$arrayDetalle[$concepto]['Detalle'][$indiceExiste]['cardetreg'].= "-".$cardetreg;
					}
					else
					{					
						$indice = count($arrayDetalle[$concepto]['Detalle']);
						$arrayDetalle[$concepto]['Detalle'][$indice]['valorFact']    = $valorFact;
						$arrayDetalle[$concepto]['Detalle'][$indice]['valorUnit']    = $valorUnit;
						$arrayDetalle[$concepto]['Detalle'][$indice]['codigo']       = $codigo;
						$arrayDetalle[$concepto]['Detalle'][$indice]['cantidad']     = $cantidad;
						$arrayDetalle[$concepto]['Detalle'][$indice]['nombreCod']    = $nombreCod;
						$arrayDetalle[$concepto]['Detalle'][$indice]['cardetreg']    = $cardetreg;
						$arrayDetalle[$concepto]['Detalle'][$indice]['entidadCargo'] = $entidadCargo;
						$arrayDetalle[$concepto]['Detalle'][$indice]['terceroUnix']  = $terceroUnix;
						$arrayDetalle[$concepto]['Detalle'][$indice]['nomTercero']   = $nomTercero;
					}
				}
			}
			$respuesta["farpmla"] = print_r($arrayDetalle, true);
		}		
		
		// --> Traer responsables de glosas (Medicos Y  Cco)
		$movhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$arrayRespon 	= array();
		$sqlRespon 		= "
		SELECT Meddoc as Codigo, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) as Nombre
		  FROM ".$movhos."_000048
		 WHERE Medest = 'on'
		 GROUP BY Meddoc
		 UNION
		SELECT Ccocod as Codigo, Cconom as Nombre
		  FROM ".$movhos."_000011
		 WHERE Ccoest = 'on' 
		";
		$resRespon = mysql_query($sqlRespon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRespon):</b><br>".mysql_error());
		while($rowRespon = mysql_fetch_array($resRespon))
		{
			$arrayRespon[$rowRespon['Codigo']] = str_replace($caracter_ma, $caracter_ok, utf8_encode(trim($rowRespon['Nombre'])));
		}
	
		// --> Obtener permisos segun el rol
		$sqlPermisos = "
		SELECT B.*
		  FROM ".$wbasedato."_000030 AS A INNER JOIN ".$wbasedato."_000285 AS B ON(A.Cjerrg = B.Rolcod)
		 WHERE A.Cjeusu = '".$wuse."'
		   AND B.Rolest = 'on'
		";
		$respuesta['SQL']['sqlPermisos'] = $sqlPermisos;
		$resPermisos = mysql_query($sqlPermisos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGlo):</b><br>".mysql_error());
		$rowPermisos = mysql_fetch_array($resPermisos);
		
		// --> Consultar nombre del rol al que se le debe enviar la glosa
		if($rowPermisos['Rolpe3'] != '')
		{
			$sqlNomRol = "
			SELECT Roldes
			  FROM ".$wbasedato."_000285
			 WHERE Rolcod = '".$rowPermisos['Rolpe3']."'
			";
			$resNomRol = mysql_query($sqlNomRol, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomRol):</b><br>".mysql_error());
			if($rowNomRol = mysql_fetch_array($resNomRol))
				$rowPermisos['RolNomEnvi'] = $rowNomRol['Roldes'];
			else
				$rowPermisos['Rolpe3'] = '';
		}

		$respuesta["Html"] = "
		<table width='100%' id='tableDetalleGlosa' historia='".$historia."' ingreso='".$ingreso."' estadoGlosa='".$estadoGlosa."' movfuo='".$movfuo."' usuario='".$wuse."'>
			<tr align='center' class='encabezadoTabla'>
				<td>C&oacute;digo</td>
				<td>Tercero</td>
				<td>Cco</td>
				<td>Cant.</td>
				<td>Val. Uni</td>
				<td>Val. Fact</td>
				<td>Cant. Glosa</td>
				<td>Val. Glosa</td>
				<td>Causa</td>
				<td>Cant. Reclamar</td>
				<td>Val. Aceptado</td>
				<td>Responsable<br><span style='font-size:8pt;font-weight:normal'>(Cco-Medico)</span></td>				
				<td>Obser Auditoria</td>
				<td style='background-color:#F2F5F7;".(($respuesta["detallarPorCco"] == 'on') ? "" : "display:none" )."' align='center'><img src='../../images/medical/sgc/Printer.png' width='17px' height='17px' title='Seleccione los registros a imprimir'></td>
			</tr>";

		// --> Pintar detalle
		$totalValFact 			= 0;
		$totalValSaldo			= 0;
		$disTexarea				= '';
		
		// --> Habilitar campos para ingresar
		$CAN_GLO = "disabled"; $VAL_GLO = "disabled"; $CAU_GLO = "disabled"; $CAN_REC = "disabled"; $RES_GLO = "disabled"; $VAL_ACE = "disabled"; $OBJ_AUD = "none";
		switch($estadoGlosa)
		{
			// --> Apenas se va a ingresar la glosa
			case '':
			case 'AN':
			{
				$campos = explode(',', $rowPermisos['Rolca1']);
				foreach($campos as $nomCamp)
				{
					$nomCamp = trim($nomCamp);
					$$nomCamp = "";
				}	
				break;
			}
			case 'GL':
			case 'RA':
			{
				if(trim($rowPermisos['Rolca3']) != '')
					$campos = explode(',', $rowPermisos['Rolca3']);
				else
					$campos = explode(',', $rowPermisos['Rolca4']);
				
				foreach($campos as $nomCamp)
				{
					$nomCamp = trim($nomCamp);
					$$nomCamp = "";
				}
				
				break;
			}
			case 'GR':
			{
				$OBJ_AUD 	= '';
				$disTexarea = 'readonly';
				break;
			}
		}
		// echo "-->".$CAU_GLO;
		
		// --> Si la glosa está en manos de un rol distinto al del usuario actual
		if($rowPermisos['Rolcod'] != $rolGlosa && $idGlosa != '' && $estadoGlosa != "AN")
		{
			$CAN_GLO = "disabled"; $VAL_GLO = "disabled"; $CAU_GLO = "disabled"; $CAN_REC = "disabled"; $RES_GLO = "disabled"; $VAL_ACE = "disabled"; $OBJ_AUD = "none";
		}
		
		$arrayCausasAgrupadas 	= array();
		$infoObje		 		= array();
				
		foreach($arrayDetalle as $conceptoDet => $infoDet)
		{
			$respuesta["Html"].= "
			<tr class='fila1' style='font-weight:bold;background-color:#D7EBF9;color:#2779AA;'>
				<td class='bordeRed2' colspan='5'>
					&nbsp;<img src='../../images/medical/iconos/gifs/i.p.previous[1].gif' style='cursor:pointer' title='' onclick='desplegar(\"".$conceptoDet."\", this)'>
					&nbsp;".$conceptoDet."-".$infoDet['Nombre']."
				</td>
				<td class='bordeRed2' colspan='1' align='right'>&nbsp;".formato($infoDet['valorFacConcepto'])."</td>
				<td class='bordeRed2' colspan='7' align='center'>&nbsp;Saldo: <span style='color:red'>".((isset($infoDet['Saldo'])) ? formato($infoDet['Saldo']) : '!!!')."</span></td>
			</tr>
			";

			$totalValFact 	+= $infoDet['valorFacConcepto'];
			$totalValSaldo 	+= $infoDet['Saldo'];
			$colorFila 		 = '';			
			
			foreach($infoDet['Detalle'] as $indiceDeta => $valoresDet)
			{
				$colorFila	= (($colorFila == 'fila1') ? 'fila2' : 'fila1');
				
				if($respuesta["detallarPorCco"] == 'off')
				{
					if(array_key_exists($valoresDet['cardetreg'], $rowDetalleGlosaMatrix))
					{
						$cantGlosada 	= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdecgl'];
						$valorGlosado	= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdevgl'];
						$causa			= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdecau'];
						$respon			= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gderes'];
						$idReg			= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['idReg'];
						$valorAceptado	= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdevac'];
						$cantReclamar	= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdecre'];
						$objecion		= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdeobj'];
						$usuObjecion	= $rowDetalleGlosaMatrix[$valoresDet['cardetreg']]['Gdeuob'];
						
						$idc = "";
						foreach($arrayCausasAgrupadas as $indic => $valCausaAgru)
						{
							if($causa == $valCausaAgru['causa'])
								$idc = $indic;
						}
						
						if($idc === "")
							$idc = count($arrayCausasAgrupadas);
						
						if($causa != "")
						{
							$arrayCausasAgrupadas[$idc]['nombre'] 	= $arrayCausas[$causa];
							$arrayCausasAgrupadas[$idc]['causa'] 	= $causa;
						}
					}
					else
					{
						$cantGlosada 	= "";
						$valorGlosado	= "";
						$causa			= "";
						$idReg			= "";
						$valorAceptado	= "";
						$cantReclamar	= "";
						$objecion		= "";
						$usuObjecion	= "";
						$respon			= "";
					}
					
					// --> Redondear la cantidad
					$cantidad 	= ceil($valoresDet['cantidad']);
					$val_causa 	= (array_key_exists($causa, $arrayCausas)) ? $arrayCausas[$causa]: "";
					
					$respuesta["Html"].= "
					<tr detalleCCo='NO' detalle='".$conceptoDet."' codigo='".$valoresDet['codigo']."' menuCco='' cardetreg='".$valoresDet['cardetreg']."' idReg='".$idReg."' saldoConcepto='".$infoDet['Saldo']."' tercero='".(($valoresDet['terceroUnix'] != '' && $valoresDet['terceroUnix'] != '0') ? $valoresDet['terceroUnix'] : '')."'>
						<td class='".$colorFila."' >
							<img title='' onClick='verDetalleCco(this, \"".$conceptoDet.$indiceDeta."\")' style='cursor:pointer;display:none;' src='../../images/medical/iconos/gifs/i.p.next[1].gif'>
							".$valoresDet['codigo']."-".utf8_encode($valoresDet['nombreCod'])."
						</td>
						<td class='".$colorFila." tooltip' nowrap ".(($valoresDet['nomTercero'] != '') ? "title='".$valoresDet['terceroUnix']."'" : "")." style='font-size:6pt;'>".utf8_encode($valoresDet['nomTercero'])."</td>
						<td class='".$colorFila."' align='center' cco='*'><b>TODOS</b></td>
						<td class='".$colorFila."' align='center'>".$cantidad."</td>
						<td class='".$colorFila."' align='right'>".formato($valoresDet['valorUnit'])."</td>
						<td class='".$colorFila."' align='right' ".(($valoresDet['valorFact'] < 0) ? "style='background-color:#FCFBBD'" : "").">".formato($valoresDet['valorFact'])."</td>
						<td class='".$colorFila."' align='center'>
							<input type='text' ".$CAN_GLO." size='4' class='entero' placeholder='' cantFac='".$cantidad."' ondblclick='copiarValor(this)' value='".$cantGlosada."'>
						</td>
						<td class='".$colorFila."' align='center'>
							<input type='text' ".$VAL_GLO." placeholder='Digite o Doble click' class='miles' valorUnita='".$valoresDet['valorUnit']."' valFac='".formato($valoresDet['valorFact'])."' onblur='validarValorMaximo(this, \"".$conceptoDet."\");sumatoriaSaldo();' ondblclick='copiarValor(this)' value='".$valorGlosado."'>
						</td>
						<td class='".$colorFila."' align='center'>
							<input type='text' ".$CAU_GLO." ".(($valorGlosado == "" && $val_causa == "" && $estadoGlosa != '' && $estadoGlosa != 'AN') ? " disabled='disabled' " : "")." style='font-size:7pt;width:150px' placeholder='Seleccione...' value='".$val_causa."' cargarAutocomplete='' conceptoSele='".$conceptoDet."' selectCausa='' valor='".$causa."' nombre='".$val_causa."'>
						</td>
						<td class='".$colorFila."' align='center'>
							<input type='text' ".$CAN_REC." ".(($valorGlosado == "" && $val_causa == "") ? " disabled='disabled' " : "")." size='4' class='entero' placeholder='' cantReclamar='".$cantGlosada."' ondblclick='copiarValor(this)' value='".$cantReclamar."'>
						</td>
						<td class='".$colorFila."' align='center'>
							<input type='text' ".$VAL_ACE." ".(($valorGlosado == "" && $val_causa == "") ? " disabled='disabled' " : "")." placeholder='Digite o Doble click' class='miles' valAceptado='' valGlosado='".$valorGlosado."' value='".$valorAceptado."' onblur='validarValorAceptado(this);sumatoriaAceptado();' ondblclick='copiarValor(this)'>
						</td>
						<td class='".$colorFila."' align='center'>
							<input type='text' ".$RES_GLO." ".(($valorGlosado == "" && $val_causa == "" && $estadoGlosa != '' && $estadoGlosa != 'AN') ? " disabled='disabled' " : "")." style='font-size:7pt;width:100px' placeholder='Seleccione...' value='".(($respon != "") ? $respon."-".$arrayRespon[$respon] : "")."' cargarAutocomplete2='' valor='".$respon."' nombre='".$arrayRespon[$respon]."' selectRespo=''>
						</td>
						<td class='".$colorFila."' align='center'>
							<img imgEditar='' style='cursor:pointer;display:".$OBJ_AUD."".(($OBJ_AUD == "" && $valorGlosado == "" && $val_causa == "") ? "none' " : "").";'  class='tooltip' title='<div style=\"background-color:#FFFFFF;color:#000000;font-weight:normal\">".$objecion."</div>' onClick='editarObjecion(this)' src='../../images/medical/hce/mod.PNG'>
							<div style='display:none;z-index:10000;font-size:8pt;border:solid 1px #000000;border-radius: 4px;padding:1px 5px 5px 5px;background-color:#feffce' align='center'>
								<b>Edici&oacute;n de observaci&oacute;n</b><br>
								<textarea ".$disTexarea." objAud='' usuedita='".$usuObjecion."' style='width: 231px; height: 70px;'>".$objecion."</textarea>
							</div>
						</td>";

					$respuesta["Html"].= "
					</tr>
					";
				}
				else
				{				
					$rowspan 	= count($valoresDet['detCco']);
					$primeraVez = true;
					// --> Detalle por cco
					if(count($valoresDet['detCco']) > 0 )
					{
						foreach($valoresDet['detCco'] as $cco => $infoDetCco)
						{
							$cantidadCco = ceil($infoDetCco['cantCco']);
							
							if(array_key_exists($infoDetCco['regCco'], $rowDetalleGlosaMatrix))
							{
								$cantGlosada 	= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdecgl'];
								$valorGlosado	= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdevgl'];
								$causa			= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdecau'];
								$respon			= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gderes'];
								$idReg			= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['idReg'];
								$valorAceptado	= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdevac'];
								$cantReclamar	= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdecre'];
								$objecion		= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdeobj'];
								$usuObjecion	= $rowDetalleGlosaMatrix[$infoDetCco['regCco']]['Gdeuob'];
								
								$idc = "";
								foreach($arrayCausasAgrupadas as $indic => $valCausaAgru)
								{
									if($causa == $valCausaAgru['causa'])
										$idc = $indic;
								}
								
								if($idc === "")
									$idc = count($arrayCausasAgrupadas);
								
								if($causa != "")
								{
									$arrayCausasAgrupadas[$idc]['nombre'] 	= $arrayCausas[$causa];
									$arrayCausasAgrupadas[$idc]['causa'] 	= $causa;
								}
							}
							else
							{
								$cantGlosada 	= "";
								$valorGlosado	= "";
								$causa			= "";
								$idReg			= "";
								$valorAceptado	= "";
								$cantReclamar	= "";
								$objecion		= "";
								$usuObjecion	= "";
								$respon			= "";
							}
							
							$val_causa 	= (array_key_exists($causa, $arrayCausas)) ? $arrayCausas[$causa]: "";
						
							$respuesta["Html"].= "
							<tr detalleCCo='SI' detalle='".$conceptoDet."' codigo='".$valoresDet['codigo']."'  class='".$conceptoDet.$indiceDeta."' cardetreg='".$infoDetCco['regCco']."' idReg='".$idReg."' saldoConcepto='".$infoDet['Saldo']."' tercero='".(($valoresDet['terceroUnix'] != '' && $valoresDet['terceroUnix'] != '0') ? $valoresDet['terceroUnix'] : '')."'>";
							if($primeraVez)
							{
								$respuesta["Html"].= "
								<td class='".$colorFila."' rowspan='".$rowspan."'>
									".$valoresDet['codigo']."-".utf8_encode($valoresDet['nombreCod'])."
								</td>
								<td class='".$colorFila." tooltip' rowspan='".$rowspan."' nowrap ".(($valoresDet['nomTercero'] != '') ? "title='".$valoresDet['terceroUnix']."'" : "")." style='font-size:6pt;'>".utf8_encode($valoresDet['nomTercero'])."</td>";
							}
								$respuesta["Html"].= "
								<td class='".$colorFila."' align='center' cco='".$cco."'>".$cco."</td>
								<td class='".$colorFila."' align='center'>".$cantidadCco."</td>
								<td class='".$colorFila."' align='right'>".formato($valoresDet['valorUnit'])."</td>
								<td class='".$colorFila."' align='right' ".(($infoDetCco['valFacCco'] < 0) ? "style='background-color:#FCFBBD'" : "").">".formato($infoDetCco['valFacCco'])."</td>
								<td class='".$colorFila."' align='center'>
									<input type='text' ".$CAN_GLO." size='4' class='entero' placeholder='' cantFac='".$cantidadCco."' ondblclick='copiarValor(this)' value='".$cantGlosada."'>
								</td>
								<td class='".$colorFila."' align='center'>
									<input type='text' ".$VAL_GLO." placeholder='Digite o Doble click' class='miles' valorUnita='".$valoresDet['valorUnit']."' valFac='".formato($infoDetCco['valFacCco'])."' onblur='validarValorMaximo(this, \"".$conceptoDet."\");sumatoriaSaldo();' ondblclick='copiarValor(this)' value='".$valorGlosado."'>
								</td>
								<td class='".$colorFila."' align='center'>
									<input type='text' ".$CAU_GLO." ".(($valorGlosado == "" && $val_causa == "" && $estadoGlosa != '' && $estadoGlosa != 'AN') ? " disabled='disabled' " : "")." style='font-size:7pt;width:150px' placeholder='Seleccione...' value='".$val_causa."' cargarAutocomplete='' conceptoSele='".$conceptoDet."' selectCausa='' valor='".$causa."' nombre='".$val_causa."'>
								</td>
								<td class='".$colorFila."' align='center'>
									<input type='text' ".$CAN_REC." ".(($valorGlosado == "" && $val_causa == "") ? " disabled='disabled' " : "")." size='4' class='entero' placeholder='' cantReclamar='".$cantGlosada."' ondblclick='copiarValor(this)' value='".$cantReclamar."'>
								</td>
								<td class='".$colorFila."' align='center'>
									<input type='text' ".$VAL_ACE." ".(($valorGlosado == "" && $val_causa == "") ? " disabled='disabled' " : "")." placeholder='Digite o Doble click' class='miles' valAceptado='' valGlosado='".$valorGlosado."' value='".$valorAceptado."' onblur='validarValorAceptado(this);sumatoriaAceptado();' ondblclick='copiarValor(this)'>
								</td>
								<td class='".$colorFila."' align='center'>
									<input type='text' ".$RES_GLO." ".(($valorGlosado == "" && $val_causa == "" && $estadoGlosa != '' && $estadoGlosa != 'AN') ? " disabled='disabled' " : "")." style='font-size:7pt;width:100px' placeholder='Seleccione...' value='".(($respon != "") ? $respon."-".$arrayRespon[$respon] : "")."' cargarAutocomplete2='' valor='".$respon."' nombre='".$arrayRespon[$respon]."' selectRespo=''>
								</td>
								<td class='".$colorFila."' align='center'>
									<img ".$OBJ_AUD." imgEditar='' class='tooltip' style='cursor:pointer;display:".$OBJ_AUD."".(($OBJ_AUD == "" && $valorGlosado == "" && $val_causa == "") ? "none' " : "").";' title='<div style=\"background-color:#FFFFFF;color:#000000;font-weight:normal\">".$objecion."</div>' onClick='editarObjecion(this)' src='../../images/medical/hce/mod.PNG'>
									<div style='display:none;z-index:10000;font-size:8pt;border:solid 1px #000000;border-radius: 4px;padding:1px 5px 5px 5px;background-color:#feffce' align='center'>
										<b>Edici&oacute;n de objeci&oacute;n</b><br>
										<textarea ".$disTexarea." objAud='' usuedita='".$usuObjecion."' style='width: 231px; height: 70px;'>".$objecion."</textarea>
									</div>
								</td>
								<td style=''>".(($idReg != "") ? "<input type='checkbox' checkParaImp='' style='cursor:pointer'>" : "")."</td>";

							$respuesta["Html"].= "
							</tr>
							";
							
							$primeraVez = false;

							if($objecion != "")
							{
								$infoObje[$idReg]['Concepto'] 			= $conceptoDet."-".$infoDet['Nombre'];
								$infoObje[$idReg]['Codigo'] 			= $valoresDet['codigo']."-".utf8_encode($valoresDet['nombreCod']);
								$infoObje[$idReg]['Cco'] 				= $cco;
								$infoObje[$idReg]['Tercero'] 			= utf8_encode($valoresDet['nomTercero']);
								$infoObje[$idReg]['valorAceptado'] 		= $valorAceptado;
								$infoObje[$idReg]['objecion'] 			= $objecion;	
								$infoObje[$idReg]['usuObjecion']		= $usuObjecion;	
							}
						}
					}
				}
			}
		}
		//print_r($infoObje);

		$respuesta["Html"].= "
			<tr><td></td></tr>
			<tr style='color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>
				<td colspan='5' align='right'><b>Total:</b></td>
				<td align='right' style='border-top:2px solid #000000;'>&nbsp;".formato($totalValFact)."</td>
				<td></td>
				<td align='right' id='sumatoriaSaldo' style='border-top:2px solid #000000;' totalValSaldo='".$totalValSaldo."'>0</td>
				<td colspan='2'></td>";
		if(($idGlosa != "" && $estadoGlosa != "AN"))
			$respuesta["Html"].= " <td align='right' id='sumatoriaAceptado' style='border-top:2px solid #000000;'>0</td>";

		if(($idGlosa != "" && $estadoGlosa != "AN"))
			$respuesta["Html"].= "
				<td colspan='3' align='right' style='".(($respuesta["detallarPorCco"] == 'on') ? "" : "display:none" )."'><span style='font-size:8px'>Imprimir todos:</span><input type='checkbox' style='cursor:pointer' onChange='checkTodos(this)'></td>
			</tr>
			<tr style='color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>
				<td colspan='5' align='right'><b>Val.Max:</b></td>
				<td colspan='2'></td>
				<td align='right' style='color:#2A5DB0'><b>".formato($totalValSaldo)."</b></td>
				<td></td>
			</tr>
		";

		// --> Habilitar botones
		$boton1 		= "disabled";
		$boton2 		= "disabled";
		$boton3 		= "disabled";
		$boton4 		= "disabled";
		$boton5 		= "disabled";
		$boton6 		= "disabled";
		$boton7 		= "disabled";
		$mensajeError 	= "";

		// --> Apenas se va a registrar la glosa
		if($idGlosa == '' || $estadoGlosa == "AN")
		{
			if(($totalValSaldo*1) > ($totalValFact*1))
			{
				$mensajeError = "
				<img width='15' heigth='15' src='../../images/medical/sgc/Warning-32.png'>&nbsp;
				Esta glosa no se puede revisar, ya que existe una inconsistencia en el valor de los saldos en UNIX.<br>
				Por favor reportar esta incosistencia con soporte sistemas.
				";
				
				$respuesta["MensajeDetalle"] = $mensajeError;
			}
			else
			{
				$boton1 = "";
				$boton2 = "";
			}
		}
		else
		{
			// --> Ya está registrada la glosa
			if($estadoGlosa == "GL" || $estadoGlosa == "RA")
			{
				$boton3 = "";
				$boton4 = "";
				$boton5 = "";
			}
			elseif($estadoGlosa == "GR" || $estadoGlosa == "AP")
				{
					$boton6 = "";
					$boton7 = "";
				}
		}
		
		if(count($rowPermisos) > 0)
		{
			// print_r($rowPermisos);
			// --> Permiso para grabar glosa
			$boton2 = ($rowPermisos['Rolpe1'] != 'on') ? "disabled" : $boton2;
			// --> Permiso para anular glosa
			$boton3 = ($rowPermisos['Rolpe2'] != 'on') ? "disabled" : $boton3;
			// --> Permiso para enviar glosa a otro ROL
			$boton4 = (trim($rowPermisos['Rolpe3']) == '') ? "disabled" : $boton4;
			// --> Permiso para dar respuesta
			$boton5 = ($rowPermisos['Rolpe4'] != 'on') ? "disabled" : $boton5;
			// --> Permiso para anular respuesta
			$boton6 = ($rowPermisos['Rolpe5'] != 'on') ? "disabled" : $boton6;
			// --> Permiso para imprimir respuesta
			$boton7 = ($rowPermisos['Rolpe5'] != 'on') ? "disabled" : $boton7;
		}
		else
		{
			$boton1 		= "disabled";
			$boton2 		= "disabled";
			$boton3 		= "disabled";
			$boton4 		= "disabled";
			$boton5 		= "disabled";
			$boton6 		= "disabled";
			$boton7 		= "disabled";
		}
		
		if($rowPermisos['Rolcod'] != $rolGlosa && $idGlosa != '' && $estadoGlosa != "AN")
		{
			$boton1 		= "disabled";
			$boton2 		= "disabled";
			$boton3 		= "disabled";
			$boton4 		= "disabled";
			$boton5 		= "disabled";
			$boton6 		= "disabled";
			$boton7 		= "disabled";
		}
		

		$respuesta["Html"].= "
		</table>
		<br>
		<table width='100%'>
			<tr>
				<td align='center'>
					<button ".$boton1." style='font-size:9pt' onclick='iniciarFormulario()'>Iniciar</button>
					&nbsp;|&nbsp;
					<button ".$boton2." style='font-size:9pt' onclick='grabarGlosa(\"".$idGlosa."\", \"GL\", \"".$rowPermisos['Rolcod']."\", \"on\")'>Grabar</button>
					&nbsp;|&nbsp;
					<button ".$boton3." style='font-size:9pt' onclick='anularGlosa(\"AN\", \"".$idGlosa."\", \"devolver la grabacion\")'>Devolver Grabacion</button>
					&nbsp;|&nbsp;";
				if($rowPermisos['Rolpe3'] != '')
				{					
					$respuesta["Html"].= "
					<button ".$boton4." style='font-size:9pt' onclick='grabarGlosa(\"".$idGlosa."\", \"".(($rowPermisos['Rolcod'] == "AUD") ? "RA" : "GL")."\", \"".$rowPermisos['Rolpe3']."\", \"off\")'>Enviar a ".$rowPermisos['RolNomEnvi']."</button>
					&nbsp;|&nbsp;";
				}
				
					$respuesta["Html"].= "			
					<button ".$boton5." style='font-size:9pt' onclick='grabarGlosa(\"".$idGlosa."\", \"".$estadoGlosa."\", \"".$rowPermisos['Rolcod']."\", \"off\")' title='Guardar respuesta temporal'>
						<img src='../../images/medical/root/grabar16.png' width='13px' height='13px'>
							Temporal
					</button>
					&nbsp;|&nbsp;
					<button ".$boton5." style='font-size:9pt' onclick='grabarGlosa(\"".$idGlosa."\", \"GR\", \"".$rowPermisos['Rolcod']."\", \"on\")'>Respuesta</button>
					&nbsp;|&nbsp;
					<button ".$boton6." style='font-size:9pt' onclick='anularGlosa(\"GL\", \"".$idGlosa."\", \"devolver la respuesta\")'>Devolver Respuesta</button>
					&nbsp;|&nbsp;
					<button ".$boton7." style='font-size:9pt' onclick='imprimirCarta(\"".$idGlosa."\")'><img src='../../images/medical/sgc/Printer.png' width='17px'  height='17px' title='Imprimir Respuesta'> Respuesta</button>
					&nbsp;|&nbsp;";
				if(count($infoObje) > 0)
					$respuesta["Html"].= "
					<button ".$boton7." style='font-size:9pt' onclick='imprimirObsAud(\"".$idGlosa."\", this)' infoObje='".json_encode($infoObje)."'><img src='../../images/medical/sgc/Printer.png' width='17px' height='17px' title='Imprimir Observacion Auditoria'> Obs Aud</button>";
					
		$respuesta["Html"].= "
			</td>
			</tr>
		</table>";
		
		$respuesta["HtmlJustificarCausas"] = "";
		if($idGlosa != '')
		{
			// --> Consultar justificaciones
			$sqlJusti = "
			SELECT Juscau, Jusdes
			  FROM ".$wbasedato."_000275 
			 WHERE Jusglo = '".$idGlosa."' ";	
			$resJusti = mysql_query($sqlJusti, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlJusti):</b><br>".mysql_error());
			while($rowJusti = mysql_fetch_array($resJusti))
				$justific[$rowJusti['Juscau']] = utf8_encode($rowJusti['Jusdes']);
					
			// --> Html para justificar las causas
			$respuesta["HtmlJustificarCausas"] = "
			<table width='100%' id='tableJustificacion'>";
			
			for($x=0; $x<count($arrayCausasAgrupadas); $x=$x+3)
			{
				$respuesta["HtmlJustificarCausas"].= "
				<tr>";
					
				for($y=$x; $y<($x+3) && $y<count($arrayCausasAgrupadas); $y++)
				{
					$codCausa 	= $arrayCausasAgrupadas[$y]['causa'];
					$nomCausa	= $arrayCausasAgrupadas[$y]['nombre'];
					
					$respuesta["HtmlJustificarCausas"].= "
					<td style='color:#000000;font-size:8pt;padding:1px;font-family:verdana;' align='center'>
						<b>".$codCausa."-".$nomCausa."</b><br>
						<textarea codCausa='".$codCausa."' placeholder='Digitar texto' style='width: 380px; height: 60px;'>".$justific[$codCausa]."</textarea>
					</td>";
				}
				
				// $respuesta["HtmlJustificarCausas"].= "
				// </tr>
				// <tr class='fila2'>";
				
				// for($j=$x; $j<($x+3) && $j<count($arrayCausasAgrupadas); $j++)
				// {				
					// $respuesta["HtmlJustificarCausas"].= "
					// <td align='center'><textarea placeholder='Digitar texto' style='width: 380px; height: 60px;'></textarea></td>";
				// }
				
				$respuesta["HtmlJustificarCausas"].= "
				</tr>";
			}
			
			$respuesta["HtmlJustificarCausas"].= "
			</table>";
		}
		
		$respuesta["Mensaje"] = utf8_encode($respuesta["Mensaje"]);

		return $respuesta;
	}
	//---------------------------------------------------------
	// -->	Pintar formulario de una glosa
	//---------------------------------------------------------
	function encabezadoGlosa($idGlosa='')
	{
		global $wbasedato;
		global $conex;
		global $wuse;
		
		$respuesta 			= array("Html" => "");
		$arr_estadoCartera 	= estadosCarteraFactura($conex, $wbasedato);
		$perGloFar			= "off";
		
		// --> Consultar si el usuario tiene permiso para ingresar glosas de farmacia
		$sqlPerFar = "
		SELECT Rolfar
		  FROM ".$wbasedato."_000030 AS A INNER JOIN ".$wbasedato."_000285 AS B ON(A.Cjerrg = B.Rolcod)
		 WHERE A.Cjeusu = '".$wuse."'
		   AND B.Rolest = 'on'
		";
		$resPerFar = mysql_query($sqlPerFar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPerFar):</b><br>".mysql_error());
		if($rowPerFar = mysql_fetch_array($resPerFar))
			$perGloFar = $rowPerFar['Rolfar'];

		// --> Consultar encabezado de la glosa
		$rowGlo = array();
		if($idGlosa != '')
		{
			$sqlGlo = "
			SELECT Glonfa, Gloent, Glotot, Glonrg, Glorad, Glofhg, Gloesg, Gloecf, Gloobs, Glohis, Gloing, Glofar, Empnom, Pactdo, Pacdoc
			  FROM  ".$wbasedato."_000273 AS A LEFT JOIN ".$wbasedato."_000024 AS B ON(A.Gloent = B.Empcod)
					LEFT JOIN ".$wbasedato."_000100 ON(Glohis = Pachis)
			 WHERE A.id = '".$idGlosa."'
			";
			$resGlo = mysql_query($sqlGlo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGlo):</b><br>".mysql_error());
			$rowGlo = mysql_fetch_array($resGlo);
			
			// --> Si es una glosa de farmacia, consulto el nombre de le empresa en farpmla
			if($rowGlo['Glofar'] == 'on')
			{
				$wbasedatoFar 	= consultarAliasPorAplicacion($conex, '09', 'farpmla');
				$sqlEmp = "
				SELECT Empnom
				  FROM ".$wbasedatoFar."_000024
				 WHERE Empcod = '".$rowGlo['Gloent']."'
				";
				$resEmp = mysql_query($sqlEmp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEmp):</b><br>".mysql_error());
				if($rowEmp = mysql_fetch_array($resEmp))
					$rowGlo['Empnom'] = $rowEmp['Empnom'];
			}	
		}

		$Gloecf    = "";
		$GloecfNom = "";
		if(count($rowGlo)>0)
		{
			$Gloecf    = $rowGlo['Gloecf'];
			$GloecfNom = (array_key_exists($rowGlo['Gloecf'], $arr_estadoCartera["ReqRad"])) ? $rowGlo['Gloecf'].'-'.$arr_estadoCartera["ReqRad"][$rowGlo['Gloecf']]["nombre"] : $rowGlo['Gloecf'];
		}

		$respuesta["Html"] = "
		<table width='100%'>
			<tr>
				<td align='right' style='font-size:9pt;font-weight: normal;font-family:verdana;'>
					<span onmouseover='$(this).css({\"color\": \"#2A5DB0\"})' onmouseout='$(this).css({\"color\": \"#4c4c4c\"})' style='cursor:pointer;color:#4c4c4c' onClick='abrirHce(\"".$rowGlo['Pacdoc']."\", \"".$rowGlo['Pactdo']."\", \"".$rowGlo['Glohis']."\", \"".$rowGlo['Gloing']."\")'>
						<img title='Consultar HCE' src='../../images/medical/sgc/verHce.png' width='17px' height='17px'>
						Ver Historia Cl&iacute;nica
					</span>
					<b>|</b>
					<span onmouseover='$(this).css({\"color\": \"#2A5DB0\"})' onmouseout='$(this).css({\"color\": \"#4c4c4c\"})' style='cursor:pointer;color:#4c4c4c' onclick='abrirOrdenes();'>
						Ver Ordenes M&eacute;dicas
					</span>
					<b>|</b>
					<span onmouseover='$(this).css({\"color\": \"#2A5DB0\"})' onmouseout='$(this).css({\"color\": \"#4c4c4c\"})' style='cursor:pointer;color:#4c4c4c' onclick='abrirHoja();'>
						Hoja de medicamentos
					</span>
				</td>
			</tr>
		</table>
		<table width='100%' estadoFacturaUnix='' tieneNotaCredito='' >
			<tr>
				<td class='fila1'>Nro factura:</td>
				<td class='fila2'><input type='text' style='width:120px' id='inpFactura' placeholder='Debe ingresar dato' ".((count($rowGlo) > 0) ? "value='".$rowGlo['Glonfa']."' disabled='disabled'" : "")."'></td>
				<td class='fila1'>Entidad:</td>
				<td class='fila2' id='inpEntidad' codEntidad='".((count($rowGlo) > 0) ? $rowGlo['Gloent'] : '')."'>".((count($rowGlo) > 0) ? $rowGlo['Gloent']." ".$rowGlo['Empnom'] : '')."</td>
				<td class='fila1'>Glosa total:</td>
				<td class='fila2'><input type='checkbox' ".((count($rowGlo) > 0 && $rowGlo['Glotot'] == 'on') ? 'checked=checked' : '')." style='cursor:pointer;' ".((count($rowGlo) > 0 && $rowGlo['Gloesg'] == "AN") ? "" : "disabled='disabled'")." id='inpGlosaTotal' onChange='glosaTotal()'></td>
				<td class='fila1'>Nro radicado:</td>
				<td class='fila2'><input type='text' id='inpRadicado' ".((count($rowGlo) > 0 && $rowGlo['Gloesg'] != "GR" && $rowGlo['Gloesg'] != "AP") ? "" : "disabled='disabled'")." value='".((count($rowGlo) > 0) ? $rowGlo['Glorad'] : '')."' placeholder='Debe ingresar dato' requerido='off' ></td>
				<td class='fila1'>Nro relaci&oacute;n glosa:</td>
				<td class='fila2'><input type='text' style='width:60px' id='inpNroRelacionGlosa' disabled='disabled' value='".((count($rowGlo) > 0) ? $idGlosa : '')."'></td>
				<td class='fila1'>Historia:</td>
				<td class='fila2'>".((count($rowGlo) > 0) ? $rowGlo['Glohis'].'-'.$rowGlo['Gloing'].'' : '')."</td>
			</tr>
			<tr>
				<td class='fila1'>Estado cartera:</td>
				<td class='fila2'>
					<input style='width:120px;' disabled='disabled' id='estado_factura' ".((count($rowGlo) > 0) ? "value='".$GloecfNom."' disabled='disabled'" : "")." valor='".((count($rowGlo) > 0) ? $Gloecf : "")."' nombre='".$GloecfNom."' placeholder='Estado cartera factura' >
				</td>
				<td class='fila1'>Observaci&oacute;n:</td>
				<td class='fila2' align='center' colspan='5'>
					<textarea id='textObservacion' placeholder='Digitar observacion' style='width: 400px; height: 50px;'>".((count($rowGlo) > 0) ? utf8_encode($rowGlo['Gloobs']) : '')."</textarea>
				</td>
				<td class='fila1'>Con nota cr&eacutedito:</td>
				<td class='fila2' align='center' id='conNotaCredito' style='cursor:help'></td>
				<td class='fila1'>Fecha de registro:</td>
				<td class='fila2' id='fechaRegistro'>".((count($rowGlo) > 0) ? $rowGlo['Glofhg'] : '')."</td>
			</tr>
			<tr ".(($idGlosa != '') ? "style='display:none'" : "").">
				<td colspan='4' align='center'>
				</td>
				<td colspan='4' align='center'>
					<br>
					<span style='".(($perGloFar == 'on') ? '' : 'display:none;')."color:#3d3d3d;font-size:8pt;padding:1px;font-family:verdana;'><input type='checkbox' id='facturaDeFarmacia' ".((isset($rowGlo['Glofar']) && $rowGlo['Glofar'] == 'on') ? "checked=checked" : "").">Checkear s&iacute; es una factura de farmacia cl&iacute;nica.</span>
					<br>
					<button style='font-size:9pt' onclick='validarFactura()'>Cargar Detalle</button>
				</td>
				<td colspan='4' align='center' id='mensajeDetalle' style='color:#000000;font-size:8pt;padding:1px;font-family:verdana;'>
				</td>
			</tr>
		</table>
		";

		return $respuesta;
	}

	function estadosCarteraFactura($conex, $wbasedato)
	{
		$arrayEstadosFacturaAutocomp = array();
		$arrayEstadosFacturaReqRad = array(); // para validar si un estado de factura requiere o no número de radicado
		$sqlEstFac = "	SELECT 	Esccod, Escnom, Esccau, Escrra AS req_radicado
						FROM 	{$wbasedato}_000279
						WHERE 	Escest = 'on'";
		$resEstFac = mysql_query($sqlEstFac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstFac):</b><br>".mysql_error());
		while($rowEst = mysql_fetch_array($resEstFac))
		{
			$arrayEstadosFacturaAutocomp[$rowEst['Esccod']] = utf8_encode(trim($rowEst['Escnom']));
			$arrayEstadosFacturaReqRad[$rowEst['Esccod']] = array("codigo"=>$rowEst['Esccod'], "causa_devolucion"=>$rowEst['Esccau'], "req_radicado"=>$rowEst['req_radicado'], "nombre"=>utf8_encode(trim($rowEst['Escnom'])));
		}
		return array("Autocomp"=>$arrayEstadosFacturaAutocomp, "ReqRad"=>$arrayEstadosFacturaReqRad);
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
		case 'obtenerDetalleUnix':
		{
			$respuesta = detalleGlosa('', $factura, $facturaDeFarmacia, $hisFar, $ingFar, $rolUsuario);
			
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'validarFacturaRegistrada':
		{
			$conexUnix = odbc_connect('facturacion','informix','sco');
			$respuesta = array("Confirm" => FALSE, "pedirIngreso" => "off");
			$respuesta['htmlIngreso'] = "";
			
			if($facturaDeFarmacia == "on")
			{
				$wbasedatoFar 				= consultarAliasPorAplicacion($conex, '09', 'farpmla');
				$fuenteFarpmla				= "";
				$respuesta['htmlIngreso'] 	= "
				<br>
				<select id='selectIngreso' class='bordeRed'>";
				
				$sqlFuente = "
				SELECT Carfue
				  FROM ".$wbasedatoFar."_000040
				 WHERE Carfac = 'on'
				   AND Carest = 'on'
				";
				$resFuente = mysql_query($sqlFuente, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFuente):</b><br>".mysql_error());
				if($rowFuente = mysql_fetch_array($resFuente))
					$fuenteFarpmla = $rowFuente['Carfue'];
			
				// --> Obtener el numero de historia
				$sqlIgresos = "
				SELECT Pachis
				  FROM ".$wbasedatoFar."_000019 INNER JOIN ".$wbasedatoFar."_000016 ON(Fdenve = Vennum )
					   INNER JOIN ".$wbasedato."_000100 ON(Vennit = Pacdoc)
				 WHERE Fdeffa = '".$fuenteFarpmla."'
				   AND Fdefac = '".trim($factura)."'
				";
				$resIgresos = mysql_query($sqlIgresos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlIgresos):</b><br>".mysql_error());
				if($rowIgresos = mysql_fetch_array($resIgresos))
				{
					$respuesta['pedirIngreso'] 	= "on"; 			
					$respuesta['historiaFar'] 	= $rowIgresos['Pachis'];
					
					// --> Consultar los ingresos relacionados desde unix
					$sqlIngUnix = "
					SELECT egrnum, egring
					  FROM inmegr
					 WHERE egrhis = '".$respuesta['historiaFar']."'
					";
					$resIngUnix = odbc_exec($conexUnix, $sqlIngUnix);
					while(odbc_fetch_row($resIngUnix))
					{
						$respuesta['htmlIngreso'].= "<option value='".trim(odbc_result($resIngUnix,'egrnum'))."'><b>Ingreso: ".trim(odbc_result($resIngUnix,'egrnum'))."</b> (Fecha ingreso: ".trim(odbc_result($resIngUnix,'egring')).")</option>";
					}
				}
				
				$respuesta['htmlIngreso'].= "
				</select>";
				
				echo json_encode($respuesta);
				return;
			}
			
			// --> Validar si la factura ya está registrada
			$sqlVal = "
			SELECT Gloecf, id
			  FROM ".$wbasedato."_000273
			 WHERE Glonfa = '".trim($factura)."'
			   AND Gloest = 'on'
			";
			$resVal = mysql_query($sqlVal, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVal):".$sqlVal."</b><br>".mysql_error());
			while($rowVal = mysql_fetch_array($resVal))
			{
				// --> Consultar el estado de la factura en UNIX
				$sqlEstFac = "
				SELECT encest, estnom
				  FROM caenc, caest
				 WHERE encfue = '20'
				   AND encdoc = '".$factura."'
				   AND encest = estcod
				";
				$resEstFac = odbc_exec($conexUnix, $sqlEstFac);
				if(odbc_fetch_row($resEstFac))
				{
					$estadoEnUnix 	= trim(odbc_result($resEstFac,'encest'));
					$nombreEstado	= trim(odbc_result($resEstFac,'estnom'));
			
					// --> Si el estado con que se registró es el mismo con el que está en unix
					if($rowVal['Gloecf'] == $estadoEnUnix)
					{
						$respuesta['Confirm'] 			= true;
						$respuesta['idGlosa'] 			= $rowVal['id'];
						$respuesta['MensajeConfirm'] 	= "La factura (".$factura.") ya se encuentra registrada con el mismo estado (".$estadoEnUnix.").\n\nPara cargar la existente, presione ACEPTAR.\nPara hacer un nuevo registro presione CANCELAR.";
					}	
				}
			}
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'grabarGlosa':
		{
			$respuesta 	= array('Error' => FALSE, 'Mensaje' => '', 'Html' => '');
			$idGlosa	= trim($idGlosa);

			// --> Nuevo registro
			if($idGlosa == '')
			{
				// --> Guardar encabezado
				$sqlEncabezado = "
				INSERT INTO ".$wbasedato."_000273
						SET Medico		= '".$wbasedato."',
							Fecha_data 	= '".date("Y-m-d")."',
							Hora_data 	= '".date("H:i:s")."',
							Glonfa		= '".$factura."',
							Gloent 		= '".$entidad."',
							Glotot 		= '".$glosaTotal."',
							Glonrg 		= '".$numRelGlosa."',
							Glorad		= '".$radicado."',
							Gloobs		= '".utf8_decode($textObservacion)."',
							Glohis		= '".$historia."',
							Gloing 		= '".$ingreso."',
							Glofhg 		= '".date("Y-m-d")." ".date("H:i:s")."',
							Glousg 		= '".$wuse."',
							Gloecf 		= '".$estado_factura."',
							Gloesg 		= '".$estado."',
							Gloest 		= 'on',
							Glofuo 		= '".$movfuo."',
							Glorol 		= '".$rolEnviado."',
							Glofar		= '".$facturaDeFarmacia."',
							Seguridad	= 'C-".$wuse."'
				";
				$resEncabezado = mysql_query($sqlEncabezado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncabezado):</b><br>".mysql_error());

				// --> Guardar detalle de la glosa
				$idGlosa 		= mysql_insert_id($conex);
				$detalleGlosa 	= json_decode(str_replace('\\', '', $detalleGlosa), true);
				foreach($detalleGlosa as $key => $valores)
				{
					$sqlDet = "
					INSERT INTO ".$wbasedato."_000274
							SET Medico		= '".$wbasedato."',
								Fecha_data 	= '".date("Y-m-d")."',
								Hora_data 	= '".date("H:i:s")."',
								Gdeidg		= '".$idGlosa."',
								Gdecon		= '".$valores['concepto']."',
								Gdecod		= '".$valores['codigo']."',
								Gdecco		= '".$valores['cco']."',
								Gdecan		= '".str_replace(',', '.', $valores['cantFac'])."',
								Gdevfa 		= '".str_replace(',', '', $valores['valFac'])."',
								Gdecgl 		= '".str_replace(',', '.', $valores['cantidadGlo'])."',
								Gdevgl		= '".str_replace(',', '', $valores['valorGlosa'])."',
								Gdecau		= '".$valores['causaGlosa']."',
								Gdereg		= '".$valores['cardetreg']."',
								Gdeter		= '".$valores['tercero']."',
								Gdeobj		= '".$valores['objAud']."',								
								Gdeuob		= '".$valores['usuObjAud']."',								
								Gderes		= '".$valores['responsable']."',								
								Gdeest		= 'on',
								Seguridad	= 'C-".$wuse."'
					";
					mysql_query($sqlDet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDet):</b><br>".mysql_error());
				}
			}
			else
			{
				// --> Actualizar encabezado
				$sqlEncabezado = "
				UPDATE ".$wbasedato."_000273
				   SET Glotot 		= '".$glosaTotal."',
					   Glonrg 		= '".$numRelGlosa."',
					   Glorad		= '".$radicado."',
					   Gloobs		= '".utf8_decode($textObservacion)."',
					   Glofhr 		= '".date("Y-m-d")." ".date("H:i:s")."',
					   Glousr 		= '".$wuse."',
					   Gloesg 		= '".$estado."',
					   Glorol 		= '".$rolEnviado."',
					   Seguridad	= 'C-".$wuse."'
				 WHERE id 			= '".$idGlosa."'
				";
				$resEncabezado = mysql_query($sqlEncabezado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncabezado):</b><br>".mysql_error());
				
				// --> Apagar todos los registros del detalle
				$sqlOffDet = "
				UPDATE ".$wbasedato."_000274
				   SET Gdeest 		= 'off'
				 WHERE Gdeidg		= '".$idGlosa."'
				";
				mysql_query($sqlOffDet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlOffDet):</b><br>".mysql_error());

				// --> Actualizar el detalle de la glosa
				$detalleGlosa 	= json_decode(str_replace('\\', '', $detalleGlosa), true);
				foreach($detalleGlosa as $key => $valores)
				{					
					if(trim($valores['idReg']) != "")
					{
						$sqlDet = "
						UPDATE ".$wbasedato."_000274
						   SET Gdecco		= '".$valores['cco']."',
							   Gdecan		= '".str_replace(',', '.', $valores['cantFac'])."',
							   Gdevfa 		= '".str_replace(',', '', $valores['valFac'])."',
							   Gdecgl 		= '".str_replace(',', '.', $valores['cantidadGlo'])."',
							   Gdecre 		= '".str_replace(',', '.', $valores['cantReclamar'])."',
							   Gdevgl		= '".str_replace(',', '', $valores['valorGlosa'])."',
							   Gdevac 		= '".str_replace(',', '', $valores['valAceptado'])."',
							   Gdecau		= '".$valores['causaGlosa']."',
							   Gdeter		= '".$valores['tercero']."',
							   Gdeobj		= '".$valores['objAud']."',
							   Gdeuob		= '".$valores['usuObjAud']."',
							   Gderes		= '".$valores['responsable']."',							   
							   Gdeest		= 'on'
						 WHERE id			= '".$valores['idReg']."'
						";
					}
					else
					{
						$sqlDet = "
						INSERT INTO ".$wbasedato."_000274
								SET Medico		= '".$wbasedato."',
									Fecha_data 	= '".date("Y-m-d")."',
									Hora_data 	= '".date("H:i:s")."',
									Gdeidg		= '".$idGlosa."',
									Gdecon		= '".$valores['concepto']."',
									Gdecod		= '".$valores['codigo']."',
									Gdecco		= '".$valores['cco']."',
									Gdecan		= '".str_replace(',', '.', $valores['cantFac'])."',
									Gdevfa 		= '".str_replace(',', '', $valores['valFac'])."',
									Gdecgl 		= '".str_replace(',', '.', $valores['cantidadGlo'])."',
									Gdevgl		= '".str_replace(',', '', $valores['valorGlosa'])."',
									Gdecau		= '".$valores['causaGlosa']."',
									Gdereg		= '".$valores['cardetreg']."',
									Gdeter		= '".$valores['tercero']."',
									Gdeobj		= '".$valores['objAud']."',
									Gdeuob		= '".$valores['usuObjAud']."',
									Gderes		= '".$valores['responsable']."',									
									Gdeest		= 'on',
									Seguridad	= 'C-".$wuse."'
						";
					}
					
					mysql_query($sqlDet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDet):</b><br>".mysql_error());
				}
			}
			
			// --> Guardar las justificaciones						
			$justificaiones = str_replace('\\\\n', '<br>', $justificaiones);
			$justificaiones = str_replace('\\', '', $justificaiones);
			$justificaiones = str_replace('<br>', '\n', $justificaiones);
			$justificaiones = json_decode($justificaiones, true);
			
			if(count($justificaiones) > 0)
			{
				// --> Borro las que existan para luego insertar nuevas, para no tener que estar actualizando
				$sqlBorrar = "
				DELETE FROM ".$wbasedato."_000275 WHERE Jusglo = '".$idGlosa."' ";	
				mysql_query($sqlBorrar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBorrar):</b><br>".mysql_error());
				
				foreach($justificaiones as $codCau => $justif)
				{
					$sqlDet = "
					INSERT INTO ".$wbasedato."_000275
							SET Medico		= '".$wbasedato."',
								Fecha_data 	= '".date("Y-m-d")."',
								Hora_data 	= '".date("H:i:s")."',
								Jusglo		= '".$idGlosa."',
								Juscau		= '".$codCau."',
								Jusdes		= '".utf8_decode($justif)."',
								Seguridad	= 'C-".$wuse."'
					";
					mysql_query($sqlDet, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDet)$sqlDet:</b><br>".mysql_error());
				}
			}			

			$respuesta['idGlosa'] 	= $idGlosa;
			$respuesta['Error'] 	= false;
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'imprimirCarta':
		{			
			$empresasNoDetPorCco 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigosEmpresasQueNoDetallanGlosaPorCco');
			$empresasNoDetPorCco 	= explode(",", $empresasNoDetPorCco);
			$glosaDeFarmacia		= "off";
			
			$sqlGloFar = "
			SELECT Glofar
			  FROM ".$wbasedato."_000273
			 WHERE id = '".$idGlosa."' 
			";
			$resGloFar = mysql_query($sqlGloFar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGloFar):</b><br>".mysql_error());
			if($rowGloFar = mysql_fetch_array($resGloFar))
				$glosaDeFarmacia = $rowGloFar['Glofar'];
			
			// --> Impresion para las empresas soat (Carta de respuesta)
			if(in_array($codEntidad, $empresasNoDetPorCco) || $glosaDeFarmacia == 'on')
			{
				$html['Html'].= "
				<style type='text/css'>
					.imp{
						color		:	#000000;
						font-size	:	12pt;
						font-family	:	Arial;
					}
				</style>
				";
			
				// --> Consultar informacion basica de la factura
				if($glosaDeFarmacia != 'on')
				{
					$sqlInfo = "
					SELECT Glonfa, Empnom, SUM(Gdevfa) as Gdevfa, SUM(Gdevgl) as Gdevgl, SUM(Gdevac) as Gdevac, Glorad  
					  FROM ".$wbasedato."_000273 AS A, ".$wbasedato."_000024, ".$wbasedato."_000274
					 WHERE A.id   	= '".$idGlosa."'
					   AND Gloent 	= Empcod 
					   AND A.id		= Gdeidg
					   AND Gdeest	= 'on'
					 GROUP BY Gdeidg 
					";
				}
				else
				{
					$wbasedatoFar 	= consultarAliasPorAplicacion($conex, '09', 'farpmla');
					
					$sqlInfo = "
					SELECT Glonfa, Empnom, SUM(Gdevfa) as Gdevfa, SUM(Gdevgl) as Gdevgl, SUM(Gdevac) as Gdevac, Glorad  
					  FROM ".$wbasedato."_000273 AS A, ".$wbasedatoFar."_000024, ".$wbasedato."_000274
					 WHERE A.id   	= '".$idGlosa."'
					   AND Gloent 	= Empcod 
					   AND A.id		= Gdeidg
					   AND Gdeest	= 'on'
					 GROUP BY Gdeidg 
					";
				}
				
				$resInfo = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".mysql_error());
				if($rowInfo = mysql_fetch_array($resInfo))
					$info = $rowInfo;
				
				$meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

				$html['Html'].= "
				<div class='imp' align='left'>
					Medellin, ".date("d")." ".$meses[(date("m")*1)]." del ".date("Y")."
					<br>
					<br>
					<br>
					Señores:
					<br>
					<b>".$info['Empnom']."</b>
					<br>
					<br>
					<br>
					<table width='100%' class='imp'>
						<tr>
							<td width='10%' align='left' valign='top'>
								Asunto:
							</td>
							<td align='left'>
								Soluci&oacute;n a Glosa Factura Nro. ".$info['Glonfa']." por $ ".number_format($info['Gdevfa'], 0, '.', ',').".
								<br>
								Radicaci&oacute;n No. ".$info['Glorad']."
							</td>
						</tr>
					</table>
					<br><br>
					<table width='100%' class='imp'>";
					
				// --> Consultar justificaciones de las glosas
				$x = 1;
				$sqlJusti = "
				SELECT Juscau, Jusdes, Caunom
				  FROM ".$wbasedato."_000275, ".$wbasedato."_000276
				 WHERE Jusglo = '".$idGlosa."'
				   AND Juscau = Caucod 
				";
				$resJusti = mysql_query($sqlJusti, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlJusti):</b><br>".mysql_error());
				while($rowJusti = mysql_fetch_array($resJusti))
				{
					$html['Html'].= "
					<tr>
						<td align='left' colspan='2'>
							<b>".$x++.".  CODIGO DE LA GLOSA: <span style='font-style: italic;'>".$rowJusti['Juscau']." ".ucfirst(strtolower($rowJusti['Caunom']))."</span></b>
						</td>
					<tr>
					<tr>
						<td width='10%'></td>
						<td align='justify' >
							<br>
							<b>Respuesta:</b> ".utf8_encode($rowJusti['Jusdes'])."
							<br><br>
						</td>
					</tr>
					";
				}
				
				$html['Html'].= " 
					</table>
				<br><br><br>
				<table width='100%' style='font-weight:bold'>
					<tr>
						<td width='40%' align='left'>
							<b>Por lo tanto de una glosa por</b>
						</td>
						<td align='left'>
							&nbsp;&nbsp;&nbsp; $ ".number_format($info['Gdevgl'], 0, '.', ',').".
						</td>
					</tr>
					<tr>
						<td width='10%' align='left'>
							<b>Cl&iacute;nica acepta</b>
						</td>
						<td align='left'>
							&nbsp;&nbsp;&nbsp; $ ".number_format($info['Gdevac'], 0, '.', ',').".
						</td>
					</tr>
					<tr>
						<td width='10%' align='left'>
							<b>Valor Reclamado por</b>
						</td>
						<td align='left'>
							&nbsp;&nbsp;&nbsp; $ ".number_format(($info['Gdevgl']-$info['Gdevac']), 0, '.', ',').".
						</td>
					</tr>
				</table>
				<br><br><br><br>
				Cordialmente,
				<br><br><br>
				";
				
				if(strlen($wuse) > 5)
					$useTaluma = substr($wuse, -5);
				else
					$useTaluma = $wuse;
				
				$sqlUse = "
				SELECT CONCAT(Ideno1, ' ', Ideno2, ' ', Ideap1, ' ', Ideap2) as nombre, Ideext, Cardes 
				  FROM talhuma_000013, root_000079
				 WHERE Ideuse LIKE '".$useTaluma."%'
				   AND Ideccg = Carcod
				";
				$resUse = mysql_query($sqlUse, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUse):</b><br>".mysql_error());
				if($rowUse = mysql_fetch_array($resUse))
				{
					$html['Html'].= "
					<b>
					".$rowUse['nombre']."<br>
					".$rowUse['Cardes']."<br>
					CLINICA LAS AMERICAS
					MEDELLIN
					TEL 3421010 EXT ".$rowUse['Ideext']."
					</b>
					";
				}
				
				$html['Html'].= "
				</div>
				";

				$nombreSH = "generarPdf_cartasRespuesta.sh";
			}
			// --> Soporte de nota credito
			else
			{
				$html['Html'].= "
				<style type='text/css'>
					.imp{
						color		:	#000000;
						font-size	:	8pt;
						font-family	:	Arial;
					}
					.bor{
						border: 1px solid black;
					}
				</style>
				";
				$idRegImp 	= json_decode(str_replace('\\', '', $idRegImp), true);
				
				$strRegImp 	= "";
				foreach($idRegImp as $valReg)
					$strRegImp.= (($strRegImp == "") ? "" : ", ")."'".$valReg."'";
				
				// --> Consultar informacion basica de la glosa
				$sqlInfo = "
				SELECT Glonfa, Empnom   
				  FROM ".$wbasedato."_000273 AS A, ".$wbasedato."_000024 AS B
				 WHERE A.id   	= '".$idGlosa."'
				   AND Gloent 	= Empcod 
				";
				$resInfo = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".mysql_error());
				if($rowInfo = mysql_fetch_array($resInfo))
					$info = $rowInfo;
				
				$meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

				$html['Html'].= "
				<div class='imp' align='center'>
					<br><br><br>
					<table width='100%' class='imp'>
						<tr>
							<td colspan='6' align='center'><b>PROMOTORA M&Eacute;DICA LAS AM&Eacute;RICAS<br>SOLICITUD DE NOTA CR&Eacute;DITO</b><br><br></td>
						</tr>
						<tr>
							<td align='left'><b>Fecha:</b></td>
							<td align='left'>".date("Y-m-d")."</td>
							<td align='left'><b>Nota Cr&eacute;dito:</b></td>
							<td align='left'>".(($glosaTotal == "on") ? "Total" : "Parcial")."</td>
							<td align='left'><b>Entidad:</b></td>
							<td align='left'>".$info['Empnom']."</td>
						</tr>
						<tr>
							<td align='left'><b>Valor:</b></td>
							<td align='left'>VALOR_TOTAL_REEMPLA</td>
							<td align='left'><b>N° factura:</b></td>
							<td align='left'>".$info['Glonfa']."</td>
						</tr>
					</table>";
				
				$html['Html'].= "
				<br>
				<table width='100%' class='imp' style='border-collapse:collapse;'>
					<tr style='font-weight:bold' align='center'>
						<td class='bor'>CONCEPTO:</td>
						<td class='bor'>C.COSTOS</td>
						<td class='bor'>TERCERO</td>
						<td class='bor'>VALOR GLOSADO</td>
						<td class='bor'>CAUSA</td>
					</tr>";
				
				$movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
				
				if($strRegImp != '')
				{
					$arrCausasSi = array();
					// --> Consultar el detalle de la glosa
					$sqlInfo = "
					SELECT Gdecon, Gdecco, Gdeter, Gdecau, SUM(Gdevac) AS sumVal, Caunom, Cconom, Ternom
					  FROM ".$wbasedato."_000274 AS A INNER JOIN ".$wbasedato."_000276 AS B ON(A.Gdecau = B.Caucod)
							INNER JOIN ".$movhos."_000011 	 AS C ON(Gdecco = Ccocod)
							LEFT  JOIN ".$wbasedato."_000196 AS D ON(Gdeter = Tercod)
					 WHERE Gdeidg	= '".$idGlosa."'
					   AND Gdeest	= 'on'
					   AND A.id IN (".$strRegImp.")
					 GROUP BY Gdecon, Gdecco, Gdeter, Gdecau
					";
					$resInfo  = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".mysql_error());
					$valTotal = 0;
					while($rowInfo = mysql_fetch_array($resInfo))
					{
						$valTotal+=$rowInfo['sumVal'];
						$html['Html'].= "
						<tr>
							<td class='bor' align='center'>".$rowInfo['Gdecon']."</td>
							<td class='bor' align='left'>".$rowInfo['Gdecco']."-".$rowInfo['Cconom']."</td>
							<td class='bor' align='left'>".$rowInfo['Gdeter']." ".$rowInfo['Ternom']."</td>
							<td class='bor' align='right'>".number_format($rowInfo['sumVal'], 0, '.', ',')."</td>
							<td class='bor' align='left'>".$rowInfo['Gdecau']."-".$rowInfo['Caunom']."</td>
						</tr>";
						
						$arrCausasSi[$rowInfo['Gdecau']] = '';
					}
				
				
					$html['Html'] = str_replace('VALOR_TOTAL_REEMPLA', '$ '.number_format($valTotal, 0, '.', ','), $html['Html']);
					
					$html['Html'].= "
						<tr>
							<td colspan='3'></td>
							<td align='right'>$ ".number_format($valTotal, 0, '.', ',')."</td>
							<td></td>
						</tr>
					</table>
					<br><br>
					<table width='100%' class='imp' style='border-collapse:collapse;'>
						<tr><td class='bor' align='center'><b>JUSTIFICACIONES:</b></td></tr>";	

					$strCausas = "";
					foreach($arrCausasSi as $idxCau => $val)
						$strCausas.= (($strCausas == "") ? "" : ", ")."'".$idxCau."'";
					
					// --> Consultar justificaciones de las glosas
					$x = 1;
					$sqlJusti = "
					SELECT Juscau, Jusdes, Caunom
					  FROM ".$wbasedato."_000275, ".$wbasedato."_000276
					 WHERE Jusglo = '".$idGlosa."'
					   AND Juscau = Caucod
					   AND Caucod IN(".$strCausas.")					   
					";
					$resJusti = mysql_query($sqlJusti, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlJusti):</b><br>".mysql_error());
					while($rowJusti = mysql_fetch_array($resJusti))
					{
						$html['Html'].= "
						<tr>
							<td class='bor' align='justify'>
								<b>".$rowJusti['Juscau']." ".ucfirst(strtolower($rowJusti['Caunom'])).":</b>
								".utf8_encode($rowJusti['Jusdes'])."
							</td>
						<tr>
						";
					}
				}
				
				$html['Html'].= " 
					</table>
				";
				
				if(strlen($wuse) > 5)
					$useTaluma = substr($wuse, -5);
				else
					$useTaluma = $wuse;
				
				$sqlUse = "
				SELECT CONCAT(Ideno1, ' ', Ideno2, ' ', Ideap1, ' ', Ideap2) as nombre
				  FROM talhuma_000013
				 WHERE Ideuse LIKE '".$useTaluma."%'
				";
				$resUse = mysql_query($sqlUse, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUse):</b><br>".mysql_error());
				if($rowUse = mysql_fetch_array($resUse))
				{
					
					$html['Html'].= "
					<table width='100%' class='imp'>
						<tr>
							<td align='left' width='50%'><br><b>SOLICITA:</b> ".$rowUse['nombre']."</td>
							<td align='right' width='50%'><br><b>AUTORIZA:__________________________________________</b></td>
						</tr>
					</table>";
				}
				
				$html['Html'].= "
				</div>
				";
				
				$nombreSH = "generarPdf_soportesCargos.sh";
			}
			
			$wnombrePDF 	= "cartaRespuesta".$info['Glonfa'];
			$archivo_dir 	= "soportes/".$wnombrePDF.".html";
			$dir			= "soportes";

			if(is_dir($dir)){ }
			else { mkdir($dir,0777); }

			if(file_exists($archivo_dir)){
				unlink($archivo_dir);
			}

			$f = fopen($archivo_dir, "w+" );
			fwrite($f, utf8_decode($html['Html']));
			fclose($f);

			if(file_exists("soportes/".$wnombrePDF.".pdf")){
				unlink("soportes/".$wnombrePDF.".pdf");
			}

			//chmod("./generarPdf_soportesCargos.sh", 0777);
			shell_exec( "./".$nombreSH." ".$wnombrePDF );

			$html['Html'] = "
				<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='800' height='700'>"
				  ."<param name='src' value='soportes/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
				  ."<p style='text-align:center; width: 60%;'>"
					."Adobe Reader no se encuentra o la versi&oacute;n no es compatible, utiliza el icono para ir a la p&aacute;gina de descarga <br />"
					."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
					  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
					."</a>"
				  ."</p>"
				."</object>
				<br>
			";
			
			echo $html['Html'];
			
			return;
			break;
		}
		case 'imprimirObservaciones':
		{
			$infoObje;
			$infoObje = json_decode(str_replace('\\', '', $infoObje), true);
			
			$html['Html'].= "
			<style type='text/css'>
				.imp{
					color		:	#000000;
					font-size	:	7pt;
					font-family	:	Arial;
				}
				.bor{
					border: 1px solid black;
				}
			</style>
			";
			
			// --> Consultar informacion basica de la glosa
			$sqlInfo = "
			SELECT Glonfa, Empnom   
			  FROM ".$wbasedato."_000273 AS A, ".$wbasedato."_000024
			 WHERE A.id   	= '".$idGlosa."'
			   AND Gloent 	= Empcod 
			";
			$resInfo = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".mysql_error());
			if($rowInfo = mysql_fetch_array($resInfo))
				$info = $rowInfo;
			
			$meses = array("", "Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

			$html['Html'].= "
			<div class='imp' align='center'>
				<br><br><br>
				<table width='100%' class='imp'>
					<tr>
						<td colspan='6' align='center'><b>PROMOTORA M&Eacute;DICA LAS AM&Eacute;RICAS<br>Observaci&oacute;nes de auditoria a glosa</b><br><br></td>
					</tr>
					<tr>						
						<td align='left'><b>N° factura:</b></td>
						<td align='left'>".$factura."</td>
					</tr>
					<tr>
						<td align='left'><b>Fecha Imp:</b></td>
						<td align='left'>".date("Y-m-d")."</td>
						<td align='left'><b>Entidad:</b></td>
						<td align='left'>".$info['Empnom']."</td>
						<td align='left'><b>Valor Fac:</b></td>
						<td align='left'>$ ".$valTotal."</td>
					</tr>					
				</table>";
			
			$html['Html'].= "
			<br>
			<table width='100%' class='imp' style='border-collapse:collapse;'>
				<tr style='font-weight:bold' align='center'>
					<td class='bor'>CONCEPTO:</td>
					<td class='bor'>CODIGO:</td>
					<td class='bor'>C.COSTOS</td>
					<td class='bor'>TERCERO</td>
					<td class='bor'>VALOR GLOSADO</td>
					<td class='bor'>OBSERVACI&Oacute;N</td>
				</tr>";
			
			$movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			
			// --> Consultar el detalle de la glosa
			// $sqlInfo = "
			// SELECT Gdecon, Gdecod, Gdecco, Gdeter, Gdevac AS sumVal, Gdeobj, Cconom, Ternom
			  // FROM ".$wbasedato."_000274 AS A INNER JOIN ".$movhos."_000011 AS C ON(Gdecco = Ccocod)
					// LEFT  JOIN ".$wbasedato."_000196 AS D ON(Gdeter = Tercod)
			 // WHERE Gdeidg	= '".$idGlosa."'
			   // AND Gdeest	= 'on'
			   // AND Gdeobj  != ''
			// ";
			// $resInfo  = mysql_query($sqlInfo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfo):</b><br>".mysql_error());
			// $valTotal = 0;
			// while($rowInfo = mysql_fetch_array($resInfo))
				
			
				// $infoObje[$idReg]['Concepto'] 	= $valoresDet['codigo']."-".utf8_encode($valoresDet['nombreCod']);
				// $infoObje[$idReg]['Codigo'] 	= $valoresDet['codigo']."-".utf8_encode($valoresDet['nombreCod']);
				// $infoObje[$idReg]['Cco'] 		= $cco;
				// $infoObje[$idReg]['Tercero'] 	= utf8_encode($valoresDet['nomTercero']);
				// $infoObje[$idReg]['CantRecla'] 	= $cantReclamar;
				// $infoObje[$idReg]['objecion'] 	= $objecion;
							
			$arrUsus = array();
			
			foreach($infoObje as $id => $info)
			{
				// --> Obtener nombre del usuario que hizo la objecion
				if($info['usuObjecion'] != "" && !array_key_exists($info['usuObjecion'], $arrUsus))
				{
					$sqlUsu = "
					SELECT Descripcion
					  FROM usuarios
					 WHERE Codigo = '".$info['usuObjecion']."' 
					";
					$resUsu  = mysql_query($sqlUsu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUsu):</b><br>".mysql_error());
					if($rowUsu = mysql_fetch_array($resUsu))
						$arrUsus[$info['usuObjecion']] = $rowUsu['Descripcion'];
				}
		
				
				$valTotal+=$rowInfo['sumVal'];
				$html['Html'].= "
				<tr>
					<td class='bor' align='center'>".$info['Concepto']."</td>
					<td class='bor' align='center'>".$info['Codigo']."</td>
					<td class='bor' align='left'>".$info['Cco']."</td>
					<td class='bor' align='left'>".$info['Tercero']."</td>
					<td class='bor' align='right'>".number_format($info['valorAceptado'], 0, '.', ',')."</td>
					<td class='bor' align='left' width='30%'>".$info['objecion']."<br><span style='font-size:5pt;font-weight:bold'>Atte. ".$arrUsus[$info['usuObjecion']]."</span></td>
				</tr>";		
			}
			
			$html['Html'].= " 
				</table>
			";
			
			$html['Html'].= "
			</div>
			";
			
			$nombreSH = "generarPdf_soportesCargos.sh";
				
			$wnombrePDF 	= "cartaRespuesta".$info['Glonfa'];
			$archivo_dir 	= "soportes/".$wnombrePDF.".html";
			$dir			= "soportes";

			if(is_dir($dir)){ }
			else { mkdir($dir,0777); }

			if(file_exists($archivo_dir)){
				unlink($archivo_dir);
			}

			$f = fopen($archivo_dir, "w+" );
			fwrite($f, utf8_decode($html['Html']));
			fclose($f);

			if(file_exists("soportes/".$wnombrePDF.".pdf")){
				unlink("soportes/".$wnombrePDF.".pdf");
			}

			//chmod("./generarPdf_soportesCargos.sh", 0777);
			shell_exec( "./".$nombreSH." ".$wnombrePDF );

			$html['Html'] = "
				<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/".$wnombrePDF.".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='800' height='700'>"
				  ."<param name='src' value='soportes/".$wnombrePDF."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
				  ."<p style='text-align:center; width: 60%;'>"
					."Adobe Reader no se encuentra o la versi&oacute;n no es compatible, utiliza el icono para ir a la p&aacute;gina de descarga <br />"
					."<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
					  ."<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
					."</a>"
				  ."</p>"
				."</object>
				<br>
			";
			
			echo $html['Html'];
			
			return;
			break;
		}
		case 'listaGlosasRegistradas':
		{
			$respuesta 					= array('Error' => FALSE, 'Mensaje' => '', 'Html' => '');
			$respuestaLis 				= listaGlosasRegistradas($selectEstadoGLosa, $fechaBuscar1, $fechaBuscar2, $rol, $verFarmacia);
			$respuesta['Html'] 			= $respuestaLis['Html'];
			$respuesta['numRegistros'] 	= $respuestaLis['numRegistros'];
			echo json_encode($respuesta);
			return;
			break;
		}
		case 'verGlosa':
		{
			$respuesta 							= array('Error' => FALSE, 'Mensaje' => '', 'HtmlEncabezado' => '');
			$respuestaGlo 						= encabezadoGlosa($idGlosa);
			$respuesta['HtmlEncabezado'] 		= $respuestaGlo['Html'];

			$respuestaDet 						= detalleGlosa($idGlosa, '', '', '', '', $rolUsuario);
			$respuesta['HtmlDetalle']			= $respuestaDet['Html'];
			$respuesta['Error']					= $respuestaDet['Error'];
			$respuesta['Mensaje']				= $respuestaDet['Mensaje'];
			$respuesta['MensajeDetalle']		= $respuestaDet['MensajeDetalle'];
			$respuesta['estadoFacturaUnix']		= $respuestaDet['estadoFacturaUnix'];
			$respuesta['tieneNotaCredito']		= $respuestaDet['tieneNotaCredito'];
			$respuesta['htmlNotaCredito']		= $respuestaDet['htmlNotaCredito'];
			$respuesta['HtmlJustificarCausas']	= $respuestaDet['HtmlJustificarCausas'];
			$respuesta['detallarPorCco']		= $respuestaDet['detallarPorCco'];

			echo json_encode($respuesta);
			return;
			break;
		}
		case 'nuevoRegistro':
		{
			$respuesta	= array('Error' => FALSE, 'Mensaje' => '', 'Html' => '');
			$respuesta	= encabezadoGlosa();

			echo json_encode($respuesta);
			return;
			break;
		}
		case 'anularGlosa':
		{
			$respuesta	= array('Error' => FALSE, 'Mensaje' => '', 'Html' => '');

			// --> Actualizar encabezado
			$sqlEncabezado = "
			UPDATE ".$wbasedato."_000273
			   SET Glofha 		= '".date("Y-m-d")." ".date("H:i:s")."',
				   Glouan 		= '".$wuse."',
				   Gloesg 		= '".$nuevoEstado."'
			 WHERE id 			= '".$idGlosa."'
			";
			$resEncabezado = mysql_query($sqlEncabezado, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEncabezado):</b><br>".mysql_error());

			echo json_encode($respuesta);
			return;
			break;
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
	  <title>Registro de glosas</title>
	</head>
		<meta charset="UTF-8">
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	$(function(){
		
		listaGlosasRegistradas("", "");
		// --> Activar Acordeones
		inicarAcordeon();

		// --> Buscadores
		$('#buscarGlosa').quicksearch('#tableListaGlosas .find');
		
		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
		// --> Activar datapicker
		$("#fechaBuscar1").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				listaGlosasRegistradas("", "");
			}
		});
		$("#fechaBuscar2").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			onSelect: function(){
				listaGlosasRegistradas("", "");
			}
		});
		
		if($("#idGlosaMostrar").val() != '')
			verGlosa($("#idGlosaMostrar").val());

		// $("#inpFactura").keyup(function(event){
			// event 	= event || window.event;
			// if(event.key == "Enter")
			// {
				// obtenerDetalleUnix();
				// setTimeout(function(){
					// $("#inpRadicado").focus();
				// }, 1000);
			// }
		// });
	});
	//--------------------------------------------------------
	//	--> Activar datapicker
	//---------------------------------------------------------
	function cargar_elementos_datapicker()
	{
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
	}
	
	function inicarAcordeon()
	{
		$("#accordionGlosa").accordion({
			// heightStyle: "auto"
			heightStyle: "content"
		});
	}

	function blockUI()
	{
		$.blockUI({
			message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
			css:{"border": "2pt solid #7F7F7F"}
		});
	}
	//----------------------------------------------------------------
	//	--> Consulta el detalle de las glosas de una factura en unix
	//----------------------------------------------------------------
	function validarFactura()
	{
		if($("#inpFactura").val() == "")
		{
			jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>Debe ingresar el n&uacute;mero de factura.</span>", "Mensaje");
			return;
		}

		// blockUI();
		
		facturaDeFarmacia = (($("#facturaDeFarmacia").is(":checked")) ? "on" : "off");
		
		$.post("registroDeGlosas.php",
		{
			consultaAjax   		: '',
			accion         		: 'validarFacturaRegistrada',
			wemp_pmla      		: $('#wemp_pmla').val(),
			factura        		: $("#inpFactura").val(),
			facturaDeFarmacia	: facturaDeFarmacia
		}, function(respuesta){
			
			cardarDetalle = false;
			if(respuesta.Confirm)
				cardarDetalle = confirm(""+respuesta.MensajeConfirm+"");
			
			if(cardarDetalle)
			{
				verGlosa(respuesta.idGlosa);
			}
			else
			{
				if(respuesta.pedirIngreso == "on")
				{
					// alert(respuesta.htmlIngreso);
					// --> Abrir modal
					$("#seleccionarIngreso").html(respuesta.htmlIngreso).show().dialog({
						dialogClass	: 	'fixed-dialog',
						modal		: 	true,
						title		: 	"<div align='center' style='font-size:10pt;font-weight:normal;color:#2a5db0;'>Factura de farmacia cl&iacute;nica<br>Seleccione el ingreso:</div>",
						width		: 	"auto",
						height		: 	"auto",
						buttons		: 	{
							Continuar: function(){
								if(respuesta.historiaFar == "" || $("#selectIngreso").val() == "")
								{
									jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>La factura no tiene una historia cl&iacute;nica relacionada.</span>", "Mensaje");
									return;
								}	
								obtenerDetalleUnix(respuesta.historiaFar, $("#selectIngreso").val());
								$("#seleccionarIngreso").html("").hide();
								$( this ).dialog( "close" );
								$( this ).dialog( "destroy" );
							}
						}
					});
					
				}
				else
				{
					obtenerDetalleUnix("", "");
				}
			}
			
		}, 'json');
	}
	//------------------------------------------------------------------------------
	//	--> Consulta el detalle de las glosas de una factura en unix
	//------------------------------------------------------------------------------
	function obtenerDetalleUnix(hisFar, ingFar)
	{
		facturaDeFarmacia = (($("#facturaDeFarmacia").is(":checked")) ? "on" : "off");
		
		$.post("registroDeGlosas.php",
		{
			consultaAjax   		: '',
			accion         		: 'obtenerDetalleUnix',
			wemp_pmla      		: $('#wemp_pmla').val(),
			factura        		: $("#inpFactura").val(),
			facturaDeFarmacia	: facturaDeFarmacia,
			hisFar				: hisFar,
			ingFar				: ingFar,
			rolUsuario			: $("#rolUsuarioActual").val()
		}, function(respuesta){

			$.unblockUI();

			if(respuesta.Error)
			{
				jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>"+respuesta.Mensaje+"</span>", "Mensaje");

				$("#inpEntidad").html("");
				$("#fechaRegistro").html("");
				$("#detalleGlosa").html("").parent().hide();
				$("#inpGlosaTotal").attr("disabled", "disabled");
				$("#inpRadicado").attr("disabled", "disabled");
				$("#inpFactura").removeAttr("disabled");
				$("#estado_factura").val("").attr("valor", "");
				return;
			}
			else
			{
				if(respuesta.MensajeDetalle != "")
					$("#mensajeDetalle").html(respuesta.MensajeDetalle);
				else
					$("#mensajeDetalle").html("");
				
				$("table[estadoFacturaUnix]").attr("estadoFacturaUnix", respuesta.estadoFacturaUnix);
				$("table[tieneNotaCredito]").attr("tieneNotaCredito", respuesta.tieneNotaCredito);
				
				if(respuesta.tieneNotaCredito == 'on')
				{
					$("#conNotaCredito").html("SI");
					$("#conNotaCredito").attr("title", respuesta.htmlNotaCredito);
					$("#conNotaCredito").addClass("tooltip");
				}
				else
					$("#conNotaCredito").html("NO");
				
				$("#inpEntidad").html(respuesta.Entidad);
				$("#inpEntidad").attr("codEntidad", respuesta.codEntidad);
				$("#fechaRegistro").html(respuesta.Fecha);
				$("#detalleGlosa").html(respuesta.Html).parent().show();
				$("#estado_factura").val(respuesta.estadoFacturaUnix+"-"+respuesta.estadoFacturaNombre);
				$("#estado_factura").attr("valor", respuesta.estadoFacturaUnix);
				$("#estado_factura").attr("nombre", respuesta.estadoFacturaNombre);
				
				maestroEstadosFacturaReqRad  = JSON.parse($("#maestroEstadosFacturaReqRad").val());
				if(maestroEstadosFacturaReqRad[respuesta.estadoFacturaUnix] != undefined && maestroEstadosFacturaReqRad[respuesta.estadoFacturaUnix].req_radicado == 'on')
					$("#inpRadicado").attr("requerido","on");
				else
					$("#inpRadicado").attr("requerido","off");

				$("#inpGlosaTotal").removeAttr("disabled");
				$("#inpRadicado").removeAttr("disabled");
				$("#inpFactura").attr("disabled", "disabled");
				
				if(respuesta.detallarPorCco == 'on')
					$("#areaSoloVerGlosas").hide();
				else
					$("#areaSoloVerGlosas").show();
				
				detallarPorCco(respuesta.detallarPorCco);

				glosaTotal();

				// --> Cargar autocomplete
				$("input[cargarAutocomplete]").each(function(){
					crear_autocomplete("maestroCausas", $(this));
				});
				
				// --> Cargar autocomplete
				$("input[cargarAutocomplete2]").each(function(){
					crear_autocomplete("maestroRespon", $(this));
				});

				// --> Validar campos enteros y miles
				activar_regex_miles($("#detalleGlosa"));
				activar_regex_enteros($("#detalleGlosa"));

				// --> Activar tooltip
				$(".tooltip").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				
				$("#accordionGlosa").accordion("destroy");
				$("#accordionGlosa").accordion({
					heightStyle: "auto"
				});
				
				$("#checkSoloVerGlosas").removeAttr("checked");
				$("#checkCantRecla").removeAttr("checked");
				$("#checkValAceptado").removeAttr("checked");
			}

		}, 'json');
	}
	//------------------------------------------------------------------------------
	//	Funcion que valida valores enteros en un campo y le da formato de miles
	//------------------------------------------------------------------------------
	function activar_regex_miles(Contenedor)
	{
		// --> cada vez que digiten en el input
		$('.miles', Contenedor).keyup(function(){
			if($(this).val() != "")
			{
				$(this).val($(this).val().replace(/[^0-9]/g, ""));

				num = $(this).val().replace(/\,/g,'');
				num = num.replace(/\./g,'');
				num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
				num = num.split('').reverse().join('').replace(/^[\,]/,'');
				$(this).val(num);
			}
		});
		
		// --> cada vez que pierda el foco
		$('.miles', Contenedor).blur(function(){
			if($(this).val() != "")
			{
				$(this).val($(this).val().replace(/[^0-9]/g, ""));

				num = $(this).val().replace(/\,/g,'');
				num = num.replace(/\./g,'');
				num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
				num = num.split('').reverse().join('').replace(/^[\,]/,'');
				$(this).val(num);
			}
		});
	}
	function regexComillas()
	{
		// --> cada vez que digiten en el input		
		$("textarea").keyup(function(){
			if($(this).val() != "")
				$(this).val($(this).val().replace(/"|'/g, ""));
		});
		
		$("textarea").focusout(function(){
			if($(this).val() != "")
			{
				mystring = $(this).val();
				mystring = mystring.replace(/"|'/g , "");
				$(this).val(mystring);
			}
		});
	}
	//------------------------------------------------------------------------------
	//	Funcion que valida valores enteros en un campo
	//------------------------------------------------------------------------------
	function activar_regex_enteros(Contenedor)
	{
		// --> cada vez que digiten en el input
		$('.entero', Contenedor).keyup(function(){
			if($(this).val() != "")
			{
				$(this).val($(this).val().replace(/[^0-9.]/g, ""));
			}
		});
	}
	//------------------------------------------------------------------------
	//	--> Al dar doble click en el campo se copia y pega el valor facturado
	//------------------------------------------------------------------------
	function copiarValor(elemento)
	{
		if($(elemento).attr("valFac") != undefined)
		{			
			cantGlo = parseFloat($(elemento).parent().parent().find("input[cantFac]").val());
			// console.log(cantGlo);
			cantGlo = (cantGlo > 0) ? cantGlo : 1;

			cantFac = parseInt($(elemento).parent().parent().find("input[cantFac]").attr("cantFac"));
			cantFac = (cantFac > 0) ? cantFac : 1;

			valUni 	= parseInt($(elemento).attr("valFac").replace(/,/g, ""));
			valUni	= valUni/cantFac;

			$(elemento).val(parseInt(cantGlo*valUni));
		}
		else
		{
			if($(elemento).attr("cantReclamar") != undefined)
			{
				$(elemento).val($(elemento).attr("cantReclamar"));
			}
			else
			{
				if($(elemento).attr("cantFac") != undefined)
					$(elemento).val($(elemento).attr("cantFac"));
				else
					$(elemento).val($(elemento).attr("valGlosado"));
			}
		}
	}
	//----------------------------------------------------------------------------------------------------
	//	--> Al dar click se igualaran todos los campos de cantidad a reclamar con cantidad glosa
	//----------------------------------------------------------------------------------------------------
	function cantReclamarPorDefecto()
	{
		if($("#checkPorCco").is(":checked"))
			selector = "[detalleCCo=SI]";
		else
			selector = "[detalleCCo=NO]";
		
		if($("#checkCantRecla").is(":checked"))
			$("#tableDetalleGlosa tr[detalle]"+selector+"").find("[cantReclamar]").each(function(){				
				if($(this).attr("cantReclamar") != "" && $(this).attr("disabled") == undefined)
					$(this).val($(this).attr("cantReclamar"));	
			});
		else
			$("#tableDetalleGlosa tr[detalle]"+selector+"").find("[cantReclamar]").each(function(){				
				if($(this).attr("disabled") == undefined)
					$(this).val("");	
			});
	}
	function abrirHoja()
	{
		his = $("#tableDetalleGlosa").attr("historia");
		ing = $("#tableDetalleGlosa").attr("ingreso");
		
		if(his != '' && ing != '')
		{		
			// var url 	= "/matrix/movhos/reportes/Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla="+$("#wemp_pmla").val()+"&whis="+his+"&wing="+ing+"&wcco=*";
			// alto		= screen.availHeight;
			// ventana 	= window.open('','','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
			// ventana.document.open();
			// ventana.document.write("<span><b>CONSULTA DESDE REGISTRO DE GLOSAS<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + url + "' height='" + (parseInt(alto,10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
		
			ruta = "/matrix/movhos/reportes/Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla="+$("#wemp_pmla").val()+"&whis="+his+"&wing="+ing+"&wcco=*";
			window.open(ruta,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
		}
	}
	function abrirOrdenes()
	{
		his = $("#tableDetalleGlosa").attr("historia");
		ing = $("#tableDetalleGlosa").attr("ingreso");
		
		if(his != '' && ing != '')
		{
			var ruta 	= "/matrix/hce/procesos/ordenes_imp.php?wemp_pmla="+$("#wemp_pmla").val()+"&whistoria="+his+"&wingreso="+ing+"&tipoimp=imp&alt=off&wtodos_ordenes=on&orden=asc&origen=on";
			window.open(ruta,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
		}
	}
	//----------------------------------------------------------------------------------------------------
	//	--> Solo se mostraran los registros que esten glosados
	//----------------------------------------------------------------------------------------------------
	function soloVerGlosas()
	{
		if($("#checkPorCco").is(":checked"))
			selector = "[detalleCCo=SI]";
		else
			selector = "[detalleCCo=NO]";
		
		if($("#checkSoloVerGlosas").is(":checked"))
		{					
			$("#tableDetalleGlosa tr[detalle]"+selector+"").each(function(){
				
				cantidadGlo 					= $(this).find("[cantFac]");
				valorGlosa 						= $(this).find("[valFac]");
				causaGlosa 						= $(this).find("[selectCausa]");

				if((cantidadGlo.val() == "" || cantidadGlo.val() == 0) && valorGlosa.val() == "" && causaGlosa.val() == "")
				{
					$(this).hide();
				}
			});
		}
		else
		{
			$("#tableDetalleGlosa tr[detalle]"+selector+"").show();		
		}			
	}
	//----------------------------------------------------------------------------------------------------
	//	--> Al dar doble click se igualaran todos los campos de valor aceptado en cero
	//----------------------------------------------------------------------------------------------------
	function valAceptadoPorDefecto()
	{
		if($("#checkPorCco").is(":checked"))
			selector = "[detalleCCo=SI]";
		else
			selector = "[detalleCCo=NO]";
		
		if($("#checkValAceptado").is(":checked"))
			$("#tableDetalleGlosa tr[detalle]"+selector+"").find("[valAceptado]").each(function(){				
				if($(this).attr("valglosado") != "" && $(this).attr("disabled") == undefined)
					$(this).val(0);	
			});
		else
			$("#tableDetalleGlosa tr[detalle]"+selector+"").find("[valAceptado]").each(function(){				
				if($(this).attr("disabled") == undefined)
					$(this).val("");	
			});
		
		sumatoriaAceptado();
	}
	//------------------------------------------------------------------------
	//	--> Acordeon
	//------------------------------------------------------------------------	
	function desplegar(concepto, elemento)
	{
		// --> Mostrar
		if(jQuery(elemento).attr("src") == "../../images/medical/iconos/gifs/i.p.next[1].gif")
		{
			jQuery(elemento).attr("src", "../../images/medical/iconos/gifs/i.p.previous[1].gif");
			// $("[detalle="+concepto+"]:[menuCco]").show();
			if($("#checkPorCco").is(":checked"))
			{
				$("[detalle="+concepto+"][detalleCCo=SI]").show();
				//$("[detalle="+concepto+"]").show();
			}
			else	
				$("[detalle="+concepto+"][detalleCCo=NO]").show();
			
			// $("[detalle="+concepto+"]").find("td").css({
				// 'border': 	''
			// });
		}
		// --> Ocultar
		else
		{
			jQuery(elemento).attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");
			
			if($("#checkPorCco").is(":checked"))
			{
				$("[detalle="+concepto+"][detalleCCo=SI]").hide();
				//$("[detalle="+concepto+"]").show();
			}
			else	
				$("[detalle="+concepto+"][detalleCCo=NO]").hide();
			
			//$("[detalle="+concepto+"]").find("img").attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");
		}
		
	}
	//----------------------------------------------------------------------------
	//	--> Limpiar campos del formulario
	//----------------------------------------------------------------------------
	function iniciarFormulario()
	{
		$("[selectcausa]").val("");
		glosaTotal();
	}
	//----------------------------------------------------------------------------
	//	--> Al dar click, todos los valores glosados seran igual a valor facturado
	//----------------------------------------------------------------------------
	function glosaTotal()
	{
		if($("#inpGlosaTotal").is(':checked'))
		{
			if($("#checkPorCco").is(":checked"))
				selector = "[detalleCCo=SI]";
			else
				selector = "[detalleCCo=NO]";
		
			$("#tableDetalleGlosa tr[detalle]"+selector+"").find("input[valFac]").each(function(){
				$(this).val($(this).attr("valFac"));
			});
			
			$("#tableDetalleGlosa tr[detalle]"+selector+"").find("input[cantFac]").each(function(){
				$(this).val($(this).attr("cantFac"));
			});
		}
		else
		{
			$("input[valFac]").val("");
			$("input[cantFac]").val("");
		}
		
		sumatoriaSaldo();
	}
	//----------------------------------------------------------------------------
	//	--> Pinta la lista de las glosas registradas
	//----------------------------------------------------------------------------
	function listaGlosasRegistradas(rol, elemento)
	{
		if(rol == '')
		{
			if($("span[select=si]").length == 0)
			{
				rol = $("#rolUsuarioActual").val();
				$("[botonRol]").attr("class", "bordeRed ui-state-default ui-corner-top").attr("select", "no");
				$("[botonRol="+rol+"]").eq(0).attr("class", "bordeRed ui-state-default ui-corner-top ui-tabs-selected ui-state-active").attr("select", "si");
			}
			else
				rol = $("span[select=si]").attr("botonRol");			
		}
		else{
			$("[botonRol]").attr("class", "bordeRed ui-state-default ui-corner-top").attr("select", "no");
			$(elemento).attr("class", "bordeRed ui-state-default ui-corner-top ui-tabs-selected ui-state-active").attr("select", "si");
		}
		
		// $("[botonRol]").attr("class", "bordeRed ui-state-default ui-corner-top").attr("select", "no");
		// $("[botonRol="+rol+"]").attr("class", "bordeRed ui-state-default ui-corner-top ui-tabs-selected ui-state-active").attr("select", "si");
		
		$.post("registroDeGlosas.php",
		{
			consultaAjax		:   '',
			accion				:   'listaGlosasRegistradas',
			wemp_pmla			:	$('#wemp_pmla').val(),
			selectEstadoGLosa	: 	$("#selectEstadoGLosa").val(),
			fechaBuscar1		:	$("#fechaBuscar1").val(),
			fechaBuscar2		:	$("#fechaBuscar2").val(),
			rol					: 	rol,
			verFarmacia 		:	 $("span[select=si]").attr("verFarmacia")
			

		}, function(respuesta){
			$("#listaDeglosas").html(respuesta.Html);
			$("#numRegistros").text(respuesta.numRegistros);
			// --> Buscadores
			$('#buscarGlosa').quicksearch('#tableListaGlosas .find');
		}, 'json');
	}
	//----------------------------------------------------------------------------
	//	--> Pinta la lista de las glosas registradas
	//----------------------------------------------------------------------------
	function anularGlosa(nuevoEstado, idGlosa, msj)
	{
		jConfirm("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>Esta seguro en "+msj+"?</span>", 'Confirmar', function(respuesta) {
			if(respuesta)
			{
				blockUI();
				$.post("registroDeGlosas.php",
				{
					consultaAjax	:   '',
					accion			:   'anularGlosa',
					wemp_pmla		:	$('#wemp_pmla').val(),
					idGlosa			:	idGlosa,
					nuevoEstado		:	nuevoEstado

				}, function(respuesta){
					$.unblockUI();
					if(!respuesta.Error)
					{
						jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>Anulacion realizada.</span>", "Mensaje");
						listaGlosasRegistradas("", "");
						verGlosa(idGlosa);
					}
				}, 'json');
			}
		});
	}
	//----------------------------------------------------------------------------
	//	--> Validar que el valor aceptado no supere el valor glosado
	//----------------------------------------------------------------------------
	function validarValorAceptado(elemento)
	{
		$(".bordeRojo").removeClass("bordeRojo");
		elemento = $(elemento);
		valorAceptado 	= parseInt(elemento.val().replace(/,/g, ""));
		valorGlosado 	= parseInt(elemento.parent().parent().find("[valFac]").val().replace(/,/g, ""));
		if(valorAceptado > valorGlosado)
		{
			jAlert("<span style='color:red;font-family:verdana;font-size:10pt;' align='center'>El valor aceptado no puede ser mayor al glosado.</span>", "Mensaje");
			elemento.addClass("bordeRojo");
			$("#popup_ok").click(function() {
				elemento.focus();
			});
		}
	}
	//----------------------------------------------------------------------------
	//	--> 
	//----------------------------------------------------------------------------
	function sumatoriaSaldo()
	{
		valTotal = 0;
		$("input[valfac]").each(function(){
			if($(this).val() != '')
				valTotal+= parseInt($(this).val().replace(/,/g, ""));
		});
		
		if(valTotal > $("#sumatoriaSaldo").attr("totalValSaldo"))
		{
			$("#sumatoriaSaldo").css({"color":"red"});
		}
		else
			$("#sumatoriaSaldo").css({"color":"#000000"});
		
		valTotal = valTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		$("#sumatoriaSaldo").text(valTotal);
				
		
	}

	//----------------------------------------------------------------------------
	//	--> 
	//----------------------------------------------------------------------------
	function sumatoriaAceptado()
	{
		valTotal = 0;
		$("input[valAceptado]").each(function(){
			if($(this).val() != '')
				valTotal+= parseInt($(this).val().replace(/,/g, ""));
		});
		
		// if(valTotal > $("#sumatoriaAceptado").attr("totalValSaldo"))
		// {
			// $("#sumatoriaAceptado").css({"color":"red"});
		// }
		// else
			// $("#sumatoriaAceptado").css({"color":"#000000"});
		
		valTotal = valTotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		$("#sumatoriaAceptado").text(valTotal);
				
		
	}
	//----------------------------------------------------------------------------
	//	--> Validar que no se supere el saldo total por concepto
	//----------------------------------------------------------------------------
	function validarValorMaximo(elemento, concepto)
	{
		$(".bordeRojo").removeClass("bordeRojo");

		// saldoConcepto = parseInt($("[detalle="+concepto+"]").attr("saldoConcepto"));
		// saldoTotalDet = 0;
		// $("[detalle="+concepto+"]").find("input[valFac]").each(function(){
			// valor = $(this).val().replace(",", "");
			// if(valor != '')
				// saldoTotalDet = saldoTotalDet+parseInt(valor);
		// });

		valFacturado = $(elemento).attr("valFac").replace(/,/g, "");
		if(valFacturado != '')
			valFacturado = parseInt(valFacturado);
		else
			valFacturado = 0;

		valGlosado = $(elemento).val().replace(/,/g, "");
		if(valGlosado != '')
			valGlosado = parseInt(valGlosado);
		else
			valGlosado = 0;

		// if(saldoTotalDet > saldoConcepto)
		if(valGlosado > valFacturado && valFacturado > 0)
		{
			jAlert("<span style='color:red;font-family:verdana;font-size:10pt;' align='center'>No se puede superar el valor facturado.<br>Por favor correguir.<b></b></span>", "Mensaje");
			// $("[detalle="+concepto+"]").find("input[valFac]").addClass("bordeRojo");
			$(elemento).addClass("bordeRojo");

			$("#popup_ok").click(function() {
				$(elemento).focus();
			});

		}
	}
	//----------------------------------------------------------------------------
	//	--> 
	//----------------------------------------------------------------------------
	function detallarPorCco(detallar)
	{
		// --> Mostrar detalle por cco
		if(detallar == "on")
		{
			$("#checkPorCco").attr("checked", "checked");
			// $("#tableDetalleGlosa tr[detalleCCo=NO]").hide();
			// $("#tableDetalleGlosa tr[detalleCCo=SI]").show();
		}
		else
		{
			$("#checkPorCco").removeAttr("checked");
			// $("#tableDetalleGlosa tr[detalleCCo=SI]").hide();
			// $("#tableDetalleGlosa tr[detalleCCo=NO]").show();
		}
	}
	//----------------------------------------------------------------------------
	//	--> Grabar la glosa
	//----------------------------------------------------------------------------
	function grabarGlosa(idGlosa, estado, rolEnviado, validarCampos)
	{
		//console.log(validarCampos);
		// --> Validar campos
		var guardar 		= true;
		var detalleGlosa 	= new Object();
		var totalValGlosas	= 0;
		var totalValAceptado= 0;
		var hayDetalle		= false;

		$(".bordeRojo").removeClass("bordeRojo");

		if($("#inpRadicado").attr("requerido") == "on" && $("#inpRadicado").val() == "")
		{
			$("#inpRadicado").addClass("bordeRojo");
			guardar = false;
		}
		
		// --> Si el estado de la factura es AP y no tiene nota credito, no es obligatoria poner la causa.
		if($("table[estadoFacturaUnix]").attr("estadoFacturaUnix") == "AP" && $("table[tieneNotaCredito]").attr("tieneNotaCredito") != 'on')
			validarCausa = false;
		else
			validarCausa = true;
		
		var index = -1;
		
		if($("#checkPorCco").is(":checked"))
			selector = "[detalleCCo=SI]";
		else
			selector = "[detalleCCo=NO]";
				
		$("#tableDetalleGlosa tr[detalle]"+selector+"").each(function(){
			
			cantidadGlo 					= $(this).find("[cantFac]");
			valorGlosa 						= $(this).find("[valFac]");
			causaGlosa 						= $(this).find("[selectCausa]");

			if((cantidadGlo.val() != "" && cantidadGlo.val() != 0) || valorGlosa.val() != "" || causaGlosa.val() != "")
			{
				index		= index+1;
				hayDetalle	= true;
				
				detalleGlosa[index] 			= new Object();
				detalleGlosa[index].concepto 	= $(this).attr("detalle");
				detalleGlosa[index].codigo 		= $(this).attr("codigo");
				detalleGlosa[index].cco 		= $(this).find("td[cco]").attr("cco");
				detalleGlosa[index].cantFac		= $(this).find("[cantFac]").attr("cantFac");
				detalleGlosa[index].valFac		= $(this).find("[valFac]").attr("valFac");
				detalleGlosa[index].objAud		= $(this).find("textarea[objAud]").val();
				detalleGlosa[index].usuObjAud	= $(this).find("textarea[objAud]").attr("usuedita");
				detalleGlosa[index].tercero		= $(this).attr("tercero");
				detalleGlosa[index].cardetreg	= $(this).attr("cardetreg");
				detalleGlosa[index].idReg		= $(this).attr("idReg");
				detalleGlosa[index].cantReclamar = $(this).find("[cantReclamar]").val();
				detalleGlosa[index].valAceptado = $(this).find("[valAceptado]").val();
				detalleGlosa[index].responsable = $(this).find("[selectRespo]").attr("valor");
				
				if(cantidadGlo.val() == "")
				{
					cantidadGlo.parent().addClass("bordeRojo");
					guardar = false;
				}
				else
					detalleGlosa[index].cantidadGlo = cantidadGlo.val();
				
				if(valorGlosa.val() == "")
				{
					valorGlosa.parent().addClass("bordeRojo");
					guardar = false;
				}
				else
				{
					detalleGlosa[index].valorGlosa = valorGlosa.val();
					totalValGlosas+= parseInt(valorGlosa.val().replace(/,/g, ""));
				}

				if(causaGlosa.attr("valor") == "" && validarCausa && causaGlosa.attr("disabled") == undefined && validarCampos == 'on')
				{
					causaGlosa.parent().addClass("bordeRojo");
					guardar = false;
				}
				else
					detalleGlosa[index].causaGlosa = causaGlosa.attr("valor");
				
				if($(this).find("[cantReclamar]").val() != undefined && $(this).find("[cantReclamar]").attr("disabled") == undefined && validarCampos == 'on')
				{
					if($(this).find("[cantReclamar]").val() == "")
					{
						$(this).find("[cantReclamar]").parent().addClass("bordeRojo");
						guardar = false;
					}
				}
				
				if($(this).find("[valAceptado]").val() != undefined && $(this).find("[valAceptado]").attr("disabled") == undefined && validarCampos == 'on')
				{					
					if($(this).find("[valAceptado]").val() == "")
					{
						$(this).find("[valAceptado]").parent().addClass("bordeRojo");
						guardar = false;
					}
					else
					{
						totalValAceptado+= parseInt($(this).find("[valAceptado]").val().replace(/,/g, ""));
					}
				}
			}
			
		});
		
		totalValSaldo = parseInt($("#tableDetalleGlosa").find("td[totalValSaldo]").attr("totalValSaldo"));

		if( (totalValGlosas-totalValAceptado) > totalValSaldo)
		{
			jAlert("<span style='color:red;font-family:verdana;font-size:10pt'>El valor total glosado <b>("+totalValGlosas+")</b> no puede superar el valor total del saldo<b>("+totalValSaldo+")</b>.<br>Por favor correguir.</span>", "Mensaje");
			return;
		}
		
		// --> Obtener la justificacion de causas
		var justificaiones = new Object();
		$("#tableJustificacion textarea[codCausa]").each(function(){
			if($(this).val() == "")
			{
				$(this).addClass("bordeRojo");
				guardar = false;
			}	
			else
				justificaiones[$(this).attr("codCausa")] =  $(this).val();
		});
		
		if(guardar && hayDetalle)
		{
			mensaje = '<span style="color:#2a5db0">Est&aacute; seguro en grabar el registro de glosa?</span>';
			jConfirm(mensaje, 'Confirmar', function(respuesta) {
				if(respuesta)
				{
					blockUI();
					$.post("registroDeGlosas.php",
					{
						consultaAjax	:   '',
						accion			:   'grabarGlosa',
						wemp_pmla		:	$('#wemp_pmla').val(),
						factura			:	$("#inpFactura").val(),
						entidad			:	$("#inpEntidad").attr('codEntidad'),
						glosaTotal		:	(($("#inpGlosaTotal").is(':checked')) ? 'on' : 'off'),
						radicado		:	$("#inpRadicado").val(),
						textObservacion	:	$("#textObservacion").val(),
						estado_factura	:	$("#estado_factura").attr("valor"),
						numRelGlosa		:	$("#inpNroRelacionGlosa").val(),
						fechaRegistro	:	$("#fechaRegistro").text(),
						historia		:	$("#tableDetalleGlosa").attr("historia"),
						ingreso			:	$("#tableDetalleGlosa").attr("ingreso"),
						movfuo			:	$("#tableDetalleGlosa").attr("movfuo"),
						detalleGlosa	:	JSON.stringify(detalleGlosa),
						justificaiones	:	JSON.stringify(justificaiones),
						idGlosa			:	idGlosa,
						estado			:	estado,
						rolEnviado		:	rolEnviado,
						facturaDeFarmacia:	(($("#facturaDeFarmacia").is(":checked")) ? "on" : "off"),
						resSessionJson	: true

					}, function(respuesta){
						$.unblockUI();
						if(respuesta.msjSession != undefined)
						{
							jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>"+respuesta.msjSession+"</span>", "Ha caducado la sesi&oacute;n de matrix");
							return;
						}
						
						if(!respuesta.Error)
						{
							jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>Registro grabado.</span>", "Mensaje");
							listaGlosasRegistradas("", "");
							verGlosa(respuesta.idGlosa);
						}
					}, 'json');
				}
			});
		}
		else
		{
			jAlert("<span style='color:red;font-family:verdana;font-size:10pt'>Debe ingresar todos los valores.</span>", "Mensaje");
			inicarAcordeon();
		}
	}
	//----------------------------------------------------------------------------
	//	--> Ver detalle de una glosa
	//----------------------------------------------------------------------------
	function nuevoRegistro()
	{
		$.post("registroDeGlosas.php",
		{
			consultaAjax	:   '',
			accion			:   'nuevoRegistro',
			wemp_pmla		:	$('#wemp_pmla').val()

		}, function(respuesta){
			$("#encabezadoGlosa").html(respuesta.Html);

			$("#detalleGlosa").html("").parent().hide();
			$("#accordionGlosa").accordion("destroy");
			$("#accordionGlosa").accordion({
				heightStyle: "auto"
			});
			$("#justificarCausas").html("").parent().hide();

		}, 'json');/*.done(function(){
			crear_autocomplete("maestroEstadosFactura", $("#estado_factura"));
		});*/
	}
	//----------------------------------------------------------------------------
	//	--> Ver detalle de una glosa
	//----------------------------------------------------------------------------
	function verGlosa(idGlosa)
	{
		blockUI();
		
		$.post("registroDeGlosas.php",
		{
			consultaAjax		:   '',
			accion				:   'verGlosa',
			wemp_pmla			:	$('#wemp_pmla').val(),
			idGlosa				: 	idGlosa,
			rolUsuario			: 	$("#rolUsuarioActual").val()

		}, function(respuesta){
			$.unblockUI();
			if(respuesta.Error)
			{
				jAlert("<span style='color:#2a5db0;font-family:verdana;font-size:10pt'>"+respuesta.Mensaje+"</span>", "Mensaje");

				$("#inpEntidad").html("");
				$("#fechaRegistro").html("");
				$("#detalleGlosa").html("").parent().hide();
				$("#inpGlosaTotal").attr("disabled", "disabled");
				return;
			}
			else
			{
				
				$("#encabezadoGlosa").html(respuesta.HtmlEncabezado);
				
				if(respuesta.MensajeDetalle != "")
					$("#mensajeDetalle").html(respuesta.MensajeDetalle);
				else
					$("#mensajeDetalle").html("");
				
				$("table[estadoFacturaUnix]").attr("estadoFacturaUnix", respuesta.estadoFacturaUnix);
				$("table[tieneNotaCredito]").attr("tieneNotaCredito", respuesta.tieneNotaCredito);
				
				if(respuesta.tieneNotaCredito == 'on')
				{
					$("#conNotaCredito").html("SI");
					$("#conNotaCredito").attr("title", respuesta.htmlNotaCredito);
					$("#conNotaCredito").addClass("tooltip");
				}
				else
					$("#conNotaCredito").html("NO");				

				$("#detalleGlosa").html(respuesta.HtmlDetalle).parent().show();
				
				estadoGlosa = $("#tableDetalleGlosa").attr("estadoGlosa");
				if(estadoGlosa == "GL" || estadoGlosa == "RA")
					$("#justificarCausas").html(respuesta.HtmlJustificarCausas).parent().show();
				else
				{
					if(estadoGlosa == "GR")
					{
						$("#justificarCausas").html(respuesta.HtmlJustificarCausas).parent().show();
						$("#justificarCausas textarea").attr("disabled", "disabled");
					}
					else
						$("#justificarCausas").html("").parent().hide();
				}
				
				if(respuesta.detallarPorCco == 'on')
					$("#areaSoloVerGlosas").hide();
				else
					$("#areaSoloVerGlosas").show();
				
				detallarPorCco(respuesta.detallarPorCco);
				
				// --> Cargar autocomplete
				$("input[cargarAutocomplete]").each(function(){
					crear_autocomplete("maestroCausas", $(this));
				});
				
				// --> Cargar autocomplete
				$("input[cargarAutocomplete2]").each(function(){
					crear_autocomplete("maestroRespon", $(this));
				});

				//$("#inpGlosaTotal").removeAttr("disabled");

				// --> Validar campos enteros y miles
				activar_regex_miles($("#detalleGlosa"));
				activar_regex_enteros($("#detalleGlosa"));
				
				sumatoriaSaldo();
				sumatoriaAceptado();
				
				// --> Activar tooltip
				$(".tooltip").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				
				$("#accordionGlosa").accordion("destroy");
				$("#accordionGlosa").accordion({
					heightStyle: "auto"
				});
				
				$("#checkSoloVerGlosas").removeAttr("checked");
				$("#checkCantRecla").removeAttr("checked");
				$("#checkValAceptado").removeAttr("checked");
				
				regexComillas();
			}

		}, 'json');
	}
	//-----------------------------------------------------------
	//	--> Cargar autocomplete de campos
	//-----------------------------------------------------------
	function crear_autocomplete(HiddenArray, CampoCargar)
	{
		ArrayVal	  = JSON.parse($("#"+HiddenArray).val());
		
		if(HiddenArray == "maestroCausas")
		{	
			ArrayValores1 = ArrayVal[CampoCargar.attr("conceptoSele")];
			ArrayValores2 = ArrayVal["*"];
		}
		else
			ArrayValores1 = ArrayVal;
		
		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores1)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].codigo = CodVal;
			ArraySource[index].value  = CodVal+"-"+ArrayValores1[CodVal];
			ArraySource[index].label  = CodVal+"-"+ArrayValores1[CodVal];
			ArraySource[index].nombre = CodVal+"-"+ArrayValores1[CodVal];
		}
		
		for (var CodVal in ArrayValores2)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].codigo = CodVal;
			ArraySource[index].value  = CodVal+"-"+ArrayValores2[CodVal];
			ArraySource[index].label  = CodVal+"-"+ArrayValores2[CodVal];
			ArraySource[index].nombre = CodVal+"-"+ArrayValores2[CodVal];
		}

		// --> Si el autocomplete ya existe, lo destruyo
		if( CampoCargar.attr("autocomplete") != undefined )
			CampoCargar.removeAttr("autocomplete");

		// --> Creo el autocomplete
		CampoCargar.autocomplete({
			minLength: 	0,
			source: 	ArraySource,
			select: 	function( event, ui ){
				
				valorAnt = CampoCargar.attr('valor');
				CampoCargar.val(ui.item.label);
				CampoCargar.attr('valor', ui.item.codigo);
				CampoCargar.attr('nombre', ui.item.nombre);
				
				if(HiddenArray == "maestroCausas")
				{
					if(valorAnt != ui.item.codigo)
					{
						mantener = false;
						$("[selectCausa]").each(function(){
							if($(this).attr("valor") == valorAnt)
								mantener = true;
						});
						
						if(!mantener)
							$("textarea[codCausa="+valorAnt+"]").parent().remove();
					}
					
					if($("#tableJustificacion").find("textarea[codCausa="+ui.item.codigo+"]").val() == undefined)
					{
						htmlCausa = ""+
						"<td style='color:#000000;font-size:8pt;padding:1px;font-family:verdana;' align='center'>"+
							"<b>"+ui.item.codigo+"-"+ui.item.nombre+"</b><br>"+
							"<textarea codCausa='"+ui.item.codigo+"' placeholder='Digitar texto' style='width: 380px; height: 60px;'></textarea>"+
						"</td>";
						
						cantTd = $("#tableJustificacion tr:last").find("td").length;
						if( cantTd < 3 && cantTd > 0)
							$("#tableJustificacion tr:last").find("td:last").after(htmlCausa);
						else
							$("#tableJustificacion").append("<tr>"+htmlCausa+"</tr>");
						
						regexComillas();
					}
				}
				
				return false;
			}
		});
		limpiaAutocomplete(CampoCargar, HiddenArray);
	}
	//----------------------------------------------------------------------------------
	//	--> Imprimir carta de repuesta a glosa
	//----------------------------------------------------------------------------------
	function imprimirCarta(idGlosa)
	{
		var idRegImp 	= new Array();		
		$("[checkParaImp]").each(function(){
			if($(this).is(':checked'))
				idRegImp.push($(this).parent().parent().attr("idReg")); 
		});
		
		$.post("registroDeGlosas.php",
		{
			consultaAjax	:   '',
			accion			:   'imprimirCarta',
			wemp_pmla		:	$('#wemp_pmla').val(),
			idGlosa			: 	idGlosa,
			codEntidad		:	$("#inpEntidad").attr('codEntidad'),
			glosaTotal		:	(($("#inpGlosaTotal").is(':checked')) ? 'on' : 'off'),
			factura			:	$("#inpFactura").val(),
			valTotal		: 	$("#sumatoriaAceptado").text(),
			idRegImp		: 	JSON.stringify(idRegImp)

		}, function(respuesta){
			// var contenido	= "<html><body onload='window.print();window.close();'>";
			// var contenido	= "<html><body>";
			// contenido 		= respuesta+"</body></html>";

			// var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=1,height=1,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
			// var windowAttr = "fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0";
			// var ventana = window.open( "", "",  windowAttr );
			// ventana.document.write(contenido);
			//ventana.document.close();

			// --> Abrir modal
			$("#imprimirCarta").html(respuesta).show().dialog({
				dialogClass	: 	'fixed-dialog',
				modal		: 	true,
				title		: 	"<div align='center' style='font-size:10pt'>Imprimir</div>",
				width		: 	"auto",
				height		: 	"700",
				buttons		: 	{
					Cerrar: function(){
						$("#imprimirCarta").html("").hide();
						$( this ).dialog( "close" );
						$( this ).dialog( "destroy" );
					}
				}
			});
			
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Imprimir carta con todas las observaciones del auditor
	//----------------------------------------------------------------------------------
	function imprimirObsAud(idGlosa, elem)
	{
		$.post("registroDeGlosas.php",
		{
			consultaAjax	:   '',
			accion			:   'imprimirObservaciones',
			wemp_pmla		:	$('#wemp_pmla').val(),
			idGlosa			: 	idGlosa,
			codEntidad		:	$("#inpEntidad").attr('codEntidad'),
			factura			:	$("#inpFactura").val(),
			valTotal		: 	$("#sumatoriaAceptado").text(),
			infoObje		:	$(elem).attr("infoObje")

		}, function(respuesta){

			// --> Abrir modal
			$("#imprimirCarta").html(respuesta).show().dialog({
				dialogClass	: 	'fixed-dialog',
				modal		: 	true,
				title		: 	"<div align='center' style='font-size:10pt'>Imprimir</div>",
				width		: 	"auto",
				height		: 	"700",
				buttons		: 	{
					Cerrar: function(){
						$("#imprimirCarta").html("").hide();
						$( this ).dialog( "close" );
						$( this ).dialog( "destroy" );
					}
				}
			});
			
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Controlar que el input no quede con basura, sino solo con un valor seleccionado
	//----------------------------------------------------------------------------------
	function limpiaAutocomplete(CampoCargar, HiddenArray)
	{
		CampoCargar.on({
			focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{					
					if(HiddenArray == "maestroCausas")
					{
						valorAnt = $(this).attr("valor");
						
						$(this).val("");
						$(this).attr("valor","");
						$(this).attr("nombre","");
					
						mantener = false;
						$("[selectCausa]").each(function(){
							if($(this).attr("valor") == valorAnt)
								mantener = true;
						});
						
						if(!mantener)
							$("textarea[codCausa="+valorAnt+"]").parent().remove();
					}
					else
					{
						$(this).val("");
						$(this).attr("valor","");
						$(this).attr("nombre","");
					}
				}
				else
				{
					$(this).val($(this).attr("nombre"));
				}
			}
		});
	}
	//-------------------------------------------------------
	//	EDITAR LA OBJECION
	//-------------------------------------------------------
	function editarObjecion(ele)
	{
		var posicion = $(ele).position();				
		$(ele).next().css({'left':posicion.left-240,'top':posicion.top+16, 'position':'absolute'}).show(400);
		$(ele).parent().css({'background-color':'#feffce'});
		$(ele).next().find("textarea").focus();
		$(ele).next().focusout(function(){
			$(this).hide(400);
			$(this).parent().css({'background-color': $(this).parent().prev().css("background-color")});
			$(this).parent().find("[imgEditar]").remove();
			$(this).before("<img imgEditar='' onClick='editarObjecion(this)' style='cursor:pointer;width:16px;height:16px;' title='<div style=\"background-color:#FFFFFF;color:#000000;font-weight:normal\">"+$(this).find("textarea").val()+"</div>' src='../../images/medical/hce/mod.PNG'>");
			$(this).parent().find("[imgEditar]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			
			if($(this).find("textarea").val() != "")
				$(this).find("textarea").attr("usuedita", $("#tableDetalleGlosa").attr("usuario"));
		});		
	}
	//-------------------------------------------------------
	//	FUNCION QUE DESPLEGA EL DETALLE POR CENTRO DE COSTOS
	//-------------------------------------------------------
	function verDetalleCco(Elemento, id)
	{
		if(jQuery(Elemento).attr("src") == "../../images/medical/iconos/gifs/i.p.next[1].gif")
			jQuery(Elemento).attr("src", "../../images/medical/iconos/gifs/i.p.previous[1].gif");
		else
			jQuery(Elemento).attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");

		// --> Pintar borde azul
		if($("."+id).is(':hidden'))
		{
			jQuery(Elemento).parent().css({
				'border-left': 		'1px solid #2A5DB0',
				'border-bottom': 	'1px solid #2A5DB0'
			}).next().css({
				'border-bottom': 	'1px solid #2A5DB0'
			});
			jQuery(Elemento).parent().parent().find('td[class]').css({
				'border-top': 		'1px solid #2A5DB0'
			});
			jQuery(Elemento).parent().parent().find('td[class]:last').css({
				'border-right': 	'1px solid #2A5DB0'
			});
		}
		// --> Quitar borde azul
		else
		{
			jQuery(Elemento).parent().css({
				'border-left': 		'',
				'border-bottom': 	''
			}).next().css({
				'border-bottom': 	''
			});
			jQuery(Elemento).parent().parent().find('td[class]').css({
				'border-top': 		''
			});
			jQuery(Elemento).parent().parent().find('td[class]:last').css({
				'border-right': 		''
			});
		}
		
		// --> Colocarle borde azul a los td del paralelo
		$("."+id).find('td[class]:last').css({
			'border-right'	: 		'1px solid #2A5DB0'
		});
		$("."+id).find('td[class]:first').css({
			'border-left'	: 		'1px solid #2A5DB0'
		});
		$("."+id+":last").find('td[class]').css({
			'border-bottom'	: 		'1px solid #2A5DB0'
		});

		// --> Ocultar y mostrar paralelo
		$("."+id).toggle(0);
	}
	//-------------------------------------------------------------------------
	//	Abrir la historia clinica
	//-------------------------------------------------------------------------
	function abrirHce(documento, tipoDoc, historia, ingreso)
	{
		var url 	= "/matrix/HCE/procesos/HCE_Impresion.php?empresa=hce&origen="+$("#wemp_pmla").val()+"&wcedula="+documento+"&wtipodoc="+tipoDoc+"&wdbmhos=movhos&whis="+historia+"&wing="+ingreso+"&wservicio=*&protocolos=0&CLASE=I&BC=1";
		window.open(url,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
	}
	
	function verReporteGlosas()
	{
		ruta = "/matrix/ips/reportes/reporteGlosasObjeciones.php?wemp_pmla="+$("#wemp_pmla").val();
		window.open(ruta,'','fullscreen=no, status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
	}
	
	function checkTodos(elemento)
	{
		if($(elemento).is(':checked'))
			$("[checkParaImp]").attr("checked", "checked");
		else
			$("[checkParaImp]").removeAttr("checked");
	}


//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.bordeRojo{
			border:			1px solid red;
		}
		.bordeRed{
			border-radius: 	4px;
			border:			1px solid #AFAFAF;
		}
		.bordeRed2{
			border-radius: 	4px;
			border:			1px solid #2779AA;
		}
		.fila1
		{
			background-color: 	#C3D9FF;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fila2
		{
			background-color: 	#E8EEF7;
			color: 				#000000;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.encabezadoTabla {
			background-color: 	#2a5db0;
			color: 				#ffffff;
			font-size: 			8pt;
			padding:			1px;
			font-family: 		verdana;
		}
		.fondoAmarillo            
		{
			background-color: 	#FFFFCC;
			color: 				#000000;
			font-size: 			8pt;
		}
		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 11pt;
		}
		button{
			font-family: 	verdana;
			font-weight:	bold;
			font-size: 		10pt;
			cursor:			pointer;
		}
		.ui-autocomplete{
			max-width: 	480px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	6pt;
		}
		
		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 7pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}
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
	encabezado("Registro de glosas y objeciones", $wactualiz, 'clinica');
	
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

	// --> Traer maestro de causas
	$arrayCausas 	= array();
	$sqlCausas 		= "
	SELECT Caucod, Caunom, Caucon
	  FROM ".$wbasedato."_000276
	 WHERE Cauest = 'on'
	";
	$resCausas = mysql_query($sqlCausas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCausas):</b><br>".mysql_error());
	while($rowCausas = mysql_fetch_array($resCausas))
	{
		$arrayCausas[$rowCausas['Caucon']][$rowCausas['Caucod']] = str_replace($caracter_ma, $caracter_ok, utf8_encode(trim($rowCausas['Caunom'])));
	}
	
	// --> Traer responsables de glosas (Medicos Y  Cco)
	$movhos 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
	$arrayRespon 	= array();
	$sqlRespon 		= "
	SELECT Meddoc as Codigo, CONCAT(Medno1, ' ', Medno2, ' ', Medap1, ' ', Medap2) as Nombre
	  FROM ".$movhos."_000048
	 WHERE Medest = 'on'
	 GROUP BY Meddoc
	 UNION
	SELECT Ccocod as Codigo, Cconom as Nombre
	  FROM ".$movhos."_000011
	 WHERE Ccoest = 'on' 
	";
	$resRespon = mysql_query($sqlRespon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRespon):</b><br>".mysql_error());
	while($rowRespon = mysql_fetch_array($resRespon))
	{
		$arrayRespon[$rowRespon['Codigo']] = str_replace($caracter_ma, $caracter_ok, utf8_encode(trim($rowRespon['Nombre'])));
	}
	
	$arr_estadoCartera 				= estadosCarteraFactura($conex, $wbasedato);
	$arrayEstadosFacturaAutocomp 	= $arr_estadoCartera["Autocomp"];
	// --> para validar si un estado de factura requiere o no número de radicado
	$arrayEstadosFacturaReqRad 		= $arr_estadoCartera["ReqRad"]; 

	echo "
	<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
	<input type='hidden' id='idGlosaMostrar' value='".$idGlosaMostrar."'>
	<input type='hidden' id='maestroCausas' value='".json_encode($arrayCausas)."'>
	<input type='hidden' id='maestroRespon' value='".json_encode($arrayRespon)."'>
	<input type='hidden' id='maestroEstadosFactura' value='".json_encode($arrayEstadosFacturaAutocomp)."'>
	<input type='hidden' id='maestroEstadosFacturaReqRad' value='".json_encode($arrayEstadosFacturaReqRad)."'>
	";
	
	echo "
	<div class='ui-tabs ui-widget ui-widget-content ui-corner-all'>
	";
	
	// --> Rol del usuario
	$sqlPermisos = "
	SELECT Rolcod, Roldes, Rolgum
	  FROM ".$wbasedato."_000030 AS A INNER JOIN ".$wbasedato."_000285 AS B ON(A.Cjerrg = B.Rolcod)
	 WHERE A.Cjeusu = '".$wuse."'
	   AND B.Rolest = 'on'
	";
	$resPermisos = mysql_query($sqlPermisos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGlo):</b><br>".mysql_error());
	if($rowPermisos = mysql_fetch_array($resPermisos))
		echo "<input type='hidden' id='rolUsuarioActual' value='".$rowPermisos['Rolcod']."' >";
	else
		echo "<input type='hidden' id='rolUsuarioActual' value='' >";
		
	// --> Consultar roles
	$sqlRoles = "
	SELECT Rolcod, Roldes, Rolfar
	  FROM ".$wbasedato."_000285
	 WHERE Rolest = 'on'
	";
	$resRoles = mysql_query($sqlRoles, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRoles):</b><br>".mysql_error());
	
	echo "
	<div style='padding-left:30px;padding-right:15px;padding-top:15px;font-family: verdana;font-size: 10pt;'>	
		<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>ROLES: &nbsp;</span>";
	while($rowRoles = mysql_fetch_array($resRoles))
	{
		if($rowRoles['Rolfar'] != 'on')
		{
			echo "
			<span botonRol = '".$rowRoles['Rolcod']."' verFarmacia='off' style='padding:3px;cursor:pointer' class='bordeRed ui-state-default ui-corner-top' onclick='listaGlosasRegistradas(\"".$rowRoles['Rolcod']."\", this)'>
				&nbsp;".$rowRoles['Roldes']."&nbsp;
			</span>&nbsp;";
		}
		else
		{		
			echo "
			<span botonRol = '".$rowRoles['Rolcod']."' verFarmacia='off' style='padding:3px;cursor:pointer' class='bordeRed ui-state-default ui-corner-top' onclick='listaGlosasRegistradas(\"".$rowRoles['Rolcod']."\", this)'>
				&nbsp;".$rowRoles['Roldes']."&nbsp;
			</span>&nbsp;";
			
			echo "
			<span botonRol = '".$rowRoles['Rolcod']."' verFarmacia='on' style='padding:3px;cursor:pointer' class='bordeRed ui-state-default ui-corner-top' onclick='listaGlosasRegistradas(\"".$rowRoles['Rolcod']."\", this)'>
				&nbsp;Farmacia&nbsp;
			</span>&nbsp;";
		}
	}	
	echo "
		&nbsp;|&nbsp;<b>Rol usuario:</b> ".((isset($rowPermisos['Roldes'])) ? $rowPermisos['Roldes'] : "<span style='color:red'>Pendiente por configurar!</span>")." 
		&nbsp;|&nbsp;<span style='cursor:pointer' onClick='verReporteGlosas()'><b>Ver reporte</b></span> 
	</div>
	<fieldset align='center' style='padding:15px;margin:15px'>
		<legend class='fieldset'>Glosas registradas</legend>
			<table width='100%'>
				<tr>
					<td align='left'>
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
							Buscar:
							<input id='buscarGlosa' class='bordeRed' placeholder='Digite palabra clave' type='text'>
							&nbsp;
							|
							&nbsp;
							Estado:
							<select id='selectEstadoGLosa' class='bordeRed' onChange='listaGlosasRegistradas(\"\", \"\")'>
								<option value='%'>Todos</option>
								<option value='GL'>Glosada</option>
								<option value='RA'>Auditada</option>
								<option value='GR'>Respondida</option>
								<option value='AP'>Generada(Arc Plano)</option>
								<option value='AN'>Anulada</option>
							<select>							
							&nbsp;
							|
							&nbsp;
							Rango de fecha:&nbsp;
							<input id='fechaBuscar1' class='bordeRed' size='11' type='text' value='".(($rowPermisos['Rolgum'] == 'on') ? date("Y-m-01") : "2017-01-01")."'>
							&nbsp;
							<span style='font-weight:normal'>y</span>
							&nbsp;
							<input id='fechaBuscar2' class='bordeRed' size='11' type='text' value='".date("Y-m-d")."'>
							&nbsp;
							|
							&nbsp;
							N registros:
							<span id='numRegistros' style='font-weight:normal'></span>
						</span>
					</td>
					<td align='right'>
						<button style='font-size:9pt' onclick='nuevoRegistro()'>Nuevo registro</button>
					</td>
				</tr>
			</table>
			<div id='listaDeglosas' style='height:230px;overflow:auto;background:none repeat scroll 0 0'>";
	// $glosasReg = listaGlosasRegistradas();
	// echo $glosasReg["Html"];

	echo "
			</div>
		</fieldset>
	</div>
	<br>
	<div id='accordionGlosa' align='center'>
		<h1 style='font-size: 11pt;' align='left'>&nbsp;&nbsp;&nbsp;Registro de glosa</h1>
		<div align='center' id='contenidoGlosa' style=''>
			<fieldset align='center' style='padding:15px;margin-top:15px;'>
				<legend class='fieldset'>Encabezado de la glosa</legend>
				<div id='encabezadoGlosa'>";
			$regGlosa = encabezadoGlosa();
			echo $regGlosa["Html"];
			echo "
				</div>
			</fieldset><br>
			<fieldset align='center' style='padding:15px;margin-bottom:15px;display:none'>
				<legend class='fieldset'>Justificaci&oacute;n de causas</legend>
				<div id='justificarCausas'></div>
			</fieldset><br>
			<fieldset align='center' style='padding:15px;margin-bottom:15px;display:none'>
				<legend class='fieldset'>Detalle de la glosa</legend>
				<table width='100%'>
					<tr>
						<td align='right' style='font-family: verdana;font-size:9pt;color:#4c4c4c'>
							<span style='display:none'>
								<input type='checkbox' id='checkPorCco' style='cursor:pointer;' onChange='detallarPorCco()'>Ver detalle por cco
								&nbsp;
								<b>|</b>
								&nbsp;
							</span>
							<span id='areaSoloVerGlosas'>
								Solo ver glosas
								<input type='checkbox' id='checkSoloVerGlosas' style='cursor:pointer;' onChange='soloVerGlosas()'>
							</span>
							&nbsp;
							<b>|</b>
							&nbsp;
							Cant. Reclamar por defecto 
							<input type='checkbox' id='checkCantRecla' style='cursor:pointer;' onChange='cantReclamarPorDefecto()'>
							&nbsp;
							<b>|</b>
							&nbsp;
							Val. Aceptado por defecto 
							<input type='checkbox' id='checkValAceptado' style='cursor:pointer;' onChange='valAceptadoPorDefecto()'>
							&nbsp;
							<b>|</b>
							&nbsp;
							<img style='cursor:pointer;' onClick='nuevoRegistro()' title='Cerrar ventana detalle' src='../../images/medical/eliminar1.png'>
							&nbsp;
						</td>
					</tr>
				</table>
				<div id='detalleGlosa'></div>
			</fieldset>
		</div>		
	</div>
	<br>
	<div align='center'>
		<button style='font-size:9pt' onclick='window.close()'>Cerrar Ventana</button>
	<div>
	<div id='imprimirCarta' style='display:none'>
	</div>
	<div id='seleccionarIngreso' style='display:none;font-family: verdana;font-size: 10pt;'>
	</div>
	<br>
	";

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
