<?php
include_once("conex.php");
if(!isset($_SESSION['user']) && !isset($accion))
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
	include_once("Funciones_sgc.php");
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, '01', 'sgc');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");

//=====================================================================================================================================================================
// F U N C I O N E S
//=====================================================================================================================================================================

	//-----------------------------
	//	CALCULADORA
	//-----------------------------
	function pintar_calculadora($row_datos)
	{
		global $conex;
		global $wbasedato;

		if (isset($row_datos))
		{
			$formula_guardar 	= $row_datos['Indfoc'];
			$OperacioResGeneral	= $row_datos['Indorg'];
			$arr_formulaCodigos = explode ('|',$formula_guardar);
			$formula_nombres	= '';
			foreach($arr_formulaCodigos as $codVariable)
			{
				// --> 	2014-03-17: EL nombre de las variables de la formula se consultan segun como esten en el momento
				//		para asi traerlos actualizados, Jerson andres trujillo.
				$codVariable = trim($codVariable);
				if(stripos($codVariable, '['))
					$codVariable = substr($codVariable, 0, stripos($codVariable, '['));

				// --> La variable corresponde a un indicador o a una estadistica
				if($codVariable{0} == 'I' || $codVariable{0} == 'E')
				{
					// --> Obtener el nombre de la variable
					$sqlNomVar = "SELECT Indnom
									FROM ".$wbasedato."_000001
								   WHERE Indcod = '".$codVariable."'
					";
					$resSqlNomVar = mysql_query($sqlNomVar, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
					if($rowSqlNomVar = mysql_fetch_array($resSqlNomVar))
						$formula_nombres.= $rowSqlNomVar['Indnom'];
					else
						$formula_nombres.='ERROR: VARIABLE NO EXISTE ('.$codVariable.')';
				}
				// --> La variable corresponde a un dato
				else
				{
					// --> Obtener el nombre de la variable
					$campo = ($codVariable{0} == 'D') ? 'Datnom' : 'Datval';

					$sqlNomVar = "SELECT ".$campo." AS Campo
									FROM ".$wbasedato."_000010
								   WHERE Datcod = '".$codVariable."'
					";
					$resSqlNomVar = mysql_query($sqlNomVar, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
					if($rowSqlNomVar = mysql_fetch_array($resSqlNomVar))
						$formula_nombres.= $rowSqlNomVar['Campo'];
					else
						$formula_nombres.='ERROR: VARIABLE NO EXISTE ('.$codVariable.')';
				}
			}
		}

		// --> Obtener si las variables manejan calculo general
		$listaElementosDet 	= '';
		$existeUnSi 		= false;
		if(isset($formula_guardar) && $formula_guardar != '')
		{
			$arrVarFormula = explode('|', $formula_guardar);
			foreach($arrVarFormula as $ValVariable)
			{
				if(strpos($ValVariable, "[*]"))
				{
					$listaElementosDet.= (($listaElementosDet == '') ? 'si' : '|si');
					$existeUnSi 		= true;
				}
				else
					$listaElementosDet.= (($listaElementosDet == '') ? 'no' : '|no');
			}
		}

		echo "
		<table style='background-color: #E8EEF7;border:solid 2px orange;padding:5px;'>
			<tr>
				<td colspan=2>
					<div id='div_wformula' style='display:none'></div>";
		// --> En este hidden se guarda, por cada variable de la formula si alguna de ellas maneja la posibilidad de tener un calculo general.
		echo "		<input type='hidden' id='listaElementosDet' value='".$listaElementosDet."'>";
		// --> En este hidden se guarda los Nombres de los elementos de la formula separados por |
		echo "		<input type='hidden' id='wformula_elementos' name='wformula_elementos' value='".((isset($formula_nombres)) ? $formula_nombres : '')."'>";
		// --> En este hidden se guarda los Codigos de los elementos de la formula separados por |
		echo "		<input type='hidden' id='wformula_guardar' name='wformula_guardar' value='".((isset($formula_guardar)) ? $formula_guardar : '')."'>
					<textarea style='width:580px;height:100px;border:solid 1px orange;' name='wformula' id='wformula'   readonly='readonly'>".((isset($formula_nombres)) ? utf8_decode($formula_nombres) : '')."</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<br><br><br>
					<table>
						<tr>
							<td colspan='2' align='left'><input type='button' class='botoncalculadora' style='width:78px;' id='borrar' name='borrar' value='<=' onclick='calculadora(\"borrar\")'></td>
							<td colspan='2' align='right'><input type='button' class='botoncalculadora' style='width:78px;' id='limpiar' name='limpiar' value='Limpiar' onclick='calculadora(\"Limpiar_pantala\")'></td>
						</tr>";
						$array_operadores=traer_operadores();
						$columna=1;
						foreach($array_operadores as $wvalor => $wnombre)
						{
							$codigo_y_nombre = explode('|', $wnombre);
							switch ($columna)
							{
								case 1:
									{
										echo"<tr>
												<td>
													<input type='button' class='botoncalculadora' id='".$codigo_y_nombre[1]."' name='".$codigo_y_nombre[1]."' rel='".$codigo_y_nombre[0]."'  value='".$wvalor."' onclick='calculadora(\"".$codigo_y_nombre[1]."\")'>
												</td>";
										$columna++;
										break;
									}
								case 2:case 3:
									{
										echo "	<td>
													<input type='button' class='botoncalculadora' id='".$codigo_y_nombre[1]."' name='".$codigo_y_nombre[1]."'  rel='".$codigo_y_nombre[0]."' value='".$wvalor."' onclick='calculadora(\"".$codigo_y_nombre[1]."\")'>
												</td>";
										$columna++;
										break;
									}
								case 4:
									{
										echo "	<td>
													<input type='button' class='botoncalculadora' id='".$codigo_y_nombre[1]."' name='".$codigo_y_nombre[1]."'  rel='".$codigo_y_nombre[0]."' value='".$wvalor."' onclick='calculadora(\"".$codigo_y_nombre[1]."\")'>
												</td>
											</tr>";
										$columna=1;
										break;
									}
							}
						}
	echo"
					</table>
				</td>
				<td style='vertical-align:text-top; font-family: verdana;font-size: 11pt;' align='center'>
					<b>Agregar:</b><br>
					<input type='radio' name='tipo_var' id='tipo_ind' value='vindicador'   	onClick='tabla_varibles(\"".$row_datos['Indcod']."\")'>Indicador&nbsp;
					<input type='radio' name='tipo_var' id='tipo_est' value='vestadistica' 	onClick='tabla_varibles(\"".$row_datos['Indcod']."\")'>Estadística&nbsp;
					<input type='radio' name='tipo_var' id='tipo_dat' value='vdato' 		onClick='tabla_varibles(\"".$row_datos['Indcod']."\")' checked='checked'>Dato&nbsp;<br>
					<img title='Busque el nombre o parte del nombre de la constante' width='16' height='16' border='0' src='../../images/medical/HCE/lupa.PNG'/>
					<input type='text' style='width:240px; border: 1px solid #999999;' name='wbus_variable' id='wbus_variable' onkeypress='' onblur='tabla_varibles(\"".$row_datos['Indcod']."\")' title='Buscar' ><br>
					<br>
					<div id='lista_variables' style='border:1px solid #999999;background:none repeat scroll 0 0 #FFFFFF;height:150px;width:380px;overflow:auto;'>
					</div>
				</td>
			</tr>
			<tr><td><br></td></tr>
			<tr>
				<td></td>";
	// --> Actualizacion 2013-10-17.
	$title1 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			<b>Sumatoria:</b> Totaliza el resultado de cada detalle.<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Promedio:</b>  &nbsp;Media aritmética del conjunto de valores del detalle.<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Generar otra consulta:</b>&nbsp;No se hará ningún tipo de agrupamiento<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;sino que se totalizara cada detalle realizando una nueva consulta.<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Sin resultado general:</b>&nbsp;Solo tendra resultados detallados.
		</span>";

	echo"		<td align='center'>
					<table id='TableOperacionGeneral' style='".(($existeUnSi) ? "" : "display:none;")."border:1px solid #999999;width:380px; font-size: 9pt;font-weight: bold;'>
						<tr>
							<td class='encabezadoTabla'>
								Tipo de operación para el resultado general:
								&nbsp;&nbsp;&nbsp;&nbsp;<img width='14' height='14' class='tooltip' title='".$title1."' src='../../images/medical/root/info.png'>
							</td>
						</tr>
						<tr>
							<td style='background-color: #D2E8FF;' align='center'>
								<input type='radio' name='tipo_res_gen' ".(($OperacioResGeneral == 'S' || $OperacioResGeneral == '' || !isset($OperacioResGeneral)) ? " CHECKED='CHECKED' " : "")." value='S'   >Sumatoria&nbsp;
								<input type='radio' name='tipo_res_gen' ".(($OperacioResGeneral == 'P') ? " CHECKED='CHECKED' " : "")." value='P' 	>Promedio&nbsp;
								<input type='radio' name='tipo_res_gen' ".(($OperacioResGeneral == 'C') ? " CHECKED='CHECKED' " : "")." value='C' 	>Generar otra consulta&nbsp;<br>
								<input type='radio' name='tipo_res_gen' ".(($OperacioResGeneral == 'O') ? " CHECKED='CHECKED' " : "")." value='O' 	>Sin resultado general
							</td>
						</tr>
					</table>
					<br>
				</td>
			</tr>
		</table>";
	}

	//Funcion para consultar el tema para guardar las opciones de menu
	function consultar_tema()
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$q = " SELECT Temcod
			     FROM root_000076
				WHERE Tememp = '".$wemp_pmla."'
				  AND Temprf = '".$wbasedato."'
				";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array ($res);
		return $row['Temcod'];
	}
	//Funcion para mostrar las magnitudes
	function traer_magnitudes()
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Magcod, Magnom "
			  ." FROM ".$wbasedato."_000006 "
			  ."WHERE Magest  = 'on' "
			  ."Order by Magnom";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;

	}
	//Funcion para mostrar los operadores matematicos en la calculadora
	function traer_operadores()
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Datval, Datnom, Datcod "
			  ."  FROM ".$wbasedato."_000010 "
			  ."  WHERE Datest  = 'on' "
			  ."	AND (Datope	= 'on' || Datnum = 'on') "
			  ."  Order by Datord";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$valor=$result[0];
			$nom=$result[1];
			$cod=$result[2];
			$array_res[$valor]=$cod.'|'.$nom;
		}
		return $array_res;

	}

	//Funcion para mostrar los grupos
	function traer_grupos($tipo)
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Grucod, Grunom
				 FROM ".$wbasedato."_000005
				WHERE Gruest  = 'on'
				  AND Gruppi IN('A', '".(($tipo == "indicador") ? "I" : "E" )."')
			 ORDER BY Grunom";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query concultar grupos: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;

	}

	//Funcion para mostrar las perspectivas
	function traer_perspectivas($tipo)
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Percod, Pernom
		         FROM ".$wbasedato."_000002
				WHERE Perest = 'on'
				  AND Perppi IN('A', '".(($tipo == "indicador") ? "I" : "E" )."')
			 ORDER BY Pernom";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query consultar perspectivas: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;
	}

	//Funcion para mostrar las jerarquias
	function traer_jerarquias($tipo)
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Jercod, Jernom
				 FROM ".$wbasedato."_000003
				WHERE Jerest  = 'on'
				  AND Jerppi IN('A', '".(($tipo == "indicador") ? "I" : "E" )."')
			 ORDER BY Jernom";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query consultar jerarquias: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;
	}

	function traer_periocidades()
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		$q = " SELECT Pricod, Prinom "
			  ."  FROM ".$wbasedato."_000008 "
			  ."  WHERE Priest= 'on' "
			  //."  AND   Descripcion != NULL "
			  ."  Order by Pricod";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;
	}

	function traer_caracteristicas($tipo)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		$q = " SELECT Carcod, Carnom
				 FROM ".$wbasedato."_000004
				WHERE Carest= 'on'
			      AND Carppi IN('A', '".(($tipo == "indicador") ? "I" : "E" )."')
			 ORDER BY Carnom";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query consultar caracteristicas: ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;
	}

	//Funcion para mostrar los centros de costos en el select del formulario
	function consultar_cc($valor, $wempresa, $cco_select='ninguno')
	{
		global $conex;
		global $wemp_pmla;
		$q = "  SELECT  Emptcc
				FROM    root_000050
				WHERE   Empcod LIKE '%".$wempresa."%'";
		$res = mysql_query($q,$conex)or die("Error: " . mysql_errno() . " - en el query (Seleccionar Tabla para CCO): ".$q." - ".mysql_error());;

		$options = '<option value="" >Seleccione..</option>';
		while($row = mysql_fetch_array($res))
		{
			$buscaNombre = str_replace('*','%',str_replace(' ','%',(trim($valor))));
			$buscaNombre = strtoupper(strtolower($buscaNombre));
			$tabla_CCO = $row['Emptcc'];

			if($tabla_CCO=='costosyp_000005')
				$campo="Cconom";
			else
				$campo="Ccodes";
			if ($tabla_CCO != 'NO APLICA')
			{
				$q_2  = " SELECT Ccocod AS codigo, ".$campo." AS nombre "
						."  FROM ".$tabla_CCO.""
						." WHERE ".$campo." LIKE '%".trim($buscaNombre)."%' "
						."   AND Ccoest = 'on' "
						." ORDER BY 1 ";
				$res2 = mysql_query($q_2,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar CCO): ".$q_2." - ".mysql_error());

				while($row2 = mysql_fetch_array($res2))
				{
					(($cco_select != 'ninguno' AND $cco_select==$row2['codigo']) ? $selected='selected' : $selected='');
					$options .= '<option value="'.$row2['codigo'].'" '.$selected.'>'.$row2['codigo'].' - '.utf8_decode(ucwords(strtolower($row2['nombre']))).'</option>';
				}
			}
		}
		return $options;
	}

	//Funcion para mostrar los responsables en el select del formulario
	function responsable($valor, $resp_select='ninguno')
	{
		global $conex;
		global $wemp_pmla;
		$options = '<option value="" >Seleccione..</option>';
		$q = "  SELECT Codigo, Descripcion "
			  ."  FROM usuarios "
			  ." WHERE Descripcion LIKE '%".trim($valor)."%' "
			  ."   AND Descripcion != '' "
			  ."   AND Activo   = 'A'"
			  ." Order by Descripcion";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query(Filtrar Responsable): ".$q." - ".mysql_error());

			while($result = mysql_fetch_array($res))
			{
				(($resp_select != 'ninguno' AND $resp_select==$result['Codigo']) ? $selected='selected' : $selected='');
				$options .= '<option value="'.$result['Codigo'].'" '.$selected.'>'.utf8_decode(ucwords(strtolower($result['Descripcion']))).'</option>';
			}
			return $options;
	}
	function traer_empresas()
	{
		global $wbasedato;
		global $conex;
		$options = '<option value="%" >Todas</option>';
		$q = " SELECT Empcod, Empdes "
		  ."     FROM root_000050 "
		  ."    WHERE Empest = 'on' "
		  ."    Order by Empdes";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query(seleccionar empresas): ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;
	}

	//Funcion para generar el codigo del indicador
	function generar_codigo($prefijo)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wfecha;
		global $whora;

		//Bloquear la tabla contra escritura
		$q = "lock table " . $wbasedato . "_000001 LOW_PRIORITY WRITE";
		$err = mysql_query($q, $conex);

		//Consultar el ultimo codigo generado
		$q_codigo =" SELECT max(SUBSTRING( Indcod FROM 2)*1) as Indcod
					   FROM ". $wbasedato ."_000001
					";
		$res_codigo = mysql_query($q_codigo, $conex) or die (mysql_errno(). " - en el query (Consultar todos codigo): ".$q_codigo . " - " . mysql_error());
		$row_codigo =mysql_fetch_array($res_codigo);

		//Genero el codigo para el indicador
		$nuevo_cod=$row_codigo['Indcod']+1;
		$wcodigo= $prefijo.$nuevo_cod;

		//Desbloqueo la tabla y envio el codigo generado
		$q = " UNLOCK TABLES";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
		return $wcodigo;
	}

	//-------------------------------------------------------------
	//	Funcion que graba en la base de datos los referentes
	//-------------------------------------------------------------
	function grabar_referentes ($wlista_referentes, $wcodigo)
	{
		global $wbasedato;
		global $conex;
		global $wfecha;
		global $whora;
		global $wuse;


		$array_referentes = explode ('|#|',$wlista_referentes);
		//echo '<pre>';print_r($array_referentes);echo '</pre>';

		// --> Inactivo todos los referentes que tenga el indicador
		if(count($array_referentes) > 0)
		{
			$q_inacti = "UPDATE ".$wbasedato."_000007
							SET Refest		= 	'off'
						  WHERE Refind		= 	'".$wcodigo."'
						";
			mysql_query($q_inacti,$conex) or die ("Error: ".mysql_errno()." - en el query(Inactivar referentes): ".$q_inacti." - ".mysql_error());
		}

		// --> Recorro el array de los referentes
		foreach ($array_referentes as $campos)
		{
			$array_campos = explode ('|', $campos);

			if ($array_campos[1]!='' || $array_campos[2]!='' || $array_campos[3]!='')
			{
				if ($array_campos[0]=='nuevo')	//Creo un nuevo referente
				{
					$q_refere = " INSERT INTO ".$wbasedato."_000007
										(	Medico, 		Fecha_data, Hora_data, 		Refind, 		Refnom, 	       			Refmet,				Refdes 			 , Refest, Seguridad, id)
								  VALUES('".$wbasedato."','".$wfecha."','".$whora."','".$wcodigo."','".$array_campos[1]."','".$array_campos[2]."','".$array_campos[3]."', 'on' ,'C-".$wuse."','')
								";
				}
				else	//Actualizo la informacion del referente
				{
					$q_refere = "UPDATE ".$wbasedato."_000007
									SET Fecha_data 	= 	'".$wfecha."' ,
										Hora_data	= 	'".$whora."',
										Refnom		= 	'".$array_campos[1]."',
										Refmet		= 	'".$array_campos[2]."',
										Refdes		= 	'".$array_campos[3]."',
										Refest		= 	'on'
								  WHERE id			= 	'".$array_campos[0]."'
								";
				}
				mysql_query($q_refere,$conex) or die ("Error: ".mysql_errno()." - en el query(Actualizar o insertar referente): ".$q_refere." - ".mysql_error());
			}
		}
	}
	//-----------------------------------------------------------------------------------------------------
	//	Funcion para obetener una array con los indicadores y estadisticas que pertenecen a ulguna formula
	//-----------------------------------------------------------------------------------------------------
	function indi_y_estadi_usadas()
	{
		global $wbasedato;
		global $conex;
		$arr_indi_esta_en_uso = array();

		$q_lista = "SELECT Indfoc
					  FROM ".$wbasedato."_000001
					 WHERE (Indfoc LIKE '%E%' OR Indfoc LIKE '%I%')
					";
		$res_lista = mysql_query($q_lista, $conex) or die ("Error: ".mysql_errno()." - en el query(Obtener lista de indicadores y estadisticas usadas): ".$q_lista." - ".mysql_error());
		while($arr_lista = mysql_fetch_array($res_lista))
		{
			$valores_formula = explode('|', $arr_lista['Indfoc']);
			foreach($valores_formula as $cod_valor)
			{
				if($cod_valor{0} == 'I' || $cod_valor{0} == 'E')
					$arr_indi_esta_en_uso[$cod_valor] = '';
			}
		}
		return $arr_indi_esta_en_uso;
	}
	//--------------------------------------------------------------------
	//	Funcion para validar si el codigo de una opcion de menu ya existe
	//--------------------------------------------------------------------
	function validar_codigo_opcion_menu($wcodigo)
	{
		global $conex;

		$q_validar_codigo = " SELECT count(*) as cantidad
							    FROM root_000080
							   WHERE Tabcod = '".$wcodigo."'
							";
		$res_validar_codigo = mysql_query($q_validar_codigo,$conex) or die ("Error: ".mysql_errno()." - en el query(Validar codigo de opcion de menu): ".$q_validar_codigo." - ".mysql_error());
		$row_validar_codigo = mysql_fetch_array ($res_validar_codigo);
		if ($row_validar_codigo['cantidad'] > 0)
			return false;
		else
			return true;
	}
	//--------------------------------------------
	//	Funcion para grabar las opciones de menu
	//--------------------------------------------
	function grabar_opciones_menu ($wcodigo, $wnombre, $wtema, $westado)
	{
		global $wbasedato, $conex, $wfecha, $whora, $wuse;

		if(validar_codigo_opcion_menu($wcodigo))
		{
			//Inserto en la 80 la opcion de menu
			$q_opc_menu = " INSERT INTO root_000080
										( Medico, Fecha_data, Hora_data, 	  Tabcod, 				Tabdes, 			  Taburl , Tabind, 	  Tabpro, Tabref, Tabest, Seguridad, id)
								  VALUES('root','".$wfecha."','".$whora."','".$wcodigo."','".utf8_decode($wnombre)."','".$wcodigo."','on',	  'off',   '',   '".$westado."' ,'C-root','')
								";
			mysql_query($q_opc_menu,$conex) or die ("Error: ".mysql_errno()." - en el query(Insertar opcion de menu): ".$q_opc_menu." - ".mysql_error());

			//Consulto el codigo de la opcion de pestaña de los inidcadores
			$q_cod = " SELECT Tabcod
						 FROM root_000080, root_000081
						WHERE Rtptem = '".$wtema."'
						  AND Rtpest = 'on'
						  AND Rtptab = Tabcod
						  AND Tabpro = 'on'
						  AND Tabest = 'on'
					 GROUP BY Tabcod
					 ORDER BY Tabcod
					";
			$res_cod 	= mysql_query($q_cod,$conex) or die ("Error: ".mysql_errno()." - en el query(Consultar tab del menu de inidcadores): ".$q_cod." - ".mysql_error());
			$arr_cod    = array();
			while($row_cod = mysql_fetch_array($res_cod))
			{
				$arr_cod[] = $row_cod['Tabcod'];
			}

			//Inserto en la 81 la relacion del arbol de opciones
			$q_opc_menu2 = " INSERT INTO root_000081
										( Medico, Fecha_data,  Hora_data, 	 Rtptem, 					Rtptab, 								Rtpstb,  Rtpord, Rtpest, Seguridad, id)
								  VALUES('root','".$wfecha."','".$whora."','".$wtema."','".(($wcodigo{0}=='I') ? $arr_cod[0] : $arr_cod[1])."','".$wcodigo."','1',  'on' ,'C-root','')
								";
			mysql_query($q_opc_menu2,$conex) or die ("Error: ".mysql_errno()." - en el query(Insertar opcion de menu): ".$q_opc_menu2." - ".mysql_error());
			return $wcodigo;
		}
		else
		{
			echo '<br>'.$wcodigo.' ';
			$consecutivo = (substr($wcodigo, 1)*1)+1;
			echo $wcodigo = 'I'.$consecutivo;
			$wcodigo = grabar_opciones_menu ($wcodigo, $wnombre, $wtema, $westado);
		}
		return $wcodigo;
	}
	//-------------------------------------------------------------
	//	Funcion para consultar y listar los indicadores existentes
	//-------------------------------------------------------------
	function pintar_lista($busc_nombre='%', $busc_cco='%', $busc_resp='%', $busc_tipo='', $busc_estado='', $codigo_indicador='')
	{
		global $wbasedato;
		global $conex;
		$cco_ya_consu = array();
		$arr_lista_modificables = obtener_lista_modificable();

		// --> Obtener todos los indicadores y estadisticas que pueden modificar su resultado
		$arr_lista_modificables = array();
		$q_modificables = " SELECT Indcod
							  FROM ".$wbasedato."_000001
							 WHERE Indare = 'on'
							   AND Indest = 'on'
						";
		$res_modificables = mysql_query($q_modificables,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicadores modificables): ".$q_modificables." - ".mysql_error());
		while($row_modificables = mysql_fetch_array($res_modificables))
		{
			$arr_lista_modificables[] = $row_modificables['Indcod'];
		}
		// --> Fin obtener indicadores y estadisticas que modifican

		$q_consul_ind="	SELECT Indnom, Emptcc, Indcco, Descripcion, Indest, Indcod, Indtes, Indare , Indfoc, Indfon
						  FROM ".$wbasedato."_000001, usuarios, root_000050
						 WHERE ";
		if($codigo_indicador != '')
		$q_consul_ind.="   	   Indcod = '".$codigo_indicador."'
						   AND ";

		$q_consul_ind.="       Indnom LIKE '%".$busc_nombre."%'
						   AND Indemp = Empcod
						   AND Indrme = Codigo
						   AND Descripcion LIKE '%".$busc_resp."%' ";
		if($busc_estado == 'on')
		$q_consul_ind.="   AND Indest = 'on'";
		elseif($busc_estado == 'off')
		$q_consul_ind.="   AND Indest != 'on'";

		if($busc_tipo == 'on')
		$q_consul_ind.="   AND Indtes = 'on'";
		elseif($busc_tipo == 'off')
		$q_consul_ind.="   AND Indtes != 'on'";

		$q_consul_ind.=" ORDER BY Indnom
						";
		//echo $q_consul_ind;
		$res_consul_ind = mysql_query($q_consul_ind,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar indicadores y estadisticas): ".$q_consul_ind." - ".mysql_error());
		$num_indicad    = mysql_num_rows ($res_consul_ind);
		$fila_lista     = "Fila2";

		$arr_indi_esta_en_uso = array();
		$arr_indi_esta_en_uso = indi_y_estadi_usadas();

		echo '<div style="color: #000000;font-size: 8pt;font-weight: bold;"> Registros: <span id="num_resg"></span></div>';
		echo"
			<table width='99%' id='table_lista'>
				<tr class='encabezadoTabla' align='center'>
					<td>Nombre</td><td>Centro de costos</td><td>Responsable Medici&oacuten</td><td>Tipo</td><td>Estado</td><td align='center' style='background-color: #FFFFFF;'></td>
				</tr>";

		$num_res = 0;
		if ($num_indicad >0)
		{
			while ($row_inidcadores = mysql_fetch_array($res_consul_ind))
			{
				$mensaje = '';
				// --> Obtener si el indicador tiene configuracion erronea es decir, cuando algun componente de la formula de
				//	   el indicador es modificable pero el indicador no lo es.
				if($row_inidcadores['Indare'] != 'on')
				{
					$arr_componentes_formu = explode('|', $row_inidcadores['Indfoc']);
					$arr_nombre_comp_formu = explode('|', $row_inidcadores['Indfon']);
					foreach($arr_componentes_formu as $indice => $valor_formula)
					{
						$valor_formula = trim($valor_formula);
						if(strstr($valor_formula, '['))
						{
							$valor_formula = substr($valor_formula, 0, stripos($valor_formula, '['));
						}

						if($valor_formula != '' && ($valor_formula{0} == 'E' || $valor_formula{0} == 'I') && in_array($valor_formula,$arr_lista_modificables))
						{
							$mensaje.= '> '.$valor_formula.'-'.$arr_nombre_comp_formu[$indice].'.<br>';
						}
					}
				}

				// --> Consultar el nombre del centro de costos
				$pintar_res = 'NO';

				if(array_key_exists($row_inidcadores['Indcco'] ,$cco_ya_consu))
				{
					$row_cco['nombre'] = $cco_ya_consu[$row_inidcadores['Indcco']];
					$pintar_res = 'SI';
				}
				else
				{
					if($row_inidcadores['Emptcc']=='costosyp_000005')
						$campo="Cconom";
					else
						$campo="Ccodes";

					$q_cco =" SELECT ".$campo." AS nombre
								FROM ".$row_inidcadores['Emptcc']."
							   WHERE Ccocod  = '".$row_inidcadores['Indcco']."'
								 AND ".$campo." LIKE '%".$busc_cco."%'
								 AND Ccoest = 'on' ";
					$res_cco = mysql_query($q_cco,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar CCO): ".$q_cco." - ".mysql_error());
					if($row_cco = mysql_fetch_array($res_cco))
					{
						$cco_ya_consu[$row_inidcadores['Indcco']] = $row_cco['nombre'];
						$pintar_res = 'SI';
					}
				}
				// --> Fin consultar nombre
				if($pintar_res == 'SI')
				{
					if ($fila_lista=='Fila2')
						$fila_lista = "Fila1";
					else
						$fila_lista = "Fila2";
					$onclick = "mostrar_ficha(\"".$row_inidcadores['Indcod']."\", \"null\", \"".(($row_inidcadores['Indtes']=='on') ? 'estadistica' : 'indicador')."\")";
					echo "
					<tr class=".$fila_lista.">
						<td onclick='".$onclick."' style='cursor:pointer;'>".utf8_decode($row_inidcadores['Indnom'])."</td>
						<td onclick='".$onclick."' style='cursor:pointer;'>".$row_cco['nombre']."</td>
						<td onclick='".$onclick."' style='cursor:pointer;'>".$row_inidcadores['Descripcion']."</td>
						<td onclick='".$onclick."' style='cursor:pointer;' align='center'>".(($row_inidcadores['Indtes']=='on') ? 'Estadística' : 'Indicador')."</td>
						<td onclick='".$onclick."' style='cursor:pointer;' align='center'>".(($row_inidcadores['Indest']=='on') ? 'ACTIVO' : 'INACTIVO')."</td>
						<td align='center' style='background-color: #FFFFFF;'>";
					// --> Opcion de eliminar
					if(!array_key_exists($row_inidcadores['Indcod'], $arr_indi_esta_en_uso) && $codigo_indicador == '')
						echo "<img width='13' height='13' tooltip2='si' title='<span style=\"color: #000000;font-size: 10pt;\">Eliminar</span>' onclick='eliminar_indicador(\"".$row_inidcadores['Indcod']."\")' src='../../images/medical/eliminar1.png' style='cursor:pointer;'>";
					// --> Indicador modificable
					if($row_inidcadores['Indare'] == 'on')
						echo "<br><img height='16' width='16' style='cursor:help;' tooltip2='si' title='<span style=\"color: #000000;font-size: 10pt;\">".(($row_inidcadores['Indcod']{0} == 'E') ? 'Estadística' : 'Indicador')." modificable</span>' src='../../images/medical/sgc/Refresh-128.png'>";
					if($mensaje != '')
						echo "<br><img height='16' width='16' style='cursor:help;' tooltip2='si' title='<table><tr><td class=\"mes_tooltip\" align=\"center\"><img height=\"17\" width=\"17\" src=\"../../images/medical/sgc/Warning-32.png\"> Mensaje de alerta:</td></tr><tr><td align=\"center\" style=\"color: #000000;font-size: 9pt;\">".(($row_inidcadores['Indcod']{0} == 'E') ? 'Esta Estadística' : 'Este Indicador')." debe ser modificable ya que<br>las siguientes variables de su formula lo son:<br>".$mensaje."</td></tr></table>' src='../../images/medical/sgc/Warning-32.png'>";
					echo"
						</td>
					</tr>";

					$num_res++;
				}
			}
		}
		else
		{
			echo'	<tr class="fila2" >
						<td colspan=6 align=center><b>No se encontraron indicadores.</b></td>
					</tr>';
		}
		echo "</table>";
		echo "<script>$('#num_resg').text('".$num_res."');</script>";
	}
	//-------------------------------------------------------------
	//	Funcion para consultar y listar los indicadores existentes
	//-------------------------------------------------------------
	function listar_indicadores($codigo_indicador)
	{
		global $wbasedato;
		global $conex;

		echo "
			<table width='97%' border='0' align='center' cellspacing='1' cellpadding='2' name='lista_indicadores' id='lista_indicadores'>
				<tr>
					<td colspan=5>
						<table width='100%'>
							<tr>
								<td width='13%' class='fila2' title='Filtrar lista de parametros' style='cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick='pintar_filtros(\"div_flitros\")'>
									<b>Filtrar lista</b>
									<img width='13' height='13' src='../../images/medical/HCE/lupa.PNG'>
								</td>
								<td width='74%'></td>
								<td width='13%' class='fila2' title='Agregar nuevo indicador' style='cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick='selecc_tipo_ficha()'>
									<b>Nuevo</b>
									<img border='0' src='../../images/medical/HCE/mas.PNG'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td></td>
				</tr>
				<tr>
					<td colspan='6' align='center'>
						<table width='50%' id='div_flitros' class='borderDiv' style='display:none'>
							<tr>
								<td align='center' class='parrafo_text' colspan='6'><b>Selección de filtros</b></td>
							</tr>
							<tr>
								<td align='center' class='Encabezadotabla'>Código:</td>
								<td align='center' class='Encabezadotabla'>Nombre:</td>
								<td align='center' class='Encabezadotabla'>Centro de costos:</td>
								<td align='center' class='Encabezadotabla'>Responsable medición:</td>
								<td align='center' class='Encabezadotabla'>Tipo:</td>
								<td align='center' class='Encabezadotabla'>Estado:</td>
							</tr>
							<tr>
								<td align='center' class='Fila2'><input type='text' size=8 name='busc_codigo' id='busc_codigo' onBlur='filtrar_lista(\"\")'/></td>
								<td align='center' class='Fila2'><input type='text' size=38 name='busc_nombre' id='busc_nombre' onBlur='filtrar_lista(\"\")'/></td>
								<td align='center' class='Fila2'><input type='text' size=30 name='busc_cco' id='busc_cco' onBlur='filtrar_lista(\"\")'/></td>
								<td align='center' class='Fila2'><input type='text' size=30 name='busc_resp' id='busc_resp' onBlur='filtrar_lista(\"\")'/></td>
								<td align='center' class='Fila2'>
									<select name='busc_tipo' id='busc_tipo' onchange='filtrar_lista(\"\")'>
										<option value=''>Todos</option>
										<option value='off'>Indicador</option>
										<option value='on'>Estadística</option>
									</select>
								</td>
								<td align='center' class='Fila2'>
									<select name='busc_estado' id='busc_estado' onchange='filtrar_lista(\"\")'>
										<option value=''>Todos</option>
										<option value='on'>Activo</option>
										<option value='off'>Inactivo</option>
									</select>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td><br>
						<div id='div_lista'>";
						echo pintar_lista('%', '%', '%', '', '', $codigo_indicador);
		echo"			</div>
					</td>
				</tr>
			</table>";
	}

	// --> Obtener todos los indicadores y estadisticas que pueden modificar su resultado
	function obtener_lista_modificable()
	{
		global $conex;
		global $wbasedato;
		$arr_lista_modificables = array();
		$q_modificables = " SELECT Indcod
							  FROM ".$wbasedato."_000001
							 WHERE Indare = 'on'
							   AND Indest = 'on'
						";
		$res_modificables = mysql_query($q_modificables,$conex) or die ("Error: ".mysql_errno()." - en el query(Indicadores modificables): ".$q_modificables." - ".mysql_error());
		while($row_modificables = mysql_fetch_array($res_modificables))
		{
			$arr_lista_modificables[] = $row_modificables['Indcod'];
		}
		return $arr_lista_modificables;
	}

	function pintar_ficha($cod_indicador, $tipo)
	{
		global $conex;
		global $wbasedato;
		global $wfecha;
		global $whora;
		//--------------------------------------------------------
		// Consultar los datos del indicador o de la estadistica
		//--------------------------------------------------------
		if ($cod_indicador != 'nuevo')
		{
			$q_datos = " 	SELECT *
							  FROM ".$wbasedato."_000001
							 WHERE Indcod = '".$cod_indicador."'
						";
			$res_datos = mysql_query($q_datos, $conex) or die (mysql_errno() . $q_datos . " - " . mysql_error());
			$row_datos = mysql_fetch_array($res_datos);
		}
		echo "	<br><br>
		<div name='ficha' id='ficha' align='left' class='borderDiv2' style='margin: 10px;' width='97%'>
				<table width='96%'>
					<tr>
						<td style='font-family: COURIER NEW;font-size: 11pt;'><b>Ficha ".$tipo."</b></td>
						<td align='right'>
							<div class='fila2' onclick='$(\"#ficha\").hide(600);filtrar_lista(\"\");' title='Cerrar ficha' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick=''>
											<b>Cerrar</b>
											<img width='10' height='10' border='0' style='cursor:pointer;' title='Eliminar Fila' src='../../images/medical/eliminar1.png'>
							</div>
						</td>
					</tr>
				</table>";
		echo"	<div class='borderDiv Titulo_azul' align=center>
				".((isset($row_datos)) ? strtoupper(utf8_decode($row_datos['Indnom'])): 'NUEVO INGRESO' )."
				</div><br>";
		echo"<div id='ref_basicos' align='left'>
				<table width='900' border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td align='left' style='font-size: 11pt;'>";
							?>
								<a href="#null" onclick="javascript:verSeccionCaracterizacion('div_basicos');">
									<img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;DATOS BASICOS
								</a>
							<?php
		echo"			</td>
					</tr>
				</table>
			</div>";
		//=========================
		//	Div Datos Basicos
		//=========================
		echo'<div id="div_basicos" align="center" class="borderDiv ">
				<table width="93%" border="0" align="center" cellspacing="1" cellpadding="2" >';
		echo"		<tr>
						<td width=14%>&nbsp;</td>
						<td width=35%>&nbsp;</td>
						<td width=14%>&nbsp;</td>
						<td width=37%>&nbsp;</td>
					</tr>";
		if (isset($row_datos))
			$wcodigo=$row_datos['Indcod'];
		else
			$wcodigo='???';

		//	-->	Nombre del indicador y Codigo
		echo"		<tr class='fila2'>
						<td class='encabezadoTabla resalto' >Nombre:&nbsp;</td>
						<td class='carBold'>
							<input type='text' style='width:300px' name='wnombre' id='wnombre' ".((isset($row_datos)) ? 'value=\''.utf8_decode($row_datos['Indnom']).'\'': '' )." onmouseover='validar(this);' onblur='validar(this)'>
							<div id='div_wnombre' style='display:none;' ></div>
						</td>
						<td class='encabezadoTabla resalto' >C&oacute;digo:&nbsp;</td>
						<td class='carBold' align=center>
							<div style='font-weight:bold;font-size:16px;color:red;' name='wcodigo' id='wcodigo'>".$wcodigo."</div>
						</td>
					</tr>";
		//	-->	Objetivo y Interpretación
		echo"		<tr class='fila1' tipo='indicador'>
						<td class='encabezadoTabla resalto'>Objetivo:</td>
						<td class='carBold'>
							<textarea style='width:300px' rows=3 name='wobjetivo' id='wobjetivo' onmouseover='validar(this);' onblur='validar(this)'>".((isset($row_datos)) ? utf8_decode($row_datos['Indobj']) : '' )."</textarea>
							<div id='div_wobjetivo' style='display:none;' ></div>
						</td>
						<td class='encabezadoTabla resalto' >Interpretaci&oacute;n:</td>
						<td class='carBold'>
							<textarea style='width:300px' rows=3 name='winterpretacion' id='winterpretacion' onmouseover='validar(this);' onblur='validar(this)'>".((isset($row_datos)) ? utf8_decode($row_datos['Indint']): '' )."</textarea>
							<div id='div_winterpretacion' style='display:none;' ></div>
						</td>
					</tr>";

			// 	--> Perpesctiva y jerarquias

				//  -->Consultar perspectivas
				$array_perspectivas=traer_perspectivas($tipo);
				//  -->Consultar jerarquias
				$array_jerarquias=traer_jerarquias($tipo);

			echo"		<tr class='fila2' ".((count($array_perspectivas) > 0 || count($array_jerarquias) > 0) ? "" : "tipo=indicador").">
							<td ".((count($array_perspectivas) > 0) ? "" : "tipo=indicador")." class='encabezadoTabla resalto'>Perspectiva:<br><font size=1>(Balanced scorecard)</font></td>
							<td ".((count($array_perspectivas) > 0) ? "" : "tipo=indicador")." class='carBold' id='checkbox_wperspectiva'>";
								echo "<table>";
								$columna=1;
								foreach($array_perspectivas as $clave => $valor)
								{
									$check='';
									if(isset($row_datos))
									{
										(($row_datos['Indper']==$clave) ? $check='checked=checked' : $check='' );
									}

									if ($columna==1)
									{
										echo "<tr class='fila2'><td><b><input type='radio' name='wperspectiva' id='wperspectiva'  ".$check." value='".$clave."' /></b></td><td><b>".utf8_decode($valor)."</b>&nbsp;&nbsp;</td>";
										$columna=2;
									}
									else
									{
										echo "<td><b><input type='radio' name='wperspectiva' id='wperspectiva' ".$check." value='".$clave."' /></b></td><td><b>".utf8_decode($valor)."</b></td>";
										$columna=1;
									}
								}
				echo		"	</table>
								<div id='div_wperspectiva' style='display:none;' ></div>
							</td>
							<td ".((count($array_jerarquias) > 0) ? "" : "tipo=indicador")." class='encabezadoTabla resalto'>Jerarqu&iacute;a:</td>
							<td ".((count($array_jerarquias) > 0) ? "" : "tipo=indicador")." class='carBold' id='checkbox_wjerarquia'>";
								echo "<table><tr class='fila2'>";
								foreach($array_jerarquias as $clave => $valor)
								{
									$check='';
									if(isset($row_datos))
									{
										(($row_datos['Indjer']==$clave) ? $check='checked=checked' : $check='' );
									}
									echo "<td><b><input type='radio' name='wjerarquia' id='wjerarquia'  ".$check." value='".$clave."' /></b></td><td><b>".utf8_decode($valor)."</b></td>";
								}
				echo "			</tr></table>
								<div id='div_wjerarquia' style='display:none;' ></div>
							</td>
						</tr>";
			//  -->	Caracteristicas y Responde A

				//  -->Consultar perspectivas
				$array_tema=traer_caracteristicas($tipo);
				//  -->Consultar jerarquias
				$array_grupos=traer_grupos($tipo);

			echo"		<tr class='fila1' ".((count($array_tema) > 0 || count($array_grupos) > 0) ? "" : "tipo=indicador").">
							<td ".((count($array_tema) > 0) ? "" : "tipo=indicador")." class='encabezadoTabla resalto' >Caracter&iacute;sticas:</td>
							<td ".((count($array_tema) > 0) ? "" : "tipo=indicador")." class='carBold' id='checkbox_wcaracteristica'>";
								$columna=1;
								if(isset($row_datos))
								{
									$caracteristicas = explode (',', $row_datos['Indcar']);
								}
								echo "<table>";
								foreach($array_tema as $wcod => $wnom)
								{
									$check='';
									if(isset($caracteristicas))
									{
										((in_array($wcod, $caracteristicas)) ? $check='checked=checked' : $check='' );
									}
									if ($columna==1)
									{
										echo "<tr class='fila1'><td><b>&nbsp;<input type='checkbox' id='wcaracteristica' name='wcaracteristica'  ".$check." value='".$wcod."' >&nbsp;".utf8_decode($wnom)."</b></td>";
										$columna=2;
									}
									else
									{
										echo "<td><b>&nbsp;<input type='checkbox' id='wcaracteristica' name='wcaracteristica'  ".$check." value='".$wcod."' >&nbsp;".utf8_decode($wnom)."</b></td></tr>";
										$columna=1;
									}
								}
			echo "				</table>
								<div id='div_wcaracteristica' style='display:none;' ></div>
							</td>
							<td ".((count($array_grupos) > 0) ? "" : "tipo=indicador")." class='encabezadoTabla resalto' >Responde a:</td>
							<td ".((count($array_grupos) > 0) ? "" : "tipo=indicador")." class='carBold' id='checkbox_wresponde_a' >";
								echo "<table>";
								$tr_i="<tr>";
								$tr_f="";
								if(isset($row_datos))
								{
									$responde_a = explode (',', $row_datos['Indgru']);
								}
								foreach($array_grupos as $clave => $valor)
								{
									$check='';
									if(isset($responde_a))
									{
										((in_array($clave, $responde_a)) ? $check='checked=checked' : $check='' );
									}
									//estas validaciones son para mostrar los resultados en dos columnas dentro de la misma celda
									if($tr_i=='<tr>')
									{
										echo $tr_i;
										$tr_i="";
										$tr_f="";
									}
									else
									{
										$tr_i="<tr>";
										$tr_f="</tr>";
									}
									echo "<td class='fila1' width='50%'><b><input type='checkbox' id='wresponde_a' name='wresponde_a'  ".$check." value='".$clave."' >";
									echo "&nbsp;".utf8_decode($valor)."</b></td>";
									echo $tr_f;
								}
								echo "</table>";
				echo		"
							<div id='div_wresponde_a' style='display:none;' ></div>
							</td>
						</tr>";
				// 	--> Empresa
				echo 	"<tr class='fila2'>
							<td class='encabezadoTabla resalto' >Empresa:</td>
							<td class='carBold'>
								<select style='width:300px' name='wempresa' id='wempresa'  onchange='recargarUnidad(\"wempresa\", \"\",\"wunidad\",\"filtrar_cco\");' onmouseover='validar(this)' onblur='validar(this)'>
									<option value=''>Seleccione..</option>
									<option value='%'>TODAS</option>";
									$array_empresas=traer_empresas();
									$check='';
									foreach($array_empresas as $wcod_emp => $wnom_emp)
									{
										if(isset($row_datos))
										{
											(($row_datos['Indemp']==$wcod_emp) ? $check='selected' : $check='' );
										}
										echo "<option value='".$wcod_emp."' ".$check.">".$wnom_emp."</option>";
									}
				echo "			</select>
								<div id='div_wempresa' style='display:none;' ></div>
							</td>";
				//	--> Unidad
				((isset($row_datos)) ? $options=consultar_cc('%', $row_datos['Indemp'],$row_datos['Indcco']) : $options=consultar_cc('', '%') );
				echo"
							<td class='encabezadoTabla resalto' >Unidad:</td>
							<td class='carBold'>Buscar:
								<img title='Busque el nombre o parte del nombre del centro de costo' width='15' height='15' border='0' src='../../images/medical/HCE/lupa.PNG'/>
								<input type='text' title='Busque el nombre o parte del nombre del centro de costo' style='width:224px' name='wbus_unidad' id='wbus_unidad' onkeypress='return enterBuscar(\"wunidad\", event);' onblur='recargarUnidad(\"wempresa\", this,\"wunidad\",\"filtrar_cco\");cambioImagen(\"ccload\",\"\");' onfocus='cambioImagen(\"\",\"ccload\");' >
								<img id='ccload' style='display:none;' width='15' height='15' border='0' src='../../images/medical/ajax-loader9.gif' />
								<select style='width:300px' name='wunidad' id='wunidad' onmouseover='validar(this)' onblur='validar(this)'>
									$options
								</select>
								<div id='div_wunidad' style='display:none;' ></div>
							</td>
						</tr>";
				// 	-->  Codigo de ley
				echo 	"<tr class='fila1'>
							<td class='encabezadoTabla resalto' tipo='indicador'>C&oacute;digo de ley:</td>
							<td class='carBold' tipo='indicador'><input type='text' style='width:300px' name='wcod_ley' id='wcod_ley' ".((isset($row_datos)) ? 'value=\''.utf8_decode($row_datos['Indcle']).'\'': '' )." ></td>
							<td class='encabezadoTabla resalto' >Estado:</td>
							<td class='carBold' align=center >";
				//	-->	Estado
				if(isset($row_datos))
				{
					if($row_datos['Indest']=='on')
					{
						$check_si='checked=checked';
						$check_no='';
					}
					else
					{
						$check_si='';
						$check_no='checked=checked';
					}
				}
				else
				{
					$check_si='checked=checked';
					$check_no='';
				}
				echo 	"
									Activo<input type='radio' name='westado' id='westado' value='on' $check_si >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									Inactivo<input type='radio' name='westado' id='westado' value='off' $check_no>
								</td>
							</tr>";
		echo '		</table><br>
				</div>';
				//-->Cierro div Datos Basicos

		echo 	"<div id='ref_basicos' align='left'>
					<table width='900' border='0' cellspacing='0' cellpadding='0'>
						<tr>
							<td align='left' style='font-size: 11pt;'>";
								?>
								<a href="#null" onclick="javascript:verSeccionCaracterizacion('div_medicion');">
										<img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;DATOS DE MEDICI&Oacute;N
								</a>
								<?php
		echo "				</td>
						</tr>
					</table>
				</div>";
				//=========================
				//	Div Datos Medicion
				//=========================
		echo '	<div id="div_medicion" align="center" class="borderDiv displCaracterizacion">
					<table width="93%" border="0" align="center" cellspacing="1" cellpadding="2">';
				echo	"<tr>
							<td width=15%>&nbsp;</td>
							<td width=35%>&nbsp;</td>
							<td width=15%>&nbsp;</td>
							<td width=35%>&nbsp;</td>
						</tr>";
				//	-->	Periodicidad y mes de inicio
				echo 	"<tr class='fila2'>
							<td class='encabezadoTabla resalto' >Periodicidad:</td>
							<td class='carBold'>
								<select style='width:300px' name='wperiocidad' id='wperiocidad' onmouseover='validar(this)' onblur='validar(this)'>
									<option value=''>Seleccione..</option>";
										//Consultar Periocidades
										$array_perioci=traer_periocidades();
										$check='';
										foreach($array_perioci as $wcod => $wnom)
										{
											if(isset($row_datos))
											{
												(($row_datos['Indpri']==$wcod) ? $check='selected' : $check='' );
											}
											echo "<option value='".$wcod."' ".$check.">".utf8_decode($wnom)."</option>";
										}
				echo 	"		</select>
								<div id='div_wperiocidad' style='display:none;' ></div>
							</td>
							<td class='encabezadoTabla resalto'>Mes de inicio:</td>
							<td class='carBold'>
								<select style='width:300px' name='wmesinicio' id='wmesinicio' onmouseover='validar(this)' onblur='validar(this)'>
									<option value=''>Seleccione..</option>";
										//Consultar Periocidades
										$array_meses =	array( 1=>'Enero', 2=>'Febrero', 3=>'Marzo', 4=>'Abril', 5=>'Mayo', 6=>'Junio',
															   7=>'Julio', 8=>'Agosto', 9=>'Septiembre', 10=>'Octubre',11=>'Noviembre', 12=>'Diciembre' );
										foreach($array_meses as $wcod => $wnom)
										{
											if(isset($row_datos))
											{
												(($row_datos['Indmim']==$wcod) ? $check='selected' : $check='' );
											}
											echo "<option value='".$wcod."' ".$check.">".utf8_decode($wnom)."</option>";
										}
				echo 	"		</select>
								<div id='div_wmesinicio' style='display:none;' ></div>
							</td>
						</tr>";
				((isset($row_datos)) ? $options=responsable('', $row_datos['Indrme']) : $options=responsable('') );
				//	-->	Magnitud y Numero de decimales
				echo 	"<tr class='fila1'>
							<td class='encabezadoTabla resalto' >Unidad de medida:</td>
							<td class='carBold'>
								<select style='width:300px' name='wmagnitud' id='wmagnitud' onmouseover='validar(this)' onblur='validar(this)' >
									<option value=''>Seleccione..</option>";
										//Consultar Magnitudes
										$array_magnitudes=traer_magnitudes();
										$check='';
										foreach($array_magnitudes as $wcod_mag => $wnom_mag)
										{
											if(isset($row_datos))
											{
												(($row_datos['Indmag']==$wcod_mag) ? $check='selected' : $check='' );
											}
											echo "<option value='".$wcod_mag."' ".$check.">".utf8_decode($wnom_mag)."</option>";
										}
				echo "			</select>
								<div id='div_wmagnitud' style='display:none;' ></div>
							</td>
							<td class='encabezadoTabla resalto' >Numero de decimales:</td>
							<td class='carBold' align='center'>
								<select style='width:35px' name='wnum_decimales' id='wnum_decimales'>";
								for($f=0; $f<8; $f++)
								{
									echo "<option ".((isset($row_datos) && $row_datos['Indnde']==$f) ? 'selected' : '' ).">".$f."</option>";
								}
				echo	"		</select>
							</td>
						</tr>";
				//	-->	Responsable de medicion Y actualizar resultados
				echo 	"<tr class='fila2'>
							<td class='encabezadoTabla resalto' >Responsable:</td>
							<td class='carBold'>Buscar:
								<img title='Busque el nombre o parte del nombre del usuario' width='15' height='15' border='0' src='../../images/medical/HCE/lupa.PNG'/>
								<input type='text' style='width:224px' name='wbus_repons_med' id='wbus_repons_med' title='Busque el nombre o parte del nombre del usuario' onkeypress='return enterBuscar(\"wresp_med\", event);' onblur='recargarLista(this,\"wresp_med\",\"filtrar_resp\");cambioImagen(\"ccload1\",\"\");' onfocus='cambioImagen(\"\",\"ccload1\");' />
								<img id='ccload1' style='display:none;' width='15' height='15' border='0' src='../../images/medical/ajax-loader9.gif' />
								<select style='width:300px' name='wresp_med' id='wresp_med' in='Ideesc' onmouseover='validar(this)' onblur='validar(this)' >
									$options
								</select>
								<div id='div_wresp_med' style='display:none;' ></div>
							</td>
							<td class='encabezadoTabla resalto' >Modifica resultados:</td>
							<td class='carBold' align='center'>
								Si<input type='radio' name='wactualizar_res' id='wactualizar_res_si' ".((isset($row_datos) && $row_datos['Indare']=='on') ? 'checked' : '' ).">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								No<input type='radio' name='wactualizar_res' id='wactualizar_res_no' ".((!isset($row_datos) || $row_datos['Indare']!='on') ? 'checked' : '' ).">
								<div id='div_wactualizar_res' style='display:none;' ></div>
							</td>
						</tr>";
				//	-->	Formula
				echo 	"<tr class='fila1'>
							<td class='encabezadoTabla resalto' >Formula:</td>
							<td class='carBold' colspan=3 align=center><br>";
								pintar_calculadora($row_datos);
		echo "				<br>
							</td>
						</tr>";
		echo '		</table><br>
				</div>';
				//-->Cierro div Datos medicion
		echo 	"<div id='ref_basicos' align='left' tipo='indicador'>
					<table width='900' border='0' cellspacing='0' cellpadding='0'>
						<tr>
							<td align='left' style='font-size: 11pt;'>";
								?>
								<a href="#null" onclick="javascript:verSeccionCaracterizacion('div_evaluacion');">
										<img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;DATOS DE SEGUIMIENTO Y CONTROL
								</a>
								<?php
		echo "				</td>
						</tr>
					</table>
				</div>";
				//=========================
				//	Div Datos Evaluacion
				//=========================
		echo '	<div id="div_evaluacion" align="center" class="borderDiv displCaracterizacion">
					<table width="93%" border="0" align="center" cellspacing="1" cellpadding="2">';
				echo	"<tr>
							<td width=15%>&nbsp;</td>
							<td width=35%>&nbsp;</td>
							<td width=15%>&nbsp;</td>
							<td width=35%>&nbsp;</td>
						</tr>";
				//	-->	Desempeño maximo y desempeño minimo
				echo 	"<tr class='fila2'>
							<td class='encabezadoTabla resalto'>Limite superior:</td>
							<td class='carBold'>
								<input type='text' style='width:300px' validar_num='si' name='wdes_max' id='wdes_max' value='".((isset($row_datos)) ? $row_datos['Inddsu'] : '' )."' >
							</td>
							<td class='encabezadoTabla resalto' >Limite inferior:</td>
							<td class='carBold'>
								<input type='text' style='width:300px' validar_num='si' name='wdes_min' id='wdes_min' value='".((isset($row_datos)) ? $row_datos['Inddmi'] : ''  )."' >
							</td>
						</tr>";
				((isset($row_datos)) ? $options=responsable('', $row_datos['Indrev']) : $options=responsable('') );
				//	-->	 Meta y Tipo de semaforizacion
				echo 	"<tr class='fila1'>
							<td class='encabezadoTabla resalto'>Meta:</td>
							<td class='carBold'>
								<input type='text' style='width:300px' validar_num='si' name='wmeta' id='wmeta'  value='".((isset($row_datos)) ? $row_datos['Indmet'] : ''  )."' >
								<div id='div_wmeta' style='display:none;' ></div>
							</td>
							<td class='encabezadoTabla resalto'>Tipo de semaforización:</td>
							<td class='carBold' align='center'>
								Ascendente <input type='radio' name='wtipo_semaforo' id='wtipo_semaforoA' tooltip='si' title='Mayor el resultado, Mejor la calificación' value='A' ".((isset($row_datos) && $row_datos['Indsem']=='A') ? 'checked' : '')." ".((!isset($row_datos)) ? 'checked': '' ).">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								Descendente <input type='radio' name='wtipo_semaforo' id='wtipo_semaforoD' tooltip='si' title='Mayor el resultado, Peor la calificación' value='D' ".((isset($row_datos) && $row_datos['Indsem']=='D') ? 'checked' : '').">
							</td>
						</tr>";
				//	-->	 Meta
				echo 	"<tr class='fila2'>
							<td class='encabezadoTabla resalto' tipo='indicador'>Responsable:</td>
							<td class='carBold' tipo='indicador'>Buscar:
								<img title='Busque el nombre o parte del nombre del usuario' width='15' height='15' border='0' src='../../images/medical/HCE/lupa.PNG'/>
								<input type='text' style='width:224' name='wbus_repons_eval' id='wbus_repons_eval'  title='Busque el nombre o parte del nombre del usuario' onkeypress='return enterBuscar(\"wresp_eval\", event);' onblur='recargarLista(this,\"wresp_eval\",\"filtrar_resp\");cambioImagen(\"ccload2\",\"\");' onfocus='cambioImagen(\"\",\"ccload2\");'>
								<img id='ccload2' style='display:none;' width='15' height='15' border='0' src='../../images/medical/ajax-loader9.gif' />
								<select style='width:300px' name='wresp_eval' id='wresp_eval' rel='000013' in='Ideesc' >
									$options
								</select>
								<div id='div_wresp_eval' style='display:none;' ></div>
							</td>
						</tr>";
		echo '		</table><br>';
				//	-->	Referentes
		echo'		<br>
					<table width="650" tipo="indicador">
						<tr>
							<td class="parrafo1">
								Referentes(Agregue todos los que desee tener)
							</td>
						</tr>
						<tr><td align="right">
								<div class="fila2" title="Agregar nuevo indicador" style="width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;" align=center onclick="agregar_referente()">
								<b>Agregar</b>
								<img border="0" src="../../images/medical/HCE/mas.PNG">
								</div>
							</td>
						</tr>
					</table>
					<table width="650" name="lista_referentes" id="lista_referentes" tipo="indicador">
						<tr class="encabezadoTabla">
							<td align="center">Nombre</td><td align="center">Valor</td><td align="center" colspan="2">Descripci&oacuten</td>
						</tr>';
					if(isset($row_datos))
					{
						//Consultar los referentes del indicador
						$q_refer="SELECT id, Refnom, Refmet, Refdes
								    FROM ".$wbasedato."_000007
								   WHERE Refind = '".$cod_indicador."'
								     AND Refest = 'on'
								";
						$res_refer = mysql_query($q_refer,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar referentes): ".$q_refer." - ".mysql_error());
						$num_refer = mysql_num_rows($res_refer);
						$k=1;
						$color_ref = "Fila2";
						while($row_refer = mysql_fetch_row($res_refer))
						{
							if($color_ref=="Fila1")
								$color_ref="Fila2";
							else
								$color_ref="Fila1";
							echo '
							<tr name="tr_'.$k.'" id="tr_'.$k.'" class="'.$color_ref.'" aling=center tr_referente="si">
								<td style="display:none">
									<input type="hidden" name="wid_reftr_'.$k.'" id="wid_reftr_'.$k.'" value="'.$row_refer[0].'">
								</td>
								<td style="padding:2px;">
									<input type="text" name="wnom_reftr_'.$k.'" id="wnom_reftr_'.$k.'" value="'.utf8_decode($row_refer[1]).'" size="40"/>
								</td>
								<td style="padding:2px;">
									<input type="text" name="wval_reftr_'.$k.'" id="wval_reftr_'.$k.'" value="'.utf8_decode($row_refer[2]).'" size="5"/>
								</td>
								<td style="padding:2px;">
									<input type="text" name="wdes_reftr_'.$k.'" id="wnom_reftr_'.$k.'" value="'.utf8_decode($row_refer[3]).'" size="45"/>
								</td>
								<td>
									<img width="10" height="10" border="0" style="cursor:pointer;" onclick="removerElemento(\'tr_'.$k.'\');" title="Eliminar Fila" src="../../images/medical/eliminar1.png">
								</td>
							</tr>';
							$k++;
						}
					}
					if (!isset($row_datos) || $num_refer==0)
					{
		echo'			<tr name="sin_referen" id="sin_referen" class="fila1" aling=center>
							<td align=center colspan=4>Sin referentes</td>
						</tr>';
					}
		echo'		</table>
					<br>
				</div>';
				//-->Cierro div Datos medicion
		echo" 	<div><table><tr class='espacio_blanco'><td colspan=3><br></td></tr></table></div>";
				//=========================
				//	Div Guardar
				//=========================
		echo '	<div id="div_guardar" align="center" class="borderDiv ">
					<table>';
				if(isset($row_datos))
					$accion_ficha = 'Actualizar';
				else
					$accion_ficha= 'Guardar';

				echo "	<tr>
							<td align='center'>
							<div id='div_mensaje' class='borderDiv FondoAmarillo' style='display:none;' align='center'></div>
							<br>
							<div name='boton_grabar' id='boton_grabar' class='fila2' style='width:105px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' title='Guardar Indicador' onclick='guardar( \"".$accion_ficha."\", \"".$tipo."\" )'>
							<b>".$accion_ficha."</b>
							<img width='15' height='15' border='0' src='../../images/medical/root/grabar.png'>
							</td>
						</tr>";
		echo '		</table>
				</div><br></div>';
				//-->Cierro div Guardar

	}
//=======================================================================================================================================================
//	FIN FUNCIONES
//=======================================================================================================================================================
	//=========================================
	//				INICIO
	//	Filtros de llamados por Jquery o Ajax
	//=========================================
	if(isset($accion))
	{
		switch($accion)
		{
			//===============================
			//	GRABAR DATOS DE LA FICHA
			//===============================
			case 'Guardar':
			{
				$wcodigo = generar_codigo(($tipo == 'estadistica') ? 'E': 'I');
				$wcodigo = grabar_opciones_menu ($wcodigo, $wnombre, $wtema, $westado);

				if($tipo == 'estadistica')
					$tipo = 'on';
				else
					$tipo = '';

				$q_grabar = " INSERT INTO ".$wbasedato."_000001
									(	Medico, 		Fecha_data, Hora_data, 		Indcod, 		Indnom, 	     Indint 			,Indemp, 	     Indcco,	   Indcle	  		,Indobj		  ,Indper			  ,Indjer			,Indcar					,Indgru			  ,Indpri			  ,Indmim				,Indmag		  ,Indfoc		  				,Indfon				 ,Indmet		,Inddmi			,Inddsu			,Indrme			,Indrev			  ,Indest, 		Indtes, 			Indnde, 			Indsem,					Indare,				Indorg, 			Seguridad, id)
							  VALUES('".$wbasedato."','".$wfecha."','".$whora."','".$wcodigo."','".$wnombre."','".$winterpretacion."','".$wempresa."','".$wunidad."','".$wcod_ley."','".$wobjetivo."','".$wperspectiva."','".$wjerarquia."','".$wcaracteristica."','".$wresponde_a."','".$wperiocidad."','".$wmesinicio."','".$wmagnitud."','".$wformula_guardar."','".$wformula_elementos."','".$wmeta."','".$wdes_min."','".$wdes_max."','".$wresp_med."','".$wresp_eval."', '".$westado."', '".$tipo."', '".$wnum_decimales."', '".$wtipo_semaforo."','".$wactualizar_res."','".$TipoOperResGen."', 	'C-".$wuse."','')
							";
				mysql_query($q_grabar,$conex) or die ("Error: ".mysql_errno()." - en el query(Grabar indicador): ".$q_grabar." - ".mysql_error());

				if($tipo != 'on')
				{
					grabar_referentes ($wlista_referentes, $wcodigo);
					echo "Indicador actualizado|".$wcodigo;
				}
				else
				{
					echo "Estadistica actualizada|".$wcodigo;
				}

				return;
				break;
			}
			case 'Actualizar':
			{
				$q_actual = "UPDATE ".$wbasedato."_000001
								SET Fecha_data 	= 	'".$wfecha."' ,
									Hora_data	= 	'".$whora."',
									Indnom		=	'".$wnombre."',
									Indint		=	'".$winterpretacion."',
									Indemp		=	'".$wempresa."',
									Indcco		=	'".$wunidad."',
									Indcle	  	=	'".$wcod_ley."',
									Indobj		=  	'".$wobjetivo."',
									Indper		=	'".$wperspectiva."',
									Indjer		=	'".$wjerarquia."',
									Indcar		=	'".$wcaracteristica."',
									Indgru		=	'".$wresponde_a."',
									Indpri		=	'".$wperiocidad."',
									Indmim		=	'".$wmesinicio."',
									Indmag		=  	'".$wmagnitud."',
									Indfoc		=  	'".$wformula_guardar."',
									Indfon		=  	'".$wformula_elementos."',
									Indmet		=	'".$wmeta."',
									Inddmi		=	'".$wdes_min."',
									Inddsu		=	'".$wdes_max."',
									Indrme		=	'".$wresp_med."',
									Indrev		=	'".$wresp_eval."',
									Indest		=	'".$westado."',
									Indnde		=	'".$wnum_decimales."',
									Indsem		=	'".$wtipo_semaforo."',
									Indare		=	'".$wactualizar_res."',
									Indorg		=	'".$TipoOperResGen."',
									Seguridad	=	'C-".$wuse."'
							  WHERE	Indcod		= 	'".$wcodigo."'
							";
				mysql_query($q_actual,$conex) or die ("Error: ".mysql_errno()." - en el query(Actualizar indicador): ".$q_actual." - ".mysql_error());

				// --> Actualizar el nombre y el estado del indicador en la opcion de menu
				$qUpdNom = "UPDATE root_000080
							   SET Tabdes = '".utf8_decode($wnombre)."',
								   Tabest = '".$westado."'
							 WHERE Tabcod = '".$wcodigo."'
				";
				mysql_query($qUpdNom,$conex) or die ("Error: ".mysql_errno()." - en el query(Actualizar Opcion de menu): ".$q_actual." - ".mysql_error());

				if($tipo != 'estadistica')
				{
					grabar_referentes ($wlista_referentes, $wcodigo);
					echo "Indicador actualizado|".$wcodigo;
				}
				else
				{
					echo "Estadistica actualizada|".$wcodigo;
				}
				return;
				break;
			}
			//=======================================================
			//	Filtrar select's de cc, responsables, variables
			//=======================================================
			case 'recarga':
			{
				$id_padre = str_replace('|', ' ', $id_padre);

				if($form=='filtrar_cco')	//FILTRAR LOS CENTROS DE COSTOS EN EL SELECT
				{
					$options= consultar_cc($id_padre, $wempresa);
					echo $options;
				}
				if($form=='filtrar_resp') 	//FILTRAR LOS RESPONSABLES EN EL SELECT
				{
					$options=responsable($id_padre);
					echo $options;
				}
				break;
			}
			case 'listar_indicadores':
			{
				listar_indicadores(((isset($codigo_indicador)) ? $codigo_indicador  : '' ));
				return;
				break;
			}
			//------------------------------------------------------------------
			// --> Pintar tabla de variables (Datos, Indicadores, Estadisticas)
			//------------------------------------------------------------------
			case 'pintar_tabla_variables':
			{
				if($busqueda=='')
					$busqueda = '%';

				// --> Consultar las variables dependiendo del tipo
				switch($tipo)
				{
					case 'vdato':
					{
						$q_vari = "SELECT Datnom, Datcod, Datsub
									 FROM ".$wbasedato."_000010
									WHERE Datnom LIKE '%".trim($busqueda)."%'
									  AND Datnum != 'on'
									  AND Datope != 'on'
									  AND Dattte != 'on'
									  AND Datest  = 'on'
									ORDER BY Datnom";
						break;
					}
					case 'vindicador':
					{
						$q_vari = "SELECT Indnom as Datnom, Indcod as Datcod, '' as Datsub
									 FROM ".$wbasedato."_000001
									WHERE Indnom LIKE '%".trim($busqueda)."%'
		 ".(($cod_indicador != '') ? "AND Indcod != '".$cod_indicador."'" : "" )."
									  AND Indest  = 'on'
									  AND Indtes  != 'on'
									ORDER BY Indnom";
						break;
					}
					case 'vestadistica':
					{
						$q_vari = "SELECT Indnom as Datnom, Indcod as Datcod, '' as Datsub
									 FROM ".$wbasedato."_000001
									WHERE Indnom LIKE '%".trim($busqueda)."%'
		 ".(($cod_indicador != '') ? "AND Indcod != '".$cod_indicador."'" : "" )."
									  AND Indest  = 'on'
									  AND Indtes  = 'on'
									ORDER BY Indnom";
						break;
					}
				}
				$res_vari = mysql_query($q_vari,$conex) or die ("Error: ".mysql_errno()." - en el query(consultar vari): ".$q_vari." - ".mysql_error());
				$color='Fila1';

				// --> Pintar la tabla
				echo "
				<table  style='background-color: #FFFFFF;width:100%'>
					<tr class='Encabezadotabla'><td colspan='2'>Nombre</td></tr>
				";
				while($row_vari = mysql_fetch_array($res_vari))
				{
					if($color=='Fila2')
						$color = 'Fila1';
					else
						$color='Fila2';

					// --> Pinto el nombre de la variable
					echo"
					<tr class='".$color."' style='font-family:Verdana, Arial, Helvetica, sans-serif;font-size:9pt;'>
						<td id='".$row_vari['Datcod']."' name='".utf8_decode($row_vari['Datnom'])."' rel='".$row_vari['Datcod']."'>".utf8_decode($row_vari['Datnom'])."</td>";
					if($row_vari['Datsub'] != '')
						echo "<td onClick='$(\"#tr_".$row_vari['Datcod']."\").toggle();' style='cursor:pointer;'><img width='10' height='10' src='../../images/medical/iconos/gifs/i.p.next[1].gif'></td>";
					else
						echo "<td onClick='calculadora(\"".$row_vari['Datcod']."\", \"dato\")' style='cursor:pointer;'><img width='13' height='13' src='../../images/medical/root/grabar.png'></td>";

					echo "</tr>";
					// --> Indica que la variable tiene configurada una agrupacion, lo que implica que se debe listar cada uno de sus detalles.
					if($row_vari['Datsub'] != '' && $row_vari['Datsub'] != ' ')
					{
						//Consulto el query del parametro a subdividir.
						$q_parametro = ' SELECT Parcrv, Parcrm, Partab, Parcon, Pardsn
										   FROM '.$wbasedato.'_000012
										  WHERE Parcod = "'.$row_vari['Datsub'].'"
										';
						$res_parametro = mysql_query($q_parametro,$conex) or die ("Error: ".mysql_errno()." - en el query(Consultar parametro): ".$q_parametro." - ".mysql_error());
						$row_parametro = mysql_fetch_array($res_parametro);

						//Armo la estructura del query
						$query_param = 'SELECT '.$row_parametro['Parcrv'].', '.$row_parametro['Parcrm'];
						$query_param.= '  FROM '.$row_parametro['Partab'];
						$query_param.= ' WHERE '.$row_parametro['Parcon'];

						//Ejecuto el query y por cada uno de sus resultados pinto una opcion de detalle.
						$resultado_parametro = array();
						ejecutar_query($query_param, $row_parametro['Pardsn'], $resultado_parametro, 'parametro');

						if(count($resultado_parametro)>0)
						{
							$style_det = 'font-family:Verdana, Arial, Helvetica, sans-serif;font-size:7pt;';
							$color2    = 'Fila1';

							echo "
							<tr id='tr_".$row_vari['Datcod']."' style='display:none;'>
								<td colspan='2' style='padding: 5px;'>
									<table style='border: 1px solid #999999;'>
									<tr><td style='".$style_det."' class='Fondoamarillo' colspan='2' align='center'><b>DETALLE</b></td></tr>
									<tr class='".$color2."' style='".$style_det."'><td id='".$row_vari['Datcod']."_Todos' name='".utf8_decode($row_vari['Datnom'])."(Todos)' rel='".$row_vari['Datcod']."[*]' ActivarOperacionGeneral='si'>".utf8_decode($row_vari['Datnom'])."(Todos)</td>
										<td onClick='calculadora(\"".$row_vari['Datcod']."_Todos\", \"dato\")' style='cursor:pointer;'><img width='13' height='13' src='../../images/medical/root/grabar.png'></td>
									</tr>";
									foreach ($resultado_parametro as $campo_valor => $campo_nombre )
									{
										if($color2=='Fila2')
											$color2 = 'Fila1';
										else
											$color2='Fila2';
										echo "
										<tr class='".$color2."' style='".$style_det."'>
											<td id='".$row_vari['Datcod']."_".str_replace(' ', '', trim($campo_valor))."' name='".utf8_decode($row_vari['Datnom'])."(".trim(utf8_decode($campo_nombre)).")' rel='".$row_vari['Datcod']."[".trim($campo_valor)."]' >".utf8_decode($row_vari['Datnom'])."(".trim(utf8_decode($campo_nombre)).")</td>
											<td onClick='calculadora(\"".$row_vari['Datcod']."_".str_replace(' ', '', trim($campo_valor))."\", \"dato\")' style='cursor:pointer;'><img width='13' height='13' src='../../images/medical/root/grabar.png'></td>
										</tr>";
									}
									echo"
									</table>
								</td>
							</tr>";
						}
					}
				}
				echo"
				</table>
				";

				break;
			}

			//Desplegar una ficha, sea una nueva o una ya existente
			case 'ver_ficha':
			{
				pintar_ficha($codigo_indicador, $tipo);
				return;
				break;
			}
			case 'filtrar_lista':
			{
				if($busc_codigo != '')
					$cod_indicador = $busc_codigo;
				pintar_lista($busc_nombre, $busc_cco, $busc_resp, $busc_tipo, $busc_estado, $cod_indicador);
				break;
				return;
			}
			case 'EliminarIndicador':
			{
				if($codigoIndEst != '')
				{
					$q_borrar = " DELETE FROM ".$wbasedato."_000001
								   WHERE Indcod = '".$codigoIndEst."'
								";
					$res_borrar = mysql_query($q_borrar, $conex) or die ("Error: ".mysql_errno()." - en el query(Eliminar indicador): ".$q_borrar." - ".mysql_error());
					if($res_borrar > 0)
					{
						if($codigoIndEst{0} == 'I')
							echo "Indicador Eliminado";
						else
							echo "Estadistica Eliminada";
					}
				}
				break;
				return;
			}
			case 'validar_integridad_actualizacion_resultados':
			{
				$arr_lista_modificables = obtener_lista_modificable();

				// --> revisar si alguno de los indicadores y estadisticas de la formula son modifcables
				$arr_componentes_formu = explode('|', $wformula_guardar);
				$arr_nombre_comp_formu = explode('|', $wformula_elementos);

				foreach($arr_componentes_formu as $indice => $valor_formula)
				{
					$valor_formula = trim($valor_formula);
					if(strstr($valor_formula, '['))
					{
						$valor_formula = substr($valor_formula, 0, stripos($valor_formula, '['));
					}

					if($valor_formula != '' && ($valor_formula{0} == 'E' || $valor_formula{0} == 'I') && in_array($valor_formula,$arr_lista_modificables))
					{
						$mensaje.= '- '.$arr_nombre_comp_formu[$indice].'.<br>';
					}
				}
				if($mensaje != '')
					echo "<b>Este indicador debe modificar resultados, ya que las <br>
						  siguientes variables de su formula son modificables:</b><br>".$mensaje;
				else
					echo 'ok';

				break;
				return;
			}

		} 	//fin switch
	}
	//=========================================
	//	FIN, Filtros de llamados por Jquery.
	//	Ejecucion normal de programa.
	//=========================================
else
{
$tema = consultar_tema();
echo "<input type='hidden' id='wtema' name='wtema' value='".$tema."'>";
echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
echo "<input type='hidden' id='wuse' name='wuse' value='".$wuse."'>";

?>
<html>
<head>
  <title>FICHA INDICADOR</title>
</head>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.editable.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>


<script type="text/javascript">
	function cerrarVentana()
	{
        window.close();
    }
	// --> Pintar tabla de variables
	function tabla_varibles(cod_indicador)
	{
		var tipo;
		tipo = $('input[name=tipo_var]:checked').val();

		$.post("Inventario_indicadores.php",
		{
			consultaAjax:   		'',
			wemp_pmla:      		$('#wemp_pmla').val(),
			wuse:           		$('#wuse').val(),
			wbasedato:				$('#wbasedato').val(),
			accion:         		'pintar_tabla_variables',
			tipo:					tipo,
			busqueda:				$('#wbus_variable').val(),
			cod_indicador:			(cod_indicador != 'nuevo') ? cod_indicador : ''
		}
		,function(resultado) {
			$('#lista_variables').html(resultado);
		});
	}

	function validar(elemento)
	{
		var valor=$("#"+elemento.id).val();
		if(valor=='')															//si el campo esta vacio
		{
			$("#"+elemento.id).css("border","1px dotted red");					//le pinto el borde de rojo
			$("#div_"+elemento.id)												//Muestro mensaje
					.text(' * Campo Obligatorio')
					.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
					.show();
		}
		else
		{														//sí es diferente de vacio,
			$("#"+elemento.id).css("border","");				//quito el borde rojo
			$("#div_"+elemento.id).css("display", "none");		//oculto el mensaje
		}
	}

	function enterBuscar(hijo, e)
	{
		tecla = (document.all) ? e.keyCode : e.which;
		if(tecla==13) { $("#"+hijo).focus(); }
		else { return true; }
		return false;
	}

    function verSeccionCaracterizacion(id){
        $("#"+id).toggle("normal");
    }

	function pintar_filtros(id){
        $("#"+id).toggle("normal");
		ajustar_tamaño(500);
    }
	function cambioImagen(img1, img2)
    {
		$('#'+img1).hide(1000);
        $('#'+img2).show(1000);
    }
	function mostrar_ficha(codigo_indicador, momento, tipo)
	{
		$.post("Inventario_indicadores.php",
		{
			consultaAjax:   		'',
			wemp_pmla:      		$('#wemp_pmla').val(),
			wuse:           		$('#wuse').val(),
			wbasedato:				$('#wbasedato').val(),
			accion:         		'ver_ficha',
			tipo:					tipo,
			codigo_indicador:		codigo_indicador
		}
		,function(data) {
			if( momento == 'despues_de_grabar')
			{
				$("#Pintar_ficha").html(data);
			}
			else
			{
				$("#Pintar_ficha").hide();			//Ocultar Div
				$("#Pintar_ficha").html(data);
				$("#Pintar_ficha").slideDown(800); //Animacion para que despliegue
			}
			// Si es de tipo estadistica oculto los campos que sean tipo='indicador'
			if(tipo == 'estadistica')
				$('[tipo=indicador]').hide();

			//Dejar en la lista inicial, solo el seleccionado
			if(codigo_indicador != 'nuevo')
				filtrar_lista(codigo_indicador);
			else
				ajustar_tamaño(200);

			$("[validar_num=si]").keyup(function(){
				if ($(this).val() !="")
					$(this).val($(this).val().replace(/[^0-9\.]/g, ""));
			});

			// --> cargar variables en la calculadora
			tabla_varibles(codigo_indicador);

			// --> Tooltip
			$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			// --> Tooltip
			$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		}
	);

	}
	function calculadora(valor, dato)
	{
		var valor_elementos = '';
		var valor_pantalla = '';
		var arr_elementos = '';

		if (valor == 'Limpiar_pantala')
		{
			$("#wformula_elementos").val('');
			$("#wformula").val('');
			$("#wformula_guardar").val('');
			$("#listaElementosDet").val('');
			$("#TableOperacionGeneral").hide();
		}
		else
		{
			if (valor == 'borrar')
			{
				valor_formula_elementos = $("#wformula_elementos").val();
				valor_formula_a_guardar = $("#wformula_guardar").val();
				listaElementosDet		= $("#listaElementosDet").val();

				arr_formula_elementos = valor_formula_elementos.split('|');
				arr_formula_a_guardar = valor_formula_a_guardar.split('|');
				arr_listaElementosDet = listaElementosDet.split('|');

				// --> elimino la ultima posicion de ambos arrays
				arr_formula_elementos.pop();
				arr_formula_a_guardar.pop();
				arr_listaElementosDet.pop();

				valor_formula_elementos = '';
				valor_formula_a_guardar	= '';
				valor_formula_pantalla	= '';
				New_listaElementosDet	= '';

				for( i=0; i < arr_formula_elementos.length; i++ )
				{
					if(i==0)
					{
						valor_formula_elementos = arr_formula_elementos[i];
						valor_formula_a_guardar = arr_formula_a_guardar[i];
						valor_formula_pantalla	= arr_formula_elementos[i];
						New_listaElementosDet	= arr_listaElementosDet[i];
					}
					else
					{
						valor_formula_elementos+= '|'+arr_formula_elementos[i];
						valor_formula_a_guardar+= '|'+arr_formula_a_guardar[i];
						New_listaElementosDet+= '|'+arr_listaElementosDet[i];
						valor_formula_pantalla+= arr_formula_elementos[i];
					}
				}
				$("#wformula_elementos").val(valor_formula_elementos);
				$("#wformula_guardar").val(valor_formula_a_guardar);
				$("#wformula").val(valor_formula_pantalla);
				$("#listaElementosDet").val(New_listaElementosDet);

				Arr_listaElementosManejaDet 	= New_listaElementosDet.split('|');
				var pintarCuadroTipoOperación 	= 'no';
				$.each(Arr_listaElementosManejaDet, function (ind, elem){
					if(elem == 'si')
						pintarCuadroTipoOperación = 'si';
				});
				if(pintarCuadroTipoOperación == 'si')
					$("#TableOperacionGeneral").show();
				else
					$("#TableOperacionGeneral").hide();
			}
			else	//agregar un nuevo elemento a la pantalla
			{
				var valor_elemento ='';
				var valor_codigo_bd='';
				valor_elemento = $("#"+valor).val();

				if (dato == 'dato') //Es una variable(Indicador, dato, estadistica)
				{
					valor_codigo_bd = $("#"+valor).attr('rel'); 					//valor del codigo que tiene el elemento en la base de datos
					valor_elemento  = $("#"+valor).attr('name');
				}
				else
					valor_codigo_bd = $("#"+valor).attr('rel');						//valor del codigo que tiene el elemento en la base de datos (sgc_10)


				// --> Esto es para mostrar o no, el cuadro de seleccion del tipo de operacion general. Actualizacion 2013-10-17.
				var manejaDetalle = '';
				if ($("#"+valor).attr('ActivarOperacionGeneral') != undefined)
					manejaDetalle = 'si';
				else
					manejaDetalle = 'no';

				var listaElementosManejaDet = $("#listaElementosDet").val();		//cadena que contiene si cada elemento maneja o no detalle
				listaElementosManejaDet+= ((listaElementosManejaDet != '') ? "|"+manejaDetalle : manejaDetalle);

				$("#listaElementosDet").val(listaElementosManejaDet);

				Arr_listaElementosManejaDet 	= listaElementosManejaDet.split('|');
				var pintarCuadroTipoOperación 	= 'no';
				$.each(Arr_listaElementosManejaDet, function (ind, elem){
					if(elem == 'si')
						pintarCuadroTipoOperación = 'si';
				});
				if(pintarCuadroTipoOperación == 'si')
				{
					$("#TableOperacionGeneral").show();
				}
				else
					$("#TableOperacionGeneral").hide();
				// <-- Fin mostrar


				valor_formula_elementos 	= $("#wformula_elementos").val(); 			//cadena con los elementos de la formula separados por |
				valor_formula_pantalla 		= $("#wformula").val();						//cadena con la formula que hay pintada
				valor_formula_a_guardar 	= $("#wformula_guardar").val();				//cadena con los codigos separados por '|', de cada uno de los elementos que contiene la formula

				if (valor_formula_elementos != '')
				{
					valor_formula_elementos+= '|'+valor_elemento;
					valor_formula_a_guardar+= '|'+valor_codigo_bd;
					valor_formula_pantalla+=valor_elemento;
				}
				else
				{
					valor_formula_elementos = valor_elemento;
					valor_formula_a_guardar = valor_codigo_bd;
					valor_formula_pantalla  = valor_elemento;
				}
				$("#wformula_elementos").val(valor_formula_elementos);
				$("#wformula_guardar").val(valor_formula_a_guardar);
				$("#wformula").val(valor_formula_pantalla);
			}
		}
	}
	function recargarLista(id_padre, id_hijo, form)
    {
		val = $("#"+id_padre.id).val();
		//Escapar espacios en blanco
		val = val.replace(" ","|");
		val = val.replace(" ","|");
		val = val.replace(" ","|");
        $('#'+id_hijo).load("Inventario_indicadores.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion=recarga&id_padre="+val+"&form="+form);
    }
	function recargarUnidad(wempresa, id_padre, id_hijo, form)
    {
		val_empresa = $("#"+wempresa).val();
		if(id_padre != '')
			val_buscar  = $("#"+id_padre.id).val();
        else
			val_buscar  = '';

		$('#'+id_hijo).load("Inventario_indicadores.php?consultaAjax=&wempresa="+val_empresa+"&accion=recarga&id_padre="+val_buscar+"&form="+form);
    }
	function validar_guardar(elemento, guardar)
	{
		if 	(document.getElementById(elemento).value =='')
		{
			$("#"+elemento).css("border","1px dotted red");
			$("#div_"+elemento)
				.text(' * Campo Obligatorio')
				.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
				.show();
			return guardar = 'no';
		}
		else
		{
			$("#"+elemento).css("border","");				//quito el borde rojo
			$("#div_"+elemento).css("display", "none");
		}
		return guardar;
	}

	function validar_guardar_checkbox (lista_checkbox, nombre_div, guardar)
	{
		if 	(lista_checkbox =='')
		{
			$("#div_"+nombre_div)
				.text(' * Campo Obligatorio')
				.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
				.show();
			return guardar = 'no';
		}
		else
		{
			$("#div_"+nombre_div).css("display", "none");
		}
		return guardar;
	}

	//Funcion para armar los elementos tipo check o tipo radio en un string separado por coma (,)	.
	function lista_de_checks(elemento_check)
	{
		var lista = '';
		checkboxes = document.getElementById('checkbox_'+elemento_check).getElementsByTagName( 'input' ); //Array que contiene los checkbox
		for (var x=0; x < checkboxes.length; x++)
		{
			if ((checkboxes[x].type == "checkbox" || checkboxes[x].type == "radio") && checkboxes[x].checked)
			{
				if(lista=='')
					lista = checkboxes[x].value;
				else
					lista = lista+","+checkboxes[x].value;
			}
		}
		return lista;
	}

	function armar_lista_referentes()
	{
		var todos_iputs = '';
		var cambio_tr	= '';
		var tr_anterior = '0';
		$('#lista_referentes').find('input').each(
			function(index)
			{
				nom_input = $(this).attr("name");
				nuevo_tr = nom_input.split('_');
				if (nuevo_tr[2] != tr_anterior) //Si cambio de tr significa que es un nuevo referente
				{
					cambio_tr	=	'|#|';
					tr_anterior = 	nuevo_tr[2];
				}
				else
				{
					cambio_tr	=	'|';
				}

				if(todos_iputs == '')
					todos_iputs = $(this).attr("value");
				else
				{
					todos_iputs+= cambio_tr+$(this).attr("value");
				}
			}
		);
		return todos_iputs;
	}
	//--------------------------------------------------------
	// Funcion que valida y guarda la informacion de la ficha
	//--------------------------------------------------------
	function guardar(accion_ficha, tipo)
    {
		var guardar='si';
		var debe_modificar_resultados = 'ok';

		// --> Obtener el estado
		var estado = $("#westado").attr('checked');
		if (estado =='checked')
			estado = 'on';
		else
			estado = 'off';

		// --> Validar los campos obligatorios
		var lista_caracteristica = lista_de_checks('wcaracteristica');
		var lista_responde_a = lista_de_checks('wresponde_a');
		guardar = validar_guardar_checkbox (lista_caracteristica, 'wcaracteristica', guardar);
		guardar = validar_guardar_checkbox (lista_responde_a, 'wresponde_a', guardar);
		if(tipo == 'indicador')
		{
			var lista_perspectivas = lista_de_checks('wperspectiva');
			var lista_jerarquia = lista_de_checks('wjerarquia');
			var lista_referentes = armar_lista_referentes();
			guardar = validar_guardar_checkbox (lista_perspectivas, 'wperspectiva', guardar);
			guardar = validar_guardar_checkbox (lista_jerarquia, 'wjerarquia', guardar);
			guardar = validar_guardar('winterpretacion', guardar);
			guardar = validar_guardar('wobjetivo', guardar);
		}

		guardar = validar_guardar('wnombre', guardar);
		guardar = validar_guardar('wempresa', guardar);
		guardar = validar_guardar('wunidad', guardar);
		guardar = validar_guardar('wresp_med', guardar);
		guardar = validar_guardar('wmagnitud', guardar);
		guardar = validar_guardar('wformula', guardar);
		guardar = validar_guardar('wperiocidad', guardar);
		guardar = validar_guardar('wmesinicio', guardar);
		// <-- Fin validar

		// --> Validar que si algun componente de la formula tiene la propiedad de 'Modifica resultados'
		// 	   entonces el indicador o estadistica tambien lo debe ser.
		var wactualizar_res = ($('#wactualizar_res_si').attr('checked') == 'checked') ? 'on' : 'off';
		$.post("Inventario_indicadores.php",
		{
			consultaAjax:   		'',
			wemp_pmla:      		$('#wemp_pmla').val(),
			wuse:           		$('#wuse').val(),
			wbasedato:				$('#wbasedato').val(),
			accion:         		'validar_integridad_actualizacion_resultados',
			wformula_guardar:		$('#wformula_guardar').val(),
			wformula_elementos:		$('#wformula_elementos').val()
		}
		,function(data) {

			if (guardar=='no')
			{
				mostrar_mensaje('<b>Datos incompletos</b>');
			}
			// --> Envio de datos a php para proceder a guardar
			else
			{
				if( data != 'ok' && wactualizar_res == 'off')
				{
					mostrar_mensaje(data);
					$("#div_wactualizar_res")
					.text('¡ Sí debe modificar resultados !')
					.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
					.show();
				}
				else
				{
					// --> Tipo de operacion para el caculo general
					OperResGen = '';
					if($("#TableOperacionGeneral").is(":hidden"))
						OperResGen = '';
					else
					{
						$("[name=tipo_res_gen]").each(function (){
							if($(this).attr('checked'))
								OperResGen = $(this).val();
						});
					}

					// --> Envio de variables
					$.post("Inventario_indicadores.php",
					{
						consultaAjax:   		'',
						wemp_pmla:      		$('#wemp_pmla').val(),
						wuse:           		$('#wuse').val(),
						wbasedato:				$('#wbasedato').val(),
						accion:         		accion_ficha,
						wnombre:    			$('#wnombre').val(),
						wcodigo:				$('#wcodigo').html(),
						wobjetivo:				$('#wobjetivo').val(),
						winterpretacion:		$('#winterpretacion').val(),
						wperspectiva:			lista_perspectivas,
						wjerarquia:				lista_jerarquia,
						wcaracteristica:		lista_caracteristica,
						wresponde_a:			lista_responde_a,
						wempresa:				$('#wempresa').val(),
						wunidad:				$('#wunidad').val(),
						wcod_ley:				$('#wcod_ley').val(),
						wresp_med:				$('#wresp_med').val(),
						wmagnitud:				$('#wmagnitud').val(),
						wactualizar_res:		wactualizar_res,
						wformula_guardar:		$('#wformula_guardar').val(),
						wformula_elementos:		$('#wformula_elementos').val(),
						wdes_max:				$('#wdes_max').val(),
						wdes_min:				$('#wdes_min').val(),
						wmeta:					$('#wmeta').val(),
						wperiocidad:			$('#wperiocidad').val(),
						wmesinicio:				$('#wmesinicio').val(),
						wresp_eval:				$('#wresp_eval').val(),
						wlista_referentes:		lista_referentes,
						westado:				estado,
						tipo:					tipo,
						wnum_decimales:			$('#wnum_decimales').val(),
						wtipo_semaforo:			($('#wtipo_semaforoA').attr('checked') == 'checked') ? 'A' : 'D',
						wtema:					$('#wtema').val(),
						TipoOperResGen:			OperResGen
					}
					,function(data) {
						respuesta = data.split('|');
						alert(respuesta[0]);
						wcodigo = respuesta[1];
						$("#div_mensaje").css("display", "none");
						var momento = 'despues_de_grabar';
						mostrar_ficha(wcodigo, momento, tipo); //Actualizar el html de la ficha
					}
					);
				}
			}

		}
		);
    }
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensaje").html("<BLINK><img width='15' height='15' src='../../images/medical/root/info.png' /></BLINK>&nbsp;"+mensaje);
		$("#div_mensaje").css({"opacity":" 0.6","fontSize":"12px"});
		$("#div_mensaje").hide();
		$("#div_mensaje").show(300);
	}
	function removerElemento(tr_referente)
	{
		//$('#'+tr_referente).slideUp(1000);
		$('#'+tr_referente).remove();
	}
	function agregar_referente()
	{
		$('#sin_referen').remove(); //elimino el tr que pinta el mensaje "Sin referentes"
		var ultimo_nombre = "tr_0";
		var ultimo_color  = "Fila1";
		var insertar_new  = "no";
		primer_ref = "si";
		$("[tr_referente=si]").each(
			function(index)
			{
				ultimo_nombre = $(this).attr("id");
				ultimo_color = $(this).attr("class");
				primer_ref = "no";
				insertar_new  = "no";
				//verificar si en el ultimo referente insertado, sus input tienen valores
				//para validar que si hay un referente sin valores, no insertar otro
				$('#'+ultimo_nombre).find('input').each(
					function(index)
					{
						hay_valor = $(this).attr("value");
						if (hay_valor != "" && hay_valor != "nuevo")
						{
							insertar_new = "si";
						}
					}
				);
			}
		);
		if(insertar_new == "si" || primer_ref == "si")
		{
			//Calcular cual es el nuevo nombre
			var new_nombre_arr = ultimo_nombre.split('_');
			consecutivo = new_nombre_arr[1];
			consecutivo++;
			new_nombre = 'tr_'+consecutivo;
			//var new_eliminar = 'eliminar_'+new_nombre;
			var new_wnom_ref= 'wnom_ref'+new_nombre;	//Nombre para el input del nombre
			var new_wdes_ref= 'wdes_ref'+new_nombre;	//Nombre para el input del referente
			var new_wval_ref= 'wval_ref'+new_nombre;	//Nombre para el input del referente
			//Calcular el nuevo color
			var new_color;
			if(ultimo_color == "Fila1")
			{
				new_color = "Fila2";
			}
			else
			{
				new_color = "Fila1";
			}

			//Inserto el nuevo referente
			$('#lista_referentes').append(
			'<tr name="'+new_nombre+'" id="'+new_nombre+'" class="'+new_color+'" align=center tr_referente="si">'
				+'<td style="display:none">'
					+'<input type="hidden" name="wid_reftr_'+consecutivo+'" id="wid_reftr_'+consecutivo+'" value="nuevo" >'
				+'</td>'
				+'<td style="padding:2px;">'
					+'<input type="text" name="'+new_wnom_ref+'" id="'+new_wnom_ref+'" size="40"/>'
				+'</td>'
				+'<td style="padding:2px;">'
					+'<input type="text" name="'+new_wval_ref+'" id="'+new_wval_ref+'" size="5"/>'
				+'</td>'
				+'<td style="padding:2px;">'
					+'<input type="text" name="'+new_wdes_ref+'" id="'+new_wdes_ref+'" size="45"/>'
				+'</td>'
				+'<td>'
					+'<img width="10" height="10" border="0" style="cursor:pointer;" onclick="removerElemento(\''+new_nombre+'\');" title="Eliminar Fila" src="../../images/medical/eliminar1.png">'
				+'</td>'
			+'</tr>'
			);

		}
	}
	//-------------------------------------------------
	// Filtra la lista de principal de los parametros
	//-------------------------------------------------
	function filtrar_lista(cod_indicador)
	{
		var busc_codigo  	= $('#busc_codigo').val();
		var busc_nombre  	= $('#busc_nombre').val();
		var busc_cco 		= $('#busc_cco').val();
		var busc_resp 		= $('#busc_resp').val();
		var busc_tipo  		= $('#busc_tipo').val();
		var busc_estado  	= $('#busc_estado').val();

		$.post("Inventario_indicadores.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'filtrar_lista',
				busc_codigo: 	busc_codigo,
				busc_nombre: 	busc_nombre,
				busc_cco:		busc_cco,
				busc_resp:		busc_resp,
				busc_tipo:		busc_tipo,
				busc_estado:	busc_estado,
				cod_indicador:	cod_indicador
			}
			,function(data) {
				$('#div_lista').hide();
				$('#div_lista').html(data);
				$('#div_lista').show(0, function()
					{
						if(cod_indicador != '')
						{
							ajustar_tamaño(90);
						}
						else
						{
							var altura_div = $("#table_lista").height();
							if(altura_div > 500)
							{
								$('#div_lista').css(
									{
										'height': 500,
										'overflow': 'auto',
										'background': 'none repeat scroll 0 0'
									}
								);
							}
							else
							{
								$('#div_lista').css(
									{
										'height': altura_div+50
									}
								);
							}
						}
					}
				);
				$('[tooltip2=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			}
		);
	}

	function selecc_tipo_ficha()
	{
		var html = "<br><br><div align='center'><table width='300px' class='borderDiv2'>"+
					"<tr><td colspan='5' align='center'style='color: #000000;font-size: 12pt;'><b>¿ TIPO ?</b><br></td></tr>"+
					"<tr>"+
						"<td width='3%'></td>"+
						"<td width='16%' align='center' style='border: 1px solid #2A5DB0;background-color: #D2E8FF;color: #000000;font-size: 10pt;cursor:pointer;' onClick='mostrar_ficha(\"nuevo\", \"null\", \"indicador\")'><b>Indicador</b></td>"+
						"<td width='3%'></td>"+
						"<td width='16%' align='center' style='border: 1px solid #2A5DB0;background-color: #D2E8FF;color: #000000;font-size: 10pt;cursor:pointer;' onClick='mostrar_ficha(\"nuevo\", \"null\", \"estadistica\")'><b>Estadistica</b></td>"+
						"<td width='3%'></td>"+
						"<tr><td colspan='5'></td></tr>"+
					"</tr>"+
					"</table></div>";
		$("#Pintar_ficha").hide();
		$("#Pintar_ficha").html(html);
		$("#Pintar_ficha").show();
	}
	//-----------------------------------------------------------------------------
	// Funcion que me elimina un indicador o estadistica
	//-----------------------------------------------------------------------------
	function eliminar_indicador(Codigo)
	{
		var mensaje = "";
		if ( Codigo.substring(0,1) == 'I')
			mensaje = "¿Está seguro que desea eliminar este indicador?";
		else
			mensaje = "¿Está seguro que desea eliminar esta estadística?";
		if(confirm(mensaje))
		{
			$.post("Inventario_indicadores.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'EliminarIndicador',
				codigoIndEst: 	Codigo
			}
			,function(data) {
				alert(data);
				filtrar_lista('');
			});
		}
	}
	//-----------------------------------------------------------------------------
	//Funcion que me ajusta el tamaño el div donde si listan los datos
	//-----------------------------------------------------------------------------
	function ajustar_tamaño(altura)
	{
		var altura_div = $("#div_lista").height();
		//alert(altura_div);
		if(altura_div > altura)
		{
			$('#div_lista').css(
				{
					'height': altura,
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
		else
		{
			$('#div_lista').css(
				{
					'height': altura_div
				}
			);
		}
	}

	$(document).ready(function()
		{
			ajustar_tamaño(500);
			$('[tooltip2=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		}
	);
</script>

<style type="text/css">
	#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
	#tooltip div{margin:0; width:auto;}

    .displCaracterizacion{
        display:none;
    }
    .borderDiv {
        border: 1px solid #2A5DB0;
        padding: 5px;
    }
	.mes_tooltip{
			background: #2A5DB0; border: 1px solid #D3D3D3;color: #FFFFFF;font-weight: normal;outline: medium none;margin: 1px; padding: 1px; height: 18px; font-size: 15px; text-align: center;
		}
	.borderDiv2 {
        border: 2px solid #2A5DB0;
        padding: 15px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
		background-color: #666666;
		color: #FFFFFF;
		font-family: verdana;
		font-size: 10pt;
		font-weight: bold;
		text-align: left;
    }
    .backgrd_seccion{
        background-color: #E4E4E4;
    }
    .carBold{
        font-weight:bold;
    }
	.espacio_blanco{
        background-color: #FFFFFF;
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
		text-align: center;
    }
	.botoncalculadora {
        cursor:pointer;
        border: 1px solid #999999;
        background-color:#D2E8FF;
		width:40px;
		height:30px;
     }
</style>
<body>
<?php

//=========================================================================================================================================\\
//                  REGISTRO FICHA INDICADOR
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:
//FECHA DE CREACION:
//=========================================================================================================================================\\
//                  ACTUALIZACIONES                                                                                                                          \\
  $wactualiz='20-Nov-2019';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//	2013-10-17: Se crea una nueva funcionalidad para los indicadores que manejen resultado detallado, se pueda calcular
//				su resultado general bajo tres modalidades una sumatoria, un promedio o un calculo de un query general(Query sin group by).
//				La ventana de seleccion para el tipo de operación para el resultado general, solo se activara si alguna
//				variable de la formula maneja la opcion de (Todos), que esto en el cuadro de mando es un resultado detallado.
//				Jerson trujillo.
//	2014-03-17: EL nombre de las variables de la formula se consultan segun como esten en el momento
//				para asi traerlos actualizados, Jerson trujillo.
//	2019-20-11: Se quita filtro de wemp_pmla para traer la lista de responsables(usuarios)
//=========================================================================================================================================\\

//================================================================
//    ENCABEZADO
//================================================================
encabezado("Inventario Indicadores y Estadísticas SGC", $wactualiz, 'clinica');
echo "	<div align='center'>
			<table width='81%' border='0' cellpadding='3' cellspacing='3'>
				<tr>
					<td align='left'>
						<div align='left' class='Titulo_azul'>
							INVENTARIO DE INDICADORES Y ESTADÍSTICAS
						</div>
						<div align='center' id='div_contenedor' class='borderDiv2'>
";
								listar_indicadores('');
echo "					</div>
						<div id='Pintar_ficha'>
						</div>
					</td>
				</tr>
			</table><br>
			<div align=center>
				<input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>
			</div><br>
		</div>
";
?>
</BODY>
</HTML>
<?php
}
}
?>
