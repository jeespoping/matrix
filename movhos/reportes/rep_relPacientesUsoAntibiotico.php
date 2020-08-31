<head>
<title>INSUMOS FACTURADOS Y DEVUELTOS POR CENTRO DE COSTOS</title>

<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

</head>

<style>
<!--
.divStyle {
  max-height: 100px;
  overflow: auto;
  height: expression(this.scrollHeight > 101? "100px": "auto");
}
-->
</style>


<script type="text/javascript">

	function ltrim(s) {
	   return s.replace(/^\s+/, "");
	}
	
	function rtrim(s) {
	   return s.replace(/\s+$/, "");
	}


	function trim(s) {
	   //return rtrim(ltrim(s));
	   return s.replace(/^\s+|\s+$/g,"");
	}

	function addCCO( cmOrigen, cmDestino ){

		if( cmDestino.value.indexOf( "% - Todos" ) > -1  ){
			return;
		}
		
		valor = cmOrigen.options[ cmOrigen.options.selectedIndex ].text;

		if( valor != "% - Todos" ){
		
			pos = cmDestino.value.indexOf( valor );
		
			if( pos == -1 ){

				if( cmDestino.value.length > 0 ){
					cmDestino.value = cmDestino.value+"\r";
				}
			
				cmDestino.value = trim( cmDestino.value+valor );

				if( cmDestino.name == "txGrupos" ){
					gruposChange(0);
				}
			}
		}
		else{
			cmDestino.value = valor;
			if( cmDestino.name == "txGrupos" ){
				gruposChange(0);
			}
		}
	}

	function removeCCO( cmOrigen, cmDestino ){

		pos = cmDestino.value.indexOf( trim( cmOrigen.options[ cmOrigen.options.selectedIndex ].text) );

		if( pos > -1 )
		{
			valor = cmDestino.value.substring( 0, pos-1 );
			valor = trim(valor)+cmDestino.value.substring( pos+cmOrigen.options[ cmOrigen.options.selectedIndex ].text.length, cmDestino.value.length );

			cmDestino.value=valor;

			if( cmDestino.value.indexOf("\n") == 0 ){
				cmDestino.value = cmDestino.value.substring( 1, cmDestino.value.length );
			}else if( cmDestino.value.indexOf("\n") == 1 ){
				cmDestino.value = cmDestino.value.substring( 2, cmDestino.value.length );
			}
		}

		if( cmDestino.name == "txGrupos" ){
			gruposChange(0);
		}
	}

	function gruposChange( valor ){
		document.mainmenu.Menu.value = valor;
		document.mainmenu.submit();
	}
	
	function cerrarVentana()
	{
    	top.close();		  
    }

    function tablaRowsPan( fila, expansion ){
        var tabla = document.getElementById('tbInformacion');

        tabla.rows[ fila ].cells[0].rowSpan = expansion;
        tabla.rows[ fila ].cells[1].rowSpan = expansion;
        tabla.rows[ fila ].cells[2].rowSpan = expansion;
        tabla.rows[ fila ].cells[3].rowSpan = expansion;
        tabla.rows[ fila ].cells[4].rowSpan = expansion;
        tabla.rows[ fila ].cells[5].rowSpan = expansion;
        tabla.rows[ fila ].cells[9].rowSpan = expansion;
		
        tabla.rows[ fila ].cells[10].rowSpan = expansion;
    }

    //verfica que un option exista con el texto exista
    function existeOption( campo, texto ){

        for( var i = 0; i < campo.options.length; i++){
            if( campo.options[ i ].text == texto ){
                return true;
            }
        }

        return false;       
    }
	  

	//agregar un option a un campo select
	//opciones debe ser un array
    function agregarOption( slCampos, opciones ){

    	if( slCampos.tagName.toLowerCase() == "select" ){
	    	//agrengando options
			for( var i = 0; i < opciones.length; i++ ){
				var auxOpt = document.createElement( "option" );
				slCampos.options.add( auxOpt, 0 );
				auxOpt.innerHTML = opciones[i];
			}
    	}
    }

	//campoDestino id del campo destino
	//campoOrigen id del campo origen
    function agregarOptionASelect( cmpOrigen, campoDestino, textoDestino ){

		var cmpDestino = document.getElementById( campoDestino );
		
        if( !existeOption( cmpDestino, cmpOrigen.options[cmpOrigen.selectedIndex].text ) ){

			if( cmpOrigen.options[ cmpOrigen.selectedIndex ].text == "% - Todos" ){
				
				var numOptions = cmpDestino.options.length;
				for( var i = 0; i <  numOptions; i++ ){
					cmpDestino.removeChild( cmpDestino.options[0] );
				}
        	}

        	if(  cmpDestino.options[ 0 ] && cmpDestino.options[ 0 ].text == "% - Todos" ){
            	return;
        	}

			agregarOption( cmpDestino, Array( cmpOrigen.options[cmpOrigen.selectedIndex].text ) );
        	addCCO( cmpOrigen, document.getElementById( textoDestino ) );
        }
    }

    function removerOption( campo, txCampo ){
		
    	removeCCO( campo, document.getElementById( txCampo ) );
    	campo.removeChild( campo.options[ campo.selectedIndex ] );
	}

    //Agrega todos las opciones de un select a otro
	function agregarTodo( slOrigen, slDestino, textoDestino ){

    	for( var i = 1; i < slOrigen.options.length; i++ ){
    		slOrigen.selectedIndex = i;
			agregarOptionASelect( slOrigen, slDestino, textoDestino );
    	}
	}

	function eliminarTodo( slOrigen, textoDestino ){

		var numOptions = slOrigen.options.length
		for( var i = 0; i < numOptions; i++ ){
			slOrigen.selectedIndex = 0;
			removerOption( slOrigen, textoDestino );
		}
	}

    //Para cargar las apciones previamente elegidos
	window.onload = function(){

		var auxTxOrigen = document.getElementById( "txOrigen" );
		
		if( auxTxOrigen ){
			agregarOption( document.getElementById( "slCcoOrigen" ), auxTxOrigen.value.split("\n").reverse() );
		}

		var auxTxDestino = document.getElementById( "txDestino" );
		
		if( auxTxDestino ){
			agregarOption( document.getElementById( "slCcoDestino" ), auxTxDestino.value.split("\n").reverse() );
		}

		var auxTxGrupos = document.getElementById( "txGrupos" );
		
		if( auxTxGrupos ){
			agregarOption( document.getElementById( "slGrupos" ), auxTxGrupos.value.split("\n").reverse() );
		}

		var auxTxArticulo = document.getElementById( "txArticulo" );
		
		if( auxTxArticulo ){
			agregarOption( document.getElementById( "slArticulo" ), auxTxArticulo.value.split("\n") )
		}

		try{
			document.getElementById( "ccoori" ).selectedIndex = -1;
			document.getElementById( "ccodes" ).selectedIndex = -1;
			document.getElementById( "grupos" ).selectedIndex = -1;
			document.getElementById( "articulo" ).selectedIndex = -1;
		}catch(e){}
	}
