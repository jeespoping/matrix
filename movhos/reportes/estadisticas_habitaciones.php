<html>
<head>
  <title>ESTADISTICAS CENTRAL DE HABITACIONES</title>

	<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet' />

	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>

	<script type='text/javascript'>

	$.datepicker.regional['esp'] = {
        closeText: 'Cerrar',
        prevText: 'Antes',
        nextText: 'Despues',
        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        changeMonth: true,
        yearRange: '-10:+10'
        };
$.datepicker.setDefaults($.datepicker.regional['esp']);

		function cargaToolTip()
		{
			var cont1 = 1;
			while(document.getElementById('wssu'+cont1))
			{
				 $('#wssu'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 cont1++;
			}
			cont1 = 1;
			while(document.getElementById('wse'+cont1))
			{
				 $('#wse'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 cont1++;
			}
		}

		//Redirecciona a la pagina inicial
		function inicioReporte(wemp_pmla,wfecha_i,wfecha_f,whora_i,whora_f)
		{
			document.location.href='estadisticas_habitaciones.php?wemp_pmla='+wemp_pmla+'&wfecha_i='+wfecha_i+'&wfecha_f='+wfecha_f+'&whora_i='+whora_i+'&whora_f='+whora_f+'&bandera=1';
		}

		function cerrarVentana()
		 {
		  window.close()
		 }

		 window.onload = function() { cargaToolTip(); }

		 $(document).ready(function() {

			$("#wfecha_i").datepicker({
			  showOn: "button",
			  buttonImage: "../../images/medical/root/calendar.gif",
			  buttonImageOnly: true,
			  maxDate:"+2Y"
			});

			$("#wfecha_f").datepicker({
			  showOn: "button",
			  buttonImage: "../../images/medical/root/calendar.gif",
			  buttonImageOnly: true,
			  maxDate:"+2Y"
			});

			});

	</script>
</head>
<body>
<?php
include_once("conex.php");

/***************************************************
*	        ESTADISTICAS DE CAMILLEROS           *
*				CONEX, FREE => OK				 *
**************************************************/

/***************************************************************************************************************************************
 * Ultima actualizacion:
 //======================================================================================================================================\\ 
 * 2017-09-06 (Jonatan Lopez) : Se omite en la estadistica las habitaciones marcadas como mantenimiento, campo Sgeman activo en 
								la tabla movhos_000024 y codigo de empleado 052.
 //======================================================================================================================================\\
 * 2017-02-10 (Arleyda Insignares): Se agrega condición con el campo ccoemp en caso de que el Query utilice la tabla costosyp_000005.
//======================================================================================================================================\\
 * 2015-08-27(Juan C Hdez):  Se modifica que en los querys no se tome en cuenta la tabla mv_20 de las habitaciones,
                             porque cuando se inactivan habitaciones, las etadisticas cambian, se tomara entonces la 
                             información de la tabla mv_67 que es el historico de las habitaciones y alli se encuentra
                             también el centro de costo que es la razón de utilizar la tabla de habitaciones.							 
//======================================================================================================================================\\
 * 2014-12-03(Camilo ZZ):  se modificó el script para que omita las habitaciones que son cubiculos
//======================================================================================================================================\\
 * 2013-08-01: (Jonatan Lopez) Se agrega este filtro Sgemed = 'on' a la consulta ppal,
			   para que no tenga en cuenta empleados no medibles, ademas se registra
			   log de informes generados con el dato de quien lo genero, fecha, hora,
			   y quienes son los no medibles en el momento del reporte.
 ****************************************************************************************************
 * 2011-02-28:	(Mario Cadavid). Adaptación de query's para que se tenga en cuenta los servicios	*
 * 				que se hacen en días diferentes desde su solicitud hasta su alistamiento. 			*
 * 				Aplicación de css al diseño.											 			*
 ***************************************************************************************************/

// Función que retorna los segundos recibidos en formato hh:mm:ss
function segundosTiempo($segundos){
	$minutos=$segundos/60;
	$horas=floor($minutos/60);
	$minutos2=$minutos%60;
	$segundos_2=$segundos%60%60%60;
	if($minutos2<10)$minutos2='0'.$minutos2;
	if($segundos_2<10)$segundos_2='0'.$segundos_2;

	if($segundos<60){ /* segundos */
	$resultado= round($segundos);
	}elseif($segundos>60 && $segundos<3600){/* minutos */
	$resultado= $minutos2.':'.$segundos_2;
	}else{/* horas */
	$resultado= $horas.':'.$minutos2.':'.$segundos_2;
	}
	return $resultado;
}
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	

    

    include_once("root/comun.php");

	$key = substr($user,2,strlen($user));
	if (strpos($user,"-") > 0)
	$wusuario = substr($user,(strpos($user,"-")+1),strlen($user));


	// INICIO DEL FORMULARIO
	echo "<form action='estadisticas_habitaciones.php' method=post>";

	$wfecha=date("Y-m-d");
	$whora = (string)date("H:i:s");
	$wactualiz='2017-09-06';

	encabezado("ESTADISTICAS CENTRAL DE HABITACIONES",$wactualiz, "clinica");

    if (!isset($wfecha_i) or !isset($wfecha_f) or !isset($whora_i) or !isset($whora_i) or !isset($resultado))
	{
		//Petición de ingreso de parametros
        echo '<center><h3 class="seccion1"><b>Ingrese las Fechas y Horas a consultar:</b></h3></center>';
        //echo '<center><span class="seccion1"><b>Ingrese las Fechas y Horas a consultar:</b></span></center>';

		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{
 			$wfecha_i=$wfecha;
  			$wfecha_f=$wfecha;
  			$whora_i="00:00:00";
  			$whora_f="23:59:59";
		}

		echo '<table align=center cellspacing="2" >';
		echo "<tr class=seccion1>";
        echo "<td align=center height='61px' width='140px'><b>Fecha Inicial</b><br>";
  		echo "<INPUT TYPE='text' NAME='wfecha_i' id='wfecha_i' value='".$wfecha."' size=11 readonly class='textoNormal'>";
		//campoFechaDefecto("wfecha_i", $wfecha_i);
        echo "</td>";
      	echo "<td align=center height='61px' width='140px'><b>Fecha Final</b><br>";
  		//campoFechaDefecto("wfecha_f", $wfecha_f);
		echo "<INPUT TYPE='text' NAME='wfecha_f' id='wfecha_f' value='".$wfecha."' size=11 readonly class='textoNormal'>";

	    echo "</td>";
	    echo "</tr>";

	    echo "<tr class=seccion1>";
	    echo "<td align=center height='61px' width='140px'><b>Hora Inicial:<br></b><INPUT TYPE='text' NAME='whora_i' VALUE='".$whora_i."' size=10></td>";
		echo "<td align=center height='61px' width='140px'><b>Hora Final:<br></b><INPUT TYPE='text' NAME='whora_f' VALUE='".$whora_f."' size=10></td>";
		echo "</tr></center>";


  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
		echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
		echo "<tr>";
		echo "<td align=center colspan=5><br /><input type='submit' value='Consultar' id='searchsubmit'></td>";
		echo "</tr>";
	}
	else
	{
		//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
		$q = "  SELECT detapl, detval "
			."  FROM root_000050, root_000051 "
			."  WHERE empcod = '".$wemp_pmla."'"
			."    AND empest = 'on' "
			."    AND empcod = detemp ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		if ($num > 0 )
		{
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);

				if ($row[0] == "cenmez")
				$wcenmez=$row[1];

				if ($row[0] == "afinidad")
				$wafinidad=$row[1];

				if ($row[0] == "movhos")
				$wbasedato=$row[1];

				if ($row[0] == "tabcco")
				$wtabcco=$row[1];
			}
			echo '<table align=center>';
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center><b>Per&iacuteodo de Consulta: </b>".$wfecha_i." al ".$wfecha_f."</td></tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center><b>Rango de Horas: </b>".$whora_i." a ".$whora_f."</b></td></tr>";
			echo '</table>';
			echo "<br>";

	 
			echo '<table align=center>';
			//
			// QUERY: CANTIDAD DE SERVICIOS EN ** EL PERIODO **
			
			if ($wtabcco == 'costosyp_000005'){
				
				$q=   "   SELECT count(*) "
					 ."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
					 ."   WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND movhal       BETWEEN '".$whora_i."' AND '".$whora_f."'"
					 ."     AND movhdi      != '00:00:00' "
					 ."     AND movhem      != '00:00:00' "
					 ."     AND movhab       = Habcod "
					 ."     AND B.fecha_data = A.movfal "
					 ."     AND habcco       = Ccocod "
					 ."     AND A.movemp     = Sgecod "
					 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
					 ."     AND Ccoemp       = '".$wemp_pmla."' ";
			}
			else{
				
				$q=   "   SELECT count(*) "
					 ."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
					 ."   WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND movhal       BETWEEN '".$whora_i."' AND '".$whora_f."'"
					 ."     AND movhdi      != '00:00:00' "
					 ."     AND movhem      != '00:00:00' "
					 ."     AND movhab       = Habcod "
					 ."     AND B.fecha_data = A.movfal "
					 ."     AND habcco       = Ccocod "
					 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
					 ."     AND A.movemp     = Sgecod ";
			}	 
			
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$wtotser=$row[0];
				echo "<tr class=fila1>";
				echo "<td colspan=2><font size=4><b>Total servicios en el per&iacute;odo (No incluye las no terminadas): "."</b></font></td>";
				echo "<td colspan=2 align=center><font size=4>".number_format($row[0],0,'.',',')."</font></td>";
				echo "<td align=center><A href='consulta_de_servicios_movhos.php?wfecha_i=".$wfecha_i."&wfecha_f=".$wfecha_f."&whora_i=".$whora_i."&whora_f=".$whora_f."&whora_i=".$whora_i."&whora_f=".$whora_f."&wemp_pmla=".$wemp_pmla."&wser=* - Todos' TARGET='_blank'> Detallar</A></td>";
				echo "</tr>";
			}

			
			//
			// QUERY: PROMEDIO DE LLEGADA A LOS SERVICIOS
			//
			$q = "   SELECT SUM(TIMESTAMPDIFF(SECOND,CONCAT(A.Movfal,' ',A.Movhal),CONCAT(A.Movfdi,' ',A.movhdi))), "
				."          SUM(TIMESTAMPDIFF(SECOND,CONCAT(A.Movfal,' ',A.Movhal),CONCAT(A.Movfec,' ',A.movhem))), "
				."          SUM(TIMESTAMPDIFF(SECOND,CONCAT(A.Movfec,' ',A.Movhem),CONCAT(A.Movfdi,' ',A.movhdi))) "
				."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				."   WHERE A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				."     AND A.movhal  BETWEEN '".$whora_i."' AND '".$whora_f."'"
				."     AND A.movhem != '00:00:00' "
				."     AND A.movhdi != '00:00:00' "
				."     AND A.movemp  = Sgecod "
				."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') ";
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				$wproseg = $row[0]/$wtotser;
				$wpromin = $wproseg/60;

				//$wprom ya esta en minutos, ahora lo divido por el numero de servicios
				$wproser=number_format($wpromin,2,'.','');

				echo "<tr class=fila2>";
				echo "<td colspan=2><b>Promedio de alistamiento de habitaciones (en minutos): </b></td>";
				echo "<td colspan=2 align=center>".$wproser."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";

				$wproseg2 = $row[1]/$wtotser;
				$wpromin = $wproseg2/60;

				//$wprom ya esta en minutos, ahora lo divido por el numero de servicios
				$wproser2=number_format($wpromin,2,'.','');

				echo "<tr class=fila1>";
				echo "<td colspan=2><b>Promedio de asignacion de empleados (en minutos): </b></td>";
				echo "<td colspan=2 align=center>".$wproser2."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";

				$wproseg3 = $row[2]/$wtotser;
				$wpromin = $wproseg3/60;

				//$wprom ya esta en minutos, ahora lo divido por el numero de servicios
				$wproser3=number_format($wpromin,2,'.','');

				echo "<tr class=fila2>";
				echo "<td colspan=2><b>Promedio de alistamiento desde asignacion del empleado (en minutos): </b></td>";
				echo "<td colspan=2 align=center>".$wproser3."</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			}

			//
			// QUERY: SERVICIO QUE MAS TARDO
			//
			$q = "   SELECT A.Id, MAX(TIMESTAMPDIFF(SECOND,CONCAT(A.Movfal,' ',A.Movhal),CONCAT(A.Movfdi,' ',A.movhdi))) "
				."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				."   WHERE A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				."     AND A.movhal  BETWEEN '".$whora_i."' AND '".$whora_f."'"
				."     AND A.movhem != '00:00:00' "
				."     AND A.movhdi != '00:00:00' "
				."     AND A.movemp  = B.Sgecod "
				."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
				."   GROUP BY A.id "
				."   ORDER BY 2 desc ";
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				echo "<tr class=fila1>";
				echo "<td colspan=2><b>Solicitud que tardo MAS en ser atendida (en minutos): <b></td>";
				$wsegundos=$row[1];
				$wminutos=$wsegundos/60;
				echo "<td colspan=2 align=center>".number_format($wminutos,2,'.','')."</td>";
				echo "<td align=center><A href='consulta_de_servicios_movhos.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;whora_i=".$whora_i."&amp;whora_f=".$whora_f."&amp;whora_i=".$whora_i."&amp;whora_f=".$whora_f."&amp;wemp_pmla=".$wemp_pmla."&amp;wid=".$row[0]."&amp;wser=* - Todos&amp;word=S"."' TARGET='_blank'> Detallar</A></td>";
				echo "</tr>";
			}


			//
			// QUERY: SERVICIO QUE MENOS TARDO
			//
			$q=  "  SELECT A.id, MIN(TIMESTAMPDIFF(SECOND,CONCAT(A.Movfal,' ',A.Movhal),CONCAT(A.Movfdi,' ',A.movhdi))) "
				."    FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				."   WHERE A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				."     AND A.movhal  BETWEEN '".$whora_i."' AND '".$whora_f."'"
				."     AND A.movhem != '00:00:00' "
				."     AND A.movhdi != '00:00:00' "
				."     AND A.movemp  = B.Sgecod "
				."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
				."   GROUP BY A.id "
				."   ORDER BY 2 asc ";
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);

				echo "<tr class=fila2>";
				echo "<td colspan=2><b>Solicitud que tardo MENOS en ser atendida (en minutos): <b></td>";
				$wsegundos=$row[1];
				$wminutos=$wsegundos/60;
				echo "<td colspan=2 align=center>".number_format($wminutos,2,'.','')."</td>";
				echo "<td align=center><A href='consulta_de_servicios_movhos.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;wemp_pmla=".$wemp_pmla."&amp;wid=".$row[0]."' TARGET='_blank'> Detallar</A></font></td>";
				echo "</tr>";
			}

			//
			// QUERY: CANTIDAD DE SERVICIOS ASIGNADOS PERO NO ATENDIDOS ** EN EL PERIODO **
			//
			$q=  "   SELECT count(*) "
				."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				."   WHERE A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				."     AND A.movhal  BETWEEN '".$whora_i."' AND '".$whora_f."'"
				."     AND A.movhem != '00:00:00' "
				."     AND A.movhdi  = '00:00:00' "
				."     AND A.movemp  = B.Sgecod "
				."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') ";
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				echo "<tr class=fila1>";
				echo "<td colspan=2><b>Total servicios asignados pero no atendidos (sin hora de llegada): "."<b></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			}


			//
			// QUERY: CANTIDAD DE SERVICIOS EN ** MENOS O IGUAL AL PROMEDIO **
			//
			$q=   "   SELECT count(*) "
				 ."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				 ."   WHERE (TIMESTAMPDIFF(SECOND,CONCAT(A.Movfal,' ',A.Movhal),CONCAT(A.Movfdi,' ',A.movhdi))) <= ".$wproseg
				 ."     AND A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				 ."     AND A.movhal  BETWEEN '".$whora_i."' AND '".$whora_f."'"
				 ."     AND A.movhem != '00:00:00' "
			  	 ."     AND A.movhdi != '00:00:00' "
				 ."     AND A.movemp  = B.Sgecod "
				 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') ";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				echo "<tr class=fila2>";
				echo "<td colspan=2><b>Total servicios respondidos en menos de (".$wproser.") minutos: <b></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			}

			//
			// QUERY: CANTIDAD DE SERVICIOS EN ** MAS DEL PROMEDIO **
			//
			$q=   "   SELECT count(*) "
				 ."   FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				 ."   WHERE (TIMESTAMPDIFF(SECOND,CONCAT(A.Movfal,' ',A.Movhal),CONCAT(A.Movfdi,' ',A.movhdi))) > ".$wproseg
				 ."     AND A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				 ."     AND A.movhal  BETWEEN '".$whora_i."' AND '".$whora_f."'"
				 ."     AND A.movhem != '00:00:00' "
				 ."     AND A.movhdi != '00:00:00' "
				 ."     AND A.movemp  = B.Sgecod "
				 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') ";
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				$row = mysql_fetch_array($res);
				echo "<tr class=fila1>";
				echo "<td colspan=2><font size=2><b>Total servicios respondidos en mas de (".$wproser.") minutos: <b></font></td>";
				echo "<td colspan=1 align=RIGHT>".number_format($row[0],0,'.',',')."</td>";
				echo "<td colspan=1 align=RIGHT>".number_format(($row[0]/$wtotser)*100,2,'.','')." %</td>";
				echo "<td>&nbsp</td>";
				echo "</tr>";
			}

			echo "<tr></tr>";
			//
			// QUERY: CANTIDAD DE SERVICIOS ** POR HABITACION ** EN EL PERIODO
			//

            if ($wtabcco == 'costosyp_000005'){

				$q=   "  SELECT Movemp, COUNT(*), "
					 ."			SUM(TIMESTAMPDIFF(SECOND,CONCAT(Movfal,' ',Movhal),CONCAT(A.Movfdi,' ',movhdi))), Sgenom  "
					 ."    FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024, ".$wbasedato."_000067 B, ".$wtabcco." "
					 ."   WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND movhal       BETWEEN '".$whora_i."' AND '".$whora_f."'"
					 ."     AND movhem      != '00:00:00' "
					 ."     AND movhdi      != '00:00:00' "
					 ."     AND B.fecha_data = A.movfal "
					 ."     AND movemp       = Sgecod  "
					 ."     AND movhab       = Habcod  "
					 ."     AND habcco       = Ccocod  "
					 ."     AND Ccoemp       = '".$wemp_pmla."' "
					 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
					 ."   GROUP BY 1 "
					 ."   ORDER BY 2 desc ";

            }
            else{

				$q=   "  SELECT Movemp, COUNT(*), "
					 ."			SUM(TIMESTAMPDIFF(SECOND,CONCAT(Movfal,' ',Movhal),CONCAT(A.Movfdi,' ',movhdi))), Sgenom  "
					 ."    FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024, ".$wbasedato."_000067 B, ".$wtabcco." "
					 ."   WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."     AND movhal       BETWEEN '".$whora_i."' AND '".$whora_f."'"
					 ."     AND movhem      != '00:00:00' "
					 ."     AND movhdi      != '00:00:00' "
					 ."     AND B.fecha_data = A.movfal "
					 ."     AND movemp       = Sgecod  "
					 ."     AND movhab       = Habcod  "
					 ."     AND habcco       = Ccocod  "
					 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
					 ."   GROUP BY 1 "
					 ."   ORDER BY 2 desc ";

			}	 
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{

				//Registro log de informes generados con el dato de quien lo genero, fecha, hora, y quienes son los no medibles en el momento del reporte. (Jonatan 01 Agosto 2013)
				$wno_medibles = '';

				$q_med =  "   SELECT Sgecod "
						 ."     FROM ".$wbasedato."_000024"
						 ."    WHERE Sgemed = 'off'";
				$res_med = mysql_query($q_med,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
				$num_med = mysql_num_rows($res_med);

				if ($num_med > 0)
					{
					while($row_med = mysql_fetch_array($res_med))
							{
								$wno_medibles .= $row_med['Sgecod'].",";
							}

					$query = "INSERT ".$wbasedato."_000155 ( Medico, Fecha_data, Hora_data, Nmeusu, Nmeemp, Nmeest, Seguridad )
							VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wusuario."','".$wno_medibles."','on', 'C-".$wusuario."')";
					$err1 = mysql_query($query,$conex) or die("error insertando log : ".mysql_errno().":".mysql_error());

					}

				echo "<th colspan=5 class=seccion1>";
				echo "<font size=3><b>Servicios por empleado: </font></b>";
				echo "</th>";
				echo "<tr class=encabezadoTabla>";
				echo "<th>Empleado</th>";
				echo "<th>Cantidad</th>";
				echo "<th>% de participaci&oacute;n</th>";
				echo "<th colspan='2'>Tiempo Promedio<br>en Minutos</th>";
				echo "</tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					if (is_integer($i / 2))
					$wclass = "fila1";
					else
					$wclass = "fila2";

					echo "<tr class=".$wclass.">";
					echo "<td colspan=1>".$row[0]."-".$row[3]."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'.',',')."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'.','')." %</td>";
					$prom_segundos=$row[2]/$row[1];
					$prom_minutos=$prom_segundos/60;
					echo "<td colspan='2' align=RIGHT><span id='wse".$i."' title='En horas: ".segundosTiempo($prom_segundos)."'>".number_format($prom_minutos,2,'.',',')."</span></td>";         //Tiempo promedio por solicitud, por servicio
					echo "</tr>";
					$wtotal=$wtotal+$row[1];
				}
				echo "<tr class=encabezadoTabla>";
				echo "<th>Total servicios: </th>";
				echo "<th colspan=1 align=RIGHT>".number_format($wtotal,0,'.',',')."</th>";
				echo "<th colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'.','')." %</th>";
				echo "<th colspan=2 align=RIGHT>&nbsp</th>";
				echo "</tr>";

			}

			echo "<tr></tr>";
			//
			// QUERY: CANTIDAD DE SOLICITUDES ** POR SERVICIO ** EN EL PERIODO
			
            if ($wtabcco == 'costosyp_000005'){

				$q=   "   SELECT movhab, COUNT(*), "
					 ."			 SUM(TIMESTAMPDIFF(SECOND,CONCAT(Movfal,' ',Movhal),CONCAT(A.Movfdi,' ',movhdi))), "
					 ."			 habcco, Cconom "
					 ."     FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
					 ."    WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."      AND movhal       BETWEEN '".$whora_i."' AND '".$whora_f."'"
					 ."      AND movhem      != '00:00:00' "
					 ."      AND movhdi      != '00:00:00' "
					 ."      AND B.fecha_data = A.movfal "
					 ."      AND movhab       = Habcod  "
					 ."      AND habcco       = Ccocod  "
					 ."      AND movemp       = Sgecod  "
					 ."     AND  Ccoemp       = '".$wemp_pmla."' "
					 ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
					 ."    GROUP BY 4 "
					 ."    ORDER BY 2 desc ";

			}	
			else{

				$q=   "   SELECT movhab, COUNT(*), "
					 ."			 SUM(TIMESTAMPDIFF(SECOND,CONCAT(Movfal,' ',Movhal),CONCAT(A.Movfdi,' ',movhdi))), "
					 ."			 habcco, Cconom "
					 ."     FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
					 ."    WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
					 ."      AND movhal       BETWEEN '".$whora_i."' AND '".$whora_f."'"
					 ."      AND movhem      != '00:00:00' "
					 ."      AND movhdi      != '00:00:00' "
					 ."      AND B.fecha_data = A.movfal "
					 ."      AND movhab       = Habcod  "
					 ."      AND habcco       = Ccocod  "
					 ."      AND movemp       = Sgecod"
					 ."      AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
					 ."    GROUP BY 4 "
					 ."    ORDER BY 2 desc ";

			}

			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);

			if ($num > 0)
			{
				echo "<tr class=seccion1>";
				echo "<th colspan=5>";
				echo "<font size=3><b>Solicitudes por Servicio o Unidad: </font></b>";
				echo "</th>";
				echo "</tr>";

				echo "<tr class=encabezadoTabla>";
				echo "<th>Servicio</th>";
				echo "<th>Cantidad</th>";
				echo "<th>% de participaci&oacute;n</th>";
				echo "<th>Tiempo Promedio<br>en Minutos</th>";
				echo "<th>&nbsp</th>";
				echo "</tr>";
				$wtotal=0;
				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					if (is_integer($i / 2))
					$wclass = "fila1";
					else
					$wclass = "fila2";

					echo "<tr class=".$wclass.">";
					echo "<td colspan=1>".$row[3]."-".$row[4]."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format($row[1],0,'.',',')."</td>";
					echo "<td colspan=1 align=RIGHT>".number_format(($row[1]/$wtotser)*100,2,'.','')." %</td>";
					$prom_segundos=$row[2]/$row[1];
					$prom_minutos=$prom_segundos/60;
					echo "<td colspan=1 align=RIGHT><span id='wssu".$i."' title='En horas: ".segundosTiempo($prom_segundos)."'>".number_format($prom_minutos,2,'.',',')."</span></td>";         //Tiempo promedio por solicitud, por servicio
					echo "<td align=center><font size=2><A href='consulta_de_servicios_movhos.php?wfecha_i=".$wfecha_i."&amp;wfecha_f=".$wfecha_f."&amp;whora_i=".$whora_i."&amp;whora_f=".$whora_f."&amp;wemp_pmla=".$wemp_pmla."&amp;wser=".$row[3]."&amp;word=S"."' TARGET='_blank'> Detallar</A></font></td>";
					$wtotal=$wtotal+$row[1];
				}

				echo "<tr class=encabezadoTabla>";
				echo "<th>Total servicios: </font></th>";
				echo "<th colspan=1 align=RIGHT>".number_format($wtotal,0,'.',',')."</th>";
				echo "<th colspan=1 align=RIGHT>".number_format(($wtotal/$wtotser)*100,2,'.','')." %</th>";
				echo "<th colspan=2 align=RIGHT>&nbsp</th>";
				echo "</tr>";
				echo "<tr>";
				echo "<th></th>";
				echo "<th colspan=1 align=RIGHT></th>";
				echo "<th colspan=1 align=RIGHT></th>";
				echo "<th colspan=2 align=RIGHT><font size='1' >Fecha y hora actual: ".$wfecha." ".$whora."</font></th>";
				echo "</tr>";
			}

		}
		else
		{
			echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
		}
	}
    echo "</center></table>";
    echo "</form>";

		//Botones "Retornar" y "Cerrar ventana"
    if (!isset($wfecha_i) or !isset($wfecha_f) or !isset($whora_i) or !isset($whora_i) or !isset($resultado))
		{
			echo "<p align='center'><input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";
		}
		else
		{
			echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$whora_i\",\"$whora_f\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";
		}

}
include_once("free.php");
?>
</body>
</html>
