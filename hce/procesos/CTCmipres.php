<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA          
//=========================================================================================================================================\\
//DESCRIPCION:	Este script realiza el consumo de los web services dispuestos por el ministerio de salud para obtener las prescripciones
// realizadas en mipres, registrar en las tablas de movimientos en Matrix y permite visualizar las prescripciones. 

// Manuales (Tomados de https://www.minsalud.gov.co/Paginas/Mipres.aspx en la sección Documentos técnicos):
// https://www.minsalud.gov.co/Documentos%20y%20Publicaciones/MIPRES%20NoPBS%20-%20Documentaci%C3%B3n%20WEB%20SERVICES%20Versi%C3%B3n%203.1.pdf
// https://www.minsalud.gov.co/Documentos%20y%20Publicaciones/Anexo%20T%C3%A9cnico%20disposici%C3%B3n%20de%20datos%20EPS%20-%20IPS%20%E2%80%93%20EOC%20v3.9%E2%80%8B.pdf
// https://www.minsalud.gov.co/Documentos%20y%20Publicaciones/Anexo%20T%C3%A9cnico%20entrega%20datos%20Anulaciones%20v1.0.pdf
// https://www.minsalud.gov.co/Documentos%20y%20Publicaciones/Anexo%20T%C3%A9cnico%20entrega%20datos%20Juntas%20de%20Profesionales%20v1.1.pdf

// Consultar lista de web services:
// https://wsmipres.sispro.gov.co/WSMIPRESNOPBS/Swagger/ui/index


//AUTOR:				Jessica Madrid Mejía
//FECHA DE CREACION: 	2017-08-14

// ------------------------------------------------------------
// 							MOVIMIENTOS
// ------------------------------------------------------------	
// mipres_000001 - Encabezado prescripción mipres 
// mipres_000002 - Detalle de medicamentos prescritos 
// mipres_000003 - Detalle de principios activos por medicamento 
// mipres_000004 - Detalle de procedimientos prescritos 
// mipres_000005 - Detalle de dispositivos prescritos 
// mipres_000006 - Detalle de productos nutricionales prescritos 
// mipres_000007 - Detalle de servicios complementarios prescritos 
// mipres_000008 - Novedades 
// mipres_000009 - Anulaciones 
// mipres_000010 - Junta profesionales 
// ------------------------------------------------------------
// 							MAESTROS
// ------------------------------------------------------------
// mipres_000011 - 1. Tipos de documento de identificación	
// mipres_000012 - 2. Código Internacional de Enfermedades V10	
// mipres_000013 - 3. Listado de enfermedades huérfanas	
// mipres_000014 - 4. Ámbitos de atención	
// mipres_000015 - 5. Tipo de prestación	
// mipres_000016 - 6. Vías de administración	
// mipres_000017 - 7. Unidades de medida	
// mipres_000018 - 8. Presentación	
// mipres_000019 - 9. Formas farmacéuticas	
// mipres_000020 - 10. Denominación Común Internacional - DCI	
// mipres_000021 - 11. Unidades de medida de dosis	
// mipres_000022 - 12. Frecuencia	
// mipres_000023 - 13. Indicaciones especiales	
// mipres_000024 - 14. Tipos de medicamentos	
// mipres_000025 - 15. Procedimientos en salud	
// mipres_000026 - 16. Tipos de dispositivos médicos	
// mipres_000027 - 17. Tipos de servicios complementarios	
// mipres_000028 - 18. Servicios complementarios tutelas	
// mipres_000029 - 19. Tipos de productos nutricionales	
// mipres_000030 - 20. Lista de productos nutricionales	
// mipres_000031 - 21. Vías de administración para productos nutricionales	
// mipres_000032 - 22. Formas de productos nutricionales	
// mipres_000033 - 23. EPS	
// mipres_000034 - 24. Medicamentos por DCI	
// mipres_000035 - 25. Medicamentos Vitales no Disponibles	

// (Maestros actualizados hasta el 9 de abril de 2018 - Códigos MIPRES ​V1.33)

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-03-02';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//  2021-10-01  Joel Payares Hdz		- Se crea parametro departamento en base de datos para obtener el valor dinamicamente.
//	2021-09-15  Jaime Mejia Quintero    - Se crea parametro para agregar mipres como soporte automatico
// 	2020-11-13	Edwin MG				- Se cambia función mysqli_connect, que se conectaba a la BD de producción y se cambia por función nueva
//										  auna_connectdb agregada en el conex.php
// 	2020-03-02	Jessica Madrid Mejía	- Se agrega el parámetro default_socket_timeout con el tiempo configurado en minutos en 
// 										  root_000051 (tiempoEsperaWSMipres) para evitar que se interrumpa el consumo de los web 
// 										  services debido a que el alto tiempo de respuesta desde el ministerio, si al terminar de 
// 									 	  consumir el web service se cerró la conexión de la base de datos se realiza nuevamente a
// 										  la ip configurada en el parámetro ipBDMatrixProduccion.
// 	2020-02-14	Jessica Madrid Mejía	- Se modifica la función consumirWebServicesKron() para que reciba por parámetros las fechas 
// 										  con las que se va a consumir el web service de prescripciones y novedades de acuerdo al 
// 										  llamado (desde el kron consume dos días y desde el monitor un solo día)
// 	2020-02-05	Jessica Madrid Mejía	- Al ejecutar la función consumirWebServicesKron() que consume los web services a partir de las 
// 										  fechas especificadas en root_000051 (ultimaFechaPrescripcionesKronMipres y ultimaFechaNovedadesKronMipres)
// 										  si se identifica que el web service no esta funcionando (retorna false) se agrega validación 
// 										  para evitar que se continue ejecutando para los días posteriores.
// 	2019-07-25	Jessica Madrid Mejía	- Se agrega el nombre y municipio del prestador como parámetros en root_000051.
// 	2019-03-01	Jessica Madrid Mejía	- Se realizan las modificaciones para que las tablas de movimientos de mipres sean multiempresa.
// 	2018-07-24	Jessica Madrid Mejía	- Si una prescripción requiere junta de profesionales crea el registro con el número de prescripción
// 										  y al consumir el web service actualiza el registro con la información obtenida, se realiza esta 
// 										  modificación para evitar que falten registros de juntas profesionales debido a que no se consumió 
// 										  el web service; cada vez que abre el programa realiza el consumo para las prescripciones con juntas
// 										  profesionales pendientes.
// 										- Se realizan varias modificaciones y se agregan algunos campos para que el monitor quede actualizado 
// 										  con la versión 2.0 de MIPRES. 
// 	2018-06-14	Jessica Madrid Mejía	- Se agrega el campo Novact (Novedad actualizada) en la tabla mipres_000008 - Novedades, cada vez que 
// 										  se registra una novedad el campo Novact queda en off, si se actualiza correctamente el estado de la 
// 										  prescripción en mipres_000001 el campo Novact quedará en on, de esta forma se garantiza que si no se
// 										  pudo consumir algún web service y no se actualizó el estado de la prescripción intente actualizar 
// 										  la prescripción cada vez que se abra el programa.
// 										- En la función registrarPrescripcionesMipres() se agrega una validación para evitar registrar 
// 										  prescripciones que solo tienen encabezado ya que al consumir el web service PrescripcionPaciente 
// 										  se ha presentado que el web service solo devuelve los datos del encabezado.
// 	2018-04-16	Jessica Madrid Mejía	- Se agrega la opcion de ver la copia del mipres en PDF
// 										- Se muestra la última modificación de los maestros
// 										- Se agrega left join a algunas consultas para evitar que se dejen de mostrar algunos registros si los 
// 										  maestros no están actualizados, si es el caso solo se muestra el código.
// 										- Si se busca por numero de prescripcion no se tiene en cuenta el filtro de fecha, para facilitarle la 
// 										  busqueda al usuario 
// 	2017-10-03	Jessica Madrid Mejía	- Se agrega el estado 1. No requiere juntas profesionales
// 	2017-09-01	Jessica Madrid Mejía	- Cuando se consumen las prescripciones del día y el web service no esta funcionando no consumir el web 
// 										  service de novedades, juntas profesionales y no ejecutar la función consumirWebServicesKron()
// 	2017-08-28	Jessica Madrid Mejía	- Se agrega la cantidad de registros cuando se consultan las pestañas de prescripciones, novedades, 
// 										  junta de profesionales y el resumen
//										- En la pestaña resumen de prescripciones se agregan las pestañas Cantidades por tecnología y cantidades
// 										  por profesional y se agrega el número de prescripción.
// 										- Se agrega el filtro ámbito de atención en la pestaña Prescripciones Mipres.
// 										- Si se consumen correctamente los web services se actualiza en root_000051 en los parámetros 
// 										  ultimaFechaPrescripcionesKronMipres y ultimaFechaNovedadesKronMipres la fecha, de lo contrario 
// 										  cada vez que se abra el monitor lo hará entre la última fecha registrada en el parámetro y 
// 										  la fecha del día anterior para garantizar que queden todos los registros de Mipres.
// 	2017-08-22	Jessica Madrid Mejía	- Se agrega validacion para que no requiera tener una sesion activa cuando se llama desde el kron 
// 										  (proceso_mipres.php)
// 	2017-08-22	Jessica Madrid Mejía	- Se agrega la funcionalidad para consultar las prescripciones, novedades, junta de profesionales 
// 										  para las prescripciones en mipres y un resumen de las cantidades prescritas por tecnología.
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------

// if(!isset($_SESSION['user']))
if(!isset($_SESSION['user']) && $proceso != "actualizar" && !isset($_GET['automatizacion_pdfs'])){
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	if(!isset($wemp_pmla))
	{
		$wemp_pmla = "01";
	}
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	
	include_once("root/comun.php");
	
	$wbasedato = consultarAliasPorAplicacion($conex,$wemp_pmla , 'movhos');
	$wbasedatoMipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'mipres');
	$tiempoEsperaWSMipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'tiempoEsperaWSMipres');
	$ipBdProduccion = consultarAliasPorAplicacion($conex,$wemp_pmla , 'ipBDMatrixProduccion');
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");
	
	




