<html>
<head>
  <title>ESTADISTICAS CENTRAL DE HABITACIONES</title>

	<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet' />

	<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>

	<script type='text/javascript'>
		function cargaToolTip()
		{
			var cont1 = 1;
			while(document.getElementById('wtt'+cont1))
			{
				 $('#wtt'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 cont1++;
			}
			cont1 = 1;
			while(document.getElementById('wtc'+cont1))
			{
				 $('#wtc'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 cont1++;
			}
			cont1 = 1;
			while(document.getElementById('wta'+cont1))
			{
				 $('#wta'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				 cont1++;
			}
		}

		//Redirecciona a la pagina inicial
		function inicioReporte(wser,wemp_pmla,wfecha_i,wfecha_f,whora_i,whora_f)
		{
			document.location.href='consulta_de_servicios_movhos.php?wemp_pmla='+wemp_pmla+'&w_ser='+wser+'&w_fecha_i='+wfecha_i+'&w_fecha_f='+wfecha_f+'&w_hora_i='+whora_i+'&w_hora_f='+whora_f+'&bandera=1';
		}
		
		function cerrarVentana()
		 {
		  window.close()		  
		 }

		 window.onload = function() { cargaToolTip(); }
	</script>	

</head>
<body>
<?php
include_once("conex.php");

/***************************************************
*	          CONSULTA DE SERVICIOS              *
*				CONEX, FREE => OK				 *
**************************************************/

/****************************************************************************************************
 * Ultima actualizacion:
 *  2017-09-06 (Jonatan): Se cambia el campo Fecha_data de la tabla movhos25 por el campo movfec (nuevo) en la misma tabla.
 * * 2015-08-27(Juan C Hdez):  Se modifica que en los querys no se tome en cuenta la tabla mv_20 de las habitaciones,
                porque cuando se inactivan habitaciones, las etadisticas cambian, se tomara entonces la 
                información de la tabla mv_67 que es el historico de las habitaciones y alli se encuentra
                también el centro de costo que es la razón de utilizar la tabla de habitaciones.
 * 2013-08-05:  (Jonatan Lopez) Se agrega filtro a las consultas para que solo muestre la informacion
				de los empleados medibles, campo Sgemed = 'on' en la tabla movhos_000024.
 * 2011-02-28:	(Mario Cadavid). Adaptación de query's para que se tenga en cuenta los servicios	*
 * 				que se hacen en días diferentes desde su solicitud hasta su alistamiento. 			*
 * 				Aplicación de css al diseño.											 			*
 * 2011-03-16:  (Mario Cadavid). Se corrigió la recepción de variables para mostrar formulario 		*
 * 				de consulta, también se adicionó botón "Retornar" con las variables de consulta	    *
 *
 * 2012-06-21:  Se agregan las consultas consultarCentroCostos y dibujarSelect que listan los centros*
 *              de costos de un grupo dado en orden alfabetico y dibuja el select con esos centros   *
 *              de costo respectivamente Viviana Rodas
 *																									*
 ***************************************************************************************************/

// Función que retorna la cantidad de minutos según una hora en formato hh:mm:ss
function tiempoNumero($tiempo) 
{
	$hhmmss = explode(':',$tiempo);
	$numero = ($hhmmss['0']*60)+($hhmmss['1'])+($hhmmss['2']/60);
	return $numero;
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


	//
	// INICIO DEL FORMULARIO
	//
	
	echo "<form action='consulta_de_servicios_movhos.php' method=post>";

	$wfecha=date("Y-m-d");
	$hora = (string)date("H:i:s");
	$wactualiz='Agosto 27 de 2015';

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
		// $wtabcco -> 'costosyp_000005';
		encabezado("CONSULTA DE SERVICIOS DE CENTRAL DE HABITACIONES",$wactualiz, "clinica");
		

		if ((!isset($wfecha_i) or !isset($wfecha_f) or !isset($wser)) and !isset($wid))
		{
			

			if (isset ($bandera))
			{  			
				$wfecha_i=$w_fecha_i;
				$wfecha_f=$w_fecha_f;
				$whora_i=$w_hora_i;
				$whora_f=$w_hora_f;
				$wser= explode("-",$w_ser);
			}
			else
			{
				$wfecha_i=$wfecha;
				$wfecha_f=$wfecha;
				$whora_i="00:00:00";
				$whora_f="23:59:59";
				$wser="%";
			}
			
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			
			$cco="Ccohos";
			$sub="off";
			$tod="Todos";
			//$cco=" ";
			$ipod="off";
			$centrosCostos = consultaCentrosCostos($cco);
			echo "<table align='center' border=0 >";
			$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod, "wser");
					
			echo $dib;
			echo "</table>";
						  
			echo '<table align=center cellspacing="2" >';
			echo "<tr class=seccion1>";
			echo "<td align=center height='61px' width='140px'><b>Fecha Inicial</b><br>";
			campoFechaDefecto("wfecha_i", $wfecha_i);
			echo "</td>";
			echo "<td align=center height='61px' width='140px'><b>Fecha Final</b><br>";
			campoFechaDefecto("wfecha_f", $wfecha_f);
			echo "</td>";
			echo "</tr>";
			
			echo "<tr class=seccion1>";
			echo "<td align=center height='61px' width='140px'><b>Hora Inicial:<br></b><INPUT TYPE='text' NAME='whora_i' VALUE='".$whora_i."' size=10></td>";
			echo "<td align=center height='61px' width='140px'><b>Hora Final:<br></b><INPUT TYPE='text' NAME='whora_f' VALUE='".$whora_f."' size=10></td>";
			echo "</tr></center>";
			
		    echo "<br>";
		    
			echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
			echo "<input type='HIDDEN' name='wcons' value='1'>";
			
			echo "<table align=center >";
			echo "<tr >";
			
			
			echo "<td align=center colspan=5><input type='submit' value='Consultar'></td>";
			echo "</tr>";
			echo "</table>";
		}
		else
		{

		//
		// QUERY:
		//
		
			if (!isset($wser))
			   {
				$wser="%-Todos";
			    $wser1=explode("-",$wser);
			    $wserv=$wser1[0];
			   }
			elseif ($wser=="%-Todos")
			   {
			    $wser1=explode("-",$wser);
			    $wserv=$wser1[0];
		       } 
		    else
				$wser1=explode("-",$wser);
			    $wserv=$wser1[0];
		        //$wserv=$wser; 
				
			if (isset($whora_i))
			   {
			    //$wrangohora=" AND A.Hora_data BETWEEN '".$whora_i."' AND '".$whora_f."' ";
			    $wrangohora=" AND A.movhal BETWEEN '".$whora_i."' AND '".$whora_f."' ";
		       } 
			  else
			     $wrangohora=" ";

			if (isset($wid))
			   $q = "  SELECT * "
				   ."    FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 B "
				   ."   WHERE Movfal   BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
				   ."     AND A.id     = ".$wid
				   ."     AND A.movemp = Sgecod "
				   .$wrangohora;
			  else
				 {

					  if  ($wtabcco == 'costosyp_000005'){
					  	   $q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal "
						      ."    FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 C, ".$wtabcco.", ".$wbasedato."_000024 B "
							  ."   WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
							  ."     AND Ccocod       LIKE '".trim($wserv)."'"
							  ."     AND Ccocod       = Habcco "
							  ."     AND Habcod       = movhab "
							  ."     AND C.fecha_data = A.movfal "
							  ."     AND A.movemp     = Sgecod "
							  ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
							  ."     AND ".$wtabcco.".Ccoemp = '".$wemp_pmla."' "
							  .$wrangohora;
					  }
					  else{ 	
						  $q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal "
						      ."    FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 C, ".$wtabcco.", ".$wbasedato."_000024 B "
							  ."   WHERE A.Movfal     BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
							  ."     AND Ccocod       LIKE '".trim($wserv)."'"
							  ."     AND Ccocod       = Habcco "
							  ."     AND Habcod       = movhab "
							  ."     AND C.fecha_data = A.movfal "
							  ."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
							  ."     AND A.movemp     = Sgecod "
							  .$wrangohora;
					  }
				 }
			// En esta consulta se muestra la hab302 entre dic 1 y 3
			//echo $q;
			$res = mysql_query($q,$conex);  //or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			$col = mysql_num_fields($res);

			
			echo '<table align=center>';
			
			if (isset($whora_i))
			   {
				echo "<tr class=seccion1>";
			    echo "<td align=center><b>Período de Consulta: </b>".$wfecha_i."&nbsp;<b>al</b>&nbsp;".$wfecha_f."</td>";
			    echo "</tr>";
			    echo "<tr class=seccion1>";
			    echo "<td align=center><b>Rango de Horas: </b>".$whora_i."&nbsp;<b>al</b>&nbsp;".$whora_f."</td>";
			    echo "</tr>";
		       } 
			else
			   {
				 echo "<tr class=seccion1>";
				 echo "<td align=center><b>Período de Consulta: </b>".$wfecha_i."<b>al</b> ".$wfecha_f."</td>";
				 echo "</tr>";
			   } 
			
			if (!isset($wid) and isset($wser) and ($wser != ""))
			   {
			    echo "<tr class=seccion1>"; 		
				echo "<td align=center><b>Servicio: </b>".$wser. "</td>";
				echo "</tr>";
			   }

			echo "</table>";   
			   
			
			echo '<table align=center>';

			echo "<tr>";
			echo "<td align=center colspan=".$col.">&nbsp</td>";
			echo "</tr>";

			if (!isset($wid))
			   if ($wser == '* - Todos')
				   $wserv='%';
				
			//
			// QUERY:
			//
			if (isset($wid))   //Si esta setiado el id busco por este, si no por el servicio u origen
			{
				if ($wtabcco == 'costosyp_000005'){

					$q=  "  SELECT  A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
									Ccocod, Cconom,   
									TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),
									Sgenom, Movfal "
						."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
						."  WHERE A.Movfal BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						."     AND Ccocod       = Habcco "
						."     AND Habcod       = movhab "
						."     AND A.movemp     = Sgecod "
						."     AND movemp       = Sgecod  "
						."     AND B.fecha_data = A.movfal "
						."     AND ".$wtabcco.".Ccoemp = '".$wemp_pmla."' "
						."     AND A.id         = ".$wid
						.$wrangohora
						."   ORDER BY A.Movfec, A.hora_data ";
				}
				else{

					$q=  "  SELECT  A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
									Ccocod, Cconom,   
									TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),
									Sgenom, Movfal "
						."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
						."  WHERE A.Movfal BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						."     AND Ccocod       = Habcco "
						."     AND Habcod       = movhab "
						."     AND A.movemp     = Sgecod "
						."     AND movemp       = Sgecod  "
						."     AND B.fecha_data = A.movfal "
						."     AND A.id         = ".$wid
						.$wrangohora
						."   ORDER BY A.Movfec, A.hora_data ";
				}	
			}		
			else
			{
				if (isset($word) and $word == "S")
				{
                    if ($wtabcco == 'costosyp_000005'){

						$q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
									Ccocod, Cconom,   
									TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),
									Sgenom, Movfdi "
							."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
							."  WHERE A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
							."     AND Ccocod   LIKE '".trim($wserv)."'"
							."     AND Ccocod   = Habcco "
							."     AND Habcod   = movhab "
							."     AND Movhem  != '00:00:00' "
							."     AND Movhdi  != '00:00:00' "
							."     AND A.movemp = Sgecod "
							."     AND movemp   = Sgecod  "
							."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
							."     AND B.fecha_data = A.movfal "
							."     AND ".$wtabcco.".Ccoemp = '".$wemp_pmla."' "
							.$wrangohora
							."   ORDER BY 9 desc ";
					}
                    else{

						$q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
									Ccocod, Cconom,   
									TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),
									Sgenom, Movfdi "
							."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
							."  WHERE A.Movfal  BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
							."     AND Ccocod   LIKE '".trim($wserv)."'"
							."     AND Ccocod   = Habcco "
							."     AND Habcod   = movhab "
							."     AND Movhem  != '00:00:00' "
							."     AND Movhdi  != '00:00:00' "
							."     AND A.movemp = Sgecod "
							."     AND movemp   = Sgecod  "
							."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
							."     AND B.fecha_data = A.movfal "
							.$wrangohora
							."   ORDER BY 9 desc ";
					}	
				}
				else
				{
					if  ($wtabcco == 'costosyp_000005'){
						$q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
									Ccocod, Cconom,   
									TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),  
									Sgenom, Movfdi "
							."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
							."  WHERE A.Movfal      BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
							."     AND Ccocod       like '".trim($wserv)."'"
							."     AND Ccocod       = Habcco "
							."     AND Habcod       = movhab "
							."     AND Movhem      != '00:00:00' "
							."     AND Movhdi      != '00:00:00' "
							."     AND A.movemp     = Sgecod "
							."     AND movemp       = Sgecod  "
							."     AND B.fecha_data = A.movfal "
							."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
							."     AND ".$wtabcco.".Ccoemp = '".$wemp_pmla."' "
							.$wrangohora
							."   ORDER BY A.Movfec, A.hora_data ";
					}
					else{
						$q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
									Ccocod, Cconom,   
									TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
									TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),  
									Sgenom, Movfdi "
							."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000067 B, ".$wtabcco.", ".$wbasedato."_000024 "
							."  WHERE A.Movfal      BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
							."     AND Ccocod       like '".trim($wserv)."'"
							."     AND Ccocod       = Habcco "
							."     AND Habcod       = movhab "
							."     AND Movhem      != '00:00:00' "
							."     AND Movhdi      != '00:00:00' "
							."     AND A.movemp     = Sgecod "
							."     AND movemp       = Sgecod  "
							."     AND A.movemp  not in (SELECT Sgecod FROM ".$wbasedato."_000024 WHERE Sgeman = 'on') "
							."     AND B.fecha_data = A.movfal "
							.$wrangohora
							."   ORDER BY A.Movfec, A.hora_data ";
					}	
				}
			}
			//echo $q;
			$res = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
			$num = mysql_num_rows($res);
			$col = mysql_num_fields($res);

			if ($num > 0)
			{
				//ACA IMPRIMO LOS NOMBRES DE LOS CAMPOS COMO TITULOS
                echo "<tr class=encabezadoTabla>";
				echo "<th>Fecha de solicitud</th>";
				echo "<th>Hora de solicitud</th>";
				echo "<th>Fecha de Asignación</th>";
				echo "<th>Hora de asignacion</th>";
				echo "<th>Fecha de Alistamiento</th>";
				echo "<th>Hora de Alistamiento</th>";
				echo "<th>Hab.</th>";
				echo "<th>Servicio</th>";
				echo "<th>Observ.</th>";
				echo "<th>Empleado</th>";
				echo "<th>Tiempo total (minutos)</th>";
				echo "<th>Tiempo cesante (minutos)</th>";
				echo "<th>Tiempo de alistamiento (minutos)</th>";

				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($res);

					if (is_integer($i / 2))
					$wclass = "fila1";
					else
					$wclass = "fila2";

					echo "<tr class=".$wclass.">";
					echo "<td>".$row[6]."</td>";
					echo "<td>".$row[7]."</td>";
					echo "<td>".$row[0]."</td>";
					echo "<td>".$row[3]."</td>";
					echo "<td>".$row[14]."</td>";
					echo "<td>".$row[4]."</td>";
					echo "<td>".$row[1]."</td>";
					echo "<td>".$row[9]."-".$row[10]."</td>";
					echo "<td>".$row[5]."</td>";
					echo "<td>".$row[2]."-".$row[13]."</td>";
					echo "<td><span id='wtt".$i."' title='En horas: ".$row[8]."'>".number_format(tiempoNumero($row[8]),2,'.','')."</span></td>";
					echo "<td><span id='wtc".$i."' title='En horas: ".$row[11]."'>".number_format(tiempoNumero($row[11]),2,'.','')."</span></td>";
					echo "<td><span id='wta".$i."' title='En horas: ".$row[12]."'>".number_format(tiempoNumero($row[12]),2,'.','')."</span></td>";
					echo "</tr>";
				}

				if (isset($wser) and $wser=='%')
				{
					$q=  "  SELECT A.Movfec, Movhab, Movemp, Movhem, Movhdi, Movobs, Movfal, Movhal,
								TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(Movfal,' ',Movhal))),  
								TIMEDIFF((CONCAT(A.Movfec,' ',movhem)),(CONCAT(Movfal,' ',Movhal))),  
								TIMEDIFF((CONCAT(A.Movfdi,' ',movhdi)),(CONCAT(A.Movfec,' ',movhem))),  
								Sgenom, Movfdi "
						."  FROM ".$wbasedato."_000025 A, ".$wbasedato."_000024 "
						."  WHERE A.Movfal BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
						."     AND Movhem      != '00:00:00' "
						."     AND Movhdi      != '00:00:00' "
						."     AND movemp       = Sgecod  "
						."     AND movhab not in (select habcod from movhos_000020) "
						.$wrangohora
						."   ORDER BY A.Movfec, A.hora_data ";

					$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$num2 = mysql_num_rows($res);
					$num=$num+$num2;

					for ($i=1;$i<=$num2;$i++)
					{
						$row = mysql_fetch_array($res);

						if (is_integer($i / 2))
						   $wclass = "fila1";
						  else
						 	$wclass = "fila2";
						
						echo "<tr class=".$wclass.">";
						echo "<td>".$row[6]."</td>";
						echo "<td>".$row[7]."</td>";
						echo "<td>".$row[0]."</td>";
						echo "<td>".$row[3]."</td>";
						echo "<td>".$row[12]."</td>";
						echo "<td>".$row[4]."</td>";
						echo "<td>".$row[1]."</td>";
						echo "<td>&nbsp</td>";
						echo "<td>".$row[5]."</td>";
						echo "<td>".$row[2]."-".$row[11]."</td>";
						echo "<td>".$row[8]."</td>";
						echo "<td>".$row[9]."</td>";
						echo "<td>".$row[10]."</td>";
						echo "</tr>";
					}

				}
				echo "<tr class=encabezadoTabla>";
				echo "<th colspan=13 align=center><b>Total servicios en el período : ".$num."</b></th>";
				//echo "<th colspan=1>".$num."</th>";
				echo "</tr>";
			}
		}
		echo "</center></table>";
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}
	echo "</form>";
	
	echo "<center><table>"; 
    echo "<tr><td align=center>";
	if(isset($wcons) && $wcons)
		echo "<input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wser\",\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$whora_i\",\"$whora_f\");'> &nbsp; ";
	echo "<input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
}
include_once("free.php");
?>
</body>
</html>