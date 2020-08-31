<?php
include_once("conex.php");
/**
 *ACTUALIZACIONES
 *2016-04-05 Camilo zapata: se adicionó en la consulta de la tabla 000148 para que tenga en cuenta solo aquellos registros activos accest = on
 *
 *
 *
 *
 * A corregir
 *
 * - El nombre del campo de asegurdaora esta mal escrito en el formulario HTML, seguir revisando el resto de campos
 */
/**********************************************************************************************************************************************
 * 105 Esta la selección de Zona (Rural, Urbana)
 *
 *	Maestros usados
 * 	==================================================================================================================================
 *	Valores posibles	Matrix	COD		Nombre tabla matrix					Tabla en unix
 *	O/P/C/I				000105	18		TIPO DE OCUPANTES					SON VALORES FIJOS Y NO SE PUEDEN CAMBIAR
 *	A/N/F/V				000105	19		ESTADO DE ASEGURAMIENTO				SON VALORES FIJOS Y NO SE PUEDEN CAMBIAR
 *	PUBLICO, OFICIAL..	000147			TIPOS DE VEHICULO					DETALLE DE CAMPOS CONSTANTES
 *
 *	Nota:
 *		-	Los valores posibles son los datos pasado inicialmente al hacer el programa
 *		-	La tabla 105 es un maestro de select COD se refiere al código del select que se desea consultar
 **********************************************************************************************************************************************/

/**********************************************************************************************************************************************
 *
 *	TABLAS USADAS
 * 	==================================================================================================================================
 *	Matrix			Nombre tabla matrix					Tabla en unix
 *	000148			ACCIDENTES DE TRANSITO				INACCDET (DETALLE DEL ACCIDENTE DE TRANSITO)
 *														INACCPRO
 *														INACCOBS
 **********************************************************************************************************************************************/

/***************************************************************************************************************************************
 * AJAX
 ***************************************************************************************************************************************/
if( !empty( $accion ) ){






	switch( $accion ){

		case 'guardarAccidentes':
			$json = guardarAccidentes( $acc_Dathis, $acc_Dating );

			return json_encode( $json );
		break;

		case 'cargarAccidentesPaciente':
			$json = cargarAccidentesPaciente( $tipodoc, $documento );
			echo json_encode( $json );
		break;
		case 'cargarEntidadFosyga':
			$json = Array("error" => 0,"html" => "","data" => "","mensaje" => "");
			/*$q = " SELECT Detval
					 FROM root_000051
					WHERE Detemp = '".$wemp_pmla."'
					  AND Detapl = 'entidadfosyga'";*/

			$q= " SELECT Detval,Empnom
					FROM ".$wbasedato."_000024, ".$wbasedato."_000193 left join root_000051 ON (Asecod=Detval)
					WHERE Detemp = '".$wemp_pmla."'
					AND Empcod=Asecoe
					AND Detapl = 'entidadfosyga'";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			if ($num > 0){
				$rs = mysql_fetch_array($res);
				$json['html'] = $rs['Detval'];
				$json['des'] = $rs['Empnom'];
			}else{
				$json['error'] = 1;
				$json['mensaje'] = "No existe una entidad que administre FOSYGA, favor comunicarse con Informatica.";
			}
			echo json_encode( $json );
		break;
		case 'calcularVigencia':
			$json = array();
			if( $fechaInicio != "" ){
				$fechaFinal = strtotime($fechaInicio."+ 1 year")-(3600*24);
				$fechaFinal = date('Y-m-d', $fechaFinal);
				$json['fechaInicio'] = $fechaInicio;
				$json['fechaFinal']  = $fechaFinal;
			}

			if( $fechaFinal != ""){
				$fechaInicio = strtotime($fechaFinal."- 1 year")+(3600*24);
				$fechaInicio = date('Y-m-d', $fechaInicio);
				$json['fechaFinal'] = $fechaFinal;
				$json['fechaInicio']  = $fechaInicio;
			}
			echo json_encode( $json );
		break;
		default: break;
	}

	exit;
}
/***************************************************************************************************************************************
 * FIN DEL AJAX
 ***************************************************************************************************************************************/

/***************************************************************************************************************************************
 * FUNCIONES
 ***************************************************************************************************************************************/

/************************************************************************************************************
 * Consulta los tipos de vehículos y devuelve el res correspondiente
 ************************************************************************************************************/
