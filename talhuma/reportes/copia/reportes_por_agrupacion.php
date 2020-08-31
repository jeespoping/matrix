<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");


include_once("../procesos/funciones_talhuma.php");
global $wemp_pmla;


$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fecha= date("Y-m-d");
$hora = date("H:i:s");

   $vectorcolor = array();
	$vectormaximo = array();
	$vectorminimo = array();
	
	
	
	$qsemaforo =  "SELECT Grusem "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucod = '".$numerodeagrupaciones[0]."' ";

	$ressemaforo = mysql_query($qsemaforo,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qsemaforo." - ".mysql_error());
	$rowsemaforo = mysql_fetch_array($ressemaforo);
	$semaforo = $rowsemaforo['Grusem'];
	
	$semaforo = '1';
	
	$anosemaforo=explode('-', $fechainicial1);

	$qcolor =    "  SELECT Msecod, Msenom ,Dsecol,Dsemax,Dsemin"
				."    FROM ".$wbasedato."_000052, ".$wbasedato."_000053 "
				."   WHERE Msecod = Dsecod " 
				."     AND Msecod = '".$semaforo."' "
				."     AND Dseano = '".$anosemaforo[0]."' "
				."ORDER BY Dseord";
	
	$rescolor = mysql_query($qcolor,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qcolor." - ".mysql_error());
	
	while($rowcolor = mysql_fetch_array($rescolor))
	{
		
		$vectormaximo[] = $rowcolor['Dsemax'];
		$vectorminimo[] = $rowcolor['Dsemin'];
		$vectorcolor[] = $rowcolor['Dsecol'];

	}
	//----------------------------------------------------------
	//----------------------------------------------------------

if($inicial2 =='no' AND $operacion=='cambiaresultadoseps')
{

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$q 	= "  SELECT DISTINCT(Encent),Epsnom "
		."     FROM ".$wbasedato."_000049  , movhos_000049"
		."    WHERE encfce  BETWEEN '".$wfechainicialeps."' AND  '".$wfechafinaleps."' "
		."      AND Encese= 'cerrado' "
		."      AND Encent LIKE Epsnom"
		." ORDER BY Encent ";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	
	
	echo "<option value='seleccione'>Todas</option>";
	while($row  = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Epsnom']."'>".$row['Encent']."</option>";
	}
	
	return;
}


if ($inicial=='no' AND $operacion=='traeresultadov3')
{
	$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
	$q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";
		
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];
	
	if($tipodeformato == '01')
	{
		$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grunom ,Grucod"
													."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013,talhuma_000051 "
													." 	   WHERE Desagr = '".$wcagrupacion."' "
													."   	 AND Evades = Descod"
													."   	 AND Mcauco = Evaevo"
													."   	 AND Ideuse = Mcauco "
													."       AND Desest !='off' "
													."		 AND Grucod = Desagr"
													."    	 AND Idecco = '".$wcentrocostos."' "
													." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "	
													."	GROUP BY Descod";
	}
	if($tipodeformato == '03')
	{
			$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051  "
											." 	   WHERE  Desagr = '".$wcagrupacion."' "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."		 AND Grucod = Desagr"
											."       AND Desest !='off' "
											."    	 AND Enccco = '".$wcentrocostos."' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'"
											."		 AND Evacal !=0  ";
										
											
			// se agrega esto a la consulta para que se busque por afinidad .						
			if($wafinidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
			}
			
			// se agrega esto a la consulta para que se busque por entidad .						
			if($wentidad!='seleccione')
			{
				$qpreguntas = $qpreguntas . " AND Encent LIKE  '%".$wentidad."%' ";
			}	

			$qpreguntas = $qpreguntas ."	GROUP BY Descod";
	
	}

	$respreguntas = mysql_query($qpreguntas,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntas." - ".mysql_error());
	echo"<table id='tabletresx-".$wcagrupacion."-".$wcentrocostos."'>";
	echo"<tr><td><div><img width='20' height='19' src='../../images/medical/root/chart.png' onclick='pintarGrafica3(\"".$wcagrupacion."\",\"".$wcentrocostos."\")' ></div></td>";
	echo"<td>";
	echo"<table align='left' id='tabletres-".$wcagrupacion."-".$wcentrocostos."'><tr class='encabezadoTabla'><td>Preguntas</td><td>Nota</td></tr>";
	
	$auxclass=0;
	while($rowpreguntas = mysql_fetch_array($respreguntas))	
	{
		$auxclass++;
		if (($auxclass%2)==0)
		{
			$wcf="fila1";  // color de fondo de la fila
		}
		else
		{
			$wcf="fila2"; // color de fondo de la fila
		}
		
		
		$valorround = round($rowpreguntas['Suma'] / $rowpreguntas['Cuantos'] , 2);
		$color = traecolor($valorround ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							
		echo "<tr><td class='".$wcf."' align='left'>".$rowpreguntas['Desdes']."</td><td align='center' bgcolor='".$color."' nowrap='nowrap' >";
		echo "<div style='display: inline-block;' onclick='traeDetallePregunta(\"".$rowpreguntas['Descod']."\",\"".$wcagrupacion."\",\"".$wcentrocostos."\",\"".$wfechainicial."\",\"".$wfechafinal."\",\"".$elemento."\",\"".$tipodeformato."\",\"".$rowpreguntas['Cuantos']."\")'  class='msg_tooltipx-".$wcagrupacion."-".$wcentrocostos."' title='Respuestas para calculo ".$rowpreguntas['Cuantos']."' >".$valorround."  <img style='float: right; display: inline-block;' id='img_mas-".$wcagrupacion."-".$wcentrocostos."-".$rowpreguntas['Descod']."' src='../../images/medical/hce/mas.PNG'></div>";
		echo "<div id='cuartaventana-".$wcagrupacion."-".$wcentrocostos."-".$rowpreguntas['Descod']."'></div>";
		echo "</td></tr>";
		
	}
	echo "</table>";
	echo "</td>";
	echo "</tr>";
	echo"<tr><td align='center' colspan=2>";
	echo"<div id='areatercera-".$wcagrupacion."-".$wcentrocostos."' ></div>";
	echo "</td></tr>";
	echo "</table>";
	return;

}
if ($inicial=='no' AND $operacion=='CargarSelectPreguntas')
{
	if($wagrupacion == 'todos')
	{
	}
	else
	{
			$q= 	 "    SELECT Descod,Desdes "
					."  	FROM ".$wbasedato."_000005 "
					."	   WHERE Desagr=".$wagrupacion." "
					."     AND Desest !='off' ";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			
			
	}
	echo "<select id='select_pregunta' style='width: 40em' >";
	echo "<option value='seleccione'>Todos</option>";
	
	while($row = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Descod']."'>".$row['Desdes']."</option>";
	}
	echo "</select>";
	return;
}

if ($inicial=='no' AND $operacion=='CargarSelectAgrupacion')
{

	$q = "SELECT Grucod,Grunom "
		."  FROM ".$wbasedato."_000051"
		." WHERE Grutem = '".$wtemareportes."' "
		."   AND Grucag = '".$wclasificacionagrupacion."'";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	
	echo "<select id='select_agrupacion' onchange='CargarSelectPreguntas()'>";
	echo "<option value='todos||todos'>Todos</option>";
	
	while($row = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Grunom']."||".$row['Grucod']."'>".$row['Grunom']."</option>";
	}
	echo "</select>";

	return;
}
	
