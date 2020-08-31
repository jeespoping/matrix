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
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='En Desarrollo, jerson';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//
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
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	include_once("ips/funciones_facturacionERP.php");
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

//------------------------------------------------------------------------------
//	Pinta el detalle de la cuenta de un paciente, es decir los cargos grabados
//------------------------------------------------------------------------------
function pintar_detalle_cuenta($whistoria, $wing)
{
	global $conex;
	global $wbasedato;
	global $wuse;

	$array_paq_cargados = array();

	// --> Obtener si el usuario es administrador
	$qAdminUse = " SELECT Cjeadm
					 FROM ".$wbasedato."_000030
					WHERE Cjeusu = '".$wuse."'
					  AND Cjeest = 'on'
	";
	$ResAdminUse = mysql_query($qAdminUse,$conex) or die("Error en el query: ".$qAdminUse."<br>Tipo Error:".mysql_error());
	if($RowAdminUse = mysql_fetch_array($ResAdminUse))
		$useAdministrador = $RowAdminUse['Cjeadm'];
	else
		$useAdministrador = 'off';

	// --> Obtener los cargos grabados como paquetes para la historia e ingreso
	$q_get_cargos = "
	   SELECT A.id as Registro, Tcarfec, Tcarser, Tcarconcod, Tcarconnom, Tcarprocod, Tcarpronom, Tcartercod, Tcarternom,
			  Tcarcan, Tcarvun, Tcarvto, Tcarrec, Tcarfac, Tcarfex, Tcarfre, Movpaqcod, Paqnom, Codigo, Descripcion, Gruinv,
			  Tcarvex, Tcarvre, Tcarres
		 FROM ".$wbasedato."_000115 as B, ".$wbasedato."_000113, ".$wbasedato."_000106 as A, usuarios, ".$wbasedato."_000200
		WHERE Movpaqhis 	= '".$whistoria."'
		  AND Movpaqing 	= '".$wing."'
		  AND Movpaqest 	= 'on'
		  AND Movpaqcod 	= Paqcod
		  AND Movpaqreg		= A.id
		  AND Tcarest 		= 'on'
		  AND Tcarusu		= Codigo
		  AND Tcarconcod	= Grucod
	";
	$res_get_cargos = mysql_query($q_get_cargos,$conex) or die("Error en el query: ".$q_get_cargos."<br>Tipo Error:".mysql_error());
	$no_existen_car = true;
	while($row_get_cargos = mysql_fetch_array($res_get_cargos))
	{
		$no_existen_car = false;
		// --> Crear un array con la informacion organizada
		$CodPaquete = $row_get_cargos['Movpaqcod'];
		$array_paq_cargados[$CodPaquete]['NomPaq'] = $row_get_cargos['Paqnom'];
		$cargos_paquete['Registro'] 		= $row_get_cargos['Registro'];
		$cargos_paquete['Fecha'] 			= $row_get_cargos['Tcarfec'];
		$cargos_paquete['Servicio'] 		= $row_get_cargos['Tcarser'];
		$cargos_paquete['CodConcepto']		= $row_get_cargos['Tcarconcod'];
		$cargos_paquete['NomConcepto']		= $row_get_cargos['Tcarconnom'];
		$cargos_paquete['CodProcedi']		= $row_get_cargos['Tcarprocod'];
		$cargos_paquete['NomProcedi']		= $row_get_cargos['Tcarpronom'];
		$cargos_paquete['CodTercero']		= $row_get_cargos['Tcartercod'];
		$cargos_paquete['NomTercero']		= $row_get_cargos['Tcarternom'];
		$cargos_paquete['Cantidad']			= $row_get_cargos['Tcarcan'];
		$cargos_paquete['ValorUn']			= $row_get_cargos['Tcarvun'];
		$cargos_paquete['ValorRe']			= $row_get_cargos['Tcarvre'];
		$cargos_paquete['ValorEx']			= $row_get_cargos['Tcarvex'];
		$cargos_paquete['ValorTo']			= $row_get_cargos['Tcarvto'];
		$cargos_paquete['entidad']			= $row_get_cargos['Tcarres'];
		$cargos_paquete['ReconExced']		= $row_get_cargos['Tcarrec'];
		$cargos_paquete['Facturable']		= $row_get_cargos['Tcarfac'];
		$cargos_paquete['Usuario']			= $row_get_cargos['Descripcion'];
		$cargos_paquete['CodUsuario']		= $row_get_cargos['Codigo'];
		$cargos_paquete['FactuExcede']		= $row_get_cargos['Tcarfex'];
		$cargos_paquete['FacturadoReconoci']= $row_get_cargos['Tcarfre'];
		$cargos_paquete['ConceptoInventar']	= $row_get_cargos['Gruinv'];
		$cargos_paquete['Facturado'] 		= (($row_get_cargos['Tcarfex'] == 0 && $row_get_cargos['Tcarfre'] == 0) ? 'no_facturado' : 'facturado');

		$array_paq_cargados[$CodPaquete]['Cargos'][] = $cargos_paquete;
	}

	// --> Pintar informacion
	foreach($array_paq_cargados as $CodPaquete => $DetPaquete)
	{
		$NomPaquete = $DetPaquete['NomPaq'];

		echo "
		<table width='100%' id='tablaDetalleCuenta'>
			<tr>
				<td class='fondoAmarillo' style='border: 1px solid #999999;'  colspan='17'>&nbsp;&nbsp; ".$CodPaquete."-".$NomPaquete."</td>
			</tr>
			<tr class='encabezadoTabla'>
				<td style='background-color: #F2F5F7;' width='2%'></td>
				<td>Fecha</td>
				<td>Concepto</td>
				<td>Procedimiento</td>
				<td>C.Costos</td>
				<td>Tercero</td>
				<td>Rec/Exc</td>
				<td>Fact.</td>
				<td>Cantidad</td>
				<td>Valor. Uni</td>
				<td>Valor. Rec</td>
				<td>Valor. Exc</td>
				<td>Valor. Tot</td>
				<td>Entidad</td>
				<td>Usuario resp.</td>
				<td>Reg.</td>
				<!--<td>Anular</td>-->
			</tr>";

		$ColorFila 		= 'fila1';
		$TotCantidades 	= 0;
		$TotValorTot 	= 0;
		foreach($DetPaquete['Cargos'] as $variables)
		{
			if($ColorFila == 'fila1')
				$ColorFila = 'fila2';
			else
				$ColorFila = 'fila1';

			echo"
			<tr class='".$ColorFila." filaDetalle'>
				<td style='background-color: #F2F5F7;' width='2%'></td>
				<td>".$variables['Fecha']."</td>
				<td>".$variables['CodConcepto']."-".$variables['NomConcepto']."</td>
				<td>".$variables['CodProcedi']."-".$variables['NomProcedi']."</td>
				<td>".$variables['Servicio']."</td>
				<td>".$variables['CodTercero']."-".$variables['NomTercero']."</td>
				<td align='center'>".$variables['ReconExced']."</td>
				<td align='center'>".$variables['Facturable']."</td>
				<td>".$variables['Cantidad']."</td>
				<td>".$variables['ValorUn']."</td>
				<td>".number_format($variables['ValorRe'],0,',','.' )."</td>
				<td>".number_format($variables['ValorEx'],0,',','.' )."</td>
				<td>".number_format($variables['ValorTo'],0,',','.' )."</td>
				<td>".utf8_encode($variables['entidad'])."</td>
				<td>".$variables['Usuario']."</td>
				<td>".$variables['Registro']."</td>";

			/*echo"
				<td align='center'>";

				// --> Se permite anular si, El valor del excedente es igual a 0, El valor reconocido es igual 0, Es un concepto
				//	   que no mueve inventario y no ha sido facturado el cargo, y es un usuario administrador o el usuario es el
				//	   mismo que grabo el cargo.
				if($variables['FactuExcede'] == 0 && $variables['FacturadoReconoci'] == 0 && $variables['ConceptoInventar'] != 'on' && $variables['Facturado'] == 'no_facturado' && ($useAdministrador == 'on' || $variables['CodUsuario'] == $wuse))
					echo '<img src="../../images/medical/eliminar1.png" title="Anular" onclick="anular(\''.$variables['Registro'].'\')" style="cursor:pointer;">';*/

			echo"
				</td>
			</tr>
			";

			$TotValorRec+= 		(($variables['Facturable'] == 'S') ? $variables['ValorRe'] : 0);
			$TotValorExc+= 		(($variables['Facturable'] == 'S') ? $variables['ValorEx'] : 0);
		}

		echo"
			<tr>
				<td colspan='8'></td>
				<td class='encabezadoTabla' colspan='2'>TOTALES:</td>
				<td class='encabezadoTabla'>$".number_format($TotValorRec,0,',','.' )."</td>
				<td class='encabezadoTabla'>$".number_format($TotValorExc,0,',','.' )."</td>
				<td class='encabezadoTabla'>$".number_format($TotValorRec+$TotValorExc,0,',','.' )."</td>
			</tr>
		</table><br>
		";
	}
	if($no_existen_car)
	{
		echo "No existen paquetes grabados en la cuenta del paciente.";
	}
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
		case 'CargarElementosDelPaquete':
		{
			$wbasedatoMovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			$html = "
			<table width='80%' id='liquidacionPaquete'>
				<tr id='tr_enc_det_concepto'>
					<td class='encabezadoTabla'>Hora</td>
					<td class='encabezadoTabla'>Concepto</td>
					<td class='encabezadoTabla'> CCosto</td>
					<td class='encabezadoTabla'>Procedimiento o Insumo</td>
					<td class='encabezadoTabla'>Tercero</td>
					<td class='encabezadoTabla'>Especialidad</td>
					<td class='encabezadoTabla'>Cant.</td>
					<td class='encabezadoTabla'>Valor Un.</td>
					<td class='encabezadoTabla' nowrap='nowrap'>Valor Total</td>
					<td class='encabezadoTabla'>Rec/Exc</td>
					<td class='encabezadoTabla'>Fact (S/N):</td>
					<td class='encabezadoTabla'>Grabar</td>
				</tr>";

			// --> Consulto si el paquete tiene una configuracion especifica para la tarifa
			$q_paquete1 = "
			   SELECT  Grucod, Grudes, Gruarc, Gruser, Grutip, Grumva, Gruinv, Gruabo, Grutab,
					   Grumca, Pronom, Paqdetcan, Paqdetvan, Paqdetvac, Paqdetfec, Paqdetpro, Paqdetter, Paqdetpai, Paqdetfac
				 FROM  ".$wbasedato."_000114, ".$wbasedato."_000200, ".$wbasedato."_000103
				WHERE  Paqdetcod		= '".$CodPaquete."'
				  AND  TRIM(paqdettar)  = '".$CodigoTarifa."'
				  AND  Paqdetest		= 'on'
				  AND  Paqdetcon		= Grucod
				  AND  Paqdetpro		= Procod
				  AND  (Grutpr = '*' OR Grutpr = Protip)
			    UNION
			   SELECT  Grucod, Grudes, Gruarc, Gruser, Grutip, Grumva, Gruinv, Gruabo, Grutab,
					   Grumca, '', Paqdetcan, Paqdetvan, Paqdetvac, Paqdetfec, Paqdetpro, Paqdetter, Paqdetpai, Paqdetfac
				 FROM  ".$wbasedato."_000114, ".$wbasedato."_000200, ".$wbasedato."_000103
				WHERE  Paqdetcod		= '".$CodPaquete."'
				  AND  TRIM(paqdettar)  = '".$CodigoTarifa."'
				  AND  Paqdetest		= 'on'
				  AND  Paqdetpai		= 'on'
				  AND  Paqdetcon		= Grucod
				  AND  (Grutpr = '*' OR Grutpr = Protip)
				  AND  Gruinv			= 'on'
			 ORDER BY  Grudes
			";
			$res_paquete 	= mysql_query($q_paquete1,$conex) or die("Error en el query: ".$q_paquete1."<br>Tipo Error:".mysql_error());
			$num_detalles 	= mysql_num_rows($res_paquete);

			// --> Si no existe un detalle para la tarifa, entonces traigo el detalle de la tarifa general (*).
			if($num_detalles < 1)
			{
				$q_paquete2 = "
				   SELECT  Grucod, Grudes, Gruarc, Gruser, Grutip, Grumva, Gruinv, Gruabo, Grutab,
						   Grumca, Pronom, Paqdetcan, Paqdetvan, Paqdetvac, Paqdetfec, Paqdetpro, Paqdetter, Paqdetpai, Paqdetfac
					 FROM  ".$wbasedato."_000114, ".$wbasedato."_000200, ".$wbasedato."_000103
					WHERE  Paqdetcod		= '".$CodPaquete."'
					  AND  TRIM(Paqdettar)  = '*'
					  AND  Paqdetest		= 'on'
					  AND  Paqdetcon		= Grucod
					  AND  Paqdetpro		= Procod
					  AND  (Grutpr = '*' OR Grutpr = Protip)
				    UNION
				   SELECT  Grucod, Grudes, Gruarc, Gruser, Grutip, Grumva, Gruinv, Gruabo, Grutab,
						   Grumca, '', Paqdetcan, Paqdetvan, Paqdetvac, Paqdetfec, Paqdetpro, Paqdetter, Paqdetpai, Paqdetfac
					 FROM  ".$wbasedato."_000114, ".$wbasedato."_000200, ".$wbasedato."_000103
					WHERE  Paqdetcod		= '".$CodPaquete."'
					  AND  TRIM(Paqdettar)  = '*'
					  AND  Paqdetest		= 'on'
					  AND  Paqdetpai		= 'on'
					  AND  Paqdetcon		= Grucod
					  AND  (Grutpr = '*' OR Grutpr = Protip)
					  AND  Gruinv			= 'on'
				 ORDER BY  Grudes
				";
				$res_paquete 	= mysql_query($q_paquete2,$conex) or die("Error en el query: ".$q_paquete2."<br>Tipo Error:".mysql_error());
			}

			$ColorFila 		= 'fila1';
			$consecutivo	= 1;
			$ConDetalle		= false;
			while($row_paquete = mysql_fetch_array($res_paquete))
			{
				$ConDetalle = true;
				// --> Si el detalle del paquete esta relacionado con un paquete de articulos de inventario.
				//	   Esto se da cuando el concepto es de inventarios (medicamentos y material).
				if($row_paquete['Paqdetpai'] == 'on')
				{
					// --> Obtener el detalle de los articulos relacionados
					$qDetArt = " SELECT Dincom, Dincan, Artcom, Artcod
								   FROM ".$wbasedato."_000191, ".$wbasedatoMovhos."_000026
								  WHERE Dincop = '".str_replace('PI-', '', $row_paquete['Paqdetpro'])."'
								    AND Dinest = 'on'
									AND Dincom = Artcod
									AND Artest = 'on'
					";
					$res_DetArt = mysql_query($qDetArt,$conex) or die("Error en el query: ".$qDetArt."<br>Tipo Error:".mysql_error());
					while($rowDetArt = mysql_fetch_array($res_DetArt))
					{
						// --> Obtener el valor del articulo
						$datosValTarifaMed = 	datos_desde_procedimiento($rowDetArt['Artcod'],  $row_paquete['Grucod'], $ccoFacturador, $ccoUbiActualPac, $CodigoResponsable, $FechaCargo, $tipoIngreso);
						if(!$datosValTarifaMed['error'])
						{
							$wvaltar 		= $datosValTarifaMed['wvaltar'];
							$existencia		= $data['wexiste'];
							$imgTar			= '<img width="15" height="15" src="../../images/medical/root/grabar.png">';
						}
						else
						{
							$wvaltar 	= 0;
							$imgTar 	= '<img width="15" height="15" tooltip="si" title="'.$datosValTarifaMed['mensaje'].'" src="../../images/medical/sgc/Warning-32.png">';
						}
						if($ColorFila == 'fila1')
							$ColorFila = 'fila2';
						else
							$ColorFila = 'fila1';

						$html.= "
							<tr class='".$ColorFila." cargo_cargo' consecutivo='".$consecutivo."'>
								<td align='left' >
									<input type='text' id='whora_cargo_".$consecutivo."' value='".date("H:i")."' size='6' >
								</td>
								<td>
									<input type='text' style='font-size: 8pt;' size='32' disabled='disabled' id ='busc_concepto_".$consecutivo."' valor='".$row_paquete['Grucod']."'   value='".$row_paquete['Grudes']."'>
								</td>
								<td>
									<select id='wccogra_".$consecutivo."' onchange='datos_desde_conceptoxcco(\"".$consecutivo."\", true)'>
										".cargar_cco($row_paquete['Grucod'], $CodigoResponsable, $row_paquete['Gruabo'], $CantCco, $ccoUbiActualPac)."
									</select>
								</td>
								<td>
									<input type='text' style='font-size: 8pt;' size='32' disabled='disabled' id='busc_procedimiento_".$consecutivo."' valor='".$row_paquete['Paqdetpro']."'   value='".$rowDetArt['Artcom']."'>
								</td>
								<td >
									<div id='div_tercero_".$consecutivo."'>
										<input type='text' size='30' style='font-size: 8pt;' id='busc_terceros_".$consecutivo."' valor='".$row_paquete['Paqdetter']."' ".(($row_paquete['Grutip']!='C') ? 'style="display:none"' : '').">
									</div>
								</td>
								<td>
									<Select  id='busc_especialidades_".$consecutivo."' ".(($row_paquete['Grutip']!='C') ? 'style="display:none"' : "")." style='font-size: 8pt;'>
										<option value='' selected>Seleccione..</option>
									</Select>
								</td>";
								/*
								<td align='center'>
									<input type='checkbox' id='waprovecha_".$consecutivo."' ".(($aprovechamiento != 'on') ? 'style="display:none;"' : '').">
								</td>
								*/
							$html.= "
								<td>
									<input id='wcantidad_".$consecutivo."' type='text' value='".$rowDetArt['Dincan']."' style='text-align: center' size='3' onchange='actualiza_valor_total(\"".$consecutivo."\")'>
								</td>
								<td style='font-family: Lucida Console'>
									$".number_format($wvaltar, 0, ',', '.')."
									<input id='wvaltar_".$consecutivo."' type='hidden' disabled='disabled' value='".$wvaltar."' style='text-align: center' size='8'>
								</td>
								<td>
									<div id='div_valor_total_".$consecutivo."' style='font-family: Lucida Console'>
										$".number_format($wvaltar*$rowDetArt['Dincan'], 0, ',', '.')."
									</div>
								</td>
								<td align='center'>
									<input class='recExce' id='wrecexc_".$consecutivo."' type='text'  maxlength='1' value='R' size='1' disabled='disabled'>
								</td>
								<td align='center'>
									<SELECT class='facturableSN' id='wfacturable_".$consecutivo."' disabled='disabled'>
										<option value='S' ".(($row_paquete['Paqdetfac'] == 'S') ? 'SELECTED' : '' ).">SI</option>
										<option value='N' ".(($row_paquete['Paqdetfac'] == 'N') ? 'SELECTED' : '' ).">NO</option>
									</SELECT>
								</td>
								<td align='center'>";
								if(!$datosValTarifaMed['error'])
								{
									$imgTar	= (($CantCco>0) ? $imgTar : '<img width="15" height="15" tooltip="si" title="No existe relación concepto-cco" src="../../images/medical/sgc/Warning-32.png">');
									$html.= $imgTar;
									$html.= "<input type='checkbox' id='wgrabable_".$consecutivo."' style='display:none' ".(($CantCco>0 ) ? 'checked="checked" ' : '').">";
								}
								else
								{
									$html.= $imgTar;
									$html.= "<input type='checkbox' id='wgrabable_".$consecutivo."' style='display:none'>";
								}
								$html.= "
									<input type='hidden' id='wporter_".$consecutivo."' >
									<input type='hidden' id='wcontip_".$consecutivo."' value='".$row_paquete['Grutip']."'>
									<input type='hidden' id='wconinv_".$consecutivo."' value='".$row_paquete['Gruinv']."'>
									<input type='hidden' id='wconabo_".$consecutivo."' value='".$row_paquete['Gruabo']."'>
									<input type='hidden' id='wconser_".$consecutivo."' value='".$row_paquete['Gruser']."'>
									<input type='hidden' id='wtipfac_".$consecutivo."' value='PAQUETE'>
									<!--OJO PENDIENTE DE REVISAR, SE DEBE OBTENER LA EXISTENCIA de la 0000001 CUANDO SEA UN ARTICULO-->
									<input type='hidden' id='wexiste_".$consecutivo."' value= '".$existencia."'>
								</td>
							</tr>";
						$consecutivo++;
					}
				}
				// --> Muestro el detalle tal cual como esta
				else
				{
					// --> Aca evaluo si tomo el valor anterior o el actual
					if (strtotime($FechaCargo) < strtotime($row_paquete['Paqdetfec']))
					{
						$wvaltar = $row_paquete['Paqdetvan'];
					}
					else
						$wvaltar = $row_paquete['Paqdetvac'];

					// --> Si es un concepto de inventario, debo obtener si el articulo maneja aprovechamiento.
					/*if($row_paquete['Grumva'] == 'on')
					{
						$q_aprove = "
							SELECT Artapv
							  FROM ".$wbasedato."_000001
							 WHERE Artcod = '".$row_paquete['Paqdetpro']."'";

						$res_aprove = mysql_query($q_aprove,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_aprove." - ".mysql_error());
						if($row_aprove = mysql_fetch_array($res_aprove))
							$aprovechamiento = $row_aprove['Artapv'];
						else
							$aprovechamiento = 'off';
					}
					else
						$aprovechamiento = 'off';*/


					if($ColorFila == 'fila1')
						$ColorFila = 'fila2';
					else
						$ColorFila = 'fila1';

					$html.= "
						<tr class='".$ColorFila." cargo_cargo' consecutivo='".$consecutivo."'>
							<td align='left' >
								<input type='text' id='whora_cargo_".$consecutivo."' value='".date("H:i")."' size='6' >
							</td>
							<td>
								<input type='text' style='font-size: 8pt;' size='32' disabled='disabled' id ='busc_concepto_".$consecutivo."' valor='".$row_paquete['Grucod']."'   value='".$row_paquete['Grudes']."'>
							</td>
							<td>
								<select id='wccogra_".$consecutivo."' onchange='datos_desde_conceptoxcco(\"".$consecutivo."\", true)'>
									".cargar_cco($row_paquete['Grucod'], $CodigoResponsable, $row_paquete['Gruabo'], $CantCco, $ccoUbiActualPac)."
								</select>
							</td>";
					$imgTar	= (($CantCco>0) ? '<img width="15" height="15" src="../../images/medical/root/grabar.png">' : '<img width="15" height="15" tooltip="si" title="No existe relación concepto-cco" src="../../images/medical/sgc/Warning-32.png">');
					$html.="
							<td>
								<input type='text' style='font-size: 8pt;' size='32' disabled='disabled' id='busc_procedimiento_".$consecutivo."' valor='".$row_paquete['Paqdetpro']."'   value='".$row_paquete['Pronom']."'>
							</td>
							<td >
								<div id='div_tercero_".$consecutivo."'>
									<input type='text' size='30' style='font-size: 8pt;' id='busc_terceros_".$consecutivo."' valor='".$row_paquete['Paqdetter']."' ".(($row_paquete['Grutip']!='C') ? 'style="display:none"' : '').">
								</div>
							</td>
							<td>
								<Select  id='busc_especialidades_".$consecutivo."' ".(($row_paquete['Grutip']!='C') ? 'style="display:none"' : "")." style='font-size: 8pt;'>
									<option value='' selected>Seleccione..</option>
								</Select>
							</td>";
							/*
							<td align='center'>
								<input type='checkbox' id='waprovecha_".$consecutivo."' ".(($aprovechamiento != 'on') ? 'style="display:none;"' : '').">
							</td>
							*/
					$html.= "
							<td>
								<input id='wcantidad_".$consecutivo."' type='text' disabled='disabled' value='".$row_paquete['Paqdetcan']."' style='text-align: center' size='3' >
							</td>
							<td style='font-family: Lucida Console'>
								$".number_format($wvaltar, 0, ',', '.')."
								<input id='wvaltar_".$consecutivo."' type='hidden' disabled='disabled' value='".$wvaltar."' style='text-align: center' size='8'>
							</td>
							<td>
								<div id='div_valor_total_".$consecutivo."' style='font-family: Lucida Console'>
									$".number_format($wvaltar*$row_paquete['Paqdetcan'], 0, ',', '.')."
								</div>
							</td>
							<td align='center'>
								<input class='recExce' id='wrecexc_".$consecutivo."' type='text'  maxlength='1' value='R' size='1' disabled='disabled'>
							</td>
							<td align='center'>
								<SELECT class='facturableSN' id='wfacturable_".$consecutivo."' disabled='disabled'>
									<option value='S' ".(($row_paquete['Paqdetfac'] == 'S') ? 'SELECTED' : '' ).">SI</option>
									<option value='N' ".(($row_paquete['Paqdetfac'] == 'N') ? 'SELECTED' : '' ).">NO</option>
								</SELECT>
							</td>
							<td align='center'>
								".$imgTar."
								<input type='checkbox' id='wgrabable_".$consecutivo."' style='display:none' ".(($CantCco>0) ? ' checked="checked" ' : '').">
								<input type='hidden' id='wporter_".$consecutivo."' >
								<input type='hidden' id='wcontip_".$consecutivo."' value='".$row_paquete['Grutip']."'>
								<input type='hidden' id='wconinv_".$consecutivo."' value='".$row_paquete['Gruinv']."'>
								<input type='hidden' id='wconabo_".$consecutivo."' value='".$row_paquete['Gruabo']."'>
								<input type='hidden' id='wconser_".$consecutivo."' value='".$row_paquete['Gruser']."'>
								<input type='hidden' id='wtipfac_".$consecutivo."' value='PAQUETE'>
								<!--OJO PENDIENTE DE REVISAR, SE DEBE OBTENER LA EXISTENCIA de la 0000001 CUANDO SEA UN ARTICULO-->
								<input type='hidden' id='wexiste_".$consecutivo."' value= '0'>
							</td>
						</tr>";
					$consecutivo++;
				}
			}

			if($ConDetalle)
			{
				$html.="
						<tr>
							<td align='center' colspan='14'>
								<br>
								<button style='font-family: verdana;font-weight:bold;font-size: 10pt;width:120px;' onclick='grabar(this)'>GRABAR</button>
							</td>
						</tr>
				";
			}
			else
			{
				$html = "
				<table width='100%'>
					<tr class='fondoAmarillo'>
						<td align='center'>
							¡¡¡ Este paquete no tiene ninguna configuración para la tarifa del paciente !!!
						</td>
					</tr>
				";
			}

			$respuesta['html'] 		= $html."</table>";
			$respuesta['html'] 		= $html."
			<input type='hidden' id='cantidadElementosPaquete' value='".($consecutivo-1)."'>
			<input type='hidden' id='hiddenCodPaquete' value='".$CodPaquete."'>
			<input type='hidden' id='hiddenNomPaquete' value='".$NomPaquete."'>
			";

			$respuesta['cantidad'] 	= $consecutivo-1;
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'GrabarCargo':
		{
			$wfecha=date("Y-m-d");
			$whora = date("H:i:s");
			$datos=array();
			$datos['whistoria']				=$whistoria;
			$datos['wing']					=$wing;
			$datos['wno1']					=$wno1;
			$datos['wno2']					=$wno2;
			$datos['wap1']					=$wap1;
			$datos['wap2']					=$wap2;
			$datos['wdoc']					=$wdoc;
			$datos['wcodemp']				=$wcodemp;
			$datos['wnomemp']				=$wnomemp;
			$datos['tipoEmpresa']			=$tipoEmpresa;
			$datos['nitEmpresa']			=$nitEmpresa;
			$datos['tipoPaciente']			=$tipoPaciente;
			$datos['tipoIngreso']			=$tipoIngreso;
			$datos['wser']					=$wser;
			$datos['wfecing']				=$wfecing;
			$datos['wtar']					=$wtar;
			$datos['wcodcon']				=$wcodcon;
			$datos['wnomcon']				=$wnomcon;
			$datos['wprocod']				=$wprocod;
			$datos['wpronom']				=$wpronom;
			$datos['wcodter']				=$wcodter;
			$datos['wnomter']				=$wnomter;
			$datos['wporter']				=$wporter;
			$datos['wcantidad']				=$wcantidad;
			$datos['wvaltar']				=$wvaltar;
			$datos['wrecexc']				=$wrecexc;
			$datos['wfacturable']			=$wfacturable;
			$datos['wcco']					=$wcco;
			$datos['wccogra']				=$wccogra;
			$datos['wfeccar']				=$wfeccar;
			$datos['whora_cargo']			=$whora_cargo.':00';
			$datos['wconinv']				=$wconinv;
			$datos['wcodpaq']				=$wcodpaq;
			$datos['wpaquete']				=$wpaquete;
			$datos['consecGrabacionPaq']	=$consecGrabacionPaq;
			$datos['wconabo']				=$wconabo;
			$datos['wdevol']				=$wdevol;
			$datos['waprovecha']			=$waprovecha;
			$datos['wconmvto']				=$wconmvto;
			$datos['wexiste']				=$wexiste;
			$datos['wbod']					=$wbod;
			$datos['wconser']				=$wconser;
			$datos['wtipfac']				=$wtipfac;
			$datos['wexidev']				=$wexidev;
			$datos['wfecha']				=$wfecha;
			$datos['whora']					=$whora;
			$datos['nomCajero']				=$nomCajero;
			$datos['cobraHonorarios']		= '*';
			$datos['wespecialdiad']			=$wespecialdiad;
			$datos['wgraba_varios_terceros']=$wgraba_varios_terceros;
			$datos['wcodcedula']			=$wcodcedula;

			// --> Validar politicas de paquetes
			if($datos['wfacturable'] == 'S')
			{
				// --> 	Armar array con las variables, para enviarlo a la funcion que me genera el query con todas las combinaciones posibles
				//		para asi obtener cual es la politica de mayor prioridad que le aplica.
				$variables = array();

					// --> Paquete
					$variables['Polccp']['combinar'] = false;
					$variables['Polccp']['valor'] 	 = $wcodpaq;
					// --> Tipo de empresa
					$variables['Poltem']['combinar'] = true;
					$variables['Poltem']['valor'] 	 = $tipoEmpresa;
					// --> Tarifa
					$variables['Poltar']['combinar'] = true;
					$variables['Poltar']['valor'] 	 = $wtar;
					// --> Nit entidad
					$variables['Polnen']['combinar'] = true;
					$variables['Polnen']['valor'] 	 = $nitEmpresa;
					// --> Codigo entidad
					$variables['Polcen']['combinar'] = true;
					$variables['Polcen']['valor'] 	 = $wcodemp;
					// --> Centro de costos
					$variables['Polcco']['combinar'] = true;
					$variables['Polcco']['valor'] 	 = $wccogra;
					// --> 	Solo las activas y solo las que sean tipo paquete y tengan configurado algun porcentaje de cobro
					$variables['otra']['combinar'] 	= false;
					$variables['otra']['valor']    	= " 	 Polest = 'on'
														 AND Polfac = 'on'
														 AND Poltpa = 'on'
														 AND Polpcp != '' ";
					$variables['otra']['SQL']		= true;

				// --> Obtener query
				$q_politicas 	= generarQueryCombinado($variables, $wbasedato."_000155");
				$res_idPolitica = mysql_query($q_politicas, $conex) or die("Error en el query: ".$q_politicas."<br>Tipo Error:".mysql_error());

				// --> El query puede que retorne varios resultados, pero solo se tomara el primero ya que este tiene un order by
				//	   por prioridad, donde el primer resultado del query es el de mayor importancia y por ende el que se debe tener
				// 	   en cuenta.
				if($row_idPolitica = mysql_fetch_array($res_idPolitica))
				{
					// --> Consultar la politica de porcentajes de cobro segun el id devulto por el query anterior.
					$infoPoliticas = "SELECT Polpcp
										FROM ".$wbasedato."_000155
									   WHERE id = '".$row_idPolitica['id']."'
									";
					$res_infoPoliticas 	= mysql_query($infoPoliticas, $conex) or die("Error en el query: ".$infoPoliticas."<br>Tipo Error:".mysql_error());
					$row_politicas 		= mysql_fetch_array($res_infoPoliticas);

					$arrPorcentajesCobro = explode('|', $row_politicas['Polpcp']);
					// --> Si el consecutivo de grabacion del paquete tiene asigando un correspondiente % de cobro
					if(array_key_exists($consecGrabacionPaq-1, $arrPorcentajesCobro))
						$porcenCobro = $arrPorcentajesCobro[$consecGrabacionPaq-1];
					// --> EL porcentaje de cobro sera el del ultimo que exista.
					else
						$porcenCobro = array_pop($arrPorcentajesCobro);
				}
				else
					$porcenCobro = 100;
			}
			$datos['porcenCobroPaquete'] 	= $porcenCobro;
			$datos['wvaltar']				= ($datos['wvaltar']*$porcenCobro)/100;

			// --> Si la empresa es particular esto se graba como excedente
			$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
			if($wcodemp == $codEmpParticular)
				$datos['wrecexc'] = 'E';

			// --> Valor excedente
			if($datos['wrecexc'] == 'E')
				$datos['wvaltarExce'] = round($wcantidad*$wvaltar);
			// --> Valor reconocido
			else
				$datos['wvaltarReco'] = round($wcantidad*$wvaltar);

			// --> Se llama a la funcion que realiza la grabacion del cargo
			$idCargo = '';
			$respuesta['Mensajes'] 		 = GrabarCargo($datos, $idCargo, true);
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'traer_terceros':
		{
			echo json_encode(obtener_array_terceros_especialidad());
			break;
			return;
		}
		case 'datos_desde_tercero':
		{
			$data = datos_desde_tercero($wcodter,$wcodesp,$wcodcon,$wtip_paciente,$whora_cargo,$wfecha_cargo,$wtipoempresa,$wtarifa,$wempresa,$wcco,$wcod_procedimiento);
			echo json_encode($data);
			break;
			return;
		}
		case 'datos_desde_conceptoxcco':
		{
			$data = datos_desde_conceptoxcco($wcodcon,$wccogra);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintar_detalle_cuenta':
		{
			echo pintar_detalle_cuenta($whistoria, $wing);
			break;
			return;
		}
		case 'anular':
		{
			$respuesta = anular($wid);
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'cargar_datos_paciente':
		{
			$data = cargar_datos($whistoria, $wing, $wcargos_sin_facturar, $welemento);
			echo json_encode($data);
			break;
			return;
		}
		case 'ObtenerConsecutivoPaquete':
		{
			// -->  Obtener el consecutivo de grabacion, es decir el numero de veces en que se ha grabado
			//		el mismo paquete para la misma historia e ingreso.
			$qConsecu = " SELECT MAX(Movpaqcga) as consecutivo
							FROM ".$wbasedato."_000115
						   WHERE Movpaqhis = '".$whistoria."'
							 AND Movpaqing = '".$wing."'
							 AND Movpaqcod = '".$wcodpaq."'
							 AND Movpaqest = 'on' ";
			$resConsecu = mysql_query($qConsecu, $conex) or die("Error en el query: ".$qConsecu."<br>Tipo Error:".mysql_error());
			$rowConsecu = mysql_fetch_array($resConsecu);
			if($rowConsecu['consecutivo'] == '')
				$Consecutivo = 1;
			else
				$Consecutivo = ($rowConsecu['consecutivo']*1)+1;

			echo $Consecutivo;
			break;
			return;
		}
		case 'congelarCuentaPaciente':
		{
			congelarCuentaPaciente($historia, $ingreso, 'PA', $congelar);
			break;
		}
		case 'estadoCuentaCongelada':
		{
			$infoEncabezado = estadoCongelacionCuentaPaciente($historia, $ingreso);

			// --> Si hay un encabezado
			if($infoEncabezado['hayEncabezado'])
				$infoEncabezado = $infoEncabezado['valores'];
			else
				$infoEncabezado['Ecoest'] = 'off';

			$infoEncabezado['wuse'] = $wuse;
			echo json_encode($infoEncabezado);
			break;
		}
		case 'guardarEnTemporal':
		{
			$sqlTemporal = "UPDATE 	".$wbasedato."_000160
							   SET  Ecotem = '".$html."'
							 WHERE 	Ecohis = '".$historia."'
							   AND  Ecoing = '".$ingreso."'
							   AND  Ecotip = 'PA'
							   AND  Ecoest = 'on' ";
			mysql_query($sqlTemporal, $conex) or die("<b>ERROR EN QUERY MATRIX 2:</b><br>".mysql_error());
			break;
		}
		case 'obtenerLiquidacionTemporal':
		{
			$sqlObtTemp = " SELECT  Ecotem
							  FROM 	".$wbasedato."_000160
							 WHERE 	Ecohis = '".$historia."'
							   AND  Ecoing = '".$ingreso."'
							   AND  Ecotip = 'PA' ";
			$resObtTemp = mysql_query($sqlObtTemp, $conex) or die("<b>ERROR EN QUERY MATRIX 2:</b><br>".mysql_error());
			if($rowObtTemp = mysql_fetch_array($resObtTemp))
				$html = $rowObtTemp['Ecotem'];
			else
				$html = '';

			echo $html;
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
	  <title>...</title>
	</head>

		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<link rel="stylesheet" href="../../../include/ips/facturacionERP.css" />

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	$(document).ready(function() {

		$( "#accordionDatosPaciente" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
		$( "#accordionContenido" ).accordion({
			collapsible: true,
			heightStyle: "content",
			active: -1
		});
		$( "#accordionDetCuenta" ).accordion({
			collapsible: true,
			heightStyle: "content",
			active: 1
		});

		crear_autocomplete('hidden_paquetes', 'SI', 'busc_paquete', 'CargarElementosDelPaquete');
		// --> cargar datapicker
		cargar_elementos_datapicker();
		$("#wfeccar").datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			maxDate:"+0D"
		});

		cargarDatosPaciente("whistoria");

		validarEstadoDeCuentaCongelada(false);

	});

	function borderojo(Elemento)
	{
		Elemento.parent().css("border","2px dotted #FF0400").attr('borderred','si');
	}

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

	//------------------------------------------------------------------------------------------------------
	//	Funcion general que recive un array o el id de un hidden Json y carga un autocomplete en un iput
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function crear_autocomplete(HiddenArray, TipoHidden, CampoCargar, AccionSelect, CampoProcedimiento)
	{
		if(TipoHidden == 'SI')
			var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		else
			var ArrayValores  = eval('(' + HiddenArray + ')');

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+'-'+ArrayValores[CodVal];
			ArraySource[index].name   = ArrayValores[CodVal];
		}

		CampoCargar = CampoCargar.split('|');
		$.each( CampoCargar, function(key, value){
			$( "#"+value ).autocomplete({
				minLength: 	0,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+value ).val(ui.item.name);
					$( "#"+value ).attr('valor', ui.item.value);
					$( "#"+value ).attr('nombre', ui.item.name);
					switch(AccionSelect)
					{
						case 'CargarElementosDelPaquete':
						{
							// --> validar historia
							if($("#whistoriaLocal").val() == '')
							{
								mostrar_mensaje('Para cargar un paquete primero debe<br> ingresar un numero de historia e ingreso.');
								$( "#"+value ).val('');
								return false;
							}
							else
							{
								$("#div_mensajes").hide();
							}
							CargarElementosDelPaquete(ui.item.value);
							validarEstadoDeCuentaCongelada(true);
							return false;
						}
					}
					return false;
				}
			});
			limpiaAutocomplete(value);
		});
	}

	//----------------------------------------------------------------------------------
	//	Controlar que el input no quede con basura, sino solo con un valor seleccionado
	//----------------------------------------------------------------------------------
	function limpiaAutocomplete(idInput)
	{
		$( "#"+idInput ).on({
		focusout: function(e) {
				if($(this).val().replace(/ /gi, "") == '')
				{
					$(this).val("");
					$(this).attr("valor","");
					$(this).attr("nombre","");
				}
				else
				{
					$(this).val($(this).attr("nombre"));
				}
			}
		});
	}

	//-------------------------------------------------------------------------------------
	//	Segun el tercero trae sus especialidades y las carga en el select especialidaes
	//-------------------------------------------------------------------------------------
	function datos_desde_tercero(codigo_tercero,n)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'datos_desde_tercero',
			wcodter:		   	codigo_tercero,
			wcodesp:		   	$('#busc_especialidades_'+n).val(),
			wcodcon:		   	$('#busc_concepto_'+n).attr('valor'),
			wtip_paciente:	   	$('#wtip_paciente_tal').val(),
			whora_cargo:	   	$('#whora_cargo_'+n).val(),
			wfecha_cargo:      	$('#wfeccar').val(),
			wtipoempresa:		$("#tipoEmpresa_tal").val(),
			wtarifa:			$("#tarifa_original_tal").val(),
			wempresa:			$("#responsable_original_tal").val(),
			wcco:				$("#wccogra_"+n).val(),
			wcod_procedimiento:	$("#busc_procedimiento_"+n).attr('valor')
		}, function (data) {

			if(data.error > 0)
			{
				alert(data.mensaje);
			}
			else
			{
				$("#wporter_"+n).val(data.wporter);
			}

		},'json');
	}
	//-------------------------------------------------------------------------------------
	//	Segun el tercero trae sus especialidades y las carga en el select especialidaes
	//-------------------------------------------------------------------------------------
    function cargarSelectEspecialidades( cadena,n )
    {
		var especialidades = cadena.split(",");
		var html_options = "";
		for( var i in especialidades ){
			var especialidad = especialidades[i].split("-");
			html_options+="<option value='"+especialidad[0]+"'>"+especialidad[1]+"</option>";
		}
		$("#busc_especialidades_"+n).html( html_options );
    }
	//------------------------------------------------------------------------------------------------------
	//	Funcion que carga el autocomplete de los terceros
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function cargar_terceros(ArrayValores, n)
	{
		var terceros      = new Array();
		var index		  = -1;
		for (var cod_ter in ArrayValores)
		{
			index++;
			terceros[index] = {};
			terceros[index].value  = cod_ter;
			terceros[index].label  = ArrayValores[cod_ter]['nombre'];
			terceros[index].especialidades  = ArrayValores[cod_ter]['especialidad'];
		}
		$("#busc_terceros_"+n).autocomplete({
			minLength: 	0,
			source: 	terceros,
			select: 	function( event, ui ){
				cargarSelectEspecialidades( ui.item.especialidades, n );
				$("#busc_terceros_"+n).val(ui.item.label);
				$("#busc_terceros_"+n).attr('valor', ui.item.value);
				datos_desde_tercero(ui.item.value,n);
				setTimeout(function(){
					var html = $("#ContenidoPaquete").html();
					guardarEnTemporal(html);
				}, 1000);
				return false;
			}
		});
	}
	//------------------------------------------------------------------------------------------------------
	//	Funcion que carga los elementos de un paquete
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function CargarElementosDelPaquete(CodigoPaquete)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'CargarElementosDelPaquete',
			wemp_pmla:				$('#wemp_pmla').val(),
			CodPaquete:				CodigoPaquete,
			NomPaquete:				$("#busc_paquete").attr('nombre'),
			CodigoResponsable:		$('#responsable_original_tal').val(),
			CodigoTarifa:			$("#tarifa_original_tal").val(),
			FechaCargo:		  		$('#wfeccar').val(),
			ccoFacturador:			$('#wcco_tal').val(),
			tipoIngreso:			$('#wtipo_ingreso_tal').val(),
			ccoUbiActualPac:		$("#ccoActualPac_tal").val()
		}
		,function(respuesta){
			pintarElementosPaquete(respuesta.html);
		},'json');
	}
	//---------------------------------------------------------------------------------
	//	Pinta un mensaje en el div correspondiente para los mensajes
	//---------------------------------------------------------------------------------
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<BLINK><img width='15' height='15' src='../../images/medical/root/info.png' /></BLINK>&nbsp;"+mensaje);
		$("#div_mensajes").css({"width":"300","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();
		$("#div_mensajes").effect("pulsate", {}, 2000);

		setTimeout(function() {
			$("#div_mensajes").hide(500);
		}, 15000);

		// --> Redimecionar el tamaño del acordeon
		$( "#accordionContenido" ).accordion("destroy");
		$( "#accordionContenido" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
	}
	//------------------------------------------------------------------------------------------------------
	//	Funcion que carga un selector de hora en un campo de texto
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function CargarTimepicker(Elemento)
	{
		$('#'+Elemento).timepicker({
			showPeriodLabels: false,
			hourText: 'Hora',
			minuteText: 'Minuto',
			amPmText: ['AM', 'PM'],
			closeButtonText: 'Aceptar',
			nowButtonText: 'Ahora',
			deselectButtonText: 'Deseleccionar',
			defaultTime: 'now'
		});
	}
	//------------------------------------------------------------------------------------------------------
	//	Funcion que graba todos los cargos de un paquete
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function grabar(Boton)
	{
		var PermitirGrabar = true;
		var ExistenCargos  = false;
		var graba_varios_terceros =0;
		$('[borderred=si]').css("border","").removeAttr('borderred');
		// -->  Recorrer las filas de los cargos que se van a grabar
		$('.cargo_cargo').each(function(){
			var consecutivo = $(this).attr('consecutivo');
			// --> Si esta checkeado para grabar
			if($("#wgrabable_"+consecutivo).attr('checked') == 'checked')
			{
				ExistenCargos = true;
				//---------------------------------------------------------------
				// --> Validacion de campos obligatorios
				//---------------------------------------------------------------

				// --> Validacion de centro de costos
				if($("#wccogra_"+consecutivo).val() == '' || $("#wccogra_"+consecutivo).val() == 'Seleccione..')
				{
					borderojo($("#wccogra_"+consecutivo));
					mostrar_mensaje('El concepto no tiene un centro de costos asociado');
					PermitirGrabar = false;
					return;
				}

				// --> Si el concepto es compartido
				//if(!$("#busc_terceros_"+consecutivo).is(':hidden'))
				if($("#wcontip_"+consecutivo).val() == 'C')
				{
					// --> Validacion del tercero
					if($("#busc_terceros_"+consecutivo).val()=='')
					{
						borderojo($("#busc_terceros_"+consecutivo));
						mostrar_mensaje("No se ha seleccionado ningun tercero");
						PermitirGrabar = false;
						return;
					}
					// --> valida de  graba varios terceros
					if ($("#busc_terceros_"+consecutivo).attr('cedulas') == undefined)
					{
						// --> Validacion de la especialidad
						if($("#busc_especialidades_"+consecutivo).val()=='' )
						{
							borderojo($("#busc_especialidades_"+consecutivo));
							mostrar_mensaje("No ha seleccionado la especialidad del tercero");
							PermitirGrabar = false;
							return;
						}
					}
					else
					{
						graba_varios_terceros =1;
					}
					// --> Validacion del % de participacion
					if($("#wporter_"+consecutivo).val() ==  "" )
					{
						borderojo($("#busc_terceros_"+consecutivo));
						mostrar_mensaje("El tercero no tiene porcentaje de participacion en este concepto");
						PermitirGrabar = false;
						return;
					}
				}
				// --> Validacion si es reconocido o excedente
				if($("#wrecexc_"+consecutivo).val()!="R" && $("#wrecexc_"+consecutivo).val()!="E")
				{
					borderojo($("#wrecexc_"+consecutivo));
					mostrar_mensaje("Se debe ingresar si el concepto es Reconocido(<b>R</b>) O Excedente(<b>E</b>)");
					PermitirGrabar = false;
					return;
				}
				// --> Validacion si es facturable
				if($("#wfacturable_"+consecutivo).val() !="S" && $("#wfacturable_"+consecutivo).val() != "N")
				{
					borderojo($("#wfacturable_"+consecutivo));
					mostrar_mensaje("Se debe ingresar si el concepto es Facturable (S/N)");
					PermitirGrabar = false;
					return;
				}
				// --> Validacion del valor de la tarifa
				if($("#wvaltar_"+consecutivo).val() =='' || $("#wvaltar_"+consecutivo).val() == '0' )
				{
					borderojo($("#wvaltar_"+consecutivo));
					mostrar_mensaje("No existe la tarifa para el procedimiento");
					PermitirGrabar = false;
					return;
				}
				// --> Validacion de la fecha del cargo
				if($("#wfeccar").val()=="")
				{
					borderojo($("#wfeccar"));
					mostrar_mensaje("No se ha ingresado la fecha para grabar este cargo");
					PermitirGrabar = false;
					return;
				}
			}
		});

		//---------------------------------------------------------------
		// --> Envio de variables para relizar la grabacion del cargo
		//---------------------------------------------------------------
		if (PermitirGrabar && ExistenCargos)
		{
			// --> Deshabilitar el boton grabar hasta que termine el proceso
			boton = jQuery(Boton);
			boton.html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Grabando..." >').attr("disabled","disabled");

			var mensajes_gra = 'paquete grabado correctamente.';
			// --> Obtener el consecutivo de grabacion
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:      	'',
				wemp_pmla:         	$('#wemp_pmla').val(),
				accion:            	'ObtenerConsecutivoPaquete',
				whistoria:			$("#whistoria_tal").val(),
				wing:				$("#wing_tal").val(),
				wcodpaq:			$("#busc_paquete").attr('valor')
			}, function(consecGrabacionPaq){

				// --> Recorrer cada concepto de grabacion del paquete
				$('.cargo_cargo').each(function(){
					var consecutivo = $(this).attr('consecutivo');
					// --> Si esta checkeado para grabar
					if($("#wgrabable_"+consecutivo).attr('checked') == 'checked')
					{
						$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
						{
							consultaAjax:      	'',
							wemp_pmla:         	$('#wemp_pmla').val(),
							accion:            	'GrabarCargo',
							whistoria:			$("#whistoria_tal").val(),
							wing:				$("#wing_tal").val(),
							wno1:				$("#wno1_tal").val(),
							wno2:    			$("#wno2_tal").val(),
							wap1:    			$("#wap1_tal").val(),
							wap2:   			$("#wap2_tal").val(),
							wdoc:    			$("#wdoc_tal").val(),
							wcodemp:   			$("#responsable_original_tal").val(),
							wnomemp:    		$("#wnomemp_tal").val(),
							tipoEmpresa:    	$("#tipoEmpresa_tal").val(),
							nitEmpresa:    		$("#nitEmpresa_tal").val(),
							tipoPaciente:		$('#wtip_paciente_tal').val(),
							tipoIngreso:		$('#wtipo_ingreso_tal').val(),
							wfecing:   			$("#wfecing_tal").val(),
							wtar:		  	  	$("#tarifa_original_tal").val(),
							wser:    			$("#wser_tal").val(),
							wcodcon:			$("#busc_concepto_"+consecutivo).attr('valor'),
							wnomcon:			$("#busc_concepto_"+consecutivo).val(),
							wprocod:			$("#busc_procedimiento_"+consecutivo).attr('valor'),
							wpronom: 			$("#busc_procedimiento_"+consecutivo).val(),
							wcodter: 			$("#busc_terceros_"+consecutivo).attr('valor'),
							wnomter: 			$("#busc_terceros_"+consecutivo).val(),
							wporter:			$("#wporter_"+consecutivo).val(),
							wcantidad:			$("#wcantidad_"+consecutivo).val(),
							wvaltar:			$("#wvaltar_"+consecutivo).val(),
							wrecexc:			$("#wrecexc_"+consecutivo).val(),
							wfacturable:		$("#wfacturable_"+consecutivo).val(),
							wcco: 				$("#wccogra_"+consecutivo).val(),
							wccogra:			$("#wccogra_"+consecutivo).val(),
							wfeccar:			$("#wfeccar").val(),
							wconinv:			$("#wconinv_"+consecutivo).val(),
							wcodpaq:			$("#busc_paquete").attr('valor'),
							wpaquete:			'on',
							consecGrabacionPaq:	consecGrabacionPaq,
							wconabo:			$("#wconabo_"+consecutivo).val(),
							wdevol:				$("#wdevol").val(),
							waprovecha:	 		'off',
							wexiste:			$("#wexiste_"+consecutivo).val(),
							wbod:				$("#wbod").val(),
							wconser:			$("#wconser_"+consecutivo).val(),
							nomCajero:			$("#nomCajero_tal").val(),
							wtipfac: 			$("#wtipfac_"+consecutivo).val(),
							wespecialdiad:		$("#busc_especialidades_"+consecutivo).val(),
							whora_cargo:		$("#whora_cargo_"+consecutivo).val(),
							wgraba_varios_terceros:	 graba_varios_terceros,
							wcodcedula:			$("#busc_terceros_"+consecutivo).attr('cedulas')
						}, function (data) {
							if(data.Mensajes != "Cargo grabado correctamente.")
								mensajes_gra+= data.Mensajes+'<br>';
						}, 'json');
					}
				});
			});

			// --> Cuando termine la grabacion
			setTimeout(function(){
				$("#ContenidoPaquete").html("");
				$("#FieldsetContenidoPaquete").hide("");
				$("#busc_paquete").val('');
				mostrar_mensaje(mensajes_gra);
				pintar_detalle_cuenta();
				// --> Descongelar la cuenta
				congelarCuentaPaciente('off');
				// --> Activar boton grabar
				boton.html('GRABAR').removeAttr("disabled");
			},300);

		}
	}
	//---------------------------------------------------------------------------------------------------
	//	Esta funcion pinta los paquetes que le han grabado al paciente
	//---------------------------------------------------------------------------------------------------
	function pintar_detalle_cuenta()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'pintar_detalle_cuenta',
			whistoria:			$("#whistoria_tal").val(),
			wing:				$("#wing_tal").val()
		}, function (data){
			$("#DivDetalleCuenta").html(data);
			// --> Redimecionar el tamaño del acordeon
			$( "#accordionDetCuenta" ).accordion("destroy");
			$( "#accordionDetCuenta" ).accordion({
				collapsible: 	true,
				heightStyle: 	"content",
				active: 		1
			});
		});
	}
	//---------------------------------------------------------------------------------------------------
	//	Funcion que hace el llamado para anular un cargo de un paciente
	//---------------------------------------------------------------------------------------------------
	function anular(wid)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'anular',
			wid:				wid

		}, function (data){
			mostrar_mensaje(data.Mensaje);
			if(!data.Error)
				pintar_detalle_cuenta();
		}, 'json');
	}
	//---------------------------------------------------------------------------------------------------
	//	Esta funcion trae los terceros o varios terceros  segun el cco seleccionado y
	//	que el concepto sea compartido
	//---------------------------------------------------------------------------------------------------
	function datos_desde_conceptoxcco(consecutivo, guardarCambioTemporal)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      	'',
			wemp_pmla:         	$('#wemp_pmla').val(),
			accion:            	'datos_desde_conceptoxcco',
			wccogra:			$("#wccogra_"+consecutivo).val(),
			wcodcon:			$("#busc_concepto_"+consecutivo).attr('valor')

		}, function (data) {

			if(data.respuesta == 'ok')
			{
				$("#wporter_"+consecutivo).val(data.porcentajes);

				if(	$("#wcontip_"+consecutivo).val() == 'C')
				{
					$("#busc_terceros_"+consecutivo).val('MULTITERCERO');
					$("#busc_terceros_"+consecutivo).attr('valor',  'MULTITERCERO');
					$("#busc_terceros_"+consecutivo).attr('cedulas', data.opciones);
					// se desabilita el campo para que no se pueda modificar
					$("#busc_terceros_"+consecutivo).attr('disabled',true);
				}
			}
			else
			{
				if($("#wcontip_"+consecutivo).val() == 'C')
				{
					$("#busc_terceros_"+consecutivo).removeAttr("cedulas");

					if($("#busc_terceros_"+consecutivo).val() == 'MULTITERCERO')
					{
						$("#busc_terceros_"+consecutivo).attr('valor',  '');
						$("#busc_terceros_"+consecutivo).val('');
						$("#busc_terceros_"+consecutivo).attr('disabled',false);
					}
				}

			}
			if(guardarCambioTemporal)
			{
				var html = $("#ContenidoPaquete").html();
				guardarEnTemporal(html);
			}
		},'json');
	}
	//--------------------------------------------------------------------------
	//	Funcion que hace el llamado para obtener los datos basicos del paciente
	//--------------------------------------------------------------------------
	function cargarDatosPaciente(elemento)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:      		'',
			wemp_pmla:         		$('#wemp_pmla').val(),
			accion:            		'cargar_datos_paciente',
			whistoria:		   		$('#whistoriaLocal').val(),
			wing:					$('#wingLocal').val(),
			wcargos_sin_facturar:	'ok',
			welemento:				elemento

		},function(data){

			// --> data.prueba valida si la historia existe
			if(data.prueba == 'no')
			{
				alert('La historia no existe');
				$('#whistoriaLocal').val('');
				$('#wingLocal').val('');
				limpiarPantalla();
			}
			else
			{
				// --> data.error indica si hay un error  en el llamado de la funcion
				if(data.error ==1)
				{
					alert(data.mensaje);
					$('#whistoriaLocal').val('');
					$('#wingLocal').val('');
					limpiarPantalla();
				}
				else
				{
					// --> datos traidos desde la funcion

					// --> Historia
					$("#whistoria_tal").val($('#whistoriaLocal').val());

					// --> Ingreso
					$("#wing_tal").val(data.wwing);
					$("#wingLocal").val(data.wwing);

					// --> Paciente
					$("#wno1_tal").val(data.wno1);
					$("#wno2_tal").val(data.wno2);
					$("#wap1_tal").val(data.wap1);
					$("#wap2_tal").val(data.wap2);
					$("#nombrePaciente").html(data.wno1+" "+data.wno2+" "+data.wap1+" "+data.wap2);

					// --> Documento
					$("#documento").html(data.wdoc);
					$("#wdoc_tal").val(data.wdoc);
					$("#div_documento_tal").val(data.wdoc);

					// --> Responsable
					$("#wnomemp_tal").val(data.wnomemp);
					$("#div_responsable_tal").val(data.responsable);
					$("#responsable_original_tal").val(data.wcodemp);

					// --> Fecha de ingreso
					$("#fechaIngreso").html(data.wfecing);
					$("#wfecing_tal").val(data.wfecing);

					// --> Servicio de facturacion
					$("#nombreCco").html($("#wcco_tal").val()+"-"+$("#div_servicio_tal").val());

					// --> Tipo de ingreso
					$("#tipoIngreso").html(data.nombre_tipo_ingreso);

					// --> Tipo de servicio
					$("#wpactam_tal").val(data.wpactam);

					// --> Nombre del servicio de ingreso
					$("#wser_tal").val(data.wser);
					$("#nomservicio_tal").val(data.wnombreservicio);
					$("#servicio").html(data.wser+"-"+data.wnombreservicio);

					// --> Tarifa
					$("#nombreTarifa").html(data.tarifa);
					$("#div_tarifa_tal").val(data.tarifa);
					$("#tarifa_original_tal").val(data.wtar);

					$("#wtip_paciente_tal").val(data.wtip_paciente);
					$("#wtipo_ingreso_tal").val(data.tipo_ingreso);

					// --> Ubicacion actual del paciente
					$("#divCcoActualPac").html(data.ccoActualPac+"-"+data.nomCcoActualPac);
					$("#ccoActualPac_tal").val(data.ccoActualPac);
					$("#nomCcoActualPac_tal").val(data.nomCcoActualPac);

					// --> Pintar los otros responsables del paciente
					$("#tableResponsables").html('');
					$("#tableResponsables").append(data.otrosResponsables).show();
					$("#tableResponsables_tal").val(data.otrosResponsables);

					// --> Tipo de empresa
					$("#tipoEmpresa_tal").val(data.tipoEmpresa);

					// --> Nit de empresa
					$("#nitEmpresa_tal").val(data.nitEmpresa);

					// --> Validar estado de cuenta congelada
					validarEstadoDeCuentaCongelada(false);
					// --> Pintar el detalle de la cuenta
					pintar_detalle_cuenta();
				}
			}
		},
		'json');
	}
	//-------------------------------------------------------------------
	//	Realiza la congelacion de la cuenta del paciente
	//-------------------------------------------------------------------
	function congelarCuentaPaciente(congelar)
	{
		var estadoActual = $("#cuentaCongelada").val();

		if($("#whistoriaLocal").val() != '' && $("#wingLocal").val() != '' && estadoActual != congelar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				accion:         'congelarCuentaPaciente',
				historia:		$("#whistoriaLocal").val(),
				ingreso:		$("#wingLocal").val(),
				congelar:		congelar
			}, function(data){
				$("#cuentaCongelada").val(congelar);
			});
		}
	}
	//-----------------------------------------------------------------------------
	// -->  Validar si la cuenta se encuentra congelada, ya que si acurrio
	//		un cierre inesperado del programa  la cuenta puede quedar congelada.
	//-----------------------------------------------------------------------------
	function validarEstadoDeCuentaCongelada(desdeSelectorPaquete)
	{
		if($("#whistoriaLocal").val() == '' || $("#wingLocal").val() == '')
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'estadoCuentaCongelada',
			historia:		$("#whistoriaLocal").val(),
			ingreso:		$("#wingLocal").val()
		}, function(info){
			// --> Si la cuenta se encuentra congelada
			if(info.Ecoest == 'on')
			{
				// --> si el usuario que la congelo es diferente al actual
				if(info.Ecousu != info.wuse)
				{
					var mensaje = 	'<br>'+
									' En este momento no se le pueden grabar cargos al paciente.<br>'+
									' La cuenta se encuentra congelada por <b>'+info.nomUsuario+'</b>'+
									', en un proceso de <b>liquidacion de '+info.Nomtip+'</b>.';

					// --> Mostrar mensaje
					$( '#divMsjCongelar').html(mensaje);
					$( '#divMsjCongelar').dialog({
						show:{
							effect: "blind",
							duration: 100
						},
						hide:{
							effect: "blind",
							duration: 100
						},
						width:  500,
						dialogClass: 'fixed-dialog',
						modal: true,
						title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
						close: function( event, ui ) {
							if(desdeSelectorPaquete)
							{
								$("#busc_paquete").val('');
								$("#busc_paquete").attr('valor', '');
								$("#busc_paquete").attr("nombre", '');
								$("#ContenidoPaquete").html("");
								$("#FieldsetContenidoPaquete").hide("");
							}
							else
							{
								limpiarPantalla();
							}
						}
					});
				}
				// --> Si es el mismo usuario que la congelo, se descongela la cuenta
				else
				{
					if(!desdeSelectorPaquete)
					{
						// --> Si la cuenta congelada era de paquetes
						if(info.Ecotip == 'PA')
						{
							// --> Validar si se desea recuperar la informacion temporal
							$( '#divMsjCongelar').html("Existe una liquidación de <b>paquetes</b> en proceso.");
							$( '#divMsjCongelar').dialog({
								width:  350,
								dialogClass: 'fixed-dialog',
								modal: true,
								title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
								close: function( event, ui ) {
										limpiarPantalla();
								},
								buttons:{
									"Recuperar": function() {
										congelarCuentaPaciente('on');
										// --> Obtener liquidacion temporal
										$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
										{
											consultaAjax:   '',
											wemp_pmla:      $('#wemp_pmla').val(),
											accion:         'obtenerLiquidacionTemporal',
											historia:		$("#whistoriaLocal").val(),
											ingreso:		$("#wingLocal").val()
										}, function(temporal){
											pintarElementosPaquete(temporal);
											$("#busc_paquete").attr('valor', $("#ContenidoPaquete").find("#hiddenCodPaquete").val());
											$("#busc_paquete").attr('nombre', $("#ContenidoPaquete").find("#hiddenNomPaquete").val());
											$("#busc_paquete").val($("#ContenidoPaquete").find("#hiddenNomPaquete").val());
										});
										$(this).dialog("destroy");
									},
									"Nueva": function() {
										congelarCuentaPaciente('off');
										$(this).dialog("destroy");
									}
								 }
							});
						}
						else
						{
							mensaje = "Usted tiene una liquidación de <b>"+info.Nomtip+"</b> en proceso.<br>Para conservar dicho proceso de Click en <b>Aceptar</b> y luego abra su prgrama correspondiente.<br>Si desea cancelar el proceso y poder liquidarle paquetes al paciente de Click en <b>Cancelar</b>.";
							$( '#divMsjCongelar').html(mensaje);
							$( '#divMsjCongelar').dialog({
								width:  680,
								dialogClass: 'fixed-dialog',
								modal: true,
								title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
								close: function( event, ui ) {
										limpiarPantalla();
								},
								buttons:{
									"Aceptar": function() {
										$(this).dialog("close");
									},
									Cancel: function() {
										congelarCuentaPaciente('off');
										$(this).dialog("destroy");
									}
								 }
							});
						}
					}
				}
			}
			// --> Si no esta congelada se congela
			else
			{
				if(desdeSelectorPaquete)
				{
					congelarCuentaPaciente('on');
					var html = $("#ContenidoPaquete").html();
					//html = 'Hola';
					guardarEnTemporal(html);
				}
			}
		}, 'json');
	}
	function limpiarPantalla()
	{
		$("#whistoriaLocal").val('');
		$("#wingLocal").val('');
		$("#informacion_inicial").find("[limpiar=si]").html("");
		$("#DivDetalleCuenta").html("");
	}
	//----------------------------------------------------------------------
	//	FUNCION QUE VA GUARDANDO UNA FOTO DE LA LIQUIDACION TEMPORAL
	//----------------------------------------------------------------------
	function guardarEnTemporal(html)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   	'',
			wemp_pmla:      	$('#wemp_pmla').val(),
			accion:         	'guardarEnTemporal',
			historia:			$("#whistoriaLocal").val(),
			ingreso:			$("#wingLocal").val(),
			html:				html
		}, function(data){
		});
	}

	//----------------------------------------------------------------------
	//	FUNCION QUE PINTA EL DETALLE DEL PAQUETE A LIQUIDAR
	//----------------------------------------------------------------------
	function pintarElementosPaquete(html)
	{
		$("#ContenidoPaquete").html(html);
		$("#FieldsetContenidoPaquete").show();
		var cantidad = $("#cantidadElementosPaquete").val();

		for(var x=1; x<=cantidad; x++)
		{
			// --> Cargar selector de hora
			CargarTimepicker('whora_cargo_'+x);

			var cod_tarifa_pac = $("#tarifa_original_tal").val();
			var nom_tarifa_pac = $("#div_tarifa_tal").val();
			$("#tarifa_cargo_"+x).val(cod_tarifa_pac);
			$("#td_tarifa_"+x).html(nom_tarifa_pac);
		}

		// --> Redimecionar el tamaño del acordeon
		$( "#accordionContenido" ).accordion("destroy");
		$( "#accordionContenido" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});

		// --> Activar tooltip
		$("[tooltip=si]").tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		// --> Activar los autocomplete de los terceros
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:     '',
			wemp_pmla:        $('#wemp_pmla').val(),
			accion:           'traer_terceros'
		}, function (data) {
			var ArrayValores  = eval('(' + data + ')');
			for(var x=1; x<=cantidad; x++)
			{
				// --> Si el concepto es compartido
				if($('#wcontip_'+x).val() == 'C')
				{
					// --> Si existe un tercero ya asignado por la configuracion del paquete
					if($("#busc_terceros_"+x).attr("valor") != '')
					{
						var CedTerceroTemp = $("#busc_terceros_"+x).attr("valor");
						// --> Consulto si el cco del facturador esta configurado como multitercero en los % de participacion
						setTimeout(function(){
							datos_desde_conceptoxcco(x, false);
						}, 500);

						// --> Si el cco no es de multitercero
						if($("#busc_terceros_"+x).val() != 'varios terceros' )
						{
							cargar_terceros(ArrayValores, x);
							//$("#busc_terceros_"+x).attr("disabled", "disabled");
							$("#busc_terceros_"+x).attr("valor", CedTerceroTemp);
							$("#busc_terceros_"+x).val(ArrayValores[CedTerceroTemp]['nombre']);
							cargarSelectEspecialidades(ArrayValores[CedTerceroTemp]['especialidad'], x);
							datos_desde_tercero(CedTerceroTemp,x);
						}
					}
					// --> Activo el autocomplete de los terceros para que sea seleccionado
					else
					{
						cargar_terceros(ArrayValores, x);
					}
				}
				// --> Si el concepto no es compartido entonces oculto el campo de tercero
				else
				{
					$("#busc_terceros_"+x).hide();
				}
			}
		});

		// --> Aplicar permisos de permitir seleccionar facturable S-N
		if($("[name=permiteSeleccionarFacturable_tal]").val() == 'on')
			$(".facturableSN").removeAttr('disabled');

		// --> Aplicar permisos para seleccionar si es reconocido o excedente
		if($("[name=permiteSeleccionarRecExc_tal]").val() == 'on')
			$(".recExce").removeAttr('disabled');
	}

	function actualiza_valor_total(consecutivo)
	{
		var cantidad 	= $("#wcantidad_"+consecutivo).val();
		var valor 		= $("#wvaltar_"+consecutivo).val();
		var valor_total = 0;
		valor_total = cantidad * valor;
		valor_total = number_format(valor_total,0,'.',',');
		$("#div_valor_total_"+consecutivo).text('$'+valor_total);
	}

	function number_format(number, decimals, dec_point, thousands_sep)
	{
		var n = !isFinite(+number) ? 0 : +number,
			prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
			sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
			dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
			s = '',
			toFixedFix = function (n, prec) {
				var k = Math.pow(10, prec);
				return '' + Math.round(n * k) / k;
			};

		s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
		if (s[0].length > 3) {
			s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
		}
		if ((s[1] || '').length < prec) {
			s[1] = s[1] || '';
			s[1] += new Array(prec - s[1].length + 1).join('0');
		}
		return s.join(dec);
	}
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.ui-autocomplete{
			max-width: 	350px;
			max-height: 200px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	9pt;
		}
		.Titulo_azul{
			color:#3399ff;
			font-weight: bold;
			font-family: verdana;
			font-size: 10pt;
		}
		.Bordegris{
			border: 1px solid #999999;
		}
		.BordeNaranja{
			border: 1px solid orange;
		}
		.campoRequerido{
				border: 1px outset #3399ff ;
				background-color:lightyellow;
				color:gray;
		}
		.pad{
			padding: 	4px;
		}
		.filaDetalle{
			font-size	: 8pt;
			font-family': verdana;
		}
		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
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

	// --> Cargar hidden de Paquetes, para el autocomplete.
	echo "<input type='hidden' id='hidden_paquetes' value='".json_encode(Obtener_array_paquetes())."'>";
	// --> Entidades
	echo "<input type='hidden' id='hidden_entidades' value='".json_encode(obtener_array_entidades())."'>";
	// --> Hidden de array de las tarifas
	echo "<input type='hidden' id='hidden_tarifas' name='hidden_tarifas' value='".json_encode(Obtener_array_tarifas())."'>";
	echo "<input type='hidden' id='cuentaCongelada' name='cuentaCongelada' value='' >";

	// -->  Div para mostrar el mensaje de que la cuenta se encuentra congelada
	echo "
	<div id='divMsjCongelar' align='center' style='display:none;font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 10pt;'>
		<br>
	</div>
	";

	echo"
	<div align='center'>
		<div width='95%' id='accordionDatosPaciente'>
			<h3>DATOS DEL PACIENTE</h3>
			<div class='pad' align='center' id='DatosPaciente'>
				<table width='90%' style='border: 1px solid #999999;background-color: #ffffff;' id='informacion_inicial'>
					<tr class='fila1' style='font-weight: bold;'>
						<td align='left' width='11%'>
							<b>Historia:</b>
						</td>
						<td align='left' width='15%'>
							<b>Ingreso Nro:</b>
						</td>
						<td align='left' colspan='2'>
							<b>Paciente:</b>
						</td>
						<td align='left'>
							<b>Documento:</b>
						</td>
						<td align='left' colspan='2'>
							<b>Fecha Ingreso:</b>
						</td>
					</tr>
					<tr class='fila2'>
						<td><input type='text' id='whistoriaLocal' size='15' onchange='cargarDatosPaciente(\"whistoria\")' value='".$whistoria."'></td>
						<td><input type='text' id='wingLocal' 	   size='3'  onchange='cargarDatosPaciente(\"wing\")' value='".$wing."'></td>
						<td id='nombrePaciente' colspan='2' limpiar='si'></td>
						<td  id='documento' limpiar='si'></td>
						<td colspan='2' id='fechaIngreso' limpiar='si'></td>
					</tr>
					<tr class='fila1' style='font-weight: bold;'>
						<td align='left'>
							<b>Servicio de Ing:</b>
						</td>
						<td align='left' width='12%'>
							<b>Tipo de Ingreso:</b>
						</td>
						<td align='left'>
							<b>Ubicación:</b>
						</td>
						<td align='left'>
							<b>Servicio de facturación:</b>
						</td>
						<td align='center' colspan='3'>
							<b>Responsables:</b>
						</td>
					</tr>
					<tr class='fila2'>
						<td id='servicio' limpiar='si'></td>
						<td id='tipoIngreso' limpiar='si'></td>
						<td id='divCcoActualPac' limpiar='si'></td>
						<td id='nombreCco'></td>
						<td align='left' colspan='3' style='font-size:8pt;' >
							<table width='100%' id='tableResponsables' style='background-color: #ffffff;' limpiar='si'>
							</table>
							<div id='div_responsable' 	style='display:none'></div>
							<div id='div_tarifa'		style='display:none'></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div width='95%' id='accordionContenido'>
			<h3>LIQUIDACION DE PAQUETES</h3>
			<div>
				<table width='64%'>
					<tr>
						<td class='encabezadoTabla'>Fecha del cargo:</td>
						<td align='left' class='fila1'>
							<input type='text' id='wfeccar' size='12' value='".date("Y-m-d")."'>
						</td>
						<td class='encabezadoTabla'>Seleccione el paquete:</td>
						<td align='center' class='fila1'>
							<input type='text' id='busc_paquete' size='50'>
						</td>
					</tr>
				</table><br>
				<table width='100%'>
					<tr>
						<td align='center'>
							<fieldset align='center' id='FieldsetContenidoPaquete' style='padding:10px;display:none'>
								<legend class='fieldset'>Contenido del paquete</legend>
								<div class='ContenidoPaquete' id='ContenidoPaquete'>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td align='right'>
							<div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
							</div>
							<br>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div width='95%' id='accordionDetCuenta'>
			<h3>DETALLE DE LA CUENTA</h3>
			<div id='DivDetalleCuenta'>";
				pintar_detalle_cuenta($whistoria, $wing);
	echo"	</div>
		</div>
	</div>";
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
