<head>
</head>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<script>
function findPosY(obj)
{
	var curtop = 0;
	if(obj.offsetParent)
    	while(1)
        {
          curtop += obj.offsetTop;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.y)
        curtop += obj.y;
    return curtop;
  }

/*********************************************************************************
 * Encuentra la posicion en X de un elemento
 *********************************************************************************/
function findPosX(obj)
  {
    var curleft = 0;
    if(obj.offsetParent)
        while(1)
        {
          curleft += obj.offsetLeft;
          if(!obj.offsetParent)
            break;
          obj = obj.offsetParent;
        }
    else if(obj.x)
        curleft += obj.x;
    return curleft;
  }

function ocultar(){
	var divTitle = document.getElementById( "dvTitle" );
	divTitle.style.display = 'none';
}

function mostrar( campo ){
	
	// if (window.netscape) {
		// adicional = 0;
	// }
	
	//Busco la posicion de la celda en la fila
	//No se usa cellIndex, por que en IE, si existe una celda oculta, 
	//el campo cellIndex merma en 1 y no corresponde a la posicion real de la celda
	//en la fila
	if( !campo.cellIndexReal ){
		for( var i = 0; i < campo.parentNode.cells.length; i++ ){
			if ( campo == campo.parentNode.cells[i] ){
				campo.cellIndexReal = i;
				break;
			}
		}
	}

	try{
		clearInterval( interval );
	}
	catch(e){}

	var divTitle = document.getElementById( "dvTitle" );
	
	// divTitle.innerHTML = campo.parentNode.parentNode.rows[0].cells[ campo.cellIndex+adicional ].innerHTML;
	divTitle.innerHTML = "<b>Columna:</b> "+campo.parentNode.parentNode.rows[0].cells[ campo.cellIndexReal ].innerHTML.replace( "<br>", " " );
	
	//Adiciono la informacion (habitacion y articulo) para el tooltip
	if( campo.cellIndexReal > 4 )
	divTitle.innerHTML = divTitle.innerHTML+"<br><br><b>Hab.</b> "+campo.parentNode.parentNode.rows[campo.parentNode.rowIndex].cells[ 0 ].innerHTML+""
						 +"<br><b>Historia:</b> "+campo.parentNode.parentNode.rows[campo.parentNode.rowIndex].cells[ 1 ].innerHTML
						 +"<br><b>Articulo:</b> "+campo.parentNode.parentNode.rows[campo.parentNode.rowIndex].cells[ 3 ].innerHTML
						 +"<br>"+campo.parentNode.parentNode.rows[campo.parentNode.rowIndex].cells[ 4 ].innerHTML;

	divTitle.style.display = '';
	divTitle.style.position = 'absolute';
	// divTitle.style.top = parseInt( findPosY(campo) )- parseInt( campo.offsetHeight );
	
	divTitle.style.left = findPosX( campo );
	if( campo.cellIndexReal < 3 ){
		divTitle.style.top = parseInt( findPosY(campo) )- parseInt( divTitle.offsetHeight );
	}
	else{
		divTitle.style.top = parseInt( findPosY(campo) )- parseInt( divTitle.offsetHeight )-campo.offsetHeight;
	}
	divTitle.style.background = "#FFFFDF";
	divTitle.style.borderStyle = "solid";
	divTitle.style.borderWidth = "1px";

	interval = setTimeout( "ocultar()", 2000 );		
}


function go_saveas() {
    // if (!!window.ActiveXObject) {
        // var nombre = "monitorKardex";
        // document.execCommand("SaveAs",false,nombre);
    // } else if (!!window.netscape) {
        // var r=document.createRange();
        // r.setStartBefore(document.getElementsByTagName("head")[0]);
        // var oscript=r.createContextualFragment('<script id="scriptid" type="application/x-javascript" src="chrome://global/content/contentAreaUtils.js"><\/script>');
        // document.body.appendChild(oscript);
        // r=null;
        // try {
            // netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
            // saveDocument(document);
        // } catch (e) {
            // //no further notice as user explicitly denied the privilege
        // } finally {
            // var oscript=document.getElementById("scriptid");    //re-defined
            // oscript.parentNode.removeChild(oscript);
        // }
    // }
	
	var nameFile = "MonitorDispensacion_<?php
include_once("conex.php"); echo date( "Y_m_d" ); ?>";
	// grab the content of the form field and place it into a variable
	var textToWrite = document.documentElement.innerHTML;
	
	if( textToWrite != '' ){
		
		//  create a new Blob (html5 magic) that conatins the data from your form feild
		var textFileAsBlob = new Blob([textToWrite], {type:'text/plain'});
		// Specify the name of the file to be saved
		var fileNameToSaveAs = nameFile+".html";
		 
		// create a link for our script to 'click'
		// var downloadLink = document.createElement("a");
		var downloadLink = $( "#aDownload" )[0];
		//  supply the name of the file (from the var above).
		// you could create the name here but using a var
		// allows more flexability later.
		downloadLink.download = fileNameToSaveAs;
		
		// allow our code to work in webkit & Gecko based browsers
		// without the need for a if / else block.
		window.URL = window.URL || window.webkitURL;
			  
		// Create the link Object.
		downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
		
		// click the new link
		downloadLink.click();
	}
}


window.onload = function(){

	if(document.getElementById("fixedFiltro")){
    	$("#fixedFiltro").draggable();    	
    }
	
	var tbEncabezado = document.getElementById( "tbQuieta" );
	
	var tbInfo = document.getElementById( "tbInformativa" );
	
	if( tbEncabezado ){
		for( var i = 0; i < tbEncabezado.rows[0].cells.length; i++ ){
			if( tbInfo.rows[0].cells[i].style.display != "none" ){
				tbEncabezado.rows[0].cells[i].width = tbInfo.rows[0].cells[i].offsetWidth-2;
			}
		}
	}
}
</script>

<style>
	BODY            
	{
		font-family: verdana;
		font-size: 10pt;
		height: 1024px;
		width: 1280px;
	}
	input{ 
		width:100px; 
	}
	.encabezadoTabla                                 
	{
		 background-color: #2A5DB0;
		 color: #FFFFFF;
		 font-size: 10pt;
		 font-weight: bold;
	}
	.fila1                                
	{
		 background-color: #C3D9FF;
		 color: #000000;
		 font-size: 10pt;
	}
	.fila2                                
	{
		 background-color: #E8EEF7;
		 color: #000000;
		 font-size: 10pt;
	}
	
	.fondoAmarillo            
	{
		 background-color: #FFFFCC;
		 color: #000000;
		 font-size: 10pt;
	}
	.fondoVerde
	{
		 background-color: #CCFFCC;
		 color: #000000;
		 font-size: 10pt;
	} 
	.fondoRojo                                    
	{
		 background-color: #FEAAA4;
		 color: #000000;
		 font-size: 10pt;
	}
</style>

