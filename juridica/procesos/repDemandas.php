<html>
<head>
  <title>PROCESOS CONTRACTUALES</title>
</head>
<?php 
 include_once("conex.php");

 if(!isset($_SESSION['user'])){
	  echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	  return;
 }

 include_once("root/comun.php");

 session_start();
 if(!isset($_SESSION['user']))
	echo "error";

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

//encabezado("PROCESOS CONTRACTUALES", "1.0 Julio 24 de 2009" , "clinica");

if( !isset( $abogado ) || !isset( $tipo ) || !isset( $sentencia ) || !isset( $estado ) ){
	
	$rowabo ="";
	$rowtip ="";
	$rowest ="";
	$rowsen ="";
	
	//Estados de las demanda, abierto y cerrado
	$sqlest = "SELECT demest FROM juridica_000001
			GROUP BY 1";
	
	$resest = mysql_query( $sqlest , $conex );
	
	//Tipo
	$sqltip = "SELECT demtip FROM juridica_000001
			GROUP BY 1 ";
	
	$restip = mysql_query( $sqltip , $conex );
	
	
	//Estado de la sentencias
	$sqlsen = "SELECT demsen FROM juridica_000001
			GROUP BY 1 ";
	
	$ressen = mysql_query( $sqlsen , $conex );
	
	//Abogados
	$sqlabo = "SELECT demabo FROM juridica_000001
			GROUP BY 1 ";
	
	$resabo = mysql_query( $sqlabo , $conex );
	
	echo "<form action='repDemandas.php?wemp_pmla=".$wemp_pmla."' method='post'><table align='center'>
		<tr class='encabezadotabla'>
			<td>Abogado</td>
			<td>Estado</td>
			<td>Tipo</td>
			<td>Sentencia</td>
		</tr><tr class='fila1'>
			<td><SELECT name='abogado'><option value='%'></option>"; 
	
	for($i = 0; $rowabo = mysql_fetch_array($resabo) < 0;$i++){
		echo "<option value='{$rowabo[0]}'>{$rowabo[0]}</option>";
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
	echo "<table align='center' width='70%'>
		<tr>
			<td>Abogado: $abogado</td>
		</tr><tr>
			<td>Estado: $estado</td>
		</tr><tr>
			<td>Tipo: $tipo</td>
		</tr><tr>
			<td>Sentencia: $sentencia</td>
		</tr>
	</table><br><br>";
	
	//Buscando los datos a mostrar en pantalla
	$sql = "SELECT * 
			FROM juridica_000001 
			WHERE demest like '$estado' AND
			demtip like '$tipo' AND
			demsen like '$sentencia' AND 
			demabo like '$abogado'";
	
	$res = mysql_query( $sql, $conex);
	
	for( $i=0;$rows = mysql_fetch_array($res);$i++ ){
		
		//Calculando la clase a pintar por fila
		$fila = "class='fila2'";
		if( $i%2 == 0){
			$fila = "class='fila1'";
		}
		//encabezado de la tabla
		if($i == 0){
		echo "<table align='center'  width='5670'>
			<tr class='encabezadotabla'>
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
				<td width=300'>No. Poliza que cubre elevento</td>
				<td width=300'>Abogado</td>
				<td width=300'>Cotización</td>
				<td width=100'>F. pagos</td>
				<td width=300'>Hon. Facturados</td>
				<td width=300'>Egreso PMLA</td>
				<td width=300'>Reembolso</td>
				<td width=300'>Observaciones</td>
				<td width=300'>Pretensiones</td>
				<td width=300'>Valor Pre</td>
				<td width=300'>Est. Proceso</td>
				<td width=100'>No. Radicado</td>
				<td width=250'>Juzgado</td>
				<td width=100'>Sentencia</td>
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
				<td>{$rows['Demnpo']}</td>
				<td>{$rows['Demabo']}</td>
				<td>{$rows['Demcot']}</td>
				<td>{$rows['Demfpa']}</td>
				<td>{$rows['Demhon']}</td>
				<td>{$rows['Dempeg']}</td>
				<td>{$rows['Demree']}</td>
				<td>{$rows['Demobs']}</td>
				<td>{$rows['Dempre']}</td>
				<td>{$rows['Demval']}</td>
				<td>{$rows['Demesp']}</td>
				<td>{$rows['Demrad']}</td>
				<td>{$rows['Demjuz']}</td>
				<td>{$rows['Demsen']}</td>
			</tr>";
	}
	echo "</table>"; 
	
	echo "<br><br>
	<form action='repDemandas.php?wemp_pmla=".$wemp_pmla."' method='post'>
		<table align='center'>
			<tr class='encabezadotabla'>
				<td><INPUT type='submit' value='Retornar' style='width:100'></td>
				<td><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></td>
			</tr>
		</table>
	</form>";
}
?>