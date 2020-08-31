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
	
	//-------------------------------------------------
	//	Funcion que consulta los tipos de parametros
	//-------------------------------------------------
	function traer_tipos()
	{
		global $wbasedato;
		global $conex;
		$q =" SELECT Tcpcod, Tcpdes
			    FROM ".$wbasedato."_000013
			   WHERE Tcpest = 'on'
			   ORDER BY Tcpdes
			";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query(seleccionar tipos): ".$q." - ".mysql_error());
		
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
	//	Funcion que pinta el formulario 
	//--------------------------------------------
	function pintar_formulario($codigo_param)
	{
		global $conex;
		global $wbasedato;
		global $wfecha;
		global $whora;
		//=========================================
		// Consultar la informacion del parametro
		//=========================================
		if ($codigo_param != 'nuevo')
		{
			$q_datos = " 	SELECT A.* , Tcpref 
							  FROM ".$wbasedato."_000012 as A, ".$wbasedato."_000013
							 WHERE Parcod = '".$codigo_param."'
							   AND Partcp = Tcpcod
						";
			$res_datos = mysql_query($q_datos, $conex) or die (mysql_errno() . $q_datos . " - " . mysql_error());
			$row_informacion = mysql_fetch_array($res_datos);
		}
		if (isset($row_informacion))
			$wcodigo=$row_informacion['Parcod'];
		else
			$wcodigo='???';
			
		echo "	<br><br>
		<div name='ficha' id='ficha' align='left' class='borderDiv2' style='margin: 10px;'>
				<div align='right'>
				<div class='fila2' onclick='removerElemento(\"ficha\");' title='Cerrar ficha' style='width:110px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick=''>
								<b>Cerrar ficha</b>
								<img width='10' height='10' border='0' style='cursor:pointer;' title='Cerrar formulario' src='../../images/medical/eliminar1.png'>
				</div>
				</div>";
		echo"	<div class='borderDiv Titulo_azul' align=center>
				".((isset($row_informacion)) ? strtoupper(utf8_decode($row_informacion['Pardes'])): 'INGRESO DE NUEVO PARAMETRO' )."
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
			<table width="93%" border="0" align="center" cellspacing="0" cellpadding="0">';
		//	-->	Codigo
		echo 	"<tr class='fila1'>
					<td class='encabezadoTabla resalto' style='width :30%' >C&oacute;digo:&nbsp;</td>
					<td class='carBold' align=center>
						<div style='font-weight:bold;font-size:16px;color:red;' name='wcodigo' id='wcodigo'>".utf8_decode($wcodigo)."</div>
					</td>
				</tr>";
		//	-->	Nombre del parametro
		echo 	"<tr class='fila2'>
					<td class='encabezadoTabla resalto' >Nombre:&nbsp;</td>	
					<td align='center' class='carBold '>
						<input type='text' size=60 name='wnombre' id='wnombre' ".((isset($row_informacion)) ? 'value=\''.utf8_decode($row_informacion['Pardes']).'\'': '' )." onmouseover='validar(this);' onblur='validar(this)'>
						<div align='right' id='div_wnombre' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Tipo
		echo 	"<tr class='fila1'>
					<td class='encabezadoTabla resalto' >Tipo de captura:</td>	
					<td align='center' class='carBold'>
						<select style='width:265px;' name='wtipo' id='wtipo'  onmouseover='validar(this)' onblur='validar(this)' onchange='ocultar_zona_ref(\"\"); $(\"#div_pintar_captura\").html(\"\");'>";
							$array_tipos=traer_tipos();
							$check='';
							foreach($array_tipos as $wcod_tipo => $wnom_tipo)
							{
								if(isset($row_informacion))
								{
									(($row_informacion['Partcp']==$wcod_tipo) ? $check='selected' : $check='' );
								}
								echo "<option value='".$wcod_tipo."' ".$check.">".$wnom_tipo."</option>";
							}
		echo "			</select>
						<div align='right' id='div_wtipo' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Referenciado
				if(isset($row_informacion))
				{
					if($row_informacion['Tcpref']=='on')
					{
						$referenciado_valor = 'on';
					}
					else
					{
						$referenciado_valor = 'off';
					}
				}
				else
				{
					$referenciado_valor = 'off';
				}
		echo "<input type='hidden' name='wreferenciado_valor' id='wreferenciado_valor' value='".$referenciado_valor."'>";
		
		//	-->	Origen de datos
		echo 	"<tr class='fila2' id='tr_dsn' name='tr_dsn' zona_ref='on'>
					<td class='encabezadoTabla resalto' >Origen de datos <font size=1>(DSN)</font>:</td>	
					<td align='center' class='carBold'>
						<select style='width:265px;' name='wdsn' id='wdsn'>";
							$array_dns=traer_dsn();
							$check='';
							foreach($array_dns as $wcod_dns => $wnom_dns)
							{
								if(isset($row_informacion))
								{
									(($row_informacion['Pardsn']==$wcod_dns) ? $check='selected' : $check='' );
								}
								echo "<option value='".$wcod_dns."' ".$check.">".$wnom_dns."</option>";
							}
		echo "			</select>
						<div align='right' id='div_wdsn' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Valor (query) 
		echo 	"<tr class='fila1' id='tr_valor' name='tr_valor' zona_ref='on'>
					<td class='encabezadoTabla resalto' >Valor <font size=1>(Query)</font>: </td>	
					<td align='center' class='carBold'>";
						if(isset($row_informacion))
						{
							$valor_query = $row_informacion['Parcrv'].'|'.$row_informacion['Parcrm'].'|'.$row_informacion['Partab'].'|'.$row_informacion['Parcon'];
						}
						pintar_realizar_query($valor_query);
		echo	"	</td>
				</tr>";
		$style = "background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 1px solid #FFFF56; width: 130px; height:20px; align:center;display:inline;";
		//	-->	valor por defecto
		echo 	"<tr class='fila2' id='tr_val_defecto' name='tr_val_defecto'>
					<td class='encabezadoTabla resalto'>Valor por defecto:&nbsp;
					</td>	
					<td align='center' class='carBold'>
						<input type='submit' value='Asignar valor por defecto' onclick='cargar_captura()' style='".$style."'/><br><br>
						<div id='div_pintar_captura' >";
						if( isset($row_informacion) && $row_informacion['Parvde'] !='' )
							pintar_captura_valor_defecto ($row_informacion['Partcp'], $row_informacion['Pardsn'], $row_informacion['Partab'], $row_informacion['Parcrv'].', '.$row_informacion['Parcrm'], $row_informacion['Parcon'], $row_informacion['Parvde'], $row_informacion['Parfip']);
		echo 	"		</div>
					</td>
				</tr>";
		//	-->	Estado
				if(isset($row_informacion))
				{
					if($row_informacion['Parest']=='on')
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
		echo 	"<tr id='tr_estado' name='tr_estado' class='fila1'>
					<td class='encabezadoTabla resalto' > Estado:</td>	
					<td align='center' class='carBold'>
						Activo<input type='radio' name='westado' id='westado' value='on' $check_si >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        Inactivo<input type='radio' name='westado' id='westado' value='off' $check_no>
					</td>
				</tr>";
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
	
	//------------------------------------------------------------------------------
	//	Funcion que pinta la lista de parametros segun unos filtros (si existen)
	//------------------------------------------------------------------------------
	function pintar_lista($busc_nombre='%', $busc_tipcalc='', $busc_tip_cap='%', $busc_estado='', $codigo_parametro='')
	{
		global $wbasedato;
		global $conex;
	
		$q_consul_dat="	SELECT Parcod, Pardes, Tcpdes, Tcpref, Parest
						  FROM ".$wbasedato."_000012, ".$wbasedato."_000013
						 WHERE Pardes LIKE '%".$busc_nombre."%'
						   AND Partcp = Tcpcod";
		if($busc_estado == 'on')
		$q_consul_dat.="	   AND Parest = 'on'";
		elseif($busc_estado == 'off')
		$q_consul_dat.="	   AND Parest != 'on'";	
		
		if($busc_tipcalc == 'on')
		$q_consul_dat.="	   AND Tcpref = 'on'";
		elseif($busc_tipcalc == 'off')	
		$q_consul_dat.="	   AND Tcpref != 'on'";
		
		if($codigo_parametro != '')
		$q_consul_dat.="   	   AND Parcod = '".$codigo_parametro."'";
		
		$q_consul_dat.="	   AND Tcpcod LIKE '%".$busc_tip_cap."%' 
						ORDER BY Parcod
						";
		$res_consul_dat = mysql_query($q_consul_dat,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar datos): ".$q_consul_dat." - ".mysql_error());
		$num_datos = mysql_num_rows ($res_consul_dat);
		$fila_lista ="Fila2";
		
		$arr_param_esta_en_uso = array();
		$arr_param_esta_en_uso = parametros_en_uso();
		
		echo '<div style="color: #000000;font-size: 8pt;font-weight: bold;"> Registros: '.$num_datos.'</div>';
		echo "	
			<table width='100%'>
				<tr class='encabezadoTabla' align='center'>
					<td>C&oacute;digo</td><td>Nombre</td><td>Tipo captura</td><td>Calculo</td><td>Estado</td><td align='center' style='background-color: #FFFFFF;'></td>
				</tr>";
		
		
		if ($num_datos >0)
		{
			while ($row_datos = mysql_fetch_array($res_consul_dat))
			{
				if ($fila_lista=='Fila2')
					$fila_lista = "Fila1";
				else
					$fila_lista = "Fila2";
				
				if ($row_datos['Tcpref'] == 'on')
				{
					$origen_dato = "Referenciado";
				}
				else
				{
					$origen_dato = "Manual";
				}
				
				$par_nom = $row_datos['Pardes'];
				$onClick = "pintar_formulario(\"".utf8_decode($row_datos['Parcod'])."\")";
				echo "
					<tr class=".$fila_lista." style='cursor:pointer;'>
						<td onclick='".$onClick."' width=15%>".utf8_decode($row_datos['Parcod'])."</td>
						<td onclick='".$onClick."' >".utf8_decode($par_nom)."</td>
						<td onclick='".$onClick."' align='center'>".$row_datos['Tcpdes']."</td>
						<td onclick='".$onClick."' align='center' >".$origen_dato."</td>
						<td onclick='".$onClick."' align='center'>".(($row_datos['Parest']=='on') ? 'ACTIVO' : 'INACTIVO')."</td>";
					if(!array_key_exists($row_datos['Parcod'], $arr_param_esta_en_uso) && $codigo_parametro == '')
						echo"<td align='center' style='background-color: #FFFFFF;' onclick='eliminar_parametro(\"".$row_datos['Parcod']."\")'><img width='10' height='10' src='../../images/medical/eliminar1.png' title='Eliminar' style='cursor:pointer;'></td>";
					echo"
					</tr>";
			}
		}
		else
		{
			echo'	<tr class="fila2" >
						<td colspan=5 align=center><b>No se encontraron parametros.</b></td>
					</tr>';
		}
		echo '	</table>';
	}
	//-----------------------------------------------------------------------------
	//	Funcion para obetener una array con los parametros que estan siendo usados 
	//-----------------------------------------------------------------------------
	function parametros_en_uso()
	{
		global $wbasedato;
		global $conex;
		$arr_parametros_en_uso  = array();
		$arr_valores_datos		= array();
		
		// --> Consultar en las datos automaticos, que querys pueden tener algun parametro
		$q_lista = "SELECT Datval
					  FROM ".$wbasedato."_000010
					 WHERE Dataut = 'on'
					   AND Datval LIKE '%$%'
					";
		$res_lista = mysql_query($q_lista, $conex) or die ("Error: ".mysql_errno()." - en el query(Obtener lista de datos usados): ".$q_lista." - ".mysql_error());
		while($arr_lista = mysql_fetch_array($res_lista))
		{
			$arr_valores_datos[] = $arr_lista['Datval'];
		}
		
		foreach($arr_valores_datos as $datval)
		{
			$longitud = strlen($datval);
			$parametro_temp = '';
			
			// --> Obtener los codigos de los parametros que contiene el query
			for ($i=0; $i<=$longitud ; $i++)
			{
				if ($parametro_temp == '')
				{
					if ($datval{$i} == '$')	//Indica que es el inicio de un parametro
					{
						$parametro_temp.= $datval{$i};
					}
				}
				else
				{
					if($datval{$i} == "%" || $datval{$i} == "'")	//Indica que es el fin de un parametro
					{
						$posicion_fin = $i;
						//En este array voy guardando los parametros existentes
						$arr_parametros_en_uso[trim($parametro_temp)] = '';
						$parametro_temp='';
					}
					else
					{
						$parametro_temp.= $datval{$i}; //Aqui voy armando la variable del parametro
					}
				}	
			}
		}
		return $arr_parametros_en_uso;
	}
	//------------------------------------------------------------
	//	Funcion para consultar y listar los parametros existentes
	//------------------------------------------------------------
	function listar_datos($codigo_parametro)
	{
		global $wbasedato;
		global $conex;
		
		echo "	
			<table width='97%' border='0' align='center' cellspacing='1' cellpadding='2' name='lista_parametros' id='lista_parametros'>	
				<tr>
					<td colspan=5>
						<table width='100%'>
							<tr>
								<td width='13%' class='fila2' title='Filtrar lista de parametros' style='cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick='pintar_filtros(\"div_flitros\")'>
									<b>Filtrar lista</b>
									<img width='13' height='13' src='../../images/medical/HCE/lupa.PNG'>
								</td>
								<td width='74%'></td>
								<td width='13%' class='fila2' title='Agregar nuevo parametro' style='cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center onclick='pintar_formulario(\"nuevo\")'>
									<b>Nuevo</b>
									<img src='../../images/medical/HCE/mas.PNG'>
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
							<table width='60%' id='div_flitros' class='borderDiv' style='display:none'>
								<tr>
									<td align='center' align='center' class='parrafo_text' colspan='5'><b>Selección de filtros</b></td>
								</tr>
								<tr>
									<td align='center' class='Encabezadotabla'>Código:</td>
									<td align='center' class='Encabezadotabla'>Nombre:</td>
									<td align='center' class='Encabezadotabla'>Tipo calculo:</td>
									<td align='center' class='Encabezadotabla'>Tipo captura:</td>
									<td align='center' class='Encabezadotabla'>Estado:</td>
								</tr>
								<tr>
									<td align='center' class='Fila2'><input type='text' size=20 name='busc_codigo' id='busc_codigo' onBlur='filtrar_lista()' /></td>
									<td align='center' class='Fila2'><input type='text' size=38 name='busc_nombre' id='busc_nombre' onBlur='filtrar_lista()' /></td>
									<td align='center' class='Fila2'>
										<select name='busc_tipcalc' id='busc_tipcalc' onchange='filtrar_lista()'>
											<option value=''>Todas</option>
											<option value='on'>Referenciado</option>
											<option value='off'>Manual</option>
										</select>
									</td>
									<td align='center' class='Fila2'>
										<select name='busc_tip_cap' id='busc_tip_cap' onchange='filtrar_lista()'>
											<option value='%'>Todas</option>";
											//Consultar los tipos de captura
											$q_tipcap = "SELECT Tcpcod, Tcpdes
														   FROM ".$wbasedato."_000013
														  WHERE Tcpest = 'on'
													   ORDER BY Tcpdes
														";
											$res_tipcap = mysql_query($q_tipcap,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar tipos de captura): ".$q_tipcap." - ".mysql_error());
											while($row_tipcap = mysql_fetch_array($res_tipcap))
											{
												echo "<option value='".$row_tipcap['Tcpcod']."'>".$row_tipcap['Tcpdes']."</option>";
											}
		echo"							</select>
									</td>
									<td align='center' class='Fila2'>
										<select name='busc_estado' id='busc_estado' onchange='filtrar_lista()'>
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
						echo pintar_lista('%','','%','' ,$codigo_parametro);
		echo"			</div><br>
					</td>
				</tr>
			</table>";
	}
	
	//--------------------------------------------------------------------------------
	//	Funcion que pinta el input del valor por defecto segun el tipo de parametro
	//--------------------------------------------------------------------------------
	function pintar_captura_valor_defecto ($tipo_captura, $dsn, $from, $select, $where, $valor_defecto = '', $fecha_inicial_periodo = '')
	{
		global $wbasedato;
		global $conex;
		$q_inf_captura = "SELECT *
						    FROM ".$wbasedato."_000013
						   WHERE Tcpcod = '".$tipo_captura."'
					";
		$res_inf_captura = mysql_query($q_inf_captura, $conex) or die (mysql_errno() . $q_inf_captura . " (Query: Consultar informacion de la captura) " . mysql_error());
		while($row_inf_captura= mysql_fetch_array($res_inf_captura))
		{
			// Si es tipo calendario 
			if ($row_inf_captura['Tcpcal']=='on')
			{
				switch( $fecha_inicial_periodo)
				{
					case 'on':
					{	
						$check1 = 'checked';
						$check2 = '';
						$check3 = '';
						$display = 'none';
						break;
					}
					case 'off':
					{	
						$check1 = '';
						$check2 = 'checked';
						$check3 = '';
						$display = 'none';						
						break;
					}
					case '':
					{	
						$check1 = '';
						$check2 = '';
						if ($valor_defecto !='')
						{
							$fecha_defecto = $valor_defecto;
							$display = '';
							$check3 = 'checked';
						}
						else
						{
							$fecha_defecto = date('Y-m-d');
							$display = 'none';
						}
						break;
					}
					
				}
				echo "<table style='width:50%;color: #000000;font-size: 9pt;font-family: verdana;font-weight: bold;' name='cont_defecto_fecha' id='cont_defecto_fecha'>	
						<tr>
							<td><input type='radio' name='wval_defecto_fecha' id='wval_defecto_fecha' value='on' onClick ='$(\"#ver_zapatec\").hide(800);' ".$check1." ></td>
							<td>Fecha Inicial del periodo</td>
						</tr>
						<tr>
							<td><input type='radio' name='wval_defecto_fecha' id='wval_defecto_fecha' value='off' onClick ='$(\"#ver_zapatec\").hide(800);' ".$check2." ></td>
							<td>Fecha Final del periodo</td>
						</tr>
						<tr>
							<td><input type='radio' name='wval_defecto_fecha' id='wval_defecto_fecha' value='' onClick ='$(\"#ver_zapatec\").show(800);' ".$check3." ></td>
							<td>Otra</td>
						</tr>
						<tr style='display:".$display.";'  name='ver_zapatec' id='ver_zapatec'>
							<td colspan='2' align='center'>";
				echo			campoFechaDefecto('wval_defecto', $fecha_defecto);
				echo		"</td>
						</tr>
					</table>";
			}
			// Si es tipo campo de texto
			elseif ($row_inf_captura['Tcptex']=='on')
			{
				echo "<input type='text' size=18 name='wval_defecto' id='wval_defecto'".(($valor_defecto !='')? ' value='.$valor_defecto.'' : '' )." >";
			}
			else
			{
				//------------------------------------------
				// Armar y ejecutar el query del parametro
				//------------------------------------------
				
				//El select
				$query_parametro= 'SELECT '.str_replace('\\', '', $select);
				
				//El from
				$query_parametro.=' FROM '.$from;
				
				//El where
				if ($where !='' && $where !=' ')
					$query_parametro.=' WHERE '. str_replace('\\', '', $where);
				
				//Ejecuto el query dependiendo del tipo de conexion
				$tipo_matrix = '';
				$lista_resultados = array();
				$res_query_parametro = ejecutar_query($query_parametro, $dsn, $lista_resultados, 'parametro');
				
				//------------------------------------------
				// Fin Armar y ejecutar el query 
				//------------------------------------------
				
				if (count($lista_resultados) > 0)
				{
					// Si es tipo seleccion unica
					if($row_inf_captura['Tcpsel']=='on')
					{
						echo "<select style='width:265px;' name='wval_defecto' id='wval_defecto'>
								<option value='*' ".(($valor_defecto=='*') ? 'SELECTED' :'').">TODOS</option>";
								foreach($lista_resultados as $valor_lis => $nombre_lis)
								{
									echo "<option value='".$valor_lis."' ".(($valor_defecto==$valor_lis) ? 'SELECTED' : '' )." >".$nombre_lis."</option>";
								}
						echo "</select>";
					}
					// Si es tipo radio 
					elseif($row_inf_captura['Tcprad']=='on')
					{
						$x=2;
						echo "<table style='width:95%;color: #000000;font-size: 8pt;font-family: verdana;font-weight: bold;'>";
						
						if($valor_defecto !='' && $valor_defecto == '*')//Selecionaron todos
								$seleccion_defecto = 'CHECKED';
							else
								$seleccion_defecto = '';
						echo "<tr><td style='width:50%' NOWRAP><input type='radio' name='wval_defecto' id='wval_defecto' value='*' ".$seleccion_defecto." >TODOS</td>";
						
						foreach($lista_resultados as $valor_lis => $nombre_lis)
						{	
							if ($x == 2)
							{
								echo "<td style='width:50%' NOWRAP><input type='radio' name='wval_defecto' id='wval_defecto' value='".$valor_lis."' ".(($valor_defecto==$valor_lis) ? 'CHECKED' : '' )." >".$nombre_lis."</td></tr>";
								$x=1;
							}
							else
							{
								echo "<tr><td style='width:50%' NOWRAP><input type='radio' name='wval_defecto' id='wval_defecto' value='".$valor_lis."' ".(($valor_defecto==$valor_lis) ? 'CHECKED' : '' )." >".$nombre_lis." </td>";
								$x=2;
							}
						}
						echo "</table>";
					}
					elseif($row_inf_captura['Tcpche']=='on')
					{
						if($valor_defecto !='')
							$array_valor_defecto = explode(',', $valor_defecto); 
							
						$x=2;
						echo "<table style='width:95%;color: #000000;font-size: 8pt;font-family: verdana;font-weight: bold;'>";
						
						if($valor_defecto !='' && $valor_defecto == '*')//Selecionaron todos
								$seleccion_defecto = 'CHECKED';
							else
								$seleccion_defecto = '';
						echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' name='wval_defecto' id='wval_defecto' value='*' ".$seleccion_defecto." >TODOS</td>";
						
						foreach($lista_resultados as $valor_lis => $nombre_lis)
						{
							if($valor_defecto !='' && in_array($valor_lis, $array_valor_defecto))
								$seleccion_defecto = 'CHECKED';
							else
								$seleccion_defecto = '';
							
							if ($x == 2)
							{
								echo "<td style='width:50%' NOWRAP><input type='checkbox' name='wval_defecto' id='wval_defecto' value='".$valor_lis."' ".$seleccion_defecto." >".$nombre_lis."</td></tr>";
								$x=1;
							}
							else
							{
								echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' name='wval_defecto' id='wval_defecto' value='".$valor_lis."' ".$seleccion_defecto." >".$nombre_lis." </td>";
								$x=2;
							}
						}
						echo "</table>";
					
					}
				}
				else
					echo "Sin resultados...";
			}
			echo "<br><br><input type='submit' value='Cancelar valor por defecto' onClick ='$(\"#div_pintar_captura\").hide(800);' style='background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 1px solid #FFFF56; width: 130px; height:20px; align:center;display:inline;'/><br><br>";
		}
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
			$select1 	= $valores[0];
			$select2 	= $valores[1];
			$from 		= $valores[2];
			$where		= $valores[3];
		}
		else
		{
			$select1 	= '';
			$select2 	= '';
			$from 		= ''; 
			$where		= ''; 
		}
		//tag_tablas
		echo"<br>
		<table width='600px' style='color: #000000;font-size: 10pt;'>
			<tr>
				<td colspan='3' class='parrafo_text' align='center'>
					Creaci&oacute;n de query
				</td>
			</tr>
			<tr>
				<td align='right'>
				</td>
				<td style='color: #000000;font-family: verdana;font-size: 8pt;font-weight: bold;'>(Campo valor)</td><td style='color: #000000;font-family: verdana;font-size: 8pt;font-weight: bold;'>(Campo visualizaci&oacute;n)</td>
			</tr>
			<tr>
				<td align='right'>
					<b>SELECT</b>
				</td>
				<td colspan='2'>";
		echo'		<input id="campo_select1" style="width:208px;" onkeyup="desplegar_busqueda(this, \'select1\', event);" value="'.$select1.'" onmouseover="validar(this)" onblur="validar(this), $(\'#caja_flotante\').hide(500);" />';
		echo'		<input id="campo_select2" style="width:208px;" onkeyup="desplegar_busqueda(this, \'select2\', event);" value="'.$select2.'" onmouseover="validar(this)" onblur="validar(this), $(\'#caja_flotante\').hide(500);" />';
		echo"	</td>
			</tr>
			<tr>
				<td align='right'>
					<b>FROM</b>
				</td>
				<td align='left' colspan='2'>
					<input id='campo_from' style='width:420px;' value='".$from."' onkeyup='desplegar_busqueda(this, \"from\", event);' onmouseover='validar(this)' onblur='validar(this); $(\"#caja_flotante\").hide(500);' />
					<div align='right' id='div_campo_from' style='display:none;' ></div>
				</td>
			</tr>
			<tr>
				<td align='right' valign='top'>
					<b>WHERE</b>
				</td>
				<td colspan='2'>
					<textarea id='campo_where' rows='6' style='width:420px;' onkeyup='desplegar_busqueda(this, \"where\", event);'  onblur='$(\"#caja_flotante\").hide(500);'>".$where."</textarea>
				</td>
			</tr>
		</table><br>
		";
	}
	//--------------------------------------------------------------------------------------------------------
	//	Funcion que crea una tabla con el nombre de todas las tablas matrix y segun el parametro de busqueda 
	//--------------------------------------------------------------------------------------------------------
	function desplegar_from($buscar, $worigen)
	{
		global $wbasedato;
		global $conex;
		
		$buscar = explode(' ', $buscar);
		$ultimo = count($buscar);
		$buscar = explode('_', $buscar[$ultimo-1]);
		
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
		$listado_tablas=array();
		while ($row_tablas_matrix = mysql_fetch_array($res_tablas_matrix))
		{
			$tabla = $row_tablas_matrix['medico'].'_'.$row_tablas_matrix['codigo'];
			$listado_tablas[$tabla]= $row_tablas_matrix['nombre'];
		}
		echo'
		<table width="100%">';
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
			
			echo'<tr class="'.$color.'"  style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:8pt;cursor:pointer;">
					<td width="20%" onClick="agregar_tabla(\''.$campo.'\', \''.$worigen.'\' );">'.utf8_encode(htmlentities($campo)).'</td>
					<td width="80%" onClick="agregar_tabla(\''.$campo.'\', \''.$worigen.'\' );">'.utf8_encode(htmlentities($descripcion)).'</td>
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
			//Consultar Operadores basicos WHERE
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
			
			//Consultar Parametros
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
			//Consultar Operadores basicos SELECT
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
		
		//Consultar los campos correspondientes segun las tablas que han seleccionado y segun la busqueda
		$wtablas_from = str_replace(' ','', $wtablas_from);
		$row_tablas   = explode (',', $wtablas_from);
		foreach($row_tablas as $nom_tabla)
		{
			if ($nom_tabla !='')
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
		echo'
		</table>';
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
				$wcondiciones = str_replace("'", "\'", $wcondiciones);
				
				$q_guardar="	INSERT INTO ".$wbasedato."_000012
								( Medico, 		  Fecha_data,  	Hora_data, 		Parcod,			Pardes, 	 	Partcp, 	  Parvde, 				Parfip, 		  Pardsn,      Partab, 	   		Parcrv, 		Parcrm, 										Parcon,			Parest, Seguridad, id )
						VALUES  ('".$wbasedato."','".$wfecha."','".$whora."','".$wcodigo."','".$wnombre."','".$wtipo."','".$wval_defecto."','".$wtip_fec_defec."',	'".$wdsn."','".$wtabla."','".str_replace('\\', '', $wcam_valor)."','".str_replace('\\', '', $wcam_nombre)."','".$wcondiciones."','".$westado."',   'C-".$wuse."','' )
							";
				$res = mysql_query($q_guardar,$conex) or die ("Error: ".mysql_errno()." - en el query (Guardar variable): ".$q_guardar." - ".mysql_error());
				echo 'Parametro creado';
				break;
				return;
			}
			case 'Actualizar':
			{
				$wcondiciones = str_replace("'", "\'", $wcondiciones);
				
				$q_actual = "UPDATE ".$wbasedato."_000012 
								SET Fecha_data 	= 	'".$wfecha."' ,
									Hora_data	= 	'".$whora."',
									Parcod		=	'".$wnuevocodigo."',
									Pardes		=	'".$wnombre."',
									Partcp		=	'".$wtipo."', 
									Parvde		=	'".$wval_defecto."',
									Parfip		= 	'".$wtip_fec_defec."',
									Pardsn		=	'".$wdsn."',
									Partab		=	'".$wtabla."',
									Parcrv		=	'".$wcam_valor."',
									Parcrm		=	'".$wcam_nombre."',
									Parcon		=	'".$wcondiciones."',
									Parest		=	'".$westado."',  
									Seguridad	=	'C-".$wuse."'
							  WHERE	Parcod		= 	'".$wcodigo."'
							";
				mysql_query($q_actual,$conex) or die ("Error: ".mysql_errno()." - en el query(Actualizar indicador): ".$q_actual." - ".mysql_error());
				
				echo 'Dato actualizado';
				break;
				return;
			}
			case 'ver_formulario':
			{
				pintar_formulario($codigo_param);
				break;
				return;	
			}
			case 'listar_parametros':
			{
				listar_datos(((isset($codigo_parametro)) ? $codigo_parametro  : '' ));
				break;
				return;	
			}
			case 'tipo_captura_referenciada':
			{
				$q_tip_cap_ref = " SELECT Tcpref
									 FROM ".$wbasedato."_000013
									WHERE Tcpcod = '".$cod_tipo_captura."'
								";
				$res_tip_cap_ref = mysql_query($q_tip_cap_ref,$conex) or die ("Error: ".mysql_errno()." - en el query(Tipo catura refernciada): ".$q_tip_cap_ref." - ".mysql_error());
				$captura_refrerenciada = mysql_fetch_array ($res_tip_cap_ref);
				if ($captura_refrerenciada['Tcpref'] == 'on')
					echo "no";
				else
					echo "si";
				break;
				return;	
			}
			case 'cargar_captura':
			{
				pintar_captura_valor_defecto ($tipo_captura, $dsn, $from, $select, $where);
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
			case 'recargar_flotante_select1':
			{
				desplegar_where_y_select($buscar, $tablas_from, $origen);
				break;
				return;	
			}
			case 'recargar_flotante_select2':
			{
				desplegar_where_y_select($buscar, $tablas_from, $origen);
				break;
				return;	
			}
			case 'filtrar_lista':
			{
				pintar_lista($busc_nombre, $busc_tipcalc, $busc_tip_cap, $busc_estado, $busc_codigo);
				break;
				return;	
			}
			case 'EliminarParametro':
			{
				if($codigoParame != '')
				{
					$q_borrar = " DELETE FROM ".$wbasedato."_000012
								   WHERE Parcod = '".$codigoParame."'
								";
					$res_borrar = mysql_query($q_borrar, $conex) or die ("Error: ".mysql_errno()." - en el query(Eliminar parametro): ".$q_borrar." - ".mysql_error());
					if($res_borrar > 0)
					{
						echo "Parámetro Eliminado";
					}
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
		ajustar_tamaño(100);
    }
	//---------------------------------------
	// Funcion que me elimina un parametro
	//---------------------------------------
	function eliminar_parametro(Codigo)
	{
		var mensaje = "¿Está seguro que desea eliminar esta parámetro?";
		if(confirm(mensaje))
		{	
			$.post("Repositorio_parametros.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'EliminarParametro', 
				codigoParame: 	Codigo
			}
			,function(data) {
				alert(data);
				filtrar_lista();
			});
		}
	}
	function ocultar_zona_ref(ocultar)
	{
        if (ocultar=='')
		{
			$.post("Repositorio_parametros.php",
				{
					consultaAjax:   		'',
					wemp_pmla:      		$('#wemp_pmla').val(),
					wuse:           		$('#wuse').val(),
					wbasedato:				$('#wbasedato').val(),
					accion:         		'tipo_captura_referenciada',
					cod_tipo_captura:		$('#wtipo').val()
				}
				,function(data) {
					ocultar = data;
					if(ocultar=='si')
					{
						$('tr[zona_ref=on]').hide();
					}
					else
					{
						$('tr[zona_ref=on]').show();
						if ($('#tr_val_defecto').attr("class") == "Fila1")	
						{	
							$('#tr_val_defecto').attr("class", "Fila2");
							$('#tr_estado').attr("class", "Fila1");
						}
					}
				}
			);
		}
		else
		{
			if(ocultar=='si')
			{
				$('tr[zona_ref=on]').hide();
				$('#tr_val_defecto').attr("class", "Fila2");
				$('#tr_estado').attr("class", "Fila1");
			}
			else
			{
				$('tr[zona_ref=on]').show();
			}
		}
	}
	
	function pintar_formulario(codigo_param, momento)
	{
		$.post("Repositorio_parametros.php",
				{
					consultaAjax:   		'',
					wemp_pmla:      		$('#wemp_pmla').val(),
					wuse:           		$('#wuse').val(),
					wbasedato:				$('#wbasedato').val(),
					accion:         		'ver_formulario',
					codigo_param:			codigo_param
				}
				,function(data) {
					if( momento == 'despues_de_grabar')
					{
						$("#Pintar_parametro").html(data);	
					}
					else
					{
						$("#Pintar_parametro").hide();			//Ocultar Div
						$("#Pintar_parametro").html(data);		
						$("#Pintar_parametro").show(600); //Animacion para que despliegue

					}
					//Validar que no inserten comilla sencilla en el campo 'select y from' de la creacion del query 
					$("#campo_select1").keyup(function(){
							if ($(this).val() !="")	
							{
								$(this).val($(this).val().replace(/DELETE|UPDATE|TRUNCATE|DROP/gi, ""));
								$(this).val($(this).val().replace(/\'/g, '"'));
							}
						});
					//Validar que no inserten comilla sencilla en el campo 'select y from' de la creacion del query 
					$("#campo_select2").keyup(function(){
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
					$("#campo_where").keyup(function(event){
							if ($(this).val() !="")	
							{
								$(this).val($(this).val().replace(/DELETE|UPDATE|TRUNCATE|DROP/gi, ""));
								$(this).val($(this).val().replace(/\"/g, "'"));
							}
						});
					
					//Dejar en la lista inicial, solo el seleccionado
					if(codigo_param != 'nuevo')
						actualizar_lista_parametros(codigo_param);
					
					if ($("#wreferenciado_valor").val()=="off")
					{
						ocultar_zona_ref('si');
					}
				}
			);
	}
	
	function removerElemento(elemento)
	{
		$('#'+elemento).hide(600);
		actualizar_lista_parametros('');
	}
	
	//--------------------------------
	//validar campos del formulario
	//--------------------------------
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
	
	//--------------------------------
	// validar campos del formulario
	//--------------------------------
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
	
	function marcar_obligatorio(elemento)
	{
		$("#div_"+elemento)
				.text(' * Campo Obligatorio')
				.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
				.show();
	}
	function actualizar_lista_parametros(codigo_parametro)
	{
		$.post("Repositorio_parametros.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'listar_parametros',
				codigo_parametro: codigo_parametro
			}
			,function(data) {
			
				$("#div_contenedor").html(data);
				ajustar_tamaño(0);
			}
		);
	}
	
	//----------------------------------
	// Funcion que guarda el formulario
	//----------------------------------
	function guardar(accion, wcodigo, momento)
    {
		var guardar='si';											//variable semaforo que me inidcara si se puede guardar o no
		var condiciones='';
		var tablas='';
		var wreferenciado;
		
		//Organizar codigo
		var nombre_par = '$'+$('#wnombre').val();
		var wnuevocodigo = nombre_par.split(" ").join("_");
		
		if(wcodigo=='???')
			wcodigo = wnuevocodigo;
		//Fin Organizar codigo
		
		guardar = validar_guardar('wnombre', guardar);				//validar que hayan escrito el nombre
		guardar = validar_guardar('wtipo', guardar);				//validar que hayan seleccionado el tipo

		
		// Obtener el valor por defecto
		var wval_defecto = ''; 
		var wtipo_defecto_fecha = '';
		// Si es diferente de radio y de checkbox
		if($('#wval_defecto').attr('type') != "checkbox" && $('#wval_defecto').attr('type') != "radio")
		{
			wval_defecto = $('#wval_defecto').val();
		}
		// Si es tipo radio o checkbox
		else
		{
			var inputs = document.getElementById('div_pintar_captura').getElementsByTagName( 'input' ); //Array que contiene todos los inputs pintados
			for (var x=0; x < inputs.length; x++)
			{
				if ((inputs[x].type == "checkbox" || inputs[x].type == "radio") && inputs[x].checked) 
				{
					if(wval_defecto=='')
							wval_defecto = inputs[x].value;
					else
						wval_defecto = wval_defecto+","+inputs[x].value;
				}
			}
		}
		
		// --> Conocer el tipo de 'fecha defecto'
		if(document.getElementById('cont_defecto_fecha') != null)
		{
			var inputs = document.getElementById('cont_defecto_fecha').getElementsByTagName( 'input' ); //Array que contiene todos los inputs pintados
			for (var x=0; x < inputs.length; x++)
			{
				if (inputs[x].type == "radio" && inputs[x].checked) 
				{		
					wtipo_defecto_fecha = inputs[x].value;
					if(wtipo_defecto_fecha != '')
						wval_defecto="0000-00-00";
				}
			}
		}

		// Obtener si es referenciado
		$.post("Repositorio_parametros.php",
		{
			consultaAjax:   		'',
			wemp_pmla:      		$('#wemp_pmla').val(),
			wuse:           		$('#wuse').val(),
			wbasedato:				$('#wbasedato').val(),
			accion:         		'tipo_captura_referenciada',
			cod_tipo_captura:		$('#wtipo').val()
		}
		,function(data) {
			if (data=='no')
				wreferenciado = 'on';
			else
				wreferenciado = 'off';
			
			// Obtener el estado
			var estado = $("#westado").attr('checked');	
			if (estado =='checked')
				estado = 'on';
			else
				estado = 'off';

				
			//Solo si el parametro es referenciado valido lo siguiente (query).
			if(wreferenciado == 'on') 
			{
				guardar = validar_guardar('wdsn', guardar);				//validar que hayan seleccionado el dsn
				guardar = validar_guardar('campo_from', guardar);	 	//validar que hayan seleccionado tablas	
				guardar = validar_guardar('campo_select1', guardar);	//validar que hayan seleccionado el campo valor
				guardar = validar_guardar('campo_select2', guardar);	//validar que hayan seleccionado el campo nombre
				
				dsn = $('#wdsn').val();
				tablas = $('#campo_from').val();
				cam_valor = $('#campo_select1').val();
				cam_nombre = $('#campo_select2').val();
				condiciones = $('#campo_where').val();
			}
			else
			{
				dsn = '';
				tablas = '';
				cam_valor = '';
				cam_nombre = '';
				condiciones = '';		
			}
			
			//Guardo el parametro
			if (guardar=='si')
			{
				$.post("Repositorio_parametros.php",
					{
						consultaAjax:   '',
						wemp_pmla:      $('#wemp_pmla').val(),
						wuse:           $('#wuse').val(),
						wbasedato:		$('#wbasedato').val(),
						accion:         accion,
						wcodigo:		wcodigo,
						wnuevocodigo:	wnuevocodigo,
						wnombre:    	$('#wnombre').val(),
						wtipo: 			$('#wtipo').val(),
						wval_defecto:	wval_defecto,
						wtip_fec_defec:	wtipo_defecto_fecha,
						wdsn:			dsn,
						wtabla: 		tablas,
						wcam_valor:		cam_valor,
						wcam_nombre:	cam_nombre,
						wcondiciones:	condiciones,
						westado:		estado
					}
					,function(data) {
						alert(data);
						$("#div_mensaje").css("display", "none");			
						pintar_formulario(wnuevocodigo, momento); //Actualizar el html de la ficha
						actualizar_lista_parametros();
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
		);
    }	
	//-------------------------------------------------------------------
	// Funcion que me pinta el elemento para obtener el valor por defeto
	//-------------------------------------------------------------------
	function cargar_captura()
	{
		$.post("Repositorio_parametros.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'cargar_captura',	
				tipo_captura:	$('#wtipo').val(),
				dsn:			$('#wdsn').val(),
				from:			$('#campo_from').val(),
				select:			$('#campo_select1').val()+', '+$('#campo_select2').val(),
				where:			$('#campo_where').val()
			}
			,function(data) {
				$('#div_pintar_captura').html(data);
				$("#div_pintar_captura").show(800);
			}
		);
	}
	function cambioImagen(img1, img2)
    {
		$('#'+img1).hide(1000);
        $('#'+img2).show(1000);
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
		$.post("Repositorio_parametros.php",
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
	//-------------------------------------------------
	// Filtra la lista de principal de los parametros
	//-------------------------------------------------
	function filtrar_lista()
	{
		var busc_codigo  = $('#busc_codigo').val();
		var busc_nombre  = $('#busc_nombre').val();
		var busc_tipcalc = $('#busc_tipcalc').val();
		var busc_tip_cap = $('#busc_tip_cap').val();
		var busc_estado  = $('#busc_estado').val();
		
		$.post("Repositorio_parametros.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'filtrar_lista', 
				busc_codigo: 	busc_codigo,
				busc_nombre: 	busc_nombre,
				busc_tipcalc:	busc_tipcalc,
				busc_tip_cap:	busc_tip_cap,
				busc_estado:	busc_estado
			}
			,function(data) {
				$('#div_lista').hide();
				$('#div_lista').html(data);
				$('#div_lista').show(0, function() 
					{
						ajustar_tamaño(100);
					}
				);
			}
		);
	}
	//-----------------------------------------------------------------------------
	//Funcion que me ajusta el tamaño el div donde si listan los datos
	//-----------------------------------------------------------------------------
	function ajustar_tamaño(aumento)
	{
		var altura_div = $("#div_lista").height();
		//alert(altura_div);
		if(altura_div > 500)
		{
			$('#div_contenedor').css(
				{
					'height': 500+aumento, 
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
		else
		{
			$('#div_contenedor').css(
				{
					'height': altura_div+80+aumento
				}
			);
		}
	}
	$(document).ready(function()
		{		
			ajustar_tamaño(0);
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
	.parrafo_text{
		background-color: #666666;
		color: #FFFFFF;
		font-family: verdana;
		font-size: 10pt;
		font-weight: bold;
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
//                  REPOSITORIO DE PARAMETROS
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:
//FECHA DE CREACION: 
//=========================================================================================================================================\\
//                  ACTUALIZACIONES                                                                                                                          \\
  $wactualiz='12-Junio-2013';
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
encabezado("Repositorio de parametros", $wactualiz, 'clinica');
echo "	<div align='center'>
			<table width='81%' border='0' cellpadding='3' cellspacing='3'>
				<tr>
					<td align='left'>
						<div align='left' class='Titulo_azul'>
							REPOSITORIO DE PARAMETROS
						</div>
						<div align='center' id='div_contenedor' class='borderDiv2' >
							";
								listar_datos('');
echo "					</div>
						<div id='Pintar_parametro'>
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
