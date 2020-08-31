<?php
include_once("conex.php");






include_once("root/comun.php");
include_once("funciones_talhuma.php");
include_once("root/magenta.php");

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fechaactual= date('Y-m-d');

if($woperacion=='creargruponotas')
{
	echo"<table>";
	echo"<tr>";
	echo"<td class='encabezadoTabla'>Nombre del Grupo</td><td class='fila1'><input type='text' id='nombredelgrupo' /></td>";
	echo"</tr>";
	echo"<tr>";
	echo"<td class='fila1' colspan='2' align='center'><input type='button' value='Crear' onclick='guardarNombre()'></td>";
	echo"</tr>";
	echo"<table>";
	return;
}

if($woperacion=='traegruponotas')
{

//tabla principal, contiene todas las demas , organizando la estructura

$q = " SELECT  DISTINCT(Notgru) "
    ."  FROM ".$wbasedato."_000047"
	." WHERE Nottde='".$wtiponota."'";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	

   

echo "<table class='fila1' id='tabppal' width='500' align='center' >";
echo "<tr>";
echo "<td width='300' align='left' >Grupos de Notas Actuales</td><td align='left'><select width='200' id='selecttiponotas'onchange='traeNotas()'>";
echo"<option value='seleccione' >Seleccione</option>";
while($row =mysql_fetch_array($res))
{
	echo"<option value='".$row['Notgru']."' >".$row['Notgru']."</option>";
}

echo"</select>";
echo "</td></tr>";
echo "<tr><td>";
echo "<input type='button' value='Crear Nuevo' onClick='creargrupo()'>";
echo "</td></tr>";
echo "</table>";


return;
}


if ($woperacion=='nuevonombre' )
{
	echo"<table id='NuevoTipo'>";
	echo"<tr class = 'encabezadoTabla'><td>";
	echo"Nombre del Tipo";
	echo"</td></tr>";
	echo"<tr class='fila1'>";
	echo"<td><input id='Nomb' type='text' />";
	echo"</td></tr>";
	echo"</table>";
	return;
}

if ($woperacion=='crearnotas' )
{
	echo"<table id='NuevaRespueta'>";
	echo"<tr class = 'encabezadoTabla'><td colspan='2'>";
	echo"Agregar Nueva Respuesta";
	echo"</td></tr>";
	echo"<tr class='fila1'>";
	echo"<td>Descripcion</td><td><input id='descripcionnuevoelemento' type='text' />";
	echo"</td></tr>";
	echo"<tr class='fila2'>";
	echo"<td>Valor</td><td><input type='text' id='valornuevoelemento' />";
	echo"</td></tr>";
	echo"<tr class='fila1'>";
	echo"<td>Icono</td><td><input type='text' id='nuevoelementoicono' />";
	echo"</td></tr>";
	echo"<tr class='fila2'>";
	$q = "SELECT Carcod,Carnom"
		."  FROM  root_000085 ";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	
	echo"<td>Caracteristica</td><td><Select id='caracteristicanuevoelemento'>";
	
	while($row =mysql_fetch_array($res))
	{
	
		echo"<option value='".$row['Carcod']."'>".$row['Carnom']."</option>";
	
	}
	
	echo"</select></td>";
	echo"</tr>";
	echo"<tr class='fila2'>";
	echo"<td colspan='2'><input type='button' value='Grabar' onclick='nuevarespuesta()' /></td>";
	echo"</tr>";
	echo"</table>";
	return;
}

if ($woperacion=='nuevanota' )
{
	if($woperacion2=='insertar')
	{
		$q = "SELECT MAX(Notcod) as mayor"
			."  FROM ".$wbasedato."_000047 "
			." WHERE Notgru = '".$wgruponota."' ";
			
		
			
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
		$row =mysql_fetch_array($res);
		
		$codigodenota = (($row['mayor'])+1)*1;
		
		
		$q  = "INSERT INTO ".$wbasedato."_000047 (Notdes,Notval,Notcar,Notgru,Nottde,Notima,Notcod) "
			. "     VALUES ('".$wdesnota."','".$wdesval."','".$wdescar."','".$wgruponota."' ,'".$wtiponota."','".$wicono."','".$codigodenota."') ";
		

		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	}
	
	
	$q  =  "SELECT Notdes,Notval,Notima "
		.  "  FROM   ".$wbasedato."_000047 "
		.  " WHERE Notgru = '".$wgruponota."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$i=0;
	while($row =mysql_fetch_array($res))
	{
		$descriptor[$i] = $row['Notdes'];
		$valores[$i] = $row['Notval'];
		$caracteristicas[$i] = $row['Notima'];
		
		$i++;
	}
	
	echo"<table><tr><td align='right' onclick='agregarCalifacion()' style='font-size:8pt ; color:gray'>agregar</td></tr></table>";
	echo"<div id='borderNotas' class='borderDiv'>";
	echo"<table id='Notas'>";
	
	
	

	echo"<tr class='fila1'>";
	$j=0;
	while ($j<$i)
	{
		echo"<td style= 'background-color : white;'>&nbsp;&nbsp;</td><td align='center'>".$descriptor[$j]."</td>";
		
		
		$j++;
	}
		
	echo "</tr>";
	echo "<tr>";
	$j=0;
	while ($j<$i)
	{
		echo"<td style= 'background-color : white;'>&nbsp;&nbsp;</td><td align='center'>".$valores[$j]."</td>";
		
		
		$j++;
	}
	
	echo "</tr>";
	echo "<tr>";
	$j=0;
	while ($j<$i)
	{
		echo"<td style= 'background-color : white;'>&nbsp;&nbsp;</td><td align='center'><img src='".$caracteristicas[$j]."'></td>";
		$j++;
	}
	
	echo "</tr>";
	echo "</table>";
	echo"</div>";
	return;
}


