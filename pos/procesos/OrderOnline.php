<html>
<head>
  	<title>MATRIX Pedidos x Internet</title>
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
    	#tipo1{color:#000066;background:#7CC6D3;font-size:9pt;font-family:Tahoma;font-weight:bold;}
    	#tipo2{color:#000066;background:#7CC6D3;font-size:18pt;font-family:Tahoma;font-weight:bold;}
    	#tipo3{color:#000066;background:#E8E8E8;font-size:8pt;font-family:Tahoma;font-weight:bold;}
    	#tipo4{color:#FF0000;background:#dddddd;font-size:8pt;font-family:Tahoma;font-weight:normal;text-align:left;}
    	#tipo5{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;border-style:inset;border-width:thin;}
    	
    	.tipo6{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	
    	#tipo7{color:#000000;background:#cccccc;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	#tipo8{color:#000066;background:#E8E8E8;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
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
    	
    	#tipoG00{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
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
<body onload=ira() BGCOLOR="#E8E8E8" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.OrderOnline.submit();
	}
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
	function teclado1()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
	}
	function tecladoT()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57 ) & event.keyCode != 40 & event.keyCode != 41 & event.keyCode != 45 & event.keyCode != 46 & event.keyCode != 13)  event.returnValue = false;
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
/*****************************************************************************************************************************  
	   PROGRAMA : OrderOnline.php
	   Fecha de Liberación : 2008-10-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2008-10-01
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite reaalizar pedidos de medicamentos
	   e insumos a un almacen de cadena.
	   
	   El programa exige que el usuario se registre antes de poder realizar un pedido.
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   .2008-10-01
	   		Release de Versión Beta.
	   		
*****************************************************************************************************************************/
function agrupar($j,&$compra)
{
	$w=-1;
	$compra1=array();
	for ($i=0;$i<=$j;$i++)
	{
		if($compra[$i][7] == "off")
		{
			$wfind=-1;
			for ($k1=0;$k1<=$w;$k1++)
				if($compra[$k1][0] == $compra[$i][0])
					$wfind=$k1;
			if($wfind == -1)
			{
				$w=$w+1;
				$wartant = $compra[$i][0];
				$compra1[$w][0]=$compra[$i][0];
				$compra1[$w][1]=$compra[$i][1];
				$compra1[$w][2]=$compra[$i][2];
				$compra1[$w][3]=$compra[$i][3];
				$compra1[$w][4]=$compra[$i][4];
				if($compra[$i][5] <= $compra[$i][2])
					if($compra[$i][5] > 0)
						$compra1[$w][5]=$compra[$i][5];
					else
						$compra1[$w][5]=1;
				else
					$compra1[$w][5]=$compra[$i][2];
				$compra1[$w][6]=$compra[$i][6];
				$compra1[$w][7]=$compra[$i][7];
			}
		}
	}
	$compra=array();
	for ($i=0;$i<=$w;$i++)
	{
		$compra[$i][0]=$compra1[$i][0];
		$compra[$i][1]=$compra1[$i][1];
		$compra[$i][2]=$compra1[$i][2];
		$compra[$i][3]=$compra1[$i][3];
		$compra[$i][4]=$compra1[$i][4];
		$compra[$i][5]=$compra1[$i][5];
		$compra[$i][6]=$compra1[$i][6];
		$compra[$i][7]=$compra1[$i][7];
	}
	return $w;
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
function comparacion1($vec1,$vec2)
{
	if($vec1[6] > $vec2[6])
		return -1;
	elseif ($vec1[6] < $vec2[6])
				return 1;
			else
				return 0;
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
		if($occur[1] < 0 or $occur[1] >23 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}
function validar8($chain)
{
	// Funcion que permite validar la estructura de una direccion de correo electronico
	$regular="^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$";
	return (ereg($regular,$chain));
}
function validar9($chain)
{
	// Funcion que permite validar la estructura de un telefono
	$regular="^([=0-9' '()-])+$";
	return (ereg($regular,$chain));
}

function ING_CLI($key,$conex,$wtel,$wnom,$wdir,$wema,$wpas,&$werr,&$e)
{
	global $empresa;
	$fecha = date("Y-m-d");
	$hora = (string)date("H:i:s");
	$query = "insert ".$empresa."_000085 (medico,fecha_data,hora_data, Mcltel, Mclnom, Mcldir, Mcleme, Mclcla, Mclnpe, Seguridad) values ('";
	$query .=  $empresa."','";
	$query .=  $fecha."','";
	$query .=  $hora."','";
	$query .=  $wtel."','";
	$query .=  $wnom."','";
	$query .=  $wdir."','";
	$query .=  $wema."','";
	$query .=  $wpas."',0,'C-".$empresa."')";
	$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO CLIENTE : ".mysql_errno().":".mysql_error());
	return true;
}

function ING_PED($key,$conex,$wtel,$compra,$kar,&$wnci,$ultimo,&$werr,&$e)
{
	global $empresa;
	if($ultimo == "0")
	{
		$query = "SELECT COUNT(*) from ".$empresa."_000085 where Mcltel='".$wtel."' ";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO HORA");
		$row = mysql_fetch_array($err);
		$ultimo="0";
		if($row[0] != '')
			$ultimo="1";
	}
	$query = "SELECT hora_data from ".$empresa."_000085 where Mcltel='".$wtel."' ";
	$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO HORA");
	$row = mysql_fetch_array($err);
	if($row[0] == "00:00:00" or $ultimo == "0" and $kar >= 0)
	{
		$query = "update  ".$empresa."_000085 set hora_data='".(string)date("H:i:s")."' where Mcltel='".$wtel."' ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO CONSECUTIVO");
		
		$query = "select Concon from ".$empresa."_000088 ";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO");
		$row = mysql_fetch_array($err);
		$wnci=$row[0] + 1;
		$query =  " update ".$empresa."_000088 set Concon = Concon + 1 ";
		$err = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO");
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000086 (medico,fecha_data,hora_data, Mennum, Mentel, Menest, Seguridad) values ('";
		$query .=  $empresa."','";
		$query .=  $fecha."','";
		$query .=  $hora."','";
		$query .=  $wnci."','";
		$query .=  $wtel."',";
		$query .=  "'A','C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ENCABEZADO : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$kar;$i++)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data, Mdenum, Mdeart, Mdecan, Mdepro, Mdeval, Mdeest, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."','";
			$query .=  $wnci."','";
			$query .=  $compra[$i][0]."',";
			$query .=  $compra[$i][5].",";
			$query .=  $compra[$i][3].",";
			$query .=  $compra[$i][4].",";
			$query .=  "'A','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DETALLE : ".mysql_errno().":".mysql_error());
		}
		$e=$e+1;
		$werr[$e]="OK! PEDIDO NUMERO: ".$wnci." GRABADO";
		return true;
	}
	else
		return false;
}

