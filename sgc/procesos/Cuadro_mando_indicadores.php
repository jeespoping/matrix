<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        CUADRO DE MANDO INDICADORES
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	2012-10-31
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='28-Nov-2019';
//--------------------------------------------------------------------------------------------------------------------------------------------     
//	28-11-2019 (Jerson Trujillo): Se corrige incosistencia en la palicacion de los filtros de visualizacion
//  18-11-2019 (Jerson Trujillo): Se cambia casting de int por float en las variables de semaforizacion
//  10-09-2019 (Jerson Trujillo): Se corrige incosistencia de migracion, manejo de decimales 
//  2017-02-09: Se agrega filtro en el campo ccoemp en caso de que el Query utilice la tabla costosyp_000005.
//              Arleyda Insignares C.
//              
//	2013-10-17: Se crea una nueva funcionalidad para los indicadores que manejen resultado detallado, para que se pueda calcular
//				el resultado general dependiendo de la modalidad que tenga configurada en la ficha, decir el reultado general puede
//				ser una sumatoria, un promedio o un calculo de un query general(Query sin group by).
//				Jerson trujillo.
//
//	2013-08-20:	Se modifica el orden para pintar los indicadores, primero deben aparecer los manuales y luego los automaticos.
//				Jerson trujillo.
//
//	2014-03-17: EL nombre de las variables de la formula se consultan segun como esten en el momento
//				para asi traerlos actualizados, Jerson trujillo.
//
//	2014-05-09: Se arregla la funcionalidad que obtiene los resultados ya calculados, ya que cuando el indicador es para
//				de periodicidad mayor a un mes, no traia los resultados, ahora ya va a traer los resultados que le corresponden
//				y los suma.
//	2014-05-27: Se corrige error en if, ver fecha del cambio.
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
	include_once("Funciones_sgc.php");
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'sgc');
	$wfecha=date("Y-m-d");
    $whora = date("H:i:s");
	$array_meses =	array( 1=>'Ene', 2=>'Feb', 3=>'Mar', 4=>'Abr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Ago', 9=>'Sep', 10=>'Oct',11=>'Nov', 12=>'Dic' );

