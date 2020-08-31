<html>
<head>
<title>REPORTE DE FACTURACIÓN RADICADA</title>
<script type="text/javascript">

	//Validación de los datos del formulario
	function valida_enviar(form)
	{
		form.submit();
	}

	 //Redirecciona a la pagina inicial
	 function inicioReporte(wemp_pmla,wfini,wffin,wfrad,wemp){
	 	document.location.href='RepFacRad.php?wemp_pmla='+wemp_pmla+'&wfini='+wfini+'&wffin='+wffin+'&wfrad='+wfrad+'&wemp='+wemp;	 		
	 }

</script>

</head>
<body>
<?php
include_once("conex.php");
/**********************************************************
*     REPORTE DE FACTURAS RADICADAS POR FECHA Y EMPRESA   *
**********************************************************/
   
//==========================================================================================================================================
//PROGRAMA				      : REPORTE DE FACTURAS RADICADAS POR FECHA Y EMPRESA                                                         	|
//AUTOR                       : John M. Cadavid García.																						|
//FECHA CREACION              : Enero 12 de 2011																							|
//FECHA ULTIMA ACTUALIZACION  : Enero 19 de 2011																							|
//DESCRIPCION			      : Reporte que muestra las facturas radicadas con base en la empresa y el intervalo de fechas especificadas	| 
//								Muestra la fecha de radicación, la empresa o entidad responsable, la cantidad de facturas, 					|
//								la suma total del valor de facturas y los totales finales													| 
//								Variables: Intervalo de fechas a analizar y resposble que puede ser todos									|
//                                                                                                                                          |
//==========================================================================================================================================

$wactualiz="Vers. 1 | Enero 19 de 2011";

//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//________________________________________________________________________________________________________________________________________\\
//																																		  \\
//Acá se coloca las fechas y descripciones de las actualizaciones realizadas al reporte													  \\
//________________________________________________________________________________________________________________________________________\\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\         

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

