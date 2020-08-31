<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<script src="efecto.php"></script>
<SCRIPT LANGUAGE="JavaScript1.2">

function nuevoAjax()
{
	var xmlhttp=false;
	try
	{
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
	}
	catch(e)
	{
		try
		{
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); }

	return xmlhttp;
}

function ajaxquery(fila, entrada)
{
	var x = new Array();
	document.images['status'].src='/matrix/images/medical/reloj.gif';

	//me indica que hacer con el id, segun tipo de entrada


	switch(entrada)
	{

		case "1": //arreglar drop down lugar de origen
		x[1]=document.tabla1.mes.value;
		x[2]=document.tabla1.ano.value;
		x[3]=document.tabla1.ano2.value;
		x[4]=document.tabla1.area.value;
		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=1&bandera=3";
		break;

		case "2": //arreglar drop down entidad
		x[1]=document.tabla2.mes.value;
		x[2]=document.tabla2.ano.value;
		x[3]=document.tabla2.ano2.value;
		x[4]=document.tabla2.area.value;
		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=2&bandera=3";
		break;

		case "3": //arreglar drop down entidad
		x[1]=document.tabla3.mes.value;
		x[2]=document.tabla3.ano.value;
		x[3]=document.tabla3.ano2.value;
		x[4]=document.tabla3.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=3&bandera=3";
		break;

		case "4": //arreglar drop down entidad
		x[1]=document.tabla4.mes.value;
		x[2]=document.tabla4.ano.value;
		x[3]=document.tabla4.ano2.value;
		x[4]=document.tabla4.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=4&bandera=3";
		break;

		case "5": //arreglar drop down entidad
		x[1]=document.tabla5.mes.value;
		x[2]=document.tabla5.ano.value;
		x[3]=document.tabla5.ano2.value;
		x[4]=document.tabla5.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=5&bandera=3";
		break;

		case "6": //arreglar drop down entidad
		x[1]=document.tabla6.mes.value;
		x[2]=document.tabla6.ano.value;
		x[3]=document.tabla6.ano2.value;
		x[4]=document.tabla6.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=6&bandera=3";
		break;

		case "7": //arreglar drop down entidad
		x[1]=document.tabla7.mes.value;
		x[2]=document.tabla7.ano.value;
		x[3]=document.tabla7.ano2.value;
		x[4]=document.tabla7.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=7&bandera=3";
		break;

		case "8": //arreglar drop down entidad
		x[1]=document.tabla8.mes.value;
		x[2]=document.tabla8.ano.value;
		x[3]=document.tabla8.ano2.value;
		x[4]=document.tabla8.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=8&bandera=3";
		break;

		case "9": //arreglar drop down entidad
		x[1]=document.tabla9.mes.value;
		x[2]=document.tabla9.ano.value;
		x[3]=document.tabla9.ano2.value;
		x[4]=document.tabla9.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=9&bandera=3";
		break;

		case "10": //arreglar drop down entidad
		x[1]=document.tabla10.mes.value;
		x[2]=document.tabla10.ano.value;
		x[3]=document.tabla10.ano2.value;
		x[4]=document.tabla10.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=10&bandera=3";
		break;

		case "11": //arreglar drop down entidad
		x[1]=document.tabla11.mes.value;
		x[2]=document.tabla11.ano.value;
		x[3]=document.tabla11.ano2.value;
		x[4]=document.tabla11.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=11&bandera=3";
		break;

		case "12": //arreglar drop down entidad
		x[1]=document.tabla12.mes.value;
		x[2]=document.tabla12.ano.value;
		x[3]=document.tabla12.ano2.value;
		x[4]=document.tabla12.area.value;

		st="mes="+x[1]+"&ano="+x[2]+"&ano2="+x[3]+"&area="+x[4]+"&wope=12&bandera=3";
		break;
	}

	ajax=nuevoAjax();
	ajax.open("POST", st, true);

	ajax.open("POST", "informeComentarios.php",true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(st);


	ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
			if(ajax.status==200){
				document.images['status'].src='/matrix/images/medical/blanco.png';
				document.getElementById(fila).innerHTML=ajax.responseText;
			}
			else
			{
				document.getElementById(fila).innerHTML="Error:"+ajax.status;
			}


		}
	}
	ajax.send(null);
}

function Seleccionar()
{
	document.ingreso.bandera.value=1;
	document.ingreso.submit();
}


</SCRIPT>

</head>

<?php
include_once("conex.php");
/****************************************************************************
 * Actualizaciones
 * --------------------------------------------------------------------------
 * Fecha 2018-05-11
 * Arleyda Insignares C. - Se crean nuevas variables para totalizar por Entidad y se modifica query
 *                         en el where 'campo entidad ccoemp' filtrando solo por el codigo.     
 * 
 * Fecha 2018-04-11
 * Arleyda Insignares C. - Se modifica consulta por Entidad, extrayendo el primero dato del string , de esta forma consulta
 *                         solamente por código y sin tener en cuenta el nombre de la Entidad.
 *                       - Se adiciona en la consulta de comentarios la relacion con la tabla Magenta_000025 
 *                         (Maestro de comentarios). 
 * 
 * Fecha 2016-05-20 
 * Arleyda Insignares C. -Se cambia encabezado con ultimo diseño 
 
 * Fecha 2013-08-29
   Edwar Jaramillo: El campo usado como filtro para la fecha antes estaba Fecha_data pero ahora se cambia a Ccofori para que sume los registros NO en la fecha en que se crean en la base de datos sino para la fecha para la que generan y se quiere que sumen.

 * Fecha: 2009-05-13
 * Por: Edwin Molina Grisales
 * Codigo de requerimiento: 1485
 *
 * -  Se anexa una nueva tabla en la opción 3 (Como se da respuesta a los
 *    usuarios) dependiendo de la fecha y la unidad elegida (Area).
 *
 * -  Se cambia la presentación inicial a la que se tiene actualmente.
 Actualizacion: Se cambia el formato en el return de la funcion promedio2 Viviana Rodas 2012-06-04
 ***************************************************************************/
?>
<body>
<?php

////////////////////////////////////////////////////////FUNCIONES///////////////////////////////

//calcula el mes de acuerdo al numero enviado
function nombre_mes($mes){
	switch ($mes){
		case 1:
		$nombre_mes="Enero";
		break;
		case 2:
		$nombre_mes="Febrero";
		break;
		case 3:
		$nombre_mes="Marzo";
		break;
		case 4:
		$nombre_mes="Abril";
		break;
		case 5:
		$nombre_mes="Mayo";
		break;
		case 6:
		$nombre_mes="Junio";
		break;
		case 7:
		$nombre_mes="Julio";
		break;
		case 8:
		$nombre_mes="Agosto";
		break;
		case 9:
		$nombre_mes="Septiembre";
		break;
		case 10:
		$nombre_mes="Octubre";
		break;
		case 11:
		$nombre_mes="Noviembre";
		break;
		case 12:
		$nombre_mes="Diciembre";
		break;
	}
	return $nombre_mes;
}

function encabezado1()
{
	$wactualiz = '2018-05-11';
	encabezado("INFORME DE COMENTARIOS Y SUGERENCIAS",$wactualiz, "clinica");
	//encabezado("INFORME DE COMENTARIOS Y SUGERENCIAS", "1.0 Mayo 13 de 2009" ,"magenta");
	echo "<br><br>";
//	$wautor="Carolina Castano P.";
//	$wversion='2006-01-10';
//
//	echo "<table align='right'>" ;
//	echo "<tr>" ;
//	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
//	echo "</tr>" ;
//	echo "<tr>" ;
//	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
//	echo "</tr>" ;
//	echo "</table></br></br></br>" ;

//	echo "<table align='center' border='3' bgcolor='#336699' >\n" ;
//	echo "<tr>" ;
//	echo "<td><img src='/matrix/images/medical/root/magenta.gif' height='61' width='113'></td>";
//	echo "<td><font color=\"#ffffff\"><font size=\"5\"><b>&nbsp;SISTEMA DE COMENTARIOS Y SUGERENCIAS &nbsp;</br></b></font></font></td>" ;
//	echo "</tr>" ;
//	echo "</table></br></br>" ;

//	echo "<center><b><font size=\"4\"><A HREF='informeComentarios.php'><font color='#00008B'>INFORME DE COMENTARIOS Y SUGERENCIAS </font></a></b></font></center>\n" ;
//	echo "<center><b><font size=\"2\"><font color='#00008B'> informeComentarios.php</font></font></center></br></br>\n" ;

	echo "	<center><img src='/matrix/images/medical/blanco.png' name='status' WIDTH=50 HEIGHT=50></center>";

/*	echo "<table align='right' >\n" ;
	echo "<tr>" ;
	echo "<td VALIGN=TOP NOWRAP>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A HREF='javascript:window.showMenu(window.mWhite1);' onMouseOver='window.showMenu(window.mWhite1);'><font color=\"#D02090\" size=\"4\"><b>Menu</A>&nbsp/</b></font></td>";
	echo "<td><b><font size=\"4\"><A HREF='ayuda.mht' target='new'><font color=\"#D02090\">Ayuda</font></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br>" ;*/

  ?>
  <SCRIPT LANGUAGE="JavaScript1.2">

  if (document.all) {

  	window.myMenu = new Menu();
  	myMenu.addMenuItem("my menu item A");
  	myMenu.addMenuItem("my menu item B");
  	myMenu.addMenuItem("my menu item C");
  	myMenu.addMenuItem("my menu item D");

  	window.mWhite1 = new Menu("White");
  	mWhite1.addMenuItem("Ingreso de Comentarios", "self.window.location='pagina1.php'");
  	mWhite1.addMenuItem("Lista de comentarios", "self.window.location='listaMagenta.php'");
  	mWhite1.bgColor = "#ADD8E6";
  	mWhite1.menuItemBgColor = "white";
  	mWhite1.menuHiliteBgColor = "#336699";

  	myMenu.writeMenus();
  }

  </SCRIPT>
  <?php
}

//calcula el mes de acuerdo al numero enviado
function Colorear($mes){
	switch ($mes){
		case 1:
		$color="#ff99ff";
		break;
		case 2:
		$color="#99ffff";
		break;
		case 3:
		$color="#99ff99";
		break;
		case 4:
		$color="#9966ff";
		break;
		case 5:
		$color="#66ccff";
		break;
		case 6:
		$color="#ffff99";
		break;
		case 7:
		$color="#33cccc";
		break;
		case 8:
		$color="#99cccc";
		break;
		case 9:
		$color="#ffccff";
		break;
		case 10:
		$color="#ff9966";
		break;
		case 11:
		$color="#00ffcc";
		break;
		case 12:
		$color="#cccc99";
		break;
	}
	return $color;
}

//calcula el promedio de dos valores y evita division por cero
function promedio($val1, $val2){
	if ($val2>0)
	{
		$variac1=($val1-$val2)/$val2;
		$variac1=$variac1*100;
		$variac1=round($variac1 * 100) / 100 ;
	}else
	{
		$variac1='Sin';
	}


	return number_format((float)$variac1,2,".",",");
}

//calcula el promedio de dos valores sin signos y evita division por cero
function promedio2($val1, $val2){
	if ($val2>0)
	{
		$variac1=($val1*100)/$val2;
		$variac1=round($variac1 * 100) / 100 ;
	}else
	{
		$variac1='Sin';
	}
	return number_format((float)$variac1,2,".",",");
}
/////////////////////////////////////////////////inicializacion de variables///////////////////////////////////


/**
	 * Include para conexion a bd Matrix
	 *
	 */
//

include_once("root/comun.php");
//


$conex = obtenerConexionBD("matrix");
//if(!isset($wemp_pmla)){
//	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
//}

//$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

//encabezado("REGISTRO DE APLICACION POR CENTRO DE COSTOS", "1.0 Mayo 11 de 2009" ,"magenta");

$empresa='magenta';
Session_start();
if(!isset($_SESSION['user']))
echo "error";

