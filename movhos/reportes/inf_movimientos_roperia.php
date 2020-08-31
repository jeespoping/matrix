<html>
<head>
  <title>INFORME DE MOVIMIENTOS DE STOCK POR SERVICIOS</title>
</head>

<script type="text/javascript">

  // Validación para movimientos con factor 1
  function valida_envio(form)
  {
	if(document.getElementById('wfecha_i').value > document.getElementById('wfecha_f').value) 
	{
		alert("La fecha inicial no puede ser mayor que la final");
		return false;
	}
	form.submit();
  }

  // Vuelve a la página anterior llevando sus parámetros
  function retornar(wemp_pmla,wfecha_i,wfecha_f,wcco,wconcepto,wronda,wprenda)
	{
		location.href = "inf_movimientos_roperia.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&wcco="+wcco+"&wconcepto="+wconcepto+"&wronda="+wronda+"&wprenda="+wprenda+"&bandera=1";
	}
	
  // Cierra la ventana
  function cerrar_ventana(cant_inic)
	{
		window.close();
    }
     
</script>
<body>

<?php
include_once("conex.php");
  /******************************************************
   *   INFORME DE MOVIMIENTOS DE STOCK POR SERVICIO		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *****************************************************************
	 * Muestra todos los movimientos generados en la aplicación de roperia a nivel interno 	*
	 * es decir, los movimientos de prendas en los servicios de la clínica.					*
	 ****************************************************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-06-14						*
	 * Modificado: 										*********************************************************************
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2013-02-26 - Se agregó TRIM(movcco) para los JOIN que se hacen del campo movcco de la tabla 000105 de movimiento 	*
	 * hospitalario ya que este campo tiene registros con un espacio en blanco al final y no se estaban visualizando en 	*
	 * el reporte que se filtra por centro de costo - Mario Cadavid															*
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2011-09-09 - Se cambió el orden de las columnas cantidad y existencia en el reporte y se cambio en el orden en el 	*
	 * que se muestra el listado, ordenando primero por nmbre de la prenda- Mario Cadavid									*
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2012-02-06 - Se adicionó el filtro por concepto en el formualrio de consulta y también se adicionó la columna 		*
	 * porcentaje de cumplimiento en los resultados del reporte cuando es por conceptos de entrega - Mario Cadavid 			*
	 ************************************************************************************************************************
	*/
   
   session_start();

// Inicia la sessión del usuario
if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
// Si el usuario no está registrado muestra el mensaje de error
if(!isset($_SESSION['user']))
	echo "error";
