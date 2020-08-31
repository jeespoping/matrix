<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");


include_once("root/comun.php");
include_once("../procesos/funciones_talhuma.php");


global $wemp_pmla;

$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fecha= date("Y-m-d");
$hora = date("H:i:s");



if ($inicial=='no' AND $operacion=='mostrarAgrupaciones' )
{
	echo "<table align = 'center' >";
	// $q = "SELECT DISTINCT(Cconom),Ccocod"
		// ."  FROM costosyp_000005 ";

	// $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//-----------------------------
	
	
	$porperiodo = consultarAliasPorAplicacion($conex, $wemp_pmla, 'buscarporperiodo');
	
	
	
	$q=  "SELECT Perano, Perper "
		."  FROM ".$wbasedato."_000009 "
		." WHERE Perfor = '".$wtemareportes."' ";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	$fechaactual= date('Y-m-d');
	$wfecha_i = $fechaactual;
	$wfecha_f = $fechaactual;
	
	if($porperiodo == $wbasedato)
	{
		echo "<tr align='left'>";
		echo "<td class='fila1'>Periodo</td>";
		echo "<td class='fila1' ><div id='selectperiodo' ><Select id='selectperiodos'>";
		echo "<option value='nada||nada'> Seleccione </option>";
		while($row = mysql_fetch_array($res))
		{
			echo "<option value='".$row['Perano']."||".$row['Perper']."'>".$row['Perano']."-".$row['Perper']."</option>";
		}
		echo "</select></div></td>";
		echo"<td class='fila1' colspan='2' align='center'>";
		echo"<input type='button' value='Consultar' onclick=verReporte() >";
		echo"</td>";
		echo"</tr>";
		echo "</table>";
	
	}
	else
	{
		echo "<tr align='left'>";
		echo "<td class='fila1' align='center'>Fecha Inicial: ";
		campofechaDefecto("wfecha_i",$wfecha_i);
		echo "</td>";
		echo "<td align='center' class='fila1'>Fecha final: ";
		campofechaDefecto("wfecha_f",$wfecha_f);
		echo "</td>";
		echo "</tr>";
		echo"<tr align='left'>";
		echo"<td class='fila1' colspan='2' align='center'>";
		echo"<input type='button' value='Consultar' onclick=verReporte() >";
		echo"</td>";
		echo"</tr>";
		echo "</table>";
	}
	

	
		
	return;
}

