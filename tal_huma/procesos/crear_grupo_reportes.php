<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");
include_once("funciones_talhuma.php");


global $wemp_pmla;


$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fecha= date("Y-m-d");
$hora = date("H:i:s");

if($inicial=='no' AND $operacion=='guardarformula')
{

	$infoCampo 	= explode("_", $campo);
	$formula 	= utf8_decode(str_replace('\\', '', $formula));

	// --> Actualizar formula
	if($id != 'nuevo')
	{
		$wagrupacion;
		$wtemaagrupacion;
		// $sqlAsignarFor = "
		$q =  "UPDATE ".$wbasedato."_000051
				  SET Gruetb ='".$wformula."'
				WHERE Grutem='".$wtemaagrupacion."'
				AND Grucod='".$wagrupacion."' ";
		mysql_query($q, $conex) or die("<b>ERROR EN QUERY MATRIX(q):</b><br>".mysql_error());
	}



	echo "Fórmula guardada";
	return;
}

if($inicial=='no' AND $operacion=='muestraagrupaciones2')
{
	//datos
	$q = "	SELECT Grucod,Grunom  "
		."	  FROM ".$wbasedato."_000051 "
		."   WHERE  Grutem = '".$wtemaagrupacion."' "
		."     AND  Grucag 	= '".$wclasificacion."'
		  ORDER BY  Grunom";



	//Nota: no tiene aun filtro , el filtro seria por tema, hay que agregar campo en la base de datos

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	echo"<tr>";
	echo"<td width='200' class='encabezadoTabla' align='left'>Seleccione Agrupación</td>";
	echo"<td width='150' class='fila1' align='left'>";
	echo"<select id='select_agrupacion'  onchange='cargarDescriptoresAgrupacion()'>";

	echo"<option value='nada' selected>Seleccione</option>";

	while($row = mysql_fetch_array($res))
	{
		echo"<option value='".$row['Grucod']."'>".($row['Grunom'])."</option>";
	}

	echo"</select>";
	echo"</td>";
	echo"<td class='fila1' align='right' width='150'>";
	echo"<input type='button' value='Nuevo' onclick='nuevaAgrupacion(\"div_nuevaagrupacion\")' ><input type='button' value='Eliminar' onclick='eliminaragrupacion()' >";
	echo"</td>";
	echo"</tr>";

	return;
}

if($inicial=='no' AND $operacion=='crearagrupacionesxdefecto')
{


	return;
}


if($inicial=='no' AND $operacion=='muestraagrupaciones')
{

	$q = "SELECT Cagcod, Cagdes"
		."  FROM ".$wbasedato."_000057
		ORDER BY Cagdes";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo"<table id='tabla_agrupacion'  width='500' align='center'  border='0' cellspacing='0' cellpadding='0'><tr>";
	echo"<td width='200' class='encabezadoTabla' align='left'>Seleccione Clasificación</td>";
	echo"<td width='150' class='fila1' align='left'>";
	echo"<select id='select_clasificacion'  onchange='cargarAgrupaciones()'>";

	echo"<option value='' selected>Seleccione</option>";

	while($row = mysql_fetch_array($res))
	{
		echo"<option value='".$row['Cagcod']."'>".($row['Cagdes'])."</option>";
	}

	echo"</select>";
	echo"</td>";
	echo"<td class='fila1' align='right' width='150'>";
	echo"<input type='button' value='Nuevo' onclick='nuevaclasificacion(\"div_nuevaclasificacion\")' ><input type='button' value='Eliminar' onclick='eliminarclasificacion()' >";
	echo"</td>";
	echo"</tr>";



	echo"</table>";

	return;


}

if($inicial=='no' AND $operacion=='eliminaragrupacion')
{
		$q = "UPDATE  ".$wbasedato."_000005 "
			."   SET Desagr ='' "
			." WHERE Desagr ='".$wagrupacion."' ";



		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$q =  "DELETE "
			 ."  FROM ".$wbasedato."_000051"
			 ." WHERE Grutem='".$wtemaagrupacion."' "
			 ."   AND Grucod='".$wagrupacion."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		return;

}

