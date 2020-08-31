<html>
<head>
  <title>REPORTE DE CARGOS DE PACIENTES HOSPITALIZADOS DADOS DE ALTA</title>
<script type="text/javascript">

	//Redirecciona a la pagina inicial
	function inicioReporte(wfecini,wfecfin,wcco,wemp_pmla,bandera){
	 	document.location.href='rep_cargospac.php?wfecini='+wfecini+'&wfecfin='+wfecfin+'&wccocod='+wcco+'&wemp_pmla='+wemp_pmla+'&bandera='+bandera;
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
   *     REPORTE DE CARGOS DE PACIENTES HOSPITALIZADOS DADOS DE ALTA		    *
   ******************************************************************************/
   
	/*--------------------------------------------------------------------------
	| DEDSCRIPCIÓN: Reporte de cargos de pacientes hospitalizados dados de alta |
	| cargos facturables y no facturables                       	     		|
	| AUTOR: Gabriel Agudelo												    |
	| FECHA DE CREACIÓN: Febrero 27 de 2019										|
	| -------------------------------------------------------------------------*/

$wactualiz="1.0 | Febrero 27 de 2019";

if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	//header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	$conex 		= 	obtenerConexionBD("matrix");
	$conexN = odbc_connect('facturacion','','') or die("No se realizo Conexion con la BD de facturacion en Informix");

	//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
	encabezado("CARGOS DE PACIENTES HOSPITALIZADOS DADOS DE ALTA",$wactualiz,"clinica");  //Inicio ELSE reporte
	

  echo "<form name='forma' action='rep_cargospac.php' method=post onSubmit='return valida_enviar(this);'>";
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
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";	
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

	echo "</table></br>";	   

	echo "<p align='center'><input type='button' id='searchsubmit' value='Consultar' onclick='Seleccionar()'> &nbsp; | &nbsp; <input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
	
  } 

//RESULTADO DE CONSULTA DEL REPORTE
else
  {
	//Inicio tabla de resultados
    echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
 	
	// Subtítulo del reporte
 	echo "<tr>";
	echo "<td height='37' colspan='2' class='titulo'><p align='left'><strong> &nbsp; Cargos Pacientes dados de Alta&nbsp;  &nbsp; </strong></p></td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11'>&nbsp;</td>";
 	echo "</tr>";

	//Muestro los parámetros que se ingresaron en la consulta
    echo "<tr class='fila2'>";
    echo "<td align=left><strong> &nbsp; Fecha inicial: </strong>&nbsp;".$wfecini." &nbsp; </td>";
    echo "<td align=left><strong> &nbsp; Fecha final: </strong>&nbsp;".$wfecfin." &nbsp; </td>";
    echo "</tr>";
    
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td>";
 	echo "</tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>";

	// Botones de "Retornar" y "Cerrar ventana"
	echo "<p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
 	echo "</td></tr>";
 	echo "<tr>";
	echo "<td height='11' colspan='2'>&nbsp;</td></tr>";
 	
	echo "</table>";

    echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
    echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
    echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	/***********************************Consulto lo pedido ********************/

	// QUERY PRINCIPAL DEL REPORTE MATRIX
	//                1     2       3     4     5       6      7      8      9      10     11                  12                 
	$q = "	SELECT Ubihis,Ubiing,Ubisac,Ubiald,Ubifad,Ingtpa,Ingcem,Empnom,Ingfei,Pactdo,Pacdoc,concat(Pacno1,' ',Pacno2,' ',Pacap1,' ',Pacap2 )  "
		."	FROM movhos_000018,cliame_000100,cliame_000101,cliame_000024 "
		."	WHERE Ubifad BETWEEN '".$wfecini."' AND '".$wfecfin."' "
		."    AND Ubihis = Inghis "		
		."	  AND Ubisac in ('1182','1183','1184','1020','1021','1187','1180','1185','1186','1188','1190',
							 '1189','1286','1282','1281','1283','1284','1285','1179')  "
		."    AND Ubihis = Inghis "
		."	  AND Ubiing = Ingnin "
		."    AND Inghis = Pachis " 
		."    AND Ingcem = Empcod "
		."	  ORDER BY Ubisac ";
	$err = mysql_query($q,$conex);
	$num = mysql_num_rows($err);
	
	
	      // Abro el archivo
   	      $archivo = fopen("rep_cargospac.txt","w"); 
		  //Coloco los titulos
		  $LineaDato="HISTORIA|INGRESO|CCOSTOS|ALTA|FECHA DE ALTA|TIPO PACIENTE|NIT|DESCRIPCION|FECHA INGRESO|TIPO DOCUMENTO|DOCUMENTO|NOMBRE|VALOR FACTURABLE|VALOR NO FACTURABLE|"; 
		  fwrite($archivo, $LineaDato.chr(13).chr(10) );
	
	$clase='fila1';
	$k=1;
	$i=0;
	
