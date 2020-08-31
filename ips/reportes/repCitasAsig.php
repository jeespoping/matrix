<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");





include_once("root/comun.php");
// $conex = obtenerConexionBD("matrix");


if (isset($accion) && $accion == 'listar')
{
	
	$dato = array('div'=>'','error'=>0);
	$resp = "<div align='center' id='tabla' diplay='none'>";
	
	//se consulta las citas dentro de un rango
	$query = "select fecha_data, Cod_exa, fecha, nom_pac, cedula, usuario 
			  from ".$wemp2."_000009 
			  where fecha_data between '".@$wfecini."' and '".@$wfecfin."' Order by fecha_data";
	$err = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );		
	$num = mysql_num_rows($err);
	
	$trs = '';
	if ($num > 0)
	{		
		for( $i = 0; $row = mysql_fetch_array($err); $i++ )
			{
				$colorf = '';
				if(($i%2) == 0){ $colorf = 'fila1'; }
				else { $colorf = 'fila2'; }
				$fecha_data=$row['fecha_data'];
				$cod_exa=$row['Cod_exa'];
				$fecha=$row['fecha'];
				$nom_pac=$row['nom_pac'];
				$cedula=$row['cedula'];
				$usuario=$row['usuario'];
				
				//se consulta la descripcion del examen
				 $query1 = "select descripcion 
						    from ".$wemp2."_000011 
						    where codigo = '".$cod_exa."' ";
				 $err1 = mysql_query($query1,$conex)or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
				 $row1 = mysql_fetch_array($err1);
				 $especialidad=$row1['descripcion'];
				 
				 //se consulta el nombre del usuario
				 $query2 = "select descripcion 
						    from usuarios 
						    where codigo = '".$usuario."' ";
				 $err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
				 $row2 = mysql_fetch_array($err2);
				 $nomUsuario=$row2['descripcion'];
				
				$trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".$cod_exa."-".$especialidad."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".$cedula."</td>
						<td>".$nomUsuario."</td>
						
						</tr>";
			 }
	 }
	 else
	 {
		$trs = "<tr><td colspan='6' align='center' class='fila2'>No se encontraron registros para esas fechas</td></tr>";
	 }
		 
	$resp .= "<table border='0'>";
	$resp .= "<th class='encabezadotabla' colspan='6'>Citas asignadas entre:".@$wfecini." y ".@$wfecfin."</th>";
	$resp .= "<tr class='encabezadotabla' align='center'>";
	$resp .= "<td>Fecha asignacion cita</td><td>Especialidad</td><td>Fecha cita</td><td>Nombre Paciente</td><td>Cedula</td><td>Usuario</td>";
	$resp .= "</tr>";
	$resp .= $trs;	
	$resp .= "</table>";
	$resp .= "</div>";
	

	$dato['div'] = $resp;
	echo json_encode($dato);
			
	
	return;
}



?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel='stylesheet' href='../../../include/root/matrix.css'/>
<script src="../../../include/root/jquery-1.3.2.min.js" type="text/javascript"></script>
<title>MATRIX</title>
</head>
<BODY>
<script type="text/javascript">
 function enviar()
 {
	$.post("repCitasAsig.php",
		{
			wemp_pmla:      $('#wemp_pmla').val(),
			consultaAjax:   '',
			wfecini:		$('#wfecini').val(),
			wfecfin:		$('#wfecfin').val(),
			wemp2:          $('#wemp2').val(),
			accion:			'listar'
		}
		,function(data) {
			if(data.error == 1)
			{
				alert('Error');
			}
			else
			{
				$("#tabla").html(data.div);
			}
		},
		"json"
	);
	
		
 }
<!--

	
//-->
</script>
<?php
/****************************************************************************
* 2012-09-20 (Viviana Rodas): Se crea el reporte de citas asignadas, 
* para obtener la informacion de citas asignadas con sus respectivas fechas de
* asignacion, especialidad, fecha para la que se asigno la cita, nombre del paciente,
* cedula y el usuario que asigno la cita.
 Modificaciones: 2012-09-25 Se agrega la consulta a la tabla citascs_000011 para colocar la descripcion del examen,
* y a la tabla usuarios colocar el nombre del usuario.Viviana Rodas
****************************************************************************/


 
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{	
    
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato = strtolower( $institucion->baseDeDatos );
	
	function solucionCitas( $codEmp ){
	
	global $conex;
	
	$solucionCitas = '';
	
	$sql = "SELECT
				detval
			FROM
				root_000051
			WHERE
				detapl = 'citas'
				AND detemp = '$codEmp'
			";
	
	$res = mysql_query( $sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$solucionCitas = $rows[0];
	}
	
	return $solucionCitas;
}
	$wemp2 = solucionCitas( $wemp_pmla );
	echo "<input type='hidden' id='wemp2' name='wemp2' value='".$wemp2."'>";
	
	$wactualiz="2012-09-25";
	 
	encabezado("Reporte de citas asignadas ",$wactualiz, "logo_".$wbasedato);
	
	 //rango de fecha para mirar el reporte de citas asignadas***
	echo "<div align='center' id='fecha'><br />";
	echo "<form name='reporte'  method='post' action='' >";
	echo "<table>";
	echo "<tr>";
	echo "<th colspan='4' class='encabezadotabla' align=center valign='top'>Seleccione el rango de fechas</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center valign='top'>Fecha Inicial</td>";
	echo "<td class='fila2' align='center'>";
		if(isset($wfecini) && !empty($wfecini))
		{
			campoFechaDefecto("wfecini",$wfecini);
		} else 
		{
			campoFecha("wfecini");
		}
		echo "</td>";
	echo "<td class='fila1' align=center valign='top'>Fecha Final</td>";
	echo "<td class='fila2' align='center'>";
		if(isset($wfecfin) && !empty($wfecfin))
		{
			campoFechaDefecto("wfecfin",$wfecfin);
		} else 
		{
			campoFecha("wfecfin");
		}
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td colspan='4' align='center'><input type='button' value='Enviar' style='width:100' onclick='javascript: enviar();'></td>";
	echo "</tr>";
	echo "</table>";
	echo "</form>";
	echo "</div>";
	
		
	echo "<div align='center' id='tabla' style='display:block'>";
	echo "</div>";
		
}
?>