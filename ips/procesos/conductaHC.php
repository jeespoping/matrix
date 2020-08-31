<?php
include_once("conex.php");

/********************************************************************************************************
 * 												FUNCIONES
 *******************************************************************************************************/

/**
 * Da al paciente en alta y graba los registros en la tabla 000142
 * 
 * @param $his
 * @param $ing
 * @param $pro
 * @return unknown_type
 */
function darAlta( $his, $ing, $pro ){
	
	global $conex;
	global $wbasedato;
	global $key;
	
	$val = false;
	
	//Colocando la historia en Alta en la historia clinica
	$sql = "UPDATE
				{$wbasedato}_000139
			SET
				hclrem = '01-ALTA'
			WHERE
				hclhis like '$his'
				AND hcling like '$ing'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	//Actualizar alta en tabla 141
	$sql = "UPDATE
				{$wbasedato}_000141 a, {$wbasedato}_000139 b, {$wbasedato}_000051 c
			SET
				espalt = 'on'
			WHERE
				hclhis = '$his'
				AND hcling = '$ing'
				AND medcod = SUBSTRING_INDEX( hclmed, '-', 1 )
				AND medusu = espmtr
				AND esphis = hclhis
				AND esping = hcling
				AND espalt != 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	//Ingresando los datos necesarios en la tabla POSTCONSULTA
	$sql = "INSERT INTO {$wbasedato}_000142 
						(     medico    ,      fecha_data    ,      hora_data     , poshis,  posing, pospro,     posfec         ,       poshor       ,  posdes   ,    Seguridad  )
				VALUES  ( '{$wbasedato}', '".date("Y-m-d")."', '".date("H:i:s")."', '$his',  '$ing', '$key', '".date("Y-m-d")."', '".date("H:i:s")."', '01-ALTA' , '{$wbasedato}' )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
}

/********************************************************************************************************
 * 											FIN DE FUNCIONES
 *******************************************************************************************************/