function CON_PED($key,$conex,$wtel,$compra,$kar,&$wnci,$ultimo,$fecha,$hora,&$werr,&$e)
{
	global $empresa;
	if($ultimo == "0")
	{
		$query = "SELECT COUNT(*) from ".$empresa."_000085 where Mcltel='".$wtel."' ";
		$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO HORA");
		$row = mysql_fetch_array($err);
		$ultimo="0";
		if($row[0] != '')
			$ultimo="1";
	}
	$query = "SELECT hora_data from ".$empresa."_000085 where Mcltel='".$wtel."' ";
	$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO HORA");
	$row = mysql_fetch_array($err);
	if($row[0] == "00:00:00" or $ultimo == "0" and $kar >= 0)
	{
		$query = "update  ".$empresa."_000085 set hora_data='".(string)date("H:i:s")."' where Mcltel='".$wtel."' ";
		$err = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO CONSECUTIVO");
		$query = "DELETE  from ".$empresa."_000086 where Mennum='".$wnci."' ";
		$err = mysql_query($query,$conex) or die("ERROR BORRANDO ENCABEZADO DE PEDIDO");
		$query = "DELETE  from ".$empresa."_000087 where Mdenum='".$wnci."' ";
		$err = mysql_query($query,$conex) or die("ERROR BORRANDO DETALLE DE PEDIDO");
		
		//$fecha = date("Y-m-d");
		//$hora = (string)date("H:i:s");
		$query = "insert ".$empresa."_000086 (medico,fecha_data,hora_data, Mennum, Mentel, Menest, Seguridad) values ('";
		$query .=  $empresa."','";
		$query .=  $fecha."','";
		$query .=  $hora."','";
		$query .=  $wnci."','";
		$query .=  $wtel."',";
		$query .=  "'C','C-".$empresa."')";
		$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO ENCABEZADO : ".mysql_errno().":".mysql_error());
		for ($i=0;$i<=$kar;$i++)
		{
			//$fecha = date("Y-m-d");
			//$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000087 (medico,fecha_data,hora_data, Mdenum, Mdeart, Mdecan, Mdepro, Mdeval, Mdeest, Seguridad) values ('";
			$query .=  $empresa."','";
			$query .=  $fecha."','";
			$query .=  $hora."','";
			$query .=  $wnci."','";
			$query .=  $compra[$i][0]."',";
			$query .=  $compra[$i][5].",";
			$query .=  $compra[$i][3].",";
			$query .=  $compra[$i][4].",";
			$query .=  "'C','C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DETALLE : ".mysql_errno().":".mysql_error());
		}
		$e=$e+1;
		$werr[$e]="OK! PEDIDO NUMERO: ".$wnci." CONFIRMADO";
		return true;
	}
	else
		return false;
}
		
//session_start();
if(1 == 2)
	echo "error";
