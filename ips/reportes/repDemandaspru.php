<html>
<head>
  <title>DEMANDAS DE RESPONSABILIDAD CIVIL</title>
</head>
<?php
include_once("conex.php");

/*********************************************************************************************************
 * Fecha de Creación: 	2009-06-24
 * Programador:			Edwin Molina Grisales
 * 
 * Objetivo:			Mostrar las diferentes demandas de responsabilidad civil de acuerdo a los campos 
 * 						de medicos involucrados, estado de los procesos, el tipo (Demanda, llamamiento 
 * 						de garantía), abogado que lleva el caso, y providencia
 ********************************************************************************************************/

/*********************************************************************************************************
 * Actualizaciones
 * 
 *						2021-11-18
						Daniel CB
						-Se realiza modificación de paramatros 01 quemados.
 * 
 * Fecha:				2009-11-25
 * Programador:			Edwin Molina Grisales
 * Modificacion:		Se elimina tres campos, estos son: 
 * 						- Nro. de poliza que cubre el evento (Demnpo)
 * 						- Fechas de pago (Dempeg)
 * 						- Pagos Nro de Egreso de la promotora (Demfpa)
 * 
 ********************************************************************************************************/

include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
	echo "error";

$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "juridica");

encabezado("DEMANDAS DE RESPONSABILIDAD CIVIL", "Noviembre 18 de 2021" , "clinica");