function consultarTiposVehiculoAcc( $cod ){

	global $conex;
	global $wbasedatos;

	$val = false;

	$sql = "SELECT
				*
			FROM
				{$wbasedatos}_000147
			WHERE
				Tipcod LIKE '$cod'
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );

	return $res;
}

function cargarAccidentesPaciente( $tipodoc, $documento ){
	global $conex;
	global $wbasedato;

	$data = Array("error" => 0,"html" => "","data" => "","mensaje" => "");
	$arr_accidentes = array();
	//El Accrei es el campo que indica que fue un reingreso de accidente y ese campo tiene el NUMERO DE INGRESO (HIS-ING)
	//Del cual fue reingreso.

	//Consulto si no existe el accidente de transito para proceder a guardar los datos
	$sql = "SELECT Acchis as his, Accing as ing, Accfec as fec, c.nombre as mun, Accmar as mar, Accpla as pla, Accrei as rei
			  FROM {$wbasedato}_000148 a INNER JOIN {$wbasedato}_000100 ON (Pachis=Acchis) LEFT JOIN root_000006 c ON (Accmun=c.codigo)
			 WHERE Pactdo = '".$tipodoc."'
			   AND Pacdoc = '".$documento."'
			   AND accest = 'on'
			   ORDER BY Acchis , Accing*1 ";

	$res = mysql_query( $sql, $conex ) or ( $data[ 'mensaje' ] = utf8_encode( mysql_errno()." - Error en el query $sql - ".mysql_error() ) );

	if( $res ){
		$num = mysql_num_rows( $res );
		if( $num > 0  ){

			while($rows = mysql_fetch_array( $res ) ){
				$rows['ing_mostrar'] = $rows['ing'];

				$existeEnReingresos = false;
				foreach( $arr_accidentes as $keyA=>$datoA ){
					if( in_array($rows['rei'], $datoA['reingresos']) ){
						$existeEnReingresos = true;
						$rows['rei'] = $keyA;
					}
				}
				//Si ha sido un REIngreso,
				if( $rows['rei'] != "" && ( array_key_exists($rows['rei'], $arr_accidentes) ||  $existeEnReingresos == true )){
					$arr_accidentes[$rows['rei']]['ing_mostrar'].= ", ".$rows['ing'];
					$arr_accidentes[$rows['rei']]['ing'] = $rows['ing']; //SIEMPRE VA EL ULTIMO REINGRESO DEL ACCIDENTE PARA QUE COJA EL SALDO DE ELLOS
					array_push($arr_accidentes[$rows['rei']]['reingresos'], $rows['ing']);
				}else{
					$rows['reingresos'] = array();
					array_push($rows['reingresos'],$rows['ing']);
					$arr_accidentes[$rows['ing']] = $rows;
				}
			}

			$data['html'] .= "<table id='tabla_lista_accidentes'>";
			$data['html'] .= "<tr class='encabezadotabla'><td colspan=6 align='center' id='titulo_lista_accidentes'><font size=5 color='yellow'>LISTA DE ACCIDENTES ANTERIORES</font></td></tr>";
			$data['html'] .= "<tr class='encabezadotabla'>";
			$data['html'] .= "<td>Fecha Accidente</td>";
			$data['html'] .= "<td>Historia - Ingreso(s)</td>";
			$data['html'] .= "<td>Municipio</td>";
			$data['html'] .= "<td>Marca vehiculo</td>";
			$data['html'] .= "<td>Placa vehiculo</td>";
			$data['html'] .= "<td>Seleccionar</td>";
			$data['html'] .= "</tr>";

			foreach( $arr_accidentes as $key=>$dato ){
				$data['html'] .= "<tr class='fila1'>";
				$data['html'] .= "<td align='center'>".$dato['fec']."</td>";
				$data['html'] .= "<td align='center'>".$dato['his']." - ".$dato['ing_mostrar']."</td>";
				$data['html'] .= "<td align='center'>".$dato['mun']."</td>";
				$data['html'] .= "<td align='center'>".$dato['mar']."</td>";
				$data['html'] .= "<td align='center'>".$dato['pla']."</td>";
				$data['html'] .= "<td align='center'><input type='radio' name='sel_acc_prev' onclick='cargarDatosAccidente(\"".$dato['his']."\",\"".$dato['ing']."\")'></td>";
				$data['html'] .= "</tr>";
			}

			$data['html'] .= "<tr class='fila1'><td align='center' colspan='6'>
								<input type='button' onclick='nuevoAccidenteTransito();' value='Nuevo Accidente' style='width:150px;'></td></tr>";
			$data['html'] .= "</table><br>";
			$data['html'] .= "<input type='hidden' id='cambioConsorcio' name='cambioConsorcio' value='off'>";
			$data['html'] .= "<script>";
			$data['html'] .= "$('#titulo_lista_accidentes').effect('pulsate', {}, 20000);";
			$data['html'] .= "</script>";
			/*Como la admision por accidente de transito es un REINGRESO
			de un accidente previo, este campo oculto contendra el ultimo ingreso del cual se copia el accidente
			ESA VARIABLE SE UTILIZA PARA COPIAR EL SALDO RESULTADO DE LOS REINGRESOS POR ACCIDENTE PARA EL TOPE NUEVO*/
			//$data['html'] .= "<input type='hidden' name='accidente_previo' id='accidente_previo' value=''>";
		}
	}
	else{
		$data[ 'error' ] = 1;
	}
	return $data;
}
/***************************************************************************************************************************************
 * FIN DE FUNCIONES
 ***************************************************************************************************************************************/

