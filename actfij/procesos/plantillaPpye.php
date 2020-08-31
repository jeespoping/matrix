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
			$wactualiz='';
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
	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("actfij/funciones_activosFijos.php");

	$conex 		= obtenerConexionBD("matrix");
	$wbasedato 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activosFijos');
	$wfecha		= date("Y-m-d");
    $whora 		= date("H:i:s");
//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------
	//	--> Actualizar campos de un formulario en cero
	//-------------------------------------------------------------------
	function inicializarCamposEnCero($formulario, $idReg)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$camposEnCero = camposQueInicianEnCero($formulario);

		foreach($camposEnCero as $campo => $valor)
		{
			$sqlUpdate = "UPDATE ".$wbasedato."_".$formulario."
							 SET ".$campo." = '0'
						   WHERE id = '".$idReg."'
						     AND (".$campo." = '' OR ".$campo." IS NULL)
			";
			mysql_query($sqlUpdate, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdate):</b><br>".mysql_error());
		}
	}

	//-------------------------------------------------------------------
	//	--> Obtener cuales campos inician en cero para un nuevo activo
	//-------------------------------------------------------------------
	function camposQueInicianEnCero($formulario)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$camposCero = array();

		$sqlCamposCero = "SELECT Percam
							FROM ".$wbasedato."_000019
						   WHERE Permed = '".$wbasedato."'
						     AND Percod = '".$formulario."'
							 AND Pericn = 'on'
		";
		$resAct = mysql_query($sqlCamposCero, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamposCero):</b><br>".mysql_error());
		while($rowAct = mysql_fetch_array($resAct))
			$camposCero[$rowAct['Percam']] = '';

		return $camposCero;
	}


	//-------------------------------------------------------------------
	//	--> Obtiene si el activo se encuentra registrado
	//-------------------------------------------------------------------
	function activoRegistrado($registroActivo, $periodoAno, $periodoMes)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$actRegistrado = 'off';

		if($registroActivo != 'nuevo')
		{
			/*$sqlReg = "SELECT Actact
						 FROM ".$wbasedato."_000001
						WHERE Actano = '".$periodoAno."'
						  AND Actmes = '".$periodoMes."'
						  AND Actreg = '".$registroActivo."'
			";*/
			$sqlReg = "SELECT Actact
						 FROM ".$wbasedato."_000001
						WHERE Actreg = '".$registroActivo."'
						  AND Actact = 'on'
			";
			$resReg = mysql_query($sqlReg, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlReg):</b><br>".mysql_error());
			if(mysql_num_rows($resReg) > 0)
				$actRegistrado = 'on';
		}
		// --> Hidden para saber si el activo se encuentra registrado
		echo "<input type='hidden' id='activoRegistrado' value='".$actRegistrado."'>";
	}

	//-------------------------------------------------------------------
	//	--> Formulario de informacion general y de control
	//-------------------------------------------------------------------
	function formularioInfoGeneral($registroActivo='nuevo', $periodoVer)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$wbasedatoFac 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		$arrPeriodoVer	= explode('-', $periodoVer);
		$periodoAno		= $arrPeriodoVer[0];
		$periodoMes		= $arrPeriodoVer[1];

		if($registroActivo != 'nuevo')
		{
			// --> Consultar activo
			$sqlAct = "SELECT A.*, Modnom, Grunom, Subnom, Estret
						 FROM ".$wbasedato."_000001 AS A LEFT JOIN ".$wbasedato."_000004 AS B ON A.Actest = B.Estcod,
							  ".$wbasedato."_000007, ".$wbasedato."_000008, ".$wbasedato."_000009
						WHERE Actano = '".$periodoAno."'
						  AND Actmes = '".$periodoMes."'
						  AND Actreg = '".$registroActivo."'
						  AND Actmoa = Modcod
						  AND Actgru = Grucod
						  AND Actsub = Subcod
						  AND Actgru = Subgru
			";
			$resAct = mysql_query($sqlAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAct):</b><br>".mysql_error());
			if($rowAct = mysql_fetch_array($resAct))
			{
				$infoActivo = $rowAct;
				// --> Codificar tildes y caracteres especiales
				foreach($infoActivo as $indice => &$valor)
					$valor = utf8_encode($valor);
			}
			else
				$camposEnCero = camposQueInicianEnCero("000001");
		}
		else
			$camposEnCero = camposQueInicianEnCero("000001");

		activoRegistrado($registroActivo, $periodoAno, $periodoMes);

		// --> Obtener array con los campos que puede editar el usuario
		$camposPermisoEditar = permisosParaEditarCampos();
		$perIngresarActivo	 = ((isset($camposPermisoEditar['actfij']['000001']['Actact'])) ? TRUE : FALSE);
		$camposPermisoEditar = ((isset($camposPermisoEditar['actfij']['000001'])) ? $camposPermisoEditar['actfij']['000001'] : array() );

		// --> Consultar compañias(Empresas)
		$arrayEmpresas = array();
		$sqlEmpresas = "SELECT Empcod, Empdes
						  FROM root_000050
						 WHERE Empest = 'on'
		";
		$resEmpresas = mysql_query($sqlEmpresas, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEmpresas):</b><br>".mysql_error());
		while($rowEmpresas = mysql_fetch_array($resEmpresas))
			$arrayEmpresas[$rowEmpresas['Empcod']] = $rowEmpresas['Empdes'];

		// --> Consultar proveedores
		$arrayProveedores = array();
		$sqlProveedores = "SELECT Pronit, Pronom
							 FROM ".$wbasedatoFac."_000006
							WHERE Proest = 'on'
		";
		$resProveedores = mysql_query($sqlProveedores, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlProveedores):</b><br>".mysql_error());
		while($rowProveedores = mysql_fetch_array($resProveedores))
			$arrayProveedores[$rowProveedores['Pronit']] = $rowProveedores['Pronom'];

		// --> Consultar estados
		$arrayEstados = array();
		$sqlEstados = "SELECT Estcod, Estnom, Estret
						 FROM ".$wbasedato."_000004
						WHERE Estest = 'on'
		";
		$resEstados = mysql_query($sqlEstados, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
		while($rowEstados = mysql_fetch_array($resEstados))
		{
			$arrayEstados[$rowEstados['Estcod']]['Nombre'] 		= $rowEstados['Estnom'];
			$arrayEstados[$rowEstados['Estcod']]['deRetiro'] 	= $rowEstados['Estret'];
		}

		// --> Consultar maestro de centros de costos
		$arrayCco = array();
		$sqlCenCost = "SELECT Ccocod, Ccodes
						 FROM ".$wbasedatoFac."_000003
						WHERE Ccoest = 'on'
		";
		$resCenCost = mysql_query($sqlCenCost, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCenCost):</b><br>".mysql_error());
		while($rowCencost = mysql_fetch_array($resCenCost))
			$arrayCco[$rowCencost['Ccocod']] = utf8_encode($rowCencost['Ccodes']);

		// --> Array de centros de costos del activo
		$arrayCcoAct = array();

		if(isset($infoActivo))
		{
			$sqlCcoAct = "SELECT Ccacco, Ccodes, Ccapor, Ccaest
							FROM ".$wbasedato."_000017, ".$wbasedatoFac."_000003
						   WHERE Ccareg = ".$infoActivo['Actreg']."
						     AND Ccaano = '".$periodoAno."'
						     AND Ccames = '".$periodoMes."'
							 AND Ccacco = Ccocod
			";
			$resCcoAct = mysql_query($sqlCcoAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoAct):</b><br>".mysql_error());
			while($rowCcoAct = mysql_fetch_array($resCcoAct))
				$arrayCcoAct[$rowCcoAct['Ccacco']] = $rowCcoAct;
		}

		// --> Consultar aseguradoras
		$arrayAseguradoras = array();
		$sqlAseguradoras = "SELECT Asenit, Asenom
							  FROM ".$wbasedato."_000006
							 WHERE Aseest = 'on'
		";
		$resAseguradoras = mysql_query($sqlAseguradoras, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAseguradoras):</b><br>".mysql_error());
		while($rowAseguradoras = mysql_fetch_array($resAseguradoras))
			$arrayAseguradoras[$rowAseguradoras['Asenit']] = utf8_encode($rowAseguradoras['Asenom']);

		// --> Tooltip para mostrar información del registro
		$infoRegistro = '
		<table style="font-weight:normal">
			<tr class=encabezadoTabla style="font-size: 8pt;font-weight:normal" align=center ><td>Modalidad</td><td>Grupo</td><td>Subgrupo</td></tr>
			<tr align=center style="background:#FCEAED;font-size: 8pt"><td>'.$infoActivo['Actmoa'].'-'.$infoActivo['Modnom'].'</td><td>'.$infoActivo['Actgru'].'-'.$infoActivo['Grunom'].'</td><td>'.$infoActivo['Actsub'].'-'.$infoActivo['Subnom'].'</td></tr>
		</table>';

		echo "
		".convenciones($registroActivo, $periodoVer, $perIngresarActivo)."
		<fieldset align='center' id='' style='padding:15px;'>
			<legend class='fieldset'>".((isset($infoActivo ) ? $infoActivo['Actnom'] : 'Nuevo'))."</legend>
			<table width='100%' id='tablaInfoGeneral'>
				<tr>
					<td class='fila1' width='10%'>Compañia:</td>
					<td class='fila2' width='15%'>
						<select tipo='obligatorio' id='igCompañia' style='width:170px;font-size: 8pt;' ".((!array_key_exists('Actemp', $camposPermisoEditar)) ? 'disabled' : '').">
							<option value=''>Seleccione...</option>";
							foreach($arrayEmpresas as $codEmp => $nomEmp)
								echo "<option value='".$codEmp."' ".((isset($infoActivo) && $infoActivo['Actemp'] == $codEmp) ? "SELECTED" : "").">".$nomEmp."</option>";
		echo "			</select>
					</td>
					<td class='fila1' width='10%'>Número de placa:</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='igPlaca' 	".((isset($infoActivo)) ? "value='".$infoActivo['Actpla']."'" : "".((isset($camposEnCero['Actpla'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Actpla', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1' width='10%'>Registro:</td>
					<td class='fila2' width='20%'>
						<table width='100%'>
							<tr>
								<td id='igRegistro' style='font-family: verdana;width:105px;font-size: 10pt;color:#0F3B7F'>".((isset($infoActivo)) ? $infoActivo['Actreg']."&nbsp;<img width='15' height='15' src='../../images/medical/sgc/info.png' style='cursor:help;' title='".$infoRegistro."' tooltip='si' > " : "")."</td>
								<td width='20%'><button style='".((isset($infoActivo)) ? 'display:none;': '' )."font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer' onclick='generarRegistro()'>Generar</button></td>
							</tr>
						</table>
					</td>
					<td class='fila1' width='9%'>Nombre:</td>
					<td class='fila2' width='11%'><input type='text' tipo='obligatorio' id='igNombre'	".((isset($infoActivo)) ? "value='".$infoActivo['Actnom']."'" : "")." ".((!array_key_exists('Actnom', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				</tr>
				<tr>
					<td class='fila1'>Nit proveedor:</td>
					<td class='fila2'>
						<select tipo='obligatorio' id='igProveedores' style='width:170px;font-size: 8pt;' ".((!array_key_exists('Actpro', $camposPermisoEditar)) ? 'disabled' : '').">
							<option value=''>Seleccione...</option>";
							foreach($arrayProveedores as $codPro => $nomPro)
								echo "<option value='".$codPro."' ".((isset($infoActivo) && $infoActivo['Actpro'] == $codPro) ? "SELECTED" : "").">".$codPro."-".$nomPro."</option>";
		echo "			</select>
					</td>
					<td class='fila1'>Factura o contrato:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='igFactura'	".((isset($infoActivo)) ? "value='".$infoActivo['Actfoc']."'" : "".((isset($camposEnCero['Actfoc'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Actfoc', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Marca/Fabricante:</td>
					<td class='fila2'><input type='text' tipo='' id='igMarca'	".((isset($infoActivo)) ? "value='".$infoActivo['Actmar']."'" : "")." ".((!array_key_exists('Actmar', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Serial:</td>
					<td class='fila2'><input type='text' tipo='' id='igSerial'	".((isset($infoActivo)) ? "value='".$infoActivo['Actser']."'" : "".((isset($camposEnCero['Actser'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Actser', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				<tr>
					<td class='fila1'>Modelo:</td>
					<td class='fila2'><input type='text' tipo='' id='igModelo'	".((isset($infoActivo)) ? "value='".$infoActivo['Actmod']."'" : "")." ".((!array_key_exists('Actmod', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila2' colspan='2' width='20%' align='center'>
						Nuevo:<input type='radio' name='nuevoUsado' id='igNuevo' ".((!isset($infoActivo)) ? "checked='checked'" : (($infoActivo['Actnue'] == 'on') ? "checked='checked'" : ""))." ".((!array_key_exists('Actnue', $camposPermisoEditar)) ? 'disabled' : '').">&nbsp;&nbsp;&nbsp;
						Usado:<input type='radio' name='nuevoUsado' id='igUsado' ".((isset($infoActivo) && $infoActivo['Actnue'] != 'on') ? "checked='checked'" : "")." ".((!array_key_exists('Actnue', $camposPermisoEditar)) ? 'disabled' : '').">
					</td>
					<td class='fila1'>Descripción:</td>
					<td class='fila2' align='center'><textarea tipo='' id='igDescripcion' rows='2' cols='30' ".((!array_key_exists('Actdes', $camposPermisoEditar)) ? 'disabled' : '').">".((isset($infoActivo)) ? $infoActivo['Actdes'] : "")."</textarea></td>
					<td class='fila1'>Fecha adquisición:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='igFechaAdqui' size='15' campoFecha='si' ".((isset($infoActivo)) ? "value='".$infoActivo['Actfad']."'" : "")." ".((!array_key_exists('Actfad', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				</tr>
				<tr>
					<td class='fila1'>Puesta en servicio:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='igFechaMarcha' size='15' campoFecha='si' ".((isset($infoActivo)) ? "value='".$infoActivo['Actfps']."'" : "")." ".((!array_key_exists('Actfps', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Estado:</td>
					<td class='fila2' align='center'>
						<select tipo='obligatorio' style='width:170px;font-size: 8pt;' id='igEstado' ".((!array_key_exists('Actest', $camposPermisoEditar)) ? 'disabled' : '')." ".((isset($infoActivo) && $infoActivo['Estret'] == 'on') ? 'disabled' : '').">
							<option value=''>Seleccione...</option>";
							foreach($arrayEstados as $codEst => $infEst)
							{
								if($infEst['deRetiro'] == 'on' && (!isset($infoActivo) || $infoActivo['Estret'] != 'on'))
									break;

								echo "<option value='".$codEst."' ".((isset($infoActivo) && $infoActivo['Actest'] == $codEst) ? "SELECTED" : "").">".$codEst."-".$infEst['Nombre']."</option>";
							}
		echo "			</select>
					</td>
					<td class='fila2' colspan='2' align='center'>
						<table id='listaCentrosCostos' style='font-size: 7pt;text-align: left;margin:3px' width='100%'>
							<tr class='fila1' align='center'><td>Centro de costos</td><td>%</td><td>Estados</td></tr>
						";
						$posicion = 0;
						foreach($arrayCcoAct as $ccoAct => $ccoValores)
						{
							$arrayEstadosCco 	= array();
							$arrayEstadosCco 	= explode(',', $ccoValores['Ccaest']);
							$rowspan 			= count($arrayEstadosCco);
							echo "
							<tr id='trPosCco".$posicion."'>
								<td rowspan='".$rowspan."' style='font-size:9px;border: 1px solid #AED0EA;' >".$ccoValores['Ccodes']."</td>
								<td rowspan='".$rowspan."' style='font-size:9px;border: 1px solid #AED0EA;' >".$ccoValores['Ccapor']."</td>
								<td style='font-size:9px;border: 1px solid #AED0EA;'>";
								echo $arrayEstados[$arrayEstadosCco[0]]['Nombre'];
							echo"</td>
							</tr>";
								foreach($arrayEstadosCco as $key => $ccoAct)
									echo (($key > 0) ? "<tr id='trPosCco".$posicion."'><td style='font-size:9px;border: 1px solid #AED0EA;'>".$arrayEstados[$ccoAct]['Nombre']."</td></tr>" : "");

							$posicion++;
						}
		echo "			</table>
						<button ".((!array_key_exists('Actcco', $camposPermisoEditar)) ? 'disabled' : '')." style='font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer' id='igCco' onclick='editarCentroCostos()'>Editar</button>
					</td>
					<td class='fila1'>Ubicación:</td>
					<td class='fila2'><input type='text' tipo='' id='igUbicacion' ".((isset($infoActivo)) ? "value='".$infoActivo['Actubi']."'" : "")." ".((!array_key_exists('Actubi', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				</tr>
				<tr>
					<td class='fila1'>Depreciable:</td>
					<td class='fila2'>
						Si:<input type='radio' tipo='obligatorio' name='actDepreciable' id='igDespreciableSi' ".((!isset($infoActivo)) ? "checked='checked'" : (($infoActivo['Actdep'] == 'on') ? "checked='checked'" : ""))." ".((!array_key_exists('Actdep', $camposPermisoEditar)) ? 'disabled' : '').">&nbsp;&nbsp;&nbsp;
						No:<input type='radio' name='actDepreciable' id='igDespreciableNo' ".((isset($infoActivo) && $infoActivo['Actdep'] != 'on') ? "checked='checked'" : "")." ".((!array_key_exists('Actdep', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td ></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan='8'>
						<br>
						<fieldset align='center' id='' style='padding:15px;'>
							<legend class='fieldset'>Seguro</legend>
							<table width='100%'>
								<tr>
									<td class='fila1'>Nit aseguradora:</td>
									<td class='fila2'>
									<select tipo='obligatorio' style='width:230px;font-size: 8pt;' id='igAseguradora' ".((!array_key_exists('Actase', $camposPermisoEditar)) ? 'disabled' : '').">
										<option value=''>Seleccione...</option>";
										foreach($arrayAseguradoras as $codAse => $nomAse)
											echo "<option value='".$codAse."' ".((isset($infoActivo) && $infoActivo['Actase'] == $codAse) ? "SELECTED" : "").">".$codAse."-".$nomAse."</option>";
					echo "			</select>
									</td>
									<td class='fila1'>Número póliza:</td>
									<td class='fila2'><input type='text' tipo='' id='igNumPoliza' ".((isset($infoActivo)) ? "value='".$infoActivo['Actpol']."'" : "".((isset($camposEnCero['Actpol'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Actpol', $camposPermisoEditar)) ? 'disabled' : '')."></td>
									<td class='fila1'>Valor asegurado:</td>
									<td class='fila2'><input type='text' tipo='' id='igValorAseg' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Actvas']."'" : "".((isset($camposEnCero['Actvas'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Actvas', $camposPermisoEditar)) ? 'disabled' : '')."></td>
								</tr>
							</table>
						</fieldset>
					</td>
				</tr>
				<tr><td colspan='8' align='right'><br><div id='div_mensajes' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td></tr>";
				$arrPerAct = traerPeriodoActual();
				$arrPerAct = implode('-', $arrPerAct);
				$modificar = (($periodoVer == $arrPerAct) ? "" : "disabled='disabled'");
		echo"	<tr><td colspan='8' align='center'><button ".$modificar." style='font-family: verdana;font-weight:bold;font-size: 10pt;' onclick='guardarInforGeneral(this, \"".$registroActivo."\")'>".(($registroActivo == 'nuevo') ? "Guardar" : "Actualizar")."</button></td></tr>
			</table>
		</fieldset>
		<br>";

		// --> 	Inicio, Pintar div oculto inicialmente el cual se muestra en ventana modal para generar registro del nuevo activo
		// -->	Obtener las modalidades de los activos
		$sqlModalidades = " SELECT Modcod, Modnom
							  FROM ".$wbasedato."_000007
							 WHERE Modest = 'on'
						  ORDER BY Modnom ";
		$resModalidades = mysql_query($sqlModalidades, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlModalidades):</b><br>".mysql_error());
		$arrModalidades = array();
		while($rowModalidades = mysql_fetch_array($resModalidades))
			$arrModalidades[$rowModalidades['Modcod']] = utf8_encode($rowModalidades['Modnom']);

		// -->	Obtener los grupos de los activos
		$sqlGrupos = " SELECT Grucod, Grunom
						 FROM ".$wbasedato."_000008
						WHERE Gruest = 'on'
					 ORDER BY Grunom ";
		$resGrupos = mysql_query($sqlGrupos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGrupos):</b><br>".mysql_error());
		$arrGrupos = array();
		while($rowGrupos = mysql_fetch_array($resGrupos))
			$arrGrupos[$rowGrupos['Grucod']] = utf8_encode($rowGrupos['Grunom']);

		// --> Ventana modal para generar el registro del activo
		echo "
		<div id='divGenerarRegistro' style='display:none' align='center'>
			<br>
			<table>
				<tr>
					<td class='encabezadoTabla'>Modalidad</td>
					<td class='encabezadoTabla'>Grupo</td>
					<td class='encabezadoTabla'>Subgrupo</td>
				</tr>
				<tr>
					<td class='fila2'>
						<select style='width:170px;font-size: 8pt;' id='generarRegModalidad'>
							<option value=''>Seleccione...</option>";
						foreach($arrModalidades as $modCod => $modNom)
		echo "				<option value='".$modCod."'>".$modNom."</option>";
		echo "			</select>
					</td>
					<td class='fila2'>
						<select style='width:170px;font-size: 8pt;' id='generarRegGrupo' onChange='cargarSelectSubgrupos()'>
							<option value=''>Seleccione...</option>";
						foreach($arrGrupos as $gruCod => $gruNom)
		echo "				<option value='".$gruCod."'>".$gruNom."</option>";
		echo "			</select>
					</td>
					<td class='fila2'>
						<select style='width:170px;font-size: 8pt;' id='generarRegSubGrupo'>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan='3' align='center'>
						<br>
						<button style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='asignarRegistroActivo()'>Asignar registro</button>
					</td>
				</tr>
			</table>
		</div>
		";

		// --> Ventana modal para agregar los centros de costos
		echo "
		<div id='divEditarCentrosDeCostos' style='padding:15px;display:none' align='center' style='font-family: verdana;font-size: 10pt;'>
			<fieldset align='center' id='' style='padding:15px;'>
				<legend class='fieldset'>Nuevo centro de costos</legend>
				<table width='100%'>
					<tr>
						<td class='fila1'><b>Centro de costos:</b></td>
						<td class='fila2'>
							<select style='width:170px;font-size: 8pt;' id='centroCostosActivo' tipo='obligatorio'>
								<option class='find' value=''>Seleccione...</option>";
								foreach($arrayCco as $codCco => $nomCco)
									echo "<option class='find' value='".$codCco."' ".((isset($infoActivo) && $infoActivo['Actcco'] == $codCco) ? "SELECTED" : "").">".$codCco."-".$nomCco."</option>";
			echo "			</select>
						</td>
						<td class='fila1'><b>Porcentaje:</b></td>
						<td class='fila2'><input type='text' size='4' id='igPorcentajeDis' tipo='obligatorio' class='entero'> <b>%</b></td>
						<td class='fila1'><b>Estados:</b></td>
						<td class='fila2'>
							<table style='font-family: verdana;font-size: 7pt;' id='listaDeEstados'>";
						$x = 0;
						foreach($arrayEstados as $codEst => $nomEst)
						{
							echo (($x%2==0) ? "<tr>" : "");
			echo "			<td><input type='checkbox' class='bordeado' value='".$codEst."'>0".($x+1)." ".$nomEst['Nombre']."</td>";
							echo (($x%2==0) ? "" : "</tr>");
							$x++;
						}
			echo "			</table>
						</td>
					</tr>
					<tr><td colspan='6' align='right'><br><div id='div_mensajes4' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td></tr>
					<tr>
						<td colspan='6' align='center'><button id='botonAgregarCco' style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='agregarCco()'>+ Agregar</button></td>
					</tr>
				</table>
			</fieldset>
			<br>
			<fieldset align='center' id='' style='padding:15px;'>
				<legend class='fieldset'>Configuración actual</legend>
				<table width='100%' id='listaConfiguracionCco'>
					<tr class='encabezadoTabla'>
						<td>Centro de costos</td>
						<td>Porcentaje</td>
						<td>Estados</td>
					</tr>";
				$totalPor = 0;
				$posicion = 0;
				foreach($arrayCcoAct as $ccoAct => $ccoValores)
				{
					$totalPor+=$ccoValores['Ccapor'];
					$arrayEstadosCco 	= array();
					$arrayEstadosCco 	= explode(',', $ccoValores['Ccaest']);
			echo "	<tr trCco='si'>
						<td class='fila2' id='tdConfCco' cco='".$ccoAct."'>".$ccoValores['Ccodes']."</td>
						<td class='fila2' id='tdConfPor' porcentaje='si'>".$ccoValores['Ccapor']."</td>
						<td class='fila2' id='tdConfEst' listaEstados='".$ccoValores['Ccaest']."'>";
					foreach($arrayEstadosCco as $estadosCco)
						echo $estadosCco.' '.$arrayEstados[$estadosCco]['Nombre'].', ';
			echo "		</td>
						<td>
							<img trPosCco='".$posicion++."' style='cursor:pointer;width:10px;height:10px;' onclick='eliminarCco(this);' title='Eliminar' src='../../images/medical/eliminar1.png'>
						</td>
					</tr>";
				}
			echo "	<tr>
						<td></td>
						<td class='fondoAmarillo' id='totalPorcentajeConf' value='".$totalPor."'><b>Total: ".$totalPor." %</b></td>
						<td colspan='2'></td>
					</tr>
				</table>
			</fieldset>
		</div>
		";
	}

	//---------------------------------------------------
	//	--> Formulario de informacion fiscal
	//---------------------------------------------------
	function formularioInfoFiscal($registroActivo, $periodoVer)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$arrPeriodoVer	= explode('-', $periodoVer);
		$periodoAno		= $arrPeriodoVer[0];
		$periodoMes		= $arrPeriodoVer[1];

		if($registroActivo != 'nuevo')
		{
			// --> Consultar información
			$sqlAct = "SELECT A.*, Actnom, Actact
						 FROM ".$wbasedato."_000002 as A, ".$wbasedato."_000001
						WHERE Aifano = '".$periodoAno."'
						  AND Aifmes = '".$periodoMes."'
						  AND Aifreg = '".$registroActivo."'
						  AND Aifreg = Actreg
						  AND Aifano = Actano
						  AND Aifmes = Actmes
			";
			$resAct = mysql_query($sqlAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAct):</b><br>".mysql_error());
			if($rowAct = mysql_fetch_array($resAct))
			{
				$infoActivo = $rowAct;
				// --> Codificar tildes y caracteres especiales
				foreach($infoActivo as $indice => &$valor)
					$valor = utf8_encode($valor);
			}
			else
				$camposEnCero = camposQueInicianEnCero("000002");
		}
		else
			$camposEnCero = camposQueInicianEnCero("000002");

		activoRegistrado($registroActivo, $periodoAno, $periodoMes);

		// --> Obtener array con los campos que puede editar el usuario
		$camposPermisoEditar = permisosParaEditarCampos();
		$perIngresarActivo	 = ((isset($camposPermisoEditar['actfij']['000001']['Actact'])) ? TRUE : FALSE);
		$camposPermisoEditar = ((isset($camposPermisoEditar['actfij']['000002'])) ? $camposPermisoEditar['actfij']['000002'] : array() );

		// --> Consultar los metodos de depreciación fiscal
		$arrayMetodosDep = array();
		$sqlMetodosDes  = "SELECT Mdfcod, Mdfnom
							 FROM ".$wbasedato."_000010
							WHERE Mdfest = 'on'
							  AND Mdftip = 'F'
		";
		$resMetodosDes = mysql_query($sqlMetodosDes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetodosDes):</b><br>".mysql_error());
		while($rowMetodosDes = mysql_fetch_array($resMetodosDes))
			$arrayMetodosDep[$rowMetodosDes['Mdfcod']] = utf8_encode($rowMetodosDes['Mdfnom']);

		// --> Consultar los porcentajes de ajuste fiscal
		$arrayPorAjusFis = array();
		$sqlPorAjusFis  = "SELECT Pafcod, Pafnom, Pafpor
							 FROM ".$wbasedato."_000011
							WHERE Pafest = 'on'
		";
		$resPorAjusFis = mysql_query($sqlPorAjusFis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPorAjusFis):</b><br>".mysql_error());
		while($rowPorAjusFis = mysql_fetch_array($resPorAjusFis))
		{
			$arrayPorAjusFis[$rowPorAjusFis['Pafcod']]['nombre'] 		= utf8_encode($rowPorAjusFis['Pafnom']);
			$arrayPorAjusFis[$rowPorAjusFis['Pafcod']]['porcentaje'] 	= $rowPorAjusFis['Pafpor'];
		}

		echo "
		".convenciones($registroActivo, $periodoVer, $perIngresarActivo)."
		<fieldset align='center' id='' style='padding:15px;'>
			<legend class='fieldset'>".((isset($infoActivo) ? $infoActivo['Actnom'] : 'Nuevo'))."</legend>
			<table width='100%' id='tablaInfoFiscal'>
				<tr>
					<td class='fila1' width='10%'>Costo de adquisición:</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='ifCostoAdqui' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifcad']."'" : "".((isset($camposEnCero['Aifcad'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifcad', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1' width='10%'>Iva:</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='ifIva' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifica']."'" : "".((isset($camposEnCero['Aifica'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifica', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1' width='10%'>Axi costo historico:</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='ifAxi' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifaxi']."'" : "".((isset($camposEnCero['Aifaxi'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifaxi', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1' width='10%'>Adiciones/Mejoras:</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='ifMejoras' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifmej']."'" : "".((isset($camposEnCero['Aifmej'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifmej', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				</tr>
				<tr>
					<td class='fila1'>Depreciación acumulada:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='ifDepreAcum' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifdac']."'" : "".((isset($camposEnCero['Aifdac'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifdac', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Axi depreciación acumulada:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='ifAxiDepreAcum' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifada']."'" : "".((isset($camposEnCero['Aifada'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifada', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Impuesto por valorización:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='ifImpuValorizacion' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifipv']."'" : "".((isset($camposEnCero['Aifipv'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifipv', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Valor de salvamento:</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='ifValorSalvamento' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifvsa']."'" : "".((isset($camposEnCero['Aifvsa'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifvsa', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				<tr>
					<td class='fila1'>Requiere ajustes fiscales:</td>
					<td class='fila2'>
						Si:<input type='radio' tipo='obligatorio' name='reqAjusFiscales' id='ifSiReqAjusFiscales' ".((!isset($infoActivo)) ? "checked='checked'" : (($infoActivo['Aifraf'] == 'on') ? "checked='checked'" : ""))." ".((!array_key_exists('Aifraf', $camposPermisoEditar)) ? 'disabled' : '').">&nbsp;&nbsp;&nbsp;
						No:<input type='radio' name='reqAjusFiscales' id='ifNoReqAjusFiscales' ".((isset($infoActivo) && $infoActivo['Aifraf'] != 'on') ? "checked='checked'" : "")." ".((!array_key_exists('Aifraf', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>% Ajuste fiscal:</td>
					<td class='fila2'>
						<select id='ifPorcenAjusteFiscal' style='width:133px;font-size: 8pt;' ".((!array_key_exists('Aifpaf', $camposPermisoEditar)) ? 'disabled' : '').">
							<option value=''>Seleccione...</option>";
						foreach($arrayPorAjusFis as $porCod => $porVal)
		echo "				<option value='".$porCod."' ".((((isset($infoActivo) && $infoActivo['Aifpaf'] == $porCod) ? "SELECTED" : ""))).">".$porVal['nombre']."&nbsp;&nbsp;-&nbsp;&nbsp;".$porVal['porcentaje']."%</option>";
		echo "			</select>
					</td>
					<td class='fila1'>Avalúo catastral</td>
					<td class='fila2'><input type='text' tipo='obligatorio' id='ifAvaluoCatastral' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifaca']."'" : "".((isset($camposEnCero['Aifaca'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifaca', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1'>Método depreciación fiscal</td>
					<td class='fila2'>
						<select id='ifMetodoDepre' style='width:160px;font-size: 8pt;' ".((!array_key_exists('Aifmde', $camposPermisoEditar)) ? 'disabled' : '').">
							<option value=''>Seleccione...</option>";
						foreach($arrayMetodosDep as $metCod => $metNom)
		echo "				<option value='".$metCod."' ".((((isset($infoActivo) && $infoActivo['Aifmde'] == $metCod) ? "SELECTED" : ""))).">".$metNom."</option>";
		echo "			</select>
					</td>
				</tr>
				<tr>
					<td class='fila1'>Valor patrimonial:</td>
					<td class='fila2'>".((isset($infoActivo)) ? formato_numero($infoActivo['Aifvpa']) : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")."</td>
					<td colspan='6'></td>
				</tr>
				<tr>
					<td colspan='8'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Reajuste fiscal</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='17' id='ifReaFisSaldoIni' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifsir']."'" : "".((isset($camposEnCero['Aifsir'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifsir', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Movimiento anual:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='17' id='ifReaFisMoviAnual' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifmar']."'" : "".((isset($camposEnCero['Aifmar'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifmar', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='17' id='ifReaFisSaldoFin' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifsfr']."'" : "".((isset($camposEnCero['Aifsfr'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifsfr', $camposPermisoEditar)) ? 'disabled' : '')."></td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Periodos de depreciación fiscal</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Vida útil:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='5' id='ifVidaUtil' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifvut']."'" : "".((isset($camposEnCero['Aifvut'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifvut', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Períodos depreciados:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='5' id='ifPeriodosDepre' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifpde']."'" : "".((isset($camposEnCero['Aifpde'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifpde', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Períodos pendientes:</td>
												<td class='fila2'>".((isset($infoActivo)) ? $infoActivo['Aifppd'] : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Depreciación fiscal</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='14' id='ifDepreFisSaldoIni' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifsin']."'" : "".((isset($camposEnCero['Aifsin'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifsin', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Deprec. mensual:</td>
												<td class='fila2'>".((isset($infoActivo)) ? formato_numero($infoActivo['Aifdme']) : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")."</td>
												<td class='fila1'>Deprec. Acumul</td>
												<td class='fila2'>".((isset($infoActivo)) ? formato_numero($infoActivo['Aifdaf']) : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Valor en libros fiscal</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' size='14' id='ifValorLibrosSaldoIni' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aifsiv']."'" : "".((isset($camposEnCero['Aifsiv'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aifsiv', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Mvto. mensual:</td>
												<td class='fila2'>".((isset($infoActivo)) ? formato_numero($infoActivo['Aifmmv']) : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".((isset($infoActivo)) ? formato_numero($infoActivo['Aifsfv']) : "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan='8' align='right'><br><div id='div_mensajes2' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td></tr>";

				$arrPerAct = traerPeriodoActual();
				$arrPerAct = implode('-', $arrPerAct);
				$modificar = (($periodoVer == $arrPerAct) ? "" : "disabled='disabled'");
		echo"	<tr><td colspan='8' align='center'><br><button id='buttonGuardarFis' ".$modificar." style='font-family: verdana;font-weight:bold;font-size: 10pt;' onclick='guardarInforFiscal(this, \"".$registroActivo."\", \"".((isset($infoActivo)) ? $infoActivo['id'] : "")."\")'>".((isset($infoActivo)) ? "Actualizar" : "Guardar")."</button></td></tr>
			</table>
		</fieldset>
		<br>";
	}

	//---------------------------------------------------
	//	-->  Formulario de informacion Niif-Nic
	//---------------------------------------------------
	function formularioInfoNiif($registroActivo, $componente, $periodoVer)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$arrPeriodoVer	= explode('-', $periodoVer);
		$periodoAno		= $arrPeriodoVer[0];
		$periodoMes		= $arrPeriodoVer[1];

		// --> Consultar Informacion
		if($registroActivo != 'nuevo')
		{
			// --> Consultar información
			$sqlAct = "SELECT A.*, Actnom, Actact
						 FROM ".$wbasedato."_000003 as A INNER JOIN ".$wbasedato."_000001 as B
						   ON (A.Ainano =  B.Actano AND A.Ainmes =  B.Actmes AND A.Ainreg = B.Actreg)
						WHERE Ainano = '".$periodoAno."'
						  AND Ainmes = '".$periodoMes."'
						  AND Ainreg = '".$registroActivo."'
						  AND Aincom = '".$componente."'
			";
			$resAct = mysql_query($sqlAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAct):</b><br>".mysql_error());
			if($rowAct = mysql_fetch_array($resAct))
			{
				$infoActivo 		= $rowAct;
				// --> Codificar tildes y caracteres especiales
				foreach($infoActivo as $indice => &$valor)
					$valor = utf8_encode($valor);
			}
			else
				$camposEnCero = camposQueInicianEnCero("000003");


			// --> 	Obtener los valores a mostrar del componente padre, ya que pueden ser la suma de los
			//		valores sus componentes o el elemento de mayor valor.
			if($componente == '*')
			{
				$arrayCamposNum = array();

				// --> Obtener los campos numericos segun el esquema de la tabla en la base de datos
				$sqlCampNum = " SHOW COLUMNS
								FROM ".$wbasedato."_000003
							   WHERE Type = 'double'
				";
				$resCampNum = mysql_query($sqlCampNum, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCampNum):</b><br>".mysql_error());
				while($rowCampNum = mysql_fetch_array($resCampNum))
					$arrayCamposNum[$rowCampNum['Field']] = '';

				// --> Obtener cuales campos son los que su valor es igual a la suma de sus componentes
				$sqlCamSum = "SELECT Percam
								FROM ".$wbasedato."_000019
							   WHERE Permed = '".$wbasedato."'
							     AND Percod = '000003'
							     AND Persvc = 'on'
							     AND Perest = 'on'
				";
				$resCamSum = mysql_query($sqlCamSum, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamSum):</b><br>".mysql_error());
				while($rowCamSum = mysql_fetch_array($resCamSum))
					$arrayCamposNum[$rowCamSum['Percam']] = 'SUMA';

				// --> Consultar la informacion de sus componentes
				$arrayInfoCom = array();
				$sqlInfoCompo = " SELECT Aincom, ".implode(', ', array_keys($arrayCamposNum))."
									FROM ".$wbasedato."_000003
								   WHERE Ainano = '".$periodoAno."'
									 AND Ainmes = '".$periodoMes."'
									 AND Ainreg = '".$registroActivo."'
									 AND Aincom != '*'
									 AND Ainest = 'on'
				";
				$resInfoCompo = mysql_query($sqlInfoCompo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoCompo):</b><br>".mysql_error());
				while($rowInfoCompo = mysql_fetch_array($resInfoCompo, MYSQL_ASSOC))
				{
					$numComponente = $rowInfoCompo['Aincom'];
					unset($rowInfoCompo['Aincom']);

					foreach($rowInfoCompo as $campo => $valor)
						$arrayInfoCom[$campo][$numComponente] = $valor;
				}

				// --> 	Recorrer el array de los valores del componente e ir analizando si se deben sumar sus valores
				// 		o dejar el de mayor valor
				foreach($arrayInfoCom as $campo => $arrayValCom)
				{
					// --> El valor del padre sera igual al valor de la suma de sus componentes
					if($arrayCamposNum[$campo] == 'SUMA')
						$infoActivo[$campo] = array_sum($arrayValCom);
					// --> El valor del padre sera igual al valor mayor de sus componentes
					else
						$infoActivo[$campo] = max($arrayValCom);
				}
			}
		}
		else
			$camposEnCero = camposQueInicianEnCero("000003");

		activoRegistrado($registroActivo, $periodoAno, $periodoMes);

		// --> Obtener array con los campos que puede editar el usuario
		$camposPermisoEditar = permisosParaEditarCampos();
		$perIngresarActivo	 = ((isset($camposPermisoEditar['actfij']['000001']['Actact'])) ? TRUE : FALSE);
		$camposPermisoEditar = ((isset($camposPermisoEditar['actfij']['000003'])) ? $camposPermisoEditar['actfij']['000003'] : array() );

		// --> Consultar maestro de unidades generadoras de efectivo
		$arrayUniGenEfec = array();
		$sqlUniGenEfec  = "SELECT Ugecod, Ugenom
							 FROM ".$wbasedato."_000022
							WHERE Ugeest = 'on'
		";
		$resUniGenEfec = mysql_query($sqlUniGenEfec, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUniGenEfec):</b><br>".mysql_error());
		while($rowUniGenEfec = mysql_fetch_array($resUniGenEfec))
			$arrayUniGenEfec[$rowUniGenEfec['Ugecod']] = utf8_encode($rowUniGenEfec['Ugenom']);

		// --> Consultar los metodos de depreciación niff
		$arrayMetodosDep = array();
		$sqlMetodosDes  = "SELECT Mdfcod, Mdfnom, Mdfrod
							 FROM ".$wbasedato."_000010
							WHERE Mdfest = 'on'
							  AND Mdftip = 'N'
		";
		$resMetodosDes = mysql_query($sqlMetodosDes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetodosDes):</b><br>".mysql_error());
		while($rowMetodosDes = mysql_fetch_array($resMetodosDes))
		{
			$arrayMetodosDep[$rowMetodosDes['Mdfcod']]['nombre'] 			= utf8_encode($rowMetodosDes['Mdfnom']);
			$arrayMetodosDep[$rowMetodosDes['Mdfcod']]['activarOrigenDato'] = utf8_encode($rowMetodosDes['Mdfrod']);
		}

		// --> Consultar maestro de costos futuros
		$arrayCostosFut = array();
		$sqlCostosFut   = "SELECT Cfucod, Cfunom
							 FROM ".$wbasedato."_000012
							WHERE Cfuest = 'on'
		";
		$resCostosFut = mysql_query($sqlCostosFut, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCostosFut):</b><br>".mysql_error());
		while($rowCostosFut = mysql_fetch_array($resCostosFut))
			$arrayCostosFut[$rowCostosFut['Cfucod']] = utf8_encode($rowCostosFut['Cfunom']);

		// --> Consultar maestro de tipos de ajuste de costos futuros
		$arrayTipoAjust = array();
		$sqlTipoAjust   = "SELECT Tajcod, Tajnom
							 FROM ".$wbasedato."_000013
							WHERE Tajest = 'on'
		";
		$resTipoAjust = mysql_query($sqlTipoAjust, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipoAjust):</b><br>".mysql_error());
		while($rowTipoAjust = mysql_fetch_array($resTipoAjust))
			$arrayTipoAjust[$rowTipoAjust['Tajcod']] = utf8_encode($rowTipoAjust['Tajnom']);

		// --> Consultar maestro de tasa de ajuste períodico
		$arrayTasaAjust = array();
		$sqlTasaAjust   = "SELECT Tapcod, Tapnom
							 FROM ".$wbasedato."_000014
							WHERE Tapest = 'on'
		";
		$resTasaAjust = mysql_query($sqlTasaAjust, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTasaAjust):</b><br>".mysql_error());
		while($rowTasaAjust = mysql_fetch_array($resTasaAjust))
			$arrayTasaAjust[$rowTasaAjust['Tapcod']] = utf8_encode($rowTasaAjust['Tapnom']);

		// --> Consultar maestro de métodos de valuación
		$arrayMetodValu = array();
		$sqlMetodValu   = "SELECT Mvacod, Mvanom
							 FROM ".$wbasedato."_000015
							WHERE Mvaest = 'on'
		";
		$resMetodValu = mysql_query($sqlMetodValu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetodValu):</b><br>".mysql_error());
		while($rowMetodValu = mysql_fetch_array($resMetodValu))
			$arrayMetodValu[$rowMetodValu['Mvacod']] = utf8_encode($rowMetodValu['Mvanom']);

		// --> Consultar nombre del origen del dato estadístico
		$nomEstadistica = '';
		if($infoActivo['Ainode'] != '' && $infoActivo['Aintde'] == 'on')
		{
			$wbasedatoSgc = consultarAliasPorAplicacion($conex, $wemp_pmla, 'sgc');
			$sqlNomEst  = "SELECT Indnom
							 FROM ".$wbasedatoSgc."_000001
							WHERE Indcod = '".$infoActivo['Ainode']."'
			";
			$resNomEst = mysql_query($sqlNomEst, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlNomEst):</b><br>".mysql_error());
			if($rowNomEst = mysql_fetch_array($resNomEst))
				$nomEstadistica = utf8_encode($rowNomEst['Indnom']);
		}

		echo "

		<fieldset align='center' id='' style='padding:15px;'>
			<legend class='fieldset' id='fieldsetInfoNiif'>".((isset($infoActivo)) ? (($componente == '*') ? $infoActivo['Actnom'] : $infoActivo['Ainnco']) : 'Nuevo')."</legend>
			<table width='100%' id='tablaInfoNiif'>
				<tr>";
				if($componente == '*')
				{
					echo "
					<td class='fila1' width='12%'>Unidad generadora de efectivo:</td>
					<td class='fila2' width='13%'>
						<select tipo='obligatorio' id='inUniGeneraEfec' style='width:140px;font-size: 8pt;' ".((!array_key_exists('Ainuge', $camposPermisoEditar)) ? 'disabled' : '').">
							<option value=''>Seleccione...</option>";
						foreach($arrayUniGenEfec as $codUniGen => $nomUniGen)
				echo " 		<option value='".$codUniGen."' ".((((isset($infoActivo) && $infoActivo['Ainuge'] == $codUniGen) ? "SELECTED" : ""))).">".$nomUniGen."</option>";
				echo "	</select>
					</td>
					<td class='fila1' width='10%'>Detalle por componentes:</td>
					<td class='fila2' width='15%'>
						Si:<input type='radio' name='detPorComponentes' id='detPorComponentesSi' onClick='verDetallePorComponentes(\"".$registroActivo."\");' ".((!isset($infoActivo)) ? "" : (($infoActivo['Aindpc'] == 'on') ? "checked='checked'" : ""))." ".((!array_key_exists('Aindpc', $camposPermisoEditar)) ? 'disabled' : '').">&nbsp;&nbsp;&nbsp;
						No:<input type='radio' name='detPorComponentes' id='detPorComponentesNo' onClick='verDetallePorComponentes(\"".$registroActivo."\");' ".((!isset($infoActivo)) ? "checked='checked'" :(($infoActivo['Aindpc'] != 'on') ? "checked='checked'" : ""))." ".((!array_key_exists('Aindpc', $camposPermisoEditar)) ? 'disabled' : '').">
					</td>";
				}
				else
				{
					echo "
					<td class='fila1' width='10%'>Componente:</td>
					<td class='fila2' width='8%' id='inComponente'> ".((isset($infoActivo)) ? $componente : "")."</td>
					<td class='fila1' width='12%'>Nombre del Componente:</td>
					<td class='fila2' width='20%'><input type='text' tipo='' size='35' id='inNombreComponente' onkeyup='asignarNombreCompo(\"".$componente."\");' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainnco']."'" : "")." ".((!array_key_exists('Ainnco', $camposPermisoEditar)) ? 'disabled' : '')."></td>";
				}
		echo"		<td class='fila1' width='10%'>Costo atribuido/adquisición:</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='inCostoAtrib' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aincad']."'" : "".((isset($camposEnCero['Aincad'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aincad', $camposPermisoEditar)) ? 'disabled' : '')."></td>
					<td class='fila1' width='10%'>Valor de salvamento</td>
					<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='inValorSalvamento' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainvsa']."'" : "".((isset($camposEnCero['Ainvsa'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Ainvsa', $camposPermisoEditar)) ? 'disabled' : '')."></td>
				</tr>
				<tr>
					<td class='fila1' width='10%'>Método de depreciación NIIF:</td>
					<td class='fila2' width='10%'>
						<select tipo='obligatorio' id='inMetodoDepNiif' style='width:140px;font-size: 8pt;' ".((!array_key_exists('Ainmdn', $camposPermisoEditar)) ? 'disabled' : '')." onChange='habilitarCampoOrigenDato(this)'>
							<option value=''>Seleccione...</option>";
						foreach($arrayMetodosDep as $metodDepCod => $infoMetodo)
				echo " 		<option activarOrigenDato='".$infoMetodo['activarOrigenDato']."' value='".$metodDepCod."' ".((((isset($infoActivo) && $infoActivo['Ainmdn'] == $metodDepCod) ? "SELECTED" : ""))).">".$infoMetodo['nombre']."</option>";
				echo "	</select>
					<td class='fila1 origenDato' width='14%' style='".(isset($infoActivo) && $infoActivo['Ainode'] != '' ? "" : "display:none")."'>
						<b>Origen del dato:</b>
						<br>
						<table style='font-size: 7pt;'>
							<tr><td>Sistema de estadísticas:</td><td align='center'><input type='radio' name='tipoOrigenDato' id='tipoOrigenDatoEstadistico' 	".((isset($infoActivo) && $infoActivo['Aintde'] == 'on') ? 'checked="checked"' : '')." onClick='cambioTipoOrigenDato()'></td></tr>
							<tr><td>Manual:					</td><td align='center'><input type='radio' name='tipoOrigenDato' id='tipoOrigenDatoManual'			".((isset($infoActivo) && $infoActivo['Aintde'] != 'on') ? 'checked="checked"' : '')." onClick='cambioTipoOrigenDato()'></td></tr>
						</table>
					</td>
					<td class='fila2 origenDato' width='11%' style='".(isset($infoActivo) && $infoActivo['Ainode'] != '' ? "" : "display:none")."'>
						<input type='text' id='inOrigenDatoEstadistico' ".((isset($infoActivo) && $infoActivo['Aintde'] == 'on') ? '' : 'style="display:none"')." size='35' tipo='obligatorio' ".(isset($infoActivo) ? "valor='".$infoActivo['Ainode']."' nombre='".$nomEstadistica."' value='".$infoActivo['Ainode']."-".$nomEstadistica."' " : "valor='' nombre='' value=''")."' >
						<input type='text' id='inValorDatoManual' 		".((isset($infoActivo) && $infoActivo['Aintde'] != 'on') ? '' : 'style="display:none"')." size='35' tipo='obligatorio' ".(isset($infoActivo) ? "value='".$infoActivo['Ainode']."'" : "".((isset($camposEnCero['Ainode'])) ? 'value="0"' : '')."")."' >
					</td>
					<td class='' width='10%'></td>
					<td class='' width='15%'></td>
					<td class='' width='10%'></td>
					<td class='' width='15%'></td>
				</tr>
				<tr>
					<td colspan='8'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Reexpresión costo por revaluación</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrsi']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrmm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrsf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Adiciones/Mejoras</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainmsi']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' id='inAdicionesMovMen' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainmmm']."'" : "".((isset($camposEnCero['Ainmmm'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Ainmmm', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainmsf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Reexpresión adición/Mejoras por revaluación</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrai']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainram']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainraf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<br>
						<table width='100%'>
							<tr>
								<td width='100%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Costos futuros por disposición final, rehabilitación y/o desmantelamiento</legend>
										<table width='100%'>
											<tr>
												<td class='fila1' width='10%'>Tipo de costos:</td>
												<td class='fila2' width='15%'>
													<select id='inTipoCostosfuturos' style='width:160px;font-size: 8pt;' ".((!array_key_exists('Aintcf', $camposPermisoEditar)) ? 'disabled' : '').">
														<option value=''>Seleccione...</option>";
													foreach($arrayCostosFut as $cosCod => $cosNom)
											echo " 		<option value='".$cosCod."' ".((((isset($infoActivo) && $infoActivo['Aintcf'] == $cosCod) ? "SELECTED" : ""))).">".$cosNom."</option>";
											echo "	</select>
												</td>
												<td class='fila1' width='10%'>Tiempo estimado:</td>
												<td class='fila2' width='15%'><input type='text' tipo='obligatorio' id='inTiempoEstimadoCosFut' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Aintes']."'" : "".((isset($camposEnCero['Aintes'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Aintes', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1' width='10%'>Fecha real:</td>
												<td class='fila2' width='15%'><input type='text' tipo='' size='12'  campoFecha='si' id='fechaRealCosFut' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainfre']."'" : "")." ".((!array_key_exists('Ainfre', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1' width='10%'>Tipo de ajuste:</td>
												<td class='fila2' width='15%'>
													<select tipo='obligatorio' id='tipoAjusteCosFut' style='width:160px;font-size: 8pt;' ".((!array_key_exists('Aintaj', $camposPermisoEditar)) ? 'disabled' : '').">
														<option value=''>Seleccione...</option>";
													foreach($arrayTipoAjust as $tipAjusCod => $tipAjusNom)
											echo " 		<option value='".$tipAjusCod."' ".((((isset($infoActivo) && $infoActivo['Aintaj'] == $tipAjusCod) ? "SELECTED" : ""))).">".$tipAjusNom."</option>";
											echo "	</select>
												</td>
											</tr>
											<tr>
												<td class='fila1' width='10%'>Tasa de ajuste períodico:</td>
												<td class='fila2' width='15%'>
													<select tipo='obligatorio' id='tasaAjustePerCosFut' style='width:160px;font-size: 8pt;' ".((!array_key_exists('Aintap', $camposPermisoEditar)) ? 'disabled' : '').">
														<option value=''>Seleccione...</option>";
													foreach($arrayTasaAjust as $tasaAjusCod => $tasaAjusNom)
											echo " 		<option value='".$tasaAjusCod."' ".((((isset($infoActivo) && $infoActivo['Aintap'] == $tasaAjusCod) ? "SELECTED" : ""))).">".$tasaAjusNom."</option>";
											echo "	</select>
												</td>
												<td class='' width='10%'></td>
												<td class='' width='15%'></td>
												<td class='' width='10%'></td>
												<td class='' width='15%'></td>
												<td class='' width='10%'></td>
												<td class='' width='15%'></td>
											</tr>
											<tr>
												<td width='100%' colspan='8'>
													<br>
													<table width='100%'>
														<tr>
															<td width='50%'>
																<fieldset align='center' id='' style='padding:12px;'>
																	<legend class='fieldset'>Costos futuros</legend>
																	<table width='100%'>
																		<tr>
																			<td class='fila1'>Valor:</td>
																			<td class='fila2' colspan='5'>
																				<input type='text' tipo='' id='inCostosFuturosVal' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainvcf']."'" : "".((isset($camposEnCero['Ainvcf'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Ainvcf', $camposPermisoEditar)) ? 'disabled' : '').">
																			</td>
																		</tr>
																		<tr>
																			<td class='fila1'>Saldo inicial:</td>
																			<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aincsi']) : "&nbsp;&nbsp;")."</td>
																			<td class='fila1'>Movimiento mensual:</td>
																			<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aincmm']) : "&nbsp;&nbsp;")."</td>
																			<td class='fila1'>Saldo final:</td>
																			<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aincsf']) : "&nbsp;&nbsp;")."</td>
																		</tr>
																	</table>
																</fieldset>
															</td>
															<td width='50%'>
																<fieldset align='center' id='' style='padding:12px;'>
																	<legend class='fieldset'>Provisión costos futuros</legend>
																	<table width='100%'>
																		<tr>
																			<td class='fila1'>Valor:</td>
																			<td class='fila2' colspan='5'>
																				<input type='text' tipo='' id='inProvisionCostosFuturosVal' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainvpc']."'" : "".((isset($camposEnCero['Ainvpc'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Ainvpc', $camposPermisoEditar)) ? 'disabled' : '').">
																			</td>
																		</tr>
																		<tr>
																			<td class='fila1'>Saldo inicial:</td>
																			<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainpci']) : "&nbsp;&nbsp;")."</td>
																			<td class='fila1'>Movimiento mensual:</td>
																			<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainpcm']) : "&nbsp;&nbsp;")."</td>
																			<td class='fila1'>Saldo final:</td>
																			<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainpcf']) : "&nbsp;&nbsp;")."</td>
																		</tr>
																	</table>
																</fieldset>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Valuación</legend>
										<table width='100%'>
											<tr>
												<td class='fila1' width='13%'>Método de valuación:</td>
												<td class='fila2'>
												<select tipo='' id='inMetodoValuacion' style='width:140px;font-size: 8pt;' ".((!array_key_exists('Ainmva', $camposPermisoEditar)) ? 'disabled' : '').">
													<option value=''>Seleccione...</option>";
												foreach($arrayMetodValu as $metodValuCod => $metodValuNom)
										echo " 		<option value='".$metodValuCod."' ".((((isset($infoActivo) && $infoActivo['Ainmva'] == $metodValuCod) ? "SELECTED" : ""))).">".$metodValuNom."</option>";
										echo "	</select>
												</td>
												<td class='fila1' width='13%'>Valor razonable:</td>
												<td class='fila2'><input type='text' tipo='' size='10' id='inValorRazonable' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainvrv']."'" : "".((isset($camposEnCero['Ainvrv'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Ainvrv', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1' width='13%'>Fecha de valuación:</td>
												<td class='fila2'><input type='text' tipo='' size='11' campoFecha='si' id='inFechaValuacion' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainfva']."'" : "")." ".((!array_key_exists('Ainfva', $camposPermisoEditar)) ? 'disabled' : '')."></td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Revaluación</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrei']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrem']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainref']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Deterioro</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Valor/Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindsi']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindmm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindsf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Períodos de depreciación NIIF</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Vida útil:</td>
												<td class='fila2'><input type='text' tipo='obligatorio' id='vidaUtilPerioDepre' class='entero' ".((isset($infoActivo)) ? "value='".$infoActivo['Ainvup']."'" : "".((isset($camposEnCero['Ainvup'])) ? 'value="0"' : '')."")." ".((!array_key_exists('Ainvup', $camposPermisoEditar)) ? 'disabled' : '')."></td>
												<td class='fila1'>Períodos depreciados:</td>
												<td class='fila2'>".(isset($infoActivo) ? $infoActivo['Ainpdn'] : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Périodos pendientes:</td>
												<td class='fila2'>".(isset($infoActivo) ? $infoActivo['Ainppd'] : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Depreciación costo NIIF</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindci']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindcm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Depreciación acumunlada:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindcf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Reexpresión depreciación costo NIIF por revaluación</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrdi']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrdm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Depreciación acumunlada:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainrdf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Depreciación costos futuros</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindfi']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindfm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Depreciación acumulada:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindff']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Depreciación deterioro</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindei']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindem']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Depreciación acumunlada:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindea']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8' align='center'>
						<br>
						<table width='100%'>
							<tr>
								<td width='100%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Depreciación de la revaluación</legend>
											<table width='100%'>
												<tr>
													<td width='50%'>
														<fieldset align='center' id='' style='padding:12px;'>
															<legend class='fieldset'>Depreciación de reexp. del costo</legend>
															<table width='100%'>
																<tr>
																	<td class='fila1'>Saldo inicial:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindri']) : "&nbsp;&nbsp;")."</td>
																	<td class='fila1'>Movimiento mensual:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindrm']) : "&nbsp;&nbsp;")."</td>
																	<td class='fila1'>Depreciación acumulada:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindrf']) : "&nbsp;&nbsp;")."</td>
																</tr>
															</table>
														</fieldset>
													</td>
													<td width='50%'>
														<fieldset align='center' id='' style='padding:12px;'>
															<legend class='fieldset'>Depreciación de reexp. de adiciones</legend>
															<table width='100%'>
																<tr>
																	<td class='fila1'>Saldo inicial:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindai']) : "&nbsp;&nbsp;")."</td>
																	<td class='fila1'>Movimiento mensual:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindam']) : "&nbsp;&nbsp;")."</td>
																	<td class='fila1'>Depreciación acumulada:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Aindaf']) : "&nbsp;&nbsp;")."</td>
																</tr>
															</table>
														</fieldset>
													</td>
												</tr>
												<tr>
													<td width='50%'>
														<br>
														<fieldset align='center' id='' style='padding:12px;'>
															<legend class='fieldset'>Depreciación de reexp. de depreciación</legend>
															<table width='100%'>
																<tr>
																	<td class='fila1'>Saldo inicial:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainddi']) : "&nbsp;&nbsp;")."</td>
																	<td class='fila1'>Movimiento mensual:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainddm']) : "&nbsp;&nbsp;")."</td>
																	<td class='fila1'>Depreciación acumulada:</td>
																	<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainddf']) : "&nbsp;&nbsp;")."</td>
																</tr>
															</table>
														</fieldset>
													</td>
													<td width='50%'>
													</td>
												</tr>
											</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='8'>
						<br>
						<table width='100%'>
							<tr>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Superávit por revaluación:</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Valor/Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainsri']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainsrm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainsrf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
								<td width='50%'>
									<fieldset align='center' id='' style='padding:12px;'>
										<legend class='fieldset'>Valor en libros NIIF:</legend>
										<table width='100%'>
											<tr>
												<td class='fila1'>Saldo inicial:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainvli']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Movimiento mensual:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainvlm']) : "&nbsp;&nbsp;")."</td>
												<td class='fila1'>Saldo final:</td>
												<td class='fila2'>".(isset($infoActivo) ? formato_numero($infoActivo['Ainvlf']) : "&nbsp;&nbsp;")."</td>
											</tr>
										</table>
									</fieldset>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan='8' align='right'><br><div id='div_mensajes3' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td></tr>";

				$arrPerAct = traerPeriodoActual();
				$arrPerAct = implode('-', $arrPerAct);
				$modificar = (($periodoVer == $arrPerAct) ? "" : "disabled='disabled'");
		echo"	<tr><td colspan='8' align='center'>
						<input type='hidden' id='idInfoNiff' value='".((isset($infoActivo)) ? $infoActivo['id'] : "")."'>
						<button id='buttonGuardarNif' ".$modificar." style='font-family: verdana;font-weight:bold;font-size: 10pt;' onclick='guardarInforNiff(this, \"".$registroActivo."\", \"".$componente."\", \"".((isset($infoActivo)) ? $infoActivo['id'] : "")."\")'>".((!isset($infoActivo)) ? "Guardar" : "Actualizar")."</button>
					</td>
				</tr>
			</table>
		</fieldset>
		<br>";

	}

	//------------------------------------------------------------
	//	--> Contenedor del formulario de informacion Niif-Nic
	//------------------------------------------------------------
	function contenedorFormularioInfoNiif($registroActivo, $componenteVer='*', $periodo)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		// --> Consultar informacion del activo
		$arrayInfoActivo = array();
		$sqlInfoActivo = "SELECT Actnom
						    FROM ".$wbasedato."_000001
						   WHERE Actreg = '".$registroActivo."'
		";
		$resInfoActivo = mysql_query($sqlInfoActivo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoActivo):</b><br>".mysql_error());
		if($rowInfoActivo = mysql_fetch_array($resInfoActivo))
		{
			$arrayInfoActivo = $rowInfoActivo;
			// --> Codificar tildes y caracteres especiales
			foreach($arrayInfoActivo as $indice => &$valor)
				$valor = utf8_encode($valor);
		}

		// --> Consultar componentes del activo
		$arrayCompoActivos = array();

		$sqlInfoCompo = "SELECT Aincom, Ainnco
						   FROM ".$wbasedato."_000003
						  WHERE Ainreg = '".$registroActivo."'
						    AND Aincom != '*'
							AND Ainest = 'on'
						  ORDER BY Aincom
		";
		$resInfoCompo = mysql_query($sqlInfoCompo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoCompo):</b><br>".mysql_error());
		while($rowInfoCompo = mysql_fetch_array($resInfoCompo))
		{
			$arrayCompoActivos[$rowInfoCompo['Aincom']] = $rowInfoCompo;
			// --> Codificar tildes y caracteres especiales
			foreach($arrayCompoActivos[$rowInfoCompo['Aincom']] as $indice => &$valor)
				$valor = utf8_encode($valor);
		}
		$camposPermisoEditar = permisosParaEditarCampos();
		$perIngresarActivo	 = ((isset($camposPermisoEditar['actfij']['000001']['Actact'])) ? TRUE : FALSE);

		echo "
		".convenciones($registroActivo, $periodo, $perIngresarActivo)."
		<fieldset align='left' id='divDetalleComponentes' style='padding:15px;".((count($arrayCompoActivos) > 0) ? "" : "display:none")."'>
			<legend class='fieldset'>Detalle por componentes</legend>
			<table width='50%' id='tablaDetalleComponentes'>
				<tr>
					<td id='comp_PRINCIPAL' colspan='3' class='".(($componenteVer == '*') ? "tdComponenteSeleccionado" : "tdComponente")."'>
						<table width='100%'>
							<tr>
								<td style='cursor:pointer' onclick='verComponente(\"".$registroActivo."\", \"*\", \"".$periodo."\");'>&nbsp;&nbsp;<b>".$arrayInfoActivo['Actnom']."</b></td>
								<td width='30%' align='right'>
									<button style='font-family: verdana;font-weight:bold;font-size: 8pt;' id='botonAgregarComponente' onclick='verComponente(\"".$registroActivo."\", \"nuevo\", \"".$periodo."\");'>Agregar</button>
								</td>
							</tr>
						</table>
					</td>
				</tr>";
			foreach($arrayCompoActivos as $componente => $valoresCompo)
			{
				echo "
				<tr><td width='3%'></td><td width='4%' style='border-left:1px solid #62BBE8;color:#62BBE8;'>----------</td><td id='comp_".$componente."' onClick='verComponente(\"".$registroActivo."\", \"".$componente."\", \"".$periodo."\")' class='".(($componenteVer == $componente) ? "tdComponenteSeleccionado" : "tdComponente")."' >&nbsp;&nbsp;".$componente."-".$valoresCompo['Ainnco']."</td></tr>";
			}
		echo"
			</table>
		</fieldset>
		<br>
		<div id='divFormularioInfoNiif'>";

		formularioInfoNiif($registroActivo, $componenteVer, $periodo);

		echo "</div>";
	}

	//---------------------------------------------------
	//	--> Tabla de convenciones para los formularios
	//---------------------------------------------------
	function convenciones($registroActivo, &$periodoVer='', $perIngresarActivo)
	{
		global $wfecha;
		global $conex;
		global $wbasedato;

		$array_meses 		= array( '01'=>'Ene', '02'=>'Feb', '03'=>'Mar', '04'=>'Abr', '05'=>'May', '06'=>'Jun', '07'=>'Jul', '08'=>'Ago', '09'=>'Sep', '10'=>'Oct', '11'=>'Nov', '12'=>'Dic');
		$wperiodo			= (($periodoVer == '') ? date("Y-m") : $periodoVer);
		$wmes 				= explode('-',$wperiodo);
		$año  				= $wmes[0];
		$wmes 				= 12;
		$wcantidad_meses 	= 12;

		$arrPeriodoActual	= explode('-', date("Y-m-d"));
		$periodoVer			= (($periodoVer != '') ? $periodoVer : $arrPeriodoActual[0].'-'.$arrPeriodoActual[1]);

		$infoPerAct = traerPeriodoActual();

		// --> Consultar si se puede pintar boton para cancelar el ingreso del activo
		$pintarCanceIngr = "display:none";
		if($perIngresarActivo && $infoPerAct['ano'].'-'.$infoPerAct['mes'] == $periodoVer)
		{
			$periVer = explode('-',$wperiodo);

			// --> Validar si el periodo actual es el mismo periodo en el que se ingreso el activo
			$sqlPerIng = " SELECT id
							 FROM ".$wbasedato."_000016
							WHERE Movano = '".$periVer[0]."'
							  AND Movmes = '".$periVer[1]."'
							  AND Movreg = '".$registroActivo."'
							  AND Movtip = 'Ingreso'
							  AND Movest = 'on'
			";
			$resPerIng = mysql_query($sqlPerIng, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPerIng):</b><br>".mysql_error());
			if($rowPerIng = mysql_fetch_array($resPerIng))
			{
				$pintarCanceIngr = "";
				$idMovIng		 = $rowPerIng['id'];
			}
		}

		// --> Pintar tabla para seleccionar meses
		echo '
		<table width="100%" id="tablaMeses">
			<tr><td colspan="12" style="font-size: 11px;" align="right"><b>Periodo Actual</b>: '.$infoPerAct['ano'].'-'.$infoPerAct['mes'].'</td></tr>
			<tr>
				<td  class="cuadroMes" style="cursor:default;font-size: 10px;" colspan="12"><b>'.$año.'</b></td>
				<td width="23px" rowspan="2" align="right"><img id="calendar" width="22" height="22" src="../../images/medical/sgc/Calendar.png" onclick="calendarioSeleccionarPeriodo(this);" style="cursor:pointer;"></td>
			</tr>';
		foreach($array_meses as $numMes => $nomMes)
			echo '<td class="'.(($periodoVer == $año.'-'.$numMes) ? 'cuadroMesSeleccionado' : 'cuadroMes').'" onClick="seleccionarPeriodoDesdeCalendario(\''.$año.'\', \''.$numMes.'\')" style="cursor:pointer;font-size: 10px;width:8.3%" id="'.$año.'-'.$numMes.'">'.$nomMes.'</td>';
		echo'
			</tr>
		</table>';

		$title = "
		<table style=\"font-weight:normal\">
			<tr>
				<td align=\"center\">
					Esto permitirá ejecutar el proceso de ingreso del activo
					<br>y que este quede habilitado para el sistema. Antes de
					<br>realizar el ingreso, por favor verifique que toda la
					<br>información necesaria del activo este diligenciada.
				</td>
			</tr>
		</table>
		";
		// --> Pintar tabla de convenciones
		echo "<br>
		<table width='100%' style='margin-bottom:4px'>
			<tr>
				<td width='50%' align='right'>
				</td>
				<td width='50%' align='right'>
					<table>
						<tr>
							<td  style='".$pintarCanceIngr."'>
								<button class='botonAnularIngreso' style='cursor:pointer;padding:1px;font-family: verdana;font-weight:bold;font-size: 8pt;' onclick='anularIngreso(\"".$registroActivo."\", \"".$idMovIng."\")'>
									<img width='11' height='11' src='../../images/medical/eliminar1.png'>
									Anular ingreso
								</button>
							</td>
							<td style='border-color:#FF9900;border-style:solid;border-width:0.1em;background:#F2F5F7;font-size:8pt;color:#919191;'>&nbsp;Campo habilitado para ingresar&nbsp;</td>
							<td style='padding:3px;border: 1px solid #919191;color:#919191; background:#FFFFEE;font-size:8pt'>&nbsp;Campo Obligatorio&nbsp;</td>
							<td style='padding:3px;border: 1px solid #919191;color:#919191; background:#FFFFFF;font-size:8pt'>&nbsp;No Obligatorio&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";

		// --> Cuadro flotante para ejecutar el proceso de ingreso del activo
		echo "
		<div id='ingresoFlotante' style='width:250px;cursor:move;display:none;z-index:900;position: fixed;'>
			<table style='background:#ffffcc;font-size:10pt;padding:3px;border: 1px solid #2A5DB0;border-radius: 5px;'>
				<tr>
					<td align='center'>
						&nbsp;<img width='13' height='13' tooltip='si' style='cursor:help' title='".$title."' src='../../images/medical/sgc/info.png'>&nbsp;&nbsp;<b>¿ Realizar ingreso del activo ?</b>
					</td>
				<tr>
				<tr><td align='center'>Si <input type='radio' name='realizarIngreso' id='siRealizarIngreso' style='cursor:pointer'>&nbsp;&nbsp; No <input type='radio' checked='checked' name='realizarIngreso' style='cursor:pointer'></td></tr>
			</table>
		</div>
		";
	}
	//---------------------------------------------------
	//	Pintar la lista de los activos
	//---------------------------------------------------
	function listarActivos()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$arrayActivos 			= array();
		$arrayCodigosRegistros 	= array();
		$wbasedatoFac 			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		$infoPerAct 			= traerPeriodoActual();

		// --> Consultar activos
		$sqlLista = "SELECT Actreg, Actnom, Actpla, Grunom, Actfad, Estnom, Actubi, MAX(concat(Actano, Actmes)), Actact
					   FROM ".$wbasedato."_000001 as A LEFT JOIN ".$wbasedato."_000004 as B on A.Actest = B.Estcod, ".$wbasedato."_000008
					  WHERE Actano = '".$infoPerAct['ano']."'
					    AND Actmes = '".$infoPerAct['mes']."'
					    AND Actgru = Grucod
					  GROUP BY Actreg
					  ORDER BY Actact, Actreg
		";
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_array($resLista))
		{
			// --> Codificar tildes y caracteres especiales
			foreach($rowLista as $indice => &$valor)
				$valor = utf8_encode($valor);

			// --> Armar array de lista de activos
			$arrayActivos[$rowLista['Actreg']] = $rowLista;
			array_push($arrayCodigosRegistros, "'".$rowLista['Actreg']."'");
		}

		// --> Traer los centros de costos
		if(count($arrayCodigosRegistros) > 0)
		{
			$arrPerAct = traerPeriodoActual();
			$sql = "SELECT Ccareg as reg, Ccacco as cco, Ccodes as nom
				      FROM ".$wbasedato."_000017, ".$wbasedatoFac."_000003
				     WHERE Ccareg IN (".implode(",",$arrayCodigosRegistros).")
					   AND Ccaano = '".$arrPerAct['ano']."'
					   AND Ccames = '".$arrPerAct['mes']."'
					   AND Ccocod = Ccacco ";
			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
			while($row = mysql_fetch_array($res))
			{
				if( array_key_exists( "ccos", $arrayActivos[$row['reg']] ) == false ){
					$arrayActivos[$row['reg']]["ccos"] = array();
				}
				$cco = array( "codigo"=>$row['cco'] , "nombre"=>ucfirst(strtolower($row['nom'])));
				array_push(  $arrayActivos[$row['reg']]["ccos"], $cco );
			}
		}

		// --> Pintar lista
		echo "
		<fieldset align='center' style='padding:15px;margin:15px'>
			<legend class='fieldset'>Lista de activos</legend>
				<table width='100%'>
					<tr>
						<td align='left' width='50%'>
							<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
								Buscar:&nbsp;&nbsp;</b><input id='buscarActivo' type='text' tipo='obligatorio' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'>
							</span>
						</td>
						<td align='center' width='10%' style='padding:3px;border-radius: 4px;border:1px solid #AFAFAF;color:#919191;font-size:8pt'>
							<img width='13' height='13' src='../../images/medical/sgc/Mensaje_alerta.png' />
							Activo pendiente de ingreso
						</td>
						<td align='right' width='5%'><button style='font-family: verdana;font-weight:bold;font-size: 10pt;' onclick='verActivo(\"nuevo\", \"InfoGeneral\", \"*\", \"\")'>Nuevo</button></td>
					</tr>
				</table>
			<div style='height:220px;overflow:auto;background:none repeat scroll 0 0;'>
				<table width='100%' id='tablaListaActivos'>
					<tr class='encabezadoTabla' align='center'>
						<td style='background-color: #F2F5F7;'></td><td>Registro</td><td>Nombre</td><td>Placa</td><td>Grupo</td><td>Fec.Adquisición</td><td>Estado</td><td>Centro de costos</td><td>Ubicación</td><td>Ingresado</td>
					</tr>
				";
				$colorFila = 'fila1';

				foreach($arrayActivos as $registroAct => $valoresAct)
				{
					$json_cco = "";
					if(isset( $valoresAct['ccos'] ))
						$json_cco = json_encode( $valoresAct['ccos'] );

					$oculto_cco = "<input type='hidden' class='ccos' value='".$json_cco."' >";

					$mostrarCco = "";
					if(count( $valoresAct['ccos'] ) == 1 )
						$mostrarCco = $valoresAct['ccos'][0]['codigo']." - ".$valoresAct['ccos'][0]['nombre'];
					elseif(count( $valoresAct['ccos'] ) > 1 )
							$mostrarCco = "<span style='cursor:pointer;font-weight:bold' onclick='listarCcosEnListaActivos(this,".$json_cco.")'><img src='../../images/medical/hce/mas2.png'>&nbsp;&nbsp;Detallar</span>";

					$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1');

					$accionClick = "onClick='verActivo(\"".$valoresAct['Actreg']."\", \"\", \"*\", \"\")'";
					$accionClickTr = "";
					if( count( $valoresAct['ccos'] ) == 1 ){ //Si hay un solo centro de costos, el onclick es para el tr
						$accionClickTr = $accionClick;
						$accionClick = "";
					}

					$imgAlerta = (($valoresAct['Actact']=='on') ? "" : "<img width='15' height='15' src='../../images/medical/sgc/Mensaje_alerta.png' />");

					echo"
					<tr ".$accionClickTr." class='find tooltip ".$colorFila."' style='cursor:pointer;color:".$colorTexto."' title='<span style=\"font-weight:normal\">Click para detallar</span>'>
						<td style='background-color: #F2F5F7;'>".$imgAlerta."</td>
						<td ".$accionClick.">".$valoresAct['Actreg']."</td>
						<td ".$accionClick.">".$valoresAct['Actnom']."</td>
						<td ".$accionClick.">".$valoresAct['Actpla']."</td>
						<td ".$accionClick.">".$valoresAct['Grunom']."</td>
						<td ".$accionClick.">".$valoresAct['Actfad']."</td>
						<td ".$accionClick.">".$valoresAct['Estnom']."</td>
						<td class='".$colorFila."'>".$mostrarCco."".$oculto_cco."</td>
						<td ".$accionClick.">".$valoresAct['Actubi']."</td>
						<td ".$accionClick." align='center'>".(($valoresAct['Actact']=='on') ? 'Si' : '<b>No</b>' )."</td>
					</tr>
					";
				}

				echo"
				</table>
			</div>
			<table width='100%'>
				<tr>
					<td align='right'>
						<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
							N° Activos: ".count($arrayActivos)."
						</span>
					</td>
				</tr>
			</table>
		</fieldset>";
	}

	//------------------------------------------------------------
	//	Realiza el ingreso de un activo
	//------------------------------------------------------------
	function registrarIngresoDelActivo($perAno, $perMes, $igRegistro)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wfecha;
		global $whora;

		$respuesta 				= array("Error" => FALSE, "Mensaje" => "");
		$activarAct 			= false;
		$arrayCompo['*'] 		= '';
		$guardarIngresoPadre	= false;

		// --> Obtener componentes del activo
		$sqlCompoAct = "SELECT Aincom
						  FROM ".$wbasedato."_000003
						 WHERE Ainano = '".$perAno."'
						   AND Ainmes = '".$perMes."'
						   AND Ainreg = '".$igRegistro."'
						   AND Aincom != '*'
						   AND Ainest = 'on'
		";
		$resCompoAct = mysql_query($sqlCompoAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCompoAct):</b><br>".mysql_error());
		while($rowCompoAct = mysql_fetch_array($resCompoAct))
			$arrayCompo[$rowCompoAct['Aincom']] = '';

		if(mysql_num_rows($resCompoAct) == 0)
			$guardarIngresoPadre = true;

		foreach($arrayCompo as $componente => $valor)
		{
			// --> Ejecutar formulas configuradas para la transacción ingreso
			$infoRespuesta = ejecutarTransaccion($igRegistro.'-'.$componente, $perAno.'-'.$perMes, 'codProcesoIngresoPPYE');
			if(count($infoRespuesta['Errores']) > 0)
			{
				$respuesta['Mensaje'] 	= implode("  ", $infoRespuesta['Errores']);
				$respuesta['Error'] 	= TRUE;
			}
			else
			{
				$activarAct = true;

				// --> 	Cuando el activo esta por componentes no genero registro de ingreso para el padre, sino solo
				//		para los componentes y si no esta por componentes ahi si le genero ingreso al padre.
				if($componente == '*' && !$guardarIngresoPadre)
					continue;

				// --> Guardar registro del movimiento del ingreso del activo
				$sqlGuardarMov = "
				INSERT INTO ".$wbasedato."_000016
						(Medico,			Fecha_data,    	Hora_data,		Movano, 		Movmes, 		Movreg, 			Movtip,		Movcom, 			Movmre,	Movest,	Seguridad, 		id)
				VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$perAno."',	'".$perMes."', 	'".$igRegistro."',	'Ingreso',	'".$componente."',	'*',	'on',	'C-".$wuse."',	'')
				";
				mysql_query($sqlGuardarMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGuardarMov):</b><br>".mysql_error());
			}
		}

		if($activarAct)
		{
			// --> Activar el activo
			$sqlAct = "UPDATE ".$wbasedato."_000001
						  SET Actact = 'on'
						WHERE Actreg = '".$igRegistro."'
						  AND Actano = '".$perAno."'
						  AND Actmes = '".$perMes."'
			";
			mysql_query($sqlAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAct):</b><br>".mysql_error());
		}

		return $respuesta;
	}
	//------------------------------------------------------------
	//	Obtener array con los campos que un usuario puede editar
	//------------------------------------------------------------
	function permisosParaEditarCampos()
	{
		global $conex;
		global $wbasedato;
		global $wuse;

		$camposPermisoEditar = array();

		// --> Consultar el rol del usuario
		$sqlRol = "  SELECT Rusrol
					   FROM ".$wbasedato."_000020
					  WHERE Rususu = '".$wuse."'
					    AND Rusest = 'on'
		";
		$resRol = mysql_query($sqlRol, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlRol):</b><br>".mysql_error());
		if($rowRol = mysql_fetch_array($resRol))
		{
			$permisoIngreso = 'off';
			$rol = $rowRol['Rusrol'];

			// --> Consultar los campos que puede editar el rol del usuario
			$sqlCam = "  SELECT Permed, Percod, Percam, Perrol
						   FROM ".$wbasedato."_000019
						  WHERE Perrol LIKE '%".$rol."%'
							AND Perest = 'on'
			";
			$resCam = mysql_query($sqlCam, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCam):</b><br>".mysql_error());
			while($rowCam = mysql_fetch_array($resCam))
			{
				$tienePermiso 	= false;
				$arrayRoles 	= explode('-', $rowCam['Perrol']);
				foreach($arrayRoles as $codRol)
					$tienePermiso = ((trim($codRol) == trim($rol)) ? true : $tienePermiso);

				// --> Asignar el permiso de edición
				if($tienePermiso)
				{
					$camposPermisoEditar[$rowCam['Permed']][$rowCam['Percod']][$rowCam['Percam']] = '';

					// --> Campo que permite pintar flotante para realizar el ingreso del activo
					if($rowCam['Percod'].$rowCam['Percam'] == '000001Actact')
						$permisoIngreso = 'on';
				}
			}

			echo "<input type='hidden' id='permisoParaRealizarIngreso' value='".$permisoIngreso."'>";
		}

		return $camposPermisoEditar;
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
		case 'guardarInfoGeneral':
		{
			$respuesta = array();

			$periodo = explode('-', $igPeriodo);
			$perAno = $periodo[0];
			$perMes = $periodo[1];

			// --> Insertar un nuevo activo
			if($registroActivo == 'nuevo')
			{
				// --> Crear el numero de registro
				$igRegistro = $regModalidad.$regGrupo.$regSubGrupo;

				// --> Obtener el ultimo consecutivo
				$sqlConsec = "SELECT REPLACE(Actreg, '".$regModalidad.$regGrupo.$regSubGrupo."', '')*1 ultimoConse
							    FROM ".$wbasedato."_000001
							   WHERE Actmoa = '".$regModalidad."'
							     AND Actgru = '".$regGrupo."'
							     AND Actsub = '".$regSubGrupo."'
							   GROUP BY Actreg
							   ORDER BY ultimoConse DESC
				";
				$resConsec  	= mysql_query($sqlConsec, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsec):</b><br>".mysql_error());
				$rowConsec  	= mysql_fetch_array($resConsec);

				$ultimoConse	= str_replace($igRegistro, '', $rowConsec['ultimoConse'])+1;

				// --> Asignar ceros a la izquierda hasta completar 3 digitos
				while(strlen($ultimoConse) < 5)
					$ultimoConse = '0'.$ultimoConse;

				$igRegistro.=$ultimoConse;

				// --> Guardar el activo
				$sqlGuardarInfoGen = "
				INSERT INTO ".$wbasedato."_000001
						(Medico,			Fecha_data,    	Hora_data,		Actano,			Actmes,			Actreg, 			Actnom,							Actpla,			Actemp,				Actpro,					Actfoc,				Actmar, 					Actser,				Actmod,							Actdes, 							Actnue, 		Actfad, 			Actfps,					Actest,				Actubi, 							Actase,					Actpol,				Actvas,				Actdep,						Actmoa,					Actgru,				Actsub,		 		Seguridad, 		id)
				VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$perAno."',	'".$perMes."',	'".$igRegistro."',	'".utf8_decode($igNombre)."',	'".$igPlaca."',	'".$igCompañia."',	'".$igProveedores."',	'".$igFactura."',	'".utf8_decode($igMarca)."', '".$igSerial."',	'".utf8_decode($igModelo)."',	'".utf8_decode($igDescripcion)."',	'".$igNuevo."',	'".$igFechaAdqui."','".$igFechaMarcha."',	'".$igEstado."',	'".utf8_decode($igUbicacion)."',	'".$igAseguradora."',	'".$igNumPoliza."',	'".$igValorAseg."',	'".$igDespreciableSi."',	'".$regModalidad."',	'".$regGrupo."',	'".$regSubGrupo."',	'C-".$wuse."',	'')
				";
				mysql_query($sqlGuardarInfoGen, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlGuardarInfoGen):</b><br>".mysql_error());

				inicializarCamposEnCero("000001", mysql_insert_id());

				$registroActivo = $igRegistro;
				$mensaje = 'Activo creado';
			}
			// --> Actualizar informacion del activo
			else
			{
				$sqlUpdateInfoGen = "
				UPDATE ".$wbasedato."_000001
				   SET Actnom = '".utf8_decode($igNombre)."',
					   Actpla = '".$igPlaca."',
					   Actemp = '".$igCompañia."',
					   Actpro = '".$igProveedores."',
					   Actfoc = '".$igFactura."',
					   Actmar = '".utf8_decode($igMarca)."',
					   Actser = '".$igSerial."',
					   Actmod = '".utf8_decode($igModelo)."',
					   Actdes = '".utf8_decode($igDescripcion)."',
					   Actnue = '".$igNuevo."',
					   Actfad = '".$igFechaAdqui."',
					   Actfps = '".$igFechaMarcha."',
					   Actest = '".$igEstado."',
					   Actubi = '".utf8_decode($igUbicacion)."',
					   Actase = '".$igAseguradora."',
					   Actpol = '".$igNumPoliza."',
					   Actvas = '".$igValorAseg."',
					   Actdep = '".$igDespreciableSi."'
				 WHERE Actano = '".$perAno."'
				   AND Actmes = '".$perMes."'
				   AND Actreg = '".$registroActivo."'
				";
				mysql_query($sqlUpdateInfoGen, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlUpdateInfoGen):</b><br>".mysql_error());

				$mensaje = 'Activo actualizado';
			}

			// --> Guardar los centros de costos con los que se relaciona el activo
			$listaCentrosCostos = json_decode(str_replace('\\', '', $listaCentrosCostos), true);

			// --> Borrar todas las relaciones que tenga
			$sqlBorrar = "
			DELETE FROM ".$wbasedato."_000017
				  WHERE Ccareg = '".$registroActivo."'
					AND Ccaano = '".$perAno."'
					AND Ccames = '".$perMes."'
			";
			mysql_query($sqlBorrar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlBorrar):</b><br>".mysql_error());

			// --> Insertar las relaciones
			foreach($listaCentrosCostos as $valores)
			{
				$sqlCcoAct = "
				INSERT INTO ".$wbasedato."_000017
						(Medico,			Fecha_data,    	Hora_data,		Ccareg,					Ccaano,			Ccames, 		Ccacco,					Ccapor,						Ccaest, 					Seguridad, 		id)
				VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$registroActivo."',	'".$perAno."',	'".$perMes."',	'".$valores['cco']."',	'".$valores['porcent']."',	'".$valores['estados']."',	'C-".$wuse."',	'')
				";
				mysql_query($sqlCcoAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoAct):</b><br>".mysql_error());

			}

			// --> Se realiza el ingreso del activo en el sistema
			if($siRealizarIngreso == "on")
			{
				$respuestaReg = registrarIngresoDelActivo($perAno, $perMes, $registroActivo);
				if($respuestaReg["Error"])
					$mensaje.= "<br><b>No se pudo realizar el proceso de ingreso, Causa: </b>&nbsp;".$respuestaReg["Mensaje"];
			}

			$respuesta['mensaje'] 			= $mensaje;
			$respuesta['registroActivo'] 	= $registroActivo;

			echo json_encode($respuesta);
			break;
		}
		case 'verFormulario':
		{
			switch($formulario)
			{
				case 'InfoGeneral':
				{
					formularioInfoGeneral($registroActivo, $periodo);
					break;
				}
				case 'InfoFiscal':
				{
					formularioInfoFiscal($registroActivo, $periodo);
					break;
				}
				case 'InfoNiif':
				{
					contenedorFormularioInfoNiif($registroActivo, $componenteVer, $periodo);
					break;
				}
			}
			break;
		}
		case 'cargarSelectSubgrupos':
		{
			$html = "<option value=''>Seleccione...</option>";
			// -->	Obtener los sub-grupos de los activos relacionado a un determinado grupo
			$sqlSubGrupos = " SELECT Subcod, Subnom
							    FROM ".$wbasedato."_000009
							   WHERE Subgru = '".$grupo."'
							GROUP BY Subcod
						    ORDER BY Subnom ";
			$resSubGrupos = mysql_query($sqlSubGrupos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSubGrupos):</b><br>".mysql_error());
			$arrSubGrupos = array();
			while($rowSubGrupos = mysql_fetch_array($resSubGrupos))
				$arrSubGrupos[$rowSubGrupos['Subcod']] = utf8_encode($rowSubGrupos['Subnom']);

			foreach($arrSubGrupos as $gruCod => $gruNom)
				$html.= "<option value='".$gruCod."'>".$gruNom."</option>";

			echo $html;
			break;
		}
		case 'guardarInfoFiscal':
		{
			$respuesta 	= array();
			$periodo 	= explode('-', $ifPeriodo);
			$perAno 	= $periodo[0];
			$perMes 	= $periodo[1];

			if(trim($idRegistroInfoFis) != '')
			{
				// --> Actualizar informacion fiscal
				$sqlActuaInfoFis = "
				UPDATE ".$wbasedato."_000002
				   SET Fecha_data 	=	'".$wfecha."',
					   Hora_data	=	'".$whora."',
					   Aifreg		=	'".$registroActivo."',
					   Aifcad		=	'".$ifCostoAdqui."',
					   Aifica		=	'".$ifIva."',
					   Aifaxi		=	'".$ifAxi."',
					   Aifmej		=	'".$ifMejoras."',
					   Aifdac		=	'".$ifDepreAcum."',
					   Aifada		=	'".$ifAxiDepreAcum."',
					   Aifipv		=	'".$ifImpuValorizacion."',
					   Aifvsa		=	'".$ifValorSalvamento."',
					   Aifraf		=	'".$ifSiReqAjusFiscales."',
					   Aifpaf		=	'".$ifPorcenAjusteFiscal."',
					   Aifsir		=	'".$ifReaFisSaldoIni."',
					   Aifmar		=	'".$ifReaFisMoviAnual."',
					   Aifsfr		=	'".$ifReaFisSaldoFin."',
					   Aifaca		=	'".$ifAvaluoCatastral."',
					   Aifmde		=	'".$ifMetodoDepre."',
					   Aifvut		=	'".$ifVidaUtil."',
					   Aifpde		=	'".$ifPeriodosDepre."',
					   Aifsin		=	'".$ifDepreFisSaldoIni."',
					   Aifsiv		=	'".$ifValorLibrosSaldoIni."'
				WHERE 	   id		= 	'".$idRegistroInfoFis."'
				";
				mysql_query($sqlActuaInfoFis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActuaInfoFis):</b><br>".mysql_error());

				$mensaje = 'Información actualizada';
			}
			else
			{
				// --> Insertar informacion fiscal
				$sqlInfoFis = "
				INSERT INTO ".$wbasedato."_000002
							(Medico,			Fecha_data,    	Hora_data,		Aifano,			Aifmes,			Aifreg, 				Aifcad,				Aifica,			Aifaxi,			Aifmej,				Aifdac,				Aifada,					Aifipv,						Aifvsa,						Aifraf,						Aifpaf,						Aifsir,					Aifmar,						Aifsfr,					Aifaca,						Aifmde,					Aifvut,				Aifpde,					Aifsin,						Aifsiv,							Seguridad, 		id)
					VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$perAno."',	'".$perMes."',	'".$registroActivo."',	'".$ifCostoAdqui."','".$ifIva."',	'".$ifAxi."',	'".$ifMejoras."',	'".$ifDepreAcum."',	'".$ifAxiDepreAcum."',	'".$ifImpuValorizacion."',	'".$ifValorSalvamento."',	'".$ifSiReqAjusFiscales."',	'".$ifPorcenAjusteFiscal."','".$ifReaFisSaldoIni."','".$ifReaFisMoviAnual."',	'".$ifReaFisSaldoFin."','".$ifAvaluoCatastral."',	'".$ifMetodoDepre."',	'".$ifVidaUtil."',	'".$ifPeriodosDepre."',	'".$ifDepreFisSaldoIni."', 	'".$ifValorLibrosSaldoIni."',	'C-".$wuse."',	'')
				";
				mysql_query($sqlInfoFis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoFis):</b><br>".mysql_error());

				inicializarCamposEnCero("000002", mysql_insert_id());

				$mensaje = 'Información guardada';
			}

			// --> Se realiza el ingreso del activo en el sistema
			if($siRealizarIngreso == "on")
			{
				$respuestaReg = registrarIngresoDelActivo($perAno, $perMes, $registroActivo);
				if($respuestaReg["Error"])
					$mensaje.= "<br><b>No se pudo realizar el proceso de ingreso, Causa: </b>&nbsp;".$respuestaReg["Mensaje"];
			}

			$respuesta['registroActivo'] 	= $registroActivo;
			$respuesta['mensaje'] 			= $mensaje;
			echo json_encode($respuesta);
			break;
		}
		case 'guardarInfoNiif':
		{
			$respuesta 	= array();
			$periodo 	= explode('-', $inPeriodo);
			$perAno 	= $periodo[0];
			$perMes 	= $periodo[1];

			if(trim($idRegistroInfoNiif) != '')
			{
				// --> Actualizar informacion niif
				$sqlActuaInfoFis = "
				UPDATE ".$wbasedato."_000003
				   SET Fecha_data 	=	'".$wfecha."',
					   Hora_data	=	'".$whora."',
					   Ainreg		=	'".$registroActivo."',
					   Aincom		=	'".$inComponente."',
					   Aindpc		=	'".$detPorComponentesSi."',
					   Ainnco		=	'".$inNombreComponente."',
					   Ainuge		=	'".$inUniGeneraEfec."',
					   Aincad		=	'".$inCostoAtrib."',
					   Ainvsa		=	'".$inValorSalvamento."',
					   Ainmmm		=	'".$inAdicionesMovMen."',
					   Aintcf		=	'".$inTipoCostosfuturos."',
					   Aintes		=	'".$inTiempoEstimadoCosFut."',
					   Ainfre		=	'".$fechaRealCosFut."',
					   Aintaj		=	'".$tipoAjusteCosFut."',
					   Aintap		=	'".$tasaAjustePerCosFut."',
					   Ainvcf		=	'".$inCostosFuturosVal."',
					   Ainvpc		=	'".$inProvisionCostosFuturosVal."',
					   Ainmva		=	'".$inMetodoValuacion."',
					   Ainvrv		=	'".$inValorRazonable."',
					   Ainfva		=	'".$inFechaValuacion."',
					   Ainvup		=	'".$vidaUtilPerioDepre."',
					   Ainmdn		=	'".$inMetodoDepNiif."',
					   Ainode		=	'".$inOrigenDatoEstadis."',
					   Aintde		=	'".$tipoOrigenDatoEstadistico."'
				WHERE 	   id		= 	'".$idRegistroInfoNiif."'
				";
				mysql_query($sqlActuaInfoFis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActuaInfoFis):</b><br>".mysql_error());

				$mensaje = 'Información actualizada';
			}
			else
			{
				if($inComponente == 'nuevo')
				{
					// --> Obtener el consecutivo para el componente
					$sqlConsComp = "SELECT MAX(Aincom) AS Consec
									  FROM ".$wbasedato."_000003
									 WHERE Ainreg = '".$registroActivo."' ";
					$resConsComp = mysql_query($sqlConsComp, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlConsComp):</b><br>".mysql_error());
					$rowConsComp = mysql_fetch_array($resConsComp);
					$inComponente= $rowConsComp['Consec']+1;
				}

				// --> Insertar informacion niif
				$sqlInfoNiif = "
				INSERT INTO ".$wbasedato."_000003
							(Medico,			Fecha_data,    	Hora_data,		Ainano,			Ainmes,			Ainreg, 				Aincom,				Aindpc,						Ainnco,						Ainuge, 				Aincad,					Ainvsa,						Ainmmm,						Aintcf,						Aintes,							Ainfre,					Aintaj,					Aintap,						Ainvcf,						Ainvpc,								Ainmva,						Ainvrv,						Ainfva,						Ainvup,						Ainmdn,					Ainode,						Aintde,								Ainest, Seguridad, 		id)
					VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$perAno."',	'".$perMes."',	'".$registroActivo."',	'".$inComponente."','".$detPorComponentesSi."',	'".$inNombreComponente."',	'".$inUniGeneraEfec."',	'".$inCostoAtrib."',	'".$inValorSalvamento."',	'".$inAdicionesMovMen."',	'".$inTipoCostosfuturos."',	'".$inTiempoEstimadoCosFut."',	'".$fechaRealCosFut."',	'".$tipoAjusteCosFut."','".$tasaAjustePerCosFut."','".$inCostosFuturosVal."',	'".$inProvisionCostosFuturosVal."',	'".$inMetodoValuacion."',	'".$inValorRazonable."',	'".$inFechaValuacion."',	'".$vidaUtilPerioDepre."',	'".$inMetodoDepNiif."',	'".$inOrigenDatoEstadis."',	'".$tipoOrigenDatoEstadistico."',	'on',	'C-".$wuse."',	'')
				";
				mysql_query($sqlInfoNiif, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoNiif):</b><br>".mysql_error());

				inicializarCamposEnCero("000003", mysql_insert_id());

				$mensaje.= 'Información guardada';
			}

			// --> Se realiza el ingreso del activo en el sistema
			if($siRealizarIngreso == "on")
			{
				$respuestaReg = registrarIngresoDelActivo($perAno, $perMes, $registroActivo);
				if($respuestaReg["Error"])
					$mensaje.= "<br><b>No se pudo realizar el proceso de ingreso, Causa: </b>&nbsp;".$respuestaReg["Mensaje"];
			}

			$respuesta['componente'] 		= $inComponente;
			$respuesta['registroActivo'] 	= $registroActivo;
			$respuesta['mensaje'] 			= $mensaje;

			echo json_encode($respuesta);
			break;
		}
		case 'pintarListadeActivos':
		{
			echo listarActivos();
			break;
		}
		case 'verDetallePorComponente':
		{
			echo formularioInfoNiif($registroActivo, $componente, $periodo);
			break;
		}
		case 'anularIngreso':
		{
			// --> Inactivar el registro del movimiento
			$sqlInactiMov = " UPDATE ".$wbasedato."_000016
								 SET Movest = 'off'
							   WHERE id = '".$idMovIngresoAnt."'
			";
			mysql_query($sqlInactiMov, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInactiMov):</b><br>".mysql_error());

			// --> Actualizar el activo como no ingresado
			$sqlActualAct = " UPDATE ".$wbasedato."_000001
								 SET Actact = 'off'
							   WHERE Actreg = '".$registroAct."'
			";
			mysql_query($sqlActualAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlActualAct):</b><br>".mysql_error());
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
	?>
	<html>
	<head>
	  <title>...</title>
	</head>

		<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>

	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	var url_add_params = addUrlCamposCompartidosTalento();

	$(function(){
		// --> Activar tabs jaquery
		$( "#tabs" ).tabs({
			heightStyle: "content"
		});

		// --> Activar el buscador de texto, para los activos
		$('#buscarActivo').quicksearch('#tablaListaActivos .find');

		// --> Tooltip
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		// --> Parametrización del datapicker
		cargar_elementos_datapicker();
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

	//--------------------------------------------------
	//	Le activa un seleccionador de fecha a un input
	//--------------------------------------------------
	function activarSeleccionadorFecha(elemento)
	{
		periodoAct 	= $("#hiddenPeriodoSeleccionado").val();
		arrPeriodo 	= periodoAct.split("-");
		minFecha	= periodoAct+"-01";
		maxFecha	= new Date(arrPeriodo[0], arrPeriodo[1], 0);

		$("#"+elemento).datepicker({
			showOn: "button",
			buttonImage: "../../images/medical/root/calendar.gif",
			buttonImageOnly: true,
			minDate: minFecha,
			maxDate: maxFecha,
			defaultDate: periodoAct+"-01"
		});
		$("#"+elemento).next().css({"cursor": "pointer"}).attr("title", "Seleccione");
		$("#"+elemento).after("&nbsp;");
	}

	//------------------------------------------------------------------------------
	//	Funcion que valida valores enteros en un campo y le da formato de miles
	//------------------------------------------------------------------------------
	function activar_regex(Contenedor)
	{
		// --> cada vez que digiten en el input
		$('.entero', Contenedor).keyup(function(){
			if($(this).val() != "")
			{
				num = $(this).val().replace(/\,/g,'');
				num = num.replace(/\./g,'');
				num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
				num = num.split('').reverse().join('').replace(/^[\,]/,'');
				$(this).val(num);
			}
		});

		// --> Cuando se pinta el formulario
		$('.entero', Contenedor).each(function(){
			if($(this).val() != "")
			{
				num = $(this).val().replace(/\,/g,'');
				num = num.replace(/\./g,'');
				num = num.toString().split('').reverse().join('').replace(/(?=\d*\,?)(\d{3})/g,'$1,');
				num = num.split('').reverse().join('').replace(/^[\,]/,'');
				$(this).val(num);
			}
		});
	}

	//-----------------------------------------------------------------
	//	Funcion guarda el BD el formulario de informacion general
	//-----------------------------------------------------------------
	function guardarInforGeneral(Boton, registroActivo)
	{
		// --> Deshabilitar el boton grabar hasta que termine el proceso
		boton = jQuery(Boton);
		boton.html('&nbsp;<span style="font-size:10px;font-weight:normal">Guardando...</span>&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" >').attr("disabled","disabled");

		$('#tablaInfoGeneral .campoObligatorio').each(function(){
			$(this).removeClass('campoObligatorio');
			if($(this).attr("disabled") == undefined)
				$(this).addClass("campoHabilitado");
		});

		$('#tablaInfoGeneral .entero').each(function(){
			if($(this).val() != "")
				$(this).val($(this).val().replace(/\,/g,''));
		});

		var permitirGuardar = true;

		// --> Validar que ya hayan asignado un numero de registro
		if($("#igRegistro").text() == '')
		{
			$("#igRegistro").attr("class", "").addClass('campoObligatorio');
			permitirGuardar = false;
		}

		// --> Validacion de campos obligatorios
		$("#tablaInfoGeneral").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '' && ($(this).attr("disabled") == undefined || ($(this).attr("campofecha") == "si" && $(this).next().is(":visible"))))
			{
				$(this).attr("class", "").addClass('campoObligatorio');
				permitirGuardar = false;
			}
		});

		// --> Validar que el % de distribucion sea del 100%
		if($("#totalPorcentajeConf").attr("value")*1 < 100 && $("#igCco").attr("disabled") == undefined)
		{
			mostrar_mensaje("El % de distribucion de los centros<br>de costos debe ser del 100%", "div_mensajes");
			if(registroActivo == 'nuevo')
				boton.html('Guardar').removeAttr("disabled");
			else
				boton.html('Actualizar').removeAttr("disabled");
			return;
		}

		// --> Armar lista de centros de costos
		var  listaCentrosCostos	= new Object();
		$("#listaConfiguracionCco tr[trCco=si]").each(function(index){
			listaCentrosCostos[index] 			= new Object();
			listaCentrosCostos[index].cco 		= $(this).find("#tdConfCco").attr("cco");
			listaCentrosCostos[index].porcent	= $(this).find("#tdConfPor").text();
			listaCentrosCostos[index].estados	= $(this).find("#tdConfEst").attr("listaEstados");
		});
		listaCentrosCostos = JSON.stringify(listaCentrosCostos);

		if(listaCentrosCostos == '{}' && $("#igCco").attr("disabled") == undefined)
		{
			$("#igCco").attr("class", "").addClass('campoObligatorio');
			permitirGuardar = false;
		}

		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'guardarInfoGeneral',
				registroActivo:			registroActivo,
				igCompañia:				$("#igCompañia").val(),
				igPlaca:				$("#igPlaca").val(),
				igNombre:				$("#igNombre").val(),
				igProveedores:			$("#igProveedores").val(),
				igFactura:				$("#igFactura").val(),
				igMarca:				$("#igMarca").val(),
				igSerial:				$("#igSerial").val(),
				igModelo:				$("#igModelo").val(),
				igDescripcion:			$("#igDescripcion").val(),
				igNuevo:				(($("#igNuevo").attr('checked') == 'checked') ? 'on' : 'off'),
				igFechaAdqui:			$("#igFechaAdqui").val(),
				igFechaMarcha:			$("#igFechaMarcha").val(),
				igEstado:				$("#igEstado").val(),
				listaCentrosCostos:		listaCentrosCostos,
				igUbicacion:			$("#igUbicacion").val(),
				igDespreciableSi:		(($("#igDespreciableSi").attr('checked') == 'checked') ? 'on' : 'off'),
				igAseguradora:			$("#igAseguradora").val(),
				igNumPoliza:			$("#igNumPoliza").val(),
				igValorAseg:			$("#igValorAseg").val(),
				regModalidad:			$("#generarRegModalidad").val(),
				regGrupo:				$("#generarRegGrupo").val(),
				regSubGrupo:			$("#generarRegSubGrupo").val(),
				siRealizarIngreso:		(($("#siRealizarIngreso").attr('checked') == 'checked') ? 'on' : 'off'),
				igPeriodo:				$("#hiddenPeriodoSeleccionado").val()

			}, function(respuesta){

				verActivo(respuesta.registroActivo, 'InfoGeneral', '*', '');

				listarActivo();

				setTimeout(function() {
					mostrar_mensaje(respuesta.mensaje, "div_mensajes");
				}, 200);


			},'json');
		}
		else
			mostrar_mensaje("Faltan campos obligatorios", "div_mensajes");

		if(registroActivo == 'nuevo')
			boton.html('Guardar').removeAttr("disabled");
		else
			boton.html('Actualizar').removeAttr("disabled");
	}
	//-----------------------------------------------------------------
	//	Funcion guarda el BD el formulario de informacion fiscal
	//-----------------------------------------------------------------
	function guardarInforFiscal(Boton, registroActivo, idRegistroInfoFis)
	{
		// --> Deshabilitar el boton grabar hasta que termine el proceso
		boton = jQuery(Boton);
		boton.html('&nbsp;<span style="font-size:10px;font-weight:normal">Guardando...</span>&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" >').attr("disabled","disabled");

		$('#tablaInfoFiscal .campoObligatorio').each(function(){
			$(this).removeClass('campoObligatorio');
			if($(this).attr("disabled") == undefined)
				$(this).addClass("campoHabilitado");
		});

		$('#tablaInfoFiscal .entero').each(function(){
			if($(this).val() != "")
				$(this).val($(this).val().replace(/\,/g,''));
		});

		var permitirGuardar = true;

		// --> Validar que ya hayan asignado un numero de registro
		if($("#igRegistro").text() == '')
		{
			$("#igRegistro").addClass('campoObligatorio');
			mostrar_mensaje("Para poder guardar la información niif-nic<br> debe existir el n° de registro del activo.", "div_mensajes2");
			boton.html('Guardar').removeAttr("disabled");
			return;
		}

		// --> Validacion de campos obligatorios
		$("#tablaInfoFiscal").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '' && ($(this).attr("disabled") == undefined || ($(this).attr("campofecha") == "si" && $(this).next().is(":visible"))))
			{
				$(this).attr("class", "").addClass('campoObligatorio');
				permitirGuardar = false;
			}
		});

		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'guardarInfoFiscal',
				registroActivo:			registroActivo,
				idRegistroInfoFis:		idRegistroInfoFis,
				ifCostoAdqui:			$("#ifCostoAdqui").val(),
				ifIva:					$("#ifIva").val(),
				ifAxi:					$("#ifAxi").val(),
				ifMejoras:				$("#ifMejoras").val(),
				ifDepreAcum:			$("#ifDepreAcum").val(),
				ifAxiDepreAcum:			$("#ifAxiDepreAcum").val(),
				ifImpuValorizacion:		$("#ifImpuValorizacion").val(),
				ifValorSalvamento:		$("#ifValorSalvamento").val(),
				ifSiReqAjusFiscales:	(($("#ifSiReqAjusFiscales").attr('checked') == 'checked') ? 'on' : 'off'),
				ifPorcenAjusteFiscal:	$("#ifPorcenAjusteFiscal").val(),
				ifAvaluoCatastral:		$("#ifAvaluoCatastral").val(),
				ifMetodoDepre:			$("#ifMetodoDepre").val(),
				ifReaFisSaldoIni:		$("#ifReaFisSaldoIni").val(),
				ifReaFisMoviAnual:		$("#ifReaFisMoviAnual").val(),
				ifReaFisSaldoFin:		$("#ifReaFisSaldoFin").val(),
				ifVidaUtil:				$("#ifVidaUtil").val(),
				ifPeriodosDepre:		$("#ifPeriodosDepre").val(),
				ifDepreFisSaldoIni:		$("#ifDepreFisSaldoIni").val(),
				ifValorLibrosSaldoIni:	$("#ifValorLibrosSaldoIni").val(),
				siRealizarIngreso:		(($("#siRealizarIngreso").attr('checked') == 'checked') ? 'on' : 'off'),
				ifPeriodo:				$("#hiddenPeriodoSeleccionado").val()

			}, function(respuesta){

				verActivo(respuesta.registroActivo, 'InfoFiscal', '*', '');
				listarActivo();

				setTimeout(function() {
					mostrar_mensaje(respuesta.mensaje, "div_mensajes2");
				}, 200);

			},'json');
		}
		else
			mostrar_mensaje("Faltan campos obligatorios", "div_mensajes2");

		if(registroActivo == 'nuevo')
			boton.html('Guardar').removeAttr("disabled");
		else
			boton.html('Actualizar').removeAttr("disabled");
	}
	//-----------------------------------------------------------------
	//	Funcion guarda el BD el formulario de informacion NIFF-NIC
	//-----------------------------------------------------------------
	function guardarInforNiff(Boton, registroActivo, inComponente, idRegistroInfoNiif)
	{
		// --> Deshabilitar el boton grabar hasta que termine el proceso
		boton = jQuery(Boton);
		boton.html('&nbsp;<span style="font-size:10px;font-weight:normal">Guardando...</span>&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" >').attr("disabled","disabled");

		$('#tablaInfoNiif .campoObligatorio').each(function(){
			$(this).removeClass('campoObligatorio');
			if($(this).attr("disabled") == undefined)
				$(this).addClass("campoHabilitado");
		});

		$('#tablaInfoNiif .entero').each(function(){
			if($(this).val() != "")
				$(this).val($(this).val().replace(/\,/g,''));
		});

		var permitirGuardar = true;

		// --> Validar que ya hayan asignado un numero de registro
		if($("#igRegistro").text() == '')
		{
			$("#igRegistro").addClass('campoObligatorio');
			mostrar_mensaje("Para poder guardar la información niif-nic<br> debe existir el n° de registro del activo.", "div_mensajes3");
			boton.html('Guardar').removeAttr("disabled");
			return;
		}

		// --> Validacion de campos obligatorios
		$("#tablaInfoNiif").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '' && $(this).attr("id") != 'inOrigenDatoEstadistico' && $(this).attr("id") != 'inValorDatoManual' && ($(this).attr("disabled") == undefined || ($(this).attr("campofecha") == "si" && $(this).next().is(":visible"))))
			{
				$(this).attr("class", "").addClass('campoObligatorio');
				permitirGuardar = false;
			}
		});

		// --> Validar que hayan selecionado el campo "origen del dato", si aplica
		if($("#inMetodoDepNiif").find("option:selected").attr("activarOrigenDato") == 'on')
		{
			if($("#tipoOrigenDatoEstadistico").is(":checked"))
			{
				if($("#inOrigenDatoEstadistico").attr("valor") == "")
				{
					$("#inOrigenDatoEstadistico").attr("class", "").addClass('campoObligatorio');
					permitirGuardar = false;
				}
				else
				{
					inOrigenDatoEstadistico 	= $("#inOrigenDatoEstadistico").attr("valor");
					tipoOrigenDatoEstadistico	= "on";
				}
			}
			else
			{
				if($("#inValorDatoManual").val() == "")
				{
					$("#inValorDatoManual").attr("class", "").addClass('campoObligatorio');
					permitirGuardar = false;
				}
				else
				{
					inOrigenDatoEstadistico 	= $("#inValorDatoManual").val();
					tipoOrigenDatoEstadistico	= "off";
				}
			}
		}
		else
		{
			tipoOrigenDatoEstadistico	= "";
			inOrigenDatoEstadistico 	= "";
		}

		if(inComponente == '*')
		{
			inUniGeneraEfec 	= $("#inUniGeneraEfec").val();
			inNombreComponente	= '';
		}
		else
		{
			inUniGeneraEfec 	= '';
			inNombreComponente	= $("#inNombreComponente").val();
		}


		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   					'',
				accion:         					'guardarInfoNiif',
				registroActivo:						registroActivo,
				idRegistroInfoNiif:					idRegistroInfoNiif,
				inUniGeneraEfec:					inUniGeneraEfec,
				detPorComponentesSi:				(($("#detPorComponentesSi").attr('checked') == 'checked') ? 'on' : 'off'),
				inComponente:						inComponente,
				inNombreComponente:					inNombreComponente,
				inCostoAtrib:						$("#inCostoAtrib").val(),
				inValorSalvamento:					$("#inValorSalvamento").val(),
				inMetodoDepNiif:					$("#inMetodoDepNiif").val(),
				inOrigenDatoEstadis:				inOrigenDatoEstadistico,
				tipoOrigenDatoEstadistico:			tipoOrigenDatoEstadistico,
				inAdicionesMovMen:					$("#inAdicionesMovMen").val(),
				inTipoCostosfuturos:				$("#inTipoCostosfuturos").val(),
				inTiempoEstimadoCosFut:				$("#inTiempoEstimadoCosFut").val(),
				fechaRealCosFut:					$("#fechaRealCosFut").val(),
				tipoAjusteCosFut:					$("#tipoAjusteCosFut").val(),
				tasaAjustePerCosFut:				$("#tasaAjustePerCosFut").val(),
				inCostosFuturosVal:					$("#inCostosFuturosVal").val(),
				inProvisionCostosFuturosVal:		$("#inProvisionCostosFuturosVal").val(),
				inMetodoValuacion:					$("#inMetodoValuacion").val(),
				inValorRazonable:					$("#inValorRazonable").val(),
				inFechaValuacion:					$("#inFechaValuacion").val(),
				vidaUtilPerioDepre:					$("#vidaUtilPerioDepre").val(),
				siRealizarIngreso:					(($("#siRealizarIngreso").attr('checked') == 'checked') ? 'on' : 'off'),
				inPeriodo:							$("#hiddenPeriodoSeleccionado").val()

			}, function(respuesta){

				verActivo(respuesta.registroActivo, 'InfoNiif', respuesta.componente, '');
				listarActivo();

				setTimeout(function() {
					mostrar_mensaje(respuesta.mensaje, "div_mensajes3");
				}, 200);

			},'json');
		}
		else
			mostrar_mensaje("Faltan campos obligatorios", "div_mensajes3");

		if(registroActivo == 'nuevo')
			boton.html('Guardar').removeAttr("disabled");
		else
			boton.html('Actualizar').removeAttr("disabled");
	}
	//-----------------------------------------------------------------
	//	Funcion que pinta un mensaje de alerta
	//-----------------------------------------------------------------
	function mostrar_mensaje(mensaje, idDiv)
	{
		$("#"+idDiv).html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#"+idDiv).css({"width":"300","opacity":" 0.9","fontSize":"11px"});
		$("#"+idDiv).show(500);
		setTimeout(function() {
			$("#"+idDiv).hide(500);
		}, 12000);
	}
	//-----------------------------------------------------------------
	//	Pintar ficha del activo
	//-----------------------------------------------------------------
	function verActivo(registro, formulario, componenteVer='*', periodo='')
	{
		if(formulario == '')
			formulario = $("#detalleActivo").find("li[class*=ui-state-active]").attr("formulario");

		if(registro == 'nuevo')
			$("#hrefTabInfoGeneral").click();

		if(registro == '')
			registro = trim((($("#igRegistro").text() == '') ? 'nuevo' : $("#igRegistro").text()));

		if(periodo=='')
			periodo = $("#hiddenPeriodoSeleccionado").val();

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'verFormulario',
			registroActivo:			registro,
			formulario:				formulario,
			componenteVer:			componenteVer,
			periodo:				periodo
		}, function(html){

			$("*").dialog("destroy");
			$("#divEditarCentrosDeCostos").remove();
			$("#divGenerarRegistro").remove();
			$('#ingresoFlotante').remove();
			$("#activoRegistrado").remove();

			$("#tab"+formulario).html("");
			$("#tab"+formulario).html(html);
			$("#detalleActivo").show(100);

			// --> Cargar placeholder a los input
			$("#tabla"+formulario).find("[tipo]").attr("placeholder", " ");

			// --> Marcar borde de los input habilitados para que el usuario ingrese #FF9900
			$("#tabla"+formulario).find("input[disabled != disabled]").addClass("campoHabilitado");
			$("#tabla"+formulario).find("select[disabled != disabled]").addClass("campoHabilitado");
			$("#tabla"+formulario).find("textarea[disabled != disabled]").addClass("campoHabilitado");

			// --> Activar seleccionador de fecha
			$("#tab"+formulario+" [campoFecha=si]").each(function(){

				activarSeleccionadorFecha($(this).attr("id"));

				if($(this).attr("disabled"))
					$(this).next().hide();

				$(this).attr("disabled", "disabled")

			});

			// --> Validar campos enteros
			activar_regex($("#tabla"+formulario));

			$("#nombreActivo").html("<span style='font-family: verdana;font-size: 10pt;font-weight:normal'><b>N° Registro:</b>&nbsp;"+((registro == 'nuevo') ? 'Nuevo Activo' : registro)+"</span>");

			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			// --> Mostrar el detalle por componentes para el formulario de informacion niif
			if(formulario == 'InfoNiif' && componenteVer == '*')
			{
				if($("#tablaDetalleComponentes tr").length > 2)
					$("#detPorComponentesSi").attr('checked', 'checked');

				if($("#detPorComponentesSi").attr('checked') == 'checked')
					$("#divDetalleComponentes").show();
				else
					$("#divDetalleComponentes").hide();

				if($("#idInfoNiff").val() == "")
					$("#botonAgregarComponente").attr("disabled", "disabled");
				else
					$("#botonAgregarComponente").removeAttr("disabled");
			}

			// --> Asignar variables hidden globales
			$("#hiddenPeriodoSeleccionado").val(periodo);
			$("#hiddenRegistroActivo").val(registro);
			$("#hiddenComponente").val(componenteVer);

			if(registro == 'nuevo' && formulario != 'InfoGeneral')
			{
				if(formulario == "InfoNiif")
				{
					$("#buttonGuardarNif").attr("disabled", "disabled");
					nomDivMens = "div_mensajes3";
				}
				else
				{
					$("#buttonGuardarFis").attr("disabled", "disabled");
					nomDivMens = "div_mensajes2";
				}
				mostrar_mensaje("Primero debe ingresar la información <br>general y de control del activo.", nomDivMens)
			}

			// --> Cargar autocomplete de origen de datos del sistema de estadísticas
			if(formulario == "InfoNiif")
				crear_autocomplete("hiddenDatosEstadisticos", "inOrigenDatoEstadistico");

			if($("#activoRegistrado").val() != 'on')
			{
				setTimeout(function(){
					if($("#permisoParaRealizarIngreso").val() == 'on')
						flotanteIngresoDelActivo();
				}, 2000);
			}
		});
	}
	//-----------------------------------------------------------------------------------
	//	--> Anula el ingreso de un activo, para activar nuevamente la opcion de ingresar
	//-----------------------------------------------------------------------------------
	function anularIngreso(registroAct, idMovIngresoAnt)
	{
		if(confirm("¿Esta seguro que desea anular el ingreso del activo, "+registroAct+"?"))
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'anularIngreso',
				registroAct:			registroAct,
				idMovIngresoAnt:		idMovIngresoAnt
			}, function(html){
				$("#activoRegistrado").val("off");
				$(".botonAnularIngreso").hide();
				listarActivo();

				setTimeout(function(){
					flotanteIngresoDelActivo();
				}, 1000);

			});
		}
	}
	//-----------------------------------------------------------
	//	--> Cargar un html flotante
	//-----------------------------------------------------------
	function flotanteIngresoDelActivo()
	{
		posicion = $('#posicionFlotante').offset();
		$('#ingresoFlotante').css({'left':posicion.left-120,'top':posicion.top-112});
		$('#ingresoFlotante').show(300);
		$('#ingresoFlotante').draggable();
	}

	//-----------------------------------------------------------
	//	--> Cargar autocomplete de datos estadisticos
	//-----------------------------------------------------------
	function crear_autocomplete(HiddenArray, CampoCargar)
	{
		ArrayValores  = JSON.parse($("#"+HiddenArray).val());

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = CodVal+"-"+ArrayValores[CodVal];
			ArraySource[index].nombre = ArrayValores[CodVal];
		}

		// --> Si el autocomplete ya existe, lo destruyo
		if( $("#"+CampoCargar).attr("autocomplete") != undefined )
			$("#"+CampoCargar).removeAttr("autocomplete");

		// --> Creo el autocomplete
		$( "#"+CampoCargar ).autocomplete({
			minLength: 	0,
			source: 	ArraySource,
			select: 	function( event, ui ){
				$( "#"+CampoCargar ).val(ui.item.label);
				$( "#"+CampoCargar ).attr('valor', ui.item.value);
				$( "#"+CampoCargar ).attr('nombre', ui.item.nombre);
				return false;
			}
		});
		limpiaAutocomplete(CampoCargar);
	}
	//----------------------------------------------------------------------------------
	//	--> Controlar que el input no quede con basura, sino solo con un valor seleccionado
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

	//---------------------------------------------------------------------
	//	Abrir ventana modal para generar un nuevo registro para un activo
	//---------------------------------------------------------------------
	function generarRegistro()
	{
		// --> Abrir ventana de dialog
		$( '#divGenerarRegistro').dialog({
			show:{
				effect: "blind",
				duration: 100
			},
			hide:{
				effect: "blind",
				duration: 100
			},
			width:  580,
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Generar Registro, Nuevo Activo",
			close: function( event, ui ) {

			}
		});
	}
	//------------------------------------------------------
	//	Asignar registro al activo
	//------------------------------------------------------
	function asignarRegistroActivo()
	{
		$('#divGenerarRegistro .campoObligatorio').removeClass('campoObligatorio');

		permitirGuardar = true;

		$("#divGenerarRegistro").find("select").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
			}
		});

		if(permitirGuardar)
		{
			$("#igRegistro").html($("#generarRegModalidad").val()+$("#generarRegGrupo").val()+$("#generarRegSubGrupo").val()+"???");
			$('#divGenerarRegistro').dialog('close');
		}

	}
	//--------------------------------------------------------------------------
	//	Pinta el seleccionador de subgrupos de activos dependiendo del grupo
	//--------------------------------------------------------------------------
	function cargarSelectSubgrupos()
	{
		if($("#generarRegGrupo").val() == '')
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'cargarSelectSubgrupos',
			grupo:					$("#generarRegGrupo").val()
		}, function(html){
			$("#generarRegSubGrupo").html("");
			$("#generarRegSubGrupo").append(html);
		});
	}
	//--------------------------------------------------------------------------
	//	Pinta la lista inicial de todos los activos
	//--------------------------------------------------------------------------
	function listarActivo()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'pintarListadeActivos'
		}, function(html){
			$("#listaActivos").html(html);
		});
	}
	//--------------------------------------------------------------------------
	//	--> Funcion que agrega un nuevo componente en la informacion niif-nic
	//--------------------------------------------------------------------------
	function verComponente(registro, componente, periodo)
	{
		// --> 	Si existe un componente nuevo, es porque aun no se ha guardado
		//		validar si el usuario lo desea cerrar o no
		if($("#comp_nuevo").length > 0)
		{
			if(!confirm("El componente nuevo aún no se ha guardado\n ¿Desea cerrarlo sin guardar los cambios?"))
				return;
			else
				$("#comp_nuevo").parent().remove();
		}

		// --> Agregar nuevo componete a la lista
		if(componente == 'nuevo')
			$("#tablaDetalleComponentes").append("<tr><td width='3%'></td><td width='4%' style='border-left:1px solid #62BBE8;color:#62BBE8;'>----------</td><td id='comp_"+componente+"' class='tdComponenteSeleccionado'>&nbsp;&nbsp;Nuevo Componente</td></tr>");

		// --> Resaltar el componente que se le dio click
		$("#tablaDetalleComponentes").find("[class*=tdComponenteSeleccionado]").removeClass("tdComponenteSeleccionado").addClass("tdComponente");
		$("#comp_"+((componente == '*') ? 'PRINCIPAL' : componente)).addClass("tdComponenteSeleccionado");

		// --> Obtener el formulario del componente
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'verDetallePorComponente',
			registroActivo:			registro,
			componente:				componente,
			periodo:				periodo
		}, function(html){
			$("#divFormularioInfoNiif").html(html);

			// --> Cargar placeholder a los input
			$("#tablaInfoNiif").find("[tipo]").attr("placeholder", " ");

			// --> Activar seleccionador de fecha
			$("#tablaInfoNiif [campoFecha=si]").each(function(){

				activarSeleccionadorFecha($(this).attr("id"));

				if($(this).attr("disabled"))
					$(this).next().hide();

				$(this).attr("disabled", "disabled")

			});

			// --> Validar campos enteros
			activar_regex($("#tablaInfoNiif"));

			// --> Tooltip
			$("#tablaInfoNiif").find('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			// --> Asignar variables hidden globales
			$("#hiddenPeriodoSeleccionado").val(periodo);
			$("#hiddenRegistroActivo").val(registro);
			$("#hiddenComponente").val(componente);

			// --> Marcar borde de los input habilitados para que el usuario ingrese #FF9900
			$("#tablaInfoNiif").find("input[disabled != disabled]").addClass("campoHabilitado");
			$("#tablaInfoNiif").find("select[disabled != disabled]").addClass("campoHabilitado");
			$("#tablaInfoNiif").find("textarea[disabled != disabled]").addClass("campoHabilitado");

			// --> Cargar autocomplete de origen de datos del sistema de estadísticas
			crear_autocomplete("hiddenDatosEstadisticos", "inOrigenDatoEstadistico");

			if(componente == '*')
				$("#detPorComponentesSi").attr('checked', 'checked');
		});
	}
	//--------------------------------------------------------------------------
	//	--> Mostrar el detalle por componentes
	//--------------------------------------------------------------------------
	function verDetallePorComponentes(registroActivo)
	{
		if($("#detPorComponentesSi").attr('checked') == 'checked')
		{
			$("#divDetalleComponentes").show(500);

			if(registroActivo != 'nuevo')
				$("#botonAgregarComponente").removeAttr("disabled");
			else
				$("#botonAgregarComponente").attr("disabled", "disabled");

			if($("#idInfoNiff").val() == "")
				$("#botonAgregarComponente").attr("disabled", "disabled");
			else
				$("#botonAgregarComponente").removeAttr("disabled");
		}
		else
		{
			$("#divDetalleComponentes").hide(500);
		}
	}
	//--------------------------------------------------------------------------
	//	--> Asignar nombre a un componente nuevo
	//--------------------------------------------------------------------------
	function asignarNombreCompo(componente)
	{
		$("#comp_"+componente).html("&nbsp;&nbsp;"+$("#inNombreComponente").val());
		$("#fieldsetInfoNiif").html($("#inNombreComponente").val());
	}
	//--------------------------------------------------------------------------
	//	--> Elimina un centro de costos de la distribucion
	//--------------------------------------------------------------------------
	function eliminarCco(elmento)
	{
		var elmento = $(elmento);
		elmento.parent().parent().remove();
		porcenAnt = $("#tdConfPor", elmento.parent().parent()).text();
		porcenAct = $("#totalPorcentajeConf").attr("value")-porcenAnt;
		$("#totalPorcentajeConf").attr("value", porcenAct);
		$("#totalPorcentajeConf").html("<b>Total: "+porcenAct+" %</b>");
		$("#igPorcentajeDis").attr("placeholder", 100-($("#totalPorcentajeConf").attr("value")*1));

		$("#listaCentrosCostos").find("[id=trPosCco"+elmento.attr("trPosCco")+"]").remove();

		if( $("#totalPorcentajeConf").attr("value")*1 < 100)
			$("#botonAgregarCco").removeAttr("disabled");
	}
	//--------------------------------------------------------------------------
	//	--> Abrir modal para editar centro de costos
	//--------------------------------------------------------------------------
	function editarCentroCostos()
	{
		// --> Abrir ventana de dialog
		$( '#divEditarCentrosDeCostos').dialog({
			show:{
				effect: "blind",
				duration: 100
			},
			hide:{
				effect: "blind",
				duration: 100
			},
			width:  1000,
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Configuración centros de costos",
			close: function( event, ui ) {
				if($("#totalPorcentajeConf").attr("value")*1 < 100)
					alert("No se ha completado el 100% de distribución\nentre los centros de costos actuales.");
			}
		});
		$("#igPorcentajeDis").attr("placeholder", 100-($("#totalPorcentajeConf").attr("value")*1));
		activar_regex($("#divEditarCentrosDeCostos"));
	}
	//----------------------------------------------------
	//	--> Agregar un cco perteneciente al activo
	//----------------------------------------------------
	function agregarCco()
	{
		var permitirGuardar = true;
		$('#divEditarCentrosDeCostos .campoObligatorio').removeClass('campoObligatorio');

		// --> Validar que el centro de costos no este configurado
		$("#listaConfiguracionCco tr[trCco=si]").each(function(index){
			if($("#centroCostosActivo").val() == $(this).find("#tdConfCco").attr("cco"))
			{
				mostrar_mensaje("Este centro de costos ya está configurado", "div_mensajes4");
				permitirGuardar = false;
			}
		});
		if(!permitirGuardar)
			return;

		// --> Validar que esten seleccionados todos los campos
		$("#divEditarCentrosDeCostos").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
			}
		});

		var listaDeEstados 		= '';
		var listaDeEstadosNom	= '';
		var arrlistaDeEstados 	= new Array();
		$("#listaDeEstados").find("input[type=checkbox]:checked").each(function(index, value){
			listaDeEstados 				= listaDeEstados+((listaDeEstados == '') ? $(this).val() : ','+$(this).val());
			listaDeEstadosNom 			= listaDeEstadosNom+((listaDeEstadosNom == '') ? '' : ', ')+$(this).parent().text();
			arrlistaDeEstados[index] 	= $(this).parent().text();
		});

		if(listaDeEstados == '')
		{
			$("#listaDeEstados").addClass('campoObligatorio');
			permitirGuardar = false;
		}

		if(($("#igPorcentajeDis").val()*1) > (100-($("#totalPorcentajeConf").attr("value")*1)))
		{
			mostrar_mensaje("No se puede superar el 100%", "div_mensajes4");
			return;
		}
		if(($("#igPorcentajeDis").val()*1) == 0)
		{
			mostrar_mensaje("El % debe ser mayor a cero", "div_mensajes4");
			return;
		}

		if(permitirGuardar)
		{
			var totalPorcentaje = 0;
			$("#listaConfiguracionCco [porcentaje='si']").each(function(){
				totalPorcentaje = totalPorcentaje+($(this).text()*1);
			});
			totalPorcentaje = totalPorcentaje+($("#igPorcentajeDis").val()*1);

			ultimoConsec = $("#listaConfiguracionCco [trCco='si']:last").find("img[trPosCco]").attr("trPosCco");

			ultimoConsec = ((ultimoConsec == undefined) ? 0 : (ultimoConsec*1)+1);

			html = "<tr trCco='si' class='fila2'>"
						+"<td id='tdConfCco' cco='"+$("#centroCostosActivo").val()+"'>"+$("#centroCostosActivo option:selected").text()+"</td>"
						+"<td id='tdConfPor' porcentaje='si'>"+$("#igPorcentajeDis").val()+"</td>"
						+"<td id='tdConfEst' listaEstados='"+listaDeEstados+"'>"+listaDeEstadosNom+"</td>"
						+"<td><img trPosCco='"+ultimoConsec+"' style='cursor:pointer;width:10px;height:10px;' onclick='eliminarCco(this);' title='Eliminar' src='../../images/medical/eliminar1.png'></td>"
					+"</tr>"
					+"<tr><td></td><td class='fondoAmarillo' id='totalPorcentajeConf' value='"+totalPorcentaje+"'><b>Total: "+totalPorcentaje+" %</b></td></td></tr>";

			html2 = "<tr id='trPosCco"+ultimoConsec+"' cco='"+$("#centroCostosActivo").val()+"'>"
						+"<td rowspan='"+arrlistaDeEstados.length+"' style='font-size:9px;border: 1px solid #AED0EA;' >"+$("#centroCostosActivo option:selected").text()+"</td>"
						+"<td rowspan='"+arrlistaDeEstados.length+"' style='font-size:9px;border: 1px solid #AED0EA;' >"+$("#igPorcentajeDis").val()+"</td>"
						+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrlistaDeEstados[0]+"</td>"
					+"</tr>";
					for(x=1;x<arrlistaDeEstados.length;x++)
						html2 = html2+"<tr id='trPosCco"+ultimoConsec+"'><td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrlistaDeEstados[x]+"</td></tr>";

			$("#totalPorcentajeConf").parent().remove();
			$("#listaConfiguracionCco").append(html);
			$("#listaCentrosCostos").append(html2);
			$("#igPorcentajeDis").attr("placeholder", 100-($("#totalPorcentajeConf").attr("value")*1));
			$("#igPorcentajeDis").val("");
			$("#centroCostosActivo").find("option[value=]").attr("selected", "selected");
			$("#listaDeEstados").find("input[type=checkbox]").removeAttr("checked");

			if(($("#totalPorcentajeConf").attr("value")*1) == 100)
				$("#botonAgregarCco").attr("disabled", "disabled");
			else
				$("#botonAgregarCco").removeAttr("disabled");
		}
		else
			mostrar_mensaje("Faltan campos obligatorios", "div_mensajes4");
	}
	//----------------------------------------------------
	//	--> Calendario flotante para seleccionar periodo
	//----------------------------------------------------
	function calendarioSeleccionarPeriodo(ele)
	{
		var mes_seleccionado = (($('#hiddenPeriodoSeleccionado').val() == '') ? $('#hiddenPeriodoActual').val() : $('#hiddenPeriodoSeleccionado').val() );
		mes_seleccionado = mes_seleccionado.split('-');
		mes_seleccionado = mes_seleccionado[1];

		$('#calendarioFlotante').find('td[ref]').each(function(index){
			$(this).addClass("cuadroMes").removeClass("cuadroMesSeleccionado");

			if(mes_seleccionado == $(this).attr('ref'))
				$(this).addClass("cuadroMesSeleccionado");
		});

		var posicion = $(ele).offset();
		$('#divCalendarioFlotante').css({'left':posicion.left,'top':posicion.top+24}).show(400);
	}
	//--------------------------------------------------------------------------------
	//	--> Seleccionar un periodo para ver el activo, desde el calendario flotante
	//--------------------------------------------------------------------------------
	function seleccionarPeriodoDesdeCalendario(año, mes)
	{
		var fecha_seleccionada = ((año == '') ? $('#año_sel').val()+'-'+mes : año+'-'+mes);
		$('#hiddenPeriodoSeleccionado').val(fecha_seleccionada);
		$('#divCalendarioFlotante').hide(400);

		var formulario = $("#detalleActivo").find("li[class*=ui-state-active]").attr("formulario");
		verActivo($("#hiddenRegistroActivo").val(), formulario, $("#hiddenComponente").val(), fecha_seleccionada);
	}
	//--------------------------------------------------------------------------------
	//	--> Pinta o oculta el campo de origen de dato estadistico
	//--------------------------------------------------------------------------------
	function habilitarCampoOrigenDato(elemento)
	{
		var elemento 			= $(elemento);
		activarOrigenDato 		= elemento.find("option:selected").attr("activarOrigenDato");
		if(activarOrigenDato == 'on')
			$(".origenDato").show(300);
		else
			$(".origenDato").hide(300);
	}
	//--------------------------------------------------------------------------------
	//	--> Pinta o oculta el campo de origen de dato estadistico
	//--------------------------------------------------------------------------------
	function cambioTipoOrigenDato()
	{
		$("#inOrigenDatoEstadistico").val("").attr("valor", "");
		$("#inOrigenDatoEstadistico").attr("nombre", "");
		$("#inValorDatoManual").val("");

		if($("#tipoOrigenDatoEstadistico").is(":checked"))
		{
			$("#inOrigenDatoEstadistico").show();
			$("#inValorDatoManual").hide();
		}
		else
		{
			$("#inOrigenDatoEstadistico").hide();
			$("#inValorDatoManual").show();
		}
	}
	//-------------------------------------------------------------------------------------
	//	--> Mostrar lista de centros de costos relacionados al activo, en la lista incial
	//		Frederick aguirre
	//-------------------------------------------------------------------------------------
	function listarCcosEnListaActivos( ele, arr_ccos )
	{
		ele = jQuery(ele);
		if( ele.parent().find("table").length > 0 ){
			//Ya existe una tabla con la lista de centros de costos, hay que eliminarla y cambiar el titulo del boton
			ele.parent().find("table").remove();
			//ele.attr("value","Mostrar todos");
			ele.find("img").attr("src", "../../images/medical/hce/mas2.png");
		}else{
			//Mostrar lista de centros de costos en los datos generales
			var html_lista_ccos = "<table style='font-size: 7pt;text-align: left;margin:3px' width='100%'>";
			var i=0;
			for( i in arr_ccos ){
				html_lista_ccos+= 	"<tr style=''>"
										+"<td style='font-size:9px;border: 1px solid #AED0EA;' width='96%' align='left'>"
											+"&nbsp;"+arr_ccos[i].codigo+"&nbsp;-&nbsp;"+arr_ccos[i].nombre+"</td>"
									+"</tr>"
			}
			html_lista_ccos+="</table>";
			ele.next().after(html_lista_ccos);
			//ele.attr("value","Ocultar todos");
			ele.find("img").attr("src", "../../images/medical/hce/menos2.png");
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
		.fila1
		{
			background-color: #C3D9FF;
			color: #000000;
			font-size: 9pt;
			padding:2px;
		}
		.fila2
		{
			background-color: #E8EEF7;
			color: #000000;
			font-size: 9pt;
			padding:3px;
		}
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}
		.campoHabilitado{
			border-color:	#FF9900;
			border-style:	solid;
			border-width:	0.1em;
		}
		.bordeCurvo{
			-moz-border-radius: 0.4em;
			-webkit-border-radius: 0.4em;
			border-radius: 0.4em;
		}
		.tdComponente{
			border:1px solid #62BBE8;
			background-color:#E6F1F9;
			color:#2779B7;
			font-size:10pt;
			cursor:pointer;
		}
		.tdComponenteSeleccionado{
			border:1px solid #2694E8;
			background-color:#62BBE8;
			color:#FFFFFF;
			font-size:10pt;
			cursor:pointer;
			font-weight:bold;
		}
		.cuadroMes2{
			background: #2A5DB0; border: 2px solid #D3D3D3;color: #FFFFFF;font-weight: normal;outline: medium none;margin: 1px; padding: 2px;text-align: center;
		}
		.cuadroMes{
			cursor:pointer;background: #E3F1FA; border: 1px solid #62BBE8;color: #000000;font-weight: normal;outline: medium none;margin: 1px; padding: 2px;text-align: center;
		}
		.cuadroMesSeleccionado{
			cursor:pointer;background: #62BBE8; border: 1px solid #2694E8;color: #FFFFFF;font-weight: bold;outline: medium none;margin: 1px; padding: 2px;text-align: center;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:4000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}

		.bordeado{
			border-radius: 4px;
			border:1px solid #AFAFAF;
		}

		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}

		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=valorCalculado]::-webkit-input-placeholder {color:gray; background:#FCEAED;font-size:8pt}
		/*Firefox*/
		[tipo=otro]::-moz-placeholder {color:#000000; background:#E5F6FF;font-size:8pt}
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		[tipo=valorCalculado]::-moz-placeholder {color:#000000; background:#FCEAED;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=valorCalculado]:-ms-input-placeholder {color:gray; background:#FCEAED;font-size:8pt}
		[tipo=valorCalculado]:-moz-placeholder {color:gray; background:#FCEAED;font-size:8pt}
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	$arrPerAct = traerPeriodoActual();
	// --> Variables hidden con informacion para ver el activo
	echo '
	<input type="hidden" id="hiddenPeriodoActual" 			value="'.implode("-", $arrPerAct).'">
	<input type="hidden" id="hiddenPeriodoSeleccionado" 	value="'.implode("-", $arrPerAct).'">
	<input type="hidden" id="hiddenRegistroActivo" 			value="'.$registroActivo.'">
	<input type="hidden" id="hiddenComponente" 				value="'.$componente.'">
	';
	// --> Crear hidden con un array para cargar autocomplete de estadisticas, del sistema de indicadores.
	$caracter_ok 	= array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma 	= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
	$wbasedatoSgc 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'sgc');
	$arrayEstadis	= array();
	$sqlEstadis = "SELECT Indcod, Indnom
					 FROM ".$wbasedatoSgc."_000001
					WHERE Indest = 'on'
					  AND Indtes = 'on'
	";
	$resEstadis = mysql_query($sqlEstadis, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstadis):</b><br>".mysql_error());
		while($rowEstadis = mysql_fetch_array($resEstadis))
			$arrayEstadis[$rowEstadis['Indcod']] = ucfirst(strtolower(str_replace($caracter_ma, $caracter_ok, utf8_encode($rowEstadis['Indnom']))));

	echo "<input type='hidden' id='hiddenDatosEstadisticos' value='".json_encode($arrayEstadis)."'>";

	// -->	Calendario para seleccionar periodo
	echo '
	<div id="divCalendarioFlotante" style="display:none;z-index:10000;position: absolute;">
        <div id="calendarioFlotante" style="border:solid 1px #4C8EAF;border-radius: 4px;padding:2px;background-color: ;">
            <table>
				<tr>
					<td colspan="3" align="center" style="font-size:11pt;text-align:center;"><b>Año</b>:
						<select id="año_sel" name="año_sel" style="width:67px;border: 1px solid #4C8EAF;background-color:lightyellow;font-size:9pt;">';
						$año_inicio = '2006';
						$año_actual = date('Y');
						for($x=$año_inicio; $x <= $año_actual+1; $x++)
							echo "<option ".(($año_actual==$x)? 'SELECTED':'').">".$x."</option>";
	echo '				</select>
						<img style="cursor:pointer;width:12px;height:12px;" onClick="$(\'#divCalendarioFlotante\').hide(500);" src="../../images/medical/eliminar1.png" title="Cerrar calendario" >
					</td>
				</tr>
                <tr>
                    <td id="ene" class="cuadroMes" ref="01" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'01\');">Ene</td>
                    <td id="feb" class="cuadroMes" ref="02" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'02\');">Feb</td>
                    <td id="mar" class="cuadroMes" ref="03" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'03\');">Mar</td>
				</tr>
				<tr>
                    <td id="abr" class="cuadroMes" ref="04" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'04\');">Abr</td>
                    <td id="may" class="cuadroMes" ref="05" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'05\');">May</td>
                    <td id="jun" class="cuadroMes" ref="06" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'06\');">Jun</td>
                </tr>
				<tr>
                    <td id="jul" class="cuadroMes" ref="07" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'07\');">Jul</td>
                    <td id="ago" class="cuadroMes" ref="08" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'08\');">Ago</td>
                    <td id="sep" class="cuadroMes" ref="09" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'09\');">Sep</td>
                </tr>
				<tr>
                    <td id="oct" class="cuadroMes" ref="10" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'10\');">Oct</td>
                    <td id="nov" class="cuadroMes" ref="11" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'11\');">Nov</td>
                    <td id="dic" class="cuadroMes" ref="12" onClick="seleccionarPeriodoDesdeCalendario(\'\', \'12\');">Dic</td>
                </tr>
            </table>
        </div>
	</div>
	';
	//----------------------------
	//    Fin calendario de mes
	//----------------------------

	echo '
	<div id="tabs">
		<div id="listaActivos">';
				listarActivos();
	echo '
		</div>
		<br>
		<span id="posicionFlotante"></span>
		<div id="detalleActivo" style="display:none">
			<ul>
				<li width="20%" formulario="InfoGeneral"><a href="#tabInfoGeneral"	onClick="verActivo(\'\', \'InfoGeneral\', \'*\', \'\')"	id="hrefTabInfoGeneral">INFORMACIÓN GENERAL Y DE CONTROL</a></li>
				<li width="20%" formulario="InfoFiscal"><a href="#tabInfoFiscal"	onClick="verActivo(\'\', \'InfoFiscal\', \'*\', \'\')" 	id="hrefTabInfoFiscal">INFORMACIÓN FISCAL</a></li>
				<li width="20%" formulario="InfoNiif"><a href="#tabInfoNiif"		onClick="verActivo(\'\', \'InfoNiif\', \'*\', \'\')" 	id="hrefTabInfoNiif">INFORMACIÓN NIIF-NIC</a></li>
				<table width="40%" style="padding:6px;">
					<tr>
						<td id="nombreActivo"></td>
						<td align="right"><img style="cursor:pointer;" width="15" height="15" onclick="$(\'#detalleActivo\').hide(200);$(\'#ingresoFlotante\').remove();" title="Cerrar Detalle" src="../../images/medical/sgc/x.png"></td>
					</tr>
				</table>
			</ul>
			<div id="tabInfoGeneral"></div>
			<div id="tabInfoFiscal"></div>
			<div id="tabInfoNiif" align="center"></div>
		</div>
	</div>';
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
