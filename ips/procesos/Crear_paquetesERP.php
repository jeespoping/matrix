<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        CREAR PAQUETES
//=========================================================================================================================================\\
//DESCRIPCION:			Programa para visualizar y crear paquetes
//AUTOR:				Jerson andres trujillo
//FECHA DE CREACION:
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-10-19';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//Octubre 19 de 2020		Edwin MG:	- Se cambia la ruta del script MarcaDeAguaERP.js, para que no use la variable globla URL_ACTUAL.
//										Esto se hace debido a los diferentes cambios en el servidor de producción que hace que la variable URL_ACTUAL
//										que contiene la ruta relativa de los script lo hace incorrectamente (el scrip que hace dicho enrutamiento y crea 
//										la variable global URL_ACTUAL es /matrix/gesapl/gestor_aplicaciones_config.php)
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
	

	include_once("ips/funciones_facturacionERP.php");
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha= date("Y-m-d");
    $whora = date("H:i:s");

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-----------------------------------------------------------------------
	//	Funcion que pinta el formulario para agregar un paquete
	//-----------------------------------------------------------------------
	function nuevo_paquete($CodPaquete)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$array_info = array();

		// --> Hidden de array de las tarifas
		echo "<input type='hidden' id='hidden_tarifas' name='hidden_tarifas' value='".json_encode(Obtener_array_tarifas())."'>";

		// --> Consultar la informacion del paquete
		if($CodPaquete != 'nuevo')
		{
			$q_info = "
			   SELECT  Paqnom, Grucod, Grudes, Grutip, Gruinv, Paqdetpro, Pronom,  A.id as id_registro, Paqest, Paqdetpai
				 FROM  ".$wbasedato."_000113, ".$wbasedato."_000114 AS A, ".$wbasedato."_000200, ".$wbasedato."_000103 AS B
				WHERE  Paqcod 		= '".$CodPaquete."'
				  AND  Paqcod 		= Paqdetcod
				  AND  Paqdetest	= 'on'
				  AND  Paqdetgen	= 'on'
				  AND  Paqdetcon	= Grucod
				  AND  Paqdetpro	= Procod
				UNION
			   SELECT  Paqnom, Grucod, Grudes, Grutip, Gruinv, Paqdetpro, '' as Pronom,  A.id as id_registro, Paqest, Paqdetpai
				 FROM  ".$wbasedato."_000113, ".$wbasedato."_000114 AS A, ".$wbasedato."_000200
				WHERE  Paqcod 		= '".$CodPaquete."'
				  AND  Paqcod 		= Paqdetcod
				  AND  Paqdetest	= 'on'
				  AND  Paqdetgen	= 'on'
				  AND  Paqdetcon	= Grucod
				  AND  Gruinv 		= 'on'
				  ";

			$res_info = mysql_query($q_info,$conex) or die("Error en el query: ".$q_info."<br>Tipo Error:".mysql_error());
			$num_detalles = mysql_num_rows($res_info);

			$arr_conceptos = array();
			$arr_procedimi = array();
			$arr_contenido = array();

			while($row_detalles = mysql_fetch_array($res_info))
			{
				$nombre_paquete 		= $row_detalles['Paqnom'];
				$estado_paquete 		= $row_detalles['Paqest'];
				$row_detalles['nuevo'] 	= 'no';
				$array_info[] 			= $row_detalles;

				$arr_conceptos[$row_detalles['Grucod']]['nombre'] 	= $row_detalles['Grudes'];
				$arr_conceptos[$row_detalles['Grucod']]['tipo'] 	= $row_detalles['Grutip'];
				$arr_procedimi[$row_detalles['Paqdetpro']] = $row_detalles['Pronom'];

				if($row_detalles['Paqdetpai'] == 'on')
					$indiceCon = $row_detalles['Grucod'];
				else
					$indiceCon = $row_detalles['Grucod'].'|'.$row_detalles['Paqdetpro'];

				$indice = count($arr_contenido[$indiceCon]);
				$arr_contenido[$indiceCon][$indice]['esDeInventario'] = $row_detalles['Paqdetpai'];
				$arr_contenido[$indiceCon][$indice]['codProcedMerca'] = $row_detalles['Paqdetpro'];
			}
		}
		else
		{
			$array_info[0]['nuevo'] = 'si';
		}

		echo "
		<div width='100%' align='right'>
			<img style='cursor:pointer;' OnClick='$(\"#contenedorVerPaquete\").hide(300);ajustarTamañoLista(500);' title='Cerrar' src='../../images/medical/eliminar1.png'>
		</div>
		<div id='accordionDatosBasicos'>
			<h3>CONTENIDO DEL PAQUETE</h3>
			<div align='center' id='DatosBasicos'>
				<table width='97%'>
					<tr>
						<td align='left' class='encabezadoTabla'>Codigo: </td>
						<td align='center' class='fila2'><input  type='text' size='5' id='codigo_paquete'  disabled='disabled' value='".((isset($nombre_paquete) ? $CodPaquete : ''))."'></td>
						<td align='left' class='encabezadoTabla'>Nombre:</td>
						<td align='center' class='fila2'><input type='text' size='50' id='nombre_paquete' msgError='Digite nombre' value='".((isset($nombre_paquete) ? $nombre_paquete : ''))."'></td>
						<td align='left' class='encabezadoTabla'> N° Detalles:</td>
						<td align='center' class='fila2'><input type='text' size='3' id='num_detalles' disabled value='".((isset($num_detalles) ? $num_detalles : '1'))."'></td>
						<td align='left' class='encabezadoTabla'>Estado:</td>
						<td align='center' class='fila2'>
							<img id='EstadoPaquete' style='cursor:pointer' width='30' height='30' ".((isset($estado_paquete) && $estado_paquete != 'on') ? "src='../../images/medical/sgc/powerOff.png' value='Inactiva' " : "src='../../images/medical/sgc/powerOn.png' value='Activa' ")." OnClick='cambiar_estado(\"".$CodPaquete."\");'>
						</td>
					</tr>
				</table>
				<br>
				<table id='tabla_nuevos_procedimientos' width='97%'>
					<tr>
						<td align='left' class='encabezadoTabla' colspan='2'>Concepto</td>
						<td align='left' class='encabezadoTabla' colspan='2'>Procedimiento</td>
					<td align='left'></td>
					</tr>
					<tr>
						<td colspan='4' align='right'>
							<span style='font-size:13px;cursor:pointer;font-weight:bold' OnClick='AgregarFilaCampos(\"tabla_nuevos_procedimientos\");'>Agregar Concepto&nbsp;<img src='../../images/medical/HCE/mas.PNG'></span>
						</td>
					</tr>
					";

				$colorFila 				= 'fila1';
				foreach($array_info as $consecutivo => $array_detalles)
				{
					if($colorFila == 'fila1')
						$colorFila = 'fila2';
					else
						$colorFila = 'fila1';

					if($array_detalles['nuevo'] == 'no')
						$valor = true;
					else
						$valor = false;

					if($valor)
					{
						if($array_detalles['Gruinv'] != 'on')
						{
							$verProcedimiento 	= '';
							$verDetInsumos 		= ';display:none';
							$codPaqInsumos		= '';
						}
						else
						{
							$verProcedimiento 	= ';display:none';
							$verDetInsumos		= '';
							$codPaqInsumos		= str_replace('PI-', '', $array_detalles['Paqdetpro']);
						}
						$manejart = (($array_detalles['Paqdetpai'] == 'on') ? 'si' : 'no');
					}
					else
					{
						$verProcedimiento 	= '';
						$verDetInsumos 		= ';display:none';
						$codPaqInsumos		= '';
					}

					echo"
					<tr id='Detalle".$consecutivo."' class='".$colorFila."' ".(($valor) ? 'id_registro="'.$array_detalles['id_registro'].'"' : 'id_registro="nuevo"' ).">
						<td align='center' class='pad' width='2%'>
							<img width='16' height='16' tooltip='si' id='imgGuardado' src='../../images/medical/root/grabar.png' title='Guardado'>
						</td>
						<td align='center' class='pad'>
							<input type='text' ".(($valor) ? 'valor="'.$array_detalles['Grucod'].'" value="'.$array_detalles['Grucod'].'-'.$array_detalles['Grudes'].'" nombre="'.$array_detalles['Grudes'].'"' : 'valor=""')." size='55' style='font-size: 9pt;' prefijo='DetPaqConcepto' id='DetPaqConcepto".$consecutivo."'  msgError='Digite el concepto' CargarAutocomplete='conceptos' MensajeObligatorio='el concepto'>
						</td>
						<td align='center' class='pad'>
							<input type='text' ".(($valor) ? 'manejart="'.$manejart.'" valor="'.$array_detalles['Paqdetpro'].'" value="'.$array_detalles['Paqdetpro'].'-'.$array_detalles['Pronom'].'" nombre="'.$array_detalles['Pronom'].'"' : 'valor=""')." size='65' style='font-size: 9pt".$verProcedimiento."' prefijo='DetPaqProcedimiento' id='DetPaqProcedimiento".$consecutivo."' msgError='Digite el procedimiento' MensajeObligatorio='el procedimiento'>
							<span consecutivo='".$consecutivo."' id='verDetalle".$consecutivo."' style='cursor:pointer".$verDetInsumos."' onClick='verDetalleArticulos(this, \"".$codPaqInsumos."\");'>
								<b>Ver detalle</b>
								<img src='../../images/medical/iconos/gifs/i.p.next[1].gif'>
							</span>
						</td>
						<td class='pad' align='right'>
							<img style='cursor:pointer' id='eliminarDetalle".$consecutivo."' OnClick='EliminarTr(this, \"".$CodPaquete."\");' codPaqInsumos='".$codPaqInsumos."' src='../../images/medical/hce/cancel.PNG'>
						</td>
					</tr>";
				}
		echo"	</table>
				<div id='divDetalleArticulos' style='display:none' consecutivoAbierto=''></div>
				<br>
				<div>
					<input type='button' value='Guardar' OnClick='guardar_paquete();'>
				</div>
			</div>
		</div>
		<br>";

		// --> Consultar el detalle de las tarifas
		if($CodPaquete != 'nuevo')
		{
			// --> Consultar la base de datos de movimiento hospitalario correspondiente a la empresa
			$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			// --> Obtener un array con todos los terceros
			$arr_tercero = obtener_array_terceros();
			// --> Hidden de array de terceros
			echo "<input type='hidden' id='hidden_terceros' name='hidden_terceros' value='".json_encode($arr_tercero)."'>";


			echo"	<div id='accordionDetalle'>
						<h3>DETALLE DEL PAQUETE POR TARIFA</h3>
						<div align='center' id='DetallePaquete'>
							<table width='100%'>
								<tr>
									<td width='98%' align='right'>
										<span onclick='AgregarNuevaTarifa(\"".$CodPaquete."\", \"\", \"Tarifa\");' style='font-size:13px;cursor:pointer;font-weight:bold'>Agregar Tarifa <img src='../../images/medical/HCE/mas.PNG'></span>
									</td>
								</tr>
							</table>";

			$q_tar = "
				   SELECT  Tarcod, Tardes, Paqdetrmm, Paqdetcon, Paqdetpro, Paqdetter, Paqdetcan, Paqdetvac, Paqdetfec, Paqdetvan, Paqdetest, ".$wbasedato."_000114.id as consecutivo, Paqdetpai, Paqdetfac
					 FROM  ".$wbasedato."_000114 , ".$wbasedato."_000025
					WHERE  Paqdetcod	= '".$CodPaquete."'
					  AND  Paqdetgen 	!= 'on'
					  AND  Paqdettar 	= Tarcod
				 ORDER BY  Tarcod, (Paqdetrmm*1) ASC, Paqdetcon, Paqdetpro
			";
			$res_tar 		= mysql_query($q_tar,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q_tar." - ".mysql_error());
			$arr_paq_det 	= array();

			while($row_tar = mysql_fetch_array($res_tar))
			{
				if($row_tar['Tarcod'] == '*')
				{
					$row_tar['Tarcod']	= 'Todos';
					$row_tar['Tardes']	= 'Todos';
				}
				$row_tar['Nuevo'] = 'no';
				$arr_paq_det[$row_tar['Tarcod']][$row_tar['Paqdetrmm']][] 	= $row_tar;
				$arr_empresa[$row_tar['Tarcod']]	= $row_tar['Tardes'];

				if($row_tar['Paqdetpai'] == 'on')
					$arr_contenido_tarifa[$row_tar['Tarcod']][$row_tar['Paqdetcon']][] = '';
				else
					$arr_contenido_tarifa[$row_tar['Tarcod']][$row_tar['Paqdetcon'].'|'.$row_tar['Paqdetpro']][] = '';
			}

			// --> Pintar el arbol del detalle por tarifa
			$Consecutivo2 = 0;
			echo "<table width='100%' id='DetTarifa'>";
			foreach($arr_paq_det as $clave_tarifa => $vecRangoTiempo )
			{
				// --> Pintar barra del nombre de la tarifa
				echo"
					<tr align='left' style='background-color : #83D8F7'>
						<td colspan='6' style='font-size: 10pt;font-family: verdana;'>
							&nbsp;<img onclick='desplegar(this, \"".$clave_tarifa."\", \"detalle\")' valign='middle' style=' display: inline-block; cursor : pointer'  src='../../images/medical/hce/mas.PNG'>
							&nbsp;&nbsp;<b>TARIFA:</b> ".$arr_empresa[$clave_tarifa]."
							<input type='hidden' id='Hidden".$clave_tarifa."' InputTarifa='si' valor='".$clave_tarifa."' nueva='no'>
						</td>
						<td  style='font-size: 10pt;font-family: verdana;' align='right'><b>VALOR:&nbsp;</b></td>
						<td  style='font-size: 10pt;font-family: verdana;' align='left' id='valorTotalTar".$clave_tarifa."'></td>
						<td class='pad' align='right'>
							<img style='cursor:pointer' tooltip='si' title='<span style=\"font-weight:normal\">Eliminar tarifa</span>' OnClick='EliminarTarifa(this, \"".$clave_tarifa."\", \"no\", \"".$CodPaquete."\");' src='../../images/medical/hce/cancel.PNG'>
						</td>
					</tr>";

				$primerRango 	= 'SI';
				$rangoAnterior 	= '';
				foreach($vecRangoTiempo as $rangoTiempo => $vec_tarifa)
				{
					$rangoSiguiente = '';
					if(next($vecRangoTiempo))
						$rangoSiguiente = key($vecRangoTiempo);

					// echo "<pre>arr_contenido:";
					// print_r($arr_contenido);
					// echo "</pre><br>------------------------------------------";
					// echo "<pre>arr_contenido_tarifa:";
					// print_r($arr_contenido_tarifa);
					// echo "</pre>";
					// $arr_faltantes 		= array_diff_key($arr_contenido, $arr_contenido_tarifa[$clave_tarifa]);
					// echo "<pre>";
					// print_r($arr_faltantes);
					// echo "</pre>";

					// --> Con esto obtengo y asigno los conceptos-procedimientos que faltan por configuar en la tarifa
					$indice_faltante 	= count($vec_tarifa);
					foreach($arr_contenido as $cod_con_pro_falta => $arrCantInfoFaltantes)
					{
						$cod_con_pro_falta2 = explode('|', $cod_con_pro_falta);
						foreach($arrCantInfoFaltantes as $consec => $arrInfoFaltantes)
						{
							if(@!isset($arr_contenido_tarifa[$clave_tarifa][$cod_con_pro_falta][$consec]))
							{
								$vec_tarifa[$indice_faltante]['Paqdetcon'] 		= $cod_con_pro_falta2[0];
								$vec_tarifa[$indice_faltante]['Paqdetpro'] 		= $arrInfoFaltantes['codProcedMerca'];
								$vec_tarifa[$indice_faltante]['Paqdetter'] 		= '';
								$vec_tarifa[$indice_faltante]['Paqdetcan'] 		= '';
								$vec_tarifa[$indice_faltante]['Paqdetvan'] 		= '';
								$vec_tarifa[$indice_faltante]['Paqdetfec'] 		= date("Y-m-d");
								$vec_tarifa[$indice_faltante]['Paqdetvac'] 		= '';
								$vec_tarifa[$indice_faltante]['Paqdetpai'] 		= $arrInfoFaltantes['esDeInventario'];
								$vec_tarifa[$indice_faltante]['consecutivo'] 	= 'nuevo-'.$Consecutivo2;
								$vec_tarifa[$indice_faltante]['Nuevo'] 			= 'si';
								$indice_faltante++;
								$Consecutivo2++;
							}
						}
					}
					echo "
					<tr class='".$clave_tarifa." detalle' rango='".$clave_tarifa."-".$rangoTiempo."' primerRango='".$primerRango."' align='center' style='display:none'>
						<td width='7%'></td>
						<td class='encabezadoTabla'>Concepto</td>
						<td class='encabezadoTabla'>Procedimiento</td>
						<td class='encabezadoTabla'>Tercero</td>
						<td class='encabezadoTabla'>Cantidad</td>
						<td class='encabezadoTabla'>Valor Ant</td>
						<td class='encabezadoTabla'>Fecha Camb</td>
						<td class='encabezadoTabla'>Valor Act</td>
						<td class='encabezadoTabla'>Facturable</td>
					</tr>
					<tr class='".$clave_tarifa." detalle' align='center' style='display:none' rango='".$clave_tarifa."-".$rangoTiempo."' primerRango='".$primerRango."'>
						<td width='7%' colspan='3' align='right'>
							<button onclick='AgregarNuevaTarifa(\"".$CodPaquete."\", \"".$clave_tarifa."\", \"".$clave_tarifa."-".$rangoTiempo."\");' style='font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer'>Agregar Rango</button>
						</td>
						<td colspan='6' class='fondoAmarillo' style='border: 1px solid #CCCCCC;'>
							<table width='100%'>
								<tr>
									<td align='left' width='10%'>";
									if($rangoAnterior != '')
										echo "&nbsp;<img width='18' height='18' style='cursor:pointer' src='../../images/medical/sgc/atras.PNG' onClick='verRango(\"".$clave_tarifa."-".$rangoTiempo."\", \"".$clave_tarifa."-".$rangoAnterior."\")'>";

					echo "			</td>
									<td align='center' width='80%'>
										<b>Tiempo quirúrgico máximo:</b>&nbsp;&nbsp;&nbsp;
										<input type='text' id='inputRango".$clave_tarifa."-".$rangoTiempo."' size='5' value='".$rangoTiempo."' style='height:12px'>&nbsp;<span style='font-size:10px'>(Minutos).</span>
									</td>
									<td align='right' width='10%' id='botonAdelante".$clave_tarifa."-".$rangoTiempo."'>";
									if($rangoSiguiente != '')
										echo "&nbsp;<img width='18' height='18' style='cursor:pointer' src='../../images/medical/sgc/adelante.PNG' adelante='' onClick='verRango(\"".$clave_tarifa."-".$rangoTiempo."\", \"".$clave_tarifa."-".$rangoSiguiente."\")'>";
					echo "				&nbsp<img style='cursor:pointer' tooltip='si' title='<span style=\"font-weight:normal\">Eliminar tiempo quirúrgico</span>' OnClick='EliminarTiempoQqx(\"si\", \"".$clave_tarifa."-".$rangoTiempo."\", \"".$CodPaquete."\", \"".$clave_tarifa."\", \"".$rangoTiempo."\");' src='../../images/medical/hce/cancel.PNG'>
									</td>
								</tr>
							</table>
						</td>
					</tr>";

					$color_f 			= 'fila2';
					$valorActualTotal 	= 0;
					$valorAnteriTotal 	= 0;
					foreach($vec_tarifa as $vec_detalle)
					{
						$consecutivo = $vec_detalle['consecutivo'];
						if($color_f == 'fila1')
							$color_f = 'fila2';
						else
							$color_f = 'fila1';

						$paqArtInventario = (($vec_detalle['Paqdetpai'] == 'on') ? true : false);

						if($paqArtInventario)
							$vec_detalle['Paqdetpro'] = $arr_contenido[$vec_detalle['Paqdetcon']]['codProcedMerca'];

						$nomProcedimiento 			= $arr_procedimi[$vec_detalle['Paqdetpro']];

						//$vec_detalle['Paqdetfac'] 	= (($vec_detalle['Paqdetpai'] == 'on') ? 'N' : $vec_detalle['Paqdetfac']);
						$vec_detalle['Paqdetfac'] 	= $vec_detalle['Paqdetfac'];
						echo "
						<tr rango='".$clave_tarifa."-".$rangoTiempo."' primerRango='".$primerRango."' class='".$clave_tarifa." detalle' style='display:none' nuevo='".$vec_detalle['Nuevo']."' id_registro='".$consecutivo."' NuevaTarifa='no' idInputTarifa='".$clave_tarifa."'>
							<td width='7%'></td>
							<td class='".$color_f." pad' align='center' style='font-size: 8pt;'>
								<input type='hidden' id='Concepto-".$consecutivo."' value='".$vec_detalle['Paqdetcon']."'>
								".$arr_conceptos[$vec_detalle['Paqdetcon']]['nombre']."
							</td>
							<td class='".$color_f." pad' align='center' style='font-size: 8pt;'>
								<input type='hidden' id='Procedimiento-".$consecutivo."' value='".$vec_detalle['Paqdetpro']."'>
								".$nomProcedimiento."
							</td>
							<td class='".$color_f." pad' align='center' style='font-size: 9pt;'>
								<input type='text' ".(($vec_detalle['Paqdetter'] != '') ? "value='".$arr_tercero[$vec_detalle['Paqdetter']]."' nombre='".$arr_tercero[$vec_detalle['Paqdetter']]."' " : "")." valor='".$vec_detalle['Paqdetter']."' size='25' id='Tercero-".$consecutivo."' ".(($arr_conceptos[$vec_detalle['Paqdetcon']]['tipo'] == 'C') ? " validar='si' CargarAutocomplete='terceros' msgError='Digite el nombre' style='font-size: 9pt;' " : " validar='no' style='display:none' " ).">
							</td>
							<td class='".$color_f." pad' align='center'>
								<input type='text' value='".$vec_detalle['Paqdetcan']."' size='5' ".(($paqArtInventario) ? "style='display:none;font-size: 9pt;' validar='no' " : "style='font-size: 9pt;' validar='si'" )." id='Cantidad-".$consecutivo."' class='entero'>
							</td>
							<td class='".$color_f." pad' align='center'>
								<input type='text' onblur='actualizarTotal2(\"".$clave_tarifa."-".$rangoTiempo."\")' value='".$vec_detalle['Paqdetvan']."' size='10' ".(($paqArtInventario ) ? "style='display:none;font-size: 9pt;' validar='no' " : "style='font-size: 9pt;' validar='si'" )." id='ValorAnterior-".$consecutivo."' class='entero'>
							</td>
							<td class='".$color_f." pad' align='center'>
								<input type='text' size='12' ".(($paqArtInventario ) ? "style='display:none;font-size: 9pt;' validar='no' value='0000-00-00' " : "style='font-size: 9pt;' validar='si' value='".$vec_detalle['Paqdetfec']."' " )." id='FechaCambio-".$consecutivo."' CargarAutocomplete='calendario'>
							</td>
							<td class='".$color_f." pad' align='center'>
								<input type='text' onblur='actualizarTotal1(\"".$clave_tarifa."-".$rangoTiempo."\")' value='".$vec_detalle['Paqdetvac']."' valuePre='".$vec_detalle['Paqdetvac']."' size='10' ".(($paqArtInventario ) ? "style='display:none;font-size: 9pt;' validar='no' " : "style='font-size: 9pt;' validar='si'" )." id='ValorActual-".$consecutivo."' class='entero'>
							</td>
							<td class='".$color_f." pad' align='center'>
								<SELECT id='facturable-".$consecutivo."' "/*.(($paqArtInventario) ? " disabled='disabled' " : "")*/.">
									<option ".(($vec_detalle['Paqdetfac'] == 'S' ) ? "SELECTED" : "")." value='S'>SI</option>
									<option ".(($vec_detalle['Paqdetfac'] == 'N' ) ? "SELECTED" : "")." value='N'>NO</option>
								</SELECT>
							</td>
						</tr>";

						$valorActualTotal+= (int)$vec_detalle['Paqdetvac'];
						$valorAnteriTotal+= (int)$vec_detalle['Paqdetvan'];
					}
					// --> Pintar valor total del paquete
					echo"
					<tr rango='".$clave_tarifa."-".$rangoTiempo."' primerRango='".$primerRango."' class='".$clave_tarifa." detalle' align='center' style='display:none'>
						<td colspan='3'></td>
						<td style='font-size: 10pt;font-family: verdana;' colspan='2' align='right'><b>VALOR TOTAL:<b></td>
						<td style='font-size: 10pt;font-family: verdana;' align='left' valorTotalAntDetTar='".$clave_tarifa."-".$rangoTiempo."'>$ ".number_format($valorAnteriTotal, 0, '.', ',')."</td>
						<td></td>
						<td style='font-size: 10pt;font-family: verdana;' align='left' valorTotalDetTar='".$clave_tarifa."-".$rangoTiempo."'>$".number_format($valorActualTotal, 0, '.', ',')."</td>
					</tr>
					<tr rango='".$clave_tarifa."-".$rangoTiempo."' primerRango='".$primerRango."' class='".$clave_tarifa." detalle' align='center' style='display:none'>
						<td colspan='9'>&nbsp;</td>
					</tr>";
					$rangoAnterior 	= $rangoTiempo;
					$primerRango 	= 'NO';
				}
			}
			echo "</table>
			<input type='hidden' id='ConsecutivoParaNuevos' 	value='".$Consecutivo2."'>
			<input type='hidden' id='ConsecutivoTarifas' 		value='0'>
			<input type='hidden' id='consecutivoRangoTiempo' 	value='0'>
			<br>
					<div>
						<input type='button' id='BotonGuardar2' value='Guardar' OnClick='guardar_detalle(\"".$CodPaquete."\");'>
					</div>
				</div>
			</div>";
		}
		echo"<br>
			<table width='95%'>
				<tr>
					<td align='right'>
						<div id='div_mensajesLocal' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
						</div>
					</td>
				</tr>
			</table>";
	}

	function generar_codigo_paquete()
	{
		global $wbasedato;
		global $conex;

		$q_select = "SELECT  concat('CP', MAX(id)+1) AS codigo "
					." FROM ".$wbasedato."_000113 ";
		$res=mysql_query($q_select,$conex) or die ("Error: ".mysql_errno()." - en el query (select maximo): ".$q_select." - ".mysql_error());
		$row = mysql_fetch_array($res);

		$codigopaquete = (($row['codigo'] != '') ?  $row['codigo'] : 'CP1') ;
		return $codigopaquete;
	}

	//-------------------------------------------------------------------------------------------------------------
	//	Funcion que guarda el log, una foto de como estaba el detalle del paquete antes de hacer estos cambios
	//-------------------------------------------------------------------------------------------------------------
	function guardarLog($codpaquete)
	{
		global $wbasedato;
		global $conex;

		$arraLog = array();
		// --> Obtener todo el detalle del paquete
		$sqlDetPaq = "SELECT *
						FROM ".$wbasedato."_000114
					   WHERE Paqdetcod = '".$codpaquete."'
		";
		$resDetPaq = mysql_query($sqlDetPaq, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDetPaq):</b><br>".mysql_error());
		while($rowDetPaq = mysql_fetch_array($resDetPaq, MYSQL_ASSOC))
			$arraLog[] = $rowDetPaq;

		$strinLog = json_encode($arraLog);

		// --> Obtener log anterior
		$logAnt = "";
		$sqlLogAnt = "SELECT Paqlog
						FROM ".$wbasedato."_000113
					   WHERE Paqcod = '".$codpaquete."'
		";
		$resLogAnt = mysql_query($sqlLogAnt, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLogAnt):</b><br>".mysql_error());
		if($rowLogAnt = mysql_fetch_array($resLogAnt))
			$logAnt = $rowLogAnt['Paqlog'];

		$strinLog = $logAnt.">>>".date("Y-m-d")." ".date("H:i:s")." FOTO = ".$strinLog;

		// --> Guardar el log en el encabezado del paquete 000114
		$sqlLog = "UPDATE ".$wbasedato."_000113
					  SET Paqlog = '".$strinLog."'
					WHERE Paqcod = '".$codpaquete."'
		";
		mysql_query($sqlLog, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLog):</b><br>".mysql_error());
	}
	//-----------------------------------------------------------------------
	//	Funcion que guarda la informacion y el contenido basico de un paquete
	//-----------------------------------------------------------------------
	function guardar_detalle_paquete($codpaquete, $paquete)
	{
		global $wbasedato;
		global $conex;
		global $wfecha;
		global $whora;
		$respuesta = array();

		// --> Organizar informacion
		$arr_paquete 			= explode('->', $paquete);
		$arr_elementos_paquete 	= array();
		$indice 				= 0;
		foreach($arr_paquete as $val_elementos)
		{
			$arr_elementos_paquete[$indice] = array();
			$arr_elementos 					= explode('|', $val_elementos);
			foreach($arr_elementos as $campo_valor)
			{
				$campo_valor = explode('=', $campo_valor);
				$arr_elementos_paquete[$indice][$campo_valor[0]] = $campo_valor[1];
			}
			$indice++;
		}

		// --> Guardar log, una foto de como estaba el detalle del paquete antes de hacer estos cambios
		if(count($arr_elementos_paquete)>0)
			guardarLog($codpaquete);

		// --> Guardar el detalle
		foreach($arr_elementos_paquete as $arr_valores)
		{
			// --> Insertar un nuevo elemento al paquete
			if($arr_valores['id'] == 'nuevo')
			{
				if(substr($arr_valores['procedimiento'], 0, 3) == 'PI-' || $arr_valores['procedimiento'] == "" || !isset($arr_valores['procedimiento']))
					$relacioandoConPaqueteArtInv = 'on';
				else
					$relacioandoConPaqueteArtInv = 'off';

				$q_guardar_det ="
				INSERT INTO ".$wbasedato."_000114
								( Medico,	Fecha_data,		Hora_data, 		Paqdetcod, 			Paqdetcon, 						Paqdetpro,								Paqdetcan,						Paqdettar,						Paqdetvac,							Paqdetfec,							Paqdetvan, 								Paqdetest,	Paqdetgen,	Paqdetter,						Paqdetpai,							Paqdetfac,							Paqdetrmm, 							Seguridad 		)
				VALUES ('".$wbasedato."',	'".$wfecha."',	'".$whora."', 	'".$codpaquete."',	'".$arr_valores['concepto']."',	'".$arr_valores['procedimiento']."',	'".$arr_valores['cantidad']."',	'".$arr_valores['Tarifa']."',	'".$arr_valores['valorActual']."',	'".$arr_valores['fechaCambio']."',	'".$arr_valores['valorAnterior']."',	'on',		'off',		'".$arr_valores['Tercero']."',	'".$relacioandoConPaqueteArtInv."',	'".$arr_valores['facturable']."',	'".$arr_valores['rango']."',	'C-".$wbasedato."'	)";

				mysql_query($q_guardar_det,$conex) or die("Error en el query: ".$q_guardar_det."<br>Tipo Error:".mysql_error());
			}
			// --> Actualizar uno ya existente
			else
			{
				$q_guardar_det ="
				UPDATE 	".$wbasedato."_000114
				   SET	Paqdetpro = '".$arr_valores['procedimiento']."',
						Paqdetcan = '".$arr_valores['cantidad']."',
						Paqdettar = '".$arr_valores['Tarifa']."',
						Paqdetvac = '".$arr_valores['valorActual']."',
						Paqdetfec = '".$arr_valores['fechaCambio']."',
						Paqdetvan = '".$arr_valores['valorAnterior']."',
						Paqdetter = '".$arr_valores['Tercero']."',
						Paqdetfac = '".$arr_valores['facturable']."',
						Paqdetrmm = '".$arr_valores['rango']."'
				 WHERE  id 		  = '".$arr_valores['id']."'
				";
				mysql_query($q_guardar_det,$conex) or die("Error en el query: ".$q_guardar_det."<br>Tipo Error:".mysql_error());
			}
		}

		$respuesta['Mensaje'] = 'Grabaci&oacute;n correcta';

		return $respuesta;
	}
	//-----------------------------------------------------------------------
	//	Funcion que guarda la informacion y el contenido basico de un paquete
	//-----------------------------------------------------------------------
	function guardar_paquete($nombrepaquete,$codpaquete,$Paquete, $Tarifa)
	{
		global $wbasedato;
		global $conex;
		global $wfecha;
		global $whora;

		// --> Insertar un nuevo paquete
		if($codpaquete == 'nuevo')
		{
			// --> Guardar el encabezado del paquete
			$Codigo = generar_codigo_paquete();
			$q_guardar="INSERT INTO ".$wbasedato."_000113
									( Medico,			Fecha_data,		Hora_data, 		Paqcod, 		Paqnom,					Paqest,	Seguridad )
							 VALUES ('".$wbasedato."',	'".$wfecha."',	'".$whora."', 	'".$Codigo."',	'".$nombrepaquete."',	'on',	'C-".$wbasedato."')";

			mysql_query($q_guardar,$conex) or die ("Error: ".mysql_errno()." - en el query (select maximo): ".$q_guardar." - ".mysql_error());

			// --> Guardar el detalle del paquete
			$arry_paquete = explode('<>', $Paquete);
			foreach($arry_paquete as $arr_valores_paq)
			{
				if($arr_valores_paq != '')
				{
					$arr_valores_paq = explode('|', $arr_valores_paq);
					foreach($arr_valores_paq as $arr_valores)
					{
						$arr_valores2 = explode('->', $arr_valores);
						$det_val[$arr_valores2[0]] 	= $arr_valores2[1];
					}

					if(substr($det_val['DetPaqProcedimiento'], 0, 3) == 'PI-' || $det_val['DetPaqProcedimiento'] == "" || !isset($det_val['DetPaqProcedimiento']))
						$relacioandoConPaqueteArtInv = 'on';
					else
						$relacioandoConPaqueteArtInv = 'off';

					$q_guardar_det ="
					INSERT INTO ".$wbasedato."_000114
									( Medico,	Fecha_data,		Hora_data, 		Paqdetcod, 		Paqdetcon, 							Paqdetpro, 								Paqdetest,	Paqdetgen,	Paqdetpai,					 		Seguridad )
					VALUES ('".$wbasedato."',	'".$wfecha."',	'".$whora."', 	'".$Codigo."',	'".$det_val['DetPaqConcepto']."',	'".$det_val['DetPaqProcedimiento']."',	'on',		'on',		'".$relacioandoConPaqueteArtInv."',	'C-".$wbasedato."')";

					mysql_query($q_guardar_det,$conex) or die("Error en el query: ".$q_guardar_det."<br>Tipo Error:".mysql_error());
				}
			}
		}
		// --> Actualizar el encabezado del paquete
		else
		{
			$q_update="
			UPDATE ".$wbasedato."_000113
			   SET Paqnom = '".$nombrepaquete."'
			 WHERE Paqcod = '".$codpaquete."' ";
			mysql_query($q_update,$conex) or die("Error en el query: ".$q_update."<br>Tipo Error:".mysql_error());

			// --> Actualizar el detalle del paquete
			$arry_paquete = explode('<>', $Paquete);
			foreach($arry_paquete as $arr_valores_paq)
			{
				if($arr_valores_paq != '')
				{
					$arr_valores_paq = explode('|', $arr_valores_paq);
					foreach($arr_valores_paq as $arr_valores)
					{
						$arr_valores2 = explode('->', $arr_valores);
						$det_val[$arr_valores2[0]] 	= $arr_valores2[1];
					}

					if(substr($det_val['DetPaqProcedimiento'], 0, 3) == 'PI-' || $det_val['DetPaqProcedimiento'] == "" || !isset($det_val['DetPaqProcedimiento']))
						$relacioandoConPaqueteArtInv = 'on';
					else
						$relacioandoConPaqueteArtInv = 'off';

					if($det_val['id_registro'] != 'nuevo')
					{
						// --> Consultar concepto y procedimiento anterior
						$sqlConPro = "SELECT Paqdetcon, Paqdetpro
										FROM ".$wbasedato."_000114
									   WHERE id = '".$det_val['id_registro']."'
						";
						$resConPro = mysql_query($sqlConPro,$conex) or die("Error en el query: ".$sqlConPro."<br>Tipo Error:".mysql_error());
						$rowConPro = mysql_fetch_array($resConPro);

						// --> Actualizar el detalle general
						$q_det ="
						UPDATE 	".$wbasedato."_000114
						   SET	Paqdetcon = '".$det_val['DetPaqConcepto']."',
								Paqdetpro = '".$det_val['DetPaqProcedimiento']."'
						 WHERE  id = '".$det_val['id_registro']."'
						";
						mysql_query($q_det,$conex) or die("Error en el query: ".$q_det."<br>Tipo Error:".mysql_error());

						// --> Actualizar el detalle por tarifa, solo para los conceptos que no son de inventario
						if($relacioandoConPaqueteArtInv == 'off')
						{
							$q_det2 ="
							UPDATE ".$wbasedato."_000114
							   SET Paqdetcon = '".$det_val['DetPaqConcepto']."',
								   Paqdetpro = '".$det_val['DetPaqProcedimiento']."'
							 WHERE Paqdetcod = '".$codpaquete."'
							   AND Paqdetcon = '".$rowConPro['Paqdetcon']."'
							   AND Paqdetpro = '".$rowConPro['Paqdetpro']."'
							   AND Paqdetest = 'on'
							   AND Paqdetgen != 'on'
							";
							mysql_query($q_det2,$conex) or die("Error en el query: ".$q_det2."<br>Tipo Error:".mysql_error());

						}
					}
					else
					{

						$q_det ="
						INSERT INTO ".$wbasedato."_000114
										( Medico,	Fecha_data,		Hora_data, 		Paqdetcod, 			Paqdetcon, 							Paqdetpro, 								Paqdetest, 	Paqdetgen,	Paqdetpai,							Seguridad )
						VALUES ('".$wbasedato."',	'".$wfecha."',	'".$whora."', 	'".$codpaquete."',	'".$det_val['DetPaqConcepto']."',	'".$det_val['DetPaqProcedimiento']."',	'on',		'on',		'".$relacioandoConPaqueteArtInv."',	'C-".$wbasedato."')
						";
						mysql_query($q_det,$conex) or die("Error en el query: ".$q_det."<br>Tipo Error:".mysql_error());
					}
				}
			}
		}
		$respuesta['Codigo']  = (($codpaquete == 'nuevo') ? $Codigo : $codpaquete);
		$respuesta['Mensaje'] = (($codpaquete == 'nuevo') ? 'Paquete Creado': 'Paquete Actualizado');
		return $respuesta;
	}
	//----------------------------------------------
	// 	Contiene la lista de paquete existentes
	//----------------------------------------------
	function contenedor_lista_paquetes()
	{
		global $wbasedato;
		global $conex;

		echo "
		<table width='100%'>
			<tr>
				<td>
					<table width='100%'>
						<tr>
							<td style='color: #000000;font-size: 10pt;font-weight: bold;'>Registros:<span id='numRegistros'></span></td>
							<td align='right' width='90%'><input type='button' value='Nuevo paquete' OnClick='VerPaquete(\"nuevo\")'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width='100%'class='Bordegris fila2' style='padding:2px; font-size: 8pt;' >
						<tr style='font-weight:bold'>
							<td><span style='font-size: 10pt;font-weight:bold;color:#999999'>Buscar:</span></td>
							<td align='center' >
								Código: <input type='text' style='width:100px' id='buscCodPaquete'>
							</td>
							<td align='center' >
								Nombre: <input type='text' style='width:400px' id='buscNomPaquete'>
							</td>
							<td align='center' >
								Estado: <select id='buscEstPaquete'>
											<option value=''>	Todos		</option>
											<option value='on'>	Activos		</option>
											<option value='off'>Inactivos	</option>
										</select>
							</td>
							<td>
								<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt' onClick='cargarlistapaquetes()' >
									<img width='15' height='15' src='../../images/medical/HCE/lupa.PNG' title='Buscar'>
								</button>
							</td>
						</tr>
					</table>
					<br>
				</td>
			</tr>
			<tr>
				<td class='Bordegris'>
					<div id='tabla_paquetes'>";
		echo 			ver_lista();
		echo"		</div>
				</td>
			</tr>
		</table>";
	}
	function ver_lista($codigo='%', $nombre='%', $estado='%')
	{
		global $wbasedato;
		global $conex;
		global $wfecha;
		global $whora;

		$q = " SELECT  Paqcod , Paqnom, Paqest
				 FROM  ".$wbasedato."_000113
				WHERE  Paqcod LIKE '%".$codigo."%'
				  AND  Paqnom LIKE '%".$nombre."%'
				  AND  Paqest LIKE '%".$estado."%'
		";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (mostrar lista ): ".$q." - ".mysql_error());

		echo "
		<table width='100%' id='tabla_lista'>
			<tr class='encabezadoTabla'>
				<td>Código</td><td>Nombre</td><td>Estado</td>
			</tr>";
		$color_f = 'fila1';

		if(mysql_num_rows($res) > 0)
		{
			while($row = mysql_fetch_array($res))
			{
				if($color_f == 'fila2')
					$color_f = "fila1";
				else
					$color_f = "fila2";

				echo "
				<tr class='".$color_f."' OnClick='VerPaquete(\"".$row['Paqcod']."\");' style='cursor:pointer'>
					<td>".$row['Paqcod']."</td>
					<td>".$row['Paqnom']."</td>
					<td>".$row['Paqest']."</td>
				</tr>";
			}
		}
		else
			echo "<tr><td colspan='3' class='fila1' align='center'>No existen paquetes creados</td></tr>";

		echo "</table>";
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
		case 'guardar_detalle_paquete':
		{
			$respuesta = guardar_detalle_paquete($codpaquete, $paquete);
			echo json_encode($respuesta);
			break;
		}
		case 'guardar_paquete':
		{
			$respuesta = guardar_paquete($nombrepaquete,$codpaquete,$Paquete, $Tarifa);
			echo json_encode($respuesta);
			break;
			return;
		}
		case 'ver_lista':
		{
			ver_lista($codigo, $nombre, $estado);
			break;
			return;
		}
		case 'crear_hidden_procedimientos':
		{
			echo json_encode(Obtener_array_procedimientos_x_concepto($CodConcepto));
			break;
			return;
		}
		case 'crear_hidden_procedimientos2':
		{
			echo json_encode(Obtener_array_procedimientos_x_concepto_simple($CodConcepto));
			break;
			return;
		}
		case 'VerPaquete':
		{
			nuevo_paquete($CodPaquete);
			break;
			return;
		}
		case 'CambiarEstado':
		{
			$q_estado = "UPDATE ".$wbasedato."_000113
							SET Paqest = '".$estado."'
						  WHERE Paqcod = '".$CodPaquete."'
			";
			$res_estado = mysql_query($q_estado,$conex) or die("Error en el query: ".$q_estado."<br>Tipo Error:".mysql_error());
			if($res_estado > 0)
					echo true;
				else
					echo false;

			break;
			return;
		}
		case 'eliminar_tarifa':
		{
			$q_eliminar = "DELETE FROM ".$wbasedato."_000114
							WHERE Paqdetcod = '".$paquete."'
							  AND Paqdettar = '".$tarifa."'
							  AND Paqdetgen != 'on'
			";
			$res_eliminar = mysql_query($q_eliminar,$conex) or die("Error en el query: ".$q_eliminar."<br>Tipo Error:".mysql_error());
			break;
		}
		case 'eliminar_detalle':
		{
			// --> Obtener el codigo del paquete, concepto y procedimiento
			$q_getCod = "SELECT Paqdetcod, Paqdetcon, Paqdetpro
						   FROM ".$wbasedato."_000114
					      WHERE id = '".$id_registro."'
			";
			$res_getCod = mysql_query($q_getCod,$conex) or die("Error en el query: ".$q_getCod."<br>Tipo Error:".mysql_error());
			if($row_getCod = mysql_fetch_array($res_getCod))
			{
				// --> Elimino el elemento para todas las tarifas que lo tengan
				$q_eliminar = "SELECT id
				                 FROM ".$wbasedato."_000114
								WHERE Paqdetcod = '".$row_getCod['Paqdetcod']."'
								  AND Paqdetcon = '".$row_getCod['Paqdetcon']."'
								  AND Paqdetpro = '".$row_getCod['Paqdetpro']."'
								  AND Paqdetgen != 'on'
							 GROUP BY Paqdettar, Paqdetrmm
				";
				$res_eliminar = mysql_query($q_eliminar,$conex) or die("Error en el query: ".$q_eliminar."<br>Tipo Error:".mysql_error());
				while($row_eliminar = mysql_fetch_array($res_eliminar))
				{
					$sqlDelete = "
					DELETE
					  FROM ".$wbasedato."_000114
					 WHERE id = '".$row_eliminar['id']."'
					";
					mysql_query($sqlDelete,$conex) or die("Error en el query: ".$sqlDelete."<br>Tipo Error:".mysql_error());
				}
			}

			$q_eliminar = "DELETE FROM ".$wbasedato."_000114
							WHERE id = '".$id_registro."'
			";
			$res_eliminar = mysql_query($q_eliminar,$conex) or die("Error en el query: ".$q_eliminar."<br>Tipo Error:".mysql_error());
			if($res_eliminar > 0)
				echo true;
			else
				echo false;
			break;
		}
		case 'Agregar_nueva_tarifa':
		{
			// --> Consultar lo elementos basicos del paquete
			$q_info = "
			   SELECT  Grudes, Grucod, Grutip, Pronom, Paqdetpro, Paqdetpai
				 FROM  ".$wbasedato."_000113, ".$wbasedato."_000114 AS A LEFT JOIN ".$wbasedato."_000103 AS B ON A.Paqdetpro = B.Procod , ".$wbasedato."_000200
				WHERE  Paqcod 		= '".$CodPaquete."'
				  AND  Paqcod 		= Paqdetcod
				  AND  Paqdetest	= 'on'
				  AND  Paqdetgen	= 'on'
				  AND  Paqdetcon	= Grucod
			 ORDER BY  Grucod, Paqdetpro
			";

			$res_info = mysql_query($q_info,$conex) or die("Error en el query: ".$q_info."<br>Tipo Error:".mysql_error());
			if(mysql_num_rows($res_info) > 0)
			{
				if($Tipo == 'Tarifa')
				{
					//$claveTarifa 	= "nueva_tarifa".$ConsecutivoTarifas;
					$primerRango 	= 'SI';
					$idInputTarifa 	= "DetPaqTarifa".$ConsecutivoTarifas;
					$claveTarifa 	= "DetPaqTarifa".$ConsecutivoTarifas;
					$NuevaTarifa	= "si";
				}
				else
				{
					$claveTarifa 	= $Tarifa;
					$primerRango 	= 'NO';
					$idInputTarifa 	= $Tarifa;
					$NuevaTarifa	= (strpos($claveTarifa, "DetPaqTarifa") === false) ? "no" : "si";
				}

				$rangoSiguiente 	= "";
				$html 				= "";

				if($Tipo == 'Tarifa')
				{
					$html.= "
					<tr align='left' style='background-color : #83D8F7'>
						<td colspan='6' class='pad' style='font-size: 10pt;font-family: verdana;'>
							&nbsp;<img onclick='desplegar(this, \"".$claveTarifa."\", \"detalle\")' valign='middle' style=' display: inline-block; cursor : pointer'  src='../../images/medical/hce/mas.PNG'>
							&nbsp;&nbsp;<b>TARIFA:</b>
							<input type='text' valor='' size='35' style='font-size: 9pt;' id='DetPaqTarifa".$ConsecutivoTarifas."' msgError='Digite la tarifa' CargarAutocomplete='tarifa' InputTarifa='si' nueva='si'>
						</td>
						<td  style='font-size: 10pt;font-family: verdana;' align='right'><b>VALOR:&nbsp;</b></td>
						<td  style='font-size: 10pt;font-family: verdana;' align='left' id='valorTotalTarDetPaqTarifa".$ConsecutivoTarifas."'>$0</td>
						<td class='pad' align='right'>
							<img style='cursor:pointer' OnClick='EliminarTarifa(this, \"".$claveTarifa."\", \"si\", \"".$CodPaquete."\");' src='../../images/medical/hce/cancel.PNG'>
						</td>
					</tr>";
				}

				$html.="
				<tr class='".$claveTarifa." detalle' rango='".$claveTarifa."-".$rangoTiempo."' align='center' style='display:none' primerRango='".$primerRango."'>
					<td width='7%'></td>
					<td class='encabezadoTabla'>Concepto</td>
					<td class='encabezadoTabla'>Procedimiento</td>
					<td class='encabezadoTabla'>Tercero</td>
					<td class='encabezadoTabla'>Cantidad</td>
					<td class='encabezadoTabla'>Valor Ant</td>
					<td class='encabezadoTabla'>Fecha Camb</td>
					<td class='encabezadoTabla'>Valor Act</td>
					<td class='encabezadoTabla'>Facturable</td>
				</tr>
				<tr class='".$claveTarifa." detalle' rango='".$claveTarifa."-".$rangoTiempo."' align='center' style='display:none' rango='".$claveTarifa."-".$rangoTiempo."' primerRango='".$primerRango."'>
					<td width='7%' colspan='3' align='right'>
						<button onclick='AgregarNuevaTarifa(\"".$CodPaquete."\", \"".$claveTarifa."\", \"".$claveTarifa."-".$rangoTiempo."\");' style='font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer'>Agregar Rango</button>
					</td>
					<td colspan='6' class='fondoAmarillo' style='border: 1px solid #CCCCCC;'>
						<table width='100%'>
							<tr>
								<td align='left' width='10%'>";
								if($rangoAnterior != '')
									$html.= "&nbsp;<img width='18' height='18' style='cursor:pointer' src='../../images/medical/sgc/atras.PNG' onClick='verRango(\"".$claveTarifa."-".$rangoTiempo."\", \"".$rangoAnterior."\")'>";

				$html.= "		</td>
								<td align='center' width='80%'>
									<b>Tiempo quir&uacute;rgico m&aacute;ximo:</b>&nbsp;&nbsp;&nbsp;
									<input type='text' id='inputRango".$claveTarifa."-".$rangoTiempo."' size='5' value='0' style='height:12px'>&nbsp;<span style='font-size:10px'>(Minutos).</span>
								</td>
								<td align='right' width='10%' id='botonAdelante".$claveTarifa."-".$rangoTiempo."'>";
								if($rangoSiguiente != '')
									$html.= "&nbsp;<img width='18' height='18' style='cursor:pointer' src='../../images/medical/sgc/adelante.PNG' adelante='' onClick='verRango(\"".$claveTarifa."-".$rangoTiempo."\", \"".$claveTarifa."-".$rangoSiguiente."\")'>";
				$html.= "		&nbsp<img style='cursor:pointer' tooltip='si' title='<span style=\"font-weight:normal\">Eliminar tiempo quir&uacute;rgico</span>' OnClick='EliminarTiempoQqx(\"no\", \"".$claveTarifa."-".$rangoTiempo."\", \"\", \"".$claveTarifa."\", \"\");' src='../../images/medical/hce/cancel.PNG'>
								</td>
							</tr>
						</table>
					</td>
				</tr>";
			}

			$color_f 		= 'fila2';
			while($row_detalles = mysql_fetch_array($res_info))
			{
				$Consecutivo++;
				$id_registro = 'nuevo-'.$Consecutivo;

				if($color_f == 'fila1')
					$color_f = 'fila2';
				else
					$color_f = 'fila1';

				if($row_detalles['Grutip'] == 'P')
				{
					$validar	= 'no';
					$pedirTer 	= 'display:none;';
				}
				else
				{
					$validar	= 'si';
					$pedirTer 	= '';
				}

				$html.="
				<tr class='".$claveTarifa." detalle' rango='".$claveTarifa."-".$rangoTiempo."' style='display:none' nuevo='si' id_registro='".$id_registro."' NuevaTarifa='".$NuevaTarifa."' idInputTarifa='".$idInputTarifa."' primerRango='".$primerRango."'>
					<td width='7%'>
						<input type='hidden' id='Concepto-".$id_registro."'			value='".$row_detalles['Grucod']."'>
						<input type='hidden' id='Procedimiento-".$id_registro."'	value='".$row_detalles['Paqdetpro']."'>
					</td>
					<td class='".$color_f." pad' align='center' style='font-size: 8pt;'>
						".$row_detalles['Grudes']."
					</td>
					<td class='".$color_f." pad' align='center' style='font-size: 8pt;'>
						".(($row_detalles['Paqdetpai'] == 'on' ) ? '' : utf8_decode($row_detalles['Pronom']))."
					</td>
					<td class='".$color_f." pad' align='center' style='font-size: 9pt;'>
						<input type='text' value='' valor='' size='25' validar='".$validar."' style='".$pedirTer."font-size: 9pt;' id='Tercero-".$id_registro."' CargarAutocomplete='terceros' msgError='Digite el nombre'>
					</td>
					<td class='".$color_f." pad' align='center'>
						<input type='text' value='' size='5' ".(($row_detalles['Paqdetpai'] == 'on' ) ? "style='display:none;font-size: 9pt;' validar='no' " : "style='font-size: 9pt;' validar='si'" )." id='Cantidad-".$id_registro."' class='entero'>
					</td>
					<td class='".$color_f." pad' align='center'>
						<input type='text' onblur='actualizarTotal2(\"".$claveTarifa."-".$rangoTiempo."\")' value='' size='10' ".(($row_detalles['Paqdetpai'] == 'on' ) ? "style='display:none;font-size: 9pt;' validar='no' " : "style='font-size: 9pt;' validar='si'" )." id='ValorAnterior-".$id_registro."' class='entero'>
					</td>
					<td class='".$color_f." pad' align='center'>
						<input type='text' size='12' ".(($row_detalles['Paqdetpai'] == 'on' ) ? "style='display:none;font-size: 9pt;' validar='no' value='0000-00-00' " : "style='font-size: 9pt;' validar='si' value='".$wfecha."' " )." id='FechaCambio-".$id_registro."' CargarAutocomplete='calendario'>
					</td>
					<td class='".$color_f." pad' align='center'>
						<input type='text' onblur='actualizarTotal1(\"".$claveTarifa."-".$rangoTiempo."\")' value='' size='10' ".(($row_detalles['Paqdetpai'] == 'on' ) ? "style='display:none;font-size: 9pt;' validar='no' " : "style='font-size: 9pt;' validar='si'" )." id='ValorActual-".$id_registro."' class='entero'>
					</td>
					<td class='".$color_f." pad' align='center'>
						<SELECT id='facturable-".$id_registro."' "/*.(($row_detalles['Paqdetpai'] == 'on' ) ? " disabled='disabled' " : "")*/.">
							<option ".(($row_detalles['Paqdetpai'] != 'on' ) ? "SELECTED" : "")." value='S'>SI</option>
							<option ".(($row_detalles['Paqdetpai'] == 'on' ) ? "SELECTED" : "")." value='N'>NO</option>
						</SELECT>
					</td>
				</tr>";
			}
			$html.="
				<tr class='".$claveTarifa." detalle' rango='".$claveTarifa."-".$rangoTiempo."' align='center' style='display:none' primerRango='".$primerRango."'>
					<td colspan='3'></td>
					<td style='font-size: 10pt;font-family: verdana;' colspan='2' align='right'><b>VALOR TOTAL:<b></td>
					<td style='font-size: 10pt;font-family: verdana;' align='left' valorTotalAntDetTar='".$claveTarifa."-".$rangoTiempo."'>$0</td>
					<td></td>
					<td style='font-size: 10pt;font-family: verdana;' align='left' valorTotalDetTar='".$claveTarifa."-".$rangoTiempo."'>$0</td>
				</tr>
				<tr class='".$claveTarifa." detalle' rango='".$claveTarifa."-".$rangoTiempo."' align='center' style='display:none' primerRango='".$primerRango."'>
					<td colspan='9'>&nbsp;</td>
				</tr>";

			$data['html'] 				= $html;
			$data['ConsecutivoTarifas'] = $ConsecutivoTarifas;
			$data['consecutivo'] 		= $Consecutivo;

			echo json_encode($data);
			break;
		}
		case 'conceptoDeInvetarios':
		{
			$qTipoConcepto = "SELECT Gruinv
							    FROM ".$wbasedato."_000200
							   WHERE Grucod = '".$CodConcepto."'
			";
			$rTipoConcepto 		= mysql_query($qTipoConcepto,$conex) or die("Error en el query: ".$qTipoConcepto."<br>Tipo Error:".mysql_error());
			$rowrTipoConcepto	= mysql_fetch_array($rTipoConcepto);
			echo trim($rowrTipoConcepto['Gruinv']);
			break;
		}
		case 'obtenerUltimoCodigoPaqInsumos':
		{
			$qUltimoPaqIns = "SELECT MAX(Eincop) AS codigo
							    FROM ".$wbasedato."_000190
							   WHERE Einest = 'on'
			";
			$rUltimoPaqIns 		= mysql_query($qUltimoPaqIns,$conex) or die("Error en el query: ".$qUltimoPaqIns."<br>Tipo Error:".mysql_error());
			$rowUltimoPaqIns	= mysql_fetch_array($rUltimoPaqIns);
			echo $rowUltimoPaqIns['codigo'];
			break;
		}
		case 'eliminarPaqueteInsumos':
		{
			// --> Elimino el encabezado del paquete de insumos
			$q_eliminar = "DELETE FROM ".$wbasedato."_000190
							WHERE Eincop = '".$codPaqInsumos."'
							  AND Einpaq = 'on'
			";
			$res_eliminar = mysql_query($q_eliminar,$conex) or die("Error en el query: ".$q_eliminar."<br>Tipo Error:".mysql_error());

			// --> Elimino el detalle del paquete de insumos
			$q_eliminarDet = "DELETE FROM ".$wbasedato."_000191
							   WHERE Dincop = '".$codPaqInsumos."'
			";
			$res_eliminar = mysql_query($q_eliminarDet,$conex) or die("Error en el query: ".$q_eliminarDet."<br>Tipo Error:".mysql_error());
			break;
		}
		case 'eliminarRango':
		{
			// --> Elimino el detalle del paquete para el rango de tiempo especifico
			$q_eliminar = "DELETE FROM ".$wbasedato."_000114
							WHERE Paqdetcod = '".$paquete."'
							  AND Paqdettar = '".$tarifa."'
							  AND Paqdetrmm = '".$rangoTiempo."'
			";
			$res_eliminar = mysql_query($q_eliminar,$conex) or die("Error en el query: ".$q_eliminar."<br>Tipo Error:".mysql_error());
			if($res_eliminar > 0)
				echo true;
			else
				echo false;
			break;
		}
		case 'number_format':
		{
			echo number_format($valor, 0, '.', ',');
			break;
		}
	}
	return;
}
//=======================================================================================================================================================
//		F I N   F I L T R O S   A J A X
//=======================================================================================================================================================


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else
{
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	?>
	<html>
	<head>
	  <title>Paquetes</title>
	</head>
		<link rel="stylesheet" href="../../../include/ips/facturacionERP.css" />
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<!-- Octubre 19 de 2020 -->
		<!-- <script src="<?=$URL_ACTUAL?>procesos/MarcaDeAguaERP.js" type="text/javascript"></script> -->
		<script src="../../ips/procesos/MarcaDeAguaERP.js" type="text/javascript"></script>
		<script src="../../../include/ips/funcionInsumosqxERP.js" type="text/javascript"></script>
		<script src="../../../include/root/toJson.js" type="text/javascript"></script>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	$(document).ready(function() {
		ajustarTamañoLista(500);
		console.log("->"+"<?=$URL_ACTUAL?>");
	});

	//----------------------------------------------------------
	//	Pintar formulario del paquete
	//----------------------------------------------------------
	function VerPaquete(CodPaquete)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'VerPaquete',
			wemp_pmla:				$('#wemp_pmla').val(),
			CodPaquete:				CodPaquete
		}
		,function(respuesta){
			$("*").dialog("destroy");
			$( '#divDetalleArticulos' ).remove();
			$('#VerPaquete').html(respuesta);
			$('#contenedorVerPaquete').show(400);

			// --> Activar acordeones
			$( "#accordionDatosBasicos" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});
			$( "#accordionDetalle" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});


			// --> se carga la marca de agua
			marcarAqua( '', 'msgError', 'campoRequerido' );
			iniciarMarcaAqua();

			cargar_elementos_datapicker();
			var NumDetalles = $('#num_detalles').val();
			for( var x = 0; x < NumDetalles; x++)
			{
				// --> Cargar autocomplete de conceptos y procedimientos
				crear_autocomplete('hidden_concepto', 'SI', 'DetPaqConcepto'+x, 'CargarProcedimientos', 'DetPaqProcedimiento'+x);
				crear_autocomplete_procedimientos2('DetPaqProcedimiento'+x, $('#DetPaqConcepto'+x).attr('valor'));
			}

			// --> cargar datapicker
			$("[CargarAutocomplete=calendario]").each(function(){
				$(this).datepicker();
			});
			// --> cargar autocomplete de terceros
			$("[CargarAutocomplete=terceros]").each(function(){
				var id_input_tercero = $(this).attr('id');
				crear_autocomplete('hidden_terceros', 'SI', id_input_tercero, 'NO', '');
			});
			activar_regex($('#DetTarifa'));

			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			// --> Pintar valores totales del paquete en la barra del nombre de la tarifa.
			/*$("#DetTarifa").find("[valorTotalDetTar]:eq(0)").each(function(){
				$("#valorTotalTar"+$(this).attr("valorTotalDetTar")).html($(this).text());
			});*/

			ajustarTamañoLista(200);
		});

	}
	//--------------------------------------------------
	//	Cargar calendario
	//--------------------------------------------------
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
	//------------------------------------------------------------------------
	// Funcion que carga un autocomplete en un input
	//------------------------------------------------------------------------
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
			ArraySource[index].nombre = ArrayValores[CodVal];
		}

		CampoCargar = CampoCargar.split('|');
		$.each( CampoCargar, function(key, value){
			$( "#"+value ).autocomplete({
				minLength: 	0,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+value ).val(ui.item.label);
					$( "#"+value ).attr('valor', ui.item.value);
					$( "#"+value ).attr('nombre', ui.item.nombre);
					switch(AccionSelect)
					{
						case 'CargarProcedimientos':
						{
							// --> Obtener si el concepto es de inventarios
							$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
							{
								consultaAjax:   		'',
								accion:         		'conceptoDeInvetarios',
								wemp_pmla:				$('#wemp_pmla').val(),
								CodConcepto:			ui.item.value
							}
							,function(respuesta){
								if($.trim(respuesta) == 'on')
								{
									var Prefijo		= $( "#"+value ).attr('prefijo');
									var Consecutivo	= $( "#"+value ).attr('id').replace(Prefijo,'');
									Consecutivo		= parseInt(Consecutivo);

									// --> Abri ventana modal para ingresar los articulos, esta funcion esta en la libreria funcionInsumosqxERP.js
									ventana_insumo('grabar', 'divDetalleArticulos', 'si', '','*','*','','','', '', '', '*', 'on', '');
									$("#divDetalleArticulos").attr("consecutivoAbierto", Consecutivo);
									// --> Ocultar el campo de procedimiento y mostrar un boton para abrir la ventana de articulos
									$("#"+CampoProcedimiento).hide();
									$('#verDetalle'+Consecutivo).show();

									$("#"+CampoProcedimiento).attr("manejArt", "si");
									$("#"+CampoCargar).attr("manejArt", "si");
								}
								else
								{
									crear_autocomplete_procedimientos(CampoProcedimiento, ui.item.value, true);
									$("#"+CampoProcedimiento).show();
								}
							});
							return false;
							break;
						}
						case 'VerificarTarifaDuplicada':
						{
							$("[InputTarifa=si]").each(function(){
								if($(this).attr("valor") == ui.item.value && $(this).attr("id") != $( "#"+value ).attr('id'))
								{
									alert("Esta tarifa ya está configurada en este paquete");
									$( "#"+value ).val('');
									$( "#"+value ).attr('valor', '');
								}
							});
							return false;
							break;
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
					$(this).val($(this).attr("valor")+"-"+$(this).attr("nombre"));
				}
			}
		});
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function crear_autocomplete_procedimientos(Campo, CodConcepto, Show)
	{
        $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'crear_hidden_procedimientos',
			wemp_pmla:				$('#wemp_pmla').val(),
			CodConcepto:			CodConcepto
		}
		,function(respuesta){
			if(Show)
				$( "#"+Campo ).show(300);

			crear_autocomplete(respuesta, 'NO', Campo);
		});
	}
	function crear_autocomplete_procedimientos2(Campo, CodConcepto)
	{
        $.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'crear_hidden_procedimientos2',
			wemp_pmla:				$('#wemp_pmla').val(),
			CodConcepto:			CodConcepto
		}
		,function(respuesta){
			crear_autocomplete(respuesta, 'NO', Campo);
		});
	}

	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function AgregarFilaCampos(Tabla)
	{
		var Agregar		= 'SI';
		var NuevaFila	= $("#"+Tabla+" tr:last").clone();
		var Consecutivo	= NuevaFila.attr('id').replace('Detalle', '');
		Consecutivo		= parseInt(Consecutivo)+1;
		NuevaFila.attr("id",'Detalle'+Consecutivo);
		NuevaFila.attr("id_registro", 'nuevo');
		var coloFila = NuevaFila.attr("class");
		NuevaFila.attr("class", ((coloFila=="fila1") ? "fila2" : "fila1"));
		NuevaFila.find('input[type]').each(function(){
			if(($(this).val() == '' || $(this).val() == $(this).attr('msgError')) && ($(this).attr('manejArt') == undefined))
			{
				Agregar = 'NO';
			}
			else
			{
				var NuevoInput 		= $(this).clone();
				var PrefijoInput	= NuevoInput.attr('prefijo');
				var ConsecutivoAnt	= NuevoInput.attr('id').replace(PrefijoInput,'');
				ConsecutivoAnt		= parseInt(ConsecutivoAnt);
				ConsecutivoNew 		= ConsecutivoAnt+1;
				$(this).attr("id", PrefijoInput+ConsecutivoNew);
				$('#verDetalle'+ConsecutivoAnt, NuevaFila).attr("consecutivo", ConsecutivoNew);
				$('#verDetalle'+ConsecutivoAnt, NuevaFila).attr("id", 'verDetalle'+ConsecutivoNew);
				$('#verDetalle'+ConsecutivoNew, NuevaFila).attr("onClick", "verDetalleArticulos(this, \"\")");
				$('#verDetalle'+ConsecutivoNew, NuevaFila).hide();
				$('#DetPaqProcedimiento'+ConsecutivoNew, NuevaFila).attr("valor", "");
				$('#eliminarDetalle'+ConsecutivoAnt, NuevaFila).attr("id", "eliminarDetalle"+ConsecutivoNew);
				$('#imgGuardado', NuevaFila).attr("src", "../../images/medical/sgc/grabar_des.png");
				$('#imgGuardado', NuevaFila).attr("title", "Pendiente de guardar");
				$('#imgGuardado', NuevaFila).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

				if($(this).attr('CargarAutocomplete'))
				{
					switch($(this).attr('CargarAutocomplete'))
					{
						case 'conceptos':
						{
							setTimeout(function(){
								crear_autocomplete('hidden_concepto', 'SI', PrefijoInput+ConsecutivoNew, 'CargarProcedimientos', 'DetPaqProcedimiento'+ConsecutivoNew);
							}, 1000);
							break;
						}
						case 'calendario':
						{
							var NewDetPaqFechaCambio = '<input type="text" id="'+PrefijoInput+ConsecutivoNew+'"  mensajeobligatorio="la fecha de cambio" cargarautocomplete="calendario" prefijo="DetPaqFechaCambio" style="font-size: 9pt;" size="10" />';
							$(this).parent().html(NewDetPaqFechaCambio);
							setTimeout(function(){
							$("#"+PrefijoInput+ConsecutivoNew).datepicker();
							}, 2000);
							break;
						}
					}
				}

				if($(this).attr('name'))
				{
					var NewName   	= $(this).attr('name').replace(ConsecutivoAnt, ConsecutivoNew);
					$(this).attr("name", NewName);
				}

				$(this).removeAttr('aqua');
				$(this).removeAttr('manejArt');
				$(this).val('');
				$(this).show();
			}
		});

		if(Agregar == 'SI')
		{
			$('#'+Tabla).append(NuevaFila);
			marcarAqua( NuevaFila, 'msgError', 'campoRequerido' );
			iniciarMarcaAqua( NuevaFila );
			activar_regex(NuevaFila);
			$('#num_detalles').val(parseInt($('#num_detalles').val())+1);
		}

		// --> Redimecionar el tamaño del acordeon
		$( "#accordionDetalle" ).accordion("destroy");
		$( "#accordionDetalle" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
		// --> Redimecionar el tamaño del acordeon de datos basicos
		$( "#accordionDatosBasicos" ).accordion("destroy");
		$( "#accordionDatosBasicos" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
	}
	function activar_regex(Contenedor)
	{
		// --> Validar enteros
		$('.entero', Contenedor).keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function EliminarTr(Elemento, CodPaquete)
	{
		if($('[id_registro]').length > 1)
		{
			Elemento 			= $(Elemento);
			var codPaqInsumos 	= Elemento.attr("codPaqInsumos");
			var id_registro 	= Elemento.parent().parent().attr('id_registro');
			// --> Debo quitar la restriccion visualmente y tambien quitarla en la BD
			if(id_registro != 'nuevo')
			{
				if(confirm('¿Esta seguro que desea eliminar este elemento del paquete?'))
				{
					$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
					{
						consultaAjax:   	'',
						accion:         	'eliminar_detalle',
						wemp_pmla:			$('#wemp_pmla').val(),
						id_registro:		id_registro
					}
					,function(respuesta){
						if(respuesta)
						{
							if(codPaqInsumos != '')
								eliminarPaqueteInsumos(codPaqInsumos);

							$(Elemento).parent().parent().remove();
							cargarlistapaquetes();
							VerPaquete(CodPaquete);
						}
					});
				}
			}
			// --> Debo quitar la restriccion solo visualmente
			else
			{
				Elemento.parent().parent().remove();
				if(codPaqInsumos != '')
					eliminarPaqueteInsumos(codPaqInsumos);
			}
			$('#num_detalles').val(parseInt($('#num_detalles').val())-1);
		}
	}
	//---------------------------------------------------------
	// 	Eliminar mercado o paquete de insumos quirurgicos
	//---------------------------------------------------------
	function eliminarPaqueteInsumos(codPaqInsumos)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   	'',
			accion:         	'eliminarPaqueteInsumos',
			wemp_pmla:			$('#wemp_pmla').val(),
			codPaqInsumos:		codPaqInsumos
		}
		,function(respuesta){
		});
	}
	//---------------------------------------------------------
	// 	Eliminar tarifa
	//---------------------------------------------------------
	function EliminarTarifa(Elemento, claveTarifa, nuevo, CodPaquete)
	{
		Elemento = $(Elemento);
		if(confirm('¿Esta seguro que desea eliminar esta tarifa del paquete?'))
		{
			// --> Elimino la tarifa visualmente
			Elemento.parent().parent().remove();
			$("."+claveTarifa+".detalle").remove();

			// --> Elimino la tarifa en la BD
			if(nuevo == 'no')
			{
				$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:   	'',
					accion:         	'eliminar_tarifa',
					wemp_pmla:			$('#wemp_pmla').val(),
					paquete:			CodPaquete,
					tarifa:				((claveTarifa == 'Todos') ? '*' : claveTarifa)
				}
				,function(respuesta){
					mostrar_mensaje("Tarifa eliminada del paquete");
				});
			}
			// --> Redimenciono el acordeon
			$( "#accordionDetalle" ).accordion("destroy");
			$( "#accordionDetalle" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});
		}
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			mostrar mensaje
	//	Descripcion:	Pinta un mensaje en el div correspondiente para los mensajes
	//	Entradas:
	//	Salidas:
	//----------------------------------------------------------------------------------
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajesLocal").html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#div_mensajesLocal").css({"width":"300","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajesLocal").hide();
		$("#div_mensajesLocal").show(500);

		$("#div_mensajesLocal").effect("pulsate", {}, 1000);

		setTimeout(function() {
			$("#div_mensajesLocal").hide(500);
		}, 15000);
	}
	//------------------------------------------
	//	Activa o inactiva el paquete
	//------------------------------------------
	function cambiar_estado(CodPaquete)
	{
		if(CodPaquete == 'nuevo')
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   	'',
			accion:         	'CambiarEstado',
			wemp_pmla:			$('#wemp_pmla').val(),
			CodPaquete:			CodPaquete,
			estado:				(($('#EstadoPaquete').attr("value") == 'Activa') ? 'off' : 'on')
		}
		,function(respuesta){
			if(respuesta)
			{
				if($('#EstadoPaquete').attr("value") == 'Inactiva')
				{
					$('#EstadoPaquete').attr('value', 'Activa');
					$('#EstadoPaquete').attr('src', '../../images/medical/sgc/powerOn.png');
				}
				else
				{
					$('#EstadoPaquete').attr('value', 'Inactiva');
					$('#EstadoPaquete').attr('src', '../../images/medical/sgc/powerOff.png');
				}
			}
		});
	}
	//------------------------------------------------------------
	//	Guarda el paquete en la BD
	//------------------------------------------------------------
	function guardar_paquete()
	{
		var Paquete = '';
		var guardar = true;
		if($('#nombre_paquete').val() == '' || $('#nombre_paquete').val() == $('#nombre_paquete').attr('msgError'))
		{
			$("#div_mensajesLocal").html('');
			mostrar_mensaje("Debe ingresar el nombre del paquete");
			return;
		}

		$("#div_mensajesLocal").html('');
		$('#tabla_nuevos_procedimientos tr').each(function(){
			if($(this).attr('id_registro') != undefined)
			{
				var DetallePaquete = '';
				Paquete+= ((Paquete == '') ? 'id_registro->'+$(this).attr('id_registro') : '<>id_registro->'+$(this).attr('id_registro'));
				$('input', $(this)).each(function(){
					var valor = (($(this).attr('valor') != undefined) ? $(this).attr('valor') : $(this).val());
					if((valor != '' && $(this).val() != $(this).attr('msgError')) || ($(this).attr("manejart") != undefined && $(this).attr("manejart") == "si"))
						DetallePaquete+= '|'+$(this).attr('prefijo')+'->'+valor;
					else
					{
						if(DetallePaquete != '')
							mostrar_mensaje('Debe ingresar '+$(this).attr('MensajeObligatorio')+'<br>');
						guardar = false;
						console.log($(this).attr("id"));
					}
				});
				Paquete+= DetallePaquete;
			}
		});

		if(Paquete == '')
		{
			$("#div_mensajesLocal").html('');
			mostrar_mensaje('Debe ingresar al menos un detalle de paquete');
			return;
		}

		if(guardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				accion:         'guardar_paquete',
				nombrepaquete:	$("#nombre_paquete").val(),
				codpaquete:		(($("#codigo_paquete").val() == '') ? 'nuevo' : $("#codigo_paquete").val()),
				Tarifa:			$("#DetPaqTarifa").attr('valor'),
				Paquete: 		Paquete

			},function(data) {

				VerPaquete(data.Codigo);
				cargarlistapaquetes();

				setTimeout(function(){
					mostrar_mensaje(data.Mensaje);
					iniciarMarcaAqua();
				}, 500);

			}, 'json');
		}
	}
	//----------------------------------------------
	//	Pinta una lista de los paquetes existentes
	//----------------------------------------------
	function cargarlistapaquetes()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:     	'',
			wemp_pmla:        	$('#wemp_pmla').val(),
			accion:           	'ver_lista',
			codigo:				$("#buscCodPaquete").val(),
			nombre:				$("#buscNomPaquete").val(),
			estado:				$("#buscEstPaquete").val()
		},function(data) {
			$("#tabla_paquetes").html(data);
			ajustarTamañoLista(500);
		});
	}
	//-------------------------------------------
	//	Acordeon
	//-------------------------------------------
	function desplegar(elemento, clase, tipo)
	{
		elemento = jQuery(elemento);
		if(elemento.attr('src')== '../../images/medical/hce/mas.PNG')
		{
			elemento.attr('src', '../../images/medical/hce/menos.PNG');
			$('.'+clase+'.'+tipo).show();

			$('.'+clase).each(function(){
				if($(this).attr("primerRango") == "NO")
					$(this).hide();
				else
					$(this).show();
			});
		}
		else
		{
			elemento.attr('src', '../../images/medical/hce/mas.PNG');
			$('.'+clase).hide();
		}

		$( "#accordionDetalle" ).accordion("destroy");
		$( "#accordionDetalle" ).accordion({
			collapsible: true,
			heightStyle: "content"
		});
	}
	//----------------------------------------------------------
	//	Agrega una nueva tarifa para configurarle al paquete
	//----------------------------------------------------------
	function AgregarNuevaTarifa(CodPaquete, Tarifa, Tipo)
	{
		rangoTiempo 	= parseInt($("#consecutivoRangoTiempo").val())+1;
		rangoAnterior 	= ((Tipo == "Tarifa") ? "" : $("."+Tarifa+"[rango]:last").attr("rango"));

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:    	'',
			wemp_pmla:       	$('#wemp_pmla').val(),
			accion:          	'Agregar_nueva_tarifa',
			CodPaquete: 		CodPaquete,
			Consecutivo:		$("#ConsecutivoParaNuevos").val(),
			ConsecutivoTarifas:	parseInt($("#ConsecutivoTarifas").val())+1,
			Tarifa:				Tarifa,
			Tipo:				Tipo,
			rangoTiempo:		rangoTiempo,
			rangoAnterior:		rangoAnterior
		},function(data) {
			if(Tipo == "Tarifa")
			{
				// --> Agrego la nueva tarifa
				$("#DetTarifa").append(data.html);
				$("#ConsecutivoParaNuevos").val(data.consecutivo);
				$("#ConsecutivoTarifas").val(data.ConsecutivoTarifas);

				// --> Cargo el autocomplete de tarifas
				crear_autocomplete('hidden_tarifas', 'SI', 'DetPaqTarifa'+data.ConsecutivoTarifas, 'VerificarTarifaDuplicada');

				// --> Redimenciono el acordeon
				$( "#accordionDetalle" ).accordion("destroy");
				$( "#accordionDetalle" ).accordion({
					collapsible: true,
					heightStyle: "content"
				});
			}
			else
			{
				$("."+Tarifa+"[rango]:last").after(data.html);
				$("tr[rango="+Tipo+"]").hide();
				$("tr[rango="+Tarifa+"-"+rangoTiempo+"]").show();
				$("#botonAdelante"+rangoAnterior).html("&nbsp;<img width='18' height='18' style='cursor:pointer' src='../../images/medical/sgc/adelante.PNG' adelante='' onClick='verRango(\""+rangoAnterior+"\", \""+Tarifa+"-"+rangoTiempo+"\")'>"+$("#botonAdelante"+rangoAnterior).html());
			}

			// --> Tooltip
			$('[tooltip=si], '+data.html).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			$("#consecutivoRangoTiempo").val(rangoTiempo);

			// --> cargar datapicker
			$("[CargarAutocomplete=calendario]").each(function(){
				$(this).datepicker();
			});

			// --> cargar autocomplete de terceros
			$("[CargarAutocomplete=terceros]").each(function(){
				var id_input_tercero = $(this).attr('id');
				crear_autocomplete('hidden_terceros', 'SI', id_input_tercero, 'NO', '');
			});

			activar_regex($('#DetTarifa'));

			// --> se carga la marca de agua
			marcarAqua( '', 'msgError', 'campoRequerido' );
			iniciarMarcaAqua();
		}, 'json');
	}
	function borderojo(Elemento)
	{
		Elemento.css("border","2px dotted #FF0400").attr('borderred','si');
	}
	//---------------------------------------
	// --> Eliminar rango de tiempo GQX
	//---------------------------------------
	function EliminarTiempoQqx(eliminarEnBD, claveRango, paquete, tarifa, rangoTiempo)
	{
		if(confirm("¿Desea eliminar este tiempo quirúrgico?"))
		{
			// --> remover el html del rango
			$("[rango="+claveRango+"]").remove();

			// --> Mostrar el ultimo rango
			rango = $("."+tarifa+"[rango]:last").attr("rango");
			$("[rango="+rango+"]").show();

			// --> Remover la flecha hacia adelante del ultimo rango
			$("#botonAdelante"+rango).find("img[adelante]").remove();

			if(eliminarEnBD == 'si')
			{
				$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:   '',
					wemp_pmla:      $('#wemp_pmla').val(),
					accion:         'eliminarRango',
					paquete:		paquete,
					tarifa:			tarifa,
					rangoTiempo:	rangoTiempo
				},function(data){
					mostrar_mensaje("Tiempo quirúrgico eliminado.");
				});
			}
			else
			{
				mostrar_mensaje("Tiempo quirúrgico eliminado.");
			}
		}
	}
	//-------------------------------------------------------------
	//	Guarda la configuracion del paquete detallada por tarifa
	//-------------------------------------------------------------
	function guardar_detalle(CodPaquete)
	{
		var paquete = '';
		var guardar = true;
		$('[borderred=si]').css("border","").removeAttr('borderred');
		$("#BotonGuardar2").hide();
		$("#BotonGuardar2").after("<span id='ImgGuardando' ><img src='../../images/medical/ajax-loader9.gif'> <b>Guardando...</b></span>");

		// --> Armar un string con la estructura de valores a guardar
		$("#DetTarifa [id_registro]").each(function(){

			var consecutivo = $(this).attr('id_registro');

			if($(this).attr('nuevo') == 'si')
			{
				// --> id
				paquete+= ((paquete == '') ? '' : '->')+'id=nuevo';
				// --> Concepto
				paquete+= '|concepto='+$("#Concepto-"+consecutivo).val();
				// --> Procedimiento
				paquete+= '|procedimiento='+$("#Procedimiento-"+consecutivo).val();
			}
			else
			{
				// --> id
				paquete+= ((paquete == '') ? '' : '->')+'id='+$(this).attr('id_registro');
				// --> Procedimiento
				paquete+= '|procedimiento='+$("#Procedimiento-"+consecutivo).val();
			}

			// --> Tarifa
			var InputTarifa = $(this).attr('idInputTarifa');
			if($(this).attr('NuevaTarifa') == 'no')
				paquete+= '|Tarifa='+((InputTarifa == 'Todos') ? '*' : InputTarifa);
			else
			{
				if($("#"+InputTarifa).attr("valor") != '' && $("#"+InputTarifa).val() != $("#"+InputTarifa).attr("msgError"))
					paquete+= '|Tarifa='+$("#"+InputTarifa).attr("valor");
				else
				{
					mostrar_mensaje("Debe seleccionar la tarifa");
					borderojo($("#"+InputTarifa));
					guardar = false;
					return false;
				}
			}

			// --> Tercero
			paquete+= '|Tercero='+$("#Tercero-"+consecutivo).attr("valor");
			/*if($("#Tercero-"+consecutivo).attr("validar") == 'si')
			{
				if($("#Tercero-"+consecutivo).attr("valor") != '' && $("#Tercero-"+consecutivo).val() != $("#Tercero-"+consecutivo).attr("msgError"))
					paquete+= '|Tercero='+$("#Tercero-"+consecutivo).attr("valor");
				else
				{
					mostrar_mensaje("Debe ingresar el tercero");
					borderojo($("#Tercero-"+consecutivo));
					guardar = false;
					return false;
				}
			}*/

			// --> Cantidad
			if($("#Cantidad-"+consecutivo).attr("validar") == 'no' || $("#Cantidad-"+consecutivo).val() != '')
				paquete+= '|cantidad='+$("#Cantidad-"+consecutivo).val();
			else
			{
				mostrar_mensaje("Debe ingresar la cantidad");
				borderojo($("#Cantidad-"+consecutivo));
				guardar = false;
				return false;
			}

			// --> Valor Anteriori
			if($("#ValorAnterior-"+consecutivo).attr("validar") == 'no' || $("#ValorAnterior-"+consecutivo).val() != '')
				paquete+= '|valorAnterior='+$("#ValorAnterior-"+consecutivo).val();
			else
			{
				mostrar_mensaje("Debe ingresar el valor anterior");
				borderojo($("#ValorAnterior-"+consecutivo));
				guardar = false;
				return false;
			}

			// --> Fecha Cambio
			paquete+= '|fechaCambio='+$("#FechaCambio-"+consecutivo).val();

			// --> Valor Actual
			if($("#ValorActual-"+consecutivo).attr("validar") == 'no' || $("#ValorActual-"+consecutivo).val() != '')
				paquete+= '|valorActual='+$("#ValorActual-"+consecutivo).val();
			else
			{
				mostrar_mensaje("Debe ingresar el valor actual");
				borderojo($("#ValorActual-"+consecutivo));
				guardar = false;
				return false;
			}

			// --> Facturable
			paquete+= '|facturable='+$("#facturable-"+consecutivo).val();

			// --> Facturable
			if($("#inputRango"+$(this).attr("rango")).val() != "")
				paquete+= '|rango='+$("#inputRango"+$(this).attr("rango")).val();
			else
			{
				mostrar_mensaje("Debe ingresar el valor del tiempo quirúrgico.");
				borderojo($("#inputRango"+$(this).attr("rango")));
				guardar = false;
				return false;
			}
		});

		// --> Procedo a guardar el contenido del paquete
		if(guardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				accion:         'guardar_detalle_paquete',
				codpaquete:		CodPaquete,
				paquete: 		paquete

			},function(data) {
				VerPaquete(CodPaquete);
				cargarlistapaquetes();

				setTimeout(function(){
					mostrar_mensaje(data.Mensaje);
					iniciarMarcaAqua();
				}, 500);

			}, 'json');
		}
		else
		{
			$('#ImgGuardando').hide();
			$("#BotonGuardar2").show();
		}
	}
	//---------------------------------------------------------------------------
	//	Ver rango de tiempo quirurgico
	//---------------------------------------------------------------------------
	function verRango(rangoActual, rangoVer)
	{
		$("tr[rango="+rangoVer+"]").show();
		$("tr[rango="+rangoActual+"]").hide();

		// --> Pintar valores totales del paquete en la barra del nombre de la tarifa.
		var elemento = $("tr[rango="+rangoVer+"]").find("td[valorTotalDetTar]");
		$("#valorTotalTar"+elemento.attr("valorTotalDetTar")).html(elemento.text());
	}
	//---------------------------------------------------------------------------
	//	Ver el detalle de los articulos de los conceptos que mueven inventario
	//---------------------------------------------------------------------------
	function verDetalleArticulos(elemento, codPaqueteInsumo)
	{
		if(codPaqueteInsumo == "")
			$("#divDetalleArticulos").attr("consecutivoAbierto", $(elemento).attr("consecutivo"));

		ventana_insumo('grabar', 'divDetalleArticulos', 'si', codPaqueteInsumo,'*','*','','','', '', '', '*', 'on', '');
	}
	//---------------------------------------------------------------------------
	//
	//---------------------------------------------------------------------------
	function insertarCodigoPaqInsumos(codigo)
	{
		var consecutivoAbierto = $("#divDetalleArticulos").attr("consecutivoAbierto");
		$('#verDetalle'+consecutivoAbierto).attr("onClick", "verDetalleArticulos(this, \""+codigo+"\")");
		$('#eliminarDetalle'+consecutivoAbierto).attr("codPaqInsumos", codigo);
		$('#DetPaqProcedimiento'+consecutivoAbierto).attr("valor", "PI-"+codigo).val("PI-"+codigo);
	}
	//--------------------------------------------------------
	//	Actualiza los valores totales del paquete por tarifa
	//--------------------------------------------------------
	function actualizarTotal1(clave)
	{
		var newValorTotal = 0;

		$("[rango="+clave+"]").find("[id^=ValorActual]").each(function(){
			newValorTotal+= (($(this).val() != "") ? parseInt($(this).val()) : 0);
		});
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'number_format',
			valor:			newValorTotal
		},function(valor) {
			$("[valorTotalDetTar="+clave+"]").html("$"+valor);
			$("#valorTotalTar"+clave).html("$"+valor);
		});
	}
	function actualizarTotal2(clave)
	{
		var newValorTotal = 0;
		$("[rango="+clave+"]").find("[id^=ValorAnterior]").each(function(){
			newValorTotal+= (($(this).val() != "") ? parseInt($(this).val()) : 0);
		});
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			wemp_pmla:      $('#wemp_pmla').val(),
			accion:         'number_format',
			valor:			newValorTotal
		},function(valor) {
			$("[valorTotalAntDetTar="+clave+"]").html("$"+valor);
		});
	}
	//---------------------------------------------------
	// --> Ajustar tamaño de la lista de paquetes.
	//---------------------------------------------------
	function ajustarTamañoLista(tamaño)
	{
		var altura_div = $("#tabla_lista").height();
		if(altura_div > 500)
		{
			$('#tabla_paquetes').css(
				{
					'height': tamaño,
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
		else
		{
			$('#tabla_paquetes').css(
				{
					'height': altura_div,
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
	}
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>




<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">

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
                       padding: 2px;
            }
		.on{
			-moz-border-radius:				2px;
			-webkit-border-radius:			2px;
			border-radius:					4px;
			border:							1.5px outset #999999;
			padding: 						3px;
			cursor:							pointer;
			background-color: 				#1BC426;
			color: 							#ffffff;
			height:							25px;
			font-size: 						8pt;
		}
		.off{
			-moz-border-radius:				2px;
			-webkit-border-radius:			2px;
			border-radius:					4px;
			border:							1.5px outset #999999;
			padding: 						3px;
			cursor:							pointer;
			background-color: 				#FF2616;
			color: 							#ffffff;
			height:							25px;
			font-size: 						8pt;
		}
		.ui-autocomplete{
			max-width: 	260px;
			max-height: 160px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}
		.pad{
			padding: 3px;
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

	// -->	ENCABEZADO

	// --> Hidden de array de conceptos
	echo "<input type='hidden' id='hidden_concepto' name='hidden_concepto' value='".json_encode(obtener_array_conceptos())."'>";
	echo"
	<div align='center'>
		<table width='95%' cellpadding='3' cellspacing='3'>
			<tr align='center'>
				<td align='center'>
					<fieldset align='center' id='' style='padding:15px;width:1200px'>
						<legend class='fieldset'>Lista de paquetes</legend>
						<div>";
							contenedor_lista_paquetes();
	echo"				</div>
					</fieldset>
					<br><br>
					<fieldset align='center' id='contenedorVerPaquete' style='padding:15px;width:1200px;display:none'>
						<legend class='fieldset'>Detalle del paquete</legend>
						<div  id='VerPaquete'>
						</div>
					</fieldset>
				</td>
			</tr>
		</table>
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
