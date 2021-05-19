<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");
include_once("../procesos/funciones_talhuma.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
global $wemp_pmla;


$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fecha= date("Y-m-d");
$hora = date("H:i:s");

$perfilver ='no';
$permisos = consultarSiEsAdmin($conex, $wemp_pmla, $wtema, $wcodtab, $_SESSION['user']);

if($permisos['esAdmin']=='on' )
  $perfilver="si";
 
 

if ($inicial=='no' AND $operacion=='CargarSelectAgrupacion')
{

	$q = "SELECT Grucod,Grunom "
		."  FROM ".$wbasedato."_000051"
		." WHERE Grutem = '".$wtemareportes."' "
		."   AND Grucag = '".$wclasificacionagrupacion."'";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	
	echo "<select id='select_agrupacion' onchange='CargarSelectPreguntas()'>";
	echo "<option value='todos||todos'>Todos</option>";
	
	while($row = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Grunom']."||".$row['Grucod']."'>".$row['Grunom']."</option>";
	}
	echo "</select>";

	return;
}

if ($operacion=='grabaComentario')
{

	$q = "UPDATE  ".$wbasedato."_000036 "
		." SET Comstr = '".utf8_decode($wcontenido)."' "
		." WHERE id = '".$widcomentario."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	
	
	$q = "UPDATE  ".$wbasedato."_000036 "
		." SET Comtip = '".$wtipodecomentario."' "
		." WHERE id = '".$widcomentario."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	echo  $q;

	return;
}

if ($operacion=='consultatipo')
{
$q = "	 SELECT  Comtip "
		." FROM ".$wbasedato."_000036 "
		." WHERE id = '".$widcomentario."' ";
		

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
$row = mysql_fetch_array($res);
echo $row['Comtip'];
return;
}

if ($inicial=='no' AND $operacion=='mostrarAgrupaciones' )
{

				
	echo "<table align = 'center' >";
	
	if($tipocco=='tabcco')
	{
		$wbasedatocyp = consultarAliasPorAplicacion($conex, $wemp_pmla, $tipocco);
		
	}
	else
	{
		$wbasedatocyp =$wmovhos.'_000011';
	}
		$q = "  SELECT Cconom ,Ccocod "
			."    FROM ".$wbasedatocyp." "
			."ORDER BY Cconom ";
	

	   
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//-----------------------------
	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Centro de costos</td>";
	echo "<td align='left' colspan='2'>";
	echo "<select id='select_wcco' >";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "<option value='todos||todos'>Todos</option>";
	
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Ccocod']."||".$row['Cconom']."'>".$row['Cconom']."</option>";
	}
	echo "<select>";
	echo "</td>";

	echo "</tr>";
	
	
	$q = "SELECT DISTINCT(Cagcod),Cagdes "
		."  FROM ".$wbasedato."_000057"
		." WHERE Cagest = 'on' ";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Clasificacion Agrupaciones</td>";
	echo "<td align='left' colspan='2'>";
	echo "<select id='select_clasificacionagrupacion' onchange='CargarSelectAgrupacion()'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "<option value='todos'>Todos</option>";
	
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Cagcod']."'>".$row['Cagcod']."-".$row['Cagdes']."</option>";
	}
	echo "<select>";
	echo "</td>";

	echo "</tr>";
	
	
	
	echo "<tr align='left' >";
	echo "<td class='fila1'>Agrupaci&oacute;n</td>";
	echo "<td class='fila1' align='left'  colspan='2'><div id='div_select_agrupacion'><select id='select_agrupacion' style='width: 40em'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "</select></div></td>";
	echo "</tr>";
	
	
	echo "<tr align='left' >";
	echo "<td class='fila1'>Pregunta</td>";
	echo "<td class='fila1' align='left'  colspan='2'><div id='div_select_pregunta'><select id='select_pregunta' style='width: 40em'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "</select></div></td>";
	echo "</tr>";
		
	

	$fechaactual= date('Y-m-d');
	$wfecha_i = $fechaactual;
	$wfecha_f = $fechaactual;
	echo "<tr align='left'>";
	echo "<td align='Left' class='fila1' >Periodo</td><td class='fila1' align='center'>Fecha Inicial: ";
	campofechaDefecto("wfecha_i",$wfecha_i);
	echo "</td>";
	echo "<td align='center' class='fila1'>Fecha final: ";
	campofechaDefecto("wfecha_f",$wfecha_f);
	echo "</td>";
	echo "</tr>";

	
	
	echo"<tr >";
	echo"<td align='left' class='fila1' colspan='1'>Tipo de Servicio</td>";
	echo"<td align='center'  class='fila1' colspan='2'>Todos&nbsp;<input type='radio' checked='checked' name='servicio' id='servicio_todos' value='si'>Hospitalario&nbsp;<input type='radio' name='servicio' id='servicio_hospitalario' value='si'>&nbsp;Terapeuticos&nbsp;<input name='servicio' type='radio' id='servicio_terapeuticos' value='si'>&nbsp;Urgencias&nbsp;<input name='servicio' type='radio' id='servicio_urgencias' value='si' >&nbsp;Cirugia&nbsp;<input name='servicio' type='radio' id='servicio_cirugia' value='si'>&nbsp;Ayudas Diagnosticas&nbsp;<input name='servicio' type='radio' id='servicio_ayudas' value='si'></td>";
	echo"</tr>";
	
	
	
	echo"<tr >";
	echo"<td  align='left' class='fila1' colspan='1'>Tipo de Comentario</td>";
	echo"<td class='fila1' align='center'  colspan='2'>Todos&nbsp;<input type='radio' checked='checked' name='tipocomentario' id='tipo_todos' checked='checked' value='si'>Queja&nbsp;<input type='radio'  name='tipocomentario' id='tipo_queja' value='si'>&nbsp;Sugerencia&nbsp;<input type='radio' name='tipocomentario' id='tipo_sugerencia' value='si'>&nbsp;Felicitacion&nbsp;<input type='radio' name='tipocomentario'  id='tipo_felicitacion' value='si' ></td>";
	echo"</tr>";
	
	echo"<tr align='left'>";
	echo"<td class='fila1' colspan='3' align='center'>";
	echo"<input type='button' value='Consultar' onclick=verReporte() >";
	echo"</td>";
	echo"</tr>";
	

	echo "</table>";
	

	
	return;
}

	
if ($inicial=='no' AND $operacion=='traeResultadosReporte')
{
		

	if($wclasificacionagrupacion != '')
	{
		//vector donde se van almacenar las agrupaciones 
		$vectoragrupaciones = array();
		// vector donde se guarda los nombres de las agrupaciones
		$vectornomagrupaciones = array();
		 
		$q1 ="  SELECT Cagcod,Cagdes "
			."     FROM ".$wbasedato."_000057 ";
			
		if($wclasificacionagrupacion != 'todos')
			 $q1= $q1." WHERE Cagcod = '".$wclasificacionagrupacion."' ";
		
		$q1= $q1." ORDER BY Cagcod" ;
		
		
			
		$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());	
	
		$s=0;
		while($row1 = mysql_fetch_array($res1))
		{
			$q2 =" SELECT Grucod,Grunom "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucag = '".$row1['Cagcod']."' ";
			
			if($wcagrupacion !='todos')
				$q2= $q2." AND Grucod='".$wcagrupacion."'" ;
			
			
			
			$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			$separador = "*|"; // se utiliza para separar 
			while($row2 = mysql_fetch_array($res2))
			{
				$vectoragrupaciones[$s] .= $separador . $row2['Grucod'];
				if($wcagrupacion !='todos')
					$vectornomagrupaciones[$s]=$row2['Grunom'];
				else
					$vectornomagrupaciones[$s]=$row1['Cagdes'];
				
				$separador = "|";
			}
			$vectoragrupaciones[$s] = str_replace("*|","",$vectoragrupaciones[$s]);
		    $s++;
		}
		
		$s=0;
		// se crea el vector de condiciones		
		while( $s < count($vectoragrupaciones) )
		{
			if ( strpos($vectoragrupaciones[$s], "|") == 1)
			{
				$numerodeagrupaciones = explode("|",$vectoragrupaciones[$s]);
				$condicion = "(";
				$separador = '|**|';
				$o=0;
				while ($o < count($numerodeagrupaciones))
				{
					$condicion .= $separador. "Desagr = '".$numerodeagrupaciones[$o]."'";
					$separador = "||";
					$o++;
				}
				$condicion .= ")";
				
				$condicion = str_replace("|**|","",$condicion);
				$condicion = str_replace("||"," OR ",$condicion);
				
			}
			else
			{
				$condicion = " Desagr='".$vectoragrupaciones[$s]."'";
				
			}
			$s++;
		}
		
	}
		
		
		
			
		
		$q = "SELECT  Descod , Desdes"
			."  FROM ".$wbasedato."_000005  ";
			
		if($wclasificacionagrupacion!='todos')
		{
			$q = $q ." WHERE ".$condicion." ";
		}
	
		
		if($wspregunta != "seleccione")
			$q = $q ."AND Descod ='".$wspregunta."' ";
		  
		

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		
		
		$contenido = "<div id='div_descriptores' class='borderDiv displ' >";
		$contenido .= "<table align='center' >";
		
		
		$contenido .="<tr class='encabezadoTabla' align='left' ><td width='300' >Pregunta</td><td>Nombre Encuestado</td><td>Historia</td><td>Centro de costos</td><td>Fecha Encuesta</td><td>Tipo Comentario</td><td>Comentario </td></tr>";
		
		$imprimo=0;
		$k=0;
		while($row = mysql_fetch_array($res))
		{
			
			if (($k%2)==0)
			{
				$wcf="fila1";  // color de fondo de la fila
			}
			else
			{
				$wcf="fila2"; // color de fondo de la fila
			}
			
			$q1 = "SELECT  Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr ,Desdes,Comtip,Enchis,EncFec,Cconom,encfce,".$wbasedato."_000036.id"
				."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 , ".$wbasedato."_000036 ,".$wmovhos."_000011 "
				." WHERE Evades = '".$row['Descod']."'"
				."   AND Evafco = Encenc "
				."   AND Enchis = Evaevo "
				."   AND Evacal != 0 "
				."   AND EncFec BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
				."   AND Encese= 'cerrado'"
				."   AND Comdes = Descod "
				."   AND Comucm = Evaevo" 
				."   AND Descod = Evades"
				."   AND Enccco = Ccocod ";
			
			// echo "<tr><td>".$q1."---</td></tr>";
				
			if($wcodcco != "")
				$q1 = $q1 . "  AND Enccco = '".$wcodcco." '";
			
			
			// se agrega esto al query para que busque por centro de costos hospitalarios
			if($whospitalario=='si')
			{
				$q1 = $q1 . " AND Ccohos = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos urgencias
			if($wurgencias=='si')
			{
				$q1 = $q1 . " AND Ccourg = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos cirugia
			if($wcirugia=='si')
			{
				$q1 = $q1 . " AND Ccocir = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
			if($wayudas=='si')
			{
				$q1 = $q1 . " AND Ccoayu = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos terapeutico
			if($wterapeutico=='si')
			{
				$q1 = $q1 . " AND Ccoter = 'on' ";
			}
			
			// se agrega esto al query para que busque por tipo de comentario
			if($wtipocomentario!='')
			{
				$q1 = $q1 . " AND Comtip = '".$wtipocomentario."' ";
			}
		
			
			
			$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
			$cuantos = mysql_num_rows($res1);
			
			 if ($cuantos != 0)
			 {
				 $imprimo = $imprimo + 1; 
			
			 }
			
		
			
			$j=0;
			while($row1 = mysql_fetch_array($res1))
			{
				
				if ($row1['Comtip'] == 1)
				{
				 $tipocomentario= "Queja";
				 $color = '#FFFFCC'; 
				}
				else if ($row1['Comtip'] == 2)
				{
				 $tipocomentario="Sugerencia";
				 $color = '#B6D884'; 
				}
				else
				{
				 $tipocomentario="Felicitacion";
				 $color = '#B6D884'; 
				}
				if (($k%2)==0)
				{
					$wcf="fila1";  // color de fondo de la fila
				}
				else
				{
					$wcf="fila2"; // color de fondo de la fila
				}
				if($perfilver=="si")
					$editarcomentario="<a style='float : right;  cursor : pointer' onclick='fnmostrar(\"".$row1['id']."\",\"".str_replace('"','\"',$row1['Comstr'])."\", \"".$row1['Comtip']."\")'>Editar </a>";
				
				
				$contenido .= "<tr  align='left' >";
				if($j==0)
					$contenido .= "<td width='300' class='encabezadoTabla' rowspan='".($cuantos)."'  >".$row['Desdes']." </td>";
				
				$contenido .= "<td class='".$wcf."' nowrap='nowrap' >".$row1['Encno1']." ".$row['Encno2']." ".$row1['Encap1']." ".$row1['Encap2']."</td>";
				$contenido .= "<td class='".$wcf."'>".$row1['Enchis']."</td>";
				$contenido .= "<td class='".$wcf."'>".$row1['Cconom']."</td>";
				$contenido .= "<td nowrap='nowrap' class='".$wcf."'>".$row1['encfce']."</td>";
				$contenido .= "<td  id='tdcomentario".$row1['id']."' align='center' bgcolor='".$color."' ><b>".$tipocomentario."<b></td>";
				$contenido .= "<td class='".$wcf."'><div id='tddiv".$row1['id']."'>".$row1['Comstr']."</div> ".$editarcomentario."</td>";
				$contenido .= "</tr>";
				$editarcomentario='';
				$j++;
				$k++;
			
			}
		
			$i++;
		}
		
		
		$contenido .= "</table>";
		$contenido .= "</div>";
		echo "<div align='center' id='diveditacomentario' style='display:none'  class='fila2'>";
		echo "<input type='hidden' id='idcomentario'>";
		echo "<br><br>";
		echo "<table align='center'>";
			echo"<tr class='encabezadoTabla'><td>Editar</td></tr>";
			echo"<tr class='fila1' ><td><div id='muestracomentario'></div></td></tr>";
			echo"<tr class='fila2' align='center' >
							<td class='fila1' align='center' >
								<input type = 'radio' id='queja2' name='tipocomentario2' value='1' >Queja
								<input type = 'radio' id='sugerencia2'  name='tipocomentario2' value='2' )'>Sugerencia
								<input type = 'radio' id='felicitacion2'  name='tipocomentario2' value='3' >Felicitacion
							</td>
							<td>
						</tr>";
			echo"<tr class='fila2' align='center' >
							<td class='fila1' align='center' >
								<input type = 'button' value='Grabar' onClick='grabaComentario(); $.unblockUI(); ' style='width:100'>
								<input type = 'button' value='Cancelar' onClick='$.unblockUI();' style='width:100'>
							</td>
							<td>
						</tr>";
		echo "</table>";
		echo "<br><br>";
		echo "</div>";
		
		if ($imprimo == 0)
		 echo "<table align='center'><tr><td>No hay datos para mostrar</td></tr></table>";
		else
		 echo $contenido;
		return;

}




?>

<html>
<head>
<title>Reportes de Comentarios</title>
 <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />


<script type='text/javascript'>
function verAgrupaciones()
{
	var tipocco = $('#tipocco').val();
	if($('#select_tema').val()=='seleccione')
	{
		
		$('#div_resultados').html('');
	}
	else
	{
	
		$.get("../reportes/reporte_comentarios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarAgrupaciones',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				tipocco 		: tipocco ,
				wcodtab 		:$('#wcodtab').val()
				
				
			}
			, function(data) {
				$('#div_resultados').html(data);
			
			});
	}

}

function CargarSelectAgrupacion()
{
	var clasificacionagrupacion;
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();
	
	
	$.get("../reportes/reporte_comentarios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectAgrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wclasificacionagrupacion	: clasificacionagrupacion,
				wcodtab 		:$('#wcodtab').val()
				
			}
			, function(data) {
			$('#div_select_agrupacion').html(data);
		
			});

}

