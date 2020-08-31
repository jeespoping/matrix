<html>
<head>
  <title>INFORME PACIENTES ATENDIDOS POR PROFESIONAL</title>
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
function retornar(wempresa,wfecha_i,wfecha_f,wpro)
	{
		location.href = "rep_pacientes_por_profesional.php?wempresa="+wempresa+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&wpro="+wpro+"&bandera=1";
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
  /**********************************************************
   *   INFORME PACIENTES ATENDIDOS POR PROFESIONAL   		*
   **********************************************************/
	/*
	 ********** DESCRIPCIÓN *********************************
	 * Muestra el número de pacientes atendidos por  		*
	 * cada profesional, es decir, el número de pacientes	*
	 * que sí asistió a las citas							*
	 ********************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-11-15						*
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
  $wactualiz = " Nov. 15 de 2011";
                                                   
  echo "<br>";				
  echo "<br>";

  //**********************************************//
  //********** P R I N C I P A L *****************//
  //**********************************************//

  // Obtengo los datos de la empresa
  $wbasedato = $wempresa;

  // Se define el formulario principal de la página
  echo "<form name='form1' id='form1' action='' method='post'  onSubmit='return valida_envio(this);'>";
  
  // Asignación de fecha y hora actual
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  // Definición de campos ocultos con los valores de las variables a tener en cuenta en el programa
  echo "<input type='hidden' name='wempresa' value='".$wempresa."'>";
  echo "<input type='hidden' name='wseguridad' id='wseguridad' value='".$wusuario."'>";
	
  // Obtener titulo de la página con base en el concepto
  $titulo = "REPORTE DE PACIENTES ATENDIDOS POR PROFESIONAL";
	
  // Se muestra el encabezado del programa
  encabezado($titulo,$wactualiz, "clinica");  

	// Borra la tabla temporal
	$qdel = "DROP TABLE IF EXISTS tmp_".$wbasedato."_000010 ";
	$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());
	
	// Creación de tabla temporal
	$qord =  " CREATE TEMPORARY TABLE IF NOT EXISTS tmp_".$wbasedato."_000010 "
			." (INDEX idx ( Codigo ) ) "
			." SELECT Codigo, Descripcion "
			."   FROM ".$wbasedato."_000010 "
			."  WHERE Activo = 'A'"
			."  GROUP BY Codigo";
	//echo $qord."<br />";	
	$resord = mysql_query($qord, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qord . " - " . mysql_error());
  
  // Si no se ha enviado datos muestre el formulario ingreso de datos
  if (!isset($envio))
    {

	    // Consulta los profesionales
         $q = " SELECT Codigo, Descripcion "
          ."   	  FROM tmp_".$wbasedato."_000010 ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);

	    //Parámetros de consulta del informe
  		if (!isset ($bandera))
  		{  			
 			$wfecha_i=$wfecha;
  			$wfecha_f=$wfecha;
			$wpro="";
		}
		else
		{
 			$wfecha_i=$wfecha_i;
  			$wfecha_f=$wfecha_f;
			$wpro=$wpro;
		}
  		
		echo "<table align='center' cellspacing='2'>";

		//Petición de selección de lavandería
		echo "<tr class=fila1>";
        echo "<td align='enter' colspan='2'>";
        echo "<div align='center' class='fila1'><b>Seleccione el profesional:</b></div>";
	    echo "</td>";
	    echo "</tr>";

		// Campo select de lavanderías
	    echo "<tr class='fila2'><td align='center' colspan='2'>";
		echo "<select name='wpro' id='wpro'>";
		echo "<option value='% - Todos'>---- Todos ----</option>";
	    for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
		  if($wpro != $row[0]." - ".$row[1])
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
      	echo "<td align='center' height='27px'><b> &nbsp; Profesional: ";
  		if($wpro!="% - Todos")
			echo $wpro." &nbsp; ";
		else
			echo " Todos &nbsp; ";
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
		echo "<tr><td align='center' colspan='3'><input type=button value='Retornar' onclick='retornar(\"$wempresa\",\"$wfecha_i\",\"$wfecha_f\",\"$wpro\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";

		// Espacio entre los botones superiores y las filas con los datos
		echo "<tr><td colspan='3' height='21'></td></tr>";
		
		// Encabezado de la tabla
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='enter'>";
		echo "<div align='center'> &nbsp; Código &nbsp; </div>";
		echo "</td>";
		echo "<td align='enter'>";
		echo "<div align='center'> &nbsp; Nombre Profesional &nbsp; </div>";
		echo "</td>";
		echo "<td align='enter'>";
		echo "<div align='center'> &nbsp; Citas atendidos &nbsp; </div>";
		echo "</td>";
	    echo "</tr>";

		// Obtengo el código del profesional
		$pro = explode("-",$wpro);
		$wpro_cod = trim($pro['0']);

		 // Consulta de los pacientes atendidos
		$qlis = " SELECT Codigo, Descripcion, COUNT(Cedula) AS Pacientes
					FROM ".$wbasedato."_000009 a, tmp_".$wbasedato."_000010 b
				   WHERE a.Fecha BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' 
					 AND Cod_equ LIKE '".$wpro_cod."' 
					 AND Cod_equ = Codigo
					 AND Asistida = 'on'
					 AND Activo = 'A'
				GROUP BY Cod_equ";
        $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
        $numlis = mysql_num_rows($reslis);
		$rowlis = mysql_fetch_array($reslis);
		
		$j = 1;	// Contador de registros de la consulta
		$tot_pac = 0; // Total de pacientes atendidos
		
		// Ciclo para imprimir las filas con los datos del informe
		while($j<=$numlis)
		{
			if (is_int ($j/2))
			   $wcf="fila1";  // color de fondo de la fila
			else
			   $wcf="fila2"; // color de fondo de la fila
			
			// Datos por fila
			echo "<tr class='".$wcf."'>";
			echo "<td align='center'> &nbsp; <b>".$rowlis['Codigo']."</b> &nbsp; </td>";
			echo "<td align='left'> &nbsp; <b>".$rowlis['Descripcion']."</b> &nbsp; </td>";
			echo "<td align='center'> &nbsp; <b>".$rowlis['Pacientes']."</b> &nbsp; </td>";
			echo "</tr>";
			
			$tot_pac += $rowlis['Pacientes'];
			$rowlis = mysql_fetch_array($reslis);
			$j++;
		}
		
		// Total pacientes atendidos
		echo "<tr class='encabezadoTabla'>";
		echo "<td align='center' colspan='2'> &nbsp; <b> Total </b> &nbsp; </td>";
		echo "<td align='center'> &nbsp; <b>".$tot_pac."</b> &nbsp; </td>";
		echo "</tr>";

		// Espacio entre las filas con los datos y los botones inferiores
		echo "<tr><td colspan='3' height='21'></td></tr>";

		// Botones Retornar y Cerrar Ventana
		echo "<tr><td align='center' colspan='3'><input type=button value='Retornar' onclick='retornar(\"$wempresa\",\"$wfecha_i\",\"$wfecha_f\",\"$wpro\")'> &nbsp;  &nbsp;  &nbsp;  &nbsp; <input type=button value='Cerrar Ventana' onclick='cerrar_ventana()'></td></tr>";
		echo "</table>";	  
	}	          
	   
  echo "<br>";
  echo "</form>";
  
} 

?>
</body>
</html>