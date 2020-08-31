<html>
<head>
  <title>INDICADOR DE CUMPLIMIENTO EN CLINICA</title>
</head>

<script type="text/javascript">

  // Validación para movimientos con factor 1
  function valida_envio(form)
  {
	//alert(document.getElementById('wfecha_i').value +" "+document.getElementById('wfecha_f').value )
	if(document.getElementById('wfecha_i').value > document.getElementById('wfecha_f').value) 
	{
		alert("La fecha inicial no puede ser mayor que la final");
		return false;
	}
	form.submit();
	
  }

// Vuelve a la página anterior llevando sus parámetros
function retornar(wemp_pmla,wfecha_i,wfecha_f,wcco)
	{
		location.href = "ind_cumplimiento_roperia.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&wcco="+wcco+"&bandera=1";
	}
	
// Cierra la ventana
function cerrar_ventana(cant_inic)
	{
		window.close();
    }
	
function enter()
{
	 var form = document.getElementById('form1');
	 valida_envio(form);
	
}	
     
</script>
<body>

<?php
include_once("conex.php");
  /******************************************************
   *   INDICADOR DE CUMPLIMIENTO EN CLINICA		   		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *************************************************************
	 * Muestra el porcentaje de cumplimiento con base en el stock y la entrega diaria 	*
	 * para el servicio y las fechas seleccionadas.										*
	 ************************************************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-04-29						*
	 * Modificado: 										*********************************************************************
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2013-02-26 - Se agregó TRIM(movcco) para los JOIN que se hacen del campo movcco de la tabla 000105 de movimiento 	*
	 * hospitalario ya que este campo tiene registros con un espacio en blanco al final y no se estaban visualizando en 	*
	 * el reporte que se filtra por centro de costo - Mario Cadavid															*
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2011-08-03 - Creación de tablas temporales para mejorar la velocidad del reporte - Mario Cadavid						*
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2011-08-01 - Creación de la función obtener_datos de permite tener en cuenta la existencia y stock para las prendas	*
	 * de las que no se hicieron movimiento en los días consultados - Mario Cadavid											*
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2011-09-22 - En la función obtener_datos se quito la división de la suma de entregas, ya que se plantea que las 		*
	 * entregas pueden ser varias al día, mientras que stock es fijo y existencia se toma el promedio - Mario Cadavid		*
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2012-02-07 - Se quitó el llamado a la función obtener_datos pues solo se van a consultar las prendas que tuvieron 	*
	 * movimiento de modo que los valores concuerden con el reporte de movimientos de stock por servicio.					*
	 * Se adicionó la columna de Stock movimientos que indica el stock con base en los movimientos del día					*
	 * En el total se cambió el cálculo de % de cumplimiento haciendo que sea igual a total existencia + total entrega 		*
	 * dividido por stock movimientos diarios - Mario Cadavid
	 * -------------------------------------------------------------------------------------------------------------------- *
	 * 2012-02-28 - Se Creó la opción de mostrar el Reporte Resumido Totalizando el STOCK DIARIO, STOCK MOVIMIENTOS,         *
	 * EXISTENCIA, EL TOTAL ENTREGA DIA y el PORCENTAJE DE CUMPLIMIENTO, por centro de costos - santiago  		
	 ************************************************************************************************************************
	*/
   
   session_start();
