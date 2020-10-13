<?php error_reporting(E_ERROR | E_PARSE); ?>
<html>
<head>
<title>MATRIX Programa de Generacion de RIPS</title>
<!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
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
    	.tipo3{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo4{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo5{color:#000066;background:#99CCFF;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.tipo6{color:#000066;background:#dddddd;font-size:7pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    </style>


</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return true">

<BODY TEXT="#000066">
<script type="text/javascript">
<!--

	function calendario(id)
	{
		if (id == "1")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfec1",button:"trigger1",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
		if (id == "2")
		{
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:"12",electric:false,inputField:"wfec2",button:"trigger2",ifFormat:"%Y-%m-%d",daFormat:"%Y/%m/%d"});
		}
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

	function ajaxquery(fila,empresa)
	{
		var x1,x2,x3,x4,x5

		for (i=0;i<document.forms.rips.ti.length;i++)
		{
			if (document.forms.rips.ti[i].checked==true)
			{
				x4=document.forms.rips.ti[i].value;
				break;
			}
		}

		x1 = document.getElementById("wenv").value;
		x2 = document.getElementById("wfec1").value;
		x3 = document.getElementById("wfec2").value;
		x6 = document.getElementById("wffa").value;
		x7 = document.getElementById("wfac").value;
		s= document.forms.rips.wemp;
		x5 = s.options[s.selectedIndex].value;



		ajax=nuevoAjax();
		st="rips.php?empresa="+empresa+"&wfec1="+x2+"&wfec2="+x3+"&wenv="+x1+"&ti="+x4+"&wemp="+x5+"&wffa="+x6+"&wfac="+x7;

		//alert(st); Se muestra la consulta Mavila :)
		ajax.open("GET", st, true);
		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4 && ajax.status==200)
			{
				document.getElementById(+fila).innerHTML=ajax.responseText;
			}
		}
		ajax.send(null);
	}
//-->
</script>
<?php
include_once("conex.php");
echo "<div id='1'>";
/**********************************************************************************************************************
	   PROGRAMA : rips.php
	   Fecha de Liberación : 2006-04-08
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2008-04-11

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite generar el registro individual
	   de prestacion de servicios que debe acompañar la informacion de facturacion de una IPS.

	   Se puede ejecutar bajo dos modalidades:
	   	. modalidad 0 : Genera los Rips de las facturas asociadas a un envio.
	   	. Modalidad 1 : Genera los Rips de particulares entre dos fechas especificadas.


	   REGISTRO DE MODIFICACIONES :
	   .2019-09-16: Camilo Zapata
	   		Se modifica el programa para que se pueda generar los rips de una factura, en caso de ser necesario buscar wffa, wfac,
	   		fuente de la factura y factura respectivamente
	   .2008-04-11
	   		Se modifico el programa para que en la construccion del archivo AT no se accesara la tabla de 106 con codigos CUPS.

	   .2008-03-27
	   		Se modifico el programa para escribir en los archivos (AT Otros Servicios) (AC Consulta) (AP Procedimientos)
	   		el codigo cups en lugar del codigo interno.

	   .2008-02-01
	   		Se corrigio subindice en generacion de archivo AC.

	   .2008-01-31
	   		Se modifico el programa para incluir en el archivo AC los campos del archivo 108 de Causa externa "Egrcex" y
	   		Tipo de DX Ppal "Egrtdp" que estaban fijos en 13 y 1 respectivamente.

	   .2007-06-25
	   		Se modifico el programa para limitar el tamaño del nombre de la entidad administradora a 30 caracteres como
	   		maximo en el archivo AF.

	   .2007-05-16
	   		Se modifico el programa para poder generar RIPS de empresas entre fechas seleccionando una empresa en especial
	   		o todas.
	   .2007-03-28
	   		Se modifica el programa para que la generacion del archivo de consulta AC no genere errores debidos al query,
	   		ya que el numero de factura no lo trae en la posicion 0 sino en la posicion 16.

	   .2007-02-05
	   		Se cambia en el programa para incluir el archivo nro 66 de relacion entre facturas y cargos para validar los
	   		cargos que pertenecen a una factura y si estan activo o no.

	   .2006-12-15
	   		Se cambia en el programa los tipos de de los campos de historia y nro de ingreso de integer a varchar.

	   .2006-11-09
	   		Se modifica el programa para grabar en la tabla 118 de movimiento de Rips solamente un registro x tipo de Rips.
	   		Se anula los envios anteriores si el envio es diferente de cero.
	   		Si la generacion presente errores en los Rips se anulan los registros de esa remision en la tabla 118.

	   .2006-10-25
	   		Se modifica el programa arreglar el tipo de documento de la tabla 49 y grabar el indicador.

	   .2006-10-23
	   		Se modifica el programa para que asocie la tarifa correspondiente a la empresa seleccionada o "01"
	   		si es particular.

	   .2006-04-08
	   		Release de Versión Beta.

***********************************************************************************************************************/
function c_fecha($wfecha)
{
	$c_fecha=substr($wfecha,8,2)."/".substr($wfecha,5,2)."/".substr($wfecha,0,4);
	return $c_fecha;
}