function esperar (  )
{
$.blockUI({ message:        '<img src="../../images/medical/ajax-loader.gif" >',
                               css:         {
                                           width:         'auto',
                                           height: 'auto'
                                       }
                       });
}
function grabaComentario()
{

var idcomentario ;
var contenidocomentario;
idcomentario = $('#idcomentario').val();

contenidocomentario = $('#mestadoencuesta').val();
var tipodecomentario ;
tipodecomentario = $('input[name=tipocomentario2]:checked').val();


	$.get("../reportes/reporte_comentarios.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'grabaComentario',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					widcomentario	: idcomentario,
					wcontenido		: contenidocomentario,
					wcodtab 		:$('#wcodtab').val(),
					wtipodecomentario : tipodecomentario
					
				}, function(data) {
				$('#tddiv'+idcomentario).text(contenidocomentario);
				if(tipodecomentario=='1'){
				$('#tdcomentario'+idcomentario).html('<b>Queja<b>');
				$('#tdcomentario'+idcomentario).attr('bgcolor','#FFFFCC');
				
				}
				if(tipodecomentario=='2'){
				$('#tdcomentario'+idcomentario).html('<b>Sugerencia<b>');
				$('#tdcomentario'+idcomentario).attr('bgcolor','#B6D884');
				}
				if(tipodecomentario=='3'){
				$('#tdcomentario'+idcomentario).html('<b>Felicitacion<b>');
				$('#tdcomentario'+idcomentario).attr('bgcolor','#B6D884');
				}
			});
			



	
				

}
function CargarSelectPreguntas()
{
	var agrupacion;
	agrupacion = $('#select_agrupacion').val().split('||');
	
	
	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectPreguntas',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wagrupacion		: agrupacion[1],
				wcodtab 		:$('#wcodtab').val()
				
			}
			, function(data) {
			$('#div_select_pregunta').html(data);
		
			});

}

