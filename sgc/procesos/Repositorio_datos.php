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
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'sgc');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");

//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S
//=====================================================================================================================================================================

	//--------------------------------------------
	//	Funcion que consulta las empresas
	//--------------------------------------------
	function traer_empresas()
	{
		global $wbasedato;
		global $conex;
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
	//--------------------------------------------
	//	Funcion que consulta los DNS
	//--------------------------------------------
	function traer_dsn()
	{
		global $wbasedato;
		global $conex;
		$q = " SELECT Dsncod, Dsnnom "
		  ."     FROM ".$wbasedato."_000011 "
		  ."    WHERE Dsnest = 'on' "
		  ."    Order by Dsnunx, Dsnnom";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query(seleccionar dns): ".$q." - ".mysql_error());

		while($result = mysql_fetch_array($res))
		{
			$cod=$result[0];
			$nom=$result[1];
			$array_res[$cod]=$nom;
		}
		return $array_res;
	}
	//--------------------------------------------
	//	Funcion que genera un nuevo codigo
	//--------------------------------------------
	function generar_codigo($tipo_temporal = 'off')
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wfecha;
		global $whora;

		//Bloquear la tabla contra escritura
		$q = "lock table " . $wbasedato . "_000010 LOW_PRIORITY WRITE";
		$err = mysql_query($q, $conex);

		//Consultar el ultimo codigo generado para datos
		$q_codigo =" SELECT max(SUBSTRING( Datcod FROM 2)*1) as Datcod
					   FROM ". $wbasedato ."_000010
					";
		$res_codigo   = mysql_query($q_codigo, $conex) or die (mysql_errno(). " - en el query (Consultar todos codigo): ".$q_codigo . " - " . mysql_error());
		$row_codigo_d = mysql_fetch_array($res_codigo);

		//Consultar el ultimo codigo generado para datos temporales
		$q_codigo =" SELECT max(SUBSTRING( Datcod FROM 3)*1) as Datcod
					   FROM ". $wbasedato ."_000010
					";
		$res_codigo   = mysql_query($q_codigo, $conex) or die (mysql_errno(). " - en el query (Consultar todos codigo): ".$q_codigo . " - " . mysql_error());
		$row_codigo_t = mysql_fetch_array($res_codigo);

		//Genero el codigo para el dato
		if($row_codigo_d['Datcod'] > $row_codigo_t['Datcod'])
			$nuevo_cod = $row_codigo_d['Datcod']+1;
		else
			$nuevo_cod = $row_codigo_t['Datcod']+1;

		if($tipo_temporal == 'on')
			$wcodigo= 'DT'.$nuevo_cod;
		else
			$wcodigo= 'D'.$nuevo_cod;
		//Desbloqueo la tabla y envio el codigo generado
		$q = " UNLOCK TABLES";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
		return $wcodigo;
	}
	//------------------------------------------------------------
	//	Funcion que pinta la funcionalidad para armar el query
	//------------------------------------------------------------
 	function pintar_realizar_query($valor='')
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wfecha;
		global $whora;

		if($valor != '')
		{
			$valores 	= explode ('|', $valor);
			$select 	= $valores[0];
			$from 		= $valores[1];
			$where		= $valores[2];
		}
		else
		{
			$select 	= '';
			$from 		= '';
			$where		= '';
		}
		//tag_tablas
		echo"<br>
		<table width='600px' style='color: #000000;font-size: 10pt;'>
			<tr>
				<td colspan=2 class='parrafo_text' align='center'>
					Creaci&oacute;n de query
				</td>
			</tr>
			<tr>
				<td align='right'>
					<b>SELECT</b>
				</td>
				<td>
					<input id='campo_select' style='width:420px;' onkeyup='desplegar_busqueda(this, \"select\", event);' value='".$select."' onmouseover='validar(this)' onblur='validar(this), $(\"#caja_flotante\").hide(500);' />
					<div align='right' id='div_tag_select' style='display:none;' ></div>
				</td>
			</tr>
			<tr>
				<td align='right'>
					<b>FROM</b>
				</td>
				<td>
					<input id='campo_from' style='width:420px;' value='".$from."' onkeyup='desplegar_busqueda(this, \"from\", event);' onmouseover='validar(this)' onblur='validar(this); $(\"#caja_flotante\").hide(500);' />
					<div align='right' id='div_tag_tablas' style='display:none;' ></div>
				</td>
			</tr>
			<tr>
				<td align='right' valign='top'>
					<b>WHERE</b>
				</td>
				<td>
					<textarea id='campo_where' rows='6' style='width:420px;' onkeyup='desplegar_busqueda(this, \"where\", event);'  onblur='$(\"#caja_flotante\").hide(500);'>".$where."</textarea>
				</td>
			</tr>
		</table><br>";
		//Boton para hacerle test la query
		$style = "background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 80px; height:22px; align:center;";
		echo "
		<div align='center' id='div_calcular' >
			<input type='submit' value='Test Query' onclick='test_query()' style='".$style."'/>
		</div>
		<div id='msj_calculando' style='display:none'>
			Calculando<br>
			<img style='cursor:pointer;' src='../../images/medical/ajax-loader11.gif'>
		</div>
		<br>";
	}

	//------------------------------------------------------------------
	//Funcion para mostrar los responsables en el select del formulario
	//------------------------------------------------------------------
	function responsable($valor, $resp_select='ninguno')
	{
		global $conex;
		global $wemp_pmla;
		$options = '<option value="" >Seleccione..</option>';
		$q = "  SELECT 	Codigo, Descripcion "
			  ."  FROM 	usuarios "
			  ." WHERE 	Empresa  = '".$wemp_pmla."' "
			  ."   AND  Descripcion LIKE '%".trim($valor)."%' "
			  ."   AND  Descripcion != '' "
			  ."   AND	Activo   = 'A'"
			  ." Order by Descripcion";

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query(Filtrar Responsable): ".$q." - ".mysql_error());

			while($result = mysql_fetch_array($res))
			{
				(($resp_select != 'ninguno' AND $resp_select==$result['Codigo']) ? $selected='selected' : $selected='');
				$options .= '<option value="'.$result['Codigo'].'" '.$selected.'>'.utf8_encode(ucwords(strtolower($result['Descripcion']))).'</option>';
			}
			return $options;
	}

	//-------------------------------------
	//Funcion para mostrar las magnitudes
	//-------------------------------------
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
	//--------------------------------------------
	//	Funcion que pinta el formulario del datos
	//--------------------------------------------
	function pintar_formulario($codigo_dato)
	{
		global $conex;
		global $wbasedato;
		global $wfecha;
		global $whora;
		//===================================
		// Consultar la informacion del dato
		//===================================
		if ($codigo_dato != 'nuevo')
		{
			$q_datos = " 	SELECT *
							  FROM ".$wbasedato."_000010
							 WHERE Datcod = '".$codigo_dato."'
						";
			$res_datos = mysql_query($q_datos, $conex) or die (mysql_errno() . $q_datos . " - " . mysql_error());
			$row_informacion = mysql_fetch_array($res_datos);
		}
		if (isset($row_informacion))
			$wcodigo = $row_informacion['Datcod'];
		else
			$wcodigo = '???';

		echo "	<br><br>
		<div name='ficha' id='ficha' align='left' class='borderDiv2' style='margin: 10px;'>
				<div align='right'>
				<div class='fila2' onclick='removerElemento(\"ficha\");' title='Cerrar ficha' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick=''>
								<b>Cerrar</b>
								<img width='10' height='10' border='0' style='cursor:pointer;' title='Cerrar formulario' src='../../images/medical/eliminar1.png'>
				</div>
				</div>";
		echo"	<div class='borderDiv Titulo_azul' align=center>
				".((isset($row_informacion)) ? strtoupper(utf8_decode($row_informacion['Datnom'])): 'INGRESO DE NUEVO DATO' )."
				</div><br>";
		echo"<div id='ref_basicos' align='left'>
				<table width='900' border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td align='left' style='font-size: 11pt;'>";
							?>
								<a href="#null" onclick="javascript:verSeccionCaracterizacion('div_basicos');">
									<img width='10' height='10' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp;INFORMACI&OacuteN BASICA
								</a>
							<?php
		echo"			</td>
					</tr>
				</table>
			</div>";
		echo'
		<div id="div_basicos" align="center" class="borderDiv ">
			<br>
			<table width="94%" border="0" align="center" cellspacing="0" cellpadding="0">';
		//	-->	Codigo
		echo 	"<tr class='fila2'>
					<td class='encabezadoTabla resalto' >C&oacute;digo:&nbsp;</td>
					<td class='carBold' align=center>
						<div style='font-weight:bold;font-size:16px;color:red;' name='wcodigo' id='wcodigo'>".$wcodigo."</div>
					</td>
				</tr>";
		//	-->	Nombre de la variable
		echo 	"<tr class='fila1'>
					<td class='encabezadoTabla resalto' style='width :30%'>Nombre:&nbsp;</td>
					<td align='center' class='carBold '>
						<input type='text' size=80 name='wnombre' id='wnombre' ".((isset($row_informacion)) ? 'value=\''.utf8_decode($row_informacion['Datnom']).'\'': '' )." onmouseover='validar(this);' onblur='validar(this)'>
						<div align='right' id='div_wnombre' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Explicacion
		echo 	"<tr class='fila2'>
					<td class='encabezadoTabla resalto' >Explicaci&oacuten:</td>
					<td align='center' class='carBold'>
						<textarea rows=3 cols=60 name='wexplicacion' id='wexplicacion' >".((isset($row_informacion)) ? utf8_decode($row_informacion['Datexp']) : '' )."</textarea>
						<div align='right' id='div_wexplicacion' style='display:none;' ></div>
					</td>
				</tr>";
				((isset($row_informacion)) ? $options=responsable('', $row_informacion['Datres']) : $options=responsable('') );
		//	-->	Empresa
		echo 	"<tr class='fila1'>
					<td class='encabezadoTabla resalto' >Empresa:</td>
					<td align='center' class='carBold'>
						<select style='width:265px;' name='wempresa' id='wempresa'  onmouseover='validar(this)' onblur='validar(this)'>
							<option value=''>Seleccione..</option>
							<option value='%'>TODAS</option>";
							$array_empresas=traer_empresas();
							$check='';
							foreach($array_empresas as $wcod_emp => $wnom_emp)
							{
								if(isset($row_informacion))
								{
									(($row_informacion['Datemp']==$wcod_emp) ? $check='selected' : $check='' );
								}
								echo "<option value='".$wcod_emp."' ".$check.">".$wnom_emp."</option>";
							}
		echo "			</select>
						<div align='right' id='div_wempresa' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Magnitud
		echo	"<tr class='fila1'>
					<td class='encabezadoTabla resalto' >Magnitud:</td>
						<td align='center' class='carBold'>
							<select style='width:265px;' name='wmagnitud' id='wmagnitud' onmouseover='validar(this)' onblur='validar(this)' >
								<option value=''>Seleccione..</option>";
									//Consultar Magnitudes
									$array_magnitudes=traer_magnitudes();
									$check='';
									foreach($array_magnitudes as $wcod_mag => $wnom_mag)
									{
										if(isset($row_informacion))
										{
											(($row_informacion['Datmag']==$wcod_mag) ? $check='selected' : $check='' );
										}
										echo "<option value='".$wcod_mag."' ".$check.">".$wnom_mag."</option>";
									}
		echo "			</select>
						<div align='right' id='div_wmagnitud' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Automatico
				if(isset($row_informacion))
				{
					if($row_informacion['Dataut']=='on')
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
		echo 	"<tr id='tr_automatico' name='tr_automatico' class='fila2'>
					<td class='encabezadoTabla resalto' > &iquest;Calculo automatico?</td>
					<td align='center' class='carBold'>
						S&iacute;<input type='radio' name='wautomatico' id='wautomatico' value='on' $check_si onclick='ocultar_zona_aut(\"si\");' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        No<input type='radio' name='wautomatico' id='wautomatico' value='off' $check_no onclick='ocultar_zona_aut(\"no\");'>
						<div align='right' id='wautomatico' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Tipo
				if(isset($row_informacion))
				{
					if($row_informacion['Datcon']=='off')
					{
						$check_cal='checked=checked';
						$check_con='';
					}
					else
					{
						$check_cal='';
						$check_con='checked=\'checked\'';
					}
				}
				else
				{
					$check_cal='checked=\'checked\'';
					$check_con='';
				}
		echo 	"<tr class='fila1' id='tr_tipo' name='tr_tipo' zona_aut='on'>
					<td class='encabezadoTabla resalto' >&iquest;Tipo?</td>
					<td align='center' class='carBold'>
						Calculado<input type='radio' name='wtipo' id='wtipo' value='off' $check_cal onclick='ocultar_zona_cal(\"si\");'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Constante<input type='radio' name='wtipo' id='wtipo' value='on' $check_con onclick='ocultar_zona_cal(\"no\");'>
						<div align='right' id='wtipo' style='display:none;'></div>
					</td>
				</tr>";
		//	-->	Valor (Solo se muestra 'Tipo= constante')
		echo 	"<tr class='fila1' zona_cal='off' zona_aut='on'>
					<td class='encabezadoTabla resalto' style='width :30%'>Valor:&nbsp;</td>
					<td align='center' class='carBold '>
						<input type='text' size=80 name='wvalor_cons' id='wvalor_cons' ".((isset($row_informacion)) ? 'value=\''.utf8_encode($row_informacion['Datval']).'\'': '' )." onmouseover='validar(this);' onblur='validar(this)'>
						<div align='right' id='div_wvalor_cons' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Origen de datos (Solo se muestra 'Tipo= calculado')
		echo 	"<tr class='fila2' id='tr_dsn' name='tr_dsn' zona_aut='on' zona_cal='on'>
					<td class='encabezadoTabla resalto' >Origen de datos <font size=1>(DSN)</font>:</td>
					<td align='center' class='carBold'>
						<select style='width:265px;' name='wdsn' id='wdsn' onmouseover='validar(this)' onblur='validar(this)'>
							<option value=''>Seleccione..</option>";
							$array_dns=traer_dsn();
							$check='';
							foreach($array_dns as $wcod_dns => $wnom_dns)
							{
								if(isset($row_informacion))
								{
									(($row_informacion['Datdsn']==$wcod_dns) ? $check='selected' : $check='' );
								}
								echo "<option value='".$wcod_dns."' ".$check.">".$wnom_dns."</option>";
							}
		echo "			</select>
						<div align='right' id='div_wdsn' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Valor (query) (Solo se muestra 'Tipo= calculado')
		echo 	"<tr class='fila1' id='tr_valor' name='tr_valor' zona_aut='on' zona_cal='on'>
					<td class='encabezadoTabla resalto' >Valor <font size=1>(Query)</font>: </td>
					<td align='center' class='carBold'>";
						pintar_realizar_query($row_informacion['Datval']);
		echo	"
					</td>
				</tr>";
		//	-->	Resultado del dato como tabla temporal
				if($row_informacion['Dattte'] == 'on')
				{
					$checkbox_on = 'background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 20px; height:20px; align:center;';
					$check_temp  = 'checked';
				}
				else
				{
					$checkbox_on = '';
					$check_temp  = '';
				}
		echo 	"<tr class='fila2' id='tr_valor' name='tr_valor' zona_aut='on' zona_cal='on'>
					<td class='encabezadoTabla resalto' >Temporal:</td>
					<td align='center' class='carBold'>
						¿Asignar resultado del dato a tabla temporal?<br>
						<div id='div_iluminar' style='".$checkbox_on."'><input style='cursor:pointer' type='checkbox' ".$check_temp." id='tipo_temporal' onClick='iluminar_check(\"tipo_temporal\");'/></div>
					</td>
				</tr>";
		//  --> Subdivision
		echo 	"<tr class='Fila1' id='tr_subdivicion' zona_aut='on' zona_cal='on'>
					<td class='encabezadoTabla resalto' >Agrupar por:</td>
					<td align='center' class='carBold'>";
					//Boton para ver los paramaetros que existen en el query
					$style = "background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 80px; height:22px; align:center;";
		echo "
						<div align='center' id='div_ver_lista_sub' ><br>
							<input type='submit' value='Ver Lista' onclick='lista_subdividir()' style='".$style."'/>
						</div><br>
						<div align='center' id='div_lista_subdividir' name='div_lista_subdividir'>";
						if($row_informacion['Datsub'] != '' && $row_informacion['Datsub'] != ' ')
						{
							$where = explode('|', $row_informacion['Datval']);
							pintar_lista_subdivision($where[2], $row_informacion['Datsub']);
						}
		echo"			</div>
					</td>
				</tr>";
		//	-->	Estado
				if(isset($row_informacion))
				{
					if($row_informacion['Datest']=='on')
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
		echo 	"<tr id='tr_estado' name='tr_estado' class='Fila2' >
					<td class='encabezadoTabla resalto' > Estado:</td>
					<td align='center' class='carBold'>
						Activo<input type='radio' name='westado' id='westado' value='on' $check_si >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Inactivo<input type='radio' name='westado' id='westado' value='off' $check_no>
					</td>
				</tr>";
		// --> 	Indicadores o estadisticas en las que esta relacionado
		//		2014-03-17, Jerson trujillo
		if(isset($row_informacion))
		{
			echo"<tr id='tr_estado' name='tr_estado' class='Fila2' >
					<td class='encabezadoTabla resalto' > Pertenece a:</td>
					<td align='center' class='carBold'>
						<table>";

			// --> Buscar en cuales formulas de indicadores esta siendo usado
			$sqlEnUso = "SELECT Indcod, Indnom
						   FROM ".$wbasedato."_000001
						  WHERE Indfoc LIKE '%".$row_informacion['Datcod']."%'
			";
			$resSqlEnUso = mysql_query($sqlEnUso, $conex ) or die("<b>ERROR EN QUERY MATRIX:</b><br>".mysql_error());
			$colFila 	 = 'fila1';

			while($rowSqlEnUso = mysql_fetch_array($resSqlEnUso))
				echo "		<tr><td style='color:#000000;font-size: 9pt;'><b>".$rowSqlEnUso['Indcod']."</b> - ".utf8_decode($rowSqlEnUso['Indnom'])."</td></tr>";

			if(mysql_num_rows($resSqlEnUso) < 1)
				echo "		<tr><td style='color:#000000;font-size: 9pt;'><b>¡ Este dato no está siendo usado en ningún indicador ni estadística !</b></td></tr>";

			echo "		</table>
					</td>
				</tr>";
		}

		echo'</table><br>
		</div>
		<br>';
		//-->Cierro div
		//=========================
		//	Div Guardar
		//=========================
		echo'<div id="div_guardar" align="center" class="borderDiv ">
				<table>';
			if(isset($row_informacion))
				$accion_ficha = 'Actualizar';
			else
				$accion_ficha= 'Guardar';

			echo "	<tr>
						<td align='center'>
						<div align='center' id='div_mensaje' style='display:none;' ></div>
						<div name='boton_grabar' id='boton_grabar' class='fila2' style='width:105px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' title='Guardar Indicador' onclick='guardar(\"".$accion_ficha."\", \"".$wcodigo."\", \"despues_de_grabar\")'>
						<b>".$accion_ficha."</b>
						<img width='15' height='15' border='0' src='../../images/medical/root/grabar.png'>
						</td>
					</tr>";
		echo '	</table>
			</div><br><div>';
			//-->Cierro div Guardar
		echo'</table>
		</div>
		</div><br>';
	}
	//-------------------------------------------------------
	//	Funcion para consultar y listar los datos existentes
	//-------------------------------------------------------
	function pintar_lista($busc_nombre='%', $busc_automa='', $busc_tipo='', $busc_estado='', $codigo_dato='')
	{
		global $wbasedato;
		global $conex;

		$q_consul_dat="	SELECT Datcod, Datnom, Datexp, Datcon, Dataut, Datest
						  FROM ".$wbasedato."_000010
						 WHERE Datope != 'on'
						   AND Datnum != 'on'
						   AND Datnom LIKE '%".$busc_nombre."%'";
		if($codigo_dato != '')
		$q_consul_dat.="   AND Datcod = '".$codigo_dato."'";
		if($busc_tipo == 'on')
		$q_consul_dat.="   AND Datcon = 'on'";
		elseif($busc_tipo == 'off')
		$q_consul_dat.="   AND Datcon != 'on'";

		if($busc_automa == 'on')
		$q_consul_dat.="   AND Dataut = 'on'";
		elseif($busc_automa == 'off')
		$q_consul_dat.="   AND Dataut != 'on'";

		if($busc_estado == 'on')
		$q_consul_dat.="   AND Datest = 'on'";
		elseif($busc_estado == 'off')
		$q_consul_dat.="   AND Datest != 'on'";

		$q_consul_dat.=" ORDER BY Datnom
						";
		$res_consul_dat = mysql_query($q_consul_dat,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar datos): ".$q_consul_dat." - ".mysql_error());
		$num_datos = mysql_num_rows ($res_consul_dat);
		$fila_lista ="Fila2";

		$arr_datos_esta_en_uso = array();
		$arr_datos_esta_en_uso = datos_usados();

		echo '<div style="color: #000000;font-size: 8pt;font-weight: bold;"> Registros: '.$num_datos.'</div>';
		echo"
			<table width='99%' id='table_lista'>
				<tr class='encabezadoTabla' align='center'>
					<td>Nombre</td><td>Autom&aacutetico</td><td>Tipo</td><td>Estado</td><td align='center' style='background-color: #FFFFFF;'></td>
				</tr>";

		if ($num_datos >0)
		{
			while ($row_datos = mysql_fetch_array($res_consul_dat))
			{
				if ($fila_lista=='Fila2')
					$fila_lista = "Fila1";
				else
					$fila_lista = "Fila2";

				if ($row_datos['Datcon'] == 'on')
				{
					$tipo_dato = "Constante";
				}
				else
				{
					$tipo_dato = "Calculado";
				}

				$dat_nom = utf8_decode($row_datos['Datnom']);
				$dat_exp = utf8_decode($row_datos['Datexp']);
				$onclick = "pintar_formulario(\"".$row_datos['Datcod']."\", \"\")";
				echo "
					<tr class=".$fila_lista." style='cursor:pointer;'>
						<td onclick='".$onclick."' width='50%'>".$dat_nom."</td>
						<td onclick='".$onclick."' align='center' >".(($row_datos['Dataut']=='on') ? 'Si' : 'No')."</td>
						<td onclick='".$onclick."' align='center'>".$tipo_dato."</td>
						<td onclick='".$onclick."' align='center'>".(($row_datos['Datest']=='on') ? 'ACTIVO' : 'INACTIVO')."</td>";
					if(!array_key_exists($row_datos['Datcod'], $arr_datos_esta_en_uso) && $codigo_dato == '')
						echo"<td align='center' style='background-color: #FFFFFF;' onclick='eliminar_dato(\"".$row_datos['Datcod']."\")'><img width='10' height='10' src='../../images/medical/eliminar1.png' title='Eliminar' style='cursor:pointer;'></td>";
					echo"
					</tr>";
			}
		}
		else
		{
			echo'	<tr class="fila2" >
						<td colspan=5 align=center><b>No se encontraron indicadores.</b></td>
					</tr>';
		}
		echo "</table><br>";
	}
	//-------------------------------------------------------
	//	Funcion para consultar y listar los datos existentes
	//-------------------------------------------------------
	function listar_datos($codigo_dato)
	{
		global $wbasedato;
		global $conex;

		echo "
			<table width='97%' border='0' align='center' cellspacing='1' cellpadding='2' name='lista_datos' id='lista_datos'>
				<tr>
					<td colspan=5>
						<table width='100%'>
							<tr>
								<td width='13%' class='fila2' title='Filtrar lista de parametros' style='cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick='pintar_filtros(\"div_flitros\")'>
									<b>Filtrar lista</b>
									<img width='13' height='13' src='../../images/medical/HCE/lupa.PNG'>
								</td>
								<td width='74%'></td>
								<td width='13%' class='fila2' title='Agregar nuevo dato' style='cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick='pintar_formulario(\"nuevo\", \"\")'>
									<b>Nuevo</b>
									<img border='0' src='../../images/medical/HCE/mas.PNG'>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=5></td>
				</tr>
				<tr>
					<td colspan=5 align='center'>
							<table width='75%' id='div_flitros' class='borderDiv' style='display:none'>
								<tr>
									<td align='center' class='parrafo_text' colspan='5' align='center'><b>Selección de filtros</b></td>
								</tr>
								<tr>
									<td align='center' class='Encabezadotabla'>Código:</td>
									<td align='center' class='Encabezadotabla'>Nombre:</td>
									<td align='center' class='Encabezadotabla'>Automático:</td>
									<td align='center' class='Encabezadotabla'>Tipo:</td>
									<td align='center' class='Encabezadotabla'>Estado:</td>
								</tr>
								<tr>
									<td align='center' class='Fila2'><input type='text' size=8 name='busc_codigo' id='busc_codigo' onBlur='filtrar_lista(\"\");'/></td>
									<td align='center' class='Fila2'><input type='text' size=50 name='busc_nombre' id='busc_nombre' onBlur='filtrar_lista(\"\");'/></td>
									<td align='center' class='Fila2'>
										<select name='busc_automa' id='busc_automa' onChange='filtrar_lista(\"\");'>
											<option value=''>Todos</option>
											<option value='on'>Si</option>
											<option value='off'>No</option>
										</select>
									</td>
									<td align='center' class='Fila2'>
										<select name='busc_tipo' id='busc_tipo' onChange='filtrar_lista(\"\");'>
											<option value='%'>Todos</option>
											<option value='off'>Calculado</option>
											<option value='on'>Constante</option>
											</select>
									</td>
									<td align='center' class='Fila2'>
										<select name='busc_estado' id='busc_estado' onChange='filtrar_lista(\"\");'>
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
						echo pintar_lista('%', '', '', '', $codigo_dato);
		echo"			</div>
					</td>
				</tr>
			</table>";
	}
	//----------------------------------------------------------------------------------------------------------------------
	//	Funcion para obetener un array con los datos que estan siendo usados en ulguna formula de un indicador o estadistica
	//----------------------------------------------------------------------------------------------------------------------
	function datos_usados()
	{
		global $wbasedato;
		global $conex;
		$arr_datos_esta_en_uso = array();

		$q_lista = "SELECT Indfoc
					  FROM ".$wbasedato."_000001
					 WHERE Indfoc LIKE '%D%'
					";
		$res_lista = mysql_query($q_lista, $conex) or die ("Error: ".mysql_errno()." - en el query(Obtener lista de datos usados): ".$q_lista." - ".mysql_error());
		while($arr_lista = mysql_fetch_array($res_lista))
		{
			$valores_formula = explode('|', $arr_lista['Indfoc']);
			foreach($valores_formula as $cod_valor)
			{
				if($cod_valor{0} == 'D')
				{
					if(strstr($cod_valor, '['))
					{
						$cod_valor = substr($cod_valor, 0, stripos($cod_valor, '['));
					}

					$arr_datos_esta_en_uso[$cod_valor] = '';
				}
			}
		}
		return $arr_datos_esta_en_uso;
	}
	//--------------------------------------------------------------------------------------------------------
	//	Funcion que crea una tabla con el nombre de todas las tablas matrix y segun el parametro de busqueda
	//--------------------------------------------------------------------------------------------------------
	function desplegar_from($buscar, $worigen)
	{
		global $wbasedato;
		global $conex;

		$listado_tablas=array();
		$buscar = explode(' ', $buscar);
		$ultimo = count($buscar);
		$buscar = explode('_', $buscar[$ultimo-1]);

		// --> Datos que trabajan como tablas temporales
		$q_dat_temp = "SELECT Datcod, Datnom
						 FROM ".$wbasedato."_000010
						WHERE Datest = 'on'
						  AND Dattte = 'on'
						  AND (Datcod LIKE '%".$buscar[0]."%' OR Datnom LIKE '%".$buscar[0]."%') ";

		$res_dat_temp = mysql_query($q_dat_temp,$conex);
		while($row_dat_temp = mysql_fetch_array($res_dat_temp))
		{
			$listado_tablas[$row_dat_temp['Datcod']]= $row_dat_temp['Datnom'];
		}

		// --> Tablas de matrix
		$q_tablas_matrix.="	SELECT medico, codigo, nombre
							  FROM formulario
							 WHERE (medico LIKE '%".$buscar[0]."%' ";
						if(count($buscar)>1)
		$q_tablas_matrix.="	 		AND codigo LIKE '%".$buscar[1]."%')";
						else
		$q_tablas_matrix.="			OR codigo LIKE '%".$buscar[0]."%' OR nombre LIKE '%".$buscar[0]."%' )";

		$q_tablas_matrix.="	 GROUP BY medico, codigo
							 ORDER BY medico, codigo";

		$res_tablas_matrix = mysql_query($q_tablas_matrix,$conex) ;

		while ($row_tablas_matrix = mysql_fetch_array($res_tablas_matrix))
		{
			$tabla = $row_tablas_matrix['medico'].'_'.$row_tablas_matrix['codigo'];
			$listado_tablas[$tabla]= $row_tablas_matrix['nombre'];
		}
		echo'
		<table>';
		$color = 'Fila1';
		foreach ($listado_tablas as $nom_tabla => $descripcion)
		{
			if($color == 'Fila1')
				$color = 'Fila2';
			else
				$color = 'Fila1';

			echo'<tr class="'.$color.'"  onClick="agregar_tabla(\''.$nom_tabla.'\', \''.$worigen.'\');" style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:8pt;cursor:pointer;">
					<td width="20%">'.utf8_encode(htmlentities($nom_tabla)).'</td>
					<td width="80%">'.utf8_encode(htmlentities($descripcion)).'</td>
				</tr>';
		}
		echo'
		</table>';
	}
	//---------------------------------------------
	//	Funcion que pinta una tabla desplegable
	//---------------------------------------------
	function pintar_tabla_desplegable($listado, $worigen)
	{
		foreach ($listado as $campo => $descripcion)
		{
			if($color == 'Fila1')
				$color = 'Fila2';
			else
				$color = 'Fila1';

			echo'<tr class="'.$color.'"  onClick="agregar_tabla(\''.$campo.'\', \''.$worigen.'\' );" style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:8pt;cursor:pointer;">
					<td width="20%">'.utf8_encode(htmlentities($campo)).'</td>
					<td width="80%">'.utf8_encode(htmlentities($descripcion)).'</td>
				</tr>';
		}
	}
	//---------------------------------------------------------------------------------------------
	//	Funcion que consulta el nombre de los campos de la tablas que han seleccionado en el from
	//---------------------------------------------------------------------------------------------
	function desplegar_where_y_select($buscar, $wtablas_from, $worigen)
	{
		global $wbasedato;
		global $conex;

		$buscar = explode(' ', $buscar);
		$ultimo = count($buscar);
		$buscar = $buscar[$ultimo-1];
		$lista_campos = '';
		$color = 'Fila2';
		echo'
		<table>';
		if ($worigen=='where')
		{
			// --> Consultar Operadores basicos WHERE
			$q_operadores ="	SELECT  Sqlnom
								  FROM 	".$wbasedato."_000015
								 WHERE	Sqlnom LIKE '%".$buscar."%'
								   AND  Sqlest = 'on'
								   AND  Sqlwhe = 'on'
							  ORDER BY	Sqlnom ";
			$res_operadores = mysql_query($q_operadores,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar operadores): ".$q_operadores." - ".mysql_error());
			if(mysql_num_rows($res_operadores)>0)
			{
				while($row_operadores = mysql_fetch_array($res_operadores))
				{
					$campo   			= $row_operadores['Sqlnom'];
					$listado[$campo] 	= $campo;
				}
				pintar_tabla_desplegable($listado, $worigen);
				unset($listado);
			}

			// --> Consultar Parametros
			$q_parametros ="	SELECT  Parcod
								  FROM 	".$wbasedato."_000012
								 WHERE	Parcod LIKE '%".$buscar."%'
								   AND  Parest = 'on'
							  ORDER BY	Pardes ";
			$res_parametros = mysql_query($q_parametros,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar parametros): ".$q_parametros." - ".mysql_error());
			if(mysql_num_rows($res_parametros)>0)
			{
				while($row_parametros = mysql_fetch_array($res_parametros))
				{
					$campo = $row_parametros['Parcod'];
					$listado[$campo]= $campo;
				}
				pintar_tabla_desplegable($listado, $worigen);
				unset($listado);
			}
		}
		else
		{
			// --> Consultar Operadores basicos SELECT
			$q_operadores ="	SELECT  Sqlnom
								  FROM 	".$wbasedato."_000015
								 WHERE	Sqlnom LIKE '%".$buscar."%'
								   AND  Sqlest = 'on'
								   AND  Sqlsel = 'on'
							  ORDER BY	Sqlnom ";
			$res_operadores = mysql_query($q_operadores,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar operadores): ".$q_operadores." - ".mysql_error());
			if(mysql_num_rows($res_operadores)>0)
			{
				while($row_operadores = mysql_fetch_array($res_operadores))
				{
					$campo   			= $row_operadores['Sqlnom'];
					$listado[$campo] 	= $campo;
				}
				pintar_tabla_desplegable($listado, $worigen);
				unset($listado);
			}
		}

		// --> Consultar los campos correspondientes segun las tablas que han seleccionado y segun la busqueda
		$wtablas_from = str_replace(' ','', $wtablas_from);
		$row_tablas   = explode (',', $wtablas_from);
		foreach($row_tablas as $nom_tabla)
		{
			if ($nom_tabla !='')
			{
				// --> Es un dato temporal, debo obtener que campos seleccionaron en el select
				if($nom_tabla{0} == 'D' && $nom_tabla{1} == 'T')
				{
					$q_dat = "SELECT Datval, Datcod
								FROM ".$wbasedato."_000010
							   WHERE Datcod = '".trim($nom_tabla)."' ";
					$res_dat = mysql_query($q_dat, $conex)or die("Error: " . mysql_errno() . " - en el query (Consultar campos del dato temporal): ".$q_dat." - ".mysql_error());;
					if($row_dat = mysql_fetch_array($res_dat))
					{
						$select 	= explode('|', $row_dat['Datval']);
						$row_select = explode (',', $select[0]);
						foreach($row_select as $campo)
						{
							$campo = trim($campo);
							if(stristr($campo, ' as '))
							{
								$campo = stristr($campo, ' as ');
								$campo = str_replace('as ', '', $campo);
								$campo = trim(str_replace('AS ', '', $campo));
							}
							elseif(stristr($campo, ' '))
							{
								$campo = stristr($campo, ' ');
								$campo = str_replace(' ', '', $campo);
							}
							$listado[$campo]= 'Campo Tabla Temporal '.$row_dat['Datcod'];
						}
						pintar_tabla_desplegable($listado, $worigen);
						unset($listado);
					}
				}
				// --> Es una tabla normal de matrix
				else
				{

					$row_med_cod = explode ('_', $nom_tabla);
					$wmedico = $row_med_cod[0];
					$codigo	 = $row_med_cod[1];
					$q_campos_tabla ="	SELECT  descripcion, Dic_Descripcion
										  FROM 	det_formulario as A, root_000030
										 WHERE	A.medico 		= '".$wmedico."'
										   AND  A.codigo 		= '".$codigo."'
										   AND 	Dic_Usuario 	=  A.medico
										   AND 	Dic_Formulario 	= A.codigo
										   AND 	Dic_Campo 		= A.campo
										   AND 	( A.descripcion LIKE '%".$buscar."%' OR Dic_Descripcion	LIKE '%".$buscar."%' )
									  ORDER BY	descripcion ";
					$res_campos_tabla = mysql_query($q_campos_tabla,$conex) or die("Error: " . mysql_errno() . " - en el query (Consulta para desplegar el where): ".$q_campos_tabla." - ".mysql_error());

					if(mysql_num_rows($res_campos_tabla)>0)
					{
						while($row_campos_tabla = mysql_fetch_array($res_campos_tabla))
						{
							$campo = $row_campos_tabla['descripcion'];
							$listado[$campo]= $row_campos_tabla['Dic_Descripcion'];
						}
						pintar_tabla_desplegable($listado, $worigen);
						unset($listado);
					}
				}
			}
		}
		echo'
		</table>';
	}
	//-------------------------------------------------------------------------
	// Funcion para obtener las descripciones de los valores de un parametro
	//-------------------------------------------------------------------------
	function obtener_descrip_valores($parametro)
	{
		global $wbasedato;
		global $conex;
		$resultado_parametro = array();
		// --> Informacion basica del parametro
		$q_in_param = " SELECT *
						  FROM ".$wbasedato."_000012
						 WHERE Parcod = '".$parametro."'
					";
		$res_in_param = mysql_query($q_in_param, $conex) or die (mysql_errno() . $q_in_param . " (Query: Consultar informacion del parametro) " . mysql_error());
		if(mysql_num_rows($res_in_param) > 0)
		{
			while($row_in_param= mysql_fetch_array($res_in_param))
			{
				//Armar el query del parametro
				$select_param	  = 	'SELECT '.$row_in_param['Parcrv'].', '.$row_in_param['Parcrm'].'';
				$from_param 	  = 	'  FROM '.$row_in_param['Partab'];
				if($row_in_param['Parcon'] != '')
					$where_param 	  = ' WHERE '.$row_in_param['Parcon'];

				$query_parametro = $select_param.$from_param.$where_param;
				//Fin Armar query

				//Ejecuto el query
				ejecutar_query($query_parametro, $row_in_param['Pardsn'], $resultado_parametro, 'parametro');
			}
		}
		return $resultado_parametro;
	}
	//-------------------------------------------
	//	FUNCION QUE CALCULA EL VALOR DE UN DATO
	//-------------------------------------------
	function calcular_dato($valor_dato, $parametros_valores='', $wdsn, $param_agrupado='', $tabla_temporal, $cod_dato = '', $campos_formulario='', $llamado_jquery = 'no')
	{
		global $conex;
		global $wbasedato;
		global $color_fila;
		global $parametros_ya_pintados;
		$color_fila = 'Fila2';
		$array_parametros_ya_seleccionados = array();
		$hacer_agrupacion = 'NO';
		//echo '<br>'.$campos_formulario;

		if($parametros_valores!='')			//Aqui entra si los parametros ya se han selecionado
		{
			// Si los parametros ya se han seleccionado creo las variables de
			// los correspondientes parametros con sus valores seleccionados
			$array_parrametros_valores = explode('|-|', $parametros_valores);
			foreach($array_parrametros_valores as $parametro_nom_val )
			{
				if($parametro_nom_val !='')
				{
					$parametro_nom_val = explode('|', $parametro_nom_val);
					$nom_param_seleccionado = $parametro_nom_val[0];
					$val_param_seleccionado = $parametro_nom_val[1];
					if(array_key_exists($nom_param_seleccionado, $array_parametros_ya_seleccionados))
					{
						$array_parametros_ya_seleccionados[$nom_param_seleccionado].= ','.$val_param_seleccionado;
					}
					else
					{
						$array_parametros_ya_seleccionados[$nom_param_seleccionado] = $val_param_seleccionado;
					}
				}
			}
		}

		if($llamado_jquery == 'si')
		{
			echo "<Div style='width:90%;'>";
			echo'<br><div class="fondoAmarillo" align=center>Test del query</div><br>
				<table style="width:90%;border: 1px solid #999999;">
					<tr><td colspan="2" align="center">';
		}

		$ejecutar_query = 'si';
		$arr_secciones_query = explode('|', $valor_dato);	//El query viene separado por |, asi = 'el_select|el_from|el_where'
		$select = str_replace('\\', '' , $arr_secciones_query[0]);
		$from 	= str_replace('\\', '' , $arr_secciones_query[1]);

		// --> Primero debo buscar en el from si existen tablas temporales y ejecutarlas
		$tablas_from = explode(',', $from);

		foreach($tablas_from as $tabla)
		{
			$tabla = trim($tabla);
			// --> Quitar el alias del nombre si que existe
			if(strstr($tabla, ' '))
				$tabla = substr($tabla, 0, stripos($tabla, ' '));

			if($tabla != '')
			{
				$query_temporal = '';
				$dsn_temporal	= '';
				$ejecutar_temporal = ejecutar_tabla_temporal($tabla, $query_temporal, $dsn_temporal);
				if($ejecutar_temporal)
				{
					calcular_dato($query_temporal, $parametros_valores, $dsn_temporal, '', 'si', $tabla, $campos_formulario);
					$ejecutar_query = 'no';
				}
			}
		}

		$where 	= str_replace('\\', '' , ' '.$arr_secciones_query[2]);
		// --> Debo recorrer toda la cadena del where, caracter por caracter, para mirar si existe algun parameto o algun dato
		if ($where != '')
		{
			$longitud = strlen($where);
			$parametro_temp = '';
			$array_parametros = array();

			// --> Obtener los codigos de los parametros que contiene el where
			for ($i=0; $i<=$longitud ; $i++)
			{
				if ($parametro_temp == '')
				{
					if ($where{$i} == '$')	//Indica que es el inicio de un parametro
					{
						$parametro_temp.= $where{$i};
						$posicion_ini = $i;
					}
				}
				else
				{
					if($where{$i} == "%" || $where{$i} == "'")	//Indica que es el fin de un parametro
					{
						$posicion_fin = $i;
						//En este array voy guardando los parametros existentes
						$array_parametros[]=$parametro_temp;
						$parametro_temp='';
					}
					else
					{
						$parametro_temp.= $where{$i}; //Aqui voy armando la variable del parametro
					}
				}
			}
			// --> Recorro el array de los parametros que contiene el where
			// --> Recorro el array de los parametros que contiene el where
			if(count($array_parametros) > 0)
			{
				foreach($array_parametros as $indi => $cod_parametro)
				{
					// --> Si el parametro ya se selcciono, adapto el where con el valor escogido
					if(array_key_exists( $cod_parametro , $array_parametros_ya_seleccionados))
					{
						$valores_parametro_seleccionado = explode (',' ,$array_parametros_ya_seleccionados[$cod_parametro]);

						// --> Si cuando seleccionaron los parametros del dato, escogieron mas de una opcion.
						if (count($valores_parametro_seleccionado) > 1)
						{
							$where = insertar_comparacion_in($cod_parametro, $valores_parametro_seleccionado, $where);
						}
						else
						{
							// --> Si el valor que seleccionaron para el parametro es igual a % significa que se selecciono la opcion 'TODOS'
							if($array_parametros_ya_seleccionados[$cod_parametro] == '%')
							{
								// --> Si este parametro es el por el que se debe agrupar, debo modificar el query agregandole un group by.
								if($param_agrupado != '' && $param_agrupado==$cod_parametro)
								{
									// --> Buscar el nombre del campo comparado con el parametro, para acomodarlo en el group by y en el select
									$posicion_ini = strpos($where, $cod_parametro);
									for($y = $posicion_ini; $y>=0; $y--)
									{
										// --> Buscar el = de la comparacion
										if($where{$y} == '=')
										{
											// --> Armo el nombre del campo
											$campo_comparacion = '';
											for($z = $y-1; $z >= 0; $z--)
											{
												// Si encontro un espacio en blanco y ya existen caracteres en la variable
												if($where{$z} == ' ' && $campo_comparacion != '')
												{
													// Invierto la cadena y le quito los espacios en blanco
													$campo_comparacion = trim(strrev($campo_comparacion));
													break;
												}
												else
													$campo_comparacion.= $where{$z};
											}
											break;
										}
									}
									$hacer_agrupacion = 'SI';

									// --> Modifico el where, para agregarle un group by
									$where.= ' GROUP BY '.$campo_comparacion;

									// --> Modifico el select para agregarle el campo
									$select.= ' ,'.$campo_comparacion;
								}

								// --> Reemplazo la comparacion normal del query, por un IN con todos los posibles valores que contenga el parametro
								$array_valores_parametro = valores_parametro($cod_parametro);

								$new_array_valores_parametro = array();

								foreach ($array_valores_parametro as $cod_valor => $nom_valor)
								{
									$new_array_valores_parametro[] = $cod_valor;
								}

								$where = insertar_comparacion_in($cod_parametro, $new_array_valores_parametro, $where);
							}
							// --> Se selecciono una sola opcion
							else
							{
								$where = str_replace($cod_parametro, $array_parametros_ya_seleccionados[$cod_parametro], $where);
							}
						}
					}
					// --> El parametro no lo han seleccionado entonces ejecuto el parametro y lo pinto
					else
					{
						$ejecutar_query = 'no';
						if($color_fila=='Fila1')
							$color_fila='Fila2';
						else
							$color_fila='Fila1';

						// --> Si el parametro no se ha pintado
						if(!array_key_exists($cod_parametro, $parametros_ya_pintados)) //Si existe es porque ya se pinto
						{
							// --> Crear array donde voy guardando los codigos de los parametros que ya he pintado
							$parametros_ya_pintados[$cod_parametro]='';

							// --> Calcular y pintar el parametro
							calcular_parametro($cod_parametro);
						}
					}
				}
			}
		}

		//Aqui se ejecuta el query del dato
		if($ejecutar_query == 'si')
		{
			// --> Se ejecutara el query pero insertando su resulado a una temporal
			if($tabla_temporal == 'si')
			{
				if($cod_dato == '')
					$nombre_temporal = 'tem_sgc';
				else
					$nombre_temporal = $cod_dato;

				$tipo_unix = borrar_tabla_temporal($wdsn, $nombre_temporal);
				// --> Si es para unix
				if($tipo_unix == 'on')
					$query_dato = 'SELECT '.$select.' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.$where : '').' INTO TEMP '.$nombre_temporal;
				// --> Si es para matrix
				else
					$query_dato = 'CREATE TEMPORARY TABLE IF NOT EXISTS '.$nombre_temporal.' AS SELECT '.$select.' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.$where : '');

				$tipo_query = 'temporal';
			}
			// --> Se ejecutara el query normal
			else
			{
				$query_dato = 'SELECT '.$select.' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.$where : '');
				$tipo_query = 'test';
			}

			$resultado_query 	 = array();
			$tiempo_inicio_query = array_sum(explode(' ', microtime()));
			ejecutar_query($query_dato, $wdsn, $resultado_query, $tipo_query, $hacer_agrupacion);
			$tiempo_final_query  = array_sum(explode(' ', microtime(true)));
			$duracion_ejecucion = $tiempo_final_query-$tiempo_inicio_query;

			// --> Pinto el resultado del query
			if(count($resultado_query) > 0 )
			{
				//$array_descrip_valores = obtener_descrip_valores($param_agrupado);
				$color_fil = 'Fila1';
				// --> Resultado de una tabla temporal
				if($tabla_temporal == 'si')
				{
					if($resultado_query[0]=='TRUE')
					{
						if($cod_dato == '')
						{
							echo "</td></tr>
								<tr>
									<td colspan='2' class='Encabezadotabla' align='center' style='cursor:pointer;' colspan='2' onClick='$(\"[res_detallado=si]\").toggle();' ><img width='11' height='11' src='../../images/medical/iconos/gifs/i.p.next[1].gif'/>&nbsp;&nbsp;<b>Resultado del query</b></td>
								</tr>
								<tr class='Fila2'><td align='center'><span style='color:#04B431'>Temporal creada con éxito</span></td></tr>";
						}
						else
						{
							$campos_form_pint   = explode('<>', $campos_formulario);
							$valor_dato_f 		= $campos_form_pint[0];
							$dsn_f 		  		= $campos_form_pint[1];
							$tipo_temporal_f  	= $campos_form_pint[2];
							$param_agrupado_f 	= $campos_form_pint[3];

							calcular_dato($valor_dato_f, $parametros_valores, $dsn_f, $param_agrupado_f, '', '', $campos_formulario);
							return;
						}
					}
					else
					{
						if($cod_dato == '')
						{
							echo "</td></tr>
								<tr>
									<td class='Encabezadotabla' align='center' style='cursor:pointer;' colspan='2' onClick='$(\"[res_detallado=si]\").toggle();' ><img width='11' height='11' src='../../images/medical/iconos/gifs/i.p.next[1].gif'/>&nbsp;&nbsp;<b>Resultado del query</b></td>
								</tr>
								<tr class='Fila2'><td align='center'><span style='color:#FF0000'>Temporal no creada</span></td></tr>";
						}
						else
							return false;
					}
				}
				// --> Un resultado de un query normal
				else
				{
					echo "	</td></tr>
							<tr>
								<td class='Encabezadotabla' align='center' style='cursor:pointer;' colspan='2' onClick='$(\"[res_detallado=si]\").toggle();' ><img width='11' height='11' src='../../images/medical/iconos/gifs/i.p.next[1].gif'/>&nbsp;&nbsp;<b>Resultado del query</b></td>
							</tr>
							<tr style='background-color: #FFFFFF;' res_detallado='si'>
								<td colspan='2' align='center'>
									<table width='100%'>";
					foreach($resultado_query as $result)
					{
						if($color_fil == 'Fila2')
							$color_fil = 'Fila1';
						else
							$color_fil = 'Fila2';

						// --> Un resultado con varias filas (Mas de un campo en el select)
						if(is_array($result))
						{
							echo "	<tr>";
							foreach($result as $valor_result)
								echo "	<td align='center' class='".$color_fil."' style='font-size: ".((count($result) > 10) ? "7" : "8")."pt;'>".$valor_result."</td>";
							echo "	</tr>";
						}
						// --> El resultado es un solo valor
						else
						{
							if($resultado_query[0]=='')
								echo '<tr class="Fila2"><td align="center">Sin resultados.</td></tr>';
							else
								echo '<tr class="Fila2"><td align="center">'.$resultado_query[0].'</td></tr>';
						}
					}
					echo "			</table>
								</td>
							</tr>";
				}
			}
			// --> Query ejecutado y tiempo de ejecucion
			echo "
						<tr>
							<td class='Encabezadotabla' align='center' colspan='2' onClick='$(\"#ver_query\").toggle();' style='cursor:pointer;'><img width='11' height='11' src='../../images/medical/iconos/gifs/i.p.next[1].gif'/>&nbsp;&nbsp;Query ejecutado</td>
						</tr>
						<tr class='Fila2' id='ver_query'><td colspan='2'>".$query_dato."</td></tr>
						<tr>
							<td class='Encabezadotabla' align='center' colspan='2' onClick='$(\"#ver_query_tiempo\").toggle();' style='cursor:pointer;'><img width='11' height='11' src='../../images/medical/iconos/gifs/i.p.next[1].gif'/>&nbsp;&nbsp;Tiempo de ejecución</td>
						</tr>";
			if($duracion_ejecucion >= 10)
				$color_duracion = '#FF0000';
			if($duracion_ejecucion > 1 && $duracion_ejecucion < 10)
				$color_duracion = 'orange';
			if($duracion_ejecucion < 1)
				$color_duracion = '#04B431';

			echo"		<tr class='Fila2' id='ver_query_tiempo'><td align='center' colspan='2' style='color:".$color_duracion."';><b>".$duracion_ejecucion."  Segundo(s)</b></td></tr>
					</table>
				<br>
				<input type='submit' value='Cerrar Test' onclick='cerrar_test()' style='background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 80px; height:22px; align:center;'/>
				</div>";
		}
		else
		{
			if($llamado_jquery == 'si')
			{
				echo "
				<tr><td align='center' colspan='2'>
				<div style='display:none;' name='div_mensaje_validacion' id='div_mensaje_validacion'></div>
				<input type='submit' id='boton_calcular' value='Calcular' onclick='ejecutar_indicador()' style='background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 80px; height:22px; align:center;'/>
				<input type='submit' value='Cerrar Test' onclick='cerrar_test()' style='background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 80px; height:22px; align:center;'/>
				</td></tr>
				</table>
				</div>";
			}
			else
			{
				// --> Este hidden es para enviar el codigo del dato para su calculo, este caso sucede cuando es un query que trabaja sobre
				//	   una temporal; en este caso el debe calcular el query pero del dato es decir lo que este guardado en el campo Datval,
				//	   no el query que este pintado en el formulario en es momento.
				$valor_dato = str_replace('"', "'", $valor_dato);
				echo '<input type="hidden" id="query_dato" value="'.$valor_dato.'">';
				echo '<input type="hidden" id="dsn_query_dato" value="'.$wdsn.'">';
				echo '<input type="hidden" id="cod_dato" value="'.$cod_dato.'">';
			}
		}
	}
	//-----------------------------------------------------------
	//	FUNCION QUE BUSCA SI HAY TABLAS TEMPORALES CREADAS Y LAS ELIMINA
	//-----------------------------------------------------------
	function eliminar_temporales_creadas($tabla)
	{
		global $conex;
		global $wbasedato;

		// --> Consultar si la tabla es temporal, en el maestro de los datos
		$q_tabla_temp = " SELECT Datdsn, Dsnunx, Dsnnom
							FROM ".$wbasedato."_000010, ".$wbasedato."_000011
						   WHERE Datcod = '".$tabla."'
							 AND Dattte = 'on'
							 AND Datdsn = Dsncod
						";
		$res_tabla_temp = mysql_query($q_tabla_temp, $conex) or die (mysql_errno() . $q_tabla_temp . " (Query: Consultar informacion si existe tabla temporal) " . mysql_error());
		if($arr_tabla_temp = mysql_fetch_array($res_tabla_temp))
		{
			// --> Consultar si la tabla temporal existe
			$q_temp_existe = "SELECT *
							    FROM ".$tabla."
							";
			if($arr_tabla_temp['Dsnunx'] == 'on')
			{
				$conexunix = @odbc_connect($arr_tabla_temp['Dsnnom'],'informix','sco');
				$res_temp_existe = @odbc_exec($conexunix, $q_temp_existe);
					
				odbc_close($conexunix);
				odbc_close_all();
			}
			else
				$res_temp_existe = mysql_query($q_temp_existe, $conex);

			if($res_temp_existe)
			{
				borrar_tabla_temporal($arr_tabla_temp['Datdsn'], $tabla);
			}
		}
	}
	//---------------------------------------------------
	//	FUNCION QUE CALCULA EL VALOR DE UN PARAMETRO
	//---------------------------------------------------
	function calcular_parametro ($cod_parametro)
	{
		global $conex;
		global $wbasedato;
		global $color_fila;

		$q_inf_param = "SELECT *
						  FROM ".$wbasedato."_000012, ".$wbasedato."_000013
						 WHERE Parcod = '".$cod_parametro."'
						   AND Partcp = Tcpcod
					";
		$res_inf_param = mysql_query($q_inf_param, $conex) or die (mysql_errno() . $q_inf_param . " (Query: Consultar informacion del parametro) " . mysql_error());
		if(mysql_num_rows($res_inf_param) > 0)
		{
			while($row_inf_param= mysql_fetch_array($res_inf_param))
			{
				$referenciado = $row_inf_param['Tcpref'];
				$dsn_paramet  = $row_inf_param['Pardsn'];

				//Conocer el tipo de captura del parametro
				if($row_inf_param['Tcpcal']=='on')
					$tipo_captura = 'calendario';
				if($row_inf_param['Tcprad']=='on')
					$tipo_captura = 'radio';
				if($row_inf_param['Tcpche']=='on')
					$tipo_captura = 'checkbox';
				if($row_inf_param['Tcpsel']=='on')
					$tipo_captura = 'seleccion';
				if($row_inf_param['Tcptex']=='on')
					$tipo_captura = 'texto';


				//Si el parametro es referenciado su valor es un query por ende debo calcularlo
				if($referenciado=='on')
				{
					$valores_parametro = array();

					//Armar el query del parametro
					$select_param 	= 	'SELECT '.$row_inf_param['Parcrv'].', '.$row_inf_param['Parcrm'];
					$from_param 	= 	'  FROM '.$row_inf_param['Partab'];
					if($row_inf_param['Parcon'] != '')
						$where_param 	= 	' WHERE '.$row_inf_param['Parcon'];
					else
						$where_param 	= 	'';
					//Fi Armar query

					$query_parametro = $select_param.$from_param.$where_param;

					ejecutar_query($query_parametro, $dsn_paramet, $valores_parametro, 'parametro');
					if(count($valores_parametro) > 0)
						pintar_captura_parametro ($tipo_captura, $cod_parametro, $row_inf_param, $valores_parametro);
				}
				else
				{
					pintar_captura_parametro ($tipo_captura, $cod_parametro, $row_inf_param, '');
				}
			}
		}
		else
		{
			echo "
			<tr class='FondoAmarillo'><td colspan=2 align=center>!!! Por favor informar a el area de informatica la siguiente inconsistencia!!!</tr></td>
			<tr><td class='Encabezadotabla'>Codigo Parametro:</td><td class='Fila2'>".$cod_parametro."</td></tr>
			<tr><td class='Encabezadotabla'>Consulta:</td><td class='Fila2'>".$q_inf_param."</td></tr>
			<tr><td class='Encabezadotabla'>Incosistencia:</td><td class='Fila2'>El parametro no existe</td></tr>";
		}
	}
	//---------------------------------------------------
	//	FUNCION QUE PINTA LA CAPTURA DE UN PARAMETRO
	//---------------------------------------------------
	function pintar_captura_parametro ($tipo_captura, $cod_parametro, $row_inf_param, $lista_valores)
	{
		global $color_fila;
		switch($tipo_captura)
		{
			case 'texto':
			{
				echo "
					<tr>
						<td class='encabezadoTabla' align='center'>".$row_inf_param['Pardes'].":</td>
					</tr>
					<tr class='Fila2' >
						<td align='center'>" ;
				echo 		"<input type='text' name='".$cod_parametro."' id='".$cod_parametro."' value='".$row_inf_param['Parvde']."' rel='parametro' tipo_cap='".$tipo_captura."'/>";
				echo 	"</td>
					</tr>
					";
				break;
			}
			case 'calendario':
			{
				echo "
					<tr>
						<td class='encabezadoTabla' align='center'>".$row_inf_param['Pardes'].":</td>
					</tr>
					<tr class='Fila2'>
						<td align='center'>" ;
				echo 		campoFechaDefecto_local($cod_parametro, Date('Y-m-d'));
				echo 	"</td>
					</tr>
					";
				break;
			}
			case 'seleccion':
			{
				echo "
					<tr>
						<td class='encabezadoTabla' align='center'>".$row_inf_param['Pardes'].":</td>
					</tr>
					<tr class='Fila2'>
						<td align='center'>";
				unset($array_valores_defecto);
				$array_valores_defecto = explode(',',$row_inf_param['Parvde']);
				echo " <select name='".$cod_parametro."' id='".$cod_parametro."' rel='parametro' tipo_cap='".$tipo_captura."'>";
				if($lista_valores!='')
				{
					if(in_array('*', $array_valores_defecto))
							$seleccion_defecto = 'SELECTED';
					else
						$seleccion_defecto = '';
					echo "<option value='%' ".$seleccion_defecto." >TODOS</option>";

					foreach($lista_valores as $codigo_val => $nombre_val)
					{
						if(in_array($codigo_val, $array_valores_defecto, TRUE))
							$seleccion_defecto = 'SELECTED';
						else
							$seleccion_defecto = '';
						echo "<option value='".$codigo_val."' ".$seleccion_defecto." >".$nombre_val."</option>";
					}
				}
				echo"	</td>
					</tr>";
				break;
			}
			case 'radio':
			{
				echo "
					<tr>
						<td class='encabezadoTabla' align='center'>".$row_inf_param['Pardes'].":</td>
					</tr>
					<tr class='Fila2'>
						<td align='center'>";
				if($lista_valores!='')
				{
					unset($array_valores_defecto);
					$array_valores_defecto = explode(',',$row_inf_param['Parvde']);
					$x=2;
					echo "<table style='width:95%;color: #000000;font-size: 7pt;font-family: verdana;font-weight: bold;'>";

					//Pintar la opcion de TODOS
					if(in_array('*', $array_valores_defecto))
							$seleccion_defecto = 'CHECKED';
					else
						$seleccion_defecto = '';
					echo "<tr><td style='width:50%' NOWRAP><input type='radio' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='%' ".$seleccion_defecto." >TODOS </td>";
					//Fin pintar la opcion de TODOS

					foreach($lista_valores as $codigo_val => $nombre_val)
					{
						if(in_array($codigo_val, $array_valores_defecto, TRUE))
							$seleccion_defecto = 'CHECKED';
						else
							$seleccion_defecto = '';

						if ($x == 2)
						{
							echo "<td style='width:50%' NOWRAP><input type='radio' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='".$codigo_val."' ".$seleccion_defecto." >".$nombre_val."</td></tr>";
							$x=1;
						}
						else
						{
							echo "<tr><td style='width:50%' NOWRAP><input type='radio' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='".$codigo_val."' ".$seleccion_defecto." >".$nombre_val." </td>";
							$x=2;
						}
					}
					echo "</table>";
				}
				echo"	</td>
					</tr>";
				break;
			}
			case 'checkbox':
			{
				echo "
					<tr>
						<td class='encabezadoTabla' align='center'>".$row_inf_param['Pardes'].":</td>
					</tr>
					<tr class='Fila2'>
						<td align='center'>";
				if($lista_valores!='')
				{
					unset($array_valores_defecto);
					$array_valores_defecto = explode(',',$row_inf_param['Parvde']);
					$x=2;
					echo "<table style='width:95%;color: #000000;font-size: 8pt;font-family: verdana;font-weight: bold;'>";

					//Pintar la opcion de TODOS
					if(in_array('*', $array_valores_defecto))
							$seleccion_defecto = 'CHECKED';
					else
						$seleccion_defecto = '';
					echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='%' ".$seleccion_defecto." >TODOS</td>";
					//Fin pintar la opcion de TODOS

					foreach($lista_valores as $codigo_val => $nombre_val)
					{
						if(in_array($codigo_val, $array_valores_defecto, TRUE))
							$seleccion_defecto = 'CHECKED';
						else
							$seleccion_defecto = '';

						if ($x == 2)
						{
							echo "<td style='width:50%' NOWRAP><input type='checkbox' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='".$codigo_val."' ".$seleccion_defecto." >".$nombre_val."</td></tr>";
							$x=1;
						}
						else
						{
							echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='".$codigo_val."' ".$seleccion_defecto." >".$nombre_val." </td>";
							$x=2;
						}
					}
					echo "</table>";
				}
				echo"	</td>
					</tr>";
				break;
			}
		}
	}
	//----------------------------------------------------------------------------------------------
	//	Pintar lista de subdivision, segun los parametros que existan en el where de la formula
	//----------------------------------------------------------------------------------------------
	function pintar_lista_subdivision($where, $valor_seleccionado='')
	{
		global $wbasedato;
		global $conex;
		$array_parametros = array();

		if ($where != '')
		{
			$where = str_replace('\\', '', $where);
			$longitud = strlen($where);
			$parametro_temp = '';

			for ($i=0; $i<=$longitud ; $i++)
			{
				if ($parametro_temp == '')
				{
					if ($where{$i} == '$')	//Indica que es el inicio de un parametro
					{
						$parametro_temp.= $where{$i};
					}
				}
				else
				{
					if($where{$i} == "'" || $where{$i} == "%")	//Indica que es el fin de un parametro
					{
						//En este array voy guardando los parametros existentes
						$array_parametros[]=$parametro_temp;
						$parametro_temp='';
					}
					else
					{
						$parametro_temp.= $where{$i}; //Aqui voy armando la variable del parametro
					}
				}
			}

		}
		echo "<select style='width:265px;' name='wsubdividir' id='wsubdividir'>
				<option value=''>Ninguna..</option>";
		foreach($array_parametros as $parametro)
		{
			// Solo los parametros que sean referenciados
			$q_tipo_param = 'SELECT Parcod
							   FROM '.$wbasedato.'_000012, '.$wbasedato.'_000013
							  WHERE Parcod = "'.$parametro.'"
							    AND Partcp = Tcpcod
								AND Tcpref = "on"
							';
			$res_tipo_param = mysql_query($q_tipo_param,$conex) or die ("Error: ".mysql_errno()." - en el query (Verificar si el parametro es referenciado): ".$q_tipo_param." - ".mysql_error());
			if(mysql_num_rows($res_tipo_param) > 0)
			{
				if($valor_seleccionado !='' && $valor_seleccionado == $parametro)
					$defecto= 'SELECTED';
				else
					$defecto= '';
				echo "<option ".$defecto." value='".$parametro."'>".$parametro."</option>";
			}
		}
		echo "</select>";
	}

	//----------------------------
	//	Pintar calendario zapatec
	//----------------------------
	function campoFechaDefecto_local($nombreCampo,$fechaDefecto)
	{
		echo "<INPUT TYPE='text' NAME='$nombreCampo' id='$nombreCampo' value='".$fechaDefecto."' size=11 readonly class='textoNormal' rel='parametro' tipo_cap='calendario' >";
		echo "&nbsp;<button id='btn$nombreCampo'>...</button>";
		funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'$nombreCampo',button:'btn$nombreCampo',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
	}
//=======================================================================================================================================================
//	FIN FUNCIONES
//=======================================================================================================================================================

//=======================================================================================================================================================
//	Filtros de llamados por Jquery o Ajax
//=======================================================================================================================================================
	if(isset($accion))
	{
		switch($accion)
		{
			case 'Guardar':
			{
				$wvalor = str_replace("'", "\'", $wvalor);
				$wcodigo = generar_codigo($wtipo_temporal);
				$q_guardar="	INSERT INTO ".$wbasedato."_000010
								( Medico, 		  Fecha_data,  	Hora_data, 		Datcod,			Datnom, 	 	Datval, 	  Datexp, 				Datnum, 	Datope,   	Datcon, 			Dattte, 	 		Dataut, 	     Datest,	  Datdsn, 		Datemp, 	     	Datmag,  		Datsub, 	Seguridad, id )
						VALUES  ('".$wbasedato."','".$wfecha."','".$whora."','".$wcodigo."','".$wnombre."','".$wvalor."','".$wexplicacion."',		'off',		'off',  '".$wtipo."','".$wtipo_temporal."','".$wautomatico."','".$westado."','".$wdsn."','".$wempresa."',  '".$wmagnitud."', '".$wsubdividir."','C-".$wuse."','' )
							";
				
				$res = mysql_query($q_guardar,$conex) or die ("Error: ".mysql_errno()." - en el query (Guardar variable): ".$q_guardar." - ".mysql_error());
				echo 'Dato creado|'.$wcodigo;
				break;
				return;
			}
			case 'Actualizar':
			{
				$wvalor = str_replace("'", "\'", $wvalor);
				
				// --> Si lo convirtieron en temporal, modifico el codigo.
				if($wtipo_temporal == 'on' && $wcodigo{1} != 'T')
					$new_wcodigo = str_replace('D', 'DT', $wcodigo);

				// --> Si era temporal y quedo no temporal
				if($wtipo_temporal == 'off' && $wcodigo{0} == 'D' && $wcodigo{1} == 'T')
					$new_wcodigo = str_replace('DT', 'D', $wcodigo);

				$q_actual = "UPDATE ".$wbasedato."_000010
								SET Fecha_data 	= 	'".$wfecha."' ,
									Hora_data	= 	'".$whora."',
									Datnom		=	'".$wnombre."',
									Datval		=	'".$wvalor."',
									Datexp		=	'".$wexplicacion."',
									Datcon		=	'".$wtipo."',
									Dataut		=	'".$wautomatico."',
									Dattte		=	'".$wtipo_temporal."',
									Datdsn		=	'".$wdsn."',
									Datemp		=	'".$wempresa."',
									Datest		=	'".$westado."',
									Datmag		=	'".$wmagnitud."',
									Datsub		=	'".$wsubdividir."',";
								if(isset($new_wcodigo))
				$q_actual.= "		Datcod		=	'".$new_wcodigo."',";
				$q_actual.= "		Seguridad	=	'C-".$wuse."'
							  WHERE	Datcod		= 	'".$wcodigo."'
							";
				mysql_query($q_actual,$conex) or die ("Error: ".mysql_errno()." - en el query(Actualizar indicador): ".$q_actual." - ".mysql_error());

				echo 'Dato actualizado|'.((isset($new_wcodigo)) ? $new_wcodigo : $wcodigo);
				break;
				return;
			}
			case 'ver_formulario':
			{
				pintar_formulario($codigo_dato);
				break;
				return;
			}
			case 'listar_datos':
			{
				pintar_lista('%', '', '', '', $codigo_dato);
				break;
				return;
			}
			case 'recargar_flotante_from':
			{
				desplegar_from($buscar, $origen);
				break;
				return;
			}
			case 'recargar_flotante_where':
			{
				desplegar_where_y_select($buscar, $tablas_from, $origen);
				break;
				return;
			}
			case 'recargar_flotante_select':
			{
				desplegar_where_y_select($buscar, $tablas_from, $origen);
				break;
				return;
			}
			case 'filtrar_resp':
			{
				$id_padre = str_replace('|', ' ', $id_padre);
				$options=responsable($id_padre);
				echo $options;
				break;
				return;
			}
			case 'validar_query':
			{
				$parametros_ya_pintados = array();
				calcular_dato($wquery, $parametros_valores, $wdsn, $param_agrupado, $tipo_temporal, $cod_dato, $campos_form, 'si');
				break;
				return;
			}
			case 'pintar_lista_subdivision':
			{
				pintar_lista_subdivision($where);
				break;
				return;
			}
			case 'filtrar_lista':
			{
				if($busc_codigo != '')
					$busc_dato = $busc_codigo;
				pintar_lista($busc_nombre, $busc_automa, $busc_tipo, $busc_estado, $busc_dato);
				break;
				return;
			}
			case 'EliminarDato':
			{
				if($codigoDato != '')
				{
					$q_borrar = " DELETE FROM ".$wbasedato."_000010
								   WHERE Datcod = '".$codigoDato."'
								";
					$res_borrar = mysql_query($q_borrar, $conex) or die ("Error: ".mysql_errno()." - en el query(Eliminar Dato): ".$q_borrar." - ".mysql_error());
					if($res_borrar > 0)
					{
						echo "Dato Eliminado";
					}
				}
				break;
				return;
			}
			case 'eliminar_temporales_creadas':
			{
				$tablas = explode(',', $from);
				foreach($tablas as $tabla)
				{
					eliminar_temporales_creadas(trim($tabla));
				}
				break;
				return;
			}
		}
	}
//=======================================================================================================================================================
//	Fin Filtros de llamados
//=======================================================================================================================================================
//=======================================================================================================================================================
//	Ejecucion normal del programa
//=======================================================================================================================================================
else
{

echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
echo "<input type='hidden' id='wuse' name='wuse' value='".$wuse."'>";

?>
<html>
<head>
	<title>MAESTRO VARIABLES</title>
	<!--<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />-->
</head>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>


<script type="text/javascript">

	function verSeccionCaracterizacion(id){
        $("#"+id).toggle("normal");
    }

	function pintar_filtros(id){
        $("#"+id).toggle("normal");
		ajustar_tamaño(500);
    }

	function iluminar_check(id_check){
		if($('#'+id_check).attr('checked') == 'checked')
		{
			$('#div_iluminar').attr('style', 'background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 20px; height:20px; align:center;');
			$('#tr_subdivicion').hide();
		}
		else
		{
			$('#div_iluminar').attr('style', '');
			$('#tr_subdivicion').show();
		}
	}
	//------------------------------------
	// Funcion que elimina un dato
	//------------------------------------
	function eliminar_dato(Codigo)
	{
		var mensaje = "";
		mensaje = "¿Está seguro que desea eliminar este dato?";
		if(confirm(mensaje))
		{
			$.post("Repositorio_datos.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'EliminarDato',
				codigoDato: 	Codigo
			}
			,function(data) {
				alert(data);
				filtrar_lista('');
			});
		}
	}
	//-----------------------------------------
	// Funcion para recargar los responsables
	//-----------------------------------------
	function recargarLista(id_padre, id_hijo, form)
	{
		var val = $("#"+id_padre.id).val();
		//Escapar espacios en blanco
		val = val.replace(" ","|");
		val = val.replace(" ","|");
		val = val.replace(" ","|");

		$('#'+id_hijo).load("Repositorio_datos.php?consultaAjax=&wemp_pmla="+$("#wemp_pmla").val()+"&accion=filtrar_resp&id_padre="+val+"&form="+form);
	}

	function pintar_formulario(codigo_dato, momento)
	{
		$.post("Repositorio_datos.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				accion:         		'ver_formulario',
				codigo_dato:			codigo_dato
			}
			,function(data) {
				if( momento == 'despues_de_grabar')
				{
					$("#Pintar_dato").html(data);
				}
				else
				{
					$("#Pintar_dato").hide();			//Ocultar Div
					$("#Pintar_dato").html(data);
					$("#Pintar_dato").show(600); 		//Animacion para que despliegue

				}

				//Validar que no inserten comilla sencilla en el campo 'select y from' de la creacion del query
				$("#campo_select").keyup(function(){
						if ($(this).val() !="")
						{
							$(this).val($(this).val().replace(/DELETE|UPDATE|TRUNCATE|DROP/gi, ""));
							$(this).val($(this).val().replace(/\'/g, '"'));
						}
					});

				$("#campo_from").keyup(function(){
						if ($(this).val() !="")
						{
							$(this).val($(this).val().replace(/DELETE|UPDATE|TRUNCATE|DROP/gi, ""));
							$(this).val($(this).val().replace(/\'/g, '"'));
						}
					});

				//Validar que no inserten comilla doble en el campo 'where' de la creacion del query
				$("#campo_where").keyup(function(){
						if ($(this).val() !="")
						{
							$(this).val($(this).val().replace(/DELETE|UPDATE|TRUNCATE|DROP/gi, ""));
							$(this).val($(this).val().replace(/\"/g, "'"));
						}
					});


				//Dejar en la lista inicial, solo el seleccionado
				if(codigo_dato != 'nuevo')
				{
					filtrar_lista(codigo_dato);
				}
				else
					ajustar_tamaño(200);

				//Ocultar zona automatica
				if($("#wautomatico").is(":checked"))
					ocultar_zona_aut('si');
				else
					ocultar_zona_aut('no');

				//Ocultar agrupar por
				if($('#tipo_temporal').attr('checked') == 'checked')
					$('#tr_subdivicion').hide();
			}
		);

	}
	function removerElemento(elemento)
	{
		$('#'+elemento).hide(600);
		filtrar_lista('');
	}

	function cerrar_test()
	{
		$('#div_calcular').html("<input type='submit' value='Test Query' onclick='test_query()' style='background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 2px solid #FFFF56; width: 80px; height:22px; align:center;'/>");

		// --> Eliminar las temporales que hayan quedado creadas
		/*$.post("Repositorio_datos.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				accion:         		'eliminar_temporales_creadas',
				from:					$('#campo_from').val()
			}
			,function(data) {
				console.log(data);
			});*/
	}
	//validar campos del formulario
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
		{														//sí existe valor entonces,
			$("#"+elemento.id).css("border","");				//quito el borde rojo
			$("#div_"+elemento.id).css("display", "none");		//oculto el mensaje
		}
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
			return guardar;
	}

	//================================================
	//	FUNCION GUARDA EL FORMULARIO EN LA BD
	//================================================
	function guardar(accion, wcodigo, momento)
    {
		var guardar='si';											//variable semaforo que me inidcara si se puede guardar o no

		guardar = validar_guardar('wnombre', guardar);				//validar que hayan escrito el nombre
		guardar = validar_guardar('wempresa', guardar);				//validar que hayan seleccionado la empresa
		guardar = validar_guardar('wmagnitud', guardar);			//validar que hayan seleccionado la magnitud

		//-------------------------------------------------------
		// Conocer si es automatico
		//-------------------------------------------------------
		var wautomatico = $("#wautomatico").attr('checked');
		if (wautomatico =='checked')
			wautomatico = 'on';
		else
			wautomatico = 'off';
		//--------------------------------------------------------

		if (wautomatico=='on')
		{
			//-------------------------------------------------------
			// Conocer el tipo (Calculado o constante)
			//-------------------------------------------------------
			var wtipo = $("#wtipo").attr('checked');
			if (wtipo =='checked')
				wtipo = 'off';
			else
				wtipo = 'on';
			//--------------------------------------------------------

			if(wtipo=='on')
			{
				guardar = validar_guardar('wvalor_cons', guardar);
				var wvalor = $('#wvalor_cons').val();
				var wdsn = '';
			}
			else
			{
				guardar = validar_guardar('wdsn', guardar);
				var wdsn = $('#wdsn').val();

				guardar = validar_guardar('campo_select', guardar);
				var campo_select = $('#campo_select').val();

				guardar = validar_guardar('campo_from', guardar);
				var campo_from = $('#campo_from').val();

				//guardar = validar_guardar('tag_condiciones', guardar);
				var campo_where = $('#campo_where').val();

				var wvalor = campo_select+'|'+campo_from+'|'+campo_where;
			}
		}
		else
		{
			var wtipo 	= '';
			var wvalor 	= '';
			var wdsn 	= '';
		}

		// --> Conocer el estado
		var estado = $("#westado").attr('checked');
		if (estado =='checked')
			estado = 'on';
		else
			estado = 'off';

		// --> Conocer si es tipo tabla temporal
		var tipo_temporal = $("#tipo_temporal").attr('checked');
		if (tipo_temporal == 'checked')
			tipo_temporal = 'on';
		else
			tipo_temporal = 'off';

		if (guardar=='si')
		{
			$.post("Repositorio_datos.php",
				{
					consultaAjax:   '',
					wemp_pmla:      $('#wemp_pmla').val(),
					wuse:           $('#wuse').val(),
					wbasedato:		$('#wbasedato').val(),
					accion:         accion,
					wcodigo:		wcodigo,
					wnombre:    	$('#wnombre').val(),
					wexplicacion: 	$('#wexplicacion').val(),
					wempresa:		$('#wempresa').val(),
					wmagnitud:		$('#wmagnitud').val(),
					wautomatico:	wautomatico,
					wdsn:			wdsn,
					wvalor: 		wvalor,
					wtipo_temporal:	tipo_temporal,
					wsubdividir:	(tipo_temporal == 'on') ? '' : $('#wsubdividir').val(),
					westado:		estado,
					wtipo:			wtipo
				}
				,function(data) {
					respuesta = data.split('|');
					alert(respuesta[0]);
					wcodigo = respuesta[1];
					$("#div_mensaje").css("display", "none");
					pintar_formulario(wcodigo, momento); //Actualizar el html de la ficha
				}
			);
		}
		else
		{
			$("#div_mensaje").text('DATOS INCOMPLETOS');
			$("#div_mensaje").css({"color":"red", "opacity":" 0.5","fontSize":"18px"});
			$("#div_mensaje").animate();
			$("#div_mensaje").show();
		}
    }
	//-----------------------------------------------------------------------------
	//Funcion que me ajusta el tamaño el div donde si listan los datos
	//-----------------------------------------------------------------------------
	function ajustar_tamaño(altura)
	{
		var altura_div = $("#div_lista").height();
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
	//-----------------------------------------------------------------------------
	//Funcion que me oculta o me muestra campos, dependiendo si es automatico o no
	//-----------------------------------------------------------------------------
	function ocultar_zona_aut(ocultar)
	{
        if(ocultar=='si')
		{
			$('tr[zona_aut=on]').show();

			if($("#wtipo").is(":checked"))
				ocultar_zona_cal('si');
			else
				ocultar_zona_cal('no');
		}
		else
			$('tr[zona_aut=on]').hide();
	}
	//-----------------------------------------------------------------------------
	//Funcion que me oculta o me muestra campos, dependiendo si es calculado o no
	//-----------------------------------------------------------------------------
	function ocultar_zona_cal(ocultar)
	{
		if(ocultar=='si')
		{
			$('tr[zona_cal=on]').show();
			$('tr[zona_cal=off]').hide();
			$('#tr_estado').attr("class", "Fila1");
		}
		else
		{
			$('tr[zona_cal=on]').hide();
			$('tr[zona_cal=off]').show();
			$('#tr_estado').attr("class", "Fila2");
		}
	}
	//---------------------------------------------------------------------------------------------------
	// Funcion que me pinta las cajas flotantes, segun una busqueda en las entradas de texto del query
	//---------------------------------------------------------------------------------------------------
	function desplegar_busqueda(ele, opcion, e )
	{
		var tecla = (document.all) ? e.keyCode : e.which;

		if(tecla==13)
		{
			var asignar_espacio = $('#campo_'+opcion).val()+' ';
			$('#campo_'+opcion).val(asignar_espacio);
		}
		/*
		if(tecla==40)
		{	primer_td = $( "div", "#caja_flotante").eq(0);
			primer_td.focus();
			$('#caja_flotante').show(500);
			console.log(tecla)
		}*/
		//Recargar la caja flotante
		$.post("Repositorio_datos.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'recargar_flotante_'+opcion,
				buscar: 		$('#campo_'+opcion).val(),
				tablas_from:	$('#campo_from').val(),
				origen:			opcion
			}
			,function(data) {

				$("#cont_caja_flotante").html(data);
				reasignarTamano();
			}
		);

		var elemento = $("#"+ele.id);
		var posicion = elemento.offset();
		var aumentar = $('#campo_'+opcion).height()+4;

		$('#caja_flotante').css({'left':posicion.left,'top':posicion.top+aumentar});
		$('#caja_flotante').show(500);
	}

	function reasignarTamano()
	{
		var height= $("#cont_caja_flotante table").height();
		if(height<=200)
			$("#cont_caja_flotante").height(height);
		else
			$("#cont_caja_flotante").height(200);
	}

	function agregar_tabla(campo, origen)
	{
		var valor_actual = $('#campo_'+origen).val();
		valor_actual = valor_actual.split(" ");
		var x;
		var valor_final='';

		for(x=0; x<valor_actual.length-1; x++)
		{
			valor_final+=valor_actual[x]+' ';
		}

		$('#campo_'+origen).val(valor_final+campo);
		$('#campo_'+origen).focus();
		$('#caja_flotante').hide(500);
	}
	function enterBuscar(hijo, e)
	{
		tecla = (document.all) ? e.keyCode : e.which;
		if(tecla==13) { $("#"+hijo).focus(); }
		else { return true; }
		return false;
	}
	function cambioImagen(img1, img2)
    {
		$('#'+img1).hide(1000);
        $('#'+img2).show(1000);
    }

	function test_query(parametros_valores)
	{
		$('#msj_calculando').show();
		var campo_select 	= $('#campo_select').val();
		var campo_from 		= $('#campo_from').val();
		var campo_where 	= $('#campo_where').val();
		var wvalor 			= campo_select+'|'+campo_from+'|'+campo_where;
		var wdsn   			= $('#wdsn').val();
		var tipo_temporal 	= ($("#tipo_temporal").attr('checked')) ? 'si' : 'no';
		var param_agrupado	= ($('#wsubdividir').val() != undefined ? $('#wsubdividir').val() : '');
		var cod_dato		= '';
		var campos_form   	= '';

		if($('#query_dato').val() != undefined)
		{
			var tipo_temporal 	= 'si';
			var campos_form		= wvalor+'<>'+wdsn+'<>'+tipo_temporal+'<>'+param_agrupado;
			var wvalor 			= $('#query_dato').val();
			var wdsn   			= $('#dsn_query_dato').val();
			var cod_dato		= $('#cod_dato').val();
		}

		$.post("Repositorio_datos.php",
			{
				consultaAjax:   	'',
				wemp_pmla:      	$('#wemp_pmla').val(),
				wuse:           	$('#wuse').val(),
				wbasedato:			$('#wbasedato').val(),
				accion:         	'validar_query',
				parametros_valores:	parametros_valores,
				tipo_temporal: 		tipo_temporal,
				param_agrupado: 	param_agrupado,
				wquery:				wvalor,
				wdsn:				wdsn,
				cod_dato:			cod_dato,
				campos_form:		campos_form
			}
			,function(data) {
				$("#div_calcular").html(data);
				$("#div_calcular").show(500);
				$('#msj_calculando').hide();
			}
		);

	}
	//---------------------------------------------------------------------------
	// Envio de parametros a la funcion medir_indicador para poder calcularlo
	//---------------------------------------------------------------------------
	function ejecutar_indicador()
	{
		$('#boton_calcular').hide();

		var tipo_parametro  = '';
		var nombre_parametr = '';
		var parametros_valores = '';
		var medir = 'si';
		var array_parametros = new Array()

		//Armar un array con el nombre de los parametros pintados y asignarles un no,
		//para poder comparar luego si a todos ya les han asignado algun valor, osea los han selecionado
		$('[rel=parametro]').each(
		function(index)
		{
			nombre_parametr= $(this).attr("name");
			array_parametros[nombre_parametr]='no';
		});


		//Recorro todos los parametros existentes, para conocer sus valores
		$('[rel=parametro]').each(
		function(index)
		{
			tipo_parametro = $(this).attr("tipo_cap");
			nombre_parametr= $(this).attr("name");
			switch(tipo_parametro)
			{
				case 'calendario':
				case 'seleccion':
				case 'texto':
				{
					valor_param = $(this).val();
					if(valor_param!='')
					{
						parametros_valores+= nombre_parametr+'|'+valor_param+'|-|';
						array_parametros[nombre_parametr]='si';
					}
					break
				}
				case 'checkbox':
				case 'radio':
				{
					if($(this).is(":checked"))
					{
						valor_param = $(this).val();
						parametros_valores+= nombre_parametr+'|'+valor_param+'|-|';
						array_parametros[nombre_parametr]='si';
					}
					break;
				}
			}
		});

		//Validar que hayan seleccionado todos los parametros
		var parametros_sin_seleccionar = '';
		for( var parametro in array_parametros)
		{
			if (array_parametros[parametro]=='no')
			{
				medir = 'no';
				parametros_sin_seleccionar+= parametro+', ';
			}
		}

		if(medir=='si')
			test_query(parametros_valores)
		else
		{
			$("#div_mensaje_validacion")
					.html('¡¡¡ Debe seleccionar todos los parametros !!! <br>Parametros sin seleccionar: '+parametros_sin_seleccionar)
					.css({"color":"red", "opacity":" 0.5","fontSize":"14px","border":"1px dotted red"})
					.show(500);
		}
	}
	//-----------------------------------------------------------------------------------
	// Funcion que me actualiza la lista de parametros en la opcion de 'Subdividir por:'
	//-----------------------------------------------------------------------------------
	function lista_subdividir()
	{
		var campo_where = $('#campo_where').val();
		$.post("Repositorio_datos.php",
			{
				consultaAjax:   	'',
				wemp_pmla:      	$('#wemp_pmla').val(),
				wuse:           	$('#wuse').val(),
				wbasedato:			$('#wbasedato').val(),
				accion:         	'pintar_lista_subdivision',
				where:				campo_where
			}
			,function(data) {
				$("#div_lista_subdividir").html(data);
				$("#div_lista_subdividir").show(500);
			}
		);
	}
	//-------------------------------------------------
	// Filtra la lista de principal de los parametros
	//-------------------------------------------------
	function filtrar_lista(codigo_dato)
	{
		var busc_codigo  	= $('#busc_codigo').val();
		var busc_nombre  	= $('#busc_nombre').val();
		var busc_automa 	= $('#busc_automa').val();
		var busc_tipo 		= $('#busc_tipo').val();
		var busc_estado  	= $('#busc_estado').val();

		$.post("Repositorio_datos.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'filtrar_lista',
				busc_codigo: 	busc_codigo,
				busc_nombre: 	busc_nombre,
				busc_automa:	busc_automa,
				busc_tipo:		busc_tipo,
				busc_estado:	busc_estado,
				busc_dato: 		codigo_dato
			}
			,function(data) {
				$('#div_lista').hide();
				$('#div_lista').html(data);
				$('#div_lista').show(0, function()
				{
					if(codigo_dato != '')
					{
						ajustar_tamaño(70);
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
				});
			}
		);
	}

	$(document).ready(function()
		{
			ajustar_tamaño(500);
		}
	);

</script>

<style type="text/css">
	.ui-autocomplete {
        max-height: 150px;
        overflow-y: auto;
        overflow-x: hidden;
    }
    * html .ui-autocomplete {
        height: 250px;
    }
    .displCaracterizacion{
        display:none;
    }
    .borderDiv {
        border: 1px solid #2A5DB0;
        padding: 5px;
    }
	.borderDiv2 {
        border: 2px solid #2A5DB0;
        padding: 15px;
    }
    .resalto{
        font-weight:bold;
		border-bottom:1px solid #FFFFFF;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
    .backgrd_seccion{
        background-color: #E4E4E4;
    }
     .carBold{
        font-weight:bold;
		padding:4px;
		border-bottom:1px solid #FFFFFF;
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
    }
	 #caja_flotante{
        position: absolute;
        border: 1px solid #CCC;
        background-color: #FFFFFF;
    }
	.BotonAmarrillo{
		background:#FFFFE1;
		color: #000000;
		cursor:pointer;
		font-size: 8pt;
		border: 1px solid #FFFF56;
		width: 100px;
		height:22px;
		align:center;
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
  $wactualiz='03-Marzo-2014';

// -->	Pintar el nombre de los indicadores o estadisticas en las que esta relacionado el dato
//		2014-03-17, Jerson trujillo
//=========================================================================================================================================\\
//
//
//=========================================================================================================================================\\
echo '
	<div id="caja_flotante" style="display:none;">
		<div id="cont_caja_flotante" style="border:solid 1px orange;background:none repeat scroll 0 0;height:200px;width:418px;overflow:auto;">';
echo'	</div>
	</div>';


//================================================================
//    ENCABEZADO
//================================================================
encabezado("Repositorio de datos", $wactualiz, 'clinica');
echo "	<div align='center'>
			<table width='81%' border='0' cellpadding='3' cellspacing='3'>
				<tr>
					<td align='left'>
						<div align='left' class='Titulo_azul'>
							REPOSITORIO DE DATOS
						</div>
						<div align='center' class='borderDiv2' id='div_contenedor'>
";
								listar_datos('');

echo "					</div>
						<div id='Pintar_dato'>
						</div>
					</td>
				</tr>
			</table>
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
