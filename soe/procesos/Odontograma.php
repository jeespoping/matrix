<html>
<head>
  <title>MATRIX Odontograma</title>
  <link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>
  <script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>
  <script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>
  <script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
    <style type="text/css">
		A	{text-decoration: none;color: #000066;}
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo3D{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo3TW{color:#000066;background:#dddddd;font-size:1pt;font-family:Arial;font-weight:bold;text-align:center;border-style:none;display:none;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo9A{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipoT02{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:110em;text-align:left;height:2em;}
    	.tipo3G{color:#FFFFFF;background:#0B615E;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:left;}

    	#tipo10{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10A{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo10B{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10W{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;height:2em;}

    	#tipo10C{color:#000066;background:#999999;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10D{color:#000066;background:#dddddd;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10E{color:#000066;background:#999999;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo10F{color:#000066;background:#dddddd;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:left;}

    	#tipo10G{color:#000066;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo10H{color:#000066;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10I{color:#000066;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10J{color:#000066;background:#999999;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10JJ{color:#000066;background:#999999;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:right;}

    	#tipo10H1{color:#000066;background:#99CCFF;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo10H2{color:#000066;background:#99CCFF;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10N1{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo10N2{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10I1{color:#000066;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo10I2{color:#000066;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo10I3{color:#000066;background:#999999;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo11{color:#000066;background:#cccccc;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#cccccc;font-size:13pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12W{color:#000000;background:#CED9FF;font-size:13pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12V{color:#000000;background:#CED9FF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12A{color:#000000;background:#CED9FF;font-size:11pt;font-family:Tahoma;font-color:#FFFFFF;font-weight:bold;text-align:center;border-style:outset;}
    	#tipo12B{color:#000066;background:#cccccc;font-size:11pt;font-family:Tahoma;font-color:#000066;font-weight:normal;text-align:center;border-style:outset;}
    	#tipo12C{color:#000066;background:#CED9FF;font-size:13pt;font-family:Tahoma;font-color:#000066;font-weight:bold;text-align:center;height:1.5em;}
    	#tipo12D{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;border-style:hidden;text-align:center;}
    	#tipo13{color:#000066;background:#cccccc;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo14{color:#000066;background:#FF0000;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo16A{color:#000066;background:#CED9FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16B{color:#000066;background:#CED9FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo16C{color:#000066;background:#CED9FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}

    	#tipo17A{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17B{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo17C{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	#tipo17D{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}


    	#tipo18A{color:#000000;background:#99CCFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo18B{color:#000000;background:#CC99FF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo18C{color:#000000;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}

    	#tipo19{color:#000066;background:#cccccc;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo20{color:#000066;background:#cccccc;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:right;}

    	#tipo21A{color:#000000;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo21B{color:#000000;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo21C{color:#000000;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}

    	#tipo22A{color:#000000;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo22B{color:#000000;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo22C{color:#000000;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}

    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:2em;text-align:center;height:2em;}
    	#tipoG02{color:#000066;background:#FF0000;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1em;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066;}
    	#tipoG03{color:#000066;background:#000099;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1em;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066;}
    	#tipoG04{color:#000066;background:#00FFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:1em;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066;}
    	#tipoG05{color:#000066;background:#cccccc;font-size:7pt;font-family:Tahoma;font-weight:bold;width:2.5em;text-align:center;height:2.5em;}
    	#tipoG06{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;width:2.5em;text-align:center;height:2.5em;}
    	#tipoG07{color:#000066;background:#cccccc;font-size:6pt;font-family:Arial;font-weight:bold;width:1em;text-align:center;height:1em;}

    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:2.5em;}

    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2.5em;}

    	.tipoGRIDT{color:#000066;background:#E0E0E0;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.tipoGRIDTR{color:#000066;background:#FFDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.tipoGRIDO{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;}
    	.tipoGRIDC{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.tipoGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipo01GRID{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}
    	.tipo01GRIDL{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:bold;text-align:left;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}
		.tipo02GRID{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}
		.tipo03GRID{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}
		.tipo04GRID{color:#000066;background:#DDDDDD;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}
		.tipo05GRID{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}
		.tipo02GRIDR{color:#000066;background:#FFDDDD;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:1em;border-style:solid;border-collapse:collapse;border-width:1px;border-color:#000066}

		.color1{color:#000066;background:#CC99FF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
		.color2{color:#000066;background:#99CCFF;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}


    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Odontograma.submit();
	}
	function cerrarVentana()
	{
		window.close()
	}
	function ejecutar(path,tipo)
	{
		if(tipo == 1)
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=425');
		else
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=580');
	}
	function tooltip(pos)
	{
		$('#SP[pos] *').tooltip();
	}
	function toggleDisplay(id)
	{
		if (id.style.display=="none")
		{
			id.style.display="";
		}
		else
		{
			id.style.display="none";
		}
	}
//-->
</script>

<?php
include_once("conex.php");
/**********************************************************************************************************************
[DOC]
	   PROGRAMA : Odontograma.php
	   Fecha de Liberacion : 2007-05-03
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2016-06-01

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite grabar los  de las
	   la Historia Clinica Odontologica, los presupuestos y la facturacion en linea, el motivo de consulta y
	   los diagnosticos, los mensages de abono a la cuenta y una consulta a la cartera por odontologo tanto de
	   las facturas de particulares como de los cargos pendientes de facturar.


	   REGISTRO DE MODIFICACIONES :
       .2018-10-23 Camilo Zapata: modificación de script para que los cargos en la sección de presupuesto se graben al centro de costos asociado al funcionario
                                  que los graba (tabla_318)
	   .2016-06-01
			*	Se modifica el programa para incluir la tabla 247 de registro cronologico de antecedentes odontologicos.
			*   Se cambia la forma de presentación del presupuesto.
			*   Se incluye la posibilidad de consultar el registro cronologico de motivos de consulta
			*   Se incluye la posibilidad de consultar el registro cronologico de diagnosticos
	   .2015-03-19
			*	Se modifica la tabla de disgnosticos odontologicos de 228 a 243.

	   .2014-12-16
			*	Se modifica la grabacion del motivo de Consulta para que no dependa de la existencia de la tabla
			* 	root_000011 y de la existencia del codigo MC01.

	   .2014-12-09
			Se realizan las siguientes modificaciones en el programa:
			* 	1. Se adiciona al motivo de consulta y al diagnostico el odontologo que registra la información.
			*   2. Se habilita el programa para que grabe los comentarios asociados a actividades realizadas en
			*      las piezas dentales, esta siempre se grababa en ".".
			*   3. Se corrige bug que no permitia grabar la actividad descrita en el input text cuando se selecciona
			*      la opcion de otra.
			*   4. Se adiciono la opcion de grabar diagnosticos repetidos a una misma historia e ingreso pero en fecha
			*      diferentes.
			*   5. Se modifico la presentacion de la tabla de abonos en la pestaña de presupuestos mostrando en tres
			*      columnas lo abonado, los facturado de ese abono y el saldo.
			*   6. Se modifico el programa para que cuando se grabara el primer registro de una historia ingreso para
			*      una fecha y odontologo, la fecha de trabajo se posicionara en esta ultima y no en la anteriormente
			*      seleccionada. Esto daba la sensacion de que la informacion no estaba quedando grabada.

	   .2014-10-15
			Se modifica la grabacion del consentimiento informado ya que la validacion la estaba haciendo x dia.

	   .2014-09-22
			Se realizan las siguientes modificaciones en el programa:
			* 	1. Se cambia la presentacion del odontograma mostrando todas las superficies y el reporte de actividades
			*      en forma de de acordion.
			*   2. Se adicionan las pestañas de Datos Personales que muestra la informacion del ingreso y
			*      la pestaña de antecedentes que estaba en un program aparte.
			*   3. La pestaña de Diagnosticos y Motivo de consulta se partio en dos pestañas separadas.
			*   4. Se adiciono en la pestaña de Presupuestos y Facturacion una tabla con la informacion de los Abonos.

	   .2013-05-16
			Se modifica el programa para validar la direccion IP donde se esta llenando la historia odontologica.
			Se valida con la tabla 95 de root.

	   .2013-02-11
			Se modifica el programa para ordenar el ingreso del paciente de forma numerica y no alafanumerica.

	   .2013-01-22
			Se modifica el programa para NO mostrar la palabra descuento en los titulos del programa.

	   .2013-01-18
			Se modifica el programa para mostrar en presupuestacion solamente los grupo de facturacion activos.

	   .2010-06-29
	   		Se modifico el programa para cambiar el algoritmo de busqueda de la actividades realizadas vs las pendientes
	   		ya que el algoritmo anterior en algunos casos no era capaz de realizar el maching.
	   		En el cambio de decidio buscar en todas las actividades de la pieza dental.

	   .2010-05-14
	   		Se modifico el programa para mostrar en el encabezado del programa el nombre de la tarifa de la tabla 25.

	   .2009-06-17
	   		Se modifico el programa en el item estado de cuenta para mostrar los cargos grabados en facturacion
	   		asociados al odontologo que se encuentra interactuando con el programa. La columna de tercero desaparece
	   		de la tabla de presupuestos al igual que en el item de presupuestos y facturacion.
	   		Se cambia la presentacion de totales en le item de estado de cuenta del paciente.
	   		Se cambia los cargos de odontologos asociados a facturas particulares con saldo por facturas particulares
	   		pendientes de recaudo.

	   .2009-06-11
	   		Se modifico el programa en el item estado de cuenta para mostrar el medico responsable del presupuesto.
	   		La impresion del subprograma (5) Estado de Cuenta se cambio para que en caso de que el usuario registrado
	   		no sea odontologo no aparezca en la columna de terceros.

	   .2009-06-02
	   		Se modifico el programa en el subproceso de saldos o estado de cuenta para mostrar de forma consolidada
	   		los saldo presupuestados, pendientes de facturacion y facturados pendientes de recaudo.
	   		Adicionalmente se modifico la grabacion en la tabla 106 de cargos para incluir el campo de tarifa ya
	   		sea x convenio o tarifa de ingreso.

	   .2009-05-27
	   		Se modifico el programa para permitir grabar datos  a pacientes inactivos. Del modulo de facturacion
	   		a los pacientes inactivos solamente se les puede generar cargos, no se les puede presupuestar nada.

	   .2009-05-13
	   		Se modifico el programa para excluir los cargos anulados del calculo de la seccion de saldos.

	   .2009-03-25
			** Se modifico la consulta a la tabla 131 de presupuestos agrupando x procedimientos para poder grabarlo,
			en la tabla 110 de detalle de procedimientos al egreso.

	   .2009-03-27
	   		Se modifico el programa en la seleccion de pacientes activos de la tabla 100 y 101 para traer todos los
	   		ingresos en orden descendente de numero de visita.

	   .2007-05-03
	   		Release de Version Beta.
[*DOC]
***********************************************************************************************************************/
function insert_log_ant($empresa,$conex,$his,$cod,$des,$vaa,$vac,$usu,$fecha,$hora)
{
	$query = "select fecha_data,hora_data, Lanhis, Lancod  from ".$empresa."_000247 ";
	$query .= " where fecha_data = '".$fecha."' ";
	$query .= "   and hora_data = '".$hora."' ";
	$query .= "   and Lanhis = '".$his."' ";
	$query .= "   and Lancod = '".$cod."' ";
	$err3 = mysql_query($query,$conex);
	$num3 = mysql_num_rows($err3);
	if($num3 == 0)
	{
		$query = "insert ".$empresa."_000247 (medico,fecha_data,hora_data, Lanhis, Lancod, Landes, Lanvaa, Lanvac, Lanusu, seguridad) values ('soe','".$fecha."','".$hora."','".$his."','".$cod."','".$des."','".$vaa."','".$vac."','".$usu."','C-soe')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 247 LOG DE ANTECEDENTES : ".mysql_errno().":".mysql_error());
	}
}
function array_s($item,&$actividad,$k,$ubicacion)
{
	$it1=substr($item,0,strpos($item," "))." Real.";
	for ($j=0;$j<=$k;$j++)
	{
		if($it1 == $actividad[$j][3] and $actividad[$j][7] == 0 and $actividad[$j][4] == $ubicacion)
		{
			$actividad[$j][7]=1;
			return true;
		}
	}
	return false;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function buscar(&$thoot,$diente,$fecha,$hora,$actividad,&$p1)
{
	for ($i=0;$i<=$p1;$i++)
		if($thoot[$i][0] == $diente and $thoot[$i][1] == $fecha and $thoot[$i][2] == $hora and $thoot[$i][3] == $actividad)
			return true;
}
function Dias($f)
{
	$aa=(integer)substr($f,0,4)*360 +(integer)substr($f,5,2)*30 + (integer)substr($f,8,2);
	$ann=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
	$ann1=($ann - $aa)/360;
	$meses=(($ann - $aa) % 360)/30;
	if ($ann1<1)
	{
		$dias1=(($ann - $aa) % 360) % 30;
		if((integer)$meses != 0)
			$Dias=(string)(integer)$meses." Mes(es) ".(string)$dias1." Dia(s)";
		else
			$Dias=(string)$dias1." Dia(s)";

	}
	else
	{
		$dias1=(($ann - $aa) % 360) % 30;
		$Dias=(string)(integer)$ann1." A&ntilde;o(s) ".(string)(integer)$meses." Mese(s) ".(string)$dias1." Dia(s)";
	}
	return $Dias;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="/^([=a-zA-Z0-9' 'ñÑ@\/#-.;_<>])+$/";
	return (preg_match($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="/^([0-9:])+$/";
	return (ereg($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un dato email
	return (filter_var($chain, FILTER_VALIDATE_EMAIL));
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Odontograma' action='Odontograma.php' method=post>";
	if(isset($addX))
		unset($add);




	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	$IPOK=0;
	$query = "select Dipnip, Dipusu from root_000095 ";
	$query .= " where Dipest = 'on'";
	$err_ip = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num_ip = mysql_num_rows($err_ip);
	if ($num_ip>0)
	{
		for ($h=0;$h<$num_ip;$h++)
		{
			$rowip = mysql_fetch_array($err_ip);
			if(($rowip[0] == substr($IIPP,0,strlen($rowip[0])) and $key == $rowip[1]) or ($rowip[0] == substr($IIPP,0,strlen($rowip[0])) and $rowip[1] == "*"))
			{
				$IPOK=1;
				$i=$num_ip+1;
			}
		}
	}
	if($IPOK > 0)
	{
		$query = "select Meddoc, Mednom, Medpor  from ".$empresa."_000051 ";
		$query .= " where Medusu = '".$key."' ";
		$err3 = mysql_query($query,$conex);
		$num3 = mysql_num_rows($err3);
		if($num3 > 0)
		{
			if(isset($wegrT))
			{
				$query = "select Parmei, Pardxi, Pareta, Parcae, Parmee, Partdx, Pardxn, Parpro, Paresp, Parser from ".$empresa."_000120 where Parcco='".$wsei."' and parest='on' ";
				$errA = mysql_query($query,$conex) or die("ERROR CONSULTANDO ARCHIVO DE PARAMETROS INGRESO AMBULATORIO : ".mysql_errno().":".mysql_error());
				$numA = mysql_num_rows($errA);
				if ($numA > 0)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "lock table ".$empresa."_000100 LOW_PRIORITY WRITE, ".$empresa."_000108 LOW_PRIORITY WRITE, ".$empresa."_000109 LOW_PRIORITY WRITE, ".$empresa."_000110 LOW_PRIORITY WRITE, ".$empresa."_000111 LOW_PRIORITY WRITE, ".$empresa."_000112 LOW_PRIORITY WRITE, ".$empresa."_000131 LOW_PRIORITY WRITE  ";
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
					$rowA = mysql_fetch_array($errA);
					$query = "insert ".$empresa."_000108 (medico,fecha_data,hora_data, Egrhis, Egring, Egrmei, Egrdxi, Egrfee, Egrhoe, Egrest, Egrcae, Egrmee, Egrcex, Egrtdp, Egrcom, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$key."','".$rowA[1]."','".$fecha."','".$hora."',".$rowA[2].",'".$rowA[3]."','".$key."','13','1','off','C-soe')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 108 EGRESOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					$wswdx=0;
					for ($i=1;$i<=$se1;$i++)
					{
						if($wdata1[$i][0] == "02")
						{
							$wswdx++;
							if($wswdx == 1)
								$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data, Diahis, Diaing, Diacod, Diatip, Dianue, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$wdata1[$i][1]."','".$rowA[5]."','".$rowA[6]."','C-soe')";
							else
								$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data, Diahis, Diaing, Diacod, Diatip, Dianue, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$wdata1[$i][1]."','S','".$rowA[6]."','C-soe')";
							$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 DIAGNOSTICOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
						}
					}
					if($wswdx == 0)
					{
						$query = "insert ".$empresa."_000109 (medico,fecha_data,hora_data, Diahis, Diaing, Diacod, Diatip, Dianue, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$rowA[1]."','".$rowA[5]."','".$rowA[6]."','C-soe')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 109 DIAGNOSTICOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					}
					$wswpr=0;
					//                 0       1
					$query = "select Ptopro, Ptonpr   from ".$empresa."_000131 ";
					$query .= " where Ptohis = '".$whis."' ";
					$query .= "   and Ptoing = '".$wing."' ";
					$query .= " Group by 1 ";
					$err_se = mysql_query($query,$conex);
					$numse = mysql_num_rows($err_se);
					if($numse > 0)
					{
						for ($i=1;$i<=$numse;$i++)
						{
							$rowSE = mysql_fetch_array($err_se);
							$wswpr++;
							$query = "insert ".$empresa."_000110 (medico,fecha_data,hora_data, Prohis, Proing, Procod, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$rowSE[0]."','C-soe')";
							$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 110 PROCEDIMIENTOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
						}
					}
					if($wswpr == 0)
					{
						$query = "insert ".$empresa."_000110 (medico,fecha_data,hora_data, Prohis, Proing, Procod, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$rowA[7]."','C-soe')";
						$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 110 PROCEDIMIENTOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					}
					$query = "insert ".$empresa."_000111 (medico,fecha_data,hora_data, Esphis, Esping, Espcod, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$rowA[8]."','C-soe')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 111 ESPECIALIDADES AMBULATORIAS : ".mysql_errno().":".mysql_error());
					$query = "insert ".$empresa."_000112 (medico,fecha_data,hora_data, Serhis, Sering, Sercod, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$rowA[9]."','C-soe')";
					$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO EN 112 SERVICIOS AMBULATORIOS : ".mysql_errno().":".mysql_error());
					$query = "UPDATE ".$empresa."_000100 set Pacact='off' where Pachis = '".$whis."'";
					$err1 = mysql_query($query,$conex) or die("ERROR INACTIVANDO PACIENTE EN ARCHIVO 100 : ".mysql_errno().":".mysql_error());
					$query = " UNLOCK TABLES";
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
					unset($paciente);
				}
			}
			if((isset($ok1) and $actividad != "" and $W != ".") or isset($assent))
			{
				$ubicacion="N/A";
				if(isset($assent))
				{
					$query = "select count(*) from ".$empresa."_000130 where Fecha_data = '".date("Y-m-d")."' and Actividad = 'CONSENTIMIENTO INFORMADO' and Identificacion = '".$paciente."' and Odontologo = '".$key."' ";
					$err1 = mysql_query($query,$conex);
					$row1 = mysql_fetch_array($err1);
					if($row1[0] == 0)
					{
						$actividad = "CONSENTIMIENTO INFORMADO";
						$W = "SE LE INFORMA Y EXPLICA AL PACIENTE EL CONSENTIMIENTO INFORMADO Y ESTE PROCEDE A FIRMARLO";
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000130 (medico,fecha_data,hora_data, Identificacion, Fecha, Hora, Diente, Actividad, Ubicacion, Comentarios, Odontologo, seguridad) values ('soe','".$fecha."','".$hora."','".$paciente."','".$fecha."','".$hora."',99,'".$actividad."','".$ubicacion."','".$W."','".$key."','C-soe')";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					}
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000130 (medico,fecha_data,hora_data, Identificacion, Fecha, Hora, Diente, Actividad, Ubicacion, Comentarios, Odontologo, seguridad) values ('soe','".$fecha."','".$hora."','".$paciente."','".$fecha."','".$hora."',99,'".$actividad."','".$ubicacion."','".$W."','".$key."','C-soe')";
					$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
				unset($ok);
				unset($R);
			}

			if(isset($ok))
			{
				$wpre=0;
				$E=array();
				$row=array();
				$row[0]  ="--------------------------------------------------------------------------------------------------------------------------------------";
				$row[1]  ="....18......17......16......15......14......13......12......11....||....21......22......23......24......25......26......27......28....";
				$row[2]  ="....ve......ve......ve......ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve......ve......ve......ve....";
				$row[3]  ="..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..";
				$row[4]  ="....li......li......li......li......li......li......li......li....||....li......li......li......li......li......li......li......li....";
				$row[5]  ="..................................................................||..................................................................";
				$row[6]  ="............................55......54......53......52......51....||....61......62......63......64......65............................";
				$row[7]  ="............................ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve............................";
				$row[8]  ="..........................di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..........................";
				$row[9]  ="............................li......li......li......li......li....||....li......li......li......li......li............................";
				$row[10] ="--------------------------------------------------------------------------------------------------------------------------------------";
				$row[11] ="............................85......84......83......82......81....||....71......72......73......74......75............................";
				$row[12] ="............................li......li......li......li......li....||....li......li......li......li......li............................";
				$row[13] ="..........................di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..........................";
				$row[14] ="............................ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve............................";
				$row[15] ="..................................................................||..................................................................";
				$row[16] ="....48......47......46......45......44......43......42......41....||....31......32......33......34......35......36......37......38....";
				$row[17] ="....li......li......li......li......li......li......li......li....||....li......li......li......li......li......li......li......li....";
				$row[18] ="..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..";
				$row[19] ="....ve......ve......ve......ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve......ve......ve......ve....";
				$row[20] ="--------------------------------------------------------------------------------------------------------------------------------------";
				$num=21;
				for ($i=0;$i<$num;$i++)
					for ($j=0;$j<67;$j++)
					{
						$E[$i][$j]=substr($row[$i],0,2);
						$row[$i] = substr($row[$i],2);
					}
				for ($h=0;$h<$num;$h++)
					for ($l=0;$l<67;$l++)
						if(isset($M[$h-1][$l]))
						{
							if(($e != "Otra : " or ($e == "Otra : " and $A1 != "")) and (isset($U[0]) or isset($U[1]) or isset($U[2]) or isset($U[3]) or isset($U[4]) or isset($U[5]) or isset($U[6]) or isset($U[7])))
							{
								if(substr($e,0,6) == "Otra :")
									$actividad=$A1;
								else
									$actividad=$e;
								$ubicacion="";
								if(isset($U[6]))
									$ubicacion="Todas";
								elseif(isset($U[7]))
											$ubicacion="N/A";
										else
										{
											if(isset($U[0]))
												$ubicacion .="Mesi/";
											if(isset($U[1]))
												$ubicacion .="Oclu/";
											if(isset($U[2]))
												$ubicacion .="Dist/";
											if(isset($U[3]))
												$ubicacion .="Vest/";
											if(isset($U[4]))
												$ubicacion .="Ling/";
											if(isset($U[5]))
												$ubicacion .="Cerv/";
										}
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");

								//$C=".";
								$query = "select count(*) from ".$empresa."_000130 where Fecha = '".$fecha."' and Identificacion = '".$paciente."' and Odontologo = '".$key."' ";
								$err1 = mysql_query($query,$conex);
								$row1 = mysql_fetch_array($err1);
								if($row1[0] == 0)
									$wfechat = $fecha;

								if(isset($PRE) and $wpre == 0)
								{
									$wpre=1;
									$C=" PREEXISTENTE -- ".$C;
								}
								$query = "insert ".$empresa."_000130 (medico,fecha_data,hora_data, Identificacion, Fecha, Hora, Diente, Actividad, Ubicacion, Comentarios, Odontologo, seguridad) values ('soe','".$fecha."','".$hora."','".$paciente."','".$fecha."','".$hora."','".$E[$h-2][$l]."','".$actividad."','".$ubicacion."','".$C."','".$key."','C-soe')";
								$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							}
						}
			}
			if(isset($ok))
				unset($R);
			if(!isset($paciente))
			{
				if(isset($numA))
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>ACTUALIZACION DE ODONTOGRAMA X PACIENTE</td></tr>";
					echo "<tr><td id=tipo18A><IMG SRC='/matrix/images/medical/root/feliz.ico'></TD><TD id=tipo18A>PACIENTE ".$dat." EGRESADO !!!!!</td></tr>";
					echo "</table><br><br></center>";
				}
				echo "<center><table border=0>";
				echo "<tr><td bgcolor=#FFFFFF align=center colspan=2><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
				echo "</table>";
			}
			else
			{
				if(isset($add))
				{
					?>
					<script>
						function ira(){document.Odontograma.add.focus();}
					</script>
					<?php
				}
				elseif(isset($addX))
					{
						?>
						<script>
							function ira(){document.Odontograma.addX.focus();}
						</script>
						<?php
					}
					elseif(isset($wfacT))
						{
							?>
							<script>
								function ira(){document.Odontograma.wfacT.focus();}
							</script>
							<?php
						}
						else
						{
							?>
							<script>
								function ira(){document.Odontograma.Data.focus();}
							</script>
							<?php
						}
				if(!isset($wsw1))
				{
					$wsw1=0;
					//                 0       1       2       3       4       5       6       7       8        9      10      11      12     13       14     15       16      17      18      19      20     21       22    23       24      25      26      27      28      29      30      31      32      33
					$query = "select Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pachis, Ingtar, Ingsei, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Ingcem, Ingent, Ingnin, Pacact, Paccea, Pacnoa, Pactea, Pacofi, Paciu, Pacbar, Pacdia, Pacpaa, Paccru, Pacnru, Pactru, Pacdru, Pacpru, Paccor, Pactus, Pactat from ".$empresa."_000100,".$empresa."_000101 ";
					$query .= " where pacdoc = '".$paciente."'";
					$query .= "   and pachis = inghis ";
					//$query .= "   and Pacact = 'on' ";
					$query .= " ORDER by CAST(Ingnin AS UNSIGNED) DESC ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTADO DATOS DEL PACIENTE : ".mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err);
						//DATA DATOS PERSONALES
						if(!isset($wdptdo))
						{
							$wdptdo = $row1[0];
							$wdpdoc = $row1[1];
							$wdpap1 = $row1[2];
							$wdpap2 = $row1[3];
							$wdpno1 = $row1[4];
							$wdpno2 = $row1[5];
							$wdphis = $row1[6];
							$wdping = $row1[16];
							$wdpest = $row1[11];
							$wdpofi = $row1[21];
							$wdpdir = $row1[12];
							$wdpciu = $row1[22];
							$wdpbar = $row1[23];
							$wdptel = $row1[13];
							$wdpcor = $row1[31];
							$wdpcem = $row1[14];
							$wdpent = $row1[15];
							$wdpcru = $row1[26];
							$wdpnru = $row1[27];
							$wdptru = $row1[28];
							$wdpdru = $row1[29];
							$wdppru = $row1[30];
							$wdpcea = $row1[18];
							$wdpnoa = $row1[19];
							$wdptea = $row1[20];
							$wdpdia = $row1[24];
							$wdppaa = $row1[25];
							$wdptus = $row1[32];
							$wdptat = $row1[33];
						}
						$wdpofiw = "";
						$wdpciuw = "";
						$wdpbarw = "";

						$estado = $row1[17];
						$nom=$row1[0]."-".$row1[1]." ".$row1[2]." ".$row1[3]." ".$row1[4]." ".$row1[5];
						$whis=$row1[6];
						$wtar=$row1[7];
						$wcea=$row1[18];
						$wnoa=$row1[19];
						$wtea=$row1[20];
						$query = "SELECT Tardes  from ".$empresa."_000025 where Tarcod='".$wtar."' ";
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if($num > 0)
						{
							$row = mysql_fetch_array($err);
							$wtarn=$row[0];
						}
						else
							$wtarn=$wtar;
						$wsei=$row1[8];
						$wing=$row1[16];
						$dat=$row1[6]."-".$row1[16];
						$wres=$row1[14]."-".$row1[15];
						if($row1[15] == "PARTICULAR")
							$wpart="on";
						else
							$wpart="off";
						$wfna=$row1[9];
						$weda=Dias($wfna);
						$query = "SELECT Seldes  from ".$empresa."_000105 where Selcod='".$row1[10]."' and Seltip='03' and Selest='on' ";
						$err = mysql_query($query,$conex);
						$row = mysql_fetch_array($err);
						$wsex=$row[0];
						$query = "SELECT Seldes  from ".$empresa."_000105 where Selcod='".$row1[11]."' and Seltip='04' and Selest='on' ";
						$err = mysql_query($query,$conex);
						$row = mysql_fetch_array($err);
						$wesc=$row[0];
						$wdir=$row1[12];
						$wtel=$row1[13];
						$query = "select Emptal from root_000050 ";
						$query .= " where Empbda = '".$empresa."' ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$wtal=$row1[0];
							$wsw1=1;
						}
					}
				}
				if($wsw1 > 0)
				{
					echo "<input type='HIDDEN' name= 'wsw1' value='".$wsw1."'>";
					echo "<input type='HIDDEN' name= 'nom' value='".$nom."'>";
					echo "<input type='HIDDEN' name= 'paciente' value='".$paciente."'>";
					echo "<input type='HIDDEN' name= 'estado' value='".$estado."'>";
					echo "<input type='HIDDEN' name= 'whis' value='".$whis."'>";
					echo "<input type='HIDDEN' name= 'wtar' value='".$wtar."'>";
					echo "<input type='HIDDEN' name= 'wtarn' value='".$wtarn."'>";
					echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
					echo "<input type='HIDDEN' name= 'wsei' value='".$wsei."'>";
					echo "<input type='HIDDEN' name= 'wfna' value='".$wfna."'>";
					echo "<input type='HIDDEN' name= 'weda' value='".$weda."'>";
					echo "<input type='HIDDEN' name= 'wsex' value='".$wsex."'>";
					echo "<input type='HIDDEN' name= 'wesc' value='".$wesc."'>";
					echo "<input type='HIDDEN' name= 'wdir' value='".$wdir."'>";
					echo "<input type='HIDDEN' name= 'wtel' value='".$wtel."'>";
					echo "<input type='HIDDEN' name= 'wtal' value='".$wtal."'>";
					echo "<input type='HIDDEN' name= 'wres' value='".$wres."'>";
					echo "<input type='HIDDEN' name= 'wpart' value='".$wpart."'>";
					echo "<input type='HIDDEN' name= 'dat' value='".$dat."'>";
					echo "<input type='HIDDEN' name= 'wcea' value='".$wcea."'>";
					echo "<input type='HIDDEN' name= 'wnoa' value='".$wnoa."'>";
					echo "<input type='HIDDEN' name= 'wtea' value='".$wtea."'>";
					switch ($DIV)
					{
						case 1:
							$tip1="tipo12A";
							$tip2="tipo12B";
							$tip3="tipo12B";
							$tip4="tipo12B";
							$tip5="tipo12B";
							$tip6="tipo12B";
							$tip7="tipo12B";
							$tip8="tipo12B";
						break;
						case 2:
							$tip1="tipo12B";
							$tip2="tipo12A";
							$tip3="tipo12B";
							$tip4="tipo12B";
							$tip5="tipo12B";
							$tip6="tipo12B";
							$tip7="tipo12B";
							$tip8="tipo12B";
						break;
						case 3:
							$tip1="tipo12B";
							$tip2="tipo12B";
							$tip3="tipo12A";
							$tip4="tipo12B";
							$tip5="tipo12B";
							$tip6="tipo12B";
							$tip7="tipo12B";
							$tip8="tipo12B";
						break;
						case 4:
							$tip1="tipo12B";
							$tip2="tipo12B";
							$tip3="tipo12B";
							$tip4="tipo12A";
							$tip5="tipo12B";
							$tip6="tipo12B";
							$tip7="tipo12B";
							$tip8="tipo12B";
						break;
						case 5:
							$tip1="tipo12B";
							$tip2="tipo12B";
							$tip3="tipo12B";
							$tip4="tipo12B";
							$tip5="tipo12A";
							$tip6="tipo12B";
							$tip7="tipo12B";
							$tip8="tipo12B";
						break;
						case 6:
							$tip1="tipo12B";
							$tip2="tipo12B";
							$tip3="tipo12B";
							$tip4="tipo12B";
							$tip5="tipo12B";
							$tip6="tipo12A";
							$tip7="tipo12B";
							$tip8="tipo12B";
						break;
						case 7:
							$tip1="tipo12B";
							$tip2="tipo12B";
							$tip3="tipo12B";
							$tip4="tipo12B";
							$tip5="tipo12B";
							$tip6="tipo12B";
							$tip7="tipo12A";
							$tip8="tipo12B";
						break;
						case 8:
							if(isset($DIVO))
							{
								$tip1="tipo12A";
								$tip2="tipo12A";
								$tip3="tipo12A";
								$tip4="tipo12A";
								$tip5="tipo12A";
								$tip6="tipo12A";
								$tip7="tipo12A";
								$tip8="tipo12A";
							}
							else
							{
								$tip1="tipo12B";
								$tip2="tipo12B";
								$tip3="tipo12B";
								$tip4="tipo12B";
								$tip5="tipo12B";
								$tip6="tipo12B";
								$tip7="tipo12B";
								$tip8="tipo12A";
							}
						break;
					}
					echo "<input type='HIDDEN' name= 'DIV' value='".$DIV."'>";
					if(isset($DIVO))
						echo "<input type='HIDDEN' name= 'DIVO' value='".$DIVO."'>";
					echo "<table border=0 align=center>";
					echo "<tr><td id=tipo12 align=center><IMG SRC='/MATRIX/images/medical/soe/Logo_soe.jpg' ></td><td id=tipo12W align=center valign=middle colspan=48>PROMOTORA MEDICA LAS AMERICAS S.A. - UNIDAD ODONTOLOGICA (SOE)</td><td id=tipo12V align=center valign=bottom><A HREF='/matrix/root/Reportes/DOC.php?files=../../soe/procesos/odontograma.php' target='_blank'> Ver. 2016-06-01</A></td></tr>";
					echo "<tr><td id=tipo9 colspan=50><b>PACIENTE : ".$nom." / HISTORIA Y NRO INGRESO : ".$dat."</b></td></tr>";
					echo "<tr><td id=tipo9 colspan=50><b>F. NACIMIENTO: ".$wfna." / EDAD: ".$weda." / SEXO: ".$wsex." / ESTADO CIVIL: ".$wesc."</b></td></tr>";
					echo "<tr><td id=tipo9 colspan=50><b>DIRECCION : ".$wdir." / TELEFONOS: ".$wtel."</b></td></tr>";
					echo "<tr><td id=tipo9 colspan=50><b>RESPONSABLE : ".$wres."</b></td></tr>";
					echo "<tr><td id=tipo9A colspan=50><b>DATOS DEL ACUDIENTE -- Identificacion : ".$wcea." Nombre : ".$wnoa." Telefonos : ".$wtea."</b></td></tr>";
					if($estado == "on")
						echo "<tr><td id=tipo9 colspan=50><b>ESTADO : ACTIVO / CODIGO TARIFA : ".$wtar."-".$wtarn."</b></td></tr>";
					else
						echo "<tr><td id=tipo9 colspan=50><b>ESTADO : INACTIVO / CODIGO TARIFA : ".$wtar."-".$wtarn."</b></td></tr>";
					echo "<tr><td bgcolor=#FFFFFF align=center colspan=2><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
					echo "</table><br><br>";
					echo "<table id=tipo12D cellspacing=1 align=center>";
					echo "<tr>";
					switch ($DIV)
					{
						case 1:
							echo " <td id=".$tip1." align=center>Datos Personales</td>";
							echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
							echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
							echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
							echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
							echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
							echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 2:
							echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
							echo " <td id=".$tip2." align=center>Antecedentes</td>";
							echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
							echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
							echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
							echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
							echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 3:
							echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
							echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
							echo " <td id=".$tip3." align=center>Motivo de Consulta</td>";
							echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
							echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
							echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
							echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 4:
							echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
							echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
							echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
							echo " <td id=".$tip4." align=center>Odontograma</td>";
							echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
							echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
							echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 5:
							echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
							echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
							echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
							echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
							echo " <td id=".$tip5." align=center>Presupuestos y Facturacion</td>";
							echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
							echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 6:
							echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
							echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
							echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
							echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
							echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
							echo " <td id=".$tip6." align=center>Diagnosticos</td>";
							echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 7:
							echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
							echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
							echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
							echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
							echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
							echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
							echo " <td id=".$tip7." align=center>Abonos y Mensajes</td>";
							echo " <td id=".$tip8." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=8'>Estado de Cuenta</A></td>";
						break;
						case 8:
							if(isset($DIVO))
							{
								echo " <td id=".$tip1." align=center>Datos Personales</tPactusd>";
								echo " <td id=".$tip2." align=center>Antecedentes</td>";
								echo " <td id=".$tip3." align=center>Motivo de Consulta</td>";
								echo " <td id=".$tip4." align=center>Odontograma</td>";
								echo " <td id=".$tip5." align=center>Presupuestos y Facturacion</td>";
								echo " <td id=".$tip6." align=center>Diagnosticos</td>";
								echo " <td id=".$tip7." align=center>Abonos y Mensajes</td>";
								echo " <td id=".$tip8." align=center>Estado de Cuenta</td>";
							}
							else
							{
								echo " <td id=".$tip1." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=1'>Datos Personales</A></td>";
								echo " <td id=".$tip2." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=2'>Antecedentes</A></td>";
								echo " <td id=".$tip3." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=3'>Motivo de Consulta</A></td>";
								echo " <td id=".$tip4." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=4'>Odontograma</A></td>";
								echo " <td id=".$tip5." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=5'>Presupuestos y Facturacion</A></td>";
								echo " <td id=".$tip6." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=6'>Diagnosticos</A></td>";
								echo " <td id=".$tip7." align=center><A HREF='/MATRIX/soe/Procesos/Odontograma.php?empresa=".$empresa."&paciente=".$paciente."&DIV=7'>Abonos y Mensajes</A></td>";
								echo " <td id=".$tip8." align=center>Estado de Cuenta</td>";
							}
						break;
					}
					echo "</tr>";
					echo "<tr>";
					echo " <td id=tipo12C align=center colspan=8></td>";
					echo "</tr>";
					echo "</table><br><br>";

					switch ($DIV)
					{
						case 1:
							//*************** DATOS PERSONALES ********************
							$werr = array();
							$e = -1;
							if(isset($okdp))
							{
								if(!validar4($wdptdo))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL TIPO DE DOCUMENTO";
								}
								if(!validar4($wdpdoc))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL DOCUMENTO";
								}
								if(!validar4($wdpno1))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL PRIMER NOMBRE";
								}
								if(!validar4($wdpno2))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL SEGUNDO NOMBRE";
								}
								if(!validar4($wdpap1))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL PRIMER APELLIDO";
								}
								if(!validar4($wdpap2))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL SEGUNDO APELLIDO";
								}
								if(!validar4($wdpest))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL ESTADO CIVIL";
								}
								if(!validar4($wdpofi))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL OFICIO";
								}
								if(!validar4($wdpdir))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO LA DIRECCION";
								}
								if(!validar4($wdpciu))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL MUNICIPIO";
								}
								if(!validar4($wdpbar))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL BARRIO";
								}
								if(!validar4($wdptel))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO LOS TELEFONOS";
								}
								if(strlen($wdpcor) > 0 and !validar6($wdpcor))
								{
									$e=$e+1;
									$werr[$e]="ERROR O NO DIGITO EL CORREO ELECTRONICO";
								}
								if($e < 0)
								{
									//echo "Actualizo<br>";
									$query =  " update ".$empresa."_000100 set Pactat = '".substr($wdptat,0,strpos($wdptat,"-"))."',";
									$query .=  "  Pacap1='".$wdpap1."',";
									$query .=  "  Pacap2='".$wdpap2."',";
									$query .=  "  Pacno1='".$wdpno1."',";
									$query .=  "  Pacno2='".$wdpno2."',";
									$query .=  "  Pacest='".substr($wdpest,0,strpos($wdpest,"-"))."',";
									$query .=  "  Pacdir='".$wdpdir."',";
									$query .=  "  Pactel='".$wdptel."',";
									$query .=  "  Paciu ='".substr($wdpciu,0,strpos($wdpciu,"-"))."',";
									$query .=  "  Pacbar='".substr($wdpbar,0,strpos($wdpbar,"-"))."',";
									$query .=  "  Pacofi='".substr($wdpofi,0,strpos($wdpofi,"-"))."',";
									$query .=  "  Paccor='".$wdpcor."',";
									$query .=  "  Paccea='".$wdpcea."',";
									$query .=  "  Pacnoa='".$wdpnoa."',";
									$query .=  "  Pactea='".$wdptea."',";
									$query .=  "  Pacdia='".$wdpdia."',";
									$query .=  "  Pacpaa='".$wdppaa."',";
									$query .=  "  Paccru='".$wdpcru."',";
									$query .=  "  Pacnru='".$wdpnru."',";
									$query .=  "  Pactru='".$wdptru."',";
									$query .=  "  Pacdru='".$wdpdru."',";
									$query .=  "  Pacpru='".$wdppru."' ";
									$query .=  "  where Pactdo='".substr($wdptdo,0,strpos($wdptdo,"-"))."' and Pacdoc='".$wdpdoc."'";
									$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PACIENTES : ".mysql_errno().":".mysql_error());
									$e=$e+1;
									$werr[$e]="OK! DATOS ACTUALIZADOS";
									$query = "select Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pachis, Ingtar, Ingsei, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Ingcem, Ingent, Ingnin, Pacact, Paccea, Pacnoa, Pactea, Pacofi, Paciu, Pacbar, Pacdia, Pacpaa, Paccru, Pacnru, Pactru, Pacdru, Pacpru, Paccor, Pactus, Pactat from ".$empresa."_000100,".$empresa."_000101 ";
									$query .= " where pacdoc = '".$wdpdoc."'";
									$query .= "   and pactdo = '".substr($wdptdo,0,strpos($wdptdo,"-"))."'";
									$query .= "   and pachis = inghis ";
									$query .= " ORDER by CAST(Ingnin AS UNSIGNED) DESC ";
									$err = mysql_query($query,$conex) or die("ERROR CONSULTADO DATOS DEL PACIENTE : ".mysql_errno().":".mysql_error());
									$num1 = mysql_num_rows($err);
									if($num1 > 0)
									{
										$row1 = mysql_fetch_array($err);
										$wdptdo = $row1[0];
										$wdpdoc = $row1[1];
										$wdpap1 = $row1[2];
										$wdpap2 = $row1[3];
										$wdpno1 = $row1[4];
										$wdpno2 = $row1[5];
										$wdphis = $row1[6];
										$wdping = $row1[16];
										$wdpest = $row1[11];
										$wdpofi = $row1[21];
										$wdpdir = $row1[12];
										$wdpciu = $row1[22];
										$wdpbar = $row1[23];
										$wdptel = $row1[13];
										$wdpcor = $row1[31];
										$wdpcem = $row1[14];
										$wdpent = $row1[15];
										$wdpcru = $row1[26];
										$wdpnru = $row1[27];
										$wdptru = $row1[28];
										$wdpdru = $row1[29];
										$wdppru = $row1[30];
										$wdpcea = $row1[18];
										$wdpnoa = $row1[19];
										$wdptea = $row1[20];
										$wdpdia = $row1[24];
										$wdppaa = $row1[25];
										$wdptus = $row1[32];
										$wdptat = $row1[33];
										$wdpofiw = "";
										$wdpciuw = "";
										$wdpbarw = "";
									}
								}
							}
							echo "<table border=0 align=center class=='tipoGRID' CELLSPACING=1px>";
							echo "<tr><td class='tipo03GRID' colspan=4>DATOS PERSONALES</td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Nro de Historia<br><input type='TEXT' name='wdphis' maxlength=10 value='".$wdphis."' readonly=readonly class='tipoGRIDO'></td><td class='tipo01GRID' colspan=2>Nro de Ingreso<br><input type='TEXT' name='wdping' maxlength=10 value='".$wdping."' readonly=readonly class='tipoGRIDO'></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Tipo de Documento<br>";
							$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='01' and Selest='on' and selcod = '".$wdptdo."' ";
							$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							if ($num>0)
							{
								$row = mysql_fetch_array($err);
								$wdptdo=$row[0]."-".$row[1];
							}
							else
								$wdptdo="0-NO DEFINIDO";
							echo "<input type='TEXT' name='wdptdo' size=30 maxlength=30 value='".$wdptdo."' readonly=readonly class='tipoGRIDO'>";
							echo "</td><td class='tipo01GRID' colspan=2>Nro de Documento<br><input type='TEXT' name='wdpdoc' maxlength=10 value='".$wdpdoc."' readonly=readonly class='tipoGRIDO'></td></tr>";
							echo "<tr><td class='tipo01GRID'>1er Nombre<br><input type='TEXT' name='wdpno1' maxlength=20 value='".$wdpno1."' class='tipoGRIDT'></td><td class='tipo01GRID'>2do Nombre<br><input type='TEXT' name='wdpno2' maxlength=20 value='".$wdpno2."' class='tipoGRIDT'></td><td class='tipo01GRID'>1er Apellido<br><input type='TEXT' name='wdpap1' maxlength=20 value='".$wdpap1."' class='tipoGRIDT'></td><td class='tipo01GRID'>2do Apellido<br><input type='TEXT' name='wdpap2' maxlength=20 value='".$wdpap2."' class='tipoGRIDT'></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Estado Civil<br>";
							$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='04' and Selest='on'  order by Selpri";
							$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							echo "<select name='wdpest' class='tipoGRIDT'>";
							if ($num>0)
							{
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									$wdpest=ver($wdpest);
									if($wdpest == $row[0])
										echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									else
										echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
								}
							}
							echo "</select>";
							echo "</td><td class='tipo01GRID' colspan=2>Ocupacion<br><input type='TEXT' name='wdpofiw' size=10 maxlength=30 id='w28' OnBlur='enter()' class='tipoGRIDT'>&nbsp;&nbsp;&nbsp;";
							$wdpofi = ver($wdpofi);
							echo "<select name='wdpofi' class='tipoGRIDT'>";
							if(isset($wdpofiw) and $wdpofiw != "")
							{
								$query = "SELECT Codigo, Descripcion from root_000003 where Descripcion like '%".$wdpofiw."%'  order by Descripcion";
								$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 858".mysql_error());
								$num = mysql_num_rows($err);
								if ($num>0)
								{
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										$wdpofi=ver($wdpofi);
										if($wdpofi == $row[0])
											echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
										else
											echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									}
								}
								else
									echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
							}
							else
							{
								$query = "SELECT Codigo, Descripcion from root_000003 where Codigo = '".$wdpofi."'  order by Descripcion";
								$err = mysql_query($query,$conex) or die(mysql_errno()."error ".mysql_error());
								$num = mysql_num_rows($err);
								if ($num>0)
								{
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										$wdpofi=ver($wdpofi);
										if($wdpofi == $row[0])
											echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
										else
											echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									}
								}
								else
									echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
							}
							echo "</select></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Direccion<br><input type='TEXT' name='wdpdir' size=40 maxlength=50 value='".$wdpdir."' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Municipo<br><input type='TEXT' name='wdpciuw' size=10 maxlength=30 id='w28' OnBlur='enter()' class='tipoGRIDT'>&nbsp;&nbsp;&nbsp;";
							$wdpciu = ver($wdpciu);
							echo "<select name='wdpciu' class='tipoGRIDT' OnChange='enter()'>";
							if(isset($wdpciuw) and $wdpciuw != "")
							{
								$query = "SELECT Codigo, Nombre from root_000006 where Nombre like '%".$wdpciuw."%'  order by Nombre";
								$err = mysql_query($query,$conex) or die(mysql_errno()."error ".mysql_error());
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									echo "<option value='SELECCIONE'>SELECCIONE</option>";
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									}
								}
								else
									echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
							}
							else
							{
								$query = "SELECT Codigo, Nombre from root_000006 where Codigo = '".$wdpciu."'  order by Nombre";
								$err = mysql_query($query,$conex) or die(mysql_errno()."error ".mysql_error());
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										$wdpciu=ver($wdpciu);
										if($wdpciu == $row[0])
											echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
										else
											echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									}
								}
								else
									echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
							}
							echo "</select></td>";
							echo "<td class='tipo01GRID'>Barrio<br><input type='TEXT' name='wdpbarw' size=10 maxlength=30 id='w28' OnBlur='enter()' class='tipoGRIDT'>&nbsp;&nbsp;&nbsp;";
							$wdpbar = ver($wdpbar);
							echo "<select name='wdpbar' class='tipoGRIDT'>";
							if(isset($wdpbarw) and $wdpbarw != "")
							{
								$query = "SELECT Barcod, Bardes  from root_000034  where Barmun= '".$wdpciu."' and Bardes like '%".$wdpbarw."%'  order by Bardes ";
								$err = mysql_query($query,$conex) or die(mysql_errno()."error en linea 902".mysql_error());
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									echo "<option value='SELECCIONE'>SELECCIONE</option>";
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									}
								}
								else
									echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
							}
							else
							{
								$query = "SELECT Barcod, Bardes  from root_000034  where Barmun= '".$wdpciu."' and Barcod = '".$wdpbar."' ";
								$err = mysql_query($query,$conex) or die(mysql_errno()."error ".mysql_error());
								$num = mysql_num_rows($err);
								if ($num > 0)
								{
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										$wdpbar=ver($wdpbar);
										if($wdpbar == $row[0])
											echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
										else
											echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									}
								}
								else
									echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
							}
							echo "</select></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Telefonos<br><input type='TEXT' name='wdptel' size=30 maxlength=50 value='".$wdptel."' class='tipoGRIDT'></td><td class='tipo01GRID' colspan=2>Correo Electronico<br><input type='TEXT' name='wdpcor' size=50 maxlength=50 value='".$wdpcor."' class='tipoGRIDT'></td></tr>";
							if(isset($wdpeps))
							{
								$wdpcem = substr($wdpeps,0,strpos($wdpeps,"-"));
								$wdpent = substr($wdpeps,strpos($wdpeps,"-")+1);
							}
							$wdpeps = $wdpcem."-".$wdpent;
							$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='06' and Selest='on' and Selcod = '".$wdptus."'  order by Selpri";
							$err = mysql_query($query,$conex) or die(mysql_errno()."error ".mysql_error());
							$num = mysql_num_rows($err);
							if ($num>0)
							{
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									$wdptus = $row[0]."-".$row[1];
								}
							}
							echo "<tr><td class='tipo01GRID' colspan=2>ESP - Asegurador<br><input type='TEXT' name='wdpeps' size=50 maxlength=50 value='".$wdpeps."' readonly=readonly class='tipoGRIDO'></td><td class='tipo01GRID' colspan=2>Afiliacion<br><input type='TEXT' name='wdptus' size=50 maxlength=50 value='".$wdptus."' class='tipoGRIDO'></td></tr>";
							echo "<tr><td class='tipo05GRID' colspan=4>DATOS DEL RESPONSABLE</td></tr>";
							echo "<tr><td class='tipo01GRID'>Cedula<br><input type='TEXT' name='wdpcru' size=20 maxlength=20 value='".$wdpcru."' class='tipoGRIDT'></td><td class='tipo01GRID'>Nombre<br><input type='TEXT' name='wdpnru' size=40 maxlength=40 value='".$wdpnru."' class='tipoGRIDT'></td><td class='tipo01GRID' colspan=2>Direccion<br><input type='TEXT' name='wdpdru' size=40 maxlength=40 value='".$wdpdru."' class='tipoGRIDT'></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Telefonos<br><input type='TEXT' name='wdptru' size=40 maxlength=40 value='".$wdptru."' class='tipoGRIDT'></td><td class='tipo01GRID' colspan=2>Parentesco<br><input type='TEXT' name='wdppru' size=40 maxlength=40 value='".$wdppru."' class='tipoGRIDT'></td></tr>";
							echo "<tr><td class='tipo05GRID' colspan=4>DATOS DEL ACOMPA&Ntilde;ANTE</td></tr>";
							echo "<tr><td class='tipo01GRID'>Cedula<br><input type='TEXT' name='wdpcea' size=20 maxlength=20 value='".$wdpcea."' class='tipoGRIDT'></td><td class='tipo01GRID'>Nombre<br><input type='TEXT' name='wdpnoa' size=40 maxlength=40 value='".$wdpnoa."' class='tipoGRIDT'></td><td class='tipo01GRID' colspan=2>Direccion<br><input type='TEXT' name='wdpdia' size=40 maxlength=40 value='".$wdpdia."' class='tipoGRIDT'></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=2>Telefonos<br><input type='TEXT' name='wdptea' size=40 maxlength=40 value='".$wdptea."' class='tipoGRIDT'></td><td class='tipo01GRID' colspan=2>Parentesco<br><input type='TEXT' name='wdppaa' size=40 maxlength=40 value='".$wdppaa."' class='tipoGRIDT'></td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=4>Tipo de Consulta<br>";
							$query = "SELECT Selcod, Seldes  from ".$empresa."_000105 where Seltip='02' and Selest='on'  order by Selpri";
							$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							echo "<select name='wdptat' class='tipoGRIDT'>";
							if ($num>0)
							{
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									$wdptat=ver($wdptat);
									if($wdptat == $row[0])
										echo "<option selected value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
									else
										echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
								}
							}
							echo "</select></td></tr>";
							echo "<tr><td class='tipo03GRID' colspan=4>DATOS OK!! <input type='checkbox' name='okdp' onclick='enter()'></td></tr>";
							echo "</table>";
							if(isset($werr) and isset($e) and $e > -1)
							{
								echo "<br><center><table border=0 aling=center>";
								for ($i=0;$i<=$e;$i++)
									if(substr($werr[$i],0,3) == "OK!")
										echo "<tr><td class='color2'><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp</td><td class='color2'>".$werr[$i]."</td></tr>";
									else
										echo "<tr><td class='color1'><IMG SRC='/matrix/images/medical/root/Malo.ico'>&nbsp&nbsp</td><td class='color1'>".$werr[$i]."</td></tr>";
								echo "</table></center>";
							}
						break;
						case 2:
							//*************** ANTECEDENTES ********************
							$werr = array();
							$e = -1;
							if(isset($okdp))
							{
								if($e < 0)
								{
									$query = "lock table ".$empresa."_000129 LOW_PRIORITY WRITE, ".$empresa."_000247 LOW_PRIORITY WRITE ";
									$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
									//                 0        1      2       3       4       5       6       7       8       9       10      11      12      13      14      15      16      17      18      19      20      21      22
									$query = "select Anthis, Anttam, Antenf, Antale, Antcir, Antein, Antmed, Antemb, Antob1, Antanf, Antpre, Anttra, Antope, Antcio, Antpro, Antort, Antend, Antbla, Antper, Antotr, Antob2, Antaya, Antabd from ".$empresa."_000129 ";
									$query .= " where Anthis = '".$whis."'";
									$err = mysql_query($query,$conex) or die("ERROR CONSULTADO ANTECEDENTES DEL PACIENTE 2 : ".mysql_errno().":".mysql_error());
									$row1 = mysql_fetch_array($err);
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$query =  " update ".$empresa."_000129 set ";
									if(isset($waptam))
									{
										if($row1[1] == "off")
											insert_log_ant($empresa,$conex,$whis,"Anttam","TRATAMIENTO MEDICO","no","si",$key,$fecha,$hora);
										$query .=  "  Anttam='on',";
									}
									else
									{
										if($row1[1] == "on")
											insert_log_ant($empresa,$conex,$whis,"Anttam","TRATAMIENTO MEDICO","si","no",$key,$fecha,$hora);
										$query .=  "  Anttam='off',";
									}
									if(isset($wapenf))
									{
										if($row1[2] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antenf","ENFERMEDADES","no","si",$key,$fecha,$hora);
										$query .=  "  Antenf='on',";
									}
									else
									{
										if($row1[2] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antenf","ENFERMEDADES","si","no",$key,$fecha,$hora);
										$query .=  "  Antenf='off',";
									}
									if(isset($wapale))
									{
										if($row1[3] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antale","ALERGIAS","no","si",$key,$fecha,$hora);
										$query .=  "  Antale='on',";
									}
									else
									{
										if($row1[3] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antale","ALERGIAS","si","no",$key,$fecha,$hora);
										$query .=  "  Antale='off',";
									}
									if(isset($wapcir))
									{
										if($row1[4] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antcir","CIRUGIAS","no","si",$key,$fecha,$hora);
										$query .=  "  Antcir='on',";
									}
									else
									{
										if($row1[4] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antcir","CIRUGIAS","si","no",$key,$fecha,$hora);
										$query .=  "  Antcir='off',";
									}
									if(isset($wapein))
									{
										if($row1[5] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antein","ENFERMEDADES INFECTOCONTAGIOSAS","no","si",$key,$fecha,$hora);
										$query .=  "  Antein='on',";
									}
									else
									{
										if($row1[5] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antein","ENFERMEDADES INFECTOCONTAGIOSAS","si","no",$key,$fecha,$hora);
										$query .=  "  Antein='off',";
									}
									if(isset($wapmed))
									{
										if($row1[6] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antmed","MEDICAMENTOS","no","si",$key,$fecha,$hora);
										$query .=  "  Antmed='on',";
									}
									else
									{
										if($row1[6] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antmed","MEDICAMENTOS","si","no",$key,$fecha,$hora);
										$query .=  "  Antmed='off',";
									}
									if(isset($wapemb))
									{
										if($row1[7] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antemb","EMBARAZO","no","si",$key,$fecha,$hora);
										$query .=  "  Antemb='on',";
									}
									else
									{
										if($row1[7] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antemb","EMBARAZO","si","no",$key,$fecha,$hora);
										$query .=  "  Antemb='off',";
									}
									if($row1[8] != $wapob1)
											insert_log_ant($empresa,$conex,$whis,"Antob1","OBSERVACIONES",$row1[8],$wapob1,$key,$fecha,$hora);
									$query .=  "  Antob1='".$wapob1."',";
									if($row1[9] != $wapanf)
											insert_log_ant($empresa,$conex,$whis,"Antanf","ANTECEDENTES FAMILIARES",$row1[9],$wapanf,$key,$fecha,$hora);
									$query .=  "  Antanf='".$wapanf."',";
									if(isset($wappre))
									{
										if($row1[10] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antpre","PREVENCION","no","si",$key,$fecha,$hora);
										$query .=  "  Antpre='on',";
									}
									else
									{
										if($row1[10] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antpre","PREVENCION","si","no",$key,$fecha,$hora);
										$query .=  "  Antpre='off',";
									}
									if(isset($waptra))
									{
										if($row1[11] == "off")
											insert_log_ant($empresa,$conex,$whis,"Anttra","TRAUMA","no","si",$key,$fecha,$hora);
										$query .=  "  Anttra='on',";
									}
									else
									{
										if($row1[11] == "on")
											insert_log_ant($empresa,$conex,$whis,"Anttra","TRAUMA","si","no",$key,$fecha,$hora);
										$query .=  "  Anttra='off',";
									}
									if(isset($wapope))
									{
										if($row1[12] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antope","OPERATORIA","no","si",$key,$fecha,$hora);
										$query .=  "  Antope='on',";
									}
									else
									{
										if($row1[12] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antope","OPERATORIA","si","no",$key,$fecha,$hora);
										$query .=  "  Antope='off',";
									}
									if(isset($wapcio))
									{
										if($row1[13] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antcio","CIRUGIA","no","si",$key,$fecha,$hora);
										$query .=  "  Antcio='on',";
									}
									else
									{
										if($row1[13] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antcio","CIRUGIA","si","no",$key,$fecha,$hora);
										$query .=  "  Antcio='off',";
									}
									if(isset($wappro))
									{
										if($row1[14] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antpro","PROTESIS","no","si",$key,$fecha,$hora);
										$query .=  "  Antpro='on',";
									}
									else
									{
										if($row1[14] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antpro","PROTESIS","si","no",$key,$fecha,$hora);
										$query .=  "  Antpro='off',";
									}
									if(isset($waport))
									{
										if($row1[15] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antort","ORTODONCIA","no","si",$key,$fecha,$hora);
										$query .=  "  Antort='on',";
									}
									else
									{
										if($row1[15] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antort","ORTODONCIA","si","no",$key,$fecha,$hora);
										$query .=  "  Antort='off',";
									}
									if(isset($wapend))
									{
										if($row1[16] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antend","ENDODONCIA","no","si",$key,$fecha,$hora);
										$query .=  "  Antend='on',";
									}
									else
									{
										if($row1[16] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antend","ENDODONCIA","si","no",$key,$fecha,$hora);
										$query .=  "  Antend='off',";
									}
									if(isset($wapbla))
									{
										if($row1[17] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antbla","BLANQUEAMIENTO","no","si",$key,$fecha,$hora);
										$query .=  "  Antbla='on',";
									}
									else
									{
										if($row1[17] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antbla","BLANQUEAMIENTO","si","no",$key,$fecha,$hora);
										$query .=  "  Antbla='off',";
									}
									if(isset($wapper))
									{
										if($row1[18] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antper","PERIODONCIA","no","si",$key,$fecha,$hora);
										$query .=  "  Antper='on',";
									}
									else
									{
										if($row1[18] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antper","PERIODONCIA","si","no",$key,$fecha,$hora);
										$query .=  "  Antper='off',";
									}
									if(isset($wapotr))
									{
										if($row1[19] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antotr","HABITOS","no","si",$key,$fecha,$hora);
										$query .=  "  Antotr='on',";
									}
									else
									{
										if($row1[19] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antotr","HABITOS","si","no",$key,$fecha,$hora);
										$query .=  "  Antotr='off',";
									}
									if($row1[20] != $wapob2)
											insert_log_ant($empresa,$conex,$whis,"Antob1","OBSERVACIONES ODONTOLOGICAS",$row1[20],$wapob2,$key,$fecha,$hora);
									$query .=  "  Antob2='".$wapob2."', ";
									if($row1[21] != $wapaya)
											insert_log_ant($empresa,$conex,$whis,"Antob1","ALERGIAS Y ALERTAS",$row1[21],$wapaya,$key,$fecha,$hora);
									$query .=  "  Antaya='".$wapaya."', ";
									if(isset($wabeasd))
									{
										if($row1[22] == "off")
											insert_log_ant($empresa,$conex,$whis,"Antabd","HABEAS DATA","no","si",$key,$fecha,$hora);
										$query .=  "  Antabd='on' ";
									}
									else
									{
										if($row1[22] == "on")
											insert_log_ant($empresa,$conex,$whis,"Antabd","HABEAS DATA","si","no",$key,$fecha,$hora);
										$query .=  "  Antabd='off' ";
									}
									$query .=  "  where Anthis='".$whis."'";
									$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ANTECEDENTES : ".mysql_errno().":".mysql_error());
									$e=$e+1;
									$werr[$e]="OK! DATOS ACTUALIZADOS";
									$query = " UNLOCK TABLES";
									$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
								}
							}
							$query = "select count(*) from ".$empresa."_000129 ";
							$query .= " where Anthis = '".$whis."'";
							$err = mysql_query($query,$conex) or die("ERROR CONSULTADO ANTECEDENTES DEL PACIENTE 1 : ".mysql_errno().":".mysql_error());
							$row1 = mysql_fetch_array($err);
							if($row1[0] == 0)
							{
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query  = "insert ".$empresa."_000129 (medico,fecha_data,hora_data, Anthis, Anttam, Antenf, Antale, Antcir, Antein, Antmed, Antemb, Antob1, Antanf, Antpre, Anttra, Antope, Antcio, Antpro, Antort, Antend, Antbla, Antper, Antotr, Antob2, Antaya, Antabd, Seguridad) ";
								$query .= " VALUES ('soe','".$fecha."' ,'".$hora."','".$whis."','off','off','off','off','off','off','off','','','off','off','off','off','off','off','off','off','off','off','','','off','C-soe')";
								$err = mysql_query($query,$conex) or die("ERROR GRABANDO ANTECEDENTES DEL PACIENTE 2 : ".mysql_errno().":".mysql_error());
							}

							$query = "select Anthis, Anttam, Antenf, Antale, Antcir, Antein, Antmed, Antemb, Antob1, Antanf, Antpre, Anttra, Antope, Antcio, Antpro, Antort, Antend, Antbla, Antper, Antotr, Antob2, Antaya, Antabd from ".$empresa."_000129 ";
							$query .= " where Anthis = '".$whis."'";
							$err = mysql_query($query,$conex) or die("ERROR CONSULTADO ANTECEDENTES DEL PACIENTE 2 : ".mysql_errno().":".mysql_error());
							$num1 = mysql_num_rows($err);
							if($num1 > 0)
							{
								$row1 = mysql_fetch_array($err);
								$waphis = $row1[0];
								if($row1[1] == "on")
									$waptam = $row1[1];
								if($row1[2] == "on")
									$wapenf = $row1[2];
								if($row1[3] == "on")
									$wapale = $row1[3];
								if($row1[4] == "on")
									$wapcir = $row1[4];
								if($row1[5] == "on")
									$wapein = $row1[5];
								if($row1[6] == "on")
									$wapmed = $row1[6];
								if($row1[7] == "on")
									$wapemb = $row1[7];
								$wapob1 = $row1[8];
								$wapanf = $row1[9];
								if($row1[10] == "on")
									$wappre = $row1[10];
								if($row1[11] == "on")
									$waptra = $row1[11];
								if($row1[12] == "on")
									$wapope = $row1[12];
								if($row1[13] == "on")
									$wapcio = $row1[13];
								if($row1[14] == "on")
									$wappro = $row1[14];
								if($row1[15] == "on")
									$waport = $row1[15];
								if($row1[16] == "on")
									$wapend = $row1[16];
								if($row1[17] == "on")
									$wapbla = $row1[17];
								if($row1[18] == "on")
									$wapper = $row1[18];
								if($row1[19] == "on")
									$wapotr = $row1[19];
								$wapob2 = $row1[20];
								$wapaya = $row1[21];
								if($row1[22] == "on")
									$wabeasd = $row1[22];
							}

							echo "<h3 OnClick='toggleDisplay(divA)' class=tipo3G>REGISTRO CRONOLOGICO DE CAMBIOS EN ANTECEDENTES  (click)</h3>";
							echo "<table border=0  align=center class=='tipoGRID' CELLSPACING=1px id='divA' style='display:none'>";
							echo "<tr><td class='tipo03GRID' colspan=6>REGISTRO CRONOLOGICO DE CAMBIOS EN ANTECEDENTES</td></tr>";
							echo "<tr><td class='tipo04GRID'>Fecha</td><td class='tipo04GRID'>Hora</td><td class='tipo04GRID'>Variable</td><td class='tipo04GRID'>Estado Anterior</td><td class='tipo04GRID'>Estado Actual</td><td class='tipo04GRID'>Odontologo</td></tr>";
							$query = "select Fecha_data, Hora_data, Lanhis, Lancod, Landes, Lanvaa, Lanvac, Lanusu, Descripcion from  ".$empresa."_000247, usuarios ";
							$query .= " where Lanhis = '".$whis." '";
							$query .= "   and Lanusu = Codigo ";
							$query .= " Order by Fecha_data desc, Hora_data desc";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							$k="";
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err);
									$clase="tipo01GRID";
									echo "<tr><td class=".$clase.">".$row1[0]."</td><td class=".$clase.">".$row1[1]."</td><td class=".$clase."L>".$row1[4]."</td><td class=".$clase."L>".$row1[5]."</td><td class=".$clase."L>".$row1[6]."</td><td class=".$clase."L>".$row1[8]."</td></tr>";

								}
							}
							echo"</table><br><br>";


							echo "<table border=0 align=center class=='tipoGRID' CELLSPACING=1px>";
							echo "<tr><td class='tipo03GRID' colspan=4>ANTECEDENTES</td></tr>";
							echo "<tr><td class='tipo01GRID' colspan=4>Nro de Historia<br><input type='TEXT' name='waphis' maxlength=10 value='".$waphis."' readonly=readonly class='tipoGRIDC'></td></tr>";
							if(!isset($wapaya))
								$wapaya = " ";
							echo "<tr><td class='tipo02GRID' colspan=4>Alertas y Alergias<br><textarea name='wapaya' cols=110 rows=5 class='tipoGRIDTR'>".$wapaya."</textarea></td></tr>";
							echo "<tr><td class='tipo01GRID'>Tratamiento Medico</td>";
							if(isset($waptam))
								echo "<td class='tipo01GRID'><input type='checkbox' name='waptam' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='waptam' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Enfermedades</td>";
							if(isset($wapenf))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapenf' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapenf' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID'>Alergias</td>";
							if(isset($wapale))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapale' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapale' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Cirugias</td>";
							if(isset($wapcir))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapcir' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapcir' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID'>Medicamentos</td>";
							if(isset($wapmed))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapmed' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapmed' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Embarazo</td>";
							if(isset($wapemb))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapemb' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapemb' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID' colspan=3>Enfermedades Infectocontagiosas</td>";
							if(isset($wapein))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapein' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapein' class='tipoGRIDT'></td>";
							echo "</tr>";
							if(!isset($wapob1))
								$wapob1 = " ";
							echo "<tr><td class='tipo02GRID' colspan=4>Observaciones<br><textarea name='wapob1' cols=110 rows=5 class='tipoGRIDT'>".$wapob1."</textarea></td></tr>";
							if(!isset($wapanf))
								$wapanf = " ";
							echo "<tr><td class='tipo05GRID' colspan=4>Antecedentes Familiares<br><textarea name='wapanf' cols=110 rows=5 class='tipoGRIDT'>".$wapanf."</textarea></td></tr>";
							echo "<tr><td class='tipo05GRID' colspan=4>Antecedentes Odontologicos</td></tr>";
							echo "<tr><td class='tipo01GRID'>Prevencion</td>";
							if(isset($wappre))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wappre' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wappre' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Trauma</td>";
							if(isset($waptra))
								echo "<td class='tipo01GRID'><input type='checkbox' name='waptra' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='waptra' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID'>Operatoria</td>";
							if(isset($wapope))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapope' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapope' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Cirugia</td>";
							if(isset($wapcio))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapcio' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapcio' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID'>Protesis</td>";
							if(isset($wappro))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wappro' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wappro' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Ortodoncia</td>";
							if(isset($waport))
								echo "<td class='tipo01GRID'><input type='checkbox' name='waport' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='waport' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID'>Endodoncia</td>";
							if(isset($wapend))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapend' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapend' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Blanqueamiento</td>";
							if(isset($wapbla))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapbla' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapbla' class='tipoGRIDT'></td>";
							echo "</tr>";
							echo "<tr><td class='tipo01GRID'>Periodoncia</td>";
							if(isset($wapper))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapper' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapper' class='tipoGRIDT'></td>";
							echo "<td class='tipo01GRID'>Habitos</td>";
							if(isset($wapotr))
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapotr' checked class='tipoGRIDT'></td>";
							else
								echo "<td class='tipo01GRID'><input type='checkbox' name='wapotr' class='tipoGRIDT'></td>";
							echo "</tr>";
							if(!isset($wapob2))
								$wapob2 = " ";
							echo "<tr><td class='tipo02GRID' colspan=4>Observaciones<br><textarea name='wapob2' cols=110 rows=5 class='tipoGRIDT'>".$wapob2."</textarea></td></tr>";
							if(isset($wabeasd))
								echo "<tr><td class='tipo02GRIDR' colspan=4>AUTORIZO LA UTILIZACION DE MIS DATOS PERSONALES PARA FINES DIFERENTES A LOS DE LA PRESTACION DEL SERVICIO DE ODONTOLOGIA???<br><input type='checkbox' name='wabeasd' checked class='tipoGRIDT'></td></tr>";
							else
								echo "<tr><td class='tipo02GRIDR' colspan=4>AUTORIZO LA UTILIZACION DE MIS DATOS PERSONALES PARA FINES DIFERENTES A LOS DE LA PRESTACION DEL SERVICIO DE ODONTOLOGIA???<br><input type='checkbox' name='wabeasd' class='tipoGRIDT'></td></tr>";
							echo "<tr><td class='tipo03GRID' colspan=4>DATOS OK!! <input type='checkbox' name='okdp' onclick='enter()'></td></tr>";
							echo "</table>";
							if(isset($werr) and isset($e) and $e > -1)
							{
								echo "<br><center><table border=0 aling=center>";
								for ($i=0;$i<=$e;$i++)
									if(substr($werr[$i],0,3) == "OK!")
										echo "<tr><td class='color2'><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp</td><td class='color2'>".$werr[$i]."</td></tr>";
									else
										echo "<tr><td class='color1'><IMG SRC='/matrix/images/medical/root/Malo.ico'>&nbsp&nbsp</td><td class='color1'>".$werr[$i]."</td></tr>";
								echo "</table></center>";
							}
						break;
						case 3:
							//****************** MOTIVO DE CONSULTA ***********************
							if(!isset($se1))
							{
								$se1=0;
								$wdata1=array();
							}
							else
							{
								$werr=array();
								$t=-1;
								$query = "lock table ".$empresa."_000132 LOW_PRIORITY WRITE  ";
								$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
								if(!isset($wegrT))
								{
									for ($i=1;$i<=$se1;$i++)
									{
										if(isset($wdel[$i]))
										{
											$query = "delete from ".$empresa."_000132 ";
											$query .= " where Mdxhis = '".$whis."' ";
											$query .= "   and Mdxing = '".$wing."' ";
											$query .= "   and Mdxcon = '".$wdata1[$i][0]."' ";
											$query .= "   and Mdxpro = '".$wdata1[$i][1]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO MOTIVO DE CONSULTA : ".mysql_errno().":".mysql_error());
											$t=$t+1;
											$werr[$t]="OK! MOTIVO DE CONSULTA BORRADO !!!";
										}
										else
										{
											$query = "update ".$empresa."_000132 set Mdxobs='".$wdata1[$i][3]."' ";
											$query .= " where Mdxhis = '".$whis."' ";
											$query .= "   and Mdxing = '".$wing."' ";
											$query .= "   and Mdxcon = '".$wdata1[$i][0]."' ";
											$query .= "   and Mdxpro = '".$wdata1[$i][1]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO MOTIVO DE CONSULTA  : ".mysql_errno().":".mysql_error());
										}
									}
								}
								$query = " UNLOCK TABLES";
								$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());

								if(isset($wpro) and $wpro != "SELECCIONAR" and isset($wobs) and $wobs != "")
								{
									$query = "select Mdxcon, Mdxpro, Mdxdes, Mdxobs from ".$empresa."_000132 ";
									$query .= " where Mdxhis = '".$whis."' ";
									$query .= "   and Mdxing = '".$wing."' ";
									if(substr($wpro,0,1) == "M")
									{
										$wclass="01";
										$query .= "   and Mdxcon = '01' ";
									}

									elseif(substr($wpro,0,1) == "P")
										{
											$wclass="03";
											$query .= "   and Mdxcon = '01' ";
										}
										else
										{
											$wclass="02";
											$query .= "   and Mdxcon = '02' ";
										}
									$query .= "   and  SUBSTRING(Mdxpro,1,2) = '".substr($wpro,0,2)."' ";
									$query .= " ORDER BY Mdxpro desc ";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									if($num2 == 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000132 (medico,fecha_data,hora_data, Mdxhis, Mdxing, Mdxcon, Mdxpro, Mdxdes, Mdxobs, Mdxusr, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$wclass."','".substr($wpro,0,strpos($wpro,"-"))."','".substr($wpro,strpos($wpro,"-")+1)."','".$wobs."','".$key."','C-soe')";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$t=$t+1;
										$werr[$t]="OK! MOTIVO DE CONSULTA GRABADO!!!";
									}
									else
									{
										$row1 = mysql_fetch_array($err1);
										$NEXT = $num2 + 1;
										while(strlen($NEXT) < 2)
											$NEXT = "0".$NEXT;
										$NEXT = "MC".$NEXT;
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000132 (medico,fecha_data,hora_data, Mdxhis, Mdxing, Mdxcon, Mdxpro, Mdxdes, Mdxobs, Mdxusr, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$wclass."','".$NEXT."','".substr($wpro,strpos($wpro,"-")+1)."','".$wobs."','".$key."','C-soe')";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$t=$t+1;
										$werr[$t]="OK! MOTIVO DE CONSULTA GRABADO!!!";
										//$t=$t+1;
										//$werr[$t]="ESTE MOTIVO MOTIVO DE CONSULTA YA SE HA GRABADO!!!! ".$NEXT." ";
									}
								}
							}

							echo "<h3 OnClick='toggleDisplay(divM)' class=tipo3G>REGISTRO CRONOLOGICO DE MOTIVOS DE CONSULTA  (click)</h3>";
							echo "<table border=0  align=center class=='tipoGRID' CELLSPACING=1px id='divM' style='display:none'>";
							echo "<tr><td class='tipo03GRID' colspan=5>REGISTRO CRONOLOGICO DE MOTIVOS DE CONSULTA </td></tr>";
							echo "<tr><td class='tipo04GRID'>Fecha</td><td class='tipo04GRID'>Ingreso</td><td class='tipo04GRID'>Codigo</td><td class='tipo04GRID'>Descripcion</td><td class='tipo04GRID'>Odontologo</td></tr>";
							$query = "select Mdxcon, Mdxpro, Mdxdes, Mdxobs, Fecha_data, descripcion, Hora_data, Mdxing from ".$empresa."_000132, usuarios ";
							$query .= " where Mdxhis = '".$whis."' ";
							$query .= "   and Mdxcon = '01' ";
							$query .= "   and Mdxusr = codigo ";
							$query .= " Order by 5 desc,7 desc ";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							$k="";
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err);
									$clase="tipo01GRID";
									echo "<tr><td class=".$clase.">".$row1[4]."</td><td class=".$clase.">".$row1[7]."</td><td class=".$clase.">".$row1[1]."</td><td class=".$clase."L>".$row1[3]."</td><td class=".$clase."L>".$row1[5]."</td></tr>";

								}
							}
							echo"</table><br><br>";


							//TABLA DE MOTIVO DE CONSULTA
							echo "<table border=0 align=center id=tipo1>";
							echo "<tr><td id=tipo10 colspan=2 align=center>MOTIVO DE CONSULTA</td></tr>";
							echo "<tr><td id=tipo13 align=center>MOTIVO CONSULTA</td><td id=tipo13 align=center>OBSERVACION</td></tr>";
							$wval=0;
							echo "<td id=tipo9 align=center>";
							echo "<select name='wpro'  OnChange='enter()'>";
							echo "<option>SELECCIONAR</option>";
							if(isset($wpro) and substr($wpro,0,strpos($wpro,"-")) == "MC01")
								echo "<option selected>MC01-MOTIVO DE CONSULTA</option>";
							else
								echo "<option>MC01-MOTIVO DE CONSULTA</option>";
							echo "</select>";
							echo "</td>";
							if(isset($wpro) and $wpro != "SELECCIONAR")
								echo "<td id=tipo9 align=center><textarea name='wobs' cols=35 rows=5 class=tipo3 OnChange='enter()'></textarea></td></tr>";
							else
								echo "<td id=tipo9 align=center></td></tr>";
							if(isset($addX))
								echo "<tr><td id=tipo10 colspan=2 align=center>ADICIONAR<input type='checkbox' name='addX' checked></td></tr>";
							else
								echo "<tr><td id=tipo10 colspan=2 align=center>ADICIONAR<input type='checkbox' name='addX'></td></tr>";
							echo "</table><br><br>";
							// FIN TABLA DE MOTIVO DE CONSULTA

							$query = "select Mdxcon, Mdxpro, Mdxdes, Mdxobs, Fecha_data, descripcion from ".$empresa."_000132, usuarios ";
							$query .= " where Mdxhis = '".$whis."' ";
							$query .= "   and Mdxing = '".$wing."' ";
							$query .= "   and Mdxcon = '01' ";
							$query .= "   and Mdxusr = codigo ";
							$err1 = mysql_query($query,$conex);
							$num2 = mysql_num_rows($err1);
							if($num2 > 0)
							{
								$se=0;
								$wdata=array();
								$wsuma=0;
								echo "<table border=0 align=center id=tipo1>";
								echo "<tr><td id=tipo10 colspan=8 align=center>MOTIVO DE CONSULTA</td></tr>";
								echo "<tr><td id=tipo13 align=center>FECHA</td><td id=tipo13 align=center>CONCEPTO</td><td id=tipo13 align=center>MOTIVO DE CONSULTA</td><td id=tipo13 align=center>DESCRIPCION</td><td id=tipo13 align=center>OBSERVACION</td><td id=tipo13 align=center>ODONTOLOGO</td><td id=tipo13 align=center>BORRAR</td></tr>";
								for ($i=0;$i<$num2;$i++)
								{
									$row2 = mysql_fetch_array($err1);
									$se1=$i+1;
									$wdata1[$se1][0]=$row2[0];
									$wdata1[$se1][1]=$row2[1];
									$wdata1[$se1][2]=$row2[2];
									$wdata1[$se1][3]=$row2[3];
									$wdata1[$se1][4]=$row2[4];
									$wdata1[$se1][5]=$row2[5];
									if($wdata1[$se1][0] == "01")
										$color="tipo21";
									else
										if($wdata1[$se1][0] == "03")
											$color="tipo22";
										else
											if($i % 2 != 0)
												$color="tipo17";
											else
												$color="tipo16";
									echo "<tr><td id=".$color."A>".$wdata1[$se1][4]."</td><td id=".$color."A>".$wdata1[$se1][0]."</td><td id=".$color."A>".$wdata1[$se1][1]."</td><td id=".$color."B>".$wdata1[$se1][2]."</td><td id=".$color."A><textarea name='wdata1[".$se1."][3]' cols=60 rows=3 class=tipo3 onblur='enter()'>".$wdata1[$se1][3]."</textarea></td><td id=".$color."B>".$wdata1[$se1][5]."</td><td id=".$color."A><input type='checkbox' name='wdel[".$se1."]' OnClick='enter()'></td></tr>";
									echo "<input type='HIDDEN' name= 'wdata1[".$se1."][0]' value='".$wdata1[$se1][0]."'>";
									echo "<input type='HIDDEN' name= 'wdata1[".$se1."][1]' value='".$wdata1[$se1][1]."'>";
									echo "<input type='HIDDEN' name= 'wdata1[".$se1."][2]' value='".$wdata1[$se1][2]."'>";
								}
								echo "<tr><td id=tipo10W colspan=8>MODIFICAR<input type='checkbox' name='MddX' onClick='enter()'></td></tr>";
								echo "</table><br><br>";
							}
							$se1=$num2;
							echo "<input type='HIDDEN' name= 'se1' value='".$se1."'>";
							if(isset($werr) and isset($t) and $t > -1)
							{
								echo "<br><br><center><table border=0 aling=center>";
								for ($i=0;$i<=$t;$i++)
									if(substr($werr[$i],0,3) == "OK!")
									{
										echo "<tr><td id=tipo18A><IMG SRC='/matrix/images/medical/root/feliz.ico'></TD><TD id=tipo18A>".$werr[$i]."</td></tr>";
									}
									else
										echo "<tr><td id=tipo18B><IMG SRC='/matrix/images/medical/root/Malo.ico'></TD><TD id=tipo18B>".$werr[$i]."</td></tr>";
								echo "</table><br><br></center>";
							}// CIERRE DE CICLO DE PROCESO DE MOTIVO DE CONSULTA
						break;
						case 4:
							//*************** HISTORIA CLINICA ODONTOLOGICA ********************
							$separador="&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
							echo "<table border=0 align=center>";
							echo "<tr><td id=tipo11 colspan=50 >ACTUALIZACION DE ODONTOGRAMA X PACIENTE</td></tr>";
							echo "<tr><td id=tipo10 colspan=50><b>ODONTOGRAMA</td></tr>";
							echo "<tr><td id=tipo11 colspan=50><b>Fecha : ";
							$query ="select Fecha from  ".$empresa."_000130 where identificacion='".$paciente."' group by 1 order by 1 desc ";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							echo "<select name='wfechat' onChange='enter()' class='tipoGRIDT'>";
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row = mysql_fetch_array($err);
									if($i == 0)
										$wfecha1 = $row[0];
									if(isset($wfechat) and $wfechat == $row[0])
										echo "<option selected>".$row[0]."</option>";
									else
										echo "<option>".$row[0]."</option>";
								}
							}
							else
								echo "<option>".date("Y-m-d")."</option>";
							echo "</select></td></tr>";
							$query = "select Antaya from ".$empresa."_000129 ";
							$query .= " where Anthis = '".$whis."'";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							if($num1 > 0)
							{
								$row = mysql_fetch_array($err);
								$wapaya = $row[0];
							}
							else
								$wapaya = " ";
							echo "<tr><td class='tipo02GRID' colspan=4>Alertas y Alergias<br><textarea name='wapaya' cols=110 rows=5 class='tipoGRIDTR'>".$wapaya."</textarea></td></tr>";
							echo "<tr><td id=tipo9 colspan=50><input type='checkbox' name='clean' onclick='enter()'> Limpiar</td></tr>";
							echo "<tr><td id=tipo9 colspan=50>";
							echo "<input type='checkbox' name='sel1' onclick='enter()'> Adultos 18 - 11 / ";
							echo "<input type='checkbox' name='sel2' onclick='enter()'> Adultos 48 - 41 / ";
							echo "<input type='checkbox' name='sel3' onclick='enter()'> Adultos 21 - 28 / ";
							echo "<input type='checkbox' name='sel4' onclick='enter()'> Adultos 31 - 38  ";
							echo "</td></tr>";
							echo "<tr><td id=tipo9 colspan=50>";
							echo "<input type='checkbox' name='sel5' onclick='enter()'> Menor 55 - 51 / ";
							echo "<input type='checkbox' name='sel6' onclick='enter()'> Menor 85 - 81 / ";
							echo "<input type='checkbox' name='sel7' onclick='enter()'> Menor 61 - 65 / ";
							echo "<input type='checkbox' name='sel8' onclick='enter()'> Menor 71 - 75  ";
							echo "</td></tr>";
							echo "<tr><td id=tipo10 colspan=50>DATOS : <input type='checkbox' name='Data' onclick='enter()'></td></tr>";
							echo "</table>";
							if($num1 == 0)
								$wfecha1 = date("Y-m-d");
							$SURFACE=array();
							for ($i=1;$i<=8;$i++)
							{
								for ($j=0;$j<=4;$j++)
								{
									$SURFACE[$i+10][$j][0]="";
									$SURFACE[$i+10][$j][1]=0;
								}
								for ($j=0;$j<=4;$j++)
								{
									$SURFACE[$i+20][$j][0]="";
									$SURFACE[$i+20][$j][1]=0;
								}
								for ($j=0;$j<=4;$j++)
								{
									$SURFACE[$i+30][$j][0]="";
									$SURFACE[$i+30][$j][1]=0;
								}
								for ($j=0;$j<=4;$j++)
								{
									$SURFACE[$i+40][$j][0]="";
									$SURFACE[$i+40][$j][1]=0;
								}
								if($i < 6)
								{
									for ($j=0;$j<=4;$j++)
									{
										$SURFACE[$i+50][$j][0]="";
										$SURFACE[$i+50][$j][1]=0;
									}
									for ($j=0;$j<=4;$j++)
									{
										$SURFACE[$i+60][$j][0]="";
										$SURFACE[$i+60][$j][1]=0;
									}
									for ($j=0;$j<=4;$j++)
									{
										$SURFACE[$i+70][$j][0]="";
										$SURFACE[$i+70][$j][1]=0;
									}
									for ($j=0;$j<=4;$j++)
									{
										$SURFACE[$i+80][$j][0]="";
										$SURFACE[$i+80][$j][1]=0;
									}
								}
							}
							$D=array();
							for ($i=1;$i<=8;$i++)
							{
								$D[$i+10]=false;
								$D[$i+20]=false;
								$D[$i+30]=false;
								$D[$i+40]=false;
								if($i < 6)
								{
									$D[$i+50]=false;
									$D[$i+60]=false;
									$D[$i+70]=false;
									$D[$i+80]=false;
								}
							}
							$S=array();
							for ($i=1;$i<=8;$i++)
							{
								$S[$i+10]=false;
								$S[$i+20]=false;
								$S[$i+30]=false;
								$S[$i+40]=false;
								if($i < 6)
								{
									$S[$i+50]=false;
									$S[$i+60]=false;
									$S[$i+70]=false;
									$S[$i+80]=false;
								}
							}
							$P=array();
							for ($i=1;$i<=8;$i++)
							{
								$P[$i+10][0]=false;
								$P[$i+20][0]=false;
								$P[$i+30][0]=false;
								$P[$i+40][0]=false;
								if($i < 6)
								{
									$P[$i+50][0]=false;
									$P[$i+60][0]=false;
									$P[$i+70][0]=false;
									$P[$i+80][0]=false;
								}
							}
							if(!isset($wfechat))
								$wfechat = $wfecha1;
							$query = "select Diente, Fecha, Hora, Actividad, Ubicacion, Comentarios, Odontologo, Descripcion from  ".$empresa."_000130, usuarios ";
							$query .= " where identificacion = '".$paciente."' ";
							$query .= "   and Fecha <= '".$wfechat."' ";
							$query .= "   and Odontologo = Codigo ";
							$query .= " Order by Diente, Fecha desc, Hora desc";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							$k="";
							$p1=-1;
							$ant=-1;
							$kn1=-1;
							$thoot=array();
							$thoot1=array();
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err);
									if($row1[4] != "N/A")
									{
										$sup=explode("/",$row1[4]);
										for ($w=0;$w<count($sup);$w++)
										{
											switch ($sup[$w])
											{
												case "Mesi":
													$SURFACE[$row1[0]][0][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][0][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][0][1] -= 1;
												break;
												case "Oclu":
													$SURFACE[$row1[0]][1][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][1][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][1][1] -= 1;
												break;
												case "Dist":
													$SURFACE[$row1[0]][2][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][2][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][2][1] -= 1;
												break;
												case "Vest":
													$SURFACE[$row1[0]][3][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][3][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][3][1] -= 1;
												break;
												case "Ling":
													$SURFACE[$row1[0]][4][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][4][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][4][1] -= 1;
												break;
												case "Cerv":
													$SURFACE[$row1[0]][3][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][3][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][3][1] -= 1;
												break;
												case "Todas":
													$SURFACE[$row1[0]][0][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][0][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][0][1] -= 1;

													$SURFACE[$row1[0]][1][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][1][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][1][1] -= 1;

													$SURFACE[$row1[0]][3][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][3][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][3][1] -= 1;

													$SURFACE[$row1[0]][4][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][4][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][4][1] -= 1;

													$SURFACE[$row1[0]][2][0] .= $row1[1]." ".$row1[3].chr(10);
													if(substr($row1[3],strlen($row1[3])-5,5) == "Pend.")
														$SURFACE[$row1[0]][2][1] += 1;
													elseif(substr($row1[3],strlen($row1[3])-5,5) == "Real.")
															$SURFACE[$row1[0]][2][1] -= 1;
												break;
											}
										}
									}
									if($k != $row1[0])
									{
										if($i != 0)
										{
											$D[$k] = true;
											$P[$k][0] = true;
											$P[$k][1] = $ant + 1;
											$P[$k][2] = $ant + 1 + $kn;
											$ant = $ant + 1 + $kn;
											for ($j=0;$j<=$kn;$j++)
												if(substr($actividad[$kn-$j][3],strlen($actividad[$kn-$j][3])-5,5) == "Pend.")
												{
													//if(!array_s($actividad[$kn-$j][3],$actividad,$kn-$j,$actividad[$kn-$j][4]))
													if(!array_s($actividad[$kn-$j][3],$actividad,$kn,$actividad[$kn-$j][4]))
													{
														$S[$k] = true;
														$p1 = $p1 + 1;
														$thoot[$p1][0] = $actividad[$kn-$j][0];
														$thoot[$p1][1] = $actividad[$kn-$j][1];
														$thoot[$p1][2] = $actividad[$kn-$j][2];
														$thoot[$p1][3] = $actividad[$kn-$j][3];
													}
												}
										}
										$actividad=array();
										$kn=-1;
										$k=$row1[0];
									}
									$kn +=1;
									$actividad[$kn][0]=$row1[0];
									$actividad[$kn][1]=$row1[1];
									$actividad[$kn][2]=$row1[2];
									$actividad[$kn][3]=$row1[3];
									$actividad[$kn][4]=$row1[4];
									$actividad[$kn][5]=$row1[5];
									$actividad[$kn][6]=$row1[6]."-".$row1[7];
									$actividad[$kn][7]=0;
									$kn1 = $kn1 + 1;
									$thoot1[$kn1][0]=$row1[0];
									$thoot1[$kn1][1]=$row1[1];
									$thoot1[$kn1][2]=$row1[2];
									$thoot1[$kn1][3]=$row1[3];
									$thoot1[$kn1][4]=$row1[4];
									$thoot1[$kn1][5]=$row1[5];
									$thoot1[$kn1][6]=$row1[6]."-".$row1[7];
								}
								$D[$k] = true;
								$P[$k][0] = true;
								$P[$k][1] = $ant + 1;
								$P[$k][2] = $ant + 1 + $kn;
								for ($j=0;$j<=$kn;$j++)
									if(substr($actividad[$j][3],strlen($actividad[$j][3])-5,5) ==  "Pend.")
									{
										//if(!array_s($actividad[$j][3],$actividad,$kn-$j,$actividad[$j][4]))
										if(!array_s($actividad[$j][3],$actividad,$kn,$actividad[$j][4]))
										{
											$S[$k] = true;
											$p1 = $p1 + 1;
											$thoot[$p1][0] = $actividad[$kn-$j][0];
											$thoot[$p1][1] = $actividad[$kn-$j][1];
											$thoot[$p1][2] = $actividad[$kn-$j][2];
											$thoot[$p1][3] = $actividad[$kn-$j][3];
										}
									}
							}
							$E=array();
							$row=array();
							$row[0]  ="--------------------------------------------------------------------------------------------------------------------------------------";
							$row[1]  ="....18......17......16......15......14......13......12......11....||....21......22......23......24......25......26......27......28....";
							$row[2]  ="....ve......ve......ve......ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve......ve......ve......ve....";
							$row[3]  ="..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..";
							$row[4]  ="....li......li......li......li......li......li......li......li....||....li......li......li......li......li......li......li......li....";
							$row[5]  ="..................................................................||..................................................................";
							$row[6]  ="............................55......54......53......52......51....||....61......62......63......64......65............................";
							$row[7]  ="............................ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve............................";
							$row[8]  ="..........................di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..........................";
							$row[9]  ="............................li......li......li......li......li....||....li......li......li......li......li............................";
							$row[10] ="--------------------------------------------------------------------------------------------------------------------------------------";
							$row[11] ="............................85......84......83......82......81....||....71......72......73......74......75............................";
							$row[12] ="............................li......li......li......li......li....||....li......li......li......li......li............................";
							$row[13] ="..........................di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..........................";
							$row[14] ="............................ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve............................";
							$row[15] ="..................................................................||..................................................................";
							$row[16] ="....48......47......46......45......44......43......42......41....||....31......32......33......34......35......36......37......38....";
							$row[17] ="....li......li......li......li......li......li......li......li....||....li......li......li......li......li......li......li......li....";
							$row[18] ="..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..di<>me..||..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..me<>di..";
							$row[19] ="....ve......ve......ve......ve......ve......ve......ve......ve....||....ve......ve......ve......ve......ve......ve......ve......ve....";
							$row[20] ="--------------------------------------------------------------------------------------------------------------------------------------";
							$num=21;
							for ($i=0;$i<$num;$i++)
								for ($j=0;$j<67;$j++)
								{
									$E[$i][$j]=substr($row[$i],0,2);
									$row[$i] = substr($row[$i],2);
								}
							$wsw2=0;

							echo "<table border=0 align=center id=tipoG00>";
							for ($i=0;$i<$num;$i++)
							{
								for ($j=0;$j<67;$j++)
								{
									switch ($E[$i][$j])
									{
										case "..":
											echo "<td id=tipoG01>&nbsp;&nbsp;</td>";
										break;
										case "<>":
											$w=$i-1;
											$SI=0;
											if(isset($sel1) and $E[$i-2][$j] >= 11 and $E[$i-2][$j] <= 18)
												$SI=1;
											elseif(isset($sel2) and $E[$i-2][$j] >= 41 and $E[$i-2][$j] <= 48)
													$SI=1;
												elseif(isset($sel3) and $E[$i-2][$j] >= 21 and $E[$i-2][$j] <= 28)
														$SI=1;
													elseif(isset($sel4) and $E[$i-2][$j] >= 31 and $E[$i-2][$j] <= 38)
															$SI=1;
														elseif(isset($sel5) and $E[$i-2][$j] >= 51 and $E[$i-2][$j] <= 55)
																$SI=1;
															elseif(isset($sel6) and $E[$i-2][$j] >= 81 and $E[$i-2][$j] <= 85)
																	$SI=1;
																elseif(isset($sel7) and $E[$i-2][$j] >= 61 and $E[$i-2][$j] <= 65)
																		$SI=1;
																	elseif(isset($sel8) and $E[$i-2][$j] >= 71 and $E[$i-2][$j] <= 75)
																			$SI=1;
											if($D[$E[$i-2][$j]])
											{
												if($S[$E[$i-2][$j]])
													$color="tipoG02";
												else
													$color="tipoG03";
												if(strlen($SURFACE[$E[$F][$C]][1][0]) == 0)
													$color="tipoG04";
												elseif($SURFACE[$E[$F][$C]][1][1] > 0)
														$color="tipoG02";
													else
														$color="tipoG03";
												$F = $i - 2;
												$C = $j;
												if((isset($M[$w][$j]) or $SI == 1) and !isset($clean))
												{
													$wsw2=1;
													echo "<td id=".$color." id='SP[".$E[$F][$C]."]' title='".$SURFACE[$E[$F][$C]][1][0]."' onMouseMove='tooltip(".$E[$F][$C].")'><input type='checkbox' name='M[".$w."][".$j."]' class='tipoG07' checked onclick='enter()'></td>";
												}
												else
													echo "<td id=".$color." id='SP[".$E[$F][$C]."]' title='".$SURFACE[$E[$F][$C]][1][0]."' onMouseMove='tooltip(".$E[$F][$C].")'><input type='checkbox' name='M[".$w."][".$j."]' class='tipoG07' onclick='enter()'></td>";
											}
											else
												if((isset($M[$w][$j]) or $SI == 1)and !isset($clean))
												{
													$wsw2=1;
													echo "<td id=tipoG04><input type='checkbox' name='M[".$w."][".$j."]' class='tipoG07' checked onclick='enter()'></td>";
												}
												else
													echo "<td id=tipoG04><input type='checkbox' name='M[".$w."][".$j."]' class='tipoG07' onclick='enter()'></td>";
										break;
										case "ve":
											if($E[$i-1][$j] == "<>")
											{
												$F = $i - 3;
												$C = $j;
											}
											else
											{
												$F = $i - 1;
												$C = $j;
											}
											if(strlen($SURFACE[$E[$F][$C]][3][0]) == 0)
												$color="tipoG04";
											elseif($SURFACE[$E[$F][$C]][3][1] > 0)
													$color="tipoG02";
												else
													$color="tipoG03";
											echo "<td id=".$color." id='SP[".$E[$F][$C]."]' title='".$SURFACE[$E[$F][$C]][3][0]."' onMouseMove='tooltip(".$E[$F][$C].")'>&nbsp;&nbsp;</td>";
										break;
										case "di":
											if($E[$i][$j+1] == "<>")
											{
												$F = $i - 2;
												$C = $j + 1;
											}
											else
											{
												$F = $i - 2;
												$C = $j - 1;
											}
											if(strlen($SURFACE[$E[$F][$C]][2][0]) == 0)
												$color="tipoG04";
											elseif($SURFACE[$E[$F][$C]][2][1] > 0)
													$color="tipoG02";
												else
													$color="tipoG03";
											echo "<td id=".$color." id='SP[".$E[$F][$C]."]' title='".$SURFACE[$E[$F][$C]][2][0]."' onMouseMove='tooltip(".$E[$F][$C].")'>&nbsp;&nbsp;</td>";
										break;
										case "me":
											if($E[$i][$j-1] == "<>")
											{
												$F = $i - 2;
												$C = $j - 1;
											}
											else
											{
												$F = $i - 2;
												$C = $j + 1;
											}
											if(strlen($SURFACE[$E[$F][$C]][0][0]) == 0)
												$color="tipoG04";
											elseif($SURFACE[$E[$F][$C]][0][1] > 0)
													$color="tipoG02";
												else
													$color="tipoG03";
											echo "<td id=".$color." id='SP[".$E[$F][$C]."]' title='".$SURFACE[$E[$F][$C]][0][0]."' onMouseMove='tooltip(".$E[$F][$C].")'>&nbsp;&nbsp;</td>";
										break;
										case "li":
											if($E[$i-1][$j] == "<>")
											{
												$F = $i - 3;
												$C = $j;
											}
											else
											{
												$F = $i - 1;
												$C = $j;
											}
											if(strlen($SURFACE[$E[$F][$C]][4][0]) == 0)
												$color="tipoG04";
											elseif($SURFACE[$E[$F][$C]][4][1] > 0)
													$color="tipoG02";
												else
													$color="tipoG03";
											echo "<td id=".$color." id='SP[".$E[$F][$C]."]' title='".$SURFACE[$E[$F][$C]][4][0]."' onMouseMove='tooltip(".$E[$F][$C].")'>&nbsp;&nbsp;</td>";
										break;
										case "||":
											echo "<td id=tipoG01><b>|</b></td>";
										break;
										case "--":
											echo "<td id=tipoG01><b>--</b></td>";
										break;
										default:
											if(is_numeric($E[$i][$j]))
												echo "<td id=tipoG01><b>".$E[$i][$j]."</b></td>";
											else
											{
												echo "<td id=tipoG04>&nbsp;&nbsp;</td>";
												$wsw=1;
											}
										break;
									}
								}
								echo "</tr>";
							}
							echo "</table>";

							$e=array();
							$e[1]="Caries ";
							$e[2]="Resina Real. ";
							$e[3]="Resina Pend. ";
							$e[4]="Amalgama Real. ";
							$e[5]="Amalgama Pend. ";
							$e[6]="Corona Real. ";
							$e[7]="Corona Pend. ";
							$e[8]="Sellante ";
							$e[9]="Endodoncia Real. ";
							$e[10]="Endodoncia Pend. ";
							$e[11]="Nucleo Real. ";
							$e[12]="Nucleo Pend. ";
							$e[13]="Provisional Real. ";
							$e[14]="Provisional Pend. ";
							$e[15]="Diente Ausente ";
							$e[16]="Exodoncia Real. ";
							$e[17]="Exodoncia Pend. ";
							$e[18]="Otra : ";

							if($wsw2 == 1)
							{
								echo "<table border=0 align=center id=tipo1>";
								$piezas="";
								for ($h=0;$h<$num;$h++)
									for ($l=0;$l<67;$l++)
										if(isset($M[$h-1][$l]))
										{
											if($piezas == "")
												$piezas .= $E[$h-2][$l];
											else
												$piezas .= "-".$E[$h-2][$l];
										}
										echo "<tr><td id=tipo10 colspan=7 align=center>PIEZA(s) DENTAL(es) : ".$piezas."</td></tr>";
										echo "<tr><td id=tipo13 colspan=2 align=center>ACTIVIDAD</font></td><td id=tipo13 colspan=3 align=center>SUPERFICIES</td><td id=tipo13 colspan=1 align=center>COMENTARIOS</td><td id=tipo13 colspan=1 align=center>PREEXISTENTE</td></tr>";
										echo "<tr><td id=tipo9 colspan=2 align=center>";
										echo "<select name='e'>";
										for ($i=1;$i<19;$i++)
										{
											echo "<option>".$e[$i]."</option>";
										}
										echo "</select>";
										echo "&nbsp;<input type='TEXT' name='A1' size=20 maxlength=60><td bgcolor=#dddddd colspan=3>";
										$j=0;
										echo "<input type='checkbox' name='U[".$j."]'>Mesi ";
										$j=1;
										echo "<input type='checkbox' name='U[".$j."]'>Oclu ";
										$j=2;
										echo "<input type='checkbox' name='U[".$j."]'>Dist ";
										$j=3;
										echo "<input type='checkbox' name='U[".$j."]'>Vest ";
										$j=4;
										echo "<input type='checkbox' name='U[".$j."]'>Ling ";
										$j=5;
										echo "<br><input type='checkbox' name='U[".$j."]'>Cervi ";
										$j=6;
										echo "<input type='checkbox' name='U[".$j."]'>Todas ";
										$j=7;
										echo "<input type='checkbox' name='U[".$j."]'>N/A</td>";
										echo "<td id=tipo9 colspan=1><textarea name='C' cols=20 rows=3>.</textarea></td>";
										echo "<td id=tipo9 colspan=1 align=center><input type='checkbox' name='PRE'></td></tr>";
										echo "<tr><td id=tipo10 colspan=7 align=center>DATOS OK!! <input type='checkbox' name='ok' onclick='enter()'></td></tr>";
										echo "<tr><td id=tipo13 align=center>Fecha</td><td id=tipo13 align=center>Hora</td><td id=tipo13 align=center>Pieza<br>Dental</td><td id=tipo13 align=center>Actividad</td><td id=tipo13 align=center>Superficies</td><td id=tipo13 align=center>Comentarios</td><td id=tipo13 align=center>Odontologo</td></tr>";
										for ($h=0;$h<$num;$h++)
											for ($l=0;$l<67;$l++)
												if(isset($M[$h-1][$l]))
												{
													$num1=0;
													if($P[$E[$h-2][$l]][0])
													{
														$num1=1;
														for ($i=$P[$E[$h-2][$l]][1];$i<=$P[$E[$h-2][$l]][2];$i++)
														{
															echo "<tr><td id=tipo9 align=center>".$thoot1[$i][1]."</td><td id=tipo9 align=center>".$thoot1[$i][2]."</td><td id=tipo9 align=center>".$thoot1[$i][0]."</td>";
															if(strpos($thoot1[$i][3],"Pend.") === false)
																echo "<td id=tipo9>".$thoot1[$i][3]."</td>";
															else
															{
																if(buscar($thoot,$E[$h-2][$l],$thoot1[$i][1],$thoot1[$i][2],$thoot1[$i][3],$p1))
																	$color="tipo14";
																else
																	$color="tipo15";
																echo "<td id=".$color.">".$thoot1[$i][3]."</td>";
															}
															echo "<td id=tipo9>".$thoot1[$i][4]."</td><td id=tipo9><b>".$thoot1[$i][5]."</td><td id=tipo9>".$thoot1[$i][6]."</td></tr>";
														}
													}
													if($num1 > 0)
														echo "<tr><td id=tipo10 colspan=7 align=center>&nbsp</td></tr>";
												}
										echo"</table><br><br>";
							}

							echo "<h3 OnClick='toggleDisplay(div0)' class=tipo3G>INFORME GENERAL DE ACTIVIDADES (click)</h3>";
							echo "<table border=0  align=center class=='tipoGRID' CELLSPACING=1px id='div0' style='display:none'>";
							echo "<tr><td class='tipo03GRID' colspan=5>INFORME GENERAL DE ACTIVIDADES</td></tr>";
							echo "<tr><td class='tipo03GRID'>Pieza Dental</td><td class='tipo03GRID'>Actividad</td><td class='tipo03GRID'>Superficies</td><td class='tipo03GRID'>Comentarios</td><td class='tipo03GRID'>Odontologo</td></tr>";
							$query = "select Fecha, Diente, Actividad, Ubicacion, Comentarios, Odontologo, Descripcion from  ".$empresa."_000130, usuarios ";
							$query .= " where identificacion = '".$paciente." '";
							$query .= "   and Fecha <= '".$wfechat."' ";
							$query .= "   and Odontologo = Codigo ";
							$query .= " Order by Fecha desc, Diente";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							$k="";
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err);
									if($k != $row1[0])
									{
										echo "<tr><td class='tipo02GRID' colspan=5>Fecha : ".$row1[0]."</td></tr>";
										$k = $row1[0];
									}
									$clase="tipo01GRID";
									if($row1[1] == 99)
										$row1[1] = "General";
									echo "<tr><td class=".$clase.">".$row1[1]."</td><td class=".$clase.">".$row1[2]."</td><td class=".$clase.">".$row1[3]."</td><td class=".$clase.">".$row1[4]."</td><td class=".$clase.">".$row1[5]."-".$row1[6]."</td>";

								}
							}
							echo"</table><br><br>";

							echo "<table border=0 align=center id=tipo1>";
							echo "<tr><td id=tipo10 colspan=6 align=center>ACTIVIDADES GENERALES</td></tr>";
							echo "<tr><td id=tipo13 colspan=2 align=center>Actividad</td><td id=tipo13 colspan=4 align=center>Comentarios</td></tr>";
							echo "<tr><td id=tipo9 colspan=2><input type='TEXT' name='actividad' size=30 maxlength=60></td><td id=tipo9 colspan=4>CONSENTIMIENTO INFORMADO<br><input type='checkbox' name='assent' onclick='enter()'><br><textarea name='W' cols=60 rows=5>.</textarea></td></tr>";
							echo "<tr><td id=tipo10 colspan=6 align=center>DATOS OK!! <input type='checkbox' name='ok1' onclick='enter()'></td></tr>";
							echo "<tr><td id=tipo13 align=center>Fecha</td><td id=tipo13 align=center>Hora</td><td id=tipo13 align=center>Actividad</td><td id=tipo13 align=center>Superficies</td><td id=tipo13 align=center>Comentarios</td><td id=tipo13 align=center>Odontologo</td></tr>";
							$query = "select Fecha, Hora, Actividad, Ubicacion, Comentarios, Odontologo, Descripcion from  ".$empresa."_000130, usuarios ";
							$query .= " where identificacion = '".$paciente." '";
							$query .= "   and Fecha <= '".$wfechat."' ";
							$query .= "   and Diente = 99";
							$query .= "   and Odontologo = Codigo ";
							$query .= " Order by Fecha desc, Hora desc";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err);
									echo "<tr><td id=tipo9 align=center>".$row1[0]."</td><td id=tipo9 align=center>".$row1[1]."</td><td id=tipo9>".$row1[2]."</td><td id=tipo9>".$row1[3]."</td><td id=tipo9>".$row1[4]."</td><td id=tipo9>".$row1[5]."-".$row1[6]."</td></tr>";
								}
							}
							echo"</table><br><br>";

							echo "<input type='HIDDEN' name= 'num' value='".$num."'>";

						break;
						case 5:
							//****************** PROCESO DE FACTURACION ***********************
                            $arraySedes = array();
                            $querySedes = " SELECT ccocod, ccodes, id
                                              FROM {$empresa}_000003
                                             WHERE Ccotip = 'A'
                                              ORDER BY id asc";
                            $rsSedes    = mysql_query( $querySedes, $conex );
                            while( $rowSedes = mysql_fetch_assoc($rsSedes) ){
                                $arraySedes[$rowSedes['ccocod']] = $rowSedes['ccodes'];
                            }
                            $queryCcoUsuario = " SELECT usscco
                                                   FROM {$empresa}_000318
                                                  WHERE usscod = '{$key}'";
                            $rsCcoUsuario    = mysql_query( $queryCcoUsuario, $conex );
                            $rowCcoUsuario   = mysql_fetch_assoc($rsCcoUsuario);
                            if( $rowCcoUsuario['usscco'] != "" ){
                                $wccoCargoUsuario = $rowCcoUsuario['usscco'];
                            }


							$query = "select Codigo from ".$empresa."_000090 ";
							$query .= " where UPPER(Descripcion) = 'BASE' ";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							if($num1 > 0)
							{
								$row2 = mysql_fetch_array($err);
								$WBASE = $row2[0];
							}
							else
								$WBASE = "NO";

							$query = "select Grucod, Grudes from ".$empresa."_000004 ";
							$query .= " where Gruest = 'on' ";
							$query .= "   and Gruabo = 'off' ";
							$query .= " order by grudes ";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							if(!isset($se))
							{
								$se=0;
								$wdata=array();
							}
							else
							{
								$werr=array();
								$t=-1;
								for ($i=1;$i<=$se;$i++)
								{
									$query = "lock table ".$empresa."_000100 LOW_PRIORITY WRITE, ".$empresa."_000101 LOW_PRIORITY WRITE, ".$empresa."_000106 LOW_PRIORITY WRITE, ".$empresa."_000131 LOW_PRIORITY WRITE, root_000050 LOW_PRIORITY WRITE, ".$empresa."_000104 LOW_PRIORITY WRITE    ";
									$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
									if($wdata[$i][11] == 0)
									{
										if($wdata[$i][4] == 0)
										{
											if($wdata[$i][10] == 0)
											{
												$query = "delete from ".$empresa."_000131 ";
												$query .= " where Ptohis = '".$whis."' ";
												$query .= "   and Ptoing = '".$wing."' ";
												$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
												$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
												$query .= "   and Ptocfa = 0 ";
												$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO PRESUPUESTO : ".mysql_errno().":".mysql_error());
												$t=$t+1;
												$werr[$t]="OK! PROCEDIMIENTO BORRADO DE PRESUPUESTOS!!!";
											}
											elseif($wdata[$i][10] != 0 and $wdata[$i][17] == 100)
												{
													$query = "delete from ".$empresa."_000131 ";
													$query .= " where Ptohis = '".$whis."' ";
													$query .= "   and Ptoing = '".$wing."' ";
													$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
													$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO PRESUPUESTO : ".mysql_errno().":".mysql_error());
													$t=$t+1;
													$werr[$t]="OK! PROCEDIMIENTO BORRADO DE PRESUPUESTOS!!!";
												}
												else
												{
													$t=$t+1;
													$werr[$t]="ERROR NO DEBE BORRAR UN PROCEDIMIENTO QUE HA SIDO FACTURADO!!!";
												}
										}
										else
										{
											$query = "select ptocan, Ptoval from ".$empresa."_000131  ";
											$query .= " where Ptohis = '".$whis."' ";
											$query .= "   and Ptoing = '".$wing."' ";
											$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
											$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
											$query .= "   and Ptocon = '".$wdata[$i][13]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO PRESUPUESTO : ".mysql_errno().":".mysql_error());
											$row1 = mysql_fetch_array($err1);
											if(($wdata[$i][4] >= $wdata[$i][10] and $wdata[$i][4] != $row1[0]) or($wdata[$i][4] >= $wdata[$i][10] and $wdata[$i][4] == $row1[0] and $wdata[$i][9] == "S"))
											{
												if($wdata[$i][9] != "S")
												{
													$query = "update ".$empresa."_000131 set Ptocan=".$wdata[$i][4];
													$query .= " where Ptohis = '".$whis."' ";
													$query .= "   and Ptoing = '".$wing."' ";
													$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
													$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
													$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PRESUPUESTO : ".mysql_errno().":".mysql_error());
												}
												else
												{
													if($wdata[$i][10] == 0 and $wdata[$i][5] != $row1[1])
													{
														$query = "update ".$empresa."_000131 set Ptocan=".$wdata[$i][4].",Ptoval=".$wdata[$i][5];
														$query .= " where Ptohis = '".$whis."' ";
														$query .= "   and Ptoing = '".$wing."' ";
														$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
														$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
														$query .= "   and Ptocon = '".$wdata[$i][13]."' ";
														$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PRESUPUESTO : ".mysql_errno().":".mysql_error());
													}
													else
													{
														if($wdata[$i][10] != 0 and $wdata[$i][5] != $row1[1])
														{
															$t=$t+1;
															$werr[$t]="ERROR NO SE PUEDE CAMBIAR EL VALOR DESPUES DE FACTURADO!!!";
														}
													}
												}
											}
											else
											{
												if($wdata[$i][4] != $row1[0])
												{
													$t=$t+1;
													$werr[$t]="ERROR NO SE DISMINUIR LO PRESUPUESTADO POR DEBAJO DE LO FACTURADO!!!";
												}
											}
										}
										if(isset($west[$i]))
										{
											$query = "update ".$empresa."_000131 set Ptoest= 'on' ";
											$query .= " where Ptohis = '".$whis."' ";
											$query .= "   and Ptoing = '".$wing."' ";
											$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
											$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
											$query .= "   and Ptocon = '".$wdata[$i][13]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADO DEL PRESUPUESTO : ".mysql_errno().":".mysql_error());
										}
										else
										{
											$query = "update ".$empresa."_000131 set Ptoest= 'off' ";
											$query .= " where Ptohis = '".$whis."' ";
											$query .= "   and Ptoing = '".$wing."' ";
											$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
											$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
											$query .= "   and Ptocon = '".$wdata[$i][13]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO ESTADO DEL PRESUPUESTO : ".mysql_errno().":".mysql_error());
										}
									}
									else
									{
										if(isset($west[$i]))
										{
											if($wdata[$i][4] - $wdata[$i][10] >= $wdata[$i][11])
											{
												$query = "update ".$empresa."_000131 set Ptocan=".$wdata[$i][4].",Ptoval=".$wdata[$i][5].",Ptocfa=Ptocfa + ".$wdata[$i][11];
												$query .= " where Ptohis = '".$whis."' ";
												$query .= "   and Ptoing = '".$wing."' ";
												$query .= "   and Ptopro = '".$wdata[$i][2]."' ";
												$query .= "   and Ptofac = '".$wdata[$i][12]."' ";
												$query .= "   and Ptocon = '".$wdata[$i][13]."' ";
												$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PRESUPUESTO FACTURADO : ".mysql_errno().":".mysql_error());

												if($wdata[$i][12] == "off" or $wtar == $wtal or $wpart == "on")
													$ER="R";
												else
													$ER="E";
												//GRABAR EN LA TABLA DE CARGOS
												$query = "select Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pachis, Ingsei, Ingcem, Ingent, max(Ingnin) from ".$empresa."_000100,".$empresa."_000101 ";
												$query .= " where pacdoc = '".$paciente."'";
												$query .= "   and pachis = inghis ";
												$query .= " group by Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pachis, Ingsei, Ingcem, Ingent ";
												$err1 = mysql_query($query,$conex);
												$num2 = mysql_num_rows($err1);
												if($num2 > 0)
												{
													if($wdata[$i][12] == "on")
													{
														//$wtipotar = $wtal;
														$wtipotar = substr($wtarifa,0,strpos($wtarifa,"-"));
													}
													else
														$wtipotar = $wtar;
													$row1 = mysql_fetch_array($err1);
													$fecha = date("Y-m-d");
													$hora = (string)date("H:i:s");
													$query = " insert into ".$empresa."_000106 (Medico, Fecha_data, Hora_data, Tcarusu, Tcarhis, Tcaring, Tcarfec, Tcarsin, Tcarres, Tcarno1, Tcarno2, Tcarap1, Tcarap2, Tcardoc, Tcarser, Tcarconcod, Tcarconnom, Tcarprocod, Tcarpronom, Tcartercod, Tcarternom, Tcarterpor, Tcarcan, Tcarvun, Tcarvto, Tcarrec, Tcarfac, Tcartfa, Tcarest, Tcarnmo, Tcarcmo, Tcarfex, Tcarfre, Tcarapr, Tcardev, Tcartar, Seguridad) ";
													$query .= "   VALUES ('soe','".$fecha."' ,'".$hora."' ,'".$key."','".$whis."' ,'".$wing."' ,'".$fecha."','".$row1[6]."','".$row1[7]."-".$row1[8]."','".$row1[3]."','".$row1[4]."' ,'".$row1[1]."','".$row1[2]."','".$row1[0]."','".$wdata[$i][18]."','".$wdata[$i][0]."','".$wdata[$i][1]."','".$wdata[$i][2]."','".$wdata[$i][3]."','".$wdata[$i][6]."','".$wdata[$i][7]."','".$wdata[$i][8]."','".$wdata[$i][11]."','".$wdata[$i][5]."','".round($wdata[$i][11]*$wdata[$i][5])."','".$ER."','S','CODIGO','on','0','0',0,0,'off','off','".$wtipotar."','C-soe')";
													$err1 = mysql_query($query,$conex) or die ("ERROR INSERTANDO CARGOS DE FACTURACION: ".mysql_errno()." - ".mysql_error());
													$t=$t+1;
													$werr[$t]="OK! PROCEDIMIENTO CARGADO EN FACTURACION!!!";
												}
												else
												{
													$t=$t+1;
													$werr[$t]="ERRORES EN DATOS DE PACIENTES E INGRESO!!!";
												}
											}
											else
											{
												$t=$t+1;
												$werr[$t]="ERROR NO PUEDE FACTURAR MAS DE LO PRESUPUESTADO!!!";
											}
										}
										else
										{
											$t=$t+1;
											$werr[$t]="ERROR NO PUEDE FACTURAR UN PRESUPUESTO INACTIVO!!!";
										}
									}
									$query = " UNLOCK TABLES";
									$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
								}
								if(isset($wsec) and $wsec != "SELECCIONAR" and isset($wpro) and $wpro != "SELECCIONAR" and isset($wcan) and $wcan > 0)
								{
									$wvalor=0;
									$descuento=0.0;
									if($xtar == "off")
									{
										$WTARD = $wtar;
										$waplica="on";
										$query = "select Codigo  from ".$empresa."_000091 ";
										$query .= " where Codigo= '".substr($wpro,0,strpos($wpro,"-"))."' ";
										$query .= "   and Estado = 'on' ";
										$err1 = mysql_query($query,$conex);
										$num2 = mysql_num_rows($err1);
										if($num2 > 0)
											$waplica="off";
										$query = "select Tarvac, Tarfec, Tarvan  from ".$empresa."_000104 ";
										$query .= " where Tartar = '".$wtar."' ";
										$query .= "   and mid(Tarcod,1,instr(Tarcod,'-') - 1) = '".substr($wpro,0,strpos($wpro,"-"))."' ";
										$query .= "   and Tarcon = '".substr($wsec,0,strpos($wsec,"-"))."' ";
										$query .= "   and Tarest = 'on' ";
										$err1 = mysql_query($query,$conex);
										$num2 = mysql_num_rows($err);
										if($num2 > 0)
										{
											$row1 = mysql_fetch_array($err1);
											if($row1[1] > date("Y-m-d"))
												$wvalor=$row1[2];
											else
												$wvalor=$row1[0];
											$wregalito = $wvalor;
											$xtar="off";
											if($wtar == $WBASE and $waplica == "on")
											{
												$wvalor = $wvalor - round($wvalor * (integer)substr($wpordesc,0,strpos($wpordesc,"-"))/100);
												$descuento=(integer)substr($wpordesc,0,strpos($wpordesc,"-"))/100;
											}

										}
									}
									//if($wvalor == 0 or $xtar == "on")
									if($xtar == "on")
									{
										$WTARD = substr($wtarifa,0,strpos($wtarifa,"-"));
										$waplica="on";
										$query = "select Codigo  from ".$empresa."_000091 ";
										$query .= " where Codigo= '".substr($wpro,0,strpos($wpro,"-"))."' ";
										$query .= "   and Estado = 'on' ";
										$err1 = mysql_query($query,$conex);
										$num2 = mysql_num_rows($err1);
										if($num2 > 0)
											$waplica="off";

										$query = "select Tarvac, Tarfec, Tarvan  from ".$empresa."_000104 ";
										$query .= " where Tartar = '".substr($wtarifa,0,strpos($wtarifa,"-"))."' ";
										$query .= "   and mid(Tarcod,1,instr(Tarcod,'-') - 1) = '".substr($wpro,0,strpos($wpro,"-"))."' ";
										$query .= "   and Tarcon = '".substr($wsec,0,strpos($wsec,"-"))."' ";
										$query .= "   and Tarest = 'on' ";
										$err1 = mysql_query($query,$conex);
										$num2 = mysql_num_rows($err);
										if($num2 > 0)
										{
											$row1 = mysql_fetch_array($err1);
											if($row1[0] > 0)
											{
												if($row1[1] > date("Y-m-d"))
													$wvalor=$row1[2];
												else
													$wvalor=$row1[0];
												$wregalito = $wvalor;
												$xtar = "on";
												if(substr($wtarifa,0,strpos($wtarifa,"-")) == $WBASE and $waplica == "on")
												{
													$wvalor = $wvalor - round($wvalor * (integer)substr($wpordesc,0,strpos($wpordesc,"-"))/100);
													$descuento=(integer)substr($wpordesc,0,strpos($wpordesc,"-"))/100;
												}
											}
											else
												$wvalor=0;
										}
										else
											$wvalor=0;
									}
									$query = "select Ptohis from ".$empresa."_000131 ";
									$query .= " where Ptohis = '".$whis."' ";
									$query .= "   and Ptoing = '".$wing."' ";
									$query .= "   and Ptopro = '".substr($wpro,0,strpos($wpro,"-"))."' ";
									$query .= "   and Ptofac = '".$xtar."' ";
									//echo $query."<br>";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									if($num2 == 0)
									{
										if($wvalor > 0)
										{
											$fecha = date("Y-m-d");
											$hora = (string)date("H:i:s");
											$query = "insert ".$empresa."_000131 (medico,fecha_data,hora_data, Ptohis, Ptoing, Ptocpt, Ptoncp, Ptopro, Ptonpr, Ptocan, Ptoval, Ptofac, Ptocon, Ptousu, Ptotar, Ptopde, Ptoest, Ptocco, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".substr($wsec,0,strpos($wsec,"-"))."','".substr($wsec,strpos($wsec,"-")+1)."','".substr($wpro,0,strpos($wpro,"-"))."','".substr($wpro,strpos($wpro,"-")+1,strpos($wpro,"|")-strpos($wpro,"-")-1)."',".$wcan.",".$wvalor.",'".$xtar."',0,'".$key."','".$WTARD."',".$descuento.",'off','{$wccoCargo}','C-soe')";
											$err2 = mysql_query($query,$conex) or die("ERROR INSERTANDO 1 EN 131 ".mysql_errno().":".mysql_error());
											$t=$t+1;
											$werr[$t]="OK! PROCEDIMIENTO PRESUPUESTADO!!!";
										}
										elseif($wvalor <= 0 and substr($wpordesc,0,strpos($wpordesc,"-")) == "100" and isset($wregalito))
											{
												//$xtar = "off";
												$fecha = date("Y-m-d");
												$hora = (string)date("H:i:s");
												$query = "insert ".$empresa."_000131 (medico,fecha_data,hora_data, Ptohis, Ptoing, Ptocpt, Ptoncp, Ptopro, Ptonpr, Ptocan, Ptocfa, Ptoval, Ptofac, Ptocon, Ptousu, Ptotar, Ptopde, Ptoest, Ptocco, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".substr($wsec,0,strpos($wsec,"-"))."','".substr($wsec,strpos($wsec,"-")+1)."','".substr($wpro,0,strpos($wpro,"-"))."','".substr($wpro,strpos($wpro,"-")+1,strpos($wpro,"|")-strpos($wpro,"-")-1)."',".$wcan.",".$wcan.",".$wregalito.",'".$xtar."',0,'".$key."','".$WTARD."',".$descuento.",'off','{$wccoCargo}','C-soe')";
												$err2 = mysql_query($query,$conex) or die("ERROR INSERTANDO 2 EN 131 ".mysql_errno().":".mysql_error());
												$t=$t+1;
												$werr[$t]="OK! PROCEDIMIENTO PRESUPUESTADO!!!";
											}
											else
											{
												$t=$t+1;
												$werr[$t]="ERROR! PROCEDIMIENTO NO PRESUPUESTADO TARIFA EN CERO O NO EXISTE REVISE!!!";
											}
									}
									else
									{
										$query = "select Ptohis, Grutip, Grumva from ".$empresa."_000131, ".$empresa."_000004 ";
										$query .= " where Ptohis = '".$whis."' ";
										$query .= "   and Ptoing = '".$wing."' ";
										$query .= "   and Ptopro = '".substr($wpro,0,strpos($wpro,"-"))."' ";
										$query .= "   and Ptofac = '".$xtar."' ";
										$query .= "   and Ptocpt = Grucod ";
										$err1 = mysql_query($query,$conex);
										$row1 = mysql_fetch_array($err1);
										if($row1[2] == "S")
										{
											$KONT=$num2 + 1;
											$fecha = date("Y-m-d");
											$hora = (string)date("H:i:s");
											$query = "insert ".$empresa."_000131 (medico,fecha_data,hora_data, Ptohis, Ptoing, Ptocpt, Ptoncp, Ptopro, Ptonpr, Ptocan, Ptoval, Ptofac, Ptocon, Ptousu, Ptotar, Ptopde, Ptoest, Ptocco, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".substr($wsec,0,strpos($wsec,"-"))."','".substr($wsec,strpos($wsec,"-")+1)."','".substr($wpro,0,strpos($wpro,"-"))."','".substr($wpro,strpos($wpro,"-")+1,strpos($wpro,"|")-strpos($wpro,"-")-1)."',".$wcan.",".$wvalor.",'".$xtar."',".$KONT.",'".$key."','".$WTARD."',".$descuento.",'off', '{$wccoCargo}','C-soe')";
											$err2 = mysql_query($query,$conex) or die("ERROR INSERTANDO 3 EN 131 ".mysql_errno().":".mysql_error());
											$t=$t+1;
											$werr[$t]="OK! PROCEDIMIENTO PRESUPUESTADO!!!";
										}
										else
										{
											$t=$t+1;
											$werr[$t]="ESTE PROCEDIMIENTO YA SE PRESUPUESTO, MODIFIQUE LA CANTIDAD PRESUPUESTADA";
										}
									}
								}
							}
							if($num1 > 0)
							{
								echo "<table border=0 align=center id=tipo1>";
								echo "<tr><td id=tipo10 colspan=3 align=center>PROCESO DE CARGOS A PRESUPUESTO </td></tr>";
								echo "<tr><td id=tipo13 align=center>SECCION</td><td id=tipo13 align=center>PROCEDIMIENTO</td><td id=tipo13 align=center>CANTIDAD</td></tr>";
								echo "<td id=tipo9 align=center>";
								echo "<select name='wsec' onchange='enter()' class=tipo3D>";
								echo "<option>SELECCIONAR</option>";
								if($estado == "on")
								{
									for ($i=0;$i<$num1;$i++)
									{
										$row = mysql_fetch_array($err);
										if(isset($wsec) and $wsec != "SELECCIONAR" and $wsec == $row[0]."-".$row[1])
											echo "<option selected>".$row[0]."-".$row[1]."</option>";
										else
											echo "<option>".$row[0]."-".$row[1]."</option>";
									}
								}
								echo "</select></td>";
								$wval=0;
								echo "<td id=tipo9 align=center>";
								if(isset($wsec) and $wsec != "SELECCIONAR" and isset($wtarifa) and $wtarifa != "SELECCIONAR")
								{
									$wvaltot=0;
									$query = "select Tarcod, Tartar, Tarvac, Tarfec, Tarvan from ".$empresa."_000104 ";
									//$query .= " where Tartar = '".$wtar."' ";
									$query .= "  where Tarcon = '".substr($wsec,0,strpos($wsec,"-"))."' ";
									$query .= "   and Tarest = 'on' ";
									$query .= "   and Tartar = '".substr($wtarifa,0,strpos($wtarifa,"-"))."' ";
									$query .= " Group by 1,2 ";
									$query .= " Order by mid(Tarcod,locate('-',Tarcod)+1) ";
                                    $err = mysql_query($query,$conex);
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										echo "<select name='wpro' class=tipo3D>";
										echo "<option>SELECCIONAR</option>";
										for ($i=0;$i<$num;$i++)
										{
											$row = mysql_fetch_array($err);
											if($row[3] > date("Y-m-d"))
												echo "<option>".$row[0]." | Tar : ".$row[1]." | Val : $".number_format((double)$row[4],0,'.',',')."</option>";
											else
												echo "<option>".$row[0]." | Tar : ".$row[1]." | Val : $".number_format((double)$row[2],0,'.',',')."</option>";
										}
									}
									echo "</select></td>";
								}
								else
									echo "</td>";

								if(isset($wsec) and $wsec != "SELECCIONAR")
								{
									echo "<td id=tipo9 align=center><input type='TEXT' name='wcan' size=5 maxlength=5></td></tr>";
									echo "<tr><td id=tipo13 align=center>TARIFA</td><td id=tipo13 align=center>ORDEN</td><td id=tipo13 align=center>TIPO DE TARIFA</td></tr>";
									echo "<td id=tipo9 align=center>";
									$query = "select Tarcod, Tardes from ".$empresa."_000025 ";
									$query .= " where Tarest = 'on' ";
									$query .= " Order by 1 ";
									$err = mysql_query($query,$conex);
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										echo "<select name='wtarifa' class=tipo3D onchange='enter()'>";
										echo "<option>SELECCIONAR</option>";
										for ($i=0;$i<$num;$i++)
										{
											$row = mysql_fetch_array($err);
											if(isset($wtarifa) and substr($wtarifa,0,strpos($wtarifa,"-")) == $row[0])
												echo "<option selected>".$row[0]."-".$row[1]."</option>";
											else
												echo "<option>".$row[0]."-".$row[1]."</option>";
										}
									}
									echo "</select></td>";
									echo "<td id=tipo9 align=center>";
									$query = "select Codigo, Descripcion  from ".$empresa."_000090 ";
									$query .= " where Estado = 'on' ";
									$query .= " Order by 1 ";
									$err = mysql_query($query,$conex);
									$num = mysql_num_rows($err);
									if($num > 0)
									{
										echo "<select name='wpordesc' class=tipo3D>";
										for ($i=0;$i<$num;$i++)
										{
											$row = mysql_fetch_array($err);
											echo "<option>".$row[0]."-".$row[1]."</option>";
										}
									}
									echo "</select></td><td id=tipo9 align=center><input type='RADIO' name=xtar value='off' onClick='enter()'><b>Ingreso</b><br><input type='RADIO' name=xtar value'on' onClick='enter()'><b>Alterna</b></td></tr>";
								}
								else
									echo "<td id=tipo9 align=center></td></tr>";
                                if( isset( $wsec )  and $wsec != "SELECCIONAR" ){
                                    echo "<tr><td id=tipo13 align=center colspan='4'>SEDE DEL CARGO</td></tr>";
                                    /*echo "<tr><td id=tipo9  align=center colspan='4'>";
                                        echo "<p> * $wccoCargo-{$arraySedes[$wccoCargo]} * </p>";
                                    echo "</td></tr>";*/
                                    $selected = "";
                                    $i = 0;
                                    echo "<tr>";
                                        echo "<td id=tipo9  align=center colspan='4'>";
                                        echo " * <SELECT name='wccoCargo'>";
                                            foreach( $arraySedes as $keyCco => $descripcion ){
                                                $i++;
                                                if( $selected == "" ){
                                                    $selected = (  ($keyCco == $wccoCargo) || ( $keyCco == $wccoCargoUsuario and $wccoCargo == "" ) || ( $wccoCargo == "" and $wccoCargoUsuario == "" and $i == 1 ) ) ? "selected" : "";
                                                }
                                                echo "<option value='{$keyCco}' {$selected}>{$descripcion}</option>";
                                                $selected = "";
                                            }
                                        echo "</SELECT> * ";
                                        echo "</td>";
                                    echo "</tr>";

                                }
								/*if(isset($add))
									echo "<tr><td id=tipo10 colspan=3 align=center>CARGOS<input type='checkbox' name='add' checked></td></tr>";
								else
									echo "<tr><td id=tipo10 colspan=3 align=center>CARGOS<input type='checkbox' name='add'></td></tr>";*/
								echo "</table><br><br>";
							}
							$soedoc="";
							$soenom="ODONTOLOGO NO ESTA EN LA TABLA 51";
							$soepor=0;
							$query = "select Meddoc, Mednom, Medpor  from ".$empresa."_000051 ";
							$query .= " where Medusu = '".$key."' ";
							$err3 = mysql_query($query,$conex);
							$num3 = mysql_num_rows($err3);
							if($num3 > 0)
							{
								$row3 = mysql_fetch_array($err3);
								$soedoc = $row3[0];
								$soenom = $row3[1];
								$soepor = $row3[2];
							}
							else
							{
								$soedoc1 = "";
								$soenom1 = "";
								$soepor1 = 0;
							}
							//                  0       1       2       3       4      5       6       7       8       9       10        11         12      13      14      15  16
							$query = "select Ptocpt, Ptoncp, Ptopro, Ptonpr, Ptocan, Ptoval, Grutip, Grumva, Ptocfa, Ptofac, Ptocon, Descripcion, Ptoest, Ptotar, Tardes, Ptopde, ptocco  from ".$empresa."_000131, ".$empresa."_000004, usuarios, ".$empresa."_000025 ";
							$query .= " where Ptohis = '".$whis."' ";
							$query .= "   and Ptoing = '".$wing."' ";
							//$query .= "   and Ptofac = 'off' ";
							//$query .= "   and Ptocon = 0 ";
							$query .= "   and Ptocpt = Grucod ";
							$query .= "   and Ptousu = Codigo ";
							$query .= "   and Ptotar = Tarcod ";
							$query .= "  Order by ".$empresa."_000131.id desc";
							$err1 = mysql_query($query,$conex);
							$num2 = mysql_num_rows($err1);
							if($num2 > 0)
							{
								$se=0;
								$wdata=array();
								$wsumaP=0;
								$wsumaF=0;
								$wsumaD=0;
								echo "<table border=0 align=center id=tipo1>";
								echo "<tr><td id=tipo10 colspan=18 align=center>PROCESO DE PRESUPUESTACION - CARGOS A FACTURACION </td></tr>";
								echo "<tr><td id=tipo13 align=center>CODIGO</td><td id=tipo13 align=center>SECCION</td><td id=tipo13 align=center>PROCEDIMIENTO</td><td id=tipo13 align=center>DESCRIPCION</td><td id=tipo13 align=center>CANTIDAD<BR>PRESUPUESTADA</td><td id=tipo13 align=center>CANTIDAD<BR>CARGADA</td><td id=tipo13 align=center>DIFERENCIA</td><td id=tipo13 align=center>CANTIDAD<BR>A CARGAR</td><td id=tipo13 align=center>VLR. UNITARIO</td><td id=tipo13 align=center>VLR TOTAL<BR>PRESUPUESTADO</td><td id=tipo13 align=center>VLR TOTAL<BR>CARGADO</td><td id=tipo13 align=center>PENDIENTE DE<BR>CARGAR</td><td id=tipo13 align=center>PORCENTAJE</td><td id=tipo13 align=center>TIPO<BR>TARIFA</td><td id=tipo13 align=center>TARIFA</td><td id=tipo13 align=center>ODONTOLOGO</td><td id=tipo13>SEDE</td><td id=tipo13 align=center>ESTADO</td></tr>";
								for ($i=0;$i<$num2;$i++)
								{
									$row2 = mysql_fetch_array($err1);
									$se=$i+1;
									$wdata[$se][0]=$row2[0];
									$wdata[$se][1]=$row2[1];
									$wdata[$se][2]=$row2[2];
									$wdata[$se][3]=$row2[3];
									$wdata[$se][4]=$row2[4];
									$wdata[$se][5]=$row2[5];
									$wdata[$se][10]=$row2[8];
									$wdata[$se][11]=0;
									$wdata[$se][12]=$row2[9];
									$wdata[$se][13]=$row2[10];
									$wdata[$se][14]=$row2[11];
									$wdata[$se][15]=$row2[12];
									$wdata[$se][16]=$row2[13]."-".$row2[14];
                                    $wdata[$se][17]=$row2[15] * 100;
									$wdata[$se][18]=$row2[16];
									if($row2[6] == "C")
									{
										$wdata[$se][6]=$soedoc;
										$wdata[$se][7]=$soenom;
										$wdata[$se][8]=$soepor;
										$wsoeter = $soenom;
									}
									else
									{
										$wdata[$se][6]=$soedoc1;
										$wdata[$se][7]=$soenom1;
										$wdata[$se][8]=$soepor1;
										$wsoeter = $soenom1;
									}
									$wdata[$se][9]=$row2[7];
									if($i % 2 != 0)
										$color="tipo17";
									else
										$color="tipo16";
									$wtotalP=$wdata[$se][4] * $wdata[$se][5];
									if($row2[15] == 1)
									{
										$wtotalF=$wdata[$se][10] * 0;
										$wtdiff=0;
									}
									else
									{
										$wtotalF=$wdata[$se][10] * $wdata[$se][5];
										$wtdiff=$wtotalP - $wtotalF;
									}
									$wdiff=$wdata[$se][4] - $wdata[$se][10];

									$wsumaP += $wtotalP;
									$wsumaF += $wtotalF;
									$wsumaD += $wtdiff;
                                    $sedeCargo = $arraySedes[$row2[16]];
									if($estado == "on")
									{
										if($wdata[$se][9] == "S")
											echo "<tr><td id=".$color."A>".$wdata[$se][0]."</td><td id=".$color."B>".$wdata[$se][1]."</td><td id=".$color."A>".$wdata[$se][2]."</td><td id=".$color."B>".$wdata[$se][3]."</td><td id=".$color."A><input type='TEXT' name='wdata[".$se."][4]' value=".$wdata[$se][4]." size=5 maxlength=5></td><td id=".$color."C>".number_format((double)$wdata[$se][10],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdiff,0,'.',',')."</td><td id=".$color."A><input type='TEXT' name='wdata[".$se."][11]' value=".$wdata[$se][11]." size=5 maxlength=5 onblur='enter()'></td><td id=".$color."C><input type='TEXT' name='wdata[".$se."][5]' value=".$wdata[$se][5]." size=8 maxlength=8 onblur='enter()'></td><td id=".$color."C>".number_format((double)$wtotalP,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalF,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtdiff,0,'.',',')."</td>";
										else
											echo "<tr><td id=".$color."A>".$wdata[$se][0]."</td><td id=".$color."B>".$wdata[$se][1]."</td><td id=".$color."A>".$wdata[$se][2]."</td><td id=".$color."B>".$wdata[$se][3]."</td><td id=".$color."A><input type='TEXT' name='wdata[".$se."][4]' value=".$wdata[$se][4]." size=5 maxlength=5></td><td id=".$color."C>".number_format((double)$wdata[$se][10],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdiff,0,'.',',')."</td><td id=".$color."A><input type='TEXT' name='wdata[".$se."][11]' value=".$wdata[$se][11]." size=5 maxlength=5 onblur='enter()'></td><td id=".$color."C>".number_format((double)$wdata[$se][5],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalP,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalF,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtdiff,0,'.',',')."</td>";
									}
									else
									{
										if($wdata[$se][9] == "S")
											echo "<tr><td id=".$color."A>".$wdata[$se][0]."</td><td id=".$color."B>".$wdata[$se][1]."</td><td id=".$color."A>".$wdata[$se][2]."</td><td id=".$color."B>".$wdata[$se][3]."</td><td id=".$color."A>".number_format((double)$wdata[$se][4],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdata[$se][10],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdiff,0,'.',',')."</td><td id=".$color."A><input type='TEXT' name='wdata[".$se."][11]' value=".$wdata[$se][11]." size=5 maxlength=5 onblur='enter()'></td><td id=".$color."C><input type='TEXT' name='wdata[".$se."][5]' value=".$wdata[$se][5]." size=8 maxlength=8 onblur='enter()'></td><td id=".$color."C>".number_format((double)$wtotalP,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalF,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtdiff,0,'.',',')."</td>";
										else
											echo "<tr><td id=".$color."A>".$wdata[$se][0]."</td><td id=".$color."B>".$wdata[$se][1]."</td><td id=".$color."A>".$wdata[$se][2]."</td><td id=".$color."B>".$wdata[$se][3]."</td><td id=".$color."A>".number_format((double)$wdata[$se][4],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdata[$se][10],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdiff,0,'.',',')."</td><td id=".$color."A><input type='TEXT' name='wdata[".$se."][11]' value=".$wdata[$se][11]." size=5 maxlength=5 onblur='enter()'></td><td id=".$color."C>".number_format((double)$wdata[$se][5],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalP,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalF,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtdiff,0,'.',',')."</td>";

									}
									if($wdata[$se][12] == "on")
										echo "<td id=".$color."A>".number_format((double)$wdata[$se][17],2,'.',',')."%</td><td id=tipo17D>ALTERNA</td><td id=".$color."A>".$wdata[$se][16]."</td><td id=".$color."B>".$wdata[$se][14]."</td>";
									else
										echo "<td id=".$color."A>".number_format((double)$wdata[$se][17],2,'.',',')."%</td><td id=".$color."A>DE INGRESO</td><td id=".$color."A>".$wdata[$se][16]."</td><td id=".$color."B>".$wdata[$se][14]."</td>";
									echo "<td id=".$color."A>{$sedeCargo}</td>";
                                    if($wdata[$se][15] == "on" or isset($west[$se]))
										echo "<td id=".$color."A><input type='checkbox' name='west[".$se."]' checked onClick='enter()'></td></tr>";
									else
										echo "<td id=".$color."A><input type='checkbox' name='west[".$se."]' onClick='enter()'></td></tr>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][0]' value='".$wdata[$se][0]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][1]' value='".$wdata[$se][1]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][2]' value='".$wdata[$se][2]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][3]' value='".$wdata[$se][3]."'>";
									if($estado != "on")
										echo "<input type='HIDDEN' name= 'wdata[".$se."][4]' value='".$wdata[$se][4]."'>";
									if($wdata[$se][9] != "S")
										echo "<input type='HIDDEN' name= 'wdata[".$se."][5]' value='".$wdata[$se][5]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][6]' value='".$wdata[$se][6]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][7]' value='".$wdata[$se][7]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][8]' value='".$wdata[$se][8]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][9]' value='".$wdata[$se][9]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][10]' value='".$wdata[$se][10]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][12]' value='".$wdata[$se][12]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][13]' value='".$wdata[$se][13]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][14]' value='".$wdata[$se][14]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][15]' value='".$wdata[$se][15]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][16]' value='".$wdata[$se][16]."'>";
                                    echo "<input type='HIDDEN' name= 'wdata[".$se."][17]' value='".$wdata[$se][17]."'>";
									echo "<input type='HIDDEN' name= 'wdata[".$se."][18]' value='".$wdata[$se][18]."'>";
								}
								echo "<tr><td id=tipo10 colspan=9>TOTAL PRESUPUESTADO Y CARGADO A FACTURACION</td><td id=tipo10B>".number_format((double)$wsumaP,0,'.',',')."</td><td id=tipo10B>".number_format((double)$wsumaF,0,'.',',')."</td><td id=tipo10B>".number_format((double)$wsumaD,0,'.',',')."</td><td id=tipo10B colspan=6></td></tr>";
								if(isset($wfacT))
									echo "<tr><td id=tipo10 colspan=18>PROCESO DE CARGOS A FACTURACION<input type='checkbox' name='wfacT' checked></td></tr>";
								else
									echo "<tr><td id=tipo10 colspan=18>PROCESO DE CARGOS A FACTURACION<input type='checkbox' name='wfacT'></td></tr>";
								echo "<tr><td colspan=16 onClick='ejecutar(".chr(34)."/MATRIX/soe/reportes/Rppto.php?empresa=".$empresa."&wced=".$paciente.chr(34).",1)'>Impresion de Presupuesto</td></tr>";
								echo "</table><br><br>";
							}
							$se=$num2;
							echo "<input type='HIDDEN' name= 'se' value='".$se."'>";
							if(isset($werr) and isset($t) and $t > -1)
							{
								echo "<br><br><center><table border=0 aling=center>";
								for ($i=0;$i<=$t;$i++)
									if(substr($werr[$i],0,3) == "OK!")
									{
										echo "<tr><td id=tipo18A><IMG SRC='/matrix/images/medical/root/feliz.ico'></TD><TD id=tipo18A>".$werr[$i]."</td></tr>";
									}
									else
										echo "<tr><td id=tipo18B><IMG SRC='/matrix/images/medical/root/Malo.ico'></TD><TD id=tipo18B>".$werr[$i]."</td></tr>";
								echo "</table><br><br></center>";
							}
							//                  0       1                 2
							$query = "select Tcarfec, sum(Tcarvto),sum(Tcarfex + Tcarfre)  from ".$empresa."_000106 ";
							$query .= " where Tcarhis = '".$whis."' ";
							$query .= "   and Tcaring = '".$wing."' ";
							$query .= "   and Tcarconcod in ('9301','9001') ";
							$query .= "   and Tcarest = 'on' ";
							$query .= "  Group by 1 ";
							$query .= "  Order by 1 desc";
							$err1 = mysql_query($query,$conex);
							$num2 = mysql_num_rows($err1);
							if($num2 > 0)
							{
								$se=0;
								$wdata=array();
								$wsumaA=0;
								$wsumaB=0;
								$wsumaC=0;
								echo "<table border=0 align=center id=tipo1>";
								echo "<tr><td id=tipo10 colspan=4 align=center>ABONOS A FACTURACION </td></tr>";
								echo "<tr><td id=tipo13 align=center>FECHA</td><td id=tipo13 align=center>VALOR <br>ABONADO</td><td id=tipo13 align=center>VALOR <br>FACTURADO</td><td id=tipo13 align=center>SALDO</td></tr>";
								for ($i=0;$i<$num2;$i++)
								{
									if($i % 2 != 0)
										$color="tipo17";
									else
										$color="tipo16";
									$row2 = mysql_fetch_array($err1);
									$row2[1] = $row2[1] * (-1);
									$saldo = $row2[1] + $row2[2];
									$row2[2] = $row2[2] * (-1);
									$wsumaA += $row2[1];
									$wsumaB += $row2[2];
									$wsumaC += $saldo;
									echo "<tr><td id=".$color."A>".$row2[0]."</td><td id=".$color."C>".number_format((double)$row2[1],0,'.',',')."</td><td id=".$color."C>".number_format((double)$row2[2],0,'.',',')."</td><td id=".$color."C>".number_format((double)$saldo,0,'.',',')."</td></tr>";
								}
								echo "<tr><td id=tipo10>TOTAL ABONOS</td><td id=tipo10B>".number_format((double)$wsumaA,0,'.',',')."</td><td id=tipo10B>".number_format((double)$wsumaB,0,'.',',')."</td><td id=tipo10B>".number_format((double)$wsumaC,0,'.',',')."</td></tr>";
								echo "</table><br><br>";
							}
							/// CIERRE DE CICLO DE PROCESO DE FACTURACION
						break;
						case 6:
							//****************** DIAGNOSTICOS ***********************
							if(!isset($se1))
							{
								$se1=0;
								$wdata1=array();
							}
							else
							{
								$werr=array();
								$t=-1;
								$query = "lock table ".$empresa."_000132 LOW_PRIORITY WRITE  ";
								$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
								if(!isset($wegrT))
								{
									for ($i=1;$i<=$se1;$i++)
									{
										if(isset($wdel[$i]))
										{
											$query = "delete from ".$empresa."_000132 ";
											$query .= " where Mdxhis = '".$whis."' ";
											$query .= "   and Mdxing = '".$wing."' ";
											$query .= "   and Mdxcon = '".$wdata1[$i][0]."' ";
											$query .= "   and Mdxpro = '".$wdata1[$i][1]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR BORRANDO DIAGNOSTICO : ".mysql_errno().":".mysql_error());
											$t=$t+1;
											$werr[$t]="OK! MOTIVO / DIAGNOSTICO BORRADO !!!";
										}
										else
										{
											$query = "update ".$empresa."_000132 set Mdxobs='".$wdata1[$i][3]."' ";
											$query .= " where Mdxhis = '".$whis."' ";
											$query .= "   and Mdxing = '".$wing."' ";
											$query .= "   and Mdxcon = '".$wdata1[$i][0]."' ";
											$query .= "   and Mdxpro = '".$wdata1[$i][1]."' ";
											$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DIAGNOSTICO : ".mysql_errno().":".mysql_error());
										}
									}
								}
								$query = " UNLOCK TABLES";
								$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());

								if(isset($class1) and (substr($class1,0,1) == "M" or substr($class1,0,1) == "D" or substr($class1,0,1) == "P") and isset($wpro) and $wpro != "SELECCIONAR" and isset($wobs) and $wobs != "")
								{
									$query = "select Mdxcon, Mdxpro, Mdxdes, Mdxobs from ".$empresa."_000132 ";
									$query .= " where Mdxhis = '".$whis."' ";
									$query .= "   and Mdxing = '".$wing."' ";
									$query .= "   and fecha_data = '".date("Y-m-d")."' ";
									if(substr($class1,0,1) == "M")
									{
										$wclass="01";
										$query .= "   and Mdxcon = '01' ";
									}

									elseif(substr($class1,0,1) == "P")
										{
											$wclass="03";
											$query .= "   and Mdxcon = '01' ";
										}
										else
										{
											$wclass="02";
											$query .= "   and Mdxcon = '02' ";
										}
									$query .= "   and Mdxpro = '".substr($wpro,0,strpos($wpro,"-"))."' ";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									if($num2 == 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000132 (medico,fecha_data,hora_data, Mdxhis, Mdxing, Mdxcon, Mdxpro, Mdxdes, Mdxobs, Mdxusr, seguridad) values ('soe','".$fecha."','".$hora."','".$whis."','".$wing."','".$wclass."','".substr($wpro,0,strpos($wpro,"-"))."','".substr($wpro,strpos($wpro,"-")+1)."','".$wobs."','".$key."','C-soe')";
										$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
										$t=$t+1;
										$werr[$t]="OK! DIAGNOSTICO GRABADO!!!";
									}
									else
									{
										$t=$t+1;
										$werr[$t]="ESTE DIAGNOSTICO YA SE HA GRABADO!!!!";
									}
								}
							}

							//TABLA DE DIAGNOSTICOS
							$clases=array();
							$clases[0]="SELECCIONAR";
							$clases[1]="DIAGNOSTICO";
							echo "<table border=0 align=center id=tipo1>";
							echo "<tr><td id=tipo10 colspan=3 align=center>DIAGNOSTICOS</td></tr>";
							echo "<tr><td id=tipo13 align=center>CLASE</td><td id=tipo13 align=center>DIAGNOSTICO</td><td id=tipo13 align=center>OBSERVACION</td></tr>";
							echo "<td id=tipo9 align=center>";
							echo "<select name='class1'>";
							for ($i=0;$i<2;$i++)
							{
								//$row = mysql_fetch_array($err);
								if(isset($class1) and $class1 == $clases[$i])
									echo "<option selected>".$clases[$i]."</option>";
								else
									echo "<option>".$clases[$i]."</option>";
							}
							echo "</select><br>";
							if(isset($wsel))
								echo "<input type='TEXT' name='wsel' size=20 maxlength=40 value='".$wsel."' onblur='enter()'></td>";
							else
								echo "<input type='TEXT' name='wsel' size=20 maxlength=40 onblur='enter()'></td>";
							$wval=0;
							echo "<td id=tipo9 align=center>";
							if(isset($class1) and (substr($class1,0,1) == "M" or substr($class1,0,1) == "D" or substr($class1,0,1) == "P"))
							{
								if(substr($class1,0,1) == "M")
								{
									$query = "select Codigo, Descripcion  from root_000011 ";
									$query .= " where Codigo = 'MC01' ";
								}
								elseif(substr($class1,0,1) == "P")
									{
										$query = "select Codigo, Descripcion  from root_000011 ";
										$query .= " where Codigo = 'PAGO' ";
									}
									else
									{
										$query = "select Ciecod, Ciedes  from ".$empresa."_000243 ";
										$query .= " where Ciedes LIKE  '%".$wsel."%' ";
										$query .= " order by Ciedes ";
									}
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
								if($num > 0)
								{
									echo "<select name='wpro'  OnChange='enter()'>";
									echo "<option>SELECCIONAR</option>";
									for ($i=0;$i<$num;$i++)
									{
										$row = mysql_fetch_array($err);
										if(isset($wpro) and substr($wpro,0,strpos($wpro,"-")) == $row[0])
											echo "<option selected>".$row[0]."-".$row[1]."</option>";
										else
											echo "<option>".$row[0]."-".$row[1]."</option>";
									}
									echo "</select>";
								}
								echo "</td>";
							}
							else
								echo "</td>";
							if(isset($wpro) and $wpro != "SELECCIONAR")
								echo "<td id=tipo9 align=center><textarea name='wobs' cols=35 rows=5 class=tipo3 OnChange='enter()'></textarea></td></tr>";
							else
								echo "<td id=tipo9 align=center></td></tr>";
							if(isset($addX))
								echo "<tr><td id=tipo10 colspan=5 align=center>DIAGNOSTICOS<input type='checkbox' name='addX' checked></td></tr>";
							else
								echo "<tr><td id=tipo10 colspan=5 align=center>DIAGNOSTICOS<input type='checkbox' name='addX'></td></tr>";
							echo "</table><br><br>";
							// FIN TABLA DIAGNOSTICOS

							echo "<h3 OnClick='toggleDisplay(divM)' class=tipo3G>REGISTRO CRONOLOGICO DE DIAGNOSTICOS (click)</h3>";
							echo "<table border=0  align=center class=='tipoGRID' CELLSPACING=1px id='divM' style='display:none'>";
							echo "<tr><td class='tipo03GRID' colspan=5>REGISTRO CRONOLOGICO DE DIAGNOSTICOS </td></tr>";
							echo "<tr><td class='tipo04GRID'>Fecha</td><td class='tipo04GRID'>Ingreso</td><td class='tipo04GRID'>Codigo</td><td class='tipo04GRID'>Descripcion</td><td class='tipo04GRID'>Odontologo</td></tr>";
							$query = "select Mdxcon, Mdxpro, Mdxdes, Mdxobs, Fecha_data, descripcion, Hora_data, Mdxing from ".$empresa."_000132, usuarios ";
							$query .= " where Mdxhis = '".$whis."' ";
							$query .= "   and Mdxcon = '02' ";
							$query .= "   and Mdxusr = codigo ";
							$query .= " Order by 5 desc,7 desc ";
							$err = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err);
							$k="";
							if($num1 > 0)
							{
								for ($i=0;$i<$num1;$i++)
								{
									$row1 = mysql_fetch_array($err);
									$clase="tipo01GRID";
									echo "<tr><td class=".$clase.">".$row1[4]."</td><td class=".$clase.">".$row1[7]."</td><td class=".$clase.">".$row1[1]."</td><td class=".$clase."L>".$row1[2]."</td><td class=".$clase."L>".$row1[5]."</td></tr>";

								}
							}
							echo"</table><br><br>";


							$query = "select Mdxcon, Mdxpro, Mdxdes, Mdxobs, Fecha_data, Descripcion from ".$empresa."_000132, usuarios ";
							$query .= " where Mdxhis = '".$whis."' ";
							$query .= "   and Mdxing = '".$wing."' ";
							$query .= "   and Mdxcon = '02' ";
							$query .= "   and Mdxusr = codigo ";
							$err1 = mysql_query($query,$conex);
							$num2 = mysql_num_rows($err1);
							if($num2 > 0)
							{
								$se=0;
								$wdata=array();
								$wsuma=0;
								echo "<table border=0 align=center id=tipo1>";
								echo "<tr><td id=tipo10 colspan=8 align=center>DIAGNOSTICOS</td></tr>";
								echo "<tr><td id=tipo13 align=center>FECHA</td><td id=tipo13 align=center>CONCEPTO</td><td id=tipo13 align=center>DIAGNOSTICO</td><td id=tipo13 align=center>DESCRIPCION</td><td id=tipo13 align=center>OBSERVACION</td><td id=tipo13 align=center>ODONTOLOGO</td><td id=tipo13 align=center>BORRAR</td></tr>";
								for ($i=0;$i<$num2;$i++)
								{
									$row2 = mysql_fetch_array($err1);
									$se1=$i+1;
									$wdata1[$se1][0]=$row2[0];
									$wdata1[$se1][1]=$row2[1];
									$wdata1[$se1][2]=$row2[2];
									$wdata1[$se1][3]=$row2[3];
									$wdata1[$se1][4]=$row2[4];
									$wdata1[$se1][5]=$row2[5];
									if($wdata1[$se1][0] == "01")
										$color="tipo21";
									else
										if($wdata1[$se1][0] == "03")
											$color="tipo22";
										else
											if($i % 2 != 0)
												$color="tipo17";
											else
												$color="tipo16";
									echo "<tr><td id=".$color."A>".$wdata1[$se1][4]."</td><td id=".$color."A>".$wdata1[$se1][0]."</td><td id=".$color."A>".$wdata1[$se1][1]."</td><td id=".$color."B>".$wdata1[$se1][2]."</td><td id=".$color."A><textarea name='wdata1[".$se1."][3]' cols=60 rows=3 class=tipo3 onblur='enter()'>".$wdata1[$se1][3]."</textarea><td id=".$color."B>".$wdata1[$se1][5]."</td></td><td id=".$color."A><input type='checkbox' name='wdel[".$se1."]' OnClick='enter()'></td></tr>";
									echo "<input type='HIDDEN' name= 'wdata1[".$se1."][0]' value='".$wdata1[$se1][0]."'>";
									echo "<input type='HIDDEN' name= 'wdata1[".$se1."][1]' value='".$wdata1[$se1][1]."'>";
									echo "<input type='HIDDEN' name= 'wdata1[".$se1."][2]' value='".$wdata1[$se1][2]."'>";
								}
								echo "<tr><td id=tipo10 colspan=8>GRABAR EGRESO DEL PACIENTE<input type='checkbox' name='wegrT' OnClick='enter()'></td></tr>";
								echo "</table><br><br>";
							}
							$se1=$num2;
							echo "<input type='HIDDEN' name= 'se1' value='".$se1."'>";
							if(isset($werr) and isset($t) and $t > -1)
							{
								echo "<br><br><center><table border=0 aling=center>";
								for ($i=0;$i<=$t;$i++)
									if(substr($werr[$i],0,3) == "OK!")
									{
										echo "<tr><td id=tipo18A><IMG SRC='/matrix/images/medical/root/feliz.ico'></TD><TD id=tipo18A>".$werr[$i]."</td></tr>";
									}
									else
										echo "<tr><td id=tipo18B><IMG SRC='/matrix/images/medical/root/Malo.ico'></TD><TD id=tipo18B>".$werr[$i]."</td></tr>";
								echo "</table><br><br></center>";
							}// CIERRE DE CICLO DE PROCESO DE MOTIVO DE CONSULTA
						break;
						case 7:
							// INICIO DEL CICLO DEL PROCESO DE GRABACION DE ABONOS A CUENTA
							if(isset($wgabo) and isset($wabo) and strlen($wabo) > 0)
							{
								$werr=array();
								$t=-1;
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query = "insert ".$empresa."_000134 (medico,fecha_data,hora_data, Fecha, Hora, Historia, Ingreso, Descripcion, Odontologo, Activo, seguridad) values ('soe','".$fecha."','".$hora."','".$fecha."','".$hora."','".$whis."','".$wing."','".$wabo."','".$key."','off','C-soe')";
								$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								$t=$t+1;
								$werr[$t]="OK! ABONO A CUENTA - GRABADO!!!";
							}
							echo "<table border=0 align=center id=tipo1>";
							echo "<tr><td id=tipo10 align=center>REGISTRO DE ABONOS A CUENTA Y MENSAJES</td></tr>";
							echo "<td id=tipo9 align=center><textarea name='wabo' cols=50 rows=5 class=tipo3'></textarea></td></tr>";
							echo "<tr><td id=tipo10>GRABAR ABONO<input type='checkbox' name='wgabo' OnClick='enter()'></td></tr>";
							echo "</table><br><br>";
							if(isset($werr) and isset($t) and $t > -1)
							{
								echo "<br><br><center><table border=0 aling=center>";
								for ($i=0;$i<=$t;$i++)
									if(substr($werr[$i],0,3) == "OK!")
									{
										echo "<tr><td id=tipo18A><IMG SRC='/matrix/images/medical/root/feliz.ico'></TD><TD id=tipo18A>".$werr[$i]."</td></tr>";
									}
									else
										echo "<tr><td id=tipo18B><IMG SRC='/matrix/images/medical/root/Malo.ico'></TD><TD id=tipo18B>".$werr[$i]."</td></tr>";
								echo "</table><br><br></center>";
							}// CIERRE DE CICLO DEL PROCESO DE GRABACION DE ABONOS A CUENTA
						break;
						case 8:
							//INICIO ESTADO DE CUENTA DEL PACIENTE
							if(!isset($paciente))
							{
								echo "<center><table border=0>";
								echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
								echo "<tr><td align=center colspan=2>UNIDAD ODONTOLOGIA (SOE)</td></tr>";
								echo "<tr><td align=center colspan=2>SALDO X PACIENTE</td></tr>";
								echo "<tr><td bgcolor=#cccccc align=center>Identificacion del Paciente</td>";
								echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='paciente' size=15 maxlength=15></td></tr>";
								echo "<td bgcolor='#cccccc'  align=center colspan=2><input type='submit' value='IR'>";
								echo "</td></tr></table><br>";
							}
							else
							{
								$soedoc="";
								$soenom="ODONTOLOGO NO ESTA EN LA TABLA 51";
								$soepor=0;
								$query = "select Meddoc, Mednom, Medpor  from ".$empresa."_000051 ";
								$query .= " where Medusu = '".$key."' ";
								$err3 = mysql_query($query,$conex);
								$num3 = mysql_num_rows($err3);
								if($num3 > 0)
								{
									$row3 = mysql_fetch_array($err3);
									if(!isset($DIVO))
									{
										$soedoc = $row3[0];
										$soenom = $row3[1];
										$soepor = $row3[2];
									}
									else
									{
										$soedoc = "";
										$soenom = "";
										$soepor = 0;
									}
								}
								else
								{
									$soedoc1 = "";
									$soenom1 = "";
									$soepor1 = 0;
								}
								$wpreT=0;
								$wcarT=0;
								$wfacT=0;
								$wsaldoT=0;
								//                 0       1       2       3       4       5       6       7       8        9      10      11      12     13       14     15       16      17
								$query = "select Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pachis, Ingtar, Ingsei, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Ingcem, Ingent, Ingnin, Pacact from ".$empresa."_000100,".$empresa."_000101 ";
								$query .= " where pacdoc = '".$paciente."'";
								$query .= "   and pachis = inghis ";
								//$query .= "   and Pacact = 'on' ";
								$query .= " ORDER by CAST(Ingnin AS UNSIGNED) DESC ";
								$err = mysql_query($query,$conex) or die("ERROR CONSULTADO DATOS DEL PACIENTE : ".mysql_errno().":".mysql_error());
								$num1 = mysql_num_rows($err);
								if($num1 > 0)
								{
									$row1 = mysql_fetch_array($err);
									$estado = $row1[17];
									$nom=$row1[0]."-".$row1[1]." ".$row1[2]." ".$row1[3]." ".$row1[4]." ".$row1[5];
									$whis=$row1[6];
									$wtar=$row1[7];
									$wsei=$row1[8];
									$wing=$row1[16];
									$dat=$row1[6]."-".$row1[16];
									$wres=$row1[14]."-".$row1[15];
									if($row1[15] == "PARTICULAR")
										$wpart="on";
									else
										$wpart="off";
									$wfna=$row1[9];
									$weda=Dias($wfna);
									$query = "SELECT Seldes  from ".$empresa."_000105 where Selcod='".$row1[10]."' and Seltip='03' and Selest='on' ";
									$err = mysql_query($query,$conex);
									$row = mysql_fetch_array($err);
									$wsex=$row[0];
									$query = "SELECT Seldes  from ".$empresa."_000105 where Selcod='".$row1[11]."' and Seltip='04' and Selest='on' ";
									$err = mysql_query($query,$conex);
									$row = mysql_fetch_array($err);
									$wesc=$row[0];
									$wdir=$row1[12];
									$wtel=$row1[13];
									$query = "select Emptal from root_000050 ";
									$query .= " where Empbda = '".$empresa."' ";
									$err1 = mysql_query($query,$conex);
									$num1 = mysql_num_rows($err1);
									if($num1 > 0)
									{
										$row1 = mysql_fetch_array($err1);
										$wtal=$row1[0];
										$wsw1=1;
									}
									echo "<input type='HIDDEN' name= 'paciente' value='".$paciente."'>";
									//                  0       1       2       3       4      5       6       7       8       9       10      11
									$query = "select Ptocpt, Ptoncp, Ptopro, Ptonpr, Ptocan, Ptoval, Grutip, Grumva, Ptocfa, Ptofac, Ptocon, Ptopde  from ".$empresa."_000131, ".$empresa."_000004 ";
									$query .= " where Ptohis = '".$whis."' ";
									$query .= "   and Ptoing = '".$wing."' ";
									//$query .= "   and Ptofac = 'off' ";
									//$query .= "   and Ptocon = 0 ";
									$query .= "   and Ptocpt = Grucod ";
									$query .= "  Order by ".$empresa."_000131.id desc";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									if($num2 > 0)
									{
										$se=0;
										$wdata=array();
										$wsumaP=0;
										$wsumaF=0;
										$wsumaD=0;
										echo "<table border=0 align=center id=tipo1>";
										echo "<tr><td id=tipo10 colspan=13 align=center>PROCESO DE PRESUPUESTACION GENERAL - CARGOS A FACTURACION </td></tr>";
										echo "<tr><td id=tipo13 align=center>CODIGO</td><td id=tipo13 align=center>SECCION</td><td id=tipo13 align=center>PROCEDIMIENTO</td><td id=tipo13 align=center>DESCRIPCION</td><td id=tipo13 align=center>CANTIDAD<BR>PRESUPUESTADA</td><td id=tipo13 align=center>CANTIDAD<BR>CARGADA</td><td id=tipo13 align=center>DIFERENCIA</td><td id=tipo13 align=center>CANTIDAD<BR>A CARGAR</td><td id=tipo13 align=center>VLR. UNITARIO</td><td id=tipo13 align=center>VLR TOTAL<BR>PRESUPUESTADO</td><td id=tipo13 align=center>VLR TOTAL<BR>CARGADO</td><td id=tipo13 align=center>PENDIENTE DE<BR>CARGAR</td><td id=tipo13 align=center>TARIFA<BR>ALTERNA</td></tr>";
										for ($i=0;$i<$num2;$i++)
										{
											$row2 = mysql_fetch_array($err1);
											$se=$i+1;
											$wdata[$se][0]=$row2[0];
											$wdata[$se][1]=$row2[1];
											$wdata[$se][2]=$row2[2];
											$wdata[$se][3]=$row2[3];
											$wdata[$se][4]=$row2[4];
											$wdata[$se][5]=$row2[5];
											$wdata[$se][10]=$row2[8];
											$wdata[$se][11]=0;
											$wdata[$se][12]=$row2[9];
											$wdata[$se][13]=$row2[10];
											if($row2[6] == "C")
											{
												$wdata[$se][6]=$soedoc;
												$wdata[$se][7]=$soenom;
												$wdata[$se][8]=$soepor;
												$wsoeter = $soenom;
											}
											else
											{
												$wdata[$se][6]=$soedoc1;
												$wdata[$se][7]=$soenom1;
												$wdata[$se][8]=$soepor1;
												$wsoeter = $soenom1;
											}
											$wdata[$se][9]=$row2[7];
											if($i % 2 != 0)
												$color="tipo17";
											else
												$color="tipo16";
											$wtotalP=$wdata[$se][4] * $wdata[$se][5];
											if ($row2[11] == 1)
											{
												$wtotalF=0;
												$wdiff=0;
												$wtdiff=0;
											}
											else
											{
												$wtotalF=$wdata[$se][10] * $wdata[$se][5];
												$wdiff=$wdata[$se][4] - $wdata[$se][10];
												$wtdiff=$wtotalP - $wtotalF;
											}

											$wsumaP += $wtotalP;
											$wsumaF += $wtotalF;
											$wsumaD += $wtdiff;
											echo "<tr><td id=".$color."A>".$wdata[$se][0]."</td><td id=".$color."B>".$wdata[$se][1]."</td><td id=".$color."A>".$wdata[$se][2]."</td><td id=".$color."B>".$wdata[$se][3]."</td><td id=".$color."A>".number_format((double)$wdata[$se][4],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdata[$se][10],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdiff,0,'.',',')."</td><td id=".$color."A>".number_format((double)$wdata[$se][11],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wdata[$se][5],0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalP,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtotalF,0,'.',',')."</td><td id=".$color."C>".number_format((double)$wtdiff,0,'.',',')."</td>";
											if($wdata[$se][12] == "on")
												echo "<td id=tipo17D>ALTERNA</td></tr>";
											else
												echo "<td id=".$color."A>DE INGRESO</td></tr>";
										}
										echo "<tr><td id=tipo10 colspan=9>TOTAL PRESUPUESTADO Y CARGADO A FACTURACION</td><td id=tipo10B>".number_format((double)$wsumaP,0,'.',',')."</td><td id=tipo10B>".number_format((double)$wsumaF,0,'.',',')."</td><td id=tipo10B>".number_format((double)$wsumaD,0,'.',',')."</td><td id=tipo10B></td></tr>";
										$wpreT += $wsumaD;
										echo "</table><br><br>";
									}

									//CARGOS ASOCIADOS AL ODONTOLOGO POR PACIENTE
									//                  0            1         2        3        4               5
									$query = "select Tcarfec, Tcarprocod, Tcarpronom, Tcarcan, Tcarvto, (Tcarfex + Tcarfre) from ".$empresa."_000106  ";
									$query .= "  where Tcarhis = '".$whis."' ";
									$query .= "    and Tcaring = '".$wing."' ";
									$query .= "    and tcardoc = '".$paciente."' ";
									$query .= "    and Tcarest = 'on' ";
									$query .= "    and Tcarusu = '".$key."'";
									$query .= "   order by  Tcarfec ";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									$totfac=0;
									$totsfac=0;
									echo "<center><table border=0 align=center id=tipo1>";
									echo "<tr><td id=tipo10J colspan=7>CARGOS ASOCIADOS AL ODONTOLOGO: ".$soenom."</td></tr>";
									echo "<tr><td id=tipo10J>FECHA</td><td id=tipo10J>CODIGO</td><td id=tipo10J>PROCEDIMIENTO</td><td id=tipo10J>CANTIDAD</td><td id=tipo10J>VALOR<BR>TOTAL</td><td id=tipo10J>VALOR<BR>FACTURADO</td><td id=tipo10J>ESTADO</td></tr>";
									if($num2 > 0)
									{
										for ($i=0;$i<$num2;$i++)
										{
											$row2 = mysql_fetch_array($err1);
											$tot = $row2[4] - $row2[5];
											$westado = "FACTURADO";
											if($tot > 0)
											{
												$totsfac += ($row2[4] - $row2[5]);
												$westado = "SIN FACTURAR";
											}
											else
												$totfac += $row2[4];
											echo "<tr><td id=tipo10H>".$row2[0]."</td><td id=tipo10G>".$row2[1]."</td><td id=tipo10G>".$row2[2]."</td><td id=tipo10I>".number_format((double)$row2[3],0,'.',',')."</td><td id=tipo10I>$".number_format((double)$row2[4],0,'.',',')."</td><td id=tipo10I>$".number_format((double)$row2[5],0,'.',',')."</td><td id=tipo10G>".$westado."</td></tr>";
										}
										echo "<tr><td id=tipo10J colspan=6>TOTAL CARGOS FACTURADOS</td><td id=tipo10JJ>$".number_format((double)$totfac,0,'.',',')."</td></tr>";
										echo "<tr><td id=tipo10J colspan=6>TOTAL CARGOS SIN FACTURAR</td><td id=tipo10JJ>$".number_format((double)$totsfac,0,'.',',')."</td></tr>";
									}
									echo "</table></center><br><br>";

									//INICIO SALDO EN LAS FACTURAS  Y CARGOS DEL PACIENTE
									$query = "select fenfac, fensal from ".$empresa."_000106,".$empresa."_000066,".$empresa."_000018 ";
									$query .= "  where Tcarhis = '".$whis."' ";
									//$query .= "    and Tcaring = '".$wing."' ";
									$query .= "    and tcardoc = '".$paciente."' ";
									$query .= "    and Tcarest = 'on' ";
									$query .= "    and (Tcarfex + Tcarfre) > 0 ";
									$query .= "    and ".$empresa."_000106.id = Rcfreg ";
									$query .= "    and Rcffac = fenfac ";
									$query .= "    and fentip = '01-PARTICULAR' ";
									$query .= "    and fensal > 0 ";
									$query .= "    group by  1,2 ";
									$query .= "    order by 1,2 ";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									$tot=0;
									echo "<center><table border=0 align=center id=tipo1>";
									echo "<tr><td id=tipo10C colspan=2 align=center>FACTURAS PARTICULARES PENDIENTES DE RECAUDO ".date("Y-m-d")." </td></tr>";
									echo "<tr><td id=tipo10C align=center>NRO FACTURA</td><td id=tipo10C align=center>VALOR</td></tr>";
									if($num2 > 0)
									{
										for ($i=0;$i<$num2;$i++)
										{
											$row2 = mysql_fetch_array($err1);
											$tot += $row2[1];
											if($row2[1] > 0)
												echo "<tr><td id=tipo10F align=center>".$row2[0]."</td><td id=tipo10D align=center>$".number_format((double)$row2[1],0,'.',',')."</td></tr>";
										}
									}
									echo "<tr><td id=tipo10E align=center>POR VALOR DE :</td><td id=tipo10C align=center>$".number_format((double)$tot,0,'.',',')."</td></tr>";
									$wfacT += $tot;
									echo "</table></center><br><br>";
									$query = "select Tcaring, Tcartercod, Tcarternom ,sum(Tcarvto - (Tcarfex + Tcarfre)) from ".$empresa."_000106  ";
									$query .= "  where Tcarhis = '".$whis."' ";
									$query .= "    and Tcaring = '".$wing."' ";
									$query .= "    and tcardoc = '".$paciente."' ";
									$query .= "    and Tcarest = 'on' ";
									$query .= "   and (Tcarvto - (Tcarfex + Tcarfre)) != 0  ";
									$query .= "   group by  1,2,3 ";
									$query .= "   order by  1,2 ";
									$err1 = mysql_query($query,$conex);
									$num2 = mysql_num_rows($err1);
									$tot=0;
									echo "<center><table border=0 align=center id=tipo1>";
									echo "<tr><td id=tipo10C colspan=3 align=center>CARGOS PENDIENTES DE FACTURAR A ".date("Y-m-d")." </td></tr>";
									echo "<tr><td id=tipo10C align=center>INGRESO</td><td id=tipo10C align=center>ODONTOLOGO</td><td id=tipo10C align=center>VALOR</td></tr>";
									if($num2 > 0)
									{
										for ($i=0;$i<$num2;$i++)
										{
											$row2 = mysql_fetch_array($err1);
											if($row2[3] > 0)
											{
												$tot += $row2[3];
												echo "<tr><td id=tipo10F align=center>".$row2[0]."</td><td id=tipo10F align=center>".$row2[1]."-".$row2[2]."</td><td id=tipo10D align=center>$".number_format((double)$row2[3],0,'.',',')."</td></tr>";
											}
										}
									}
									echo "<tr><td id=tipo10E colspan=2 align=center>POR VALOR DE :</td><td id=tipo10C align=center>$".number_format((double)$tot,0,'.',',')."</td></tr>";
									$wcarT += $tot;
									$wsaldoT = $wpreT + $wcarT + $wfacT;
									echo "</table></center><br><br>";
									echo "<center><table border=0 align=center id=tipo1>";
									echo "<tr><td id=tipo10I3 align=center colspan=2>SALDOS</td></tr>";
									echo "<tr><td id=tipo10H1 align=center>PRESUPUESTADO : </td><td id=tipo10H2 align=center>$".number_format((double)$wpreT,0,'.',',')."</td></tr>";
									echo "<tr><td id=tipo10N1 align=center>CARGADO : </td><td id=tipo10N2 align=center>$".number_format((double)$wcarT,0,'.',',')."</td></tr>";
									echo "<tr><td id=tipo10H1 align=center>FACTURADO : </td><td id=tipo10H2 align=center>$".number_format((double)$wfacT,0,'.',',')."</td></tr>";
									echo "<tr><td id=tipo10I1 align=center>ABONO MAXIMO : </td><td id=tipo10I2 align=center>$".number_format((double)$wsaldoT,0,'.',',')."</td></tr>";
									echo "</table></center><br><br>";
								}
								else
								{
									echo "<br><br><center><table border=0 aling=center>";
									echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
									echo "<tr><td align=center colspan=2>ACTUALIZACION DE ODONTOGRAMA X PACIENTE</td></tr>";
									echo "<tr><td id=tipo18B><IMG SRC='/matrix/images/medical/root/Malo.ico'></TD><TD id=tipo18B>PACIENTE NO EXISTE !!!!!</td></tr>";
									echo "<tr><td align=center colspan=2><input type='submit' value='ENTER'></td></tr>";
									echo "</table><br><br></center>";
								}
							}
							//CIERRE ESTADO DE CUENTA DEL PACIENTE
						break;
					}
				} // CIERRE DE CICLO DE VERIFICACION DE EXISTENCIA DE PACIENTE
				else
				{
					echo "<br><br><center><table border=0 aling=center>";
					echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
					echo "<tr><td align=center colspan=2>ACTUALIZACION DE ODONTOGRAMA X PACIENTE</td></tr>";
					echo "<tr><td id=tipo18B><IMG SRC='/matrix/images/medical/root/Malo.ico'></TD><TD id=tipo18B>PACIENTE NO EXISTE !!!!!</td></tr>";
					echo "<tr><td align=center colspan=2><input type='submit' value='ENTER'></td></tr>";
					echo "</table><br><br></center>";
				}
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>ODONTOLOGO NO REGISTRADO EN LA TABLA 51 -- COMUNIQUESE CON SISTEMAS!!!!</MARQUEE></FONT>";
			echo "<br><br>";
			echo "<input type='submit' value='ENTER'></center>";
		}
	}
	else
	{
		echo "<table border=0 align=center id=tipo5>";
		echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LA HISTORIA CLINICA NO PUEDE SER USADA FUERA DE LA INSTITUCION !!!</td></tr>";
		echo "</table></center>";
	}
}
?>

</body>
</html>