if($inicial=='no' AND $operacion=='eliminarclasificacion')
{
		$q = "SELECT Grucod "
			." FROM ".$wbasedato."_000051"
			." WHERE Grucag = '".$wclasificacion."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



		while($row = mysql_fetch_array($res))
		{
			$q1 = "UPDATE  ".$wbasedato."_000005 "
				."   SET Desagr ='' "
				." WHERE Desagr ='".$row['Grucod']."' ";


			$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
		}


		$q =  "DELETE "
			 ."  FROM ".$wbasedato."_000051"
			 ." WHERE  Grucag ='".$wclasificacion."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());



		$q =  "DELETE "
			 ."  FROM ".$wbasedato."_000057"
			 ." WHERE  Cagcod='".$wclasificacion."' ";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		return;

}

if ($inicial=='no' AND $operacion=='guardarNuevaClasificacion' )
{
	$q = "SELECT MAX(Cagcod * 1) AS Cagcod  "
		."  FROM ".$wbasedato."_000057";



	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$nuevocodigo = (($row['Cagcod']*1));
	echo $nuevocodigo;
	$nuevocodigo = (($row['Cagcod']*1) + 1);

	$q = "INSERT INTO ".$wbasedato."_000057 "
		."            ( Cagcod,
						Cagdes,
						Cagest,
						Medico,
						Seguridad,
						Fecha_data,
						Hora_data)"
		."		VALUES( '".$nuevocodigo."',
						'".utf8_decode($wcodigoclasificacion)."',
						'on',
						'".$wbasedato."',
						'C-".$wbasedato."',
						'".$fecha."',
						'".$hora."')";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

if ($inicial=='no' AND $operacion=='guardarNuevaAgrupacion' )
{
	$q = "SELECT MAX(Grucod * 1) AS Grucod  "
		."  FROM ".$wbasedato."_000051";



	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$nuevocodigo = (($row['Grucod']*1));
	echo $nuevocodigo;
	$nuevocodigo = (($row['Grucod']*1) + 1);



	$q = "INSERT INTO ".$wbasedato."_000051 "
		."            ( Grucod,
						Grunom,
						Grusem,
						Grutem,
						Grucag,
						Gruest,
						Medico,
						Seguridad,
						Fecha_data,
						Hora_data)"
		."		VALUES( '".$nuevocodigo."',
						'".utf8_decode($codigoagrupacion)."',
						'".$codigosemaforo."',
						'".$codigotema."',
						'".$codigoclasificacion."',
						'on',
						'".$wbasedato."',
						'C-".$wbasedato."',
						'".$fecha."',
						'".$hora."')";

	echo $q;



	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;

}

if ($inicial=='no' AND $operacion=='cargaDescriptoresAgregar')
{

	$q  = "SELECT Descod, Desdes "
		. "  FROM ".$wbasedato."_000005 "
		. " WHERE Desagr='' "
		. "   AND Descom='".$wcompetencia."'"
		."    AND Destip!='09'";

	// 09 son los tipos de datos que no son tenidos en cuenta en los informes entonces no deben ni salir
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	$i=1;
	echo "<table>";
	while($row = mysql_fetch_array($res))
	{
		echo "<tr><td>";
		echo "<div  id='div_descriptoragregado_".$row['Descod']."'>";
		echo "<table align='left'><tr><td><input id='".$row['Descod']."' type='checkbox'   onclick='grabarDescriptorAgrupacion(this)' ></td><td></td><td style='font-size:12px'>".$row['Desdes']."</td></tr></table>";
		echo "</div>";
		echo "</td></tr>";
		$i++;
	}
	echo "</table>";
	return;
}

