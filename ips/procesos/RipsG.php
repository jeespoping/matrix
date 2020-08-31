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

	function ajaxquery(fila,empresa, wemp_pmla)
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
		s= document.forms.rips.wemp;
		x5 = s.options[s.selectedIndex].value;
		factura = document.getElementById("factura").value; //agregado por WILL

		ajax=nuevoAjax();
		//st="ripsG.php?empresa="+empresa+"&wemp_pmla="+wemp_pmla+"&wfec1="+x2+"&wfec2="+x3+"&wenv="+x1+"&ti="+x4+"&wemp="+x5;
		st="ripsG.php?empresa="+empresa+"&wemp_pmla="+wemp_pmla+"&wfec1="+x2+"&wfec2="+x3+"&wenv="+x1+"&ti="+x4+"&wemp="+x5+"&factura="+factura;
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
	   PROGRAMA : RipsG.php
	   Fecha de Liberacion : 2006-04-08
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : Diciembre 24 de 2013

	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite generar el registro individual
	   de prestacion de servicios que debe acompañar la informacion de facturacion de una IPS.

	   Se puede ejecutar bajo dos modalidades:
	   	. modalidad 0 : Genera los Rips de las facturas asociadas a un envio.
	   	. Modalidad 1 : Genera los Rips de particulares entre dos fechas especificadas.

		
	   PROGRAMA : RipsG.php
	   Fecha de Modifica : 2019-06-19
	   Modifica : Ing. Gustavo Alvendaño
	   
	   Para generar los archivos AP no se tiene encuenta la temporal 18 se procede a modificar el query con el fin que no duplique
	   los cargos por cada uno de los pacientes
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
// --> 2013-12-24. Jerson Trujillo.
function consultarAliasPorAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		die("La institucion con el codigo :".$codigoInstitucion." no se encuentra, ni la aplicacion: ".$nombreAplicacion.".  Por favor verifique el valor de wemp_pmla");
	}
	return $alias;
}

