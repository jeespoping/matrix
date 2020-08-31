<?php
include_once("conex.php");





include_once("root/comun.php");
// $conex = obtenerConexionBD("matrix");


if (isset($accion) && $accion == 'listar')
{
	
	$columnas_titulo = 9;	
	
	$dato = array();
	$resp = "<div align='center' id='tabla' >";
	
	//se consulta las citas dentro de un rango
	if ($caso == 1 or $caso == 3)
	{
		$query = "select fecha_data, Cod_equ, Cod_exa, fecha, nom_pac, cedula, usuario, Comentarios as Comentario, id as id, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi,TIME_FORMAT( CONCAT(hf,'00'), '%H:%i') as hf,Telefono
				  from ".$wbasedato."_000001 
				  where fecha_data between '".@$wfecini."' and '".@$wfecfin."' 
				  and cod_equ like '".$filtro."'
				  and activo = 'A'  
				  Order by fecha_data,hi";

	}
	else if ($caso == 2 and $valCitas!='on') 
	{
		$query = "select fecha_data, Cod_equ, Cod_exa, fecha, nom_pac, cedula, usuario, Comentario, id as id, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi 
				  from ".$wbasedato."_000009 
				  where fecha_data between '".@$wfecini."' and '".@$wfecfin."'
				  and cod_equ like '".$filtro."'
				  AND activo = 'A'
				  Order by fecha_data,hi";
	
	}
	else if ($caso == 2 and $valCitas=='on') 
	{
		$query = "select a.fecha_data, Cod_equ, Cod_exa, fecha, nom_pac, cedula, usuario, Comentario, a.id as id, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi 
				  from ".$wbasedato."_000009 a ,".$wbasedato1."_000051 b
				  where a.fecha_data between '".@$wfecini."' and '".@$wfecfin."'
				  and b.medcod like '".$filtro."'
				  and b.medcid = a.cod_equ
				  AND a.activo = 'A'
				  Order by a.fecha_data,hi";
	}
	
	$res = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	$num = mysql_num_rows($res);
	
	$trs = '';
	$i = 0;
	if ($num > 0)
	{	

		while($row = mysql_fetch_assoc($res)){
				$colorf = '';
				$i % 2 == 0 ? $colorf = "fila1" : $colorf = "fila2";
				$fecha_data =$row['fecha_data'];
				$cod_exa    =$row['Cod_exa'];
				$fecha      =$row['fecha'];
				$nom_pac    =utf8_encode($row['nom_pac']);
				$cedula     =$row['cedula'];
				$usuario    =$row['usuario'];
				$cod_equ    =$row['Cod_equ']; //medico
				$comentario =utf8_encode($row['Comentario']);
				$hora       =$row['hi'];
				$horafin    =$row['hf'];
				$celular    =$row['Telefono'];
				$id         =$row['id'];
				
				$titulo_comentario = '';
				//Si la variable $mostrar_obser == true, es porque se selecciono el cajon de mostrar comentarios, entonces mostrara una columna adicional con el dato.
				if($mostrar_obser == 'true' and trim($comentario) != ''){					
					
					//Se imprime un div para cada comentario, asi al seleccionar Ver se mostrara el que este relacionado.
					$div_comentario = "<div class='modal_comentario_".$id."' style='display:none;' title='Comentario en el turno de ".$nom_pac." - ".$cedula."'>		
										<table border='0' width=400px>
										  <tbody>
											<tr>
											  <td align=left>".$comentario."</td>								  
										  </tbody>
										</table>					
									  </div>";	
					$dato_comentario = "<td align=center><a href='javascript:' onclick='ver_comentario(\"$id\");' class='tooltip' title='".$comentario."'>Ver ".$div_comentario."</a></td>";
					
				}else{
				//Si no hay comentarios imprime vacio.
				$dato_comentario = "<td align='center'></td>";
				}
				
				//se consulta la descripcion del examen y el medico o el equipo
				if ($caso == 1 or $caso == 3)
				{
					$query1 = "select a.Descripcion as examen, b.Descripcion as equipo
							   from ".$wbasedato."_000006 a, ".$wbasedato."_000003 b 
						       where a.Codigo = '".$cod_exa."' 
							   and b.Codigo = '".$cod_equ."' 
							   and a.Cod_equipo = b.Codigo";
				    $med="Sala";			   
				}
				else
				{
					$query1 = "select a.Descripcion as examen, b.Descripcion as equipo
							   from ".$wbasedato."_000011 a, ".$wbasedato."_000010 b 
							   where a.Codigo = '".$cod_exa."' 
							   and b.Codigo = '".$cod_equ."' 
							   and a.Cod_equipo = b.Codigo";
				    $med="Medico"; 
				}
				$err1 = mysql_query($query1,$conex)or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
				$row1 = mysql_fetch_array($err1);
				$especialidad=$row1['examen'];
				$equiMed=$row1['equipo'];
				 
				if ($mostrar_obser == 'true'){
		        
					$trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".$equiMed."</td>
						<td align=center>".$cod_exa."-".utf8_encode($especialidad)."</td>
						<td align=center>".$hora."</td>
						<td align=center>".$horafin."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".$cedula."</td>
						<td>".$celular."</td>						
						".$dato_comentario."
						</tr>";
				}
				else{
						$trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".$equiMed."</td>
						<td align=center>".$cod_exa."-".utf8_encode($especialidad)."</td>
						<td align=center>".$hora."</td>
						<td align=center>".$horafin."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".$cedula."</td>
						<td>".$celular."</td>				
						</tr>";					
				}		


						
			 }
	 }
	 else
	 {
		$trs = "<tr><td colspan='8' align='center' class='fila2'>No se encontraron registros en ese rango de fechas</td></tr>";
	 }
		 
	$resp .= "<table border='0' align='center'>";
	if ($num>0)
	{
        // Cuando escoga ver comentarios se agrega una columna
		if ($mostrar_obser == 'true')
		{
			$columnas_titulo = 10; //Si no selecciona el cajon de mostrar comentarios seran solamente 8 columnas en el encabezado.
			$titulo_comentario = "<td width='10%'>Comentarios</td>";
		}
        // Adicionar Titulo
		$resp .= "<th class='encabezadotabla' colspan='".$columnas_titulo."'>Turnos asignados entre:".@$wfecini." y ".@$wfecfin."</th>";
		$resp .= "<tr class='encabezadotabla' align='center'>";
		$resp .= "<td width='14%'>Fecha asignacion turno</td><td width='22%'>".$med."</td><td width='22%'>Tipo Reserva</td><td width='4%'>Hora Inicio</td><td width='4%'>Hora Fin</td><td width='8%'>Fecha turno</td><td width='25%'>Nombre Solicitante</td><td width='15%'>Codigo</td><td width='10%'>Celular</td>$titulo_comentario";
		$resp .= "</tr>";
	}
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
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<title>Reporte de Citas Asignadas</title>
</head>
<BODY>
<script type="text/javascript">

 function ver_comentario(id){
 
 $(".modal_comentario_"+id).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				// maxHeight:600,
				height:'auto',				
				width: 'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Comentario"/*,
				open:function(){
				var s = $('#cont_dlle_modal').height();
				var s2 = $(this).dialog( "option", "maxHeight" );
				if(s < s2){
				$(this).height(s);
				}
				}*/
				});
				
 $(".modal_comentario_"+id).dialog( "open" );
 
 }

 function enviar()
 {
 
	$.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
	var mostrar_obser = $('#mostrar_obser').is(':checked');
	
	$.post("repCitasAsigSala.php",
		{
			wemp_pmla:      $('#wemp_pmla').val(),
			consultaAjax:   '',
			wfecini:		$('#wfecini').val(),
			wfecfin:		$('#wfecfin').val(),
			wbasedato:      $('#wbasedato').val(),
			caso:      		$('#caso').val(),
			valCitas:       $('#valCitas').val(),
			filtro:         $('#slDoctor').val(),
			wbasedato1:     $('#wbasedato1').val(),
			mostrar_obser:	mostrar_obser,
			accion:			'listar'
		}
		,function(data) {
			    //alert(data.div);
				$("#tabla").html(data.div);
				document.getElementById('tabla').style.visibility = 'visible';
				$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });			
				
			$.unblockUI();
		},"json"
	).done(function(){	 });
	
		
 }