ini_set('display_errors', false);
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

  // Consulta los datos de stock y existencia de las prendas que no estan en los movimiento (Tabla 105 de Movhos)
  function obtener_datos($wemp_pmla,$fecha,$cco,$templist,$temp105)
    {  
	  global $user;   
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	     
	  // Consulto las prendas asignadas para el centro de costos, tengan o no movimiento ese día
	  $qpre = "	  SELECT Movpre
					FROM ".$templist."
				   WHERE Movfec = '".$fecha."' 
					 AND TRIM(Movcco) = '".$cco."'
				GROUP BY 1
				   UNION
				  SELECT Stopre
					FROM ".$wbasedato."_000104 
				   WHERE Stocco = '".$cco."' 
				GROUP BY 1
				ORDER BY 1"; 
	  $respre = mysql_query($qpre,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qpre." - ".mysql_error());
	  $numpre = mysql_num_rows($respre); 
	  $rowpre = mysql_fetch_array($respre);

	  // Consulto las rondas hechas en el dia
	  $qron = "	  SELECT Movron
					FROM ".$templist."
				   WHERE Movfec = '".$fecha."' 
					 AND TRIM(Movcco) = '".$cco."'
				GROUP BY Movron "; 
	  //$resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
	  //$rowron = mysql_fetch_array($resron);

	  $stock = 0;
	  $existencia = 0;
	  $entrega = 0;
	  //$contot = 0;
	  
	  $j=1;
	  // Recorro las prendas del centro de costo
	  while($j<=$numpre)
	  {
		  $i=1;
		  $cont=0;
		  $stockpre = 0;
		  $existenciapre = 0;
		  $entregapre = 0;
		  echo $rowpre[0]." - ";

		  $resron = mysql_query($qron,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qron." - ".mysql_error());
	  	  $numron = mysql_num_rows($resron); 

		  // Recorro las rondas del día
		  while($i<=$numron)
		  {
			  $rowron = mysql_fetch_array($resron);
			  // Consulto si la prenda tiene movimientos para esa fecha, centro de costo y ronda
			  $q = "  SELECT Movpre, Movsto, Movexi, Movcan
						FROM ".$templist."
					   WHERE TRIM(Movcco) = '".$cco."'
						 AND Movfec = '".$fecha."' 
						 AND Movpre = '".$rowpre[0]."' 
						 AND Movron = '".$rowron[0]."' "; 
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $num = mysql_num_rows($res); 
			  
			  // Si tiene movimiento consulto los datos del movimiento
			  if($num>0)
			  {
				  $row = mysql_fetch_array($res);
				  $stockpre += $row['Movsto'];
				  $existenciapre += $row['Movexi'];
				  $entregapre += $row['Movcan'];
			  }
			  // Si no tiene movimiento consulto el último stock y existencia registrada
			  else
			  {
				  // Consulto el último movimiento registrado para la prenda
				  // Aca si se tiene en cuenta las rondas de conteo, por eso no se filtra con Tcofac=1
				  $q1 = "  SELECT Movexi, Movsto
							FROM ".$temp105." 
						   WHERE TRIM(Movcco) = '".$cco."'
							 AND Movfec <= '".$fecha."' 
							 AND Movpre = '".$rowpre[0]."'  
						ORDER BY Movfec DESC, Movhor DESC
						   LIMIT 0,1"; 
				  $res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
				  $num1 = mysql_num_rows($res1); 
				  $row1 = mysql_fetch_array($res1);
				  // Si encontro datos en movimiento anterior, tome esos datos
				  if($num1>0)
				  {
					  $stockpre += $row1['Movsto'];
					  $existenciapre += $row1['Movexi'];
				  }
				  // Si no se encuentran datos en movimientos entonces vaya a tomar los datos de la tabla 104 (Stock por CCO)
				  else
				  {
					  $q2 = "  SELECT Stoexi, Stosto
								FROM ".$wbasedato."_000104 
							   WHERE Stocco = '".$cco."'
								 AND Stopre = '".$rowpre[0]."' "; 
					  $res2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
					  $num2 = mysql_num_rows($res2); 
					  $row2 = mysql_fetch_array($res2);
					  $stockpre += $row2['Stosto'];
					  $existenciapre += $row2['Stoexi'];
				  }
			  }
			  
			  $cont++;
			  //$contot++;
			  //$rowron = mysql_fetch_array($resron);
			  $i++;
		  }
		  if($cont>0)
		  {
			  // Se comenta porque no se necesita el promedio por día sino el total del día - 2011-09-20
			  $stock += $stockpre/$cont;
			  $existencia += $existenciapre/$cont;
			  //$entrega += $entregapre/$cont;
			  //$stock += $stockpre;
			  //$existencia += $existenciapre;
			  $entrega += $entregapre;
		  }
		
		$rowpre = mysql_fetch_array($respre);
		$j++;
	  }
	  echo "<br>";

	  return $stock." - ".$existencia." - ".$entrega;
	      
    }

	
  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtengo los datos de la empresa
  datos_empresa($wemp_pmla);

  // Se define el formulario principal de la página
  echo "<form name='form1' id='form1' action='ind_cumplimiento_roperia.php' method='post'  onSubmit='return valida_envio(this);'>";
  
  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  // Obtener titulo de la página con base en el concepto
  $titulo = "INDICADOR DE CUMPLIMIENTO EN CLINICA";
	
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

	    //Parámetros de consulta del informe
  		if (!isset ($bandera))
  		{  			
 			$wfecha_i=$wfecha;
  			$wfecha_f=$wfecha;
			$wcco="";
		}
		else
		{
 			$wfecha_i=$wfecha_i;
  			$wfecha_f=$wfecha_f;
			$wcco=$wcco;
		}
  		
		echo "<table align='center' cellspacing='2'>";

		//Petición de selección de centro de costo o servicio
		echo "<tr class=fila1>";
        echo "<td colspan='2'>";
        echo "<div align='center' class='fila1'><b>Seleccione el servicio:</b></div>";
	    echo "</td>";
	    echo "</tr>";

		// Campo select de centros de costos o servicios
	    echo "<tr class='fila2'><td align='center' colspan='2'>";
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
        echo "</select></td></tr>";
        echo "<tr><td colspan='2' height='11'></td></tr>";

		//Petición de ingreso de fechas a consultar
		echo "<tr class=fila1>";
        echo "<td colspan='2'>";
        echo "<div align='center' class='fila1'><b>Ingrese las Fechas a consultar:</b></div>";
	    echo "</td>";
	    echo "</tr>";
		echo "<tr class=fila2>";
		echo "<td align=center height='61px' width='140px'>Fecha Inicial<br>";
  		campoFechaDefecto("wfecha_i", $wfecha_i);
        echo "</td>";
      	echo "<td align=center height='61px' width='140px'>Fecha Final<br>";
  		campoFechaDefecto("wfecha_f", $wfecha_f);
	    echo "</td>";
	    echo "</tr>";
	    echo "</table>";	  

	  // Botones Aceptar y Cerrar Ventana
	  echo "<br><table align='center'>";
	  //echo "<tr><td align='center'><input type='submit' value='Consultar'> &nbsp; &nbsp; &nbsp; &nbsp; <input type='button' value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
	  echo  "<tr>";
      echo  "<td><input name='wtipo' value='resumido' type='radio' onclick='enter()'></td>";
      echo  "<td>Resumido</td>";
      echo  "<td><input name='wtipo' value='detallado' type='radio' onclick='enter()'></td>";
      echo  "<td>Detallado</td>";
      echo  "</tr>";
	  echo  "<tr><td>&nbsp;</td></tr>";
	  echo "<tr><td colspan=4 align=center><input type='button' value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
	  echo "</table>";	  

	  echo "<input type='hidden' name='envio' id='envio' value='1'>";
	  
    } 
	else	// ACA INICIA LA IMPRESION DEL INFORME SEGUN LOS PARAMETROS DE CONSULTA
    {
		
		echo "<table align='center' cellspacing='2'>";
		echo "<tr class=fila2>";
      	echo "<td align='center' height='27px'> &nbsp; Servicio: ";
  		echo $wcco." &nbsp; ";
	    echo "</td>";
	    echo "</tr>";
		echo "<tr class=fila2>";
		echo "<td align='center' height='27px'> &nbsp; Fecha Inicial: ";
  		echo $wfecha_i." &nbsp; ";
        echo "</td>";
		echo "</tr>";
		echo "<tr class=fila2>";
      	echo "<td align='center' height='27px'> &nbsp; Fecha Final: ";
  		echo $wfecha_f." &nbsp; ";
	    echo "</td>";
	    echo "</tr>";
		echo "<tr><td align='center' height='11px' class='fila2'>Tipo de Reporte: ".$wtipo." </td></tr>";
		echo "<tr><td align='center' height='11px'> &nbsp; </td></tr>";
		echo "</table>";
		
		// Tabla principal con los datos del informe
		echo "<table align='center' cellspacing='2'>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='6'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wcco\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='6' height='21'></td></tr>";
		
		// Encabezado de la tabla
		echo "<tr class='encabezadoTabla'>";
		echo "<td colspan='6' align='center'>";
		echo " &nbsp; SOLICITUDES POR SERVICIO O UNIDAD &nbsp; ";
		echo "</td>";
	    echo "</tr>";

		// Obtengo el código del servicio
		$cco = explode(" - ",$wcco);
		$wcco_cod = $cco['0'];
		

		/****************************************************************************************************
		 * Agosto 2 de 2011
		 * Creando tabla temporal para la tabla 105 de movhos entre las fechas consultadas
		 ****************************************************************************************************/
		$temp105 = "temp105".date("His");
		$sql = "  CREATE TEMPORARY TABLE IF NOT EXISTS ".$temp105."
				(  INDEX idxfec ( Movfec ), INDEX idxcco ( Movcco(4) ), INDEX idxron ( Movron(4) ) )
				  SELECT *
					FROM ".$wbasedato."_000105 
				   WHERE Movfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND TRIM(Movcco) LIKE '".$wcco_cod."' 
					 AND Movest = 'on' ";

		$res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

		/****************************************************************************************************
		 * Febrero 7 de 2012
		 * Creando tabla temporal para la tabla 116 de movhos entre las fechas consultadas
		 ****************************************************************************************************/
		$temp116 = "temp116".date("His");
		$sql = "  CREATE TEMPORARY TABLE IF NOT EXISTS ".$temp116."
				(  INDEX idxcco ( Stocco(10) ), INDEX idxpre ( Stopre(10) ) )
				  SELECT *
					FROM ".$wbasedato."_000116 
				   WHERE Fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND Stocco LIKE '".$wcco_cod."' 
					 AND Stoest = 'on' ";

		$res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );

		/****************************************************************************************************
		 * Agosto 2 de 2011
		 * Creando tabla temporal para los movimientos de solo entrega entre las fechas consultadas
		 ****************************************************************************************************/
		//$templist = "templist".date("His");
		/****************************************************************************************************
		 * Febrero 8 de 2012
		 * No es necesario crear tabla temporal pues ya no se va a usar la función obtener_datos
		 ****************************************************************************************************/
		
	
		/* Se comenta porque ya no se va a usar temporal de la consulta principal
		// Agrupo por Fecha y CCO para el ciclo del reporte
		$qlis = " SELECT Movfec, Movcco, Ccocod, Cconom
					FROM ".$templist."
				GROUP BY Movfec, Movcco
				ORDER BY Movfec, Movcco";
        $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
        $numlis = mysql_num_rows($reslis);
		$rowlis = mysql_fetch_array($reslis);
		//echo "<br>".$qlis;
		*/

		// Arreglo que permitirá llevar los datos de stock diario por servicio
		$arr_stock = array ();

	    if($wtipo=="detallado")
		{			
			// Consulta principal del reporte donde se obtiene la lista de movimientos de entrega para los servicios
			$sql = "  SELECT a.Fecha_data Fecha_data, a.Hora_data Hora_data, Movfec, Movhor, Movpre, Movcco, Movron, SUM(Movcan) cantidad, SUM(Movexi) existencia, SUM(Movsto) stock, Ccocod, Cconom
						FROM ".$wbasedato."_000011, ".$temp105." a, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100
					   WHERE Movcco = Ccocod
						 AND Movron = Roncod 
						 AND Roncon = Concod 
						 AND Rontip = 'Interna' 
						 AND Contco = Tcocod 
						 AND Tcofac = '1'
					GROUP BY Movfec, Movcco
					ORDER BY Movfec, Movcco, Movron, Movpre";
			$res = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
			$numlis = mysql_num_rows($res);
			$rowlis = mysql_fetch_array($res);
			
			// Consulta el stock diario por servicio y lo agrupa por fecha y centro de costo
			$qsto = "SELECT Fecha_data, Stocco, SUM(Stosto) stockcco 
					   FROM ".$temp116."
				   GROUP BY Fecha_data, Stocco 
				   ORDER BY Fecha_data, Stocco ";
			$ressto = mysql_query( $qsto ) or die( mysql_errno(). " - Error en el query $qsto - ".mysql_error() );
			$numsto = mysql_num_rows($ressto);
			
			// Ciclo para llenar el encabezado de la tabla con las rondas consultados
			for ($i=1;$i<=$numsto;$i++)
			{
				$rowsto = mysql_fetch_array($ressto);
				$fecha_stock = $rowsto['Fecha_data'];
				$cco_stock = $rowsto['Stocco'];
				$cantidad_stock = $rowsto['stockcco'];
				$arr_stock[$fecha_stock][$cco_stock] = $cantidad_stock;
			}
			
			$j=1;	// Contador de registros de la consulta
			$l=0;	// Contador para obtener el divisor de los totales finales del reporte
			$total_stock = 0;
			$total_stock_dia = 0;
			$total_existencia = 0;
			$total_entrega = 0;
			$total_cumplimiento = 0;
			
			// Ciclo para recorrer todos los registros de la consulta
			while($j<=$numlis)
			{
				
				$k=1;	// Contador para el estilo de las filas
				$aux_fecha = $rowlis['Movfec'];	// Asigno fecha de recorrido actual
				$sum_stock = 0;
				$sum_stock_dia = 0;
				$sum_existencia = 0;
				$sum_entrega = 0;
				$sum_cumplimiento = 0;

				// Espacio entre las filas por cada fecha
				echo "<tr><td colspan='6' height='21'></td></tr>";
				// Encabezado con la fecha actual
				echo "<tr class='encabezadoTabla'>";
				echo "<td colspan='6' align='center'>";
				echo " <b>".fecha_texto($aux_fecha)."</b> ";
				echo "</td>";
				echo "</tr>";
				
				// Titulos de las columnas
				echo "<tr class='encabezadoTabla'>";
				echo "<td align='center'>";
				echo " SERVICIO ";
				echo "</td>";
				echo "<td align='center'>";
				echo " STOCK DIARIO ";
				echo "</td>";
				echo "<td align='center'>";
				echo " STOCK MOVIMIENTOS ";
				echo "</td>";
				echo "<td align='center'>";
				echo " EXISTENCIA ";
				echo "</td>";
				echo "<td align='center'>";
				echo " TOTAL ENTREGA DIA ";
				echo "</td>";
				echo "<td align='center'>";
				echo " % DE CUMPLIMIENTO ";
				echo "</td>";
				echo "</tr>";

				$stock = 0;
				$stock_dia = 0;
				$existencia = 0;
				
				// Ciclo para recorrer los registros de cada fecha
				while($rowlis['Movfec']==$aux_fecha && $j<=$numlis) 
				{
					if (is_int ($k/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
					   
					$fecha_stock = $rowlis['Movfec'];
					$cco_stock = $rowlis['Ccocod'];
					
					/*
					$stock_exist = obtener_datos($wemp_pmla,$aux_fecha,$rowlis['Ccocod'],$templist,$temp105);
					$stock_exist_arr = explode(" - ",$stock_exist);
					*/
					
					$stock = $rowlis['stock'];
					$stock_dia = $arr_stock[$fecha_stock][$cco_stock];
					
					$existencia = $rowlis['existencia'];
					$entrega = $rowlis['cantidad'];
					
					// Calculo el porcentaje de cumplimiento
					$cumplimiento = (($existencia+$entrega)/$stock)*100;
					$sum_stock += $stock;
					$sum_stock_dia += $stock_dia;
					$sum_existencia += $existencia;
					$sum_entrega += $entrega;
					
					echo "<tr class='".$wcf."'>";
					echo "<td>";
					echo " ".$rowlis['Ccocod']." - ".$rowlis['Cconom']." ";
					echo "</td>";
					echo "<td align='right'>";
					echo " ".number_format($stock_dia,0,'.',',')." ";
					echo "</td>";
					echo "<td align='right'>";
					echo " ".number_format($stock,0,'.',',')." ";
					echo "</td>";
					echo "<td align='right'>";
					echo " ".number_format($existencia,0,'.',',')." ";
					echo "</td>";
					echo "<td align='right'>";
					echo " ".number_format($entrega,0,'.',',')." ";
					echo "</td>";
					echo "<td align='right'>";
					echo " ".number_format($cumplimiento,2,'.',',')."% ";
					echo "</td>";
					echo "</tr>";

					$rowlis = mysql_fetch_array($res);
					$j++;
					$k++;
				}
				
				if($sum_stock>0)
					$sum_cumplimiento = (($sum_existencia + $sum_entrega) / $sum_stock ) * 100;
				else
					$sum_cumplimiento = "";

				// Totales por fechas
				echo "<tr class='encabezadoTabla'>";
				echo "<td>";
				echo " TOTAL ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($sum_stock_dia,0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($sum_stock,0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($sum_existencia,0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($sum_entrega,0,'.',',')." ";
				echo "</td>";
				//$cumplimiento = (($sum_existencia+$sum_entrega)/$sum_stock)*100;
				echo "<td align='right'>";
				echo " ".number_format($sum_cumplimiento,2,'.',',')."% ";
				echo "</td>";
				echo "</tr>";
				
				$l++;
				$total_stock += $sum_stock;
				$total_stock_dia += $sum_stock_dia;
				$total_existencia += $sum_existencia;
				$total_entrega += $sum_entrega;
			}
			
			// Espacio para los totales finales
			echo "<tr><td colspan='6' height='21'></td></tr>";

			if($total_stock>0)
				$total_cumplimiento = (($total_existencia + $total_entrega) / $total_stock ) * 100;
			else
				$total_cumplimiento = "";

			// Totales finales
			echo "<tr class='encabezadoTabla'>";
			echo "<td>";
			echo " TOTAL GENERAL ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($total_stock_dia,0,'.',',')." ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($total_stock,0,'.',',')." ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($total_existencia,0,'.',',')." ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($total_entrega,0,'.',',')." ";
			echo "</td>";
			//$cumplimiento = (($sum_existencia+$sum_entrega)/$sum_stock)*100;
			echo "<td align='right'>";
			echo " ".number_format($total_cumplimiento,2,'.',',')."% ";
			echo "</td>";
			echo "</tr>";
		}
		else
		{
			// Consulta principal del reporte donde se obtiene el total de movimientos de entrega para los servicios
			$sql = "  SELECT a.Fecha_data Fecha_data, a.Hora_data Hora_data, Movfec, Movhor, Movpre, Movcco, Movron, SUM(Movcan) cantidad, SUM(Movexi) existencia, SUM(Movsto) stock, Ccocod, Cconom
						FROM ".$wbasedato."_000011, ".$temp105." a, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100
					   WHERE Movcco = Ccocod
						 AND Movron = Roncod 
						 AND Roncon = Concod 
						 AND Rontip = 'Interna' 
						 AND Contco = Tcocod
						 AND Tcofac = '1'
					   GROUP BY Movcco
					   ORDER BY Movcco, Movron, Movpre";	   
			
			$resres = mysql_query( $sql ) or die( mysql_errno(). " - Error en el query $sql - ".mysql_error() );
			$numlisres = mysql_num_rows($resres);
			echo "<tr class='encabezadoTabla'>";
			echo "<td align='center'>";
			echo " SERVICIO ";
			echo "</td>";
			echo "<td align='center'>";
			echo " STOCK DIARIO ";
			echo "</td>";
			echo "<td align='center'>";
			echo " STOCK MOVIMIENTOS ";
			echo "</td>";
			echo "<td align='center'>";
			echo " EXISTENCIA ";
			echo "</td>";
			echo "<td align='center'>";
			echo " TOTAL ENTREGA DIA ";
			echo "</td>";
			echo "<td align='center'>";
			echo " % DE CUMPLIMIENTO ";
			echo "</td>";
			echo "</tr>";
			$j=0;
			$stock = 0;
			$cantidad = 0;
			$existencia = 0;
			$wstocktotal = 0;
			//recorro el centro de costos
			while($j<$numlisres)
			{
				$rowlisres = mysql_fetch_array($resres);
				
				if (is_int ($j/2))
					$wcf="fila1";  // color de fondo de la fila
				else
					$wcf="fila2"; // color de fondo de la fila
					
				// Consulta el stock por servicio y lo agrupa por fecha y centro de costo
				$qstores = "SELECT Stocco, SUM(Stosto) stockcco
							  FROM ".$temp116."
							 WHERE Stocco = '".$rowlisres['Ccocod']."'	
							 GROUP BY Fecha_data,Stocco";			 
				$resstores = mysql_query( $qstores ) or die( mysql_errno(). " - Error en el query $qsto - ".mysql_error() );
				$numstores = mysql_num_rows($resstores);
				$i=0;
				$wstock = 0;
				while($i<$numstores)
				{
					$rowstores = mysql_fetch_array($resstores);
					$wstock +=  $rowstores['stockcco'];
					$i++;
				
				}
				
				$wstockprom = 0;
				if($numstores>0)
				{
					$wstockprom = $wstock/$numstores;
				}
				
				$cumplimiento = (($rowlisres['existencia'] + $rowlisres['cantidad']) / $rowlisres['stock'] ) * 100;
				echo "<tr class='".$wcf."'>";
				echo "<td>";
				echo " ".$rowlisres['Ccocod']." - ".$rowlisres['Cconom']." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($wstockprom,0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($rowlisres['stock'],0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($rowlisres['existencia'],0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($rowlisres['cantidad'],0,'.',',')." ";
				echo "</td>";
				echo "<td align='right'>";
				echo " ".number_format($cumplimiento,2,'.',',')."% ";
				echo "</td>";
				echo "</tr>";
				$wstocktotal += $wstockprom;
				$stock += $rowlisres['stock'];
				$existencia += $rowlisres['existencia'];
				$cantidad += $rowlisres['cantidad'];
				$j++;
			}
			echo "<tr class='encabezadoTabla'>";
			echo "<td>";
			echo " TOTAL ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($wstocktotal,0,'.',',')." ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($stock,0,'.',',')." ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($existencia,0,'.',',')." ";
			echo "</td>";
			echo "<td align='right'>";
			echo " ".number_format($cantidad,0,'.',',')." ";
			echo "</td>";
			if($stock != 0)
				$totalcumplimiento = (($existencia + $cantidad)/$stock)*100;
			else
				$totalcumplimiento = 0;
			echo "<td align='right'>";
			echo " ".number_format($totalcumplimiento,2,'.',',')."% ";
			echo "</td>";
			echo "</tr>";
		}
		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='6' height='21'></td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='6'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wcco\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	
	}	          
	echo "<br>";
	echo "</form>";
  
} 

?>
</body>
</html>