<body>
<?php
/****************************************************************************************************************************
 * Este reporte muestra cuantos productos han sido creados en Central de Mezclas y cuantos han sido cargado a los pacientes
 *
 * Fecha de creación: Septiembre 1 de 2011
 *
 * Actualizaciones:
 *
 * Febrero 5 de 2018 	Edwin MG.	No se muestran los articulos genericos LQ0000 e IC0000 ni tampoco los grupos de articulos E00 y LTR
 *									configurados en root_000051 con parametro gruposArticulosNoMostrablesMonitorDispensacion
 * Febrero 07 de 2017.	Edwin MG.	Se modifica la funcion go_saveas para que funcione correctamente.
 * Julio 06 de 2012.	Edwin MG.	Se modifica query para que salgan todos los articulos.
 ****************************************************************************************************************************/
 
/****************************************************************************************************************************
 *														FUNCIONES
 ****************************************************************************************************************************/

/************************************************************************************************************************
 * Consulto la diferentes horas de administracion para un medicamento para un paciente que no maneja ciclos de produccion
 ************************************************************************************************************************/
function consultarHorasAdministracionSinCpx( $fechaInicio, $horaInicio, $frecuencia, $fechaActual ){

	$val = "";
	
	$tiempoInicio = strtotime( $fechaInicio." ".$horaInicio );
	$tiempoFinalInicial = strtotime( $fechaActual." 00:00:00" );
	$tiempoFinalFinal = strtotime( $fechaActual." 23:59:59" );
	
	while( $tiempoInicio <= $tiempoFinalFinal ){
		
		if( $tiempoInicio >= $tiempoFinalInicial && $tiempoInicio <= $tiempoFinalFinal ){
			$val .= "-".date( "H", $tiempoInicio );
		}
		
		$tiempoInicio += $frecuencia*3600;
	}
	
	return substr( $val, 1 );
}

/******************************************************************************************
 * Busca la condicion de un articulo de acuerdo al codigo y su descripcion
 ******************************************************************************************/
 function consultarCondicion( $wbasedato, $conex, $condicion ){

	$val = "";

	if( !empty( $condicion ) ){

		$sql = "SELECT 
					Condes, Contip
				FROM 
					{$wbasedato}_000042
				WHERE 
					concod = '$condicion'
				";
					
		$resAN = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrowsAN = mysql_num_rows( $resAN );
				
		if( $numrowsAN ){
		
			$rowsAN = mysql_fetch_array( $resAN );
			
			$val .= $rowsAN[ 'Condes' ];
		
			if( $rowsAN[ 'Contip' ] == 'AN' ){
				$val .= "- ".$rowsAN[ 'Contip' ];
			}
		}
	}
	
	return $val;
}
 
 /************************************************************************************************************************
 * Devuelve un array con todas las frecuncias, con codigo y valor
 * 
 * @return unknown_type
 ************************************************************************************************************************/
function consultarFrecuencias(){
	
	global $conex;
	global $wbasedato;
	
	$val = false;
	
	$sql = "SELECT
				*					
			FROM {$wbasedato}_000043
			WHERE 1
			";

	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - $sql ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		@$val[ $rows[ 'Percod' ] ] = $rows[ 'Perequ' ];
	}
	
	return $val;
}
 
 /************************************************************************************************************
  * Consulta la informacion para una ronda, es decir la hora, la cantidad a dispensar y la cantidad dispensada
  ************************************************************************************************************/
 function consultarInformacionHora( $horasAplicacion, $ronda, &$hora, &$cdi, &$dis ){
 
	$val = "";
	
	if( empty( $horasAplicacion ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicacion );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		if( $ronda == $valores[0] ){
			$hora = $valores[0];
			$cdi = $valores[1];
			$dis = $valores[2];
			$val = $exp[$i];
			break;
		}
	}
	
	return $val; 
 }
 
 /************************************************************************
  * Consulto la regleta del dia anterior
  ************************************************************************/
 function consultarMediaNocheDiaAnterior( $conex, $wbasedato, $reg ){
 
	$val = "";
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000054
			WHERE
				id = '$reg'
			"; //echo "......<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $rows = mysql_fetch_array( $res ) ){
		
		$val = $rows[ 'Kadcpx' ];
	}
	
	return $val;
 }
 
 /************************************************************************************
 * Consulta el saldo de dispensacion grabado
 ************************************************************************************/ 
function consultarSaldoDispensacionGrabado( $horasAplicar ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < 1; $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		// $val = $valores[1];
		
		if( $valores[0] == 'Ant' ){
			$val = $valores[2];
		}
	}
	
	return $val;
}

/************************************************************************************
 * Consulta el saldo de dispensacion
 ************************************************************************************/ 
function consultarSaldoDispensacion( $horasAplicar ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < 1; $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		// $val = $valores[1];
		
		if( $valores[0] == 'Ant' ){
			$val = $valores[1];
		}
	}
	
	return $val;
}
 
/**
 * Consulto el total de saldo en piso, teniendo en cuenta la historia
 */
 function consultarSaldosEnPiso( $conex, $wbasedato, $historia, $ingreso, $articulo ){
 
	$val = 0;
 
	$sql = "SELECT
				SUM(spauen - spausa)
			FROM
				{$wbasedato}_000004
			WHERE
				spahis = '$historia'
				AND spaing = '$ingreso'
				AND spaart = '$articulo'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		$rows = mysql_fetch_array( $res );
		
		$val = $rows[0];
	}
	
	return $val;
 }
 
 /****************************************************************************************
 * Calcula la cantidad a dispensar hasta una ronda dada
 * 
 * @return unknown_type
 ****************************************************************************************/
function cantidadTotalADispensarRonda( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += @$valores[1];
		
		if( $ronda == $valores[0] ){
			break;
		}
	}
	
	return $val;
}
 
 /****************************************************************************************
 * Calcula la cantidad dispensada hasta una ronda dada
 * 
 * @return unknown_type
 ****************************************************************************************/
 function cantidadTotalDispensadaRonda( $horasAplicar, $ronda ){
	
	$val = 0;
	
	if( empty( $horasAplicar ) ){
		return $val;
	}
	
	$exp = explode( ",", $horasAplicar );
	
	for( $i = 0; $i < count( $exp ); $i++ ){
		
		$valores = explode( "-", $exp[$i] );
		
		$val += @$valores[2];
		
		if( $ronda == $valores[0] ){
			break;
		}
	}
	
	return $val;
}
 
 
/******************************************************************************************************************************
 * Dada una regleta, que tiene la siguiente forma
 * [Hora1]-Can1-Dis1,[Hora2]-Can2-Dis2,[Hora3]-Can3-Dis3,...,[HoraN]-CanN-DisN
 *
 * Siendo HoraN, un formato de hora de 24 con minutos y segundos (ejem: 10:00:00), devuelve las horas en que el medicamento 
 * fue cargado al paciente
 *
 * @param	$regleta	String
 ******************************************************************************************************************************/
function consultarHorasGrabadas( $regleta ){

	$val = "";
	
	if( !empty($regleta) ){
		
		//Busco todas las horas posibles segun la regleta
		$horasRegleta = explode( ",", $regleta );
		
		//Consulto las horas de aplicacion, estas son todas aquellas que CanN sean mayores a 0
		$paso = false;
		for( $i = 0; $i < count( $horasRegleta ); $i++ ){
			
			//indice	Significado
			//	0		Hora
			//	1		Cantidad a cargar
			//	2		Cantidad dispensada
			$valores = explode( "-", $horasRegleta[$i] );
			
			if( $valores[0] == "00:00:00" & $paso ){
				break;
			}
			
			if( $valores[0] != "Ant" ){
				if( @$valores[2] > 0 ){
					$val .= "-".substr( $valores[0], 0, 2 );
				}
				$paso = true;
			}
		}
	}
	
	return substr( $val, 1 );
}
  