//=====================================================================================================================================================================     
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	function pintarFiltros()
	{
		global $wfecha;
		
		$arrayTipDoc = consultarTiposDocumentoMipres();
		$arrayAmbitosAtencion = consultarAmbitoAtencion("");
		
		$html = "";
		$html .= "	<br><br>
					<div style='position:relative;'>
					<span align='right' style='font-weight:bold;color:#0033FF;text-decoration: underline;cursor:pointer;position:absolute;right:0%;top:-20px;' onclick='mostrarModificacionMaestros();'>Ver &uacute;ltima actualizaci&oacute;n de maestros</span>
					<div id='tabsMipres'  style='display:none;' >
						<ul>
							<li><a href='#tabPrescripciones'>Prescripciones Mipres</a></li>
							<li><a href='#tabNovedades'>Novedades</a></li>
							<li><a href='#tabJuntaProfesionales'>Junta profesionales</a></li>
							<li><a href='#tabResumen'>Resumen de prescripciones</a></li>
						</ul>
						<div id='tabPrescripciones'>
							<br>
							<br>
							<table align='center'>
								<tr class='encabezadoTabla'>
									<td colspan='4' align='center'>Par&aacute;metros de b&uacute;squeda</td>
								</tr>
								<tr>
									<td class='fila1' width='20%'>
										<b>Fecha inicial:</b>
									</td>
									<td class='fila2' width='30%'>
										<input type='text' id='fechaInicial' name='fechaInicial' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
									<td class='fila1'  width='20%'>
										<b>Fecha final:</b>
									</td>
									<td class='fila2'  width='30%'>
										<input type='text' id='fechaFinal' name='fechaFinal' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>Paciente:</b>
									</td>
									<td class='fila2'>
										<select id='tipDocPac' name='tipDocPac' style='width:50%' onchange='habilitarDocumento(this);' habilitarCampo='docPac'>
											<option value=''>Tipo documento</option>";
											foreach($arrayTipDoc as $codTipDoc => $valueTipDoc)
											{
		$html .= "								<option value='".$codTipDoc."'>".$codTipDoc."-".$valueTipDoc."</option>";													
											}
		$html .= "						</select>
											<input type='text' id='docPac' name='docPac' style='display:none;'>
										</td>
										<td class='fila1' width='80px'>
											<b>Profesional de la salud:</b>
										</td>
										<td class='fila2'>
											<select id='tipDocMed' name='tipDocMed' style='width:50%' onchange='habilitarDocumento(this);' habilitarCampo='docMed'>
												<option value=''>Tipo documento</option>";
												foreach($arrayTipDoc as $codTipDoc => $valueTipDoc)
												{
		$html .= "									<option value='".$codTipDoc."'>".$codTipDoc."-".$valueTipDoc."</option>";													
												}
		$html .= "						</select>
										<input type='text' id='docMed' name='docMed' style='display:none;'>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>EPS:</b>
									</td>
									<td class='fila2'>
										<input type='text' id='txtResponsable' name='txtResponsable'>
										<input type='hidden' id='txtCodResponsable' name='txtCodResponsable'>
									</td>
									<td class='fila1' width='80px'>
										<b>Tipo de prescripci&oacute;n:</b>
									</td>
									<td class='fila2'>
										<select id='tipoPrescripcion' name='tipoPrescripcion'  style='width:90%'>
											<option value=''>VER TODOS</option>
											<option value='M'>Medicamentos</option>
											<option value='P'>Procedimientos</option>
											<option value='D'>Dispositivos m&eacute;dicos</option>
											<option value='N'>Productos nutricionales</option>
											<option value='S'>Servicios complementarios</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>N&uacute;mero de prescripci&oacute;n:</b>
									</td>
									<td class='fila2'>
										<input type='text' id='nroPrescripcion' name='nroPrescripcion' onkeypress='return soloNumeros(event);'>
									</td>
									<td class='fila1' width='80px'>
										<b style='display:inline;'>Filtro MIPRES:</b>
									</td>
									<td class='fila2'>
										<select name='filtroMipres' id='filtroMipres'  style='width:90%;display:inline;'>
											<option value=''>VER TODOS</option>
											<option value='SinOrdenes' selected>Sin &oacute;rdenes</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>&Aacute;mbito de atenci&oacute;n:</b>
									</td>
									<td class='fila2'>
										<select name='filtroAmbitoAtencion' id='filtroAmbitoAtencion'  style='width:90%;display:inline;'>
											<option value=''>VER TODOS</option>";
												foreach($arrayAmbitosAtencion as $codAmbAte => $valueAmbAte)
												{
		$html .= "									<option value='".$codAmbAte."'>".$valueAmbAte."</option>";													
												}
		$html .= "						</select>
									</td>
									<td class='fila1' width='80px'>
										<b style='display:inline;'></b>
									</td>
									<td class='fila2'>
										
									</td>
								</tr>
								<tr>
									<td class='encabezadotabla' align='center' colspan='4'>
										<input type='button' id='btnBuscar' value='Buscar' onclick='pintarPrescripciones();'>
									</td>
								</tr>
							</table>
							<br><br><br>
							<div id='listaPrescripciones'>
							</div>
						
						</div>
						<div id='tabNovedades'>
							<br>
							<br>
							<table align='center'>
								<tr class='encabezadoTabla'>
									<td colspan='4' align='center'>Par&aacute;metros de b&uacute;squeda</td>
								</tr>
								<tr>
									<td class='fila1' width='20%'>
										<b>Fecha inicial:</b>
									</td>
									<td class='fila2' width='30%'>
										<input type='text' id='fechaInicialN' name='fechaInicialN' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
									<td class='fila1'  width='20%'>
										<b>Fecha final:</b>
									</td>
									<td class='fila2'  width='30%'>
										<input type='text' id='fechaFinalN' name='fechaFinalN' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
								</tr>
								<tr>
									<td class='encabezadotabla' align='center' colspan='4'>
										<input type='button' id='btnBuscarN' value='Buscar' onclick='pintarNovedades();'>
									</td>
								</tr>
							</table>
							<br><br><br>
							<div id='listaNovedades'>
							</div>
						</div>
						<div id='tabJuntaProfesionales'>
							<br>
							<br>
							<table align='center'>
								<tr class='encabezadoTabla'>
									<td colspan='4' align='center'>Par&aacute;metros de b&uacute;squeda</td>
								</tr>
								<tr>
									<td class='fila1' width='20%'>
										<b>Fecha inicial:</b>
									</td>
									<td class='fila2' width='30%'>
										<input type='text' id='fechaInicialJP' name='fechaInicialJP' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
									<td class='fila1'  width='20%'>
										<b>Fecha final:</b>
									</td>
									<td class='fila2'  width='30%'>
										<input type='text' id='fechaFinalJP' name='fechaFinalJP' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>Tipo de tecnolog&iacute;a:</b>
									</td>
									<td class='fila2'>
										<select id='tipoTecnologia' name='tipoTecnologia'  style='width:90%'>
											<option value=''>VER TODOS</option>
											<option value='M'>Medicamentos incluidos en la lista UNIRS</option>
											<option value='N'>Productos nutricionales de tipo Ambulatorio</option>
											<option value='S'>Servicios complementarios</option>
										</select>
									</td>
									<td class='fila1' width='80px'>
										<b>Estado de la junta de profesionales:</b>
									</td>
									<td class='fila2'>
										<select id='estadoJM' name='estadoJM'  style='width:90%'>
											<option value=''>VER TODOS</option>
											<option value='2'>Requiere junta de profesionales y pendiente evaluaci&oacute;n</option>
											<option value='3'>Evaluada por la junta de profesionales y fue aprobada</option>
											<option value='4'>Evaluada por la junta de profesionales y no fue aprobada</option>
										</select>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>N&uacute;mero de prescripci&oacute;n:</b>
									</td>
									<td class='fila2'>
										<input type='text' id='nroPrescripcionJM' name='nroPrescripcionJM' onkeypress='return soloNumeros(event);'>
									</td>
									<td class='fila1' width='80px'>
										<b></b>
									</td>
									<td class='fila2'>
										
									</td>
								</tr>
								<tr>
									<td class='encabezadotabla' align='center' colspan='4'>
										<input type='button' id='btnBuscarJP' value='Buscar' onclick='pintarPrescripcionesJP();'>
									</td>
								</tr>
							</table>
							<br><br><br>
							<div id='listaJuntaProfesionales'>
							</div>
						</div>
						<div id='tabResumen'>
							<br>
							<br>
							<table align='center'>
								<tr class='encabezadoTabla'>
									<td colspan='4' align='center'>Par&aacute;metros de b&uacute;squeda</td>
								</tr>
								<tr>
									<td class='fila1' width='20%'>
										<b>Fecha inicial:</b>
									</td>
									<td class='fila2' width='30%'>
										<input type='text' id='fechaInicialR' name='fechaInicialR' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
									<td class='fila1'  width='20%'>
										<b>Fecha final:</b>
									</td>
									<td class='fila2'  width='30%'>
										<input type='text' id='fechaFinalR' name='fechaFinalR' readOnly='readOnly' value='".date("Y-m-d")."'>
									</td>
								</tr>
								<tr>
									<td class='fila1' width='80px'>
										<b>Tipo de tecnolog&iacute;a:</b>
									</td>
									<td class='fila2'>
										<select id='tipoTecnologiaR' name='tipoTecnologiaR'  style='width:90%'>
											<option value='M'>Medicamentos</option>
											<option value='P'>Procedimientos</option>
											<option value='D'>Dispositivos</option>
											<option value='N'>Productos nutricionales</option>
											<option value='S'>Servicios complementarios</option>
										</select>
									</td>
									<td class='fila1' width='80px'>
										<b></b>
									</td>
									<td class='fila2'>
										
									</td>
								</tr>
								
								<tr>
									<td class='encabezadotabla' align='center' colspan='4'>
										<input type='button' id='btnBuscarR' value='Buscar' onclick='pintarResumen();'>
									</td>
								</tr>
							</table>
							<br><br><br>

							
							<div id='listaResumen'>
								
							</div>
						</div>
					</div>
					</div>
					<br>";
		
		return $html;						
	}

	function pintarGenerarPDF($wemp_pmla, $historia, $ingreso,$responsable, $fechaInicial, $fechaFinal, $tipDocPac, $docPac, $tipDocMed, $docMed, $codEps, $tipoPrescrip, $nroPrescripcion, $filtroMipres, $ambitoAtencion){
		$arrayPrescripciones = consultarPrescripcionesMonitor($wemp_pmla, $fechaInicial, $fechaFinal, $tipDocPac, $docPac, $tipDocMed, $docMed, $codEps, $tipoPrescrip, $nroPrescripcion, $filtroMipres, $ambitoAtencion);
		//colocar aca el tema de la automatizacion
		$contadorPDF = 1;
		if (count($arrayPrescripciones) > 0) {
			foreach ($arrayPrescripciones as $key => $value) {
				if(!empty($value['nroPrescripcion'])){
					generarPDF($wemp_pmla,$value['nroPrescripcion'],'',$contadorPDF,$historia,$ingreso,$responsable);
					$contadorPDF = $contadorPDF + 1;	
				}
			}
		}	
		return;		
	}

	function pintarTabPrescripciones($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion)
	{
		$arrayPrescripciones = consultarPrescripcionesMonitor($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion);
		
		$htmlTab = "";
		$htmlTab .= "
										<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
											Buscar:&nbsp;&nbsp;</b><input id='buscar' type='text' placeholder='Buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
										</span>
										<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position: absolute; left: 40%;'>
											Cantidad de prescripciones en Mipres: ".count($arrayPrescripciones)."
										</span>
										<table align='center' id='tablePrescripciones' width='100%'>
											<tr class='EncabezadoTabla' align='center'>
												<td colspan='9'>PRESCRIPCIONES EN MIPRES</td>
											</tr>
											<tr class='EncabezadoTabla' align='center'>
												<td rowspan='2'>Fecha</td>
												<td rowspan='2'>Hora</td>
												<td colspan='2'>Datos del paciente</td>
												<td colspan='2'>Prescrito por</td>
												<td rowspan='2'>Responsable</td>
												<td rowspan='2'>N&uacute;mero de prescripci&oacute;n</td>
												<td rowspan='2'></td>
											</tr>
											<tr class='EncabezadoTabla' align='center'>
												<td width='95px'>Documento</td>
												<td>Nombre</td>
												<td width='95px'>Documento</td>
												<td>Nombre</td>
											</tr>";
											
											if(count($arrayPrescripciones)>0)
											{
												foreach($arrayPrescripciones as $key => $value)
												{
													if ($clase_fila=='Fila2')
														$clase_fila = "Fila1";
													else
														$clase_fila = "Fila2";
													
		$htmlTab .= "								<tr class='".$clase_fila." find'>
														<td>".$value['fecha']."</td>										
														<td>".$value['hora']."</td>										
														<td>".$value['tipDocPac']." ".$value['docPac']."</td>										
														<td>".$value['nombrePac']."</td>										
														<td>".$value['tipDocMed']." ".$value['docMed']."</td>										
														<td>".$value['nombreMed']."</td>										
														<td>".$value['eps']."</td>										
														<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcion']."\")'>".$value['nroPrescripcion']."</td>										
														<td><img src='../../images/medical/sgc/Printer.png'/ width='25px' onclick='generarPDF(\"".$value['nroPrescripcion']."\");'></td>
													</tr>
													";										
												}
											}
											else
											{
		$htmlTab .= "							<tr class='fila1' align='center'>
													<td colspan='9' style='font-weight:bold;'>No se encontraron prescripciones en MIPRES con los criterios de b&uacute;squeda seleccionados.</td>
												</tr>";										
											}
											
		$htmlTab .= "				
										</table>
									";
									
		return $htmlTab;							
	}
		
	function pintarTabNovedades($wemp_pmla,$fechaInicial,$fechaFinal)
	{
		$arrayNovedades = consultarNovedadesMonitor($wemp_pmla,$fechaInicial,$fechaFinal);
		
		$htmlTab = "";
		$htmlTab .= "	
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position: absolute; left: 40%;'>
							Cantidad de novedades Mipres: ".count($arrayNovedades)."
						</span>
						<br><br>
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position:relative;left:30%;'>
							Buscar:&nbsp;&nbsp;</b><input id='buscarN' type='text' placeholder='Buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
						</span>
						<table align='center' id='tableNovedades' width='42%'>
							<tr class='EncabezadoTabla' align='center'>
								<td colspan='4'>PRESCRIPCIONES CON NOVEDADES</td>
							</tr>
							<tr class='EncabezadoTabla' align='center'>
								<td>Fecha</td>
								<td>N&uacute;mero de prescripci&oacute;n inicial</td>
								<td>N&uacute;mero de prescripci&oacute;n final</td>
								<td>Estado</td>
							</tr>";
							if(count($arrayNovedades)>0)
							{
								foreach($arrayNovedades as $key => $value)
								{
									if ($clase_fila=='Fila2')
										$clase_fila = "Fila1";
									else
										$clase_fila = "Fila2";
									
		$htmlTab .= "				<tr class='".$clase_fila." find'>
										<td>".$value['fecha']."</td>										
										<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcionInicial']."\")'>".$value['nroPrescripcionInicial']."</td>										
										<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcionFinal']."\")'>".$value['nroPrescripcionFinal']."</td>										
										<td>".$value['descEstado']."</td>										
									</tr>";										
								}
							}
							else
							{
		$htmlTab .= "			<tr class='fila1' align='center'>
									<td colspan='4' style='font-weight:bold;'>No se encontraron prescripciones con novedades en MIPRES con los criterios de b&uacute;squeda seleccionados.</td>
								</tr>";										
							}
							
		$htmlTab .= "	</table>";
									
		return $htmlTab;									
	}
	
	function pintarTabJuntasProfesionales($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,$estadoJM,$nroPrescripcion)
	{
		$arrayJuntasProfesionales = consultarJuntasProfesionales($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,$estadoJM,$nroPrescripcion);
		
		$htmlTab = "";
		$htmlTab .= "	<table style='border: 1px solid black;border-radius: 5px;;position: absolute;right:17%'>
							<tr>
							<td align='center' style='font-size:8pt' colspan='2'><b>Convenciones</b></td>
							</tr>
							<tr>
							<td><span class='fondoAmarillo' style='border-radius:3px'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;Pendiente&nbsp;&nbsp;</span></td>
							
							<td><span class='fondoGris' style='border-radius:3px'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;No requiere junta&nbsp;</span></td>
							</tr>
							<tr>
							<td><span class='fondoVerde' style='border-radius:3px'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;Aprobado&nbsp;&nbsp;</span></td>
							
							<td><span class='fondoRojo' style='border-radius:3px'>&nbsp;&nbsp;&nbsp;</span><span style='font-size:7pt;vertical-align:top;'>&nbsp;Rechazado&nbsp;&nbsp;</span></td>
							</tr>
							
						</table>
						<br>
						<br>
						<br>
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position:relative;left:15%;'>
							Buscar:&nbsp;&nbsp;</b><input id='buscarJP' type='text' placeholder='Buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
						</span>
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position: absolute; left: 40%;'>
							Cantidad de prescripciones en Mipres: ".count($arrayJuntasProfesionales)."
						</span>
						<table align='center' id='tableJuntasProfesionales' width='70%'>
							<tr class='EncabezadoTabla' align='center'>
								<td colspan='5'>PRESCRIPCIONES QUE REQUIEREN JUNTAS PROFESIONALES</td>
							</tr>
							<tr class='EncabezadoTabla' align='center'>
								<td>Fecha</td>
								<td>N&uacute;mero de prescripci&oacute;n</td>
								<td>Tipo de tecnolog&iacute;a</td>
								<td>Tecnolog&iacute;a</td>
								<td>Estado</td>
							</tr>";
							if(count($arrayJuntasProfesionales)>0)
							{
								foreach($arrayJuntasProfesionales as $key => $value)
								{
									if ($clase_fila=='Fila2')
										$clase_fila = "Fila1";
									else
										$clase_fila = "Fila2";
									
									$fondoEstado = "class='fondoAmarillo'";
									if($value['estado']=="3")
									{
										$fondoEstado = "class='fondoVerde'";
									}
									else if($value['estado']=="4")
									{
										$fondoEstado = "class='fondoRojo'";
									}
									else if($value['estado']=="1")
									{
										$fondoEstado = "class='fondoGris'";
									}
									
									$tecnologia = $value['tecnologia'];
									if($tecnologia=="")
									{
										$tecnologia = $value['codigo'];
									}
									
		$htmlTab .= "				<tr class='".$clase_fila." find'>
										<td>".$value['fecha']."</td>										
										<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcion']."\")'>".$value['nroPrescripcion']."</td>										
										<td>".$value['tipoTecnologia']."</td>										
										<td>".$tecnologia."</td>										
										<td ".$fondoEstado.">".$value['descEstado']."</td>										
									</tr>";		

								}
							}
							else
							{
		$htmlTab .= "			<tr class='fila1' align='center'>
									<td colspan='5' style='font-weight:bold;'>No se encontraron prescripciones con novedades en MIPRES con los criterios de b&uacute;squeda seleccionados.</td>
								</tr>";										
							}

		$htmlTab .= "	</table>";
	
		return $htmlTab;									
	}
	
	function pintarTabResumen($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia)
	{
		$arrayResumen = consultarResumen($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,"tec");
		$arrayResumenMed = consultarResumen($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,"med");
		
		$htmlTab = "";
		$htmlTab .= "
					<div id='tabsResumen'  style='display:none;' >
						<ul>
							<li><a href='#tabCantTec'>Cantidades por tecnolog&iacute;a</a></li>
							<li><a href='#tabCantMed'>Cantidades por profesional</a></li>
						</ul>
						<div id='tabCantTec'>
							<br>						
							<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position:relative;left:15%;'>
								Buscar:&nbsp;&nbsp;</b><input id='buscarR' type='text' placeholder='Buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
							</span>
							<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position: absolute; left: 45%;'>
								Cantidad de tecnolog&iacute;as: ".count($arrayResumen)."
							</span>
							<table align='center' id='tableResumen' width='70%'>
								<tr class='EncabezadoTabla' align='center'>
									<td colspan='3'>RESUMEN DE CANTIDADES PRESCRITAS POR TECNOLOG&Iacute;A</td>
								</tr>
								<tr class='EncabezadoTabla' align='center'>
									<td>Descripci&oacute;n</td>
									<td>Cantidad de veces prescrita</td>
									<td>N&uacute;mero de prescripci&oacute;n</td>
								</tr>";
								if(count($arrayResumen)>0)
								{
									$totalCantidades = 0;
									foreach($arrayResumen as $key => $value)
									{
										if ($clase_fila=='Fila2')
											$clase_fila = "Fila1";
										else
											$clase_fila = "Fila2";
										
			$htmlTab .= "				<tbody class='find'>
											<tr class='".$clase_fila."'>
												<td rowspan='".$value['cantidad']."'>".$value['descripcion']."</td>										
												<td rowspan='".$value['cantidad']."' align='center'>".$value['cantidad']."</td>										
												<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcion'][0]."\")'>".$value['nroPrescripcion'][0]."</td>										
											</tr>";
											
											if($value['cantidad']>"1")										
											{
												for($i=1;$i<$value['cantidad'];$i++)
												{
			$htmlTab .= "							<tr class='".$clase_fila."'>
														<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcion'][$i]."\")'>".$value['nroPrescripcion'][$i]."</td>										
													</tr>";												
												}
						
											}
											
			$htmlTab .= "				</tbody>";					
										$totalCantidades += $value['cantidad'];

									}
			$htmlTab .= "				<tr class='encabezadoTabla'>
											<td align='right'>Total:</td>										
											<td align='center'>".$totalCantidades."</td>										
											<td align='center'></td>										
										</tr>";		
								}
								else
								{
			$htmlTab .= "			<tr class='fila1' align='center'>
										<td colspan='3' style='font-weight:bold;'>No se encontraron prescripciones en MIPRES con los criterios de b&uacute;squeda seleccionados.</td>
									</tr>";										
								}

			$htmlTab .= "	</table>
						</div>
						
						<div id='tabCantMed'>
							<br>						
							<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position:relative;left:13%;'>
								Buscar:&nbsp;&nbsp;</b><input id='buscarRM' type='text' placeholder='Buscar' style='border-radius: 4px;border:1px solid #AFAFAF;'>
							</span>
							<span style='font-family: verdana;font-weight:bold;font-size: 10pt;position: absolute; left: 45%;'>
								Cantidad de tecnolog&iacute;as: ".count($arrayResumen)."
							</span>
							<table align='center' id='tableResumen' width='75%'>
								<tr class='EncabezadoTabla' align='center'>
									<td colspan='5'>RESUMEN DE CANTIDADES PRESCRITAS POR PROFESIONAL DE LA SALUD</td>
								</tr>
								<tr class='EncabezadoTabla' align='center'>
									<td>Profesional de la salud</td>
									<td>Cantidad de prescripciones</td>
									<td>Descripci&oacute;n tecnolog&iacute;a</td>
									<td>Cantidad de veces prescrita</td>
									<td>N&uacute;mero de prescripci&oacute;n</td>
								</tr>";
								if(count($arrayResumenMed)>0)
								{
			$htmlTab .= "			<tbody>";	
			
									$totalCantidades = 0;
									$nombreMedicoAnterior = "";
									foreach($arrayResumenMed as $key => $value)
									{
										if ($clase_fila=='Fila2')
											$clase_fila = "Fila1";
										else
											$clase_fila = "Fila2";
										
										if($nombreMedicoAnterior!=$value['medico'])
										{
			$htmlTab .= "					</tbody>";
			$htmlTab .= "					<tbody class='find'>
												<tr class='Fila1'>
													<td rowspan='".$value['cantidadTecnologias']."' class='nombreMedico'>".$value['medico']."</td>										
													<td rowspan='".$value['cantidadTecnologias']."' class='nombreMedico' align='center'>".$value['cantidadTecnologias']."</td>										";									
										}
										else
										{
			$htmlTab .= "					<tr class='Fila1'>";								
										}
										
										$nombreMedicoAnterior=$value['medico'];
			$htmlTab .= "				
											
												<td class='".$clase_fila."' rowspan='".$value['cantidad']."'>".$value['descripcion']."</td>										
												<td class='".$clase_fila."' rowspan='".$value['cantidad']."' align='center'>".$value['cantidad']."</td>										
												<td class='".$clase_fila." verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcion'][0]."\")'>".$value['nroPrescripcion'][0]."</td>										
											</tr>";
											
										
											if($value['cantidad']>"1")										
											{
												for($i=1;$i<$value['cantidad'];$i++)
												{
			$htmlTab .= "							<tr class='".$clase_fila."'>
														<td class='verPrescripcion' onclick='abrirModalMipres(\"".$value['nroPrescripcion'][$i]."\")'>".$value['nroPrescripcion'][$i]."</td>										
													</tr>";												
												}
						
											}
											
										$totalCantidades += $value['cantidad'];

									}
			$htmlTab .= "				<tr class='encabezadoTabla'>
											<td align='right' colspan='1'>Total:</td>										
											<td align='center'>".$totalCantidades."</td>										
											<td align='center' colspan='3'></td>										
										</tr>";		
								}
								else
								{
			$htmlTab .= "			<tr class='fila1' align='center'>
										<td colspan='5' style='font-weight:bold;'>No se encontraron prescripciones en MIPRES con los criterios de b&uacute;squeda seleccionados.</td>
									</tr>";										
								}
								
			$htmlTab .= "	</table>
						</div>
						
						</div>";
	
		return $htmlTab;									
	}
	
	function consultarPrescripcionesMonitor($wemp_pmla,$fechaInicial,$fechaFinal,$tipDocPac,$docPac,$tipDocMed,$docMed,$codEps,$tipoPrescrip,$nroPrescripcion,$filtroMipres,$ambitoAtencion)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoMipres;
		
		$fechaInicialMonitorMipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'fechaInicioMonitorMipres');
		
		$arrayPrescripciones = array();
		if($filtroMipres=="SinOrdenes")
		{
			$queryPrescripciones = "SELECT Ctcmip AS Prescripcion 
									  FROM ".$wbasedato."_000134 
									 WHERE Ctcmip!='' 
									   AND (Ctcacc='E' OR Ctcacc='EM')
									
									 UNION
									
									SELECT Ctcmia AS Prescripcion 
									  FROM ".$wbasedato."_000134 
									 WHERE Ctcmia!='' 
									   AND (Ctcacc='E' OR Ctcacc='EM')

									 UNION

									SELECT Ctcmip AS Prescripcion 
									  FROM ".$wbasedato."_000135 
									 WHERE Ctcmip!='' 
									   AND (Ctcacc='E' OR Ctcacc='EM')
									   
									 UNION
									 
									SELECT Ctcmia AS Prescripcion 
									  FROM ".$wbasedato."_000135 
									 WHERE Ctcmia!='' 
									   AND (Ctcacc='E' OR Ctcacc='EM');";


			
			$resPrescripciones = mysql_query($queryPrescripciones, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPrescripciones . " - " . mysql_error());		   
			$numPrescripciones = mysql_num_rows($resPrescripciones);
			
			if($numPrescripciones>0)
			{
				while($rowsPrescripciones = mysql_fetch_array($resPrescripciones))
				{
					$arrayPrescripciones[$rowsPrescripciones['Prescripcion']] = $rowsPrescripciones['Prescripcion'];
				}
			}
		}
		
		$filtroPaciente = "";
		if($tipDocPac!="" && $docPac != "")
		{
			$filtroPaciente = " AND Pretip='".$tipDocPac."'
								AND Preidp='".$docPac."'";
		}
		
		$filtroMedico = "";
		if($tipDocMed!="" && $docMed != "")
		{
			$filtroMedico = " AND Pretim='".$tipDocMed."'
								AND Preidm='".$docMed."'";
		}
		
		$filtroEps = "";
		if($codEps!="")
		{
			$filtroEps = " AND Preeps='".$codEps."'";
		}
		
		$filtroNroPrescripcion = "";
		if($nroPrescripcion!="")
		{
			$filtroNroPrescripcion = " AND Prenop='".$nroPrescripcion."'";
		}
		
		$filtroAmbitoAtencion = "";
		if($ambitoAtencion!="")
		{
			$filtroAmbitoAtencion = " AND Precaa='".$ambitoAtencion."'";
		}

		//cambio jaime mejia para obtener estados diferente de 4
		$filtroEstado = " AND Preepr='" . '4'. "'";

		$tablaFiltroTipoPresc = "";
		$condicionFiltroTipoPresc = "";
		if($tipoPrescrip=="M")
		{
			$tablaFiltroTipoPresc = ",".$wbasedatoMipres."_000002";
			$condicionFiltroTipoPresc = "AND Prenop=Mednop";
		}
		else if($tipoPrescrip=="P")
		{
			$tablaFiltroTipoPresc = ",".$wbasedatoMipres."_000004";
			$condicionFiltroTipoPresc = "AND Prenop=Pronop";
		}
		else if($tipoPrescrip=="D")
		{
			$tablaFiltroTipoPresc = ",".$wbasedatoMipres."_000005";
			$condicionFiltroTipoPresc = "AND Prenop=Disnop";
		}
		else if($tipoPrescrip=="N")
		{
			$tablaFiltroTipoPresc = ",".$wbasedatoMipres."_000006";
			$condicionFiltroTipoPresc = "AND Prenop=Nutnop";
		}
		else if($tipoPrescrip=="S")
		{
			$tablaFiltroTipoPresc = ",".$wbasedatoMipres."_000007";
			$condicionFiltroTipoPresc = "AND Prenop=Sernop";
		}
		
		if($filtroNroPrescripcion=="")
		{
			// si no tiene numero de prescripción debe tener en cuenta el rango de fecha
			$filtroFecha = "Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'";
		}
		else
		{
			$filtroFecha = "Prefep BETWEEN '".$fechaInicialMonitorMipres."' AND '".date('Y-m-d')."'";
		}

		//Automatizacion de la consulta para sacar soporte Mipres automatico
		if (isset($_GET['automatizacion_pdfs'])){
			$queryMipres = "SELECT Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps,Epsdes,Precaa
			FROM " . $wbasedatoMipres . "_000001,mipres_000033 " . $tablaFiltroTipoPresc . " 
		   WHERE 	" . $filtroFecha . "
				  " . $filtroPaciente . "
				  " . $filtroMedico . "
				  " . $filtroEps . "
				  " . $filtroNroPrescripcion . "
				  " . $filtroAmbitoAtencion . "
				  " . $filtroEstado . "
			 AND Preeps=Epscod
				  " . $condicionFiltroTipoPresc . "
		ORDER BY Prefep,Prehop,Prepnp,Presnp,Prepap,Presap;";
		}
		
		else{
		$queryMipres = "SELECT Prefep,Prehop,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Prenop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Preeps,Epsdes
						  FROM ".$wbasedatoMipres."_000001,mipres_000033 ".$tablaFiltroTipoPresc." 
						 WHERE 	".$filtroFecha."
								".$filtroPaciente."
								".$filtroMedico."
								".$filtroEps."
								".$filtroNroPrescripcion."
								".$filtroAmbitoAtencion."
						   AND Preeps=Epscod
								".$condicionFiltroTipoPresc."
					  ORDER BY Prefep,Prehop,Prepnp,Presnp,Prepap,Presap;";
		}			  
  
		$resMipres = mysql_query($queryMipres, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMipres . " - " . mysql_error());		   
		$numMipres = mysql_num_rows($resMipres);
		
		$arrayMipres = array();
		if($numMipres>0)
		{
			while($rowsMipres = mysql_fetch_array($resMipres))
			{
				if (isset($_GET['automatizacion_pdfs'])){
					while($rowsMipres['Precaa'] == '11' || $rowsMipres['Precaa'] == '12'){
						exit();
					}
				}
				if(!in_array($rowsMipres['Prenop'],$arrayPrescripciones))
				{
					$arrayMipres[$rowsMipres['Prenop']]['nroPrescripcion'] = $rowsMipres['Prenop'];
					$arrayMipres[$rowsMipres['Prenop']]['fecha'] = $rowsMipres['Prefep'];
					$arrayMipres[$rowsMipres['Prenop']]['hora'] = $rowsMipres['Prehop'];
					$arrayMipres[$rowsMipres['Prenop']]['tipDocPac'] = $rowsMipres['Pretip'];
					$arrayMipres[$rowsMipres['Prenop']]['docPac'] = $rowsMipres['Preidp'];
					$arrayMipres[$rowsMipres['Prenop']]['nombrePac'] = $rowsMipres['Prepnp']." ".$rowsMipres['Presnp']." ".$rowsMipres['Prepap']." ".$rowsMipres['Presap'];
					$arrayMipres[$rowsMipres['Prenop']]['tipDocMed'] = $rowsMipres['Pretim'];
					$arrayMipres[$rowsMipres['Prenop']]['docMed'] = $rowsMipres['Preidm'];
					$arrayMipres[$rowsMipres['Prenop']]['nombreMed'] = $rowsMipres['Prepnm']." ".$rowsMipres['Presnm']." ".$rowsMipres['Prepam']." ".$rowsMipres['Presam'];
					$arrayMipres[$rowsMipres['Prenop']]['eps'] = $rowsMipres['Epsdes'];

				}
			}
		}
		
		return $arrayMipres;
		
	}
	
	function consultarNovedadesMonitor($wemp_pmla,$fechaInicial,$fechaFinal)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoMipres;
		
		$queryNovedades = "SELECT Novcod,Novnpr,Novnpf,Novfec
							 FROM ".$wbasedatoMipres."_000008 
							WHERE Novfec BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
						 ORDER BY Novfec;";
							
		$resNovedades = mysql_query($queryNovedades, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNovedades . " - " . mysql_error());		   
		$numNovedades = mysql_num_rows($resNovedades);
		
		$estadoNovedades = array(
			1 => "Modificaci&oacute;n",
			2 => "Anulaci&oacute;n",
			3 => "Transcripci&oacute;n",
		);
		
		$arrayNovedades = array();
		$contadorNovedades = 0;
		if($numNovedades>0)
		{
			while($rowsNovedades = mysql_fetch_array($resNovedades))
			{
				$arrayNovedades[$contadorNovedades]['codigo'] = $rowsNovedades['Novcod'];
				$arrayNovedades[$contadorNovedades]['descEstado'] = $estadoNovedades[$rowsNovedades['Novcod']];
				$arrayNovedades[$contadorNovedades]['nroPrescripcionInicial'] = $rowsNovedades['Novnpr'];
				$arrayNovedades[$contadorNovedades]['nroPrescripcionFinal'] = $rowsNovedades['Novnpf'];
				$arrayNovedades[$contadorNovedades]['fecha'] = $rowsNovedades['Novfec'];
				
				$contadorNovedades++;
			}
		}			
		
		return $arrayNovedades;
	}
	
	function consultarJuntasProfesionales($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,$estadoJM,$nroPrescripcion)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoMipres;
		
		$condicionEstado = "";
		if($estadoJM!="")
		{
			$condicionEstado = "AND Jupejm='".$estadoJM."'";
		}
		
		$condicionNroPrescripcion = "";
		if($nroPrescripcion!="")
		{
			$condicionNroPrescripcion = "AND Jupnpr='".$nroPrescripcion."'";
			
		}
		
		$condicionFecha = "";
		if($condicionNroPrescripcion=="")
		{
			// si no tiene numero de prescripcion debe tener en cuenta el rango de fechas
			$condicionFecha = "		Jupfpr BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
								AND ";
		}
			
		
		
		if($tipoTecnologia=="M")
		{
			$queryJuntaProfesional = "SELECT Jupfpr,Jupnpr,Juptte,Jupejm,'' AS codigo,Meddmp AS tecnologia
										FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000002,".$wbasedatoMipres."_000001 
									   WHERE ".$condicionFecha."
											 Juptte='M'
											 ".$condicionEstado."
											 ".$condicionNroPrescripcion."
										 AND Mednop=Jupnpr
										 AND Medcom=Jupcon
										 AND Prenop=Jupnpr
										 AND Preepr='4';";
		}
		else if($tipoTecnologia=="N")
		{
			$queryJuntaProfesional = "SELECT Jupfpr,Jupnpr,Juptte,Jupejm,Nutdpn AS codigo,Lpnnom AS tecnologia
										FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000006
								   LEFT JOIN mipres_000030 
								          ON Lpncod=Nutdpn
											,".$wbasedatoMipres."_000001 
									   WHERE ".$condicionFecha."
											 Juptte='N'
											 ".$condicionEstado."
											 ".$condicionNroPrescripcion."
										 AND Nutnop=Jupnpr
										 AND Nutcon=Jupcon
										 AND Prenop=Jupnpr
										 AND Preepr='4';";
		}
		else if($tipoTecnologia=="S")
		{
			$queryJuntaProfesional = "SELECT Jupfpr,Jupnpr,Juptte,Jupejm,Sercsc AS codigo,Sctdes AS tecnologia
										FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000007 
								   LEFT JOIN mipres_000028 
								          ON Sctcod=Sercsc
											,mipres_000028,".$wbasedatoMipres."_000001
									   WHERE ".$condicionFecha."
										     Juptte='S'
											 ".$condicionEstado."
											 ".$condicionNroPrescripcion."
										 AND Sernop=Jupnpr
										 AND Sercos=Jupcon
										 AND Prenop=Jupnpr
										 AND Preepr='4';";
		}
		else
		{
			$queryJuntaProfesional = "SELECT Jupfpr,Jupnpr,Juptte,Jupejm,'' AS codigo,Meddmp AS tecnologia
										FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000002,".$wbasedatoMipres."_000001 
									   WHERE ".$condicionFecha."
											 Juptte='M'
											 ".$condicionEstado."
											 ".$condicionNroPrescripcion."
										 AND Mednop=Jupnpr
										 AND Medcom=Jupcon
										 AND Prenop=Jupnpr
										 AND Preepr='4'

									   UNION

									  SELECT Jupfpr,Jupnpr,Juptte,Jupejm,Nutdpn AS codigo,Lpnnom AS tecnologia
										FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000006
								   LEFT JOIN mipres_000030 
								          ON Lpncod=Nutdpn
										    ,".$wbasedatoMipres."_000001 
									   WHERE ".$condicionFecha."
											 Juptte='N'
											 ".$condicionEstado."
											 ".$condicionNroPrescripcion."
										 AND Nutnop=Jupnpr
										 AND Nutcon=Jupcon
										 AND Prenop=Jupnpr
										 AND Preepr='4'

									   UNION

									  SELECT Jupfpr,Jupnpr,Juptte,Jupejm,Sercsc AS codigo,Sctdes AS tecnologia
										FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000007 
								   LEFT JOIN mipres_000028 
								          ON Sctcod=Sercsc
											,".$wbasedatoMipres."_000001
									   WHERE ".$condicionFecha."
											 Juptte='S'
											 ".$condicionEstado."
											 ".$condicionNroPrescripcion."
										 AND Sernop=Jupnpr
										 AND Sercos=Jupcon
										 AND Prenop=Jupnpr
										 AND Preepr='4'

									ORDER BY Jupfpr,Jupnpr,Juptte,Jupejm,tecnologia;";
		}
		
		
		$resJuntaProfesional = mysql_query($queryJuntaProfesional, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryJuntaProfesional . " - " . mysql_error());		   
		$numJuntaProfesional = mysql_num_rows($resJuntaProfesional);
		
		
		$estadoJunta = array(
			1 => "No requiere junta de profesionales",
			2 => "Requiere junta de profesionales y pendiente evaluaci&oacute;n",
			3 => "Evaluada por la junta de profesionales y fue aprobada",
			4 => "Evaluada por la junta de profesionales y no fue aprobada",
		);

		$tipoTecnologias = array(
			"M" => "Medicamentos incluidos en la lista UNIRS",
			"N" => "Productos nutricionales de tipo Ambulatorio",
			"S" => "Servicios Complementarios",
		);
		
		$arrayJuntaProfesional = array();
		$contadorJunta = 0;
		if($numJuntaProfesional>0)
		{
			while($rowsJuntaProfesional = mysql_fetch_array($resJuntaProfesional))
			{
				$arrayJuntaProfesional[$contadorJunta]['fecha'] = $rowsJuntaProfesional['Jupfpr'];
				$arrayJuntaProfesional[$contadorJunta]['nroPrescripcion'] = $rowsJuntaProfesional['Jupnpr'];
				$arrayJuntaProfesional[$contadorJunta]['tipoTecnologia'] = $tipoTecnologias[$rowsJuntaProfesional['Juptte']];
				$arrayJuntaProfesional[$contadorJunta]['codigo'] = $rowsJuntaProfesional['codigo'];
				$arrayJuntaProfesional[$contadorJunta]['tecnologia'] = $rowsJuntaProfesional['tecnologia'];
				$arrayJuntaProfesional[$contadorJunta]['estado'] = $rowsJuntaProfesional['Jupejm'];
				$arrayJuntaProfesional[$contadorJunta]['descEstado'] = $estadoJunta[$rowsJuntaProfesional['Jupejm']];
				
				$contadorJunta++;
			}
		}			
		
		return $arrayJuntaProfesional;
	}
	
	function consultarResumen($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,$tipo)
	{
		global $conex;
		global $wbasedato;
		global $wbasedatoMipres;
		
		
		$ordenar = "ORDER BY Medico,Descripcion,Prenop";
		if($tipo=="tec")
		{
			$ordenar = "ORDER BY Descripcion,Prenop";
		}
		
		
		if($tipoTecnologia=="M")
		{
			$queryResumen = " SELECT CONCAT_WS(' ',Prepnm,Presnm,Prepam,Presam) AS Medico,'' AS Codigo,Meddmp AS Descripcion,Prenop
								FROM ".$wbasedatoMipres."_000001,".$wbasedatoMipres."_000002 
							   WHERE Mednop=Prenop 
								 AND Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' 
								".$ordenar.";";
		}
		else if($tipoTecnologia=="P")
		{
			$queryResumen = " SELECT CONCAT_WS(' ',Prepnm,Presnm,Prepam,Presam) AS Medico,Procup AS Codigo,Cupdes AS Descripcion,Prenop
								FROM ".$wbasedatoMipres."_000001,".$wbasedatoMipres."_000004 
						   LEFT JOIN mipres_000025 
								  ON Procup=Cupcod
							   WHERE Pronop=Prenop 
								 AND Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' 
								".$ordenar.";";
		}
		else if($tipoTecnologia=="D")
		{
			$queryResumen = " SELECT CONCAT_WS(' ',Prepnm,Presnm,Prepam,Presam) AS Medico,Tdmcod AS Codigo,Tdmdes AS Descripcion,Prenop
								FROM ".$wbasedatoMipres."_000001,".$wbasedatoMipres."_000005
						   LEFT JOIN mipres_000026 
								  ON Tdmcod=Discdi
							   WHERE Disnop=Prenop  
								 AND Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'
								".$ordenar.";";
		}
		else if($tipoTecnologia=="N")
		{
			$queryResumen = " SELECT CONCAT_WS(' ',Prepnm,Presnm,Prepam,Presam) AS Medico,Nutdpn AS Codigo,Lpnnom AS Descripcion,Prenop
								FROM ".$wbasedatoMipres."_000001,".$wbasedatoMipres."_000006
						   LEFT JOIN mipres_000030 
								  ON Lpncod=Nutdpn
							   WHERE Nutnop=Prenop 
								 AND Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."' 
								".$ordenar.";";
		}
		else if($tipoTecnologia=="S")
		{
			$queryResumen = " SELECT CONCAT_WS(' ',Prepnm,Presnm,Prepam,Presam) AS Medico,Sercsc AS Codigo,Sctdes AS Descripcion,Prenop
								FROM ".$wbasedatoMipres."_000001,".$wbasedatoMipres."_000007
						   LEFT JOIN mipres_000028 
								  ON Sctcod=Sercsc								
							   WHERE Sernop=Prenop 
								 AND Prefep BETWEEN '".$fechaInicial."' AND '".$fechaFinal."'  
								".$ordenar.";";
		}
		
		$resResumen = mysql_query($queryResumen, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryResumen . " - " . mysql_error());		   
		$numResumen = mysql_num_rows($resResumen);
		
		
		$arrayResumen = array();
		$arrayResumenM = array();
		$arrayCantidades = array();
		$idArray = "";
		if($numResumen>0)
		{
			while($rowsResumen = mysql_fetch_array($resResumen))
			{
				if($rowsResumen['Descripcion']=="")
				{
					$rowsResumen['Descripcion'] = $rowsResumen['Codigo'];
				}
				
				if($tipo=="tec")
				{
					$idArray = $rowsResumen['Descripcion'];
					
					$arrayResumen[$idArray]['descripcion'] = $rowsResumen['Descripcion'];
					$arrayResumen[$idArray]['cantidad'] += 1;
					$arrayResumen[$idArray]['nroPrescripcion'][] = $rowsResumen['Prenop'];
					
					
				}
				else
				{
					$idArray = $rowsResumen['Medico']."_".$rowsResumen['Descripcion'];
					
					$arrayResumenM[$idArray]['medico'] = $rowsResumen['Medico'];
					$arrayResumenM[$idArray]['descripcion'] = $rowsResumen['Descripcion'];
					$arrayResumenM[$idArray]['cantidad'] += 1;
					$arrayResumenM[$idArray]['nroPrescripcion'][] = $rowsResumen['Prenop'];
					
					
					$arrayCantidades[$rowsResumen['Medico']]+=1;
				}
				
			}
		}
		
		
		if($tipo=="med")
		{
			if(count($arrayResumenM)>0)
			{
				arsort($arrayCantidades);
				
				foreach($arrayCantidades as $keyMedico => $valueCantidad)
				{
					foreach($arrayResumenM as $keyResumen => $valueResumen)
					{
						if($keyMedico==$valueResumen['medico'])
						{
							$arrayResumen[$keyResumen]=$arrayResumenM[$keyResumen];
							$arrayResumen[$keyResumen]['cantidadTecnologias']=$valueCantidad;
						}
					}
				}
			}
		}
		
		return $arrayResumen;
	}
	
	function consumirWebService($urlWebserviceMipres)
	{
		global $conex;
		global $tiempoEsperaWSMipres;
		global $ipBdProduccion;
		
		$tiempoEsperaWS = (int)$tiempoEsperaWSMipres * 60;
		
		ini_set('default_socket_timeout', $tiempoEsperaWS);
		$resultado = file_get_contents($urlWebserviceMipres,false );
		
		// Comprobar si el servidor sigue funcionando
		if (!mysqli_ping($conex)) 
		{
			//Conexión a la base de datos
			$conex = auna_connectdb() or die("No se realizo Conexion");
		}
		
		return $resultado;
	}
	
	function consumirWebServicesMipres($fechaWSmipres,$wemp_pmla,$general,$historia,$ingreso,$tipoDocumento,$documento,$hora,$origen)
	{
		global $conex;
		
		$urlWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceMipres' );
		$nitWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );
		$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
		
		
		if($general=="on")
		{
			// http://wsmipres.sispro.gov.co/wsmipresnopbs/api/Prescripcion/800067065/2017-03-01/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6
			$urlWebserviceMipres = $urlWSmipres."Prescripcion/".$nitWSmipres."/".$fechaWSmipres."/".$tokenWSmipres."";
			// $urlWebserviceMipres = $urlWSmipres."Prescdgfdgdgripcion/".$nitWSmipres."/".$fechaWSmipres."/".$tokenWSmipres."";
		}
		else
		{
			// http://wsmipres.sispro.gov.co/wsmipresnopbs/api/PrescripcionPaciente/800067065/2017-03-01/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6/CC/70104380
			$urlWebserviceMipres = $urlWSmipres."PrescripcionPaciente/".$nitWSmipres."/".$fechaWSmipres."/".$tokenWSmipres."/".$tipoDocumento."/".$documento."";
		}
		
		// $result = file_get_contents($urlWebserviceMipres,false );
		$result = consumirWebService($urlWebserviceMipres);
		
		if($result!=false)
		{
			$jsonMipres =json_decode($result);
		
		
			registrarPrescripcionesMipres($jsonMipres,$historia,$ingreso);
			
			
			
			
			// al registrar las prescripciones si el origen es ordenes debe devolver un consecutivo, si es diferente debe devolver un array 
			// con los consecutivos
			
			$consecutivoMipres = array();
			
			if($general=="on")
			{
				return $jsonMipres;
			}
			else
			{
				if(count($jsonMipres)>0)
				{
					$consecutivoMipres = consultarConsecutivoMipres($jsonMipres,$fechaWSmipres,$hora,$origen);
					
					if($origen=="ordenes" && count($consecutivoMipres)>0)
					{
						actualizarHistoria($consecutivoMipres[0],$historia,$ingreso);
					}
				}
				return $consecutivoMipres;
			}
		}
		else
		{
			return $result;
		}

		
		
	}
	
	function consumirWebServiceJuntaProfesionalXFecha($fechaConsulta,$wemp_pmla)
	{
		global $conex;
		
		$urlWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceMipres' );
		$nitWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );
		$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
		
		// web service junta profesional
		// https://wsmipres.sispro.gov.co/WSMIPRESNOPBS/api/JuntaProfesionalXFecha/800067065/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6/2017-06-28
		$urlWebserviceMipres = $urlWSmipres."JuntaProfesionalXFecha/".$nitWSmipres."/".$tokenWSmipres."/".$fechaConsulta."";
		
		// $result = file_get_contents($urlWebserviceMipres,false );
		$result = consumirWebService($urlWebserviceMipres);

		$jsonMipres =json_decode($result);
		
		
		registrarJuntaProfesional($jsonMipres,$wemp_pmla);
		
	}
	
	function consumirWebServiceNovedades($fechaConsulta,$wemp_pmla)
	{
		global $conex;
		
		$urlWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceMipres' );
		$nitWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );
		$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
		
		// web service novedades
		// https://wsmipres.sispro.gov.co/wsmipresnopbs/api/NovedadesPrescripcion/800067065/2017-06-08/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6
		$urlWebserviceMipres = $urlWSmipres."NovedadesPrescripcion/".$nitWSmipres."/".$fechaConsulta."/".$tokenWSmipres."";
		
		// $result = file_get_contents($urlWebserviceMipres,false );
		$result = consumirWebService($urlWebserviceMipres);

		$jsonMipres =json_decode($result);
		
		if(count($jsonMipres)>0)
		{
			registrarNovedad($jsonMipres,$wemp_pmla);
		}
		
		return $jsonMipres;
		
	}
		
	function consumirWebServiceAnulacion($nroPrescripcion,$wemp_pmla)
	{
		global $conex;
		
		$urlWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceMipres' );
		$nitWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );
		$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
		
		// https://wsmipres.sispro.gov.co/wsmipresnopbs/api/AnulacionXPrescripcion/800067065/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6/20170620123001472223
		$urlWebserviceMipres = $urlWSmipres."AnulacionXPrescripcion/".$nitWSmipres."/".$tokenWSmipres."/".$nroPrescripcion."";
		
		// $result = file_get_contents($urlWebserviceMipres,false );
		$result = consumirWebService($urlWebserviceMipres);

		$jsonMipres =json_decode($result);
		
		return $jsonMipres;
	}
	
	function consumirWebServiceJuntaProfesionalXPrescripcion($nroPrescripcion,$wemp_pmla)
	{
		global $conex;
		
		registrarJunta($nroPrescripcion);
		
		$urlWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceMipres' );
		$nitWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );
		$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
		
		// https://wsmipres.sispro.gov.co/WSMIPRESNOPBS/api/JuntaProfesional/800067065/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6/20170810166002158108
		$urlWebserviceMipres = $urlWSmipres."JuntaProfesional/".$nitWSmipres."/".$tokenWSmipres."/".$nroPrescripcion."";
		
		// $result = file_get_contents($urlWebserviceMipres,false );
		$result = consumirWebService($urlWebserviceMipres);

		$jsonMipres =json_decode($result);
		
		
		registrarJuntaProfesional($jsonMipres,$wemp_pmla);
		
	}
	
	function consumirWebServicePrescripcion($nroPrescripcion,$wemp_pmla)
	{
		global $conex;
		
		$urlWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'urlWebserviceMipres' );
		$nitWSmipres   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nitWebserviceMipres' );
		$tokenWSmipres = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tokenWebserviceMipres' );
		
		// https://wsmipres.sispro.gov.co/wsmipresnopbs/api/PrescripcionXNumero/800067065/9AC52A56-26F0-49CE-9B3A-8BB0DE6722B6/20170620165001468495
		$urlWebserviceMipres = $urlWSmipres."PrescripcionXNumero/".$nitWSmipres."/".$tokenWSmipres."/".$nroPrescripcion."";
		
		// $result = file_get_contents($urlWebserviceMipres,false );
		$result = consumirWebService($urlWebserviceMipres);
		
		$jsonMipres =json_decode($result);
		
		return $jsonMipres;
	}
	
	function consumirWebServicesKron($fechaDiaAnterior,$ultimaFechaKronP,$fechaKronP,$ultimaFechaKronN,$fechaKronN,$wemp_pmla)
	{
		global $conex;
		
		$fechaActual = date("Y-m-d");
		
		$fechasKron = "";
		
		if($ultimaFechaKronP>=$fechaDiaAnterior)
		{
			$fechasKron .= "Las prescripciones est&aacute; actualizadas hasta el ".$ultimaFechaKronP." | ";
		}
		
		while($fechaKronP<=$fechaDiaAnterior)
		{
			$jsonPrescripciones = consumirWebServicesMipres($fechaKronP,$wemp_pmla,"on","","","","","","");
			
			if($jsonPrescripciones===false)
			{
				$fechaKronP = $fechaActual;
			}
			else
			{
				actualizarUltimaFechaKronMipres($fechaKronP,"ultimaFechaPrescripcionesKronMipres");
				
				$fechasKron .= "P: ".$fechaKronP." | ";
				
				$fechaKronP = strtotime ( '+1 day' , strtotime ( $fechaKronP ) ) ;
				$fechaKronP = date ( "Y-m-d" , $fechaKronP );	
			}
			
		}
		
		if($ultimaFechaKronN>=$fechaDiaAnterior)
		{
			$fechasKron .= "Las novedades est&aacute; actualizadas hasta el ".$ultimaFechaKronN." | ";
		}
		while($fechaKronN<=$fechaDiaAnterior)
		{
			$jsonNovedades =  consumirWebServiceNovedades($fechaKronN,$wemp_pmla);
			
			if($jsonNovedades===false)
			{
				$fechaKronN = $fechaActual;
			}
			else
			{
				actualizarUltimaFechaKronMipres($fechaKronN,"ultimaFechaNovedadesKronMipres");
				
				$fechasKron .= "N: ".$fechaKronN." | ";
				
				$fechaKronN = strtotime ( '+1 day' , strtotime ( $fechaKronN ) ) ;
				$fechaKronN = date ( "Y-m-d" , $fechaKronN );	
			}
			
		}
		
		return $fechasKron;
	}
	
	function actualizarHistoria($consecutivoMipres,$historia,$ingreso)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$updateEncabezado = "UPDATE ".$wbasedatoMipres."_000001 
								SET Prehis='".$historia."',
									Preing='".$ingreso."' 
							  WHERE Prenop='".$consecutivoMipres."' 
								AND Preest='on';";
								
		$resUpdateEncabezado = mysql_query($updateEncabezado,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updateEncabezado." - ".mysql_error());	
		
		// --------		
		
		$updateMedicamentos = "UPDATE ".$wbasedatoMipres."_000002 
								  SET Medhis='".$historia."',
									  Meding='".$ingreso."' 
								WHERE Mednop='".$consecutivoMipres."' 
								  AND Medest='on';";
								
		$resUpdateMedicamentos = mysql_query($updateMedicamentos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updateMedicamentos." - ".mysql_error());	
		
		// --------		
		
		$updatePrincipiosActivos = "UPDATE ".$wbasedatoMipres."_000003 
									   SET Pamhis='".$historia."',
										   Paming='".$ingreso."' 
									 WHERE Pamnop='".$consecutivoMipres."' 
									   AND Pamest='on';";
								
		$resUpdatePrincipiosActivos = mysql_query($updatePrincipiosActivos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updatePrincipiosActivos." - ".mysql_error());	
		
		// --------		
		
		$updateProcedimientos = "UPDATE ".$wbasedatoMipres."_000004 
									SET Prohis='".$historia."',
										Proing='".$ingreso."' 
								  WHERE Pronop='".$consecutivoMipres."' 
								    AND Proest='on';";
								
		$resUpdateProcedimientos = mysql_query($updateProcedimientos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updateProcedimientos." - ".mysql_error());	
		
		// --------		
		
		$updateDispositivos = "UPDATE ".$wbasedatoMipres."_000005 
								  SET Dishis='".$historia."',
									  Dising='".$ingreso."' 
								WHERE Disnop='".$consecutivoMipres."' 
								  AND Disest='on';";
								
		$resUpdateDispositivos = mysql_query($updateDispositivos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updateDispositivos." - ".mysql_error());	
		
		// --------		
		
		$updateNutriciones = "UPDATE ".$wbasedatoMipres."_000006 
								 SET Nuthis='".$historia."',
									 Nuting='".$ingreso."' 
							   WHERE Nutnop='".$consecutivoMipres."' 
							     AND Nutest='on';";
								
		$resUpdateNutriciones = mysql_query($updateNutriciones,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updateNutriciones." - ".mysql_error());	
		
		// --------		
		
		$updateServicios = "UPDATE ".$wbasedatoMipres."_000007 
							   SET Serhis='".$historia."',
								   Sering='".$ingreso."' 
							 WHERE Sernop='".$consecutivoMipres."' 
							   AND Serest='on';";
								
		$resUpdateServicios = mysql_query($updateServicios,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$updateServicios." - ".mysql_error());	
		
	}
	
	function consultarPrescripcionesPendientesJM($wfechaInicial,$wfechaFinal,$wemp_pmla)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryPrescripcion = "SELECT Jupfpr 
								FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000001 
								 WHERE Jupfpr BETWEEN '".$wfechaInicial."' AND '".$wfechaFinal."' 
									 AND Jupejm='2'
									 AND Jupnpr=Prenop
									 AND Preepr='4'
							GROUP BY Jupfpr;";
		
		$resPrescripcion = mysql_query($queryPrescripcion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPrescripcion . " - " . mysql_error());		   
		$numPrescripcion = mysql_num_rows($resPrescripcion);
		
		$primeraFecha = "";
		if($numPrescripcion>0)
		{
			
			while($rowsPrescripcion = mysql_fetch_array($resPrescripcion))
			{
				
				if($primeraFecha=="")
				{
					$primeraFecha=$rowsPrescripcion['Jupfpr'];
				}
				consumirWebServiceJuntaProfesionalXFecha($rowsPrescripcion['Jupfpr'],$wemp_pmla);
			}
			
			// echo "<script>alert('Desde el ".$primeraFecha." tiene prescripciones pendientes que requieren junta de profesionales')</script>";
			// echo "<script>jAlert('Desde el ".$primeraFecha." tiene prescripciones pendientes que requieren junta de profesionales','ALERTA')</script>";
		}
		
		echo "	<input type='hidden' id='fechaInicialJuntaProfesionales' value='".$primeraFecha."'>";
	
		consultarJuntasPendientesDeRegistrar($wemp_pmla);	
		
	}
	
	function registrarJuntaProfesional($jsonMipres,$wemp_pmla)
	{
		global $conex;
		global $wbasedatoMipres;
		
		if(count($jsonMipres)>0)
		{
			foreach($jsonMipres as $key => $value)
			{
				foreach($value as $key1 => $value1)
				{
					
					registrarJunta($value1->NoPrescripcion);
					
					$value1->Observaciones = str_replace("'","",$value1->Observaciones);
					
					if($value1->FProceso=="")
					{
						$value1->FProceso = "0000-00-00";
					}
					
					$qUpdateJuntaMedica = " UPDATE ".$wbasedatoMipres."_000010 
											   SET Jupfpr='".$value1->FPrescripcion."',
											       Juptte='".$value1->TipoTecnologia."',
											       Jupcon='".$value1->Consecutivo."',
											       Jupejm='".$value1->EstJM."',
												   Jupcep='".$value1->CodEntProc."',
												   Jupobs='".$value1->Observaciones."',
												   Jupnac='".$value1->NoActa."',
												   Jupfpc='".$value1->FProceso."',
												   Juptid='".$value1->TipoIDPaciente."',
												   Jupnid='".$value1->NroIDPaciente."'
											 WHERE Jupnpr='".$value1->NoPrescripcion."' 
											   AND Jupest='on';";
													
					$resUpdateJuntaMedica = mysql_query($qUpdateJuntaMedica,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdateJuntaMedica." - ".mysql_error());	
					
					if($value1->EstJM == "3" || $value1->EstJM == "4")
					{
						actualizarEstadoJuntaMedica($value1->NoPrescripcion,$value1->TipoTecnologia,$value1->Consecutivo,$value1->EstJM);
					}
				}
			}
		}
	}
	
	function actualizarEstadoJuntaMedica($nroPrescripcion,$tipoTecnologia,$consecutivo,$estJM)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryUpdate = "";
		
		if($tipoTecnologia=="M")
		{
			$queryUpdate = " UPDATE ".$wbasedatoMipres."_000002 
								SET Medejm='".$estJM."' 
							  WHERE Mednop='".$nroPrescripcion."' 
							    AND Medcom='".$consecutivo."' 
							    AND Medest='on';";
		}
		elseif($tipoTecnologia=="N")
		{
			$queryUpdate = " UPDATE ".$wbasedatoMipres."_000006 
								SET Nutejm='".$estJM."' 
							  WHERE Nutnop='".$nroPrescripcion."' 
							    AND Nutcon='".$consecutivo."' 
							    AND Nutest='on';";
		}
		elseif($tipoTecnologia=="S")
		{
			$queryUpdate = " UPDATE ".$wbasedatoMipres."_000007 
								SET Serejm='".$estJM."' 
							  WHERE Sernop='".$nroPrescripcion."' 
							    AND Sercos='".$consecutivo."' 
							    AND Serest='on';";
		}
		
		if($queryUpdate!="")
		{
			$resUpdate = mysql_query($queryUpdate,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$queryUpdate." - ".mysql_error());	
		}
	}
	
	function registrarNovedad($jsonMipres,$wemp_pmla)
	{
		global $conex;
		global $wbasedatoMipres;
		
		if(count($jsonMipres)>0)
		{
			foreach($jsonMipres as $key => $value)
			{
				foreach($value as $key1 => $value1)
				{
					
					$queryNovedades ="SELECT * 
										FROM ".$wbasedatoMipres."_000008 
									   WHERE Novcod='".$value1->TipoNov."' 
										 AND Novnpr='".$value1->NoPrescripcion."' 
										 AND Novnpf='".$value1->NoPrescripcionF."' 
										 AND Novfec='".$value1->FNov."' 
										 AND Novest='on';";
										 
					$resNovedades = mysql_query($queryNovedades, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNovedades . " - " . mysql_error());		   
					$numNovedadesn = mysql_num_rows($resNovedades);
					
					if($numNovedadesn==0)
					{
						$qInsertNovedades = " INSERT INTO ".$wbasedatoMipres."_000008 ( Medico ,    Fecha_data     ,	 Hora_data	      ,			Novcod	  	 ,				Novnpr	 	   ,			Novnpf			  ,		Novfec		  ,	Novest,Novact ,Seguridad ) 
																 VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$value1->TipoNov."' ,'".$value1->NoPrescripcion."','".$value1->NoPrescripcionF."','".$value1->FNov."',  'on' , 'off' ,'C-".$wbasedatoMipres."');";
														
						$resInsertNovedades = mysql_query($qInsertNovedades,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertNovedades." - ".mysql_error());	
						
						$numInsertNovedades = mysql_affected_rows();
			
						if($numInsertNovedades>0)
						{
							actualizarNovedad($value1->TipoNov,$value1->NoPrescripcion,$value1->NoPrescripcionF);
						}
						
					}
				}
			}
		}
	}
	
	function actualizarEstadoPrescripcion($nroPrescripcion,$estado)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryEncabezado = "  SELECT * 
								FROM ".$wbasedatoMipres."_000001
							   WHERE Prenop='".$nroPrescripcion."' 
								 AND Preest='on';";
							 
		$resEncabezado = mysql_query($queryEncabezado, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEncabezado . " - " . mysql_error());		   
		$numEncabezado = mysql_num_rows($resEncabezado);
		
		$prescripcionActualizada = false;
		if($numEncabezado>0)
		{
			$queryActualizarEstado = "UPDATE ".$wbasedatoMipres."_000001 
										 SET Preepr='".$estado."'
									   WHERE Prenop='".$nroPrescripcion."' 
										 AND Preest='on';";
								 
			$resActualizarEstado = mysql_query($queryActualizarEstado, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryActualizarEstado . " - " . mysql_error());		   
			
			$numActualizarEstado = mysql_affected_rows();
			
			if($numActualizarEstado>0)
			{
				$prescripcionActualizada = true;
			}
			
		}
		
		return $prescripcionActualizada;
	}
	
	function actualizarCTC($nroPrescripcionAnterior,$nroPrescripcionFinal)
	{
		global $conex;
		global $wbasedato;
		
		// actualizar movhos_000134
		$queryUpdateCtcMedicamentos = "UPDATE ".$wbasedato."_000134 
										  SET Ctcmia='".$nroPrescripcionAnterior."',
											  Ctcmip='".$nroPrescripcionFinal."' 
										WHERE Ctcmip='".$nroPrescripcionAnterior."' 
										  AND Ctcest='on';";
		
		$resUpdateCtcMedicamentos = mysql_query($queryUpdateCtcMedicamentos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryUpdateCtcMedicamentos . " - " . mysql_error());
		
		// actualizar movhos_000135
		$queryUpdateCtcProcedimientos = "UPDATE ".$wbasedato."_000135 
											SET Ctcmia='".$nroPrescripcionAnterior."',
												Ctcmip='".$nroPrescripcionFinal."' 
										  WHERE Ctcmip='".$nroPrescripcionAnterior."' 
											AND Ctcest='on';";
											
		$resUpdateCtcProcedimientos = mysql_query($queryUpdateCtcProcedimientos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryUpdateCtcProcedimientos . " - " . mysql_error());									
	}
	
	function consultarConsecutivoMipres($jsonMipres,$fecha,$hora,$origen)
	{
		$consecutivoMipres = array();
		if(count($jsonMipres)>0)
		{
			foreach($jsonMipres as $key => $value)
			{
				foreach($value as $key1 => $value1)
				{
					
					if($key1=="prescripcion")
					{
						
						$fechaHoraOrdenes = $fecha." ".$hora;
						$fechaHoraOrdenesEnSegudos = strtotime($fechaHoraOrdenes);
						
						$fechaHoraPrescripcion = substr($value1->FPrescripcion,0,10)." ".substr($value1->HPrescripcion,0,8);
						$fechaHoraPrescripcionEnSegudos = strtotime($fechaHoraPrescripcion);
						
						if($fechaHoraPrescripcionEnSegudos>=$fechaHoraOrdenesEnSegudos)
						{
							$consecutivoMipres[] = $value1->NoPrescripcion;
							if($origen=="ordenes")
							{
								break 2;
								
							}
						}
					}
					
				}
			}
		}
		
		return $consecutivoMipres;
	}
	
	function consultarPrescripcionSinRegistrar($idPrescripcion)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryPrescripcion = "SELECT * 
								FROM ".$wbasedatoMipres."_000001 
							   WHERE Prenop='".$idPrescripcion."' 
							     AND Preest='on';";
								 
		$resPrescripcion = mysql_query($queryPrescripcion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPrescripcion . " - " . mysql_error());		   
		$numPrescripcion = mysql_num_rows($resPrescripcion);
		
		$sinRegistrar = true;
		if($numPrescripcion>0)
		{
			$sinRegistrar = false;
		}

		return $sinRegistrar;
	}
	
	function registrarPrescripcionesMipres($jsonMipres,$historia,$ingreso)
	{
		global $wemp_pmla;
		
		
		$arrayEncabezados = array();
		$arrayMedicamentos = array();
		$arrayPrincipiosActivos = array();
		$arrayProcedimientos = array();
		$arrayDispositivos = array();
		$arrayProductosNutricionales = array();
		$arrayServiciosComplementarios = array();
				
		
		$cantidadPrescripciones = 0;
		if(count($jsonMipres)>0)
		{
			foreach($jsonMipres as $key => $value)
			{
				foreach($value as $key1 => $value1)
				{
					if($key1=="prescripcion")
					{
						$idPrescripcion = $value1->NoPrescripcion;
						
						$sinRegistrar = consultarPrescripcionSinRegistrar($idPrescripcion);
					}
					
					if($sinRegistrar)
					{
						if($key1=="prescripcion")
						{
							
							$arrayEncabezados[$idPrescripcion]['NoPrescripcion'] = $value1->NoPrescripcion;
							$arrayEncabezados[$idPrescripcion]['FPrescripcion'] = substr($value1->FPrescripcion,0,10);
							$arrayEncabezados[$idPrescripcion]['HPrescripcion'] = substr($value1->HPrescripcion,0,8);
							$arrayEncabezados[$idPrescripcion]['CodHabIPS'] = $value1->CodHabIPS;
							$arrayEncabezados[$idPrescripcion]['TipoIDIPS'] = $value1->TipoIDIPS;
							$arrayEncabezados[$idPrescripcion]['NroIDIPS'] = $value1->NroIDIPS;
							$arrayEncabezados[$idPrescripcion]['CodDANEMunIPS'] = $value1->CodDANEMunIPS;
							$arrayEncabezados[$idPrescripcion]['DirSedeIPS'] = utf8_decode($value1->DirSedeIPS);
							$arrayEncabezados[$idPrescripcion]['TelSedeIPS'] = $value1->TelSedeIPS;
							$arrayEncabezados[$idPrescripcion]['TipoIDProf'] = $value1->TipoIDProf;
							$arrayEncabezados[$idPrescripcion]['NumIDProf'] = $value1->NumIDProf;
							$arrayEncabezados[$idPrescripcion]['PNProfS'] = utf8_decode($value1->PNProfS);
							$arrayEncabezados[$idPrescripcion]['SNProfS'] = utf8_decode($value1->SNProfS);
							$arrayEncabezados[$idPrescripcion]['PAProfS'] = utf8_decode($value1->PAProfS);
							$arrayEncabezados[$idPrescripcion]['SAProfS'] = utf8_decode($value1->SAProfS);
							$arrayEncabezados[$idPrescripcion]['RegProfS'] = $value1->RegProfS;
							$arrayEncabezados[$idPrescripcion]['TipoIDPaciente'] = $value1->TipoIDPaciente;
							$arrayEncabezados[$idPrescripcion]['NroIDPaciente'] = $value1->NroIDPaciente;
							$arrayEncabezados[$idPrescripcion]['PNPaciente'] = utf8_decode($value1->PNPaciente);
							$arrayEncabezados[$idPrescripcion]['SNPaciente'] = utf8_decode($value1->SNPaciente);
							$arrayEncabezados[$idPrescripcion]['PAPaciente'] = utf8_decode($value1->PAPaciente);
							$arrayEncabezados[$idPrescripcion]['SAPaciente'] = utf8_decode($value1->SAPaciente);
							$arrayEncabezados[$idPrescripcion]['CodAmbAte'] = $value1->CodAmbAte;
							$arrayEncabezados[$idPrescripcion]['EnfHuerfana'] = $value1->EnfHuerfana;
							$arrayEncabezados[$idPrescripcion]['CodEnfHuerfana'] = $value1->CodEnfHuerfana;
							$arrayEncabezados[$idPrescripcion]['CodDxPpal'] = $value1->CodDxPpal;
							$arrayEncabezados[$idPrescripcion]['CodDxRel1'] = $value1->CodDxRel1;
							$arrayEncabezados[$idPrescripcion]['CodDxRel2'] = $value1->CodDxRel2;
							$arrayEncabezados[$idPrescripcion]['SopNutricional'] = $value1->SopNutricional;
							$arrayEncabezados[$idPrescripcion]['CodEPS'] = $value1->CodEPS;
							$arrayEncabezados[$idPrescripcion]['TipoIDMadrePaciente'] = $value1->TipoIDMadrePaciente;
							$arrayEncabezados[$idPrescripcion]['NroIDMadrePaciente'] = $value1->NroIDMadrePaciente;
							$arrayEncabezados[$idPrescripcion]['TipoTransc'] = $value1->TipoTransc;
							$arrayEncabezados[$idPrescripcion]['TipoIDDonanteVivo'] = $value1->TipoIDDonanteVivo;
							$arrayEncabezados[$idPrescripcion]['NroIDDonanteVivo'] = $value1->NroIDDonanteVivo;
							$arrayEncabezados[$idPrescripcion]['EstPres'] = $value1->EstPres;
							
						}
						
						
						if($key1=="medicamentos")
						{
							foreach($value1 as $key2 => $value2)
							{
								$idMedicamento = $key2;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['ConOrden'] = $value2->ConOrden;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['TipoMed'] = $value2->TipoMed;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['TipoPrest'] = $value2->TipoPrest;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CausaS1'] = $value2->CausaS1;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CausaS2'] = $value2->CausaS2;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CausaS3'] = $value2->CausaS3;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['MedPBSUtilizado'] = utf8_decode($value2->MedPBSUtilizado);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS31'] = $value2->RznCausaS31;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescRzn31'] = utf8_decode($value2->DescRzn31);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS32'] = $value2->RznCausaS32;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescRzn32'] = utf8_decode($value2->DescRzn32);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CausaS4'] = $value2->CausaS4;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['MedPBSDescartado'] = utf8_decode($value2->MedPBSDescartado);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS41'] = $value2->RznCausaS41;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescRzn41'] = utf8_decode($value2->DescRzn41);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS42'] = $value2->RznCausaS42;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescRzn42'] = utf8_decode($value2->DescRzn42);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS43'] = $value2->RznCausaS43;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescRzn43'] = utf8_decode($value2->DescRzn43);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS44'] = $value2->RznCausaS44;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescRzn44'] = utf8_decode($value2->DescRzn44);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CausaS5'] = $value2->CausaS5;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['RznCausaS5'] = $value2->RznCausaS5;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CausaS6'] = $value2->CausaS6;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DescMedPrinAct'] = utf8_decode($value2->DescMedPrinAct);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CodFF'] = $value2->CodFF;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CodVA'] = $value2->CodVA;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['JustNoPBS'] = utf8_decode($value2->JustNoPBS);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['Dosis'] = str_replace(",",".",$value2->Dosis);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DosisUM'] = $value2->DosisUM;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['NoFAdmon'] = $value2->NoFAdmon;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CodFreAdmon'] = $value2->CodFreAdmon;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['IndEsp'] = $value2->IndEsp;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CanTrat'] = str_replace(",",".",$value2->CanTrat);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['DurTrat'] = $value2->DurTrat;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['CantTotalF'] = str_replace(",",".",$value2->CantTotalF);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['UFCantTotal'] = $value2->UFCantTotal;
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['IndRec'] = utf8_decode($value2->IndRec);
								$arrayMedicamentos[$idPrescripcion][$idMedicamento]['EstJM'] = $value2->EstJM;
								
								// Si el estado de la junta medica es diferente a 1 consumir el web service por numero de prescripcion
								if($value2->EstJM != "1")
								{
									consumirWebServiceJuntaProfesionalXPrescripcion($idPrescripcion,$wemp_pmla);
								}
								
								foreach($value2->PrincipiosActivos as $key3 => $value3)
								{
									$idMedicamentoPA = $key2;
									$idPrincipioActivo = $key3;
									
									$idMedicamentoPA = $value3->ConOrden;
									$idPrincipioActivo = $value3->CodPriAct;
									
									
									$arrayPrincipiosActivos[$idPrescripcion][$idMedicamentoPA][$idPrincipioActivo]['ConOrden'] = $value3->ConOrden;
									$arrayPrincipiosActivos[$idPrescripcion][$idMedicamentoPA][$idPrincipioActivo]['CodPriAct'] = $value3->CodPriAct;
									$arrayPrincipiosActivos[$idPrescripcion][$idMedicamentoPA][$idPrincipioActivo]['ConcCant'] = str_replace(",",".",$value3->ConcCant);
									$arrayPrincipiosActivos[$idPrescripcion][$idMedicamentoPA][$idPrincipioActivo]['UMedConc'] = $value3->UMedConc;
									$arrayPrincipiosActivos[$idPrescripcion][$idMedicamentoPA][$idPrincipioActivo]['CantCont'] = str_replace(",",".",$value3->CantCont);
									$arrayPrincipiosActivos[$idPrescripcion][$idMedicamentoPA][$idPrincipioActivo]['UMedCantCont'] = $value3->UMedCantCont;
									
								}
								
								$cantidadPrescripciones++;
							}
						}
						
						if($key1=="procedimientos")
						{
							foreach($value1 as $key2 => $value2)
							{
								$idProcedimiento = $key2;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['ConOrden'] = $value2->ConOrden;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['TipoPrest'] = $value2->TipoPrest;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS11'] = $value2->CausaS11;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS12'] = $value2->CausaS12;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS2'] = $value2->CausaS2;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS3'] = $value2->CausaS3;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS4'] = $value2->CausaS4;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['ProPBSUtilizado'] = $value2->ProPBSUtilizado;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS5'] = $value2->CausaS5;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['ProPBSDescartado'] = $value2->ProPBSDescartado;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['RznCausaS51'] = $value2->RznCausaS51;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['DescRzn51'] = utf8_decode($value2->DescRzn51);
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['RznCausaS52'] = $value2->RznCausaS52;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['DescRzn52'] = utf8_decode($value2->DescRzn52);
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS6'] = $value2->CausaS6;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CausaS7'] = $value2->CausaS7;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CodCUPS'] = $value2->CodCUPS;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CanForm'] = $value2->CanForm;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CadaFreUso'] = $value2->CadaFreUso;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CodFreUso'] = $value2->CodFreUso;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['Cant'] = $value2->Cant;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CantTotal'] = $value2->CantTotal;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['CodPerDurTrat'] = $value2->CodPerDurTrat;
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['JustNoPBS'] = utf8_decode($value2->JustNoPBS);
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['IndRec'] = utf8_decode($value2->IndRec);
								$arrayProcedimientos[$idPrescripcion][$idProcedimiento]['EstJM'] = $value2->EstJM;
								
								$cantidadPrescripciones++;
							}
						}
						
						if($key1=="dispositivos")
						{
							foreach($value1 as $key2 => $value2)
							{
								$idDispositivos = $key2;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['ConOrden'] = $value2->ConOrden;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['TipoPrest'] = $value2->TipoPrest;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CausaS1'] = $value2->CausaS1;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CodDisp'] = $value2->CodDisp;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CanForm'] = $value2->CanForm;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CadaFreUso'] = $value2->CadaFreUso;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CodFreUso'] = $value2->CodFreUso;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['Cant'] = $value2->Cant;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CodPerDurTrat'] = $value2->CodPerDurTrat;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['CantTotal'] = $value2->CantTotal;
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['JustNoPBS'] = utf8_decode($value2->JustNoPBS);
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['IndRec'] = utf8_decode($value2->IndRec);
								$arrayDispositivos[$idPrescripcion][$idDispositivos]['EstJM'] = $value2->EstJM;
								
								$cantidadPrescripciones++;
							}
						}
						
						if($key1=="productosnutricionales")
						{
							foreach($value1 as $key2 => $value2)
							{
								$idNutriciones = $key2;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['ConOrden'] = $value2->ConOrden;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['TipoPrest'] = $value2->TipoPrest;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CausaS1'] = $value2->CausaS1;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CausaS2'] = $value2->CausaS2;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CausaS3'] = $value2->CausaS3;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CausaS4'] = $value2->CausaS4;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['ProNutUtilizado'] = utf8_decode($value2->ProNutUtilizado);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['RznCausaS41'] = $value2->RznCausaS41;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescRzn41'] = utf8_decode($value2->DescRzn41);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['RznCausaS42'] = $value2->RznCausaS42;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescRzn42'] = utf8_decode($value2->DescRzn42);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CausaS5'] = $value2->CausaS5;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['ProNutDescartado'] = utf8_decode($value2->ProNutDescartado);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['RznCausaS51'] = $value2->RznCausaS51;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescRzn51'] = utf8_decode($value2->DescRzn51);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['RznCausaS52'] = $value2->RznCausaS52;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescRzn52'] = utf8_decode($value2->DescRzn52);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['RznCausaS53'] = $value2->RznCausaS53;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescRzn53'] = utf8_decode($value2->DescRzn53);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['RznCausaS54'] = $value2->RznCausaS54;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescRzn54'] = utf8_decode($value2->DescRzn54);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DXEnfHuer'] = $value2->DXEnfHuer;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DXVIH'] = $value2->DXVIH;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DXCaPal'] = $value2->DXCaPal;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DXEnfRCEV'] = $value2->DXEnfRCEV;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['TippProNut'] = $value2->TippProNut;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DescProdNutr'] = $value2->DescProdNutr;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CodForma'] = $value2->CodForma;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CodViaAdmon'] = $value2->CodViaAdmon;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['JustNoPBS'] = utf8_decode($value2->JustNoPBS);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['Dosis'] = $value2->Dosis;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DosisUM'] = $value2->DosisUM;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['NoFAdmon'] = $value2->NoFAdmon;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CodFreAdmon'] = $value2->CodFreAdmon;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['IndEsp'] = $value2->IndEsp;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CanTrat'] = $value2->CanTrat;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['DurTrat'] = $value2->DurTrat;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['CantTotalF'] = $value2->CantTotalF;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['UFCantTotal'] = $value2->UFCantTotal;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['IndRec'] = utf8_decode($value2->IndRec);
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['NoPrescAso'] = $value2->NoPrescAso;
								$arrayProductosNutricionales[$idPrescripcion][$idNutriciones]['EstJM'] = $value2->EstJM;
								
								
								// Si el estado de la junta medica es diferente a 1 consumir el web service por numero de prescripcion
								if($value2->EstJM != "1")
								{
									consumirWebServiceJuntaProfesionalXPrescripcion($idPrescripcion,$wemp_pmla);
								}
								
								$cantidadPrescripciones++;
							}
						}
						
						if($key1=="serviciosComplementarios")
						{
							foreach($value1 as $key2 => $value2)
							{
								$idServicios = $key2;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['ConOrden'] = $value2->ConOrden;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['TipoPrest'] = $value2->TipoPrest;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CausaS1'] = $value2->CausaS1;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CausaS2'] = $value2->CausaS2;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CausaS3'] = $value2->CausaS3;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CausaS4'] = $value2->CausaS4;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['DescCausaS4'] = utf8_decode($value2->DescCausaS4);
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CausaS5'] = $value2->CausaS5;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CodSerComp'] = utf8_decode($value2->CodSerComp);
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['DescSerComp'] = $value2->DescSerComp;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CanForm'] = $value2->CanForm;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CadaFreUso'] = $value2->CadaFreUso;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CodFreUso'] = $value2->CodFreUso;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['Cant'] = $value2->Cant;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CantTotal'] = $value2->CantTotal;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['CodPerDurTrat'] = $value2->CodPerDurTrat;
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['JustNoPBS'] = utf8_decode($value2->JustNoPBS);
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['IndRec'] = utf8_decode($value2->IndRec);
								$arrayServiciosComplementarios[$idPrescripcion][$idServicios]['EstJM'] = $value2->EstJM;
								
								
								// Si el estado de la junta medica es diferente a 1 consumir el web service por numero de prescripcion
								if($value2->EstJM != "1")
								{
									consumirWebServiceJuntaProfesionalXPrescripcion($idPrescripcion,$wemp_pmla);
								}
								
								$cantidadPrescripciones++;
							}
						}
						
					}
				}
			}
			
			// if(count($arrayEncabezados)>0)
			if(count($arrayEncabezados)>0 && $cantidadPrescripciones>0)
			{
				registrarPrescripcion($historia,$ingreso,$arrayEncabezados,$arrayMedicamentos,$arrayPrincipiosActivos,$arrayProcedimientos,$arrayDispositivos,$arrayProductosNutricionales,$arrayServiciosComplementarios);
			}
			
		}
	}
	
	function registrarPrescripcion($historia,$ingreso,$arrayEncabezados,$arrayMedicamentos,$arrayPrincipiosActivos,$arrayProcedimientos,$arrayDispositivos,$arrayProductosNutricionales,$arrayServiciosComplementarios)
	{
		global $conex;
		global $wbasedatoMipres;
		
		foreach($arrayEncabezados as $nroPrescripcion => $datosEnc)
		{
			$datosEnc = str_replace("'","",$datosEnc);
			$qInsertEncabezado = " INSERT INTO ".$wbasedatoMipres."_000001 ( Medico ,    Fecha_data     ,	 Hora_data	      ,		Prehis	  ,		Preing	 ,					Prenop			 ,				Prefep			  ,				Prehop			,			Prechi			,			Precii			 ,			 Preidi			 ,				Precdi			  ,				Predii			,				Pretei		  ,				Pretim			,			Preidm			 ,			Prepnm			,			Presnm		   ,			Prepam		  ,				Presam		 ,				Prerps		 ,				Pretip			   ,				Preidp			,				Prepnp		  ,				Presnp			,			Prepap			  ,				Presap			,				Precaa		 ,				Preteh			,				Preceh			  ,			  Precdp		   ,			Predrp			,			Predrs			 ,				Prersn			   ,		Preeps			 ,					Pretmp				,				Preimp				  	 ,				Prettr		   ,					Pretdv			,					Preidv			,			Preepr		   ,Preest,Seguridad ) 
													  VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$datosEnc['NoPrescripcion']."' ,'".$datosEnc['FPrescripcion']."','".$datosEnc['HPrescripcion']."','".$datosEnc['CodHabIPS']."','".$datosEnc['TipoIDIPS']."','".$datosEnc['NroIDIPS']."','".$datosEnc['CodDANEMunIPS']."','".$datosEnc['DirSedeIPS']."','".$datosEnc['TelSedeIPS']."','".$datosEnc['TipoIDProf']."','".$datosEnc['NumIDProf']."','".$datosEnc['PNProfS']."','".$datosEnc['SNProfS']."','".$datosEnc['PAProfS']."','".$datosEnc['SAProfS']."','".$datosEnc['RegProfS']."','".$datosEnc['TipoIDPaciente']."','".$datosEnc['NroIDPaciente']."','".$datosEnc['PNPaciente']."','".$datosEnc['SNPaciente']."','".$datosEnc['PAPaciente']."','".$datosEnc['SAPaciente']."','".$datosEnc['CodAmbAte']."','".$datosEnc['EnfHuerfana']."','".$datosEnc['CodEnfHuerfana']."','".$datosEnc['CodDxPpal']."','".$datosEnc['CodDxRel1']."','".$datosEnc['CodDxRel2']."','".$datosEnc['SopNutricional']."','".$datosEnc['CodEPS']."','".$datosEnc['TipoIDMadrePaciente']."','".$datosEnc['NroIDMadrePaciente']."','".$datosEnc['TipoTransc']."','".$datosEnc['TipoIDDonanteVivo']."','".$datosEnc['NroIDDonanteVivo']."','".$datosEnc['EstPres']."', 'on' ,'C-".$wbasedatoMipres."');";
													
			$resInsertEncabezado = mysql_query($qInsertEncabezado,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertEncabezado." - ".mysql_error());	
												
			if(mysql_affected_rows()==1)
			{
				$data['error'] = 0;
				
				foreach($arrayMedicamentos as $idMed => $arrayMed)
				{
					if($idMed===$nroPrescripcion)
					{
						foreach($arrayMed as $nroPresMed => $datosMed)
						{
							$datosMed = str_replace("'","",$datosMed);
							$qInsertMedicamento = "  INSERT INTO ".$wbasedatoMipres."_000002 ( Medico ,   	Fecha_data	,		Hora_data	, 		Medhis	,	  Meding   ,			Mednop	  ,			Medcom			,			Medtme		   ,			Medtpr			,			Medcs1		   ,			Medcs2		  ,				Medcs3		 ,					Medmpu			,				Medr31		   ,			Medd31			,				Medr32		   ,			Medd32			,			Medcs4		   ,				Medmpd			   ,				Medr41		  ,				Medd41		   ,			Medr42			  ,				Medd42		   ,				Medr43		  ,				Medd43		   ,			Medr44			  ,				Medd44		   ,			Medcs5		  ,				Medrc5			,			Medcs6		   ,				Meddmp			 ,			 Medcff		  ,			  Medcva	   ,			Medjnp			,			Meddos		 ,			Meddum			,			Mednfa			,				Medcfa		   ,			Medcie		 ,				Medctr		,			 Medcdt		   ,			Medctf			 ,				Medufc			,			 Medirp		  ,			 Medejm		   ,Medest,Seguridad ) 
																		VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$nroPrescripcion."','".$datosMed['ConOrden']."','".$datosMed['TipoMed']."','".$datosMed['TipoPrest']."','".$datosMed['CausaS1']."','".$datosMed['CausaS2']."','".$datosMed['CausaS3']."','".$datosMed['MedPBSUtilizado']."','".$datosMed['RznCausaS31']."','".$datosMed['DescRzn31']."','".$datosMed['RznCausaS32']."','".$datosMed['DescRzn32']."','".$datosMed['CausaS4']."','".$datosMed['MedPBSDescartado']."','".$datosMed['RznCausaS41']."','".$datosMed['DescRzn41']."','".$datosMed['RznCausaS42']."','".$datosMed['DescRzn42']."','".$datosMed['RznCausaS43']."','".$datosMed['DescRzn43']."','".$datosMed['RznCausaS44']."','".$datosMed['DescRzn44']."','".$datosMed['CausaS5']."','".$datosMed['RznCausaS5']."','".$datosMed['CausaS6']."','".$datosMed['DescMedPrinAct']."','".$datosMed['CodFF']."','".$datosMed['CodVA']."','".$datosMed['JustNoPBS']."','".$datosMed['Dosis']."','".$datosMed['DosisUM']."','".$datosMed['NoFAdmon']."','".$datosMed['CodFreAdmon']."','".$datosMed['IndEsp']."','".$datosMed['CanTrat']."','".$datosMed['DurTrat']."','".$datosMed['CantTotalF']."','".$datosMed['UFCantTotal']."','".$datosMed['IndRec']."','".$datosMed['EstJM']."', 'on' ,'C-".$wbasedatoMipres."');";
							
							$resInsertMedicamento = mysql_query($qInsertMedicamento,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertMedicamento." - ".mysql_error());	
					
							if(mysql_affected_rows()==1)
							{
								foreach($arrayPrincipiosActivos as $nroPresPrincActivos => $datosPrincActivos)
								{
									if($nroPresPrincActivos===$nroPrescripcion)
									{
										foreach($datosPrincActivos as $nroPresPA => $datosPA1)
										{
											if($datosMed['ConOrden']==$nroPresPA)
											{
												foreach($datosPA1 as $nroPresPA1 => $datosPA)
												{
													$datosPA = str_replace("'","",$datosPA);
													$qInsertPA = " INSERT INTO ".$wbasedatoMipres."_000003 ( Medico ,		 Fecha_data	  ,		 Hora_data	  ,		Pamhis	  ,		Paming	 ,		  Pamnop	    ,			Pamcop		  ,				Pamcpa		  ,			 Pamcca			 ,			 Pamumc			,			 Pamcco		   ,			 Pamumm			  ,Pamest, Seguridad) 
																					  VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$nroPrescripcion."','".$datosPA['ConOrden']."','".$datosPA['CodPriAct']."','".$datosPA['ConcCant']."','".$datosPA['UMedConc']."','".$datosPA['CantCont']."','".$datosPA['UMedCantCont']."', 'on' ,'C-".$wbasedatoMipres."');";
														
													$resInsertPA = mysql_query($qInsertPA,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertPA." - ".mysql_error());	
												}
											}
										}
									}
								}
								
							}
						}
					}
				}
				
				
				foreach($arrayProcedimientos as $idProc => $arrayProc)
				{
					if($idProc===$nroPrescripcion)
					{
						foreach($arrayProc as $nroPresProc => $datosProc)
						{
							if($datosProc['Cant']=="")
							{
								$datosProc['Cant'] = 0;
							}
							$datosProc = str_replace("'","",$datosProc);
							$qInsertProcedimiento = "INSERT INTO ".$wbasedatoMipres."_000004 ( Medico ,		Fecha_data	,		Hora_data	,		Prohis	,	 Proing	   ,			Pronop	  ,				Procop		  ,				Protpr			,				Proc11		 ,				Proc12		  ,				Procs2		  ,				Procs3		  ,				Procs4		  ,					Proppu			  ,				Procs5		  ,					Proppd			   ,				Proc51		   ,				Prod51		 ,				Proc52			 ,				Prod52		   ,			Procs6		   ,			Procs7		   ,			Procup		   ,			Procfo		    ,				Procaf		   ,			Procfu			 ,			Procan		  ,				Procat		  	,				Procdt				,				Projnp		  ,				Proirp		 ,				Proejm	   ,Proest,Seguridad ) 
																		VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$nroPrescripcion."','".$datosProc['ConOrden']."','".$datosProc['TipoPrest']."','".$datosProc['CausaS11']."','".$datosProc['CausaS12']."','".$datosProc['CausaS2']."','".$datosProc['CausaS3']."','".$datosProc['CausaS4']."','".$datosProc['ProPBSUtilizado']."','".$datosProc['CausaS5']."','".$datosProc['ProPBSDescartado']."','".$datosProc['RznCausaS51']."','".$datosProc['DescRzn51']."','".$datosProc['RznCausaS52']."','".$datosProc['DescRzn52']."','".$datosProc['CausaS6']."','".$datosProc['CausaS7']."','".$datosProc['CodCUPS']."','".$datosProc['CanForm']."','".$datosProc['CadaFreUso']."','".$datosProc['CodFreUso']."','".$datosProc['Cant']."','".$datosProc['CantTotal']."','".$datosProc['CodPerDurTrat']."','".$datosProc['JustNoPBS']."','".$datosProc['IndRec']."','".$datosProc['EstJM']."', 'on' ,'C-".$wbasedatoMipres."');";
							
							$resInsertProcedimiento = mysql_query($qInsertProcedimiento,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertProcedimiento." - ".mysql_error());	
						}
					}
				}
				
				foreach($arrayDispositivos as $idDisp => $arrayDisp)
				{
					if($idDisp===$nroPrescripcion)
					{
						foreach($arrayDisp as $nroPresDisp => $datosDisp)
						{
							$datosDisp = str_replace("'","",$datosDisp);
							$qInsertDispositivos = " INSERT INTO ".$wbasedatoMipres."_000005 ( Medico ,		Fecha_data	,	Hora_data		,	Dishis		,	  Dising   ,			Disnop	  ,				Discod		  ,				Distpr			,				Discs1		,				Discdi		,				Discfo		 ,				Discaf			,				Discfu		  ,				Discan	   ,				Discdt			 ,				Discat			,				Disjnp		   ,			Disirp		  ,				Disejm		,Disest,Seguridad ) 
																		VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$nroPrescripcion."','".$datosDisp['ConOrden']."','".$datosDisp['TipoPrest']."','".$datosDisp['CausaS1']."','".$datosDisp['CodDisp']."','".$datosDisp['CanForm']."','".$datosDisp['CadaFreUso']."','".$datosDisp['CodFreUso']."','".$datosDisp['Cant']."','".$datosDisp['CodPerDurTrat']."','".$datosDisp['CantTotal']."' ,'".$datosDisp['JustNoPBS']."','".$datosDisp['IndRec']."','".$datosDisp['EstJM']."', 'on' ,'C-".$wbasedatoMipres."');";
							
							$resInsertDispositivos = mysql_query($qInsertDispositivos,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertDispositivos." - ".mysql_error());	
						}
					}
				}
				
				foreach($arrayProductosNutricionales as $idNutr => $arrayNutr)
				{
					if($idNutr===$nroPrescripcion)
					{
						foreach($arrayNutr as $nroPresNutr => $datosNutr)
						{
							$datosNutr = str_replace("'","",$datosNutr);
							$qInsertNutriciones = "  INSERT INTO ".$wbasedatoMipres."_000006 ( Medico ,		Fecha_data	,	Hora_data		,	Nuthis		,	Nuting	   ,		Nutnop		  ,				Nutcon		  ,				Nuttpr			,				Nutcs1		,				Nutcs2		,				Nutcs3		,			Nutcs4			,					Nutpnu			,				Nutc41			,				Nutd41		  ,					Nutc42		  ,				Nutd42			,			Nutcs5			,				Nutpnd				 ,				Nutc51			 ,				Nutd51		   ,				Nutc52		   ,				Nutd52		 ,				Nutc53			 ,				Nutd53		   ,				Nutc54		   ,				Nutd54		 ,				Nuthue		 	,		Nutvih		 	  ,				Nutccp		  ,				Nuterc		 	,			Nuttpn				,				Nutdpn			 ,			Nutcfo			  ,				Nutcva			  ,				Nutjnp			,			Nutdos		  ,				Nutdum		  ,				Nutnfa		   ,				Nutcfa		  ,			Nuties		     ,			Nutcat		   ,			Nutdut		   ,				Nutctf		  ,					Nutuft		  ,				Nutirp		 ,				Nutnpa			,			Nutejm		    ,Nutest,Seguridad ) 
																		VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."','".$nroPrescripcion."','".$datosNutr['ConOrden']."','".$datosNutr['TipoPrest']."','".$datosNutr['CausaS1']."','".$datosNutr['CausaS2']."','".$datosNutr['CausaS3']."','".$datosNutr['CausaS4']."','".$datosNutr['ProNutUtilizado']."','".$datosNutr['RznCausaS41']."','".$datosNutr['DescRzn41']."','".$datosNutr['RznCausaS42']."','".$datosNutr['DescRzn42']."','".$datosNutr['CausaS5']."','".$datosNutr['ProNutDescartado']."','".$datosNutr['RznCausaS51']."','".$datosNutr['DescRzn51']."','".$datosNutr['RznCausaS52']."','".$datosNutr['DescRzn52']."','".$datosNutr['RznCausaS53']."','".$datosNutr['DescRzn53']."','".$datosNutr['RznCausaS54']."','".$datosNutr['DescRzn54']."','".$datosNutr['DXEnfHuer']."','".$datosNutr['DXVIH']."','".$datosNutr['DXCaPal']."','".$datosNutr['DXEnfRCEV']."','".$datosNutr['TippProNut']."','".$datosNutr['DescProdNutr']."','".$datosNutr['CodForma']."','".$datosNutr['CodViaAdmon']."','".$datosNutr['JustNoPBS']."','".$datosNutr['Dosis']."','".$datosNutr['DosisUM']."','".$datosNutr['NoFAdmon']."','".$datosNutr['CodFreAdmon']."','".$datosNutr['IndEsp']."','".$datosNutr['CanTrat']."','".$datosNutr['DurTrat']."','".$datosNutr['CantTotalF']."','".$datosNutr['UFCantTotal']."','".$datosNutr['IndRec']."','".$datosNutr['NoPrescAso']."','".$datosNutr['EstJM']."', 'on' ,'C-".$wbasedatoMipres."');";
							
							$resInsertNutriciones = mysql_query($qInsertNutriciones,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertNutriciones." - ".mysql_error());	
						}
					}
				}
				
				foreach($arrayServiciosComplementarios as $idServ => $arrayServ)
				{
					if($idServ===$nroPrescripcion)
					{
						foreach($arrayServ as $nroPresServ => $datosServ)
						{
							$datosServ = str_replace("'","",$datosServ);
							$qInsertServCompl = "INSERT INTO ".$wbasedatoMipres."_000007 ( Medico ,		Fecha_data	,	Hora_data		,	 Serhis		,	 Sering		,			Sernop		 ,				Sercos		  ,				Sertpr			,			Sercs1			,			Sercs2			,			Sercs3			,			Sercs4			,				Serdc4			,			Sercs5			,			Sercsc			   ,			Serdsc			   ,			Sercaf		    ,			Serfru		    ,			Sercfu			  ,			Sercan		  ,				Sercat			 ,					Sercdt			,				Serjnp		  ,				Serirp		 ,			Serejm		   ,Serest,Seguridad) 
																	VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$historia."','".$ingreso."' ,'".$nroPrescripcion."','".$datosServ['ConOrden']."','".$datosServ['TipoPrest']."','".$datosServ['CausaS1']."','".$datosServ['CausaS2']."','".$datosServ['CausaS3']."','".$datosServ['CausaS4']."','".$datosServ['DescCausaS4']."','".$datosServ['CausaS5']."','".$datosServ['CodSerComp']."','".$datosServ['DescSerComp']."','".$datosServ['CanForm']."','".$datosServ['CadaFreUso']."','".$datosServ['CodFreUso']."','".$datosServ['Cant']."','".$datosServ['CantTotal']."','".$datosServ['CodPerDurTrat']."','".$datosServ['JustNoPBS']."','".$datosServ['IndRec']."','".$datosServ['EstJM']."', 'on' ,'C-".$wbasedatoMipres."');";
							
							$resInsertServCompl  = mysql_query($qInsertServCompl,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertServCompl." - ".mysql_error());	
						}
					}
				}
				
			}										
		}
		
	}

	function pintarPrescripcionMipres($fechaMipres,$wemp_pmla,$general,$historia,$ingreso,$tipoDocumento,$documento,$pArrayCodPrescMipres,$reporte)
	{
		
		$estiloEncabezado = "style='font-weight:bold;font-family:verdana;font-size:10pt;text-align: center;'";
		
		
		if(!is_array($pArrayCodPrescMipres))
		{
			$arrayCodPrescMipres[] = $pArrayCodPrescMipres;
		}
		else
		{
			$arrayCodPrescMipres = $pArrayCodPrescMipres;
		}
		
		
		
		$cadenaTooltipJMMed = "";
		$cadenaTooltipJMPro = "";
		$cadenaTooltipJMDis = "";
		$cadenaTooltipJMNut = "";
		$cadenaTooltipJMSer = "";
		
		$cadenaTooltipEstado = "";
		$cadenaTooltipPrincipiosActivos = "";
		$cadenaTooltipMedUtl = "";
		$cadenaTooltipMedDesc = "";
		$cadenaTooltipMedDet = "";
		$cadenaTooltipProcUtl = "";
		$cadenaTooltipProcDesc = "";
		$cadenaTooltipProcDet = "";
		$cadenaTooltipDispDet = "";
		$cadenaTooltipNutUtl = "";
		$cadenaTooltipNutDesc = "";
		$cadenaTooltipNutDet = "";
		$cadenaTooltipSerDet = "";
		
		
		$htmlMipres = "";
		$html = "";
		$htmlMipres .= "<div id='modalMipres'><br><br>";
		
		foreach($arrayCodPrescMipres as $keyCodPresMipres => $codPrescMipres)
		{	$html = "";
			$reporteCtcMedicamentos = 0;
			$reporteCtcProcedimientos = 0;
			$encabezadoPrescripcion = consultarEncabezadoPrescripcion($codPrescMipres);
		
			$medicamentosPrescripcion = array();
			$nutricionesPrescripcion = array();
			$procedimientosPrescripcion = array();
			if($reporte=="ctcMedicamentos")
			{
				$medicamentosPrescripcion = consultarMedicamentosPrescripcion($codPrescMipres);
				$nutricionesPrescripcion = consultarProductosNutricionalesPrescripcion($codPrescMipres);
				
			}
			elseif($reporte=="ctcProcedimientos")
			{
				$procedimientosPrescripcion = consultarProcedimientosPrescripcion($codPrescMipres);
				$dispositivosPrescripcion = consultarDispositivosPrescripcion($codPrescMipres);
				$serviciosPrescripcion = consultarServiciosComplementariosPrescripcion($codPrescMipres);
			}
			else
			{
				$medicamentosPrescripcion = consultarMedicamentosPrescripcion($codPrescMipres);
				$nutricionesPrescripcion = consultarProductosNutricionalesPrescripcion($codPrescMipres);
				$procedimientosPrescripcion = consultarProcedimientosPrescripcion($codPrescMipres);
				$dispositivosPrescripcion = consultarDispositivosPrescripcion($codPrescMipres);
				$serviciosPrescripcion = consultarServiciosComplementariosPrescripcion($codPrescMipres);
			}
			
			
			$diagnosticos = "";
			if($encabezadoPrescripcion['CodDxPpal']!="")
			{
				$diagnosticos .= "<b>Diagnóstico principal:</b> ".consultarDiagnostico($encabezadoPrescripcion['CodDxPpal']);
			}
			if($encabezadoPrescripcion['CodDxRel1']!="")
			{
				$diagnosticos .= "<br><b>Diagnóstico relacionado 1:</b> ".consultarDiagnostico($encabezadoPrescripcion['CodDxRel1']);
			}
			if($encabezadoPrescripcion['CodDxRel2']!="")
			{
				$diagnosticos .= "<br><b>Diagnóstico relacionado 2:</b> ".consultarDiagnostico($encabezadoPrescripcion['CodDxRel2']);
			}
			
			$tooltipEstado = "";
			$estiloAnulacion = "";
			if($encabezadoPrescripcion['EstPres']=="2")
			{
				$estiloAnulacion = "style='cursor:pointer; color:#0033ff;text-decoration:underline;font-weight:bold;'";
				
				$detallesAnulacion = consultarDetallesAnulacion($codPrescMipres);
				if(count($detallesAnulacion)>0)
				{
					// ------------------------------------------
					// Tooltip
					// ------------------------------------------	
					
						$infoTooltipEstado = "<table align=\"center\">
											<tr class=\"encabezadoTabla\">
												<td colspan=\"8\" align=\"center\">DETALLES DE LA ANULACIÓN</td>
											</tr>
											<tr class=\"encabezadoTabla\" align=\"center\">
												<td>Tipo de Anulación</td>
												<td>Justificación de la anulación</td>
												<td>Observación</td>
												<td>Fecha de la Solicitud</td>
												<td>Usuario que solicita la anulación</td>
												<td>Estado de la Anulación</td>
												<td>Fecha de la Anulación</td>
												<td>Usuario que Anula</td>
											</tr>";
											
											
												if ($clase_fila=='Fila2')
													$clase_fila = "Fila1";
												else
													$clase_fila = "Fila2";
												
						$infoTooltipEstado .= "		<tr class=\"".$clase_fila."\" >
														<td>".consultarTipoAnulacion($detallesAnulacion['TipoAnulacion'])."</td>
														<td>".$detallesAnulacion['Justificacion']."</td>
														<td>".$detallesAnulacion['Observacion']."</td>
														<td align=\"center\">".$detallesAnulacion['FSolicitud']."</td>
														<td>".$detallesAnulacion['Usuario_Solicita']." - ".consultarNombreUsuario($detallesAnulacion['Usuario_Solicita'])."</td>
														<td align=\"center\">".consultarEstadoAnulacion($detallesAnulacion['EstAnulacion'])."</td>
														<td align=\"center\">".$detallesAnulacion['FAnulacion']."</td>
														<td>".$detallesAnulacion['Usuario_Anula']." - ".consultarNombreUsuario($detallesAnulacion['Usuario_Anula'])."</td>
													</tr>";
											
											
						$infoTooltipEstado .= "</table>";
						$tooltipEstado = "<div id=\"dvTooltipEstado_".$codPrescMipres."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipEstado."</div>";
						$cadenaTooltipEstado .= "tooltipEstado_".$codPrescMipres."|";
					// ------------------------------------------	
				}
				
			}
			
			$hising = "";
			if($historia!="" && $ingreso!="")
			{
				$hising = $historia."-".$ingreso;
			}
			
			$html .= "	<div>
							<table id='tablaEncabezadoMipres' align='center' width='100%'>
								<tr class='encabezadoTabla'>
									<td colspan='8' align='center'>PRESCRIPCIÓN MIPRES</td>
								</tr>
								<tr class='encabezadoTabla' ".$estiloEncabezado.">
									<td colspan='8' align='center'>Datos de la prescripción</td>
								</tr>
								<tr class='fila1' ".$estiloEncabezado.">
									<td rowspan='2' colspan='2'>Número de prescripción</td>
									<td rowspan='2'>Fecha y hora</td>
									<td rowspan='2'>Estado de la prescripción</td>
									<td rowspan='2'>Ámbito de atención</td>
									<td colspan='3'>Realizado por</td>
								</tr>
								<tr class='fila1' ".$estiloEncabezado.">
									<td>Documento</td>
									<td>Nombre</td>
									<td>Registro médico</td>
								</tr>
								<tr class='fila2'>
									<td colspan='2' align='center'>".$codPrescMipres."</td>
									<td align='center'>".$encabezadoPrescripcion['FPrescripcion']." ".$encabezadoPrescripcion['HPrescripcion']."</td>
									<td align='center' id='tooltipEstado_".$codPrescMipres."' title='".$tooltipEstado."' ".$estiloAnulacion.">".consultarEstadoPrescripcion($encabezadoPrescripcion['EstPres'])."</td>
									<td align='center'>".consultarAmbitoAtencion($encabezadoPrescripcion['CodAmbAte'])."</td>
									<td align='center'>".$encabezadoPrescripcion['TipoIDProf']." ".$encabezadoPrescripcion['NumIDProf']."</td>
									<td>".$encabezadoPrescripcion['PNProfS']." ".$encabezadoPrescripcion['SNProfS']." ".$encabezadoPrescripcion['PAProfS']." ".$encabezadoPrescripcion['SAProfS']."</td>
									<td align='center'>".$encabezadoPrescripcion['RegProfS']."</td>
								</tr>
								<tr class='encabezadoTabla' ".$estiloEncabezado.">
									<td colspan='8' align='center'>Datos del paciente</td>
								</tr>
								<tr class='fila1' ".$estiloEncabezado.">
									<td>Historia</td>
									<td>Documento</td>
									<td>Nombre</td>
									<td>Enfermedad huérfana</td>
									<td colspan='2'>Diagnóstico</td>
									<td>Requiere soporte nutricional</td>
									<td>EPS</td>
								</tr>
								<tr class='fila2'>
									<td>".$hising."</td>
									<td>".$encabezadoPrescripcion['TipoIDPaciente']." ".$encabezadoPrescripcion['NroIDPaciente']."</td>
									<td>".$encabezadoPrescripcion['PNPaciente']." ".$encabezadoPrescripcion['SNPaciente']." ".$encabezadoPrescripcion['PAPaciente']." ".$encabezadoPrescripcion['SAPaciente']."</td>
									<td align='center'>".consultarEnfermedadHuerfana($encabezadoPrescripcion['EnfHuerfana'],$encabezadoPrescripcion['CodEnfHuerfana'])."</td>
									<td colspan='2'>".$diagnosticos."</td>
									<td align='center'>".consultarCampoBooleano($encabezadoPrescripcion['SopNutricional'])."</td>
									<td>".consultarEps($encabezadoPrescripcion['CodEPS'])."</td>
								</tr>
							</table>
							<br>";
							
							if(count($medicamentosPrescripcion)>0)
							{
								$reporteCtcMedicamentos++;
								
								$html .= "	<table id='tablaMedicamentosMipres' align='center' width='100%'>
												<tr class='encabezadoTabla'>
													<td colspan='15' align='center'>MEDICAMENTOS</td>
												</tr>
												<tr class='encabezadoTabla' align='center'>
													<td>&nbsp;&nbsp;&nbsp;</td>
													<td>Tipo de medicamento</td>
													<td>Tipo de prestación</td>
													<td>Descripción medicamento</td>
													<td>Forma Farmacéutica</td>
													<td>Vía</td>
													<td>Justificación</td>
													<td>Dosis</td>
													<td>Frecuencia</td>
													<td>Indicaciones Especiales</td>
													<td>Duración Tratamiento</td>
													<td>Cantidad Total Formulada</td>
													<td>Indicaciones o recomendaciones para el paciente</td>
													<td>Estado de la Junta de Profesionales</td>
													<td>Ver detalles de la prescripci&oacute;n</td>
													<!--<td>&nbsp;&nbsp;&nbsp;</td>-->
												</tr>
												";
												foreach($medicamentosPrescripcion as $keyMed => $valueMed)
												{
													if ($fila_lista=='Fila2')
														$fila_lista = "Fila1";
													else
														$fila_lista = "Fila2";
													
													$estiloJM = "";
													if($valueMed['EstJM']=="3" || $valueMed['EstJM']=="4")
													{
														$estiloJM = "style='cursor:pointer; color:#0033ff;text-decoration:underline;font-weight:bold;'";
														$detallesJuntaProfesional = consultarDetallesJuntaProfesional($codPrescMipres);
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
															$infoTooltipJMMed = "<table align=\"center\">
																				<tr class=\"encabezadoTabla\">
																					<td colspan=\"3\" align=\"center\">DETALLES DE LA EVALUACIÓN</td>
																				</tr>
																				<tr class=\"encabezadoTabla\" align=\"center\">
																					<td>Observaciones</td>
																					<td>Número Acta</td>
																					<td>Fecha del Proceso</td>
																				</tr>";
															$infoTooltipJMMed .= "		<tr class=\"Fila1\" >
																							<td>".$detallesJuntaProfesional['Observaciones']."</td>
																							<td>".$detallesJuntaProfesional['NoActa']."</td>
																							<td>".$detallesJuntaProfesional['FProceso']."</td>
																						</tr>";
																				
																				
															$infoTooltipJMMed .= "</table>";
															$tooltipJMMed = "<div id=\"dvTooltipJMMed_".$codPrescMipres."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipJMMed."</div>";
															$cadenaTooltipJMMed .= "tooltipJMMed_".$codPrescMipres."_".$valueMed['ConOrden']."|";
														// ------------------------------------------	
														// 
														
														
													}
													
													
													
													
													
								$html .= "			<tr class='".$fila_lista."'>
														<td align='center'>".$valueMed['ConOrden']."</td>
														<td align='center'>".consultarTipoMedicamento($valueMed['TipoMed'])."</td>
														<td align='center'>".consultarTipoPrestacion($valueMed['TipoPrest'])."</td>
														<td align='center'>".$valueMed['DescMedPrinAct']."</td>
														<td align='center'>".consultarFormaFarmaceutica($valueMed['CodFF'])."</td>
														<td align='center'>".consultarVia($valueMed['CodVA'])."</td>
														<td>".ucfirst($valueMed['JustNoPBS'])."</td>
														<td align='center'>".$valueMed['Dosis']." ".consultarUnidadMedida($valueMed['DosisUM'])."</td>
														<!--<td>".$valueMed['Dosis']." ".consultarUnidadMedida($valueMed['DosisUM'])."</td>-->
														<td align='center'>".$valueMed['NoFAdmon']." ".consultarFrecuencia($valueMed['CodFreAdmon'])."</td>
														<td>".consultarIndicacionesEspeciales($valueMed['IndEsp'])."</td>
														<td align='center'>".$valueMed['CanTrat']." ".consultarDuracionTratamiento($valueMed['DurTrat'])."</td>
														<td align='center'>".$valueMed['CantTotalF']." ".consultarPresentacion($valueMed['UFCantTotal'])."</td>
														<td>".ucfirst($valueMed['IndRec'])."</td>
														<td align='center' id='tooltipJMMed_".$codPrescMipres."_".$valueMed['ConOrden']."' title='".$tooltipJMMed."' ".$estiloJM.">".consultarEstadoJuntaMedica($valueMed['EstJM'])."</td>";
														 
														$botonMedPrincipiosActivos = "";
														
														$principiosActivos = consultarPrinciosActivosPrescripcion($codPrescMipres,$valueMed['ConOrden']);
														
														
														if(count($principiosActivos)>0)
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															
																$infoTooltipMed = "<table align=\"center\">
																					<tr class=\"encabezadoTabla\">
																						<td colspan=\"3\" align=\"center\">PRINCIPIOS ACTIVOS</td>
																					</tr>
																					<tr class=\"encabezadoTabla\">
																						<td>Descripción</td>
																						<td>Concentración</td>
																						<td>Cantidad</td>
																					</tr>";
																					
																					foreach($principiosActivos as $keyPA => $valuePA)
																					{
																						if ($clase_fila=='Fila2')
																							$clase_fila = "Fila1";
																						else
																							$clase_fila = "Fila2";
																						
																$infoTooltipMed .= "		<tr class=\"".$clase_fila."\" >
																							<td>".consultarPrincipioActivo($valuePA['CodPriAct'])."</td>
																							<td>".$valuePA['ConcCant']." ".consultarUnidadMedida($valuePA['UMedConc'])."</td>
																							<td>".$valuePA['CantCont']." ".consultarUnidadMedida($valuePA['UMedCantCont'])."</td>
																						</tr>";
																					}
																					
																$infoTooltipMed .= "</table>";
																$tooltipMed = "<div id=\"dvTooltipPA_".$codPrescMipres."_".$valueMed['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipMed."</div>";
																$cadenaTooltipPrincipiosActivos .= "tooltipMed_".$codPrescMipres."_".$valueMed['ConOrden']."|";
															// ------------------------------------------	
															
															$botonMedPrincipiosActivos = "<span class='detalles' id='tooltipMed_".$codPrescMipres."_".$valueMed['ConOrden']."'  title='".$tooltipMed."'>Principios activos</span>";
														}
															
														
														$botonMedUtilizados = "";
														if($valueMed['MedPBSUtilizado']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															$infoTooltipMedUtl= "	<table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"3\" align=\"center\">MEDICAMENTOS UTILIZADOS</td>
																						</tr>
																						<tr class=\"encabezadoTabla\">
																							<td align=\"center\">Medicamentos PBS utilizado</td>
																							<td colspan=\"2\" align=\"center\">Razones de no utilización</td>
																						</tr>
																						<tr class=\"fila1\" >
																							<td rowspan=\"2\">".$valueMed['MedPBSUtilizado']."</td>
																							<td class=\"fila2\" >Lo utilizó y no se obtuvieron resultados clínicos o paraclínicos satisfactorios en el término previsto de sus indicaciones?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueMed['RznCausaS31']).". ".ucfirst($valueMed['DescRzn31'])."</td>
																						</tr>
																						<tr class=\"fila1\" >
																							<td>Lo utilizó y se observaron reacciones adversas o intolerancia por el paciente?</td>
																							<td>".consultarCampoBooleano($valueMed['RznCausaS32']).". ".ucfirst($valueMed['DescRzn32'])."</td>
																						</tr>
																					</table>";	
															
																
																$tooltipMedUtl = "<div id=\"dvTooltipMedUtl_".$codPrescMipres."_".$valueMed['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipMedUtl."</div>";
																$cadenaTooltipMedUtl .= "tooltipMedUtl_".$codPrescMipres."_".$valueMed['ConOrden']."|";
															// ------------------------------------------
															
															$botonMedUtilizados = "<span class='detalles' id='tooltipMedUtl_".$codPrescMipres."_".$valueMed['ConOrden']."'  title='".$tooltipMedUtl."'>Med. utilizados</span>";
														}
														
														$botonMedDescartados = "";
														if($valueMed['MedPBSDescartado']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															
															$infoTooltipMedDesc = " <table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"3\" align=\"center\">MEDICAMENTOS DESCARTADOS</td>
																						</tr>
																						<tr class=\"encabezadoTabla\">
																							<td>Medicamentos PBS descartado</td>
																							<td colspan=\"2\" align=\"center\">Razón del descarte</td>
																						</tr>
																						<tr >
																							<td rowspan=\"4\" class=\"fila1\">".$valueMed['MedPBSDescartado']."</td>
																							<td class=\"fila1\">Lo descartó porque se prevén reacciones adversas o intolerancia por el paciente?</td>
																							<td class=\"fila1\">".consultarCampoBooleano($valueMed['RznCausaS41']).". ".ucfirst($valueMed['DescRzn41'])."</td>
																						</tr>
																						<tr  >
																							<td class=\"fila2\">Lo descartó porque existen indicaciones o contraindicaciones expresas?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueMed['RznCausaS42']).". ".ucfirst($valueMed['DescRzn42'])."</td>
																						</tr>
																						<tr >
																							<td class=\"fila1\">¿Lo descartó porque no existe otra alternativa en el PBS?</td>
																							<td class=\"fila1\">".consultarCampoBooleano($valueMed['RznCausaS43']).". ".ucfirst($valueMed['DescRzn43'])."</td>
																						</tr>
																						<tr>
																							<td class=\"fila2\">Lo descartó porque tiene mejor evidencia científica disponible sobre seguridad, eficacia y efectividad clínica?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueMed['RznCausaS44']).". ".ucfirst($valueMed['DescRzn44'])."</td>
																						</tr>
																					</table>";	
															
																$tooltipMedDesc = "<div id=\"dvTooltipMedDesc_".$codPrescMipres."_".$valueMed['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipMedDesc."</div>";
																$cadenaTooltipMedDesc .= "tooltipMedDesc_".$codPrescMipres."_".$valueMed['ConOrden']."|";
															// ------------------------------------------
															
															$botonMedDescartados = "<span class='detalles' id='tooltipMedDesc_".$codPrescMipres."_".$valueMed['ConOrden']."'  title='".$tooltipMedDesc."'>Med. descartados</span>";
														}
														
														
														
														$botonMedDetalles = "";
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
														$infoTooltipMedDet = " <table align=\"center\">
																					<tr class=\"encabezadoTabla\">
																						<td colspan=\"2\" align=\"center\">DETALLES ADICIONALES</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>¿La indicación o uso previsto del medicamento está registrado/aprobado por el competente?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueMed['CausaS5'])."</td>
																					</tr>
																					<tr class=\"fila2\">
																						<td>El medicamento aparece en la lista de Uso No Indicado en el Registro Sanitario - UNIR?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueMed['RznCausaS5'])."</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>Existe evidencia científica disponible sobre seguridad, eficacia y efectividad clínica?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueMed['CausaS6'])."</td>
																					</tr>
																					
																				</table>";	
														
															$tooltipMedDet = "<div id=\"dvTooltipMedDet_".$codPrescMipres."_".$valueMed['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipMedDet."</div>";
															$cadenaTooltipMedDet .= "tooltipMedDet_".$codPrescMipres."_".$valueMed['ConOrden']."|";
														// ------------------------------------------
														
														$botonMedDetalles = "<span class='detalles' id='tooltipMedDet_".$codPrescMipres."_".$valueMed['ConOrden']."'  title='".$tooltipMedDet."'>Detalles adicionales</span>";
														
														
														
														
														
														$botonesMedicamentos = "";
														if($botonMedPrincipiosActivos!="")
														{
															$botonesMedicamentos .= $botonMedPrincipiosActivos."<br><br>";
														}
														if($botonMedUtilizados!="")
														{
															$botonesMedicamentos .= $botonMedUtilizados."<br><br>";
														}
														if($botonMedDescartados!="")
														{
															$botonesMedicamentos .= $botonMedDescartados."<br><br>";
														}
														if($botonMedDetalles!="")
														{
															$botonesMedicamentos .= $botonMedDetalles."<br><br>";
														}
														
														$botonesMedicamentos = substr($botonesMedicamentos,0,-8);
														
								$html .= "				<td align='center'>
															".$botonesMedicamentos."
														</td>
													</tr>";
												}
								$html .= "	</table>";	
							}
							
							if(count($procedimientosPrescripcion)>0)
							{
								$reporteCtcProcedimientos++;
								$html .= "	<table id='tablaProcedimientosMipres' align='center' width='100%'>
												<tr class='encabezadoTabla'>
													<td colspan='16' align='center'>PROCEDIMIENTOS</td>
												</tr>
												
												<tr class='encabezadoTabla' align='center'>
													<td>&nbsp;&nbsp;&nbsp;</td>
													<td>Tipo de prestación</td>
													<td>Código CUPS</td>
													<td>Descripción</td>
													<td>Cantidad</td>
													<td>Frecuencia</td>
													<td>Duración Tratamiento</td>
													<td>Cantidad Total</td>
													<td>Justificación No PBS</td>
													<td>Indicaciones o recomendaciones para el paciente</td>
													<td>Estado de la Junta de Profesionales</td>
													<td>Ver detalles de la prescripci&oacute;n</td>
												</tr>
												";
												foreach($procedimientosPrescripcion as $keyProc => $valueProc)
												{
													if ($fila_lista=='Fila2')
														$fila_lista = "Fila1";
													else
														$fila_lista = "Fila2";
													
													$estiloJM = "";
													if($valueProc['EstJM']=="3" || $valueProc['EstJM']=="4")
													{
														$estiloJM = "style='cursor:pointer; color:#0033ff;text-decoration:underline;font-weight:bold;'";
														$detallesJuntaProfesional = consultarDetallesJuntaProfesional($codPrescMipres);
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
															$infoTooltipJMPro = "<table align=\"center\">
																				<tr class=\"encabezadoTabla\">
																					<td colspan=\"3\" align=\"center\">DETALLES DE LA EVALUACIÓN</td>
																				</tr>
																				<tr class=\"encabezadoTabla\" align=\"center\">
																					<td>Observaciones</td>
																					<td>Número Acta</td>
																					<td>Fecha del Proceso</td>
																				</tr>";
															$infoTooltipJMPro .= "		<tr class=\"Fila1\" >
																							<td>".$detallesJuntaProfesional['Observaciones']."</td>
																							<td>".$detallesJuntaProfesional['NoActa']."</td>
																							<td>".$detallesJuntaProfesional['FProceso']."</td>
																						</tr>";
																				
																				
															$infoTooltipJMPro .= "</table>";
															$tooltipJMPro = "<div id=\"dvTooltipJMPro_".$codPrescMipres."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipJMPro."</div>";
															$cadenaTooltipJMPro .= "tooltipJMPro_".$codPrescMipres."_".$valueProc['ConOrden']."|";
														// ------------------------------------------	
														// 
														
														
													}
													
													$duracionDelTratamiento = "";
													if($valueProc['Cant']!="0")
													{
														$duracionDelTratamiento = $valueProc['Cant']." ".consultarDuracionTratamiento($valueProc['CodPerDurTrat']);
													}
													
													
								$html .= "			<tr class='".$fila_lista."'>
														<td align='center'>".$valueProc['ConOrden']."</td>
														<td align='center'>".consultarTipoPrestacion($valueProc['TipoPrest'])."</td>
														<td align='center'>".$valueProc['CodCUPS']."</td>
														<td>".consultarProcedimientoCups($valueProc['CodCUPS'])."</td>
														<td align='center'>".$valueProc['CanForm']."</td>
														<td align='center'>".$valueProc['CadaFreUso']." ".consultarFrecuencia($valueProc['CodFreUso'])."</td>
														<td align='center'>".$duracionDelTratamiento."</td>
														<td align='center'>".$valueProc['CantTotal']."</td>
														<td>".$valueProc['JustNoPBS']."</td>
														<td>".$valueProc['IndRec']."</td>
														<td align='center' id='tooltipJMPro_".$codPrescMipres."_".$valueProc['ConOrden']."' title='".$tooltipJMPro."' ".$estiloJM.">".consultarEstadoJuntaMedica($valueProc['EstJM'])."</td>";
														
														$botonMedPrincipiosActivos = "";
														$botonProcUtilizados = "";
														if($valueProc['ProPBSUtilizado']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															$infoTooltipProcUtl= "	<table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"1\" align=\"center\">PROCEDIMIENTOS UTILIZADOS</td>
																						</tr>
																						<tr class=\"encabezadoTabla\">
																							<td align=\"center\">Procedimiento PBS utilizado</td>
																						</tr>
																						<tr class=\"fila1\" >
																							<td rowspan=\"1\">".consultarProcedimientoCups($valueProc['ProPBSUtilizado'])."</td>
																						</tr>
																					</table>";	
															
																$tooltipProcUtl = "<div id=\"dvTooltipProcUtl_".$codPrescMipres."_".$valueProc['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipProcUtl."</div>";
																$cadenaTooltipProcUtl .= "tooltipProcUtl_".$codPrescMipres."_".$valueProc['ConOrden']."|";
															// ------------------------------------------
															
															$botonProdProcUtilizados = "<span class='detalles' id='tooltipProcUtl_".$codPrescMipres."_".$valueProc['ConOrden']."'  title='".$tooltipProcUtl."'>Proced. utilizados</span>";
														}
														
														$botonProcedDescartados = "";
														if($valueProc['ProPBSDescartado']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															
															$infoTooltipProcDesc = " <table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"3\" align=\"center\">PROCEDIMIENTOS DESCARTADOS</td>
																						</tr>
																						<tr class=\"encabezadoTabla\">
																							<td>Procedimiento descartado</td>
																							<td colspan=\"2\" align=\"center\">Razón del descarte</td>
																						</tr>
																						<tr >
																							<td rowspan=\"4\" class=\"fila1\">".consultarProcedimientoCups($valueProc['ProPBSDescartado'])."</td>
																							<td class=\"fila1\">¿Lo descartó porque no existe alternativa en el PBS?</td>
																							<td class=\"fila1\">".consultarCampoBooleano($valueProc['RznCausaS51']).". ".ucfirst($valueProc['DescRzn51'])."</td>
																						</tr>
																						<tr  >
																							<td class=\"fila2\">Lo descartó porque tiene mejor evidencia científica disponible sobre seguridad, eficacia y efectividad clínica?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueProc['RznCausaS52']).". ".ucfirst($valueProc['DescRzn52'])."</td>
																						</tr>
																					</table>";	
															
																$tooltipProcDesc = "<div id=\"dvTooltipProcDesc_".$codPrescMipres."_".$valueProc['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipProcDesc."</div>";
																$cadenaTooltipProcDesc .= "tooltipProcDesc_".$codPrescMipres."_".$valueProc['ConOrden']."|";
															// ------------------------------------------
															
															$botonProcedDescartados = "<span class='detalles' id='tooltipProcDesc_".$codPrescMipres."_".$valueProc['ConOrden']."'  title='".$tooltipProcDesc."'>Proced. descartados</span>";
														}
														
														
														
														$botonProcDetalles = "";
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
														$infoTooltipProcDet = " <table align=\"center\">
																					<tr class=\"encabezadoTabla\">
																						<td colspan=\"2\" align=\"center\">DETALLES ADICIONALES</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>Tiene CUPS?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueProc['CausaS11'])."</td>
																					</tr>
																					<tr class=\"fila2\">
																						<td>Es una combinación de CUPS?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueProc['CausaS12'])."</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>¿El procedimiento se encuentra en fase experimental?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueProc['CausaS2'])."</td>
																					</tr>
																					<tr class=\"fila2\">
																						<td>¿El Procedimiento se encuentra financiado con recursos de la UPC?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueProc['CausaS3'])."</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>¿Existe evidencia científica disponible sobre seguridad, eficacia y efectividad clínica?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueProc['CausaS6'])."</td>
																					</tr>
																					<tr class=\"fila2\">
																						<td>¿El Procedimiento prescrito se realizará en Colombia?</td>
																						<td align=\"center\">".consultarCampoBooleano($valueProc['CausaS7'])."</td>
																					</tr>
																				</table>";	
														
															$tooltipProcDet = "<div id=\"dvTooltipMedDet_".$codPrescMipres."_".$valueProc['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipProcDet."</div>";
															$cadenaTooltipProcDet .= "tooltipProcDet_".$codPrescMipres."_".$valueProc['ConOrden']."|";
														// ------------------------------------------
														
														$botonProcDetalles = "<span class='detalles' id='tooltipProcDet_".$codPrescMipres."_".$valueProc['ConOrden']."'  title='".$tooltipProcDet."'>Detalles adicionales</span>";
														
														
														
														$botonesProcedimientos = "";
														if($botonProcDetalles!="")
														{
															$botonesProcedimientos .= $botonProcDetalles."<br><br>";
														}
														if($botonProcUtilizados!="")
														{
															$botonesProcedimientos .= $botonProcUtilizados."<br><br>";
														}
														if($botonProcedDescartados!="")
														{
															$botonesProcedimientos .= $botonProcedDescartados."<br><br>";
														}
														
														$botonesProcedimientos = substr($botonesProcedimientos,0,-8);
														
								$html .= "				<td align='center'>
															".$botonesProcedimientos."
														</td>
													</tr>";
												}
								$html .= "	</table>";	
							}
							
							if(count($dispositivosPrescripcion)>0)
							{
								$reporteCtcProcedimientos++;
								
								$html .= "	<table id='tablaProcedimientosMipres' align='center' width='100%'>
												<tr class='encabezadoTabla'>
													<td colspan='16' align='center'>DISPOSITIVOS</td>
												</tr>
												
												<tr class='encabezadoTabla' align='center'>
													<td>&nbsp;&nbsp;&nbsp;</td>
													<td>Tipo de prestación</td>
													<td>Descripción</td>
													<td>Cantidad</td>
													<td>Frecuencia</td>
													<td>Duración Tratamiento</td>
													<td>Cantidad total</td>
													<td>Justificación No PBS</td>
													<td>Indicaciones o recomendaciones para el paciente</td>
													<td>Estado de la Junta de Profesionales</td>
													<td>Ver detalles de la prescripci&oacute;n</td>
												</tr>
												";
												foreach($dispositivosPrescripcion as $keyDisp => $valueDisp)
												{
													if ($fila_lista=='Fila2')
														$fila_lista = "Fila1";
													else
														$fila_lista = "Fila2";
													
													$estiloJM = "";
													if($valueDisp['EstJM']=="3" || $valueDisp['EstJM']=="4")
													{
														$estiloJM = "style='cursor:pointer; color:#0033ff;text-decoration:underline;font-weight:bold;'";
														$detallesJuntaProfesional = consultarDetallesJuntaProfesional($codPrescMipres);
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
															$infoTooltipJMDis = "<table align=\"center\">
																				<tr class=\"encabezadoTabla\">
																					<td colspan=\"3\" align=\"center\">DETALLES DE LA EVALUACIÓN</td>
																				</tr>
																				<tr class=\"encabezadoTabla\" align=\"center\">
																					<td>Observaciones</td>
																					<td>Número Acta</td>
																					<td>Fecha del Proceso</td>
																				</tr>";
															$infoTooltipJMDis .= "		<tr class=\"Fila1\" >
																							<td>".$detallesJuntaProfesional['Observaciones']."</td>
																							<td>".$detallesJuntaProfesional['NoActa']."</td>
																							<td>".$detallesJuntaProfesional['FProceso']."</td>
																						</tr>";
																				
																				
															$infoTooltipJMDis .= "</table>";
															$tooltipJMDis = "<div id=\"dvTooltipJMDis_".$codPrescMipres."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipJMDis."</div>";
															$cadenaTooltipJMDis .= "tooltipJMDis_".$codPrescMipres."_".$valueDisp['ConOrden']."|";
														// ------------------------------------------	
														// 
														
														
													}
													
								$html .= "			<tr class='".$fila_lista."'>
														<td align='center'>".$valueDisp['ConOrden']."</td>
														<td align='center'>".consultarTipoPrestacion($valueDisp['TipoPrest'])."</td>
														<td>".consultarDispositivo($valueDisp['CodDisp'])."</td>
														<td align='center'>".$valueDisp['CanForm']."</td>
														<td align='center'>".$valueDisp['CadaFreUso']." ".consultarFrecuencia($valueDisp['CodFreUso'])."</td>
														<td align='center'>".$valueDisp['Cant']." ".consultarDuracionTratamiento($valueDisp['CodPerDurTrat'])."</td>
														<td align='center'>".$valueDisp['CantTotal']."</td>
														<td>".$valueDisp['JustNoPBS']."</td>
														<td>".$valueDisp['IndRec']."</td>
														<td align='center' id='tooltipJMDis_".$codPrescMipres."_".$valueDisp['ConOrden']."' title='".$tooltipJMDis."' ".$estiloJM.">".consultarEstadoJuntaMedica($valueDisp['EstJM'])."</td>";
														
														$botonVerDetalles = "";
														
														
														$botonesDispositivos = "";
														if($botonVerDetalles!="")
														{
															$botonesDispositivos .= $botonVerDetalles;
														}
														
								
								$html .= "				<td align='center'>
															".$botonesDispositivos."
														</td>
													</tr>";
												}
								$html .= "	</table>";	
							}
							
							if(count($nutricionesPrescripcion)>0)
							{
								$reporteCtcMedicamentos++;
								
								$html .= "	<table id='tablaNutricionesMipres' align='center' width='100%'>
												<tr class='encabezadoTabla'>
													<td colspan='16' align='center'>PRODUCTOS NUTRICIONALES</td>
												</tr>
												
												<tr class='encabezadoTabla' align='center'>
													<td>&nbsp;&nbsp;&nbsp;</td>
													<td>Tipo Producto Nutricional</td>
													<td>Tipo de prestación</td>
													<td>Producto Nutricional</td>
													<td>Forma Farmacéutica</td>
													<td>Vía</td>
													<td>Justificación</td>
													<td>Dosis</td>
													<td>Frecuencia</td>
													<td>Duración Tratamiento</td>
													<td>Cantidad Total Formulada</td>
													<td>Indicaciones especiales</td>
													<td>Indicaciones o recomendaciones para el paciente</td>
													<td>Estado de la Junta de Profesionales</td>
													<td>Ver detalles de la prescripci&oacute;n</td>
												</tr>
												";
												foreach($nutricionesPrescripcion as $keyNut => $valueNut)
												{
													if ($fila_lista=='Fila2')
														$fila_lista = "Fila1";
													else
														$fila_lista = "Fila2";
													
													$estiloJM = "";
													if($valueNut['EstJM']=="3" || $valueNut['EstJM']=="4")
													{
														$estiloJM = "style='cursor:pointer; color:#0033ff;text-decoration:underline;font-weight:bold;'";
														$detallesJuntaProfesional = consultarDetallesJuntaProfesional($codPrescMipres);
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
															$infoTooltipJMNut = "<table align=\"center\">
																				<tr class=\"encabezadoTabla\">
																					<td colspan=\"3\" align=\"center\">DETALLES DE LA EVALUACIÓN</td>
																				</tr>
																				<tr class=\"encabezadoTabla\" align=\"center\">
																					<td>Observaciones</td>
																					<td>Número Acta</td>
																					<td>Fecha del Proceso</td>
																				</tr>";
															$infoTooltipJMNut .= "		<tr class=\"Fila1\" >
																							<td>".$detallesJuntaProfesional['Observaciones']."</td>
																							<td>".$detallesJuntaProfesional['NoActa']."</td>
																							<td>".$detallesJuntaProfesional['FProceso']."</td>
																						</tr>";
																				
																				
															$infoTooltipJMNut .= "</table>";
															$tooltipJMNut = "<div id=\"dvTooltipJMNut_".$codPrescMipres."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipJMNut."</div>";
															$cadenaTooltipJMNut .= "tooltipJMNut_".$codPrescMipres."_".$valueNut['ConOrden']."|";
														// ------------------------------------------	
														// 
														
														
													}
													
													
								$html .= "			<tr class='".$fila_lista."'>
														<td align='center'>".$valueNut['ConOrden']."</td>
														<td>".consultarTipoProductoNutricional($valueNut['TippProNut'])."</td>
														<td align='center'>".consultarTipoPrestacion($valueNut['TipoPrest'])."</td>
														<td>".consultarProductoNutricional($valueNut['DescProdNutr'])."</td>
														<td align='center'>".consultarFormaProductoNutricional($valueNut['CodForma'])."</td>
														<td align='center'>".consultarViaProductoNutricional($valueNut['CodViaAdmon'])."</td>
														<td>".$valueNut['JustNoPBS']."</td>
														<td align='center'>".$valueNut['Dosis']." ".consultarUnidadMedida($valueNut['DosisUM'])."</td>
														<td align='center'>".$valueNut['NoFAdmon']." ".consultarFrecuencia($valueNut['CodFreAdmon'])."</td>
														<td align='center'>".$valueNut['CanTrat']." ".consultarDuracionTratamiento($valueNut['DurTrat'])."</td>
														<td align='center'>".$valueNut['CantTotalF']." ".consultarFormaProductoNutricional($valueNut['UFCantTotal'])."</td>
														<td>".consultarIndicacionesEspeciales($valueNut['IndEsp'])."</td>
														<td>".$valueNut['IndRec']."</td>
														<td align='center' id='tooltipJMNut_".$codPrescMipres."_".$valueNut['ConOrden']."' title='".$tooltipJMNut."' ".$estiloJM.">".consultarEstadoJuntaMedica($valueNut['EstJM'])."</td>";
														
														$botonNutDetalles = "";
														$botonProdNutrUtilizados = "";
														if($valueNut['ProNutUtilizado']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															$infoTooltipNutUtl= "	<table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"3\" align=\"center\">PRODUCTOS NUTRICIONALES UTILIZADOS</td>
																						</tr>
																						<tr class=\"encabezadoTabla\">
																							<td align=\"center\">Producto nutricional utilizado</td>
																							<td colspan=\"2\" align=\"center\">Razones de no utilización</td>
																						</tr>
																						<tr class=\"fila1\" >
																							<td rowspan=\"2\">".$valueNut['ProNutUtilizado']."</td>
																							<td class=\"fila2\" >Lo utilizó y no se obtuvieron resultados clínicos o paraclínicos satisfactorios en el término previsto de sus indicaciones?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueNut['RznCausaS41']).". ".ucfirst($valueNut['DescRzn41'])."</td>
																						</tr>
																						<tr class=\"fila1\" >
																							<td>Lo utilizó y se observaron reacciones adversas o intolerancia por el paciente?</td>
																							<td>".consultarCampoBooleano($valueNut['RznCausaS42']).". ".ucfirst($valueNut['DescRzn42'])."</td>
																						</tr>
																					</table>";	
															
																$tooltipNutUtl = "<div id=\"dvTooltipNutUtl_".$codPrescMipres."_".$valueNut['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipNutUtl."</div>";
																$cadenaTooltipNutUtl .= "tooltipNutUtl_".$codPrescMipres."_".$valueNut['ConOrden']."|";
															// ------------------------------------------
															
															$botonProdNutrUtilizados = "<span class='detalles' id='tooltipNutUtl_".$codPrescMipres."_".$valueNut['ConOrden']."'  title='".$tooltipNutUtl."'>Prod Nut. utilizados</span>";
														}
														
														$botonProdNutrDescartados = "";
														if($valueNut['ProNutDescartado']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															
															$infoTooltipNutDesc = " <table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"3\" align=\"center\">PRODUCTOS NUTRICIONALES DESCARTADOS</td>
																						</tr>
																						<tr class=\"encabezadoTabla\">
																							<td>Producto nutricional descartado</td>
																							<td colspan=\"2\" align=\"center\">Razón del descarte</td>
																						</tr>
																						<tr >
																							<td rowspan=\"4\" class=\"fila1\">".$valueNut['ProNutDescartado']."</td>
																							<td class=\"fila1\">Lo descartó porque se prevén reacciones adversas o intolerancia por el paciente?</td>
																							<td class=\"fila1\">".consultarCampoBooleano($valueNut['RznCausaS51']).". ".ucfirst($valueNut['DescRzn51'])."</td>
																						</tr>
																						<tr  >
																							<td class=\"fila2\">Lo descartó porque existen indicaciones o contraindicaciones expresas?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueNut['RznCausaS52']).". ".ucfirst($valueNut['DescRzn52'])."</td>
																						</tr>
																						<tr >
																							<td class=\"fila1\">Lo descartó porque no existe otra alternativa en el PBS?</td>
																							<td class=\"fila1\">".consultarCampoBooleano($valueNut['RznCausaS53']).". ".ucfirst($valueNut['DescRzn53'])."</td>
																						</tr>
																						<tr>
																							<td class=\"fila2\">Lo descartó porque tiene mejor evidencia científica disponible sobre seguridad, eficacia y efectividad clínica?</td>
																							<td class=\"fila2\">".consultarCampoBooleano($valueNut['RznCausaS54']).". ".ucfirst($valueNut['DescRzn54'])."</td>
																						</tr>
																					</table>";	
															
																$tooltipNutDesc = "<div id=\"dvTooltipNutDesc_".$codPrescMipres."_".$valueNut['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipNutDesc."</div>";
																$cadenaTooltipNutDesc .= "tooltipNutDesc_".$codPrescMipres."_".$valueNut['ConOrden']."|";
															// ------------------------------------------
															
															$botonProdNutrDescartados = "<span class='detalles' id='tooltipNutDesc_".$codPrescMipres."_".$valueNut['ConOrden']."'  title='".$tooltipNutDesc."'>Prod Nut. descartados</span>";
														}
														
														if($valueNut['NoPrescAso']!="")
														{
															// ------------------------------------------
															// Tooltip
															// ------------------------------------------	
															
															$infoTooltipNutDet = " <table align=\"center\">
																						<tr class=\"encabezadoTabla\">
																							<td colspan=\"2\" align=\"center\">DETALLES ADICIONALES</td>
																						</tr>
																						<tr class=\"fila1\">
																							<td>Número de prescripción asociada</td>
																							<td align=\"center\">".$valueNut['NoPrescAso']."</td>
																						</tr>
																					</table>";	
															
																$tooltipNutDet = "<div id=\"dvTooltipNutDet_".$codPrescMipres."_".$valueNut['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipNutDet."</div>";
																$cadenaTooltipNutDet .= "tooltipNutDet_".$codPrescMipres."_".$valueNut['ConOrden']."|";
															// ------------------------------------------
															
															$botonNutDetalles = "<span class='detalles' id='tooltipNutDet_".$codPrescMipres."_".$valueNut['ConOrden']."'  title='".$tooltipNutDet."'>Detalles adicionales</span>";
															
														}
														
														// agregar diagnosticos
														
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
														$infoTooltipNutDet = " <table align=\"center\">
																					<tr class=\"encabezadoTabla\">
																						<td colspan=\"2\" align=\"center\">DIAGNÓSTICOS</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>Diagnóstico de Enfermedad Huérfana,Enfermedad rara, Ultra-Huérfana y Olvidada</td>
																						<td align=\"center\">".consultarCampoBooleano($valueNut['DXEnfHuer'])."</td>
																					</tr>
																					<tr class=\"fila2\">
																						<td>Diagnóstico de VIH</td>
																						<td align=\"center\">".consultarCampoBooleano($valueNut['DXVIH'])."</td>
																					</tr>
																					<tr class=\"fila1\">
																						<td>Diagnóstico de Cáncer en cuidado paliativo</td>
																						<td align=\"center\">".consultarCampoBooleano($valueNut['DXCaPal'])."</td>
																					</tr>
																					<tr class=\"fila2\">
																						<td>Diagnóstico de Enfermedad Renal Crónica Estadio V</td>
																						<td align=\"center\">".consultarCampoBooleano($valueNut['DXEnfRCEV'])."</td>
																					</tr>
																				</table>";	
														
															$tooltipNutDet = "<div id=\"dvTooltipNutDet_".$codPrescMipres."_".$valueNut['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipNutDet."</div>";
															$cadenaTooltipNutDet .= "tooltipNutDet_".$codPrescMipres."_".$valueNut['ConOrden']."|";
														// ------------------------------------------
														
														$botonNutDiagnosticos = "<span class='detalles' id='tooltipNutDet_".$codPrescMipres."_".$valueNut['ConOrden']."'  title='".$tooltipNutDet."'>Detalles adicionales</span>";
														
														
														$botonesNutriciones = "";
														if($botonProdNutrUtilizados!="")
														{
															$botonesNutriciones .= $botonProdNutrUtilizados."<br><br>";
														}
														if($botonProdNutrDescartados!="")
														{
															$botonesNutriciones .= $botonProdNutrDescartados."<br><br>";
														}
														if($botonNutDetalles!="")
														{
															$botonesNutriciones .= $botonNutDetalles."<br><br>";
														}
														if($botonNutDiagnosticos!="")
														{
															$botonesNutriciones .= $botonNutDiagnosticos."<br><br>";
														}
														
														$botonesNutriciones = substr($botonesNutriciones,0,-8);
														
								
								$html .= "				<td align='center'>
															".$botonesNutriciones."
														</td>
													</tr>";
												}
								$html .= "	</table>";	
							}
							if(count($serviciosPrescripcion)>0)
							{
								$reporteCtcProcedimientos++;
								
								$html .= "	<table id='tablaNutricionesMipres' align='center' width='100%'>
												<tr class='encabezadoTabla'>
													<td colspan='15' align='center'>SERVICIOS COMPLEMENTARIOS</td>
												</tr>
												
												<tr class='encabezadoTabla' align='center'>
													<td>&nbsp;&nbsp;&nbsp;</td>
													<td>Tipo de prestación</td>
													<td>Servicio complementario</td>
													<td>Descripción</td>
													<td>Cantidad</td>
													<td>Frecuencia</td>
													<td>Duración Tratamiento</td>
													<td>Cantidad del tratamiento</td>
													<td>Justificación</td>
													<td>Indicaciones o recomendaciones para el paciente</td>
													<td>Estado de la Junta de Profesionales</td>
													<td>Ver detalles de la prescripci&oacute;n</td>
												</tr>
												";
												foreach($serviciosPrescripcion as $keySer => $valueSer)
												{
													if ($fila_lista=='Fila2')
														$fila_lista = "Fila1";
													else
														$fila_lista = "Fila2";
													
													$estiloJM = "";
													if($valueSer['EstJM']=="3" || $valueSer['EstJM']=="4")
													{
														$estiloJM = "style='cursor:pointer; color:#0033ff;text-decoration:underline;font-weight:bold;'";
														$detallesJuntaProfesional = consultarDetallesJuntaProfesional($codPrescMipres);
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														
															$infoTooltipJMSer = "<table align=\"center\">
																				<tr class=\"encabezadoTabla\">
																					<td colspan=\"3\" align=\"center\">DETALLES DE LA EVALUACIÓN</td>
																				</tr>
																				<tr class=\"encabezadoTabla\" align=\"center\">
																					<td>Observaciones</td>
																					<td>Número Acta</td>
																					<td>Fecha del Proceso</td>
																				</tr>";
															$infoTooltipJMSer .= "		<tr class=\"Fila1\" >
																							<td>".$detallesJuntaProfesional['Observaciones']."</td>
																							<td>".$detallesJuntaProfesional['NoActa']."</td>
																							<td>".$detallesJuntaProfesional['FProceso']."</td>
																						</tr>";
																				
																				
															$infoTooltipJMSer .= "</table>";
															$tooltipJMSer = "<div id=\"dvTooltipJMSer_".$codPrescMipres."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipJMSer."</div>";
															$cadenaTooltipJMSer .= "tooltipJMSer_".$codPrescMipres."_".$valueSer['ConOrden']."|";
														// ------------------------------------------	
														// 
														
														
													}
													
													
								$html .= "			<tr class='".$fila_lista."'>
														<td align='center'>".$valueSer['ConOrden']."</td>
														<td align='center'>".consultarTipoPrestacion($valueSer['TipoPrest'])."</td>
														<td>".consultarServicioComplementario($valueSer['CodSerComp'])."</td>
														<td>".utf8_decode($valueSer['DescSerComp'])."</td>
														<td align='center'>".$valueSer['CanForm']."</td>
														<td align='center'>".$valueSer['CadaFreUso']." ".consultarFrecuencia($valueSer['CodFreUso'])."</td>
														<td align='center'>".$valueSer['Cant']." ".consultarDuracionTratamiento($valueSer['CodPerDurTrat'])."</td>
														<td align='center'>".$valueSer['CantTotal']."</td>
														<td>".$valueSer['JustNoPBS']."</td>
														<td>".$valueSer['IndRec']."</td>
														<td align='center' id='tooltipJMSer_".$codPrescMipres."_".$valueSer['ConOrden']."' title='".$tooltipJMSer."' ".$estiloJM.">".consultarEstadoJuntaMedica($valueSer['EstJM'])."</td>";
														
														$botonServCompDetalles = "";
														
														// ------------------------------------------
														// Tooltip
														// ------------------------------------------	
														$infoTooltipSerDet= "	<table align=\"center\">
																					<tr class=\"encabezadoTabla\">
																						<td colspan=\"2\" align=\"center\">DETALLES ADICIONALES</td>
																					</tr>
																					<tr class=\"fila2\" >
																						<td>El uso del servicio es cosmético o suntuario?</td>
																						<td>".consultarCampoBooleano($valueSer['CausaS1']).". </td>
																					</tr>
																					<tr class=\"fila1\" >
																						<td>El servicio se prestará en Colombia?</td>
																						<td>".consultarCampoBooleano($valueSer['CausaS2']).". </td>
																					</tr>
																					<tr class=\"fila2\" >
																						<td>El servicio está registrado por la autoridad competente?</td>
																						<td>".consultarCampoBooleano($valueSer['CausaS3']).". </td>
																					</tr>
																					<tr class=\"fila1\" >
																						<td>El servicio corresponde a la condición clínica y diagnóstico del paciente?</td>
																						<td>".consultarCampoBooleano($valueSer['CausaS4']).". ".ucfirst($valueSer['DescCausaS4'])."</td>
																					</tr>
																					<tr class=\"fila2\" >
																						<td>Existe evidencia disponible sobre seguridad, eficacia y efectividad?</td>
																						<td>".consultarCampoBooleano($valueSer['CausaS5']).". </td>
																					</tr>
																				</table>";	
														
															$tooltipSerDet = "<div id=\"dvTooltipSerDet_".$codPrescMipres."_".$valueSer['ConOrden']."\" style=\"font-family:verdana;font-size:10pt\">".$infoTooltipSerDet."</div>";
															$cadenaTooltipSerDet .= "tooltipSerDet_".$codPrescMipres."_".$valueSer['ConOrden']."|";
														// ------------------------------------------
														
														$botonServCompDetalles = "<span class='detalles' id='tooltipSerDet_".$codPrescMipres."_".$valueSer['ConOrden']."'  title='".$tooltipSerDet."'>Ver detalles</span>";
													
														$botonesServicios = "";
														if($botonServCompDetalles!="")
														{
															$botonesServicios .= $botonServCompDetalles;
														}
								
								$html .= "				<td align='center'>
															".$botonesServicios."
														</td>
													</tr>";
												}
								$html .= "	</table>";	
							}
							
			
			$html .= "</div>
			
						<br><br>";
			// var_dump("--------");
			// var_dump($reporte);
			// if(($reporte=="ctcMedicamentos" && $reporteCtcMedicamentos!=0) || ($reporte=="ctcProcedimientos" && $reporteCtcProcedimientos!=0))
			if(($reporte=="ctcMedicamentos" && $reporteCtcMedicamentos!=0) || ($reporte=="ctcProcedimientos" && $reporteCtcProcedimientos!=0) || ($reporte=="" && ($reporteCtcMedicamentos!=0 || $reporteCtcProcedimientos!=0)))
			{
				$htmlMipres .=  $html;
			}
			
			
		}
			
		
		
		if($reporte=="ctcMedicamentos" && $htmlMipres=="<div id='modalMipres'><br><br>")
		{
			$htmlMipres = "	<div id='modalMipres'>
							<br>
							<p><b>No hay prescripciones de Medicamentos o Productos nutricinales registradas en MIPRES para el paciente.</b></p>
							<input type='button' value='Cerrar' onclick='cerrarModal();'>
						</div>";
		}
		elseif($reporte=="ctcProcedimientos" && $htmlMipres=="<div id='modalMipres'><br><br>")
		{
			$htmlMipres = "	<div id='modalMipres'>
							<br>
							<p><b>No hay prescripciones de Procedimientos, Dispositivos o Servicios complementarios registradas en MIPRES para el paciente.</b></p>
							<input type='button' value='Cerrar' onclick='cerrarModal();'>
						</div>";
		}
		elseif($reporte=="" && $htmlMipres=="<div id='modalMipres'><br><br>")
		{
			$htmlMipres = "	<div id='modalMipres'>
							<br>
							<p><b>No se encontró el registro de la prescripciones MIPRES.</b></p>
							<input type='button' value='Cerrar' onclick='cerrarModal();'>
						</div>";
		}
		else
		{
			$htmlMipres .= "	<input type='button' value='Cerrar' onclick='cerrarModal();'>
								<input type='hidden' id='tooltipJMMed' value='".$cadenaTooltipJMMed."'>
								<input type='hidden' id='tooltipJMPro' value='".$cadenaTooltipJMPro."'>
								<input type='hidden' id='tooltipJMDis' value='".$cadenaTooltipJMDis."'>
								<input type='hidden' id='tooltipJMNut' value='".$cadenaTooltipJMNut."'>
								<input type='hidden' id='tooltipJMSer' value='".$cadenaTooltipJMSer."'>
								<input type='hidden' id='tooltipEstadoPrescripcion' value='".$cadenaTooltipEstado."'>
								<input type='hidden' id='tooltipPrincipiosActivos' value='".$cadenaTooltipPrincipiosActivos."'>
								<input type='hidden' id='tooltipMedicamentosUtilizados' value='".$cadenaTooltipMedUtl."'>
								<input type='hidden' id='tooltipMedicamentosDescartados' value='".$cadenaTooltipMedDesc."'>
								<input type='hidden' id='tooltipMedicamentosDetalles' value='".$cadenaTooltipMedDet."'>
								<input type='hidden' id='tooltipProcedimientosUtilizados' value='".$cadenaTooltipProcUtl."'>
								<input type='hidden' id='tooltipProcedimientosDescartados' value='".$cadenaTooltipProcDesc."'>
								<input type='hidden' id='tooltipProcedimientosDetalles' value='".$cadenaTooltipProcDet."'>
								<input type='hidden' id='tooltipDispositivosDetalles' value='".$cadenaTooltipDispDet."'>
								<input type='hidden' id='tooltipProdNutricionalesUtilizados' value='".$cadenaTooltipNutUtl."'>
								<input type='hidden' id='tooltipProdNutricionalesDescartados' value='".$cadenaTooltipNutDesc."'>
								<input type='hidden' id='tooltipProdNutricionalesDetalles' value='".$cadenaTooltipNutDet."'>
								<input type='hidden' id='tooltipServiciosComplementariosDetalles' value='".$cadenaTooltipSerDet."'>
							</div>";
		
		}
		
		return $htmlMipres;
	}
	
	function consultarEncabezadoPrescripcion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryEncabezado = "  SELECT Prefep,Prehop,Pretim,Preidm,Prepnm,Presnm,Prepam,Presam,Prerps,Pretip,Preidp,Prepnp,Presnp,Prepap,Presap,Precaa,Preteh,Preceh,Precdp,Predrp,Predrs,Prersn,Preeps,Preepr,Prechi,Predii,Pretei,Preidi
								FROM ".$wbasedatoMipres."_000001 
							   WHERE Prenop='".$codPrescMipres."' 
								 AND Preest='on'";
								 
								 
		$resEncabezado = mysql_query($queryEncabezado, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEncabezado . " - " . mysql_error());		   
		$numEncabezado = mysql_num_rows($resEncabezado);
		
		$arrayEncabezado = array();
		if($numEncabezado>0)
		{
			while($rowsEncabezado = mysql_fetch_array($resEncabezado))
			{
				
				$arrayEncabezado['historia'] = $rowsEncabezado['Prehis'];
				$arrayEncabezado['ingreso'] = $rowsEncabezado['Preing'];
				$arrayEncabezado['FPrescripcion'] = $rowsEncabezado['Prefep'];
				$arrayEncabezado['HPrescripcion'] = $rowsEncabezado['Prehop'];
				$arrayEncabezado['TipoIDProf'] = $rowsEncabezado['Pretim'];
				$arrayEncabezado['NumIDProf'] = $rowsEncabezado['Preidm'];
				$arrayEncabezado['PNProfS'] = utf8_encode($rowsEncabezado['Prepnm']);
				$arrayEncabezado['SNProfS'] = utf8_encode($rowsEncabezado['Presnm']);
				$arrayEncabezado['PAProfS'] = utf8_encode($rowsEncabezado['Prepam']);
				$arrayEncabezado['SAProfS'] = utf8_encode($rowsEncabezado['Presam']);
				$arrayEncabezado['RegProfS'] = $rowsEncabezado['Prerps'];
				$arrayEncabezado['TipoIDPaciente'] = $rowsEncabezado['Pretip'];
				$arrayEncabezado['NroIDPaciente'] = $rowsEncabezado['Preidp'];
				$arrayEncabezado['PNPaciente'] = utf8_encode($rowsEncabezado['Prepnp']);
				$arrayEncabezado['SNPaciente'] = utf8_encode($rowsEncabezado['Presnp']);
				$arrayEncabezado['PAPaciente'] = utf8_encode($rowsEncabezado['Prepap']);
				$arrayEncabezado['SAPaciente'] = utf8_encode($rowsEncabezado['Presap']);
				$arrayEncabezado['CodAmbAte'] = $rowsEncabezado['Precaa'];
				$arrayEncabezado['EnfHuerfana'] = $rowsEncabezado['Preteh'];
				$arrayEncabezado['CodEnfHuerfana'] = $rowsEncabezado['Preceh'];
				$arrayEncabezado['CodDxPpal'] = $rowsEncabezado['Precdp'];
				$arrayEncabezado['CodDxRel1'] = $rowsEncabezado['Predrp'];
				$arrayEncabezado['CodDxRel2'] = $rowsEncabezado['Predrs'];
				$arrayEncabezado['SopNutricional'] = $rowsEncabezado['Prersn'];
				$arrayEncabezado['CodEPS'] = $rowsEncabezado['Preeps'];
				$arrayEncabezado['EstPres'] = $rowsEncabezado['Preepr'];
				$arrayEncabezado['CodHabIPS'] = $rowsEncabezado['Prechi'];
				$arrayEncabezado['DirSedeIPS'] = $rowsEncabezado['Predii'];
				$arrayEncabezado['TelSedeIPS'] = $rowsEncabezado['Pretei'];
				$arrayEncabezado['NroIDIPS'] = $rowsEncabezado['Preidi'];
				
				
				
			}
		}	
		
		return $arrayEncabezado;
	}
	
	function consultarMedicamentosPrescripcion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryMedicamentos = "SELECT Medcom,Medtme,Medtpr,Medcs1,Medcs2,Medcs3,Medmpu,Medr31,Medd31,Medr32,Medd32,Medcs4,Medmpd,Medr41,Medd41,Medr42,Medd42,Medr43,Medd43,Medr44,Medd44,Medcs5,Medrc5,Medcs6,Meddmp,Medcff,Medcva,Medjnp,Meddos,Meddum,Mednfa,Medcfa,Medcie,Medctr,Medcdt,Medctf,Medufc,Medirp,Medejm
								FROM ".$wbasedatoMipres."_000002 
							   WHERE Mednop='".$codPrescMipres."' 
								 AND Medest='on';";
								 
								 
		$resMedicamentos = mysql_query($queryMedicamentos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMedicamentos . " - " . mysql_error());		   
		$numMedicamentos = mysql_num_rows($resMedicamentos);
		
		$arrayMedicamentos = array();
		$contadorMedicamentos = 0;
		if($numMedicamentos>0)
		{
			while($rowsMedicamentos = mysql_fetch_array($resMedicamentos))
			{
				
				$arrayMedicamentos[$contadorMedicamentos]['ConOrden'] = utf8_encode($rowsMedicamentos['Medcom']);
				$arrayMedicamentos[$contadorMedicamentos]['TipoMed'] = utf8_encode($rowsMedicamentos['Medtme']);
				$arrayMedicamentos[$contadorMedicamentos]['TipoPrest'] = utf8_encode($rowsMedicamentos['Medtpr']);
				$arrayMedicamentos[$contadorMedicamentos]['CausaS1'] = utf8_encode($rowsMedicamentos['Medcs1']);
				$arrayMedicamentos[$contadorMedicamentos]['CausaS2'] = utf8_encode($rowsMedicamentos['Medcs2']);
				$arrayMedicamentos[$contadorMedicamentos]['CausaS3'] = utf8_encode($rowsMedicamentos['Medcs3']);
				$arrayMedicamentos[$contadorMedicamentos]['MedPBSUtilizado'] = utf8_encode($rowsMedicamentos['Medmpu']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS31'] = utf8_encode($rowsMedicamentos['Medr31']);
				$arrayMedicamentos[$contadorMedicamentos]['DescRzn31'] = utf8_encode($rowsMedicamentos['Medd31']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS32'] = utf8_encode($rowsMedicamentos['Medr32']);
				$arrayMedicamentos[$contadorMedicamentos]['DescRzn32'] = utf8_encode($rowsMedicamentos['Medd32']);
				$arrayMedicamentos[$contadorMedicamentos]['CausaS4'] = utf8_encode($rowsMedicamentos['Medcs4']);
				$arrayMedicamentos[$contadorMedicamentos]['MedPBSDescartado'] = utf8_encode($rowsMedicamentos['Medmpd']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS41'] = utf8_encode($rowsMedicamentos['Medr41']);
				$arrayMedicamentos[$contadorMedicamentos]['DescRzn41'] = utf8_encode($rowsMedicamentos['Medd41']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS42'] = utf8_encode($rowsMedicamentos['Medr42']);
				$arrayMedicamentos[$contadorMedicamentos]['DescRzn42'] = utf8_encode($rowsMedicamentos['Medd42']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS43'] = utf8_encode($rowsMedicamentos['Medr43']);
				$arrayMedicamentos[$contadorMedicamentos]['DescRzn43'] = utf8_encode($rowsMedicamentos['Medd43']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS44'] = utf8_encode($rowsMedicamentos['Medr44']);
				$arrayMedicamentos[$contadorMedicamentos]['DescRzn44'] = utf8_encode($rowsMedicamentos['Medd44']);
				$arrayMedicamentos[$contadorMedicamentos]['CausaS5'] = utf8_encode($rowsMedicamentos['Medcs5']);
				$arrayMedicamentos[$contadorMedicamentos]['RznCausaS5'] = utf8_encode($rowsMedicamentos['Medrc5']);
				$arrayMedicamentos[$contadorMedicamentos]['CausaS6'] = utf8_encode($rowsMedicamentos['Medcs6']);
				$arrayMedicamentos[$contadorMedicamentos]['DescMedPrinAct'] = utf8_encode($rowsMedicamentos['Meddmp']);
				$arrayMedicamentos[$contadorMedicamentos]['CodFF'] = utf8_encode($rowsMedicamentos['Medcff']);
				$arrayMedicamentos[$contadorMedicamentos]['CodVA'] = utf8_encode($rowsMedicamentos['Medcva']);
				$arrayMedicamentos[$contadorMedicamentos]['JustNoPBS'] = utf8_encode($rowsMedicamentos['Medjnp']);
				$arrayMedicamentos[$contadorMedicamentos]['Dosis'] = utf8_encode($rowsMedicamentos['Meddos']);
				$arrayMedicamentos[$contadorMedicamentos]['DosisUM'] = utf8_encode($rowsMedicamentos['Meddum']);
				$arrayMedicamentos[$contadorMedicamentos]['NoFAdmon'] = utf8_encode($rowsMedicamentos['Mednfa']);
				$arrayMedicamentos[$contadorMedicamentos]['CodFreAdmon'] = utf8_encode($rowsMedicamentos['Medcfa']);
				$arrayMedicamentos[$contadorMedicamentos]['IndEsp'] = utf8_encode($rowsMedicamentos['Medcie']);
				$arrayMedicamentos[$contadorMedicamentos]['CanTrat'] = utf8_encode($rowsMedicamentos['Medctr']);
				$arrayMedicamentos[$contadorMedicamentos]['DurTrat'] = utf8_encode($rowsMedicamentos['Medcdt']);
				$arrayMedicamentos[$contadorMedicamentos]['CantTotalF'] = utf8_encode($rowsMedicamentos['Medctf']);
				$arrayMedicamentos[$contadorMedicamentos]['UFCantTotal'] = utf8_encode($rowsMedicamentos['Medufc']);
				$arrayMedicamentos[$contadorMedicamentos]['IndRec'] = utf8_encode($rowsMedicamentos['Medirp']);
				$arrayMedicamentos[$contadorMedicamentos]['EstJM'] = utf8_encode($rowsMedicamentos['Medejm']);
					
				
				$contadorMedicamentos++;
			}
		}	
		
		return $arrayMedicamentos;
		
	}
	
	function consultarPrinciosActivosPrescripcion($codPrescMipres,$consMed)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryPrincipiosActivos = "SELECT Pamcop,Pamcpa,Pamcca,Pamumc,Pamcco,Pamumm 
									 FROM ".$wbasedatoMipres."_000003
									WHERE Pamnop='".$codPrescMipres."'
									  AND Pamcop='".$consMed."'
									  AND Pamest='on';";
								 
								 
		$resPrincipiosActivos = mysql_query($queryPrincipiosActivos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPrincipiosActivos . " - " . mysql_error());		   
		$numPrincipiosActivos = mysql_num_rows($resPrincipiosActivos);
		
		$arrayPrincipiosActivos = array();
		$contadorPrincipiosActivos = 0;
		if($numPrincipiosActivos>0)
		{
			while($rowPrincipiosActivos = mysql_fetch_array($resPrincipiosActivos))
			{
				
				$arrayPrincipiosActivos[$contadorPrincipiosActivos]['ConOrden'] = utf8_encode($rowPrincipiosActivos['Pamcop']);
				$arrayPrincipiosActivos[$contadorPrincipiosActivos]['CodPriAct'] = utf8_encode($rowPrincipiosActivos['Pamcpa']);
				$arrayPrincipiosActivos[$contadorPrincipiosActivos]['ConcCant'] = utf8_encode($rowPrincipiosActivos['Pamcca']);
				$arrayPrincipiosActivos[$contadorPrincipiosActivos]['UMedConc'] = utf8_encode($rowPrincipiosActivos['Pamumc']);
				$arrayPrincipiosActivos[$contadorPrincipiosActivos]['CantCont'] = utf8_encode($rowPrincipiosActivos['Pamcco']);
				$arrayPrincipiosActivos[$contadorPrincipiosActivos]['UMedCantCont'] = utf8_encode($rowPrincipiosActivos['Pamumm']);
				
				$contadorPrincipiosActivos++;
			}
		}	
		
		return $arrayPrincipiosActivos;
	}
	
	function consultarProcedimientosPrescripcion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryProcedimientos = "  SELECT Procop,Protpr,Proc11,Proc12,Procs2,Procs3,Procs4,Proppu,Procs5,Proppd,Proc51,Prod51,Proc52,Prod52,Procs6,Procs7,Procup,Procfo,Procfu,Procan,Procdt,Projnp,Proirp,Proejm,Procaf,Procat
									FROM ".$wbasedatoMipres."_000004 
								   WHERE Pronop='".$codPrescMipres."'
									 AND Proest='on';";
								 
								 
		$resProcedimientos = mysql_query($queryProcedimientos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryProcedimientos . " - " . mysql_error());		   
		$numProcedimientos = mysql_num_rows($resProcedimientos);
		
		$arrayProcedimientos = array();
		$contadorProcedimientos = 0;
		if($numProcedimientos>0)
		{
			while($rowProcedimientos = mysql_fetch_array($resProcedimientos))
			{
				if($rowProcedimientos['Procaf']==0)
				{
					$rowProcedimientos['Procaf'] = "";
				}
				
				if($rowProcedimientos['Procat']==0)
				{
					$rowProcedimientos['Procat'] = "";
				}
				
				$arrayProcedimientos[$contadorProcedimientos]['ConOrden'] = utf8_encode($rowProcedimientos['Procop']);
				$arrayProcedimientos[$contadorProcedimientos]['TipoPrest'] = utf8_encode($rowProcedimientos['Protpr']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS11'] = utf8_encode($rowProcedimientos['Proc11']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS12'] = utf8_encode($rowProcedimientos['Proc12']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS2'] = utf8_encode($rowProcedimientos['Procs2']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS3'] = utf8_encode($rowProcedimientos['Procs3']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS4'] = utf8_encode($rowProcedimientos['Procs4']);
				$arrayProcedimientos[$contadorProcedimientos]['ProPBSUtilizado'] = utf8_encode($rowProcedimientos['Proppu']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS5'] = utf8_encode($rowProcedimientos['Procs5']);
				$arrayProcedimientos[$contadorProcedimientos]['ProPBSDescartado'] = utf8_encode($rowProcedimientos['Proppd']);
				$arrayProcedimientos[$contadorProcedimientos]['RznCausaS51'] = utf8_encode($rowProcedimientos['Proc51']);
				$arrayProcedimientos[$contadorProcedimientos]['DescRzn51'] = utf8_encode($rowProcedimientos['Prod51']);
				$arrayProcedimientos[$contadorProcedimientos]['RznCausaS52'] = utf8_encode($rowProcedimientos['Proc52']);
				$arrayProcedimientos[$contadorProcedimientos]['DescRzn52'] = utf8_encode($rowProcedimientos['Prod52']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS6'] = utf8_encode($rowProcedimientos['Procs6']);
				$arrayProcedimientos[$contadorProcedimientos]['CausaS7'] = utf8_encode($rowProcedimientos['Procs7']);
				$arrayProcedimientos[$contadorProcedimientos]['CodCUPS'] = utf8_encode($rowProcedimientos['Procup']);
				$arrayProcedimientos[$contadorProcedimientos]['CanForm'] = utf8_encode($rowProcedimientos['Procfo']);
				$arrayProcedimientos[$contadorProcedimientos]['CadaFreUso'] = utf8_encode($rowProcedimientos['Procaf']);
				$arrayProcedimientos[$contadorProcedimientos]['CodFreUso'] = utf8_encode($rowProcedimientos['Procfu']);
				$arrayProcedimientos[$contadorProcedimientos]['Cant'] = utf8_encode($rowProcedimientos['Procan']);
				$arrayProcedimientos[$contadorProcedimientos]['CantTotal'] = utf8_encode($rowProcedimientos['Procat']);
				$arrayProcedimientos[$contadorProcedimientos]['CodPerDurTrat'] = utf8_encode($rowProcedimientos['Procdt']);
				$arrayProcedimientos[$contadorProcedimientos]['JustNoPBS'] = utf8_encode($rowProcedimientos['Projnp']);
				$arrayProcedimientos[$contadorProcedimientos]['IndRec'] = utf8_encode($rowProcedimientos['Proirp']);
				$arrayProcedimientos[$contadorProcedimientos]['EstJM'] = utf8_encode($rowProcedimientos['Proejm']);
				
				$contadorProcedimientos++;
			}
		}
		
		return $arrayProcedimientos;
	}
	
	function consultarDispositivosPrescripcion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryDispositivos = "SELECT Discod,Distpr,Discs1,Discdi,Discfo,Discfu,Discan,Discdt,Disjnp,Disirp,Disejm,Discaf,Discat 
								FROM ".$wbasedatoMipres."_000005
							   WHERE Disnop='".$codPrescMipres."'
								 AND Disest='on';";
								 
								 
		$resDispositivos = mysql_query($queryDispositivos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDispositivos . " - " . mysql_error());		   
		$numDispositivos = mysql_num_rows($resDispositivos);
		
		$arrayDispositivos= array();
		$contadorDispositivos = 0;
		if($numDispositivos>0)
		{
			while($rowDispositivos = mysql_fetch_array($resDispositivos))
			{
				if($rowDispositivos['Procaf']==0)
				{
					$rowDispositivos['Procaf'] = "";
				}
				
				if($rowDispositivos['Procat']==0)
				{
					$rowDispositivos['Procat'] = "";
				}
				
				$arrayDispositivos[$contadorDispositivos]['ConOrden'] = utf8_encode($rowDispositivos['Discod']);
				$arrayDispositivos[$contadorDispositivos]['TipoPrest'] = utf8_encode($rowDispositivos['Distpr']);
				$arrayDispositivos[$contadorDispositivos]['CausaS1'] = utf8_encode($rowDispositivos['Discs1']);
				$arrayDispositivos[$contadorDispositivos]['CodDisp'] = utf8_encode($rowDispositivos['Discdi']);
				$arrayDispositivos[$contadorDispositivos]['CanForm'] = utf8_encode($rowDispositivos['Discfo']);
				$arrayDispositivos[$contadorDispositivos]['CadaFreUso'] = utf8_encode($rowDispositivos['Discaf']);
				$arrayDispositivos[$contadorDispositivos]['CodFreUso'] = utf8_encode($rowDispositivos['Discfu']);
				$arrayDispositivos[$contadorDispositivos]['Cant'] = utf8_encode($rowDispositivos['Discan']);
				$arrayDispositivos[$contadorDispositivos]['CodPerDurTrat'] = utf8_encode($rowDispositivos['Discdt']);
				$arrayDispositivos[$contadorDispositivos]['CantTotal'] = utf8_encode($rowDispositivos['Discat']);
				$arrayDispositivos[$contadorDispositivos]['JustNoPBS'] = utf8_encode($rowDispositivos['Disjnp']);
				$arrayDispositivos[$contadorDispositivos]['IndRec'] = utf8_encode($rowDispositivos['Disirp']);
				$arrayDispositivos[$contadorDispositivos]['EstJM'] = utf8_encode($rowDispositivos['Disejm']);
				$contadorDispositivos++;
			}
		}
		
		return $arrayDispositivos;
	}
	
	function consultarProductosNutricionalesPrescripcion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryNutriciones = "SELECT Nutcon,Nuttpr,Nutcs1,Nutcs2,Nutcs3,Nutcs4,Nutpnu,Nutc41,Nutd41,Nutc42,Nutd42,Nutcs5,Nutpnd,Nutc51,Nutd51,Nutc52,Nutd52,Nutc53,Nutd53,Nutc54,Nutd54,Nuttpn,Nutdpn,Nutcfo,Nutcva,Nutjnp,Nutdos,Nutdum,Nutnfa,Nutcfa,Nutcat,Nutdut,Nutctf,Nutuft,Nutirp,Nutnpa,Nutejm,Nuthue,Nutvih,Nutccp,Nuterc,Nuties
							   FROM ".$wbasedatoMipres."_000006 
							  WHERE Nutnop='".$codPrescMipres."'
								AND Nutest='on';";
								 
								 
		$resNutriciones = mysql_query($queryNutriciones, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNutriciones . " - " . mysql_error());		   
		$numNutriciones = mysql_num_rows($resNutriciones);
		
		$arrayNutriciones = array();
		$contadorNutriciones = 0;
		if($numNutriciones>0)
		{
			while($rowsNutriciones = mysql_fetch_array($resNutriciones))
			{
				$arrayNutriciones[$contadorNutriciones]['ConOrden'] = utf8_encode($rowsNutriciones['Nutcon']);
				$arrayNutriciones[$contadorNutriciones]['TipoPrest'] = utf8_encode($rowsNutriciones['Nuttpr']);
				$arrayNutriciones[$contadorNutriciones]['CausaS1'] = utf8_encode($rowsNutriciones['Nutcs1']);
				$arrayNutriciones[$contadorNutriciones]['CausaS2'] = utf8_encode($rowsNutriciones['Nutcs2']);
				$arrayNutriciones[$contadorNutriciones]['CausaS3'] = utf8_encode($rowsNutriciones['Nutcs3']);
				$arrayNutriciones[$contadorNutriciones]['CausaS4'] = utf8_encode($rowsNutriciones['Nutcs4']);
				$arrayNutriciones[$contadorNutriciones]['ProNutUtilizado'] = utf8_encode($rowsNutriciones['Nutpnu']);
				$arrayNutriciones[$contadorNutriciones]['RznCausaS41'] = utf8_encode($rowsNutriciones['Nutc41']);
				$arrayNutriciones[$contadorNutriciones]['DescRzn41'] = utf8_encode($rowsNutriciones['Nutd41']);
				$arrayNutriciones[$contadorNutriciones]['RznCausaS42'] = utf8_encode($rowsNutriciones['Nutc42']);
				$arrayNutriciones[$contadorNutriciones]['DescRzn42'] = utf8_encode($rowsNutriciones['Nutd42']);
				$arrayNutriciones[$contadorNutriciones]['CausaS5'] = utf8_encode($rowsNutriciones['Nutcs5']);
				$arrayNutriciones[$contadorNutriciones]['ProNutDescartado'] = utf8_encode($rowsNutriciones['Nutpnd']);
				$arrayNutriciones[$contadorNutriciones]['RznCausaS51'] = utf8_encode($rowsNutriciones['Nutc51']);
				$arrayNutriciones[$contadorNutriciones]['DescRzn51'] = utf8_encode($rowsNutriciones['Nutd51']);
				$arrayNutriciones[$contadorNutriciones]['RznCausaS52'] = utf8_encode($rowsNutriciones['Nutc52']);
				$arrayNutriciones[$contadorNutriciones]['DescRzn52'] = utf8_encode($rowsNutriciones['Nutd52']);
				$arrayNutriciones[$contadorNutriciones]['RznCausaS53'] = utf8_encode($rowsNutriciones['Nutc53']);
				$arrayNutriciones[$contadorNutriciones]['DescRzn53'] = utf8_encode($rowsNutriciones['Nutd53']);
				$arrayNutriciones[$contadorNutriciones]['RznCausaS54'] = utf8_encode($rowsNutriciones['Nutc54']);
				$arrayNutriciones[$contadorNutriciones]['DescRzn54'] = utf8_encode($rowsNutriciones['Nutd54']);
				$arrayNutriciones[$contadorNutriciones]['DXEnfHuer'] = utf8_encode($rowsNutriciones['Nuthue']);
				$arrayNutriciones[$contadorNutriciones]['DXVIH'] = utf8_encode($rowsNutriciones['Nutvih']);
				$arrayNutriciones[$contadorNutriciones]['DXCaPal'] = utf8_encode($rowsNutriciones['Nutccp']);
				$arrayNutriciones[$contadorNutriciones]['DXEnfRCEV'] = utf8_encode($rowsNutriciones['Nuterc']);
				$arrayNutriciones[$contadorNutriciones]['TippProNut'] = utf8_encode($rowsNutriciones['Nuttpn']);
				$arrayNutriciones[$contadorNutriciones]['DescProdNutr'] = utf8_encode($rowsNutriciones['Nutdpn']);
				$arrayNutriciones[$contadorNutriciones]['CodForma'] = utf8_encode($rowsNutriciones['Nutcfo']);
				$arrayNutriciones[$contadorNutriciones]['CodViaAdmon'] = utf8_encode($rowsNutriciones['Nutcva']);
				$arrayNutriciones[$contadorNutriciones]['JustNoPBS'] = utf8_encode($rowsNutriciones['Nutjnp']);
				$arrayNutriciones[$contadorNutriciones]['Dosis'] = utf8_encode($rowsNutriciones['Nutdos']);
				$arrayNutriciones[$contadorNutriciones]['DosisUM'] = utf8_encode($rowsNutriciones['Nutdum']);
				$arrayNutriciones[$contadorNutriciones]['NoFAdmon'] = utf8_encode($rowsNutriciones['Nutnfa']);
				$arrayNutriciones[$contadorNutriciones]['CodFreAdmon'] = utf8_encode($rowsNutriciones['Nutcfa']);
				$arrayNutriciones[$contadorNutriciones]['IndEsp'] = utf8_encode($rowsNutriciones['Nuties']);
				$arrayNutriciones[$contadorNutriciones]['CanTrat'] = utf8_encode($rowsNutriciones['Nutcat']);
				$arrayNutriciones[$contadorNutriciones]['DurTrat'] = utf8_encode($rowsNutriciones['Nutdut']);
				$arrayNutriciones[$contadorNutriciones]['CantTotalF'] = utf8_encode($rowsNutriciones['Nutctf']);
				$arrayNutriciones[$contadorNutriciones]['UFCantTotal'] = utf8_encode($rowsNutriciones['Nutuft']);
				$arrayNutriciones[$contadorNutriciones]['IndRec'] = utf8_encode($rowsNutriciones['Nutirp']);
				$arrayNutriciones[$contadorNutriciones]['NoPrescAso'] = utf8_encode($rowsNutriciones['Nutnpa']);
				$arrayNutriciones[$contadorNutriciones]['EstJM'] = utf8_encode($rowsNutriciones['Nutejm']);
				
				$contadorNutriciones++;
			}
		}
		
		return $arrayNutriciones;
	}
	
	function consultarServiciosComplementariosPrescripcion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryServicios = "SELECT Sercos,Sertpr,Sercs1,Sercs2,Sercs3,Sercs4,Serdc4,Sercs5,Sercsc,Serdsc,Sercaf,Sercfu,Sercan,Sercdt,Serjnp,Serirp,Serejm,Serfru,Sercat
							 FROM ".$wbasedatoMipres."_000007
							WHERE Sernop='".$codPrescMipres."'
							  AND Serest='on';";
								 
								 
		$resServicios = mysql_query($queryServicios, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryServicios . " - " . mysql_error());		   
		$numServicios = mysql_num_rows($resServicios);
		
		$arrayServicios = array();
		$contadorServicios = 0;
		if($numServicios>0)
		{
			while($rowServicios = mysql_fetch_array($resServicios))
			{
				if($rowServicios['Procaf']==0)
				{
					$rowServicios['Procaf'] = "";
				}
				
				if($rowServicios['Procat']==0)
				{
					$rowServicios['Procat'] = "";
				}
				
				$arrayServicios[$contadorServicios]['ConOrden'] = utf8_encode($rowServicios['Sercos']);
				$arrayServicios[$contadorServicios]['TipoPrest'] = utf8_encode($rowServicios['Sertpr']);
				$arrayServicios[$contadorServicios]['CausaS1'] = utf8_encode($rowServicios['Sercs1']);
				$arrayServicios[$contadorServicios]['CausaS2'] = utf8_encode($rowServicios['Sercs2']);
				$arrayServicios[$contadorServicios]['CausaS3'] = utf8_encode($rowServicios['Sercs3']);
				$arrayServicios[$contadorServicios]['CausaS4'] = utf8_encode($rowServicios['Sercs4']);
				$arrayServicios[$contadorServicios]['DescCausaS4'] = utf8_encode($rowServicios['Serdc4']);
				$arrayServicios[$contadorServicios]['CausaS5'] = utf8_encode($rowServicios['Sercs5']);
				$arrayServicios[$contadorServicios]['CodSerComp'] = utf8_encode($rowServicios['Sercsc']);
				$arrayServicios[$contadorServicios]['DescSerComp'] = utf8_encode($rowServicios['Serdsc']);
				$arrayServicios[$contadorServicios]['CanForm'] = utf8_encode($rowServicios['Sercaf']);
				$arrayServicios[$contadorServicios]['CadaFreUso'] = utf8_encode($rowServicios['Serfru']);
				$arrayServicios[$contadorServicios]['CodFreUso'] = utf8_encode($rowServicios['Sercfu']);
				$arrayServicios[$contadorServicios]['Cant'] = utf8_encode($rowServicios['Sercan']);
				$arrayServicios[$contadorServicios]['CantTotal'] = utf8_encode($rowServicios['Sercat']);
				$arrayServicios[$contadorServicios]['CodPerDurTrat'] = utf8_encode($rowServicios['Sercdt']);
				$arrayServicios[$contadorServicios]['JustNoPBS'] = utf8_encode($rowServicios['Serjnp']);
				$arrayServicios[$contadorServicios]['IndRec'] = utf8_encode($rowServicios['Serirp']);
				$arrayServicios[$contadorServicios]['EstJM'] = utf8_encode($rowServicios['Serejm']);
				$contadorServicios++;
			}
		}
		
		return $arrayServicios;
	}
	
	function consultarDetallesAnulacion($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryAnulacion = " SELECT Anutip,Anujus,Anuobs,Anufso,Anuuso,Anuean,Anufan,Anuuan 
							  FROM ".$wbasedatoMipres."_000009 
							 WHERE Anunpr='".$codPrescMipres."' 
							   AND Anuest='on';";
								 
								 
		$resAnulacion = mysql_query($queryAnulacion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAnulacion . " - " . mysql_error());		   
		$numAnulacion = mysql_num_rows($resAnulacion);
		
		$detallesAnulacion = array();
		if($numAnulacion>0)
		{
			while($rowsAnulacion = mysql_fetch_array($resAnulacion))
			{
				$detallesAnulacion['TipoAnulacion'] = $rowsAnulacion['Anutip'];
				$detallesAnulacion['Justificacion'] = $rowsAnulacion['Anujus'];
				$detallesAnulacion['Observacion'] = $rowsAnulacion['Anuobs'];
				$detallesAnulacion['FSolicitud'] = $rowsAnulacion['Anufso'];
				$detallesAnulacion['Usuario_Solicita'] = $rowsAnulacion['Anuuso'];
				$detallesAnulacion['EstAnulacion'] = $rowsAnulacion['Anuean'];
				$detallesAnulacion['FAnulacion'] = $rowsAnulacion['Anufan'];
				$detallesAnulacion['Usuario_Anula'] = $rowsAnulacion['Anuuan'];
			}
		}	
		
		return $detallesAnulacion;
	}
	
	function consultarDetallesJuntaProfesional($codPrescMipres)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryDetalleJM = "   SELECT Jupobs,Jupnac,Jupfpc 
								FROM ".$wbasedatoMipres."_000010 
							   WHERE Jupnpr='".$codPrescMipres."' 
							     AND Jupest='on';";
							 
		$resDetalleJM = mysql_query($queryDetalleJM, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDetalleJM . " - " . mysql_error());		   
		$numDetalleJM = mysql_num_rows($resDetalleJM);
		
		$detalleJM = array();
		if($numDetalleJM>0)
		{
			$rowsDetalleJM = mysql_fetch_array($resDetalleJM);
			
			$detalleJM['Observaciones'] = utf8_encode($rowsDetalleJM['Jupobs']);
			$detalleJM['NoActa'] = $rowsDetalleJM['Jupnac'];
			$detalleJM['FProceso'] = $rowsDetalleJM['Jupfpc'];
		}						 
		
		return $detalleJM;
	}
	
	function consultarTiposDocumentoMipres()
	{
		global $conex;
		
		$queryTiposDocumentos = " SELECT Tdicod,Tdides 
									FROM mipres_000011;";
							 
		$resTiposDocumentos = mysql_query($queryTiposDocumentos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTiposDocumentos . " - " . mysql_error());		   
		$numTiposDocumentos = mysql_num_rows($resTiposDocumentos);
		
		$detalleTiposDocumentos = array();
		if($numTiposDocumentos>0)
		{
			while($rowsTiposDocumentos = mysql_fetch_array($resTiposDocumentos))
			{
				$detalleTiposDocumentos[$rowsTiposDocumentos['Tdicod']] = $rowsTiposDocumentos['Tdides'];
			}
			
		}						 
		
		return $detalleTiposDocumentos;
	}
	
	function consultarDiagnostico($codDiagnostico)
	{
		global $conex;
		
		$queryDiagnostico = "  SELECT Ciedes 
								 FROM mipres_000012 
							    WHERE Ciecod='".$codDiagnostico."';";
								 
								 
		$resDiagnostico = mysql_query($queryDiagnostico, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDiagnostico . " - " . mysql_error());		   
		$numDiagnostico = mysql_num_rows($resDiagnostico);
		
		$diagnostico = $codDiagnostico;
		if($numDiagnostico>0)
		{
			$rowsDiagnostico = mysql_fetch_array($resDiagnostico);
			
			$diagnostico = utf8_encode($rowsDiagnostico['Ciedes']);
		}	
		
		return $diagnostico;
	}
	
	function consultarEnfermedadHuerfana($enfHuerfana,$codEnfermedadHuerfana)
	{
		global $conex;
		
		
		if($codEnfermedadHuerfana!="")
		{
			$queryEnfermedadHuerfana = "  SELECT Ehudes 
											FROM mipres_000013 
										   WHERE Ehucod='".$codEnfermedadHuerfana."';";
									 
									 
			$resEnfermedadHuerfana = mysql_query($queryEnfermedadHuerfana, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEnfermedadHuerfana . " - " . mysql_error());		   
			$numEnfermedadHuerfana = mysql_num_rows($resEnfermedadHuerfana);
			
			$enfermedadHuerfana = $codEnfermedadHuerfana;
			if($numEnfermedadHuerfana>0)
			{
				$rowsEnfermedadHuerfana = mysql_fetch_array($resEnfermedadHuerfana);
				
				$enfermedadHuerfana = utf8_encode($rowsEnfermedadHuerfana['Ehudes']);
			}
		}
		else
		{
			$enfermedadHuerfana = consultarCampoBooleano($enfHuerfana);
		}
			
		
		return $enfermedadHuerfana;
	}
	
	function consultarAmbitoAtencion($codAmbitoAtencion)
	{
		global $conex;
		
		if($codAmbitoAtencion=="")
		{
			$queryAmbitoAtencion = "  SELECT Amacod,Amades 
										FROM mipres_000014;";
									 
									 
			$resAmbitoAtencion = mysql_query($queryAmbitoAtencion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAmbitoAtencion . " - " . mysql_error());		   
			$numAmbitoAtencion = mysql_num_rows($resAmbitoAtencion);
			
			$arrayAmbitosAtencion = array();
			if($numAmbitoAtencion>0)
			{
				while($rowsAmbitoAtencion = mysql_fetch_array($resAmbitoAtencion))
				{
					$arrayAmbitosAtencion[$rowsAmbitoAtencion['Amacod']] = $rowsAmbitoAtencion['Amades'];
				}
				
			}	
			
			return $arrayAmbitosAtencion;
		}
		else
		{
			$queryAmbitoAtencion = "  SELECT Amades 
										FROM mipres_000014 
									   WHERE Amacod='".$codAmbitoAtencion."';";
									 
									 
			$resAmbitoAtencion = mysql_query($queryAmbitoAtencion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryAmbitoAtencion . " - " . mysql_error());		   
			$numAmbitoAtencion = mysql_num_rows($resAmbitoAtencion);
			
			$ambitoAtencion = $codAmbitoAtencion;
			if($numAmbitoAtencion>0)
			{
				$rowsAmbitoAtencion = mysql_fetch_array($resAmbitoAtencion);
				
				$ambitoAtencion = utf8_encode($rowsAmbitoAtencion['Amades']);
			}	
			
			return $ambitoAtencion;
		}
		
		
	}
	
	function consultarVia($codVia)
	{
		global $conex;
		
		$queryVia = " SELECT Viades 
						FROM mipres_000016 
					   WHERE Viacod='".$codVia."';";
								 
								 
		$resVia = mysql_query($queryVia, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryVia . " - " . mysql_error());		   
		$numVia = mysql_num_rows($resVia);
		
		$via = $codVia;
		if($numVia>0)
		{
			$rowVia = mysql_fetch_array($resVia);
			
			$via = utf8_encode($rowVia['Viades']);
		}	
		
		return $via;
	}
	
	function consultarUnidadMedida($codUnidadMedida)
	{
		global $conex;
		
		$queryUnidadMedida = " SELECT Unides,Unided 
								 FROM mipres_000017 
								WHERE Unicod='".$codUnidadMedida."';";
								 
								 
		$resUnidadMedida = mysql_query($queryUnidadMedida, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryUnidadMedida . " - " . mysql_error());		   
		$numUnidadMedida = mysql_num_rows($resUnidadMedida);
		
		$unidadMedida = $codUnidadMedida;
		if($numUnidadMedida>0)
		{
			$rowUnidadMedida = mysql_fetch_array($resUnidadMedida);
			
			$unidadMedida = utf8_encode($rowUnidadMedida['Unided']);
		}	
		
		return $unidadMedida;
	}
	
	function consultarPresentacion($codPresentacion)
	{
		global $conex;
		
		$queryPresentacion = " SELECT Predes 
								 FROM mipres_000018 
								WHERE Precod='".$codPresentacion."';";
								 
								 
		$resPresentacion = mysql_query($queryPresentacion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPresentacion . " - " . mysql_error());		   
		$numPresentacion = mysql_num_rows($resPresentacion);
		
		$presentacion = $codPresentacion;
		if($numPresentacion>0)
		{
			$rowPresentacion = mysql_fetch_array($resPresentacion);
			
			$presentacion = utf8_encode($rowPresentacion['Predes']);
		}	
		
		return $presentacion;
	}
	
	function consultarFormaFarmaceutica($codFormaFarmaceutica)
	{
		global $conex;
		
		$queryFormaFarmaceutica = "SELECT Fofdes 
									 FROM mipres_000019 
								    WHERE Fofcod='".$codFormaFarmaceutica."';";
								 
								 
		$resFormaFarmaceutica = mysql_query($queryFormaFarmaceutica, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryFormaFarmaceutica . " - " . mysql_error());		   
		$numFormaFarmaceutica = mysql_num_rows($resFormaFarmaceutica);
		
		$formaFarmaceutica = $codFormaFarmaceutica;
		if($numFormaFarmaceutica>0)
		{
			$rowsFormaFarmaceutica = mysql_fetch_array($resFormaFarmaceutica);
			
			$formaFarmaceutica = utf8_encode($rowsFormaFarmaceutica['Fofdes']);
		}	
		
		return $formaFarmaceutica;
	}
	
	function consultarPrincipioActivo($codPA)
	{
		global $conex;
	
		$queryPA = "  SELECT Dcides 
						FROM mipres_000020 
					   WHERE Dcicod='".$codPA."';";
								 
		$resPA = mysql_query($queryPA, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPA . " - " . mysql_error());		   
		$numPA = mysql_num_rows($resPA);
		
		$principioActivo = $codPA;
		if($numPA>0)
		{
			$rowPA = mysql_fetch_array($resPA);
			
			$principioActivo = utf8_encode($rowPA['Dcides']);
		}	
		
		return $principioActivo;
	}
	
	function consultarFrecuencia($codFrecuencia)
	{
		global $conex;
		
		$queryFrecuencia = " SELECT Fredes 
								 FROM mipres_000022 
								WHERE Frecod='".$codFrecuencia."';";
								 
								 
		$resFrecuencia = mysql_query($queryFrecuencia, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryFrecuencia . " - " . mysql_error());		   
		$numFrecuencia = mysql_num_rows($resFrecuencia);
		
		$frecuencia = $codFrecuencia;
		if($numFrecuencia>0)
		{
			$rowFrecuencia = mysql_fetch_array($resFrecuencia);
			
			$frecuencia = utf8_encode($rowFrecuencia['Fredes']);
		}	
		
		return $frecuencia;
	}
	
	function consultarIndicacionesEspeciales($codIndicacion)
	{
		global $conex;
		
		$queryIndicacion = " SELECT Inddes 
							   FROM mipres_000023 
							  WHERE Indcod='".$codIndicacion."';";
								 
								 
		$resIndicacion = mysql_query($queryIndicacion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryIndicacion . " - " . mysql_error());		   
		$numIndicacion = mysql_num_rows($resIndicacion);
		
		$indicacion = $codIndicacion;
		if($numIndicacion>0)
		{
			$rowIndicacion = mysql_fetch_array($resIndicacion);
			
			$indicacion = utf8_encode($rowIndicacion['Inddes']);
		}	
		
		return $indicacion;
	}
	
	function consultarProcedimientoCups($codCups)
	{
		global $conex;
		
		$queryCups = " SELECT Cupdes 
						 FROM mipres_000025 
						WHERE Cupcod='".$codCups."';";
								 
								 
		$resCups = mysql_query($queryCups, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryCups . " - " . mysql_error());		   
		$numCups = mysql_num_rows($resCups);
		
		$procedimiento = $codCups;
		if($numCups>0)
		{
			$rowCups = mysql_fetch_array($resCups);
			$procedimiento = utf8_encode($rowCups['Cupdes']);
		}	
		
		return $procedimiento;
	}
	
	function consultarDispositivo($codDispositivo)
	{
		global $conex;
		
		$queryDispositivos = " SELECT Tdmdes 
								 FROM mipres_000026 
								WHERE Tdmcod='".$codDispositivo."';";
								 
								 
		$resDispositivos = mysql_query($queryDispositivos, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryDispositivos . " - " . mysql_error());		   
		$numDispositivos = mysql_num_rows($resDispositivos);
		
		$dispositivo = $codDispositivo;
		if($numDispositivos>0)
		{
			$rowDispositivos = mysql_fetch_array($resDispositivos);
			$dispositivo = utf8_encode($rowDispositivos['Tdmdes']);
		}	
		
		return $dispositivo;
	}
	
	function consultarServicioComplementario($codServicio)
	{
		global $conex;
		
		$queryServicios = " SELECT Tscdes 
							  FROM mipres_000027 
							 WHERE Tsccod='".$codServicio."';";
								 
								 
		$resServicios = mysql_query($queryServicios, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryServicios . " - " . mysql_error());		   
		$numServicios = mysql_num_rows($resServicios);
		
		$servicio = $codServicio;
		if($numServicios>0)
		{
			$rowServicios = mysql_fetch_array($resServicios);
			$servicio = utf8_encode($rowServicios['Tscdes']);
		}	
		
		return $servicio;
	}
	
	function consultarTipoProductoNutricional($codTipProdNut)
	{
		global $conex;
		
		$queryTipProdNut = "  SELECT Tpndes 
								FROM mipres_000029 
							   WHERE Tpncod='".$codTipProdNut."';";
								 
		$resTipProdNut = mysql_query($queryTipProdNut, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryTipProdNut . " - " . mysql_error());		   
		$numTipProdNut = mysql_num_rows($resTipProdNut);
		
		$tipProdNut = $codTipProdNut;
		if($numTipProdNut>0)
		{
			$rowTipProdNut = mysql_fetch_array($resTipProdNut);
			
			$tipProdNut = utf8_encode($rowTipProdNut['Tpndes']);
		}	
		
		return $tipProdNut;
	}
	
	function consultarProductoNutricional($codProdNut)
	{
		global $conex;
		
		$queryProdNut = "  SELECT Lpnnom 
							FROM mipres_000030 
						   WHERE Lpncod='".$codProdNut."';";
								 
		$resProdNut = mysql_query($queryProdNut, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryProdNut . " - " . mysql_error());		   
		$numProdNut = mysql_num_rows($resProdNut);
		
		$prodNut = $codProdNut;
		if($numProdNut>0)
		{
			$rowProdNut = mysql_fetch_array($resProdNut);
			
			$prodNut = utf8_encode($rowProdNut['Lpnnom']);
		}	
		
		return $prodNut;
	}
	
	function consultarViaProductoNutricional($codViaProdNut)
	{
		global $conex;
		
		$queryViaProdNut = "  SELECT Vpndes 
								FROM mipres_000031 
							   WHERE Vpncod='".$codViaProdNut."';";
								 
		$resViaProdNut = mysql_query($queryViaProdNut, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryViaProdNut . " - " . mysql_error());		   
		$numViaProdNut = mysql_num_rows($resViaProdNut);
		
		$viaProdNut = $codViaProdNut;
		if($numViaProdNut>0)
		{
			$rowViaProdNut = mysql_fetch_array($resViaProdNut);
			
			$viaProdNut = utf8_encode($rowViaProdNut['Vpndes']);
		}	
		
		return $viaProdNut;
	}
	
	function consultarFormaProductoNutricional($codFormaProdNut)
	{
		global $conex;
		
		$queryFormaProdNut = "SELECT Fpndes 
								FROM mipres_000032 
							   WHERE Fpncod='".$codFormaProdNut."';";
								 
		$resFormaProdNut = mysql_query($queryFormaProdNut, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryFormaProdNut . " - " . mysql_error());		   
		$numFormaProdNut = mysql_num_rows($resFormaProdNut);
		
		$formaProdNut = $codFormaProdNut;
		if($numFormaProdNut>0)
		{
			$rowFormaProdNut = mysql_fetch_array($resFormaProdNut);
			
			$formaProdNut = utf8_encode($rowFormaProdNut['Fpndes']);
		}	
		
		return $formaProdNut;
	}
	
	function consultarEps($codEps)
	{
		global $conex;
		
		$queryEps = " SELECT Epsdes 
						FROM mipres_000033 
					   WHERE Epscod='".$codEps."';";
								 
								 
		$resEps = mysql_query($queryEps, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEps . " - " . mysql_error());		   
		$numEps = mysql_num_rows($resEps);
		
		$eps = $codEps;
		if($numEps>0)
		{
			$rowsEps = mysql_fetch_array($resEps);
			
			$eps = utf8_encode($rowsEps['Epsdes']);
		}	
		
		return $eps;
	}
	
	function consultarListaEps()
	{
		global $conex;
		
		$queryEps = " SELECT Epscod,Epsdes 
						FROM mipres_000033;";
								 
								 
		$resEps = mysql_query($queryEps, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryEps . " - " . mysql_error());		   
		$numEps = mysql_num_rows($resEps);
		
		$eps = array();
		if($numEps>0)
		{
			while($rowsEps = mysql_fetch_array($resEps))
			{
				$eps[$rowsEps['Epscod']] = utf8_encode($rowsEps['Epsdes']);
			}
			
		}	
		
		return $eps;
	}
	
	function consultarCampoBooleano($campo)
	{
		$respuesta = "-";
		if($campo=="0")
		{
			$respuesta = "No";
		}
		elseif($campo=="1")
		{
			$respuesta = "Si";
		}
		
		return $respuesta;
	}
	
	function consultarEstadoPrescripcion($estPresc)
	{
		// 1: Modificado 2: Anulado 4: Activo
		$estadoPrescripcion = $estPresc;
		if($estPresc=="1")
		{
			$estadoPrescripcion = "Modificado";
		}
		elseif($estPresc=="2")
		{
			$estadoPrescripcion = "Anulado";
		}
		elseif($estPresc=="4")
		{
			$estadoPrescripcion = "Activo";
		}
		
		return $estadoPrescripcion;
	}
	
	function consultarTipoMedicamento($tipoMed)
	{
		// 1: Medicamento 2: Vital No Disponible 3: Preparación Magistral 7: UNIRS 9: Urgencia Médica (Solo Transcripción)
		$tipoMedicamento = $tipoMed;
		if($tipoMed=="1")
		{
			$tipoMedicamento = "Medicamento";
		}
		elseif($tipoMed=="2")
		{
			$tipoMedicamento = "Vital No Disponible";
		}
		elseif($tipoMed=="3")
		{
			$tipoMedicamento = "Preparación Magistral";
		}
		elseif($tipoMed=="7")
		{
			$tipoMedicamento = "UNIRS";
		}
		elseif($tipoMed=="9")
		{
			$tipoMedicamento = "Urgencia Médica (Solo Transcripción)";
		}
		
		return $tipoMedicamento;
	}
	
	function consultarTipoPrestacion($tipoPrest)
	{
		// 1: Única 2: Sucesiva
		$tipoPrestacion = $tipoPrest;
		if($tipoPrest=="1")
		{
			$tipoPrestacion = "Única";
		}
		elseif($tipoPrest=="2")
		{
			$tipoPrestacion = "Sucesiva";
		}
		
		return $tipoPrestacion;
	}
	
	function consultarDuracionTratamiento($durTrat)
	{
		// 1: Minuto(s) 2: Hora(s) 3: Día(s) 4: Semana(s) 5: Mes(es) 6: Año
		$duracionTratamiento = $durTrat;
		if($durTrat=="1")
		{
			$duracionTratamiento = "Minuto(s)";
		}
		elseif($durTrat=="2")
		{
			$duracionTratamiento = "Hora(s)";
		}
		elseif($durTrat=="3")
		{
			$duracionTratamiento = "Día(s)";
		}
		elseif($durTrat=="4")
		{
			$duracionTratamiento = "Semana(s)";
		}
		elseif($durTrat=="5")
		{
			$duracionTratamiento = "Mes(es)";
		}
		elseif($durTrat=="6")
		{
			$duracionTratamiento = "Año";
		}

		
		return $duracionTratamiento;
	}
	
	function consultarEstadoJuntaMedica($estadoJM)
	{
		// 1. No requiere junta de profesionales
		// 2. Requiere junta de profesionales y pendiente evaluación
		// 3. Evaluada por la junta de profesionales y fue aprobada
		// 4. Evaluada por la junta de profesionales y no fue aprobada
		$juntaMedica = $estadoJM;
		if($estadoJM=="1")
		{
			$juntaMedica = "No requiere junta de profesionales";
		}
		elseif($estadoJM=="2")
		{
			$juntaMedica = "Requiere junta de profesionales y pendiente evaluación";
		}
		elseif($estadoJM=="3")
		{
			$juntaMedica = "Evaluada por la junta de profesionales y fue aprobada";
		}
		elseif($estadoJM=="4")
		{
			$juntaMedica = "Evaluada por la junta de profesionales y no fue aprobada";
		}
		
		return $juntaMedica;
	}
	
	function consultarTipoAnulacion($tipo)
	{
		// 1. Por solicitud de la IPS
		// 2. Por solicitud de la EPS
		// 3. Por solicitud del prescriptor
		
		$tipoAnulacion = $tipo;
		if($tipo=="1")
		{
			$tipoAnulacion = "Por solicitud de la IPS";
		}
		elseif($tipo=="2")
		{
			$tipoAnulacion = "Por solicitud de la EPS";
		}
		elseif($tipo=="3")
		{
			$tipoAnulacion = "Por solicitud del prescriptor";
		}
		
		return $tipoAnulacion;
	}
	
	function consultarEstadoAnulacion($estado)
	{
		// 0: No Anulado
		// 1: Anulado
		
		$estadoAnulacion = $estado;
		if($estado=="0")
		{
			$estadoAnulacion = "No Anulado";
		}
		elseif($estado=="1")
		{
			$estadoAnulacion = "Anulado";
		}
		
		return $estadoAnulacion;
	}
	
	function consultarNombreUsuario($documento)
	{
		global $conex;
		
		$nombre = "";
		if($documento!="")
		{
			$nroDocumento = substr($documento,2);
		
			$queryNombre = "  SELECT CONCAT_WS(' ',Ideno1,Ideno2,Ideap1,Ideap2) as nombre 
								FROM talhuma_000013 
							   WHERE Ideced='".$nroDocumento."' 
								 AND Ideest='on';";
								 
			$resNombre = mysql_query($queryNombre, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNombre . " - " . mysql_error());		   
			$numNombre = mysql_num_rows($resNombre);
			
			
			if($numNombre>0)
			{
				$rowsNombre = mysql_fetch_array($resNombre);
				
				$nombre = utf8_encode($rowsNombre['nombre']);
			}						 
			
		}
		
		return $nombre;
	}
	
	function actualizarUltimaFechaKronMipres($ultimaFecha,$detapl)
	{
		global $conex;
		global $wemp_pmla;
		
		$qUpdate = " UPDATE root_000051 
							SET Detval='".$ultimaFecha."' 
						  WHERE Detemp='".$wemp_pmla."' 
							AND Detapl='".$detapl."';";
		
		$resUpdate = mysql_query($qUpdate,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qUpdate." - ".mysql_error());	
				
	}
	
	function consultarPresentacionProductoNutricional($codProdNut)
	{
		global $conex;
		
		$queryPresentacionProdNut = " SELECT Lpnfor,Lpnpre 
										FROM mipres_000030 
									   WHERE Lpncod='".$codProdNut."';";
								 
		$resPresentacionProdNut = mysql_query($queryPresentacionProdNut, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPresentacionProdNut . " - " . mysql_error());		   
		$numPresentacionProdNut = mysql_num_rows($resPresentacionProdNut);
		
		$presentacionComercial = "";
		if($numPresentacionProdNut>0)
		{
			$rowPresentacionProdNut = mysql_fetch_array($resPresentacionProdNut);
			
			$presentacionComercial = $rowPresentacionProdNut['Lpnfor']." ".$rowPresentacionProdNut['Lpnpre'];
		}	
		
		return $presentacionComercial;
	}
	
	function mayuscula($texto)
	{
		$caracterMayuscula = array("Á","É","Í","Ó","Ú");
		$caracterMinuscula = array("á","é","í","ó","ú");
		
		$texto =  str_replace($caracterMinuscula, $caracterMayuscula, $texto);
					
		$texto = strtoupper(utf8_decode($texto));
		
		return $texto;
	}
		
	function generarPDF($wemp_pmla,$nroPrescripcion,$tipoPrescripcion,$contadorPDF=null,$historia=null,$ingreso=null,$responsable=null)
	{
		global $conex;
		
		include_once("root/CifrasEnLetras.php");
		
		$departamento = "ANTIOQUIA";
		$municipio = "MEDELL&Iacute;N";
		$regimen = "CONTRIBUTIVO";
		$especialidad = "";
		
		// $nombrePrestador = "";
		// if($wemp_pmla=="01")
		// {
			// $nombrePrestador = "CL&Iacute;NICA LAS AMERICAS";
		// }
		// elseif($wemp_pmla=="10")
		// {
			// $nombrePrestador = "INSTITUTO DE CANCEROLOG&Iacute;A";
		// }
		
		$municipio   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'municipioMipres' );
		$departamento   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'departamentoMipres' );
		$nombrePrestador   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'nombrePrestadorMipres' );
		
		$CodVer   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'codigoVersionMipres' );
		$resolucion   = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'resolucionMipres' );
		
		
		$encabezadoPrescripcion = consultarEncabezadoPrescripcion($nroPrescripcion);
		
		
		$medicamentosPrescripcion = consultarMedicamentosPrescripcion($nroPrescripcion);
		$nutricionesPrescripcion = consultarProductosNutricionalesPrescripcion($nroPrescripcion);
		$procedimientosPrescripcion = consultarProcedimientosPrescripcion($nroPrescripcion);
		$dispositivosPrescripcion = consultarDispositivosPrescripcion($nroPrescripcion);
		$serviciosPrescripcion = consultarServiciosComplementariosPrescripcion($nroPrescripcion);
		
		
		$htmlInicial = "	
							<tr>
								<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>DATOS DEL PRESTADOR</td>
							</tr>
							<tr>
								<td colspan='4' style='border:1px solid #101010;'><b>Departamento:<br></b>&nbsp;".$departamento."</td>
								<td colspan='4' style='border:1px solid #101010;'><b>Municipio:<br></b>&nbsp;".$municipio."</td>
								<td colspan='4' style='border:1px solid #101010;'><b>C&oacute;digo habilitaci&oacute;n:</b><br>&nbsp;".$encabezadoPrescripcion['CodHabIPS']."</td>
							</tr>
							<tr>
								<td colspan='6' style='border:1px solid #101010;'><b>Documento de identificaci&oacute;n:<br></b>&nbsp;".$encabezadoPrescripcion['NroIDIPS']."</td>
								<td colspan='6' style='border:1px solid #101010;'><b>Nombre Prestador de Servicios de Salud:<br></b>&nbsp;".$nombrePrestador."</td>
							</tr>
							<tr>
								<td colspan='6' style='border:1px solid #101010;'><b>Direcci&oacute;n:<br></b>&nbsp;".mayuscula($encabezadoPrescripcion['DirSedeIPS'])."</td>
								<td colspan='6' style='border:1px solid #101010;'><b>Tel&eacute;fono:<br></b>&nbsp;".mayuscula($encabezadoPrescripcion['TelSedeIPS'])."</td>
							</tr>
							<tr>
								<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>DATOS DEL PACIENTE</td>
							</tr>
							<tr>
								<td colspan='2' style='border:1px solid #101010;'><b>Documento de Identificaci&oacute;n:<br></b>&nbsp;".$encabezadoPrescripcion['TipoIDPaciente']."".$encabezadoPrescripcion['NroIDPaciente']."</td>
								<td colspan='4' style='border:1px solid #101010;'><b>Primer Apellido:<br></b>&nbsp;".mayuscula($encabezadoPrescripcion['PAPaciente'])."</td>
								<td colspan='2' style='border:1px solid #101010;'><b>Segundo Apellido:<br></b>&nbsp;".mayuscula($encabezadoPrescripcion['SAPaciente'])."</td>
								<td colspan='2' style='border:1px solid #101010;'><b>Primer Nombre:<br></b>&nbsp;".mayuscula($encabezadoPrescripcion['PNPaciente'])."</td>
								<td colspan='2' style='border:1px solid #101010;'><b>Segundo Nombre:<br></b>&nbsp;".mayuscula($encabezadoPrescripcion['SNPaciente'])."</td>
							</tr>
							<tr>
								<td colspan='3' style='border:1px solid #101010;'><b>N&uacute;mero Historia Cl&iacute;nica:<br></b>&nbsp;".$encabezadoPrescripcion['NroIDPaciente']."</td>
								<td colspan='3' style='border:1px solid #101010;'><b>Diagn&oacute;stico Principal:<br></b>&nbsp;".$encabezadoPrescripcion['CodDxPpal']." ".mayuscula(consultarDiagnostico($encabezadoPrescripcion['CodDxPpal']))."</td>
								<td colspan='3' style='border:1px solid #101010;'><b>Usuario R&eacute;gimen:<br></b>&nbsp;".$regimen."</td>
								<td colspan='3' style='border:1px solid #101010;'><b>Ambito atenci&oacute;n:<br></b>&nbsp;".mayuscula(consultarAmbitoAtencion($encabezadoPrescripcion['CodAmbAte']))."</td>
							</tr>";
		
		$htmlFinal = "		<tr>
								<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>PROFESIONAL TRATANTE</td>
							</tr>
							<tr>
								<td colspan='6' style='border:1px solid #101010;'>Documento de Identificaci&oacute;n:<br>&nbsp;".$encabezadoPrescripcion['TipoIDProf']." ".$encabezadoPrescripcion['NumIDProf']."</td>
								<td colspan='6' style='border:1px solid #101010;'>Nombre:<br>&nbsp;".mayuscula($encabezadoPrescripcion['PNProfS'])." ".mayuscula($encabezadoPrescripcion['SNProfS'])." ".mayuscula($encabezadoPrescripcion['PAProfS'])." ".mayuscula($encabezadoPrescripcion['SAProfS'])."</td>
							</tr>
							<tr>
								<td colspan='6' style='border:1px solid #101010;'>Registro Profesional:<br>&nbsp;".$encabezadoPrescripcion['RegProfS']."</td>
								<td colspan='6' rowspan='2' style='border:1px solid #101010;text-align:center;'><br><br>Firma</td>
							</tr>
							<tr>
								<td colspan='6' rowspan='2' style='border:1px solid #101010;'>Especialidad:<br>&nbsp;".$especialidad."</td>
							</tr>
							<tr>
								<td colspan='6' rowspan='1' style='border:1px solid #101010;position:relative;'>CodVer:<span style='position:absolute;right:7px;'>".$CodVer."</span></td>
							</tr>
							<tr>
								<td colspan='12' style='border:1px solid #FFFFFF;font-size:8pt;'><b>".$resolucion."</b></td>
							</tr>";
		
		$formulaMedica = false;
		if(count($medicamentosPrescripcion)>0 || count($nutricionesPrescripcion)>0)
		{
			$formulaMedica = true;
		}
		
		$planDeManejo = false;
		if(count($procedimientosPrescripcion)>0 || count($dispositivosPrescripcion)>0 || count($serviciosPrescripcion)>0)
		{
			$planDeManejo = true;
		}
		
		
		
		$html = "";
		
		$htmlMedicamentos = "";
		$htmlNutriciones = "";
		if($formulaMedica)
		{
			$saltoPagina = "";
			if($planDeManejo)
			{
				$saltoPagina = "page-break-after: always;";
			}
			
			if(count($medicamentosPrescripcion)>0)
			{
				$htmlMedicamentos = "<tr>
										<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>MEDICAMENTOS</td>
									</tr>
									<tr align='center'>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Tipo prestaci&oacute;n<br></td>
										<td colspan='3' style='border:1px solid #101010;width:100px;'>Nombre<br>Medicamento /<br>Forma Farmac&eacute;utica<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Dosis<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>V&iacute;a Administraci&oacute;n<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Frecuencia<br>Administraci&oacute;n<br></td>
										<td colspan='2' style='border:1px solid #101010;width:100px;'>Indicaciones<br>Especiales<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Duraci&oacute;n<br>Tratamiento<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Recomendaciones<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Cantidades<br>Farmac&eacute;uticas<br> Nro / Letras / Unidad<br>Farmac&eacute;utica<br></td>
									</tr>";
									
				foreach($medicamentosPrescripcion as $keyMed => $valueMed)					
				{
					
					$cantidadLetras = CifrasEnLetras::convertirNumeroEnLetras($valueMed['CantTotalF']);
					
					$htmlMedicamentos.="<tr align=''>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".mayuscula(consultarTipoPrestacion($valueMed['TipoPrest']))."</td>
											<td colspan='3' style='border:1px solid #101010;width:100px;'>".mayuscula($valueMed['DescMedPrinAct'])." / ".mayuscula(consultarFormaFarmaceutica($valueMed['CodFF']))."</td>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".$valueMed['Dosis']." ".mayuscula(consultarUnidadMedida($valueMed['DosisUM']))."</td>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".mayuscula(consultarVia($valueMed['CodVA']))."</td>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".$valueMed['NoFAdmon']." ".mayuscula(consultarFrecuencia($valueMed['CodFreAdmon']))."</td>
											<td colspan='2' style='border:1px solid #101010;width:100px;'>".mayuscula(consultarIndicacionesEspeciales($valueMed['IndEsp']))."</td>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".mayuscula($valueMed['CanTrat']." ".strtoupper(consultarDuracionTratamiento($valueMed['DurTrat'])))."</td>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".mayuscula($valueMed['IndRec'])."</td>
											<td colspan='1' style='border:1px solid #101010;width:100px;'>".$valueMed['CantTotalF']." / ".mayuscula($cantidadLetras)." / ".mayuscula(consultarPresentacion($valueMed['UFCantTotal']))."</td>
										</tr>";
				}
			}
			
			if(count($nutricionesPrescripcion)>0)
			{
				$htmlNutriciones = "<tr>
										<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>PRODUCTOS NUTRICIONALES</td>
									</tr>
									<tr align='center'>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Tipo prestaci&oacute;n<br></td>
										<td colspan='4' style='border:1px solid #101010;width:100px;'>Producto Nutricional /<br>Forma<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Dosis<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>V&iacute;a Administraci&oacute;n<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Frecuencia<br>Administraci&oacute;n<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Indicaciones<br>Especiales<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Duraci&oacute;n Tratamiento<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Recomendaciones<br></td>
										<td colspan='1' style='border:1px solid #101010;width:100px;'>Cantidades<br>Farmac&eacute;uticas<br>Nro / Letras / Unidad<br>Farmac&eacute;utica<br></td>
									</tr>";
									
				foreach($nutricionesPrescripcion as $keyNut => $valueNut)					
				{
					$cantidadLetras = CifrasEnLetras::convertirNumeroEnLetras($valueNut['CantTotalF']);
					$htmlNutriciones .="<tr>
											<td colspan='1' style='border:1px solid #101010;'>".mayuscula(consultarTipoPrestacion($valueNut['TipoPrest']))."</td>
											<td colspan='4' style='border:1px solid #101010;'>".mayuscula(consultarTipoProductoNutricional($valueNut['TippProNut']))."-".mayuscula(consultarProductoNutricional($valueNut['DescProdNutr']))." ".mayuscula(consultarPresentacionProductoNutricional($valueNut['DescProdNutr']))." / ".mayuscula(consultarFormaProductoNutricional($valueNut['CodForma']))."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueNut['Dosis']." ".mayuscula(consultarUnidadMedida($valueNut['DosisUM']))."</td>
											<td colspan='1' style='border:1px solid #101010;'>".mayuscula(consultarViaProductoNutricional($valueNut['CodViaAdmon']))."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueNut['NoFAdmon']." ".mayuscula(consultarFrecuencia($valueNut['CodFreAdmon']))."</td>
											<td colspan='1' style='border:1px solid #101010;'>".mayuscula(consultarIndicacionesEspeciales($valueNut['IndEsp']))."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueNut['CanTrat']." ".mayuscula(consultarDuracionTratamiento($valueNut['DurTrat']))."</td>
											<td colspan='1' style='border:1px solid #101010;'>".mayuscula($valueNut['IndRec'])."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueNut['CantTotalF']." / ".mayuscula($cantidadLetras)." / ".mayuscula(consultarFormaProductoNutricional($valueNut['UFCantTotal']))."</td>
										</tr>";
				}				
			}
			
			$htmlEncabezado = "	<tr style='height:75px;width:900px;'>
									<td colspan='2' style='border:1px solid #101010;width:230px;'><img src='../../../images/medical/movhos/LogoMinsalud.PNG' width='225px'></td>
									<td colspan='8' style='border:1px solid #101010;font-weight:bold;font-family:courier;font-size:14pt;text-align:center;width:450px;'>F&Oacute;RMULA M&Eacute;DICA</td>
									<td colspan='2' style='border:1px solid #101010;font-weight:bold;font-size:7pt;width:225px;'>
										<table style='font-size:7.5pt;width:98%;height:70px; border-collapse: collapse;margin-left: 1%;'>
											<tr>
												<td style='border:1px solid #101010;font-weight:bold;'>Fecha y hora de Expedici&oacute;n (AAAA-MM-DD)</td>
											</tr>
											<tr>
												<td style='border:1px solid #101010; border-spacing: 15px;'>".$encabezadoPrescripcion['FPrescripcion']." ".$encabezadoPrescripcion['HPrescripcion']."</td>
											</tr>
											<tr>
												<td style='border:1px solid #101010;font-weight:bold;'>Nro. Prescripci&oacute;n</td>
											</tr>
											<tr>
												<td style='border:1px solid #101010;font-size:9pt;'>".$nroPrescripcion."</td>
											</tr>
										</table>
									</td>
								</tr>";
			// word-break: break-all;
			$html .= "	<div style='".$saltoPagina."'>
							<table class='tablasImpresionMipres' style='border:1px solid #101010; border-collapse: collapse;width:900px;font-family:sans-serif;font-size:7.5pt;table-layout: auto;'>
								".$htmlEncabezado."
								".$htmlInicial."
								".$htmlMedicamentos."
								".$htmlNutriciones."
								".$htmlFinal."
							</table>
						</div>";
						
		}
		
		$htmlProcedimientos = "";
		$htmlDispositivos = "";
		$htmlServicios = "";
		$htmlPlanDeManejo = "";
		if($planDeManejo)
		{
			if(count($procedimientosPrescripcion)>0)
			{
				$htmlProcedimientos = "<tr>
											<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>PROCEDIMIENTOS</td>
										</tr>
										<tr align='center'>
											<td colspan='1' style='border:1px solid #101010;'>Tipo prestaci&oacute;n<br></td>
											<td colspan='3' style='border:1px solid #101010;'>Procedimiento<br></td>
											<td colspan='2' style='border:1px solid #101010;'>Indicaciones o Recomendaciones<br></td>
											<td colspan='1' style='border:1px solid #101010;'>Cantidad<br></td>
											<td colspan='2' style='border:1px solid #101010;'>Frecuencia Uso<br></td>
											<td colspan='2' style='border:1px solid #101010;'>Duraci&oacute;n Tratamiento (Cantidad - Per&iacute;odo)<br></td>
											<td colspan='1' style='border:1px solid #101010;'>Cantidad Total<br></td>
										</tr>";
										
				foreach($procedimientosPrescripcion as $keyProc => $valueProc)					
				{
					$duracionDelTratamiento = "";
					if($valueProc['Cant']!="0")
					{
						$duracionDelTratamiento = $valueProc['Cant']." ".consultarDuracionTratamiento($valueProc['CodPerDurTrat']);
					}
					
					$htmlProcedimientos.="<tr>
											<td colspan='1' style='border:1px solid #101010;'>".mayuscula(consultarTipoPrestacion($valueProc['TipoPrest']))."</td>
											<td colspan='3' style='border:1px solid #101010;'>".$valueProc['CodCUPS']." - ".mayuscula(consultarProcedimientoCups($valueProc['CodCUPS']))."</td>
											<td colspan='2' style='border:1px solid #101010;'>".mayuscula($valueProc['IndRec'])."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueProc['CanForm']."</td>
											<td colspan='2' style='border:1px solid #101010;'>".$valueProc['CadaFreUso']." ".mayuscula(consultarFrecuencia($valueProc['CodFreUso']))."</td>
											<td colspan='2' style='border:1px solid #101010;'>".mayuscula($duracionDelTratamiento)."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueProc['CantTotal']."</td>
										</tr>";
				}				
			}
			
			if(count($dispositivosPrescripcion)>0)
			{
				$htmlDispositivos .= "<tr>
										<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>DISPOSITIVOS M&Eacute;DICOS</td>
									</tr>
									<tr align='center'>
										<td colspan='1' style='border:1px solid #101010;'>Tipo prestaci&oacute;n<br></td>
										<td colspan='3' style='border:1px solid #101010;'>Dispositivo M&eacute;dico<br></td>
										<td colspan='2' style='border:1px solid #101010;'>Indicaciones o Recomendaciones<br></td>
										<td colspan='1' style='border:1px solid #101010;'>Cantidad<br></td>
										<td colspan='2' style='border:1px solid #101010;'>Frecuencia Uso<br></td>
										<td colspan='2' style='border:1px solid #101010;'>Duraci&oacute;n Tratamiento (Cantidad - Per&iacute;odo)<br></td>
										<td colspan='1' style='border:1px solid #101010;'>Cantidad Total<br></td>
									</tr>";
									
				foreach($dispositivosPrescripcion as $keyDisp => $valueDisp)					
				{
					$duracionDelTratamiento = "";
					if($valueDisp['CodPerDurTrat']!="")
					{
						$duracionDelTratamiento = $valueDisp['Cant']." ".consultarDuracionTratamiento($valueDisp['CodPerDurTrat']);
					}					
					
					$htmlDispositivos .= "<tr>
											<td colspan='1' style='border:1px solid #101010;'>".mayuscula(consultarTipoPrestacion($valueDisp['TipoPrest']))."</td>
											<td colspan='3' style='border:1px solid #101010;'>".mayuscula(consultarDispositivo($valueDisp['CodDisp']))."</td>
											<td colspan='2' style='border:1px solid #101010;'>".mayuscula($valueDisp['IndRec'])."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueDisp['CanForm']."</td>
											<td colspan='2' style='border:1px solid #101010;'>".$valueDisp['CadaFreUso']." ".mayuscula(consultarFrecuencia($valueDisp['CodFreUso']))."</td>
											<td colspan='2' style='border:1px solid #101010;'>".mayuscula($duracionDelTratamiento)."</td>
											<td colspan='1' style='border:1px solid #101010;'>".$valueDisp['CantTotal']."</td>
										</tr>";
				}					
			}
			
			if(count($serviciosPrescripcion)>0)
			{
				$htmlServicios = "<tr>
										<td colspan='12' style='border:1px solid #101010;background-color:#bfbfbf;font-weight:bold;text-align:center;font-family:sans-serif;'>SERVICIOS COMPLEMENTARIOS</td>
									</tr>
									<tr align='center'>
										<td colspan='1' style='border:1px solid #101010;'>Tipo prestaci&oacute;n<br></td>
										<td colspan='3' style='border:1px solid #101010;'>Servicio Complementario<br></td>
										<td colspan='2' style='border:1px solid #101010;'>Indicaciones o Recomendaciones<br></td>
										<td colspan='1' style='border:1px solid #101010;'>Cantidad<br></td>
										<td colspan='2' style='border:1px solid #101010;'>Frecuencia Uso<br></td>
										<td colspan='2' style='border:1px solid #101010;'>Duraci&oacute;n Tratamiento (Cantidad - Per&iacute;odo)<br></td>
										<td colspan='1' style='border:1px solid #101010;'>Cantidad Total<br></td>
									</tr>";
				
				foreach($serviciosPrescripcion as $keySer => $valueSer)					
				{
					$duracionDelTratamiento = "";
					if($valueSer['Cant']!="")
					{
						$duracionDelTratamiento = $valueSer['Cant']." ".consultarDuracionTratamiento($valueSer['CodPerDurTrat']);
					}	
					
					$htmlServicios.="<tr>
										<td colspan='1' style='border:1px solid #101010;'>".mayuscula(consultarTipoPrestacion($valueSer['TipoPrest']))."</td>
										<td colspan='3' style='border:1px solid #101010;'>".mayuscula(consultarServicioComplementario($valueSer['CodSerComp']))."</td>
										<td colspan='2' style='border:1px solid #101010;'>".mayuscula($valueSer['IndRec'])."</td>
										<td colspan='1' style='border:1px solid #101010;'>".$valueSer['CanForm']."</td>
										<td colspan='2' style='border:1px solid #101010;'>".$valueSer['CadaFreUso']." ".mayuscula(consultarFrecuencia($valueSer['CodFreUso']))."</td>
										<td colspan='2' style='border:1px solid #101010;'>".mayuscula($duracionDelTratamiento)."</td>
										<td colspan='1' style='border:1px solid #101010;'>".$valueSer['CantTotal']."</td>
									</tr>";
				}				
			}
			
			$htmlEncabezado = "	<tr style='height:75px;width:900px;'>
									<td colspan='2' style='border:1px solid #101010;width:230px;'><img src='../../../images/medical/movhos/LogoMinsalud.PNG' width='225px'></td>
									<td colspan='7' style='border:1px solid #101010;font-weight:bold;font-family:courier;font-size:14pt;text-align:center;width:450px;'>PLAN DE MANEJO</td>
									<td colspan='3' style='border:1px solid #101010;font-weight:bold;font-size:7pt;width:225px;'>
										<table style='font-size:7.5pt;width:98%;height:70px; border-collapse: collapse;margin-left: 1%;'>
											<tr>
												<td style='border:1px solid #101010;font-weight:bold;'>Fecha y hora de Expedici&oacute;n (AAAA-MM-DD)</td>
											</tr>
											<tr>
												<td style='border:1px solid #101010; border-spacing: 15px;'>".$encabezadoPrescripcion['FPrescripcion']." ".$encabezadoPrescripcion['HPrescripcion']."</td>
											</tr>
											<tr>
												<td style='border:1px solid #101010;font-weight:bold;'>Nro. Prescripci&oacute;n</td>
											</tr>
											<tr>
												<td style='border:1px solid #101010;font-size:9pt;'>".$nroPrescripcion."</td>
											</tr>
										</table>
									</td>
								</tr>";
			$html .= "	<div>
							<table class='tablasImpresionMipres' style='border:1px solid #101010; border-collapse: collapse;width:900px;font-family:sans-serif;font-size:8pt;table-layout: fixed;'>
								".$htmlEncabezado."
								".$htmlInicial."
								".$htmlProcedimientos."
								".$htmlDispositivos."
								".$htmlServicios."
								".$htmlFinal."
							</table>
						</div>";		
		}
		
		$fechaHora = strtotime(date("Y-m-d H:i:s"));
		
		$wnombrePDF = $fechaHora."_".$nroPrescripcion;
		// $wnombrePDF = $nroPrescripcion;
	  
	 
		
		//CREAR UN ARCHIVO .HTML CON EL CONTENIDO CREADO
		$dir = 'mipres';
		if(is_dir($dir)){ }
		else { mkdir($dir,0777); }
		
		$archivo_dir = $dir."/".$wnombrePDF.".html";
		// echo "<div style='display:none;'>".$archivo_dir."</div>";
		if(file_exists($archivo_dir)){
			unlink($archivo_dir);
		}
		$f = fopen( $archivo_dir, "w+" );
		fwrite( $f, $html);
		fclose( $f );

		$mipresPdf = "".$dir."/".$wnombrePDF.".pdf";
		
		// Si no se crean los pdf, revisar que generarPdfMipres.sh tenga todos los permisos
		$respuesta = shell_exec( "./generarPdfMipres.sh ".$wnombrePDF );
		if(file_exists($archivo_dir)){
			unlink($archivo_dir);
		}

		//modificacion jaime mejia
		if (isset($_GET['automatizacion_pdfs'])) {

			//CARPETA PRINCIPAL
			$carpetaPrincipal= consultarAliasPorAplicacion($conex, $wemp_pmla, 'documentosAutomatizacion');

			if (!file_exists($carpetaPrincipal)) {
				mkdir($carpetaPrincipall, 0777, true);
			}

			//CARPETA PARA GUARDAR POR EMPRESA
			$carpetaEmpresa = $carpetaPrincipal . '/' . $wemp_pmla;
			if (!file_exists($carpetaEmpresa)) {
				mkdir($carpetaEmpresa, 0777, true);
			}

			//CREAR CARPETA PARA GUARDAR SOPORTES
			$carpeta = $carpetaEmpresa . '/' . $historia . '-' . $ingreso;
			if (!file_exists($carpeta)) {
				mkdir($carpeta, 0777, true);
			}

			//CARPETA PARA GUARDAR POR RESPONSABLE
			$carpetaResponsable = $carpeta  . '/' . $responsable;
			if (!file_exists($carpetaResponsable)) {
				mkdir($carpetaResponsable, 0777, true);
			}

			$carpetaHistoria = $historia.'-'.$ingreso;
			$nombrePDFautomatizacion = $carpetaHistoria .'-'.$_GET['soporte'].'-'.$contadorPDF.'.pdf';
			$archivo_destino = $carpetaResponsable .'/'. $nombrePDFautomatizacion;
			copy($mipresPdf,$archivo_destino);
			unlink($mipresPdf);
			return ;
		}
		
		$htmlPDF  ="<div id='modalPdfMipres' align='center'>
						<br>
						<input type='button' value='Cerrar' onclick='cerrarModal();borrarPdf(\"".$mipresPdf."\");'>
						<object type='application/pdf' data='".$mipresPdf."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1  style='display: block;margin: 10px;width:90%;height:85%;'>
							<param name='src' value='".$mipresPdf."' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />
							<p style='text-align:center; width: 60%;'>
							Adobe Reader no se encuentra o la versi&oacute;n no es compatible, utiliza el icono para ir a la p&aacute;gina de descarga <br />
								<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">
								<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />
								</a>
							</p>
						</object>
						<input type='button' value='Cerrar' onclick='cerrarModal();borrarPdf(\"".$mipresPdf."\");'>
						<br>
						<br>
					</div>";
		
		return $htmlPDF;
	}
	
	function borrarPDF($wemp_pmla,$pdfMipres)
	{
		// borra el pdf actual al cerrarlo
		if(file_exists($pdfMipres)){
			unlink($pdfMipres);
		}
		
		// busca los archivos modificados hace mas de 24 horas en y los elimina
		$resultado = shell_exec( "find /var/www/matrix/hce/procesos/mipres/* -mtime +0 -exec rm {} \; ");
		
	}
	
	function consultarUltimaModificacionMaestros($wemp_pmla)
	{
		global $conex;
		
		$tablasMovimiento = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'tablasMovimientoMipres' );
		
		$tablasMovimiento = str_replace(",","','",$tablasMovimiento);
		
		$queryMaestros = "SELECT codigo,nombre 
							FROM formulario 
						   WHERE medico='mipres' 
						     AND codigo NOT IN ('".$tablasMovimiento."');";

		$resMaestros = mysql_query($queryMaestros, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryMaestros . " - " . mysql_error());		   
		$numMaestros = mysql_num_rows($resMaestros);
		
		$arrayModificacionMaestros = array();
		if($numMaestros>0)
		{
			while($rowMaestros = mysql_fetch_array($resMaestros))
			{
				$queryUltimaModificacion = " SELECT Fecha_data 
											   FROM mipres_".$rowMaestros['codigo']." 
										   ORDER BY Fecha_data DESC 
											  LIMIT 1;";
				
				$resUltimaModificacion = mysql_query($queryUltimaModificacion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryUltimaModificacion . " - " . mysql_error());		   
				$numUltimaModificacion = mysql_num_rows($resMaestros);
				
				if($numUltimaModificacion>0)
				{
					$rowUltimaModificacion = mysql_fetch_array($resUltimaModificacion);
					
					$arrayModificacionMaestros[$rowMaestros['codigo']]['codigo'] = $rowMaestros['codigo'];
					$arrayModificacionMaestros[$rowMaestros['codigo']]['descripcion'] = $rowMaestros['nombre'];
					$arrayModificacionMaestros[$rowMaestros['codigo']]['fecha'] = $rowUltimaModificacion['Fecha_data'];
				}
			}
		}								 
		
		return $arrayModificacionMaestros;
	}
	
	function mostrarUltimaModificacionMaestros($wemp_pmla)
	{
		$arrayModificacionMaestros = consultarUltimaModificacionMaestros($wemp_pmla);
		
		$html = "";
		$html .= "	<div id='modalModificacionMaestros'>
						<br>
						<table align='center'>
							<tr class='EncabezadoTabla' align='center'>
								<td colspan='2'>&Uacute;LTIMA MODIFICACI&Oacute;N MAESTROS MIPRES</td>
							</tr>
							<tr class='EncabezadoTabla' align='center'>
								<td>Tabla</td>
								<td>&Uacute;ltima modificaci&oacute;n</td>
							</tr>";
							if(count($arrayModificacionMaestros)>0)
							{
								foreach($arrayModificacionMaestros as $keyMaestro => $valueMaestro)
								{
									if ($fila_lista=='Fila2')
										$fila_lista = "Fila1";
									else
										$fila_lista = "Fila2";
									
													
		$html .= "					<tr class='".$fila_lista."'>
										<td>mipres_".$keyMaestro." - ".$valueMaestro['descripcion']."</td>
										<td align='center'>".$valueMaestro['fecha']."</td>
									</tr>";							
								}
							}
							else
							{
		$html .= "				<tr class='fila1'>
									<td colspan='2'>No se encontraron maestros</td>
								</tr>";						
							}
		
		$html .= "		</table>
						<br>
						<input type='button' value='Cerrar' onclick='cerrarModal();'>
					</div>";
					
		return $html;			
	}
	
	function consultarNovedadesPendientes()
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryNovedades = "SELECT Novcod,Novnpr,Novnpf
							 FROM ".$wbasedatoMipres."_000008 
							WHERE Novest='on' 
							  AND Novact='off';";
		
		$resNovedades = mysql_query($queryNovedades, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryNovedades . " - " . mysql_error());		   
		$numNovedades = mysql_num_rows($resNovedades);
		
		$primeraFecha = "";
		if($numNovedades>0)
		{
			while($rowsNovedades = mysql_fetch_array($resNovedades))
			{
				actualizarNovedad($rowsNovedades['Novcod'],$rowsNovedades['Novnpr'],$rowsNovedades['Novnpf']);
			}
		}
	}
	
	function actualizarNovedad($tipoNovedad,$nroPrescripcion,$nroPrescripcionF)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedatoMipres;
		
		// 1: Modificación 2: Anulación 3: Transcripción

		$prescripcionActualizada = false;

		// si es tipo 1 (Modificación) consumir el web service de PrescripcionXNumero para la nueva prescripcion, actualizar en movhos_000134 o movhos_000135 el consecutivo separado por | y actualizar el estado en la tabla de encabezados
		if($tipoNovedad=="1")
		{
			$sinRegistrar = consultarPrescripcionSinRegistrar($nroPrescripcionF);
			
			if($sinRegistrar)
			{
				$jsonPrescripcion= consumirWebServicePrescripcion($nroPrescripcionF,$wemp_pmla);
			
			
				if(count($jsonPrescripcion)>0)
				{
					// consultar datos de la prescripcion anterior
					$encabezadoAnterior = consultarEncabezadoPrescripcion($nroPrescripcion);
					
					// registrar nueva prescripcion
					registrarPrescripcionesMipres($jsonPrescripcion,$encabezadoAnterior['historia'],$encabezadoAnterior['ingreso']);
					
				}
			}
			
			// actualizarTablasCTC
			actualizarCTC($nroPrescripcion,$nroPrescripcionF);
			
			// actualizar estado de la prescripcion anterior
			$prescripcionActualizada = actualizarEstadoPrescripcion($nroPrescripcion,$tipoNovedad);
			
			
			
		}
		elseif($tipoNovedad=="2") // si es tipo 2 (Anulación) consumir el web service de anulación por prescripcion, guardar en tabla y actualizar el estado en la tabla de encabezados
		{
			$jsonAnulacion = consumirWebServiceAnulacion($nroPrescripcion,$wemp_pmla);
			
			if(count($jsonAnulacion)>0)
			{
				foreach($jsonAnulacion as $keyAnulacion => $valueAnulacion)
				{
					foreach($valueAnulacion as $keyAnulacion1 => $valueAnulacion1)
					{
						$valueAnulacion1->Justificacion = str_replace("'","",$valueAnulacion1->Justificacion);
						$valueAnulacion1->Observacion = str_replace("'","",$valueAnulacion1->Observacion);
						
						if($valueAnulacion1->FAnulacion=="")
						{
							$valueAnulacion1->FAnulacion = "0000-00-00";
						}
						
						$qInsertAnulacion = " INSERT INTO ".$wbasedatoMipres."_000009 ( Medico ,    Fecha_data     ,	 Hora_data	      ,					Anunpr	  	 		 ,					Anutip	 	   	   ,				Anujus			  	 ,					Anuobs		  	 , 					Anufso		  	, 					Anuuso		  		 , 					Anuean		  	  , 				Anufan		  	 , 						Anuuan		   ,Anuest,Seguridad ) 
																 VALUES ('mipres','".date("Y-m-d")."','".date("H:i:s")."','".$valueAnulacion1->NoPrescripcion."' ,'".$valueAnulacion1->TipoAnulacion."','".$valueAnulacion1->Justificacion."','".$valueAnulacion1->Observacion."','".$valueAnulacion1->FSolicitud."','".$valueAnulacion1->Usuario_Solicita."','".$valueAnulacion1->EstAnulacion."','".$valueAnulacion1->FAnulacion."','".$valueAnulacion1->Usuario_Anula."', 'on' ,'C-mipres');";
														
						$resInsertAnulacion = mysql_query($qInsertAnulacion,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertAnulacion." - ".mysql_error());	
						
						$numInsertAnulacion = mysql_affected_rows();
			
						if($numInsertAnulacion>0)
						{
							$prescripcionActualizada = actualizarEstadoPrescripcion($nroPrescripcion,$tipoNovedad);
						}
					}
				}
			}
		}
		
		if($prescripcionActualizada)
		{
			marcarNovedadActualizada($nroPrescripcion,$nroPrescripcionF);
		}
	}
	
	function marcarNovedadActualizada($nroPrescripcion,$nroPrescripcionF)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryUpdateNovedad = "UPDATE ".$wbasedatoMipres."_000008
								  SET Novact='on'
								WHERE Novnpr='".$nroPrescripcion."' 
								  AND Novnpf='".$nroPrescripcionF."' 
								  AND Novest='on';";
		
		$resUpdateNovedad = mysql_query($queryUpdateNovedad, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryUpdateNovedad . " - " . mysql_error());									
	}
	
	function registrarJunta($nroPrescripcion)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryJuntaProfesional =" SELECT * 
									FROM ".$wbasedatoMipres."_000010 
								   WHERE Jupnpr='".$nroPrescripcion."' 
									 AND Jupest='on';";
							 
		$resJuntaProfesional = mysql_query($queryJuntaProfesional, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryJuntaProfesional . " - " . mysql_error());		   
		$numJuntaProfesional = mysql_num_rows($resJuntaProfesional);
		
		if($numJuntaProfesional==0)
		{
			$qInsertJuntaMedica = " INSERT INTO ".$wbasedatoMipres."_000010 ( Medico ,    Fecha_data     ,	 Hora_data	      ,		Jupnpr		   ,Jupest,Seguridad ) 
													   VALUES ('".$wbasedatoMipres."','".date("Y-m-d")."','".date("H:i:s")."','".$nroPrescripcion."' , 'on' ,'C-".$wbasedatoMipres."');";
											
			$resInsertJuntaMedica = mysql_query($qInsertJuntaMedica,$conex) or die("Error: " . mysql_errno() . " - en el query: ".$qInsertJuntaMedica." - ".mysql_error());	
			
		}
	}
	
	function consultarJuntasPendientesDeRegistrar($wemp_pmla)
	{
		global $conex;
		global $wbasedatoMipres;
		
		$queryPrescripcion = "SELECT Jupnpr 
								FROM ".$wbasedatoMipres."_000010,".$wbasedatoMipres."_000001 
							   WHERE Jupejm=''
								 AND Jupnpr=Prenop
								 AND Preepr='4'
							GROUP BY Jupnpr;";
		
		$resPrescripcion = mysql_query($queryPrescripcion, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryPrescripcion . " - " . mysql_error());		   
		$numPrescripcion = mysql_num_rows($resPrescripcion);
		
		if($numPrescripcion>0)
		{
			while($rowsPrescripcion = mysql_fetch_array($resPrescripcion))
			{
				consumirWebServiceJuntaProfesionalXPrescripcion($rowsPrescripcion['Jupnpr'],$wemp_pmla);
			}
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
		case 'consultarPrescripcionPacFec':
		{	
			$data = consumirWebServicesMipres($fechaMipres,$wemp_pmla,$general,$historia,$ingreso,$tipoDocumento,$documento,$hora,$origen);
			
			if($data == "")
			{
				$data = consumirWebServicesMipres($fechaDiaSig,$wemp_pmla,$general,$historia,$ingreso,$tipoDocumento,$documento,$horaDiaSig,$origen);
			}
			
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarPrescripcionMipres':
		{	
			$data = pintarPrescripcionMipres($fechaMipres,$wemp_pmla,$general,$historia,$ingreso,$tipoDocumento,$documento,$codPrescMipres,$reporte);
			echo json_encode($data);
			break;
			return;
		}
		case 'consultarListaEps':
		{	
			$data = consultarListaEps();
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarReportePrescripcionMipres':
		{
			if (isset($_GET['automatizacion_pdfs'])) {
				$data = pintarGenerarPDF($wemp_pmla, $historia, $ingreso,$responsable, $fechaInicial, $fechaFinal, $tipDocPac, $docPac, $tipDocMed, $docMed, $codEps, $tipoPrescrip, $nroPrescripcion, $filtroMipres, $ambitoAtencion);					
			}
			else{
				$data = pintarTabPrescripciones($wemp_pmla, $fechaInicial, $fechaFinal, $tipDocPac, $docPac, $tipDocMed, $docMed, $codEps, $tipoPrescrip, $nroPrescripcion, $filtroMipres, $ambitoAtencion);
				$data = utf8_encode($data);
				echo json_encode($data);
			}
			break;
			return;
		}
		case 'pintarReporteNovedadesMipres':
		{	
			$data = pintarTabNovedades($wemp_pmla,$fechaInicial,$fechaFinal);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarReporteJuntasProfesionalesMipres':
		{	
			$data = pintarTabJuntasProfesionales($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia,$estadoJM,$nroPrescripcion);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarReporteResumen':
		{	
			$data = pintarTabResumen($wemp_pmla,$fechaInicial,$fechaFinal,$tipoTecnologia);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'generarPDF':
		{	
			$data = generarPDF($wemp_pmla,$nroPrescripcion,$tipoPrescripcion);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'borrarPDF':
		{
			$data = borrarPDF($wemp_pmla,$pdfMipres);
			$data = utf8_encode($data);
			echo json_encode($data);
			break;
			return;
		}
		case 'pintarModificacionMaestros':
		{
			$data = mostrarUltimaModificacionMaestros($wemp_pmla);
			$data = utf8_encode($data);
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
	  <title>CTC MIPRES</title>
	</head>
	
		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />	
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>		
		
		<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		
		<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		
		
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
		

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(document).ready(function() {

		$("#msjEspere").hide();
	
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
		
		
		iniciarDatepicker("fechaInicial","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinal","Seleccione la fecha final");
		iniciarDatepicker("fechaInicialN","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinalN","Seleccione la fecha final");
		iniciarDatepicker("fechaInicialJP","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinalJP","Seleccione la fecha final");
		iniciarDatepicker("fechaInicialR","Seleccione la fecha inicial");
		iniciarDatepicker("fechaFinalR","Seleccione la fecha final");
		
		
			
		// --> Activar tabs jquery
		$( "#tabsMipres" ).tabs({
			heightStyle: "content"
		});
		
		$("#tabsMipres").show();
		cargarAutocompleteEps();
		
		if($("#fechaInicialJuntaProfesionales").val()!="" && $("#fechaInicialJuntaProfesionales").val()!=undefined)
		{
			jAlert("Desde el "+$("#fechaInicialJuntaProfesionales").val()+" tiene prescripciones activas pendientes que requieren junta de profesionales","ALERTA");
			$("#fechaInicialJP").val($("#fechaInicialJuntaProfesionales").val());
			$("#tabsMipres").tabs({ selected: 2 });
			$("#estadoJM").val("2");
			pintarPrescripcionesJP();
		}
		
	});

	
	function iniciarDatepicker(campo,descripcion)
	{
		$("#"+campo).datepicker({
			
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonText: descripcion,
			dateFormat: 'yy-mm-dd',
			buttonImageOnly: true,
			changeMonth: true,
			changeYear: true,
			minDate:$("#fechaInicioMonitorMipres").val(),
			maxDate:new Date()
		});
	}
	
	
	function iniciarTooltip(tooltip)
	{
		//Tooltip
		var cadenaTooltip = $("#"+tooltip).val();
		
		cadenaTooltip = cadenaTooltip.split("|");
		

		for(var i = 0; i < cadenaTooltip.length-1;i++)
		{
			$( "#"+cadenaTooltip[i] ).tooltip();
		}
		
	}
	
	function abrirModalMipres(codPrescMipres)
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarPrescripcionMipres',
			wemp_pmla:				$('#wemp_pmla').val(),
			historia: 				"",
			ingreso: 				"",
			tipoDocumento: 			"",
			documento: 				"",
			fechaMipres: 			"",
			general: 				"",
			codPrescMipres: 		codPrescMipres,
			reporte:				""
		}
		, function(data) {
			
			
			$( "#dvAuxModalMipres" ).html( data );
			
					
			var canWidth = $(window).width()*0.8;
			if( $( "#dvAuxModalMipres" ).width()-50 < canWidth )
				canWidth = $( "#dvAuxModalMipres" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#dvAuxModalMipres" ).height()-50 < canHeight )
				canHeight = $( "#dvAuxModalMipres" ).height();

		
			$.blockUI({ message: $('#modalMipres'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "95%",
				height	: "80%",
				left	: "2.5%",
				top		: '100px',
			} });
			
			
			
			iniciarTooltip("tooltipEstadoPrescripcion");
			iniciarTooltip("tooltipJMMed");
			iniciarTooltip("tooltipJMNut");
			iniciarTooltip("tooltipPrincipiosActivos");
			iniciarTooltip("tooltipMedicamentosUtilizados");
			iniciarTooltip("tooltipMedicamentosDescartados");
			iniciarTooltip("tooltipMedicamentosDetalles");
			iniciarTooltip("tooltipProdNutricionalesUtilizados");
			iniciarTooltip("tooltipProdNutricionalesDescartados");
			iniciarTooltip("tooltipProdNutricionalesDetalles");
			
			iniciarTooltip("tooltipJMPro");
			iniciarTooltip("tooltipJMDis");
			iniciarTooltip("tooltipJMSer");
			iniciarTooltip("tooltipProcedimientosUtilizados");
			iniciarTooltip("tooltipProcedimientosDescartados");
			iniciarTooltip("tooltipProcedimientosDetalles");
			iniciarTooltip("tooltipDispositivosDetalles");
			iniciarTooltip("tooltipServiciosComplementariosDetalles");
			
			
			
		},'json');
		
	}
	
	function cerrarModal()
	{
		$.unblockUI();
	}
	function habilitarDocumento(elemento)
	{
		campo = $(elemento).attr('habilitarCampo');
		if($(elemento).val()!="")
		{
			$("#"+campo).show();
		}
		else
		{
			$("#"+campo).hide();
			$("#"+campo).val("");
		}
	}
	
	function cargarAutocompleteEps()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'consultarListaEps',
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {
			
			var arrayEps = data;
			var eps	= new Array();
			var index		= -1;
			
			for (var codEps in arrayEps)
			{
				index++;
				eps[index] = {};
				eps[index].value  = codEps;
				eps[index].label  = codEps+"-"+arrayEps[codEps];
				eps[index].nombre = arrayEps[codEps];
				
			}
			
			$( "#txtResponsable" ).autocomplete({
				minLength: 	0,
				source: 	eps,
				select: 	function( event, ui ){					
					$( "#txtResponsable" ).val(ui.item.nombre);
					$( "#txtCodResponsable" ).val(ui.item.value);
					
					return false;
				},
				change: function( event, ui ) {
					if ( !ui.item ) {
						
						if(ui.item!==undefined)
						{
							// No se ha seleccionado una auxiliar válida
							jAlert("No se ha seleccionado una eps v&aacute;lida","ALERTA");
							$( "#txtResponsable" ).val("");
							$( "#txtCodResponsable" ).val("");
						}
					}
				}
			});	
			
		},'json');
			
	}
	
	function soloNumeros(e){
		var key = window.Event ? e.which : e.keyCode;
				
		return ((key >= 48 && key <= 57) || key<= 8);
	}
	
	function pintarPrescripciones()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarReportePrescripcionMipres',
			wemp_pmla		: $('#wemp_pmla').val(),
			fechaInicial	: $("#fechaInicial").val(),
			fechaFinal		: $("#fechaFinal").val(),
			tipDocPac		: $("#tipDocPac").val(),
			docPac			: $("#docPac").val(),
			tipDocMed		: $("#tipDocMed").val(),
			docMed			: $("#docMed").val(),
			codEps			: $("#txtCodResponsable").val(),
			tipoPrescrip	: $("#tipoPrescripcion").val(),
			nroPrescripcion	: $("#nroPrescripcion").val(),
			filtroMipres	: $("#filtroMipres").val(),
			ambitoAtencion	: $("#filtroAmbitoAtencion").val()
		}
		, function(data) {
			
			$("#listaPrescripciones").html(data);
			
			$('#buscar').quicksearch('#tablePrescripciones .find');
			
		
		
		},'json');
	}
	
	function pintarNovedades()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarReporteNovedadesMipres',
			wemp_pmla		: $('#wemp_pmla').val(),
			fechaInicial	: $("#fechaInicialN").val(),
			fechaFinal		: $("#fechaFinalN").val()
		}
		, function(data) {
			
			$("#listaNovedades").html(data);
			
			$('#buscarN').quicksearch('#tableNovedades .find');
		
		},'json');
	}
	
	function pintarPrescripcionesJP()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarReporteJuntasProfesionalesMipres',
			wemp_pmla		: $('#wemp_pmla').val(),
			fechaInicial	: $("#fechaInicialJP").val(),
			fechaFinal		: $("#fechaFinalJP").val(),
			tipoTecnologia	: $("#tipoTecnologia").val(),
			estadoJM		: $("#estadoJM").val(),
			nroPrescripcion : $("#nroPrescripcionJM").val()
		}
		, function(data) {
			
			$("#listaJuntaProfesionales").html(data);
			
			$('#buscarJP').quicksearch('#tableJuntasProfesionales .find');
		},'json');
	}
	
	function pintarResumen()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarReporteResumen',
			wemp_pmla		: $('#wemp_pmla').val(),
			fechaInicial	: $("#fechaInicialR").val(),
			fechaFinal		: $("#fechaFinalR").val(),
			tipoTecnologia	: $("#tipoTecnologiaR").val()
		}
		, function(data) {
			
			$("#listaResumen").html(data);
			
			// --> Activar tabs jquery
			$( "#tabsResumen" ).tabs({
				heightStyle: "content"
			});
			
			$("#tabsResumen").show();
			
			
			
			$('#buscarR').quicksearch('#tableResumen .find');
			$('#buscarRM').quicksearch('#tableResumen .find');
		
		},'json');
	}
	
	function cerrarVentana()
	{
		top.close();		  
    }
	
	function generarPDF(nroPrescripcion)
	{
		console.log(nroPrescripcion);
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'generarPDF',
			wemp_pmla		: $('#wemp_pmla').val(),
			nroPrescripcion	: nroPrescripcion,
			tipoPrescripcion: $('#tipoPrescripcion').val()
		}
		, function(data) {
			
			$( "#divAuxModalPdf" ).html( data );
				
			var canWidth = $(window).width()*0.8;
			if( $( "#divAuxModalPdf" ).width()-50 < canWidth )
				canWidth = $( "#divAuxModalPdf" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#divAuxModalPdf" ).height()-50 < canHeight )
				canHeight = $( "#divAuxModalPdf" ).height();

		
			$.blockUI({ message: $('#modalPdfMipres'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "95%",
				height	: "90%",
				left	: "2.5%",
				top		: '50px',
			} });
			
		
		},'json');
	}
	
	function borrarPdf(pdfMipres)
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'borrarPDF',
			wemp_pmla		: $('#wemp_pmla').val(),
			pdfMipres		: pdfMipres
		}
		, function(data) {
			
			
		
		},'json');
	}
	
	function mostrarModificacionMaestros()
	{
		$.post("CTCmipres.php",
		{
			consultaAjax 	: '',
			accion			: 'pintarModificacionMaestros',
			wemp_pmla		: $('#wemp_pmla').val()
		}
		, function(data) {
			
			$( "#divAuxModalMaestros" ).html( data );
				
			var canWidth = $(window).width()*0.8;
			if( $( "#divAuxModalMaestros" ).width()-50 < canWidth )
				canWidth = $( "#divAuxModalMaestros" ).width();

			var canHeight = $(window).height()*0.8;;
			if( $( "#divAuxModalMaestros" ).height()-50 < canHeight )
				canHeight = $( "#divAuxModalMaestros" ).height();

		
			$.blockUI({ message: $('#modalModificacionMaestros'),
			css: {
				overflow: 'auto',
				cursor	: 'auto',
				width	: "50%",
				height	: "90%",
				left	: "25%",
				top		: '50px',
			} });
			
		
		},'json');
	}
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
	.detalles{
		font-family: verdana;
		font-size: 7pt;
		color: #0033FF;
		font-weight: bold;
		text-decoration: underline;
		cursor:pointer;
	}
	.verPrescripcion{
		font-family: verdana;
		font-weight: bold;
		color:#0033ff;
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
	
	if($proceso!="actualizar")
	{
		$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
		$wbasedato1 = strtolower( $institucion->baseDeDatos );
		
		if ($wemp_pmla == 01 || $wemp_pmla == 10 )
		{
			$logo = $wbasedato1;
		}
		else 
		{
			$logo = "logo_".$wbasedato1;
		}
		// -->	ENCABEZADO
		encabezado("MONITOR MIPRES", $wactualiz, $logo);
		
		$fechaInicialMonitorMipres = consultarAliasPorAplicacion($conex,$wemp_pmla , 'fechaInicioMonitorMipres');
		
		echo "	<input type='hidden' id='wbasedato' value='".$wbasedato."'>
				<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>
				<input type='hidden' id='fechaInicioMonitorMipres' value='".$fechaInicialMonitorMipres."'>";
		
		echo "	<div id='msjEspere' style='display:inline;' align='center'>
				<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...
			</div>";
		
		
		
		
		$fechaAnterior = strtotime ( '-1 day' , strtotime ( $wfecha ) ) ;
		$fechaDiaAnterior = date ( "Y-m-d" , $fechaAnterior );
		
		
		
	//modificacion jaime mejia
	if(!isset($_GET['automatizacion_pdfs'])){		
		$resultadoWebService = consumirWebServicesMipres($wfecha,$wemp_pmla,"on","","","","","","");
		if($resultadoWebService!=false)
		{
			consumirWebServiceNovedades($wfecha,$wemp_pmla);
			consultarPrescripcionesPendientesJM($fechaInicialMonitorMipres,$wfecha,$wemp_pmla);
			
			consultarNovedadesPendientes();
			
			$ultimaFechaKronP = consultarAliasPorAplicacion($conex,$wemp_pmla,'ultimaFechaPrescripcionesKronMipres');
			$fechaKronP = strtotime ( '+1 day' , strtotime ( $ultimaFechaKronP ) ) ;
			$fechaKronP = date ( "Y-m-d" , $fechaKronP );	
			
			$ultimaFechaKronN = consultarAliasPorAplicacion($conex,$wemp_pmla,'ultimaFechaNovedadesKronMipres');
			$fechaKronN = strtotime ( '+1 day' , strtotime ( $ultimaFechaKronN ) ) ;
			$fechaKronN = date ( "Y-m-d" , $fechaKronN );	
			
		
			consumirWebServicesKron($fechaDiaAnterior,$ultimaFechaKronP,$fechaKronP,$ultimaFechaKronN,$fechaKronN,$wemp_pmla);
		}
		else
		{
			echo "<script>jAlert('No se pudo consumir el Web Service del ministerio para la fecha actual','ALERTA')</script>";
		}
	}
		
		
		$filtrosMonitor = pintarFiltros();
		echo $filtrosMonitor;
		
			
		
		
		// Modal mipres
		echo "<div id='dvAuxModalMipres' style='display:none'></div>";
		echo "<div id='divAuxModalPdf' style='display:none'></div>";
		echo "<div id='divAuxModalMaestros' style='display:none'></div>";
		
		echo "<div id='divReporteMipres'></div>";
		
		echo "<p align=center><span><input type='button' value='Cerrar ventana' onclick='cerrarVentana();'></span></p>";
	
		
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