echo "<div id='accidentesTransito' style='display:none'>";
echo "<form id='frAcc'>";

echo "<div id='hiOcultos'>";
echo "<INPUT type='hidden' name='dat_Acchis' value=''>";
echo "<INPUT type='hidden' name='dat_Accing' value=''>";

echo "</div>";

echo "<div id='infDatosAcc'>";

/************************************************************************************
 * DATOS DEL SITIO DONDE OCURRIO EL ACCIDENTE
 ************************************************************************************/
echo "<div>";
echo "<div>";

echo "<br>";
echo "<center>";
echo "<table>";

echo "<tr class='fila1' style='font-weight:bold'>";
echo "<td>Historia</td>";
echo "<td>Identifiaci&oacute;n</td>";
echo "<td>Paciente</td>";
echo "<td>Fecha de nacimiento</td>";
echo "<td>Direcci&oacute;n</td>";
echo "</tr>";

echo "<tr class='fila2'>";
echo "<td name='historia'> &nbsp;</td>";
echo "<td name='identificacion'></td>";
echo "<td name='nombre'></td>";
echo "<td name='fecNac'></td>";
echo "<td name='direccion'></td>";
echo "</tr>";

echo "</table>";
echo "</center>";

echo "</div>";
echo "</div>";
/************************************************************************************/

echo "<br><center><div id='lista_accidentes'></div><input type='hidden' name='accidente_previo' id='accidente_previo' value=''></center>";
/************************************************************************************
 * DATOS DEL SITIO DONDE OCURRIO EL ACCIDENTE
 ************************************************************************************/
echo "<div acordeon>";
echo "<h3>DATOS DEL SITIO DONDE OCURRIO EL ACCIDENTE</h3>";
echo "<div style='heigth:100%'>";

echo "<center>";
echo "<table>";


echo "<tr class='fila2'>";

/********************************************************************************
 * Condición del accidentado
 ********************************************************************************/
echo "<th class='fila1'>Condición del paciente</th>";
echo "<td>";
$res = consultaMaestros( "000105", "Selcod, Seldes", "Seltip = '18'", "", "", 0 );
crearSelectHTMLAcc(  $res, "", "dat_Acccon_ux_accocu", "style='width:100%' msgError" );
echo "</td>";
/********************************************************************************/

/********************************************************************************
 * Fecha y hora del accidente
 ********************************************************************************/
echo "<th class='fila1'>Fecha y hora del accidente</th>";

echo "<td colspan='2'>";
echo "<INPUT type='text' id='dat_Accfec' name='dat_Accfec_ux_accfec' style='width:100;' fecha msgError='YYYY-MM-DD'></textarea>";
echo "<INPUT type='text' id='dat_Acchor' name='dat_Acchor_ux_acchor' style='width:90;' hora msgError='HH:MM:SS'>";
echo "</td>";
/********************************************************************************/

echo "</tr>";


echo "<tr class='fila1' align='center'>";
echo "<th>Departamento</th>";
echo "<th>Municipio</th>";
echo "<th>Direcci&oacute;n del acc.</th>";
echo "<th>Detalle de la direcci&oacute;n</th>";
echo "<th>Zona</td>";
echo "</tr>";

