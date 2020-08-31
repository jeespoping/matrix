<html>
<head>
<title>REPORTE VENTAS Y ENTREGAS POR GRUPO</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<style type="text/css">
BODY
{
    font-family: Verdana;
    font-size: 10pt;
    margin: 0px;
}
</style>


<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="javascript">
	function enviar(){
		document.forma.submit();
	}

	function consultarSubGrupos()
	{
		var contenedor = document.getElementById('cntSubgrupo');
		var imagen = document.getElementById('imagen1');

		var parametros = "consultaAjax=01&basedatos="+document.forms.forma.wbasedato.value+"&grupo=" + document.forms.forma.wgrupo.value;

		try{
		ajax=nuevoAjax();

		ajax.open("POST", "../../../include/root/comun.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				contenedor.innerHTML=ajax.responseText;
			}
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}
	}

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

			}
		}
		else{
			cmDestino.value = valor;
		}
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
		consultarSubGrupos();
	}

    function removerOption( campo, txCampo ){

    	removeCCO( campo, document.getElementById( txCampo ) );
    	campo.removeChild( campo.options[ campo.selectedIndex ] );
	}

    //Para cargar las apciones previamente elegidos
	window.onload = function(){

		var auxTxGrupos = document.getElementById( "txGrupos" );

		if( auxTxGrupos ){
			agregarOption( document.getElementById( "slGrupos" ), auxTxGrupos.value.split("\n").reverse() );
		}

		try{
			document.getElementById( "wgrupo" ).selectedIndex = -1;
		}catch(e){}
	}

	// Vuelve a la página anterior llevando sus parámetros
	function retornar_consulta(wemp_pmla,wfecini,wfecfin,wsede,wgrupo,txGrupos,wsubgrupo,wtipofecha)
	{
		location.href = "RepFacGrupo.php?wemp_pmla="+wemp_pmla+"&wfecini="+wfecini+"&wfecfin="+wfecfin+"&wsede="+wsede+"&wgrupo="+wgrupo+"&txGrupos="+txGrupos+"&wsubgrupo="+wsubgrupo+"&wtipofecha="+wtipofecha;
	}
</SCRIPT>

</head>

<?php
include_once("conex.php");
/*BS'D
 * REPORTE VENTAS Y ENTREGAS POR GRUPO
 */
//=================================================================================================================================
//PROGRAMA: RepFacGrupo.php
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepFacGrupo.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
/*
    Septiembre 03 de 2013
    Edwar Jaramillo:        Se adiciona la columna de telefono, para esto se hace un cruce con la tabla 000041

*/
//+-------------------+------------------------+-----------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 |
//+-------------------+------------------------+-----------------------------------------+
//|  2008-09-01       | Mauricio Sánchez       | creación del script.					 |
//+-------------------+------------------------+-----------------------------------------+
//+-------------------+------------------------+-----------------------------------------+
//|  2011-05-27       | Mario Cadavid          | Se modificó el query principal por 	 |
//| lentitud en resultados, se crearon tablas temporarles y se pasaron condiciones en el |
//| query a codigo PHP. También se modificó el diseño adaptándolo al nuevo diseño con 	 |
//| base en los css del sistema.					 									 |
//+-------------------+------------------------+-----------------------------------------+
//|  2011-09-19       | Mario Cadavid          | Se adicionó la búsqueda por varios 	 |
//| grupos teniendo en cuenta que si se seleccionan varios grupos se entiende que se 	 |
//| incluyen todos los subgrupos de estos												 |
//+-------------------+------------------------+-----------------------------------------+

//FECHA ULTIMA ACTUALIZACION 	: 2011-09-19




/*DESCRIPCION:Este reporte presenta la lista de facturas por centro(s) de costo(s), por detalle y por procedimiento

TABLAS QUE UTILIZA:
 clisur_000003 Maestro de centros de costos.
 clisur_000004 Maestro de conceptos.
 clisur_000018 Información basica de la factura.
 clisur_000066 Relación entre conceptos y procedimientos.
 clisur_000106 Procedimientos.

INCLUDES:
  conex.php = include para conexión mysql

VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $resultado =
=================================================================================================================================*/

/**
 * Crea una clausal IN (x) para la consulta principal, en caso de que $valores
 * sea vacio crea un LIKE '%'
 */