if( !isset( $abogado ) || !isset( $tipo ) || !isset( $sentencia ) || !isset( $estado ) ){
	
	$rowabo ="";
	$rowtip ="";
	$rowest ="";
	$rowsen ="";
		
	//Tipos de detselecciones
	$sqlest = "SELECT subcodigo, descripcion FROM det_selecciones
			WHERE medico='juridica' AND
			codigo='001'
			GROUP BY 1";
	
	//Estados de las demanda, abierto y cerrado
	$sqlest = "SELECT demest FROM {$wbasedato}_000001
			WHERE demest<>''
			GROUP BY 1";
	
	$resest = mysql_query( $sqlest , $conex );
	
	//Tipo
	$sqltip = "SELECT demtip FROM {$wbasedato}_000001
	WHERE demtip<>''
			GROUP BY 1 ";
	
	$restip = mysql_query( $sqltip , $conex );
	
	
	//Estado de la sentencias
	$sqlsen = "SELECT demsen FROM {$wbasedato}_000001
	WHERE demsen<>''
			GROUP BY 1 ";
	
	$ressen = mysql_query( $sqlsen , $conex );
	
	//Abogados
	$sqlabo = "SELECT demabo FROM {$wbasedato}_000001
				WHERE demabo<>''
			GROUP BY 1 ";
	
	$resabo = mysql_query( $sqlabo , $conex );
	
	//Polizas
	$sqlpol = "SELECT dempol FROM {$wbasedato}_000001
				WHERE dempol<>''
			GROUP BY 1 ";
	
	$respol = mysql_query( $sqlpol , $conex );
	
	echo "<form action='repDemandaspru.php?wemp_pmla=".$wemp_pmla."' method='post'><table align='center'>
		<br><p align=center>Nombre médico involucrado: <INPUT type='text' name='medicos'></p><br>
		<tr class='encabezadotabla'>
			<td>Abogado</td>
			<td>Estado</td>
			<td>Tipo</td>
			<td>Poliza</td>
			<td>Providencia</td>
		</tr><tr class='fila1'>
			<td><SELECT name='abogado'><option value='%'>% - Todos</option>"; 
	
	for($i = 0; $rowabo = mysql_fetch_array($resabo);$i++){
		echo "<option value='$rowabo[0]'>$rowabo[0]</option>";
	}
	echo "</SELECT></td>
			<td><SELECT name='estado'><option value='%'>% - Todos</option>"; 
	
	for( $i = 0; $rowest = mysql_fetch_array($resest); $i++ ){
		echo "<option value='$rowest[0]'>$rowest[0]</option>";
	}
	
	echo "</SELECT></td>
			<td><SELECT name='tipo'><option value='%'>% - Todos</option>"; 
	
	for( $i = 0; $rowtip = mysql_fetch_array($restip); $i++ ){
		echo "<option value='{$rowtip[0]}'>$rowtip[0]</option>";
	}
	
	echo "</SELECT></td>
			<td><SELECT name='poliza'><option value='%'>% - Todos</option>"; 
	
	for( $i = 0; $rowpol = mysql_fetch_array($respol); $i++ ){
		echo "<option value='{$rowpol[0]}'>$rowpol[0]</option>";
	}
	
	echo "</SELECT></td>
			<td><SELECT name='sentencia'><option value='%'>% - Todos</option>"; 
	
	for( $i = 0; $rowsen = mysql_fetch_array($ressen); $i++ ){
		echo "<option  value='{$rowsen[0]}'>$rowsen[0]</option>";
	}
	
	echo "</SELECT></td>
		</tr>
	</table><br><br>
	<table align='center'>
		<tr>
			<td><INPUT type='submit' value='Ver' style='width:100'></td>
			<td><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></td>
		</tr>
	</table>
	</form>";
}
else
{
	//Información de la busqueda
	
	if( $abogado == "%" )
		$mostrarabogado = "Todos";
	else
		$mostrarabogado = $abogado;
	
	if( $estado == "%" )
		$mostrarestado ="Todos"; 
	else
		$mostrarestado = $estado;
	
	if( $tipo == "%" )
		$mostrartipo ="Todos"; 
	else
		$mostrartipo = $tipo;
		
	if( $sentencia == "%" )
		$mostrarsentencia ="Todos"; 
	else
		$mostrarsentencia = $sentencia;
		
	if( $poliza == "%" )
		$mostrarpoliza ="Todos"; 
	else
		$mostrarpoliza = $poliza;
		
	if( !isset($medicos) || empty($medicos) )
		$mostrarmedico ="Todos"; 
	else
		$mostrarmedico = $medicos;

	echo "<table align='center' width='70%'>
		<tr>
			<td>Abogado: $mostrarabogado</td>
		</tr><tr>
			<td>Estado: $mostrarestado</td>
		</tr><tr>
			<td>Tipo: $mostrartipo</td>
		</tr><tr>
			<td>Poliza: $mostrarpoliza</td>
		</tr><tr>
			<td>Providencia: $mostrarsentencia</td>
		</tr><tr>
			<td>Medico involucrado: $mostrarmedico</td>
		</tr>
	</table><br><br>";
	
	if( !isset($medicos) || empty($medicos) ){
		$medicos = "%";
	}
	else{
		$medicos = "%".$medicos."%";
	}
	
	//Buscando los datos a mostrar en pantalla
	$sql = "SELECT * 
			FROM {$wbasedato}_000001 
			WHERE demest like '$estado' AND
			demtip like '$tipo' AND
			demsen like '$sentencia' AND 
			demabo like '$abogado' AND
			dempol like '$poliza' AND
			demmin like '$medicos'";
	
	$res = mysql_query( $sql, $conex);
	
	$resnum = 0;	//Total de filas en la consulta
	$valtot = 0;	//Valor total de las pretensiones
	for( $i=0;$rows = mysql_fetch_array($res);$i++ ){
		
		//Calculando la clase a pintar por fila
		$fila = "class='fila2'";
		if( $i%2 == 0){
			$fila = "class='fila1'";
		}
		//encabezado de la tabla
		if($i == 0){
		echo "<table align='center'  width='5670'>
			<tr class='encabezadotabla' align='center'>
				<td width=50'>Est.</td>
				<td width=90'>Tipo</td>
				<td width=90'>F. Notificación</td>
				<td width=90'>F. Evento</td>
				<td width=300'>Paciente</td>
				<td width=300'>Demandante</td>
				<td width=300'>Médico involucrados</td>
				<td width=300'>Procedimiento</td>
				<td width=300'>Entidad por la que ingresa el paciente</td>
				<td width=300'>Poliza que cubre elevento</td>
				".//<td width=300'>No. Poliza que cubre elevento</td>
				"<td width=300'>Abogado</td>
				<td width=300'>Cotización</td>
				".//<td width=100'>F. pagos</td>
				"<td width=300'>Hon. Facturados</td>
				".//<td width=300'>Egreso PMLA</td>
				"<td width=300'>Reembolso</td>
				<td width=300'>Observaciones</td>
				<td width=300'>Pretensiones</td>
				<td width=150'>Valor Pre</td>
				<td width=300'>Est. Proceso</td>
				<td width=100'>No. Radicado</td>
				<td width=250'>Juzgado</td>
				<td width=100'>Providencia</td>
			</tr>"; 
		}

		//datos de la tabla
		echo "<tr $fila>
				<td>{$rows['Demest']}</td>
				<td>{$rows['Demtip']}</td>
				<td>{$rows['Demfno']}</td>
				<td>{$rows['Demfev']}</td>
				<td>{$rows['Demnpa']}</td>
				<td>{$rows['Demnde']}</td>
				<td>{$rows['Demmin']}</td>
				<td>{$rows['Dempro']}</td>
				<td>{$rows['Dement']}</td>
				<td>{$rows['Dempol']}</td>
				".//<td>{$rows['Demnpo']}</td>
				"<td>{$rows['Demabo']}</td>
				<td>{$rows['Demcot']}</td>
				".//<td>{$rows['Demfpa']}</td>
				"<td>{$rows['Demhon']}</td>
				".//<td>{$rows['Dempeg']}</td>
				"<td>{$rows['Demree']}</td>
				<td>{$rows['Demobs']}</td>
				<td>{$rows['Dempre']}</td>
				<td align='right'>".number_format($rows['Demval'],2,".",",")."</td>
				<td>{$rows['Demesp']}</td>
				<td>{$rows['Demrad']}</td>
				<td>{$rows['Demjuz']}</td>
				<td>{$rows['Demsen']}</td>
			</tr>";
		$valtot += $rows['Demval'];
		$resnum = $i;
	}
	
	if( $resnum == 0 ){
		echo "<p align='center'>No se encontraron datos a mostrar</p>";
	}
	
	echo "<tr class='encabezadotabla'>
		<td colspan='19' align='left'>Totales</td>
		<td align='right'>".number_format($valtot,2,".",",")."</td>
		<td colspan='4'></td>
	</tr>";
	
	echo "</table>"; 
	
	echo "<br><br>
	<form action='repDemandaspru.php?wemp_pmla=".$wemp_pmla."' method='post'>
		<table align='center'>
			<tr align=center>
				<td><INPUT type='submit' value='Retornar' style='width:100'></td>
				<td><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></td>
			</tr>
		</table>
	</form>";
}
?>