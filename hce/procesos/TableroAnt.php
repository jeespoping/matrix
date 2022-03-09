<html>
<head>
  	<title>MATRIX Tablero de Pacientes Egresados</title>  	
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
    	#tipoT06{color:#000066;background:#999999;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;height:2em;}
    	#tipoT07{color:#FFFFFF;background:#EAEAEA;font-size:9pt;font-family:Arial;font-weight:bold;text-right 	:center;height:2em;}
    	
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
		document.forms.TableroAnt.submit();
	}
	function Henter(his)
	{
		document.getElementById('whis').value=his;
		document.forms.TableroAnt.submit();
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
include_once("conex.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : TableroAnt.php
	   Fecha de Liberación : 2011-02-28
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2014-12-23
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite Ingresar al programa de 
	   Historia Clinica Electronica. Los pacientes pueden ser ubicados por diferentes criterios de seleccion.
	   
	   
	   REGISTRO DE MODIFICACIONES : 
	   .2020-06-09
			  Se crea opcion para consultar pacientes tanto activos como egresados y permitir enviar por correo
			  en formato pdf tanto la HCE como las ordenes, con parametro GET accion=COIDC
	   .2018-03-27
			  Se agrega función consultarUltimaFechaKardex, la cuál consulta la última fecha del kardex y se modifica
			  la url de consulta del kardex para un paciente
		
	   .2014-12-23
			  Se agrega para el codigo "10" IDC el icono de HCE-IDC y al url http://www.idclasamericas.co/MxB/mx.php.
			  
	   .2014-11-06
			  Se agrega la accion CO para consuta de Historia Clinica y Ordenes Medicas de forma simultanea.
			  
	   .2014-03-28
			  Se modifica la busqueda x cedula y tipo de documento en la tabla root_000037 teniendo en cuenta el codigo
			  de la empresa, ya que en algunas ocaciones no estaba encontrando la informacion.
			  
	   .2014-02-14
			  Se modifica la url de la accion=R para agregarle las tablas de movimiento y el origen.
			  
	   .2014-01-23
			  Se modifica el programa para eliminar de la busqueda de pacientes en la tabla 36 de historia clinica
			  para los pacientes con accion=E.
			  
	   .2013-12-02
			  Se corrige el llamado de impresion y consulta de pacientes egresados ya que no contemplaba ni el servicio
			  ni el numero de ingreso del paciente.
			  
	   .2013-11-27
			  Se habilita en el programa la accion "H" para entrar a la HCE a ingresos anteriores al activo.
			  
	   .2013-11-05
              Se ponen dinamicas las tablas de hce y movhos al igual que la empresa origen.
              
	   .2013-08-29
			Se modifica el programa para mostrar en la seleccion de ingreso la fecha de ingreso y el servicio de 
			ingreso.
			
	   .2013-03-06
			Se adiciona en el llamado del programa de impresion la opcion de protocolo en 0.
			
	   .2012-05-11
			Se Modifica el programa para acceder a la tabla 39 de HCE con el proposito de validar las direcciones IP
			validas con autorizacion de acceso a la historia clinica electronica.
			
	   .2011-11-01
			Se modifico el programa para hacer la validacion x empresa en el caso de los usuarios que tienen restricciones
			de visualizacion por este item.
			
	   .2011-10-13
			Se modifico el programa para tener en cuenta el centro de costos como variable de ambiente y realizar 
			el hipervinculo a la Historia con el servicio.
			
	   .2011-02-28
	   		Release de Versión Beta.
	   
	   		
[*DOC]
***********************************************************************************************************************/

/********************************************************************************************************
 * Consulta la última fecha en que tuvo el kardex para una historia e ingreso
 ********************************************************************************************************/
function consultarUltimaFechaKardex( $conex, $wbasedato, $historia, $ingreso )
{
	
	$fecha = "";
	 
	$sql = "SELECT MAX( fecha_data ) as fecha 
			  FROM ".$wbasedato."_000053
			 WHERE Karhis = '".$historia."' 
			   AND Karing = '".$ingreso."'
			";
	
	$res = mysql_query( $sql, $conex ) or die(mysql_errno().":".mysql_error());	
	
	if( $rows = mysql_fetch_array($res) ){
		$fecha = $rows['fecha'];
	}
	
	return $fecha;
}

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function buscarC($SempT,$numemp,$elemento)
{
	//echo $numemp." ".$SempT[0]." ".$elemento."<BR>";
	for ($i=0;$i<$numemp;$i++)
	{
		if(strpos(strtoupper($SempT[$i]),strtoupper($elemento)) !== false)
			return true;
	}
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
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='TableroAnt' action='TableroAnt.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'historia' value='".$historia."'>";
	echo "<input type='HIDDEN' name= 'accion' value='".$accion."'>";
	if(isset($wcco))
		echo "<input type='HIDDEN' name= 'wcco' value='".$wcco."'>";

	
	$IPOK=0;
	$query = "select ctanip, ctausu from ".$historia."_000039 ";
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
		$wservicio = "*";
		if(isset($wcco))
		{
			$query = "select Ccoseu from ".$empresa."_000011 ";
			$query .= " where Ccocod = '".$wcco."' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wservicio = $row[0];
			}
		}
		$wcont=-1;
		echo "<table border=0 CELLSPACING=0>";
		echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/HCE".$wemp_pmla.".jpg'></td>";
		echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;TABLERO DE PACIENTES EGRESADOS HCE&nbsp;&nbsp;<A HREF='/MATRIX/root/Reportes/DOC.php?files=/matrix/HCE/procesos/TableroAnt.php' target='_blank'>Version 2014-12-23</A></td></tr>";
		echo "<tr><td id=tipoT03 colspan=2></td></tr>";
		echo "</table><br><br>";
		echo "<center><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'></IMG></center><br>";
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2>DATOS DEL PACIENTE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Numero de Historia</td>";
		if(!isset($whis))
			$whis="";
		if(!isset($wnin))
			$wnin="";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' id='whis' size=10 maxlength=15 value='".$whis."' onBlur='enter()'></td></tr>";
		if(!isset($wced))
		{
			$wced="";
		}
		echo "<tr><td bgcolor=#cccccc align=center>Tipo de Documento</td><td bgcolor=#cccccc align=center><select name='wtdo'>";
		$query  = "select Codigo,Descripcion from root_000007 ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<option></option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(isset($wtdo) and $row[0] == $wtdo)
					echo "<option selected>".$row[0]."</option>";
				else
					echo "<option>".$row[0]."</option>";
			}
		}
		else
			echo "<option></option>";
		echo "</select>";
		echo "<tr><td bgcolor=#cccccc align=center>Documento</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wced' size=15 maxlength=15 value='".$wced."' onBlur='enter()'></td></tr>";
		if($wced != "" and $wtdo != "")
		{
			$query  = "select Orihis from root_000037 where Oritid = '".$wtdo."' and Oriced = '".$wced."'  and oriori = '".$wemp_pmla."'";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$row = mysql_fetch_array($err);
				$whis=$row[0];
			}
		}
		echo "<tr><td bgcolor=#cccccc align=center>Numero de Ingreso</td><td bgcolor=#cccccc align=center><select name='wnin' onChange=\"Henter('".$whis."')\">";
		if($whis != "")
		{
			if($accion == "E")
			{
				$query  = "select Ubihis,Ubiing,".$empresa."_000018.Fecha_data,Cconom from ".$empresa."_000018,".$empresa."_000011 ";
				$query .= "    where Ubihis = '".$whis."' "; 
				$query .= " 	 and Ubisac = '".$wcco."' ";
				$query .= " 	 and ubiald = 'on' ";  
				$query .= " 	 and Ubisac = Ccocod ";
				$query .= "  group by 1,2 ";
				$query .= "  order by cast(Ubiing as UNSIGNED) ";
			}
			else
			{
				$query  = "select Firhis,Firing,".$empresa."_000018.Fecha_data,Cconom from ".$historia."_000036,".$empresa."_000018,".$empresa."_000011 ";
				$query .= "   where firhis = '".$whis."' "; 
				$query .= "     and firhis = Ubihis ";
				$query .= "     and firing = Ubiing ";
				$query .= "     and Ubisac = Ccocod ";
				$query .= " group by 1,2 ";
				$query .= " order by cast(Firing as UNSIGNED) ";
			}
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<option></option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>Ing: ".$row[1]."- Fecha: ".$row[2]." Servicio: ".$row[3]."</option>";
				}
			}
			else
				echo "<option></option>";
			echo "</select>";
			echo "</td></tr>";
		}
		else
		{
			if($accion == "E")
			{
				$query  = "select Ubihis,Ubiing,".$empresa."_000018.Fecha_data,Cconom from ".$empresa."_000018,".$empresa."_000011 ";
				$query .= "    where Ubihis = '".$whis."' "; 
				$query .= " 	 and Ubisac = '".$wcco."' ";
				$query .= " 	 and ubiald = 'on' ";  
				$query .= " 	 and Ubisac = Ccocod ";
				$query .= "  group by 1,2 ";
				$query .= "  order by cast(Ubiing as UNSIGNED) ";
			}
			else
			{
				$query  = "select Firhis,Firing,".$empresa."_000018.Fecha_data,Cconom from ".$historia."_000036,".$empresa."_000018,".$empresa."_000011 ";
				$query .= "   where firhis = '".$whis."' "; 
				$query .= "     and firhis = Ubihis ";
				$query .= "     and firing = Ubiing ";
				$query .= "     and Ubisac = Ccocod ";
				$query .= " group by 1,2 ";
				$query .= " order by cast(Firing as UNSIGNED) ";
			}
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<option></option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>Ing: ".$row[1]."- Fecha: ".$row[2]." Servicio: ".$row[3]."</option>";
				}
			}
			else
				echo "<option></option>";
			echo "</select>";
			echo "</td></tr>";
		}
		//echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnin' size=10 maxlength=15 value='".$wnin."'></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='button' value='ENTER' onClick='enter()'></td></tr></table><br><br>";
		
		$numemp=0;
		$query  = "select Empemp,Rolemp from ".$historia."_000020,".$historia."_000019,".$historia."_000025 ";
		$query .= " where usucod = '".$key."' ";
		$query .= " and usurol = rolcod ";
		$query .= " and rolemp = empcod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			$SempT=explode(";",$row[0]);
			$numemp=$num;
			$wsel=$row[1];
		}
		
		if(strpos($wnin,"-") !== false)
		{
			$wnin = substr($wnin,0,strpos($wnin,"-"));
			$wnin = substr($wnin,5);
		}
			
		
		//                 0       1        2       3      4       5       6       7       8       9       10      11                            12
		$query  = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, ".$empresa."_000016.fecha_data, Ubifad  ";
		$query .= " from ".$empresa."_000016,".$empresa."_000018,root_000037,root_000036 ";
		$query .= " where inghis = '".$whis."' "; 
		$query .= "   and inging = '".$wnin."' ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging "; 
		if($accion == "E")
			$query .= "   and Ubisac = '".$wcco."' ";
		if( $accion != "COIDC" )
			if( (isset($accion) and $accion != "R" and $accion != "H" ) )
				$query .= "   and ubiald = 'on'  ";
		$query .= "   and orihis = ubihis  "; 
		$query .= "   and oriori = '".$wemp_pmla."' ";  
		$query .= "   and oriced = pacced ";  
		$query .= "   and oritid = pactid ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			$row = mysql_fetch_array($err);
			if($numemp == 0 or $wsel == "*" or $wsel == "NO APLICA" or buscarC($SempT,$numemp,$row[10]))
			{
				echo "<table border=0 align=center id=tipo5>";
				if($wemp_pmla == "10")
				{
					$pathIDC = "http://www.idclasamericas.co/MxB/mx.php?doc=".$row[2]."&tdoc=".$row[3]." ";
					echo "<tr><td id=tipoT07 align=right colspan=6><A HREF='".$pathIDC."' target='_blank'><IMG SRC='/matrix/images/medical/HCE/IDC.png'></A></td></tr>";
				}
				if($accion == "CO")
					echo "<tr><td id=tipoT04 align=center>HISTORIA<BR>CLINICA</td><td id=tipoT04 align=center>FECHA<BR>INGRESO</td><td id=tipoT04 align=center>NOMBRE<br>PACIENTE</td><td id=tipoT04 align=center>FECHA<br>NACIMIENTO</td><td id=tipoT04 align=center>SEXO</td><td id=tipoT04 align=center>RESPONSABLE</td><td id=tipoT04 align=center>ORDENES<br>MEDICAS</td></tr>";
				if( $accion == "COIDC")
					echo "<tr><td id=tipoT04 align=center>HISTORIA<BR>CLINICA</td><td id=tipoT04 align=center>FECHA<BR>INGRESO</td><td id=tipoT04 align=center>NOMBRE<br>PACIENTE</td><td id=tipoT04 align=center>FECHA<br>NACIMIENTO</td><td id=tipoT04 align=center>SEXO</td><td id=tipoT04 align=center>RESPONSABLE</td><td id=tipoT04 align=center>ENVIAR<br>HCE</td><td id=tipoT04 align=center>ORDENES<br>MEDICAS</td></tr>";
				else
					echo "<tr><td id=tipoT04 align=center>HISTORIA<BR>CLINICA</td><td id=tipoT04 align=center>FECHA<BR>INGRESO</td><td id=tipoT04 align=center>NOMBRE<br>PACIENTE</td><td id=tipoT04 align=center>FECHA<br>NACIMIENTO</td><td id=tipoT04 align=center>SEXO</td><td id=tipoT04 align=center>RESPONSABLE</td></tr>";
				
				$wcont++;
				if($wcont % 2 == 0)
					$tipo="tipo18";
				else
					$tipo="tipo19";
				$nombre=$row[6]." ".$row[7]." ".$row[4]." ".$row[5];
				if($row[9] == "F")
					$sexo="FEMENINO";
				else
					$sexo="MASCULINO";
				if(!isset($wdbhce))
					$wdbhce = $historia;
				switch($accion)
				{
					case "I":
						$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wing=".$row[1]."&wservicio=".$wservicio."&protocolos=0&CLASE=I&BC=1";
					break;
					case "A":
						$path="/matrix/HCE/procesos/HCE_Notas.php?empresa=".$historia."&wemp_pmla=".$wemp_pmla."&wdbmhos=".$empresa."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wing=".$wnin."&wservicio=".$wservicio;
					break;
					case "C":
						$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wing=".$row[1]."&wservicio=".$wservicio."&protocolos=0&CLASE=C&BC=1";
					break;
					case "CO":
						$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wing=".$row[1]."&wservicio=".$wservicio."&protocolos=0&CLASE=C&BC=1";
						// $path1="/matrix/hce/procesos/ordenes.php?wemp_pmla=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&hce=on&editable=off&et=on&historia=".$whis."&ingreso=".$row[1]."&programa=TableroAnt";
						$fecha = consultarUltimaFechaKardex( $conex, $empresa, $whis, $row[1] );
						$path1="/matrix/hce/procesos/ordenes.php?wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&editable=off&et=on&historia=".$whis."&ingreso=".$row[1]."&programa=TableroAnt&wfecha=".$fecha."&waccion=b";
					break;
					case "COIDC":
						$path="/matrix/HCE/procesos/HCE_Impresion.php?empresa=".$wdbhce."&wdbmhos=".$empresa."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wing=".$row[1]."&wservicio=".$wservicio."&protocolos=0&CLASE=C&BC=1";
						// $path1="/matrix/hce/procesos/ordenes.php?wemp_pmla=".$codemp."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&hce=on&editable=off&et=on&historia=".$whis."&ingreso=".$row[1]."&programa=TableroAnt";
						$fecha = consultarUltimaFechaKardex( $conex, $empresa, $whis, $row[1] );
						$path1="/matrix/hce/procesos/ordenesidc.php?wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&editable=off&et=on&historia=".$whis."&ingreso=".$row[1]."&programa=TableroAnt&wfecha=".$fecha."&waccion=b";
						$path2="/matrix/hce/procesos/envioCorreoHCEOrdenes.php?wemp_pmla=".$wemp_pmla."&historia=".$whis."&ingreso=".$row[1]."&esIframe=off";
					break;
					case "R":
						$path="/matrix/HCE/procesos/HCE_Resumen.php?empresa=".$wdbhce."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&origen=".$wemp_pmla."&wing=".$wnin;
					break;
					case "S":
						$path="/matrix/movhos/procesos/bitacora.php?ok=0&ctc=1&empresa=".$empresa."&codemp=".$wemp_pmla."&whis=".$whis."&wnin=".$wnin."";
					break;
					case "H":
						$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$wdbhce."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&whisa=".$whis."&winga=".$wnin."";
					break;
					case "E":
						$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$wdbhce."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&whisa=".$whis."&winga=".$wnin."";
					break;
				}
				echo "<tr style='cursor: hand;cursor: pointer;'><td id=".$tipo."A onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row[0]."-".$row[1]."</td><td id=".$tipo."A onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row[12]."</td><td id=".$tipo."A onclick='ejecutar(".chr(34).$path.chr(34).")'>".$nombre."</td><td id=".$tipo."A onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row[8]."</td><td id=".$tipo."A onclick='ejecutar(".chr(34).$path.chr(34).")'>".$sexo."</td><td id=".$tipo."A onclick='ejecutar(".chr(34).$path.chr(34).")'>".$row[10]."-".$row[11]."</td>";
				if($accion == "CO" )
					echo "<td id=".$tipo." onclick='ejecutar(".chr(34).$path1.chr(34).")'><IMG SRC='/matrix/images/medical/hce/OM.png'></td></tr>";
				if($accion == "COIDC" )
					echo "<td id=".$tipo." onclick='ejecutar(".chr(34).$path2.chr(34).")'><IMG SRC='/matrix/images/medical/hce/OM.png'></td><td id=".$tipo." onclick='ejecutar(".chr(34).$path1.chr(34).")'><IMG SRC='/matrix/images/medical/hce/OM.png'></td></tr>";
				else
					echo "</tr>";
				echo "</table></center>";
			}
		}
		else
		{
			if($whis != "" and $wnin != "")
			{
				echo "<table border=0 align=center id=tipo5>";
				echo "<tr><td id=tipoT02 colspan=8><IMG SRC='/matrix/images/medical/root/interes.gif' style='vertical-align:middle;'>&nbsp;LA HISTORIA E INGRESO NO EXISTE O NO TIENE ALTA DEFINITIVA</td></tr>";
				echo "</table></center>";
			}
		}
		if($accion == "E")
		{
			echo "<br><br><table border=0 align=center id=tipo5>";
			echo "<tr><td id=tipoT06 align=center colspan=6>PACIENTES ACTIVOS EN LA UNIDAD</td></tr>";
			echo "<tr><td id=tipoT04 align=center>HISTORIA<BR>CLINICA</td><td id=tipoT04 align=center>FECHA<BR>INGRESO</td><td id=tipoT04 align=center>NOMBRE<br>PACIENTE</td><td id=tipoT04 align=center>FECHA<br>NACIMIENTO</td><td id=tipoT04 align=center>SEXO</td><td id=tipoT04 align=center>RESPONSABLE</td></tr>";
			//                  0        1       2      3       4       5       6       7       8       9       10      11                      12
			$query  = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, ".$empresa."_000016.fecha_data  ";
			$query .= " from ".$empresa."_000018,root_000037,root_000036,".$empresa."_000016 ";
			$query .= " where ubiald = 'off'  ";
			$query .= " and ubisac = '".$wcco."' ";
			$query .= " and ubihis = orihis  ";
			$query .= " and ubiing = oriing  ";
			$query .= " and oriori = '".$wemp_pmla."'  ";
			$query .= " and oriced = pacced  ";
			$query .= " and oritid = pactid  ";
			$query .= " and orihis = inghis "; 
			$query .= " and oriing = inging  ";
			$query .= "  order by 13 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($wcont % 2 == 0)
						$tipo="tipo18";
					else
						$tipo="tipo19";
					$nombre=$row[6]." ".$row[7]." ".$row[4]." ".$row[5];
					if($row[9] == "F")
						$sexo="FEMENINO";
					else
						$sexo="MASCULINO";
					if(!isset($wdbhce))
						$wdbhce = $historia;
					$path="/matrix/HCE/procesos/HCE_iFrames.php?empresa=".$wdbhce."&wemp_pmla=".$wemp_pmla."&wcedula=".$row[2]."&wtipodoc=".$row[3]."&wdbmhos=".$empresa."&whisa=".$row[0]."&winga=".$row[1]."";
					echo "<tr style='cursor: hand;cursor: pointer;' onclick='ejecutar(".chr(34).$path.chr(34).")'><td id=".$tipo."A>".$row[0]."-".$row[1]."</td><td id=".$tipo."A>".$row[12]."</td><td id=".$tipo."A>".$nombre."</td><td id=".$tipo."A>".$row[8]."</td><td id=".$tipo."A>".$sexo."</td><td id=".$tipo."A>".$row[10]."-".$row[11]."</td></tr>";
				}
			}
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
