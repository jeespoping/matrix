<html>
<head>
  <title>INFORME HORARIOS RECORRIDOS LAVANDERIAS</title>
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
		location.href = "inf_horarios_lavanderia.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&wlav="+wlav+"&bandera=1";
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
   *   INFORME HORARIOS RECORRIDOS LAVANDERIAS   		*
   ******************************************************/
	/*
	 ********** DESCRIPCIÓN *****************************
	 * Muestra el horario por fechas de los recorridos 	*
	 * hechos de ropa sucia y limpia para la lavandería.*
	 ****************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-04-28						*
	 * Modificado: 										*
	 * Aca se ponen los comentarios de las 				*
	 * modificaciones del programa						*
	 ****************************************************
	 * Cambia el orden del encabezado de la tabla del reporte así: Envio sucia (S1), 
	 * Recibo limpia (s1), Envio Sucia (S2), Recibo Limpia (S2), Envio Sucia (S3), Recibo Limpia (S3).
	 * ordenando por el orden de la ronda y el código : queries en lineas 234 y 312 - Santiago Rivera Botero	*/
   
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
  $wactualiz = " FEB. 29 de 2012";
                                                   
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
	  
	  if($num > 0 )
	  {
		  for($i=1;$i<=$num;$i++)
		     {   
		       $row = mysql_fetch_array($res);
		      
		       if($row[0] == "movhos")
		          $wbasedato=$row[1];

			   if($row[0] == "tabcco")
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
  echo "<form name='form1' id='form1' action='inf_horarios_lavanderia.php' method='post'  onSubmit='return valida_envio(this);'>";
  
  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  // Obtener titulo de la página con base en el concepto
  $titulo = "INFORME HORARIOS RECORRIDOS LAVANDERIAS";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

  // Si no se ha enviado datos muestre el formulario de selección de lavanderia e intervalo de fechas
  if(!isset($envio))
    {

	    // Consulta de las lavanderias
        $q ="SELECT Lavcod, Lavnom "
          ."   FROM ".$wbasedato."_000108 "
          ."  WHERE Lavest = 'on' "
          ."    AND (Lavind = 'A' "
          ."     OR Lavind = '') ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

	    //Parámetros de consulta del informe
  		if(!isset ($bandera))
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

		//Petición de selección de lavandería
		echo "<tr class=fila1>";
        echo "<td align='enter' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Seleccione la Lavandería:</b></div>";
	    echo "</td>";
	    echo "</tr>";

		// Campo select de lavanderías
	    echo "<tr class='fila2'><td align='center' colspan='2'>";
		echo "<select name='wlav' id='wlav'>";
	    for($i=1;$i<=$num;$i++)
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

	    // Consulta de rondas
        $q = " 	SELECT Roncod, Ronnom, Ronjor, Tcofac "
			."    FROM ".$wbasedato."_000102, ".$wbasedato."_000100, ".$wbasedato."_000101 "
			."   WHERE Rontip = 'Externa' "
			."     AND Ronest = 'on' "
			."     AND Roncon = Concod "
			."     AND Conest = 'on' "
			."     AND Conrep != 'on' "
			."     AND Contco = Tcocod "
			."     AND Tcoest = 'on' "
			."   ORDER BY Ronord ASC,Roncod ASC ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		
		$numcol = $num+1;

		echo "<table align='center' cellspacing='2'>";
		echo "<tr class=fila2>";
      	echo "<td align='center' height='27px'><b> &nbsp; Lavandería: ";
  		echo $wlav." &nbsp; ";
	    echo "</b></td>";
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
		echo "<tr><td align='center' colspan='".$numcol."'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wlav\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='".$numcol."' height='21'></td></tr>";
		
		// Encabezado de la tabla
		echo "<tr class='encabezadoTabla'>";

		echo "<td align='enter'>";
		echo "<div align='center'> &nbsp; Fecha &nbsp; </div>";
		echo "</td>";

		// Arreglo que permitirá llevar los datos de las rondas
		$recorridos = array();

		// Ciclo para llenar el encabezado de la tabla con las rondas consultados
	    for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res); 
			echo "<td align='enter'>";
			echo "<div align='center'> &nbsp; ".$row['Ronnom']." &nbsp; </div>";
			echo "</td>";
			$recorridos[$i] = $row['Roncod'];
		}
	    echo "</tr>";

		// Obtengo el código de la lavandería
		$lav = explode("-",$wlav);
		$wlav_cod = $lav['0'];
		
		// Consulta de los movimientos realizados para la lavandería y fechas a consultar 
		$qlis = " SELECT Enlfec, Enlhor, Enlcon, Enllav, Enlron, Roncod, Ronhin, 
						 Ronhfi, Ronnom, Roncon, Rontip
					FROM ".$wbasedato."_000102, ".$wbasedato."_000110
				   WHERE Enlfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND Enlest = 'on' 
					 AND Enllav = '".$wlav_cod."' 
					 AND Enlron = Roncod
				   ORDER BY Enlfec, Ronord,Roncod, Roncon";	
        $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
        $numlis = mysql_num_rows($reslis);
		$rowlis = mysql_fetch_array($reslis);
		
		$j=1;	// Contador de registros de la consulta
		$k=1;	// Contador para el estilo de las filas
		
		// Arreglo que contendrá los datos de cada fila del informe
		$movimientos = array();
	    
		// Ciclo para imprimir las filas con los datos del informe
		while($j<=$numlis)
		{
			if (is_int ($k/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila
			
			$aux_fecha = $rowlis['Enlfec'];
			// Ciclo para asignar los datos de la fila al arreglo
			while($rowlis['Enlfec']==$aux_fecha && $j<=$numlis) 
			{
				$ronda = $rowlis['Roncod'];
				$movimientos[$ronda] = $rowlis['Enlhor'];
				$rowlis = mysql_fetch_array($reslis);
				$j++;
			}
			
			echo "<tr class='".$wcf."'>";

			// Celda con la fecha para cada recorrido
			echo "<td align='enter'>";
			echo "<div align='center'> &nbsp; <b>".$aux_fecha."</b> &nbsp; </div>";
			echo "</td>";
			
			// Ciclo que imprime los datos de la fila 
			// Con base en los arreglos $recorridos y $movimientos
			for ($i=1;$i<=$num;$i++)
			{
				$ronda = $recorridos[$i];
				echo "<td align='enter'>";
				if(isset($movimientos[$ronda]) && $movimientos[$ronda])
					echo "<div align='center'> &nbsp; ".$movimientos[$ronda]." &nbsp; </div>";
				else
					echo "<div align='center'>&nbsp;</div>";
				echo "</td>";
			}
			echo "</tr>";
			$k++;
			
			// Se reinicia el arreglo para la nueva fila
			unset($movimientos);
			$movimientos = array(); 
		}
		
		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='".$numcol."' height='21'></td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='".$numcol."'><input type=button value='Retornar' onclick='retornar(\"$wemp_pmla\",\"$wfecha_i\",\"$wfecha_f\",\"$wlav\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  
	}	          
	   
  echo "<br>";
  echo "</form>";
  
} 

?>
</body>
</html>