echo "<tr class='fila2'>";

/********************************************************************************
 * Departamento
 ********************************************************************************/
echo "<td>";
echo "<INPUT type='text' id='Accdep' name='Accdep' msgError='Ingrese el departamento'>";
echo "<INPUT type='hidden' id='dat_Accdep' name='dat_Accdep'>";
echo "</td>";
/********************************************************************************/

/********************************************************************************
 * Municipio
 ********************************************************************************/
echo "<td>";
echo "<INPUT type='text' id='Accmun' name='Accmun' srcDep='dat_Accdep' msgError='Ingrese el municipio'>";
echo "<INPUT type='hidden' id='dat_Accmun' name='dat_Accmun_ux_accmun'>";
echo "</td>";
/********************************************************************************/

/********************************************************************************
 * Dirección de la residencia
 ********************************************************************************/
echo "<td>";
echo "<INPUT type='text' id='dat_Accdir' name='dat_Accdir_ux_acclug_ux_urglug' msgError='Dirección' depend='dat_Accdtd'>";
echo "</td>";
/********************************************************************************/

/********************************************************************************
 * Detalle de la dirección
 ********************************************************************************/
echo "<td>";
echo "<textarea id='dat_Accdtd' name='dat_Accdtd_ux_acclug_ux_urglug' msgError='Detalle de la dirección' depend='dat_Accdir'></textarea>";
echo "</td>";
/********************************************************************************/

/********************************************************************************
 * Zona
 ********************************************************************************/
echo "<td>";
$res = consultaMaestros( "000105", "Selcod, Seldes", "Seltip = '05'", "", "", 0 );
crearSelectHTMLAcc(  $res, "dat_Acczon", "dat_Acczon_ux_acczon", "msgError" );
echo "</td>";
/********************************************************************************/

/********************************************************************************
 * Descripción del accidente
 ********************************************************************************/
echo "</tr>";

echo "<tr>";
echo "<th class='fila1' align='left'>";
echo "Descripci&oacute;n breve<br>del accidente";
echo "</th>";
//echo "<tr>";
echo "<td class='fila2' colspan='6'>";
echo "<textarea id='dat_Accdes' name='dat_Accdes' style='width:100%' msgError='Descripción breve del accidente'></textarea>";
echo "</td>";
echo "</tr>";

echo "</table>";
echo "</center>";

echo "</div>";
echo "</div>";
/********************************************************************************/



/******************************************************************************************
 * DATOS DEL VEHICULO DEL ACCIDENTE DE TRANSITO
 ******************************************************************************************/
echo "<div acordeon>";
echo "<h3 class='fila1'>DATOS DEL VEHICULO</h3>";
echo "<div>";


echo "<center>";

//$cambioConsorcio .= "<img size='5' id='img_cambio_consorcio' src='../../images/medical/root/Advertencia.png' title='title en un imagen no sé si funciona' onclick='validarEstadoAseguramiento( $(this).parent().parent().next(\"tr\").next(\"select\").find(\"option:selected\") );' >";
$cambioConsorcio .= "<div id='div_ima_cambio_consorcio' style='display:none;'><img size='5' id='img_cambio_consorcio' style='' src='../../images/medical/root/Advertencia.png' title='ESTA ENTIDAD NO EXISTE, CLICK PARA CAMBIAR' onclick='validarEstadoAseguramiento( $(\"select[name=dat_Accase_ux_accase] > option:selected\") );' ></div>";
echo "<table id='tabla_datos_vehiculo'>";

echo "<tr class='fila1'>";
echo "<th rowspan='2'>Estado de<br>aseguramiento</th>";
echo "<th rowspan='2'>Marca</th>";
echo "<th rowspan='2'>Placa</th>";
echo "<th rowspan='2'>Tipo de<br>servicio</th>";
echo "<th rowspan='2'>C&oacute;digo<br>de aseguradora $cambioConsorcio </th>";
echo "<th rowspan='2'>N&uacute;mero de p&oacute;liza</th>";
echo "<th colspan='2'>Vigencia</th>";
echo "</tr>";
echo "<tr class='fila1'>";
echo "<th>Fecha inicio</th>";
echo "<th>Fecha final</th>";
echo "</tr>";

echo "<tr class='fila2'>";
/***************************************************************************************************
 * Estado de aseguramiento
 ***************************************************************************************************/
