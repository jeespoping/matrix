<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
<script src="efecto.php"></script>
<script>
        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
</script>

<!-- Loading language definition file -->
     <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo6{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo7{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    </style>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
</SCRIPT>

<script type="text/javascript">
$(document).ready(function() {
	$("#fecOri, #fecRec").datepicker({
       showOn: "button",
       buttonImage: "../../images/medical/root/calendar.gif",
       buttonImageOnly: true,
       maxDate:"+1D"
    });
});  


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

function ajaxquery(fila, entrada, opeAnt)
{
	var x = new Array();

	//me indica que hacer con el id, segun tipo de entrada

	switch(entrada)
	{

		case "1": //arreglar drop down lugar de origen
		x[1] = opeAnt;
		x[2]=document.tabla1.lugOri.value;
		x[3]=document.tabla1.ent.value;
		x[4]=document.tabla1.his.value;
		x[5]=document.tabla1.res.value;
		x[6]=document.tabla1.ser.value;
		st="wope="+x[1]+"&lugOri="+x[2]+"&ent="+x[3]+"&his="+x[4]+"&res="+x[5]+"&ser="+x[6];
		break;

		case "2": //arreglar drop down entidad
		x[1] = opeAnt;
		x[2] = document.forms['resultados['+fila+']'].elements['motCau['+fila+']'].value;
		x[3] = document.forms['resultados['+fila+']'].elements['motEst['+fila+']'].value;
		x[4] = document.forms['resultados['+fila+']'].elements['motInv['+fila+']'].value;
		x[5] = document.forms['resultados['+fila+']'].elements['motCla['+fila+']'].value;
		x[6] = document.forms['resultados['+fila+']'].elements['motTip['+fila+']'].value;
		x[7] = document.forms['resultados['+fila+']'].elements['motDes['+fila+']'].value;
		x[8] = document.forms['resultados['+fila+']'].elements['motNum['+fila+']'].value;
		st="wope="+x[1]+"&wcau="+x[2]+"&west="+x[3]+"&winv="+x[4]+"&wcla="+x[5]+"&wtip="+x[6]+"&wdes="+x[7]+"&wnum="+x[8]+"&wid="+fila;
		break;

		case "3": //drop down y seleccion de causa
		x[1] = opeAnt;
		x[2] = document.forms['resultados['+fila+']'].elements['motCau['+fila+']'].value;
		x[3] = document.forms['resultados['+fila+']'].elements['motEst['+fila+']'].value;
		x[4] = document.forms['resultados['+fila+']'].elements['motInv['+fila+']'].value;
		x[5] = document.forms['resultados['+fila+']'].elements['motCla['+fila+']'].value;
		x[6] = document.forms['resultados['+fila+']'].elements['motTip['+fila+']'].value;
		x[7] = document.forms['resultados['+fila+']'].elements['motDes['+fila+']'].value;
		x[8] = document.forms['resultados['+fila+']'].elements['motNum['+fila+']'].value;
		st="wope="+x[1]+"&west="+x[3]+"&winv="+x[4]+"&wcla="+x[5]+"&wtip="+x[6]+"&wdes="+x[7]+"&wnum="+x[8]+"&wid="+fila;

		break;
	}


	ajax=nuevoAjax();
	ajax.open("POST", st, true);
	ajax.open("POST", "detalleComentario.php",true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(st);


	ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
			if(ajax.status==200){
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


function noenter() {
	return !(window.event && window.event.keyCode == 13);
}

function guardar()
{
	alert('La informacion se almacenara');
	document.enviar.lugOri.value=document.tabla1.lugOri.value;
	document.enviar.ent.value=document.tabla1.ent.value;
	document.enviar.fecOri.value=document.tabla2.fecOri.value;
	document.enviar.fecRec.value=document.tabla2.fecRec.value;
	for (var j=0; j < document.tabla2.vol.length; j++)
	{
		if (document.tabla2.vol[j].checked)
		{
			document.enviar.vol.value = document.tabla2.vol[j].value;
		}
	}

	for (var j=0; j < document.tabla2.perDil.length; j++)
	{
		if (document.tabla2.perDil[j].checked)
		{
			document.enviar.perDil.value = document.tabla2.perDil[j].value;
		}
	}

	document.enviar.acoNom.value=document.tabla2.acoNom.value;
	document.enviar.acoTel.value=document.tabla2.acoTel.value;
	document.enviar.acoDir.value=document.tabla2.acoDir.value;
	document.enviar.acoEma.value=document.tabla2.acoEma.value;
	document.enviar.emo.value=document.tabla2.emo.value;
	document.enviar.comEst.value=document.tabla2.comEst.value;
	document.enviar.idPac.value=document.tabla2.idPac.value;
	document.enviar.aut.value=document.tabla2.aut.value;
	document.enviar.doc.value=document.tabla2.doc.value;
	document.enviar.tipDoc.value=document.tabla2.tipDoc.value;
	document.enviar.tel.value=document.tabla2.tel.value;
	document.enviar.dir.value=document.tabla2.dir.value;
	document.enviar.ema.value=document.tabla2.ema.value;
	document.enviar.priNom.value=document.tabla2.priNom.value;
	document.enviar.segNom.value=document.tabla2.segNom.value;
	document.enviar.priApe.value=document.tabla2.priApe.value;
	document.enviar.segApe.value=document.tabla2.segApe.value;
	document.enviar.tamaini.value=document.tabla2.tamaini.value;
	document.enviar.his.value=document.tabla1.his.value;
	document.enviar.ser.value=document.tabla1.ser.value;
	document.enviar.res.value=document.tabla1.res.value;

	for (var i=0;i<=document.enviar.tamano.value;i++)
	{
		document.enviar.elements['motEst['+i+']'].value = document.forms['resultados['+i+']'].elements['motEst['+i+']'].value;
		document.enviar.elements['motInv['+i+']'].value = document.forms['resultados['+i+']'].elements['motInv['+i+']'].value;
		document.enviar.elements['motCla['+i+']'].value= document.forms['resultados['+i+']'].elements['motCla['+i+']'].value;
		document.enviar.elements['motTip['+i+']'].value = document.forms['resultados['+i+']'].elements['motTip['+i+']'].value;
		document.enviar.elements['motDes['+i+']'].value = document.forms['resultados['+i+']'].elements['motDes['+i+']'].value;
		document.enviar.elements['motNum['+i+']'].value = document.forms['resultados['+i+']'].elements['motNum['+i+']'].value;
		document.enviar.elements['motCau['+i+']'].value = document.forms['resultados['+i+']'].elements['motCau['+i+']'].value;
	}

	document.enviar.submit();
}

//-->

</script>
</head>
<body>
<?php
include_once("conex.php"); 

/**
 * INGRESO DE COMENTARIO
 * 
 * Este programa permite ingresar un comentario y sus motivos al igual que leerlo en cualquier momento
 * 
 * @name  matrix\magenta\procesos\detalleComentario.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-03
 * @version 2007-01-29
 * 
 * @modified 2006-04-20  Mejora de la interfaz de usuario
 * @modified 2007-01-04 Se realiza documentacion del programa y se adapta para que muestre la historia clinica y reciba de otros programas el servicio y la entidad
 * @modified 2007-04-29 Se adapata funcion guardar de javascript para que no haya limite de palabras, comentando el caso 4 d ela funcion de ajax
 
 Actualizacion: Se revisa el script, se encuentra que el calendario solo funciona en internet explorer Viviana Rodas Fecha 14-05-2012
 * 
 * @table magenta_000008, select
 * @table magenta_000016, select sobre datos del paciente
 * @table magenta_000017, select, insert, update 
 * @table magenta_000018, select, insert, update
 * @table magenta_000019, select
 * @table magenta_000024, select
 * @table magenta_000025, select
 * @table usuarios, select sobre el nombre del usuario
 * 
 *  @var $acoDir, direccion del acompanana
 *  @var $acoEma, email del acompanante
 *  @var $acoNom, nombre del acompananate
 *  @var $acoTel, telefono del acompanante
 *  @var $aut, persona que ingreso el comentario
 *  @var $cau lista de causas para desplegar en drop down
 *  @var $color color para desplegar el tipo de usuario que es el paciente
 *  @var $comEst estado del comentario
 *  @var $dir direccion del paciente
 *  @var $doc documento del paciente
 *  @var $ema email del paciente
 *  @var $emo contacto emocional
 *  @var $ent entidad del paciente
 *  @var $entS lista de entidades para el drop down
 *  @var $exp para utilizar con la funcion explode
 *  @var $fecOri fecha de origen del comentario
 *  @var $fecRec fecha de recepcion del comentario
 *  @var $his historia clinica del paciente
 *  @var $idCom id del comentario en tabla 000017
 *  @var $idPac id del paciente en tabla 000016
 *  @var $inicial, recibe la cantidad de motivos para recorrerlos en un for
 *  @var $lugOri lugar de origen del comentario
 *  @var $lugOriS lista de lugares de origen para el drop down
 *  @var $motCau vector de causas de los motivos
 *  @var $motCla vector de clasificaciones de los motivos
 *  @var $motDes vector de descripciones de los motivos
 *  @var $motEst vector de estados de los motivos
 *  @var $motInv vector de investigaciones de los motivos
 *  @var $motNum vector de numero de los motivos
 *  @var $motTip vector de tipo de los motivos
 *  @var $numCom numero del comentario
 *  @var $perDil persona que dilignecio el comentario
 *  @var $priApe primer apellido del paciente
 *  @var $priNom primer nombre del paciente
 *  @var $res responsable por el ultimo ingreso del paciente
 *  @var $respuesta recibe el tipo de usuario del paciente
 *  @var $segApe segundo apellido del paciente
 *  @var $segNom segundo nombre del paciente
 *  @var $senal indica si el comentario no existe o no ha sido guardado (1)
 *  @var $ser ultimo servicio en que estuvo el paciente
 *  @var $tamaini cantidad de motivos del comentario mas 1
 *  @var $tamano cantidad de motivos del comentario
 *  @var $tel telefono del paciente
 *  @var $tipDoc tipo de documento del paciente
 *  @var $tipUsu tipo de usuario para afinidad (AAA, BBB, VIP, no clasificado)
 *  @var $vol volveria o no a la clinica
 *  @var $wcau parte de la cauas de un motivo para pasar a ajax
 *  @var $wcla clasificacion del motivo para pasar a ajax
 *  @var $wdes descripcion del motivo para pasar a ajax
 * 	@var $west estado del motivo pasado por ajax
 *  @var $wid ide del comentario pasado por ajax
 *  @var $winv investigacion del comentario pasado por ajax
 *  @var $wnum numero del motivo pasado por ajax
 *  @var $wope indica cual es la operacion que el usuario desea hacer (llenar un drop down o guardar)
 *  @var $wtip tipo de motivo pasdo por ajax
*/
//=================================================================================================================================

//////////////////////////////////////////////funciones php//////////////////////////////////////////////

/**
 * funcion para pintar el encabezadoscript del programa
 *
 * @param unknown_type $doc documento del paciente
 * @param unknown_type $tipDoc tipo de documento del paciente
 * @param unknown_type $priNom primer nombre del paciente
 * @param unknown_type $segNom segundo nombre del paciente
 * @param unknown_type $priApe primer apellido del paciente
 * @param unknown_type $segApe segundo apellido del paciente
 * @param unknown_type $dir direccion del paciente
 * @param unknown_type $tel telefono del paciente
 * @param unknown_type $ema email del paciente
 */

/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-03 (Arleyda Insignares C.)
 						-Se Modifica los campos de calendario fecha de generacion y fecha de recepcion con utilidad jquery 
						 y se elimina uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado con ultimo diseño y se configura color de titulos con la clase 'fila1'
*************************************************************************************************************************/

function encabezadoscript($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $his, $res, $ser)
{

	$wautor="Carolina Castano P.";
	$wversion='2007-01-29';

	//elaboramos el menu de opciones
	$dir2=urlencode($dir);
	$cadena="'pagina1.php?matrix=".$doc."-".$tipDoc."&bandera=3&his=".$his."&res=".$res."&ser=".$ser."'";
	$cadena2="'comentario.php?doc=".$doc."&tipDoc=".$tipDoc."&priNom=".$priNom."&segNom=".$segNom."&priApe=".$priApe."&segApe=".$segApe."&tel=".$tel."&dir=".$dir2."&ema=".$ema."&his=$his&res=$res&ser=$ser'";

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
	  	<?php
	  	echo 'mWhite1.addMenuItem("Datos del paciente", "self.window.location='.$cadena.'");';
	  	echo 'mWhite1.addMenuItem("Comentarios del paciente", "self.window.location='.$cadena2.'");';
	  	?>
	  	mWhite1.bgColor = "#ADD8E6";
	  	mWhite1.menuItemBgColor = "white";
	  	mWhite1.menuHiliteBgColor = "#336699";

	  	myMenu.writeMenus();
	    }
	  </SCRIPT>

	 <?php

/*	if (isset ($idCom))
		 echo "<center><b><font size=\"4\"><A HREF='detalleComentario.php?idCom=$idCom'><font color=\"#D02090\">CONSULTA E INGRESO DE COMENTARIOS POR PACIENTE</font></a></b></font></center>\n" ;
		 else
		 echo "<center><b><font size=\"4\"><A HREF='detalleComentario.php?doc=$doc&tipDoc=$tipDoc&priNom=$priNom&segNom=$segNom&priApe=$priApe&segApe=$segApe&dir=$dir2&tel=$tel&ema=$ema&orden=1&his=$his&res=$res&ser=$ser'><font color=\"#D02090\">CONSULTA E INGRESO DE COMENTARIOS POR PACIENTE</font></a></b></font></center>\n" ;
		 echo "<center><b><font size=\"2\"><font color=\"#D02090\"> detalleComentario.php</font></font></center></br></br></br>\n" ;
		 echo "\n" ;
*/	
       
}

/**
 * funcion para la presentacion de los datos del paciente
 *
 * @param unknown_type $doc documento de identidad del paciente
 * @param unknown_type $tipDoc tipo de documento de identidad
 * @param unknown_type $priNom primer nombre
 * @param unknown_type $segNom segundo nombre
 * @param unknown_type $priApe primer apellido
 * @param unknown_type $segApe segundo apellido
 * @param unknown_type $tel telefono
 * @param unknown_type $dir direccion
 * @param unknown_type $ema email
 * @param unknown_type $tipUsu tipo de usuario
 * @param unknown_type $color color del usuario segun afinidad
 */

function datosPaciente($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $tel, $dir, $ema, $tipUsu, $color)
{
	echo "<fieldset  style='border:solid;border-color:#00008B; width=100%' align='center'>";
	echo "<table align='center'  width='100%'>";
	echo "<tr>";
	echo "<td rowspan=4><b><font size='3'><font color='#00008B'> DATOS DEL PACIENTE:</font></font></td>";
	echo "<td> <b><font size='3'  color='#00008B'>Documento:</font></b>$doc-".substr($tipDoc,0,2)."</td>";

	echo "<td><b><font size='3'  color='#00008B'>Nombre:&nbsp;</font></b>$priNom ";

	if ($segNom != ' ' and $segNom != '' and  $segNom != '- -')
	echo "$segNom ";

	echo "$priApe ";

	if ($segApe != ' ' and $segApe != '' and  $segApe != '- -')
	echo "$segApe";

	echo "</td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td><b><font size='3'  color='#00008B'> Telefono: </font></b>$tel</td>";
	echo "<td><b><font size='3'  color='#00008B'>Direccion: </font></b>$dir</td>";
	echo "</tr>";
	echo "<tr>";

	echo "<td><b><font size='3'  color='#00008B'> Email: </font></b>$ema</td>";
	echo "<td><b><font size='3'  color='#00008B'>AFINIDAD:</font></b> <font color='#$color'>$tipUsu</font></td>";
	echo "</tr>";
	echo "</table></fieldset></br></br></br>";
}


/**
 * funcion de htm que contiene los primeros dos drop down, lugar de origen y entidad
 *
 * @param unknown_type $lugOri, lugar de origen o servicio
 * @param unknown_type $num1, tamano de la lista de lugares para desplegar
 * @param unknown_type $lugOriS vector o lista de lugares de origen o servicios
 * @param unknown_type $ent entidad del paciente
 * @param unknown_type $num2 tamano de la lista de entidades para desplegar
 * @param unknown_type $entS vector o lista de entidades para desplegar
 * @param unknown_type $his historia clinica
 */
function divx1($lugOri, $num1, $lugOriS, $ent, $num2, $entS, $his, $res, $ser)
{
	$fila='ajaxquery("x1","1", "1")';
	$fila2='ajaxquery("x1","1", "2")';

	echo "<div id='x1'>";
	echo "<form name='tabla1' >";

	echo "<input type='hidden' name='res' value='".trim($res)."' />";
	echo "<input type='hidden' name='ser' value='".trim($ser)."' />";

	echo "</br><table align='center'>";

	echo "<tr>";
	if ( $his== "-")
	{
		echo "<td  align='left'> HISTORIA CLINICA: <INPUT TYPE='text' NAME='his' VALUE='' size='10'> </td>";
	}else
	{
		echo "<td   align='left'>HISTORIA CLINICA: <INPUT TYPE='text' NAME='his' VALUE='".$his."' size='10'></td>";
	}
	echo "</tr>";

	echo "<tr><td><b><font size='3'>&nbsp;</font></font></td></tr>";

	echo "<tr>";
	if ( $lugOri != "" and $num1 >0)
	{
		echo "<td align='left' >LUGAR DE ORIGEN: <select name='lugOri'>".$lugOriS."</select><input type='button' name='envio1' value='...' Onclick='".$fila."' > ".$ser."</td>";
	}else
	{
		echo "<td align='left''>LUGAR DE ORIGEN: <input type='text' name='lugOri' value='".$lugOri."'  size=25 onkeypress='return noenter()' ><input type='button' name='envio1' value='...' Onclick='".$fila."' > ".$ser."</td>";
	}
	echo "</tr>";

	echo "<tr><td><b><font size='3'>&nbsp;</font></font></td></tr>";

	echo "<tr>";
	if ( $ent!= "" and $num2 >0)
	{
		echo "<td align='left' >ENTIDAD <select name='ent' >".$entS."</select><input type='button' name='envio2' value='...' Onclick='".$fila2."' > ".$res."</td></tr>";
	}else
	{
		echo "<td align='left' >ENTIDAD:<input type='text' name='ent' value='".$ent."'  size=25   onkeypress='return noenter()'><input type='button' name='envio1' value='...' Onclick='".$fila2."' size=10 maxlength=10> ".$res."</td></tr>";
	}
	echo "</tr>";
	echo "</table></BR>";


	echo "</form>";
	echo "</div>";
}
/**
 * funcion html que muestra los datos del acompañante y las fechas del comentario
 *
 * @param unknown_type $fecOri, fecha de origen
 * @param unknown_type $fecRec, fecha de recpcion
 * @param unknown_type $vol, volveria a la clinica
 * @param unknown_type $perDil, persona que diligencio el comentario
 * @param unknown_type $acoNom, nombre del acompañante
 * @param unknown_type $acoDir, direccion del acompañante
 * @param unknown_type $acoTel, telefono del acompañante
 * @param unknown_type $acoEma, email del acompañante
 * @param unknown_type $idPac, identificacion del paciente en la tabla 0000016
 * @param unknown_type $comEstm estado del comentario
 * @param unknown_type $emo, contacto emocional
 * @param unknown_type $aut, quien escribio el comentario
 * @param unknown_type $doc, documento del paciente
 * @param unknown_type $tipDoc, tipo de documento del paciente
 * @param unknown_type $priNom, primer nombre del paciente
 * @param unknown_type $segNom, segundo nombre del paciente
 * @param unknown_type $priApe, primer apellido del paciente
 * @param unknown_type $segApe, segundo apellido del paciente
 * @param unknown_type $dir, direccion del paciente
 * @param unknown_type $tel, telefono del paciente
 * @param unknown_type $ema, email del paciente
 * @param unknown_type $tamaini, cantidad de motivos del comentario
 */
function divx2($fecOri, $fecRec, $vol, $perDil, $acoNom, $acoDir, $acoTel, $acoEma, $idPac, $comEst, $emo, $aut, $doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $tamaini)
{
				echo "<div id='x1'>";
				echo "<form name='tabla2' >";
				echo "<table align='center'>";
				$cal="calendario('fecOri','1')";
				echo "<td  >FECHA DE GENERACION: <input type='text' readonly='readonly' id='fecOri' name='fecOri' value='".$fecOri."' class=tipo3 ></td>";
				echo "<td><b><font size='3'>&nbsp;</font></font></td>";
				echo "<td  >FECHA DE RECEPCION:: <input type='text' readonly='readonly' id='fecRec' name='fecRec' value='".$fecRec."' class=tipo3 ></td>";
				echo "</table></BR>";

				switch ($vol)
				{
					case 'SI':
					echo "<CENTER><b><font size='3'>VOLVERIA A LA CLINICA:</b></font>";
					echo "<input type='radio' name='vol' value='SI' checked > <font size='2'>SI&nbsp;";
					echo "<input type='radio' name='vol' value='NO' > <font size='2'>NO&nbsp;";
					echo "<input type='radio' name='vol' value='NO RESPONDE'><font size='2'>NO RESPONDE</CENTER></BR>";
					break;
					case 'NO':
					echo "<CENTER><b><font size='3'>VOLVERIA A LA CLINICA:</b></font>";
					echo "<input type='radio' name='vol' value='SI'> <font size='2'>SI&nbsp;";
					echo "<input type='radio' name='vol' value='NO' checked > <font size='2'>NO&nbsp;";
					echo "<input type='radio' name='vol' value='NO RESPONDE'><font size='2'>NO RESPONDE</CENTER></BR>";
					break;
					case 'NO RESPONDE':
					echo "<CENTER><b><font size='3'>VOLVERIA A LA CLINICA:</b></font>";
					echo "<input type='radio' name='vol' value='SI'> <font size='2'>SI&nbsp;";
					echo "<input type='radio' name='vol' value='NO' > <font size='2'>NO&nbsp;";
					echo "<input type='radio' name='vol' value='NO RESPONDE' checked ><font size='2'>NO RESPONDE</CENTER></BR>";
					break;
				}

				switch ($perDil)
				{
					case 'Usuario':
					echo "<CENTER><b><font size='3'>QUIEN DILIGENCIO EL COMENTARIO:</b></font>";
					echo "<input type='radio' name='perDil' value='Usuario' checked > <font size='2'>USUARIO&nbsp;";
					echo "<input type='radio' name='perDil' value='Acompanante' > <font size='2'>ACOMPANANTE&nbsp;";
					echo "<input type='radio' name='perDil' value='Empleado'><font size='2'>EMPLEADO</CENTER></BR>";
					break;
					case 'Acompanante':
					echo "<CENTER><b><font size='3'>QUIEN DILIGENCIO EL COMENTARIO:</b></font>";
					echo "<input type='radio' name='perDil' value='Usuario'> <font size='2'>USUARIO&nbsp;";
					echo "<input type='radio' name='perDil' value='Acompanante' checked > <font size='2'>ACOMPANANTE&nbsp;";
					echo "<input type='radio' name='perDil' value='Empleado'><font size='2'>EMPLEADO</CENTER></BR>";
					break;
					case 'Empleado':
					echo "<CENTER><b><font size='3'>QUIEN DILIGENCIO EL COMENTARIO:</b></font>";
					echo "<input type='radio' name='perDil' value='Usuario'> <font size='2'>USUARIO&nbsp;";
					echo "<input type='radio' name='perDil' value='Acompanante' > <font size='2'>ACOMPANANTE&nbsp;";
					echo "<input type='radio' name='perDil' value='Empleado' checked ><font size='2'>EMPLEADO</CENTER></BR>";
					break;
				}


				echo "<CENTER><b><font size='3'><font color='#00008B'>INFORMACION DEL ACOMPANANATE (EN CASO DE ACOMPANANTE)</b></font></font>";
				echo "<table align='center'>";
				echo "<tr>";
				echo "<td>NOMBRE: </td>";
				echo "<td><input type='text' name='acoNom' value='$acoNom' /></td>";
				echo "<td><b><font size='3'>&nbsp;</font></font></td>";
				echo "<td>DIRECCION:</td>";
				echo "<td><input type='text' name='acoDir' value='$acoDir' /></td>";
				echo"</tr>";
				echo "<tr>";
				echo "<td>TELEFONO: </td>";
				echo "<td><input type='text' name='acoTel' value='$acoTel' /></td>";
				echo "<td><b><font size='3'>&nbsp;</font></font></td>";
				echo "<td>EMAIL:</td>";
				echo "<td><input type='text' name='acoEma' value='$acoEma' /></td>";
				echo "</tr>";
				echo "</table></BR>";

				echo "<input type='hidden' name='idPac' value='$idPac' />";
				echo "<input type='hidden' name='emo' value='$emo' />";
				echo "<input type='hidden' name='comEst' value='$comEst' />";
				echo "<input type='hidden' name='aut' value='$aut' />";

				echo "<input type='hidden' name='doc' value='$doc' />";
				echo "<input type='hidden' name='tipDoc' value='$tipDoc' />";
				echo "<input type='hidden' name='priNom' value='$priNom' />";
				echo "<input type='hidden' name='segNom' value='$segNom' />";
				echo "<input type='hidden' name='priApe' value='$priApe' />";
				echo "<input type='hidden' name='segApe' value='$segApe' />";
				echo "<input type='hidden' name='dir' value='$dir' />";
				echo "<input type='hidden' name='tel' value='$tel' />";
				echo "<input type='hidden' name='ema' value='$ema' />";
				echo "<input type='hidden' name='tamaini' value='$tamaini' />";

				echo "</form>";
				echo "</div>";
}

/**
 * funcion htm que muestra los motivos y sus causas y descripciones
 *
 * @param unknown_type $i
 * @param unknown_type $motNum numero del motivo
 * @param unknown_type $motEst estado del motivo
 * @param unknown_type $motInv investigacion del motivo
 * @param unknown_type $motCla clasificacion del motivo
 * @param unknown_type $motCau causa del motivo
 * @param unknown_type $motDes descripcion del motivo
 * @param unknown_type $motTip tipo de motivo
 * @param unknown_type $num numero del motivo
 * @param unknown_type $cau drop down de causas del motivo
 */
function divx3($i,$motNum, $motEst, $motInv, $motCla, $motCau, $motDes, $motTip, $num, $cau)
{
	echo "<div id='".$i."'>";
	echo "<form name='resultados[".$i."]' action='detalleComentario.php' method=post>";
	$fila3='ajaxquery("'.$i.'","2", "3")';
	$fila4='ajaxquery("'.$i.'","3", "3")';

	echo "<CENTER><b><font size='3'><font color='#00008B'>MOTIVO NUMERO: ".$motNum." </b></font></font></BR></BR>";
	echo "<input type='hidden' name='motNum[".$i."]' value='".$motNum."' />";
	echo "<input type='hidden' name='motEst[".$i."]' value='".$motEst."' />";
	echo "<input type='hidden' name='motInv[".$i."]' value='".$motInv."' />";
	echo "<input type='hidden' name='motTip[".$i."]' value='".$motTip." '/>";
	echo "<input type='hidden' name='motCla[".$i."]' value='".$motCla."' />";
	if ( $cau!= "" and $num >0)
	{
		echo "<font size='3'>CAUSA DEL COMENTARIO <select name='motCau[".$i."]' onChange='".$fila3."'>".$cau."</select><input type='button' name='envio4' value='...' Onclick='".$fila4."' ></td></tr>";
	}else
	{
		echo "<font size='3'>CAUSA DEL COMENTARIO: <input type='text' name='motCau[".$i."]' value='".$motCau."' onkeypress='return noenter()' size=35><input type='button' name='envioC' value='...'  Onclick='".$fila3."' ></td></tr>";
	}
	echo "</br></br><center><font size='3'>CLASIFICACION: ".$motCla."  &nbsp;&nbsp;&nbsp;&nbsp;TIPO: ".$motTip."</font></center>";
	echo "</br></br><b><font size='3'>DESCRIPCION:</font>	</BR>";
	echo "<textarea rows='4' cols='50' name='motDes[".$i."]' style='font-family: Arial; font-size:14'>".$motDes."</textarea></BR></BR>";

	echo "</form>";
	echo "</div>";
}

/**
 * funcion html que muestra la opcion de guardar de la pagina e hipervinculos de adelante y atras
 *
 * @param unknown_type $doc, documento del paciente
 * @param unknown_type $tipDoc, tipo de documento del paciente
 * @param unknown_type $priNom, primer nombre del paciente
 * @param unknown_type $segNom, segundo nombre del paciente
 * @param unknown_type $priApe, primer apellido del paciente
 * @param unknown_type $segApe, segundo apellido del paciente
 * @param unknown_type $dir, direccion del paciente
 * @param unknown_type $tel, telefono del paciente
 * @param unknown_type $ema, email del paciente
 * @param unknown_type $tamano, cantidad de motivos del comentario
 * @param unknown_type $idCom, id del comentario en tabla 000017
 * @param unknown_type $his, historia del paciente
 * @param unknown_type $res, responsable aconsejado del paciente
 * @param unknown_type $ser, servicio en que estuvo el paciente aconsejado
 */
function divx4($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $tamano, $idCom, $his, $res, $ser, $numCom)
{
	echo "<center>";
	echo "<div id='x4'>";
	echo "<form name='enviar' action='detalleComentario.php' method=post>";
	$fila5='guardar()';
	echo "<input type='hidden' name='wope' value='4' />";
	echo "<input type='hidden' name='lugOri' value='' />";
	echo "<input type='hidden' name='ent' value='' />";
	echo "<input type='hidden' name='fecOri' value='' />";
	echo "<input type='hidden' name='fecRec' value='' />";
	echo "<input type='hidden' name='vol' value='' />";
	echo "<input type='hidden' name='perDil' value='' />";
	echo "<input type='hidden' name='acoNom' value='' />";
	echo "<input type='hidden' name='acoTel' value='' />";
	echo "<input type='hidden' name='acoDir' value='' />";
	echo "<input type='hidden' name='acoEma' value='' />";
	echo "<input type='hidden' name='emo' value='' />";
	echo "<input type='hidden' name='comEst' value='' />";
	echo "<input type='hidden' name='idPac' value='' />";
	echo "<input type='hidden' name='aut' value='' />";
	echo "<input type='hidden' name='doc' value='$' />";
	echo "<input type='hidden' name='tipDoc' value='' />";
	echo "<input type='hidden' name='tel' value='' />";
	echo "<input type='hidden' name='dir' value='' />";
	echo "<input type='hidden' name='ema' value='' />";
	echo "<input type='hidden' name='priNom' value='' />";
	echo "<input type='hidden' name='segNom' value='' />";
	echo "<input type='hidden' name='priApe' value='' />";
	echo "<input type='hidden' name='segApe' value='$' />";
	echo "<input type='hidden' name='tamaini' value='' />";
	echo "<input type='hidden' name='his' value='' />";
	echo "<input type='hidden' name='ser' value='' />";
	echo "<input type='hidden' name='res' value='' />";
	echo "<input type='hidden' name='idCom' value='$idCom' />";
	echo "<input type='hidden' name='numCom' value='$numCom' />";
	echo "<input type='hidden' name='tamano' value='$tamano' />";

	for ($i=0;$i<=$tamano;$i++)
	{
		echo "<input type='hidden' name='motEst[".$i."]' value='' />";
		echo "<input type='hidden' name='motInv[".$i."]' value='' />";
		echo "<input type='hidden' name='motCla[".$i."]' value='' />";
		echo "<input type='hidden' name='motTip[".$i."]' value='' />";
		echo "<input type='hidden' name='motDes[".$i."]' value='' />";
		echo "<input type='hidden' name='motNum[".$i."]' value='' />";
		echo "<input type='hidden' name='motCau[".$i."]' value='' />";
	}

	echo "<input type='button'  name='GUARDAR' value='GUARDAR' Onclick='".$fila5."'/></CENTER></BR>";

	echo "</form>";
    
	$dir2=urlencode($dir);
    echo "<center>";
	echo "<a href='pagina1.php?matrix=".$doc."-".$tipDoc."&bandera=3&his=".$his."&res=".$res."&ser=".$ser."' align='right'><<--Datos paciente</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	if (isset ($idCom) and $idCom!='')
	{
		echo "<a href='contacto.php?idCom=$idCom' align='right'>CONTACTO EMOCIONAL</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='asignacion.php?idCom=$idCom' align='right'>Asignacion-->></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	echo "</center>";
	echo "</div>";
	echo "</CENTER>";
}

/********************************funciones************************************/

/**
 * Error al consultar el nombre del usuario del sistema por el codigo
 *
 */

function  DisplayError1()
{
	echo '<script language="Javascript">';
	echo 'alert ("No se ha encontrado el nombre del usuario del sistema, intente más tarde o avise a Sistemas")';
	echo '</script>';

}

/**
 * Error al tratar de buscar un paciente por el documento
 *
 */
function  DisplayError2()
{
	echo '<script language="Javascript">';
	echo 'alert ("El paciente seleccionado no se encuentra en la base de datos de Matrix, intente más tarde o avise a Sistemas")';
	echo '</script>';

}

/*error al no encontra un comentario por su id*/
function  DisplayError3()
{
	echo '<script language="Javascript">';
	echo 'alert ("El comentario seleccionado no se encuentra en la base de datos de Matrix, intente más tarde o avise a Sistemas")';
	echo '</script>';

}

/**
 * Funcion que busca el tipo de usuario de afinidad que es el paciente (si es AAA, BBB o VIP)
 *
 * @param unknown_type $doc, documento del paciente
 * @param unknown_type $tipDoc, tipo de documento del paciente
 * @return unknown respuesta, compuesto por el tipo de usuario y el color separados por guion
 */
function  TipoUsuario($doc, $tipDoc)
{
	global $empresa;
	global $conex;

	//Busco en base de datos de afinidad el tipo de Usuario de la persona, para mostrar
	$query ="SELECT clitip FROM " .$empresa."_000008 where clidoc='$doc' and clitid='$tipDoc' ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0)
	{
		$row=mysql_fetch_row($err);

		$inicial = explode ('-',$row[0]);
		if ($inicial[2] == '1' and $inicial[0] != 'VIP')
		$color='EA198E';
		else
		$color='0000FF';

		if ($inicial[0] == 'VIP')
		$tipUsu=$inicial[0];
		else
		$tipUsu=$inicial[1];
	}else
	{
		$tipUsu='NO CLASIFICADO';
		$color='0000FF';
	}

	$respuesta = $tipUsu.'-'.$color;
	return $respuesta;
}
/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
   echo "error";
else
{
	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	include_once("root/comun.php");
	//Abrir conexiones
	
	
	

    $titulo    = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";
    $wactualiz = "2016-05-04";
	$empresa='magenta';
	
	/////////////////////////////////////////////////encabezado general///////////////////////////////////
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  
	echo "<br></br>";
    echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>CONSULTA E INGRESO DE COMENTARIOS</b></font></div></div></BR>";

	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	if (!isset($wope)) //primera vez que se abre la ventana
	{
		if (!isset ($idCom)) //el comentario es nuevo, aun no existe
		{
			$dir=urldecode($dir);
			$senal=1; //indica que no hay datos del comentario, es para ingresar un comentario nuevo

			//inicializo variables en vacio
			$lugOri='';
			$fecOri='';
			$fecRec='';
			$ent='';
			if ($his=='-')
			{
				$his='';
			}
			else
			{
				$his=$his;
			}
			$perDil='Usuario';
			$acoNom='';
			$acoDir='';
			$acoTel='';
			$acoEma='';
			$emo='NO';
			$comEst='INGRESADO';
			$tamano=0;
			$motCau[0]='';
			$motNum[0]=1;
			$motEst[0]='INGRESADO';
			$motInv[0]='';
			$motTip[0]='';
			$motCla[0]='';
			$motDes[0]='';
			$vol='SI';
			$idCom='';
			$numCom='';
			$tamaini=1; //cantidad de cuadros de motivos que se mostraran +1


			$inicial=strpos($user,"-");
			$aut=substr($user, $inicial+1, strlen($user));

			//busco el nombre de la persona conectada
			$query ="SELECT descripcion FROM usuarios where codigo='".$aut."' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores
			{
				$row=mysql_fetch_row($err);
				$aut=$row[0];
			}else
			{
				DisplayError1(); //no se encontro el nombre para el codigo
			}

			$idPac=trim($doc).'-'.$tipDoc;
		}

		if (isset ($idCom) and $idCom!='') //si me mandan el id del comentario, es decir este ya existe
		{
			//busco los daros del comentario
			$query ="SELECT id_persona, ccoori, ccofori, ccofrec, cconaco, ccotusu, ccoatel, ccoadir, ccoaema, ccoent, ccovol, ccoaut, ccoest, ccocemo, ccohis, cconum FROM " .$empresa."_000017 where Ccoid=".$idCom." ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num >0) //se llena los valores del comentario
			{
				$row=mysql_fetch_row($err);
				$idPac=$row[0];
				$lugOri=$row[1];
				$fecOri=$row[2];
				$fecRec=$row[3];
				$ent=$row[9];
				$perDil=$row[5];
				$acoNom=$row[4];
				$acoDir=$row[7];
				$acoTel=$row[6];
				$acoEma=$row[8];
				$vol=$row[10];
				$aut=$row[11];
				$comEst=$row[12];
				$emo=$row[13];
				$his=$row[14];
				$numCom=$row[15];
				$ser='';
				$res='';


				//busco los datos del paciente
				$exp=explode('-',$idPac);
				if(isset($exp[3]))
				{
					$exp[0]=$exp[0].'-'.$exp[1];
					$exp[1]=$exp[2];
					$exp[2]=$exp[3];
				}
				$query ="SELECT cpedoc, cpetdoc, cpeno1, cpeno2, cpeap1, cpeap2, cpedir, cpetel, cpeema FROM " .$empresa."_000016 where cpedoc='".$exp[0]."' and cpetdoc='".$exp[1]."-".$exp[2]."'";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores
				{
					$row=mysql_fetch_row($err);
					$doc=$row[0];
					$tipDoc=$row[1];
					$priNom=$row[2];
					$segNom=$row[3];
					$priApe=$row[4];
					$segApe=$row[5];
					$dir=$row[6];
					$tel=$row[7];
					$ema=$row[8];
				}else
				{
					//echo 'no se encontro el paciente con el documento dado';
					DisplayError2();
				}

				//busco los datos de los motivos para el comentario
				$query ="SELECT cmonum, cmotip, cmodes, cmocla, cmoest, cmocau, cmoinv FROM " .$empresa."_000018 where id_comentario=".$idCom." ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);

				//echo $query;
				if  ($num >0) //se llena los valores en un vector por motivo
				{
					$tamano = $num;
					for($i=0;$i<$num;$i++)
					{
						$row=mysql_fetch_row($err);
						$motNum[$i]=$row[0];

						if (!isset($bandera1) or $bandera1 !=2)
						$motTip[$i]=$row[1];
						$motDes[$i]=$row[2];
						$motCla[$i]=$row[3];
						$motEst[$i]=$row[4];
						$motCau[$i]=$row[5];
						$motInv[$i]=$row[6];
					}
					$motTip[$i]='';
					$motDes[$i]='';
					$motCla[$i]='';
					$motEst[$i]='INGRESADO';
					$motCau[$i]='';
					$motInv[$i]='';
					$motNum[$i]=$motNum[$i-1]+1;
					$tamaini=$tamano+1;
				}
			}else
			{
				DisplayError3();
			}

			$tam=$tamano-1;
		}


		//consulto el tipo de usuario
		$respuesta=TipoUsuario($doc, $tipDoc);
		$exp=explode('-',$respuesta);
		$tipUsu=$exp[0];
		$color=$exp[1];

		//pinto el html
		encabezadoscript($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $his, $res, $ser);
		datosPaciente($doc,$tipDoc, $priNom, $segNom, $priApe, $segApe, $tel, $dir, $ema, $tipUsu,$color);

		if (isset ($senal) or !isset($idCom))
		{
			echo "<center><b><font size='4'><font color='#00008B'>INGRESO DE COMENTARIO NUEVO</b></font></font></center></BR>";
		}
		else
		{
			echo "<center><b><font size='4'><font color='#00008B'>DATOS DEL COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";
			echo "<center><b><font size='3'><font color='#00008B'>PERSONA QUE INGRESO EL COMENTARIO: ".$aut."</b></font></font></center>";
		}
		//pinto los diferentes formularios
		divx1($lugOri, 0, '', $ent, 0, '', $his, $res, $ser);
		divx2($fecOri, $fecRec, $vol, $perDil, $acoNom, $acoDir, $acoTel, $acoEma, $idPac, $comEst, $emo, $aut, $doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $tamaini);
		for($i=0;$i<=$tamano;$i++)
		{
			divx3($i, $motNum[$i], $motEst[$i], $motInv[$i], $motCla[$i], $motCau[$i], $motDes[$i] , $motTip[$i], '', 0);
		}

		divx4($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $tamano, $idCom, $his, $res, $ser, $numCom);
	}
	else // se hace alguna de las peticiones ajax sobre el documento
	{
		//respuestas a llamadas del ajax segun el tercer parametro de la funcion ajax
		switch ($wope)
		{
			case '1': //lleno opciones de lugar de origen
			$query="select carcod, carnom  FROM " .$empresa."_000019 where carnom like '%".strtoupper($lugOri)."%' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			$lugOriS='';
			for($j=0;$j<$num;$j++)
			{
				$row=mysql_fetch_row($err);
				$lugOriS=$lugOriS."<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
			divx1($lugOri, $num, $lugOriS, $ent, 0, '', $his, $res, $ser);
			break;

			case '2':				//llenar opcion de entidad
			//Basados en $entT buscar el cargo en la tabla de cargos
			$query="select cempcod, cempnom  FROM " .$empresa."_000025 where cempnom like '%".strtoupper($ent)."%' and cempest='on'";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			$entS="";
			for($j=0;$j<$num;$j++)
			{
				$row=mysql_fetch_row($err);
				$entS=$entS."<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
			divx1($lugOri, 0, '', $ent, $num, $entS, $his, $res, $ser);
			break;

			case '3': //llenado de seleccion de drop down de causas y clasificacion y tipo de comentarios automaticos
			$cau="";
			if(isset($wcau) and $wcau!='')
			{
				$exp=explode ('-', $wcau);
				if (isset($exp[1]))
				{
					if (isset($exp[2]))
					{
						$wcla=$exp[3].'-'.$exp[4];
						$wtip=$exp[2];
					}
					$cau=$cau."<option value='".$exp[0]."-".$exp[1]."-".$wtip."-".$wcla."'>".$exp[0]."-".$exp[1]."</option>";
					$query = "SELECT caucod, caunom, cautip, caucla FROM " .$empresa."_000024 WHERE  caucod like '%".substr($exp[0],0,2)."%' and caunom<>'".$exp[1]."' and cauest='on' order by caucod";
				}else
				{
					$query = "SELECT caucod, caunom, cautip, caucla FROM " .$empresa."_000024 WHERE  caucod like '%".$wcau."%' and cauest='on' order by caucod";
				}
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
			}else
			{
				$num=0;
				$wcau='';
			}

			if  (isset ($num) and $num >0)
			{
				for($j=0;$j<$num;$j++)
				{
					$row=mysql_fetch_row($err);
					if ($j==0 and !isset($exp[1]))
					{
						$wcla=$row[3];
						$wtip=$row[2];
					}

					$cau=$cau."<option value='".$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."'>".$row[0]."-".$row[1]."</option>";

				}
			}
			divx3($wid, $wnum, $west, $winv, $wcla, $wcau, $wdes , $wtip, $num, $cau);

			break;

			case '4': //almacenar el comentario
			if ($acoNom == '')
			$acoNom = '- -';
			if ($acoDir == '')
			$acoDir = '- -';
			if ($acoTel == '')
			$acoTel = '- -';
			if ($acoEma == '')
			$acoEma= '- -';

			if (!isset($idCom) or $idCom=='')// es la primera vez que se guarda, almacenar el comentario
			{
				//primero busco el consecutivo del año
				$query ="SELECT Max(cconum) FROM " .$empresa."_000017 where fecha_data >= '".date('Y')."-01-01' and ccotom!='on' ";
				$err=mysql_query($query,$conex);
				$num=mysql_num_rows($err);
				//echo $query;
				if  ($num >0) //se llena los valores del comentario
				{
					$row=mysql_fetch_row($err);
					$numCom=$row[0]+1;
				}
				else
				{
					$numCom=1;
				}


				$query= " INSERT INTO  " .$empresa."_000017 (medico, Fecha_data, Hora_data, id_persona, ccoori, ccofori, ccofrec, cconaco, ccotusu, ccoatel, ccoadir, ccoaema, ccoent, ccovol, ccoaut, ccoest, ccocemo, ccohis, cconum, seguridad)";
				$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$idPac."', '".strtoupper($lugOri)."','".$fecOri."', '".$fecRec."','".strtoupper($acoNom)."','".$perDil."','".strtoupper($acoTel)."', '".strtoupper($acoDir)."', '".strtoupper($acoEma)."', '".strtoupper($ent)."','".strtoupper($vol)."','".strtoupper($aut)."','".strtoupper($comEst)."','".strtoupper($emo)."', '".strtoupper($his)."', '".$numCom."','A-magenta') ";
				//echo $query;
				$err=mysql_query($query,$conex);

				$idCom= mysql_insert_id($conex);
				
				$query="update " .$empresa."_000017 set Ccoid=id ";
				$query=$query." where id= ".$idCom."  ";
				//echo $query;
				$err=mysql_query($query,$conex);
				
				$exp=explode('-',$motCau[0] );

				$query= " INSERT INTO  " .$empresa."_000018 (medico, Fecha_data, Hora_data, id_comentario, cmonum, cmotip, cmodes, cmocla, cmoest, cmocau, seguridad)";
				$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$idCom.", ".$motNum[0].",'".$motTip[0]."', '".strtoupper($motDes[0])."','".$motCla[0]."', '".$motEst[0]."', '".$exp[0]."-".$exp[1]."', 'A-magenta') ";
				//echo $query;
				$err=mysql_query($query,$conex);

				$tamano++;

			}else if (isset($idCom) and $idCom!='') // ya se habia guardado, se hace update
			{

				$query="update " .$empresa."_000017 set ccoori='".strtoupper($lugOri)."', ccofori='".$fecOri."', ccofrec ='".$fecRec."', cconaco='".strtoupper($acoNom)."', ccotusu='".$perDil."', ccoatel='".strtoupper($acoTel)."', ccoadir='".strtoupper($acoDir)."', ccoaema='".strtoupper($acoEma)."', ccoent='".strtoupper($ent)."', ccovol='".strtoupper($vol)."', ccoaut='".strtoupper($aut)."', ccoest='".strtoupper($comEst)."', ccocemo='".strtoupper($emo)."', ccohis='".strtoupper($his)."' ";
				$query=$query." where Ccoid= ".$idCom."  ";
				//echo $query;
				$err=mysql_query($query,$conex);

				//hago update de todos los motivos menos del último, si el útimo motivo está vacio en
				//descripcion hago update de todos, si esta lleno hago insert del utimo

				$inicial=$tamano;//para hacer el for y poder incrementar el tamano

				for($i=0;$i<=$inicial;$i++)
				{
					$exp=explode('-',$motCau[$i] );

					if ($motDes[$i]!='' and $i==$inicial)//si el ultimo motivo esta lleno es nuevo
					{
						$query= " INSERT INTO  " .$empresa."_000018 (medico, Fecha_data, Hora_data, id_comentario, cmonum, cmotip, cmodes, cmocla, cmoest, cmocau,seguridad)";
						$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."',".$idCom.", ".$motNum[$i].",'".$motTip[$i]."', '".strtoupper($motDes[$i])."','".$motCla[$i]."','".$motEst[$i]."', '".$exp[0]."-".$exp[1]."', 'A-magenta') ";
						$err=mysql_query($query,$conex);
						$tamano++;

						//el comentario debe quedar en estado ingresado otra vez.
						$query="update " .$empresa."_000017 set ccoest='INGRESADO' ";
						$query=$query." where Ccoid= ".$idCom."  ";
						//echo $query;
						$err=mysql_query($query,$conex);

					}else if ($motDes[$i]!='' and $i !=$inicial)
					{

						$query="update " .$empresa."_000018 set cmotip='".$motTip[$i]."', cmodes='".strtoupper($motDes[$i])."', cmocla='".$motCla[$i]."', cmocau='".$exp[0]."-".$exp[1]."'  ";
						$query=$query."where id_comentario=".$idCom." and cmonum=".$motNum[$i]."  ";
						//echo $query;
						$err=mysql_query($query,$conex);
					}
				}

			}

			//consulto el tipo de usuario
			$respuesta=TipoUsuario($doc, $tipDoc);
			$exp=explode('-',$respuesta);
			$tipUsu=$exp[0];
			$color=$exp[1];

			//pinto el html
			encabezadoscript($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $his, $res, $ser);
			datosPaciente($doc,$tipDoc, $priNom, $segNom, $priApe, $segApe, $tel, $dir, $ema, $tipUsu,$color);

			if (isset ($senal) or !isset($idCom))
			{
				echo "<center><b><font size='4'><font color='#00008B'>INGRESO DE COMENTARIO NUEVO</b></font></font></center></BR>";
			}
			else
			{
				echo "<center><b><font size='4'><font color='#00008B'>DATOS DEL COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";
				echo "<center><b><font size='3'><font color='#00008B'>PERSONA QUE INGRESO EL COMENTARIO: ".$aut."</b></font></font></center>";
			}
			//pinto los diferentes formularios
			divx1($lugOri, 0, '', $ent, 0, '', $his, $res, $ser);
			divx2($fecOri, $fecRec, $vol, $perDil, $acoNom, $acoDir, $acoTel, $acoEma, $idPac, $comEst, $emo, $aut, $doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $tamaini);

			for($i=0;$i<$tamano;$i++)
			{
				divx3($i, $motNum[$i], $motEst[$i], $motInv[$i], $motCla[$i], $motCau[$i], $motDes[$i] , $motTip[$i], '', 0);
			}
			if (isset ($motDes[$i-1]) and $motDes[$i-1]!='')
			divx3($tamano, $tamano+1, 'INGRESADO', '', '', '', '' , '', '', '');
			echo "</br><center><b><font size='4'><font color='#00008B'>COMENTARIO NUMERO: ".$numCom."</b></font></font></center></BR>";
			echo "<center><b><font size='3'><font color='#00008B'>PERSONA QUE INGRESO EL COMENTARIO: ".$aut."</b></font></font></center>";
			divx4($doc, $tipDoc, $priNom, $segNom, $priApe, $segApe, $dir, $tel, $ema, $tamano, $idCom, $his, $res, $ser, $numCom);
			break;
		}
	}
}
?>
</body>
</html>