if ($inicial=='no' AND $operacion=='mostrarfilas')
{
// si son encuestas	
if ($wtiporeportes == 03)
{
	$q = "		SELECT Encenc,Cconom,encfce ,Ccocod"
		."		  FROM  ".$wbasedato."_000049 , costosyp_000005 "
		."		 WHERE Encenc = '".$wencuesta."' "
		." 		   AND Enccco = Ccocod "
		." 		   AND Encese ='cerrado' "
		."         AND encfce BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'  "
		."         AND Ccoemp = '".$wemp_pmla."'  "
		."    GROUP BY Enccco";
	
	$res = mysql_query($q,$conex) or die ("Error 5: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	$vec_ccoxnumencuestas = array();
	
	while($row = mysql_fetch_array($res))
	{
		 $vectorencuesta[]=$row['Ccocod'];//Vector con codigo de encuestas
		 $vectornomencuestas[]=$row['Cconom'];//Vector con nombre de encuestas
	}

	$q = 	 "   SELECT  encfce,Encenc ,Count(Enchis) as cuantos,Enccco"
			."     FROM  ".$wbasedato."_000049"
			."    WHERE  Encese ='cerrado' "
			."      AND  Encenc = '".$wencuesta."' "
			."      AND  encfce BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'  "
			." GROUP BY encfce,Enccco";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
	while($row = mysql_fetch_array($res))
	{
		$vectorencuestasxdia[$row['Enccco']][$row['encfce']] = $row['cuantos'];//numero de encuestas del tipo "x" por dia
	}
	$qcon = $q;

	
		// se guardan las metas------------------------
		$q = " SELECT Metcod,Metfor,Metfin,Metffi,Metval,Metcco"
			."  FROM ".$wbasedato."_000054 "
			." WHERE  Metffi BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'" 
			."   AND  Metfor = '".$wencuesta."' ";
		 
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
		$vectormetas=array();
		$vectormetasfechafin=array();
		while($row =mysql_fetch_array($res))
		{
			$vectormetas[$row['Metcco']][$row['Metffi']] = '<a style="cursor:pointer" onclick="mostrarmeta(this)"><img src="../../images/medical/root/grabar1.png" border="0" /></a><table style="display: none"><tr><td align=center class=encabezadoTabla>Meta </td><td align=center class=encabezadoTabla>'.$row['Metval'].'</td></tr><tr><td align=center class=encabezadoTabla>Fecha inicial</td><td class=encabezadoTabla >'.$row['Metfin'].'</td></tr><tr><td align=center class=encabezadoTabla>Fecha final</td><td  class=encabezadoTabla >'.$row['Metffi'].'</td></tr><tr><td class=encabezadoTabla>E. Realizadas</td>';
			$vectormetasfechafin[$row['Metcco']][$row['Metffi']] = $row['Metfin'] ;
			$vectormetasvalor[$row['Metcco']][$row['Metffi']]	=  $row['Metval'] ;
						
			
		}

		
		$diasTotalesInforme = calcularDiferenciaDias($fechainicial1,$fechafinal1);
		$k=0;
		while($k < count($vectorencuesta))
		{		
			$encabezado1="<td class='fila2'  style='cursor:pointer'>".$vectornomencuestas[$k]."</td>";
			$cuantasxevaluacionxcco = 0;
			$valormeta='';
			
			for($i = 1; $i<=$diasTotalesInforme + 1; $i++)
			{
			   $j = $i-1;
			   $dia = strtotime ( "+{$j} day" , strtotime ( $fechainicial1 ) ) ;
			   $nuevafecha = date ( 'Y-m-d' , $dia );
			  
			   // si hay meta fijada se concatena en el td
			   //$icono='';
			  
			   if($vectormetas[$vectorencuesta[$k]][$nuevafecha]!='')
			   {
				
				// $valormeta= $vectormetasvalor[$vectorencuesta[$k]][$nuevafecha];
				$valormeta.= $vectormetas[$vectorencuesta[$k]][$nuevafecha];
				//$icono=$vectormetas[$vectorencuesta[$k]][$nuevafecha];
		
				$diasTotales= calcularDiferenciaDias($vectormetasfechafin[$vectorencuesta[$k]][$nuevafecha],$nuevafecha);
				$cuantasencuestas = 0;
				for($l = 1; $l<=$diasTotales+1; $l++)
				{
					 $n = $l-1;
					 $dia2 = strtotime ( "+{$n} day" , strtotime ( $vectormetasfechafin[$vectorencuesta[$k]][$nuevafecha] ) ) ;
					 $nuevafecha2 = date ( 'Y-m-d' , $dia2 );
					 $cuantasencuestas = $cuantasencuestas + $vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha2];
				}
				
				
				$percent = (( Round((( $cuantasencuestas / ($vectormetasvalor[$vectorencuesta[$k]][$nuevafecha]) ) *( 100)),2)) > 100 ) ? 100 : ( Round((( $cuantasencuestas / ($vectormetasvalor[$vectorencuesta[$k]][$nuevafecha]) ) *( 100)),2));
				//$icono .='<td  class=encabezadoTabla>'.$cuantasencuestas.'</td></tr><tr><td class=encabezadoTabla>Cumplimiento</td><td class = encabezadoTabla>'.$percent.'%</td></table>';
			    if($vectormetasvalor[$vectorencuesta[$k]][$nuevafecha] > $cuantasencuestas )
				{
					$valormeta = str_replace("grabar1.png","borrar.png",$valormeta);
				}
				else
				{
				   $valormeta =   str_replace("grabar1.png","grabar.png",$valormeta);
				}
				$valormeta.='<td  class=encabezadoTabla>'.$cuantasencuestas.'</td></tr><tr><td class=encabezadoTabla>Cumplimiento</td><td class = encabezadoTabla>'.$percent.'%</td></table><br>';
			   }
			  
			  
			   
			  // ------------ 
			   $tablaencuestados='';
			   if($vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha]!='')
			   {
					 $q2=" SELECT  Encno1,Encno2,Encap1,Encap2,Enchis,encfce "
						."  FROM  ".$wbasedato."_000049"
						." WHERE  Encese ='cerrado' "
						."   AND  encfce = '".$nuevafecha."' "
						."   AND  Enccco = '".$vectorencuesta[$k]."' "
						."   AND  Encenc = '".$wencuesta."' " ; 
						
					$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
					
					$tablaencuestados='<div id="nose" style="display:none"><table><tr class=encabezadoTabla><td>Nombre</td><td>Historia</td><td>Fecha</td></tr>';
					while($row2 =mysql_fetch_array($res2))
					{
						$tablaencuestados .='<tr class=fila1><td >'.$row2['Encno1'].' '.$row2['Encno2'].' '.$row2['Encap1'].' '.$row2['Encap2'].'</td><td>'.$row2['Enchis'].'</td><td>'.$row2['encfce'].'</td></tr>';
					
					}
					$tablaencuestados .='</table></div>';
				}
			   			  
			   $encabezado1 .= "<td style='cursor:pointer' class='fila2 msg_tooltip-".$wencuesta."' align='center' nowrap='nowrap'  ><a onclick='mostrarencuestados(this);' >".$vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha]." </a>".$tablaencuestados."</td>";
			   $cuantasxevaluacionxcco = $cuantasxevaluacionxcco +  $vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha];
			}
			$encabezado1 .= "<td nowrap='nowrap' align= 'center' class='fila2'>".$valormeta."</td><td class='fila2' align= 'center' >".$cuantasxevaluacionxcco."</td>";
			$encabezado1 .="</tr>";
			$encabezadovector[$k] = $encabezado1;
			$encabezado1 = '';
 			$k++;
		}	
	
		$k=0;
		while($k < count($vectorencuesta))
		{
			echo "<tr class = 'xxx".$wencuesta."' >";
			echo $encabezadovector[$k];
			echo "</tr>";
			$k++;
		}
	
	
}
if ($wtiporeportes == 01 or $wtiporeportes == 04 )
{
	$q = "		SELECT Mcafor,Cconom,".$wbasedato."_000032.Fecha_data ,Ccocod"
		."		  FROM  ".$wbasedato."_000032 , talhuma_000013 ,costosyp_000005 "
		."		 WHERE Mcafor = '".$wencuesta."' "
		."   	   AND Ideuse = Mcauco "
		." 		   AND Idecco = Ccocod "
		."         AND Ccoemp = '".$wemp_pmla."' " ;
	
		
		if($wano =='nada' && $wperiodo=='nada')
			$q = $q	." AND Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'  GROUP BY  Ccocod";

		if($wano !='nada' && $wperiodo!='nada')	
			 $q = $q." AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."'  GROUP BY Ccocod";
			 

	
	$res = mysql_query($q,$conex) or die ("Error 5: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	$vec_ccoxnumencuestas = array();
	
	while($row = mysql_fetch_array($res))
	{
		 $vectorencuesta[]=$row['Ccocod'];//Vector con codigo de encuestas
		 $vectornomencuestas[]=$row['Cconom'];//Vector con nombre de encuestas
	}
	
		$k=0;
		while($k < count($vectorencuesta))
		{		
			$encabezado1="<td align='Left' class='fila2'  style='cursor:pointer' onclick='mostrarencuestados(\"amostrarencuestados\")'>".$vectornomencuestas[$k]." </td>";
			$cuantasxevaluacionxcco = 0;
			$valormeta='';
			$cuantasencuestas = 0;
				
			  // ------------ 
			   $tablaencuestados='';
			   
					
				 $q2="  SELECT  Ideno1,Ideno2,Ideap1,Ideap2,Mcauco,".$wbasedato."_000032.Fecha_data,Tottot  "
					."    FROM  ".$wbasedato."_000032, talhuma_000013,costosyp_000005,".$wbasedato."_000035"
					."   WHERE  Ideuse = Mcauco "
					." 	   AND  Idecco = Ccocod "
					."     AND  Idecco = '".$vectorencuesta[$k]."' "
					."     AND  Mcafor = '".$wencuesta."' " 
					."     AND  Totcdr = Mcaucr  "
					."     AND  Totcdo = Mcauco " 
					."     AND  Totano=  Mcaano "
					."     AND  Totper=  Mcaper "
					."     AND  Totcod=  Mcafor  "
					."     AND  Ccoemp = '".$wemp_pmla."' " ;


				if($wano =='nada' && $wperiodo=='nada')
					$q2 = $q2	." AND Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'";
				else	
					 $q2 = $q2." AND Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."'";
			 
				$q2 .= "ORDER BY Tottot";

				
					$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
					
					$tablaencuestados='<div id="nose" style="display:none"><table><tr class=encabezadoTabla><td>Nombre</td><td>Codigo</td><td>Fecha</td><td>Calificacion</td></tr>';
					while($row2 =mysql_fetch_array($res2))
					{
						$cuantasxevaluacionxcco = $cuantasxevaluacionxcco  + 1;
						$tablaencuestados .='<tr class=fila1 align="Left"><td>'.$row2['Ideno1'].' '.$row2['Ideno2'].' '.$row2['Ideap1'].' '.$row2['Ideap2'].'</td><td>'.$row2['Mcauco'].'</td><td>'.$row2['Fecha_data'].'</td><td align="center">'.$row2['Tottot'].'</td></tr>';
					
					}
					$tablaencuestados .='</table></div>';
				
			
			
			$encabezado1 .= "<td class='fila2' align= 'center' ><a id='amostrarencuestados' style='cursor:pointer'  onclick ='mostrarencuestados(this)' >".$cuantasxevaluacionxcco."</a> ".$tablaencuestados."</td>";
			$encabezado1 .="</tr>";
			$encabezadovector[$k] = $encabezado1;
			$encabezado1 = '';
 			$k++;
		}	
	
		$k=0;
		while($k < count($vectorencuesta))
		{
			
			echo "<tr class = 'xxx".$wencuesta."' >";
			echo $encabezadovector[$k];
			echo "</tr>";
			$k++;
		}
	
}
return;
}

