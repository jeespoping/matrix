<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
/**
 * ACTUALIZACIONES
 * 2017-12-06 Edwar Jaramillo:
 * 		Se modifica query que consulta los médicos por especialidad para que no busque las especialidades de un médico en la movhos_48 sino que se haga
 * 		join con movhos_65 para buscar las subespecialidades.
 */


include_once("root/comun.php");


global $wemp_pmla;

$operacion    = (!isset($operacion)) ? '': $operacion;
$user_session = explode('-',$_SESSION['user']);
$wuse         = $user_session[1];

// query que actualiza si un determinado Medico hace Turno
if($operacion == 'hacedisponibilidad')
{
	$q= "  UPDATE movhos_000048 "
	   ."	  SET Medhdi = '".$wvalor."' "
	   ."   WHERE Meddoc = '".$wcodigo."' ";

	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}
//--

//-- Dibuja la tabla de medicos por especialidad seleccionada
if($operacion == 'traermedicos' )
{
	/*$q= "  SELECT Medno1, Medno2, Medap1, Medap2 ,Meddoc  ,Medhdi,Medtel,Medbip,Medcel "
	   ."    FROM movhos_000048 "
	   ."   WHERE Medesp LIKE '%".$wespecialidad."%' "
	   ."     AND Medest = 'on'  "
	   ."ORDER BY Medhdi Desc ,Medno1, Medno2, Medap1 " ;*/
	$q = "	SELECT  mv48.Medno1, mv48.Medno2, mv48.Medap1, mv48.Medap2, mv48.Meddoc, mv48.Medhdi, mv48.Medtel , mv48.Medbip, mv48.Medcel
			FROM    movhos_000065 AS mv65
			        INNER JOIN
			        movhos_000048 AS mv48 ON (mv65.Esmtdo = mv48.Medtdo AND mv65.Esmndo = mv48.Meddoc AND mv48.Medest = 'on')
			WHERE   mv65.Esmcod LIKE '%{$wespecialidad}%'
			GROUP BY mv65.Esmtdo, mv65.Esmndo
			ORDER BY mv48.Medhdi Desc ,mv48.Medno1, mv48.Medno2, mv48.Medap1";

	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	echo "<table id='table_medicos'>";
	echo "<tr><td colspan='5'><input style='float: right' type='button' value='Cerrar' onclick='javascript:window.close();'><td></tr>";
	echo "<tr><td class='encabezadoTabla' >Disponibilidad</td><td class='encabezadoTabla' >Medico</td><td  class='encabezadoTabla'>Telefono</td><td  class='encabezadoTabla'>Celular</td><td  class='encabezadoTabla' >Biper</td></tr>";
	$k=0;

	while ($row = mysql_fetch_array($res))
	{
		if (($k%2)==0)
		{
			$wcf="fila1";  // color de fondo de la fila
		}
		else
		{
			$wcf="fila2"; // color de fondo de la fila
		}

		$chequeado = '';
		if($row['Medhdi']== 'on' )
		{
			$chequeado= "checked";
		}
		echo "<tr class='".$wcf."' align='left'  ><td align='center'><input type='checkbox' id='".$row['Meddoc']."'  value='on' ".$chequeado." onclick='grabadisponibilidad(this,\"".$row['Meddoc']."\")'></td><td nowrap='nowrap'>".$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2']."</td><td><input size='40' type='text' value='".$row['Medtel']."' id='tel_".$row['Meddoc']."' onblur='cambiatelefono(\"".$row['Meddoc']."\",\"tel_".$row['Meddoc']."\")'></td><td><input size='40' type='text' value='".$row['Medcel']."' id='cel_".$row['Meddoc']."' onblur='cambiacelular(\"".$row['Meddoc']."\",\"cel_".$row['Meddoc']."\")'></td><td><input type='text' size='40' value='".$row['Medbip']."' id='bip_".$row['Meddoc']."' onblur='cambiabiper(\"".$row['Meddoc']."\",\"bip_".$row['Meddoc']."\")' </td><tr>";
		$k++;
	}
	echo"</table>";


	return;
}
//--

//-- Cambia el telefono de un medico
if($operacion == 'cambiatelefono' )
{
	$q= "  UPDATE movhos_000048 "
	   ."	  SET Medtel = '".$wvalor."' "
	   ."   WHERE Meddoc = '".$wcodigo."' ";

	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}
//--

//-- Cambia numero de biper
if($operacion == 'cambiabiper' )
{
	$q= "  UPDATE movhos_000048 "
	   ."	  SET Medbip = '".$wvalor."' "
	   ."   WHERE Meddoc = '".$wcodigo."' ";

	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}

//-- Cambia numero de celular
if($operacion == 'cambiacelular' )
{
	$q= "  UPDATE movhos_000048 "
	   ."	  SET Medcel = '".$wvalor."' "
	   ."   WHERE Meddoc = '".$wcodigo."' ";

	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}


?>

<html>
<head>
<title>Medicos con disponibilidad</title>
 <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type='text/javascript'>
function grabadisponibilidad(elemento,codigo)
{

	var valor = 'off';
	if($('#'+elemento.id+':checked').val()=='on')
	{
		valor = 'on';
	}

	var codigo = elemento.id;

	$.get("medicos_con_disponibilidad.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'hacedisponibilidad',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wespecialidad	: $('#select_especialidad').val(),
				wvalor			: valor,
				wcodigo			: codigo
			}
			,function(data) {

			});


}
function traemedicos()
{

	if($('#tr_primer').next().hasClass('xxx'))
	{

	  $('#tr_segundo').remove();
	   $('#div_datos').html('');
	}

	if($('#select_especialidad').val() == '')
	{

		alert('seleccione una disponibilidad');
		return;

	}
	else
	{

		$.get("medicos_con_disponibilidad.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traermedicos',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wespecialidad	: $('#select_especialidad').val()
			}
			, function(data) {
				$('#div_datos').html(data)
			});

	}


}