if ($inicial=='no' AND $operacion=='grabarDescriptorAgrupacion')
{
	$q = "UPDATE  "
		." ".$wbasedato."_000005 "
		."   SET Desagr ='".$wagrupacion."' "
		." WHERE Descod = '".$wdescriptor."'";


	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$q 	="	SELECT Descod, Desdes"
		."	  FROM ".$wbasedato."_000005 "
		."	 WHERE Descod = '".$wdescriptor."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	$row = mysql_fetch_array($res);
	$i=1;
	echo "<div  id='div_descriptor_".$row['Descod']."'>";
	echo "<table><tr><td style='font-size:12px' align='left' width='40'></td><td><input id='".$row['Descod']."' type='checkbox'  checked  onclick='eliminardescriptor(this)' ></td><td></td><td style='font-size:12px'  align='left'>".$row['Desdes']."</td></tr></table>";
	echo "</div>";

	return;
}

if($inicial=='no' AND $operacion=='cargarFormulaAgrupacion')
{
	if ($wtema =='05')
	{
		$caracter_ok 		= array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
		$caracter_ma 		= array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");
		$formulaEnPantalla 	= array();
		$formulaTexto		= '';


			$sqlformula = "SELECT  Gruetb
							 FROM ".$wbasedato."_000051
							WHERE Grutem='".$wtemaagrupacion."'
							  AND Grucod='".$wagrupacion."' ";

			$res = mysql_query($sqlformula,$conex) or die ("Error 55: ".mysql_errno()." - en el query: ".$sqlformula." - ".mysql_error());

			if($rowFormula = mysql_fetch_array($res))
			{
				if($rowFormula['Gruetb']!='')
				{
					$formulaEnPantalla = $rowFormula['Gruetb']	;

					// --> Codificar tildes y caracteres especiales
					foreach($rowFormula as $indice => &$valor)
						$valor = utf8_encode($valor);

					$rowFormula['Gruetb'] = str_replace($caracter_ma, $caracter_ok, $rowFormula['Gruetb']);
					$rowFormula['Gruetb'] = str_replace($caracter_ma, $caracter_ok, $rowFormula['Gruetb']);

					$formulaEnPantalla = json_decode($formulaEnPantalla, true);

					foreach($formulaEnPantalla as $indice => &$valores)
					{
						$formulaTexto.= $valores["nombre"];
						$valores["nombre"] = str_replace($caracter_ma, $caracter_ok, $valores["nombre"]);
					}
				}
			}



		echo "
		<div id='accordionFormula' align='center' style='font-family: verdana;font-weight: normal;font-size: 10pt;'>
				<div>
				<table style='padding:5px;margin:5px;'>
					<tr>
						<td width='30%'>
							<input type='hidden' id='formulaEnPantalla' value='".json_encode($formulaEnPantalla)."'>
							<table>
								<tr>
									<td colspan='2' align='left' ><input type='button' class='botoncalculadora' style='width:93px;' value='Borrar' onclick='borrarCalc(\"ultimo\")'></td>
									<td colspan='2' align='right'><input type='button' class='botoncalculadora' style='width:93px;' value='Limpiar' onclick='borrarCalc(\"todos\")'></td>
								</tr>
								<tr>
									<td colspan='2' align='left' ><input colspan='2' class='botoncalculadora' type='button' onclick='calculadora(this)' valor='Agrupacion' value='Agrupacion' tipo='Operador' tabla='' periodo=''></td>
									<td colspan='2' align='right'></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='7' value='7' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='8' value='8' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='9'	value='9' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='/' value='/' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='4' value='4' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='5' value='5' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='6' value='6' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='*' value='*' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='1' value='1' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='2' value='2' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='3' value='3' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='-' value='-' tipo='Operador' tabla='' periodo=''></td>
								</tr>
								<tr>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='0' value='0' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='(' value='(' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor=')' value=')' tipo='Operador' tabla='' periodo=''></td>
									<td><input class='botoncalculadora' type='button' onclick='calculadora(this)' valor='+' value='+' tipo='Operador' tabla='' periodo=''></td>
								</tr>

							</table>
						</td>
						<td width='70%' style='vertical-align:text-top;' align='left'>
							<br><br>
							<fieldset align='center' style='padding:15px;'>
								<legend class='fieldset'>formula</legend>
								<div id='displayCalculadora' style='height:190px;overflow:auto;background:none repeat scroll 0 0;'>
								".$formulaTexto."
								</div>
							</fieldset>
						</td>
					</tr>
					<tr>
						<td colspan='2' align='right'>
							<br>
							<div id='div_mensajes' class='bordeCurvo fondoAmarillo' style='display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div>
						</td>
					</tr>
					<tr><td align='center' colspan='2'><br><button style='font-family: verdana;font-weight:bold;font-size: 9pt;' onclick='guardarFormula(\"".((isset($rowFormula['id'])) ? $rowFormula['id'] : 'nuevo')."\")'>Guardar</button></td></tr>
				</table>
			</div>
		</div>";
	}
		return;

}
if($inicial=='no' AND $operacion=='cargarDescriptoresAgrupacion')
{
	$q 	="	SELECT * "
		."	  FROM ".$wbasedato."_000005 "
		."	 WHERE Desagr = '".$wagrupacion."'
		  ORDER BY Desdes";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());


	$i=1;

	while($row = mysql_fetch_array($res))
	{
		echo "<div  id='div_descriptor_".$row['Descod']."'>";
		echo "<table><tr><td style='font-size:12px' width='40'></td><td><input id='".$row['Descod']."' type='checkbox'  checked  onclick='eliminardescriptor(this)' ></td><td></td><td style='font-size:12px' align='left'>".$row['Desdes']."</td></tr></table>";
		echo "</div>";
		$i++;
	}




	return;
}

