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

	include_once("actfij/funciones_activosFijos.php");
	$periodo = traerPeriodoActual();
	$anoActual = $periodo['ano'];
	$mesActual = $periodo['mes'];


//=====================================================================================================================================================================
//		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================

	function lista_activos()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $anoActual, $mesActual;

		$arrayActivos = array();
		$arrayCodigosRegistros = array();
		$wbasedatoFac = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		$arrEstados = array();
		$sqlEstDep = "SELECT Estcod as cod, Estnom as nom
						FROM ".$wbasedato."_000004";
		$resEstDep = mysql_query($sqlEstDep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstDep):</b><br>".mysql_error());
		while($rowEstados = mysql_fetch_array($resEstDep))
			$arrEstados[$rowEstados['cod']] = $rowEstados['nom'];

		echo "<input type='hidden' id='arrEstados' value='".json_encode($arrEstados)."' />";

		// --> Consultar activos
		$sqlLista = "SELECT Actreg, Actnom, Actpla, Grunom, Actfad, Actest, Actubi
					   FROM ".$wbasedato."_000001 as A, ".$wbasedato."_000008
					  WHERE Actgru = Grucod
					    AND Actact = 'on'
		";
		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_array($resLista))
		{
			// --> Codificar tildes y caracteres especiales
			foreach($rowLista as $indice => &$valor)
				$valor = utf8_encode($valor);

			$rowLista['ccos'] = array();
			$rowLista['nestado'] = "";
			@$rowLista['nestado'] = $arrEstados[$rowLista['Actest']];

			// --> Armar array de lista de activos
			$arrayActivos[$rowLista['Actreg']] = $rowLista;
			array_push($arrayCodigosRegistros, "'".$rowLista['Actreg']."'");
		}


		// --> Array de centros de costos del activo
		$arrayCcoAct = array();

		if( count($arrayCodigosRegistros) > 0 )
		{
			$sqlCcoAct = "SELECT Ccareg as reg, Ccacco as cco, Ccodes as nom, Ccapor as por, Ccaest as estados
							FROM ".$wbasedato."_000017, ".$wbasedatoFac."_000003
						  WHERE Ccareg IN (".implode(",",$arrayCodigosRegistros).")
						     AND Ccaano = '".$anoActual."'
						     AND Ccames = '".$mesActual."'
							 AND Ccacco = Ccocod
			";
			$resCcoAct = mysql_query($sqlCcoAct, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCcoAct):</b><br>".mysql_error());
			while($row = mysql_fetch_array($resCcoAct))
			{
				$row['estados'] = explode( ",",$row['estados'] );
				foreach( $row['estados'] as &$estadox )
					$estadox = $estadox."-".$arrEstados[$estadox];
				$cco = array( "codigo"=>$row['cco'] , "nombre"=>$row['nom'], "por"=>$row['por'], "estados"=>implode(",",$row['estados']) );
				array_push(  $arrayActivos[$row['reg']]["ccos"], $cco );
			}
		}

		echo "<div class='div_lista' style='height: 350px; overflow: auto; background: none repeat scroll 0px 0px transparent;'>";
		echo"<table width='100%' id='tablaListaActivos'>
				<tr>
					<td colspan='4' align='left' width='50%'><span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>Buscar:&nbsp;&nbsp;</b><input id='buscarActivo' type='text' tipo='obligatorio' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'></td>
					<td colspan='4' align='right' width='50%'></td>
				</tr>
				<tr class='encabezadoTabla' align='center'>
					<td>Registro</td><td>Nombre</td><td>Placa</td><td>Grupo</td><td>Fec.Adquisición</td><td>Estado</td><td>Centro de costos</td><td>Ubicación</td>
				</tr>
			";
			$colorFila = 'fila1';

			//--- Por ahora el permiso desd la lista es el de grabar

			$permiso[1]='grabar';
			$permiso = json_encode($permiso);
			//----

			foreach($arrayActivos as $registroAct => $valoresAct)
			{
				$json_cco = "";
				if( isset( $valoresAct['ccos'] ) )
					$json_cco = json_encode( $valoresAct['ccos'] );
				$oculto_cco = "<input type='hidden' class='ccos' value='".$json_cco."' >";

				$mostrarCco = "";
				if( count( $valoresAct['ccos'] ) == 1 ){
					$mostrarCco = $valoresAct['ccos'][0]['codigo']." - ".$valoresAct['ccos'][0]['nombre'];
				}else if( count( $valoresAct['ccos'] ) > 1 ){
					//$mostrarCco = "<input type='button' value='Mostrar todos' onclick='listarCcosEnListaActivos(this,".$json_cco.")' >";
					$mostrarCco = "<img src='../../images/medical/hce/mas2.png' style='cursor:pointer;' onclick='listarCcosEnListaActivos(this,".$json_cco.")' > <span> Detallar</span>";
				}

				$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1');

				$accionClick = "onClick='traerInfoActivo(\"".$valoresAct['Actreg']."\", \"".$valoresAct['Actnom']."\", \"".$valoresAct['Actpla']."\", \"".$valoresAct['Grunom']."\" , \"".$valoresAct['Actfad']."\" , \"".$valoresAct['nestado']."\" , ".$json_cco." , \"".$valoresAct['Actubi']."\" , ".$permiso." )'";
				$accionClickTr = "";
				if( count( $valoresAct['ccos'] ) == 1 ){ //Si hay un solo centro de costos, el onclick es para el tr
					$accionClickTr = $accionClick;
					$accionClick = "";
				}

				echo "
				<tr ".$accionClickTr." class='tooltip ".$colorFila." find' style='cursor:pointer' title='<span style=\"font-weight:normal\">Click para seleccionar</span>'>
					<td ".$accionClick.">".$valoresAct['Actreg']."</td>
					<td ".$accionClick.">".$valoresAct['Actnom']."</td>
					<td ".$accionClick.">".$valoresAct['Actpla']."</td>
					<td ".$accionClick.">".$valoresAct['Grunom']."</td>
					<td ".$accionClick.">".$valoresAct['Actfad']."</td>
					<td ".$accionClick.">".$valoresAct['nestado']."</td>
					<td class='tooltip ".$colorFila." find'>".$mostrarCco."".$oculto_cco."</td>
					<td ".$accionClick.">".$valoresAct['Actubi']."</td>
				</tr>
				";
			}

		echo "</table>";
		echo "</div>";
	}

	//---------------------------
	// --> Pinta el Html necesario para hacer una adicion
	//---------------------------
	function htmlAdicion()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $op;

		$wbasedatoFac 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');


		// --> Consultar centros de costos
		$arrayCco = array();
		$sqlCenCost = "SELECT Ccocod, Ccodes
						 FROM ".$wbasedatoFac."_000003
						WHERE Ccoest = 'on'
		";
		$resCenCost = mysql_query($sqlCenCost, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCenCost):</b><br>".mysql_error());
		while($rowCencost = mysql_fetch_array($resCenCost))
			$arrayCco[$rowCencost['Ccocod']] = utf8_encode($rowCencost['Ccodes']);


		// --> Consultar centros de costos
		$arrayNit = array();
		$sqlNit = "SELECT Pronit, Pronom
						 FROM ".$wbasedatoFac."_000006
						WHERE Proest = 'on'
		";
		$resNit = mysql_query($sqlNit, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCenCost):</b><br>".mysql_error());
		while($rowNit = mysql_fetch_array($resNit))
			$arrayNit[$rowNit['Pronit']] = utf8_encode($rowNit['Pronom']);


		$codImagen = "<img width='15' height='15' src='../../images/medical/sgc/info.png' style='cursor:help;' title='Meses que se le adicionan' tooltip='si' >";

		$html=  convenciones()."
				<br><br>"
				.datos_generales()."
				<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset' id='legendAdicion'>Movimiento</legend>
					<table id='tablaAdicion'>
						<tr>
							<td class='fila1' width='15%'>Año</td><td class='fila2'  width='15%'><input type='text' id='adicionAno' class='input_anio campomovimiento' tipo='obligatorio' placeholder=' ' readonly><img id='calendar' width='22' height='22' src='../../images/medical/sgc/Calendar.png' onclick='calendarioSeleccionarPeriodo(this);' style='cursor:pointer;'></td>
							<td class='fila1' width='15%'>Mes</td><td class='fila2' width='15%' ><input type='text' id='adicionMes' class='input_mes campomovimiento' tipo='obligatorio' placeholder=' ' readonly></td>
							<td class='fila1' width='15%'>Valor de la adición</td><td class='fila2'  width='15%'><input type='text' class='entero campomovimiento' id='adicionvad' tipo='obligatorio' placeholder=' '></td>
						</tr>
						<tr>
							<td class='fila1' width='15%'>Modifica vida util</td>
							<td class='fila2'  width='15%'>Si:
									<input  type='radio' class='campomovimiento' checked='checked' name='vidautil' value='si' onclick='validarVidaUtil(\"si\")'>
									   No:
									<input  type='radio' class='campomovimiento' value='no' name='vidautil' onclick='validarVidaUtil(\"no\")'>
							</td>
										<td  class='fila1 vidalUtil' >Vida util fiscal (meses)".$codImagen."</td><td class='fila2 vidalUtil'  ><input id ='adicionVfiscal' input='text' class='entero campomovimiento' tipo='obligatorio' placeholder=' '></td>
							<td  class='fila1 vidalUtil' >Vida util Niif (meses)".$codImagen."</td><td class='fila2 vidalUtil'   ><input id ='adicionVniif' input='text' class='entero campomovimiento'  tipo='obligatorio' placeholder=' '></td>

						</tr>
						<tr>
							<td class='fila1' width='15%'>Modifica Costo futuro por disposición final</td>
							<td class='fila2'  width='15%'>Si:
									<input  type='radio' class='campomovimiento' checked='checked' name='costofutxdispfinal' value='si' onclick='validarCFPDF(\"si\")'>
									   No:
									<input  type='radio' class='campomovimiento' value='no' name='costofutxdispfinal' onclick='validarCFPDF(\"no\")'>
							</td>
							<td  class='fila1 costofutxdispfinal' >Valor Costo futuro por disposición final</td><td class='fila2 costofutxdispfinal'  ><input id ='adicionCFPDF' input='text' class='entero campomovimiento' tipo='obligatorio' placeholder=''></td>
							<td class='fila1' width='15%'>Nit Proveedor</td><td class='fila2'  width='15%'>

								<select  id='adicionnit' class='campomovimiento' tipo='obligatorio' style='width:170px;font-size: 8pt;'>
									<option value=''>Seleccione...</option>";
									foreach($arrayNit as $codNit => $nomNit)
										$html.="<option value='".$codNit."' >".$codNit."-".$nomNit."</option>";
						$html.="</select>

							</td>
						</tr>
						<tr>

							<td class='fila1'  >Descripción</td><td class='fila2' colspan='1' ><textarea  id='adiciondes' class='campomovimiento' rows='2'  cols='40' ></textarea></td>
						</tr>
						<tr>
							<td colspan='6' align='center'>
								<input type='button' class='botongrabar'	 style='display : none' value='Grabar' onclick='grabarAdicion()'>
								<input type='button' class='botonactualizar' style='display : none' value='Actualizar' onclick='ActualizarAdicion()'>
								<!--<input type='button' class='botonanular'	 style='display : none' value='Anular' onclick='anularAdicion()'>-->
								<input type='button' value='Limpiar' onclick='limpiardatosadicion()'>
							</td>
						</tr>
					</table>
				</fieldset>
				<br>
				<div id='divhistorico' style='display:none;'></div>";
		echo $html;
	}

	function htmlTraslado()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $op;
		global $anoActual, $mesActual;

		$wbasedatoFac 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		// --> Consultar centros de costos
		$arrayCco = array();
		$sqlCenCost = "SELECT Ccocod, Ccodes
						 FROM ".$wbasedatoFac."_000003
						WHERE Ccoest = 'on'
		";
		$resCenCost = mysql_query($sqlCenCost, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCenCost):</b><br>".mysql_error());
		while($rowCencost = mysql_fetch_array($resCenCost))
			$arrayCco[$rowCencost['Ccocod']] = utf8_encode($rowCencost['Ccodes']);

		$html=  convenciones()."
				<br><br>"
				.datos_generales()."
				<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset' id='legendTraslado'>Movimiento</legend>
					<table id='tablaTraslado'>
						<tr>
							<td class='fila1' width='15%'>Año</td><td class='fila2'  width='15%'><input type='text' id='trasladoAno' class='input_anio campomovimiento' tipo='obligatorio' placeholder=' ' readonly><img id='calendar' width='22' height='22' src='../../images/medical/sgc/Calendar.png' onclick='calendarioSeleccionarPeriodo(this);' style='cursor:pointer;'></td>
							<td class='fila1' width='15%'>Mes</td><td class='fila2' width='15%'><input type='text' id='trasladoMes' class='input_mes campomovimiento' tipo='obligatorio' placeholder=' ' readonly></td>
							<td class='fila2'>&nbsp;</td>
						</tr>
						<tr>
							<td class='fila1' width='15%' rowspan='1'>Centro de costos destino</td>
							<td class='fila2'  width='15%'>";
							/*
								<select class='campomovimiento' tipo='obligatorio' style='width:170px;font-size: 8pt;' id='trasladoccd'  onchange='agregarCentroCostos()'>
									<option value=''>Seleccione...</option>";
									foreach($arrayCco as $codCco => $nomCco)
										$html.="<option value='".$codCco."' >".$codCco."-".$nomCco."</option>";
						$html.="</select>*/
						$html.="<button id='botoneditarCentroCostos' onclick='editarCentroCostos()' style='font-family: verdana;font-weight:bold;font-size: 7pt;cursor:pointer'>Editar</button>";
						$html.="</td>
							<td class='fila2' colspan='3' >
								<table id='listaCentrosCostos' style='font-size: 7pt;text-align: left;margin:3px'>
								</table>
							</td>
						</tr>
						<tr>
							<td class='fila1'>Descripción</td><td class='fila2' colspan='2'><textarea class='campomovimiento' id='trasladodes' rows='4'  cols='50'></textarea></td>
						</tr>
						<tr>
							<td colspan='5' align='center'>
											<input type='button' class='botongrabar' 	 style='display : none' value='Grabar' onclick='grabarTraslado()'>
											<input type='button' class='botonactualizar' style='display : none' value='Actualizar' onclick='ActualizarRetiro()'>
											<!--<input type='button' class='botonanular'	 style='display : none' value='Anular' onclick='AnularRetiro()'>-->
											<input type='button' value='Limpiar' onclick='limpiardatostraslado()'>
							</td>
						</tr>
					</table>
				</fieldset>
				<br>
				<div id='divhistorico' style='display:none;'></div>";



		// --> Consultar estados
		$arrayEstados = array();
		$sqlEstados = "SELECT Estcod, Estnom
						 FROM ".$wbasedato."_000004
						WHERE Estest = 'on'
		";
		$resEstados = mysql_query($sqlEstados, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
		while($rowEstados = mysql_fetch_array($resEstados))
			$arrayEstados[$rowEstados['Estcod']] = htmlentities($rowEstados['Estnom']);

			// --> Consultar maestro de centros de costos
		$arrayCco = array();
		$sqlCenCost = "SELECT Ccocod, Ccodes
						 FROM ".$wbasedatoFac."_000003
						WHERE Ccoest = 'on'
		";
		$resCenCost = mysql_query($sqlCenCost, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCenCost):</b><br>".mysql_error());
		while($rowCencost = mysql_fetch_array($resCenCost))
			$arrayCco[$rowCencost['Ccocod']] = htmlentities($rowCencost['Ccodes']);


		echo "<input type='hidden' value='".json_encode( $arrayEstados )."' id='arrayEstados' />";
		echo "<input type='hidden' value='".json_encode( $arrayCco )."' id='arrayCcos' />";

		echo $html;
	}

	function htmlTrasladoCCOS($wactivo){
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $op;
		global $anoActual, $mesActual;

		$wbasedatoFac = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

			// --> Consultar estados
		$arrayEstados = array();
		$sqlEstados = "SELECT Estcod, Estnom
						 FROM ".$wbasedato."_000004
						WHERE Estest = 'on'
		";
		$resEstados = mysql_query($sqlEstados, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
		while($rowEstados = mysql_fetch_array($resEstados))
			$arrayEstados[$rowEstados['Estcod']] = htmlentities($rowEstados['Estnom']);

			// --> Consultar maestro de centros de costos
		$arrayCco = array();
		$sqlCenCost = "SELECT Ccocod, Ccodes
						 FROM ".$wbasedatoFac."_000003
						WHERE Ccoest = 'on'
		";
		$resCenCost = mysql_query($sqlCenCost, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlCenCost):</b><br>".mysql_error());
		while($rowCencost = mysql_fetch_array($resCenCost))
			$arrayCco[$rowCencost['Ccocod']] = htmlentities($rowCencost['Ccodes']);

		// --> Array de centros de costos del activo
		$arrayCcoAct = array();

		$sqlCcoAct = "SELECT Ccacco, Ccodes, Ccapor, Ccaest
						FROM ".$wbasedato."_000017, ".$wbasedatoFac."_000003
					   WHERE Ccareg = '".$wactivo."'
						 AND Ccaano = '".$anoActual."'
						 AND Ccames = '".$mesActual."'
						 AND Ccacco = Ccocod
		";

		//echo $sqlCcoAct;
		$resCcoAct = mysql_query($sqlCcoAct, $conex) or die("<b>ERROR EN QUERY MATRIX($sqlCcoAct):</b><br>".mysql_error());
		while($rowCcoAct = mysql_fetch_array($resCcoAct))
			$arrayCcoAct[$rowCcoAct['Ccacco']] = $rowCcoAct;


		echo "
		<div id='divEditarCentrosDeCostos' style='padding:15px;display:;' align='center' style='font-family: verdana;font-size: 10pt;'>
			<input type='hidden' value='' id='porcentajeTotal' />
			<input type='hidden' value='".$wactivo."' id='activoVentana' />
			<fieldset align='center' id='' style='padding:15px;'>
				<legend class='fieldset'>Traslado centro de costos</legend>
				<table width='100%'>
					<tr class='encabezadoTabla'>
						<td align='center'><b>Centro de costos<br>origen:</b></td>
						<td align='center'><b>Porcentajes</b></td>
						<td align='center'><b>Estados</b></td>
						<td align='center'>
							<b>Centro de costos<br>destino:</b>
						</td>
					</tr>
		";
		foreach($arrayCcoAct as $ccoAct => $ccoValores)
		{
			echo "<tr class='fila2 tr_centrocosto'>
					<td class='td_cco' cco='".$ccoAct."'>".$ccoValores['Ccodes']."</td>
					<td class='td_porcentaje' porcentaje='".$ccoValores['Ccapor']."'>".$ccoValores['Ccapor']."</td>
					<td class='td_estados'>";
			$arr_estadosOri = explode(",",$ccoValores['Ccaest']);
					foreach($arr_estadosOri as $estadosCco)
						echo $estadosCco.' '.$arrayEstados[$estadosCco].', ';

			echo "<input type='hidden' class='hidden_estadosOrigen' value='".json_encode($arr_estadosOri)."' />";
			echo "	</td>
					<td>
						<table class='tabladestinos'>
							<tr class='fila1'><td>Codigo</td><td>%</td><td>Estados</td><td>&nbsp;</td></tr>
							<tr>
								<td>
								<select style='width:170px;font-size: 8pt;' class='ccodestinot' tipo='obligatorio'>
									<option class='find' value=''>Seleccione...</option>";
									foreach($arrayCco as $codCco => $nomCco)
										echo "<option class='find' value='".$codCco."'>".$codCco."-".$nomCco."</option>";
				echo "			</select>
								</td>
								<td><input type='text' value='".$ccoValores['Ccapor']."' class='porcentajedestinot' tipo='obligatorio'></td>
								<td class='fila2'>
									<table style='font-family: verdana;font-size: 7pt;' class='listaDeEstados'>";
								$x = 0;
								foreach($arrayEstados as $codEst => $nomEst)
								{
									echo (($x%2==0) ? "<tr>" : "");
					echo "			<td><input type='checkbox' class='bordeado' value='".$codEst."'>0".($x+1)." ".$nomEst."</td>";
									echo (($x%2==0) ? "" : "</tr>");
									$x++;
								}
					echo "			</table>
								</td>
								<td>
									<img style='cursor:pointer;width:10px;height:10px;' class='adicionarCcoDestino' onclick='agregarCcoTraslado(this);' title='Copiar' src='../../images/medical/root/adicionar2.png'>
									<img style='cursor:pointer;width:10px;height:10px;' class='eliminarCcoDestino' onclick='eliminarCcot(this);' title='Eliminar' src='../../images/medical/eliminar1.png'>
								</td>
							</tr>
						</table>
					</td>
				</tr>";
		}

		echo "	<tr><td colspan='4' align='right'><br><div id='div_mensajes4' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></td></tr>
				<tr>
					<td colspan='4' align='center'>
						<button id='botonAgregarCco' style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='agregarCco()'>Guardar</button>
						<button id='botonCancelarCco' style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='cancelarCco()'>Cancelar</button>
					</td>
				</tr>
				</table>
			</fieldset>
			<br>
		</div>
		";
	}
	//---------------------------
	// --> Pinta el Html necesario para hacer un retiro
	//---------------------------
	function htmlRetiro()
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $op;

		// --> Consultar centros de costos
		$arrayMotivos = array();
		$sql = "SELECT Motcod, Motdes
						 FROM ".$wbasedato."_000021
						WHERE Motest = 'on'
		";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(sql):</b><br>".mysql_error());
		while($rowMotivos = mysql_fetch_array($res))
			$arrayMotivos[$rowMotivos['Motcod']] = utf8_encode($rowMotivos['Motdes']);

		$html=  convenciones()."
				<br><br>".datos_generales()."
				<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset' id='legendRetiro'>Movimiento</legend>
					<table id='tablaRetiro'>
						<tr>
							<td class='fila1' width='15%'>Año</td><td class='fila2'  width='15%'><input type='text' id='RetiroAno' class='input_anio campomovimiento' tipo='obligatorio' placeholder=' ' readonly><img id='calendar' width='22' height='22' src='../../images/medical/sgc/Calendar.png' onclick='calendarioSeleccionarPeriodo(this);' style='cursor:pointer;'></td>
							<td class='fila1' width='15%'>Mes</td><td class='fila2' width='15%' ><input type='text' id='RetiroMes' class='input_mes campomovimiento' tipo='obligatorio' placeholder=' ' readonly></td>
							<td class='fila1' width='15%'>Motivo</td>
							<td class='fila2'  width='15%'>
								<select tipo='obligatorio' style='width:170px;font-size: 8pt;' id='Retiromotivo' class='campomovimiento' >
									<option value=''>Seleccione...</option>";
									foreach($arrayMotivos as $codMot => $nomMot)
										$html.="<option value='".$codMot."' >".$codMot."-".$nomMot."</option>";
						$html.="</select>
							</td>
						</tr>
						<tr>
							<td class='fila1'>Dia de retiro</td><td class='fila2'> <input type='text' class='campomovimiento' tipo='obligatorio' id='retiroFecha' value='' maxlength=2> </td>
							<td class='fila1'>Descripción</td><td class='fila2' colspan='3'><textarea class='campomovimiento' id='Retirodes' rows='4'  cols='50'></textarea></td>
						</tr>
						<tr>
							<td colspan='6' align='center'>
									<input type='button' class='botongrabar' 	 style='display : none' value='Grabar' onclick='grabarRetiro()'>
									<input type='button' class='botonactualizar' style='display : none' value='Actualizar' onclick='ActualizarRetiro()'>
									<!-- <input type='button' class='botonanular'	 style='display : none' value='Anular' onclick='AnularRetiro()'> -->
									<input type='button' value='Limpiar' onclick='limpiardatosRetiro()'>
							</td>
						</tr>
					</table>
				</fieldset>
				<div id='divhistorico' style='display:none;'></div>";

		echo $html;
	}

	//---------------------------
	// --> Pinta el Html necesario para consultar los movimiento historicos
	//---------------------------
	function htmlHistorico ($op, $activo='')
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $anoActual, $mesActual;

		$movimiento = $op;

		switch($movimiento)
		{
			case 'a':
			{
				$movimiento = 'Adicion';
				break;
			}

			case 't' :
			{
				$movimiento = 'Traslado';
				break;
			}
			case 'r' :
			{
				$movimiento = 'Retiro';
				break;
			}
		}

		$arrEstados = array();
		$sqlEstDep = "SELECT Estcod as cod, Estnom as nom
						FROM ".$wbasedato."_000004";
		$resEstDep = mysql_query($sqlEstDep, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstDep):</b><br>".mysql_error());
		while($rowEstados = mysql_fetch_array($resEstDep))
			$arrEstados[$rowEstados['cod']] = $rowEstados['nom'];

		$arrayActivos = array();
		$arrayCodigosRegistros = array();
		$wbasedatoFac = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		$arrCantidadPorActivo = array();

		$andLista = "";
		if( $activo != "" )
			$andLista = "AND Actreg = '".$activo."'";
		// --> Consultar activos
		$sqlLista = "SELECT a.* ,Actnom ,Actreg , Actpla, Grunom, Actfad, Estnom, Actubi
					   FROM ".$wbasedato."_000016 a, ".$wbasedato."_000001, ".$wbasedato."_000008, ".$wbasedato."_000004
					  WHERE Movreg = Actreg
					  ".$andLista."
					    AND Movtip = '".$movimiento."'
						AND Actgru = Grucod
					    AND Actest = Estcod
						AND Movano = Actano
						AND Movmes = Actmes
						AND Actact = 'on'
						";

		$resLista = mysql_query($sqlLista, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlLista):</b><br>".mysql_error());
		while($rowLista = mysql_fetch_assoc($resLista))
		{
			// --> Codificar tildes y caracteres especiales
			foreach($rowLista as $indice => &$valor){
				$valor = utf8_encode($valor);

			}
			$rowLista = array_change_key_case($rowLista, CASE_LOWER); //poner todas las claves en minuscula

			// --> Armar array de lista de activos
			//$arrayActivos[$rowLista['actreg']] = $rowLista;
			array_push( $arrayActivos, $rowLista );
			array_push($arrayCodigosRegistros, "'".$rowLista['actreg']."'");

			//Por cada vez que el activo este en el historico se suma uno a su posicion en el arreglo
			if( array_key_exists( $rowLista['actreg'], $arrCantidadPorActivo ) == false )
				$arrCantidadPorActivo[$rowLista['actreg']] = 0;

			$arrCantidadPorActivo[$rowLista['actreg']]++;
		}

		/*Traer los centros de costos origen*/
		if( count($arrayCodigosRegistros) > 0 ){
			$sql = "SELECT Ccaano as ano, Ccames as mes, Ccareg as reg, Ccacco as cco, Ccodes as nom, Ccapor as por, Ccaest as estados
						   FROM ".$wbasedatoFac."_000003, ".$wbasedato."_000017
						  WHERE Ccareg IN (".implode(",",$arrayCodigosRegistros).")
							AND Ccocod = Ccacco
							";

			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
			while($row = mysql_fetch_assoc($res))
			{
				foreach($arrayActivos as $registroActx => &$valoresActx)
				{
					if( $valoresActx['actreg'] == $row['reg'] ){
						if( array_key_exists( "ccos", $valoresActx ) == false ){
							$valoresActx["ccos"] = array();
						}
						if( $valoresActx['movano'] ==  $row['ano'] && $valoresActx['movmes'] ==  $row['mes'] ){

							$arr_aux = explode( ",",$row['estados'] );
							foreach( $arr_aux as &$estadox )
								$estadox = $arrEstados[$estadox];
							//$cco = array( "codigo"=>$row['cco'] , "nombre"=>$row['nom'] );
							$cco = array( "codigo"=>$row['cco'] , "nombre"=>$row['nom'], "por"=>$row['por'], "estados"=>implode(",",$arr_aux) );
							array_push(  $valoresActx["ccos"], $cco );
						}
					}
				}
			}
		}
		/*FIN traer los centros de costos*/

		foreach($arrayActivos as $numreg => &$valoresActivo)
		{
			if( $valoresActivo['movcom'] != "*" ){
				$sql = "SELECT Ainnco as nombre
						   FROM ".$wbasedato."_000003
						  WHERE Ainreg = '".$valoresActivo['actreg']."'
						    AND Aincom = '".$valoresActivo['movcom']."'
							AND Ainano = '".$valoresActivo['movano']."'
							AND Ainmes = '".$valoresActivo['movmes']."'
							";
				$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
				while( $row = mysql_fetch_array($res) ){
					$valoresActivo['nomcomponente'] = $row['nombre'];
				}
			}

			if( $movimiento == 'Traslado' ){
				$wbasedatoFac = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

				$arrayCco = array();
				$sqlCenCost = "SELECT Ccocod, Ccodes
								 FROM ".$wbasedatoFac."_000003
				";
				$resCenCost = mysql_query($sqlCenCost, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCenCost):</b><br>".mysql_error());
				while($rowCencost = mysql_fetch_array($resCenCost))
					$arrayCco[$rowCencost['Ccocod']] = utf8_encode($rowCencost['Ccodes']);


				$valoresActivo['arr_costos'] = array();
				$q = "SELECT Mcccco as cco, Mccpor as porcentaje, Mccest as estados_origen, Mccccd as ccos_destino, Mccpod as porcentajes_destino, Mccesd as estados_destino
						FROM ".$wbasedato."_000036
					   WHERE Mccidm = '".$valoresActivo['id']."' ";
				$res = mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX($q):</b><br>".mysql_error());
				while( $row = mysql_fetch_assoc($res) ){

					$arr_aux = explode( ",",$row['estados_destino'] );
					foreach( $arr_aux as &$estadoxx )
						$estadoxx = $estadoxx."-".$arrEstados[$estadoxx];

					$row['estados_destino_nombres'] = implode(",",$arr_aux);


					$existe = false;
					foreach( $valoresActivo['arr_costos'] as &$arr_ccos ){
						if( $arr_ccos['cco'] == $row['cco'] ){
							$existe = true;
							$row['estados_destino'] = str_replace(",", "|", $row['estados_destino']);
							$arr_ccos['estados_destino'].= ",".$row['estados_destino'];

							$row['estados_destino_nombres'] = str_replace(",", "|", $row['estados_destino_nombres']);
							$arr_ccos['estados_destino_nombres'].= ",".$row['estados_destino_nombres'];
							$arr_ccos['estados_origen'].= ",".$row['estados_origen'];
							$arr_ccos['nombres_ccos_destino'].= ",".$row['ccos_destino']."-".$arrayCco[$row['ccos_destino']];
							$arr_ccos['porcentajes_destino'].= ",".$row['porcentajes_destino'];
							$arr_ccos['ccos_destino'].= ",".$row['ccos_destino'];
						}
					}
					if( $existe == false ){
						$row['nombre_cco'] = $arrayCco[$row['cco']];
						$row['nombres_ccos_destino'] = $row['ccos_destino']."-".$arrayCco[$row['ccos_destino']];
						$row['estados_destino'] = str_replace(",", "|", $row['estados_destino']);
						$row['estados_destino_nombres'] = str_replace(",", "|", $row['estados_destino_nombres']);
						$row['estados_destino_nombres'] = str_replace(",", "|", $row['estados_destino_nombres']);
						array_push( $valoresActivo['arr_costos'] , $row );
					}
				}
			}

			//Si es mayor que uno, quiere decir que hay movimientos posteriores
			if( $arrCantidadPorActivo[$valoresActivo['actreg']] > 1 ){
				$valoresActivo['posterior'] = 1;
			}
			//Se le resta uno a la cantidad de repeticiones del activo
			$arrCantidadPorActivo[$valoresActivo['actreg']]--;
		}

		$html = "";

		$general="";
		if( $activo == "" )
			$general="_general";


		if( $activo == "" )
			$html= "<div class='div_lista' style='height: 350px; overflow: auto; background: none repeat scroll 0px 0px transparent;'>";

		$html.= "<div id='detalle_historico".$general."'>";

		$msj_m = "";
		if( $movimiento == 'Adicion' )
			$msj_m = "adiciones";
		else if( $movimiento == 'Traslado' )
			$msj_m = "traslados";
		else if( $movimiento == 'Retiro' )
			$msj_m = "retiros";

		if( $activo != "" ){
			$html.="<div style='float:left'>
						<img src='../../images/medical/hce/mas2.png' style='cursor:pointer;' onclick='verHistoricoMovimientos(this)' >
						<span>Ver ".$msj_m." del activo</span>
					</div><br>";
		}

		$display="";
		$namebuscador = "";
		if( $activo != "" ){
			$display = "none";
			$namebuscador = "activo";
		}
		$msj2 = " de todos los activos";
		if( $activo != "" )
			$msj2 = " del activo";

		$html.="
				<fieldset align='center' style='padding:15px;margin:15px;display:".$display.";' id='panel_historico_movimientos".$general."'>
						<legend class='fieldset' >Lista de Movimientos de ".$movimiento."".$msj2."</legend>
				 <table width='70%' id='tablaListaActivosHistorico".$general."'>
					<tr>
						<td colspan='3' align='left' width='50%'><span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>Buscar Movimientos:&nbsp;&nbsp;</b><input id='buscarActivoHistorico".$namebuscador."' type='text' tipo='obligatorio' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'></td>
						<td colspan='1' align='right' width='25%'></td>
						<td colspan='1' align='right' width='25%'></td>
					</tr>
					<tr class='encabezadoTabla' align='center'>
						<td>Año</td><td>Mes</td><td>Registro</td><td>Nombre</td><td>Fecha del Movimiento</td>
					</tr>
				";
			$colorFila = 'fila1';

			//--- Por ahora el permiso desd la lista es el de grabar

			//$permiso[1]='actualizar';
			$permiso[1]='anular';
			$permiso = json_encode($permiso);
			//----

			foreach($arrayActivos as $registroAct => $valoresAct)
			{
				$colorFila = (($colorFila == 'fila1') ? 'fila2' : 'fila1');

				$json_datos = json_encode( $valoresAct );
				$nombremostrar = $valoresAct['actnom'];
				if( isset($valoresAct['nomcomponente']) == true )
					$nombremostrar.= " *".$valoresAct['nomcomponente'];
				$html.="
						<tr onClick='traerInfoActivoHistorico(".$json_datos." , ".$permiso.")' class='tooltip ".$colorFila." find' style='cursor:pointer' title='<span style=\"font-weight:normal\">Click para seleccionar</span>'>
							<td>".$valoresAct['movano']."</td>
							<td>".$valoresAct['movmes']."</td>
							<td>".$valoresAct['actreg']."</td>
							<td>".$nombremostrar."</td>
							<td>".$valoresAct['fecha_data']."</td>
						</tr>
						";
			}

		$html.=
				"</table>
				</fieldset>
				</div>";

		if( $activo == "" )
			$html.= "</div>";


		return $html;
	}
	//---------------------------------------------------
	//	--> Tabla de convenciones para los formularios
	//---------------------------------------------------
	function convenciones()
	{

		global $wfecha;
		global $anoActual;
		global $mesActual;

		echo "<input type='hidden' id='hiddenPeriodoSeleccionado' 	value='".$anoActual."-".$mesActual."'>
			  <input type='hidden' id='hiddenfechaActual' value='".$wfecha."'>";

		echo "
		<div align='right'>
			<table>
				<tr>
					<td style='padding:3px;border: 1px solid #919191;color:#919191; background:#FFFFEE;font-size:8pt'>&nbsp;Campo Obligatorio&nbsp;</td>
					<!--<td style='padding:3px;border: 1px solid #919191;color:#919191; background:#E5F6FF;font-size:8pt'>&nbsp;Campo Calculado&nbsp;</td>-->
					<td style='padding:3px;border: 1px solid #919191;color:#919191; background:#FFFFFF;font-size:8pt'>&nbsp;No Obligatorio&nbsp;</td>
				</tr>
			</table>
		</div>";
	}
	//-------------------------
	//--> Funcion que pinta los componentes de un activo
	//-------------------------
	function pintarComponentes($registroActivo, $nombreActivo)
	{

		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		$infoPer = traerPeriodoActual();

		// --> Consultar componentes del activo
		$arrayCompoActivos = array();
		$sqlInfoCompo =  "
			   SELECT Aincom, Ainnco
				 FROM ".$wbasedato."_000003
				WHERE Ainano = '".$infoPer['ano']."'
				  AND Ainmes = '".$infoPer['mes']."'
				  AND Ainreg = '".$registroActivo."'
				  AND Aincom != '*'
				  AND Ainest = 'on'";

		$resInfoCompo = mysql_query($sqlInfoCompo, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoCompo):</b><br>".mysql_error());
		while($rowInfoCompo = mysql_fetch_array($resInfoCompo))
		{
			$arrayCompoActivos[$rowInfoCompo['Aincom']] = $rowInfoCompo;
			// --> Codificar tildes y caracteres especiales
			foreach($arrayCompoActivos[$rowInfoCompo['Aincom']] as $indice => &$valor)
				$valor = utf8_encode($valor);
		}

		if(count($arrayCompoActivos) > 0)
		{

			echo"<table width='50%' id='tablaDetalleComponentes'>
					<tr>
						<td id='comp_PRINCIPAL' colspan='3' class='tdComponenteSeleccionado' style='cursor:pointer' onclick='traerInfoComponente(\"".$registroActivo."\", \"*\", \"off\" ,this);' >
							<table width='100%'>
								<tr>
									<td >&nbsp;&nbsp;<b>".$nombreActivo."</b></td>
								</tr>
							</table>
						</td>
					</tr>";
				foreach($arrayCompoActivos as $componente => $valoresCompo)
				{

					echo "
					<tr><td width='3%'></td><td width='4%' style='border-left:1px solid #62BBE8;color:#62BBE8;'>----------</td><td id='comp_".$componente."' onClick='traerInfoComponente(\"".$registroActivo."\", \"".$componente."\" , \"".$valoresCompo['Ainnco']."\", this )' class='tdComponente' >&nbsp;&nbsp;".$componente."-".$valoresCompo['Ainnco']."</td></tr>";
				}
			echo"
				</table>";
		}



	}

	function datos_generales()
	{

		echo"<br>
				<input type='hidden' id='componente' value='' />
				<input type='hidden' id='escomponente' value='' />
				<center><table width='100%' ><tr><td width='100%' align ='center' id='divComponente'></td></tr></table></center>
			 <br>
				<fieldset align='center' style='padding:15px;margin:15px'>
				<legend class='fieldset' id='legendAdicion'>Datos Generales</legend>
				<table width='100%'>
					<tr>
						<td class='fila1' width='10%' >Registro</td><td width='15%' id='campReg' class='fila2'></td>
						<td class='fila1' width='10%' >Nombre</td><td width='15%'  id='campNom' class='fila2' ></td>
						<td class='fila1' width='10%' >Placa</td><td width='15%'  id='campPla' class='fila2'></td>
						<td class='fila1' width='10%' >Grupo</td><td width='15%'  id='campGru' class='fila2'></td>
					</tr>
					<tr>
						<td class='fila1' width='10%'>Fec. Adquisición </td><td width='15%' id='campFecAdq' class='fila2'></td>
						<td class='fila1' width='10%'>Estado</td><td width='15%'  id='campEst' class='fila2'></td>
						<td class='fila1' width='10%'>Centro de costo</td><td width='15%'  id='campCco' class='fila2'>
						</td>
						<td class='fila1' width='10%'>Ubicación </td><td width='15%' id='campUbi' class='fila2' ></td>
					</tr>
					<tr>
						<td style = 'display: none' class='fila1 td_componente' width='10%'>Numero Componente</td><td style = 'display: none' width='15%' id='campNumCom' class='fila2 td_componente'></td>
						<td style = 'display: none' class='fila1 td_componente' width='10%'>Nombre Componente</td><td style = 'display: none' width='15%'  id='campNomCom' class='fila2 td_componente'></td>
						<td class='fondoAmarillo td_posterior' style='display:none; font-size:12pt; font-weight:bold;' align='center' colspan=4>Existen movimientos posteriores para este activo</td>
					</tr>
				</table>
				</fieldset>";
	}

	//----------------------------------------
	// -->  Funcion que graba la adicion.
	//----------------------------------------
	function grabarAdicion ($registroActivo ,$ano,$mes,$valoradicion,$nit,$modvidautil,$vidafiscal,$vidaniif,$descripcion,$escomponente='',$componente='',$modCFPDF,$valorCFPDF)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;

		if( $componente == "" )
			$componente = "*";

		if( $escomponente == 'on' ){
			$sql = "SELECT Subcom
					  FROM ".$wbasedato."_000030
					 WHERE Subdes = 'Adición' OR (Subtip='01' and Subcod='04')
					";
			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
			$row = mysql_fetch_array($res);
			if( $row[0] == "off" ){
				echo "La operación no está disponible para componentes";
				exit;
			}
		}

		/************************************/
		$estadoRetirado = "";
		$sql = "SELECT Estcod
				  FROM ".$wbasedato."_000004
				 WHERE Estret = 'on' ";

		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){
			while($row = mysql_fetch_array($res))
				$estadoRetirado = $row['Estcod'];
		}
		$estadosActivo = array();
		$sql = "SELECT Ccaest as estado
				  FROM ".$wbasedato."_000017
				 WHERE Ccareg = '".$registroActivo."'
				   AND Ccaano = '".$ano."'
				   AND Ccames = '".$mes."'
				UNION
				SELECT Actest as estado
				  FROM ".$wbasedato."_000001
				 WHERE Actreg = '".$registroActivo."'
				   AND Actano = '".$ano."'
				   AND Actmes = '".$mes."'
				";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());

		if( $res ){
			$num = mysql_num_rows($res);
			if( $num > 0 ){
				while( $row = mysql_fetch_array($res) )
					$estadosActivo = array_merge($estadosActivo,explode(",",$row[0]));
			}
		}
		if( in_array($estadoRetirado, $estadosActivo ) == true ){
			echo htmlentities("El activo esta retirado, no es posible realizar la accion");
			exit;
		}
		/************************************/

		$sql = "SELECT id
				  FROM ".$wbasedato."_000003
				 WHERE Ainreg = '".$registroActivo."'
				   AND Aincom = '".$componente."'
				   AND Ainano = '".$ano."'
				   AND Ainmes = '".$mes."'
				";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());

		if( $res ){
			$num = mysql_num_rows($res);
			if( $num == 0 ){
				echo "No existe registro del activo ".$registroActivo." para el período ".$ano."-".$mes;
				exit;
			}
		}

		// inserto en tabla de movimientos
		$q = "INSERT ".$wbasedato."_000016 (		Medico 			, 	Fecha_data		, 		Hora_data		,	 Movano	 	, 	 Movmes		, 		Movreg			, 			Movvad		,  		  Movtip		, 	Movmre		,Movnit 		,		Movmvu			, Movvuf			, 	Movvun			, 	Movdes			, 		Moveco 			, 		Movcom			,		Movmcf		,		Movcfd			, 	Movest		, Seguridad) "
								 ." VALUES (	'".$wbasedato."'	,	'".$wfecha."'	,		'".$whora."'	,	'".$ano."'  ,	'".$mes."' 	, '".$registroActivo."'	,	'".$valoradicion."'		, 'Adicion'			, 	'*'			,	'".$nit."'	,	'".$modvidautil."'	, '".$vidafiscal."'	,	'".$vidaniif."'	,	'".$descripcion."'	, '".$escomponente."'	,	'".$componente."'	,	'".$modCFPDF."'	,	'".$valorCFPDF."'	,	'on'		, 'C-".$wbasedato."' 	)";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		if( $componente == '*' ){
			// actualizo info fiscal
			$q = "UPDATE ".$wbasedato."_000002
					 SET Aifmej  = (Aifmej + '".$valoradicion."' ),
						 Aifvut  = (Aifvut + '".$vidafiscal."' ),
						 Aifppd  = (Aifvut + '".$vidafiscal."' )
				   WHERE Aifreg  = '".$registroActivo."' ";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}
		// Actualizacion en tabla Niif
		if($escomponente !='on')
		{
			$q = "UPDATE ".$wbasedato."_000003
					 SET Ainvup  = (Ainvup + '".$vidaniif."' ),
						 Ainmmm  = (Ainmmm +  '".$valoradicion."'),
						 Aincmm  = (Aincmm +  '".$valorCFPDF."'),
						 Ainppd  = (Ainppd +  '".$vidaniif."')
				   WHERE Ainreg  = '".$registroActivo."' ";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}
		else
		{
			$q = "UPDATE ".$wbasedato."_000003
					 SET Ainvup  = (Ainvup + '".$vidaniif."' ),
						 Ainmmm  = (Ainmmm + '".$valoradicion."'),
						 Aincmm  = (Aincmm +  '".$valorCFPDF."'),
						 Ainppd  = (Ainppd +  '".$vidaniif."')
				   WHERE Ainreg  = '".$registroActivo."'
					 AND Aincom  = '".$componente."'";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}
		//echo 1; exit;
		// --> Ejecutar formulas configuradas para la transacción adición
		$infoRespuesta = ejecutarTransaccion($registroActivo.'-'.$componente, $ano.'-'.$mes, 'codProcesoAdicionPPYE');
		if(count($infoRespuesta['Errores']) > 0)
			echo implode("  ", $infoRespuesta['Errores']);
		else
			echo 1;
	}

	function anularAdicion($registroActivo,$valoradicion,$modvidautil,$vidafiscal,$vidaniif,$escomponente,$componente,$registrodemovimiento)
	{

		//Actualizo info del movimiento
		$q = "UPDATE ".$wbasedato."_000016
				 SET Movest = 'off'
			   WHERE Aifreg  = '".$registrodemovimiento."' ";
		// mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());


		//Actualizo info Fiscal
		$q = "UPDATE ".$wbasedato."_000002
				 SET Aifmej  = (Aifmej - '".$valoradicion."' ),
					 Aifvut  = (Aifvut - '".$vidafiscal."' )
			   WHERE Aifreg  = '".$registroActivo."' ";
		// mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());


		// Actualizacion en tabla Niif
		if($escomponente !='on')
		{
			$q = "UPDATE ".$wbasedato."_000003
					 SET Ainvup  = (Ainvup - '".$vidaniif."' ),
						 Ainmmm  = (Ainmmm -  '".$valoradicion."')
				   WHERE Ainreg  = '".$registroActivo."' ";

			// mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}


		// else
		// {
			// $q = "UPDATE ".$wbasedato."_000003
					 // SET Ainvup  = (Ainvup - '".$vidaniif."' ),
						 // Ainmmm  = (Ainmmm -  '".$valoradicion."')
				   // WHERE Ainreg  = '".$registroActivo."'
					 // AND Aincom  = '".$componente."'";

			// mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		// }
		//--------------------
		echo  "1";

	}


	//----------------------------------------
	// -->  Funcion que graba la adicion.
	//----------------------------------------
	function grabarTraslado($registroActivo ,$ano,$mes,$descripcion,$jsonCcos,$escomponente,$componente='')
	{

		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;

		if( $componente == "" )
			$componente = "*";

		if( $escomponente == 'on' ){
			$sql = "SELECT Subcom
					  FROM ".$wbasedato."_000030
					 WHERE Subdes = 'Traslado' OR (Subtip='01' and Subcod='02')
					";
			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlEstados):</b><br>".mysql_error());
			$row = mysql_fetch_array($res);
			if( $row[0] == "off" ){
				echo "La operación no está disponible para componentes";
				exit;
			}
		}

		/************************************/
		$estadoRetirado = "";
		$sql = "SELECT Estcod
				  FROM ".$wbasedato."_000004
				 WHERE Estret = 'on' ";

		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){
			while($row = mysql_fetch_array($res))
				$estadoRetirado = $row['Estcod'];
		}
		$estadosActivo = array();
		$sql = "SELECT Ccaest as estado
				  FROM ".$wbasedato."_000017
				 WHERE Ccareg = '".$registroActivo."'
				   AND Ccaano = '".$ano."'
				   AND Ccames = '".$mes."'
				UNION
				SELECT Actest as estado
				  FROM ".$wbasedato."_000001
				 WHERE Actreg = '".$registroActivo."'
				   AND Actano = '".$ano."'
				   AND Actmes = '".$mes."'
				";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());

		if( $res ){
			$num = mysql_num_rows($res);
			if( $num > 0 ){
				while( $row = mysql_fetch_array($res) )
					$estadosActivo = array_merge($estadosActivo,explode(",",$row[0]));
			}
		}
		if( in_array($estadoRetirado, $estadosActivo ) == true ){
			echo htmlentities("El activo esta retirado, no es posible realizar la accion");
			exit;
		}
		/************************************/

		$sql = "SELECT id
			  FROM ".$wbasedato."_000003
			 WHERE Ainreg = '".$registroActivo."'
			   AND Aincom = '".$componente."'
			   AND Ainano = '".$ano."'
			   AND Ainmes = '".$mes."'
			";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());

		if( $res ){
			$num = mysql_num_rows($res);
			if( $num == 0 ){
				echo "No existe registro del activo ".$registroActivo." para el período ".$ano."-".$mes;
				exit;
			}
		}

		$datos_validos = true;
		$arr_ccos = array();
		//Crear el arreglo para registrar los nuevos centros de costos
		foreach($jsonCcos as $pos=>$datos){
			$arr_ccoDes = $datos['ccos_destino'];
			$arr_porDes = $datos['porcentajes_destino'];
			$arr_estDes = $datos['estados_destino'];

			$i=0;
			foreach( $arr_ccoDes as $ccodes ){
				if( array_key_exists($ccodes, $arr_ccos ) == false ){
					$arr_ccos[ $ccodes ] = array('estados'=>array(), 'porcentaje'=>0);
				}

				$arr_auxest = explode("|",$arr_estDes[$i]);
				foreach($arr_auxest as $estAux ){
					if( in_array( $estAux, $arr_ccos[ $ccodes ]['estados'] ) == false )
						array_push($arr_ccos[ $ccodes ]['estados'], $estAux);
				}

				//$arr_ccos[ $ccodes ]['estados'] = array_merge( $arr_ccos[ $ccodes ]['estados'], explode("|",$arr_estDes[$i]));
				$arr_ccos[ $ccodes ]['porcentaje']+= $arr_porDes[$i]*1;
				$i++;
			}
		}

		$q = "INSERT ".$wbasedato."_000016 (	Medico 		, 	Fecha_data		, 		Hora_data		,	 Movano	 	, 	 Movmes		, 		Movreg			,  Movtip	 ,		Movmre		,			Movcom 		,	 	Movdes			,	Movest	, Seguridad) "
								." VALUES (	'".$wbasedato."',	'".$wfecha."'	,		'".$whora."'	,	'".$ano."'  ,	'".$mes."' 	, '".$registroActivo."'	, 'Traslado' ,		'*'			,	'".$componente."'	,	'".$descripcion."'	,	 'on'		, 'C-".$wbasedato."' 	)";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());

		$id_16 = mysql_insert_id();

		foreach($jsonCcos as $pos=>$datosp){
			$arr_ccoDes = $datosp['ccos_destino'];
			$arr_porDes = $datosp['porcentajes_destino'];
			$arr_estDes = $datosp['estados_destino'];
			$arr_estOri = $datosp['estados_origen'];
			$i=0;
			foreach( $arr_ccoDes as $ccodes ){
				$estados_origen = explode("|",$arr_estOri[$i]);
				$estados_origen = implode(",",$estados_origen);
				$porcentaje_destino = $arr_porDes[$i];
				$estados_destino = explode("|",$arr_estDes[$i]);
				$estados_destino = implode(",",$estados_destino);
				$q = "INSERT ".$wbasedato."_000036 (	Medico 		, 	Fecha_data		, 		Hora_data		,		Mccidm		,	 		Mccreg	 		,	 Mccano	 	, 	 Mccmes		, 			Mcccco		,  		Mccpor	 		   ,  		Mccest	 		,  		Mccccd	 		,  		Mccpod	 				,  		Mccesd	 			,		Seguridad			) "
										." VALUES (	'".$wbasedato."',	'".$wfecha."'	,		'".$whora."'	,	".$id_16."		,'".$registroActivo."'  	,	'".$ano."'  ,	'".$mes."' 	, '".$datosp['cco']."' 	, '".$datosp['porcentaje']."' , '".$estados_origen."' 	, '".$ccodes."' 	, '".$porcentaje_destino."' 	, '".$estados_destino."' 	,	 'C-".$wbasedato."' 	)";

				mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
				$i++;
			}
		}

		$q = "DELETE
				FROM ".$wbasedato."_000017
			   WHERE Ccareg = '".$registroActivo."'
			     AND Ccaano = '".$ano."'
				 AND Ccames = '".$mes."' ";

		mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());


		foreach( $arr_ccos as $cco=>$datoscco ){
			$datoscco['estados'] = implode(",",$datoscco['estados']);
			$q = "INSERT ".$wbasedato."_000017 (	Medico 		, 	Fecha_data		, 		Hora_data		, 		Ccareg			,			Ccaano		,			Ccames		,			Ccacco		,				Ccapor					,						Ccaest			, Seguridad) "
								." VALUES 	   ('".$wbasedato."',	'".$wfecha."'	,		'".$whora."'	, '".$registroActivo."'	,		'".$ano."'		,		'".$mes."'		,		'".$cco."'		,		'".$datoscco['porcentaje']."'	,		'".$datoscco['estados']."'		, 'C-".$wbasedato."' 	)";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}
