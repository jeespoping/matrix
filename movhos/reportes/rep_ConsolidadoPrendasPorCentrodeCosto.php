<head>
  <title>CONSOLIDADO DE PRENDAS POR CENTRO DE COSTO</title>
</head>
<body>
<script type="text/javascript">
	function enter()
	{
	 document.conpreenvrec.submit();
	}
	
	function cerrarVentana()
	{
      window.close()		  
    }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   * CONSOLIDADO DE PRENDAS POR CENTRO DE COSTO  *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/

	/*
	 ********** DESCRIPCIÓN *****************************************************************
	 * Muestra todos los movimientos generados en la aplicación de roperia a nivel interno 	*
	 * es decir, los movimientos de prendas en los servicios de la clínica.					*
	 ****************************************************************************************
	 * Autor: John M. Cadavid. G.						*
	 * Fecha creacion: 2011-06-14						*
	 * 													*
	 * Modificaciones 									*
	 ************************************************************************************************************************
	 * 2013-02-26 - Se agregó TRIM(movcco) para los JOIN que se hacen del campo movcco de la tabla 000105 de movimiento hospitalario
	 *              ya que este campo tiene registros con un espacio en blanco al final y no se estaban visualizando en el reporte
	 *              que se filtra por centro de costo
	 *				También se incluyeron los totales para el reporte detallado - Mario Cadavid
	 ************************************************************************************************************************
	 * 2011-12-29 - Se agrega la funcion imprimir_centrodecostoresumido para permitir que se muestren los consolidados de prendas
	 *              por centro de costos, se elimina el boton consultar y se activa el javascript enter() en los radio button resumindo 
	 *              y detallado, cada seleccion hace la diferencia entre los informes - Jonatan Lopez Aguirre
	 ************************************************************************************************************************
	 * 2011-09-09 - Se modificó el query principal para que tenga en cuenta solo los movimientos de entrega 				*
	 *	        	Se condicionó la consulta si es para todos los centros de costos o para uno solo - Mario Cadavid		*
	 ************************************************************************************************************************
	 */

