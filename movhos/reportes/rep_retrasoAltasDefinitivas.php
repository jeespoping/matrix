<html>
<head>
<title>REPORTE DE RETRASOS EN ALTAS DEFINITVAS</title>
</head>
<body>
<?php
include_once("conex.php");
//**************************************DESCRIPCION DEL PROGRAMA*************************************************//
//*Este programa consiste en mostrar un reporte de la cantidad de demoras en las altas definitivas, este reporte
//*se puede realizar por cco en un consolidado que se genera al elegir la opcion "Todos" en el menú inicial
//*en terminos generales lo que hace es que busca en las tablas 18 y 23 los pacientes que al ser dados de alta definitiva
//*se les agregó una justificacion por demora en el proceso de alta.
//*

//ACTUALIZACIONES 
// Julio 10 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de los centros de costos \\
//de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos de la primera funcion.                                                                                                                         \\
//=========================================================================================================================================\\
//DESCRIPCION  Mayo 17 de 2012                                                                                                           \\
//=========================================================================================================================================\\
//Se agregó la funcion ordnenar(), la cual tiene como fin organizar el vector de justificaciones en orden de ocurrencias, es decir, la justificacion  \\
//que mas se haya presentado en el periodo irá de primera, y la que se haya presentado menos aunque mayor a cero, se presentará al final.//
//=========================================================================================================================================\\


include_once("root/comun.php");
//*********************************************FUNCIONES********************************************************//
function generarMenuCco()
{
	global $wtabcco;
	global $wmovhos;
	global $conex;
	
	 $q = " SELECT ".$wtabcco.".Ccocod, ".$wtabcco.".Cconom "
          ."   FROM ".$wtabcco.", ".$wmovhos."_000011"
          ."  WHERE ".$wtabcco.".Ccocod = ".$wmovhos."_000011.Ccocod"
		  ."	AND ".$wmovhos."_000011.Ccohos = 'on'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
	  
	  echo "<select name='wcco'>";
		  echo "<option value='Todos'>Todos</option>";    
		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res); 
		      echo "<option value='".$row[0]." / ".$row[1]."'>".$row[0]." - ".$row[1]."</option>";
	         }
	      echo "</select>";
}

function menuInicial()
{
	global $fechaini;
	global $fechafin;
	global $wbasedato;
	global $wmovhos;
	$wbasedato=$wmovhos;
	
					
			//**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
	 	$cco="Ccohos";  // filtros para la consulta
		$sub="off";
		$tod="Todos";
		$ipod="off";  //para que pinte le select mediano
		//$cco="Todos";
		$filtro="--";
		$centrosCostos = consultaCentrosCostos($cco);  
		 
		
		echo "<table align='center' border=0>";		
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		echo $dib;
		echo "</table>";
			
		echo"<br>";	
		echo "<center><table width=350>";
			echo "<tr>";
				echo "<td class='fila1' algin=center width=100>Fecha Inicial</td>";
				echo "<td class='fila1' algin=center width=100>Fecha Final</td>";
			echo "</tr>";
			echo "<tr>";
				echo "<td class='fila2' algin=center width=150>";
				campoFechaDefecto( "fechaini", $fechaini );
				echo "</td>";
				echo "<td class='fila2' algin=center width=150>";
				campoFechaDefecto( "fechafin", $fechafin );
				echo "</td>";
			echo "</tr>";
		echo "</table></center>";
		echo "<center><table>";
			echo "<tr>";
				echo "<input type=hidden name='inicio' value=1>";
				echo "<td><input type='submit' value='Aceptar'></input></td>";
			echo "</tr>";
		echo "</table></center>";
}