if ($inicial=='no' AND $operacion=='desagrupaDescriptor')
{

	$q 	="	UPDATE   ".$wbasedato."_000005 "
		."     SET Desagr ='' "
		."	 WHERE Descod = '".$wdescriptor."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	return;
}

?>

<html>
<head>
<title>Crear Maestros Reportes</title>

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<script type='text/javascript'>

function crearagrupacionesxdefecto()
{


	$.post("crear_grupo_reportes.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		operacion		: 'crearagrupacionesxdefecto',
		wemp_pmla		: $('#wemp_pmla').val(),
		wtema           : $('#wtema').val(),
		wuse			: $('#wuse').val(),
		wclasificacion	:  $('#select_clasificacion').val(),
		wagrupacion		: $('#select_agrupacion').val(),
		wtemaagrupacion	: $('#select_tema').val()

	}
	, function(data) {

	});

}

function cargarAgrupaciones(nombreseccion)
{


	wtemaagrupacion = $('#select_tema').val();
	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'muestraagrupaciones2',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemaagrupacion	: wtemaagrupacion,
				wclasificacion	:  $('#select_clasificacion').val()

			}
			, function(data) {
			$('#div_descriptores').html('');
			if($('#tabla_agrupacion tr').length!=2	)
			{
				$('#tabla_agrupacion').append(data);
			}
			else
			{
				$('#tabla_agrupacion tr:last').remove();
				$('#tabla_agrupacion').append(data);
			}
	});

}

function verSeccion(nombreseccion)
{
	var wtemaagrupacion ;

	wtemaagrupacion = $('#select_tema').val();


	if (wtemaagrupacion =='seleccione' )
	{
		$('#div_agrupacion').html('');
	}
	else
	{
		$.get("crear_grupo_reportes.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'muestraagrupaciones',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wtemaagrupacion	: wtemaagrupacion

				}
				, function(data) {
				$('#div_agrupacion').html(data)

		});
	}

}