<!--

	
//-->
</script>
<?php
/*****************************************************************************************************************
* 2014-03-05 (Jonatan Lopez): Se agrega la columna de comentarios, si el usuario 
* selecciona el cajon para verlas, estas se podran ver en un tooltip y en una ventana modal.

* 2012-09-20 (Viviana Rodas): Se crea el reporte de citas asignadas, 
* para obtener la informacion de citas asignadas con sus respectivas fechas de
* asignacion, especialidad, fecha para la que se asigno la cita, nombre del paciente,
* cedula y el usuario que asigno la cita.

 Modificaciones:
 2013-04-03 Se modifica el programa para que se pueda utilizar en todas las unidades de citas.Viviana Rodas
 2012-09-25 Se agrega la consulta a la tabla citascs_000011 para colocar la descripcion del examen.Viviana Rodas
********************************************************************************************************************/

/*Funcion para el select de medicos o equipos*/
function selecMedEqu()
{
	global $caso;
	global $valCitas;
	global $wbasedato;
	global $wbasedato1;
	
	if ($caso == 2 and $valCitas=="on")
	{
		$sql = "SELECT Mednom, Medcod
				FROM ".$wbasedato1."_000051
				WHERE Medcid != ''
			    AND Medest = 'on'
		        ORDER BY Mednom";
	}
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo 
				from ".$wbasedato."_000003 
				where activo='A' ";
	}
	
	else if ($caso == 2 and $valCitas!="on")
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo 
				from ".$wbasedato."_000010 
				where activo='A' 
				group by descripcion 
				order by descripcion";
	}
				
	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
 	//$rows = mysql_fetch_array( $res1 );
	return $res1;
}

 
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{	
echo "<form name='reporte' method=post>";  
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato1 = strtolower( $institucion->baseDeDatos );
	
	//Buscando el doctor por el que fue filtrado
	if( !isset( $slDoctor ) ){
		$nmFiltro = "% - Todos";
		$filtro = '%';
		$slDoctor = "% - Todos";
	}
	else{
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}
	if (!isset($valCitas))
	{
		$valCitas = "off";
	}
	
	echo "<input type='hidden' id='wemp_pmla name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='caso' name='caso' value='".$caso."'>";
	echo "<input type='hidden' id='valCitas' name='valCitas' value='".$valCitas."'>";
	echo "<input type='hidden' id='wbasedato1' name='wbasedato1' value='".$wbasedato1."'>";

	$wactualiz="2017-06-29";
	 
	if ($wemp_pmla == 01)
	{
		encabezado("REPORTE DE TURNOS ASIGNADOS", $wactualiz, $wbasedato1 );
	}
	else
	{
		encabezado("REPORTE DE TURNOS ASIGNADOS", $wactualiz, "logo_".$wbasedato1 );
	}
	
	 //rango de fecha para mirar el reporte de citas asignadas***
	echo "<div align='center' id='fecha'><br />";
	echo "<form name='reporte'  method='post' action='' >";
	echo "<table>";
	echo "<tr>";
	echo "<th colspan='2' class='encabezadotabla' align=center valign='top'>Seleccione el rango de fechas</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center valign='top'>Fecha Inicial</td>";
	echo "<td class='fila2' align='left'>";
		if(isset($wfecini) && !empty($wfecini))
		{
			campoFechaDefecto("wfecini",$wfecini);
		} else 
		{
			campoFecha("wfecini");
		}
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center valign='top'>Fecha Final</td>";
	echo "<td class='fila2' align='left'>";
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
	if ($caso == 2)
	{		
		echo "	<td class='fila1' align=center>Filtro por Profesional</td>";
	}
	else
	{
		echo "	<td class='fila1' align=center>Filtro por Sala</td>";
	}
	$res1=selecMedEqu();	
	echo "	<td class='fila2'><select name='slDoctor' id='slDoctor' onchange=''>";
	echo "	<option value='%'>% - Todos</option>";
	
	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){
	
		if ($caso == 2 and $valCitas=="on")
		{

			$rows['Medcod'] = trim( $rows['Medcod'] );
			$rows['Mednom'] = trim( $rows['Mednom'] );
			
			if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) )
			{
				echo "<option value='{$rows['Medcod']}'>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
			else
			{
				echo "<option value='{$rows['Medcod']}' selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
		}
		else if ($caso == 1 or $caso == 3)
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );
			
			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option value='{$rows['codigo']}'>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option value='{$rows['codigo']}' selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		else if ($caso == 2 and $valCitas!= "on")
		{
			
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );
			
			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			
			{
				echo "<option value='{$rows['codigo']}'>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option value='{$rows['codigo']}' selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		
	}//for
	
	echo "</select>";
	echo"</td>";	
	echo "</tr>";
	echo "<tr><td class=fila1>¿Desea ver los comentarios?:</td><td class=fila2><input type=checkbox id='mostrar_obser'></td></tr>";
	echo "<tr>";
	echo "<td colspan='2' align='center' class='fila2'><input type='button' value='Enviar' style='width:100' onclick='enviar();'></td>";
	echo "</tr>";
	echo "</table>";	
	echo "</div>";
	echo "<br><br>";
		
	echo "<div id='tabla' name='tabla'>";
	echo "</div>";
	echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "</form>";
	echo "</body>";
	echo "</html>";
		
}
?>