<html>
<head>
  	<title>MATRIX Tablero de Pacientes RIS-PACS</title>
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
    	#tipo2{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;}
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.tipo4{color:#000066;background:#dddddd;font-size:6pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;}
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo7{color:#FFFFFF;background:#000066;font-size:12pt;font-family:Tahoma;font-weight:bold;width:30em;}
    	#tipo8{color:#99CCFF;background:#000066;font-size:6pt;font-family:Tahoma;font-weight:bold;}
    	#tipo9{color:#660000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo10{color:#FFFFFF;background:#000066;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11{color:#000066;background:#999999;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11A{color:#000066;background:#DDDDDD;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo11B{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;height:3em;}
    	#tipo12{color:#000066;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:normal;text-align:left;}
    	#tipo13{color:#000066;background:#99CCFF;font-size:9pt;font-family:Tahoma;font-weight:normal;text-align:left;}
    	#tipo14{color:#FFFFFF;background:#000066;font-size:14pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo15{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo18{color:#000066;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    	#Tbueno{color:#000066;background:#00FF00;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#Tmalo{color:#000066;background:#FF0000;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
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
</head>
<body onload=ira() BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.Tablero_AGFA.submit();
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
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : Tablero_AGFA.php
	   Fecha de LiberaciÛn : 2009-08-31
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2009-09-28
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gr·fica que permite verificar los cargos que llegan
	   a facturacion a traves de mensajeria HL7 proveniente de RIS 
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2009-09-28
	   		Se modifico el programa para que la consulta en le archivo aymov de Servinte se hiciera basada en la fecha del
	   		mensaje DFT.
	   .2009-08-31
	   		Release de VersiÛn Beta.
	   		
***********************************************************************************************************************/
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
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
	$regular="^([=a-zA-Z0-9' 'Ò—@?/*#-.:;_<>])+$";
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

function comparacion($vec1,$vec2)
{
	if($vec1[0] > $vec2[0])
		return 1;
	elseif ($vec1[0] < $vec2[0])
				return -1;
			else
				return 0;
}

function VAL_MSG($key,$conex,$wok,$tipo,$numero,$prioridad,$werr,$e)
{
	if($wok == 0 or $prioridad == "on")
	{
	 	$query = "UPDATE agfa_000007 set Validado = 'on' where Tipo = '".$tipo."' and Numero = '".$numero."'  ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO MENSAJES INTEGRADOS");
		$e=$e+1;
		$werr[$e]="OK! MENSAJE VALIDADO";
		return;
	}
	else
	{
		$e=$e+1;
		$werr[$e]="ERROR MENSAJE NO VALIDADO !!! REVISE";
	}
	return;
}
		
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='Tablero_AGFA' action='Tablero_AGFA.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'tmensaje' value='".$tmensaje."'>";
	
	$prioridad="off";
	$query = "select Prioridad   ";
	$query .= " from ".$empresa."_000008 ";
	$query .= " where Codigo = '".$key."'  ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		$row = mysql_fetch_array($err);
		$prioridad=$row[0];
	}
	
	if($ok == 99)
	{
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		echo "<meta http-equiv='refresh' content='120;url=/matrix/INTERFACES/procesos/Tablero_AGFA.php?ok=99&empresa=".$empresa."&tmensaje=".$tmensaje."'>";
		echo "<table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.Tablero_AGFA.wfecha.focus();}
		</script>
		<?php
		echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/INTERFACES/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=5><font size=2>Ver. 2009-09-28 </font></td></tr>";
		echo "<tr><td align=center colspan=5 id=tipo14><b>TABLERO DE PACIENTES RIS - PASC</td></tr>";
		if (!isset($wfecha))
			$wfecha=date("Y-m-d");
		$year = (integer)substr($wfecha,0,4);
		$month = (integer)substr($wfecha,5,2);
		$day = (integer)substr($wfecha,8,2);
		$nomdia=mktime(0,0,0,$month,$day,$year);
		$nomdia = strftime("%w",$nomdia);
		$wsw=0;
		switch ($nomdia)
		{
			case 0:
				$diasem = "DOMINGO";
				break;
			case 1:
				$diasem = "LUNES";
				break;
			case 2:
				$diasem = "MARTES";
				break;
			case 3:
				$diasem = "MIERCOLES";
				break;
			case 4:
				$diasem = "JUEVES";
				break;
			case 5:
				$diasem = "VIERNES";
				break;
			case 6:
				$diasem = "SABADO";
				break;
		}
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA :</b></td>";
		echo "<td bgcolor='#cccccc' align=center><b>".$diasem."</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 readonly='readonly' value=".$wfecha." class=tipo6></td></tr>";
		echo "</table><br>";
		
		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		//                  0       1       2              3       4       5               6       7         8
		$query = "select Tipo, Numero, Identificacion, Paciente, Texto, Medico_rx, Facturable, Validado, Fecha_data  ";
		$query .= " from ".$empresa."_000007 ";
		$query .= " where Tipo = '".$tmensaje."'  ";
		if($tmensaje == "DFT")
			$query .= " and Facturable = 'on'  ";
		$query .= " and Validado = 'off'  ";
		$query .= " order by Numero ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			
			echo "<table border=0 align=center id=tipo5>";
			echo "<tr><td bgcolor='#cccccc' align=center colspan=10>".$num." MENSAJES ".$tmensaje." PENDIENTES DE VALIDACION</td></tr>";
			echo "<tr><td bgcolor='#999999' align=center>ITEM</td><td bgcolor='#999999' align=center>NUMERO</td><td bgcolor='#999999' align=center>IDENTIFICACION</td><td bgcolor='#999999' align=center>PACIENTE</td><td bgcolor='#999999' align=center>MEDICO</td><td bgcolor='#999999' align=center>SELECCION</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$j = $i + 1;
				$row = mysql_fetch_array($err);
				if($i % 2 == 0)
					$tipo="tipo13";
				else
					$tipo="tipo12";
				$path="/matrix/Interfaces/procesos/Tablero_AGFA.php?ok=0&empresa=".$empresa."&tmensaje=".$tmensaje."&numeromsj=".$row[1]."&identificacion=".$row[2]."&wfechaw=".$row[8]."";
				echo "<tr><td id=".$tipo.">".$j."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$row[2]."</td><td id=".$tipo.">".$row[3]."</td><td id=".$tipo.">".$row[5]."</td><td id=".$tipo."><A HREF='".$path."'>Editar</A></td></tr>";
			}
		}
		echo "</table></center>";
	}
	else
	{
		echo "<input type='HIDDEN' name= 'identificacion' value='".$identificacion."'>";
		echo "<input type='HIDDEN' name= 'numeromsj' value='".$numeromsj."'>";
		echo "<input type='HIDDEN' name= 'wfechaw' value='".$wfechaw."'>";
		echo "<table border=0 align=center id=tipo2>";
		echo "<tr><td align=center colspan=6><IMG SRC='/matrix/images/medical/INTERFACES/logo_".$empresa.".png'></td></tr>";
		echo "<tr><td align=right colspan=6><font size=2>Ver. 2009-09-28</font></td></tr>";
		//******* INICIALIZACION DEL SISTEMA *********
		if(isset($ok) and $ok == 9)
			$ok=0;
		
		//******* GRABACION DE INFORMACION *********
		if(isset($ok) and $ok == 2)
		{
			$werr=array();
			$e=-1;
			if(isset($wok))
				VAL_MSG($key,$conex,$wok,$tmensaje,$numeromsj,$prioridad,&$werr,&$e);
			$ok=1;
		}
		
		
		//********************************************************************************************************
		//*                                         DATOS DEL MENSAJE                                            *                                
		//********************************************************************************************************
		
		//                  0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16
		$query = "select Pactdc, Pacdoc, Pacnom, Pacap1, Pacap2, Pacfna, Pacsex, Pacest, Pacdir, Pactel, Pacpai, Pacmun, Pacbar, Paczon, Pacofi, Pacres, Pacmed ";
		$query .= " from ".$empresa."_000002 ";
		$query .= " where Pactdc = '".substr($identificacion,0,2)."'  ";
		$query .= "   and Pacdoc = '".substr($identificacion,2)."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		echo "<tr><td align=center colspan=6 id=tipo14><b>TABLERO DE PACIENTES RIS - PASC</b></td></tr>";
		$color="#dddddd";
		$color1="#000099";
		$color2="#006600";
		$color3="#cc0000";
		$color4="#CC99FF";
		$color5="#99CCFF";
		$color6="#FF9966";
		$color7="#cccccc";
		echo "<tr><td align=center bgcolor=#999999 colspan=6><b>MENSAJES ".$tmensaje." PENDIENTES DE VALIDACION</b></td></tr>";
		
		//PRIMERA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center>Tipo doc. : <br>".$row[0]."</td>";
		echo "<td bgcolor=".$color." align=center>Identificacion. :<br>".$row[1]."</td>";
		$nombre=$row[2]." ".$row[3]." ".$row[4];
		echo "<td bgcolor=".$color." align=center>Nombre :<br>".$nombre."</td>";
		echo "<td bgcolor=".$color." align=center>F. Nacimiento :<br>".$row[5]."</td>";
		echo "<td bgcolor=".$color." align=center>Sexo :<br>".$row[6]."</td>";
		switch($row[7])
		{
			case "S": 
				$estado="SOLTERO(a)";
			break;
			case "C": 
				$estado="CASADO(a)";
			break;
			case "V": 
				$estado="VIUDO(a)";
			break;
			case "M": 
				$estado="MENOR";
			break;
			case "D": 
				$estado="DIVORCIADO(a)";
			break;
			case "U": 
				$estado="UNION LIBRE";
			break;
		}
		echo "<td bgcolor=".$color." align=center>Estado Civil :<br>".$estado."</td>";
		echo "</tr>";
		
		//SEGUNDA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center>Direccion :<br>".$row[8]."</td>";
		echo "<td bgcolor=".$color." align=center>Telefonos :<br>".$row[9]."</td>";
		$query = "SELECT Nombre  from  root_000006 where Codigo='".$row[10]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
		}
		else
		{
			$row1=array();
			$row1[0]="";
		}
		echo "<td bgcolor=".$color." align=center>Pais :<br>".$row1[0]."</td>";
		$query = "SELECT Nombre  from  root_000006 where Codigo='".$row[11]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
		}
		else
		{
			$row1=array();
			$row1[0]="";
		}
		echo "<td bgcolor=".$color." align=center>Municipio :<br>".$row1[0]."</td>";
		$query = "SELECT Bardes  from  root_000034 where Barcod='".$row[11]."' and Barmun='".$row[12]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
		}
		else
		{
			$row1=array();
			$row1[0]="";
		}
		echo "<td bgcolor=".$color." align=center>Barrio :<br>".$row1[0]."</td>";
		switch($row[13])
		{
			case "R": 
				$zona="RURAL";
			break;
			case "U": 
				$zona="URBANA";
			break;
		}
		echo "<td bgcolor=".$color." align=center>Zona :<br>".$zona."</td>";
		echo "</tr>";
		
		//TERCERA LINEA
		$query = "SELECT Descripcion from  root_000003 where Codigo='".$row[14]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
		}
		else
		{
			$row1=array();
			$row1[0]="";
		}
		echo "<td bgcolor=".$color." align=center colspan=3>Oficio :<br>".$row1[0]."</td>";
		$query = "SELECT Empdes from ".$empresa."_000005 where Empnit='".$row[14]."' ";
		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num1 = mysql_num_rows($err1);
		if ($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
		}
		else
		{
			$row1=array();
			$row1[0]="";
		}
		echo "<td bgcolor=".$color." align=center colspan=3>Responsable :<br>".$row1[0]."</td>";
		
		
		echo "</tr>";
		
		//PARTE CENTRAL DE LA PANTALLA 
		echo "<td bgcolor=#cccccc colspan=3 align=center><input type='RADIO' name=ok value=1 checked onclick='enter()'><b>PROCESO</b></td><td bgcolor=#cccccc colspan=3 align=center><input type='RADIO' name=ok value=2 onclick='enter()'><b>VALIDAR</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=6 align=center><input type='submit' value='OK'></td></tr>";
		echo "<tr><td bgcolor=#ffffff colspan=6 align=center><A HREF='/MATRIX/Interfaces/Procesos/Tablero_AGFA.php?ok=99&empresa=".$empresa."&tmensaje=".$tmensaje."'><IMG SRC='/matrix/images/medical/INTERFACES/lista.png' alt='Lista'><br>Lista</A></td></tr></table><br><br></center>";
		if(isset($werr) and isset($e) and $e > -1)
		{
			echo "<br><br><center><table border=0 aling=center id=tipo2>";
			for ($i=0;$i<=$e;$i++)
				if(substr($werr[$i],0,3) == "OK!")
					echo "<tr><td align=center bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color5."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
				else
					echo "<tr><td align=center bgcolor=".$color4."><IMG SRC='/matrix/images/medical/root/Malo.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'></font></TD><TD bgcolor=".$color4."><font color=#000000 face='tahoma'><b>".$werr[$i]."</b></font></td></tr>";
			echo "</table><br><br></center>";
		}

			
		//********************************************************************************************************
		//*                             DATOS ASOCIADOS AL MENSAJE                                               *                                                                 
		//********************************************************************************************************
		$wok=0;
		echo "<table border=0 align=center id=tipo2>";
		echo "<tr><td id=tipo11A colspan=2>EXAMENES EN EL RIS</td></tr>";
		echo "<tr><td id=tipo11>Codigo</td><td id=tipo11>Descripcion</td></tr>";
		$query = "select Texto ";
		$query .= " from ".$empresa."_000007 ";
		$query .= " where Tipo   = '".$tmensaje."' ";
		$query .= "   and Numero = ".$numeromsj;
		$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row2 = mysql_fetch_array($err2);
		$items = explode("<BR>", $row2[0]);
		$filas=-1;
		$DFT2 = array();
		$numc=count($items) - 1;
		if ($numc>0)
		{
			$campus=array();
			for ($i=0;$i<$numc;$i++)
			{
				$campus[$i][0] = substr($items[$i],0,strpos($items[$i]," "));
				$campus[$i][1] = substr($items[$i],strpos($items[$i]," ") + 1);
				$campus[$i][2] ="off";
				if($i % 2 == 0)
				{
					$tipo="tipo13";
				}
				else
				{
					$tipo="tipo12";
				}
				$DFT1 = explode("-",$campus[$i][0]);
				for ($w=0;$w<count($DFT1);$w++)
				{
					if($DFT1[$w] != "")
					{
						$filas = $filas + 1;
						$DFT2[$filas][0] = $DFT1[$w];
				 		echo "<tr><td id=".$tipo.">".$DFT1[$w]."</td><td id=".$tipo.">".$campus[$i][1]."</td></tr>";
			 		}
		 		}
			 	echo "<input type='HIDDEN' name= 'campus[".$i."][0]' value='".$campus[$i][0]."'>";
			}
			if($filas > -1)
				usort($DFT2,'comparacion');
			echo "<input type='HIDDEN' name= 'numc' value='".$numc."'>";
			$query  = "select movdoc, movtip from aymov ";
			$query .= " where movfue = 'RA' ";
			$query .= "   and movtid = '".substr($identificacion,0,2)."'";
			$query .= "   and movced = '".substr($identificacion,2)."'";
			$query .= "   and movfec <= '".$wfechaw."'";
			$query .= " order by movdoc desc ";
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
			}
			if($count > 0)
			{
				if($odbc[1] == "I")
				{
					$tipop="HOSPITALIZADO";
					$query  = "select cardetcod, exanom from facardet, inexa ";
					$query .= " where cardetfue = 'RA' ";
					$query .= "   and cardetdoc = ".$odbc[0];
					$query .= "   and cardetcod <> '0' ";
					$query .= "   and cardetcod = exacod ";
					$conex_o = odbc_connect('facturacion','','');
					$err_o = odbc_do($conex_o,$query);
					$campos= odbc_num_fields($err_o);
					$count= -1 ;
					$Dodbc=array();
					while(odbc_fetch_row($err_o))
					{
						
						$count += 1;
						for($m=1;$m<=$campos;$m++)
						{
							$Dodbc[$count][$m-1]=odbc_result($err_o,$m);
						}
					}
				}
				else
				{
					$tipop="AMBULATORIO";
					$query  = "select cardetcod, exanom from aycardet, inexa ";
					$query .= " where cardetfue = 'RA' ";
					$query .= "   and cardetdoc = ".$odbc[0];
					$query .= "   and cardetcod <> '0' ";
					$query .= "   and cardetcod = exacod ";
					$conex_o = odbc_connect('facturacion','','');
					$err_o = odbc_do($conex_o,$query);
					$campos= odbc_num_fields($err_o);
					$count= -1 ;
					$Dodbc=array();
					while(odbc_fetch_row($err_o))
					{
						
						$count += 1;
						for($m=1;$m<=$campos;$m++)
						{
							$Dodbc[$count][$m-1]=odbc_result($err_o,$m);
						}
					}
				}
				$query  = "select drodetart,artnom from ivdrodet, ivart ";
				$query .= "  where drodetfue = 'RA' ";
				$query .= "    and drodetdoc = ".$odbc[0];
				$query .= "   and  drodetart = artcod ";
				$conex_o = odbc_connect('facturacion','','');
				$err_o = odbc_do($conex_o,$query);
				$campos= odbc_num_fields($err_o);
				while(odbc_fetch_row($err_o))
				{
					$count += 1;
					for($m=1;$m<=$campos;$m++)
					{
						$Dodbc[$count][$m-1]=odbc_result($err_o,$m);
					}
				}
				if($count > -1)
				usort($Dodbc,'comparacion');
				echo "<tr><td id=tipo11B colspan=2></td></tr>";
				echo "<tr><td id=tipo11B colspan=2></td></tr>";
				echo "<tr><td id=tipo11A colspan=2>EXAMENES EN SERVINTE NRO DE INGRESO : ".$odbc[0]." ".$tipop."</td></tr>";
				echo "<tr><td id=tipo11>Codigo</td><td id=tipo11>Descripcion</td></tr>";
				for ($i=0;$i<=$count;$i++)
				{
					if($i % 2 == 0)
					{
						$tipo="tipo13";
					}
					else
					{
						$tipo="tipo12";
					}
				 	echo "<tr><td id=".$tipo.">".$Dodbc[$i][0]."</td><td id=".$tipo.">".$Dodbc[$i][1]."</td></tr>";
				}
			}
		}
		echo "</table>";
		if($count  == $filas)
		{
			for ($i=0;$i<=$count;$i++)
			{
				if(isset($Dodbc[$i][0]) and isset($DFT2[$i][0]) and $Dodbc[$i][0] != $DFT2[$i][0])
					$wok = 1;
			}
		}
		else
			$wok = 1;
		echo "<br><br><table border=0 align=center id=tipo2>";
		if($wok == 0)
			echo "<tr><td id=Tbueno>COMPATIBLES</td></tr>";
		else
			echo "<tr><td id=Tmalo>INCOMPATIBLES</td></tr>";
		echo "</table>";
		echo "<input type='HIDDEN' name= 'wok' value='".$wok."'>";
		echo"</form>";
	}
}
?>
</body>
</html>