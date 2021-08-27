<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>

<html>
<head>
  	<title>MATRIX HCE-Historia Clinica Electronica</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->
 
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    
    <!-- JQUERY para los tabs -->
	<link type="text/css" href="../../Jquery/ui.all.css" rel="stylesheet" />
	<script type="text/javascript" src="../../Jquery/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="../../Jquery/ui.core.js"></script>
	<script type="text/javascript" src="../../Jquery/ui.tabs.js"></script>
	<script type="text/javascript" src="../../Jquery/ui.draggable.js"></script>
	<link type="text/css" href="../../Jquery/ui.datepicker.css" rel="stylesheet" />
	<script type="text/javascript" src="../../Jquery/ui.datepicker.js"></script>
	<!-- Fin JQUERY para los tabs -->

	
    <style type="text/css">
		.ui-state-barra   {border: 1px solid #C3D9FF; background: #C3D9FF; color: #000; }
		.ui-state-barra   {
			background: #fff; /* Old browsers */
			/* IE9 SVG, needs conditional override of 'filter' to 'none' */
			background: -moz-linear-gradient(top,  #fff 0%, #C3D9FF 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#C3D9FF)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#C3D9FF 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#C3D9FF 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#C3D9FF',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#C3D9FF')";
			zoom:1;
		}
		.ui-state-barra2   {border: 1px solid #fefcea; background: #000000; color: #000; }
		.ui-state-barra2   {
			background: #fff; /* Old browsers */
			/* IE9 SVG, needs conditional override of 'filter' to 'none' */
			background: -moz-linear-gradient(top,  #fff 0%, #E8EEF7 30%); /* FF3.6+ */
			background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#fff), color-stop(30%,#E8EEF7)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top,  #fff 0%,#E8EEF7 30%); /* IE10+ */
			background: linear-gradient(to bottom,  #fff 0%,#E8EEF7 30%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#E8EEF7',GradientType=0 ); /* IE6-8 */
			-ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff', endColorstr='#E8EEF7')";
			zoom:1;
		}

    	body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo3A{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	
    	#tipoT00{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipoT01{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2em;}
    	#tipoT02{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:110em;text-align:left;height:2em;}
    	#tipoT03{color:#000000;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;width:110em;text-align:right;height:2em;}
    	
    	#tipoL01{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;text-align:center;height:2em;}
    	#tipoL02{color:#000000;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;width:15em;text-align:center;height:2em;}
    	#tipoL01A{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;align:center;text-align:center;height:5em;}
    	#tipoL02A{color:#000000;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;width:15em;align:center;text-align:center;height:5em;}
    	#tipoL01B{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:15em;align:center;text-align:center;}
    	#tipoL02B{color:#000000;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;width:15em;align:center;text-align:center;}
    	#tipoL03{color:#000066;background:#FFFFFF;font-size:12pt;font-family:Arial;font-weight:bold;width:90em;text-align:center;height:2em;}
    	#tipoL04{color:#000000;background:#E8EEF7;font-size:12pt;font-family:Arial;font-weight:bold;width:75em;text-align:center;height:2em;}
    	#tipoL05{color:#000000;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:bold;width:25em;text-align:center;height:2em;}
    	#tipoL06{color:#000066;background:#999999;font-size:12pt;font-family:Arial;font-weight:bold;width:90em;text-align:left;height:1em;}
    	#tipoL07{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;width:90em;text-align:left;height:1em;}
    	
    	#tipoG000{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:left;}
    	#tipoG001{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG11{color:#000066;background:#99CCFF;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG51{color:#FF0000;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG52{color:#FFFFFF;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG53{color:#000066;background:#CC99FF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG61{color:#FF0000;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG62{color:#FFFFFF;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG63{color:#000066;background:#999999;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:3em;}
    	#tipoM03{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Tahoma;font-weight:bold;width:30em;text-align:left;height:3em;}
    	
    </style>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#tabs").tabs(); //JQUERY:  Activa los tabs para las secciones del kardex
			//$("#registrof").datepicker({showOn: 'button', buttonImage: '../../images/medical/root/calendar.gif', buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true,yearRange: "-90:+10"});			
			//var array=$(".fechaYear");		   
		   var x=document.getElementsByTagName("input");
			for(var i=0; i<x.length; i++)
			{
				if(x[i].type == "text")
				{
					if(x[i].id.substring(0,9) == "registrof")
					{
						$("#"+x[i].id).datepicker({showOn: 'button', buttonImage: '../../images/medical/root/calendar.gif', buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true,yearRange: "-90:+10"});
					}
				}
			}
		  });
		  
		function verocultar(cual,objeto) {

		     var c=cual.nextSibling;
		     if(c.style.display=='none') {
		          c.style.display='block';
		          document.getElementById(objeto).src = "/matrix/images/medical/hce/menos.png";
		     } else {
		          c.style.display='none';
		          document.getElementById(objeto).src = "/matrix/images/medical/hce/mas.png";
		     }
		     return false;
		}

		function buscarmenu(valor)
		{
		  if (valor =='')
		    {
		     $("#divgeneral").show();
		     $("#divbusqueda").hide(); 
		    }
		  else
		  	{
		  	  $('#tblbusqueda tbody tr').each(function() {                        
                row = $(this).text().toUpperCase();
                n   = row.includes(valor.trim().toUpperCase());
		        if (n==false)
		           {
		           	$(this).hide();
		           }
		        else{
		        	$(this).show();
		        }
 			  });	
		  	 $("#divgeneral").hide();
		     $("#divbusqueda").show();
		    } 
		}
	
	function calendario(idx)
	{
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'24',electric:false,inputField:idx,button:'btn_'+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	}

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
	
	function estaEnProceso(xmlhttp) {
		switch ( xmlhttp.readyState ) {
			case 1, 2, 3:
			return true;
			break;
			// Case 4 y 0
			default:
			return false;
			break;
		}
	}
	function ajaxquery(fila,root,swiches,wbasedato,ok,accion, wemp_pmla)
	{
		var x = new Array();
		
		x[1] = document.getElementById("w01").value; // bfor
		
		st="root="+root+"&swiches="+swiches+"&wbasedato="+wbasedato+"&ok="+ok+"&accion="+accion+"&bfor="+x[1]+"&wemp_pmla="+wemp_pmla;
		
		try{
		ajax=nuevoAjax();
		ajax.open("POST", "configuracion.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
   		ajax.send(st);
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				//alert(ajax.responseText);
				document.getElementById(+fila).innerHTML=ajax.responseText;
			} 
		}
		if ( !estaEnProceso(ajax) ) 
		{
			ajax.send(null);
		}
		}catch(e){ }
	
	}

	function enter()
	{
		document.forms.HCE.submit();
	}
	function ejecutar(path,tipo)
	{
		if(tipo == 1)
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=425');
		else
			window.open(path,'','fullscreen=0,status=0,menubar=0,toolbar =0,directories =0,resizable=0,scrollbars =1,titlebar=0,width=900,height=580');
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function teclado2()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13) event.returnValue = false;
	}
	function teclado3()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & (event.keyCode < 97 || event.keyCode > 122 ) & event.keyCode != 13 & event.keyCode != 45) event.returnValue = false;
	}
	function teclado4()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & event.keyCode != 32 & event.keyCode != 13) event.returnValue = false;
	}
	function teclado5()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 13) event.returnValue = false;
	}


