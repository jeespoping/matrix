<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION:
/*



//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION	
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-01-29	|	Jerson	|	Se corrige error que existia en producción tambien, al crear aun política nueva no quedaba guardada
	MIGRA_2	|	2019-01-29	|	Jerson	|	Se coloca utf8_encode al obtener el nombre del procedimento
	MIGRA_3	|	2019-01-29	|	Jerson	|	Se coloca utf8_encode al obtener el nombre del articulo


	
	
*/
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
// 2020-05-26: jerson trujillo, se agrega una nueva politica de Cobros por meses calendario (Sin importar el ingreso).
// 2018-02-20 Felipe alvarez Sanchez Se añade la politica de modificacion de cargos  grabados  por estancia, la politica de estancia pide  tipo de habitacion
// concepto , procedimiento , y si el cargo es facturable , esta funciona  de la siguiente manera al momento de grabar la estancia,
//    mira si hay cargos como este cobrados en habitaciones de UCI o UCE (o la habitacion especificada) y los cambia a no facturables, El programa Evaluara los cargos y hara elemento
//    cambio leyendo politicas, adicional a esto estos cargos quedaran marcados pues si hay una devolucion quedaran en el estado inicial (si antes estaban facturables
//    cuando se pase a no facturables por la politica y luego se anulen quedaran denuevo en no facturables )

 
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='2020-05-26';
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
	$wuse		 	= $user_session[1];
	

	include_once("root/comun.php");
	

	include_once("ips/funciones_facturacionERP.php");
	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex 			= obtenerConexionBD("matrix");
	$wbasedato 		= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha			= date("Y-m-d");
    $whora 			= date("H:i:s");
	
	//print_r($_SERVER);

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function generar_codigo()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wfecha;
		global $whora;

		// --> Bloquear la tabla contra escritura
		$q = "lock table ".$wbasedato ."_000155 LOW_PRIORITY WRITE";
		$err = mysql_query($q, $conex);

		// --> Consultar el ultimo codigo generado
		$q_codigo =" SELECT max(SUBSTRING( Polcod FROM 2)*1) as Polcod
					   FROM ". $wbasedato ."_000155
					";
		$res_codigo = mysql_query($q_codigo, $conex) or die (mysql_errno(). " - en el query (Consultar ultimo codigo): ".$q_codigo . " - " . mysql_error());
		$row_codigo = mysql_fetch_array($res_codigo);

		// --> Genero el codigo para la politica
		$nuevo_cod	= $row_codigo['Polcod']+1;
		$wcodigo	= 'P'.$nuevo_cod;

		// --> Desbloqueo la tabla y envio el codigo generado
		$q = " UNLOCK TABLES";
		$err = mysql_query($q, $conex) or die (mysql_errno() . $q . " - " . mysql_error());
		return $wcodigo;
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function lista_politicas($PolCodigo = '', $PolNombre = '', $PolTipo = '', $PolCodCP = '', $PolEntidad = '', $buscEstado = '')
	{
		global $wbasedato;
		global $conex;

		$q_politicas = "		SELECT Polcod, Polnom, Poltpa, Polccp, Polcen, CAST(REPLACE(Polcod, 'P', '') AS SIGNED) AS CONSECUTIVO, Polest
								  FROM ".$wbasedato."_000155
								 WHERE (Polest = 'on' OR Polest = 'off')
		".(($PolCodigo !='') ? "   AND Polcod LIKE '%".$PolCodigo."%'" : "")."
		".(($PolNombre !='') ? "   AND Polnom LIKE '%".$PolNombre."%'" : "")."
		".(($PolTipo !='' && $PolTipo == 'on') ? "     AND Poltpa = 'on' " : "")."
		".(($PolTipo !='' && $PolTipo == 'off') ? "    AND Poltpa != 'on' " : "")."
		".(($PolCodCP !='') ? "    AND Polccp = '".$PolCodCP."'" : "")."
		".(($PolEntidad !='') ? "  AND Polesp = '".$PolEntidad."'" : "")."
		".(($buscEstado !='') ? "  AND Polest = '".$buscEstado."'" : "")."
							  ORDER BY CONSECUTIVO
		";
		$r_politicas = mysql_query($q_politicas,$conex) or die("Error en el query: ".$q_politicas."<br>Tipo Error:".mysql_error());

		echo "
		<table width='100%' id='tabla_lista' numRegistros='".mysql_num_rows($r_politicas)."'>
			<tr class='encabezadoTabla' align='center'>
				<td>Código</td><td>Nombre</td><td>Tipo</td><td>Concepto/Paquete</td><td>Entidad</td><td>Estado</td>
			</tr>";
		$arr_conceptos 	= obtener_array_conceptos();
		$arr_paquetes 	= Obtener_array_paquetes();
		$arr_entidades 	= obtener_array_entidades();
		$color_fila 	= 'fila1';
		if (mysql_num_rows($r_politicas) >0)
		{
			while ($row_politicas = mysql_fetch_array($r_politicas))
			{
				if ($fila_lista=='fila2')
					$fila_lista = "fila1";
				else
					$fila_lista = "fila2";

				echo"
				<tr class='".$fila_lista."' OnClick='nueva_politica(\"".$row_politicas['Polcod']."\");' style='cursor:pointer;'>
					<td width='4%'>".$row_politicas['Polcod']."</td>
					<td width='32%'>".$row_politicas['Polnom']."</td>
					<td width='10%' align='center'>".(($row_politicas['Poltpa'] == 'on') ? 'Paquete' : 'Concepto')."</td>
					<td width='25%'>".(($row_politicas['Poltpa'] == 'on') ? $arr_paquetes[$row_politicas['Polccp']] : $arr_conceptos[$row_politicas['Polccp']])."</td>
					<td width='25%'>".(($row_politicas['Polcen'] != '*') ? $arr_entidades[$row_politicas['Polcen']] : 'TODOS')."</td>
					<td width='4%'>".(($row_politicas['Polest'] == 'on') ? 'Activo' : 'Inactivo')."</td>
				</tr>
				";
			}
		}
		else
		{
			echo'
				<tr class="fila2" >
					<td colspan="6" align=center><b>No se encontraron políticas.</b></td>
				</tr>';
		}
		echo"
		</table>
		";
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function cargar_hiddens_para_autocomplete()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;

		// --> Codigos de convenios de las entidades
		echo "<input type='hidden' id='hidden_entidades' value='".json_encode(obtener_array_entidades())."'>";
		// --> Especialidades
		echo "<input type='hidden' id='hidden_especialidades' value='".json_encode(obtener_array_especialidades())."'>";
		// --> Conceptos
		echo "<input type='hidden' id='hidden_conceptos' value='".json_encode(obtener_array_conceptos())."'>";
		// --> Procedimientos
		echo "<input type='hidden' id='hidden_procedimientos' value='".json_encode(obtener_array_procedimientos($conex, $wemp_pmla, $wbasedato))."'>";
		// --> Paquetes
		echo "<input type='hidden' id='hidden_paquetes' value='".json_encode(Obtener_array_paquetes())."'>";
		// --> Centros de costos
		echo "<input type='hidden' id='hidden_cco' value='".json_encode(Obtener_array_cco())."'>";
		// --> Hidden de array de las tarifas
		//echo "<input type='hidden' id='hidden_tarifas' name='hidden_tarifas' value='".json_encode(Obtener_array_tarifas())."'>";
		// --> Hidden de array de los tipos de empresa
		echo "<input type='hidden' id='hidden_tiposEmpresa' value='".json_encode(Obtener_array_tipos_empresa())."'>";
		// --> Hidden con los tipos de habitaciones
		echo "<input type='hidden' id='hidden_tiposHabitaciones' value='".json_encode(arrayTiposHabitaciones())."'>";

	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function ObtenerMensajesAyuda(&$MsjAyuda1, &$MsjAyuda2, &$MsjAyuda3, &$MsjAyuda4, &$MsjAyuda5, &$MsjAyuda6)
	{
		global $wbasedato;
		global $conex;

		$MsjAyuda1 = "
			<table>
				<tr class=\"encabezadoTabla\">
					<td>Ayuda, Datos Basicos</td>
				</tr>
			</table>
		";
		$MsjAyuda2 = "
			<table style=\"width:300px\">
				<tr class=\"encabezadoTabla\">
					<td align=\"center\">Ayuda, Restricción de días</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fondoAmarillo\">
					<td>
						Aplica cuando se quiera limitar el número de cobros al día de un determinado concepto,
						cuando no se pueda cobrar un concepto durante un determinado periodo de estancia de un paciente.
					</td>
				</tr>
				<tr class=\"fila1\">
					<td>Número de veces al día que se cobra:</td>
				</tr>
				<tr class=\"fila2\" style=\"font-weight:normal;text-align: justify;\">
					<td>Cuantas veces al día se permite grabarle  a un paciente el concepto-procedimiento registrado en los datos básicos. </td>
				</tr>
				<tr class=\"fila1\">
					<td>Que se debe cobrar:</td>
				</tr>
				<tr class=\"fila2\" style=\"font-weight:normal;text-align: justify;\">
					<td>
						-Indica cual es el concepto-procedimiento que se cobrará cuando se cumpla el número de veces al día que se permite grabar.
						-Puede ser otro concepto-procedimiento diferente al registrado en los datos básicos.
						-Si los campos concepto-procedimiento están en blanco, indica que se cobrara el concepto-procedimiento registrado en los datos básicos.
					</td>
				</tr>
			</table>
		";
		$MsjAyuda3 = "
			<table style=\"width:300px\">
				<tr class=\"encabezadoTabla\">
					<td>Ayuda, Restricción de rango de horas</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fondoAmarillo\">
					<td>
						Aplica cuando se quiera limitar el número de cobros durante toda la estancia del paciente,
						de un determinado concepto
					</td>
				</tr>
			</table>
		";
		$MsjAyuda4 = "
			<table style=\"width:300px\">
				<tr class=\"encabezadoTabla\">
					<td>Ayuda, No facturables</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fondoAmarillo\">
					<td>
						Aplica cuando al cobrar un determinado concepto, este implique el no cobro de otro
						concepto que previamente haya sido cargado en la cuenta del paciente.
					</td>
				</tr>
			</table>
		";
		$MsjAyuda5 = "
			<table style=\"width:300px\">
				<tr class=\"encabezadoTabla\">
					<td>Ayuda, Recargos</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fondoAmarillo\">
					<td>
						Aplica cuando dependiendo del día o de la hora, se requiera aplicar un recargo o un aumento
						en el valor de cobro de un determinado concepto.
					</td>
				</tr>
			</table>
		";
		$MsjAyuda6 = "
			<table style=\"width:500px\">
				<tr class=\"encabezadoTabla\">
					<td colspan =\"2\">Ayuda, Estancia</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila1\">
					<td>
						Apartir de que día se cobra la estancia:
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila2\">
					<td>
						Cantidad de dias que no se cobran desde el ingreso del paciente<br>
						Ejemplo: si se pone 1  , no se estaria combrando dia de ingreso
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila1\">
					<td>
						Numero de días que se restan de la estancia:
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila2\">
					<td>
						Cantidad de dias que no se cobran, restados desde la fecha de egreso
						Ejemplo: si se pone 1  , no se estaria combrando dia de egreso
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila1\">
					<td>
						Cobrar estancia por traslado basado en:
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila2\">
					<td>
						Sirve para definir  cual va a ser el criterio para cobrar <br>
						la habitacion el dia que hayan traslados.
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila1\">
					<td>
						Tiempo minimo:
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila2\">
				<td>
						Define cuanto es lo minimo que tiene que estar un paciente para que se cobre la habitacion. si
						la estancia no supera este tiempo no se tendria en cuenta.
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila1\">
					<td>
						Cuidado Critico:
					</td>
				</tr>
				<tr style=\"font-weight:normal;text-align: justify;\" class=\"fila2\">
					<td>
						Aqui se ponen las habitaciones que se pagan siempre y cuando haya
						un tiempo minimo de estancia  y si no cumple se cobra otro concepto y procedimiento
					</td>
				</tr>

			</table>
		";
	}
	//--------------------------------------------------------------------------
	//	Funcion que pinta el formulario para ingresar o modificar una politica
	//--------------------------------------------------------------------------
	function PintarFormularioPolitica($CodigoPolitica)
	{
		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		// --> Definicion de mensajes de ayuda
		$MsjAyuda1 = '';
		$MsjAyuda2 = '';
		$MsjAyuda3 = '';
		$MsjAyuda4 = '';
		$MsjAyuda5 = '';
		$MsjAyuda6 = '';

		ObtenerMensajesAyuda($MsjAyuda1, $MsjAyuda2, $MsjAyuda3, $MsjAyuda4, $MsjAyuda5, $MsjAyuda6);
		// --> Fin mensajes de ayuda

		// --> Consultar informacion de la politica
		if($CodigoPolitica != 'Nueva')
		{
			$q_info = "SELECT *
						 FROM ".$wbasedato."_000155
						WHERE Polcod = '".$CodigoPolitica."'
			";
			$r_info = mysql_query($q_info,$conex) or die("Error en el query: ".$q_info."<br>Tipo Error:".mysql_error());
			if(mysql_num_rows($r_info) > 0)
			{
				$info_politica = mysql_fetch_array($r_info);

				// --> Consultar el nombre del paquete
				if ($info_politica['Poltpa'] == 'on')
				{
					$q_ccp = " SELECT Paqnom
								 FROM ".$wbasedato."_000113
								WHERE Paqcod = '".$info_politica['Polccp']."'
					";
					$r_ccp 		= mysql_query($q_ccp,$conex) or die("Error en el query: ".$q_ccp."<br>Tipo Error:".mysql_error());
					$row_ccp 	= mysql_fetch_array($r_ccp);
					$nom_concep_o_paqu = $row_ccp['Paqnom'];
				}
				// --> Consultar el nombre del concepto Y del procedimiento
				else
				{
					$q_ccp = " SELECT Grudes, Grutpr
								 FROM ".$wbasedato."_000200
								WHERE Grucod = '".$info_politica['Polccp']."'
					";
					$r_ccp 		= mysql_query($q_ccp,$conex) or die("Error en el query: ".$q_ccp."<br>Tipo Error:".mysql_error());
					$row_ccp 	= mysql_fetch_array($r_ccp);
					$nom_concep_o_paqu = $row_ccp['Grudes'];

				}
				// --> Obtener el nombre de la especialidad
				if ($info_politica['Polesp'] == '*')
					$nom_esp = 'TODOS';
				else
				{
					$wbaseda_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
					$q_especialidad = " SELECT Espnom
										  FROM ".$wbaseda_movhos."_000044
										 WHERE Espcod = '".$info_politica['Polesp']."'
									";
					$r_especialidad 	= mysql_query($q_especialidad,$conex) or die("Error en el query: ".$q_especialidad."<br>Tipo Error:".mysql_error());
					$row_especialidad	= mysql_fetch_array($r_especialidad);
					$nom_esp 			= $row_especialidad['Espnom'];
				}
				// --> Obtener nombre del centro de costos
				if ($info_politica['Polcco'] == '*')
					$nom_cco = 'TODOS';
				else
				{
					$nom_cco = Obtener_array_cco($info_politica['Polcco']);
					$nom_cco = $nom_cco[$info_politica['Polcco']];
				}

				// --> Obtener nombre del centro de costos actual del paciente
				if ($info_politica['Polcca'] == '*')
					$nom_ccoAct = 'TODOS';
				else
				{
					$nom_ccoAct = Obtener_array_cco($info_politica['Polcca']);
					$nom_ccoAct = $nom_ccoAct[$info_politica['Polcca']];
				}

				// --> Obtener nombre del tipo de empresa
				if($info_politica['Poltem'] == '*')
					$nom_tipoEmpresa = 'TODOS';
				else
				{
					$q_tipoEmpresa  = "SELECT Temdes
										 FROM ".$wbasedato."_000029
										WHERE Temcod = '".$info_politica['Poltem']."' ";
					$res_tipoEmpresa = mysql_query($q_tipoEmpresa,$conex) or die("Error en el query: ".$q_tipoEmpresa."<br>Tipo Error:".mysql_error());
					$nom_tipoEmpresa = mysql_fetch_array($res_tipoEmpresa);
					$nom_tipoEmpresa = $nom_tipoEmpresa['Temdes'];
				}

				// --> Obtener nombre de la tarifa
				if($info_politica['Poltar'] == '*')
					$nom_tar = 'TODOS';
				else
				{
					$q_tarifa  = "SELECT Tardes
									FROM ".$wbasedato."_000025
								   WHERE Tarcod = '".$info_politica['Poltar']."' ";
					$res_tarifa = mysql_query($q_tarifa,$conex) or die("Error en el query: ".$q_tarifa."<br>Tipo Error:".mysql_error());
					$nom_tar = mysql_fetch_array($res_tarifa);
					$nom_tar = $nom_tar['Tardes'];
				}

				// --> Obtener nombre del nit de la entidad
				if($info_politica['Polnen'] == '*')
					$nom_Nitent = 'TODOS';
				else
				{
					$q_nitEntidad = "SELECT Nitnom
									   FROM ".$wbasedato."_000189
									  WHERE Nitnit = '".$info_politica['Polnen']."' ";
					$res_nitEntidad = mysql_query($q_nitEntidad,$conex) or die("Error en el query: ".$q_nitEntidad."<br>Tipo Error:".mysql_error());
					$nom_Nitent = mysql_fetch_array($res_nitEntidad);
					$nom_Nitent = $nom_Nitent['Nitnom'];
				}

				// --> Obtener nombre de la entidad
				if($info_politica['Polcen'] == '*')
					$nom_ent = 'TODOS';
				else
				{
					$q_entidad = "SELECT Empnom
									FROM ".$wbasedato."_000024
								   WHERE Empcod = '".$info_politica['Polcen']."' ";
					$res_entidad = mysql_query($q_entidad,$conex) or die("Error en el query: ".$q_entidad."<br>Tipo Error:".mysql_error());
					$nom_ent = mysql_fetch_array($res_entidad);
					$nom_ent = $nom_ent['Empnom'];
				}

				// --> Obtener restricciones de dias.
				if($info_politica['Polrdi'] == 'on')
				{
					$arr_res_dias 	= array();
					$q_res_dias = " SELECT Rdinum, Rdicco, Rdicpr, Rdidin, Rdidfi, id
									  FROM ".$wbasedato."_000156
									 WHERE Rdicpo = '".$info_politica['Polcod']."'
									   AND Rdiest = 'on'
								  ORDER BY id
								";
					$r_res_dias = mysql_query($q_res_dias,$conex) or die("Error en el query: ".$q_res_dias."<br>Tipo Error:".mysql_error());
					while($row_res_dias	= mysql_fetch_array($r_res_dias))
					{
						$arr_res_dias[$row_res_dias['id']]['Rdinum'] = $row_res_dias['Rdinum'];
						$arr_res_dias[$row_res_dias['id']]['Rdicco'] = $row_res_dias['Rdicco'];
						$arr_res_dias[$row_res_dias['id']]['Rdicpr'] = $row_res_dias['Rdicpr'];
						$arr_res_dias[$row_res_dias['id']]['Rdidin'] = $row_res_dias['Rdidin'];
						$arr_res_dias[$row_res_dias['id']]['Rdidfi'] = $row_res_dias['Rdidfi'];
					}
				}
				// --> Obtener restricciones de cantidades.
				if($info_politica['Polrrh'] == 'on')
				{
					$arr_res_hora 	= array();
					$q_res_hora = " SELECT Rrhcco, Rrhcpr, Rrhhin, Rrhhfi, id
									  FROM ".$wbasedato."_000158
									 WHERE Rrhcpo = '".$info_politica['Polcod']."'
									   AND Rrhest = 'on'
								  ORDER BY id
								";
					$r_res_hora = mysql_query($q_res_hora,$conex) or die("Error en el query: ".$q_res_hora."<br>Tipo Error:".mysql_error());
					while($row_res_hora	= mysql_fetch_array($r_res_hora))
					{
						$arr_res_hora[$row_res_hora['id']]['Rrhcco'] = $row_res_hora['Rrhcco'];
						$arr_res_hora[$row_res_hora['id']]['Rrhcpr'] = $row_res_hora['Rrhcpr'];
						$arr_res_hora[$row_res_hora['id']]['Rrhhin'] = $row_res_hora['Rrhhin'];
						$arr_res_hora[$row_res_hora['id']]['Rrhhfi'] = $row_res_hora['Rrhhfi'];
					}
				}
				// --> Obtener restricciones de cancelaciones o anulaciones.
				if($info_politica['Polrca'] == 'on')
				{
					$arr_res_anul 	= array();
					$q_res_anul = " SELECT *
									  FROM ".$wbasedato."_000157
									 WHERE Rcacpo = '".$info_politica['Polcod']."'
									   AND Rcaest = 'on'
								  ORDER BY id
								";
					$r_res_anul = mysql_query($q_res_anul,$conex) or die("Error en el query: ".$q_res_anul."<br>Tipo Error:".mysql_error());
					if(mysql_num_rows($r_res_anul) > 0)
					{
						$row_res_anul				= mysql_fetch_array($r_res_anul);
						$row_res_anul['conEntrada'] = false;
						$row_res_anul['proEntrada'] = false;

						if($row_res_anul['Rcaeoc'] != '' || $row_res_anul['Rcacca'] != '' || $row_res_anul['Rcacpa'] != '')
						{
							// --> 	Si el concepto-procedimiento a grabar son igual a vacio, significa que se va a grabar
							//		el concepto-procedimiento de entrada de la politica (El de los datos basicos)
							if($row_res_anul['Rcaccc'] == '' && $row_res_anul['Rcacpc'] == '')
							{
								$row_res_anul['Rcaccc'] = $info_politica['Polccp'];
								$row_res_anul['Rcacpc'] = $info_politica['Polpro'];
							}

							if($row_res_anul['Rcaccc'] == $info_politica['Polccp'])
								$row_res_anul['conEntrada'] = true;

							if($row_res_anul['Rcacpc'] == $info_politica['Polpro'])
								$row_res_anul['proEntrada'] = true;
						}
					}
					else
					{
						$row_res_anul['id'] 		= 'nuevo';
						$row_res_anul['Rcaeoc'] 	= '';
						$row_res_anul['Rcacca'] 	= '';
						$row_res_anul['Rcacpa'] 	= '';
						$row_res_anul['Rcacco'] 	= '';
						$row_res_anul['Rcafcg'] 	= '';
						$row_res_anul['Rcaccc'] 	= '';
						$row_res_anul['Rcacpc'] 	= '';
						$row_res_anul['Rcafcn'] 	= '';
						$row_res_anul['Rcamay'] 	= '';
						$row_res_anul['conEntrada'] = false;
						$row_res_anul['proEntrada'] = false;
						$row_res_anul['Rcasig'] 	= '';
						$row_res_anul['Rcasfg'] 	= '';
						$row_res_anul['Rcafsg'] 	= '';
						$row_res_anul['Rcacsg'] 	= '';

					}
				}

				// --> obtener las retricciones estancia
				if($info_politica['Polpen'] == 'on')
				{
					$arr_res_pen = array();
					$q_res_pen = " SELECT ppedii,ppedie,ppetfa,ppepro,ppecon,ppemin,ppetha,".$wbasedato."_000172 .id,ppehes,ppemcg
									 FROM ".$wbasedato."_000172
									WHERE ppecod = '".$info_politica['Polcod']."'
									  AND ppeest = 'on'
								";
					$r_res_pen = mysql_query($q_res_pen,$conex) or die("Error en el query: ".$q_res_pen."<br>Tipo Error:".mysql_error());
					while($row_res_pen	= mysql_fetch_array($r_res_pen))
					{
						$arr_res_pen['ppedii'] 	= $row_res_pen['ppedii'];
						$arr_res_pen['ppedie'] 	= $row_res_pen['ppedie'];
						$arr_res_pen['ppetfa'] 	= $row_res_pen['ppetfa'];
						$arr_res_pen['ppehes'] 	= $row_res_pen['ppehes'];
						$arr_res_pen['ppepro'] 	= $row_res_pen['ppepro'];
						$arr_res_pen['ppecon'] 	= $row_res_pen['ppecon'];
						$arr_res_pen['ppemin'] 	= $row_res_pen['ppemin'];
						$arr_res_pen['ppetha']  = $row_res_pen['ppetha'];
						$arr_res_pen['ppemcg']  = $row_res_pen['ppemcg'];
						$arr_res_pen['id'] 		= $row_res_pen['id'];
					}
				}

				// --> Obtener las restricciones quirurgicas
				$SQLresQui = "SELECT *
								FROM ".$wbasedato."_000226
							   WHERE Pcicod = '".$info_politica['Polcod']."'
							";
				$RESresQui = mysql_query($SQLresQui, $conex) or die("<b>ERROR EN QUERY MATRIX: (SQLresQui)</b><br>".mysql_error());
				if($ROWresQui = mysql_fetch_array($RESresQui))
					$arrayResQui = $ROWresQui;

				// --> Obtener todos los procedimientos existentes
				$array_procedimientos = array();
				$array_procedimientos['*'] = 'TODOS';

				$q_procedim = "
						SELECT Procod, Pronom
						  FROM ".$wbasedato."_000103
						 WHERE Proest = 'on'
						 GROUP BY Procod
				";
				$r_procedim = mysql_query($q_procedim,$conex) or die("Error en el query: ".$q_procedim."<br>Tipo Error:".mysql_error());
				while($row_procedim	= mysql_fetch_array($r_procedim))
					$array_procedimientos[$row_procedim['Procod']] = utf8_encode($row_procedim['Pronom']); //MIGRA_2

				// --> Obtener un array de conceptos y saber si mueve o no inventario
				$arrayConceptosInv = array();
				$sqlConInv = "SELECT Grucod, Gruinv
								FROM ".$wbasedato."_000200
							   WHERE Gruest = 'on' ";
				$resConInv = mysql_query($sqlConInv, $conex) or die("ERROR EN QUERY MATRIX (sqlConInv): ".mysql_error());
				while($arrayConInv = mysql_fetch_array($resConInv))
				{
					$arrayConceptosInv[$arrayConInv['Grucod']] = (($arrayConInv['Gruinv'] == 'on') ? TRUE : FALSE );
				}

				// --> Obtener todos los articulos existentes
				$array_articulos		= array();
				$array_articulos['*']	= 'TODOS';
				$tablaArticulos 		= obtenerTablaArticulos();

				$q_articulo = " SELECT Artcod, ".$tablaArticulos->campoNom." as Nombre
								  FROM ".$tablaArticulos->tabla."
								 WHERE Artest = 'on'
								 ORDER BY Nombre
				";
				$r_articulo = mysql_query($q_articulo,$conex) or die("Error en el query: ".$q_articulo."<br>Tipo Error:".mysql_error());
				while($row_articulo	= mysql_fetch_array($r_articulo))
					$array_articulos[$row_articulo['Artcod']] = utf8_encode($row_articulo['Nombre']); //MIGRA_3

				// --> Obtener todos los conceptos existentes
				$array_conceptos 		= obtener_array_conceptos();

				// --> Obtener array de los tipos de habitaciones
				$arrayTiposHabitaciones = arrayTiposHabitaciones();
			}
		}
		else
			$row_res_anul['id'] = 'nuevo';	//MIGRA_1
		

		// --> Pintar formulario.
		echo "
		<table align='center' width='100%'>
			<tr>
				<td align='center'>
					<div align='right'><img src='../../images/medical/eliminar1.png' title='Cerrar' onclick='cerrarFormulario();' style='cursor:pointer;'></div>
					<br>
					<div id='accordionDatosBasicos'>
						<h3>Datos Basicos</h3>
						<div align='center' id='DatosBasicos'>
							<table>
								<tr>
									<td colspan='4' align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda1."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
									</td>
								</tr>
								<tr class='encabezadoTabla DatBas'>
									<td style='width:13%;'>Codigo:</td>
									<td style='width:12%;'>Estado:</td>
									<td style='width:25%;'>Nombre:</td>
									<td style='width:25%;'>Explicación:</td>
								</tr>
								<tr class='DatBas'>
									<td class='fila2 pad'>
										<input type='text' style='width:100px;' id='formCodigo' disabled='disabled' value='".((isset($info_politica)) ? $info_politica['Polcod'] : '')."'>
									</td>
									<td class='fila2 pad'>
										<img id='EstadoPolitica' style='cursor:pointer' width='35' height='35' ".((isset($info_politica) && $info_politica['Polest'] != 'on') ? "src='../../images/medical/sgc/powerOff.png' value='Inactiva' " : "src='../../images/medical/sgc/powerOn.png' value='Activa' ")." OnClick='cambiar_estado(\"".$CodigoPolitica."\");'>
									</td>
									<td class='fila2 pad'>
										<input type='text' class='anchoInput' id='formNombre' class='reset' msgError='Ingrese el nombre deseado' value='".((isset($info_politica)) ? $info_politica['Polnom'] : '')."' MsgOblig='el nombre'>
									</td>
									<td class='fila2 pad'>
										<textarea style='height:45px;width:300px;font-size:11px' id='formExplicacion'>".((isset($info_politica)) ? $info_politica['Polexp'] : '')."</textarea>
									</td>
								</tr>
								";
						if(isset($info_politica))
						{
							if($info_politica['Poltpa'] != 'on')
							{
								$check1 	= "CHECKED='CHECKED'";
								$check2 	= "";
								$MsgOblig	= "el concepto";
							}
							else
							{
								$check1 = "";
								$check2 = "CHECKED='CHECKED'";
								$MsgOblig	= "el paquete";
							}
						}
						else
						{
							$check1 = "CHECKED='CHECKED'";
							$check2 = "";
							$MsgOblig	= "el concepto";
						}
						if(isset($info_politica) && $info_politica['Polfac'] != '')
						{
							if($info_politica['Polfac'] == 'on')
							{
								$checkFac1 = "CHECKED='CHECKED'";
								$checkFac2 = "";
							}
							else
							{
								$checkFac1 = "";
								$checkFac2 = "CHECKED='CHECKED'";
							}
						}
						else
						{
							$checkFac1 = "CHECKED='CHECKED'";
							$checkFac2 = "";
						}
						if($arrayConceptosInv[$info_politica['Polccp']])
							$nom_procedimiento = $array_articulos[$info_politica['Polpro']];
						else
							$nom_procedimiento = $array_procedimientos[$info_politica['Polpro']];

						echo"	<tr class='encabezadoTabla DatBas'>
									<td align='center' colspan='2'>
										Concepto:
										<input type='radio' name='tipo_politica' id='formTipoConcepto' OnChange='CambioTipoConceptoOPaquete(this)' ".$check1.">&nbsp;&nbsp;&nbsp;&nbsp;
										<!--Paquete:  <input type='radio' name='tipo_politica' id='formTipoPaquete'  OnChange='CambioTipoConceptoOPaquete(this)'  ".$check2.">-->
									</td>
									<td>Procedimiento:</td>
									<td>Especialidad:</td>
								</tr>
								<tr class='DatBas'>
									<td class='fila2 pad' colspan='2'>
										<input type='text' class='anchoInput' id='formConceptoPaquete' class='reset' msgError='Seleccione...' ".((isset($info_politica)) ? "valor='".$info_politica['Polccp']."' value='".$info_politica['Polccp']."-".$nom_concep_o_paqu."' nombre='".$nom_concep_o_paqu."'" : '')." MsgOblig='".$MsgOblig."'>
									</td>
									<td class='fila2 pad'>
										<input type='text' class='anchoInput' style='display:none' id='formProcedimiento' class='reset' msgError='Digite el procedimiento' ".((isset($info_politica) && isset($nom_procedimiento)) ? "valor='".$info_politica['Polpro']."' value='".$info_politica['Polpro']."-".$nom_procedimiento."' nombre='".$nom_procedimiento."'" : "value='' nombre='' ")." MsgOblig='el procedimento'>
									</td>
									<td class='fila2 pad'>
										<input type='text' class='anchoInput' id='formEspecialidad' class='reset' msgError='Digite la especialidad' ".((isset($info_politica)) ? "valor='".$info_politica['Polesp']."' value='".$info_politica['Polesp']."-".$nom_esp."' nombre='".$nom_esp."'" : '')." MsgOblig='la especialidad'>
									</td>
								</tr>
								<tr class='encabezadoTabla DatBas'>
									<td colspan='2'>Tipo de empresa:</td>
									<td>Tarifa:</td>
									<td>Nit Entidad:</td>
								</tr>
								<tr>
									<td class='fila2 pad' colspan='2'>
										<input type='text' id='formTipoEmpresa' class='anchoInput' class='reset' msgError='Nombre del tipo de empresa' ".((isset($info_politica)) ? "value='".$info_politica['Poltem']."-".$nom_tipoEmpresa."' nombre='".$nom_tipoEmpresa."' valor='".$info_politica['Poltem']."'" : '')." MsgOblig='el tipo de empresa'>
									</td>
									<td class='fila2 pad'>
										<input type='text' id='formTarifa' class='anchoInput' class='reset' msgError='Nombre de la tarifa' ".((isset($info_politica)) ? "value='".$info_politica['Poltar']."-".$nom_tar."' nombre='".$nom_tar."' valor='".$info_politica['Poltar']."'" : '')." MsgOblig='la tarifa'>
									</td>
									<td class='fila2 pad'>
										<input type='text' class='anchoInput' id='formNitEntidad' class='reset' msgError='Nombre o nit de la entidad' ".((isset($info_politica)) ? "valor='".$info_politica['Polnen']."' value='".$info_politica['Polnen']."-".$nom_Nitent."' nombre='".$nom_Nitent."'" : '')." MsgOblig='la entidad'>
									</td>
								</tr>
								<tr class='DatBas encabezadoTabla'>
									<td colspan='2'>Convenio Entidad:</td>
									<td>Centro de costos que origina el cargo:</td>
									<td>Centro de costos actual del paciente:</td>
								</tr>
								<tr>
									<td class='fila2 pad' colspan='2'>
										<input type='text' id='formEntidad' class='anchoInput' class='reset' msgError='Nombre del convenio' ".((isset($info_politica)) ? "value='".$info_politica['Polcen']."-".$nom_ent."' nombre='".$nom_ent."' valor='".$info_politica['Polcen']."'" : '')." MsgOblig='el convenio'>
									</td>
									<td class='fila2 pad'>
										<input type='text' class='anchoInput' id='formCentroCostos' class='reset' msgError='Digite el centro de costos' ".((isset($info_politica)) ? "valor='".$info_politica['Polcco']."' value='".$info_politica['Polcco']."-".$nom_cco."' nombre='".$nom_cco."'" : "valor='' value='' nombre=''")." MsgOblig='el centro de costos'>
									</td>
									<td class='fila2 pad'>
										<input type='text' class='anchoInput' id='formCentroCostosPac' class='reset' msgError='Digite el centro de costos' ".((isset($info_politica)) ? "valor='".$info_politica['Polcca']."' value='".$info_politica['Polcca']."-".$nom_ccoAct."' nombre='".$nom_ccoAct."'" : " valor='' value='' nombre=''")." MsgOblig='el centro de costos'>
									</td>
								</tr>
								<tr class='DatBas'>
									<td class='encabezadoTabla' colspan='2'>Tipo de habitación:</td>
									<td></td>
									<td></td>
								</tr>
								<tr>
									<td class='fila2 pad' colspan='2'>
										<b>Agregar:</b>&nbsp;<input type='text' id='formTipHab' style='width:260px;' class='reset' msgError='Digite el tipo de habitación' valor='' value='' nombre='' MsgOblig='el tipo de habitación'>
										<table id='listaTiposHab' style='font-size: 7pt;text-align: left;padding-left:25px;' width='100%'>";
										if(isset($info_politica))
										{
											$arrayTiposHab = explode('-', $info_politica['Poltha']);
											foreach($arrayTiposHab as $valorTipoHab)
											{
												if(trim($valorTipoHab) != '')
												{
													echo "
													<tr codTipoHab='".$valorTipoHab."'>
														<td width='80%'><b>-</b>&nbsp;&nbsp;".$valorTipoHab."-".$arrayTiposHabitaciones[$valorTipoHab]."</td>
														<td><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td></td>
													</tr>";
												}
											}
										}
						echo"			</table>
									</td>
									<td coldspan='2'></td>
								</tr>
							</table>
						</div>
					</div>
					<br>
					<div id='accordionResDia'>
						<h3 align='left'>Restricción de días</h3>
						<div align='center'>
							<table id='ResDia'>
								<tr>
									<td colspan='5' align='right'><img style='cursor:help' class='tooltip' title='".$MsjAyuda2."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />&nbsp;	</td>
								</tr>
								<tr class='encabezadoTabla ResDia' align='center'>
									<td style='width:20%;' 	rowspan='2'><b>Número de veces al día que se cobra:</b></td>
									<td style='width:59%;' 	colspan='2'><b>Que se debe cobrar:</b></td>
									<td style='width:20%;' 	colspan='2'><b>Estancia:</b></td>
								</tr>
								<tr class='fila1 ResDia' align='center' style='font-weight:bold'>
									<td style='width:30%;'>Concepto:</td>
									<td style='width:29%;'>Procedimiento:</td>
									<td style='width:10%;'>Día inicio:</td>
									<td style='width:10%;'>Día fin:</td>
								</tr>";

						if(!isset($arr_res_dias))
						{
							$arr_res_dias['nuevo']['Rdinum'] = '';
							$arr_res_dias['nuevo']['Rdicco'] = '';
							$arr_res_dias['nuevo']['Rdicpr'] = '';
							$arr_res_dias['nuevo']['Rdidin'] = '';
							$arr_res_dias['nuevo']['Rdidfi'] = '';
						}
						$consec = 1;
						foreach($arr_res_dias as $id_clave => $valores)
						{
							// --> si es un concepto de inventario
							if($arrayConceptosInv[$valores['Rdicco']])
								$nomPro1 = $array_articulos[$valores['Rdicpr']];
							else
								$nomPro1 = $array_procedimientos[$valores['Rdicpr']];

							echo"
								<tr class='ResDia' id='fila".$consec."' align='center' align='center'>
									<td class='fila2 pad'>
										<input type='text' value='".$valores['Rdinum']."' style='width:110px;' prefijo='formNumCobra' id='formNumCobra".$consec."' class='reset entero' msgError='Digite el número' CamposActiva='formConCobra".$consec."-formProCobra".$consec."-formDiaInicio".$consec."-formDiaFin".$consec."' OnBlur='ActivarCampos(this);'>
									</td>
									<td class='fila2 pad'>
										<input type='text' ".(($valores['Rdicco'] != '') ? "valor='".$valores['Rdicco']."' value='".$valores['Rdicco']."-".$array_conceptos[$valores['Rdicco']]."' 		nombre='".$array_conceptos[$valores['Rdicco']]."' " 		: " valor=''" )." style='width:300px;".(($valores['Rdicco'] == '') ? "display:none" : "")."' prefijo='formConCobra' id='formConCobra".$consec."' class='reset 1VezOculto limpiar' msgError='Digite el concepto' MsgOblig='el concepto que se cobra' CargarAutocomplete='formProCobra'>
									</td>
									<td class='fila2 pad'>
										<input type='text' ".(($valores['Rdicpr'] != '') ? "valor='".$valores['Rdicpr']."' value='".$valores['Rdicpr']."-".$nomPro1."' nombre='".$nomPro1."' " 	: " valor=''" )." style='width:300px;".(($valores['Rdicpr'] == '') ? "display:none" : "")."' prefijo='formProCobra' id='formProCobra".$consec."' class='reset 1VezOculto limpiar' msgError='Digite el procedimiento' MsgOblig='el procedimiento que se cobra' >
									</td>
									<td class='fila2 pad'>
										<input type='text' value='".$valores['Rdidin']."' style='width:110px;".(($valores['Rdidin'] == '') ? "display:none" : "")."' prefijo='formDiaInicio' id='formDiaInicio".$consec."' class='reset entero 1VezOculto' msgError='Digite día inicio' MsgOblig='el dia inicio'>
									</td>
									<td class='fila2 pad'>
										<input type='text' value='".$valores['Rdidfi']."' style='width:110px;".(($valores['Rdidfi'] == '') ? "display:none" : "")."' prefijo='formDiaFin' id='formDiaFin".$consec."' class='reset entero 1VezOculto' msgError='Digite el día fin' MsgOblig='el dia fin'>
										<input type='hidden' value='".$id_clave."' prefijo='formIdResDia' id='formIdResDia".$consec."' >
									</td>
								</tr>";
							$consec++;
						}
						echo"
							</table>
						</div>
					</div>
					<div id='accordionResHor'>
						<h3 align='left'>Restricción de cantidades</h3>
						<div align='center'>
							<table id='ResHor'>
								<tr>
									<td colspan='5' align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda3."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
									</td>
								</tr>
								<tr class='ResHor'>
									<td colspan='7' align='right' style='font-size:9pt;font-weight:bold;'>
										<span  OnClick='AgregarFilaCampos(\"ResHor\");' style='cursor:pointer'>Agregar <img src='../../images/medical/HCE/mas.PNG'></span>
									</td>
									<tr class='ResHor' align='center'>
										<td class='encabezadoTabla' style='width:60%;' 	colspan='2'><b>Que se debe cobrar:</b></td>
										<td class='encabezadoTabla' style='width:39%;' 	colspan='2'><b>Rango en el que aplica:</b></td>
										<td style='width:1%;' 	rowspan='2'></td>
									</tr>
									<tr class='fila1 ResHor' align='center' style='font-weight:bold'>
										<td style='width:30%;'>Concepto</td>
										<td style='width:30%;'>Procedimiento</td>
										<td style='width:20%;'>Mínimo</td>
										<td style='width:19%;'>Máximo</td>
									</tr>
								</tr>";

						if(!isset($arr_res_hora))
						{
							$arr_res_hora['nuevo']['Rrhcco'] = '';
							$arr_res_hora['nuevo']['Rrhcpr'] = '';
							$arr_res_hora['nuevo']['Rrhhin'] = '';
							$arr_res_hora['nuevo']['Rrhhfi'] = '';
						}
						$consec = 1;
						foreach($arr_res_hora as $id_clave => $valores)
						{
							// --> si es un concepto de inventario
							if($arrayConceptosInv[$valores['Rrhcco']])
								$nomPro2 = $array_articulos[$valores['Rrhcpr']];
							else
								$nomPro2 = $array_procedimientos[$valores['Rrhcpr']];

							echo"
								<tr class='ResHor' id='fila".$consec."' align='center'>
									<td class='fila2 pad'>
										<input type='text' ".(($valores['Rrhcco'] != '') ? "valor='".$valores['Rrhcco']."' value='".$valores['Rrhcco']."-".$array_conceptos[$valores['Rrhcco']]."' 		nombre='".$array_conceptos[$valores['Rrhcco']]."' " : "" )." style='width:300px;' prefijo='formConCobraHoras' id='formConCobraHoras".$consec."' class='reset limpiar' msgError='Digite el código' CamposActiva='formProCobraHoras".$consec."' OnBlur='ActivarCampos(this);' CargarAutocomplete='formProCobraHoras' MsgOblig='concepto'>
									</td>
									<td class='fila2 pad'>
										<input type='text' ".(($valores['Rrhcpr'] != '') ? "valor='".$valores['Rrhcpr']."' value='".$valores['Rrhcpr']."-".$nomPro2."' nombre='".$nomPro2."' " : "" )." style='width:300px;".(($valores['Rrhcpr']=='') ? "display:none" : "")."' prefijo='formProCobraHoras' id='formProCobraHoras".$consec."' class='reset limpiar' msgError='Digite el código' MsgOblig='procedimiento'>
									</td>
									<td class='fila2 pad'>
										<input type='text' value='".$valores['Rrhhin']."' style='width:110px;' prefijo='formHoraInicio' id='formHoraInicio".$consec."' class='reset entero ' msgError='Cantidad minima' MsgOblig='la hora de inicio'>
									</td>
									<td class='fila2 pad'>
										<input type='text' value='".$valores['Rrhhfi']."' style='width:110px;' prefijo='formHoraFin' id='formHoraFin".$consec."' class='reset entero ' msgError='Cantidad maxima' MsgOblig='la hora fin'>
									</td>
									<td class='fila2 pad' align='center'>
										<input type='hidden' value='".$id_clave."' prefijo='formIdResHor' id='formIdResHor".$consec."' >
										<img style='cursor:pointer' OnClick='EliminarTr(this, \"ResHor\", \"formIdResHor\");' src='../../images/medical/hce/cancel.PNG'>
									</td>
								</tr>";
							$consec++;
						}
						echo"
							</table>
						</div>
					</div>
					<div id='accordionResAnu' align='center'>
						<h3 align='left'>No facturables</h3>
						<div align='center' id='DivResAnu'>

							<table width='100%'>
								<tr>
									<td align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda4."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
									</td>
								</tr>
							</table>

							<table width='100%' align='center'>
								<tr>
									<td align='center' class='fila1 pad' style='font-weight:bold;' width='69%'>
										Por cargos ya grabados
									</td>
									<td width='1%'></td>
									<td align='center' class='fila1 pad' style='font-weight:bold;' width='30%'>
										Por semanas de gestación
									</td>
								</tr>
								<tr>";

							$msjCheck1 = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
								Traer concepto de entrada
								&nbsp;
							</span>";
							$msjCheck2 = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
								Traer procedimiento de entrada
								&nbsp;
							</span>";


							$check_may1 	= "CHECKED='CHECKED'";
							$check_may2 	= "";
							if($valores['Rcamay'] != '')
							{
								if($valores['Rcamay'] == 'on')
								{
									$check_may1 = "CHECKED='CHECKED'";
									$check_may2 = "";
								}
								else
								{
									$check_may1 = "";
									$check_may2 = "CHECKED='CHECKED'";
								}
							}

							if($arrayConceptosInv[$row_res_anul['Rcaccc']])
								$nomPro4 = $array_articulos[$row_res_anul['Rcacpc']];
							else
								$nomPro4 = $array_procedimientos[$row_res_anul['Rcacpc']];

							echo	"<td class='fila2 pad' style='font-size:9pt' align='center'>
										<input type='hidden' value='".$row_res_anul['id']."' id='formIdResAnu' >
										Si ya hay grabado
										<input type='text' size='2' placeholder='&nbsp;' value='".$row_res_anul['Rcaeoc']."' id='formEventOcurridos' class='entero'>
										cargo(s) de:<br><br>
										<div align='center'>
											<table id='listaCargosAnula' class='Bordegris' style='font-size: 7pt;text-align: center;' width='80%'>
												<tr class='encabezadoTabla' style='font-size:8pt' encabezado='si'>
													<td>Concepto</td>
													<td>Procedimiento</td>
													<td>Cco</td>
													<td></td>
												</tr>
												<tr style='font-size:8pt' encabezado='si'>
													<td><input type='text' id='formCodConAnula' style='font-size:8pt;' placeholder='Digite el código o nombre' size='30' valor=''></td>
													<td><input type='text' id='formCodProAnula' style='font-size:8pt;' placeholder='Digite el código o nombre' size='30' valor=''></td>
													<td><input type='text' id='formCodCcoAnula' style='font-size:8pt;' placeholder='Digite el código o nombre' size='25' valor=''></td>
													<td><input type='button' style='font-size:8pt;cursor:pointer' value='Agregar' onClick='agregarCargosAnula()'/></td>
												</tr>";
												
										if($row_res_anul['Rcacca'] != "" &&  $row_res_anul['Rcacpa'] != "" &&  $row_res_anul['Rcacco'] != "")
										{
											$arrConAnu = json_decode($row_res_anul['Rcacca'], true);
											$arrProAnu = json_decode($row_res_anul['Rcacpa'], true);
											$arrCcoAnu = json_decode($row_res_anul['Rcacco'], true);
											
											foreach($arrConAnu as $idxAnu => $codConAnu)
											{
												if($arrayConceptosInv[$codConAnu])
													$nomProAnu = $array_articulos[$arrProAnu[$idxAnu]];
												else
													$nomProAnu = $array_procedimientos[$arrProAnu[$idxAnu]];
												
												$nomCcoAnu = Obtener_array_cco($arrCcoAnu[$idxAnu]);
												$nomCcoAnu = $nomCcoAnu[$arrCcoAnu[$idxAnu]];
							
												echo "
												<tr codConAn='".$codConAnu."' codProAn='".$arrProAnu[$idxAnu]."' codCcoAn='".$arrCcoAnu[$idxAnu]."' encabezado='no'>
													<td align='left'>&nbsp;".$codConAnu."-".$array_conceptos[$codConAnu]."</td>
													<td align='left'>&nbsp;".$arrProAnu[$idxAnu]."-".$nomProAnu."</td>
													<td align='left'>&nbsp;".$arrCcoAnu[$idxAnu]."-".$nomCcoAnu."</td>
													<td align='center' width='2%'><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>
													</td>
												</tr>";
											}
										}	
									
							echo "			</table>
										</div>
										<br>
										Colocarlo(s) como
										<select id='formFacturableCargoGrabado'>
											<option ".(($row_res_anul['Rcafcg'] == 'S') ? "selected='selected'" : '')." value='S'	>SI</option>
											<option ".(($row_res_anul['Rcafcg'] != 'S') ? "selected='selected'" : '')." value='N' >NO</option>
										</select> facturable, y realizar la grabación de:<br>
										<b>Concepto:</b>
										<input type='checkbox' class='tooltip' title='".$msjCheck1."' ".(($row_res_anul['conEntrada']) ? "checked='CHECKED' " : '')." onChange='traerConProEntrada(this, \"formConCobraSiAnula\", \"formConceptoPaquete\");'>
										<input type='text' id='formConCobraSiAnula' style='font-size:8pt;' placeholder='Digite el código o nombre' size='28' ".(($row_res_anul['Rcaccc'] != '') ? "valor='".$row_res_anul['Rcaccc']."' value='".$row_res_anul['Rcaccc']."-".$array_conceptos[$row_res_anul['Rcaccc']]."' nombre='".$array_conceptos[$row_res_anul['Rcaccc']]."' " : "" )." 	".(($row_res_anul['conEntrada']) ? "disabled='disabled' " : '').">&nbsp;
										<b>Procedimiento:</b>
										<input type='checkbox' class='tooltip' title='".$msjCheck2."' ".(($row_res_anul['proEntrada']) ? "checked='CHECKED' " : '')." onChange='traerConProEntrada(this, \"formProCobraSiAnula\", \"formProcedimiento\");'>
										<input type='text' id='formProCobraSiAnula' style='font-size:8pt;' placeholder='Digite el código o nombre' size='28' ".(($row_res_anul['Rcacpc'] != '') ? "valor='".$row_res_anul['Rcacpc']."' value='".$row_res_anul['Rcacpc']."-".$nomPro4."' nombre='".$nomPro4."' " : "" )." 	".(($row_res_anul['proEntrada']) ? "disabled='disabled' " : '').">
										<br>como
										<select id='formFacturableCargoNuevo'>
											<option ".(($row_res_anul['Rcafcn'] != 'N') ? "selected='selected'" : '')." value='S'	>SI</option>
											<option ".(($row_res_anul['Rcafcn'] == 'N') ? "selected='selected'" : '')." value='N' 	>NO</option>
										</select> facturable.<br>
										¿Validar que el valor del cargo a grabar, sea mayor al del cargo ya grabado?
										<select id='formValidarSiEsMayor'>
											<option value='on'	".(($row_res_anul['Rcamay'] != 'off') ? 'SELECTED' : '' ).">SI</option>
											<option value='off' ".(($row_res_anul['Rcamay'] == 'off') ? 'SELECTED' : '' ).">NO</option>
										</select>
									</td>
									<td></td>
									<td class='fila2 pad' style='font-size:9pt'>
										Entre la semana inicial
										<input type='text' size='2' id='formSemIniGestacion' placeholder='&nbsp;' value='".$row_res_anul['Rcasig']."' class='entero'>
										y la semana final
										<input type='text' size='2' id='formSemFinGestacion' placeholder='&nbsp;' value='".$row_res_anul['Rcasfg']."' class='entero'>
										de gestación, el cargo es <b>no facturable</b>.
										<br><br>
										(&nbsp;&nbsp;<b>Origen del dato HCE:</b>
										Formulario	<input type='text' size='7' placeholder='&nbsp;' id='formFormularioHce'	value='".$row_res_anul['Rcafsg']."'>
										Campo		<input type='text' size='6' placeholder='&nbsp;' id='formCampoHce'		value='".$row_res_anul['Rcacsg']."'>&nbsp;&nbsp;)
									</td>
								</tr>
							</table><br>";

						/*echo"
							<table id='ResAnu'>
								<tr class='ResAnu'>
									<td colspan='7' align='right' style='font-size:9pt;font-weight:bold;'>
										<span style='cursor:pointer' OnClick='AgregarFilaCampos(\"ResAnu\");'>Agregar <img src='../../images/medical/HCE/mas.PNG'></span>
									</td>
								</tr>
								<tr class='ResAnu' style='font-weight:bold;' align='center'>
									<td style='width:13%;' rowspan='2' class='encabezadoTabla'>Eventos ocurridos para que aplique:</td>
									<td style='width:38%;' colspan='2' class='encabezadoTabla'>Cambiar a NO facturable:</td>
									<td style='width:38%;' colspan='2' class='encabezadoTabla'>Que se debe cobrar:</td>
									<td style='width:10%;' rowspan='2' class='encabezadoTabla'>Validar si valor es mayor:</td>
									<td style='width:1%;'  rowspan='2'></td>
								</tr>
								<tr class='fila1 ResAnu' align='center' style='font-weight:bold'>
									<td style='width:19%;'>Concepto</td>
									<td style='width:19%;'>Procedimiento</td>
									<td style='width:19%;'>Concepto</td>
									<td style='width:19%;'>Procedimiento</td>
								</tr>
								";
						if(!isset($arr_res_anul))
						{
							$arr_res_anul['nuevo']['Rcaeoc'] = '';
							$arr_res_anul['nuevo']['Rcacca'] = '';
							$arr_res_anul['nuevo']['Rcacpa'] = '';
							$arr_res_anul['nuevo']['Rcaccc'] = '';
							$arr_res_anul['nuevo']['Rcacpc'] = '';
							$arr_res_anul['nuevo']['Rcamay'] = '';
						}
						$consec = 1;
						foreach($arr_res_anul as $id_clave => $valores)
						{
							echo"
								<tr class='ResAnu' id='fila".$consec."'>
									<td class='fila2 pad'>
										<input type='text' value='".$valores['Rcaeoc']."' style='width:110px;' prefijo='formEventOcurridos' id='formEventOcurridos".$consec."' class='reset entero' msgError='Digite el número' CamposActiva='formCodConAnula".$consec."-formCodProAnula".$consec."-formConCobraSiAnula".$consec."-formProCobraSiAnula".$consec."-formValidarSiEsMayor".$consec."-formValidarNoEsMayor".$consec."' OnBlur='ActivarCampos(this);'>
									</td>
									<td class='fila2 pad'>
										<input type='text' CargarAutocomplete='formCodProAnula' ".(($valores['Rcacca'] != '') ? "valor='".$valores['Rcacca']."' value='".$valores['Rcacca']."-".$array_conceptos[$valores['Rcacca']]."' nombre='".$array_conceptos[$valores['Rcacca']]."' " : "" )." style='width:210px;".(($valores['Rcacca'] == '') ? "display:none" : "")."' prefijo='formCodConAnula' id='formCodConAnula".$consec."' class='reset 1VezOculto limpiar' msgError='Digite el código' MsgOblig='el codigo que anula'>
									</td>
									<td class='fila2 pad'>
										<input type='text' ".(($valores['Rcacpa'] != '') ? "valor='".$valores['Rcacpa']."' value='".$valores['Rcacpa']."-".$array_procedimientos[$valores['Rcacpa']]."' nombre='".$array_procedimientos[$valores['Rcacpa']]."' " : "" )." style='width:210px;".(($valores['Rcacpa'] == '') ? "display:none" : "")."' prefijo='formCodProAnula' id='formCodProAnula".$consec."' class='reset 1VezOculto limpiar' msgError='Digite el código' MsgOblig='el codigo que anula'>
									</td>
									<td class='fila2 pad'>
										<input type='text'  CargarAutocomplete='formProCobraSiAnula' ".(($valores['Rcaccc'] != '') ? "valor='".$valores['Rcaccc']."' value='".$valores['Rcaccc']."-".$array_conceptos[$valores['Rcaccc']]."' nombre='".$array_conceptos[$valores['Rcaccc']]."' " : "" )." style='width:210px;".(($valores['Rcaccc'] == '') ? "display:none" : "")."' prefijo='formConCobraSiAnula' id='formConCobraSiAnula".$consec."' class='limpiar reset 1VezOculto' msgError='Digite el código' MsgOblig='el codigo que cobra, si anula'>
									</td>
									<td class='fila2 pad'>
										<input type='text' ".(($valores['Rcacpc'] != '') ? "valor='".$valores['Rcacpc']."' value='".$valores['Rcacpc']."-".$array_procedimientos[$valores['Rcacpc']]."' nombre='".$array_procedimientos[$valores['Rcacpc']]."' " : "" )." style='width:210px;".(($valores['Rcacpc'] == '') ? "display:none" : "")."' prefijo='formProCobraSiAnula' id='formProCobraSiAnula".$consec."' class='limpiar reset 1VezOculto' msgError='Digite el código' MsgOblig='el codigo que cobra, si anula'>
									</td>";
							if($valores['Rcamay'] != '')
							{
								$display_may = '';
								if($valores['Rcamay'] == 'on')
								{
									$check_may1 = "CHECKED='CHECKED'";
									$check_may2 = "";
								}
								else
								{
									$check_may1 = "";
									$check_may2 = "CHECKED='CHECKED'";
								}
							}
							else
							{
								$display_may 	= 'display:none';
								$check_may1 	= "CHECKED='CHECKED'";
								$check_may2 	= "";
							}
							echo"	<td class='fila2 pad' align='center'>
										Si<input type='radio' name='tipo_politica".$consec."' style='".$display_may."' prefijo='formValidarSiEsMayor' id='formValidarSiEsMayor".$consec."' class='1VezOculto' ".$check_may1.">
										No<input type='radio' name='tipo_politica".$consec."' style='".$display_may."' prefijo='formValidarNoEsMayor' id='formValidarNoEsMayor".$consec."' class='1VezOculto' ".$check_may2.">
									</td>
									<td class='fila2 pad' align='center'>
										<input type='hidden' value='".$id_clave."' prefijo='formIdResAnu' id='formIdResAnu".$consec."' >
										<img OnClick='EliminarTr(this, \"ResAnu\", \"formIdResAnu\");' style='cursor:pointer' src='../../images/medical/hce/cancel.PNG'>
									</td>
								</tr>";
							$consec++;
						}
						echo"
							</table>";*/

						echo"
						</div>
					</div>
					<div id='accordionRecargos'>
						<h3 align='left'>Recargos</h3>
					";

					if($info_politica['Polarn'] != '')
						$tipoPorcentajen = true;
					else
						$tipoPorcentajen = false;

					if($info_politica['Polarf'] != '')
						$tipoPorcentajef = true;
					else
						$tipoPorcentajef = false;

					if($info_politica['Polard'] != '')
						$tipoPorcentajed = true;
					else
						$tipoPorcentajed = false;
					
					// --> Obtener horario nocturno por defecto
					$q_IniHoraNoct = "SELECT Detval
										FROM root_000051
									   WHERE Detemp = '".$wemp_pmla."'
										 AND Detapl = 'InicioHorarioNocturno'
									";
					$r_IniHoraNoct = mysql_query($q_IniHoraNoct, $conex) or die("Error en el query: ".$q_IniHoraNoct."<br>Tipo Error:".mysql_error());

					$q_FinHoraNoct = "SELECT Detval
										FROM root_000051
									   WHERE Detemp = '".$wemp_pmla."'
										 AND Detapl = 'FinHorarioNocturno'
									";
					$r_FinHoraNoct = mysql_query($q_FinHoraNoct, $conex) or die("Error en el query: ".$q_FinHoraNoct."<br>Tipo Error:".mysql_error());
					if(mysql_num_rows($r_IniHoraNoct) > 0 && mysql_num_rows($r_FinHoraNoct) > 0)
					{
						$InicioHorarioNocturno 	= mysql_fetch_array($r_IniHoraNoct);
						$InicioHorarioNocturno 	= explode(':', $InicioHorarioNocturno['Detval']);
						$InicioHorarioNocturno 	= $InicioHorarioNocturno[0].":".$InicioHorarioNocturno[1];
						$FinHorarioNocturno 	= mysql_fetch_array($r_FinHoraNoct);
						$FinHorarioNocturno 	= explode(':', $FinHorarioNocturno['Detval']);
						$FinHorarioNocturno 	= $FinHorarioNocturno[0].":".$FinHorarioNocturno[1];
					}

					// --> Recargos
					echo"
						<div align='center' id='DivRecargos'>
							<table id='DivRecargos' width='100%'>
								<tr>
									<td colspan='4' align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda5."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
									</td>
								</tr>";
								// --> Recargo nocturno y festivo
						echo"	<tr class='encabezadoTabla OtrasRes' align='center'>
									<td style='width:50%;' colspan='2'>
										Recargo nocturno:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										% <input type='radio' name='TipoRecargoNocturno' id='RecarNocPorcentaje' ".(($tipoPorcentajen) ? 'checked=checked': '')." OnClick='$(\".TipoRecNoctPorcentaje\").show();$(\".TipoRecNoctOtro\").hide();'>&nbsp;&nbsp;
										otro <input type='radio' name='TipoRecargoNocturno' id='RecarNocOtro'    ".(($tipoPorcentajen) ? '': 'checked=checked')." OnClick='$(\".TipoRecNoctPorcentaje\").hide();$(\".TipoRecNoctOtro\").show();'>
									</td>
									<td style='width:50%;' colspan='2'>
										Recargo festivo:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										% <input type='radio' name='TipoRecargoFestivo' id='RecarFesPorcentaje' ".(($tipoPorcentajef) ? 'checked=checked': '')." OnClick='$(\".TipoRecFesPorcentaje\").show();$(\".TipoRecFesOtro\").hide();'>&nbsp;&nbsp;
										otro <input type='radio' name='TipoRecargoFestivo' id='RecarFesOtro'    ".(($tipoPorcentajef) ? '': 'checked=checked')." OnClick='$(\".TipoRecFesPorcentaje\").hide();$(\".TipoRecFesOtro\").show();'>
									</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td style='width:25%;".(($tipoPorcentajen) ? 'display:none;': '')."' class='TipoRecNoctOtro fila1'>Concepto:</td>
									<td style='width:25%;".(($tipoPorcentajen) ? 'display:none;': '')."' class='TipoRecNoctOtro fila1'>Procedimiento:</td>
									<td style='width:50%;".(($tipoPorcentajen) ? '': 'display:none;')."background-color: #FFFFFF;' class='TipoRecNoctPorcentaje' colspan='2'></td>
									<td style='width:25%;".(($tipoPorcentajef) ? 'display:none;': '')."'' class='TipoRecFesOtro fila1'>Concepto:</td>
									<td style='width:25%;".(($tipoPorcentajef) ? 'display:none;': '')."'' class='TipoRecFesOtro fila1'>Procedimiento:</td>
									<td style='width:50%;".(($tipoPorcentajef) ? '': 'display:none;')."background-color: #FFFFFF;' class='TipoRecFesPorcentaje' colspan='2'></td>
								</tr>
								<tr class='OtrasRes'>
									<td class='fila2 pad TipoRecNoctOtro' ".(($tipoPorcentajen) ? 'style="display:none;"': '').">
										<input type='text' style='width:230px;' id='formCodConRecNoc' class='reset limpiar' msgError='Digite el código' ".(($info_politica['Polcrn'] != '') ? "valor='".$info_politica['Polcrn']."' value='".$info_politica['Polcrn']."-".$array_conceptos[$info_politica['Polcrn']]."' nombre='".$array_conceptos[$info_politica['Polcrn']]."' " : "" )." >
									</td>
									<td class='fila2 pad TipoRecNoctOtro' ".(($tipoPorcentajen) ? 'style="display:none;"': '').">
										<input type='text' style='width:230px;' id='formCodProRecNoc' class='reset limpiar' msgError='Digite el código' ".(($info_politica['Polprn'] != '') ? "valor='".$info_politica['Polprn']."' value='".$info_politica['Polprn']."-".$array_procedimientos[$info_politica['Polprn']]."' nombre='".$array_procedimientos[$info_politica['Polprn']]."' " : "" ).">
									</td>
									<td class='fila2 pad TipoRecNoctPorcentaje' colspan='2' ".(($tipoPorcentajen) ? '': 'style="display:none;"').">
										<input type='text' style='width:110px;' id='formPorcentajeRecNoc' class='reset entero' msgError='Digite el %' ".(($info_politica['Polarn'] != '') ? " value='".$info_politica['Polarn']."' " : "" ).">
									</td>
									<td class='fila2 pad TipoRecFesOtro' ".(($tipoPorcentajef) ? 'style="display:none;"': '').">
										<input type='text' style='width:230px;' id='formCodConRecFest' class='reset limpiar' msgError='Digite el código' ".(($info_politica['Polcrf'] != '') ? "valor='".$info_politica['Polcrf']."' value='".$info_politica['Polcrf']."-".$array_conceptos[$info_politica['Polcrf']]."' nombre='".$array_conceptos[$info_politica['Polcrf']]."' " : "" )." >
									</td>
									<td class='fila2 pad TipoRecFesOtro' ".(($tipoPorcentajef) ? 'style="display:none;"': '').">
										<input type='text' style='width:230px;' id='formCodProRecFest' class='reset limpiar' msgError='Digite el código' ".(($info_politica['Polprf'] != '') ? "valor='".$info_politica['Polprf']."' value='".$info_politica['Polprf']."-".$array_procedimientos[$info_politica['Polprf']]."' nombre='".$array_procedimientos[$info_politica['Polprf']]."' " : "" ).">
									</td>
									<td class='fila2 pad TipoRecFesPorcentaje' colspan='2' ".(($tipoPorcentajef) ? '': 'style="display:none;"').">
										<input type='text' style='width:110px;' id='formPorcentajeRecFes' class='reset entero' msgError='Digite el %' ".(($info_politica['Polarf'] != '') ? " value='".$info_politica['Polarf']."' " : "" ).">
									</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td style='width:25%;".(($tipoPorcentajen && false) ? 'display:none;': '')."' class=' fila2'>
										Hora Inicio:&nbsp;&nbsp;&nbsp;<input type='text' id='horaIniReNoc' size='8' placeholder='Seleccione...' ".(($info_politica['Polhin'] != '00:00:00' && $info_politica['Polhin'] != '') ? "value='".$info_politica['Polhin']."'" : "value='".$InicioHorarioNocturno."'").">
									</td>
									<td style='width:25%;".(($tipoPorcentajen && false) ? 'display:none;': '')."' class=' fila2'>
										Hora Fin:&nbsp;&nbsp;&nbsp;<input type='text' id='horaFinReNoc' size='8' placeholder='Seleccione...' 	".(($info_politica['Polhfn'] != '00:00:00' && $info_politica['Polhfn'] != '') ? "value='".$info_politica['Polhfn']."'" : "value='".$FinHorarioNocturno."")."
									</td>
									<td style='width:50%;display:none;background-color: #FFFFFF;'colspan='6'></td>
								</tr>
								<tr><td colspan='6'><br></td></tr>";

								// --> Recargo Dominical
						echo"	<tr class='OtrasRes' align='center'>
									<td style='width:50%;' colspan='2' class='encabezadoTabla'>
										Recargo dominical:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										% <input type='radio' name='TipoRecargoDominical' id='RecarDomPorcentaje' ".(($tipoPorcentajed) ? 'checked=checked': '')." OnClick='$(\".TipoRecDomiPorcentaje\").show();$(\".TipoRecDomiOtro\").hide();'>&nbsp;&nbsp;
										otro <input type='radio' name='TipoRecargoDominical' id='RecarDomOtro'    ".(($tipoPorcentajed) ? '': 'checked=checked')." OnClick='$(\".TipoRecDomiPorcentaje\").hide();$(\".TipoRecDomiOtro\").show();'>
									</td>
									<td style='width:50%;' colspan='2'>
									</td>
								</tr>
								<tr class='encabezadoTabla' align='center'>
									<td style='width:25%;".(($tipoPorcentajed) ? 'display:none;': '')."' class='TipoRecDomiOtro fila1'>Concepto:</td>
									<td style='width:25%;".(($tipoPorcentajed) ? 'display:none;': '')."' class='TipoRecDomiOtro fila1'>Procedimiento:</td>
									<td style='width:50%;".(($tipoPorcentajed) ? '': 'display:none;')."background-color: #FFFFFF;' class='TipoRecDomiPorcentaje' colspan='2'></td>
									<td style='width:25%;display:none;'></td>
									<td style='width:25%;display:none;'></td>
									<td style='width:50%;display:none;background-color: #FFFFFF;' colspan='2'></td>
								</tr>
								<tr class='OtrasRes'>
									<td class='fila2 pad TipoRecDomiOtro' ".(($tipoPorcentajed) ? 'style="display:none;"': '').">
										<input type='text' style='width:230px;' id='formCodConRecDom' class='reset limpiar' msgError='Digite el código' ".(($info_politica['Polcrd'] != '') ? "valor='".$info_politica['Polcrd']."' value='".$info_politica['Polcrd']."-".$array_conceptos[$info_politica['Polcrd']]."' nombre='".$array_conceptos[$info_politica['Polcrd']]."' " : "" )." >
									</td>
									<td class='fila2 pad TipoRecDomiOtro' ".(($tipoPorcentajed) ? 'style="display:none;"': '').">
										<input type='text' style='width:230px;' id='formCodProRecDom' class='reset limpiar' msgError='Digite el código' ".(($info_politica['Polprd'] != '') ? "valor='".$info_politica['Polprd']."' value='".$info_politica['Polprd']."-".$array_procedimientos[$info_politica['Polprd']]."' nombre='".$array_procedimientos[$info_politica['Polprd']]."' " : "" ).">
									</td>
									<td class='fila2 pad TipoRecDomiPorcentaje' colspan='2' ".(($tipoPorcentajed) ? '': 'style="display:none;"').">
										<input type='text' style='width:110px;' id='formPorcentajeRecDom' class='reset entero' msgError='Digite el %' ".(($info_politica['Polard'] != '') ? " value='".$info_politica['Polard']."' " : "" ).">
									</td>
									<td class='fila2 pad' style='display:none;'>
									</td>
									<td class='fila2 pad' style='display:none;': '')>
									</td>
									<td class='fila2 pad' colspan='2' style='display:none;')>
									</td>
								</tr>
							</table>
						</div>
					</div>";
					/*$arr_res_pen['ppedii'] 	= $row_res_pen['ppedii'];
					$arr_res_pen['ppedie'] 	= $row_res_pen['ppedie'];
					$arr_res_pen['ppetfa'] 	= $row_res_pen['ppetfa'];
					$arr_res_pen['id'] 		= $row_res_pen['id'];*/

			if(!isset($arr_res_pen))
			{
				echo "<input type='hidden' value='nuevo' id='formIdResPension' >";

			}
			else
			{
				echo "<input type='hidden' value='".$arr_res_pen['id']."' id='formIdResPension' >";
				// si encuentra politica de cobro el dia de traslados la habitacion de ingreso
				if($arr_res_pen['ppetfa'] =='ingreso')
				{
					$variable_ccomayor='';
					$variable_ingreso='checked';
					$variable_cconoche ='';
				}
				// si encuentra politica de cobro el dia de traslados la habitacion de valor mayor
				if($arr_res_pen['ppetfa'] =='ccomayor')
				{
					$variable_ccomayor='checked';
					$variable_ingreso='';
					$variable_cconoche ='';
				}
				// si encuentra politica de cobro el dia de traslados la habitacion de valor mayor
				if($arr_res_pen['ppetfa'] =='cconoche')
				{
					$variable_ccomayor ='';
					$variable_ingreso  ='';
					$variable_cconoche ='checked';
				}
				// si encuentra politica habitacion de cuidado critico
				if($arr_res_pen['ppetha']!='')
				{
					$que_hab = "SELECT Procod,Pronom  "
							 ."  FROM  ".$wbasedato."_000103"
							 ." WHERE  Protip='H' ";
					$res_que_hab = mysql_query($que_hab,$conex) or die("Error en el query: ".$que_hab."<br>Tipo Error:".mysql_error());
					$nvec_aux=array();
					while($row_que_hab = mysql_fetch_array($res_que_hab))
					{
						$nvec_aux [$row_que_hab['Procod']] =$row_que_hab['Procod']."-".$row_que_hab['Pronom'];
					}

					$checkes = explode('!!' ,$arr_res_pen['ppetha'] );
					$html ="";
					for($t=0; $t<(count($checkes) -1) ; $t++ )
					{
						$checkdias = explode(':' ,$checkes[$t] );
						$html .= "<tr id='tipoynumdias_".$checkdias[0]."'><td colspan='1'><input type='checkbox' class='checkhab' id='checkbox_".$checkdias[0]."' value='".$checkdias[0]."' checked><font size='1'>".$nvec_aux [$checkdias[0]]."</font></td><td><input type='text' class='texthab' id='text_num_dias_min_".$checkdias[0]."' value='".$checkdias[1]."'></td></tr>";
					}
					$html .="";
				}
				// si encuentro politica de  minimo de tiempo por estancia
				$valor_minimo_tiempo = '';
				if ($arr_res_pen['ppemin'] !='')
				{

					$valor_minimo_tiempo = $arr_res_pen['ppemin'];

				}
				if($arr_res_pen['ppemcg'] !='')
				{
					
					
				}
			}


			// --> Estancia
			$consec = 1;
			echo"	<div id='accordionOtrasRes'>
						<h3 align='left'>Políticas de pensión</h3>
						<div align='center' id='DivOtrasRes'>
							<table width='100%' id='OtrasRes'>
								<tr>
									<td colspan='7' align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda6."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
									</td>
								</tr>
								<tr class='encabezadoTabla OtrasRes' align='center'>
									<td colspan ='2' style='width:15%;'><b>Apartir de que día se cobra la estancia</b></td>
									<td colspan ='2' style='width:20%;'><b>Numero de días que se restan de la estancia</b></td>
									<td colspan='3' style='width:65%;'><b>Cobrar estancia por traslado basado en</b></td>
								</tr>
								<tr class='OtrasRes'>
									<td colspan ='2' style='width:15%;' class='fila2 pad'>
										<input type='text' style='width:80px;'  id='formPensDiasApartIng' class='reset entero' msgError='Número de días' value='".((isset($arr_res_pen)) ? $arr_res_pen['ppedii'] : '')."'>
									</td>
									<td colspan ='2' style='width:20%;' class='fila2 pad' align='center'>
										<input type='text' style='width:80px;'  id='formPensDiasApartAlt' class='reset entero' msgError='Número de días' value='".((isset($arr_res_pen)) ? $arr_res_pen['ppedie'] : '')."'>
									</td>
									<td  colspan ='1' style='width:15%;' class='fila2 pad' >
										Habitación origen<input type='radio' ".$variable_ingreso." value='ingreso' id='tp_ingreso' name='tipopension' >
									</td>
									<td colspan ='1' style='width:20%;'  class='fila2 pad'>
										Habitación de mayor valor<input type='radio' ".$variable_ccomayor."  value='ccomayor' id='tp_ccomayor' name='tipopension' >
									</td>
									<td colspan ='1'  style='width:30%;' class='fila2 pad'>
										Habitación en la que pasa la noche<input type='radio' ".$variable_cconoche."  value='cconoche' id='tp_ccomayor' name='tipopension' ><input type='text' style='width:50px;' id='horaespecificaestancia' class='horaespecificaestancia' value='".$arr_res_pen['ppehes']."'>
									</td>
								</tr>
								<tr>
									<td class='encabezadoTabla'>Tiempo Minimo a tener en cuenta por estancia (en horas) </td><td colspan='2'></td>
								</tr>
								<tr>
									<td class='fila2 pad'><input type='text' id='formPenstiempominimo' value='".$valor_minimo_tiempo."'></td><td colspan='2'></td>
								</tr>
							</table>
							<br>
							<br>
							<table>
								<tr class='encabezadoTabla ' align='center'>
									<td  colspan='3' style='width:100%;'>
									Cuidado Critico
									</td>
								</tr>
								<tr class='fila2 ' align='center'>
								<td valign='top'  style='width:15%;' >
									<table style='width:15%;'  >
										<tr  class='fila1'>
											<td  style='width:15%;' colspan='1' >Tipos de habitacion</td>
										</tr>
										<tr>
											<td  colspan='1'  style='width:15%;'>
	
												<select   id='tipo_de_habitacion' onchange='agregar_tipo_habitacion()' >";
												$q_tip_hab  = "SELECT Procod,Pronom  "
															 ."  FROM  ".$wbasedato."_000103"
															 ." WHERE  Protip='H' ";
												$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
												echo"<option value='seleccione'>seleccione</option>";
												while($row_tip_hab = mysql_fetch_array($res_tip_hab))
												{
													echo"<option value='".$row_tip_hab['Procod']."'>".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";

												}

			echo"								</select>
											</td>
										</tr>
									</table>
									<td  colspan='1'  style='width:50%;'>
										<table id='table_tipos_habitacion'>
										<tr>
											<td colspan='1' style='width:60%;' class='fila1'>Hab seleccionadas</td>
											<td colspan='1' style='width:10%;' class='fila1'>Min horas estancia</td>
										</tr>";
			echo						$html;

			echo"						</table>
									</td>
									<td valign='top' style='width:35%;'>
											<table>
												<tr class='fila1'>
													<td style='width:15%;'>Concepto</td>
													<td style='width:15%;'>Procedimiento</td>
												</tr>
												<tr class='fila2' align='center'>
													<td style='width:15%;' align='left'>
														<input size='20'  type='text' ".(($arr_res_pen['ppecon'] != '') ? "valor='".$arr_res_pen['ppecon']."' value='".$array_conceptos[$arr_res_pen['ppecon']]."' " : "" )."  prefijo='formConCobraHab' id='formConCobraHab".$consec."' class='reset limpiar consecutivocon' consecutivo='".$consec."'  msgError='Digite el código' CamposActiva='formProCobraHab".$consec."' OnBlur='ActivarCampos(this);' CargarAutocomplete='formProCobraHab' MsgOblig='concepto'>
													</td>
													<td style='width:15%;' align='left'>
														<input size='20'  type='text' ".(($arr_res_pen['ppepro'] != '') ? "valor='".$arr_res_pen['ppepro']."' value='".$array_procedimientos[$arr_res_pen['ppepro']]."' " : "" )."  prefijo='formProCobraHab' id='formProCobraHab".$consec."' class='reset limpiar consecutivocon'  consecutivo='".$consec."' msgError='Digite el código' MsgOblig='procedimiento'>
													</td>
												</tr>
											</table>
									</td>
								</tr>
							</table>
							<br>
							
							<table id='tablaPoliticaModificarCargosEstancia' width='100%' >
							<tr class='encabezadoTabla'><td colspan='5' align='center'>Politica de Modificacón de Cargos segun estancia</td></tr>
							<tr class='fila1'><td>Tipo de habitacion</td><td>Concepto</td><td>Procedimiento</td><td>Facturable</td><td><input type='button' value='Agregar' onclick='agregar_habitacion_politica_modificar()'></td></tr>";
		
		if($arr_res_pen['ppemcg'] !='' )
		{
			$auxM =   explode('!',$arr_res_pen['ppemcg']); 
			$consec 			=2;
			for($t=1 ; $t<count($auxM) ; $t++ ){
				echo"<tr class='CambioCargosPoliticas'><td><select class='selectppal'>";
				
				
									$q_tip_hab  = "SELECT Procod,Pronom  "
												 ."  FROM  ".$wbasedato."_000103"
												 ." WHERE  Protip='H' ";
									$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
									echo"<option value='seleccione'>seleccione</option>";
									$auxMtipocama = explode(':',$auxM[$t]);
									while($row_tip_hab = mysql_fetch_array($res_tip_hab))
									{
										if($auxMtipocama[0] == $row_tip_hab['Procod'] )
										{
											echo"<option selected value='".$row_tip_hab['Procod']."'>".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";

										}
										else
										{
											echo"<option value='".$row_tip_hab['Procod']."'>".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";

										}
										
									}
									// en la posicion 1 esta el concepto
									$conceptoM = $auxMtipocama[1];
									// en la posicion 2 esta el procedimiento
									$procedimientoM=$auxMtipocama[2];
									// en la posicion 3 esta facturable o no 
									$facturable =  $auxMtipocama[3];
									
				echo				"</select></td>
									 <td >
										<input size='20'  type='text' ".(($conceptoM != '') ? "valor='".$conceptoM."' value='".$conceptoM."-".$array_conceptos[$conceptoM]."' nombre='".$array_conceptos[$conceptoM]."' " : "" )."  prefijo='formConCobraHab' id='formConCobraHab".$consec."' consecutivo='".$consec."'  class='reset limpiar consecutivocon' msgError='Digite el código' CamposActiva='formProCobraHab".$consec."' OnBlur='ActivarCampos(this);' CargarAutocomplete='formProCobraHab' MsgOblig='concepto'>
									 </td>
									 <td >
										<input size='20'  type='text' ".(($procedimientoM != '') ? "valor='".$procedimientoM."' value='".$procedimientoM."-".$array_procedimientos[$procedimientoM]."' nombre='".$array_procedimientos[$procedimientoM]."' " : "" )."  prefijo='formProCobraHab' id='formProCobraHab".$consec."' consecutivo='".$consec."'  class='reset limpiar consecutivopro' msgError='Digite el código' MsgOblig='procedimiento'>
									 </td>
									 <td><select id='tipo' class='selectfacturable'>";
				echo				 "<option value='S' ".(($facturable =='S') ? 'selected' : '').">Si</option><option value='N'  ".(($facturable =='N') ? 'selected' : '').">No</option></select></td><td><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>
									 </tr>";
			$consec++;
			}
		}
		else
		{
				echo"<tr class='CambioCargosPoliticas'><td><select class='selectppal'>";
				$consec 			=2;
				
									$q_tip_hab  = "SELECT Procod,Pronom  "
												 ."  FROM  ".$wbasedato."_000103"
												 ." WHERE  Protip='H' ";
									$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
									echo"<option value='seleccione'>seleccione</option>";
									while($row_tip_hab = mysql_fetch_array($res_tip_hab))
									{
										echo"<option value='".$row_tip_hab['Procod']."'>".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";

									}
				echo				"</select></td>
									 <td >
										<input size='20'  type='text' ".(($arr_res_pen['ppecon'] != '') ? "valor='".$arr_res_pen['ppecon']."' value='".$array_conceptos[$arr_res_pen['ppecon']]."' " : "" )."  prefijo='formConCobraHab' id='formConCobraHab".$consec."' consecutivo='".$consec."'  class='reset limpiar consecutivocon' msgError='Digite el código' CamposActiva='formProCobraHab".$consec."' OnBlur='ActivarCampos(this);' CargarAutocomplete='formProCobraHab' MsgOblig='concepto'>
									 </td>
									 <td >
										<input size='20'  type='text' ".(($arr_res_pen['ppepro'] != '') ? "valor='".$arr_res_pen['ppepro']."' value='".$array_procedimientos[$arr_res_pen['ppepro']]."' " : "" )."  prefijo='formProCobraHab' id='formProCobraHab".$consec."' consecutivo='".$consec."'  class='reset limpiar consecutivopro' msgError='Digite el código' MsgOblig='procedimiento'>
									 </td>
									 <td>
									 <select id='tipo' class='selectfacturable'><option value='S'>Si</option><option value='N'>No</option></select>
									 </td>
									 <td></td>
									 </tr>";
			
		}
		
							
							
		echo"					</table><input type='hidden' id='consecutivo' value='3'>
						</div>
					</div>";

		// --> Políticas quirúrgicas
		if($arrayConceptosInv[$arrayResQui['Pcicnf']])
			$nomPro5 = $array_articulos[$arrayResQui['Pcipnf']];
		else
			$nomPro5 = $array_procedimientos[$arrayResQui['Pcipnf']];

		if($arrayConceptosInv[$arrayResQui['Pciccc']])
			$nomPro6 = $array_articulos[$arrayResQui['Pciccp']];
		else
			$nomPro6 = $array_procedimientos[$arrayResQui['Pciccp']];

		echo"
					<div id='accordionQuirurgicas'>
						<h3 align='left'>Políticas quirúrgicas</h3>
						<div align='center'>
							<table id='Quirurgicas' width='100%'>
								<tr>
									<td colspan='3' align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda9."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
										<input type='hidden' id='formIdPolQuirurgicas' value='".((isset($arrayResQui)) ? $arrayResQui['id'] : "nuevo" )."'>
									</td>
								</tr>
								<tr>
									<td align='center' class='fila1 pad' style='font-weight:bold;' width='49%'>
										Restringir cantidades
									</td>
									<td width='2%'></td>
									<td align='center' class='fila1 pad' style='font-weight:bold;' width='49%'>
										No facturables
									</td>
								</tr>
								<tr>
									<td class='fila2 pad' style='font-size:9pt'>
										La cantidad permitida a facturar es de
										<input type='text' id='cantPermFac' 		class='entero'	size='1' placeholder='&nbsp;' value='".((isset($arrayResQui)) ? $arrayResQui['Pcicpf'] : '')."'>
										, durante los primeros
										<input type='text' id='diasDespuPermFac' 	class='entero'	size='1' placeholder='&nbsp;' value='".((isset($arrayResQui)) ? $arrayResQui['Pcipdf'] : '')."'>
										días después<br>de la ultima cirugía. <!--o procedimiento terapéutico (dependiendo del cco).-->
									</td>
									<td></td>
									<td class='fila2 pad' style='font-size:9pt'>
										Si en el mismo día de grabación hay un cargo de:<br>
										<b>Concepto:</b>		<input type='text' id='conNoFactuMismoDia' style='font-size:8pt;' placeholder='Digite el código o nombre' size='29' ".((isset($arrayResQui['Pcicnf']) && $arrayResQui['Pcicnf'] != '') ? "value='".$arrayResQui['Pcicnf']."-".$array_conceptos[$arrayResQui['Pcicnf']]."' 		valor='".$arrayResQui['Pcicnf']."' nombre='".$array_conceptos[$arrayResQui['Pcicnf']]."'" : '').">&nbsp;&nbsp;
										<b>Procedimiento:</b>	<input type='text' id='proNoFactuMismoDia' style='font-size:8pt;' placeholder='Digite el código o nombre' size='29' ".((isset($arrayResQui['Pcipnf']) && $arrayResQui['Pcipnf'] != '') ? "value='".$arrayResQui['Pcipnf']."-".$nomPro5."' 	valor='".$arrayResQui['Pcipnf']."' nombre='".$nomPro5."'" : '').">
										<br>Entonces, cambiar este cargo ya grabado a <b>no facturable</b>.
									</td>
								</tr>
								<tr><td><br></td></tr>
								<tr>
									<td align='center' class='fila1 pad' style='font-weight:bold;'>
										Cambio de código
									</td>
									<td></td>
									<td align='center' class='pad' style='font-weight:bold;'>
									</td>
								</tr>
								<tr>
									<td class='fila2 pad' style='font-size:9pt'>
										<input type='text' id='diasCambioCodigo' size='2' class='entero' placeholder='&nbsp;' value='".((isset($arrayResQui)) ? $arrayResQui['Pciccd'] : '')."'>
										días después del procedimiento
										<input type='text' id='proRealizadoCambioCodigo' style='font-size:8pt;' placeholder='Digite el código o nombre' size='29' ".((isset($arrayResQui['Pciccr']) && $arrayResQui['Pciccr'] != '') ? "value='".$arrayResQui['Pciccr']."-".$array_procedimientos[$arrayResQui['Pciccr']]."' 	valor='".$arrayResQui['Pciccr']."'	nombre='".$array_procedimientos[$arrayResQui['Pciccr']]."'" : '')."> se reconoce como:<br>
										<b>Concepto:</b>		<input type='text' id='conCambioCodigo' style='font-size:8pt;' placeholder='Digite el código o nombre' size='29' ".((isset($arrayResQui['Pciccc']) && $arrayResQui['Pciccc'] != '') ? "value='".$arrayResQui['Pciccc']."-".$array_conceptos[$arrayResQui['Pciccc']]."' 		valor='".$arrayResQui['Pciccc']."' 	nombre='".$array_conceptos[$arrayResQui['Pcicnf']]."'" : '').">&nbsp;
										<b>Procedimiento:</b>	<input type='text' id='proCambioCodigo' style='font-size:8pt;' placeholder='Digite el código o nombre' size='29' ".((isset($arrayResQui['Pciccp']) && $arrayResQui['Pciccp'] != '') ? "value='".$arrayResQui['Pciccp']."-".$nomPro6."' 	valor='".$arrayResQui['Pciccp']."'	nombre='".$nomPro6."'" : '')."><br><br>
										<b>Nota: </b>Este cambio de código aplica para el mismo ingreso de realización de la cirugía;<br>
										Para las próximos ingresos aplica, siempre y cuando sean antes del día de inicio de la restricción.
									</td>
									<td></td>
									<td class='pad' style='font-size:9pt'></td>
								</tr>
							</table>
						</div>
					</div>";
		// --> Otras politicas
		echo"
					<div id='accordionResVarias'>
						<h3 align='left'>Otras</h3>
						<div align='center'>
							<table id='ResVarias' width='100%'>
								<tr>
									<td colspan='5' align='right'>
										<img style='cursor:help' class='tooltip' title='".$MsjAyuda8."' width='20' height='20' border='0' src='../../images/medical/root/help.png' />
									</td>
								</tr>
								<tr class='ResVarias fila1' align='center' style='font-weight:bold;'>
									<td>Cargos adicionales:</td>
									<td>Permitir facturar:</td>
									<td>Pedir tercero:</td>
									<td>Incruentos:</td>
								</tr>";
						// --> Cargos adicionales
						$cargosAdicionale = array();
						if(isset($info_politica['Polcad']))
							$cargosAdicionale = json_decode($info_politica['Polcad'], TRUE);

						echo"	<tr class='ResVarias' style='style='color:#000000;font-size:8pt;padding:1px;font-family:verdana;''>
									<td class='fila2 pad' align='center'>
										Al grabar el cargo de entrada, incluir el cobro de los siguientes cargos:<br>
										<b>Concepto:</b>		<input type='text' id='conAdicional' style='font-size:8pt;' placeholder='Digite el código o nombre' size='27' valor=''>&nbsp;
										<b>Procedimiento:</b>	<input type='text' id='proAdicional' style='font-size:8pt;' placeholder='Digite el código o nombre' size='27' valor='' nombre=''>
										<input type='button' style='font-size:8pt;cursor:pointer' value='Agregar' onClick='agregarCargosAdicionales()'/><br>
										<br>
										<table id='listaCargosAdicionales' class='Bordegris' style='font-size: 7pt;text-align: center;".((count($cargosAdicionale) == 0) ? "display:none" : "")."' width='98%'>
											<tr class='encabezadoTabla' style='font-size:8pt' encabezado='si'>
												<td>Concepto</td>
												<td>Procedimiento</td>
												<td>%Cobro</td>
												<td>Cant.</td>
												<td>Fact.</td>
												<td>Cco</td>
												<td></td>
											</tr>";
										foreach($cargosAdicionale as $valoresCargoAd)
										{
											if($arrayConceptosInv[$valoresCargoAd['concepto']])
												$nomProAd = $array_articulos[$valoresCargoAd['procedimiento']];
											else
												$nomProAd = $array_procedimientos[$valoresCargoAd['procedimiento']];

											echo"
											<tr codCon='".$valoresCargoAd['concepto']."' codPro='".$valoresCargoAd['procedimiento']."'>
												<td align='left'>&nbsp;".$valoresCargoAd['concepto']."-".$array_conceptos[$valoresCargoAd['concepto']]."</td>
												<td align='left'>&nbsp;".$valoresCargoAd['procedimiento']."-".$nomProAd."</td>
												<td align='center'>&nbsp;<input type='text' name='porCobroAdi' size='5' class='entero' value='".$valoresCargoAd['porcentajeCobro']."'></td>
												<td align='center'>&nbsp;<input type='text' name='cargoAdicCant' size='3' class='entero' value='".$valoresCargoAd['cargoAdicCant']."'></td>
												<td align='center'>
													<select cargoAdicFacturable=''>
														<option value='S' ".(($valoresCargoAd['cargoAdicFacturable'] == 'S') ? "SELECTED" : "").">Si</option>
														<option value='N' ".(($valoresCargoAd['cargoAdicFacturable'] == 'N') ? "SELECTED" : "").">No</option>
													</select>
												</td>
												<td align='center'>
													<select cargoAdicCco=''>
														<option value='PAC' ".(($valoresCargoAd['cargoAdicCco'] == 'PAC') ? "SELECTED" : "").">Act. Paciente</option>
														<option value='ORI' ".(($valoresCargoAd['cargoAdicCco'] == 'ORI') ? "SELECTED" : "").">Orig. Cargo</option>														
													</select>
												</td>
												<td align='center' width='2%'><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>
												</td>
											</tr>";
										}
						echo"			</table>
									</td>
									<td class='fila2 pad' align='center'>
										Si:<input type='radio' name='PermitirFac' id='PermitirFacSi' ".$checkFac1." OnChange='toogleAcordeonesRestriciones()' >&nbsp;
										No:<input type='radio' name='PermitirFac' id='PermitirFacNo' ".$checkFac2." OnChange='toogleAcordeonesRestriciones()'>
									</td>
									<td class='fila2 pad' align='center' id='manejoTerceros' style=';".(($info_politica['Poltpa'] == 'on') ? " display:none'" : "'").">
										<table>
											<tr>
												<td align='right'>Si:</td>
												<td><input type='radio' 		".(($info_politica['Polmte']=='C') ? "checked='checked'" : "")." name='pedirTercero' id='PedirTerceroSi' 	 value='C'></td>
												<td align='right'>No:</td>
												<td><input type='radio' 		".(($info_politica['Polmte']=='P') ? "checked='checked'" : "")." name='pedirTercero' id='PedirTerceroNo' 	 value='P'></td>
											</tr>
											<tr>
												<td align='right'>Por defecto:</td>
												<td><input type='radio'	".(($info_politica['Polmte']=='' || $info_politica['Polmte']=='D' || $CodigoPolitica == 'Nueva') ? "checked='checked'" : "")." name='pedirTercero' id='PedirTerceroDefault' value='D'></td>
												<td align='right'>Opcional:</td>
												<td><input type='radio'	".(($info_politica['Polmte']=='OP') ? "checked='checked'" : "")." name='pedirTercero' id='PedirTerceroOpcional' value='OP'></td>
											</tr>
										</table>
									</td>
									<td class='fila2 pad' align='center'>
										Porcentaje de cobro: <input type='text' size='3' value='".((isset($info_politica)) ? $info_politica['Polpci'] : '100')."' id='porcenCobroIncru' class='entero'> <b>%</b>
									</td>
								</tr>
								<tr class='ResVarias fila1' align='center' style='font-weight:bold;'>
									<td>Cobros por meses calendario (Sin importar el ingreso):</td>
								</tr>	
								<tr style='color:#000000;font-size:8pt;padding:1px;font-family:verdana;'>
									<td class='fila2 pad' align='center'>
										Permitir grabar sí facturable 
										<input type='text' class='entero' id='cobrosMesCant' style='font-size:8pt;' placeholder='#' size='3' valor='' value='".$info_politica['Polcmc']."'>
										 vez/veces, en el periodo de <input type='text' class='entero' id='cobrosMesNumMeses' style='font-size:8pt;' placeholder='#' size='3' valor='' value='".$info_politica['Polcmn']."'>
										mes/es calendario.
									</td>
								</tr>";
						echo"
							</table>
						</div>
					</div>";
		$arrayPorcenPaque = array();
		//$arrayPorcenPaque = explode('|', $info_politica['Polpcp']);
		// --> Porcentajes de cobro para paquetes
		echo "		<div id='accordionPorcentajesPaquetes' style='display:none'>
						<h3 align='left'>Porcentajes de cobro</h3>
						<div align='center' id='DivResAnu'>
							<table width='100%'>
								<tr><td align='right'><img style='cursor:help' class='tooltip' title='".$MsjAyuda7."' width='20' height='20' border='0' src='../../images/medical/root/help.png' /></td></tr>
							</table>
							<table width='50%' id='valoresPorcentajesCobro'>
								<tr>
									<td width='20%' align='left' class='encabezadoTabla'>Paquete 1:</td>
									<td width='30%' align='center' class='fila1'><input type='text' size='10' value='".$arrayPorcenPaque[0]."' class='entero'> <b>%</b></td>
									<td width='20%' align='left' class='encabezadoTabla'>Otros:</td>
									<td width='30%' align='center' class='fila1'><input type='text' size='10' value='".$arrayPorcenPaque[1]."' class='entero'> <b>%</b></td>
								</tr>
							</table>
						</div>
					</div>";
		echo"		<br>
					<div>
						<button style='font-family: verdana;font-weight:bold;font-size: 10pt;width:150px;' onclick='guardar_politica(this)'>Guardar Política</button>
					</div>
					<table width='95%'>
						<tr>
							<td align='right'>
								<div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>";
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function contenedor_lista_politicas()
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
							<td align='right' width='90%'><input type='button' value='Nueva Política' OnClick='nueva_politica();'></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table width='100%'class='Bordegris fila2' style='padding:2px; font-size: 8pt;' >
						<tr style='font-weight:bold'>
							<td ></td>
							<td width='6%' align='center' >Código:</td>
							<td width='25%' align='center' >Nombre:</td>
							<td width='25%' align='center' >Tipo:</td>
							<td width='20%' align='center' id='NomBusTipo'></td>
							<td width='20%' align='center' >Entidad:</td>
							<td width='4%' align='center' >Estado:</td>
							<td></td>
						</tr>
						<tr>
							<td align='center'><span style='font-size: 10pt;font-weight:bold;color:#999999'>Buscar:</span></td>
							<td align='center' class='pad'><input type='text' style='width:98%' id='busc_codigo' ></td>
							<td align='center' class='pad'><input type='text' style='width:98%' id='busc_nombre'/></td>
							<td align='center' class='pad' id='ConteTipos'>
								<input type='radio' name='busc_tipo' id='busc_Concepto' value='off' OnChange='BusquedaTipo(this)'>Concepto
								<input type='radio' name='busc_tipo' id='busc_Paquete'  value='on'  OnChange='BusquedaTipo(this)'>Paquete
								<input type='radio' name='busc_tipo' id='busc_Ambos'    value='%'   OnChange='BusquedaTipo(this)'>Ambos

							</td>
							<td align='center' class='pad'><input type='text' style='width:98%' id='busc_ConceptoPaquete'></td>
							<td align='center' class='pad'><input type='text' style='width:98%' id='busc_entidad'></td>
							<td align='center' class='pad'>
								<select id='buscEstado'>
									<option value=''>Todos</option>
									<option value='on'>Activo</option>
									<option value='off'>Inactivo</option>
								</select>
							</td>
							<td>
								<button style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt' onClick='ListarPoliticas()' >
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
					<div id='div_lista'>";
		echo 		lista_politicas('', '', '', '', '', '');
		echo"		</div>
				</td>
			</tr>
		</table>";
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
		case 'crear_hidden_procedimientos':
		{
			//$Array_proc	= Obtener_array_procedimientos_x_concepto($CodConcepto);
			$Array_proc	= Obtener_array_detalle_concepto($CodConcepto);
			echo json_encode($Array_proc);
			break;
			return;
		}
		case 'mostrar_formulario':
		{
			PintarFormularioPolitica($CodigoPolitica);
			break;
			return;
		}
		case 'agregar_habitacion_politica_modificar':
		{
			
			$html.="<tr class='CambioCargosPoliticas'><td><select class='selectppal' >";
			//$consec =3;
		
			$q_tip_hab  = "SELECT Procod,Pronom  "
						 ."  FROM  ".$wbasedato."_000103"
						 ." WHERE  Protip='H' ";
			$res_tip_hab = mysql_query($q_tip_hab,$conex) or die("Error en el query: ".$q_tip_hab."<br>Tipo Error:".mysql_error());
			$html.="<option value='seleccione'>seleccione</option>";
			while($row_tip_hab = mysql_fetch_array($res_tip_hab))
			{
				$html.="<option value='".$row_tip_hab['Procod']."'>".$row_tip_hab['Procod']."-".$row_tip_hab['Pronom']."</option>";

			}
			$html.=				"</select></td>
								 <td >
									<input size='20'  type='text'  value=''  prefijo='formConCobraHab' id='formConCobraHab".$consec."' consecutivo='".$consec."'  class='reset limpiar consecutivocon' msgError='Digite el código' CamposActiva='formProCobraHab".$consec."' OnBlur='ActivarCampos(this);' CargarAutocomplete='formProCobraHab' MsgOblig='concepto'>
								 </td>
								 <td >
									<input size='20'  type='text' value=''  prefijo='formProCobraHab' id='formProCobraHab".$consec."' consecutivo='".$consec."'  class='reset limpiar consecutivopro' msgError='Digite el código' MsgOblig='procedimiento'>
								 </td>
								 <td><select id='tipo' class='selectfacturable'><option value='S'>Si</option><option value='N'>No</option></select></td><td></td>
								 </tr>";
			
			echo $html;
			break;
			return;
		}
		case 'guardar_formulario':
		{
			// --> Si se va a guardar politica tipo concepto
			if($formTipoPaquete == 'off')
			{
				// --> Armar querys para las restricciones de dias.
				if(isset($RestriDias) && $RestriDias != '')
				{
					if($formCodigo != '')
						$formCodigoTemp = $formCodigo;
					else
						$formCodigoTemp = generar_codigo();

					$ApliRestDias 	= 'on';
					$RestriDias		= explode('->', $RestriDias);
					foreach($RestriDias as $valor)
					{
						$valores 	= explode('|', $valor);
						if($valores[0] == 'nuevo')
						{
							$q_dias[]	=	"
							INSERT INTO ".$wbasedato."_000156
									(Medico,			Fecha_data,    	Hora_data,		Rdicpo,					Rdinum,				Rdicco,				Rdicpr,					Rdidin,				Rdidfi,				Rdiest,	Seguridad, 		id)
							VALUES(	'".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$formCodigoTemp."',	'".$valores[1]."',	'".$valores[2]."',	'".$valores[3]."',		'".$valores[4]."',	'".$valores[5]."',	'on',	'C-".$wuse."',	'')
							";
						}
						else
						{
							$q_dias[]	=	"
							UPDATE ".$wbasedato."_000156
							SET		Rdinum 	= '".$valores[1]."',
									Rdicco 	= '".$valores[2]."',
									Rdicpr 	= '".$valores[3]."',
									Rdidin 	= '".$valores[4]."',
									Rdidfi 	= '".$valores[5]."'
							WHERE 	id 		= '".$valores[0]."'
							";
						}
					}
				}
				else
					$ApliRestDias = 'off';

				// --> Armar querys para las restricciones de rangos de horas.
				if(isset($RestriRangHoras) && $RestriRangHoras != '')
				{
					if($formCodigo != '')
						$formCodigoTemp = $formCodigo;
					else
						$formCodigoTemp = generar_codigo();

					$ApliRestRangHo	= 'on';
					$RestriRangHoras= explode('->', $RestriRangHoras);
					foreach($RestriRangHoras as $valor)
					{
						$valores 			= explode('|', $valor);
						if($valores[0] == 'nuevo')
						{
							$q_rango_horas[]	=	"
							INSERT INTO ".$wbasedato."_000158
									(Medico,			Fecha_data,    	Hora_data,		Rrhcpo,					Rrhcco,				Rrhcpr,				Rrhhin,					Rrhhfi,				Rrhest,	Seguridad, 		id)
							VALUES(	'".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$formCodigoTemp."',	'".$valores[1]."',	'".$valores[2]."',	'".$valores[3]."',		'".$valores[4]."',	'on',	'C-".$wuse."',	'')
							";
						}
						else
						{
							$q_rango_horas[]	=	"
							UPDATE ".$wbasedato."_000158
							SET		Rrhcco 	= '".$valores[1]."',
									Rrhcpr 	= '".$valores[2]."',
									Rrhhin 	= '".$valores[3]."',
									Rrhhfi 	= '".$valores[4]."'
							WHERE 	id		= '".$valores[0]."'
							";
						}
					}
				}
				else
					$ApliRestRangHo = 'off';

				// --> Armar querys para las restricciones de pension.
				if(isset($RestringirPension) && $RestringirPension != '')
				{
					if($formCodigo != '')
						$formCodigoTemp = $formCodigo;
					else
						$formCodigoTemp = generar_codigo();

					if($idRestriccionPension == 'nuevo')
					{

						$q_pension[]	=	"
						INSERT INTO ".$wbasedato."_000172
							  (		Medico		,	  Fecha_data ,   Hora_data 	,		ppecod			 ,		ppedii		 		   ,		ppedie	 	 		   ,	ppetfa			, ppetha	  			,        ppecon				, 		ppepro				,				ppeest  ,	Seguridad    ,  id ,   ppemin		, ppehes	, ppemcg		 )
						VALUES(	'".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$formCodigoTemp."',	'".$formPensDiasApartIng."',	'".$formPensDiasApartAlt."','".$formPensCobro."', '".$vec_tipohab."'	, '".$formConCobraHab."'	,	'".$formProCobraHab1."'	,		     	   'on'  ,	'C-".$wuse."',	'' ,  '".$formPenstiempominimo."' , '".$horaespecificaestancia."', '".$formModificacionCargos."' )
						";
					}
					else
					{
						$q_pension[]	=	"
						UPDATE ".$wbasedato."_000172
						SET		ppedii 	= '".$formPensDiasApartIng."',
								ppedie 	= '".$formPensDiasApartAlt."',
								ppetfa 	= '".$formPensCobro."',
								ppehes	= '".$horaespecificaestancia."',
								ppetha	= '".$vec_tipohab."' ,
								ppepro	= '".$formProCobraHab1."' ,
								ppecon  = '".$formConCobraHab1."',
								ppemin  = '".$formPenstiempominimo."',
								ppemcg  = '".$formModificacionCargos."'
						WHERE 	id		= '".$idRestriccionPension."'
						";

					}
					$ApliRestPension = 'on';

				}
				else
					$ApliRestPension = 'off';

				// --> Armar query para la restriccion de no facturables
				$RestriAnul = json_decode(str_replace('\\', '', $RestriAnul));
	
				if(isset($RestriAnul->id))
				{
					if($formCodigo != '')
						$formCodigoTemp = $formCodigo;
					else
						$formCodigoTemp = generar_codigo();

					$ApliRestAnula	= 'on';

					if($RestriAnul->id == 'nuevo')
					{
						$q_anulaciones[]	=	"
						INSERT INTO ".$wbasedato."_000157
								(Medico,			Fecha_data,    	Hora_data,		Rcacpo,					Rcaeoc,								Rcacca,											Rcacpa,											Rcafcg,										Rcaccc,								Rcacpc,								Rcafcn,										 Rcamay,								Rcaest,	Rcasig,									Rcasfg,									Rcafsg,									Rcacsg,								Rcacco,											Seguridad, 		id)
						VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$formCodigoTemp."',	'".$RestriAnul->eventOcurridos."',	'".json_encode($RestriAnul->codConAnula)."',	'".json_encode($RestriAnul->codProAnula)."',	'".$RestriAnul->facturableCargoGrabado."',	'".$RestriAnul->conCobraSiAnula."',	'".$RestriAnul->proCobraSiAnula."',	'".$RestriAnul->facturableCargoNuevo."',	'".$RestriAnul->validarSiEsMayor."',	'on',	'".$RestriAnul->formSemIniGestacion."',	'".$RestriAnul->formSemFinGestacion."',	'".$RestriAnul->formFormularioHce."',	'".$RestriAnul->formCampoHce."',	'".json_encode($RestriAnul->codCcoAnula)."',	'C-".$wuse."',	'')
						";
					}
					else
					{
						$q_anulaciones[]	=	"
						UPDATE	".$wbasedato."_000157
						SET		Rcaeoc 	= '".$RestriAnul->eventOcurridos."',
								Rcacca 	= '".json_encode($RestriAnul->codConAnula)."',
								Rcacpa 	= '".json_encode($RestriAnul->codProAnula)."',
								Rcafcg 	= '".$RestriAnul->facturableCargoGrabado."',
								Rcaccc 	= '".$RestriAnul->conCobraSiAnula."',
								Rcacpc 	= '".$RestriAnul->proCobraSiAnula."',
								Rcafcn 	= '".$RestriAnul->facturableCargoNuevo."',
								Rcamay 	= '".$RestriAnul->validarSiEsMayor."',
								Rcasig 	= '".$RestriAnul->formSemIniGestacion."',
								Rcasfg	= '".$RestriAnul->formSemFinGestacion."',
								Rcafsg	= '".$RestriAnul->formFormularioHce."',
								Rcacsg	= '".$RestriAnul->formCampoHce."',
								Rcacco	= '".json_encode($RestriAnul->codCcoAnula)."'
						WHERE 	id		= '".$RestriAnul->id."'
						";
					}
				}
				else
					$ApliRestAnula = 'off';

				// --> Armar query para las restriccion quirurgicas
				$polQuirurgicas = json_decode(str_replace('\\', '', $polQuirurgicas));

				if(isset($polQuirurgicas->id))
				{
					if($formCodigo != '')
						$formCodigoTemp = $formCodigo;
					else
						$formCodigoTemp = generar_codigo();

					if($polQuirurgicas->id == 'nuevo')
					{
						$SQLpolQuirurgicas =	"
						INSERT INTO ".$wbasedato."_000226
								(Medico,			Fecha_data,    	Hora_data,		Pcicod,					Pcicpf,								Pcipdf,									Pcicnf,										Pcipnf,										Pciccd,									Pciccr,											Pciccc,									Pciccp,									Seguridad, 		id)
						VALUES	('".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$formCodigoTemp."',	'".$polQuirurgicas->cantPermFac."',	'".$polQuirurgicas->diasDespuPermFac."','".$polQuirurgicas->conNoFactuMismoDia."',	'".$polQuirurgicas->proNoFactuMismoDia."',	'".$polQuirurgicas->diasCambioCodigo."','".$polQuirurgicas->proRealizadoCambioCodigo."','".$polQuirurgicas->conCambioCodigo."',	'".$polQuirurgicas->proCambioCodigo."',	'C-".$wuse."',	'')
						";
					}
					else
					{
						$SQLpolQuirurgicas =	"
						UPDATE	".$wbasedato."_000226
						   SET	Pcicpf = '".$polQuirurgicas->cantPermFac."',
								Pcipdf = '".$polQuirurgicas->diasDespuPermFac."',
								Pcicnf = '".$polQuirurgicas->conNoFactuMismoDia."',
								Pcipnf = '".$polQuirurgicas->proNoFactuMismoDia."',
								Pciccd = '".$polQuirurgicas->diasCambioCodigo."',
								Pciccr = '".$polQuirurgicas->proRealizadoCambioCodigo."',
								Pciccc = '".$polQuirurgicas->conCambioCodigo."',
								Pciccp = '".$polQuirurgicas->proCambioCodigo."'
						 WHERE	id	   = '".$polQuirurgicas->id."'
						";
					}
				}
			}
			// --> Si se va a guardar politica tipo paquete
			else
			{
				$ApliRestDias			= 'off';
				$ApliRestRangHo			= 'off';
				$ApliRestAnula			= 'off';
				$ApliRestPension		= 'off';
				$formCodConRecNoc		= '';
				$formCodProRecNoc		= '';
				$formPorcentajeRecNoc	= '';
				$formCodConRecFest		= '';
				$formCodProRecFest		= '';
				$formPorcentajeRecFes	= '';
				$formCodConRecDom		= '';
				$formCodProRecDom		= '';
				$formPorcentajeRecDom	= '';
				$formManejoTerceros		= '';

				// --> 	Si se esta es realizando una actualizacion a una politica, puede que depronto se este
				//		actualizando es de una politica tipo concepto a una tipo paquete, en este caso debo eliminar
				//		los registros de restricciones que existian; ya que una politica tipo paquete no presenta restricciones.
				if($formCodigo != '')
				{
					// --> Restricciones de dias
					$q_delete1 = "DELETE FROM ".$wbasedato."_000156
								   WHERE Rdicpo = '".$formCodigo."' ";
					mysql_query($q_delete1,$conex) or die("Error en el query: ".$q_delete1."<br>Tipo Error:".mysql_error());

					// --> Restricciones de anulaciones
					$q_delete2 = "DELETE FROM ".$wbasedato."_000157
								   WHERE Rcacpo = '".$formCodigo."' ";
					mysql_query($q_delete2,$conex) or die("Error en el query: ".$q_delete2."<br>Tipo Error:".mysql_error());

					// --> Restricciones de rango de horas
					$q_delete3 = "DELETE FROM ".$wbasedato."_000158
								   WHERE Rrhcpo = '".$formCodigo."' ";
					mysql_query($q_delete3,$conex) or die("Error en el query: ".$q_delete3."<br>Tipo Error:".mysql_error());
				}
			}

			$horaIniReNoc = (($horaIniReNoc != '') ? $horaIniReNoc.':00' : '');
			$horaFinReNoc = (($horaFinReNoc != '') ? $horaFinReNoc.':00' : '');

			$arrayCargosAdc	= array();
			if($cargosAdicionales != '')
			{
				$cargosAdicionales 	= explode('<>', $cargosAdicionales);
				foreach($cargosAdicionales as $indice => $valoresCarAdi)
				{
					$valoresCarAdi = explode('|', $valoresCarAdi);
					$arrayCargosAdc[$indice]['concepto'] 			= $valoresCarAdi[0];
					$arrayCargosAdc[$indice]['procedimiento'] 		= $valoresCarAdi[1];
					$arrayCargosAdc[$indice]['porcentajeCobro']		= $valoresCarAdi[2];
					$arrayCargosAdc[$indice]['cargoAdicCant']		= $valoresCarAdi[3];
					$arrayCargosAdc[$indice]['cargoAdicFacturable']	= $valoresCarAdi[4];
					$arrayCargosAdc[$indice]['cargoAdicCco']		= $valoresCarAdi[5];
				}
			}
			$arrayCargosAdc	= json_encode($arrayCargosAdc);
			
			//-- elimino politica de pension
			if($ApliRestPension =='off')
			{
					// --> Restricciones de anulaciones
					$q_delete2 = "DELETE FROM ".$wbasedato."_000172
								   WHERE ppecod = '".$formCodigo."' ";
					mysql_query($q_delete2,$conex) or die("Error en el query: ".$q_delete2."<br>Tipo Error:".mysql_error());

			}
			
			// --> Actualizar politica
			if($formCodigo != '')
			{
				$q_update = "
				UPDATE ".$wbasedato."_000155
				   SET 	Polnom = '".$formNombre."',
						Polexp = '".$formExplicacion."',
						Polccp = '".$formConceptoPaquete."',
						Polpro = '".$formProcedimiento."',
						Poltem = '".$formTipoEmpresa."',
						Poltar = '".$formTarifa."',
						Polcen = '".$formEntidad."',
						Polnen = '".$formNitEntidad."',
						Polesp = '".$formEspecialidad."',
						Polcco = '".$formCentroCostos."',
						Polcca = '".$formCentroCostosPac."',
						Poltha = '".$formTipHab."',
						Polrdi = '".$ApliRestDias."',
						Polrrh = '".$ApliRestRangHo."',
						Polrca = '".$ApliRestAnula."',
						Polcrn = '".$formCodConRecNoc."',
						Polprn = '".$formCodProRecNoc."',
						Polarn = '".$formPorcentajeRecNoc."',
						Polhin = '".$horaIniReNoc."',
						Polhfn = '".$horaFinReNoc."',
						Polcrf = '".$formCodConRecFest."',
						Polprf = '".$formCodProRecFest."',
						Polarf = '".$formPorcentajeRecFes."',
						Polcrd = '".$formCodConRecDom."',
						Polprd = '".$formCodProRecDom."',
						Polard = '".$formPorcentajeRecDom."',
						Poltpa = '".$formTipoPaquete."',
						Polfac = '".$formPermitirFacturar."',
						Polmte = '".$formManejoTerceros."',
						Polpen = '".$ApliRestPension."',
						Pollog = '".$listaPorcentajes."',
						Polpci = '".$formPorcenCobroIncru."',
						Polcad = '".$arrayCargosAdc	."',
						Polcmc = '".trim($cobrosMesCant)."',
						Polcmn = '".trim($cobrosMesNumMeses)."'
				WHERE 	Polcod = '".$formCodigo."' ";

				$res_update = mysql_query($q_update,$conex) or die("Error en el query: ".$q_update."<br>Tipo Error:".mysql_error());
				$mensaje	= "Actualización correcta";
			}
			// --> Insertar politica
			else
			{
				$formCodigo = generar_codigo();

				// --> Insertar la politica
				$q_insertar = "
				INSERT INTO ".$wbasedato."_000155
					   (Medico,				Fecha_data,    	Hora_data,		Polcod, 			Polnom,				Polexp, 				Polccp,						Polpro,						Poltem,					Poltar,				Polcen,				Polnen,					Polesp,					Polcco,					Polcca,						Poltha,				Polrdi,				Polrrh,					Polrca,					Polcrn,						Polprn,					Polarn,							Polcrf,						Polprf,						Polarf,						Polcrd,						Polprd,						Polard,						Poltpa,					Polfac,						   			Polpen,			Polmte,						Pollog,					Polpci,						Polest,	Polhin,					Polhfn,					Polcad,					Polcmc,						Polcmn, 						Seguridad, 		id)
				VALUES(	'".$wbasedato."',	'".$wfecha."',	'".$whora."',	'".$formCodigo."',	'".$formNombre."',	'".$formExplicacion."',	'".$formConceptoPaquete."',	'".$formProcedimiento."',	'".$formTipoEmpresa."',	'".$formTarifa."',	'".$formEntidad."',	'".$formNitEntidad."',	'".$formEspecialidad."','".$formCentroCostos."','".$formCentroCostosPac."',	'".$formTipHab."',	'".$ApliRestDias."','".$ApliRestRangHo."',	'".$ApliRestAnula."',	'".$formCodConRecNoc."',	'".$formCodProRecNoc."','".$formPorcentajeRecNoc."',	'".$formCodConRecFest."',	'".$formCodProRecFest."',	'".$formPorcentajeRecFes."'	,'".$formCodConRecDom."',	'".$formCodProRecDom."',	'".$formPorcentajeRecDom."','".$formTipoPaquete."',	'".$formPermitirFacturar."',  '".$ApliRestPension."',	'".$formManejoTerceros."',	'".$listaPorcentajes."','".$formPorcenCobroIncru."','on',	'".$horaIniReNoc."',	'".$horaFinReNoc."',	'".$arrayCargosAdc."',	'".trim($cobrosMesCant)."',	'".trim($cobrosMesNumMeses)."',	'C-".$wuse."',	'')
				";
				mysql_query($q_insertar,$conex) or die("Error en el query: ".$q_insertar."<br>Tipo Error:".mysql_error());
				$mensaje	= "Grabación correcta";
			}
			// --> Insertar las restricciones de dias
			if(isset($q_dias))
			{
				foreach($q_dias as $query)
					mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
			}
			// --> Insertar las restricciones de rango de horas
			if(isset($q_rango_horas))
			{
				foreach($q_rango_horas as $query)
					mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
			}
			// --> Insertar las restricciones de anulaciones
			if(isset($q_anulaciones))
			{
				foreach($q_anulaciones as $query)
					mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
			}
			// --> Insertar las restricciones de pension
			if(isset($q_pension))
			{
				foreach($q_pension as $query)
					mysql_query($query,$conex) or die("Error en el query: ".$query."<br>Tipo Error:".mysql_error());
			}
			// --> Insertar las restricciones quirurgicas
			if(isset($SQLpolQuirurgicas))
			{
				mysql_query($SQLpolQuirurgicas,$conex) or die("Error en el query: ".$SQLpolQuirurgicas."<br>Tipo Error:".mysql_error());
			}

			$respuesta['codigo']  = $formCodigo;
			$respuesta['mensaje'] = $mensaje;
			echo json_encode($respuesta);

			break;
			return;
		}
		case 'eliminar_restriccion':
		{
			switch ($TipoRestriccion)
			{
				case 'ResDia':
				{
					$tabla  = '000156';
					$campo1 = 'Rdicpo';
					$campo2 = 'Polrdi';
					break;
				}
				case 'ResHor':
				{
					$tabla  = '000158';
					$campo1 = 'Rrhcpo';
					$campo2 = 'Polrrh';
					break;
				}
				case 'ResAnu':
				{
					$tabla  = '000157';
					$campo1 = 'Rcacpo';
					$campo2 = 'Polrca';
					break;
				}
			}
			if(isset($tabla))
			{
				$q_eliminar = "DELETE FROM ".$wbasedato."_".$tabla."
								WHERE id = '".$IdRestricion."'
				";
				$res_eliminar = mysql_query($q_eliminar,$conex) or die("Error en el query: ".$q_eliminar."<br>Tipo Error:".mysql_error());
				if($res_eliminar > 0)
				{
					echo true;
					// --> Obtener cuantas restricciones tiene la politica
					$q_cant = "SELECT COUNT(*) as cantidad
								 FROM ".$wbasedato."_".$tabla."
							    WHERE ".$campo1." = '".$CodigoPolitica."'
					";
					$res_cant = mysql_query($q_cant,$conex) or die("Error en el query: ".$q_cant."<br>Tipo Error:".mysql_error());
					$row_cant = mysql_fetch_array($res_cant);

					// --> Si no tiene restriccion, desactivar el tipo de restriccion de la ficha de la politica.
					if($row_cant['cantidad'] == 0)
					{
						$q_upda = "UPDATE ".$wbasedato."_000155
								      SET ".$campo2." = 'off'
									WHERE Polcod = '".$CodigoPolitica."'
						";
						$res_cant = mysql_query($q_upda,$conex) or die("Error en el query: ".$q_upda."<br>Tipo Error:".mysql_error());
					}
				}
				else
					echo false;

			}
			break;
			return;
		}
		case 'ListarPoliticas':
		{
			lista_politicas($BuscCodigo, $BuscNombre, $BuscTipo, $BuscCodCP, $BuscEntidad, $buscEstado);
			break;
			return;
		}
		case 'CambiarEstado':
		{
			$q_estado = "UPDATE ".$wbasedato."_000155
							SET Polest = '".$estado."'
						  WHERE Polcod = '".$CodPolitica."'
			";
			$res_estado = mysql_query($q_estado,$conex) or die("Error en el query: ".$q_estado."<br>Tipo Error:".mysql_error());
			if($res_estado > 0)
					echo true;
				else
					echo false;

			break;
			return;
		}
		case 'ObtenerArrayEntidades':
		{
			echo json_encode(obtener_array_empresas_x_nit($codTarifa, $tipoEmpresa));
			break;
			return;
		}
		case 'ObtenerArrayConveniosEntidades':
		{
			// --> Array con caracteres especiales para escaparlos en el nombre
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			$q_entidades = "SELECT Empcod, Empnom
							  FROM ".$wbasedato."_000024
							 WHERE Empest = 'on'
							   AND Emptem LIKE '".$tipoEmpresa."'
							   AND Empnit LIKE '".$codNitEnt."'
							   AND Emptar LIKE '".$codTarifa."'
						  ORDER BY Empnom ";
			$res_entidades = mysql_query($q_entidades,$conex) or die("Error en el query: ".$q_entidades."<br>Tipo Error:".mysql_error());
			$arr_entidades = array();
			while($row_entidades = mysql_fetch_array($res_entidades))
			{
				$row_entidades['Empnom'] = str_replace($caracter_ma, $caracter_ok, $row_entidades['Empnom']);
				$arr_entidades[trim($row_entidades['Empcod'])] = trim(utf8_encode($row_entidades['Empnom']));
			}

			echo json_encode($arr_entidades);
			break;
			return;
		}
		case 'ObtenerTarifas':
		{
			$arr_tar = array();
			// --> Array con caracteres especiales para escaparlos en el nombre de las tarifas
			$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
			$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

			// --> Obtener las tarifas segun el tipo de empresa
			$q_tar = "
			SELECT Tarcod, Tardes
			  FROM ".$wbasedato."_000024, ".$wbasedato."_000025
			 WHERE Empest = 'on'
			   ".(($codTipoEmp != '*') ? "AND Emptem = '".$codTipoEmp."'" : "")."
			   AND Emptar = Tarcod
			   AND Tarest = 'on'
			 ";

			$res_tar = mysql_query($q_tar,$conex) or die ("Query (Consultar tarifas segun el tipo de empresa): ".$q_tar." - ".mysql_error());
			while($row_tar = mysql_fetch_array($res_tar))
			{
				$row_tar['Tardes'] = str_replace($caracter_ma, $caracter_ok, $row_tar['Tardes']);
				$arr_tar[trim($row_tar['Tarcod'])] = $row_tar['Tardes'];
			}
			echo json_encode($arr_tar);
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
	  <title>Politicas de grabacion de cargos</title>
	</head>

	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
	<link rel="stylesheet" href="../../../include/ips/facturacionERP.css" />
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script src="<?=$URL_ACTUAL?>procesos/MarcaDeAguaERP.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>

	<!--<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>-->
	<script type="text/javascript">
//=====================================================================================================================================================================
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
//=====================================================================================================================================================================

	var url_add_params = addUrlCamposCompartidosTalento();

	function validar_campos_obligatorios(Elementos, Guardar)
	{
		var Elementos 	= Elementos.split(",");
		var Mensaje		= '';

		$.each(Elementos, function(key, value)
		{
			if($('#'+value).attr('valor'))
				valor = $('#'+value).attr('valor');
			else
				valor = $('#'+value).val();

			if(valor == '' || valor == ' ')
			{
				$('#'+value).addClass('campoObligatorio');
				Guardar = false;

				if($('#'+value).attr('msgError'))
					Mensaje+='Debe ingresar '+$('#'+value).attr('MsgOblig')+'.<br>';
			}
			else
			{
				if($('#'+value).attr('msgError') && valor == $('#'+value).attr('msgError'))
				{
					$('#'+value).addClass('campoObligatorio');
					Guardar = false;
					Mensaje+='Debe ingresar '+$('#'+value).attr('MsgOblig')+'.<br>';
				}
			}

		});

		if(!Guardar)
		{
			mostrar_mensaje('<b>Faltan campos obligatorios:</b><br>'+Mensaje);
		}
		return Guardar;
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			mostrar mensaje
	//	Descripcion:	Pinta un mensaje en el div correspondiente para los mensajes
	//	Entradas:
	//	Salidas:
	//----------------------------------------------------------------------------------
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<img width='15' height='15' src='../../images/medical/root/info.png' />&nbsp;"+mensaje);
		$("#div_mensajes").css({"width":"300","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();

		$("#div_mensajes").effect("pulsate", {}, 2000);

		setTimeout(function() {
			$("#div_mensajes").hide(500);
		}, 15000);
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function ActivarCampos(Campos)
	{
		var Elementos = $(Campos).attr('CamposActiva').split("-");
		$.each( Elementos, function(key, value)
		{
			if($(Campos).val() != '')
				$('#'+value).show();
			else
				$('#'+value).hide();
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
		var Agregar		= 'NO';
		var NuevaFila	= $("#"+Tabla+" tr:last").clone();
		var NuevoId  	= NuevaFila.attr('id').replace('fila', '')
		NuevoId			= parseInt(NuevoId)+1;
		NuevaFila.attr("id",'fila'+NuevoId);
		NuevaFila.find('input[type]').each(function(){
			if($(this).val() != '' && $(this).val() != $(this).attr('msgError') && $(this).attr('type') != 'radio')
			{
				Agregar = 'SI';
			}
			var NuevoInput 		= $(this).clone();
			var PrefijoInput	= NuevoInput.attr('prefijo');
			var ConsecutivoAnt	= NuevoInput.attr('id').replace(PrefijoInput,'');
			ConsecutivoAnt		= parseInt(ConsecutivoAnt);
			ConsecutivoNew 		= ConsecutivoAnt+1;
			$(this).attr("id", PrefijoInput+ConsecutivoNew);

			if($(this).attr('CargarAutocomplete'))
			{
				var IdCampoAutocomplete = $(this).attr('CargarAutocomplete')+ConsecutivoNew;
				setTimeout(function(){
					crear_autocomplete(false, 'hidden_conceptos', 'SI', PrefijoInput+ConsecutivoNew, 'CargarProcedimientos', IdCampoAutocomplete, false);
				}, 1000);
			}

			if($(this).attr('CamposActiva'))
			{
				var CamposActiva    = $(this).attr('CamposActiva').split(ConsecutivoAnt).join(ConsecutivoNew);
				$(this).attr('CamposActiva', CamposActiva);
			}

			if($(this).attr('name'))
			{
				var NewName   	= $(this).attr('name').replace(ConsecutivoAnt, ConsecutivoNew);
				$(this).attr("name", NewName);
			}

			$(this).removeAttr('aqua');
			$(this).val('');
		});

		if(Agregar == 'SI')
		{
			$('#'+Tabla).append(NuevaFila);
			marcarAqua( NuevaFila, 'msgError', 'campoRequerido' );
			iniciarMarcaAqua( NuevaFila );
			activar_regex(NuevaFila);
			limpiar_valor(NuevaFila);
			$('.1VezOculto', NuevaFila).hide();
		}
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function crear_autocomplete_procedimientos(Campo, CodConcepto, Show, ActivarTodosProce)
	{
		/*if($( "#"+Campo ).attr("valor") == '')
			return;*/

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
				if(Campo == 'formProcedimiento')
					ActivarTodosProce = true;
			crear_autocomplete(ActivarTodosProce, respuesta, 'NO', Campo);
		});
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function BusquedaTipo(Elemento)
	{
		if( $("#busc_ConceptoPaquete").attr("autocomplete") != undefined )
			$( "#busc_ConceptoPaquete" ).autocomplete( "destroy" ).attr('valor', '').attr('value', '');
		switch(Elemento.id)
		{
			case 'busc_Concepto':
			{
				$("#NomBusTipo").text("Concepto:");
				$('#busc_ConceptoPaquete').show(200);
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'busc_ConceptoPaquete', 'NO');
				break;
			}
			case 'busc_Paquete':
			{
				$("#NomBusTipo").text("Paquete:");
				$('#busc_ConceptoPaquete').show(200);
				crear_autocomplete(false, 'hidden_paquetes', 'SI', 'busc_ConceptoPaquete');
				break;
			}
			case 'busc_Ambos':
			{
				$("#NomBusTipo").text("");
				$('#busc_ConceptoPaquete').hide(200);
				break;
			}
		}
		$(Elemento).attr('CHECKED', 'CHECKED');
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function CambioTipoConceptoOPaquete(Elemento)
	{
		$( "#formConceptoPaquete" ).autocomplete( "destroy" ).attr('valor', '').attr('value', '').removeAttr('disabled');
		// --> Cargar autocomplete de los conceptos
		if(Elemento.id == 'formTipoConcepto')
		{
			// --> Colocar el correspondiente mensaje obligatorio
			$( "#formConceptoPaquete" ).attr('MsgOblig', 'el concepto');
			// --> Cargar autocomplete de los conceptos
			crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConceptoPaquete', 'CargarProcedimientos', true);

			// --> Mostrar las secciones de restricciones
			$("#accordionResDia").show();
			$("#accordionResHor").show();
			$("#accordionResAnu").show();
			$("#accordionRecargos").show();
			$("#accordionOtrasRes").show();
			$("#accordionResVarias").show();
			$("#accordionQuirurgicas").show();
			$("#accordionPorcentajesPaquetes").hide();
		}
		else
		{
			// --> Colocar el correspondiente mensaje obligatorio
			$( "#formConceptoPaquete" ).attr('MsgOblig', 'el paquete');
			// --> Cargar autocomplete de los paquetes
			crear_autocomplete(false, 'hidden_paquetes', 'SI', 'formConceptoPaquete');
			// --> Ocultar el campo de procedimientos y manejo de terceros
			// --> Si el autocomplete ya existe, lo destruyo
			if( $("#formProcedimiento").attr("autocomplete") != undefined )
				$( "#formProcedimiento" ).autocomplete( "destroy" ).attr('valor', '').attr('value', '').hide();

			// --> Ocultar las secciones de restricciones
			$("#accordionResDia").hide();
			$("#accordionResHor").hide();
			$("#accordionResAnu").hide();
			$("#accordionRecargos").hide();
			$("#accordionOtrasRes").hide();
			$("#accordionResVarias").hide();
			$("#accordionQuirurgicas").hide();
			$("#accordionPorcentajesPaquetes").show();
		}
	}
	//---------------------------------------------------------------------
	//	Funcion que oculta o visualiza los acordeones de las restricciones
	//---------------------------------------------------------------------
	function toogleAcordeonesRestriciones()
	{
		if($("#PermitirFacSi").attr('checked') == 'checked')
		{
			// --> Mostrar las secciones de restricciones
			$("#accordionResDia").show();
			$("#accordionResHor").show();
			$("#accordionResAnu").show();
			$("#accordionRecargos").show();
			$("#accordionOtrasRes").show();
			$("#accordionResVarias").show();
			$("#accordionQuirurgicas").show();
		}
		else
		{
			// --> Ocultar las secciones de restricciones
			$("#accordionResDia").hide();
			$("#accordionResHor").hide();
			$("#accordionResAnu").hide();
			$("#accordionRecargos").hide();
			$("#accordionOtrasRes").hide();
			$("#accordionQuirurgicas").hide();
		}
	}
	
	function agregar_habitacion_politica_modificar()
	{
		$("#consecutivo").val($("#consecutivo").val()*1 + 1);
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'agregar_habitacion_politica_modificar',
			wemp_pmla:				$('#wemp_pmla').val(),
			consec:					$("#consecutivo").val()
			
		}
		,function(data){
			
			$('#tablaPoliticaModificarCargosEstancia').append(data);
			crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobraHab'+$("#consecutivo").val()+'', 'CargarProcedimientos', 'formProCobraHab'+$("#consecutivo").val()+'', false);
			
		});
	
	}
	//----------------------------------------------------------
	//	--> Guardar la politica en la BD
	//----------------------------------------------------------
	function guardar_politica(Boton)
	{
		var Guardar 			= true;
		var listaPorcentajes 	= '';

		$('.campoObligatorio').removeClass('campoObligatorio');

		$("#div_mensajes").html('');

		// --> Validar la informacion la basica
		Guardar = validar_campos_obligatorios('formNombre,formTipoEmpresa,formTarifa,formEntidad,formNitEntidad,formEspecialidad,formCentroCostos,formCentroCostosPac', Guardar);

		var formTipHab = '';
		$("#listaTiposHab tr").each(function(){
			formTipHab = formTipHab+((formTipHab == '') ? '' : '-')+$(this).attr("codTipoHab");
		});
		if(formTipHab == '')
		{
			mostrar_mensaje('<b>Faltan campos obligatorios:</b><br>Debe ingresar el tipo de habitación');
			Guardar = false;
		}

		// --> Validar que hayan seleccionado el tipo (Concepto o paquete)
		if(Guardar)
		{
			Guardar = validar_campos_obligatorios('formConceptoPaquete' ,Guardar);
			if(Guardar)
			{
				// --> Si es tipo concepto hay que validar que hayan seleccionado el procedimiento
				if($('#formTipoConcepto').attr('checked'))
				{
					var formTipoPaquete	  	= 'off';
					var formProcedimiento 	= $('#formProcedimiento').attr('valor');
					Guardar 		  		= validar_campos_obligatorios('formProcedimiento' ,Guardar);
				}
				else
					formTipoPaquete	= 'on';
			}
		}

		if(Guardar)
		{			
			// --> Restricciones para los conceptos
			if(formTipoPaquete == 'off')
			{
				// --> Validar restricciones de dias
				var UltiConsec 	= parseInt($('#ResDia tr:last').attr('id').replace('fila', ''));
				var RestriDias 	= '';
				for(var x=1; x<=UltiConsec; x++)
				{
					var valorInput = $('#formNumCobra'+x).val();
					if( valorInput != '' && valorInput != $('#formNumCobra'+x).attr('msgError'))
					{
						Guardar = validar_campos_obligatorios('formDiaInicio'+x+',formDiaFin'+x, Guardar);
						if(Guardar)
							RestriDias+= ((RestriDias != '') ? '->' : '')+(($('#formIdResDia'+x).val() != '') ? $('#formIdResDia'+x).val() : 'nuevo' )+'|'+valorInput+'|'+$('#formConCobra'+x).attr('valor')+'|'+$('#formProCobra'+x).attr('valor')+'|'+$('#formDiaInicio'+x).val()+'|'+$('#formDiaFin'+x).val();
					}
				}
				// --> Validar restricciones de rango de horas
				if($('#ResHor tr:last').attr('id'))
				{
					var UltiConsec 		= parseInt($('#ResHor tr:last').attr('id').replace('fila', ''));
					var RestriRangHoras	= '';
					for(var x=1; x<=UltiConsec; x++)
					{
						if($('#formConCobraHoras'+x).attr('valor'))
						{
							var valorInput = $('#formConCobraHoras'+x).attr('valor');
							if( valorInput != '' && valorInput != $('#formConCobraHoras'+x).attr('msgError'))
							{
								Guardar = validar_campos_obligatorios('formProCobraHoras'+x+',formHoraInicio'+x+',formHoraFin'+x, Guardar);
								if(Guardar)
									RestriRangHoras+= ((RestriRangHoras != '') ? '->' : '')+(($('#formIdResHor'+x).val() != '') ? $('#formIdResHor'+x).val() : 'nuevo' )+'|'+valorInput+'|'+$('#formProCobraHoras'+x).attr('valor')+'|'+$('#formHoraInicio'+x).val()+'|'+$('#formHoraFin'+x).val();
							}
						}
						else
						{
							var valorInput = $('#formHoraInicio'+x).val();
							if( valorInput != '' && valorInput != $('#formHoraInicio'+x).attr('msgError'))
							{
								Guardar = validar_campos_obligatorios('formHoraFin'+x, Guardar);
								if(Guardar)
									RestriRangHoras+= ((RestriRangHoras != '') ? '->' : '')+(($('#formIdResHor'+x).val() != '') ? $('#formIdResHor'+x).val() : 'nuevo' )+'|||'+$('#formHoraInicio'+x).val()+'|'+$('#formHoraFin'+x).val();
							}
						}
					}
				}


				// --> Validar restricciones de no facturables
				var RestriAnul 	=  new Object();
				hayRegCarAnu 	= $("#listaCargosAnula").find("tr[encabezado=no]").length;
				
				if(hayRegCarAnu > 0 || $("#formEventOcurridos").val() != '' || $("#formConCobraSiAnula").val() != '' || $("#formProCobraSiAnula").val() != '')
				{
					Guardar = validar_campos_obligatorios('formEventOcurridos,formConCobraSiAnula,formProCobraSiAnula', Guardar);
					if(Guardar && hayRegCarAnu > 0)
					{
						RestriAnul.id 						= $('#formIdResAnu').val();
						RestriAnul.eventOcurridos 			= $('#formEventOcurridos').val();
						
						RestriAnul.codConAnula				= new Object();
						RestriAnul.codProAnula				= new Object();
						RestriAnul.codCcoAnula				= new Object();
						
						$("#listaCargosAnula").find("tr[encabezado=no]").each(function(index){
							RestriAnul.codConAnula[index] = $(this).attr("codConAn");
							RestriAnul.codProAnula[index] = $(this).attr("codProAn");
							RestriAnul.codCcoAnula[index] = $(this).attr("codCcoAn");
						});
						
						RestriAnul.facturableCargoGrabado	= $('#formFacturableCargoGrabado').val();
						RestriAnul.conCobraSiAnula			= $('#formConCobraSiAnula').attr("valor");
						RestriAnul.proCobraSiAnula 			= $('#formProCobraSiAnula').attr("valor");
						RestriAnul.facturableCargoNuevo 	= $('#formFacturableCargoNuevo').val();
						RestriAnul.validarSiEsMayor 		= $('#formValidarSiEsMayor').val();
					}
				}				
				
				// --> No facturables por semanas de gestacion
				if($("#formSemIniGestacion").val() != '' || $("#formSemFinGestacion").val() != '')
				{
					Guardar = validar_campos_obligatorios('formSemIniGestacion,formSemFinGestacion,formFormularioHce,formCampoHce', Guardar);
					if(Guardar)
					{
						if(!('id' in RestriAnul))
							RestriAnul.id 					= $('#formIdResAnu').val();

						RestriAnul.formSemIniGestacion		= $('#formSemIniGestacion').val();
						RestriAnul.formSemFinGestacion		= $('#formSemFinGestacion').val();
						RestriAnul.formFormularioHce		= $('#formFormularioHce').val();
						RestriAnul.formCampoHce				= $('#formCampoHce').val();
					}
				}

				RestriAnul = JSON.stringify(RestriAnul);

				// --> Recargo nocturno
				if($("#RecarNocPorcentaje").attr('checked') == 'checked')
				{
					var formPorcentajeRecNoc	= $("#formPorcentajeRecNoc").val();
					var horaIniReNoc			= $("#horaIniReNoc").val();
					var horaFinReNoc			= $("#horaFinReNoc").val();
					var formCodConRecNoc 		= '';
					var formCodProRecNoc 		= '';
				}
				else
				{
					var formPorcentajeRecNoc	= "";
					var formCodConRecNoc		= (($('#formCodConRecNoc').val() != $('#formCodConRecNoc').attr('msgError')) ? $('#formCodConRecNoc').attr('valor') : '');
					var formCodProRecNoc		= (($('#formCodProRecNoc').val() != $('#formCodProRecNoc').attr('msgError')) ? $('#formCodProRecNoc').attr('valor') : '');
					var horaIniReNoc			= $("#horaIniReNoc").val();
					var horaFinReNoc			= $("#horaFinReNoc").val();
					if(formCodConRecNoc != '')
						Guardar = validar_campos_obligatorios('formCodProRecNoc,horaIniReNoc,horaFinReNoc', Guardar);
				}
				// --> Recargo festivo
				if($("#RecarFesPorcentaje").attr('checked') == 'checked')
				{
					var formPorcentajeRecFes	= $("#formPorcentajeRecFes").val();
					var formCodConRecFest 		= '';
					var formCodProRecFest 		= '';
				}
				else
				{
					var formPorcentajeRecFes	= "";
					var formCodConRecFest			= (($('#formCodConRecFest').val() != $('#formCodConRecFest').attr('msgError')) ? $('#formCodConRecFest').attr('valor') : '');
					var formCodProRecFest			= (($('#formCodProRecFest').val() != $('#formCodProRecFest').attr('msgError')) ? $('#formCodProRecFest').attr('valor') : '');
					if(formCodConRecFest != '')
						Guardar = validar_campos_obligatorios('formCodProRecFest', Guardar);
				}

				// --> Recargo dominical
				if($("#RecarDomPorcentaje").attr('checked') == 'checked')
				{
					var formPorcentajeRecDom	= $("#formPorcentajeRecDom").val();
					var formCodConRecDom 		= '';
					var formCodProRecDom 		= '';
				}
				else
				{
					var formPorcentajeRecDom	= "";
					var formCodConRecDom		= (($('#formCodConRecDom').val() != $('#formCodConRecDom').attr('msgError')) ? $('#formCodConRecDom').attr('valor') : '');
					var formCodProRecDom		= (($('#formCodProRecDom').val() != $('#formCodProRecDom').attr('msgError')) ? $('#formCodProRecDom').attr('valor') : '');
					if(formCodConRecDom != '')
						Guardar = validar_campos_obligatorios('formCodProRecDom', Guardar);
				}

				// --> Cargos adicionales
				var cargosAdicionales = '';
				$("#listaCargosAdicionales tr").each(function(){
					if($(this).attr("encabezado") == undefined)
					{
						var codCon 				= $(this).attr("codCon");
						var codPro 				= $(this).attr("codPro");
						var porCobro 			= $(this).find("[name=porCobroAdi]").val();
						var cargoAdicCant		= $.trim($(this).find("[name=cargoAdicCant]").val());
						var cargoAdicFacturable = $(this).find("select[cargoAdicFacturable]").val();
						var cargoAdicCco 		= $(this).find("select[cargoAdicCco]").val();

						if(porCobro == '')
						{
							$(this).find("[name=porCobroAdi]").addClass('campoObligatorio');
							mostrar_mensaje('<b>Faltan campos obligatorios:</b><br>Porcentaje de cobro para cargos adicionales');
							Guardar = false;
						}
						if(cargoAdicCant == '' || cargoAdicCant == '0')
						{
							$(this).find("[name=cargoAdicCant]").addClass('campoObligatorio');
							mostrar_mensaje('<b>Faltan campos obligatorios:</b><br>Debe definir la cantidad para los cargos adicionales');
							Guardar = false;
						}
						
						cargosAdicionales = cargosAdicionales+((cargosAdicionales == '') ? '' : '<>' )+codCon+"|"+codPro+"|"+porCobro+"|"+cargoAdicCant+"|"+cargoAdicFacturable+"|"+cargoAdicCco;
					}
				});


				// --> Restriccion Pension
				var formPensDiasApartIng  		= (($('#formPensDiasApartIng').val() != $('#formPensDiasApartIng').attr('msgError')) ? $('#formPensDiasApartIng').val() : '');
				var	formPensDiasApartAlt		= (($('#formPensDiasApartAlt').val() != $('#formPensDiasApartAlt').attr('msgError')) ? $('#formPensDiasApartAlt').val() : '');
				var formPensCobro				= $("input[name='tipopension']:checked").val();
				var horaespecificaestancia      = $("#horaespecificaestancia").val();
				var RestringirPension 			= '';
				var idRestriccionPension		=  $("#formIdResPension").val() ;
				var vec_tipohab					= "";
				//--- cambio de cargos ya grabados
				var formModificacionCargos		='';
				$(".CambioCargosPoliticas").each(function (){
					
					//alert($(this).find(".consecutivocon").attr('valor'));
					if($(this).find('select').val() != 'seleccione' && $(this).find(".consecutivocon").attr('valor') != ''  && $(this).find(".consecutivocon").attr('valor') != undefined  &&  $(this).find(".consecutivopro").attr('valor') != '' &&  $(this).find(".consecutivopro").attr('valor') != undefined)
					{ 
						formModificacionCargos += "!"+$(this).find('.selectppal').val()+":"+$(this).find(".consecutivocon").attr('valor')+":"+$(this).find(".consecutivopro").attr('valor')+":"+$(this).find(".selectfacturable").val();
					}
				});
				
				
				
				var formConCobraHab1			= (($('#formConCobraHab1').val() != $('#formConCobraHab1').attr('msgError')) ? $('#formConCobraHab1').attr('valor') : '');
				var formProCobraHab1			= (($('#formProCobraHab1').val() != $('#formProCobraHab1').attr('msgError')) ? $('#formProCobraHab1').attr('valor') : '');
				
				var formConCobraHab2			= (($('#formConCobraHab2').val() != $('#formConCobraHab2').attr('msgError')) ? $('#formConCobraHab2').attr('valor') : '');
				var formProCobraHab2			= (($('#formProCobraHab2').val() != $('#formProCobraHab2').attr('msgError')) ? $('#formProCobraHab2').attr('valor') : '');
				
				//alert(formConCobraHab2)
				
				var formPenstiempominimo		= $('#formPenstiempominimo').val();


				$('[id^=tipoynumdias_]').each(function () {
					if($(this).find('.checkhab').is(':checked') &&  $(this).find('.texthab').val() !='' &&  $(this).find('.texthab').val()!=0)
					{
						vec_tipohab = vec_tipohab + $(this).find('.checkhab').val() + ":" + $(this).find('.texthab').val() + "!!";
					}
				});
				
				if( formProCobraHab2 !='' || formProCobraHab1 !='' || formConCobraHab1 !='' || formConCobraHab2 !='' || formPensDiasApartAlt !='' || formPensDiasApartIng!='' || formPensCobro=='ingreso' || formPensCobro=='ccomayor' || formPensCobro=='cconoche' || vec_tipohab!='' || formPenstiempominimo !='')
					RestringirPension = "si";

				// --> Validar politicas quirúrgicas:
				var polQuirurgicas =  new Object();

				// --> 1.Restriccion de cantidades
				if($("#cantPermFac").val() != '' || $("#diasDespuPermFac").val() != '')
				{
					Guardar = validar_campos_obligatorios('cantPermFac,diasDespuPermFac', Guardar);
					if(Guardar)
					{
						polQuirurgicas.id 					= $('#formIdPolQuirurgicas').val();
						polQuirurgicas.cantPermFac 			= $('#cantPermFac').val();
						polQuirurgicas.diasDespuPermFac 	= $('#diasDespuPermFac').val();
					}
				}
				// --> 2.Restriccion de no acturables
				if($("#conNoFactuMismoDia").val() != '' || $("#proNoFactuMismoDia").val() != '')
				{
					Guardar = validar_campos_obligatorios('conNoFactuMismoDia,proNoFactuMismoDia', Guardar);
					if(Guardar)
					{
						polQuirurgicas.id 					= $('#formIdPolQuirurgicas').val();
						polQuirurgicas.conNoFactuMismoDia 	= $('#conNoFactuMismoDia').attr("valor");
						polQuirurgicas.proNoFactuMismoDia 	= $('#proNoFactuMismoDia').attr("valor");
					}
				}
				// --> 3.Cambio de codigo
				if($("#diasCambioCodigo").val() != '' || $("#conCambioCodigo").val() != '' || $("#proCambioCodigo").val() != '' || $("#proRealizadoCambioCodigo").val() != '')
				{
					Guardar = validar_campos_obligatorios('diasCambioCodigo,conCambioCodigo,proCambioCodigo,proRealizadoCambioCodigo', Guardar);
					if(Guardar)
					{
						polQuirurgicas.id 						= $('#formIdPolQuirurgicas').val();
						polQuirurgicas.diasCambioCodigo 		= $('#diasCambioCodigo').val();
						polQuirurgicas.proRealizadoCambioCodigo = $('#proRealizadoCambioCodigo').attr("valor");
						polQuirurgicas.conCambioCodigo 			= $('#conCambioCodigo').attr("valor");
						polQuirurgicas.proCambioCodigo 			= $('#proCambioCodigo').attr("valor");
					}
				}
				polQuirurgicas = JSON.stringify(polQuirurgicas);
				
				// --> Cobros por meses calendario: 2020-05-11 Jerson
				if(($("#cobrosMesCant").val() != '' && $("#cobrosMesNumMeses").val() == '') || ($("#cobrosMesCant").val() == '' && $("#cobrosMesNumMeses").val() != '')) 
				{
					$("#cobrosMesCant").addClass('campoObligatorio');
					$("#cobrosMesNumMeses").addClass('campoObligatorio');
					mostrar_mensaje('<b> Cobros por meses calendario:</b><br>Si desea registrar la politica los dos campos deben estar llenos. Si no va registar nada, debe dejar los dos vacios');
					Guardar = false;
				}

			}
			// --> Restricciones para los paquetes
			else
			{
				$("#valoresPorcentajesCobro").find("input").each(function(){
					listaPorcentajes+= ((listaPorcentajes == '') ? $(this).val() : '|'+$(this).val());
				});
			}
		}
		
		/*if(RestringirPension=='si')
		{
			$(".CambioCargosPoliticas").each(function (){
				
				
				if($(this).find('select').val() != 'seleccione' && $(this).find(".consecutivocon").attr('valor') != ''  && $(this).find(".consecutivocon").attr('valor') != 'undefined'  &&  $(this).find(".consecutivopro").attr('valor') != '' &&  $(this).find(".consecutivopro").attr('valor') != 'undefined')
				{
					
				}
				else
				{
					$(this).find('select').addClass('campoObligatorio');
					$(this).find(".consecutivocon").addClass('campoObligatorio');
					$(this).find(".consecutivopro").addClass('campoObligatorio');
					mostrar_mensaje('<b>Faltan campos obligatorios:</b><br>Debe ingresar el tipo de habitación');
					Guardar = false;
				}
			})
				
			
		}*/

		if(Guardar)
		{
			// --> Deshabilitar el boton grabar hasta que termine el proceso
			boton = jQuery(Boton);
			boton.html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >').attr("disabled","disabled");

			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'guardar_formulario',
				wemp_pmla:				$('#wemp_pmla').val(),
				formCodigo:				$('#formCodigo').val(),
				formNombre:				$('#formNombre').val(),
				formExplicacion:		$('#formExplicacion').val(),
				formTipoEmpresa:		$('#formTipoEmpresa').attr('valor'),
				formTarifa:				$('#formTarifa').attr('valor'),
				formNitEntidad:			$('#formNitEntidad').attr('valor'),
				formEntidad:			$('#formEntidad').attr('valor'),
				formEspecialidad:		$('#formEspecialidad').attr('valor'),
				formCentroCostos:		$('#formCentroCostos').attr('valor'),
				formCentroCostosPac:	$('#formCentroCostosPac').attr('valor'),
				formTipHab:				formTipHab,
				formTipoPaquete:		formTipoPaquete,
				formConceptoPaquete:	$('#formConceptoPaquete').attr('valor'),
				formProcedimiento:		formProcedimiento,
				RestriDias:				RestriDias,
				RestriRangHoras:		RestriRangHoras,
				RestriAnul:				RestriAnul,
				polQuirurgicas:			polQuirurgicas,
				formCodConRecNoc:		formCodConRecNoc,
				formCodProRecNoc:		formCodProRecNoc,
				formPorcentajeRecNoc:	formPorcentajeRecNoc,
				horaIniReNoc:			horaIniReNoc,
				horaFinReNoc:			horaFinReNoc,
				formCodConRecFest:		formCodConRecFest,
				formCodProRecFest:		formCodProRecFest,
				formPorcentajeRecFes:	formPorcentajeRecFes,
				formCodConRecDom:		formCodConRecDom,
				formCodProRecDom:		formCodProRecDom,
				formPorcentajeRecDom:	formPorcentajeRecDom,
				RestringirPension:		RestringirPension,
				formPensDiasApartIng:   formPensDiasApartIng,
				formPensDiasApartAlt:   formPensDiasApartAlt,
				idRestriccionPension:   idRestriccionPension,
				formPensCobro:			formPensCobro,
				horaespecificaestancia: horaespecificaestancia,
				vec_tipohab:			vec_tipohab,
				formProCobraHab1:		formProCobraHab1,
				formConCobraHab1:		formConCobraHab1,
				cargosAdicionales:		cargosAdicionales,
				formPermitirFacturar:	(($('#PermitirFacSi').attr('checked') == 'checked') ? 'on' : 'off'),
				formManejoTerceros:		$("[name=pedirTercero]:checked").val(),
				formPorcenCobroIncru:	(($("#porcenCobroIncru").val() == '') ? 100 : $("#porcenCobroIncru").val()),
				cobrosMesCant:			$("#cobrosMesCant").val(),
				cobrosMesNumMeses:		$("#cobrosMesNumMeses").val(),
				listaPorcentajes:		listaPorcentajes,
				formPenstiempominimo:	formPenstiempominimo,
				formModificacionCargos: formModificacionCargos
			}
			,function(respuesta){
				if(respuesta)
				{
					ListarPoliticas();
					nueva_politica(respuesta.codigo);
					setTimeout(function(){
						mostrar_mensaje(respuesta.mensaje);
					}, 500);
					// --> Activar boton grabar
					boton.html('Guardar Política').removeAttr("disabled");
				}
			},'json');
		}
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function crear_autocomplete(AgregarOpcTodos, HiddenArray, TipoHidden, CampoCargar, AccionSelect, CampoProcedimiento, ActivarTodosProce)
	{
		//console.log(CampoCargar+"="+AgregarOpcTodos);
		
		if(TipoHidden == 'SI')
			var ArrayValores  = eval('(' + $('#'+HiddenArray).val() + ')');
		else
			var ArrayValores  = eval('(' + HiddenArray + ')');	

		if(AgregarOpcTodos)
			ArrayValores['*'] = 'TODOS';

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
			// --> Si el autocomplete ya existe, lo destruyo
			if( $("#"+value).attr("autocomplete") != undefined )
				$("#"+value).removeAttr("autocomplete");

			// --> Creo el autocomplete
			$( "#"+value ).autocomplete({
				minLength: 	1,
				source: 	ArraySource,
				select: 	function( event, ui ){
					$( "#"+value ).val(ui.item.label);
					$( "#"+value ).attr('valor', ui.item.value);
					$( "#"+value ).attr('nombre', ui.item.nombre);
					switch(AccionSelect)
					{
						case 'CargarProcedimientos':
						{
							crear_autocomplete_procedimientos(CampoProcedimiento, ui.item.value, true, ActivarTodosProce);
							return false;
							break;
						}
						case 'CargarTarifas':
						{
							// --> Limpiar los campos dependientes: tarifas, nit entidades, convenios entidades
							limpiarCapo("formTarifa");
							limpiarCapo("formNitEntidad");
							limpiarCapo("formEntidad");
							traerArrayTarifas(ui.item.value);
							traerArrayNits('%');
							traerArrayEntidades('%');
							return false;
							break;
						}
						case 'CargarNitEntidades':
						{
							// --> Limpiar los campos dependientes: nit entidades, convenios entidades
							limpiarCapo("formNitEntidad");
							limpiarCapo("formEntidad");

							traerArrayNits(ui.item.value);
							traerArrayEntidades('%');

							return false;
							break;
						}
						case 'CargarConveniosEntidades':
						{
							// --> Limpiar los campos dependientes: convenios entidades
							limpiarCapo("formEntidad");
							traerArrayEntidades(ui.item.value);
							return false;
							break;
						}
						case 'agregarTipoHab':
						{
							var duplicado = false;
							$("#listaTiposHab tr").each(function(){
								if($(this).attr("codTipoHab") == ui.item.value)
									duplicado = true;
							});

							if(!duplicado)
							{
								var newTipoHab = 	"<tr codTipoHab='"+ui.item.value+"'><td width='80%'>"
													+"<b>-</b>&nbsp;&nbsp;"+ui.item.value+"-"+ui.item.nombre+"</td>"
													+"<td><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>"
													+"</td></tr>"
								$("#listaTiposHab").append(newTipoHab);
							}
							$("#"+CampoCargar).val("");
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
	//----------------------------------------------------------------------------------
	//	Limpia un input y le recarga la marca de agua
	//----------------------------------------------------------------------------------
	function limpiarCapo(idInput)
	{
		$("#"+idInput).val($("#"+idInput).attr('msgError')).attr('valor', '').attr('nombre', '').addClass('campoRequerido');
		marcarAqua($("#"+idInput), 'msgError', 'campoRequerido' );
		iniciarMarcaAqua($("#"+idInput));
	}
	//-----------------------------------------------------------------------
	//	Funcion que obtiene un array de tarifas dado un tipo de empresa
	//-----------------------------------------------------------------------
	function traerArrayTarifas(tipoEmpresa)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerTarifas',
			wemp_pmla:				$('#wemp_pmla').val(),
			codTipoEmp:				tipoEmpresa
		}
		,function(arrayTarifas){
			crear_autocomplete(true, arrayTarifas, 'NO', 'formTarifa', 'CargarNitEntidades');
		});
	}
	//------------------------------------------------------------------------------
	//	Funcion que obtiene un array de nit's de entidades y carga el autocomplete
	//------------------------------------------------------------------------------
	function traerArrayNits(codTarifa)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerArrayEntidades',
			wemp_pmla:				$('#wemp_pmla').val(),
			codTarifa:				((codTarifa != '*') ? codTarifa : '%'),
			tipoEmpresa:			(($("#formTipoEmpresa").attr("valor") != '*' ? $("#formTipoEmpresa").attr("valor") : '%'))
		}
		,function(arrayNits){
			crear_autocomplete(true, arrayNits, 'NO', 'formNitEntidad', 'CargarConveniosEntidades');
		});
	}
	//----------------------------------------------------------------------------------
	//	Funcion que obtiene un array de convenios de entidades y carga el autocomplete
	//----------------------------------------------------------------------------------
	function traerArrayEntidades(codNitEnt)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'ObtenerArrayConveniosEntidades',
			wemp_pmla:				$('#wemp_pmla').val(),
			codNitEnt:				((codNitEnt != '*') ? codNitEnt : '%'),
			codTarifa:				(($("#formTarifa").attr("valor") != '*' && $("#formTarifa").attr("valor") != '' ? $("#formTarifa").attr("valor") : '%')),
			tipoEmpresa:			(($("#formTipoEmpresa").attr("valor") != '*' ? $("#formTipoEmpresa").attr("valor") : '%'))
		}
		,function(arrayRespuesta){
			crear_autocomplete(true, arrayRespuesta, 'NO', 'formEntidad');
		});
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function ListarPoliticas()
	{
		$("[name=busc_tipo]").each(function(){
			if($(this).attr('checked') == 'checked')
			{
				BuscTipo = $(this).val();
			}
		});

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   	'',
			accion:         	'ListarPoliticas',
			wemp_pmla:			$('#wemp_pmla').val(),
			BuscCodigo:			$('#busc_codigo').val(),
			BuscNombre:			$('#busc_nombre').val(),
			BuscTipo:			BuscTipo,
			BuscCodCP:			$('#busc_ConceptoPaquete').attr('valor'),
			BuscEntidad:		$('#busc_entidad').attr('valor'),
			buscEstado:			$('#buscEstado').val()
		}
		,function(respuesta){
			$("#div_lista").html(respuesta);
			$("#numRegistros").html($("#tabla_lista").attr('numRegistros'));
			ajustarTamañoLista(500);
		});
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function cambiar_estado(CodPolitica)
	{
		if(CodPolitica == 'Nueva')
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   	'',
			accion:         	'CambiarEstado',
			wemp_pmla:			$('#wemp_pmla').val(),
			CodPolitica:		CodPolitica,
			estado:				(($('#EstadoPolitica').attr("value") == 'Activa') ? 'off' : 'on')
		}
		,function(respuesta){
			if(respuesta)
			{
				if($('#EstadoPolitica').attr("value") == 'Inactiva')
				{
					$('#EstadoPolitica').attr('value', 'Activa');
					$('#EstadoPolitica').attr('src', '../../images/medical/sgc/powerOn.png');
				}
				else
				{
					$('#EstadoPolitica').attr('value', 'Inactiva');
					$('#EstadoPolitica').attr('src', '../../images/medical/sgc/powerOff.png');
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
	function nueva_politica(CodPolitica)
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   	'',
			accion:         	'mostrar_formulario',
			wemp_pmla:			$('#wemp_pmla').val(),
			CodigoPolitica:		((!CodPolitica) ? 'Nueva' : CodPolitica)
		}
		,function(respuesta){
			$('#DivFormularioPolitica').html(respuesta);
			$('#configurarPolitica').show('300');
			marcarAqua( '', 'msgError', 'campoRequerido' );
			iniciarMarcaAqua();

			// --> Activar acordeones
			$( "#accordionDatosBasicos" ).accordion({
				collapsible: true,
				heightStyle: "content"
			});
			$( "#accordionResDia" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionResHor" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionResAnu" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionRecargos" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionOtrasRes" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionResVarias" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionQuirurgicas" ).accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			$( "#accordionPorcentajesPaquetes" ).hide().accordion({
				collapsible: true,
				heightStyle: "content",
				active: 1
			});
			
			//---------
			$(".horaespecificaestancia").mask("Hn:Nn:Nn");

			$(".horaespecificaestancia").keyup(function(){

				if ( $(this).val().substring(0,1) == "2" && $(this).val().substring(0,2)*1 > 23 )
				{
					$(this).val( "2_:__:__" );
					$(this).caret(1);
				}
			});
			//----------

			// --> Cargar autocomplete de las especialidades
			crear_autocomplete(true, 'hidden_especialidades', 'SI', 'formEspecialidad');
			// --> Cargar autocomplete de las centros de costos
			crear_autocomplete(true, 'hidden_cco', 'SI', 'formCentroCostos');
			crear_autocomplete(true, 'hidden_cco', 'SI', 'formCentroCostosPac');
			crear_autocomplete(true, 'hidden_cco', 'SI', 'formCodCcoAnula');
			
			// --> Cargar autocomplete de los tipos de empresa
			crear_autocomplete(true, 'hidden_tiposEmpresa', 'SI', 'formTipoEmpresa', 'CargarTarifas');
			// --> Cargar autocomplete de los tipos de habitaciones
			crear_autocomplete(true, 'hidden_tiposHabitaciones', 'SI', 'formTipHab', 'agregarTipoHab');

			if(CodPolitica)
			{
				traerArrayTarifas($("#formTipoEmpresa").attr("valor"));
				traerArrayNits($("#formTarifa").attr("valor"));
				traerArrayEntidades($("#formNitEntidad").attr("formNitEntidad"));
			}
			// --> Cargar autocomplete de los conceptos o paquetes
			if($('#formTipoConcepto').attr('CHECKED') == 'checked')
			{
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConceptoPaquete', 'CargarProcedimientos', 'formProcedimiento', true);
				crear_autocomplete_procedimientos('formProcedimiento', $('#formConceptoPaquete').attr('valor'), true);
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobra1', 'CargarProcedimientos', 'formProCobra1', false);
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobraHoras1', 'CargarProcedimientos', 'formProCobraHoras1', false);

				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formCodConAnula', 'CargarProcedimientos', 'formCodProAnula', true);
				crear_autocomplete_procedimientos('formCodProAnula', '"'+$('#formCodConAnula').attr('valor')+'"', true, true);

				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobraSiAnula', 'CargarProcedimientos', 'formProCobraSiAnula', true);
				crear_autocomplete_procedimientos('formProCobraSiAnula', '"'+$('#formConCobraSiAnula').attr('valor')+'"', true, true);

				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'conNoFactuMismoDia', 'CargarProcedimientos', 'proNoFactuMismoDia', false);
				crear_autocomplete_procedimientos('proNoFactuMismoDia', '"'+$('#conNoFactuMismoDia').attr('valor')+'"', true, false);

				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'conCambioCodigo', 'CargarProcedimientos', 'proCambioCodigo', false);
				crear_autocomplete(false, 'hidden_procedimientos', 'SI', 'proRealizadoCambioCodigo', '', '', false);
				crear_autocomplete_procedimientos('proCambioCodigo', '"'+$('#conCambioCodigo').attr('valor')+'"', true, false);

				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formCodConRecNoc', 'CargarProcedimientos', 'formCodProRecNoc', true);
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formCodConRecFest', 'CargarProcedimientos', 'formCodProRecFest', true);
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formCodConRecDom', 'CargarProcedimientos', 'formCodProRecDom', true);
				
				
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobraHab1', 'CargarProcedimientos', 'formProCobraHab1', false);
				
				
				//crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobraHab2', 'CargarProcedimientos', 'formProCobraHab2', false);
				//procedimiento que crea autocompletar automaticamente segun el numero de politicas de modificacion de cargos en estancia que haya
				consecutivo=2;
				$(".CambioCargosPoliticas").each(function(){
					
					crear_autocomplete(false, 'hidden_conceptos', 'SI', 'formConCobraHab'+consecutivo+'', 'CargarProcedimientos', 'formProCobraHab'+consecutivo+'', false);
					consecutivo ++;
				});
				
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'conAdicional', 'CargarProcedimientos', 'proAdicional', false);
				crear_autocomplete(false, 'hidden_conceptos', 'SI', 'conAdicional', 'CargarProcedimientos', 'proAdicional', false);
				crear_autocomplete_procedimientos('proAdicional', '"'+$('#conAdicional').attr('valor')+'"', true, false);

				//crear_autocomplete_procedimientos('formProCobra1|formCodCobraHoras1|formCodAnula1|formCodCobraSiAnula1|formCodRecNoc|formCodRecFest', '%', false);
			}
			else
			{
				crear_autocomplete(false, 'hidden_paquetes', 'SI', 'formConceptoPaquete');
				// --> ocultar las restricciones
				$("#accordionResDia").hide(200);
				$("#accordionResHor").hide(200);
				$("#accordionResAnu").hide(200);
				$("#accordionRecargos").hide(200);
				$("#accordionOtrasRes").hide(200);
				$("#accordionResVarias").hide(200);
				$("#accordionQuirurgicas").hide(200);
				$( "#accordionPorcentajesPaquetes" ).show();
			}
			// --> Cargar regex
			activar_regex($('#DivFormularioPolitica'));
			limpiar_valor('#DivFormularioPolitica');
			// --> Tooltip
			$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			ajustarTamañoLista(200);
			toogleAcordeonesRestriciones();

			//--> se carga el timepicker (Seleccionador de hora)
			CargarTimepicker('horaIniReNoc');
			CargarTimepicker('horaFinReNoc');
			
				
			
			
		});
	}
	//------------------------------------------------------------------------------------------------------
	//	Funcion que carga un selector de hora en un campo de texto
	// 	Jerson Trujillo.
	//------------------------------------------------------------------------------------------------------
	function CargarTimepicker(Elemento)
	{
		$('#'+Elemento).timepicker({
			showPeriodLabels: false,
			hourText: 'Hora',
			minuteText: 'Minuto',
			amPmText: ['AM', 'PM'],
			closeButtonText: 'Aceptar',
			nowButtonText: 'Ahora',
			deselectButtonText: 'Deseleccionar',
			defaultTime: 'now'
		});
	}
	function limpiar_valor(Contenedor)
	{
		$('.limpiar', Contenedor).blur(function(){
				if ($(this).val() == '' || $(this).val() == $(this).attr('msgError'))
				{
					$(this).attr("valor", "");
				}
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
	function toogle(atributo)
	{
		$('.'+atributo).toggle();
	}
	function EliminarTr(Elemento, TipoRestriccion, Hidden)
	{
		var IdRestricion = $('[prefijo='+Hidden+']', $(Elemento).parent()).val();
		// --> Debo quitar la restriccion visualmente y tambien quitarla en la BD

		if(IdRestricion != '' && IdRestricion != 'nuevo')
		{
			if(confirm('¿Esta seguro que desea eliminar la restricción?'))
			{
				$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:   	'',
					accion:         	'eliminar_restriccion',
					wemp_pmla:			$('#wemp_pmla').val(),
					TipoRestriccion:	TipoRestriccion,
					IdRestricion:		IdRestricion,
					CodigoPolitica:		$('#formCodigo').val()
				}
				,function(respuesta){
					if(respuesta)
					{
						$(Elemento).parent().parent().find("input").each(function(){
							$(this).val('');
							if($(this).attr('valor') != undefined)
								$(this).attr('valor', '');
							if($(this).attr('type') != undefined && $(this).attr('type') == 'hidden')
								$(this).attr('valor', 'nuevo');
						});
					}
				});
			}
		}
		// --> Debo quitar la restriccion solo visualmente
		else
		{
			if(parseInt($(Elemento).parent().parent().attr('id').replace('fila', '')) > 1)
				$(Elemento).parent().parent().remove();
		}
	}
	function cargar_stylo_button()
	{
		$('input[type=button]').addClass("boton");
	}
	function AsignarOnblurInicializarValores(Elementos)
	{
		Elementos = Elementos.split("-");
		$.each( Elementos, function(key, value){
			$('#'+value).attr('OnBlur', 'InicializarValor(this);');
		});
	}
	function InicializarValor(Elemento)
	{
		if($(Elemento).val() == '' || $(Elemento).val() == ' ' || $(Elemento).val() == '   ')
		{
			$(Elemento).attr('valor', '');
		}
	}
	function agregar_tipo_habitacion()
	{

		if ($('#tipo_de_habitacion').val() !='seleccione' && $('#checkbox_'+$('#tipo_de_habitacion').val()).length == 0)
		{
			$("#table_tipos_habitacion").append("<tr id='tipoynumdias_"+$('#tipo_de_habitacion').val()+"'><td colspan='1'><input type='checkbox'  class='checkhab' id='checkbox_"+$('#tipo_de_habitacion').val()+"' checked value='"+$('#tipo_de_habitacion').val()+"'><font size='1'>"+$('#tipo_de_habitacion :selected').text()+"</font></td><td><input type='text' value='' class='texthab' id='text_num_dias_min_"+$('#tipo_de_habitacion').val()+"'></td></tr>");
		}
	}
	function cerrarFormulario()
	{
		$("#configurarPolitica").hide(400);
		ajustarTamañoLista(500);
	}
	//---------------------------------------------------
	// --> Ajustar tamaño de la lista de las politicas.
	//---------------------------------------------------
	function ajustarTamañoLista(tamaño)
	{
		var altura_div = $("#tabla_lista").height();
		if(altura_div > 500)
		{
			$('#div_lista').css(
				{
					'height': tamaño,
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
		else
		{
			$('#div_lista').css(
				{
					'height': altura_div,
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
	}
	//----------------------------------------------------------
	// --> Copia el valor de un input y lo pega en otro input
	//----------------------------------------------------------
	function traerConProEntrada(elemento, inputPegar, inputCopiar)
	{
		if($(elemento).is(":checked"))
		{
			$("#"+inputPegar).val($("#"+inputCopiar).val());
			$("#"+inputPegar).attr("valor", $("#"+inputCopiar).attr("valor"));
			$("#"+inputPegar).attr("nombre", $("#"+inputCopiar).attr("nombre"));
			$("#"+inputPegar).attr("disabled", "disabled");
		}
		else
		{
			$("#"+inputPegar).val("");
			$("#"+inputPegar).attr("valor", "");
			$("#"+inputPegar).attr("nombre", "");
			$("#"+inputPegar).removeAttr("disabled");
		}
	}
	//-----------------------------------------------------------------------------------------------
	// --> Funcion que agrega un concepto-procedimiento, en la politica de cargos adicionales
	//-----------------------------------------------------------------------------------------------
	function agregarCargosAdicionales()
	{
		codCon = $("#conAdicional").attr("valor");
		codPro = $("#proAdicional").attr("valor");

		if(codCon == '' || codPro == '')
			return;

		var duplicado = false;

		if(!duplicado)
		{
			var newCargoAd = 	"<tr codCon='"+codCon+"' codPro='"+codPro+"'>"
								+"<td align='left'>&nbsp;"+codCon+"-"+$("#conAdicional").attr("nombre")+"</td>"
								+"<td align='left'>&nbsp;"+codPro+"-"+$("#proAdicional").attr("nombre")+"</td>"
								+"<td align='center'>&nbsp;<input type='text' name='porCobroAdi' class='entero' size='5'></td>"
								+"<td align='center'>&nbsp;<input type='text' name='cargoAdicCant' class='entero' size='3'></td>"
								+"<td align='center'>&nbsp;<select cargoAdicFacturable=''><option value='S'>Si</option><option value='N'>No</option></select></td>"
								+"<td align='center'>&nbsp;<select cargoAdicCco=''><option value='PAC'>Act. Paciente</option><option value='ORI'>Orig. Cargo</option></select></td>"
								+"<td align='center' width='2%'><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>"
								+"</td></tr>"
			$("#listaCargosAdicionales").append(newCargoAd).show();
		}
		activar_regex($('#listaCargosAdicionales'));

		$("#conAdicional").attr("valor", "").val("");
		$("#conAdicional").attr("nombre", "");
		$("#proAdicional").attr("valor", "").val("");
		$("#proAdicional").attr("nombre", "");
	}
	//-----------------------------------------------------------------------------------------------
	// --> Funcion que agrega un concepto-procedimiento-cco, a la lista de cargos que anula
	//-----------------------------------------------------------------------------------------------
	function agregarCargosAnula()
	{
		codCon = $("#formCodConAnula").attr("valor");
		codPro = $("#formCodProAnula").attr("valor");
		codCco = $("#formCodCcoAnula").attr("valor");

		if(codCon == '' || codPro == '' || codCco == '')
			return;
		
		// --> Validar que no exista la combinacion 
		yaExiste = false;
		$("#listaCargosAnula").find("tr[encabezado=no]").each(function(index){
			if(codCon == $(this).attr("codConAn") && codPro == $(this).attr("codProAn") && codCco == $(this).attr("codCcoAn"))
			{
				alert("Combinación ya existe, por favor modifíquela.");
				yaExiste = true;
				return;
			}		
		});
		
		if(yaExiste)
			return;
		
		var newCargoAn = 	"<tr codConAn='"+codCon+"' codProAn='"+codPro+"' codCcoAn='"+codCco+"' encabezado='no'>"
							+"<td align='left'>&nbsp;"+codCon+"-"+$("#formCodConAnula").attr("nombre")+"</td>"
							+"<td align='left'>&nbsp;"+codPro+"-"+$("#formCodProAnula").attr("nombre")+"</td>"
							+"<td align='left'>&nbsp;"+codCco+"-"+$("#formCodCcoAnula").attr("nombre")+"</td>"
							+"<td align='center' width='2%'><img style='cursor:pointer;' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>"
							+"</td></tr>";
							
		$("#listaCargosAnula").append(newCargoAn).show();
		

		$("#formCodConAnula").attr("valor", "").val("");
		$("#formCodConAnula").attr("nombre", "");
		$("#formCodProAnula").attr("valor", "").val("");
		$("#formCodProAnula").attr("nombre", "");
		$("#formCodCcoAnula").attr("valor", "").val("");
		$("#formCodCcoAnula").attr("nombre", "");
	}
	//----------------------------
	// Carga de programa
	//----------------------------
	$(document).ready(function()
	{
		//cargar_stylo_button();
		// --> Cargar autocomplete de las entidades
		crear_autocomplete(false, 'hidden_entidades', 'SI', 'busc_entidad');
		// --> Cargar autocomplete de los conceptos
		$("#busc_Concepto").trigger("click");

		AsignarOnblurInicializarValores ('busc_ConceptoPaquete-busc_entidad');
		ajustarTamañoLista(500);
		$("#numRegistros").html($("#tabla_lista").attr('numRegistros'));
		
		$.mask.definitions['H']='[012]';
		$.mask.definitions['N']='[012345]';
		$.mask.definitions['n']='[0123456789]';

	
	});
//=======================================================================================================================================================
//	F I N  F U N C I O N E S  J A V A S C R I P T
//=======================================================================================================================================================
	</script>


<!--=====================================================================================================================================================================
	E S T I L O S
=====================================================================================================================================================================-->
	<style type="text/css">
		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}

		#tooltip{font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:3px;opacity:1;border-radius: 4px;}
		#tooltip div{margin:0; width:auto;}

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
			padding: 	3px;
			text-align:center;
		}
		.boton{
			-moz-border-radius:				2px;
			-webkit-border-radius:			2px;
			border-radius:					4px;
			border:							1.5px outset #999999;
			padding: 						3px;
			cursor:							pointer;
			background-color: 				#D2E8FF;
			color: 							#000000;
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
		.boton:hover{
			color:							#ffffff;
		}
		.anchoInput{
			width:310px;
		}
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		/*Chrome*/
		::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		input:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}

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
	cargar_hiddens_para_autocomplete();
	echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";

	echo"
	<div align='center'>
		<table width='95%' cellpadding='3' cellspacing='3'>
			<tr align='center'>
				<td align='center'>
					<fieldset align='center' id='' style='padding:15px;width:1200px'>
						<legend class='fieldset'>Lista de politicas</legend>
						<div>";
							contenedor_lista_politicas();
	echo"				</div>
					</fieldset>
					<br><br>
					<fieldset align='center' id='configurarPolitica' style='padding:15px;width:1200px;display:none'>
						<legend class='fieldset'>Configurar politica</legend>
						<div class='DivFormularioPolitica' id='DivFormularioPolitica'>
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