function fnmostrar( id , contenido, tipocomentario){
		
	
			$("#idcomentario").val(id);
			$.get("../reportes/reporte_comentarios.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'consultatipo',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					widcomentario	: id,
				}, function(data) {
				
				if(data=='1'){
					$('#queja2').attr('checked',true);
				}
				if(data=='2'){
					$('#sugerencia2').attr('checked',true);
				}
				if(data=='3'){
					$('#felicitacion2').attr('checked',true);
				}
			});
			
		
		
	
		
		
		$("#muestracomentario").html("<textarea  id='mestadoencuesta' rows='6'  cols='40'>"+$('#tddiv'+id).text()+"</textarea>");
		
		
		if( $('#diveditacomentario' ) ){
			$.blockUI({ message: $('#diveditacomentario' ), 
							css: { left: ( $(window).width() - 600 )/2 +'px', 
								  top: '200px',
								  width: '600px'
								 } 
					  });
			
		}
	}

function verReporte()
{
	

	var agrupacion;
	var centrocostos;
	var codcco;
	var nombrecco;
	var hospitalario;
	var urgencias;
	var terapeutico;
	var cirugia;
	var sugerencia;
	var queja;
	var felicitacion;
	var clasificacionagrupacion;
	var ayudas;
	var tipocomentario;
	
	agrupacion = $('#select_agrupacion').val();
	centrocostos = $('#select_wcco').val();
	afinidad = $('#select_afinidad').val();
	entidad = $('#select_entidad').val();
	pregunta = $('#select_pregunta').val();
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();
	
	if( $('#servicio_hospitalario:checked').val()=='si')
	{
		hospitalario = $('#servicio_hospitalario').val();
	}
	
	if( $('#servicio_urgencias:checked').val()=='si')
	{
		urgencias = $('#servicio_urgencias').val();
	}
	
	if( $('#servicio_terapeuticos:checked').val()=='si')
	{
		terapeutico = $('#servicio_terapeuticos').val();
	}
	
	if( $('#servicio_cirugia:checked').val()=='si')
	{
		cirugia = $('#servicio_cirugia').val();
	}
	
	if( $('#servicio_ayudas:checked').val()=='si')
	{
		ayudas = $('#servicio_ayudas').val();
	}
	
	if( $('#tipo_queja:checked').val()=='si')
	{
		tipocomentario = 1;
	}
	
	if( $('#tipo_sugerencia:checked').val()=='si')
	{
		tipocomentario = 2;
	}
	
	if( $('#tipo_felicitacion:checked').val()=='si')
	{
		tipocomentario = 3;
	}
	else if ($('#tipo_todos:checked').val()=='si')
	{
		tipocomentario = '';
	}

	
	
	if(centrocostos=='seleccione' || agrupacion=='seleccione')
	{
		var mensaje = unescape('Debe seleccionar un centro de costos o una agrupación');
		alert(mensaje);
		return;
	}
	
	if(centrocostos=='todos||todos')
	{
		centrocostos='todos';
		
	}
	else
	{
		centrocostos=centrocostos.split('||');
		
		nombrecco=centrocostos[1];
		codcco=centrocostos[0];
	}
	
	var fechainicial2;
	var fechafinal2;
	var porcco;
	var porpregunta;
	var poragrupacion ='si';
	
	var splitagrupacion = agrupacion.split('||');
	
	var codigoagrupacion = splitagrupacion[1];
	var nombreagrupacion = splitagrupacion[0];
	
	
	
	
	esperar (  );
	$.get("../reportes/reporte_comentarios.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeResultadosReporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wcagrupacion	: codigoagrupacion,
				wnagrupacion	: nombreagrupacion,
				porcco			: porcco,
				porpregunta		: porpregunta,
				poragrupacion	: poragrupacion,
				fechainicial1 	:$('#wfecha_i').val(),
				fechafinal1		:$('#wfecha_f').val(),
				wcco			: centrocostos,
				wcodcco			: codcco,
				wnomcco			: nombrecco,
				wafinidad		: afinidad,
				wentidad		: entidad,
				whospitalario	: hospitalario,
				wurgencias 		: urgencias,
				wterapeutico 	: terapeutico,
				wcirugia 		: cirugia,
				wayudas			: ayudas,
				wspregunta		: pregunta,
				wtipocomentario :tipocomentario,
				wclasificacionagrupacion : clasificacionagrupacion,
				wcodtab 		:$('#wcodtab').val()
	
				
			}
			, function(data) {
			$('#div_contenido_reporte').html(data);
			$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			$.unblockUI();
			});
	
	
	
}



