<html>
<head>
<title>Reporte promedio Urgencias
</title>
</head>
<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();		  
     }
	 
</script>
<script type="text/javascript">
	function inicio(){ 
		document.location.href='rep_PromedioUrgencias.php?wemp_pmla='+document.forms.forma.wemp_pmla.value; 
	}
</script>	
<body>
<?php
include_once("conex.php");

/**
* REPORTE INDICADOR DE OPORTUNIDAD DE ATENCION EN URGENCIAS                                                *
*/
// ===========================================================================================================================================
// PROGRAMA				      :Reporte de tiempo promedio de atencion en urgencias                                                			 |
// AUTOR				      :Ing. Luis Haroldo Zapata Arismendy                                                                        	 |
// FECHA CREACION			  :Diciembre 9 de 2011.                                                                                          |
// FECHA ULTIMA ACTUALIZACION :Diciembre 15 de 2011.                                                                                       	 |
// DESCRIPCION			      :Reporte para saber cual es el tiempo promedio de atencion en urgencias, desde que ingresa el paciente 
//							   hasta el momento en que es atendido por el médico.  															 |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// HCE_000022		 		  :Tabla que contiene la fecha y hora de llegada del paciente y la fecha y hora en que fue atendido por 
//							   el médico.
// MOVHOS_000018			  :Tabla que contiene los centros de costos con los que se va a hacer la relacion con la tabla HCE_000022	
//                                                                                         													 |
// ==========================================================================================================================================
//	Modificaciones:
//  Noviembre 18 de 2021   Daniel CB.   - Se realiza corrección de parametro 01 quemado.	
// ==========================================================================================================================================

$wactualiz = "2021-11-18";

	
//================================================================================
include_once("root/comun.php");

if(!isset($_SESSION['user']))
	exit("error session no abierta");


$conex = obtenerConexionBD("matrix");
if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wmovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

Encabezado("INDICADOR DE OPORTUNIDAD DE ATENCION EN URGENCIAS", $wactualiz  ,"clinica");

if( !isset($mostrar) ){  
	$mostrar = 'off';
}

echo "<form action='rep_PromedioUrgencias.php?wemp_pmla=$wemp_pmla' method='post'>";