if ($inicial=='no' AND $operacion=='traeDetallePregunta')
{
    $q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";
		
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];
	
	//Se establece la condicion-----------
	if ( strpos($wagrupacion, "|")!== false)
	{
		$numerodeagrupaciones = explode("|",$wagrupacion);
		$condicion = "(";
		$separador = '|**|';
		$o=0;
		while ($o < count($numerodeagrupaciones))
		{
			$condicion .= $separador. "Desagr = '".$numerodeagrupaciones[$o]."'";
			$separador = "||";
			$o++;
		}
		$condicion .= ")";
		
		$condicion = str_replace("|**|","",$condicion);
		$condicion = str_replace("||"," OR ",$condicion);
		
	}
	else
	{
		$condicion = " Desagr='".$wagrupacion."'";
		
	}
	//----------------------------------

	if (($tipodeformato == '01' || $tipodeformato == '04' ) && ($wcentrocostos!='todos')  )
	{
		$q= 	 "    SELECT Evaevo, Evaevr, Evacal,Ideno1,Ideno2,Ideap1,Ideap2,Evadat "
				."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
				." 	   WHERE ".$condicion." "
				."   	 AND Evades = Descod"
				."       AND Desest !='off' "
				."       AND Evades = '".$wcodpregunta."' "
				."   	 AND Mcauco = Evaevo"
				."   	 AND Ideuse = Mcauco "
				."    	 AND Idecco = '".$wcentrocostos."' "
				//."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
				."       AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "	
				."  ORDER BY Evacal " ;
		
		
	}
	if (($tipodeformato == '01' || $tipodeformato == '04' ) && ($wcentrocostos=='todos')  )
	{
		$q= 	 "    SELECT Evaevo, Evaevr, Evacal,Ideno1,Ideno2,Ideap1,Ideap2,Evadat "
				."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
				." 	   WHERE ".$condicion." "
				."   	 AND Evades = Descod"
				."       AND Evades = '".$wcodpregunta."' "
				."   	 AND Mcauco = Evaevo"
				."       AND Desest !='off' "
				."   	 AND Ideuse = Mcauco "
			//	."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
				." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "	
				."  ORDER BY Evacal " ;
		
		
	}
	else if ( ($tipodeformato == '03') && ($wcentrocostos!='todos'))
	{
		$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr  "
			."  	FROM ".$wbasedato."_000005,  ".$wbasedato."_000049, ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo "
			." 	   WHERE ".$condicion." "
			."   	 AND Evades = Descod"
			."       AND Desest !='off' "
			."   	 AND Evafco = Encenc "
			."       AND Evades = '".$wcodpregunta."' "
			."   	 AND Enchis = Evaevo "
			."       AND Evacal != 0 "
			."       AND Encese= 'cerrado'"
			."    	 AND Enccco = '".$wcentrocostos."' "
			."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
			
			
			
			// se agrega esto a la consulta para que se busque por afinidad .						
			if($wafinidad!='seleccione')
			{
				$q = $q . " AND Encafi LIKE  '%".$wafinidad."%' ";
			}
			
			// se agrega esto a la consulta para que se busque por entidad .						
			if($wentidad!='seleccione')
			{
				$q = $q . " AND Encent LIKE  '%".$wentidad."%' ";
			}						
			
			$q = $q ."  ORDER BY Evacal ,Encno1,Encno2, Encap1, Encap2 " ;
			
			

	}
	else if ( ($tipodeformato == '03') && ($wcentrocostos=='todos'))
	{
		
		$q = " 	  SELECT Encno1,Encno2, Encap1, Encap2, Evaevo, Evaevr, Evacal,Evadat,Comstr "
			."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000049  , ".$wbasedato."_000007  LEFT JOIN  ".$wbasedato."_000036  ON  Comdes = Evades AND Comucm = Evaevo "
			." 	   WHERE ".$condicion."  "
			."   	 AND Evades = Descod"
			."       AND Desest !='off' "
			."   	 AND Evafco = Encenc "
			."       AND Evades = '".$wcodpregunta."' "
			."   	 AND Enchis = Evaevo " 
			."       AND Encese= 'cerrado'"
			."       AND Evacal != 0 "
			."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
		
			// se agrega esto a la consulta para que se busque por afinidad .						
			if($wafinidad!='seleccione')
			{
				$q = $q . " AND Encafi LIKE  '%".$wafinidad."%' ";
			}
			
			// se agrega esto a la consulta para que se busque por entidad .						
			if($wentidad!='seleccione')
			{
				$q = $q . " AND Encent LIKE  '%".$wentidad."%' ";
			}						
			
			$q = $q ."  ORDER BY Evacal ,Encno1,Encno2, Encap1, Encap2" ;
			

	}
	
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//tabla respuesta detalle de preguntas ( persona que contesto y que contesto)
	
	if($tipodeformato=='03')
	{
		
		
		$tablarespuestas ;
		$tablarespuestas  = "<table align='center' id='tablex-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>";
		$tablarespuestas .= "<tr>";
		$tablarespuestas .="<td>";
	
		
		$tablarespuestas .= "<table align='center' cellspacing=1 cellpadding=0 id='table".$wcodpregunta."' style='padding:4px; background-color: white;'>" ;
		
		$tablarespuestas .= "<tr>";
		$tablarespuestas .= "<td class='encabezadoTabla'>Paciente</td><td class='encabezadoTabla'>Que contesto</td><td class='encabezadoTabla'>Valor</td><td class='encabezadoTabla' >Porcentaje</td>";
		$tablarespuestas .= "</tr>";
		
		$k=0;
		$l=0;
		$contador=1;
		$auxiliar ='';
		while($row = mysql_fetch_array($res))
		{
			if (($k%2)==0)
			{
				$wcf="fila1";  // color de fondo de la fila
			}
			else
			{
				$wcf="fila2"; // color de fondo de la fila
			}
			if ($auxiliar != $row['Evacal'])
			{
				$l=0;
				 $tablarespuestas  = str_replace ( 'cambiarrowspan', "".$contador."" , $tablarespuestas );
				 @$tablarespuestas  = str_replace ( 'resultado', "Numero de respuestas: ".$contador." <br> ".Round((($contador / $wnumeropreguntas) * 100),2)."% " , $tablarespuestas );
				 $tablarespuestas  = str_replace ( 'estilo', "".$wcf."" , $tablarespuestas );
				 $contador = 1;
			}
			else
			{	
				$l++;
				$contador++; 
			}	
				
			$auxiliar = $row['Evacal'];
			
			$tablarespuestas .= "<tr align='left'>";
			if($row['Comstr']=='')
			{
				$colorcomentario='black';
			}
			else
			{
				$colorcomentario='gray';
			}
			$tablarespuestas .= "<td class='estilo' >".$row['Encno1']." ".$row['Encno2']." ".$row['Encap1']." ".$row['Encap2']."</td><td class='estilo'>".$row['Evadat']."</td><td align='center' class='estilo'  title='".$row['Comstr']."'  style='cursor: pointer; color: ".$colorcomentario."' >".$row['Evacal']." </td>";

			if($l==0)
			{
				$tablarespuestas .="<td rowspan='cambiarrowspan'  class='estilo' align='center' >resultado</td>";
				$k++;
			}
			
			$tablarespuestas .= "</tr>";
		}
		$tablarespuestas  = str_replace ( 'cambiarrowspan', "".$contador."" , $tablarespuestas );
		@$tablarespuestas  = str_replace ( 'resultado', "Numero de respuestas: ".$contador." <br>".Round((($contador / $wnumeropreguntas) *100),2)."%" , $tablarespuestas );
		$tablarespuestas  = str_replace ( 'estilo', "".$wcf."" , $tablarespuestas );
		$tablarespuestas .= "</table>";
		$tablarespuestas .= "</td></tr></table>";
		
		echo $tablarespuestas;
	}
	else if($tipodeformato=='01' )
	{
		echo "<table align='center' id='tablex-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>";
		echo "<tr>";
		echo "<td><img width='20' height='19' src='../../images/medical/root/chart.png' onclick='pintarGrafica4(\"".$wagrupacion."\",\"".$wcentrocostos."\",\"".$wcodpregunta."\")' ></div></td>";
		echo "<td>";
		echo "<table align='center' id='table-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'>" ;
		
		echo "<tr>";
		echo "<td class='encabezadoTabla'>Empleado</td><td class='encabezadoTabla'>Puntaje</td>";
		echo "</tr>";

		while($row = mysql_fetch_array($res))
		{
			echo "<tr align='left'>";
			echo "<td class='fila1' nowrap='nowrap'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td><td class='fila1' align='center'>".$row['Evacal']."</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "</td>";
		echo "</tr>";
		echo "<tr><td colspan='2'  align='center' ><div id='areacuatro-".$wcodpregunta."-".$wagrupacion."-".$wcentrocostos."'></div></td></tr>";
		echo "</table>";
	
	}
	else if($tipodeformato=='04' )
	{
	
	    $q2 = "SELECT  Calmax "
			."  FROM  ".$wbasedato."_000034 "
			." WHERE  Calfor = '".$wtemareportes."' "
			."   AND  Calest = 'on' ";
			
		$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
		
		$row2 = mysql_fetch_array($res2);
		
		$multiplicador = $row2['Calmax'];
		
		echo "<table align='center' id='table".$wcodpregunta."'>" ;
		
		echo "<tr>";
		echo "<td class='encabezadoTabla'>Empleado</td><td class='encabezadoTabla'>Que contesto</td><td class='encabezadoTabla'>Valor</td>";
		echo "</tr>";
		

		
		while($row = mysql_fetch_array($res))
		{
			echo "<tr align='left'>";
			echo "<td class='fila1'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td><td class='fila1'>".$row['Evadat']."</td><td class='fila1' align='center'>".(($row['Evacal']) * ($multiplicador))."</td>";
			echo "</tr>";
		}
		echo "</table>";
	
	}
	
	return;
}
// llena la ventana modal con el detalle de cada pregunta contestada con su respectivo promedio
if ($inicial=='no' AND $operacion=='traeResultadosPorPregunta')
{

	$q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];
	

	
	if($wnombrecco=='')
	{
		$wnombrecco = "Todos";	
	}
	
	if($tipodeformato !='01' && $tipodeformato !='03' )
	{

		echo "<table align='center'>";
		echo "<tr >";
		echo "<td class='encabezadoTabla' align='left'>INDICADOR : </td>";
		echo "<td class='fila1' align='left'>".$wnombreagr."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadoTabla' align='left'>CENTRO DE COSTOS: </td>";
		echo "<td class='fila1' align='left'>".$wnombrecco."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='encabezadoTabla'>PERIODO</td><td class='fila1'>".$wfechainicial."   HASTA  ".$wfechafinal."</td>";
		echo "</tr>";
		
		if($wafinidad!='seleccione')
		{
			echo "<tr>";
			echo "<td class='encabezadoTabla'>Afinidad</td><td class='fila1'>".$wafinidad."</td>";
			echo "</tr>";
		}
						
		if($wentidad!='seleccione')
		{
			echo "<tr>";
			echo "<td class='encabezadoTabla'>Entidad</td><td class='fila1'>".$wentidad."</td>";
			echo "</tr>";
		}	
		
				
		echo "</table>";
	}
	echo "<br>";
	//Se establece la condicion-----------
	if ( strpos($wagrupacion, "|") !== false)
	{
		$numerodeagrupaciones = explode("|",$wagrupacion);
		$condicion = "(";
		$separador = '|**|';
		$o=0;
		while ($o < count($numerodeagrupaciones))
		{
			$condicion .= $separador. "Desagr = '".$numerodeagrupaciones[$o]."'";
			$separador = "||";
			$o++;
		}
		$condicion .= ")";
		
		$condicion = str_replace("|**|","",$condicion);
		$condicion = str_replace("||"," OR ",$condicion);
		
	}
	else
	{
		$condicion = " Desagr='".$wagrupacion."'";
		$numerodeagrupaciones[0]=$wagrupacion;
		
	}
	//----------------------------------
	
	$qsemaforo =  "SELECT Grusem "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucod = '".$numerodeagrupaciones[0]."' ";
	
	$ressemaforo = mysql_query($qsemaforo,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$qsemaforo." - ".mysql_error());
	$rowsemaforo = mysql_fetch_array($ressemaforo);
	$semaforo = $rowsemaforo['Grusem'];
	$semaforo = '1';
	

	$qcolor =    " SELECT Msecod, Msenom ,Dsecol,Dsemax,Dsemin"
				."   FROM ".$wbasedato."_000052, ".$wbasedato."_000053 "
				."  WHERE Msecod = Dsecod " 
				."    AND Msecod = '".$semaforo."' ";
				
				
	
	$rescolor = mysql_query($qcolor,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$qcolor." - ".mysql_error());
	
	while($rowcolor = mysql_fetch_array($rescolor))
	{
		$vectormaximo[] = $rowcolor['Dsemax'];
		$vectorminimo[] = $rowcolor['Dsemin'];
		$vectorcolor[] = $rowcolor['Dsecol'];
	}
	if($tipodeformato !='01' &&  $tipodeformato !='03')
	{
		echo "<br>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td colspan='2' class='encabezadoTabla'>Semaforo</td>";
		echo "</tr>";
		$i=0;
		while($i<count($vectormaximo))
		{
			echo "<tr>";
			echo "<td class='encabezadoTabla' >Nota entre  ".$vectorminimo[$i]." y ".$vectormaximo[$i]."</td>";
			echo "<td bgcolor='".$vectorcolor[$i]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			echo "</tr>";
			$i++;
		}
	echo "</table>";
	}
	
	$vectorpreguntas=array();
	$vectorvalores = array();
	$vectorvaloresagrupacion = array();
	$vectorcuantos = array();
	$vectorcuantosnoaplica = array();

	
	
	switch($tipodeformato)
       {
               case '01': 
	
				if($wcentrocostos=='todos')
				{
					
					
					// $qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Desagr"
											// ."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											// ." 	   WHERE ".$condicion." "
											// ."   	 AND Evades = Descod"
											// ."   	 AND Mcauco = Evaevo"
											// ."   	 AND Ideuse = Mcauco "
											// ."       AND Desest !='off' "
											// ."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											// ."       AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "		
											// ."	GROUP BY Descod"
											// ."  ORDER BY Desagr, Descod";
					
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Desagr"
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." 	   WHERE Desagr =  "
											."   	 AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."   	 AND Ideuse = Mcauco "
											."       AND Desest !='off' "
											// ."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "		
											."	GROUP BY Descod"
											."   ORDER BY Desagr, Descod";
											
											
					
											
					$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." 	   WHERE  ".$condicion." "
											."       AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."   	 AND Ideuse = Mcauco "
											."       AND Desest !='off' "
										//	."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
											."  	 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";		
			
				
					


					
				}
				else
				{
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grunom ,Grucod"
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013,".$wbasedato."_000051 "
											." 	   WHERE Desagr = valoracambiar "
											."   	 AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."   	 AND Ideuse = Mcauco "
											."       AND Desest !='off' "
											."		 AND Grucod = Desagr"
											."    	 AND Idecco = '".$wcentrocostos."' "
										//	."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "	
											."	GROUP BY Descod";
											
					$qpreguntastotal = 	" 		  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." 	   WHERE ".$condicion." "
											." 		 AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."   	 AND Ideuse = Mcauco "
											."       AND Desest !='off' "
											."    	 AND Idecco = '".$wcentrocostos."' "
											//."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
											."		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";	

										
				}					
										
			   break;
			   case '03' :
				if($wcentrocostos=='todos')
				{
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051 "
											." 	   WHERE Desagr = valoracambiar "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."		 AND Grucod = Desagr"
											."       AND Desest !='off' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado' ";
									
					
					$qpreguntas2 =  " 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,Grucod,Grunom "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051  "
											." 	   WHERE Desagr = valoracambiar "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."		 AND Grucod = Desagr"
											."   	 AND Enchis = Evaevo "
											."       AND Desest !='off' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
									
											
											
					$qpreguntasconnoaplica = $qpreguntas ;
					$qpreguntas =  $qpreguntas." AND Evacal != 0 ";	
					// se agrega esto a la consulta para que se busque por afinidad .						
					if($wafinidad!='seleccione')
					{
						$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
						
						$qpreguntas2 = $qpreguntas2 . "      AND Encafi LIKE  '%".$wafinidad."%' "
													. " ORDER BY Descod ";
						$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Encafi LIKE  '%".$wafinidad."%' ";
					}
					
					// se agrega esto a la consulta para que se busque por entidad .						
					if($wentidad!='seleccione')
					{
						$qpreguntas = $qpreguntas . " AND Encent LIKE  '%".$wentidad."%' ";
						$qpreguntas2 = $qpreguntas2 . " AND Encent LIKE  '%".$wentidad."%' ";
						$qpreguntasconnoaplica = $qpreguntasconnoaplica . " AND Encent LIKE  '%".$wentidad."%' ";
					}	

					if($wspregunta != 'seleccione')
					{
						$qpreguntas = $qpreguntas . " AND Descod = '".$wspregunta."' ";	
						$qpreguntas2 = $qpreguntas2 . " AND Descod = '".$wspregunta."' ";	
						$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Descod = '".$wspregunta."' ";	
					}
					
					$qpreguntas = $qpreguntas ."	GROUP BY Descod";
					$qpreguntas2 = $qpreguntas2 ."	GROUP BY Descod HAVING Suma = 0"
												." ORDER  BY Descod";
					$qpreguntasconnoaplica = $qpreguntasconnoaplica ."	GROUP BY Descod";
					
					$qpreguntas = $qpreguntas." UNION ". $qpreguntas2;
					
					
					$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
											." 	   WHERE ".$condicion." "
											."		 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."		 AND Grucod = Desagr"
											."   	 AND Enchis = Evaevo "
											."       AND Desest !='off' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
					
					$qpreguntastotalnoaplica = $qpreguntastotal ;					
					$qpreguntastotal = $qpreguntastotal ."       AND Evacal != 0 ";
					
					// se agrega esto a la consulta para que se busque por afinidad .						
					if($wafinidad!='seleccione')
					{
						$qpreguntastotal = $qpreguntastotal . " AND Encafi LIKE  '%".$wafinidad."%' ";
						$qpreguntastotalnoaplica = $qpreguntastotalnoaplica ." AND Encafi LIKE  '%".$wafinidad."%' ";
					}
					
					// se agrega esto a la consulta para que se busque por entidad .						
					if($wentidad!='seleccione')
					{
						$qpreguntastotal = $qpreguntastotal . " AND Encent LIKE  '%".$wentidad."%' ";
						$qpreguntastotalnoaplica = $qpreguntastotalnoaplica ." AND Encent LIKE  '%".$wentidad."%' ";
					}	
					
					// se agrega esto a la consulta para que se busque por pregunta .	
					if($wspregunta != 'seleccione')
					{
						$qpreguntastotal = $qpreguntastotal . " AND Descod = '".$wspregunta."' ";
						$qpreguntastotalnoaplica = $qpreguntastotalnoaplica . " AND Descod = '".$wspregunta."' ";						
					}
					
					$qtotalxagrupacion = " 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
										."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
										." 	   WHERE ".$condicion." "
										."		 AND Evades = Descod"
										."   	 AND Evafco = Encenc "
										."       AND Desest !='off' "
										."   	 AND Enchis = Evaevo "
										."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
										."       AND Encese= 'cerrado'";
										
					$qtotalxagrupacionnoaplica = $qtotalxagrupacion;					
					$qtotalxagrupacion = $qtotalxagrupacion . "    AND Evacal != 0 ";

					
					if($wspregunta != 'seleccione')
					{
						$qtotalxagrupacion = $qtotalxagrupacion . " AND Descod = '".$wspregunta."' ";	
						$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica . " AND Descod = '".$wspregunta."' ";	
					}
					
					$qtotalxagrupacion = $qtotalxagrupacion ."	GROUP BY Descod";
					$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica ."	GROUP BY Descod";
							
					$qtotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
											." 	   WHERE ".$condicion." "
											."       AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."       AND Desest !='off' "
											."       AND Evacal != 0 "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
					
					if($wspregunta != 'seleccione')
					{
						$qtotal = $qtotal . " AND Descod = '".$wspregunta."' ";	
					}
	
				}
				else
				{
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 ,".$wbasedato."_000051  "
											." 	   WHERE Desagr = valoracambiar "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."		 AND Grucod = Desagr"
											."       AND Desest !='off' "
											."    	 AND Enccco = '".$wcentrocostos."' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
					
					$qpreguntas2 =  " 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos ,Grucod,Grunom"
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049,".$wbasedato."_000051   "
											." 	   WHERE ".$condicion." "
											."   	 AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."		 AND Grucod = Desagr"
											."       AND Desest !='off' "
											."    	 AND Enccco = '".$wcentrocostos."' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
					
					$qpreguntasconnoaplica = $qpreguntas ;
					$qpreguntas =  $qpreguntas ."       AND Evacal != 0 ";
							
					// se agrega esto a la consulta para que se busque por afinidad .						
					if($wafinidad!='seleccione')
					{
						$qpreguntas = $qpreguntas . " AND Encafi LIKE  '%".$wafinidad."%' ";
						$qpreguntas2 = $qpreguntas2 . " AND Encafi LIKE  '%".$wafinidad."%' ";
						$qpreguntasconnoaplica = $qpreguntasconnoaplica .  " AND Encafi LIKE  '%".$wafinidad."%' ";
					}
					
					// se agrega esto a la consulta para que se busque por entidad .						
					if($wentidad!='seleccione')
					{
						$qpreguntas = $qpreguntas . " AND Encent LIKE  '%".$wentidad."%' ";
						$qpreguntas2 = $qpreguntas2 . " AND Encent LIKE  '%".$wentidad."%' ";
						$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Encent LIKE  '%".$wentidad."%' ";
					}

					// se agrega esto a la consulta para que se busque por pregunta .	
					if($wspregunta != 'seleccione')
					{
						$qpreguntas = $qpreguntas . " AND Descod = '".$wspregunta."' ";	
						$qpreguntas2 = $qpreguntas2 . " AND Descod = '".$wspregunta."' ";	
						$qpreguntasconnoaplica = $qpreguntasconnoaplica ." AND Descod = '".$wspregunta."' ";	
					}
					/*
					$qpreguntas = $qpreguntas 	."	GROUP BY Descod";
					
					$qpreguntas2 = $qpreguntas2 ."	GROUP BY Descod HAVING Suma = 0"
												."  ORDER BY Descod";
												
					$qpreguntas = $qpreguntas." UNION ". $qpreguntas2;
					
					
					$qpreguntasconnoaplica = $qpreguntasconnoaplica ." GROUP BY Descod";
											
					$qpreguntastotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
											." 	   WHERE ".$condicion." "
											."       AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."       AND Desest !='off' "
											."    	 AND Enccco = '".$wcentrocostos."' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
					
					$qpreguntastotalnoaplica = $qpreguntastotal ;
					$qpreguntastotal =  $qpreguntastotal ."       AND Evacal != 0 ";
					
					// se agrega esto a la consulta para que se busque por afinidad .						
					if($wafinidad!='seleccione')
					{
						$qpreguntastotal = $qpreguntastotal . " AND Encafi LIKE  '%".$wafinidad."%' ";
						$qpreguntastotalnoaplica = $qpreguntastotalnoaplica . " AND Encafi LIKE  '%".$wafinidad."%' ";
					}
					
					// se agrega esto a la consulta para que se busque por entidad .						
					if($wentidad!='seleccione')
					{
						$qpreguntastotal = $qpreguntastotal . " AND Encent LIKE  '%".$wentidad."%' ";
						$qpreguntastotalnoaplica = $qpreguntastotalnoaplica .  " AND Encent LIKE  '%".$wentidad."%' ";
					}	
					
					// se agrega esto a la consulta para que se busque por pregunta .	
					if($wspregunta != 'seleccione')
					{
						$qpreguntastotal = $qpreguntastotal . " AND Descod = '".$wspregunta."' ";
						$qpreguntastotalnoaplica = $qpreguntastotalnoaplica . " AND Descod = '".$wspregunta."' ";						
					}
					
					$qtotal = 	     " 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
											." 	   WHERE ".$condicion." "
											."       AND Evades = Descod"
											."   	 AND Evafco = Encenc "
											."   	 AND Enchis = Evaevo "
											."       AND Desest !='off' "
											."    	 AND Enccco = '".$wcentrocostos."' "
											."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											."       AND Encese= 'cerrado'";
											
					$qtotalnoaplica = $qtotal;
					$qtotal = $qtotal."       AND Evacal != 0 ";
					
					if($wspregunta != 'seleccione')
					{
						$qtotal = $qtotal . " AND Descod = '".$wspregunta."' ";
						$qtotalnoaplica = 	$qtotalnoaplica ." AND Descod = '".$wspregunta."' ";					
					}
											
					
					$qtotalxagrupacion = " 	  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
										."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049  "
										." 	   WHERE ".$condicion." "
										."		 AND Evades = Descod"
										."   	 AND Evafco = Encenc "
										."       AND Desest !='off' "
										."   	 AND Enchis = Evaevo "
										."  	 AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
										."       AND Encese= 'cerrado'"
										."    	 AND Enccco = '".$wcentrocostos."' ";
										
					$qtotalxagrupacionnoaplica = $qtotalxagrupacion;
					$qtotalxagrupacion = $qtotalxagrupacion ."       AND Evacal != 0 ";
										
										
					if($wspregunta != 'seleccione')
					{
						$qtotalxagrupacion = $qtotalxagrupacion . " AND Descod = '".$wspregunta."' ";
						$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica ." AND Descod = '".$wspregunta."' "; 						
					}
					
					$qtotalxagrupacion = $qtotalxagrupacion ."	GROUP BY Descod";
					$qtotalxagrupacionnoaplica = $qtotalxagrupacionnoaplica ."	GROUP BY Descod";
		*/
				}			
	
				break;
				
				case '04' :
				
				$q = "SELECT  Calmax "
					."  FROM  ".$wbasedato."_000034 "
					." WHERE  Calfor = '".$wtemareportes."' "
					."   AND  Calest = 'on' ";
					
				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				$row = mysql_fetch_array($res);
				
				$multiplicador = $row['Calmax'];
	
				
				if($wcentrocostos=='todos')
				{
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." 	   WHERE ".$condicion." "
											."   	 AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."   	 AND Ideuse = Mcauco "
											."       AND Desest !='off' "
											//."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "	
											."	GROUP BY Descod";
											
					$qpreguntastotal = 	     "    SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." 	   WHERE ".$condicion." "
											."       AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."       AND Desest !='off' "
											."   	 AND Ideuse = Mcauco "
											//."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
											." 		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";		
			
											
				}
				else
				{
					$qpreguntas = 	" 			  SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
											."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." 	   WHERE ".$condicion." "
											."   	 AND Evades = Descod"
											."   	 AND Mcauco = Evaevo"
											."   	 AND Ideuse = Mcauco "
											."       AND Desest !='off' "
											."    	 AND Idecco = '".$wcentrocostos."' "
											//."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' "
											." 		AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "		
											."	GROUP BY Descod";
											
					
					$qpreguntastotal = 	" 	  SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos "
										."  	FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
										." 	   WHERE ".$condicion." "
										."       AND Evades = Descod"
										."   	 AND Mcauco = Evaevo"
										."       AND Desest !='off' "
										."   	 AND Ideuse = Mcauco "
										."    	 AND Idecco = '".$wcentrocostos."' "
									//	."  	 AND ".$wbasedato."_000032.fecha_data  BETWEEN '".$wfechainicial."' AND '".$wfechafinal."' ";
										."		 AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' ";		
			
				}
			
							
	
				break;
				
				default :
                break;
		}					
	
	if($tipodeformato=='04')
	{
		$respreguntas = mysql_query($qpreguntas,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntas." - ".mysql_error());

		if($tipodeformato=='03')
		{
			$respreguntasnoaplica = mysql_query($qpreguntasconnoaplica ,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntasconnoaplica." - ".mysql_error());	
		}
		
		while($rowpreguntas = mysql_fetch_array($respreguntas))	
		{
			
			$vectorpreguntas[$rowpreguntas['Descod']] = $rowpreguntas['Desdes'];
			$vectorcodpreguntas[] = $rowpreguntas['Descod'];
			
			if($tipodeformato=='04')
			{
				
				$valor = ($rowpreguntas['Suma'] / $rowpreguntas['Cuantos']) *($multiplicador) ;
			}
			else
			{
				$valor = $rowpreguntas['Suma'] / $rowpreguntas['Cuantos'] ;
				
				
			}
			$valorround =  round($valor,2);
		
			$vectorvalores[$rowpreguntas['Descod']] = $valorround;
			$vectorcuantos[$rowpreguntas['Descod']] = $rowpreguntas['Cuantos'];
		
		}
		if($tipodeformato=='03')
		{
			while($rowpreguntasnoaplica = mysql_fetch_array($respreguntasnoaplica))	
			{
				$vectorcuantosnoaplica[$rowpreguntasnoaplica['Descod']] = $rowpreguntasnoaplica['Cuantos'];
			}
		}
		
		$vectortotalxagrupacion = array();
		// $vectortotalxagrupacionnoaplica = array();
		
		if($tipodeformato=='03')
		{
			$restotalxagrupacion = mysql_query($qtotalxagrupacion,$conex) or die ("Error 5: ".mysql_errno()." - en el query: ".$qtotalxagrupacion." - ".mysql_error()); 
			// $restotalxagrupacionnoaplica = mysql_query($qtotalxagrupacionnoaplica,$conex) or die ("Error 5: ".mysql_errno()." - en el query: ".$qtotalxagrupacionnoaplica." - ".mysql_error()); 
		}
		if($tipodeformato=='03')
		{
			while($rowtotalxagrupacion = mysql_fetch_array($restotalxagrupacion))	
			{
				$vectortotalxagrupacion[$rowtotalxagrupacion['Descod']] = $rowtotalxagrupacion['Cuantos'];	
			}
		}	
	}
	else
	{
		$auxqpreguntas = $qpreguntas ;
		$auxqpreguntasconnoaplica = $qpreguntasconnoaplica;
		echo "<table id='tablesegundax-".$windicador."'>";
		echo "<tr><td><img width='20' height='19' src='../../images/medical/root/chart.png' onclick='pintarGrafica2(\"".$windicador."\")' ></div></td>";
		echo "<td>";
		echo "<table align = 'center' id='tablesegunda-".$windicador."'>";
		echo "<tr><td  class='encabezadoTabla' >Agrupacion</td><td  class='encabezadoTabla' >Valor</td></tr>";
		$auxclass=0;
		foreach($numerodeagrupaciones as $claveagrupacion => $valoragrupacion	)
		{
			$auxclass++;
			if (($auxclass%2)==0)
			{
				$wcf="fila1";  // color de fondo de la fila
			}
			else
			{
				$wcf="fila2"; // color de fondo de la fila
			}
			
			$qpreguntas =  $auxqpreguntas;
			$qpreguntas =str_replace("valoracambiar",$valoragrupacion ,$qpreguntas) ;
			$respreguntas = mysql_query($qpreguntas,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntas." - ".mysql_error());
			$valortotalagru = 0;
			$cuantostotalagru = 0;
			while($rowpreguntas = mysql_fetch_array($respreguntas))	
			{	
				$vectorpreguntas[$rowpreguntas['Descod']] = $rowpreguntas['Desdes'];
				$vectorcodpreguntas[] = $rowpreguntas['Descod'];
				$valor = $rowpreguntas['Suma'] ;
				$valorround =  round($valor,2);
				$valor = $valorround;
				$valortotalagru = $valortotalagru + $valorround;
				$cuantostotalagru = $cuantostotalagru + $rowpreguntas['Cuantos'];
				$nombreagrupacion = $rowpreguntas['Grunom'];
				$codigoagrupacion = $rowpreguntas['Grucod'];
			}
			if($tipodeformato=='03')
			{
				$cuantostotalagrunoaplica = 0;
				$qpreguntasconnoaplica = $auxqpreguntasconnoaplica;
				$qpreguntasconnoaplica =str_replace("valoracambiar",$valoragrupacion ,$qpreguntasconnoaplica) ;
				$respreguntasnoaplica =  mysql_query($qpreguntasconnoaplica,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$qpreguntasconnoaplica." - ".mysql_error());
				while($rowpreguntasnoaplica = mysql_fetch_array($respreguntasnoaplica))	
				{
					$cuantostotalagrunoaplica = $cuantostotalagrunoaplica + $rowpreguntasnoaplica['Cuantos'];
				}
			}
			
			@$totalvalorsobrecuantos= $valortotalagru/$cuantostotalagru;
			$totalvalorsobrecuantos =   round($totalvalorsobrecuantos,2);
			$color = traecolor($totalvalorsobrecuantos ,$vectormaximo,$vectorminimo,$vectorcolor);
			if($cuantostotalagru != '0')
			{
				echo "<tr>";
				echo "<td class='".$wcf."' align='left' >".$nombreagrupacion."</td>";
				echo "<td bgcolor='".$color."' align='center' >";
				if($tipodeformato=='03')
				{
					echo "<div class='msg_tooltip1".$windicador."'  title='Respuestas para cálculo ".$cuantostotalagru."<br> Respuestas Totales ".$cuantostotalagrunoaplica." '  onclick='abrir3ventana(\"".$codigoagrupacion."\",\"".$wcentrocostos."\" ,\"".$wfechainicial."\",\"".$wfechafinal."\")'>".$totalvalorsobrecuantos." <img style='float: right; valign' id='img_mas-".$codigoagrupacion."-".$wcentrocostos."' src='../../images/medical/hce/mas.PNG'> </div>";
				}else
				{
					echo "<div class='msg_tooltip1".$windicador."'  title='Respuestas para cálculo ".$cuantostotalagru."'  onclick='abrir3ventana(\"".$codigoagrupacion."\",\"".$wcentrocostos."\" ,\"".$wfechainicial."\",\"".$wfechafinal."\")'>".$totalvalorsobrecuantos." <img id='img_mas-".$codigoagrupacion."-".$wcentrocostos."' style='float: right; valign' src='../../images/medical/hce/mas.PNG'></div>";
				}
				echo "<div id='tercerventana-".$codigoagrupacion."-".$wcentrocostos."'></div>";
				echo "</td>";
				echo "</tr>";
			}
		
		}
		echo "</table >";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='2' align='center'>";
		echo"<div id='areasegunda-".$windicador."' ></div>";
		echo "</td>";
		echo "</tr>";
		echo "</table >";
	}
	
	if( $tipodeformato=='04')
	{
	echo "<table align = 'center'>";

	echo "<tr ><td class='encabezadoTabla'>Pregunta</td><td class='encabezadoTabla'>Valor</td></tr>";
	
	$m=0;
	
	foreach ($vectorpreguntas as $clave => $valor)
	{
	  
		$color = traecolor ($vectorvalores[$clave] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
		if ($vectortotalxagrupacion[$clave] )
			$repuestas = $vectortotalxagrupacion[$clave];
		else
			$repuestas = 0;
			
		if ($vectorvalores[$clave]==0)
			$valorrepuesta ="N.A";
		else
			$valorrepuesta = $vectorvalores[$clave];
		
		
	
		if($tipodeformato=='03')
		{
			
			//echo "<tr id='trx_".$clave."'><td  class='fila1' align='left'>".$valor."</td><td  class='msg_tooltip1'  title='Respuestas para cálculo ".$repuestas."<br> Respuestas Totales ".$vectorcuantosnoaplica[$clave]." '   align='center' bgcolor='".$color."' id='td_".$clave."' style='cursor:pointer' onclick='traeDetallePregunta(\"".$clave."\",\"".$wagrupacion."\",\"".$wcentrocostos."\",\"".$wfechainicial."\",\"".$wfechafinal."\" , \"trx_".$clave."\",\"".$tipodeformato."\",\"".$vectorcuantos[$clave]."\")'>".$valorrepuesta."</td></tr>";
			echo "<tr id='trx_".$clave."'><td  class='fila1' align='left'>".$valor."</td><td  class='msg_tooltip1'  title='Respuestas para cálculo ".$repuestas."<br> Respuestas Totales ".$vectorcuantosnoaplica[$clave]." '   align='center' bgcolor='".$color."' id='td_".$clave."' style='cursor:pointer' onclick='abrir3ventana(\"".$wagrupacion."\",\"".$wcentrocostos."\")'>".$valorrepuesta."</td></tr>";
		}
		else
		{
			echo "<tr id='trx_".$clave."'><td  class='fila1' align='left'>".$valor."</td><td  class='msg_tooltip1'  title='Respuestas para Totales ".$vectorcuantos[$clave]."'   align='center' bgcolor='".$color."' id='td_".$clave."' style='cursor:pointer' onclick='traeDetallePregunta(\"".$clave."\",\"".$wagrupacion."\",\"".$wcentrocostos."\",\"".$wfechainicial."\",\"".$wfechafinal."\" , \"trx_".$clave."\",\"".$tipodeformato."\",\"".$vectorcuantos[$clave]."\")'>".$valorrepuesta."</td></tr>";		
				
		}
	  
	}
	if($tipodeformato=='03')
	{
		$restotal = mysql_query($qtotal,$conex) or die ("Error 6: ".mysql_errno()." - en el query: ".$qtotal." - ".mysql_error());
		$rowtotal = mysql_fetch_array($restotal);
	
		$respreguntastotal = mysql_query($qpreguntastotal,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qpreguntastotal." - ".mysql_error());	
		$rowpreguntastotal = mysql_fetch_array($respreguntastotal);
		
		$respreguntastotalnoaplica = mysql_query($qpreguntastotalnoaplica,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qpreguntastotalnoaplica." - ".mysql_error());	
		$rowpreguntastotalnoaplica = mysql_fetch_array($respreguntastotalnoaplica);
	}
	
	if($tipodeformato=='04')
	{
		@$totalrespuestas = ($rowpreguntastotal['Suma'] / $rowpreguntastotal['Cuantos']) *($multiplicador);
		$color = traecolor ($totalrespuestas ,$vectormaximo,$vectorminimo,$vectorcolor) ;
		
	}
	else
	{
		@$totalrespuestas = $rowpreguntastotal['Suma'] / $rowpreguntastotal['Cuantos'];
		$color = traecolor ($totalrespuestas ,$vectormaximo,$vectorminimo,$vectorcolor) ;
		
	
	}
	$totalrespuestas = round($totalrespuestas,2);
	if($tipodeformato=='03')
	{
		echo "<tr>";
		echo "<td align='left' class='encabezadoTabla'>TOTAL</td><td align='center' bgcolor='".$color."'  class='msg_tooltip1'  title='Respuestas para cálculo : ".$rowtotal['Cuantos']." <br> Respuestas Totales ".$rowpreguntastotalnoaplica['Cuantos']."' >".$totalrespuestas."</td>";
		echo "</tr>";
	}
	echo "</table>";
    }
	return;
}