function eliminardescriptor(elemento)
{
	var descriptor=elemento.id;

	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'desagrupaDescriptor',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wdescriptor		: elemento.id

			}
			, function(data) {

	});

	$('#div_descriptor_'+descriptor).remove();

	// falta eliminar de base de datos y dismunuir numero

}

function fnMostrar2( celda )
{

	if( $('#'+celda ) )
	{
		var ancho = $('#'+celda).width();
		var alto = $('#'+celda).height();

		if (celda == 'div_nuevosdescriptores')
			alto =450;



		ancho = (ancho*1)+100;


		//alto = (alto*1)+100;
		$.blockUI({ message: $('#'+celda ),
						css: { left: ( $(window).width() - 1000 )/2 +'px',
							  top: '100px',
							  width: ancho + 50,
							  height: alto
							 }
				  });

		if(celda=='div_nuevosdescriptores')
		{
			$('#nombreAgrupacionAgregar').val($('#select_agrupacion').val());
		}
	}
}

function nuevaclasificacion (div)
{
	fnMostrar2(div);
}


function nuevaAgrupacion(div)
{
	// alert($('#select_clasificacion').val());
	if(	$('#select_clasificacion').val()=='')
	{
	alert("Debe seleccionar una agrupacion");
	}
	else
	{
	fnMostrar2(div);
	}
}


function  grabaNuevaAgrupacion ()
{

	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'guardarNuevaAgrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				codigoagrupacion: $('#text_nuevaagrupacion').val(),
				codigosemaforo	: $('#select_semaforo').val(),
				codigotema		: $('#select_tema').val(),
				codigoclasificacion : $('#select_clasificacion').val()

			}
			, function(data) {
				verSeccion();
	});



}
function calculadora(elemento)
{

		var formulaEnPantalla 	= new Object();
		formulaEnPantalla		= JSON.parse($("#formulaEnPantalla").val());
		var nuevoIndex			= formulaEnPantalla.length;
		var textoPantalla		= '';


		if(elemento != '')
		{
			var elemento 							= $(elemento);
			formulaEnPantalla[nuevoIndex] 			= new Object();
			formulaEnPantalla[nuevoIndex].tipo 		= elemento.attr("tipo");

			if(elemento.attr("operadorLogico") == 'si')
				formulaEnPantalla[nuevoIndex].nombre 	= elemento.find("option:selected").attr("nombre");
			else
				formulaEnPantalla[nuevoIndex].nombre 	= ((elemento.attr("nombre") == undefined) ? elemento.attr("valor") : elemento.attr("nombre"));

			formulaEnPantalla[nuevoIndex].valor 	= ((elemento.attr("operadorLogico") == 'si') ? elemento.find("option:selected").val() : elemento.attr("valor") );
			// formulaEnPantalla[nuevoIndex].tabla		= elemento.attr("tabla");
			// formulaEnPantalla[nuevoIndex].periodo	= elemento.attr("periodo");
		}

		$(formulaEnPantalla).each(function(index, objeto){
			textoPantalla = textoPantalla+objeto.nombre;
		});

		$("#displayCalculadora").html(textoPantalla);
		$("#formulaEnPantalla").val(JSON.stringify(formulaEnPantalla));






}

function borrarCalc(tipo)
{
	var formulaEnPantalla 	= new Object();
	formulaEnPantalla		= JSON.parse($("#formulaEnPantalla").val());

	if(tipo == 'ultimo')
	{
		ultimoIndex	= formulaEnPantalla.length-1;
		formulaEnPantalla.splice(ultimoIndex, 1);
	}
	else
		formulaEnPantalla 	= new Array();

	$("#formulaEnPantalla").val(JSON.stringify(formulaEnPantalla));
	calculadora("");
}