/******************************************************************************************************************************
 * Dada una regleta, que tiene la siguiente forma
 * [Hora1]-Can1-Dis1,[Hora2]-Can2-Dis2,[Hora3]-Can3-Dis3,...,[HoraN]-CanN-DisN
 *
 * Siendo HoraN, un formato de hora de 24 con minutos y segundos (ejem: 10:00:00), devuelve las hora en que el medicamento 
 * debe ser aplicado
 *
 * @param	$regleta	String
 ******************************************************************************************************************************/
function consultarHorasAdministracion( $regleta ){

	$val = "";

	if( !empty($regleta) ){
		
		//Busco todas las horas posibles segun la regleta
		$horasRegleta = explode( ",", $regleta );
		
		//Consulto las horas de aplicacion, estas son todas aquellas que CanN sean mayores a 0
		$paso = false;
		for( $i = 0; $i < count( $horasRegleta ); $i++ ){
			
			//indice	Significado
			//	0		Hora
			//	1		Cantidad a cargar
			//	2		Cantidad dispensada
			$valores = explode( "-", $horasRegleta[$i] );
			
			if( $valores[0] == "00:00:00" && $paso ){
				break;
			}
			
			if( $valores[0] != "Ant" ){
				if( @$valores[1] > 0 ){
					$val .= "-".substr( $valores[0], 0, 2 );
				}
				$paso = true;
			}
		}
	}
	
	return substr( $val, 1 );
}

/****************************************************************************************************
 * Consulto cuantos medicamentos hay en nevera (creados)
 ****************************************************************************************************/
function consultarSaldoLotesPorArticulo( $conex, $wcenmez, $producto, $fecha ){

	$val = 0;
	
	//Consulto el saldo de los articulos siempre y cuando no se halla vencido el medicamento
	$sql = "SELECT
				SUM(plosal) as Saldo
			FROM
				{$wcenmez}_000004
			WHERE
				plopro = '$producto'
				AND plosal > 0 
				AND plofve >= '$fecha'
			"; //echo ".....<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		@$val = $rows['Saldo'];
	}
	
	return $val;
}
  
/************************************************************************************************
 * Devuelve un array con la informacion del paciente ( nombre, historia, ingreso, cedula, 
 * habitación, centro de costo )
 * 
 * @param $conex
 * @param $his
 * @return unknown_type
 ************************************************************************************************/
function consultarInformacionDelPaciente( $conex, $wbasedato, $his, $ori, &$paciente ){
	
	$val = "";
	
	//Consulta la inforamcion basica del paciente
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000020 c, root_000036 a, root_000037 b 
			WHERE
				habhis = '$his'
				AND habhis = orihis				
				AND oriced = pacced
				AND pactid = oritid 
				AND oriori = '$ori'
			"; //echo ".........<pre>$sql</pre>";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$rows = mysql_fetch_array( $res );
		
		$paciente[ $his ]['historia'] = $rows[ 'Habhis' ];
		$paciente[ $his ]['ingreso'] =  $rows[ 'Habing' ];
		$paciente[ $his ]['nombre'] = $rows['Pacno1']." ".$rows['Pacno2']." ".$rows['Pacap1']." ".$rows['Pacap2'];
		$paciente[ $his ]['cedula'] = $rows['Pacced'];
		$paciente[ $his ]['cco'] = $rows['Habcco'];
		$paciente[ $his ]['habitacion'] = $rows['Habcod'];
	}
	
	return $val;
}

/****************************************************************************************************************************
 * Consulta la informacion basica de un producto ( codigo, nombre comercial, nombre generico, saldo )
 ****************************************************************************************************************************/
function consultarInformacionProducto( $conex, $wcenmez, $wbasedato, $producto, &$articulos ){


	$sql = "SELECT
					*
				FROM
					{$wbasedato}_000026
				WHERE
					artcod = '$producto'
				";
	
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$rows = mysql_fetch_array( $res );
		
		$grupo = explode( '-', $rows['Artgru'] );
		
		$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
		$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
		$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
		$articulos[ $rows['Artcod'] ][ 'Saldo' ] = 0; //consultarSaldoLotesPorArticulo( $conex, $wcenmez, $producto, date( "Y-m-d" ) );
		$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
		$articulos[ $rows['Artcod'] ][ 'grupo' ] = $grupo[0];
	}
	else{

		$sql = "SELECT
					*
				FROM
					{$wcenmez}_000002
				WHERE
					artcod = '$producto'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $num > 0 ){
		
			$rows = mysql_fetch_array( $res );
			
			$articulos[ $rows['Artcod'] ][ 'codigo' ] = $rows['Artcod'];
			$articulos[ $rows['Artcod'] ][ 'nombreComercial' ] = $rows['Artcom'];
			$articulos[ $rows['Artcod'] ][ 'nombreGenerico' ] = $rows['Artgen'];
			$articulos[ $rows['Artcod'] ][ 'Saldo' ] = consultarSaldoLotesPorArticulo( $conex, $wcenmez, $producto, date( "Y-m-d" ) );
			$articulos[ $rows['Artcod'] ][ 'unidadMinima' ] = $rows['Artuni'];
			$articulos[ $rows['Artcod'] ][ 'grupo' ] = '';
		}
	}
}

/********************************************************************************
 * Dibuja la tabla con los resultados
 ********************************************************************************/