$color="#eeeeee";
$color1="#cccccc";
$color2="#bbbbbb";
$color3="#aaaaaa";
	// Creación de tabla donde se muestra el resultado de la consulta
    
	echo "<table border=1>";
	echo "<tr class='fila1'>";
	echo "<td align=center >HISTORIA</td>";
	echo "<td align=center >INGRESO</td>";
	echo "<td align=center >CCOSTOS</td>";
	echo "<td align=center >ALTA</td>";
	echo "<td align=center  >FECHA ALTA</td>";
	echo "<td align=center >TIPO PACIENTE</td>";
	echo "<td align=center  >ENTIDAD</td>";
	echo "<td align=center  >NOMBRE</td>";
	echo "<td align=center >FECHA INGRESO</td>";
	echo "<td align=center  >TIPO DOCUMENTO</td>";
	echo "<td align=center >DOCUMENTO</td>";
	echo "<td align=center >PACIENTE</td>";
	echo "<td align=center  >VALOR FACTURABLE</td>";
	echo "<td align=center  >VALOR NO FACTURABLE</td></tr>";
   
	// Inicio del ciclo general de los resultados de la consulta
	for ($i=1;$i<=$num;$i++) 
		{
			$row = mysql_fetch_row($err);
				
				//De hipocrates tomo los valores cobrados
				$q1="Select Cardethis,Cardetnum,sum(cardettot)   "
				  ." From facardet "
				  ." Where Cardethis = ".$row[0]." and Cardetnum = ".$row[1]." and cardetfac = 'S' and Cardetanu = 0   "
				  ." group by 1,2 ";
				  
				 $result1 = odbc_do($conexN,$q1);                   // Ejecuto el query
				 $fact=odbc_result($result1,3);
				 
				 $q2="Select Cardethis,Cardetnum,sum(cardettot)   "
				  ." From facardet "
				  ." Where Cardethis = ".$row[0]." and Cardetnum = ".$row[1]." and cardetfac = 'N' and Cardetanu = 0   "
				  ." group by 1,2 ";
				  
				 $result2 = odbc_do($conexN,$q2);                   // Ejecuto el query
				 $nfact=odbc_result($result2,3);
				  
				
			
				// Se establece la clase para la fila en el ciclo actual
				if (is_int ($i/2))
				  {
					$wcf="F8FBFC";  // color de fondo
				  }
				 else
				  {
					$wcf="DFF8FF"; // color de fondo
				  }

				echo "<tr bgcolor=".$wcf.">";
				echo "<td align=left  >".$row[0]."</td>";
				echo "<td align=left  >".$row[1]."</td>";
				echo "<td align=left  >".$row[2]."</td>";
				echo "<td align=left  >".$row[3]."</td>";
				echo "<td align=left  >".$row[4]."</td>";
				echo "<td align=left  >".$row[5]."</td>";
				echo "<td align=left  >".$row[6]."</td>";
				echo "<td align=left  >".$row[7]."</td>";
				echo "<td align=left  >".$row[8]."</td>";
				echo "<td align=left  >".$row[9]."</td>";
				echo "<td align=left  >".$row[10]."</td>";
				echo "<td align=left  >".$row[11]."</td>";
				echo "<td align=left  >".number_format($fact,0,'.',',')."</td>";
				echo "<td align=left  >".number_format($nfact,0,'.',',')."</td></tr>";
				$LineaDato = "";
				for ($j = 0; $j <= 11; $j++)
				  {
					$row[$j]= str_replace("|", ' ',$row[$j]);
					$LineaDato=$LineaDato.$row[$j]."|";
					$LineaDato = str_replace(chr(13).chr(10) , ' ',$LineaDato); 
					$LineaDato = str_replace("\n", ' ', $LineaDato);
				  }
				  $fact= str_replace("|", ' ',$fact);
				  $nfact= str_replace("|", ' ',$nfact);
				  $LineaDato=$LineaDato.$fact."|".$nfact."|";
				  $LineaDato = str_replace(chr(13).chr(10) , ' ',$LineaDato); 
				  $LineaDatos = str_replace("\n", ' ', $LineaDatos);
				  
				  fwrite($archivo,$LineaDato.chr(13).chr(10) );
								
		}		
		echo "</table>";
		fclose($archivo);
		echo "<li><A href='rep_cargospac.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
       	echo "<br>";
       	echo "<li>Registros generados: ".$num;
				
		// Botones de "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wfecini\",\"$wfecfin\",\"$aux1\",\"$wemp_pmla\",\"$bandera\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
  }
}
?>
</body>
</html>