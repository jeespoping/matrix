<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	rep_tarjetaDietas.php
 * Fecha		:	2013-05-08
 * Por			:	Frederick Aguirre Sanchez
 * Descripcion	:	Permite visualizar e imprimir las tarjetas de dietas.
 * Condiciones  :   El patron NVO no se imprime.
					Solo tienen observaciones los patrones con productos (campo Dieind en movhos77)
					El patron SI Sólo imprime ficho en los servicios principales
 *********************************************************************************************************
 Actualizaciones:
 **********************************************************************************************************/
 //2019-10-03: Camilo Zapata: Se adicionan las intolerancias al sticker.
 // 2019-06-26, Jerson Trujillo: Se modifica el tamaño del texto de la observacion
 //Enero 21 de 2014 Jonatan Lopez
 //Se agrega la observacion de DSN o de enfermeria en la tarjeta de dietas, si el paciente tiene observacion DSN y observacion
 //de enfermeria imprimirá la observacion de DSN, ya que esta tiene prelacion.
 /**********************************************************************************************************/

$wactualiz = "2019-10-03";

if(!isset($_SESSION['user'])){
	echo "Reingresar a matrix";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

//La siguiente condicion se hace porque si existe el parametro action quiere decir que viene una solicitud ajax y no debe
//enviar como respuesta el texto "<html><head><title... " etc.
if(! isset($_REQUEST['action'] )){
	echo "<html>";
	echo "<head>";
	echo "<title>IMPRESION TARJETAS DE DIETAS</title>";
	echo '<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />';
	echo '<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>';
	echo ' <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />';
	echo '<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>';
	echo '<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>';
}

//********************INCLUDES, BD, VARIABLES GLOBALES, SESSION****************************//


include_once("root/comun.php");
include_once("movhos/movhos.inc.php");




$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
$pdf = "";


//**************************ENCARGADO DE RECIBIR LOS LLAMADOS CON AJAX********************************************//
if( isset($_REQUEST['action'] )){
	$action = $_REQUEST['action'];
	if( $action == "mostrarLista"){
		consultarTarjetas( $_REQUEST['servicio'], $_REQUEST['patron'], $_REQUEST['fecha'], $_REQUEST['piso'], $_REQUEST['habitacion'], $_REQUEST['historia']);
	}
	return;
}
//FIN*LLAMADOS*AJAX******************************************************************

	//Retorna el codigo html con el diseño de la tarjeta y su contenido
	function getTarjeta( $wdatos_tarjeta ){
		/*
		$wdatos_tarjeta arreglo que contiene:
		-imagen		(String)
		-color   	(String)
		-patron		(String)
		-habitacion (String)
		-edad		(String)
		-nombre		(String)
		-servicio	(String)
		-fecha		(String)
		-observaciones (String)
		-patron_dsn (String)
		-tipopos (String)
		-productos	(Arreglo asociativo, con claves: producto y cantidad)
		*/

		$wtarjeta = "<div style='width:50%; height:252px; overflow:hidden;'>";

		$wtarjeta .="<div style='display: table; height:100%; width:100%;'>"; //para centrar vertical
		$wtarjeta .="<div style=' display: table-cell; vertical-align: middle;'>";//para centrar vertical

		$wtarjeta.="<table align='center'>";
		$wtarjeta.="<tr>";
		$wtarjeta.="<td colspan=2 width='35%' align='left'>";
		$wtarjeta.="<img src='../../images/medical/root/".$wdatos_tarjeta['imagen']."'>";
		$wtarjeta.="</td>";
		$wtarjeta.="<td width='65%' align='left'>";
		$style_color = "";
		if( $wdatos_tarjeta['color'] != '' && $wdatos_tarjeta['color'] != 'NO APLICA' ){
			$style_color = "background-color: #".$wdatos_tarjeta['color'].";";
		}

		$tamano_letra = "patronc";
		if( strlen($wdatos_tarjeta['patron']) > 21 ){
				$tamano_letra = "patroncp";
		}
		$wtarjeta.="<div class='".$tamano_letra."' align='center' style='border: 1px solid gray; ".$style_color."'>";
		$wtarjeta.="<b>".$wdatos_tarjeta['patron']."</b>";

		$wdatos_tarjeta['patron_dsn'] = trim($wdatos_tarjeta['patron_dsn']);

		if( empty( $wdatos_tarjeta['patron_dsn'] ) == false ){
			$tamano_letra = "patrondsn1";
			if( strlen($wdatos_tarjeta['patron_dsn']) <= 26 ){
				$tamano_letra = "patrondsn2";
			}
			$wtarjeta.="<div class='".$tamano_letra."' align='center'>";
			$wtarjeta.= htmlentities($wdatos_tarjeta['patron_dsn']);
			$wtarjeta.="</div>";
		}
		$wtarjeta.="</div>";

		$wtarjeta.="</td>";
		$wtarjeta.="</tr>";

		$wtarjeta.="<tr>";
		$wtarjeta.="<td align='left' width='25%'>";
		$wtarjeta.="<b>Habitacion:</b>";
		$wtarjeta.="</td>";
		$wtarjeta.="<td align='left' width='45%'>";
		$wtarjeta.=$wdatos_tarjeta['habitacion'];
		if( isset( $wdatos_tarjeta['tipopos'] ) )
			$wtarjeta.= "<font class='tipopos' align='right'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>(P)</b></span>";

		$wtarjeta.="</td>";

		$wtarjeta.="<td align='left' width='30%'>";
		$wtarjeta.="<b>Fecha:</b>&nbsp;&nbsp;&nbsp;&nbsp;";
		$wtarjeta.=$wdatos_tarjeta['fecha']."";
		$wtarjeta.="</td>";

		$wtarjeta.="</tr>";

		$wtarjeta.="<tr>";
		$wtarjeta.="<td align='left' width='25%'>";
		$wtarjeta.="<b>Edad:</b>";
		$wtarjeta.="</td>";
		$wtarjeta.="<td align='left' colspan=2 width='75%'>";
		$wtarjeta.=$wdatos_tarjeta['edad'];
		$wtarjeta.="</td>";
		$wtarjeta.="</tr>";

		$wtarjeta.="<tr>";
		$wtarjeta.="<td align='left' width='25%'>";
		$wtarjeta.="<b>Nombre:</b>";
		$wtarjeta.="</td>";
		$wtarjeta.="<td align='left' colspan=2 width='75%'>";
		$wtarjeta.=htmlentities($wdatos_tarjeta['nombre']);
		$wtarjeta.="</td>";
		$wtarjeta.="</tr>";

		$wtarjeta.="<tr>";
		$wtarjeta.="<td align='left' width='25%'>";
		$wtarjeta.="<b>Servicio:</b>";
		$wtarjeta.="</td>";
		$wtarjeta.="<td align='left' colspan=2 width='75%'>";
		$wtarjeta.=$wdatos_tarjeta['servicio'];
		$wtarjeta.="</td>";
		$wtarjeta.="</tr>";

		if( isset($wdatos_tarjeta['productos'])){

			$cantidad_tarjetas = count($wdatos_tarjeta['productos']);
			$mitad_tarjetas = $cantidad_tarjetas / 2;

			$wtarjeta.="<tr>";
			$wtarjeta.="<td align='left' width='25%'>";
			$wtarjeta.="<b>Productos:</b>";
			$wtarjeta.="</td>";

			//IMPRIMIR LA MITAD DE LOS PRODUCTOS EN UNA COLUMNA
			$wtarjeta.="<td align='left' width='37%'>";
			foreach( $wdatos_tarjeta['productos'] as $ind=>$producto ){
				if( $ind < $mitad_tarjetas )
					$wtarjeta.= $producto['cantidad']." - ".htmlentities( $producto['producto'] )."<br>";
			}
			$wtarjeta.="</td>";

			//IMPRIMIR LA OTRA MITAD DE LOS PRODUCTOS EN OTRA COLUMNA
			$wtarjeta.="<td align='left' width='37%'>";
			foreach( $wdatos_tarjeta['productos'] as $ind=>$producto ){
				if( $ind >= $mitad_tarjetas )
					$wtarjeta.= $producto['cantidad']." - ".htmlentities($producto['producto'])."<br>";
			}
			$wtarjeta.="</td>";
			$wtarjeta.="</tr>";
		}

		//Si el paciente tiene observaciones en DSN para el servicio, las mostrara, sino muestra las de enfermeria. 20 de Enero de 2014.
		if( trim(isset($wdatos_tarjeta['observaciones']))){
			$wtarjeta.="<tr>";
			$wtarjeta.="<td align='left' width='100%' colspan=3 class='textObs'>";
			$wtarjeta.="<b style='font-size: 8pt;'>Observ. DSN:&nbsp;</b>".htmlentities($wdatos_tarjeta['observaciones']);
			$wtarjeta.="</td></tr>";
		}else{
		if(trim(isset($wdatos_tarjeta['observaciones_enfer'])) != ''){
				$wtarjeta.="<tr>";
				$wtarjeta.="<td align='left' width='100%' colspan=3 class='textObs'>";
				$wtarjeta.="<b style='font-size: 8pt;'>Observ. Enfer:</b>&nbsp;".htmlentities($wdatos_tarjeta['observaciones_enfer']);
				$wtarjeta.="</td></tr>";
				}
		if(trim(isset($wdatos_tarjeta['observaciones_intol'])) != ''){//2019-10-03
				$wtarjeta.="<tr>";
				$wtarjeta.="<td align='left' width='100%' colspan=3 class='textObs'>";
				$wtarjeta.="<b style='font-size: 8pt;'>Intolerancias:</b>&nbsp;".htmlentities($wdatos_tarjeta['observaciones_intol']);
				$wtarjeta.="</td></tr>";
			}
		}

		$wtarjeta.="</table>";

		$wtarjeta.="</div>";//para centrar vertical
		$wtarjeta.="</div>";
		$wtarjeta.="</div>";

		return $wtarjeta;
	}

	//Consulta e imprime las tarjetas con los parametros de busqueda
	function consultarTarjetas($wser, $wpat, $wfecha, $wcco, $whab, $whis){

		global $wbasedato;
		global $conex;
		global $wemp_pmla;

		$codigos_patrones = array(); //Contiene los codigos de los patrones
		$nombres_patrones = array(); //Contiene los nombres de los patrones

		//Buscar todos los patrones que por lo generan tienen productos
		$q = "SELECT diecod as patron "
		   ."	FROM ".$wbasedato."_000041 "
		   ."  WHERE Dieind = 'on' ";
		$resp = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$patrones_con_productos = array();
		while($rowp = mysql_fetch_assoc($resp)){
			array_push( $patrones_con_productos, $rowp['patron'] );
		}

		$wcco=trim($wcco);
		if( $wcco == '%' )
			$wcco = "";
		$and ="";
		if( $wcco != "")
			$and = "	  AND movcco='".$wcco."' ";

		$wpat=trim($wpat);
		if( $wpat == '%' )
			$wpat = "";
		$andpat ="";
		if( $wpat != "")
			$andpat = "	  AND movdie = '".$wpat."' ";

		$whab=trim($whab);
		$andhab ="";
		if( $whab != "")
			$andhab = "	  AND movhab = '".$whab."' ";

		$whis=trim($whis);
		$andhis ="";
		if( $whis != "")
			$andhis = "	  AND movhis = '".$whis."' ";



		$q= " SELECT Movhis as historia, Moving as ingreso, Movcco as piso, Diecod as cod_patron, Diedes as patron, Diecol as color, Movhab as habitacion, Sercod as cod_servicio, Sernom as servicio, movods, movdsn, "
		    ." CONCAT(pacno1, ' ', pacno2,' ',pacap1,' ',pacap2) as nombre, pacnac, seraso as servicio_asociado, Dieind as tiene_productos, Movobs as observ_enferme, movint intolerancias "
			." FROM ".$wbasedato."_000077, ".$wbasedato."_000041, ".$wbasedato."_000011, root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000076 "
			." WHERE movfec = '".$wfecha."'"
			.$and
			." AND movser = '".$wser."' "
			.$andpat
			.$andhab
			.$andhis
			." AND movdie = diecod "
			." AND movcco = Ccocod"
			." AND movhis = orihis"
			." AND moving = oriing"
			." AND oriori = '".$wemp_pmla."'"
			." AND oriced = pacced "
			." AND oritid = pactid"
			." AND movhis = ubihis "
			." AND moving = ubiing "
			." AND movest = 'on'"
			." AND movser = sercod "
			." AND movdie != 'NVO' "; //Regla de negocio, no se muestra ni se imprime NVO
		$q.= " UNION ";
		$q.= " SELECT Movhis as historia, Moving as ingreso, Movcco as piso, movdie as cod_patron, movdie as patron, '' as color, Movhab as habitacion, Sercod as cod_servicio, Sernom as servicio, movods, movdsn, "
		    ." CONCAT(pacno1, ' ', pacno2,' ',pacap1,' ',pacap2) as nombre, pacnac, seraso as servicio_asociado, 'off' as tiene_productos, Movobs as observ_enferme, movint intolerancias"
			." FROM ".$wbasedato."_000077, ".$wbasedato."_000011, root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000076 "
			." WHERE movfec = '".$wfecha."'"
			.$and
			." AND movser = '".$wser."' "
			.$andhab
			.$andhis
			." AND movdie REGEXP ',' " //Patrones combinados
			." AND movcco = Ccocod"
			." AND movhis = orihis"
			." AND moving = oriing"
			." AND oriori = '".$wemp_pmla."'"
			." AND oriced = pacced "
			." AND oritid = pactid"
			." AND movhis = ubihis "
			." AND moving = ubiing "
			." AND movest = 'on'"
			." AND movser = sercod "
			." ORDER BY piso, habitacion";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		$tarjetas_por_pagina = 8;
		$indice_tarjeta = 1;
		$num_tarjeta = 1;
		$existe_al_menos_uno = false;

		if ($num > 0){
			crearPDF();
			agregarPaginaPDF();

			while($row = mysql_fetch_assoc($res)){
				//Solo los patrones principales tienen servicio asociado
				if( ($row['servicio_asociado'] == '' || $row['servicio_asociado'] == 'NO APLICA' || $row['servicio_asociado'] == '.') && $row['patron'] == 'SI' ){
					continue;	//Regla de negocio, las tarjetas de SI solo se imprimen en los servicios principales
				}
				$datos = array();
				$seguir = true;
				$pos = strrpos($row['cod_patron'] , ",");

				//TIENE PATRONES COMBINADOS
				if ($pos != ''){
					$pats = explode(",", $row['cod_patron'] );
					$row['patron'] = '';

					//Si seleccionaron un patron, en la combinacion debe existir
					if( $wpat != ""){
						if( in_array( $wpat, $pats ) == false) {
							$seguir = false;
						}
					}
					//NVO no se muestra nunca, regla de negocio
					if( in_array( 'NVO', $pats ) == true) {
							$seguir = false;
					}
					//Regla de negocio, las tarjetas de SI solo se imprimen en los servicios principales
					if( ($row['servicio_asociado'] == '' || $row['servicio_asociado'] == 'NO APLICA' || $row['servicio_asociado'] == '.') && ( in_array( 'SI', $pats ) == true) ){
						$seguir = false;
					}

					if( $seguir == true ){
						foreach( $pats as $pos=>$patron ){
							if( in_array( $patron, $patrones_con_productos )) { //Uno de los patrones de la combinacion es de productos
								$lista_productos = consultarProductos($wfecha, $row['historia'], $row['ingreso'], $row['cod_servicio'], $patron, $row['piso'] );
								$datos['productos'] = $lista_productos;
							}
							//PARA CONCATENAR EL NOMBRE DE LOS PATRONES
							if( in_array( $patron, $codigos_patrones )) {
								$indic = array_search($patron, $codigos_patrones);
								$row['patron'].=  $nombres_patrones[ $indic ].", ";
							}else{
								$nombre_pat = nombre_patron( $patron );
								array_push($codigos_patrones, $patron );
								array_push($nombres_patrones, $nombre_pat );
								$row['patron'].=  $nombre_pat.", ";
							}
						}
						$row['patron'] = trim( $row['patron'] );
						$aux = substr($row['patron'], -1);  //Si el ultimo caracter es una coma se elimina
						if( $aux == "," ){
							$row['patron'] = substr($row['patron'], 0, -1);
						}
						//El color del patron principal
						$row['color'] = getColorPatronPrincipal( $pats );
					}
				}else{
					if( in_array( $row['cod_patron'], $codigos_patrones ) == false ){
						array_push($codigos_patrones, $row['cod_patron'] );
						array_push($nombres_patrones, $row['patron'] );
					}
				}

				if( $seguir == false ){
					continue;
				}

				$existe_al_menos_uno = true;
				$wedad = traerEdad($row['pacnac']);

				$datos['imagen'] = "clinica.JPG";
				$datos['color'] = $row['color'];
				$datos['fecha'] = $wfecha;
				$datos['habitacion'] = $row['habitacion'];
				$datos['nombre'] = $row['nombre'];
				$datos['servicio'] = $row['servicio'];
				$datos['patron'] = $row['patron'];
				$datos['patron_dsn'] = strtoupper($row['movdsn']);
				if( pacienteEsPOS($row['historia'], $row['ingreso'], $wser ) ){
					$datos['tipopos'] = 'on';
				}
				$datos['edad'] = $wedad;
				if( $row['movods'] != '' ){
					$datos['observaciones'] = $row['movods'];
					$datos['observaciones_intol'] = $row['intolerancias'];
				}
				if( $row['observ_enferme'] != '' ){
					$datos['observaciones_enfer'] = $row['observ_enferme'];
					if(  trim($row['intolerancias']) != '' )
						$datos['observaciones_intol'] = $row['intolerancias'];
				}
				if( $row['tiene_productos'] == 'on' ){
					$lista_productos = consultarProductos($wfecha, $row['historia'], $row['ingreso'], $row['cod_servicio'], $row['cod_patron'], $row['piso'] );
					if(  count( $lista_productos ) > 0 )
						$datos['productos'] = $lista_productos;
				}
				$tarjeta = getTarjeta($datos);

				agregarTarjetaPDF( $tarjeta, $num_tarjeta );

				$num_tarjeta++;

				if( ($indice_tarjeta % $tarjetas_por_pagina) == 0 ){
					agregarPaginaPDF();
					$num_tarjeta = 1;
				}
				$indice_tarjeta++;
			}

			if( $existe_al_menos_uno == false ){
				echo "No hay registros";
				return;
			}

			imprimirPDF();

			echo "OK";
		}else{
			echo "No hay registros";
		}
	}

	//Retorna el color del patron principal de la combinacion
	function getColorPatronPrincipal($wpats){
		global $wbasedato;
		global $conex;

		$color = "";

		for( $i=0; $i<count($wpats); $i++){
				$sec = "";
				$pat = trim($wpats[ $i ]); //Elijo solo el patron de la posicion $i

				//Creo la secuencia con los patrones faltantes
				foreach( $wpats as $pos=>$patr){
					if( $pos != $i)
						$sec.= $patr.",";
				}
				$aux = substr($sec, -1);  //Si el ultimo caracter es , se quita
				if( $aux == "," ){
					$sec = substr($sec, 0, -1);
				}

				//Busco resultados donde patpri = $pat y las secuencias contengan los otros patrones
				$q = " SELECT patsec as secuencia, diecol as color "
					."   FROM ".$wbasedato."_000128, ".$wbasedato."_000041 "
					."  WHERE  patpri = '".$pat."' "
					."    AND  patsec REGEXP '".$sec."' "
					."    AND patppa = diecod ";

				$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$num = mysql_num_rows($res);

				$resultados = array();
				while( $row = mysql_fetch_assoc($res) ){

					$pats1 = explode(",", trim($row['secuencia'] ) ); //Creo un arreglo con la secuencia consultada
					$pats2 = explode(",", trim($sec) );	//Creo un arreglo con la secuencia creada
					sort($pats2); sort($pats1); 	//Ordeno ambos arreglos

					//Si ambos arreglos son iguales ya encontre el registro que tiene la combinacion
					if( $pats1 == $pats2 ){
						//ENCONTRADA LA COMBINACION
						$color = $row['color'];
						return $color;
					}
				}
		}
		return $color;
	}

	//Funcion que retorna un array con los productos designados a un paciente en una fecha-servicio-patron-piso
	function consultarProductos($wfecha, $whis, $wing, $wser, $wpatron, $wcco ){

		global $wbasedato;
		global $conex;

		//Busco si esta opcion esta grabada para el paciente en la tabla 000084
		$q = " SELECT Prodes as producto, Detcan as cantidad "
			."   FROM ".$wbasedato."_000084, ".$wbasedato."_000082 "
			."  WHERE  detfec = '".$wfecha."'"
			."    AND dethis = '".$whis."'"
			."    AND deting = '".$wing."'"
			."    AND detser = '".$wser."'"
			."    AND detpat = '".$wpatron."'"
			."    AND detcco = '".$wcco."'"
			."    AND detpro = procod "
			."    AND detest = 'on' "
			."ORDER BY detser ASC";

		$respro = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num_pro = mysql_num_rows($respro);

		$resultados = array();
		while( $rowser = mysql_fetch_assoc($respro) ){
			$dato = array();
			$dato['producto'] = $rowser['producto'];
			$dato['cantidad'] = $rowser['cantidad'];
			array_push( $resultados, $dato);
		}
		return $resultados;
	}

	//Se realiza la configuracion para el archivo pdf
	function crearPDF(){
		global $pdf;

		require_once('root/tcpdf/config/lang/spa.php');
		require_once('root/tcpdf/tcpdf.php');

		$nombre_logo = "clinica";
		$datos_paciente = "datos";

		define("PDF_HEADER_LOGO_MX", "medical/root/".$nombre_logo.".jpg"); // Imagen del logo.
		define("PDF_HEADER_LOGO_WIDTH_MX",3); // tamaño ancho en mm de la imagen del logo.
		define("PDF_HEADER_TITLE_MX",$datos_paciente); // Título 1 en encabezado.
		define("PDF_HEADER_STRING_MX","Laboratorio Tel: 3421010 ext. 1132"); // Texto 2 en encabezado.

		define("PDF_MARGIN_TOP_MX",0);
		define("PDF_MARGIN_LEFT_MX",0);
		define("PDF_MARGIN_RIGHT_MX",0);
		define("PDF_MARGIN_BOTTOM_MX",0);

		// create new PDF document
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('PMLA');
		$pdf->SetTitle('Resultado');
		$pdf->SetSubject('Tarjeta Dietas');
		$pdf->SetKeywords('PMLA, PDF, resultado, clínica, dietas');


		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO_MX, PDF_HEADER_LOGO_WIDTH_MX, PDF_HEADER_TITLE_MX, PDF_HEADER_STRING_MX);

		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT_MX, PDF_MARGIN_TOP_MX, PDF_MARGIN_RIGHT_MX);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM_MX);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		//set some language-dependent strings
		$pdf->setLanguageArray($l);

		// set font
		$pdf->SetFont('dejavusans', '', 8);
	}

	//Retorna si un paciente-servicio es tipo POS
	function pacienteEsPOS( $whis, $wing, $wservicio='01' ){

		global $conex;
        global $wbasedato;
		global $wemp_pmla;

		$q = " SELECT * "
			."   FROM ".$wbasedato."_000076, ".$wbasedato."_000016 "
			."  WHERE Inghis = '".$whis."' "
			."	  AND Inging = '".$wing."' "
			."    AND Ingtip != '' "
			."    AND Sertpo LIKE CONCAT('%', Ingtip , '%') "
			."    AND Sercod = '".$wservicio."'"
			."    AND Serest = 'on' ";

		$res = mysql_query($q, $conex);
		$num = mysql_num_rows($res);
		if ($num > 0 )
			return true;

		return false;
	}

	//FUNCION QUE ME RETORNA EL NOMBRE DE UN PATRON
	function nombre_patron($valor_patr){
        global $wbasedato;
        global $conex;

        $query_nom_pat="SELECT Diedes
						  FROM ".$wbasedato."_000041
						 WHERE Diecod='".$valor_patr."'
						";
        $resnp = mysql_query($query_nom_pat, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_nom_pat." - ".mysql_error());
        $rownp=mysql_fetch_array($resnp);
        return $rownp[0];
    }

	//Funcion que agrega una nueva pagina al PDF
	function agregarPaginaPDF(){
		global $pdf;

		$pdf->SetAutoPageBreak(false, 0);
		// add a page
		$pdf->AddPage();

		$pdf->lastPage();
	}

	//Funcion que determina la posicion donde debe ir ubicada la tarjeta
	function agregarTarjetaPDF( $contenido_pdf, $numero_tarjeta=1 ){
		global $pdf;

		$contenido_css = "<style>
							table {
								font-family: helvetica;
								font-size: 8pt;
							}
							.patronc{
								font-size: 13pt;
							}
							.patroncp{
								font-size: 9pt;
							}
							.patrondsn1{
								font-size: 5pt;
							}
							.patrondsn2{
								font-size: 7pt;
							}
							.tipopos{
								font-size: 10pt;
							}
							.textObs{
								font-size: 10pt;
							}
						</style>";
		$contenido_pdf = $contenido_css." ".$contenido_pdf;
		$contenido_pdf = str_replace("'",'"',$contenido_pdf);
		$html = str_replace("\\", "", $contenido_pdf);

		$y = $pdf->getY();
		$x = 0;

		//Explicacion: Las medidas de una hoja tamaño carta son 21.59 x 27.94
		//Cada celda para la tarjeta mide 10x6.984
		//Si son dos tarjetas por fila se ocupa un width de 20, queda un espacio 1.59 que se divide entre 4 (0.3975) para dejar de "margen" izq y der para cada tarjeta. Asi se ocupa todo el width
		//Si son 4 tarjetas por columna se ocupa un height de 6.984x4=27.936, quedan 0.004 despreciables. Se ocupa todo el heigth
		if( $numero_tarjeta == 1 ){
			$x = 0.3975;
			$y = 0;
		}else if( $numero_tarjeta == 2 ){
			$x = 11.1925;
			$y = 0;
		}else if( $numero_tarjeta == 3 ){
			$x = 0.3975;
			$y = 6.985;
		}else if( $numero_tarjeta == 4 ){
			$x = 11.1925;
			$y = 6.985;
		}else if( $numero_tarjeta == 5 ){
			$x = 0.3975;
			$y = 13.97;
		}else if( $numero_tarjeta == 6 ){
			$x = 11.1925;
			$y = 13.97;
		}else if( $numero_tarjeta == 7 ){
			$x = 0.3975;
			$y = 20.955;
		}else if( $numero_tarjeta == 8 ){
			$x = 11.1925;
			$y = 20.955;
		}

		$pdf->StartTransform();
		$pdf->Rect($x, $y, 10, 6.8, 'CNZ'); //Aunque la celda mide 10x6.984, solo se muestra el contenido hasta 6.8 por si la tarjeta desborda el tamaño de la celda
		$pdf->writeHTMLCell(10, 6.984, $x, $y, $html); //Se imprime una celda de 10x6.984
		$pdf->StopTransform();
	}

	//Se crea el PDF en la carpeta resultados
	function imprimirPDF(){
		global $pdf;

		$dir = 'resultados';

		if(is_dir($dir)){ }
		else { mkdir($dir,0777); }

		$sufijo_orden = "prueba";
		$archivo_dir = $dir."/resultado_".$sufijo_orden.".pdf";
		if(file_exists($archivo_dir)){
			unlink($archivo_dir);
		}

		$pdf->Output($archivo_dir, 'F');
	}

	//Consultar la edad en año-mes de un paciente
	function traerEdad( $wnac ){

		$mensaje_edad = "";
		if( empty( $wnac ) == false){
			$row_fecha_nacimiento = explode('-', $wnac);
			$fecha_nacimiento = $row_fecha_nacimiento[2].'/'.$row_fecha_nacimiento[1].'/'.$row_fecha_nacimiento[0];
			$wedad = tiempo_transcurrido($fecha_nacimiento, date('d/m/Y'));
			$mensaje_edad = "";
			if( $wedad[0] > 1 )
				$mensaje_edad.= $wedad[0]." A&ntilde;os ";
			else if( $wedad[0] == 1 )
				$mensaje_edad.= $wedad[0]." A&ntilde;o ";

			if( $wedad[1] > 1 )
				$mensaje_edad.= $wedad[1]." Meses";
			else if( $wedad[1] == 1 )
				$mensaje_edad.= $wedad[1]." Mes";
			//$wedad = htmlentities ( $mensaje_edad );
		}
		return $mensaje_edad;
	}

	//Para calcular la edad
	function tiempo_transcurrido($fecha_nacimiento, $fecha_control)
	{
		$fecha_actual = $fecha_control;

		if(!strlen($fecha_actual)){
			$fecha_actual = date('d/m/Y');
		}
		// separamos en partes las fechas
		$array_nacimiento = explode ( "/", $fecha_nacimiento );
	    $array_actual = explode ( "/", $fecha_actual );

	    $anos =  $array_actual[2] - $array_nacimiento[2]; // calculamos años
	    $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses
	    $dias =  $array_actual[0] - $array_nacimiento[0]; // calculamos días

	    //ajuste de posible negativo en $días
	    if ($dias < 0){
		    --$meses;
		    //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual
		    switch ($array_actual[1])
			{
				case 1:
					$dias_mes_anterior=31;
					break;
				case 2:
					$dias_mes_anterior=31;
					break;
				case 3:
					if (bisiesto($array_actual[2])){
						$dias_mes_anterior=29;
						break;
					}
					else{
						$dias_mes_anterior=28;
						break;
					}
				case 4:
					$dias_mes_anterior=31;
					break;
				case 5:
					$dias_mes_anterior=30;
					break;
				case 6:
					$dias_mes_anterior=31;
					break;
				case 7:
					$dias_mes_anterior=30;
					break;
				case 8:
					$dias_mes_anterior=31;
					break;
				case 9:
					$dias_mes_anterior=31;
					break;
				case 10:
					$dias_mes_anterior=30;
					break;
				case 11:
					$dias_mes_anterior=31;
					break;
				case 12:
					$dias_mes_anterior=30;
				break;
			}

		$dias=$dias + $dias_mes_anterior;

			if ($dias < 0){
				--$meses;
				if($dias == -1)
				{
					$dias = 30;
				}
				if($dias == -2)
				{
					$dias = 29;
				}
			}
		}
		//ajuste de posible negativo en $meses
		if ($meses < 0){
			--$anos;
			$meses=$meses + 12;
		}
		$tiempo[0] = $anos;
		$tiempo[1] = $meses;
		$tiempo[2] = $dias;

		return $tiempo;
	}

	//Retorna si el año es bisiesto o no
	function bisiesto($anio_actual){
		$bisiesto=false;
		//probamos si el mes de febrero del año actual tiene 29 días
		if (checkdate(2,29,$anio_actual)){
			$bisiesto=true;
		}
		return $bisiesto;
	}

	//Funcion que imprime el formulario cuando se carga la pagina
	function vistaInicial(){

		global $wemp_pmla;
		global $wactualiz;
		global $wtabcco;
		global $wbasedato;
		global $conex;

		encabezado("IMPRESION TARJETAS DE DIETAS", $wactualiz, "clinica");
		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";

		echo "<form name='formtarjeta' action='' method=post>";
		echo "<input type='hidden' id ='wemp_pmla' name ='wemp_pmla' value='".$wemp_pmla."'/>";

		echo "<center>";
		echo "<table>";
		//Traigo los centros de costos
		$q = "SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom
				FROM ".$wtabcco.", ".$wbasedato."_000011
			   WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod
				 AND ccohos  = 'on' ";

		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		//=================================
		// SELECCIONAR EL SERVICIO
		//=================================
		//Consultar los servicios del maestro
		$q = " SELECT sernom, serhin, serhfi, sercod
		FROM ".$wbasedato."_000076
		WHERE serest = 'on' ";
		$resser = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numser = mysql_num_rows($resser);

		$q = "SELECT Diecod as codigo, Diedes as nombre "
			."	FROM ".$wbasedato."_000041 "
			 ."  WHERE Dieest = 'on' "
			 ."    AND Diecod != 'NVO'" //Regla de negocio, no se muestra ni se imprime NVO
			 ." ORDER BY diedes ";
		$respat = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numpat = mysql_num_rows($respat);

		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Fecha </b></td>";
		echo "<td align=center class='fila1'>";

		echo "<input type='text' id='wfec_i' name='wfec_i' value='".date("Y-m-d")."' />";

		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Centro de costos</b></td>";
		echo "<td align=center class='fila1'>";

		echo "<SELECT name='wcco' id='wcco' style='".$width_sel." margin:5px;'>";
		echo "<option value='%'>% - TODOS</option>";
		for ($i=1;$i<=$num;$i++){
			$row = mysql_fetch_array($res);
			echo "<OPTION value='".$row[0]."'>".$row[0]." - ".$row[1]."</OPTION>";
		}
		echo "</SELECT>";

		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Servicio</b></td>";
		echo "<td align=center class='fila1'>";

		echo "<SELECT name='wser' id='wser' style='".$width_sel." margin:5px;'>";
		for ($i=1;$i<=$numser;$i++){
			$rowser = mysql_fetch_array($resser);
			echo "<OPTION value=".$rowser[3].">".$rowser[0]."</OPTION>";
		}
		echo "</SELECT>";

		echo "</td>";
		echo "</tr>";


		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Patron</b></td>";
		echo "<td align=center class='fila1'>";

		echo "<SELECT name='wpat' id='wpat' style='".$width_sel." margin:5px;' >";
		echo "<option value='%'>% - TODOS</option>";
		for ($i=1;$i<=$numpat;$i++){
			$rowpat = mysql_fetch_array($respat);
			echo "<OPTION value='".$rowpat[0]."'>".$rowpat[1]."</OPTION>";
		}
		echo "</SELECT>";

		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Habitacion </b></td>";
		echo "<td align=center class='fila1'>";

		echo "<input type='text' id='whab' name='whab' value='' />";

		echo "</td>";
		echo "</tr>";

		echo "<tr>";
		echo "<td align=center class='encabezadotabla'><b>Historia</b></td>";
		echo "<td align=center class='fila1'>";

		echo "<input type='text' id='whis' name='whis' value='' />";

		echo "</td>";
		echo "</tr>";

		echo "</table>";
		echo "<input type='button' id='btn_consultar' onclick='consutarLista()' value='Consultar'>";
		echo "<br><br>";

		echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."' />";
		echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."' /	>";

		echo "<br>";
		echo "<a id='enlace_retornar' href='#' >RETORNAR</a>";

		echo "</center>";

		echo "</form>";


		echo '<center>';

		echo "<div id='div_contenedor_pdf'></div>";

		echo '</center>';

		echo '<center>';

		echo "<br><br>";

		echo "<input type=button id='cerrar_ventana' value='Cerrar Ventana' onClick='javascript:cerrarVentana()' />";
		echo "<br><br>";
		echo "<br><br>";
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/ajax-loader5.gif'/>";
		echo "<br><br> Por favor espere un momento ... <br><br>";
		echo '</div>';
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo '</center>';
	}