</script>

<body>

<?php
include_once("conex.php");
/***************************************************************************
 *
 * Creado por: Edwin Molina Grisales
 * Programa: rep_relPacientesUsoAntibiotico.php
 * Fecha: 12-10-2009
 * Objetivo:  
 *
 **************************************************************************/

/***************************************************************************
 *
 * Tablas usadas para el reporte (todos de tipo consulta)
 * 
 * 000002	Encabezado cargos
 * 000003	Detalle cargos
 * 000011	Servicios o centros de costos
 * 000026	Para hallar las unidades de medida de los productos
 * 000002	De cenpro, para hallar las unidades de medida de los productos
 * 
 * 
 * Variables principales del programa
 *
 * $ccoori:					Centro de costos origen
 * $ccodes:					Centro de costos destino
 * $fechaini				Fecha Inicial
 * $fechafin				Fecha Final
 *
 *
 * Actualizaciones:
 * ===================================================================================
 * Diciembre 11 de 2015 Jessica Madrid
 * Se agrega el la columna Empresa responsable (Codigo y nombre de la empresa), 
 * además se adiciona el buscador quicksearch.
  * ===================================================================================
 * Noviembre 12 de 2013: Jonatan Lopez
 * Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 
 * para que traiga los datos de contingencia (tabla movhos_00143) con estado activo.
 * ===================================================================================
 * Julio 11 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones 
 * consultaCentrosCostos que hace la consulta de los centros de costos 
 * de un grupo seleccionado y dibujarSelect que dibuja el select con los 
 * centros de costos obtenidos de la primera funcion. 
 ***************************************************************************/

?>

<?php

/****************************************************************************
 * FUNCIONES
 ****************************************************************************/

function ccoCMZ(){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	$sql = "SELECT
				ccocod
			FROM
				{$wbasedato}_000011
			WHERE
				ccotra = 'on'
				AND ccoima = 'on'
				AND ccoest = 'on'
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		$val = $rows[0];
	}
	
	return $val;
}

/********************************************************************************
 * Calcula la edad de la persona de acuerdo a la fecha de nacimiento
 * 
 * @param date $fnac		Fecha de nacimientos				
 * @return entero			Edad de la persona
 ********************************************************************************/
function calculoEdad( $fnac ){
	
	$edad = 0;
	
	$nac = explode( "-", $fnac );				//fecha de nacimiento
	$fact = date( "Y-m-d" );					//fecha actual

	if( count($nac) == 3 ){
		$edad = date("Y") - $nac[0];
		
		if( date("Y-m-d") < date( "Y-".$nac[1]."-".$nac[2] ) ){
			$edad--;
		}
	}
		
	return $edad;
}

