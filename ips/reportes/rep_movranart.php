<html>
<head>
<title>MATRIX - [MOVIMIENTO POR ARTICULO]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_movranart.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_pacxmedico.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}

	function volver_atras(wart1,wart2,fec1,fec2)
	{
		document.location.href='rep_movranart.php?wart1='+wart1+'&wart2='+wart2+'&fec1='+fec1+'&fec2='+fec2;
	}
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      : REPORTE DE MOVIMIENTO DE ARTÍCULOS POR CONCEPTO                                                             |
//AUTOR				          : John Mario Cadavid García.                                                                                  |
//FECHA CREACION			  : Diciembre 23 DE 2010.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : Diciembre 23 DE 2010.                                                                                       |
//DESCRIPCION			      : Este reporte es para ver el movimiento de los artículos entre fechas y clasificados por conceptos.          |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 21-Diciembre-2010";

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
encabezado("Movimiento de artículos por concepto",$wactualiz,"clinica");

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
 



 // Consulto los datos de la empresa actual y los asigno a la variable $emp
 $consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
 $empresa = $consulta->baseDeDatos;
 //Formulario de ingreso de parámetros de consulta
 echo "<form name='forma' action='rep_movranart.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
 echo "<input type='HIDDEN' NAME= 'form' value='forma'>";
 
 if (!isset($fec1) or $fec1 == '')
 	$fec1 = date("Y-m-d");
 if (!isset($fec2) or $fec2 == '')
 	$fec2 = date("Y-m-d");
 	
 //Si no he recibido variables muestro el formulario de solicitud de parámetros
 //if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '' or !isset($wart1) or $wart1 == '' or !isset($wart2) or $wart2 == '') 
 if (!isset($form) or $form == '')
  {
  	echo "<form name='rep_movranart' action='' method=post>";
  
	//Petición de ingreso de parametros
 	echo '<span class="subtituloPagina2">';
 	echo '<p align="center">Ingrese los parámetros de consulta</p>';
 	echo "</span>";
 	echo "<br>";
  	
  	//Inicio tabla de ingreso de parametros
 	echo "<table align='center' border=0>";

  	//Solicitud de artículo inicial
 	if(!isset($wart1))
 		$wart1='';
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Artículo Inicial</td>";
 	echo "<td class='fila2' align='left' width=150><input type='TEXT' name='wart1' value='".$wart1."' size=20 maxlength=20></td></tr>";
 	
 	//Solicitud de artículo final
 	if(!isset($wart2))	
 		$wart2='';  
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Artículo Final</td>";
 	echo "<td class='fila2' align='left' width=150><input type='TEXT' name='wart2' value='".$wart2."' size=20 maxlength=20></td></tr>";
 	
 	//Solicitud fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='left' width=150>";
 	campoFechaDefecto("fec1",$fec1);
 	echo "</td></tr>";
 		
 	//Solicitud fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='left'>";
 	campoFechaDefecto("fec2",$fec2);
 	echo "</td></tr>";
 	
 	//Solicitud de concepto
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Concepto</td>";
 	echo "<td class='fila2' align='left'>";
		$query = "SELECT Concod, Condes from ".$empresa."_000008 order by Concod";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wcon'>";
			echo "<option value=0> --- TODOS --- </option>";
			for ($i=0;$i<$num;$i++)
			{
				$select = '';
				$row = mysql_fetch_array($err);
				if($row[0]==$wcon)
					$select = 'selected';
				echo "<option value=".$row[0]." ".$select.">".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		}
 	echo "</td></tr>";
 	
 	//Boton de OK o Aceptar
 	echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          
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
    
    //Encabezado del reporte
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='2' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REPORTE DE PRODUCTOS POR CONCEPTO</b></font></td>";
    echo "</tr>";
    echo "</table>";
    
    //Muestro los parámetros que se ingresaron en la consulta
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
    echo "<td align=left colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>Artículo inicial: ".$wart1."</b></font></td>";
    echo "<td align=left colspan='1' bgcolor=#FFFFFF width=41>&nbsp;</td>";
    echo "<td align=left colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>Fecha inicial: ".$fec1."</b></font></b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=left colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>Artículo final: ".$wart2."</b></font></td>";
    echo "<td align=left colspan='1' bgcolor=#FFFFFF>&nbsp;</td>";
    echo "<td align=left colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>Fecha final: ".$fec2."</b></font></b></font></td>";
    echo "</tr>";
    echo "</table>";

    // Inicia la tabla donde se muestran los datos consultados del reporte
    echo "<br>";
    echo "<table border=0 bordercolor=#ffffff cellspacing=1 cellpadding=0 align=center size='100'>";

    	//Query o consulta principal a la base de datos que arroja los valores solicitados
    	$query = "SELECT  Concod, Condes, Menfec, ".$empresa."_000010.Hora_data, Artnom, Mdecan, Mdevto, Mdepiv, Artcod, cco_orig.Ccocod, cco_orig.Ccodes, cco_dest.Ccocod, cco_dest.Ccodes ";
		$query .= "  FROM ".$empresa."_000011, ".$empresa."_000001, ".$empresa."_000008, ((".$empresa."_000010 ";
		$query .= "  LEFT JOIN ".$empresa."_000003 AS cco_orig ON Mencco = cco_orig.Ccocod) "; 
		$query .= "  LEFT JOIN ".$empresa."_000003 AS cco_dest ON Menccd = cco_dest.Ccocod) "; 
		$query .= "  WHERE Menfec between '".$fec1."' and '".$fec2."'";
		if($wcon>0)
			$query .= " 	AND Mencon = ".$wcon." ";
		$query .= "     AND Mencon = Mdecon ";
		$query .= "     AND Mdecon = Concod ";
		$query .= "     AND Mendoc = Mdedoc ";
		$query .= "     AND Mdeart between '".$wart1."' and '".$wart2."'";
		$query .= "     AND Mdeart = Artcod ";
		$query .= "     ORDER BY Concod, Menfec, ".$empresa."_000010.Hora_data ";	
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		    
    
	//echo mysql_errno() ."=". mysql_error();
    //Obtengo la primer fila del query
	$row = mysql_fetch_array($err);
	
    $i=1;
    //Ciclo para recorrer todos los registros de la consulta
    while ($i<=$num)
	{

   	 //Muestro el concepto como encabezado para despues mostrar sus artículos
	 echo "<tr>";
	 echo "<td align=center colspan=8 height=21>&nbsp;</td>";
	 echo "</tr>";
	 echo "<tr>";
	 echo "<td colspan=8 class='encabezadoTabla'>&nbsp;".$row[0]." - ".$row[1]."</td>";
	 echo "</tr>";

	//Muestro el encabezado de los campos de la tabla
	echo "<tr>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;FECHA&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;HORA&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;ARTICULO&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;C.C. ORIGEN&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;C.C. DESTINO&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;CANTIDAD&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;CTO UNITARIO&nbsp;</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbsp;CTO TOTAL&nbsp;</b></td>";
    echo "</tr>";
	 
	 $ii = 1;
	 $concant=0;   //Variable para llevar sumatoria de cantidades por cada concepto
	 $contot = 0;  //Variable para llevar sumatoria de totales por cada concepto
	 //$contotiva = 0;
	 $aux = $row[0]; //Auxiliar para saber cuando se cambia de concepto 
	 
	 //Ciclo para recorrer los registros de cada concepto
	 while($row[0]==$aux && $i<=$num) {
		 if (is_int ($ii/2))
		   $wcf="fila1";  // color de fondo de la fila
		 else
		   $wcf="fila2"; // color de fondo de la fila
	
		//Calcula el costo unitario del articulo
		if($row[5] != 0)
			$cosuni=$row[6] / $row[5];
		else
			$cosuni=0;
		
		//Variables para calculo de la sumatoria para costos, cantidad e ivas
		$cant = $row[5]; //Sumatoria cantidad
		$valiva = ($row[7] / 100) * $cosuni;  //Calculo iva 
		$costot = ($cosuni + $valiva) * $cant; //Costo total por fila
		
		$cosuniiva = $cosuni + $valiva; //Recalculo costo unitario con IVA
		$concant += $row[5]; //Sumatoria cantidad
		$contot += $costot;  //Sumatoria costos totales 
		//$contotiva += $valiva;  //Sumatoria iva
	    
	   	  //Se imprime los valores de cada fila
		  echo "<tr class=".$wcf.">";
		  echo "<td align=center>&nbsp;".$row[2]."&nbsp;</td>";
		  echo "<td align=center>&nbsp;".$row[3]."&nbsp;</td>";
		  echo "<td align=left>".$row[8]." - ".$row[4]."</td>";
		  echo "<td align=left>".$row[9]." - ".$row[10]."</td>";
		  echo "<td align=left>".$row[11]." - ".$row[12]."</td>";
		  echo "<td align=center>&nbsp;".$row[5]."&nbsp;</td>";
		  echo "<td align=right>&nbsp;$".number_format((double)$cosuniiva,2,'.',',')."&nbsp;</td>";
		  echo "<td align=right>&nbsp;$".number_format((double)$costot,2,'.',',')."&nbsp;</td>";
		  echo "</tr>";

  	      //Obtengo la siguiente fila
		  $row = mysql_fetch_array($err);
	 	  $ii++;
	 	  $i++;
	    } //Fin Ciclo para recorrer los registros de cada concepto

	   	  //Imprimo totales para los artículos de cada concepto
	  	  echo "<tr class='encabezadoTabla'>";
		  echo "<td align=left colspan=5> &nbsp; TOTALES &nbsp; </td>";
		  echo "<td align=center> &nbsp; ".$concant." &nbsp; </td>";
		  echo "<td align=center> &nbsp; </td>";
		  echo "<td align=right> &nbsp; $".number_format((double)$contot,2,'.',',')." &nbsp; </td>";
		  echo "</tr>";
	  
	 } //Fin Ciclo para recorrer todos los registros de la consulta
    
   // Botones de opciones para Volver atrás o Cerrar ventana 
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center>
   <a href='rep_movranart.php?wemp_pmla=".$wemp_pmla."&wart1=".$wart1."&wart2=".$wart2."&fec1=".$fec1."&fec2=".$fec2."&wcon=".$wcon."'>Retornar</a></td></tr>";
   echo "</table>";
	 
  } // Fin ELSE si el usuario ya ingresado datos al formulario 
    
	echo "</table>"; 

  } // Fin ELSE reporte


?>
</body>
</html>