echo "<td>";
$res = consultaMaestros( "000105", "Selcod, Seldes", "Seltip = '19'", "", "", 0 );
crearSelectHTMLAcc(  $res, "", "dat_Accase_ux_accase", "onchange='validarEstadoAseguramiento(this);' style='width:100%' msgError" );
echo "</td>";
/***************************************************************************************************/


/***************************************************************************************************
 * Marca
 ***************************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accmar_ux_accmar' msgError='Marca del vehículo'>";
echo "</td>";
/***************************************************************************************************/

/***************************************************************************************************
 * Placa
 ***************************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accpla_ux_accpla' style='width:65' msgError='Placa del vehículo'>";
echo "</td>";
/***************************************************************************************************/

/***************************************************************************************************
 * Tipo de servicio
 ***************************************************************************************************/
echo "<td>";
$res = consultaMaestros( "000162", "Tipcod, Tipdes as des", "Tipest = 'on'", "", "", 0 );
crearSelectHTMLAcc(  $res, "", "dat_Acctse_ux_acctip", "msgError" );
echo "</td>";
/***************************************************************************************************/

/***************************************************************************************************
 * Código de la aseguradora
 ***************************************************************************************************/
//echo "<td>";
echo "<td>";
echo "<INPUT type='text' name='_ux_accasn' msgError='Código de aseguradora'>";
echo "<INPUT type='hidden' name='dat_Acccas_ux_acccas'>";
echo "</td>";
/***************************************************************************************************/

/***************************************************************************************************
 * Póliza
 ***************************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accpol_ux_accpol' msgError='Número de póliza'>";
echo "</td>";
/***************************************************************************************************/


/***************************************************************************************************
 * Vigencia
 ***************************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accvfi_ux_accfin' style='width:80;' fecha msgError='YYYY-MM-DD'>";
echo "</td>";
echo "<td>";
echo "<INPUT type='text' name='dat_Accvff_ux_accffi' style='width:80;' fecha msgError='YYYY-MM-DD'>";
echo "</td>";
/***************************************************************************************************/

echo "</tr>";

echo "<tr class='fila1'>";
echo "<th colspan='2' align='left'>Hubo intervenci&oacute;n de la autoridad?</th>";
echo "<td align='left' class='fila2' colspan='6'>";
// echo "<INPUT type='checkbox' name='dat_Accaut_ux_accaut'>";

echo "<table>";
echo "<tr style='font-size:10pt;'>";
echo "<td style='width:15'>";
echo "S&iacute;";
echo "</td>";
echo "<td style='width:35'>";
echo "<INPUT type='radio' name='dat_Accaut_ux_accaut' value='on' style='width:10' msgError>";
echo "</td>";
echo "<td style='width:15'>";
echo "No";
echo "</td>";
echo "<td style='width:35'>";
echo "<INPUT type='radio' name='dat_Accaut_ux_accaut' value='off' style='width:35' msgError>";
echo "</td>";
echo "</tr>";
echo "</table>";

echo "</tr>";


echo "<tr class='fila1'>";
echo "<th colspan='2' align='left'>Se cobro excedente de p&oacute;liza?</th>";
echo "<td align='left' class='fila2' colspan='6'>";
// echo "<INPUT type='checkbox' name='dat_Acccep'>";


echo "<table>";
echo "<tr style='font-size:10pt;'>";
echo "<td style='width:15'>";
echo "S&iacute;";
echo "</td>";
echo "<td  style='width:35'>";
echo "<INPUT type='radio' name='dat_Acccep' value='on' style='width:10' msgError>";
echo "</td>";
echo "<td style='width:15'>";
echo "No";
echo "</td>";
echo "<td style='width:35'>";
echo "<INPUT type='radio' name='dat_Acccep' value='off' style='width:35' msgError>";
echo "</td>";
echo "</tr>";
echo "</table>";


echo "</td>";
echo "</tr>";

echo "</table>";

echo "</center>";


echo "</div>";
echo "</div>";
/******************************************************************************************/


/******************************************************************************************************************************
 * DATOS DEL PROPIETARIO DEL ACCIDENTE
 ******************************************************************************************************************************/
echo "<div acordeon>";
echo "<h3>DATOS DEL PROPIETARIO</h3>";
echo "<div>";


echo "<center>";

echo "<table id='tabla_datos_propietario'>";