function consultarMedicosTratantes( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = [];
	
	//Buscando la fecha y hora de inicio del medicamento
	$sql = "SELECT
				medno1, medno2, medap1, medap2, medesp
			FROM
				{$wbasedato}_000047 a,
				{$wbasedato}_000048 b
			WHERE
				methis = '$his'
				AND meting = '$ing'
				AND mettdo = medtdo
				AND metdoc = meddoc
				AND SUBSTRING_INDEX( medesp, '-', 1 ) = metesp
			"; //echo ".......<pre> $sql</pre>";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	for( $i = 0;$rows = mysql_fetch_array( $res ); $i++ ){
		$val[0] = $rows;
	}
	
	return $val;
}

/**
 * Devuelve la informacion básica de un paciente de acuerdo a la historia y al ingreso, estas son
 * 
 * Historia
 * Nombre 1
 * Nombre 2
 * Apellido 1
 * Apellido 2
 * Tipo de documento
 * Numero de documento
 * 
 * @return unknown_type
 */

function informacionPaciente( $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = '';
	
	//Buscando la fecha y hora de inicio del medicamento
	$sql = "SELECT
				ubihis,
				ubiing,
				pactid,
				pacced,
				pacno1,
				pacno2,
				pacap1,
				pacap2,
				pacnac,
				ubihac,
				ubiald,
				ubifad,
				ubimue,
				b.fecha_data as fecing,
				ingres,
				ingnre
			FROM
				{$wbasedato}_000018 a,
				{$wbasedato}_000016 b,
				root_000036 c,
				root_000037 d
			WHERE
				ubihis = '$his'
				AND ubiing = '$ing'
				AND inghis = ubihis
				AND inging = ubiing
				AND orihis = ubihis
				AND oriced = pacced
				AND oritid = pactid
				AND oriori = '01'
			"; //echo "......<pre>".$sql."</pre>";
			// echo ".....<pre>".$sql."</pre>";	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows;
	}
	
	return $val;
}

/**
 * Crea una clausal IN (x) para la consulta principal, en caso de que $valores
 * sea vacio crea un LIKE '%'
 * 
 * @param String $valores	Lista para crear un IN
 * @return String $in		Clausula IN o LIKE
 */
function crearIN( $valores ){
	
	if( empty( $valores ) ){
		$in = "LIKE '%'";
		return $in;
	}
	else{
	
		$in = "IN (";
		$i = 0;

		$ccocodnam = explode( "\r", $valores );
		
		foreach( $ccocodnam as $val ){

			if( !empty($val) ){
				
				if( $val == "% - Todos" ){
					$in = "LIKE '%'";
					return $in;
				}

				$exp = explode(" - ", $val );

				if( $i > 0 ){
					$in .= ",";
				}
				$in .= "'".trim($exp[0])."'";

				$i++;
			}

		}
		$in .= ")";

		return $in;
	}
	
}

/********************************************************************************
 * Consulta la fecha de incio de un medicamento
 * 
 * @param $his
 * @param $ing
 * @param $art
 * @return unknown_type
 ********************************************************************************/
function consultarFechaInicioMedicamento( $his, $ing, $art, $cmz = 'off' ){
	
	global $conex;
	global $wbasedato;
	global $cargos;
	
	$val = '';
//	return $val;
	//Buscando la fecha y hora de inicio del medicamento
	
	if( $cmz == 'off' ){
		//Se agrega "UNION" para que traiga los datos de la tabla de contingencia(movhos_00143) Jonatan 12 Nov 2013 
		$sql = "SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000003 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeart = '$art'
					AND fenfue IN $cargos
				UNION
				SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000143 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeart = '$art'
					AND fenfue IN $cargos
					AND fdeest = 'on'
				ORDER BY
					fecha_data ASC
				"; //echo "<br>......<pre>$sql</pre>";
	}
	else{
		//Se agrega "UNION" para que traiga los datos de la tabla de contingencia(movhos_00143) Jonatan 12 Nov 2013 
		$sql = "SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000003 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeari = '$art'
					AND fenfue IN $cargos
				UNION
				SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000143 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeari = '$art'
					AND fenfue IN $cargos
					AND fdeest = 'on'
				ORDER BY
					fecha_data ASC
				"; //echo "<br>......<pre>$sql</pre>";
	}
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[0];
	}
	
	return $val;
}

/********************************************************************************
 * Consulta la fecha de finalizacion de un medicamento
 * 
 * @param $his
 * @param $ing
 * @param $art
 * @return unknown_type
 ********************************************************************************/
