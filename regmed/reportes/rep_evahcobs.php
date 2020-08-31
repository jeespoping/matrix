<html>
<head>
  <title>REPORTE DE EVALUACION HISTORIAS CLINICAS</title>
<script type="text/javascript">

	//Redirecciona a la pagina inicial
	function inicioReporte(wfecini,wfecfin,wcco,wemp_pmla,bandera){
	 	document.location.href='rep_evahcobs.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wccocod='+wcco+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera;
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
	function cerrarVentana()
	{
	 window.close()
	}
</script>

</head>
<?php
include_once("conex.php");

  /******************************************************************************
   *     REPORTE DE EVALUACION HISTORIAS CLINICAS							    *
   ******************************************************************************/
   
	/*--------------------------------------------------------------------------
	| DEDSCRIPCIÓN: Reporte de evaluacion de las historias clinicas Serv Observacion |
	| Urgencias/Emergencias con base en fecha inicial y fecha final	     		     |
	| AUTOR: Gabriel Agudelo												         |
	| FECHA DE CREACIÓN: Febrero 17 de 2016										     |
	| FECHA DE ACTUALIZACIÓN: 													     |
	--------------------------------------------------------------------------------*/

	/*--------------------------------------------------------------------------
	| ACTUALIZACIONES															|
	|---------------------------------------------------------------------------|
	| FECHA:  																	|
	| AUTOR: 																	|
	| DESCRIPCIÓN:																|
	----------------------------------------------------------------------------*/

$wactualiz="1.0 | Febrero 17 de 2016";
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
encabezado("FORMULARIO EVALUACION HISTORIA CLINICA SERVICIO OBSERVACION",$wactualiz,"clinica");

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
  


  /*$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "farpmla");
  //$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "jcievahc");
	
  $q = " SELECT empdes "
      ."  FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wentidad=$row[0];*/
  
  echo "<form name='forma' action='rep_evahcobs.php' method=post onSubmit='return valida_enviar(this);'>";
  $wfecha=date("Y-m-d");   
  
 /* echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";*/
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
  		$q= "   SELECT Ccosto "
 	       ."   FROM jcievahc_000003 "
 	       ."    GROUP BY 1 "
 	       ."    ORDER by 1 "; 	
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
					echo "<option selected>".$row1[0]."</option>"; 
	            else
					echo "<option>".$row1[0]."</option>";
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
	echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Formulario Evaluacion Historia Clinica Serv. Observacion&nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";

	//Muestro los parámetros que se ingresaron en la consulta
    echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Fecha inicial: </strong>&nbsp;".$wfecini." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Fecha final: </strong>&nbsp;".$wfecfin." &nbsp; </td>";
    echo "</tr>";
    /*echo "<tr class='fila2'>";
    echo "<td align=left colspan=2><strong> &nbsp; Centro de costos: </strong>&nbsp;".$wccocod." &nbsp; </td>";
    echo "</tr>";*/
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>";

	// Botones de "Retornar" y "Cerrar ventana"
	echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
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
	$q = "	SELECT * "
		."	FROM jcievahc_000003 "
		."	WHERE Fecha BETWEEN '".$wfecini."' "
		."    AND '".$wfecfin."'"
		."	  AND Ccosto LIKE '".$wccostos[0]."%' "
		."	  ORDER BY Ccosto ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);

	$wglobalc=0;
	$wglobalnc=0;
	$wglobalna=0;
	$wtotgenc=0;
	$wtotgennc=0;
	$wtotgenna=0;
	$wtotpor=0;
	$wglobalpor=0;
	$bandera1=0;
	$bandera2=0;
	
	$clase='fila1';
	$k=1;
	$i=1;
	$j=6;
	$cont=0;
	$contgen=0;
	
$wtotalc6=0;	$wtotalnc6=0;	$wtotalna6=0;	$por6=0;
$wtotalc7=0;	$wtotalnc7=0;	$wtotalna7=0;	$por7=0;
$wtotalc8=0;	$wtotalnc8=0;	$wtotalna8=0;	$por8=0;
$wtotalc9=0;	$wtotalnc9=0;	$wtotalna9=0;	$por9=0;

$crite6="Descripcion e Interpretacion de Paraclinicos";
$crite7="Evaluacion de la Evolucion";
$crite8="Interconsulta por Especialista (Pertinencia)";
$crite9="Aplico la Norma de NO Utilizacion de Abreviaturas"; 

$color="#eeeeee";
$color1="#cccccc";
$color2="#bbbbbb";
$color3="#aaaaaa";
	// Creación de tabla donde se muestra el resultado de la consulta
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
   
	// Inicio del ciclo general de los resultados de la consulta
	while ($i <= $num) 
		{
			$row = mysql_fetch_array($err);
			if ($bandera1==0) 
				{
					$wccocod=$row['Ccosto'];
				}
					 	
			// Si cambia el centro de costos muestre el total del anterior
			if ($wccocod!=$row['Ccosto'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9;
							   
					
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>"; 
					echo "<tr class='encabezadoTabla' height='31'>";
						echo"<td align=center border=1 colspan='5'>CRITERIOS</td>";
						echo "<td align=center border=1 >CUMPLE</td>";
						echo "<td align=center border=1>NO CUMPLE</td>";
						echo "<td align=center border=1 >NO APLICA</td>";
						echo "<td align=center border=1>% DE CUMPLIMIENTO</td></tr>";
					echo "<tr><td align='left' colspan='5'>&nbsp;</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna6."</td>";
							$por6=(($wtotalc6+$wtotalna6)/$cont)*100;
							$por6=ROUND($por6,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por6."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna7."</td>";
							$por7=(($wtotalc7+$wtotalna7)/$cont)*100;
							$por7=ROUND($por7,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por7."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna8."</td>";
							$por8=(($wtotalc8+$wtotalna8)/$cont)*100;
							$por8=ROUND($por8,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por8."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna9."</td>";
							$por9=(($wtotalc9+$wtotalna9)/$cont)*100;
							$por9=ROUND($por9,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por9."%</td></tr>";
						
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";
						echo"<td align=left colspan='5' bgcolor=".$color2."><strong>TOTAL GENERAL CENTRO DE COSTOS      (Historias Evaluadas:".$cont.")</strong></td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgennc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenna."</td>";
							$wtotpor=(($wtotgenc+$wtotgenna)/($wtotgenc+$wtotgenna+$wtotgennc))*100;
							$wtotpor=ROUND($wtotpor,2);
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotpor."%</td></tr>";
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr></table>";   
						
					$wtotalc6=0;	$wtotalnc6=0;	$wtotalna6=0;	$por6=0;
					$wtotalc7=0;	$wtotalnc7=0;	$wtotalna7=0;	$por7=0;
					$wtotalc8=0;	$wtotalnc8=0;	$wtotalna8=0;	$por8=0;
					$wtotalc9=0;	$wtotalnc9=0;	$wtotalna9=0;	$por9=0;
									
					$wglobalc=$wglobalc+$wtotgenc;
					$wglobalnc=$wglobalnc+$wtotgennc;
					$wglobalna=$wglobalna+$wtotgenna;
					$contgen=$contgen+$cont;
					
					$wtotgenc=0;
					$wtotgennc=0;
					$wtotgenna=0;
					$cont=0;
					$wtotpor=0;
					
				}
			// Se define si se muestra el título del centro de costos
			if (($bandera1==0) or ($wccocod!=$row['Ccosto']))
				{   
					$waux=$wccocod;
					$wccocod=$row['Ccosto'];
					$bandera1=1;
					$pinto=0;	// Define si muestro el encabezado de la tabla
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";   
					echo "<tr><td align='left' class='encabezadoTabla' colspan='9' height='31'>&nbsp;CENTRO DE COSTOS: ".$wccocod."</td></tr></table>";   
				}
			 
		 	
				// Se establece la clase para la fila en el ciclo actual
				if (is_int ($k/2))
					$clase='fila1';
				else
					$clase='fila2';
				$k=$k+1;

				// Asigno los totales para responsable, centro de costos y total general
				
					if ($row[P1]=="C-CUMPLE")
						$wtotalc6=$wtotalc6 + 1;
					if ($row[P1]=="NA-NO APLICA")
						$wtotalna6=$wtotalna6 + 1;
					if ($row[P1]=="NC-NO CUMPLE")
						$wtotalnc6=$wtotalnc6 + 1;
					if ($row[P2]=="C-CUMPLE")
						$wtotalc7=$wtotalc7 + 1;
					if ($row[P2]=="NA-NO APLICA")
						$wtotalna7=$wtotalna7 + 1;
					if ($row[P2]=="NC-NO CUMPLE")
						$wtotalnc7=$wtotalnc7 + 1;
					if ($row[P3]=="C-CUMPLE")
						$wtotalc8=$wtotalc8 + 1;
					if ($row[P3]=="NA-NO APLICA")
						$wtotalna8=$wtotalna8 + 1;
					if ($row[P3]=="NC-NO CUMPLE")
						$wtotalnc8=$wtotalnc8 + 1;
					if ($row[P4]=="C-CUMPLE")
						$wtotalc9=$wtotalc9 + 1;
					if ($row[P4]=="NA-NO APLICA")
						$wtotalna9=$wtotalna9 + 1;
					if ($row[P4]=="NC-NO CUMPLE")
						$wtotalnc9=$wtotalnc9 + 1;
															
				$i= $i + 1;
				$cont=$cont + 1;
				
		}
		
		// Si no se tienen resultados se muestra el mensaje correspondiente
		if ($cont==0)
			{
				echo "<table align='center' border=0 bordercolor=#000080 width=570 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><b>Sin ningún documento en el rango de fechas seleccionado</b></td><tr>";
			}
		// Muestra el total del responsable o caja y del centro de costos
		// Esto debido a que en el último ciclo estos no se muestran
		else
			{	
			
				if ($wccocod==$row['Ccosto'])
				{	
					$wtotgenc=$wtotalc6+$wtotalc7+$wtotalc8+$wtotalc9;
					
					$wtotgennc=$wtotalnc6+$wtotalnc7+$wtotalnc8+$wtotalnc9;
					
					$wtotgenna=$wtotalna6+$wtotalna7+$wtotalna8+$wtotalna9;
							   
					
					echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>"; 
					echo "<tr class='encabezadoTabla' height='31'>";
						echo"<td align=center border=1 colspan='5'>CRITERIOS</td>";
						echo "<td align=center border=1 >CUMPLE</td>";
						echo "<td align=center border=1>NO CUMPLE</td>";
						echo "<td align=center border=1 >NO APLICA</td>";
						echo "<td align=center border=1>% DE CUMPLIMIENTO</td></tr>";
					echo "<tr><td align='left' colspan='5'>&nbsp;</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc6."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna6."</td>";
							$por6=(($wtotalc6+$wtotalna6)/$cont)*100;
							$por6=ROUND($por6,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por6."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc7."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna7."</td>";
							$por7=(($wtotalc7+$wtotalna7)/$cont)*100;
							$por7=ROUND($por7,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por7."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color." >".$crite8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalnc8."</td>";
						echo "<td align=right border=1 bgcolor=".$color.">".$wtotalna8."</td>";
							$por8=(($wtotalc8+$wtotalna8)/$cont)*100;
							$por8=ROUND($por8,2);
						echo "<td align=right border=1 bgcolor=".$color.">".$por8."%</td></tr>";
						
						echo"<td align=left colspan='5' bgcolor=".$color1." >".$crite9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalnc9."</td>";
						echo "<td align=right border=1 bgcolor=".$color1.">".$wtotalna9."</td>";
							$por9=(($wtotalc9+$wtotalna9)/$cont)*100;
							$por9=ROUND($por9,2);
						echo "<td align=right border=1 bgcolor=".$color1.">".$por9."%</td></tr>";
						
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";
						echo"<td align=left colspan='5' bgcolor=".$color2."><strong>TOTAL GENERAL CENTRO DE COSTOS      (Historias Evaluadas:".$cont.")</strong></td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgennc."</td>";
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotgenna."</td>";
							$wtotpor=(($wtotgenc+$wtotgenna)/($wtotgenc+$wtotgenna+$wtotgennc))*100;
							$wtotpor=ROUND($wtotpor,2);
						echo "<td align=right border=1 bgcolor=".$color2.">".$wtotpor."%</td></tr>";
					echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";   
					
					$wglobalc=$wglobalc+$wtotgenc;
					$wglobalnc=$wglobalnc+$wtotgennc;
					$wglobalna=$wglobalna+$wtotgenna;
					$contgen=$contgen+$cont;
					
					if ($wccostos=='%')
					{
						echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";
							echo"<td align=left border=2 colspan='5' bgcolor=".$color3."><strong>TOTAL GENERAL TODOS LOS CENTRO DE COSTOS   (Historias Evaluadas:".$contgen.")</strong></td>";
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalc."</td>";
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalnc."</td>";
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalna."</td>";
								$wglobalpor=(($wglobalc+$wglobalna)/($wglobalc+$wglobalnc+$wglobalna))*100;
								$wglobalpor=ROUND($wglobalpor,2);
							echo "<td align=right border=2 bgcolor=".$color3.">".$wglobalpor."%</td></tr>";
						echo "<tr><td align='left' colspan='9'>&nbsp;</td></tr>";   
					}
				}			
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