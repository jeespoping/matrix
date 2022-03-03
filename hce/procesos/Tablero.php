<html>
<head>
  	<title>MATRIX Tablero de Pacientes</title>  	
    <link rel="stylesheet" href="../../zpcal/themes/winter.css"/>

    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo12{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo18A{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo19A{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo18{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo19{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    	#tipoT00{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipoT01{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;height:2em;}
    	#tipoT02{color:#000000;background:#C3D9FF;font-size:12pt;font-family:Arial;font-weight:bold;width:110em;text-align:left;height:2em;}
    	#tipoT03{color:#000000;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;width:110em;text-align:right;height:1em;}
    	#tipoT04{color:#FFFFFF;background:#003366;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	#tipoT05{color:#000066;background:#cccccc;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;height:3em;}
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#FFFFFF;background:#FFFFFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG54{color:#000066;background:#DDDDDD;font-size:6pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG11{color:#FFFFFF;background:#99CCFF;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG21{color:#FFFFFF;background:#CC3333;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG32{color:#FF0000;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG33{color:#006600;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG34{color:#000066;background:#FFFF66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG42{color:#FF0000;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG41{color:#FFFFFF;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	#tipoG44{color:#000066;background:#00CC66;font-size:5pt;font-family:Tahoma;font-weight:bold;width:6.4em;text-align:center;height:3em;}
    	
    	#tipoM00{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:3em;}
    	
    </style>    

    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>
	<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>


    <script type="text/javascript">
	<!--
	function ejecutar(path,wpar)
	{
		$.ajax({
			  type: "POST",
			  url: "/matrix/hce/procesos/HCE_close.php",
			  data: {"whce" : "1"},
			  async:false,
			  success: function(data){whce = data;}
		  });
		if(whce == 1)
		{
			alert("YA EXISTE UNA HISTORIA ABIERTA, POR FAVOR CIERRELA ANTES DE ENTRAR A OTRA!!!. GRACIAS");
		}
		else
		{
			if(wpar == 1)
			{
				alert("POR FAVOR ENTRE A LA HISTORIA A TRAVES DE LA SALA DE ESPERA DE URGENCIAS!!!. GRACIAS");
			}
			else
			{
				window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
			}
		}
	}
	function IGP(path)
	{
		window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
	}
	function enter()
	{
		document.forms.Tablero.submit();
	}
	function Seleccion(par)
	{
		if(par == 1)
		{
			document.getElementById('cri1').value=document.getElementById('wcsel1').value;
		}
		else
		{
			document.getElementById('cri2').value=document.getElementById('wcsel2').value;
		}
	}
	function llenar(par)
	{
		if(par == 1)
		{
			tip=document.getElementById('wclass1').value.split('-');
			wcsel1=document.getElementById('wcsel1');
			wcsel1.options.length=0;
			switch(tip[0])
			{
				case "1":
					items = document.getElementById('WSA').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel1.options[i+1]=new Option(items[i],items[i]);
					}
				break;
				case "5":
					items = document.getElementById('WRE').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel1.options[i+1]=new Option(items[i],items[i]);
					}
				break;
				case "6":
					items = document.getElementById('WME').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel1.options[i+1]=new Option(items[i],items[i]);
					}
				break;
				case "7":
					items = document.getElementById('WES').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel1.options[i+1]=new Option(items[i],items[i]);
					}
				break;
			}
		}
		else
		{
			tip=document.getElementById('wclass2').value.split('-');
			wcsel2=document.getElementById('wcsel2');
			wcsel2.options.length=0;
			switch(tip[0])
			{
				case "1":
					items = document.getElementById('WSA').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel2.options[i+1]=new Option(items[i],items[i]);
					}
				break;
				case "5":
					items = document.getElementById('WRE').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel2.options[i+1]=new Option(items[i],items[i]);
					}
				break;
				case "6":
					items = document.getElementById('WME').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel2.options[i+1]=new Option(items[i],items[i]);
					}
				break;
				case "7":
					items = document.getElementById('WES').value.split('|');
					for (i=0;i<items.length;i++)
					{
						wcsel2.options[i+1]=new Option(items[i],items[i]);
					}
				break;
			}
		}
	}
	function clean()
	{
		document.getElementById('cri1').value="";
		document.getElementById('wclass1').value="0-SELECCIONE";
		document.getElementById('cri2').value="";
		document.getElementById('wclass2').value="0-SELECCIONE";
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

/*
	window.onbeforeunload = function()
	{
		alert("LA VENTANA SE VA A CERRAR");
	}
*/

	//-->

	$(document).on('change','#selectsede',function(){
        window.location.href = "Tablero.php?empresa="+$('#empresa').val()+"&codemp="+$('#codemp').val()+"&wdbhce="+$('#wdbhce').val()+"&wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val();
    });

</script>
 
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : Tablero.php
	   Fecha de Liberaci&oacute;n : 2009-08-06
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2019-11-18
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gr&aacute;fica que permite Ingresar al programa de 
	   Historia Clinica Electronica. Los pacientes pueden ser ubicados por diferentes criterios de seleccion.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2020-10-20
				Se agrega filtro para que no se muestre pacientes de servicio domiciliario
		.2019-11-18
				Se agrega slash(/) a la ruta matrix/HCE/procesos/HCE_Impresion.php para evitar que al abrir se construya 
				erróneamente /matrix/hce/procesos/matrix/HCE/procesos/HCE_Impresion.php, esta situación ocurría al aplicar
				un filtro.
	   .2018-07-23
				Se corrige la consulta que trae los medicos tratantes para que no muestre medicos repetidos.
	   .2016-07-19
				Se modifica el programa para que excluya las unidades ambulatorias que hacen admisiones.
	   .2016-06-10
				Se modifica el programa para que cuando la firma este vencida permita actualizarla en PassHCE.php
	   .2016-05-25
				Se modifica el programa para permitir el acceso de codigos basados em ip (*)
	   .2015-07-01
			  Se modifica el programa para prevernir el ingreso a mas de una Historia en el mismo Login.
	   .2014-11-12
              Se cambia la busqueda de pacientes en centros de costos de urgencias.
	   .2013-11-05
              Se ponen dinamicas las tablas de hce y movhos al igual que la empresa origen.
	   .2013-09-30
			1. Se adiciona en le query el campo Habord para mejorar el orden de las habitaciones.
			2. Se cambia el query de alimentacion de los medicos para mejorar la busqueda binaria.
	   .2013-07-30
			1. Se activa la opcion de consulta para el programa de Informacion General de Pacientes IGP.
			2. Se cambia el query de la unidades ambulatorias para excluir aquellas que tengan la opcion de hoteleria prendida
			   Ccohot de la tabla movhos 11.
			3. Igualmente para esta unidades se traen los pacientes que no tengan asignadas camas hospitalarias.
	   .2013-05-20
			Se activa la validacion para que medicos de urgencias no puedan entrar a la HCE a traves de este tablero para
			pacientes que se encuentren en la unidad de Urgencias.
	   .2013-05-10
			Se Modicica el programa para Validar.
				1. La fecha de vencimiento de la Firma Electronica
				2. Si al ip no tiene permiso de grabacion solamente le abre el programa de consulta
	   .2012-10-02
			Se Modifica el programa para corregir el metodo de asignacion de medicos y especialidades por paciente, ya
			que la busqueda binaria no los estaba encontrando. Tambien se le adiciono al programa un control para que
			medicos asignados a la Unidad de Urgencias no pudieran entrar a la historia de pacientes de urgencias a traves
			de este tablero, sino por la salda de espera de Urgencias.
			Se modifica La Variable historia por 'wdbhce' para hacer el tablero multibase de datos.
	   .2012-07-09
			Se Modifica el programa para corregir el metodo de busqueda bajo el criterio de empresa responsable ya que no
			lo estaba haciendo de forma correcta. Este metodo se da&ntilde;o al corregir la evaluacion de empresas agrupadas. 
	   .2012-06-25
			Se Modifica el programa para corregir el metodo de evaluacion de las empresas agrupadas ya que no lo estaba
			haciendo de forma correcta. 
	   .2012-02-28
			Se Modifica el programa para acceder a la tabla 39 de HCE con el proposito de validar las direcciones IP
			validas con autorizacion de acceso a la historia clinica electronica.
	   .2012-02-14
			Se Modicica el programa para agregar una opcion de busqueda de los pacientes por los suguientes criterios de busqueda.
				1. Todos los Pacientes
				2. Hospitalizados
				3. Activos en Unidades Ambulatorias
				4. Egresados Hace Menos de 6 Horas
				5. Fallecidos
	   .2012-01-31	
			Se Modifica el programa para agregar una opcion de busqueda de los pacientes hospitalizados a los que no se les
			ha ingresado informacion en la HCE.
	   .2012-01-10
			Se modifica el programa para cambiar en la columna de habitacion de pacientes egresados en nobre del centro de costos
			por el mensaje ALTA DEFINITIVA HACE MENOS DE 6 HORAS.
	   .2012-01-06
			Se modifica el programa para incluir validacion de existencia de la variables WSA WRE WME WES. estaban mostrado warning.
	   .2011-12-12
			Se modifica el query de consulta de pacientes para incluir los pacientes egresados en los dos dias anteriores.
	   .2011-11-25
	   		Se cambia el proceso de busqueda x criterios ya que el anterior no lo hacia correctamente.
	   .2011-10-25
	   		Se cambia el query de los pacientes que no estan en unidades hospitalarias para generalizarlas en un solo
	   		query (Admisiones, Urgencias, Cirugia, Fisiatria y Otros).
	   .2011-10-03
	   		Se cambia el query de los pacientes que no estan en urgencias para que excluya los pacientes que estan 
	   		en la cama virtual de urgencias porque en este caso lo muestra dos veces.
	   .2011-05-04
	   		Se cambia el query de los pacientes en Urgencias para mostar todos los que tengan alta definitiva en off
	   		sin rango de fechas. 
	   .2011-02-14
	   		Se agrega el boton de cerrar ventana.
	   .2011-02-07
	   		Se la validaci&oacute;n por empresas,servicios y especialidades.
	   .2011-01-05
	   		Se corrige la opcion de limpiar campos de criterio de busqueda que estaba sacando errores en javaScript  
	   .2009-08-06
	   		Release de Versi&oacute;n Beta.
	   
	   		
[*DOC]
***********************************************************************************************************************/


function partir($data)
{
	$data1="";
	if(is_numeric(substr($data,0,1)))
	{
		$k=0;
		while(is_numeric(substr($data,$k,1)) and $k <= strlen($data))
		{
			$data1 .= substr($data,$k,1);
			$k++;
		}
		$data2="";
		while($k <= strlen($data))
		{
			$data2 .= substr($data,$k,1);
			$k++;
		}
		if(is_numeric($data1) and !is_numeric($data2) and $data2 != " " and $data2 != "")
			$data1 = $data1."-".$data2;
		return $data1;
	}
	else
	{
		$k=0;
		while(!is_numeric(substr($data,$k,1)) and $k <= strlen($data))
		{
			$data1 .= substr($data,$k,1);
			$k++;
		}
		$data2="";
		while($k <= strlen($data))
		{
			$data2 .= substr($data,$k,1);
			$k++;
		}
		if(!is_numeric($data1) and is_numeric($data2))
			$data1 = $data1."-".$data2;
		return $data1;
	}
}
function bi($d,$n,$k)
{
	//$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$elemento)
{
	//echo "especial: ".$especial." elemento: ".$elemento."<br>";
	if($especial == 0 or $Sroot == "on")
		return true;
	if($especial == 1)
	{
		//$elemento=substr($elemento,0,strrpos($elemento,"-"));
		for ($i=0;$i<$numemp;$i++)
		{
			//if(strpos(strtoupper($SempT[$i]),strtoupper($elemento)) !== false)
			//echo "Empresa: ".$SempT[$i]." elemento: ".$elemento."<br>";
			if(strtoupper($SempT[$i]) == strtoupper($elemento))
				return true;
		}
		return false;
	}
	if($especial == 2)
	{
		for ($i=0;$i<$numuni;$i++)
		{
			if(strpos(strtoupper($elemento),strtoupper($Suni[$i])) !== false)
				return true;
		}
		return false;
	}
	if($especial == 3)
	{
		for ($i=0;$i<$numesp;$i++)
		{
			if(strpos(strtoupper($elemento),strtoupper($Sesp[$i])) !== false)
				return true;
		}
		return false;
	}
	
}
function search1($st1,$st2)
{
	$st3=explode("|",$st1);				
	for ($i=0;$i<count($st3);$i++)
		if($st3[$i] == $st2)
			return true;
	return false;
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
	$regular="^([=a-zA-Z0-9' '��@?/*#-.:;_<>])+$";
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
		if($occur[1] < 0 or $occur[1] >23 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}

function valgen($ok,$conex,$wtem,&$werr,&$e)
{
	global $empresa;
	//VALIDACION DE DATOS GENERALES
	if(!validar4($wtem) or $wtem == "0-NO APLICA"  or $wtem == "-" or $wtem == "SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO ESCOGIO UNIDAD";
	}
	if($e == -1)
		return true;
	else
		return false;
}

@session_start();
if(!isset($_SESSION["user"]))
	echo "error";
else
{
	if(!isset($_SESSION["HCEON"]))
		$_SESSION["HCEON"] = 0;
		
	$wemp_pmla = $codemp;

	include_once("root/comun.php");
	$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
	$wactualiz = "03 de marzo de 2022";

	echo "<form name='Tablero' action='Tablero.php' method=post>";
	encabezado( "TABLERO DE PACIENTES HCE", $wactualiz, $institucion->baseDeDatos, TRUE );

	$key = substr($user,2,strlen($user));
	//
	echo "<center><input type='HIDDEN' name= 'empresa' id='empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'codemp' id='codemp' value='".$codemp."'>";
	echo "<input type='HIDDEN' name= 'wdbhce' id='wdbhce' value='".$wdbhce."'>";
	echo "<input type='hidden' id='sede' name= 'sede' value='".$selectsede."'>";
	echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
	
	// echo "<table border=0 CELLSPACING=0>";
	// echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/HCE".$codemp.".jpg'></td>";
	// echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;TABLERO DE PACIENTES HCE&nbsp;&nbsp;<A HREF='/matrix/root/reportes/DOC.php?files=/var/www/matrix/hce/procesos/Tablero.php' target='_blank'>Version 2018-07-23</A></td></tr>";
	// echo "<tr><td id=tipoT03 colspan=2></td></tr>";
	// echo "</table><br><br>";
	echo "<center><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'></IMG></center><br>";
	
	$dateA=date("Y-m-d");
	$dateB=strtotime("-1 day");
	$dateC=strftime("%Y-%m-%d",$dateB);
	
	$Semp="";
	$Sesp=array();
	$Sroot="off";
	$numesp=0;
	$SempT=array();
	$numemp=0;
	$Suni=array();
	$numuni=0;
	$Wexiste=0;
	$IPOK=0;
	$query = "select ctanip, ctausu, ctagra from ".$wdbhce."_000039 ";
	$query .= " where ctaest = 'on'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$IPTIP=$row[2];
			echo "<input type='HIDDEN' name= 'IPTIP' value='".$IPTIP."'>";
			if(($row[0] == substr($IIPP,0,strlen($row[0])) and $key == $row[1]) or ($row[0] == substr($IIPP,0,strlen($row[0])) and $row[1] == "*") or ($row[0] == "*" and $key == $row[1]))
			{
				$IPOK=1;
				$i=$num+1;
			}
		}
	}
	
	$query = "select Usufve from ".$wdbhce."_000020 ";
	$query .= " where usucod = '".$key."' ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		$row = mysql_fetch_array($err);
		if(date("Y-m-d") > $row[0])
			if($IPOK > 0)
				$IPOK = -1;
	}
			
	if($IPOK > 0)
	{
		//                 0       1      2     3
		$query = "select usurol,Rolatr,Rolemp,Usuuni from ".$wdbhce."_000020,".$wdbhce."_000019 ";
		$query .= " where usucod = '".$key."' ";
		$query .= "   and usurol = rolcod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$Wexiste=1;
			$row = mysql_fetch_array($err);
			if($row[1] == "on")
			{
				$Sroot="on";
			}
			if($row[3] != "NO APLICA" and $row[3] != "")
			{
				$Suni=explode(",",$row[3]);
				$numuni=count($Suni);
			}
			$Semp=$row[2];
			$query = "select Medesp from ".$empresa."_000048 ";
			$query .= " where Meduma = '".$key."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$Sesp[$i]=$row[0];
				}
			}
			$numesp=$num;
		}
		if($Semp != "NO APLICA" and $Semp != "*")
		{
			$query = "select Empemp from ".$wdbhce."_000025 ";
			$query .= " where Empcod = '".$Semp."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				$SempT=explode(",",$row[0]);
				$numemp=count($SempT);
			}
		}
		
		if($Wexiste == 1)
		{
			$medurg = 0;
			$query = "select Medurg ";
			$query .= "  from ".$empresa."_000048 "; 
			$query .= "   where Meduma = '".$key."' ";
			$query .= "     and Medesp not in ('100241','100242') ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				if($row[0] == "on")
					$medurg = 1;
			}
			
			$MEDESP=array();
			
			//                                  0       1      2       3      4       5       6       7
			$query = "select lpad(Methis,12,'0'),lpad(Meting,8,'0'),Medno1, Medno2, Medap1, Medap2, Metesp, Espnom, max(Metfek), Metdoc";
			$query .= "  from ".$empresa."_000018,".$empresa."_000047,".$empresa."_000048,".$empresa."_000044 "; 
			$query .= "   where Ubiald = 'off' ";
			$query .= " 	and Ubihis = Methis ";
			$query .= " 	and Ubiing = Meting ";
			$query .= " 	and Metest = 'on' ";
			$query .= " 	and Mettdo = Medtdo ";  
			$query .= " 	and Metdoc = Meddoc "; 
			$query .= " 	and Metesp = Espcod ";			
			$query .= "   group by Methis, Meting, Metdoc";
			$query .= "   order by 1,2,9 desc,3,4,5,6 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wkey="";
				$whising="";
				$whisa="";
				$winga="";
				$lhis=0;
				$ling=0;
				$kme=-1;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					
					if(substr($whisa,0,$lhis) != $row[0] or substr($winga,0,$ling) != $row[1])
					{
						$wkey=$row[8];
						$whisa=$row[0];
						$winga=$row[1];
						$lhis=strlen($whisa);
						$ling=strlen($winga);
						//while(strlen($whisa) < 12) $whisa = $whisa."0";
						//while(strlen($winga) < 8) $winga = $winga."0";
						$kme++;
						$MEDESP[$kme][0] = $whisa.$winga;
						$MEDESP[$kme][1] = $row[2]." ".$row[3]." ".$row[4]." ".$row[5];
						$MEDESP[$kme][2] = $row[6]."-".$row[7];
					}
					elseif(substr($whisa,0,$lhis) == $row[0] and substr($winga,0,$ling) == $row[1] and $wkey == $row[8])
						{
							$MEDESP[$kme][1] .= "<br>".$row[2]." ".$row[3]." ".$row[4]." ".$row[5];
							$MEDESP[$kme][2] .= "<br>".$row[6]."-".$row[7];
						}
				}
			}
			if(isset($SHCE))
			{
				$query = "DROP TABLE IF EXISTS HCE36";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$query = "CREATE TEMPORARY TABLE if not exists HCE36 as ";
				$query .= " select concat(Firhis,Firing) as t2 from ".$wdbhce."_000036 where Firpro='000051' GROUP BY Firhis,Firing ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				$query = "CREATE INDEX clave1 on HCE36 (t2(12))";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				$query = "DROP TABLE IF EXISTS MH1811";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$query = "CREATE TEMPORARY TABLE if not exists MH1811 as ";
				$query .= " select Ubihis, Ubiing, Ubisac, Ubialp, Ubiptr, Ubiald, concat(Ubihis, Ubiing) as t1 ";
				$query .= "  from ".$empresa."_000018,".$empresa."_000011 ";
				$query .= "  where ubiald = 'off' ";  
				$query .= "  and ubisac = Ccocod "; 
				$query .= "  and Ccohos = 'on' ";  
				$query .= "  and Ccourg != 'on' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				$query = "DROP TABLE IF EXISTS MHSHCE";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$query = "CREATE TEMPORARY TABLE if not exists MHSHCE as ";
				$query .= " select Ubihis, Ubiing, Ubisac, Ubialp, Ubiptr, Ubiald ";
				$query .= "  from MH1811 ";
				$query .= "  where t1 not in (select t2 from HCE36) ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				$query = "CREATE UNIQUE INDEX clave2 on MHSHCE (Ubihis(20),Ubiing(20))";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				//                   0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16                 17
				$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Habcod, ".$empresa."_000016.fecha_data, Habord  ";
				$query .= " from MHSHCE,".$empresa."_000011,".$empresa."_000020,root_000037,root_000036,".$empresa."_000016 ";
				$query .= " where ubiald = 'off'  ";
				$query .= " and ubisac = Ccocod  ";
				$query .= " and Ccohos = 'on'  ";
				$query .= " and Ccourg != 'on'  ";
				$query .= " and ubihis = Habhis ";
				$query .= " and ubiing = Habing ";
				$query .= " and ubihis = orihis  ";
				$query .= " and ubiing = oriing  ";
				$query .= " and oriori = '".$codemp."'  ";
				$query .= " and oriced = pacced  ";
				$query .= " and oritid = pactid  ";
				$query .= " and orihis = inghis "; 
				$query .= " and oriing = inging  ";
				$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
				$query .= "  order by 11,19,17 ";
			}
			else
			{
				if(!isset($x) or $x == 0)
				{
					//                   0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16                 17                 18
					$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Habcpa, ".$empresa."_000016.fecha_data, Habord  ";
					$query .= " from ".$empresa."_000018,".$empresa."_000011,".$empresa."_000020,root_000037,root_000036,".$empresa."_000016 ";
					$query .= " where ubiald = 'off'  ";
					$query .= " and ubisac = Ccocod  ";
					$query .= " and Ccohos = 'on'  ";
					$query .= " and Ccourg != 'on'  ";
					$query .= " and ubihis = Habhis ";
					$query .= " and ubiing = Habing ";
					$query .= " and ubihis = orihis  ";
					$query .= " and ubiing = oriing  ";
					$query .= " and oriori = '".$codemp."'  ";
					$query .= " and oriced = pacced  ";
					$query .= " and oritid = pactid  ";
					$query .= " and orihis = inghis "; 
					$query .= " and oriing = inging  ";
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Habcpa, ".$empresa."_000016.fecha_data, Habord  ";
					$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016,".$empresa."_000020  ";
					$query .= "  where ubiald = 'off'  ";
					$query .= "    and ubihis = Habhis ";
					$query .= "    and ubiing = Habing ";
					$query .= "    and ubisac = Ccocod  "; 
					$query .= "    and Ccourg = 'on'  ";
					$query .= "    and ubihis = orihis "; 
					$query .= "    and ubiing = oriing ";
					$query .= "    and oriori = '".$codemp."'  "; 
					$query .= "    and oriced = pacced  ";
					$query .= "    and oritid = pactid  "; 
					$query .= "    and orihis = inghis  ";
					$query .= "    and oriing = inging "; 
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Cconom, ".$empresa."_000016.fecha_data, 0  ";
					$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
					$query .= "  where ubiald = 'off'  ";
					$query .= "    and ubihis not in (select Habhis from ".$empresa."_000020 where Habhis = Ubihis) ";
					$query .= "    and ubisac = Ccocod  "; 
					$query .= "    and Ccourg = 'on'  ";
					$query .= "    and ubihis = orihis "; 
					$query .= "    and ubiing = oriing ";
					$query .= "    and oriori = '".$codemp."'  "; 
					$query .= "    and oriced = pacced  ";
					$query .= "    and oritid = pactid  "; 
					$query .= "    and orihis = inghis  ";
					$query .= "    and oriing = inging "; 
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Cconom, ".$empresa."_000016.fecha_data, 0  ";
					$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
					$query .= "  where ubiald = 'off'  ";
					$query .= "    and ubihis not in (select Habhis from ".$empresa."_000020 where Habhis = Ubihis) ";
					$query .= "    and ubisac = Ccocod  "; 
					$query .= "    and Ccoadm = 'on'  ";
					$query .= "    and Ccohos != 'on'  ";
					$query .= "    and Ccoayu != 'on'  ";
					$query .= "    and ubihis = orihis "; 
					$query .= "    and ubiing = oriing ";
					$query .= "    and oriori = '".$codemp."'  "; 
					$query .= "    and oriced = pacced  ";
					$query .= "    and oritid = pactid  "; 
					$query .= "    and orihis = inghis  ";
					$query .= "    and oriing = inging "; 
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Cconom, ".$empresa."_000016.fecha_data, 0  ";
					$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
					$query .= "  where ubiald = 'off'  ";
					$query .= "    and ubihis not in (select Habhis from ".$empresa."_000020 where Habhis = Ubihis) ";
					$query .= "    and ubisac = Ccocod  "; 
					$query .= "    and Ccocir = 'on'  ";
					$query .= "    and Ccohos != 'on'  ";
					$query .= "    and ubihis = orihis "; 
					$query .= "    and ubiing = oriing ";
					$query .= "    and oriori = '".$codemp."'  "; 
					$query .= "    and oriced = pacced  ";
					$query .= "    and oritid = pactid  "; 
					$query .= "    and orihis = inghis  ";
					$query .= "    and oriing = inging "; 
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'ALTA DEFINITIVA HACE MENOS DE 6 HORAS', ".$empresa."_000016.fecha_data,0  ";
					$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
					$query .= "  where Ubifad between '".$dateC."' and '".$dateA."' ";
					$query .= "    and ubiald = 'on'  ";
					$query .= "    and Ccohos = 'on'  ";
					$query .= "    and cast(mid(timediff(now(),concat(ubifad,' ',ubihad)),1,locate(':',timediff(now(),concat(ubifad,' ',ubihad)))-1) as SIGNED) <= 6  ";
					$query .= "    and ubisac = Ccocod  "; 
					$query .= "    and ubihis = orihis "; 
					$query .= "    and ubiing = oriing ";
					$query .= "    and oriori = '".$codemp."'  "; 
					$query .= "    and oriced = pacced  ";
					$query .= "    and oritid = pactid  "; 
					$query .= "    and orihis = inghis  ";
					$query .= "    and oriing = inging "; 
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'ALTA DEFINITIVA HACE MENOS DE 6 HORAS', ".$empresa."_000016.fecha_data,0  ";
					$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
					$query .= "  where Ubifad between '".$dateC."' and '".$dateA."' ";
					$query .= "    and ubiald = 'on'  ";
					$query .= "    and Ccourg = 'on'  ";
					$query .= "    and cast(mid(timediff(now(),concat(ubifad,' ',ubihad)),1,locate(':',timediff(now(),concat(ubifad,' ',ubihad)))-1) as SIGNED) <= 6  ";
					$query .= "    and ubisac = Ccocod  "; 
					$query .= "    and ubihis = orihis "; 
					$query .= "    and ubiing = oriing ";
					$query .= "    and oriori = '".$codemp."'  "; 
					$query .= "    and oriced = pacced  ";
					$query .= "    and oritid = pactid  "; 
					$query .= "    and orihis = inghis  ";
					$query .= "    and oriing = inging "; 
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  UNION ALL ";
					$query .= " select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'FALLECIDO', ".$empresa."_000016.fecha_data, 0  ";
					$query .= " from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016 ";
					$query .= " where ubiald = 'off'  ";
					$query .= " and Ubimue = 'on'  ";
					$query .= " and ubisac = Ccocod  ";
					$query .= " and Ccohos = 'on'  ";
					$query .= " and Ccourg != 'on'  ";
					$query .= " and ubihis = orihis  ";
					$query .= " and ubiing = oriing  ";
					$query .= " and oriori = '".$codemp."'  ";
					$query .= " and oriced = pacced  ";
					$query .= " and oritid = pactid  ";
					$query .= " and orihis = inghis "; 
					$query .= " and oriing = inging  ";
					$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
					$query .= "  order by 11,19,17 ";
				}
				elseif(isset($x) and $x == 1)
					{
						//                   0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16                 17
						$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Habcpa, ".$empresa."_000016.fecha_data,Habord  ";
						$query .= " from ".$empresa."_000018,".$empresa."_000011,".$empresa."_000020,root_000037,root_000036,".$empresa."_000016 ";
						$query .= " where ubiald = 'off'  ";
						$query .= " and ubisac = Ccocod  ";
						$query .= " and Ccohos = 'on'  ";
						$query .= " and Ccourg != 'on'  ";
						$query .= " and ubihis = Habhis ";
						$query .= " and ubiing = Habing ";
						$query .= " and ubihis = orihis  ";
						$query .= " and ubiing = oriing  ";
						$query .= " and oriori = '".$codemp."'  ";
						$query .= " and oriced = pacced  ";
						$query .= " and oritid = pactid  ";
						$query .= " and orihis = inghis "; 
						$query .= " and oriing = inging  ";
						$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
						$query .= "  order by 11,19,17 ";
					}
					elseif(isset($x) and $x == 2)
						{
							$query  = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Cconom, ".$empresa."_000016.fecha_data,0  ";
							$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
							$query .= "  where ubiald = 'off'  ";
							$query .= "    and ubisac = Ccocod  "; 
							$query .= "    and Ccoing = 'on'  ";
							$query .= "    and ubihis = orihis "; 
							$query .= "    and ubiing = oriing ";
							$query .= "    and oriori = '".$codemp."'  "; 
							$query .= "    and oriced = pacced  ";
							$query .= "    and oritid = pactid  "; 
							$query .= "    and orihis = inghis  ";
							$query .= "    and oriing = inging ";
							$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
							$query .= "  order by 11,19,17 "; 
						}
						elseif(isset($x) and $x == 3)
							{
								$query  = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'ALTA DEFINITIVA HACE MENOS DE 6 HORAS', ".$empresa."_000016.fecha_data,0  ";
								$query .= "  from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016  ";
								$query .= "  where Ubifad between '".$dateC."' and '".$dateA."' ";
								$query .= "    and ubiald = 'on'  ";
								$query .= "    and cast(mid(timediff(now(),concat(ubifad,' ',ubihad)),1,locate(':',timediff(now(),concat(ubifad,' ',ubihad)))-1) as SIGNED) <= 6  ";
								$query .= "    and ubisac = Ccocod  "; 
								$query .= "    and ubihis = orihis "; 
								$query .= "    and ubiing = oriing ";
								$query .= "    and oriori = '".$codemp."'  "; 
								$query .= "    and oriced = pacced  ";
								$query .= "    and oritid = pactid  "; 
								$query .= "    and orihis = inghis  ";
								$query .= "    and oriing = inging "; 
								$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
								$query .= "  order by 11,19,17 ";
							}
							elseif(isset($x) and $x == 4)
							{
								$query  = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'FALLECIDO', ".$empresa."_000016.fecha_data,0  ";
								$query .= " from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016 ";
								$query .= " where ubiald = 'off'  ";
								$query .= " and Ubimue = 'on'  ";
								$query .= " and ubisac = Ccocod  ";
								$query .= " and Ccohos = 'on'  ";
								$query .= " and Ccourg != 'on'  ";
								$query .= " and ubihis = orihis  ";
								$query .= " and ubiing = oriing  ";
								$query .= " and oriori = '".$codemp."'  ";
								$query .= " and oriced = pacced  ";
								$query .= " and oritid = pactid  ";
								$query .= " and orihis = inghis "; 
								$query .= " and oriing = inging  ";
								$query .= " and ccodom != 'on'  ";	//Se agrega filtro para que no muestre los pacientes de servicio domiciliario
								$query .= "  order by 11,19,17 ";
							}
				
			}
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			
			if ($num>=0)
			{
				$variables=array();
				$variables[0]="1-Servicio Actual";
				$variables[1]="2-Habitacion";
				$variables[2]="3-Historia Clinica";
				$variables[3]="4-Nombre Paciente";
				$variables[4]="5-Entidad Responsable";
				$variables[5]="6-Medico Tratante";
				$variables[6]="7-Especialidad Medica";
				$criterios=array();
				echo "<table border=0 align=center id=tipo5 CELLSPACING=1 CELLPADDING=2>";
				echo "<tr><td id=tipoT05 align=center>";
				echo "Primera Variable de Busqueda</td><td id=tipoT05 align=center><select name='wclass1' id=wclass1 onChange='llenar(1)'>";
				echo "<option>0-SELECCIONE</option>";
				for ($i=0;$i<7;$i++)
				{
					if($wclass1 == $variables[$i])
						echo "<option selected value='".$variables[$i]."'>".$variables[$i]."</option>";
					else
						echo "<option value='".$variables[$i]."'>".$variables[$i]."</option>";
				}
				echo "</select></td><td id=tipoT05 align=center>";
				if(isset($wcri1) and $wcri1 != "")
					echo "Primer Criterio de Busqueda</td><td id=tipoT05 align=center><input type='TEXT' name='wcri1' value='".$wcri1."' id=cri1 size=30 maxlength=30>";
				else
					echo "Primer Criterio de Busqueda</td><td id=tipoT05 align=center><input type='TEXT' name='wcri1' id=cri1 size=30 maxlength=30>";
				echo "</td>";
				echo "<td id=tipoT05 align=center>";
				echo "Seleccion de Primer Criterio</td><td id=tipoT05 align=center><select name='wcsel1' id=wcsel1 onChange='Seleccion(1)'>";
				echo "<option>0-SELECCIONE</option>";
				echo "</select></td></tr>";
				echo "<tr><td id=tipoT05 align=center>";
				echo "Segunda Variable de Busqueda</td><td id=tipoT05 align=center><select name='wclass2' id=wclass2 onChange='llenar(2)'>";
				echo "<option>0-SELECCIONE</option>";
				for ($i=0;$i<7;$i++)
				{
					if($wclass2 == $variables[$i])
						echo "<option selected value='".$variables[$i]."'>".$variables[$i]."</option>";
					else
						echo "<option value='".$variables[$i]."'>".$variables[$i]."</option>";
				}
				echo "</select></td><td id=tipoT05 align=center>";
				if(isset($wcri2) and $wcri2 != "")
					echo "Segundo Criterio de Busqueda</td><td id=tipoT05 align=center><input type='TEXT' name='wcri2' value='".$wcri2."' id=cri2 size=30 maxlength=30>";
				else
					echo "Segundo Criterio de Busqueda</td><td id=tipoT05 align=center><input type='TEXT' name='wcri2' id=cri2 size=30 maxlength=30>";
				echo "<td id=tipoT05 align=center>";
				echo "Seleccion de Segundo Criterio</td><td id=tipoT05 align=center><select name='wcsel2' id=wcsel2 onChange='Seleccion(2)'>";
				echo "<option>0-SELECCIONE</option>";
				echo "</select></td></tr>";
				echo "<tr><td id=tipoT05 align=center colspan=2><input type='button' name='INICIAR' value='INICIAR CRITERIOS'  onClick='clean()'></td><td id=tipoT05 align=center colspan=2><input type='submit' value='BUSCAR'></td>";
				if(isset($SHCE))
					echo "<td id=tipoT05 align=center colspan=2>PACIENTES HOSPITALIZADOS SIN 	 <input type='checkbox' name='SHCE' checked class=tipo4 onClick='enter()'></td></tr>";
				else
					echo "<td id=tipoT05 align=center colspan=2>PACIENTES HOSPITALIZADOS SIN HISTORIA <input type='checkbox' name='SHCE' class=tipo4 onClick='enter()'></td></tr>";
				echo "<tr><td id=tipoT05 align=center colspan=6>";
				if(isset($x) and $x == 0)
					echo "<input type='RADIO' name=x value=0 checked onClick='enter()'>Todos&nbsp;&nbsp;";
				else
					echo "<input type='RADIO' name=x value=0 onClick='enter()'>Todos&nbsp;&nbsp;";
				if(isset($x) and $x == 1)
					echo "<input type='RADIO' name=x value=1 checked onClick='enter()'>Hospitalizados&nbsp;&nbsp;";
				else
					echo "<input type='RADIO' name=x value=1 onClick='enter()'>Hospitalizados&nbsp;&nbsp;";
				if(isset($x) and $x == 2)
					echo "<input type='RADIO' name=x value=2 checked onClick='enter()'>Activos No Hospitalizados&nbsp;&nbsp;";
				else
					echo "<input type='RADIO' name=x value=2 onClick='enter()'>Activos No Hospitalizados&nbsp;&nbsp;";
				if(isset($x) and $x == 3)
					echo "<input type='RADIO' name=x value=3 checked onClick='enter()'>Egresados Hace Menos de 6 Horas&nbsp;&nbsp;";
				else
					echo "<input type='RADIO' name=x value=3 onClick='enter()'>Egresados Hace Menos de 6 Horas&nbsp;&nbsp;";
				if(isset($x) and $x == 4)
					echo "<input type='RADIO' name=x value=4 checked onClick='enter()'>Fallecidos</td></tr>";
				else
					echo "<input type='RADIO' name=x value=4 onClick='enter()'>Fallecidos</td></tr>";
				echo "</table>";
				echo "<table border=0 align=center id=tipo5>";
				echo "<tr><td id=tipoT04 align=center>FECHA<BR>INGRESO</td><td id=tipoT04 align=center>SERVICIO<BR>ACTUAL</td><td id=tipoT04 align=center>HABITACION</td><td id=tipoT04 align=center>HISTORIA<BR>CLINICA</td><td id=tipoT04 align=center>NOMBRE<br>PACIENTE</td><td id=tipoT04 align=center>RESPONSABLE</td><td id=tipoT04 align=center>MEDICO(s)<BR>TRATANTE(s)</td><td id=tipoT04 align=center>ESPECIALIDAD(es)</td><td id=tipoT04 align=center>IGP</td></tr>";
				if(isset($wclass1) and isset($wcri1) and $wcri1 != "")
					$wcriterio1 = substr($wclass1,0,1);
				if(isset($wclass2) and isset($wcri2) and $wcri2 != "")
					$wcriterio2 = substr($wclass2,0,1);
				$wcont=0;
				if((!isset($wcri1) and !isset($wcri2)))
				{
					$WSA="";
					$WRE="";
					$WME="";
					$WES="";
				}
				
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					//$row[16]=partir($row[16]);
					$whisa=$row[0];
					$winga=$row[1];
					while(strlen($whisa) < 12) $whisa = "0".$whisa;
					while(strlen($winga) < 8) $winga = "0".$winga;
					$pos=bi($MEDESP,$kme,$whisa.$winga);
					if($pos != -1)
					{
						$wmed = $MEDESP[$pos][1];
						$wesp = $MEDESP[$pos][2];
					}
					else
					{
						$wmed="";
						$wesp="";
					}
					if(isset($WSA) and strpos($WSA,$row[10]."-".$row[11]) === false and !isset($wcri1) and !isset($wcri2))
					{
						if($WSA == "")
							$WSA .= $row[10]."-".$row[11];
						else
							$WSA .= "|".$row[10]."-".$row[11];
					}
					if($row[13] == "")
						$row[13] = " ";
					if(isset($WRE) and strpos($WRE,$row[13]) === false and !isset($wcri1) and !isset($wcri2))
					{
						if($WRE == "")
							$WRE .= $row[13];
						else
							$WRE .= "|".$row[13];
					}
					if($wmed != "" and !isset($wcri1) and !isset($wcri2))
					{
						$wap=explode("<br>",$wmed);	
						for ($ji=0;$ji<count($wap);$ji++)
						{
							//if(strpos($WME,$wap[$ji]) === false)
							if(!search1($WME,$wap[$ji]))
							{
								if($WME == "")
									$WME .= $wap[$ji];
								else
									$WME .= "|".$wap[$ji];
							}
						}
					}
					if($wesp != "" and !isset($wcri1) and !isset($wcri2))
					{
						$wap=explode("<br>",$wesp);	
						for ($ji=0;$ji<count($wap);$ji++)
						{
							if(strpos($wap[$ji],"-") !== false)
							{
								$wap[$ji]=substr($wap[$ji],strpos($wap[$ji],"-")+1);
								//if(strpos($WES,$wap[$ji]) === false)
								if(!search1($WES,$wap[$ji]))
								{
									if($WES == "")
										$WES .= $wap[$ji];
									else
										$WES .= "|".$wap[$ji];
								}
							}
						}
					}
					if(!isset($wcri1) and !isset($wcri2))
					{
						if($WSA != "")
						{
							$wap=explode("|",$WSA);				
							sort($wap);
							$WSA=implode("|",$wap);
						}
						if($WRE != "")
						{
							$wap=explode("|",$WRE);				
							sort($wap);
							$WRE=implode("|",$wap);
						}
						if($WME != "")
						{
							$wap=explode("|",$WME);				
							sort($wap);
							$WME=implode("|",$wap);
						}
						if($WES != "")
						{
							$wap=explode("|",$WES);				
							sort($wap);
							$WES=implode("|",$wap);
						}
					}
					$nombre=$row[6]." ".$row[7]." ".$row[4]." ".$row[5];
					$criterios[0] = $row[10]."-".$row[11];
					$criterios[1] = $row[16];
					$criterios[2] = $row[0]."-".$row[1];
					$criterios[3] = $nombre;
					$criterios[4] = $row[12];
					$criterios[5] = $wmed;
					$criterios[6] = $wesp;
					$criterios[7] = $row[12]."-".$row[13];
					$especial=0;
					if(isset($wcriterio1) and $wcriterio1 != "1" and $wcriterio1 != "5" and $wcriterio1 != "7" and $Semp != "NO APLICA" and $Semp != "*" and count($SempT) > 0 and $Sroot == "off")
					{
						$especial=1;
						$posB=$especial+3;
					}
					if(isset($wcriterio1) and $wcriterio1 != "1" and $wcriterio1 != "5" and $wcriterio1 != "7" and count($Suni) > 0 and $Sroot == "off" and count($SempT) == 0)
					{
						$especial=2;
						$posB=$especial-2;
					}
					if(isset($wcriterio1) and $wcriterio1 != "1"  and $wcriterio1 != "5" and $wcriterio1 != "7" and $Sroot == "off" and count($Sesp) > 0 and count($Suni) == 0 and count($SempT) == 0)
					{
						$especial=3;
						$posB=$especial+3;
					}
					if(isset($wcriterio2) and $wcriterio1 != "1" and $wcriterio2 != "5" and $wcriterio2 != "7" and $Semp != "NO APLICA" and $Semp != "*" and count($SempT) > 0 and $Sroot == "off")
					{
						$especial=1;
						$posB=$especial+3;
					}
					if(isset($wcriterio2) and $wcriterio2 != "1" and $wcriterio2 != "5" and $wcriterio2 != "7" and count($Suni) > 0 and $Sroot == "off" and count($SempT) == 0)
					{
						$especial=2;
						$posB=$especial-2;
					}
					if(isset($wcriterio2) and $wcriterio1 != "1" and $wcriterio2 != "5" and $wcriterio2 != "7" and $Sroot == "off" and count($Sesp) > 0 and count($Suni) == 0 and count($SempT) == 0)
					{
						$especial=3;
						$posB=$especial+3;
					}
					if((!isset($wcriterio1) or $wcriterio1 == "1" or $wcriterio1 == "5" or $wcriterio1 == "7") and $Semp != "NO APLICA" and $Semp != "*" and count($SempT) > 0 and $Sroot == "off")
					{
						$especial=1;
						$posB=$especial+3;
						if(!isset($wcriterio1))
							$wcriterio1 = "5";
					}
					if((!isset($wcriterio1) or $wcriterio1 == "1" or $wcriterio1 == "5" or $wcriterio1 == "7") and $Sroot == "off" and count($Suni) > 0 and count($SempT) == 0)
					{
						$especial=2;
						$posB=$especial-2;
						if(!isset($wcriterio1))
							$wcriterio1 = "1";
					}
					
					if((!isset($wcriterio1) or $wcriterio1 == "1" or $wcriterio1 == "5" or $wcriterio1 == "7") and $Sroot == "off" and count($Sesp) > 0 and count($Suni) == 0 and count($SempT) == 0)
					{
						$especial=3;
						$posB=$especial+3;
						if(!isset($wcriterio1))
							$wcriterio1 = "7";
					}
					if((!isset($wcriterio2) or $wcriterio2 == "1" or $wcriterio2 == "5" or $wcriterio2 == "7") and $Semp != "NO APLICA" and $Semp != "*" and count($SempT) > 0 and $Sroot == "off")
					{
						$especial=1;
						$posB=$especial+3;
						if(!isset($wcriterio2))
							$wcriterio2 = "5";
					}
					if((!isset($wcriterio2) or $wcriterio2 == "1" or $wcriterio2 == "5" or $wcriterio2 == "7") and $Sroot == "off" and count($Suni) > 0 and count($SempT) == 0)
					{
						$especial=2;
						$posB=$especial-2;
						if(!isset($wcriterio2))
							$wcriterio2 = "1";
					}
					if((!isset($wcriterio2) or $wcriterio2 == "1" or $wcriterio2 == "5" or $wcriterio2 == "7") and $Sroot == "off" and count($Sesp) > 0 and count($Suni) == 0 and count($SempT) == 0)
					{
						$especial=3;
						$posB=$especial+3;
						if(!isset($wcriterio2))
							$wcriterio2 = "7";
					}
					
					$wparurg = 0;
					if($row[11] == "URGENCIAS" and $medurg == 1)
						$wparurg = 1;
					if($especial == 0 and !isset($posB))
						$posB=$especial;
					if(isset($wcriterio1))
					{
						$pos=(integer)$wcriterio1 - 1;
						if($pos == 4)
							$pos = 7;
						if((isset($wcri1) and @strpos(strtoupper($criterios[$pos]),strtoupper($wcri1)) !== false and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])) or ($especial > 0 and (!isset($wcri1) or $wcri1 == "") and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])))
						{
							if(isset($wcriterio2))
							{
								$pos=(integer)$wcriterio2 - 1;
								if((isset($wcri2) and @strpos(strtoupper($criterios[$pos]),strtoupper($wcri2)) !== false and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])) or ($especial > 0 and (!isset($wcri2) or $wcri2 == "")  and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])))
								{
									$wcont++;
									if($wcont % 2 == 0)
										$tipo="tipo18";
									else
										$tipo="tipo19";
									if(isset($IPTIP) and $IPTIP == "off")
										$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wservicio=*&protocolos=0&CLASE=C";
									else
									{
										$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$wdbhce."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."";
										$path1="/matrix/HCE/procesos/HCE_IGP.php?wemp_pmla=".$wemp_pmla."&empresa=".$wdbhce."&wcedula=".$row[2]."&wtipodoc=".$row[3];
									}
									echo "<tr style='cursor: hand;cursor: pointer;'><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[17]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[10]."-".$row[11]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo.">".$row[16]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo.">".$row[0]."-".$row[1]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$nombre."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[12]."-".$row[13]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$wmed."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$wesp."</td><td onclick='IGP(".chr(34).$path1.chr(34).")' id=".$tipo."A><IMG SRC='/matrix/images/medical/hce/Man.png'></td></tr>";
								}
							}
							else
							{
								$wcont++;
								if($wcont % 2 == 0)
									$tipo="tipo18";
								else
									$tipo="tipo19";
								if(isset($IPTIP) and $IPTIP == "off")
									$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wservicio=*&protocolos=0&CLASE=C";
								else
								{
									$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$wdbhce."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."";
									$path1="/matrix/HCE/procesos/HCE_IGP.php?wemp_pmla=".$wemp_pmla."&empresa=".$wdbhce."&wcedula=".$row[2]."&wtipodoc=".$row[3];
								}
								echo "<tr style='cursor: hand;cursor: pointer;'><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[17]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[10]."-".$row[11]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo.">".$row[16]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo.">".$row[0]."-".$row[1]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$nombre."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[12]."-".$row[13]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$wmed."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$wesp."</td><td onclick='IGP(".chr(34).$path1.chr(34).")' id=".$tipo."A><IMG SRC='/matrix/images/medical/hce/Man.png'></td></tr>";
							}
						}
					}
					else
					{	
						$wcont++;
						if($wcont % 2 == 0)
							$tipo="tipo18";
						else
							$tipo="tipo19";
						if(isset($IPTIP) and $IPTIP == "off")
							$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wservicio=*&protocolos=0&CLASE=C";
						else
						{
							$path="/matrix/HCE/procesos/HCE_iFrames.php?wemp_pmla=".$wemp_pmla."&empresa=".$wdbhce."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."";
							$path1="/matrix/HCE/procesos/HCE_IGP.php?wemp_pmla=".$wemp_pmla."&empresa=".$wdbhce."&wcedula=".$row[2]."&wtipodoc=".$row[3];
						}
						echo "<tr style='cursor: hand;cursor: pointer;'><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[17]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[10]."-".$row[11]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo.">".$row[16]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo.">".$row[0]."-".$row[1]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$nombre."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$row[12]."-".$row[13]."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$wmed."</td><td onclick='ejecutar(".chr(34).$path.chr(34).",".$wparurg.")' id=".$tipo."A>".$wesp."</td><td onclick='IGP(".chr(34).$path1.chr(34).")' id=".$tipo."A><IMG SRC='/matrix/images/medical/hce/Man.png'></td></tr>";
					}
				}
			}
			if(isset($WSA))
				echo "<input type='HIDDEN' name= 'WSA' value='".$WSA."' id=WSA>";
			if(isset($WRE))
				echo "<input type='HIDDEN' name= 'WRE' value='".$WRE."' id=WRE>";
			if(isset($WME))
				echo "<input type='HIDDEN' name= 'WME' value='".$WME."' id=WME>";
			if(isset($WES))
				echo "<input type='HIDDEN' name= 'WES' value='".$WES."' id=WES>";
			if($wcont == 0)
				echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;NO SE ENCONTRARON REGISTROS CON EL CRITERIO DE BUSQUEDA ESTABLECIDO</td></tr>";
			echo "</table></center>";
		}
		else
		{
			echo "<table border=0 align=center id=tipo5>";
			echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;USUARIO NO REGISTRADO PARA ACCESO A LA HISTORIA CLINICA</td></tr>";
			echo "</table></center>";
		}
	}
	else
	{
		if($IPOK == 0)
		{
			echo "<table border=0 align=center id=tipo5>";
			echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LA HISTORIA CLINICA NO PUEDE SER USADA FUERA DE LA INSTITUCION !!!</td></tr>";
			echo "</table></center>";
		}
		else
		{
			echo "<table border=0 align=center id=tipo5>";
			echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;SU FIRMA ELECTRONICA HA VENCIDO, POR FAVOR ACTUALICELA EN : <A HREF='/matrix/hce/procesos/PassHCE.php?wemp_pmla=".$wemp_pmla."&empresa=".$wdbhce."'>Actualizar Firma</A></td></tr>";
			echo "</table></center>";
		}
	}
}
?>
</body>
</html>