if( $mostrar == 'off' )				//si no hay rango de fechas entonces  pedirlos al usuario
{

	if( !isset( $fechafin ) ){
		$fechafin = date("Y-m-d");
	}
	
	if( !isset( $fechaini ) ){
		$fechaini = date("Y-m-01");
	}

	//Buscando los centros de costos

	$sql = "SELECT cconom, ccocod 
			FROM
				".$wmovhos."_000011
			WHERE ccoest = 'on'
			AND ccoing = 'on'";
	
	$res = mysql_query( $sql );

	echo "<br><br><table align='center'>";
	echo " <tr class='encabezadotabla'>";
	echo "	<td align='center'>Centro de Costos</td>";
	echo "	</tr><tr class='fila1'>";
	echo "	<td align='center'>";
	echo "	<SELECT name='cco'>";
	echo "	<option value='1130 - URGENCIAS'>1130 - URGENCIAS</option>" ;

	//Generando los datos del Select
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		echo "<option value='{$rows['ccocod']} - {$rows['cconom']}'>{$rows['ccocod']} - {$rows['cconom']}</option>";
	}
				
	echo "</SELECT>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	echo "<br><br><table align='center'>";
	echo "<tr class='encabezadotabla'>";
	echo "<td align='center' style='width:200'>Fecha inicial</td>";
	echo "<td align='center' style='width:200'>Fecha final</td>";
	echo "</tr><tr class='fila1'>";
	echo "<td align='center'>";
	campoFechaDefecto( "fechaini", $fechaini );
	echo "</td>";
	echo "<td align='center'>";
	campoFechaDefecto( "fechafin", "$fechafin" );
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	//Botones ver y cerrar
	echo "<br><table align='center'>";
	echo  "<tr>";
	echo  "<td align='center' width='150'><INPUT type='submit' value='Ver' style='width:100' name='btVer'></INPUT></td>";
	echo  "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></INPUT></td>";
	echo  "</tr>";
	echo  "</table>";

	echo "<INPUT type='hidden' name='mostrar' value='on'>";
	
	echo "</form>";

}
else{
	
	//informacion ingresada por el usuario
	echo "<br><table align='center'>";
	echo "<tr align='left'>";
	echo "<td width='150' class='fila1'>Fecha inicial</td>";
	echo "<td width='150' class='fila2'>$fechaini</td>";
	echo "</tr>";
	echo "<tr class='fila1' align='left'>";
	echo "<td class='fila1'>Fecha final</td>";
	echo "<td class='fila2'>$fechafin</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1'>Centro de costos:</td>";
	echo "<td class='fila2'>$cco</td>";
	echo "</tr>";
	echo "</table><br><br>";

			
	$cco_str = explode(" - ",$cco);
	$cco = $cco_str[0];

	//==================================================================================================================
	//realizamos la consulta buscando el centro de costos que se relaciona entre movhos y hce
	//y que la historia del paciente en HCE sea igual a la historia del paciente ingresada en MOVHOS
	//y que el ingreso del paciente en HCE sea igual al ingreso del paciente en MOVHOS

	$q= "  SELECT 	  A.fecha_data,A.hora_data,A.mtrfco,A.mtrhco "
		."	FROM 	 ".$wbasedato."_000022 A,".$wmovhos."_000018 B"
		."	WHERE 	  A.mtrhco > A.hora_data "							   //Que la hora de atencion del medico sea mayor que la hora de entrada
		."	AND 	  A.fecha_data between '$fechaini' AND '$fechafin' "  //del facturaddor para que permita sacar la diferencia en tiempo correctamente
		."	AND 	  A.mtrhis=B.ubihis "
		."	AND 	  A.mtring=B.ubiing "
		."  AND 	  A.mtrhis not in (SELECT firhis from ".$wbasedato."_000036 WHERE firpro='000137' and firhis=mtrhis and firing=mtring)"
		."  AND 	  ubisac LIKE  '".$cco."' "
		."  ORDER BY  A.Fecha_data ASC";

	$result = mysql_query($q, $conex) or die("ERROR EN QUERY $q - ".mysql_error() );
				
	$num = mysql_num_rows( $result );

	$promdia = 0;						        	//variables inicializadas para los promedios por dia y por paciente
	$pacdia = 0;
	$j=1;								        	//se declara esta variable para la presentacion de los datos
	if( $num > 0 )
	{
		echo "<table align=center >";
		echo "<tr class='encabezadoTabla'>";
		echo "<td align=center>&nbsp;Fecha</td>";
		echo "<td align=center>&nbsp;Cantidad <br> de pacientes&nbsp;</td>";
		echo "<td align=center>&nbsp;Tiempo promedio de<br> atenci&oacute;n&nbsp; (HH:mm:ss)</td>";
		echo "</tr>";
		
		$rowpro = mysql_fetch_array( $result );
		
		$i = 0;
		$pac = 0;						         	//variables inicializadas para los promedios totales de cada dia
		$prom = 0;
		$wclass="fila2";

		while( $i < $num  )
		{
		    $fecha_data=$rowpro['fecha_data'];
		    $fecha_ant=$fecha_data;	
			
			while($fecha_data==$fecha_ant)
			{			                            //mientras que la fecha no cambie que realice la resta en tiempo en segundos 
													//(convirtiendo la fecha y la hora a traves de strtotime)
				$hora_data=$rowpro['hora_data'];	//despues de realizar la resta que vaya sumando las diferencias en tiempo con la variable $promdia
				$mtrfco=$rowpro['mtrfco'];			//y que sume los pacientes por dia a traves de la variable $pacdia
				$mtrhco=$rowpro['mtrhco'];
				$horaini="$fecha_data $hora_data";	//en la tabla, los campos de fecha_data y Hora_data son registrados desde el ingreso o llegada
				$horafin="$mtrfco $mtrhco";			//y los campos de mtrfco y mtrhco son registrados al momento de ingresar al consultorio.
									
				$fecha1 = strtotime($horaini) ; 
				$fecha2 = strtotime($horafin) ; 
				$fecha_res = ($fecha2 - $fecha1);
								 
				$prom=$prom+$fecha_res;		        //para sacar acumulados totales por rango de fechas
				$pac++;
				
				$promdia=$promdia+$fecha_res;       //para sacar acumulados por dia
				$pacdia++;		                    // y pacientes por dia
				
				$rowpro = mysql_fetch_array( $result );
				$fecha_data=$rowpro['fecha_data'];
				$i++;
			}
			
			if ($wclass=="fila1")
			   $wclass="fila2";
			  else
                 $wclass="fila1";			  
			
			$promediodia = $promdia/$pacdia;	    //con esta variable saco los promedios por dia y los muestro
			echo "<tr class='$wclass'>";								
			echo "<td align=center> $fecha_ant </td>";
			echo "<td align=center>$pacdia</td>";
			echo "<td align=center>".date("H:i:s", $promediodia + strtotime( "1970-01-01 00:00:00") )."</td>";
			echo "</tr>";

			$totalpac= $pac;
			$totalprom=$prom;
						
			$promdia = 0;
			$pacdia = 0;
			$j++;
			
		}	
		//Cuando termina de recorrer el ciclo imprimo el total de todos los pacientes de ese rango de fechas
		//y el promedio total obtenido del mismo.
		echo "<tr class='titulo'>";//este class es para colocarle color al total de los datos
		echo"<td align=center>Total periodo   </td>";
		
		if($totalpac>0)
		  {
		   $promedio = $totalprom/$totalpac; //para sacar el promedio total del rango de fechas
		  }
		 else
			$promedio= 0;
		
		echo "<td align=center> $pac</td>";
		echo "<td align=center>".date("H:i:s", $promedio + strtotime( "1970-01-01 00:00:00") )."</td>";
		echo "</tr></table>";
	}
	else
	  {									    //si al hacer el recorrido por fechas y al verificar el centro de costos 
											// no encuentra datos que imprima el mensaje.
		echo "<center><b>No se encontraron resultados</b></center>";
	  }
		
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td align=center width='150'>";
	echo "<INPUT type='submit' value='Retornar' style='width:100'>";
	echo "</td>";
	echo "<td align= center width='150'>";
	echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
	echo "</td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<INPUT type='hidden' name='mostrar' value='off'>";
	echo "<INPUT type='hidden' name='fechaini' value='$fechaini'>";
	echo "<INPUT type='hidden' name='fechafin' value='$fechafin'>";
	echo "</form>";
}	
?>

</body>
</html>