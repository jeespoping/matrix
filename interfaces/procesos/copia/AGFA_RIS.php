<html>
<head>
  	<title>MATRIX Actualizacion de Pacientes en RIS</title>
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
    <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
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
    	
    	#tipoT00{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:left;}
    	#tipoT01{color:#000000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:15em;text-align:center;}
    	#tipoT02{color:#000000;background:#C3D9FF;font-size:14pt;font-family:Arial;font-weight:normal;width:110em;text-align:left;}
    	#tipoT03{color:#000000;background:#E8EEF7;font-size:7pt;font-family:Arial;font-weight:normal;width:110em;text-align:right;height:2em;}
    	
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
    	#tipoM01{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	#tipoM02{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;width:40em;text-align:left;height:3em;}
    	
    </style>
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.AGFA_RIS.submit();
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
		if ((event.keyCode < 65 || event.keyCode > 90 ) & event.keyCode != 32 & event.keyCode != 13) event.returnValue = false;
	}
	function teclado5()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & (event.keyCode < 65 || event.keyCode > 90 ) & event.keyCode != 45 & event.keyCode != 13) event.returnValue = false;
	}

//-->
</script>
<?php
include_once("conex.php");

/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : agfa_ris.php
	   Fecha de Liberacion : 2009-06-25
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2014-07-23
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite actualizar la base de datos
	   de pacientes del RIS (Sistema de Informacion de Radiologia)
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2014-07-23
			Se Modifico el programa para incluir el telefono celular del paciente.
			
	   .2014-01-28
			Se Modifico el programa para incluir la fuente "OR" de Ortopedia en la busqueda de la tabla aymov.
			
	   .2012-08-16
			Se Modifico el programa para incluir en la busqueda ODBC la tabla inpac.
			
	   .2012-05-09
	   		Se modifico en el programa para tener en cuenta los campos nulos de la consulta a informix.
	   		
	   .2010-06-22
	   		Se modifico en el programa para NO permitir nombres de pacientes de mas de 30 caracteres y con espacios en
	   		blanco de sobra. Esta modificacion se realizo en la grabacion de los mensajes ADT A04 y A08.
	   		
	   .2010-05-13
	   		Se modifico en el programa para NO permitir cambiar los datos correspondientes al medico MED00000.
	   		Que implica que no hay medico visible en la orden y por tanto NO se deba enviar un mensaje MFN.
	   		
	   .2010-05-10
	   		Se modifico en el programa para cambiar la validacion de los datos demograficos del medico haciendolos mas restrictivos
	   		y se adiciono el medico MED00000 que implica que no hay medico visible en la orden y por tanto NO se deba enviar
	   		un mensaje MFN.
	   		
	   .2010-04-05
	   		Se modifico en el programa para cambiar la validacion de la existencia de pacientes, verificando la existencia
	   		de un mismo documento con mas de un tipo de documento.
	   		
	   .2010-01-06
	   		Se modifico en el programa para la grabacion de la fecha de nacimiento en los mensajes HL7 para asegurarse de 
	   		que el dia y el mes sean  < 10  tengan 2 digitos.
	   		
	   .2009-11-11
	   		Se modifico en el programa para notificarle al usuario que existe un documento con tipo de documento diferente 
	   		al digitado.
	   		
	   .2009-10-01
	   		Se modifico en el programa para que la inicializaci�n de la fecha de nacimiento se "0000-00-00".
	   		
	   .2009-09-30
	   		Se modifico en el programa la consulta a la tabla aymov de Servinte para que se considerara la fuente RA ,la
	   		fuente RX (Unidad Basica) y la fuente SF (San Fernando).
	   		Adicionalmente se definieron (soltero y La Mota) como los default para estado civil y barrio.
	   		
	   .2009-09-11
	   		Se modifico el programa para que NO permitiera digitar en los campos de nombres y apellidos del paciente
	   		y del medico remitente caracteres diferentes a letras.
	   		
	   .2009-09-10
	   		Se modifico el programa para que permitiera digitar en el campo documento letras y el signo guion (-).
	   		
	   .2009-09-07
	   		Se modificaro el programa para que cuando el paciente se consulte por tipo de documento e identificacion y
	   		NO exista limpie los campos restantes.
	   		Se le agrego al programa la opcion para traer de Servinte los datos demograficos basicos cuando este no exista
	   		en la base de pacientes de radiologia.
	   		Se corrigio la opcion anterior ya que no estaba funcionando en forma adecuada.
	   		
	   .2009-07-31
	   		Se modificaro el programa para que cuando el pais sea diferente a colombia coloque en municipio y barrio el
	   		pais y para que cuando en general el barrio no exista, coloque el municipio.
	   		
	   .2009-07-28
	   		Se modificaron los mensajes de validacion de la direccion y el telefono que estaban incorrectos.
	   		
	   .2009-07-27
	   		Se cambiaron las rutas de los iconos del grupo AGFA al de INTERFACES.
	   		
	   .2009-07-06
	   		Se corrigio el programa para poder modificar los datos de un paciente ya ingresado, ya que cuando el paciente
	   		ya existia no permitia modificar los datos del primer ingreso.
	   		
	   .2009-07-02
	   		Se modifico el programa para crear los mensaje para el RIS en estado off.
	   		
	   .2009-06-25
	   		Release de Versi�n Beta.
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
		if($occur[1] < 0 or $occur[1] >24 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}

function valgen($ok,$conex,$wtdo,$wdoc,$wnom,$wap1,$wap2,$wfna,$wsex,$west,$wdir,$wtel,$wcel,$wpai,$wmun,$wbar,$wzon,$wofi,$wres,$wmed,$wmecd,$wmeno,$wmea1,$wmea2,$wmeem,$wmete,$wmece,$wmedi,&$werr,&$e)
{
	global $empresa;
	//VALIDACION DE DATOS GENERALES
	if($wtdo == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO TIPO DE DOCUMENTO";
	}
	if($wsex == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO SEXO";
	}
	if($west == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO ESTADO CIVIL";
	}
	if($wpai == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO PAIS";
	}
	if($wmun == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO MUNICIPIO";
	}
	if($wbar == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO BARRIO";
	}
	if($wzon == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO ZONA";
	}
	if($wofi == "0-SELECCIONE")
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO OFICIO";
	}
	if($wmed == "0-SELECCIONE")
	{
		$query = "SELECT Medcod FROM ".$empresa."_000003 ";
		$query .= "where Medcod = '".$wmecd."'";
		$err = mysql_query($query,$conex) or die ( mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num == 0)
		{
			if(!validar4($wmeno) or strlen($wmeno) < 3 or $wmeno == "   ")
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO NOMBRE DEL MEDICO";
			}
			if(!validar4($wmea1) or strlen($wmeno) < 3 or $wmeno == "   ")
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO PRIMER APELLIDO DEL MEDICO";
			}
			if(!validar4($wmea2) or strlen($wmeno) < 3 or $wmeno == "   ")
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO SEGUNDO APELLIDO DEL MEDICO";
			}
			if(!validar4($wmeem))
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO EL E-MAIL DEL MEDICO";
			}
			if(!validar4($wmete))
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO EL TELEFONO DEL MEDICO";
			}
			if(!validar4($wmece))
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO EL CELULAR DEL MEDICO";
			}
			if(!validar4($wmedi))
			{
				$e=$e+1;
				$werr[$e]="ERROR O NO DIGITO LA DIRECCION DEL MEDICO";
			}
		}
		else
		{
			$e=$e+1;
			$werr[$e]="ERROR O NO DIGITO MEDICO O YA EXISTE - SELECCIONELO";
		}
	}
	if(!validar4($wdoc))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO DOCUMENTO DEL PACIENTE";
	}
	if(!validar4($wnom))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO NOMBRE DEL PACIENTE";
	}
	if(!validar4($wap1))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO EL PRIMER APELLIDO DEL PACIENTE";
	}
	if(!validar4($wap2))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO EL SEGUNDO APELLIDO DEL PACIENTE";
	}
	if(!validar3($wfna) or $wfna == "0000-00-00")
	{
		$e=$e+1;
		$werr[$e]="ERROR NO DIGITO FECHA DE NACIMIENTO O ESTA INCORRECTA";
	}
	if(!validar4($wdir))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO CORRECTAMENTE LA DIRECCION";
	}
	if(!validar4($wtel))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO CORRECTAMENTE EL TELEFONO";
	}
	if(!validar2($wcel))
	{
		$e=$e+1;
		$werr[$e]="ERROR O NO DIGITO CORRECTAMENTE EL CELULAR";
	}
		
	if($e == -1)
		return true;
	else
		return false;
}



