<html>
<head>
<title>MATRIX - REPORTE DE VENTAS POR RANGO DE ARTICULO Y FECHA</title>
<script type="text/javascript">
	function valida_enviar(form)
	{
		//Inicio de validaciones
		/*if(form.wfue.value!='' && (form.wfac.value=='' || form.wfac.value=='%'))
			 {
			   alert("Debe entrar tambien un valor para la factura");
			   form.wfue.focus();
			   return false;
			 }*/

		form.submit();
	}

</script>
</head>
<?php
include_once("conex.php");
  /***********************************************************************************
   *     PROGRAMA PARA EL REPORTE DE VENTAS CON RANGO DE ARTICULOS Y RANGO DE FECHAS *
   ***********************************************************************************/
   
//==========================================================================================================================================
//PROGRAMA				      : REPORTE DE VENTAS CON RANGO DE ARTICULOS Y RANGO DE FECHAS                                                             |
//AUTOR                       : John Mario Cadavid García.																					|
//FECHA CREACION              : Enero 04 de 2010																							|
//FECHA ULTIMA ACTUALIZACION  :                                                                                        						|
//DESCRIPCION			      : Reporte de ventas con rango de articulos y rango de fecha. (centro de costos,fecha,codigo,descripcion,		|
//								cantidad,precio de venta unitario,precio de venta total).													|
//                                                                                                                                          |
//==========================================================================================================================================

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//  Aca se coloca los comentarios de las actualizaciones que se hagan                                                                     \\
//________________________________________________________________________________________________________________________________________\\
//========================================================================================================================================\\
//========================================================================================================================================\\         

$wactualiz="Enero 4 de 2010"; 

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="Vers. 30-Diciembre-2010";