function bi($d,$n,$k,$i)
{
	$li=0;
	$ls=$n;
	while ($ls - $li > 1)
	{
		$lm=(integer)(($li + $ls) / 2);
		if(strtoupper($k) == strtoupper($d[$lm][$i][0]))
			return $lm;
		elseif(strtoupper($k) < strtoupper($d[$lm][$i][0]))
					$ls=$lm;
				else
					$li=$lm;
	}
	if(strtoupper($k) == strtoupper($d[$li][$i][0]))
		return $li;
	elseif(strtoupper($k) == strtoupper($d[$ls][$i][0]))
				return $ls;
			else
				return -1;
}
function anular($conex,$envio,$tipo,$remi,$est)
{
	global $empresa;
	if($envio != 0)
	{
		if($est == 0)
		{
			$query =  " update ".$empresa."_000118 set Mriest = 'off'  where Mrienv = '".$envio."' AND Mritip = '".$tipo."' AND Mrirem != '".$remi."'";
			$err = mysql_query($query,$conex)or die("ERROR ACTUALIZANDO MOVIMIENTO DE RIPS  : ".mysql_errno().":".mysql_error());
		}
		else
		{
			$query =  " update ".$empresa."_000118 set Mriest = 'off'  where Mrirem = '".$remi."'";
			$err = mysql_query($query,$conex)or die("ERROR ACTUALIZANDO MOVIMIENTO DE RIPS  : ".mysql_errno().":".mysql_error());
		}
	}
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='rips' action='rips.php' method=post>";




	if(!isset($wfec1))
		$wfec1=date("Y-m-d");
	if(!isset($wfec2))
		$wfec2=date("Y-m-d");
	if(!isset($wenv))
		$wenv=0;
	if(!isset( $wfac ) )
		$wfac = "";
	if(!isset( $wffa ) )
		$wffa = "";
	if(!isset($ti) or !isset($wenv) or ($wenv == 0 and $ti == 0 and $wfac == "" ) )
	{
		echo "<center> ";
		//Se organiza la informacion de la tabla :)
		echo "<table border=0 id=tipo2> ";
		echo "<tr>";
			echo "<td align=center colspan=2>";
				echo "<b>PROMOTORA MEDICA LAS AMERICAS S.A.<b>";
			echo "</td>";
		echo "</tr> ";
		echo "<tr>";
		echo "<td align=center colspan=2>PROGRAMA DE GENERACION DE RIPS Ver. 2019-09-16</td>";
		echo "</tr> ";
		echo "<tr>";
		echo "<td align=center colspan=2>";
			echo "<br>* Si desea buscar por factura, recuerde la fuente y el prefijo de la factura, ejemplo: fuente: 20 factura: CS-99999 *<br>";
		echo "</td>";
		echo "</tr> ";
		echo "<tr>";
		echo "<td bgcolor=#999999 align=center colspan=2>";
			echo "<input type='RADIO' name=ti value=0 checked><b>X ENVIO</b>";
			echo "<input type='RADIO' name=ti value=1><b>PARTICULARES</b>";
			echo "<input type='RADIO' name=ti value=2><b>EMPRESAS</b>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Nro. Envio</td>";
			echo "<td bgcolor=#cccccc align=center>";
				echo "<input type='TEXT' name='wenv' id='wenv' value=".$wenv." size=10 maxlength=10 class=tipo3>";
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Factura</td>";
			echo "<td bgcolor=#cccccc align=center>";
				echo "Fuente: <input type='TEXT' name='wffa' id='wffa' value='".$wffa."' size=10 maxlength=3 class=tipo3> ";
				echo " Factura: <input type='TEXT' name='wfac' id='wfac' value='".$wfac."' size=10 maxlength=10 class=tipo3>";
			echo "</td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>Empresa</td>";
			echo "<td bgcolor=#cccccc align=center>";
			echo "<select name='wemp' id=tipo1>";
				$query = "SELECT Empcod, Empnom  from ".$empresa."_000024, ".$empresa."_000029 where mid(Emptem,1,2) = Temcod and Temche = 'off'  order by Empnom";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0){
					echo "<option value='TODAS'>TODAS</option>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_row($err);
						//Se ordena campo de EPS en orden alfabetico :)
						echo "<option value='".$row[0]."-".$row[1]."'> ".$row[1]." - ".$row[0]." </option>";
					}
				}
				else{
					echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
				}
			echo "</select>";
			echo "</td>";
		echo "</tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial de Proceso</td> ";
		$cal="calendario('1')";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec1' id='wfec1' readonly='readonly'  value=".$wfec1." size=10 maxlength=10 class=tipo3><button id='trigger1' onclick=".$cal.">...</button>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		//]]></script>
		<?php
		echo "</td></tr> ";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Final de Proceso</td> ";
		$cal="calendario('2')";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wfec2' id='wfec2' readonly='readonly'  value=".$wfec2." size=10 maxlength=10 class=tipo3><button id='trigger2' onclick=".$cal.">...</button>";
		?>
		<script type="text/javascript">//<![CDATA[
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
		//]]></script>
		<?php
		echo "</td></tr>";

		$id="ajaxquery('1','".$empresa."')";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='button' value='ENTER' Onclick=".$id." class=tipo4>&nbsp;&nbsp;<input type=button value='Cerrar ventana' class=tipo4 onclick='javascript:window.close();'></td></tr>";
		echo "</table><br><br>  ";
		echo "<IMG SRC='/matrix/images/medical/root/CLOCK.gif'>";
		echo "</center> ";
	}
	else
	{
		echo "<center> ";
		$query = "lock table ".$empresa."_000049 LOW_PRIORITY WRITE ";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO DE RIPS : ".mysql_errno().":".mysql_error());
		$query = "select Cfgrip from ".$empresa."_000049 where Id=1 ";
		$err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE RIPS : ".mysql_errno().":".mysql_error());
		$row2 = mysql_fetch_row($err2);
		$query =  " update ".$empresa."_000049 set Cfgrip = Cfgrip + 1 where Id=1 ";
		$err1 = mysql_query($query,$conex) or die("ERROR INCREMENTANDO CONSECUTIVO DE RIPS : ".mysql_errno().":".mysql_error());
		$wremi=$row2[0] + 1;
		$query = " UNLOCK TABLES";
		$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLA DE CONSECUTIVO DE RIPS T49 : ".mysql_errno().":".mysql_error());
		while (strlen($wremi) < 6)
		{
			$wremi = "0".$wremi;
		}
		$fac=array();
		$facant='';
		$fil=-1;
		switch($ti)
		{
			case 0:
				$condicionFactura = "";
				if( $wfac != "" && $wffa != "" ){
					$condicionFactura = " and fenffa = '".$wffa."' and fenfac = '".$wfac."' ";
					$condicionEnvio   = "";
				}
				if( $wenv != "0" ){
					$condicionEnvio = " and Rennum = ".$wenv;
				}
				$query = "select Fenfac, Grutri from ".$empresa."_000040, ".$empresa."_000020, ".$empresa."_000021, ".$empresa."_000018, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000004 ";
				$query .= " where Carenv = 'on' ";
				$query .= " and Carfue = Renfue ";
				$query .= $condicionEnvio;
				$query .= $condicionFactura;
				$query .= " and Renfue = Rdefue ";
				$query .= " and Rennum = Rdenum ";
				$query .= " and Rdefac = Fenfac ";
				$query .= " and Fenhis = Tcarhis ";
				$query .= " and Fening = Tcaring ";
				$query .= " and fenfac = Rcffac ";
   				$query .= " and ".$empresa."_000106.id = Rcfreg ";
   				$query .= " and Rcfest = 'on' ";
				$query .= " and Tcarconcod = Grucod ";
				$query .= " and Grutri != '' ";
				$query .= " and Grutri != 'NA-NO APLICA' ";
				$query .= " group by Fenfac, Grutri ";
				$query .= " order by Fenfac ";
			break;
			case 1:
				$query = "select Fenfac, Grutri from ".$empresa."_000018, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000004, ".$empresa."_000024 ";
				$query .= " where Fenhis != '0' ";
				$query .= " and Fenfec between '".$wfec1."' and '".$wfec2."' ";
				$query .= " and Fencod = Empcod ";
				$query .= " and Empcod = '01'";
				$query .= " and Fenhis = Tcarhis ";
				$query .= " and Fening = Tcaring ";
				$query .= " and fenfac = Rcffac ";
   				$query .= " and ".$empresa."_000106.id = Rcfreg ";
   				$query .= " and Rcfest = 'on' ";
				$query .= " and Tcarconcod = Grucod ";
				$query .= " and Grutri != '' ";
				$query .= " and Grutri != 'NA-NO APLICA' ";
				$query .= " group by Fenfac, Grutri";
				$query .= " order by Fenfac ";
			break;
			case 2:
				$query = "select Fenfac, Grutri from ".$empresa."_000018, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000004, ".$empresa."_000024 ";
				$query .= " where Fenhis != '0' ";
				$query .= " and Fenfec between '".$wfec1."' and '".$wfec2."' ";
				$query .= " and Fencod = Empcod ";
				if($wemp != "TODAS")
					$query .= " and Empcod = '".substr($wemp,0,strpos($wemp,"-"))."'";
				$query .= " and Fenhis = Tcarhis ";
				$query .= " and Fening = Tcaring ";
				$query .= " and fenfac = Rcffac ";
   				$query .= " and ".$empresa."_000106.id = Rcfreg ";
   				$query .= " and Rcfest = 'on' ";
				$query .= " and Tcarconcod = Grucod ";
				$query .= " and Grutri != '' ";
				$query .= " and Grutri != 'NA-NO APLICA' ";
				$query .= " group by Fenfac, Grutri";
				$query .= " order by Fenfac ";
			break;
		}
		$err = mysql_query($query,$conex)or die("ERROR  : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				if($facant != $row[0])
				{
					if($fil > -1)
					{
						$col=$col + 1;
						$fac[$fil][$col][0]="AF";
						$fac[$fil][$col][1]=0;
						$col=$col + 1;
						$fac[$fil][$col][0]="US";
						$fac[$fil][$col][1]=0;
						$fac[$fil][1][0]=$col - 2;
					}
					$facant = $row[0];
					$fil = $fil + 1;
					$fac[$fil][0][0]=$row[0];
					$fac[$fil][1][0]=0;
					$fac[$fil][2][0]=0;
					$col=2;
				}
				$col=$col + 1;
				$fac[$fil][$col][0]=substr($row[1],0,strpos($row[1],"-"));
				$fac[$fil][$col][1]=0;
			}
			$col=$col + 1;
			$fac[$fil][$col][0]="AF";
			$fac[$fil][$col][1]=0;
			$col=$col + 1;
			$fac[$fil][$col][0]="US";
			$fac[$fil][$col][1]=0;
			$fac[$fil][1][0]=$col - 2;
		}
		$win="(".chr(34).$fac[0][0][0].chr(34);

		for ($i=1;$i<=$fil;$i++)
		{
			$win .= ",".chr(34).$fac[$i][0][0].chr(34);

		}
		$win .= ")";
		$query = "select Cfgnit, Cfgnom, Cfgcpr, Cfgtdo from ".$empresa."_000049 ";
		$err = mysql_query($query,$conex);
		$row = mysql_fetch_row($err);
		$confnit=$row[0];
		$confnom=$row[1];
		$confcpr=$row[2];
		$conftdo=substr($row[3],0,strpos($row[3],"-"));
		$kt=0;
		if($ti == 0)
		{
			$query = "select Rencod from ".$empresa."_000040, ".$empresa."_000020 ";
			$query .= " where Carenv = 'on' ";
			$query .= " and Carfue = Renfue ";
			$query .= " and Rennum = ".$wenv;
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_row($err);
			$wempresa=$row[0];
		}
		else
			$wempresa="01";
		$ruta="C:/Inetpub/wwwroot/MATRIX/ips/rips/".$empresa."/";
		//$ruta="C:/wamp/www/MATRIX/IPS/rips/".$empresa."/";
		$dh=opendir($ruta);
		if(readdir($dh) == false)
			mkdir($ruta,0777);
		//Se construye tabla de información  :)
		echo "<table border=0>";
		echo "<tr>";
		//Titulo de la table de la información mavila :)
		echo "<td align=center colspan=2><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td>";
		echo "</tr>";
		echo "<tr><td align=center colspan=2><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=2>PROGRAMA DE GENERACION DE RIPS Ver. 2007-05-16</font></td></tr>";
		switch ($ti)
		{
			case 0:
				echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>ENVIO NRo. : ".$wenv."</b></font>";
			break;
			case 1:
				echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>PARTICULARES</b></font>";
			break;
			case 2:
				echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>EMPRESAS - ".$wemp."</b></font>";
			break;
		}
		echo "<tr>";
		echo "<td align=center bgcolor=#999999 colspan=2>";
			echo "<font size=2><b>REMISION NRo. : ".$wremi."</b></font>";
		echo "</td>";
		echo "</tr>";
		if($ti == 1 OR $ti == 2)
			echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>DESDE: ".$wfec1." HASTA ".$wfec2."</b></font></td></tr>";
		
		//Titulos de las tablas mavila :)
		echo "<tr>";
		echo "<td bgcolor=#CCCCCC align=center><b>TIPO<BR>ARCHIVO</b></td>";
		echo "<td bgcolor=#CCCCCC align=center><b>NRo.<BR>REGISTRO</b></td>";
		echo "</tr>";

		//******** GENERACION DE ARCHIVO AF *********
		$kAF=0;
		$query = "select Fenfac, Fenfec, Fencop, Fenval, Empcmi, Empnom, Ingpol  from ".$empresa."_000018, ".$empresa."_000024, ".$empresa."_000101 ";
		$query .= " where Fenfac in ".$win." ";
		$query .= "   and Fencod = Empcod ";
		$query .= "   and Fenhis = Inghis ";
		$query .= "   and Fening = Ingnin ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex);

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AF".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[0],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " AF-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "AF" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}
				//MODIFICACION 2007-06-25 LIMITACION DEL NOMBRE DE LA ENTIDAD ADMINISTRADORA A 30 CARACTERES  row[5].
				$registro=$confcpr.",".$confnom.",".$conftdo.",".$confnit.",".$row[0].",".c_fecha($row[1]).",".c_fecha($wfec1).",".c_fecha($wfec2).",".$row[4].",".substr($row[5],0,30).",,,".substr($row[6],0,10).",".$row[2].",0,0,".$row[3];
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kAF++;
			}
			fclose($file);
			anular($conex,$wenv,"AF",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','AF','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kAF;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AF</B></font></td><td bgcolor=#999999><font size=2><B>".$kAF."</B></font></td></tr>";

		//******** GENERACION DE ARCHIVO US *********
		$kUS=0;
		$query = "select Empcmi, Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pacsex, Paczon, Pactus, Paciu, Pacdep, Pacfna, Fenfac   from ".$empresa."_000018, ".$empresa."_000024, ".$empresa."_000100 ";
		$query .= " where Fenfac in ".$win." ";
		$query .= "   and Fencod = Empcod ";
		$query .= "   and Fenhis = Pachis ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex);

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/US".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[13],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " US-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "US" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}
				$ann=(integer)substr($row[12],0,4)*360 +(integer)substr($row[12],5,2)*30 + (integer)substr($row[12],8,2);
				$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				$ann1=(integer)(($aa - $ann)/360);
				$meses=(integer)((($aa - $ann) % 360)/30);
				$dias1=(integer)((($aa - $ann) % 360) % 30);
				if ($ann1 > 0)
				{
					$wedad = $ann1;
					$wmed=1;
				}
				elseif($meses > 0)
					{
						$wedad = $meses;
						$wmed=2;
					}
					else
					{
						$wedad = $dias1;
						$wmed=3;
					}
				$registro=$row[1].",".$row[2].",".$row[0].",".$row[9].",".$row[3].",".$row[4].",".$row[5].",".$row[6].",".$wedad.",".$wmed.",".$row[7].",".$row[11].",".substr($row[10],2,3).",".$row[8];
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kUS++;
			}
			fclose($file);
			anular($conex,$wenv,"US",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','US','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kUS;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS US</B></font></td><td bgcolor=#999999><font size=2><B>".$kUS."</B></font></td></tr>";

		//Generacion de los archivos AM-MEDICAMENTOS Mavila :)
		//******** GENERACION DE ARCHIVO AM *********		
		$kAM=0;
		
		//Se actualiza la construcción de la consulta :)
		$query  = " SELECT Fenfac as factura, Pactdo as tipo_documento, Pacdoc as documento,  ";
		//Se evidencia que el número de autorización viene dado desde el número de ingreso de la orden :)
		$query .= " Ingord as orden_ingreso, ";
		$query .= " Tcarprocod as codigo_articulo, ";
		$query .= " Artpos as articulo_pos, Tcarpronom as descripcion_articulo,  ";
		$query .= " Artffa as forma_farmaceutica, Artcon as concentracion, Artuni as unidad_medida, ";
		$query .= " Tcarcan as cantidad, Tcarvun as valor_unitario, Tcarvto as valor_total,  ";
		//Se adiciona variable para el calculo de las cantidades teniendo en cuenta los registros negativos :)
		$query .= " SUM(CASE WHEN cargo.Tcarvto > 0 THEN (cargo.Tcarcan)  ELSE (cargo.Tcarcan)*(-1) ";
		$query .= " END) AS Sum_cantidad, ";
		//Se adiciona variable para el calculo del valor total teniendo en cuenta los registros negativos :)
		$query .= " SUM(Tcarvto) AS Sum_valor_total, ";
		//Se obtinene el codigo del cums asociado al articulo de la tabla 000244 :)
		$query .= " eq_articulo.Cumcod AS cums ";
		$query .= " from ".$empresa."_000018 factura ";
		$query .= " INNER JOIN ".$empresa."_000100 paciente ON factura.Fenhis = paciente.Pachis ";
		$query .= " INNER JOIN ".$empresa."_000101 ingreso ";
		$query .= " ON factura.Fenhis=ingreso.Inghis and factura.Fening =ingreso.Ingnin ";
		$query .= " INNER JOIN ".$empresa."_000106 cargo ";
		$query .= " ON factura.Fenhis =cargo.Tcarhis AND factura.fening=cargo.Tcaring ";
		$query .= " INNER JOIN ".$empresa."_000066 det_cargo ";
		$query .= " ON factura.fenfac = det_cargo.Rcffac AND cargo.id =det_cargo.Rcfreg ";
		$query .= " INNER JOIN ".$empresa."_000004 grupo ";
		$query .= " ON grupo.Grucod = cargo.Tcarconcod AND grupo.Grutri = 'AM-MEDICAMENTOS' ";
		$query .= " INNER JOIN ".$empresa."_000001 articulo ON cargo.Tcarprocod = articulo.Artcod ";
		$query .= " LEFT JOIN ".$empresa."_000244 eq_articulo ON cargo.Tcarprocod = eq_articulo.Cumint ";
		$query .= " WHERE det_cargo.Rcfest = 'on'  ";
		$query .= " AND Fenfac in ".$win." ";
		//Se realiza agrupación para obtener los calculos de las cantidades y valor total :)
		$query .= " GROUP BY Fenfac, Pactdo, Pacdoc, Ingord, ";
		$query .= " Tcarprocod, Artpos, Tcarpronom,  Artffa, Artcon, Artuni ";
		$query .= " ORDER BY Fenfac ";

		//CONSULTA ANTERIOR :)
			/*$query = "select Fenfac, Pactdo, Pacdoc, ";
			//Se evidencia que el número de autorización viene dado desde el número de ingreso de la orden :)
			$query .= " Ingord, Cumcodq	, Artpos, Tcarpronom, ";
			$query .= " Artffa, Artcon, Artuni, Tcarcan, Tcarvun, Tcarvto  ";
			$query .= " from ".$empresa."_000018, "; //Datos de factura :)
			$query .= "  ".$empresa."_000100, "; //Info paciente :)
			$query .= "  ".$empresa."_000101, "; //Info ingreso :)
			$query .= "  ".$empresa."_000106, "; //info de cargos :)
			$query .= "  ".$empresa."_000066, "; //info cargos con facturas  :)
			$query .= "  ".$empresa."_000004, "; //Grupo de inventarios :)
			$query .= "  ".$empresa."_000001, "; //Articulos de inventarios :)
			$query .= "  ".$empresa."_000244 "; //Equivalencia en cums :)
			$query .= " where Fenfac in ".$win." ";
			$query .= "   and Fenhis = Pachis ";
			$query .= "   and Fenhis = Inghis ";
			$query .= "   and Fening = Ingnin ";
			$query .= "   and Fenhis = Tcarhis ";
			$query .= "   and Fening = Tcaring ";
			$query .= "   and fenfac = Rcffac ";
	   		$query .= "   and ".$empresa."_000106.id = Rcfreg ";
	   		$query .= "   and Rcfest = 'on' ";
			$query .= "   and Tcarconcod = Grucod ";
			$query .= "   and Grutri = 'AM-MEDICAMENTOS' ";
			$query .= "   and Tcarprocod = Artcod ";
			//Filtro para la adicion del cum de los medicamentos 
			$query .= "   and Tcarprocod = Cumint ";
			$query .= "   order by Fenfac ";
		CONSULTA ANTERIOR */
		//echo 'Se imprime consulta: '.$query;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AM".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[0],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " AM-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "AM" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}
				if($row[5] == "on")
					$wpos=1;
				else
					$wpos=2;
				
				//Se realiza validacion de la infamcion que se muestra :)
				$factura = $row[0];
				$tipo_documento = $row[1];
				$documento = $row[2];
				$orden_autorizacion = $row[3];
				$codigo_articulo = $row[4];
				$articulo_pos = $wpos;
				$descripcion_articulo = $row[6];
				$forma_farmaceutica = $row[7];
				$concentracion = $row[8];
				$unidad_medida = substr($row[9],strpos($row[9],"-")+1);
				$cantidad = $row[10];
				$valor_unitario = $row[11];
				$valor_total = $row[12];
				$suma_cantidad = $row[13];
				$suma_valor_total = $row[14];
				//Se realiza validacion del cums del articulo :)
				if ($row[15] != '') {
					$cums_articulo = $row[15];
				}else{
					$cums_articulo = $row[4];
				}				

				//$registro=$row[0].",".$confcpr.",".$row[1].",".$row[2].",".$row[3].",".$row[4].",".$wpos.",".$row[6].",".$row[7].",".$row[8].",".substr($row[9],strpos($row[9],"-")+1).",".$row[10].",".$row[11].",".$row[12];
				//Se adiciona variables para los registros :)
				$registro= $factura.",".$confcpr.",".$tipo_documento.",".$documento.",".$orden_autorizacion.",".$cums_articulo.",".$articulo_pos.",".$descripcion_articulo.",".$forma_farmaceutica.",".$concentracion.",".$unidad_medida.",".$suma_cantidad.",".$valor_unitario.",".$suma_valor_total;				
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kAM++;
			}
			fclose($file);
			anular($conex,$wenv,"AM",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','AM','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kAM;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AM</B></font></td><td bgcolor=#999999><font size=2><B>".$kAM."</B></font></td></tr>";

		//Generacion de los archivos AC-MEDICAMENTOS Mavila :)
		//******** GENERACION DE ARCHIVO AC *********
		$kAC=0;		
		$query = "select Fenfac, Pactdo, Pacdoc, Tcarfec, ";
		//Se evidencia que el número de autorización viene dado desde el número de ingreso de la orden :)
		$query .= " Ingord, Procup, '10', Egrcex , Egrdxi,'','','', Egrtdp, ";
		$query .= " Tcarvto,0, Tcarvto, Fenfac, Pactdo  ";
		$query .= " from ".$empresa."_000018, ".$empresa."_000100, ".$empresa."_000101, ".$empresa."_000108, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000103, ".$empresa."_000004 ";
		$query .= " where Fenfac in ".$win." ";
		$query .= "   and Fenhis = Pachis ";
		$query .= "   and Fenhis = Inghis ";
		$query .= "   and Fening = Ingnin ";
		$query .= "   and Inghis = Egrhis ";
		$query .= "   and Ingnin = Egring ";
		$query .= "   and Egrhis = Tcarhis ";
		$query .= "   and Egring = Tcaring ";
		$query .= "   and fenfac = Rcffac ";
   		$query .= "   and ".$empresa."_000106.id = Rcfreg ";
   		$query .= "   and Rcfest = 'on' ";
   		$query .= "   and Tcarprocod = Procod ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AC-CONSULTA' ";
		$query .= "   order by Fenfac ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex);

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AC".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[16],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " AC-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "AC" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}
				if(strpos($row[5],"-") !== false)
					$row[5]=substr($row[5],0,strpos($row[5],"-"));
				$registro=$row[16].",".$confcpr.",".$row[17].",".$row[2].",".c_fecha($row[3]).",".$row[4].",".$row[5].",".$row[6].",".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",".$row[13].",".$row[14].",".$row[15];
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kAC++;
			}
			fclose($file);
			anular($conex,$wenv,"AC",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','AC','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kAC;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AC</B></font></td><td bgcolor=#999999><font size=2><B>".$kAC."</B></font></td></tr>";

		//Generacion del archivo AP-Procedimientos mavila :)
		//******** GENERACION DE ARCHIVO AP *********
		$kAP=0;
		$query = "select Fenfac, Pactdo, Pacdoc, Tcarfec, ";
		//Se evidencia que el número de autorización viene dado desde el número de ingreso de la orden :)
		$query .= " Ingord, Procup, '1', '1', '2', Egrdxi,'' , '', '1', Tcarvto, Pachis, Pactdo, Pacdoc ";
		$query .= " from ".$empresa."_000018, ".$empresa."_000100, ".$empresa."_000101, ".$empresa."_000108, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000103, ".$empresa."_000004 ";
		$query .= " where Fenfac in ".$win." ";
		$query .= "   and Fenhis = Pachis ";
		$query .= "   and Fenhis = Inghis ";
		$query .= "   and Fening = Ingnin ";
		$query .= "   and Inghis = Egrhis ";
		$query .= "   and Ingnin = Egring ";
		$query .= "   and Egrhis = Tcarhis ";
		$query .= "   and Egring = Tcaring ";
		$query .= "   and fenfac = Rcffac ";
   		$query .= "   and ".$empresa."_000106.id = Rcfreg ";
   		$query .= "   and Rcfest = 'on' ";
   		$query .= "   and Tcarprocod = Procod ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AP-PROCEDIMIENTOS' ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex);

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AP".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[0],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " AP-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "AP" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}

				//se realiza validacion del campo Procup que contiene el cups del procedimiento 
				//Si este campo contiene un '-' concatenando el cups mas la descripción :)
				if(strpos($row[5],"-") !== false){
					//Se obtiene solo el codigo cups del procedimiento :)
					$row[5]=substr($row[5],0,strpos($row[5],"-"));
				}
				
				$registro=$row[0].",".$confcpr.",".$row[15].",".$row[16].",".c_fecha($row[3]).",".$row[4].",".$row[5].",".$row[6].",".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$row[12].",".$row[13];
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kAP++;
			}
			fclose($file);
			anular($conex,$wenv,"AP",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','AP','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062){
				die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
			}
		}
		$kt += $kAP;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AP</B></font></td><td bgcolor=#999999><font size=2><B>".$kAP."</B></font></td></tr>";

		//Generacion de los archivos AU-URGENCIAS Mavila :)
		//******** GENERACION DE ARCHIVO AU *********
		$kAU=0;
		$query = "select Fenfac, Pactdo, Pacdoc, Ingfei, Inghin, Ingord, '13', Egrdxi, '', '', '', '1', Tcarfec, ".$empresa."_000106.hora_data, Fenhis, Fening  from ".$empresa."_000018, ".$empresa."_000100, ".$empresa."_000101, ".$empresa."_000108, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000004 ";
		$query .= " where Fenfac in ".$win." ";
		$query .= "   and Fenhis = Pachis ";
		$query .= "   and Fenhis = Inghis ";
		$query .= "   and Fening = Ingnin ";
		$query .= "   and Inghis = Egrhis ";
		$query .= "   and Ingnin = Egring ";
		$query .= "   and Egrhis = Tcarhis ";
		$query .= "   and Egring = Tcaring ";
		$query .= "   and fenfac = Rcffac ";
   		$query .= "   and ".$empresa."_000106.id = Rcfreg ";
   		$query .= "   and Rcfest = 'on' ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AU-URGENCIAS' ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AU".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[0],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " AU-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "AU" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}
				$estado="1";
				$dkill="";
				if(substr($row[11],0,1) == "+" or substr($row[11],0,1) == "-")
				{
					$query = "select Sercod, Ccodes   from ".$empresa."_000108, ".$empresa."_000003 ";
					$query .= " where Serhis = '".$row[14]."'";
					$query .= "   and Sering = '".$row[15]."'";
					$query .= "   and Sercod = Ccocod ";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 = 1)
					{
						$row1 = mysql_fetch_row($err1);
						if($row1[1] = "URGENCIAS")
						{
							$estado="2";
							$query = "select Diacod   from ".$empresa."_000109 ";
							$query .= " where Diahis = '".$row[14]."'";
							$query .= "   and Diaing = '".$row[15]."'";
							$query .= "   and Diatip = 'P' ";
							$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if($num1 = 1)
							{
								$row1 = mysql_fetch_row($err1);
								$dkill=$row1[0];
							}
						}
					}
				}
				$registro=$row[0].",".$confcpr.",".$row[1].",".$row[2].",".c_fecha($row[3]).",".$row[4].",".$row[5].",".$row[6].",".$row[7].",".$row[8].",".$row[9].",".$row[10].",".$row[11].",".$estado.",".$dkill.",".c_fecha($row[12]).",".$row[13];
				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kAU++;
			}
			fclose($file);
			anular($conex,$wenv,"AU",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','AU','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kAU;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AU</B></font></td><td bgcolor=#999999><font size=2><B>".$kAU."</B></font></td></tr>";

		//Generacion de los archivos AT-OTROS SERVICIOS Mavila :)
		//******** GENERACION DE ARCHIVO AT *********
		$kAT=0;		
		$query = "select Fenfac, Pactdo, Pacdoc, ";
		//Se evidencia que el número de autorización viene dado desde el número de ingreso de la orden :)
		$query .= " Ingord, Tcarprocod, Tcarpronom, ";
		$query .= " Tcarcan, Tcarvun, Tcarvto, Grudes,  ";
		//Se adiciona variable para el calculo de las cantidades teniendo en cuenta los registros negativos :)
		$query .= " SUM(CASE WHEN Tcarvto > 0 THEN (Tcarcan)  ELSE (Tcarcan)*(-1) ";
		$query .= " END) AS Sum_cantidad, ";
		//Se adiciona variable para el calculo del valor total teniendo en cuenta los registros negativos :)
		$query .= " SUM(Tcarvto) AS Sum_valor_total ";
		$query .= " FROM ".$empresa."_000018, ".$empresa."_000100, ".$empresa."_000101, ".$empresa."_000106, ".$empresa."_000066, ".$empresa."_000004 ";
		$query .= " where Fenfac in ".$win." ";
		$query .= "   and Fenhis = Pachis ";
		$query .= "   and Fenhis = Inghis ";
		$query .= "   and Fening = Ingnin ";
		$query .= "   and Fenhis = Tcarhis ";
		$query .= "   and Fening = Tcaring ";
		$query .= "   and fenfac = Rcffac ";
   		$query .= "   and ".$empresa."_000106.id = Rcfreg ";
   		$query .= "   and Rcfest = 'on' ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AT-OTROS SERVICIOS' ";
		//Se realiza agrupación para obtener los calculos de las cantidades y valor total :)
		$query .= " GROUP BY Fenfac, Pactdo, Pacdoc, Ingord, ";
		$query .= " Tcarprocod, Tcarpronom ";
		$query .= "   order by Fenfac ";

		//echo "Se imprime consulta am: ".$query;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AT".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$pos=bi($fac,$fil,$row[0],0);
				if($pos != -1)
				{
					for ($j=3;$j<=$fac[$pos][1][0]+2;$j++)
					{
						//echo " AT-Fac : ".$fac[$pos][0][0]." pos : ".$pos." j : ".$j." ".$fac[$pos][$j][0]." : ".$fac[$pos][$j][1]."<br>";
						if($fac[$pos][$j][0] == "AT" and $fac[$pos][$j][1] == 0)
						{
							$fac[$pos][$j][1] = 1;
							$fac[$pos][2][0]++;
						}
					}
				}
				if(strpos($row[9],"MATERIAL") === false)
					$Wtipo="4";
				else
					$Wtipo="1";

				//Se realiza validacion de la infamcion que se muestra :)
				$factura = $row[0];
				$tipo_documento = $row[1];
				$documento = $row[2];
				$orden_autorizacion = $row[3];
				$codigo_articulo = $row[4];
				$descripcion_articulo = $row[5];
				$cantidad = $row[6];
				$valor_unitario = $row[7];
				$valor_total = $row[8];
				//calculo cantidad y calculo de valor total :)
				$suma_cantidad = $row[10];
				$suma_valor_total = $row[11];

				//$registro=$row[0].",".$confcpr.",".$row[1].",".$row[2].",".$row[3].",".$Wtipo.",".$row[4].",".$row[5].",".$row[6].",".$row[7].",".$row[8];
				$registro= $factura.",".$confcpr.",".$tipo_documento.",".$documento.",".$orden_autorizacion.",".$Wtipo.",".$codigo_articulo.",".$descripcion_articulo.",".$suma_cantidad.",".$valor_unitario.",".$suma_valor_total;

				$registro=$registro.chr(13).chr(10);
  				fwrite ($file,$registro);
  				$kAT++;
			}
			fclose($file);
			anular($conex,$wenv,"AT",$wremi,0);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','AT','".$wempresa."','".$fecha."','on','C-".$empresa."')";
			$err1 = mysql_query($query,$conex);
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kAT;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AT</B></font></td><td bgcolor=#999999><font size=2><B>".$kAT."</B></font></td></tr>";

		//******** GENERACION DE ARCHIVO CT *********
		$kCT=0;
		$datafile="../rips/".$empresa."/CT".$wremi.".txt";
		$file = fopen($datafile,"w+");
		if($kAF > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",AF".$wremi.",".$kAF;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
  		if($kUS > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",US".$wremi.",".$kUS;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
  		if($kAM > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",AM".$wremi.",".$kAM;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
  		if($kAC > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",AC".$wremi.",".$kAC;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
  		if($kAP > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",AP".$wremi.",".$kAP;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
  		if($kAU > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",AU".$wremi.",".$kAU;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
  		if($kAT > 0)
		{
			$kCT++;
			$registro=$confcpr.",".date("d/m/Y").",AT".$wremi.",".$kAT;
			$registro=$registro.chr(13).chr(10);
	  		fwrite ($file,$registro);
  		}
		fclose($file);
		$kt += $kAT;
		anular($conex,$wenv,"CT",$wremi,0);
		$fecha = date("Y-m-d");
		$hora = (string)date("H:i:s");
  		$query = "insert ".$empresa."_000118 (medico,fecha_data,hora_data, Mrienv, Mrirem, Mritip, Mriemp, Mrifec, Mriest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wenv.",'".$wremi."','CT','".$wempresa."','".$fecha."','on','C-".$empresa."')";
  		$err1 = mysql_query($query,$conex);
  		if($err1 != 1 and mysql_errno() != 1062)
  			die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS CT</B></font></td><td bgcolor=#999999><font size=2><B>".$kCT."</B></font></td></tr>";

		//******** GENERACION DE INCONSISTENCIAS *********
		$wsw=0;
		for ($i=0;$i<=$fil;$i++)
		{
			if($fac[$i][1][0] != $fac[$i][2][0])
			for ($j=3;$j<=$fac[$i][1][0]+2;$j++)
			{
				if($fac[$i][$j][1] == 0)
				{
					//echo "<pre>".print_r( $fac )."</pre>";
					switch ($fac[$i][$j][0])
					{
						case "AF":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO AF &nbsp .EMPRESA INCORRECTA &nbsp .(Y/O) INGRESO BORRADO</font></td></tr>";
						break;
						case "US":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO US &nbsp .EMPRESA INCORRECTA &nbsp .(Y/O) USUARIO BORRADO</font></td></tr>";
						break;
						case "AM":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO AM &nbsp .NO EXISTE EL ARTICULO EN EL MAESTRO &nbsp .(Y/O) EMPRESA INCORRECTA &nbsp .(Y/O) USUARIO BORRADO&nbsp .(Y/O) INGRESO BORRADO</font></td></tr>";
						break;
						case "AC":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO AC &nbsp .NO EXISTE EL EGRESO &nbsp .(Y/O) EMPRESA INCORRECTA &nbsp .(Y/O) USUARIO BORRADO&nbsp .(Y/O) INGRESO BORRADO</font></td></tr>";
						break;
						case "AP":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO AP &nbsp .NO EXISTE EL EGRESO &nbsp .(Y/O) EMPRESA INCORRECTA &nbsp .(Y/O) USUARIO BORRADO&nbsp .(Y/O) INGRESO BORRADO</font></td></tr>";
						break;
						case "AU":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO AU &nbsp .NO EXISTE EL EGRESO &nbsp .(Y/O) EMPRESA INCORRECTA &nbsp .(Y/O) USUARIO BORRADO&nbsp .(Y/O) INGRESO BORRADO</font></td></tr>";
						break;
						case "AT":
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>NO GENERO ARCHIVO AT &nbsp .EMPRESA INCORRECTA &nbsp .(Y/O) USUARIO BORRADO&nbsp .(Y/O) INGRESO BORRADO</font></td></tr>";
						break;
						default:
							echo "<tr><td bgcolor=#999999><font size=2>".$fac[$i][0][0]."</font></td><td bgcolor=#999999><font size=2>FACTURA CON ERROR NO ESPECIFICO REVISAR TABLA DE GRUPOS DE INVENTARIO (Grutip) TIPOS DE RIPS</font></td></tr>";
					}
					$wsw++;
				}
			}
		}
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS GENERADOS</B></font></td><td bgcolor=#999999><font size=2><B>".$kt."</B></font></td></tr>";
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS INCONSISTENTES</B></font></td><td bgcolor=#999999><font size=2><B>".$wsw."</B></font></td></tr>";
		if($wsw == 0)
		{
			$ruta="../rips/".$empresa."/";
	   		//echo "<tr><td bgcolor=#dddddd  colspan=2 align=center><b><A href=".$ruta.">Haga Click Para Bajar el(Los) Archivos</A></b></td></tr></center>";
	   		$d = dir("../rips/".$empresa."/");
			while (false !== ($files = $d->read()))
			{
				if(substr($files,2,6) == $wremi)
				{
			 		echo "<tr><td bgcolor=#dddddd  colspan=2 align=center><b><A href=".$ruta.$files.">".$files." Haga Click Para Bajar el Archivo</A></b></td></tr></center>";
		 		}
			}
			$d->close();
   		}
   		else
   		{
	   		anular($conex,$wenv,"AA",$wremi,1);
	   		$datafile="../rips/".$empresa."/AF".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/US".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/AM".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/AC".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/AP".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/AU".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/AT".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
	   		$datafile="../rips/".$empresa."/CT".$wremi.".txt";
	   		if (file_exists($datafile))
	   			unlink($datafile);
   		}
		echo "</table>";

		echo "</center>";
	}
	echo "</form>";
}
echo "</div>";
?>
</body>
</html>