function guardarFormula()
{
	$.get("crear_grupo_reportes.php",
	{
		consultaAjax 	: '',
		inicial			: 'no',
		operacion		: 'guardarformula',
		wemp_pmla		: $('#wemp_pmla').val(),
		wtema           : $('#wtema').val(),
		wuse			: $('#wuse').val(),
		wagrupacion		: $('#select_agrupacion').val(),
		wtemaagrupacion	: $('#select_tema').val(),
		wformula		: $("#formulaEnPantalla").val()

	}
	, function(data) {

	});

}

function grabaNuevaclasificacion ()
{

	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'guardarNuevaClasificacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wcodigoclasificacion : $('#text_nuevaclasificacion').val()

			}
			, function(data) {
				verSeccion();
				$('#text_nuevaclasificacion').val('') ;
	});



}


function  cargaDescriptoresAgregar ()
{
	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'cargaDescriptoresAgregar',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				codigoagrupacion: $('#text_nuevaagrupacion').val(),
				wcompetencia	: $('#select_ncompetencias').val()

			}
			, function(data) {
			$('#div_agregardescriptores').html(data);
	});



}

function grabarDescriptorAgrupacion (elemento)
{


	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'grabarDescriptorAgrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wagrupacion		: $('#select_agrupacion').val(),
				wcompetencia	: $('#select_ncompetencias').val(),
				wdescriptor		: elemento.id


			}
			, function(data) {
			$('#div_descriptores').append(data);
	});
	var descriptor=elemento.id;
	$('#div_descriptoragregado_'+descriptor).remove();

}

function cargarDescriptoresAgrupacion()
{
	$('#div_nombreagrupacion').show();
	$('#div_descriptores').show();
	$('#div_formula').show();


	if ($('#select_agrupacion').val() =='nada')
	{


		 $('#div_descriptores').hide();
		 $('#div_nombreagrupacion').hide();
	}
	else
	{
		$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'cargarDescriptoresAgrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wagrupacion		: $('#select_agrupacion').val()


			}
			, function(data) {



			if($('#select_agrupacion').val() =='nada')
			{

				$('#div_descriptores').html('');
			}
			else
			{
				$('#div_descriptores').html(data);
			}
			});


			if($('#wtema').val()=='05');
			{
				$.get("crear_grupo_reportes.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'cargarFormulaAgrupacion',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wagrupacion		: $('#select_agrupacion').val(),
					wtemaagrupacion	: $('#select_tema').val(),


				}
				, function(data) {

				if($('#select_agrupacion').val() =='nada')
				{
					$('#div_formula').html('');
				}
				else
				{
					$('#div_formula').html(data);
				}
				});
			}
	}

}

function eliminaragrupacion()
{
	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'eliminaragrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wagrupacion		: $('#select_agrupacion').val(),
				wtemaagrupacion	: $('#select_tema').val()

			}
			, function(data) {
				verSeccion();
				$('#div_descriptores').html('');
			});




}

