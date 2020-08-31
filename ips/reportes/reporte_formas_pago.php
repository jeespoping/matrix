<html>
<head>
  <title>REPORTE DE FORMAS DE PAGO</title>
<script type="text/javascript">

	//Redirecciona a la pagina inicial
	function inicioReporte(wfecini,wfecfin,wcco,wemp_pmla,bandera){
	 	document.location.href='reporte_formas_pago.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wccocod='+wcco+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera;
	}

	function Seleccionar()
	{
		var fecini = document.forma.wfecini.value;
		var fecfin = document.forma.wfecfin.value;
	 
		//Valida que la fecha final sea mayor o igual a la incial
		if(!esFechaMenorIgual(fecini,fecfin))
		{
		   alert("La fecha inicial no puede ser mayor que la fecha final");
		   form.wfecini.focus();
		   return false;
		}

		document.forma.submit();
	}
</script>

</head>
<?php
include_once("conex.php");

  /******************************************************************************
   *     REPORTE DE FORMAS DE PAGO POR CENTRO DE COSTO Y RESPONSABLE		    *
   ******************************************************************************/
   
	/*--------------------------------------------------------------------------
	| DEDSCRIPCIÓN: Reporte de formas de pago realizadas en cada centro de 		|
	| costos y por cada caja con base en fecha inicial y fecha final			|
	| AUTOR: Mario Cadavid														|
	| FECHA DE CREACIÓN: Marzo 22 de 2011										|
	| FECHA DE ACTUALIZACIÓN: 													|
	----------------------------------------------------------------------------*/

	/*--------------------------------------------------------------------------
	| ACTUALIZACIONES															|
	|---------------------------------------------------------------------------|
	| FECHA:  																	|
	| AUTOR: 																	|
	| DESCRIPCIÓN:																|
	----------------------------------------------------------------------------*/

$wactualiz="1.0 | Marzo 22 de 2011";
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

//Validación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user']))
{
	$usuarioValidado = false;
}
else 
{
	if (strpos($user, "-") > 0)
		$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == "")
{
	$usuarioValidado = false;
}

session_start();

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("REPORTE FORMAS DE PAGO POR CENTRO DE COSTO",$wactualiz,"clinica");

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte
	
  //Conexion base de datos
  


  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "farpmla");
	
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wentidad=$row[0];
  
  echo "<form name='forma' action='reporte_formas_pago.php' method=post onSubmit='return valida_enviar(this);'>";
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' NAME= 'form' value='forma'>";

  // Si no se han enviado datos por el formulario
  if (!isset($form) or $form == '')
  {
	// Trae los datos de consultas anteriores
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
		 $wccocod="%-Todos los centros de costos";
	}
	
	//Inicio tabla de ingreso de parametros
 	echo "<table align='center' border='0' cellspacing='4' bordercolor='ffffff'>";
 	
 	//Petición de ingreso de parametros
 	echo "<tr>";
 	echo "<td height='37' colspan='5'>";
 	echo '<p align="left" class="titulo"><strong> &nbsp; Seleccione los datos a consultar &nbsp;  &nbsp; </strong></p>';
 	echo "</td></tr>";
		
 	//Solicitud fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=221 align=right> &nbsp; Fecha inicial &nbsp; </td>";
 	echo "<td class='fila2' align='left' width=171>";
 	campoFechaDefecto("wfecini",$wfecini);
 	echo "</td>";
	echo "</tr>";
 		
 	//Solicitud fecha final
 	echo "<tr>";
 	echo "<td class='fila1' align=right> &nbsp; Fecha final &nbsp; </td>";
 	echo "<td class='fila2' align='left' width='141'>";
 	campoFechaDefecto("wfecfin",$wfecfin);
 	echo "</td>";
 	echo "</tr>";
	
	echo "<tr>";  

	//SELECCIONAR CENTRO DE COSTOS
	if (isset($wccocod))
	{
		echo "<td class=fila1 align=right> &nbsp; Centro de costos &nbsp; </td>";
		echo "<td class=fila2>";
		echo "<select name='wccocod'>";   
  		// Consulto los centros de costos donde se generan facturas
  		$q= "   SELECT ccocod, ccodes "
 	       ."     FROM ".$wbasedato."_000003, ".$wbasedato."_000040 "
 	       ."    WHERE Ccoffa=Carfue"
 	       ."      AND Carfac='on'"
 	       ."    ORDER by 1"; 	
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);  
		
  		if ($num1 > 0 )
      	  {
      		if (isset($wccocod) && $wccocod=='%')
			   echo "<option selected>%-Todos los centros de costos</option>";
			else
			   echo "<option>%-Todos los centros de costos</option>";
		    for ($i=1;$i<=$num1;$i++)
	           {
	            $row1 = mysql_fetch_array($res1); 
				if(isset($wccocod) && $row1[0]==$wccocod)
					echo "<option selected>".$row1[0]." - ".$row1[1]."</option>"; 
	            else
					echo "<option>".$row1[0]." - ".$row1[1]."</option>";
	           } 
          }
     	echo "</select></td></tr>"; 
	}
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";	
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
	

	echo "</table></br>";	   

	echo "<p align='center'><input type='button' id='searchsubmit' value='Consultar' onclick='Seleccionar()'> &nbsp; | &nbsp; <input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
	
  } 