if ($inicial=='no' AND $operacion=='traeResultadosReporte')
{

	$q = "SELECT  ".$wbasedato."_000002.Forcod , ".$wbasedato."_000002.Fordes "
				."  FROM  ".$wbasedato."_000002, ".$wbasedato."_000042  "
				." WHERE  ".$wbasedato."_000042.Forcod = ".$wbasedato."_000002.Fortip "
				."   AND   ".$wbasedato."_000042.Fortip = '".$wtiporeporte."'"
				."   AND   ".$wbasedato."_000042.Forcod = '".$wtemareportes."'";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			while($row = mysql_fetch_array($res))
			{
				$vectorencuesta[]=$row['Forcod'];//Vector con codigo de encuestas
				$vectornomencuestas[]=$row['Fordes'];//Vector con nombre de encuestas
			}	
			
	// si es encuesta		
	if ($wtiporeporte == 03)
	{
			
			//-------------------------------------------------------------------	
			//-------------------------------------
			$q = "SELECT  encfce,Encenc ,Count(Enchis) as cuantos"
				."  FROM  ".$wbasedato."_000049"
				." WHERE  Encese ='cerrado' "
				."   AND  encfce BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'  "
				." GROUP BY encfce,Encenc";

			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			
			while($row = mysql_fetch_array($res))
			{
				$vectorencuestasxdia[$row['Encenc']][$row['encfce']] = $row['cuantos'];//numero de encuestas del tipo "x" por dia
			}
			
			//------------------------------------------------------------------------------------	
			// se pinta el encabezado de los dias-------------------------------------------------
			$diasTotalesInforme = calcularDiferenciaDias($fechainicial1,$fechafinal1);
			$encabezado ="<td class='encabezadoTabla'>nombre de la encuesta</td>";
			for($i = 1; $i<=$diasTotalesInforme + 1  ; $i++)
				   {
						   $j = $i-1;
						   $dia = strtotime ( "+{$j} day" , strtotime ( $fechainicial1 ) ) ;
						   $nuevafecha = date ( 'Y-m-d' , $dia );
						   $encabezado .= "<td class='msg_tooltip encabezadoTabla' title='{$nuevafecha}' style='width:30px'>".date ( 'd' , $dia )."</td>";
						   $fechasOrdenadas[$nuevafecha]='s';
				   }
			//--------------------------------------------------------------------------------------	
			// se guardan las metas------------------------
			$q = " SELECT Metcod,Metfor,Metfin,Metffi,Metval"
			 ."  FROM ".$wbasedato."_000054 "
			 ." WHERE  Metffi BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'" ;
			 
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
			$vectormetas=array();
			$vectormetasfechafin=array();
			while($row =mysql_fetch_array($res))
			{
				$vectormetas[$row['Metfor']][$row['Metffi']] = '<a><img src="../../images/medical/root/grabar.png" border="0"  class="msg_tooltip" title="<table><tr><td colspan=2 align=center class=encabezadoTabla>Meta fijada entre</td></tr><tr><td class=encabezadoTabla >'.$row['Metfin'].'</td><td class=encabezadoTabla >'.$row['Metffi'].'</td></tr><tr><td class=fila1>Meta</td><td class=fila1>'.$row['Metval'].'</td></tr><tr><td class=fila1>Encuestas Realizadas</td>';
				$vectormetasfechafin[$row['Metfor']][$row['Metffi']] = $row['Metfin'] ; 
			}
			//------------------------------------------------	
			// se guardan en el vector numero de encuestar realizadas por dia-----------------------
			$k=0;
			while($k < count($vectorencuesta))
			{		
				$encabezado1="<td class='encabezadoTabla' onclick='abrirDetalleEncuesta(\"".$vectorencuesta[$k]."\")' style='cursor:pointer'>".$vectornomencuestas[$k]."</td>";
				$cantidadtotalxevaluacion = 0;
				for($i = 1; $i<=$diasTotalesInforme + 1; $i++)
				{
				   $j = $i-1;
				   $dia = strtotime ( "+{$j} day" , strtotime ( $fechainicial1 ) ) ;
				   $nuevafecha = date ( 'Y-m-d' , $dia );
				  
				 
				  //------------ 
				   $tablaencuestados='';
				   if($vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha]!='')
				   {
						 $q2=" SELECT  Encno1,Encno2,Encap1,Encap2,Enchis,encfce "
							."  FROM  ".$wbasedato."_000049"
							." WHERE  Encese ='cerrado' "
							."   AND  encfce = '".$nuevafecha."' "
							."   AND  Encenc = '".$vectorencuesta[$k]."' "; 
							
						$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
						
						$tablaencuestados='<div id="nose" style="display:none"><table><tr class=encabezadoTabla><td>Nombre</td><td>Historia</td><td>Fecha</td></tr>';
						while($row2 =mysql_fetch_array($res2))
						{
						
							$tablaencuestados .='<tr class=fila1><td>'.$row2['Encno1'].' '.$row2['Encno2'].' '.$row2['Encap1'].' '.$row2['Encap2'].'</td><td>'.$row2['Enchis'].'</td><td>'.$row2['encfce'].'</td></tr>';
						
						}
						$tablaencuestados .='</table></div>';
				   }
							  
				   $encabezado1 .= "<td  style='cursor:pointer' class='fila1 msg_tooltip' align='center' nowrap='nowrap' ><a onclick='mostrarencuestados(this);' >".$vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha]."</a>".$tablaencuestados."</td>";
				   $cantidadtotalxevaluacion =  $cantidadtotalxevaluacion + $vectorencuestasxdia[$vectorencuesta[$k]][$nuevafecha] ;
				}
				$encabezado1 .= "<td align='center' class = 'fila1' >--</td><td align='center' class = 'fila1'  >".$cantidadtotalxevaluacion."</td>";
				$encabezadovector[$k] = $encabezado1;
				$encabezado1 = '';
				$k++;
			}
			//-------------------------------------------			
			// Se pinta la tabla 
			echo "<table id='tablareportes'><tr><td  align='center' class='encabezadoTabla' colspan='".(count($fechasOrdenadas) + 3)."'>Dias</td></tr>";
			echo "<tr>";
			echo $encabezado;
			echo  "<td class='encabezadoTabla'  >Meta</td><td class='encabezadoTabla' >Total</td>";
			echo "</tr>";	
			// Se pintan las encuestas por dia
			$k=0;
			while($k <= count($vectorencuesta))
			{
				echo "<tr id = 'tr_".$vectorencuesta[$k]."' >";
				echo $encabezadovector[$k];
			
				echo "</tr>";
				$k++;
			}
			//----------------------------------------
			 echo"</table>";
			 echo "<br>";
			 echo "<br>";
			 echo "<br>";
			 echo "<table>";
		     echo "<tr><td><input type='button' value='Graficar' onclick='pintarGrafica()'></td></tr>";
		     echo "<tr><td><div id='amchart1' style='width:1500px; height:600px;'></div></td></tr>";
		     echo "</table>";
			 return;
	}
	if ($wtiporeporte == 01 or  $wtiporeporte == 04)// sie es formato de evaluaciones
	{
		$q = "SELECT   Fecha_data,Mcafor ,Count(Mcauco) as cuantos"
				."  FROM  ".$wbasedato."_000032";
				
		if($wano =='nada' && $wperiodo=='nada')
			$q = $q	." WHERE  Fecha_data BETWEEN '".$fechainicial1."' AND '".$fechafinal1."'  GROUP BY Fecha_data,Mcafor";

		if($wano =='nada' && $wperiodo=='nada')	
			 $q = $q	." WHERE  Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."'  GROUP BY Fecha_data,Mcafor";
			 
			 
			$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			
			while($row = mysql_fetch_array($res))
			{
				$vectorencuestasxdia[$row['Mcafor']][$row['Fecha_data']] = $row['cuantos'];//numero de encuestas del tipo "x" por dia
			}
			
		//------------------------------------------------------------------------------------	
		// se pinta el encabezado de los dias-------------------------------------------------
	
			$encabezado ="<td class='encabezadoTabla'>Nombre de la Evaluacion</td>";
		
			//------------------------------------------------	
			// se guardan en el vector numero de encuestar realizadas por dia-----------------------
			$k=0;
			while($k < count($vectorencuesta))
			{		
				$encabezado1="<td class='encabezadoTabla' align='left' onclick='abrirDetalleEncuesta(\"".$vectorencuesta[$k]."\")' style='cursor:pointer'>".$vectornomencuestas[$k]."</td>";
				$cantidadtotalxevaluacion = 0;
				$tablaencuestados='';
				   
				  
				   
						 $q2=" SELECT  Ideno1,Ideno2,Ideap1,Ideap2,Mcauco,".$wbasedato."_000032.Fecha_data "
							."   FROM  ".$wbasedato."_000032, talhuma_000013"
							."   WHERE  Mcaano = '".$wano."' AND Mcaper = '".$wperiodo."' "
							."    AND Ideuse = Mcauco "
							."    AND  Mcafor = '".$vectorencuesta[$k]."' "; 
							
						$res2 = mysql_query($q2,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
						
						$tablaencuestados='<div id="nose" style="display:none"><table ><tr class=encabezadoTabla><td>Nombre</td><td>Codigo</td><td>Fecha</td></tr>';
						$cantidadtotalxevaluacion=0;
						while($row2 =mysql_fetch_array($res2))
						{
							$tablaencuestados .='<tr class=fila1><td>'.$row2['Ideno1'].' '.$row2['Ideno2'].' '.$row2['Ideap1'].' '.$row2['Ideap2'].'</td><td>'.$row2['Mcauco'].'</td><td>'.$row2['Fecha_data'].'</td></tr>';
						    $cantidadtotalxevaluacion = $cantidadtotalxevaluacion  + 1 ;
						}
						$tablaencuestados .='</table></div>';
				   
				
		
				$encabezado1 .= "<td align='center' class = 'fila1' onclick='abrirDetalleEncuesta(\"".$vectorencuesta[$k]."\")' style='cursor:pointer' >".$cantidadtotalxevaluacion." </td>";
				$encabezadovector[$k] = $encabezado1;
				$encabezado1 = '';
				$k++;
			}
			//-------------------------------------------			
			// Se pinta la tabla 
			echo "<table>";
			echo "<tr>";
			echo $encabezado;
			echo "<td class='encabezadoTabla'>Total</td>";
			echo "</tr>";	
			// Se pintan las encuestas por dia
			$k=0;
			while($k <= count($vectorencuesta))
			{
				echo "<tr id = 'tr_".$vectorencuesta[$k]."' >";
				echo $encabezadovector[$k];
				echo "</tr>";
				$k++;
			}
			//----------------------------------------
			echo"</table>";
			return;
	}
	
	
}