?>
<style>
/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
.ui-datepicker {font-size:12px;}
/* IE6 IFRAME FIX (taken from datepicker 1.5.3 */
.ui-datepicker-cover {
    display: none; /*sorry for IE5*/
    display/**/: block; /*sorry for IE5*/
    position: absolute; /*must have*/
    z-index: -1; /*must have*/
    filter: mask(); /*must have*/
    top: -4px; /*must have*/
    left: -4px; /*must have*/
    width: 200px; /*must have*/
    height: 200px; /*must have*/
}
</style>
<script>
	$.datepicker.regional['esp'] = {
		closeText: 'Cerrar',
		prevText: 'Antes',
		nextText: 'Despues',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		dayNamesMin: ['D','L','M','M','J','V','S'],
		weekHeader: 'Sem.',
		dateFormat: 'yy-mm-dd',
		yearSuffix: ''
	};
	$.datepicker.setDefaults($.datepicker.regional['esp']);

</script>
<script>

//************cuando la pagina este lista...**********//
	$(document).ready(function() {
		//agregar eventos a campos de la pagina
		$("#enlace_retornar").hide();
		$("#enlace_retornar").click(function() {
			restablecer_pagina();
		});
		$("#wfec_i").datepicker({
			  showOn: "button",
			  buttonImage: "../../images/medical/root/calendar.gif",
			  buttonImageOnly: true,
			  maxDate:"+1D"
		});


		$("#whis").keyup(function(){
			if ($(this).val() !="")
				$(this).val($(this).val().replace(/[^0-9]/g, ""));
		});
	});

	//Funcion que se activa cuando se presiona el enlace "retornar"
	function restablecer_pagina(){
		//Si esta visible la tabla de menu...
		$("#wcco option").eq(0).attr('selected',true);
		$("#wser option").eq(0).attr('selected',true);
		$("#wpat option").eq(0).attr('selected',true);
		$("#div_contenedor_pdf").hide( 'drop', {}, 500 ); //esconder lista
		$("#enlace_retornar").hide(); //esconder enlace retornar
	}

	//Al presionar el boton consultar se hace el llamado para crear las tarjetas con los parametros elegidos
	function consutarLista(){

		var wemp_pmla = $("#wemp_pmla").val();
		var servicio = $("#wser").val();
		var patron = $("#wpat").val();
		var fecha = $("#wfec_i").val();
		var piso = $("#wcco").val();
		var habitacion = $("#whab").val();
		var historia = $("#whis").val();

		habitacion = habitacion.toUpperCase();
		//muestra el mensaje de cargando
		$.blockUI({ message: $('#msjEspere') });
		$("#enlace_retornar").fadeIn('slow');

		//se crea una variable aleatoria para el llamado ajax evitando que se almacene una respuesta fija en cache
		var rango_superior = 245;
		var rango_inferior = 11;
		var aleatorio = Math.floor(Math.random()*(rango_superior-(rango_inferior-1))) + rango_inferior;
		//Realiza el llamado ajax con los parametros de busqueda
		$.get('rep_tarjetaDietas.php', { wemp_pmla: wemp_pmla, action: "mostrarLista", habitacion: habitacion, historia: historia, servicio: servicio, patron: patron, fecha: fecha, piso: piso, consultaAjax: aleatorio} ,
			function(data) {
				$.unblockUI();
				if( data.trim() == 'OK' ){
					var object= '<br>'
								+'<object type="application/pdf" data="resultados/resultado_prueba.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" width="900" height="700">'
									+'<param name="src" value="resultados/resultado_prueba.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" />'
									+'<p style="text-align:center; width: 60%;">'
										+'Adobe Reader no se encuentra o la versión no es compatible, utiliza el icono para ir a la página de descarga <br />'
										+'<a href="http://get.adobe.com/es/reader/" onclick="this.target=\'_blank\'">'
											+'<img src="../../images/medical/root/prohibido.gif" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" />'
										+'</a>'
									+'</p>'
								+'</object>';
					$("#div_contenedor_pdf").html(object);
				}else{
					$("#div_contenedor_pdf").html(data);
				}
			});
	}

	function isJson(value) {
		try {
			eval('(' + value + ')');
			return true;
		} catch (ex) {
			return false;
		}
	}

	function alerta( txt ){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
							$.unblockUI();
						}, 1600 );
	}
</script>
</head>
    <body>
		<!-- LO QUE SE MUESTRA AL INGRESAR POR PRIMERA VEZ -->
			<?php
					vistaInicial();
			?>
    </body>
</html>