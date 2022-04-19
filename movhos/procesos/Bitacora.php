<html>
<head>
  	<title>MATRIX Bitacora de Pacientes</title>
  	<!-- UTF-8 is the recommended encoding for your pages -->

	<!--LAS SIGUIENTES LIBRERIAS SE AGREGAN CON comun.php-->
    <!--<title>Zapatec DHTML Calendar</title>-->
   <!-- Loading Theme file(s) -->
    <!--<link rel="stylesheet" href="../../zpcal/themes/winter.css" />-->

<!-- Loading Calendar JavaScript files -->
    <!--<script type="text/javascript" src="../../zpcal/src/utils.js"></script>-->
    <!--<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>-->
    <!--<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>-->
    <!-- Loading language definition file -->

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>


    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
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
    	#tipo16{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo17{color:#000066;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	#tipo18{color:#000066;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;}  	
    	#tipo19{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	
    	#tipo20{color:#FF0000;background:#DDDDDD;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;font-style: italic;}
    	#tipo21{color:#FF0000;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;font-style: italic;}
    	
    	#tipo22{color:#FF0000;background:#CC99FF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;font-style: italic;}
    	#tipo23{color:#FF0000;background:#FFCC66;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;font-style: italic;}
    	#tipo24{color:#FF0000;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:left;font-style: italic;}

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

	$(document).on('change','#selectsede',function(){
		window.location.href = "Bitacora.php?wemp_pmla="+$("#wemp_pmla").val()+"&empresa="+$('#empresa').val()+"&ok="+$("#ok").val()+"&selectsede="+$("#selectsede").val();
	});

	function enter()
	{
		document.forms.bitacora.submit();
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
if(isset($_REQUEST['codemp']) && !isset($_REQUEST['wemp_pmla'])){
	$wemp_pmla=$_REQUEST['codemp'];
}
elseif(isset($_REQUEST['wemp_pmla'])){
	$wemp_pmla = $_REQUEST['wemp_pmla'];
}
else{
	die('Falta parametro wemp_pmla...');
}
include_once("conex.php");

	$wactualiz = "23 de marzo de 2022";

/**********************************************************************************************************************
	   PROGRAMA : bitacora.php
	   Fecha de Liberaci? : 2007-06-05
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2015-04-13

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gr?ica que permite grabar los  de las
	   cirugias en los diversos quirofanos y en las horas especificadas por los cirujanos.

	
	   REGISTRO DE MODIFICACIONES :
	   11/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla y se actualiza encabezado
	   	
	   .23 de marzo de 2022 Sebastian Alvarez Barona
			Se agrega el filtro de sedes (Sede80 y SedeSur) a la información ofrecida en pacientes activos
	      
	   .2018-05-24 Jonatan Lopez
			Se agrega el c?igo de autorizaci? proveniente de la admisi?, al ingresa en la edici? del paciente.
	   
	   .2016-10-04 Jonatan Lopez
			Se comenta la linea en la que se filtra el proceso de traslado para pacientes de cirugia y que al trasladarlos de urgencias este 
			parametro queda en off y no es posible gestionarles bitacora.
	   
	   .2015-04-13
			Se adiciona al query de pacientes en urgencias los pacientes que estan en areas de admision es decir
			las historias que en la tabla 11 tienen en "on" la variable Ccoadm.
			
	   .2014-01-08
			Se coloca en color rojo los pacientes que en la bitacora tengan al menos un registro de Paciente Especial PE.
			Esta opcion la pueden acceder los usuarios que esten matriculados en la tabla 34 de Movimiento Hospitalario.
			
       .2013-06-14
            Se agrega la columna fecha de ingreso en todas las tablas, para mostrar la fecha de ingreso del paciente a la clinica(movhos_000016).
            -Jonatan Lopez
            
	   .2013-02-04
			Cada textarea tendra el tamano justo para el texto.
			Se agrega la funcion encabezadotabla.
			La fila con los titulos "Numero	Fecha	Hora	Usuario	Tema	Observacion" no se repetira por cada registro
			-Frederick Aguirre

	   .2012-08-21
			Se agrego la columna afinidad, en todas la tablas que se muestran para determinar si un paciente es afin. En las consultas que muestran
			los datos se agregaron los campos Pacced, Pactid para enviarlos a la funcion que dice si un paciente es afin. Viviana Rodas

	   .2010-04-13
	   		Se modifico $dateB se aumento a 4 en urgencias.

	   .2009-03-03
	   		Se modificaron en el programa todas las consultas a la base de datos que involucren las tablas 36 y 37 de root,
	   		para hacer el join por documento y tipo de documento. Esto con el proposito de que los querys sean mas eficientes.

	   .2008-10-01
	   		Se modifico el programa para incluir en la lista de pacientes los que estan un cirugia y no se les ha asignado cama.
	   		Tambien se modifico el programa para cambiar el acceso de la tabla 5 de costos y presupuestos a la 11 de movimiento
	   		hospitalario con los siguientes parametros:

	   		ccohos : (on) Pacientes Hospitalizados
	   		ccourg : (on) Pacientes En Urgencias
	   		ccocir : (on) Pacientes En Cirugia

	   .2008-07-14
	   		Se modifico el programa para reconstruir el query de los pacientes fallecidos ya que la consulta a la tabla 33 no
			estaba filtrando la opcion de alta x muerte.

	   .2008-05-20
	   		Se modifico el programa para recosntruir el query de los pacientes fallecidos ya que la muerte antes y despues de 48
            horas actualmente queda registrada en la tabla 33 y no en la tabla 18.

	   .2007-11-22
	   		Se modifico el programa para incluir los pacientes que fallecieron y un hipervinculo a consulta en la bitacora.

	   .2007-10-25
	   		Se modifico el programa para incluir los pacientes que ingresaron a urgencias en los dos ultimos dias.

	   .2007-10-23
	   		Se reestructuro el query para que el orden de presentacion de la lista cambiara a pacientes en alta,
	   		pacientes en translado y los anteriores x habitacion. Tambien se cambio la presentacion colocando
	   		el numero de la habitacion en la primera fila.

	   .2007-10-20
	   		Se reestructuro el query de las lista para realizar validacion con la tabla 20.

	   .2007-10-17
	   		Se reestructuro el query de las lista para que ejecutara de forma mas eficiente.

	   .2007-10-16
	   		Se reestructuro el query de las lista para NO mostrar los pacientes de urgencias.

	   .2007-08-06
	   		Se organizo la lista de registro de la bitacora en orden descendente de grabacion. Es decir la bitacora siempre
	   		mostrara el ultimo comentario de primero.

	   .2007-07-24
	   		Se retiraron de la lista de despliegue los datos del tipo de documento, indentificacion, sexo y fecha de nacimiento.
	   		La lista se organizo por pacientes en proceso de alta, pacientes en proceso de translado, servicio actual y cedula.

	   .2007-05-03
	   		Release de Versi? Beta.


***********************************************************************************************************************/
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0]) and $d[0] == $k)
			return 0;
		else
			return -1;
}

 //Fecha de ingreso del paciente
 function fecha_ingreso($whis, $wing, $conex)
 {

   global $empresa;

    $q_ingreso = " SELECT Fecha_data "
                ."   FROM ".$empresa."_000016 "
                ."  WHERE inghis = '".$whis."'"
                ."    AND inging = '".$wing."'";
    $res_ingreso = mysql_query($q_ingreso,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q_ingreso." - ".mysql_error());
    $row_ingreso = mysql_fetch_array($res_ingreso);
    $wfec_ing_pac = $row_ingreso['Fecha_data'];

    return $wfec_ing_pac;

 }


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
	$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
	if (preg_match($decimal,$chain,$occur))
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
	$regular="/^(\+|-)?([[:digit:]]+)$/";
	if (preg_match($regular,$chain,$occur))
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
	$fecha="/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/";
	if(preg_match($fecha,$chain,$occur))
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
	$regular="/[A-Za-z0-9]/i";
	return (preg_match($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="/^([0-9:])+$/";
	return (preg_match($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	if(preg_match($hora,$chain,$occur))
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
	$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
	if(preg_match($hora,$chain,$occur))
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
	//echo ' $wtem '.($wtem);
	//echo '<br> $wtem '.validar4($wtem);
	//echo '<br> validar5 '.validar5('123456');

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


function ING_TUR($key,$conex,$whis,$wnin,$wusr,$wtem,$wreg,&$werr,&$e)
{
	global $empresa;
	$query = "select Connum from ".$empresa."_000001 where Contip='Bitacora'";
	$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
	$row = mysql_fetch_array($err);
	$wnci=$row[0] + 1;
	$query =  " update ".$empresa."_000001 set Connum = Connum + 1 where Contip='Bitacora'";
	$err = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	$query = "insert ".$empresa."_000021 (medico,fecha_data,hora_data, Bithis, Biting, Bitnum, Bitusr, Bittem, Bitobs, Seguridad) values ('";
	$query .=  $empresa."','";
	$query .=  $fecha."','";
	$query .=  $hora."','";
	$query .=  $whis."','";
	$query .=  $wnin."',";
	$query .=  $wnci.",'";
	$query .=  substr($wusr,0,strpos($wusr,"-"))."','";
	$query .=  substr($wtem,0,strpos($wtem,"-"))."','";
	$query .=  mysql_real_escape_string($wreg)."','C-".$empresa."')";
	$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO BITACORA : ".mysql_errno().":".mysql_error());

	$e=$e+1;
	$werr[$e]="OK! BITACORA GRABADA";
	return true;
}

@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='bitacora' action='Bitacora.php' method=post>";	

	include_once("root/magenta.php");  //para saber si el paciente es afin
	include_once("root/comun.php");	

	echo "<center><input type='HIDDEN' name= 'empresa' id='empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'sede' id='sede' value='".$selectsede."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>"; 

	if(isset($ctc))
		echo "<input type='HIDDEN' name= 'ctc' value='".$ctc."'>";

	if($ok == 99)
	{
		// $wemp_pmla = $codemp;
		encabezado("BITACORA DE PACIENTES", $wactualiz, "clinica", TRUE);

			$estadosede=consultarAliasPorAplicacion($conexion, $wemp_pmla, "filtrarSede");
			$sFiltroSede="";
			$codigoSede = '';
			if($estadosede=='on')
			{	  
				$codigoSede = (isset($selectsede)) ? $selectsede : consultarsedeFiltro();
				$sFiltroSede = (isset($codigoSede) && ($codigoSede != '')) ? " AND Ccosed = '{$codigoSede}' " : "";
			}

		$sUrlCodigoSede = ($estadosede=='on') ? '&selectsede='.$codigoSede : '';

		echo "<input type='HIDDEN' name= 'ok' id='ok' value='".$ok."'>";
		echo "<meta http-equiv='refresh' content='900;url=/matrix/movhos/procesos/bitacora.php?ok=99&empresa=".$empresa."&wemp_pmla=".$wemp_pmla.$sUrlCodigoSede."'>";
		echo "<table border=0 align=center id=tipo5>";
		?>
		<script>
			function ira(){document.bitacora.wfecha.focus();}
		</script>
		<?php
		//echo "<tr><td align=center colspan=5><IMG SRC='/matrix/images/medical/movhos/logo_".$empresa.".png'></td></tr>";
		//echo "<tr><td align=right colspan=5><font size=2>Ver. 2010-04-13 </font></td></tr>";
		//echo "<tr><td align=center colspan=5 id=tipo14><b>BITACORA DE PACIENTES</b></td></tr>";
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
		
		$ESPECIALES=array();
		$query = "select Ubihis ";
		$query .= " from ".$empresa."_000018,".$empresa."_000021 ";
		$query .= " where ubiald = 'off'  ";
		$query .= "   and ubihis = Bithis ";
 		$query .= "   and Bittem = 'PE' ";
 		$query .= " Group by Ubihis ";
		$query .= " Order by Ubihis ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$ESPECIALES[$i] = $row[0];
			}
			$numesp=$num;
		}
		else
			$numesp=-1;
		
		
		echo "<tr><td bgcolor='#cccccc' align=center><b>FECHA :</b></td>";
		echo "<td bgcolor='#cccccc' align=center><b>".$diasem."</b></td>";
		echo "<td bgcolor='#cccccc' align=center valign=center><input type='TEXT' name='wfecha' size=10 maxlength=10 readonly='readonly' value=".$wfecha." class=tipo6></td></tr>";
		echo "</table><br>";
		echo "<table border=0 align=center id=tipo2>";
		echo "<tr><td bgcolor='#999999' rowspan=2><IMG SRC='/matrix/images/medical/root/procesos.ico'  alt='PROCESOS'><td align=center bgcolor='#999999'>Consulta a la Bitacora de Pacientes</td></tr>";
		echo "<tr><td align=center bgcolor='#dddddd'><A HREF='/matrix/movhos/procesos/rbitacora.php?ok=99&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&codemp=".$wemp_pmla."' target='_blank'><IMG SRC='/matrix/images/medical/movhos/Consulta.png' alt='Consulta'></A></td></tr>";
		echo "<tr><td align=center colspan='2'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></td></tr></table><br>";

		echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
		//                  0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15      16        17       18
		$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr, Habcod, Pacced, Pactid ";
		$query .= " from ".$empresa."_000018,".$empresa."_000011,".$empresa."_000020,root_000037,root_000036,".$empresa."_000016 ";
		$query .= " where ubiald = 'off'  ";
		$query .= " and ubisac = Ccocod  ";
		$query .= " and Ccohos = 'on'  ";
		$query .= " and ubihis = Habhis ";
 		$query .= " and ubiing = Habing ";
		$query .= " and ubihis = orihis  ";
		$query .= " and ubiing = oriing  ";
		$query .= " and oriori = '".$wemp_pmla."'  ";
		$query .= " and oriced = pacced  ";
		$query .= " and oritid = pactid  ";
		$query .= " and orihis = inghis ";
		$query .= " and oriing = inging  ";
		$query .= " {$sFiltroSede}  ";

		//$query .= " order by Ubialp DESC, Ubiptr DESC, Ubisac, Pacced ";
		$query .= " order by Ubialp DESC, Ubiptr DESC, Habcod ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
        echo "<table id=tipo5  style='text-align: left; width: 1250px;' border='0' >
                <tbody>
                    <tr>
                    <td style='width: 662px;'></td>
                    <td id=tipo18 style='width: 213px;'>PACIENTE EN PROCESO DE ALTA</td>
                    </tr>
                    <tr>
                    <td style='width: 662px;'></td>
                    <td id=tipo17 style='width: 213px;'>PACIENTE EN PROCESO DE TRANSLADO</td>
                    </tr>
                    <tr>
                    <td style='width: 662px;'></td>
                    <td id=tipo24 style='width: 213px;'>PACIENTE ESPECIAL</td>
                    </tr>
                </tbody>
                </table>";
			echo "<table border=0 align=center id=tipo5>";
			echo "<tr><td bgcolor='#cccccc' align=center colspan=11>PACIENTES ACTIVOS</td></tr>";
			echo "<tr><td bgcolor='#999999' align=center>HABITACION</td><td bgcolor='#999999' align=center>HISTORIA</td><td bgcolor='#999999' align=center>NRO. INGRESO</td><td bgcolor='#999999' align=center>FECHA DE INGRESO</td><td bgcolor='#999999' align=center>NOMBRE</td><td bgcolor='#999999' align=center>SERVICIO<BR>ACTUAL</td><td bgcolor='#999999' align=center>DESCRIPCION<BR>SERVICIO</td><td bgcolor='#999999' align=center>RESPONSABLE</td><td bgcolor='#999999' align=center>DESCRIPCION<br>RESPONSABLE</td><td bgcolor='#999999' align=center>AFINIDAD</td><td bgcolor='#999999' align=center>SELECCION</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$especial=0;
				$pos=bi($ESPECIALES,$numesp,$row[0]);
				if($pos != -1)
					$especial=1;
				if($i % 2 == 0)
				{
					if($row[14] == "on")
					{
						if($especial == 0)
							$tipo="tipo18";
						else
							$tipo="tipo23";
					}
					elseif($row[15] == "on")
						{
							if($especial == 0)
								$tipo="tipo17";
							else
								$tipo="tipo22";
						}
						elseif($especial == 0)
								$tipo="tipo12";
							else
								$tipo="tipo20";
				}
				else
				{
					if($row[14] == "on")
					{
						if($especial == 0)
							$tipo="tipo18";
						else
							$tipo="tipo23";
					}
					elseif($row[15] == "on")
						{
							if($especial == 0)
								$tipo="tipo17";
							else
								$tipo="tipo22";
						}
						elseif($especial == 0)
								$tipo="tipo13";
							else
								$tipo="tipo21";;
				}
				$nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
                $wfecha_ingreso = fecha_ingreso($row[0], $row[1], $conex);
				$path="/matrix/movhos/procesos/bitacora.php?ok=0&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&whis=".$row[0]."&wnin=".$row[1]."";
				echo "<tr><td id=".$tipo.">".$row[16]."</td><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$wfecha_ingreso."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[10]."</td><td id=".$tipo.">".$row[11]."</td><td id=".$tipo.">".$row[12]."</td><td id=".$tipo.">".$row[13]."</td>";

				$wdpa=$row[17];
				$wtid=$row[18];

				// ======================================================================================================
				// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				$wafin = clienteMagenta($wdpa, $wtid, $wtpa, $wcolorpac);
				if ($wafin)
				{
					echo "<td align=center id=".$tipo."><font color=".$wcolorpac."><b>".$wtpa."</b></font></td>";

				}
				else
				   echo "<td id=".$tipo.">&nbsp</td>";

				// ======================================================================================================


				echo "<td id=".$tipo."><A HREF='".$path."'>Editar</A></td></tr>";
			}
		}
		echo "</table></center>";


		//PACIENTES EN CIRUGIA
		$dateA=date("Y-m-d");
		$dateB=strtotime("-1 day");
		$dateC=strftime("%Y-%m-%d",$dateB);
		//                   0      1        2      3       4        5      6       7       8       9       10      11
		$query  = "select Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Pacced, Pactid";
		$query .= " from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016 ";
		//$query .= " where ".$empresa."_000018.fecha_data between '".$dateC."' and '".$dateA."' ";
		$query .= " where ubiald = 'off' ";
		$query .= "   and ubisac = Ccocod  ";
		$query .= "   and Ccocir = 'on'  ";
		$query .= "   and ubihis = orihis ";
		$query .= "   and ubiing = oriing ";
		//$query .= "   and Ubiptr = 'on'  "; Se comenta esta linea para que muestre todos los pacientes activos de cirugia.
		$query .= "   and oriori = '".$wemp_pmla."' ";
		$query .= "   and oriced = pacced ";
		$query .= "   and oritid = pactid  ";
		$query .= "   and orihis = inghis ";
 		$query .= "   and oriing = inging ";
		$query .= " {$sFiltroSede}  ";
		$query .= " order by Ubihis  ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<BR><BR><BR><center><table border=0 align=center id=tipo5>";
			echo "<tr><td bgcolor='#cccccc' align=center colspan=9>PACIENTES EN CIRUGIA</td></tr>";
			echo "<tr><td bgcolor='#999999' align=center>HISTORIA</td><td bgcolor='#999999' align=center>NRO. INGRESO</td><td bgcolor='#999999' align=center>FECHA DE INGRESO</td><td bgcolor='#999999' align=center>NOMBRE</td><td bgcolor='#999999' align=center>RESPONSABLE</td><td bgcolor='#999999' align=center>DESCRIPCION<br>RESPONSABLE</td><td bgcolor='#999999' align=center>AFINIDAD</td><td bgcolor='#999999' align=center>SELECCION</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$especial=0;
				$pos=bi($ESPECIALES,$numesp,$row[0]);
				if($pos != -1)
					$especial=1;
				$nombre=$row[3]." ".$row[4]." ".$row[5]." ".$row[6];
				if($i % 2 == 0)
					if($especial == 0)
						$tipo="tipo12";
					else
						$tipo="tipo20";
				else
					if($especial == 0)
						$tipo="tipo13";
					else
						$tipo="tipo21";
				$nombre=$row[2]." ".$row[3]." ".$row[4]." ".$row[5];
                $wfecha_ingreso = fecha_ingreso($row[0], $row[1], $conex);
				$path="/matrix/movhos/procesos/bitacora.php?ok=0&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&whis=".$row[0]."&wnin=".$row[1]."";
				echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$wfecha_ingreso."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[8]."</td><td id=".$tipo.">".$row[9]."</td>";

				$wdpa=$row[10];
				$wtid=$row[11];
				// ======================================================================================================
				// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				$wafin = clienteMagenta($wdpa, $wtid, $wtpa, $wcolorpac);
				if ($wafin)
				{
					echo "<td align=center id=".$tipo."><font color=".$wcolorpac."><b>".$wtpa."</b></font></td>";

				}
				else
				   echo "<td id=".$tipo.">&nbsp</td>";

				// ======================================================================================================



				echo "<td id=".$tipo."><A HREF='".$path."'>Editar</A></td></tr>";
			}
			echo "</table></center>";
		}


		//PACIENTES EN URGENCIAS AREAS DE ADMISION
		$dateA=date("Y-m-d");
		//2010-04-13
		$dateB=strtotime("-4 day");
		$dateC=strftime("%Y-%m-%d",$dateB);
		//                   0      1        2      3       4        5      6       7       8       9       10      11
		$query  = "select Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Pacced, Pactid ";
		$query .= " from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016 ";
		$query .= " where ".$empresa."_000018.fecha_data between '".$dateC."' and '".$dateA."' ";
		$query .= "   and ubiald = 'off' ";
		$query .= "   and ubisac = Ccocod  ";
		$query .= "   and Ccourg = 'on'  ";
		$query .= "   and ubihis = orihis ";
		$query .= "   and ubiing = oriing ";
		$query .= "   and oriori = '".$wemp_pmla."' ";
		$query .= "   and oriced = pacced ";
		$query .= "   and oritid = pactid  ";
		$query .= "   and orihis = inghis ";
 		$query .= "   and oriing = inging ";
		$query .= " {$sFiltroSede}  ";
 		$query .= " UNION ALL ";
 		$query .= "select Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Pacced, Pactid ";
		$query .= " from ".$empresa."_000018,".$empresa."_000011,root_000037,root_000036,".$empresa."_000016 ";
		$query .= " where ".$empresa."_000018.fecha_data between '".$dateC."' and '".$dateA."' ";
		$query .= "   and ubiald = 'off' ";
		$query .= "   and ubisac = Ccocod  ";
		$query .= "   and Ccoadm = 'on'  ";
		$query .= "   and ubihis = orihis ";
		$query .= "   and ubiing = oriing ";
		$query .= "   and oriori = '".$wemp_pmla."' ";
		$query .= "   and oriced = pacced ";
		$query .= "   and oritid = pactid  ";
		$query .= "   and orihis = inghis ";
 		$query .= "   and oriing = inging ";
		$query .= " {$sFiltroSede}  ";
		$query .= " order by Ubihis  ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<BR><BR><BR><center><table border=0 align=center id=tipo5>";
			echo "<tr><td bgcolor='#cccccc' align=center colspan=9>PACIENTES EN URGENCIAS O AREAS DE ADMISION DESDE ".$dateC." HASTA ".$dateA."</td></tr>";
			echo "<tr><td bgcolor='#999999' align=center>HISTORIA</td><td bgcolor='#999999' align=center>NRO. INGRESO</td><td bgcolor='#999999' align=center>FECHA DE INGRESO</td><td bgcolor='#999999' align=center>NOMBRE</td><td bgcolor='#999999' align=center>RESPONSABLE</td><td bgcolor='#999999' align=center>DESCRIPCION<br>RESPONSABLE</td><td bgcolor='#999999' align=center>AFINIDAD</td><td bgcolor='#999999' align=center>SELECCION</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$especial=0;
				$pos=bi($ESPECIALES,$numesp,$row[0]);
				if($pos != -1)
					$especial=1;
				$nombre=$row[3]." ".$row[4]." ".$row[5]." ".$row[6];
				if($i % 2 == 0)
					if($especial == 0)
						$tipo="tipo12";
					else
						$tipo="tipo20";
				else
					if($especial == 0)
						$tipo="tipo13";
					else
						$tipo="tipo21";
				$nombre=$row[2]." ".$row[3]." ".$row[4]." ".$row[5];
                $wfecha_ingreso = fecha_ingreso($row[0], $row[1], $conex);
				$path="/matrix/movhos/procesos/bitacora.php?ok=0&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&whis=".$row[0]."&wnin=".$row[1]."";
				echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$wfecha_ingreso."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[8]."</td><td id=".$tipo.">".$row[9]."</td>";

				$wdpa=$row[10];
				$wtid=$row[11];
				// ======================================================================================================
				// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				$wafin = clienteMagenta($wdpa, $wtid, $wtpa, $wcolorpac);
				if ($wafin)
				{
					echo "<td align=center id=".$tipo."><font color=".$wcolorpac."><b>".$wtpa."</b></font></td>";

				}
				else
				   echo "<td id=".$tipo.">&nbsp</td>";

				// ======================================================================================================



				echo "<td id=".$tipo."><A HREF='".$path."'>Editar</A></td></tr>";
			}
			echo "</table></center>";
		}

		//PACIENTES EN FALLECIDOS
		//                   0      1        2      3       4        5      6       7       8       9           10          11      12
		$query  = "select Ubihis, Ubiing, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ingres, Ingnre, Tipo_egre_serv, Pacced, Pactid  ";
		$query .= "  from ".$empresa."_000018,root_000037,root_000036,".$empresa."_000016,".$empresa."_000033, ".$empresa."_000011 ";
		$query .= "  where ubiald = 'off'   ";
		$query .= "    and Ubimue = 'on'   ";
		$query .= "    and Ubisac = Ccocod   ";
		$query .= "    and ubihis = orihis  ";
		$query .= "    and ubiing = oriing  ";
		$query .= "    and oriori = '".$wemp_pmla."' ";
		$query .= "    and oriced = pacced  ";
		$query .= "    and oritid = pactid  ";
		$query .= "    and orihis = inghis  ";
		$query .= "    and oriing = inging  ";
		$query .= "    and inghis = Historia_clinica  ";
		$query .= "    and inging = Num_ingreso  ";
		$query .= "    and Tipo_egre_serv like 'MUERTE%'  ";
		$query .= " {$sFiltroSede}  ";
		$query .= "  order by Ubihis  ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<BR><BR><center><input type=button value='Cerrar ventana' onclick='javascript:window.close();'><BR><table border=0 align=center id=tipo5>";
			echo "<tr><td bgcolor='#cccccc' align=center colspan=9>PACIENTES FALLECIDOS</td></tr>";
			echo "<tr><td bgcolor='#999999' align=center>HISTORIA</td><td bgcolor='#999999' align=center>NRO. INGRESO</td><td bgcolor='#999999' align=center>FECHA DE INGRESO</td><td bgcolor='#999999' align=center>NOMBRE</td><td bgcolor='#999999' align=center>RESPONSABLE</td><td bgcolor='#999999' align=center>DESCRIPCION<br>RESPONSABLE</td><td bgcolor='#999999' align=center>ANTES DE <br>48 HORAS</td><td bgcolor='#999999' align=center>DESPUES DE <br>48 HORAS</td><td bgcolor='#999999' align=center>AFINIDAD</td><td bgcolor='#999999' align=center>SELECCION</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$especial=0;
				$pos=bi($ESPECIALES,$numesp,$row[0]);
				if($pos != -1)
					$especial=1;
				$nombre=$row[3]." ".$row[4]." ".$row[5]." ".$row[6];
                $wfecha_ingreso = fecha_ingreso($row[0], $row[1], $conex);
                if($especial == 1)
					$tipo="tipo20";
				elseif($i % 2 == 0)
						$tipo="tipo12";
					else
						$tipo="tipo13";
				$nombre=$row[2]." ".$row[3]." ".$row[4]." ".$row[5];
				if($row[10] == "MUERTE MAYOR A 48 HORAS")
				{
					$row[10]="on";
					$row[11]="off";
				}
				else
				{
					$row[10]="off";
					$row[11]="on";
				}
				$path="/matrix/movhos/procesos/bitacora.php?ok=0&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&whis=".$row[0]."&wnin=".$row[1]."";
				echo "<tr><td id=".$tipo.">".$row[0]."</td><td id=".$tipo.">".$row[1]."</td><td id=".$tipo.">".$wfecha_ingreso."</td><td id=".$tipo.">".$nombre."</td><td id=".$tipo.">".$row[8]."</td><td id=".$tipo.">".$row[9]."</td><td id=".$tipo.">".$row[11]."</td><td id=".$tipo.">".$row[10]."</td>";

				$wdpa=$row[11];
				$wtid=$row[12];
				// ======================================================================================================
				// En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				$wafin = clienteMagenta($wdpa, $wtid, $wtpa, $wcolorpac);
				if ($wafin)
				{
					echo "<td align=center id=".$tipo."><font color=".$wcolorpac."><b>".$wtpa."</b></font></td>";

				}
				else
				   echo "<td id=".$tipo.">&nbsp</td>";

				// ======================================================================================================



				echo "<td id=".$tipo."><A HREF='".$path."'>Editar</A></td></tr>";
			}
			echo "</table></center>";
		}
	}
	else
	{
		encabezado("BITACORA DE PACIENTES", $wactualiz, "clinica");

		echo "<table border=0 align=center id=tipo2>";
		//echo "<tr><td align=center colspan=6><IMG SRC='/matrix/images/medical/movhos/logo_".$empresa.".png'></td></tr>";
		//echo "<tr><td align=right colspan=6><font size=2>Ver. 2010-04-13 </font></td></tr>";
		//******* INICIALIZACION DEL SISTEMA *********
		if(isset($ok) and $ok == 9)
			$ok=0;

		//******* GRABACION DE INFORMACION *********
		if(isset($ok) and $ok == 2)
		{
			$werr=array();
			$e=-1;
			if(valgen($ok,$conex,$wtem,$werr,$e))
			{
				$query = "lock table ".$empresa."_000001 LOW_PRIORITY WRITE, ".$empresa."_000021 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE ARCHIVOS : ".mysql_errno().":".mysql_error());
				if(ING_TUR($key,$conex,$whis,$wnin,$wusr,$wtem,$wreg,$werr,$e))
				{
					$ok=0;
					unset($wtem);
				}
				$query = " UNLOCK TABLES";
				$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());
				if($ok != 0)
					$ok = 1;
			}
			else
				$ok=1;
		}


		//******* INICIALIZACION DE CAMPOS *********
		if(isset($ok) and $ok == 0)
		{
			$wusr="";
			$query = "SELECT Codigo, Descripcion  from usuarios where Codigo='".$key."' ";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
			$wusr=$row1[0]."-".$row1[1];
			$wreg="";
			$ok=1;
		}

		//*******PROCESO DE INFORMACION *********

		//********************************************************************************************************
		//*                                         DATOS DEL PACIENTE                                             *
		//********************************************************************************************************
		$wbasedatoCliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
		$query_aut =  " SELECT Ingord 
						  FROM ".$wbasedatoCliame."_000101 
						 WHERE Inghis = '".$whis."'
						   AND Ingnin = '".$wnin."'";
		$res_aut = mysql_query($query_aut, $conex) or die("Error: ".mysql_errno()." - en el query_aut: ".$query_aut." - ".mysql_error());
		$row_aut = mysql_fetch_array($res_aut);			
		$cod_aut = $row_aut['Ingord'];
		
		//                  0       1       2       3       4       5       6       7       8       9      10      11      12      13      14      15
		$query = "select Ubihis, Ubiing, Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2, Pacnac, Pacsex, Ubisac, Cconom, Ingres, Ingnre, Ubialp, Ubiptr ";
		$query .= " from ".$empresa."_000018,root_000036,root_000037,costosyp_000005,".$empresa."_000016 ";
		if(!isset($ctc))
			$query .= " where ubiald = 'off'  ";
		else
			$query .= " where ubiald != ''  ";
		$query .= " and ubihis = '".$whis."' ";
		$query .= " and ubiing = '".$wnin."' ";
		$query .= " and ubihis = orihis  ";
		$query .= " and ubiing = oriing  ";
		$query .= " and  oriori = '".$wemp_pmla."'  ";
		$query .= " and oriced = pacced  ";
		$query .= " and oritid = pactid  ";
		$query .= " and  ubisac = ccocod  ";
		$query .= " and ubihis = inghis ";
		$query .= " and ubiing = inging  ";
		$query .= " order by Ubisac,Pacced ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);

		//echo "<tr><td align=center colspan=6 id=tipo14><b>BITACORA DE PACIENTES</td></tr>";
		$color="#dddddd";
		$color1="#000099";
		$color2="#006600";
		$color3="#cc0000";
		$color4="#CC99FF";
		$color5="#99CCFF";
		$color6="#FF9966";
		$color7="#cccccc";
		?>
		<script>
			function ira(){document.bitacora.wtem.focus();}
		</script>
		<?php
		echo "<tr class='encabezadotabla'><td align=center colspan=7><b>DATOS DEL PACIENTE</b></td></tr>";

		//PRIMERA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center>Historia :<br><input type='TEXT' name='whis' size=9 maxlength=9  readonly='readonly' value='".$whis."' class=tipo3></td>";
		echo "<td bgcolor=".$color." align=center>Nro Ing. :<br><input type='TEXT' name='wnin' size=4 maxlength=4  readonly='readonly' value='".$wnin."' class=tipo3></td>";
		echo "<td bgcolor=".$color." align=center>Tipo doc. :<br>".$row[3]."</td>";
		echo "<td bgcolor=".$color." align=center>Identificacion. :<br>".$row[2]."</td>";
		$nombre=$row[4]." ".$row[5]." ".$row[6]." ".$row[7];
		echo "<td bgcolor=".$color." align=center>Nombre :<br>".$nombre."</td>";
		echo "<td bgcolor=".$color." align=center>F. Nacimiento :<br>".$row[8]."</td>";
		echo "<td bgcolor=".$color." align=center>Código Autorización :<br>".$cod_aut."</td>";
		echo "</tr>";

		//SEGUNDA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." align=center>Sexo :<br>".$row[9]."</td>";
		echo "<td bgcolor=".$color." align=center>Servicio :<br>".$row[10]."</td>";
		echo "<td bgcolor=".$color." align=center>Descripcion :<br>".$row[11]."</td>";
		echo "<td bgcolor=".$color." align=center>Responsable :<br>".$row[12]."</td>";
		echo "<td bgcolor=".$color." align=center>Descripcion :<br>".$row[13]."</td>";
		echo "<td bgcolor=".$color." align=center colspan='2' rowspan='1'>Usuario : <br>".$wusr."</td>";
		echo "<input type='HIDDEN' name= 'wusr' value='".$wusr."'>";
		echo "</tr>";

		//TERCERA LINEA
		echo "<tr><td align=center colspan=7 bgcolor=".$color."><b>Tema : </b>";
		$query = "SELECT Codigo, Descripcion, Usuarios  from  ".$empresa."_000034 where Estado='on' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wtem' id=tipo1>";
		if ($num>0)
		{
			echo "<option>SELECCIONAR</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[2] == "*" or strpos($row[2],$key) !== false)
				{
					$wtem=ver($wtem);
					if($wtem == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
			}
		}
		echo "</select>";
		echo"</td></tr>";

		//CUARTA LINEA
		echo "<tr>";
		echo "<td bgcolor=".$color." valign=center colspan=7 align=center>Registro :<br><textarea name='wreg' cols=100 rows=10 class=tipo3>".$wreg."</textarea></td>";
		echo "</tr>";

		//PARTE CENTRAL DE LA PANTALLA
		$ok=1;
		echo "<td bgcolor=#cccccc colspan=4 align=center><input type='RADIO' name=ok value=1 checked onclick='enter()'><b>PROCESO</b></td><td bgcolor=#cccccc colspan=3 align=center><input type='RADIO' name=ok value=2 onclick='enter()'><b>GRABAR</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=7 align=center><input type='submit' value='OK'></td></tr>";
		if(isset($ctc))
			echo "<tr><td bgcolor=#ffffff colspan=7 align=center><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'></td></tr></table><br><br></center>";
		else
			echo "<tr><td bgcolor=#ffffff colspan=7 align=center><A HREF='/MATRIX/movhos/Procesos/bitacora.php?ok=99&empresa=".$empresa."&wemp_pmla=".$wemp_pmla."'><IMG SRC='/matrix/images/medical/movhos/pac.png' alt='Lista'><br>Lista</A></td></tr></table><br><br></center>";
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
		//*                             DATOS ASOCIADOS A LA BITACORA DEL PACIENTE                               *
		//********************************************************************************************************
		echo "<table border=0 align=center id=tipo2>";
		echo "<tr><td colspan=6 id=tipo19><b>Tema :</b> ";
		$query = "SELECT Codigo, Descripcion  from  ".$empresa."_000034 where Estado='on' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		echo "<select name='wtem1' id=tipo1 OnChange='enter()'>";
		if ($num>0)
		{
			echo "<option>TODOS</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$wtem1=ver($wtem1);
				if($wtem1 == $row[0])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
		}
		echo "</select>";
		echo"</td></tr>";
		$query = "select Bitnum, Bitusr, usuarios.Descripcion, Bittem, ".$empresa."_000034.Descripcion, Bitobs, ".$empresa."_000021.fecha_data, ".$empresa."_000021.Hora_data  ";
		$query .= " from ".$empresa."_000021,usuarios,".$empresa."_000034 ";
		$query .= " where Bithis = '".$whis."' ";
		$query .= "   and Biting = '".$wnin."' ";
		$query .= "   and Bitusr = usuarios.Codigo  ";
		if(isset($wtem1) and $wtem1 != "TODOS")
			$query .= "   and Bittem = '".$wtem1."' ";
		$query .= "   and Bittem = ".$empresa."_000034.Codigo  ";
		$query .= " order by Bitnum desc";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());


		$num = mysql_num_rows($err);
		if ($num>0)
		{

			echo "<tr class='encabezadotabla'><td align='center'>Numero</td><td align='center'>Fecha</td><td align='center'>Hora</td><td align='center'>Usuario</td><td align='center'>Tema</td><td align='center'>Observacion</td></tr>";

			$cols = 80;//Numero de caracteres en horizontal para el textarea

			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
				{
					$tipo="tipo12";
				}
				else
				{
					$tipo="tipo13";
				}
				$row = mysql_fetch_array($err);

				//reemplaza todos los saltos de linea por <br>
				$aux = preg_match("/[\n|\r|\n\r]/", '<br>', $row[5]);

				$cont = substr_count($aux, '<br><br>'); //cuentas cuantos saltos de linea tiene
				$rows = 0;
				if( $cont == 0){ //Si no tiene saltos de linea, Determina si se debe crear una nueva columna si el texto es muy largo (mas del 90% del texto horizontal)
					$rows = ceil(strlen(implode($row[5])) / ($cols*0.9))+1;
				}else{
					$rowsaux = 0;
					$lineas = explode( '<br><br>' , $aux ); //Crea un arreglo donde en cada posicion hay una cadena de texto por cada salto de linea
					foreach( $lineas as $linea ){
						$conta = ceil(strlen($linea) / ($cols*0.9));	//Determina si se debe crear una nueva columna si el texto es muy largo(mas del 90% del texto horizontal)
						if( $conta > 1 ){
							$rowsaux+=$conta;
						}
					}
					$rows = $cont + $rowsaux; //El numero de filas es: numero de saltos de linea + filas extras por texto muy largo
				}
				//el minimo de filas es 3
				if( $rows < 3 ){
					$rows = 3;
				}else{
					$rows++;
				}

			 	echo "<tr id='".$tipo."'><td align='center' >".$row[0]."</td><td align='center'>".$row[6]."</td><td align='center'>".$row[7]."</td><td align='center'>".$row[2]."</td><td align='center'>".$row[4]."</td><td align='center'><textarea name='wobs[".$i."]' cols=".$cols." readonly='readonly' rows=".($rows)." class=tipo3>".($row['Bitobs'])."</textarea></td></tr>";
			}
		}
		echo "</table>";
		echo "<br><br>";
		echo"</form>";
	}
}
?>
</body>
</html>