?>

<html>
<head>
<title>Reportes de cumplimiento</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>	
<script type='text/javascript'>

function pintarGrafica (tipo)
{
var  wtipografico = 'column';
if (tipo == 'line')
 wtipografico = "line" ;
if (tipo == 'column')
 wtipografico = "column" ;
if (tipo == 'pie')
 wtipografico = "pie" ;

 
$('#tablareportes').LeerTablaAmericas({ 
			
 			empezardesdefila: 2, 
			tipografico: wtipografico, 
			dimension : '3d' , 
			titulo : 'Notas Promedio por Centro de Costos ' ,
			tituloy: 'cantidad',
			filaencabezado : [1,1],
			datosadicionales : 'todo'
						
		});
}
function verAgrupaciones()
{
	if($('#select_tema').val()=='seleccione')
	{
		$('#div_resultados').html('');
	}
	else
	{
		
		var temareportes =  $('#select_tema').val().split("||");  
		var tiporeporte = temareportes[1];
		$.get("../reportes/reporte_cumplimiento_evaluaciones.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarAgrupaciones',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: temareportes[0],
				wtiporeportes	: tiporeporte
				
			}
			, function(data) {
			$('#div_resultados').html(data);
			
			});
			
			
	}
}
// trae los detalles de centro de costos de cada una de las encuestas.
// mira el tr al que se le dio clic y apartir de ahi agrega filas para mostrar los centros de costos donde se hizo la encuesta y trae los resultados
// detallados
function abrirDetalleEncuesta (encuesta)
{
	
	codencuesta = encuesta; 
	encuesta = 'tr_'+encuesta;
	salir = false;
	
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
	
	if (  $('#'+encuesta).siblings().hasClass('xxx'+codencuesta)  ){
		$('#'+encuesta).siblings('.xxx'+codencuesta).remove();
		salir = true;	
	}
	
	
	if(salir)
		return;
	
	var temareportes =  $('#select_tema').val().split("||");  
	var tiporeporte = temareportes[1];
	
	$.get("../reportes/reporte_cumplimiento_evaluaciones.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'mostrarfilas',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wencuesta		: codencuesta,
				fechainicial1 	:$('#wfecha_i').val(),
				fechafinal1		:$('#wfecha_f').val(),
				wtemareportes	: temareportes[0],
				wtiporeportes	: tiporeporte,
				wperiodo		:periodo,
				wano			:ano
				
			}
			, function(data) {
				
				$(data).insertAfter( $('#'+encuesta) );
				$.unblockUI();
				$(".msg_tooltip-"+codencuesta).tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
				//$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			});
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
$.blockUI({ message:'<img src="../../images/medical/ajax-loader.gif" >',
            css:    {
					   width:  'auto',
					   height: 'auto'
                    }
          });
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
	var sugerencia;
	var queja;
	var felicitacion;
	var ayudas;
	var ano;
	var periodo;
	var fechainicial2;
	var fechafinal2;
	var porcco;
	var porpregunta;
	var poragrupacion ='si';
	
	if ($('#selectperiodos').val()==undefined)
	{
	 ano = 'nada';
	 periodo = 'nada';
	}
	else
	{
		if($('#selectperiodos').val()=="nada||nada")
		{
		 alert("Debe seleccionar algun Periodo");
		 return;
		}
		var wanoperiodo = $('#selectperiodos').val().split("||");
		ano = wanoperiodo[0];
		periodo = wanoperiodo[1];
	}
	
	
	var temareportes =  $('#select_tema').val().split("||");  
	var tiporeporte = temareportes[1];
	
	esperar (  );
	$.get("../reportes/reporte_cumplimiento_evaluaciones.php",
			{
				consultaAjax 	: '',
				inicial			: 'no',
				operacion		: 'traeResultadosReporte',
				wemp_pmla		: $('#wemp_pmla').val(),
				wtema           : $('#wtema').val(),
				wuse			: $('#wuse').val(),
				wtemareportes	: temareportes[0],
				poragrupacion	: poragrupacion,
				fechainicial1 	:$('#wfecha_i').val(),
				fechafinal1		:$('#wfecha_f').val(),
				wtiporeporte	:tiporeporte,
				wperiodo		:periodo,
				wano			:ano
			}
			, function(data) {
			$('#div_contenido_reporte').html(data);
			$(".msg_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			$(".msg1_tooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: -50 });
			$.unblockUI();
			});
}