function consultarFechaFinMedicamento( $his, $ing, $art, $cmz = 'off' ){
	
	global $conex;
	global $wbasedato;
	global $cargos;
	
	$val = '';
//	return $val;
	//Buscando la fecha y hora de inicio del medicamento
	
	if( $cmz == 'off' ){
		//Se agrega "UNION" para que traiga los datos de la tabla de contingencia(movhos_00143) Jonatan 12 Nov 2013 
		$sql = "SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000003 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeart = '$art'
					AND fenfue IN $cargos
				UNION
				SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000143 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeart = '$art'
					AND fenfue IN $cargos
					AND fdeest = 'on'
				ORDER BY
					fecha_data DESC
				"; //echo "<br>......<pre>$sql</pre>";
	}
	else{
		//Se agrega "UNION" para que traiga los datos de la tabla de contingencia(movhos_00143) Jonatan 12 Nov 2013 
		$sql = "SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000003 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeari = '$art'
					AND fenfue IN $cargos
				UNION
				SELECT
					b.fecha_data
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000143 b
				WHERE
					fenhis = '$his'
					AND fening = '$ing'
					AND fennum = fdenum
					AND fdeari = '$art'
					AND fenfue IN $cargos
					AND fdeest = 'on'
				ORDER BY
					fecha_data DESC
				"; //echo "<br>......<pre>$sql</pre>";
		
	}
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[0];
	}
	
	return $val;
}

/****************************************************************************
 FIN DE FUNCIONES
/***************************************************************************/

include_once("root/comun.php");

if(!isset($_SESSION['user']))
	exit("error");
//else{
	
$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

encabezado( "RELACION DE PACIENTES CON USO DE ANTIBIOTICOS SISTEMATICOS", "Diciembre 11 de 2013" ,"clinica");

//AQUI COMIENZA EL PROGRAMA
//if( !isset($ccoori) || !isset($ccodes) || !isset($fechaini) || !isset($fechafin) )
if( !isset($Menu) || $Menu == 0 || empty($txOrigen) )
{
	//Definicion de variables
	if( !isset($txOrigen) ){
		$txOrigen='';
	}
	
	if( !isset($txDestino) ){
		$txDestino = '';
	}
	
	if( !isset($txGrupos) ){
		$txGrupos = '';
	}
	
	if( !isset($txArticulo) ){
		$txArticulo = '';
	}
	
	$q = " SELECT detapl, detval "
		. "   FROM root_000050, root_000051 "
		. "  WHERE empcod = '" . $wemp_pmla . "'"
		. "    AND empest = 'on' "
		. "    AND empcod = detemp ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
				$wcenmez = $row[1];

			if ($row[0] == "afinidad")
				$wafinidad = $row[1];

			if ($row[0] == "movhos")
				$wbasedato = $row[1];

			if ($row[0] == "tabcco")
				$wtabcco = $row[1];
		}
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}
	
	if( !isset($fechaini) )
		$fechaini = date("Y-m-01");
	
	if( !isset($fechafin) )
		$fechafin = date("Y-m-t");

	echo "
	<br><form action='rep_relPacientesUsoAntibiotico.php?wemp_pmla=01' method='post' name='mainmenu' id='mainmenu'>
	<table align=center width='600'>
	<tr class='encabezadotabla' align=center>
			<td width='550'>Centro de costos de origen</td>
	</tr>";
	
	echo "<tr class='fila1'>";
	echo "		<td><SELECT id='ccoori' name='ccoori' style='width:100%' onChange='javascript: agregarOptionASelect( this,\"slCcoOrigen\",\"txOrigen\")'>";
	

	//Generando lista de opciones de Centro de costos
	
	$cco="ccoing<>'on' AND ccourg<>'on' AND ccofac='on'";
	$filtro="--";
		
	$centrosCostos = consultaCentrosCostos($cco, $filtro);
	foreach ($centrosCostos as $centroCostos)
	{
         echo "<option>".$centroCostos->codigo." - ".$centroCostos->nombre."</option>";	
    }

	echo "</SELECT>";
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( ccoori, txOrigen );' style='display:none'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( ccoori, txOrigen );' style='display:none'>";
	
	echo "<tr style='display:none'><td>
			  <TEXTAREA id='txOrigen' name='txOrigen' style='width:100%;' value='$txOrigen' Rows='3' readonly>$txOrigen</TEXTAREA></td>";
	
	echo "<tr>";
	echo "<td>";
	echo "<SELECT id='slCcoOrigen' name='slCcoOrigen' multiple style='width:600' onDblClick='removerOption( this, \"txOrigen\" )' size='5'></select>";
	echo "</td>";
	echo "</tr>";
	
	echo "<tr class='encabezadotabla' align=center>
			<td width='550'>Centro de costos de destino</td>
	</tr>";
	
	echo "<tr class='fila1'><td><SELECT id='ccodes' name='ccodes' style='width:600' onChange='javascript: agregarOptionASelect( this,\"slCcoDestino\",\"txDestino\")'>";
	echo "			<option value='%'>% - Todos</option>";

	//Generando lista de opciones de Centro de costos
	
	$cco="ccofac='on'";
	$filtro="--";
		
	$centrosCostos = consultaCentrosCostos($cco, $filtro);
	foreach ($centrosCostos as $centroCostos)
	{
         echo "<option>".$centroCostos->codigo." - ".$centroCostos->nombre."</option>";	
    }

	echo "</SELECT>";
	
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( ccodes, txDestino );' style='display:none'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( ccodes, txDestino );' style='display:none'>";
	
	echo "<tr style='display:none'><td><TEXTAREA id='txDestino' name='txDestino' style='width:100%;' value='$txDestino' Rows='3' readonly>$txDestino</TEXTAREA></td></tr>";
	
	echo "<tr>";
	echo "<td>";
	echo "<SELECT id='slCcoDestino' name='slCcoDestino' multiple style='width:600' onDblClick='removerOption( this, \"txDestino\" )' size='5'></select>";
	echo "</td>";
	echo "</tr>";
	
	
	echo "<tr class='encabezadotabla' align=center>
			<td width='550'>Grupos de Articulos a Buscar</td>
	</tr>";
	
	echo "<tr class='fila1'>
				<td>
					<SELECT id='grupos' name='grupos' style='width:600' onChange='javascript: agregarOptionASelect( this,\"slGrupos\",\"txGrupos\")'>";
	echo "					<option value='%'>% - Todos</option>";
	
	//Hallando los grupos de articulo			
	$sql = "SELECT 
				artgru
			FROM 
				{$wbasedato}_000026
			GROUP BY 1
			ORDER BY 1 ASC";
	
