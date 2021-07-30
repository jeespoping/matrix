<html>
<head>
  	<title>MATRIX Tablero de Pacientes Unidades Ambulatorias</title>  	
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

    <script type="text/javascript">
	<!--
	function ejecutar(path)
	{
		window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
	}
	function enter()
	{
		document.forms.TableroAmb.submit();
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
	//-->
</script>
 
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">

<?php
$consultaAjax = '';
include_once("conex.php");
include_once("root/comun.php");
$wemp_pmla=$_REQUEST['wemp_pmla'];
$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : TableroAmb.php
	   Fecha de Liberación : 2009-09-04
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2016-03-03
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite Ingresar al programa de 
	   Historia Clinica Electronica de las Unidades Ambulatorias. Los pacientes pueden ser ubicados por diferentes 
	   criterios de seleccion.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2020-10-20 Edwin MG
				Se agrega filtro de servicio domiciliario
	   .2016-04-12 Verónica Arismendy Se modifica la consulta porque no se estaban teniendno en cuenta las citas de la tabla citasfi_000017 
	    *************************************************************************************************
	   .2016-03-03 Verónica Arismendy
              Se valida si llega el parámetro wcit para mostrar en el listado sólo los pacientes que tengan cita el día actual
			  Se debe tener en cuenta que actualmente está configurado para fisioterapia, si se desea adicionar otra especialidad 
			  se debe actualizar el swicth de la función consultarPrefijoNumeroTabla y configurar la url para que llegue el parámetro $wcit.
	   .2015-08-27
              Se modifica el acceso a la tabla hce 22 validando que el codigo del medico no este en nulo.
	   .2013-11-05
              Se ponen dinamicas las tablas de hce y movhos al igual que la empresa origen.
	   .2012-05-11
			Se Modifica el programa para acceder a la tabla 39 de HCE con el proposito de validar las direcciones IP
			validas con autorizacion de acceso a la historia clinica electronica.
	   .2009-10-05
			Se modifico el programa para que el query principal hiciera join con la tabla 11 de movhos para traer el
			centro de costos de la unidad que llega por parametros.
	   .2009-09-04
	   		Release de Versión Beta.
	   
	   		
[*DOC]
***********************************************************************************************************************/
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
		for ($i=0;$i<$numemp;$i++)
		{
			if(strpos(strtoupper($elemento),strtoupper($SempT[$i])) !== false)
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

//2016-03-03 Verónica Arismendy se consulta el prefijo y el número de la tabla de citas dependiendo del tipo de atención se devuelve el nombre completo de la tabla de citas
function consultarPrefijoNumeroTabla($wcit, $wemp_pmla, $conex){

		//Se consulta en la 51 el prefijo de la tabla envíandole la descripción
		$q = "SELECT detval 
		      FROM root_000050, root_000051 
			  WHERE empcod = '".$wemp_pmla."' 
			  AND empest = 'on' 
			  AND empcod = detemp 
			  AND detdes = '".$wcit."' 
			  AND detapl = 'citas'
		";	
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $pref = mysql_fetch_assoc($res);

		$prefijoTabla = isset($pref["detval"]) && $pref["detval"] != "" ? $pref["detval"] : "";
		
		//Con el mismo parametro se hace un case para ver que tabla de citas maneja. OBS: cada que se agregue esta funcionalidad a un servcio se debe añadir al case
		$numTablaCita = array();
		switch($wcit){
			case 'Fisioterapia' : 
							$numTablaCita = array("000009", "000017");
							break;
		}
		
		$arrTablaCitas = array(
			"prefijo" => $prefijoTabla != "" ? $prefijoTabla : "",
			"nombreTabla" => $numTablaCita
		);
		
		
		return $arrTablaCitas;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='TableroAmb' action='TableroAmb.php?wemp_pmla=".$wemp_pmla."' method=post>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'codemp' value='".$codemp."'>";
	echo "<input type='HIDDEN' name= 'historia' value='".$historia."'>";
	echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
	
	if(isset($servicioDomiciliario) && $servicioDomiciliario == 'on' )
		echo "<input type='HIDDEN' name= 'servicioDomiciliario' value='".$servicioDomiciliario."'>";
	
	$wcit = isset($wcit) ? $wcit : "";
	echo "<input type='hidden' name='wcit' id='wcit' value='".$wcit."'>"; //2016-03-03 Verónica Arismendy
	
	echo "<table border=0 CELLSPACING=0>";
	echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/lmatrix.jpg'></td>";
	echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;TABLERO DE PACIENTES HCE UNIDADES AMBULATORIA&nbsp;&nbsp;<A HREF='/MATRIX/root/Reportes/DOC.php?files=/matrix/HCE/procesos/TableroAmb.php' target='_blank'>Version 2016-04-12</A></td></tr>";
	echo "<tr><td id=tipoT03 colspan=2></td></tr>";
	echo "</table><br><br>";
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
	global $whce;
	$IPOK=0;
	$query = "select ctanip, ctausu from ".$whce."_000039 ";
	$query .= " where ctaest = 'on'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			if(($row[0] == substr($IIPP,0,strlen($row[0])) and $key == $row[1]) or ($row[0] == substr($IIPP,0,strlen($row[0])) and $row[1] == "*") or ($row[0] == "*" and $key == $row[1]))
			{
				$IPOK=1;
				$i=$num+1;
			}
		}
	}
	
	//if($IIPP == "192.168.0.1" or substr($IIPP,0,5) == "132.1" or $IIPP == "127.0.0.1" or $IIPP == "::1")
	if($IPOK > 0)
	{
		$wservicio = "";
		$query = "select Ccoseu from ".$empresa."_000011 ";
		$query .= " where Ccocod = '".$wcco."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$wservicio = $row[0];
		}
		
		//                 0       1      2     3
		$query = "select usurol,Rolatr,Rolemp,Usuuni from ".$whce."_000020,".$whce."_000019 ";
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
				$Suni=explode(";",$row[3]);
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
			$query = "select Empemp from ".$whce."_000025 ";
			$query .= " where Empcod = '".$Semp."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				$SempT=explode(";",$row[0]);
				$numemp=$num;
			}
		}
		
		
		$filtroServDom = '';
		if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
			$filtroServDom = " AND ccodom = 'on' ";
		}
		
		if( !empty( $wcco ) ){
			$filtroCco .= " and ubisac = '".$wcco."' ";
		}
		
		if($Wexiste == 1)
		{	
			// 2016-03-03 Verónica Arismendy
			//En caso de que venga el parámetro $wcit quiere decir que la consulta debe traer sólo los pacientes que tengan cita el día actual.	
			if(isset($wcit) && $wcit != ""){
		
				//Con el parametro se consulta el prefijo de tabla de citas y el número
				$arrInfoTabla = consultarPrefijoNumeroTabla($wcit, $codemp, $conex);
				
				if(isset($arrInfoTabla["nombreTabla"]) && count($arrInfoTabla["nombreTabla"]) >= 1){		
					
					$fecha = date("Y-m-d");
					$countTablas = count($arrInfoTabla["nombreTabla"]);
					
					if($countTablas > 1){
						$query = "";
					
						for($i=0; $i<$countTablas; $i++){
							
							$query .= "SELECT Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'AMBULATORIO', ".$wmovhos."_000016.fecha_data, Ccoseu  
								FROM ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016, ".$arrInfoTabla["prefijo"] . "_" . $arrInfoTabla["nombreTabla"][$i]." c
								WHERE ubiald = 'off' 
								$filtroCco;
								AND ubisac   = Ccocod 
								AND ubihis   = orihis 
								AND ubiing   = oriing 
								AND oriori   = '".$codemp."' 
								AND oriced   = pacced  
								AND oritid   = pactid 
								AND orihis   = inghis 
								AND oriing   = inging  
								AND c.Cedula = Pacced 
								AND c.Fecha  = '".$fecha."'
								$filtroServDom;
							";
						
							$can = $i+1;
							if($arrInfoTabla["nombreTabla"][$can] != ""){
								$query.= " UNION ";
							}					
						}
						
						$query.= " ORDER BY 11,17";
							
					}else{
						//                 0       1       2       3       4       5       6       7       8       9      10     11      12      13      14      15         16                 17
						$query = "SELECT Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'AMBULATORIO', ".$wmovhos."_000016.fecha_data, Ccoseu 
							FROM ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016, ".$arrInfoTabla["prefijo"] . "_" . $arrInfoTabla["nombreTabla"][0]." c
							WHERE ubiald = 'off' 
							$filtroCco;
							AND ubisac   = Ccocod 
							AND ubihis   = orihis 
							AND ubiing   = oriing 
							AND oriori   = '".$codemp."' 
							AND oriced   = pacced  
							AND oritid   = pactid 
							AND orihis   = inghis 
							AND oriing   = inging  
							AND c.Cedula = Pacced 
							AND c.Fecha  = '".$fecha."'
							$filtroServDom;
							ORDER BY 11,17 ";	
					}					
			}else{
				$query = "";
			}
							
		}
		else{
				//Query por defecto es decir si no viene el parámetro para buscar asociado con la tabla de citas está es la consulta
			    //                 0       1       2       3       4       5       6       7       8       9      10     11      12      13      14      15         16                 17
				$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, 'AMBULATORIO', ".$wmovhos."_000016.fecha_data, Ccoseu  ";
				$query .= " from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016 ";
				$query .= " where ubiald = 'off'  ";
				$query .= $filtroCco;
				$query .= " and ubisac = Ccocod  ";
				$query .= " and ubihis = orihis  ";
				$query .= " and ubiing = oriing  ";
				$query .= " and oriori = '".$codemp."'  ";
				$query .= " and oriced = pacced  ";
				$query .= " and oritid = pactid  ";
				$query .= " and orihis = inghis "; 
				$query .= " and oriing = inging  ";
				$query .= $filtroServDom;
				$query .= "  order by 11,17 ";
		}
		
			if($query != ""){
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
			}else{
				$num = 0;
			}
						
			
			if ($num>0)
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
				echo "<tr><td id=tipoT05 align=center colspan=2><input type='button' name='INICIAR' value='INICIAR CRITERIOS'  onClick='clean()'></td><td id=tipoT05 align=center colspan=2><input type='submit' value='BUSCAR'></td><td id=tipoT05 align=center colspan=2></td></tr>";
				echo "</table>";
				echo "<table border=0 align=center id=tipo5>";
				echo "<tr><td id=tipoT04 align=center>FECHA<BR>INGRESO</td><td id=tipoT04 align=center>SERVICIO<BR>ACTUAL</td><td id=tipoT04 align=center>HABITACION</td><td id=tipoT04 align=center>HISTORIA<BR>CLINICA</td><td id=tipoT04 align=center>NOMBRE<br>PACIENTE</td><td id=tipoT04 align=center>RESPONSABLE</td><td id=tipoT04 align=center>MEDICO(s)<BR>TRATANTE(s)</td><td id=tipoT04 align=center>ESPECIALIDAD(es)</td></tr>";
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
					
					if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' && empty( $wcco ) )
						$wservicio = $row[18];
					
					$wmed="";
					$wesp="";
					$query = "select Medno1, Medno2, Medap1, Medap2, Mtretr, Espnom, max(".$whce."_000022.Fecha_data) ";
					$query .= " from ".$whce."_000022,".$wmovhos."_000048, ".$wmovhos."_000044 ";
					$query .= "  where Mtrhis = '".$row[0]."' "; 
					$query .= "    and Mtring = '".$row[1]."' ";
					$query .= "    and Mtrest = 'on' ";
					$query .= "    and Mtrmed != '' "; 
					$query .= "    and Mtrmed = Meduma "; 
					$query .= "    and Mtretr = Espcod ";
					$query .= "  group by 1,2,3,4,5,6 ";
					$query .= "  order by 7 desc ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if ($num>0)
					{
						$wkey="";
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);	
							if($j == 0)
							{
								$wkey=$row1[6];
								$wmed .= $row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3];
								$wesp .= $row1[4]."-".$row1[5];
							}
							else
							{
								if($wkey == $row1[6])
								{
									$wmed .= "<br>".$row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3];
									if(strpos($wesp,$row1[4]) === false)
										$wesp .= "<br>".$row1[4]."-".$row1[5];
								}
							}
						}
					}
					if(isset($WSA) and strpos($WSA,$row[10]."-".$row[11]) === false and !isset($wcri1) and !isset($wcri2))
					{
						if($WSA == "")
							$WSA .= $row[10]."-".$row[11];
						else
							$WSA .= "|".$row[10]."-".$row[11];
					}
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
							if(strpos($WME,$wap[$ji]) === false)
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
								if(strpos($WES,$wap[$ji]) === false)
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
					$criterios[4] = $row[12]."-".$row[13];
					$criterios[5] = $wmed;
					$criterios[6] = $wesp;
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
					//echo $especial." ".$wcriterio1." ".$wcriterio2." ".$Semp." ".count($Sesp)." ".count($SempT)." ".$Sroot." ".$wcri1." ".$wcri2."<br>";
					if($especial == 0 and !isset($posB))
						$posB=$especial;
					if(isset($wcriterio1))
					{
						$pos=(integer)$wcriterio1 - 1;
						if((isset($wcri1) and @strpos(strtoupper($criterios[$pos]),strtoupper($wcri1)) !== false and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])) or ($especial > 0 and (!isset($wcri1) or $wcri1 == "") and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])))
						{
							//echo "PASO CRITERIO 1<BR>";
							if(isset($wcriterio2))
							{
								$pos=(integer)$wcriterio2 - 1;
								if((isset($wcri2) and @strpos(strtoupper($criterios[$pos]),strtoupper($wcri2)) !== false and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])) or ($especial > 0 and (!isset($wcri2) or $wcri2 == "")  and buscarC($especial,$Sroot,$Sesp,$numesp,$SempT,$numemp,$Suni,$numuni,$criterios[$posB])))
								{
									//echo "PASO CRITERIO 2<BR>";
									$wcont++;
									if($wcont % 2 == 0)
										$tipo="tipo18";
									else
										$tipo="tipo19";
									$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$historia."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&wservicio=".$wservicio."";
									echo "<tr style='cursor: hand;cursor: pointer;' onclick='ejecutar(".chr(34).$path.chr(34).")'><td id=".$tipo."A>".$row[17]."</td><td id=".$tipo."A>".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[16]."</td><td id=".$tipo.">".$row[0]."-".$row[1]."</td><td id=".$tipo."A>".$nombre."</td><td id=".$tipo."A>".$row[12]."-".$row[13]."</td><td id=".$tipo."A>".$wmed."</td><td id=".$tipo."A>".$wesp."</td></tr>";
								}
							}
							else
							{
								$wcont++;
								if($wcont % 2 == 0)
									$tipo="tipo18";
								else
									$tipo="tipo19";
								$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$historia."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&wservicio=".$wservicio."";
								echo "<tr style='cursor: hand;cursor: pointer;' onclick='ejecutar(".chr(34).$path.chr(34).")'><td id=".$tipo."A>".$row[17]."</td><td id=".$tipo."A>".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[16]."</td><td id=".$tipo.">".$row[0]."-".$row[1]."</td><td id=".$tipo."A>".$nombre."</td><td id=".$tipo."A>".$row[12]."-".$row[13]."</td><td id=".$tipo."A>".$wmed."</td><td id=".$tipo."A>".$wesp."</td></tr>";
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
						$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$historia."&origen=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&wservicio=".$wservicio."";
						echo "<tr style='cursor: hand;cursor: pointer;' onclick='ejecutar(".chr(34).$path.chr(34).")'><td id=".$tipo."A>".$row[17]."</td><td id=".$tipo."A>".$row[10]."-".$row[11]."</td><td id=".$tipo.">".$row[16]."</td><td id=".$tipo.">".$row[0]."-".$row[1]."</td><td id=".$tipo."A>".$nombre."</td><td id=".$tipo."A>".$row[12]."-".$row[13]."</td><td id=".$tipo."A>".$wmed."</td><td id=".$tipo."A>".$wesp."</td></tr>";
					}
				}
			}
			echo "<input type='HIDDEN' name= 'WSA' value='".$WSA."' id=WSA>";
			echo "<input type='HIDDEN' name= 'WRE' value='".$WRE."' id=WRE>";
			echo "<input type='HIDDEN' name= 'WME' value='".$WME."' id=WME>";
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
		echo "<table border=0 align=center id=tipo5>";
		echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LA HISTORIA CLINICA NO PUEDE SER USADA FUERA DE LA INSTITUCION !!!</td></tr>";
		echo "</table></center>";
	}
}
?>
</body>
</html>