//FUNCION DE INGRESO DE PACIENTES
function GRABAR($key,$conex,$wtdo,$wdoc,$wnom,$wap1,$wap2,$wfna,$wsex,$west,$wdir,$wtel,$wcel,$wpai,$wmun,$wbar,$wzon,$wofi,$wres,$wmed,$wmecd,$wmeno,$wmea1,$wmea2,$wmeem,$wmete,$wmece,$wmedi,&$werr,&$e)
{
	global $empresa;
	$ESTADO="INDEFINIDO";
	$query = "lock table ".$empresa."_000002 LOW_PRIORITY WRITE,".$empresa."_000003 LOW_PRIORITY WRITE , ".$empresa."_000004 LOW_PRIORITY WRITE, ".$empresa."_000006 LOW_PRIORITY WRITE ";
	$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVOS  : ".mysql_errno().":".mysql_error());
	$wsw=0;
	//VALIDACION DE EXISTENCIA DEL MEDICO
	$query = "SELECT count(*) FROM ".$empresa."_000003 ";
	$query .= "where Medcod = '".substr($wmed,0,strpos($wmed,"-"))."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_array($err);
    	if ($row[0] > 0)
    		$wsw = 1;
	}
	if($wsw == 0)
	{
		$query =  " update ".$empresa."_000006 set Concon = Concon + 1 where Contip='MED' ";
		$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE MEDICOS");
		$query = "select Concon from ".$empresa."_000006 where Contip='MED' ";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE MEDICOS");
		$row = mysql_fetch_array($err1);
		$Kons=(string)$row[0];
		while(strlen($Kons) < 5)
			$Kons = "0".$Kons;
		$Kons = "MED".$Kons;
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000003 (medico,fecha_data,hora_data, Medcod, Mednom, Medap1, Medap2, Medema, Medtel, Medcel, Meddir, Medact, Seguridad) values ('";
		$query .=  $empresa."','";
		$query .=  $fecha."','";
		$query .=  $hora."','";
		$query .=  $Kons."','";
		$query .=  $wmeno."','";
		$query .=  $wmea1."','";
		$query .=  $wmea2."','";
		$query .=  $wmeem."','";
		$query .=  $wmete."','";
		$query .=  $wmece."','";
		$query .=  $wmedi."',";
		$query .=  "'on','C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO MEDICO : ".mysql_errno().":".mysql_error());
		$medico=$Kons;
		$ESTMED="MAD";
	}
	else
	{
		if (substr($wmed,0,strpos($wmed,"-")) != "MED00000")
		{
			$query  =  " update ".$empresa."_000003 set ";		
			$query .=  " Mednom = '".$wmeno."',";
			$query .=  " Medap1 = '".$wmea1."',";
			$query .=  " Medap2 = '".$wmea2."',";
			$query .=  " Medema = '".$wmeem."',";
			$query .=  " Medtel = '".$wmete."',";
			$query .=  " Medcel = '".$wmece."',";
			$query .=  " Meddir = '".$wmedi."' ";
			$query .=  " where Medcod='".substr($wmed,0,strpos($wmed,"-"))."' ";
			$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO MEDICO : ".mysql_errno().":".mysql_error());
		}
		$medico=$wmecd;
		$ESTMED="MUP";
	}
	$wsw=0;
	//VALIDACION DE EXISTENCIA DEL PACIENTE
	$query = "SELECT count(*) FROM ".$empresa."_000002 ";
	$query .= "where Pactdc = '".substr($wtdo,0,strpos($wtdo,"-"))."'";
	$query .= "  and Pacdoc = '".$wdoc."' ";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_array($err);
    	if ($row[0] > 0)
    		$wsw = 1;
	}
	if($wsw == 0)
	{
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000002 (medico,fecha_data,hora_data, Pactdc, Pacdoc, Pacnom, Pacap1, Pacap2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Paccel, Pacpai, Pacmun, Pacbar, Paczon, Pacofi, Pacres, Pacmed, Pacact, Seguridad) values ('";
		$query .=  $empresa."','";
		$query .=  $fecha."','";
		$query .=  $hora."','";
		$query .=  substr($wtdo,0,strpos($wtdo,"-"))."','";
		$query .=  $wdoc."','";
		$query .=  $wnom."','";
		$query .=  $wap1."','";
		$query .=  $wap2."','";
		$query .=  $wfna."','";
		$query .=  substr($wsex,0,strpos($wsex,"-"))."','";
		$query .=  substr($west,0,strpos($west,"-"))."','";
		$query .=  $wdir."','";
		$query .=  $wtel."','";
		$query .=  $wcel."','";
		$query .=  substr($wpai,0,strpos($wpai,"-"))."','";
		$query .=  substr($wmun,0,strpos($wmun,"-"))."','";
		$query .=  substr($wbar,0,strpos($wbar,"-"))."','";
		$query .=  substr($wzon,0,strpos($wzon,"-"))."','";
		$query .=  substr($wofi,0,strpos($wofi,"-"))."','";
		$query .=  substr($wres,0,strpos($wres,"-"))."','";
		$query .=  $medico."',";
		$query .=  "'on','C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO PACIENTE : ".mysql_errno().":".mysql_error());
		$ESTADO="A04";
	}
	else
	{
		$query  =  " update ".$empresa."_000002 set ";
		$query .=  " Pacnom = '".$wnom."',";
		$query .=  " Pacap1 = '".$wap1."',";
		$query .=  " Pacap2 = '".$wap2."',";
		$query .=  " Pacfna = '".$wfna."',";
		$query .=  " Pacsex = '".substr($wsex,0,strpos($wsex,"-"))."',";
		$query .=  " Pacest = '".substr($west,0,strpos($west,"-"))."',";
		$query .=  " Pacdir = '".$wdir."',";
		$query .=  " Pactel = '".$wtel."',";
		$query .=  " Paccel = '".$wcel."',";
		$query .=  " Pacpai = '".substr($wpai,0,strpos($wpai,"-"))."',";
		$query .=  " Pacmun = '".substr($wmun,0,strpos($wmun,"-"))."',";
		$query .=  " Pacbar = '".substr($wbar,0,strpos($wbar,"-"))."',";
		$query .=  " Paczon = '".substr($wzon,0,strpos($wzon,"-"))."',";
		$query .=  " Pacofi = '".substr($wofi,0,strpos($wofi,"-"))."',";
		$query .=  " Pacres = '".substr($wres,0,strpos($wres,"-"))."',";
		$query .=  " Pacmed = '".$medico."',";
		$query .=  " Pacact = 'on' where Pactdc='".substr($wtdo,0,strpos($wtdo,"-"))."' and Pacdoc='".$wdoc."' ";
		$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO PACIENTE : ".mysql_errno().":".mysql_error());
		$ESTADO="A08";
	}
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	$query = "insert ".$empresa."_000004 (medico,fecha_data,hora_data, Relfec, Relhor, Reltdo, Reldoc, Relcom, Seguridad) values ('";
	$query .=  $empresa."','";
	$query .=  $fecha."','";
	$query .=  $hora."','";
	$query .=  $fecha."','";
	$query .=  $hora."','";
	$query .=  substr($wtdo,0,strpos($wtdo,"-"))."','";
	$query .=  $wdoc."','";
	$query .=  $medico."',";
	$query .=  "'C-".$empresa."')";
	$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ARCHIVO DE RELACION MEDICO-PACIENTE : ".mysql_errno().":".mysql_error());
	$e=$e+1;
	$werr[$e]="OK! DATOS ACTUALIZADOS ";
	$query = " UNLOCK TABLES";
	$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());	
	$query = "lock table ".$empresa."_000006  WRITE ";
	$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO  : ".mysql_errno().":".mysql_error());
	if($ESTADO == "A04")
	{
		//MENSAJE ADT_A04 INGRESO DE PACIENTES
		$query =  " update ".$empresa."_000006 set Concon = Concon + 1 where Contip='ADT_A04' ";
		$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE MENSAJES");
		$query = "select Concon from ".$empresa."_000006 where Contip='ADT_A04' ";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE MENSAJES");
		$row = mysql_fetch_array($err1);
		$ProcessingID=$row[0];
		$query = " UNLOCK TABLES";
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
		$fecna=explode("-",$wfna);
		if(strlen($fecna[1]) < 2)
			$fecna[1] = "0".$fecna[1];
		if(strlen($fecna[2]) < 2)
			$fecna[2] = "0".$fecna[2];
		$wapg=trim($wap1)." ".trim($wap2);
		$wapg=substr($wapg,0,30);
		$wnom=trim($wnom);
		$wnom=substr($wnom,0,30);
		$texto  = "MSH|^~\\\&|HIS|MATRIX|QDOC|AGFA Healthcare Colombia|".date("YmdHis")."||ADT^A04|".$ProcessingID."|P|2.4|||||CO|8859/1||".chr(13);
		$texto .= "EVN|A04|".date("YmdHis")."|".date("YmdHis")."|||".chr(13);
		$texto .= "PID||".substr($wtdo,0,strpos($wtdo,"-")).$wdoc."|".substr($wtdo,0,strpos($wtdo,"-")).$wdoc."||".$wapg."^".$wnom." ||".$fecna[0].$fecna[1].$fecna[2]."000000|".substr($wsex,0,strpos($wsex,"-"))."|||".$wdir."^^".substr($wmun,strpos($wmun,"-")+1)."^^".substr($wmun,0,strpos($wmun,"-"))."^CO^P||".$wtel."|".$wcel."||".substr($west,0,strpos($west,"-"))."|||||||||||||||".chr(13);
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000001(medico, fecha_data, hora_data, Numero, Tipo, Clase, Mensaje, Contador, Estado, Estado_servinte, seguridad)";
		$query .= " values ('AGFA','".$fecha."','".$hora."',".$ProcessingID.",'ADT_A04','O','".$texto."',0,'off','off','C-AGFA')";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."ERROR GRABANDO MENSAJE");

	}
	else
	{
		//MENSAJE ADT_A08 ACTUALIZACION DATOS DE PACIENTES
		$query =  " update ".$empresa."_000006 set Concon = Concon + 1 where Contip='ADT_A08' ";
		$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE MENSAJES");
		$query = "select Concon from ".$empresa."_000006 where Contip='ADT_A08' ";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE MENSAJES");
		$row = mysql_fetch_array($err1) ;
		$ProcessingID=$row[0];
		$query = " UNLOCK TABLES";
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
		$fecna=explode("-",$wfna);
		if(strlen($fecna[1]) < 2)
			$fecna[1] = "0".$fecna[1];
		if(strlen($fecna[2]) < 2)
			$fecna[2] = "0".$fecna[2];
		$wapg=trim($wap1)." ".trim($wap2);
		$wapg=substr($wapg,0,30);
		$wnom=trim($wnom);
		$wnom=substr($wnom,0,30);
		$texto  = "MSH|^~\\\&|HIS|MATRIX|QDOC|AGFA Healthcare Colombia|".date("YmdHis")."||ADT^A08|".$ProcessingID."|P|2.4|||||CO|8859/1||".chr(13);
		$texto .= "EVN|A08|".date("YmdHis")."|".date("YmdHis")."|||".chr(13);
		$texto .= "PID||".substr($wtdo,0,strpos($wtdo,"-")).$wdoc."|".substr($wtdo,0,strpos($wtdo,"-")).$wdoc."||".$wapg."^".$wnom." ||".$fecna[0].$fecna[1].$fecna[2]."000000|".substr($wsex,0,strpos($wsex,"-"))."|||".$wdir."^^".substr($wmun,strpos($wmun,"-")+1)."^^".substr($wmun,0,strpos($wmun,"-"))."^CO^P||".$wtel."|".$wcel	."||".substr($west,0,strpos($west,"-"))."|||||||||||||||".chr(13);
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000001(medico, fecha_data, hora_data, Numero, Tipo, Clase, Mensaje, Contador, Estado, Estado_servinte, seguridad)";
		$query .= " values ('AGFA','".$fecha."','".$hora."',".$ProcessingID.",'ADT_A08','O','".$texto."',0,'off','off','C-AGFA')";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."ERROR GRABANDO MENSAJE");
	}
	if (substr($wmed,0,strpos($wmed,"-")) != "MED00000")
	{
		$query = "lock table ".$empresa."_000006  WRITE ";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO  : ".mysql_errno().":".mysql_error());
		//MENSAJE MFN_M02 INGRESO DE PACIENTES
		$query =  " update ".$empresa."_000006 set Concon = Concon + 1 where Contip='MFN_M02' ";
		$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE MENSAJES");
		$query = "select Concon from ".$empresa."_000006 where Contip='MFN_M02' ";
		$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE MENSAJES");
		$row = mysql_fetch_array($err1);
		$ProcessingID=$row[0];
		$query = " UNLOCK TABLES";
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
		$fecna=explode("-",$wfna);
		$activo=date("YmdHis");
		$ano=substr($activo,0,4);
		$ano=$ano+5;
		$activo=$ano.substr($activo,4);
		$texto  = "MSH|^~\\\&|HIS|MATRIX|QDOC|AGFA Healthcare Colombia|".date("YmdHis")."||MFN^M02|".$ProcessingID."|P|2.4|||||CO|8859/1||".chr(13);
		$texto .= "MFI|PRA||UPD|||".chr(13);
		$texto .= "MFE|".$ESTMED."|1|".date("YmdHis")."|".$medico.chr(13);
		$texto .= "STF|".$medico."|".$medico."|".$wmea1." ".$wmea2."^".$wmeno."^^^DR^M.D.|||||||^^^".$wmeem."^".$wmete."^".$wmece."|".$wmedi."||".$activo.chr(13);
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000001(medico, fecha_data, hora_data, Numero, Tipo, Clase, Mensaje, Contador, Estado, Estado_servinte, seguridad)";
		$query .= " values ('AGFA','".$fecha."','".$hora."',".$ProcessingID.",'MFN_M02','O','".$texto."',0,'off','off','C-AGFA')";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."ERROR GRABANDO MENSAJE");
	}

	return true;
}
		