</script>

</head>

<!-- <BODY TEXT="#000066" onload='mueveReloj()' FACE="ARIAL"> -->
<BODY TEXT="#000066" FACE="ARIAL">

<?php


/**********************************************************************************************************************  
[DOC]
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite registrar los datos clinicos
	   de un paciente, en distintos formularios segun la estructura logica definida en la metadata de la HCE.
	   
	   REGISTRO DE MODIFICACIONES :
	   		
	   .2011-10-05
       2016-06-27:  Arleyda Insignares C.
       				- Se Modifica toda la opcion 'F' (formulario) que se construye en el unico 'switch Case' del script. La 
       				razón obedece a que la Funcion Deep() generaba inconsistencias en el Menu. Todo el codigo anterior 
       				queda comentado en la parte final.
       				- Se crea función javascript buscarmenu() y verocultar(), para realizar la busqueda en el Menú y 
       				desplegar las opciones.
       				
[*DOC]   		
***********************************************************************************************************************/
function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function ver1($chain)
{
	if(strrpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strrpos($chain,"-"));
}
function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	if(ereg($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or 
		  ($occur[2] == 4 and  $occur[3] > 30) or 
		  ($occur[2] == 6 and  $occur[3] > 30) or 
		  ($occur[2] == 9 and  $occur[3] > 30) or 
		  ($occur[2] == 11 and $occur[3] > 30) or 
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or 
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="^([=a-zA-Z0-9' 'ñÑ@?/*#-.:;_<>])+$";
	return (ereg($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="^([0-9:])+$";
	return (ereg($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
			return false;
		else
			return true;
	else
		return false;
}
function validar7($chain)
{
	// Funcion que permite validar la estructura de un campo Hora Especial
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >24 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}

//session_start();
if(!isset($_SESSION['user']))
	echo "error de Sessión ";
else
{
	

    include_once("root/comun.php");
    

    include_once("root/magenta.php");
	
	
	//echo "<form name='configuracion' action='configuracion.php' method=post>";
	
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."' />";
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	
	$key = substr($user,2,strlen($user));
	switch($accion)
	{
		case "T": // Pinta encabezado
			echo "<body style='height: 120px; margin-top:0;'>";
			$wactualiz = '2021-08-02'; // Ultima actualización
			$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
			$wbasedato1 = strtolower( $institucion->baseDeDatos );
			encabezado("Historia Cl&iacute;nica Electronica - CONFIGURACION",$wactualiz, $wbasedato1);
		break;
		case "U": // Pinta nombre de usuario que hizo login en matrix
		    $key = substr($user,2,strlen($user));
			$query = "select descripcion from usuarios where codigo = '".$key."'";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$wuser=$row[0];
			echo "<input type='HIDDEN' name= 'accion' value='".$accion."' />";
			$color="#dddddd";
			$color1="#C3D9FF";
			$color2="#E8EEF7";
			$color3="#CC99FF";
			$color4="#99CCFF";
			$wuser=trim($wuser);
			echo "<body style='background-color: #DCDCDC' FACE='ARIAL'>";
			echo "<B>USUARIO : </B>";
			echo "<table border=0>";
			echo "<tr><td id=tipoL05 align='left'>".$wuser."</td></tr>";
			echo "</table>";
			
		break;
		case "F": 

            echo "<IMG SRC='/matrix/images/medical/hce/lupa.png'><font size='12px'><input type='TEXT' id='wbusqueda' name='wbusqueda' size=30 height=80 maxlength=30 class=tipo3 OnChange='buscarmenu(this.value)'></font>";
            echo "</br></br>";
		    $query    = "SELECT Precod, Predes, Preurl, Prenod from ".$wbasedato."_000009 ";
			$query   .= " where Preest = 'on' ";
			$query   .= " Order by Precod ";
			$res      = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
			$vfila        = 0;
			$vnodo        ='0';
			$compor   	  ='';
			$numcodigoant ='';
			$desnodoant   ='';
            $vdetalle     = 0;  
            $vnivel       = 0;
            $vnivel2      = 0;
            $regbusqueda  ='';
            $divbusqueda  ="<div id='divbusqueda' style='display:none;'><table id='tblbusqueda'><tbody>";
            echo "<div id = 'divgeneral'>";
			while($row = mysql_fetch_assoc($res))
			     {
			     	 $numcodigo = $row['Precod'];
			 	     $desmenu   = $row['Predes'];
			 	     $desnodo   = $row['Prenod'];
			 	     $desformu  = substr($row['Preurl'],2,6);
			 	     $varlong   = strlen($numcodigo);

			 	     if ($desnodo == 'on' && $varlong > 3)
			 	     	{$vnodo = '1' ;}
			 	     else
			 	        {$vnodo = '0' ;}
                     
                     if ($desnodo == 'on' && $vnodo == '0')
			 	     {
			 	     	 $regbusqueda = $numcodigo."-".$desmenu." / ";
			 	     	 $class1   = "ui-state-barra";
			 	     	 $vnivel   = 0;
			 	     	 $vnivel2  = 0;
			 	     	 if ($vdetalle == 1)
				          	{ echo "</td></tr></table></fieldset>"; }

				         if ($vdetalle == 1 && strlen($numcodigo)==3)
				              { echo "</td></tr></table></fieldset>";}
				               
			 	     	 if ($vfila>0)
			 	     	 {echo "</table></fieldset>";}

				 	     echo "<fieldset class='".$class1."' border='0px'>";
					     echo "<a onclick='return verocultar(this,".$numcodigo.");'";
					     if ($vfila==0){
					        echo "href='javascript:void(0);'><IMG SRC='/matrix/images/medical/hce/mas.png' id=".$numcodigo."><font color='000099' size='2'>&nbsp;".$numcodigo." - ".$desmenu."</font></a>";
					     }
					     else{
					     	echo "href='javascript:void(0);'>&nbsp;&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/hce/mas.png' id=".$numcodigo."><font color='000099'  size='2'>".$numcodigo." - ".$desmenu."</font></a>";
					     }
					     echo "<div id='".$numcodigo."' style='display: none;'> ";
					     echo "<table>";
					     $vdetalle =0;
				     }
				     else
				     {
				     	$class1   = "ui-state-barra";
				        if ($desnodo == 'on')
				        {
				           $regbusqueda .= $numcodigo."-".$desmenu." / ";	
				           // Si sube de nivel 				        
				           if (strlen($numcodigoant) < strlen($numcodigo) )
				              {
				               $vnivel++;
				               $vnivel2++;	
				              }

				           // Si ambos son dos y sube de nivel
				           if ( (strlen($numcodigoant) > strlen($numcodigo)) && ($desnodoant=='on') && ($desnodo=='on') )
					          {
 								 echo "</td></tr></table></fieldset>";
					          }

				           // Si el nivel es uno y el anterior fue un formulario
				           if ($vnivel>=1 && $vdetalle == 1 && strlen($numcodigo)==3)
				              { echo "</td></tr></table></fieldset>";}

				           if ($vdetalle == 1)
					          { 
					          	   if (strlen($numcodigoant) > strlen($numcodigo))
					          	   {
					          	   	   	echo "</td></tr></table></fieldset>";
					          	   	   	if ($vnivel2>=3)
					          	   	   	{
					          	   	   	    echo "</td></tr></table></fieldset>";
					          	   	   	}
					          	        $vnivel = 0;  
					               }
					          }
				 	      echo "<tr><td><fieldset class='".$class1."'>";
					      echo "<a onclick='return verocultar(this,".$numcodigo.");'";
					      echo "href='javascript:void(0);'><IMG SRC='/matrix/images/medical/hce/mas.png' id=".$numcodigo."><font color='000099' size='2'>&nbsp;".$numcodigo." - ".$desmenu."</font></a>";
					      echo "<div id='".$numcodigo."' style='display: none;'> ";
					      echo "<table>"; 
					      $vdetalle = 1; 

					    }
				        else	
				     	{ 
				     	  // Si la opcion anterior fue un nodo y la longitud del codigo es la misma
				     	  if ((strlen($numcodigoant) == strlen($numcodigo)) && $desnodoant =='on' )
				          	 {echo "</td></tr></table></fieldset>";}
				          if ((strlen($numcodigoant) == strlen($numcodigo)) && strlen($numcodigo)==3)
				             {echo "<table><tr><td>";}	
				          // Si la opcion anterior es formulario y sube de nivel	
				          if ($vdetalle = 1 && (strlen($numcodigoant) > strlen($numcodigo)) )
				             {echo "</td></tr></table></fieldset>";}
				          //Agregar al campo para busqueda
				          $regbusqueda .= $numcodigo."-".$desmenu;
				          $divbusqueda .= "<tr><td><font color='000099' size='2'><a target='principal' href='configuracion.php?accion=M&wformulario=".$desformu."&windmen=".$numcodigo."&wemp_pmla=".$wemp_pmla."'>&nbsp;&nbsp;"." | ".$numcodigo." - ".$desmenu."</a></font></td></tr>";
                          $class1   = "ui-state-barra2";
                          echo "<tr><td class='".$class1."'><font color='000099' size='2'><a target='principal' href='configuracion.php?accion=M&wformulario=".$desformu."&windmen=".$numcodigo."&wemp_pmla=".$wemp_pmla."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$numcodigo." - ".$desmenu."</a></font></td></tr>";
				     	  $vdetalle = 1; 		     	  
				        }
				     }
				     $numcodigoant = $row['Precod'];
				     $desnodoant   = $row['Prenod'];
				     $vfila++;
			 	 }
		    $divbusqueda .= "</tbody></table></div>";
		    echo "</td></tr></table></fieldset>";
		    echo "</div>";
		    echo $divbusqueda ;			
			echo "</br></br><table width='250px'><tr><td align='center'><IMG SRC='/matrix/images/medical/hce/button.gif' onclick='javascript:top.close();'></td></tr></table>";			
		    break;
		case "D": 
			$key = substr($user,2,strlen($user));
			
			echo "<body style='background-color:#FFFFFF' FACE='ARIAL'>";
			$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
			echo "<center><input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
			echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
			//                 0      1      2      3      4      5      6      7      8      9      10     11
			$query = " select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wmovhos."_000016,".$wmovhos."_000018,".$wmovhos."_000011 ";
			$query .= " where pacced = '".$wcedula."'";
			$query .= "   and pactid = '".$wtipodoc."'";
			$query .= "   and pacced = oriced ";
			$query .= "   and pactid = oritid ";
			$query .= "   and oriori = '".$wemp_pmla."'";
			$query .= "   and inghis = orihis ";
			$query .= "   and inging = oriing ";
			$query .= "   and ubihis = inghis "; 
			$query .= "   and ubiing = inging ";
			$query .= "   and ccocod = ubisac ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$sexo="MASCULINO";
			if($row[5] == "F")
				$sexo="FEMENINO";
			$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$ann1=($aa - $ann)/360;
			$meses=(($aa - $ann) % 360)/30;
			if ($ann1<1)
			{
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
			}
			else
			{
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$ann1." Años ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
			}
			$wpac = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
			$color="#dddddd";
			$color1="#C3D9FF";
			$color2="#E8EEF7";
			$color3="#CC99FF";
			$color4="#99CCFF";
			echo "<table border=0>";
			//echo "<tr><td colspan=6 id=tipoL03></td></tr>";
			echo "<tr><td id=tipoL01>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
			echo "<tr><td id=tipoL01>Historia Clinica</td><td id=tipoL02>".$row[6]."-".$row[7]."</td><td id=tipoL01>Edad</td><td id=tipoL02>".$wedad."</td><td id=tipoL01>Sexo</td><td id=tipoL02>".$sexo."</td></tr>";
			echo "<tr><td id=tipoL01>Servicio</td><td id=tipoL02>".$row[11]."</td><td id=tipoL01>Habitacion</td><td id=tipoL02>".$row[10]."</td><td id=tipoL01>Entidad</td><td id=tipoL02>".$row[8]."</td></tr>";
			echo "</table>";
		break;
		
		case "M": 
			    $key = substr($user,2,strlen($user));
				//

				//

				
				$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
				
				echo "<input type='HIDDEN' name= 'accion' value='".$accion."'>";
				if (isset($wformulario))
                   {				
				    echo "<input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
					echo "<input type='HIDDEN' name= 'windmen' value='".$windmen."'>";
				   }
				  else
				    {
					 $wformulario=""; 
					 $windmen=""; 
					}
				$color ="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				
				
				//Orden y nombre de las pestañas (tabs)
				echo "<font size='2'><div id='tabs'>";

				echo "<ul>";
				echo "<li><a href='#F_encpro'><span>Encabezado</span></a></li>";
				echo "<li><a href='#F_detpro'><span>Detalle</span></a></li>";
				echo "<li><a href='#F_prereq'><span>Prerequisito</span></a></li>";
				echo "<li><a href='#F_visaso'><span>Vistas Asocia.</span></a></li>";
				echo "<li><a href='#F_seguri'><span>Seguridad</span></a></li>";
				echo "<li><a href='#F_docencia'><span>Docencia</span></a></li>";
				echo "<li><a href='#F_Roles'><span>Roles</span></a></li>";
				echo "<li><a href='#F_Empresas'><span>Empresas</span></a></li>";
				echo "<li><a href='#F_Admarbol'><span>Adm. Arbol</span></a></li>";
				echo "<li><a href='#F_OrdImpresion'><span>Orden Impre</span></a></li>";
				echo "<li><a href='#F_Tipodato'><span>Tipo Dato</span></a></li>";
				echo "<li><a href='#F_valfor'><span>Validacion</span></a></li>";				
				//echo "<li><a href='#F_valfor'><span>Validaciones Foraneas</span></a></li>";				
				echo "</ul>";
				
				//==============================================================================================================================================================================================
				//Aca traigo el nombre o descripcion del formulario
				$q = " SELECT encpro, predes "
				    ."   FROM ".$wbasedato."_000001,".$wbasedato."_000009 "
				    ."  WHERE encpro = '".$wformulario."'"
					."    AND INSTR(preurl,encpro) > 0 "
					."    AND precod = '".$windmen."'";
				$res = mysql_query($q,$conex) or die("error fatal ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($res);
				
				if ($num > 0)
				  {
				   $row = mysql_fetch_array($res);
				  }
			    else
			      {
			       $row[0]="";
			       $row[1]="";
		          } 
				    
			    //Nombre del Protocolo
				echo "<p align='center'><font size=5><strong>".$row[0]." - ".$row[1]."</strong></font></p>";
				//==============================================================================================================================================================================================
				
				echo "<div id='F_encpro'>";
				echo "<iframe src='Maestro_protocolos.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."&windmen=".$windmen."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='500' width='1000' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_detpro'>";                                                                                                                                              
				//echo "<iframe src='Detalle_protocolo.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."&wparametro=campos' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1050' marginheiht=0>";
				//echo "</iframe>";
				echo "<iframe src='Detalle_protocolo.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."&wparametro=grilla' id=grilla name=grilla marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='550' width='1200' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_valfor'>";
				echo "<iframe src='Validaciones_Foraneas.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_prereq'>";
				echo "<iframe src='Prerequisitos.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_visaso'>";
				echo "<iframe src='Vistas_Asociadas.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_seguri'>";
				echo "<iframe src='Seguridad.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='1500' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_docencia'>";
				echo "<iframe src='Docencia.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='1500' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";

				echo "<div id='F_Roles'>";
				echo "<iframe src='Roles.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_Empresas'>";
				//echo "<iframe src='EmpresasHCE.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "<iframe src='EmpresasHCE.php?wemp_pmla=".$wemp_pmla."&wformulario=".$wformulario."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_Admarbol'>";
				echo "<iframe src='admonArbol.php?wemp_pmla=".$wemp_pmla."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";
				
				echo "<div id='F_OrdImpresion'>";
				echo "<iframe src='orden_impresion.php?wemp_pmla=".$wemp_pmla."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";

				echo "<div id='F_Tipodato'>";
				echo "<iframe src='Tipodatos.php?wemp_pmla=".$wemp_pmla."' marginwidth=1 scrolling='yes' framespacing='0' frameborder='1' border='0' height='600' width='1300' marginheiht=0>";
				echo "</iframe>";
				echo "</div>";			

		break;
	}
	//echo "</form>";
}

?>
</body>
</html>