echo "<tr class='fila1' titulo='propietario_pn'>";

echo "<th style='width:150;'>Tipo de documento</th>";
echo "<th style='width:150;'>Nro de identificaci&oacute;n</th>";
echo "<th style='width:150;'>Apellido 1</th>";
echo "<th style='width:150;'>Apellido 2</th>";
echo "<th style='width:150;'>Nombre 1</th>";
echo "<th style='width:150;'>Nombre 2</th>";

echo "</tr>";

echo "<tr class='fila1' titulo='propietario_nit' style='display:none;'>";

echo "<th style='width:150;'>Tipo de documento</th>";
echo "<th style='width:150;'>Nro de identificaci&oacute;n</th>";
echo "<th style='width:600;' colspan='4'>Nombre entidad</th>";

echo "</tr>";


echo "<tr class='fila2'>";

/************************************************************************************
 * Tipo de identificación del propietario
 ************************************************************************************/
echo "<td>";
$res = consultaMaestros( "root_000007", "Codigo, Descripcion", "Estado = 'on'", "", "" );
crearSelectHTMLAcc(  $res, "", "dat_Acctid_ux_acptid", "style='width:100%' msgError onchange='validarObligatorios(this)' aplicanit='si'" );
// echo "<INPUT type='text' name='dat_Acctid_ux_acptid' style='width:100%;'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Número de identificación del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accnid_ux_acpide' style='width:100%;' numerico msgError='Nro de identificación'  onBlur='validarCamposCedResAccPropietario();' aplicanit='si'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Apellido 1 del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accap1_ux_acpap1' style='width:100%;' msgError='Primer apellido' aplicanit='si'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Apellido 2 del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accap2_ux_acpap2' style='width:100%;' placeholder='Segundo apellido' aplicanit='no'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Nombre 1 del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accno1_ux_acpno1' style='width:100%;' msgError='Primer Nombre' aplicanit='no'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Nombre 2 del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accno2_ux_acpno2' style='width:100%;' placeholder='Segundo Nombre' aplicanit='no'>";
echo "</td>";
/************************************************************************************/

echo "</tr>";

echo "<tr class='fila1'>";

echo "<th>Direcci&oacute;n</th>";
echo "<th colspan='2'>Detalle de la direcci&oacute;n</th>";
echo "<th>Departamento</th>";
echo "<th>Municipio</th>";
echo "<th>Tel&eacute;fono fijo</th>";

echo "</tr>";

echo "<tr class='fila2'>";

/************************************************************************************
 * Dirección del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' id='dat_Accpdi_ux_acpdir' name='dat_Accpdi_ux_acpdir' style='width:100%;' msgError='Dirección del propietario' depend='dat_Accpdd_ux_acpdir'>";
echo "</td>";
/************************************************************************************/

 /************************************************************************************
 * Detalle de la Dirección del propietario
 ************************************************************************************/
echo "<td colspan='2'>";
echo "<textarea id='dat_Accpdd_ux_acpdir' name='dat_Accpdd_ux_acpdir' style='width:100%;' msgError='Detalle de la dirección del propietario' depend='dat_Accpdi_ux_acpdir'></textarea>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Departamento del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='AccDepPropietario' id='AccDepPropietario' style='width:100%;' msgError='Departamento del propietario'>";
echo "<INPUT type='hidden' name='dat_Accpdp_ux_acpdep' id='dat_Accpdp_ux_acpdep'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Municipio del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='AccMunPropietario' id='AccMunPropietario' style='width:100%;' srcDep='dat_Accpdp_ux_acpdep' msgError='Municipio del propietario'>";
echo "<INPUT type='hidden' name='dat_Accpmn_ux_acpmun' id='dat_Accpmn_ux_acpmun'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Teléfono del propietario
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Acctel_ux_acptel' style='width:100%;' msgError='Teléfono del propietario'>";
echo "</td>";
/************************************************************************************/

echo "</tr>";

echo "</table>";

echo "</center>";

echo "</div>";
echo "</div>";
/******************************************************************************************************************************/


/******************************************************************************************************************************
 * DATOS DEL CONDUCTRO DEL VEHICULO INVOLUCRADO DEL ACCIDENTE
 ******************************************************************************************************************************/
echo "<div acordeon>";
echo "<h3>DATOS DEL CONDUCTOR</h3>";
echo "<div>";


echo "<center>";

