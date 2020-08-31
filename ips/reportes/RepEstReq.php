<head>
  <title>REPORTE DE GESTION DE REQUERIMIENTOS</title>
</head>

  <!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
    <title>Zapatec DHTML Calendar</title>

<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/fancyblue.css" />

<!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

<!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

   <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}

    	.titulo1{color:#FFFFFF;background:#006699;font-size:18pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo2{color:#003366;background:#A4E1E8;font-size:9pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#57C8D5;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.titulo4{color:#003366;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.texto1{color:#003366;background:#cccccc;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.texto2{color:#006699;background:#f5f5dc;font-size:9pt;font-family:Tahoma;text-align:center;}
    	.texto3{color:#006699;background:#A4E1E8;font-size:9pt;font-weight:bold;font-family:Tahoma;text-align:center;}
    	.texto4{color:#003366;background:#999999;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.texto5{color:#006699;background:#FF0000;font-size:7pt;font-family:Tahoma;text-align:center;}
    	.acumulado1{color:#003366;background:#FFCC66;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.acumulado2{color:#003366;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado3{color:#003366;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;text-align:left;}
    	.acumulado4{color:#003366;background:#FFFFFF;font-size:9pt;font-family:Tahoma;font-weight:bold;text-align:right;}
    	.error1{color:#FF0000;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
   </style>

<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->

function Seleccionar()
{
	document.forma.submit();
}

</script>

<body onload=ira()>

<script type="text/javascript">

function enter()
{
	document.forms.RepEstReq.submit();
}
</script>

<?php
include_once("conex.php");

/**
 * NOMBRE:  REPORTE DE GESTION DE ESTADOS DE REQUERIMIENTOS
 *
 * PROGRAMA: RepEstReq.php
 * TIPO DE SCRIPT: PRINCIPAL
 * //DESCRIPCION: Este reporte permite listar los requerimientos de usuarios segun un estado dado, para identificar aquellos
 * que puedan estar sin evaluar, en tramite, en promotora, rechazados y cerrados.
 *
 * HISTORIAL DE ACTAULIZACIONES:
 * 2006-12-22 Juan David Jaramillo, creacion del script
 *
 *
 * Tablas que utiliza:
 * $wbasedato."_000122: select de Gestion de Requerimientos
 * $wbasedato."_000125: select de Seguimiento de Requerimientos

 *
 * @author jjaramillo
 * @package defaultPackage
 */

$wautor="Juan David Jaramillo R";



//=================================================================================================================================

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	//$wbasedato='clisur';
	$key = substr($user,2,strlen($user));

	/**
	 * include de conexión a base de datos
	 *
	 */
	

	


	echo "<form action='RepEstReq.php' method=post name='forma'>";

	$wfecha=date("Y-m-d");
	$wbd="clisur";

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

	if (!isset($wfecini) or !isset($wfecfin) or !isset ($resultado))
	{
		echo "<center><table border=1>";
		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
		echo "<tr><td class='titulo1'>REPORTE DE GESTION DE REQUERIMIENTOS</td></tr>";

		//INGRESO DE VARIABLES PARA EL REPORTE//
		if (!isset ($bandera))
		{
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
		}

		echo "<tr>";
		$cal="calendario('wfecini','1')";
		echo "<td align=center class='texto3'><b>FECHA INICIAL DE REQUERIMIENTOS: </font></b>
		      <input type='text' readonly='readonly' NAME='wfecini' value=".$wfecini." SIZE=10>
		      <input type='button' name='fecini' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
			  ?>
			  	<script type="text/javascript">//<![CDATA[
			  	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini',button:'fecini',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
			  	//]]></script>
			  <?php
		echo "<td  align=center class='texto3'><b>FECHA FINAL DE REQUERIMIENTOS: </font></b>
			  <INPUT TYPE='text' readonly='readonly' NAME='wfecfin' value=".$wfecfin." SIZE=10>
			  <input type='button' name='fecfin' value='...' onclick=".$cal."' size=10 maxlength=10></td>";
			  ?>
			  	<script type="text/javascript">//<![CDATA[
			  	Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin',button:'fecfin',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
			  	//]]></script>
			  <?php
		echo "</tr>";

		//SELECCIONAR estado a consultar

		echo "<tr>";
		echo "<td align=left colspan=2 class='texto3' >ESTADOS DEL REQUERIMIENTO:
			  <select name='west' onchange='enter()' ondblclick='enter()'>";
			if (isset ($west))
			{
				if ($west=='SIN EVALUAR')
				{
					echo "<option>SIN EVALUAR</option>";
					echo "<option>EN TRAMITE</option>";
					echo "<option>EN PROMOTORA</option>";
					echo "<option>RECHAZADO</option>";
					echo "<option>CERRADO</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='EN TRAMITE')
				{
					echo "<option>EN TRAMITE</option>";
					echo "<option>SIN EVALUAR</option>";
					echo "<option>EN PROMOTORA</option>";
					echo "<option>RECHAZADO</option>";
					echo "<option>CERRADO</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='EN PROMOTORA')
				{
					echo "<option>EN PROMOTORA</option>";
					echo "<option>SIN EVALUAR</option>";
					echo "<option>EN TRAMITE</option>";
					echo "<option>RECHAZADO</option>";
					echo "<option>CERRADO</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='RECHAZADO')
				{
					echo "<option>RECHAZADO</option>";
					echo "<option>SIN EVALUAR</option>";
					echo "<option>EN TRAMITE</option>";
					echo "<option>EN PROMOTORA</option>";
					echo "<option>CERRADO</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='CERRADO')
				{
					echo "<option>CERRADO</option>";
					echo "<option>SIN EVALUAR</option>";
					echo "<option>EN TRAMITE</option>";
					echo "<option>EN PROMOTORA</option>";
					echo "<option>RECHAZADO</option>";
					echo "<option>TODOS</option>";
				}
				if ($west=='TODOS')
				{
					echo "<option>TODOS</option>";
					echo "<option>SIN EVALUAR</option>";
					echo "<option>EN TRAMITE</option>";
					echo "<option>EN PROMOTORA</option>";
					echo "<option>RECHAZADO</option>";
					echo "<option>CERRADO</option>";
				}
			}
			else
			{
				echo "<option> </option>";
				echo "<option>SIN EVALUAR</option>";
				echo "<option>EN TRAMITE</option>";
				echo "<option>EN PROMOTORA</option>";
				echo "<option>RECHAZADO</option>";
				echo "<option>CERRADO</option>";
				echo "<option>TODOS</option>";
			}
		echo "</select></td></tr>";

		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

		echo "<tr><td align=center class='texto3' COLSPAN='2'>";
		echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> GENERAR REPORTE";
		echo "</td></tr>";
		echo "</table>";

	}
	//MUESTRA DE DATOS DEL REPORTE
	else
	{
		echo "<table  align=center width='80%'>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td>&nbsp;</td></tr>";
		echo "<tr><td><font size=2><B>Fecha: ".date('Y-m-d')."</B></font></td></tr>";
		echo "<tr><td><font size=2><B>REPORTE DE GESTION PARA ESTADOS DE REQUERIMIENTOS</B></font></td></tr>";

		echo "</tr><td align=right><A href='RepEstReq.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;west=".$west."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></td></tr>";
		echo "<tr><td><font size=2>Fecha inicial: ".$wfecini."</font></td></tr>";
		echo "<tr><td><font size=2>Fecha final: ".$wfecfin."</font></td></tr>";
		echo "<tr><td><font size=2>Estado: ".$west."</font></td></tr>";
		echo "</table></br>";

		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
		echo "<input type='HIDDEN' NAME= 'west' value='".$west."'>";
		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		/***********************************Consulto lo pedido ********************/

		//se busca en la tabla 22 el detalle de recibos pendientes por cuadrar en estado = on y entre el rango de fechas.

		$clase1="class='texto1'";
		$clase2="class='texto4'";

		switch($west)
		{
			case "SIN EVALUAR":
			$wcest= "S";
			break;
			case "EN TRAMITE":
			$wcest= "T";
			break;
			case "EN PROMOTORA":
			$wcest= "P";
			break;
			case "RECHAZADO":
			$wcest= "R";
			break;
			case "CERRADO":
			$wcest= "C";
			break;
			case "TODOS":
			$wcest= "A";
			break;
			default:
			$wcest= "A";
		}

		if($wcest=="A")
		{
			$q = " SELECT  sgrcsc,fecha_data,hora_data,sgrnom,sgrext,sgrres,sgrest ".
	     	 "   FROM  ".$wbasedato."_000122 "
			." 	WHERE  fecha_data between '".$wfecini."'"
			."    AND  '".$wfecfin."'"
			."  ORDER BY  fecha_data,hora_data,sgrcsc ";
		}
		else
		{
			$q = " SELECT  sgrcsc,fecha_data,hora_data,sgrnom,sgrext,sgrres,sgrest ".
	     	 "   FROM  ".$wbasedato."_000122 "
			." 	WHERE  fecha_data between '".$wfecini."'"
			."    AND  '".$wfecfin."'"
			."    AND  sgrest = '".$wcest."' "
			."  ORDER BY  fecha_data,hora_data,sgrcsc ";
		}

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

		$wtotreq = 0;
		$wtotsev = 0;
		$wtotpse = 0;
		$wtottra = 0;
		$wtotptr = 0;
		$wtotpro = 0;
		$wtotppr = 0;
		$wtotrec = 0;
		$wtotpre = 0;
		$wtotcer = 0;
		$wtotpce = 0;

		if ($num > 0)
		{
			echo "<table align=center border=0 width=90%>";
			echo "<th align=CENTER class='titulo2'>Requerimiento</th>";
			echo "<th align=CENTER class='titulo2'>Fecha de Grabacion</th>";
			echo "<th align=CENTER class='titulo2'>Hora de Grabacion</th>";
			echo "<th align=CENTER class='titulo2'>Nombre del Usuario</th>";
			echo "<th align=CENTER class='titulo2'>Extencion</th>";
			echo "<th align=CENTER class='titulo2'>Responsable del Requerimiento</th>";
			echo "<th align=CENTER class='titulo2'>Estado</th>";

			for ($j=0;$j<$num;$j++)
			{
	      		$row = mysql_fetch_array($err);

	      		switch($row[6])
				{
					case "S":
					$wdesest= "SIN EVALUAR";
					break;
					case "P":
					$wdesest= "EN PROMOTORA";
					break;
					case "T":
					$wdesest= "EN TRAMITE";
					break;
					case "R":
					$wdesest= "RECHAZADO";
					break;
					case "C":
					$wdesest= "CERRADO";
					break;
					default:
					$wdesest= "NO VALIDO";
				}

				if ($j%2==0)
               		$clase1="class='texto1'";
            	else
	           		$clase1="class='texto4'";

	           	if(($row[6]<>"R") and ($row[6]<>"C") and ($row[1] < $wfecha))
	           		$clase2="class='texto5'";
	           	else
	           		$clase2=$clase1;


				echo "<tr>";
				echo "<th align=center ".$clase1.">".$row[0]."</th>";
				echo "<th align=center ".$clase1.">".$row[1]."</th>";
				echo "<th align=center ".$clase1.">".$row[2]."</th>";
				echo "<th align=center ".$clase1.">".$row[3]."</th>";
				echo "<th align=center ".$clase1.">".$row[4]."</th>";
				echo "<th align=center ".$clase1.">".$row[5]."</th>";
				echo "<th align=center ".$clase2.">".$wdesest."</th>";
				echo "</tr>";

	     		$wtotreq=$wtotreq+1;
	     		if($row[6]=="S")
	     			$wtotsev=$wtotsev+1;
	     		if($row[6]=="T")
	     			$wtottra=$wtottra+1;
	     		if($row[6]=="P")
	     			$wtotpro=$wtotpro+1;
	     		if($row[6]=="R")
	     			$wtotrec=$wtotrec+1;
	     		if($row[6]=="C")
	     			$wtotcer=$wtotcer+1;
	  		}
    	}
		echo "</table>";

		if ($wtotreq==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=2 color='#000080' face='arial'><b>No se encontraron Requerimientos en el Estado y Rango de Fechas Seleccionados...</td><tr>";
		}
		else
		{
			echo "<table align=center border=0 width=90%>";

			$wtotptt=($wtotreq/$wtotreq)*100;
			$wtotpse=($wtotsev/$wtotreq)*100;
	     	$wtotptr=($wtottra/$wtotreq)*100;
	     	$wtotppr=($wtotpro/$wtotreq)*100;
	     	$wtotpre=($wtotrec/$wtotreq)*100;
	     	$wtotpce=($wtotcer/$wtotreq)*100;

	     	echo "<tr><th align=CENTER class='acumulado3' width=15%></th>";
	     	echo "<th align=CENTER class='acumulado3' width=8%>TOTAL </th>";
	     	echo "<th align=CENTER class='acumulado3' width=5%></th>";
	     	echo "<th align=CENTER class='acumulado3'>% PARTICIPACION </th></tr>";

			echo "<tr><th align=CENTER class='acumulado3' width=15%>TOTAL REQUERIMIENTOS: </th>";
			echo "<th align=CENTER class='acumulado4' width=8%>".number_format($wtotreq,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado4' width=5%></th>";
			echo "<th align=CENTER class='acumulado3'>".number_format($wtotptt,2,'.',',')."% </th></tr>";

			echo "<tr><th align=CENTER class='acumulado3' width=15%>TOTAL SIN EVALUAR: </th>";
			echo "<th align=CENTER class='acumulado4' width=8%>".number_format($wtotsev,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado4' width=5%></th>";
			echo "<th align=CENTER class='acumulado3'>".number_format($wtotpse,2,'.',',')."%</th></tr>";

			echo "<tr><th align=CENTER class='acumulado3' width=15%>TOTAL EN TRAMITE: </th>";
			echo "<th align=CENTER class='acumulado4' width=8%>".number_format($wtottra,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado4' width=5%></th>";
			echo "<th align=CENTER class='acumulado3'>".number_format($wtotptr,2,'.',',')."%</th></tr>";

			echo "<tr><th align=CENTER class='acumulado3' width=15%>TOTAL EN PROMOTORA: </th>";
			echo "<th align=CENTER class='acumulado4' width=8%>".number_format($wtotpro,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado4' width=5%></th>";
			echo "<th align=CENTER class='acumulado3' >".number_format($wtotppr,2,'.',',')."%</th></tr>";

			echo "<tr><th align=CENTER class='acumulado3' width=15%>TOTAL RECHAZADOS: </th>";
			echo "<th align=CENTER class='acumulado4' width=8%>".number_format($wtotrec,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado4' width=5%></th>";
			echo "<th align=CENTER class='acumulado3' >".number_format($wtotpre,2,'.',',')."%</th></tr>";

			echo "<tr><th align=CENTER class='acumulado3' width=15%>TOTAL CERRADOS: </th>";
			echo "<th align=CENTER class='acumulado4' width=8%>".number_format($wtotcer,0,'.',',')."</th>";
			echo "<th align=CENTER class='acumulado4' width=5%></th>";
			echo "<th align=CENTER class='acumulado3' >".number_format($wtotpce,2,'.',',')."%</th></tr>";
		}
		echo "</table>";
		echo "</br><center><A href='RepEstReq.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;west=".$west."&amp;wbasedato=".$wbd."&amp;bandera='1'>VOLVER</A></center>";
	}
}
?>
</body>
</html>