if ($inicial=='no' AND $operacion=='mostrarAgrupaciones' )
{

				
	echo "<table align = 'center'>";
	
	if($tipocco=='tabcco')
	{
		$wbasedatocyp = consultarAliasPorAplicacion($conex, $wemp_pmla, $tipocco);
		
	}
	else
	{
		$wbasedatocyp ='movhos_000011';
	}
		$q = "  SELECT Cconom ,Ccocod "
			."    FROM ".$wbasedatocyp." "
			."ORDER BY Cconom ";
	
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//-----------------------------
	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Centro de costos</td>";
	echo "<td align='left' colspan='2'>";
	echo "<select id='select_wcco' >";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "<option value='todos||todos'>Todos</option>";
	
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Ccocod']."||".$row['Cconom']."'>".$row['Cconom']."</option>";
	}
	echo "<select>";
	echo "</td>";

	echo "</tr>";
	
	
	$q = "SELECT DISTINCT(Cagcod),Cagdes "
		."  FROM ".$wbasedato."_000057"
		." WHERE Cagest = 'on' ";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
	echo "<tr align='center' class='fila1'>";
	echo "<td align='Left'>Clasificacion Agrupaciones</td>";
	echo "<td align='left' colspan='2'>";
	echo "<select id='select_clasificacionagrupacion' onchange='CargarSelectAgrupacion()'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "<option value='todos'>Todos</option>";
	
	while($row =mysql_fetch_array($res))
	{
		echo "<option value='".$row['Cagcod']."'>".$row['Cagcod']."-".$row['Cagdes']."</option>";
	}
	echo "<select>";
	echo "</td>";

	echo "</tr>";

	echo "<tr align='left' >";
	echo "<td class='fila1'>Agrupaci&oacute;n</td>";
	echo "<td class='fila1' align='left'  colspan='2'><div id='div_select_agrupacion'><select id='select_agrupacion' style='width: 40em'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "</select></div></td>";
	echo "</tr>";

	echo "<tr align='left' >";
	echo "<td class='fila1'>Pregunta</td>";
	echo "<td class='fila1' align='left'  colspan='2'><div id='div_select_pregunta'><select id='select_pregunta' style='width: 40em'>";
	echo "<option value='seleccione'>Seleccione</option>";
	echo "</select></div></td>";
	echo "</tr>";

	$fechaactual= date('Y-m-d');
	$wfecha_i = $fechaactual;
	$wfecha_f = $fechaactual;
	
	$porperiodo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'buscarporperiodo');
	
	
	if($porperiodo == $wbasedato)
	{
		$q=  "SELECT Perano, Perper "
		."  FROM ".$wbasedato."_000009 "
		." WHERE Perfor = '".$wtemareportes."' ";
	
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
		echo "<tr align='left'>";
		echo "<td class='fila1'>Periodo</td>";
		echo "<td class='fila1'  colspan='2'<div id='selectperiodo' ><Select id='selectperiodos'>";
		echo "<option value='nada||nada'> Seleccione </option>";
		while($row = mysql_fetch_array($res))
		{
			echo "<option value='".$row['Perano']."||".$row['Perper']."'>".$row['Perano']."-".$row['Perper']."</option>";
		}
		echo "</select></div></td>";
		echo "</tr>";
	
	}else
	{
		echo "<tr align='left'>";
		echo "<td align='left' class='fila1' >Periodo 1<input type='checkbox' id='periodo1' value='si' checked></td><td class='fila1' align='center'>Fecha Inicial: ";
		campofechaDefecto("wfecha_i",$wfecha_i);
		echo "</td>";
		echo "<td align='center' class='fila1'>Fecha final: ";
		campofechaDefecto("wfecha_f",$wfecha_f);
		echo "</td>";
		echo "</tr>";
		echo "<tr align='left'>";
		echo "<td  class='fila1' align='left'>Periodo 2<input type='checkbox' id='periodo2' value='si' ></td><td class='fila1' align='center'>Fecha Inicial: ";
		campofechaDefecto("wfecha_i1",$wfecha_i);
		echo "</td>";
		echo "<td class='fila1' align='center'>Fecha final: ";
		campofechaDefecto("wfecha_f1",$wfecha_f);
		echo "</td>";
		echo "</tr>";
	}	
	if($wbasedato=='encumage')
	{
	
	
		echo"<tr align='left'>";
		echo"<td class='fila1' colspan='1'>Tipo de Servicio</td>";
		echo"<td class='fila1' colspan='2'>Todos&nbsp;<input type='radio' name='servicio' id='servicio_todos' checked='checked'  value='si'>Hospitalario&nbsp;<input type='radio' name='servicio' id='servicio_hospitalario' value='si'>&nbsp;Terapeuticos&nbsp;<input type='radio' name='servicio' id='servicio_terapeuticos' value='si'>&nbsp;Urgencias&nbsp;<input type='radio' name='servicio' id='servicio_urgencias' value='si' >&nbsp;Cirugia&nbsp;<input type='radio' name='servicio' id='servicio_cirugia' value='si'>&nbsp;Ayudas diagnosticas&nbsp;<input type='radio' name='servicio'  id='servicio_ayudas' value='si'></td>";
		echo"</tr>";
	
		echo "<tr align='Left' >";
		echo "<td class='fila1'>Entidad</td>";

		$q =   "  SELECT Epsnom "
			  ."    FROM movhos_000049 "
			  ."ORDER BY Epsnom";
				
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		echo "<td colspan='1' class='fila1'>";

		echo "<div id='epsselect'><select id='select_entidad' onclick ='cambiarfechaseps()' >";
		echo "<option value='seleccione'>Todas</option>";
		while($row =mysql_fetch_array($res))
		{
			echo "<option value='".$row['Epsnom']."'>".$row['Epsnom']."</option>";
		}
		echo "</select>";
		echo "</div></td>";
		echo "<td class='fila1'></td>";
		echo "</tr>";
	
	
		echo "<tr align='Left' >";
		echo "<td class='fila1'>Afinidad</td>";
		echo "<td class='fila1' align='left'  colspan='2'><select id='select_afinidad'>";
		echo "<option value='seleccione'>Todos</option>";
		echo "<option value='AFI'>Afinidad</option>";
		echo "<option value='VIP'>VIP</option>";
		echo "</select></td>";
		echo "</tr>";
	}
	echo"<tr align='left'>";
	echo"<td class='fila1' colspan='3' align='center'>";
	echo"<input type='button' value='Consultar' onclick='verReporte()' >";
	echo"</td>";
	echo"</tr>";

	echo "</table>";

	return;

}