//está funcion recibe el array de justificaciones y lo organiza 
function ordenar($array_justi)
{
	$justiOrd = array();
	$mas=true;
	do
	{	
		foreach($array_justi as $keyJusti=>$arrVal)
		{
			if(@!$mayor)
			{
				$justiMay = $keyJusti;
				$mayor = $arrVal;//$array_justi[$justiMay];
			}else//acá comparo lo valores 
				{
					if($mayor['total']<$arrVal['total'])
					{
						$justiMay = $keyJusti;
						$mayor =  $arrVal;//$array_justi[$justiMay];
					}
				}
		}
		//acá agrego el mayor al array nuevo y lo elimino del viejo.
		if(@!$justiOrd[$justiMay])
			{
				$justiOrd[$justiMay]=$mayor;
				unset($array_justi[$justiMay]);				
			}
			unset($mayor);
			if(sizeof($array_justi)<=0)
				$mas=false;
	}while($mas);
	//echo"<pre>";print_r($justiOrd);echo"</pre>";
	return($justiOrd);
}

function cantidadIncurrencias()
{
	global $wmovhos;
	global $wtabcco;
	global $fechaini;
	global $fechafin;
	global $conex;
	global $totretra;
	global $arrjusti;
	global $arrcanti;
	global $wcco;
	global $array_ccos;
	global $array_justi;
	
	$wcco1=explode("-",$wcco);
	$wcco=$wcco1[0];
	if($wcco=="%")
	{
			//buscar ccos que tuvieron retrasos para construir la cabezera
			$query1 = "SELECT  Ubisac, COUNT(a.id), b.Cconom "
					."  FROM ".$wmovhos."_000023, ".$wmovhos."_000018 a, ".$wtabcco." b,".$wmovhos."_000011 "
					." WHERE ( Ubifad BETWEEN  '".$fechaini."' and '".$fechafin."')"
					."	 AND ( Ubihad between '00:00:00' and '23:59:59')"
					."	 AND Ubijus = Juscod "
					."   AND ".$wmovhos."_000011.Ccocod = Ubisac"
					."   AND b.Ccocod = Ubisac "
					."	 AND Ccopal = 'on'"
					." GROUP BY 1 "
					."HAVING 2>0";
			$rs1 = mysql_query($query1, $conex) or die (mysql_errno().":".mysql_error());
			$rsnum1 = mysql_num_rows($rs1);
			for($i=0; $i<$rsnum1; $i++)
			{
				$reg1 = mysql_fetch_array($rs1);
				$array_ccos[$reg1[0]]['can'] = 0;
				$array_ccos[$reg1[0]]['des']=$reg1[2];	
			}
			
			//buscar justificaciones que han dado
		
			//acá se debe generar la matriz.
			$query = "SELECT Juscod, Jusdes, COUNT(".$wmovhos."_000018.id), Ubisac"
					."  FROM ".$wmovhos."_000023, ".$wmovhos."_000018, ".$wmovhos."_000011"
					." WHERE ( Ubifad BETWEEN  '".$fechaini."' and '".$fechafin."')"
					."	 AND ( Ubihad between '00:00:00' and '23:59:59')"
					."	 AND Ubijus = Juscod"
					."   AND Ubisac = Ccocod"
					."	 AND Ccopal = 'on'"
					." GROUP BY 1, 4"
					." ORDER BY 1";
			$rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
			$rsnum = mysql_num_rows($rs);
			for ($i=0; $i<$rsnum; $i++)
			{
				//se construye la matriz.
				$reg = mysql_fetch_array($rs);
				$array_justi[$reg[1]][$reg[3]]= $reg[2];
				if(!array_key_exists('total',$array_justi[$reg[1]]))
					{
						$array_justi[$reg[1]]['total']=0;
					}
					$array_justi[$reg[1]]['total']+= $reg[2];
			}
			//echo '<pre>';print_r($array_justi);echo '</pre>';
			$array_justi=ordenar($array_justi);
	}else
		{
		 $cco = explode("-",$wcco);
		  $cco = trim($cco[0]);
		  $wconcco=" And Ubisac = '".$cco."'";
		  //Apartir de acá generamos el reporte.
			$query = "SELECT Juscod, Jusdes, COUNT(".$wmovhos."_000018.id)"
					."  FROM ".$wmovhos."_000023, ".$wmovhos."_000018"
					." WHERE ( Ubifad BETWEEN  '".$fechaini."' and '".$fechafin."')"
					."	 AND ( Ubihad between '00:00:00' and '23:59:59')"
					."	 AND Ubijus = Juscod"
					     .$wconcco
					." GROUP BY 1, 2"
					." ORDER BY 3 DESC";

			$rs = mysql_query($query, $conex) or die (mysql_errno().":".mysql_error());
			$rsnum = mysql_num_rows($rs);
			for ($i=0; $i<$rsnum; $i++)
			{
				$reg = mysql_fetch_array($rs);
				$arrjusti[$i] = "$reg[0] - $reg[1]";
				$arrcanti [$i] = $reg[2];
				$totretra = $totretra + $reg[2];
			}
		}
						
}