//echo 1; exit;
		// --> Ejecutar formulas configuradas para la transacción traslado
		//$componente 	= '*';
		$infoRespuesta 	= ejecutarTransaccion($registroActivo.'-'.$componente, $ano.'-'.$mes, 'codProcesoTrasladoPPYE');
		if(count($infoRespuesta['Errores']) > 0)
			echo implode("  ", $infoRespuesta['Errores']);
		else
			echo 1;

	}

	//----------------------------------------
	// -->  Funcion que graba el retiro.
	//----------------------------------------
	function grabarRetiro ($registroActivo,$ano,$mes,$motivo,$descripcion,$escomponente='',$componente='',$retiroFecha)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		global $wfecha;
		global $whora;

		if( $motivo == "" )
			$motivo = '*';

		if( $componente == "" )
			$componente = "*";

		if( $escomponente == 'on' ){
			$sql = "SELECT Subcom
			  FROM ".$wbasedato."_000030
			 WHERE Subdes = 'Retiro' OR (Subtip='01' and Subcod='03')
			";
			$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
			$row = mysql_fetch_array($res);
			if( $row[0] == "off" ){
				echo "La operación no está disponible para componentes";
				exit;
			}
		}

		$fecha_retiro = date("Y-m-d", strtotime($ano."-".$mes."-".$retiroFecha));
		//Si tiene movimientos despues de la fecha indicada no se puede grabar el retiro
		$q = " SELECT Movreg as reg
				 FROM ".$wbasedato."_000016
				WHERE Movreg = '".$registroActivo."'
				  AND Movano = '".$ano."'
				  AND Movmes = '".$mes."'
				  AND Movest = 'on'
				  AND Fecha_data >= '".$fecha_retiro."'
				";
		if($escomponente !='on')
			$q.= " AND Movcom = '".$componente."'";

		$res = mysql_query($q, $conex) or die("Error en el query ".$q);
		$num = mysql_num_rows($res);
		if ($num > 0){
			echo "No es posible retirar el activo porque existen movimientos posteriores a la fecha ".$fecha_retiro;
			exit;
		}

		/************************************/
		$estadoRetirado = "";
		$sql = "SELECT Estcod
				  FROM ".$wbasedato."_000004
				 WHERE Estret = 'on' ";

		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){
			while($row = mysql_fetch_array($res))
				$estadoRetirado = $row['Estcod'];
		}
		$estadosActivo = array();
		$sql = "SELECT Ccaest as estado
				  FROM ".$wbasedato."_000017
				 WHERE Ccareg = '".$registroActivo."'
				   AND Ccaano = '".$ano."'
				   AND Ccames = '".$mes."'
				UNION
				SELECT Actest as estado
				  FROM ".$wbasedato."_000001
				 WHERE Actreg = '".$registroActivo."'
				   AND Actano = '".$ano."'
				   AND Actmes = '".$mes."'
				";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());

		if( $res ){
			$num = mysql_num_rows($res);
			if( $num > 0 ){
				while( $row = mysql_fetch_array($res) )
					$estadosActivo = array_merge($estadosActivo,explode(",",$row[0]));
			}
		}
		if( in_array($estadoRetirado, $estadosActivo ) == true ){
			echo htmlentities("El activo esta retirado, no es posible realizar la accion");
			exit;
		}
		/************************************/

		$sql = "SELECT id
			  FROM ".$wbasedato."_000003
			 WHERE Ainreg = '".$registroActivo."'
			   AND Aincom = '".$componente."'
			   AND Ainano = '".$ano."'
			   AND Ainmes = '".$mes."'
			";
		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());

		if( $res ){
			$num = mysql_num_rows($res);
			if( $num == 0 ){
				echo "No existe registro del activo ".$registroActivo." para el período ".$ano."-".$mes;
				exit;
			}
		}

		$fecha_periodo_actual = date("Y-m-01");
		$fecha_periodo_elegido = $ano."-".$mes."-01";
		$mesesDiff = mesesDiferencia( $fecha_periodo_elegido, $fecha_periodo_actual);
		/*if( $mesesDiff>1 ){
			echo "No puede hacer un retiro para un periodo tan antiguo. $mesesDiff";
			return;
		}*/

		$q = " SELECT Movreg as reg
				 FROM ".$wbasedato."_000016
				WHERE Movreg = '".$registroActivo."'
				  AND Movano = '".$ano."'
				  AND Movmes = '".$mes."'
				  AND Movtip IN ('Retiro')
				  AND Movest = 'on'
				";
		if($escomponente !='on')
			$q.= " AND Movcom = '".$componente."'";

		$res = mysql_query($q, $conex) or die("Error en el query ".$q);
		$num = mysql_num_rows($res);

		$tieneRetiro = false;
		if ($num > 0){
			while( $row = mysql_fetch_assoc($res) ){
				$tieneRetiro = true;
			}
		}

		$estadoRetirado = "";
		$sql = "SELECT Estcod
				  FROM ".$wbasedato."_000004
				 WHERE Estret = 'on' ";

		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		if( $res ){
			while($row = mysql_fetch_array($res))
				$estadoRetirado = $row['Estcod'];
		}


		$mensaje="";
		if( $tieneRetiro == true ){
			$mensaje = "El registro ya tiene un retiro para el periodo ".$ano." - ".$mes;
			echo $mensaje;
			return;
		}else if( $estadoRetirado == "" ){
			$mensaje = "No existe un estado definido para realizar el retiro en el maestro de estados.";
			echo $mensaje;
			return;
		}else{
			$q = "INSERT ".$wbasedato."_000016 (	Medico 		, 	Fecha_data		, 		Hora_data		,	 Movano	 	, 	 Movmes		, 		Movreg			, 			 Movtip		, 		Movdes		, 			Movmre	 ,		Moveco 	 		, 		Movcom	  		,		Movdre	,		Movest	, Seguridad) "
									 ." VALUES (	'".$wbasedato."',	'".$wfecha."'	,		'".$whora."'	,	'".$ano."'  ,	'".$mes."' 	, '".$registroActivo."'	,		'Retiro'	,	'".$descripcion."'	,	'".$motivo."',	'".$escomponente."',	 '".$componente."',	 '".$retiroFecha."'	, 'on'	, 'C-".$wbasedato."' 	)";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}

		//Actualizar estado del activo
		if($escomponente !='on')
		{
			$q = "UPDATE ".$wbasedato."_000001
					SET Actest  = '".$estadoRetirado."'
				  WHERE Actreg  = '".$registroActivo."' ";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());


			$q = "UPDATE ".$wbasedato."_000017
					 SET Ccaest = '".$estadoRetirado."'
				   WHERE Ccareg = '".$registroActivo."'
					 AND Ccaano = '".$ano."'
					 AND Ccames = '".$mes."'";
			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}
		else
		{
			//PENDIENTE RETIRAR UN COMPONENTE
			$q = "UPDATE ".$wbasedato."_000003
					 SET Ainest  = 'off'
				   WHERE Ainreg  = '".$registroActivo."'
					 AND Aincom  = '".$componente."'";

			mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query  ".$q." - ".mysql_error());
		}