function crearIN( $valores ){

	global $wsubgrupo;

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

				if( $val == "%" ){
					$in = "LIKE '%'";
					return $in;
				}

				$exp = explode("-", $val );

				if( $i > 0 ){
					$in .= ",";
				}
				$in .= "'".trim($exp[0])."'";

				$i++;
			}

		}
		$in .= ")";
		if($i>1)
			$wsubgrupo = '%';
		return $in;
	}
}

/**
 * Forma la lista de grupos seleccionados
 */
function mostrarIN( $valores ){

	if( empty( $valores ) ){
		$in = " Todos los grupos ";
		return $in;
	}
	else{

		$in = "";
		$i = 0;

		$ccocodnam = explode( "\r", $valores );

		foreach( $ccocodnam as $val ){

			if( !empty($val) ){

				if( $val == "%" ){
					$in = " Todos los grupos ";
					return $in;
				}

				$exp = explode("-", $val );

				if( $i > 0 ){
					$in .= " - ";
				}
				$in .= "".trim($exp[1])."";

				$i++;
			}

		}
		$in .= "";
		return $in;
	}
}

/**
 * Forma la lista de grupos seleccionados
 */
function parametroIN( $valores ){

	if( empty( $valores ) ){
		$in = "";
		return $in;
	}
	else{

		$in = "";
		$i = 0;

		$ccocodnam = explode( "\r", $valores );

		foreach( $ccocodnam as $val ){

			if( !empty($val) ){

				if( $val == "%" ){
					$in = "";
					return $in;
				}

				$exp = explode("-", $val );

				if( $i > 0 ){
					$in .= ":";
				}
				$in .= "".trim($exp[0]."-".$exp[1])."";

				$i++;
			}

		}
		$in .= "";
		return $in;
	}

}

include_once("root/comun.php");

