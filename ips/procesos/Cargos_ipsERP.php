<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        TITULO DEL PROGRAMA
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				GERMAN FELIPE ALVAREZ
//FECHA DE CREACION:




/*

//--------------------------------------------------------------------------------------------------------------------------------------------
//                  CAMBIOS PARA MIGRACION
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
	CODIGO	|	FECHA		|	AUTOR 	|	DESCRIPCION
----------------------------------------------------------------------------------------------------------------------------------------------
	MIGRA_1	|	2019-01-22	|	Jerson	|	Se corrige tilde en el tooltip para mostrar si se aplic� alguna politica
	MIGRA_2	|	2019-01-31	|	Jerson	|	Se agrega coma(,) en el select del query




*/
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
$wactualiza = '(2020-03-04)';
/**--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
// --> 2020-03-04: 	Jerson Trujillo: Los conceptos que mueven inventario, no se dejaran regrabar ya que por el integrador
					no se puede enviar la tarifa del cargo a devolver y la fecha(En el caso de cargos grabados el mes anterior)
2020-01-22, Jerson: Mostrar mensaje de ventana de mantenimiento dependiendo de variables en la root_51
Noviembre 18 2019: Jerson, Se coloca trim en la variable historia, al momento de imprimir el sticker de solicitud de examen.
Septiembre 12-09-2019: Jerson
	- Se agrega nueva funcionalidad para grabar iva
Agosto 21-08-2019: Jerson
		Para los conceptos de inventario validar si se est� actualizando el cron de tarifas de medicamentos y materiales, para no dejar grabar cargos
Julio 16 de 2019 Jerson Trujillo:
	- Se controla la cantidad maxima permitida a grabar para cargos que mueven inventario, con la variable de la 51 cantidadMaxPermitidaParaGrabarCargosInv
Marzo 4 de 2019 Jerson Trujillo:
	- Cuando exista una excepcion tarifaria, se activa un campo nuevo para ingresar el codigo cups con el cual se deben generar los RIPS
Octubre 23 2018 Jerson Trujillo:
	- Se agrega enlace para acceder al agreso del paciente, solo se activa si el paciente tiene alta definitiva y esta en urgencias.
Septiembre 09 2018 Jerson Trujillo:
	- Nuevo control, si se graba una cantidad > 100 y es cargo de inventarios se saca una advertencia, Si no es de inventario
	  No se permite grabar.
Mayo 16 2018: Jerson Trujillo
	- Se corrige incosistencia en la impresion del soporte del cargo, ya que no aparecia el valor del descuento del cargo.
	- Se modifica la impresion del soporte del cargo, para que se incluya el valor del recargo, si lo hubo.
Abril 11 2018: Jerson Trujillo
	- Nueva validacion, cuando se de click en el check de devolucion solo se desplegaran los conceptos que muevan inventario.
Noviembre 08 2017:
 * Se corrige incosistencia, con la politica de tercero por defecto que estaba grabando tercero cunado este ya habia sido borrado.
Octubre 11 2017: Jerson Trujillo
 * Se marca de color rojo lo cargos de materiales que no hayan integrado y se agrega tooltip en la columna de registro en el detalle de los cargos
	  indicando el numero de registro en unix.

Septiembre 14 2017: Jerson Andres Trujillo:
 * 	Se crea una nueva funcionalidad para que dependiendo de un permiso en la cliame_000030, el grabador pueda seleccionar si se va
		a aplicar recargo, es decir si el cargo tiene una politica de recargo pero el grabador selecciono que NO aplica recrago, entonces
		se omitira la aplicacion de la politica del recargo.
Marzo 28 2017: Camilo Zapata
 * se modifican las consultas que tienen que ver con la tabla 205 para que tengan en cuenta si el responsable est� descartado o no Resdes
 * se adiciona el manejo del campo tcarreg = 'pen' para especificar que el cargo est� pendiente de regrabar por cambio de responsable
Febrero 02 2017: Jerson Trujillo
	- 	Se modifica la funcionalidad ObtenerPoliticaManejoTerceros, para que trabaje con un nuevo parametro "OP" el cual indica
		que al ingresar un tercero sera opcional, se pinta el input para pedir tercero pero no sera obligatorio ingresarlo.

Enero 26 2017 Edwar Jaramillo:
 * Se empieza a usar la funci�n "validar_cargo_ingreso_inactivo", en caso de identificar que se est� grabando cargo a ingreso inactivo, se
		setean ciertas variables que indicar�n posteriormente si para el cargo se debe actualizar en unix el n�mero del ingreso. Esas variables son
		tenidas en cuenta en funciones_facturacionERP para que en al tabla de cargos se marquen los cargos a los que se les debe actualizar el n�mero de ingreso
		en unix, pues el integrador siempre graba con el �ltimo ingreso activo.
Diciembre 29 2016 Edwar Jaramillo:
 * En la validaci�n
		"Validar si se debe redondear a la centena valores totales, Jerson Trujillo 2016-02-24"
		se debe tener en cuenta que los insumos no se pueden redondear y se estaban redondeando, por tanto se agrega si
		lo que se est� grabando es un insumo "$wconinv != 'on'" adicional a los tipos de empresa que permiten redondear
Junio 20 2016 Edwar:
 * Modificaci�n para que al cargar los datos del paciente en el encabezado utilice el ingreso que llega por par�metro url desde los monitores, el programa estaba siempre cargando el �ltimo ingreso.
Julio 30 2015 Edwar:
 * En la funci�n "cargar_terceros" se cambia de l�nea el llamado a la funci�n "cargarSelectEspecialidades", pues primero se debe escribir el c�digo del tercero
		en el campo respectivo antes de llamar la funci�n, de esta forma cuando se llame la funci�n "cargarSelectEspecialidades" encontrar� el valor del tercero, antes se estaba
		quedando vac�o y no encontraba excepci�n tarifaria.
--------------------------------------------------------------------------------------------------------------------------------------------*/