//Validación de usuario
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
encabezado("REPORTE DE FACTURACIÓN RADICADA ",$wactualiz,"clinica");

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
	


	// Consulto los datos de la empresa actual y los asigno a la variable $empresa
	$consulta = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	$empresa = $consulta->baseDeDatos;
      
	echo "<form name='form1' action='RepFacRad.php' method=post onSubmit='return valida_enviar(this);'>";
  
    //Aca traigo las variables necesarias de la empresa
	$q = " SELECT empdes, emphos "
	    ." FROM root_000050 "
	    ." WHERE empcod = '".$wemp_pmla."'"
	    ." AND empest = 'on' ";
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res); 
	  
	$wnominst=$row[0];
	$whosp=$row[1];
	 
	//Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
	$q = " SELECT detapl, detval, empdes, empbda, emphos "
	    ." FROM root_000050, root_000051 "
	    ." WHERE empcod = '".$wemp_pmla."'"
	    ." AND empest = 'on' "
	    ." AND empcod = detemp "; 
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res); 
	  
	if ($num > 0 )
	{
		for ($i=1;$i<=$num;$i++)
	    {   
	      $row = mysql_fetch_array($res);
	      
	      $wbasedato=strtolower($row[3]);   //Base de dato de la empresa
	      $wemphos=$row[4];     //Indica si la facturacion es Hospitalaria o POS
	      
	      if ($row[0] == "cenmez")
	         $wcenmez=$row[1];
	      if ($row[0] == "afinidad")
	         $wafinidad=$row[1];
	      if ($row[0] == "movhos")
	         $wbasedato=$row[1];
	      if ($row[0] == "tabcco")
	         $wtabcco=$row[1];
	      if ($row[0] == "camilleros")
	         $wcencam=$row[1];   
		         
	      $winstitucion=$row[2];   
	    }  
	}
	else
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
  

	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";	
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'form' value='form1'>";
  
	$wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla   

	//===========================================================================================================================================
	//INICIO DEL PROGRAMA   
	//===========================================================================================================================================
  
 	if (!isset($form) or $form == '')
    {

		// Declaro las variables de fechas y les asigno la fecha actual si no estan aún definidas
		if (!isset($wfini) or $wfini == '')
			$wfini = date("Y-m-d");
		if (!isset($wffin) or $wffin == '')
			$wffin = date("Y-m-d");
		if (!isset($wfrad) or $wfrad == '')
			$wfrad = date("Y-m-d");
		if (!isset($wemp) or $wemp== '')
			$wemp = '';
			
		//Inicio tabla de ingreso de parametros
		echo "<table align='center' border=0>";
		
		//Petición de ingreso de parametros
		echo "<tr>";
		echo "<td height='37' colspan='2'>";
		echo '<p align="left" class="titulo"><strong> &nbsp; Seleccione los datos a consultar &nbsp;  &nbsp; </strong></p>';
		echo "</td></tr>";
	
		//Solicitud fecha inicial de facturación
		echo "<tr>";
		echo "<td class='fila1' width=201>Fecha inicial de facturación</td>";
		echo "<td class='fila2' align='left' width=141>";
		campoFechaDefecto("wfini",$wfini);
		echo "</td></tr>";
			
		//Solicitud fecha final de facturación
		echo "<tr>";
		echo "<td class='fila1'>Fecha final de facturación</td>";
		echo "<td class='fila2' align='left'>";
		campoFechaDefecto("wffin",$wffin);
		echo "</td></tr>";
		
		//Solicitud fecha final de radicación
		echo "<tr>";
		echo "<td class='fila1'>Fecha Final de radicación</td>";
		echo "<td class='fila2' align='left'>";
		campoFechaDefecto("wfrad",$wfrad);
		echo "</td></tr>";

		//Solicitud de el responsable
		echo "<tr>";
		echo "<td class='fila1'>Responsable</td>";
		echo "<td class='fila2' align='left'>";

		// Consulto los responsables registrados en la base de datos
		$q1 = " SELECT empcod, empnom "
			 ." FROM ".$wbasedato."_000024 "
			 ." WHERE empcod = empres "
			 ." AND empcod <> '01' "
			 ." ORDER BY empnom"; 
		$res1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num1 = mysql_num_rows($res1);
		$select = '';   

		// Campo select que va a desplegar los responsables
		echo "<select name='wemp2'>";   
		echo "<option value='%'> - - - - - - - TODOS - - - - - - - </option>";
		if ($num1 > 0 )
		{
			for ($i=1;$i<=$num1;$i++)
			{
				$select = '';   
				$row1 = mysql_fetch_array($res1); 
				if ($wemp==$row1[0])
					$select = ' selected';   
				echo "<option ".$select.">".$row1[0]." - ".$row1[1]."</option>"; 
			} 
		} 
		echo "</select>"; 
		echo "</td></tr>";
						
		//Botones "Consultar" y "Cerrar ventana"
		echo "<tr><td align=center colspan=4><br /><input type='submit' id='searchsubmit' value='Consultar'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          
		echo "</table>";
	
	} 
	else 
	{

		list($wemp) = explode(' - ',$wemp2);
 
		//Muestro los parámetros que se ingresaron en la consulta
		echo "<table border=0 cellspacing=2 cellpadding=0 align=center size='300'>"; 
		echo "<tr class='fila2'>";
		echo "<td align=left width=201><strong>&nbsp;Fecha inicial facturación <s/trong></td>";
		echo "<td align=left width=201><strong>&nbsp;Fecha final facturación </strong></td>";
		echo "<td align=left width=201><strong>&nbsp;Fecha final radicación </strong></td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
		echo "<td align=left>&nbsp;".$wfini."</td>";
		echo "<td align=left>&nbsp;".$wffin."</td>";
		echo "<td align=left>".$wfrad."</td>";
		echo "</tr>";
		echo "</tr>";
		if($wemp!='%') 
		{
			echo "<tr class='fila2'>";
			echo "<td colspan='3' align=left><strong> Responsable: ".$wemp2."</strong></td>";
			echo "</tr>";
		}
		echo "</table>";

		    
		/*****************************************************************************
		***** INICIO DE REPORTE DE FACTURAS RADICADAS  *******************************
		*****************************************************************************/				
		    
		// Borra la tabla temporal
		$qdel = "DROP TABLE IF EXISTS rad";
		$resrad = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());
		
		// Crea una tabla temporal que me permita tener el registros de radicadas sin duplicados
		// Esto para que en la sumatoria de radicadas no me repita valores de una misma factura que se ha radicado 2 o mas veces
		$qrad =  " CREATE TEMPORARY TABLE IF NOT EXISTS rad AS "
				." SELECT rdefac, rdefue, rdeffa, rdenum, rennom, renfec, renfue, rennum, rencod "                    
				." FROM  ".$wbasedato."_000021, ".$wbasedato."_000020 "
				." WHERE rencod LIKE '".$wemp."' "
				." AND rdefue = renfue "
				." AND rdenum = rennum "
				." AND renfec between '".$wfini."' AND '".$wfrad."' "
				." GROUP BY rdefac, rdefue  ";

		$resrad = mysql_query($qrad, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qrad . " - " . mysql_error());

		// Consulta las facturas radicadas segun las fechas de facturación que solicite el usuario, agrupadas por mes y calcula la sumatoria de estas
		$q = 	 " SELECT rencod, rennom, YEAR(renfec) AS anio, MONTH(renfec) AS mes, COUNT(fenfac), SUM(fenval+fenabo+fencmo+fencop+fendes) "
				." FROM  ".$wbasedato."_000018, rad, ".$wbasedato."_000040 "
				." WHERE fenfec between '".$wfini."' AND '".$wffin."'"
				." AND fenest = 'on' "
				." AND fenffa IN (  SELECT carfue "
				."					FROM ".$wbasedato."_000040 "
				."					WHERE carfac = 'on' ) "
				." AND fenffa = rdeffa "
				." AND fenfac = rdefac "
				." AND rdefue = carfue "
				." AND carrad = 'on' "
				." GROUP BY rencod, rennom, anio, mes  "
				." ORDER BY renfec ";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res);
			 
		if ($num > 0)
		{

			$row = mysql_fetch_array($res);
			$i=1;
			$concanttot=0;   //Variable para llevar sumatoria de cantidades
			$contottot = 0;  //Variable para llevar sumatoria de totales
			
			echo "<table border=0 cellspacing=2 cellpadding=0 align=center>"; 
			//Ciclo para recorrer todos los registros de la consulta
			while ($i<=$num)
			{

				//Muestro el mes como encabezado para despues mostrar sus facturas radicadas
				echo "<tr>";
				echo "<td align=center colspan=3 height=21>&nbsp;</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td colspan=3 class='titulo'>&nbsp;".mes_texto($row[3])." de ".$row[2]."</td>";
				echo "</tr>";

				echo "<tr class='encabezadoTabla'>";
				echo "<th>RESPONSABLE</th>";
				echo "<th>CANTIDAD</th>";
				echo "<th>VALOR</th>";
				echo "</tr>";
				 
				$ii = 1;
				$concant=0;   	//Variable para llevar sumatoria de cantidades por cada mes
				$contot = 0;  	//Variable para llevar sumatoria de totales por cada mes
				$aux = $row[3]; //Auxiliar para saber cuando se cambia de mes
				 
				//Ciclo para recorrer los registros de cada mes
				while($row[3]==$aux && $i<=$num) 
				{
					if (is_int ($ii/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
				
					//Variables para calculo de cantidades y sumas totales
					$concant += $row[4]; 	//Sumatoria cantidad mes
					$contot += $row[5];  	//Sumatoria costos totales mes
					$concanttot += $row[4]; //Sumatoria cantidad
					$contottot += $row[5];  //Sumatoria costos totales 
											
					//Se imprime los valores de cada fila
					echo "<tr class=".$wcf.">";
					echo "<td align=left>&nbsp;".$row[0]." - ".$row[1]."&nbsp;</td>";
					echo "<td align=center>&nbsp;".$row[4]."&nbsp;</td>";
					echo "<td align=right>&nbsp;".number_format((double)$row[5],2,'.',',')."&nbsp;</td>";
					echo "</tr>";
			
					//Obtengo la siguiente fila
					$row = mysql_fetch_array($res);
					$ii++;
					$i++;
				} //Fin Ciclo para recorrer los registros de cada concepto
			
				//Imprimo totales por mes
				echo "<tr class='encabezadoTabla'>";
				echo "<td align=left> &nbsp; TOTAL MES &nbsp; </td>";
				echo "<td align=center> &nbsp; ".$concant." &nbsp; </td>";
				echo "<td align=right> &nbsp; ".number_format((double)$contot,2,'.',',')." &nbsp; </td>";
				echo "</tr>";
			}

			//Imprimo totales
			echo "<tr>";
			echo "<td align=center colspan=3 height=21>&nbsp;</td>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla' height=27>";
			echo "<td align=left> &nbsp; TOTAL FACTURAS RADICADAS &nbsp; </td>";
			echo "<td align=center> &nbsp; ".$concanttot." &nbsp; </td>";
			echo "<td align=right> &nbsp; ".number_format((double)$contottot,2,'.',',')." &nbsp; </td>";
			echo "</tr>";

			$concanrad = $concanttot;
			$contotrad = $contottot;
	 	} 
		else 
		{
			echo "<br /><p align='center'>No se encontraron facturas radicadas para la consulta</p>";
		}


		 /*****************************************************************************
		 ***** INICIO DE REPORTE DE FACTURAS NO RADICADAS  ****************************
		 *****************************************************************************/				

		// Borra la tabla temporal
		$qdel2 = "DROP TABLE IF EXISTS rad2";
		$resrad2 = mysql_query($qdel2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel2 . " - " . mysql_error());
		
		// Crea una tabla temporal que me permita tener el registros de radicadas sin duplicados
		// Esto para que en la sumatoria de radicadas no me repita valores de una misma factura que se ha radicado 2 o mas veces
		$qrad2 = " CREATE TEMPORARY TABLE IF NOT EXISTS rad2 AS "
				." SELECT rdefac, rdefue, rdeffa, rdenum, rennom, renfec, renfue, rennum, rencod "                    
				." FROM  ".$wbasedato."_000021, ".$wbasedato."_000020 "
				." WHERE rencod LIKE '".$wemp."' "
				." AND rdefue = renfue "
				." AND rdenum = rennum "
				." AND renfec > '".$wfini."' "
				." GROUP BY rdefac, rdefue  ";

		$resrad2 = mysql_query($qrad2, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qrad2 . " - " . mysql_error());

		// Consulta las facturas que no han sido radicadas segun las fechas de facturación que solicite el usuario
		$q = " (SELECT empcod, empnom, COUNT( fenfac ), SUM( fenval+fenabo+fencmo+fencop+fendes ) "
			." FROM  ".$wbasedato."_000018, ".$wbasedato."_000024 "
			." WHERE  fenfec between '".$wfini."' AND '".$wffin."'"
			." AND fenfac NOT IN (  SELECT rdefac "
			." 						FROM ".$wbasedato."_000021, ".$wbasedato."_000040 "
			." 						WHERE rdefac = fenfac "
			." 						AND rdefue = carfue "
			." 						AND carrad = 'on' ) "
			." AND fencod LIKE '".$wemp."' "
			." AND fenest = 'on' "
			." AND fencod = empcod "
			." GROUP BY empcod, empnom, fencod ) "
			." UNION ALL "
			." (SELECT rencod, rennom, COUNT(fenfac), SUM(fenval+fenabo+fencmo+fencop+fendes) "
			." FROM  ".$wbasedato."_000018, rad2, ".$wbasedato."_000040 "
			." WHERE fenfec between '".$wfini."' AND '".$wffin."'"
			." AND fenest = 'on' "
			." AND fenffa IN (SELECT carfue FROM ".$wbasedato."_000040 WHERE carfac = 'on') "
			." AND fenffa = rdeffa "
			." AND fenfac = rdefac "
			." AND rdefue = carfue "
			." AND renfec > '".$wfrad."' "
			." AND carrad = 'on' "
			." GROUP BY rencod, rennom)  "
			." ORDER BY 1, 2 ";

		$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
		$num = mysql_num_rows($res); 

		$concanttot = 0;   //Variable para llevar sumatoria de cantidades
		$contottot = 0;  //Variable para llevar sumatoria de totales

		if ($num > 0)
		{
			$row = mysql_fetch_array($res);
			$i=1;
			$ii=1;
				
			//Muestro el encabezado de las facturas no radicadas
			echo "<tr>";
			echo "<td align=center colspan=3 height=41>&nbsp;</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td colspan=3 class='titulo'>&nbsp;Facturas no radicadas</td>";
			echo "</tr>";

			echo "<tr class='encabezadoTabla'>";
			echo "<th>RESPONSABLE</th>";
			echo "<th>CANTIDAD</th>";
			echo "<th>VALOR</th>";
			echo "</tr>";
			 
			     
		    //Ciclo para recorrer todos los registros de la consulta
			while ($i<=$num)
			{
				if (is_int ($ii/2))
				   $wcf="fila1";  // color de fondo de la fila
				else
				   $wcf="fila2"; // color de fondo de la fila
				
				$cod = $row[0];
				$nom = $row[1];
				$can = $row[2];
				$tot = $row[3];
				
				//Obtengo la siguiente fila
				$row = mysql_fetch_array($res);
				while($row[0] == $cod)
				{
					$can += $row[2];
					$tot += $row[3];
					$row = mysql_fetch_array($res);
					$i++;
					$ii++;
				}

				//Variables para calculo de cantidades y sumas totales
				$concanttot += $can; //Sumatoria cantidad
				$contottot += $tot;  //Sumatoria costos totales 
										
				//Se imprime los valores de cada fila
				echo "<tr class=".$wcf.">";
				echo "<td align=left>&nbsp;".$cod." - ".$nom."&nbsp;</td>";
				echo "<td align=center>&nbsp;".$can."&nbsp;</td>";
				echo "<td align=right>&nbsp;".number_format((double)$tot,2,'.',',')."&nbsp;</td>";
				echo "</tr>";
		
				$i++;
				$ii++;
			}

			//Imprimo totales
			echo "<tr class='encabezadoTabla' height=21>";
			echo "<td align=left> &nbsp; TOTAL FACTURAS NO RADICADAS &nbsp; </td>";
			echo "<td align=center> &nbsp; ".$concanttot." &nbsp; </td>";
			echo "<td align=right> &nbsp; ".number_format((double)$contottot,2,'.',',')." &nbsp; </td>";
			echo "</tr>";

			//Imprimo total radicadas mas no radicadas
			$concan = $concanrad + $concanttot;
			$contot = $contotrad + $contottot;
			echo "<tr height=31>";
			echo "<td colspan='3'> &nbsp; </td>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla' height=21>";
			echo "<td align=left> &nbsp; TOTAL RADICADAS MAS NO RADICADAS &nbsp; </td>";
			echo "<td align=center> &nbsp; ".$concan." &nbsp; </td>";
			echo "<td align=right> &nbsp; ".number_format((double)$contot,2,'.',',')." &nbsp; </td>";
			echo "</tr>";
	 	}
				
		echo "</table>";
				
		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfini\",\"$wffin\",\"$wfrad\",\"$wemp\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
					
	}	    
}
?>
</body>
</html>