else
{
	if (!isset($bandera) or $bandera!=3)
	{
		encabezado1();
	}

	//averiguo quien es el usuario
	$inicial=strpos($user,"-");
	$aut=substr($user, $inicial+1, strlen($user));

	//selecciono las areas de interes para el codigo del usuario
	$query ="SELECT A.crecod, B.id_area, C.carcod, C.carnom FROM ".$empresa."_000021 A, ".$empresa."_000020 B, ".$empresa."_000019 C  where A.crecod='".$aut."' and B.id_responsable=A.crecod and C.carcod=B.id_area ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	if ($num>0)
	{
		$row=mysql_fetch_row($err);
		$areNomS="<option>Todas</option>";
		if ($row[3]!='Servicio Magenta')
		{
			for($i=0;$i<$num;$i++)
			{
				$areNomS=$areNomS."<option>".$row[2]."-".$row[3]."</option>";
				//guardo un vector con id y nombre de las areas, por si escogen todas las areas poder recorrerlo
				$areaL['id'][$i]=$row[1];
				$areaL['nombre'][$i]=$row[2]."-".$row[3];
				$row=mysql_fetch_row($err);
			}
		}else //para servicio Magenta, se mostraran o habilitan todas las areas
		{
			$query ="SELECT carcod, carcod, carnom FROM  ".$empresa."_000019 ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores y un vector con los resultados
			{
				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					$areNomS=$areNomS."<option>".$row[1]."-".$row[2]."</option>";
					//guardo un vector con id y nombre de las areas, por si escogen todas las areas poder recorrerlo
					$areaL['id'][$i]=$row[0];
					$areaL['nombre'][$i]=$row[1]."-".$row[2];
				}
			}
		}

		$numAre=$num;

		//////////////////////PROGRAMA, PANTALLA UNO, VALORES DEL REPORTE////////////////////////////

		if (!isset($mes))
		{
			$mes=date('m');
		}
		$mesS="<option>".$mes."</option>";

		for($i=1;$i<=12;$i++)
		{
			if ($i<10)
			$mes1="0".$i;
			else
			$mes1=$i;

			if ($mes1 != $mes)
			{
				$mesS=$mesS."<option>".$mes1."</option>";
			}

		}


		//escojo la fecha del comentario mas viejo, para ofrecer el reporte desde ahi
//		$query="select Min(ccofori) from magenta_000017";
//		$err=mysql_query($query,$conex);
//		$row=mysql_fetch_row($err);
//		$n=explode('-',$row[0]);

		$fecha=1990;//$n[0];

		if (!isset($ano))
		{
			$ano=date('Y');
		}
		$anoS="<option selected>".$ano."</option>";
		$ano2=$ano-1;
		$ano2S="<option selected>".$ano2."</option>";

		for($i=$fecha;$i<=date('Y');$i++)
		{

			if ($i != $ano)
			{
				$anoS=$anoS."<option>".$i."</option>";
			}
			$j=$i-1;
			if ($ano2 != $j and $j>=$fecha)
			{
				$ano2S=$ano2S."<option>".$j."</option>";
			}
		}

		if (!isset($bandera) or $bandera==1)
		{

			//pinto formulario inicial
			echo "<center><font color='#00008B'>SELECCIONE LAS CONDICIONES DEL INFORME</font></center></BR>";

			echo "<fieldset style='border:solid;border-color:#00008B; width=700' align=center></br>";

			echo "<form NAME='ingreso' ACTION='informeComentarios.php' METHOD='POST'>";
			echo "<table align='center' width='450' >";
			echo "<tr>";

			echo "<td width=40%><font size=2  color='#00008B' face='arial'><b>MES:&nbsp</b></td>";
			echo "<td width=10%><select name='mes' >$mesS</select></td>";
			echo "<td width=20% ALIGN='CENTER'><font size=2  color='#00008B' face='arial'><b>ANO:&nbsp</b></td>";
			echo "<td width=30%><select name='ano' onchange='Seleccionar()'>$anoS</select></td>";

			echo "</td>";
			echo "</tr>";
			echo "<tr>";

			echo "<td width=40% ><font size=2  color='#00008B' face='arial'><b>ANO COMPARATIVO:</font></b></td>";
			echo "<td colspan='3'  width='60%'><select name='ano2'>$ano2S</select></td>";

			echo "</td>";
			echo "</tr>";
			echo "<tr>";

			echo "<td   width='40%'><font size=2  color='#00008B' face='arial'><b>UNIDAD:&nbsp</b></td>";
			echo "<td   width='60%' colspan='3'><select name='area' >$areNomS</select></td>";
			echo "<input type='hidden' name='bandera' value='2' />";
			echo "<input type='hidden' name='numAre' value='".$numAre."' />";

			echo "</tr></TABLE></br>";
			echo "<TABLE align=center><tr>";
			echo "<tr><td align=center ><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='ACEPTAR' ></td></tr>";
			echo "</TABLE>";
			echo "</td>";
			echo "</tr>";
			echo "</form>";
			echo "</fieldset>";
		}

		//////////////////////////////MUESTRA DE NENU DEL INFORME//////////////////////////////////////////////
		if (isset($bandera) and $bandera!=1)
		{
			if ($bandera!=3)
			echo "<center><b><font size=\"3\"><font color='#00008B'>Reporte para: ".nombre_mes($mes)." de ".$ano."</font></b></font></center></BR>\n" ;

			// AJUSTO LAS FECHAS LIMITE Y CUENTO EL NUMERO DE MESES

			//cuento el número de meses
			if (substr($mes,0,1)>0)
			$nmeses=$mes;
			else
			$nmeses=substr($mes,1,1);


			//busco el id del area, si se escogio una
			if ($area!='Todas')
			{
				$area1=explode('-',$area); //buscar el id del area
				$query ="SELECT carcod, carnom FROM ".$empresa."_000019 where carcod='".$area1[0]."' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0)
				{
					$row=mysql_fetch_row($err);
					$idArea=$row[0];
					$unidadS['id'][0]= $row[0];
					$unidadS['nombre'][0]= $row[0]."-".$row[1];
				}
			}

			//METO LAS AREAS EN UN VECTOR PARA PODER RECORRERLO DESPUES HACIENDO QUERYS
			$query ="SELECT carcod, carnom FROM  ".$empresa."_000019 ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores y un vector con los resultados
			{
				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					$unidad['id'][$i]= $row[0];
					$unidad['nombre'][$i]= $row[0]."-".$row[1];
					if (isset($unidadS['id'][0]) and $unidadS['id'][0]==$unidad['id'][$i])
					$puesto=$i;
				}
			}

			$unidades=$num;

			if ($bandera==2)
			{
				//pinto los titulos de menu del informe
				$fila='ajaxquery("x1","1")';
				echo "<div id='x1'>";
				echo "<form name='tabla1' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial' ALIGN=LEFT><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>1. Total comentarios  $ano - $ano2 y variacion</a></font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x2","2")';
				echo "<div id='x2'>";
				echo "<form name='tabla2' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>2. Comentarios Positivos y por Mejorar ".$ano2." ".$ano." - Nivel de Satisfaccion Comentarios y Sugerencias</a></font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x3","3")';
				echo "<div id='x3'>";
				echo "<form name='tabla3' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>3. Como se da respuesta a los usuarios</a></font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x4","4")';
				echo "<div id='x4'>";
				echo "<form name='tabla4' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>4. Clasificacion por tipo de comentarios aprobados</a></font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x5","5")';
				echo "<div id='x5'>";
				echo "<form name='tabla5' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>5. El Usuario utilizaria de nuevo nuestros servicios</font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x6","6")';
				echo "<div id='x6'>";
				echo "<form name='tabla6' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>6. Causas mas relevantes de los comentarios por mejorar y positivos</font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x7","7")';
				echo "<div id='x7'>";
				echo "<form name='tabla7' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>7. Causas mas relevantes de los comentarios por unidad</a></font></b></br>";
				echo "</form>";
				echo "</div>";


				$fila='ajaxquery("x8","8")';
				echo "<div id='x8'>";
				echo "<form name='tabla8' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>8. COMPARATIVO Comentarios Positivos y Por Mejorar entre unidades y Nivel de Satisfaccion</a></font></b></br>";
				echo "</form>";
				echo "</div>";


				$fila='ajaxquery("x9","9")';
				echo "<div id='x9'>";
				echo "<form name='tabla9' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>9. SEMAFORIZACION. ".$ano2." - ".$ano."</a></font></b></br>";
				echo "</form>";
				echo "</div>";


				$fila='ajaxquery("x10","10")';
				echo "<div id='x10'>";
				echo "<form name='tabla10' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>10. Comentarios por entidad</a></font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x11","11")';
				echo "<div id='x11'>";
				echo "<form name='tabla11' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>11. Comentarios afinidad</a></font></b></br>";
				echo "</form>";
				echo "</div>";

				$fila='ajaxquery("x12","12")';
				echo "<div id='x12'>";
				echo "<form name='tabla12' >";
				echo "<input type='hidden' name='mes' value='".$mes."' />";
				echo "<input type='hidden' name='ano' value='".$ano."'/>";
				echo "<input type='hidden' name='ano2' value='".$ano2."' />";
				echo "<input type='hidden' name='area' value='".$area."' />";
				echo "<font size=2  color='#00008B' face='arial'><b><input type='radio' name='nume' onclick='".$fila."' value=3 color=#FFFFFF>12. Comentarios X Lugar de Origen</a></font></b></br>";
				echo "</form>";
				echo "</div>";
			}


			///////////////////////////////////////MUESTRA ALGUNA ACCION EJECUTADA EN AJAX//////////////////
			if ($bandera==3) //se sabe que se recibio alguna peticion de ajax
			{


				//hago la primera operacion de contabilidad de comentarios
				$agrado=0;
				$desagrado=0;
				$agrado2=0;
				$desagrado2=0;

				for($i=0;$i<$unidades;$i++)
				{
					$unidad['agradoTotal'][$i]=0;
					$unidad['desagradoTotal'][$i]=0;
					$unidad['agradoTotal2'][$i]=0;
					$unidad['desagradoTotal2'][$i]=0;

					for($j=1;$j<=$nmeses;$j++)
					{
						if ($i==0)
						{
							$cmes['agrado'][$j]=0;
							$cmes['desagrado'][$j]=0;
							$cmes['agrado2'][$j]=0;
							$cmes['desagrado2'][$j]=0;
							$cmes['total'][$j]=0;
							$cmes['total2'][$j]=0;
							$cmes['fisicos'][$j]=0;
							$cmes['fisicos2'][$j]=0;
						}

						if ($j<10)
						{
							$date1=$ano."-0".$j."-01";
							$date2=$ano."-0".$j."-31";
							$date3=$ano2."-0".$j."-01";
							$date4=$ano2."-0".$j."-31";
						}else
						{
							$date1=$ano."-".$j."-01";
							$date2=$ano."-".$j."-31";
							$date3=$ano2."-".$j."-01";
							$date4=$ano2."-".$j."-31";
						}

						$query ="SELECT A.id, A.ccofori, B.cmonum ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmotip='Agrado' ";

						$err=mysql_query($query,$conex);
						$num1=mysql_num_rows($err);
						$unidad[$i.'agrado'][$j]=$num1; //numero de comentarios de agrado por unidad por mes
						$cmes['agrado'][$j]=$cmes['agrado'][$j]+$num1; //numero de comentarios de agrado del mes

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmotip='Desagrado' ";
						$err=mysql_query($query,$conex);
						$num2=mysql_num_rows($err);
						$unidad[$i.'desagrado'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
						$cmes['desagrado'][$j]=$cmes['desagrado'][$j]+$num2; //numero de comentarios de desagrado del mes

						$cmes['total'][$j]=$cmes['total'][$j]+$num1+$num2;

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmotip='Agrado' ";
						$err=mysql_query($query,$conex);
						$num3=mysql_num_rows($err);
						$unidad[$i.'agrado2'][$j]= $num3;  //numero de comentarios de desagrado por unidad por mes
						$cmes['agrado2'][$j]=$cmes['agrado2'][$j]+$num3; //numero de comentarios de agrado del mes


						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmotip='Desagrado' ";
						$err=mysql_query($query,$conex);
						$num4=mysql_num_rows($err);
						$unidad[$i.'desagrado2'][$j]= $num4;  //numero de comentarios de desagrado por unidad por mes
						$cmes['desagrado2'][$j]=$cmes['desagrado2'][$j]+$num4; //numero de comentarios de desagrado del mes

						$cmes['total2'][$j]=$cmes['total2'][$j]+$num3+$num4;

						//numero de comentarios de agrado y desagrado por unidad
						$unidad['agradoTotal'][$i]=$unidad['agradoTotal'][$i]+$unidad[$i.'agrado'][$j];
						$unidad['desagradoTotal'][$i]=$unidad['desagradoTotal'][$i]+$unidad[$i.'desagrado'][$j];
						$unidad['agradoTotal2'][$i]=$unidad['agradoTotal2'][$i]+$unidad[$i.'agrado2'][$j];
						$unidad['desagradoTotal2'][$i]=$unidad['desagradoTotal2'][$i]+$unidad[$i.'desagrado2'][$j];
					}

					//numero de comentarios de agrado y desagrado
					$agrado=$agrado+$unidad['agradoTotal'][$i];
					$desagrado=$desagrado+$unidad['desagradoTotal'][$i];
					$agrado2=$agrado2+$unidad['agradoTotal2'][$i];
					$desagrado2=	$desagrado2+$unidad['desagradoTotal2'][$i];
				}

				//numero total de comentarios
				$comentarios=$agrado+$desagrado;
				$comentarios2=$agrado2+$desagrado2;

				//ahora muestro cosas dependiendo del informe
				switch ($wope)
				{

					//consulto los comentarios, del mes del ano, del ano total, del mes del ano pasado y del ano pasado acumulado

					case '1':

					/******************************************************************************/
					//grafico primer indicador:

					echo "<font size=2  color='#00008B' face='arial'><b>1. Total comentarios  $ano - $ano2 y variacion</font></b></br>";

					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center>";

					echo "<tr>";
					echo "<td align = center>&nbsp;</td>";
					echo "<td align = center ><b><font size=2  face='arial'>".$ano."</font></b></td>";
					echo "<td align = center ><b><font size=2 face='arial'>".$ano2."</font></b></td>";
					echo "<td align = center ><b><font size=2  face='arial'>Variac.</font></b></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td>Total ".nombre_mes($mes)."</td>";
					echo "<td align = center><font size=2  face='arial'>".$cmes['total'][$nmeses]."</font></td>";
					echo "<td align = center><font size=2 face='arial'>".$cmes['total2'][$nmeses]."</font></td>";
					echo "<td align = center ><font size=2  face='arial'>".promedio($cmes['total'][$nmeses],$cmes['total2'][$nmeses])."%</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td>Acumulado</td>";
					echo "<td align = center><font size=2  face='arial'>".$comentarios."</font></td>";
					echo "<td align = center><font size=2 face='arial'>".$comentarios2."</font></td>";
					echo "<td align = center ><font size=2  face='arial'>".promedio($comentarios, $comentarios2)."%</font></td>";
					echo "</tr>";

					echo "</table></br>";

					/******************************************************************************/

					break;

					case '2':

					// Busqueda de cantidad de formatos físicos

					for($j=1;$j<=$nmeses;$j++)
					{
						if ($j==1)
						{
							$cmes['fisicos'][$j]=0;
							$cmes['fisicos2'][$j]=0;
							$fitotal=0;
							$fitotal2=0;
						}

						if ($j<10)
						{
							$date1=$ano."-0".$j."-01";
							$date2=$ano."-0".$j."-31";
							$date3=$ano2."-0".$j."-01";
							$date4=$ano2."-0".$j."-31";
						}else
						{
							$date1=$ano."-".$j."-01";
							$date2=$ano."-".$j."-31";
							$date3=$ano2."-".$j."-01";
							$date4=$ano2."-".$j."-31";
						}

						if ($area=='Todas')
						{

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A  where A.Ccofori between '".$date1."'  and '".$date2."'  ";
							//echo $query;
							$err=mysql_query($query,$conex);
							$can1=mysql_num_rows($err);
							$cmes['fisicos'][$j]=$cmes['fisicos'][$j]+$can1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date3."' and  '".$date4."' ";
							$err=mysql_query($query,$conex);
							$can2=mysql_num_rows($err);
							$cmes['fisicos2'][$j]=$cmes['fisicos2'][$j]+$can2; //numero de comentarios de agrado del mes

						}
						else
						{
							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area=".$unidad['id'][0]." group by A.id ";
							$err=mysql_query($query,$conex);
							$can1=mysql_num_rows($err);
							$cmes['fisicos'][$j]=$cmes['fisicos'][$j]+$can1; //numero de comentarios fisicos del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area=".$unidad['id'][0]." group by A.id ";
							$err=mysql_query($query,$conex);
							$can2=mysql_num_rows($err);
							$cmes['fisicos2'][$j]=$cmes['fisicos2'][$j]+$can2; //numero de comentarios fisicos del mes

							$cmes['fitotal'][$j]=0;
							$cmes['fitotal2'][$j]=0;
						}

						$fitotal=$fitotal+$can1;
						$fitotal2=$fitotal2+$can2;
					}

					/******************************************************************************/
					// grafico segundo indicador: 2. Comentarios Positivos y por Mejorar 2005 2006 - Nivel de Satisfaccion Comentarios y Sugerencias

					echo "<font size=2  color='#00008B' face='arial'><b>2. Comentarios Positivos y por Mejorar ".$ano2." ".$ano." - Nivel de Satisfaccion Comentarios y Sugerencias</font></b></br></br>";

					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='6' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='7' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";
					echo "<td align = center  colspan='3'><font size=2  face='arial'>&nbsp;</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center rowspan=2><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2  face='arial'>Positivos</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2 face='arial'>Por Mejorar</font></td>";
					echo "<td align = center   rowspan=2 bgcolor='#ff00cc'> <font size=2  face='arial'>Total</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#cc66ff'><font size=2  face='arial'>Formatos Fisicos</font></td>";
					echo "<td align = center colspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Positvos</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>Por Mejorar</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Vacias</font></td>";
					echo "<td align = center  rowspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Total</font></td>";
					echo "<td align = center rowspan=2  bgcolor='#6699ff'><font size=2  face='arial'>Formatos Fisicos</font></td>";
					echo "<td align = center   rowspan=2 bgcolor='#ff9933'><font size=2 face='arial'>Variacion Positivos</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#ff9933' > <font size=2  face='arial'>Variacion por Mejorar</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#ff9933'><font size=2  face='arial'>Variacion Total</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";

					echo "</tr>";

					for($i=1;$i<=$nmeses;$i++)
					{
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['agrado2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['agrado2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['desagrado2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['desagrado2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['total2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['fisicos2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['agrado'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['agrado'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['desagrado'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['desagrado'][$i], $cmes['total'][$i])."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>0</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['total'][$i]."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['fisicos'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio ($cmes['agrado'][$i], $cmes['agrado2'][$i])."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".promedio ($cmes['desagrado'][$i], $cmes['desagrado2'][$i])."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio ($cmes['total'][$i], $cmes['total2'][$i])."%</font></td>";

						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$agrado2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($agrado2, $comentarios2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$desagrado2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($desagrado2, $comentarios2) ."%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial' >".$comentarios2."</font></td>";
					echo "<td align = center bgcolor='#cc66ff'><font size=2 face='arial'>".$fitotal2."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".$agrado."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($agrado, $comentarios) ."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$desagrado."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($desagrado, $comentarios)."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>0</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".$comentarios."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$fitotal."</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".promedio ($agrado, $agrado2)."%</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>".promedio ($desagrado, $desagrado2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff9933'><font size=2  face='arial'>".promedio ($comentarios, $comentarios2)."%</font></td>";

					echo "</tr>";

					//busco el total del ano antepasado
					$ano3=$ano-2;
					if ($area=='Todas')
					{

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$ano3."-01-01'  and '".$ano3."-".$mes."31'  and B.id_comentario =A.id  ";
						//echo $query;
						$err=mysql_query($query,$conex);
						$can1=mysql_num_rows($err);
						$pasado=	$can1;

					}
					else
					{

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$ano3."-01-01'  and '".$ano3."-".$mes."31'  and B.id_comentario =A.id and B.id_area=".$unidad['id'][0]." ";
						//echo $query;
						$err=mysql_query($query,$conex);
						$can1=mysql_num_rows($err);
						$pasado=	$can1;
					}
					echo "<tr>";
					echo "<td  colspan=5><font size=2  face='arial'>TOTAL ANO ". $ano3.":</font></td>";
					echo "<td ALIGN=CENTER><font size=2  face='arial'>".$pasado."</font></td>";
					echo "</table></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td>Positivos</td>';
					echo "<td align='center'>".$cmes['agrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['agrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Por mejorar</td>';
					echo "<td align='center'>".$cmes['desagrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['desagrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Positivos</td>';
					echo "<td>".promedio2 ($cmes['agrado2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td>".promedio2 ($cmes['agrado'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Por mejorar</td>';
					echo "<td>".promedio2 ($cmes['desagrado2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td>".promedio2 ($cmes['desagrado'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";

					echo "</table></br></br>";

					break;


					case '3':

					// busco el numero de comentarios en las diferentes respuestas

					// Ahora hago los query de cada  unidad
					// numero de comentarios para cada una de las respuestas dadas

					$telefonica=0;
					$carta=0;
					$email=0;
					$personal=0;
					$pendiente=0;
					$nrespuesta=0;
					$telefonica2=0;
					$carta2=0;
					$email2=0;
					$personal2=0;
					$pendiente2=0;
					$nrespuesta2=0;

					for($i=0;$i<$unidades;$i++)
					{
						$unidad['telefonicaTotal'][$i]=0;
						$unidad['cartaTotal'][$i]=0;
						$unidad['emailTotal'][$i]=0;
						$unidad['personalTotal'][$i]=0;
						$unidad['pendienteTotal'][$i]=0;
						$unidad['nrespuestaTotal'][$i]=0;
						$unidad['telefonicaTotal2'][$i]=0;
						$unidad['cartaTotal2'][$i]=0;
						$unidad['emailTotal2'][$i]=0;
						$unidad['personalTotal2'][$i]=0;
						$unidad['pendienteTotal2'][$i]=0;
						$unidad['nrespuestaTotal2'][$i]=0;

						for($j=1;$j<=$nmeses;$j++)
						{
							if ($i==0)
							{
								$cmes['telefonica'][$j]=0;
								$cmes['carta'][$j]=0;
								$cmes['email'][$j]=0;
								$cmes['personal'][$j]=0;
								$cmes['pendiente'][$j]=0;
								$cmes['nrespuesta'][$j]=0;
								$cmes['telefonica2'][$j]=0;
								$cmes['carta2'][$j]=0;
								$cmes['email2'][$j]=0;
								$cmes['personal2'][$j]=0;
								$cmes['pendiente2'][$j]=0;
								$cmes['nrespuesta2'][$j]=0;
							}

							if ($j<10)
							{
								$date1=$ano."-0".$j."-01";
								$date2=$ano."-0".$j."-31";
								$date3=$ano2."-0".$j."-01";
								$date4=$ano2."-0".$j."-31";
							}else
							{
								$date1=$ano."-".$j."-01";
								$date2=$ano."-".$j."-31";
								$date3=$ano2."-".$j."-01";
								$date4=$ano2."-".$j."-31";
							}

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Escrito'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'carta'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['carta'][$j]=$cmes['carta'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Telefonico'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'telefonica'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['telefonica'][$j]=$cmes['telefonica'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Mail'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'email'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['email'][$j]=$cmes['email'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Personal'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'personal'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['personal'][$j]=$cmes['personal'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres=''  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'pendiente'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['pendiente'][$j]=$cmes['pendiente'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='No Respuesta'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nrespuesta'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nrespuesta'][$j]=$cmes['nrespuesta'][$j]+$num1; //numero de comentarios de agrado del mes


							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Escrito'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'carta2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['carta2'][$j]=$cmes['carta2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Telefonico'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'telefonica2'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['telefonica2'][$j]=$cmes['telefonica2'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Mail'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'email2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['email2'][$j]=$cmes['email2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Personal'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'personal2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['personal2'][$j]=$cmes['personal2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres=''  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'pendiente2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['pendiente2'][$j]=$cmes['pendiente2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='No respuesta'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nrespuesta2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nrespuesta2'][$j]=$cmes['nrespuesta2'][$j]+$num1; //numero de comentarios de agrado del mes

							//numero de comentarios de agrado y desagrado por unidad
							$unidad['cartaTotal'][$i]=$unidad['cartaTotal'][$i]+$unidad[$i.'carta'][$j];
							$unidad['telefonicaTotal'][$i]=$unidad['telefonicaTotal'][$i]+$unidad[$i.'telefonica'][$j];
							$unidad['emailTotal'][$i]=$unidad['emailTotal'][$i]+$unidad[$i.'email'][$j];
							$unidad['personalTotal'][$i]=$unidad['personalTotal'][$i]+$unidad[$i.'personal'][$j];
							$unidad['pendienteTotal'][$i]=$unidad['pendienteTotal'][$i]+$unidad[$i.'pendiente'][$j];
							$unidad['cartaTotal2'][$i]=$unidad['cartaTotal2'][$i]+$unidad[$i.'carta2'][$j];
							$unidad['telefonicaTotal2'][$i]=$unidad['telefonicaTotal2'][$i]+$unidad[$i.'telefonica2'][$j];
							$unidad['emailTotal2'][$i]=$unidad['emailTotal2'][$i]+$unidad[$i.'email2'][$j];
							$unidad['personalTotal2'][$i]=$unidad['personalTotal2'][$i]+$unidad[$i.'personal2'][$j];
							$unidad['pendienteTotal2'][$i]=$unidad['pendienteTotal2'][$i]+$unidad[$i.'pendiente2'][$j];
							$unidad['nrespuestaTotal2'][$i]=$unidad['nrespuestaTotal2'][$i]+$unidad[$i.'nrespuesta2'][$j];
							$unidad['nrespuestaTotal'][$i]=$unidad['nrespuestaTotal'][$i]+$unidad[$i.'nrespuesta'][$j];
						}

						//numero de comentarios por tipo de respuesta
						$carta=	$carta+$unidad['cartaTotal'][$i];
						$email=$email+$unidad['emailTotal'][$i];
						$personal=$personal+$unidad['personalTotal'][$i];
						$telefonica=	$telefonica+$unidad['telefonicaTotal'][$i];
						$pendiente=	$pendiente+$unidad['pendienteTotal'][$i];
						$carta2=	$carta2+$unidad['cartaTotal2'][$i];
						$email2=$email2+$unidad['emailTotal2'][$i];
						$personal2=$personal2+$unidad['personalTotal2'][$i];
						$telefonica2=	$telefonica2+$unidad['telefonicaTotal2'][$i];
						$pendiente2=	$pendiente2+$unidad['pendienteTotal2'][$i];
						$nrespuesta2=	$nrespuesta2+$unidad['nrespuestaTotal2'][$i];
						$nrespuesta=	$nrespuesta+$unidad['nrespuestaTotal'][$i];
					}

					/********************************************************************/
					//pinto tercer indicador
					echo "<font size=2  color='#00008B' face='arial'><b>3. Como se da respuesta a los usuarios</font></b></br></br>";
					echo "<font size=2  color='#00008B' face='arial'><p align=center><b>Por comentario</font></p></b></br></br>";

					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='8' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='8' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";
					echo "<td align = center  colspan='1'><font size=2  face='arial'>&nbsp;</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>Telefonica</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2 face='arial'>Escrita</font></td>";
					echo "<td align = center    bgcolor='#ff00cc'> <font size=2  face='arial'>Email</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>Personal</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Subtotal</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Pendiente</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >No respuesta</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2 face='arial' >Total</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Telefonica</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Escrita</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Email</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>Personal</font></td>";
					echo "<td align = center  bgcolor='#0000ff' > <font size=2  face='arial' color='#ffffff'>Subtotal</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Pendiente</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>No respuesta</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Total</font></td>";
					echo "<td align = center   bgcolor='#ff9933'><font size=2  face='arial'>Variacion</font></td>";
					echo "</tr>";


					$subfinal=0;
					$subfinal2=0;

					for($i=1;$i<=$nmeses;$i++)
					{
						$subtotal=$cmes['telefonica'][$i]+$cmes['carta'][$i]+$cmes['email'][$i]+$cmes['personal'][$i];
						$subtotal2=$cmes['telefonica2'][$i]+$cmes['carta2'][$i]+$cmes['email2'][$i]+$cmes['personal2'][$i];
						$subfinal=$subfinal+$subtotal;
						$subfinal2=$subfinal2+$subtotal2;

						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['telefonica2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['carta2'][$i]."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['email2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['personal2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$subtotal2."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['pendiente2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nrespuesta2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".($subtotal2+$cmes['pendiente2'][$i]+$cmes['nrespuesta2'][$i])/*+$cmes['total2'][$i]*/."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['telefonica'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['carta'][$i]."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['email'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['personal'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$subtotal."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['pendiente'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nrespuesta'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".($subtotal+$cmes['pendiente'][$i]+$cmes['nrespuesta'][$i])."</font></td>";

						echo "<td align = center ><font size=2  face='arial'>".promedio ($subtotal, $subtotal2)."%</font></td>";


						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$telefonica2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".$carta2 ."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$email2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". $personal2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". $subfinal2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$pendiente2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$nrespuesta2."</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2 face='arial'>".($subfinal2+$pendiente2+$nrespuesta2)/*$comentarios2*/."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".$telefonica."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".$carta."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$email."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2 face='arial'>".$personal."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>". $subfinal."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$pendiente."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$nrespuesta."</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".($subfinal+$pendiente+$nrespuesta)."</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".promedio ($subtotal, $subtotal2)."%</font></td>";

					echo "</tr>";
					echo "</table></br>";
					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Telefonica</td>';
					echo "<td align='center'>".$cmes['telefonica2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['telefonica'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Escrita</td>';
					echo "<td align='center'>".$cmes['carta2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['carta'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Email</td>';
					echo "<td align=center >".$cmes['email2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['email'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Personal</td>';
					echo "<td align=center >".$cmes['personal2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['personal'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Pendiente</td>';
					echo "<td align=center >".$cmes['pendiente2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['pendiente'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >No respuesta</td>';
					echo "<td align=center >".$cmes['nrespuesta2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['nrespuesta'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Telefonica</td>';
					echo "<td align='center'>".promedio2 ($cmes['telefonica2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['telefonica'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Escrita</td>';
					echo "<td align='center'>".promedio2 ($cmes['carta2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['carta'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Email</td>';
					echo "<td align=center >".promedio2 ($cmes['email2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['email'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Personal</td>';
					echo "<td align=center >".promedio2 ($cmes['personal2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['personal'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Pendiente</td>';
					echo "<td align=center >".promedio2 ($cmes['pendiente2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['pendiente'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >No respuesta</td>';
					echo "<td align=center >".promedio2 ($cmes['nrespuesta2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['nrespuesta'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";


					echo "</table></br></br>";

					//COMO SE LE DA RESPUESTA A LOS USUARIOS POR FORMULARIO
					$telefonica=0;
					$carta=0;
					$email=0;
					$personal=0;
					$pendiente=0;
					$nrespuesta=0;
					$telefonica2=0;
					$carta2=0;
					$email2=0;
					$personal2=0;
					$pendiente2=0;
					$nrespuesta2=0;

					for($i=0;$i<1;$i++)
					{
						$unidad['telefonicaTotal'][$i]=0;
						$unidad['cartaTotal'][$i]=0;
						$unidad['emailTotal'][$i]=0;
						$unidad['personalTotal'][$i]=0;
						$unidad['pendienteTotal'][$i]=0;
						$unidad['nrespuestaTotal'][$i]=0;
						$unidad['telefonicaTotal2'][$i]=0;
						$unidad['cartaTotal2'][$i]=0;
						$unidad['emailTotal2'][$i]=0;
						$unidad['personalTotal2'][$i]=0;
						$unidad['pendienteTotal2'][$i]=0;
						$unidad['nrespuestaTotal2'][$i]=0;

						if($area == 'Todas')
							$areaelegida = "%";
						else{
							$areaelegida = $area;
						}

						for($j=1;$j<=$nmeses;$j++)
						{
							if ($i==0)
							{
								$cmes['telefonica'][$j]=0;
								$cmes['carta'][$j]=0;
								$cmes['email'][$j]=0;
								$cmes['personal'][$j]=0;
								$cmes['pendiente'][$j]=0;
								$cmes['nrespuesta'][$j]=0;
								$cmes['telefonica2'][$j]=0;
								$cmes['carta2'][$j]=0;
								$cmes['email2'][$j]=0;
								$cmes['personal2'][$j]=0;
								$cmes['pendiente2'][$j]=0;
								$cmes['nrespuesta2'][$j]=0;
							}

							if ($j<10)
							{
								$date1=$ano."-0".$j."-01";
								$date2=$ano."-0".$j."-31";
								$date3=$ano2."-0".$j."-01";
								$date4=$ano2."-0".$j."-31";
							}else
							{
								$date1=$ano."-".$j."-01";
								$date2=$ano."-".$j."-31";
								$date3=$ano2."-".$j."-01";
								$date4=$ano2."-".$j."-31";
							}

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Escrito'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date1' AND '$date2'
									 AND a.ccotres='Escrito' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date1' AND '$date2'
//									 AND a.Ccotres = 'Escrito'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'carta'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['carta'][$j]=$cmes['carta'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Telefonico'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date1' AND '$date2'
									 AND a.ccotres='telefonico' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date1' AND '$date2'
//									 AND a.Ccotres = 'telefonico'
//									 AND b.id_area LIKE '{$areaelegida}'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'telefonica'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['telefonica'][$j]=$cmes['telefonica'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Mail'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date1' AND '$date2'
									 AND a.ccotres='mail' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date1' AND '$date2'
//									 AND a.Ccotres = 'mail'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'email'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['email'][$j]=$cmes['email'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='Personal'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date1' AND '$date2'
									 AND a.ccotres='personal' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date1' AND '$date2'
//									 AND a.Ccotres = 'personal'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'personal'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['personal'][$j]=$cmes['personal'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres=''  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date1' AND '$date2'
									 AND a.ccotres='' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date1' AND '$date2'
//									 AND a.Ccotres = ''
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'pendiente'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['pendiente'][$j]=$cmes['pendiente'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccotres='No Respuesta'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date1' AND '$date2'
									 AND a.ccotres='No Respuesta' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date1' AND '$date2'
//									 AND a.Ccotres = 'No Respuesta'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nrespuesta'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nrespuesta'][$j]=$cmes['nrespuesta'][$j]+$num1; //numero de comentarios de agrado del mes


							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Escrito'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date3' AND '$date4'
									 AND a.ccotres='Escrito' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date3' AND '$date4'
//									 AND a.Ccotres = 'Escrito'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'carta2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['carta2'][$j]=$cmes['carta2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Telefonico'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date3' AND '$date4'
									 AND a.ccotres='telefonico' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date3' AND '$date4'
//									 AND a.Ccotres = 'telefonico'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'telefonica2'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['telefonica2'][$j]=$cmes['telefonica2'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Mail'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date3' AND '$date4'
									 AND a.ccotres='mail' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date3' AND '$date4'
//									 AND a.Ccotres = 'mail'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'email2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['email2'][$j]=$cmes['email2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='Personal'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date3' AND '$date4'
									 AND a.ccotres='personal' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date3' AND '$date4'
//									 AND a.Ccotres = 'personal'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'personal2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['personal2'][$j]=$cmes['personal2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres=''  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date3' AND '$date4'
									 AND a.ccotres='' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date3' AND '$date4'
//									 AND a.Ccotres = ''
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'pendiente2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['pendiente2'][$j]=$cmes['pendiente2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccotres='No respuesta'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$query = "SELECT a.id FROM {$empresa}_000017 as a
									 WHERE a.Ccofori  BETWEEN '$date3' AND '$date4'
									 AND a.ccotres='No respuesta' AND ccoori like '$areaelegida'";
//							$query = "SELECT a.id
//									 FROM {$empresa}_000017 AS a,
//									 (SELECT id_comentario, id_area
//									 FROM {$empresa}_000018
//									 GROUP BY 1 , 2
//									 ) AS b
//									 WHERE a.id = b.id_comentario
//									 AND a.Ccofori BETWEEN '$date3' AND '$date4'
//									 AND a.Ccotres = 'No respuesta'
//									 AND b.id_area LIKE '$areaelegida'
//									 GROUP BY 1";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nrespuesta2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nrespuesta2'][$j]=$cmes['nrespuesta2'][$j]+$num1; //numero de comentarios de agrado del mes

							//numero de comentarios de agrado y desagrado por unidad
							$unidad['cartaTotal'][$i]=$unidad['cartaTotal'][$i]+$unidad[$i.'carta'][$j];
							$unidad['telefonicaTotal'][$i]=$unidad['telefonicaTotal'][$i]+$unidad[$i.'telefonica'][$j];
							$unidad['emailTotal'][$i]=$unidad['emailTotal'][$i]+$unidad[$i.'email'][$j];
							$unidad['personalTotal'][$i]=$unidad['personalTotal'][$i]+$unidad[$i.'personal'][$j];
							$unidad['pendienteTotal'][$i]=$unidad['pendienteTotal'][$i]+$unidad[$i.'pendiente'][$j];
							$unidad['cartaTotal2'][$i]=$unidad['cartaTotal2'][$i]+$unidad[$i.'carta2'][$j];
							$unidad['telefonicaTotal2'][$i]=$unidad['telefonicaTotal2'][$i]+$unidad[$i.'telefonica2'][$j];
							$unidad['emailTotal2'][$i]=$unidad['emailTotal2'][$i]+$unidad[$i.'email2'][$j];
							$unidad['personalTotal2'][$i]=$unidad['personalTotal2'][$i]+$unidad[$i.'personal2'][$j];
							$unidad['pendienteTotal2'][$i]=$unidad['pendienteTotal2'][$i]+$unidad[$i.'pendiente2'][$j];
							$unidad['nrespuestaTotal2'][$i]=$unidad['nrespuestaTotal2'][$i]+$unidad[$i.'nrespuesta2'][$j];
							$unidad['nrespuestaTotal'][$i]=$unidad['nrespuestaTotal'][$i]+$unidad[$i.'nrespuesta'][$j];
						}

						//numero de comentarios por tipo de respuesta
						$carta=	$carta+$unidad['cartaTotal'][$i];
						$email=$email+$unidad['emailTotal'][$i];
						$personal=$personal+$unidad['personalTotal'][$i];
						$telefonica=	$telefonica+$unidad['telefonicaTotal'][$i];
						$pendiente=	$pendiente+$unidad['pendienteTotal'][$i];
						$carta2=	$carta2+$unidad['cartaTotal2'][$i];
						$email2=$email2+$unidad['emailTotal2'][$i];
						$personal2=$personal2+$unidad['personalTotal2'][$i];
						$telefonica2=	$telefonica2+$unidad['telefonicaTotal2'][$i];
						$pendiente2=	$pendiente2+$unidad['pendienteTotal2'][$i];
						$nrespuesta2=	$nrespuesta2+$unidad['nrespuestaTotal2'][$i];
						$nrespuesta=	$nrespuesta+$unidad['nrespuestaTotal'][$i];
					}

					/********************************************************************/
					//pinto tercer indicador
					if($areaelegida != "%")
						$areaprint = $areaelegida;
					else
						$areaprint = "Todas";
					echo "<font size=2  color='#00008B' face='arial'><p align=center><b>Por formato<br>Area: $areaprint</p></font></b></br></br>";


					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='8' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='8' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";
					echo "<td align = center  colspan='1'><font size=2  face='arial'>&nbsp;</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>Telefonica</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2 face='arial'>Escrita</font></td>";
					echo "<td align = center    bgcolor='#ff00cc'> <font size=2  face='arial'>Email</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>Personal</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Subtotal</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Pendiente</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >No respuesta</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2 face='arial' >Total</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Telefonica</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Escrita</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Email</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>Personal</font></td>";
					echo "<td align = center  bgcolor='#0000ff' > <font size=2  face='arial' color='#ffffff'>Subtotal</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Pendiente</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>No respuesta</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Total</font></td>";
					echo "<td align = center   bgcolor='#ff9933'><font size=2  face='arial'>Variacion</font></td>";
					echo "</tr>";


					$subfinal=0;
					$subfinal2=0;

					for($i=1;$i<=$nmeses;$i++)
					{
						$subtotal=$cmes['telefonica'][$i]+$cmes['carta'][$i]+$cmes['email'][$i]+$cmes['personal'][$i];
						$subtotal2=$cmes['telefonica2'][$i]+$cmes['carta2'][$i]+$cmes['email2'][$i]+$cmes['personal2'][$i];
						$subfinal=$subfinal+$subtotal;
						$subfinal2=$subfinal2+$subtotal2;

						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['telefonica2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['carta2'][$i]."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['email2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['personal2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$subtotal2."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['pendiente2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nrespuesta2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".($subtotal2+$cmes['pendiente2'][$i]+$cmes['nrespuesta2'][$i])/*+$cmes['total2'][$i]*/."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['telefonica'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['carta'][$i]."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['email'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['personal'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$subtotal."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['pendiente'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nrespuesta'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".($subtotal+$cmes['pendiente'][$i]+$cmes['nrespuesta'][$i])."</font></td>";

						echo "<td align = center ><font size=2  face='arial'>".promedio ($subtotal, $subtotal2)."%</font></td>";


						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$telefonica2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".$carta2 ."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$email2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". $personal2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". $subfinal2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$pendiente2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$nrespuesta2."</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2 face='arial'>".($subfinal2+$pendiente2+$nrespuesta2)/*$comentarios2*/."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".$telefonica."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".$carta."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$email."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2 face='arial'>".$personal."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>". $subfinal."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$pendiente."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$nrespuesta."</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".($subfinal+$pendiente+$nrespuesta)."</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".promedio ($subtotal, $subtotal2)."%</font></td>";

					echo "</tr>";
					echo "</table></br>";
					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Telefonica</td>';
					echo "<td align='center'>".$cmes['telefonica2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['telefonica'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Escrita</td>';
					echo "<td align='center'>".$cmes['carta2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['carta'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Email</td>';
					echo "<td align=center >".$cmes['email2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['email'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Personal</td>';
					echo "<td align=center >".$cmes['personal2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['personal'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Pendiente</td>';
					echo "<td align=center >".$cmes['pendiente2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['pendiente'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >No respuesta</td>';
					echo "<td align=center >".$cmes['nrespuesta2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['nrespuesta'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Telefonica</td>';
					echo "<td align='center'>".promedio2 ($cmes['telefonica2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['telefonica'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Escrita</td>';
					echo "<td align='center'>".promedio2 ($cmes['carta2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['carta'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Email</td>';
					echo "<td align=center >".promedio2 ($cmes['email2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['email'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Personal</td>';
					echo "<td align=center >".promedio2 ($cmes['personal2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['personal'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Pendiente</td>';
					echo "<td align=center >".promedio2 ($cmes['pendiente2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['pendiente'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >No respuesta</td>';
					echo "<td align=center >".promedio2 ($cmes['nrespuesta2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align=center >".promedio2 ($cmes['nrespuesta'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";


					echo "</table></br></br>";


					break;

					case '4':

					/************************************************************************************/
					// Busqueda de cuarto indicador,

					// Ahora hago los query de cada  unidad
					// numero de comentarios para cada una de las respuestas dadas

					$act=0;
					$apt=0;
					$ppt=0;
					$ppa=0;
					$sincom=0;
					$pend=0;
					$act2=0;
					$apt2=0;
					$ppt2=0;
					$ppa2=0;
					$sincom2=0;
					$pend2=0;

					for($i=0;$i<$unidades;$i++)
					{
						$unidad['actTotal'][$i]=0;
						$unidad['aptTotal'][$i]=0;
						$unidad['pptTotal'][$i]=0;
						$unidad['ppaTotal'][$i]=0;
						$unidad['sincomTotal'][$i]=0;
						$unidad['pendTotal'][$i]=0;
						$unidad['actTotal2'][$i]=0;
						$unidad['aptTotal2'][$i]=0;
						$unidad['pptTotal2'][$i]=0;
						$unidad['ppaTotal2'][$i]=0;
						$unidad['sincomTotal2'][$i]=0;
						$unidad['pendTotal2'][$i]=0;

						for($j=1;$j<=$nmeses;$j++)
						{
							if ($i==0)
							{
								$cmes['act'][$j]=0;
								$cmes['apt'][$j]=0;
								$cmes['ppt'][$j]=0;
								$cmes['ppa'][$j]=0;
								$cmes['sincom'][$j]=0;
								$cmes['pend'][$j]=0;
								$cmes['act2'][$j]=0;
								$cmes['apt2'][$j]=0;
								$cmes['ppt2'][$j]=0;
								$cmes['ppa2'][$j]=0;
								$cmes['sincom2'][$j]=0;
								$cmes['pend2'][$j]=0;
							}

							if ($j<10)
							{
								$date1=$ano."-0".$j."-01";
								$date2=$ano."-0".$j."-31";
								$date3=$ano2."-0".$j."-01";
								$date4=$ano2."-0".$j."-31";
							}else
							{
								$date1=$ano."-".$j."-01";
								$date2=$ano."-".$j."-31";
								$date3=$ano2."-".$j."-01";
								$date4=$ano2."-".$j."-31";
							}

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.cmotip='Desagrado' and B.cmocla='1-Actitudinal-Cualitativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'act'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['act'][$j]=$cmes['act'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.cmotip='Desagrado' and B.cmocla='2-Aptitudinal-Cualitativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'apt'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['apt'][$j]=$cmes['apt'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.cmotip='Desagrado' and B.cmocla='3-Por proceso tecnico-Operativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'ppt'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['ppt'][$j]=$cmes['ppt'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.cmotip='Desagrado' and B.cmocla='4-Por proceso de atencion-Operativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'ppa'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['ppa'][$j]=$cmes['ppa'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'   and B.cmotip='Desagrado' and B.cmover='NO' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'sincom'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['sincom'][$j]=$cmes['sincom'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and  B.cmotip='Desagrado' and B.cmover='' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'pend'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['pend'][$j]=$cmes['pend'][$j]+$num1; //numero de comentarios de agrado del mes


							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.cmotip='Desagrado' and B.cmocla='1-Actitudinal-Cualitativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'act2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['act2'][$j]=$cmes['act2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.cmotip='Desagrado' and B.cmocla='2-Aptitudinal-Cualitativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'apt2'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['apt2'][$j]=$cmes['apt2'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.cmotip='Desagrado' and B.cmocla='3-Por proceso tecnico-Operativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'ppt2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['ppt2'][$j]=$cmes['ppt2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.cmotip='Desagrado' and B.cmocla='4-Por proceso de atencion-Operativo' and B.cmover='SI' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'ppa2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['ppa2'][$j]=$cmes['ppa2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.cmotip='Desagrado' and B.cmover='NO' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'sincom2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['sincom2'][$j]=$cmes['sincom2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."' and  B.cmotip='Desagrado' and B.cmover='' and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'pend2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['pend2'][$j]=$cmes['pend2'][$j]+$num1; //numero de comentarios de agrado del mes

							//numero de comentarios de agrado y desagrado por unidad
							$unidad['actTotal'][$i]=$unidad['actTotal'][$i]+$unidad[$i.'act'][$j];
							$unidad['aptTotal'][$i]=$unidad['aptTotal'][$i]+$unidad[$i.'apt'][$j];
							$unidad['pptTotal'][$i]=$unidad['pptTotal'][$i]+$unidad[$i.'ppt'][$j];
							$unidad['ppaTotal'][$i]=$unidad['ppaTotal'][$i]+$unidad[$i.'ppa'][$j];
							$unidad['sincomTotal'][$i]=$unidad['sincomTotal'][$i]+$unidad[$i.'sincom'][$j];
							$unidad['pendTotal'][$i]=$unidad['pendTotal'][$i]+$unidad[$i.'pend'][$j];
							$unidad['actTotal2'][$i]=$unidad['actTotal2'][$i]+$unidad[$i.'act2'][$j];
							$unidad['aptTotal2'][$i]=$unidad['aptTotal2'][$i]+$unidad[$i.'apt2'][$j];
							$unidad['pptTotal2'][$i]=$unidad['pptTotal2'][$i]+$unidad[$i.'ppt2'][$j];
							$unidad['ppaTotal2'][$i]=$unidad['ppaTotal2'][$i]+$unidad[$i.'ppa2'][$j];
							$unidad['sincomTotal2'][$i]=$unidad['sincomTotal2'][$i]+$unidad[$i.'sincom2'][$j];
							$unidad['pendTotal2'][$i]=$unidad['pendTotal2'][$i]+$unidad[$i.'pend2'][$j];
						}

						//numero de comentarios por tipo de respuesta
						$act=	$act+$unidad['actTotal'][$i];
						$apt=$apt+$unidad['aptTotal'][$i];
						$ppt=$ppt+$unidad['pptTotal'][$i];
						$ppa=	$ppa+$unidad['ppaTotal'][$i];
						$sincom=	$sincom+$unidad['sincomTotal'][$i];
						$pend=	$pend+$unidad['pendTotal'][$i];
						$act2=$act2+$unidad['actTotal2'][$i];
						$apt2=$apt2+$unidad['aptTotal2'][$i];
						$ppt2=	$ppt2+$unidad['pptTotal2'][$i];
						$ppa2=	$ppa2+$unidad['ppaTotal2'][$i];
						$sincom2=	$sincom2+$unidad['sincomTotal2'][$i];
						$pend2=	$pend2+$unidad['pendTotal2'][$i];
					}


					/*****************************************************************************************/
					//grafico cuarto indicador

					echo "<font size=2  color='#00008B' face='arial'><b>4. Clasificacion por tipo de comentarios aprobados</font></b></br></br>";


					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='12' bgcolor='#ff00cc'><font size=2  face='arial'>".$desagrado2." comentarios por mejorar en el ano: ".$ano2."</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>Act.</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2 face='arial'>%</font></td>";
					echo "<td align = center    bgcolor='#ff00cc'> <font size=2  face='arial'>Apt.</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >PPT.</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >PPA</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2 face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2 face='arial' >Sin comprobar</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>PEND</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Total Comproba</font></td>";
					echo "</tr>";



					$subfinal2=0;

					for($i=1;$i<=$nmeses;$i++)
					{

						$subtotal2=$cmes['act2'][$i]+$cmes['apt2'][$i]+$cmes['ppt2'][$i]+$cmes['ppa2'][$i];

						$subfinal2=$subfinal2+$subtotal2;

						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['act2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['act2'][$i], $act2)."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['apt2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['apt2'][$i], $apt2)."%</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['ppt2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['ppt2'][$i], $ppt2)."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['ppa2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['ppa2'][$i], $ppa2)."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['sincom2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['sincom2'][$i], $sincom2)."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['pend2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$subtotal2."</font></td>";



						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$act2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($act2, $act2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$apt2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". promedio2 ($apt2, $apt2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". $ppt."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".promedio2 ($ppt2, $ppt2)."%</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$ppa2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($ppa2, $ppa2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$sincom2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($sincom2, $sincom2)."%</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$pend."</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2 face='arial'>".$subfinal2."</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>porcentaje</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($act2, $subfinal2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'></font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>". promedio2 ($apt2, $subfinal2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".promedio2 ($ppt2, $subfinal2)."%</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' ></font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($ppa2, $subfinal2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'></font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2 face='arial'>".promedio2 ($subfinal2, $subfinal2)."%</font></td>";
					echo "</tr>";
					echo "</table></br>";

					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='13' bgcolor='#ff00cc'><font size=2  face='arial'>".$desagrado." comentarios por mejorar en el ano: ".$ano."</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>Act.</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2 face='arial'>%</font></td>";
					echo "<td align = center    bgcolor='#ff00cc'> <font size=2  face='arial'>Apt.</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >PPT.</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >PPA</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2 face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2 face='arial' >Sin comprobar</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>PEND</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Total Comproba</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial' >Total</font></td>";
					echo "</tr>";


					$subfinal=0;


					for($i=1;$i<=$nmeses;$i++)
					{
						$subtotal=$cmes['act'][$i]+$cmes['apt'][$i]+$cmes['ppt'][$i]+$cmes['ppa'][$i];
						$subfinal=$subfinal+$subtotal;

						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['act'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['act'][$i], $act)."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['apt'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['apt'][$i], $apt)."%</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$cmes['ppt'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['ppt'][$i], $ppt)."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['ppa'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['ppa'][$i], $ppa)."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['sincom'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['sincom'][$i], $sincom)."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['pend'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".$subtotal."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".($subtotal+$cmes['pend'][$i]+$cmes['sincom'][$i])."</font></td>";



						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$act."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2 face='arial'>".promedio2 ($act, $act) ."%</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".$apt."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>". promedio2 ($apt, $apt)."%</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>". $ppt."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial' >".promedio2 ($ppt, $ppt)."%</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial' >".$ppa."</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2 face='arial'>".promedio2 ($ppa, $ppa)."%</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".$sincom."</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>".promedio2 ($sincom, $sincom)."%</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'>".$pend."</font></td>";
					echo "<td align = center bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$subfinal."</font></td>";
					echo "<td align = center bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".($subfinal+$pend+$sincom)."</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>porcentaje</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2 face='arial'>".promedio2 ($act, $subfinal) ."%</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'></font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'>". promedio2 ($apt, $subfinal)."%</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial' >".promedio2 ($ppt, $subfinal)."%</font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial' ></font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2 face='arial'>".promedio2 ($ppa, $subfinal)."%</font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'></font></td>";
					echo "<td align = center  bgcolor='#6699ff'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#6699ff'><font size=2  face='arial'></font></td>";
					echo "<td align = center bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".promedio2 ($subfinal, $subfinal)."%</font></td>";
					echo "<td align = center bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'></font></td>";
					echo "</tr>";
					echo "</table></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Actitudinal</td>';
					echo "<td align='center'>".$cmes['act2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['act'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Aptitudinal</td>';
					echo "<td align='center'>".$cmes['apt2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['apt'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Por proceso técnico</td>';
					echo "<td align=center >".$cmes['ppt2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['ppt'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Por proceso de atencion </td>';
					echo "<td align=center >".$cmes['ppa2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['ppa'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Sin comprobar</td>';
					echo "<td align=center >".$cmes['sincom2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['sincom'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Pendientes </td>';
					echo "<td align=center >".$cmes['pend2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['pend'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Comprobado</td>';
					echo "<td align='center'>".$subtotal2 ."</td>";
					echo "<td align='center'>".$subtotal."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Total</td>';
					echo "<td align='center'>".$cmes['desagrado2'][$nmeses]."</td>";
					echo "<td align='center'>".($subtotal+$cmes['pend'][$nmeses]+$cmes['sincom'][$nmeses])."</td>";
					echo "</tr>";

					$comprobados2=$cmes['sincom2'][$nmeses];
					$comprobados=$cmes['sincom'][$nmeses];

					echo "</table></br></br>";

					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Por mejorar</td>';
					echo "<td align='center'>".$cmes['desagrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['desagrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Comprobados</td>';
					echo "<td align='center'>".$subtotal2."</td>";
					echo "<td align='center'>".$subtotal."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Sin comprobar</td>';
					echo "<td align=center >".$cmes['sincom2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['sincom'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >Pendientes </td>';
					echo "<td align=center >".$cmes['pend2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['pend'][$nmeses]."</td>";
					echo "</tr>";
					echo "</table></br></br>";

					break;

					case '5':

					/************************************************************************************/
					//BUSQUEDA DE QUINTO INDICADOR

					// Ahora hago los query de cada  unidad
					// numero de comentarios para cada una de las respuestas dadas

					$si=0;
					$no=0;
					$cambia=0;
					$nores=0;
					$si2=0;
					$no2=0;
					$cambia2=0;
					$nores2=0;

					for($i=0;$i<$unidades;$i++)
					{
						$unidad['siTotal'][$i]=0;
						$unidad['noTotal'][$i]=0;
						$unidad['cambiaTotal'][$i]=0;
						$unidad['noresTotal'][$i]=0;
						$unidad['siTotal2'][$i]=0;
						$unidad['noTotal2'][$i]=0;
						$unidad['cambiaTotal2'][$i]=0;
						$unidad['noresTotal2'][$i]=0;

						for($j=1;$j<=$nmeses;$j++)
						{
							if ($i==0)
							{
								$cmes['si'][$j]=0;
								$cmes['no'][$j]=0;
								$cmes['cambia'][$j]=0;
								$cmes['nores'][$j]=0;
								$cmes['si2'][$j]=0;
								$cmes['no2'][$j]=0;
								$cmes['cambia2'][$j]=0;
								$cmes['nores2'][$j]=0;

							}

							if ($j<10)
							{
								$date1=$ano."-0".$j."-01";
								$date2=$ano."-0".$j."-31";
								$date3=$ano2."-0".$j."-01";
								$date4=$ano2."-0".$j."-31";
							}else
							{
								$date1=$ano."-".$j."-01";
								$date2=$ano."-".$j."-31";
								$date3=$ano2."-".$j."-01";
								$date4=$ano2."-".$j."-31";
							}

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='SI' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'si'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['si'][$j]=$cmes['si'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='NO' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'no'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['no'][$j]=$cmes['no'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='camb' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'cambia'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['cambia'][$j]=$cmes['cambia'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='NO RESPONDE' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nores'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nores'][$j]=$cmes['nores'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='SI' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'si2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['si2'][$j]=$cmes['si2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='NO' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'no2'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['no2'][$j]=$cmes['no2'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='camb' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'cambia2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['cambia2'][$j]=$cmes['cambia2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='NO RESPONDE' and  B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nores2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nores2'][$j]=$cmes['nores2'][$j]+$num1; //numero de comentarios de agrado del mes



							//numero de comentarios de agrado y desagrado por unidad
							$unidad['siTotal'][$i]=$unidad['siTotal'][$i]+$unidad[$i.'si'][$j];
							$unidad['noTotal'][$i]=$unidad['noTotal'][$i]+$unidad[$i.'no'][$j];
							$unidad['cambiaTotal'][$i]=$unidad['cambiaTotal'][$i]+$unidad[$i.'cambia'][$j];
							$unidad['noresTotal'][$i]=$unidad['noresTotal'][$i]+$unidad[$i.'nores'][$j];
							$unidad['siTotal2'][$i]=$unidad['siTotal2'][$i]+$unidad[$i.'si2'][$j];
							$unidad['noTotal2'][$i]=$unidad['noTotal2'][$i]+$unidad[$i.'no2'][$j];
							$unidad['cambiaTotal2'][$i]=$unidad['cambiaTotal2'][$i]+$unidad[$i.'cambia2'][$j];
							$unidad['noresTotal2'][$i]=$unidad['noresTotal2'][$i]+$unidad[$i.'nores2'][$j];
						}

						//numero de comentarios por tipo de respuesta
						$si=	$si+$unidad['siTotal'][$i];
						$no=$no+$unidad['noTotal'][$i];
						$cambia=$cambia+$unidad['cambiaTotal'][$i];
						$nores=	$nores+$unidad['noresTotal'][$i];
						$si2=$si2+$unidad['siTotal2'][$i];
						$no2=$no2+$unidad['noTotal2'][$i];
						$cambia2=	$cambia2+$unidad['cambiaTotal2'][$i];
						$nores2=	$nores2+$unidad['noresTotal2'][$i];

					}

					/*****************************************************************************************/
					//grafico quinto indicador

					echo "<font size=2  color='#00008B' face='arial'><b>5. El Usuario utilizaria de nuevo nuestros servicios</font></b></br></br>";


					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='9' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='9' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";

					echo "</tr>";
					echo "<tr>";
					echo "<td align = center rowspan=2><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2  face='arial'>SI</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2 face='arial'>NO</font></td>";
					echo "<td align = center colspan=2  bgcolor='#ff00cc'> <font size=2  face='arial'>CAMBIO OPINION</font></td>";
					echo "<td align = center colspan=2  bgcolor='#ff00cc'><font size=2  face='arial'>NO RESPONDE</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>TOTAL</font></td>";
					echo "<td align = center colspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>SI</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>NO</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>CAMBIO DE OPINION</font></td>";
					echo "<td align = center colspan=2 bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>NO RESPONDE</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>TOTAL</font></td>";

					echo "</tr>";

					echo "<tr>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";

					echo "</tr>";

					$subfinal=0;
					$subfinal2=0;

					for($i=1;$i<=$nmeses;$i++)
					{


						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['si2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['si2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['no2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['no2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['cambia2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['cambia2'][$i], $cmes['total2'][$i]) ."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nores2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['nores2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['total2'][$i]."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['si'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['si'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['no'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['no'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['cambia'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['cambia'][$i], $cmes['total'][$i]) ."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nores'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['nores'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial' >".($cmes['si'][$i]+$cmes['no'][$i]+$cmes['cambia'][$i]+$cmes['nores'][$i])."</font></td>";

						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$si2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($si2, $comentarios2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$no2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($no2, $comentarios2) ."%</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$cambia2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($cambia2, $comentarios2)."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$nores2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($nores2, $comentarios2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>".$comentarios2."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$si."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($si, $comentarios)."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$no."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($no, $comentarios)."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$cambia."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($cambia, $comentarios)."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$nores."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($nores, $comentarios)."%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".($si+$no+$cambia+$nores)."</font></td>";
					echo "</tr>";
					echo "</table></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >SI</td>';
					echo "<td align='center'>".$cmes['si2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['si'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO</td>';
					echo "<td align='center'>".$cmes['no2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['no'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >CAMBIO DE OPINION</td>';
					echo "<td align=center >".$cmes['cambia2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['cambia'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO RESPONDE </td>';
					echo "<td align=center >".$cmes['nores2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['nores'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >SI</td>';
					echo "<td align='center'>".promedio2 ($cmes['si2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['si'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO</td>';
					echo "<td align='center'>".promedio2 ($cmes['no2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['no'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >CAMBIO DE OPINION</td>';
					echo "<td align='center'>".promedio2 ($cmes['cambia2'][$nmeses],$cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['cambia'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO RESPONDE</td>';
					echo "<td align='center'>".promedio2 ($cmes['nores2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['nores'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";



					echo "</table></br></br>";

					/*******************
					 * Se muestra la misma tabla pero por formatos
					 */

					/************************************************************************************/
					//BUSQUEDA DE QUINTO INDICADOR

					// Ahora hago los query de cada  unidad
					// numero de comentarios para cada una de las respuestas dadas

					$si=0;
					$no=0;
					$cambia=0;
					$nores=0;
					$si2=0;
					$no2=0;
					$cambia2=0;
					$nores2=0;

					for($i=0;$i<1;$i++)
					{
						$unidad['siTotal'][$i]=0;
						$unidad['noTotal'][$i]=0;
						$unidad['cambiaTotal'][$i]=0;
						$unidad['noresTotal'][$i]=0;
						$unidad['siTotal2'][$i]=0;
						$unidad['noTotal2'][$i]=0;
						$unidad['cambiaTotal2'][$i]=0;
						$unidad['noresTotal2'][$i]=0;

						for($j=1;$j<=$nmeses;$j++)
						{
							if ($i==0)
							{
								$cmes['si'][$j]=0;
								$cmes['no'][$j]=0;
								$cmes['cambia'][$j]=0;
								$cmes['nores'][$j]=0;
								$cmes['si2'][$j]=0;
								$cmes['no2'][$j]=0;
								$cmes['cambia2'][$j]=0;
								$cmes['nores2'][$j]=0;

							}

							if ($j<10)
							{
								$date1=$ano."-0".$j."-01";
								$date2=$ano."-0".$j."-31";
								$date3=$ano2."-0".$j."-01";
								$date4=$ano2."-0".$j."-31";
							}else
							{
								$date1=$ano."-".$j."-01";
								$date2=$ano."-".$j."-31";
								$date3=$ano2."-".$j."-01";
								$date4=$ano2."-".$j."-31";
							}

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date1."' and '".$date2."'  and A.ccovol='SI'  ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'si'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['si'][$j]=$cmes['si'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='NO'";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'no'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['no'][$j]=$cmes['no'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='camb' ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'cambia'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['cambia'][$j]=$cmes['cambia'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date1."'  and '".$date2."'  and A.ccovol='NO RESPONDE' ";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nores'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nores'][$j]=$cmes['nores'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='SI'";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'si2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['si2'][$j]=$cmes['si2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='NO'";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'no2'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['no2'][$j]=$cmes['no2'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='camb'";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'cambia2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['cambia2'][$j]=$cmes['cambia2'][$j]+$num1; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori ";
							$query= $query. "FROM " .$empresa."_000017 A where A.Ccofori  between '".$date3."'  and '".$date4."'  and A.ccovol='NO RESPONDE'";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'nores2'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['nores2'][$j]=$cmes['nores2'][$j]+$num1; //numero de comentarios de agrado del mes



							//numero de comentarios de agrado y desagrado por unidad
							$unidad['siTotal'][$i]=$unidad['siTotal'][$i]+$unidad[$i.'si'][$j];
							$unidad['noTotal'][$i]=$unidad['noTotal'][$i]+$unidad[$i.'no'][$j];
							$unidad['cambiaTotal'][$i]=$unidad['cambiaTotal'][$i]+$unidad[$i.'cambia'][$j];
							$unidad['noresTotal'][$i]=$unidad['noresTotal'][$i]+$unidad[$i.'nores'][$j];
							$unidad['siTotal2'][$i]=$unidad['siTotal2'][$i]+$unidad[$i.'si2'][$j];
							$unidad['noTotal2'][$i]=$unidad['noTotal2'][$i]+$unidad[$i.'no2'][$j];
							$unidad['cambiaTotal2'][$i]=$unidad['cambiaTotal2'][$i]+$unidad[$i.'cambia2'][$j];
							$unidad['noresTotal2'][$i]=$unidad['noresTotal2'][$i]+$unidad[$i.'nores2'][$j];
						}

						//numero de comentarios por tipo de respuesta
						$si=	$si+$unidad['siTotal'][$i];
						$no=$no+$unidad['noTotal'][$i];
						$cambia=$cambia+$unidad['cambiaTotal'][$i];
						$nores=	$nores+$unidad['noresTotal'][$i];
						$si2=$si2+$unidad['siTotal2'][$i];
						$no2=$no2+$unidad['noTotal2'][$i];
						$cambia2=	$cambia2+$unidad['cambiaTotal2'][$i];
						$nores2=	$nores2+$unidad['noresTotal2'][$i];
					}

					$comentarios=$si+$no+$cambia+$nores;
					$comentarios2=$si2+$no2+$cambia2+$nores2;

					/*****************************************************************************************/
					//grafico quinto indicador
//					if($areaelegida != "%")
//						$areaprint = $areaelegida;
//					else
//						$areaprint = "Todas";
//					echo "<font size=2  color='#00008B' face='arial'><p align=center><b>Por formato<br>Area: $areaprint</p></font></b></br></br>";


					echo "<p align='CENTER'><font size=2  color='#00008B' face='arial'><b>Por Formatos</font></b></p><br>";
//					echo "<br><font size=2  color='#00008B' face='arial'><b>Area: Todas</font></b></p></br>";


					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='9' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='9' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";

					echo "</tr>";
					echo "<tr>";
					echo "<td align = center rowspan=2><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2  face='arial'>SI</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2 face='arial'>NO</font></td>";
					echo "<td align = center colspan=2  bgcolor='#ff00cc'> <font size=2  face='arial'>CAMBIO OPINION</font></td>";
					echo "<td align = center colspan=2  bgcolor='#ff00cc'><font size=2  face='arial'>NO RESPONDE</font></td>";
					echo "<td align = center   bgcolor='#ff00cc'><font size=2  face='arial'>TOTAL</font></td>";
					echo "<td align = center colspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>SI</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>NO</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>CAMBIO DE OPINION</font></td>";
					echo "<td align = center colspan=2 bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>NO RESPONDE</font></td>";
					echo "<td align = center   bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>TOTAL</font></td>";

					echo "</tr>";

					echo "<tr>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";

					echo "</tr>";

					$subfinal=0;
					$subfinal2=0;

					for($i=1;$i<=$nmeses;$i++)
					{


						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['si2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['si2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['no2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['no2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['cambia2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['cambia2'][$i], $cmes['total2'][$i]) ."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nores2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['nores2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".($cmes['si2'][$i]+$cmes['no2'][$i]+$cmes['cambia2'][$i]+$cmes['nores2'][$i])."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['si'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['si'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['no'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['no'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['cambia'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['cambia'][$i], $cmes['total'][$i]) ."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['nores'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['nores'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial' >".($cmes['si'][$i]+$cmes['no'][$i]+$cmes['cambia'][$i]+$cmes['nores'][$i])."</font></td>";

						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$si2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($si2, $comentarios2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$no2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($no2, $comentarios2) ."%</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial' >".$cambia2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($cambia2, $comentarios2)."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$nores2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($nores2, $comentarios2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>".$comentarios2."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$si."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($si, $comentarios)."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$no."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($no, $comentarios)."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$cambia."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($cambia, $comentarios)."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$nores."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($nores, $comentarios)."%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".($si+$no+$cambia+$nores)."</font></td>";
					echo "</tr>";
					echo "</table></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >SI</td>';
					echo "<td align='center'>".$cmes['si2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['si'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO</td>';
					echo "<td align='center'>".$cmes['no2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['no'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >CAMBIO DE OPINION</td>';
					echo "<td align=center >".$cmes['cambia2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['cambia'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO RESPONDE </td>';
					echo "<td align=center >".$cmes['nores2'][$nmeses]."</td>";
					echo "<td align=center >".$cmes['nores'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >SI</td>';
					echo "<td align='center'>".promedio2 ($cmes['si2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['si'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO</td>';
					echo "<td align='center'>".promedio2 ($cmes['no2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['no'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >CAMBIO DE OPINION</td>';
					echo "<td align='center'>".promedio2 ($cmes['cambia2'][$nmeses],$cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['cambia'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td align=center >NO RESPONDE</td>';
					echo "<td align='center'>".promedio2 ($cmes['nores2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td align='center'>".promedio2 ($cmes['nores'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";



					echo "</table></br></br>";

					break;

					case '6':

					/************************************************************************************/
					//BUSQUEDA DE SEXTO INDICADOR

					// Busco primero las causas y las organizo en orden descendente


					$Fecha2=$ano."-01-01";
					$Fecha3=$ano."-".$nmeses."-31";

					$query="Select count(B.id), B.cmocau from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$Fecha2."'  and '".$Fecha3."' and B.id_comentario=A.id and B.cmotip='Desagrado' group by B.cmocau order by 1 desc";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

					echo "<font size=2  color='#00008B' face='arial'><b>6 Causas mas relevantes de los comentarios por mejorar y positivos</font></b></br></br>";
					if ($num>0)
					{
						echo "<center><font size=2  color='#00008B' face='arial'><b>6.1 Causas mas relevantes de los comentarios por mejorar</font></b></center></br>";
						echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
						echo "<tr>";
						echo "<td align = center bgcolor='#33cc99' ><font size=2  face='arial'>Causas por mejorar</font></td>";
						echo "<td align = center bgcolor='#33cc99'><font size=2  face='arial'>Codigo</font></td>";

						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  bgcolor='#33cc99' ><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
						}
						echo "<td align = center bgcolor='#cc99ff' ><font size=2  face='arial'>% ".nombre_mes($nmeses)."</font></td>";
						echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>Acum a ".nombre_mes($nmeses)."</font></td>";
						echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>% Acum a ".nombre_mes($nmeses)."</font></td>";
						echo "</tr>";

						$totMes=0;
						$totAcu=0;
						for ($i=1;$i<=$num;$i++)
						{
							$totCausa=0;
							$row = mysql_fetch_row($err);
							echo "<tr>";
							$exp=explode('-',$row[1]);
							echo "<td align = center ><font size=2  face='arial'>".$exp[1]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$exp[0]."</font></td>";

							for($j=1;$j<=$nmeses;$j++)
							{

								if ($j<10)
								{
									$date1=$ano."-0".$j."-01";
									$date2=$ano."-0".$j."-31";
								}else
								{
									$date1=$ano."-".$j."-01";
									$date2=$ano."-".$j."-31";
								}

								$q="Select count(*) from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$date1."'  and '".$date2."' and B.id_comentario=A.id and B.cmocau='".$row[1]."' and B.cmotip='Desagrado'   ";
								$res=mysql_query($q,$conex);
								$num2=mysql_num_rows($res);
								$row2 = mysql_fetch_row($res);

								echo "<td align = center ><font size=2  face='arial'>".$row2[0]."</font></td>";
								$totCausa=$totCausa+$row2[0];
							}


							$porMes=promedio2($row2[0],$cmes['desagrado'][$nmeses]);
							$porAcum=promedio2($totCausa,$desagrado);
							$totMes=	$totMes+$porMes;
							$totAcu=	$totAcu+$porAcum;
							echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".	$porMes."%</font></td>";
							echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$totCausa."</font></td>";
							echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$porAcum."%</font></td>";
							echo "<tr>";
						}
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>Totales</font></td>";
						echo "<td align = center ><font size=2  face='arial'>&nbsp</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center ><font size=2 face='arial'>".$cmes['desagrado'][$j]."</font></td>";
						}
						echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$totMes."%</font></td>";
						echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$desagrado."</font></td>";
						echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>  ".$totAcu."%</font></td>";
						echo "</tr>";

					}else
					{
						ECHO 'NO SE ENCUENTRA NINGUN MOTIVO ESTE MES';
					}
					echo "</table></br>";

					$query="Select count(B.id), B.cmocau from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$Fecha2."'  and '".$Fecha3."' and B.id_comentario=A.id and B.cmotip='Agrado' group by B.cmocau order by 1 desc";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

					if ($num>0)
					{
						echo "<center><font size=2  color='#00008B' face='arial'><b>6.2 Causas mas relevantes de los comentarios positivos</font></b></center></br>";
						echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
						echo "<tr>";
						echo "<td align = center bgcolor='#33cc99' ><font size=2  face='arial'>Causas positivas</font></td>";
						echo "<td align = center bgcolor='#33cc99'><font size=2  face='arial'>Codigo</font></td>";

						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  bgcolor='#33cc99' ><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
						}
						echo "<td align = center bgcolor='#cc99ff' ><font size=2  face='arial'>% ".nombre_mes($nmeses)."</font></td>";
						echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>Acum a ".nombre_mes($nmeses)."</font></td>";
						echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>% Acum a ".nombre_mes($nmeses)."</font></td>";
						echo "</tr>";

						$totMes=0;
						$totAcu=0;
						for ($i=1;$i<=$num;$i++)
						{
							$totCausa=0;
							$row = mysql_fetch_row($err);
							echo "<tr>";
							$exp=explode('-',$row[1]);
							echo "<td align = center ><font size=2  face='arial'>".$exp[1]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$exp[0]."</font></td>";

							for($j=1;$j<=$nmeses;$j++)
							{

								if ($j<10)
								{
									$date1=$ano."-0".$j."-01";
									$date2=$ano."-0".$j."-31";
								}else
								{
									$date1=$ano."-".$j."-01";
									$date2=$ano."-".$j."-31";
								}

								$q="Select count(*) from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$date1."'  and '".$date2."' and B.id_comentario=A.id and B.cmocau='".$row[1]."' and B.cmotip='Agrado'   ";
								$res=mysql_query($q,$conex);
								$num2=mysql_num_rows($res);
								$row2 = mysql_fetch_row($res);

								echo "<td align = center ><font size=2  face='arial'>".$row2[0]."</font></td>";
								$totCausa=$totCausa+$row2[0];
							}


							$porMes=promedio2($row2[0],$cmes['agrado'][$nmeses]);
							$porAcum=promedio2($totCausa,$agrado);
							$totMes=	$totMes+$porMes;
							$totAcu=	$totAcu+$porAcum;
							echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".	$porMes."%</font></td>";
							echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$totCausa."</font></td>";
							echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$porAcum."%</font></td>";
							echo "<tr>";
						}
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>Totales</font></td>";
						echo "<td align = center ><font size=2  face='arial'>&nbsp</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center ><font size=2 face='arial'>".$cmes['agrado'][$j]."</font></td>";
						}
						echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$totMes."%</font></td>";
						echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$agrado."</font></td>";
						echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>  ".$totAcu."%</font></td>";
						echo "</tr>";
						echo "</table></br>";
					}else
					{
						ECHO 'NO SE ENCUENTRA NINGUN MOTIVO ESTE MES';
					}


					break;

					case '7':

					echo "<font size=2  color='#00008B' face='arial'><b>7. Causas mas relevantes de los comentarios por unidad</font></b></br>";
					/************************************************************************************/
					//BUSQUEDA DE SEXTO INDICADOR, COMENTARIO DE AGRADO Y DESAGRADO POR UNIDAD

					$Fecha2=$ano."-01-01";
					$Fecha3=$ano."-".$nmeses."-31";

					if (isset ($unidadS['id'][0]))
					{
						$vas=$unidadS['id'][0];
						$unidadS['id'][0]=$unidad['id'][0];
						$unidad['id'][0]=$vas;
						$vas=$unidadS['nombre'][0];
						$unidadS['nombre'][0]=$unidad['nombre'][0];
						$unidad['nombre'][0]=$vas;
						$vas=$unidades;
						$unidades=1;

					}

					for($i=0;$i<$unidades;$i++)
					{
						$pintar=0;
						//verifico si la unidad esta en la lista para el responsable, si lo esta hago el proceso
						for($y=0;$y<$numAre;$y++)
						{

							if ($unidad['id'][$i]==$areaL['id'][$y])
							{
								$query ="SELECT id_responsable FROM ".$empresa."_000020 where id_area='".$unidad['id'][$i]."' ";
								$err=mysql_query($query,$conex);
								$row = mysql_fetch_row($err);

								if ($unidad['id'][$i]==41 or $row[0]!='01750')
								$pintar=1;
							}

						}

						if ($pintar==1)
						{
							$query="	SELECT 	count(B.id), B.cmocau
										FROM 	".$empresa."_000017 A, ".$empresa."_000018 B
										WHERE 	A.Ccofori between '".$Fecha2."'  and '".$Fecha3."'
												AND B.Id_Comentario=A.id
												AND B.cmotip='Desagrado'
												AND B.Id_Area='".$unidad['id'][$i]."'
										GROUP BY B.cmocau
										ORDER BY 1 DESC";

							$err=mysql_query($query,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
							$num=mysql_num_rows($err);

							// echo "<pre>"; print_r($query); echo "</pre>";
							if ($num>0)
							{
								echo "<center><font size=2  color='#00008B' face='arial'><b>7. Causas mas relevantes de los comentarios por mejorar para ".$unidad['nombre'][$i]."</font></b></center></br>";
								echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
								echo "<tr>";
								echo "<td align = center bgcolor='#33cc99' ><font size=2  face='arial'>Causas por mejorar</font></td>";
								echo "<td align = center bgcolor='#33cc99'><font size=2  face='arial'>Codigo</font></td>";

								for($j=1;$j<=$nmeses;$j++)
								{
									echo "<td align = center  bgcolor='#33cc99' ><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
								}
								echo "<td align = center bgcolor='#cc99ff' ><font size=2  face='arial'>% ".nombre_mes($nmeses)."</font></td>";
								echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>Acum a ".nombre_mes($nmeses)."</font></td>";
								echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>% Acum a ".nombre_mes($nmeses)."</font></td>";
								echo "</tr>";

								$totMes=0;
								$totAcu=0;
								for ($k=1;$k<=$num;$k++)
								{
									$totCausa=0;
									$row = mysql_fetch_row($err);
									echo "<tr>";
									$exp=explode('-',$row[1]);
									echo "<td align = center ><font size=2  face='arial'>".$exp[1]."</font></td>";
									echo "<td align = center ><font size=2  face='arial'>".$exp[0]."</font></td>";

									for($j=1;$j<=$nmeses;$j++)
									{

										if ($j<10)
										{
											$date1=$ano."-0".$j."-01";
											$date2=$ano."-0".$j."-31";
										}else
										{
											$date1=$ano."-".$j."-01";
											$date2=$ano."-".$j."-31";
										}

										$q="Select count(*) from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$date1."'  and '".$date2."' and B.Id_Comentario=A.id and B.cmocau='".$row[1]."' and B.cmotip='Desagrado' and B.Id_Area='".$unidad['id'][$i]."'   ";
										$res=mysql_query($q,$conex);
										$num2=mysql_num_rows($res);
										$row2 = mysql_fetch_row($res);
										echo "<td align = center ><font size=2  face='arial'>".$row2[0]."</font></td>";
										$totCausa=$totCausa+($row2[0]*1);
									}
									if (isset(	$unidadS['id'][0]))
									{
										$porMes=promedio2($row2[0],$unidad[$puesto.'desagrado'][$nmeses]);
										$porAcum=promedio2($totCausa,	$unidad['desagradoTotal'][$puesto]);
									}else
									{
										$porMes=promedio2($row2[0],$unidad[$i.'desagrado'][$nmeses]);
										$porAcum=promedio2($totCausa,	$unidad['desagradoTotal'][$i]);
									}
									$totMes=	$totMes+$porMes;
									$totAcu=	$totAcu+$porAcum;
									echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".	$porMes."%</font></td>";
									echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$totCausa."</font></td>";
									echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$porAcum."%</font></td>";
									echo "<tr>";
								}
								echo "<tr>";
								echo "<td align = center ><font size=2  face='arial'>Totales</font></td>";
								echo "<td align = center ><font size=2  face='arial'>&nbsp</font></td>";
								for($j=1;$j<=$nmeses;$j++)
								{
									if (isset(	$unidadS['id'][0]))
									echo "<td align = center ><font size=2 face='arial'>".$unidad[$puesto.'desagrado'][$j]."</font></td>";
									else
									echo "<td align = center ><font size=2 face='arial'>".$unidad[$i.'desagrado'][$j]."</font></td>";
								}
								echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$totMes."%</font></td>";
								if (isset(	$unidadS['id'][0]))
								echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$unidad['desagradoTotal'][$puesto]."</font></td>";
								else
								echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$unidad['desagradoTotal'][$i]."</font></td>";
								echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>  ".$totAcu."%</font></td>";
								echo "</tr>";
								echo "</table></br>";
							}else
							{
								echo "<center><font size=2  color='#00008B' face='arial'><b>7. Este mes no hay motivos por mejorar para: ".$unidad['nombre'][$i]."</font></b></center></br>";
							}


							$query="Select count(B.id), B.cmocau from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$Fecha2."'  and '".$Fecha3."' and B.Id_Comentario=A.id and B.cmotip='Agrado' and B.Id_Area='".$unidad['id'][$i]."' group by B.cmocau order by 1 desc";
							$err=mysql_query($query,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());
							$num=mysql_num_rows($err);
							if ($num>0)
							{
								echo "<center><font size=2  color='#00008B' face='arial'><b>7. Causas mas relevantes de los comentarios positivos para ".$unidad['nombre'][$i]."</font></b></center></br>";
								echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
								echo "<tr>";
								echo "<td align = center bgcolor='#33cc99' ><font size=2  face='arial'>Causas positivas</font></td>";
								echo "<td align = center bgcolor='#33cc99'><font size=2  face='arial'>Codigo</font></td>";

								for($j=1;$j<=$nmeses;$j++)
								{
									echo "<td align = center  bgcolor='#33cc99' ><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
								}
								echo "<td align = center bgcolor='#cc99ff' ><font size=2  face='arial'>% ".nombre_mes($nmeses)."</font></td>";
								echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>Acum a ".nombre_mes($nmeses)."</font></td>";
								echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>% Acum a ".nombre_mes($nmeses)."</font></td>";
								echo "</tr>";

								$totMes=0;
								$totAcu=0;
								for ($k=1;$k<=$num;$k++)
								{
									$totCausa=0;
									$row = mysql_fetch_row($err);
									echo "<tr>";
									$exp=explode('-',$row[1]);
									echo "<td align = center ><font size=2  face='arial'>".$exp[1]."</font></td>";
									echo "<td align = center ><font size=2  face='arial'>".$exp[0]."</font></td>";

									for($j=1;$j<=$nmeses;$j++)
									{

										if ($j<10)
										{
											$date1=$ano."-0".$j."-01";
											$date2=$ano."-0".$j."-31";
										}else
										{
											$date1=$ano."-".$j."-01";
											$date2=$ano."-".$j."-31";
										}

										$q="Select count(*) from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$date1."'  and '".$date2."' and B.Id_Comentario=A.id and B.cmocau='".$row[1]."' and B.cmotip='Agrado' and B.Id_Area='".$unidad['id'][$i]."'  ";
										$res=mysql_query($q,$conex);
										$num2=mysql_num_rows($res);
										$row2 = mysql_fetch_row($res);

										echo "<td align = center ><font size=2  face='arial'>".$row2[0]."</font></td>";
										$totCausa=$totCausa+$row2[0];
									}

									if (isset(	$unidadS['id'][0]))
									{
										$porMes=promedio2($row2[0],$unidad[$puesto.'agrado'][$nmeses]);
										$porAcum=promedio2($totCausa,	$unidad['agradoTotal'][$puesto]);
									}else
									{
										$porMes=promedio2($row2[0],$unidad[$i.'agrado'][$nmeses]);
										$porAcum=promedio2($totCausa,	$unidad['agradoTotal'][$i]);
									}
									$totMes=	$totMes+$porMes;
									$totAcu=	$totAcu+$porAcum;
									echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".	$porMes."%</font></td>";
									echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$totCausa."</font></td>";
									echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$porAcum."%</font></td>";
									echo "<tr>";
								}
								echo "<tr>";
								echo "<td align = center ><font size=2  face='arial'>Totales</font></td>";
								echo "<td align = center ><font size=2  face='arial'>&nbsp</font></td>";
								for($j=1;$j<=$nmeses;$j++)
								{
									if (isset(	$unidadS['id'][0]))
									echo "<td align = center ><font size=2 face='arial'>".$unidad[$puesto.'agrado'][$j]."</font></td>";
									else
									echo "<td align = center ><font size=2 face='arial'>".$unidad[$i.'agrado'][$j]."</font></td>";
								}
								echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>".$totMes."%</font></td>";
								if (isset(	$unidadS['id'][0]))
								echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$unidad['agradoTotal'][$puesto]."</font></td>";
								else
								echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$unidad['agradoTotal'][$i]."</font></td>";
								echo "<td align = center bgcolor='#cc99ff'><font size=2  face='arial'>  ".$totAcu."%</font></td>";
								echo "</tr>";
								echo "</table></br>";
							}else
							{
								echo "<center><font size=2  color='#00008B' face='arial'><b>7. Este mes no hay motivos positivos para: ".$unidad['nombre'][$i]."</font></b></center></br>";
							}

						}
					}

					break;

					case '8':

					/************************************************************************************/
					//Indicador comparitivo entre comentarios positivos y negativos y nivel de satisfaccion, organizados por cantidad

					$Fecha2=$ano."-01-01";
					$Fecha3=$ano."-".$nmeses."-31";
					echo "<font size=2  color='#00008B' face='arial'><b>8. COMPARATIVO Comentarios Positivos y Por Mejorar entre unidades y Nivel de Satisfaccion</font></b></br></br>";

					$query="Select count(B.id), B.Id_Area from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$Fecha2."'  and '".$Fecha3."' and B.Id_Comentario=A.id  group by B.Id_Area order by 1 desc";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					if ($num>0)
					{
						echo "<center><font size=2  color='#00008B' face='arial'><b>8.1 COMPARATIVO Comentarios Positivos y Por Mejorar entre unidades y Nivel de Satisfaccion</font></b></center></br>";

						echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
						echo "<tr>";
						echo "<td align = center rowspan=2 ><font size=2  face='arial'>Unidad</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  colspan=3><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
						}
						echo "<td align = center  colspan=3><font size=2  face='arial'>Acumulado</font></td>";
						echo "<td align = center  rowspan=2><font size=2  face='arial'>Nivel de satisfaccion</font></td>";
						echo "</tr>";


						echo "<tr>";

						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  ><font size=2 face='arial'>Total</font></td>";
							echo "<td align = center><font size=2 face='arial'>-</font></td>";
							echo "<td align = center   ><font size=2 face='arial'>+</font></td>";
						}

						echo "<td align = center  bgcolor='#99ccff' ><font size=2 face='arial'>Total</font></td>";
						echo "<td align = center  ><font size=2 face='arial'>-</font></td>";
						echo "<td align = center  ><font size=2 face='arial'>+</font></td>";
						echo "</tr>";

						for($n=0;$n<$num;$n++)
						{
							$row = mysql_fetch_row($err);

							for($i=0;$i<$unidades;$i++)
							{
								$pintar=0;
								//verifico si la unidad esta en la lista para el responsable, si lo esta hago el proceso
								for($y=0;$y<$numAre;$y++)
								{
									if ($unidad['id'][$i]==$areaL['id'][$y] and $unidad['id'][$i]==$row[1])
									{
										$query ="SELECT id_responsable FROM ".$empresa."_000020 where id_area='".$unidad['id'][$i]."' ";
										$res=mysql_query($query,$conex);
										$row2 = mysql_fetch_row($res);

										if ($unidad['id'][$i]==41 or $row[0]!='01750')
										$pintar=1;
									}
								}

								if ($pintar==1)
								{
									if (isset($unidadS['id'][0]))
									$i=$puesto;
									echo "<tr>";
									echo "<td align = center  bgcolor='#99ccff'><font size=2 face='arial'>".$unidad['nombre'][$i]."</font></td>";


									$totalP=0;
									$totalN=0	;
									for($j=1;$j<=$nmeses;$j++)
									{
										$totMes=$unidad[$i.'desagrado'][$j]+$unidad[$i.'agrado'][$j];
										echo "<td align = center><font size=2  face='arial'>".	$totMes."</font></td>";
										echo "<td align = center ><font size=2  face='arial'>".$unidad[$i.'desagrado'][$j]."</font></td>";
										echo "<td align = center ><font size=2  face='arial'>".$unidad[$i.'agrado'][$j]."</font></td>";
										$totalP=$totalP+$unidad[$i.'agrado'][$j];
										$totalN=$totalN+	$unidad[$i.'desagrado'][$j];
									}
									$total=$totalP+$totalN;
									$por=promedio2($totalP,$total);
									echo "<td align = center bgcolor='#99ccff' ><font size=2  face='arial'>".$total."</font></td>";
									echo "<td align = center ><font size=2  face='arial'>	".$totalN."</font></td>";
									echo "<td align = center ><font size=2  face='arial'>	".$totalP."</font></td>";
									echo "<td align = center ><font size=2  face='arial'>".$por."%</font></td>";
									echo "<tr>";
								}
							}
						}

						echo "<tr>";
						echo "<td align = center  bgcolor='#99ccff'><font size=2 face='arial'>Total Clinica</font></td>";

						for($j=1;$j<=$nmeses;$j++)
						{
							$totMes=$cmes['desagrado'][$j]+$cmes['agrado'][$j];
							echo "<td align = center><font size=2  face='arial'>".	$totMes."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$cmes['desagrado'][$j]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$cmes['agrado'][$j]."</font></td>";
						}

						$por=promedio2($agrado,$comentarios);
						echo "<td align = center bgcolor='#99ccff' ><font size=2  face='arial'>".$comentarios."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>	".$desagrado."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>	".$agrado."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$por."%</font></td>";
						echo "<tr>";

					}
					echo "</table><br>";
					/************************************************************************************/
					// desgloce para hospitalizacion
					$pinta=0;

					for($y=0;$y<$numAre;$y++)
					{
						if (!isset($unidadS['id'][0]))
						{
							$exp=explode('-',$areaL['nombre'][$y]);
							if ($exp[1]=='Servicio Magenta' or $exp[1]=='Tercer piso')
							$pintar=2;
						}
					}

					if ($pintar==2) //pinto indicador de hospitalizacion por pisos
					{
						echo "<center><font size=2  color='#00008B' face='arial'><b>8.2 Comentarios Positivos y Por Mejorar Hospitalizacion (por lugar de origen)</font></b></center></br>";

						echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
						echo "<tr>";
						echo "<td align = center rowspan=2 ><font size=2  face='arial'>Lugar de origen</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  colspan=3><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
							$totalMes[$j]=0;
							$totalMesP[$j]=0;
							$totalMesN[$j]=0;
						}
						echo "<td align = center  colspan=3><font size=2  face='arial'>Acumulado</font></td>";
						echo "<td align = center  rowspan=2><font size=2  face='arial'>Nivel de satisfaccion</font></td>";
						echo "</tr>";


						echo "<tr>";

						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  ><font size=2 face='arial'>Total</font></td>";
							echo "<td align = center><font size=2 face='arial'>-</font></td>";
							echo "<td align = center   ><font size=2 face='arial'>+</font></td>";
						}

						echo "<td align = center  bgcolor='#99ccff' ><font size=2 face='arial'>Total</font></td>";
						echo "<td align = center  ><font size=2 face='arial'>-</font></td>";
						echo "<td align = center  ><font size=2 face='arial'>+</font></td>";
						echo "</tr>";

						$q="select A.ccoori  from magenta_000017 A, magenta_000018 B, magenta_000020 C where A.Ccofori between '".$ano."-01-01' and '".$ano."-".$nmeses."-31' and A.id=B.Id_Comentario and B.Id_Area=C.Id_Area and C.Id_Responsable='01750'  group by A.ccoori";

						$err=mysql_query($q,$conex);
						$nlugares=mysql_num_rows($err);

						for($i=1;$i<=$nlugares;$i++)
						{
							$totalP=0;
							$totalN=0;
							$total=0;

							$row = mysql_fetch_row($err);
							echo "<td align = center><font size=2  face='arial'>".	$row[0]."</font></td>";
							for($j=1;$j<=$nmeses;$j++)
							{

								if ($j<10)
								{
									$date1=$ano."-0".$j."-01";
									$date2=$ano."-0".$j."-31";
								}else
								{
									$date1=$ano."-".$j."-01";
									$date2=$ano."-".$j."-31";
								}

								$q1="select * from magenta_000017 A, magenta_000018 B, magenta_000020 C where A.Ccofori between '".$date1."' and '".$date2."' and A.id=B.Id_Comentario  and A.ccoori='".$row[0]."' and C.Id_Responsable='01750' and C.Id_Area=B.Id_Area ";
								$res1=mysql_query($q1,$conex);
								$num1=mysql_num_rows($res1);

								$q2="select * from magenta_000017 A, magenta_000018 B, magenta_000020 C  where A.Ccofori between '".$date1."' and '".$date2."' and B.cmotip='Desagrado' and A.id=B.Id_Comentario  and A.ccoori='".$row[0]."' and C.Id_Responsable='01750' and C.Id_Area=B.Id_Area   ";
								$res2=mysql_query($q2,$conex);
								$num2=mysql_num_rows($res2);

								$q3="select * from magenta_000017 A, magenta_000018 B, magenta_000020 C where A.Ccofori between '".$date1."' and '".$date2."' and B.cmotip='Agrado' and A.id=B.Id_Comentario  and A.ccoori='".$row[0]."' and C.Id_Responsable='01750' and C.Id_Area=B.Id_Area   ";
								$res3=mysql_query($q3,$conex);
								$num3=mysql_num_rows($res3);


								echo "<td align = center ><font size=2  face='arial'>".$num1."</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$num2."</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$num3."</font></td>";
								$totalP=$totalP+$num3;
								$totalN=$totalN+$num2;
								$totalMes[$j]=$totalMes[$j]+$num3+$num2;
								$totalMesP[$j]=$totalMesP[$j]+$num3;
								$totalMesN[$j]=$totalMesN[$j]+$num2;
							}

							$total=$totalP+$totalN;
							$por=promedio2($totalP,$total);
							echo "<td align = center bgcolor='#99ccff' ><font size=2  face='arial'>".$total."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>	".$totalN."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>	".$totalP."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$por."%</font></td>";
							echo "<tr>";
						}
						$acumuP=0;
						$acumuN=0;
						$acumu=0;
						echo "<tr>";
						echo "<td align = center rowspan=2 ><font size=2  face='arial'>Total Hospitalizacion</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center ><font size=2  face='arial'>".$totalMes[$j]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$totalMesN[$j]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$totalMesP[$j]."</font></td>";

							$acumuP=$acumuP+$totalMesP[$j];
							$acumuN=$acumuN+	$totalMesN[$j];
						}

						$acumu=$acumuP+$acumuN;
						$por=promedio2($acumuP,$acumu);
						echo "<td align = center bgcolor='#99ccff' ><font size=2  face='arial'>".$acumu."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>	".$acumuN."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>	".$acumuP."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$por."%</font></td>";
						echo "<tr>";

						echo "</table><br>";
					}


					break;

					case '9';

					/************************************************************************************/
					//Indicador de semaforizacion realizo todas la averiguaciones por area por mes

					$verde=0;
					$verde2=0;
					$amarillo=0;
					$amarillo2=0;
					$rojo=0;
					$rojo2=0;
					$pendiente=0;
					$pendiente2=0;


					for($i=0;$i<$unidades;$i++)
					{
						$uni[$i]=0;
						$uni2[$i]=0;
						$unidad['rojoTotal'][$i]=0;
						$unidad['rojoTotal2'][$i]=0;
						$unidad['verdeTotal'][$i]=0;
						$unidad['verdeTotal2'][$i]=0;
						$unidad['amarilloTotal'][$i]=0;
						$unidad['amarilloTotal2'][$i]=0;
						$unidad['pendienteTotal'][$i]=0;
						$unidad['pendienteTotal2'][$i]=0;

						for($j=1;$j<=$nmeses;$j++)
						{
							if ($i==0)
							{
								$cmes['verde'][$j]=0;
								$cmes['verde2'][$j]=0;
								$cmes['amarillo'][$j]=0;
								$cmes['amarillo2'][$j]=0;
								$cmes['rojo'][$j]=0;
								$cmes['rojo2'][$j]=0;
								$cmes['pendiente'][$j]=0;
								$cmes['pendiente2'][$j]=0;
								$cmes['total'][$j]=0;
								$cmes['total2'][$j]=0;
							}

							if ($j<10)
							{
								$date1=$ano."-0".$j."-01";
								$date2=$ano."-0".$j."-31";
								$date3=$ano2."-0".$j."-01";
								$date4=$ano2."-0".$j."-31";
							}else
							{
								$date1=$ano."-".$j."-01";
								$date2=$ano."-".$j."-31";
								$date3=$ano2."-".$j."-01";
								$date4=$ano2."-".$j."-31";
							}

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmosem='verde' and B.cmotip='Desagrado' and cmoest<>'INGRESADO' and cmoest<>'ASIGNADO' and cmoest<>'TRAMITANDO'";
							$err=mysql_query($query,$conex);
							$num1=mysql_num_rows($err);
							$unidad[$i.'verde'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
							$cmes['verde'][$j]=$cmes['verde'][$j]+$num1; //numero de comentarios de agrado del mes


							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmosem='amarillo' and B.cmotip='Desagrado' and cmoest<>'INGRESADO' and cmoest<>'ASIGNADO' and cmoest<>'TRAMITANDO' ";
							$err=mysql_query($query,$conex);
							$num2=mysql_num_rows($err);
							$unidad[$i.'amarillo'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
							$cmes['amarillo'][$j]=$cmes['amarillo'][$j]+$num2; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmosem='verde' and B.cmotip='Desagrado' and cmoest<>'INGRESADO' and cmoest<>'ASIGNADO' and cmoest<>'TRAMITANDO' ";
							$err=mysql_query($query,$conex);
							$num3=mysql_num_rows($err);
							$unidad[$i.'verde2'][$j]= $num3;  //numero de comentarios de desagrado por unidad por mes
							$cmes['verde2'][$j]=$cmes['verde2'][$j]+$num3; //numero de comentarios de agrado del mes


							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmosem='amarillo' and B.cmotip='Desagrado' and cmoest<>'INGRESADO' and cmoest<>'ASIGNADO' and cmoest<>'TRAMITANDO' ";
							$err=mysql_query($query,$conex);
							$num4=mysql_num_rows($err);
							$unidad[$i.'amarillo2'][$j]= $num4;  //numero de comentarios de desagrado por unidad por mes
							$cmes['amarillo2'][$j]=$cmes['amarillo2'][$j]+$num4; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmosem='rojo' and B.cmotip='Desagrado' and cmoest<>'INGRESADO' and cmoest<>'ASIGNADO' and cmoest<>'TRAMITANDO' ";
							$err=mysql_query($query,$conex);
							$num5=mysql_num_rows($err);
							$unidad[$i.'rojo'][$j]= $num5; //numero de comentarios de agrado por unidad por mes
							$cmes['rojo'][$j]=$cmes['rojo'][$j]+$num5; //numero de comentarios de agrado del mes


							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmosem='rojo' and B.cmotip='Desagrado'  and cmoest<>'INGRESADO' and cmoest<>'ASIGNADO' and cmoest<>'TRAMITANDO' ";
							$err=mysql_query($query,$conex);
							$num6=mysql_num_rows($err);
							$unidad[$i.'rojo2'][$j]= $num6;  //numero de comentarios de desagrado por unidad por mes
							$cmes['rojo2'][$j]=$cmes['rojo2'][$j]+$num6; //numero de comentarios de agrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmoest<>'CERRADO' and B.cmoest<>'INVESTIGADO' and B.cmotip='Desagrado' ";
							$err=mysql_query($query,$conex);
							$num7=mysql_num_rows($err);
							$unidad[$i.'pendiente'][$j]= $num7;  //numero de comentarios de desagrado por unidad por mes
							$cmes['pendiente'][$j]=$cmes['pendiente'][$j]+$num7; //numero de comentarios de desagrado del mes

							$query ="SELECT A.id, A.ccofori, B.id ";
							$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id and B.id_area='".$unidad['id'][$i]."' and B.cmoest<>'CERRADO' and B.cmoest<>'INVESTIGADO' and B.cmotip='Desagrado' ";
							$err=mysql_query($query,$conex);
							$num8=mysql_num_rows($err);
							$unidad[$i.'pendiente2'][$j]= $num8;  //numero de comentarios de desagrado por unidad por mes
							$cmes['pendiente2'][$j]=$cmes['pendiente2'][$j]+$num8; //numero de comentarios de desagrado del mes

							$cmes['total2'][$j]=$cmes['total2'][$j]+$num3+$num4+$num6+$num8;
							$cmes['total'][$j]=$cmes['total'][$j]+$num1+$num2+$num5+$num7;

							//numero de comentarios de la unidad en el mes
							$umes2[$i][$j]=$num3+$num4+$num6+$num8;
							$umes[$i][$j]=$num1+$num2+$num5+$num7;


							//numero de comentarios de agrado y desagrado por unidad
							$unidad['verdeTotal'][$i]=$unidad['verdeTotal'][$i]+$unidad[$i.'verde'][$j];
							$unidad['amarilloTotal'][$i]=$unidad['amarilloTotal'][$i]+$unidad[$i.'amarillo'][$j];
							$unidad['verdeTotal2'][$i]=$unidad['verdeTotal2'][$i]+$unidad[$i.'verde2'][$j];
							$unidad['amarilloTotal2'][$i]=$unidad['amarilloTotal2'][$i]+$unidad[$i.'amarillo2'][$j];

							$unidad['rojoTotal'][$i]=$unidad['rojoTotal'][$i]+$unidad[$i.'rojo'][$j];
							$unidad['pendienteTotal'][$i]=$unidad['pendienteTotal'][$i]+$unidad[$i.'pendiente'][$j];
							$unidad['rojoTotal2'][$i]=$unidad['rojoTotal2'][$i]+$unidad[$i.'rojo2'][$j];
							$unidad['pendienteTotal2'][$i]=$unidad['pendienteTotal2'][$i]+$unidad[$i.'pendiente2'][$j];

						}

						//comentarios de la unidad
						$uni[$i]=$uni[$i]+$unidad['verdeTotal'][$i]+$unidad['amarilloTotal'][$i]+$unidad['rojoTotal'][$i]+$unidad['pendienteTotal'][$i];
						$uni2[$i]=$uni2[$i]+$unidad['verdeTotal2'][$i]+$unidad['amarilloTotal2'][$i]+$unidad['rojoTotal2'][$i]+$unidad['pendienteTotal2'][$i];

						//numero de comentarios de agrado y desagrado

						$verde=$verde+$unidad['verdeTotal'][$i];
						$amarillo=$amarillo+$unidad['amarilloTotal'][$i];
						$verde2=$verde2+$unidad['verdeTotal2'][$i];
						$amarillo2=	$amarillo2+$unidad['amarilloTotal2'][$i];

						$rojo=$rojo+$unidad['rojoTotal'][$i];
						$pendiente=$pendiente+$unidad['pendienteTotal'][$i];
						$rojo2=$rojo2+$unidad['rojoTotal2'][$i];
						$pendiente2=	$pendiente2+$unidad['pendienteTotal2'][$i];
					}

					/************************************************************************************/
					//Grafico indicadores de semaforizacion

					echo "<font size=2  color='#00008B' face='arial'><b>9. SEMAFORIZACION. ".$ano2." - ".$ano." </font></b></br></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>9.1 SEMAFORIZACION. ".$ano2." - ".$ano." - Reporte general de la Clinica</font></b></center></br>";

					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='8' bgcolor='#cc66ff'><font size=2  face='arial'>Nivel de respuesta".$ano2."</font></td>";
					echo "<td align = center colspan='10' bgcolor='#3366ff'><font size=2 face='arial' >Nivel de respuesta".$ano."</font></td>";
					echo "<td align = center bgcolor='#ff9900' ><font size=2  face='arial'>Variacion</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2  face='arial'>1</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2 face='arial'>%</font></td>";
					echo "<td align = center    bgcolor='#ffff66'> <font size=2  face='arial'>2</font></td>";
					echo "<td align = center   bgcolor='#ffff66'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial' >3</font></td>";
					echo "<td align = center   bgcolor='#ff0000'><font size=2 face='arial' c>%</font></td>";
					echo "<td align = center  bgcolor='#cc66ff'><font size=2  face='arial' >Total</font></td>";
					echo "<td align = center  bgcolor='#cc66ff'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>1</font></td>";
					echo "<td align = center    bgcolor='#339933'><font size=2 face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ffff66' > <font size=2  face='arial'>2</font></td>";
					echo "<td align = center   bgcolor='#ffff66'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial' >3</font></td>";
					echo "<td align = center    bgcolor='#ff0000'><font size=2 face='arial' >%</font></td>";
					echo "<td align = center  bgcolor='#ccffff'><font size=2  face='arial'>Pendiente</font></td>";
					echo "<td align = center    bgcolor='#ccffff'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center   bgcolor='#3366ff'><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center   bgcolor='#3366ff'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center   bgcolor='#339933'><font size=2  face='arial' >1</font></td>";
					echo "</tr>";


					for($i=1;$i<=$nmeses;$i++)
					{
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['verde2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['verde2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['amarillo2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['amarillo2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['rojo2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['rojo2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['total2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['total2'][$i], $desagrado2) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['verde'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['verde'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['amarillo'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['amarillo'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['rojo'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['rojo'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['pendiente'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['pendiente'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".($cmes['verde'][$i]+$cmes['amarillo'][$i]+$cmes['rojo'][$i]+$cmes['pendiente'][$i])."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['total'][$i], $desagrado) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio ($cmes['verde'][$i], $cmes['verde2'][$i]) ."%</font></td>";
						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2  face='arial'>".$verde2."</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2 face='arial'>".promedio2 ($verde2, $desagrado2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ffff66'><font size=2  face='arial'>".$amarillo2."</font></td>";
					echo "<td align = center  bgcolor='#ffff66'><font size=2  face='arial'>".promedio2 ($amarillo2, $desagrado2) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".$rojo2."</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".promedio2 ($rojo2, $desagrado2) ."%</font></td>";
					echo "<td align = center bgcolor='#cc66ff'><font size=2  face='arial' >".$desagrado2."</font></td>";
					echo "<td align = center bgcolor='#cc66ff'><font size=2  face='arial' >100%</font></td>";
					echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>".$verde."</font></td>";
					echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>".promedio2 ($verde, $desagrado) ."%</font></td>";
					echo "<td align = center bgcolor='#ffff66'><font size=2  face='arial'>".$amarillo."</font></td>";
					echo "<td align = center bgcolor='#ffff66'><font size=2 face='arial'>".promedio2 ($amarillo, $desagrado)."%</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".$rojo."</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".promedio2 ($rojo, $desagrado) ."%</font></td>";
					echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$pendiente."</font></td>";
					echo "<td align = center bgcolor='#ccffff'><font size=2 face='arial'>".promedio2 ($pendiente, $desagrado)."%</font></td>";
					echo "<td align = center  bgcolor='#3366ff'><font size=2  face='arial' >".($verde+$amarillo+$rojo+$pendiente)."</font></td>";
					echo "<td align = center bgcolor='#3366ff'><font size=2  face='arial' >100%</font></td>";
					echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>".promedio ($verde, $verde2)."%</font></td>";

					echo "</tr>";
					echo "</table></br>";

					/************************************************************************************/
					//Grafico indicadores de semaforizacion, indicadores por área

					echo "<center><font size=2  color='#00008B' face='arial'><b>9.2 SEMAFORIZACION. Por Unidad </font></b></center></br>";

					for($j=0;$j<$unidades;$j++)
					{
						$pintar=0;
						//verifico si la unidad esta en la lista para el responsable, si lo esta hago el proceso
						for($y=0;$y<$numAre;$y++)
						{
							if ($unidad['id'][$j]==$areaL['id'][$y])
							{
								$query ="SELECT id_responsable FROM ".$empresa."_000020 where id_area='".$unidad['id'][$j]."' ";
								$res=mysql_query($query,$conex);
								$row2 = mysql_fetch_row($res);

								if ($unidad['id'][$j]==159 or $row2[0]!=2)
								$pintar=1;
							}
						}

						if ($pintar==1)
						{

							echo "<center><font size=2  color='#00008B' face='arial'><b>9.2 SEMAFORIZACION. ".$unidad['nombre'][$j]."</font></b></center></br>";

							echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

							echo "<tr>";
							echo "<td>&nbsp;</td>";
							echo "<td align = center colspan='8' bgcolor='#cc66ff'><font size=2  face='arial'>Nivel de respuesta".$ano2."</font></td>";
							echo "<td align = center colspan='10' bgcolor='#3366ff'><font size=2 face='arial' >Nivel de respuesta".$ano."</font></td>";
							echo "<td align = center bgcolor='#ff9900' ><font size=2  face='arial'>Variacion</font></td>";
							echo "</tr>";

							echo "<tr>";
							echo "<td align = center ><font size=2  face='arial'>Mes</font></td>";
							echo "<td align = center bgcolor='#339933'><font size=2  face='arial'>1</font></td>";
							echo "<td align = center bgcolor='#339933'><font size=2 face='arial'>%</font></td>";
							echo "<td align = center    bgcolor='#ffff66'> <font size=2  face='arial'>2</font></td>";
							echo "<td align = center   bgcolor='#ffff66'><font size=2  face='arial'>%</font></td>";
							echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial' >3</font></td>";
							echo "<td align = center   bgcolor='#ff0000'><font size=2 face='arial' c>%</font></td>";
							echo "<td align = center  bgcolor='#cc66ff'><font size=2  face='arial' >Total</font></td>";
							echo "<td align = center  bgcolor='#cc66ff'><font size=2  face='arial' >%</font></td>";
							echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>1</font></td>";
							echo "<td align = center    bgcolor='#339933'><font size=2 face='arial'>%</font></td>";
							echo "<td align = center  bgcolor='#ffff66' > <font size=2  face='arial'>2</font></td>";
							echo "<td align = center   bgcolor='#ffff66'><font size=2  face='arial'>%</font></td>";
							echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial' >3</font></td>";
							echo "<td align = center    bgcolor='#ff0000'><font size=2 face='arial' >%</font></td>";
							echo "<td align = center  bgcolor='#ccffff'><font size=2  face='arial'>Pendiente</font></td>";
							echo "<td align = center    bgcolor='#ccffff'><font size=2  face='arial' >%</font></td>";
							echo "<td align = center   bgcolor='#3366ff'><font size=2  face='arial'>Total</font></td>";
							echo "<td align = center   bgcolor='#3366ff'><font size=2  face='arial'>%</font></td>";
							echo "<td align = center   bgcolor='#339933'><font size=2  face='arial' >1</font></td>";
							echo "</tr>";


							for($i=1;$i<=$nmeses;$i++)
							{

								echo "<tr>";
								echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$unidad[$j.'verde2'][$i]."</font></td>";
								echo "<td align = center ><font size=2 face='arial'>".promedio2 ($unidad[$j.'verde2'][$i], $umes2[$j][$i]) ."%</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".$unidad[$j.'amarillo2'][$i]."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad[$j.'amarillo2'][$i], $umes2[$j][$i]) ."%</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$unidad[$j.'rojo2'][$i]."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad[$j.'rojo2'][$i], $umes2[$j][$i]) ."%</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".$umes2[$j][$i]."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($umes2[$j][$i], $uni2[$j]) ."%</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$unidad[$j.'verde'][$i]."</font></td>";
								echo "<td align = center ><font size=2 face='arial'>".promedio2 ($unidad[$j.'verde'][$i], $umes[$j][$i]) ."%</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".$unidad[$j.'amarillo'][$i]."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad[$j.'amarillo'][$i], $umes[$j][$i]) ."%</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$unidad[$j.'rojo'][$i]."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad[$j.'rojo'][$i], $umes[$j][$i]) ."%</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$unidad[$j.'pendiente'][$i]."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad[$j.'pendiente'][$i], $umes[$j][$i]) ."%</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".($unidad[$j.'verde'][$i]+$unidad[$j.'amarillo'][$i]+$unidad[$j.'rojo'][$i]+$unidad[$j.'pendiente'][$i])."</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($umes[$j][$i], $uni[$j]) ."%</font></td>";
								echo "<td align = center  ><font size=2  face='arial'>".promedio ($unidad[$j.'verde'][$i], $unidad[$j.'verde2'][$i]) ."%</font></td>";

								echo "</tr>";

							}

							echo "<tr>";
							echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
							echo "<td align = center bgcolor='#339933'><font size=2  face='arial'>".$unidad['verdeTotal2'][$j]."</font></td>";
							echo "<td align = center bgcolor='#339933'><font size=2 face='arial'>".promedio2 ($unidad['verdeTotal2'][$j], $uni2[$j]) ."%</font></td>";
							echo "<td align = center  bgcolor='#ffff66'><font size=2  face='arial'>".$unidad['amarilloTotal2'][$j]."</font></td>";
							echo "<td align = center  bgcolor='#ffff66'><font size=2  face='arial'>".promedio2 ($unidad['amarilloTotal2'][$j], $uni2[$j]) ."%</font></td>";
							echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".$unidad['rojoTotal'][$j]."</font></td>";
							echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".promedio2 ($unidad['rojoTotal2'][$j], $uni2[$j]) ."%</font></td>";
							echo "<td align = center bgcolor='#cc66ff'><font size=2  face='arial' >".$uni2[$j]."</font></td>";
							echo "<td align = center bgcolor='#cc66ff'><font size=2  face='arial' >100%</font></td>";
							echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>".$unidad['verdeTotal'][$j]."</font></td>";
							echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>".promedio2 ($unidad['verdeTotal'][$j], $uni[$j]) ."%</font></td>";
							echo "<td align = center bgcolor='#ffff66'><font size=2  face='arial'>".$unidad['amarilloTotal'][$j]."</font></td>";
							echo "<td align = center bgcolor='#ffff66'><font size=2 face='arial'>".promedio2 ($unidad['amarilloTotal'][$j], $uni[$j])."%</font></td>";
							echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".$unidad['rojoTotal'][$j]."</font></td>";
							echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".promedio2 ($unidad['rojoTotal'][$j], $uni[$j]) ."%</font></td>";
							echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$unidad['pendienteTotal'][$j]."</font></td>";
							echo "<td align = center bgcolor='#ccffff'><font size=2 face='arial'>".promedio2 ($unidad['pendienteTotal'][$j], $uni[$j])."%</font></td>";
							echo "<td align = center  bgcolor='#3366ff'><font size=2  face='arial' >".($unidad['verdeTotal'][$j]+$unidad['amarilloTotal'][$j]+$unidad['rojoTotal'][$j]+$unidad['pendienteTotal'][$j])."</font></td>";
							echo "<td align = center bgcolor='#3366ff'><font size=2  face='arial' >100%</font></td>";
							echo "<td align = center  bgcolor='#339933'><font size=2  face='arial'>".promedio ($unidad['verdeTotal'][$j], $unidad['verdeTotal2'][$j])."%</font></td>";

							$promedio[$j]=promedio2 ($unidad['verdeTotal'][$j], $uni[$j]);
							echo "</tr>";
							echo "</table></br>";

						}
					}


					/************************************************************************************/
					///pinto comparativo

					echo "<center><font size=2  color='#00008B' face='arial'><b>9.3 COMPARATIVO DE SEMAFORIZACION ACUMULADO A ".nombre_mes($mes)." de ".$ano."</font></b></center></br>";
					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center  ><font size=2  face='arial'>&nbsp;</font></td>";
					echo "<td align = center colspan='9' bgcolor='#3366ff'><font size=2 face='arial' > TOTAL DE COMENTARIOS EN CADA NIVEL DE RESPUESTA: 1,2,3,PENDIENTE Y PORCENTAJE CORRESPONDIENTE A LA SEMAFORIZACION</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>UNIDADES</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2  face='arial'>1</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2 face='arial'>%</font></td>";
					echo "<td align = center    bgcolor='#ffff66'> <font size=2  face='arial'>2</font></td>";
					echo "<td align = center   bgcolor='#ffff66'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial' >3</font></td>";
					echo "<td align = center   bgcolor='#ff0000'><font size=2 face='arial' c>%</font></td>";
					echo "<td align = center  bgcolor='#ccffff'><font size=2  face='arial'>Pendiente</font></td>";
					echo "<td align = center    bgcolor='#ccffff'><font size=2  face='arial' >%</font></td>";
					echo "<td align = center  bgcolor='#cc66ff'><font size=2  face='arial' >Total</font></td>";
					echo "</tr>";


					for($j=0;$j<$unidades;$j++)
					{
						$pintar=0;
						//verifico si la unidad esta en la lista para el responsable, si lo esta hago el proceso
						for($y=0;$y<$numAre;$y++)
						{
							if ($unidad['id'][$j]==$areaL['id'][$y])
							{
								$query ="SELECT id_responsable FROM ".$empresa."_000020 where id_area='".$unidad['id'][$j]."' ";
								$res=mysql_query($query,$conex);
								$row2 = mysql_fetch_row($res);

								if ($unidad['id'][$j]==159 or $row2[0]!=2)
								$pintar=1;
							}
						}

						if ($pintar==1)
						{

							echo "<tr>";
							echo "<td align = center ><font size=2  face='arial'>".$unidad['nombre'][$j]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$unidad['verdeTotal'][$j]."</font></td>";
							echo "<td align = center ><font size=2 face='arial'>".promedio2 ($unidad['verdeTotal'][$j], $uni[$j]) ."%</font></td>";
							echo "<td align = center  ><font size=2  face='arial'>".$unidad['amarilloTotal'][$j]."</font></td>";
							echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad['amarilloTotal'][$j], $uni[$j]) ."%</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$unidad['rojoTotal'][$j]."</font></td>";
							echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad['rojoTotal'][$j], $uni[$j]) ."%</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$unidad['pendienteTotal'][$j]."</font></td>";
							echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($unidad['pendienteTotal'][$j], $uni[$j]) ."%</font></td>";
							echo "<td align = center  ><font size=2  face='arial'>".($unidad['verdeTotal'][$j]+$unidad['amarilloTotal'][$j]+$unidad['rojoTotal'][$j]+$unidad['pendienteTotal'][$j])."</font></td>";
							echo "</tr>";
						}
					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2  face='arial'>".$verde."</font></td>";
					echo "<td align = center bgcolor='#339933'><font size=2 face='arial'>".promedio2 ($verde, $desagrado) ."%</font></td>";
					echo "<td align = center  bgcolor='#ffff66'><font size=2  face='arial'>".$amarillo."</font></td>";
					echo "<td align = center  bgcolor='#ffff66'><font size=2  face='arial'>".promedio2 ($amarillo, $desagrado) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".$rojo."</font></td>";
					echo "<td align = center  bgcolor='#ff0000'><font size=2  face='arial'>".promedio2 ($rojo, $desagrado) ."%</font></td>";
					echo "<td align = center bgcolor='#ccffff'><font size=2  face='arial'>".$pendiente."</font></td>";
					echo "<td align = center bgcolor='#ccffff'><font size=2 face='arial'>".promedio2 ($pendiente, $desagrado)."%</font></td>";
					echo "<td align = center  bgcolor='#cc66ff''><font size=2  face='arial' >".($verde+$amarillo+$rojo+$pendiente)."</font></td>";

					echo "</tr>";

					echo "</table></br>";


					break;

					case '10':

					/************************************************************************************/
					//BUSQUEDA DE ultimo INDICADOR

					// Busco primero las causas y las organizo en orden descendente, por entidad


					$Fecha2=$ano."-01-01";
					$Fecha3=$ano."-".$nmeses."-31";

					for($j=1;$j<=$nmeses;$j++)
					{
						$cmes['agrado'][$j]=0;
						$cmes['desagrado'][$j]=0;
					}

					$totagrado    = 0;
					$totdesagrado = 0;
					$totgeneral   = 0;

					$query=" Select count(B.id), A.ccoent, substring_index(A.ccoent,'-',1), 
					               concat(C.Cempcod,' ',C.Cempnom) as NomEntidad
						     from ".$empresa."_000017 A, ".$empresa."_000018 B, ".$empresa."_000025 C 
						     where A.Ccofori between '".$Fecha2."' and '".$Fecha3."' 
						       and B.id_comentario=A.id  
						       and C.Cempcod = substring_index(A.ccoent,'-',1)
						       group by 3 order by 2 desc";
						
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);


					if ($num>0)
					{
						echo "<center><font size=2  color='#00008B' face='arial'><b>10. Comentarios por entidad</font></b></center></br>";
						echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>&nbsp</font></td>";


						for($j=1;$j<$nmeses;$j++)
						{
							echo "<td align = center   colspan=3><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
						}
						echo "<td align = center  colspan=5><font size=2 face='arial'>".nombre_mes($nmeses)."</font></td>";
						echo "<td align = center colspan=5><font size=2  face='arial'>Acumulado ".nombre_mes($nmeses)."</font></td>";


						echo "</tr>";

						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>Entidad</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							$color=Colorear($j);
							echo "<td align = center  bgcolor='". $color. "' ><font size=2 face='arial'>Positivos</font></td>";
							echo "<td align = center  bgcolor='". $color. "' ><font size=2 face='arial'>Por mejorar</font></td>";
							echo "<td align = center  bgcolor='". $color. "' ><font size=2 face='arial'>Total</font></td>";
						}
						echo "<td align = center  bgcolor='". $color ."' ><font size=2  face='arial'>% participacion</font></td>";
						echo "<td align = center  bgcolor='". $color ."' ><font size=2  face='arial'>Satisfaccion</font></td>";
						echo "<td align = center  bgcolor='#0000ff' ><font size=2 face='arial' color='#ffffff'>Positivos</font></td>";
						echo "<td align = center  bgcolor='#0000ff' ><font size=2 face='arial' color='#ffffff'>Por mejorar</font></td>";
						echo "<td align = center  bgcolor='#0000ff' ><font size=2 face='arial' color='#ffffff'>Total</font></td>";
						echo "<td align = center  bgcolor='#0000ff' ><font size=2 face='arial' color='#ffffff'>% participacion</font></td>";
						echo "<td align = center  bgcolor='#0000ff' ><font size=2 face='arial' color='#ffffff'>Nivel de satisfaccion</font></td>";


						echo "</tr>";

						$totMes=0;
						$totAcu=0;
						for ($i=1;$i<=$num;$i++)
						{
							$totCausaA=0;
							$totCausaB=0;
							$row = mysql_fetch_row($err);
							echo "<tr>";
							echo "<td align = center ><font size=2  face='arial'>".$row[3]."</font></td>";

							for($j=1;$j<=$nmeses;$j++)
							{

								if ($j<10)
								{
									$date1=$ano."-0".$j."-01";
									$date2=$ano."-0".$j."-31";
								}else
								{
									$date1=$ano."-".$j."-01";
									$date2=$ano."-".$j."-31";
								}

                                // Contar comentarios de Agrado
								$q="Select count(*) from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$date1."'  and '".$date2."' and B.id_comentario=A.id and substring_index(A.ccoent,'-',1)='".$row[2]."' and B.cmotip='Agrado'   ";
								$res=mysql_query($q,$conex);
								$num2=mysql_num_rows($res);
								$row2 = mysql_fetch_row($res);

								echo "<td align = center ><font size=2  face='arial'>".$row2[0]."</font></td>";
								$totCausaA=$totCausaA+$row2[0];
								$cmes['agrado'][$j]=$cmes['agrado'][$j]+$row2[0];

								// Contar comentarios de desagrado
								$q="Select count(*) from ".$empresa."_000017 A, ".$empresa."_000018 B where A.Ccofori between '".$date1."'  and '".$date2."' and B.id_comentario=A.id and substring_index(A.ccoent,'-',1)='".$row[2]."' and B.cmotip='Desagrado'   ";
								$res=mysql_query($q,$conex);
								$num2=mysql_num_rows($res);
								$row3 = mysql_fetch_row($res);

								echo "<td align = center ><font size=2  face='arial'>".$row3[0]."</font></td>";
								$totCausaB=$totCausaB+$row3[0];
								$cmes['desagrado'][$j]=$cmes['desagrado'][$j]+$row3[0];

								$causaMes=$row2[0]+$row3[0];

								echo "<td align = center ><font size=2  face='arial'>".	$causaMes."</font></td>";
							}
							$total=$totCausaA+$totCausaB;
							$parti=$cmes['agrado'][$nmeses]+$cmes['desagrado'][$nmeses];
							echo "<td align = center ><font size=2  face='arial'>".	promedio2($causaMes,$parti)."%</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".	promedio2($row2[0],$causaMes)."%</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".	$totCausaA."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".	$totCausaB."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".	$total."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".	promedio2($total,$comentarios)."%</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".	promedio2($totCausaA,$total)."%</font></td>";

							echo "<tr>";
						}
						echo "<tr>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>Total</font></td>";

						for($j=1;$j<=$nmeses;$j++)
						{
							$totmes=$cmes['agrado'][$j]+$cmes['desagrado'][$j];
							echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".$cmes['agrado'][$j]."</font></td>";
							echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".$cmes['desagrado'][$j]."</font></td>";
							echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".$totmes."</font></td>";
							$totagrado    = $totagrado + $cmes['agrado'][$j];
							$totdesagrado = $totdesagrado + $cmes['desagrado'][$j];
							$totgeneral   = $totgeneral+$totmes;
						}
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>&nbsp</font></td>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>&nbsp</font></td>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>  ".$totagrado."</font></td>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>  ".$totdesagrado."</font></td>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>  ".$totgeneral."</font></td>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>  100%</font></td>";
						echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>  ".promedio2($agrado,$comentarios)."%</font></td>";
						echo "</tr>";

					}
					echo "</table></br>";

					break;

		 			case'11':

					//hago las consultas para AAA
					$agrado=0;
					$desagrado=0;
					$agrado2=0;
					$desagrado2=0;

					for($j=1;$j<=$nmeses;$j++)
					{
						$cmes['agrado'][$j]=0;
						$cmes['desagrado'][$j]=0;
						$cmes['agrado2'][$j]=0;
						$cmes['desagrado2'][$j]=0;
						$cmes['total'][$j]=0;
						$cmes['total2'][$j]=0;


						if ($j<10)
						{
							$date1=$ano."-0".$j."-01";
							$date2=$ano."-0".$j."-31";
							$date3=$ano2."-0".$j."-01";
							$date4=$ano2."-0".$j."-31";
						}else
						{
							$date1=$ano."-".$j."-01";
							$date2=$ano."-".$j."-31";
							$date3=$ano2."-".$j."-01";
							$date4=$ano2."-".$j."-31";
						}

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.cmotip='Agrado' and  id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)  and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-AAA-1' ";
						$err=mysql_query($query,$conex);
						$num1=mysql_num_rows($err);
						$cmes['agrado'][$j]=$cmes['agrado'][$j]+$num1; //numero de comentarios de agrado del mes

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.cmotip='Desagrado' and   id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)   and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-AAA-1' ";
						$err=mysql_query($query,$conex);
						$num2=mysql_num_rows($err);
						$cmes['desagrado'][$j]=$cmes['desagrado'][$j]+$num2; //numero de comentarios de desagrado del mes

						$cmes['total'][$j]=$cmes['total'][$j]+$num1+$num2;

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id  and B.cmotip='Agrado' AND id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)   and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-AAA-1'  ";
						$err=mysql_query($query,$conex);
						$num3=mysql_num_rows($err);
						$cmes['agrado2'][$j]=$cmes['agrado2'][$j]+$num3; //numero de comentarios de agrado del mes


						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id  and B.cmotip='Desagrado' AND id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)   and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-AAA-1' ";
						$err=mysql_query($query,$conex);
						$num4=mysql_num_rows($err);
						$cmes['desagrado2'][$j]=$cmes['desagrado2'][$j]+$num4; //numero de comentarios de desagrado del mes

						$cmes['total2'][$j]=$cmes['total2'][$j]+$num3+$num4;

						//numero de comentarios de agrado y desagrado
						$agrado=$agrado+$cmes['agrado'][$j];
						$desagrado=$desagrado+$cmes['desagrado'][$j];
						$agrado2=$agrado2+$cmes['agrado2'][$j];
						$desagrado2=$desagrado2+$cmes['desagrado2'][$j];

					}

					//numero total de comentarios
					$comentariosA=$agrado+$desagrado;
					$comentarios2A=$agrado2+$desagrado2;

					/******************************************************************************/
					// grafico segundo indicador: 2. Comentarios Positivos y por Mejorar 2005 2006 - Nivel de Satisfaccion Comentarios y Sugerencias

					echo "<font size=2  color='#00008B' face='arial'><b>2. Comentarios AFINIDAD</font></b></br></br>";

					echo "<center><font size=2  color='#00008B' face='arial' align='center'><b>11.1 Comentarios AAA</font></b></center></br></br>";
					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='5' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='5' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";
					echo "<td align = center  colspan='3'><font size=2  face='arial'>&nbsp;</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center rowspan=2><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2  face='arial'>Positivos</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2 face='arial'>Por Mejorar</font></td>";
					echo "<td align = center   rowspan=2 bgcolor='#ff00cc'> <font size=2  face='arial'>Total</font></td>";
					echo "<td align = center colspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Positvos</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>Por Mejorar</font></td>";
					echo "<td align = center  rowspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Total</font></td>";
					echo "<td align = center   rowspan=2 bgcolor='#ff9933'><font size=2 face='arial'>Variacion Positivos</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#ff9933' > <font size=2  face='arial'>Variacion por Mejorar</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#ff9933'><font size=2  face='arial'>Variacion Total</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";

					echo "</tr>";

					for($i=1;$i<=$nmeses;$i++)
					{
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['agrado2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['agrado2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['desagrado2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['desagrado2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['total2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['agrado'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['agrado'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['desagrado'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['desagrado'][$i], $cmes['total'][$i])."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['total'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio ($cmes['agrado'][$i], $cmes['agrado2'][$i])."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".promedio ($cmes['desagrado'][$i], $cmes['desagrado2'][$i])."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio ($cmes['total'][$i], $cmes['total2'][$i])."%</font></td>";

						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$agrado2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($agrado2, $comentarios2A) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$desagrado2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($desagrado2, $comentarios2A) ."%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial' >".$comentarios2A."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".$agrado."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($agrado, $comentariosA) ."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$desagrado."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($desagrado, $comentariosA)."%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".$comentariosA."</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".promedio ($agrado, $agrado2)."%</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>".promedio ($desagrado, $desagrado2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff9933'><font size=2  face='arial'>".promedio ($comentariosA, $comentarios2A)."%</font></td>";

					echo "</tr></table></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td>Positivos</td>';
					echo "<td align='center'>".$cmes['agrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['agrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Por mejorar</td>';
					echo "<td align='center'>".$cmes['desagrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['desagrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Positivos</td>';
					echo "<td>".promedio2 ($cmes['agrado2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td>".promedio2 ($cmes['agrado'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Por mejorar</td>';
					echo "<td>".promedio2 ($cmes['desagrado2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td>".promedio2 ($cmes['desagrado'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";

					echo "</table></br></br>";


					//hago las consultas para BBB
					$agrado=0;
					$desagrado=0;
					$agrado2=0;
					$desagrado2=0;

					for($j=1;$j<=$nmeses;$j++)
					{

						$cmes['agrado'][$j]=0;
						$cmes['desagrado'][$j]=0;
						$cmes['agrado2'][$j]=0;
						$cmes['desagrado2'][$j]=0;
						$cmes['total'][$j]=0;
						$cmes['total2'][$j]=0;

						if ($j<10)
						{
							$date1=$ano."-0".$j."-01";
							$date2=$ano."-0".$j."-31";
							$date3=$ano2."-0".$j."-01";
							$date4=$ano2."-0".$j."-31";
						}else
						{
							$date1=$ano."-".$j."-01";
							$date2=$ano."-".$j."-31";
							$date3=$ano2."-".$j."-01";
							$date4=$ano2."-".$j."-31";
						}

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.cmotip='Agrado' and id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)  and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-BBB-1' ";
						$err=mysql_query($query,$conex);
						$num1=mysql_num_rows($err);
						$unidad[$i.'agrado'][$j]= $num1; //numero de comentarios de agrado por unidad por mes
						$cmes['agrado'][$j]=$cmes['agrado'][$j]+$num1; //numero de comentarios de agrado del mes

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori  between '".$date1."'  and '".$date2."'  and B.id_comentario =A.id and B.cmotip='Desagrado' and id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)  and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-BBB-1' ";
						$err=mysql_query($query,$conex);
						$num2=mysql_num_rows($err);
						$unidad[$i.'desagrado'][$j]= $num2; //numero de comentarios de agrado por unidad por mes
						$cmes['desagrado'][$j]=$cmes['desagrado'][$j]+$num2; //numero de comentarios de desagrado del mes

						$cmes['total'][$j]=$cmes['total'][$j]+$num1+$num2;

						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori  between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id  and B.cmotip='Agrado' and id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)  and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-BBB-1'  ";
						$err=mysql_query($query,$conex);
						$num3=mysql_num_rows($err);
						$unidad[$i.'agrado2'][$j]= $num3;  //numero de comentarios de desagrado por unidad por mes
						$cmes['agrado2'][$j]=$cmes['agrado2'][$j]+$num3; //numero de comentarios de agrado del mes


						$query ="SELECT A.id, A.ccofori, B.id ";
						$query= $query. "FROM " .$empresa."_000017 A, " .$empresa."_000018 B, ".$empresa."_000016 C, ".$empresa."_000008 D where A.Ccofori between '".$date3."'  and '".$date4."'  and B.id_comentario =A.id  and B.cmotip='Desagrado' and id_persona=CONCAT(C.Cpedoc, '-', C.Cpetdoc)  and C.cpedoc=D.clidoc and C.cpetdoc=D.clitid and D.clitip='AFIN-BBB-1' ";
						$err=mysql_query($query,$conex);
						$num4=mysql_num_rows($err);
						$unidad[$i.'desagrado2'][$j]= $num4;  //numero de comentarios de desagrado por unidad por mes
						$cmes['desagrado2'][$j]=$cmes['desagrado2'][$j]+$num4; //numero de comentarios de desagrado del mes

						$cmes['total2'][$j]=$cmes['total2'][$j]+$num3+$num4;

						//numero de comentarios de agrado y desagrado
						$agrado=$agrado+$cmes['agrado'][$j];
						$desagrado=$desagrado+$cmes['desagrado'][$j];
						$agrado2=$agrado2+$cmes['agrado2'][$j];
						$desagrado2=$desagrado2+$cmes['desagrado2'][$j];

					}

					//numero total de comentarios
					$comentariosB=$agrado+$desagrado;
					$comentarios2B=$agrado2+$desagrado2;


					/******************************************************************************/
					// grafico segundo indicador: 2. Comentarios Positivos y por Mejorar 2005 2006 - Nivel de Satisfaccion Comentarios y Sugerencias


					echo "<center><font size=2  color='#00008B' face='arial' align='center'><b>11.2 Comentarios BBB</font></b></center></br></br>";
					echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";

					echo "<tr>";
					echo "<td>&nbsp;</td>";
					echo "<td align = center colspan='5' bgcolor='#ff00cc'><font size=2  face='arial'>".$ano2."</font></td>";
					echo "<td align = center colspan='5' bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>".$ano."</font></td>";
					echo "<td align = center  colspan='3'><font size=2  face='arial'>&nbsp;</font></td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align = center rowspan=2><font size=2  face='arial'>Mes</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2  face='arial'>Positivos</font></td>";
					echo "<td align = center colspan=2 bgcolor='#ff00cc'><font size=2 face='arial'>Por Mejorar</font></td>";
					echo "<td align = center   rowspan=2 bgcolor='#ff00cc'> <font size=2  face='arial'>Total</font></td>";
					echo "<td align = center colspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Positvos</font></td>";
					echo "<td align = center colspan=2   bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>Por Mejorar</font></td>";
					echo "<td align = center  rowspan=2  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>Total</font></td>";
					echo "<td align = center   rowspan=2 bgcolor='#ff9933'><font size=2 face='arial'>Variacion Positivos</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#ff9933' > <font size=2  face='arial'>Variacion por Mejorar</font></td>";
					echo "<td align = center  rowspan=2 bgcolor='#ff9933'><font size=2  face='arial'>Variacion Total</font></td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td align = center  bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>#</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>#</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'> #</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2 face='arial' color='#ffffff'>%</font></td>";

					echo "</tr>";

					for($i=1;$i<=$nmeses;$i++)
					{
						echo "<tr>";
						echo "<td align = center ><font size=2  face='arial'>".nombre_mes($i)."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['agrado2'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['agrado2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['desagrado2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['desagrado2'][$i], $cmes['total2'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['total2'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['agrado'][$i]."</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio2 ($cmes['agrado'][$i], $cmes['total'][$i]) ."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$cmes['desagrado'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio2 ($cmes['desagrado'][$i], $cmes['total'][$i])."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".$cmes['total'][$i]."</font></td>";
						echo "<td align = center ><font size=2 face='arial'>".promedio ($cmes['agrado'][$i], $cmes['agrado2'][$i])."%</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".promedio ($cmes['desagrado'][$i], $cmes['desagrado2'][$i])."%</font></td>";
						echo "<td align = center  ><font size=2  face='arial'>".promedio ($cmes['total'][$i], $cmes['total2'][$i])."%</font></td>";

						echo "</tr>";

					}

					echo "<tr>";
					echo "<td align = center ><font size=2  face='arial'>Total</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2  face='arial'>".$agrado2."</font></td>";
					echo "<td align = center bgcolor='#ff99cc'><font size=2 face='arial'>".promedio2 ($agrado2, $comentarios2B) ."%</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".$desagrado2."</font></td>";
					echo "<td align = center  bgcolor='#ff99cc'><font size=2  face='arial'>".promedio2 ($desagrado2, $comentarios2B) ."%</font></td>";
					echo "<td align = center bgcolor='#ff00cc'><font size=2  face='arial' >".$comentarios2B."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".$agrado."</font></td>";
					echo "<td align = center  bgcolor='#99ffff'><font size=2  face='arial'>".promedio2 ($agrado, $comentariosB) ."%</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2  face='arial'>".$desagrado."</font></td>";
					echo "<td align = center bgcolor='#99ffff'><font size=2 face='arial'>".promedio2 ($desagrado, $comentariosB)."%</font></td>";
					echo "<td align = center  bgcolor='#0000ff'><font size=2  face='arial' color='#ffffff'>".$comentariosB."</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2 face='arial'>".promedio ($agrado, $agrado2)."%</font></td>";
					echo "<td align = center bgcolor='#ffcc33'><font size=2  face='arial'>".promedio ($desagrado, $desagrado2)."%</font></td>";
					echo "<td align = center  bgcolor='#ff9933'><font size=2  face='arial'>".promedio ($comentariosB, $comentarios2B)."%</font></td>";

					echo "</tr></table></br>";

					echo "<center><font size=2  color='#00008B' face='arial'><b>".nombre_mes($mes)."</font></b></center></br>";
					echo'<table border="1" align="center" cellpadding="2" >';
					echo '<tr>';
					echo '<td></td>';
					echo '<td align=center>'.$ano2.'</td>';
					echo '<td align=center>'.$ano.'</td>';
					echo "</tr>";
					echo '<tr>';
					echo '<td>Positivos</td>';
					echo "<td align='center'>".$cmes['agrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['agrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Por mejorar</td>';
					echo "<td align='center'>".$cmes['desagrado2'][$nmeses]."</td>";
					echo "<td align='center'>".$cmes['desagrado'][$nmeses]."</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Positivos</td>';
					echo "<td>".promedio2 ($cmes['agrado2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td>".promedio2 ($cmes['agrado'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";
					echo '<tr>';
					echo '<td>Por mejorar</td>';
					echo "<td>".promedio2 ($cmes['desagrado2'][$nmeses], $cmes['total2'][$nmeses]) ."%</td>";
					echo "<td>".promedio2 ($cmes['desagrado'][$nmeses], $cmes['total'][$nmeses]) ."%</td>";
					echo "</tr>";

					echo "</table></br></br>";

					break;

					case '12':

					/************************************************************************************/
					//Indicador Comentarios x Lugar de Origen - Ingeniero Gustavo Avendaño Requerimiento # 777 por monica 2008-11-06

					$Fecha2=$ano."-01-01";
					$Fecha3=$ano."-".$nmeses."-31";
					echo "<font size=2  color='#00008B' face='arial'><b>12. Comentarios X Lugar de Origen</font></b></br></br>";

					$query="Select count(*), A.ccoori from ".$empresa."_000017 A where A.Ccofori between '".$Fecha2."'  and '".$Fecha3."' group by A.ccoori order by 1 desc";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

                    /************************************************************************************/
					// desgloce para hospitalizacion
					$pinta=0;

					for($y=0;$y<$numAre;$y++)
					{
						if (!isset($unidadS['id'][0]))
						{
							$exp=explode('-',$areaL['nombre'][$y]);
							if ($exp[1]=='Servicio Magenta' or $exp[1]=='Tercer piso')
							$pintar=2;
						}
					}

					if ($pintar==2) //pinto indicador de hospitalizacion por pisos
					{
						echo "<center><font size=2  color='#00008B' face='arial'><b>12. Comentarios x Lugar de Origen</font></b></center></br>";

						echo "<table Border=1 style='border:solid;border-color:#00008B;' align = center width='700'>";
						echo "<tr>";
						echo "<td align = center rowspan=2 ><font size=2  face='arial'>Lugar de origen</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  colspan=1><font size=2 face='arial'>".nombre_mes($j)."</font></td>";
							$totalMes[$j]=0;
							$totalMesP[$j]=0;
							$totalMesN[$j]=0;
						}
						echo "<td align = center  colspan=1><font size=2  face='arial'>Acumulado</font></td>";
						echo "</tr>";

						echo "<tr>";

						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center  ><font size=2 face='arial'>Total</font></td>";
						}

						echo "<td align = center  bgcolor='#99ccff' ><font size=2 face='arial'>Total</font></td>";
						echo "</tr>";

						$q="select A.ccoori  from magenta_000017 A where A.Ccofori between '".$ano."-01-01' and '".$ano."-".$nmeses."-31' group by A.ccoori";

						$err=mysql_query($q,$conex);
						$nlugares=mysql_num_rows($err);

						for($i=1;$i<=$nlugares;$i++)
						{
							$totalP=0;
							$totalN=0;
							$total=0;

							$row = mysql_fetch_row($err);
							echo "<td align = center><font size=2  face='arial'>".	$row[0]."</font></td>";
							for($j=1;$j<=$nmeses;$j++)
							{

								if ($j<10)
								{
									$date1=$ano."-0".$j."-01";
									$date2=$ano."-0".$j."-31";
								}else
								{
									$date1=$ano."-".$j."-01";
									$date2=$ano."-".$j."-31";
								}

								$q1="select * from magenta_000017 A where A.Ccofori between '".$date1."' and '".$date2."' and A.ccoori='".$row[0]."'";
								$res1=mysql_query($q1,$conex);
								$num1=mysql_num_rows($res1);

								echo "<td align = center ><font size=2  face='arial'>".$num1."</font></td>";
								/*
								echo "<td align = center ><font size=2  face='arial'>".$num2."</font></td>";
								echo "<td align = center ><font size=2  face='arial'>".$num3."</font></td>";
								*/
                                $totalMes[$j]=$totalMes[$j] + $num1;
                                $total=$total+$num1;
							}

							//$total=$num1;
							echo "<td align = center bgcolor='#99ccff' ><font size=2  face='arial'>".$total."</font></td>";
							/*echo "<td align = center ><font size=2  face='arial'>	".$totalN."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>	".$totalP."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$por."%</font></td>";
							*/
                            echo "<tr>";
						}
						$acumuP=0;
						$acumuN=0;
						$acumu=0;
						echo "<tr>";
						echo "<td align = center rowspan=2 ><font size=2  face='arial'>Total General</font></td>";
						for($j=1;$j<=$nmeses;$j++)
						{
							echo "<td align = center ><font size=2  face='arial'>".$totalMes[$j]."</font></td>";
							/*
							echo "<td align = center ><font size=2  face='arial'>".$totalMesN[$j]."</font></td>";
							echo "<td align = center ><font size=2  face='arial'>".$totalMesP[$j]."</font></td>";
                            */
							$acumuP=$acumuP+$totalMes[$j];

						}

						$acumu=$acumuP;

						echo "<td align = center bgcolor='#99ccff' ><font size=2  face='arial'>".$acumu."</font></td>";
						/*
						echo "<td align = center ><font size=2  face='arial'>	".$acumuN."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>	".$acumuP."</font></td>";
						echo "<td align = center ><font size=2  face='arial'>".$por."%</font></td>";
                        */
						echo "<tr>";

						echo "</table><br>";
					}


					break;

				}
			}
		}

	}else
	{

		echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
		echo "<tr><td colspan='2' ALIGN='center'><font size=3 color='#000080' face='arial'><b>EL USUARIO ESTA INACTIVO O NO TIENE AREAS ASIGNADAS PARA CONSULTAR EL INFORME</td><tr>";
		echo "</table>";
	}
}

?>

</body>

</html>