//Inicio
if(!isset($_SESSION['user'])){
	echo "error";
}else{

	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;

  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
	$wactualiz = "Sept. 03 de 2013";

    // Se muestra el encabezado del programa
    encabezado("REPORTE VENTAS Y ENTREGAS POR SEDE Y GRUPO",$wactualiz, "logo_".$wbasedato);

  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";  //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec6";  //Fondo encabezado del detalle
  	$wclfg="003366"; //Color letra parametros

  	echo "<form action='RepFacGrupo.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

  if (!isset($bandera))
  	{
		//Parámetros de consulta del reporte
  		if (!isset($wfecini) || $wfecini=="")
  			$wfecini=$wfecha;

  		if (!isset($wfecfin) || $wfecfin=="")
  			$wfecfin=$wfecha;

		if( !isset($txGrupos) )
			$txGrupos = '';

		if( !isset($wsede) )
			$wsede = '';

		if( !isset($wgrupo) )
			$wgrupo = '';

		if( !isset($wsubgrupo) )
			$wsubgrupo = '';

		//Fecha inicial de consulta
  		echo "<table align='center' border=0 width=640>";
  		echo "<tr height=31>";
  		echo "<td class='fila2' align=center><b>Fecha inicial </b>";
  		campoFechaDefecto("wfecini",$wfecini);
  		echo "</td>";

  		//Fecha final de consulta
  		echo "<td class='fila2' align=center><b>Fecha final </b>";
  		campoFechaDefecto("wfecfin",$wfecfin);
  		echo "</td>";
  		echo "</tr>";

		//Sede
  		echo "<tr height=31>";
  		echo "<td colspan=2 align=center class='fila2' align=center><b>Sede: </b>";
  		echo "<select name='wsede'>";
  		$q=  "SELECT ccocod, ccodes "
  		."    FROM ".$wbasedato."_000003 "
  		."    ORDER by 1";
  		$res1 = mysql_query($q,$conex);
  		$num1 = mysql_num_rows($res1);
  		if ($num1 > 0 )
  		{
  			echo "<option value='%'>Todas las sedes</option>";
  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
  				if(isset($wsede) && $wsede==$row1[0]."-".$row1[1])
					echo "<option value='".$row1[0]."-".$row1[1]."' selected>".$row1[0]."-".$row1[1]."</option>";
				else
					echo "<option value='".$row1[0]."-".$row1[1]."'>".$row1[0]."-".$row1[1]."</option>";
  			}
  		}
  		echo "</select></td>";
		echo "</tr><tr height=31>";

  		//Grupos
  		echo "<td colspan=2 height=31 align=center class='fila2' ><b>Grupo : </b>";
  		echo "<select name='wgrupo' style='width: 450px' onChange='javascript: agregarOptionASelect( this,\"slGrupos\",\"txGrupos\")'>";
  		$q2= "SELECT grucod, grudes "
  		."    FROM ".$wbasedato."_000004 "
  		."    WHERE gruest = 'on'  "
  		."     order by grucod, grudes ";
  		$res2 = mysql_query($q2,$conex);
  		$num2 = mysql_num_rows($res2);
  		echo "<option value='%'>Todos los grupos</option>";
  		for ($i=1;$i<=$num2;$i++)
  		{
  			$row2 = mysql_fetch_array($res2);
			if(isset($wgrupo) && $wgrupo==$row2[0])
				echo "<option value=".$row2[0]." selected>".$row2[0]."-".$row2[1]."</option>";
			else
				echo "<option value=".$row2[0].">".$row2[0]."-".$row2[1]."</option>";
  		}
  		echo "</select>";

  		echo "</td>";
  		echo "</tr>";

		//$txGrupos = str_replace(':','\r',$txGrupos);

		echo "<tr>";
  		echo "<td colspan=2 align=center class='fila2'>";
		echo "<SELECT id='slGrupos' name='slGrupos' multiple style='width:600' onDblClick='removerOption( this, \"txGrupos\" )' size='5'></select>";
  		echo "</td>";
  		echo "</tr>";
		echo "<tr style='display:none'>";
  		echo "<td colspan=2 align=center class='fila2'>";
		echo "<textarea id='txGrupos' name='txGrupos' style='width:100%;' value='".$txGrupos."' Rows='3'>".$txGrupos."</textarea></td>";
  		echo "</td>";
  		echo "</tr>";

  		//Subgrupos -- Carga por ajax
  		echo "<tr height=31>";
  		echo "<td colspan=2 height=31 align=center class='fila2'><b>Subgrupo : </b>";
  		echo "<span id='cntSubgrupo'>";
  		echo "<select name='wsubgrupo' style='width: 450px'>";
  		echo "<option value='%'>Todos los subgrupos</option>";
  		echo "</select>";
  		echo "</span></td>";

  		//Vendedor
//  		echo "<tr>";
//  		echo "<td align=center class='fila2' colspan=2><b>Vendedor : </b>";
//  		echo "<select name='wvendedor' style='width: 450px'>";
//  		$q2= "SELECT Cjeusu, Cjecco "
//  		."    FROM ".$wbasedato."_000030 "
//  		."    order by 2 ";
//  		$res2 = mysql_query($q2,$conex);
//  		$num2 = mysql_num_rows($res2);
//  		echo "<option value='%'>Todos los vendedores</option>";
//  		for ($i=1;$i<=$num2;$i++)
//  		{
//  			$row2 = mysql_fetch_array($res2);
//  			echo "<option value=".$row2[0].">".$row2[0]."-".$row2[1]."</option>";
//  		}
//  		echo "</select></td>";
//  		echo "</tr>";

  		echo "<tr align='center' height=31><td colspan=2 class='fila2'>";
  		echo "<b>Fecha facturacion</b><input type='radio' name='wtipofecha' value='F' onclick='javascript:enviar();'>";
  		echo " &nbsp;&nbsp;&nbsp; <b>Fecha entrega</b><input type='radio' name='wtipofecha' value='E' onclick='javascript:enviar();'>";
  		echo "</td></tr>";
    	echo "</table>";

				echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
				echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    	echo "<br><br><div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

    	//Se configura el calendario cuando ingresa la primera vez
		funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
		funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
  	} else {
        $caracteres2               = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
        $caracteres                = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
		echo "<table border=0 align='center' width='670'><tr><td class='encabezadoTabla' align='center'>";
  		if ($wtipofecha=='F'){
  			echo "REPORTE VENTAS Y ENTREGAS POR SEDE Y GRUPO [POR FECHA DE FACTURACION]";
  		}else{
  			echo "REPORTE VENTAS Y ENTREGAS POR SEDE Y GRUPO [POR FECHA DE ENTREGA]";
  		}
  		echo "</td></tr></table><br>";

		if($wsede=='%')
			$sede = ' Todas las sedes ';
		else
			$sede = $wsede;
		if($wgrupo=='%')
			$grupo = ' Todos los grupos ';
		else
			$grupo = mostrarIN($txGrupos);

		if($wsubgrupo=='%')
			$subgrupo = ' Todos los subgrupos ';
		else
		{
			$q = " SELECT
						Sgrcod, Sgrdes
					FROM
						".$wbasedato."_000005
					WHERE
						Sgrcod = '".$wsubgrupo."'
						AND Sgrest = 'on' ;";
			$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$rs = mysql_fetch_array($res);
			$subgrupo = $rs[0]."-".$rs[1];
		}

		if($wtipofecha=='F')
			$tipofecha = ' fecha facturacion ';
		else
			$tipofecha = ' fecha entrega';

  		echo "<table border='0' align='center' width='410'>";
  		echo "<tr class=fila2><td align=left>&nbsp;<B>Fecha inicial:</B> ".$wfecini."</td>";
  		echo "<td align=center>&nbsp;<B>Fecha final:</B> ".$wfecfin."</td></tr>";
  		echo "<tr class=fila2><td align=left colspan=2>&nbsp;<B>Sede:</B> ".$sede."</td></tr>";
  		echo "<tr class=fila2><td align=left colspan=2>&nbsp;<B>Grupo:</B> ".$grupo."</td></tr>";
  		echo "<tr class=fila2><td align=left colspan=2>&nbsp;<B>Subgrupo:</B> ".$subgrupo."</td></tr>";
  		echo "<tr class=fila2><td align=left colspan=2>&nbsp;<B>Reporte por:</B> ".$tipofecha."</td></tr>";


//  		echo "<td><B>Grupo :</B> ".$wgrupo."</td></tr>";
//  		echo "<tr><td><B>Procedimiento :</B> ".$wprocod."</td>";
//  		echo "<tr><td colspan=3><B>Sede :</B> ".$wsede."</td></tr>";
  		echo "</table>";

  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wgrupo' value='".$wgrupo."'>";
  		echo "<input type='HIDDEN' NAME= 'txGrupos' value='".$txGrupos."'>";
  		echo "<input type='HIDDEN' NAME= 'wsede' value='".$wsede."'>";
  		echo "<input type='HIDDEN' NAME= 'wsubgrupo' value='".$wsubgrupo."'>";


		// Borra la tabla temporal
		$qdel = "DROP TABLE IF EXISTS tempuv_01";
		$errdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		if($wtipofecha != 'E')
		{
			// Creo una tabla temporal con las ordenes de laboratorio del periodo
			$qcrea =  " CREATE TABLE IF NOT EXISTS tempuv_01 AS "
			  ."  		SELECT MIN(ordfen) ordfen, ordfac, ordffa, ordvel, ordvem,
							   ordlei, ordled, ordcaj,  ordnro, ordfec, ordfre
						  FROM ".$wbasedato."_000133
						 WHERE ordfec BETWEEN '".$wfecini."' AND '".$wfecfin."'
						 GROUP  BY ordfac ";
			$errcrea = mysql_query($qcrea, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcrea . " - " . mysql_error());
		}
		else
		{
			// Creo una tabla temporal con las ordenes de laboratorio del periodo
			$qcrea =  " CREATE TABLE IF NOT EXISTS tempuv_01 AS "
			  ."  		SELECT MIN(ordfen) ordfen, ordfac, ordffa, ordvel, ordvem,
							   ordlei, ordled, ordcaj, ordnro, ordfec, ordfre
						  FROM ".$wbasedato."_000133
						 WHERE ordfen BETWEEN '".$wfecini."' AND '".$wfecfin."'
						 GROUP  BY ordfac ";
			$errcrea = mysql_query($qcrea, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcrea . " - " . mysql_error());
		}

		// Borra la tabla temporal
		$qdel = "DROP TABLE IF EXISTS tempuv_02";
		$errdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		$wsede_arr = explode("-",$wsede);

		if($wtipofecha != 'E')
		{
			// Creo una tabla temporal con las facturas del periodo
			$qcrea =  "  CREATE TABLE IF NOT EXISTS tempuv_02 AS "
					 ."	 SELECT Fenfac, Fendpa, Fennpa, Fenval, Fenvnc, Fensal, Fenabo, Fencop, Fenrbo,
								ordfen, ordfac, ordffa, ordvel, ordvem, ordlei, ordled, ordcaj,
								Fenfec, Fencco, Fenest, Fenffa, ordnro, ordfec, ordfre
						   FROM ".$wbasedato."_000018
					  LEFT JOIN tempuv_01
							 ON ordfac = Fenfac
							AND ordffa = Fenffa
						  WHERE Fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'
							AND Fencco LIKE '".$wsede_arr[0]."'
							AND Fenest = 'on'";
		}
		else
		{
			$qcrea =  "  CREATE TABLE IF NOT EXISTS tempuv_02 AS "
					 ."	 SELECT Fenfac, Fendpa, Fennpa, Fenval, Fenvnc, Fensal, Fenabo, Fencop, Fenrbo,
								ordfen, ordfac, ordffa, ordvel, ordvem, ordlei, ordled, ordcaj,
								Fenfec, Fencco, Fenest, Fenffa, ordnro, ordfec, ordfre
						   FROM ".$wbasedato."_000018
					  LEFT JOIN tempuv_01
							 ON ordfac = Fenfac
							AND ordffa = Fenffa
						  WHERE ordfen BETWEEN '".$wfecini."' AND '".$wfecfin."'
							AND Fencco LIKE '".$wsede_arr[0]."'
							AND Fenest = 'on'";
		}

		$errcrea = mysql_query($qcrea, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcrea . " - " . mysql_error());


		if($wgrupo=='%')
			$sql_grupos = " LIKE '%' ";
		else
			$sql_grupos = crearIN($txGrupos);

		// Borra la tabla temporal
		$qdel = "DROP TABLE IF EXISTS tempuv_03";
		$errdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creo una tabla temporal con las facturas del periodo
		$qcrea =  " CREATE TABLE IF NOT EXISTS tempuv_03 AS "
				 ."	SELECT Fenfac, Fendpa, Fennpa, Fenval, Fenvnc, Fensal, Fenabo, Fencop, Fenrbo,
						   ordfen, ordfac, ordffa, ordvel, ordvem, ordlei, ordled, ordcaj,
						   Fenfec, Fencco, Fenest, Fenffa, Fdecon, ordnro, ordfec, ordfre
					  FROM ".$wbasedato."_000065, tempuv_02
					  WHERE Fdeest = 'on'
						AND Fdecon ".$sql_grupos."
						AND Fdefue = Fenffa
						AND Fdedoc = Fenfac
						";
		$errcrea = mysql_query($qcrea, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qcrea . " - " . mysql_error());
		//echo $qcrea."<br>";

		//Consulta
		$q = "SELECT
				Fenfac Factura,
				CONCAT( Fendpa,  ' - ', Fennpa ) Paciente,
				Vennum,	Grudes Grupo,
				Vdecan Cantidad,
				( Vdevun * Vdecan ) - (Vdedes*(1+(Vdepiv /100))),
				Fenval Valor_Total_Factura,
				( Fenabo + Fencop + Fenrbo )Abonos,
				Fenvnc Nota_Credito,
				Fensal Saldo,
				IFNULL( TRIM( ordfen ) ,  ''  ) Fecha_entrega,
				ordvel,
				ordvem,
				ordcaj,
				Fenfec Fecha_factura,
				Fdecon,
                Clite1 AS telefono,
                ordnro numeroOrden,
                ordfec fechaOrden,
                ordfre fechaRecepcion
			FROM
				tempuv_03,
				".$wbasedato."_000017, ".$wbasedato."_000016, ".$wbasedato."_000001, ".$wbasedato."_000004, ".$wbasedato."_000041
			WHERE Vdenum = Vennum
				AND Vennfa = Fenfac
				AND Venffa = Fenffa
				AND Vdeart = Artcod
				AND Fdecon = SUBSTRING_INDEX( Artgru,  '-', 1  )
				AND SUBSTRING( Artgru FROM INSTR( Artgru,  '-'  )  + 1  )  LIKE  '".$wsubgrupo."'
				AND Grucod = Fdecon
                AND Fendpa = Clidoc
			ORDER BY Fenfac
		";

		//Si es por fecha de entrega se modifica unicamente Fenfec por Ordfen
		//echo "<br>".$q."<br>";

		$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($err);

		//Variables acumuladoras
		$nroFacturas = 0;
		$acumSub = 0;
		$acumTotal = 0;
		$acumAbonos = 0;
		$acumNotas = 0;
		$acumSaldos = 0;

		$paramGrupos = parametroIN($txGrupos);

		//echo "<a href='RepFacGrupo.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."&wsede=".$wsede."&wgrupo=".$wgrupo."&wsubgrupo=".$wsubgrupo.">VOLVER</a>";
		if($num > 0)
		{

			echo "<br><br><div align='center'><input type='submit' value=' Retornar '></div><br>";
			echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";

			//Variables de control
			$cdFactura = "";
			$cdGru = "";
			$auxNombreCco = "";
			$auxNombreGru = "";

			$row = mysql_fetch_array($err);

			$cont1 = 1;

			echo "<br><br><table border=0 align=center>";

			while($cont1 <= $num){

				if (is_integer($cont1/2))
				  $wclass="fila1";
				else
				  $wclass="fila2";

				//Muestra el titulo de cada columna
				if($cont1 == 1){
					echo "<tr class='encabezadoTabla'>";
					echo "<td align=center><b>FACTURA</b></td>";
                    echo "<td align=center><b>FECHA FACTURA</b></td>";
                    //campos agragados el 24 de septiempre
                    echo "<td align=center><b>NRO ORDEN</b></td>";
                    echo "<td align=center><b>FECHA ORDEN</b></td>";
					echo "<td align=center><b>FECHA<br>RECEPCI&Oacute;N</b></td>";
                    //
                    echo "<td align=center width='20%'><b>PACIENTE</b></td>";
					echo "<td align=center><b>TELEFONO</b></td>";
					echo "<td align=center><b>CAJA</b></td>";
					echo "<td align=center><b>MEDICO</b></td>";
					echo "<td align=center><b>GRUPO</b></td>";
					echo "<td align=center><b>CANTIDAD</b></td>";
					echo "<td align=center><b>SUBTOTAL</b></td>";
					echo "<td align=center><b>TOTAL FACTURA</b></td>";
					echo "<td align=center><b>ABONOS</b></td>";
					echo "<td align=center><b>NOTA CREDITO</b></td>";
					echo "<td align=center><b>SALDO</b></td>";
					echo "<td align=center><b>FECHA ENTREGA</b></td>";
					echo "<td align=center><b>VENDEDOR LENTE</b></td>";
					echo "<td align=center><b>VENDEDOR MONTURA</b></td>";
					//DEBUG
//					echo "<td align=center class=".$wclass."><b>ARTICULO</b></td>";
					echo "</tr>";
				}

				echo "<tr>";

				if($cdFactura != $row[0]){

                    $nombre = str_replace( $caracteres, $caracteres2, $row[1] );
					$qmed = "SELECT Vmpmed
							   FROM ".$wbasedato."_000050
							  WHERE Vmpvta = '".$row[2]."'";
					$resmed = mysql_query( $qmed, $conex ) or die( mysql_errno()." - Error en el query $qmed -".mysql_error() );
					$rowmed = mysql_fetch_array($resmed);

                    $telefono = str_replace(" ", "<br>", trim($row['telefono']));
                    $telefono = str_replace("-", "<br>", $telefono);
					echo "<td align=left class=".$wclass.">".$row[0]."</td>";
                    echo "<td align=left class=".$wclass.">".$row['Fecha_factura']."</td>";
                    //campos agregados el 24 de septiembre
                    echo "<td align=left class=".$wclass.">".$row['numeroOrden']."</td>";
                    echo "<td align=left class=".$wclass.">".$row['fechaOrden']."</td>";
					echo "<td align=left class=".$wclass.">".$row['fechaRecepcion']."</td>";
                    //
                    echo "<td align=left class=".$wclass.">".$nombre."</td>";
					echo "<td align=left class=".$wclass.">".$telefono."</td>";
					echo "<td align=left class=".$wclass.">".$row[13]."</td>";
					echo "<td align=left class=".$wclass.">".$rowmed[0]."</td>";

					$nroFacturas++;
				} else {
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
                    echo "<td align=left class=".$wclass.">&nbsp;</td>";
                    //agregados 24 de septiembre
                    echo "<td align=left class=".$wclass.">&nbsp;</td>";
                    echo "<td align=left class=".$wclass.">&nbsp;</td>";
                    echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
				}

				echo "<td align=left class=".$wclass.">".$row[3]."</td>";
				echo "<td align=left class=".$wclass.">".$row[4]."</td>";
				echo "<td align=left class=".$wclass.">".number_format($row[5],0,'.',',')."</td>";

				if($cdFactura != $row[0]){
					echo "<td align=left class=".$wclass.">".number_format($row[6],0,'.',',')."</td>";
					echo "<td align=left class=".$wclass.">".number_format($row[7],0,'.',',')."</td>";
					echo "<td align=left class=".$wclass.">".number_format($row[8],0,'.',',')."</td>";
					echo "<td align=left class=".$wclass.">".number_format($row[9],0,'.',',')."</td>";
					$acumTotal += $row[6];
					$acumAbonos += $row[7];
					$acumNotas += $row[8];
					$acumSaldos += $row[9];
				}else {
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
					echo "<td align=left class=".$wclass.">&nbsp;</td>";
				}

				$vendedor_lente = "";
				if(isset($row['Fdecon']) && ($row['Fdecon']=='LO' || $row['Fdecon']=='LE'))
					if(isset($row['ordvel']) && $row['ordvel']!="")
						$vendedor_lente = $row['ordvel'];

				$vendedor_montura = "";
				if($wtipofecha == 'E')
				{
					if(isset($row['Fdecon']) && ($row['Fdecon']=='LO' || $row['Fdecon']=='LE' || $row['Fdecon']=='MT'))
						if(isset($row['ordvem']) && $row['ordvem']!="")
							$vendedor_montura = $row['ordvem'];
				}
				else
				{
					if(isset($row['Fdecon']) && $row['Fdecon']=='MT')
						if(isset($row['ordvem']) && $row['ordvem']!="")
							$vendedor_montura = $row['ordvem'];
				}

				echo "<td align=left class=".$wclass.">".$row[10]."</td>";
				echo "<td align=left class=".$wclass.">".$vendedor_lente."</td>";
				echo "<td align=left class=".$wclass.">".$vendedor_montura."</td>";
//				echo "<td align=left class=".$wclass.">".strtoupper($row[13])."</td>";
				echo "</tr>";

				//Acumuladores de valores totales
				$acumSub += $row[5];

				$cdFactura = $row[0];

				$row = mysql_fetch_array($err);
				$cont1++;

			}
			echo "<tr>";
			echo "<td align=center class='encabezadoTabla' colspan='10'><b>Facturas encontradas: ".$nroFacturas."</b></td>";
			echo "<td align=center class='encabezadoTabla'><b>".number_format($acumSub,0,'.',',')."</b></td>";
			echo "<td align=center class='encabezadoTabla'><b>".number_format($acumTotal,0,'.',',')."</b></td>";
			echo "<td align=center class='encabezadoTabla'><b>".number_format($acumAbonos,0,'.',',')."</b></td>";
			echo "<td align=center class='encabezadoTabla'><b>".number_format($acumNotas,0,'.',',')."</b></td>";
			echo "<td align=center class='encabezadoTabla'><b>".number_format($acumSaldos,0,'.',',')."</b></td>";
			echo "<td align=center class='encabezadoTabla' colspan=4><b>&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";

			//echo "<br><br><div align='center'><a href='RepFacGrupo.php?wemp_pmla=".$wemp_pmla."&wfecini=".$wfecini."&wfecfin=".$wfecfin."&wsede=".$wsede."&wgrupo=".$wgrupo."&wsubgrupo=".$wsubgrupo."><center>VOLVER</center></a></div><br>";
		}
		else
		{
			echo "<br><br><table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><b>No se encontraron documentos con los criterios especificados</td><tr></table><br>";
		}
		echo "<br><div align='center'><input type='submit' value=' Retornar '></div><br>";
		echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  	}
}
liberarConexionBD($conex);
?>
</html>