//				echo $sql;
	$res = mysql_query( $sql, $conex );

	
	
	for( $i = 0; $rows = mysql_fetch_array($res); $i++){
		echo "<option>{$rows['artgru']}</option>";
	}
	
	echo "</SELECT>";
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( grupos, txGrupos );' style='display:none'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( grupos, txGrupos );' style='display:none'>";
	
	echo "<tr>";
	echo "<td>";
	echo "<SELECT id='slGrupos' name='slGrupos' multiple style='width:600' onDblClick='removerOption( this, \"txGrupos\" )' size='5'></select>";
	echo "</td>";
	echo "</tr>";
	
	echo "<tr style='display:none'><td><TEXTAREA id='txGrupos' name='txGrupos' style='width:100%;' value='$txGrupos' onChange='javascript: gruposChange( 0 );' Rows='3' readonly>$txGrupos</TEXTAREA></td>";

	echo "<tr class='encabezadotabla' align=center>
			<td width='550'>Articulos a Buscar</td>
	</tr>
	";
		
	echo "<tr class='fila1'>";
	echo "<td>"; 
	echo "<SELECT name='slParmetroBusqueda' style='width:30%'>"; 
	echo "<option value='artcom'>Nombre comercial</option>";
	echo "<option value='artcod'>C&oacute;digo"; 
	echo "</SELECT>"; 
	echo "<INPUT type='text' name='txArticuloBuscar' style='width:50%' value='".@$txArticuloBuscar."'><INPUT type='submit' value='Buscar' style='width:20%'></td>";
	echo "</tr>";
	echo "<tr class='fila1'><td><SELECT id='articulo' name='articulo' style='width:75%'  onChange='javascript: agregarOptionASelect( this,\"slArticulo\",\"txArticulo\")'>";
	echo "					<option value='%'>% - Todos</option>";
	
	if( !empty($txArticuloBuscar) ){
		//Creando clausula IN para la consulta
		$grupoArticulo=crearIN($txGrupos);
		
		//Hallando los articulo de acuerdo a los grupos
		$sql = "(SELECT 
					artcod, artcom
				FROM 
					{$wbasedato}_000026
				WHERE
					$slParmetroBusqueda LIKE '%$txArticuloBuscar%'
					AND artgru $grupoArticulo
				)
				UNION
				(SELECT 
					a.artcod, a.artcom
				FROM 
					{$wcenmez}_000002 a, 
					{$wcenmez}_000003 b,
					{$wcenmez}_000001 c, 
					{$wcenmez}_000009 d,
					{$wbasedato}_000026 e
				WHERE
					a.$slParmetroBusqueda LIKE '%$txArticuloBuscar%'
					AND a.arttip = c.tipcod
					AND c.tippro = 'on'
					AND c.tipcdo != 'on'
					AND a.artcod = b.pdepro
					AND b.pdeins = d.appcod
					AND d.apppre = e.artcod 
					AND e.artgru $grupoArticulo 
				GROUP BY
					a.artcod
				)
				ORDER BY 2
				";
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		for( $i = 0; $rows = mysql_fetch_array($res); $i++){
			echo "<option>{$rows['artcod']} - {$rows['artcom']}</option>";
		}
	}
	
	echo "</SELECT>";
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( articulo, txArticulo );' style='display:none'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( articulo, txArticulo );' style='display:none'>";
	echo "<INPUT type='button' value='+ Todo' onClick='javascript: agregarTodo( articulo, \"slArticulo\", \"txArticulo\" );'>";
	echo "<INPUT type='button' value='- Todo' onClick='javascript: eliminarTodo( slArticulo, \"txArticulo\" );'>";
	
	echo "<tr style='display:none'><td><TEXTAREA id='txArticulo' name='txArticulo' style='width:100%;' value='$txArticulo' Rows='3' readonly>$txArticulo</TEXTAREA></td></tr>";
	
	echo "<tr>";
	echo "<td>";
	echo "<SELECT id='slArticulo' name='slArticulo' multiple style='width:600' onDblClick='removerOption( this, \"txArticulo\" )' size='5'></select>";
	echo "</td>";
	echo "</tr>";
	
	echo "</table>";
	
	//tabla para la eleccion de fechas
	echo "
	<br>
	<table align=center>
		<tr class='encabezadotabla' align=center>
			<td>Fecha inicial
			<td>Fecha final
		<tr class='fila1'>
			<td>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "		<td>";
	campoFechaDefecto( "fechafin", $fechafin );
	echo "
	</table><br>";

	echo "<br><table align=center>
		<tr>
			<td><INPUT type='button' value='Generar' style='width:120' onClick='javascript: gruposChange( 1 );'></td>
			<td><INPUT type='button' value='Cerrar Ventana' onClick='cerrarVentana();' style='width:120'></td>
	</table>
	
	<INPUT type='hidden' name='wcenmez' value='$wcenmez'>
	<INPUT type='hidden' name='Menu' value='0'>
	
	</form>";
}
else{
	
	$infoArticulos = Array();	//Nombre de los articulos
	$datos = Array();			//Datos para mostrar
	
	//encabezado del informe, fecha inicial y fecha final con que fue generado el reporte
	echo "<form name='main' action='rep_relPacientesUsoAntibiotico.php?wemp_pmla=$wemp_pmla' method='post'>";
	
	echo 
	"<br><table align=center>
		<tr>
			<td align='left' class='fila1'>Centro de costos origen</td>
			<td class='fila2'>";
	
	if( $txOrigen === "% - Todos" || !isset($txOrigen) || $txOrigen == '' ){
		$impresion = "Todos";
	}
	else{
		$impresion = str_replace("\r","<br>", $txOrigen );
	}
	
	echo $impresion;
	
	echo "	</tr><tr>
			<td align='left' class='fila1'>Centro de costos destino</td>
			<td class='fila2'>";
	
	if( $txDestino === "% - Todos" || !isset($txDestino) || $txDestino == '' ){
		$impresion = "Todos";
	}
	else{
		$impresion = str_replace("\r","<br>", $txDestino );
	}
	
	echo $impresion;
	
	echo "</tr><tr>
			<td align='left' class='fila1'>Grupos</td>
			<td class='fila2'>";
	
	if( !isset($antibioticos) ){
		if( $txGrupos === "% - Todos" || !isset($txGrupos) || $txGrupos == '' ){
			$impresion = "Todos";
		}
		else{
			$impresion = str_replace("\r","<br>", $txGrupos );
		}
	}
	else{
		$impresion = "Antibi&oacute;ticos";
	}
	
	echo $impresion;
	
	echo "</td></tr><tr>
			<td align='left' class='fila1'>Articulo</td>
			<td class='fila2'><div class='divStyle'>";
	
	if( $txArticulo === "% - Todos" || !isset($txArticulo) || $txArticulo == '' ){
		$impresion = "Todos";
	}
	else{
		$impresion = str_replace("\r","<br>", $txArticulo );
	}
	
	echo $impresion;
	
	
	//Buscando las fuentes de cargos
	$sql = "SELECT
				ccofca
			FROM
				{$wbasedato}_000011
			WHERE
				ccofca <> '' AND
				ccofca <> 'NO APLICA'				
			GROUP BY ccofca";
				
	$res = mysql_query( $sql, $conex );
	
	//generando string con las opciones de cargos que deben estar en la consulta principal
	$cargos ="(";
	for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		if( $i > 0 )
			$cargos .= ",";
		
		$cargos = $cargos."'$rows[0]'";
	}
	$cargos = $cargos.")";
	
	$ccoori = crearIN( $txOrigen );
	$ccodes = crearIN( $txDestino );
	$grupos = crearIN( $txGrupos );
	$articulos = crearIN( $txArticulo );
	
	echo "</div></td></tr><tr>
			<td align='left' class='fila1'>Fecha inicial</td>
			<td class='fila2'>$fechaini</td>
		</tr><tr>
			<td align='left' class='fila1'>Fecha final</td>
			<td class='fila2'>$fechafin</td>
		</tr>
	</table><br>";
	//Se agrega "UNION" para que traiga los datos de la tabla de contingencia(movhos_00143) Jonatan 12 Nov 2013 
	if( true || !isset( $antibioticos ) ){
		$sql = "(SELECT
					fenhis,
					fening,
					fdeart,
					artcom,
					'off' as cmz
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000003 b,
					{$wbasedato}_000026 c
				WHERE
					fencco $ccoori
					AND fdeser $ccodes
					AND fennum = fdenum
					AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
					AND fenfue IN $cargos
					AND fdeser <> ''
					AND fdeart $articulos
					AND (fdelot = ''
						OR (fdelot != '' 
							AND fdeart = fdeari
						) 
					)
					AND fdeart = artcod
					AND artgru $grupos
				GROUP BY 
					fenhis, fening, fdeart)
				UNION
				(SELECT
					fenhis,
					fening,
					fdeart,
					artcom,
					'off' as cmz
				FROM
					{$wbasedato}_000002 a,
					{$wbasedato}_000143 b,
					{$wbasedato}_000026 c
				WHERE
					fencco $ccoori
					AND fdeser $ccodes
					AND fennum = fdenum
					AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
					AND fenfue IN $cargos
					AND fdeser <> ''
					AND fdeart $articulos
					AND (fdelot = ''
						OR (fdelot != '' 
							AND fdeart = fdeari
						) 
					)
					AND fdeart = artcod
					AND artgru $grupos
					AND fdeest = 'on'
				GROUP BY 
					fenhis, fening, fdeart)";
				
	}
				
	if( true || !isset( $antibioticos ) ){
		//Se agrega "UNION" para que traiga los datos de la tabla de contingencia(movhos_00143) Jonatan 12 Nov 2013 
		if( $ccoori == "LIKE '%'" || strpos( $ccoori,"'".ccoCMZ()."'" ) ){
			
			$sql .= "UNION
					(SELECT
						fenhis,
						fening,
						fdeari as fdeart,
						c.artcom,
						'on' as cmz
					FROM
						{$wbasedato}_000002 a,
						{$wbasedato}_000003 b,
						{$wcenmez}_000002 c,
						{$wcenmez}_000001 d,
						{$wbasedato}_000026 e
					WHERE
						fencco $ccoori
						AND fdeser $ccodes
						AND fennum = fdenum
						AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
						AND fenfue IN $cargos
						AND fdeser <> ''
						AND fdeari $articulos
						AND fdeari = c.artcod
						AND c.arttip = tipcod
						AND tipcdo != 'on'
						AND fdeart = e.artcod 
						AND e.artgru $grupos
					GROUP BY 
						fenhis, fening, fdeari)
					UNION
					(SELECT
						fenhis,
						fening,
						fdeari as fdeart,
						c.artcom,
						'on' as cmz
					FROM
						{$wbasedato}_000002 a,
						{$wbasedato}_000143 b,
						{$wcenmez}_000002 c,
						{$wcenmez}_000001 d,
						{$wbasedato}_000026 e
					WHERE
						fencco $ccoori
						AND fdeser $ccodes
						AND fennum = fdenum
						AND b.fecha_data BETWEEN '$fechaini' AND '$fechafin'
						AND fenfue IN $cargos
						AND fdeser <> ''
						AND fdeari $articulos
						AND fdeari = c.artcod
						AND c.arttip = tipcod
						AND tipcdo != 'on'
						AND fdeart = e.artcod 
						AND e.artgru $grupos
						AND fdeest = 'on'
					GROUP BY 
						fenhis, fening, fdeari)
					order by 
						fenhis asc, fening asc";
		}
	}
