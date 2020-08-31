<html>
<head>
  <title>MATRIX</title>
</head>

<script>
	selected = 0;
	idxCelda = 4;

	function mostrarCargas( campo ){

		if( selected != 0 ){
			campo.parentNode.parentNode.parentNode.rows[ selected ].cells[ idxCelda ].firstChild.checked = false;
			campo.parentNode.parentNode.parentNode.rows[ selected+1 ].style.display = 'none';
			selected = 0;

			var tb = campo.parentNode.parentNode.parentNode;

			for( var i = 1; i < tb.rows.length; i = i+2 ){
				tb.rows[ i ].style.display = '';
			}
		}

		if( campo.checked == true ){
			document.getElementById( 'dvCargar' ).style.display = '';
			selected = campo.parentNode.parentNode.rowIndex;

//			var auxColSpan = campo.parentNode.parentNode.parentNode.rows[ selected+1 ].cells[0].colSpan;
//			try{
				campo.parentNode.parentNode.parentNode.rows[ selected+1 ].cells[0].colSpan = '1';
//			} catch(e){}
			campo.parentNode.parentNode.parentNode.rows[ selected+1 ].style.display = '';
			campo.parentNode.parentNode.parentNode.rows[ selected+1 ].cells[0].colSpan = '5';

			var tb = campo.parentNode.parentNode.parentNode;

			for( var i = 1; i < tb.rows.length; i++ ){
				if( i != selected ){
					tb.rows[ i ].style.display = 'none';
				}
				else{
					i++;
				}
			}
		}
		else{
			document.getElementById( 'dvCargar' ).style.display = 'none';
		}
		
	}
</script>

<BODY TEXT="#000066">


<?php
include_once("conex.php");

/**
 * Busca si un examen tien archivos o no
 * 
 * @return unknown_type
 */
function tieneArchivos( $his, $ing, $fec, $hor, $exa ){

	global $wbasedato;
	global $conex;
	
	//Actualizando el examen a PENDIENTE
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000031
			WHERE
				exahis = '$his'
				AND exaing = '$ing'
				AND exafec = '$fec'
				AND exahor = '$hor'
				AND exacod = '$exa'
			";
				
	$res = mysql_query( $sql, $conex ) or die ( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_num_rows( $res ) > 0 ){
//		return true;
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			echo "<a href='../../images/medical/{$wbasedato}/{$rows['Exaarc']}' target='_blank'>Archivo $i</a><br>";
		}
	}
	else{
		return false;	
	}
}

/*******************************************************************************************************************
 *			 										FUNCIONES
 *******************************************************************************************************************/

function mostrarDetallesExamen( $his, $ing, $fecha, $hora, $codexa ){
	
	global $wbasedato;
	global $conex;
	
	$encabezado = Array();
	$fechaHoraExamen = Array();
	$datos = Array();
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000021 a, {$wbasedato}_000022 b, {$wbasedato}_000001 c, {$wbasedato}_000019 d, {$wbasedato}_000020 e
			WHERE
				detexa = '$codexa'
				AND dethex = exahis
				AND detiex = exaing
				AND dethrx = exahor
				AND detfex = exafec
				AND detexa = a.exacod
				AND dethis = '$his'
				AND exaact = 'on'
				AND a.exaest like '01-%'
				AND dethis = hclhis
				AND deting = hcling
				AND d.exacod = detexa
				AND detest = 'on'
				AND parcod = detpar
				AND parexa = detexa
				AND exahis = '$his'
				AND exaing = '$ing'
				AND exafec = '$fecha'
				AND exahor = '$hora'
			GROUP BY 
				 dethex, detiex, detfex, dethrx, detexa, detpar
			";
	
	$res = mysql_query( $sql, $conex ) or die ( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			@$encabezado[ $rows['Parnom'] ] = '';
			@$fechaHoraExamen[ $rows['Exafec'].", ".$rows['Exahor'] ] = '';
			@$datos[ $rows['Parcod'] ][ $rows['Exafec'].", ".$rows['Exahor'] ] = $rows['Detval']; 
			@$datosIng[ $rows['Exafec'].", ".$rows['Exahor'] ] = $rows['Deting'];
		}
		
		//Creando el encabezado de la tabla
		echo "<table align='center' style='font-size:10pt'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Fecha, Hora<br>del examen</td>";
		
		foreach( $encabezado as $keyEncabezado => $valorEncabezado ){
			echo "<td>";
			echo $keyEncabezado;
			echo "</td>";
		}
		echo "<td>";
		echo "Ver archivos";
		echo "</td>";
		
		echo "</tr>";
		
		foreach( $fechaHoraExamen as $keyExamen => $valorExamen ){
			echo "<tr>";
			
			echo "<td>$keyExamen</td>";
			
			foreach( $datos as $keyDatos => $valorDatos ){
				echo "<td>".str_replace( "\n","<br>", htmlentities( substr( $valorDatos[ $keyExamen ], 0 ) ) )."</td>";
			}
			
			//Mostrando los archivos adjuntos
			$exp = explode( ",", $keyExamen );
			echo "<td>";
			if( tieneArchivos( $his, $datosIng[ $keyExamen ], $exp[0], trim( $exp[1] ), $codexa ) ){
//				echo "<td align='center'>";
//				echo "<INPUT type='button' value='ver' onClick=''>";
//				echo "</td>";
			}
			else{
//				echo "<td>";
//				echo "</td>";
			}
			echo "</td>";	
					
			echo "</tr>";
		}
		
		echo "</table>";
	}
	else{
		echo "<center><b>SIN DETALLE PARA EL EXAMEN</b></center>";
	}
}


