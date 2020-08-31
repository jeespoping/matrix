<html>
<head>
  <title>INDICADOR DE CUMPLIMIENTO LAVANDERIAS</title>
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
function retornar(wemp_pmla,wfecha_i,wfecha_f,wlav)
	{
		location.href = "ind_cumplimiento_lavanderia.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&wlav="+wlav+"&bandera=1";
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
   *   INDICADOR DE CUMPLIMIENTO LAVANDERIAS	   		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *************************************************************
	 * Muestra el porcentaje de cumplimiento de entrega por ronda de las lavanderías 	*
	 ************************************************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-04-29						*
	 * Modificado: 										*********************************************************************
	 * 2012-01-25 - Se creo la tabla temporal "tmp_movhos_000111" e igualemnte se modificó el query principal del reporte	*
	 * de modo que incluya la tabla temporal y se quitó el GROUP BY que tenia ya que no se considera necesario pues es un	*
	 * reporte detallado y el GROUP BY restaba velocidad a la consulta														*
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
  $wactualiz = " Abr. 29 de 2011";
                                                   
  echo "<br>";				
  echo "<br>";

  //**********************************************//
  //********** F U N C I O N E S *****************//
  //**********************************************//

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
  echo "<form name='form1' id='form1' action='ind_cumplimiento_lavanderia.php' method='post'  onSubmit='return valida_envio(this);'>";
  
  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  // Obtener titulo de la página con base en el concepto
  $titulo = "INDICADOR DE CUMPLIMIENTO LAVANDERIAS";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  // Si no se ha enviado datos muestre el formulario de selección de servicio e intervalo de fechas
  if (!isset($envio))
    {

	    // Consulta de las lavanderias
         $q = " SELECT Lavcod, Lavnom "
          ."   FROM ".$wbasedato."_000108 "
          ."  WHERE Lavest = 'on' "
          ."    AND (Lavind = 'A' "
          ."     OR Lavind = '') ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

	    //Parámetros de consulta del informe
  		if (!isset ($bandera))
  		{  			
 			$wfecha_i=$wfecha;
  			$wfecha_f=$wfecha;
			$wlav="";
		}
		else
		{
 			$wfecha_i=$wfecha_i;
  			$wfecha_f=$wfecha_f;
			$wlav=$wlav;
		}
  		
		echo "<table align='center' cellspacing='2'>";

		//Petición de selección de centro de costo o servicio
		echo "<tr class=fila1>";
        echo "<td align='enter' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Seleccione la lavandería:</b></div>";
	    echo "</td>";
	    echo "</tr>";

		// Campo select de centros de costos o servicios
	    echo "<tr class='fila2'><td align='center' colspan='2'>";
		echo "<select name='wlav' id='wlav'>";
		echo "<option>% - Todas las lavanderías</option>";
	    for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
		  if($wlav != $row[0]." - ".$row[1])
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		  else
			echo "<option selected>".$row[0]." - ".$row[1]."</option>";
         }
        echo "</select></td></tr>";
        echo "<tr><td colspan='2' height='11'></td></tr>";

		//Petición de ingreso de fechas a consultar
		echo "<tr class=fila1>";
        echo "<td align='enter' colspan='2'>";
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
	  echo "<tr><td align='center'><input type='submit' value='Consultar'> &nbsp; &nbsp; &nbsp; &nbsp; <input type='button' value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
	  echo "</table>";	  

	  echo "<input type='hidden' name='envio' id='envio' value='1'>";
	  
    } 
	else	// ACA INICIA LA IMPRESION DEL INFORME SEGUN LOS PARAMETROS DE CONSULTA
    {

		echo "<table align='center' cellspacing='2'>";
		echo "<tr class=fila2>";
      	echo "<td align='center' height='27px'> &nbsp; Lavandería: ";
  		echo $wlav." &nbsp; ";
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
		echo "<tr><td align='center' height='11px'> &nbsp; </td></tr>";
		echo "</table>";
		
		// Tabla principal con los datos del informe
		echo "<table align='center' cellspacing='2'>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='4'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wlav\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='4' height='21'></td></tr>";
		
		// Obtengo el código de la lavandería
		$lav = explode(" - ",$wlav);
		$wlav_cod = $lav['0'];

		// Borra la tabla temporal de recibos
		$qdel = "	DROP TABLE IF EXISTS tmp_movhos_000111 ";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());

		// Creación de la tabla temporal de movimientos de prendas de lavandería
		$qmov =  " CREATE TEMPORARY TABLE IF NOT EXISTS tmp_movhos_000111 "
				." ( INDEX idxfec ( Enlfec ), INDEX idxpre ( Molpre(10) ), INDEX idxlav ( Mollav(10) ), INDEX idxcon ( Molcon(10) ), INDEX idxron ( Molron(10) )     ) "
				." SELECT b.Fecha_data Fecha_data, Enlfec, Molcan, Molcon, Molron, Mollav, Molpre "
				." FROM ".$wbasedato."_000110, ".$wbasedato."_000111 b " 
				." WHERE Enlfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND Enlest = 'on'
					 AND Enllav LIKE '".$wlav_cod."' 
					 AND Enlfec = b.Fecha_data
					 AND Mollav = Enllav
					 AND Molcon = Enlcon
					 AND Molron = Enlron
					 AND Molest = 'on' ";
		$resmov = mysql_query($qmov, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qmov . " - " . mysql_error());
		
		// Consulta de los movimientos realizados para las fechas a consultar 
		$qlis = " SELECT Tcofac, Lavcod, Lavnom, Precod, Predes, Enlfec, Molcan, Molron, Ronjor
					FROM ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000102, 
						 ".$wbasedato."_000103, ".$wbasedato."_000108, tmp_movhos_000111
				   WHERE Contco = Tcocod
					 AND Tcoest = 'on'
					 AND Conest = 'on'
					 AND Conrep != 'on'
					 AND Roncon = Concod
					 AND Rontip = 'Externa'
					 AND Ronest = 'on'
					 AND Molcon = Concod
					 AND Molron = Roncod
					 AND Mollav = Lavcod
					 AND Molpre = Precod
					ORDER BY Mollav, Molpre, Enlfec, Ronhin, Molron";
        $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
        $numlis = mysql_num_rows($reslis);
		$rowlis = mysql_fetch_array($reslis);

		
		$j=1;	// Contador de registros de la consulta
		
		// Ciclo para recorrer todos los registros de la consulta
		while($j<=$numlis)
		{
			
			$k=1;	// Contador para el estilo de las filas
			$aux_lav = $rowlis['Lavcod'];	// Asigno lavanderia de recorrido actual

			// Espacio entre las filas por cada fecha
			echo "<tr><td colspan='4' height='21'></td></tr>";
			// Encabezado con la fecha actual
			echo "<tr class='encabezadoTabla'>";
			echo "<td align='enter' colspan='4'>";
			echo "<div align='center'> &nbsp; <b>".$rowlis['Lavnom']."</b> &nbsp; </div>";
			echo "</td>";
			echo "</tr>";
			
			// Titulos de las columnas
			echo "<tr class='encabezadoTabla'>";
			echo "<td align='enter'>";
			echo "<div align='center'> &nbsp; CODIGO &nbsp; </div>";
			echo "</td>";
			echo "<td align='enter'>";
			echo "<div align='center'> &nbsp; TIPO DE PRENDA &nbsp; </div>";
			echo "</td>";
			echo "<td align='enter'>";
			echo "<div align='center'> &nbsp; CUMPLIMIENTO &nbsp; </div>";
			echo "</td>";
			echo "<td align='enter'>";
			echo "<div align='center'> &nbsp; Nro. VECES &nbsp; </div>";
			echo "</td>";
			echo "</tr>";
			
			// Ciclo para recorrer los registros de cada fecha
			while($rowlis['Lavcod']==$aux_lav && $j<=$numlis) 
			{

				if (is_int ($k/2))
				   $wcf="fila1";  // color de fondo de la fila
				else
				   $wcf="fila2"; // color de fondo de la fila
				
				echo "<tr class='".$wcf."'>";
				echo "<td align='enter'>";
				echo "<div align='center'> &nbsp; ".$rowlis['Precod']." &nbsp; </div>";
				echo "</td>";
				echo "<td align='enter'>";
				echo "<div align='center'> &nbsp; ".$rowlis['Predes']." &nbsp; </div>";
				echo "</td>";
				echo "<td align='enter'>";
				echo "<div align='center'> &nbsp; 91-100% &nbsp; <br> &nbsp; 81%-90% &nbsp; <br> &nbsp; 1-80% &nbsp; <br> &nbsp; 0% &nbsp; </div>";
				echo "</td>";

				$cum_excelente = 0;
				$cum_bueno = 0;
				$cum_regular = 0;
				$cum_malo = 0;
				$aux_pre = $rowlis['Precod'];	// Asigno prenda de recorrido actual
				$aux_jor = $rowlis['Ronjor'];
				$aux_fac = $rowlis['Tcofac'];
				$aux_can = $rowlis['Molcan'];
				$l = 1;
				while($rowlis['Precod']==$aux_pre && $j<=$numlis) 
				{
					$cum = -1;
					if (is_int ($l/2))
					{
						if($aux_jor == $rowlis['Ronjor'])
						{
							if($aux_fac=='-1' && $rowlis['Tcofac']=='1')
							{
								if($rowlis['Molcan']>0)
									$cum = ($aux_can/$rowlis['Molcan'])*100;
								else
									$cum = 0;
							}
							elseif($aux_fac=='1' && $rowlis['Tcofac']=='-1')
							{
								if($aux_can>0)
									$cum = ($rowlis['Molcan']/$aux_can)*100;
								else
									$cum = 0;
							}
							if($cum >= 0 && $cum < 1)
								$cum_malo++;
							if($cum >= 1 && $cum < 81)
								$cum_regular++;
							if($cum >= 81 && $cum < 90)
								$cum_bueno++;
							if($cum >= 90)
								$cum_excelente++;
						}
						else
						{
							$l--;
						}
					}
					
					$aux_pre = $rowlis['Precod'];	// Asigno prenda de recorrido actual
					$aux_jor = $rowlis['Ronjor'];
					$aux_fac = $rowlis['Tcofac'];
					$aux_can = $rowlis['Molcan'];
					$rowlis = mysql_fetch_array($reslis);
					$j++;
					$l++;
				}
				echo "<td align='enter'>";
				echo "<div align='center'> &nbsp; ".$cum_excelente." &nbsp; <br> &nbsp; ".$cum_bueno." &nbsp; <br> &nbsp; ".$cum_regular." &nbsp; <br> &nbsp; ".$cum_malo." &nbsp; </div>";
				echo "</td>";
				echo "</tr>";
				$k++;
			}
			
		}
		
		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='4' height='21'></td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='4'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wlav\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  
	}	          
	   
  echo "<br>";
  echo "</form>";
  
} 

?>
</body>
</html>