//	echo ".......".$antibioticos;
	// echo ".....<pre>".$sql."</pre>";
//	return;

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	
	echo "<td  align='right' colspan=5 width='40%'><span style='font-family: verdana;font-weight:bold;font-size: 10pt;'>Buscar:&nbsp;&nbsp;</b><input id='buscarTabla' type='text' placeholder='Digite palabra clave' style='border-radius: 4px;border:1px solid #AFAFAF'></td>";
	
//	echo "<br>.....Rows: $numrows";
//	echo "<br>.....Inicio: ".date("H:i:s");
	if( $numrows > 0 ){
		
		echo "<table align='center' id='tbInformacion'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td>HC</td>";
		echo "<td>Nombres</td>";
		echo "<td>Edad</td>";
		echo "<td>Cama</td>";
		echo "<td>Fecha de ingreso</td>";
		echo "<td>Fecha de egreso</td>";
		echo "<td>Medicamento</td>";
		echo "<td>Fecha inicio</td>";
		echo "<td>Fecha finalizaci&oacute;n</td>";
		echo "<td>M&eacute;dico(s) tratantes</td>";
		echo "<td>Empresa responsable</td>";
		echo "</tr>";
		
		$filaPacNuevo = 0;
		
		for( $i = 0, $j=0, $k = 0; $rows = mysql_fetch_array($res); $k++, $i++ ){
			
			$fila = "class='fila".(($i%2)+1)."'";
			$fila2 = "class='fila".(($j%2)+1)."'";
			$rows['fdeart'] = strtoupper($rows['fdeart']);
			
			if( @$paciente['ubihis'] != $rows['fenhis'] || @$paciente['ubiing'] != $rows['fening'] ){
				
				$j++;
				echo "<tbody class='find'>";
				if( $k > 0 ) echo "<script>tablaRowsPan(".($filaPacNuevo+1).",".($k-$filaPacNuevo).")</script>";
				
				$filaPacNuevo = $k;
				
				$i=$j;
				
				$fila2 = "class='fila".(($j%2)+1)."'";
				
				$paciente = informacionPaciente( $rows['fenhis'], $rows['fening'] );
				$medicosTratantes = consultarMedicosTratantes( $rows['fenhis'], $rows['fening']);			
			
				echo "<tr $fila2>";
					
				echo "<td align='center'>{$rows['fenhis']}-{$rows['fening']}</td>";
				echo "<td>{$paciente['pacno1']} {$paciente['pacno2']} {$paciente['pacap1']} {$paciente['pacap2']}</td>";
				echo "<td align='center'>".calculoEdad( $paciente['pacnac'] )."</td>";
				echo "<td align='center'>".$paciente['ubihac']."</td>";
				echo "<td align='center'>{$paciente['fecing']}</td>";
				echo "<td align='center'>";
				echo ( $paciente['ubifad'] == '0000-00-00' )? "ACTIVO" : $paciente['ubifad'] ;
				echo "</td>";
				echo "<td>{$rows['fdeart']}-{$rows['artcom']}</td>";
				echo "<td align='center'>".consultarFechaInicioMedicamento( $rows['fenhis'], $rows['fening'], $rows['fdeart'], $rows['cmz'] )."</td>";
				echo "<td align='center'>".consultarFechaFinMedicamento( $rows['fenhis'], $rows['fening'], $rows['fdeart'], $rows['cmz'] )."</td>";
					
				echo "<td>";
					
				if( !empty($medicosTratantes) ){
					foreach( $medicosTratantes as $key => $value ){
						echo $value['medno1']." ".$value['medno2']." ".$value['medap1']." ".$value['medap2']."<br>";
					}
				}
			echo "<td align='center'>[".$paciente['ingres']."] ".$paciente['ingnre']."</td>";
				echo "</td>";
			}
			elseif( true || !isset( $artPorPaciente[ $rows['fenhis']."-".$rows['fening'] ][ $artMostrar ] ) ){
				echo "<tr $fila2>";
				echo "<td>{$rows['fdeart']}-{$rows['artcom']}</td>";
				echo "<td align='center'>".consultarFechaInicioMedicamento( $rows['fenhis'], $rows['fening'], $rows['fdeart'], $rows['cmz'] )."</td>";
				echo "<td align='center'>".consultarFechaFinMedicamento( $rows['fenhis'], $rows['fening'], $rows['fdeart'], $rows['cmz'] )."</td>";
				
				@$artPorPaciente[ $rows['fenhis']."-".$rows['fening'] ][$artMostrar]=true;
			}
			echo "</tr>";
		}
		echo "<script>tablaRowsPan(".($filaPacNuevo+1).",".($k-$filaPacNuevo).")</script>";
		echo "</tbody>";
		
		echo "<table>";
		
		?>
	
	<script>
	$('#buscarTabla').quicksearch('#tbInformacion .find');
	</script>
	<?php
	}
//	echo "<br>.....Fin: ".date("H:i:s");
	
	echo "<INPUT type=HIDDEN name=fechaini value='$fechaini'>";
	echo "<INPUT type=HIDDEN name=fechafin value='$fechafin'>";
	echo "<INPUT type=HIDDEN name='txOrigen' value='$txOrigen'>";
	echo "<INPUT type=HIDDEN name='txDestino' value='$txDestino'>";
	echo "<INPUT type=HIDDEN name='txGrupos' value='$txGrupos'>";
	echo "<INPUT type=HIDDEN name='txArticulo' value='$txArticulo'>";
	echo "<INPUT type=HIDDEN name='txArticuloBuscar' value='$txArticuloBuscar'>";
	echo "<INPUT type=HIDDEN name='slParmetroBusqueda' value='$slParmetroBusqueda'>";
	
	echo "<br><table align=center>
			<tr align=center>
			<td colspan=5>
				<INPUT type='submit' value='Retornar' style='width:100'> | 
				<INPUT type='button' value='Cerrar' onClick='cerrarVentana()' style='width:100'></td>
		</tr>
	</table>
	</form>";
}

?>

</body>
