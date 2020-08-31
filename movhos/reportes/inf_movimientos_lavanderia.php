<html>
<head>
  <title>INFORME DE MOVIMIENTOS A LAVANDERIAS</title>
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
function retornar(wemp_pmla,wfecha_i,wfecha_f,wlav,wconcepto,wprenda)
	{
		location.href = "inf_movimientos_lavanderia.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&wlav="+wlav+"&wconcepto="+wconcepto+"&wprenda="+wprenda+"&bandera=1";
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
   *   INFORME DE MOVIMIENTOS A LAVANDERIAS		   		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *****************************************************************
	 * Muestra todos los movimientos generados en la aplicación de roperia a nivel externo 	*
	 * es decir, los movimientos de prendas hacia y desde lavanderias.						*
	 ****************************************************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-06-15						*
	 * Modificado: 										*
	 * Aca se ponen los comentarios de las 				*
	 * modificaciones del programa						*
	 ****************************************************
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
  $wactualiz = " Jun. 14 de 2011";
                                                   
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
  echo "<form name='form1' id='form1' action='inf_movimientos_lavanderia.php' method='post'  onSubmit='return valida_envio(this);'>";
  
  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  // Obtener titulo de la página con base en el concepto
  $titulo = "INFORME DE MOVIMIENTOS A LAVANDERIAS";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  // Si no se ha enviado datos muestre el formulario de selección de servicio e intervalo de fechas
  if (!isset($envio))
    {

	  // Consulta de lavanderias
      $q = " SELECT  Lavcod, Lavnom "
          ."   FROM ".$wbasedato."_000108 "
          ."  WHERE Lavest = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);

	  // Consulta de conceptos
      $qcon = " SELECT Concod, Condes "
          ."   FROM ".$wbasedato."_000101, ".$wbasedato."_000102 "
          ."  WHERE Rontip = 'Externa' "
          ."  	AND Roncon = Concod "
          ."  GROUP BY Roncon "
		  ."  ORDER BY Concod ";
      $rescon = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
      $numcon = mysql_num_rows($rescon);

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
			$wlav="";
			$wconcepto="";
			$wprenda="";
		}
  		
		echo "<table align='center' cellspacing='2'>";

		//Petición de selección de centro de costo o servicio
		echo "<tr>";
        echo "<td class=fila1 align='center' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Seleccione la lavanderia:</b></div>";
	    echo "</td>";
        echo "<td align='center' width='37px'>&nbsp;</td>";
        echo "<td class=fila1 align='center'>";
        echo "<div align='center' class='fila1'><b>Seleccione el concepto:</b></div>";
	    echo "</td>";
	    echo "</tr>";

		// Campo select de lavanderias
	    echo "<tr>";
		echo "<td class='fila2' align='center' colspan='2'>";
		echo "<select name='wlav' id='wlav'>";
		echo "<option>% - Todas las lavanderias</option>";
	    for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
		  if($wlav != $row[0]." - ".$row[1])
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		  else
			echo "<option selected>".$row[0]." - ".$row[1]."</option>";
         }
        echo "</select>";
		echo "</td>";
        echo "<td align='center' width='37px'>&nbsp;</td>";
        echo "<td class='fila2' align='center'>";
		echo "<select name='wconcepto' id='wconcepto'>";
		echo "<option>% - Todos los conceptos</option>";
	    for ($i=1;$i<=$numcon;$i++)
	     {
	      $rowcon = mysql_fetch_array($rescon); 
		  if($wconcepto != $rowcon[0]." - ".$rowcon[1])
			echo "<option>".$rowcon[0]." - ".$rowcon[1]."</option>";
		  else
			echo "<option selected>".$rowcon[0]." - ".$rowcon[1]."</option>";
         }
        echo "</select>";
	    echo "</td>";
		echo "</tr>";
        echo "<tr><td colspan='4' height='11'></td></tr>";

		//Petición de ingreso de fechas a consultar
		echo "<tr>";
        echo "<td class=fila1 align='center' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Ingrese las Fechas a consultar:</b></div>";
	    echo "</td>";
        echo "<td align='center'>&nbsp;</td>";
        echo "<td class=fila1 align='center'>";
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
        echo "<td class=fila2 align='center'>";
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

		echo "<table align='center' cellspacing='2'>";
		echo "<tr>";
      	echo "<td class=fila2 colspan='2' align='center' height='27px'> &nbsp; Lavanderia:";
  		echo $wlav." &nbsp; ";
	    echo "</td>";
      	echo "<td width='37px'>&nbsp;</td>";
      	echo "<td class=fila2 align='center' height='27px'> &nbsp; Concepto: ";
  		echo $wconcepto." &nbsp; ";
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
		echo "<tr><td align='center' colspan='14'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wlav\",\"$wconcepto\",\"$wprenda\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='4' height='21'></td></tr>";
		
		// Obtengo el código del servicio
		$lav = explode(" - ",$wlav);
		$wlav_cod = $lav['0'];
		
		// Obtengo el código del concepto
		$con = explode(" - ",$wconcepto);
		$wcon_cod = $con['0'];

		// Obtengo el código del servicio
		$pre = explode(" - ",$wprenda);
		$wpre_cod = $pre['0'];

		// Consulta de los movimientos realizados para las fechas a consultar 
		$qlis = " SELECT A.Fecha_data, A.Hora_data, Enlfec, Enlhor, Enlcon, Condes, Enllav, Lavnom, Enlron, Ronnom, Enlpmo, Enlpba, Enlpal, A.Seguridad, Molpre, Predes, Molcan, Molsal
					FROM ".$wbasedato."_000110 A, ".$wbasedato."_000111 B, ".$wbasedato."_000101, ".$wbasedato."_000102, ".$wbasedato."_000103, ".$wbasedato."_000108
				   WHERE Enlfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND Enllav LIKE '".$wlav_cod."' 
					 AND Enllav = Lavcod 
					 AND Enlron = Roncod 
					 AND Enlcon LIKE '".$wcon_cod."' 
					 AND Enlcon = Roncon 
					 AND Enlfec = B.Fecha_data 
					 AND Enllav = Mollav 
					 AND Enlron = Molron 
					 AND Enlcon = Molcon 
					 AND Molpre LIKE '".$wpre_cod."' 
					 AND Molpre = Precod 
					 AND Roncon = Concod 
					 AND Prelav = Lavcod
					 AND Enlest = 'on' 
					 AND Molest = 'on' 
				GROUP BY Enlfec, Enlhor, Enlcon, Enlron, Enllav, Molpre
				ORDER BY Enlfec, Enlhor, Enllav, Enlcon, Enlron ";
	
        $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
        $numlis = mysql_num_rows($reslis);
		$rowlis = mysql_fetch_array($reslis);
		//echo $qlis."<br>";
		
		// Titulos de las columnas
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Fecha registro &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Hora registro &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Fecha movimiento &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Hora movimiento &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Prenda &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Lavanderia &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Concepto &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Ronda &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Cantidad &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Saldo antes de movimiento &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Peso prendas mojadas &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Peso prendas baja suciedad &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Peso prendas alta suciedad &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; Usuario &nbsp; </div>";
		echo "</td>";
		echo "</tr>";

		
		$j=1;	// Contador de registros de la consulta
		$sum_can = 0;
		$sum_exi = 0;
		$sum_moj = 0;
		$sum_baj = 0;
		$sum_alt = 0;

		// Ciclo para recorrer todos los registros de la consulta
		while($j<=$numlis)
		{
			
			if (is_int ($j/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila
			
			// Calculo el porcentaje de cumplimiento
			$sum_can += $rowlis['Molcan'];
			$sum_exi += $rowlis['Molsal'];
			$sum_moj += $rowlis['Enlpmo'];
			$sum_baj += $rowlis['Enlpba'];
			$sum_alt += $rowlis['Enlpal'];
			   
			echo "<tr class='".$wcf."'>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Fecha_data']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Hora_data']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlfec']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlhor']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Molpre']." - ".$rowlis['Predes']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enllav']." - ".$rowlis['Lavnom']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlcon']." - ".$rowlis['Condes']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlron']." - ".$rowlis['Ronnom']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Molcan']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Molsal']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlpmo']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlpba']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Enlpal']." &nbsp; </div>";
			echo "</td>";
			echo "<td align='center'>";
			echo "<div align='center'> &nbsp; ".$rowlis['Seguridad']." &nbsp; </div>";
			echo "</td>";
			echo "</tr>";

			$rowlis = mysql_fetch_array($reslis);
			$j++;
		}

		// Titulos de las columnas
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='center' colspan='8'>";
		echo "<div align='center'> &nbsp; TOTAL &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; ".$sum_can." &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; ".$sum_exi." &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; ".$sum_moj." &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; ".$sum_baj." &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; ".$sum_alt." &nbsp; </div>";
		echo "</td>";
		echo "<td align='center'>";
		echo "<div align='center'> &nbsp; </div>";
		echo "</td>";
		echo "</tr>";
		
		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='14' height='21'></td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='14'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wlav\",\"$wconcepto\",\"$wprenda\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  
		echo "</div>";
	}	          
	   
  echo "<br>";
  echo "</form>";
  
} 

?>
</body>
</html>