function pintarResultados( $detalleTabla, $pacientes, $articulos, $origen, $ccoDestino ){
        
        $ccoDestino1 = $ccoDestino[1];
        $Ccocpx = $ccoDestino[2];
        if ($Ccocpx == 'on'){
            $Ccocpx = 'on';
        }
        else {
            $Ccocpx = 'off';
        }       
        
	if( !empty($detalleTabla) ){
	
		echo "<div id='fixedFiltro'  style='position:absolute;z-index:99;width:101%;height:93px;right:2px;top:220px;padding:5px;background:#FFFFCC;border:2px solid #FFD700;display:none' >";
		echo "<table align='center' id='tbQuieta'>";
		
		//Creo encabezado de la tabla
		echo "<tr class='encabezadotabla'>";
		echo "<th>Hab.</td>";
		echo "<th>Historia</td>";
		echo "<th>Nombre del<br>paciente</td>";		
		echo "<th>C&oacute;digo del<br>articulo</td>";
		echo "<th>Nombre del<br>articulo</td>";
		echo "<th>Dosis</td>";
		echo "<th>Fecha de inicio</td>";
		echo "<th>Hora de inicio</td>";
		echo "<th>Frecuencia</td>";
		echo "<th>D&iacute;as de tratamiento</td>";
		echo "<th>Dosis Maxima</td>";
		echo "<th>Horarios de<br>Administraci&oacute;n</td>";
        
		if ($Ccocpx != 'off'){
			echo "<th>Rondas Grabadas</th>";
		}
		if( $origen == "CM" ){
			echo "<th>Dosis en nevera</th>";
			echo "<th>Dosis pendientes<br>por producir</th>";		
		}
		else{
			echo "<th style='display:none' id='hola5'>Dosis en nevera</th>";
			echo "<th style='display:none'>Dosis pendientes<br>por producir</th>";
		}
		
		echo "<th>Cantidad sin dispensar<br>d&iacute;a anterior</th>";
		echo "<th>Cantidad grabada<br>del saldo del d&iacute;a anterior</th>";
		echo "<th>Cantidad grabada hoy<br>(No incluye saldo del día anterior)</th>";
		echo "<th>Dosis pendientes<br>por grabar</th>";
		echo "<th>Condici&oacute;n</th>";
		echo "<th>Observaciones</th>";
		echo "<th>Enviar</th>";
		echo "<th>Suspendido</th>";
		
		if( $origen == "CM" ){
			echo "<th>Confirmado</th>";
		}
		else{
			echo "<th style='display:none'>Confirmado</th>";
		}
		
		echo "<th>Aprobado en el perfil</th>";
		echo "<th>Saldo en servicio</th>";
		if ($Ccocpx != 'off'){
        	echo "<th>Kardex <br>generado hoy</th>";
        }
		echo "</tr>";
		
		echo "</table>";
		
		echo "</div>";
	
	
	
		echo "<table align='center' id='tbInformativa'>";
		
		//Creo encabezado de la tabla
		echo "<tr class='encabezadotabla'>";
		echo "<th>Hab.</td>";
		echo "<th>Historia</td>";
		echo "<th>Nombre del<br>paciente</td>";		
		echo "<th>C&oacute;digo del<br>articulo</td>";
		echo "<th>Nombre del<br>articulo</td>";
		echo "<th>Dosis</td>";
		echo "<th>Fecha de inicio</td>";
		echo "<th>Hora de inicio</td>";
		echo "<th>Frecuencia</td>";
		echo "<th>D&iacute;as de tratamiento</td>";
		echo "<th>Dosis Maxima</td>";
		echo "<th>Horarios de<br>Administraci&oacute;n</td>";
				
		if ( $Ccocpx != 'off')
		{
			echo "<th>Rondas Grabadas</th>";
		}
		
		if( $origen == "CM" ){
			echo "<th>Dosis en nevera</th>";
			echo "<th>Dosis pendientes<br>por producir</th>";		
		}
		else{
			echo "<th style='display:none' id='hola5'>Dosis en nevera</th>";
			echo "<th style='display:none'>Dosis pendientes<br>por producir</th>";
		}
		
		echo "<th>Cantidad sin dispensar<br>d&iacute;a anterior</th>";
		echo "<th>Cantidad grabada<br>del saldo del d&iacute;a anterior</th>";
		echo "<th>Cantidad grabada hoy<br>(No incluye saldo del día anterior)</th>";
		echo "<th>Dosis pendientes<br>por grabar</th>";
		echo "<th>Condici&oacute;n</th>";
		echo "<th>Observaciones</th>";
		echo "<th>Enviar</th>";
		echo "<th>Suspendido</th>";
		
		if( $origen == "CM" ){
			echo "<th>Confirmado</th>";
		}
		else{
			echo "<th style='display:none'>Confirmado</th>";
		}
		
		echo "<th>Aprobado en el perfil</th>";
		echo "<th>Saldo en servicio</th>";
		if ( $Ccocpx != 'off'){
			echo "<th>Kardex <br>generado hoy</th>";
		}
		
		//Mostrando la informacion encontrada
		$j = 0;
		$l = 0;
		foreach( $pacientes as $keyPaciente => $valuePaciente ){
			
			//Consulto cuantos medicamentos tiene un paciente
			$rowsSpan = $valuePaciente[ 'totalArticulos' ];
			
			$celdasAdicionales = 0;	//Sirve para javascript
			

			
			for( $k = 0; $k < $rowsSpan; $k++ ){
			
				$classFilaPaciente = "class='fila".( ($j%2)+1 )."'";
				$classFilaArticulo = "class='fila".( ($l%2)+1 )."'";
				
				//Creo la fila para los pacientes
				if( $k == 0 ){	//Esto es para que muestre una sola vez la información del paciente por piso
				
					echo "<tr $classFilaPaciente>";
					
					//Mostrando la inforamcion del paciente
					echo "<td align='center' rowspan='$rowsSpan' onMouseOver='mostrar( this );'>";
					echo $valuePaciente['habitacion'];
					echo "</td>";
					echo "<td align='center' rowspan='$rowsSpan' onMouseOver='mostrar( this );'>";
					echo $valuePaciente['historia']."-".$valuePaciente['ingreso'];
					echo "</td>";
					echo "<td rowspan='$rowsSpan' onMouseOver='mostrar( this );'>";
					echo $valuePaciente['nombre'];
					echo "</td>";
					
					$l = $j;
				}
				else{
					echo "<tr $classFilaArticulo>";
					echo "<td style='display:none'>{$valuePaciente['habitacion']}</td>";
					echo "<td style='display:none'>{$valuePaciente['historia']}-{$valuePaciente['ingreso']}</td>";
					echo "<td style='display:none'>{$valuePaciente['nombre']}</td>";					
				}
				
				$i = $valuePaciente['filasAsociadas'][$k];
				
				//Codigo del articulo
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $articulos[ $detalleTabla[$i]['articulo'] ]['codigo'];
				echo "</td>";
				
				//Nombre del articulo
				echo "<td onMouseOver='mostrar( this );'>";
				echo $articulos[ $detalleTabla[$i]['articulo'] ]['nombreComercial'];
				echo "</td>";
				
				//Dosis
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[$i]['dosis'];
				echo "</td>";
				
				//Fecha de inicio
				echo "<td onMouseOver='mostrar( this );' align='center'>";
				echo $detalleTabla[$i]['fechaInicio'];
				echo "</td>";
				
				//Hora de inicio
				echo "<td onMouseOver='mostrar( this );' align='center'>";
				echo $detalleTabla[$i]['horaInicio'];
				echo "</td>";
				
				//Frecuencia
				echo "<td onMouseOver='mostrar( this );' align='center'>";
				echo $detalleTabla[$i]['frecuencia'];
				echo "</td>";
				
				//dias de tratamiento
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[$i]['diasTratamiento'];
				echo "</td>";
				
				//dosis maximas
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[$i]['dosisMaxima'];
				echo "</td>";
				
				//Horarios de administración
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[$i]['horasAplicacion'];
				echo "</td>";
				
				
				//Dosis grabadas
				if ( $Ccocpx != 'off'){
					echo "<td align='center' onMouseOver='mostrar( this );'>";
					echo $detalleTabla[$i]['cantidadGrabada']."";
					echo "</td>";
				}
				
				//Dosis en nevera
				//Solo aplica para central de mezclas
				if( $origen == "CM" ){
					echo "<td align='center' onMouseOver='mostrar( this );'>";
					echo $articulos[ $detalleTabla[$i]['articulo'] ][ 'Saldo' ];
					echo "</td>";
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );' style='display:none'>";
					echo $articulos[ $detalleTabla[$i]['articulo'] ][ 'Saldo' ];
					echo "</td>";
				}
				
				//Dosis pendientes por producir
				//Solo aplica para central de mezclas
				if( $origen == "CM" ){
					echo "<td align='center' onMouseOver='mostrar( this );'>";
					echo $detalleTabla[ $i ][ 'dosisPendientes' ]."";
					echo "</td>";
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );' style='display:none'>";
					echo $detalleTabla[ $i ][ 'dosisPendientes' ]."";
					echo "</td>";
				}
				
				//Cantidad sin dispensar dia anterior
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[ $i ][ 'saldoDispensacion' ];
				echo "</td>";
				
				//Cantidad grabada del saldo de dispensacion del día anterior
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[ $i ][ 'saldoGrabado' ];
				echo "</td>";
				
				//Dosis cantidad grabada
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[ $i ][ 'cantidadGrabadaHoy' ];
				echo "</td>";
				
				//Dosis pendientes por grabar             
				echo "<td align='center' onMouseOver='mostrar( this );'>";
				echo $detalleTabla[ $i ][ 'dosisPorGrabar' ]."";
				echo "</td>";
				
				//Condicion
				echo "<td onMouseOver='mostrar( this );'>";
				echo $detalleTabla[ $i ][ 'Condicion' ];
				echo "</td>";
				
				//Observaciones
				echo "<td onMouseOver='mostrar( this );'>";
				echo $detalleTabla[ $i ][ 'Observaciones' ];
				echo "</td>";
				
				//No Enviar
				if( $detalleTabla[ $i ][ 'enviar' ] == "No" ){
					echo "<td align='center' onMouseOver='mostrar( this );' class='fondoVerde'>";
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );'>";
				}
				echo $detalleTabla[ $i ][ 'enviar' ];
				echo "</td>";
				
				//Suspendido
				if( $detalleTabla[ $i ][ 'suspendido' ] == "Si" ){
					echo "<td align='center' onMouseOver='mostrar( this );' class='fondorojo'>";
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );'>";
				}
				echo $detalleTabla[ $i ][ 'suspendido' ];
				echo "</td>";
				
				//Confirmado
				if( $origen == "CM" ){
					if( $detalleTabla[ $i ][ 'confirmado' ] == "No" ){
						echo "<td align='center' onMouseOver='mostrar( this );' bgColor='#9F81F7'>";				
						echo $detalleTabla[ $i ][ 'confirmado' ];
						echo "</td>";
					}
					else{
						echo "<td align='center' onMouseOver='mostrar( this );'>";				
						echo $detalleTabla[ $i ][ 'confirmado' ];
						echo "</td>";
					}
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );' style='display:none'>";
					echo $detalleTabla[ $i ][ 'confirmado' ];
					echo "</td>";
				}
				
				//Aprobado en el perfil
				if( $detalleTabla[ $i ][ 'aprobado' ] == "No" ){
					echo "<td align='center' onMouseOver='mostrar( this );' class='fondoAmarillo'>";
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );'>";
				}
				echo $detalleTabla[ $i ][ 'aprobado' ];
				echo "</td>";
				
				//Saldo en servicio				
				if( empty($detalleTabla[ $i ][ 'saldoPiso' ]) ){
					echo "<td align='center' onMouseOver='mostrar( this );'>";
					echo "0 ".$articulos[ $detalleTabla[$i]['articulo'] ][ 'unidadMinima' ];
				}
				else{
					echo "<td align='center' onMouseOver='mostrar( this );' bgcolor='#FACC2E'>";
					echo $detalleTabla[ $i ][ 'saldoPiso' ]." ".$articulos[ $detalleTabla[$i]['articulo'] ][ 'unidadMinima' ];
				}
				echo "</td>";
				
				//Kardex automático
				if ( $Ccocpx != 'off'){
					if( true || $k == 0 ){
						if( $detalleTabla[ $i ][ 'kardexAutomatico' ] == "No" ){
							echo "<td align='center' onMouseOver='mostrar( this );' style='background:#247FFF' rowspan='0'>"; //2:E8EEF7 1:C3D9FF
						}
						else{
							echo "<td align='center' onMouseOver='mostrar( this );' rowspan='0'>";
						}
						echo $detalleTabla[ $i ][ 'kardexAutomatico' ];
						echo "</td>";
					}
				}
				
				
				echo "</tr>";
				
				$l++;
			}
			$j++;
			
		}
		
		echo "</table>";
	}
}