if ($inicial=='no' AND $operacion=='traeResultadosReporte')
{
	if($wclasificacionagrupacion != '')
	{
		//vector donde se van almacenar las agrupaciones 
		$vectoragrupaciones = array();
		// vector donde se guarda los nombres de las agrupaciones
		$vectornomagrupaciones = array();
		 
		$q1 ="  SELECT Cagcod,Cagdes "
			."     FROM ".$wbasedato."_000057 ";
			
		if($wclasificacionagrupacion != 'todos')
			 $q1= $q1." WHERE Cagcod = '".$wclasificacionagrupacion."' ";
		
		$q1= $q1." ORDER BY Cagcod" ;
		
		$res1 = mysql_query($q1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());	
	
		$s=0;
		while($row1 = mysql_fetch_array($res1))
		{
			$q2 =" SELECT Grucod,Grunom,Grucag "
				."   FROM ".$wbasedato."_000051"
				."  WHERE Grutem = '".$wtemareportes."' "
				."    AND Grucag = '".$row1['Cagcod']."' ";
			
			if($wcagrupacion !='todos')
				$q2= $q2." AND Grucod='".$wcagrupacion."'" ;
			
			$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
			$separador = "*|"; // se utiliza para separar 
			while($row2 = mysql_fetch_array($res2))
			{
				//$auxvector=$row2['Grucod'];
				$vectoragrupaciones[$row2['Grucag']][] = $row2['Grucod'];
				if($wcagrupacion !='todos')
					$vectornomagrupaciones[$row2['Grucag']]=$row2['Grunom'];
				else
					$vectornomagrupaciones[$row2['Grucag']]=$row1['Cagdes'];
				
				$separador = "|";
			}
			//$vectoragrupaciones[$s] = str_replace("*|","",$vectoragrupaciones[$s]);
		    $s++;
		}
		
		$s=0;
		// se crea el vector de condiciones
		$r=0;
		foreach($vectoragrupaciones as $clasificacion => $arry_agrupacion )
		{
			$vectorcondiciones[$clasificacion] = (count($arry_agrupacion) > 1) ? "(Desagr = '".implode("' OR Desagr = '", $arry_agrupacion)."')" : "Desagr = '".implode(" OR ", $arry_agrupacion)."'";
			
			if($r==0)
			{	
				
				if(count($arry_agrupacion) > 1)
				{
					
					$numerodeagrupaciones[0] = implode($arry_agrupacion);
				
				}
				else
				{	
					
					$numerodeagrupaciones = implode($arry_agrupacion);
				}
				$r=1;
			}
			
		}
		
	}

	
	// colores de tds
	//---------------------------------------------------------
	//- se crean los vectores que contienen los rangos maximo minimo y el color de este rango
	
	// Tabla de convenciones del semaforo

	echo "<table align='left'>";
	echo "<tr>";
	echo "<td colspan='2' class='encabezadoTabla'>Semaforo</td>";
	
	
	
	echo "</tr>";
	$i=0;
	while($i<count($vectormaximo))
	{
		echo "<tr>";
		echo "<td class='encabezadoTabla' >Nota entre  ".$vectorminimo[$i]." y ".$vectormaximo[$i]."</td>";
		echo "<td bgcolor='".$vectorcolor[$i]."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "</tr>";
		$i++;
	}

	echo "</table>";
	//--------------------------------------------------------------
	
	echo "<br>";
	echo "<br>";
	echo "<br>";
	echo "<br>";
	
	//---------------------------------------------------------------
	// Se consulta el tipo de formato par luego utilizar el switch
	$q = " SELECT  	Fortip "
		."   FROM  ".$wbasedato."_000042 "
		."  WHERE Forcod = ".$wtemareportes." ";
		
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);
	$tipodeformato = $row['Fortip'];
	//-------------------------------------------------------
	
	switch($tipodeformato)
     {
               case '01': 
                if ($wcco!='')
				{	
					
					//echo "sies1";
					//venctor de cco que se llena con los nombres de los cco que tienen respuestas 
					$vectorccocontestado = array();
					//venctor de cco que se llena con los codigos de los cco que tienen respuestas
					$vectorcodccocontestado  = array();
		
					if ($wcco=='todos')
					{

						$querycco =	 "SELECT DISTINCT(Cconom),Ccocod"
									."  FROM ".$wbasedato."_000032 ,costosyp_000005, talhuma_000013,  ".$wbasedato."_000002"
									." WHERE Ideuse = Mcauco "
									."   AND Idecco = Ccocod"
									."   AND Forcod = Mcafor"
									."   AND ".$wbasedato."_000002.Fortip = '".$wtemareportes."' ";
	
						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());
						$i=0;
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Cconom'];
							$vectorcodccocontestado[$i] = $rowquerycco['Ccocod'];
							$i++;
						}
						
					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;
						
						
					}
		
					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					$vectorresporcco = array();
					$vectorresporccocuantos = array();
					$n=0;
					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{
			
							$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos,".$wbasedato."_000051.Grucag,Idecco  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Mcauco = Evaevo"
												."   AND Desest !='off' "
												."   AND Ideuse = Mcauco "
												."   AND Idecco IN ('".implode("','",$vectorcodccocontestado)."') "
												//."   AND ".$wbasedato."_000032.fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' ";
												." AND Mcaano = '".$wano1."' AND Mcaper = '".$wperiodo1."' ";					
							
							
							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000013.Idecco";
							$arr_querys_aplican[] 	= $qccoxagrupacion;
											
							// $resccoxagrupacion = mysql_query($qccoxagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
							// $rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);	
							// $cuantos=$rowccoxagrupacion['Cuantos'];
							
							
							// @$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos;
							// $roundresvalorcco = round($resvalorcco ,2);
							// @$vectorresporcco[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $roundresvalorcco;
							// @$vectorresporccocuantos[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $cuantos;
						
					}
					$union=" UNION  ";
					$querys_aplican = implode($union, $arr_querys_aplican);
					
					$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
					
					while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
					{
						$cuantos = $rowccoxagrupacion['Cuantos'];
						
						
						@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;
						$roundresvalorcco = round($resvalorcco ,2);
						$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Idecco']]= $roundresvalorcco;
						$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Idecco']]= $cuantos;
					}
					
					if( $unperiodo!='1')
					{
									
						$vectorresporcco1 = array();
						$vectorresporccocuantos1=array();
						
						$n=0;
						while($n < count($vectoragrupaciones))
						{
						
							$j=0;
							while($j < count($vectorccocontestado))
							{
								
								
								$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
													."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
													." WHERE Desagr='".$vectoragrupaciones[$n]."' "
													."   AND Evades = Descod"
													."   AND Mcauco = Evaevo"
													."   AND Desest !='off' "
													."   AND Ideuse = Mcauco "
													."   AND Idecco = '".$vectorcodccocontestado[$j]."' "
													//."   AND  ".$wbasedato."_000032.fecha_data  BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' ";
													." 	 AND Mcaano = '".$wano2."' AND Mcaper = '".$wperiodo2."' ";		
													
								$resccoxagrupacion = mysql_query($qccoxagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
								$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);	
								
								$cuantos1=$rowccoxagrupacion['Cuantos'];
								
								@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1;
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $roundresvalorcco1;
								$vectorresporccocuantos1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]=$cuantos1;
								$j++;
							}
							$n++;
						}
					}
				}
				$m=0;
		
				// si se va a ver por todos las agrupaciones sin ningun detalle

		
				echo"<br><br>";
				
				echo"<table style='border:#2A5DB0 1px solid'  id='tablareporte'  align='center'>";
		
				echo"<tr><td class='encabezadoTabla' rowspan='2' >Unidad</td>";
				
				echo"<td align='center' class='encabezadoTabla' colspan='".count($vectorcondiciones)." '>Nombre del indicador</td>";
				//funciona si es porcco
				//------------------------------------
		
				if ($wcco!='' )
				{	
					$j=0;
					echo"<tr>";
					foreach($vectorcondiciones as $clasificacion => $codigo)
					{
						echo"<td class='encabezadoTabla' align='center'>".$vectornomagrupaciones[$clasificacion]."</td>";
						$j++;
					}
				}
				//echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
				echo"</tr>";
				//-------------------
				if($wcco!='')
				{
					
					$auxind=0;
					foreach($vectorccocontestado as $clavecco => $codigocco)
					{
						
						
					
						$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
						$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$clavecco]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								
								
				
						if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]] =='' )
							$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]= 0;
							
						if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]] == '')
							$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]]= 0;
						
						if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$clavecco]]=='')
						  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$clavecco]] = 0;

						  
						$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
						if (($auxind%2)==0)
						{
							$wcf="fila1";  // color de fondo de la fila
						}
						else
						{
							$wcf="fila2"; // color de fondo de la fila
						}
						echo"<tr >";
						echo"<td class='".$wcf."' colspan='1' align='Left'  ><div id='sss'>".$codigocco."</div></td><td align='center'  class='msg_tooltip'    style='cursor: pointer;' bgcolor='".$color."' ><div title='Respuestas para cálculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$clavecco]]."' onclick='abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$clavecco]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$clavecco]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]."\",\"".$auxind."\",\"".$tipodeformato."\")'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$clavecco]]." <img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'></div><div id='ddiv-".$auxind."'></div></td>";
						echo"</tr>";
						$auxind++;
					}
				}
					
			echo"</table>";	  
			
               break;
			   
			   case '03':
				if ($wcco!='')
				{	
					//venctor de cco que se llena con los nombres de los cco que tienen respuestas 
					$vectorccocontestado = array();
					//venctor de cco que se llena con los codigos de los cco que tienen respuestas
					$vectorcodccocontestado  = array();
		
					if ($wcco=='todos')
					{
						
						// Consulta que se arroja los centros de costo en que se hara la busqueda
						// hay ciertos parametros que se van agregando  si son seleccionados 
						$querycco =	 "SELECT distinct(Cconom) AS Nombre, Enccco"
									."  FROM ".$wbasedato."_000049 , movhos_000011 "
									." WHERE Enccco = Ccocod "
									."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'" 
									."   AND Encese= 'cerrado'";
							
						// se agrega esto a la consulta para que se busque por afinidad .						
						if($wafinidad!='seleccione')
						{
							$querycco = $querycco . " AND Encafi LIKE  '%".$wafinidad."%' ";
						}
						
						// se agrega esto a la consulta para que se busque por entidad .						
						if($wentidad!='seleccione')
						{
							$querycco = $querycco . " AND Encent LIKE  '%".$wentidad."%' ";
						}
						
						// se agrega esto al query para que busque por centro de costos hospitalarios
						if($whospitalario=='si')
						{
							$querycco = $querycco . " AND Ccohos = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos urgencias
						if($wurgencias=='si')
						{
							$querycco = $querycco . " AND Ccourg = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos cirugia
						if($wcirugia=='si')
						{
							$querycco = $querycco . " AND Ccocir = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos terapeutico
						if($wterapeutico=='si')
						{
							$querycco = $querycco . " AND Ccoter = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
						if($wayudas=='si')
						{
							$querycco = $querycco . " AND Ccoayu = 'on' ";
						}
						//------------------------------------------------------------
						
						// si se se maneja mas de un periodo se debe hacer un Union que arroja los centros de costos
						// donde hay respuestas tando del periodo 1 como del period 2
						if($unperiodo!='1')
						{
							// se concatena a la consulta un union que saca los centros de costos que tienen respuesta en un periodo
							// con los del otro periodo
							$querycco =	$querycco."    UNION DISTINCT "
													."SELECT distinct(Cconom) AS Nombre, Enccco"
													."  FROM ".$wbasedato."_000049 , movhos_000011 "
													." WHERE Enccco = Ccocod "
													."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."'" 
													."   AND Encese= 'cerrado'";
						
							
							// se agrega esto a la consulta para que se busque por afinidad .						
							if($wafinidad!='seleccione')
							{
								$querycco = $querycco . "AND Encafi LIKE  '%".$wafinidad."%' ";
							}
							
							// se agrega esto a la consulta para que se busque por entidad .						
							if($wentidad!='seleccione')
							{
								$querycco = $querycco . "AND Encent LIKE  '%".$wentidad."%' ";
							}
							
							// se agrega esto al query para que busque por centro de costos hospitalarios
							if($whospitalario=='si')
							{
								$querycco = $querycco . " AND Ccohos = 'on' ";
							}
							
							// se agrega esto al query para que busque por centro de costos urgencias
							if($wurgencias=='si')
							{
								$querycco = $querycco . " AND Ccourg = 'on' ";
							}
							
							// se agrega esto al query para que busque por centro de costos cirugia
							if($wcirugia=='si')
							{
								$querycco = $querycco . " AND Ccocir = 'on' ";
							}
							
							// se agrega esto al query para que busque por centro de costos terapeutico
							if($wterapeutico=='si')
							{
								$querycco = $querycco . " AND Ccoter = 'on' ";
							}
							// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
							if($wayudas=='si')
							{
								$querycco = $querycco . " AND Ccoayu = 'on' ";
							}
								
						}
						
						// se imprime la consulta para ver si esta buena.
				
						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());
			
						$i=0;
						// por medio de este while se llenan los vectores  vectorccocontestado con el nombre y vectorcodccocontestado con el codigo
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Nombre'];
							$vectorcodccocontestado[$i] = $rowquerycco['Enccco'];
							$i++;
						}
						
					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;
					}
					
					// vector donde se almacena el promedio de las notas por centro de costos
					$vectorresporcco = array();
					// vector donde se almacena la cantidad de preguntas contestadas por agrupacion en centro de costos
					$vectorresporccocuantos = array();
					$vectorresporccocuantosnoaplica= array();
					$n=0;
			
					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{
						
								$qccoxagrupacion = 	"SELECT encumage_000049.Enccco, encumage_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
												."   AND Encese= 'cerrado'"
												."   AND Desest !='off' ";
												
												
							$qccoxagrupacionconnoaplica = $qccoxagrupacion;
							
							$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";					
							// se agrega esto a la consulta para que se busque por afinidad .						
							if($wafinidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Encafi LIKE  '%".$wafinidad."%' ";
								$qccoxagrupacionconnoaplica =$qccoxagrupacionconnoaplica . " AND Encafi LIKE  '%".$wafinidad."%' ";
							}
							
							// se agrega esto a la consulta para que se busque por entidad .						
							if($wentidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Encent LIKE  '%".$wentidad."%' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Encent LIKE  '%".$wentidad."%' ";
							}
							
							if($wspregunta!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
							}
							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
							$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";
			
							$arr_querys_aplican[] 	= $qccoxagrupacion;
							$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;
		
					}
					$union=" UNION  ";
					$querys_aplican = implode($union, $arr_querys_aplican);
					$querys_NOaplican = implode($union, $arr_querys_NOaplican);
				
					//echo '</pre>'.$querys_aplican.'</pre><br><br>';
					$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
					//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
					$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());	
					
					while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
					{
						$cuantos = $rowccoxagrupacion['Cuantos'];
						
						
						@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;
						$roundresvalorcco = round($resvalorcco ,2);
						$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco;
						$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos;
					}
					
					while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
					{
						$cuantosnoaplica = $rowccoxagrupacionconnoaplica['Cuantos'];
						$vectorresporccocuantosnoaplica[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica;
					}
					

					// si tiene mas de  uno , se debe hacer para el otro periodo
					if( $unperiodo!='1')
					{
						$arr_querys_aplican = array();
						$arr_querys_NOaplican = array();
						$vectorresporcco1 = array();
						$n=0;
						foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
						{
			
								$qccoxagrupacion = 	" SELECT encumage_000049.Enccco, encumage_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
												."   AND Encese= 'cerrado'"
												."   AND Desest !='off' ";
												
								
								$qccoxagrupacionconnoaplica = $qccoxagrupacion;
							
								$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";		
								
								// se agrega esto a la consulta para que se busque por afinidad .						
								if($wafinidad!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
								}
								
								// se agrega esto a la consulta para que se busque por entidad .						
								if($wentidad!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . "AND Encent LIKE  '%".$wentidad."%' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encent LIKE  '%".$wentidad."%' ";
								}
								
								if($wspregunta!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
								}
								
								$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
								$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";
															
								$arr_querys_aplican[] 	= $qccoxagrupacion;
								$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;
								
						}
							
							$union=" UNION  ";
							$querys_aplican = implode($union, $arr_querys_aplican);
							$querys_NOaplican = implode($union, $arr_querys_NOaplican);
							
							$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_aplican." - ".mysql_error());	
							//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
							$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());	
							
							
							while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
							{
								$cuantos1 = $rowccoxagrupacion['Cuantos'];
								
								
								@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1 ;
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco1;
								$vectorresporccocuantos1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos1;
							}
					
							while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
							{
								$cuantosnoaplica1 = $rowccoxagrupacionconnoaplica['Cuantos'];
								$vectorresporccocuantosnoaplica1[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica1;
							}
					}
					
				}
				echo"<br><br>";
				echo"<table style='border:#2A5DB0 1px solid' id='tablareporte'>";
				if( $unperiodo=='1')
				{
					echo"<tr><td class='encabezadoTabla' rowspan='2' >Nombre del Indicador</td>";
					echo"<td align='center' class='encabezadoTabla' colspan='".(count($vectorccocontestado) + 1)." '>Notas Promedio</td>";
				}
				else
				{
				  echo"<tr><td class='encabezadoTabla' rowspan='3' >Nombre del Indicador</td>";
				  echo"<td align='center' class='encabezadoTabla' colspan='".((count($vectorccocontestado) + 2) * 2)." '>Notas Promedio</td>"; 
				}
				
				//funciona si es porcco
				//------------------------------------
		
				if ($wcco!='' )
				{	
					$j=0;
					echo"<tr class='pisos'>";

						if( $unperiodo=='1')
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
							echo"</tr>";
						}
						else
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' colspan='2' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' colspan='2'>TOTAL CLINICA</td>";
							echo"</tr>";
							echo"<tr>";
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla Periodo'  align='center'>Periodo1</td><td class='encabezadoTabla Periodo' align='center'>Periodo2</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla Periodo' align='center'>Periodo1</td><td class='encabezadoTabla Periodo' align='center'>Periodo2</td>";
							echo"</tr>";
							
						}
		
				}
	
				if($wcco!='')
				{
					
					$auxind = 0;
					foreach ( $vectorcondiciones  as $clasificacion => $codigo )
					{
			
						//--------nombre de agrupacion
						echo"<tr class='ocultar' >";
						echo"<td width='200' class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$clasificacion]."</div></td>";
						//---------------------------
						
						if($wcco!='')
						{
					//03
							$j=0;
							
							while($j < count($vectorccocontestado))
							{	
								// trae los datos de cada uno de los centro de costos por agrupacion
								
							
								$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								
								
								if( $unperiodo=='1')
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;
									
									
									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td align='center'   style='cursor: pointer;' bgcolor='".$color."'><div  class='msg_tooltip'  title='Respuestas para cálculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br>  Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."'  onclick='abrirporpreguntas(this, \"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")'><img style='float: right; display: inline-block;' id='img_mas-".$auxind."' src='../../images/medical/hce/mas.PNG'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."</div><div id='ddiv-".$auxind."'></div></td>";
								
									$auxind++;
								}
								else
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;
									
									
									if($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]] = 0;
									
									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td bgcolor='".$color."'   align='center' style='cursor: pointer;' ><div class='msg_tooltip'  title='Respuestas para cálculo ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")' >".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."</div><div id='ddiv-".$auxind."'></div></td >";
										$auxind++;
									echo"<td  bgcolor='".$color1."' style='cursor: pointer;' align='center' ><div class='msg_tooltip'  title='Respuestas para cálculo ".$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuetas Totales ".$vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]."'    onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."\", \"".$auxind."\")' >".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."</div><div id='ddiv-".$auxind."'></div></td>";
										$auxind++;
									
								}
								
								$j++;
							
					
							}
							
						
							
						}
						//-------------total de la agrupacion
						
						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
											." WHERE ".$vectorcondiciones[$clasificacion]." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
											."   AND Encese= 'cerrado'"
											."   AND Desest !='off' ";
						
						$qtagrupacionnoaplica = $qtagrupacion;
						$qtagrupacion = $qtagrupacion ."   AND Evacal !=0  ";
						
		
						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}
						
						// se agrega esto a la columna para que se busque por entidad .						
						if($wentidad!='seleccione')
						{
							$qtagrupacion =$qtagrupacion . "AND Encent LIKE  '%".$wentidad."%' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encent LIKE  '%".$wentidad."%' ";
						}
						
						if($wspregunta!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . " AND Descod  = '".$wspregunta."' ";
						}
							
						 
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());	
						$rowtagrupacion = mysql_fetch_array($restagrupacion);
						
						$restagrupacionnoaplica = mysql_query($qtagrupacionnoaplica,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica." - ".mysql_error());	
						$rowtagrupacionnoaplica = mysql_fetch_array($restagrupacionnoaplica);
						
						$qtagrupacion1 = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
											." WHERE ".$vectorcondiciones[$clasificacion]." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
											."   AND Encese= 'cerrado'"
											."   AND Desest !='off' ";
										
						$qtagrupacionnoaplica1 = $qtagrupacion1;
						$qtagrupacion1= $qtagrupacion1 ."   AND Evacal !=0  ";	
						
						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}	

						if($wentidad !='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encent LIKE  '%".$wentidad."%' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1. "AND Encent LIKE  '%".$wentidad."%' ";
						}	
						
						if($wspregunta!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . " AND Descod  = '".$wspregunta."' ";
						}
							
						 
						$restagrupacion1 = mysql_query($qtagrupacion1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion1." - ".mysql_error());	
						$rowtagrupacion1 = mysql_fetch_array($restagrupacion1);
						
						$restagrupacion1noaplica = mysql_query($qtagrupacionnoaplica1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica1." - ".mysql_error());	
						$rowtagrupacion1noaplica = mysql_fetch_array($restagrupacion1noaplica);
						
						
							$cuantost= $rowtagrupacion['Cuantos'];
							$cuantostnoaplica = $rowtagrupacionnoaplica['Cuantos'];
							@$valor = $rowtagrupacion['Suma']  / $cuantost;
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;
							
							$cuantost1= $rowtagrupacion1['Cuantos'];
							$cuantostnoaplica1 = $rowtagrupacion1noaplica['Cuantos'];
							@$valor1 = $rowtagrupacion1['Suma']  / $cuantost1;
							$total1 = $valor1 + $total1;
							$float_redondeado1=round($valor1 * 100) / 100;
							$color = traecolor ($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$color1 = traecolor ($float_redondeado1 ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$auxind = $auxind + 1;
							if( $unperiodo=='1')
							{
																																	
								echo"<td align='center'  align='center' class='msg_tooltip'  title='Respuestas para cálculo ".$cuantost." <br> Respuestas totales ".$cuantostnoaplica."' bgcolor =  '".$color."'  style='cursor: pointer;'  onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";
							}
							else
							{
								$auxind = $auxind + 1;
								echo"<td align='center'  class='msg_tooltip'  title='Respuestas para cálculo ".$cuantost."  <br> Respuestas totales ".$cuantostnoaplica." '   style='cursor: pointer;'   bgcolor =  '".$color."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td>";
								$auxind = $auxind + 1;
								echo"<td align='center'  class='msg_tooltip'  title='Respuestas para cálculo ".$cuantost1." <br> Respuestas Totales ".$cuantostnoaplica1."' ' style='cursor: pointer; '  bgcolor =  '".$color1."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado1."\")'>".$float_redondeado1."</td>";
								$auxind = $auxind + 1;
							}
							$auxind = $auxind + 1;
							$i++;

						//---------------------------------------
						
						echo"</tr>";
						$m++;
					}
				}
				else if($poragrupacion=='si' )
				{
					while($m < count($vectoragrupaciones))
					{
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$m]."</div></td>";
						//---------------------------
						
						if($porcco=='si')
						{
					
							$j=0;
							while($j < count($vectorccocontestado))
							{
								
								
								
								if( $unperiodo=='1')
								{
									echo"<td align='center'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{
								
									echo"<td class='encabezadoTabla'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td ><td align='center' class='encabezadoTabla' class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."' >".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
									
									
								}
								
								$j++;
							}
							
						}
						//-------------total de la agrupacion
						
						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007  "
											." WHERE ".$vectorcondiciones[$n]." "
											."   AND Evades = Descod"
											."   AND Desest !='off' ";
							

						 
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());	
						$rowtagrupacion = mysql_fetch_array($restagrupacion);
						
						
							$valor = $rowtagrupacion['Suma']  / $rowtagrupacion['Cuantos'];
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;
							
							$color = traecolor($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							
								if( $unperiodo=='1')
								{
										echo"<td align='center' style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";
								}
								else
								{
									
									echo"<td align='center'   align='center'  style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."</td><td  style='cursor: pointer;' align='center' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."<div id='ddiv-".$auxind."'></div></td>";
		
								}

							$i++;

						echo"</tr>";
						$m++;
					}
				}
				echo"</table>";	 
				
				
				
				
				
				/*	  
				if ($wcco!='')
				{	
					//venctor de cco que se llena con los nombres de los cco que tienen respuestas 
					$vectorccocontestado = array();
					//venctor de cco que se llena con los codigos de los cco que tienen respuestas
					$vectorcodccocontestado  = array();
		
					if ($wcco=='todos')
					{
						
						// Consulta que se arroja los centros de costo en que se hara la busqueda
						// hay ciertos parametros que se van agregando  si son seleccionados 
						$querycco =	 "SELECT distinct(Cconom) AS Nombre, Enccco"
									."  FROM ".$wbasedato."_000049 , movhos_000011 "
									." WHERE Enccco = Ccocod "
									."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'" 
									."   AND Encese= 'cerrado'";
							
						// se agrega esto a la consulta para que se busque por afinidad .						
						if($wafinidad!='seleccione')
						{
							$querycco = $querycco . " AND Encafi LIKE  '%".$wafinidad."%' ";
						}
						
						// se agrega esto a la consulta para que se busque por entidad .						
						if($wentidad!='seleccione')
						{
							$querycco = $querycco . " AND Encent LIKE  '%".$wentidad."%' ";
						}
						
						// se agrega esto al query para que busque por centro de costos hospitalarios
						if($whospitalario=='si')
						{
							$querycco = $querycco . " AND Ccohos = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos urgencias
						if($wurgencias=='si')
						{
							$querycco = $querycco . " AND Ccourg = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos cirugia
						if($wcirugia=='si')
						{
							$querycco = $querycco . " AND Ccocir = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos terapeutico
						if($wterapeutico=='si')
						{
							$querycco = $querycco . " AND Ccoter = 'on' ";
						}
						
						// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
						if($wayudas=='si')
						{
							$querycco = $querycco . " AND Ccoayu = 'on' ";
						}
						//------------------------------------------------------------
						
						// si se se maneja mas de un periodo se debe hacer un Union que arroja los centros de costos
						// donde hay respuestas tando del periodo 1 como del period 2
						if($unperiodo!='1')
						{
							// se concatena a la consulta un union que saca los centros de costos que tienen respuesta en un periodo
							// con los del otro periodo
							$querycco =	$querycco."    UNION DISTINCT "
													."SELECT distinct(Cconom) AS Nombre, Enccco"
													."  FROM ".$wbasedato."_000049 , movhos_000011 "
													." WHERE Enccco = Ccocod "
													."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."'" 
													."   AND Encese= 'cerrado'";
						
							
							// se agrega esto a la consulta para que se busque por afinidad .						
							if($wafinidad!='seleccione')
							{
								$querycco = $querycco . "AND Encafi LIKE  '%".$wafinidad."%' ";
							}
							
							// se agrega esto a la consulta para que se busque por entidad .						
							if($wentidad!='seleccione')
							{
								$querycco = $querycco . "AND Encent LIKE  '%".$wentidad."%' ";
							}
							
							// se agrega esto al query para que busque por centro de costos hospitalarios
							if($whospitalario=='si')
							{
								$querycco = $querycco . " AND Ccohos = 'on' ";
							}
							
							// se agrega esto al query para que busque por centro de costos urgencias
							if($wurgencias=='si')
							{
								$querycco = $querycco . " AND Ccourg = 'on' ";
							}
							
							// se agrega esto al query para que busque por centro de costos cirugia
							if($wcirugia=='si')
							{
								$querycco = $querycco . " AND Ccocir = 'on' ";
							}
							
							// se agrega esto al query para que busque por centro de costos terapeutico
							if($wterapeutico=='si')
							{
								$querycco = $querycco . " AND Ccoter = 'on' ";
							}
							// se agrega esto al query para que busque por centro de costos de ayudas diagnosticas
							if($wayudas=='si')
							{
								$querycco = $querycco . " AND Ccoayu = 'on' ";
							}
								
						}
						
						// se imprime la consulta para ver si esta buena.
				
						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());
			
						$i=0;
						// por medio de este while se llenan los vectores  vectorccocontestado con el nombre y vectorcodccocontestado con el codigo
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Nombre'];
							$vectorcodccocontestado[$i] = $rowquerycco['Enccco'];
							$i++;
						}
						
					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;
					}
		
					// vector donde se almacena el promedio de las notas por centro de costos
					$vectorresporcco = array();
					// vector donde se almacena la cantidad de preguntas contestadas por agrupacion en centro de costos
					$vectorresporccocuantos = array();
					$vectorresporccocuantosnoaplica= array();
					$n=0;
			
					$arr_querys_aplican = array();
					$arr_querys_NOaplican = array();
					foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
					{
						
								$qccoxagrupacion = 	"SELECT encumage_000049.Enccco, encumage_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
												."   AND Encese= 'cerrado'"
												."   AND Desest !='off' ";
												
												
							$qccoxagrupacionconnoaplica = $qccoxagrupacion;
							
							$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";					
							// se agrega esto a la consulta para que se busque por afinidad .						
							if($wafinidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Encafi LIKE  '%".$wafinidad."%' ";
								$qccoxagrupacionconnoaplica =$qccoxagrupacionconnoaplica . " AND Encafi LIKE  '%".$wafinidad."%' ";
							}
							
							// se agrega esto a la consulta para que se busque por entidad .						
							if($wentidad!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Encent LIKE  '%".$wentidad."%' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Encent LIKE  '%".$wentidad."%' ";
							}
							
							if($wspregunta!='seleccione')
							{
								$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
								$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
							}
							$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
							$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";
			
							$arr_querys_aplican[] 	= $qccoxagrupacion;
							$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;
		
					}
					$union=" UNION  ";
					$querys_aplican = implode($union, $arr_querys_aplican);
					$querys_NOaplican = implode($union, $arr_querys_NOaplican);
				
					//echo '</pre>'.$querys_aplican.'</pre><br><br>';
					$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
					//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
					$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());	
					
					while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
					{
						$cuantos = $rowccoxagrupacion['Cuantos'];
						
						
						@$resvalorcco = $rowccoxagrupacion['Suma'] / $cuantos ;
						$roundresvalorcco = round($resvalorcco ,2);
						$vectorresporcco[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco;
						$vectorresporccocuantos[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos;
					}
					
					while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
					{
						$cuantosnoaplica = $rowccoxagrupacionconnoaplica['Cuantos'];
						$vectorresporccocuantosnoaplica[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica;
					}
					

					// si tiene mas de  uno , se debe hacer para el otro periodo
					if( $unperiodo!='1')
					{
						$arr_querys_aplican = array();
						$arr_querys_NOaplican = array();
						$vectorresporcco1 = array();
						$n=0;
						foreach($vectorcondiciones as $cod_clasificacion => $arr_agrupacion)
						{
			
								$qccoxagrupacion = 	" SELECT encumage_000049.Enccco, encumage_000051.Grucag, SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005 left join ".$wbasedato."_000051 on (Desagr = Grucod), ".$wbasedato."_000007 , ".$wbasedato."_000049  "
												." WHERE ".$arr_agrupacion." "
												."   AND Evades = Descod"
												."   AND Evafco = Encenc "
												."   AND Enchis = Evaevo "
												."   AND Enccco IN ('".implode("','",$vectorcodccocontestado)."') "
												."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
												."   AND Encese= 'cerrado'"
												."   AND Desest !='off' ";
												
								
								$qccoxagrupacionconnoaplica = $qccoxagrupacion;
							
								$qccoxagrupacion = $qccoxagrupacion."   AND Evacal != 0 ";		
								
								// se agrega esto a la consulta para que se busque por afinidad .						
								if($wafinidad!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
								}
								
								// se agrega esto a la consulta para que se busque por entidad .						
								if($wentidad!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . "AND Encent LIKE  '%".$wentidad."%' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . "AND Encent LIKE  '%".$wentidad."%' ";
								}
								
								if($wspregunta!='seleccione')
								{
									$qccoxagrupacion = $qccoxagrupacion . " AND Descod  = '".$wspregunta."' ";
									$qccoxagrupacionconnoaplica = $qccoxagrupacionconnoaplica . " AND Descod  = '".$wspregunta."' ";
								}
								
								$qccoxagrupacion .= " GROUP BY ".$wbasedato."_000049.Enccco";
								$qccoxagrupacionconnoaplica .= " GROUP BY ".$wbasedato."_000049.Enccco";
															
								$arr_querys_aplican[] 	= $qccoxagrupacion;
								$arr_querys_NOaplican[]	= $qccoxagrupacionconnoaplica;
								
						}
							
							$union=" UNION  ";
							$querys_aplican = implode($union, $arr_querys_aplican);
							$querys_NOaplican = implode($union, $arr_querys_NOaplican);
							
							$resccoxagrupacion = mysql_query($querys_aplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_aplican." - ".mysql_error());	
							//$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);
							$resccoxagrupacionconnoaplica = mysql_query($querys_NOaplican,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querys_NOaplican." - ".mysql_error());	
							
							
							while($rowccoxagrupacion  = mysql_fetch_array($resccoxagrupacion))
							{
								$cuantos1 = $rowccoxagrupacion['Cuantos'];
								
								
								@$resvalorcco1 = $rowccoxagrupacion['Suma'] / $cuantos1 ;
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $roundresvalorcco1;
								$vectorresporccocuantos1[$rowccoxagrupacion['Grucag']][$rowccoxagrupacion['Enccco']]= $cuantos1;
							}
					
							while( $rowccoxagrupacionconnoaplica = mysql_fetch_array($resccoxagrupacionconnoaplica) )
							{
								$cuantosnoaplica1 = $rowccoxagrupacionconnoaplica['Cuantos'];
								$vectorresporccocuantosnoaplica1[$rowccoxagrupacionconnoaplica['Grucag']][$rowccoxagrupacionconnoaplica['Enccco']]= $cuantosnoaplica1;
							}
					}
					
				}
	
				$m=0;
			
				echo"<br><br>";
				echo"<table style='border:#2A5DB0 1px solid' id='tablareporte'>";
				if( $unperiodo=='1')
				{
					echo"<tr><td class='encabezadoTabla' rowspan='2' >Nombre del Indicador</td>";
					echo"<td align='center' class='encabezadoTabla' colspan='".(count($vectorccocontestado) + 1)." '>Notas Promedio</td>";
				}
				else
				{
				  echo"<tr><td class='encabezadoTabla' rowspan='3' >Nombre del Indicador</td>";
				  echo"<td align='center' class='encabezadoTabla' colspan='".((count($vectorccocontestado) + 2) * 2)." '>Notas Promedio</td>"; 
				}
				
				//funciona si es porcco
				//------------------------------------
		
				if ($wcco!='' )
				{	
					$j=0;
					echo"<tr>";

						if( $unperiodo=='1')
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' >TOTAL CLINICA</td>";
							echo"</tr>";
						}
						else
						{
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' colspan='2' align='center'>".$vectorccocontestado[$j]."</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' colspan='2'>TOTAL CLINICA</td>";
							echo"</tr>";
							echo"<tr>";
							$j=0;
							while($j < count($vectorccocontestado))
							{
								echo"<td class='encabezadoTabla' align='center'>Periodo1</td><td class='encabezadoTabla' align='center'>Periodo2</td>";
								$j++;
							}
							echo"<td class='encabezadoTabla' align='center'>Periodo1</td><td class='encabezadoTabla' align='center'>Periodo2</td>";
							echo"</tr>";
							
						}
		
				}
	
				if($wcco!='')
				{
					
					
					foreach ( $vectorcondiciones  as $clasificacion => $codigo )
					{
			
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td width='200' class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$clasificacion]."</div></td>";
						//---------------------------
						
						if($wcco!='')
						{
					//03
							$j=0;
							while($j < count($vectorccocontestado))
							{	
								// trae los datos de cada uno de los centro de costos por agrupacion
								
							
								$color = traecolor ($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								$color1 = traecolor ($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								
								
								if( $unperiodo=='1')
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;
									
									
									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td align='center'  class='msg_tooltip'  title='Respuestas para cálculo  ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br>  Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."'  style='cursor: pointer;' bgcolor='".$color."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\")'>".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{
									if($vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]] = 0;
									
									
									if($vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]] =='' )
										$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]] == '')
										$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]= 0;
									
									if($vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]=='')
									  $vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]] = 0;
									
									$condicion = (count($vectoragrupaciones[$clasificacion]) > 1) ? "".implode("|", $vectoragrupaciones[$clasificacion])."" : "".implode("|", $vectoragrupaciones[$clasificacion])."";
									echo"<td bgcolor='".$color."'   align='center' style='cursor: pointer;' class='msg_tooltip'  title='Respuestas para cálculo ".$vectorresporccocuantos[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuestas Totales ".$vectorresporccocuantosnoaplica[$clasificacion][$vectorcodccocontestado[$j]]."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."\")' >".$vectorresporcco[$clasificacion][$vectorcodccocontestado[$j]]."</td ><td  style='cursor: pointer;' align='center' class='msg_tooltip'  title='Respuestas para cálculo ".$vectorresporccocuantos1[$clasificacion][$vectorcodccocontestado[$j]]."<br> Respuetas Totales ".$vectorresporccocuantosnoaplica1[$clasificacion][$vectorcodccocontestado[$j]]."'   bgcolor='".$color1."' onclick='abrirporpreguntas(this,\"".$condicion."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."\")' >".$vectorresporcco1[$clasificacion][$vectorcodccocontestado[$j]]."</td>";
									
									
								}
								
								$j++;
					
							}
						
							
						}
						//-------------total de la agrupacion
						
						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
											." WHERE ".$vectorcondiciones[$clasificacion]." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' "
											."   AND Encese= 'cerrado'"
											."   AND Desest !='off' ";
						
						$qtagrupacionnoaplica = $qtagrupacion;
						$qtagrupacion = $qtagrupacion ."   AND Evacal !=0  ";
						
		
						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}
						
						// se agrega esto a la columna para que se busque por entidad .						
						if($wentidad!='seleccione')
						{
							$qtagrupacion =$qtagrupacion . "AND Encent LIKE  '%".$wentidad."%' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . "AND Encent LIKE  '%".$wentidad."%' ";
						}
						
						if($wspregunta!='seleccione')
						{
							$qtagrupacion = $qtagrupacion . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica = $qtagrupacionnoaplica . " AND Descod  = '".$wspregunta."' ";
						}
							
						 
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());	
						$rowtagrupacion = mysql_fetch_array($restagrupacion);
						
						$restagrupacionnoaplica = mysql_query($qtagrupacionnoaplica,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica." - ".mysql_error());	
						$rowtagrupacionnoaplica = mysql_fetch_array($restagrupacionnoaplica);
						
						$qtagrupacion1 = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 , ".$wbasedato."_000049 "
											." WHERE ".$vectorcondiciones[$clasificacion]." "
											."   AND Evades = Descod"
											."   AND Evafco = Encenc "
											."   AND Enchis = Evaevo "
											."   AND ".$wbasedato."_000049.Fecha_data BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' "
											."   AND Encese= 'cerrado'"
											."   AND Desest !='off' ";
										
						$qtagrupacionnoaplica1 = $qtagrupacion1;
						$qtagrupacion1= $qtagrupacion1 ."   AND Evacal !=0  ";	
						
						// si la busqueda es por afinidad
						if($wafinidad!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . "AND Encafi LIKE  '%".$wafinidad."%' ";
						}	

						if($wentidad !='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . "AND Encent LIKE  '%".$wentidad."%' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1. "AND Encent LIKE  '%".$wentidad."%' ";
						}	
						
						if($wspregunta!='seleccione')
						{
							$qtagrupacion1 = $qtagrupacion1 . " AND Descod  = '".$wspregunta."' ";
							$qtagrupacionnoaplica1 = $qtagrupacionnoaplica1 . " AND Descod  = '".$wspregunta."' ";
						}
							
						 
						$restagrupacion1 = mysql_query($qtagrupacion1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion1." - ".mysql_error());	
						$rowtagrupacion1 = mysql_fetch_array($restagrupacion1);
						
						$restagrupacion1noaplica = mysql_query($qtagrupacionnoaplica1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacionnoaplica1." - ".mysql_error());	
						$rowtagrupacion1noaplica = mysql_fetch_array($restagrupacion1noaplica);
						
						
							$cuantost= $rowtagrupacion['Cuantos'];
							$cuantostnoaplica = $rowtagrupacionnoaplica['Cuantos'];
							@$valor = $rowtagrupacion['Suma']  / $cuantost;
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;
							
							$cuantost1= $rowtagrupacion1['Cuantos'];
							$cuantostnoaplica1 = $rowtagrupacion1noaplica['Cuantos'];
							@$valor1 = $rowtagrupacion1['Suma']  / $cuantost1;
							$total1 = $valor1 + $total1;
							$float_redondeado1=round($valor1 * 100) / 100;
							$color = traecolor ($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$color1 = traecolor ($float_redondeado1 ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							if( $unperiodo=='1')
							{
																																	
								echo"<td align='center'  align='center' class='msg_tooltip'  title='Respuestas para cálculo ".$cuantost." <br> Respuestas totales ".$cuantostnoaplica."' bgcolor =  '".$color."'  style='cursor: pointer;'  onclick='abrirporpreguntas(this,\"".$condicion."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$clasificacion]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."</td>";
							}
							else
							{
								
								echo"<td align='center'  class='msg_tooltip'  title='Respuestas para cálculo ".$cuantost."  <br> Respuestas totales ".$cuantostnoaplica." '   style='cursor: pointer;'   bgcolor =  '".$color."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td><td align='center'  class='msg_tooltip'  title='Respuestas para cálculo ".$cuantost1." <br> Respuestas Totales ".$cuantostnoaplica1."' ' style='cursor: pointer; '  bgcolor =  '".$color1."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado1."\")'>".$float_redondeado1."</td>";
								
							}
							$i++;

						//---------------------------------------
						
						echo"</tr>";
						$m++;
					}
				}
				else if($poragrupacion=='si' )
				{
					while($m < count($vectoragrupaciones))
					{
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$m]."</div></td>";
						//---------------------------
						
						if($porcco=='si')
						{
					
							$j=0;
							while($j < count($vectorccocontestado))
							{
								
								
								
								if( $unperiodo=='1')
								{
									echo"<td align='center'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{
								
									echo"<td class='encabezadoTabla'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td ><td align='center' class='encabezadoTabla' class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."' >".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
									
									
								}
								
								$j++;
							}
							
						}
						//-------------total de la agrupacion
						
						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007  "
											." WHERE ".$vectorcondiciones[$n]." "
											."   AND Evades = Descod"
											."   AND Desest !='off' ";
							

						 
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());	
						$rowtagrupacion = mysql_fetch_array($restagrupacion);
						
						
							$valor = $rowtagrupacion['Suma']  / $rowtagrupacion['Cuantos'];
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;
							
							$color = traecolor($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							
								if( $unperiodo=='1')
								{
										echo"<td align='center' style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td>";
								}
								else
								{
									
									echo"<td align='center'   align='center'  style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."</td><td  style='cursor: pointer;' align='center' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td>";
		
								}

							$i++;

						echo"</tr>";
						$m++;
					}
				}
				echo"</table>";	 */ 
				break;
			   
			   Case '04':
			   
				$q = "SELECT  Calmax "
					."  FROM  ".$wbasedato."_000034 "
					." WHERE  Calfor = '".$wtemareportes."' "
					."   AND  Calest = 'on' ";
					
				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				$row = mysql_fetch_array($res);
				
				$multiplicador = $row['Calmax'];
					
			    if ($wcco!='')
				{	
					$vectorccocontestado = array();
					$vectorcodccocontestado  = array();
		
					if ($wcco=='todos')
					{
		
						$querycco =	 "SELECT DISTINCT(Cconom),Ccocod"
									."  FROM ".$wbasedato."_000032 ,costosyp_000005, talhuma_000013, ".$wbasedato."_000002 "
									." WHERE Ideuse = Mcauco "
									."   AND Idecco = Ccocod"
									."   AND Forcod = Mcafor"
									."   AND ".$wbasedato."_000002.Fortip = '".$wtemareportes."' ";
						
						$resquerycco = mysql_query($querycco,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$querycco." - ".mysql_error());
						
						$i=0;
						while($rowquerycco = mysql_fetch_array($resquerycco))
						{
							$vectorccocontestado[$i] = $rowquerycco['Cconom'];
							$vectorcodccocontestado[$i] = $rowquerycco['Ccocod'];
							$i++;
						}
						
					}
					else
					{
						$vectorccocontestado[]  = $wnomcco;
						$vectorcodccocontestado[] = $wcodcco;
		
					}
		
					$vectorresporcco = array();
					$vectorresporccocuantos[]= array();
					$n=0;
					while($n < count($vectoragrupaciones))
					{
					
						$j=0;
						while($j < count($vectorccocontestado))
						{
							
							
							$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
												."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
												." WHERE Desagr='".$vectoragrupaciones[$n]."' "
												."   AND Evades = Descod"
												."   AND Mcauco = Evaevo"
												."   AND Desest !='off' "
												."   AND Ideuse = Mcauco "
												."   AND Idecco = '".$vectorcodccocontestado[$j]."' "
												//."   AND ".$wbasedato."_000032.fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' ";
												."   AND Mcaano = '".$wano1."' AND Mcaper = '".$wperiodo1."' ";		
												
							$resccoxagrupacion = mysql_query($qccoxagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
							$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);	
							$cuantos =$rowccoxagrupacion['Cuantos'];
							
							@$resvalorcco = ($rowccoxagrupacion['Suma'] / $cuantos ) * ($multiplicador);
							
							
							$roundresvalorcco = round($resvalorcco ,2);
							$vectorresporcco[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $roundresvalorcco;
							$vectorresporccocuantos[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $cuantos;
							$j++;
						}
						$n++;
										
					}
		
					if( $unperiodo!='1')
					{
	
						$vectorresporcco1 = array();
						$vectorresporccocuantos1=array();
						$n=0;
						while($n < count($vectoragrupaciones))
						{
						
							$j=0;
							while($j < count($vectorccocontestado))
							{
								
								
								$qccoxagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
													."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
													." WHERE Desagr='".$vectoragrupaciones[$n]."' "
													."   AND Evades = Descod"
													."   AND Mcauco = Evaevo"
													."   AND Ideuse = Mcauco "
													."   AND Desest !='off' "
													."   AND Idecco = '".$vectorcodccocontestado[$j]."' "
													//."   AND  ".$wbasedato."_000032.fecha_data  BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' ";
													."   AND Mcaano = '".$wano2."' AND Mcaper = '".$wperiodo2."' ";		
													
								$resccoxagrupacion = mysql_query($qccoxagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qccoxagrupacion." - ".mysql_error());	
								$rowccoxagrupacion = mysql_fetch_array($resccoxagrupacion);	
								
								$cuantos1 = $rowccoxagrupacion['Cuantos'];
								
								
								@$resvalorcco1 = ($rowccoxagrupacion['Suma'] / $cuantos1) * ($multiplicador);
								$roundresvalorcco1 = round($resvalorcco1 ,2);
								$vectorresporcco1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]= $roundresvalorcco1;
								
								$vectorresporccocuantos1[$vectoragrupaciones[$n]][$vectorcodccocontestado[$j]]=$cuantos1;
								$j++;
							}
							$n++;
						}
					}
				}
				$m=0;
		
				// si se va a ver por todos las agrupaciones sin ningun detalle

				echo"<br><br>";
				echo"<table style='border:#2A5DB0 1px solid'>";
				
				echo"<tr><td class='encabezadoTabla' rowspan='2' >Nombre del Indicador</td>";
				
				echo"<td align='center' class='encabezadoTabla' colspan='".(count($vectorccocontestado) + 1)." '>Notas Promedio</td>";
	
				//funciona si es porcco
				//------------------------------------
		
				if ($wcco!='' )
				{	
					
					$j=0;
					echo"<tr>";
					while($j < count($vectorccocontestado))
					{
						
						if( $unperiodo=='1')
						{
							echo"<td class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]."</td>";
						}
						else
						{
							echo"<td>";
							echo"<table>";
							echo"<tr>";
							echo"<td colspan='2' class='encabezadoTabla' align='center'>".$vectorccocontestado[$j]."</td>";
							echo"</tr>";
							echo"<tr>";
							echo"<td class='encabezadoTabla' align='center'>Periodo1</td><td class='encabezadoTabla' align='center'>Periodo2<td>";
							echo"</tr>";
							echo"</table>";
							echo"</td>";
							
						}
						$j++;
					}
				}
				//-----------------------------------
		
				if( $unperiodo=='1')
				{
					echo"<td class='encabezadoTabla' >TOTAL CLINICA</td></tr>";
				}
				else
				{
					echo"<td>";
					echo"<table>";
					echo"<tr>";
					echo"<tr>";
					echo"<td class='encabezadoTabla' align='center' colspan='2'>TOTAL CLINICA</td>";
					echo"</tr>";
					echo"<td class='encabezadoTabla'>Periodo1</td ><td class='encabezadoTabla'>periodo2</td>";
					echo"</tr>";
					echo"</table>";
				}
	
				if($wcco!='')
				{
					while($m < count($vectoragrupaciones))
					{
		
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$m]."</div></td>";
						//---------------------------
						
						if($wcco!='')
						{
					
							$j=0;
							while($j < count($vectorccocontestado))
							{	
						
					
								$color = traecolor ($vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								$color1 = traecolor ($vectorresporcco1[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]] ,$vectormaximo,$vectorminimo,$vectorcolor) ;
								
								
								if( $unperiodo=='1')
								{
									echo"<td align='center' bgcolor='".$color."'  class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."'  style='cursor: pointer;' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."\")'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{
									echo"<td>";
									echo"<table  style='width:100%;  height:100%;'>";
									echo"<tr>";
									echo"<td bgcolor='".$color."'  style='cursor: pointer;' align='center' class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."'  onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."\")' >".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td ><td  style=' cursor: pointer;' bgcolor='".$color1."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$vectorresporcco1[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."\")'  class='msg_tooltip'  title='Cantidad de Respuestas ".$vectorresporccocuantos1[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."'>".$vectorresporcco1[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
									echo"</tr>";
									echo"</table>";
									echo"</td>";
									
								}
								
								$j++;
	
							}
							
						}
						//-------------total de la agrupacion

						$qtagrupacion = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." WHERE Desagr='".$vectoragrupaciones[$m]."' "
											."   AND Evades = Descod"
											."   AND Mcauco = Evaevo"
											."   AND Desest !='off' "
											."   AND Ideuse = Mcauco "
											//."   AND  ".$wbasedato."_000032.fecha_data  BETWEEN '".$fechainicial1."' AND '".$fechafinal1."' ";
											." AND Mcaano = '".$wano1."' AND Mcaper = '".$wperiodo1."' ";		
							
						 
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());	
						$rowtagrupacion = mysql_fetch_array($restagrupacion);
						
						$qtagrupacion1 = 	" SELECT SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007 ,  ".$wbasedato."_000032 , talhuma_000013 "
											." WHERE Desagr='".$vectoragrupaciones[$m]."' "
											."   AND Evades = Descod"
											."   AND Desest !='off' "
											."   AND Mcauco = Evaevo"
											."   AND Ideuse = Mcauco "
										//	."   AND  ".$wbasedato."_000032.fecha_data  BETWEEN '".$fechainicial2."' AND '".$fechafinal2."' ";
											."   AND Mcaano = '".$wano2."' AND Mcaper = '".$wperiodo2."' ";		
 
						$restagrupacion1 = mysql_query($qtagrupacion1,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion1." - ".mysql_error());	
						$rowtagrupacion1 = mysql_fetch_array($restagrupacion1);
						
						
							$cuantost= $rowtagrupacion['Cuantos'];
							@$valor = $rowtagrupacion['Suma']  / $cuantost;
							
							$total = $valor + $total;
							$float_redondeado=(round($valor * 100) / 100) * ($multiplicador);
							
							$cuantost1= $rowtagrupacion1['Cuantos'];
							
							@$valor1 = $rowtagrupacion1['Suma']  / $cuantost1;
							$total1 = $valor1 + $total1;
							$float_redondeado1=(round($valor1 * 100) / 100 ) * ($multiplicador);
							$color = traecolor ($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							$color1 = traecolor ($float_redondeado1 ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							
							if( $unperiodo=='1')
							{
								echo"<td align='center' bgcolor =  '".$color."' style='cursor: pointer;'  class='msg_tooltip'  title='Cantidad de Respuestas ".$cuantost."'  onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td>";
							}
							else
							{
								echo"<td>";
								echo"<table  style='width:100%; height:100%;'>";
								echo"<tr>";
								echo"<td align='center'   class='msg_tooltip'  title='Cantidad de Respuestas ".$cuantost."'    style='cursor: pointer;'  bgcolor =  '".$color."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td><td  class='msg_tooltip'  title='Cantidad de Respuestas ".$cuantost1."'  align='center'  style='cursor: pointer; '  bgcolor =  '".$color1."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"todos\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado1."\")'>".$float_redondeado1."</td>";
								echo"</tr>";
								echo"</table>";
								echo"</td>";
							}
							$i++;
						
						
						//---------------------------------------
						
						echo"</tr>";
						$m++;
					}
				}
				else if($poragrupacion=='si' )
				{
					while($m < count($vectoragrupaciones))
					{
						
				
						//--------nombre de agrupacion
						echo"<tr >";
						echo"<td class='encabezadoTabla' colspan='1' align='Left'  ><div id=td_nombreagrupacion>".$vectornomagrupaciones[$m]."</div></td>";
						//---------------------------
						
						if($porcco=='si')
						{
					
							$j=0;
							while($j < count($vectorccocontestado))
							{
								
								// no se que hace aqui
								
								if( $unperiodo=='1')
								{
									echo"<td align='center'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
								}
								else
								{
									echo"<td>";
									echo"<table>";
									echo"<tr>";
									echo"<td class='encabezadoTabla'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td ><td class='encabezadoTabla'>".$vectorresporcco[$vectoragrupaciones[$m]][$vectorcodccocontestado[$j]]."</td>";
									echo"</tr>";
									echo"</table>";
									echo"</td>";
									
								}
								
								$j++;
							}
							
						}
						//-------------total de la agrupacion
						
						$qtagrupacion = 	" SELECT Descod,Desdes,SUM(Evacal) AS Suma, COUNT(Evacal) AS Cuantos  "
											."  FROM ".$wbasedato."_000005, ".$wbasedato."_000007  "
											." WHERE Desagr='".$vectoragrupaciones[$m]."' "
											."   AND Evades = Descod"
											."   AND Desest !='off' ";
				 
						$restagrupacion = mysql_query($qtagrupacion,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$qtagrupacion." - ".mysql_error());	
						$rowtagrupacion = mysql_fetch_array($restagrupacion);
						
							$valor = ($rowtagrupacion['Suma']  / $rowtagrupacion['Cuantos']) * ($multiplicador);
							$total = $valor + $total;
							$float_redondeado=round($valor * 100) / 100;
							
							$color = traecolor($float_redondeado ,$vectormaximo,$vectorminimo,$vectorcolor) ;
							
								if( $unperiodo=='1')
								{
										echo"<td align='center' bgcolor='".$color ."'>".$float_redondeado."</td>";
								}
								else
								{
									echo"<td>";
									echo"<table style='width:100%;  height:100%;'>";
									echo"<tr>";
									echo"<td align='center'   style='cursor: pointer;' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial1."\",\"".$fechafinal1."\",\"".$vectornomagrupaciones[$j]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")' >".$float_redondeado."</td><td  style='cursor: pointer;' align='center' bgcolor='".$color ."' onclick='abrirporpreguntas(this,\"".$vectoragrupaciones[$m]."\",\"".$vectorcodccocontestado[$j]."\",\"".$fechainicial2."\",\"".$fechafinal2."\",\"".$vectornomagrupaciones[$m]."\",\"".$vectorccocontestado[$j]."\",\"".$float_redondeado."\")'>".$float_redondeado."</td>";
									echo"</tr>";
									echo"</table>";
									echo"</td>";
									
								}
	
							$i++;
						
						
						//---------------------------------------
						
						echo"</tr>";
						$m++;
					}
				
				}
				echo"</table>";	  
               break;
			   
			   
               default :
                       break;
       }
	   //echo"</table>";
	   if( $unperiodo=='1')
	   {
		   echo "<br>";
		   echo "<br>";
		   echo "<br>";
		   echo "<table>";
		   echo "<tr><td><input type='button' value='Graficar' onclick='pintarGrafica()'></td></tr>";
		   echo "</table>";
		   echo "<br>";
		   echo "<br>";
		   echo "<table>";
		   echo "<tr><td><div id='amchart1' style='width:1500px; height:600px;'></div></td></tr>";
		   echo "</table>";
	   }
	   return;
}





?>
<html>
<head>
<title>Reportes por Agrupacion</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />		
<script type='text/javascript'>
	var fechainicialeps;
	var fechafinaleps;
	var fechainicialeps1;
	var fechafinaleps1;
	var gtipoformato;
	
function cambiarfechaseps(){
	
	if(fechainicialeps != $('#wfecha_i').val() || fechafinaleps != $('#wfecha_f').val())
	{
		if($('#periodo2:checked').val()=='si' )
		{
			fechainicialeps = $('#wfecha_i').val().replace(/-/gi,""); 
			fechafinaleps = $('#wfecha_f').val().replace(/-/gi,"");
			fechainicialeps1 = $('#wfecha_i1').val().replace(/-/gi,""); 
			fechafinaleps1 = $('#wfecha_f1').val().replace(/-/gi,"");
			
			if (fechainicialeps1 < fechainicialeps)
				fechainicialeps = $('#wfecha_i1').val();
			else
				fechainicialeps = $('#wfecha_i').val();
			
			if(fechafinaleps1 > fechafinaleps )
				fechafinaleps = $('#wfecha_f1').val();
			else 
				fechafinaleps = $('#wfecha_f').val();
		
		}
		else
		{
			fechainicialeps = $('#wfecha_i').val();
			fechafinaleps = $('#wfecha_f').val();

		}
		
			$.get("../reportes/reportes_por_agrupacion.php",
				{
					consultaAjax 	: '',
					inicial2			: 'no',
					operacion		: 'cambiaresultadoseps',
					wfechainicialeps : fechainicialeps,
					wfechafinaleps	 : fechafinaleps,
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wemp_pmla		: $('#wemp_pmla').val()
					
				}
				, function(data) {
				
				
				$('#select_entidad').html(data);
				});
	}

}

function abrir3ventana(wagrupacion,centrocostos,fechainicial,fechafinal)
{



if ($('#selectperiodos').val()==undefined)
{
 ano = 'nada';
 periodo = 'nada';
}
else
{
var wanoperiodo = $('#selectperiodos').val().split("||");
ano = wanoperiodo[0];
periodo = wanoperiodo[1];
}

//cambia imagen de + a -
if($("#img_mas-"+wagrupacion+'-'+centrocostos).attr('src') == '../../images/medical/hce/mas.PNG')
{
	$("#img_mas-"+wagrupacion+'-'+centrocostos).attr('src', '../../images/medical/hce/menos.PNG');
}else
{
	$("#img_mas-"+wagrupacion+'-'+centrocostos).attr('src', '../../images/medical/hce/mas.PNG');
}
//-----------	

if($('#tabletresx-'+wagrupacion+'-'+centrocostos).length > 0)
{

$('#tabletresx-'+wagrupacion+'-'+centrocostos).remove();
$('#tercerventana-'+wagrupacion+'-'+centrocostos).css("background-color","");
}
else
{

$.post("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeresultadov3',
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wemp_pmla		: $('#wemp_pmla').val(),
				wperiodo		: periodo,
				wano			: ano,
				wcagrupacion	: wagrupacion,
				wcentrocostos	: centrocostos,
				wtemareportes	: $('#select_tema').val(),
				wtipoformato	: gtipoformato,
				wfechainicial	: fechainicial,
				wfechafinal		: fechafinal,
				fechainicial1	: fechafinal,
				wentidad 		: $('#select_entidad').val(),
				wafinidad		: $('#select_afinidad').val(),
				
			}
			, function(data) {
					
					
					
					$('#tercerventana-'+wagrupacion+'-'+centrocostos).html(data);
					$('#tercerventana-'+wagrupacion+'-'+centrocostos).css("background-color","white");
					$('#tabletres-'+wagrupacion+'-'+centrocostos).css("bgcolor","white");
					$(".msg_tooltipx-"+wagrupacion+'-'+centrocostos).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			
			});

}
}

function mostrarSoloTd( obj ){
	obj = jQuery(obj);
	obj = obj.parent();
	//console.log( obj.html() );
	var indice_fila = obj.parent().index();//Numero de tr en la tabla
	var indice_columna = obj.index();//Numero de td en el tr
	//console.log("indice_fila->"+indice_fila+" indice_columna->"+indice_columna );
	
	obj.parent().addClass('noocultar'); //Le agrega la clase noocultar al tr
	obj.parent().parent().find('tr.ocultar').not('.noocultar').hide(); //Oculto todos los tr con la clase ocultar que no tengan la clase noocultar
	
	obj.addClass('noocultar'); //Le agrega la clase noocultar al td
	obj.parent().find('td').eq(0).addClass('noocultar'); //Le agrega la clase noocultar al td
	obj.parent().find('td').not('.noocultar').hide(); //Oculto todos los td que no tengan la clase noocultar
	
	$(".pisos").find('td').eq( (indice_columna-1) ).addClass('noocultar'); //Le agrega la clase noocultar al td
	$(".pisos").find('td').not('.noocultar').hide(); //Oculto todos los td que no tengan la clase noocultar
	
	
    obj.parent().parent().find('td.Periodo').hide();
	/*$(".pisos").find('td').each( function(){
		if( $(this).index() != (indice_columna-1) ){
			$(this).hide();
		}
	});*/
}


function abrirporpreguntas(ele, agrupacion, centrocco , fechainicial, fechafinal,nombreagrupacion,nombrecco,puntaje,indicador,tipoformato )
{
	
		gtipoformato=tipoformato;
		if ($('#selectperiodos').val()==undefined)
		{
		 ano = 'nada';
		 periodo = 'nada';
		}
		else
		{
			var wanoperiodo = $('#selectperiodos').val().split("||");
			ano = wanoperiodo[0];
			periodo = wanoperiodo[1];
		}
	
	
	if(puntaje=='0')
	{
	}
	else
	{
	if( $('#tablesegundax-'+indicador).length > 0)
	{
		$('#tablesegundax-'+indicador).remove();
		$('#ddiv-'+indicador).css("background-color","");
		
		$(".noocultar").removeClass('noocultar');
		$("#tablareporte tr, #tablareporte td").show();
	}
	else
	{
		
		//if( ele != null ){
			mostrarSoloTd( ele );
		//}
		$.get("../reportes/reportes_por_agrupacion.php",
				{
					consultaAjax 	: '',
					inicial			: 'no',
					operacion		: 'traeResultadosPorPregunta',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wtemareportes	: $('#select_tema').val(),
					wagrupacion		: agrupacion,
					wcentrocostos	: centrocco,
					wfechainicial	: fechainicial,
					wfechafinal		: fechafinal,
					wnombreagr		: nombreagrupacion,
					wnombrecco		: nombrecco,
					wseleccco		: $('#select_wcco').val(),
					wentidad 		: $('#select_entidad').val(),
					wafinidad		: $('#select_afinidad').val(),
					wspregunta 		: $('#select_pregunta').val(),
					wperiodo		:periodo,
					wano			:ano,
					windicador		:indicador
					
				}
				, function(data) {
				$('#ddiv-'+indicador).html(data);
				$('#ddiv-'+indicador).css("background-color","white");
				$(".msg_tooltip1"+indicador).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				//var div ='div_reporte';
				//fnMostrar2(div);
				});

		}
	}
}

//************************************************************************************************************************

function pintarGrafica ()
{

$("[id^=tablesegundax-]").remove();
$("[id^=ddiv-]").css("background-color","");
$('#tablareporte tr, #tablareporte td').show();
$(".noocultar").removeClass('noocultar');
// 
$('#tablareporte').LeerTablaAmericas({ 
			
			empezardesdefila: 2, 
			titulo : 'Notas Promedio por Centro de Costos ' ,
			tituloy: 'cantidad',
			filaencabezado : [1,0],
			datosadicionales : 'todo'
						
		});
		
	
}



function pintarGrafica2 (ind)
{
$("[id^=tabletresx]").remove();
$("[id^=tercerventana]").css("background-color","");
$("#areasegunda-"+ind).css("width","400px");
$("#areasegunda-"+ind).css("height","300px");
$('#tablesegunda-'+ind).LeerTablaAmericas({ 
			
			empezardesdefila: 1, 
			titulo : 'Notas Promedio por Agrupacion ' ,
			tituloy: 'cantidad',
			filaencabezado : [0,1],
			datosadicionales : 'todo',
			divgrafica: "areasegunda-"+ind
			
	
						
		});
}

function pintarGrafica3 (agrupacion, centrodecostos)
{
$("[id^=tablex]").remove();
$("[id^=cuartaventana]").css("background-color","");
$("#areatercera-"+agrupacion+"-"+centrodecostos).css("width","500px");
$("#areatercera-"+agrupacion+"-"+centrodecostos).css("height","500px");
$('#tabletres-'+agrupacion+'-'+centrodecostos).LeerTablaAmericas({ 
			
			empezardesdefila: 1, 
			titulo : ' ' ,
			tituloy: ' ',
			filaencabezado : [0,1],
			datosadicionales : 'todo',
			divgrafica: "areatercera-"+agrupacion+"-"+centrodecostos,
			columnadatos: 1
						
		});
		
	
}


function pintarGrafica4 (agrupacion, centrodecostos,cpregunta)
{
$("#areacuatro-"+cpregunta+"-"+agrupacion+"-"+centrodecostos).css("width","400px");
$("#areacuatro-"+cpregunta+"-"+agrupacion+"-"+centrodecostos).css("height","300px");

$('#table-'+cpregunta+'-'+agrupacion+'-'+centrodecostos).LeerTablaAmericas({ 
			
			empezardesdefila: 1, 
			titulo : ' ' ,
			tituloy: ' ',
			filaencabezado : [0,1],
			datosadicionales : 'todo',
			divgrafica: "areacuatro-"+cpregunta+"-"+agrupacion+"-"+centrodecostos,
			columnadatos: 1
						
		});
		
	
}

//********************************************************************************************************************************

function verAgrupaciones()
{
	if($('#select_tema').val()=='seleccione')
	{
		$('#div_resultados').html('');
	}
	else
	{
		$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarAgrupaciones',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val()
				
			}
			, function(data) {
				$('#div_resultados').html(data);
				// calendario( 'wfecha_i' );
				// calendario( 'wfecha_f' );
			});
	}

}

