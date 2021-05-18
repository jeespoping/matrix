<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");

$wbasedatoMov 	= consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
global $wemp_pmla;

if($operacion == 'grabatelefono' )
{
	
	$q= "  UPDATE ".$wbasedatoMov."_000048 "
	   ."	  SET Medtel = '".$wtelefono."' "	
	   ."   WHERE Meddoc = '".$wcodigo."' ";
	   
	$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

if($operacion == 'grababiper' )
{
	
$q= "  UPDATE ".$wbasedatoMov."_000048 "
   ."	  SET Medbip = '".$wbiper."' "	
   ."   WHERE Meddoc = '".$wcodigo."' ";
   
$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

return;

}
if($operacion == 'traermedicos' )
{
	
$q= "  SELECT Medno1, Medno2, Medap1, Medap2 ,Meddoc  "
   ."    FROM ".$wbasedatoMov."_000048 "		
   ."   WHERE Medesp LIKE '%".$wespecialidad."%' "
   ."     AND Medest = 'on' "
   ."     AND Medhdi = 'on' "
   ."ORDER BY Medno1, Medno2, Medap1 " ;
   
$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo "<tr align='left' class='xxx' id='tr_segundo'  ><td class='encabezadoTabla'>Seleccione Medico </td><td align='left' class='fila1'>";
echo "<select id='select_medico' onchange='traedatos(this)'>";
echo "<option value=''>seleccione</option>";
while ($row = mysql_fetch_array($res)) 
{
	echo "<option value = '".$row['Meddoc']."'>".$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2']."</option>";
}
echo "</select>";
echo "</td></tr>";
		   
return;
}

if($operacion == 'traedatos' )
{
	
$q= "  SELECT Medno1, Medno2, Medap1, Medap2 ,Meddoc, Medtel,Medbip  "
   ."    FROM ".$wbasedatoMov."_000048 "		
   ."   WHERE Meddoc = '".$wcodigo."' ";
   
$res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


$row = mysql_fetch_array($res);

echo "<table id = 'tabladatos' align='center' ><tr align='center'><td colspan='2' class='encabezadoTabla' > ".$row['Medno1']." ".$row['Medno2']." ".$row['Medap1']." ".$row['Medap2']."</td></tr>";
echo "<tr align='center'  ><td class='encabezadoTabla'>Telefono:</td><td class='fila1'><input type='text' id='telefono' value='".$row['Medtel']."'  onblur='grabatelefono()'/></td></tr> ";
echo "<tr align='center' ><td class='encabezadoTabla'>Biper:</td><td class='fila1'><input type='text' id='biper' value='".$row['Medbip']."' onblur='grababiper()'/></td></tr> ";
echo "</table>";
		   
return;
}

?>

<html>
<head>
<title>Maestro Telefonos</title>
 <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script type='text/javascript'>
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
		
		$.get("telefonos_medicos.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traermedicos',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wespecialidad	: $('#select_especialidad').val()
			}
			, function(data) {
				$('#tabla_principal').append(data)
			});
			
	}
	

}

function grabatelefono()
{
$.get("telefonos_medicos.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'grabatelefono',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wtelefono		: $('#telefono').val(),
				wcodigo 		: $('#select_medico').val()
			}
			, function(data) {
		
			});


}
function grababiper()
{
$.get("telefonos_medicos.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'grababiper',
				wemp_pmla		: $('#wemp_pmla').val(),
				wuse			: $('#wuse').val(),
				wbiper			: $('#biper').val(),
				wcodigo 		: $('#select_medico').val()
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
		
		$.get("telefonos_medicos.php",
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

$wactualiz = "(Diciembre 17 de 2012)";

$titulo = "MAESTRO TELEFONOS MEDICOS";
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
$q= " SELECT Espnom,Espcod "
	."  FROM ".$wbasedatoMov."_000044  "
	." WHERE  Esphdi = 'on' ";
	
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
echo "<div id='div_datos' align='center'></div>";

?>
</body>
</html>