/****************************************************************************************************************************
 * 												FIN DE FUNCIONES
 ****************************************************************************************************************************/
  
/****************************************************************************************************************************
 *												INICIO DEL PROGRAMA
 ****************************************************************************************************************************/
include_once( "root/comun.php" );
   
if(!isset($_SESSION['user'])){
	exit("<b>Usuario no registrado</b>");
}
else{

	
		
	$conex = obtenerConexionBD("matrix");
	
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");

	encabezado( "MONITOR DISPENSACION", "1.0 Julio 06 de 2012" ,"clinica" );

	echo "<form>";

	if( empty($fecha) ){
		$fecha = date( "Y-m-d" );
	}
	
	if( empty($mostrar) ){
		$mostrar = "off";
	}
	
	echo "<INPUT type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	//$fecha = date( "Y-m-d" );
	if( $mostrar == "off" || empty( $slCcoDestino ) || empty( $slCcoHabitacion ) ){	//Si no se han elegido los parametros

		echo "<table align='center'>";

		echo "<tr class='encabezadotabla'>";
		echo "<td class='fila1'>Centro de costos origen</td>";
		
		//Buscando los centro de costos de traslado (SF y CM)
		//Estos son los de origen
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011
				WHERE
					ccotra = 'on'
					AND ccofac = 'on'
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		echo "<td class='fila2'>";
		echo "<select name='slCcoOrigen'>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			if( "{$rows['Ccocod']}-{$rows['Ccoima']}-{$rows['Cconom']}" == $slCcoOrigen ){
				echo "<option value='{$rows['Ccocod']}-{$rows['Ccoima']}-{$rows['Cconom']}' selected>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
			}
			else{
				echo "<option value='{$rows['Ccocod']}-{$rows['Ccoima']}-{$rows['Cconom']}'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
			}
		}
		
		echo "</select>";
		echo "</td>";
		
		echo "</tr>";
		/*********************************************************************/
		
		@list( $cod, $des ) = @explode( "-", $slCcoDestino );
		
		echo "<tr class='encabezadotabla'>";
		echo "<td class='fila1'>Centro de costos destino</td>";
		
		//Busco los centro de costos destino
		//Estos son los de origen
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011
				WHERE					
					ccohos = 'on'
					
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		echo "<td class='fila2'>";
		echo "<select name='slCcoDestino' onChange='javascript:document.forms[0].submit();'>";
		echo "<option value=''></option>";
		
		if( $cod == '%' ){
			echo "<option value='%-Todos-on' selected>% - Todos</option>";
		}
		else{
			echo "<option value='%-Todos-on'>% - Todos</option>";
		}
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			if( $cod == $rows['Ccocod'] ){
				echo "<option value='{$rows['Ccocod']}-{$rows['Cconom']}-{$rows['Ccocpx']}' selected>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
			}
			else{
				echo "<option value='{$rows['Ccocod']}-{$rows['Cconom']}-{$rows['Ccocpx']}'>{$rows['Ccocod']} - {$rows['Cconom']}</option>";
			}
		}
		
		echo "</select>";
		echo "</td>";
		
		echo "</tr>";
		/************************************************************************************/
		
		
		
		echo "<tr class='encabezadotabla'>";
		echo "<td class='fila1' colspan='2' align='center'>Habitaci&oacute;n</td>";
		
		echo "<tr class='encabezadotabla'>";
		echo "<td class='fila2' colspan='2'>";
		
		echo "<table align='center'>";
		
		
		//Busco las habitaciones segun el cco
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011 a, 
					{$wbasedato}_000020 b
				WHERE
					
					ccohos = 'on'
					AND habcco = ccocod
					AND habhis != ''
					AND ccocod LIKE '$cod'
				ORDER BY
					habcod
				";
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		echo "<td class='fila2'>";
		echo "<select name='slCcoHabitacion'>";
		
		if( $cod == '' ){
			echo "<option></option>";
		}
		
		echo "<option value='%-Todos'>% - Todos</option>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			echo "<option value='{$rows['Habcod']} - {$rows['Habhis']}'>{$rows['Habcod']} - {$rows['Habhis']}</option>";
		}
		
		echo "</select>";
		echo "</table>";
		
		echo "</td>";
		
		echo "</tr>";
		/************************************************************************************/
		
		echo "<tr>";
		
		echo "<td class='fila1'><b>Fecha</b></td>";
		
		echo "<td class='fila2'>";
		campoFechaDefecto( "fecha", $fecha );
		echo "</td>";
		echo "</tr>";

		
		echo "</tr>";

		echo "</table>";
		
		echo "<br><table align='center'>";
		
		echo "<tr><td>";
		echo "<center><INPUT type='button' value='Aceptar' onclick='document.forms[0].submit();'></center>";
		echo "</td>";
		
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";
		
		echo "</table>";
		
		echo "<INPUT type='hidden' name='mostrar' value='on'>";
		
		
	}
	else{	//Ya se escogieron los parametos
	
	
		/**************************************************************************
		 * Consulta de articulos generico que no se deben mostrar
		 **************************************************************************/
		$sql = "SELECT Artcod
				  FROM ".$wcenmez."_000001 a, ".$wcenmez."_000002 b, ".$wbasedato."_000068 c
			     WHERE arttip = tipcod
			       AND tiptpr = arktip
				   AND arkcod = artcod 
				   AND tiptpr = 'LQ'
				   ";
		
		$resGen = mysql_query( $sql, $conex ) or die ( "Error: ".mysql_errno()." - en el query: ".$sql." - ".mysql_error() );
		
		$arrGenLQIC = Array();
		while( $rows = mysql_fetch_array( $resGen) ){
			$arrGenLQIC[] = $rows['Artcod'];
		}
		/**************************************************************************/
		
		$arrayLC = consultarAliasPorAplicacion( $conex, $wemp_pmla, 'gruposArticulosNoMostrablesMonitorDispensacion' );
		$arrayLC = explode( ',', $arrayLC );
	
		$nombreArchivo = "monitorDispensacion.html";
		
		//Variable global que muestra los resultados que se muestran en pantalla segun los parametros de entrada
		$encabezadoImpresion = "<head></head><style>
								BODY            
								{
									font-family: verdana;
									font-size: 10pt;
									height: 1024px;
									width: 1280px;
								}
								.encabezadoTabla                                 
								{
									 background-color: #2A5DB0;
									 color: #FFFFFF;
									 font-size: 10pt;
									 font-weight: bold;
								}
								.fila1                                
								{
									 background-color: #C3D9FF;
									 color: #000000;
									 font-size: 10pt;
								}
								.fila2                                
								{
									 background-color: #E8EEF7;
									 color: #000000;
									 font-size: 10pt;
								}
							</style>
							<body>
							";
							
		$pieImpresion = "</body>";
	
		$ccoOrigen = explode( "-", $slCcoOrigen );
		$ccoDestino = explode( "-", $slCcoDestino );
		
		$ori = 'SF';
		if( $ccoOrigen[1] == 'on' ){ 
			$ori = 'CM';
		}
	
		//Mostrando informacion de fecha
		// echo "<center style='font-size:20pt'>Origen: <b>{$ccoOrigen[2]}</b><br>Destino: <b>{$ccoDestino[1]}</b></center>";
		// echo "<center style='font-size:20pt'><b>$fecha</b></center>";
		// echo "<center style='font-size:14pt'>Desde las <b>00:00:00</b> - <b>23:59:59</b></center>";
		// echo "<br>";
		
		$tablaInformativa = "<table align='center'>";
		
		$tablaInformativa .= "<tr>";
		$tablaInformativa .= "<td class='fila1'><b>Origen:</b></td>";		
		$tablaInformativa .= "<td class='fila2'>{$ccoOrigen[2]}</td>";
		$tablaInformativa .= "</tr>";
		
		$tablaInformativa .= "<tr>";
		$tablaInformativa .= "<td class='fila1'><b>Destino:</b></td>";		
		$tablaInformativa .= "<td class='fila2'>{$ccoDestino[1]}</td>";
		$tablaInformativa .= "</tr>";
		
		$tablaInformativa .= "<tr>";
		$tablaInformativa .= "<td class='fila1'><b>Fecha:</b></td>";		
		$tablaInformativa .= "<td class='fila2'>$fecha</td>";
		$tablaInformativa .= "</tr>";
		
		$tablaInformativa .= "<tr>";
		$tablaInformativa .= "<td class='fila1'><b>Horas:</b></td>";
		$tablaInformativa .= "<td class='fila2'>00:00:00 - 23:59:59</td>";
		$tablaInformativa .= "</tr>";
		
		$tablaInformativa .= "</table><br>";
		
		$encabezadoImpresion .= $tablaInformativa;
		
		echo $tablaInformativa;
		
		$frecuencias = consultarFrecuencias();
		
		list( $codHab, $historia ) = explode( "-", $slCcoHabitacion );
		
		//Consulto los medicamentos del dia para central de mezclas
		//Todo medicamento de la central de mezclas debe estar confirmado por enfermeria (kadcnf = on ) y son 
		//los unicos que estan confirmados
        // Se elimina este parametro en la consulta (AND ccocod = habcco) para que muestre los datos de cualquier piso 2 Enero de 2012
		$sql = "SELECT
					*
				FROM
					{$wbasedato}_000011 a, 
					{$wbasedato}_000020 b,
					{$wbasedato}_000053 c, 
					{$wbasedato}_000054 d					
				WHERE
					ccocod like '{$ccoDestino[0]}'
					AND ccocod = habcco
					AND habcod LIKE '".trim( $codHab )."'
					AND habhis = karhis
					AND habing = karing
					AND c.fecha_data = '$fecha'
					AND c.fecha_data = kadfec
					AND karhis = kadhis
					AND karing = kading
					AND karcco = kadcco
					AND kadori = '$ori'
					AND kadest = 'on'
					AND karest = 'on'
				ORDER BY
					habcod asc
				"; //echo "......<pre>$sql</pre>";
				
				
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$numrows = mysql_num_rows( $res );
		
		if( $numrows > 0 ){
		
			$detalleTabla = Array();	//Contiene la informacion de la tabla a dibujar
			$pacientes = Array();		//Contiene la informacion de los pacientes
			$articulos = Array();		//Contiene la informacion de la tabla
		
			for( $i = 0, $fila = 0; $rows = mysql_fetch_array( $res ); $i++, $fila++ ){
				
				//Si el articulo es LQ0000 o IC0000 no se muestra
				if( in_array( $rows['Kadart'], $arrGenLQIC ) ){
					continue;
				}
				
				//Consulto la informacion del medicamento si no se ha consultado con anterioridad
				if( empty( $articulos[ $rows['Kadart'] ] ) ){
					consultarInformacionProducto( $conex, $wcenmez, $wbasedato, $rows['Kadart'], $articulos );
				}
				
				//Si el grupo del articulo es E00 o LTQ no se muestra
				if( in_array( $articulos[ $rows['Kadart'] ]['grupo'], $arrayLC ) ){
					continue;
				}
			
				//Consulto nombre la informacion del paciente si no se ha consultado
				if( empty( $pacientes[ $rows['Kadhis'] ] ) ){
					consultarInformacionDelPaciente( $conex, $wbasedato, $rows['Kadhis'], $wemp_pmla, $pacientes );
				}
				
				//Agrego informacion adicional a los pacientes
				@$pacientes[ $rows['Kadhis'] ][ 'totalArticulos' ]++;
				@$pacientes[ $rows['Kadhis'] ][ 'filasAsociadas' ][] = $fila;
				
				
				//Agrego informacion adicional al articulo
				@$articulos[ $rows['Kadart'] ][ 'totalHistorias' ]++;
				@$articulos[ $rows['Kadart'] ][ 'hisAsociadas' ][] = $rows[ 'Kadhis' ];
				
				//Agrego informacion para la tabla
				
				//Consulto la regleta del dia anterior y la informacion necesaria de la media noche
				$regletaAnterior = consultarMediaNocheDiaAnterior( $conex, $wbasedato, $rows['Kadreg'] );
				$infoHora = consultarInformacionHora( $regletaAnterior, "00:00:00", $hora, $cdi, $dis );
				
				if( !empty( $infoHora ) ){
					$infoHora .= ',';
				}
				
				
				$detalleTabla[ $fila ][ 'historia' ] = $rows[ 'Kadhis' ];
				$detalleTabla[ $fila ][ 'articulo' ] = $rows[ 'Kadart' ];
				
							
				
				$detalleTabla[ $fila ][ 'enviar' ] = ( $rows[ 'Kadess' ] == 'on' )? "No" : "Si";				
				$detalleTabla[ $fila ][ 'suspendido' ] = ( $rows[ 'Kadsus' ] == 'on' )? "Si" : "No";
				
				if( $rows[ 'Kadori' ] == 'SF' ){
					$detalleTabla[ $fila ][ 'confirmado' ] = "No requiere";
				}
				else{
					$detalleTabla[ $fila ][ 'confirmado' ] = ( $rows[ 'Kadcon' ] == 'on' )? "Si" : "No";
				}
				
				$detalleTabla[ $fila ][ 'aprobado' ] = ( $rows[ 'Kadare' ] == 'on' )? "Si" : "No";
				$detalleTabla[ $fila ][ 'saldoPiso' ] = round( consultarSaldosEnPiso( $conex, $wbasedato, $rows['Kadhis'], $rows['Kading'], $rows['Kadart'] ), 3 )."";
				
				
				
				
				$detalleTabla[ $fila ][ 'fechaInicio' ] = $rows['Kadfin'];
				$detalleTabla[ $fila ][ 'horaInicio' ] = $rows['Kadhin'];
				$detalleTabla[ $fila ][ 'frecuencia' ] = $frecuencias[ $rows['Kadper'] ]." Horas";
				
				$detalleTabla[ $fila ][ 'dosis' ] = $rows['Kadcfr']." ".$rows['Kadufr'];
				
				//Calculando dosis pendientes por grabar
				if( $rows['Ccocpx'] == "on" ){	//Si maneja ciclos de produccion
				
					$detalleTabla[ $fila ][ 'horasAplicacion' ] = consultarHorasAdministracion( $infoHora.$rows['Kadcpx'] );
					$detalleTabla[ $fila ][ 'cantidadGrabada' ] = consultarHorasGrabadas( $infoHora.$rows['Kadcpx'] );	
				
					$detalleTabla[ $fila ][ 'kardexAutomatico' ] = ( $rows[ 'Karaut' ] != 'on' )? "Si" : "No";	//kardex generado hoy, si esta en on significa que no lo han generado hoy
					$detalleTabla[ $fila ][ 'saldoDispensacion' ] = round( consultarSaldoDispensacion( $rows['Kadcpx'] ), 3 );
					$detalleTabla[ $fila ][ 'saldoGrabado' ] = round( consultarSaldoDispensacionGrabado( $rows['Kadcpx'] ) , 3 );
					
				
				
				
					$totalAplicaciones = cantidadTotalADispensarRonda( $infoHora.$rows['Kadcpx'], "22:00:00" );
					$dosisGrabadas = cantidadTotalDispensadaRonda( $infoHora.$rows['Kadcpx'], "22:00:00" );
					
					$detalleTabla[ $fila ][ 'dosisPorGrabar' ] = round( $totalAplicaciones-$dosisGrabadas, 3 );
					$dosisPendientesPorProducir = $detalleTabla[ $fila ][ 'dosisPorGrabar' ] - $articulos[ $rows['Kadart'] ][ 'Saldo' ];
					
					//Solo aplica para central de mezclas
					if( $rows[ 'Kadori' ] == "CM" ){
						$detalleTabla[ $fila ][ 'dosisPendientes' ] = ( $dosisPendientesPorProducir > 0 )? round( $dosisPendientesPorProducir, 3 ): 0;	//Dosis pendientes por producir
					}
					else{
						$detalleTabla[ $fila ][ 'dosisPendientes' ] = "NA";	//Dosis pendientes por producir
					}
					
					$detalleTabla[ $fila ][ 'cantidadGrabadaHoy' ] = round( $dosisGrabadas - $detalleTabla[ $fila ][ 'saldoGrabado' ] );
				}
				else{	//Si no maneja ciclos de produccion
				
					$detalleTabla[ $fila ][ 'horasAplicacion' ] = consultarHorasAdministracionSinCpx( $detalleTabla[ $fila ][ 'fechaInicio' ], $detalleTabla[ $fila ][ 'horaInicio' ], $detalleTabla[ $fila ][ 'frecuencia' ], $fecha );
					$detalleTabla[ $fila ][ 'cantidadGrabada' ] = 'NA';
					
					
					$detalleTabla[ $fila ][ 'kardexAutomatico' ] = "NA";	//kardex generado hoy, si esta en on significa que no lo han generado hoy
					$detalleTabla[ $fila ][ 'saldoDispensacion' ] = $rows['Kadsad'];
					
					if( $rows['Kadsad'] > 0 ){
					
						$detalleTabla[ $fila ][ 'saldoGrabado' ] = min( $rows['Kadsad'], $rows['Kaddis'] );
					}
					else{
						$detalleTabla[ $fila ][ 'saldoGrabado' ] = 0;
					}
					
					$detalleTabla[ $fila ][ 'dosisPorGrabar' ] = $rows['Kadcdi'] - $rows['Kaddis'];
					
					$dosisPendientesPorProducir = $detalleTabla[ $fila ][ 'dosisPorGrabar' ] - $articulos[ $rows['Kadart'] ][ 'Saldo' ];
					
					//Solo aplica para central de mezclas
					if( $rows[ 'Kadori' ] == "CM" ){
						$detalleTabla[ $fila ][ 'dosisPendientes' ] = ( $dosisPendientesPorProducir > 0 )? round( $dosisPendientesPorProducir, 3 ): 0;	//Dosis pendientes por producir
					}
					else{
						$detalleTabla[ $fila ][ 'dosisPendientes' ] = "NA";	//Dosis pendientes por producir
					}
					
					$detalleTabla[ $fila ][ 'cantidadGrabadaHoy' ] = $rows[ 'Kaddis' ] - $rows[ 'Kadsad' ];
					
					if( $detalleTabla[ $fila ][ 'cantidadGrabadaHoy' ] < 0 ){
						$detalleTabla[ $fila ][ 'cantidadGrabadaHoy' ] = 0;
					}
					
					
				}	//Fin de calculos para cco que no maenjan ciclos de produccion
				
				$detalleTabla[ $fila ][ 'Observaciones' ] = $rows[ 'Kadobs' ];
				$detalleTabla[ $fila ][ 'Condicion' ] = consultarCondicion( $wbasedato, $conex, $rows[ 'Kadcnd' ] );
				
				$detalleTabla[ $fila ][ 'diasTratamiento' ] = $rows[ 'Kaddia' ];
				$detalleTabla[ $fila ][ 'dosisMaxima' ] = $rows[ 'Kaddma' ];
			}
			
			//Creo link para descargar el archivo
			echo "<a href='#1' onClick='go_saveas(); return false'>Descargar archivo</a><br><br>";
			echo "<a id='aDownload' style='display:none'></a><br><br>";
			
			//Pinto los resultados encontrados
			pintarResultados( $detalleTabla, $pacientes, $articulos, $ori, $ccoDestino );
		}
		else{
			echo "<center style='font-size:20pt'><b>NO SE ENCONTRARON RESULTADOS</b></center>";
		}
		
		echo "<br><table align='center' width='100%'>";
		echo "<td align='right'>";
		echo "<br>Hora de generaci&oacute;n del reporte: <b>".date( "H:i:s" )."</b>";
		echo "</td></tr>";
		echo "</table>";
		
		
		echo "<br><table align='center'>";
		
		echo "<tr><td>";
		echo "<center><INPUT type='button' value='Retornar' onclick='document.forms[0].submit();'></center>";
		echo "</td>";
		
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";
		
		
		
		echo "</table>";
	}

	echo "<div id='dvTitle' style='display:none;position:absolute' onMouseOver='this.style.display=\"none\"'></div>"; 
	
	echo "</form>";
}
   
  /****************************************************************************************************************************
   *												FIN DEL PROGRAMA
   ****************************************************************************************************************************/