//--------------------------------------------------------------------------------------------------------------------------------------------
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if (!isset($_SESSION['user']) || !array_key_exists('user', $_SESSION)) {
	echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
	return;
} else {
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-', $_SESSION['user']);
	$wuse = $user_session[1];


	include_once("root/comun.php");


	include "../../gesapl/procesos/gestor_aplicaciones_config.php";
	include_once("../../gesapl/procesos/gesapl_funciones.php");
	$conex 		= 	obtenerConexionBD("matrix");
	include_once("ips/funciones_facturacionERP.php");
	$wbasedato 	= 	consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	$wfecha		=	date("Y-m-d");
	$whora 		= 	date("H:i:s");


	$hay_unix 	= consultarAplicacion($conex, $wemp_pmla, "conexionUnix");
	$graba_unix = consultarAplicacion($conex, $wemp_pmla, "grabarUnix");
	if ($hay_unix == "off" && $graba_unix == "on") {
		echo '<br/><br/><br/><br/>
			<div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
				[?] PROGRAMA NO DISPONIBLE...<br />Nos encontramos en una ventana de mantenimiento, por favor intente ingresar mas tarde, Disculpas por las molestias.
			</div>';
		return;
	}

	//=====================================================================================================================================================================
	//		F U N C I O N E S	 G E N E R A L E S    P H P
	//=====================================================================================================================================================================

	//------------------------------------------------------------------------------------
	//	Nombre:			cargar_hiddens_para_autocomplete
	//	Descripcion:	Funcion que crea los hiddens para cargarlos en los autocomplete
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------
	function cargar_hiddens_para_autocomplete()
	{
		global $caracter_ok;
		global $caracter_ma;
		global $conex;

		// --> Entidades
		echo "<input type='hidden' id='hidden_entidades' value='" . json_encode(obtener_array_entidades()) . "'>";
		// --> Hidden de array de las tarifas
		echo "<input type='hidden' id='hidden_tarifas' name='hidden_tarifas' value='" . json_encode(Obtener_array_tarifas()) . "'>";

		// --> Cargar cups
		$arrCups = array();
		$sqlCups = "
		SELECT Codigo, Nombre
		  FROM root_000012
		 WHERE Estado = 'on'
		";
		$resCups = mysql_query($sqlCups, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $sqlCups . " - " . mysql_error());
		while ($rowCups = mysql_fetch_array($resCups)) {
			$arrCups[$rowCups['Codigo']] = utf8_encode(str_replace($caracter_ma, $caracter_ok, $rowCups['Nombre']));
		}
		echo "<input type='hidden' id='hidden_proCups' name='hidden_proCups' value='" . json_encode($arrCups) . "'>";
	}
	//-------------------------------------------------------------------------------------------------------
	//	Funcion que obtiene la informacion relacionada al grabador (Usuario) asi como sus diferentes permisos
	//-------------------------------------------------------------------------------------------------------
	function cargar_datos_caja()
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wuse;

		$data = array(
			'wcco' => '', 'wnomcco' => '', 'wbod' => '', 'wcaja' => '', 'wnomcaj' => '', 'wcajadm' => '',
			'wtiping' => '', 'nomCajero' => '', 'cambiarResponsable' => '', 'cambiarTarifa' => '', 'permiteRegrabar' => '', 'permiteImprimirSoporteCargo' => '', 'permiteImprimirSolicitudExamen' => '', 'permiteAnularCargo' => '',
			'permiteSeleccionarFacturable' => '', 'permiteSeleccionarRecExc' => '', 'wtipcli' => '', 'permisoParaHacerDescuento' => '', 'permisoParaAplicarRecago' => 'off'
		);

		$q =  " SELECT Cjecco, Cjecaj, Cjetin, cjetem, cjeadm, cjebod, Descripcion, Cjecrc,
					   Cjectc, Cjeprc, Cjesfc, Cjesre, Cjepic, Cjepis, Cjepac, Cjehdc, Cjepar
				  FROM " . $wbasedato . "_000030, usuarios
				 WHERE Cjeusu = '" . $wuse . "'
				   AND Cjeest = 'on'
				   AND Cjeusu = Codigo";

		$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);

		if ($row = mysql_fetch_array($res)) {
			$pos 									= strpos($row['Cjecco'], "-");
			$data['wcco']    						= substr($row['Cjecco'], 0, $pos);
			$data['wnomcco'] 						= substr($row['Cjecco'], $pos + 1, strlen($row['Cjecco']));
			$data['wbod'] 	 						= $row['cjebod'];
			$pos 									= strpos($row['Cjecaj'], "-");
			$data['wcaja']   						= substr($row['Cjecaj'], 0, $pos);
			$data['wnomcaj'] 						= substr($row['Cjecaj'], $pos + 1, strlen($row['Cjecaj']));
			$data['wcajadm'] 						= $row['cjeadm'];
			$data['wtiping'] 						= $row['Cjetin'];
			$data['nomCajero'] 						= $row['Descripcion'];
			$data['cambiarResponsable'] 			= $row['Cjecrc'];
			$data['cambiarTarifa'] 					= $row['Cjectc'];
			$data['permiteRegrabar'] 				= $row['Cjeprc'];
			$data['permiteImprimirSoporteCargo']	= $row['Cjepic'];
			$data['permiteImprimirSolicitudExamen']	= $row['Cjepis'];
			$data['permiteAnularCargo']				= $row['Cjepac'];
			$data['permiteSeleccionarFacturable'] 	= $row['Cjesfc'];
			$data['permiteSeleccionarRecExc'] 		= $row['Cjesre'];
			$data['permisoParaHacerDescuento'] 		= $row['Cjehdc'];
			$data['permisoParaAplicarRecago'] 		= $row['Cjepar'];
			$data['wtipcli'] 						= $row['cjetem'];
		}

		return $data;
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function traer_interfaz_abonos($wconmca, $wcco)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;



		if ($wconmca == "on")   //Indica que es un abono y que mueve caja
		{
			//Aca traigo la fuente y el consecutivo del centro de costo para saber si tiene fuente y consecutivo
			//Si no tiene estos dos datos bien no hace la grabaci�n del cargo.
			$q = "SELECT ccofrc, ccorci "                //Fuente Recibo de Caja y Consecutivo
				. "  FROM " . $wbasedato . "_000003 "
				. " WHERE ccocod='" . $wcco . "'"
				. "   AND ccoest = 'on' ";



			$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$row = mysql_fetch_array($err);
			$wfuerec   = $row[0];               //Fuente Recibo de Caja
			$wnrorec   = $row[1];               //Consecutivo recibo de caja

			if (is_numeric($wnrorec) and $wnrorec > 0 and $wfuerec != "NO APLICA")
				$wtiene_consecutivo = "on";
			else
				$wtiene_consecutivo = "off";

			if ($wtiene_consecutivo == "on") {
				$fk = 1;
				$dataresp = formasdepago($fk);
			} //Fin del then de if $wtiene_consecutivo
			else {
				// no quitar los !!! estos estan validando algo para mostrar la el blockUI
				$dataresp = "!!! NO EXISTE FUENTE, NI CONSECUTIVO PARA RECIBOS EN ESTE CENTRO DE COSTO";
			}
		} //Fin del then de if ($wconmca) SI mueve caja
		else {
			$fk = 1;
			$dataresp = formasdepago($fk);
		}
		echo $dataresp;
	}

	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------
	function formasdepago($fk)
	{

		global $wbasedato;
		global $conex;

		$html  = "<table   style='border-bottom-color:#000000;border-bottom-width:2px;border-bottom-style:solid;border-top-color:#000000;border-top-style:solid;border-top-width:2px; border-left-color:#000000; border-left-width:3px;border-left-style:solid;border-right-color:#000000; border-right-width:2px;border-right-style:solid;'>";
		$html .= "<tr><td align=center colspan=15 class=encabezadoTabla><b>D E T A L L E &nbsp;&nbsp; D E L&nbsp;&nbsp; A B O N O</b></td></tr>";

		for ($j = 1; $j <= $fk; $j++) {
			$html .= "<tr>";

			$q =  " SELECT fpacod, fpades "
				. "   FROM " . $wbasedato . "_000023 "
				. "  WHERE fpaest = 'on' "
				. "  ORDER BY fpacod ";

			$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			$wcf2 = 'fila2';

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//FORMA DE PAGO
			$html .=  "<td align=left class=" . $wcf2 . " colspan=1><b>Forma de pago:</b><br><select id='wfpa' name='wfpa' onchange='pedir_datos_banco(" . $j . ")'>";
			for ($i = 1; $i <= $num; $i++) {
				$row = mysql_fetch_array($res);
				$comp = $row[0] . " - " . $row[1];
				$html .= "<option  >" . $row[0] . " - " . $row[1] . "</option >";
				if ($i == 1)
					$wfpa[$j] = $row[0] . " - " . $row[1];
			}
			$html .=  "</select></td>";

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//DOCUMENTO ANEXO
			$html .=  "<td class=" . $wcf2 . " colspan=1><b>Dcto Anexo:</b><br><INPUT TYPE='text' NAME='wdocane' id='wdocane'  ></td>";                        //wdocane
			/////////////////////

			//////busco para la opcion seleccionada, si es tarjeta o cheque
			$expefpa = explode('-', $wfpa[$j]);
			$q =  " SELECT fpache, fpatar "
				. "   FROM " . $wbasedato . "_000023 "
				. "  WHERE fpacod='" . $expefpa[0] . "' ";

			$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			$row = mysql_fetch_array($res);


			if ($row[0] == 'on' or $row[1] == 'on') {
				$obliga = 'on';
				//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//BANCO, consulto lista de bancos
				$html .=  "<td class=" . $wcf2 . " colspan=1><div id ='div_datos_banco'><table class=" . $wcf2 . " ><tr><td><b>Datos del Banco:</b><br><select name='wobsrec' id='wobsrec' >";
				$q =  " SELECT bancod, bannom "
					. "   FROM " . $wbasedato . "_000069 "
					. "  WHERE banest='on' ";

				$resu = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num1 = mysql_num_rows($resu);
				for ($y = 1; $y <= $num1; $y++) {
					$banc = mysql_fetch_array($resu);
					$html .=  "<option>" . $banc[0] . '-' . $banc[1] . "</option>";
				}
				$html .=  "</select></td>";

				$colspan = 9;
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//PLAZA
				$html .=  "<td class=" . $wcf2 . " colspan=1><b>Ubicacion:</b><br><select name='wubica' id='wubica' ><option selected>1-Local</option ><option>2-Otras plazas</option></select></td>";                        //wdocane
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//NUMERO DE AUTORIZACION
				$html .=  "<td class=" . $wcf2 . " colspan=1><b>N� autorizacion:</b><br><INPUT TYPE='text' NAME='wautori' id='wautori' ></td></tr></table></div></td>";                           //wobsrec
			} else {
				$obliga = 'off';
				$colspan = 9;
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//OBSERVACIONES
				$html .=  "<td class=" . $wcf2 . " colspan=1><div id='div_datos_banco'><table><tr><td class=" . $wcf2 . "><b>Observacion:</b><br><INPUT TYPE='text' NAME='wobsrec'  id='wobsrec'></td>";                           //wobsrec
				// espacios en blanco para nivelar
				$html .=  "<td class=" . $wcf2 . " colspan=1><b>&nbsp;</td>";
				$html .=  "<td class=" . $wcf2 . " colspan=1><b>&nbsp;</td></tr></table></div></td>";
			}

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//RECONOCIDO O EXCEDENTE PARA LA FORMA DE PAGO
			$html .=  "<td align=center class=" . $wcf2 . " colspan=1><b>(R)ec-(E)xc</b><br><INPUT TYPE='text' NAME='wrecexcfpa' id='wrecexcfpa' SIZE='1' MAXLENGTH='1' VALUE='R' onkeypress='if (event.keyCode != 69 & event.keyCode != 82 & event.keyCode != 101 & event.keyCode != 114)  event.returnValue = false'></td>";

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//VALOR
			$html .=  "<td class=" . $wcf2 . " colspan=2><b>Valor: </b><br><INPUT TYPE='text'  id='wvalfpa'></td>";  //wvalfpa


			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//BANCO EN EL QUE SE CONSIGNA O DESTINO
			$html .=  "<td class=" . $wcf2 . " colspan=1><b>En que Banco se consigna:<br></b><select name='wbandes' id='wbandes' >";
			$wfpago = explode("-", $wfpa[$j]);
			$q = " SELECT bancod, bannom "
				. "   FROM " . $wbasedato . "_000069, " . $wbasedato . "_000023 "
				. "  WHERE banest = 'on' "
				. "    AND banrec = 'on' "
				. "    AND bancod = fpacba "
				. "    AND fpacod = '" . $wfpago[0] . "'"
				. "    AND fpaest = 'on' "
				. "  UNION "
				. " SELECT bancod, bannom "
				. "   FROM " . $wbasedato . "_000069 "
				. "  WHERE banest = 'on' "
				. "    AND bancag = 'on' "
				. "    AND banrec = 'on' ";

			$resban = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$numban = mysql_num_rows($resban);
			$banc = mysql_fetch_array($resban);

			$html .=  "<option selected>" . $banc[0] . '-' . $banc[1] . "</option >";
			$resu = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num1 = mysql_num_rows($resu);

			for ($y = 1; $y <= $numban; $y++) {
				$banc = mysql_fetch_array($resban);
				$html .=  "<option>" . $banc[0] . '-' . $banc[1] . "</option>";
			}
			$html .=  "</select></td>";

			$html .=  "</tr>";
			$html .=  "<input type='hidden' NAME='obliga'  id='obliga' value='" . $obliga . "'>";
		}
		$html .= "<tr align='center'><td colspan='" . $colspan . "' align='center'><input type='button'  value='Grabar' onclick='grabarabono()'  >";
		$html .= "<input type='button' value='Cerrar' onClick='$.unblockUI();'></td></tr>";

		return $html;
	}
	//----------------------------
	//	Nombre:
	//	Descripcion:
	//	Entradas:
	//	Salidas:
	//----------------------------

	function pedir_datos_banco($wdato)
	{


		global $wbasedato;
		global $conex;


		$expefpa = explode('-', $wdato);
		$q =  " SELECT fpache, fpatar "
			. "   FROM " . $wbasedato . "_000023 "
			. "  WHERE fpacod='" . $expefpa[0] . "' ";

		$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);
		$wcf2 = 'fila2';

		if ($row[0] == 'on' or $row[1] == 'on') {
			$obliga = 'on';
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//BANCO, consulto lista de bancos
			$html .=  "<table><tr><td class=" . $wcf2 . " colspan=1><b>Datos del Banco:</b><br><select name='wobsrec' id='wobsrec' >";
			$q =  " SELECT bancod, bannom "
				. "   FROM " . $wbasedato . "_000069 "
				. "  WHERE banest='on' ";

			$resu = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num1 = mysql_num_rows($resu);
			for ($y = 1; $y <= $num1; $y++) {
				$banc = mysql_fetch_array($resu);
				$html .=  "<option>" . $banc[0] . '-' . $banc[1] . "</option>";
			}
			$html .=  "</select></td>";

			$colspan = 9;
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//PLAZA
			$html .=  "<td class=" . $wcf2 . " colspan=1><b>Ubicacion:</b><br><select name='wubica' id='wubica' ><option selected>1-Local</option ><option>2-Otras plazas</option></select></td>";                        //wdocane
			////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//NUMERO DE AUTORIZACION
			$html .=  "<td class=" . $wcf2 . " colspan=1><b>N� autorizacion:</b><br><INPUT TYPE='text' NAME='wautori'  id='wautori'></td></tr></table>";                           //wobsrec
		} else {
			$obliga = 'off';
			$colspan = 9;
			/////////////////////////////////////////////////////////////////////////////////////////////////////////////
			//OBSERVACIONES
			$html .=  "<table><tr><td class=" . $wcf2 . " colspan=1><b>Observacion:</b><br><INPUT TYPE='text' id='wobsrec' NAME='wobsrec' ></td>";                           //wobsrec
			// espacios en blanco para nivelar
			$html .=  "<td class=" . $wcf2 . " colspan=1><b>&nbsp;</td>";
			$html .=  "<td class=" . $wcf2 . " colspan=1><b>&nbsp;</td></tr></table>";
		}
		echo $html;
	}
	function grabarabono($datos)
	{


		global $conex;
		global $wbasedato;
		global $wuse;
		global $wemp_pmla;
		$wusuario = $wuse;

		// --> ESTABLECER VARIABLES NECESARIAS

		$whistoria 	= $datos['whistoria'];
		$wing 		= $datos['wing'];
		$wfeccar 	= $datos['wfeccar'];
		$wser 		= $datos['wser'];
		$wcodemp 	= $datos['wcodemp'];
		$wnomemp 	= $datos['wnomemp'];
		$wno1 		= $datos['wno1'];
		$wno2 		= $datos['wno2'];
		$wap1 		= $datos['wap1'];
		$wap2 		= $datos['wap2'];
		$wdoc 		= $datos['wdoc'];
		$wccogra 	= $datos['wccogra'];
		$wcodcon 	= $datos['wcodcon'];
		$wnomcon 	= $datos['wnomcon'];
		$wprocod 	= $datos['wprocod'];
		$wpronom 	= $datos['wpronom'];
		$wcodter 	= $datos['wcodter'];
		$wnomter 	= $datos['wnomter'];
		$wporter 	= $datos['wporter'];
		$wfacturable = $datos['wfacturable'];
		$wtipfac 	= $datos['wtipfac'];
		$waprovecha = $datos['waprovecha'];
		$wvaltar 	= $datos['wvaltar'];
		$wrecexcfpa = $datos['wrecexcfpa'];
		$wcco 		= $datos['wcco'];
		$wcaja		= $datos['wcaja'];
		$wfecha = date("Y-m-d");
		$hora = date("H:i:s");

		//---datos de la ventana de abono
		$wobsrec = $datos['wobsrec'];
		$wvalfpa = $datos['wvalfpa'];
		$obliga  = $datos['obliga'];
		$wubica  = $datos['wubica'];
		$wautori = $datos['wautori'];
		$wdocane = $datos['wdocane'];
		$wconmca = $datos['wconmca'];
		//----------------


		$data = array();
		$data['error'] = 0;
		$data['mensaje'] = '';


		$wnromvto = "";
		$wconmvto = "";
		$wtotfpa = $wvaltar;
		$wcantidad = 1;


		if ($wconmca == 'on') {
			//Aca traigo la fuente y el consecutivo del centro de costo para saber si tiene fuente y consecutivo
			//Si no tiene estos dos datos bien no hace la grabaci�n del cargo.
			$q = "SELECT ccofrc, ccorci "                //Fuente Recibo de Caja y Consecutivo
				. "  FROM " . $wbasedato . "_000003 "
				. " WHERE ccocod='" . $wcco . "'"
				. "   AND ccoest = 'on' ";
			$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$row = mysql_fetch_array($err);
			$wfuerec   = $row[0];               //Fuente Recibo de Caja
			$wnrorec   = $row[1];               //Consecutivo recibo de caja


			$wvalfpa = str_replace(",", "", $wvalfpa);    //Le quito el formato al n�mero
			$wtotfpa = $wtotfpa;

			//ACA SE VALIDA QUE SE HALLAN DIGITADO TODOS LOS DATOS NECESARIOS PARA LA FORMA DE PAGO SELECCIONADA
			$wvalida = "on";
			if ($obliga == "on" and $wtotfpa > 0)
				if ($wobsrec == "" or $wobsrec[1] == " ")
					$wvalida = "off";
				elseif ($wubica == "" or $wubica[1] == " ")
					$wvalida = "off";
				elseif ($wautori[1] == "" or $wautori[1] == " ")
					$wvalida = "off";
				elseif ($wdocane[1] == "" or $wdocane[1] == " ")
					$wvalida = "off";

			if ($wvalida == "off") {
				$data['error'] = 1;
				$data['mensaje'] = "FALTAN DATOS POR REGISTRAR PARA ESTA FORMA DE PAGO";
			}

			if ($wvalida == "on") {
				$wvaltar = $wtotfpa * (-1); //pongo el valor negativo para los abonos
				$wprocod = "";
				$wpronom = "";
				$wnromvto = "";
				$wconmvto = "";
				$wtipfac = "ABONO";

				if (!isset($wno2))
					$wno2 = "";

				if (!isset($wccogra) || trim($wccogra) == "")
					$wccogra = $wcco;

				//SEPTIEMBRE 5  DE 2007:
				$waprovecha = "off";

				//GRABAR EN LA TABLA DE CARGOS ======================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
				$q = " INSERT INTO " . $wbasedato . "_000106 (   Medico       ,   Fecha_data ,   Hora_data,   tcarusu     ,   tcarhis       ,   tcaring  ,   tcarfec    ,   tcarsin ,   tcarres                 ,   tcarno1 ,   tcarno2  ,   tcarap1 ,   tcarap2 ,   tcardoc ,   tcarser    ,   tcarconcod ,   tcarconnom ,   tcarprocod ,   tcarpronom ,   tcartercod ,   tcarternom ,   tcarterpor ,   tcarcan      ,   tcarvun    ,   tcarvto                      ,   tcarrec       ,   tcarfac        ,   tcartfa    ,tcarest,   tcarnmo     ,   tcarcmo     ,   tcarapr       , Seguridad) "
					. "                            VALUES ('" . $wbasedato . "','" . $wfecha . "' ,'" . $hora . "' ,'" . $wusuario . "','" . $whistoria . "' ,'" . $wing . "' ,'" . $wfeccar . "','" . $wser . "','" . $wcodemp . "-" . $wnomemp . "','" . $wno1 . "','" . $wno2 . "' ,'" . $wap1 . "','" . $wap2 . "','" . $wdoc . "','" . $wccogra . "','" . $wcodcon . "','" . $wnomcon . "','" . $wprocod . "','" . $wpronom . "','" . $wcodter . "','" . $wnomter . "','" . $wporter . "','" . $wcantidad . "','" . $wvaltar . "','" . round($wcantidad * $wvaltar) . "','" . $wrecexcfpa . "','" . $wfacturable . "','" . $wtipfac . "','on'   ,'" . $wnromvto . "','" . $wconmvto . "','" . $waprovecha . "', 'C-" . $wusuario . "')";
				$res2 = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$wid = mysql_insert_id();   //Esta funci�n devuelve el id despues de un insert, siempre y cuando el campo sea de autoincremento

				//**************************
				//Aca grabo la auditoria
				//**************************
				$q = " INSERT INTO " . $wbasedato . "_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu      , Seguridad) "
					. "                            VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $hora . "' ,'" . $whistoria . "','" . $wing . "','" . $wid . "', 'Grabo','" . $wusuario . "', 'C-" . $wusuario . "')";
				$res2 = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


				//***********************************************************************************************************************************
				//***********************************************************************************************************************************
				//***********************************************************************************************************************************
				//* DESDE ACA GRABO EN LAS TABLAS DE LOS RECIBOS DE CAJA ****************************************************************************
				//***********************************************************************************************************************************
				//***********************************************************************************************************************************
				//***********************************************************************************************************************************
				//ACA ACTUALIZO Y TOMO EL <CONSECUTIVO> DE **** RECIBOS ****
				//EN LA TABLA DE CENTROS DE COSTO **************************
				$q = "lock table " . $wbasedato . "_000003 LOW_PRIORITY WRITE";
				$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$q =  " UPDATE " . $wbasedato . "_000003 "
					. "    SET ccorci = ccorci + 1 "         //Consecutivo Recibo de Caja Inicial
					. "  WHERE ccocod = '" . $wcco . "'"
					. "    AND ccorci < ccorcf "
					. "    AND ccoest = 'on' ";

				$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$q = "SELECT ccofrc, ccorci "                //Fuente Recibo de Caja y Consecutivo
					. "  FROM " . $wbasedato . "_000003 "
					. " WHERE ccocod='" . $wcco . "'"
					. "   AND ccoest = 'on' ";
				$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$row = mysql_fetch_array($err);
				$wfuerec   = $row[0];               //Fuente Recibo de Caja
				$wnrorec   = $row[1];               //Consecutivo recibo de caja

				$q = " UNLOCK TABLES";
				$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				//***********************************************************************************************************************************


				//===================================================================================================================================
				//===================================================================================================================================
				//GRABO EN LA TABLA DEL -- <ENCABEZADO> -- EN EL **** RECIBO DE CAJA SIN FACTURA ****
				$q = " INSERT INTO " . $wbasedato . "_000020 (   Medico       ,   Fecha_data,   Hora_data,  renfue    ,  rennum    ,  renvca                            ,   rencod     ,   rennom     ,   rencaj,      renusu      ,   rencco  , renest,   renfec    , renobs, Seguridad        ) "
					. "                            VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $hora . "' ," . $wfuerec . "," . $wnrorec . "," . number_format($wtotfpa, 0, '.', '') . ",'" . $wcodemp . "','" . $wnomemp . "','" . $wcaja . "','" . $wusuario . "','" . $wcco . "', 'on'  ,'" . $wfecha . "', ''    , 'C-" . $wusuario . "')";
				$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


				//===================================================================================================================================
				//===================================================================================================================================
				//GRABO EN LA TABLA DEL -- <DETALLE DE FACTURAS> -- EN EL **** RECIBO DE CAJA SIN FACTURA ****
				$q = " INSERT INTO " . $wbasedato . "_000021 (   Medico       ,   Fecha_data,   Hora_data,  rdefue    ,  rdenum     ,  rdecco  , rdefac, rdevta,   rdevca                            ,  rdehis      ,  rdeing , rdeffa, rdecon, rdevco, rdesfa, rdeest,   rdereg, Seguridad        ) "
					. "                           VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $hora . "' ," . $wfuerec . "," . $wnrorec . ",'" . $wcco . "', ''    , ''    , " . number_format($wtotfpa, 0, '.', '') . "," . $whistoria . "," . $wing . ", ''    , ''    , 0     , 0     ,'on'   , " . $wid . ", 'C-" . $wusuario . "')";
				$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());


				$wvalcam = 0;
				////////////////////////////////////////////////////////////////////////
				///ACA TRAIGO CADA UNA DE LAS FORMA DE PAGO DEL RECIBO DE CAJA



				//===================================================================================================================================
				//===================================================================================================================================
				//GRABO EN LA TABLA DEL -- <DETALLE DE FORMAS DE PAGO> -- EN EL ** RECIBO DE CAJA **
				$wbanco_des = explode("-", $wbandes);
				$q = " INSERT INTO " . $wbasedato . "_000022 (   Medico       ,   Fecha_data,   Hora_data,  rfpfue    ,  rfpnum    ,   rfpcco  ,   rfpfpa   ,  rfpvfp  ,   rfpdan,      rfpobs  ,   rfppla    ,   rfpaut   , rfpecu, rfpest,    rfpcaf   ,    rfpbai           ,    rfpban           , Seguridad        ) "
					. "                            VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $hora . "' ," . $wfuerec . "," . $wnrorec . ",'" . $wcco . "','" . $wcfpa . "'," . $wvfpa . ",'" . $wdane . "','" . $wobs . "','" . $wplaza . "','" . $wauto . "', 'S'   , 'on'  , '" . $wcaja . "', '" . $wbanco_des[0] . "', '" . $wbanco_des[0] . "', 'C-" . $wusuario . "')";
				$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		} else {


			$wvalfpa = str_replace(",", "", $wvalfpa);    //Le quito el formato al n�mero
			$wtotfpa = $wvalfpa;

			//ACA SE VALIDA QUE SE HALLAN DIGITADO TODOS LOS DATOS NECESARIOS PARA LA FORMA DE PAGO SELECCIONADA
			$wvalida = "on";

			//echo "obliga:".$obliga."----obserec:".$wobsrec."----ubicacion".$wubica."------autori".$wautori."---wdocane:".$wdocane;
			if ($wvalfpa == '' or  $wvalfpa == ' '  or  $wvalfpa == 0)
				$wvalida = "off";
			if ($obliga == "on" and $wtotfpa > 0)
				if ($wobsrec == "" or $wobsrec == " ")
					$wvalida = "off";
				elseif ($wubica == "" or $wubica == " ")
					$wvalida = "off";
				elseif ($wautori == "" or $wautori == " ")
					$wvalida = "off";
				elseif ($wdocane == "" or $wdocane == " ")
					$wvalida = "off";

			if ($wvalida == "off") {
				$data['error'] = 1;
				$data['mensaje'] = 'FALTAN DATOS POR REGISTRAR PARA ESTA FORMA DE PAGO';
			}

			if ($wvalida == "on") {
				$wvaltar = $wtotfpa * (-1); //Coloco el valor negativo para los abonos
				$wprocod = "";
				$wpronom = "";
				$wnromvto = "";
				$wconmvto = "";
				$wtipfac = "ABONO";

				if (!isset($wno2))
					$wno2 = "";

				if (trim($wccogra) == "")
					$wccogra = $wcco;

				//SEPTIEMBRE 5  DE 2007:
				if (!isset($waprovecha))
					$waprovecha = "off";

				//GRABAR EN LA TABLA DE CARGOS ======================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================================
				$q = " INSERT INTO " . $wbasedato . "_000106 (   Medico       ,   Fecha_data ,   Hora_data,   tcarusu     ,   tcarhis       ,   tcaring  ,   tcarfec    ,   tcarsin ,   tcarres                 ,   tcarno1 ,   tcarno2  ,   tcarap1 ,   tcarap2 ,   tcardoc ,   tcarser    ,   tcarconcod ,   tcarconnom ,   tcarprocod ,   tcarpronom ,   tcartercod ,   tcarternom ,   tcarterpor ,   tcarcan      ,   tcarvun    ,   tcarvto                      ,   tcarrec       ,   tcarfac        ,   tcartfa    ,tcarest,   tcarnmo     ,   tcarcmo     ,   tcarapr       , Seguridad) "
					. "                            VALUES ('" . $wbasedato . "','" . $wfecha . "' ,'" . $hora . "' ,'" . $wusuario . "','" . $whistoria . "' ,'" . $wing . "' ,'" . $wfeccar . "','" . $wser . "','" . $wcodemp . "-" . $wnomemp . "','" . $wno1 . "','" . $wno2 . "' ,'" . $wap1 . "','" . $wap2 . "','" . $wdoc . "','" . $wccogra . "','" . $wcodcon . "','" . $wnomcon . "','" . $wprocod . "','" . $wpronom . "','" . $wcodter . "','" . $wnomter . "','" . $wporter . "','" . $wcantidad . "','" . $wvaltar . "','" . round($wcantidad * $wvaltar) . "','" . $wrecexcfpa . "','" . $wfacturable . "','" . $wtipfac . "','on'   ,'" . $wnromvto . "','" . $wconmvto . "','" . $waprovecha . "', 'C-" . $wusuario . "')";
				$res2 = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

				$wid = mysql_insert_id();   //Esta funci�n devuelve el id despues de un insert, siempre y cuando el campo sea de autoincremento

				//**************************
				//Aca grabo la auditoria
				//**************************
				$q = " INSERT INTO " . $wbasedato . "_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg  , audacc ,   audusu      , Seguridad) "
					. "                            VALUES ('" . $wbasedato . "','" . $wfecha . "','" . $hora . "' ,'" . $whistoria . "','" . $wing . "','" . $wid . "', 'Grabo','" . $wusuario . "', 'C-" . $wusuario . "')";
				$res2 = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			}
		}
		return $data;
	}

	//------------------------------------------------------------------------------------
	//	Funcion que trae toda la informacion relacionada a un concepto de facturacion
	//------------------------------------------------------------------------------------
	function datos_desde_concepto($wcodcon, $wcodemp, $wtar, $ccoUbiActualPac)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $wuse;

		// --> array respuesta
		$data					= array();
		$data['warctar'] 		= '';
		$data['option_select'] 	= '';

		$q =  " SELECT grucod, grudes, gruarc, gruser, grutip, grumva, gruinv, gruabo, grutab, grumca
				  FROM " . $wbasedato . "_000200
				 WHERE gruest = 'on'
				   AND grucod = '" . $wcodcon . "'
			       AND gruser in ('A','H')
				 ORDER BY grudes ";

		$res = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
		$row = mysql_fetch_array($res);


		$wcodcon = $row['grucod'];   //Codigo del concepto
		$wnomcon = $row['grudes'];   //Nombre del concepto
		$wconser = $row['gruser'];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
		$wcontab = $row['grutab'];   //Tipo de Abono
		$wconmca = $row['grumca'];   //indica si el concepto mueve caja

		$data['wcontip'] = $row['grutip'];   //Tipo de concepto (P)ropio o (C)ompartido
		$data['warctar'] = $row['gruarc'];   //Archivo para validar las tarifas
		$data['wconabo'] = $row['gruabo'];   //indica si es un concepto de abono
		$data['wconmva'] = $row['grumva'];   //Indica si el valor se puede colocar al momento de grabar el cargo
		$data['wconinv'] = $row['gruinv'];   //Indica si mueve inventarios
		$data['wconser'] = $row['gruser'];   //Tipo de servicio (P)OS, (H)OSPITALARIO o (A)MBOS
		$data['wconmca'] = $row['grumca'];   //indica si el concepto mueve caja
		$wconabo = $data['wconabo'];
		$option_select = '';
		$option_select = cargar_cco($wcodcon, $wcodemp, $wconabo, $numcco, $ccoUbiActualPac);
		$data['option_select'] = $option_select;

		// --> Para los conceptos de inventario validar si se est� actualizando el cron de tarifas de medicamentos y materiales
		if ($data['wconinv'] == 'on') {
			$resAct = (array) actualizandoTarifaMedicamentos();
			$data 	= array_merge($data, $resAct);
		} else
			$data['actualizando'] = false;

		return $data;
	}

	function traer_datos_generales()
	{
		$data = array();
		$data['fecha_actual'] = date("Y-m-d");

		return $data;
	}
	//------------------------------------------------------------------------------
	//	Funcion que muestra el detalle de la cuenta resumido y en acordeones
	//------------------------------------------------------------------------------
	function detalle_cuenta_resumido($whistoria, $wing, $permiteAnularCargo = 'off')
	{
		global $conex;
		global $wbasedato;
		global $wemp_pmla;
		global $wuse;

		$array_paq_cargados 	= array();
		$array_cuenta_cargos	= array();
		$array_cargParalelos	= array();
		$usuariosConsultados	= array();

		$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
		$conceptoEstancia = trim(consultarAliasPorAplicacion($conex, $wemp_pmla, 'concepto_estancia'));

		// --> Obtener si el usuario es administrador
		$qAdminUse = " SELECT Cjeadm
					 FROM " . $wbasedato . "_000030
					WHERE Cjeusu = '" . $wuse . "'
					  AND Cjeest = 'on'
	";
		$ResAdminUse = mysql_query($qAdminUse, $conex) or die("Error en el query: " . $qAdminUse . "<br>Tipo Error:" . mysql_error());
		if ($RowAdminUse = mysql_fetch_array($ResAdminUse))
			$useAdministrador = $RowAdminUse['Cjeadm'];
		else
			$useAdministrador = 'off';

		// --> Array de centros de costos por empresa
		$arrayCco = Obtener_array_cco();

		// --> Obtener los cargos grabados al paciente
		$q_get_cargos = "
	   SELECT A.id as Registro, Tcarfec, Tcarser, Tcarconcod, Tcarconnom, Tcarprocod, Tcarpronom, Tcartercod, Tcarternom, Tcarpor, Empcod, Empnom,
			  Tcartfa, Tcarcan, Tcarvun, Tcarvto, Tcarrec, Tcarfac, Tcarfex, Tcarvre, Tcarvex, Tcarfre, Tcaridp, Tcarpar, Tcarppr, Tcaraun, Tcaraud,
			  Tcardev, Tcarreg, Tcarusu, Tcardun, Tcarfun, Tcarlun, Tcarvro, Audrcu, Gruinv, Audpol, A.Seguridad as usuReg
		 FROM " . $wbasedato . "_000106 as A INNER JOIN " . $wbasedato . "_000024 AS B ON (A.Tcarres = B.Empcod) INNER JOIN " . $wbasedato . "_000107 AS C ON (A.id = C.Audreg), " . $wbasedato . "_000200
		WHERE Tcarhis 	= '" . $whistoria . "'
		  AND Tcaring 	= '" . $wing . "'
		  AND Tcarest 	= 'on'
		  AND Tcarconcod= Grucod
	 ORDER BY Tcarconnom, Tcarfec DESC, Registro DESC
	";

		$res_get_cargos = mysql_query($q_get_cargos, $conex) or die("Error en el query: " . $q_get_cargos . "<br>Tipo Error:" . mysql_error());
		while ($row_get_cargos = mysql_fetch_array($res_get_cargos)) {
			// --> Crear un array con la informacion organizada
			$inf_cargo['CodProcedi']		= $row_get_cargos['Tcarprocod'];
			$inf_cargo['NomProcedi']		= $row_get_cargos['Tcarpronom'];
			$inf_cargo['Fecha'] 			= $row_get_cargos['Tcarfec'];
			$inf_cargo['Servicio'] 			= $row_get_cargos['Tcarser'];
			$inf_cargo['NomServicio'] 		= ((array_key_exists($row_get_cargos['Tcarser'], $arrayCco)) ? $arrayCco[$row_get_cargos['Tcarser']] : '?');
			$inf_cargo['CodTercero']		= $row_get_cargos['Tcartercod'];
			$inf_cargo['NomTercero']		= $row_get_cargos['Tcarternom'];
			$inf_cargo['TipoFact']			= $row_get_cargos['Tcartfa'];
			$inf_cargo['Devolucion']		= $row_get_cargos['Tcardev'];
			$inf_cargo['yaRegrabado']		= $row_get_cargos['Tcarreg'];
			$inf_cargo['Cantidad']			= $row_get_cargos['Tcarcan'];
			$inf_cargo['ValorUn']			= $row_get_cargos['Tcarvun'];
			$inf_cargo['ValorTo']			= $row_get_cargos['Tcarvto'];
			$inf_cargo['ValorRecargo']		= $row_get_cargos['Tcarvro'];
			$inf_cargo['ValorRe']			= $row_get_cargos['Tcarvre'];
			$inf_cargo['ValorEx']			= $row_get_cargos['Tcarvex'];
			$inf_cargo['Tcarpor']			= $row_get_cargos['Tcarpor'];
			$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
			$inf_cargo['ReconExced']		= $row_get_cargos['Tcarrec'];
			$inf_cargo['Facturable']		= $row_get_cargos['Tcarfac'];
			$inf_cargo['CodUsuario']		= $row_get_cargos['Tcarusu'];
			$inf_cargo['codEntidad']		= $row_get_cargos['Empcod'];
			$inf_cargo['Entidad']			= utf8_encode($row_get_cargos['Empnom']);
			$inf_cargo['FactuExcede']		= $row_get_cargos['Tcarfex'];
			$inf_cargo['FacturadoReconoci']	= $row_get_cargos['Tcarfre'];
			$inf_cargo['ConceptoInventar']	= $row_get_cargos['Gruinv'];
			$inf_cargo['Registro']			= $row_get_cargos['Registro'];
			$inf_cargo['GraboParalelo']		= $row_get_cargos['Tcarpar'];
			$inf_cargo['idParalelo']		= $row_get_cargos['Tcaridp'];
			$inf_cargo['pendienteRevicion']	= $row_get_cargos['Tcarppr'];
			$inf_cargo['politicaAplico']	= $row_get_cargos['Audpol'];
			$inf_cargo['pendienteRegrabar']	= $row_get_cargos['Tcarreg'];
			$inf_cargo['actualizadoUnixC']	= $row_get_cargos['Tcaraun'];
			$inf_cargo['actualizadoUnixD']	= $row_get_cargos['Tcaraud'];
			$inf_cargo['usuarioRegrabo']	= explode("-", $row_get_cargos['usuReg']);
			$inf_cargo['usuarioRegrabo']	= $inf_cargo['usuarioRegrabo'][1];
			$facturado						= (($row_get_cargos['Tcarfex'] == 0 && $row_get_cargos['Tcarfre'] == 0) ? 'no_facturado' : 'facturado');

			// --> Tooltip de la fuente y documento
			if ($inf_cargo['ConceptoInventar'] == 'on') {
				$inf_cargo['tooltipDocFue'] = "
				<table>
					<tr align=center><td>Documento</td><td>Fuente</td><td>Linea</td></tr>
					<tr style=font-weight:normal><td>" . $row_get_cargos['Tcardun'] . "</td><td>" . $row_get_cargos['Tcarfun'] . "</td><td>" . $row_get_cargos['Tcarlun'] . "</td></tr>
				</table>";
			} else {
				$inf_cargo['tooltipDocFue'] = "
				<table style=font-weight:normal>
					<tr align=center><td><b>Cardetreg</b></td></tr>
					<tr ><td>" . $row_get_cargos['Audrcu'] . "</td></tr>
				</table>";
			}

			// --> Obtener la especialidad del tercero
			$inf_cargo['nomEspecialidad'] = '';
			if (trim($inf_cargo['CodTercero']) != '') {
				$sqlEspe = " SELECT Espnom
						   FROM " . $wbasedato . "_000159, " . $wbasedato_movhos . "_000044
						  WHERE Terrel = '" . $inf_cargo['Registro'] . "'
						    AND Teresp = Espcod
			";
				$resEspe = mysql_query($sqlEspe, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlEspe):</b><br>" . mysql_error());
				if ($rowEspe = mysql_fetch_array($resEspe))
					$inf_cargo['nomEspecialidad'] = $rowEspe['Espnom'];
			}

			// --> Obtener el nombre del usuario que grabo el cargo
			$inf_cargo['Usuario'] = '';
			if (trim($inf_cargo['CodUsuario']) != '') {
				if (array_key_exists($inf_cargo['CodUsuario'], $usuariosConsultados))
					$inf_cargo['Usuario'] = $usuariosConsultados[$inf_cargo['CodUsuario']];
				else {
					$sqlNomUsu = "SELECT Descripcion
								FROM usuarios
							   WHERE Codigo = '" . $inf_cargo['CodUsuario'] . "'
				";
					$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlNomUsu):</b><br>" . mysql_error());
					if ($rowNomUsu = mysql_fetch_array($resNomUsu)) {
						$inf_cargo['Usuario'] 							= $rowNomUsu['Descripcion'];
						$usuariosConsultados[$inf_cargo['CodUsuario']] 	= $rowNomUsu['Descripcion'];
					}
				}
			}

			// --> Obtener el nombre del usuario que REGRABO el cargo
			$inf_cargo['nomRegrabo'] = '';
			if (trim($inf_cargo['usuarioRegrabo']) != '' && $inf_cargo['pendienteRegrabar'] == "REG") {
				if (array_key_exists($inf_cargo['usuarioRegrabo'], $usuariosConsultados))
					$inf_cargo['nomRegrabo'] = $usuariosConsultados[$inf_cargo['usuarioRegrabo']];
				else {
					$sqlNomUsu = "SELECT Descripcion
								FROM usuarios
							   WHERE Codigo = '" . $inf_cargo['usuarioRegrabo'] . "'
				";
					$resNomUsu = mysql_query($sqlNomUsu, $conex) or die("<b>ERROR EN QUERY MATRIX (sqlNomUsu):</b><br>" . mysql_error());
					if ($rowNomUsu = mysql_fetch_array($resNomUsu)) {
						$inf_cargo['nomRegrabo']							= $rowNomUsu['Descripcion'];
						$usuariosConsultados[$inf_cargo['usuarioRegrabo']] 	= $rowNomUsu['Descripcion'];
					}
				}
			}

			$array_cuenta_cargos[$facturado][$row_get_cargos['Tcarconcod']]['NomConcepto'] = $row_get_cargos['Tcarconnom'];
			$array_cuenta_cargos[$facturado][$row_get_cargos['Tcarconcod']]['InfConcepto'][$row_get_cargos['Registro']] = $inf_cargo;

			// --> Array para saber cuales son los cargos grabados en paralelo
			if ($row_get_cargos['Tcarpar'] == 'on' && $row_get_cargos['Tcaridp'] > 0)
				$array_cargParalelos[$row_get_cargos['Tcaridp']] = '';
		}

		// --> Pintar informacion
		echo "
	<table width='100%' id='detalleCargo'>
		<tr>
			<td colspan='17' align='center'>
				<table style='font-size: 7pt;font-family: verdana;font-weight:bold'>
					<tr><td colspan='9' align='center' class='encabezadoTabla' style='font-size: 8pt;font-family: verdana;'>Convenciones</td></tr>
					<tr>
						<td style='border:1px solid #999999;padding:2px'>
							Cargo facturado: <img width='15' height='15' src='../../images/medical/root/grabar.png'/>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Cargo con paralelo: <img src='../../images/medical/iconos/gifs/i.p.next[1].gif'>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Cargo por revisar: <img width='15' height='15' src='../../images/medical/sgc/Warning-32.png'>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Cargo con pol&iacute;tica: <img width='15' height='15' src='../../images/medical/sgc/Mensaje_alerta.png'>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Cargo regrabado: <img width='15' height='15' src='../../images/medical/sgc/Refresh-128.png'>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Anular cargo: <img src='../../images/medical/eliminar1.png'>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Imprimir cargo: <img src='../../images/medical/sgc/Printer.png' width='18px' height='16px'>
						</td>
						<td style='border:1px solid #999999;padding:3px'>
							Imprimir solicitud de examen: <img src='../../images/medical/sgc/hoja.png' width='16px' height='16px'>
						</td>
						<td style='border:1px solid #999999;padding:3px;background-color:#ffbcbc'>
							Cargo sin integrar
						</td>
					</tr>
				</table><br>
			</td>
		</tr>
	";
		$toltGra = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Cargo grabado&nbsp;</font>";
		$toltAnu = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Anular&nbsp;</font>";
		$toltImp = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/sgc/Printer.png\">&nbsp;Imprimir Soporte&nbsp;</font>";
		$toltReg = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Regrabar&nbsp;</font>";
		$toltImp7 = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Imprimir Soporte para todos los cargos de este concepto&nbsp;</font>";

		// --> No existen cargos en la cuenta del paciente
		if (count($array_cuenta_cargos) == 0) {
			echo "
		<tr>
			<td colspan='17' align='center' style='font-size: 9pt;font-family: verdana;font-weight:bold'>
				<img width='15' height='15' src='../../images/medical/sgc/info.png'>
				No existen cargos grabados al paciente.
			</td>
		</tr>";
		}

		// --> Pintar cargos
		foreach ($array_cuenta_cargos as $tipoFact => $arrCoceptos) {
			// --> Barra de cargos facturados o no facturados
			echo "
		<tr align='left' width='100%'>
			<td colspan='18' style='background-color : #83D8F7;border: 1px solid #999999;font-family: verdana;'>
				&nbsp;<img onclick='desplegar(this, \"" . $tipoFact . "\", \"conceptos\")' valign='middle' style=' display: inline-block; cursor : pointer'  src='../../images/medical/hce/menos.PNG'>
				&nbsp;&nbsp;" . (($tipoFact == 'facturado') ? 'Cargos facturados' : 'Cargos no facturados') . "
			</td>
		</tr>
		<tr align='center' class='" . $tipoFact . " conceptos' style=''>
			<td width='6%' colspan='2'></td>
			<td class='encabezadoTabla'>Fecha</td>
			<td class='encabezadoTabla'>Procedimiento</td>
			<td class='encabezadoTabla'>C.Costos</td>
			<td class='encabezadoTabla'>Tercero</td>
			<td class='encabezadoTabla'>Tipo Fac</td>
			<td class='encabezadoTabla'>Rec/Exc</td>
			<td class='encabezadoTabla'>Fact.</td>
			<td class='encabezadoTabla'>Cantidad</td>
			<td class='encabezadoTabla'>Valor. Uni</td>
			<td class='encabezadoTabla'>Valor. Rec</td>
			<td class='encabezadoTabla'>Valor. Exc</td>
			<td class='encabezadoTabla'>Desc.</td>
			<td class='encabezadoTabla'>Valor. Tot</td>
			<td class='encabezadoTabla'>Entidad</td>
			<td class='encabezadoTabla'>Usuario resp.</td>
			<td class='encabezadoTabla'>Registro</td>
		</tr>";

			$Total_rec_cuenta = 0;
			$Total_exe_cuenta = 0;
			$Total_r_e_cuenta = 0;

			foreach ($arrCoceptos as $codConcepto => $arrInfoConceptos) {
				$Total_rec 		= 0;
				$Total_exe 		= 0;
				$valorTotalCon 	= 0;

				// --> Barra del nombre del concepto
				echo "
			<tr align='left' class='" . $tipoFact . " conceptos' style=''>
				<td width='3%'></td>
				<td colspan='13' class='fondoAmarillo' style='border: 1px solid #999999;'>
					&nbsp;<img class='" . $tipoFact . "-imagen' onclick='desplegar(this, \"" . $tipoFact . '-' . $codConcepto . "\", \"procedimiento\")' valign='middle' style=' display: inline-block; cursor : pointer'  src='../../images/medical/hce/mas.PNG'>
					&nbsp;&nbsp;<b>" . $codConcepto . "-" . $arrInfoConceptos['NomConcepto'] . "</b>
				</td>
				<td id='" . $tipoFact . "-" . $codConcepto . "' class='fondoAmarillo' style='border: 1px solid #999999;' align='right'></td>
				<td colspan='3' class='fondoAmarillo' style='border: 1px solid #999999;'></td>
				<td colspan='2'></td>
				<td><input type='checkbox' tooltip='si' style='cursor:pointer;' ImgImprimirSoporte='' title='" . $toltImp7 . "' onClick='checkearTodos(this, \"ImgImprimirSoporte=" . $codConcepto . "\")' id=''></td>
			</tr>";

				$ColorFila 		= 'fila1';
				foreach ($arrInfoConceptos['InfConcepto'] as $idRegistro => $variables) {
					if (!array_key_exists($idRegistro, $array_cargParalelos)) {
						if ($ColorFila == 'fila1')
							$ColorFila = 'fila2';
						else
							$ColorFila = 'fila1';

						// --> Tooltip para mostrar si se aplico alguna politica
						if ($variables['politicaAplico'] != "") {
							$title = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
								<b>Log Pol&iacute;tica: </b>" . $variables['politicaAplico'] . "&nbsp;
							</span>";
							$infoCargo = "<img tooltip='si' width='15' height='15' src='../../images/medical/sgc/Mensaje_alerta.png' title='" . $title . "'><br>";
						} else
							$infoCargo = "";

						echo "
					<tr class='" . $tipoFact . " " . $tipoFact . '-' . $codConcepto . " procedimiento' style='display:none'>";
						// --> Si el cargo tiene un paralelo relacionado
						if ($variables['GraboParalelo'] == 'on' && array_key_exists($variables['idParalelo'], $array_cargParalelos)) {
							$title = "
						<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
							&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
							Cargo en paralelo&nbsp;
						</span>";
							$infoCargo .= "<img imgParalelo title='" . $title . "' tooltip='si' onClick='verParalelo(this, \"" . $variables['idParalelo'] . "\")' style='cursor:pointer' src='../../images/medical/iconos/gifs/i.p.next[1].gif'>";
							$pintarParalelo = true;
						} else {
							/*if($variables['pendienteRevicion'] == 'CR')
						{
							$title = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\" >
								Pendiente de revision&nbsp;
							</span>";
							$infoCargo.= "<img id ='imagen_redistro_".$idRegistro."' style='cursor:pointer' width='15' title='".$title."' tooltip='si'  onclick='comfirmar_revision(\"".$idRegistro."\")' height='15' src='../../images/medical/sgc/Warning-32.png'>";
						}*/

							if ($variables['pendienteRegrabar'] == "pen" && $conceptoEstancia != $codConcepto && $variables['ConceptoInventar'] != 'on') {
								$title = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\" >
								Pendiente de regrabaci&oacute;n por cambio de tarifa&nbsp;
							</span>";
								$infoCargo .= "<img id ='imagen_redistro_" . $idRegistro . "' style='cursor:pointer' width='15' title='" . $title . "' tooltip='si'  onclick='comfirmar_revision(\"" . $idRegistro . "\")' height='15' src='../../images/medical/sgc/Warning-32.png'>";
							}

							if ($variables['pendienteRevicion'] == 'PT') {
								$title = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\" >
								Pendiente de tarifa&nbsp;
							</span>";
								$infoCargo .= "<img id ='imagen_redistro_" . $idRegistro . "' style='cursor:pointer' width='15' title='" . $title . "' tooltip='si'  height='15' src='../../images/medical/sgc/Warning-32.png'>";
							}

							if ($variables['pendienteRegrabar'] == 'REG') {
								$title = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
								&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\" >
								<b>Regrabado por:</b>&nbsp;" . $variables['nomRegrabo'] . "
							</span>";
								$infoCargo .= "<img style='cursor:info' width='15' title='" . $title . "' tooltip='si'  height='15' src='../../images/medical/sgc/Refresh-128.png'>";
							}

							$pintarParalelo = false;
						}
						// --> Tooltip para la especialidad
						$toolEspe = "";
						if ($variables['nomEspecialidad'] != '') {
							$toolEspe = "
						<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
							&nbsp;<b>Especialidad:</b>&nbsp;" . $variables['nomEspecialidad'] . "&nbsp;
						</span>";
						}

						// --> Tooltip para el nombre del cco
						$toolNomCco = "
						<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
							&nbsp;<b>Cco:</b>&nbsp;" . $variables['NomServicio'] . "&nbsp;
						</span>";

						// --> Tooltip para el codigo de la entidad
						$toolCodEnt = "
						<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
							&nbsp;<b>C&oacute;digo:</b>&nbsp;" . $variables['codEntidad'] . "&nbsp;
						</span>";

						// --> Tooltip para el recargo
						if ($variables['ValorRecargo'] > 0) {
							$toolRecargo = "
							<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 8pt;\">
								&nbsp;<b>Recargo:</b>&nbsp;" . number_format($variables['ValorRecargo'], 0, '.', ',') . "&nbsp;
							</span>";

							$infoRecargo = "<img tooltip='si' title='" . $toolRecargo . "' style='cursor:help' width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;";
						} else
							$infoRecargo = "";

						// --> Si el cargo es de material o medicamentos, verificar si ya se actualiz� en unix, lo que indica si est� integrado o no
						$colorFondoInte = '';

						if ($variables['ConceptoInventar'] == 'on') {
							if ($variables['Devolucion'] == 'on') {
								if ($variables['actualizadoUnixD'] != 'on')
									$colorFondoInte = 'background-color:#ffbcbc';
							} else {
								if ($variables['actualizadoUnixC'] != 'on') {
									$colorFondoInte = 'background-color:#ffbcbc';
								}
							}
						}

						// --> Pintar informacion del cargo
						echo "
						<td></td>
						<td align='right'>" . $infoCargo . "</td>
						<td class='" . $ColorFila . "' align='center'>" . $variables['Fecha'] . "</td>
						<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;" . $colorFondoInte . "' ><span " . (($colorFondoInte != '') ? "blink" : "") . ">" . $variables['CodProcedi'] . "-" . $variables['NomProcedi'] . "</span></td>
						<td class='" . $ColorFila . "' style='cursor:help;' tooltip='si' title='" . $toolNomCco . "' >" . $variables['Servicio'] . "</td>
						<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;cursor:help;' " . (($variables['nomEspecialidad'] != '') ? " tooltip='si' title='" . $toolEspe . "' " : "") . ">
							" . $variables['NomTercero'] . "
						</td>
						<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . (($variables['Devolucion'] == 'on') ? "Devoluci&oacute;n" : $variables['TipoFact']) . "</td>
						<td class='" . $ColorFila . "' align='center'>" . $variables['ReconExced'] . "</td>
						<td class='" . $ColorFila . "' align='center'>" . $variables['Facturable'] . "</td>
						<td class='" . $ColorFila . "' align='center'>" . $variables['Cantidad'] . "</td>
						<td class='" . $ColorFila . "' align='right'>" . number_format($variables['ValorUn'], 0, '.', ',') . "</td>
						<td class='" . $ColorFila . "' align='right'>" . number_format($variables['ValorRe'], 0, '.', ',') . "</td>
						<td class='" . $ColorFila . "' align='right'>" . number_format($variables['ValorEx'], 0, '.', ',') . "</td>
						<td class='" . $ColorFila . "' align='right'>" . $variables['Tcarpor'] . "%</td>
						<td class='" . $ColorFila . "' align='right'>" . $infoRecargo . number_format($variables['ValorTo'], 0, '.', ',') . "</td>
						<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;cursor:help;' tooltip='si' title='" . $toolCodEnt . "'  >" . $variables['Entidad'] . "</td>
						<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . (($variables['Usuario'] != '') ? $variables['Usuario'] : $variables['CodUsuario']) . "</td>
						<td class='" . $ColorFila . "' align='center' tooltip='si' title='" . $variables['tooltipDocFue'] . "'>" . $idRegistro . "</td>
						<td align='center'>";

						if ($tipoFact == 'facturado')
							echo "<img tooltip='si' title='" . $toltGra . "' width='15' height='15' src='../../images/medical/root/grabar.png'/>";
						else {
							$mesCargo 		= explode('-', $variables['Fecha']);
							$cargoMesAct 	= ((date("Y-m") == $mesCargo[0] . "-" . $mesCargo[1]) ? true : false);

							// --> Se permite anular si, El valor del facturado excedente es igual a 0, El valor facturado reconocido es igual 0
							//	   y es un concepto que no mueve inventario y no ha sido facturado el cargo, y es un usuario administrador o el usuario es el
							//	   mismo que grabo el cargo.
							// 	   2016-02-25, Jerson Trujillo:
							//			Se quita la restriccion de que solo anule el usuario que grabo el cargo.
							//			Y se coloca restriccion de que se anule solo lo del mes actual.

							if ($variables['FactuExcede'] == 0 && $variables['FacturadoReconoci'] == 0 && $variables['ConceptoInventar'] != 'on' && $tipoFact == 'no_facturado' && ($useAdministrador == 'on' || ($cargoMesAct && $permiteAnularCargo == 'on')/*|| $variables['CodUsuario'] == $wuse*/))
								echo "<img imgAnular='" . $variables['Registro'] . "' src='../../images/medical/eliminar1.png' tooltip='si' title='" . $toltAnu . "' onclick='abririModalParaAnularCargo(\"" . $variables['Registro'] . "\")' style='cursor:pointer;'>";
						}

						// --> Regrabar el cargo
						echo "
						</td>
						<td align='center'>";
						// --> 2020-03-04: Los conceptos que mueven inventario, no se dejaran regrabar ya que por el integrador
						//		no se puede enviar la tarifa del cargo a devolver y la fecha(En el caso de cargos grabados el mes anterior)
						if ($variables['ConceptoInventar'] != 'on' && $tipoFact == 'no_facturado' && $variables['Devolucion'] != 'on' && $variables['yaRegrabado'] != 'on' && $variables['pendienteRegrabar'] == "pen" && $conceptoEstancia != $codConcepto)
							echo "<input type='checkbox' tooltip='si' regrabar='si' idCargo='" . $idRegistro . "' title='" . $toltReg . "' disabled='disabled'>";

						// --> Icono para imprimir el soporte del cargo
						echo "</td>
						<td align='center'>
							<input type='checkbox' ImgImprimirSoporte='" . $codConcepto . "' codEmpImp='" . $variables['codEntidad'] . "' value='" . $idRegistro . "' tooltip='si' style='cursor:pointer;' title='" . $toltImp . "'>
						</td>
					";

						// <img ImgImprimirSoporte='' src='../../images/medical/sgc/Printer.png' width='20px' height='18px' tooltip='si' title='".$toltImp."' onclick='imprimirSoporteCargo(\"".$variables['Registro']."\",  \"".$whistoria."\", \"".$wing."\")' style='cursor:pointer;'>

						if ($variables['ConceptoInventar'] != 'on') {
							$toltExa = "<font style=\"font-weight:normal\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Imprimir Solicitud de examen&nbsp;</font>";
							// --> Icono para imprimir la solicitud del examen
							echo "</td>
							<td align='center'>
								<img ImgImprimirSolicitudExamen='' src='../../images/medical/sgc/hoja.png' width='17px' height='17px' tooltip='si' title='" . $toltExa . "' onclick='imprimirSolicitudExamen(\"" . $variables['Registro'] . "\")' style='cursor:pointer;'>
							</td>
						</tr>
						";
						}

						// --> Aqui se pinta el cargo paralelo si lo hay.
						if ($pintarParalelo) {
							$varParalelo = $arrInfoConceptos['InfConcepto'][$variables['idParalelo']];
							echo "
						<tr id='" . $variables['idParalelo'] . "' class='" . $tipoFact . " " . $tipoFact . '-' . $codConcepto . " procedimiento-paralelo' style='display:none'>
							<td colspan='2'></td>
							<td class='" . $ColorFila . "' style='border-left: 2px dotted #72A3F3;' align='center'>" . $varParalelo['Fecha'] . "</td>
							<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . $varParalelo['CodProcedi'] . "-" . $varParalelo['NomProcedi'] . "</td>
							<td class='" . $ColorFila . "'>" . $varParalelo['Servicio'] . "</td>
							<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . $varParalelo['NomTercero'] . "</td>
							<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . $varParalelo['TipoFact'] . "</td>
							<td class='" . $ColorFila . "' align='center'>" . $varParalelo['ReconExced'] . "</td>
							<td class='" . $ColorFila . "' align='center'>" . $varParalelo['Facturable'] . "</td>
							<td class='" . $ColorFila . "' align='center'>" . $varParalelo['Cantidad'] . "</td>
							<td class='" . $ColorFila . "' align='right'>" . number_format($varParalelo['ValorUn'], 0, '.', ',') . "</td>
							<td class='" . $ColorFila . "' align='right'>" . number_format($varParalelo['ValorRe'], 0, '.', ',') . "</td>
							<td class='" . $ColorFila . "' align='right'>" . number_format($varParalelo['ValorEx'], 0, '.', ',') . "</td>
							<td class='" . $ColorFila . "' align='right'>" . $varParalelo['Tcarpor'] . "%</td>
							<td class='" . $ColorFila . "' align='right'>" . number_format($varParalelo['ValorTo'], 0, '.', ',') . "</td>
							<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . $varParalelo['Entidad'] . "</td>
							<td class='" . $ColorFila . "' style='font-size: 8pt;font-family: verdana;' >" . $varParalelo['Usuario'] . "</td>
							<td class='" . $ColorFila . "' style='border-right: 2px dotted #72A3F3;' align='center'>" . $varParalelo['Registro'] . "</td>
						</tr>
						";
						}
					}

					$Total_rec += (($variables['Facturable'] == 'S') ? $variables['ValorRe'] : 0);
					$Total_exe += (($variables['Facturable'] == 'S') ? $variables['ValorEx'] : 0);
					$valorTotalCon += (($variables['Facturable'] == 'S') ? $variables['ValorTo'] : 0);
				}

				$html_barra = '<b>$' . number_format($valorTotalCon, 0, '.', ',') . '</b>';

				echo "
			<input type='hidden' class='HiddenTotales' id_barra='" . $tipoFact . "-" . $codConcepto . "' value='" . $html_barra . "'>
			";

				echo "
				<tr class='" . $tipoFact . " " . $tipoFact . "-" . $codConcepto . " procedimiento' style='display:none;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>
					<td colspan='8'></td>
					<td colspan='3' align='right' ><b>TOTALES:&nbsp;</b></td>
					<td align='right'>&nbsp;$" . number_format($Total_rec, 0, '.', ',') . "</td>
					<td align='right'>&nbsp;$" . number_format($Total_exe, 0, '.', ',') . "</td>
					<td align='right'>&nbsp;</td>
					<td align='right'>&nbsp;$" . number_format($valorTotalCon, 0, '.', ',') . "</td>
				</tr>";
				$Total_rec_cuenta += $Total_rec;
				$Total_exe_cuenta += $Total_exe;
				$Total_r_e_cuenta += $valorTotalCon;
			}
			echo "
		<tr class='" . $tipoFact . " conceptos' style='font-size: 9pt;font-family: verdana;'>
			<td colspan='9'></td>
			<td colspan='2' style='color:#2a5db0'><b>TOTALES CUENTA:&nbsp;</b></td>
			<td align='right' style='color:#2a5db0'>&nbsp;<b>$" . number_format($Total_rec_cuenta, 0, '.', ',') . "</b></td>
			<td align='right' style='color:#2a5db0'>&nbsp;<b>$" . number_format($Total_exe_cuenta, 0, '.', ',') . "</b></td>
			<td align='right' style='color:#2a5db0'>&nbsp;</td>
			<td align='right' style='color:#2a5db0'>&nbsp;<b>$" . number_format($Total_r_e_cuenta, 0, '.', ',') . "</b></td>
		</tr>
		<tr>
			<td colspan='20' align='right' style='font-size: 8pt;font-family: verdana;'color:#ffffff;><br>
				Seleccionar todos:<input type='checkbox' tooltip='si' style='cursor:pointer;' title='" . $toltReg . "' onClick='checkearTodos(this, \"regrabar\")' id='seleccionarTodosRegrabar' disabled='disabled'>
			</td>
			<td align='center' style='font-size: 8pt;font-family: verdana;'color:#ffffff;><br>
				<input type='checkbox' ImgImprimirSoporte='' tooltip='si' style='cursor:pointer;' title='" . $toltImp . "' onClick='checkearTodos(this, \"ImgImprimirSoporte\")' id='seleccionarTodosImprimir'>
			</td>
		</tr>";

			$msj  = "<font style=\"font-weight:normal;text-align:justify\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Se regrabaran solo los cargos<br>que esten checkeados&nbsp;</font>";
			$msj2 = "<font style=\"font-weight:normal;text-align:justify\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;Imprimir&aacute; el soporte de los cargos seleccionados&nbsp;</font>";
			echo "
		<tr>
			<td colspan='20' align='center'><br>
				<button tooltip='si' title='" . $msj . "' id='botonRegrabar' style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;display:none' onClick='ventanaReGrabar(this)' >REGRABAR</button>
				&nbsp;<b>|</b>&nbsp;
				<button ImgImprimirSoporte='' tooltip='si' title='" . $msj2 . "' id='botonRegrabar' style='cursor:pointer;font-family: verdana;font-weight:bold;font-size: 8pt;' onClick='imprimirSoporteCargo(\"\", \"" . $whistoria . "\", \"" . $wing . "\", \"\")' >
					<img src='../../images/medical/sgc/Printer.png' width='12px' height='12px'>
					IMPRIMIR SOPORTE
				</button>
			</td>
		</tr>";
		}
		echo "</table>";

		// -->  Div para seleccionar un nuevo responsable para realizar la regrabacion de cargos
		//		Nota: Este div solo es visible en una ventana emergente Dialog, cuando den click a regrabar.
		echo "
	<div id='divRegrabar' align='center' style='display:none;font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 10pt;'>
		<table>
			<tr>
				<td align='center'>
					Seleccione el nuevo responsable al <br>
					cual se le va a grabar el cargo(s).
				</td>
			</tr>
			<tr align='center'>
				<td class='fila2'>
					<SELECT id='respRegrabar' style='width:270px'></SELECT>
				</td>
			</tr>
			<tr>
				<td align='center'><button style='font-family: verdana;font-weight:bold;color: #2A5DB0;font-size: 8pt' onClick='realizarRegrabacion(this)' id='botonIP'>Iniciar Proceso</button></td>
			</tr>
			<tr>
				<td align='center' id='td_progressbar' style='display:none'>
				</td>
			</tr>
			<tr>
				<td align='center' id='msjRegrabacion'></td>
			</tr>
		</table><br>
		<div id='listaCargosRegrabar'>
		</div>
		<br>
	<div>
	";

		// -->  Div para mostrar el mensaje de que la cuenta se encuentra congelada
		echo "
	<div id='divMsjCongelar' align='center' style='display:none;font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 10pt;'>
		<br>
	</div>
	";
	}
	//-----------------------------------------------------------------------
	//	--> Verificar si un medico con su especialidad hacen disponibilidad
	//		y si tiene tarifa para un doble cuadro de turno.
	//		Jerson Trujillo.
	//-----------------------------------------------------------------------
	/*function verificarDisponibilidad()
{
	$respuesta = array('haceDispon'=> '');

	$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

	// --> Validar si el medico y la especialidad hacen disponibilidad
	$sqlDispon = "SELECT Meddoc
					FROM ".$wbasedato_movhos."_000048, ".$wbasedato_movhos."_000044
				   WHERE Meddoc = '".$medico."'
					 AND Medesp = '".$especialidad."'
					 AND Medhdi = 'on'
					 AND Medesp = Espcod
					 AND Esphdi = 'on'
	";
	$resDispon = mysql_query($sqlDispon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlDispon):</b><br>".mysql_error());
	if(mysql_fetch_array($resDispon))
	{
		$respuesta['haceDispon'] = "SI";

		// --> Validar si el medico tiene doble tarifa por dos diferentes cuadros de turnos.

	}
	else
		$respuesta['haceDispon'] = "NO";
}*/
	//------------------------------------------------------
	//	--> Obtener array de los conceptos
	//------------------------------------------------------
	function  obtenerArrayInfoConceptos($muevenInventario = '%')
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;
		global $caracter_ok;
		global $caracter_ma;

		$q_conceptos = "
	SELECT Grucod, Grudes, Gruarc, Grumva
	  FROM " . $wbasedato . "_000200
	 WHERE Gruest = 'on'
	   AND Gruser in ('A','H')
	   AND Gruinv LIKE '%" . $muevenInventario . "%'
	   AND Gruiva <= 0
	 ORDER BY Grudes ";

		$res_conceptos = mysql_query($q_conceptos, $conex) or die("Error: " . mysql_errno() . " - en el query (Consultar conceptos): " . $q_conceptos . " - " . mysql_error());

		$arr_conceptos = array();
		while ($row_conceptos = mysql_fetch_array($res_conceptos)) {
			$row_conceptos['Grudes'] = str_replace($caracter_ma, $caracter_ok, $row_conceptos['Grudes']);
			$arr_conceptos[trim($row_conceptos['Grucod'])]['nombre']  		= $row_conceptos['Grudes'];
			$arr_conceptos[trim($row_conceptos['Grucod'])]['archivo'] 		= $row_conceptos['Grudes'];
			$arr_conceptos[trim($row_conceptos['Grucod'])]['modificaVal'] 	= $row_conceptos['Grumva'];
		}
		return $arr_conceptos;
	}
	//----------------------------------------------------------------------------------------------------------
	//	--> Funcion que genera el html del soporte impreso, para los cargos si mueven inventario
	//----------------------------------------------------------------------------------------------------------
	function quitarAlertaCargosPorRegrabar($historia, $ingreso)
	{
		global $conex;
		global $wemp_pmla;
		global $wbasedato;

		$sqlCar = "
	SELECT count(*) AS CANT
	  FROM " . $wbasedato . "_000106 INNER JOIN " . $wbasedato . "_000200 ON(Tcarconcod = Grucod AND Gruinv != 'on')
	 WHERE Tcarhis = '" . $historia . "'
	   AND Tcaring = '" . $ingreso . "'
	   AND Tcarreg = 'pen'
	   AND Tcarest = 'on'
	";
		$resCar = mysql_query($sqlCar, $conex) or die("Error en el query: " . $sqlCar . "<br>Tipo Error:" . mysql_error());
		$rowCar = mysql_fetch_array($resCar);
		if ($rowCar["CANT"] == 0) {
			$sqlActAlerta = "
		UPDATE " . $wbasedato . "_000282
		   SET Preest = 'off'
		 WHERE Prehis = '" . $historia . "'
		   AND Preing = '" . $ingreso . "'
		";
			mysql_query($sqlActAlerta, $conex) or die("Error en el query: " . $sqlActAlerta . "<br>Tipo Error:" . mysql_error());
		}
	}
	//----------------------------------------------------------------------------------------------------------
	//	--> Funcion que genera el html del soporte impreso, para los cargos si mueven inventario
	//----------------------------------------------------------------------------------------------------------
	function pintarCargosQueMuevenInventario($datosBasicos, $resInfoCar, $numPagina, &$valorTotalMedic, &$valorTotalRecono)
	{
		// --> Encabezado para los cargos que SI mueven inventario
		if (mysql_num_rows($resInfoCar) > 0) {
			$respuesta .= "
		<tr>
			<td  colspan='3' align='left' style='" . (($numPagina > 1) ? "border:1px;border-style:dotted none none none;" : "") . "'>
				<b>RELACION DE MEDICAMENTOS</b>
			</td>
		</tr>
		<tr>
			<td  colspan='3' align='left'>
				<table width='100%' class='doted' style='border:1px;border-style:dotted none dotted none;'>
					<tr style='font-weight:bold' align='center'>
						<td>Articulo</td>
						<td>Descripci�n</td>
						<td align='right'>Cantidad</td>
						<td align='right'>Val. Uni.</td>
						<td align='right'>Val. Total</td>
					</tr>";
		} else
			return $respuesta;


		// --> Pintar cada cargo que SI mueve inventario, solo se pintan 9 cargos por pagina
		$numeroFilas		= 1;
		while ($numeroFilas <= 9 && $rowInfoCar = mysql_fetch_array($resInfoCar)) {
			$valorTotalMedic = $valorTotalMedic + $rowInfoCar['Tcarvto'];
			$valorTotalRecono = $valorTotalRecono + $rowInfoCar['Tcarvre'];

			$respuesta .= "
					<tr>
						<td align='center'>" . $rowInfoCar['Tcarprocod'] . "</td>
						<td align='left' style='font-size:3mm;'>" . $rowInfoCar['Tcarpronom'] . "</td>
						<td align='right'>" . $rowInfoCar['Tcarcan'] . "</td>
						<td align='right'>" . number_format($rowInfoCar['Tcarvun'], 0, '.', ',') . "</td>
						<td align='right'>" . number_format($rowInfoCar['Tcarvto'], 0, '.', ',') . "</td>
					</tr>";

			$numeroFilas++;
		}

		// --> Paginar cuando haya mas de 9 cargos
		if ((mysql_num_rows($resInfoCar) - ($numPagina * 9)) > 0) {
			$numPagina++;

			$respuesta .= "
				</table>
			</td>
		</tr>
		</table>
		<table width='100%' style='font-size:2.5mm;font-weight:600;'>
			<tr><td align='right'>Continua...</td></tr>
		</table>
		<br><br>
		<table width='100%' style='font-size:2.5mm;font-weight:600;'>
			<tr><td align='right'>Pagina " . $numPagina . "</td></tr>
		</table>
		" . $datosBasicos;

			$respuesta .= pintarCargosQueMuevenInventario($datosBasicos, $resInfoCar, $numPagina, $valorTotalMedic, $valorTotalRecono);
		} else {
			$respuesta .= "
				</table>
			</td>
		</tr>
		";
		}

		return $respuesta;
	}

	//=======================================================================================================================================================
	//		F I N	 F U N C I O N E S	 P H P
	//=======================================================================================================================================================

	//=======================================================================================================================================================
	//		F I L T R O S  	 D E  	L L A M A D O S  	P O R  	J Q U E R Y  	O  	A J A X
	//=======================================================================================================================================================
	if (isset($accion)) {
		switch ($accion) {
			case 'cargar_datos': {
					$data = cargar_datos($whistoria, $wing, $wcargos_sin_facturar, $welemento);

					// --> Buscar si el paciente tiene turno de cx
					$sqlTur = "
			SELECT Turtur
			  FROM tcx_000011
			 WHERE Turhis = '" . $whistoria . "'
			   AND Turnin = '" . $data['wwing'] . "'
			   AND Turest = 'on'
			";
					$resTur = mysql_query($sqlTur, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTur):</b><br>" . mysql_error());
					if ($rowTur = mysql_fetch_array($resTur))
						$data['tieneTurnoCx'] = "on";
					else
						$data['tieneTurnoCx'] = "off";

					$data['sqlTur'] = $sqlTur;

					echo json_encode($data);
					break;
					return;
				}
			case 'cargar_datos_caja': {
					$data = cargar_datos_caja();
					echo json_encode($data);
					break;
					return;
				}
			case 'traer_conceptos': {
					echo json_encode(obtenerArrayInfoConceptos($muevenInventario));
					break;
					return;
				}
			case 'traer_datos_generales': {
					$data = traer_datos_generales();
					echo json_encode($data);
					break;
					return;
				}

			case 'datos_desde_concepto': {
					$data = datos_desde_concepto($wcodcon, $wcodemp, $wtar, $ccoUbiActualPac);
					echo json_encode($data);
					break;
					return;
				}

			case 'datos_desde_conceptoxcco': {
					$data = datos_desde_conceptoxcco($wcodcon, $wccogra);

					// --> 2017-09-28: Consultar si el paciente tiene insumos pendientes por aplicar, Jerson Andres Trujillo Cardona
					$data['tieneInsumosPorAplicar'] = "off";
					$wbasedatoMov 					= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

					// -->  Por ahora solo se va a hacer est� validacion si el cco de grabacion es urgencias, para las ayudas no se debe hacer.
					//		NOTA: Queda pendiente activar validacion para hospitalizacion, cuando se este grabando en pisos.
					$sqlCco = "
			SELECT Ccocod
			  FROM " . $wbasedatoMov . "_000011
			 WHERE Ccocod = '" . trim($wccogra) . "'
			   AND Ccourg = 'on'
			";
					$resCco = mysql_query($sqlCco, $conex) or die("Error: " . mysql_errno() . " - en el sqlCco: " . $sqlCco . " - " . mysql_error());
					if (mysql_num_rows($resCco) > 0) {
						$sqlInsumos = "
				SELECT Carins, SUM(Carcca - Carcap - Carcde) as saldo_insumos, Caraux, Descripcion
				  FROM " . $wbasedatoMov . "_000227 LEFT JOIN usuarios on(Caraux = Codigo)
				 WHERE Carhis = '" . $historia . "'
				   AND Caring = '" . $ingreso . "'
				   AND Carcca - Carcap - Carcde > 0
				   AND Carest = 'on'
				   AND Cartra = 'on'
				 GROUP BY Carins";
						$resInsumos = mysql_query($sqlInsumos, $conex) or die("Error: " . mysql_errno() . " - en el sqlInsumos: " . $sqlInsumos . " - " . mysql_error());
						if (mysql_num_rows($resInsumos) > 0) {
							$arrUsuPenApli = array();
							$data['tieneInsumosPorAplicar'] 		= "on";
							$data['listaResponInsumosPorAplicar'] = "<table width='100%'>
						<tr><td class='fila1' align='center'><b>Responsables</b></td></tr>";
							while ($rowInsumos = mysql_fetch_array($resInsumos)) {
								if (!array_key_exists($rowInsumos['Caraux'], $arrUsuPenApli)) {
									$data['listaResponInsumosPorAplicar'] .= "<tr><td align='center' class='fila2' style='font-weight:normal'>" . utf8_encode(strtolower($rowInsumos['Descripcion'])) . "</td></tr>";
									$arrUsuPenApli[$resInsumos['Caraux']] = '';
								}
							}

							$data['listaResponInsumosPorAplicar'] .= "
					</table>";
						}
					}

					echo json_encode($data);
					break;
					return;
				}
			case 'traer_detalle_del_concepto': {
					$Array_proc	= Obtener_array_detalle_concepto($cod_concepto, $ConceptoInventar, $Tarifa);

					if ($ConceptoInventar != 'on') {
						// --> Agregarle al array los procedimientos de la 70
						$Array_proc = obtener_array_procedimientosEmpresa2($conex, $wemp_pmla, $wbasedato, $wcod_empresa, $centroCostos, $Array_proc);
					}

					echo json_encode($Array_proc);
					break;
					return;
				}
			case 'datos_desde_procedimiento': {
					$Data = array();
					// --> Primero validar en las politicas que el concepto-procedimiento sea facturable
					if (ValidarSiEsFacturable($wcodcon, $wprocod, $CodTipEmp, $wtar, $CodNit, $wcodemp, $especialidad, $wccogra, $whis, $wing)) {
						$Data 				= datos_desde_procedimiento($wprocod, $wcodcon, $wccogra, $ccoActualPac, $wcodemp, $wfeccar, $wtipo_ingreso, $especialidad, $cobraHonorarios, true, $medico, $fecha, $hora, $enTurno, $grupoMedico);
						$Data['Facturable'] = 'si';

						// --> Validar si existe alguna excepcion tarifaria que me permita modificar el valor del cargo
						$arrayResExcep 				= medicoExcepcionTarifaria($conex, $wemp_pmla, $wbasedato, $medico, $wcodcon, $wcodemp, $wprocod);
						$Data['excepcionTarifaria'] = $arrayResExcep['excepcionTarifaria'];

						// --> Validar si se debe pedir lote, esto aplica para las vacunas
						$Data['pedirLote'] 	= "off";
						$ccoQueGrabaVacunas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoQueGrabaVacunas');

						// --> 2018-01-11: Por ahora no se pedira el lote desde el programa de cargos, por eso se coloca el false en el if
						if ($ccoQueGrabaVacunas == $wccogra && $wdevol != 'on') {
							// --> Si el concepto es de inventario
							$sqlTipoCon = "
					SELECT count(*)
					  FROM " . $wbasedato . "_000200
					 WHERE Grucod = '" . $wcodcon . "'
					   AND Gruinv = 'on'
					";
							$resTipoCon = mysql_query($sqlTipoCon, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlTipoCon):</b><br>" . $sqlTipoCon . "<br>" . mysql_error());
							$rowTipoCon = mysql_fetch_array($resTipoCon);

							if ($rowTipoCon[0] > 0) {
								$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

								// --> Si el codigo que se va a grabar est� en el maestro de vacunas
								$sqlCodVac = "
						SELECT Vaccom as Cod
						  FROM " . $wbasedato . "_000297
						 WHERE Vaccom = '" . $wprocod . "'
						   AND Vacest = 'on'
						 UNION
						SELECT Artcod as Cod
					      FROM " . $wbasedatoMov . "_000026
					     WHERE Artcod = '" . $wprocod . "'
						   AND Artest = 'on'
					       AND Artvac = 'on'
						";
								$resCodVac = mysql_query($sqlCodVac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCodVac):</b><br>" . $sqlCodVac . "<br>" . mysql_error());
								if ($rowCodVac = mysql_fetch_array($resCodVac))
									$Data['pedirLote'] 	= "on";
							}
						}
					} else {
						$Data['Facturable'] = 'no';
					}

					echo json_encode($Data);
					break;
					return;
				}
			case 'traer_terceros': {
					echo json_encode(obtener_array_terceros_especialidad());
					break;
					return;
				}
			case 'datos_desde_tercero': {
					$data = datos_desde_tercero($wcodter, $wcodesp, $wcodcon, $wtip_paciente, $whora_cargo, $wfecha_cargo, $wtipoempresa, $wtarifa, $wempresa, $wcco, $wcod_procedimiento, $cuadroTurno, $conDisponibilidad);
					echo json_encode($data);
					break;
					return;
				}
			case 'GrabarCargo': {
					$wfecha = date("Y-m-d");
					$whora = date("H:i:s");
					$datos = array();
					$datos['whistoria']		= $whistoria;
					$datos['wing']			= $wing;
					$datos['wno1']			= $wno1;
					$datos['wno2']			= $wno2;
					$datos['wap1']			= $wap1;
					$datos['wap2']			= $wap2;
					$datos['wdoc']			= $wdoc;
					$datos['wcodemp']		= $wcodemp;
					$datos['wnomemp']		= $wnomemp;
					$datos['tipoEmpresa']	= $tipoEmpresa;
					$datos['nitEmpresa']	= $nitEmpresa;
					$datos['tipoPaciente']	= $tipoPaciente;
					$datos['tipoIngreso']	= $tipoIngreso;
					$datos['wser']			= $wser;
					$datos['wfecing']		= $wfecing;
					$datos['wtar']			= $wtar;
					$datos['wcodcon']		= $wcodcon;
					$datos['wnomcon']		= $wnomcon;
					$datos['wprocod']		= $wprocod;
					$datos['wpronom']		= $wpronom;
					$datos['wcodter']		= $wcodter;
					$datos['wnomter']		= $wnomter;
					$datos['wporter']		= $wporter;
					$datos['grupoMedico']	= $grupoMedico;
					$datos['wterunix']		= $wterunix;
					$datos['wcantidad']		= $wcantidad;
					$datos['wvaltar']		= $wvaltar;
					$datos['porDescuento']	= $porDescuento;
					$datos['wrecexc']		= $wrecexc;
					$datos['wfacturable']	= $wfacturable;
					$datos['wcco']			= $wcco;
					$datos['wccogra']		= $wccogra;
					$datos['wfeccar']		= (($wfeccar < $wfecing) ? $wfecing : $wfeccar);
					$datos['whora_cargo']	= $whora_cargo . ':00';
					$datos['wconinv']		= $wconinv;
					$datos['wconabo']		= $wconabo;
					$datos['wdevol']		= $wdevol;
					$datos['waprovecha']	= $waprovecha;
					$datos['wconmvto']		= (isset($wconmvto)) ? $wconmvto : '';
					$datos['wexiste']		= $wexiste;
					$datos['wbod']			= $wbod;
					$datos['wconser']		= $wconser;
					$datos['wtipfac']		= $wtipfac;
					$datos['wexidev']		= (isset($wexidev)) ? $wexidev : '';
					$datos['wfecha']		= $wfecha;
					$datos['whora']			= $whora;
					$datos['nomCajero']		= $nomCajero;
					$datos['cobraHonorarios']		= $cobraHonorarios;
					$datos['wespecialidad']			= ((!isset($wespecialidad) || $wespecialidad == '') ? '*' : $wespecialidad);
					$datos['wgraba_varios_terceros'] = $wgraba_varios_terceros;
					$datos['wcodcedula']			= (isset($wcodcedula)) ? $wcodcedula : '';
					$datos['estaEnTurno']			= $estaEnTurno;
					$datos['tipoCuadroTurno']		= $tipoCuadroTurno;
					$datos['ccoActualPac']			= $ccoActualPac;
					$datos['codHomologar']			= $codHomologar;
					$datos['validarCondicMedic']	= TRUE;
					$datos['desdeCargosIps']		= TRUE;
					$datos['respuesta_array']		= 'on';
					$datos['wvaltarExce']			= 0;
					$datos['desde_CargosPDA']		= false;
					$datos['wpaquete']				= '';
					$datos['wcodpaq']				= '';
					$datos['habitacion']			= '';
					$datos['fecIngHab']				= '';
					$datos['horIngHab']				= '';
					$datos['diasFacturados']		= '';
					$datos['diasEstancia']			= '';
					$datos['fecEgrHab']				= '';
					$datos['horEgrHab']				= '';
					$datos['wnromvto']				= '';
					$datos['pendRevicion']			= '';
					$datos['idCargosAnexos']		= '';
					$datos['logRegistroCargo']		= '';
					$datos['pendRevicion']			= '';
					$datos['politicaAplico']		= '';
					$datos['politicaTercero']		= $codigoPolitica;
					$datos['permGrabarCargoCcoDifPda']		= "on";
					$datos['respuesta_array']		= true;
					$datos['aplicarRecago']			= $aplicarRecago;
					$datos['loteVacuna']			= $loteVacuna;
					$datos['codigoRips']			= $codigoRips;

					$codEmpParticular = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');

					// --> 	$tarifaDigitada == 'on', indica que el cargo fue grabado con una tarifa digitada por el grabador, por eso se marca con este estado para
					//		que el monitor lo lea y le creen la tarifa, esto no aplica para los pacientes con tarifa particular
					$datos['estadoMonitor']			= (($tarifaDigitada == 'si' && $wcodemp != $codEmpParticular) ? 'PT' : '');

					// --> Si la empresa es particular esto se graba como excedente
					if ($wcodemp == $codEmpParticular)
						$datos['wrecexc'] = 'R';

					// --> Valor excedente
					if ($datos['wrecexc'] == 'E')
						$datos['wvaltarExce'] = round($wcantidad * $wvaltar);
					// --> Valor reconocido
					else
						$datos['wvaltarReco'] = round($wcantidad * $wvaltar);

					// --> Validar si se debe redondear a la centena valores totales, Jerson Trujillo 2016-02-24
					$tiposEmpRedondean 	=	consultarAliasPorAplicacion($conex, $wemp_pmla, 'tiposEmpresaQueRedondeanValorCargosFI');
					$tiposEmpRedondean	= 	explode(',', $tiposEmpRedondean);
					if (in_array($tipoEmpresa, $tiposEmpRedondean) && $wconinv != 'on') {
						$datos['wvaltarExce'] = round($datos['wvaltarExce'], -2);
						$datos['wvaltarReco'] = round($datos['wvaltarReco'], -2);
					}

					// Si se est� grabando un insumo a un ingreso inactivo, se deben agregar par�metros adicionales para que al pasar esos insumo a unix
					// no queden con �ltimo ingreso activo sino con el ingreso inactivo con el que se est� grabando el cargo, pues el integrador siempre
					// va a grabar a unix con el �ltimo ingreso activo.
					validar_cargo_ingreso_inactivo($conex, $wemp_pmla, $wbasedato, $datos);

					$respuesta = validar_y_grabar_cargo($datos, false);

					echo json_encode($respuesta);
					break;
					return;
				}
			case 'grabarabono': {
					$datos = array();
					$datos['whistoria']		= $whistoria;
					$datos['wing']			= $wing;
					$datos['wfeccar']		= $wfeccar;
					$datos['wser']			= $wser;
					$datos['wcodemp']		= $wcodemp;
					$datos['wnomemp']		= $wnomemp;
					$datos['wno1']			= $wno1;
					$datos['wno2']			= $wno2;
					$datos['wap1']			= $wap1;
					$datos['wap2']			= $wap2;
					$datos['wdoc']			= $wdoc;
					$datos['wccogra']		= $wccogra;
					$datos['wcodcon']		= $wcodcon;
					$datos['wnomcon']		= $wnomcon;
					$datos['wprocod']		= $wprocod;
					$datos['wpronom']		= $wpronom;
					$datos['wcodter']		= $wcodter;
					$datos['wnomter']		= $wnomter;
					$datos['wvaltar'] 		= $wvaltar;
					$datos['wrecexcfpa']	= $wrecexcfpa;
					$datos['wcco'] 			= $wcco;
					$datos['wfacturable']	= $wfacturable;
					$datos['wtipfac']		= $wtipfac;
					$datos['waprovecha']	= $waprovecha;
					$datos['wcaja']			= $wcaja;
					$datos['wobsrec'] 		= $wobsrec;
					$datos['wvalfpa']		= $wvalfpa;
					$datos['obliga']		= $obliga;
					$datos['wubica']		= $wubica;
					$datos['wautori']		= $wautori;
					$datos['wdocane']		= $wdocane;
					$datos['wconmca']		= $wconmca;

					$data = grabarabono($datos);
					echo json_encode($data);

					break;
					return;
				}
			case 'mostrar_cargos_grabados': {
					global $wuse;
					$data = mostrar_cargos_grabados($whistoria, $wing, $wcajadm, $wuse, $wcargos_sin_facturar);
					echo json_encode($data);
					break;
					return;
				}
			case 'anular': {
					$respuesta = anular($wid, 'ANULO', $causaAnulacion, $justificacionAnulacion);

					echo json_encode($respuesta);
					break;
					return;
				}
			case 'validarSiCargoEstaFacturado': {
					$respuesta 		= array('Error' => false, 'Mensaje' => '');
					$respuestaVer 	= verificarCargoFacturado($idCargo);
					if ($respuestaVer == 'on') {
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= 'El cargo no se puede anular porque ya est&aacute; facturado';
					}

					echo json_encode($respuesta);
					break;
					return;
				}
			case 'grabaIva': {
					$respuesta 		= array('Error' => false, 'Mensaje' => '');
					$valIva			= str_replace(",", "", $valIva);
					$ccoIva			= consultarAliasPorAplicacion($conex, $wemp_pmla, 'ccoParaGrabarCargoDeIva');

					// --> Variables basicas para grabar un cargo en unix
					$variablesUnix 					= array();
					$variablesUnix['historia'] 		= $historia;
					$variablesUnix['ingreso'] 		= $ingreso;
					$variablesUnix['fechaCargo'] 	= date("Y-m-d");
					$variablesUnix['horaCargo'] 	= date("H:i:s");
					$variablesUnix['porcenTercero']	= "";
					$variablesUnix['cantidad']		= "1";
					$variablesUnix['valorUnitario']	= $valIva;
					$variablesUnix['valorTotal']	= $valIva;
					$variablesUnix['recExe']		= 'R';
					$variablesUnix['facturable']	= 'S';
					$variablesUnix['festivo']		= 'N';
					$variablesUnix['valorRecargo']	= '0';
					$variablesUnix['valorExceden']	= '0';
					$variablesUnix['valorReconoc']	= '0';
					$variablesUnix['ccoActualPac']	= $ccoIva;
					$variablesUnix['terceroUnix']	= '';
					$variablesUnix['esPaquete']		= 'off';
					$variablesUnix['codigoPaquete']	= '';
					$variablesUnix['wusuario']		= $wuse;

					$variablesUnix['concepto'] 		= $concepto;
					$variablesUnix['procedimiento']	= '0';
					$variablesUnix['nomProcedi']	= '';
					$variablesUnix['ccoGraba'] 		= $ccoIva;
					$variablesUnix['tipoEmpresa']	= $tipoEmpresa;
					$variablesUnix['tarifa']		= $tarifa;
					$variablesUnix['responsable'] 	= $responsable;
					$variablesUnix['tipoIngreso'] 	= $tipoIngreso;
					$variablesUnix['tipoPaciente']	= $tipoPaciente;
					$variablesUnix['codigoRips']	= '';
					$variablesUnix['tercero']		= '';
					$variablesUnix['especialidad']	= '';
					$variablesUnix['cobraHono']		= '';
					$variablesUnix['grupoMedico']	= '';
					$variablesUnix['estaEnTurno']	= '*';
					$variablesUnix['registroUnix']	= '';
					$variablesUnix['codHomologar']	= '';

					$variablesUnix['bloqueo_global_fuentes'] 	= false;
					$variablesUnix['usa_fuente_cargo']       	= false;
					$variablesUnix['conexUnix_FacturacionPpal']	= '';
					$variablesUnix['desdeGrabacionIva']			= true;


					$logUnix = grabarCargoFacturacionUnix($variablesUnix, $logRegistroCargo);

					if ($logUnix['Error']) {
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= $logUnix['MensajeError'];
						echo json_encode($respuesta);
						return;
					}

					$logUnix = json_encode($logUnix);


					//--> Grabar cargo en matrix
					$sql106 = "
			INSERT INTO " . $wbasedato . "_000106 SET
			Medico		= '" . $wbasedato . "',
			Fecha_data	= '" . date("Y-m-d") . "',
			Hora_data	= '" . date("H:i:s") . "',
			Tcarusu		= '" . $wuse . "',
			Tcarhis		= '" . $historia . "',
			Tcaring		= '" . $ingreso . "',
			Tcarfec		= '" . date("Y-m-d") . "',
			Tcarres		= '" . $responsable . "',
			Tcarno1		= '" . $wno1 . "',
			Tcarno2		= '" . $wno2 . "',
			Tcarap1		= '" . $wap1 . "',
			Tcarap2		= '" . $wap2 . "',
			Tcardoc		= '" . $wdoc . "',
			Tcarser		= '" . $ccoIva . "',
			Tcarconcod	= '" . $concepto . "',
			Tcarconnom	= '" . $nomCon . "',
			Tcarprocod	= '0',
			Tcarpronom	= '',
			Tcarcan		= '1',
			Tcarvun		= '" . $valIva . "',
			Tcarvto		= '" . $valIva . "',
			Tcarrec		= 'R',
			Tcarfac		= 'S',
			Tcartfa		= 'MANUAL',
			Tcarest		= 'on',
			Tcartar		= '" . $tarifa . "',
			Tcarvre		= '" . $valIva . "',
			Seguridad	= 'C-" . $wuse . "'
			";
					$res106 	= mysql_query($sql106, $conex); // or die("<b>ERROR EN QUERY MATRIX(sql106):</b><br>".mysql_error());

					if (!$res106) {
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= mysql_error();
						echo json_encode($respuesta);
						return;
					}

					$ultimoid 	= mysql_insert_id();

					$sql107 = "
			INSERT INTO " . $wbasedato . "_000107 SET
			Medico		= '" . $wbasedato . "',
			Fecha_data	= '" . date("Y-m-d") . "',
			Hora_data	= '" . date("H:i:s") . "',
			Audhis		= '" . $historia . "',
			Auding		= '" . $ingreso . "',
			Audreg		= '" . $ultimoid . "',
			Audacc		= 'GRABO',
			Audusu		= '" . $wuse . "',
			Audrun		= '" . $logUnix . "',
			Audrcu		= '" . $logRegistroCargo . "',
			Seguridad	= 'C-" . $wuse . "'
			";
					$res107 = mysql_query($sql107, $conex) or die("<b>ERROR EN QUERY MATRIX(sql106):</b><br>" . mysql_error());

					if (!$res107) {
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= mysql_error();
						echo json_encode($respuesta);
						return;
					}

					echo json_encode($respuesta);
					break;
					return;
				}
			case 'validarSiGrabaIva': {
					$respuesta 				= array('Error' => false, 'Mensaje' => '');
					$calcularIvaDesdeUnix 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'calcularValorIvaDesdeUnix');
					$valorTotal 			= 0;
					$conexUnix 	 			= odbc_connect('facturacion', 'informix', 'sco');

					// --> validar si ya existe un concepto de iva grabado

					$sqlYaGrabado = "
			SELECT cardettot
			  FROM FACARDET
			 WHERE cardethis = '" . $historia . "'
			   AND cardetnum = '" . $ingreso . "'
			   AND cardetcon = '" . $conceptoIva . "'
			   AND cardetfac = 'S'
			   AND cardetanu = 0
			";
					$resYaGrabado = odbc_exec($conexUnix, $sqlYaGrabado);
					if (odbc_fetch_row($resYaGrabado)) {
						$valorgrabado			= odbc_result($resYaGrabado, 'cardettot');
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "Ya existe una grabacion de iva por valor de $ " . number_format($valorgrabado, 0, '.', ',') . "<br>
										   Para hacer una nueva grabacion primero debe anular la que existe.";
						echo json_encode($respuesta);
						return;
					}


					if ($calcularIvaDesdeUnix == 'on') {

						// --> Obtener el valor total de los cargos grabados en cco de cx y que sean si facturables
						$sqlValTotal = "
				 SELECT sum(cardettot) as total
				   FROM FACARDET
				  WHERE cardethis = '" . $historia . "'
				    AND cardetnum = '" . $ingreso . "'
					AND cardetfac = 'S'
					AND cardetanu = 0
				";
						$resValTotal = odbc_exec($conexUnix, $sqlValTotal);
						if (odbc_fetch_row($resValTotal))
							$valorTotal	= odbc_result($resValTotal, 'total');

						$valorTotal = ((is_null($valorTotal)) ? 0 : $valorTotal);
					}

					odbc_close($conexUnix);

					$respuesta['sqlValTotal'] 		= $sqlValTotal;
					$respuesta['valorTotalCar'] 	= number_format($valorTotal, 0, '.', ',');
					$respuesta['valorTotalIva'] 	= number_format(((int) $valorTotal * ((int)$valorIvaParaCx / 100)), 0, '.', ',');
					echo json_encode($respuesta);
					break;
					return;
				}
			case 'ObtenerElTipoDeConcepto': {
					$Data = array();
					// --> Primero validar en las politicas que el concepto sea facturable
					if (ValidarSiEsFacturable($CodigoConcepto)) {
						$Data['Facturable'] = 'si';

						$Tipo_concepto = "SELECT Grutip
									FROM " . $wbasedato . "_000200
								   WHERE Grucod = '" . $CodigoConcepto . "' ";
						$res_Tipo_concepto = mysql_query($Tipo_concepto, $conex) or die("Error en el query: " . $Tipo_concepto . "<br>Tipo Error:" . mysql_error());
						if ($row_Tipo_concepto = mysql_fetch_array($res_Tipo_concepto))
							$Data['Tipo'] = $row_Tipo_concepto['Grutip'];
						else
							$Data['Tipo'] = '';
					} else {
						$Data['Facturable'] = 'no';
						$Data['Tipo'] 		= '';
					}
					echo json_encode($Data);
					break;
					return;
				}
			case 'crear_hidden_procedimientos': {
					echo json_encode(Obtener_array_procedimientos_x_concepto($CodConcepto, $Tarifa));
					break;
					return;
				}
			case 'traer_interfaz_abonos': {

					traer_interfaz_abonos($wconmca, $wcco);
					break;
					return;
				}
			case 'pedir_datos_banco': {
					pedir_datos_banco($wdato);
					break;
					return;
				}
			case 'Obtener_tarifa_de_empresa': {
					$q_tar = " SELECT Emptar, Tardes
						 FROM " . $wbasedato . "_000024, " . $wbasedato . "_000025
						WHERE Empcod = '" . $Empresa . "'
						  AND Emptar = Tarcod
			";
					$res_tar = mysql_query($q_tar, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q_tar . " - " . mysql_error());
					if ($row_tar = mysql_fetch_array($res_tar)) {
						$data['NomTarifa'] = $row_tar['Tardes'];
						$data['CodTarifa'] = $row_tar['Emptar'];
					}
					echo json_encode($data);
					break;
					return;
				}
			case 'PintarDetalleCuentaResumido': {
					echo detalle_cuenta_resumido($historia, $ingreso, $permiteAnularCargo);
					break;
				}
			case 'ObtenerPoliticaManejoTerceros': {
					$codigoPolitica 				= '';
					$PolManejoTercero 				= politicaManejoTercero($CodCon, $CodProc, $CodTipEmp, $CodTar, $CodNit, $CodEnt, $CodCco, $historia, $ingreso, $codigoPolitica);
					$respuesta['PolManejoTercero']	= $PolManejoTercero;
					$respuesta['codigoPolitica']	= $codigoPolitica;
					echo json_encode($respuesta);
					break;
				}
			case 'obtenerActualResponsable': {
					$data = array();
					$qRespo = " SELECT Ingtpa, Empcod, Empnom, Emptem, Empnit, Tarcod, Tardes
						  FROM " . $wbasedato . "_000101, " . $wbasedato . "_000024, " . $wbasedato . "_000025
						 WHERE Inghis = '" . $whistoria . "'
						   AND Ingnin = '" . $wing . "'
						   AND Ingcem = Empcod
						   AND Ingtar = Tarcod
						 UNION
						SELECT Ingtpa, Empcod, Empnom, Emptem, Empnit, Tarcod, Tardes
						  FROM " . $wbasedato . "_000101, " . $wbasedato . "_000024, " . $wbasedato . "_000025
						 WHERE Inghis = '" . $whistoria . "'
						   AND Ingnin = '" . $wing . "'
						   AND Ingtpa = 'P'
					       AND Empcod = '" . consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular') . "'
						   AND Ingtar = Tarcod

			";
					$resRespo = mysql_query($qRespo, $conex) or die("Error en el query: " . $qRespo . "<br>Tipo Error:" . mysql_error());
					if ($rowRespo = mysql_fetch_array($resRespo)) {
						// --> Si el paciente es particular, no existe relacion con la tabla 24
						if ($rowRespo['Ingtpa'] == 'P') {
							$rowRespo['Empcod'] = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
							$rowRespo['Empnom'] = 'Particular';
							$rowRespo['Emptem'] = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tipoempresaparticular');
						}

						$data = $rowRespo;
					}
					// --> Obtener el codigo de la empresa PARTICULAR.
					$codEmpPartic	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
					// --> Si el nuevo responsable es un particular, se debe pedir confirmacion con que tarifa se va a continuar
					if ($codEmpPartic == $rowRespo['Empcod'])
						$data['pedirCambioTarifa'] = true;
					else
						$data['pedirCambioTarifa'] = false;

					echo json_encode($data);
					break;
				}
			case 'regrabarCargo': {
					$data = regrabarCargo($idCargo, $responsble, $tipoIngreso, $tipoPaciente, '', 'REGRABACION DESDE CARGOS', 'on');

					echo json_encode($data);
					break;
				}
			case 'quitarAlertaCargosPorRegrabar': {
					quitarAlertaCargosPorRegrabar($historia, $ingreso);

					break;
				}
			case 'obtResponsablesPaciente': {
					$option    = '';
					$obtReaPac = "SELECT Resnit, Empnom
							FROM " . $wbasedato . "_000205, " . $wbasedato . "_000024
						   WHERE Reshis = '" . $historia . "'
							 AND Resing = '" . $ingreso . "'
							 AND Resest = 'on'
							 AND Resnit = Empcod
							 AND Resdes != 'on'
						   UNION
						  SELECT Empcod AS Resnit, Empnom
							FROM " . $wbasedato . "_000101, " . $wbasedato . "_000024
						   WHERE Inghis = '" . $historia . "'
							 AND Ingnin = '" . $ingreso . "'
							 AND Ingtpa = 'P'
							 AND Empcod = '" . consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular') . "'

			";
					$rObtReaPac = mysql_query($obtReaPac, $conex) or die("Error en el query: " . $obtReaPac . "<br>Tipo Error:" . mysql_error());
					if (mysql_num_rows($rObtReaPac) == 0)
						$option .= "<option value=''>No hay mas responsables para el paciente.</option>";
					else {
						$option .= "<option value=''>Seleccione...</option>";
						while ($rowObtReaPac = mysql_fetch_array($rObtReaPac)) {
							$option .= "<option value='" . $rowObtReaPac['Resnit'] . "'>" . $rowObtReaPac['Resnit'] . "-" . $rowObtReaPac['Empnom'] . "</option>";
						}
					}

					echo $option;
					break;
				}
			case 'horaFechaDelServidor': {
					$data['Hora']  = date("H:i");
					$data['Fecha'] = date("Y-m-d");
					echo json_encode($data);
					break;
				}
			case 'congelarCuentaPaciente': {
					congelarCuentaPaciente($historia, $ingreso, 'CA', $congelar);
					break;
				}
			case 'estadoCuentaCongelada': {
					$infoEncabezado = estadoCongelacionCuentaPaciente($historia, $ingreso);

					// --> Si hay un encabezado
					if ($infoEncabezado['hayEncabezado'])
						$infoEncabezado = $infoEncabezado['valores'];
					else
						$infoEncabezado['Ecoest'] = 'off';

					$infoEncabezado['wuse'] = $wuse;
					echo json_encode($infoEncabezado);
					break;
				}
			case 'terceroPorDefecto': {
					$info = traer_terceros_por_defecto($concepto);
					echo json_encode($info);
					break;
				}
			case 'comfirmar_revision': {
					$q =  " 	UPDATE " . $wbasedato . "_000106
			               SET Tcarreg = 'OM'
					     WHERE id = '" . $idregistro . "' ";

					$err = mysql_query($q, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());

					quitarAlertaCargosPorRegrabar($historia, $ingreso);

					break;
				}
				/*case 'verificarDisponibilidad':
		{
			$respuesta = verificarDisponibilidad();
			echo json_encode($respuesta);
			break;
		}*/
			case 'imprimirSolicitudExamen': {
					$respuesta 		= array('Error' => false, 'Html' => '', 'Mensaje' => '');
					$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');

					// --> Consultar informaci�n
					$sqlInfoPac = "
			SELECT A.Fecha_data, Tcarhis, Tcaring, Tcarternom, Pacno1, Pacno2, Pacap1, Pacap2, Pactdo, Pacdoc, Pronom,
				   Medno1, Medno2, Medap1, Medap2, Meddoc
			  FROM " . $wbasedato . "_000106 AS A INNER JOIN " . $wbasedato . "_000100 AS B ON(A.Tcarhis = B.Pachis)
				   INNER JOIN " . $wbasedato . "_000101 AS C ON(B.Pachis = C.Inghis AND C.Ingnin = A.Tcaring)
				   INNER JOIN " . $wbasedato . "_000103 AS D ON (A.Tcarprocod = D.Procod)
				   LEFT  JOIN " . $wbasedatoMov . "_000048	AS E ON(C.Ingmei = E.Meddoc)
			 WHERE  A.id = '" . $idCargo . "'
			";
					$resInfoPac = mysql_query($sqlInfoPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoPac):</b><br>" . mysql_error());
					if ($rowInfoPac = mysql_fetch_array($resInfoPac)) {
						// --> Noviembre 18 2019, Se coloca trim
						$rowInfoPac['Tcarhis'] = trim($rowInfoPac['Tcarhis']);
						$rowInfoPac['Tcaring'] = trim($rowInfoPac['Tcaring']);

						if (substr($rowInfoPac['Meddoc'], 0, 4) == '0000')
							$nomMedico = "&nbsp;";
						else
							$nomMedico = trim($rowInfoPac['Medno1'] . " " . $rowInfoPac['Medno2'] . " " . $rowInfoPac['Medap1'] . " " . $rowInfoPac['Medap2']);

						$respuesta['Html'] = "
				<html>
					<head></head>
					<body style='margin-top:0mm;margin-left:8mm;' align='left'>
						<style type='text/css'>
							.doted{
								font-size:4mm;
								font-weight: 400;
							}
						</style>
						<table class='doted' style='width:84mm'>
							<tr>
								<td class='b' style='width:45mm'></td><td class='b' style='height:16mm' align='center'>" . $rowInfoPac['Fecha_data'] . "</td>
							</tr>
							<tr>
								<td class='b' align='left' style='height:8mm'>" . trim($rowInfoPac['Pacap1'] . " " . $rowInfoPac['Pacap2']) . "</td>
								<td class='b' align='center'>" . $rowInfoPac['Pactdo'] . " " . $rowInfoPac['Pacdoc'] . "</td>
							</tr>
							<tr>
								<td class='b' align='left' style='height:8mm'>" . trim($rowInfoPac['Pacno1'] . " " . $rowInfoPac['Pacno2']) . "</td>
								<td class='b' align='center'>" . $rowInfoPac['Tcarhis'] . "-" . $rowInfoPac['Tcaring'] . "</td>
							</tr>
							<tr>
								<td class='b' colspan='2' align='center' valign='top' style='height:8mm;font-size:3mm;'>" . trim($rowInfoPac['Pronom']) . "</td>
							</tr>
							<tr>
								<td class='b' colspan='2' align='center' style='height:9mm'>" . $nomMedico . "</td>
							</tr>
						</table>
						<table style='height:11mm;width:84mm' class='b'>
							<tr><td></td></tr>
						</table>
						<table class='doted' style='width:117mm'>
							<tr>
								<td class='b' align='left' style='width:69mm;height:8mm' colspan='2'>" . trim($rowInfoPac['Pacap1'] . " " . $rowInfoPac['Pacap2']) . "</td>
								<td class='b' align='left' >" . trim($rowInfoPac['Pacno1'] . " " . $rowInfoPac['Pacno2']) . "</td>
							</tr>
							<tr>
								<td class='b' align='left' style='width:24mm;height:8mm'>" . $rowInfoPac['Tcarhis'] . "-" . $rowInfoPac['Tcaring'] . "</td>
								<td class='b' align='left' style='width:45mm;'>" . $rowInfoPac['Pactdo'] . " " . $rowInfoPac['Pacdoc'] . "</td>
								<td class='b' >" . date("d-m-Y") . "</td>
							</tr>
							<tr>
								<td class='b' colspan='2' align='left' valign='top' style='font-size:3mm;height:8mm'>" . trim($rowInfoPac['Pronom']) . "</td>
								<td class='b' ></td>
							</tr>
						</table>
					</body>
				</html>
				";

						// --> Generar archivo pdf
						if (!$respuesta['Error']) {
							$wnombrePDF 	= "solicitudExamen_" . $rowInfoPac['Tcarhis'] . "-" . $rowInfoPac['Tcaring'];
							$archivo_dir 	= "soportes/" . $wnombrePDF . ".html";
							$dir			= "soportes";

							if (is_dir($dir)) {
							} else {
								mkdir($dir, 0777);
							}

							if (file_exists($archivo_dir)) {
								unlink($archivo_dir);
							}

							$f = fopen($archivo_dir, "w+");
							fwrite($f, $respuesta['Html']);
							fclose($f);

							shell_exec("./generarPdf_sticker.sh " . $wnombrePDF);

							$respuesta['Html'] = "
						<br>
						<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/" . $wnombrePDF . ".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='600' height='300'>"
								. "<param name='src' value='soportes/" . $wnombrePDF . "' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
								. "<p style='text-align:center; width: 90%;'>"
								. "Adobe Reader no se encuentra o la versi�n no es compatible, utiliza el icono para ir a la p�gina de descarga <br />"
								. "<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
								. "<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
								. "</a>"
								. "</p>"
								. "</object>
						<br>
					";
						}
					} else {
						$respuesta['Mensaje'] 	= utf8_encode("No se encontr� informaci�n del cargo");
						$respuesta['Error'] 	= true;
					}

					$respuesta['Mensaje'] 	= utf8_encode($respuesta['Mensaje']);
					$respuesta['Html'] 		= utf8_encode($respuesta['Html']);
					$respuesta['nombrePdf']	= $wnombrePDF;
					echo json_encode($respuesta);
					return;
					break;
				}
			case 'imprimirSoporteCargo': {
					$respuesta 		= array('Error' => false, 'Html' => '', 'Mensaje' => '');
					$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
					$idCargo		= explode("-", $idCargo);
					$inIdCaro		= "";
					$primeraVez 	= true;
					foreach ($idCargo as $valId) {
						$inIdCaro .= ($primeraVez) ? "'" . $valId . "'" : ",'" . $valId . "'";
						$primeraVez = false;
					}

					$respuesta['Html'] = "
			<html>
				<head></head>
				<body style='margin:0mm'>
				<style type='text/css'>
					.doted{
						font-size:3.5mm;
						font-weight: 400;
					}
					.doted2{
						font-size:3.5mm;
						font-weight: 400;
					}
					.borde{
						border:1px;
						border-style:dotted dotted dotted dotted;
						font-weight: 400;
					}
				</style>";

					// --> Consultar informaci�n del paciente
					//MIGRA_2
					$sqlInfoPac = "
			SELECT Pacno1, Pacno2, Pacap1, Pacap2, Pactdo, Pacdoc, Pacfna, Ingfei, Inghin, Ingtpa,
			       Descripcion, Medno1, Medno2, Medap1, Medap2
			  FROM " . $wbasedato . "_000100 AS A INNER JOIN " . $wbasedato . "_000101 AS B ON(A.Pachis = B.Inghis AND B.Ingnin = '" . $ingreso . "')
				   LEFT  JOIN root_000011 				AS E ON(B.Ingdig = E.Codigo)
				   LEFT  JOIN " . $wbasedatoMov . "_000048	AS F ON(B.Ingmei = F.Meddoc)
			 WHERE A.Pachis = '" . $historia . "'
			";
					$resInfoPac = mysql_query($sqlInfoPac, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoPac):</b><br>" . mysql_error());
					if ($rowInfoPac = mysql_fetch_array($resInfoPac)) {
						// --> Si es tipo de empresa particular
						if ($rowInfoPac['Ingtpa'] == 'P') {
							$responsable = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigoempresaparticular');
						}

						$sqlSqlEmpPart = "
				SELECT Empnit, Empnom, Emptar, Tardes
				  FROM " . $wbasedato . "_000024 INNER JOIN " . $wbasedato . "_000025 ON (Emptar = Tarcod)
				 WHERE Empcod = '" . trim($responsable) . "'
				";
						$resSqlEmpPart = mysql_query($sqlSqlEmpPart, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlSqlEmpPart):</b><br>" . mysql_error());
						if ($rowSqlEmpPart = mysql_fetch_array($resSqlEmpPart)) {
							$rowInfoPac['Empnom'] = $rowSqlEmpPart['Empnom'];
							$rowInfoPac['Empnit'] = $rowSqlEmpPart['Empnit'];
							$rowInfoPac['Tarcod'] = $rowSqlEmpPart['Emptar'];
							$rowInfoPac['Tardes'] = $rowSqlEmpPart['Tardes'];
						}

						// --> Calcular edad
						$diff 	= abs(strtotime(date('Y-m-d')) - strtotime($rowInfoPac['Pacfna']));
						$years 	= floor($diff / (365 * 60 * 60 * 24));
						$months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
						$edad	= (($years >= 0) ? $years : $months);

						$encabezado 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'encabezadoImpresionSoporteCargoFacInt');
						$datosBasicos 	= "
				<table width='100%' class='doted' style='border:1px;border-style:none dotted none dotted;'>
					<tr>
						<td style='border:1px;border-style:dotted none dotted none;' align='center' colspan='3'>" . $encabezado . "</td>
					</tr>
					<tr>
						<td style='border:1px;border-style:none none dotted none;' colspan='3' align='left'><b>PACIENTE</b></td>
					</tr>
					<tr class='doted2'>
						<td align='left' nowrap='nowrap'>NOMBRE:" . $rowInfoPac['Pacno1'] . " " . $rowInfoPac['Pacno2'] . " " . $rowInfoPac['Pacap1'] . " " . $rowInfoPac['Pacap2'] . "</td>
						<td align='left' nowrap='nowrap'>&nbsp;IDENTIF.:" . $rowInfoPac['Pactdo'] . "-" . $rowInfoPac['Pacdoc'] . "&nbsp;&nbsp;EDAD:" . $edad . "</td>
						<td align='left'>&nbsp;HISTORIA:" . $historia . "-" . $ingreso . "</td>
					</tr>
					<tr class='doted2'>
						<td align='left'>FECHA/HORA:" . $rowInfoPac['Ingfei'] . "/" . $rowInfoPac['Inghin'] . "</td>
						<td align='left'>&nbsp;MEDICO:<span style='font-size:3mm;'>" . ucwords(strtolower($rowInfoPac['Medno1'] . " " . $rowInfoPac['Medno2'] . " " . $rowInfoPac['Medap1'] . " " . $rowInfoPac['Medap2'])) . "</span></td>
						<td align='left'>&nbsp;DIAGNOST.:<span style='font-size:3mm;'>" . $rowInfoPac['Descripcion'] . "</span></td>
					</tr>
					<tr>
						<td style='border:1px;border-style:dotted none dotted none;' colspan='3' align='left'><b>RESPONSABLE</b></td>
					</tr>
					<tr class='doted2'>
						<td align='left' nowrap='nowrap'>EMPRESA:" . trim($rowInfoPac['Empnom']) . "</td>
						<td align='left' nowrap='nowrap'>CED/NIT.:&nbsp;" . $rowInfoPac['Empnit'] . "</td>
						<td align='left'>TARIFA:&nbsp;" . trim($rowInfoPac['Tarcod']) . "-" . trim($rowInfoPac['Tardes']) . "</td>
					</tr>
					";

						$respuesta['Html'] .= "
				<table width='100%' style='font-size:2.5mm;font-weight:600;'>
					<tr><td align='right'>Pagina 1</td></tr>
				</table>" . $datosBasicos;;
					} else {
						$respuesta['Error'] 	= true;
						$respuesta['Mensaje'] 	= "No se encontro informaci�n del paciente";
						$respuesta['Mensaje'] 	= utf8_encode($respuesta['Mensaje']);
						echo json_encode($respuesta);
						return;
					}

					$tiposEmpRedondean 	=	consultarAliasPorAplicacion($conex, $wemp_pmla, 'tiposEmpresaQueRedondeanValorCargosFI');
					$tiposEmpRedondean	= 	explode(',', $tiposEmpRedondean);

					// --> Consultar informacion del o los cargos que no mueven inventario
					$sqlInfoCar = "
			SELECT Tcarconcod, Tcarconnom, Tcarprocod, Tcarpronom, Tcarcan, Tcartun, Tcarvun, Tcarvto, Tcarvre, Tcarvro, Gruinv, Tcarpor, Emptem
			  FROM " . $wbasedato . "_000106 AS A INNER JOIN " . $wbasedato . "_000200 ON(Tcarconcod = Grucod AND Gruinv != 'on')
				   LEFT JOIN " . $wbasedato . "_000024 AS C ON(A.Tcarres = C.Empcod)
			 WHERE A.id IN(" . $inIdCaro . ")
			   AND Tcarest = 'on'
			   AND Tcarfac = 'S' ";

					$resInfoCar = mysql_query($sqlInfoCar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoCar):</b><br>" . mysql_error());

					$valorTotalCargos 	= 0;
					$totalRecargo 		= 0;
					$totalDescuento 	= 0;
					$valorTotalRecono 	= 0;

					// --> Encabezado para los cargos que no mueven inventario
					if (mysql_num_rows($resInfoCar) > 0) {
						$respuesta['Html'] .= "
				<tr>
					<td  colspan='3' align='left' style='border:1px;border-style:dotted none dotted none;'>
						<b>RELACION DE EXAMENES</b>
					</td>
				</tr>
				<tr>
					<td  colspan='3' align='left'>
						<table width='100%' class='doted' style='border:1px;border-style:none none dotted none;'>
							<tr style='font-weight:bold' align='center'>
								<td>Cpto</td>
								<td>Descripci�n</td>
								<td>Codigo</td>
								<td>Descripci�n</td>
								<td>Can.</td>
								<td>Tercero</td>
								<td align='right'>Valor</td>
							</tr>";

						// --> Pintar cada cargo que no mueve inventario
						while ($rowInfoCar = mysql_fetch_array($resInfoCar)) {
							$tipoEmpresa		= trim($rowInfoCar['Emptem']);
							$recargo 			= $rowInfoCar['Tcarvro'];
							$valorCargo			= round(($rowInfoCar['Tcarvun'] - $rowInfoCar['Tcarvro']) * $rowInfoCar['Tcarcan']);

							if (in_array($tipoEmpresa, $tiposEmpRedondean) && $wconinv != 'on')
								$valorCargo = round($valorCargo, -2);

							$descuento 			= $valorCargo * ($rowInfoCar['Tcarpor'] / 100);
							$valorTotalCargos 	= $valorTotalCargos + $valorCargo;
							$totalRecargo		= $totalRecargo + $recargo;
							$totalDescuento		= $totalDescuento + $descuento;
							$valorTotalRecono	= $valorTotalRecono + ($rowInfoCar['Tcarvre'] - $descuento);

							//------------------------------------------------------
							$respuesta['VAR']['valorCargo'] = $valorCargo;
							$respuesta['VAR']['descuento'] = $descuento;
							$respuesta['VAR']['Tcarpor'] = $rowInfoCar['Tcarpor'];
							//------------------------------------------------------

							$respuesta['Html'] .= "
								<tr>
									<td align='center'>" . $rowInfoCar['Tcarconcod'] . "</td>
									<td align='center'>" . $rowInfoCar['Tcarconnom'] . "</td>
									<td align='center'>" . $rowInfoCar['Tcarprocod'] . "</td>
									<td align='center' style='font-size:3mm;'>" . $rowInfoCar['Tcarpronom'] . "</td>
									<td align='center'>" . $rowInfoCar['Tcarcan'] . "</td>
									<td align='center' style='font-size:3mm;'>" . $rowInfoCar['Tcartun'] . "</td>
									<td align='right'>" . number_format($valorCargo, 0, '.', ',') . "</td>
								</tr>";
						}
						// --> Pintar valor total de los cargos que no mueven inv
						$respuesta['Html'] .= "
								<tr>
									<td colspan='3'></td>
									<td colspan='4' align='right'>
										TOTAL:" . number_format($valorTotalCargos, 0, '.', ',') . "
										&nbsp;&nbsp;+&nbsp;&nbsp;
										RECAR:
										" . $totalRecargo . "
										&nbsp;&nbsp;-&nbsp;&nbsp;
										DES:
										" . $totalDescuento . "
										&nbsp;&nbsp;=&nbsp;&nbsp;
										" . number_format(($valorTotalCargos + $totalRecargo - $totalDescuento), 0, '.', ',') . "
									</td>
								</tr>
							</table>
						</td>
					</tr>";
					}

					// --> Consultar informacion del o los cargos que SI mueven inventario
					$sqlInfoCar = "
			SELECT Tcarconcod, Tcarconnom, Tcarprocod, Tcarpronom, Tcarcan, Tcarternom, Tcarvun, Tcarvto, Tcarvre, Gruinv
			  FROM " . $wbasedato . "_000106 AS A INNER JOIN " . $wbasedato . "_000200 ON(Tcarconcod = Grucod AND Gruinv = 'on')
			 WHERE A.id IN(" . $inIdCaro . ")
			   AND Tcarest = 'on'
			   AND Tcarfac = 'S' ";

					$resInfoCar 		= mysql_query($sqlInfoCar, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlInfoCar):</b><br>" . mysql_error());
					$valorTotalMedic 	= 0;

					$respuesta['Html'] .= pintarCargosQueMuevenInventario($datosBasicos, $resInfoCar, 1, $valorTotalMedic, $valorTotalRecono);

					// --> Pintar total general y pie de pagina
					$respuesta['Html'] .= "
				<tr>
					<td style='border:1px;border-style:none dotted dotted none;' valign='bottom'>
						Firma Autorizada
					</td>
					<td style='border:1px;border-style:none none dotted none;' align='right'>
						VALOR MEDICAMENTOS:<br>
						+ EXA/PROC:<br>
						- VALOR RECONOCIDO:<br>
						A PAGAR:<br>
					</td>
					<td style='border:1px;border-style:none none dotted none;' align='right'>
						" . number_format($valorTotalMedic, 0, '.', ',') . "<br>
						" . number_format(($valorTotalCargos + $totalRecargo - $totalDescuento), 0, '.', ',') . "<br>
						" . number_format($valorTotalRecono, 0, '.', ',') . "<br>
						" . number_format(($valorTotalMedic + ($valorTotalCargos + $totalRecargo - $totalDescuento) - $valorTotalRecono), 0, '.', ',') . "
					</td>
				</tr>
				</table>
				<table width='100%' class='doted'>
					<tr>
						<td style='font-size:2.5mm;' align='right'>Fecha Imp:" . date("Y-m-d") . "&nbsp;&nbsp;Hora Imp:" . date("H:i:s") . "&nbsp;&nbsp;Usuario:" . $wuse . "</td>
					</tr>
				</table>
				</body>
				</html>
				";

					// --> Generar archivo pdf
					if (!$respuesta['Error']) {
						$wnombrePDF 	= "soporteCargos_" . $historia . "-" . $ingreso;
						$archivo_dir 	= "soportes/" . $wnombrePDF . ".html";
						$dir			= "soportes";

						if (is_dir($dir)) {
						} else {
							mkdir($dir, 0777);
						}

						if (file_exists($archivo_dir)) {
							unlink($archivo_dir);
						}

						$f = fopen($archivo_dir, "w+");
						fwrite($f, $respuesta['Html']);
						fclose($f);

						if (file_exists("soportes/" . $wnombrePDF . ".pdf")) {
							unlink("soportes/" . $wnombrePDF . ".pdf");
						}

						//chmod("./generarPdf_soportesCargos.sh", 0777);
						shell_exec("./generarPdf_soportesCargos.sh " . $wnombrePDF);

						$respuesta['Html'] = "
					<object type='application/pdf' data='../../../matrix/ips/procesos/soportes/" . $wnombrePDF . ".pdf' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 width='800' height='700'>"
							. "<param name='src' value='soportes/" . $wnombrePDF . "' pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1 />"
							. "<p style='text-align:center; width: 60%;'>"
							. "Adobe Reader no se encuentra o la versi�n no es compatible, utiliza el icono para ir a la p�gina de descarga <br />"
							. "<a href='http://get.adobe.com/es/reader/' onclick='this.target=\"_blank\">"
							. "<img src='../../images/medical/root/prohibido.gif' alt='Descargar Adobe Reader' width='32' height='32' style='border: none;' />"
							. "</a>"
							. "</p>"
							. "</object>
					<br>
				";
					}


					$respuesta['Mensaje'] 	= utf8_encode($respuesta['Mensaje']);
					$respuesta['Html'] 		= utf8_encode($respuesta['Html']);
					$respuesta['nombrePdf']	= $wnombrePDF;
					echo json_encode($respuesta);
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
	else {

?>
		<html>

		<head>
			<title>Cargos</title>
		</head>
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet" />
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

		<link rel="stylesheet" type="text/css" href="../../../matrix/interoperabilidad/procesos/mtxCtias.css?v=<?= md5_file('../../../matrix/interoperabilidad/procesos/mtxCtias.css'); ?>">
		<script src="../../../matrix/interoperabilidad/procesos/mtxCitas.js?v=<?= md5_file('../../../matrix/interoperabilidad/procesos/mtxCitas.js'); ?>"></script>

		<script type="text/javascript">
			//=====================================================================================================================================================================
			// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T
			//=====================================================================================================================================================================

			var url_add_params = addUrlCamposCompartidosTalento();

			var nomcco = "";
			var FormularioCargo = '';
			var ArrayInfoTerceros = new Array();
			var blinkSpan;

			//---------------------------------------------------
			//	Funciones que se cargan al iniciar el programa
			//---------------------------------------------------
			$(document).ready(function() {

				// --> Crear variable compartidas para todo el gestor
				crear_variables_compartidas();
				// --> se traen valores
				cargar_datos_caja();
				traer_datos_generales();
				traer_conceptos('%');
				traer_terceros();

				crear_autocomplete("hidden_proCups", "SI", "codRips", "", "");

				// --> Cargar datos basicos del paciente
				cargar_datos('wing');

				var f = new Date();

				// --> se carga el datepicker wfeccar
				cargar_elementos_datapicker();
				$("#wfeccar").datepicker({
					showOn: "button",
					buttonImage: "../../images/medical/root/calendar.gif",
					buttonImageOnly: true,
					maxDate: "+0D",
					minDate: new Date(f.getFullYear(), f.getMonth(), 1),
					onSelect: function() {
						$("#busc_concepto_1").val("");
						$("#busc_procedimiento_1").val("");
						$("#wvaltar_1").val("");
					}
				});

				//--> se carga el timepicker whora_cargo
				CargarTimepicker('whora_cargo_1');

				// --> Clonar el formulario de ingreso de un cargo
				setTimeout(function() {
					FormularioCargo = $(".cargo_cargo").clone();
				}, 100);

				// --> Tooltip
				$('.tooltip').tooltip({
					track: true,
					delay: 0,
					showURL: false,
					showBody: ' - ',
					opacity: 0.95,
					left: -50
				});

				// --> Regex
				$("#wcantidad_1").keyup(function() {
					if ($(this).val() != "")
						$(this).val($(this).val().replace(/[^0-9.]/g, ""));
				});

				$("#wvaltar_1").keyup(function() {
					if ($(this).val() != "")
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
				});
				$("#inputDescuento").keyup(function() {
					if ($(this).val() != "")
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
				});

				$("#numVias").keyup(function() {
					if ($(this).val() != "")
						$(this).val($(this).val().replace(/[^0-9]/g, ""));
				});
				$("#numVias").change(function() {
					if ($(this).val() == '' || $(this).val() == 0)
						$(this).val(1);
					pintarSelectVias();
				});

				// --> Inhabilitar el pegar en el input de cantidad
				myInput = document.getElementById('wcantidad_1');
				myInput.onpaste = function(e) {
					e.preventDefault();
					alert("Esta accion esta prohibida.");
				}
			});
			//------------------------------------------------------------------------------------------------------
			//	Funcion que carga un autocomplete para seleccionar un responsable
			//------------------------------------------------------------------------------------------------------
			function crear_variables_compartidas() {
				// --> Historia
				if ($("#div_campos_compartidos").find("#whistoria_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="whistoria_tal" type="hidden" value="" name="whistoria">');
				// --> Ingreso
				if ($("#div_campos_compartidos").find("#wing_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wing_tal" type="hidden" value="" name="wing">');
				// --> Nombre 1
				if ($("#div_campos_compartidos").find("#wno1_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wno1_tal" type="hidden" value="" name="wno1">');
				// --> Nombre 2
				if ($("#div_campos_compartidos").find("#wno2_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wno2_tal" type="hidden" value="" name="wno2">');
				// --> Apellido 1
				if ($("#div_campos_compartidos").find("#wap1_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wap1_tal" type="hidden" value="" name="wap1">');
				// --> Apellido 2
				if ($("#div_campos_compartidos").find("#wap2_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wap2_tal" type="hidden" value="" name="wap2">');
				// --> Documento
				if ($("#div_campos_compartidos").find("#wdoc_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wdoc_tal" type="hidden" value="" name="wdoc">');
				// --> Tipo Documento
				if ($("#div_campos_compartidos").find("#wtip_doc_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wtip_doc_tal" type="hidden" value="" name="wtip_doc_tal">');
				// --> Nombre de empresa
				if ($("#div_campos_compartidos").find("#wnomemp_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wnomemp_tal" type="hidden" value="" name="wnomemp">');
				// --> Fecha de ingreso
				if ($("#div_campos_compartidos").find("#wfecing_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wfecing_tal" type="hidden" value="" name="wfecing">');
				// --> Servicio de ingreso
				if ($("#div_campos_compartidos").find("#wser_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wser_tal" type="hidden" value="" name="wser">');
				// -->
				if ($("#div_campos_compartidos").find("#wpactam_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wpactam_tal" type="hidden" value="" name="wpactam">');
				// --> Nombre del servicio de ingreso
				if ($("#div_campos_compartidos").find("#nomservicio_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="nomservicio_tal" type="hidden" value="" name="nomservicio">');
				// --> Nombre Responsable
				if ($("#div_campos_compartidos").find("#div_responsable_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="div_responsable_tal" type="hidden" value="" name="div_responsable">');
				// --> Codigo Responsable
				if ($("#div_campos_compartidos").find("#responsable_original_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="responsable_original_tal" type="hidden" value="" name="responsable_original">');
				// --> Nombre Tarifa
				if ($("#div_campos_compartidos").find("#div_tarifa_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="div_tarifa_tal" type="hidden" value="" name="div_tarifa">');
				// --> Codigo Tarifa
				if ($("#div_campos_compartidos").find("#tarifa_original_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="tarifa_original_tal" type="hidden" value="" name="tarifa_original">');
				// -->
				if ($("#div_campos_compartidos").find("#div_documento_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="div_documento_tal" type="hidden" value="" name="div_documento">');
				// --> cco del facturador
				if ($("#div_campos_compartidos").find("#wcco_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wcco_tal" type="hidden" value="" name="wcco">');
				// --> Nombre del cco del facturador
				if ($("#div_campos_compartidos").find("#div_servicio_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="div_servicio_tal" type="hidden" value="" name="div_servicio">');
				// --> Tipo de paciente
				if ($("#div_campos_compartidos").find("#wtip_paciente_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wtip_paciente_tal" type="hidden" value="" name="wtip_paciente">');
				// --> Div para pintar cuadro de datos basicos del paciente
				if ($("#div_campos_compartidos").find("#div_datos_basicos_tal").length == 0)
					$("#div_campos_compartidos").append('<div id="div_datos_basicos_tal" style="display:none"></div>');
				// --> Usuario administrador
				if ($("#div_campos_compartidos").find("#wcajadm_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wcajadm_tal" type="hidden" value="" name="wcajadm">');
				// --> tipo de ingreso
				if ($("#div_campos_compartidos").find("#wtipo_ingreso_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wtipo_ingreso_tal" type="hidden" value="" name="wtipo_ingreso">');
				// --> Hubicacion del paciente
				if ($("#div_campos_compartidos").find("#ccoActualPac_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="ccoActualPac_tal" type="hidden" value="" name="ccoActualPac">');
				// --> Nombre Hubicacion del paciente
				if ($("#div_campos_compartidos").find("#nomCcoActualPac_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="nomCcoActualPac_tal" type="hidden" value="" name="nomCcoActualPac">');
				// --> Nombre del tipo de ingreso
				if ($("#div_campos_compartidos").find("#wtipo_ingreso_nom_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wtipo_ingreso_nom_tal" type="hidden" value="" name="wtipo_ingreso_nom">');
				// --> Tipo de empresa
				if ($("#div_campos_compartidos").find("#tipoEmpresa_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="tipoEmpresa_tal" type="hidden" value="" name="tipoEmpresa">');
				// --> Nit de empresa
				if ($("#div_campos_compartidos").find("#nitEmpresa_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="nitEmpresa_tal" type="hidden" value="" name="nitEmpresa">');
				// --> Html con los responsables
				if ($("#div_campos_compartidos").find("#tableResponsables_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="tableResponsables_tal" type="hidden" value="" name="tableResponsables">');
				// --> Si el usuario maneja bodega
				if ($("#div_campos_compartidos").find("#wbod_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="wbod_tal" type="hidden" value="" name="wbod">');
				// --> Nombre del usuario
				if ($("#div_campos_compartidos").find("#nomCajero_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="nomCajero_tal" type="hidden" value="" name="nomCajero">');
				// --> Si el usuario puede cambiar el responsable del cargo
				if ($("#div_campos_compartidos").find("#permiteCambiarResponsable_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteCambiarResponsable_tal" type="hidden" value="" name="permiteCambiarResponsable_tal">');
				// --> Si el usuario puede cambiar de tarifa del cargo
				if ($("#div_campos_compartidos").find("#permiteCambiarTarifa_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteCambiarTarifa_tal" type="hidden" value="" name="permiteCambiarTarifa_tal">');
				// --> Si el usuario puede regrabar cargos
				if ($("#div_campos_compartidos").find("#permiteRegrabar_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteRegrabar_tal" type="hidden" value="" name="permiteRegrabar_tal">'); // --> Si el usuario puede regrabar cargos
				// --> Si el usuario puede imprimir soportes del cargo
				if ($("#div_campos_compartidos").find("#permiteImprimirSoporteCargo_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteImprimirSoporteCargo_tal" type="hidden" value="" name="permiteImprimirSoporteCargo_tal">');
				// --> Si el usuario puede imprimir solicitud del examen
				if ($("#div_campos_compartidos").find("#permiteImprimirSolicitudExamen_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteImprimirSolicitudExamen_tal" type="hidden" value="" name="permiteImprimirSolicitudExamen_tal">');
				// --> Si el usuario puede anular cargos
				if ($("#div_campos_compartidos").find("#permiteAnularCargo_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteAnularCargo_tal" type="hidden" value="" name="permiteAnularCargo_tal">');
				// --> Si el usuario puede seleccionar si el cargo es facturable o no
				if ($("#div_campos_compartidos").find("#permiteSeleccionarFacturable_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteSeleccionarFacturable_tal" type="hidden" value="" name="permiteSeleccionarFacturable_tal">');
				// --> Si el usuario puede seleccionar si el cargo es reconocido o excedente
				if ($("#div_campos_compartidos").find("#permiteSeleccionarRecExc_tal").length == 0)
					$("#div_campos_compartidos").append('<input id="permiteSeleccionarRecExc_tal" type="hidden" value="" name="permiteSeleccionarRecExc_tal">');
			}
			//------------------------------------------------------------------------------------------------------
			//	Funcion que carga un autocomplete para seleccionar un responsable
			//------------------------------------------------------------------------------------------------------
			function CambiarResponsable() {
				if ($('#busc_resp_1').val() == '') {
					$('#td_responsable').html($('#div_responsable_tal').val());
				} else {
					$("#td_responsable").html("<input type='text' id='busc_resp_1' valor='' size='20' style='font-size: 8pt'>");
					// --> Cargar autocomplete de las entidades
					crear_autocomplete('hidden_entidades', 'SI', 'busc_resp_1', 'CambiarResponsable');
				}
			}
			//------------------------------------------------------------------------------------------------------
			//	Funcion que carga un autocomplete para seleccionar un responsable
			//------------------------------------------------------------------------------------------------------
			function CambiarTarifa() {
				if ($('#busc_tarifa_1').val() == '') {
					$('#td_tarifa').html($('#div_tarifa_tal').val());
				} else {
					$("#td_tarifa").html("<input type='text' id='busc_tarifa_1' valor='' size='20' style='font-size: 8pt'>");
					// --> Cargar autocomplete de las entidades
					crear_autocomplete('hidden_tarifas', 'SI', 'busc_tarifa_1', 'CambiarTarifa');
				}
			}
			//------------------------------------------------------------------------------------------------------
			//	Funcion que carga un selector de hora en un campo de texto
			// 	Jerson Trujillo.
			//------------------------------------------------------------------------------------------------------
			function CargarTimepicker(Elemento) {
				$('#' + Elemento).timepicker({
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
			//------------------------------------------------------------------------------------------------------
			//	Funcion general que recive un array o el id de un hidden Json y carga un autocomplete en un iput
			// 	Jerson Trujillo.
			//------------------------------------------------------------------------------------------------------
			function crear_autocomplete(HiddenArray, TipoHidden, CampoCargar, AccionSelect, CampoProcedimiento) {
				if (TipoHidden == 'SI')
					var ArrayValores = eval('(' + $('#' + HiddenArray).val() + ')');
				else
					var ArrayValores = eval('(' + HiddenArray + ')');

				var ArraySource = new Array();
				var index = -1;
				for (var CodVal in ArrayValores) {
					index++;
					ArraySource[index] = {};
					ArraySource[index].value = CodVal;
					ArraySource[index].label = CodVal + '-' + ArrayValores[CodVal];
					ArraySource[index].name = ArrayValores[CodVal];
				}

				CampoCargar = CampoCargar.split('|');
				$.each(CampoCargar, function(key, value) {
					$("#" + value).autocomplete({
						minLength: 3,
						source: ArraySource,
						select: function(event, ui) {
							$("#" + value).val(ui.item.label);
							$("#" + value).attr('valor', ui.item.value);
							$("#" + value).attr('nombre', ui.item.name);
							switch (AccionSelect) {
								case 'CargarProcedimientos': {
									crear_autocomplete_procedimientos(CampoProcedimiento, ui.item.value, true);
									return false;
								}
								case 'CambiarTarifa': {
									$("#td_tarifa").html(ui.item.label);
									$("#hidden_tarifa").val(ui.item.value);
									datos_desde_procedimiento(true);
									return false;
								}
								case 'CambiarResponsable': {
									$("#td_responsable").html(ui.item.label);
									$("#hidden_responsable").val(ui.item.value);
									$("#wnomemp").val(ui.item.name);
									$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
										consultaAjax: '',
										wemp_pmla: $('#wemp_pmla').val(),
										accion: 'Obtener_tarifa_de_empresa',
										Empresa: ui.item.value
									}, function(data) {
										$("#td_tarifa").html(data.CodTarifa + '-' + data.NomTarifa);
										$("#hidden_tarifa").val(data.CodTarifa);
										datos_desde_procedimiento(true);
									}, 'json');
									return false;
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
			function limpiaAutocomplete(idInput) {
				$("#" + idInput).on({
					focusout: function(e) {
						if ($(this).val().replace(/ /gi, "") == '') {
							$(this).val("");
							$(this).attr("valor", "");
							$(this).attr("nombre", "");
							if (idInput == "busc_terceros_1")
								datos_desde_procedimiento(true);
						} else {
							$(this).val($(this).attr("valor") + "-" + $(this).attr("nombre"));
						}
					}
				});
			}
			//-----------------------------------------------------------------------
			//	Crea el autocomplete de procedimientos dependiendo del concepto dado
			//	Jerson trujillo.
			//-----------------------------------------------------------------------
			function crear_autocomplete_procedimientos(Campo, CodConcepto, Show) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					accion: 'crear_hidden_procedimientos',
					wemp_pmla: $('#wemp_pmla').val(),
					CodConcepto: CodConcepto,
					Tarifa: $("#hidden_tarifa").val()
				}, function(respuesta) {
					if (Show)
						$("#" + Campo).show(300);

					crear_autocomplete(respuesta, 'NO', Campo, '');
				});
			}
			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function cargar_elementos_datapicker() {
				$.datepicker.regional['esp'] = {
					closeText: 'Cerrar',
					prevText: 'Antes',
					nextText: 'Despues',
					monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
						'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
					],
					monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun',
						'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'
					],
					dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
					dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
					dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
					weekHeader: 'Sem.',
					dateFormat: 'yy-mm-dd',
					yearSuffix: ''
				};
				$.datepicker.setDefaults($.datepicker.regional['esp']);
			}
			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function limpiarPantalla() {
				$("#whistoria").val('');
				$("#wing").val('');
				$("input[type='radio'][defecto='si']").attr("checked", true);
				$("#informacion_inicial").find("[limpiar=si]").html("");
				$("#div_cargos_cargados_resumido").html('');
				$("#accordionDetCuentaResumido").hide();
			}
			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function ActivarCampos(Campos) {
				var Elementos = $(Campos).attr('CamposActiva').split("-");
				$.each(Elementos, function(key, value) {
					if ($(Campos).val() != '')
						$('#' + value).show(300);
					else
						$('#' + value).hide(300);
				});
			}
			//----------------------------
			//	Nombre: cargar_datos
			//	Descripcion: funcion que carga los datos basicos informativos dados una historia y un ingreso
			//	Entradas: elemento - elemento desde donde se hace el llamado a la funcion
			//	Salidas:
			//----------------------------
			function cargar_datos(elemento) {
				$("#busc_concepto_1").val('');
				$("#busc_concepto_1").attr('valor', '');
				$("#busc_procedimiento_1").val('');

				$("#botonGrabarIva").hide();
				$("#linkAbrirHce").hide();
				var id = elemento; //variable que almacena el id del elemento de donde se hizo el llamado a la funcion cargar_datos

				// si la historia es vacia  se  inician los datos y no se continua la ejecucion de la funcion
				if ($("#whistoria_tal").val() == '' && $("#whistoria").val() == '') {
					limpiarPantalla();
					return;
				} else {
					if ($("#whistoria").val() == '') {
						$("#whistoria").val($("#whistoria_tal").val());
						$("#wing").val($("#wing_tal").val());
					}
				}

				// --> se hace una llamada ajax cargar_datos
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
						consultaAjax: '',
						wemp_pmla: $('#wemp_pmla').val(),
						accion: 'cargar_datos',
						whistoria: $('#whistoria').val(),
						wing: $('#wing').val(),
						wcargos_sin_facturar: $("#cargos_sin_facturar").val(),
						welemento: id

					}, function(data) {

						//data.prueba indica si la historia existe
						if (data.prueba == 'no') {
							alert('La historia no existe');
							$('#whistoria').val('');
							$('#wing').val('');
						} else {
							// data.error indica si hay un error  en el llamado de la funcion
							if (data.error == 1) {
								alert(data.mensaje);
								$('#whistoria').val('');
								$('#wing').val('');
								limpiarPantalla();
								$("#msjIngPost").hide().attr("hayIngresoPosterior", "off");
							} else {
								// --> datos traidos desde la funcion

								// --> Historia
								$("#whistoria_tal").val($('#whistoria').val());

								// --> Ingreso
								$("#wing").val(data.wwing);
								$("#wing_tal").val(data.wwing);

								if (data.wing_max > data.wwing)
									$("#msjIngPost").show().attr("hayIngresoPosterior", "on");
								else
									$("#msjIngPost").hide().attr("hayIngresoPosterior", "off");

								// --> Paciente
								$("#wno1").val(data.wno1);
								$("#wno1_tal").val(data.wno1);
								$("#wno2").val(data.wno2);
								$("#wno2_tal").val(data.wno2);
								$("#wap1").val(data.wap1);
								$("#wap1_tal").val(data.wap1);
								$("#wap2").val(data.wap2);
								$("#wap2_tal").val(data.wap2);

								// --> Documento
								$("#wdoc").val(data.wdoc);
								$("#wdoc_tal").val(data.wdoc);

								// --> Documento
								$("#wtip_doc").val(data.wtip_doc);
								$("#wtip_doc_tal").val(data.wtip_doc);

								// --> Responsable
								$("#wnomemp").val(data.wnomemp);
								$("#wnomemp_tal").val(data.wnomemp);

								// --> Fecha de ingreso
								$("#wfecing").html(data.wfecing);
								$("#wfecing_tal").val(data.wfecing);

								$("#wser").val(data.wser);
								$("#wser_tal").val(data.wser);

								// --> Ubicacion actual del paciente
								$("#divCcoActualPac").html(data.ccoActualPac + "-" + data.nomCcoActualPac);
								$("#ccoActualPac").val(data.ccoActualPac);
								$("#nomCcoActualPac").val(data.nomCcoActualPac);
								$("#ccoActualPac_tal").val(data.ccoActualPac);
								$("#nomCcoActualPac_tal").val(data.nomCcoActualPac);

								$("#wpactam").val(data.wpactam);
								$("#wpactam_tal").val(data.wpactam);

								$("#nomservicio").val(data.wnombreservicio);
								$("#nomservicio_tal").val(data.wnombreservicio);

								$("#div_tipo_servicio").html(data.wnombreservicio);

								$("#div_responsable").html(data.responsable);
								$("#div_responsable_tal").val(data.responsable);

								$("#responsable_original").val(data.wcodemp);
								$("#responsable_original_tal").val(data.wcodemp);

								$("#td_responsable").html(data.responsable);

								$("#hidden_responsable").val(data.wcodemp);

								$("#div_tarifa").html(data.tarifa);
								$("#div_tarifa_tal").val(data.tarifa);

								$("#tarifa_original").val(data.wtar);
								$("#tarifa_original_tal").val(data.wtar);

								$("#td_tarifa").html(data.tarifa);
								$("#hidden_tarifa").val(data.wtar);
								$("#div_paciente").html(data.paciente);

								// --> Pintar los otros responsables del paciente
								$("#tableResponsables").html('');
								$("#tableResponsables").append(data.otrosResponsables).show();
								$("#tableResponsables_tal").val(data.otrosResponsables);

								$("#div_documento").html(data.wdoc);
								$("#div_documento_tal").val(data.wdoc);

								$("#div_servicio").html($("#wcco").val() + '-' + nomcco);
								$("#div_servicio_tal").val(nomcco);

								$("#wtip_paciente").val(data.wtip_paciente);
								$("#wtip_paciente_tal").val(data.wtip_paciente);

								$("#wtipo_ingreso").val(data.tipo_ingreso);
								$("#wtipo_ingreso_tal").val(data.tipo_ingreso);
								$("#wtipo_ingreso_nom_tal").val(data.nombre_tipo_ingreso);

								$("#div_tipo_ingreso").html(data.nombre_tipo_ingreso);

								// --> Tipo de empresa
								$("#tipoEmpresa").val(data.tipoEmpresa);
								$("#tipoEmpresa_tal").val(data.tipoEmpresa);

								// --> Nit de empresa
								$("#nitEmpresa").val(data.nitEmpresa);
								$("#nitEmpresa_tal").val(data.nitEmpresa);

								// --> Nit de empresa
								$("#empPermiteDescuento").val(data.empPermiteDescuento);

								// --> Pintar el detalle de la cuenta simple
								$("#cargos_sin_facturar").val(data.cargos_sin_facturar);
								$("#tabla_informativos_basicos").css("display", "block");

								// --> Pintar el detalle de la cuenta simple resumido
								PintarDetalleCuentaResumido($('#whistoria').val(), data.wwing);

								// --> verificar si se pueden grabar cargos, por congelacion de cuenta.
								validarEstadoDeCuentaCongelada(false)

								$("#linkAbrirHce").show();

								// --> Permiso para hacer decuento en cargos
								if ($("#permisoParaHacerDescuento").val() == 'on' && $("#empPermiteDescuento").val() == 'on') {
									$("#inputDescuento").parent().show();
									$(".inputDescuento").show();
								} else {
									$("#inputDescuento").parent().hide();
									$(".inputDescuento").hide();
								}

								// --> Ingreso activo en unix
								if (data.ingresoActivoUnix)
									$("#ingresoActivoUnix").css({
										"color": "green"
									}).html("<b>Activo</b>").attr("estado", "on");
								else
									$("#ingresoActivoUnix").css({
										"color": "red"
									}).html("<b>Inactivo</b>").attr("estado", "off");

								// TODO: Edier
								console.log(data.grabarCargos);
								$('#grabarCargos').val(data.grabarCargos);
								if (data.grabarCargos == 'on') {
									if ($("#ingresoActivoUnix").attr("estado") == "off") {
										$("#botonGrabar").attr('disabled', 'disabled');
									} else if ($("#ingresoActivoUnix").attr("estado") == "off") {
										$("#botonGrabar").removeAttribute('disabled');
									}
								}

								// --> Datos del alta definitiva, si tiene alta definitiva muestro el enlace para egresar
								if ($.trim(data.altaDefinitiva) == "on" && $.trim(data.enUrgencias) == "on" && $.trim(data.tieneEgreso) != "on") {
									$("#btnEgresar").show();
									$("#btnEgresar").attr("fechaAlta", data.fechaDefinitiva);
									$("#btnEgresar").attr("horaAlta", data.horaDefinitiva);
								} else {
									$("#btnEgresar").hide();
									$("#btnEgresar").attr("fechaAlta", "");
									$("#btnEgresar").attr("horaAlta", "");
								}
								if (data.tieneTurnoCx == 'on')
									$("#botonGrabarIva").show();
								else
									$("#botonGrabarIva").hide();

							}
						}
					},
					'json');
			}
			//-------------------------------------------------------------------------------
			//	Funcion que hace el llamado para obtener el detalle de la cuenta resumido
			//-------------------------------------------------------------------------------
			function PintarDetalleCuentaResumido(historia, ingreso) {
				clearInterval(blinkSpan);

				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'PintarDetalleCuentaResumido',
					historia: historia,
					ingreso: ingreso,
					permiteAnularCargo: $("#permiteAnularCargo").val()

				}, function(respuesta) {

					// --> Obtener si el acordeon estaba visible, para dejarlo tal cual como estaba en el caso de que sea una recarga
					if ($("#div_cargos_cargados_resumido").is(":visible"))
						var desplegar = -1;
					else
						var desplegar = 1;

					// --> Si ya existe un acordeon activo lo destruyo para volverlo a recargar
					var string = $("#accordionDetCuentaResumido").attr("class");
					if (string.indexOf("ui-accordion") >= 0)
						$("#accordionDetCuentaResumido").accordion("destroy");

					// --> Cargar el acordeon
					$("#div_cargos_cargados_resumido").html(respuesta);
					$("#accordionDetCuentaResumido").show().accordion({
						collapsible: true,
						heightStyle: "content",
						active: desplegar
					});

					// --> Asignar valores totales a las barras de los conceptos
					$(".HiddenTotales").each(function() {
						var id_barra = $(this).attr('id_barra');
						var Texto_ba = $(this).val();
						$("#" + id_barra).html(Texto_ba);
					});

					// --> Activar tooltip
					$("[tooltip=si]").tooltip({
						track: true,
						delay: 0,
						showURL: false,
						showBody: ' - ',
						opacity: 0.95,
						left: -50
					});

					// --> Cargar autocomplete de los responsables asociados al paciente, para la opcion de regrabar
					$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
						consultaAjax: '',
						wemp_pmla: $('#wemp_pmla').val(),
						accion: 'obtResponsablesPaciente',
						historia: $("#whistoria").val(),
						ingreso: $("#wing").val(),
						respActual: $("#hidden_responsable").val()

					}, function(Options) {
						$("#respRegrabar").children().remove().end().append(Options).removeAttr('disabled');
					});

					// --> Activar el boton para regrabar, si el usuario tiene permisos para esto
					if ($("#permiteRegrabar").val() == "on") {
						$("#botonRegrabar").show();
						$('[regrabar=si]').each(function() {
							$(this).removeAttr('disabled');
						});
						$("#seleccionarTodosRegrabar").removeAttr('disabled');
					}

					// --> Imprimir soporte del cargo
					if ($("#permiteImprimirSoporteCargo").val() != "on") {
						$("[ImgImprimirSoporte]").hide();
						$("#seleccionarTodosImprimir").hide();
					}

					// --> Imprimir soporte del cargo
					if ($("#permiteImprimirSolicitudExamen").val() != "on")
						$("[ImgImprimirSolicitudExamen]").hide();

					blinkSpan = setInterval(function() {
						$("span[blink]").css('visibility', $("span[blink]").css('visibility') === 'hidden' ? '' : 'hidden')
					}, 700);

				});
			}
			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function cargar_datos_caja() {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
						consultaAjax: '',
						wemp_pmla: $('#wemp_pmla').val(),
						accion: 'cargar_datos_caja'

					}, function(data) {
						nomcco = data.wnomcco;
						$("#div_servicio_tal").val(nomcco);
						ccoGrabador = ((data.wcco != '' && nomcco != '') ? data.wcco + '-' + nomcco : '&nbsp; <img width="15" height="15" src="../../images/medical/sgc/Mensaje_alerta.png"> Usuario no registrado para grabar cargos.');
						$("#div_servicio").html(ccoGrabador);

						$("#wcajadm").val(data.wcajadm);
						$("#wcajadm_tal").val(data.wcajadm);

						$("#wcco").val(data.wcco);
						$("#wcco_tal").val(data.wcco);
						$("#wnomcco").val(data.wnomcco);
						$("#wbod").val(data.wbod);
						$("#wbod_tal").val(data.wbod);
						$("#wcaja").val(data.wcaja);
						$("#nomCajero").val(data.nomCajero);
						$("#nomCajero_tal").val(data.nomCajero);
						$("#permiteCambiarResponsable").val(data.cambiarResponsable);
						$("#permiteCambiarResponsable_tal").val(data.cambiarResponsable);
						$("#permiteCambiarTarifa").val(data.cambiarTarifa);
						$("#permiteCambiarTarifa_tal").val(data.cambiarTarifa);
						$("#permiteRegrabar").val(data.permiteRegrabar);
						$("#permiteRegrabar_tal").val(data.permiteRegrabar);
						$("#permiteImprimirSoporteCargo").val(data.permiteImprimirSoporteCargo);
						$("#permiteImprimirSoporteCargo_tal").val(data.permiteImprimirSoporteCargo);
						$("#permiteImprimirSolicitudExamen").val(data.permiteImprimirSolicitudExamen);
						$("#permiteImprimirSolicitudExamen_tal").val(data.permiteImprimirSolicitudExamen);
						$("#permiteAnularCargo").val(data.permiteAnularCargo);
						$("#permiteAnularCargo_tal").val(data.permiteAnularCargo);
						$("#permiteSeleccionarFacturable").val(data.permiteSeleccionarFacturable);
						$("#permiteSeleccionarFacturable_tal").val(data.permiteSeleccionarFacturable);
						$("#permiteSeleccionarRecExc").val(data.permiteSeleccionarRecExc);
						$("#permiteSeleccionarRecExc_tal").val(data.permiteSeleccionarRecExc);
						$("#permisoParaHacerDescuento").val(data.permisoParaHacerDescuento);

						if (data.permiteSeleccionarRecExc == 'on')
							$("[name=wrecexc_1]").removeAttr('disabled');
						if (data.permiteSeleccionarFacturable == 'on')
							$("[name=wfacturable_1]").removeAttr('disabled');
						if (data.cambiarResponsable == 'on')
							$("#ImgCambioRes").show();
						if (data.cambiarTarifa == 'on')
							$("#ImgCambioTar").show();


						// --> Permiso para aplicar recargo
						$("#permisoParaAplicarRecago").val(data.permisoParaAplicarRecago);
						if ($("#permisoParaAplicarRecago").val() == 'on')
							$("[aplicarRecago]").show();
						else
							$("[aplicarRecago]").hide();
					},
					'json');
			}
			//-----------------------------------------------------
			// --> Seleccionar el tipo de cargo a grabar
			//-----------------------------------------------------
			function tipo_de_cargo(tipo) {
				// --> Limpiar variables
				$("#busc_concepto_1").val("").attr("nombre", "").attr("valor", "");
				$("#wccogra_1").val("");
				$("#busc_procedimiento_1").val("").attr("nombre", "").attr("valor", "");
				$("#busc_terceros_1").val("").attr("nombre", "").attr("valor", "").hide();
				$("#busc_especialidades_1").val("").attr("nombre", "").attr("valor", "").hide();
				$("#conDisponibilidad").hide();
				$("#botonGrabar").removeAttr('disabled');

				// console.log(tipo);

				switch (tipo) {
					case 'devolucion': {
						$("#wdevol").val('on');
						$("#pedirVias").hide();
						//$("#contenedorTrauma").removeAttr("style");
						$("#tdVias1").hide(0);
						$("#tdVias2").hide(0);
						$("#botonGrabar").html('DEVOLVER');
						traer_conceptos('on');
						break;
					}
					case 'cargo': {
						$("#wdevol").val('off');
						$("#pedirVias").hide();
						//$("#contenedorTrauma").removeAttr("style");
						$("#tdVias1").hide(0);
						$("#tdVias2").hide(0);
						$("#botonGrabar").html('GRABAR CARGO');
						//TODO :edier
						if ($("#ingresoActivoUnix").attr("estado") == "off") {
							$("#botonGrabar").attr('disabled', 'disabled');
						}
						traer_conceptos('%');
						break;
					}
					case 'trauma': {
						$("#contenedorTrauma").css({
							"border": "1px solid #000000",
							"padding": "2px",
							"background-color": "#FFFFCC"
						});
						$("#pedirVias").show(400);
						$("#tdVias1").show(0);
						pintarSelectVias();
						$("#botonGrabar").html('GRABAR TRAUMA');
					}
				}
			}
			//----------------------------------------------------------------
			//	--> Pintar seleccionador de vias.
			//----------------------------------------------------------------
			function pintarSelectVias() {
				if ($("#numVias").val() == '' || $("#numVias").val() < 1)
					numVias = 1;
				else
					numVias = $("#numVias").val();

				var htmlVias = "<select id='selectVia' style='background-color:#FFFFCC;width:45px;'>";
				for (x = 1; x <= numVias; x++)
					htmlVias = htmlVias + "<option>" + x + "</option>";

				htmlVias = htmlVias + "</select>";

				$("#tdVias2").html(htmlVias).show(0);
			}
			//------------------------------------------------------------------
			//	--> Autocomplete para seleccionar el concepto
			//------------------------------------------------------------------
			function cargar_conceptos(ArrayValores) {
				var conceptos = new Array();
				var index = -1;

				for (var cod_con in ArrayValores) {
					index++;
					conceptos[index] = {};
					conceptos[index].label = cod_con + '-' + ArrayValores[cod_con]['nombre'];
					conceptos[index].nombre = ArrayValores[cod_con]['nombre'];
					conceptos[index].valor = cod_con;
					conceptos[index].modificaVal = ArrayValores[cod_con]['modificaVal'];
				}

				$("#busc_concepto_1").autocomplete({
					minLength: 0,
					source: conceptos,
					select: function(event, ui) {
						$("#busc_concepto_1").val(ui.item.label);
						$("#busc_concepto_1").attr('valor', ui.item.valor);
						$("#busc_concepto_1").attr("nombre", ui.item.nombre);
						$("#busc_concepto_1").attr("modificaVal", ui.item.modificaVal);
						$("#busc_concepto_1").attr("polManejoTerceros", '');
						$("#busc_concepto_1").attr("codigoPolitica", '');
						$("#busc_procedimiento_1").val('');
						$("#busc_terceros_1").hide();
						$('#busc_especialidades_1').hide();
						$("#conDisponibilidad").hide();
						$(".tdCuadroTurno").hide();

						ValidarTipoConcepto(ui.item.valor, 1);
						validarEstadoDeCuentaCongelada(true);
						return false;
					}
				});
				limpiaAutocomplete('busc_concepto_1');
			}

			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function ValidarTipoConcepto(CodigoConcepto, consecutivo) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'ObtenerElTipoDeConcepto',
					CodigoConcepto: CodigoConcepto

				}, function(data) {
					if (data.Facturable == 'si') {
						datos_desde_concepto(CodigoConcepto, consecutivo);
					} else {
						$("#busc_concepto_1").val('');
						$("#busc_concepto_1").attr('valor', '');
						$("#busc_procedimiento_1").val('');
						alert("El concepto est� configurado como no facturable.");
					}
				}, 'json');
			}
			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function traer_conceptos(muevenInventario) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'traer_conceptos',
					muevenInventario: muevenInventario
				}, function(data) {
					var ArrayValores = data;
					cargar_conceptos(ArrayValores);
				}, 'json');
			}
			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function traer_datos_generales() {
				// trae fecha
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'traer_datos_generales'

				}, function(data) {
					$("#wfeccar").val(data.fecha_actual);
				}, 'json');
			}
			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function datos_desde_concepto(cod_concepto, num_paquete) {
				var validaciones = '';
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'datos_desde_concepto',
					wcodcon: cod_concepto,
					wcodemp: $("#hidden_responsable").val(),
					wtar: $("#hidden_tarifa").val(),
					ccoUbiActualPac: $("#ccoActualPac").val()
				}, function(data) {
					if (data.actualizando) {
						jAlert("<span style='color:#2a5db0;font-size:10pt'>En este momento no se pueden grabar cargos de " + $("#busc_concepto_1").attr('nombre') + "<br>porque se est� realizando el proceso de actualizaci�n de tarifas.<br><br>Por favor espere un momento para volver a grabar el cargo.</span>", "Mensaje");

						$("#busc_concepto_1").val('');
						$("#busc_concepto_1").attr('valor', '');
						$("#busc_procedimiento_1").val('');
						return;
					}

					// --> 	2019-10-03: Jerson Trujillo. Para cargos de medicamentos y materiales, no se permitir� grabarle a un ingreso menor al ultimo.
					//		Esto se hace porque se present� un caso donde una secretaria por error grab� un medicamento a un ingreso menor al actual
					//		Y el integrador se bloqueo porque este tenia una fecha menor al mes actual. requerimiento (01)1710 5061
					if (data.wconinv == 'on' && $("#msjIngPost").attr("hayIngresoPosterior") == "on") {
						jAlert("<span style='color:#2a5db0;font-size:10pt'>Solo se pueden grabar cargos de " + $("#busc_concepto_1").attr('nombre') + "<br>en el ultimo ingreso del paciente.</span>", "Mensaje");

						$("#busc_concepto_1").val('');
						$("#busc_concepto_1").attr('valor', '');
						$("#busc_procedimiento_1").val('');
						return;
					}

					$("#wccogra_" + num_paquete).html(data.option_select);
					$("#warctar").val(data.warctar);
					$("#wconabo_" + num_paquete).val(data.wconabo);
					$("#wcontip_" + num_paquete).val(data.wcontip);
					$("#wconmva").val(data.wconmva);
					$("#wconinv_" + num_paquete).val(data.wconinv);
					$("#wconser_" + num_paquete).val(data.wconser);

					// --> validar multitercero
					datos_desde_conceptoxcco();

					// --> Si el concepto no modifica valor, deshabilito la edicion del campo de valor
					if (data.wconmva == 'N')
						$("#wvaltar_1").attr('disabled', 'disabled');
					else
						$("#wvaltar_1").removeAttr('disabled');

					// --> si el concepto no es de abono trae los procedimientos asociados a este
					if (data.wconabo != 'on') {
						traer_procedimientos(cod_concepto);
					} else {
						traer_interfaz_abonos(data.wconmca);
					}
				}, 'json');
			}
			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function traer_interfaz_abonos(wconmca) {

				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'traer_interfaz_abonos',
					wconmca: wconmca,
					wcco: $("#wcco").val()

				}, function(data) {

					if (/!!!/.test(data)) {
						alert(data);
					} else {
						$("#div_abono").html(data);
						$.blockUI({
							message: $('#div_abono'),
							css: {
								left: ($(window).width() - 1500) / 2 + 'px',
								top: '350px',
								width: '1500px'
							}
						});
					}
				});

			}

			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function traer_procedimientos(cod_concepto) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'traer_detalle_del_concepto',
					cod_concepto: cod_concepto,
					ConceptoInventar: $("#wconinv_1").val(),
					Tarifa: $("#hidden_tarifa").val(),
					wcod_empresa: $("#hidden_responsable").val(),
					centroCostos: $("#wcco").val()
				}, function(data) {
					var ArrayValores = eval('(' + data + ')');
					cargar_procedimiento(ArrayValores);
				});
			}
			//----------------------------
			//	Nombre:
			//	Descripcion:
			//	Entradas:
			//	Salidas:
			//----------------------------
			function cargar_procedimiento(ArrayValores) {
				var procedimientos = new Array();
				var index = -1;
				for (var cod_pro in ArrayValores) {
					index++;
					procedimientos[index] = {};
					procedimientos[index].value = cod_pro + '-' + ArrayValores[cod_pro];
					procedimientos[index].label = cod_pro + '-' + ArrayValores[cod_pro];
					procedimientos[index].nombre = ArrayValores[cod_pro];
					procedimientos[index].valor = cod_pro;
				}
				$("#busc_procedimiento_1").autocomplete({
					minLength: 0,
					source: procedimientos,
					select: function(event, ui) {
						$("#busc_procedimiento_1").val(ui.item.label);
						$("#busc_procedimiento_1").attr('valor', ui.item.valor);
						$("#busc_procedimiento_1").attr("nombre", ui.item.nombre);
						datos_desde_procedimiento(true);
						return false;
					}
				});
				limpiaAutocomplete('busc_procedimiento_1');
			}


			//-------------------------------------------------------
			//	--> Funcion que obtiene la tarifa (valor) del cargo
			//-------------------------------------------------------
			function delay_datos_desde_procedimiento(cargarTerceros) {
				$("#mensajeTarifa").show();
				$("#wvaltar_1").hide();
				setTimeout(function() {
					datos_desde_procedimiento(cargarTerceros);
				}, 400);
			}
			//-------------------------------------------------------
			//	--> Funcion que obtiene la tarifa (valor) del cargo
			//-------------------------------------------------------
			function datos_desde_procedimiento(cargarTerceros) {
				$("#wvaltar_1").attr('disabled', 'disabled');
				$("#wvaltar_1").attr('tarifaDigitada', 'no');
				$("#wvaltar_1").css("border", "");
				$("#wvaltar_1").attr("codHomologar", "");
				$("#wvaltar_1").val("");

				// --> Validar que haya un procedimiento seleccionado
				if ($("#busc_procedimiento_1").attr('valor') == '') {
					$("#busc_procedimiento_1").val("");
					$("#busc_procedimiento_1").attr("nombre", "");
					return;
				}
				// --> Validar que haya un cco seleccionado
				if ($("#wccogra_1").val() == '') {
					mostrar_mensaje("Para conocer el valor de la tarifa debe seleccionar un centro de costos.");
					borderojo($("#wccogra_1"));
					return;
				}

				// --> Mensaje de calculando
				$("#mensajeTarifa").show();
				$("#wvaltar_1").hide();

				// --> Envio de variables
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'datos_desde_procedimiento',
					wcodcon: $("#busc_concepto_1").attr('valor'),
					CodTipEmp: $("#tipoEmpresa").val(),
					CodNit: $("#nitEmpresa").val(),
					wcodemp: $("#hidden_responsable").val(),
					warctar: $("#warctar").val(),
					wccogra: $("#wccogra_1").val(),
					ccoActualPac: $("#ccoActualPac").val(),
					wprocod: $("#busc_procedimiento_1").attr('valor'),
					wtar: $("#hidden_tarifa").val(),
					whis: $("#whistoria").val(),
					wing: $("#wing").val(),
					MueveInventario: $("#wconinv_1").val(),
					wfeccar: $("#wfeccar").val(),
					wdevol: $("#wdevol").val(),
					wtipo_ingreso: $("#wtipo_ingreso").val(),
					especialidad: ($("#busc_especialidades_1").val() == '') ? '*' : $("#busc_especialidades_1").val(),
					cobraHonorarios: (($("#aplicarHonorarios").is(":checked")) ? 'on' : 'off'),
					enTurno: (($("#conDisponibilidad").is(":checked")) ? 'on' : 'off'),
					medico: $("#busc_terceros_1").attr('valor'),
					grupoMedico: $("#grupoMedico").val(),
					fecha: $("#wfeccar").val(),
					hora: $("#whora_cargo_1").val() + ':00'

				}, function(data) {

					$(".tdPedirCodRips").hide();
					$("#codRips").hide();

					if (data.Facturable == 'si') {
						// --> Si existe excepcion tarifaria, entonces se permite modificar el valor del cargo
						if (data.excepcionTarifaria) {
							$("#wvaltar_1").removeAttr('disabled');
							$("#wvaltar_1").val('');
							$("#wexiste_1").val(0);
							$("#wtipfac_1").val('MANUAL');
							$("#wexidev").val('');

							$("#mensajeTarifa").hide();
							$("#wvaltar_1").show();

							$(".tdPedirCodRips").show();
							$("#codRips").show();

							mostrar_mensaje("Se encontr&oacute; una excepci&oacuten tarifaria.<br>Por favor digite el valor de la tarifa");
							$("#wvaltar_1").css("border", "1px dotted #FF0400");

							// setTimeout(function() {
							// $("#codRips").focus();
							// }, 1000);

							// --> Aplicar politica de manejo de terceros, si existe.
							if (cargarTerceros)
								politicaManejoTercero();

							$("#codRips").focus();

							return;
						}

						// --> No se encontro tarifa
						if (data.error) {
							//  --> Si no se encuentra tarifa y el concepto permite modificar valor
							if ($("#busc_concepto_1").attr('modificaVal') == 'S') {
								$("#wvaltar_1").removeAttr('disabled');
								$("#wvaltar_1").val('');
								$("#wexiste_1").val(0);
								$("#wtipfac_1").val('MANUAL');
								$("#wexidev").val('');
								$("#wvaltar_1").attr('tarifaDigitada', 'si');

								// --> Aplicar politica de manejo de terceros, si existe.
								if (cargarTerceros)
									politicaManejoTercero();

								if (($("#wcontip_1").val() == 'C' || politicaManejoTerceros == 'C') && $("#busc_terceros_1").val() == '') {
									$("#mensajeTarifa").hide();
									$("#wvaltar_1").show();
									return;
								} else {
									mostrar_mensaje("Por favor digite el valor de la tarifa");
									$("#wvaltar_1").css("border", "1px dotted #FF0400");
									setTimeout(function() {
										$("#wvaltar_1").focus();
									}, 1000);
								}
							} else {
								// --> Aplicar politica de manejo de terceros, si existe.
								if (cargarTerceros)
									politicaManejoTercero();

								setTimeout(function() {

									// -->
									politicaManejoTerceros = $('#busc_concepto_1').attr('polManejoTerceros');
									// if((politicaManejoTerceros == 'OP' || politicaManejoTerceros == 'C' || $("#wcontip_1").val() == 'C') && $("#busc_terceros_1").attr("valor") == '')
									if ($("#busc_terceros_1").is(":visible")) {
										mostrar_mensaje(data.mensaje);
										return;
									} else {
										mostrar_mensaje(data.mensaje);
										$("#busc_procedimiento_1").val('');
										$("#busc_procedimiento_1").focus();
										$("#wvaltar_1").val('');
										$("#wexiste_1").val('');
										$("#wtipfac_1").val('');
										$("#wexidev").val('');
										$("#div_valor_total_1").html('');
										$("#aplicarHonorarios").attr("CHECKED", "CHECKED");
										$(".tdConHonorarios").hide(200);
										$("#aplicarHonorarios").attr("manejaDobleTarHon", "NO");
										$(".tdPedirLote").hide(200);
										$("#loteVacuna").val("");
										$("#codRips").val("");
										$("#codRips").attr("valor", "");
									}
								}, 1000);
							}
						} else {
							$("#wvaltar_1").val(data.wvaltar);
							$("#wvaltar_1").attr("codHomologar", data.codHomologar);
							$("#wexiste_1").val(data.wexiste);
							$("#wtipfac_1").val(data.wtipfac);
							$("#wexidev").val(data.wexidev);

							calcularValorTotal();

							// --> Si el procedimiento maneja aprovechamiento
							if (data.aprovechamiento == 'on') {
								$('.tdAprovechamiento').show();
								$('#waprovecha_1').attr("manejaAprovechamiento", "on");
							} else {
								$('.tdAprovechamiento').hide();
								$('#waprovecha_1').attr("manejaAprovechamiento", "off");
							}

							// --> 	Activar el checkbox "Con Honorarios", ya que el procedimiento maneja dos tarifas
							//		una que incluye y otra que no, los honorarios.
							if (data.manejaTarifaConHonorarios) {
								$(".tdConHonorarios").show(200);
								$("#aplicarHonorarios").attr("manejaDobleTarHon", "SI");
							} else {
								$("#aplicarHonorarios").attr("manejaDobleTarHon", "NO");
								$("#aplicarHonorarios").attr("CHECKED", "CHECKED");
								$(".tdConHonorarios").hide(200);
							}

							// --> Aplicar politica de manejo de terceros, si existe.
							if (cargarTerceros)
								politicaManejoTercero();

							// --> Si es se debe pedir lote (Esto es para las vacunas)
							if ($.trim(data.pedirLote) == 'on')
								$(".tdPedirLote").show(200);
							else {
								$(".tdPedirLote").hide(200);
								$("#loteVacuna").val("");
							}
						}
					} else {
						$("#busc_procedimiento_1").val('');
						$("#busc_procedimiento_1").attr('valor', '');
						$("#wvaltar_1").val('');
						$("#wexiste_1").val('');
						$("#wtipfac_1").val('');
						$("#wexidev").val('');
						$("#div_valor_total_1").html('');

						$("#busc_terceros_1").val('');
						$("#busc_terceros_1").attr('valor', '');
						$("#busc_terceros_1").attr('nombre', '');
						$("#codRips").val("");
						$("#codRips").attr("valor", "");

						$("#busc_terceros_1").hide();
						$('#busc_especialidades_1').hide();
						$('#busc_especialidades_1').html("<option value='' selected>Seleccione..</option>");

						$("#conDisponibilidad").hide();

						$(".tdCuadroTurno").hide();
						mostrar_mensaje("El procedimiento est&aacute; configurado como no facturable.");
					}

					$("#mensajeTarifa").hide();
					$("#wvaltar_1").show();

				}, 'json');
			}

			function valVacio(elmento) {
				(($(elmento).val() == "") ? $(elmento).val(0) : $(elmento).val());
				calcularValorTotal();
			}

			function calcularValorTotal() {
				((parseInt($("#inputDescuento").val().replace(",", "")) > "100") ? $("#inputDescuento").val(0) : "");

				var tiposEmpRedondean = new Object();
				var tiposEmpRedondean = JSON.parse($("#tiposEmpRedondean").val());
				var valDescuento = parseInt($("#inputDescuento").val().replace(",", ""));
				var cant_total = (($("#wvaltar_1").val() * 1) * ($("#wcantidad_1").val() * 1));
				cant_total = cant_total - (cant_total * (valDescuento / 100));

				// --> Validar si el tipo de empresa del paciente, debe redondear valor
				for (var codTipEmp in tiposEmpRedondean) {
					if (tiposEmpRedondean[codTipEmp] == $("#tipoEmpresa").val()) {
						cant_total = Math.round(cant_total / 100) * 100;
						break;
					}
				}

				cant_total = number_format(cant_total, 0, ',', '.');

				$("#div_valor_total_1").html(cant_total);
			}

			function traer_terceros() {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'traer_terceros'
				}, function(data) {
					ArrayInfoTerceros = eval('(' + data + ')');
					cargar_terceros(ArrayInfoTerceros, 1);
				});
			}

			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function cargar_terceros(ArrayValores, n) {
				var index = -1;
				var arrayTerceros = new Array();
				for (var cod_ter in ArrayValores) {
					index++;
					arrayTerceros[index] = {};
					arrayTerceros[index].value = cod_ter;
					arrayTerceros[index].label = cod_ter + '-' + ArrayValores[cod_ter]['nombre'];
					arrayTerceros[index].name = ArrayValores[cod_ter]['nombre'];
					arrayTerceros[index].especialidades = ArrayValores[cod_ter]['especialidad'];
				}
				$("#busc_terceros_" + n).autocomplete({
					minLength: 0,
					source: arrayTerceros,
					select: function(event, ui) {
						$("#busc_terceros_" + n).val(ui.item.label);
						$("#busc_terceros_" + n).attr('valor', ui.item.value);
						$("#busc_terceros_" + n).attr('nombre', ui.item.name);
						cargarSelectEspecialidades(ui.item.especialidades, n);

						datos_desde_tercero('on');
						//verificarDisponibilidad();

						return false;
					}
				});
				limpiaAutocomplete('busc_terceros_' + n);
			}
			//---------------------------------------------------------------------------------
			// --> Verificar si un medico con su respectiva especialidad, hacen disponibilidad
			//---------------------------------------------------------------------------------
			/*function verificarDisponibilidad()
			{
				$.post("<?= $URL_AUTOLLAMADO ?>?"+url_add_params,
				{
					consultaAjax:   '',
					wemp_pmla:      $('#wemp_pmla').val(),
					accion:         'verificarDisponibilidad',
					medico:			$("#busc_terceros_1").attr('valor'),
					especialidad:	$("#busc_especialidades_1").val()
				}, function (respuesta){
					if(respuesta.haceDispon == "SI")
						$(".checkDisponibilidad").show(200);
					else
					{
						$(".checkDisponibilidad").hide(200);
						$("#conDisponibilidad").removeAttr("checked");
					}

					datos_desde_tercero('on');

				}, 'json');
			}*/

			//------------------------------------------------------------------------
			//	Nombre:			cargarSelectEspecialidades
			//	Descripcion:	Segun el tercero trae sus especialidades y las carga en el select especialidaes
			//	Entradas:		cadena: especialidades del tercero
			//	Salidas:
			//-----------------------------------------------------------------------
			function cargarSelectEspecialidades(cadena, n) {
				var especialidades = cadena.split(",");
				var html_options = "";
				for (var i in especialidades) {
					var especialidad = especialidades[i].split("-");
					html_options += "<option value='" + especialidad[0] + "'>" + especialidad[1] + "</option>";
				}
				$("#busc_especialidades_" + n).html(html_options);

				// -->  Vuelve y se verifica la tarifa del procedimiento, ya que dependiendo la especialidad puede que
				//		cambie el valor de la tarifa.
				setTimeout(function() {
					datos_desde_procedimiento(false);
				}, 1000);
			}
			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function datos_desde_tercero(limpiarCuadroTurno) {
				if (limpiarCuadroTurno == 'on') {
					$("#tipoCuadroTurno").attr("validar", "no").html("");
					$(".tdCuadroTurno").hide(300);
				}

				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'datos_desde_tercero',
					wcodter: $("#busc_terceros_1").attr("valor"),
					wcodesp: $('#busc_especialidades_1').val(),
					wcodcon: $('#busc_concepto_1').attr('valor'),
					wtip_paciente: $('#wtip_paciente').val(),
					whora_cargo: $('#whora_cargo_1').val(),
					wfecha_cargo: $('#wfeccar').val(),
					wtipoempresa: $("#tipoEmpresa").val(),
					wtarifa: $("#tarifa_original").val(),
					wempresa: $("#responsable_original").val(),
					wcco: $("#wccogra_1").val(),
					wcod_procedimiento: $("#busc_procedimiento_1").attr('valor'),
					conDisponibilidad: (($("#conDisponibilidad").is(":checked")) ? $("#conDisponibilidad").attr("codMedDisponible") : $("#conDisponibilidad").attr("codMedNoDisponible")),
					cuadroTurno: (($("#tipoCuadroTurno").attr("validar") == 'si') ? $("#tipoCuadroTurno").val() : '')

				}, function(data) {
					if (data.error > 0)
						alert(data.mensaje);
					else {
						if ($.trim(data.dobleCuadroDeTurno) == 1) {
							var select = "<option value=''>Seleccione..</option>";
							// --> Pintar select de los cuadros de turno en los que el medico esta disponible
							$.each(data.cuadrosDeTurno, function(index, value) {
								select += "<option value='" + index + "'>" + value + "</option>";
							});
							$("#tipoCuadroTurno").html("");
							$("#tipoCuadroTurno").attr("validar", "si").append(select);
							$(".tdCuadroTurno").show(300);
							$("#wporter_1").val(data.wporter);
							$("#grupoMedico").val(data.grupoMedico);
							$("#wterunix").val(data.wterunix);
						} else {
							if ($("#tipoCuadroTurno").attr("validar") == 'no') {
								$("#tipoCuadroTurno").attr("validar", "no").html("");
								$(".tdCuadroTurno").hide(300);
								$("#wporter_1").val(data.wporter);
								$("#grupoMedico").val(data.grupoMedico);
								$("#grupoMedico").val(data.grupoMedico);
								$("#wterunix").val(data.wterunix);
							} else {
								$("#tipoCuadroTurno").attr("validar", "si");
								$(".tdCuadroTurno").show(300);
								$("#wporter_1").val(data.wporter);
								$("#grupoMedico").val(data.grupoMedico);
								$("#wterunix").val(data.wterunix);
							}
						}
					}

				}, 'json');
			}
			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function number_format(number, decimals, dec_point, thousands_sep) {
				var n = !isFinite(+number) ? 0 : +number,
					prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
					sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
					dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
					s = '',
					toFixedFix = function(n, prec) {
						var k = Math.pow(10, prec);
						return '' + Math.round(n * k) / k;
					};

				s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
				if (s[0].length > 3) {
					s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
				}
				if ((s[1] || '').length < prec) {
					s[1] = s[1] || '';
					s[1] += new Array(prec - s[1].length + 1).join('0');
				}
				return s.join(dec);
			}

			function borderojo(Elemento) {
				Elemento.parent().css("border", "1px dotted #FF0400").attr('borderred', 'si');
			}
			//----------------------------
			// Nombre:
			// Descripcion:
			// Entradas:
			// Salidas:
			//----------------------------
			function grabar(Boton) {
				var PermitirGrabar = true;
				var graba_varios_terceros = 0;
				var porcentajeParticipacion = '';
				var wcodter = '';
				var wnomter = '';
				var wespecialidad = '';

				$('[borderred=si]').css("border", "").removeAttr('borderred');
				var consecutivo = '1';

				//---------------------------------------------------------------
				// --> Validacion de campos obligatorios
				//---------------------------------------------------------------
				// --> validar historia
				if ($("#whistoria").val() == '') {
					borderojo($("#whistoria"));
					mostrar_mensaje('No se ha seleccionado ningun paciente.');
					PermitirGrabar = false;
					return;
				}
				// --> validacion del concepto
				if ($("#busc_concepto_" + consecutivo).attr('valor') == '') {
					borderojo($("#busc_concepto_" + consecutivo));
					mostrar_mensaje('No se ha seleccionado ningun concepto');
					PermitirGrabar = false;
					return;
				}

				// --> Validacion de centro de costos
				if ($("#wccogra_" + consecutivo).val() == '') {
					borderojo($("#wccogra_" + consecutivo));
					mostrar_mensaje('El concepto no tiene un centro de costos asociado');
					PermitirGrabar = false;
					return;
				}

				// --> Validacion de procedimiento
				if ($("#busc_procedimiento_" + consecutivo).attr('valor') == '') {
					borderojo($("#busc_procedimiento_" + consecutivo));
					mostrar_mensaje("No se ha seleccionado ningun procedimiento.");
					PermitirGrabar = false;
					return;
				}

				var politicaManejoTerceros = $('#busc_concepto_1').attr('polManejoTerceros');
				var codigoPolitica = $('#busc_concepto_1').attr('codigoPolitica');

				// --> 	Si para el concepto no existe una politica definida para el manejo de terceros,
				//		o la politica es que sea C (compartido).
				if (politicaManejoTerceros == '' || politicaManejoTerceros == 'C') {
					// --> 	Si el concepto es compartido o por politica se dice que maneje terceros, osea compartido
					//		y esta chequeada la opcion de aplicar honorarios.
					if (($("#wcontip_" + consecutivo).val() == 'C' || politicaManejoTerceros == 'C' || $("#aplicarHonorarios").attr("manejaDobleTarHon") == "SI") && $("#aplicarHonorarios").is(":checked")) {
						// --> Validacion del tercero
						if ($("#busc_terceros_" + consecutivo).val() == '') {
							borderojo($("#busc_terceros_" + consecutivo));
							mostrar_mensaje("No se ha seleccionado ningun tercero");
							PermitirGrabar = false;
							return;
						}
						// --> valida de  graba varios terceros
						if ($("#busc_terceros_" + consecutivo).attr('cedulas') == undefined) {
							// --> Validacion de la especialidad
							if ($("#busc_especialidades_" + consecutivo).val() == '') {
								borderojo($("#busc_especialidades_" + consecutivo));
								mostrar_mensaje("No ha seleccionado la especialidad del tercero");
								PermitirGrabar = false;
								return;
							}
						} else {
							graba_varios_terceros = 1;
						}
						// --> Validacion del % de participacion
						porcentajeParticipacion = $("#wporter_" + consecutivo).val();
						if (porcentajeParticipacion == "") {
							borderojo($("#busc_terceros_" + consecutivo));
							mostrar_mensaje("El tercero no tiene porcentaje de participacion en este concepto");
							PermitirGrabar = false;
							return;
						}
						wcodter = $("#busc_terceros_" + consecutivo).attr('valor');
						wnomter = $("#busc_terceros_" + consecutivo).attr('nombre');
						wespecialidad = $("#busc_especialidades_" + consecutivo).val();
					}
				}

				// --> Si tiene politica de tercero OP (Opcional) y seleccionaron un tercero
				if (politicaManejoTerceros == 'OP' && $("#busc_terceros_" + consecutivo).attr('valor') != '') {
					porcentajeParticipacion = $("#wporter_" + consecutivo).val();
					if (porcentajeParticipacion == "") {
						borderojo($("#busc_terceros_" + consecutivo));
						mostrar_mensaje("El tercero no tiene porcentaje de participacion en este concepto");
						PermitirGrabar = false;
						return;
					}
					wcodter = $("#busc_terceros_" + consecutivo).attr('valor');
					wnomter = $("#busc_terceros_" + consecutivo).attr('nombre');
					wespecialidad = $("#busc_especialidades_" + consecutivo).val();
				}

				// --> Validar el tipo de cuadro de turno, si es el caso
				var tipoCuadroTurno = '';
				if ($("#tipoCuadroTurno").attr("validar") == 'si') {
					if ($("#tipoCuadroTurno").val() != '')
						tipoCuadroTurno = $("#tipoCuadroTurno").val();
					else {
						borderojo($("#tipoCuadroTurno"));
						mostrar_mensaje("Debe seleccionar el cuadro de turno en el que est� el m�dico.");
						return;
					}
				}

				// --> Validacion de la cantidad
				if ($("#wcantidad_" + consecutivo).val() <= 0) {
					borderojo($("#wcantidad_" + consecutivo));
					mostrar_mensaje("La cantidad digitada debe ser mayor a cero");
					PermitirGrabar = false;
					return;
				}

				cantMaxInv = parseInt($("#cantidadMaxPermitidaParaGrabarCargosInv").val());
				if ($("#wcantidad_" + consecutivo).val() > cantMaxInv) {
					if ($("#wconinv_1").val() == "on") {
						if (!confirm("Alerta! esta grabando una cantidad muy alta. Desea continuar?")) {
							$('#wcantidad_1').focus();
							return;
						}
					} else {
						alert("Alerta! no se permite grabar una cantidad mayor a " + cantMaxInv + ".");
						return;
					}
				}

				// --> Validacion del valor de la tarifa
				if ($("#wvaltar_" + consecutivo).val() == '' || $("#wvaltar_" + consecutivo).val() == '0') {
					borderojo($("#wvaltar_" + consecutivo));
					mostrar_mensaje("No existe la tarifa para el procedimiento");
					PermitirGrabar = false;
					return;
				}
				// --> Validacion de la fecha del cargo
				if ($("#wfeccar").val() == "") {
					borderojo($("#wfeccar"));
					mostrar_mensaje("No se ha ingresado la fecha para grabar este cargo");
					PermitirGrabar = false;
					return;
				}

				// --> Validacion del lote
				if ($("#loteVacuna").parent().is(":visible")) {
					if ($("#loteVacuna").val() == "") {
						borderojo($("#loteVacuna"));
						mostrar_mensaje("Debe ingresar el # de lote");
						PermitirGrabar = false;
						return;
					}
				}

				// --> Validacion del codigo para rips
				codigoRips = "";
				if ($("#codRips").is(":visible")) {
					if ($("#codRips").attr("valor") == "") {
						borderojo($("#codRips"));
						mostrar_mensaje("Debe ingresar el codigo para los RIPS");
						PermitirGrabar = false;
						return;
					} else
						codigoRips = $("#codRips").attr("valor");
				}

				//---------------------------------------------------------------
				// --> Envio de variables para relizar la grabacion del cargo
				//---------------------------------------------------------------
				if (PermitirGrabar) {
					// --> Deshabilitar el boton grabar hasta que termine el proceso
					boton = jQuery(Boton);
					boton.html('&nbsp;<img class="" border="0" src="../../images/medical/ajax-loader2.gif" title="Cargando.." >').attr("disabled", "disabled");


					var tarifa_original = $("#tarifa_original").val();
					var responsable_original = $("#responsable_original").val();

					if ($("#aplicarHonorarios").attr("manejaDobleTarHon") == 'SI')
						var cobraHonorarios = (($("#aplicarHonorarios").is(":checked")) ? 'on' : 'off');
					else
						var cobraHonorarios = '';

					$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
						consultaAjax: '',
						wemp_pmla: $('#wemp_pmla').val(),
						accion: 'GrabarCargo',
						whistoria: $("#whistoria").val(),
						wing: $("#wing").val(),
						wno1: $("#wno1").val(),
						wno2: $("#wno2").val(),
						wap1: $("#wap1").val(),
						wap2: $("#wap2").val(),
						wdoc: $("#wdoc").val(),
						wcodemp: $("#hidden_responsable").val(),
						wnomemp: $("#wnomemp").val(),
						tipoEmpresa: $("#tipoEmpresa").val(),
						nitEmpresa: $("#nitEmpresa").val(),
						tipoPaciente: $('#wtip_paciente').val(),
						tipoIngreso: $('#wtipo_ingreso').val(),
						wfecing: $("#wfecing").text(),
						wtar: $("#hidden_tarifa").val(),
						wser: $("#wser").val(),
						wcodcon: $("#busc_concepto_" + consecutivo).attr('valor'),
						wnomcon: $("#busc_concepto_" + consecutivo).attr('nombre'),
						warctar: $("#warctar").val(),
						wprocod: $("#busc_procedimiento_" + consecutivo).attr('valor'),
						wpronom: $("#busc_procedimiento_" + consecutivo).attr('nombre'),
						wcodter: wcodter,
						wnomter: wnomter,
						wporter: porcentajeParticipacion,
						codigoPolitica: codigoPolitica,
						grupoMedico: ((wcodter != '') ? $("#grupoMedico").val() : ''),
						wterunix: ((wcodter != '') ? $("#wterunix").val() : ''),
						wcantidad: $("#wcantidad_" + consecutivo).val(),
						wvaltar: $("#wvaltar_" + consecutivo).val(),
						porDescuento: $("#inputDescuento").val(),
						wrecexc: $('[name=wrecexc_' + consecutivo + ']:checked').val(),
						wfacturable: (($("#wfacturable_S").is(':checked')) ? 'S' : 'N'), //$('[name=wfacturable_'+consecutivo+']:checked').val(),
						aplicarRecago: (($("#aplicarRecagSi").is(':checked')) ? 'on' : 'off'),
						wcco: $("#wcco").val(),
						wccogra: $("#wccogra_" + consecutivo).val(),
						wfeccar: $("#wfeccar").val(),
						wconinv: $("#wconinv_" + consecutivo).val(),
						wconabo: $("#wconabo_" + consecutivo).val(),
						wdevol: $("#wdevol").val(),
						waprovecha: $("#waprovecha_" + consecutivo).attr("manejaAprovechamiento"),
						wexiste: $("#wexiste_" + consecutivo).val(),
						wbod: $("#wbod").val(),
						wconser: $("#wconser_" + consecutivo).val(),
						wtipfac: $("#wtipfac_" + consecutivo).val(),
						wespecialidad: wespecialidad,
						whora_cargo: $("#whora_cargo_" + consecutivo).val(),
						nomCajero: $("#nomCajero").val(),
						cobraHonorarios: cobraHonorarios,
						wgraba_varios_terceros: graba_varios_terceros,
						wcodcedula: $("#busc_terceros_" + consecutivo).attr('cedulas'),
						estaEnTurno: (($("#conDisponibilidad").is(":checked")) ? 'on' : 'off'),
						tipoCuadroTurno: tipoCuadroTurno,
						ccoActualPac: $("#ccoActualPac").val(),
						tarifaDigitada: $("#wvaltar_1").attr('tarifaDigitada'),
						codHomologar: $("#wvaltar_1").attr('codHomologar'),
						loteVacuna: $("#loteVacuna").val(),
						codigoRips: codigoRips
					}, function(data) {

						// --> Mostrar mensajes
						mostrar_mensaje(data.Mensajes.mensaje);

						// --> Si no hay ningun error
						if (!data.Mensajes.error) {

							$.mtxCitas({
								historia: $("#whistoria").val(),
								ingreso: $("#wing").val(),
								wemp_pmla: $('#wemp_pmla').val(),
								cco_sede: $("#wccogra_1").val(),
								accept: function() {

									PintarDetalleCuentaResumido($("#whistoria").val(), $("#wing").val());
									// --> Limpiar el formulario
									limpiar_campos();

									// --> Actualizar informacion del responsable, ya que puede que esta haya cambiado al superar algun tope.
									actualizarResposable();

									cargar_datos('whistoria');

									// --> Descongelar la cuenta del paciente.
									congelarCuentaPaciente('off')

									// --> Chequear reconocido, facturable SI y con honorarios.
									$("#wrecexc_R").attr("CHECKED", "CHECKED");
									$("#wfacturable_S").attr("CHECKED", "CHECKED");
									$("#aplicarHonorarios").attr("CHECKED", "CHECKED");
									$(".tdConHonorarios").hide(200);
									$("#aplicarHonorarios").attr("manejaDobleTarHon", "NO");

									// --> Imprimir soporte del cargo
									if ($("#permiteImprimirSoporteCargo").val() == "on")
										imprimirSoporteCargo(data.Mensajes.idCargo, $("#whistoria").val(), $("#wing").val(), $("#hidden_responsable").val());


									$(".tdPedirCodRips").hide();
									$("#codRips").val("").attr("valor", "").hide();

								}
							});
						}
						// --> Activar boton grabar
						boton.html('GRABAR').removeAttr("disabled");

					}, 'json');
				}
			}

			//-----------------------------------------------------------------
			//	--> Funcion que realiza la impresion de un soporte de un cargo
			//-----------------------------------------------------------------
			function imprimirSoporteCargo(idCargo, historia, ingreso, responsable) {
				if (idCargo == "") {
					$('input[ImgImprimirSoporte]:checked').each(function() {
						idCargo += (idCargo == "") ? $(this).val() : "-" + $(this).val();
					});
				}

				if (idCargo == "") {
					jAlert("<span style='color:red'>No hay cargos seleccionados para imprimir</span>", "Mensaje");
					return;
				} else {
					if (responsable == "") {
						// --> Validar que los cargos seleccionados sean de la misma empresa
						empAnt = "";
						respDiferentes = false;

						$('input[ImgImprimirSoporte]:checked').each(function() {
							if (empAnt != "" && $(this).attr("codEmpImp") != undefined && empAnt != $(this).attr("codEmpImp")) {
								respDiferentes = true;
							} else
							if ($(this).attr("codEmpImp") != undefined)
								empAnt = $(this).attr("codEmpImp");
						});

						if (respDiferentes) {
							jAlert("<span style='color:red'>Los cargos a imprimir deben ser de un mismo resposable.</span>", "Mensaje");
							return;
						}
					} else
						empAnt = responsable;
				}

				jConfirm("Desea imprimir el soporte del cargo?", 'Confirmar', function(respuesta) {
					if (respuesta) {
						$.blockUI({
							message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
							css: {
								"border": "2pt solid #7F7F7F"
							}
						});

						$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
							consultaAjax: '',
							wemp_pmla: $('#wemp_pmla').val(),
							accion: 'imprimirSoporteCargo',
							idCargo: $.trim(idCargo),
							historia: $.trim(historia),
							ingreso: $.trim(ingreso),
							responsable: empAnt
						}, function(respuesta) {
							$.unblockUI();
							if (respuesta.Error)
								jAlert("<span style='color:red'>" + respuesta.Mensaje + "</span>", "Mensaje");
							else {
								var contenido = respuesta.Html;

								// --> Abrir modal
								$("#imprimirSoporte").html(contenido).show().dialog({
									dialogClass: 'fixed-dialog',
									modal: true,
									title: "<div align='center' style='font-size:10pt'>Imprimir Soporte</div>",
									width: "auto",
									height: "700",
									buttons: {
										Cerrar: function() {
											$("#imprimirSoporte").html("").hide();
											$(this).dialog("close");
											$(this).dialog("destroy");
											$("#detalleCargo").find('input[ImgImprimirSoporte]').removeAttr('checked');
										}
									}
								});
							}
						}, 'json');
					}
				});
			}
			//-------------------------------------------------------------------------------
			//	--> Funcion que realiza la impresion del sticker de la solicitud del examen
			//-------------------------------------------------------------------------------
			function imprimirSolicitudExamen(idCargo) {
				jConfirm("Desea imprimir el sticker de solicitud del examen?", 'Confirmar', function(respuesta) {
					if (respuesta) {
						$.blockUI({
							message: "<div style='background-color: #111111;color:#ffffff;font-size: 15pt;'><img width='19' heigth='19' src='../../images/medical/ajax-loader3.gif'>&nbsp;&nbsp;Consultando...</div>",
							css: {
								"border": "2pt solid #7F7F7F"
							}
						});

						$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
							consultaAjax: '',
							wemp_pmla: $('#wemp_pmla').val(),
							accion: 'imprimirSolicitudExamen',
							idCargo: $.trim(idCargo)
						}, function(respuesta) {
							$.unblockUI();
							if (respuesta.Error)
								jAlert("<span style='color:red'>" + respuesta.Mensaje + "</span>", "Mensaje");
							else {
								// var contenido	= respuesta.Html;
								// var windowAttr = "location=yes,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,resizable=yes,screenX=1,screenY=1,personalbar=no,scrollbars=no";
								// var ventana = window.open( "", "",  windowAttr );
								// ventana.document.write(contenido);

								// --> Abrir modal
								$("#imprimirSoporte").html(respuesta.Html).show().dialog({
									dialogClass: 'fixed-dialog',
									modal: true,
									title: "<div align='center' style='font-size:10pt'>Imprimir Sticker</div>",
									width: "auto",
									buttons: {
										Cerrar: function() {
											$("#imprimirSoporte").html("").hide();
											$(this).dialog("close");
										}
									}
								});

							}
						}, 'json');
					}
				});
			}

			//---------------------------------------------------------------------------
			//	Actualiza las variables relacionadas al responsable si este ha cambiado
			//---------------------------------------------------------------------------
			function actualizarResposable() {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'obtenerActualResponsable',
					whistoria: $("#whistoria").val(),
					wing: $("#wing").val()
				}, function(data) {
					// --> Si hay cambio de responsable
					if (data.Empcod != $("#hidden_responsable").val()) {
						mostrar_mensaje($("#div_mensajes").text() + "<br><span style='color:red'>El paciente ha cambiado de responsable <b>(" + data.Empnom + "</b>)<br>por superaci&oacute;n de topes.</span>");
						return;
						$("#div_responsable").html(data.Empcod + '-' + data.Empnom);
						$("#div_responsable_tal").val(data.Empcod + '-' + data.Empnom);

						$("#responsable_original").val(data.Empcod);
						$("#responsable_original_tal").val(data.Empcod);

						$("#td_responsable").html(data.Empcod + '-' + data.Empnom);
						$("#hidden_responsable").val(data.Empcod);

						$("#wnomemp").val(data.Empnom);
						$("#wnomemp_tal").val(data.Empnom);

						// --> Tipo de empresa
						$("#tipoEmpresa").val(data.Emptem);
						$("#tipoEmpresa_tal").val(data.Emptem);

						// --> Nit de empresa
						$("#nitEmpresa").val(data.Empnit);
						$("#nitEmpresa_tal").val(data.Empnit);

						$("#div_tarifa").html(data.Tarcod + '-' + data.Tardes);
						$("#div_tarifa_tal").val(data.Tarcod + '-' + data.Tardes);

						$("#tarifa_original").val(data.Tarcod);
						$("#tarifa_original_tal").val(data.Tarcod);

						$("#td_tarifa").html(data.Tarcod + '-' + data.Tardes);
						$("#hidden_tarifa").val(data.Tarcod);

						// -->  Si el nuevo responsable es un particular, se debe pedir confirmacion con que tarifa
						//		se va a seguir facturando si con tarifa particular o con la tarifa del anterior responsable
						/*if(data.pedirCambioTarifa)
						{
							var mensaje =" Se ha realizado cambio de responsable por superacion de topes."+
								mensaje+=" La nueva tarifa del paciente es PARTICULAR, �Desea continuar con tarifa PARTICULAR o con tarifa "+$("#div_tarifa").html()+"?";

							if(confirm(mensaje))
							{
								$("#div_tarifa").html(data.Tarcod+'-'+data.Tardes);
								$("#div_tarifa_tal").val(data.Tarcod+'-'+data.Tardes);

								$("#tarifa_original").val(data.Tarcod);
								$("#tarifa_original_tal").val(data.Tarcod);

								$("#td_tarifa").html(data.Tarcod+'-'+data.Tardes);
								$("#hidden_tarifa").val(data.Tarcod);
							}
						}*/
					}
				}, 'json');
			}
			//---------------------------------------------------------------------------------
			//	Nombre:			limpiar_campos
			//	Descripcion:	Limpia todos los campos del formulario
			//	Entradas:
			//	Salidas:
			//----------------------------------------------------------------------------------
			function limpiar_campos() {
				$('.cargo_cargo').find("input[valor]").each(function() {
					$(this).attr('valor', '');
				});
				$('.cargo_cargo').find("input[value]").each(function() {
					$(this).val('');
				});
				$('.cargo_cargo').find("[id^=div_valor_total]").each(function() {
					$(this).text('');
				});
				$('.cargo_cargo').find("[id^=busc_terceros]").each(function() {
					$(this).hide();
				});
				$('.cargo_cargo').find("[id^=busc_especialidades]").each(function() {
					$(this).hide();
				});
				$('.cargo_cargo').find("select").each(function() {
					$(this).find('option').remove();
					$(this).append('<option value="">Seleccione..</option>');
				});
				$('.cargo_cargo').find("[id^=wcantidad]").each(function() {
					$(this).val('1');
				});
				$('.cargo_cargo').find("[id^=wrecexc]").each(function() {
					$(this).val('R');
				});
				$('.cargo_cargo').find("[id^=wfacturable]").each(function() {
					$(this).val('S');
				});

				$("#aplicarHonorarios").attr("CHECKED", "CHECKED");
				$(".tdConHonorarios").hide(200);
				$("#aplicarHonorarios").attr("manejaDobleTarHon", "NO");

				// --> Actualizar la fecha y la hora desde el servidor
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'horaFechaDelServidor'
				}, function(data) {
					$('#whora_cargo_1').val(data.Hora);
					$('#wfeccar').val(data.Fecha);
				}, 'json');

				$("#tipoCuadroTurno").attr("validar", "no").html("");
				$(".tdCuadroTurno").hide(300);
				$("#inputDescuento").val("0");
				$(".tdPedirLote").hide(300);
				$("#loteVacuna").val("");
				$("#codRips").val("").attr("valor", "");
			}
			//---------------------------------------------------------------------------------
			//	Nombre:			mostrar mensaje
			//	Descripcion:	Pinta un mensaje en el div correspondiente para los mensajes
			//	Entradas:
			//	Salidas:
			//----------------------------------------------------------------------------------
			function mostrar_mensaje(mensaje) {
				$("#div_mensajes").html("<BLINK><img width='15' height='15' src='../../images/medical/root/info.png' /></BLINK>&nbsp;" + mensaje);
				$("#div_mensajes").css({
					"width": "300",
					"opacity": " 0.6",
					"fontSize": "11px"
				});
				$("#div_mensajes").hide();
				$("#div_mensajes").show(500);

				$("#div_mensajes").effect("pulsate", {}, 2000);

				setTimeout(function() {
					$("#div_mensajes").hide(500);
				}, 15000);
			}

			function mostrar_cargos_grabados() {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'mostrar_cargos_grabados',
					whistoria: $("#whistoria").val(),
					wing: $("#wing").val(),
					wcargos_sin_facturar: $("#cargos_sin_facturar").val(),
					wcajadm: $("#wcajadm").val()
				}, function(data) {

					// --> Obtener si el acordeon estaba visible, para dejarlo tal cual como estaba en el caso de que sea una recarga
					if ($("#div_cargos_cargados").is(":visible"))
						var desplegar = -1;
					else
						var desplegar = 1;

					// --> Si ya existe un acordeon activo lo destruyo para volverlo a recargar
					var string = $("#accordionDetCuentaSimple").attr("class");
					if (string.indexOf("ui-accordion") >= 0)
						$("#accordionDetCuentaSimple").accordion("destroy");

					// --> Cargar el acordeon
					$("#div_cargos_cargados").html(data.html);
					$("#accordionDetCuentaSimple").accordion({
						collapsible: true,
						heightStyle: "content",
						active: desplegar
					});

				}, 'json');

			}

			function limpiarPantalla_grabar() {
				$("#busc_concepto_1").val('');
				$("#busc_procedimiento_1").val('');
				$("#busc_terceros_1").val('');
				$("#wvaltar_1").val('');
				$("#wcantidad_1").val('1');
				$("#wporter_1").val('');
				$("#grupoMedico").val('');
				$("#wterunix").val('');
				$("#wexiste_1").val('');
				$("#wtipfac_1").val('');
				$("#wexidev").val('');
				$("#wccogra_1").html("<option selected value=''>Seleccione..</option>");
				$("#warctar").val('');
				$("#wconabo_1").val('');
				$("#wcontip_1").val('');
				$("#wconmva").val('');
				$("#wconinv_1").val('');
				$("#wconser_1").val('');
				$("#busc_especialidades_1").html("<option value=''>Seleccione Medico</option>");
				$("#div_valor_total_1").html('');
				$("#whora_cargo_1").val('');
			}

			function CambiarFoco(e, Elemento) {
				var tecla = (document.all) ? e.keyCode : e.which;
				if (tecla == 13) {
					$('#' + Elemento).focus();
				}
			}

			function anular(wid) {
				if ($("#causaAnulacion").val() == "" || $("#justificacionAnulacion").val() == "") {
					if ($("#causaAnulacion").val() == "")
						jAlert("<span style='color:red'>Debe seleccionar la causa</span>", "Mensaje");
					else
						jAlert("<span style='color:red'>Debe ingresar la justificaci&oacute;n</span>", "Mensaje");
					return;
				}

				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'anular',
					wid: wid,
					causaAnulacion: $("#causaAnulacion").val(),
					justificacionAnulacion: $("#justificacionAnulacion").val()
				}, function(data) {
					mostrar_mensaje(data.Mensaje);
					if (!data.Error) {
						$("#anularCargo").dialog("close");

						jAlert("<span style='color:#2a5db0'>Cargo anulado</span>", "Mensaje");
						//mostrar_cargos_grabados();
						PintarDetalleCuentaResumido($("#whistoria").val(), $("#wing").val());

					} else {
						jAlert("<span style='color:#2a5db0'>" + data.Mensaje + "</span>", "Mensaje");
						//$("#anularCargo").dialog( "close" );
						$("div [class=ui-dialog-buttonset]").find("button").removeAttr("disabled", "disabled");
					}
				}, 'json');
			}

			function abririModalParaAnularCargo(idCargo) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'validarSiCargoEstaFacturado',
					idCargo: idCargo,
				}, function(data) {

					if (!data.Error) {
						if (confirm("Esta seguro que desea anular este cargo?")) {
							$("#causaAnulacion").val("");
							$("#justificacionAnulacion").val("");

							// --> Abrir modal
							$("#anularCargo").show().dialog({
								dialogClass: 'fixed-dialog',
								modal: true,
								title: "<div align='center' style='font-size:10pt'>Anular cargo</div>",
								width: "350px",
								buttons: {
									Anular: function() {
										//$("div [class=ui-dialog-buttonset]").find("button").attr("disabled", "disabled");
										anular(idCargo);
										//$( this ).dialog( "close" );
									}
								}
							});
						}
					} else
						jAlert("<span style='color:#2a5db0'>" + data.Mensaje + "</span>", "Mensaje");

				}, 'json');
			}

			function grabarIVA() {
				$("#modalGrabarIva #vtc").text("0");
				$("#modalGrabarIva #pi").text("0");
				$("#modalGrabarIva #vti").text("0");

				// --> Abrir modal
				$("#modalGrabarIva").show().dialog({
					dialogClass: 'fixed-dialog',
					modal: true,
					title: "<div align='center' style='font-size:10pt'>Grabar Iva</div>",
					width: 'auto',
					buttons: {
						Grabar: function() {
							conceptoIva = $("#selecConceptoIva").val();
							valIva = $("#modalGrabarIva #vti").text();

							if (conceptoIva == "") {
								jAlert("<span style='color:#2a5db0'>Debe seleccionar el concepto</span>", "Mensaje");
								return;
							}

							if (valIva == "0") {
								jAlert("<span style='color:#2a5db0'>EL valor del iva debe ser mayor a 0</span>", "Mensaje");
								return;
							}

							$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
								consultaAjax: '',
								wemp_pmla: $('#wemp_pmla').val(),
								accion: 'grabaIva',
								historia: $("#whistoria").val(),
								ingreso: $("#wing").val(),
								valIva: valIva,
								concepto: conceptoIva,
								nomCon: $("#selecConceptoIva option:selected").attr("nomCon"),
								tipoEmpresa: $("#tipoEmpresa").val(),
								tarifa: $("#hidden_tarifa").val(),
								responsable: $("#hidden_responsable").val(),
								tipoIngreso: $('#wtipo_ingreso').val(),
								tipoPaciente: $('#wtip_paciente').val(),
								wno1: $("#wno1").val(),
								wno2: $("#wno2").val(),
								wap1: $("#wap1").val(),
								wap2: $("#wap2").val(),
								wdoc: $("#wdoc").val()

							}, function(data) {

								if (!data.Error) {
									jAlert("<span style='color:#2a5db0'>Iva grabado correctamente</span>", "Mensaje");
									inactivarGrabarIva(true);
									$("#modalGrabarIva #vtc").text("0");
									$("#modalGrabarIva #pi").text("0");
									$("#modalGrabarIva #vti").text("0");
									PintarDetalleCuentaResumido($("#whistoria").val(), $("#wing").val());
								} else
									jAlert("<span style='color:#2a5db0'>" + data.Mensaje + "</span>", "Mensaje");

							}, 'json');
						},
						Calcular: function() {

							conceptoIva = $("#selecConceptoIva").val();
							valorIvaParaCx = $("#selecConceptoIva option:selected").attr("iva");

							if (conceptoIva == "") {
								jAlert("<span style='color:#2a5db0'>Debe seleccionar el concepto</span>", "Mensaje");
								return;
							}

							$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
								consultaAjax: '',
								wemp_pmla: $('#wemp_pmla').val(),
								accion: 'validarSiGrabaIva',
								historia: $("#whistoria").val(),
								ingreso: $("#wing").val(),
								valorIvaParaCx: valorIvaParaCx,
								conceptoIva: conceptoIva

							}, function(data) {

								if (!data.Error) {
									$("#modalGrabarIva #vtc").text(data.valorTotalCar);
									$("#modalGrabarIva #pi").text(valorIvaParaCx);
									$("#modalGrabarIva #vti").text(data.valorTotalIva);
									inactivarGrabarIva(false);
								} else
									jAlert("<span style='color:#2a5db0'>" + data.Mensaje + "</span>", "Mensaje");

							}, 'json');
						}
					}
				});
				inactivarGrabarIva(true);
			}

			function inactivarGrabarIva(inactivar) {
				$("div [class=ui-dialog-buttonset]").find("button").each(function() {
					if ($(this).text() == "Grabar")
						if (inactivar)
							$(this).hide();
						else
							$(this).show();
				});
			}

			function pedir_datos_banco(dato) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'pedir_datos_banco',
					wdato: $("#wfpa").val()

				}, function(data) {

					$("#div_datos_banco").html(data);

				});

			}

			function grabarabono() {
				var consecutivo = 1;
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'grabarabono',
					whistoria: $("#whistoria").val(),
					wing: $("#wing").val(),
					wno1: $("#wno1").val(),
					wno2: $("#wno2").val(),
					wap1: $("#wap1").val(),
					wap2: $("#wap2").val(),
					wdoc: $("#wdoc").val(),
					wcodemp: $("#hidden_responsable").val(),
					wnomemp: $("#wnomemp").val(),
					wser: $("#wser").val(),
					wcodcon: $("#busc_concepto_" + consecutivo).attr('valor'),
					wnomcon: $("#busc_concepto_" + consecutivo).val(),
					wprocod: $("#busc_procedimiento_" + consecutivo).attr('valor'),
					wpronom: $("#busc_procedimiento_" + consecutivo).val(),
					wcodter: $("#busc_terceros_" + consecutivo).attr('valor'),
					wnomter: $("#busc_terceros_" + consecutivo).val(),
					wfacturable: (($("#wfacturable_S").is(':checked')) ? 'S' : 'N'), //$('[name=wfacturable_'+consecutivo+']:checked').val(),
					wcco: $("#wcco").val(),
					wccogra: $("#wccogra_" + consecutivo).val(),
					wfeccar: $("#wfeccar").val(),
					waprovecha: $("#waprovecha_" + consecutivo).attr("manejaAprovechamiento"),
					wtipfac: $("#wtipfac_" + consecutivo).val(),
					wvaltar: $("#wvalfpa").val(),
					wrecexcfpa: $("#wrecexcfpa").val(),
					wcaja: $("#wcaja").val(),
					wobsrec: $("#wobsrec").val(),
					wvalfpa: $("#wvalfpa").val(),
					obliga: $("#obliga").val(),
					wubica: $("#wubica").val(),
					wautori: $("#wautori").val(),
					wdocane: $("#wdocane").val(),
					wconmca: $("#wconmca").val()
				}, function(data) {


					if (data.error != 1) {
						// --> Cuando termine la grabacion
						setTimeout(function() {
							//mostrar_cargos_grabados();
							mostrar_mensaje("Cargo grabado correctamente.");
							setTimeout(function() {
								$("#div_mensajes").hide(500);
							}, 10000);
							//tipo_de_cargo($('[name=tipo_ingreso]:checked').val());
							limpiar_campos();
						}, 300);

						$.unblockUI();

					} else {
						alert(data.mensaje);
					}

				}, 'json');

			}

			//------------------------------------------------------------------------------------
			//	Esta funcion trae los terceros o varios terceros  segun el cco seleccionado y
			//	que el concepto sea compartido.
			//	Autor: Felipe alvarez
			//------------------------------------------------------------------------------------
			function datos_desde_conceptoxcco() {
				var consecutivo = '1';
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'datos_desde_conceptoxcco',
					wccogra: $("#wccogra_" + consecutivo).val(),
					wcodcon: $("#busc_concepto_" + consecutivo).attr('valor'),
					historia: $.trim($("#whistoria").val()),
					ingreso: $.trim($("#wing").val())

				}, function(data) {

					if (data.respuesta == 'ok') {
						$("#wporter_" + consecutivo).val(data.porcentajes);
						$("#grupoMedico").val(data.grupoMedico);

						if ($("#wcontip_" + consecutivo).val() == 'C' || $('#busc_concepto_1').attr('polManejoTerceros') == 'C') {
							//cargarSelectEspecialidades( ui.item.especialidades, consecutivo );
							$("#busc_terceros_" + consecutivo).val('MULTITERCERO');
							$("#busc_terceros_" + consecutivo).attr('valor', 'MULTITERCERO');
							$("#busc_terceros_" + consecutivo).attr('cedulas', data.opciones);
							// se desabilita el campo para que no se pueda modificar
							$("#busc_terceros_" + consecutivo).attr('disabled', true);
						}
					} else {
						if ($("#wcontip_" + consecutivo).val() == 'C' || $('#busc_concepto_1').attr('polManejoTerceros') == 'C') {
							$("#busc_terceros_" + consecutivo).removeAttr("cedulas");
							$("#busc_terceros_" + consecutivo).attr('disabled', false);

							if ($("#busc_terceros_" + consecutivo).val() == 'MULTITERCERO') {
								$("#busc_terceros_" + consecutivo).attr('valor', '');
								$("#busc_terceros_" + consecutivo).val('');
							}
						}
					}

					// -->  Vuelve y se verifica la tarifa del procedimiento, ya que dependiendo del cco puede que
					//		cambie el valor de la tarifa.
					datos_desde_procedimiento(true);

					// --> 2017-09-28, Si hay insumos por aplicar mostrar mensaje y no dejar grabar
					if (data.tieneInsumosPorAplicar == "on") {
						jAlert("<span style='color:red;font-size:10pt'>No se pueden grabar cargos.<br>Porque el paciente tiene insumos por aplicar.</span>" + data.listaResponInsumosPorAplicar + "", "Mensaje");
						$("#botonGrabar").html('&nbsp;<img width="15" height="15" src="../../images/medical/sgc/Mensaje_alerta.png">&nbsp;GRABAR').attr("disabled", "disabled");
						$("#botonGrabar").attr("title", "<span style=font-weight:normal>No se puede grabar porque el paciente tiene insumos por aplicar</span>" + data.listaResponInsumosPorAplicar + "");
						$("#botonGrabar").tooltip({
							track: true,
							delay: 0,
							showURL: false,
							showBody: ' - ',
							opacity: 0.95,
							left: -50
						});
					} else {
						$("#botonGrabar").html('GRABAR').removeAttr("disabled");
						$("#botonGrabar").tooltip("destroy");
					}

				}, 'json');
			}
			//--------------------------------------------------------------------
			//	Funcion que despliega y oculta el detalle de la cuenta detallada
			//--------------------------------------------------------------------
			function desplegar(elemento, clase, tipo) {
				elemento = jQuery(elemento);
				if (elemento.attr('src') == '../../images/medical/hce/mas.PNG') {
					elemento.attr('src', '../../images/medical/hce/menos.PNG');
					$('.' + clase + '.' + tipo).show();
				} else {
					elemento.attr('src', '../../images/medical/hce/mas.PNG');
					$('.' + clase).hide();
					$('.' + clase + '-imagen').each(function() {
						$(this).attr('src', '../../images/medical/hce/mas.PNG');
					});

					// --> Colocar flecha abajo en los tr que manejen paralelo
					Elemento = $('.' + clase).find("[imgParalelo]");
					Elemento.attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");

					// --> Quitar estilos de los paralelos
					Elemento.parent().next().css({
						'border-left': ''
					});
					Elemento.parent().parent().find('td[class]').css({
						'border-top': ''
					});
					Elemento.parent().parent().find('td[class]:last').css({
						'border-right': ''
					});
				}

				// --> redimencionar acordeon
				$("#accordionDetCuentaResumido").accordion("destroy");
				$("#accordionDetCuentaResumido").accordion({
					collapsible: true,
					heightStyle: "content"
				});
			}

			//----------------------------------------------------------
			//	Funcion que obtine la politica de manejo de terceros
			//----------------------------------------------------------
			function politicaManejoTercero() {
				// --> Envio de variables
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'ObtenerPoliticaManejoTerceros',
					historia: $("#whistoria").val(),
					ingreso: $("#wing").val(),
					CodCon: $("#busc_concepto_1").attr('valor'),
					CodProc: $("#busc_procedimiento_1").attr('valor'),
					CodTipEmp: $("#tipoEmpresa").val(),
					CodTar: $("#hidden_tarifa").val(),
					CodNit: $("#nitEmpresa").val(),
					CodEnt: $("#hidden_responsable").val(),
					CodCco: $("#wccogra_1").val()
				}, function(respuesta) {

					polManejoTerceros = $.trim(respuesta.PolManejoTercero);

					$('#busc_concepto_1').attr('polManejoTerceros', polManejoTerceros);
					$('#busc_concepto_1').attr('codigoPolitica', $.trim(respuesta.codigoPolitica));
					// --> Si esta chekeada la opcion de cobrar los honorarios
					if ($("#aplicarHonorarios").is(":checked")) {
						switch (polManejoTerceros) {
							// --> Si maneja tercero
							case 'C':
							case 'OP': {
								if (!$("#busc_terceros_1").is(":visible"))
									$("#busc_terceros_1").show().focus();

								$('#busc_especialidades_1').show();
								$("#conDisponibilidad").show();
								terceroPorDefecto();
								break;
							}
							// --> No maneja tercero
							case 'P': {
								$("#busc_terceros_1").hide();
								$('#busc_especialidades_1').hide();
								$("#conDisponibilidad").hide();
								$(".tdCuadroTurno").hide();
								$("#wcantidad_1").focus();
								break;
							}
							// --> 	No tiene niguna politica, entonces el tercero depende de si el concepto
							//		es propio o compartido.
							case '': {
								if ($("#wcontip_1").val() == "C" || $("#aplicarHonorarios").attr("manejaDobleTarHon") == "SI") {
									if (!$("#busc_terceros_1").is(":visible"))
										$("#busc_terceros_1").show().focus();
									$('#busc_especialidades_1').show();
									$("#conDisponibilidad").show();
									terceroPorDefecto();
								} else {
									$("#busc_terceros_1").hide();
									$('#busc_especialidades_1').hide();
									$("#conDisponibilidad").hide();
									$(".tdCuadroTurno").hide();
									$("#wcantidad_1").focus();
								}
								break;
							}
						}
					}
					// --> 	No se van a cobrar honorarios en el cargo (Esto se da cuando hay cortes�as especiales
					//		por parte del medico, osea el medico renuncia a los honorarios para favorecer al paciente)
					else {
						$("#busc_terceros_1").hide();
						$('#busc_especialidades_1').hide();
						$("#conDisponibilidad").hide();
						$(".tdCuadroTurno").hide();
						$("#wcantidad_1").focus();
					}
				}, 'json');
			}
			//--------------------------------------------
			//	FUNCION QUE DESPLEGA UN CARGO EN PARALELO
			//--------------------------------------------
			function verParalelo(Elemento, id) {
				if (jQuery(Elemento).attr("src") == "../../images/medical/iconos/gifs/i.p.next[1].gif")
					jQuery(Elemento).attr("src", "../../images/medical/iconos/gifs/i.p.previous[1].gif");
				else
					jQuery(Elemento).attr("src", "../../images/medical/iconos/gifs/i.p.next[1].gif");

				// --> Pintar borde azul
				if ($("#" + id).is(':hidden')) {
					jQuery(Elemento).parent().next().css({
						'border-left': '2px dotted #72A3F3'
					});
					jQuery(Elemento).parent().parent().find('td[class]').css({
						'border-top': '2px dotted #72A3F3'
					});
					jQuery(Elemento).parent().parent().find('td[class]:last').css({
						'border-right': '2px dotted #72A3F3'
					});
				}
				// --> Quitar borde azul
				else {
					jQuery(Elemento).parent().next().css({
						'border-left': ''
					});
					jQuery(Elemento).parent().parent().find('td[class]').css({
						'border-top': ''
					});
					jQuery(Elemento).parent().parent().find('td[class]:last').css({
						'border-right': ''
					});
				}
				// --> Colocarle borde azul a los td del paralelo
				$("#" + id).find('td[class]').css({
					'border-bottom': '2px dotted #72A3F3'
				});

				// --> Ocultar y mostrar paralelo
				$("#" + id).toggle(0);
			}
			//-------------------------------------------------------------------
			//	MOSTRAR VENTANA PARA REGRABAR CARGO
			//-------------------------------------------------------------------
			function ventanaReGrabar(boton) {
				if ($("#ingresoActivoUnix").attr("estado") == "off") {
					jAlert("<span style='color:#2a5db0'>No se pueden regrabar cargos, porque el ingreso del paciente est&aacute; inactivo.</span>", "Mensaje");
					return;
				}

				$("div[class^=ui-dialog]").remove();
				$("#msjRegrabacion").html("");
				$("#botonIP").html('Iniciar Proceso').show();
				$("#respRegrabar").attr("valor", "");
				$("#respRegrabar").attr("nombre", "");
				$("#respRegrabar").val("");
				$("#respRegrabar").removeAttr("disabled");
				$("#td_progressbar").hide();
				$("#progressbar2").text("Procesando...");
				// --> si existe al menos un cargo para regrabar
				if ($('[regrabar=si]:checked').length > 0) {
					// --> Pintar lista de los cargos que se van a regrabar
					var lista;
					lista = "<table>";
					lista += "	<tr class='fondoAmarillo' align='center'><td colspan='4'><b>Lista de cargos a regrabar:</b></td></tr>";
					lista += "	<tr class='encabezadoTabla' align='center'><td>Cargo</td><td>Regrabado</td><td>Grab&oacute;</td><td>Anul&oacute;/Devolvi&oacute;</td><tr>";

					var colorF = 'fila1';
					$('[regrabar=si]:checked').each(function() {
						if (colorF == 'fila1')
							colorF = 'fila2';
						else
							colorF = 'fila1';

						lista += "<tr align='center' class='" + colorF + "'><td>" + $(this).attr('idCargo') + "</td><td id='Reg-" + $(this).attr('idCargo') + "'></td><td style='font-size: 8pt;font-family: verdana;'></td><td style='font-size: 8pt;font-family: verdana;'></td></tr>";
					});

					lista += "</table>";

					$("#listaCargosRegrabar").html(lista);

					// --> Abrir ventana de dialog
					$('#divRegrabar').dialog({
						show: {
							effect: "blind",
							duration: 100
						},
						hide: {
							effect: "blind",
							duration: 100
						},
						width: 650,
						dialogClass: 'fixed-dialog',
						modal: true,
						title: "Reagrabar cargos",
						close: function(event, ui) {
							// --> Quitar alerta de que la historia ya no tiene cargos pendientes por regrabar
							$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
								consultaAjax: '',
								wemp_pmla: $('#wemp_pmla').val(),
								accion: 'quitarAlertaCargosPorRegrabar',
								historia: $("#whistoria").val(),
								ingreso: $("#wing").val()
							}, function(data) {});

							PintarDetalleCuentaResumido($("#whistoria").val(), $("#wing").val());
						}
					});
				} else {
					alert('No ha seleccionado ningun cargo para regrabar');
				}
			}

			//-------------------------------------------------------------------
			//	REGRABAR CARGOS
			//-------------------------------------------------------------------
			function realizarRegrabacion(boton) {
				boton = jQuery(boton);
				if ($("#respRegrabar").val() != '') {
					boton.hide();
					$("#respRegrabar").attr('disabled', 'disabled');
					// --> Cargar barra de progreso
					$("#td_progressbar").html("<div id='progressbar' div align='center' class=''><div align='center' id='progressbar2' class='progress-label'>Procesando...</div></div>");
					progressbar = $("#progressbar"),
						progressLabel = $(".progress-label");
					progressbar.progressbar({
						value: false,
						change: function() {
							var porcentajeCompletado = progressbar.progressbar("value");
							porcentajeCompletado = parseInt(porcentajeCompletado);
							progressLabel.text("Regrabando... " + porcentajeCompletado + " %");
						},
						complete: function() {
							progressLabel.text("Proceso finalizado!");
						}
					});
					$("#td_progressbar").show();
					$("#progressbar2").show();

					// --> Recorrer cada cargo a regrabar
					$('[regrabar=si]:checked').each(function(index) {

						idCargo = $(this).attr('idCargo');
						$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
							consultaAjax: '',
							wemp_pmla: $('#wemp_pmla').val(),
							accion: 'regrabarCargo',
							idCargo: idCargo,
							responsble: $("#respRegrabar").val(),
							tipoIngreso: $('#wtipo_ingreso').val(),
							tipoPaciente: $('#wtip_paciente').val()
						}, function(data) {

							// --> Aumentar barra de progreso
							progress();

							// --> Mostrar mensaje
							$("#Reg-" + data.idCargo).next().html(data.MsjGrabado).next().html(data.MsjAnulado);

							// --> Marcar como regrabado
							if (data.Regrabado)
								$("#Reg-" + data.idCargo).html('<img width="15" height="15" src="../../images/medical/root/grabar.png">');
							else {
								$("#Reg-" + data.idCargo).html('<img width="15" height="15" src="../../images/medical/hce/close.png">');
								//return false;
							}

						}, 'json');


					});
				} else {
					$("#msjRegrabacion").html("<div style='color:red'>Debe seleccionar el responsable.</div>");
				}
			}
			//-------------------------------------------------------------------
			//	EJECUTA LA BARRA DE PROGRESO
			//-------------------------------------------------------------------
			function progress() {
				var val = progressbar.progressbar("value") || 0;
				var cant = 100 / $('[regrabar=si]:checked').length;
				progressbar.progressbar("value", val + cant);
			}

			function checkearTodos(check, atributo) {
				// --> Deschequear todo
				if (jQuery(check).is(":checked"))
					$("#detalleCargo").find('input[' + atributo + ']').attr('checked', 'checked');
				// --> Checkear todo
				else
					$("#detalleCargo").find('input[' + atributo + ']').removeAttr('checked');
			}
			//--------------------------------------------------------------------------------------------------
			// -->  Validar si la cuenta se encuentra congelada, ya que si acurrio un cierre inesperado
			//		del programa  la cuenta puede quedar congelada y no se puede permitir que graben cargos
			//--------------------------------------------------------------------------------------------------
			function validarEstadoDeCuentaCongelada(desdeSelectorConcepto) {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'estadoCuentaCongelada',
					historia: $("#whistoria").val(),
					ingreso: $("#wing").val()
				}, function(info) {
					// --> Si la cuenta se encuentra congelada
					if (info.Ecoest == 'on') {
						// --> si el usuario que la congelo es diferente al actual
						if (info.Ecousu != info.wuse) {
							// --> No se permiten grabar cargos, ya que la cuenta esta congelada por otro usuario
							var mensaje = '<br>' +
								' En este momento no se le pueden grabar cargos al paciente.<br>' +
								' La cuenta se encuentra congelada por <b>' + info.nomUsuario + '</b>' +
								', en un proceso de <b>liquidacion de ' + info.Nomtip + '</b>.';

							// --> Mostrar mensaje
							$('#divMsjCongelar').html(mensaje);
							$('#divMsjCongelar').dialog({
								width: 500,
								dialogClass: 'fixed-dialog',
								modal: true,
								title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
								close: function(event, ui) {
									if (desdeSelectorConcepto) {
										$("#busc_concepto_1").val('');
										$("#busc_concepto_1").attr('valor', '');
										$("#busc_concepto_1").attr("nombre", '');
										$("#busc_concepto_1").attr("polManejoTerceros", '');
										$("#busc_concepto_1").attr("codigoPolitica", '');
									} else
										limpiarPantalla();
								}
							});
						}
						// --> Si es el mismo usuario que la congelo
						else {
							if (!desdeSelectorConcepto) {
								// --> Si el usuario la congelo desde un programa diferente al de cargos
								if (info.Ecotip != 'CA') {
									mensaje = "Usted tiene una liquidaci�n de <b>" + info.Nomtip + "</b> en proceso.<br>Para conservar dicho proceso de Click en <b>Aceptar</b> y luego abra su prgrama correspondiente.<br>Si desea cancelar el proceso y poder grabarle cargos al paciente de Click en <b>Cancelar</b>.";
									$('#divMsjCongelar').html(mensaje);
									$('#divMsjCongelar').dialog({
										width: 680,
										dialogClass: 'fixed-dialog',
										modal: true,
										title: '<img width="15" height="15" src="../../images/medical/root/info.png" /> &nbsp;&nbsp;Mensaje',
										close: function(event, ui) {
											limpiarPantalla();
										},
										buttons: {
											"Aceptar": function() {
												$(this).dialog("close");
											},
											Cancel: function() {
												congelarCuentaPaciente('off');
												$(this).dialog("destroy");
											}
										}
									});
								}
								// --> Si es desde el mismo programa de cargos que estaba congelada, entonces se descongela automaticamente
								else
									congelarCuentaPaciente('off');
							}
						}
					}
					// --> Si no esta congelada se congela
					else {
						if (desdeSelectorConcepto)
							congelarCuentaPaciente('on');
					}
				}, 'json');
			}
			//-------------------------------------------------------------------
			//	Realiza la congelacion de la cuenta del paciente
			//-------------------------------------------------------------------
			function congelarCuentaPaciente(congelar) {
				// --> 2016-02-22, Jerson Trujillo se apaga la opcion de congelar la cuenta.
				return;

				var estadoActual = $("#cuentaCongelada").val();

				if ($("#whistoria").val() != '' && $("#wing").val() != '' && estadoActual != congelar) {
					$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
						consultaAjax: '',
						wemp_pmla: $('#wemp_pmla').val(),
						accion: 'congelarCuentaPaciente',
						historia: $("#whistoria").val(),
						ingreso: $("#wing").val(),
						congelar: congelar
					}, function(data) {
						$("#cuentaCongelada").val(congelar);
					});
				}
			}
			//-------------------------------------------------------------------------
			//	Funcion que dado un concepto, obtiene un tercero por defecto si lo hay
			//-------------------------------------------------------------------------
			function terceroPorDefecto() {
				$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
					consultaAjax: '',
					wemp_pmla: $('#wemp_pmla').val(),
					accion: 'terceroPorDefecto',
					concepto: $("#busc_concepto_1").attr('valor')
				}, function(info) {
					info.codigo = $.trim(info.codigo);
					info.nombre = $.trim(info.nombre);

					if (info.codigo != "" && info.nombre != "") {
						$("#busc_terceros_1").attr("valor", info.codigo);
						$("#busc_terceros_1").attr("nombre", info.nombre);
						$("#busc_terceros_1").val(info.codigo + "-" + info.nombre);
						// --> Obtener el porcentaje de participacion
						cargarSelectEspecialidades(ArrayInfoTerceros[info.codigo].especialidad, 1);
						datos_desde_tercero(on);
						//verificarDisponibilidad();
					} else {
						$("#busc_terceros_1").attr("valor", "");
						$("#busc_terceros_1").attr("nombre", "");
						$("#busc_terceros_1").val("");
					}
				}, 'json');
			}
			//-------------------------------------------------------------------------
			//	Abrir la historia clinica
			//-------------------------------------------------------------------------
			function abrirHce() {
				if ($("#wdoc").val() != '' && $("#wtip_doc").val() != '') {
					var url = "/matrix/HCE/procesos/HCE_Impresion.php?empresa=hce&origen=" + $("#wemp_pmla").val() + "&wcedula=" + $("#wdoc").val() + "&wtipodoc=" + $("#wtip_doc").val() + "&wdbmhos=" + $("#wbasedato_movhos").val() + "&whis=" + $("#whistoria").val() + "&wing=" + $("#wing").val() + "&wservicio=*&protocolos=0&CLASE=I&BC=1";
					alto = screen.availHeight;
					ventana = window.open('', '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
					ventana.document.open();
					ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto, 10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
				}
			}
			//-------------------------------------------------------------------------
			//	Abrir ordenes medicas
			//-------------------------------------------------------------------------
			function abrirOrdenes() {
				if ($("#wdoc").val() != '' && $("#wtip_doc").val() != '') {
					var url = "/matrix/hce/procesos/ordenes_imp.php?wemp_pmla=" + $("#wemp_pmla").val() + "&whistoria=" + $("#whistoria").val() + "&wingreso=" + $("#wing").val() + "&tipoimp=imp&alt=off&wtodos_ordenes=on&orden=asc&origen=on";
					console.log(url);
					alto = screen.availHeight;
					ventana = window.open('', '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
					ventana.document.open();
					ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><iframe name='' src='" + url + "' height='" + (parseInt(alto, 10)) + "' width='100%' scrolling=no frameborder='0'></iframe>");
				}
			}

			function abrirBitacora() {
				whis = $("#whistoria").val();
				wing = $("#wing").val();

				var url = "/matrix/movhos/procesos/rBitacora.php?ok=0&empresa=movhos&codemp=" + $("#wemp_pmla").val() + "&whis=" + whis + "&wnin=" + wing;
				alto = screen.availHeight;
				ventana = window.open('', '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
				ventana.document.open();
				ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + url + "' height='" + (parseInt(alto, 10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
			}

			function abrirSolicitudCamas() {
				var url = "/matrix/cen_camilleros/procesos/Consulta_Central_X_Origen.php?wcentral=CAMAS&wemp_pmla=" + $("#wemp_pmla").val() + "";
				alto = screen.availHeight;
				ventana = window.open('', '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
				ventana.document.open();
				ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + url + "' height='" + (parseInt(alto, 10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
			}

			function abrirHoja() {
				whis = $("#whistoria").val();
				wing = $("#wing").val();

				var url = "/matrix/movhos/reportes/Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=" + $("#wemp_pmla").val() + "&whis=" + whis + "&wing=" + wing + "&wcco=*";
				alto = screen.availHeight;
				ventana = window.open('', '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
				ventana.document.open();
				ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + url + "' height='" + (parseInt(alto, 10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
			}

			function egresar() {
				whis = $("#whistoria").val();
				wing = $("#wing").val();


				destino = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=" + $("#wemp_pmla").val() + "&documento=" + $("#wdoc").val() + "&wtipodoc=" + $("#wtip_doc").val() + "&historia=" + whis + "&ingreso=" + wing + "&ccoEgreso=" + $("#ccoActualPac").val() + "&fechaAltDefinitiva=" + $("#btnEgresar").attr("fechaAlta") + "&horaAltDefinitiva=" + $("#btnEgresar").attr("horaAlta");
				console.log(destino);
				alto = screen.availHeight;
				window.open(destino, '', 'fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
				// ventana.document.open();
				// ventana.document.write("<span><b>CONSULTA DESDE GRABACI&Oacute;N DE CARGOS<b></span><br><input type='button' value='Cerrar Ventana' onclick='window.close();'><br><iframe name='' src='" + destino + "' height='" + (parseInt(alto,10) - 150) + "' width='100%' scrolling=yes frameborder='0'></iframe>");
			}

			function comfirmar_revision(idregistro) {
				var r = confirm("Omitir regrabacion del cargo?");
				if (r == true) {

					$.post("<?= $URL_AUTOLLAMADO ?>?" + url_add_params, {
						consultaAjax: '',
						wemp_pmla: $('#wemp_pmla').val(),
						accion: 'comfirmar_revision',
						idregistro: idregistro,
						historia: $("#whistoria").val(),
						ingreso: $("#wing").val()
					}, function(data) {

						$("#imagen_redistro_" + idregistro).remove();

					});

				} else {

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
			.ui-autocomplete {
				max-width: 300px;
				max-height: 150px;
				overflow-y: auto;
				overflow-x: hidden;
				font-size: 8pt;
			}

			.ui-progressbar {
				position: relative;
			}

			.progress-label {
				position: absolute;
				z-index: 5000 left: 50%;
				top: 4px;
				font-family: verdana;
				font-weight: normal;
				color: green;
				font-size: 11pt;
			}

			#tooltip {
				font-family: verdana;
				font-weight: normal;
				color: #2A5DB0;
				font-size: 8pt;
				position: absolute;
				z-index: 3000;
				border: 1px solid #2A5DB0;
				background-color: #FFFFFF;
				padding: 3px;
				opacity: 1;
				border-radius: 4px;
			}

			#tooltip div {
				margin: 0;
				width: auto;
			}

			/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMA�O  */
			.ui-datepicker {
				font-size: 12px;
			}

			/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
			.ui-datepicker-cover {
				display: none;
				/*sorry for IE5*/
				display
				/**/
				: block;
				/*sorry for IE5*/
				position: absolute;
				/*must have*/
				z-index: -1;
				/*must have*/
				filter: mask();
				/*must have*/
				top: -4px;
				/*must have*/
				left: -4px;
				/*must have*/
				width: 200px;
				/*must have*/
				height: 200px;
				/*must have*/

				.filaDetalle {
					font-size: 8pt;
					font-family': verdana;

				}
		</style>
		<!--=====================================================================================================================================================================
	F I N   E S T I L O S
=====================================================================================================================================================================-->



		<!--=====================================================================================================================================================================
	I N I C I O   B O D Y
=====================================================================================================================================================================-->

		<BODY>
			<div id="actualiza" class="version" style="text-align:right;">Subversi&oacute;n: <?= $wactualiza ?></div>
			<?php
			echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='" . $wemp_pmla . "'>";
			//-->Datos ocultos propios del programa de cargos
			echo "<input type='hidden' id='wno1' name='wno1' >";
			echo "<input type='hidden' id='wno2' name='wno2' >";
			echo "<input type='hidden' id='wap1' name='wap1' >";
			echo "<input type='hidden' id='wap2' name='wap2' >";
			echo "<input type='hidden' id='wdoc' name='wdoc' >";
			echo "<input type='hidden' id='wtip_doc' name='wtip_doc' >";
			echo "<input type='hidden' id='wser' name='wser' >";
			echo "<input type='hidden' id='warctar' name='warctar' >";
			echo "<input type='hidden' id='wconmva' name='wconmva' >";
			echo "<input type='hidden' id='wtip_paciente' name='wtip_paciente' >";
			echo "<input type='hidden' id='wtipo_ingreso' name='wtipo_ingreso' >";
			echo "<input type='hidden' id='wcaja' name='wcaja' >";
			echo "<input type='hidden' id='nomCajero' name='nomCajero' >";
			echo "<input type='hidden' id='wcajadm' name='wcajadm' >";
			echo "<input type='hidden' id='permiteCambiarResponsable' 		name='permiteCambiarResponsable' >";
			echo "<input type='hidden' id='permiteCambiarTarifa' 			name='permiteCambiarTarifa' >";
			echo "<input type='hidden' id='permiteRegrabar' 				name='permiteRegrabar' >";
			echo "<input type='hidden' id='permiteImprimirSoporteCargo'		name='permiteImprimirSoporteCargo' >";
			echo "<input type='hidden' id='permiteImprimirSolicitudExamen'	name='permiteImprimirSolicitudExamen' >";
			echo "<input type='hidden' id='permiteAnularCargo'				name='permiteAnularCargo' >";
			echo "<input type='hidden' id='permiteSeleccionarFacturable' 	name='permiteSeleccionarFacturable' >";
			echo "<input type='hidden' id='permiteSeleccionarRecExc' 		name='permiteSeleccionarRecExc' >";
			echo "<input type='hidden' id='permisoParaHacerDescuento' 		name='permisoParaHacerDescuento' >";
			echo "<input type='hidden' id='permisoParaAplicarRecago' 		name='permisoParaAplicarRecago' >";
			echo "<input type='hidden' id='wnomcco' name='wnomcco' >";
			echo "<input type='hidden' id='wcco' name='wcco' >";
			echo "<input type='hidden' id='cargos_sin_facturar' name='cargos_sin_facturar' >";
			echo "<input type='hidden' id='wdevol' name='wdevol' value='off'>";
			echo "<input type='hidden' id='wcodpaq' name='wcodpaq' value='' >";
			echo "<input type='hidden' id='wconmvto' name='wconmvto' value='' >";
			echo "<input type='hidden' id='wbod' name='wbod' value='off' >";
			echo "<input type='hidden' id='wexidev' name='wexidev' value='' >";
			echo "<input type='hidden' id='num_paquete' name='num_paquete' value='1' >";
			echo "<input type='hidden' id='cuentaCongelada' name='cuentaCongelada' value='' >";
			echo "<input type='hidden' id='ccoActualPac' name='ccoActualPac' value='' >";
			echo "<input type='hidden' id='nomCcoActualPac' name='ccoActualPac' value='' >";

			$wbasedato_movhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			echo "<input type='hidden' id='wbasedato_movhos' value='" . $wbasedato_movhos . "' >";

			$cantidadMaxPermitidaParaGrabarCargosInv = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cantidadMaxPermitidaParaGrabarCargosInv');
			echo "<input type='hidden' id='cantidadMaxPermitidaParaGrabarCargosInv' value='" . $cantidadMaxPermitidaParaGrabarCargosInv . "' >";

			echo "
	<input type='hidden' id='wnomemp' name='wnomemp'>
	<input type='hidden' id='hidden_responsable'>
	<input type='hidden' id='responsable_original'>
	<input type='hidden' id='hidden_tarifa'>
	<input type='hidden' id='tarifa_original'>
	<input type='hidden' id='tipoEmpresa'>
	<input type='hidden' id='nitEmpresa'>
	<input type='hidden' id='empPermiteDescuento'>
	<input type='hidden' id='grabarCargos'>
	";
			cargar_hiddens_para_autocomplete();

			$codMedDisponible 	=	consultarAliasPorAplicacion($conex, $wemp_pmla, 'codParticipacionMedicoDisponible');
			$codMedNoDisponible =	consultarAliasPorAplicacion($conex, $wemp_pmla, 'codParticipacionMedicoNoDisponible');
			$tiposEmpRedondean 	=	consultarAliasPorAplicacion($conex, $wemp_pmla, 'tiposEmpresaQueRedondeanValorCargosFI');
			$tiposEmpRedondean	= 	explode(',', $tiposEmpRedondean);

			echo "<input type='hidden' id='tiposEmpRedondean' value='" . json_encode($tiposEmpRedondean) . "'>";

			echo "
	<table width='98%' align='center'>";
			// --> informacion inicial
			echo '
		<tr>
			<td align="center" width="90%">
				<div id="informacion_inicial" width="80%">
					<table width="90%" style="border: 1px solid #999999;">
						<tr>
							<td colspan="7" align="left" id="linkAbrirHce" style="display:none">
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirHce()";>
									Ver Historia Cl&iacute;nica
								</span>
								<b>&nbsp;|&nbsp;</b>
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirOrdenes()";>
									Ver Ordenes M&eacute;dicas
								</span>
								<b>&nbsp;|&nbsp;</b>
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirBitacora()";>
									Ver Bitacora
								</span>
								<b>&nbsp;|&nbsp;</b>
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirSolicitudCamas()";>
									Ver Solicitud de Camas
								</span>
								<b>&nbsp;|&nbsp;</b>
								<span onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="abrirHoja()";>
									Hoja de medicamentos
								</span>
								<b>&nbsp;|&nbsp;</b>
								<span id="btnEgresar" fechaAlta="" horaAlta="" onmouseover="$(this).css({\'color\': \'#2A5DB0\'})" onmouseout="$(this).css({\'color\': \'#000000\'})" style="font-size:8pt;font-weight: normal;cursor:pointer;" onclick="egresar()";>
									Egresar paciente
								</span>
							</td>
						</tr>
						<tr>
							<td align=center colspan="8" class="encabezadoTabla"><b>D A T O S &nbsp&nbspD E L &nbsp&nbspP A C I E N T E</b></td>
						</tr>
						<tr class="fila1" style="font-weight: bold;">
							<td align="left" width="11%">
								<b>Historia:</b>
							</td>
							<td align="left" width="12%">
								<b>Ingreso Nro:</b>
							</td>
							<td align="left" width="5%">
								<b>Estado</b>
							</td>
							<td align="left" colspan="2" width="22%">
								<b>Paciente:</b>
							</td>
							<td align="left">
								<b>Documento:</b>
							</td>
							<td align="left">
								<b>Fecha Ingreso:</b>
							</td>
							<td align="left">
								<b>Fecha del cargo:</b>
							</td>
						</tr>
						<tr class="fila2">
							<td align="left">
								<input type="text" id="whistoria" size="13"  value="" onchange="cargar_datos(\'whistoria\')" onkeypress="CambiarFoco(event, \'busc_concepto_1\');">
							</td>
							<td align="left">
								<input type="text" id="wing" value="" size="1" onchange="cargar_datos(\'wing\')" >
								<span style="font-size: 8pt;font-family: verdana;display:none;" id="msjIngPost" hayIngresoPosterior="off">
									<img width="15" height="15" src="../../images/medical/sgc/Mensaje_alerta.png">
									Hay ingresos posteriores.
								</span>
							</td>
							<td align="center" colspan="1" id="ingresoActivoUnix" limpiar="si">
							</td>
							<td align="left" colspan="2" id="div_paciente" limpiar="si">
							</td>
							<td align="left" id="div_documento" limpiar="si">
							</td>
							<td align="left" id="wfecing" limpiar="si">
							</td>
							<td align="left" >
								<input type="text" disabled id="wfeccar" name="wfeccar" value="" size="10">
							</td>
						</tr>
						<tr class="fila1" style="font-weight: bold;">
							<td align="left">
								<b>Servicio de Ing:</b>
							</td>
							<td align="left" width="10%">
								<b>Tipo de Ingreso:</b>
							</td>
							<td align="left">
								<b>Ubicaci&oacute;n:</b>
							</td>
							<td align="left">
								<b>Servicio de facturaci&oacute;n:</b>
							</td>
							<td align="center" colspan="4">
								<b>Responsables:</b>
							</td>
						</tr>
						<tr class="fila2">
							<td align="center" id="div_tipo_servicio" limpiar="si" style="background-color:#F4CDCB;font-weight:bold">
							</td>
							<td align="left" id="div_tipo_ingreso" limpiar="si">
							</td>
							<td align="left" id="divCcoActualPac" limpiar="si">
							</td>
							<td align="left" id="div_servicio">
							</td>
							<td align="left" colspan="4" style="font-size:8pt;" >
								<table width="100%" id="tableResponsables" style="background-color: #ffffff;display:none" limpiar="si">
								</table>
								<div id="div_responsable" 	style="display:none"></div>
								<div id="div_tarifa"		style="display:none"></div>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>';
			// --> Fin informacion inicial
			echo "
		<tr><td><br><br></td></tr>";

			$title1 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			Cambiar de responsable&nbsp;
		</span>";
			$title2 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			Cambiar de tarifa&nbsp;
		</span>";
			$title3 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			Aplicarle honorarios al cargo&nbsp;
		</span>";
			$title4 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			Cargo para procedimientos que deben aplicar<br>un % de cobro, seg&uacute;n el n&deg; de v&iacute;as y especialista.&nbsp;
		</span>";
			$title5 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			Solo para medicamentos y materiales.
		</span>";

			$opcGraIva	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'activarOpcionGrabarIvaDesdeCargosERP');
			$title6 = "
		<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
			&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
			<b>Opci�n " . (($opcGraIva == 'on') ? "activa" : "inactiva") . "</b> (Ver parametro activarOpcionGrabarIvaDesdeCargosERP en root_51).
		</span>";

			// --> Ingreso de cargos
			echo "
		<tr align='center'>
			<td align='center' style='98%'>
				<table align='center' id='tabla_ingreso_cargos'>
					<tr>
						<td align=center colspan='17' class='encabezadoTabla'><b>I N G R E S O &nbsp&nbsp&nbsp D E &nbsp&nbsp&nbsp C A R G O S</b></td>
					</tr>
					<tr id='tr_tipo_de_cargo'>
						<td class='fila1' colspan='17'>
							<div id='div_tipo_de_cargo' style='float : right'>
								<b>Cargo</b>
								<input type='radio' defecto='si' 	style='cursor:pointer' name='tipo_ingreso' onclick='tipo_de_cargo(\"cargo\")' value='cargo' tipo='cargo' checked='checked'>
								<b>Devolucion</b>
								<input type='radio' 				style='cursor:pointer' name='tipo_ingreso'  onclick='tipo_de_cargo(\"devolucion\")' value='devolucion' tipo='devolucion' class='tooltip' title='" . $title5 . "'>
								<span id='contenedorTrauma' style='display:none'>
									&nbsp;<b>Trauma</b>
									<input type='radio' style='cursor:pointer' name='tipo_ingreso' onclick='tipo_de_cargo(\"trauma\")' value='trauma' tipo='trauma' class='tooltip' title='" . $title4 . "'>
									<span id='pedirVias' style='display:none'>
										Numero de v&iacute;as:
										<input type='text' id='numVias' value='1' style='width:16px;height:13px;'/>&nbsp;
									</span>
								</span>
								<button id='botonGrabarIva' " . (($opcGraIva == 'on') ? "" : "disabled") . " class='tooltip' title='" . $title6 . "' style='font-family: verdana;font-weight:bold;font-size: 10pt;width:130px;cursor:pointer;display:none' onclick='grabarIVA(this)'>Grabar IVA</button>
							</div>
						</td>
					</tr>
					<tr id='tr_enc_det_concepto' class='encabezadoTabla' style='font-size: 8pt;' align='center'>
						<td>Hora</td>
						<td id='tdVias1' style='display:none;'>V&iacute;a</td>
						<td>Concepto</td>
						<td> Cen.Costos</td>
						<td>Procedimiento o Insumo</td>
						<td class='tdPedirLote'			style='display:none'>Lote</td>
						<td class='tdPedirCodRips'		style='display:none'>C&oacute;digo para RIPS</td>
						<td class='tdConHonorarios'		style='display:none'>Con Honorarios</td>
						<td>Tercero</td>
						<td>Especialidad</td>
						<td class='checkDisponibilidad'>Disponible</td>
						<td class='tdCuadroTurno'		style='display:none'>Cuadro de turno</td>
						<td class='tdAprovechamiento' 	style='display:none'>Aprov.</td>
						<td>Cant.</td>
						<td>Valor Unit</td>
						<td class='inputDescuento' style='display:none'>Descuento</td>
						<td>Valor Total</td>
						<td aplicarRecago='' style='display:none'>Aplicar Recargo</td>
						<td>Reco/Exce</td>
						<td>&nbsp&nbspFacturable&nbsp&nbsp;</td>
						<td>
							Responsable
							<img style='cursor:pointer;display:none' id='ImgCambioRes' onClick='CambiarResponsable();' class='tooltip' title='" . $title1 . "' width='16' height='16' src='../../images/medical/sgc/Refresh-128.png' />
						</td>
						<td>
							Tarifa
							<img style='cursor:pointer;display:none' id='ImgCambioTar' onClick='CambiarTarifa();' class='tooltip' title='" . $title2 . "' width='16' height='16' src='../../images/medical/sgc/Refresh-128.png' />
						</td>
					</tr>
					<tr class='fila2 cargo_cargo' consecutivo='1'>
						<td align='left' >
							<input type='text' id='whora_cargo_1' name='whora_cargo_1' value='" . date("H:i") . "' size='4' >
						</td>
						<td id='tdVias2' style='border: 1px solid #000000;display:none'>
						</td>
						<td align='left' >
							<input type='text' name='busc_concepto_1' id='busc_concepto_1' value='' valor='' nombre='' size='21' polManejoTerceros='' codigoPolitica='' style='font-size: 8pt;'>
						</td>
						<td align='left'>
							<select name='wccogra_1' id='wccogra_1' style='font-size:8pt;width:200px' onchange='datos_desde_conceptoxcco()'></select>
						</td>
						<td align='left'>
							<input type='text' id='busc_procedimiento_1' value='' valor='' nombre='' size='30' style='font-size: 8pt;'>
						</td>
						<td align='center' class='tdPedirLote' style='display:none'>
							<input type='text' id='loteVacuna' placeholder='Digite el # de lote' size='14'>
						</td>
						<td align='center' class='tdPedirCodRips' style='display:none'>
							<input type='text' id='codRips' placeholder='Codigo para rips' size='14' valor='' nombre=''>
						</td>
						<td align='center' class='tdConHonorarios' style='display:none'>
							<input type='checkbox' id='aplicarHonorarios' checked='checked' onChange='datos_desde_procedimiento(true);' class='tooltip' title='" . $title3 . "' manejaDobleTarHon='NO'>
						</td>
						<td align='center' id='tercero_1'>
							<div id='div_tercero_1'>
								<input type='text' name='busc_terceros_1' id='busc_terceros_1' value='' valor='' nombre='' size='30' style='display:none;font-size: 8pt;'>
							</div>
						</td>
						<td align='center'>
							<Select id='busc_especialidades_1' style='display:none;font-size: 9pt;' onChange='datos_desde_procedimiento(false);'>
								<option value='' selected>Seleccione..</option>
							</Select>
						</td>
						<td class='checkDisponibilidad' align='center'>
							<input type='checkbox' id='conDisponibilidad' style='display:none' codMedDisponible='" . $codMedDisponible . "' codMedNoDisponible='" . $codMedNoDisponible . "' onChange='datos_desde_tercero(\"on\");delay_datos_desde_procedimiento(false);' tooltip='si' title='<span style=\"font-weight:normal;font-size:10px\" align=\"center\">&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">&nbsp;&nbsp;Marque esta casilla solo si el medico<br>seleccionado esta con disponibilidad (En turno).</span>'>
						</td>
						<td align='center' class='tdCuadroTurno' style='display:none'>
							<Select id='tipoCuadroTurno' style='font-size: 9pt;' onChange='$(\"#grupoMedico\").val($(this).val()); datos_desde_tercero(\"off\"); datos_desde_procedimiento(false)  ' validar='no'>
							</Select>
						</td>
						<td align='center' class='tdAprovechamiento' style='display:none'>
							<input type='checkbox' id='waprovecha_1' manejaAprovechamiento = 'off'>
						</td>
						<td align='center'>
							<input type='text'  name='wcantidad_1' id='wcantidad_1' size='3' value='1' style='text-align: center' onkeyup='calcularValorTotal()'>
						</td>
						<td align='center'>
							<div id='mensajeTarifa' style='display:none'>
								<img src='../../images/medical/ajax-loader9.gif'><span style='font-size: 7pt;'>Calculando...</span>
							</div>
							<input type='text' name='wvaltar_1' id='wvaltar_1' value='0' size='8' tarifaDigitada='no' codHomologar='' style='text-align: center;font-family:Courier New, Courier, monospace;font-weight:bold;font-size:11pt;' disabled='disabled' onkeyup='calcularValorTotal();' onblur='$(\"#busc_terceros_1\").focus();'>
						</td>
						<td align='center' style='display:none'>
							<input type='text' id='inputDescuento' value='0' size='4' style='text-align: center;font-family:Courier New, Courier, monospace;font-weight:bold;font-size:11pt;' onkeyup='calcularValorTotal();' onblur='valVacio(this)'>%
						</td>
						<td align='center'>
							<div id='div_valor_total_1' style='text-align: center;font-family:Courier New, Courier, monospace;font-weight:bold;font-size:11pt'></div>
						</td>";

			$titRecSI = "<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
										&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
										Indica que, si se debe aplicar recargo, este efectivamente SI ser&aacute; aplicado.
									</span>";
			$titRecNO = "<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
										&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
										Indica que, si se debe aplicar recargo, este NO ser&aacute; aplicado.
									</span>";
			echo "
						<td align='center' aplicarRecago='' style='display:none'>
							Si&nbsp;<input type='radio' id='aplicarRecagSi' name='aplicarRecag' value='on' 	style='cursor:pointer' class='tooltip' title='" . $titRecSI . "' CHECKED='CHECKED'><br>
							No<input type='radio' id='aplicarRecagNo' name='aplicarRecag' value='off' 	style='cursor:pointer' class='tooltip' title='" . $titRecNO . "'>
						</td>
						<td align='center' style='font-weight:bold'>";
			$titleR = "<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
										&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
										Reconocido
									</span>";
			$titleE = "<span style=\"font-family: verdana;font-weight:normal;color: #2A5DB0;font-size: 9pt;\">
										&nbsp;<img width=\"12\" height=\"12\" src=\"../../images/medical/root/info.png\">
										Excedente
									</span>";
			echo "
							R<input type='radio' id='wrecexc_R' name='wrecexc_1' value='R' disabled='disabled' style='cursor:pointer' class='tooltip' title='" . $titleR . "' CHECKED='CHECKED'><br>
							E<input type='radio' id='wrecexc_E' name='wrecexc_1' value='E' disabled='disabled' style='cursor:pointer' class='tooltip' title='" . $titleE . "'>
						</td>
						<td align='center' style='font-weight:bold;'>
							Si<input type='radio' id='wfacturable_S' name='wfacturable_1'  value='S' disabled='disabled' style='cursor:pointer' CHECKED='CHECKED'><br>
							No<input type='radio' id='wfacturable_N' name='wfacturable_1'  value='N' disabled='disabled' style='cursor:pointer'>
							<input type='hidden' id='wporter_1' name='wporter_1' >
							<input type='hidden' id='grupoMedico' name='grupoMedico' >
							<input type='hidden' id='wterunix'  name='wterunix' >
							<input type='hidden' id='wcontip_1' name='wcontip_1' >
							<input type='hidden' id='wconinv_1' name='wconinv_1' >
							<input type='hidden' id='wconabo_1' name='wconabo_1' >
							<input type='hidden' id='wexiste_1' name='wexiste_1' >
							<input type='hidden' id='wconser_1' name='wconser_1' >
							<input type='hidden' id='wtipfac_1' name='wtipfac_1' >
						</td>
						<td align='left'>
							<div id='td_responsable' style='display:inline;font-size: 7pt' limpiar='si'></div>
						</td>
						<td align='left'>
							<div id='td_tarifa' style='display:inline;font-size: 7pt' limpiar='si'></div>
						</td>
					</tr>
					<tr>
						<td colspan='15' align='right' width='100%'>
							<br>
							<div id='div_mensajes' class='fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'>
							</div>
						</td>
					<tr>
						<td colspan='15' align='center' width='100%'>
							<button id='botonGrabar' style='font-family: verdana;font-weight:bold;font-size: 10pt;width:150px;cursor:pointer' onclick='grabar(this)'>GRABAR CARGO</button>
						</td>
					</tr>
				</table><br>
			</td>
		</tr>
		<tr align='center'>
			<td>
				<div width='95%' id='accordionDetCuentaSimple' style='display:none'>
					<h3>DETALLE DE LA CUENTA</h3>
					<div id='div_cargos_cargados' style='display:none'>
					</div>
				</div>
			</td>
		</tr>
		<tr align='center'>
			<td>
				<div width='95%' id='accordionDetCuentaResumido' style='display:none' class=''>
					<h3>DETALLE DE LA CUENTA RESUMIDO</h3>
					<div id='div_cargos_cargados_resumido' style='display:none'>
					</div>
				</div>
			</td>
		</tr>
	</table>";

			// div oculta para los conceptos que sean de abono
			echo "<div id='div_abono' class='fila2' align='middle' style='display:none;width:100%;cursor:default;' ></div>";


			// --> Consultar causas de anulacion
			$causasAnulacion 	= array();
			$sqlCauAnu 			= "
	SELECT Caucod, Caudes
	  FROM " . $wbasedato . "_000268
	 WHERE Cauest = 'on'
	";
			$resCauAnu = mysql_query($sqlCauAnu, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlCauAnu):</b><br>" . mysql_error());
			while ($rowCauAnu = mysql_fetch_array($resCauAnu))
				$causasAnulacion[$rowCauAnu['Caucod']] = $rowCauAnu['Caudes'];

			// --> Div oculto para anular un cargo
			echo "
	<div id='anularCargo' style='display:none;font-family: verdana;font-size: 9pt;' align='center'>
		<br>
		<b>Causa:</b>
		<select id='causaAnulacion' style='border-radius: 4px;border:1px solid #AFAFAF;'>
			<option value=''>Seleccione</option>";
			foreach ($causasAnulacion as $codCausa => $desCausa)
				echo "<option value='" . $codCausa . "'>" . $desCausa . "</option>";
			echo "
		<select>
		<br>
		<br>
		<b>Justificaci&oacuten:</b>
		<br>
		<textarea id='justificacionAnulacion' style='width:270px;height:78px;border-radius: 4px;border:1px solid #AFAFAF;'></textarea>
		<br><br>
	</div>";

			// --> Cargar conceptos de IVA
			$arrConIva 	= array();
			$sqlConIva 	= "
	SELECT Grucod, Grudes, Gruiva
	  FROM " . $wbasedato . "_000200
	 WHERE Gruest = 'on'
	   AND Gruiva > 0
	";
			$resConIva = mysql_query($sqlConIva, $conex) or die("Error: " . mysql_errno() . " - en el query: " . $sqlConIva . " - " . mysql_error());
			while ($rowConIva = mysql_fetch_array($resConIva)) {
				$arrConIva[$rowConIva['Grucod']]['nom'] = utf8_encode(str_replace($caracter_ma, $caracter_ok, $rowConIva['Grudes']));
				$arrConIva[$rowConIva['Grucod']]['iva'] = $rowConIva['Gruiva'];
			}

			//Se cierra conexi�n de la base de datos :)
			//Se comenta cierre de conexion para la version estable de matrix :)
			//mysql_close($conex);

			// --> Div oculto para grabar iva
			echo "
	<div id='modalGrabarIva' style='display:none;font-family: verdana;font-size: 9pt;' align='center'>
		<br>
		<br>
		<table>
			<tr class='fila1'>
				<td>Concepto</td>
			</tr>
			<tr class='fila2'>
				<td>
					<select id='selecConceptoIva' onChange='inactivarGrabarIva(true)'>
						<option value=''>Seleccione...</option>";
			foreach ($arrConIva as $codCon => $infoCon)
				echo "<option value='" . $codCon . "' iva='" . $infoCon['iva'] . "' nomCon='" . $infoCon['nom'] . "'>" . $codCon . "-" . $infoCon['nom'] . "</option>";
			echo "</select>
				</td>
			</tr>
		</table>
		<br><br>
		<table>
			<tr class='encabezadoTabla'><td colspan='3' align='center'>Calculo del iva</td></tr>
			<tr class='fila1'><td>Valor total cargos cx</td><td>% Iva</td><td>Valor Iva</td></tr>
			<tr class='fila2' align='right'><td id='vtc'>0</td><td id='pi'>0</td><td id='vti' style='font-weight:bold'>0</td></tr>
		</table>
	</div>";

			// --> Div oculto para imprimir soporte de cargo
			echo "
	<div id='imprimirSoporte' style='display:none;align='center'>
	</div>
	";

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
} //Fin de session
?>