if( isset($consultaAjax) ){

	echo "Hola de ajax..... ";
	
	include_once("root/comun.php");

	if( !isset($wemp_pmla) ){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$key = substr($user, 2, strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;
	
	switch( $consultaAjax ){
		case 6:
			darAlta( $his, $ing, $key ); 
			break;
			
		default: break;
	}
	
}
else{
?>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery-ui-1.7.2.custom.js"></script> 	<!-- Nucleo jquery -->
<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script> <!-- Block UI -->
<script type="text/javascript" src="../../../include/root/ui.core.js"></script>	<!-- Nucleo jquery -->
<script>

/**
 *	Genera la tabla principal que se mostrará en el programa
 */
function darAlta(){
	
	var parametros = "";
	
	parametros = "consultaAjax=6&wemp_pmla="+document.forms[0].elements[ 'wemp_pmla' ].value
				 +"&his="+document.forms[0].elements[ 'hiHis' ].value
				 +"&ing="+document.forms[0].elements[ 'hiIng' ].value;

	try{
		$.blockUI({ message: $('#msjEspere') });
		ajax=nuevoAjax();
		
		ajax.open("POST", "./conductaHC.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function() 
		{
			if (ajax.readyState==4 && ajax.status==200)
			{ 
//				var col = window.document.getElementById( 'dvPrincipal' );
//				alert( ajax.responseText );
			}
			$.unblockUI();
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
	}catch(e){	}

	pAlta.style.display="none";
	pMsgAlta.style.display="block";
}

</script>

<?php

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include_once("root/comun.php");

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

encabezado("SALA DE OBSERVACION", "2010-02-05", "logo_".$wbasedato );

if( !isset($his) ){
	
	echo "<form method='post'>";
	
	echo "<input type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	
	$sql = "SELECT
				hclfec, 
				hclhor, 
				hclmce, 
				hclapf, 
				hclafa, 
				hclefi, 
				hclcon, 
				hclcie, 
				hclacl, 
				hclmed, 
				hclnom, 
				hclhis, 
				hcldoc, 
				hcling
			FROM
				{$wbasedato}_000139
			WHERE
				hclhis like '%'
				AND hcldoc like '%'
				AND hclnom like '%'
				AND hclrem like '04-%'
				AND hclhis IN (SELECT pachis FROM {$wbasedato}_000100 WHERE pachis = hclhis AND pacact = 'on' )
				AND hcling IN (SELECT MAX(ingnin+0) FROM {$wbasedato}_000101 WHERE inghis = hclhis GROUP BY inghis )
			GROUP BY hclhis
			ORDER BY hclfec desc
		   ";

		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
		$num = mysql_num_rows( $res );

	if( $num > 0 ){
	
		echo "<br><br><CENTER><b>PACIENTES EN SALA DE OBSERVACION</b></CENTER>";
		
		echo "<br><br>";
			echo "<table align='center'>";
			echo "<tr class='encabezadotabla' align='center'>";
			echo "<td width='100'>Historia</td>";
			echo "<td width='100'>Documento</td>";
			echo "<td width='300'>Nombre</td>";
			echo "<td width='300'>Medico Tratante</td>";
			echo "<td width='150'>Enlace</td>";
			echo "</tr>";

		for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
			
			$class = "class='fila".(($i%2)+1)."'";
				
			echo "<tr $class>";
			echo "<td align='right'>{$rows['hclhis']}</td>";
			echo "<td align='right'>{$rows['hcldoc']}</td>";
			echo "<td>{$rows['hclnom']}</td>";
			echo "<td>{$rows['hclmed']}</td>";
			echo "<td><a href='conductaHC.php?wemp_pmla=$wemp_pmla&his={$rows['hclhis']}' target='blank'>Ver conducta</a></td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}
	else{
		echo "<br><br><CENTER><b>SIN PACIENTES EN SALA DE OBSERVACION</b></CENTER>";
	}
	
	echo "<br><br><CENTER><INPUT TYPE='button' value='Cerrar' onclick='javascript:cerrarVentana();' style='width:100'></CENTER>";
	
	echo "<meta name='met' id='met' http-equiv='refresh' content='60;url=conductaHC.php?wemp_pmla=$wemp_pmla'>";

	echo "</form>";
	
}
else{
	
	echo "<form>";
	
	$sql = "SELECT
				hclcon, hclhis, hcling, hclnom, hcldoc
			FROM
				{$wbasedato}_000139
			WHERE
				hclhis = '$his'
				AND hcling IN (SELECT MAX(ingnin+0) FROM {$wbasedato}_000101 WHERE inghis = hclhis GROUP BY inghis )
				AND hclrem like '04-%'
			GROUP BY hclhis
			ORDER BY hcling DESC
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query - ".mysql_error() );
	$num = mysql_num_rows( $res );
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			@$ing = $rows['hcling'];
			
			echo "<table align=center>";
			
			echo "<tr>";		
			echo "<td class='encabezadotabla' width=120>Historia:</td>";
			echo "<td class='fila1'>{$rows['hclhis']}-{$rows['hcling']}</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class='encabezadotabla'>Documento:</td>";
			echo "<td class='fila1'>{$rows['hcldoc']}</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td class='encabezadotabla'>Nombre:</td>";
			echo "<td class='fila1'>{$rows['hclnom']}</td>";
			echo "</tr>";
			
			echo "</table>";
			
//		echo "<tr>";
//		echo "<td colspan='2'><br></td>";
//		echo "</tr>";
			
			echo "<br><table align=center>";
			
			echo "<tr>";
			echo "<td colspan='2' class='encabezadotabla'>Condcuta:</td>";
			echo "</tr>";
			
			echo "<tr>";
			echo "<td colspan=2 class='fila2'>".str_replace("\n","<br>",$rows['hclcon'])."</td>";
			echo "</tr>";
			echo "</table>";
			
			echo "<INPUT TYPE='hidden' name='hiHis' value='{$rows['hclhis']}'>";
			echo "<INPUT TYPE='hidden' name='hiIng' value='{$rows['hcling']}'>";
			
		}
		
		echo "<br><br><table align='center' style='width:500'><tr><td>";
		echo "<p align='right' id='pAlta'>Dar al paciente de alta: <INPUT type='checkbox' name='cbAlta' onclick='javascript: darAlta();'></p>";
		echo "</tr></td></table>";
		
		echo "<br><br><CENTER><INPUT TYPE='button' value='Cerrar' onclick='javascript:cerrarVentana();' style='width:100'></CENTER>";
		
		echo "<br><CENTER><a href='../Reportes/impresionHC.php?wemp_pmla=$wemp_pmla&his=$his&ing=$ing&enf=off' target='new'>Ver Historia</a></CENTER>";
		
		
		
		echo "<br><br><br>";
		echo "<h1><p align='center' id='pMsgAlta' style='display:none;color:red'>EL PACIENTE HA SIDO DADO DE ALTA</p></h1>";
		
		
		echo "<INPUT TYPE='hidden' name='wemp_pmla' value='$wemp_pmla'>";
		
		//Mensaje de espera
		echo "<div id='msjEspere' style='display:none;'>"; 
	    echo "<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento..."; 
		echo "</div>";
	
	}
	else{
		echo "<CENTER><b>EL PACIENTE NO SE ENCUENTRA EN SALA DE OBSERVACION</b></CENTER>";
		
		echo "<br><br><CENTER><INPUT TYPE='button' value='Cerrar' onclick='javascript:cerrarVentana();' style='width:100'></CENTER>";
	}
	
	echo "</form>";
	
}

}
?>