function muestraMetas(codigo,dia)
{
	var celda = 'div_meta';
	fnMostrar2(celda);
}

function mostrarencuestados(elemento)
{
	elemento = jQuery(elemento);
	if ( elemento.next() != undefined )
		elemento.next().toggle();
}
function mostrarmeta(elemento)
{
	elemento = jQuery(elemento);
	if ( elemento.next() != undefined )
		elemento.next().toggle();
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
$wactualiz = "(Diciembre 03 de 2012)";
echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';

/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/

//------- campos ocultos
echo "<input type='hidden' name='wtema' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' name='wuse' id='wuse' value='".$wuse."'>";
echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
//----------------------------

// tabla principal
echo"<table id='tabla_principal' width='900' align='center'>";
// primer tr :  Select tema.
echo"<tr><td>";

// datos select 
$q = "	SELECT  Forcod, Fordes,Fortip "
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
	echo"<option value='".$row['Forcod']."||".$row['Fortip']." '>".$row['Fordes']."</option>";
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

// -----div Muestra de resultados-------------------------
echo "<table align='center'>";
echo "<tr>";
echo "<td>";
echo "<div id='div_contenido_reporte'></div>";
echo "<td>";
echo "</tr>";
echo "</table>";

echo "<br>";
// div ocultos

// -------------------------------------------------------------
// echo "<div id='div_reporte' class='fila2' align='middle'  style='display:none;cursor:default;background:none repeat scroll 0 0;height: 400px' >";
echo "<div id='div_meta' class='fila2' align='middle'  style='display:none;height:auto;background:none repeat' >";
echo "<input id='nombreAgrupacionAgregar' type='hidden'>";
echo "<br><br>";
echo "</td>";
// div de detalle de preguntas 
echo "<div id='detallepreguntas'>";
echo "</div>";
echo"<br><br>";
echo"<input type='button' value='Cancelar'  onClick='$.unblockUI();' style='width:100'>";
echo"</div>";

function calcularDiferenciaDias($fecha_inicio, $fecha_fin)//funcion que calcula la diferencia en Dias de dos fechas
{
   $inicio = strtotime($fecha_inicio." 00:00:00");
   $fin = strtotime($fecha_fin." 00:00:00");
   $resultado = $fin*1 - $inicio*1;
   $resultado = gmdate( "z", $resultado);
   return ($resultado);
}
?>
</body>
</html>