session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  

  include_once("root/comun.php");
 
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
     
  
 	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz=" Febrero 26 de 2013 ";                      // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
  $q = " SELECT detapl, detval, empdes "
      ."   FROM root_000050, root_000051 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' "
      ."    AND empcod = detemp "; 
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res); 
  
  if ($num > 0 )
     {
	  for ($i=1;$i<=$num;$i++)
	     {   
	      $row = mysql_fetch_array($res);
	      
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
	         
	      if ($row[0] == "invecla")
	         $winvecla=$row[1];    
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  encabezado("CONSOLIDADO DE PRENDAS POR CENTRO DE COSTO",$wactualiz, "clinica");  
       
  
  //==================================================================================================================================================
  //************************************************  E M P I E Z A N   L A S   F U N C I O N E S  ***************************************************
  //==================================================================================================================================================
  function imprimir_centrodecosto($warrPrendas, $cconum, $cconom)
    {
         global $conex;
		 global $wbasedato;
		 global $wcco;
		 global $wtipo;
         global $totalpeso;
         global $totalcantidad;
		 global $totalpesoindividual;

		 //print_r(array_keys($warrPrendas));
		 $pos_str = explode(" - ",$wcco);
					
		 $warr_movprendas=array_keys($warrPrendas);    //Trae todas las claves de un arreglo asociativo
		 $wcont=count($warrPrendas);                   //warrPrendas: En esta matriz estan todas las prendas que tuvieron movimiento

		 echo "<center><table>";		 
		 echo "<tr class=encabezadoTabla>";
		 echo "<th>Código Centro de costos</th>";
		 echo "<th>Nombre Centro de costos</th>";
		 echo "<th>Código</th>";
		 echo "<th>Descripción</th>";
		 echo "<th>Cantidad Suministrada</th>";
		 echo "<th>Peso Individual<br>de la Prenda</th>";
		 echo "<th>Peso Total</th>";
			
		 $wtot_mvto=0;
		 $wtot_pesind=0;
		 $wtot_pestot=0;
			 
		 for ($i=0;$i<$wcont;$i++)
			{
			 if (isset($wclass) and $wclass=="fila1")
				$wclass="fila2";
			   else
				  $wclass="fila1";
					
			 echo "<tr class='".$wclass."'>";
			 echo "<td align=center>".$cconum."</td>";                 //Codigo Centro de costos
			 echo "<td align=left>".$cconom."</td>";                   //Nombre Centro de costos
			 if (isset($warrPrendas[$warr_movprendas[$i]][0])) echo "<td align=center>".$warrPrendas[$warr_movprendas[$i]][0]."</td>";                   //Codigo prenda
			 if (isset($warrPrendas[$warr_movprendas[$i]][1])) echo "<td align=left  >".$warrPrendas[$warr_movprendas[$i]][1]."</td>";                   //Descripcion prenda
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) echo "<td align=center>".$warrPrendas[$warr_movprendas[$i]][2]."</td>";                   //Cantidad Movimiento
			 if (isset($warrPrendas[$warr_movprendas[$i]][3])) echo "<td align=right>".number_format($warrPrendas[$warr_movprendas[$i]][3],2,'.',',')." Kg</td>";                //Peso Individual
			 echo "<td align=right>".number_format(($warrPrendas[$warr_movprendas[$i]][2]*$warrPrendas[$warr_movprendas[$i]][3]),2,'.',',')." Kg</td>"; //Peso Total
			 echo "</tr>";
				
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) $wtot_mvto  =$wtot_mvto  +$warrPrendas[$warr_movprendas[$i]][2];
			 if (isset($warrPrendas[$warr_movprendas[$i]][3])) $wtot_pesind=$wtot_pesind+$warrPrendas[$warr_movprendas[$i]][3];
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) $wtot_pestot=$wtot_pestot+($warrPrendas[$warr_movprendas[$i]][2]*$warrPrendas[$warr_movprendas[$i]][3]);
			}

			echo "<tr class='encabezadoTabla'>";
			echo "<td align='center' colspan='4'> <b> Sub Total </b> </td>";                 //Codigo Centro de costos
			echo "<td align=center>".number_format($wtot_mvto,0,'.',',')."</td>";                   //Cantidad Movimiento
			echo "<td align=right>".number_format($wtot_pesind,2,'.',',')." Kg</td>";                //Peso Individual
			echo "<td align=right>".number_format($wtot_pestot,2,'.',',')." Kg</td>"; //Peso Total
			echo "</tr>";
			echo "<tr>";         
			echo "<th colspan=7> &nbsp; </th>";
			echo "</tr>";
			
         $totalpeso += $wtot_pestot;
         $totalcantidad += $wtot_mvto;
		 $totalpesoindividual += $wtot_pesind;
	}
      
      // Se agrega esta funcion para mostrar el informe resumido segun el centro de costos seleccionado (Jonatan Lopez)  
   function imprimir_centrodecostoresumido($warrPrendas,$cconum, $cconom)
    {
         global $conex;
		 global $wbasedato;
		 global $wcco;
         global $wtipo;
         global $cont_class;
         global $totalpeso;
         global $totalcantidad;
		 //print_r(array_keys($warrPrendas));
		 $pos_str = explode(" - ",$wcco);
					
		 $warr_movprendas=array_keys($warrPrendas);    //Trae todas las claves de un arreglo asociativo
		 $wcont=count($warrPrendas);                   //warrPrendas: En esta matriz estan todas las prendas que tuvieron movimiento
				
		 $wtot_mvto  =0;
		 $wtot_pesind=0;
		 $wtot_pestot=0;
	 
         for ($i=0;$i<$wcont;$i++)
		 {           
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) $wtot_mvto  =$wtot_mvto  +$warrPrendas[$warr_movprendas[$i]][2];
			 if (isset($warrPrendas[$warr_movprendas[$i]][3])) $wtot_pesind=$wtot_pesind+$warrPrendas[$warr_movprendas[$i]][3];
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) $wtot_pestot=$wtot_pestot+($warrPrendas[$warr_movprendas[$i]][2]*$warrPrendas[$warr_movprendas[$i]][3]);
		 }
         
         if (is_integer($cont_class / 2))
            $wclass = "fila1";
         else
            $wclass = "fila2";
        
		 echo "<tr class='encabezadoTabla'>";
		 echo "<td align=center>".$cconum."</td>";                   //Codigo Centro de costos
         echo "<td align=left>".$cconom."</td>";                   //Nombre Centro de costos
         echo "<td align=right>".number_format($wtot_mvto,0,'.',',')."</td>";
		 echo "<td align=right>".number_format($wtot_pestot,2,'.',',')." Kg</td>";
         echo "</tr>";  
         $totalpeso += $wtot_pestot;
         $totalcantidad += $wtot_mvto;
         
         $cont_class++;
	}
        
       
  //===============================================================================================================================================
  //*************************************************  T E R M I N A N   L A S   F U N C I O N E S  ***********************************************
  //===============================================================================================================================================
  
    
  //FORMA ================================================================
  echo "<form name='conpreenvrec' action='rep_ConsolidadoPrendasPorCentrodeCosto.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wfec_i) or trim($wfec_i) == "" or !isset($wfec_f) or trim($wfec_f) == "" or !isset($wcco) or trim($wcco) == "" )
     {     
	  //echo '<h2 class="seccion1" colspan=1><b>SELECCIONE CENTRO DE COSTO:</b></h2>';

	  $wfecha = date("Y-m-d");
	  
	    //Parámetros de consulta del informe
  		if (!isset ($wfi))
 			$wfi=$wfecha;
  		if (!isset ($wff))
 			$wff=$wfecha;
  		if (!isset ($wc))
 			$wc="";
	  
	  //Seleccionar CENTRO DE COSTOS
	  echo "<center><table cellspacing=2 cellpadding=2>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
      //    ."    AND ".$wbasedato."_000011.ccohos = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=4> &nbsp; Seleccione la Unidad :  &nbsp; </font></td>";
	  echo "<td align=center><select name='wcco'>";
	  if (isset($wcco)) 
	     echo "<option selected>".$wcco."</option>";
	  //  else 
	  //   echo "<option>&nbsp</option>";
      echo "<option>% - Todos</option>";		 
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
		  if($wc != $row[0]." - ".$row[1])
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		  else
			echo "<option selected>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
	  
      echo "<br>";
      echo "<center><table cellspacing=2 cellpadding=2 width='310'>";
      echo "<tr class=seccion1>";
      echo "<td align=center><b>Fecha Inicial</b><br>";
            campoFechaDefecto("wfec_i", $wfi);
      echo "</td>";
      echo "<td align=center><b>Fecha Final</b><br>";
            campoFechaDefecto("wfec_f", $wff);
      echo "</td>";
      echo "</tr>";
      echo "</table>";
      echo "<br>";
      echo "<table style='text-align: left; width: 237px; height: 33px;' border='0' cellpadding='2' cellspacing='2'>
            <tbody>
            <tr>
            <td><input name='wtipo' value='resumido' type='radio' onclick='enter()'></td>
            <td>Resumido</td>
            <td><input name='wtipo' value='detallado' type='radio' onClick = 'enter()'></td>
            <td>Detallado</td>
            </tr>
            </tbody>
            </table>";
      echo "<br>"; 
      echo "<center><table cellspacing=1 width='310'>";	    
      echo "<tr><td align=center colspan=2></b><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr></center>";
      echo "</table>";
     }
    else 
       {
	    $wcco1=explode(" - ",$wcco);
                     
	   
		// 2011-09-09 - Se modificó el query principal para que tenga en cuenta solo los movimientos de entrega (tcofac = 1)
		//También se condicionó la consulta si es para todos los centros de costos o para uno solo
                $q = " SELECT movcco, cconom, movpre, predes, SUM(movcan), prepes "
				."   FROM ".$wbasedato."_000105, ".$wbasedato."_000011, ".$wbasedato."_000103, ".$wbasedato."_000102, ".$wbasedato."_000101, ".$wbasedato."_000100 "
				."  WHERE TRIM(movcco)  LIKE '".trim($wcco1[0])."'"
				."    AND movfec  BETWEEN '".$wfec_i."' AND '".$wfec_f."' "
				."    AND movcco  = ccocod "
				."    AND movpre  = precod "
				."    AND movron  = roncod "
				."    AND roncon  = concod "
				."    AND contco  = tcocod "
				."    AND tcofac  = 1 "
				."  GROUP BY 1, 3 ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $num = mysql_num_rows($res);

		if ($num > 0)
		 {
		    $i=0;
			$row = mysql_fetch_array($res);
			
                        mysql_data_seek ($res,0);
			
			echo "<center><table>";
			echo "<tr class=titulo>";
			echo "<th align=center> &nbsp; <b>Fecha Inicial</b> &nbsp; </th>";
			echo "<th align=center bgcolor='#ffffff'><font size=4>&nbsp</font></th>";
			echo "<th align=center> &nbsp; <b>Fecha Final</b> &nbsp; </th>";
			echo "</tr>";

			echo "<tr class=fila2>";
			echo "<td align=center> &nbsp; <b>".$wfec_i."</b> &nbsp; </td>";
			echo "<td align=center bgcolor='#ffffff'>&nbsp</td>";
			echo "<td align=center> &nbsp; <b>".$wfec_f."</b> &nbsp; </td>";
			echo "</tr>";
			echo "</table></center>";
	
			if($wtipo == 'resumido')
			{
				 echo "<center><table>";		 
				 echo "<tr class=encabezadoTabla>";
				 echo "<th>Código Centro de costos</th>";
				 echo "<th>Nombre Centro de costos</th>";
				 echo "<th>Cantidad Suministrada</th>";	 
				 echo "<th>Peso Total</th></tr>";
			}
				 
			$cont_class = 0;
			$totalpeso = 0;
			$totalcantidad = 0;
			$totalpesoindividual = 0;
			while ($i <= $num)
			    {
				 $cconum = $row[0];
				 $cconom= $row[1];
                                 
				 $warrPrendas = array(); 
				 $wccoaux=$row[0];
				 while ($i <= $num and $wccoaux == $row[0])
				    {
					
                                         $warrPrendas[$row[2]][0]=$row[2];    //Codigo prenda
					 $warrPrendas[$row[2]][1]=$row[3];    //Descripcion prenda
					 $warrPrendas[$row[2]][2]=$row[4];    //Cantidad de prendas
					 $warrPrendas[$row[2]][3]=$row[5];    //Peso de cada prenda
						
					 $row = mysql_fetch_array($res); 
					 $i++;
				    }
					// Se agrega esta validación relacionda con el radio button, dependiendo de valor se muestra el informe (Jonatan Lopez) 
					if ($wtipo == 'detallado')
					 {
						imprimir_CentrodeCosto($warrPrendas,$cconum, $cconom);
					 }
					if ($wtipo == 'resumido')
					 {
						imprimir_CentrodeCostoresumido($warrPrendas,$cconum, $cconom);
					 }
                                  
                }
				if($wtipo == 'resumido')
				  {
					 echo "<tr>";         
					 echo "<th colspan=2 class='fila1'>Total</th>";
					 echo "<th class='fila1' align = right>".number_format($totalcantidad)."</th>";	 
					 echo "<th class='fila1' align = right>".number_format($totalpeso,2,'.',',')." Kg</th>";                                   
					 echo "</tr>";
					 echo "</tr>";
					 echo "</table></center>";
					 echo "<br><br>";                         
				  } 
				else
				  {
					 echo "<tr>";         
					 echo "<th colspan=7> &nbsp; </th>";
					 echo "</tr>";
					 echo "<tr class='encabezadoTabla'>";         
					 echo "<th colspan=4> TOTAL </th>";
					 echo "<th align = right>".number_format($totalcantidad)."</th>";	 
					 echo "<th align = right>".number_format($totalpesoindividual,2,'.',',')." Kg</th>";	 
					 echo "<th align = right>".number_format($totalpeso,2,'.',',')." Kg</th>";                                   
					 echo "</tr>";
					 echo "</tr>";
					 echo "</table></center>";
					 echo "<br><br>";                         
				  } 

				echo "</table></center>";
				echo "<br><br>";                         
                          
		 }
	   }
    
	echo "</form>";
	  
	if(isset($wfec_f) && isset($wfec_i) && isset($wcco))
	{
		echo "<center><table width='310'>"; 
		echo "<tr>";  
		echo "<td align=center><a href='rep_ConsolidadoPrendasPorCentrodeCosto.php?wemp_pmla=".$wemp_pmla."&wfi=".$wfec_i."&wff=".$wfec_f."&wc=".$wcco."'><b>Retornar</b></a></td></tr><tr><td align=center><br><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td>"; 
		echo "</tr>";
		echo "</table>";
	}
    
} // if de register

include_once("free.php");

?>