echo "<table id='tabla_datos_conductor'>";

echo "<tr class='fila1'>";

echo "<th style='width:150;'>Tipo de documento</th>";
echo "<th style='width:150;'>Nro de identificaci&oacute;n</th>";
echo "<th style='width:150;'>Apellido 1</th>";
echo "<th style='width:150;'>Apellido 2</th>";
echo "<th style='width:150;'>Nombre 1</th>";
echo "<th style='width:150;'>Nombre 2</th>";

echo "</tr>";

echo "<tr class='fila2'>";

/************************************************************************************
 * Tipo de identificación del conductor
 ************************************************************************************/
echo "<td>";
$res = consultaMaestros( "root_000007", "Codigo, Descripcion", "Estado = 'on'", "", "" );
crearSelectHTMLAcc(  $res, "", "dat_Acccti_ux_acptic", "style='width:100%'  msgError" );
echo "</td>";
/************************************************************************************/


/************************************************************************************
 * Número de identificación del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Acccni_ux_acpid2_ux_accced' numerico msgError='Idetificación del conductor' onblur='validarCamposCedResAccConductor();'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Apellido 1 del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accca1_ux_acpac1' style='width:100%;' msgError='Primer apellido del conductor'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Apellido 2 del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accca2_ux_acpac2' style='width:100%;' placeholder='Segundo Apellido del conductor'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Nombre 1 del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Acccn1_ux_acpnc1' style='width:100%;' msgError='Primer nombre del conductor'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Nombre 2 del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Acccn2_ux_acpnc2' style='width:100%;' placeholder='Segundo nombre del conductor'>";
echo "</td>";
/************************************************************************************/


echo "</tr>";

echo "<tr class='fila1'>";

echo "<th>Direcci&oacute;n</th>";
echo "<th colspan='2'>Detalle de la direcci&oacute;n</th>";
echo "<th>Departamento</th>";
echo "<th>Municipio</th>";
echo "<th>Tel&eacute;fono fijo</th>";

echo "</tr>";

echo "<tr class='fila2'>";

/************************************************************************************
 * Dirección del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' id='dat_Acccdi' name='dat_Acccdi_ux_accdir' style='width:100%;' msgError='Dirección del conductor' depend='dat_Acccdd'>";
echo "</td>";
/************************************************************************************/

 /************************************************************************************
 * Detalle de la Dirección del conductor
 ************************************************************************************/
echo "<td colspan='2'>";
echo "<textarea id='dat_Acccdd' name='dat_Acccdd_ux_accdir' style='width:100%;' msgError='Detalle de la dirección del conductor' depend='dat_Acccdi'></textarea>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Departamento del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='AccConductordp' id='AccConductordp' style='width:100%;' msgError='Departamento del conductor'>";
echo "<INPUT type='hidden' name='dat_Acccdp' id='dat_Acccdp'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Municipio del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='AccConductorMun' id='AccConductorMun' style='width:100%;' srcDep='dat_Acccdp'  msgError='Municipio del conductor'>";
echo "<INPUT type='hidden' name='dat_Acccmn_ux_accmuc' name='dat_Acccmn_ux_accmuc'>";
echo "</td>";
/************************************************************************************/

/************************************************************************************
 * Teléfono del conductor
 ************************************************************************************/
echo "<td>";
echo "<INPUT type='text' name='dat_Accctl_ux_acctel' style='width:100%;' msgError='Telefono del conductor'>";
echo "</td>";
/************************************************************************************/

echo "</tr>";

echo "</table>";

echo "</center>";



echo "</div>";
echo "</div>";

echo "</div>";	//Fin de infDatosAcc

/******************************************************************************************
 * Center
 ******************************************************************************************/
echo "<center class='fondoamarillo'>";
echo "<table>";
echo "<tr>";
echo "<td>";
echo "<INPUT type='button' value='Guardar' onClick='guardarAccidentes();' style='width:100'>";
// echo "<INPUT type='button' value='reset' onClick='resetearAccidentes();' style='width:100'>";
echo "</td>";
echo "<td>";
echo "<INPUT type='button' value='Salir sin guardar' onClick='cerrarAccidentes();' style='width:150'>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</center>";
/******************************************************************************************/
echo "</form>";
echo "</div>";	//Esto cierra todo el div que contiene la información de accidentes de transito
/*******************************************************************************************************************************/
?>