//@session_start();
//if(!isset($_SESSION['user']))
//	echo "error";
//else
//{
	//$key = substr($user,2,strlen($user));
	$key = "agfa";
	echo "<form name='AGFA_RIS' action='AGFA_RIS.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	
	//******* INICIALIZACION DEL SISTEMA *********
	if(isset($ok) and $ok == 9)
		$ok=0;
	
	//******* GRABACION DE INFORMACION *********
	if(isset($ok) and $ok == 2)
	{
		$werr=array();
		$e=-1;
		if(valgen($ok,$conex,$wtdo,$wdoc,$wnom,$wap1,$wap2,$wfna,$wsex,$west,$wdir,$wtel,$wcel,$wpai,$wmun,$wbar,$wzon,$wofi,$wres,$wmed,$wmecd,$wmeno,$wmea1,$wmea2,$wmeem,$wmete,$wmece,$wmedi,&$werr,&$e))
		{
			if(GRABAR($key,$conex,$wtdo,$wdoc,$wnom,$wap1,$wap2,$wfna,$wsex,$west,$wdir,$wtel,$wcel,$wpai,$wmun,$wbar,$wzon,$wofi,$wres,$wmed,$wmecd,$wmeno,$wmea1,$wmea2,$wmeem,$wmete,$wmece,$wmedi,&$werr,&$e))
			{
				$ok=0;
				unset($wtdo);
				unset($wsex);
				unset($west);
				unset($wpai);
				unset($wmun);
				unset($wbar);
				unset($wzon);
				unset($wofi);
				unset($wres);
				unset($wmed);
			}
			if($ok != 0)
				$ok = 1;
		}
		else
			$ok=1;
	}
	
	
	//******* INICIALIZACION DE CAMPOS *********
	if(isset($ok) and $ok == 0)
	{
		unset($wtdo);
		unset($wsex);
		unset($west);
		unset($wpai);
		unset($wmun);
		unset($wbar);
		unset($wzon);
		unset($wofi);
		unset($wres);
		unset($wmed);
		$ok=1;
		if(!isset($wtdo))
			$wtdo="0-SELECCIONE";
		$wdoc="";
		$wnom="";
		$wap1="";
		$wap2="";
		$wfna="0000-00-00";
		if(!isset($wsex))
			$wsex="0-SELECCIONE";
		if(!isset($west))
			$west="0-SELECCIONE";
		$wdir="";
		$wtel="";
		$wcel="";
		if(!isset($wpai))
			$wpai="0-SELECCIONE";
		if(!isset($wmun))
			$wmun="0-SELECCIONE";
		$wmunx="";
		if(!isset($wbar))
			$wbar="0-SELECCIONE";
		$wbarx="";
		if(!isset($wzon))
			$wzon="0-SELECCIONE";
		if(!isset($wofi))
			$wmofi="0-SELECCIONE";
		$wofix="";
		if(!isset($wres))
			$wres="0-SELECCIONE";
		$wresx="";
		if(!isset($wmed))
			$wmed="0-SELECCIONE";
		$wmedx="";
		$wmecd="";
		$wmeno="";
		$wmea1="";
		$wmea2="";
		$wmeem="";
		$wmete="";
		$wmece="";
		$wmedi=""; 
	}
	
	//*******CONSULTA DE INFORMACION *********
	if(isset($ok)  and $ok == 3)
	{
		if(!isset($wtdo))
			$wtdo="0-SELECCIONE";
		if(!isset($wdoc))
			$wdoc = "";
		if(!isset($wnom))
			$wnom = "";
		if(!isset($wap1))
			$wap1 = "";
		if(!isset($wap2))
			$wap2 = "";
		if(isset($querys) and $querys != "")
		{
			$querys=stripslashes($querys);
			$qa=$querys;
		}
		else
		{
			            //       0       1       2       3       4       5       6      7       8       9       10      11      12      13       14     15      16      17     18
            $querys = "select Pactdc, Pacdoc, Pacnom, Pacap1, Pacap2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Pacpai, Pacmun, Pacbar, Paczon, Pacofi, Pacres, Pacmed, Pacact, paccel  from  ".$empresa."_000002 ";
			$querys .= " where Pacdoc != 0 ";
			if($wtdo != "0-SELECCIONE" and $wtdo != "")
				$querys .= "   and Pactdc ='".substr($wtdo,0,strpos($wtdo,"-"))."'";
			if($wdoc != "")
				$querys .= "     and Pacdoc = '".$wdoc."'";
			if($wnom != "")
				$querys .= "     and Pacnom like '%".$wnom."%'";
			if($wap1 != "")
				$querys .= "     and Pacap1 like '%".$wap1."%'";
			if($wap2 != "")
				$querys .= "     and Pacap2 like '%".$wap2."%'";
			$querys .=" Order by  Pacap1, Pacap2, Pacnom  ";
			$err = mysql_query($querys,$conex);
			$numero = mysql_num_rows($err);
			$numero=$numero - 1;
		}
		if ($numero>=0)
		{
			if(isset($wposs) and $wposs != 0)
			{
				$wpos = $wposs - 1;
				if ($wpos < 0)
					$wpos=0;
				if ($wpos > $numero)
					$wpos=$numero;
				$wposs=0;
			}
			if(isset($qa))
			{
				$qa=str_replace(chr(34),chr(39),$qa);
				$qa=substr($qa,0,strpos($qa," limit "));
				$querys=$qa;
			}
			if(isset($qa) and $qa == $querys)
			{
				if(isset($wb) and $wb == 1)
				{
					unset($querysR);
					$wpos = $wpos  + 1;
					if ($wpos > $numero)
						$wpos=$numero;
				}
				elseif(isset($wb) and $wb == 2)
				{
					unset($querysR);
					$wpos = $wpos - 1;
					if ($wpos < 0)
						$wpos=0;
				}
			}
			else
				$wpos=0;
			$wp=$wpos+1;
			//echo "Registro Nro : ".$wpos."<br>";
			$querys .=  " limit ".$wpos.",1";
			$err = mysql_query($querys,$conex);
			$querys=str_replace(chr(39),chr(34),$querys);
			echo "<input type='HIDDEN' name= 'querys' value='".$querys."'>";
			echo "<input type='HIDDEN' name= 'wpos' value='".$wpos."'>";
			echo "<input type='HIDDEN' name= 'numero' value='".$numero."'>";
			$row = mysql_fetch_array($err);
			$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[0]."' and Seltip='01' and Selest='on' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wtdo=$row[0]."-".$row1[0];
			}
			else
				$wtdo="0-SELECCIONE";
			$wdoc=$row[1];
			$wnom=$row[2];
			$wap1=$row[3];
			$wap2=$row[4];
			$wfna=$row[5];
			$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[6]."' and Seltip='03' and Selest='on' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wsex=$row[6]."-".$row1[0];
			}
			else
				$wsex="0-SELECCIONE";
			$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[7]."' and Seltip='04' and Selest='on' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$west=$row[7]."-".$row1[0];
			}
			else
				$west="0-SELECCIONE";
			$wdir=$row[8];
			$wtel=$row[9];
			$wcel=$row[18];
			$query = "SELECT Nombre from root_000006 where Codigo='".$row[10]."'  ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wpai=$row[10]."-".$row1[0];
			}
			else
				$wpai="0-SELECCIONE";
			$query = "SELECT Nombre from root_000006 where Codigo='".$row[11]."'  ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wmun=$row[11]."-".$row1[0];
			}
			else
				$wmun="0-SELECCIONE";
			$query = "SELECT Bardes  from root_000034 where Barcod='".$row[12]."'  ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wbar=$row[12]."-".$row1[0];
			}
			else
				$wbar="0-SELECCIONE";
			$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[13]."' and Seltip='05' and Selest='on' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wzon=$row[13]."-".$row1[0];
			}
			else
				$wzon="0-SELECCIONE";
			$query = "SELECT Descripcion  from root_000003 where Codigo='".$row[14]."' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wofi=$row[14]."-".$row1[0];
			}
			else
				$wofi="0-SELECCIONE";
			$query = "SELECT Empdes  from ".$empresa."_000005 where Empnit='".$row[15]."' ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$wres=$row[15]."-".$row1[0];
			}
			else
				$wres="0-SELECCIONE";
			$query = "SELECT Relcom from ".$empresa."_000004 where Reltdo='".$row[0]."' and  Reldoc='".$row[1]."' order by Relfec desc, Relhor desc ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$query = "SELECT  Medcod, Mednom, Medap1, Medap2, Medema, Medtel, Medcel, Meddir from ".$empresa."_000003 where Medcod='".$row1[0]."' and Medact='on' ";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row2 = mysql_fetch_array($err2);
				$wmed=$row2[0]."-".$row2[1];
				$wmecd=$row2[0];
				$wmeno=$row2[1];
				$wmea1=$row2[2];
				$wmea2=$row2[3];
				$wmeem=$row2[4];
				$wmete=$row2[5];
				$wmece=$row2[6];
				$wmedi=$row2[7]; 
			}
		}
		else
		{
			unset($wtdo);
			unset($wsex);
			unset($west);
			unset($wpai);
			unset($wmun);
			unset($wbar);
			unset($wzon);
			unset($wofi);
			unset($wres);
			unset($wmed);
			$ok=1;
			if(!isset($wtdo))
				$wtdo="0-SELECCIONE";
			$wdoc="";
			$wnom="";
			$wap1="";
			$wap2="";
			$wfna="0000-00-00";
			if(!isset($wsex))
				$wsex="0-SELECCIONE";
			if(!isset($west))
				$west="0-SELECCIONE";
			$wdir="";
			$wtel="";
			$wcel="";
			if(!isset($wpai))
				$wpai="0-SELECCIONE";
			if(!isset($wmun))
				$wmun="0-SELECCIONE";
			$wmunx="";
			if(!isset($wbar))
				$wbar="0-SELECCIONE";
			$wbarx="";
			if(!isset($wzon))
				$wzon="0-SELECCIONE";
			if(!isset($wofi))
				$wmofi="0-SELECCIONE";
			$wofix="";
			if(!isset($wres))
				$wres="0-SELECCIONE";
			$wresx="";
			if(!isset($wmed))
				$wmed="0-SELECCIONE";
			$wmedx="";
			$wmecd="";
			$wmeno="";
			$wmea1="";
			$wmea2="";
			$wmeem="";
			$wmete="";
			$wmece="";
			$wmedi=""; 
		}
		
	}
		
	//*******PROCESO DE INFORMACION *********
	
	//********************************************************************************************************
	//*                                         DATOS DEL PACIENTE                                           *                                
	//********************************************************************************************************
	
	$color="#dddddd";
	$color1="#C3D9FF";
	$color2="#E8EEF7";
	$color3="#CC99FF";
	$color4="#99CCFF";
	
	echo "<table border=0 id=tipoT00>";
	if(strlen($wdoc) > 0 and strlen($wnom) == 0)
	{
		?>
			<script>
				function ira(){document.AGFA_RIS.wnom.focus();}
			</script>
		<?php
	}
	echo "<tr><td align=center id=tipoT01 rowspan=2><IMG SRC='/matrix/images/medical/root/clinica.png'></td>";
	echo "<td id=tipoT02>CLINICA LAS AMERICAS<BR>ACTUALIZACION DE PACIENTES DEL RIS </td></tr>";
	echo "<tr><td id=tipoT03><A HREF='/MATRIX/root/Reportes/DOC.php?files=../../INTERFACES/procesos/AGFA_RIS.php' target='_blank'>Version 2014-07-23</A></td></tr>";
	echo "</table><br><br>";
	if(isset($werr) and isset($e) and $e > -1)
	{
		echo "<br><br><center><table border=0 aling=center id=tipo2>";
		if(substr($werr[0],0,3) == "OK!" and count($werr) == 1)
			echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[0]."</b></font></td></tr>";
		else
			echo "<tr><td align=center bgcolor=".$color3."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color3."><font color=#000000 face='tahoma'><b>".count($werr)." ERRORES EN GRABACION. VER AL FINAL DE LA PAGINA</b></font></td></tr>";
		echo "</table><br><br></center>";
	}
	echo "<table border=0 align=center>";
	if(isset($ok) and $ok == 3)
	{
		if(isset($wp))
		{
			$n=$numero +1 ;
			echo "<tr><td align=right colspan=2><font size=2><b>Registro Nro. ".$wp." De ".$n."</b></font></td></tr>";
		}
		else
			echo "<tr><td align=right colspan=2><font size=2 color='#CC0000'><b>Consulta Sin Registros</b></font></td></tr>";
	}
	echo "<tr><td align=center bgcolor=".$color." colspan=2><b>DATOS DEL PACIENTE</b></td></tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Tipo de Documento : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='01' and Selest='on' order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wtdo' id=tipo1>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wtdo=ver($wtdo);
			if($wtdo == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Documento : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wdoc' size=15 maxlength=15  value='".$wdoc."' class=tipo3 onkeypress='teclado5()' Onblur='enter()'></td>";
	echo "</tr>";
	if($wnom == "")
	{
		//                  0       1       2       3       4       5      6       7       8       9       10      11      12      13      14      15      16      17      18
		$query = "SELECT Pactdc, Pacdoc, Pacnom, Pacap1, Pacap2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Pacpai, Pacmun, Pacbar, Paczon, Pacofi, Pacres, Pacmed, Pacact, Paccel from ".$empresa."_000002 where Pactdc='".$wtdo."' and Pacdoc='".$wdoc."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			if($wnom == "")
				$wnom=$row[2];
			if($wap1 == "")
				$wap1=$row[3];
			if($wap2 == "")
				$wap2=$row[4];
			if($wfna == "0000-00-00")
				$wfna=$row[5];
			if($wsex =="0-SELECCIONE")
			{
				$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[6]."' and Seltip='03' and Selest='on' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wsex=$row[6]."-".$row1[0];
				}
				else
					$wsex="0-SELECCIONE";
			}
			if($west == "0-SELECCIONE")
			{
				$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[7]."' and Seltip='04' and Selest='on' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$west=$row[7]."-".$row1[0];
				}
				else
					$west="0-SELECCIONE";
			}
			if($wdir == "")
				$wdir=$row[8];
			if($wtel == "")
			{
				$wtel=$row[9];
				$wcel=$row[18];
			}
			if($wpai == "0-SELECCIONE")
			{
				$query = "SELECT Nombre from root_000006 where Codigo='".$row[10]."'  ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wpai=$row[10]."-".$row1[0];
				}
				else
					$wpai="0-SELECCIONE";
			}
			if($wmun == "0-SELECCIONE" and $wmunx == "")
			{
				$query = "SELECT Nombre from root_000006 where Codigo='".$row[11]."'  ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wmun=$row[11]."-".$row1[0];
				}
				else
					$wmun="0-SELECCIONE";
			}
			if($wbar == "0-SELECCIONE" and $wbarx == "")
			{
				$query = "SELECT Bardes  from root_000034 where Barcod='".$row[12]."'  ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wbar=$row[12]."-".$row1[0];
				}
				else
					$wbar="0-SELECCIONE";
			}
			if($wzon == "0-SELECCIONE")
			{
				$query = "SELECT Seldes from clisur_000105 where Selcod='".$row[13]."' and Seltip='05' and Selest='on' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wzon=$row[13]."-".$row1[0];
				}
				else
					$wzon="0-SELECCIONE";
			}
			if($wofi == "0-SELECCIONE" and $wofix == "")
			{
				$query = "SELECT Descripcion  from root_000003 where Codigo='".$row[14]."' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wofi=$row[14]."-".$row1[0];
				}
				else
					$wofi="0-SELECCIONE";
			}
			if($wres == "0-SELECCIONE" and $wresx == "")
			{
				$query = "SELECT Empdes  from ".$empresa."_000005 where Empnit='".$row[15]."' ";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wres=$row[15]."-".$row1[0];
				}
				else
					$wres="0-SELECCIONE";
			}
			if($row[17] == "on" and ($row[16] == substr($wmed,0,strpos($wmed,"-")) or $wmed == "0-SELECCIONE"))
			{
				if(strtoupper($wmedx) != "CAMBIAR")
				{
					$query = "SELECT Relcom from ".$empresa."_000004 where Reltdo='".$row[0]."' and  Reldoc='".$row[1]."' order by Relfec desc, Relhor desc ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$query = "SELECT  Medcod, Mednom, Medap1, Medap2, Medema, Medtel, Medcel, Meddir from ".$empresa."_000003 where Medcod='".$row1[0]."' and Medact='on' ";
						$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row2 = mysql_fetch_array($err2);
						$wmed=$row2[0]."-".$row2[1];
						$wmecd=$row2[0];
						$wmeno=$row2[1];
						$wmea1=$row2[2];
						$wmea2=$row2[3];
						$wmeem=$row2[4];
						$wmete=$row2[5];
						$wmece=$row2[6];
						$wmedi=$row2[7]; 
					}
				}
				else
				{
					$wmed="0-SELECCIONE";
					$wmedx="";
					$wmecd="";
					$wmeno="";
					$wmea1="";
					$wmea2="";
					$wmeem="";
					$wmete="";
					$wmece="";
					$wmedi=""; 
				}
			}
		}
		else
		{
			//                  0       1        2      3       4        5      6       7       8       9       10
			$query  = "select movtid, movced, movnom, movape, movap2, movnac, movsex, movdir, movtel, movmun, movcer from aymov ";
			$query .= " where (movfue = 'RA' ";
			$query .= "    or  movfue = 'RX' ";
			$query .= "    or  movfue = 'OR' ";
			$query .= "    or  movfue = 'SF') ";
			$query .= "   and movtid = '".$wtdo."'";
			$query .= "   and  movced = '".$wdoc."'";
			$query .= "   and  movap2 is not null ";
			$query .= "   union all ";
			$query .= " select movtid, movced, movnom, movape, ' ', movnac, movsex, movdir, movtel, movmun, movcer from aymov ";
			$query .= " where (movfue = 'RA' ";
			$query .= "    or  movfue = 'RX' ";
			$query .= "    or  movfue = 'OR' ";
			$query .= "    or  movfue = 'SF') ";
			$query .= "   and movtid = '".$wtdo."'";
			$query .= "   and  movced = '".$wdoc."'";
			$query .= "   and  movap2 is  null ";
			$conex_o = odbc_connect('facturacion','','');
			$err_o = odbc_do($conex_o,$query);
			$campos= odbc_num_fields($err_o);
			$count=0;
			if(odbc_fetch_row($err_o))
			{
				$count++;
				$odbc=array();
				for($m=1;$m<=$campos;$m++)
				{
					$odbc[$m-1]=odbc_result($err_o,$m);
				}
				unset($west);
				unset($wbar);
				//unset($wmed);
				$ok=1;
				if($wnom == "")
					$wnom=$odbc[2];
				if($wap1 == "")
					$wap1=$odbc[3];
				if($wap2 == "")
					$wap2=$odbc[4];
				if($wfna == "0000-00-00")
					$wfna=$odbc[5];
				if($wsex =="0-SELECCIONE")
					$wsex=$odbc[6];
				if(!isset($west))
					$west="S-SOLTERO(a)";
				if($wdir == "")
					$wdir=$odbc[7];
				if($wtel == "")
				{
					$wtel=$odbc[8];
					$wcel="";
				}
				if($wpai == "0-SELECCIONE")
				{
					if(substr($odbc[9],0,2) == "01")
					{
						$wpai=$odbc[9];
						$wmun=$odbc[9];
					}
					else
					{
						$wpai="01000";
						$wmun=$odbc[9];
					}
					$wmunx="";
				}
				if(!isset($wbar))
				{
					$wbar="01608-LA MOTA";
					$wbarx="";
				}
				if($wzon == "0-SELECCIONE")
					$wzon="U";
				if($wofi == "0-SELECCIONE" and $wofix == "")
				{
					$wofi="P76";
					$wofix="";
				}
				if($wres == "0-SELECCIONE" and $wresx == "")
				{
					$wres=$odbc[10];
					$wresx="";
				}
				if(!isset($wmed) or $wmed == "0-SELECCIONE")
				{
					$wmed="0-SELECCIONE";
					$wmedx="";
					$wmecd="";
					$wmeno="";
					$wmea1="";
					$wmea2="";
					$wmeem="";
					$wmete="";
					$wmece="";
					$wmedi=""; 
				}
			}
			else
			{
					//              0       1        2      3       4        5      6       7       8       9       10
				$query  = "select pactid, pacced, pacnom, pacap1, pacap2, pacnac, pacsex, pacdir, pactel, pacmun, paccer from inpac ";
				$query .= " where pactid = '".$wtdo."'";
				$query .= "   and pacced = '".$wdoc."'";
				$query .= "   and pacap2 is not null ";
				$query .= "   union all ";
				$query .= " select pactid, pacced, pacnom, pacap1, ' ', pacnac, pacsex, pacdir, pactel, pacmun, paccer from inpac ";
				$query .= " where pactid = '".$wtdo."'";
				$query .= "   and pacced = '".$wdoc."'";
				$query .= "   and pacap2 is  null ";
				$conex_o = odbc_connect('facturacion','','');
				$err_o = odbc_do($conex_o,$query);
				$campos= odbc_num_fields($err_o);
				$count=0;
				if(odbc_fetch_row($err_o))
				{
					$count++;
					$odbc=array();
					for($m=1;$m<=$campos;$m++)
					{
						$odbc[$m-1]=odbc_result($err_o,$m);
					}
					unset($west);
					unset($wbar);
					//unset($wmed);
					$ok=1;
					if($wnom == "")
						$wnom=$odbc[2];
					if($wap1 == "")
						$wap1=$odbc[3];
					if($wap2 == "")
						$wap2=$odbc[4];
					if($wfna == "0000-00-00")
						$wfna=$odbc[5];
					if($wsex =="0-SELECCIONE")
						$wsex=$odbc[6];
					if(!isset($west))
						$west="S-SOLTERO(a)";
					if($wdir == "")
						$wdir=$odbc[7];
					if($wtel == "")
					{
						$wtel=$odbc[8];
						$wcel="";
					}
					if($wpai == "0-SELECCIONE")
					{
						if(substr($odbc[9],0,2) == "01")
						{
							$wpai=$odbc[9];
							$wmun=$odbc[9];
						}
						else
						{
							$wpai="01000";
							$wmun=$odbc[9];
						}
						$wmunx="";
					}
					if(!isset($wbar))
					{
						$wbar="01608-LA MOTA";
						$wbarx="";
					}
					if($wzon == "0-SELECCIONE")
						$wzon="U";
					if($wofi == "0-SELECCIONE" and $wofix == "")
					{
						$wofi="P76";
						$wofix="";
					}
					if($wres == "0-SELECCIONE" and $wresx == "")
					{
						$wres=$odbc[10];
						$wresx="";
					}
					if(!isset($wmed) or $wmed == "0-SELECCIONE")
					{
						$wmed="0-SELECCIONE";
						$wmedx="";
						$wmecd="";
						$wmeno="";
						$wmea1="";
						$wmea2="";
						$wmeem="";
						$wmete="";
						$wmece="";
						$wmedi=""; 
					}
				}
			}
		}
		$query = "SELECT Pactdc, Pacdoc from ".$empresa."_000002 where Pacdoc='".$wdoc."' ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			if($wtdo != $row[0])
			{
				$wdocA=$row[1];
				$wtdoA=$row[0];
				$we=1;
			}
			if($num > 1)
			{
				$we=2;
				$wtdoA=$row[0];
				for ($i=1;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$wtdoA .= "-".$row[0];
				}
				
			}
		}
	}
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Nombre : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wnom' size=30 maxlength=30  value='".$wnom."' onkeypress='teclado4()' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Primer Apellido : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wap1' size=30 maxlength=30  value='".$wap1."' onkeypress='teclado4()' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Segundo Apellido : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wap2' size=30 maxlength=30  value='".$wap2."' onkeypress='teclado4()' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left valign=center>Fecha Nacimiento : </td><td bgcolor=".$color2." align=left><input type='TEXT' name='wfna' size=10 maxlength=21 id='wfna' value='".$wfna."' class=tipo3>&nbsp&nbsp<IMG SRC='/matrix/images/medical/INTERFACES/calendario.jpg' id='trigger2'></td>";
	?>
	<script type="text/javascript">//<![CDATA[
		Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfna',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	//]]></script>
	<?php
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Sexo : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='03' and Selest='on' order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wsex' id=tipo1>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wsex=ver($wsex);
			if($wsex == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Estado Civil : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='04' and Selest='on' order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='west' id=tipo1>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$west=ver($west);
			if($west == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Direccion : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wdir' size=60 maxlength=60  value='".$wdir."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Telefonos : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wtel' size=60 maxlength=60  value='".$wtel."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Celular : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wcel' size=11 maxlength=11  value='".$wcel."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Pais : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Codigo, Nombre   from root_000006 where Codigo like '01%' order by Nombre";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wpai' id=tipo1 OnChange='enter()'>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wpai=ver($wpai);
			if($wpai == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	$municipio=$wmun;
	echo "<td bgcolor=".$color1." align=left>Municipio : </td><td bgcolor=".$color2." align=left>";
	if($wpai != "01000")
		$query = "SELECT Codigo, Nombre   from root_000006 where Codigo = '".$wpai."' order by Nombre";
	else
		$query = "SELECT Codigo, Nombre   from root_000006 where Nombre like '%".$wmunx."%' order by Nombre";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wmun' id=tipo1 OnChange='enter()'>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wmun=ver($wmun);
			if($wmun == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>&nbsp<input type='TEXT' name='wmunx' value='".$wmunx."' size=30 maxlength=30  class=tipo3 OnBlur='enter()'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Barrio : </td><td bgcolor=".$color2." align=left>";
	
	if($wpai == "01000")
	{
		$query = "SELECT Barcod, Bardes from root_000034 where Bardes like '%".$wbarx."%' and Barmun = '".ver($wmun)."' order by Bardes";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wbar' id=tipo1>";
		echo "<option>0-SELECCIONE</option>";
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wbar=ver($wbar);
				if($wbar == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		else
		{
			echo "<option>".$municipio."</option>";	
		}
	}
	else
	{
		echo "<select name='wbar' id=tipo1>";
		echo "<option>0-SELECCIONE</option>";
		echo "<option>".$municipio."</option>";	
	}
	echo "</select>&nbsp<input type='TEXT' name='wbarx' value='".$wbarx."' size=30 maxlength=30  class=tipo3 OnBlur='enter()'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Zona : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='05' and Selest='on' order by Selpri";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wzon' id=tipo1>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wzon=ver($wzon);
			if($wzon == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Oficio : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Codigo, Descripcion  from root_000003 where Descripcion like '%".$wofix."%' order by Descripcion";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wofi' id=tipo1>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wofi=ver($wofi);
			if($wofi == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>&nbsp<input type='TEXT' name='wofix' value='".$wofix."' size=30 maxlength=30  class=tipo3 OnBlur='enter()'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Responsable : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Empnit, Empdes from ".$empresa."_000005 where Empdes like '%".$wresx."%' and Empest='on' order by Empdes";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	$wres=ver1($wres);
	echo "<select name='wres' id=tipo1>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if($wres == $row[0])
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";
		}
	}
	echo "</select>&nbsp<input type='TEXT' name='wresx' value='".$wresx."' size=30 maxlength=30  class=tipo3 OnBlur='enter()'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Medico Remitente : </td><td bgcolor=".$color2." align=left>";
	$query = "SELECT Medcod, Mednom, Medap1, Medap2, Medema, Medtel, Medcel, Meddir from ".$empresa."_000003 where Mednom like '%".$wmedx."%' order by Mednom";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	echo "<select name='wmed' id=tipo1 OnChange='enter()'>";
	echo "<option>0-SELECCIONE</option>";
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$wnommed=$row[1]." ".$row[2]." ".$row[3];
			$wmed=ver($wmed);
			if($wmed == $row[0])
			{
				echo "<option selected>".$row[0]."-".$wnommed."</option>";
				$wmecd=$row[0];
				$wmeno=$row[1];
				$wmea1=$row[2];
				$wmea2=$row[3];
				$wmeem=$row[4];
				$wmete=$row[5];
				$wmece=$row[6];
				$wmedi=$row[7];
			}
			else
				echo "<option>".$row[0]."-".$wnommed."</option>";
		}
	}
	else
	{
		$wmecd="";
		$wmeno="";
		$wmea1="";
		$wmea2="";
		$wmeem="";
		$wmete="";
		$wmece="";
		$wmedi=""; 
	}
	echo "</select>&nbsp<input type='TEXT' name='wmedx' value='".$wmedx."' size=30 maxlength=30  class=tipo3 OnBlur='enter()'>";
	echo "</td>";
	echo "</tr>";
	echo "<tr><td align=center bgcolor=".$color." colspan=2><b>DATOS DEL MEDICO REMITENTE</b></td></tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Codigo : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmecd' size=15 maxlength=15 readonly='readonly' value='".$wmecd."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Nombre : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmeno' size=30 maxlength=30  value='".$wmeno."' onkeypress='teclado4()' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Primer Apellido : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmea1' size=30 maxlength=30  value='".$wmea1."' onkeypress='teclado4()' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Segundo Apellido : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmea2' size=30 maxlength=30  value='".$wmea2."' onkeypress='teclado4()' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>E-mail : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmeem' size=60 maxlength=60  value='".$wmeem."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Telefonos : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmete' size=30 maxlength=30  value='".$wmete."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Celular : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmece' size=20 maxlength=20  value='".$wmece."' class=tipo3></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=".$color1." align=left>Direccion : ";
	echo "<td bgcolor=".$color2." align=left><input type='TEXT' name='wmedi' size=60 maxlength=60  value='".$wmedi."' class=tipo3></td>";
	echo "</tr>";
	if(isset($we))
		if($we == 1)
			echo "<tr><td bgcolor=#FFFF00 colspan=2 align=center>EXISTE EL NUMERO DE DOCUMENTO <b><font size=5 color=#CC0000>".$wdocA."</font></b> PERO CON TIPO DE DOCUMENTO <b><font size=5 color=#CC0000>".$wtdoA."</font></b> Y NO CON TIPO DE DOCUMENTO <b><font size=5 color=#CC0000>".$wtdo." </font></b>!!!!</td></tr>";
		else
			echo "<tr><td bgcolor=#FFFF00 colspan=2 align=center>EXISTE EL NUMERO DE DOCUMENTO <b><font size=5 color=#CC0000>".$wdocA."</font></b> PERO CON TIPOS DE DOCUMENTOS <b><font size=5 color=#CC0000>".$wtdoA."</font></b> FAVOR REVISE EN LA BASE DE DATOS DEL RIS EL TIPO DE DOCUMENTO CORRECTO!!!!</td></tr>";
	//PARTE CENTRAL DE LA PANTALLA 
	switch ($ok)
	{
		case 1:
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>";
			echo "<input type='RADIO' name=ok value=0 onclick='enter()'>INICIAR&nbsp";
			echo "<input type='RADIO' name=ok value=1 checked onclick='enter()'>PROCESO&nbsp";
			echo "<input type='RADIO' name=ok value=3 onclick='enter()'>CONSULTAR&nbsp";
			echo "<input type='RADIO' name=ok value=2 onclick='enter()'>GRABAR</td></tr>";
		break;
		case 3:
			echo "<tr><td bgcolor=#cccccc align=center colspan=2>";
			echo "<input type='RADIO' name=ok value=0 onclick='enter()'>INICIAR&nbsp";
			echo "<input type='RADIO' name=ok value=1 onclick='enter()'>PROCESO&nbsp";
			echo "<input type='RADIO' name=ok value=3 checked onclick='enter()'>CONSULTAR&nbsp";
			echo "<input type='RADIO' name=wb value=1  onclick='enter()'> Adelante <input type='RADIO' name=wb value=2 onclick='enter()'> Atras&nbsp";
			echo "<input type='RADIO' name=ok value=2 onclick='enter()'>GRABAR</td></tr>";
		break;
	}
	echo "<tr><td bgcolor=#999999 colspan=2 align=center><input type='submit' value='OK'></td></tr></table><br><br></center>";
	if(isset($werr) and isset($e) and $e > -1)
	{
		echo "<br><br><center><table border=0 aling=center id=tipo2>";
		for ($i=0;$i<=$e;$i++)
			if(substr($werr[$i],0,3) == "OK!")
				echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/feliz.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
			else
				echo "<tr><td align=center bgcolor=".$color3."><IMG SRC='/matrix/images/medical/root/Malo.png'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color3."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
		echo "</table><br><br></center>";
	}
	echo"</form>";
//}
?>
</body>
</html>
