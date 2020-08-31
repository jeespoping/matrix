<?php
include_once("conex.php");

/*********************************************************************************
 * Por:	Edwin Molina Grisales
 * Requerimiento 2135 - Dicembre 06 de 2010
 * 
 * Descripción: Mostrar los pacientes admitidos a la institución dado una fecha
 * 				y un responsable
 * 
 ********************************************************************************/

include_once("root/comun.php");

if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

$wactualiz = "Diciembre 6 de 2010";
encabezado("REPORTE DE INGRESO DE PACIENTES POR RESPONSABLE", $wactualiz, "clinica");

echo "<form>";

//El usuario se encuentra registrado
if( !isset($_SESSION['user']) ){
	echo "Error: Usuario No registrado";
}
elseif( !isset($menu) || $menu == 'on' ){
	
	if( !isset( $fecha) ){
		$fecha = date( "Y-m-d" );
	}
	
	echo "<center><b>INGRESE LOS PARAMETROS DE CONSULTA</b></center>";
	
	echo "<br><br>";
	echo "<table align='center'>";
	
	echo "<tr>";
	echo "<td class='encabezadotabla'>Responsable: </td>";
	echo "<td class='fila1'>";
	
	
	//consultando el codigo de todos los responsables
	$sql = "SELECT
				empcod, empnit, empres, empnom
			FROM
				{$wbasedato}_000024
			WHERE
				empres=empcod
			ORDER BY
				empnom asc
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	echo "<select name='codigoResponsable'>";
	
	for( $i = 0; $rows = mysql_fetch_array($res); $i++ ){
		echo "<option>{$rows['empres']} - {$rows['empnom']}</option>";
	}
	
	echo "</select>";
	
	echo"</td>";
	echo "</tr>";
	echo "<tr>";
	
	echo "<tr>";
	echo "<td class='encabezadotabla'>Fecha Inicial</td>";
	echo "<td class='fila1'>";
	campoFechaDefecto( "fecha", $fecha );
	echo"</td>";
	echo "</tr>";
	echo "<tr>";
	
	echo "</table>";
	
	echo "<INPUT type='hidden' name='menu' value='off'>";
	echo "<INPUT type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	echo "<br><br>";
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td><INPUT type='submit' value='Generar' style='width:100'></td>";
	echo "<td><INPUT type='button' onClick='javascript: cerrarVentana();' value='Cerrar' style='width:100'></td>";
	echo "</tr>";
	echo "</table>";
}
else{
	
	echo "<center><b>PARAMETROS DE BUSQUEDA</b></center>";
	echo "<br>";
	echo "<table align='center'>";
	
	echo "<tr>";
	echo "<td class='encabezadotabla'>Responsable: </td>";
	echo "<td class='fila1'>$codigoResponsable</td>";
	echo "</tr>";
	echo "<tr>";
	
	echo "<tr>";
	echo "<td class='encabezadotabla'>Fecha Inicial</td>";
	echo "<td class='fila1'>$fecha</td>";
	echo "</tr>";
	echo "<tr>";
	
	echo "</table>";
	
	list( $codigoResponsable ) = explode( " - ", $codigoResponsable );
	
	echo "<br><br>";
	
	//Consulta el ingreso de pacientes en una fecha dada
	$sql = "SELECT 
				inghis, 
				ingnin, 
				ingfei, 
				inghin, 
				pactdo, 
				pacdoc, 
				pacno1, 
				pacno2, 
				pacap1, 
				pacap2, 
				pacact
		 	FROM 
		 		{$wbasedato}_000101 a, 
		 		{$wbasedato}_000100 b
			WHERE
				Ingcem = '$codigoResponsable'
				AND Pachis = Inghis 
				AND Ingfei = '$fecha'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		//IMPRESION DE ENCABEZADO DE TABLA
		echo "<center><b>RESULTADOS</b></center>";
		
		echo "<br>";
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		echo "<td rowspan='2' style='width:100'>Historia</td>";
		echo "<td colspan='2' style='width:200'>Ingreso</td>";
//		echo "<td>Hora de<br>ingreso</td>";
		echo "<td rowspan='2' style='width:120'>Nro. de<br>identificaci&oacute;n</td>";
		echo "<td rowspan='2'>Nombre</td>";
		echo "<td rowspan='2'>Activo</td>";
		echo "</tr>";
		
		echo "<tr  class='encabezadotabla' align='center'>";
		echo "<td>Fecha</td>";
		echo "<td>Hora</td>";
		echo "</tr>";
		
		//IMPRESION DE RSULTADO DE BUSQUEDA
		for( $i = 0; $rows = mysql_fetch_array($res);  $i++ ){
			
			$class = "class='fila".(($i%2)+1)."'";
			
			echo "<tr $class>";
			
			echo "<td align='center'>{$rows['inghis']}-{$rows['ingnin']}</td>";
			echo "<td align='center'>{$rows['ingfei']}</td>"; 
			echo "<td align='center'>{$rows['inghin']}</td>";
			echo "<td align='center'>{$rows['pactdo']} {$rows['pacdoc']}</td>";
			echo "<td>{$rows['pacno1']} {$rows['pacno2']} {$rows['pacap1']} {$rows['pacap2']}</td>";
			echo "<td align='center'>".( ($rows['pacact'] != 'on' )? "No" : "S&iacute;" )."</td>";
			
			echo "</tr>";
		}
		
		echo "</table>";
	}
	else{	//si no se encuentran registros de la consulta
		echo "<center><b>NO SE ENCONTRARON REGISTROS</b></center>";
	}
	
	echo "<INPUT type='hidden' name='menu' value='on'>";
	echo "<INPUT type='hidden' name='fecha' value='$fecha'>";
	echo "<INPUT type='hidden' name='wemp_pmla' value='$wemp_pmla'>";
	
	echo "<br><br>";
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td><INPUT type='submit' value='Regresar' style='width:100'></td>";
	echo "<td><INPUT type='button' onClick='javascript: cerrarVentana();' value='Cerrar' style='width:100'></td>";
	echo "</tr>";
	echo "</table>";
}

echo "</form>";
?>
