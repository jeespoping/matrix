<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:	2014-11-10
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
	$user_session 	= explode('-',$_SESSION['user']);
	$wuse 			= $user_session[1];
	

	include_once("root/comun.php");
	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	include_once("actfij/funciones_activosFijos.php");

	$conex 		= obtenerConexionBD("matrix");
	$wbasedato 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activosFijos');
	$wfecha		= date("Y-m-d");
    $whora 		= date("H:i:s");
	$caracter_ok 	= array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma 	= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
	$periodoAct 	= traerPeriodoActual();

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//-------------------------------------------------------------------
	//	-->	Pinta la lista de formulas existentes
	//-------------------------------------------------------------------
	function listarFormulas()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $caracter_ok;
		global $caracter_ma;
		global $periodoAct;

		$listaFormulas 	= array();

		$sqlCamposFor = " SELECT Permed, Percod, Percam, Perdes, ".$wbasedato."_000005.id, Fornom, Forcod
							FROM ".$wbasedato."_000005, ".$wbasedato."_000019
						   WHERE Forano = '".$periodoAct['ano']."'
						     AND Formes = '".$periodoAct['mes']."'
							 AND Forest = 'on'
							 AND Forfor != ''
							 AND Permed = '".$wbasedato."'
							 AND Percod = Fortab
							 AND Percam = Forcam
		";
		$resCamposFor = mysql_query($sqlCamposFor, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCamposFor):</b><br>".mysql_error());
		while($rowCamposFor = mysql_fetch_array($resCamposFor))
		{
			// --> Codificar tildes y caracteres especiales
			foreach($rowCamposFor as $indice => &$valor)
				$valor = utf8_encode($valor);

			$listaFormulas[] 		= $rowCamposFor;
			$listFormuAtocomplete[$rowCamposFor['Forcod']] = str_replace($caracter_ma, $caracter_ok, $rowCamposFor['Fornom']);
		}

		// --> Hidden para autocomplete de formulas ya creadas
		echo "<input type='hidden' id='hiddenFormulas' value='".json_encode($listFormuAtocomplete)."'>";

		$colorFila		= "fila1";
		echo "
		<div id='accordionLista' align='center'>
			<h1 style='font-size: 11pt;' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;Formulación</h1>
			<div style='font-family: verdana;font-weight: normal;' align='left'>
				<span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>
					Buscar:&nbsp;</b>
					<input id='buscarFormula' type='text' tipo='obligatorio' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF;width:210px'>
				</span>
				&nbsp;<button style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='verFormula()'>+ Nuevo</button>
				<br><br>
				<div style='height:210px;overflow:auto;background:none repeat scroll 0 0;'>
					<table width='100%' id='listaDeFormulas'>";
					foreach($listaFormulas as $indice => $infoCampo)
					{
						$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1' );
						$colorFila = "";
						echo"
						<tr style='font-size: 9pt;' class='".$colorFila." find'>
							<td onClick='
							verFormula(\"".$infoCampo['Permed']."\", \"".$infoCampo['Percod']."\", \"".$infoCampo['Percam']."\" , \"".$infoCampo['id']."\")'
							style='border-bottom:1px solid #BFBFBF;cursor:pointer'>
								- <span onmouseover='$(this).css({\"color\": \"#3DABE3\",\"font-weight\": \"bold\"})' onmouseout='$(this).css({\"color\": \"#000000\",\"font-weight\": \"normal\"})'>".$infoCampo['Fornom']."</span>
							</td>
							<td><img style='cursor:pointer;' onClick='quitarFormulaDelCampo(\"".$infoCampo['Permed']."_".$infoCampo['Percod']."_".$infoCampo['Percam']."\", \"".$infoCampo['id']."\")' title='Quitar formula del campo' src='../../images/medical/eliminar1.png'></td>
						</tr>";
					}
			echo "
					</table>
				</div>
			</div>
		</div>
		";
	}
	//---------------------------------------------------------------------------------------
	//	-->	Pinta la relacion de los campos fisicos de las tablas con su respectiva formula
	//---------------------------------------------------------------------------------------
	function listaFlujoDeTrabajo()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$arrayLisPro = array();

		$sqlListPro = "SELECT Subcod, Subdes, Tipcod, Tipdes
						 FROM ".$wbasedato."_000030, ".$wbasedato."_000029
						WHERE Subest = 'on'
						  AND Subtip = Tipcod
						  AND Tipest = 'on'
					 ORDER BY Subord
		";
		$resListPro = mysql_query($sqlListPro, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlListPro):</b><br>".mysql_error());
		while($rowListPro = mysql_fetch_array($resListPro))
		{
			$arrayLisPro[$rowListPro['Tipcod']]['Nombre'] = utf8_encode($rowListPro['Tipdes']);
			$arrayLisPro[$rowListPro['Tipcod']]['SubPro'][$rowListPro['Subcod']] = utf8_encode($rowListPro['Subdes']);
		}

		echo "
		<div id='accordionListaFlujoTrabajo' align='center'>
			<h1 style='font-size: 11pt;' align='left'>&nbsp;&nbsp;&nbsp;&nbsp;Configurar ejecución de procesos y transacciones</h1>
			<div style='font-family: verdana;font-weight: normal;font-size: 10pt;' align='left' >
				<table width='100%'>";
				foreach($arrayLisPro as $codPro => $valoresPro)
				{
					echo "<tr><td colspan='2'><b>".$valoresPro['Nombre']."</b></td></tr>";
					foreach($valoresPro['SubPro'] as $codSubPro => $nomSubPro)
					echo "<tr><td width='5%'></td><td><span style='cursor:pointer' onClick='pintarFormularioFlujoTrabajo(\"".$codPro."\", \"".$codSubPro."\"  )' onmouseover='$(this).css({\"color\": \"#3DABE3\",\"font-weight\": \"bold\"})' onmouseout='$(this).css({\"color\": \"#000000\",\"font-weight\": \"normal\"})'>- ".$nomSubPro."</span></td></tr>";
				}
		echo "	</table>
			</div>
		</div>
		";
	}
	//-------------------------------------------------------------------
	//	-->	Pinta el formulario para crear o editar una formula
	//-------------------------------------------------------------------
	function formularioFormula($grupo='', $tabla='', $campo='' , $id='')
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $periodoAct;

		$caracter_ok 		= array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
		$caracter_ma 		= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
		$formulaEnPantalla 	= array();
		$formulaTexto		= '';

		// --> Obtener formula
		if($grupo != '' && $tabla != '' && $campo != '')
		{
			$sqlFormula = "SELECT Percod, Percam, Perdes, Permed, A.Forfor, A.id AS id , A.Fornom, A.Formde
							 FROM ".$wbasedato."_000019, ".$wbasedato."_000005 AS A
							WHERE Permed = '".$grupo."'
							  AND Percod = '".$tabla."'
							  AND Percam = '".$campo."'
							  AND Forano = '".$periodoAct['ano']."'
							  AND Formes = '".$periodoAct['mes']."'
							  AND Fortab = Percod
							  AND Forcam = Percam
							  AND A.id 	 = '".$id."'
			";

			$resFormula = mysql_query($sqlFormula, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlFormula):</b><br>".mysql_error());
			if($rowFormula = mysql_fetch_array($resFormula))
			{
				// --> Codificar tildes y caracteres especiales
				foreach($rowFormula as $indice => &$valor)
					$valor = utf8_encode($valor);

				$rowFormula['Perdes'] = str_replace($caracter_ma, $caracter_ok, $rowFormula['Perdes']);
				$rowFormula['Perdes'] = str_replace($caracter_ma, $caracter_ok, $rowFormula['Perdes']);

				$formulaEnPantalla = json_decode($rowFormula['Forfor'], true);
				/*echo "<pre>";
				print_r($formulaEnPantalla);
				echo "</pre>";*/
				foreach($formulaEnPantalla as $indice => &$valores)
				{
					$formulaTexto.= $valores["nombre"];
					$valores["nombre"] = str_replace($caracter_ma, $caracter_ok, $valores["nombre"]);
				}
			}
		}

		// --> Obtener cuales variables estan relacionadas con el sistema de indicadores
		$camposSgc = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camposPpyeSgc');
		$camposSgc = explode(",", $camposSgc);

		// --> Obtener campos
		$arrayVariables = array();
		$sqlVariables = "SELECT Percod, Percam, Perdes, Permed
						   FROM ".$wbasedato."_000019
						  WHERE Perpaf = 'on'
							AND Perest = 'on'
		";
		$resVariables = mysql_query($sqlVariables, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVariables):</b><br>".mysql_error());
		while($rowVariables = mysql_fetch_array($resVariables))
		{
			// --> Codificar tildes y caracteres especiales
			foreach($rowVariables as $indice => &$valor)
				$valor = utf8_encode($valor);

			$tipo = ((in_array($rowVariables['Percam'], $camposSgc)) ? 'Estadistica' : 'Campo');
			$arrayVariables[$rowVariables['Permed']."_".$rowVariables['Percod']][$rowVariables['Percam']]['Nombre']	= $rowVariables['Perdes'];
			$arrayVariables[$rowVariables['Permed']."_".$rowVariables['Percod']][$rowVariables['Percam']]['Tipo']	= $tipo;
		}

		// --> Obtener metodos de depreciacion
		$arrayMetodos = array("NO_APLICA" => "NO APLICA");
		$sqlMetodos = "  SELECT Mdfcod, Mdfnom, Mdftip
						   FROM ".$wbasedato."_000010
						  WHERE Mdfest = 'on'
		";
		$resMetodos = mysql_query($sqlMetodos, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlMetodos):</b><br>".mysql_error());
		while($rowMetodos = mysql_fetch_array($resMetodos))
		{
			if($rowMetodos['Mdftip'] == 'N')
				$tipo = " (NIIF)";
			if($rowMetodos['Mdftip'] == 'F')
				$tipo = " (FISCAL)";

			$arrayMetodos[$rowMetodos['Mdfcod']] = $rowMetodos['Mdfnom'].$tipo;
		}

		// --> Pintar Calculadora
		echo "
		<div id='accordionFormula' align='left' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
			<h1 style='font-size: 11pt;'>&nbsp;&nbsp;&nbsp;&nbsp;".(isset($rowFormula) ? $rowFormula['Perdes'] : "Nueva formula")."</h1>
			<div>
				<table style='padding:5px;margin:5px;'>
					<tr>
						<td><span style='font-weight:bold;'>Nombre:&nbsp;&nbsp;</span></td>
						<td>
							<input id='Nombreformula' valor='' nombre='' value='".(isset($rowFormula) ? $rowFormula['Fornom'] : "")."' type='text' tipo='obligatorio' placeholder='Digite el nombre de la formula' style='border-radius: 4px;border:1px solid #AFAFAF;width:480px'>
						</td>
					</tr>
					<tr>
						<td><span style='font-weight:bold;'>Campo&nbsp;:&nbsp;&nbsp;</span></td>
						<td>
							<input id='campoFormulado' valor='".(isset($rowFormula) ? $rowFormula['Permed']."_".$rowFormula['Percod']."_".$rowFormula['Percam'] : "")."' nombre='".(isset($rowFormula) ? $rowFormula['Perdes'] : "")."' value='".(isset($rowFormula) ? $rowFormula['Perdes'] : "")."' type='text' tipo='obligatorio' placeholder='Seleccione el campo al cual se le va a asignar la fórmula' style='border-radius: 4px;border:1px solid #AFAFAF;width:480px'>
						</td>
					</tr>
					<tr>
						<td><span style='font-weight:bold;'>Método de depreciación&nbsp;:&nbsp;&nbsp;</span></td>
						<td>
							<select id='metodoDepreciacion' style='border-radius: 4px;border:1px solid #AFAFAF;font-size: 8pt;'>";
							foreach($arrayMetodos as $cod => $nom)
								echo "<option ".((isset($rowFormula) && $rowFormula['Formde'] == $cod) ? "SELECTED='SELECTED'" : "")."' value='".$cod."'>".$nom."</option>";
	echo "					</select>
						</td>
					</tr>
					<tr>
						<td colspan='2'>
							<input type='hidden' id='formulaEnPantalla' value='".json_encode($formulaEnPantalla)."'>
							<b>Fórmula:</b>
							<textarea style='font-family: verdana;font-weight:bold;color:#605B60;background:#FFFFF2;font-size:9pt;width:700px;height:80px;border:solid 1px #999999;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px;' id='displayCalculadora' readonly='readonly'>".$formulaTexto."</textarea><br><br>
						</td>
					</tr>
					<tr>
						<td width='30%'>
							<table>
								<tr>
									<td colspan='2' align='left' ><input type='button' class='botoncalculadora' style='width:93px;' value='Borrar' onclick='borrarCalc(\"ultimo\")'></td>
									<td colspan='2' align='right'><input type='button' class='botoncalculadora' style='width:93px;' value='Limpiar' onclick='borrarCalc(\"todos\")'></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='7' value='7' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='8' value='8' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='9'	value='9' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='/' value='/' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='4' value='4' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='5' value='5' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='6' value='6' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='*' value='*' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='1' value='1' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='2' value='2' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='3' value='3' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='-' value='-' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='0' value='0' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='(' value='(' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor=')' value=')' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='+' value='+' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='?' 	nombre=' SI, '		value=' SI: ' 		tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor=':' 	nombre=' SINO, ' 	value=' SINO: ' 	tipo='Operador' tabla='' periodo=''></td>
									<td colspan='2'>
										<select select='si' class='botoncalculadora' style='width:93px;' onChange='calculadora(this)' tipo='Operador' tabla='' periodo=''>
											<option value='' 	nombre=''>Operador</option>
											<option value='=='	nombre='=='>== Igual a</option>
											<option value='!='	nombre='!='>!= &nbsp;Diferente</option>
											<option value='>'	nombre='>'>> &nbsp;&nbsp;Mayor que</option>
											<option value='<'	nombre='<'>< &nbsp;&nbsp;Menor que</option>
											<option value='>='	nombre='>='>>= Mayor o igual que</option>
											<option value='<='	nombre='<='>=< Menor o igual que</option>
											<option value=' AND '	nombre='&nbsp;AND&nbsp;'>AND Conjunción</option>
											<option value=' OR '	nombre='&nbsp;OR&nbsp;'>OR &nbsp;&nbsp;Disyunción</option>
										</select>
									</td>
								</tr>
							</table>
						</td>
						<td width='70%' style='vertical-align:text-top;' align='left'>
							<fieldset align='center' style='padding:15px;'>
								<legend class='fieldset'>Lista de campos</legend>
								<span style='font-weight:bold;'>
									Buscar campo:&nbsp;&nbsp;</b>
									<input id='buscarVariable' type='text' tipo='obligatorio' placeholder='Digite el nombre del campo' style='border-radius: 4px;border:1px solid #AFAFAF;width:310px'>
								</span>
								<br><br>
								<div style='height:190px;overflow:auto;background:none repeat scroll 0 0;'>
									<table width='100%' id='tablaListaVariables'>";
									$colorFila = 'fila2';
									foreach ($arrayVariables as $tabla => $arrCampos)
									{
										$desTabla  = '';
										$medCodTab = explode('_', $tabla);
										// --> Consultar descripcion de la tabla
										$sqlDesTabla = "SELECT nombre
														  FROM formulario
														 WHERE medico = '".$medCodTab[0]."'
														   AND codigo = '".$medCodTab[1]."' ";
										$resDesTabla = mysql_query($sqlDesTabla, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesTabla):</b><br>".mysql_error());
										if($rowDesTabla = mysql_fetch_array($resDesTabla))
											$desTabla = utf8_encode($rowDesTabla['nombre']);

		echo "								<tr class='encabezadoTabla'><td>".$desTabla."</td><td colspan='2'>Periodo</td></tr>";
										foreach ($arrCampos as $codCampo => $arrValores)
										{
											//$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1');
		echo "								<tr class='find ".$colorFila."'>
												<td>".$arrValores['Nombre']."</td>
												<td onclick='calculadora(this)' valor='".$codCampo."' tabla='".$tabla."' nombre='".$arrValores['Nombre']." (Act)' tipo='".$arrValores['Tipo']."' periodo='Actual' 		class='botoncalculadora' style='width:16px;height:16px;font-size:9pt;cursor:pointer'><b>Act.<b></td>
												<td onclick='calculadora(this)' valor='".$codCampo."' tabla='".$tabla."' nombre='".$arrValores['Nombre']." (Ant)' tipo='".$arrValores['Tipo']."' periodo='Anterior' 	class='botoncalculadora' style='width:16px;height:16px;font-size:9pt;cursor:pointer'><b>Ant.</b></td>
											</tr>";
										}
									}
		echo "						</table>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td colspan='2' align='right'>
							<br>
							<div id='div_mensajes' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div>
						</td>
					</tr>
					<tr><td align='center' colspan='2'><br><button style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='guardarFormula(\"".((isset($rowFormula['id'])) ? $rowFormula['id'] : 'nuevo')."\")'>Guardar</button></td></tr>
				</table>
			</div>
		</div>
		";
	}
	//-------------------------------------------------------------------
	//	-->	Pinta el formulario para crear o editar un flujo de trabajo
	//-------------------------------------------------------------------
	function formularioFlujoTrabajo($proceso, $subProceso)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $periodoAct;

		$caracter_ok 		= array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
		$caracter_ma 		= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

		// --> Consultar el nombre del proceso y del subproceso
		$infoProSub = "SELECT Tipdes, Subdes
						 FROM ".$wbasedato."_000030, ".$wbasedato."_000029
						WHERE Subtip = '".$proceso."'
						  AND Subcod = '".$subProceso."'
						  AND Subtip = Tipcod ";
		$resProSub = mysql_query($infoProSub, $conex) or die("<b>ERROR EN QUERY MATRIX(infoProSub):</b><br>".mysql_error());
		if($rowProSub = mysql_fetch_array($resProSub))
		{
			$nomProceso 	= utf8_encode($rowProSub['Tipdes']);
			$nomSubProceso 	= utf8_encode($rowProSub['Subdes']);
		}

		// --> Consultar la configuracion de la ejecución
		$arrayConfEje = array();
		$infoConEje = "SELECT Ejeord, Ejecfo
						 FROM ".$wbasedato."_000034
						WHERE Ejeano = '".$periodoAct['ano']."'
						  AND Ejemes = '".$periodoAct['mes']."'
						  AND Ejetip = '".$proceso."'
						  AND Ejesub = '".$subProceso."'
						  AND Ejeest = 'on'
						ORDER BY Ejeord ";

		$resConEje = mysql_query($infoConEje, $conex) or die("<b>ERROR EN QUERY MATRIX(infoConEje):</b><br>".mysql_error());
		while($rowConEje = mysql_fetch_array($resConEje))
		{
			// --> Obtener la descripcion del campo
			$sqlDesCam = "SELECT Fornom
						   FROM ".$wbasedato."_000005
						  WHERE Forcod = '".$rowConEje['Ejecfo']."'
							AND Forano = '".$periodoAct['ano']."'
							AND Formes = '".$periodoAct['mes']."'
			";

			$resDesCam = mysql_query($sqlDesCam, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDesCam):</b><br>".mysql_error());
			if($rowDesCam = mysql_fetch_array($resDesCam))
			{
				$arrayConfEje[$rowConEje['Ejeord']]["Descripcion"] = utf8_encode($rowDesCam['Fornom']);
				$arrayConfEje[$rowConEje['Ejeord']]["id"] = $rowConEje['Ejecfo'];
			}
			else
			{
				$arrayConfEje[$rowConEje['Ejeord']]["Descripcion"] = "";
				$arrayConfEje[$rowConEje['Ejeord']]["id"] = "";
			}
		}

		echo "
		<div id='accordionDefFlujoTrabajo' align='left' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
			<h1 style='font-size: 11pt;'>&nbsp;&nbsp;&nbsp;&nbsp;Configuración actual</h1>
			<div>
				<table>
					<tr>
						<td><b>Tipo:</b></td><td>".$nomProceso."</td>
						<td>&nbsp;&nbsp;&nbsp;&nbsp;<b>Nombre:</b></td><td>".$nomSubProceso."</b></td>
					</tr>
				</table><br><br>
				<table width='100%'>
					<tr>
						<td>
							<span style='font-weight:bold;'>
								Agregar formula a la ejecución:&nbsp;&nbsp;</b>
								<input id='buscarFormulaAgr' type='text' tipo='obligatorio' value='' valor='' nombre='' placeholder='Digite el nombre de la formula' style='border-radius: 4px;border:1px solid #AFAFAF;width:385px'>
							</span>
						</td>
					</tr>
					<tr>
						<td><br>
							<fieldset align='center' style='padding:15px;width:685px' >
								<legend class='fieldset'>Orden de ejecución</legend>
								<div style='height:318px;overflow:auto;background:none repeat scroll 0 0;'>
									<ul id='listaOrdenEjecucionSortable2'>";
									foreach($arrayConfEje as $consecutivo => $valores)
										echo "<li idLi='li_".$consecutivo."' class='ui-state-default'>".$consecutivo."</li>";
		echo "						</ul>
									<ul id='listaOrdenEjecucionSortable'>";
									foreach($arrayConfEje as $consecutivo => $valores)
										echo"
										<li idLi='li_".$consecutivo."' class='ui-state-default' formula='".$valores['id']."'>
											<table width='100%'>
												<tr>
													<td width='97%'>".$valores['Descripcion']."<td>
													<td align='right'><img src='../../images/medical/eliminar1.png' title='Quitar' onclick='quitarLiLista(\"li_".$consecutivo."\")' style='cursor:pointer;'></td>
												</tr>
											</table>
										</li>";
		echo "						</ul>
								</div>
							</fieldset>
						</td>
					</tr>
					<tr><td align='center'><br><button style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='guardarEjecucion(\"".$proceso."\", \"".$subProceso."\")'>Guardar</button></td></tr>
					<tr>
						<td align='right'>
							<div id='div_mensajes2' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		";
	}