//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S
//=====================================================================================================================================================================

	//--------------------------------------------------------------------------------------------------
	//	Funcion que retorna el numero de columnas que se le deben asigar a un texarea segun el texto
	//--------------------------------------------------------------------------------------------------
	function numero_columnas_textarea($texto, $cols)
	{
		// --> reemplaza todos los saltos de linea por <br>
		$aux  = preg_replace("[\n|\r|\n\r]", '<br>', $texto);
		$cont = substr_count($aux, '<br><br>'); //cuentas cuantos saltos de linea tiene
		$rows = 0;
		if( $cont == 0)
		{
			//Si no tiene saltos de linea, Determina si se debe crear una nueva columna si el texto es muy largo (mas del 90% del texto horizontal)
			$rows = ceil(strlen($texto) / ($cols*0.9))+1;
		}
		else
		{
			$rowsaux = 0;
			$lineas = explode( '<br><br>' , $aux ); //Crea un arreglo donde en cada posicion hay una cadena de texto por cada salto de linea
			foreach( $lineas as $linea )
			{
				$conta = ceil(strlen($linea) / ($cols*0.9)); //Determina si se debe crear una nueva columna si el texto es muy largo(mas del 90% del texto horizontal)
				if( $conta > 1 )
				{
					$rowsaux+=$conta;
				}
			}
			$rows = $cont + $rowsaux; //El numero de filas es: numero de saltos de linea + filas extras por texto muy largo
		}
		//el minimo de filas es 3
		if( $rows < 3 )
		{
			$rows = 3;
		}
		else
		{
			$rows++;
		}

		return $rows;
	}
	//-----------------------------------------------------------------------------------------
	//	Funcion obtiene los codigos de todos lo centros de costos con sus respectivos nombres
	//-----------------------------------------------------------------------------------------
	function obtener_cadena_cco($todos_cco)
	{
		global $conex;
		global $wemp_pmla;
		$cadena_val = '';
		$array_valores = array(" ", "(", ")", ".", "/", "-", "?");

		$q = "  SELECT  Emptcc, Empcod
				  FROM  root_000050
			  ORDER BY  Empcod";

		$res = mysql_query($q,$conex)or die("Error: " . mysql_errno() . " - en el query (Seleccionar Tabla para CCO): ".$q." - ".mysql_error());;

		while($row = mysql_fetch_array($res))
		{
			$tabla_cco = $row['Emptcc'];
			$cod_empre = $row['Empcod'];

			if ($tabla_cco != 'NO APLICA')
			{
				if($tabla_cco=='costosyp_000005')
					$campo="Cconom";
				else
					$campo="Ccodes";

				if($tabla_cco=='costosyp_000005'){

					$q_2  = " SELECT A.Ccocod AS codigo, A.".$campo." AS nombre
							    FROM ".$tabla_cco." AS A ".($tabla_cco=='costosyp_000005' ? 'LEFT JOIN movhos_000011 as B ON A.Ccocod = B.Ccocod' : '')."
							   WHERE A.Ccoest = 'on' 
							     AND A.Ccoemp = '".$wemp_pmla."'
							   ORDER BY nombre
							";
				}   
				else{             
					$q_2  = " SELECT A.Ccocod AS codigo, A.".$campo." AS nombre
							    FROM ".$tabla_cco." AS A ".($tabla_cco=='costosyp_000005' ? 'LEFT JOIN movhos_000011 as B ON A.Ccocod = B.Ccocod' : '')."
							   WHERE A.Ccoest = 'on'
							   ORDER BY nombre
							";
				}		

				$res2 = mysql_query($q_2,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar CCO): ".$q_2." - ".mysql_error());

				while($row2 = mysql_fetch_array($res2))
				{
					// --> Si este centro de costos esta en alguno de los indicadores que estan pintados
					if(in_array($cod_empre.$row2['codigo'], $todos_cco))
					{
						$nombre_cco    = utf8_decode($row2['nombre']);
						$nombre_limpio = str_replace($array_valores, '', $nombre_cco);

						if($cadena_val == '')
							$cadena_val = $cod_empre.$row2['codigo'].'|'.$nombre_cco.'|'.$nombre_limpio;
						else
							$cadena_val.= '|<>|'.$cod_empre.$row2['codigo'].'|'.$nombre_cco.'|'.$nombre_limpio;
					}
				}
			}
		}
		return $cadena_val;
	}
	//----------------------------------------------------------------------
	//	Funcion que pinta las opciones de visualizacion de los indicadores
	//----------------------------------------------------------------------
	function pintar_opciones_visualizacion($todos_cco, $tipo)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		$a = array('á','é','í','ó','ú','Á','É','Í','Ó','Ú','ñ','Ñ');
		$b = array('a','e','i','o','u','A','E','I','O','U','n','N');

		// --> obtener el filtro por defecto
		$FiltroPorDefecto = consultarAliasPorAplicacion($conex, $wemp_pmla, 'FiltroDefectoCMI');
		// --> obtener las opciones de filtros
		$q_opc = "SELECT Prodes, Proprf, Protbl, Procpr, Profpr, Prodpr
				    FROM ".$wbasedato."_000014
				   WHERE Proest = 'on' ";
		$res_opc = mysql_query($q_opc,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_opc." - ".mysql_error());

		echo "
		<table style='color: #2A5DB0;font-size: 12pt;'>
			<tr>
				<td align='center'>";
			while($row_opc = mysql_fetch_array($res_opc))
			{
				$prefijo = $row_opc['Procpr'];
				$prefijo = $prefijo{0}.$prefijo{1}.$prefijo{2};
				// --> Obtener todos los posibles valores que maneja el filtro
				$q_valores = "SELECT ".$row_opc['Procpr'].", ".$row_opc['Prodpr']."
							    FROM ".$row_opc['Proprf']."_".$row_opc['Protbl']."
							   WHERE ".str_replace('AND', '', $row_opc['Profpr'])."
							     AND ".$prefijo."ppi IN ('A', '".$tipo."')
							";
				$res_valores = mysql_query($q_valores,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_valores." - ".mysql_error());
				$cadena_val  = "";

				while($row_valores = mysql_fetch_array($res_valores))
				{
					if($cadena_val == "")
						$cadena_val = $row_valores[0].'|'.$row_valores[1].'|'.str_replace(' ', '', $row_valores[1]);
					else
						$cadena_val.= '|<>|'.$row_valores[0].'|'.$row_valores[1].'|'.str_replace(' ', '', $row_valores[1]);
				}

				if($cadena_val == "")
					$cadena_val = 'no_aplica|Sin '.$row_opc['Prodes'].'|Sin'.str_replace(' ', '', $row_opc['Prodes']);
				else
					$cadena_val.= '|<>|no_aplica|Sin '.$row_opc['Prodes'].'|Sin'.str_replace(' ', '', $row_opc['Prodes']);

				// --> Pinto el boton con la opcion de filtrado
				$filtrado = strtolower(str_replace($a, $b, str_replace(' ', '', $row_opc['Prodes'])));
				echo "<div name='opc_visualizacion' filtrado='".$filtrado."' codigos='".$cadena_val."' descripcion='".$row_opc['Prodes']."' onclick='SegmentarTabla(\"".$filtrado."\", \"".$cadena_val."\", \"si\")' style='cursor:pointer;display:inline' val='".$filtrado."'>&nbsp;".$row_opc['Prodes']."&nbsp;</div>&nbsp;&nbsp; ";
				if($FiltroPorDefecto == str_replace(' ', '',strtolower(str_replace($a, $b, $row_opc['Prodes']))))
				{
					$filtro_defecto = str_replace(' ', '',strtolower(str_replace($a, $b, $row_opc['Prodes'])));
					$cadena_val_defecto = $cadena_val;
				}

			}
			// --> Aqui se agrega una nueva opcion de filtro que sera por centro de cotos, cuyos valores se obtienen por de aparte
			//	   ya que no se puede manejar desde la tabla sgc_000014 como en los casos anteriores, porque los centros de costos
			//	   no se manejan en una sola tabla.

				// --> Obtener todos los valores de los centros de costos con sus respectivos nombres (de todas las empresas)
				$cadena_val = obtener_cadena_cco($todos_cco);
				if($FiltroPorDefecto == 'centrosdecostos')
				{
					$filtro_defecto = 'centrosdecostos';
					$cadena_val_defecto = $cadena_val;
				}
				// --> Pinto el filtro para centros de costos
				echo "<div name='opc_visualizacion' filtrado='centrosdecostos' codigos='".$cadena_val."' descripcion='Centros de costos' onclick='SegmentarTabla(\"centrosdecostos\", \"".$cadena_val."\", \"si\")' style='cursor:pointer;display:inline' val='centrosdecostos'>&nbsp;Centros de costos&nbsp;</div>&nbsp;&nbsp; ";

			// <-- Fin agregar nueva opcion de filtro

		echo "		 <input type='hidden' id='filtro_selec_defecto' 	value='".$filtro_defecto."' >
					 <input type='hidden' id='cadena_val_selec_defecto' value='".$cadena_val_defecto."' >
				</td>
			</tr>
		</table>
			";
	}
	//--------------------------------------------
	//	Funcion que devuelve el color de semaforo
	//--------------------------------------------
	function semaforizacion($valor_result, $desempe_min, $meta_indicad, $desempe_sup, $tipo_semafor)
	{
		$valor_result = str_replace(".", "", $valor_result);
		
		$tamaño = "width='15' height='15'";
		switch($tipo_semafor)
		{
			case 'A':
				{
					if($valor_result < $desempe_min)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/no_conforme.png'>"; //$color_semaforo = '#E96C6A';
					if($valor_result >= $desempe_min && $valor_result < $meta_indicad)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/aceptable.png'>"; //$color_semaforo = '#FFF29A';
					if($valor_result >= $meta_indicad && $valor_result < $desempe_sup)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/conforme.png'>"; //$color_semaforo = '#82EAB8';
					if($valor_result >= $desempe_sup)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/estandar_superior.png'>"; //$color_semaforo = '#88C2FF';
					break;
				}
			case 'D':
				{
					if($valor_result < $desempe_min)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/estandar_superior.png'>"; //$color_semaforo = '#88C2FF';
					if($valor_result >= $desempe_min && $valor_result < $meta_indicad)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/conforme.png'>"; //$color_semaforo = '#82EAB8';
					if($valor_result >= $meta_indicad && $valor_result < $desempe_sup)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/aceptable.png'>"; //$color_semaforo = '#FFF29A';
					if($valor_result >= $desempe_sup)
						$color_semaforo = "<img ".$tamaño." src='../../images/medical/sgc/no_conforme.png'>"; //$color_semaforo = '#E96C6A';
					break;
				}
		}

		return $color_semaforo;
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
	//---------------------------------------------------------
	//Funcion para consultar el tema en las opciones de menu
	//---------------------------------------------------------
	function consultar_tema_cco_cargo(&$wtema, &$cargo, &$cco_usuario, $wuse, $wemp_pmla)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		//Consultar el tema
		$q = " SELECT Temcod
			     FROM root_000076
				WHERE Tememp = '".$wemp_pmla."'
				  AND Temprf = '".$wbasedato."'
				";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$row = mysql_fetch_array ($res);
		$wtema = $row['Temcod'];

		$wbd_talhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
		//Consultar el cargo
		$q2 = " SELECT Ideccg, Idecco
			     FROM ".$wbd_talhuma."_000013
				WHERE Ideuse = '".substr($wuse, -5)."-".$wemp_pmla."'
				";
		$res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
		if($row2 = mysql_fetch_array ($res2))
		{
			$cargo = $row2['Ideccg'];
			$cco_usuario = $row2['Idecco'];
		}
	}

	//------------------------
	//	COLOR DE FILA
	//------------------------
	function color_fila($color_fila)
	{
		if ($color_fila == "Fila1")
			$color_fila = "Fila2";
		else
			$color_fila = "Fila1";

		return $color_fila;
	}
	//-----------------------------------------------------------
	// Pintar tooltips con la informacion de la formula aplicada
	//-----------------------------------------------------------
	function info_formula_aplicada($wperiodo, $formula_valores, $formula_fuentes)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		$periodo_fechas		 = explode('|', $wperiodo);
		$arr_formula_fuentes = explode('|', $formula_fuentes);
		$arr_formula_valores = explode('|', $formula_valores);
		$arr_info_fuentes	 = array();


		foreach($arr_formula_fuentes as $cod_dato)
		{
			if($cod_dato{0} == 'I' || $cod_dato{0} == 'E')
			{
				if(strstr($cod_dato, '['))
				{
					$cod_dato = substr($cod_dato, 0, stripos($cod_dato, '['));
				}

				$q_movimiento = "SELECT Descripcion as responsable, Movana as analisis
								   FROM ".$wbasedato."_000009, usuarios
								  WHERE Movind = '".trim($cod_dato)."'
									AND ( '".$periodo_fechas[0]."' >= Movfip AND '".$periodo_fechas[1]."' <= Movffp )
									AND Movgen = 'on'
									AND Movusu = Codigo";

				$res_movimiento = mysql_query($q_movimiento, $conex) or die (mysql_errno() . $q_movimiento . " (Query: Consultar movimiento) " . mysql_error());
				if($row_movimiento = mysql_fetch_array($res_movimiento))
				{
					$arr_info_fuentes[] = $row_movimiento;
				}
				else
					$arr_info_fuentes[] = '';
			}
			else
				$arr_info_fuentes[] = '';
		}

		echo '
		<table>
			<tr>';
		foreach($arr_formula_valores  as $indice => $valor_for)
		{
			if (is_array($arr_info_fuentes[$indice]))
			{
				$informacion = $arr_info_fuentes[$indice];
				$tooltip = "<table width=\"350px\">
								<tr><td class=\"Fondoamarillo\" align=\"center\"><b>ORIGEN DEL DATO = <span style=\"font-family:Courier New;font-size: 12pt;\">".$valor_for."</span></b></td></tr>
								<tr><td class=\"Encabezadotabla\" align=\"center\">Análisis:</td></tr>
								<tr><td class=\"fila2\" style=\"font-size: 9pt;align:center;\">".(($informacion['analisis'] != '') ? utf8_decode($informacion['analisis']) : 'Sin analizar')."</td></tr>
								<tr><td class=\"Encabezadotabla\" align=\"center\">Responsable:</td></tr>
								<tr><td class=\"fila2\" style=\"font-size: 9pt;\" align=\"center\">".$informacion['responsable']."</td></tr>
							</table>";

				echo "<td tooltip2='si' title='".$tooltip."' style='cursor:help;'><b>".$valor_for."</b></td>";
			}
			else
				echo '<td><b>'.$valor_for.'</b></td>';
		}
		echo '
			</tr>
		</table>';
	}
	//-----------------------------------------------------------
	// Pintar informacion basica del indicador o la estadistica
	//-----------------------------------------------------------
	function pintar_infor_basica($codigo, &$formula_calcular, &$nombre_magnitud, &$tipo_estadistica, &$numero_decimales, &$formula_nombres, &$resp_medicion, &$modifica_resulta, $llamado_recursivo, &$tipo_resultado_general)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;

		$q_datos = " 	SELECT A.* , Descripcion, Prinom, Magnom
						  FROM ".$wbasedato."_000001 as A, usuarios, ".$wbasedato."_000008, ".$wbasedato."_000006
						 WHERE Indcod = '".$codigo."'
						   AND Indrme = Codigo
						   AND Indpri = Pricod
						   AND Indmag = Magcod
						";

		$res_datos = mysql_query($q_datos, $conex) or die (mysql_errno() . $q_datos . " - " . mysql_error());
		$row_datos = mysql_fetch_array($res_datos);

		$formula_calcular 		= $row_datos['Indfoc'];
		$formula_nombres  		= $row_datos['Indfon'];
		$nombre_magnitud  		= $row_datos['Magnom'];
		$tipo_estadistica 		= $row_datos['Indtes'];
		$numero_decimales 		= $row_datos['Indnde'];
		$resp_medicion    		= $row_datos['Indrme'];
		$modifica_resulta 		= $row_datos['Indare'];
		$tipo_resultado_general = $row_datos['Indorg'];

		//--------------------------
		// --> PINTO INFORMACION
		//--------------------------
		if($llamado_recursivo == 'no')
		{
			echo'<div class="borderDiv Titulo_azul" align=center>
					<div style="cursor:pointer;" onclick="cerrar_seccion(\'div_basicos\');">
					<img width="10" height="10" src="../../images/medical/iconos/gifs/i.p.next[1].gif" />
					'.(($tipo_estadistica != 'on') ? 'DESCRIPCIÓN DEL INDICADOR' : 'DESCRIPCIÓN DE LA ESTADISTICA').'
					</div>
				</div><br>
				<div id="div_basicos" align="center" class="borderDiv "><br>
					<table width="96%" border="0" align="center" >
				';
			//	-->	Nombre y Codigo
			echo"		<tr class='fila1'>
							<td class='encabezadoTabla' width=15%>Nombre:&nbsp;</td>
							<td width=35% style='font-weight:bold;' id='nombre_dato_graf'>
								<div>".utf8_decode($row_datos['Indnom'])."</div>
							</td>
							<td class='encabezadoTabla' width=15%>C&oacute;digo:&nbsp;</td>
							<td width=35% style='font-weight:bold;' align=center>
								<div>".$codigo."</div>
							</td>
						</tr>";
			echo "<input type='hidden' id='hidden_nombre_indicador2' value='".utf8_decode($row_datos['Indnom'])."'>";

			// --> Esto se pinta solo para los inidcadores
			if($tipo_estadistica != 'on')
			{
				//	-->	Objetivo e Interpretación
				echo"		<tr class='fila2'>
								<td class='encabezadoTabla'>Objetivo:</td>
								<td>
									<div style='text-align: justify;'>".utf8_decode($row_datos['Indobj'])."</div>
								</td>
								<td class='encabezadoTabla' >Interpretaci&oacute;n:</td>
								<td>
									<div style='text-align: justify;'>". utf8_decode($row_datos['Indint'])."</div>
								</td>
							</tr>";
			}
			$nom_emp_cco = consultar_cc_y_empresa($row_datos['Indemp'], $row_datos['Indcco']);
			$nom_emp_cco = explode('|', $nom_emp_cco);
			//	-->	Empresa y unidad
			echo"		<tr class='".(($tipo_estadistica != 'on') ? 'Fila1' : 'Fila2' )."'>
							<td class='encabezadoTabla'>Empresa:</td>
							<td>
								<div>".$nom_emp_cco[0]."</div>
							</td>
							<td class='encabezadoTabla' >Unidad:</td>
							<td>
								<div>".$nom_emp_cco[1]."</div>
							</td>
						</tr>";
			//	-->	Responsable y periodicidad
			echo"		<tr class='".(($tipo_estadistica != 'on') ? 'Fila2' : 'Fila1' )."'>
							<td class='encabezadoTabla'>Responsable:</td>
							<td>
								<div>".$row_datos['Descripcion']."</div>
							</td>
							<td class='encabezadoTabla'>Periodicidad:</td>
							<td>
								<div>".$row_datos['Prinom']."</div>
							</td>
						</tr>";

			// --> Esto se pinta solo para los inidcadores
			if($tipo_estadistica != 'on')
			{
				//	-->	Meta  y Limites
				echo"		<tr class='fila1'>
								<td class='encabezadoTabla'>Meta:</td>
								<td>
									<div>".(($row_datos['Indmet'] != '') ? formato_respuesta($row_datos['Indmet']).' '.$row_datos['Magnom'] : '' )."</div>
								</td><td class='encabezadoTabla'>Limites:</td>
								<td align='center'>
									<div>
									<table class='Fila1'>
										<tr>
											<td align='center'><b>Inferior: </b></td><td align='center'>".(($row_datos['Inddmi'] != '') ? $row_datos['Inddmi'].' '.$row_datos['Magnom'] : '')."</td>
											<td align='center'><b>Superior: </b></td><td align='center'>".(($row_datos['Inddsu'] != '') ? $row_datos['Inddsu'].' '.$row_datos['Magnom'] : '')."</td>
										</tr>
									</table>
									</div>
								</td>
							</tr>";
			}

			$arr_formulaCodigos = explode ('|', $formula_calcular);
			$formula_nombres 	= '';
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
						$formula_nombres.= $rowSqlNomVar['Indnom'].'|';
					else
						$formula_nombres.= 'ERROR: VARIABLE NO EXISTE ('.$codVariable.')|';
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
						$formula_nombres.= $rowSqlNomVar['Campo'].'|';
					else
						$formula_nombres.= 'ERROR: VARIABLE NO EXISTE ('.$codVariable.')|';
				}
			}
			//	-->	Formula y unidad de medida
			echo"		<tr class='Fila2'>
							<td class='encabezadoTabla'>Formula:</td>
							<td>
								<div>".utf8_decode(str_replace('|', '', $formula_nombres))."</div>
							</td><td class='encabezadoTabla'>Unidad de medida:</td>
							<td align='center'>
								<div>".$nombre_magnitud."</div>
							</td>
						</tr>";

			// --> Esto se pinta solo para los inidcadores
			if ($tipo_estadistica != 'on')
			{
				//	-->	responde a
				$in_responde = '';
				$array_responde_a = explode(',', $row_datos['Indgru']);
				foreach ($array_responde_a  as $valor_resp)
				{
					if($in_responde == '')
						$in_responde = "'".$valor_resp."'";
					else
						$in_responde.= ", '".$valor_resp."'";
				}

				//Consulto la descripcion del 'responde a'
				$q_respon = " SELECT Grunom
								FROM ".$wbasedato."_000005
							   WHERE Grucod IN (".$in_responde.")
							";
				$res_respon = mysql_query($q_respon, $conex) or die (mysql_errno() . $q_respon . " - " . mysql_error());

				echo"	<tr class='fila1'>
							<td class='encabezadoTabla'>Responde a:</td>
							<td>
								<div>";
							while($row_respon = mysql_fetch_array($res_respon))
							{
								echo $row_respon ['Grunom'].', ';
							}
				echo"			</div>
							</td>
							<td class='encabezadoTabla'>Semaforización:</td>";
							if($row_datos['Indsem'] == 'A')
							{
								$semafori = 'Ascendente';
								$title_se = "<table><tr><td style=\"color: #000000;font-size: 10pt;align:center;\"><b>Mayor el resultado, Mejor la calificación</b></td></tr></table>";
							}
							else
							{
								$semafori = 'Descendente';
								$title_se = "<table><tr><td style=\"color: #000000;font-size: 10pt;align:center;\"><b>Mayor el resultado, Peor la calificación</b></td></tr></table>";
							}

				echo"		<td align='center'><font tooltip2='si' title='".$title_se."' style='cursor:help'>".$semafori ."</font></td>
						</tr>";
			}
			echo '	</table><br>
				</div>';
		}
		//----------------------------------
		// <-- Cierro informacion basica
		//----------------------------------
	}


	//--------------------------------------------------------
	// Consulta el cco y la empresa del inidcador a calcular
	//--------------------------------------------------------
	function consultar_cc_y_empresa($wempresa, $cco)
	{
		global $conex;
		global $wemp_pmla;
		$nom_empresa='';
		$nom_cco='';

		$q = "  SELECT  Emptcc, Empdes
				FROM    root_000050
				WHERE   Empcod ='".$wempresa."'";
		$res = mysql_query($q,$conex)or die("Error: " . mysql_errno() . " - en el query (consultar empresa): ".$q." - ".mysql_error());;

		if( mysql_num_rows($res) > 0)
		{
			$row = mysql_fetch_array($res);
			$nom_empresa = $row['Empdes'];
			$tabla_CCO = $row['Emptcc'];
			$nom_cco = '';

			if($tabla_CCO=='costosyp_000005')
				$campo="Cconom";
			else
				$campo="Ccodes";

			if ($tabla_CCO != 'NO APLICA' && $cco != '')
			{
				if($tabla_CCO=='costosyp_000005'){

					$q_2  = " SELECT ".$campo." AS nombre "
							."  FROM ".$tabla_CCO.""
							." WHERE Ccocod = '".$cco."' "
							."  AND Ccoemp = '".$wemp_pmla."' ";
				}
				else{
			
					$q_2  = " SELECT ".$campo." AS nombre "
					."  FROM ".$tabla_CCO.""
					." WHERE Ccocod = '".$cco."' ";
				}			


				$res2 = mysql_query($q_2,$conex)or die("Error: " . mysql_errno() . " - en el query (Consultar CCO): ".$q_2." - ".mysql_error());

				if( mysql_num_rows($res2) > 0)
				{
					$row2 = mysql_fetch_array($res2);
					$nom_cco = $row2['nombre'];
				}
			}
		}
		$nom_cco_emp = $nom_empresa.'|'.$nom_cco;
		return $nom_cco_emp;
	}
	//---------------------------------------------------------------------------
	//	OBTENER SI EL CALCULO DEFINITIVO DE UN INDICADOR ESTA DESACTULIZADO
	//---------------------------------------------------------------------------
	function calculo_desactualizado($arr_componentes_formu, $arr_nombre_comp_formu, $wperiodo_calcular, $fecha_calc, $hora_calc, &$mensaje_desactual)
	{
		global $conex;
		global $wbasedato;

		$periodo_fechas 	= explode('|', $wperiodo_calcular);
		$fecha_hora_calculo = strtotime($fecha_calc.' '.$hora_calc);
		// --> recorrer cada variable de la formula
		foreach($arr_componentes_formu as $indice => $valor_formula)
		{
			$valor_formula = trim($valor_formula);
			if(strstr($valor_formula, '['))
			{
				$valor_formula = substr($valor_formula, 0, stripos($valor_formula, '['));
			}

			// --> Si es un indicador o estadistica
			if($valor_formula != '' && ($valor_formula{0} == 'E' || $valor_formula{0} == 'I'))
			{
				// --> Obtener la fecha y hora en que se calculo
				$q_movimiento = "SELECT Fecha_data, Hora_data
								   FROM ".$wbasedato."_000009
								  WHERE Movind = '".$valor_formula."'
									AND ( '".$periodo_fechas[0]."' >= Movfip AND '".$periodo_fechas[1]."' <= Movffp )
									AND Movcal = 'on'
									AND Movgen = 'on' ";
				$res_movimiento = mysql_query($q_movimiento, $conex) or die (mysql_errno() . $q_movimiento . " (Query: Consultar fecha y hora de movimiento) " . mysql_error());
				if($row_movimiento = mysql_fetch_array($res_movimiento))
				{
					$fecha_hora_mov = strtotime($row_movimiento['Fecha_data'].' '.$row_movimiento['Hora_data']);
					if($fecha_hora_mov > $fecha_hora_calculo)
						$mensaje_desactual.= (($mensaje_desactual == '') ? 'Este resultado no es confiable, ya que los cálculos de las <br>siguientes variables de su fórmula se han modificado:<br><b>- '.$arr_nombre_comp_formu[$indice].'</b>' : '<b><br>- '.$arr_nombre_comp_formu[$indice].'</b>' );
				}
			}
		}

		if($mensaje_desactual == '')
			return false;
		else
			return true;
	}
	//---------------------------------------------------------------------------
	//	CONSULTAR SI EL INIDICADOR YA SE HA CALCULADO, EN UN DETERMINADO PERIODO
	//---------------------------------------------------------------------------
	function estado_indicador ($mes_pintado, $codigo, &$row_resultado, $cco_aplican_usuario, $tipo_permiso)
	{
		global $conex;
		global $wbasedato;

		// --> Como la variable $mes_pintado viene en formato yyyy-m, la convierto a yyyy-mm-dd
		$mes_pintado =  explode ('-',$mes_pintado);
		$año = $mes_pintado[0];
		$mes = $mes_pintado[1];

		if( $mes < 10)
			$mes = '0'.$mes;

		$mes_pintado = $año.'-'.$mes;

		$fecha_i = $mes_pintado.'-01';
		$fecha_f = $mes_pintado.'-'.getUltimoDiaMes($año,$mes);

		// --> Consultar si para dicho periodo el indicador ya se encuentra calculado
		$q_estado= 	"SELECT A.id as id, Movres, Magnom, Movcal, Movtem, Movdsu, Movdmi, Movmet, Movsem, Movfap,
							A.Fecha_data as fecha, A.Hora_data as hora, Movvde, Movfin, Movorg
					   FROM ".$wbasedato."_000009 as A, ".$wbasedato."_000006
					  WHERE Movind = '".$codigo."'
					    AND ( '".$fecha_i."' >= Movfip AND '".$fecha_f."' <= Movffp )
						AND Movmag = Magcod
					";
		$res_estado    = mysql_query($q_estado,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar resultado indicador): ".$q_estado." - ".mysql_error());
		if (mysql_num_rows($res_estado) > 0)
		{
			$array_resultados = array();
			while($row_estado = mysql_fetch_array ($res_estado))
			{
				$array_resultados[trim($row_estado['Movvde'])] = $row_estado;
			}

			// --> Si el tipo de permiso que tiene el usuario loggeado es de visualizacion y el cco al que pertenece
			//     esta dentro del resultado detallado; entonces muestro el resultado pero de ese detalle.
			if($tipo_permiso == 'vizualizar' && array_key_exists($cco_aplican_usuario['cco_usuario'], $array_resultados) && count($cco_aplican_usuario['cco_coordina']) == 0)
			{
				$row_resultado 	= $array_resultados[$cco_aplican_usuario['cco_usuario']];
				$estado_ind   	= $row_resultado['id'];
			}
			// sino, muestro el resultado general
			else
			{
				$row_resultado 	= $array_resultados['*'];
				$estado_ind 	= $row_resultado['id'];
			}
		}
		else
		{
			$estado_ind = 'Sin Calcular';
		}
		return $estado_ind;
	}
	//---------------------------------------------------------------------
	//	CALCULAR EN QUE MESES SE APLICA EL INDICADOR, RETORNANDO UN ARRAY
	//---------------------------------------------------------------------
	function calcular_meses_aplicar($array_meses_pintados, &$info_inidcadore, $cod_indi_mostrar, $wemp_pmla, &$todos_cco, $pendientes_calc)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $cmi_consulta;

		$wtema 			= '';
		$cargo			= '';
		$cco_usuario 	= '';
		$array_nombre_indicadores = array();

		// --> Query para el cuadro de mando de consulta, es decir se muestran todos los indicadores pero solo con opciones de visualizacion
		if( isset($cmi_consulta) && $cmi_consulta == 'on')
		{
			// --> Pintar solo un indicador o estadistica epecifica, osea le dieron click a uno entonces en la cuadricula
			//	   solo muestro ese.
			if ($cod_indi_mostrar != '%')
			{
					$q_indicadores=	"
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'vizualizar' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, usuarios
									 WHERE Indest = 'on'
									   AND Indcod = '".$cod_indi_mostrar."'
									   AND Indpri = Pricod
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									";
			}
			else
			{
					$q_indicadores=	"
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'vizualizar' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, usuarios
									 WHERE Indest = 'on'
									   AND Indpri = Pricod
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									 ORDER BY  Indtes, Indnom
								";
			}
		}
		// --> Solo los indicadores y estadisticas permitidas para que el usuario las pueda ver
		else
		{
			consultar_tema_cco_cargo($wtema, $cargo, $cco_usuario, $wuse, $wemp_pmla);
			// --> Pintar solo un indicador o estadistica epecifica, osea le dieron click a uno entonces en la cuadricula
			//	   solo muestro ese.
			if ($cod_indi_mostrar != '%')
			{
					$q_indicadores=	"
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'vizualizar' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, root_000082, usuarios
									 WHERE Indest = 'on'
									   AND Indcod = '".$cod_indi_mostrar."'
									   AND Indpri = Pricod
									   AND Indcod = Prftab
									   AND Prftem = '".$wtema."'
									   AND ( Prfuse = '".substr($wuse, -5)."-".$wemp_pmla."' OR Prfccg = '".$cargo."' OR Prfcco = '".$cco_usuario."')
									   AND Prfest = 'on'
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									 UNION
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'vizualizar' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, usuarios
									 WHERE Indest = 'on'
									   AND Indcod = '".$cod_indi_mostrar."'
									   AND Indpri = Pricod
									   AND Indrev = '".$wuse ."'
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									 UNION
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'calcular' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, usuarios
									 WHERE Indest = 'on'
									   AND Indcod = '".$cod_indi_mostrar."'
									   AND Indpri = Pricod
									   AND Indrme = '".$wuse ."'
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									";
			}
			else
			{
					$q_indicadores=	"
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'vizualizar' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, root_000082, usuarios
									 WHERE Indest = 'on'
									   AND Indpri = Pricod
									   AND Indcod = Prftab
									   AND Prftem = '".$wtema."'
									   AND ( Prfuse = '".substr($wuse, -5)."-".$wemp_pmla."' ".(($cargo != '' && $cco_usuario != '') ? "OR Prfccg = '".$cargo."' OR Prfcco = '".$cco_usuario."')" : ")" )."
									   AND Prfest = 'on'
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									 UNION
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'vizualizar' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, usuarios
									 WHERE Indest = 'on'
									   AND Indpri = Pricod
									   AND Indrev = '".$wuse ."'
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									 UNION
									SELECT Indnom, Indmim, Prindi, Indcod, Indtes, Indnde, Descripcion, 'calcular' as permiso, Indper, Indjer, Indcar, Indgru, Indcco, Indemp, Magnom, Indare, Indfoc, Indfon
									  FROM ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000006, usuarios
									 WHERE Indest = 'on'
									   AND Indpri = Pricod
									   AND Indrme = '".$wuse ."'
									   AND Indrme = Codigo
									   AND Indmag = Magcod
									 ORDER BY  Indtes, Indnom
								";
			}
		}

		$q_res = mysql_query($q_indicadores,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar indicadores): ".$q_indicadores." - ".mysql_error());
		while($row_ind = mysql_fetch_array($q_res))
		{
			$periodicidad 		 = $row_ind['Prindi']/30;					//Indica cada cuanto se mide el indicador
			if($periodicidad > 1)
				$mes_inicio_medicion = $row_ind['Indmim']-1;
			else
				$mes_inicio_medicion = 1;

			$fecha_calculo 		 = '2004-'.$mes_inicio_medicion;
			// --> Recorrer mes a mes desde el 2004 hasta el mes actual, para analizar en que meses se debe generar el indicador o la estadistica
			while(strtotime($fecha_calculo) < strtotime(date('Y-m')))
			{
				$arra_fec_calc = explode ('-',$fecha_calculo);
				$mes_calc = $arra_fec_calc[1]+$periodicidad;
				$año_calc = $arra_fec_calc[0];
				if ($mes_calc >12)
				{
					$año_calc++;
					$mes_calc = $mes_calc-12;
				}
				$fecha_calculo = $año_calc."-".$mes_calc;						//Esta variable alamacena una fecha valida en la cual se debe calcular el indicador

				if (array_key_exists($fecha_calculo, $array_meses_pintados)) 	// Si esta fecha valida existe en las fechas que estan pintadas entonces la almaceno en el array
				{
					// Calculo la fecha de inicio y fin del periodo
					if($periodicidad == 1)
					{
						$fecha_ini_periodo = $año_calc.'-'.(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc).'-01';
						$fecha_fin_periodo = $año_calc.'-'.(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc).'-'.getUltimoDiaMes($año_calc,(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc));
					}
					else
					{
						$fecha_fin_periodo = $año_calc.'-'.(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc).'-'.getUltimoDiaMes($año_calc,(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc));
						$mes_calc = $mes_calc-$periodicidad+1;
						if($mes_calc >= 1)
							$fecha_ini_periodo = $año_calc.'-'.(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc).'-01';
						else
						{
							$año_calc = $año_calc-1;
							$mes_calc = 12+$mes_calc;
							$fecha_ini_periodo = $año_calc.'-'.(($mes_calc < 10) ? '0'.$mes_calc : $mes_calc).'-01';
						}
					}



					// --> En este array almaceno los meses en los cuales se debe aplicar el indicador, con su correspondiente periodo.
					$array_nombre_indicadores[$row_ind['Indcod']][$fecha_calculo] = $fecha_ini_periodo.'|'.$fecha_fin_periodo.'|'.$row_ind['permiso'];;
					$info_inidcadore[$row_ind['Indcod']]['nombre'] 			= $row_ind['Indnom'];
					$info_inidcadore[$row_ind['Indcod']]['tipo']   			= $row_ind['Indtes'];
					$info_inidcadore[$row_ind['Indcod']]['num_dec']			= $row_ind['Indnde'];
					$info_inidcadore[$row_ind['Indcod']]['res_med']			= $row_ind['Descripcion'];
					$info_inidcadore[$row_ind['Indcod']]['Perspectiva']		= $row_ind['Indper'];
					$info_inidcadore[$row_ind['Indcod']]['Jerarquia']		= $row_ind['Indjer'];
					$info_inidcadore[$row_ind['Indcod']]['Caracteristica']	= $row_ind['Indcar'];
					$info_inidcadore[$row_ind['Indcod']]['Grupo']			= $row_ind['Indgru'];
					$info_inidcadore[$row_ind['Indcod']]['Unidad_medida']	= $row_ind['Magnom'];
					$info_inidcadore[$row_ind['Indcod']]['Cco']				= $row_ind['Indemp'].$row_ind['Indcco'];
					$info_inidcadore[$row_ind['Indcod']]['Modificable']		= $row_ind['Indare'];
					$info_inidcadore[$row_ind['Indcod']]['Formula']			= $row_ind['Indfoc'];
					$info_inidcadore[$row_ind['Indcod']]['Formula_nombres']	= $row_ind['Indfon'];
					$todos_cco[] = $row_ind['Indemp'].$row_ind['Indcco'];
				}
			}

		}
		return $array_nombre_indicadores;
	}
	// --> Obtener el ultimo dia del mes
	function getUltimoDiaMes($elAnio,$elMes)
	{
		return date("d",(mktime(0,0,0,$elMes+1,1,$elAnio)-1));
	}

	//--------------------------------------------------------------------------------------
	//	Obtiene un array con los datos que se calculan automaticamente
	//--------------------------------------------------------------------------------------
	function ObtenerDatosAutomaticos()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;

		$array_datos_automa = array();

		$q_dat = " SELECT Datcod
			         FROM ".$wbasedato."_000010
				    WHERE Datest  = 'on'
					  AND Datnum != 'on'
					  AND Datope != 'on'
					  AND Dataut  = 'on'
				";
		$res_dat = mysql_query($q_dat,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_dat." - ".mysql_error());
		while($row_dat = mysql_fetch_array($res_dat))
		{
			$array_datos_automa[$row_dat['Datcod']] = '';
		}
		return $array_datos_automa;
	}
	//--------------------------------------------------------------------------------------
	//	Obtiene los centros de costo relacionados al usuario, es decir el cco del usuario
	//  mas los cco en los que es coordinador
	//--------------------------------------------------------------------------------------
	function obtener_cco_aplican_al_usuario()
	{
		global $conex;
		global $wemp_pmla;
		global $wuse;
		$cco_aplican_usuario 				 = array();
		$cco_aplican_usuario['cco_usuario']  = array();
		$cco_aplican_usuario['cco_coordina'] = array();

		$wbd_talhuma = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

		// --> Centro de costos del usuario
		$q_cco = " SELECT Idecco
			         FROM ".$wbd_talhuma."_000013
				    WHERE Ideuse = '".substr($wuse, -5)."-".$wemp_pmla."'
				";
		$res_cco = mysql_query($q_cco,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_cco." - ".mysql_error());
		$row_cco = mysql_fetch_array ($res_cco);
		$cco_usuario = $row_cco['Idecco'];
		$cco_aplican_usuario['cco_usuario'] = $cco_usuario;
		// --> Centro de costos que coordina
		$q_coor = " SELECT Ajeccc
					  FROM ".$wbd_talhuma."_000008
					 WHERE Ajeucr = '".substr($wuse, -5)."-".$wemp_pmla."'
					   AND Ajecoo = 'on'
					 GROUP BY Ajeccc
				";
		$res_coor = mysql_query($q_coor,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_coor." - ".mysql_error());
		while($row_coor = mysql_fetch_array ($res_coor))
		{
			$cco_coor = $row_coor['Ajeccc'];
			$cco_coor = explode(',', $cco_coor);
			foreach ($cco_coor as $cco)
			{
				if(!in_array(trim($cco), $cco_aplican_usuario))
					$cco_aplican_usuario['cco_coordina'][]= trim($cco);
			}
		}

		return $cco_aplican_usuario;
	}

	//----------------------------------------------------------------------------------
	//	PINTAR LOS INDICADORES, CON SUS CORRESPONDIENTES MESES EN LOS CUALES SE APLICA
	//----------------------------------------------------------------------------------
	function pintar_meses($wperiodo, $cod_indi_mostrar, $wperiodo_resaltar, $wemp_pmla, $wcantidad_meses = 12, $pendientes_calc)
	{
		$wmes = explode('-',$wperiodo);
		$año  = $wmes[0];
		$wmes = $wmes[1];
		global $array_meses;
		global $conex;
		global $wbasedato;
		global $cmi_consulta;

		// --> Obtener array de los datos que se calculan automaticamente.
		$arr_datos_automaticos = ObtenerDatosAutomaticos();

		// --> Si es el cuadro de mando de consulta.
		if(isset($cmi_consulta) && $cmi_consulta == 'on')
		{
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
		}
		else
		{
			$cco_aplican_usuario = obtener_cco_aplican_al_usuario();
		}

		echo'
		<div align="center" >
			<table cellspacing="3px" id="tabla_filtros" style="display:none;" ></table>
			<table cellspacing="3px" id="tabla_ppal">
			<tr class="fijo" id="selectable" >';
			echo '<td colspan="2"></td>';

		// --> Pintar nombre de los años segun el que seleccionen en el calendario
		for( $x=$wmes+1; $x<=$wcantidad_meses; $x++)
		{
			if(isset($cantidad_año[$año-1]))
				$cantidad_año[$año-1]++;
			else
				$cantidad_año[$año-1]=1;
		}

		$x = ($wmes-$wcantidad_meses)+1;
		for( $x=(($x >= 1) ? $x : 1); $x<=$wmes; $x++)
		{
			if(isset($cantidad_año[$año]))
				$cantidad_año[$año]++;
			else
				$cantidad_año[$año]=1;
		}
		foreach($cantidad_año as $año_pintar => $colspan)
		{
			echo '<td  class="ano" colspan="'.$colspan.'" style="text-align: center;height:33px;"><b>'.$año_pintar.'</b></td>';
		}
		echo'
			</tr>
			<tr class="fijo" id="selectable" >';
			echo '<td class="ano" colspan="2" align="center" width="18%">Nombre</td>';

		//Pintar nombre de los meses
		$diff 				= (12-$wcantidad_meses);
	
		for( $x=$wmes+1+$diff ; $x<=$wcantidad_meses+$diff; $x++)
		{
			echo '<td class="mes"  id= "'.$array_meses[$x].'" >'.$array_meses[$x].'</td>';
			$array_meses_pintados[($año-1).'-'.$x] = $array_meses[$x];
		}
		$x = ($wmes-$wcantidad_meses)+1;
		for( $x=(($x >= 1) ? $x : 1); $x<=$wmes; $x++)
		{
			echo '<td class="mes" id= "'.$array_meses[$x].'" >'.$array_meses[$x].'</td>';
			$array_meses_pintados[$año.'-'.$x] = $array_meses[$x];
		}
		echo'
			</tr>';

		$color_fila 				= "Fila1";
		$info_inidcadore 			= array();
		$array_nombre_indicadores 	= array();
		$lista_indicadores 			= array();
		$todos_cco		 			= array();
		$lista_calculados_completos	= array();
		$array_nombre_indicadores 	= calcular_meses_aplicar($array_meses_pintados, $info_inidcadore, $cod_indi_mostrar, $wemp_pmla, $todos_cco, $pendientes_calc);
		$primera_vez = 'si';
		$num_fila	 = -1;

		if(count($array_nombre_indicadores)>0 || $wperiodo_resaltar !='')
		{
			// --> Pintar opciones de visualizacion (Filtros).
			echo '
				<tr class="fijo" id="Barra_filtros">
					<td colspan="14" align="center" style="border:solid 1px #2A5DB0;padding: 2px;background-color:#FFFFE4;">';
						pintar_opciones_visualizacion($todos_cco, 'I');
			echo '	</td>
				</tr>';

			// --> 	Inicio Modificacion: 2013-08-20 	-Jerson Trujillo.
			// --> 	Recorrer el array de los indicadores que se van a pintar, para obtener de cada uno si se calcula
			//		manual o automaticamente.
			$array_nombre_indicadores_ordenado = array();
			foreach ($array_nombre_indicadores as $codigo_indicador => $arr_meses_indic)
			{
				$Formula_indicador 		= $info_inidcadore[$codigo_indicador]['Formula'];
				$arr_variables_formu 	= explode('|', $Formula_indicador);

				// --> De la formula del indicador, recorrer cada una de las variables de su formula.
				foreach($arr_variables_formu as $indice => $variable_formula)
				{
					// --> Si aun no se le ha definido la modalidad de calculo, o si ya la tiene que sea diferente de automatica (A).
					if (!isset($info_inidcadore[$codigo_indicador]['ModalidadCalculo']) || $info_inidcadore[$codigo_indicador]['ModalidadCalculo'] != 'A')
					{
						// --> Limpiar la variable
						$variable_formula = trim($variable_formula);
						if(strstr($variable_formula, '['))
							$variable_formula = substr($variable_formula, 0, stripos($variable_formula, '['));

						if($variable_formula != '')
						{
							// --> 	Si la variable corresponde a un indicador o a una estadistica,
							//		en este caso el indicador se define como automatico (A).
							if($variable_formula{0} == 'E' || $variable_formula{0} == 'I')
							{
								$info_inidcadore[$codigo_indicador]['ModalidadCalculo'] = 'A';
								break 1;
							}
							else
							{
								if(array_key_exists($variable_formula, $arr_datos_automaticos))
								{
									$info_inidcadore[$codigo_indicador]['ModalidadCalculo'] = 'A';
									break 1;
								}
								else
									$info_inidcadore[$codigo_indicador]['ModalidadCalculo'] = 'M';
							}
						}
					}
					else
						break 1;
				}
				$array_nombre_indicadores_ordenado[$codigo_indicador] = $info_inidcadore[$codigo_indicador]['ModalidadCalculo'];
			}
			arsort($array_nombre_indicadores_ordenado);
			// --> 	Fin Modificacion: 2013-08-20 	-Jerson Trujillo.


			// --> Recorrer los indicadores para pintarlos
			$pintar_barra_estadisticas = true;
			$pintar_barra_indicadores  = true;
			foreach ($array_nombre_indicadores_ordenado as $codigo_indicador => $arr_meses_indic)
			{
				$arr_meses_indic  = $array_nombre_indicadores[$codigo_indicador];
				$num_fila++;
				$lista_calculados_completos[$num_fila] = 'si';
				$nombre 		  = $info_inidcadore[$codigo_indicador]['nombre'];
				$tipo_estadistica = $info_inidcadore[$codigo_indicador]['tipo'];
				$num_decimales	  = $info_inidcadore[$codigo_indicador]['num_dec'];
				$res_medicion	  = $info_inidcadore[$codigo_indicador]['res_med'];
				$perspectiva	  = $info_inidcadore[$codigo_indicador]['Perspectiva'];
				$perspectiva	  = (($perspectiva != '') ? $perspectiva : 'no_aplica');
				$jerarquia	      = $info_inidcadore[$codigo_indicador]['Jerarquia'];
				$jerarquia	  	  = (($jerarquia != '') ? $jerarquia : 'no_aplica');
				$caracteristica	  = $info_inidcadore[$codigo_indicador]['Caracteristica'];
				$caracteristica	  = (($caracteristica != '') ? $caracteristica : 'no_aplica');
				$grupo	  		  = $info_inidcadore[$codigo_indicador]['Grupo'];
				$grupo	  		  = (($grupo != '') ? $grupo : 'no_aplica');
				$centro_costos	  = $info_inidcadore[$codigo_indicador]['Cco'];
				$centro_costos	  = (($centro_costos != '') ? $centro_costos : 'no_aplica');
				$perspectiva	  = (($perspectiva != '') ? $perspectiva : 'no_aplica');
				$unidad_medida	  = $info_inidcadore[$codigo_indicador]['Unidad_medida'];
				$Ind_modificable  = $info_inidcadore[$codigo_indicador]['Modificable'];
				$Formula_calcular = $info_inidcadore[$codigo_indicador]['Formula'];
				$Formula_nombres  = $info_inidcadore[$codigo_indicador]['Formula_nombres'];


				if($pintar_barra_indicadores && $tipo_estadistica != 'on')
				{
					$pintar_barra_indicadores = false;
					echo '
					<tr class="fijo" id="selectable" >
						<td class="ano" colspan = "14" align="center" >INDICADORES</td>
					</tr>';
				}

				if($pintar_barra_estadisticas && $tipo_estadistica == 'on')
				{
					$pintar_barra_estadisticas = false;
					echo '
					<tr class="titulo_estad" id="selectable" >
						<td class="ano" colspan = "14" align="center" >ESTAD&Iacute;STICAS</td>
					</tr>';
				}

				$color_fila = color_fila($color_fila);
				$tooltip = "<table>
								<tr><td class=\"Encabezadotabla\" align=\"center\">RESPONSABLE DE LA MEDICIÓN</td></tr>
								<tr><td style=\"color: #000000;font-size: 10pt;align:center;\"><b>".ucfirst(strtolower($res_medicion))."</b></td></tr>
							</table>";

				// --> Si es el cuadro de mando de consulta.
				$mensaje = '';
				$img_para_cmi_consulta = '';
				$arr_componentes_formu = explode('|', $Formula_calcular);
				$arr_nombre_comp_formu = explode('|', $Formula_nombres);

				if(isset($cmi_consulta) && $cmi_consulta == 'on')
				{
					// --> Obtener si el indicador tiene configuracion erronea es decir, cuando algun componente de la formula de
					//	   el indicador es modificable pero el indicador no lo es.

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

					// --> Asignar imagen de modifcable o de alerta
					if($Ind_modificable == 'on')
						$img_para_cmi_consulta = "<img height='16' width='16' tooltip='si' title='<span style=\"color: #000000;font-size: 10pt;\">".(($tipo_estadistica == 'on') ? 'Estadística' : 'Indicador')." modificable</span>' src='../../images/medical/sgc/Refresh-128.png'>";
					elseif($mensaje != '')
						$img_para_cmi_consulta = "<img height='17' width='17' tooltip='si' title='<table><tr><td class=\"mes_tooltip\" align=\"center\"><img height=\"17\" width=\"17\" src=\"../../images/medical/sgc/Warning-32.png\"> Mensaje de alerta:</td></tr><tr><td align=\"center\" style=\"color: #000000;font-size: 9pt;\">".(($tipo_estadistica == 'on') ? 'Esta Estadística' : 'Este Indicador')." debe ser modificable ya que<br>las siguientes variables de su formula lo son:<br>".$mensaje."</td></tr></table>' src='../../images/medical/sgc/Warning-32.png'>";
				}
				
				$perspectivaF 			= "-".implode("-,-",explode(",", $perspectiva))."-";
				$jerarquiaF 			= "-".implode("-,-",explode(",", $jerarquia))."-";
				$caracteristicaF 		= "-".implode("-,-",explode(",", $caracteristica))."-";
				$grupoF 				= "-".implode("-,-",explode(",", $grupo))."-";
				$centro_costosF 		= "-".implode("-,-",explode(",", $centro_costos))."-";
				
				// --> Pintar tr del indicador
				echo "
					<tr id='".$num_fila."' class='".$color_fila."' ".(($tipo_estadistica == 'on') ? 'tipo_tr=estadistica' : 'tipo_tr=indicador')." perspectiva='".$perspectivaF."' jerarquia='".$jerarquiaF."' caracteristicas='".$caracteristicaF."' objetivos='".$grupoF."' centrosdecostos='".$centro_costosF."'>
						<td style='background-color: #FFFFFF;' >
							<span style='VISIBILITY:hidden;display:none'>".utf8_decode($nombre)."</span>
							<A name='".$codigo_indicador."'></A>
							<img width='20' height='19' style='background-color: #FFFFFF;cursor:pointer;' onClick='generar_tablero( \"normal\", \"".$codigo_indicador."\", \"0000-00-00|0000-00-00\", \"".utf8_decode($nombre)."\", \"".$unidad_medida."\" );' tooltip='si' title='<span style=\"color: #000000;font-size: 10pt;\">Graficar</span>' src='../../images/medical/root/chart.png'>&nbsp;<br>
							".$img_para_cmi_consulta."
							<span style='cursor:help;color:#2A5DB0;font-weight:bold' tooltip='si' title='<span style=\"color: #000000;font-size: 10pt;\">".(($info_inidcadore[$codigo_indicador]['ModalidadCalculo'] == 'A') ? 'Calculo automático' : 'Calculo manual')."</span>'>".$info_inidcadore[$codigo_indicador]['ModalidadCalculo']."</span>
						</td>
						<td style='text-align: left;cursor:default;' tooltip='si' title='".$tooltip."' id='hidden_nombre_indicador'>
						<b>".utf8_decode($nombre)."</b>
						</td>";

					foreach ($array_meses_pintados as $mes_pintado => $nom_mes)
					{
						// --> Tabla Tooltip con el mes en el que estoy posicionado y formula aplicada
						$tool_inf = "<table>
										<tr><td class=\"mes_tooltip\" align=\"center\">".$nom_mes."</td></tr>";

						if(array_key_exists($mes_pintado, $arr_meses_indic ))
						{
							$meses_y_permiso 	= explode('|', $arr_meses_indic[$mes_pintado]);
							$tipo_permiso		= $meses_y_permiso [2];
							$periodo_calcular 	= $meses_y_permiso [0].'|'.$meses_y_permiso [1];
							$row_resultado		= array();
							$id_movimiento 		= estado_indicador($mes_pintado, $codigo_indicador, $row_resultado, $cco_aplican_usuario, $tipo_permiso);

							// --> Periodo sin calcular
							if ($id_movimiento =='Sin Calcular')
							{
								$lista_calculados_completos[$num_fila] = 'no';
								$tool_inf.= "</table>";
								if($tipo_permiso == 'calcular')
								{
									if ($wperiodo_resaltar !='' && $wperiodo_resaltar==$periodo_calcular)
									{
										$style = "color: #3399FF;cursor:pointer;font-size: 10pt;align:center;";
										echo "<td tooltip='si' title='".$tool_inf."' align=center style='".$style."'>
													<b>Calculando</b> <br>
													<img width='20' height='20' border='0' src='../../images/medical/animated_loading.gif'>
											  </td>";
									}
									else
									{
										$style = "background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 1px solid #FFFF56; width: 54px; height:22px; align:center;";
										$accion_boton ='Medir';
										echo "<td tooltip='si' title='".$tool_inf."' align=center onclick='' style='cursor:pointer;'>
													<input type='submit' value='Calcular' onclick='medir_indicador(\"".$codigo_indicador."\", \"".$periodo_calcular."\")' style='".$style."'/>
											  </td>";

										//Esta lista es para guardar todos los codigos de los indicadores y estadisticas que se deben calcular, correspondientes al mes especifico,
										//Con el fin de poder realizar un recorrido automatico de todo el mes e ir calculando.
										$lista_indicadores[$mes_pintado].= $codigo_indicador.'|'.$periodo_calcular.'|'.$id_movimiento.'|-|';
									}
								}
								else
								{
									echo "<td tooltip='si' title='".$tool_inf."'  align='center'><img height='15' width='15' src='../../images/medical/root/info.png'></td>";
								}
							}
							// --> Periodo ya calculado
							else
							{
								$style = "color: #000000;cursor:pointer;font-size: 8pt;align:center;";

								// --> parametros para la semaforizacion
								$valor_result 		= formato_respuesta($row_resultado['Movres'], $num_decimales);
								$semaforizar		= (($row_resultado['Movdmi'] == '' || $row_resultado['Movmet'] == '' || $row_resultado['Movdsu'] == '') ? false : true);
								// --> 18-11-2019: Se cambia casting de int por float
								$desempe_min  		= (float)$row_resultado['Movdmi'];
								$meta_indicad 		= (float)$row_resultado['Movmet'];
								$desempe_sup  		= (float)$row_resultado['Movdsu'];
								$tipo_semafor 		= $row_resultado['Movsem'];
								$fecha_calc   		= $row_resultado['fecha'];
								$hora_calc    		= $row_resultado['hora'];
								$Formula_calculada  = $row_resultado['Movfin'];
								$OperacioCalcGener  = $row_resultado['Movorg'];
								$formu_aplica 		= (($OperacioCalcGener == 'O') ? 'Solo para ver detalle' : str_replace('|', '', $row_resultado['Movfap']));
								if($formu_aplica != '')
								{
									$tool_inf.= "<tr><td class=\"\" align=\"center\" style=\"font-family:Courier New;font-size: 9pt;\"><b>".$formu_aplica."</b></td></tr>";
								}
								else
								{
									$tool_inf.= "</table>";
								}

								if($semaforizar)
								{
									$formu_aplica = trim($formu_aplica);
									// --> 2017-02-27
									//		Si dentro de la formula está un 0/0 y es ascendente el resultado del indicador se reemplaza por 100%
									//		de lo contrario se deja el 0 % 
									if($formu_aplica != "" && strpos($formu_aplica, "0/0") && $tipo_semafor == "A")
										$valor_result = "100";
									
									
									// if($codigo_indicador == "I1157"){
										// echo "codigo_indicador:".$codigo_indicador;
										// echo "<br>valor_result:".$valor_result;
										// echo "<br>desempe_min:".$desempe_min;
										// echo "<br>meta_indicad:".$meta_indicad;
										// echo "<br>desempe_sup:".$desempe_sup;
									// }
									
									$img_semaforo = semaforizacion($valor_result, $desempe_min, $meta_indicad, $desempe_sup, $tipo_semafor);
								}
								else
									$img_semaforo = '';

								$img_semaforo = (($OperacioCalcGener == 'O') ? '' : '<div align="center">'.$img_semaforo.'</div>');
								// <-- Fin semaforizacion
								if ($wperiodo_resaltar !='' && $wperiodo_resaltar==$periodo_calcular)
								{
									$style = "cursor:pointer;font-size: 8pt;align:center;";
									echo "<td  tooltip='si' title='".$tool_inf."' align=center style='".$style."'>
												<span style='VISIBILITY:hidden;display:none'>".formato_respuesta($row_resultado['Movres'], $num_decimales)."</span>
												".$img_semaforo."
												<b>Detallando</b><br>
												<img width='12' height='12' border='0' src='../../images/medical/iconos/gifs/i.p.next[1].gif'>
										  </td>";
								}
								else
								{
									// --> Si tiene calculo temporal
									if($row_resultado['Movtem']=='on')
									{
										$tool_inf.= "</table>";
										// --> Muestro el resultado temporal y permito que al darle click se pueda ver el detalle
										echo "<td tooltip='si' title='".$tool_inf."' align=center style='".$style."' onclick='medir_indicador(\"".$codigo_indicador."\", \"".$periodo_calcular."\", \"\", \"".$id_movimiento."\")'>";
										echo 	$img_semaforo;

										if ($OperacioCalcGener != 'O')
											echo $valor_result." <span style='font-size: ".(($wcantidad_meses>10) ? "7" : "8")."pt'>".$row_resultado['Magnom']."<span>";
										else
											echo "Detallar";

										echo"	<br><img width='15' height='15' border='0' src='../../images/medical/reloj.gif'>";
										echo"</td>";

										if ($tipo_permiso=='calcular')
											$lista_indicadores[$mes_pintado].= $codigo_indicador.'|'.$periodo_calcular.'|'.$id_movimiento.'|-|';

									}
									// --> Si tiene calculo definitivo
									elseif($row_resultado['Movcal']=='on')
									{
										$arr_componentes_formu2 = explode('|', $Formula_calculada);
										$mensaje_desactual = '';
										if(calculo_desactualizado($arr_componentes_formu2, $arr_nombre_comp_formu, $periodo_calcular, $fecha_calc, $hora_calc, $mensaje_desactual))
										{
											$img_calculo_def = "<img width='15' height='15' border='0' tooltip='si' src='../../images/medical/sgc/grabar_des.png'>";
											$mensaje_usuario = (($tipo_permiso=='calcular') ? "Por favor calcule nuevamente ".(($codigo_indicador{0} == 'I') ? "este indicador" : "esta estadística").":" : "Calculo desactualizado:");
											$tool_inf.= "<tr><td class=\"mes_tooltip\" align=\"center\">&nbsp;<img width=\"15\" height=\"15\" src=\"../../images/medical/sgc/grabar_des.png\"> &nbsp;".$mensaje_usuario."</td></tr><tr><td class=\"\" align=\"center\" style=\"font-size: 9pt;\">".$mensaje_desactual."</td></tr></table>";
										}
										else
										{
											$img_calculo_def = "<img width='15' height='15' border='0' src='../../images/medical/root/grabar.png'>";
											$tool_inf.= "</table>";
										}
										echo "<td tooltip='si' title='".$tool_inf."' align=center style='".$style."' title='Click para ver detalle' onclick='medir_indicador(\"".$codigo_indicador."\", \"".$periodo_calcular."\", \"\", \"".$id_movimiento."\")'>";
										echo 	$img_semaforo;

										if ($OperacioCalcGener != 'O')
											echo 	$valor_result." <span style='font-size: ".(($wcantidad_meses>10) ? "7" : "8")."pt'>".$row_resultado['Magnom']."<span>";
										else
											echo "Detallar";

										echo	"<br>".$img_calculo_def."
											</td>";
									}
								}
							}
						}
						else
						{
							echo "<td tooltip='si' title='".$tool_inf."'></td>";
						}
					}
				echo "</tr>";
			}

			// --> Pintar opcion de Recorrer todo el mes
			if($wperiodo_resaltar=='') //Indica que esta desplegada la lista completa de todos los indicadores y datos
			{

				echo "	<tr class='tr_final'>
							<td class='ano' colspan='2'>Calcular Todos</td>";
							$style = "background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 1px solid #FFFF56; width: 54px; height:22px; align:center;";
							foreach ($array_meses_pintados as $mes_pintado => $nom_mes)
							{

								if(count($lista_indicadores[$mes_pintado])>0)
								{
									echo "
									<td class='ano'>
										<input type='submit' value='Calcular' onclick='recorrer_automatico(\"".$lista_indicadores[$mes_pintado]."\")' style='".$style."'/>
									</td>";
								}
								else
								{
									echo "<td class='ano'>&nbsp;</td>";
								}

							}
				echo'	</tr>';
			}
		}
		else
		{
			echo "<td></td><td colspan=12 class='ano'>...Sin indicadores ni estadisticas asignadas...</td>";
			echo "<script>$.unblockUI();</script>";
		}

		echo'
		</table>
		</div>
		';

		// --> Para el cuadro de mando de consulta se pinta un checkbox para pintar solo los inidcadores pendientes de calcular,
		//     este hidden es para crear un json con la lista de los id de los tr de los indicadores y estadisticas que ya estan
		//     calculados en todos los meses pintados, para luego con jquery hacerle un remove() si se checkeo la respectiva opción.
		$arr_ind_ya_calculados = array();
		foreach($lista_calculados_completos as $indice => $calculado_todo)
		{
			if($calculado_todo == 'si')
				$arr_ind_ya_calculados[$indice] = '';
		}
		echo "<input type='hidden' id='hidden_calc_completos' value='".json_encode($arr_ind_ya_calculados)."'>";
	}
	//--------------------------------------------
	//	FUNCION QUE ME CALCULA UN INDICADOR
	//--------------------------------------------
	function calcular_indicador($wperiodo_calcular, $codigo, $parametros_valores, $id_movimiento='', $lista_recorrido='', $mostrar_mensaje='', $llamado_recursivo = 'no', $foto_calculo_temp = '')
	{
		global $conex;
		global $wbasedato;
		global $array_meses;
		global $wuse;
		global $foto_calculo;
		$foto_calculo = '';
		global $foto_calculo_temporales;
		$foto_calculo_temporales = $foto_calculo_temp;
		global $parametros_seleccionados;
		$parametros_seleccionados = $parametros_valores;
		global $tipo_resultado_general;

		$array_parametros_ya_seleccionados = array();

		// --> Crear un hidden con los meses del periodo seleccionado, para resaltarlo
		$arr_fecha_per 	= explode('|', $wperiodo_calcular);
		$mes_ini 	   	= explode('-', $arr_fecha_per[0]);
		$mes_ini	   	= $mes_ini[1];
		$mes_ini	   	= ($mes_ini{0} == '0') ? $mes_ini{1} : $mes_ini;
		$mes_fin 	   	= explode('-', $arr_fecha_per[1]);
		$mes_fin	   	= $mes_fin[1];
		$mes_fin	   	= ($mes_fin{0} == '0') ? $mes_fin{1} : $mes_fin;
		$hidden_meses 	= '';
		while($mes_ini*1 <= $mes_fin*1)
		{
			$hidden_meses = ($hidden_meses == '') ? $array_meses[$mes_ini] : $hidden_meses.'|'.$array_meses[$mes_ini];
			$mes_ini++;
		}
		echo "<input type='hidden' id= 'hidden_meses_res' value='".$hidden_meses."'>";
		// <-- Fin crear hidden
		echo "<input type='hidden' id= 'hidden_tag_posicionar' value='".$codigo."'>";

		// --> Aqui entra si los parametros ya se han selecionado
		if($parametros_valores!='')
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
			//echo '<pre>';print_r($array_parametros_ya_seleccionados);echo '</pre>';
		}

		// --> Pintar la informacion basica del indicador o de las estadistica
		$formula_calcular 		= '';
		$formula_nombres  		= '';
		$nombre_magnitud  		= '';
		$tipo_estadistica 		= '';
		$numero_decimales 		= '';
		$resp_medicion	  		= '';
		$modifica_resulta 		= '';
		$tipo_resultado_general = '';

		pintar_infor_basica($codigo, $formula_calcular, $nombre_magnitud, $tipo_estadistica, $numero_decimales, $formula_nombres, $resp_medicion, $modifica_resulta, $llamado_recursivo, $tipo_resultado_general);

		//--------------------------
		// --> Zona de calculo
		//--------------------------

		//Indica que el indicador o la estadistica ya cuenta con algun movimiento, osea con algun calculo registrado para dicho periodo
		if($id_movimiento !='')
		{
			//Consultar Movimiento
			$q_movimiento = "SELECT *
							   FROM ".$wbasedato."_000009 as A, ".$wbasedato."_000006
							  WHERE A.id='".$id_movimiento."'
								AND Movmag = Magcod
							";
			$res_movimiento = mysql_query($q_movimiento, $conex) or die (mysql_errno() . $q_movimiento . " (Query: Consultar movimiento) " . mysql_error());
			$row_movimiento = mysql_fetch_array($res_movimiento);
		}
		//-------------------------
		// --> Pintar resultado
		//-------------------------
		if($id_movimiento !='' && $llamado_recursivo == 'no')
		{
			// --> Tipo del resultado general
			switch ($row_movimiento['Movorg'])
			{
				case 'C':
					$nom_tipo_res_gen = 'Consulta General';
					break;
				case 'S':
					$nom_tipo_res_gen = 'Sumatoria';
					break;
				case 'P':
					$nom_tipo_res_gen = 'Promedio';
					break;
				case 'O':
					$nom_tipo_res_gen = 'Sin cálculo general';
					break;
				default:
					$nom_tipo_res_gen = '';
					break;
			}

			echo '<br>
			<div class="borderDiv Titulo_azul" align=center>
				<div style="cursor:pointer;width:300px;" onclick="cerrar_seccion(\'div_resultados\');">
					<img width="10" height="10" src="../../images/medical/iconos/gifs/i.p.next[1].gif" />
					RESULTADO '.(($row_movimiento['Movtem']=='on') ? "TEMPORAL" : "" ).'
				</div>
			</div><br>';
			echo'
			<div class="borderDiv" align=center id="div_resultados"><br>
				<table width="70%" border="0" align="center" style="border: 1px solid #999999;">
					<tr class="EncabezadoTabla">
						<td align="center" style="font-size: 11pt;">
							Formula Aplicada:
						</td>
						<td align="center" style="font-size: 11pt;">
							Resultado General: &nbsp;
							<span style="font-family:Courier New;font-size: 11pt;">'.$nom_tipo_res_gen.'</span>
						</td>
					</tr>
					<tr align="center">
						<td>';
							if($row_movimiento['Movorg'] != 'O')
								info_formula_aplicada($wperiodo_calcular, $row_movimiento['Movfap'], $row_movimiento['Movfin']);
							else
								echo 'Detallar';
			echo'		</td>
						<td><b>'.(($row_movimiento['Movorg'] != 'O') ? formato_respuesta($row_movimiento['Movres'], $numero_decimales).' '.$row_movimiento['Magnom'] : 'Detallar' ).'</b></td>
					</tr>';
			//---------------------------------------------
			// Pintar analisis de resultados o explicacion
			//---------------------------------------------
			$num_rows_textarea = numero_columnas_textarea(utf8_decode($row_movimiento['Movana']), 50);
			echo "
					<tr>
						<td class='EncabezadoTabla' align='center' colspan='2' style='font-size: 11pt;'>
							An&aacute;lisis de Resultados
						</td>
					</tr>
					<tr>
						<td align='center'colspan='2'><br><textarea style='border: 1px solid #999999;' rows='".($num_rows_textarea-1)."' cols=50 disabled='disabled'>".utf8_decode($row_movimiento['Movana'])."</textarea><br><br></td>
					</tr>
					<tr id='ver_detalle' style='display:none'><td colspan='2' align='right' class='Titulo_azul' onClick='cerrar_seccion(\"contenedor_detalle\");' style='cursor:pointer;'><img width='10' height='10' src='../../images/medical/iconos/gifs/i.p.next[1].gif' />&nbsp; Ver resultado detallado</td></tr>
				</table><br>";
			//---------------------------------------------
			// Fin Pintar analisis de resultados
			//---------------------------------------------

			//-------------------------------------
			// --> Pintar resultados detallados
			//-------------------------------------

			// --> Consultar si existe resultado detallado
			$q_res_det = "SELECT Movdde, Movvde, Movres
							FROM ".$wbasedato."_000009
						   WHERE Movind = '".$row_movimiento['Movind']."'
							 AND Movfip = '".$row_movimiento['Movfip']."'
							 AND Movffp = '".$row_movimiento['Movffp']."'
							 AND Movgen != 'on'
						   ORDER BY Movvde
						";
			$res_res_det = mysql_query($q_res_det, $conex) or die (mysql_errno() . $q_res_det . " (Query: Consultar movimiento detallado) " . mysql_error());

			if(mysql_num_rows($res_res_det)>0)
			{
				// --> Activo la opcion de desplegar el detalle
				echo "<script>
					$('#ver_detalle').show();
					</script>";

				$array_resultados_det = array();
				$array_res_det_pintar = array();
				$cco_aplican_usuario  = obtener_cco_aplican_al_usuario();

				// --> Recorrer todos los resultados detallados y guardarlos en un array.
				while($row_res_det = mysql_fetch_array($res_res_det))
				{
					$array_resultados_det[trim($row_res_det['Movvde'])]['descripcion'] = $row_res_det['Movdde'];
					$array_resultados_det[trim($row_res_det['Movvde'])]['resultado']   = formato_respuesta($row_res_det['Movres'], $numero_decimales)." ".$row_movimiento['Magnom'];
				}

				// --> Filtrar los resultados detallados segun los centros de costos relacionados con el usuario.

				// --> Si el usuario loggeado NO es el responsable de calculo.
				if($resp_medicion != $wuse)
				{
					// --> Si el cco al que pertenece el usuario esta dentro del resultado detallado
					//     entonces muestro el resultado pero de ese detalle.
					if(array_key_exists($cco_aplican_usuario['cco_usuario'], $array_resultados_det))
						$array_res_det_pintar[$cco_aplican_usuario['cco_usuario']] = $array_resultados_det[$cco_aplican_usuario['cco_usuario']];

					// --> Si el o los centros de costos que coordina el usuario esta dentro del resultado detallado
					//     entonces muestro el resultado pero de ese detalle.
					foreach($cco_aplican_usuario['cco_coordina'] as $cco_cordina)
					{
						if(array_key_exists(trim($cco_cordina), $array_resultados_det))
							$array_res_det_pintar[trim($cco_cordina)] = $array_resultados_det[trim($cco_cordina)];
					}
				}

				// --> Si el usuario logueado es el responsable de calcular el indicador, entonces debo mostrar todos los resultados existentes.
				// --> Si no hay resultados detallados ya filtrados, entonces tambien muestro todos los resultados.
				if($resp_medicion == $wuse || count($array_res_det_pintar) == 0)
				{
					$array_res_det_pintar = $array_resultados_det;
				}

				// --> Pinto el detalle
				echo'	<div  id="contenedor_detalle" style="width:730;border: 1px solid #999999;display:none;">
							<table width="98%" id="table_detalle">
								<tr>
									<td align="left" style=" color: #000000;font-size: 10pt;font-weight: bold;">Graficar <img width="21" height="20" style="cursor:pointer" src="../../images/medical/root/chart.png" onclick="pintarGrafica()" /></td>
									<td align="right"><img width="10" height="10" onClick="cerrar_seccion(\'contenedor_detalle\');$(\'#contenedor_graficador\').hide();" src="../../images/medical/eliminar1.png" title="Cerrar" style="cursor:pointer;"></td></tr>
								<tr><td colspan="2" class="Encabezadotabla" align="center"><b>RESULTADO DETALLADO</b></td></tr>
								<tr class="parrafo_text">
									<td align="center">Nombre</td><td align="center">Resultado</td>
								</tr>';
							$color_fil 			  = 'Fila1';
							foreach($array_res_det_pintar as $valor_detalle => $arr_valores_res_det)
							{
								if($color_fil == 'Fila1')
									$color_fil = 'Fila2';
								else
									$color_fil = 'Fila1';

								echo "<tr class='".$color_fil."'><td>".$valor_detalle."-".$arr_valores_res_det['descripcion']."</td><td>".$arr_valores_res_det['resultado']."</td></tr>";
							}
				echo"		</table>
							<div align='center' id='contenedor_graficador'>
							</div><br>
						</div><br>";
			}
			//-------------------------------------
			// --> Fin resultados detallados
			//-------------------------------------
			echo "</div>";

			// --> Si  esta calculado temporalmente o tiene configurado en la ficha que se permite actualizar el resultado, puedo permitir que se re-calcule.
			if(($row_movimiento['Movtem']=='on' || $modifica_resulta == 'on') && $resp_medicion == $wuse)
			{
				echo '<br>
				<div class="borderDiv Titulo_azul" align=center>
					<div style="cursor:pointer;width:300px;" onclick="cerrar_seccion(\'div_resultados\');">
						<img width="10" height="10" src="../../images/medical/iconos/gifs/i.p.next[1].gif" />
						CALCULAR
					</div>
				</div><br>';
				echo '
				<div class="borderDiv" align=center id="div_resultados">
					<table width="60%" border="0" align="center" >
						<tr>
							<td><br></td>
						</tr>';
				calcular_formula($formula_calcular, $wperiodo_calcular, $array_parametros_ya_seleccionados, $nombre_magnitud, $codigo, $id_movimiento, $tipo_estadistica, $lista_recorrido, $mostrar_mensaje, $numero_decimales, $formula_nombres, $llamado_recursivo, $tipo_resultado_general);
				echo "
					</table>";
			}
		}
		//---------------------------
		// --> Fin pintar resultado
		//---------------------------
		else
		{
			if($llamado_recursivo == 'no')
			{
				echo '<br>
					<div class="borderDiv Titulo_azul" align=center>
						<div style="cursor:pointer;width:300px;" onclick="cerrar_seccion(\'div_resultados\');">
							<img width="10" height="10" src="../../images/medical/iconos/gifs/i.p.next[1].gif" />
							CALCULAR
						</div>
					</div><br>';
				echo '<div class="borderDiv" align=center id="div_resultados">
						<table width="60%" border="0" align="center" >
							<tr>
								<td><br></td>
							</tr>';
			}

			calcular_formula($formula_calcular, $wperiodo_calcular, $array_parametros_ya_seleccionados, $nombre_magnitud, $codigo, $id_movimiento, $tipo_estadistica, $lista_recorrido, $mostrar_mensaje, $numero_decimales, $formula_nombres, $llamado_recursivo, $tipo_resultado_general);

			if($llamado_recursivo == 'no')
			{
				echo "	</table>
				</div>
					";
			}
		}
		//--------------------------
		// <-- Fin Zona de calculo
		//--------------------------
	}
	//------------------------------------------------------------
	//	FUNCION QUE CALCULA EL VALOR DE LA FORMULA DEL INDICADOR
	//------------------------------------------------------------
	function calcular_formula($formula ,$wperiodo_calcular, $array_parametros_ya_seleccionados, $magnitud, $codigo_indicador, $id_movimiento='', $tipo_estadistica, $lista_recorrido, $mostrar_mensaje, $numero_decimales, $formula_nombres, $llamado_recursivo, $tipo_resultado_general)
	{
		global $conex;
		global $wbasedato;
		global $parametros_seleccionados;
		global $foto_calculo;
		global $foto_calculo_temporales;
		global $id_movimiento;
		global $lista_recorrido;
		global $mostrar_mensaje;
		global $color_fila;
		global $tipo_resultado_general;
		$array_formula_valores 	 = array();
		$array_nombres_y_valores = array();
		$indice = 0;
		$color_fila = 'Fila2';
		//$foto_calculo = '';						//Esta variable es para guardar los valores o los querys correspondientes de cada dato de la formula
		$parametros_ya_pintados = array();
		$pintar_boton_continuar = 'no';
		$parametros_agrupamiento = '';

		//---------------------------
		// Mensaje del recorrido
		//---------------------------
		if ($lista_recorrido!='' && $mostrar_mensaje!='no')
		{
			$cantidad_recorrido = explode('|-|', $lista_recorrido);
			$cantidad_recorrido = count($cantidad_recorrido)-1;
			echo "<tr><td colspan='2' align='center'>
					<div class='borderDiv FondoAmarillo' style='width: 300px;'>
						<img width='15' height='15' src='../../images/medical/root/info.png'>
						<b>Calculando Todos:</b><br>
						Calculos restantes : <b>".$cantidad_recorrido."</b></div>
					<br>
					</td></tr>";
		}
		//---------------------------
		// FIN mensaje del recorrido
		//---------------------------

		if($tipo_estadistica=='on')
			$calculando = 'Estadistica';
		else
			$calculando = 'Indicador';

		// --> Descomponer la formula en datos y calcular uno a uno
		$datos_formula   = explode('|', $formula);
		$nombres_formula = explode('|', $formula_nombres);
		foreach ($datos_formula as $indice_for => $codigo_dato)
		{
			if ($codigo_dato != '')
			{
				$tipo_variable = $codigo_dato[0];
				switch($tipo_variable)
				{
					// --> SI la variable corresponde a un indicador o una estadistica
					case 'I':
					case 'E':
					{
						$resultado = '';
						$analisis  = '';
						// --> Obtener el resultado del indicador o de la estadistica
						$respuesta = obtener_resultado($wperiodo_calcular, $codigo_dato, $resultado, $analisis, $codigo_indicador, $pintar_boton_continuar, $nombres_formula[$indice_for]);
						if($respuesta)
						{
							$foto_calculo.= $resultado.'|';
							$array_formula_valores[$indice] = $resultado;
						}
						else
						{
							$array_formula_valores[$indice] = '';
						}
						break;
					}
					// --> Si no es ni un indicador, ni una estadistica (Dato, Operador, Numero)
					default:
					{
						//Si se encuentra el caracter '[' dentro del codigo del dato, significa que el dato se
						//calculara pero para un filtro en especifico, configurada en su ficha.
						//Ejm: El codigo puede ser -> D20[1182], significa que el dato se clculara solo para el
						//centro de costos 1182
						if(strstr($codigo_dato, '['))
						{
							$detallado_por = strstr($codigo_dato, '[');
							$buscar = array('[', ']');
							$detallado_por = str_replace($buscar, '', $detallado_por);
							$codigo_dato = substr($codigo_dato, 0, stripos($codigo_dato, '['));
						}

						// --> En este array van quedando guardados los resultados de cada variable
						$param_agrupado = '';
						$pintar_nombre_y_res = 'no';
						$array_formula_valores[$indice] = calcular_dato($codigo_dato, $wperiodo_calcular, $array_parametros_ya_seleccionados, $calculando, $parametros_ya_pintados, $detallado_por, $codigo_indicador, $pintar_boton_continuar, $nombres_formula[$indice_for], $param_agrupado, $llamado_recursivo, $pintar_nombre_y_res);

						if($pintar_nombre_y_res == 'si')
						{
							$array_nombres_y_valores[$codigo_dato]['nombre'] = $nombres_formula[$indice_for];
							$array_nombres_y_valores[$codigo_dato]['valor']  = $array_formula_valores[$indice];
						}

						if($param_agrupado != '')
						{
							if ($parametros_agrupamiento == '')
								$parametros_agrupamiento.= $param_agrupado;
							else
								$parametros_agrupamiento.= '|'.$param_agrupado;
						}
						break;
					}
				}
				$indice++;
			}
		}
		// <-- Descomponer formula

		// --> Contar cuantos datos ya tienen su correspondiente valor asignado
		$numero_datos_con_valor = 0;
		foreach ($array_formula_valores as $valor_dato_asignado)
		{
			//	--> 2014-05-27: Jerson trujillo, se cambio el tipo de dato a string
			$valor_dato_asignado = (string)$valor_dato_asignado;
			if($valor_dato_asignado != '')
			{
				$numero_datos_con_valor++;
			}
		}

		// --> Si ya todos los datos tienen su correpondiente valor asignado entonces realizo un eval de la formula y
		//     pinto el resultado del indicador o del dato.
		if(count($array_formula_valores) == $numero_datos_con_valor)
		{
			// --> Primero pinto el nombre de los datos calculados con su correspondiente resultado
			foreach ($array_nombres_y_valores as $pintar_dat_cal)
			{
				$resultado_dato = 0;
				// --> 	Si el resultado es un array, debo sumar cada uno de sus valores y dependiendo del tipo de
				//		resultado general que tenga configurado en la ficha (Sumatoria, promedio, generar consulta), lo realizo.
				if(is_array($pintar_dat_cal['valor']))
				{
					// --> Si dentro de los valores existe el resultado_general, lo quito del array.
					if( isset($pintar_dat_cal['valor']['RESULTADO_GENERAL']) )
					{
						$resultado_dato_temp = $pintar_dat_cal['valor']['RESULTADO_GENERAL'];
						unset($pintar_dat_cal['valor']['RESULTADO_GENERAL']);
					}

					foreach($pintar_dat_cal['valor'] as $res_val)
					{
						if($res_val != '')
							$resultado_dato+=$res_val;
					}

					// --> Determinar el resultado general segun el tipo. (Modificaion del 2013-10-17).
					switch($tipo_resultado_general)
					{
						// --> Tipo promedio
						case 'P':
						{
							$resultado_dato = $resultado_dato/count($pintar_dat_cal['valor']);
							break;
						}
						// --> Tipo consulta general
						case 'C':
						{
							$resultado_dato = $resultado_dato_temp;
							break;
						}
						// --> Tipo sumatoria o el default
						default:
						break;
					}
				}
				else
					$resultado_dato = $pintar_dat_cal['valor'];

				echo "
				<tr class='parrafo_text'>
					<td colspan='2' align='center'><b>".utf8_decode($pintar_dat_cal['nombre'])."</b></td>
				</tr>
				<tr>
					<td class='Encabezadotabla' width='35%'>Resultado:</td><td class='Fila1' align='center'>".$resultado_dato."</td>
				</tr>";
			}

			echo "<tr><td colspan='2'><br></td></tr>";
			echo'<tr><td colspan="2">
			<div class="borderDiv Titulo_azul" align=center>RESULTADO '.(($tipo_estadistica=='on') ? 'DE LA ESTADíSTICA': 'DEL INDICADOR').':   <p style="color:#000000;">';
				$formula_a_ejecutar = '';
				$foto_formu_valores = '';
				$formula_eval  		= '$formula_a_ejecutar = @(';
				$plantilla_formula  = '';
				$indice_contene 	= 0;
				$array_contenedor   = array();
				$array_formul_detal = array();

				// --> Recorrer el valor resultante de cada dato
				foreach ($array_formula_valores as $valor_formula)
				{
					$valor_detalle = 0;
					if(is_array($valor_formula))
					{
						// --> Si dentro de los valores existe el resultado_general, lo quito del array.
						if( isset($valor_formula['RESULTADO_GENERAL']) )
						{
							$valor_detalle_temp = $valor_formula['RESULTADO_GENERAL'];
							unset($valor_formula['RESULTADO_GENERAL']);
						}

						foreach($valor_formula as $res_agrupado_por => $res_valor)
						{
							if($res_valor != '')
								$valor_detalle+=$res_valor;
						}

						// --> Determinar el resultado general segun el tipo (Modificaion del 2013-10-17).
						switch($tipo_resultado_general)
						{
							// --> Tipo promedio
							case 'P':
							{
								$valor_detalle = $valor_detalle/count($valor_formula);
								break;
							}
							// --> Tipo consulta general
							case 'C':
							{
								$valor_detalle = $valor_detalle_temp;
								break;
							}
							// --> Tipo sumatoria o el default
							default:
							break;
						}

						$formula_eval.=$valor_detalle;
						$foto_formu_valores.= ($foto_formu_valores == '') ? $valor_detalle : '|'.$valor_detalle ;

						// --> Esto es para poder calcular la formula pero por cada detalle
						$array_contenedor[$indice_contene] = $valor_formula;
						$plantilla_formula.='ARRAY'.$indice_contene.'N';
						$indice_contene++;
					}
					else
					{
						$plantilla_formula.=$valor_formula;
						$formula_eval.=$valor_formula;
						$foto_formu_valores.= ($foto_formu_valores == '') ? $valor_formula : '|'.$valor_formula ;
					}
				}
				// --> Ejecutar el eval de la formula general y pintar el resultado
				$formula_eval.= ');';
				eval($formula_eval);
				$resultado_a_pintar = formato_respuesta($formula_a_ejecutar, $numero_decimales);
				$resultado_a_pintar = explode(',', $resultado_a_pintar);

				if($resultado_a_pintar[1] == '00')
				{
					$resultado_a_pintar = $resultado_a_pintar[0];
				}
				else
				{
					$resultado_a_pintar = formato_respuesta($formula_a_ejecutar, $numero_decimales);
				}

				echo str_replace('|', '', $foto_formu_valores).' = <span style="color:#539B2D">'.$resultado_a_pintar.' '.$magnitud.'</span></p>';

				// --> Si el tipo de resultado general en la ficha es "Sin cálculo general", coloco un mensaje de explicacion
				if($tipo_resultado_general == 'O')
				{
					echo '
						<span style=" color: #000000;font-size: 10pt;">
							Nota: El resultado general '.(($tipo_estadistica=='on') ? 'de la estadística': 'del indicador').' no será<br>visible, solo se podrá observar el detalle.
						</span><br><br>';
				}

				// --> Calcular la formula pero para los detalles
				if(count($array_contenedor) > 0)
				{
					$arr_parametros_agrupamiento = explode('|', $parametros_agrupamiento);

					if (count($arr_parametros_agrupamiento) >= 1)
					{
						$realizar_detalle = 'no';
						//  --> Si existe mas de un parametro de agrupamiento debo validar q todos ellos sean iguales
						if (count($arr_parametros_agrupamiento) > 1)
						{
							$realizar_detalle = 'si';
							$param_anterior   = $arr_parametros_agrupamiento[0];
							for($w=1; $w < count($arr_parametros_agrupamiento);$w++)
							{
								if($arr_parametros_agrupamiento[$w] != $param_anterior)
								{
									$realizar_detalle = 'no';
									break;
								}
								else
									$param_anterior = $arr_parametros_agrupamiento[$w];
							}
						}
						else
							$realizar_detalle = 'si';

						if($realizar_detalle == 'si')
						{
							// --> Reemplazar los valores del detalle en cada formula
							foreach($array_contenedor as $indice => $arr_detalle)
							{
								foreach($arr_detalle as $cod_detalle => $result_detalle )
								{
									if (!isset($array_formul_detal[$cod_detalle]))
									{
										$formula_detalle = str_replace('ARRAY'.$indice.'N', $result_detalle, $plantilla_formula);
										$array_formul_detal[$cod_detalle] = $formula_detalle;
									}
									else
									{
										$formula_detalle = str_replace('ARRAY'.$indice.'N', $result_detalle, $array_formul_detal[$cod_detalle]);
										$array_formul_detal[$cod_detalle] = $formula_detalle;
									}
								}
							}

							// --> verificar que no hayan quedado valores sin asignar
							foreach($array_formul_detal as $cod_detalle => $formula_detalle)
							{
								$pos = strpos('--'.$formula_detalle, 'ARRAY');
								// --> Si existe la palabra ARRAY en la formula significa que no hay valor para ese detalle
								if($pos)
								{
									// --> Le asigno un valor de cero (0)
									foreach($array_contenedor as $indice => $arr_detalle)
									{
										if(!array_key_exists($cod_detalle, $arr_detalle))
										{
											$formula_detalle = str_replace('ARRAY'.$indice.'N', '0', $formula_detalle);
											$array_formul_detal[$cod_detalle] = $formula_detalle;
										}
									}
								}
							}
							//echo '<br><br><pre>';print_r($array_formul_detal);echo '</pre>';

							// --> Creo un hidden con un json de los resultados detallados
							echo "<input type='hidden' id='hidden_resultados_detallados' name='hidden_resultados_detallados' value='".json_encode($array_formul_detal)."'>";
						}
					}
				}
				//echo '<pre>';print_r($array_formul_detal);echo '</pre>';
				//---------------------------------------------
				// Pintar analisis de resultados o explicacion
				//---------------------------------------------
				echo "
					<table>
						<tr><td class='Encabezadotabla' align='center'>".(($calculando=='Indicador')? 'An&aacute;lisis de resultados': 'Explicaci&oacute;n')."</td></tr>
						<tr><td class='Fila2' align='center'>
							<textarea rows=2 cols=50 name='analisis' id='analisis' ".(($calculando=='Indicador')? "onmouseover='validar(this);' onblur='validar(this)' " : "").">".utf8_decode($row_movimiento['Movana'])."</textarea>
							</td>
						</tr>
						<tr>
							<td><div id='div_analisis' style='display:none;' ></div></td>
						</tr>
					</table><br>";

				//---------------------------------------------
				// Fin Pintar analisis de resultados
				//---------------------------------------------
				$foto_calculo.=$foto_calculo_temporales;
				// --> Hidden para guardar la foto del calculo
				echo "<input type='hidden' id='hidden_foto_calculo' value='".str_replace("'", '"', $foto_calculo)."'>";
				// --> Pintar botones de guardar y nuevo
				echo '
				<div name="boton_guardar" id="boton_guardar" class="fila2"  onclick="guardar_movimiento(\''.$id_movimiento.'\', \''.$codigo_indicador.'\', \''.$wperiodo_calcular.'\', \''.$foto_formu_valores.'\', \''.$formula_a_ejecutar.'\', \''.$lista_recorrido.'\', \''.$calculando.'\');" title="Guardar" style="display:inline;width:170px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;">
					<b>Guardar Resultado</b>
					<img width="14" height="14" border="0" style="cursor:pointer;"  src="../../images/medical/root/grabar.png">
				</div>&nbsp;&nbsp;&nbsp;
				<div name="boton_guardar_no" id="boton_guardar_no" class="fila2"  onclick="medir_indicador(\''.$codigo_indicador.'\', \''.$wperiodo_calcular.'\', \'\', \''.$id_movimiento.'\', \''.$lista_recorrido.'\', \''.$tip_permiso_temporal.'\')" title="calcular" style="display:inline;width:170px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;">
					<b>Nuevo Calculo</b>
					<img width="13" height="13" border="0" style="cursor:pointer;"  src="../../images/medical/root/suspender.png">
				</div><br><br>
			</div>
			</td></tr>
			';
			exit();
		}
		else
		{
			if ($llamado_recursivo == 'no')
			{
				echo "
				<tr><td align='center' colspan='2'>
				<div style='display:none;width:400px' name='div_mensaje_validacion' id='div_mensaje_validacion'></div>
				<br>
				<div id='boton_calcular' name='boton_calcular' class='fila2' onclick='ejecutar_indicador(\"".$codigo_indicador."\",\"".$wperiodo_calcular."\", \"".$id_movimiento."\", \"".$lista_recorrido."\", \"".$mostrar_mensaje."\")' title='Calcular el indicador' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;'>
					<b>Calcular</b>
					<img width='14' height='14' style='cursor:pointer;'  src='../../images/medical/root/grabar.png'>
				</div>
				<div id='msj_calculando' style='display:none;color: #000000;font-family: verdana;font-size: 8pt;font-weight: bold;'>
					<br>
					Calculando<br>
					<img style='cursor:pointer;' title='Cerrar formulario' src='../../images/medical/ajax-loader11.gif'>
				</div>
				</td></tr>";
			}

			if ($lista_recorrido!='' && $mostrar_mensaje!='no' && $pintar_boton_continuar == 'si')
			{
				echo"
				<tr><td align='center' colspan='2'>
				<div class='fila2' onclick='recorrer_automatico(\"".$lista_recorrido."\")' title='Continuar con el siguiente calculo' style='width:150px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;'>
					<b>Siguiente calculo</b>
					<img width='15' height='15' border='0' style='cursor:pointer;'  src='../../images/medical/citas/adelante.jpg'>
				</div>
				</td></tr>";
			}
		}

	}

	//-------------------------------------------
	//	FUNCION QUE CALCULA EL VALOR DE UN DATO
	//-------------------------------------------
	function calcular_dato($codigo_dato, $wperiodo_calcular, $array_parametros_ya_seleccionados, $calculando, &$parametros_ya_pintados, $detallado_por='', $codigo_indicador, &$pintar_boton_continuar, $nombre_dato, &$param_agrupado, $llamado_recursivo, &$pintar_nombre_y_res)
	{
		global $conex;
		global $wbasedato;
		global $color_fila;
		global $parametros_seleccionados;
		global $foto_calculo;
		global $foto_calculo_temporales;
		global $cod_ind;
		global $id_movimiento;
		global $lista_recorrido;
		global $mostrar_mensaje;
		global $tipo_resultado_general;
		$cod_ind = $codigo_indicador;
		//Consulto las propiedades del dato
		$q_inf_dato = "SELECT *
						 FROM ".$wbasedato."_000010
						WHERE Datcod = '".$codigo_dato."'
					";
		$res_inf_dato = mysql_query($q_inf_dato, $conex) or die (mysql_errno() . $q_inf_dato . " (Query: Consultar informacion del dato) " . mysql_error());
		if($row_inf_dato = mysql_fetch_array($res_inf_dato))
		{
			$valor_dato		= $row_inf_dato['Datval'];
			$param_agrupado	= trim($row_inf_dato['Datsub']);		//Codigo del parametro por el cual se debe agrupar el dato
			$origen_dato	= $row_inf_dato['Datdsn'];
			//$nombre_dato	= $row_inf_dato['Datnom'];

			if($row_inf_dato['Datnum']=='on')
				$tipo_dato = 'numero';
			elseif($row_inf_dato['Datope']=='on')
					$tipo_dato = 'operador';
				elseif($row_inf_dato['Datcon']=='on')
						$tipo_dato = 'constante';
					elseif($row_inf_dato['Dataut']=='on')
							$tipo_dato = 'automatico';
						else
							$tipo_dato = 'no_automatico';

			switch($tipo_dato)
			{
				// --> Si el dato es de alguno de estos tres tipos, no necesita ningun calculo extra
				//	   ya que su valor real es el mismo al que tiene el campo 'Datval'.
				case 'numero':
				case 'operador':
				{
					$foto_calculo.= $valor_dato.'|';
					return $valor_dato;
					break;
				}
				case 'constante':
				{
					// --> Pintar titulo del dato, solo lo pinto cuando no hayan parametros seleccionados es decir cuando se entra por primera vez
					if(count($array_parametros_ya_seleccionados) == 0)
						echo "<tr class='parrafo_text'><td colspan='2' align='center'><b>".utf8_decode($nombre_dato)."</b></td></tr>";

					$pintar_nombre_y_res = 'si';
					$foto_calculo.= $valor_dato.'|';
					return $valor_dato;
					break;
				}
				//Indica que el dato contiene un query a ejecutar
				case 'automatico':
				{
					// --> Pintar titulo del dato, solo lo pinto cuando no hayan parametros seleccionados es decir cuando se entra por primera vez
					if(count($array_parametros_ya_seleccionados) == 0)
						echo "<tr class='parrafo_text'><td colspan='2' align='center'><b>".utf8_decode($nombre_dato)."</b></td></tr>";

					$resultado = '';
					$analisis  = '';
					$respuesta_dato_query = calcular_query_de_dato($codigo_dato, $valor_dato, $array_parametros_ya_seleccionados, $parametros_ya_pintados, $wperiodo_calcular, $origen_dato, $param_agrupado,  $detallado_por, 'no');
					if($respuesta_dato_query!='no_hay_resultado')
					{
						$pintar_nombre_y_res = 'si';
						return $respuesta_dato_query;
					}
					break;
				}
				case 'no_automatico':
				{
					if(array_key_exists( $codigo_dato, $array_parametros_ya_seleccionados))
					{
						$foto_calculo.= $array_parametros_ya_seleccionados[$codigo_dato].'|';
						$pintar_nombre_y_res = 'si';
						return $array_parametros_ya_seleccionados[$codigo_dato];
					}
					else
					{
						echo "
						<tr class='parrafo_text'><td colspan='2' align='center'><b>".utf8_decode($nombre_dato)."</b></td></tr>" ;
						echo "
								<td class='Encabezadotabla' width='35%'>".utf8_decode($row_inf_dato['Datexp'])."</td>
								<td align='center' class='Fila1'>
								<input type='text' name='".$codigo_dato."' id='".$codigo_dato."'  size='30' rel='parametro' tipo_cap='texto'>
								<br><font size='1'><b>Campo num&eacute;rico, separador decimal (.)</b></font>
								</td>";
					}
					break;
				}
			}
		}
	}
	//---------------------------------------------------
	//	FUNCION QUE CALCULA EL VALOR DE UN PARAMETRO
	//---------------------------------------------------
	function calcular_parametro ($cod_parametro, $wperiodo_calcular, $detallado_por='', $id_input)
	{
		global $conex;
		global $wbasedato;
		global $color_fila;

		$q_inf_param = "SELECT *
						  FROM ".$wbasedato."_000012, ".$wbasedato."_000013
						 WHERE Parcod = '".$cod_parametro."'
						   AND Partcp = Tcpcod
					";
		$res_inf_param = mysql_query($q_inf_param, $conex) or die (mysql_errno() . $q_inf_param . " (Query: Consultar informacion del parametro2) " . mysql_error());
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
					$where_param	= '';

					if($row_inf_param['Parcon']!='')
					{
						$where_param 	= 	' WHERE '.$row_inf_param['Parcon'];
					}
					//Fin Armar query

					$query_parametro = $select_param.$from_param.$where_param;
					$valores_parametro = array();

					ejecutar_query($query_parametro, $dsn_paramet, $valores_parametro, 'parametro');

					if(count($valores_parametro) > 0)
						pintar_captura_parametro ($tipo_captura, $id_input, $wperiodo_calcular, $row_inf_param, $valores_parametro, $detallado_por);
				}
				else
				{
					pintar_captura_parametro ($tipo_captura, $id_input, $wperiodo_calcular, $row_inf_param, '');
				}
			}
		}
		else
		{
			echo "
			<tr class='FondoAmarillo'><td colspan=2 align=center>!!! Por favor informar a el area de informatica la siguiente inconsistencia!!!</tr></td>
			<tr><td class='Encabezadotabla'>Codigo Parametro:</td><td class='Fila1'>".$cod_parametro."</td></tr>
			<tr><td class='Encabezadotabla'>Consulta:</td><td class='Fila2'>".$q_inf_param."</td></tr>
			<tr><td class='Encabezadotabla'>Incosistencia:</td><td class='Fila1'>El parametro no existe</td></tr>";
		}
	}
	//---------------------------------------------------
	//	FUNCION QUE PINTA LA CAPTURA DE UN PARAMETRO
	//---------------------------------------------------
	function pintar_captura_parametro ($tipo_captura, $cod_parametro, $wperiodo_calcular, $row_inf_param, $lista_valores, $detallado_por='')
	{
		global $color_fila;
		//echo "<input type='hidden' name='' id='' pos_i='".$posicion_inicial."'  pos_f='".$posicion_final."'>";
		switch($tipo_captura)
		{
			case 'texto':
			{
				echo "
					<tr class='".$color_fila."'>
						<td width=30% class='encabezadoTabla'>".$row_inf_param['Pardes'].":</td>
						<td align='center'>" ;
				echo 		"<input type='text' name='".$cod_parametro."' id='".$cod_parametro."' value='".$row_inf_param['Parvde']."' rel='parametro' tipo_cap='".$tipo_captura."'/>
							<br><font size='1'><b>Campo num&eacute;rico, separador decimal (.)</b></font>
							</td>
					</tr>
					";
				break;
			}
			case 'calendario':
			{
				$wperiodo_calcular = explode('|', $wperiodo_calcular);
				$ocultar_calendario = '';

				//Asignar valor por defecto
				if ($row_inf_param['Parfip'] == 'on')			//Parametro que me indica que por defecto es la fecha inicial del periodo
				{
					$fecha_defecto = $wperiodo_calcular[0];
					// Esto es para validar que si se esta calculando un indicador de un periodo que ya termino, oculto
					// la seleccion de fechas, ya que se calculara con las fechas por defecto osea las fechas del periodo
					if(strtotime(date('Y-m-d')) > strtotime($wperiodo_calcular[1]))
						$ocultar_calendario = 'none';
				}
				elseif($row_inf_param['Parfip'] == 'off')		//Por defecto es la fecha final del periodo
					{
						$fecha_defecto = $wperiodo_calcular[1];
						if(strtotime(date('Y-m-d')) > strtotime($wperiodo_calcular[1]))
							$ocultar_calendario = 'none';
					}
					else
						$fecha_defecto = date('Y-m-d');	//Por defecto es la fecha que hayan seleccionado por defecto cuando crearon el parametro

					echo "
						<tr class='".$color_fila."' style='display: ".$ocultar_calendario.";'>
							<td width=30% class='encabezadoTabla'>".$row_inf_param['Pardes'].":</td>
							<td align='center'>" ;
					echo 		campoFechaDefecto_local($cod_parametro, $fecha_defecto);
					echo 	"</td>
						</tr>
						";
				break;
			}
			case 'seleccion':
			{
				echo "
					<tr class='".$color_fila."'>
						<td width=30% class='encabezadoTabla'>".$row_inf_param['Pardes'].":</td>
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
					switch($detallado_por)
					{
						case '':
						{
							echo "<option value='%' ".$seleccion_defecto." >TODOS</option>";
							foreach($lista_valores as $codigo_val => $nombre_val)
							{
								if(in_array($codigo_val, $array_valores_defecto, TRUE))
									$seleccion_defecto = 'SELECTED';
								else
									$seleccion_defecto = '';
								echo "<option value='".$codigo_val."' ".$seleccion_defecto." >".$nombre_val."</option>";
							}
							break;
						}
						case '*':
						{
							echo "<option value='%' 'SELECTED' >TODOS</option>";
							break;
						}
						default:
						{
							echo "<option value='".$detallado_por."' 'SELECTED' >".$lista_valores[$detallado_por]."</option>";
							break;
						}
					}
				}
				echo"	</td>
					</tr>";
				break;
			}
			case 'radio':
			{
				echo "
					<tr class='".$color_fila."'>
						<td width=30% class='encabezadoTabla'>".$row_inf_param['Pardes'].":</td>
						<td align='center'>";
				if($lista_valores!='')
				{
					unset($array_valores_defecto);
					$array_valores_defecto = explode(',',$row_inf_param['Parvde']);
					echo "<table style='width:95%;color: #000000;font-size: 8pt;font-family: verdana;font-weight: bold;'>";

					if($lista_valores!='')
					{
						switch($detallado_por)
						{
							case '':
							{
								$x=2;
								if(in_array('*', $array_valores_defecto))
								$seleccion_defecto = 'CHECKED';
								else
									$seleccion_defecto = '';
								echo "<tr><td style='width:50%' NOWRAP><input type='radio' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='%' ".$seleccion_defecto." >Todos</td>";

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
								break;
							}
							case '*':
							{
								echo "<tr><td style='width:50%' NOWRAP><input type='radio' rel='parametro' CHECKED tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='%' >Todos</td></tr>";
								break;
							}
							default:
							{
								echo "<tr><td style='width:50%' NOWRAP><input type='radio' rel='parametro' CHECKED tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='".$detallado_por."' >".$lista_valores[$detallado_por]."</td></tr>";
								break;
							}
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
					<tr class='".$color_fila."'>
						<td width=30% class='encabezadoTabla'>".$row_inf_param['Pardes'].":</td>
						<td align='center'>";
				if($lista_valores!='')
				{
					unset($array_valores_defecto);
					$array_valores_defecto = explode(',',$row_inf_param['Parvde']);
					$x=1;
					echo "<table style='width:95%;color: #000000;font-size: 8pt;font-family: verdana;font-weight: bold;'>";

					if($lista_valores!='')
					{
						switch($detallado_por)
						{
							case '':
							{
								$x=2;
								if(in_array('*', $array_valores_defecto))
								$seleccion_defecto = 'CHECKED';
								else
									$seleccion_defecto = '';
								echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' rel='parametro' tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='%' ".$seleccion_defecto." >Todos</td>";

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
								break;
							}
							case '*':
							{
								echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' rel='parametro' CHECKED tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='%' >Todos</td></tr>";
								break;
							}
							default:
							{
								echo "<tr><td style='width:50%' NOWRAP><input type='checkbox' rel='parametro' CHECKED tipo_cap='".$tipo_captura."' name='".$cod_parametro."' id='".$cod_parametro."' value='".$detallado_por."' >".$lista_valores[$detallado_por]."</td></tr>";
								break;
							}
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
	//-------------------------------------------------------------------------------------------------------------
	//		FUNCION CALCULAR QUERY DE UN DATO
	//-------------------------------------------------------------------------------------------------------------
	function  calcular_query_de_dato($codigo_dato, $valor_dato, $array_parametros_ya_seleccionados, &$parametros_ya_pintados, $wperiodo_calcular, $origen_dato, $param_agrupado, $detallado_por='', $tabla_temporal)
	{
		global $cod_ind;
		global $wbasedato;
		global $conex;
		global $parametros_seleccionados;
		global $foto_calculo;
		global $foto_calculo_temporales;
		//echo "<br>entro=".$foto_calculo;
		global $id_movimiento;
		global $lista_recorrido;
		global $mostrar_mensaje;
		global $tipo_resultado_general;
		$ejecutar_query = 'si';

		// --> Armo el query del dato el cual viene separado por |, asi = 'el_select|el_from|el_where'
		$arr_secciones_query = explode('|', $valor_dato);
		$select = $arr_secciones_query[0];
		$from 	= $arr_secciones_query[1];
		$where 	= ' '.$arr_secciones_query[2];
		// <-- Fin armar query

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
					calcular_query_de_dato($tabla, $query_temporal, $array_parametros_ya_seleccionados, $parametros_ya_pintados, $wperiodo_calcular, $dsn_temporal, '', '', 'si');
					$ejecutar_query = 'no';
				}
			}
		}

		// --> Revisar si existen parametro en el where.
		//     Debo recorrer toda la cadena del where, caracter por caracter, para mirar si existe algun parametro
		if ($where != '')
		{
			$hacer_agrupacion = 'NO';
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
			if(count($array_parametros) > 0)
			{
				foreach($array_parametros as $indi => $cod_parametro)
				{
					// --> Si el parametro tiene que calcularce por un valor especifico, lo concateno con ese valor de detalle, ya que
					//	   puede que se deba pintar el mismo parametro pero con otro valor de detalle, entonces les modifico el nombre
					//     para poder diferenciarlos.
					if($cod_parametro==$param_agrupado && $detallado_por != '')
						$cod_parametro_new = $cod_parametro.'->'.$detallado_por;
					else
						$cod_parametro_new = $cod_parametro;


					// --> Si el parametro ya se selcciono, adapto el where con el valor escogido
					if(array_key_exists( $cod_parametro_new , $array_parametros_ya_seleccionados))
					{
						//--> Obtener si el parametro es referenciado
						$q_param_ref = "SELECT Tcpref
										  FROM sgc_000012, sgc_000013
										 WHERE Parcod = '".$cod_parametro."'
										   AND Partcp = Tcpcod
										";
						$res_param_ref = mysql_query($q_param_ref, $conex) or die (mysql_errno() . $q_param_ref . " (Query: Informacion basica parametro) " . mysql_error());
						$row_param_ref = mysql_fetch_array($res_param_ref);

						// --> Si el parametro es referenciado
						if($row_param_ref['Tcpref'] == 'on')
						{
							$valores_parametro_seleccionado = explode (',' ,$array_parametros_ya_seleccionados[$cod_parametro_new]);

							// --> Si cuando seleccionaron los parametros del dato, escogieron mas de una opcion.
							if (count($valores_parametro_seleccionado) > 1)
							{
								$where = insertar_comparacion_in($cod_parametro, $valores_parametro_seleccionado, $where);
							}
							else
							{
								// --> Si el valor que seleccionaron para el parametro es igual a % significa que se selecciono la opcion 'TODOS'
								if($array_parametros_ya_seleccionados[$cod_parametro_new] == '%')
								{
									// --> Hidden para saber por q parametro se agrupo el dato
									echo "<input type='hidden' name='hidden_parametro_groupby' id='hidden_parametro_groupby' value='".$param_agrupado."' >";
									// --> Si este parametro es el por el que se debe agrupar, debo modificar el query agregandole un group by.
									if($param_agrupado != '' && $param_agrupado==$cod_parametro)
									{
										$campo_comparacion = campo_para_group_by($where, $cod_parametro);
										$hacer_agrupacion = 'SI';

										// --> Modifico el where, para agregarle un group by
										$where.= ' GROUP BY '.$campo_comparacion;

										// --> Modifico el select para agregarle el campo
										$select.= ' ,'.$campo_comparacion;

										// --> Esto indica que el resultado general del dato, osea el total no es ni una suma
										//	   ni un promedio, sino ejecutar el mismo query pero sin group by. Por eso aca creo
										//	   una copia del group by que se agrego, para luego volver a quitarcelo al query
										//     y dejarlo sin group by, para nuevamente ejecutarlo. (Modificaion del 2013-10-17).
										if($tipo_resultado_general== 'C')
										{
											$where_quitar 	= ' GROUP BY '.$campo_comparacion;
											$select_quitar	= ' ,'.$campo_comparacion;
										}
									}

									// --> Reemplazo la comparacion normal del query, por un IN con todos los posibles valores que contenga el parametro
									$array_valores_parametro = valores_parametro($cod_parametro);

									foreach ($array_valores_parametro as $cod_valor => $nom_valor)
									{
										$new_array_valores_parametro[] = $cod_valor;
									}

									$where = insertar_comparacion_in($cod_parametro, $new_array_valores_parametro, $where);
								}
								// --> Se selecciono una sola opcion
								else
								{
									// --> Hidden para saber por q parametro se agrupo el dato
									echo "<input type='hidden' name='hidden_parametro_groupby' id='hidden_parametro_groupby' value='".$param_agrupado."' >";
									// --> Si este parametro es el por el que se debe agrupar, debo modificar el query agregandole un group by.
									if($param_agrupado != '' && $param_agrupado==$cod_parametro)
									{
										$campo_comparacion = campo_para_group_by($where, $cod_parametro);
										$hacer_agrupacion = 'SI';

										// --> Modifico el where, para agregarle un group by
										$where.= ' GROUP BY '.$campo_comparacion;

										// --> Modifico el select para agregarle el campo
										$select.= ' ,'.$campo_comparacion;

										if($tipo_resultado_general== 'C')
										{
											$where_quitar 	= ' GROUP BY '.$campo_comparacion;
											$select_quitar	= ' ,'.$campo_comparacion;
										}
									}
									$where = str_replace($cod_parametro, $array_parametros_ya_seleccionados[$cod_parametro_new], $where);
								}
							}
						}
						// --> Si no es referenciado realizo el reemplazo normal en el where
						else
						{
							$where = str_replace($cod_parametro, $array_parametros_ya_seleccionados[$cod_parametro_new], $where);
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
						if(!array_key_exists($cod_parametro_new, $parametros_ya_pintados)) //Si existe es porque ya se pinto
						{
							// --> Crear array donde voy guardando los codigos de los parametros que ya he pintado
							$parametros_ya_pintados[$cod_parametro_new]='';

							// --> Calcular y pintar el parametro
							calcular_parametro($cod_parametro, $wperiodo_calcular, (($cod_parametro==$param_agrupado) ? $detallado_por : ''), $id_input = $cod_parametro_new);
						}
					}
				}
				unset($array_parametros);
			}
		}
		// <-- Fin revisar si existen parametro en el where

		// --> Aqui se ejecuta el query del dato
		if($ejecutar_query == 'si')
		{
			// --> Se ejecutara el query pero insertando su resulado a una temporal
			if($tabla_temporal == 'si')
			{
				$nombre_temporal = $codigo_dato;
				$tipo_unix = borrar_tabla_temporal($origen_dato, $nombre_temporal);
				// --> Si es para unix
				if($tipo_unix == 'on')
					$query_dato = 'SELECT '.$select.' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.$where : '').' INTO TEMP '.$nombre_temporal;
				// --> Si es para matrix
				else
					$query_dato = 'CREATE TEMPORARY TABLE IF NOT EXISTS '.$nombre_temporal.' AS SELECT '.$select.' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.$where : '');

				$tipo_query = 'temporal';
				$foto_calculo_temporales.= '[TEMPORAL '.$nombre_temporal.' --> '.$query_dato.'] ';
				//$foto_calculo.= '[DATO TEMPORAL = '.$query_dato.'] ';
			}
			// --> Se ejecutara el query normal
			else
			{
				$query_dato = 'SELECT '.$select.' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.$where : '');
				$tipo_query = 'dato';
				$foto_calculo.= $query_dato.'|';

				// --> Aca creo un query anexo a ejecutar, pero sin group by. (Modificaion del 2013-10-17).
				if(isset($where_quitar) && isset($select_quitar))
				{
					$query_dato_anexo = 'SELECT '.str_replace($select_quitar, ' ', $select).' FROM '.$from.(($where!='' && $where!=' ') ? ' WHERE '.str_replace($where_quitar, ' ', $where) : '');
					$tipo_query_anexo = 'dato';
				}
			}

			// --> Ejecutar el query principal del dato
			$resultado_query 	= array();
			echo "<tr><td colspan='2' align='center'>";
			ejecutar_query($query_dato, $origen_dato, $resultado_query, $tipo_query, $hacer_agrupacion);
			echo "</td></tr>";
			//echo '<br><br><pre>';print_r($resultado_query);echo '</pre>';

			// --> Si existe un query anexo, lo ejecuto. (Modificaion del 2013-10-17).
			if(isset($query_dato_anexo))
			{
				$resultado_query_anexo 	= array();
				ejecutar_query($query_dato_anexo, $origen_dato, $resultado_query_anexo, $tipo_query, 'NO');
			}

			if(count($resultado_query) > 0 )
			{
				// --> Si hay mas de un resultado
				if(count($resultado_query) > 1 || $hacer_agrupacion == 'SI')
				{
					// --> Si existe un agrupamiento, entonces envio el array completo el cual contiene los resultados detallados
					if( $hacer_agrupacion == 'SI')
					{
						$resultado_general = $resultado_query;

						// --> 	Si se ejecuto un query anexo, envio la variable resultado_general con el correspondiente resultado
						//		(Modificaion del 2013-10-17).
						if(isset($resultado_query_anexo))
							$resultado_general['RESULTADO_GENERAL'] = $resultado_query_anexo[0];
					}
					else
					{
						$resultado_general  = 0;
						foreach($resultado_query as $res_agrupado_por => $res_valor)
						{
							if($res_valor != '')
								$resultado_general+=$res_valor;
						}
					}
				}
				// --> Es un solo resultado
				else
				{
					// --> Resultado de una tabla temporal
					if($tabla_temporal == 'si')
					{
						if($resultado_query[0]=='TRUE')
						{
							//Deshabilitar el boton de calcular
							echo "<script>
							$('#boton_calcular').hide();
							</script>";
							calcular_indicador($wperiodo_calcular, $cod_ind, $parametros_seleccionados, $id_movimiento, $lista_recorrido, $mostrar_mensaje, 'si', $foto_calculo_temporales);
							return 'no_hay_resultado';
						}
						else
						{
							// --> Diseñar mensaje de que la temporal no fue creada
						}
					}
					// --> Un resultado de un query normal
					else
					{
						if($resultado_query[0]=='')
							$resultado_general='0';
						else
							$resultado_general = $resultado_query[0];
					}
				}
				// --> Retorno el resultado general del query
				return $resultado_general;
			}
			else
			{
				return 'no_hay_resultado';
			}
		}
		else
		{
			return 'no_hay_resultado';
		}
	}

	//--------------------------------------------------------------------------------------------
	//		Funcion que buscar el campo de comparacion para agregar al group by
	//--------------------------------------------------------------------------------------------
	function campo_para_group_by($where, $cod_parametro)
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
						return $campo_comparacion = trim(strrev($campo_comparacion));
						break;
					}
					else
					$campo_comparacion.= $where{$z};
				}
				break;
			}
		}
	}
	//-------------------------------------------------------------------------------------------------------------
	//		Funcion que obtiene el resultado de un indicador o una estadistica, y valida si ya esta calculado o no
	//-------------------------------------------------------------------------------------------------------------
	function obtener_resultado($wperiodo_calcular, $codigo_dato, &$resultado, &$analisis, $codigo_indicador, &$pintar_boton_continuar = 'no', $nombre_var)
	{
		global $wbasedato;
		global $conex;
		global $wuse;

		$periodo_fechas = explode('|', $wperiodo_calcular);
		// --> Consultar informacion basica del indicador o estadistica
		$q_info_basic = "SELECT Indtes, Indnom, Indrme, Descripcion
						   FROM ".$wbasedato."_000001, usuarios
						  WHERE Indcod = '".$codigo_dato."'
						    AND Indrme = Codigo ";

		$res_info_basic = mysql_query($q_info_basic, $conex) or die (mysql_errno() . $q_info_basic . " (Query: Informacion basica) " . mysql_error());

		if($row_info_basic = mysql_fetch_array($res_info_basic))
		{
			$resp_medicion = $row_info_basic['Descripcion'];
			$resp_medi_cod = $row_info_basic['Indrme'];
		}

		// --> Verificar si la variable ya esta calculada para el correspondiente periodo
		$q_movimiento = "SELECT Movres, Movana, Descripcion
						   FROM ".$wbasedato."_000009, usuarios
						  WHERE Movind = '".$codigo_dato."'
							AND ((Movfip BETWEEN '".$periodo_fechas[0]."' AND  '".$periodo_fechas[1]."') AND (Movffp BETWEEN '".$periodo_fechas[0]."' AND  '".$periodo_fechas[1]."'))
							AND Movcal = 'on'
							AND Movgen = 'on'
							AND Movusu = Codigo";

		$res_movimiento = mysql_query($q_movimiento, $conex) or die (mysql_errno() . $q_movimiento . " (Query: Consultar movimiento) " . mysql_error());

		// --> 	Recorro los resultados, cuando la periodicidad del indicador sea diferente a mensual y
		//		en cambio la periodicidad del dato es mensual, entonces el query va a arrojar mas de un resultados
		//		los cuales se sumaran en este ciclo.
		//		Jerson trujillo, 2014-05-09
		$resultado 	= 0;
		$analisis	= '';
		while($row_movimiento = mysql_fetch_array($res_movimiento))
		{
			$resultado+=$row_movimiento['Movres'];
			if($row_movimiento['Movana'] != '')
			{
				$analisis.=$row_movimiento['Movres'].' = '.$row_movimiento['Movana'].'.   ';
			}
		}

		if(mysql_num_rows($res_movimiento) > 0)
		{
			$num_rows_textarea = numero_columnas_textarea(utf8_decode($analisis), 50);
			// --> Pinto titulo del indicador o la estadistica, con su repectivo valor y reponsable de calculo
			echo "<tr class='parrafo_text'><td colspan='2' align='center'><b>".utf8_decode($nombre_var)."</b></td></tr>";
			echo "<td class='Encabezadotabla' width='35%'>Valor:</td><td class='Fila1' align='center'>".$resultado."</td>";
			echo "<tr>
					<td class='Encabezadotabla' width='35%'>Explicaci&oacute;n:</td>
					<td class='Fila2' align='center'>
						<textarea rows='".$num_rows_textarea."' cols=50 disabled>".utf8_decode($analisis)."</textarea>
					</td>
				</tr>
				<tr>
					<td class='Encabezadotabla' width='35%'>Calculado por:</td>
					<td class='Fila1' align='center'>
						".$row_movimiento['Descripcion']."
					</td>
				</tr>
				";
			$respuesta = true;
		}
		else
		{
			// --> Pinto titulo del indicador o la estadistica, con el mensaje de que no se ha calculado
			echo "<tr class='parrafo_text'><td colspan='2' align='center'><b>".utf8_decode($nombre_var)."</b></td></tr>
				<tr>
					<td class='Encabezadotabla' width='40%' align='center'>
						<blink>
						<img width='15' height='15' src='../../images/medical/root/info.png'>&nbsp; &iexcl;  Alerta !
						</blink>
					</td>
					<td class='Fila1' align='center'>".(($codigo_dato[0]=='I') ? 'Este indicador':'Esta estadística')." no se ha calculado para este periodo</td>
				</tr>";
			echo "<tr>
					<td class='Encabezadotabla' width='40%'>Responsable del calculo:</td>
					<td class='Fila2' align='center'>
					<b>".$resp_medicion."</b>
					</td>
				</tr>";

			// --> si el usuario logeado es el responsable de calcular el inidcador o la estadistica, activo un boton para que lo puedan calcular
			if($resp_medi_cod==$wuse)
			{
				echo "
				<tr>
					<td class='Encabezadotabla' width='40%'> ¿Calcular el dato? </td>";
				$lista_recorrer = $codigo_dato.'|'.$wperiodo_calcular.'||-|'.$codigo_indicador.'|'.$wperiodo_calcular.'|';
				$style = "background:#FFFFE1; color: #000000;cursor:pointer;font-size: 8pt;border: 1px solid #FFFF56; width: 54px; height:22px; align:center;";
				echo "<td class='Fila1' align='center'>
						<input type='submit' value='Calcular' onclick='recorrer_automatico(\"".$lista_recorrer."\", \"no\")' style='".$style."'/>
					</td>
				</tr>
				";
			}

			$respuesta = false;
			$pintar_boton_continuar = 'si';

			//Deshabilitar el boton de calcular
			echo "<script>
			$('#boton_calcular').hide();
			</script>";
		}
		return $respuesta;
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
		case 'generar_cuadro':
		{
			pintar_meses($wperiodo, ((isset($wcod_indicador)) ? $wcod_indicador: '%'), ((isset($wperiodo_resaltar))? $wperiodo_resaltar: ''), $wemp_pmla, $wcantidad_meses, $wpendientes_calc);
			break;
			return;
		}
		case 'calcular_indicador':
		{
			calcular_indicador($wperiodo_calcular, $wcodigo, ((isset($parametros_valores))? $parametros_valores: ''), $id_movimiento, ((isset($lista_recorrido))? $lista_recorrido : ''), ((isset($mostrar_mensaje))? $mostrar_mensaje : ''), 'no');
			break;
			return;
		}

		//==============================================================
		// Guardar el movimiento general de un indicador o estadistica
		//==============================================================
		case 'guardar_movimiento':
		{
			$wperiodo_calcular = explode('|', $wperiodo_calcular);
			$fecha_ini_periodo = $wperiodo_calcular[0];
			$fecha_fin_periodo = $wperiodo_calcular[1];
			$arr_res_detallados = json_decode(str_replace('\\', '', $arr_res_detallados));

			// --> Definir si es calculo temporal o definitivo
			if( strtotime($wfecha) > strtotime($fecha_fin_periodo))
			{
				$est_calculado 	= 'on';
				$est_temporal	= '';
			}
			else
			{
				$est_calculado 	= '';
				$est_temporal	= 'on';
			}
			// --> Consultar informacion basica del indicador o estadistica
			$q_info_ind = "SELECT *
							 FROM ".$wbasedato."_000001
							WHERE Indcod = '".$codigo."'
							";
			$res_info_ind 	= mysql_query($q_info_ind,$conex) or die ("Error: ".mysql_errno()." - en el query(consultar informacion del indicador): ".$q_info_ind." - ".mysql_error());
			$row_info_ind 	= mysql_fetch_array($res_info_ind);

			$desempe_superi = $row_info_ind ['Inddsu'];
			$desempe_mini	= $row_info_ind ['Inddmi'];
			$meta			= $row_info_ind ['Indmet'];
			$tipo_semaforiz	= $row_info_ind ['Indsem'];
			$magnitud		= $row_info_ind ['Indmag'];
			$formula_indi	= $row_info_ind ['Indfoc'];
			$modifica_resul = $row_info_ind ['Indare'];
			$TipoOperResGen = $row_info_ind ['Indorg'];

			//------------------------------------
			// Grabar el movimiento detallado
			//------------------------------------
			// --> Si hay resultados detallados
			if(count($arr_res_detallados) > 0)
			{
				$calculo_general = 'off';
				// --> Obtener un array con las descripciones de los detalles por los que se hizo el group by
				$array_descrip_valores = array();
				$array_descrip_valores = obtener_descrip_valores($parametro_groupby);

				foreach ($arr_res_detallados as $valor_det => $resul_det)
				{
					if($resul_det != '')
					{
						eval('$resultado_detalle = @('.$resul_det.');');
						if ($id_movimiento!='')
						{
							// Consultar si ya existe un movimiento para el detalle
							$q_existe = " SELECT id
										    FROM ".$wbasedato."_000009
										   WHERE Movind = '".$codigo."'
										     AND Movfip = '".$fecha_ini_periodo."'
											 AND Movffp = '".$fecha_fin_periodo."'
											 AND Movvde = '".$valor_det."'
										";
							$res_existe = mysql_query($q_existe,$conex) or die ("Error: ".mysql_errno()." - en el query(Existe movimiento detallado): ".$q_existe." - ".mysql_error());

							// --> Actualizo el existente
							if(mysql_num_rows($res_existe) > 0)
							{
								$row_existe = mysql_fetch_array($res_existe);
								$q_update = "UPDATE ".$wbasedato."_000009
												SET Fecha_data 	= 	'".$wfecha."' ,
													Hora_data	= 	'".$whora."',
													Movdsu		=	'".$desempe_superi."',
													Movdmi		=	'".$desempe_mini."',
													Movmet		=	'".$meta."',
													Movsem		=	'".$tipo_semaforiz."',
													Movmag		=	'".$magnitud."',
													Movfin		=	'".$formula_indi."',
													Movdde		= 	'".$array_descrip_valores[trim($valor_det)]."',
													Movfap		= 	'".$resul_det."',
													Movfot		=	'".$foto_calculo."',
													Movres		= 	'".$resultado_detalle."',
													Movtem		= 	'".$est_temporal."',
													Movcal		= 	'".$est_calculado."',
													Movpsu		=	'".$parametro_groupby."',
													Movana		=	'".$analisis."',
													Movgen		= 	'".$calculo_general."',
													Movorg		= 	'".$TipoOperResGen."',
													Seguridad	=	'C-".$wuse."'
											  WHERE	id			= 	'".$row_existe['id']."'
											";
								mysql_query($q_update,$conex) or die ("Error: ".mysql_errno()." - en el query(Actualizar movimiento detallado): ".$q_update." - ".mysql_error());

							}
							// --> Inserto uno nuevo
							else
							{
								// --> Query para el movimiento detallado
								$q_detall = " INSERT INTO ".$wbasedato."_000009
														(		Medico, 	Fecha_data, Hora_data, 	   Movind, 				Movfip, 				Movffp, 		 		Movvde, 					Movdde, 							Movdsu, 			Movdmi, 			Movmet,				Movsem, 		Movmag, 	     Movfin, 				Movfap, 		Movfot,					Movres, 				Movcal,  		Movtem, 		  Movusu, 		   Movpsu,				Movana,		       Movgen,				Movorg,   				Seguridad, id)
												VALUES	('".$wbasedato."','".$wfecha."','".$whora."','".$codigo."','".$fecha_ini_periodo."','".$fecha_fin_periodo."', '".$valor_det."', '".$array_descrip_valores[trim($valor_det)]."', '".$desempe_superi."','".$desempe_mini."','".$meta."','".$tipo_semaforiz."', '".$magnitud."','".$formula_indi."','".$resul_det."','".$foto_calculo."','".$resultado_detalle."','".$est_calculado."', '".$est_temporal."','".$wuse."','".$parametro_groupby."','".$analisis."','".$calculo_general."',	'".$TipoOperResGen."', 'C-".$wuse."','')
											";
								//Ejecuto el query
								mysql_query($q_detall,$conex) or die ("Error: ".mysql_errno()." - en el query(Grabar movimiento detallado 1): ".$q_detall." - ".mysql_error());
							}
						}
						else
						{
							// --> Query para el movimiento detallado
							$q_detall = " INSERT INTO ".$wbasedato."_000009
													(		Medico, 	Fecha_data, Hora_data, 	   Movind, 				Movfip, 				Movffp, 		 		Movvde, 					Movdde, 							Movdsu, 			Movdmi, 			Movmet,				Movsem, 		Movmag, 	     Movfin, 			Movfap, 			Movfot,					Movres, 				Movcal,  		Movtem, 		  Movusu, 		   Movpsu,				Movana,		       Movgen,				Movorg,        			Seguridad, id)
											VALUES	('".$wbasedato."','".$wfecha."','".$whora."','".$codigo."','".$fecha_ini_periodo."','".$fecha_fin_periodo."', '".$valor_det."', '".$array_descrip_valores[trim($valor_det)]."', '".$desempe_superi."','".$desempe_mini."','".$meta."','".$tipo_semaforiz."', '".$magnitud."','".$formula_indi."','".$resul_det."','".$foto_calculo."','".$resultado_detalle."','".$est_calculado."', '".$est_temporal."','".$wuse."','".$parametro_groupby."','".$analisis."','".$calculo_general."',	'".$TipoOperResGen."', 	'C-".$wuse."','')
										";
							//Ejecuto el query
							mysql_query($q_detall,$conex) or die ("Error: ".mysql_errno()." - en el query(Grabar movimiento detallado 2): ".$q_detall." - ".mysql_error());
						}
					}
				}
			}


			//------------------------------------
			// Grabar el movimiento general
			//------------------------------------
			$calculo_general = 'on';
			// --> Actualizo el movimiento general
			if ($id_movimiento!='')
			{
				// --> Si ya existe un id y el indicador actualiza resultados, debo sacar una foto de los registros anteriores.
				$log_movimiento = '';
				if($modifica_resul == 'on')
				{
					$reg_ante = "SELECT *
								   FROM ".$wbasedato."_000009
								  WHERE id = '".$id_movimiento."' ";
					$res_reg_ante = mysql_query($reg_ante,$conex) or die ("Error: ".mysql_errno()." - en el query(Consultar valores movimiento): ".$reg_ante." - ".mysql_error());

					if($row_reg_ante = mysql_fetch_array($res_reg_ante))
					{
						for($i=0; $i < mysql_num_fields($res_reg_ante); $i++)
						{
							$log_movimiento.= '[['.mysql_field_name($res_reg_ante, $i).'--->'.$row_reg_ante[$i].']]';
						}
					}
				}

				// --> Query para actualizar el movimiento general
				$q_actual = "UPDATE ".$wbasedato."_000009
								SET Fecha_data 	= 	'".$wfecha."' ,
									Hora_data	= 	'".$whora."',
									Movdsu		=	'".$desempe_superi."',
									Movdmi		=	'".$desempe_mini."',
									Movmet		=	'".$meta."',
									Movsem		=	'".$tipo_semaforiz."',
									Movmag		=	'".$magnitud."',
									Movfin		=	'".$formula_indi."',
									Movfap		= 	'".$formula_a_ejecutar."',
									Movfot		=	'".$foto_calculo."',
									Movres		= 	'".$resultado."',
									Movtem		= 	'".$est_temporal."',
									Movcal		= 	'".$est_calculado."',
									Movpsu		=	'".$parametro_groupby."',
									Movana		=	'".$analisis."',
									Movgen		= 	'".$calculo_general."',
									Movlog		= 	'".$log_movimiento."',
									Movorg		= 	'".$TipoOperResGen."',
									Seguridad	=	'C-".$wuse."'
							  WHERE	id			= 	'".$id_movimiento."'
							";
			}
			// --> Inserto un nuevo movimiento
			else
			{

				// --> Query para el movimiento general
				$q_actual = " INSERT INTO ".$wbasedato."_000009
										(		Medico, 	Fecha_data, Hora_data, 	   Movind, 				Movfip, 				Movffp, 		 Movvde, Movdde, 		Movdsu, 			Movdmi, 		Movmet,		    Movsem, 				Movmag, 	     Movfin, 					Movfap, 			Movfot,			Movres, 				Movcal,  		Movtem, 		 Movusu, 		 Movpsu,				Movana,		       Movgen,					Movorg,        		Seguridad, id)
								VALUES	('".$wbasedato."','".$wfecha."','".$whora."','".$codigo."','".$fecha_ini_periodo."','".$fecha_fin_periodo."', '*', 	'Todos', '".$desempe_superi."','".$desempe_mini."','".$meta."','".$tipo_semaforiz."', '".$magnitud."','".$formula_indi."','".$formula_a_ejecutar."','".$foto_calculo."','".$resultado."', '".$est_calculado."', '".$est_temporal."','".$wuse."','".$parametro_groupby."','".$analisis."','".$calculo_general."',	'".$TipoOperResGen."', 'C-".$wuse."','')
							";
			}

			//Ejecuto el query
			mysql_query($q_actual,$conex) or die ("Error: ".mysql_errno()." - en el query(Grabar movimiento): ".$q_actual." - ".mysql_error());

			if($id_movimiento == '')
			{
				//Consulto el ultimo id generado en el movimiento, es decir el id con el q quedo registrado el movimiento
				//para retornarlo a la funcion javascript.
				$q_id_movimiento 	= "SELECT MAX(id) as id
										 FROM ".$wbasedato."_000009";
				$res_id_movimiento 	= mysql_query($q_id_movimiento,$conex) or die ("Error: ".mysql_errno()." - en el query(consultar id movimiento): ".$q_id_movimiento." - ".mysql_error());
				$id_movimiento 		= mysql_fetch_array($res_id_movimiento);
				$id_movimiento 		= $id_movimiento['id'];
			}

			echo $id_movimiento;
			break;
		}
		//=================================
		// Fin guardar el movimiento
		//=================================
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
	echo "<input type='hidden' id='cmi_consulta' name='cmi_consulta' value='".$cmi_consulta."'>";
	echo "<input type='hidden' id='wuse' name='wuse' value='".$wuse."'>";
	?>
	<html>
	<head>
	  <title>CUADRO DE MANDO INDICADORES</title>
	</head>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript" ></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
	<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>


	<script type="text/javascript">
		//-----------------------------------------------------------------------
		// Funcion que hace el llamado al graficador en la opcion ver detalle
		//-----------------------------------------------------------------------
		function pintarGrafica()
		{
			$('#contenedor_graficador').html("<div id='amchart2' style='border: 1px solid #999999; width:650px; height:350px;'></div>");
			$('#contenedor_graficador').show();
			$('#table_detalle').LeerTablaAmericas(
			{
				empezardesdefila: 	3,
				titulo 			: 	'Resultado Detallado',
				datosadicionales: 	'nada'	,
				filaencabezado : 	[2,1],
				divgrafica :		'amchart2'
			});
		}
		//-----------------------------------------------------------------------------
		// Funcion que hace el llamado al graficador en le cuadro de mando principal
		//-----------------------------------------------------------------------------
		function pintarGrafica2(indicador, codigo_indicador, unidad_medida)
		{
			var cerrar = "<div align=right id='div_cerrar'>"
							+"<div class='fila2' onclick='$(\"#div_cerrar_ventana\").show();$(\"#tag_llamado_posicion\").attr(\"href\", \"#"+codigo_indicador+"\");$(\"#contenedor_graficador2\").html(\"\");$(\"#contenedor_graficador2\").hide(600);generar_tablero(\"normal\");' title='Cerrar ficha' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center >"
								+"<b>Cerrar</b>"
								+"<img width='10' height='10' border='0' style='cursor:pointer;' title='Cerrar' src='../../images/medical/eliminar1.png'>"
							+"</div>"
						+"</div><br>";
			var titulo_indicador = '<div style="background-color: #2A5DB0;color: #FFFFFF;padding: 5px;border: 1px solid #999999;font-family: verdana;font-size: 11pt;" align=center id="NombreIndicador"></div><br>';
			var titulo = "<div class='borderDiv Titulo_azul' id='div_titulo_gra' align=center onclick='cerrar_seccion(\"div_cont_gra\");'>"
							+"<div style='cursor:pointer;'><img width='10' height='10' src='../../images/medical/iconos/gifs/i.p.next[1].gif'/> GRAFICO</div>"
						+"</div><br>";

			$('#contenedor_graficador2').html('<div id="zona_grafico">'+cerrar+titulo_indicador+titulo+"<div class='borderDiv' id='div_cont_gra'><br><div id='amchart1' style='border: 1px solid #999999; width:600px; height:350px;'></div><br></div><br>");
			$('#NombreIndicador').html($('#hidden_nombre_indicador').text());
			$('#contenedor_graficador2').show();
			$('#tabla_ppal').LeerTablaAmericas(
			{
				empezardesdefila: 	4,
				datosadicionales: 	'nada'	,
				titulo 			: 	indicador,
				filaencabezado 	: 	[1,1],
				invertirgrafica : 	"si" ,
				filadatos 		:   1,
				columnadatos 	:	2,
				sintitulo		:	'si',
				tituloy			:	unidad_medida
			});
		}
		//--------------------------------------
		// Funcion que remueve un elemnto html
		//--------------------------------------
		function removerElemento(elemento)
		{
			$('#'+elemento).hide();
			generar_tablero('normal');
		}
		//-----------------------------------------------------------------
		// Funcion que pinta el div contenedor para calcular el indicador
		//-----------------------------------------------------------------
		function medir_indicador(codigo, periodo, parametros_valores, id_movimiento, lista_recorrido_nueva, mostrar_mensaje)
		{
			$('#div_cerrar_ventana').hide();
			$.post("Cuadro_mando_indicadores.php",
				{
					consultaAjax:   		'',
					wemp_pmla:      		$('#wemp_pmla').val(),
					wuse:           		$('#wuse').val(),
					cmi_consulta:			$('#cmi_consulta').val(),
					wbasedato:				$('#wbasedato').val(),
					accion:         		'calcular_indicador',
					wcodigo:				codigo,
					wperiodo_calcular:		periodo,
					parametros_valores:		parametros_valores,
					id_movimiento:			id_movimiento,
					lista_recorrido:		lista_recorrido_nueva,
					mostrar_mensaje:		mostrar_mensaje
				}
				,function(data) {
					generar_tablero( 'normal', codigo, periodo);

					// --> Si existia un grafico pintado
					if($("#contenedor_graficador2").find("#zona_grafico").text() != '')
						$("#contenedor_graficador2").html('<div id="zona_grafico">'+$("#zona_grafico").html()+'</div>'+data);
					else
					{
						// --> Boton de cerrar
						var cerrar = "<div align=right id='div_cerrar'>"
							+"<div class='fila2' onclick='$(\"#div_cerrar_ventana\").show();$(\"#tag_llamado_posicion\").attr(\"href\", \"#"+codigo+"\");$(\"#contenedor_graficador2\").html(\"\");$(\"#contenedor_graficador2\").hide(600);generar_tablero(\"normal\");' title='Cerrar ficha' style='width:90px;cursor:pointer;border: 4px double; margin-bottom: 4px;margin-top: 3px;' align=center >"
								+"<b>Cerrar</b>"
								+"<img width='10' height='10' border='0' style='cursor:pointer;' title='Cerrar' src='../../images/medical/eliminar1.png'>"
							+"</div><br>"
						+"</div>";
						var titulo_indicador = '<div style="background-color: #2A5DB0;color: #FFFFFF;padding: 5px;border: 1px solid #999999;font-family: verdana;font-size: 11pt;" id="NombreIndicador" align=center></div><br>';
						$("#contenedor_graficador2").html(cerrar+titulo_indicador+data);
						$('#NombreIndicador').html($('#hidden_nombre_indicador2').val());
					}
					$("#contenedor_graficador2").show(500);

					$("input[type=text]").keyup(function(){
						if ($(this).val() !="")
							$(this).val($(this).val().replace(/[^0-9\.-]/g, ""));
					});
					$("#analisis").keyup(function(){
						if ($(this).val() !="")
							$(this).val($(this).val().replace(/\'/g, '"'));
					});
				}
			);
		}
		//---------------------------------------------------------------------------
		// Envio de parametros a la funcion medir_indicador para poder calcularlo
		//---------------------------------------------------------------------------
		function ejecutar_indicador(codigo, periodo, id_movimiento, lista_recorrido, mostrar_mensaje)
		{
			$('#msj_calculando').show();
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
				array_parametros[nombre_parametr]= 'no';
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

						//Esta validación es para controlar la situación de que aparezca dos o más veces el mismo parámetro
						//por ende debo controlar que solo envié un solo valor, porque si envió dos o mas valores por un
						//solo parámetro esto me genera un error.
						if(array_parametros[nombre_parametr] == 'no')
						{
							if(valor_param!='')
							{
								parametros_valores+= nombre_parametr+'|'+valor_param+'|-|';
								array_parametros[nombre_parametr]='si';
							}
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
				medir_indicador(codigo, periodo, parametros_valores, id_movimiento, lista_recorrido, mostrar_mensaje)
			else
			{
				$("#div_mensaje_validacion")
						.html('¡¡¡ Debe seleccionar todos los parametros !!! <br>Parametros sin seleccionar: '+parametros_sin_seleccionar)
						.css({"color":"red", "opacity":" 0.5","fontSize":"14px","border":"1px dotted red"})
						.show(500);
				$('#msj_calculando').hide();
				$('#boton_calcular').show();
			}
		}

		//-------------------------------
		// Funcion que genera el tablero
		//-------------------------------
		function generar_tablero(opcion, cod_indicador, periodo_resaltar, nombre_indicador, unidad_medida)
		{
			// --> Pintar Block UI mensaje de espera
			mensaje_espera();
			var periodo='';
			switch(opcion)
			{
				case 'adelante':
				{
					periodo = $('#selecccionar_mes').val();
					var ano_mes = periodo.split('-');
					ano_mes[1] = (ano_mes[1]*1)+1;
					if(ano_mes[1]>12)
					{
						periodo = ((ano_mes[0]*1)+1)+'-'+'1';
					}
					else
					{
						periodo = ano_mes[0]+'-'+ano_mes[1];
					}
					$('#selecccionar_mes').val(periodo);
					break;
				}
				case 'atras':
				{
					periodo = $('#selecccionar_mes').val();
					var ano_mes = periodo.split('-');
					ano_mes[1] = (ano_mes[1]*1)-1;
					if(ano_mes[1]==0)
					{
						periodo = ((ano_mes[0]*1)-1)+'-'+'12';
					}
					else
					{
						periodo = ano_mes[0]+'-'+ano_mes[1];
					}
					$('#selecccionar_mes').val(periodo);
					break;
				}
			}

			periodo = $('#selecccionar_mes').val();

			$.post("Cuadro_mando_indicadores.php",
				{
					consultaAjax:   		'',
					wemp_pmla:      		$('#wemp_pmla').val(),
					wuse:           		$('#wuse').val(),
					cmi_consulta:			$('#cmi_consulta').val(),
					wbasedato:				$('#wbasedato').val(),
					accion:         		'generar_cuadro',
					wcod_indicador:			cod_indicador,
					wperiodo:				periodo,
					wperiodo_resaltar:		periodo_resaltar,
					wcantidad_meses:		$('#cantidad_meses').val()
				}
				,function(data) {

					var filtrado = $("#tabla_ppal").find('#filtro_selec_defecto').val();
					var codigos  = $("#tabla_ppal").find('#cadena_val_selec_defecto').val();
					if(cod_indicador != '')
					{
						// --> Realizar copia de como estaba visualizado el tablero
						$("#tabla_filtros_foto").html($("#tabla_filtros").html());
						viendo_detalle = 'si';
					}

					$("#cuadro_mando").html(data);

					// --> Eliminar los tr que ya tengan todos los meses pintados ya calculados
					if((($('#pendientes_calculo').attr('checked') == 'checked') ? 'on' : 'off') == 'on' && cod_indicador == undefined)
					{
						var arr_tr_eliminar = eval('(' + $('#hidden_calc_completos').val() + ')');
						$.each( arr_tr_eliminar, function( key, value ){
							$('#'+key).remove();
						});
					}
					// <-- Fin eliminar los tr que ya tengan todos los meses pintados ya calculados

					if(filtrado == undefined && codigos == undefined)
					{
						filtrado = $('#filtro_selec_defecto').val();
						codigos  = $('#cadena_val_selec_defecto').val();
					}

					if(filtrado != undefined && codigos != undefined )
					{
						SegmentarTabla(filtrado, codigos, 'no');
					}
					if(nombre_indicador != undefined)
					{
						pintarGrafica2('', cod_indicador, unidad_medida);
					}

					// --> Cambiar de color el mes seleccionado
					if(cod_indicador != '' && cod_indicador != undefined && $('#hidden_meses_res').val() != undefined)
					{
						var arr_meses_resaltar = $('#hidden_meses_res').val().split("|");
						$.each( arr_meses_resaltar, function( key, value ) {
							$('#'+value).css({
								'color'	 :	'#2A5DB0',
								'background-color': '#FFFFCC'
							});
						});
					}
				}
			);
		}
		function posicionElemento(ele, contenedor)
		{
			var mes_seleccionado = $('#'+contenedor).val();
			mes_seleccionado = mes_seleccionado.split('-');
			mes_seleccionado = mes_seleccionado[1];

			$('#cont_caja_flotante').find('td').each(
					function(index)
					{
						if($(this).attr('ref'))
							$(this).css({'border':'1px solid #999999'});
						if(mes_seleccionado == $(this).attr('ref'))
						{
							$(this).css({'border':'2px solid orange'});
						}
						$(this).show();
					}
				);

			var elemento = $("#"+ele.id);
			var posicion = elemento.offset();


			$('#caja_flotante').css({'left':posicion.left,'top':posicion.top+24});
			$('#caja_flotante').show(500);
		}
		function asignar_año_mes(mes)
		{
			var fecha_seleccionada = $('#año_sel').val()+'-'+mes;
			$('#selecccionar_mes').val(fecha_seleccionada);
			$('#caja_flotante').hide(500);
			generar_tablero('normal');
			$('#contenedor_graficador2').hide();
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

		function guardar_movimiento(id_movimiento, codigo, wperiodo_calcular, formula_a_ejecutar, resultado, lista_recorrido, tipo)
		{
			var guardar = 'si';

			if(tipo == 'Indicador')
			{
				guardar = validar_guardar('analisis', guardar);
			}

			if(guardar == 'si')
			{
				$.post("Cuadro_mando_indicadores.php",
					{
						consultaAjax:   		'',
						wemp_pmla:      		$('#wemp_pmla').val(),
						wuse:           		$('#wuse').val(),
						cmi_consulta:			$('#cmi_consulta').val(),
						wbasedato:				$('#wbasedato').val(),
						accion:         		'guardar_movimiento',
						id_movimiento:			id_movimiento,
						codigo:					codigo,
						wperiodo_calcular:		wperiodo_calcular,
						foto_calculo:			$('#hidden_foto_calculo').val(),
						analisis:				$('#analisis').val(),
						formula_a_ejecutar:		formula_a_ejecutar,
						resultado:				resultado,
						arr_res_detallados:		$('#hidden_resultados_detallados').val(),
						parametro_groupby: 		$('#hidden_parametro_groupby').val()
					}
					,function(data) {
						alert('Resultado Guardado');
						if(lista_recorrido!='')	//Continuo con el recorrido
						{
							recorrer_automatico(lista_recorrido);
						}
						else
						{
							medir_indicador(codigo, wperiodo_calcular, '', data);
						}
					}
				);
			}
		}

		//----------------------------------------------------------------------
		// Funcion que va midiendo indicadores segun una variable de reccorrido
		//----------------------------------------------------------------------
		function recorrer_automatico(lista_recorrido, mostrar_mensaje)
		{
			var valores_lista;
			var lista_recorrido_nueva = '';
			var codigo 			= '';
			var periodo			= '';
			var id_movimiento 	= '';

			if(lista_recorrido=='fin')
			{
				alert('Fin de calcular todos');
				generar_tablero('normal');
				removerElemento("contenedor_graficador2");
			}
			else
			{
				lista_recorrido = lista_recorrido.split('|-|');
				for(var x=0; x<lista_recorrido.length; x++)
				{
					valores_lista = lista_recorrido[x];

					if(valores_lista !='' && x==0)
					{
						valores_lista2 		= valores_lista.split('|');
						var codigo  		= valores_lista2[0];
						var periodo 		= valores_lista2[1]+'|'+valores_lista2[2];
						var id_movimiento 	= valores_lista2[3];
						if(id_movimiento == 'Sin Calcular')
						{
							id_movimiento='';
						}

					}
					else
					{
						if(valores_lista !='')
						{
							lista_recorrido_nueva+= valores_lista+'|-|';
						}
					}
				}
				if(lista_recorrido_nueva == '')
					lista_recorrido_nueva = 'fin';
				if(codigo !='' && periodo !='')
					medir_indicador(codigo, periodo, '', id_movimiento, lista_recorrido_nueva, mostrar_mensaje);
			}
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
		function validar_numerico(elemento)
		{
			if ($("#"+elemento.id) !="")
				$("#"+elemento.id).val($("#"+elemento.id).val().replace(/[^0-9\.]/g, ""));
		}
		function parpadeo()
		{
			try {
				var blink = document.all.tags("BLINK");

				for (var i=0; i < blink.length; i++){
					blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "";
				}
			}
			catch(e){
			}
		}
		//--------------------------------------------------------------------------
		// --> Pintar Block UI mensaje de espera
		//--------------------------------------------------------------------------
		function mensaje_espera()
		{
			$.unblockUI();
			var mensaje_esp = "<div style='background-color: #FFE57E;color:#6D6D6D;font-size: 11pt;'><img width='17' heigth='17' src='../../images/medical/ajax-loader3.gif'> &nbsp; &nbsp;Cargando...</div>";
			$.blockUI({
				message: mensaje_esp,
				css: 	{
							width: 	'300px',
							border: '2px solid #E2B709'
						}
			});
		}
		//----------------------------------------------------------------------------
		// --> Funcion que re-pinta la tabla principal segun las opciones de filtrado
		//----------------------------------------------------------------------------
		function SegmentarTabla(filtrado, codigos, primera_carga)
		{
			// --> Pintar Block UI mensaje de espera
			if(primera_carga == 'si')
				mensaje_espera();
			// <-- Fin mensaje

			// --> Para llevar el "encabezado" a la tabla de filtros (secundaria)
			var trs_seccion_estadistica = '';
			var html_trs_fijos 		  = $("#tabla_ppal tr.fijo").clone();
			var html_titulo_estadis   = $("#tabla_ppal tr.titulo_estad").clone();
			var html_trs_final 		  = $("#tabla_ppal tr.tr_final").clone();

			$("#tabla_filtros").html(html_trs_fijos);

			//--> Mantener valores por defecto
			$("#tabla_ppal").find('#filtro_selec_defecto').val(filtrado);
			$("#tabla_ppal").find('#cadena_val_selec_defecto').val(codigos);

			$("#tabla_ppal").hide();

			//--> Recorro todos los codigos (tipos) que maneja el filtro, para fitrar los indicadores
			var primer_segmento = 'si';
			var valoresArreglo 	= codigos.split("|<>|");
			recorrer_valores_filtro(valoresArreglo, filtrado, primera_carga, primer_segmento, 'indicador');

			// --> Agrego el tr del titulo de las estadisticas
			$("#tabla_filtros").append(html_titulo_estadis);
			//--> Recorro todos los codigos (tipos) que maneja el filtro, para fitrar las estadisticas
			recorrer_valores_filtro(valoresArreglo, filtrado, primera_carga, 'no', 'estadistica');

			$("#tabla_filtros").append(html_trs_final);

			// --> Resalto el tr seleccionado
			$("div[name=opc_visualizacion]").each(function(index)
				{
					$(this).css({
						'border'		  	: '',
						'background-color'	: ''
					});
				});
			$("div[val="+filtrado+"]").each(function(index)
				{
					$(this).css({
						'border'			: '2px outset #53A9FF',
						'background-color'  : '#FFFFFF'
					});
				});

			// --> Tooltip
			$("#tabla_filtros").find('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			$('[tooltip2=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			$("#tabla_filtros").show();
			$.unblockUI();

			// --> Posicionar la pantalla, donde se encontraba antes de ver el detalle de un indicador
			if($('#tag_llamado_posicion').attr('href') != 'NULL')
			{
				window.location = $('#tag_llamado_posicion').attr('href');
			}
			$('#tag_llamado_posicion').attr('href', 'NULL');
		}

		function recorrer_valores_filtro(valoresArreglo, filtrado, primera_carga, primer_segmento, tipo_tr)
		{
			$.each(valoresArreglo, function( i ){

				arr_valoresArreglo 	   = valoresArreglo[i].split("|");
				codigo_filtro 		   = "-"+arr_valoresArreglo[0]+"-";
				nombre_filtro 		   = arr_valoresArreglo[1];
				nombre_filtro_sin_espa = arr_valoresArreglo[2];

				// --> Si existe al menos un tr con el tipo de filtro
				if( $("#tabla_ppal tbody").find( "tr["+filtrado+"*="+codigo_filtro+"][tipo_tr="+tipo_tr+"]" ).length > 0 )
				{
					// --> Crear hiddens con los nombres de cada segmento que se pinte, para ir guardando si esta visible o no
					if($('#hidden_'+nombre_filtro_sin_espa+tipo_tr).attr('type') == undefined)
					{
						var guardar_hiddens = "<input type='hidden' id='hidden_"+nombre_filtro_sin_espa+tipo_tr+"'>";
						var hiden_existente = $('#hidden_segmentos').html();
						$('#hidden_segmentos').html(hiden_existente+guardar_hiddens);
					}

					// --> Pintar titulo del segmento
					var stylo_segmento = 'cursor:pointer;border: 1px solid #999999;color: #2A5DB0;font-family: verdana;font-size: 11pt;background-color: #EFEFEF;';

					var trEncabezado = "<tr><td style='"+stylo_segmento+"' colspan='14' onClick=\"toggle_segmento('"+i+"','"+nombre_filtro_sin_espa+tipo_tr+"');\">"
												+"<img id='img_seg"+nombre_filtro_sin_espa+tipo_tr+"' />&nbsp;&nbsp;"
												+nombre_filtro
												+"&nbsp;&nbsp;&nbsp;"
											+"</td>"
										+"</tr>";
					// --> Pintar la barra de los filtros nuevamente
					var botones_filtros = '';
					$("#tabla_filtros").find("div[name=opc_visualizacion]").each(function(index)
					{
						var subsegmento = 'ocultar'+i+'='+nombre_filtro_sin_espa+tipo_tr;
						if($(this).attr('filtrado') != filtrado)
							botones_filtros+='<div class="sub_opc_visualizacion'+nombre_filtro_sin_espa+tipo_tr+'" style="cursor:pointer;display:inline;" onClick="SubSegmentarTabla(this, \''+subsegmento+'\',\''+$(this).attr('filtrado')+'\', \''+tipo_tr+'\', \''+$(this).attr('codigos')+'\', \''+nombre_filtro_sin_espa+tipo_tr+'\')">'+$(this).attr('descripcion')+'</div>&nbsp;&nbsp;&nbsp;';
					});

					var BarraFiltros = '<tr class="fijo" ocultar'+i+'='+nombre_filtro_sin_espa+tipo_tr+'>'
											+'<td></td>'
											+'<td colspan="13" align="center" style="background-color: #FFFFE4;color: #2A5DB0;font-size: 11pt;border:solid 1px #2A5DB0;padding: 3px;">'
													+botones_filtros
									  +'</tr>';

					// --> Obtengo los tr con ese tipo de filtro para indicadores
					var trsDelFiltradoInd = $("#tabla_ppal tbody").find( "tr["+filtrado+"*="+codigo_filtro+"][tipo_tr="+tipo_tr+"]" ).clone();

					trsDelFiltradoInd.find('[tooltip=si]').each(function(){
							$(this).attr('titlesegmento', $(this).attr('title'))
						});

					// --> Pinto el titulo del segmento y los correspondientes tr's en la seccion de indicadores
					$("#tabla_filtros").append(trEncabezado);
					$("#tabla_filtros").append(BarraFiltros);
					$("#tabla_filtros").append(trsDelFiltradoInd);

					// --> Agregar atributos para conocer cuales tr debo ocultar segun cada agrupacion
					$("#tabla_filtros").find( "tr["+filtrado+"*="+codigo_filtro+"][tipo_tr="+tipo_tr+"]" ).each(function(index)
						{
							if($(this).attr('tiene_ocultar') != 'si')
							{
								$(this).attr('ocultar'+i, nombre_filtro_sin_espa+tipo_tr);
								$(this).attr('tiene_ocultar', 'si');
							}
						});

					// --> Si es la primera carga del programa, cierro los segmentos, menos el primero que se pinte
					if(primera_carga == 'si')
					{
						if(primer_segmento == 'si')
						{
							primer_segmento = 'no';
							$('#img_seg'+nombre_filtro_sin_espa+tipo_tr).attr('src', '../../images/medical/hce/menos.PNG');
							$("#hidden_"+nombre_filtro_sin_espa+tipo_tr).attr('visible', 'si');
						}
						else
						{
							$('[ocultar'+i+'='+nombre_filtro_sin_espa+tipo_tr+']').each(function(){
								$(this).hide();
							});
							$('#img_seg'+nombre_filtro_sin_espa+tipo_tr).attr('src', '../../images/medical/hce/mas.PNG');
							$("#hidden_"+nombre_filtro_sin_espa+tipo_tr).attr('visible', 'no');
						}
					}
					// --> Mantengo el cuadro tal cual como el usuario lo ha dejado
					else
					{
						if ($("#hidden_"+nombre_filtro_sin_espa+tipo_tr).attr('visible') == 'si')
						{
							$('#img_seg'+nombre_filtro_sin_espa+tipo_tr).attr('src', '../../images/medical/hce/menos.PNG');
						}
						else
						{
							$('#img_seg'+nombre_filtro_sin_espa+tipo_tr).attr('src', '../../images/medical/hce/mas.PNG');
							$('[ocultar'+i+'='+nombre_filtro_sin_espa+tipo_tr+']').each(function(){
								$(this).hide();
							});
						}
					}
				}
			});
		}

		function toggle_segmento(consecutivo, nombre_seg)
		{
			$('[ocultar'+consecutivo+'='+nombre_seg+']').each(function(){
				if($("#hidden_"+nombre_seg).attr('visible') == 'si')
				{
					$("[eliminartr"+nombre_seg+"]").remove();
					// --> Dejo los botones del subsegmento sin resaltar
					$(".sub_opc_visualizacion"+nombre_seg).each(function(index)
						{
							$(this).css({
								'border'		  	: '',
								'background-color'	: ''
							});
						});

					$(this).hide();
				}
				else
				{
					$(this).show();
				}
			});
			cambiar_img_seg(nombre_seg);
		}
		//----------------------------------------------------------------------------
		// --> Funcion que subsegmenta una seccion ya segmentada
		//----------------------------------------------------------------------------
		function SubSegmentarTabla(div_boton, subsegmento, filtrado, tipo_tr, codigos, nombre_segmento)
		{
			var valoresArreglo 	= codigos.split("|<>|");
			$("[eliminartr"+nombre_segmento+"]").remove();
			var nombre_filtro      = "";
			var	nom_filtr_sin_espa = "";
			var ArrInfFiltros  = {};

			//--> Creo un array de codigos con su respectivo nombre, de todas las opciones que maneja el filtro
			$.each(valoresArreglo, function( i ){
					arr_valoresArreglo 	   = valoresArreglo[i].split("|");
					codigo_filtro 		   = "-"+arr_valoresArreglo[0]+"-";
					codigo_filtro+='C';
					nombre_filtro 		   = arr_valoresArreglo[1];
					nombre_filtro_sin_espa = arr_valoresArreglo[2];
					ArrInfFiltros[codigo_filtro] = {};
					ArrInfFiltros[codigo_filtro]['nombre_filtro'] 	   = nombre_filtro;
					ArrInfFiltros[codigo_filtro]['nomb_filt_sin_espa'] = nombre_filtro_sin_espa;
			});

			var cod_valor			= "";
			var ArrSubsegment  		= {};
			var consecutivo 		= '0';
			var primera_vez 		= 'si';
			var imagen      		= "menos";
			var cod_valor_visible 	= '';

			// --> Recorro cada uno de los tr que tiene el segmento, y voy armando un array donde van a quedar agrupados
			// 	   los tr segun cada opcion del filtro.
			$("["+subsegmento+"]").each(function(){
				if($(this).attr('class') !='fijo')
				{
					$(this).show();
					var tr_original = $(this).clone();
					$(this).hide();

					if(tr_original.attr(filtrado) != undefined)
					{
						var arr_valores = tr_original.attr(filtrado).split(",");
						jQuery.each(arr_valores, function(i, cod_valor) {
							cod_valor+='C';
							nombre_filtro       = ArrInfFiltros[cod_valor]['nombre_filtro'];
							nom_filtr_sin_espa  = ArrInfFiltros[cod_valor]['nomb_filt_sin_espa'];
							if(primera_vez == 'si')
								cod_valor_visible = cod_valor;

							if( ArrSubsegment[cod_valor] == undefined)
							{
								ArrSubsegment[cod_valor] = {};
								var barra_html = '<tr eliminartr'+nombre_segmento+' ><td></td><td colspan="13" onClick="$(\'['+nombre_segmento+nom_filtr_sin_espa+']\').toggle();cambiar_img_seg(\''+nombre_segmento+nom_filtr_sin_espa+'\')" style="cursor:pointer;border: 1px solid #999999;color: #2A5DB0;font-family: verdana;font-size: 10pt;background-color: #F8F8F8;"><img id="img_seg'+nombre_segmento+nom_filtr_sin_espa+'" src="../../images/medical/hce/'+imagen+'.PNG">&nbsp;&nbsp;'+nombre_filtro+'</td></tr>';
								ArrSubsegment[cod_valor]['tr_barra'] =  barra_html;
								ArrSubsegment[cod_valor]['tr_indicadores'] = {};
							}

							// --> Agregarle una clase que los identifique para poder hacerle toggle
							var tr_objeto   = $('<div>').append(tr_original).html();
							if(cod_valor_visible == cod_valor)
								tr_objeto = tr_objeto.replace('<tr ','<tr eliminartr'+nombre_segmento+' '+nombre_segmento+nom_filtr_sin_espa+' tooltip_subsegmento ');
							else
								tr_objeto = tr_objeto.replace('<tr ','<tr eliminartr'+nombre_segmento+' style="display:none" '+nombre_segmento+nom_filtr_sin_espa+' tooltip_subsegmento ');

							ArrSubsegment[cod_valor]['tr_indicadores'][consecutivo++] =  tr_objeto;
							imagen		= 'mas';
							primera_vez = 'no';
						});
					}
				}
			});


			// --> Recorrer el array e ir armando el codigo html
			var codigo_html = "";
			jQuery.each(ArrSubsegment, function(i, info_html) {
				codigo_html+= info_html.tr_barra;
				jQuery.each(info_html.tr_indicadores, function(x, tr_ind_html){
					codigo_html+=tr_ind_html;
				});
			});
			// --> Agrego el codigo html despues de la barra de botones
			var barra_subsegmento = $("["+subsegmento+"][class=fijo]");
			barra_subsegmento.after(codigo_html);

			// --> Tooltip
			$('[tooltip_subsegmento]').find('[tooltip=si]').each(function(){
				$(this).attr('title', $(this).attr('titlesegmento'));
				$(this).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			});
			// --> Los demas botones los dejo normal
			$(".sub_opc_visualizacion"+nombre_segmento).each(function(index)
				{
					$(this).css({
						'border'		  	: '',
						'background-color'	: ''
					});
				});
			// --> Resalto el boton seleccionado
			$(div_boton).css({
				'border'			: '1px outset #53A9FF',
				'background-color'  : '#FFFFFF'
			});
		}
		//-------------------------------------------------
		// --> Cambiar imagen de los subseqmentos, + por -
		//-------------------------------------------------
		function cambiar_img_seg(nombre_filtro)
		{
			if($('#img_seg'+nombre_filtro).attr('src') == '../../images/medical/hce/mas.PNG')
			{
				$('#img_seg'+nombre_filtro).attr('src', '../../images/medical/hce/menos.PNG');
				$("#hidden_"+nombre_filtro).attr('visible', 'si');
			}
			else
			{
				$('#img_seg'+nombre_filtro).attr('src', '../../images/medical/hce/mas.PNG');
				$("#hidden_"+nombre_filtro).attr('visible', 'no');
			}
		}
		function cerrar_seccion(id)
		{
			$("#"+id).toggle("normal");
		}

		// --> Jquery para seleccionar la cantidad de meses a pintar
		function pluggin_cantidad_meses()
		{
			$( "#slider-range-max" ).slider({
			range: "max",
			min: 1,
			max: 12,
			value: 12,
			slide: function( event, ui ) {
				$( "#cantidad_meses" ).val( ui.value );
			},
			change: function( event, ui ) {
				generar_tablero('normal');
			}
			});

			$( "#cantidad_meses" ).val( $( "#slider-range-max" ).slider( "value" ) );
		}
		function iluminar_check(id_check)
		{
			if($('#'+id_check).attr('checked') == 'checked')
			{
				$('#div_iluminar').attr('style', 'display:inline;background:#DADADA;cursor:pointer;border: 1px solid #999999; width: 20px; height:20px; align:center;');
			}
			else
			{
				$('#div_iluminar').attr('style', 'display:inline;');
			}
		}
		function cerrar_flotante()
		{
			$('#convenciones_flotante').hide(500);
			$('#ver_convenciones').show(500);
		}
		function cargar_flotante()
		{
			posicion = $('#cuadro_mando').offset();
			$('#convenciones_flotante').css({'right':posicion.left,'top':posicion.top});
			$('#convenciones_flotante').show(300);
			$('#convenciones_flotante').draggable();
			$('#ver_convenciones').hide();
		}
		//-------------------------------
		//	Al cargar la pagina
		//-------------------------------
		$(document).ready(function()
		{
			if (browser=="Microsoft Internet Explorer" || browser=="Netscape")
			{
				setInterval( "parpadeo()", 600 );
			}
			pluggin_cantidad_meses();
			// --> Pintar seccion de indicadores segun el filtro por defecto
			var filtrado = $('#filtro_selec_defecto').val();
			var codigos  = $('#cadena_val_selec_defecto').val();
			if(filtrado != undefined && codigos != undefined )
				SegmentarTabla(filtrado, codigos, 'si');
				//$.unblockUI();

			// --> Caja flotante de convenciones y semaforizacion
			cargar_flotante();
		});
	</script>

	<style type="text/css">
		.mes{
			background: #2A5DB0; border: 2px solid #D3D3D3;color: #FFFFFF;font-weight: normal;outline: medium none;margin: 3px; padding: 5px; height: 50px; font-size: 1.5em; text-align: center;
		}
		.mes_tooltip{
			background: #2A5DB0; border: 1px solid #D3D3D3;color: #FFFFFF;font-weight: normal;outline: medium none;margin: 1px; padding: 1px; height: 18px; font-size: 15px; text-align: center;
		}
		.ano{
			background: #2A5DB0; border: 2px solid #D3D3D3;color: #FFFFFF;font-weight: normal;margin: 1 auto; padding: 1 auto; height: 33px; font-size: 1em; text-align: center;
		}
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:2px;opacity:1;}
		#tooltip h7, #tooltip div{margin:0; width:auto}

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
        /*top:0;*/
        /*left: 10px;*/
        border: 1px solid #CCC;
        background-color: #F2F2F2;
		}
		.opcionOperaciones {
			cursor:pointer;
			border: 1px solid #999999;
			background-color:#D2E8FF;
			width:40px;
			height:30px;
		 }
	</style>
	<body>
	<?php
	//--------------------------------------------
	// 		Tabla de convenciones
	//--------------------------------------------
	// --> Si es el cuadro de mando de consulta.
	echo '<div id="convenciones_flotante" align="right" style="cursor:pointer;border:2px solid #E2B709;background-color: #FFFFFF;display:none;width:200px;position: fixed;">
			<img onClick="cerrar_flotante();" style="width:11px;height:11px;" src="../../images/medical/eliminar1.png" title="Cerrar Opciones">
			<table style="width:200px;cursor:move">
				<tr><td colspan="2" align="center" style="background: #2A5DB0; border: 1px solid #D3D3D3;color: #FFFFFF;margin: 1px; padding: 1px;font-size: 10px; text-align: center"><b>Convenciones</b></td></tr>';
	if(isset($cmi_consulta) && $cmi_consulta == 'on')
	{
		echo'	<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="width:50%;border: 1px solid #999999;">Modificable<br><img height="16" width="16" src="../../images/medical/sgc/Refresh-128.png"></td>
					<td style="width:50%;border: 1px solid #999999;">Configuración errónea<br><img width="16" height="16"  src="../../images/medical/sgc/Warning-32.png"></td>
				</tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="border: 1px solid #999999;">Sin Calcular<br><img height="15" width="15" src="../../images/medical/root/info.png" title="Sin calcular"></td>
					<td style="border: 1px solid #999999;">Calculo Temporal<br><img width="15" height="15"  src="../../images/medical/reloj.gif"></td>
				</tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="border: 1px solid #999999;">Calculo Desactualizado<br><img width="15" height="15"  src="../../images/medical/sgc/grabar_des.png"></td>
					<td style="border: 1px solid #999999;">Calculo Definitivo<br><img width="15" height="15"  src="../../images/medical/root/grabar.png"></td>
				</tr>
			</table>
			<table style="width:200px;cursor:move">
				<tr><td colspan="2" align="center" style="background: #2A5DB0; border: 1px solid #D3D3D3;color: #FFFFFF;margin: 1px; padding: 1px;font-size: 10px; text-align: center"><b>Semaforización</b></td></tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="width:50%;border: 1px solid #999999;">Estándar Superior<br><img width="15" height="15" src="../../images/medical/sgc/estandar_superior.png"></td>
					<td style="width:50%;border: 1px solid #999999;">Conforme<br><img width="15" height="15" src="../../images/medical/sgc/conforme.png"></td>
				</tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="border: 1px solid #999999;">Aceptable<br><img width="15" height="15" src="../../images/medical/sgc/aceptable.png"></td>
					<td style="border: 1px solid #999999;">No conforme<br><img width="15" height="15" src="../../images/medical/sgc/no_conforme.png"></td>
				</tr>
			</table>';
	}
	else
	{
		echo'	<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="width:50%;border: 1px solid #999999;">Sin Calcular<br><img height="15" width="15" src="../../images/medical/root/info.png" title="Sin calcular"></td>
					<td style="width:50%;border: 1px solid #999999;">Calculo Temporal<br><img width="15" height="15"  src="../../images/medical/reloj.gif"></td>
				</tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="border: 1px solid #999999;">Calculo Desactualizado<br><img width="15" height="15"  src="../../images/medical/sgc/grabar_des.png"></td>
					<td style="border: 1px solid #999999;">Calculo Definitivo<br><img width="15" height="15"  src="../../images/medical/root/grabar.png"></td>
				</tr>
			</table>
			<table style="width:200px">
				<tr><td colspan="2" align="center" style="background: #2A5DB0; border: 1px solid #D3D3D3;color: #FFFFFF;margin: 1px; padding: 1px;font-size: 10px; text-align: center"><b>Semaforización</b></td></tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="width:50%;border: 1px solid #999999;">Estándar Superior<br><img width="15" height="15" src="../../images/medical/sgc/estandar_superior.png"></td>
					<td style="width:50%;border: 1px solid #999999;">Conforme<br><img width="15" height="15" src="../../images/medical/sgc/conforme.png"></td>
				</tr>
				<tr style="color: #000000;font-size: 10px;" align="center">
					<td style="border: 1px solid #999999;">Aceptable<br><img width="15" height="15" src="../../images/medical/sgc/aceptable.png"></td>
					<td style="border: 1px solid #999999;">No conforme<br><img width="15" height="15" src="../../images/medical/sgc/no_conforme.png"></td>
				</tr>
			</table>';
	}
	echo '
			</table>
		</div>';
	//----------------------------
	//    Calendario de mes
	//----------------------------
	echo '
	<div id="caja_flotante" style="display:none;z-index:3000">
        <div id="cont_caja_flotante" style="border:solid 1px orange;">
            <table>
				<tr>
					<td colspan=3 align="right" style="cursor:pointer;" onClick="$(\'#caja_flotante\').hide(500);"><img class="img_del1" border="0" src="../../images/medical/eliminar1.png" title="Cerrar Opciones" ></td>
				<tr>
				<tr>
					<td colspan="3" align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:12pt;text-align:center;font-weight:bold;">Año:
							<select id="año_sel" name="año_sel" style="width:80px;height:20px;border: 1px solid #FFA500;background-color:#FFFFE1;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:9pt;text-align:center;font-weight:bold;">';
							$año_inicio = '2006';
							$año_actual = date('Y');
							for($x=$año_inicio; $x <= $año_actual+1; $x++)
							{
								echo "<option ".(($año_actual==$x)? 'SELECTED':'').">".$x."</option>";
							}
	echo '					</select>
					</td>
				</tr>
                <tr>
                    <td id="ene" style="display:none;" class="opcionOperaciones" ref="1" onClick="asignar_año_mes(\'1\');">Ene</td>
                    <td id="feb" style="display:none;" class="opcionOperaciones" ref="2" onClick="asignar_año_mes(\'2\');">Feb</td>
                    <td id="mar" style="display:none;" class="opcionOperaciones" ref="3" onClick="asignar_año_mes(\'3\');">Mar</td>
				</tr>
				<tr>
                    <td id="abr" style="display:none;" class="opcionOperaciones" ref="4" onClick="asignar_año_mes(\'4\');">Abr</td>
                    <td id="may" style="display:none;" class="opcionOperaciones" ref="5" onClick="asignar_año_mes(\'5\');">May</td>
                    <td id="jun" style="display:none;" class="opcionOperaciones" ref="6" onClick="asignar_año_mes(\'6\');">Jun</td>
                </tr>
				<tr>
                    <td id="jul" style="display:none;" class="opcionOperaciones" ref="7" onClick="asignar_año_mes(\'7\');">Jul</td>
                    <td id="ago" style="display:none;" class="opcionOperaciones" ref="8" onClick="asignar_año_mes(\'8\');">Ago</td>
                    <td id="sep" style="display:none;" class="opcionOperaciones" ref="9" onClick="asignar_año_mes(\'9\');">Sep</td>
                </tr>
				<tr>
                    <td id="oct" style="display:none;" class="opcionOperaciones" ref="10" onClick="asignar_año_mes(\'10\');">Oct</td>
                    <td id="nov" style="display:none;" class="opcionOperaciones" ref="11" onClick="asignar_año_mes(\'11\');">Nov</td>
                    <td id="dic" style="display:none;" class="opcionOperaciones" ref="12" onClick="asignar_año_mes(\'12\');">Dic</td>
                </tr>
            </table>
        </div>
	</div>
	';
	//----------------------------
	//    Fin calendario de mes
	//----------------------------
	//----------------------------
	//    ENCABEZADO
	//----------------------------
	if(isset($cmi_consulta) && $cmi_consulta='on')
		$titulo = "CMI - Cuadro de Mando Integral (Consulta)";
	else
		$titulo = "CMI - Cuadro de Mando Integral";

	encabezado($titulo, $wactualiz, 'clinica');
	echo "<script>mensaje_espera();</script>";
	//----------------------------
	//	SELECCION DE MES
	//----------------------------
	$fecha	= 	explode ('-', $wfecha);
	$mes	=	$fecha[1];
	if ($mes<10)
		$mes =	substr($mes, 1, 1);
	$fecha_defecto	=	$fecha[0].'-'.$mes;
	echo"	<div align='center'>
				<br>
				<div class='Titulo_azul borderDiv' style='width:50%; align:center;' >
				<table width='100%'>
					<tr>
						<td width='17%' align='center' style='border: 1px solid #2A5DB0;background-color: #D2E8FF;color: #000000;font-size: 10pt;cursor:pointer;' onClick='generar_tablero(\"atras\");$(\"#contenedor_graficador2\").hide()'><b><< Anterior</b></td>
						<td width='9%'></td>
						<td align='center'>
							ÚLTIMO MES:&nbsp;&nbsp;&nbsp;
							<input type='text' id='selecccionar_mes' size='7' value='".$fecha_defecto."' style='font-weight: bold; font-family: verdana;font-size: 12pt;border: 0'/ >
							<img id='calendar' width='23' height='23' style='cursor:pointer;' onclick='posicionElemento(this,\"selecccionar_mes\");' src='../../images/medical/sgc/Calendar.png'>
						</td>
						<td width='9%'></td>
						<td width='17%' align='center' style='border: 1px solid #2A5DB0;background-color: #D2E8FF;color: #000000;font-size: 10pt;cursor:pointer;' onClick='generar_tablero(\"adelante\");$(\"#contenedor_graficador2\").hide();'><b>Siguiente >></b></td>
					</tr>
					<tr>
						<td width='17%'></td>
						<td width='9%'></td>
						<td align='center'>
							CANTIDAD DE MESES:
							<input type='text' id='cantidad_meses' style='font-family: verdana;font-size: 12pt;border: 0; color: #000000; font-weight: bold;' size='2'/><br>
							<div  id='slider-range-max'></div>
						</td>
						<td width='9%'></td>
						<td width='17%'></td>
					</tr>";
	if(isset($cmi_consulta) && $cmi_consulta == 'on')
	{
		echo"		<tr>
						<td colspan='5' align='center' style='padding: 5px;'>
							SOLO PENDIENTES DE CALCULAR:
							<div id='div_iluminar' style='display:inline;'>
								<input type='checkbox' id='pendientes_calculo' style='cursor:pointer; font-family: verdana;font-size: 12pt;' onClick='iluminar_check(\"pendientes_calculo\");generar_tablero(\"normal\");'/>
							</div>
						</td>
					</tr>";
	}
	echo"			<tr id='ver_convenciones'>
						<td colspan='5' align='right'>
							<div style='font-size: 9pt;cursor:pointer' onClick='cargar_flotante();'>
								Ver Convenciones
								<img src='../../images/medical/hce/mas.PNG' >
							</div>
						</td>
					</tr>
				</table>
				</div><br><br>
				<table width='85%' border='0' cellpadding='3' cellspacing='3' align='center' >
				<tr>
					<td align='center' class='borderDiv2' id='cuadro_mando'>";
					pintar_meses($fecha_defecto, '%', '', $wemp_pmla, 12, 'off');
	echo "
					</td>
				</tr>
				<tr>
					<td align='center' id='div_calcular' >
						<div class='borderDiv2' id='contenedor_graficador2' style='display:none'>
						</div>
					</td>
				</tr>
				</table>
				<br>
				<div align='center' id='div_cerrar_ventana'>
					<input type=submit value='Cerrar Ventana' onclick='cerrarVentana()'>
				</div><br>
			</div>
	";
	// --> Este div es temporal para guardar unos hidden que me dicen si los segmentos estaban desplegadas o no
	echo'<div id="hidden_segmentos" style="display:none;"></div>';
	echo '<a id="tag_llamado_posicion" href="NULL"></a>'
	?>
	</BODY>
	</HTML>
	<?php
}//Fin de ejecucion normal del programa
}//Fin de session
?>