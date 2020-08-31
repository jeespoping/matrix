<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");
include_once("../procesos/funciones_talhuma.php");


global $wemp_pmla;


$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fecha= date("Y-m-d");
$hora = date("H:i:s");





if ($inicial=='no' AND $operacion=='mostrarAgrupaciones' )
{

				
	echo "<table align = 'center' >";
	
			

	if($tipocco=='tabcco')
	{
		$wbasedatocyp = consultarAliasPorAplicacion($conex, $wemp_pmla, $tipocco);
		
	}
	else
	{
		$wbasedatocyp ='movhos_000011';
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
	
	
	
	$q = "SELECT Descod,Desdes"
		 ."  FROM ".$wbasedato."_000005 "
		 ." WHERE  Destip = '05'"
		 ."   AND  Desest !='off'";

	   
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	

	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Preguntas</td>";
	echo "<td align='left' colspan='2'>";
	echo "<select id='select_pregunta' >";
	echo "<option value='seleccione'>Seleccione</option>";

	
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Descod']."'>".$row['Desdes']."</option>";
	}
	echo "<select>";
	echo "</td>";

	echo "</tr>";
		
	

	$fechaactual= date('Y-m-d');
	$wfecha_i = $fechaactual;
	$wfecha_f = $fechaactual;
	echo "<tr align='left'>";
	echo "<td align='left' class='fila1' >Periodo</td><td class='fila1' align='center'>Fecha Inicial: ";
	campofechaDefecto("wfecha_i",$wfecha_i);
	echo "</td>";
	echo "<td align='center' class='fila1'>Fecha final: ";
	campofechaDefecto("wfecha_f",$wfecha_f);
	echo "</td>";
	echo "</tr>";

	
	
	echo"<tr >";
	echo"<td align='left' class='fila1' colspan='1'>Tipo de Servicio</td>";
	echo"<td align='center'  class='fila1' colspan='2'>Todos&nbsp;<input name='servcios' checked='checked' type='radio' id='todos' value='si'>Hospitalario&nbsp;<input name='servcios' type='radio' id='servicio_hospitalario' value='si'>&nbsp;Terapeuticos&nbsp;<input name='servcios' type='radio' id='servicio_terapeuticos' value='si'>&nbsp;Urgencias&nbsp;<input name='servcios' type='radio' id='servicio_urgencias' value='si' >&nbsp;Cirugia&nbsp;<input type='radio' name='servcios' id='servicio_cirugia' value='si'>&nbsp;Ayudas Diagnosticas&nbsp;<input type='radio' name='servcios' id='servicio_ayudas' value='si'></td>";
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
		
		
		$q = "SELECT  DISTINCT(Ideuse) as usuario,Ideno1,Ideno2,Ideap1,Ideap2 ,costosyp_000005.Cconom,Cardes "
			."  FROM ".$wbasedato."_000050 , talhuma_000013, costosyp_000005,movhos_000011,root_000079 "
			." WHERE Peresc = Ideuse"
			."   AND Idecco = costosyp_000005.Ccocod"
			."   AND Ideccg = Carcod"
			."   AND movhos_000011.Ccocod = costosyp_000005.Ccocod";
		
			if($wcodcco != "")
				$q = $q . "  AND costosyp_000005.Ccocod = '".$wcodcco." '";
			
			// echo "<table><tr><td>".$q."</td></tr></table>";
			// se agrega esto al query para que busque por centro de costos hospitalarios
			if($whospitalario=='si')
			{
				$q = $q . " AND Ccohos = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos urgencias
			if($wurgencias=='si')
			{
				$q = $q . " AND Ccourg = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos cirugia
			if($wcirugia=='si')
			{
				$q = $q . " AND Ccocir = 'on' ";
			}
			
			// se agrega esto al query para que busque por centro de costos terapeutico
			if($wterapeutico=='si')
			{
				$q = $q . " AND Ccoter = 'on' ";
			}
			// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
			if($wayudas=='si')
			{
				$q = $q . " AND Ccoayu = 'on' ";
			}		
			
			$q = $q . " ORDER BY costosyp_000005.Ccocod";
		
		 // echo "<table><tr><td>".$q."</td></tr></table>";
		  
	
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$contenido = "<div id='div_descriptores' class='borderDiv displ' >";
		$contenido .= "<table align='center' >";

		$k=0;
		$imprimo=0;
		$contenido .=  "<tr class='encabezadoTabla'><td align='left'>Persona mencionada</td><td align='left'>Centro de Costos</td><td align='left'>Cargo</td><td>Numero de menciones</td><td>Persona que menciona</td><td align='left'>Historia</td><td align='left'>Fecha de Mención</td><td align='left'>Comentario	</td></tr>";
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
			
			$q1 = "SELECT  Encno1,Encno2, Encap1, Encap2,Enchis,Encfec,Enccom,Encenc"
				."  FROM  ".$wbasedato."_000049 ,  ".$wbasedato."_000050 , costosyp_000005 "
				." WHERE EncFec BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'  "
				."   AND Encese= 'cerrado'"
				."   AND Enchis= Perhis "
				."   AND Encing = Pering"
				."   AND Enccco = Ccocod" 
				."   AND Peresc ='".$row['usuario']."' " 
				."   AND perdes	= '".$wspregunta."' ";
				
			
			
			
			$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
			$cuantos = mysql_num_rows($res1);
			
	
			$j=0;
			if ($cuantos != 0)
			 {
				 $imprimo = $imprimo + 1; 
			
			 }
			while($row1 = mysql_fetch_array($res1))
			{
				
				if (($k%2)==0)
				{
					$wcf="fila1";  // color de fondo de la fila
				}
				else
				{
					$wcf="fila2"; // color de fondo de la fila
				}
				
				$contenido .= "<tr>";
				if($j==0)
				{
					$contenido .= "<td class='".$wcf."' nowrap='nowrap' rowspan='".$cuantos."' align='left' >".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
					$contenido .= "<td class='".$wcf."' nowrap='nowrap' rowspan='".$cuantos."' align='left'>".$row['Cconom']."</td>";
					$contenido .= "<td class='".$wcf."' nowrap='nowrap' rowspan='".$cuantos."' align='left'>".$row['Cardes']."</td>";
					$contenido .= "<td class='".$wcf."' nowrap='nowrap' align='left' rowspan='".$cuantos."' align='center'>".$cuantos."</td>";
				}
				$q3= "SELECT Comstr "
				." FROM  ".$wbasedato."_000036 "
				." WHERE Comdes = '69' "
				." AND Comuco= '".$row1['Enchis']."' "
				." AND Comfor = '".$row1['Encenc']."'"; 
				$res3 = mysql_query($q3,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q3." - ".mysql_error());
				$row3 = mysql_fetch_array($res3);
				$contenido .= "<td class='".$wcf."' align='left' nowrap='nowrap' >".$row1['Encno1']." ".$row['Encno2']." ".$row1['Encap1']." ".$row1['Encap2']."</td><td  align='left' nowrap='nowrap' class='".$wcf."' >".$row1['Enchis']."</td><td align='left' nowrap='nowrap' class='".$wcf."'>".$row1['Encfec']."</td><td  align='left' class='".$wcf."' width='500'>".$row3['Comstr']." </td>";
			
				$contenido .= "</tr>";
				$j++;
				$k++;
			
			}
		
			$i++;
		}
		$contenido .= "</table>";
		$contenido .= "</div>";
		
		if ($imprimo == 0)
		 echo "<table align='center'><tr><td>No hay datos para mostrar</td></tr></table>";
		else
		 echo $contenido;
		return;

}




?>

<html>
<head>
<title>Reportes de Menciones</title>
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
	tipocco = $('#tipocco').val();
	if($('#select_tema').val()=='seleccione')
	{
		$('#div_resultados').html('');
	}
	else
	{
		$.get("../reportes/reporte_empleados_mencionados.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarAgrupaciones',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				tipocco 		: tipocco 
				
			}
			, function(data) {
			$('#div_resultados').html(data);
			
			});
	}

}

function CargarSelectPreguntas()
{
	
	
	
	
	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectPreguntas',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val()
			
				
			}
			, function(data) {
			$('#div_select_pregunta').html(data);
		
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

function verReporte()
{
	


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
	var ayudas;
	
	
	centrocostos = $('#select_wcco').val();
	afinidad = $('#select_afinidad').val();
	entidad = $('#select_entidad').val();
	pregunta = $('#select_pregunta').val();
	
	
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
	
	

	
	
	if(centrocostos=='seleccione' || pregunta=='seleccione')
	{
		var mensaje = unescape('Debe seleccionar un centro de costos o una Pregunta');
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


	
	
	
	esperar (  );
	$.get("../reportes/reporte_empleados_mencionados.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeResultadosReporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				porcco			: porcco,
				porpregunta		: porpregunta,
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
				wayudas			: ayudas,
				wcirugia 		: cirugia,
				wspregunta		: pregunta
	
				
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