?>
</body>

<script>
if(document.getElementById("fixedFiltro")) { 
	fixedMenuId = "fixedFiltro"; 
	var fixedMenu = {
		hasInner:typeof window.innerWidth == "number", 
		hasElement:document.documentElement != null && document.documentElement.clientWidth, 
		menu:document.getElementById ? document.getElementById(fixedMenuId) : document.all ? document.all[fixedMenuId] : document.layers[fixedMenuId]
	}; 
	fixedMenu.computeShifts = function() { 
		fixedMenu.shiftX = fixedMenu.hasInner ? pageXOffset : fixedMenu.hasElement ? document.documentElement.scrollLeft : document.body.scrollLeft; fixedMenu.shiftX += fixedMenu.targetLeft > 0 ? fixedMenu.targetLeft : (fixedMenu.hasElement ? document.documentElement.clientWidth : fixedMenu.hasInner ? window.innerWidth - 20 : document.body.clientWidth) - fixedMenu.targetRight - fixedMenu.menu.offsetWidth; 
		fixedMenu.shiftY = fixedMenu.hasInner ? pageYOffset : fixedMenu.hasElement ? document.documentElement.scrollTop : document.body.scrollTop; fixedMenu.shiftY += fixedMenu.targetTop > 0 ? fixedMenu.targetTop : (fixedMenu.hasElement ? document.documentElement.clientHeight : fixedMenu.hasInner ? window.innerHeight - 20 : document.body.clientHeight) - fixedMenu.targetBottom - fixedMenu.menu.offsetHeight }; 
		fixedMenu.moveMenu = function() { 
			fixedMenu.computeShifts(); 
			if(fixedMenu.currentX != fixedMenu.shiftX || fixedMenu.currentY != fixedMenu.shiftY) { 
				fixedMenu.currentX = fixedMenu.shiftX; 
				fixedMenu.currentY = fixedMenu.shiftY; 
				if(document.layers) { 
					fixedMenu.menu.left = fixedMenu.currentX; fixedMenu.menu.top = fixedMenu.currentY 
				}
				else { 
					fixedMenu.menu.style.left = fixedMenu.currentX + "px"; fixedMenu.menu.style.top = fixedMenu.currentY + "px" 
				} 
			}
			fixedMenu.menu.style.right = ""; 
			fixedMenu.menu.style.bottom = "" 
		}; 
		fixedMenu.floatMenu = function() { 
			fixedMenu.moveMenu(); 
			setTimeout("fixedMenu.floatMenu()", 20) 
		};
		fixedMenu.addEvent = function(a, b, f) { 
			if(typeof a[b] != "function" || typeof a[b + "_num"] == "undefined") { 
				a[b + "_num"] = 0; 
				if(typeof a[b] == "function") {
					a[b + 0] = a[b]; a[b + "_num"]++ 
				}
				a[b] = function(c) { 
				var g = true; c = c ? c : window.event; for(var d = 0;d < a[b + "_num"];d++)if(a[b + d](c) === false)g = false; return g 
				} 
			}
			for(var e = 0;e < a[b + "_num"];e++)
				if(a[b + e] == f)
					return; a[b + a[b + "_num"]] = f; a[b + "_num"]++ }; fixedMenu.supportsFixed = function() { var a = document.createElement("div"); a.id = "testingPositionFixed"; a.style.position = "fixed"; a.style.top = "0px"; a.style.right = "0px"; document.body.appendChild(a); var b = 1; if(typeof a.offsetTop == "number" && a.offsetTop != null && a.offsetTop != "undefined")b = parseInt(a.offsetTop); if(b == 0)return true; return false }; fixedMenu.init = function() { if(fixedMenu.supportsFixed())fixedMenu.menu.style.position = "fixed"; else { var a = document.layers ? fixedMenu.menu : fixedMenu.menu.style; fixedMenu.targetLeft = parseInt(a.left); fixedMenu.targetTop = parseInt(a.top); fixedMenu.targetRight = parseInt(a.right); fixedMenu.targetBottom = parseInt(a.bottom); if(document.layers) { menu.left = 0; menu.top = 0 }fixedMenu.addEvent(window, "onscroll", fixedMenu.moveMenu); fixedMenu.floatMenu() } }; fixedMenu.addEvent(window, "onload", fixedMenu.init); fixedMenu.hide = function() { fixedMenu.menu.style.display = "none"; return false }; fixedMenu.show = function() { fixedMenu.menu.style.display = "block"; return false } }
</script>