function CargarSelectPreguntas()
{
	var agrupacion;
	agrupacion = $('#select_agrupacion').val().split('||');
	
	
	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectPreguntas',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wagrupacion		: agrupacion[1]
				
			}
			, function(data) {
			$('#div_select_pregunta').html(data);
		
			});

}

function CargarSelectAgrupacion()
{
	var clasificacionagrupacion;
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();
	
	
	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'CargarSelectAgrupacion',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wclasificacionagrupacion	: clasificacionagrupacion
				
			}
			, function(data) {
			$('#div_select_agrupacion').html(data);
		
			});

}

function borrarpreguntas(){
 $('#div_contenido_porpreguntas').html('');
}

function fnMostrar2( celda )
{
    
	if( $('#'+celda ) )
	{
		$.blockUI({ message: $('#'+celda ),
						css: { left: ( $(window).width() - 1000 )/2 +'px',
							  top: '100px',
							  width: '1000px',
							  height: 'auto',
							  overflow:	'scroll'
							 }
				  });
	
	}

}
function esperar (  )
{
$.blockUI({ message:        '<img src="../../images/medical/ajax-loader.gif" >',
                               css:         {
                                           width:         'auto',
                                           height: 'auto'
                                       }
                       });
}

function traeDetallePregunta(pregunta,agrupacion,centrocco,fechainicial,fechafinal,elemento,tipoformato,numeropreguntas)
{
	
	
	
	if( $("#"+elemento).next('tr').hasClass('tumama')){
		if( $("#"+elemento).next('tr').css('display') == 'none'){
			$("#"+elemento).next('tr').slideDown('slow');
		}else{
			$("#"+elemento).next('tr').hide('slow');
		}
		return;
	}
	
	if(fechainicial=='' &&  fechafinal =='')
	{
		if ($('#selectperiodos').val()==undefined)
		{
		 ano = 'nada';
		 periodo = 'nada';
		}
		else
		{
			var wanoperiodo = $('#selectperiodos').val().split("||");
			ano = wanoperiodo[0];
			periodo = wanoperiodo[1];
		}
	}
	//cambia imagen de + a -
	if($("#img_mas-"+agrupacion+"-"+centrocco+"-"+pregunta).attr('src') == '../../images/medical/hce/mas.PNG')
	{
		$("#img_mas-"+agrupacion+"-"+centrocco+"-"+pregunta).attr('src', '../../images/medical/hce/menos.PNG');
	}else
	{
	$("#img_mas-"+agrupacion+"-"+centrocco+"-"+pregunta).attr('src', '../../images/medical/hce/mas.PNG');
	}
	//-----------			
	if( $("#tablex-"+pregunta+"-"+agrupacion+"-"+centrocco).length > 0)
	{
		$("#tablex-"+pregunta+"-"+agrupacion+"-"+centrocco).remove();
		$("#cuartaventana-"+agrupacion+"-"+centrocco+"-"+pregunta).css("background-color","");
		
		
	}
	else
	{

	$.get("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeDetallePregunta',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wagrupacion		: agrupacion,
				wcentrocostos	: centrocco,
				wfechainicial	: fechainicial,
				wfechafinal		: fechafinal,
				wseleccco		: $('#select_wcco').val(),
				wcodpregunta	: pregunta,
				wtipoformato	: gtipoformato,
				wentidad 		: $('#select_entidad').val(),
				wafinidad		: $('#select_afinidad').val(),
				wnumeropreguntas : numeropreguntas,
				wperiodo		: periodo,
				wano			: ano
			
			}
			, function(data) {
			//$('#detallepreguntas').html(data);
				
				
				$("#cuartaventana-"+agrupacion+"-"+centrocco+"-"+pregunta).css("background-color","white");
				$("#cuartaventana-"+agrupacion+"-"+centrocco+"-"+pregunta).html(data);
				// $("#"+elemento).after("<tr class='tumama fila1'><td colspan=2>"+data+"</td></tr>");
				// $(".msg_tooltip3").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			});
			
			}

}

function verReporte()
{
	
	var agrupacion;
	var centrocostos;
	var codcco;
	var nombrecco;
	var hospitalario;
	var urgencias;
	var terapeutico;
	var cirugia;
	var clasificacionagrupacion;
	var ayudas;
	var ano;
	var periodo;
	
	if ($('#selectperiodos').val()==undefined)
	{
	 ano = 'nada';
	 periodo = 'nada';
	}
	else
	{
		var wanoperiodo = $('#selectperiodos').val().split("||");
		ano = wanoperiodo[0];
		periodo = wanoperiodo[1];
	}
	
	agrupacion = $('#select_agrupacion').val();
	centrocostos = $('#select_wcco').val();
	afinidad = $('#select_afinidad').val();
	entidad = $('#select_entidad').val();
	pregunta = $('#select_pregunta').val();
	clasificacionagrupacion = $('#select_clasificacionagrupacion').val();
	var tipocco = $('#tipocco').val();

	if( $('#servicio_hospitalario:checked').val()=='si')
	{
		hospitalario = $('#servicio_hospitalario').val();
	}
	
	if( $('#servicio_urgencias:checked').val()=='si')
	{
		urgencias = $('#servicio_urgencias').val();
	}
	
	if( $('#servicio_terapeuticos:checked').val()=='si')
	{
		terapeutico = $('#servicio_terapeuticos').val();
	}
	
	if( $('#servicio_cirugia:checked').val()=='si')
	{
		cirugia = $('#servicio_cirugia').val();
	}
	
	if( $('#servicio_ayudas:checked').val()=='si')
	{
		ayudas = $('#servicio_ayudas').val();
	}

	if(centrocostos=='seleccione' || agrupacion=='seleccione')
	{
		var mensaje = unescape('Debe seleccionar un centro de costos o una agrupación');
		alert(mensaje);
		return;
	}
	
	if(centrocostos=='todos||todos')
	{
		centrocostos='todos';
		
	}
	else
	{
		centrocostos=centrocostos.split('||');
		
		nombrecco=centrocostos[1];
		codcco=centrocostos[0];
	}
	
	var fechainicial2;
	var fechafinal2;
	var porcco;
	var porpregunta;
	var poragrupacion ='si';
	
	var splitagrupacion = agrupacion.split('||');
	
	var codigoagrupacion = splitagrupacion[1];
	var nombreagrupacion = splitagrupacion[0];
	
	
	
	if($('#periodo2:checked').val()=='si' )
	{
	
	
		fechainicial2=$('#wfecha_i1').val();
		fechafinal2=$('#wfecha_f1').val();
		unperiodo=2;
	 
	}
	else
	{
		unperiodo=1;
	}
	//selectperiodo
	if($('#wfecha_i').val()==undefined)
	{
				if($('#selectperiodos').val()=='nada||nada')
				{
				alert ("debe seleccionar una periodo");
				return;
				}
				
	}
	esperar();
	$.post("../reportes/reportes_por_agrupacion.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeResultadosReporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: $('#select_tema').val(),
				wcagrupacion	: codigoagrupacion,
				wnagrupacion	: nombreagrupacion,
				porcco			: porcco,
				porpregunta		: porpregunta,
				poragrupacion	: poragrupacion,
				fechainicial1 	:$('#wfecha_i').val(),
				fechafinal1		:$('#wfecha_f').val(),
				fechafinal2		: fechafinal2,
				fechainicial2	: fechainicial2,
				unperiodo		: unperiodo,
				wcco			: centrocostos,
				wcodcco			: codcco,
				wnomcco			: nombrecco,
				wafinidad		: afinidad,
				wentidad		: entidad,
				whospitalario	: hospitalario,
				wurgencias 		: urgencias,
				wterapeutico 	: terapeutico,
				wayudas			: ayudas,
				wcirugia 		: cirugia,
				wspregunta		: pregunta,
				wclasificacionagrupacion : clasificacionagrupacion,
				tipocco 		: tipocco ,
				wperiodo1		: periodo,
				wano1			: ano
	
				
			}
			, function(data) {
				$('#div_contenido_reporte').html(data);
				$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				$.unblockUI();
			});

}

