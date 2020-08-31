<head>
  <title>REPORTE DE FACTURADO A CENTROS DE COSTOS</title>
</head>
<body>
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");

/****************************************************************
 * 
 * Actualizaciónes:
 * 
 * Abril 30 de 2009.
 * 
 * -  Se corrige la consulta que busca los registros seleccionados
 *    ya que repetia muchos or. 
 * -  Se corrige presentación.
 * -  Se incorpora Calendario.
 
 2012-06-21:  Se agregan las consultas consultarCentroCostos y dibujarSelect que listan los centros  
 *              de costos de un grupo dado en orden alfabetico y dibuja el select con esos centros   
 *              de costo respectivamente Viviana Rodas
 * 
 2012-06-25: Se cambio .$wcco. por .trim($wcco). en las consultas que traen los medicamentos porque 
 *				cuando se consultan todos, enviaba % con un espacio.
 *
 2013-11-06: Se agregan "UNION" a las consultas donde se encuentre la tabla movhos_000003 para que traiga los  
			 datos de contingencia (tabla movhos_00143) con estado activo. Jonatan Lopez	
 *
 ****************************************************************/

session_start();

if (!isset($user))
if (!isset($_SESSION['user']))
session_register("user");

session_register("wemp_pmla", "wcen_mezc");