else	// Si el usuario está registrado inicia el programa
{	            
 	
  

  include_once("root/comun.php");
  

  
  // Obtengo los datos del usuario
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
 
  // Aca se coloca la ultima fecha de actualización
  $wactualiz = " Febrero 26 de 2013 ";
                                                   
  echo "<br>";				
  echo "<br>";

  //**********************************************//
  //********** F U N C I O N E S *****************//
  //**********************************************//

  // Convierte formato fecha (yyyy-mm-dd) en texto
  function fecha_texto( $fecha ){
	$fec = explode("-",$fecha);
	$mes = (int)$fec[1];
	$dia = $fec[2];
	$anio = $fec[0];
    return mes_texto($mes)." ".$dia." de ".$anio;
  }
  
  // Consulta los datos de las aplicaciones
  function datos_empresa($wemp_pmla)
    {  
	  global $user;   
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	     
	  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
	  $q = " SELECT detapl, detval, empdes "
	      ."   FROM root_000050, root_000051 "
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
		      
		      if ($row[0] == "movhos")
		         $wbasedato=$row[1];

			  if ($row[0] == "tabcco")
		         $wtabcco=$row[1];

			 }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
    }
  
  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtengo los datos de la empresa
  datos_empresa($wemp_pmla);

  // Se define el formulario principal de la página
  echo "<form name='form1' id='form1' action='inf_movimientos_roperia.php' method='post'  onSubmit='return valida_envio(this);'>";
  
  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  // Obtener titulo de la página con base en el concepto
  $titulo = "INFORME DE MOVIMIENTOS DE STOCK POR SERVICIO";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  // Si no se ha enviado datos muestre el formulario de selección de servicio e intervalo de fechas
  if (!isset($envio))
    {

	    // Consulta de servicios o centros de costos
	  $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

	  // Consulta de conceptos de roperia
      $qcon = " SELECT Tcocod, Tconom, Tcofac "
          ."   FROM ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000102 "
          ."  WHERE Rontip = 'Interna' "
          ."  	AND Ronest = 'on' "
          ."  	AND Roncon = Concod "
          ."  	AND Conest = 'on' "
          ."  	AND Contco = Tcocod "
          ."  	AND Tcoest = 'on' "
		  ."  GROUP BY Tcocod, Tconom "
		  ."  ORDER BY Tcocod, Tconom ";
      $rescon = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
      $numcon = mysql_num_rows($rescon);

	  // Consulta de rondas en servicios o centros de costos
      $qron = " SELECT Roncod, Ronnom, Ronhin, Ronhfi "
          ."   FROM ".$wbasedato."_000102 "
          ."  WHERE Rontip = 'Interna' "
		  ."  ORDER BY Roncon, Ronhin ";
      $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
      $numron = mysql_num_rows($resron);

	  // Consulta de prendas
      $qpre = " SELECT precod, predes "
          ."   FROM ".$wbasedato."_000103 "
		  ."  ORDER BY precod ";
      $respre = mysql_query($qpre,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qpre." - ".mysql_error());
      $numpre = mysql_num_rows($respre);

	  //Parámetros de consulta del informe
  		if (!isset ($bandera))
  		{  			
 			$wfecha_i=$wfecha;
  			$wfecha_f=$wfecha;
			$wcco="";
			$wronda="";
			$wprenda="";
		}
  		
		echo "<table align='center' cellspacing='2'>";

		//Petición de selección de centro de costo o servicio
		echo "<tr>";
        echo "<td class=fila1 align='center' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Seleccione el servicio:</b></div>";
	    echo "</td>";
        echo "<td align='center' width='37px'>&nbsp;</td>";
        echo "<td class=fila1 align='center'>";
        echo "<div align='center' class='fila1'><b>Seleccione el concepto:</b></div>";
	    echo "</td>";
        echo "<td class=fila1 align='center'>";
        echo "<div align='center' class='fila1'><b>Seleccione la ronda:</b></div>";
	    echo "</td>";
	    echo "</tr>";

	    echo "<tr>";
		// Campo select de centros de costos o servicios
		echo "<td class='fila2' align='center' colspan='2'>";
		echo "<select name='wcco' id='wcco'>";
		echo "<option>% - Todos los servicios</option>";
	    for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
		  if($wcco != $row[0]." - ".$row[1])
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		  else
			echo "<option selected>".$row[0]." - ".$row[1]."</option>";
         }
        echo "</select>";
		echo "</td>";
        echo "<td align='center' width='37px'>&nbsp;</td>";

		// Campo select de conceptos
        echo "<td class='fila2' align='center'>";
		echo "<select name='wconcepto' id='wconcepto'>";
		echo "<option>% - Todos los conceptos</option>";
	    for ($i=1;$i<=$numcon;$i++)
	     {
	      $rowcon = mysql_fetch_array($rescon); 
		  if($wconcepto != $rowcon[0]." - ".$rowcon[1]." - ".$rowcon[2])
			echo "<option value='".$rowcon[0]." - ".$rowcon[1]." - ".$rowcon[2]."'>".$rowcon[0]." - ".$rowcon[1]."</option>";
		  else
			echo "<option value='".$rowcon[0]." - ".$rowcon[1]." - ".$rowcon[2]."' selected>".$rowcon[0]." - ".$rowcon[1]."</option>";
         }
        echo "</select>";
	    echo "</td>";

		// Campo select de rondas
        echo "<td class='fila2' align='center'>";
		echo "<select name='wronda' id='wronda'>";
		echo "<option>% - Todas las rondas</option>";
	    for ($i=1;$i<=$numron;$i++)
	     {
	      $rowron = mysql_fetch_array($resron); 
		  if($wronda != $rowron[0]." - ".$rowron[1])
			echo "<option>".$rowron[0]." - ".$rowron[1]."</option>";
		  else
			echo "<option selected>".$rowron[0]." - ".$rowron[1]."</option>";
         }
        echo "</select>";
	    echo "</td>";
		echo "</tr>";
        echo "<tr><td colspan='5' height='11'></td></tr>";

		echo "<tr>";
		//Petición de ingreso de fechas a consultar
        echo "<td class=fila1 align='center' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Ingrese las Fechas a consultar:</b></div>";
	    echo "</td>";
        echo "<td align='center'>&nbsp;</td>";
        echo "<td class='fila1' colspan='2' align='center'>";
        echo "<div align='center' class='fila1'><b>Seleccione la prenda:</b></div>";
	    echo "</td>";
	    echo "</tr>";
		echo "<tr>";
		echo "<td class=fila2 align=center height='61px' width='140px'>Fecha Inicial<br>";
  		campoFechaDefecto("wfecha_i", $wfecha_i);
        echo "</td>";
      	echo "<td class=fila2 align=center height='61px' width='140px'>Fecha Final<br>";
  		campoFechaDefecto("wfecha_f", $wfecha_f);
	    echo "</td>";
        echo "<td align='center'>&nbsp;</td>";

		// Campo select de centros de prendas
        echo "<td class='fila2' colspan='2' align='center'>";
		echo "<select name='wprenda' id='wprenda'>";
		echo "<option>% - Todas las prendas</option>";
	    for ($i=1;$i<=$numpre;$i++)
	     {
	      $rowpre = mysql_fetch_array($respre); 
		  if($wprenda != $rowpre[0]." - ".$rowpre[1])
			echo "<option>".$rowpre[0]." - ".$rowpre[1]."</option>";
		  else
			echo "<option selected>".$rowpre[0]." - ".$rowpre[1]."</option>";
         }
        echo "</select>";
	    echo "</td>";
	    echo "</tr>";
	    echo "</table>";	  

	  // Botones Aceptar y Cerrar Ventana
	  echo "<br><table align='center'>";
	  echo "<tr><td align='center'><input type='submit' value='Consultar'> &nbsp; &nbsp; &nbsp; &nbsp; <input type='button' value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
	  echo "</table>";	  

	  echo "<input type='hidden' name='envio' id='envio' value='1'>";
	  
    } 
	else	// ACA INICIA LA IMPRESION DEL INFORME SEGUN LOS PARAMETROS DE CONSULTA
    {

		// Obtengo el código del servicio
		$cco = explode(" - ",$wcco);
		$wcco_cod = $cco['0'];
		
		// Obtengo el código del concepto
		$con = explode(" - ",$wconcepto);
		$wcon_cod = $con['0'];
		if(isset($con['2']))
			$wcon_fac = $con['2'];
		else
			$wcon_fac = '-1';

		// Obtengo el código de la ronda
		$ron = explode(" - ",$wronda);
		$wron_cod = $ron['0'];

		// Obtengo el código del servicio
		$pre = explode(" - ",$wprenda);
		$wpre_cod = $pre['0'];

		echo "<table align='center' cellspacing='2'>";
		echo "<tr>";
      	echo "<td class=fila2 colspan='2' align='center' height='27px'> &nbsp; Servicio:";
  		echo $wcco." &nbsp; ";
	    echo "</td>";
      	echo "<td width='37px'>&nbsp;</td>";
      	echo "<td class=fila2 align='center' height='27px'> &nbsp; Concepto: ".$con['0']." - ".$con['1']." &nbsp; <br /> &nbsp; Ronda: ".$wronda." &nbsp; ";
	    echo "</td>";
	    echo "</tr>";
		echo "<tr>";
		echo "<td class=fila2 align='center' height='27px'> &nbsp; Fecha Inicial: ";
  		echo $wfecha_i." &nbsp; ";
        echo "</td>";
      	echo "<td class=fila2 align='center' height='27px'> &nbsp; Fecha Final: ";
  		echo $wfecha_f." &nbsp; ";
	    echo "</td>";
      	echo "<td width='37px'>&nbsp;</td>";
      	echo "<td class=fila2 align='center' height='27px'> &nbsp; Prenda: ";
  		echo $wprenda." &nbsp; ";
	    echo "</td>";
	    echo "</tr>";
		echo "<tr><td align='center' height='11px'> &nbsp; </td></tr>";
		echo "</table>";
		
		// Tabla principal con los datos del informe
		echo "<div align='center'>";
		echo "<table align='center' cellspacing='2'>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='11'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wcco\",\"$wconcepto\",\"$wronda\",\"$wprenda\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='11' height='21'></td></tr>";
		
		// Consulta de los movimientos realizados para las fechas a consultar 
		$qlis = " SELECT A.Fecha_data, A.Hora_data, Movfec, Movhor, Movpre, Predes, Movcco, Cconom, Movron, Ronnom, Movcan, Movexi, Movsto, A.Seguridad, Tcofac
					FROM ".$wbasedato."_000105 A, ".$wbasedato."_000103, ".$wbasedato."_000011, ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000102
				   WHERE Movfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND Movest = 'on' 
					 AND Tcocod LIKE '".$wcon_cod."' 
					 AND TRIM(Movcco) LIKE '".$wcco_cod."' 
					 AND Movron LIKE '".$wron_cod."' 
					 AND Movpre LIKE '".$wpre_cod."' 
					 AND Movcco = Ccocod 
					 AND Movpre = Precod 
					 AND Movron = Roncod 
					 AND Roncon = Concod	 
					 AND Rontip = 'Interna' 
					 AND Contco = Tcocod
				GROUP BY Movfec, Movhor, Movron, Movcco, Movpre
				ORDER BY Movpre, Movfec, Movhor, Movcco, Movron";
        $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
        $numlis = mysql_num_rows($reslis);
		$rowlis = mysql_fetch_array($reslis);

		// Titulos de las columnas
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='center'>";
		echo " Fecha registro ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Hora registro ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Fecha movimiento ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Hora movimiento ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Prenda ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Servicio ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Ronda ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Stock asignado ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Existencia antes de movimiento ";
		echo "</td>";
		echo "<td align='center'>";
		echo " Cantidad ";
		echo "</td>";
		if($wcon_fac=='1')
		{
			echo "<td align='center'>";
			echo " Porcentaje de cumplimiento ";
			echo "</td>";
		}
		echo "<td align='center'>";
		echo " Usuario ";
		echo "</td>";
		echo "</tr>";

		
		$j=1;	// Contador de registros de la consulta
		$sum_can = 0;
		$sum_exi = 0;
		$sum_sto = 0;
		if($wcon_fac=='1')
		{
			$sum_can_ent = 0;
			$sum_exi_ent = 0;
			$sum_sto_ent = 0;
			$sum_por = 0;
			$cont_por = 0;
		}

		// Ciclo para recorrer todos los registros de la consulta
		while($j<=$numlis)
		{
			
			if (is_int ($j/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila
			
			// Calculo el porcentaje de cumplimiento
			$sum_can += $rowlis['Movcan'];
			$sum_exi += $rowlis['Movexi'];
			$sum_sto += $rowlis['Movsto'];
			if($wcon_fac=='1')
			{
				if($rowlis['Tcofac']=='1')
				{
					$sum_can_ent += $rowlis['Movcan'];
					$sum_exi_ent += $rowlis['Movexi'];
					$sum_sto_ent += $rowlis['Movsto'];

					$porcentaje_cum = (($rowlis['Movcan']+$rowlis['Movexi'])/$rowlis['Movsto']) * 100; 
					$sum_por += $porcentaje_cum;
					$porcentaje = number_format($porcentaje_cum, 2,".",",")." %";
					$cont_por++;
				}
				else
				{
					$porcentaje = ""; 
				}
			}
			echo "<tr class='".$wcf."'>";
			echo "<td align='center'>";
			echo " ".$rowlis['Fecha_data']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".$rowlis['Hora_data']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".$rowlis['Movfec']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".$rowlis['Movhor']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".$rowlis['Movpre']." - ".$rowlis['Predes']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".$rowlis['Movcco']." - ".$rowlis['Cconom']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".$rowlis['Movron']." - ".$rowlis['Ronnom']." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".number_format($rowlis['Movsto'], 0,".",",")." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".number_format($rowlis['Movexi'], 0,".",",")." ";
			echo "</td>";
			echo "<td align='center'>";
			echo " ".number_format($rowlis['Movcan'], 0,".",",")." ";
			echo "</td>";
			if($wcon_fac=='1')
			{
				echo "<td align='center'>";
				echo " ".$porcentaje." ";
				echo "</td>";
			}
			echo "<td align='center'>";
			echo " ".$rowlis['Seguridad']." ";
			echo "</td>";
			echo "</tr>";

			$rowlis = mysql_fetch_array($reslis);
			$j++;
		}

		if($wcon_fac=='1')
		{
			if($cont_por>0 && $sum_sto>0)
			{
				$porcentaje_cum_total = (($sum_exi + $sum_can) / $sum_sto ) * 100;
				$porcentaje_total = number_format($porcentaje_cum_total, 2,".",",")." %";
			}
			else
			{
				$porcentaje_total = "";
			}
		}
		
		// Total registros
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='center' colspan='7'>";
		echo " TOTAL ";
		echo "</td>";
		echo "<td align='center'>";
		echo " ".number_format($sum_sto, 0,".",",")." ";
		echo "</td>";
		echo "<td align='center'>";
		echo " ".number_format($sum_exi, 0,".",",")." ";
		echo "</td>";
		echo "<td align='center'>";
		echo " ".number_format($sum_can, 0,".",",")." ";
		echo "</td>";
		if($wcon_fac=='1')
		{
			echo "<td align='center'>";
			echo " ".$porcentaje_total." ";
			echo "</td>";
		}
		echo "<td align='center'> </td>";
		echo "</tr>";

		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='11' height='21'></td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='11'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wcco\",\"$wconcepto\",\"$wronda\",\"$wprenda\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  
		echo "</div>";
	}	          
	   
  echo "<br>";
  echo "</form>";
  
} 

?>
</body>
</html>