else
{
	$key="farstore";
	if(!isset($ok))
		$ok=98;
	echo "<form name='OrderOnline' action='OrderOnline.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	/*echo "<MAP NAME='BANNER'>";
		echo "<AREA SHAPE=RECT COORDS='677,2,690,14' HREF='http://www.lafarmastore.com/eContent/home.asp' target='_blank'>";
		echo "<AREA SHAPE=RECT COORDS='705,2,723,14' HREF='http://www.lafarmastore.com/eContent/contactus.asp' target='_blank'>";
		echo "<AREA SHAPE=RECT COORDS='738,2,755,14' HREF='http://www.lafarmastore.com/eContent/SiteMap.asp' target='_blank'>";
	echo "</MAP>"; */
	echo "<center>";
	//echo "<A HREF='procesos.map'><IMG SRC='/matrix/images/medical/pos/banner1.png' ISMAP USEMAP='#BANNER'> </A>";
	echo "<table bgcolor=#999999 border=0 align=center id=tipo5 cellpadding=0 cellspacing=0>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td></tr>";
	echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner1.swf' HEIGHT=77 WIDTH=764></td></tr>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/LINEA.png' WIDTH=764></td></tr>";
	switch ($ok)
	{
		case 99:
			echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
			?>
			<script>
				function ira(){document.OrderOnline.wtel.focus();}
			</script>
			<?php
			
			if(isset($wtel) and isset($wnom) and isset($wdir) and isset($wema) and isset($wpas))
			{
				$werr=array();
				$e=-1;
				if(strlen($wtel) <= 5)
				{
					$e=$e+1;
					$werr[$e]="NUMERO DE TELEFONO MUY CORTO DEBE SER MAYOR A 5 CARACTERES";
				}
				if(!validar9($wtel))
				{
					$e=$e+1;
					$werr[$e]="NUMERO DE TELEFONO ESCRITO DE FORMA INADECUADA";
				}
				if(strlen($wnom) <= 10)
				{
					$e=$e+1;
					$werr[$e]="NOMBRE MUY CORTO DEBE SER MAYOR A 10 CARACTERES";
				}
				if(!validar4($wnom))
				{
					$e=$e+1;
					$werr[$e]="NOMBRE ESCRITO DE FORMA INADECUADA";
				}
				if(strlen($wdir) <= 10)
				{
					$e=$e+1;
					$werr[$e]="DIRECCION MUY CORTA DEBE SER MAYOR A 10 CARACTERES";
				}
				if(!validar4($wdir))
				{
					$e=$e+1;
					$werr[$e]="DIRECCION ESCRITA DE FORMA INADECUADA";
				}
				if(!validar8($wema))
				{
					$e=$e+1;
					$werr[$e]="E-MAIL ESCRITO DE FORMA INADECUADA";
				}
				if(strlen($wpas) <= 2)
				{
					$e=$e+1;
					$werr[$e]="CLAVE MUY CORTA DEBE SER MAYOR A 2 CARACTERES";
				}
				if(!validar4($wpas))
				{
					$e=$e+1;
					$werr[$e]="CLAVE ESCRITA DE FORMA INADECUADA";
				}
				$query = "SELECT Mcltel from ".$empresa."_000085 where Mcltel='".$wtel."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($e == -1)
				{
					if($num == 0)
					{
						if(ING_CLI($key,$conex,$wtel,$wnom,$wdir,$wema,$wpas,&$werr,&$e))
						{
							echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 aling=center id=tipo5>";
							//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
							//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner2.swf' HEIGHT=120 WIDTH=700></td></tr>";
							echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/exitoso.png'></td>";
							echo "<tr><td align=center><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=98&empresa=".$empresa."'>IR A INGRESO</td>";
							echo "</table>";
						}
						
					}
					else
					{
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 aling=center id=tipo5>";
						//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
						//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner2.swf' HEIGHT=120 WIDTH=800></td></tr>";
						echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/fallo.png'></td>";
						echo "<tr><td align=center><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=99&empresa=".$empresa."'>IR A REGISTRO</td>";
						echo "</table>";
					}
				}
			}
			if(!isset($wtel))
				$wtel="";
			if(!isset($wnom))
				$wnom="";
			if(!isset($wdir))
				$wdir="";
			if(!isset($wema))
				$wema="";
			if(!isset($wpas))
				$wpas="";
			if(!isset($e) or $e > -1)
			{
				echo "<tr><td align=center bgcolor='#FFFFFF'><EMBED SRC='/matrix/images/medical/pos/banner2.swf' HEIGHT=160 WIDTH=740></td></tr>";
				//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner.png'></td>";
				//echo "</table><br><br><br>";
				echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center cellpadding=0 cellspacing=0>";
				echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/TOP.png' width=100%></td>";
				echo "<tr><td align=center id=tipo1>PEDIDOS EN LINEA Ver. 2008-10-01</td></tr>";
				echo "<tr><td align=center id=tipo2><b>REGISTRO</td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Telefono</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='TEXT' name='wtel' size=40 maxlength=50 value='".$wtel."' class=tipo6 onkeypress='tecladoT()'></td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Nombres y Apellidos</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='TEXT' name='wnom' size=40 maxlength=50 value='".$wnom."' class=tipo6></td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Dirección</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='TEXT' name='wdir' size=40 maxlength=50 value='".$wdir."' class=tipo6></td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Correo Electronico</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='TEXT' name='wema' size=40 maxlength=80 value='".$wema."' class=tipo6></td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Clave de Acceso</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='password' name='wpas' size=8 maxlength=8 value='".$wpas."' class=tipo6></td></tr>";
				echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/BOTTOM.png' width=100%></td></tr>";
				echo "<tr><td align=right id=tipo3><input type=submit value='ENVIAR'></td></tr>";
				echo "<tr><td align=center bgcolor=#E8E8E8><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=98&empresa=".$empresa."'>IR A INGRESO</td></tr>";
				echo "</table></td></tr>";
				if(isset($e) and $e > -1)
				{
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center id=tipo5>";
					for ($i=0;$i<=$e;$i++)
						echo "<tr><td id=tipo4>".$werr[$i]."</td></tr>";
					echo "</table></td></tr>";
				}
			}
		break;
		case 98:
			?>
			<script>
				function ira(){document.OrderOnline.wtel.focus();}
			</script>
			<?php
			echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
			//echo "<table border=0 align=center id=tipo5>";
			if(isset($wtel) and isset($wpas) and !isset($carrito))
			{
				$werr=array();
				$e=-1;
				$query = "SELECT Mcltel, Mclcla  from ".$empresa."_000085 where Mcltel='".$wtel."' and  Mclcla='".$wpas."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num == 0)
				{
					$e=$e+1;
					$werr[$e]="USUARIO NO EXISTE";
				}
			}
			if(!isset($wtel))
				$wtel="";
			if(!isset($wpas))
				$wpas="";
			if((!isset($e) or $e > -1) and !isset($carrito))
			{
				echo "<tr><td align=center bgcolor='#FFFFFF'><EMBED SRC='/matrix/images/medical/pos/banner2.swf' HEIGHT=160 WIDTH=740></td></tr>";
				//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner.png'></td>";
				//echo "<br><br><br>";
				echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center cellpadding=0 cellspacing=0>";
				echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/TOP.png' width=100%></td>";
				echo "<tr><td align=center id=tipo1>PEDIDOS EN LINEA Ver. 2008-10-01</td></tr>";
				echo "<tr><td align=center id=tipo2><b>INGRESO</td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Telefono</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='TEXT' name='wtel' size=40 maxlength=50 value='".$wtel."' class=tipo6></td></tr>";
				echo "<tr><td align=left id=tipo1><b>*Clave de Acceso</td></tr>";
				echo "<tr><td align=left id=tipo1><input type='password' name='wpas' size=8 maxlength=8 value='".$wpas."' class=tipo6></td></tr>";
				echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/BOTTOM.png' width=100%></td></tr>";
				echo "<tr><td align=right id=tipo3><input type=submit value='ENVIAR'></td></tr>";
				echo "<tr><td align=center bgcolor=#E8E8E8><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=99&empresa=".$empresa."'>SI NO SE HA REGISTRADO HAGALO AQUI</td></tr>";
				echo "<tr><td align=right bgcolor=#E8E8E8><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=97&empresa=".$empresa."'><IMG SRC='/matrix/images/medical/pos/mapita.png' alt='COBERTURA DEL SERVICIO'></td></tr>";
				echo "</table></td></tr>";
				if(isset($e) and $e > -1)
				{
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
					for ($i=0;$i<=$e;$i++)
						echo "<tr><td id=tipo4>".$werr[$i]."</td></tr>";
					echo "</table></td></tr>";
				}
				
			}
			else
			{
				if(isset($GT))
				{
					$query = "lock table ".$empresa."_000086 LOW_PRIORITY WRITE, ".$empresa."_000087 LOW_PRIORITY WRITE, ".$empresa."_000088 LOW_PRIORITY WRITE,  ".$empresa."_000085 LOW_PRIORITY WRITE ";
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
					$werr=array();
					$e=-1;
					$wnci=0;
					$color5="#99CCFF";
					if(ING_PED($key,$conex,$wtel,$compra,$kar,&$wnci,$ultimo,&$werr,&$e))
					{
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 aling=center>";
						echo "<tr><td bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>CORRECTO !!!</b></font></TD><TD bgcolor=".$color5."><font size=4 color=#000000 face='tahoma'><b>Pedido Registrado Con El Numero : ".$wnci."</b></font></td></tr>";
						echo "</table></td></tr>";
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center id=tipo5>";
						//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
						//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner.swf' HEIGHT=120 WIDTH=800></td></tr>";
						echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/pedido.png'></td>";
						echo "<tr><td align=center><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=98&empresa=".$empresa."'>IR A INGRESO</td>";
						echo "</table></td></tr>";
					}
					else
					{
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center id=tipo5>";
						//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
						//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner.swf' HEIGHT=120 WIDTH=800></td></tr>";
						echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/no_pedido.png'></td>";
						echo "<tr><td align=center><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=98&empresa=".$empresa."'>IR A INGRESO</td>";
						echo "</table></td></tr>";
					}
					$query = " UNLOCK TABLES";													
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
				}
				else
				{
					if(!isset($kar))
					{
						$query = "lock table ".$empresa."_000085 LOW_PRIORITY WRITE ";
						$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO HORA");
						$query = "SELECT COUNT(*) from ".$empresa."_000085 where Mcltel='".$wtel."' ";
						$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO HORA");
						$num1 = mysql_num_rows($err1);
						$ultimo="0";
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							if($row1[0] != '')
							{
								$ultimo=$row1[0];
								$query = "update  ".$empresa."_000085 set hora_data='00:00:00' where Mcltel='".$wtel."' ";
								$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO HORA");
							}
						}
						$query = " UNLOCK TABLES";													
						$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
					}
					echo "<input type='HIDDEN' name= 'ultimo' value='".$ultimo."'>";
					if(!isset($kar))
					{
						$compra=array();
						$kar=-1;
					}
					if(isset($search))
					{
						for ($i=0;$i<=$num;$i++)
							if(isset($sel[$i]))
							{
								$kar=$kar + 1;
								$compra[$kar][0]=$search[$i][0];
								$compra[$kar][1]=$search[$i][1];
								$compra[$kar][2]=$search[$i][2];
								$compra[$kar][3]=$search[$i][3];
								$compra[$kar][4]=$search[$i][4];
								$compra[$kar][5]=1;
								$compra[$kar][6]=-1;
								if($kar > 0)
								{
									usort($compra,'comparacion1');
									$compra[$kar][6]=$compra[0][6] + 1;
								}
								else
									$compra[$kar][6]=$kar;
							}
					} 
					if($kar > -1)
					{
						for ($i=0;$i<=$kar;$i++)
							if(isset($del[$i]))
								$compra[$i][7]="on";
							else
								$compra[$i][7]="off";
						usort($compra,'comparacion');
						$kar=agrupar($kar,$compra);
						usort($compra,'comparacion1');
					}
					$carrito="on";
					if(isset($letra))
						$palabra=$letra;
					if(!isset($palabra))
						$palabra="";
					else
						$letra=$palabra;
					echo "<input type='HIDDEN' name= 'carrito' value='".$carrito."'>";
					echo "<input type='HIDDEN' name= 'wtel' value='".$wtel."'>";
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
					//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
					//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner.swf' HEIGHT=120 WIDTH=800></td></tr>";
					//echo "</table><br>";
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center cellpadding=0 cellspacing=0>";
					echo "<tr><td align=center id=tipo1>PEDIDOS EN LINEA Ver. 2008-10-01</td></tr>";
					echo "<tr><td align=center id=tipo1>";
					echo "<table border=0 align=center cellpadding=0 cellspacing=0><tr>";
					for ($i=65;$i<=90;$i++)
						echo "<td  align=center  id=tipo1>".chr($i)."<br><input type='RADIO' name=letra value='".chr($i)."' OnClick='enter()'></td>";
					echo "</tr></TABLE></td>";
					echo "<tr><td align=center id=tipo1 valign=center>Buscar Por:<br> <input type='TEXT' name='palabra' size=40 maxlength=40 value='".$palabra."' class=tipo6> <IMG SRC='/matrix/images/medical/pos/LUPA.png' OnClick='enter()'></td></tr>";
					echo "<tr><td align=center id=tipo1 valign=center></td></tr>";
					echo "</table></td></tr>";
					$query = "SELECT Conbod, Contar, Conlin from ".$empresa."_000088 ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if($num > 0 and isset($letra) and strlen($letra) > 0)
					{
						$row = mysql_fetch_array($err);
						$bodega=$row[0];
						$tarifa=$row[1];
						$numero=$row[2];
						
						$query  = "SELECT count(*)  from ".$empresa."_000001,".$empresa."_000007,".$empresa."_000026 ";
						$query .= " where Artnom like '".$letra."%' ";
						$query .= "   and Artcod = Karcod ";
						$query .= "   and Karcco = '".$bodega."' ";
						$query .= "   and Karexi > 0 ";
						$query .= "   and MID(Mtatar,1,LOCATE('-',Mtatar)-1) = '".$tarifa."' ";
						$query .= "   and MID(Mtacco,1,LOCATE('-',Mtacco)-1) = '".$bodega."' ";
						$query .= "   and MID(Mtaart,1,LOCATE('-',Mtaart)-1) = Artcod ";
						$query .= "   Order by  Artnom ";
						$err = mysql_query($query,$conex);
						$row = mysql_fetch_array($err);
						$Totales=$row[0];
						if(!isset($q) or $q != $letra)
						{
							$q=$letra;
							unset($Pagina);
							unset($Inicial);
						}
						if(isset($Pagina) and $Pagina > 0)
						{
							$Paginas=(integer)($Totales / $numero);
							if($Paginas * 10 < $Totales)
								$Paginas++;
							if($Pagina > $Paginas)
								$Pagina=$Paginas;
							if($Pagina == 0)
								$Pagina++;
							$Inicial=($Pagina - 1 ) * $numero;
							$Final= $Inicial + $numero;
						}
						else
						{
							if (!isset($Inicial))
							{
								$Inicial=0;
								$Final=$numero;
							}
							else
								if(isset($Direction))
								{
									if($Direction == "1")
									{
										if($Final < $Totales)
										{
											$Inicial = $Final;
											$Final=$Final+$numero;
										}
									}
									else
									{
										if($Inicial >= $numero)
										{
											$Final = $Inicial;
											$Inicial=$Inicial-$numero;
										}
									}
								}
								$Pagina=$Inicial / $numero + 1;
						}
						echo "<input type='HIDDEN' name= 'q' value='".$q."'>";
						echo "<input type='HIDDEN' name= 'Inicial' value='".$Inicial."'>";
						echo "<input type='HIDDEN' name= 'Final' value='".$Final."'>";	
						echo "<input type='HIDDEN' name= 'Totales' value='".$Totales."'>";
						//                  0        1      2       3        4      5       6
						$query  = "SELECT Artcod, Artnom, Karexi, Karpro, Mtavan, Mtafec, Mtavac  from ".$empresa."_000001,".$empresa."_000007,".$empresa."_000026 ";
						$query .= " where Artnom like '".$letra."%' ";
						$query .= "   and Artcod = Karcod ";
						$query .= "   and Karcco = '".$bodega."' ";
						$query .= "   and Karexi > 0 ";
						$query .= "   and MID(Mtatar,1,LOCATE('-',Mtatar)-1) = '".$tarifa."' ";
						$query .= "   and MID(Mtacco,1,LOCATE('-',Mtacco)-1) = '".$bodega."' ";
						$query .= "   and MID(Mtaart,1,LOCATE('-',Mtaart)-1) = Artcod ";
						$query .= "   Order by  Artnom ";
						$query = $query." limit ".$Inicial.",".$numero;
						$query=stripslashes($query);
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						$Paginas=(integer)($Totales / $numero);
						if($Paginas * $numero < $Totales)
							$Paginas++;
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
						echo "<tr><td align=center id=tipo3>Pagina Nro :<b> ".$Pagina."</b>&nbsp &nbspDe : <b>".$Paginas."</b>&nbsp &nbsp <b>Vaya a la Pagina Nro :</b> <input type='TEXT' name='Pagina' size=10 maxlength=10 value=0> [Siguiente]<input type='RADIO' name=Direction value='1'> [Anterior]<input type='RADIO' name=Direction value='0'> <input type=submit value='ENVIAR'></td></tr>";
						echo "</table></td></tr>";
						if($num > 0)
						{
							echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
							echo "<tr><td align=center  id=tipo7 colspan=4>RESULTADO DE LA BUSQUEDA</td></tr>";
							//echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>SELECCIONAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CODIGO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td></tr>";
							echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>SELECCIONAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td></tr>";
							$search=array();
							for ($i=0;$i<$num;$i++)
							{
								if($i % 2 == 0)
									$color="#dddddd";
								else
									$color="#FFFFFF";
								$row = mysql_fetch_array($err);
								$search[$i][0]=$row[0];
								$search[$i][1]=$row[1];
								$search[$i][2]=$row[2];
								$search[$i][3]=$row[3];
								if($row[5] >= date("Y-m-d"))
									$search[$i][4]=$row[4];
								else
									$search[$i][4]=$row[6];
								echo "<tr>";
								echo "<td bgcolor=".$color." align=center><input type='checkbox' name='sel[".$i."]' OnClick='enter()'></td>";
								//echo "<td bgcolor=".$color." align=center>".$search[$i][0]."</td>";
								echo "<td bgcolor=".$color." align=left>".$search[$i][1]."</td>";
								echo "<td bgcolor=".$color." align=right>".number_format((double)$search[$i][2],0,'.',',')."</td>";
								echo "<td bgcolor=".$color." align=right>$".number_format((double)$search[$i][4],0,'.',',')."</td>";
								echo "</tr>";
								echo "<input type='HIDDEN' name= 'search[".$i."][0]' value='".$search[$i][0]."'>";
								echo "<input type='HIDDEN' name= 'search[".$i."][1]' value='".$search[$i][1]."'>";
								echo "<input type='HIDDEN' name= 'search[".$i."][2]' value=".$search[$i][2].">";
								echo "<input type='HIDDEN' name= 'search[".$i."][3]' value=".$search[$i][3].">";
								echo "<input type='HIDDEN' name= 'search[".$i."][4]' value=".$search[$i][4].">";
							}
							echo "<input type='HIDDEN' name= 'num' value=".$num.">";
							echo "<td id=tipo7 align=center colspan=4>".number_format((double)$Totales,0,'.',',')." ARTICULOS COMIENZAN POR: ".$letra."  </td></tr>";
							echo "<td id=tipo7 align=center colspan=4> SELECCIONE [Siguiente] O [Anterior]  Y PRESIONE EL BOTON DE [ENVIAR] PARA CONOCERLOS</td></tr>";
							echo "</table></td></tr>";
						}
					}
					if($kar > -1)
					{
						$totgen=0;
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
						echo "<tr><td align=center id=tipo7 colspan=5>ARTICULOS SELECCIONADOS</td><td align=center id=tipo7><IMG SRC='/matrix/images/medical/pos/carrito.png'></td></tr>";
						//echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>ELIMINAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CODIGO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CANTIDAD</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>TOTAL</b></font></td></tr>";
						echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>ELIMINAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CANTIDAD</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>TOTAL</b></font></td></tr>";
						for ($i=0;$i<=$kar;$i++)
						{
							if($i % 2 == 0)
								$color="#dddddd";
							else
								$color="#ffffff";
							$row = mysql_fetch_array($err);
							echo "<tr>";
							echo "<td bgcolor=".$color." align=center><input type='checkbox' name='del[".$i."]' OnClick='enter()'></td>";
							//echo "<td bgcolor=".$color." align=center>".$compra[$i][0]."</td>";
							echo "<td bgcolor=".$color." align=left>".$compra[$i][1]."</td>";
							echo "<td bgcolor=".$color." align=right>".number_format((double)$compra[$i][2],0,'.',',')."</td>";
							echo "<td bgcolor=".$color." align=right><input type='TEXT' name='compra[".$i."][5]' size=10 maxlength=10 value='".$compra[$i][5]."' class=tipo6 onkeypress='teclado1()'></td>";
							echo "<td bgcolor=".$color." align=right>$".number_format((double)$compra[$i][4],0,'.',',')."</td>";
							$wtotal=$compra[$i][5] * $compra[$i][4];
							$totgen += $wtotal;
							echo "<td bgcolor=".$color." align=right>$".number_format((double)$wtotal,0,'.',',')."</td>";
							echo "</tr>";
							echo "<input type='HIDDEN' name= 'compra[".$i."][0]' value='".$compra[$i][0]."'>";
							echo "<input type='HIDDEN' name= 'compra[".$i."][1]' value='".$compra[$i][1]."'>";
							echo "<input type='HIDDEN' name= 'compra[".$i."][2]' value=".$compra[$i][2].">";
							echo "<input type='HIDDEN' name= 'compra[".$i."][3]' value=".$compra[$i][3].">";
							echo "<input type='HIDDEN' name= 'compra[".$i."][4]' value=".$compra[$i][4].">";
							echo "<input type='HIDDEN' name= 'compra[".$i."][6]' value=".$compra[$i][6].">";
						}
						echo "<td id=tipo7 align=center colspan=5>TOTAL GENERAL: </td><td id=tipo7 align=right>$".number_format((double)$totgen,0,'.',',')."</td></tr>";
						echo "<td id=tipo8 align=center colspan=6>GRABAR EL PEDIDO: <input type='checkbox' name='GT' OnClick='enter()'> <IMG SRC='/matrix/images/medical/pos/dinero.png'></td></tr>";
						echo "</table></td></tr>";
					}
					echo "<input type='HIDDEN' name= 'kar' value=".$kar.">";
				}
			}
		break;
		case 97:
			echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center id=tipo5>";
			echo "<tr><td align=center id=tipo8 ><A HREF='/MATRIX/pos/Procesos/OrderOnline.php?ok=98&empresa=".$empresa."'>IR A INGRESO</td>";
			echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/mapa.png'></td>";
			echo "</table>";
		break;
		case 96:
			echo "<input type='HIDDEN' name= 'ok' value='".$ok."'>";
			if(isset($GT))
			{
				$query = "lock table ".$empresa."_000086 LOW_PRIORITY WRITE, ".$empresa."_000087 LOW_PRIORITY WRITE, ".$empresa."_000088 LOW_PRIORITY WRITE,  ".$empresa."_000085 LOW_PRIORITY WRITE ";
				$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO");
				$werr=array();
				$e=-1;
				$wnci=$wnped;
				$color5="#99CCFF";
				if(CON_PED($key,$conex,$wtel,$compra,$kar,&$wnci,$ultimo,$wfec,$whor,&$werr,&$e))
				{
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 aling=center>";
					echo "<tr><td bgcolor=".$color5."><IMG SRC='/matrix/images/medical/root/feliz.ico'>&nbsp&nbsp<font color=#000000 face='tahoma'><b>CORRECTO !!!</b></font></TD><TD bgcolor=".$color5."><font size=4 color=#000000 face='tahoma'><b>Pedido Confirmado Con El Numero : ".$wnci."</b></font></td></tr>";
					echo "</table></td></tr>";
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center id=tipo5>";
					//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
					//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner.swf' HEIGHT=120 WIDTH=800></td></tr>";
					echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/Confirmado.png'></td>";
					echo "</table></td></tr>";
				}
				else
				{
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center id=tipo5>";
					//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
					//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner.swf' HEIGHT=120 WIDTH=800></td></tr>";
					echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/no_Confirmado.png'></td>";
					echo "</table></td></tr>";
				}
				$query = " UNLOCK TABLES";													
				$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
			}
			else
			{
				if(!isset($kar))
				{
					$query = "lock table ".$empresa."_000085 LOW_PRIORITY WRITE ";
					$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO HORA");
					$query = "SELECT COUNT(*) from ".$empresa."_000085 where Mcltel='".$wtel."' ";
					$err1 = mysql_query($query,$conex) or die("ERROR CONSULTANDO HORA");
					$num1 = mysql_num_rows($err1);
					$ultimo="0";
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						if($row1[0] != '')
						{
							$ultimo=$row1[0];
							$query = "update  ".$empresa."_000085 set hora_data='00:00:00' where Mcltel='".$wtel."' ";
							$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO HORA");
						}
					}
					$query = " UNLOCK TABLES";													
					$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO ENCABEZADO Y CONSECUTIVO");
				}
				echo "<input type='HIDDEN' name= 'ultimo' value='".$ultimo."'>";
				if(!isset($kar))
				{
					$compra=array();
					$kar=-1;
				}
				if(isset($wnped) and $kar == -1)
				{
					$query = "SELECT Conbod from ".$empresa."_000088 ";
					$err = mysql_query($query,$conex);
					$row = mysql_fetch_array($err);
					$bodega=$row[0];
					//                  0        1      2       3       4       5       6
					$query  = "SELECT Mentel, Mdeart, Mdecan, Mdepro, Mdeval, Artnom, Karexi from ".$empresa."_000086,".$empresa."_000087,".$empresa."_000001,".$empresa."_000007 ";
					$query .= " where Mennum = '".$wnped."' ";
					$query .= "   and Mennum = Mdenum ";
					$query .= "   and Mdeart = Artcod ";
					$query .= "   and Artcod = Karcod ";
					$query .= "   and Karcco = '".$bodega."' ";
					//$query .= "   and Karexi > 0 ";
					$query .= "   Order by  ".$empresa."_000087.id ";
					$err = mysql_query($query,$conex) or die("ERROR CONSULTANDO PEDIDO ANTERIOR");
					$num = mysql_num_rows($err);
					if($num > 0)
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$kar=$kar + 1;
							$wtel=$row[0];
							$compra[$kar][0]=$row[1];
							$compra[$kar][1]=$row[5];
							$compra[$kar][2]=$row[6];
							$compra[$kar][3]=$row[3];
							$compra[$kar][4]=$row[4];
							$compra[$kar][5]=$row[2];
							$compra[$kar][6]=$kar;
						}
				}
				if(isset($search))
				{
					for ($i=0;$i<=$num;$i++)
						if(isset($sel[$i]))
						{
							$kar=$kar + 1;
							$compra[$kar][0]=$search[$i][0];
							$compra[$kar][1]=$search[$i][1];
							$compra[$kar][2]=$search[$i][2];
							$compra[$kar][3]=$search[$i][3];
							$compra[$kar][4]=$search[$i][4];
							$compra[$kar][5]=1;
							$compra[$kar][6]=-1;
							if($kar > 0)
							{
								usort($compra,'comparacion1');
								$compra[$kar][6]=$compra[0][6] + 1;
							}
							else
								$compra[$kar][6]=$kar;
						}
				} 
				if($kar > -1)
				{
					for ($i=0;$i<=$kar;$i++)
						if(isset($del[$i]))
							$compra[$i][7]="on";
						else
							$compra[$i][7]="off";
					usort($compra,'comparacion');
					$kar=agrupar($kar,$compra);
					usort($compra,'comparacion1');
				}
				$carrito="on";
				if(isset($letra))
					$palabra=$letra;
				if(!isset($palabra))
					$palabra="";
				else
					$letra=$palabra;
				echo "<input type='HIDDEN' name= 'carrito' value='".$carrito."'>";
				echo "<input type='HIDDEN' name= 'wtel' value='".$wtel."'>";
				echo "<input type='HIDDEN' name= 'wnped' value='".$wnped."'>";
				echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
				echo "<input type='HIDDEN' name= 'whor' value='".$whor."'>";
				echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
				//echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/banner1.png'></td>";
				//echo "<tr><td align=center><EMBED SRC='/matrix/images/medical/pos/banner.swf' HEIGHT=120 WIDTH=800></td></tr>";
				//echo "</table><br>";
				echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center cellpadding=0 cellspacing=0>";
				echo "<tr><td align=center id=tipo1>PEDIDOS EN LINEA Ver. 2008-10-01</td></tr>";
				echo "<tr><td align=center id=tipo1>";
				echo "<table border=0 align=center cellpadding=0 cellspacing=0><tr>";
				for ($i=65;$i<=90;$i++)
					echo "<td  align=center  id=tipo1>".chr($i)."<br><input type='RADIO' name=letra value='".chr($i)."' OnClick='enter()'></td>";
				echo "</tr></TABLE></td>";
				echo "<tr><td align=center id=tipo1 valign=center>Buscar Por:<br> <input type='TEXT' name='palabra' size=40 maxlength=40 value='".$palabra."' class=tipo6> <IMG SRC='/matrix/images/medical/pos/LUPA.png' OnClick='enter()'></td></tr>";
				echo "<tr><td align=center id=tipo1 valign=center></td></tr>";
				echo "</table></td></tr>";
				$query = "SELECT Conbod, Contar, Conlin from ".$empresa."_000088 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0 and isset($letra) and strlen($letra) > 0)
				{
					$row = mysql_fetch_array($err);
					$bodega=$row[0];
					$tarifa=$row[1];
					$numero=$row[2];
					
					$query  = "SELECT count(*)  from ".$empresa."_000001,".$empresa."_000007,".$empresa."_000026 ";
					$query .= " where Artnom like '".$letra."%' ";
					$query .= "   and Artcod = Karcod ";
					$query .= "   and Karcco = '".$bodega."' ";
					$query .= "   and Karexi > 0 ";
					$query .= "   and MID(Mtatar,1,LOCATE('-',Mtatar)-1) = '".$tarifa."' ";
					$query .= "   and MID(Mtacco,1,LOCATE('-',Mtacco)-1) = '".$bodega."' ";
					$query .= "   and MID(Mtaart,1,LOCATE('-',Mtaart)-1) = Artcod ";
					$query .= "   Order by  Artnom ";
					$err = mysql_query($query,$conex);
					$row = mysql_fetch_array($err);
					$Totales=$row[0];
					if(!isset($q) or $q != $letra)
					{
						$q=$letra;
						unset($Pagina);
						unset($Inicial);
					}
					if(isset($Pagina) and $Pagina > 0)
					{
						$Paginas=(integer)($Totales / $numero);
						if($Paginas * 10 < $Totales)
							$Paginas++;
						if($Pagina > $Paginas)
							$Pagina=$Paginas;
						if($Pagina == 0)
							$Pagina++;
						$Inicial=($Pagina - 1 ) * $numero;
						$Final= $Inicial + $numero;
					}
					else
					{
						if (!isset($Inicial))
						{
							$Inicial=0;
							$Final=$numero;
						}
						else
							if(isset($Direction))
							{
								if($Direction == "1")
								{
									if($Final < $Totales)
									{
										$Inicial = $Final;
										$Final=$Final+$numero;
									}
								}
								else
								{
									if($Inicial >= $numero)
									{
										$Final = $Inicial;
										$Inicial=$Inicial-$numero;
									}
								}
							}
							$Pagina=$Inicial / $numero + 1;
					}
					echo "<input type='HIDDEN' name= 'q' value='".$q."'>";
					echo "<input type='HIDDEN' name= 'Inicial' value='".$Inicial."'>";
					echo "<input type='HIDDEN' name= 'Final' value='".$Final."'>";	
					echo "<input type='HIDDEN' name= 'Totales' value='".$Totales."'>";
					//                  0        1      2       3        4      5       6
					$query  = "SELECT Artcod, Artnom, Karexi, Karpro, Mtavan, Mtafec, Mtavac  from ".$empresa."_000001,".$empresa."_000007,".$empresa."_000026 ";
					$query .= " where Artnom like '".$letra."%' ";
					$query .= "   and Artcod = Karcod ";
					$query .= "   and Karcco = '".$bodega."' ";
					$query .= "   and Karexi > 0 ";
					$query .= "   and MID(Mtatar,1,LOCATE('-',Mtatar)-1) = '".$tarifa."' ";
					$query .= "   and MID(Mtacco,1,LOCATE('-',Mtacco)-1) = '".$bodega."' ";
					$query .= "   and MID(Mtaart,1,LOCATE('-',Mtaart)-1) = Artcod ";
					$query .= "   Order by  Artnom ";
					$query = $query." limit ".$Inicial.",".$numero;
					$query=stripslashes($query);
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$Paginas=(integer)($Totales / $numero);
					if($Paginas * $numero < $Totales)
						$Paginas++;
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
					echo "<tr><td align=center id=tipo3>Pagina Nro :<b> ".$Pagina."</b>&nbsp &nbspDe : <b>".$Paginas."</b>&nbsp &nbsp <b>Vaya a la Pagina Nro :</b> <input type='TEXT' name='Pagina' size=10 maxlength=10 value=0> [Siguiente]<input type='RADIO' name=Direction value='1'> [Anterior]<input type='RADIO' name=Direction value='0'> <input type=submit value='ENVIAR'></td></tr>";
					echo "</table></td></tr>";
					if($num > 0)
					{
						echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
						echo "<tr><td align=center  id=tipo7 colspan=4>RESULTADO DE LA BUSQUEDA</td></tr>";
						//echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>SELECCIONAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CODIGO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td></tr>";
						echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>SELECCIONAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td></tr>";
						$search=array();
						for ($i=0;$i<$num;$i++)
						{
							if($i % 2 == 0)
								$color="#dddddd";
							else
								$color="#FFFFFF";
							$row = mysql_fetch_array($err);
							$search[$i][0]=$row[0];
							$search[$i][1]=$row[1];
							$search[$i][2]=$row[2];
							$search[$i][3]=$row[3];
							if($row[5] >= date("Y-m-d"))
								$search[$i][4]=$row[4];
							else
								$search[$i][4]=$row[6];
							echo "<tr>";
							echo "<td bgcolor=".$color." align=center><input type='checkbox' name='sel[".$i."]' OnClick='enter()'></td>";
							//echo "<td bgcolor=".$color." align=center>".$search[$i][0]."</td>";
							echo "<td bgcolor=".$color." align=left>".$search[$i][1]."</td>";
							echo "<td bgcolor=".$color." align=right>".number_format((double)$search[$i][2],0,'.',',')."</td>";
							echo "<td bgcolor=".$color." align=right>$".number_format((double)$search[$i][4],0,'.',',')."</td>";
							echo "</tr>";
							echo "<input type='HIDDEN' name= 'search[".$i."][0]' value='".$search[$i][0]."'>";
							echo "<input type='HIDDEN' name= 'search[".$i."][1]' value='".$search[$i][1]."'>";
							echo "<input type='HIDDEN' name= 'search[".$i."][2]' value=".$search[$i][2].">";
							echo "<input type='HIDDEN' name= 'search[".$i."][3]' value=".$search[$i][3].">";
							echo "<input type='HIDDEN' name= 'search[".$i."][4]' value=".$search[$i][4].">";
						}
						echo "<input type='HIDDEN' name= 'num' value=".$num.">";
						echo "<td id=tipo7 align=center colspan=4>".number_format((double)$Totales,0,'.',',')." ARTICULOS COMIENZAN POR: ".$letra."  </td></tr>";
						echo "<td id=tipo7 align=center colspan=4> SELECCIONE [Siguiente] O [Anterior]  Y PRESIONE EL BOTON DE [ENVIAR] PARA CONOCERLOS</td></tr>";
						echo "</table></td></tr>";
					}
				}
				if($kar > -1)
				{
					$totgen=0;
					echo "<tr><td align=center bgcolor='#E8E8E8'><table border=0 align=center>";
					echo "<tr><td align=center id=tipo7 colspan=5>ARTICULOS SELECCIONADOS</td><td align=center id=tipo7><IMG SRC='/matrix/images/medical/pos/carrito.png'></td></tr>";
					//echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>ELIMINAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CODIGO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CANTIDAD</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>TOTAL</b></font></td></tr>";
					echo "<tr><td align=center bgcolor=#7CC6D3><font color=#000066>ELIMINAR</font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DESCRIPCION</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>DISPONIBLE</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>CANTIDAD</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>UNITARIO</b></font></td><td align=center bgcolor=#7CC6D3><font color=#000066>VALOR<BR>TOTAL</b></font></td></tr>";
					for ($i=0;$i<=$kar;$i++)
					{
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#ffffff";
						$row = mysql_fetch_array($err);
						echo "<tr>";
						echo "<td bgcolor=".$color." align=center><input type='checkbox' name='del[".$i."]' OnClick='enter()'></td>";
						//echo "<td bgcolor=".$color." align=center>".$compra[$i][0]."</td>";
						echo "<td bgcolor=".$color." align=left>".$compra[$i][1]."</td>";
						echo "<td bgcolor=".$color." align=right>".number_format((double)$compra[$i][2],0,'.',',')."</td>";
						echo "<td bgcolor=".$color." align=right><input type='TEXT' name='compra[".$i."][5]' size=10 maxlength=10 value='".$compra[$i][5]."' class=tipo6 onkeypress='teclado1()'></td>";
						echo "<td bgcolor=".$color." align=right>$".number_format((double)$compra[$i][4],0,'.',',')."</td>";
						$wtotal=$compra[$i][5] * $compra[$i][4];
						$totgen += $wtotal;
						echo "<td bgcolor=".$color." align=right>$".number_format((double)$wtotal,0,'.',',')."</td>";
						echo "</tr>";
						echo "<input type='HIDDEN' name= 'compra[".$i."][0]' value='".$compra[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'compra[".$i."][1]' value='".$compra[$i][1]."'>";
						echo "<input type='HIDDEN' name= 'compra[".$i."][2]' value=".$compra[$i][2].">";
						echo "<input type='HIDDEN' name= 'compra[".$i."][3]' value=".$compra[$i][3].">";
						echo "<input type='HIDDEN' name= 'compra[".$i."][4]' value=".$compra[$i][4].">";
						echo "<input type='HIDDEN' name= 'compra[".$i."][6]' value=".$compra[$i][6].">";
					}
					echo "<td id=tipo7 align=center colspan=5>TOTAL GENERAL: </td><td id=tipo7 align=right>$".number_format((double)$totgen,0,'.',',')."</td></tr>";
					echo "<td id=tipo8 align=center colspan=6>CONFIRMAR EL PEDIDO: <input type='checkbox' name='GT' OnClick='enter()'> <IMG SRC='/matrix/images/medical/pos/dinero.png'></td></tr>";
					echo "</table></td></tr>";
				}
				echo "<input type='HIDDEN' name= 'kar' value=".$kar.">";
			}
		break;
	}
	echo "<tr><td  bgcolor='#E8E8E8' align=center> &nbsp </td></tr>";
	echo "<tr><td align=center><IMG SRC='/matrix/images/medical/pos/ultimo.png'></td></tr>";
	echo "<tr><td bgcolor='#E8E8E8' align=center><INPUT type=button value='Cerrar Ventana' OnClick='javascript:window.close();'</td></tr>";
	echo "</table>";
}
?>
</body>
</html>