if (!isset($_SESSION['user']))
echo "error";
else
{
	

    

    include_once("root/comun.php");

	// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wactualiz = "(Noviembre 6 de 2013)";             // Aca se coloca la ultima fecha de actualizacion de este programa //
	// =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	echo "<form name='stock' action='facxser.php' method=post>";
	
	echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
	
	$wfecha = date("Y-m-d");
	$whora = (string)date("H:i:s");

	encabezado("CONSULTA DE SERVICIOS DE CENTRAL DE HABITACIONES",$wactualiz, "clinica");
	
	if (strpos($user, "-") > 0)
	   $wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
	   
	// Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
	$q = " SELECT detapl, detval "
	. "   FROM root_000050, root_000051 "
	. "  WHERE empcod = '" . $wemp_pmla . "'"
	. "    AND empest = 'on' "
	. "    AND empcod = detemp ";
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i = 1;$i <= $num;$i++)
		{
			$row = mysql_fetch_array($res);
			if ($row[0] == "cenmez")
			$wcenmez = $row[1];

			if ($row[0] == "afinidad")
			$wafinidad = $row[1];

			if ($row[0] == "movhos")
			$wbasedato = $row[1];

			if ($row[0] == "tabcco")
			$wtabcco = $row[1];
		}
	}
	else
	{
		echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	}

	if (!isset($wcco) or trim($wcco) == "")
	{
		// /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//se llaman a las funciones consultaCentrosCostos y dibujarSelect
		
		$cco="Ccohos";
		$sub="off";
		$tod="Todos";
		//$cco=" ";
		$ipod="off";
		$centrosCostos = consultaCentrosCostos($cco);
					
		echo "<table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		echo $dib;
										
		echo "</table>";
								 
		echo "<center><table cellspacing=1>"; 
	    echo "<br>";
      
		echo "<tr class=seccion1><td colspan=1>DESDE: </font></td>";
		echo "<td>";
		
		/**
		 * Campo para Fecha Inicial
		 */
		
		campoFechaDefecto("fecha1",date("Y-m-d"));

		echo "</select></td></tr>";
		echo "<tr class=seccion1><td colspan=1>HORA: </font></td>";
		echo "<td><select name='hora1'>";
		echo "<option>07:00:00</option>";
		echo "<option>19:00:00</option>";
		echo "<option>00:00:00</option>";
		echo "</select></td></tr>";

		echo "<tr class=seccion1><td colspan=1>HASTA: </font></td>";
		echo "<td >";
		
		/**
		 * Campo para Fecha Final
		 */
		campoFechaDefecto("fecha2",date("Y-m-d"));
		
		echo "</select></td></tr>";
		/**
		 * Select de la hora final
		 */
		echo "<tr class=seccion1><td colspan=1>HORA: </font></td>";
		echo "<td ><select name='hora2'>";
		echo "<option>06:59:59</option>";
		echo "<option>18:59:59</option>";
		echo "</select></td></tr>";

		echo "<tr><td align=center colspan=2><br><input type='submit' value='ENTRAR'> | <input type=button value='Cerrar' onclick='cerrarVentana()'></td></tr>";
		echo "</table>";
		
	}
	else
	{	
	      	
		if ($wcco == '')
		{
			$wcco = '%';
			$wnomcco='Todos los servicios';
			
		}
		else
		{
			
			$wnomcco=$wcco;  //nombre completo con el codigo y el nombre
			$wccosto = explode("-", $wcco);
			$wcco = $wccosto[0];   //codigo
			
					
			
		}

		$fechas="";
		
		if( $fecha2-$fecha1 > 2 ){
			$fechas = "OR A.fecha_data BETWEEN '".date("Y-m-d",mktime(0,0,0,$month1,$day1+1,$year1))."' AND '".date("Y-m-d",mktime(0,0,0,$month2,$day2-1,$year2))."' ";
			echo $fechas;
			$hora="((A.Fecha_data='".$fecha1."'  and A.Hora_data between '".$hora1."' and '24:00:00') or (A.Fecha_data='".$fecha2."'  and A.Hora_data between '00:00:00' and '".$hora2."') ".$fechas.")";
		}				
		else{
			//fecha1==fecha2 o menos de un dìa de diferencia
			if($fecha1==$fecha2){
				$hora="A.Fecha_data='".$fecha1."' and A.Hora_data between '".$hora1."' and '".$hora2."' {$fechas}";
			}
			else{
				$hora="((A.Fecha_data='".$fecha1."'  and A.Hora_data between '".$hora1."' and '24:00:00') or (A.Fecha_data='".$fecha2."'  and A.Hora_data between '00:00:00' and '".$hora2."') )";
			}
		}	

		$exph1=explode(':',$hora1);
		$exph2=explode(':',$hora2);
		$expf1 = explode("-",$fecha1);
		$expf2 = explode("-",$fecha2);		
		$tiempo = mktime($exph2[0], $exph2[1], $exph2[2],  $expf2[1], $expf2[2], $expf2[0]) - mktime($exph1[0], $exph1[1], $exph1[2], $expf1[1], $expf1[2],  $expf1[0]);
		
		$tiempo=$tiempo/(60*60*24);
		
		if($tiempo<1)
		{
			$tiempo=1;
		}
		
		/**
		 * Busca los registros que corresponden a los encabezados ya seleccionados
		 * y que tambien esten dentro de las fechas y horas estipuladas
		 */
		//2008-03-31
		
		$facfde="repRepFacfde".date("Yis");
	 $q = " CREATE TEMPORARY TABLE ".$facfde." "
		."     SELECT Fdeart as art, SUM(Fdecan) as can,  Artcom as nom, Unides as uni "
		."       FROM  ".$wbasedato."_000002, ".$wbasedato."_000003 A, ".$wbasedato."_000026, ".$wbasedato."_000027 "
		."      WHERE ".$hora." "
		."        AND Fennum = Fdenum "
		."        AND Fenfue = 'GD'"
		."        AND Fdeest = 'on' "
		."		  AND Fdeser LIKE '".trim($wcco)."'"
		."        AND Fdeart = Artcod "
		."        AND Artuni = unicod  "
		." GROUP BY Fdeart "
		/*********************************************************************************************************************/
		/* Noviembre 06 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."      UNION "
		."     SELECT Fdeart as art, SUM(Fdecan) as can,  Artcom as nom, Unides as uni "
		."       FROM  ".$wbasedato."_000002, ".$wbasedato."_000143 A, ".$wbasedato."_000026, ".$wbasedato."_000027 "
		."      WHERE ".$hora." "
		."        AND Fennum = Fdenum "
		."        AND Fenfue = 'GD'"
		."        AND Fdeest = 'on' "
		."		  AND Fdeser LIKE '".trim($wcco)."'"
		."        AND Fdeart = Artcod "
		."        AND Artuni = unicod  "		
		." GROUP BY Fdeart "
		." ORDER BY 3 ";		
		$err = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
		echo mysql_error();

		/**
		 * Se el grupo y las unidades del artículo en el maestro de artículos
		 * para todos los registros cuya fuente sea de carga y se almacenan en otra tabla
		 */
		//2008-03-31
		$facfdeFin="repRepFacfdeFin".date("Yis");
		$q = " CREATE TEMPORARY TABLE ".$facfdeFin." "
		."     SELECT Fdeart, sum(Fdecan) as Fdecan"
		."       FROM  ".$wbasedato."_000035 A, ".$wbasedato."_000028, ".$wbasedato."_000003 "
		."      WHERE Denori LIKE '".trim($wcco)."' "
		."        AND ".$hora." "
		."        AND Dencon=Devcon "
		."        AND Devnum = Fdenum "
		."        AND Devlin = Fdelin "
		." GROUP BY Fdeart "
		/*********************************************************************************************************************/
		/* Noviembre 06 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
		/*********************************************************************************************************************/
		."      UNION "
		."     SELECT Fdeart, sum(Fdecan) as Fdecan"
		."       FROM  ".$wbasedato."_000035 A, ".$wbasedato."_000028, ".$wbasedato."_000143 "
		."      WHERE Denori LIKE '".trim($wcco)."' "
		."        AND ".$hora." "
		."        AND Dencon=Devcon "
		."        AND Devnum = Fdenum "
		."        AND Devlin = Fdelin "
		."        AND Fdeest = 'on'"
		." GROUP BY Fdeart ";
		$err = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
		echo mysql_error();
		

		/**
		 * Se restan las cantidades de los registros que hayan sido cargados con fuentes de devolución.
		 */
		$q = "UPDATE ".$facfdeFin.", ".$facfde." "
		."       SET can = can - Fdecan "
		."     WHERE Fdeart = art ";
		$err = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
		echo mysql_error();

		$q = " SELECT *  "
		."       FROM ".$facfde." "
		."      WHERE can > 0 ";
		
		$err = mysql_query($q,$conex)or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);

		echo "<h2 class=seccion1><b>Servicio o Unidad: ".$wnomcco."</b></h2>";

		//Encabezado de fecha y hora
		echo "<table align=center cellspacing=0>
					<tr class=encabezadotabla>
						<th width=100>Fecha Inicial</th>
						<th width=100>Hora Inicial</th>
						<th width=100>Fecha Final</th>
						<th width=100>Hora Final</th>
					</tr><tr class=fila1>
						<td align=center>$fecha1</td>
						<td align=center>$hora1</td>
						<td align=center>$fecha2</td>
						<td align=center>$hora2</td>
					</tr>
			 </table>";
		
		echo '<table align=center>';