//=======================================================================================================================================================
//		F I N	 F U N C I O N E S	 P H P
//=======================================================================================================================================================

//=======================================================================================================================================================
//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y
//=======================================================================================================================================================
if(isset($accion))
{
	switch($accion)
	{
		case 'guardarFormula':
		{
			$infoCampo 	= explode("_", $campo);
			$formula 	= utf8_decode(str_replace('\\', '', $formula));

			// --> Actualizar formula
			if($id != 'nuevo')
			{
				$sqlAsignarFor = "
				UPDATE ".$wbasedato."_000005
				   SET Fortab = '".$infoCampo[1]."',
					   Forcam = '".$infoCampo[2]."',
				       Forfor = '".$formula."',
				       Formde = '".$metodoDepreciacion."',
					   Fornom = '".$nombre."'
				 WHERE id	  = '".$id."'
				";
				mysql_query($sqlAsignarFor, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlAsignarFor):</b><br>".mysql_error());
			}
			// --> Insertar nueva formula
			else
			{
				// --> consultar el ultimo codigo y asignar codigo unico a la formula
				$sqlselect = "SELECT Forcod
								FROM ".$wbasedato."_000005
							ORDER BY Forcod ";

				$res = mysql_query($sqlselect, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlselect):</b><br>".mysql_error());
				while($row = mysql_fetch_array($res))
					$codigo = ($row['Forcod'] *1) + 1;

				$sqlInsert 	= "
				INSERT INTO ".$wbasedato."_000005
							(Medico, 			Fecha_data, 	Hora_data,		Forano					,				Formes,			Forcod			,	Fornom	  	   ,				Fortab	,				Forcam	,					Forfor	, 		Forest	,	Formde,						Seguridad, 		id)
				VALUES		('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$periodoAct['ano']."',	'".$periodoAct['mes']."',	'".$codigo."' 	,'".$nombre."' 		,	'".$infoCampo[1]."'	,	'".$infoCampo[2]."'	,			  '".$formula."',		'on'	,	'".$metodoDepreciacion."',	'C-".$wuse."',	'')
				";
				mysql_query($sqlInsert, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInsert):</b><br>".mysql_error());
			}

			echo "Fórmula guardada";
			break;
		}
		case 'pintarFormularioFormula':
		{
			$html = formularioFormula($grupo, $tabla, $campo, $id);
			echo $html;
			break;
		}
		case 'pintarListaCamposConFormulas':
		{
			$html = listarFormulas();
			echo $html;
			break;
		}
		case 'pintarFormularioFlujoTrabajo':
		{
			$html = formularioFlujoTrabajo($proceso, $subProceso);
			echo $html;
			break;
		}
		case 'eliminarFormula':
		{
			$sqlEliminar = "DELETE FROM ".$wbasedato."_000005
							 WHERE id = '".$idRegistro."'
			";
			mysql_query($sqlEliminar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEliminar):</b><br>".mysql_error());
			break;
		}
		case 'guardarEjecucion':
		{
			$arrayListaOrdenEjecucion = json_decode(str_replace('\\', '', $listaOrdenEjecucion));

			// --> Limpiar el proceso o transaccion
			$sqlEliminar = "DELETE FROM ".$wbasedato."_000034
							 WHERE Ejeano = '".$periodoAct['ano']."'
							   AND Ejemes = '".$periodoAct['mes']."'
							   AND Ejetip = '".$proceso."'
							   AND Ejesub = '".$subProceso."'
			";
			mysql_query($sqlEliminar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEliminar):</b><br>".mysql_error());

			foreach($arrayListaOrdenEjecucion as $orden => $valores)
			{

				$sqlInsert 	= "
				INSERT INTO ".$wbasedato."_000034
							(Medico, 			Fecha_data, 	Hora_data, 		Ejeano,						Ejemes,						Ejetip,			Ejesub  		 , 		Ejecfo				,  Ejeord	 	,	Ejeest,		Seguridad	 , 	id)
				VALUES		('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$periodoAct['ano']."',	'".$periodoAct['mes']."',	'".$proceso."',	'".$subProceso."',	'".$valores."'		, '".($orden+1)."'	,	'on'  , 	'C-".$wuse."',	'')
				";
				mysql_query($sqlInsert, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInsert):</b><br>".mysql_error());

			}
			echo "Ejecución guardada";
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
		// --> Activar Acordeones

		$("#accordionFormula").accordion({
			heightStyle: "fill"
		});

		var altura = ($("#accordionFormula").find("div:eq(0)").height()/2)-38;

		$("#accordionLista").accordion({
			heightStyle: "fill",
			collapsible: true
		}).find("div:eq(0)").css("height", altura).parent().css("height", altura);

		/*$("#accordionRelacion").accordion({
			heightStyle: "Auto",
			collapsible: true
		}).find("div:eq(0)").css("height", altura).parent().css("height", altura+10);*/

		$("#accordionListaFlujoTrabajo").accordion({
			heightStyle: "fill",
			collapsible: true
		}).find("div:eq(0)").css("height", altura).parent().css("height", altura);

		// --> Activar el buscador de texto, para los campos con formula
		$('#buscarFormula').quicksearch('#listaDeFormulas .find');

		// --> Activar el buscador de texto, para las variables
		$('#buscarVariable').quicksearch('#tablaListaVariables .find');

		// --> Cargar autocomplete de campo
		crear_autocomplete("hiddenCampos", "campoFormulado", "");
	});

	//-----------------------------------------------------------
	//	--> Funcion que simula el display de la calculadora
	//-----------------------------------------------------------
	function calculadora(elemento)
	{
		var formulaEnPantalla 	= new Object();
		formulaEnPantalla		= JSON.parse($("#formulaEnPantalla").val());
		var nuevoIndex			= formulaEnPantalla.length;
		var textoPantalla		= '';

		if(elemento != '')
		{
			var nombreElem							= "";
			var valorElem							= "";
			var elemento 							= $(elemento);
			formulaEnPantalla[nuevoIndex] 			= new Object();
			formulaEnPantalla[nuevoIndex].tipo 		= elemento.attr("tipo");

			if(elemento.attr("select") == 'si')
			{
				nombreElem 	= elemento.find("option:selected").attr("nombre");
				valorElem	= elemento.find("option:selected").val()
			}
			else
			{
				nombreElem 	= ((elemento.attr("nombre") == undefined) ? elemento.attr("valor") : elemento.attr("nombre"));
				valorElem	= elemento.attr("valor");
			}

			if(valorElem == "")
				return;

			formulaEnPantalla[nuevoIndex].nombre	= nombreElem;
			formulaEnPantalla[nuevoIndex].valor 	= valorElem;
			formulaEnPantalla[nuevoIndex].tabla		= elemento.attr("tabla");
			formulaEnPantalla[nuevoIndex].periodo	= elemento.attr("periodo");
		}

		$(formulaEnPantalla).each(function(index, objeto){
			textoPantalla = textoPantalla+objeto.nombre;
		});

		$("#displayCalculadora").val(textoPantalla);
		$("#formulaEnPantalla").val(JSON.stringify(formulaEnPantalla));
	}
	//-----------------------------------------------------------
	//	--> Borrar variables de la pantalla de la calculadora
	//-----------------------------------------------------------
	function borrarCalc(tipo)
	{
		var formulaEnPantalla 	= new Object();
		formulaEnPantalla		= JSON.parse($("#formulaEnPantalla").val());

		if(tipo == 'ultimo')
		{
			ultimoIndex	= formulaEnPantalla.length-1;
			formulaEnPantalla.splice(ultimoIndex, 1);
		}
		else
			formulaEnPantalla 	= new Array();

		$("#formulaEnPantalla").val(JSON.stringify(formulaEnPantalla));
		calculadora("");
	}
	//-----------------------------------------------------------
	//	--> Cargar autocomplete de campos
	//-----------------------------------------------------------
	function crear_autocomplete(HiddenArray, CampoCargar, accionSelect)
	{
		ArrayValores  = JSON.parse($("#"+HiddenArray).val());

		var ArraySource   = new Array();
		var index		  = -1;
		for (var CodVal in ArrayValores)
		{
			index++;
			ArraySource[index] = {};
			ArraySource[index].value  = CodVal;
			ArraySource[index].label  = ArrayValores[CodVal];
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
				switch(accionSelect)
				{
					case 'agregarFormulaEjecucion':
					{
						$( "#"+CampoCargar ).val('');
						$( "#"+CampoCargar ).attr('valor', '');
						$( "#"+CampoCargar ).attr('nombre', '');

						// --> Obtener ultimo consecutivo
						var consecutivo = $("#listaOrdenEjecucionSortable2 li").length;
						consecutivo++;

						// --> Agregar li
						var html1 = "<li idLi='li_"+consecutivo+"' class='ui-state-default'>"+consecutivo+"</li>";
						$("#listaOrdenEjecucionSortable2").append(html1);

						var html2 = "	<li idLi='li_"+consecutivo+"' class='ui-state-default' formula='"+ui.item.value+"'>"
											+"<table width='100%'><tr><td width='97%'>"+ui.item.nombre+"<td>"
											+"<td align='right'><img src='../../images/medical/eliminar1.png' title='Quitar' onclick='quitarLiLista(\"li_"+consecutivo+"\")' style='cursor:pointer;'></td></tr>"
									+"	</li>";
						$("#listaOrdenEjecucionSortable").append(html2);

						return false;
						break;
					}
				}
				return false;
			}
		});
		limpiaAutocomplete(CampoCargar);
	}
	//----------------------------------------------------------------------------------
	//	--> Quita un elemento li de la lista de orden de ejecución
	//----------------------------------------------------------------------------------
	function quitarLiLista(li)
	{
		$("[idLi="+li+"]").hide(300, function() {
			$(this).remove();
			$("#listaOrdenEjecucionSortable2 li").each(function(index, value){
				index++;
				$(this).attr("idLi", "li_"+index).text(index);
			});
			$("#listaOrdenEjecucionSortable li").each(function(index, value){
				index++;
				$(this).attr("idLi", "li_"+index);
				$(this).find("img").attr("onClick", "quitarLiLista(\"li_"+index+"\")");
			});
		});
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
	//----------------------------------------------------------------------------------
	//	--> Guardar la formula
	//----------------------------------------------------------------------------------
	function guardarFormula(id)
	{
		// --> Validar que hayan seleccionado un campo
		if($("#campoFormulado").attr("valor") == '')
		{
			mostrar_mensaje("Debe seleccionar el campo al cual le va asignar la fórmula", "div_mensajes");
			return;
		}
		// --> Validar que hayan ingresado una formula
		if($("#displayCalculadora").val() == '')
		{
			mostrar_mensaje("Debe ingresar una fórmula", "div_mensajes");
			return;
		}
		// --> Validar que hayan ingresado el nombre
		if($("#Nombreformula").val() == '')
		{
			mostrar_mensaje("Debe ingresar el nombre de la fórmula", "div_mensajes");
			return;
		}

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'guardarFormula',
			campo:					$("#campoFormulado").attr("valor"),
			formula:				$("#formulaEnPantalla").val(),
			nombre:					$("#Nombreformula").val(),
			metodoDepreciacion:		$("#metodoDepreciacion").val(),
			id:						id
		}, function(respuesta){
			mostrar_mensaje(respuesta, "div_mensajes");
			verListaDeFormulas();
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Deja un campo sin formula
	//----------------------------------------------------------------------------------
	function quitarFormulaDelCampo(campo, idRegistro)
	{
		if(confirm("¿Desea quitar la fórmula de este campo?"))
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'eliminarFormula',
				idRegistro:				idRegistro
			}, function(respuesta){

				if($("#campoFormulado").attr("valor") == campo)
					verFormula();

				setTimeout(function() {
					mostrar_mensaje("Formula eliminada", "div_mensajes");
				}, 200);

				verListaDeFormulas();
			});
		}
	}
	//----------------------------------------------------------------------------------
	//	--> Mostrar mesaje de alerta en pantalla
	//----------------------------------------------------------------------------------
	function mostrar_mensaje(mensaje, idDiv)
	{
		$("#"+idDiv).html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#"+idDiv).css({"width":"300","opacity":" 0.9","fontSize":"11px"});
		$("#"+idDiv).show(500);
		setTimeout(function() {
			$("#"+idDiv).hide(500);
		}, 12000);
	}
	//----------------------------------------------------------------------------------
	//	--> Pinta el formulario de ingreso de una formula
	//----------------------------------------------------------------------------------
	function verFormula(grupo='', tabla='', campo='', id='')
	{

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion:         'pintarFormularioFormula',
			grupo:			grupo,
			tabla:			tabla,
			campo:			campo,
			id:				id
		}, function(html){
			$("#contenedorFormulario").html(html);

			// --> Activar el acordeon
			$("#accordionFormula").accordion({
				heightStyle: "fill"
			});
			// --> Activar el buscador de texto, para las variables
			$('#buscarVariable').quicksearch('#tablaListaVariables .find');

			// --> Cargar autocomplete de campo
			crear_autocomplete("hiddenCampos", "campoFormulado", "");

		});
	}
	//----------------------------------------------------------------------------------
	//	--> Pinta la lista de campos con formulas
	//----------------------------------------------------------------------------------
	function verListaDeFormulas()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion:         'pintarListaCamposConFormulas'
		}, function(html){

			$("#contenedorListaFormulas").html(html);
			// --> Activar el acordeon
			$("#accordionLista").accordion({
				heightStyle: "fill",
				collapsible: true
			});

			// --> Activar el buscador de texto, para los campos con formula
			$('#buscarFormula').quicksearch('#listaDeFormulas .find');
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Pinta lel formulario para crear o editar un flujo de trabajo
	//----------------------------------------------------------------------------------
	function pintarFormularioFlujoTrabajo(proceso, subProceso)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   '',
			accion:         'pintarFormularioFlujoTrabajo',
			proceso:		proceso,
			subProceso:		subProceso
		}, function(html){

			$("#contenedorFormulario").html(html);
			// --> Activar el acordeon
			$("#accordionDefFlujoTrabajo").accordion({
				heightStyle: "fill",
				collapsible: true
			});

			// --> Cargar autocomplete de formulas
			crear_autocomplete("hiddenFormulas", "buscarFormulaAgr", "agregarFormulaEjecucion");

			// --> Activar sortable
			$( "#listaOrdenEjecucionSortable" ).sortable({
				placeholder: "ui-state-highlight"
			});

			$( "#listaOrdenEjecucionSortable" ).disableSelection();
		});
	}
	//----------------------------------------------------------------------------------
	//	--> Guarda la configuracion de ejecucion de un proceso o transaccion
	//----------------------------------------------------------------------------------
	function guardarEjecucion(proceso, subProceso)
	{
		listaOrdenEjecucion	= new Array();

		$("#listaOrdenEjecucionSortable li").each(function(index){
			listaOrdenEjecucion.push( $(this).attr("formula") );
		});

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'guardarEjecucion',
			proceso:				proceso,
			subProceso:				subProceso,
			listaOrdenEjecucion: 	JSON.stringify(listaOrdenEjecucion)
		}, function(respuesta){
			mostrar_mensaje(respuesta, "div_mensajes2");
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
			font-family: verdana;
		}
		.fila2
		{
			background-color: #E8EEF7;
			color: #000000;
			font-size: 9pt;
			padding:3px;
			font-family: verdana;
		}

		.encabezadoTabla {
			background-color: #2a5db0;
			color: #ffffff;
			font-size: 9pt;
			padding:3px;
			font-family: verdana;
		}
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:3px;
		}
		.bordeCurvo{
			-moz-border-radius: 0.4em;
			-webkit-border-radius: 0.4em;
			border-radius: 0.4em;
		}
		.divAreaTrabajo{
			border: 				1px solid #dddddd;
			color: 					#362b36;
			-moz-border-radius: 	6px;
			-webkit-border-radius: 	6px;
			border-radius: 			6px;
			background-color: 		#F2F5F7;
			width:					100%;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 6pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}

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

		.botoncalculadora {
			cursor:				pointer;
			border: 			1px solid #999999;
			background-color:	#D2E8FF;
			width:				45px;
			height:				35px;
			font-size: 			12pt;
			-moz-border-radius:3px;
			-webkit-border-radius:3px;
			border-radius:3px;
		}
		.ui-autocomplete{
			max-width: 	480px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}
		.bordeCurvo{
			-moz-border-radius: 0.4em;
			-webkit-border-radius: 0.4em;
			border-radius: 0.4em;
		}

		#listaOrdenEjecucionSortable{
			list-style-type: 	none;
			float: 				left;
			margin: 			0;
			padding: 			0;
			width:				95%;
		}
		#listaOrdenEjecucionSortable2 {
			list-style-type: 	none;
			float: 				left;
			margin: 			0;
			padding: 			0;
			width:				5%;
		}
		#listaOrdenEjecucionSortable li, #listaOrdenEjecucionSortable2 li { cursor:default;margin: 0 5px 5px 5px; padding: 5px; font-size: 1em; height: 1.2em;font-family: verdana;font-weight: normal;}
		#listaOrdenEjecucionSortable li { cursor:move;}
		#listaOrdenEjecucionSortable li, #listaOrdenEjecucionSortable2 li { height: 1.2em; line-height: 1em; font-family: verdana;font-weight: normal;}
		.ui-state-highlight { height: 1.2em; line-height: 1em;}

		/*.lista_cuentas{
			list-style-type: none; margin: 0; padding: 0; display:inline-block; vertical-align:top;
			font-size: 3pt;
			font-weight: normal;
		}
		.lista_cuentas li { margin: 0 3px 3px 0; padding: 0.4em; padding-left: 1.5em; font-size: 2.5em; }
		.item_cuenta  {border: 1px solid #C3D9FF; background: #C3D9FF; color: #000;}
		.item_cuenta  {
			background: #fff; /* Old browsers */
			background: -moz-linear-gradient(top,  #fff 0%, #C3D9FF 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#C3D9FF)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#C3D9FF 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#C3D9FF',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#C3D9FF')";
			zoom:1;
			cursor: pointer;
		}*/



	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php
	$arrayVariables 	= array();
	// --> Crear hidden con un json del maestro de campos (Variables)
	$sqlVariables = "SELECT Percod, Percam, Perdes, Permed, F.nombre
					   FROM ".$wbasedato."_000019, formulario AS F
					  WHERE Perpaf = 'on'
						AND Perest = 'on'
						AND Permed = F.medico
						AND Percod = F.codigo
	";
	$resVariables = mysql_query($sqlVariables, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlVariables):</b><br>".mysql_error());
	while($rowVariables = mysql_fetch_array($resVariables))
	{
		// --> Codificar tildes y caracteres especiales
		foreach($rowVariables as $indice => &$valor)
			$valor = utf8_encode($valor);

		$arrayVariables[$rowVariables['Permed']."_".$rowVariables['Percod']."_".$rowVariables['Percam']]= str_replace($caracter_ma, $caracter_ok, $rowVariables['Perdes'].' ('.$rowVariables['nombre'].')');
	}

	echo "<input type='hidden' id='hiddenCampos' value='".json_encode($arrayVariables)."'>";
	// --> Fin Crear hidden

	echo '
	<table width="100%" cellspacing="10">
		<tr>
			<td width="45%" valign="top" id="contenedorListaFormulas">';
				listarFormulas();
	echo '	</td>
			<td rowspan="2" width="55%" valign="top" id="contenedorFormulario">';
				formularioFormula();
	echo '	</td>
		</tr>
		<tr>
			<td width="45%" valign="top" id="contenedorListaFlujoTrabajo">';
				listaFlujoDeTrabajo();
	echo'	</td>
		</tr>
	</table>
	';
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