//RESULTADO DE CONSULTA DEL REPORTE
else
  {
	
	// si el centro de costos es diferente a todas los centros de costos, la meto en el vector solo
	// si son todos los costos los meto todos en un vector para luego preguntarlos en un for 
		
	if ($wccocod !='%-Todos los centros de costos')
	{
		$wcco=explode('-', $wccocod);
		$wccostos[0]=trim ($wcco[0]);
		$num1=1;
    }	
	else
    { 
	   $wccostos='%';
	   $num1=2;
	}
	$aux1=$wccostos[0];

	//Inicio tabla de resultados
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
 	
	// Subtítulo del reporte
 	echo "<tr>";
	echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Listado de formas de pago por centro de costos y por caja &nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";

	//Muestro los parámetros que se ingresaron en la consulta
    echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Fecha inicial: </strong>&nbsp;".$wfecini." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Fecha final: </strong>&nbsp;".$wfecfin." &nbsp; </td>";
    echo "</tr>";
    echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Centro de costos: </strong>&nbsp;".$wccocod." &nbsp; </td>";
    echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>";

	// Botones de "Retornar" y "Cerrar ventana"
	echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$aux1\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
 	echo "</td></tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
    echo "</table>";

    echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
    echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
    echo "<input type='HIDDEN' NAME= 'wccocod' value='".$wccocod."'>";
    echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	/***********************************Consulto lo pedido ********************/

	// QUERY PRINCIPAL DEL REPORTE
	$q = "	SELECT ".$wbasedato."_000022.fecha_data, ".$wbasedato."_000022.hora_data, rfpnum, ccocod, ccodes, fpades, rfpvfp, cajcod, cajdes, rdefac, fenval "
		."	FROM ".$wbasedato."_000022, ".$wbasedato."_000028, ".$wbasedato."_000023, "
		." 		 ".$wbasedato."_000021, ".$wbasedato."_000018, ".$wbasedato."_000003  "
		."	WHERE ".$wbasedato."_000022.fecha_data BETWEEN '".$wfecini."' "
		."    AND '".$wfecfin."'"
		."	  AND rfpest = 'on' "
		."	  AND fencco LIKE '".$wccostos[0]."' "
		."	  AND fencco = ccocod "
		."	  AND cajcod = rfpcaf "
		."	  AND rfpfpa = fpacod "
		."	  AND rfpfue = rdefue "
		."	  AND rfpnum = rdenum "
		."	  AND rfpcco = rdecco "
		."	  AND rdeest = 'on' "
		."	  AND rdefac = fenfac "
		."	  ORDER BY fencco, cajcod, farpmla_000022.fecha_data, farpmla_000022.hora_data  ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	$wtotal = 0;
	$wtotsal= 0;
	$bandera1=0;
	$bandera2=0;
	$wtotfac=0;
	$wsaldo=0;
	$wtotgenfac=0;
	$wtotgensal=0;
	$clase='fila1';
	$k=1;
	$i=1;

	// Creación de tabla donde se muestra el resultado de la consulta
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
   
	// Inicio del ciclo general de los resultados de la consulta
	while ($i <= $num) 
		{
			$row = mysql_fetch_array($err);
			
			if ($bandera1==0) 
				{
					$wccocod=$row['ccocod'];
					$wccodes=$row['ccodes'];
				}
			if ($bandera2==0)
			 	{
		  			$wcajacod=$row['cajcod'];
		  			$wcajanom=$row['cajdes'];
		 		}
		 	
			// Si cambia la caja o responsable muestre el total de la anterior
			if (($wcajacod!=$row['cajcod']) or ($wcajacod==$row['cajcod'] and $wccocod!=$row['ccocod']))
		 		{
					echo "<tr class='encabezadoTabla'>";
					echo "<td align=left colspan=6><strong> &nbsp; Total responsable </strong> &nbsp; </td>";
					echo "<td align=right><strong>".number_format($wtotfac,0,'.',',')." </strong></td>";
					echo "<td align=right><strong>".number_format($wsaldo,0,'.',',')."</strong> &nbsp; </td>";
					echo "</tr>";
		    		$wtotal = $wtotal+$wtotfac;
					$wtotsal = $wtotsal+$wsaldo;
					$wtotfac=0;
					$wsaldo=0;
		 		}		
			// Si cambia el centro de costos muestre el total del anterior
			if ($wccocod!=$row['ccocod'])
				{	
					echo "<tr><td align='left' colspan='8'>&nbsp;</td></tr>";   
					echo "<tr class='encabezadoTabla' height='31'>";
					echo"<td align=left colspan='6'>&nbsp;TOTAL CENTRO DE COSTOS</td>";
					echo "<td align=right >".number_format($wtotal,0,'.',',')."</td>";
					echo "<td align=right >".number_format($wtotsal,0,'.',',')."</td></tr>";
					echo "<tr><td align='left' colspan='8'>&nbsp;</td></tr>";   
						
					$wtotal=0;
					$wtotsal=0;
				}
			// Se define si se muestra el título del centro de costos
			if (($bandera1==0) or ($wccocod!=$row['ccocod']))
				{   
					$waux=$wccocod;
					$wccocod=$row['ccocod'];
					$wccodes=$row['ccodes'];
					$bandera1=1;
					$pinto=0;	// Define si muestro el encabezado de la tabla
					echo "<tr><td align='left' colspan='8'>&nbsp;</td></tr>";   
					echo "<tr><td align='left' class='encabezadoTabla' colspan='8' height='31'>&nbsp;CENTRO DE COSTOS: ".$wccocod." - ".$wccodes."</td></tr>";   
				}
			 
		 	// Se define si se muestra el título de la caja o responsable
			if (($bandera2==0) or ($wcajacod!=$row['cajcod']) or ($wcajacod=$row['cajcod'] and $waux!=$row['ccocod']) )			 	
		 		{	
				 	$wcajacod=$row['cajcod'];
		  			$wcajanom=$row['cajdes'];
		  			$bandera2=1;
		  			$pinto=0;
		  			$waux=$row['ccocod'];
					echo "<tr><td align='left' colspan='8'>&nbsp;</td></tr>";   
					echo "<tr><td colspan=9 valign='bottom' class='textoNormal'><b> RESPONSABLE: Cod. ".$wcajacod." | ".$wcajanom."</b></td></tr>";   
		  			
	  			}
			  	
				// Se establece la clase para la fila en el ciclo actual
				if (is_int ($k/2))
					$clase='fila1';
				else
					$clase='fila2';
				$k=$k+1;

				// Pinta el encabezado de los valores de la tabla
				if ($pinto==0)
					{
						echo "<tr class='encabezadoTabla'>";
						echo "<td align=CENTER>Caja</td>";
						echo "<td align=CENTER>Forma de pago</td>";
						echo "<td align=CENTER>Fecha</td>";
						echo "<td align=CENTER>Nro recibo</td>";
						echo "<td align=CENTER>Centro de costos</td>";
						echo "<td align=CENTER>Nro factura</td>";
						echo "<td align=CENTER>Valor</td>";
						echo "<td align=CENTER>Total</td>";			        				
						echo "</tr>";
						$pinto=1;
					}														
					
					// Si el valor pagado es diferente al valor total se destaca la fila en amarillo
					if($row['rfpvfp']!=$row['fenval'])
						$clase = 'fondoAmarillo';
					
					// Pinta los valores para las filas
					echo '<tr>';
					echo "<td align=right class=".$clase.">".$row['cajdes']."</td>";
					echo "<td align=right class=".$clase.">".$row['fpades']."</td>";
					echo "<td align=right class=".$clase.">".$row['fecha_data']."</td>";
					echo "<td align=right class=".$clase.">".$row['rfpnum']."</td>";
					echo "<td align=right class=".$clase.">".$row['ccocod']." - ".$row['ccodes']."</td>";
					echo "<td align=right class=".$clase.">".$row['rdefac']."</td>";
					echo "<td align=right class=".$clase.">".number_format($row['rfpvfp'],0,'.',',')."</td>";
					echo "<td align=right class=".$clase.">".number_format($row['fenval'],0,'.',',')."</td>";
					echo '</tr>';

				// Asigno los totales para responsable, centro de costos y total general
				$wtotgenfac=$wtotgenfac + $row['rfpvfp'];
				$wtotgensal=$wtotgensal + $row['fenval'];
				$wtotfac = $wtotfac+$row['rfpvfp'];
				$wsaldo = $wsaldo+$row['fenval'];
				$i= $i + 1;
		}
		
		// Si no se tienen resultados se muestra el mensaje correspondiente
		if ($wtotfac==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=570 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><b>Sin ningún documento en el rango de fechas seleccionado</b></td><tr>";
			}
		// Muestra el total del responsable o caja y del centro de costos
		// Esto debido a que en el último ciclo estos no se muestran
		else
			{	

				echo "<tr class='encabezadoTabla'>";
				echo "<td align=left colspan=6><strong> &nbsp; Total responsable </strong> &nbsp; </td>";
				echo "<td align=right><strong>".number_format($wtotfac,0,'.',',')." </strong></td>";
				echo "<td align=right><strong>".number_format($wsaldo,0,'.',',')."</strong> &nbsp; </td>";
				echo "</tr>";
			
				$wtotal = $wtotal+$wtotfac;
				$wtotsal = $wtotsal+$wsaldo;

				echo "<tr><td align='left' colspan='8'>&nbsp;</td></tr>";   
				echo "<tr><td align=left class='encabezadoTabla' colspan='6' height='31'>&nbsp;TOTAL CENTRO DE COSTOS </td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wtotal,0,'.',',')."</td>";
				echo "<td align=right class='encabezadoTabla'>".number_format($wtotsal,0,'.',',')."</td></tr>";
			}
			 
			// Muestra el total general del reporte
			if ($num1==2 and $wtotgenfac != 0)
				{	
					echo "<tr><td align='left' colspan='8'>&nbsp;</td></tr>";   
					echo "<td align=left class='encabezadoTabla' colspan='6' height='37'>&nbsp;TOTAL GENERAL</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgenfac,0,'.',',')."</td>";
					echo "<td align=right class='encabezadoTabla'>".number_format($wtotgensal,0,'.',',')."</td></tr>"; 	
				}
		echo "</table>";
		$bandera=1;
		
		// Botones de "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$aux1\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
	}
}
?>
</body>
</html>