//		echo "<tr style='font-size:11pt'><td colspan=5><b>Periodo</b>:  
//			 <br><b>Desde:</b> $fecha1, a las $hora1 <br><b>Hasta:</b> $fecha2, a las $hora2<tr><td><br>";
		echo "<tr class=encabezadoTabla>";
		echo "<th>Codigo Articulo</th>";
		echo "<th>Nombre Articulo</th>";
		echo "<th>Cantidad</th>";
		echo "<th>Promedio por dia</th>";
		echo "<th>Unidad</th>";
		echo "</tr>";

		if ($num > 0)
		{
			for($i = 1;$i <= $num;$i++)
			{
				$row = mysql_fetch_array($err);

				if (is_integer($i / 2))
				$wclass = "fila1";
				else
				$wclass = "fila2";

				echo "<tr class=".$wclass.">";
				echo "<td align=left>".$row[0]."</td>";
				echo "<td align=left>".$row[2]."</td>";
				echo "<td align=right>".$row[1]."</td>";
				echo "<td align=right>".round($row[1]/$tiempo, 2)."</td>";
				echo "<td align=left>".$row[3]."</td>";
				echo "</tr>";
			}
		}
		else
		{
			echo "NO HAY CARGOS A SERVICIO EN LAS FECHAS INDICADAS";
		}
		echo '</div>';
		echo '</div>';
		echo '</div>';

		echo "<tr>";
		echo"<tr><td><tr><br><td colspan=5 align=center><input type='submit' value='Retornar'>";
		echo " | <input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
//		echo "<tr><th align=center colspan=10><A href='stock.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."'><b>Inicio</b></A></td>";
		echo "</tr>";
	}
	echo "</table>";
	

    echo "</form>";
    
    echo "<input type='HIDDEN' name='wemp_pmla' value='" . $wemp_pmla . "'>";
	echo "<input type='HIDDEN' name='wbasedato' value='" . $wbasedato . "'>";

//	echo "<center><table>"; 
//	echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
//	echo "</table>";
} // if de register
include_once("free.php");
?>