function buscarPacintes(){

	global $conex;
	global $wbasedato;
	
	echo "<table align='center'>";
	echo "<tr class='fila1'>";
	echo "<td>Historia</td>";
	echo "<td><INPUT type='text' name='his' id='historia'></td>";
	echo "</tr>";
	echo "<tr class='fila1'>";
	echo "<td>Cedula</td>";
	echo "<td><INPUT type='text' name='ced' id='cedula'></td>";
	echo "</tr>";
	echo "<tr class='fila1'>";
	echo "<td>Nombres</td>";
	echo "<td><INPUT type='text' name='nombre' id='nombre'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td><br></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='2' align='center'><INPUT type='submit' value='Buscar'></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<br><br>";
}

/**
 * 
 */
function buscarExamenesHistoriaIngreso( $nombre, $ced, $his, $ing ){
	
	global $conex;
	global $wbasedato;
	
	$val = false;
	
	if( empty($nombre) && empty($ced) && empty($his) ){
		return;
	}
	
	$sql = "SELECT
				*, a.id as aid
			FROM
				{$wbasedato}_000021 a, {$wbasedato}_000019 b, {$wbasedato}_000002 c 
			WHERE
				a.exaest like '01-%'
				AND a.exaact = 'on'
				AND a.exacod = b.exacod
				AND a.exahis = c.pachis
				AND c.pachis like '%$his%'
				AND c.pacnid like '%$ced%'
				AND c.pacnpa like '%$nombre%'
				AND a.exaing like '%'
			ORDER BY
				a.exahis desc, a.exaing desc 
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		$val = true;
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td style='width:100'>";
		echo "Historia";
		echo "</td>";
		echo "<td>";
		echo "Nombre";
		echo "</td>";
		echo "<td style='width:100'>";
		echo "Fecha";
		echo "</td>";
		echo "<td>";
		echo "Examen";
		echo "</td>";
		echo "<td>";
		echo "Seleccionar";
		echo "</td>";
		echo "</tr>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$fila = "class='fila".(($i%2)+1)."'";
			
			echo "<tr $fila>";
			echo "<td align='center'>";
			echo $rows['Exahis']."-".$rows['Exaing'];
			echo "</td>";
			echo "<td>";
			echo $rows['Pacnpa'];
			echo "</td>";
			echo "<td align='center'>";
			echo $rows['Exafec'];
			echo "</td>";
			echo "<td>";
			echo $rows['Exanom'];
			echo "</td>";
			echo "<td align='center'>";
			echo "<INPUT type='checkbox' name='tdSeleccionado' value='{$rows['aid']}' onClick='javascript: mostrarCargas( this )'>";
			echo "</td>";
			echo "</tr>";
			
			echo "<tr  style='display:none'><td colspan='5' align='center'>";
//			echo "<div>1111";
			mostrarDetallesExamen( $rows['Exahis'], $rows['Exaing'], $rows['Exafec'], $rows['Exahor'], $rows['Exacod'] );
//			echo "</div>"; 
			echo "</td></tr>";
			
		}
		
		echo "</table>";
		
		echo "<br><br>";
	}
	
	return $val;
}


/**
 * Busca los datos de encabezado de un examen pór ID
 * 
 * @param $id
 * @return unknown_type
 */
function buscarExamen( $id ){
	
	global $conex;
	global $wbasedato;
	
	$rows = Array();
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000021
			WHERE
				id = '$id'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		return $rows;	
	}
	
	return $rows;
}

/**
 * 
 * @param $ruta
 * @param $archivo
 * @param $id
 * @return unknown_type
 */