?>
<head>
  <title>Crear Notas</title>
  
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

<script type="text/javascript">

function MuestraGruposNotas()
{

	var tiponota = $('#selecttipo').val();
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
		
	var params   = 'TiposdeNotas.php?consultaAjax=&woperacion=traegruponotas&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wtiponota='+tiponota;
		$.get(params, function(data) {
		$('#NombredeGrupo').html('');
		$('#NuevasRespuestas').html('');
		$('#NotasGuardadas').html('');
		$('#divppal').html(data);
		
	});

}

function agregarCalifacion()
{

	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var params   = 'TiposdeNotas.php?consultaAjax=&woperacion=crearnotas&wemp_pmla='+emp_pmla+'&wtema='+tema;
		$.get(params, function(data) {
		$('#NuevasRespuestas').html(data);
		
	});
}
function guardarNombre()
{
	var nombregrupo=$('#nombredelgrupo').val();
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	
	$('#NombredeGrupo').html('<table><tr><td style="color: #2A5DB0; font-size:16pt" id="titulogrupo">'+nombregrupo+'</td></tr></table>');
	
	var params   = 'TiposdeNotas.php?consultaAjax=&woperacion=crearnotas&wemp_pmla='+emp_pmla+'&wtema='+tema;
		$.get(params, function(data) {
		$('#NuevasRespuestas').html(data);
		
	});

}


function traeNotas()
{
	var gruponota = $('#selecttiponotas').val();
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	
	$('#NombredeGrupo').html('<table><tr><td id="titulogrupo" style="color: #2A5DB0; font-size:16pt"><b>'+$('#selecttiponotas').val()+'<b></td></tr></table>');
	
	var params   = 'TiposdeNotas.php?consultaAjax=&woperacion=nuevanota&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wgruponota='+gruponota+'&woperacion2=no';
		$.get(params, function(data) {
		$('#NotasGuardadas').html(data);
		
	});
	
}



function nuevarespuesta()
{
	
	var des = $('#descripcionnuevoelemento').val();
	var val = $('#valornuevoelemento').val();
	var car = $('#caracteristicanuevoelemento').val();
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var gruponota = $('#titulogrupo').text();
	var tipodescriptor = $('#selecttipo').val();
	var icono =$('#nuevoelementoicono').val();
	
	
	var params   = 'TiposdeNotas.php?consultaAjax=&woperacion=crearnotas&wemp_pmla='+emp_pmla+'&wtema='+tema;
		$.get(params, function(data) {
		$('#NuevasRespuestas').html(data);
		
	});
	
	
	var params   = 'TiposdeNotas.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wtema='+tema+'&woperacion=nuevanota&wdesnota='+des+'&wdesval='+val+'&wdescar='+car+'&woperacion2=insertar&wgruponota='+gruponota+'&wtiponota='+tipodescriptor+'&wicono='+icono;

		$.get(params, function(data) {
		$('#NotasGuardadas').html(data);
	});
}


function creargrupo()
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	
	var params   = 'TiposdeNotas.php?consultaAjax=&woperacion=creargruponotas&wemp_pmla='+emp_pmla+'&wtema='+tema;

	$.get(params, function(data) {
		$('#NombredeGrupo').html(data);
		$('#NuevasRespuestas').html('');
		$('#NotasGuardadas').html('');
	});

}




	
</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 3px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
</style>

<body>

<?php
  /*********************************************************
   *               VISUALIZACION DE RELACIONES             *
   *                                                       *
   *     				                        		   *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : RealizarListaEncuestas.php
//AUTOR                      : Felipe Alvarez Sanchez
//
//FECHA CREACION             : Septiembre 20 de 2012
//FECHA ULTIMA ACTUALIZACION :
 
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//   Este Programa realiza un registro de personas a encuestar de una lista total de pacientes  en cada servicio
//   De esta manera se facilita la realizacion de las encuestas															                    \\        
//========================================================================================================================================\\
//========================================================================================================================================\\
//                                                                           \\
//========================================================================================================================================\\

echo "<input type='hidden' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' id='wemp_pmla' value='".$wemp_pmla."'>";


$q = "SELECT Tdecod,Tdedes "
	."  FROM root_000083";
	
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	
echo "<table align='center' width='500' id='escalas'>";
echo "<tr class='encabezadoTabla' width='500'><td align='left'>Seleccione Tipo de Nota</td><td align='left' width='200'><select id='selecttipo' onchange='MuestraGruposNotas()'>";
echo "<option value='seleccione'>Seleccione</option>";
while($row =mysql_fetch_array($res))
{

	echo "<option value='".$row['Tdecod']."'>".$row['Tdecod']."-".$row['Tdedes']."</option>";
	
}
echo "</select></td></tr>";

echo "</table>";


echo "<br>";
echo "<br>";
echo "<br>";
echo "<div id='divppal' width='600' align='center'>";
echo "</div>";
echo "<br>";
echo "<br>";
echo "<br>";


echo "<table align='center'>";
echo "<tr><td>";
echo "<div id='NombredeGrupo'  >";

echo "</div>";
echo "</td></tr>";

echo "</table>";


echo "<table align='center'>";
echo "<tr><td>";
echo "<div id='NuevasRespuestas' >";

echo "</div>";
echo "</td></tr>";
echo "<tr><td>";
echo "<div id='NotasGuardadas' >";

echo "</div>";
echo "</td></tr>";
echo "</table>";




?>
</body>