//echo 1; exit;
		// --> Ejecutar formulas configuradas para la transaccion retiro
		$infoRespuesta = ejecutarTransaccion($registroActivo.'-'.$componente, $ano.'-'.$mes, 'codProcesoRetiroPPYE');
		if(count($infoRespuesta['Errores']) > 0)
			echo implode("  ", $infoRespuesta['Errores']);
		else
			echo 1;
	}
	//-----------------------------------------
	//-->Funcion que trae los centros de costo de destino
	//----------------------------------------
	function CentrosdeCostosTraslado($ccodestino)
	{
		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;

		//-- Elimina la primera , si la cadena empieza por ,
		if($ccodestino{0} == ',')
		{
			$ccodestino = substr($ccodestino, 1);
		}


		$cco = array();
		$wbasedatoFac = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');

		$sql = "SELECT  Ccodes as nom , Ccocod as cco
				   FROM ".$wbasedatoFac."_000003
				  WHERE Ccocod IN (".$ccodestino.") ";


		$res = mysql_query($sql, $conex) or die("<b>ERROR EN QUERY MATRIX($sql):</b><br>".mysql_error());
		$i=0;
		while($row = mysql_fetch_array($res))
		{
			$cco[$i] =$row['cco']."-".$row['nom'];
			$i++;
		}

		return $cco;

	}

	function mesesDiferencia($date1,$date2){
		$ts1 = strtotime($date1);
		$ts2 = strtotime($date2);

		$year1 = date('Y', $ts1);
		$year2 = date('Y', $ts2);

		$month1 = date('m', $ts1);
		$month2 = date('m', $ts2);

		$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
		return $diff;
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

		case 'grabarAdicion':
		{
			grabarAdicion ($registroActivo ,$ano,$mes,$valoradicion,$nit,$modvidautil,$vidafiscal,$vidaniif,$descripcion,$escomponente,@$componente,$modCFPDF,$valorCFPDF);
			break;
		}

		case 'anularAdicion':
		{
			anularAdicion ($registroActivo,$valoradicion,$modvidautil,$vidafiscal,$vidaniif,$escomponente,$componente,$registrodemovimiento);
			break;
		}

		case 'grabarTraslado' :
		{
			grabarTraslado ($registroActivo ,$ano,$mes,$descripcion,$jsonCcos,$escomponente,@$componente);
			break;
		}
		case 'grabarRetiro' :
		{
			grabarRetiro ($registroActivo ,$ano,$mes,$motivo,$descripcion,$escomponente,@$componente,$retiroFecha);
			break;
		}
		case 'CentrosdeCostosTraslado' :
		{
			$data = CentrosdeCostosTraslado($ccodestino);
			echo json_encode( $data );
			break;
		}
		case 'pintarComponentes' :
		{
			pintarComponentes ($registroActivo ,$nombreActivo);
			break;
		}
		case 'htmlHistorico' :
		{
			$html = htmlHistorico ($op,@$activo);
			echo $html;
			break;
		}
		case 'htmlTrasladoCCOS':
		{
			htmlTrasladoCCOS($registroActivo);
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
	var ActivoSeleccionado="";
	//Variable que contendrá el Array/Objeto con los datos necesarios sobre los centros de costo durante un traslado
	var arr_costosGlobal = new Array();
	var fecha_actual = "<?php echo date("y-m-d") ?>";

	$(document).ready(function() {

		// --> Activar el buscador de texto, para los activos
		$('#buscarActivo').quicksearch('#tablaListaActivos .find');
		flotanteElegirActivo();
		// --> Activar el buscador de texto, para los movimientos historicos
		$('#buscarActivoHistorico').quicksearch('#tablaListaActivosHistorico_general .find');

		// --> Activar tabs jaquery
		$( "#operaciones" ).tabs({
			heightStyle: "content"
		});


		// --> Validar enteros
		$('.entero').keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});

		// --> Tooltip
		$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
		$('[tooltip=si]').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

		$("#operaciones").show();

		//Convierte los campos tipo fecha en datepicker
		$("#retiroFecha").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
	});

	//--------------------------------------------------------
	// --> Traer la informacion basica del activo y muestra en pantalla
	//--------------------------------------------------------
	function traerInfoActivo(Actreg,Actnom,Actpla,Grunom,Actfad,Estnom,arr_ccos,Actubi,permisos)
	{

		limpiardatostraslado();
		limpiardatosRetiro();
		limpiardatosadicion();

		$("#campReg").html(Actreg);
		$("#campNom").html(Actnom);
		$("#campPla").html(Actpla);
		$("#campGru").html(Grunom);
		$("#campFecAdq").html(Actfad);
		$("#campEst").html(Estnom);
		//$("#campCco").html(Ccodes);
		$("#campUbi").html(Actubi);
		$("#wregistro").val(Actreg);
		$("#escomponente").val("off");
		$("#componente").val("");

		$('#ingresoFlotante').hide();

		aplicarpermisos(permisos);

		//Mostrar lista de centros de costos en los datos generales
		var html_lista_ccos = "<table style='font-size: 7pt;text-align: left;margin:3px' width='100%'>";
		html_lista_ccos = "<tr class='fila1' align='center'><td>Centro de costos</td><td>%</td><td>Estados</td></tr>";
		var i=0;
		for( i in arr_ccos ){
			var arrayEstadosCco = arr_ccos[i]['estados'].split(',');
			rowspan 			= arrayEstadosCco.length;

			html_lista_ccos+= 	"<tr id='trPosCco"+i+"'>"
									+"<td rowspan='"+rowspan+"' style='font-size:9px;border: 1px solid #AED0EA;' width='96%' align='left'>"
										+"&nbsp;"+arr_ccos[i].codigo+"-"+arr_ccos[i].nombre+"</td>"
									+"<td rowspan='"+rowspan+"' style='font-size:9px;border: 1px solid #AED0EA;' >"+arr_ccos[i].por+"</td>"
									+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayEstadosCco[0]+"</td>"
								+"</tr>";
								var key=0;
								for(key in arrayEstadosCco){
									if(key > 0)
										html_lista_ccos+="<tr id='trPosCco"+i+"'><td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayEstadosCco[key]+"</td></tr>";
								}


		}
		html_lista_ccos+="</table>";
		$("#campCco").html(html_lista_ccos);

		$("#panel_historico_movimientos").hide(); //ocultar el panel de historico

		// traer info de componentes
		pintarComponentes(Actreg, Actnom);

		ActivoSeleccionado = Actreg;
		actualizarlistahistoricoActivo();

		var $tabs = $('#operaciones').tabs();
		$tabs.tabs('option', 'selected',0);
	}

	//------
	//--> Muestra el registro en sus diferentes componentes
	//-------
	function pintarComponentes(registro, nombre)
	{

		if($("#op").val() !='t')
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'pintarComponentes',
				registroActivo:			registro,
				nombreActivo:			nombre



			}, function(data){

				$("#divComponente").html(data);

			});
		}

	}


	function traerInfoComponente(Registro,componente,nombre,elemento)
	{
		elemento = jQuery(elemento);
		elemento.attr('id');
		$(".tdComponenteSeleccionado").addClass('tdComponente');
		$(".tdComponenteSeleccionado").removeClass('tdComponenteSeleccionado');
		elemento.removeClass('tdComponente');
		elemento.addClass('tdComponenteSeleccionado');
		if(componente == '*')
		{
			$(".td_componente").hide();
			$("#escomponente").val('off');
			$("#componente").val('');
		}
		else
		{
			$(".td_componente").show();
			$("#campNumCom").html(componente);
			$("#campNomCom").html(nombre);
			$("#escomponente").val('on');
			$("#componente").val(componente);
		}
	}


	//------------
	//-->Funcion que trae permisos y habilita los botones de grabar , anular o modificar
	//-------
	function aplicarpermisos(permisos)
	{
		// obtener permisos
		var j =0;
		var permiso ='';
		var grabar =false;
		var actualizar =false;
		var anular =false;

		$(".botongrabar").hide();
		$(".botonactualizar").hide();
		$(".botonanular").hide();

		for(j in permisos)
		{
				permiso = permisos[j]
				switch(permiso) {
					case 'grabar':
					{
						$(".botongrabar").show();
						$(".campomovimiento").attr( 'disabled' , false);
						break;
					}
					case 'actualizar':
					{
						$(".botonactualizar").show();
						break;
					}
					case 'anular':
					{
						$(".botonanular").show();
						$(".campomovimiento").attr( 'disabled' , true);
						break;
					}
				}
				permiso = '';

		}



	}

	function traerInfoActivoHistorico(arr_datos, permisos)
	{
		/*LLEVAR LOS VALORES DEL ENCABEZADO*/
		$("#campReg").html(arr_datos.actreg);
		$("#campNom").html(arr_datos.actnom);
		$("#campPla").html(arr_datos.actpla);
		$("#campGru").html(arr_datos.grunom);
		$("#campFecAdq").html(arr_datos.actfad);
		$("#campEst").html(arr_datos.estnom);
		$("#campUbi").html(arr_datos.actubi);
		$("#wregistro").val(arr_datos.actreg);
		$("#escomponente").val(arr_datos.moveco);
		$("#componente").val(arr_datos.movcom);
		$("#registrodemovimiento").val(arr_datos.id);
		var ccodestino = "";//arr_datos.movccd;

		aplicarpermisos(permisos);

		if( arr_datos.posterior != undefined ){
			$(".td_posterior").show();
		}else{
			$(".td_posterior").hide();
		}

		var arr_ccos = arr_datos.ccos;

		var html_lista_ccos = "<table style='font-size: 7pt;text-align: left;margin:3px' width='100%'>";
		html_lista_ccos = "<tr class='fila1' align='center'><td>Centro de costos</td><td>%</td><td>Estados</td></tr>";
		var i=0;
		for( i in arr_ccos ){
			var arrayEstadosCco = arr_ccos[i]['estados'].split(',');
			rowspan 			= arrayEstadosCco.length;

			html_lista_ccos+= 	"<tr id='trPosCco"+i+"'>"
									+"<td rowspan='"+rowspan+"' style='font-size:9px;border: 1px solid #AED0EA;' width='96%' align='left'>"
										+"&nbsp;"+arr_ccos[i].codigo+"-"+arr_ccos[i].nombre+"</td>"
									+"<td rowspan='"+rowspan+"' style='font-size:9px;border: 1px solid #AED0EA;' >"+arr_ccos[i].por+"</td>"
									+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayEstadosCco[0]+"</td>"
								+"</tr>";
								var key=0;
								for(key in arrayEstadosCco){
									if(key > 0)
										html_lista_ccos+="<tr id='trPosCco"+i+"'><td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayEstadosCco[key]+"</td></tr>";
								}


		}
		html_lista_ccos+="</table>";
		$("#campCco").html(html_lista_ccos);

		/*ACTUALIZAR BOTONES*/

		/*FIN ACTUALIZAR BOTONES*/

		if( arr_datos.movcom != '*' ){
			$(".td_componente").show();
			$("#campNumCom").html( arr_datos.movcom );
			$("#campNomCom").html( arr_datos.nomcomponente );
			$("#escomponente").val('on');
			$("#componente").val( arr_datos.movcom );
		}

		var $tabs = $('#operaciones').tabs();
		$tabs.tabs('option', 'selected',0);

		ActivoSeleccionado = $("#wregistro").val();
		actualizarlistahistoricoActivo();

		/*LLEVAR LOS VALORES DEL MOVIMIENTO*/
		if( $("#op").val() == "r" ){
			$("#RetiroAno").val( arr_datos.movano );
			$("#RetiroMes").val( arr_datos.movmes );
			$("#Retiromotivo").val( arr_datos.movmre );
			$("#Retirodes").val( arr_datos.movdes );
			$("#retiroFecha").val( arr_datos.movdre );
		}else if( $("#op").val() == "a" ){
			$("#adicionAno").val( arr_datos.movano );
			$("#adicionMes").val( arr_datos.movmes);
			$("#adicionvad").val( arr_datos.movvad );
			$("#adicionnit").val( arr_datos.movnit );
			$("input[name='vidautil'][value='"+arr_datos.movmvu+"']").prop("checked",true);
			$("#adicionVfiscal").val( arr_datos.movvuf );
			$("#adicionVniif").val( arr_datos.movvun );
			$("#adiciondes").val( arr_datos.movdes );
		}else if( $("#op").val() == "t" ){
			$("#trasladoAno").val( arr_datos.movano );
			$("#trasladoMes").val( arr_datos.movmes );
			$("#trasladodes").val( arr_datos.movdes );


			arr_costos = arr_datos.arr_costos;

			var ii = 0;
			for( ii = 0; ii<arr_costos.length; ii++ ){
				arr_costos[ii].ccos_destino = (arr_costos[ii].ccos_destino).split(",");
				arr_costos[ii].porcentajes_destino = (arr_costos[ii].porcentajes_destino).split(",");
				arr_costos[ii].estados_destino = (arr_costos[ii].estados_destino).split(",");
				arr_costos[ii].estados_destino_nombres = (arr_costos[ii].estados_destino_nombres).split(",");
				if( arr_costos[ii].nombres_ccos_destino != undefined )
					arr_costos[ii].nombres_ccos_destino = (arr_costos[ii].nombres_ccos_destino).split(",");
			}

			mostrarListaCcosPanel( arr_costos );
			$("#botoneditarCentroCostos").attr("disabled",true);
		}
	}


	//------------------------------------------------
	//--> Graba el movimiento de adicion a activos fijos
	//------------------------------------------------
	function grabarAdicion()
	{

		var permitirGuardar = true;
		var mensaje ;
		$('#tablaAdicion .campoObligatorio').removeClass('campoObligatorio');


		// --> Validacion de campos obligatorios
		$("#tablaAdicion").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});

		// --> validacion de seleccion de activo
		if ($("#wregistro").val() =='')
		{
			permitirGuardar = false;
			mensaje = 'No hay activo seleccionado';
		}


		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'grabarAdicion',
				registroActivo:			$("#wregistro").val(),
				ano:					$("#adicionAno").val(),
				mes:					$("#adicionMes").val(),
				valoradicion:			$("#adicionvad").val(),
				nit:					$("#adicionnit").val(),
				modvidautil:			$("input[name='vidautil']:checked").val(),
				vidafiscal:				$("#adicionVfiscal").val(),
				vidaniif:				$("#adicionVniif").val(),
				descripcion:			$("#adiciondes").val(),
				escomponente:			$("#escomponente").val(),
				componente:				$("#componente").val(),
				modCFPDF:				$("input[name='costofutxdispfinal']:checked").val(),
				valorCFPDF:				$("#adicionCFPDF").val()
			}, function(data){

					if(data == 1)
					{
						limpiardatosadicion();
						alert("Grabacion Exitosa");
						ActivoSeleccionado = $("#wregistro").val();
						actualizarlistahistoricoActivo();
						actualizarlistahistoricoGeneral();
					}
					else
					{
						alert("Ocurrio un Error en la Grabacion: "+data);
					}

			});
		}
		else
		{
			alert(mensaje);
		}

	}

	function anularAdicion()
	{

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'anularAdicion',
				registroActivo:			$("#wregistro").val(),
				valoradicion:			$("#adicionvad").val(),
				modvidautil:			$("input[name='vidautil']:checked").val(),
				vidafiscal:				$("#adicionVfiscal").val(),
				vidaniif:				$("#adicionVniif").val(),
				escomponente:			$("#escomponente").val(),
				componente:				$("#componenten").val(),
				registrodemovimiento:	$("#registrodemovimiento").val()

			}, function(data){
				if(data == 1)
					{
						limpiardatosadicion();
						alert("Anulacion Exitosa");
						ActivoSeleccionado = $("#wregistro").val();
						actualizarlistahistoricoActivo();
						$(".campomovimiento").attr( 'disabled' , false);
						$("#escomponente").val('off');
						$("#componente").val('');
					}
					else
					{
						alert("Ocurrio un Error en la Anulacion")
					}


			});


	}

	function actualizarlistahistoricoActivo()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'htmlHistorico',
			op:						$("#op").val(),
			activo:					ActivoSeleccionado
		}, function(data){
			//$("#detalle_historico").remove();
			$("#divhistorico").html(data).show();
			$('#buscarActivoHistoricoactivo').quicksearch('#tablaListaActivosHistorico .find');
			$('#divhistorico').find('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });

			//$("#tabHistorico").html(data);
		});


	}

	function actualizarlistahistoricoGeneral()
	{
		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'htmlHistorico',
			op:						$("#op").val()
		}, function(data){
			//$("#detalle_historico").remove();
			$("#tabHistorico").html(data);
			$('#buscarActivoHistorico').quicksearch('#tablaListaActivosHistorico_general .find');
			$('#tabHistorico').find('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
			//$("#tabHistorico").html(data);
		});
	}


	function limpiardatosadicion()
	{
		actualizarlistahistoricoGeneral();
		$("#adicionAno").val('');
		$("#adicionMes").val('');
		$("#adicionvad").val('');
		$("#adicionnit").val('');
		$("#adicionVfiscal").val('');
		$("#adicionVniif").val('');
		$("#adicionCFPDF").val('');
		$("#adiciondes").val('');
		$("[name=vidautil][value=si]").prop('checked',true);
		$("[name=costofutxdispfinal][value=si]").prop('checked',true);
		$(".td_posterior").hide();
	}

	function grabarTraslado()
	{

		var permitirGuardar = true;
		var mensaje ;
		$('#tablaTraslado .campoObligatorio').removeClass('campoObligatorio');

		var listaCentrosCostos = '';
		$("#listaCentrosCostos tr").each(function(){
			listaCentrosCostos+= ","+$(this).attr("codCcoAct");
		});

		if(listaCentrosCostos == '' )
		{

			if(listaCentrosCostos == '')
			{
				$("#trasladoccd").addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}

		}

		// --> Validacion de campos obligatorios
		$("#tablaTraslado").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});

		// --> validacion de seleccion de activo
		if ($("#wregistro").val() =='')
		{
			permitirGuardar = false;
			mensaje = 'No hay activo seleccionado';
		}

		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
				{
					consultaAjax:   		'',
					accion:         		'grabarTraslado',
					registroActivo:			$("#wregistro").val(),
					ano:					$("#trasladoAno").val(),
					mes:					$("#trasladoMes").val(),
					jsonCcos:				arr_costosGlobal,
					descripcion:			$("#trasladodes").val(),
					escomponente:			$("#escomponente").val(),
					componente:				(($("#componente").val() == '') ? '*' : $("#componente").val())
				}, function(data){
						if(data == 1)
						{
							alert("Grabacion Exitosa");
							limpiardatostraslado();

							ActivoSeleccionado = $("#wregistro").val();
							actualizarlistahistoricoActivo();
							actualizarlistahistoricoGeneral();
						}
						else
						{
							alert("Ocurrio un Error en la Grabacion\n"+data)
						}
				});

		}
		else
		{
			alert(mensaje);
		}

	}

	function limpiardatostraslado()
	{
		$("#trasladoAno").val('');
		$("#trasladoMes").val('');
		$("#trasladodes").val('');
		$("#listaCentrosCostos").html("");
		$("#botoneditarCentroCostos").attr("disabled",false);
		$(".td_posterior").hide();
	}


	function validarVidaUtil(parametro)
	{
		if(parametro=='si')
		{
			// $(".vidalUtil").addClass('campoObligatorio');
			$("#adicionVniif").attr( 'tipo' , 'obligatorio');
			$("#adicionVniif").attr( 'placeholder' , ' ');
			$("#adicionVfiscal").attr( 'tipo' , 'obligatorio');
			$("#adicionVfiscal").attr( 'placeholder' , ' ');
			$("#adicionVfiscal").attr( 'disabled' , false );
			$("#adicionVniif").attr( 'disabled' , false);

		}
		else
		{
			$("#adicionVniif").removeAttr( 'tipo' );
			$("#adicionVniif").removeAttr( 'placeholder' );
			$("#adicionVfiscal").removeAttr( 'tipo' );
			$("#adicionVfiscal").removeAttr( 'placeholder');
			$("#adicionVfiscal").attr( 'disabled' , true );
			$("#adicionVniif").attr( 'disabled' , true);
			$("#adicionVfiscal").val('');
			$("#adicionVniif").val('');
		}
	}

	function validarCFPDF(parametro)
	{
		if(parametro=='si')
		{
			// $(".vidalUtil").addClass('campoObligatorio');
			$("#adicionCFPDF").attr( 'tipo' , 'obligatorio');
			$("#adicionCFPDF").attr( 'placeholder' , ' ');
			$("#adicionCFPDF").attr( 'disabled' , false);

		}
		else
		{
			$("#adicionCFPDF").removeAttr( 'tipo' );
			$("#adicionCFPDF").removeAttr( 'placeholder' );
			$("#adicionCFPDF").attr( 'disabled' , true);
			$("#adicionCFPDF").val('');
		}
	}

	//----------------------------------------------------
	//	--> Calendario flotante para seleccionar periodo
	//----------------------------------------------------
	function calendarioSeleccionarPeriodo(ele)
	{
		var mes_seleccionado = (($('#hiddenPeriodoSeleccionado').val() == '') ? $('#hiddenFechaActual').val() : $('#hiddenPeriodoSeleccionado').val() );
		mes_seleccionado = mes_seleccionado.split('-');
		mes_seleccionado = mes_seleccionado[1];

		$('#calendarioFlotante').find('td[ref]').each(function(index){
			$(this).addClass("cuadroMes").removeClass("cuadroMesSeleccionado");

			if(mes_seleccionado == $(this).attr('ref'))
				$(this).addClass("cuadroMesSeleccionado");
		});

		var posicion = $(ele).offset();
		//console.log("left: "+posicion.left + " , top: "+posicion.top+" ,margintop: "+$(ele).css("marginTop")+" cale"+$('#divCalendarioFlotante') );

		$('#divCalendarioFlotante').css({'left':posicion.left,'top':posicion.top+24}).show(400);
	}

	//--------------------------------------------------------------------------------
	//	--> Seleccionar un periodo para ver el activo, desde el calendario flotante
	//--------------------------------------------------------------------------------
	function seleccionarPeriodoDesdeCalendario(mes)
	{
		var fecha_seleccionada = $('#año_sel').val()+'-'+mes;
		$('#hiddenPeriodoSeleccionado').val(fecha_seleccionada);
		$('#divCalendarioFlotante').hide(400);

		$(".input_anio").val( $('#año_sel').val() );
		$(".input_mes").val( mes );

		//var formulario = $("#detalleActivo").find("li[class*=ui-state-active]").attr("formulario");
		//verActivo($("#hiddenRegistroActivo").val(), formulario, $("#hiddenComponente").val(), fecha_seleccionada);
	}


	function agregarCentroCostos()
	{
		var ccoAct		= $("#trasladoccd").val();
		if(ccoAct == '')
			return;

		var nomAct		= $("#trasladoccd :selected").text();
		var duplicado 	= false;
		$("#listaCentrosCostos tr").each(function(){
			if($(this).attr("codCcoAct") == ccoAct)
				duplicado = true;
		});

		if(!duplicado)
		{
			var newCcoAct = 	"<tr style='' codCcoAct='"+ccoAct+"'>"
									+"<td style='font-size:9px;border: 1px solid #AED0EA;' align='left'>"
										+"&nbsp;"+nomAct+"</td>"
									+"<td style='border: 1px solid #AED0EA;'><img style='cursor:pointer;width:10px;height:10px' onclick='$(this).parent().parent().remove();' title='Eliminar' src='../../images/medical/eliminar1.png'></td>"
								+"</tr>"
			$("#listaCentrosCostos").append(newCcoAct);
			$("#tdListaCentrosCostos").show();
		}
	}


	//------------------------------------------------
	//--> Graba el movimiento de adicion a activos fijos
	//------------------------------------------------
	function grabarRetiro()
	{

		var permitirGuardar = true;
		var mensaje ;
		$('#tablaRetiro .campoObligatorio').removeClass('campoObligatorio');


		// --> Validacion de campos obligatorios
		$("#tablaRetiro").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
				mensaje = 'Faltan campos por llenar';
			}
		});

		// --> validacion de seleccion de activo
		if ($("#wregistro").val() =='')
		{
			permitirGuardar = false;
			mensaje = 'No hay activo seleccionado';
		}

		var dateaux = $("#retiroFecha").val()+"/"+$("#RetiroMes").val()+"/"+$("#RetiroAno").val();
		if( isDate(dateaux) == false ){
			permitirGuardar = false;
			mensaje = 'El período y día seleccionado no son válidos.';
		}


		if(permitirGuardar)
		{
			$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
			{
				consultaAjax:   		'',
				accion:         		'grabarRetiro',
				registroActivo:			$("#wregistro").val(),
				ano:					$("#RetiroAno").val(),
				mes:					$("#RetiroMes").val(),
				motivo:					$("#Retiromotivo").val(),
				descripcion:			$("#Retirodes").val(),
				escomponente:			$("#escomponente").val(),
				componente:				(($("#componente").val() == '') ? '*' : $("#componente").val()),
				retiroFecha:			$("#retiroFecha").val()
			}, function(data){

					if(data == 1)
					{
						limpiardatosRetiro();
						alert("Grabacion Exitosa");
						ActivoSeleccionado = $("#wregistro").val();
						actualizarlistahistoricoActivo();
						actualizarlistahistoricoGeneral();
					}
					else
					{
						alert("Ocurrio un Error en la Grabacion \n"+data)
					}

			});
		}
		else
		{
			alert(mensaje);
		}

	}

	function limpiardatosRetiro()
	{
		$("#RetiroAno").val("");
		$("#RetiroMes").val("");
		$("#Retiromotivo").val("");
		$("#Retirodes").val("");
		$("#retiroFecha").val("");
		$(".td_posterior").hide();
	}

	function listarCcosEnListaActivos( ele, arr_ccos ){
		ele = jQuery(ele);
		if( ele.parent().find("table").length > 0 ){
			//Ya existe una tabla con la lista de centros de costos, hay que eliminarla y cambiar el titulo del boton
			ele.parent().find("table").remove();
			//ele.attr("value","Mostrar todos");
			ele.attr("src", "../../images/medical/hce/mas2.png");
		}else{
			//Mostrar lista de centros de costos en los datos generales
			var html_lista_ccos = "<table style='font-size: 7pt;text-align: left;margin:3px' width='100%'>";
			var i=0;
			for( i in arr_ccos ){
				html_lista_ccos+= 	"<tr style=''>"
										+"<td style='font-size:9px;border: 1px solid #AED0EA;' width='96%' align='left'>"
											+"&nbsp;"+arr_ccos[i].codigo+"-"+arr_ccos[i].nombre+"</td>"
									+"</tr>"
			}
			html_lista_ccos+="</table>";
			ele.next().after(html_lista_ccos);
			//ele.attr("value","Ocultar todos");
			ele.attr("src", "../../images/medical/hce/menos2.png");
		}
	}

	function verHistoricoMovimientos(ele){
		$("#panel_historico_movimientos").toggle();
		ele = jQuery(ele);
		if( $("#panel_historico_movimientos").is(":visible") )
			ele.attr("src", "../../images/medical/hce/menos2.png");
		else
			ele.attr("src", "../../images/medical/hce/mas2.png");
	}

	function editarCentroCostos(){
		if( $("#wregistro").val() == "" )
			return;

		if( $( '#divEditarCentrosDeCostos').length > 0  && $("#activoVentana").val() == $("#wregistro").val() ){
			mostrarPanelCcosTraslado();
			return;
		}

		$.post("<?=$URL_AUTOLLAMADO?>?"+url_add_params,
		{
			consultaAjax:   		'',
			accion:         		'htmlTrasladoCCOS',
			registroActivo:			$("#wregistro").val(),
			ano:					$("#trasladoAno").val(),
			mes:					$("#trasladoMes").val()
		}, function(data){
			$("#divEditarCentrosDeCostos").remove();
			$("body").append( data );
			// --> Abrir ventana de dialog
			mostrarPanelCcosTraslado();
		});
	}

	function mostrarPanelCcosTraslado(){

		$("*").dialog("destroy");

		$( '#divEditarCentrosDeCostos').dialog("destroy");
		$( '#divEditarCentrosDeCostos').dialog({
			show:{
				effect: "blind",
				duration: 100
			},
			hide:{
				effect: "blind",
				duration: 100
			},
			width:  1200,
			dialogClass: 'fixed-dialog',
			modal: true,
			title: "Configuración traslado centros de costos",
			close: function( event, ui ) {
				if($("#porcentajeTotal").attr("value")*1 < 100)
					alert("No se ha completado el 100% de distribución\entre los centros de costos.");
			}
		});
		activar_regex($("#divEditarCentrosDeCostos"));

		//
		//Mostrar los datos en la nueva ventana
		$.each(arr_costosGlobal,function(index,datos){
			$(".tr_centrocosto").each(function(){
				if( $(this).find(".td_cco").attr("cco") == datos.cco ){
					//Si no hay los suficientes destinos...
					var kkk=0;
					while( $(this).find(".tabladestinos").length < datos.ccos_destino.length && kkk<50 ){
						var clon = $(this).find(".tabladestinos").clone(false);
						$(this).find(".tabladestinos").after(clon);
						kkk++;
					}
					var j=0;
					for (j in datos.ccos_destino){
						$(this).find(".ccodestinot:eq("+j+")").val( datos.ccos_destino[j] );
						$(this).find(".porcentajedestinot:eq("+j+")").val( datos.porcentajes_destino[j] );
					}
					for (j in datos.estados_destino){
						var arraux = datos.estados_destino[j].split("|");
						var lista = $(this).find(".listaDeEstados:eq("+j+")");
						var k=0;
						for(k in arraux){
							lista.find(":checkbox[value="+arraux[k]+"]").prop("checked",true);
						}
					}
				}
			});
		});
	}

	//--------------------------------------------------
	//	Funcion que valida valores enteros en un campo
	//--------------------------------------------------
	function activar_regex(Contenedor)
	{
		// --> Validar enteros
		$('.entero', Contenedor).keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
	}


	function agregarCco()
	{
		var permitirGuardar = true;
		var mensajeError = "Faltan campos obligatorios";

		var arrEstados = $("#arrEstados").val();
		arrEstados = $.parseJSON(arrEstados);

		$('#divEditarCentrosDeCostos .campoObligatorio').removeClass('campoObligatorio');

		// --> Validar que esten seleccionados todos los campos
		$("#divEditarCentrosDeCostos").find("[tipo=obligatorio]").each(function(){
			if($(this).val() == '')
			{
				$(this).addClass('campoObligatorio');
				permitirGuardar = false;
			}
		});

		var nombres_ccos = new Object();
		$(".ccodestinot:first option").each(function(){
			nombres_ccos[ $(this).val() ] = $(this).text();
		});

		var arr_costos = new Array();
		$(".tr_centrocosto").each(function(){
			var cco = $(this).find(".td_cco").attr("cco");
			var porcentaje = $(this).find(".td_porcentaje").attr("porcentaje");

			var ccos_destino = new Array();
			var nombres_ccos_destino = new Array();
			$(this).find(".ccodestinot").each(function(){
				if( ccos_destino.indexOf( $(this).val() ) == -1 ){
					ccos_destino.push( $(this).val() );
					nombres_ccos_destino.push( nombres_ccos[$(this).val()] );
				}else{
					permitirGuardar = false;
					mensajeError = "Existe un centro de costos destino más de una vez";
				}
			});
			/*Analizar la frecuencia de los cco destino, NO PUEDE ESTAR el mismo cco varias veces*/


			var porcentajes_destino = new Array();
			var sumPorc = 0;
			$(this).find(".porcentajedestinot").each(function(){
				porcentajes_destino.push( $(this).val() );
				sumPorc+= ($(this).val()*1);
			});
			var estados_destino = new Array();
			var estados_destino_nombres = new Array();
			$(this).find(".listaDeEstados").each(function(){
				var listaDeEstados = "";
				var listaDeEstadosNombres = "";
				$(this).find("input[type=checkbox]:checked").each(function(){
					listaDeEstados = listaDeEstados+((listaDeEstados == '') ? $(this).val() : '|'+$(this).val());
					listaDeEstadosNombres = listaDeEstadosNombres+((listaDeEstadosNombres == '') ? $(this).val()+"-"+arrEstados[$(this).val()] : '|'+$(this).val()+"-"+arrEstados[$(this).val()]);
				});
				if(listaDeEstados == '')
				{
					$(this).addClass('campoObligatorio');
					permitirGuardar = false;
					return false;
				}
				estados_destino.push( listaDeEstados );
				estados_destino_nombres.push( listaDeEstadosNombres );
			});

			estados_origen = $(this).find(".hidden_estadosOrigen").val();

			var datos = new Object();
			datos.cco = cco;
			datos.nombre_cco = $(".td_cco[cco="+cco+"]").text();
			datos.porcentaje = porcentaje;
			datos.ccos_destino = ccos_destino;
			datos.nombres_ccos_destino = nombres_ccos_destino;
			datos.porcentajes_destino = porcentajes_destino;
			datos.suma_destino = sumPorc;
			datos.estados_destino = estados_destino;
			datos.estados_destino_nombres = estados_destino_nombres;
			datos.estados_origen =  JSON.parse(estados_origen);

			arr_costos.push(datos);
		});

		var sumaTotal = 0;
		$.each(arr_costos,function(index,datos){
				if( datos.suma_destino*1 > datos.porcentaje*1 ){
					var nombre_cco = datos.nombre_cco;
					mensajeError = "La suma de los porcentajes del centro de costo "+nombre_cco+" es "+datos.suma_destino+", mayor que la permitida "+datos.porcentaje;
					permitirGuardar = false;
					return false;
				}
				sumaTotal+=datos.suma_destino*1;
			});
		//console.log(sumaTotal);
		$("#porcentajeTotal").val( sumaTotal );
		if( permitirGuardar == true && sumaTotal*1 < 100 ){
			mensajeError = "No se ha completado el 100% del porcentaje";
			permitirGuardar = false;
		}

		if(permitirGuardar)
		{
			arr_costosGlobal = arr_costos;

			/*console.log(arrEstados);
			arr_costos_aux = arr_costos;
			$.each(arr_costos_aux,function(index,datos){
				var auxuno = new Array();
				$.each(datos.estados_destino,function(i,estados_destino){
					var aux = new Array();
					var arrestadod = estados_destino.split("|");
					$.each( arrestadod,function(j,estadoxx){
						var str = estadoxx+"-"+arrEstados[ estadoxx ];
						aux.push( str );
					});
					auxuno.push( aux.join("|") );
				});
				datos.estados_destino = auxuno;
			});*/

			mostrarListaCcosPanel( arr_costos );

			$( '#divEditarCentrosDeCostos').dialog("close");
		}
		else
			mostrar_mensaje(mensajeError, "div_mensajes4");
	}

	function mostrarListaCcosPanel( arr_costos ){
			//Mostrar lista de centros de costos en los datos generales
			var html_lista_ccos = "<table style='font-size: 7pt;text-align: left;margin:3px' width='100%'>";
			html_lista_ccos = "<tr class='fila1' align='center'><td>Centro de costos</td><td>% origen</td><td>Ccos destino</td><td>% destino</td><td>Estados</td></tr>";
			var i=0;

			$.each(arr_costos,function(index,arr_ccos){
				var arrayEstadosCco = arr_ccos.estados_destino_nombres;
				var arrayporcentajes_destino = arr_ccos.porcentajes_destino;
				var arrayDestinos = arr_ccos.ccos_destino;
				if( arr_ccos.nombres_ccos_destino != undefined )
					arrayDestinos = arr_ccos.nombres_ccos_destino

				rowspan 			= arrayEstadosCco.length;

				html_lista_ccos+= 	"<tr id='trPosCco"+i+"'>"
										+"<td rowspan='"+rowspan+"' style='font-size:9px;border: 1px solid #AED0EA;' align='left'>"
											+"&nbsp;"+arr_ccos.cco+"-"+arr_ccos.nombre_cco+"</td>"
										+"<td rowspan='"+rowspan+"' style='font-size:9px;border: 1px solid #AED0EA;' >"+arr_ccos.porcentaje+"%</td>"
										+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayDestinos[0]+"</td>"
										+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayporcentajes_destino[0]+"%</td>"
										+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+(arrayEstadosCco[0]).replace("|","<br>")+"</td>"
									+"</tr>";
				var key=0;
				for(key in arrayEstadosCco){
					if(key > 0)
						html_lista_ccos+="<tr id='trPosCco"+i+"'>"
							+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayDestinos[key]+"</td>"
							+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+arrayporcentajes_destino[key]+"%</td>"
							+"<td style='font-size:9px;border: 1px solid #AED0EA;'>"+(arrayEstadosCco[key]).replace("|","<br>")+"</td></tr>";
				}
				i++;
			});
			html_lista_ccos+="</table>";
			$("#listaCentrosCostos").html(html_lista_ccos);
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

	function agregarCcoTraslado(obj){
		obj=jQuery(obj);
		var clon = obj.parents(".tabladestinos").clone(false);
		obj.parents(".tabladestinos").after(clon);
	}

	function eliminarCcot(obj){
		obj=jQuery(obj);

		var canticco = obj.parents(".tr_centrocosto").find(".tabladestinos").length;
		if( canticco > 1 )
			obj.parents(".tabladestinos").remove();
	}

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
	}

	function cancelarCco(){
		$( '#divEditarCentrosDeCostos').dialog("close");
	}

	function isDate(txtDate)
	{
	  var currVal = txtDate;
	  if(currVal == '')
		return false;

	  //Declare Regex
	  var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
	  var dtArray = currVal.match(rxDatePattern); // is format OK?

	  if (dtArray == null)
		 return false;

	  //Checks for dd/mm/yyyy format.
	  dtDay = dtArray[1];
		dtMonth= dtArray[3];
		dtYear = dtArray[5];


	  if (dtMonth < 1 || dtMonth > 12)
		  return false;
	  else if (dtDay < 1 || dtDay> 31)
		  return false;
	  else if ((dtMonth==4 || dtMonth==6 || dtMonth==9 || dtMonth==11) && dtDay ==31)
		  return false;
	  else if (dtMonth == 2)
	  {
		 var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
		 if (dtDay> 29 || (dtDay ==29 && !isleap))
			  return false;
	  }
	  return true;
	}

	//	--> Cargar un html flotante
	//-----------------------------------------------------------
	function flotanteElegirActivo()
	{

		var posicion = $('#trasladoAno').offset();
		$('#ingresoFlotante').css({'left':100,'top':200});
		$('#ingresoFlotante').show();
		$('#ingresoFlotante').draggable();
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
	echo '<input type="hidden" id="registrodemovimiento"><input type="hidden" id="wregistro">
		  <div id="divCalendarioFlotante" style="display:none;z-index:10000;position: absolute;">
			<div id="calendarioFlotante" style="border:solid 1px #4C8EAF;border-radius: 4px;padding:2px;background-color: ;">
				<table>
				<tr>
					<td colspan="3" align="center" style="font-size:11pt;text-align:center;"><b>Año</b>:
						<select id="año_sel" name="año_sel" style="width:67px;border: 1px solid #4C8EAF;background-color:lightyellow;font-size:9pt;">';
						$año_inicio = 2006;
						$año_actual = date('Y');
						for($x=$año_inicio; $x <= $año_actual+1; $x++)
							echo "<option ".(($anoActual==$x)? 'SELECTED':'').">".$x."</option>";
		echo '			</select>
						<img style="cursor:pointer;width:12px;height:12px;" onClick="$(\'#divCalendarioFlotante\').hide(500);" src="../../images/medical/eliminar1.png" title="Cerrar calendario" >
					</td>
				</tr>
				<tr>
					<td id="ene" class="cuadroMes" ref="01" onClick="seleccionarPeriodoDesdeCalendario(\'01\');">Ene</td>
					<td id="feb" class="cuadroMes" ref="02" onClick="seleccionarPeriodoDesdeCalendario(\'02\');">Feb</td>
					<td id="mar" class="cuadroMes" ref="03" onClick="seleccionarPeriodoDesdeCalendario(\'03\');">Mar</td>
				</tr>
				<tr>
					<td id="abr" class="cuadroMes" ref="04" onClick="seleccionarPeriodoDesdeCalendario(\'04\');">Abr</td>
					<td id="may" class="cuadroMes" ref="05" onClick="seleccionarPeriodoDesdeCalendario(\'05\');">May</td>
					<td id="jun" class="cuadroMes" ref="06" onClick="seleccionarPeriodoDesdeCalendario(\'06\');">Jun</td>
				</tr>
				<tr>
					<td id="jul" class="cuadroMes" ref="07" onClick="seleccionarPeriodoDesdeCalendario(\'07\');">Jul</td>
					<td id="ago" class="cuadroMes" ref="08" onClick="seleccionarPeriodoDesdeCalendario(\'08\');">Ago</td>
					<td id="sep" class="cuadroMes" ref="09" onClick="seleccionarPeriodoDesdeCalendario(\'09\');">Sep</td>
				</tr>
				<tr>
					<td id="oct" class="cuadroMes" ref="10" onClick="seleccionarPeriodoDesdeCalendario(\'10\');">Oct</td>
					<td id="nov" class="cuadroMes" ref="11" onClick="seleccionarPeriodoDesdeCalendario(\'11\');">Nov</td>
					<td id="dic" class="cuadroMes" ref="12" onClick="seleccionarPeriodoDesdeCalendario(\'12\');">Dic</td>
				</tr>
				</table>
			</div>
		</div>';

	echo"
	<input type='hidden' id='op' value='".$op."' >
	<div id='tabs'>
			<div id='buscadorActivos'>
				<fieldset align='center' style='padding:15px;margin:15px'>
					<legend class='fieldset'>Buscar Activo</legend>";
					lista_activos();
				echo"</fieldset>
			</div>
			<br>
		<div id='operaciones'  style='display:none' >
			<ul>";
			if( $op == "a" ) echo "<li><a href='#tabAdicion'>Adición</a></li>";
			if( $op == "t" ) echo "<li><a href='#tabTraslado'>Traslado</a></li>";
			if( $op == "r" ) echo "<li><a href='#tabRetiro'>Retiro</a></li>";

			$titulo_historico = "Historico global de ";
			if( $op == "a" ) $titulo_historico.= " Adiciones";
			if( $op == "t" ) $titulo_historico.= " Traslados";
			if( $op == "r" ) $titulo_historico.= " Retiros";

			echo "<li><a href='#tabHistorico'>".$titulo_historico."</a></li>";
		echo "</ul>";
		if( $op == "a" ){
			echo "<div id='tabAdicion'>";
					htmlAdicion();
			echo	"</div>";
		}
		if( $op == "t" ){
			echo "<div id='tabTraslado'>";
						htmlTraslado();
			echo	"</div>";
		}
		if( $op == "r" ){
			echo "<div id='tabRetiro' >";
						htmlRetiro();
			echo "</div>";
		}
		echo "<div id='tabHistorico' >";
						$htmlHistorico = htmlHistorico($op);
						echo $htmlHistorico;
			echo "</div>";

	echo "</div>
	</div>";

		// --> Cuadro flotante para ejecutar el proceso de ingreso del activo
		echo "
		<div id='ingresoFlotante' style='width:250px;cursor:move;display:none;z-index:900;position: fixed;'>
			<table style='background:#ffffcc;font-size:10pt;padding:3px;border: 1px solid #2A5DB0;border-radius: 5px;'>
				<tr>
					<td align='center'>
						&nbsp;<img width='13' height='13' tooltip='si' style='cursor:help' title='' src='../../images/medical/sgc/info.png'>&nbsp;&nbsp;<b>Debe seleccionar un activo.</b>
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