function estadísticasConsolidadas()
{
	global $wemp_pmla;
	global $wmovhos;
	global $wtabcco;
	global $conex;
	global $totretra;
	global $arrcanti;
	global $arrjusti;
	global $array_ccos;
	global $array_justi;
	global $fechaini;
	global $fechafin;
	global $wcco;
	$total=0;
	
	$wcco1=explode("-",$wcco);
	$wcco=$wcco1[0];
	if($wcco=="%")
	{
		$i=0;
		$aux = (sizeof($array_ccos)*2)+3;
		$tamtab = 200+(sizeof($array_ccos)*120);
		echo "<center><table width=".$tamtab.">";
			echo "<tr class='encabezadotabla'>";
			  echo"<td align='center' colspan='".$aux."'>JUSTIFICACION VS CENTROS DE COSTOS</td>";
			echo"</tr>";
			echo "<tr class='fila1'>";
			echo "<td colspan='".$aux."'>RANGO DE FECHAS: <b>".$fechaini."</b> AL <b>".$fechafin."</b></td></tr>";
			echo "<tr class='encabezadotabla'>";
				echo "<td rowspan=2>JUSTIFICACION</td>";
				//recorro el array de centros de costos que tuvieron algun tipo de retraso para agregarlos al encabezado
				foreach($array_ccos as $keycco=>$cco)
				{
					echo "<td align='center' colspan=2 width=120 onmouseover=this.style.color='red' onmouseout=this.style.color='white'><span title='".$cco['des']."'>".$keycco."</span></td>";
				}
				echo "<td align='center' colspan=2>TOTAL</td>";
				echo "<tr class='encabezadotabla'>";
				foreach($array_ccos as $keycco=>$cco)
				{
					echo "<td align='center' width=120>CANT.</td>";
					echo "<td align='center' width=120>%</td>";
				}
				echo "<td align='center' width=120>CANT.</td>";
				echo "<td align='center'>%</td>";
				echo "</tr>";
				
			echo "</tr>";
			
			//se recorre el array de justificaciones para tener el total de retrasos desde este punto, lo cual es necesario para sacar los porcentajes.
			foreach($array_justi as $keyjusti=>$ocu)
			{
				$total+=$ocu['total'];
			}
			
			//construcción del cuerpo del reporte.
			// recorremos el array de justificaciones, y comparamos el array de centros de costos y si en algun campo coinciden entonces agregamos
			// la cantidad de ocurrencias de dicha justificacion en ese centro de costos.	
			foreach($array_justi as $keyJusti=>$justi)
			{
				$totjus = 0;
				if (is_integer($i/2))
					  $wclass="fila1";
						else
							$wclass="fila2";
				echo "<tr class='$wclass'>";
					echo "<td>".$keyJusti."</td>";
					foreach($array_ccos as $keycco=>$cco)
					{
						if(array_key_exists($keycco,$justi))
						{
							$totjus = $totjus + $justi[$keycco];
							$array_ccos[$keycco]['can']=$array_ccos[$keycco]['can']+$justi[$keycco];
						}
					}
					foreach($array_ccos as $keycco=>$cco)
					{
						if(array_key_exists($keycco,$justi))
						{
							$p= ($justi[$keycco]/$totjus)*100;
							echo "<td align='center' width=60>".$justi[$keycco]."</td>";
							echo "<td align='center' width=60>".number_format($p,2,",",".")."</td>";
						}else
							{
								echo "<td align='center' width=60> </td>";
								echo "<td align='center' width=60> </td>";
							}
					}
					echo"<td align='center'><b>".$totjus."</b></td>";
					$p2 = ($totjus/$total)*100;
					echo "<td align='center'>".number_format($p2,2,",",".")."</td>";
				echo"</tr>";
				$i++;
			}
			//aca van los totales por centro de costos.
			echo "<tr class='encabezadoTabla'>";
				echo "<td><b>TOTAL</b></td>";
				foreach($array_ccos as $keycco=>$cco)
				{
					$p= ($cco['can']/$total)*100;
					echo "<td align='center' width=60><b>".$cco['can']."</b></td>";
					echo "<td align='center' width=60>".number_format($p,2,",",".")."</td>";
				}
				echo "<td align='center'><b>".$total."</b></td>";
				echo "<td align='center'><b>100%</b></td>";
			echo "</tr>";
			echo "<tr><td></td></tr>";
			echo "<tr><td></td></tr>";
			echo "<tr><td></td></tr>";
			echo "<tr><td></td></tr>";
		echo "</table></center>";
		
		echo "<center><table>";
		echo "<tr><td><A HREF='rep_retrasoAltasDefinitivas.php?wemp_pmla=01'>Retornar</A></td></tr>";
		echo "</table></center>";
		
	}else
	  {
			$cco2 = $wcco;
		echo "<center><table width=550>";
			echo "<tr class='encabezadotabla' align='center'><td width=150><b>CENTRO DE COSTOS<b></td>";
			echo "<td width=200><b>Rango de fechas:</b></td>";
			echo "<td width=150><b>TOTAL DE RETRASOS:<b></td></tr>";	
			echo "<tr class='fila2' align=center>";
			echo "<td width=150>".$cco2."</td>";
			echo "<td width=200>".$fechaini." al ".$fechafin."</td>";
			echo "<td width=150>".$totretra."</td>";
			echo "</tr>";
		echo "</table></center>";
		$numjus = sizeof($arrjusti);
		
		echo "<center><table width=550>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align=center width=200>Tipo-Justificacion</td>";
		echo "<td align=center width=150>Cantidad de ocurrencias</td>";
		echo "<td align=center width=150>Porcentaje equivalente</td>";
		echo "</tr>";
		for($i=0; $i < $numjus; $i++)
		{
			$porc = (($arrcanti[$i])/($totretra))*100;
			$porc = number_format($porc, 2, ",", "");
			
			if (is_integer($i/2))
					  $wclass="fila1";
						else
							$wclass="fila2";
							
			echo "<tr class='".$wclass."' align='center'>";
				echo "<td width=200 align='left'>".$arrjusti[$i]."</td>";
				echo "<td width=150>".$arrcanti[$i]."</td>";
				echo "<td width=150>".$porc."%</td>";
			echo "</tr>";
		}
		echo "<br><br>";
		echo "<center><table>";
		echo "<tr><td><A HREF='rep_retrasoAltasDefinitivas.php?wemp_pmla=01'>Retornar</A></td></tr>";
		echo "</table></center>";
	  }
}

//*********************************************FIN FUNCIONES****************************************************//
$wactualiz = "2012-07-10";
encabezado("REPORTE CAUSAS DE DEMORA EN LAS  ALTAS",$wactualiz, "clinica");
session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
  {
  
  //variables globales
  $totretra=0;
  $arrjusti = array();
  $arrcanti = array();
  //para la matriz de incurrencias
  $array_ccos = array();
  $array_justi = array();
  
  

	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");
	$fhoy = date("Y-m-d");
	$acumulado = 0;
	
  echo "<form name='reporteRetrasosAD' action='rep_retrasoAltasDefinitivas.php?wemp_pmla=01' method=post>";
  
	if( !isset($fechaini) )
		$fechaini = date("Y-m-01");
	if( !isset($fechafin) )
		$fechafin = date("Y-m-t");
		
		
	if(!isset($inicio) or ($inicio==0))
	  {
			menuInicial();
      }else
	    {
			cantidadIncurrencias();
			estadísticasConsolidadas();
		}
  echo "</form>";
  echo "<br>";
    echo "<center><table>"; 
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table></center>";
 }
?>
</body>
</html>