@session_start();
//if(!@session_is_registered("user"))
if(!isset($_SESSION['user']))	
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	//echo "<form name='rips' action='rips.php' method=post>";
	echo "<form name='rips' action='RipsG.php' method=post>";
	//include("conex.php");
	mysql_select_db("matrix");

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $empresa.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------

	if(!isset($wfec1))
		$wfec1=date("Y-m-d");
	if(!isset($wfec2))
		$wfec2=date("Y-m-d");
	if(!isset($wenv))
		$wenv=0;
	if(!isset($factura))
		$factura = $_GET['factura'];
	//$factura = $_GET['factura'];
	//$factura = 'PRUE-980000079';
		
	if(!isset($ti) or !isset($wenv) or ($wenv == 0 and $ti == 0) or !isset($factura))
	{
		echo "<center> ";
		echo "<table border=0 id=tipo2> ";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr> ";
		echo "<tr><td align=center colspan=2>PROGRAMA DE GENERACION DE RIPS Ver. 2013-01-19</td></tr> ";
		echo "<tr>
				  <td bgcolor=#999999 align=center colspan=2>
				  		<input type='RADIO' name=ti value=0 checked><b>X ENVIO</b>
				  		<input type='RADIO' name=ti value=2><b>EMPRESAS</b>
				  		<input type='RADIO' name=ti value=3><b>FACTURA</b>
				  </td>
			  </tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. Envio</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wenv' id='wenv' value=".$wenv." size=10 maxlength=10 class=tipo3></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Factura</td><td><input type='TEXT' name='factura' id='factura' size=70 maxlength=70 placeholder='PREFIJO-NUMERO'></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa</td><td bgcolor=#cccccc align=center><select name='wemp' id=tipo1>";
		
		$query = "SELECT Empcod, Empnom  from ".$empresa."_000024, ".$empresa."_000029 where mid(Emptem,1,2) = Temcod and Temche = 'off'  order by Empnom";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
				
		if ($num>0)
		{
			echo "<option value='TODAS'>TODAS</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
			}
		}
		else
			echo "<option value='0-NO APLICA'>0-NO APLICA</option>";
		echo "</select></td></tr>";
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

		$id='ajaxquery("1","'.$empresa.'", "'.$wemp_pmla.'")';
		echo "<tr>
				  <td bgcolor=#cccccc  colspan=2 align=center>
				  		<input type='button' value='ENTER' Onclick='".$id."' class=tipo4>&nbsp;&nbsp;
				  		<input type=button value='Cerrar ventana' class=tipo4 onclick='javascript:window.close();'>
				  </td>
			  </tr>";
		echo "</table><br><br>  ";
		echo "<IMG SRC='/matrix/images/medical/root/CLOCK.gif'>";
		echo "</center> ";
	}
	ElSE
	{
		
		echo "<center> ";
		$query = "lock table ".$empresa."_000049 LOW_PRIORITY WRITE ";
		$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO CONSECUTIVO DE RIPS : ".mysql_errno().":".mysql_error());
		$query = "select Cfgrip from ".$empresa."_000049 where Id=1 ";
		$err2 = mysql_query($query,$conex) or die("ERROR CONSULTANDO CONSECUTIVO DE RIPS : ".mysql_errno().":".mysql_error());
		$row2 = mysql_fetch_array($err2);
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
				$query = "select Fenfac, Grutri from ".$empresa."_000040, ".$empresa."_000020, ".$empresa."_000021, ".$empresa."_000018, ".$empresa."_000106, ".$empresa."_000066, ".$tablaConceptos." ";
				$query .= " where Carenv = 'on' ";
				$query .= " and Carfue = Renfue ";
				$query .= " and Rennum = ".$wenv;
				$query .= " and Renfue = Rdefue ";
				$query .= " and Rennum = Rdenum ";
				$query .= " and Rdefac = Fenfac ";
				$query .= " and fenfac = Rcffac ";
   				$query .= " and ".$empresa."_000106.id = Rcfreg ";
   				$query .= " and Rcfest = 'on' ";
				$query .= " and Tcarconcod = Grucod ";
				$query .= " and Grutri != '' ";
				$query .= " and Grutri != 'NA-NO APLICA' ";
				$query .= " group by Fenfac, Grutri ";
				$query .= " order by Fenfac ";
			break;
			case 2:
				$query = "select Fenfac, Grutri from ".$empresa."_000018, ".$empresa."_000106, ".$empresa."_000066, ".$tablaConceptos.", ".$empresa."_000024 ";
				$query .= " where Fenfec between '".$wfec1."' and '".$wfec2."' ";
				$query .= " and Fencod = Empcod ";
				if($wemp != "TODAS")
					$query .= " and Empcod = '".substr($wemp,0,strpos($wemp,"-"))."'";
				$query .= " and fenfac = Rcffac ";
   				$query .= " and ".$empresa."_000106.id = Rcfreg ";
   				$query .= " and Rcfest = 'on' ";
				$query .= " and Tcarconcod = Grucod ";
				$query .= " and Grutri != '' ";
				$query .= " and Grutri != 'NA-NO APLICA' ";
				$query .= " group by Fenfac, Grutri";
				$query .= " order by Fenfac ";
								
			break;
			case 3:
				$query = "select Fenfac, Grutri from ".$empresa."_000018, ".$empresa."_000106, ".$empresa."_000066, ".$tablaConceptos.", ".$empresa."_000024";
				$query .= " where Fenfac = '".$factura."' ";
				$query .= " and fenfac = Rcffac ";
				$query .= " and ".$empresa."_000106.id = Rcfreg ";
				$query .= " and Rcfest = 'on' ";
				$query .= " and Tcarconcod = Grucod ";
				$query .= " and Grutri != '' ";
				$query .= " and Grutri != 'NA-NO APLICA' ";
				$query .= " group by Fenfac, Grutri";
				$query .= " order by Fenfac ";

				//echo 'FACTURA ='.$factura;
			break;
		}

		$err = mysql_query($query,$conex)or die("ERROR  : ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		
		if($num > 0)
		{
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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
		$row = mysql_fetch_array($err);
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
			$row = mysql_fetch_array($err);
			$wempresa=$row[0];
		}
		else
			$wempresa="11";
		//$ruta="/Applications/MAMP/htdocs/MATRIX/IPS/rips/".$empresa."/";
		$ruta="/var/www/matrix/ips/rips/".$empresa."/";
		//$ruta="C:/wamp/www/MATRIX/IPS/rips/".$empresa."/";
		$dh=opendir($ruta);
		if(readdir($dh) == false)
			mkdir($ruta,0777);
		echo "<table border=0>";
		echo "<tr><td align=center colspan=2><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
		echo "<tr><td align=center colspan=2><font size=2>PROGRAMA DE GENERACION DE RIPS Ver. 2019-06-19</font></td></tr>";
		switch ($ti)
		{
			case 0:
				echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>ENVIO NRo. : ".$wenv."</b></font>";
			break;
			case 2:
				echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>EMPRESAS - ".$wemp."</b></font>";
			break;
		}
		echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>REMISION NRo. : ".$wremi."</b></font>";
		echo "</td></tr>";
		if($ti == 2)
			echo "<tr><td align=center bgcolor=#999999 colspan=2><font size=2><b>DESDE: ".$wfec1." HASTA ".$wfec2."</b></font></td></tr>";
		echo "<tr><td bgcolor=#CCCCCC align=center><b>TIPO<BR>ARCHIVO</b></td><td bgcolor=#CCCCCC align=center><b>NRo.<BR>REGISTRO</b></td></tr>";

		  //******** GENERACION DEL TABLA TEMPORAL 18 *********
   
      $query =  "  CREATE TEMPORARY TABLE if not exists temp18 "
				. " (UNIQUE INDEX idx1(Tcarhis, Tcaring,Fenfac)) "
        . " select Fenfac, Tcarhis, Tcaring, Fencod, Fenfec, Fencop, Fenval 
		    from ".$empresa."_000018,".$empresa."_000066,".$empresa."_000106 
			where Fenfac in ".$win." 
			 and Fenfac = Rcffac 
			 and Rcfreg = ".$empresa."_000106.id 
			 and Rcfest = 'on' 
			 group by Fenfac, Tcarhis, Tcaring, Fencod, Fenfec, Fencop, Fenval  ";
        $err = mysql_query($query,$conex) or die ("Tepm18 ".mysql_errno().":".mysql_error());
	 

        //******** GENERACION DEL TABLA TEMPORAL GENERAL *********
        $query =  "  CREATE TEMPORARY TABLE if not exists tempG "
        . " (INDEX idx1(Tcarhis,Tcaring,Fenfac),INDEX idx2(Tcarprocod(20)),INDEX idx3(Tcarconcod(20))) "
        . " select Fenfac, Tcarhis, Tcaring, Pachis, Pactdo, Pacdoc, Inghis, Ingnin, Ingord, Ingfei, Inghin, Tcarprocod, Tcarpronom, Tcarcan, Tcarvun, Tcarvto, Tcarconcod, Tcarfec, ".$empresa."_000106.hora_data as hora_data,Rcfreg  "
        . " from ".$empresa."_000018, ".$empresa."_000100, ".$empresa."_000101, ".$empresa."_000106, ".$empresa."_000066 "
        . " where Fenfac in ".$win.""
		. "   and Tcarhis = Pachis "
        . "   and Tcarhis = Inghis "
        . "   and Tcaring = Ingnin "
        . "   and fenfac = Rcffac "
        . "   and ".$empresa."_000106.id = Rcfreg "
        . "   and Rcfest = 'on'  "
		. "   group by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20"
        . " order by Fenfac ";
        $err = mysql_query($query,$conex) or die ("TepmG ".mysql_errno().":".mysql_error());

		//******** GENERACION DE ARCHIVO AF *********
		$kAF=0;
		$query = "select Fenfac, Fenfec, Fencop, Fenval, Empcmi, Empnom, Ingpol  from temp18, ".$empresa."_000024, ".$empresa."_000101 ";
		$query .= " where Fencod = Empcod ";
		$query .= "   and Tcarhis = Inghis ";
		$query .= "   and Tcaring = Ingnin ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex) or die ("AF ".mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AF".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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
		$query = "select Empcmi, Pactdo, Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2, Pacsex, Paczon, Pactus, Paciu, Pacdep, Pacfna, Fenfac   from temp18, ".$empresa."_000024, ".$empresa."_000100 ";
		$query .= " where Fencod = Empcod ";
		$query .= "   and Tcarhis = Pachis ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex) or die ("US ".mysql_errno().":".mysql_error());;

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/US".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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

		//******** GENERACION DE ARCHIVO AM *********
		$kAM=0;
		$query = "select Fenfac, Pactdo, Pacdoc, Ingord, Tcarprocod, Artpos, Tcarpronom, Artffa, Artcon, Artuni, Tcarcan, Tcarvun, Tcarvto   from tempG, ".$tablaConceptos.", ".$empresa."_000001 ";
		$query .= " where Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AM-MEDICAMENTOS' ";
		$query .= "   and Tcarprocod = Artcod ";
		$query .= "   order by Fenfac ";

		$err = mysql_query($query,$conex) or die ("AM ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AM".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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
				$registro=$row[0].",".$confcpr.",".$row[1].",".$row[2].",".$row[3].",".$row[4].",".$wpos.",".$row[6].",".$row[7].",".$row[8].",".substr($row[9],strpos($row[9],"-")+1).",".$row[10].",".$row[11].",".$row[12];
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

		//******** GENERACION DE ARCHIVO AC *********
		$kAC=0;
		//                0         1      2        3        4         5        6     7        8     9 10 11   12       13    14    15      16      17
		$query = "select Fenfac, Pactdo, Pacdoc, Tcarfec, Ingord, Procup, '10', Egrcex , Egrdxi,'','','', Egrtdp, Tcarvto,0, Tcarvto, Fenfac, Pactdo   from tempG, ".$empresa."_000108, ".$empresa."_000103, ".$tablaConceptos." ";
		$query .= " where Tcarhis = Egrhis ";
		$query .= "   and Tcaring = Egring ";
   		$query .= "   and Tcarprocod = Procod ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AC-CONSULTA' ";
		$query .= "   order by Fenfac ";
		//echo $query."<br>";
		$err = mysql_query($query,$conex) or die ("AC ".mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AC".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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

		//******** GENERACION DE ARCHIVO AP *********
		$kAP=0;
		$query = "select Fenfac, Pactdo, Pacdoc, Tcarfec, Ingord, Procup, '1', '1', '2', Egrdxi,'' , '', '1', Tcarvto, Pachis, Pactdo, Pacdoc  from tempG, ".$empresa."_000108, ".$empresa."_000103, ".$tablaConceptos." ";
		$query .= " where Tcarhis = Egrhis ";
		$query .= "   and Tcaring = Egring ";
   		$query .= "   and Tcarprocod = Procod ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AP-PROCEDIMIENTOS' ";
		//$query .= "   GROUP by 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17 ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex) or die ("AP ".mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AP".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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
				if(strpos($row[5],"-") !== false)
					$row[5]=substr($row[5],0,strpos($row[5],"-"));
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
			if($err1 != 1 and mysql_errno() != 1062)
			 die("ERROR GRABANDO MOVIMIENTO DE RIPS : ".mysql_errno().":".mysql_error());
		}
		$kt += $kAP;
		echo "<tr><td bgcolor=#999999><font size=2><B>TOTAL REGISTROS AP</B></font></td><td bgcolor=#999999><font size=2><B>".$kAP."</B></font></td></tr>";

		//******** GENERACION DE ARCHIVO AU *********
		$kAU=0;
		$query = "select Fenfac, Pactdo, Pacdoc, Ingfei, Inghin, Ingord, '13', Egrdxi, '', '', '', '1', Tcarfec, tempG.hora_data, Tcarhis, Tcaring  from tempG, ".$empresa."_000108, ".$tablaConceptos." ";
		$query .= " where Tcarhis = Egrhis ";
		$query .= "   and Tcaring = Egring ";
		$query .= "   and Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AU-URGENCIAS' ";
		$query .= "   order by Fenfac ";
		$err = mysql_query($query,$conex) or die ("AU ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AU".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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
						$row1 = mysql_fetch_array($err1);
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
								$row1 = mysql_fetch_array($err1);
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

		//******** GENERACION DE ARCHIVO AT *********
		$kAT=0;
		$query = "select Fenfac, Pactdo, Pacdoc, Ingord, Tcarprocod, Tcarpronom, Tcarcan, Tcarvun, Tcarvto, Grudes   from tempG, ".$tablaConceptos." ";
		$query .= " where Tcarconcod = Grucod ";
		$query .= "   and Grutri = 'AT-OTROS SERVICIOS' ";
		$query .= " order by Fenfac ";
		$err = mysql_query($query,$conex) or die ("AT ".mysql_errno().":".mysql_error());

		$num = mysql_num_rows($err);
		if($num > 0)
		{
			$datafile="../rips/".$empresa."/AT".$wremi.".txt";
			$file = fopen($datafile,"w+");
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
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
				$registro=$row[0].",".$confcpr.",".$row[1].",".$row[2].",".$row[3].",".$Wtipo.",".$row[4].",".$row[5].",".$row[6].",".$row[7].",".$row[8];
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
	}
		echo "</table>";

		echo "</center>";
	
	echo "</form>";
}
echo "</div>";
?>
</body>
</html>