function cambiatelefono(codigo,valor)
{

$.get("medicos_con_disponibilidad.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'cambiatelefono',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wcodigo 		: codigo,
				wvalor			: $('#'+valor).val()
			}
			, function(data) {

			});


}
function cambiabiper(codigo,valor)
{
$.get("medicos_con_disponibilidad.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'cambiabiper',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wcodigo 		: codigo,
				wvalor			: $('#'+valor).val()
			}
			, function(data) {

			});


}

function cambiacelular(codigo,valor)
{
$.get("medicos_con_disponibilidad.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'cambiacelular',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wcodigo 		: codigo,
				wvalor			: $('#'+valor).val()
			}
			, function(data) {

			});


}

function  traedatos ()
{

var wcodigo ;
if($('#select_medico').val() == '')
	{

		alert('seleccione un medico');
		return;

	}
	else
	{

		$.get("medicos_con_disponibilidad.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traedatos',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wcodigo			: $('#select_medico').val()
			}
			, function(data) {
			$('#div_datos').html(data);


			});

	}


}

function cerrar ()
{
	alert("hola");
	window.close();
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

$wactualiz = "(Diciembre 18 de 2012)";

$titulo = "Telefonos de Medicos Disponibles";
// Se muestra el encabezado del programa
encabezado($titulo,$wactualiz, "clinica");
echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';


/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/


/*
------------------------------------
-----------------------------------
*/
//------- campos ocultos

echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
//----------------------------

// tabla principal
echo"<table id='tabla_principal' width='500' align='center'>";
echo "<tr align='left' id='tr_primer' ><td class='encabezadoTabla' >Seleccione Especialidad</td><td  align='left' class='fila1'>";
$q= "    SELECT Espnom,Espcod "
	."     FROM movhos_000044  "
	."    WHERE  Esphdi = 'on' "
	." ORDER BY Espnom";

$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<select id='select_especialidad' onchange='traemedicos(this)'>";
echo "<option value=''>seleccione</option>";

while ($row = mysql_fetch_array($res))
{
	echo " <option value = '".$row['Espcod']."'>".$row['Espnom']."</option>";
}

echo "</td></tr>";
echo "</table>";
echo "<br><br>";

//-- div de Impresion de Resultados
echo "<div id='div_datos' align='center'></div>";
//--
//--boton cerrar
echo  "<div id='div_botoncerrar' align='center'><input type='button' value='Cerrar' onclick='javascript:window.close();' ></div>";
//--

?>
</body>
</html>