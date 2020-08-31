<?php
include_once("conex.php");
//=======================================================================================================
//	FUNCIONES GENERALES PARA EL SISTEMA DE GESTION DE LA CALIDAD (SGC)
//=======================================================================================================
//	Autor:						Jerson andres trujillo
//	Fecha creacion : 			04 de Diciembre 2012
// 	Fecha de modificacion:		23 de mayo 2012
//	
//=======================================================================================================
//	ACTUALIZACIONES
//=======================================================================================================
//
// 10-09-2019 (Jerson Trujillo): Se corrige incosistencia de migracion, manejo de decimales 
// 21-05-2013 (Jerson Trujillo): Se agrego funcionalidad para soportar querys temporales
//=======================================================================================================

//-------------------------------------------------------------------------------------------------------------
//	FUNCION que retorna todos los valores de un parametro
//-------------------------------------------------------------------------------------------------------------
function valores_parametro($codigo)
{
	global $wbasedato;
	global $conex;
	
	//Consulto el query del parametro.
	$q_parametro = ' SELECT Parcrv, Parcrm, Partab, Parcon, Pardsn
					   FROM '.$wbasedato.'_000012
					  WHERE Parcod = "'.trim($codigo).'"
					';
	$res_parametro = mysql_query($q_parametro,$conex) or die ("Error: ".mysql_errno()." - en el query(Consultar query del parametro): ".$q_parametro." - ".mysql_error());
	$row_parametro = mysql_fetch_array($res_parametro);
	
	//Armo la estructura del query
	$query_param = 'SELECT '.$row_parametro['Parcrv'].', '.$row_parametro['Parcrm'];
	$query_param.= '  FROM '.$row_parametro['Partab'];
	
	if($row_parametro['Parcon'] != '')
		$query_param.= ' WHERE '.$row_parametro['Parcon'];
	
	//Ejecuto el query para obtener sus valores
	$resultado_parametro = array();
	ejecutar_query($query_param, $row_parametro['Pardsn'], $resultado_parametro, 'parametro');
	
	return $resultado_parametro;
}
//-----------------------------------------------------------------------------
//	Funcion que inserta un IN mysql con un serie de valores, en una comparacion
//  ya existente en el where de un query.
//-----------------------------------------------------------------------------
function insertar_comparacion_in($cod_parametro, $valores_in, $where)
{
	// --> Armar la nueva cadena de comparacion a insertar
	$cadena_insertar = ' IN (';
	foreach ($valores_in as $valor_comparar)
	{
		$valor_comparar = trim($valor_comparar);
		if($valor_comparar !='')
		{
			if($cadena_insertar == ' IN (')
				$cadena_insertar.= "'".$valor_comparar."'";
			else
				$cadena_insertar.= ", '".$valor_comparar."'";
		}
	}
	$cadena_insertar.= ')';
	
	// --> Estas son algunas de las posibilidades de como puede estar
	// 	   creada la condicion del parametro, para poder reemplazarla por el IN()
	$cadena_buscar_opc_1 = "='".$cod_parametro."'";
	$cadena_buscar_opc_2 = "= '".$cod_parametro."'";
	$cadena_buscar_opc_3 = "=  '".$cod_parametro."'";
	$cadena_buscar_opc_4 = "=' ".$cod_parametro."'";
	$cadena_buscar_opc_5 = "= ' ".$cod_parametro."'";
	$cadena_buscar_opc_6 = "=  ' ".$cod_parametro."'";
	
	// --> Solo se entra a una de estas opciones, dependiendo de la cadena que encuentre en el where.
	if(strpos($where, $cadena_buscar_opc_1) !== false)
		$where = str_replace($cadena_buscar_opc_1, $cadena_insertar, $where);
	elseif(strpos($where, $cadena_buscar_opc_2) !== false)
			$where = str_replace($cadena_buscar_opc_2, $cadena_insertar, $where);
		elseif(strpos($where, $cadena_buscar_opc_3) !== false)
				$where = str_replace($cadena_buscar_opc_3, $cadena_insertar, $where);
			elseif(strpos($where, $cadena_buscar_opc_4) !== false)
					$where = str_replace($cadena_buscar_opc_4, $cadena_insertar, $where);
				elseif(strpos($where, $cadena_buscar_opc_5) !== false)
						$where = str_replace($cadena_buscar_opc_5, $cadena_insertar, $where);
					elseif(strpos($where, $cadena_buscar_opc_6) !== false)
						$where = str_replace($cadena_buscar_opc_6, $cadena_insertar, $where);
	
	return $where;
}
//-----------------------------------------------------------------------------
//	Funcion que le da formato a un resultado de un dato o indicador 
//  dependiendo del parametro de numero de decimles. 
//-----------------------------------------------------------------------------
function formato_respuesta($resultado, $num_decimales='2')
{
	$resultado = (float)$resultado;
	if($resultado != '')
	{	
		$resultado = number_format($resultado, $num_decimales, ',', '.');
			
		if(strstr($resultado, '.'))
		{
			$row_resultado = explode(',', $resultado);
			$resultado = $row_resultado[0];
		}
		else
		{
			$row_resultado = explode(',', $resultado);
			if($row_resultado[1]=='00')
			{
				$resultado = $row_resultado[0];
			}
		}
	}
	else
		$resultado = 0;
	
	return $resultado;
}
//-----------------------------------------------------------
//	FUNCION QUE BUSCA SI UNA TABLA ES TEMPORAL Y LA EJECUTA	
//-----------------------------------------------------------
function ejecutar_tabla_temporal($tabla, &$query_temporal, &$dsn_temporal)
{
	global $conex;
	global $wbasedato;
	
	// --> Consultar si una tabla es temporal, en el maestro de los datos
	$q_tabla_temp = " SELECT Datval, Datdsn, Dsnunx, Dsnnom 
						FROM ".$wbasedato."_000010, ".$wbasedato."_000011
					   WHERE Datcod = '".$tabla."'
						 AND Dattte = 'on'
						 AND Datdsn = Dsncod
					";
	$res_tabla_temp = mysql_query($q_tabla_temp, $conex) or die (mysql_errno() . $q_tabla_temp . " (Query: Consultar informacion si existe tabla temporal) " . mysql_error());
	if(mysql_num_rows($res_tabla_temp) > 0)
	{
		$arr_tabla_temp = mysql_fetch_array($res_tabla_temp);
		$query_temporal = $arr_tabla_temp['Datval'];
		$dsn_temporal 	= $arr_tabla_temp['Datdsn'];
		
		// --> Consultar si la tabla temporal ya fue creada
		$q_temp_existe = "SELECT * 
							FROM ".$tabla."
						";
		if($arr_tabla_temp['Dsnunx'] == 'on')
		{
			$conexunix = @odbc_connect($arr_tabla_temp['Dsnnom'],'informix','sco');
			$res_temp_existe = @odbc_exec($conexunix, $q_temp_existe);
		}
		else
			$res_temp_existe = mysql_query($q_temp_existe, $conex);
		
		if(!$res_temp_existe)
			return true;
		else
			return false;
	}
	else
	{
		return false;
	}
}
//---------------------------------------------------------------------------------------
//	Funcion que borra una tabla temporal sin inportar si es de unix o matrix 
//---------------------------------------------------------------------------------------
function borrar_tabla_temporal($dsn, $nombre_temporal)
{
	global $wbasedato;
	global $conex;
	$q_tipo_conex = " SELECT Dsnunx, Dsnnom
						FROM ".$wbasedato."_000011
					   WHERE Dsncod = '".$dsn."'
					";
	$res_tipo_conex = mysql_query($q_tipo_conex,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar tipo de conexion): ".$q_tipo_conex." - ".mysql_error());
	$row_tipo_conex = mysql_fetch_array($res_tipo_conex);
	
	if ($row_tipo_conex['Dsnunx'] == 'on')
	{
		$tipo_unix = 'on';
		$conexunix = @odbc_connect($row_tipo_conex['Dsnnom'],'informix','sco');
		$query = "DROP TABLE ".$nombre_temporal;
		$res = @odbc_exec($conexunix,$query);
	}
	else
	{
		$tipo_unix = 'off';
		$query = "DROP TABLE IF EXISTS ".$nombre_temporal;
		$res = mysql_query($query, $conex);
	}
	return $tipo_unix;
}
//---------------------------------------------------------------------------------------
//	Funcion que ejecuta un query sin inportar si es unix o matrix y retorna la respuesta 
//---------------------------------------------------------------------------------------
function ejecutar_query($query, $dsn, &$lista_resultados, $tipo_res, $hacer_agrupacion='NO')
{
	global $wbasedato;
	global $conex;
	$q_tipo_conex = " SELECT Dsnunx, Dsnnom
						FROM ".$wbasedato."_000011
					   WHERE Dsncod = '".$dsn."'
					";
	$res_tipo_conex = mysql_query($q_tipo_conex,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar tipo de conexion): ".$q_tipo_conex." - ".mysql_error());
	$row_tipo_conex = mysql_fetch_array($res_tipo_conex);
	
	if ($row_tipo_conex['Dsnunx'] == 'on')
	{
		$conexunix = @odbc_connect($row_tipo_conex['Dsnnom'],'informix','sco');
		$res = @odbc_exec($conexunix,$query);
		$tipo_matrix = 'off';
	}
	else
	{
		$res = mysql_query($query, $conex);
		$tipo_matrix = 'on';
	}
	
	$res_query_parametro = $res;
	if (!$res_query_parametro)
	{	echo "
		<br>
		<table style='opacity:0.9;width:480px;border: 1px solid #2A5DB0;padding: 5px;background-color: #FFFFCC;color: #000000;font-size: 9pt;'>
			<tr>
				<td colspan=2 align=center>
					<BLINK><img style='cursor:pointer' width='15' height='15' src='../../images/medical/root/info.png' onClick='$(\"[mensaje_error=si]\").toggle(300);'/></BLINK>
					<b>&nbsp;El cálculo no se puede realizar en este momento.</b><br>Por favor comunicarse informática.<br>
				</td>
			</tr>
			<tr mensaje_error='si' style='display:none'>
				<td style='border: 1px solid #999999;' colspan='2' align='center'><b>Información del error</b></td>
			</tr>
			<tr mensaje_error='si' style='display:none'>
				<td><b>Consulta:</b></td>
				<td style='border: 1px solid #999999;'>".$query."</td></tr>";
		if ($tipo_matrix=='on')
		{
			echo "
			<tr mensaje_error='si' style='display:none'> 
				<td><b>Mensaje de error:</b></td>
				<td style='border: 1px solid #999999;'>" . mysql_error()."</td>
			</tr>";
		}
		else
		{
			echo "
			<tr mensaje_error='si' style='display:none'>
				<td><b>Mensaje de error:</b></td>
				<td style='border: 1px solid #999999;'>" . odbc_errormsg()."</td>
			</tr>";
		}
		echo "</table><br>";
	}
	else
	{
		// --> Armar resultados a devolver para un query tipo matrix y segun el tipo de resultado deseado.
		if($tipo_matrix=='on')
		{
			if(@mysql_num_rows($res_query_parametro) > 0)
			{
				switch($tipo_res)
				{
					// --> Resultados para un parametro
					case 'parametro':
					{
						while($row_parametro = @mysql_fetch_array($res_query_parametro))
						{
							$lista_resultados[$row_parametro[0]] = $row_parametro[1];
						}
						break;
					}
					// --> Resultados para un dato 
					case 'dato':
					{
						while($row_dato = @mysql_fetch_array($res_query_parametro))
						{
							if(mysql_num_rows($res_query_parametro) > 1)
							{
								// --> Indica que es el query de un dato, pero con la suma de todos sus resultados
								if($hacer_agrupacion == 'NO')
									$lista_resultados[0]+= $row_dato[0]; 	
								// --> Es el query de un dato pero con mas de un campo en el select y con un group by, entonces se deben retornar varios resultados
								else
									$lista_resultados[$row_dato[1]] = $row_dato[0]; 
							}
							// --> Indica que es el query de un dato, pero con un solo resultado
							else
								$lista_resultados[0] = $row_dato[0]; 	
						}
						break;
					}
					// --> Resultados para el test de un dato, se devuelven todos los resultados del query con sus respectivos campos 
					case 'test':
					{
						// --> Nombre de los campos 
						for($i=0; $i < mysql_num_fields($res_query_parametro); $i++)
						{
							$array_nom[$i] = '<b>'.mysql_field_name($res_query_parametro, $i).'</b>';
						}
						$lista_resultados[] = $array_nom;
						
						// --> Resultado
						while($row_dato = @mysql_fetch_array($res_query_parametro))
						{							
							for($i=0; $i < mysql_num_fields($res_query_parametro); $i++)
							{
								// --> valores
								$array_val[$i] = $row_dato[$i];
							}
							$lista_resultados[] = $array_val;							
						}
						break;
					}
				}
			}
			else
			{
				// --> Resultados para un crear una tabla temporal
				if( $tipo_res == 'temporal')
				{
					if($res_query_parametro == 1)
						$lista_resultados['0'] = 'TRUE';
					else
						$lista_resultados['0'] = 'FALSE';
				}
				else
					$lista_resultados['0'] = '0';
			}	
		}
		// --> Armar resultados a devolver para un query tipo unix.
		else
		{
			$hay_resultados = 'no';
			switch($tipo_res)
			{
				// --> Resultados para un parametro
				case 'parametro':
				{
					while(odbc_fetch_row($res_query_parametro))
					{
						$hay_resultados = 'si';
						$inidce = odbc_result($res_query_parametro, 1);
						$valor 	= odbc_result($res_query_parametro, 2);
						$lista_resultados[$inidce] = $valor;
					}
					break;
				}
				// --> Resultados para un crear una tabla temporal
				case 'temporal':
				{
					$hay_resultados = 'si';
					if ($res_query_parametro)
						$lista_resultados['0'] = 'TRUE';
					else
						$lista_resultados['0'] = 'FALSE';
					break;
				}
				// --> Resultados para un dato 
				case 'dato':
				{
					while(odbc_fetch_row($res_query_parametro))
					{
						$hay_resultados = 'si';
						$inidce 		= odbc_result($res_query_parametro, 1);
						// --> Indica que es el query de un dato, pero con la suma de todos sus resultados
						if($hacer_agrupacion == 'NO')
							$lista_resultados[0]+= $inidce;
						// --> Es el query de un dato pero con mas de un campo en el select y con un group by, entonces se deben retornar varios resultados
						else
						{
							$valor 	= odbc_result($res_query_parametro, 2);
							$lista_resultados[$valor] = $inidce;		
						}
					}
					break;
				}
				// --> Resultados para el test de un dato, se devuelven todos los resultados del query con sus respectivos campos 
				case 'test':
				{
					$hay_resultados = 'si';
					// --> Nombre de los campos
					for($i=1; $i <= odbc_num_fields($res_query_parametro); $i++)
					{
						$array_nom[$i] = '<b>'.odbc_field_name($res_query_parametro, $i).'</b>';
					}
					$lista_resultados[] = $array_nom;
					
					// --> Resultado
					while(odbc_fetch_row($res_query_parametro))
					{							
						for($i=1; $i <= odbc_num_fields($res_query_parametro); $i++)
						{
							// --> valores
							$array_val[$i] = odbc_result($res_query_parametro, $i);
						}
						$lista_resultados[] = $array_val;						
					}
					break;
					
				}
			}
			if ($hay_resultados == 'no')
				$lista_resultados['0'] = '0';
		}
	}
}