function grabarRutaArchivo( $ruta, $archivo, $id ){
	
	global $conex;
	global $wbasedato;
	
	$fecha = date( "Y-m-d" );
	$hora = date( "H:i:s" );
	
	$infoExamen = buscarExamen( $id );
	
	$his = $infoExamen['Exahis'];
	$ing = $infoExamen['Exaing'];
	$fec = $infoExamen['Exafec'];
	$hor = $infoExamen['Exahor'];
	$cod = $infoExamen['Exacod'];
	$rut = $ruta;
	
	$exp = explode( ".", $archivo );
	
	$archivo = "$his-$ing-$cod-$fecha-".date("His").".".$exp[ count($exp)-1 ];
	
	$arc = $archivo;
	
	$sql = "INSERT INTO {$wbasedato}_000031(    medico   , fecha_data, hora_data,   seguridad   , exahis, exaing, exafec, exahor, exacod, exarut, exaarc, exaest )
							         VALUES( '$wbasedato',  '$fecha' ,  '$hora' , 'C-$wbasedato', '$his', '$ing', '$fec', '$hor', '$cod', '$rut', '$arc',  'on'  )";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		return true;
	}
	else{
		return false;
	}
	
}
/*******************************************************************************************************************
 *			 										FIN DE FUNCIONES
 *******************************************************************************************************************/

/*******************************************************************************************************************
 *			 										INICIO DEL PROGRAMA
 *******************************************************************************************************************/
session_start();
if(!isset($_SESSION['user'])){
	echo "error";
}
else
{
	$grupo = $wbasedato;
//	$wbasedato = 'oncologo';
	
	if(!isset($key))
		$key = substr($user,2,strlen($user));
	
	include_once("root/comun.php");
	
	

	
//	$conex = obtenerConexionBD( "matrix" );

	encabezado( "PUBLICACION DE EXAMENES", "2010-09-27", "fmatrix" );
	
	echo "<form action='publicarExamenes.php' enctype='multipart/form-data' method=post>";
	echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	echo "<INPUT type='hidden' name='wbasedato' value='$wbasedato'>";
	
	if( !isset($files) || !isset( $tdSeleccionado ) || $tdSeleccionado <= 0 ){
		
		buscarPacintes();
		$bsResulados = buscarExamenesHistoriaIngreso( @$nombre, @$ced, @$his, @$ing );
		
		if( $bsResulados ){
			echo "<div id='dvCargar' style='display:none'>";
			echo "<center><table border=0>";
//			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
//			echo "<tr><td align=center colspan=2>PUBLICACION DE ARCHIVOS EN LA INTRANET</td></tr>";
			echo "<tr><td class='fila1' align=center>Nombre del Archivo</td>";
			echo "<td class='fila1'><input type='file' name='files'  size=60 maxlength=60 /></td></tr>";

			if($key != "root"){
				echo "<tr style='display:none'><td class='fila1' align=center colspan=2><INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1> Archivo Plano Publico<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> Archivo Plano Privado<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 3 checked> Imagen";
			}
			echo "<tr><td class='fila1' colspan=2 align=center><input type='submit' value='Publicar'></td></tr></table>";
			echo "<input type='HIDDEN' name= 'grupo' value='".$grupo."'>";
			echo "</div>";
		}
	}
	else
	{
		$real= $_FILES['files']['name'];
		$files=$_FILES['files']['tmp_name'];
	
		// $real= $HTTP_POST_FILES['files']['name'];
		// $files=$HTTP_POST_FILES['files']['tmp_name'];
		
		if($key != "root"){
			switch ($radio1)
			{
				case 3:
					// $ruta="C:/Inetpub/wwwroot/matrix/images/medical/".$grupo."/";
					$ruta="/var/www/matrix/images/medical/".$grupo."/";
//					$ruta="C:/emolina/".$grupo."/";
					$dh=opendir($ruta);
					
					if( !empty( $real ) ){
						grabarRutaArchivo( $ruta, &$real, $tdSeleccionado );
					}
					
					if(readdir($dh) == false){
						mkdir($ruta,0777);
					}
				break;
			}
		}
		if( !isset($ruta) or @!copy($files, $ruta.$real) ) 
		{
    		echo "<center><b>ERROR LA COPIA NO PUDO HACERSE<br><br></b></center>";
    		echo "<center><INPUT type='submit' value='Retornar'></center>";
		}
		else
		{
			echo "<table border=0 align=center>";
			echo "<tr><td align=center class='fila1'>LA PUBLICACION EXITOSA</td></tr>";
			echo "<tr><td align=center class='fila1'>ARCHIVO: <B>".$real."</B></td></tr>";
			echo "<tr><td align=center class='fila1'>RUTA :<B>".$ruta."</B></td>";
			echo "<tr><td align='center'><INPUT type='submit' value='Retornar'></td></tr>"; 
			echo "</tr></table>";
		}
	}
}
?>
</body>
</html>