<?php
include_once("conex.php");

/*********************************************************************************************************
 * Programa:		impresionNotas.php
 * Por:				Edwin Molna Grisales
 * Descripcion:		Imprime las notas de remision y contraremision de un paciente
 * 
 * variables:
 * 
 * $txHis:	Parametro de busqueda por historia
 * $txDoc:	Parametro de busqueda por Nro de Documento
 * $txNom:	Parametro de busqueda por Nombre
 * $his:		Historia del paciente
 * $fecha:	Fecha para imprimir la nota
 * $nota:	Nota a imprimir de la fecha
 *********************************************************************************************************/

/*********************************************************************************************************
 * 											  FUNCIONES
 *********************************************************************************************************/


/**
 * Consultar notas
 * 
 * @param $his		Historia
 * @param $fecha	Fecha
 * @return unknown_type
 */
function consultarNota( $his, $ing, $fecha ){
	
	global $conex;
	global $wbasedato;
	
	$nota = '';
	
	$sql = "SELECT
				nrcnot
			FROM
				{$wbasedato}_000014
			WHERE
				nrchis = '$his'
				AND nrcing = '$ing'
				AND nrcfec = '$fecha'
				AND nrcest = 'on'
				AND nrcnot != '.'				
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		 $nota = $rows['nrcnot'];
	}
	
	return $nota;
	
}

/*********************************************************************************************************
 *	 									  FIN DE FUNCIONES
 *********************************************************************************************************/

/*********************************************************************************************************
 * 										INCIO DEL PROGRAMA
 *********************************************************************************************************/

include_once("root/comun.php");

include_once( "../../consultorios/procesos/funcionesGenerales.php" );

$conex = obtenerConexionBD("matrix");

$key = substr($user, 2, strlen($user));

$infoMedico = new classMedico( $doc );

$wbasedato = $infoMedico->bdHC;
$wbasecitas = $infoMedico->bdCitas;

/*
 * Si no existe historia se crea un buscador para encontrar el paciente con todas 
 * las posibles notas creadas a la fecha
 */

if( !isset($his ) ){
	
	echo "<form>";
	
	echo "<INPUT type='hidden' name='doc' value='$doc'>";
	
	$doctorName = $infoMedico->nombre;
	$titulo = "IMPRESION DE NOTAS DE REMISION Y CONTRAREMISION DR. ".strtoupper( $doctorName );
	encabezado( $titulo, "2010-01-13", "logo_".$wbasedato );
	
	//Seteando txHis
	if( !isset( $txHis ) ){
		$txHis = '';
	}
	
	if( !isset( $txDoc ) ){
		$txDoc = '';
	}
	
	if( !isset( $txNom ) ){
		$txNom = '';
	}
	
	if( !isset( $txFecha ) ){
		$txFecha = date("Y-m-d");
	}
	
	echo "<table align='center'>";
	echo "<tr class='encabezadotabla' align='center'>";
	echo "<td colspan='2'>Paramétros de Busqueda</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' style='width:100'>Historia</td>";
	echo "<td class='fila2' style='width:100'><INPUT type='text' name='txHis' style='width:100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' style='width:150'>Nro de documento</td>";
	echo "<td class='fila2' style='width:100'><INPUT type='text' name='txDoc' style='width:100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' style='width:100'>Nombre</td>";
	echo "<td class='fila2' style='width:100'><INPUT type='text' name='txNom' style='width:100%'></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align='center' colspan='2'>";
	echo "<br><INPUT type='submit' style='width:100' value='Enviar'>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	$sql = "SELECT
				pachis as his,
				pacnid as doc,
				nrcfec as fec,
				pacnpa as nom,
				nrcnot
			FROM
				{$wbasedato}_000002 b, {$wbasedato}_000014 c 
			WHERE
				pachis like '%$txHis%'
				AND pachis =  nrchis
				AND pacnpa like '%$txNom%'
				AND pacnid like '%$txDoc%'
				AND nrcfec like '%$txFecha%'
				AND nrcest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
	$numrows = mysql_num_rows( $res );
	
	if( $numrows > 0 ){
		
		echo "<br><br>";
		echo "<table align='center'>";
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td style='width:150'>Historia</td>";
		echo "<td style='width:300'>Nombre</td>";
		echo "<td style='width:150'>Nro de Documento</td>";
		echo "<td style='width:150'>Fecha de la Nota de Remision y contraremision</td>";
		echo "<td style='width:150'>Impresion</td>";
		echo "</tr>";
		
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$fila = "class='fila".($i%2+1)."'";
			
			echo "<tr $fila>";
			echo "<td align='center'>{$rows['his']}</td>";
			echo "<td>{$rows['nom']}</td>";
			echo "<td>{$rows['doc']}</td>";
			echo "<td align='center'>{$rows['fec']}</td>";
			echo "<td align='center'><a target='_blank' href='impresionNotas.php?his={$rows['his']}&fecha={$rows['fec']}&doc=$doc'>Imprimir</a></td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}
	
	echo "</form>";
}
else{
	
	/**
	 * Si no existe una nota enviada se busca la Nota de remision y contraremision de acuerdo
	 * para la fecha dada, si no hay fecha dada se asume que es la fecha actual
	 */
	
	if( !isset($nota) ){
		
		if( !isset($fecha) || empty($fecha) ){
			$fecha = date("Y-m-d");			
		}
		
		$nota = consultarNota( $his, $ing, $fecha );
	}
	
	if( $infoMedico->codigo != '' ){
		
		if( !empty( $nota ) ){
	
			//Genernado formato de impresion
	
			//Encabezado de la impresion
			
			echo "<div class='centrar5' id='dvNotas'>";
			
			echo "Medellín, ".date( "d" )." de ".nombreMes( date( "n" ) )." de ".date( "Y" );
			echo "<br><br>";
			
			echo "<center><b><H3>NOTAS DE REMISION Y CONTRAREMISION</b></center></H3><br><br>";
	
			echo str_replace( "\n", "<br>", htmlentities( trim($nota) ) );
			
			echo "<br><br>";
			
			echo "Atentamente, ";
			echo "<br><br><br><br>";
			echo $infoMedico->nombre;
			echo "<br>C.C. ";
			echo number_format( $infoMedico->nroIdentificacion, 0, '', '.' );
			echo "<br>R.M.: ";
			echo $infoMedico->registro;
			echo "<br>";
			
			echo "</div>";
		}

	}
	else{
		echo "<center><b>EL DOCTOR NO SE ENCUENTRA REGISTRADO EN LA BASE DE DATOS</b></center>";
	}
}
?>