</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
	#tooltip
	{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>
</head>
<body >

<?php

$wactualiz = "(Diciembre 03 de 2012)";

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
echo '<input type="hidden" id="wcodtab" name="wcodtab" value="'.$wcodtab.'" />';









/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/


/*
------------------------------------
-----------------------------------
*/
//------- campos ocultos

echo "<input type='hidden' name='wtema' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' name='wemp_pmla' id='tipocco' value='".$tipocco."'>";
//----------------------------

// tabla principal
echo"<table id='tabla_principal' width='900' align='center'>";
// primer tr :  Select tema.
echo"<tr><td>";

// datos select 
$q = "	SELECT  Forcod, Fordes "
	."	  FROM  ".$wbasedato."_000042 "
	."   WHERE Fortip='01' "
	."      OR Fortip='03' "
	."      OR Fortip='04' ";
// Nota: solo esta funcionando para evaluaciones internas Fortip=01 y para encuestas usuarios registrados Fortip=03
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo"<div id='div_tema' align='center'>";
echo"<table width='500' align='center'  border='0' cellspacing='0' cellpadding='0'>";
echo"<tr>";
echo"<td width='200' class='encabezadoTabla' align='Center' >Seleccione Tema</td>";
echo"<td  width='300' class='encabezadoTabla' align='Left'>";
echo"<select id='select_tema' onchange='verAgrupaciones()'>";

echo"<option value='seleccione' selected >Seleccione</option>";

while($row = mysql_fetch_array($res))
{
	echo"<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
}

echo"</select>";
echo"</td>";
echo"</tr>";


echo"</table>";		



echo"</div>"; 
		
echo"</td></tr>";




echo"</table>";
echo"<br><br>";
echo"<div id='div_resultados' >";
echo"</div>";



// -----div Muestra de resultados-------------------------


echo "<table align='center'>";
echo "<tr>";
echo "<td>";
echo "<div id='div_contenido_reporte'></div>";
echo "<td>";
echo "</tr>";
echo "</table>";

echo "<br>";
// div ocultos

// -------------------------------------------------------------
// echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0;height: 400px' >";
echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;height:auto;background:none repeat' >";
echo "<input id='nombreAgrupacionAgregar' type='hidden'>";
echo "<br><br>";


echo "</td>";

// dqiv para mostrar las preguntas
echo "<div id='div_contenido_porpreguntas'>";

echo "</div>";
// div de detalle de preguntas 
echo "<div id='detallepreguntas'>";
echo "</div>";


echo"<br><br>";
echo"<input type='button' value='Cancelar'  onClick='$.unblockUI();' style='width:100'>";
echo"</div>";


?>
</body>
</html>