//VAlidación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("REPORTE DE VENTAS",$wactualiz,"clinica");

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
 


 // Consulto los datos de la empresa actual y los asigno a la variable $empresa
 $consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
 $empresa = $consulta->baseDeDatos;
      
    //Encabezado del reporte
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='2' class='titulo'><b>REPORTE DE VENTAS CON RANGO DE ARTICULOS Y RANGO DE FECHAS</b></font></td>";
    echo "</tr>";
    echo "</table>";
    
 //Formulario de ingreso de parámetros de consulta
  echo "<form name='reporte_de_ventas' action='RepVentasArtFec.php' method=post onSubmit='return valida_enviar(this);'>";

 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
 echo "<input type='HIDDEN' NAME= 'form' value='forma'>";
 
 if (!isset($fec1) or $fec1 == '')
 	$fec1 = date("Y-m-d");
 if (!isset($fec2) or $fec2 == '')
 	$fec2 = date("Y-m-d");
 	
 //Si no he recibido el formulario muestro el formulario de solicitud de parámetros
 if (!isset($form) or $form == '')
  {
  	echo "<form name='rep_movranart' action='' method=post>";
  
	//Petición de ingreso de parametros
 	echo '<p align="center"> <strong>&nbsp; Ingrese los parámetros de la consulta &nbsp;  &nbsp;</strong> </p>';
  	
  	//Inicio tabla de ingreso de parametros
 	echo "<table align='center' border=0>";

  	//Solicitud de artículo inicial
 	if(!isset($wart1))
 		$wart1='';
 	echo "<tr>";
 	echo "<td class='fila1' width=134>&nbsp;Artículo Inicial</td>";
 	echo "<td class='fila2' align='left'><input type='TEXT' name='wart1' tabindex='1' value='".$wart1."' size=20 maxlength=20></td>";
 	
 	//Solicitud fecha inicial
 	echo "<td width='21'>&nbsp;</td>";
 	echo "<td class='fila1' width=121>&nbsp;Fecha Inicial</td>";
 	echo "<td class='fila2' align='left'>";
 	campoFechaDefecto("fec1",$fec1);
 	echo "</td></tr>";

 	//Solicitud de artículo final
 	if(!isset($wart2))	
 		$wart2='';  
 	echo "<tr>";
 	echo "<td class='fila1'>&nbsp;Artículo Final</td>";
 	echo "<td class='fila2' align='left'><input type='TEXT' name='wart2' tabindex='2' value='".$wart2."' size=20 maxlength=20></td>";
 	
 		
 	//Solicitud fecha final
 	echo "<td>&nbsp;</td>";
 	echo "<td class='fila1'>&nbsp;Fecha Final</td>";
 	echo "<td class='fila2' align='left'>";
 	campoFechaDefecto("fec2",$fec2);
 	echo "</td></tr>";
 	
 	//Boton de OK o Aceptar
 	echo "<tr><td align=center colspan=5><br /><input type='submit' id='searchsubmit' value='OK'></td></tr>";          
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';

   // Botones de opciones para Volver atrás o Cerrar ventana 
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // Fin IF si no he recibido variables
 else // Si el usuario ya ingresado datos al formulario y hemos recibido variables comenzamos a mostrar el reporte
  {
  	
    ///////////////// ACA COMIENZA LA IMPRESION DEL REPORTE /////////////////
    
    //Muestro los parámetros que se ingresaron en la consulta
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
    echo "<td align=left colspan='1'><font size='2' text color=#003366><b>Artículo inicial: ".$wart1."</b></font></td>";
    echo "<td align=left colspan='1' width=41>&nbsp;</td>";
    echo "<td align=left colspan='1'><font size='2' text color=#003366><b>Fecha inicial: ".$fec1."</b></font></b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=left colspan='1'><font size='2' text color=#003366><b>Artículo final: ".$wart2."</b></font></td>";
    echo "<td align=left colspan='1'>&nbsp;</td>";
    echo "<td align=left colspan='1'><font size='2' text color=#003366><b>Fecha final: ".$fec2."</b></font></b></font></td>";
    echo "</tr>";
    echo "</table>";

    // Inicia la tabla donde se muestran los datos consultados del reporte
    echo "<br>";
    echo "<table border=0 bordercolor=#ffffff cellspacing=1 cellpadding=0 align=center size='100'>";


      //Query o consulta principal a la base de datos que arroja los valores solicitados
	  $q = "  SELECT ccocod, ccodes, venfec, artcod, artnom, SUM(vdecan), vdevun, SUM(vdevun*vdecan)"
	      ."    FROM ".$empresa."_000001, ".$empresa."_000016, ".$empresa."_000017, ".$empresa."_000003 "
          ."   WHERE venfec between '".$fec1."' AND '".$fec2."'"
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."     AND vdeart between '".$wart1."' and '".$wart2."'"
          ."     AND ccocod = vencco "
          ."   GROUP BY venfec, artcod, vdevun "
          ."   ORDER BY ccocod, venfec DESC, artnom, artcod ";

      $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
    
	//echo mysql_errno() ."=". mysql_error();
    //Obtengo la primer fila del query y pregunto si encuentra resultados para la consulta
	if($row = mysql_fetch_array($err)) {
	
    $i=1;

	//Muestro el encabezado de los campos de la tabla
	echo "<tr class='encabezadoTabla'>";
    echo "<td align=center><b>&nbsp;C.C.&nbsp;</b></td>";
    echo "<td align=center><b>&nbsp;FECHA&nbsp;</b></td>";
    echo "<td align=center><b>&nbsp;CODIGO&nbsp;</b></td>";
    echo "<td align=center><b>&nbsp;DESCRIPCION&nbsp;</b></td>";
    echo "<td align=center><b>&nbsp;CANTIDAD&nbsp;</b></td>";
    echo "<td align=center><b>&nbsp;PREC. VENTA UNIT.&nbsp;</b></td>";
    echo "<td align=center><b>&nbsp;PREC. VENTA TOTAL&nbsp;</b></td>";
    echo "</tr>";
	 
	 $ii = 1;
	 $concant= 0;   //Variable para llevar sumatoria de cantidades
	 $contot = 0;  //Variable para llevar sumatoria de totales
	 
	 //Ciclo para recorrer los registros de cada concepto
	 while($i<=$num) {
		 if (is_int ($ii/2))
		   $wcf="fila1";  // color de fondo de la fila
		 else
		   $wcf="fila2"; // color de fondo de la fila
	
		//Variables para calculo de la sumatoria
		$concant += $row[5]; //Sumatoria cantidad
		$contot += $row[7];  //Sumatoria costos totales 
		//$contotiva += $valiva;  //Sumatoria iva
	    
	   	  //Se imprime los valores de cada fila
		  echo "<tr class=".$wcf.">";
		  echo "<td align=center>&nbsp;".$row[0]." - ".$row[1]."&nbsp;</td>";
		  echo "<td align=center>&nbsp;".$row[2]."&nbsp;</td>";
		  echo "<td align=left>".$row[3]."</td>";
		  echo "<td align=left>".$row[4]."</td>";
		  echo "<td align=left>".$row[5]."</td>";
		  echo "<td align=center>&nbsp;".number_format((double)$row[6],2,'.',',')."&nbsp;</td>";
		  echo "<td align=right>&nbsp;".number_format((double)$row[7],2,'.',',')."&nbsp;</td>";
		  echo "</tr>";

  	      //Obtengo la siguiente fila
		  $row = mysql_fetch_array($err);
	 	  $ii++;
	 	  $i++;
	    } //Fin Ciclo para recorrer los registros de cada concepto

	   	  //Imprimo totales para los artículos de cada concepto
	  	  echo "<tr class='encabezadoTabla'>";
		  echo "<td align=left colspan=4> &nbsp; TOTALES &nbsp; </td>";
		  echo "<td align=center> &nbsp; ".$concant." &nbsp; </td>";
		  echo "<td align=center> &nbsp; </td>";
		  echo "<td align=right> &nbsp; ".number_format((double)$contot,2,'.',',')." &nbsp; </td>";
		  echo "</tr>";
  	// Fin si encuentra resultados para la consulta   
  	} else {
     	echo "<p align='center'>No se encontraron resultados para la búsqueda.</p>";
    }
    
   // Botones de opciones para Volver atrás o Cerrar ventana 
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center height='37'><br />
   <a href='RepVentasArtFec.php?wemp_pmla=".$wemp_pmla."&wart1=".$wart1."&wart2=".$wart2."&fec1=".$fec1."&fec2=".$fec2."' class='enlaceboton'><b>Retornar</b></a></td></tr>";
   echo "</table>";
	 
  } // Fin ELSE si el usuario ya ingresado datos al formulario 
    
	echo "</table>"; 

  } // Fin ELSE reporte

?>
</body>
</html>