</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
	#tooltip
	{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
</style>

</head>
<body >

<?php

/**
 PROGRAMA                   : reportes_por_agrupacion.php
 AUTOR                      : Felipe Alvarez Sanchez.
 FECHA CREACION             : Noviembre 05  de 2012

 DESCRIPCION:				: Programa que agrupa a los descriptores para asi formar reportes de la misma indole


 ACTUALIZACIONES:


**/
$wactualiz = "(Diciembre 03 de 2012)";

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';

/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/

//------- campos ocultos

echo "<input type='hidden' name='wtema' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' name='wemp_pmla' id='tipocco' value='".$tipocco."'>";

//----------------------------

//tabla principal
echo"<table id='tabla_principal' width='900' align='center'>";
//primer tr :  Select tema.
echo"<tr><td>";

//datos select 
$q = "	SELECT  Forcod, Fordes "
	."	  FROM  ".$wbasedato."_000042 "
	."   WHERE Fortip='01' "
	."      OR Fortip='03' "
	."      OR Fortip='04' ";
// Nota: solo esta funcionando para evaluaciones internas Fortip=01 y para encuestas usuarios registrados Fortip=03
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

echo"<div id='div_tema' align='center'>";
echo"<table width='500' align='center'  border='0' cellspacing='0' cellpadding='0'>";
echo"<tr>";
echo"<td width='200' class='encabezadoTabla' align='Center' >Seleccione Tema</td>";
echo"<td  width='300' class='encabezadoTabla' align='Left'>";
echo"<select id='select_tema' onchange='verAgrupaciones()'>";

echo"<option value='seleccione' selected >Seleccione</option>";

while($row = mysql_fetch_array($res))
{
	echo"<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
}
echo"</select>";
echo"</td>";
echo"</tr>";
echo"</table>";		
echo"</div>"; 	
echo"</td></tr>";
echo"</table>";
echo"<br><br>";
echo"<div id='div_resultados' >";
echo"</div>";

//-----div Muestra de resultados-------------------------

echo "<table align='center'>";
echo "<tr>";
echo "<td>";
echo "<div id='div_contenido_reporte'></div>";
echo "<td>";
echo "</tr>";
echo "</table>";

//-------------------------------------------------------------
echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0;height: 400px' >";
//echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;height:auto;background:none repeat' >";
echo "<input id='nombreAgrupacionAgregar' type='hidden'>";
echo "<br><br>";

// div para mostrar las preguntas
echo "<div id='div_contenido_porpreguntas'>";

echo "</div>";
// div de detalle de preguntas 
echo "<div id='detallepreguntas'>";
echo "</div>";

echo"<br><br>";
echo"<input type='button' value='Cancelar'  onClick='$.unblockUI(); borrarpreguntas();' style='width:100'>";
echo"</div>";

echo "<div id='div_esperar' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0;height: 400px' >";
echo "</div>";
//------------------------------------------------------
//------------------------------------------------------
function traecolor ($puntaje ,$vectormaximo,$vectorminimo,$vectorcolor) 
{
 $i=0;
 $color = '#F2F2F2';
 while($i<count($vectormaximo))
 {
	if($puntaje>$vectorminimo[$i] AND $puntaje<=$vectormaximo[$i])
	$color = $vectorcolor[$i];
	$i++;
 }
 return $color;
}
	
?>

</body>
</html>