function eliminarclasificacion()
{

	$.get("crear_grupo_reportes.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'eliminarclasificacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wagrupacion		: $('#select_agrupacion').val(),
				wtemaagrupacion	: $('#select_tema').val(),
				wclasificacion	: $('#select_clasificacion').val()

			}
			, function(data) {
				verSeccion();
				$('#div_descriptores').html('');
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
</style>

</head>
<body>

<?php


/**
 PROGRAMA                   : Crear_grupo_reportes.php
 AUTOR                      : Felipe Alvarez Sanchez.
 FECHA CREACION             : Noviembre 05  de 2012

 DESCRIPCION:				: Programa que agrupa a los descriptores para asi formar reportes de la misma indole


 ACTUALIZACIONES:


**/
$wactualiz = "(Junio 28 de 2012)";

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
//----------------------------

//tabla principal
echo"<table id='tabla_principal' width='900' align='center'>";
//primer tr : titulo
echo"<tr><td>";

echo"<div id='div_titulo' align='center'>";

echo"<table width='900' align='Center'  border='0' cellspacing='0' cellpadding='0'>";
echo"<tr align='Center'>";
echo"<td><b>CREAR AGRUPACIÓN DE DESCRIPTORES</b></td>";
echo"</tr>";
echo"</table>";
echo"<br>";
echo"<br>";
echo"<br>";

echo"</div>";
echo"</td></tr>";

//segundo tr: Select tema.
echo"<tr><td>";

//datos select
$q = "	SELECT  Forcod, Fordes "
	."	  FROM  ".$wbasedato."_000042 "
	."   WHERE Fortip='01' "
	."      OR Fortip='03' "
	."      OR Fortip='04'
			OR Fortip='05'
	  ORDER BY Fordes";



// Nota: solo esta funcionando para evaluaciones internas Fortip=01 y para encuestas usuarios registrados Fortip=03
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo"<div id='div_tema' align='center'>";
echo"<table width='500' align='center'  border='0' cellspacing='0' cellpadding='0'>";
echo"<tr>";
echo"<td width='200' class='encabezadoTabla' align='Center' >Seleccione Tema</td>";
echo"<td  width='300' class='fila1' align='Left'>";
echo"<select id='select_tema' onchange='verSeccion(\"div_agrupacion\")'>";

echo"<option value='seleccione' selected>Seleccione</option>";

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

// Tercer tr: visualizacion de tema y crear nuevo grupo
echo"<tr><td>";


echo"<div id='div_agrupacion'  align='center'>";



echo"</div>";

echo"</td></tr>";
echo"<br>";
echo"<br>";
echo"<br>";
// Cuarto tr: div de descriptores
echo"<tr><td>";
//datos





echo "</div>";
echo"</td></tr>";
// quinto tr: div de descriptores
echo"<tr><td><br>";
echo "<div id='div_formula'   style='display:none'>";


echo "</div>";
echo"<div id='div_nombreagrupacion' width='900'  style='display:none' >";
echo"<br>";
echo"<br>";
echo"<table width='900'>";
echo"<tr>";
echo"<td align='center'  colspan='2'><b>DESCRIPTORES EN LA AGRUPACIÓN</b></td>";
echo"</tr>";
echo"<tr>";
echo"<td align='center' colspan='2'><input type='button' value='Agregar Descriptor' onclick='nuevaAgrupacion(\"div_nuevosdescriptores\")'></td>";
echo"</tr>";
echo"</table>";

echo"</div>";

echo "<div id='div_descriptores' class='borderDiv displ'  style='display:none'>";
echo "</div>";
echo"</td></tr>";


echo"</table>";

//div ocultos

//-------------------------------------------------------------
//---------div para agregar nueva agrupacion-------------------
echo "<div id='div_nuevaagrupacion' class='fila2' align='middle'  style='display:none;width:100;cursor:default' >";

echo "<br><br>";

echo"<table align='center' style='border:#2A5DB0 1px solid'>";

echo"<tr class='encabezadoTabla'>";
echo"<td align='Center' colspan='2'>CREAR NUEVA AGRUPACIÓN</td>";
echo"</tr>";
echo"<tr class='fila1'>";
echo"<td align='Left' ><b>Nombre de Agrupación:<b></td>";
echo"<td align='Left' ><input type='text' id='text_nuevaagrupacion'></td>";
echo"</tr>";

echo"<tr class='fila1' align='Left'>";
echo"<td><b>Semaforo:<b></td>";
echo"<td><select id='select_semaforo'>";
$q = "SELECT Msenom, Msecod"
	."  FROM ".$wbasedato."_000052 ";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

while($row = mysql_fetch_array($res))
{
	echo"<option value='".$row['Msecod']."'>".$row['Msenom']."</option>";
}

echo "</select></td>";
echo"</tr>";



echo"<tr class='fila2' align='center' >";
echo"<td colspan='2' align='center' >";
echo"<input type='button' value='Grabar' onClick='grabaNuevaAgrupacion(); $.unblockUI()' style='width:100'>";
echo"<input type='button' value='Cancelar' onClick='$.unblockUI();' style='width:100'>";
echo"</td>";
echo"</tr>";

echo"</table>";

echo"<br><br>";

echo"</div>";


echo "<div id='div_nuevaclasificacion' class='fila2' align='middle'  style='display:none;width:100;cursor:default' >";

echo "<br><br>";

echo"<table align='center' style='border:#2A5DB0 1px solid'>";

echo"<tr class='encabezadoTabla'>";
echo"<td align='Center' colspan='2'>CREAR NUEVA CLASIFICACION</td>";
echo"</tr>";
echo"<tr class='fila1'>";
echo"<td align='Left' ><b>Nombre de clasificación:<b></td>";
echo"<td align='Left' ><input type='text' id='text_nuevaclasificacion'></td>";
echo"</tr>";
echo"<tr class='fila2' align='center' >";
echo"<td colspan='2' align='center' >";
echo"<input type='button' value='Grabar' onClick='grabaNuevaclasificacion(); $.unblockUI()' style='width:100'>";
echo"<input type='button' value='Cancelar' onClick='$.unblockUI();' style='width:100'>";
echo"</td>";
echo"</tr>";

echo"</table>";

echo"<br><br>";

echo"</div>";
//------------------------------------------------------
//------------------------------------------------------

//------------------------------------------------------
//-----div agregar descriptores-------------------------

echo "<div id='div_nuevosdescriptores' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0; overflow:auto; height: 600 ' >";
echo "<input id='nombreAgrupacionAgregar' type='hidden'>";
echo "<br><br>";

echo "<div id='div_contenedor_table' style='overflow: scroll; height: 400px;' >";
echo"<table align='center' id='div_nuevosdescriptores_table' name='div_nuevosdescriptores_table' style='border:#2A5DB0 1px solid'>";

echo"<tr class='encabezadoTabla'>";
echo"<td align='Center' colspan='1'>AGREGAR DESCRIPTORES</td>";
echo"</tr>";

// echo"<tr class='fila1'>";
// echo"<td align='Left' colspan='1' ><b>AGRUPACIÓN<b></td>";
// echo"</tr>";
echo"<tr class='fila1'>";
echo"<td colspan='1' align='Left' ><div id=td_nombreagrupacion></div></td>";
echo"</tr>";
echo"<tr class='fila1'>";
echo"<td colspan='1' align='Left' ><b>DESCRIPTORES<b></td>";
echo"</tr>";
echo"<tr class='encabezadoTabla'>";

echo"<td colspan='1' align='Left' >Competencia</td>";
echo"</tr>";
echo"<tr class='encabezadoTabla'>";



//-------Select Competencias
echo"<td colspan='1' align='Left' >";

$q =  "SELECT Comcod,Comdes"
	 ."  FROM ".$wbasedato."_000004
	 ORDER BY Comdes";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo"<select id='select_ncompetencias' onchange='cargaDescriptoresAgregar()'>";
	echo"<option value='' selected >Seleccione</option>";
while($row = mysql_fetch_array($res))
{
	echo"<option value='".$row['Comcod']."'>".$row['Comdes']."</option>";
}
echo"</select>";
echo"</td>";
//--------------------------

echo"</tr>";
echo"<tr class='fila1'>";
echo"<td colspan='1' align='center' >";
echo"<div id=div_agregardescriptores>";


echo"</div>";
echo"</td>";
echo"</tr>";
echo"<tr class='fila2' align='center' >";
echo"<td colspan='1' align='center' >";
echo"<input type='button' value='Cancelar' onClick='$.unblockUI();' style='width:100'>";
echo"</td>";
echo"</tr>";

echo"</table>";

echo "</div>";

echo"<br><br>";

echo"</div>";


//------------------------------------------------------
//------------------------------------------------------


?>

</body>
</html>

