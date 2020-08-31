<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Frederick Aguirre
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
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");

	$conex 		= obtenerConexionBD("matrix");
	$wbasedato 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activosFijos');
	$wfecha		= date("Y-m-d");
    $whora 		= date("H:i:s");

	include_once("actfij/funciones_activosFijos.php");
	$periodo = traerPeriodoActual();
	$anoActual = $periodo['ano'];
	$mesActual = $periodo['mes'];

//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================


	function listarCuentasCreadas($arr_titulos_cuentas, $arr_grupos_cco, $arr_grupos_activos)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $anoActual, $mesActual;

		$arrayRegistros = array();

		// --> Consultar activos
		$sqlLista = "SELECT Cuetcc as titulo_cuenta, Cuegra as grupo_activo, Cuegrc as grupo_cco, Cuenum as numero_cuenta, b.id
					   FROM ".$wbasedato."_000027, ".$wbasedato."_000028 b
					  WHERE Cueano = '".$anoActual."'
					    AND Cuemes = '".$mesActual."'
						AND Cuetcc = Tcccod
						AND Cueano = Tccano
						AND Cuemes = Tccmes
						AND Cueest = 'on'
						AND Tccest = 'on'
					ORDER BY Cuetcc,Cuegra,Cuegrc
		";
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_assoc($resLista))
		{
			array_push($arrayRegistros, $rowLista);
		}
		echo "<div class='div_lista' style='height: 350px; overflow: auto; background: none repeat scroll 0px 0px transparent;'>";
		echo"<table width='100%' id='tablaListaRegistros'>
				<tr>
					<td colspan='5' align='left' width='50%'><span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>Buscar:&nbsp;&nbsp;</b><input id='buscarActivo' type='text' tipo='obligatorio' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'></td>
				</tr>
				<tr class='encabezadoTabla' align='center'>
					<td>Titulo Cuenta</td><td>Grupo activos</td><td>Grupo<br>Centro de Costos</td><td>Numero cuenta</td>
				</tr>
			";
		$colorFila = 'fila1';


		foreach($arrayRegistros as $posi => &$valoresAct)
		{
			if( array_key_exists($valoresAct['titulo_cuenta'], $arr_titulos_cuentas ) == true && ($arr_titulos_cuentas[$valoresAct['titulo_cuenta']]['cuentafija'] == "on")){
				//Las cuentas fijas no se muestran en esta lista.
				continue;
			}
			$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1');
			$valoresAct['titulo_cuenta_nombre'] = ($valoresAct['titulo_cuenta']);
			if( array_key_exists($valoresAct['titulo_cuenta'], $arr_titulos_cuentas ) == true ){
				$valoresAct['titulo_cuenta_nombre'] = ($arr_titulos_cuentas[$valoresAct['titulo_cuenta']]['nombre']);
			}
			$valoresAct['grupo_activo_nombre'] = "";
			if( array_key_exists($valoresAct['grupo_activo'], $arr_grupos_activos ) == true ){
				$valoresAct['grupo_activo_nombre'] = ($arr_grupos_activos[$valoresAct['grupo_activo']]);
			}
			$valoresAct['grupo_cco_nombre'] = "";
			if( array_key_exists($valoresAct['grupo_cco'], $arr_grupos_cco ) == true ){
				$valoresAct['grupo_cco_nombre'] = ($arr_grupos_cco[$valoresAct['grupo_cco']]);
			}

			$json_valores = json_encode($valoresAct);

			$accionClick = "onClick='mostrarInfoCuenta(".$json_valores.")' class='tooltip ".$colorFila." find' style='cursor:pointer' title='<span style=\"font-weight:normal\">Click para seleccionar</span>'";

			echo "
			<tr ".$accionClick.">
				<td>".$valoresAct['titulo_cuenta_nombre']."</td>
				<td>".$valoresAct['grupo_activo_nombre']."</td>
				<td>".$valoresAct['grupo_cco_nombre']."</td>
				<td>".$valoresAct['numero_cuenta']."</td>
			</tr>
			";
		}

		echo "</table>";
		echo "</div>";
	}

	function listarTitulosCreados($arr_campos_tablas)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $anoActual, $mesActual;

		$arrayRegistros = array();
		$arrayRegistrosHidden = array();

		// --> Consultar activos
		$sqlLista = "SELECT Tcccod as codigo, Tccnom as nombre, Tcctab as tabla, Tcccam as campo, Tccest as estado, Tccfij as cuentafija, a.id, Cuenum as numerocuentafija
					   FROM ".$wbasedato."_000027 a LEFT JOIN ".$wbasedato."_000028 ON (Tcccod = Cuetcc AND Cueano = '".$anoActual."' AND Cuemes = '".$mesActual."' AND Cueest='on')
					  WHERE Tccano = '".$anoActual."'
					    AND Tccmes = '".$mesActual."'
					   GROUP BY Tcccod
					   ORDER BY Tcccod
					  ";

		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_assoc($resLista))
		{
			if( $rowLista['cuentafija'] != "on" )
				$rowLista['numerocuentafija'] = "";

			$rowLista['nombre'] = htmlentities($rowLista['nombre']);

			array_push($arrayRegistros, $rowLista);

			//En el autocomplete de titulos de cuenta, no van las cuentas fijas, porque no se les puede agregar mas numeros de cuentas. Solo tiene una.
			if( $rowLista['cuentafija'] != "on" && $rowLista['estado'] == "on")
				$arrayRegistrosHidden[$rowLista['codigo']] = $rowLista['nombre'];
		}

		echo "<div class='div_lista' style='height: 350px; overflow: auto; background: none repeat scroll 0px 0px transparent;'>";

		echo"<table width='100%' id='tablaListaRegistrosTitulo'>
				<tr>
					<td colspan='5' align='left' width='50%'><span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>Buscar:&nbsp;&nbsp;</b>
					<input id='buscarTituloCuenta' type='text' tipo='obligatorio' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'></td>

				</tr>
				<tr class='encabezadoTabla' align='center'>
					<td>Codigo</td><td>Nombre</td><td>Campo</td><td>Cuenta fija</td><td>Estado</td>
				</tr>
			";
		$colorFila = 'fila1';

		foreach($arrayRegistros as $posi => &$valoresAct)
		{
			$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1');

			$valoresAct['campo_nombre'] = "";
			if( array_key_exists($valoresAct['tabla']."-".$valoresAct['campo'], $arr_campos_tablas ) == true ){
				$valoresAct['campo_nombre'] = $arr_campos_tablas[$valoresAct['tabla']."-".$valoresAct['campo']];
			}
			if( $valoresAct['tabla'] == "000002" ){
				$valoresAct['tipoCampo'] = "fiscal";
			}else{
				$valoresAct['tipoCampo'] = "niif";
			}

			$json_valores = json_encode($valoresAct);

			$accionClick = "onClick='mostrarInfoTituloCuenta(".$json_valores.")' class='tooltip find ".$colorFila." find' style='cursor:pointer' title='<span style=\"font-weight:normal\">Click para seleccionar</span>'";

			echo "
			<tr ".$accionClick.">
				<td>".$valoresAct['codigo']."</td>
				<td>".$valoresAct['nombre']."</td>
				<td>".$valoresAct['campo_nombre']."</td>
				<td>".$valoresAct['cuentafija']."</td>
				<td>".$valoresAct['estado']."</td>
			</tr>
			";
		}
		echo "</table>";
		echo "</div>";

		echo "<input type='hidden' id='hiddenTitulos' value='".json_encode($arrayRegistrosHidden)."'>";
	}

	//---------------------------
	// --> Pinta el Html necesario para hacer una cuenta
	//---------------------------
	function htmlCuenta($arr_grupos_cco, $arr_grupos_activos,$arr_campos_tablas)
	{

		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $op;
		global $anoActual, $mesActual;

		$wbasedatoFac 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		//Cuegra as grupo_activo, Cuegrc as grupo_cco, Tcctab as tabla, Tcccam as campo, Cuenum as numero_cuenta
		echo "
				<fieldset align='center' style='padding:15px;margin:15px; width: 50%'>
					<legend class='fieldset' id='legendCuenta'>Cuenta</legend>
					<table id='tablaCuentas'>
						<tr>
							<td class='fila1' width='15%'>Título cuenta</td>
							<td class='fila2'>
								<input id='titulocuenta' valor='' nombre='' type='text' tipo='obligatorio' placeholder='Seleccione el titulo de cuenta' style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Grupo Activos</td>
							<td class='fila2'>
								<input id='grupoactivos' valor='' nombre='' type='text' tipo='obligatorio' placeholder='Seleccione el grupo de activos' style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Grupo Centro de Costos</td>
							<td class='fila2'>
								<input id='grupocco' valor='' nombre='' type='text' tipo='obligatorio' placeholder='Seleccione el grupo de centros de costos' style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Número de cuenta</td>
							<td class='fila2'>
								<input id='cuenta_numero' type='text' tipo='obligatorio' class='entero' placeholder='Ingrese el número de cuenta' style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td colspan='2' align='center'>
								<input type='button' class='botongrabar' id='botongrabar' value='Grabar' onclick='grabarCuenta()'>
								<input type='button' class='botonactualizar' id='botonactualizar' style='display : none' value='Actualizar' onclick='actualizarCuenta()'>
								<input type='button' class='botonanular' id='botonanular'	 style='display : none' value='Anular' onclick='anularCuenta()'>
								<input type='button' class='botonCancelar' id='botonCancelar'	 style='display :' value='Cancelar' onclick='limpiardatos()'>
							</td>
						</tr>
					</table>
				</fieldset>";

	}

	function htmlTituloCuenta($arr_campos_tablas)
	{

		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $op;
		global $anoActual, $mesActual;

		$wbasedatoFac 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		//Cuegra as grupo_activo, Cuegrc as grupo_cco, Tcctab as tabla, Tcccam as campo, Cuenum as numero_cuenta
		echo "
				<fieldset align='center' style='padding:15px;margin:15px; width: 50%'>
					<legend class='fieldset' id='legendCuenta'>Titulo Cuenta</legend>
					<table id='tablaTituloCuenta'>
						<tr>
							<td class='fila1' width='25%'>Código</td>
							<td class='fila2'>
								<input id='codigotitulo' type='text' disabled style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='25%'>Nombre</td>
							<td class='fila2'>
								<input id='nombretitulo' type='text' tipo='obligatorio' placeholder='Ingrese el nombre de la cuenta' style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='25%'>Tipo campo</td>
							<td class='fila2'>
								<input name='tipocampo' type='radio' tipo='obligatorio' value='on' onclick='cargarListaCampos(\"on\")' checked>NIIF &nbsp;&nbsp;&nbsp;&nbsp;
								<input name='tipocampo' type='radio' tipo='obligatorio' value='off' onclick='cargarListaCampos(\"off\")'>FISCAL
							</td>
						</tr>
						<tr>
							<td class='fila1' width='25%'>Campo Ficha</td>
							<td class='fila2'>
								<input id='campotitulo' valor='' nombre='' type='text' tipo='obligatorio' placeholder='Seleccione el campo' style='border-radius: 4px;border:1px solid #AFAFAF;width:320px'>
							</td>
						</tr>
						<tr>
							<td class='fila1' width='25%'>Estado</td>
							<td class='fila2'>
								<input name='estadotitulo' type='radio' tipo='obligatorio' value='on'>Activo &nbsp;&nbsp;&nbsp;&nbsp;
								<input name='estadotitulo' type='radio' tipo='obligatorio' value='off'>Inactivo
							</td>
						</tr>
						<tr>
							<td class='fila1' width='25%'>Cuenta Fija? </td>
							<td class='fila2'>
								<input name='cuentafijatitulo' type='radio' tipo='obligatorio' onclick='validarCuentaFija(this)' value='on'>Si &nbsp;&nbsp;&nbsp;&nbsp;
								<input name='cuentafijatitulo' type='radio' tipo='obligatorio' onclick='validarCuentaFija(this)' value='off'>No
							</td>
						</tr>
						<tr style='display:none'>
							<td class='fila1' width='25%'>Número<br>Cuenta Fija</td>
							<td class='fila2'>
								<input type='text' name='numerocuentafijatitulo' id='numerocuentafijatitulo' value=''>
							</td>
						</tr>
						<tr>
							<td colspan='2' align='center'>
								<br>
								<input type='button' class='botongrabartitulo' id='botongrabartitulo' value='Grabar' onclick='grabarTituloCuenta()'>
								<input type='button' class='botonactualizartitulo' id='botonactualizartitulo' style='display : none' value='Actualizar' onclick='actualizarTituloCuenta()'>
								<input type='button' class='botonanulartitulo' id='botonanulartitulo'	 style='display : none' value='Anular' onclick='anularTituloCuenta()'>
								<input type='button' class='botonCancelartitulo' id='botonCancelartitulo'	 style='display :' value='Cancelar' onclick='limpiardatosTitulo()'>
							</td>
						</tr>
					</table>
				</fieldset>";

	}

	//----------------------------------------
	// -->  Funcion que graba la cuenta.
	//----------------------------------------
	function grabarCuenta ($titulo_cuenta, $grupo_activos ,$grupo_cco,$numero_cuenta)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;

		$q = "SELECT Tccfij as cuentafija, Tccnom as nombre FROM ".$wbasedato."_000027
			   WHERE Tcccod='".$titulo_cuenta."'
			     AND Tccano = '".$anoActual."'
				 AND Tccmes = '".$mesActual."'";
		$res = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($q):</b><br>".mysql_error());
		if( $res ){
			$row = mysql_fetch_assoc($res);
			if( $row['cuentafija'] == "on" ){
				echo "No se puede crear una cuenta con el Titulo de cuenta: ".$row['nombre']." \nPorque es una cuenta fija.";
				return;
			}
		}


		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		$q = "INSERT ".$wbasedato."_000028 (		Medico 			, 	Fecha_data		, 		Hora_data		, 		Cueano			 , 		Cuemes			 , 		Cuetcc			 ,	 Cuegra	 				, 	 Cuegrc			,   		  Cuenum		,	Cueest		, 	Seguridad			) "
								 ." VALUES (	'".$wbasedato."'	,	'".$wfecha."'	,	'".$whora."'		,	'".$anoActual."'  	 ,	'".$mesActual."' 	 ,	'".$titulo_cuenta."'  , '".$grupo_activos."'  ,	'".$grupo_cco."' 	, 	 '".$numero_cuenta."'	,	'on'		, 'C-".$wbasedato."' 	)";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		echo "OK";
	}

	function grabarTituloCuenta ($nombre, $campo, $cuentafija, $numerocuentafija)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;

		$nombre = utf8_decode($nombre);

		$codigo = "";
		$q = " SELECT MAX( CAST( Tcccod AS UNSIGNED ) ) + 1 "
			."   FROM ".$wbasedato."_000027 ";
		$res = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($q):</b><br>".mysql_error());
		if( $res ){
			$row = mysql_fetch_array($res);
			if( $row[0] != "" )
				$codigo = $row[0];
			else
				$codigo = 1;
		}

		//El codigo debe tener 4 bits siempre
		$codigo = str_pad($codigo, 3, '0', STR_PAD_LEFT); //se ponen los ceros necesarios a la izquierda

		$campo = explode("-",$campo);
		$tabla = $campo[0];
		$campo = $campo[1];

		if( $tabla == '*' && $campo == '*' && $cuentafija == "off" ){
			echo "Si no es una cuenta fija debe asignar un Campo Ficha para el titulo de cuenta";
			return;
		}

		$q = "INSERT ".$wbasedato."_000027 (		Medico 			, 	Fecha_data		, 		Hora_data		,	 Tccano	 		,	 Tccmes	 		,	 Tcccod	 		, 	 Tccnom			, 		Tcctab	,		Tcccam			, 		Tccfij			, 		Tccest		, 	Seguridad			) "
								 ." VALUES (	'".$wbasedato."'	,	'".$wfecha."'	,	'".$whora."'		,'".$anoActual."'  ,  '".$mesActual."'  ,	'".$codigo."'  ,	'".$nombre."' 	, '".$tabla."'	,	'".$campo."'		,	'".$cuentafija."'	, 		'on'		,	'C-".$wbasedato."' 	)";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		if( $cuentafija == "on" ){
			$q = "INSERT ".$wbasedato."_000028 (		Medico 			, 	Fecha_data		, 		Hora_data		,	 Cueano	 		,	 Cuemes	 		, 		Cuetcc			 ,	 Cuegra	 	, 	 Cuegrc		,   		  Cuenum		,	Cueest		, 	Seguridad			) "
									 ." VALUES (	'".$wbasedato."'	,	'".$wfecha."'	,	'".$whora."'		,'".$anoActual."'  ,  '".$mesActual."'  ,	'".$codigo."'  		, '*'  			,	'*' 		, 	 '".$numerocuentafija."'	,	'on'		, 'C-".$wbasedato."' 	)";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		}

		echo "OK";
	}

	function actualizarCuenta($id_registro, $titulo_cuenta, $grupo_activos ,$grupo_cco,$numero_cuenta)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;

		$q = "UPDATE ".$wbasedato."_000028 SET Cuetcc = '".$titulo_cuenta."',
											   Cuegra = '".$grupo_activos."',
											   Cuegrc = '".$grupo_cco."' ,
											   Cuenum = '".$numero_cuenta."'
				WHERE id= ".$id_registro;

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		echo "OK";
	}

	function actualizarTituloCuenta($id_registro, $codigo, $nombre, $campo, $cuentafija, $numerocuentafija, $estado)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;

		$nombre = utf8_decode($nombre);

		$campo = explode("-",$campo);
		$tabla = $campo[0];
		$campo = $campo[1];

		$q = "UPDATE ".$wbasedato."_000027 SET Tccnom = '".$nombre."',
											   Tcctab = '".$tabla."',
											   Tcccam = '".$campo."',
											   Tccfij = '".$cuentafija."',
											   Tccest = '".$estado."'
				WHERE id= ".$id_registro;

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		$q = "DELETE FROM ".$wbasedato."_000028	WHERE Cuetcc='".$codigo."' AND Cuegra = '*' AND Cueano = '".$anoActual."' AND Cuemes = '".$mesActual."' limit 1";
		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		if( $cuentafija == "on" ){
			$q = "INSERT ".$wbasedato."_000028 (		Medico 			, 	Fecha_data		, 		Hora_data			, 		Cueano			 , 		Cuemes			 , 		Cuetcc			 ,	 Cuegra	 	, 	 Cuegrc		,   		  Cuenum		,	Cueest		, 	Seguridad			) "
									 ." VALUES (	'".$wbasedato."'	,	'".$wfecha."'	,	'".$whora."'		,	'".$anoActual."'  		,	'".$mesActual."'  		,	'".$codigo."'  		, '*'  			,	'*' 		, 	 '".$numerocuentafija."'	,	'on'		, 'C-".$wbasedato."' 	)";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		}

		echo "OK";
	}

	function anularCuenta($id_registro)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;

		//Actualizo info del movimiento
		$q = "UPDATE ".$wbasedato."_000028
				 SET Cueest = 'off'
			   WHERE id = '".$id_registro."' ";
		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		echo "OK";
	}

	function anularTituloCuenta($id_registro)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;
		global $anoActual, $mesActual;

		//Actualizo info del movimiento
		$q = "UPDATE ".$wbasedato."_000027
				 SET Tccest = 'off'
			   WHERE id = '".$id_registro."' ";
		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		$q = "UPDATE ".$wbasedato."_000027 a, ".$wbasedato."_000028 b
			  SET Cueest = 'off'
			WHERE Cuetcc = Tcccod
			  AND Cueano = '".$anoActual."'
			  AND Cuemes = '".$mesActual."'
			  AND a.id='".$id_registro."'";
		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		echo "OK";
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

		case 'grabarCuenta':
		{
			grabarCuenta($titulo_cuenta, $grupo_activos ,$grupo_cco,$numero_cuenta);
			break;
		}

		case 'grabarTituloCuenta':
		{
			grabarTituloCuenta($nombre, $campo, $cuentafija, $numerocuentafija);
			break;
		}

		case 'actualizarCuenta':
		{
			actualizarCuenta($id_registro, $titulo_cuenta, $grupo_activos ,$grupo_cco,$numero_cuenta);
			break;
		}

		case 'actualizarTituloCuenta':
		{
			actualizarTituloCuenta($id_registro, $codigo, $nombre, $campo, $cuentafija, $numerocuentafijatitulo, $estado);
			break;
		}

		case 'anularCuenta':
		{
			anularCuenta($id_registro);
			break;
		}

		case 'anularTituloCuenta':
		{
			anularTituloCuenta($id_registro);
			break;
		}

		case 'consultarLista':
		{
				/*Traer los titulos de cuentas*/
				$arr_titulos_cuentas = array();
				$sql = "SELECT Tcccod as codigo, Tccnom as nombre, Tccfij as cuentafija
							   FROM ".$wbasedato."_000027
							  WHERE Tccest = 'on'";
				$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
				while($row = mysql_fetch_array($res))
				{
					foreach($row as $indice => &$valor)
						$valor = htmlentities($valor);
					$arr_titulos_cuentas[$row['codigo']] = array('nombre'=>$row['nombre'], 'cuentafija'=>$row['cuentafija']);
				}

				/*Traer los grupos de centros de costos*/
				$arr_grupos_cco = array();
				$sql = "SELECT Grccod as codigo, Grcdes as nombre
							   FROM ".$wbasedato."_000026
							  WHERE Grcest = 'on'";
				$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
				while($row = mysql_fetch_array($res))
				{
					foreach($row as $indice => &$valor)
						$valor = htmlentities($valor);
					$arr_grupos_cco[$row['codigo']] = $row['nombre'];
				}

				/*Traer los grupos de activos*/
				$arr_grupos_activos = array();
				$sql = "SELECT Grucod as codigo, Grunom as nombre
							   FROM ".$wbasedato."_000008
							  WHERE Gruest = 'on'";
				$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
				while($row = mysql_fetch_array($res))
				{
					foreach($row as $indice => &$valor)
						$valor = htmlentities($valor);
					$arr_grupos_activos[$row['codigo']] = $row['nombre'];
				}

				listarCuentasCreadas($arr_titulos_cuentas, $arr_grupos_cco, $arr_grupos_activos);

			break;
		}

		case 'consultarListaTitulo':
		{
				/*Traer los campos de las tablas*/
				$arr_campos_tablas = array();
				$sql = "SELECT Percod as tabla, Percam as campo, Perdes as campo_nombre
							   FROM ".$wbasedato."_000019
							  WHERE Percod IN ('000002','000003')
								AND Perest = 'on'";
				$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
				while($row = mysql_fetch_array($res))
				{
					foreach($row as $indice => &$valor)
						$valor = htmlentities($valor);
					$arr_campos_tablas[$row['tabla']."-".$row['campo']] = $row['campo_nombre'];
				}
				$arr_campos_tablas["*-*"] = "NO APLICA";
				listarTitulosCreados($arr_campos_tablas);

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

	$(document).ready(function(){
		// --> Activar Acordeones

		$("#accordionFormula").accordion({
			heightStyle: "fill"
		});

		$('#buscarActivo').quicksearch('#tablaListaRegistros .find');
		$('#buscarTituloCuenta').quicksearch('#tablaListaRegistrosTitulo .find');
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		var altura = ($("#accordionFormula").find("div:eq(0)").height()/2)-44;

		$( "#tabs" ).tabs({
			heightStyle: "content"
		});

		$('.entero').keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});

		$("[name=estadotitulo][value=on]").attr("checked",true);
		$("[name=cuentafijatitulo][value=off]").attr("checked",true);

		// --> Cargar autocomplete de campo
		crear_autocomplete("hiddenTitulos", "titulocuenta");
		crear_autocomplete("hiddenCamposNiif", "campotitulo");
		crear_autocomplete("hiddenGrupoActivos", "grupoactivos");
		crear_autocomplete("hiddenGrupoCcos", "grupocco");
	});

	function cargarListaCampos( tipoNiif ){
		$("#campotitulo").val("").attr("valor","");
		if( tipoNiif == "on" ){
			crear_autocomplete("hiddenCamposNiif", "campotitulo");
		}else{
			crear_autocomplete("hiddenCamposFiscal", "campotitulo");
		}
	}

	//-----------------------------------------------------------
	//	--> Cargar autocomplete de campos
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
			ArraySource[index].label  = ArrayValores[CodVal];
			ArraySource[index].nombre = ArrayValores[CodVal];
		}

		try{
			$("#"+CampoCargar).autocomplete( "destroy" );
		}catch(error){

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
					if( $(this).attr("valor") == "" ){
						$(this).val("");
					}else{
						if( idInput != "campotitulo" ) $(this).val($(this).attr("valor")+"-"+$(this).attr("nombre"));
					}
				}
			}
		});
	}

	function grabarCuenta()
	{

		var permitirGuardar = true;
		var mensaje;
		$('#tablaCuentas .campoObligatorio').removeClass('campoObligatorio');


		// --> Validacion de campos obligatorios
		$("#tablaCuentas").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});


		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'grabarCuenta',
				titulo_cuenta:			$("#titulocuenta").attr("valor"),
				grupo_activos:			$("#grupoactivos").attr("valor"),
				grupo_cco:				$("#grupocco").attr("valor"),
				numero_cuenta:			$("#cuenta_numero").val()

			}, function(data){

					if($.trim(data) == "OK"){
						limpiardatos();
						alert("Grabacion Exitosa");
						actualizarLista();
					}
					else{
						alert("Ocurrio un Error\n"+data)
					}

			});
		}
		else
		{
			alert(mensaje);
		}
	}

	function grabarTituloCuenta()
	{

		var permitirGuardar = true;
		var mensaje;
		$('#tablaTituloCuenta .campoObligatorio').removeClass('campoObligatorio');


		// --> Validacion de campos obligatorios
		$("#tablaTituloCuenta").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});

		var estado = $("[name=estadotitulo]:checked").val();
		var cuentafija = $("[name=cuentafijatitulo]:checked").val();

		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'grabarTituloCuenta',
				nombre:					$("#nombretitulo").val(),
				campo:					$("#campotitulo").attr("valor"),
				estado:					estado,
				cuentafija:				cuentafija,
				numerocuentafija:		$("#numerocuentafijatitulo").val()

			}, function(data){

					if($.trim(data) == "OK"){
						limpiardatosTitulo();
						alert("Grabacion Exitosa");
						actualizarListaTitulo();
					}
					else{
						alert("Ocurrio un Error\n"+data)
					}

			});
		}
		else
		{
			alert(mensaje);
		}
	}

	function actualizarCuenta()
	{

		var permitirGuardar = true;
		var mensaje;
		$('#tablaCuentas .campoObligatorio').removeClass('campoObligatorio');


		// --> Validacion de campos obligatorios
		$("#tablaCuentas").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});

		if( $("#registroMostrado").val() == "" ){
			alert("No se puede actualizar, no existe el registro");
			return;
		}


		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'actualizarCuenta',
				titulo_cuenta:			$("#titulocuenta").attr("valor"),
				grupo_activos:			$("#grupoactivos").attr("valor"),
				grupo_cco:				$("#grupocco").attr("valor"),
				numero_cuenta:			$("#cuenta_numero").val(),
				id_registro:			$("#registroMostrado").val()

			}, function(data){

					if($.trim(data) == "OK"){
						limpiardatos();
						alert("Grabacion Exitosa");
						actualizarLista();
					}else{
						alert("Ocurrio un Error\n"+data)
					}

			});
		}
		else
		{
			alert(mensaje);
		}
	}

	function actualizarTituloCuenta()
	{
		var permitirGuardar = true;
		var mensaje;
		$('#tablaTituloCuenta .campoObligatorio').removeClass('campoObligatorio');

		// --> Validacion de campos obligatorios
		$("#tablaTituloCuenta").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});

		if( $("#registroMostradoTitulo").val() == "" ){
			alert("No se puede actualizar, no existe el registro");
			return;
		}
		var estado = $("[name=estadotitulo]:checked").val();
		var cuentafija = $("[name=cuentafijatitulo]:checked").val();

		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'actualizarTituloCuenta',
				codigo:         		$("#codigotitulo").val(),
				nombre:					$("#nombretitulo").val(),
				campo:					$("#campotitulo").attr("valor"),
				estado:					estado,
				cuentafija:				cuentafija,
				numerocuentafijatitulo:	$("#numerocuentafijatitulo").val(),
				id_registro:			$("#registroMostradoTitulo").val()

			}, function(data){
					if($.trim(data) == "OK"){
						limpiardatosTitulo();
						alert("Grabacion Exitosa");
						actualizarListaTitulo();
						actualizarLista();
						crear_autocomplete("hiddenTitulos", "titulocuenta");
					}else{
						alert("Ocurrio un Error\n"+data)
					}
			});
		}
		else
		{
			alert(mensaje);
		}
	}

	function anularCuenta()
	{


		if( $("#registroMostrado").val() == "" ){
			alert("No se puede anular, no existe el registro");
			return;
		}
		if( confirm("¿Desea anular el registro?") == false )
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'anularCuenta',
			id_registro:			$("#registroMostrado").val()

		}, function(data){
			if($.trim(data) == "OK"){
				limpiardatos();
				alert("Exito al anular");
				actualizarLista();
			}
			else{
				alert("Ocurrio un Error \n"+data)
			}
		});
	}

	function anularTituloCuenta()
	{


		if( $("#registroMostradoTitulo").val() == "" ){
			alert("No se puede anular, no existe el registro");
			return;
		}
		if( confirm("¿Desea anular el registro?\nSe borrarán todas las cuentas asociadas a dicho Titulo Cuenta") == false )
			return;

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'anularTituloCuenta',
			id_registro:			$("#registroMostradoTitulo").val()

		}, function(data){
			if($.trim(data) == "OK"){
				limpiardatosTitulo();
				alert("Exito al anular");
				actualizarListaTitulo();
				actualizarLista();
			}
			else{
				alert("Ocurrio un Error \n"+data)
			}
		});
	}

	function actualizarLista()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'consultarLista'

		}, function(data){
			$("#tablaListaRegistros").parent().html(data);
			$("#tablaListaRegistros").find('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		});
	}

	function actualizarListaTitulo()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'consultarListaTitulo'

		}, function(data){
			$("#tablaListaRegistrosTitulo").parent().html(data);
			crear_autocomplete("hiddenTitulos", "titulocuenta");
			$("#tablaListaRegistrosTitulo").find('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		});
	}

	function mostrarInfoCuenta( datos ){
		limpiardatos();
		$("#titulocuenta").attr("valor",datos.titulo_cuenta).val(datos.titulo_cuenta+"-"+datos.titulo_cuenta_nombre);
		$("#grupoactivos").attr("valor",datos.grupo_activo).val(datos.grupo_activo+"-"+datos.grupo_activo_nombre);
		$("#grupocco").attr("valor",datos.grupo_cco).val(datos.grupo_cco+"-"+datos.grupo_cco_nombre);
		$("#campos").attr("valor",datos.tabla+"-"+datos.campo).val(datos.campo_nombre);
		$("#cuenta_numero").val(datos.numero_cuenta);

		$("#botongrabar").hide();
		$("#botonactualizar").show();
		$("#botonanular").show();

		$("#registroMostrado").val( datos.id );

		//Llevar la pantalla hasta el panel de cuenta
		posicion = $("#titulocuenta").offset();
		ejeY = posicion.top;

		$('html, body').animate({
			scrollTop: ejeY+'px',
			scrollLeft: '0px'
		},0);
	}

	function mostrarInfoTituloCuenta( datos ){

		limpiardatosTitulo();

		$("#nombretitulo").val(datos.nombre);
		$("#codigotitulo").val(datos.codigo);
		$("#campotitulo").attr("valor",datos.tabla+"-"+datos.campo).val(datos.campo_nombre);
		if( datos.estado == "on" )
			$("[name=estadotitulo][value=on]").attr("checked",true);
		else
			$("[name=estadotitulo][value=off]").attr("checked",true);

		if( datos.cuentafija == "on" ){
			$("[name=cuentafijatitulo][value=on]").attr("checked",true);
			$("#numerocuentafijatitulo").val(datos.numerocuentafija);
			$("#numerocuentafijatitulo").attr("obl","obligatorio").parent().parent().show();
		}else{
			$("[name=cuentafijatitulo][value=off]").attr("checked",true);
		}

		if( datos.tipoCampo == "niif" ){
			$("[name=tipocampo][value=on]").prop("checked",true);
		}else if( datos.tipoCampo == "fiscal" ){
			$("[name=tipocampo][value=off]").prop("checked",true);
		}

		$("#botongrabartitulo").hide();
		$("#botonactualizartitulo").show();
		$("#botonanulartitulo").show();

		$("#registroMostradoTitulo").val( datos.id );

		//Llevar la pantalla hasta el panel de titulo cuenta
		posicion = $("#nombretitulo").offset();
		ejeY = posicion.top;

		$('html, body').animate({
			scrollTop: ejeY+'px',
			scrollLeft: '0px'
		},0);
	}

	function limpiardatos()
	{
		$("#titulocuenta").attr("valor","").val("");
		$("#grupoactivos").attr("valor","").val("");
		$("#grupocco").attr("valor","").val("");
		$("#campos").attr("valor","").val("");
		$("#cuenta_numero").val("");
		$("#registroMostrado").val("");

		$("#botongrabar").show();
		$("#botonactualizar").hide();
		$("#botonanular").hide();
	}

	function limpiardatosTitulo()
	{
		$("[name=estadotitulo][value=on]").attr("checked",true);
		$("[name=cuentafijatitulo][value=off]").attr("checked",true);

		$("#nombretitulo").val("");
		$("#codigotitulo").val("");
		$("#campotitulo").attr("valor","").val("");
		$("#registroMostradoTitulo").val("");
		$("#numerocuentafijatitulo").attr("obl","").parent().parent().hide();
		$("#numerocuentafijatitulo").val("");

		$("#botongrabartitulo").show();
		$("#botonactualizartitulo").hide();
		$("#botonanulartitulo").hide();
	}

	function validarCuentaFija(obj){
		obj = jQuery(obj);
		if( obj.val() == "on" ){
			$("#numerocuentafijatitulo").attr("obl","obligatorio").parent().parent().show();
			$("#campotitulo").val("").attr("valor","");
			$("#campotitulo").autocomplete( "search", "NO APLICA" );
		}else{
			$("#numerocuentafijatitulo").attr("obl","").parent().parent().hide();
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
	</style>
<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->
	<BODY>
	<?php

	/*Traer los titulos de cuentas*/
	$arr_titulos_cuentas = array();
	$sql = "SELECT Tcccod as codigo, Tccnom as nombre, Tccfij as cuentafija
				   FROM ".$wbasedato."_000027
				  WHERE Tccest = 'on'
				    AND Tccano = '".$anoActual."'
				    AND Tccmes = '".$mesActual."'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		$arr_titulos_cuentas[$row['codigo']] = array('nombre'=>$row['nombre'], 'cuentafija'=>$row['cuentafija']);
	}

	/*Traer los grupos de centros de costos*/
	$arr_grupos_cco = array();
	$sql = "SELECT Grccod as codigo, Grcdes as nombre
				   FROM ".$wbasedato."_000026
				  WHERE Grcest = 'on'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		$arr_grupos_cco[$row['codigo']] = $row['nombre'];
	}

	/*Traer los grupos de activos*/
	$arr_grupos_activos = array();
	$sql = "SELECT Grucod as codigo, Grunom as nombre
				   FROM ".$wbasedato."_000008
				  WHERE Gruest = 'on'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		$arr_grupos_activos[$row['codigo']] = $row['nombre'];
	}

	/*Traer los campos de las tablas*/
	$arr_campos_tablas = array();
	$arr_campos_tablasFiscal = array();
	$sql = "SELECT Percod as tabla, Percam as campo, Perdes as campo_nombre
				   FROM ".$wbasedato."_000019
				  WHERE Percod IN ('000002','000003')
				    AND Perest = 'on'";
	$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
	while($row = mysql_fetch_array($res))
	{
		foreach($row as $indice => &$valor)
			$valor = htmlentities($valor);
		if( $row['tabla'] == '000003' )
			$arr_campos_tablas[$row['tabla']."-".$row['campo']] = $row['campo_nombre'];
		else if( $row['tabla'] == '000002' )
			$arr_campos_tablasFiscal[$row['tabla']."-".$row['campo']] = $row['campo_nombre'];
	}
	$arr_campos_tablas["*-*"] = "NO APLICA";
	$arr_campos_tablasFiscal["*-*"] = "NO APLICA";

	echo "<input type='hidden' id='hiddenCamposNiif' value='".json_encode($arr_campos_tablas)."'>";
	echo "<input type='hidden' id='hiddenCamposFiscal' value='".json_encode($arr_campos_tablasFiscal)."'>";
	echo "<input type='hidden' id='hiddenGrupoCcos' value='".json_encode($arr_grupos_cco)."'>";
	echo "<input type='hidden' id='hiddenGrupoActivos' value='".json_encode($arr_grupos_activos)."'>";
	echo "<input type='hidden' id='registroMostrado' value=''>";
	echo "<input type='hidden' id='registroMostradoTitulo' value=''>";

	/*<div id='buscadorActivos'>
		<fieldset align='center' style='padding:15px;margin:15px'>
			<legend class='fieldset'>Buscar Activo</legend>";
		echo "<div>";
			listarCuentasCreadas($arr_grupos_cco, $arr_grupos_activos,$arr_campos_tablas);
		echo "</div>";
		echo"</fieldset>
	</div>
	<br>*/

	echo"

	<div id='tabs'>

		<div id='operaciones'  style='display:' >
			<ul>
				<li width='20%' ><a href='#tabTitulosCuenta' id='hreftabCuenta'>Títulos de Cuentas</a></li>
				<li width='20%' ><a href='#tabCuenta' id='hreftabCuenta'>Cuentas Formuladas</a></li>
			</ul>
			<div id='tabTitulosCuenta'>";
	echo "		<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset'>Buscar titulos de cuenta</legend>";
	echo "			<div>";

	$arr_campos_tablas = array_merge( $arr_campos_tablas, $arr_campos_tablasFiscal );
					listarTitulosCreados($arr_campos_tablas);
	echo "			</div>";
	echo "		</fieldset>";
				htmlTituloCuenta($arr_grupos_cco, $arr_grupos_activos,$arr_campos_tablas);
	echo "	</div>";
	echo "	<div id='tabCuenta'>";
	echo "		<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset'>Buscar cuentas</legend>";
	echo "			<div>";
					listarCuentasCreadas($arr_titulos_cuentas, $arr_grupos_cco, $arr_grupos_activos);
	echo "			</div>";
	echo "		</fieldset>";
				htmlCuenta($arr_grupos_cco, $arr_grupos_activos,$arr_campos_tablas);
	echo "	</div>";

	echo "</div>

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
