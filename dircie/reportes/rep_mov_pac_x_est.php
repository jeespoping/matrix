<?php
include_once("conex.php");
/******************************************************
*   REPORTE DE MOVIMIENTOS DE PACIENTES POR ESTUDIO  *
******************************************************/
/*
 **********     DESCRIPCIÓN     *********************************************************************
 * Muestra una cuadrícula con la lista de conceptos (filas) y frecuencias (columnas) del estudio 	  *
 * de modo que se pueda VISUALIZAR  la cantidad de frecuencias por concepto y paciente 				      *
 ****************************************************************************************************
 * Autor: John M. Cadavid. G.						*
 * Fecha creacion: 2011-12-27						*
 * ****************************************         Modificaciones			 	*****************************************
 * 2017-03-21 - Arleyda Insignares C. -Se actualiza interfaz gráfica, y se coloca acordeón por cada paciente y de *
 *              esta forma poder cargar varios pacientes en la misma consulta.                                 *
 *                                                                                                                *
 * 2012-01-17 - Se adicionó filtro para visualizar datos por paciente. Se incluyó la visualización de la fecha de *
 * generación del reporte y se modificó la visualización de los encabezados de las columnas. Mario Cadavid			  *
 ******************************************************************************************************************
*/
 
 $wactualiz = "2016-03-21";
 if(!isset($_SESSION['user'])){
   echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
    <tr><td>Error, inicie nuevamente</td></tr>
    </table></center>";
   return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //********************************** Inicio  ***********************************************************
  
  

  include_once("root/comun.php");
  
  $wbasedato  = consultarAliasPorAplicacion($conex, $wemp_pmla, "dircie");
  $wfecha     = date("Y-m-d");
  $whora      = (string)date("H:i:s");
  $pos        = strpos($user,"-");
  $wusuario   = substr($user,$pos+1,strlen($user));


  // **************************************  FUNCIONES PHP  ************************************************


  if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){

        $data='';

		    // Consulta de datos del estudio
        $qest =  " 	SELECT Estcod, Estnom, Cestmo 
        				    FROM ".$wbasedato."_000002, ".$wbasedato."_000004 
        				   WHERE Cescoe = '".$westudio."' 
          				     AND Cescol = '".$wlaboratorio."' 
          				     AND Cescoe = Estcod 
          				     AND Estest = 'on' 
          				     AND Cesest = 'on' ";

        $res_est = mysql_query($qest,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qest." - ".mysql_error());
		    $row_est = mysql_fetch_array($res_est);
		
		    
        // Consulta de conceptos
        $qcon = "  SELECT IF(Lecval=0,Conval,Lecval) as Concosto, Concod, Condes, Contip 
        				      FROM ".$wbasedato."_000003, ".$wbasedato."_000005 
        				   WHERE Leccol = '".$wlaboratorio."' 
          				     AND Leccoe = '".$westudio."' 
          				     AND Leccoc = Concod 
          				     AND Conest = 'on' 
          				     AND Lecest = 'on' 
        				   ORDER BY Concod, Condes ";

        $res_con = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
        $numcon = mysql_num_rows($res_con);
		
    		
        // Consulta de frecuencias
    		$q = " 	SELECT Lefvfr, Leftvi, Lefper, Lefnum, Tvides 
          			    FROM ".$wbasedato."_000006 A
          			    LEFT JOIN ".$wbasedato."_000013 B 
          			       on A.Leftvi = B.Tvicod
      			    WHERE   Lefcol = '".$wlaboratorio."' 
          			    AND Lefcoe = '".$westudio."' 
          			    AND Lefest = 'on' 
      				  ORDER BY Lefvfr ";
                   	
    		$res_fre = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    		$numfre  = mysql_num_rows($res_fre);

    		
        // Arreglo que permitirá llevar los datos de las frecuencias del estudio
    		$frecuencias = array();
        $tiposvisita = array();
    		
    		// Definición del número de columnas de la tabla
    		$numcol = $numfre+4;

    		// Definición de la variable que mostrará el costo total del estudio
    		$total_estudio = 0;
    		
    		if (isset($wpaciente) && $wpaciente!='')
    		{
      			$paciente = explode("-",$wpaciente);
      			$pactid = $paciente[0];
      			$pacced = $paciente[1];
      			
      			// Consulta de paciente seleccionado
      			 
      			$q = "SELECT Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2
        					   FROM ".$wbasedato."_000008, ".$wbasedato."_000011
        				  WHERE Mopcol = '".$wlaboratorio."' 
            					 AND Mopcoe = '".$westudio."' 
            					 AND Pacced = '".$pacced."'
            					 AND Pactid = '".$pactid."'					 
            					 AND Mopide = Pacced 
            					 AND Moptid = Pactid
            					 AND Mopest = 'on'
            					 AND Pacest = 'on'
        			    GROUP BY Pacced,Pactid	
        				  ORDER BY Pacap1, Pacap2, Pacno1, Pacno2";

    		}
    		else
    		{
      			// Consulta de los pacientes del estudio
      			$q = "SELECT Pacced, Pactid, Pacno1, Pacno2, Pacap1, Pacap2
        					   FROM ".$wbasedato."_000008, ".$wbasedato."_000011
        				  WHERE Mopcol = '".$wlaboratorio."'
            					 AND Mopcoe = '".$westudio."' 
            					 AND Mopide = Pacced 
            					 AND Moptid = Pactid
            					 AND Mopest = 'on'
            					 AND Pacest = 'on'
        				 GROUP BY Pacced,Pactid	 
        				 ORDER BY Pacap1, Pacap2, Pacno1, Pacno2";
    		}
    		
    		$res_pac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    		$num_pac = mysql_num_rows($res_pac);
    		
    		$fecha = date("d/m/Y");		

    		// Consulta de pacientes vinculados al estudio actual
    		$q =   " SELECT Pactid, Pacced, Pacno1, Pacno2, Pacap1, Pacap2 
    			         FROM ".$wbasedato."_000007, ".$wbasedato."_000011  
    			      WHERE Paecol = '".$wlaboratorio."'  
        			    	AND Paecoe = '".$westudio."'   
        			    	AND Paeide = Pacced  
        			    	AND Paetid = Pactid  
        			    	AND Pacest = 'on'  
        			    	AND Paeest = 'on'  
    			      ORDER BY Pacap1, Pacap2, Pacno1, Pacno2";

        $res_pacs = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num_pacs = mysql_num_rows($res_pacs);
		
		// * * * * * * * * * * * *  Inicio el ciclo de recorrido de los pacientes del estudio	* * * * * * * * 
		
		while($row_pac = mysql_fetch_assoc($res_pac)){ 			
			
			// Arreglo que permitirá llevar los totales
			$totales = array();

			// Variable $data1 contendrá el encabezado de las cuadrículas
			$data1 =  '';
			$data3 =  '';
			$data2 =  "<tr class='encabezadoTabla'>";
			$i     =  0;
			
			// Ciclo para llenar el encabezado de la cuadrícula con las frecuencias
			if  ($numfre>0)
			{
				while($row_fre = mysql_fetch_assoc($res_fre)){
        					$i++;
        					$frecuencias[$i] = $row_fre['Lefvfr'];
                  $tiposvisita[$i] = $row_fre['Leftvi'];
        					$perfre          = $row_fre['Lefper'] == 'M' ?  'M' : "D";
	                $tipovi 		     = $row_fre['Tvides'];
	                $numvis          = $row_fre['Lefnum'];
	                $data1           .= "<td align='center' width='31px'> ".$row_fre['Lefvfr']." </td>";
	                $data3           .= "<td align='center' width='31px'> ".$tipovi."/".$perfre." </td>";
	      }
			}

			for ($j=1;$j<=$numvis;$j++)
      {
          $data2 .= $data1;
      }
      $data2 .= "</tr>";
	

			// Inicializo los indices del arreglo que me van a llevar los totales
			$totales['cantidad']['1'] = 0;
			$totales['costo']['1']    = 0;
			$totales['total']['1']    = 0;
			$numlis =0;
			
			// Consulta de los movimientos realizados para Estudio y Paciente
			$qlis = " SELECT Concod, Condes, Conval, Mopvfr, Moprea, Mopcan, Mopnvi, Moptvi
						 FROM ".$wbasedato."_000003, ".$wbasedato."_000008
					   WHERE Mopcol = '".$wlaboratorio."'
    						 AND Mopcoe = '".$westudio."'
    						 AND Mopide = '".$row_pac['Pacced']."'
    						 AND Moptid = '".$row_pac['Pactid']."' 
    						 AND Mopcoc = Concod
    						 AND Conest = 'on'
    						 AND Mopest = 'on'
					   ORDER BY Concod, Condes, Mopvfr";

			
			$reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
			$numlis = mysql_num_rows($reslis);

      //Llevar a un array la consulta de movimientos por Estudio
      $resultset2 = array();
                    
      while ($row = mysql_fetch_assoc($reslis)) 
             $resultset2[] = $row;
			
			$j = 0;
			
			// Ciclo para imprimir las filas con los datos del informe
			if ($numlis>0){

    				$data .= "<div class='accordionFiltros' align='center'>";

    				$data .= "<h1 style='font-size: 11pt;' align='left'>". $row_pac['Pacap1']." ".$row_pac['Pacap2']." ".$row_pac['Pacno1']." ".$row_pac['Pacno2']." - ".$row_pac['Pactid']." - ".$row_pac['Pacced']. "</h1>";
    				$data .= "<div class='ui-tabs ui-widget ui-widget-content ui-corner-all'>";
    	      $data .= "<table>";       
    				
    				// Espacio entre los botones superiores y las filas con los datos
    				$data .= "<tr><td height='21'></td></tr>";
    				
    				// Encabezado con título de las columnas
    				$data .= "<tr class='encabezadoTabla'>";
    				$data .= "<td align='center' rowspan='2' width='50%'> CONCEPTOS </td>";
    				for ($j=1;$j<=$numvis;$j++)
    	      {
    				    $data .= $data3;
    			  }

    				// Columna donde se mostrará la frecuencia del concepto
    				$data .= "<td align='center' rowspan='2'> Total <br> Eventos </td>";
    				// Columna donde se mostrará el costo del concepto
    				$data .= "<td align='center' rowspan='2'> Costo en <br> ".$row_est['Cestmo']." </td>";
    				// Columna donde se mostrará el total de costo x frecuencia
    				$data .= "<td align='center' rowspan='2'> Total <br> Costo </td>";
    				$data .= "</tr>";
    				$data .= $data2;	

    				while ($row_con = mysql_fetch_assoc($res_con))
    				{

        					// Arreglo que contendrá los datos de cada fila del informe
        					$j ++;
        					$movimientos = array();
        					$realizado   = array();				
        					$cont_frecuencia = 0;
        					
        					// Seleccionar color de fondo de la fila
        					if (is_int ($j/2))
        					   $wcf="fila1";  
        					else
        					   $wcf="fila2"; 
        					
        					$aux_concepto = $row_con['Concod'];
        					
        					/*******************************************************************************************
        					* CONDICIONAL PARA DIFERENCIAR DATOS DE MOVIMIENTOS PROGRAMADOS Y MOVIMIENTOS REALIZADOS *
        					*******************************************************************************************/
        					
        					if ($wtipo=='Moprea')
        					{
                      foreach ($resultset2 as $rowlis)
                      {
                            $frecuencia = $rowlis['Mopvfr'];
                            $numerovisi = $rowlis['Mopnvi'];
                            $tipovisita = $rowlis['Moptvi'];

                            if ($rowlis['Concod'] == $aux_concepto){   

                                 $movimientos[$frecuencia][$numerovisi][$tipovisita] = $rowlis['Mopcan'];
                                 $realizado[$frecuencia][$numerovisi][$tipovisita]   = $rowlis['Moprea'];   

                                 if ($rowlis['Moprea']=='on')
                                 {
                                    $totales[$frecuencia][$numerovisi][$tipovisita] += $rowlis['Mopcan'];
                                    $cont_frecuencia += $rowlis['Mopcan'];      
                                 }
                                 
                            }
                                  
                      }

        						
        						$data .= "<tr class='".$wcf."'>";

        						// Celda con el concepto para cada recorrido
        						$data .= "<td><b>".$row_con['Concod']." - ".$row_con['Condes']."</b> </td>";
        						
        						// Ciclo que imprime los datos de la fila 
        						// Con base en los arreglos $frecuencias y $movimientos
        						for ($k=1;$k<=$numvis;$k++)
        	          {
          							for ($i=1;$i<=$numfre;$i++)
          							{
            								$frecuencia = $frecuencias[$i];
                            $tipovisita = $tiposvisita[$i];
            								// Cambio puntos por doble guión bajo para que no me afecte al tomar el nombre del campo
            								$concepto = codificar($row_con['Concod']);
            								$data .= "<td align='center' width='5%'>";
            								
            								if(isset($movimientos[$frecuencia][$k][$tipovisita]) && $movimientos[$frecuencia][$k][$tipovisita] > 0)
            								{
            									if(isset($realizado[$frecuencia][$k][$tipovisita]) && $realizado[$frecuencia][$k][$tipovisita]=='on')
            										$data .= $movimientos[$frecuencia][$k][$tipovisita]." ";
            								}
            										
            								$data .= "</td>";
          							}
    					      }

        					}
        					else
        					{

                      foreach ($resultset2 as $rowlis)
                      {
                            $frecuencia = $rowlis['Mopvfr'];
                            $numerovisi = $rowlis['Mopnvi'];
                            $tipovisita = $rowlis['Moptvi'];

                            if ($rowlis['Concod'] == $aux_concepto){   

                                 $movimientos[$frecuencia][$numerovisi][$tipovisita] = $rowlis['Mopcan'];
                                 $realizado[$frecuencia][$numerovisi][$tipovisita]   = $rowlis['Moprea'];   
                                 $totales[$frecuencia][$numerovisi][$tipovisita]    += $rowlis['Mopcan'];
                                 $cont_frecuencia += $rowlis['Mopcan'];      
                            }
                                  
                       }
            						
            						$data .= "<tr class='".$wcf."'>";

            						// Celda con el concepto para cada recorrido
            						$data .= "<td><b>".$row_con['Concod']." - ".$row_con['Condes']."</b> </td>";
            						
            						// Ciclo que imprime los datos de la fila 
            						// Con base en los arreglos $frecuencias y $movimientos
            						for ($k=1;$k<=$numvis;$k++){

                						for ($i=1;$i<=$numfre;$i++){
                                
                  							$frecuencia = $frecuencias[$i];
                                $tipovisita = $tiposvisita[$i];
                  							$data .= "<td align='center' width='5%'>";
                  								
                  							if(isset($movimientos[$frecuencia][$k][$tipovisita]) && $movimientos[$frecuencia][$k][$tipovisita])
                  									$data .= $movimientos[$frecuencia][$k][$tipovisita]." ";
                  										
                  							$data .= "</td>";
                						}
            					  }
        					}

			
					/********************************************************
					******* DATOS POR PACIENTE ******************************
					********************************************************/
					
					// Frecuencia del concepto
					$data .= "<td align='right'> ".number_format($cont_frecuencia,0,'.',',')." </td>";
					$totales['cantidad']['1']  += $cont_frecuencia;		// Total de frecuencias para el concepto

					// Costo unitario del concepto
					$data .= "<td align='right'> ".number_format($row_con['Concosto'],2,'.',',')." </td>";
					$totales['costo']['1']  += $row_con['Concosto'];		// Total de costos unitarios de los conceptos

					// Costo total de el valor del concepto X las frecuencias de éste
					$total_concepto = $row_con['Concosto'] * $cont_frecuencia;
					$data .= "<td align='right'> ".number_format($total_concepto,2,'.',',')." </td>";
					$totales['total']['1']  += $total_concepto;		// Total conceptos X frecuencias para el paciente


					/*******************************************************/
					
					$data .= "</tr>";

					// Se reinicia el arreglo para la nueva fila
          //unset($totales); 
					unset($movimientos);
					unset($realizado);

				} // Fin de recorrido de conceptos
          mysql_data_seek ( $res_con , 0 );
          mysql_data_seek ( $res_fre , 0 );

					// Pie de la tabla para mostrar los totales
					$data .= "<tr class='encabezadoTabla'>";
					$data .= "<td align='center'>";
					$data .= "TOTALES";
					$data .= "</td>";
					
					// Ciclo para llenar el pie de página de la tabla con los totales de las frecuencias

					for ($k=1;$k<=$numvis;$k++){
            
    						for ($i=1;$i<=count($frecuencias);$i++){

      							$indice_frecuencia = $frecuencias[$i];
                    $tipos_visita      = $tiposvisita[$i]; 
      							$numero_visita     = $k;
      							if(isset($totales[$indice_frecuencia][$numero_visita][$tipos_visita]))
      								$total_frecuencia = $totales[$indice_frecuencia][$numero_visita][$tipos_visita];
      							else
      								$total_frecuencia = 0;
      								
      							$data .= "<td align='center'>".number_format($total_frecuencia,0,'.',',')."</td>";
    						}
				    }
			
					// Celda donde se muestra el total de la sumatoria de la cantidad de frecuencias
					$data .= "<td align='right'> ".number_format($totales['cantidad']['1'],0,'.',',')." </td>";

					// Celda donde se muestra el total de la sumatoria del costo unitario de los conceptos
					$data .= "<td align='center'> ".number_format($totales['costo']['1'],2,'.',',')." </td>";

					// Celda donde se muestra el total de la sumatoria de costos unitario de los conceptos X las frecuencias
					$data .= "<td align='center'> ".number_format($totales['total']['1'],2,'.',',')." </td>";
					
					$data .= "</tr>";

					// Sumo al costo total del estudio
					$total_estudio += $totales['total']['1'];
					
					// Espacio entre las filas con los datos y la fila con el total general
					$data .= "<tr><td colspan='".$numcol."' height='20'></td></tr>";

			}	
            
        $data .= "</table>";
		    $data .= "</div>";
		    $data .= "</div>";

		} // Fin de recorrido de pacientes
		
		// Costo total del estudio
		$data .= "<center><table>";
		$data .= "<tr class='encabezadoTabla' height='27'>";
		$numcol_total = $numcol;
        
    if ($num_pac > 0){
			
    			$data .= "<td align='center' colspan='".$numcol_total."'><div align='left'> <b>COSTO TOTAL DEL ESTUDIO</b> </div></td>";
    			$data .= "<td align='center'> ".number_format($total_estudio,2,'.',',')." </td>";			
	  }
	  else{
 			    $data .= "<td align='center' colspan='".$numcol_total."'><div align='left'> <b>NO HAY REGISTROS LOCALIZADOS</b> </div></td>";
	  }

	  $data .= "</tr></table></center>";
		echo $data;		
    return;

	}	          
	   
     //Consultar los estudios según el laboratorio seleccionado 
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarEstudios"){

          $data = array('error'=>0,'html'=>'','mensaje'=>'');

          $q  = " SELECT Cescoe
                  From ".$wbasedato."_000004 
                  Where Cescol='".$wcodigola."'"  ;

          $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

          $num = mysql_num_rows($res);

          if ($num == 0){
              $data['error'] = 1;
              $data['mensaje'] = 'NO HAY ESTUDIOS ASIGNADOS AL LABORATORIO SELECCIONADO';
          }
          else{
              while($row = mysql_fetch_assoc($res))
              {
                $data['html'] .= "<option value='".$row['Cescoe']."'>".$row['Cescoe']."</option>";
              }
          }  

          echo json_encode($data);
          return;
     }


       //Consultar los pacientes asignado al Estudio seleccionado
       if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarPacientes"){

            $data = array('error'=>0,'html'=>'','mensaje'=>'');

            $q  = " SELECT Paecol,Paecoe, concat (B.Pactid,'-',B.Pacced) as Idepac, 
                    concat (B.Pacno1,' ',B.Pacno2,' ',B.Pacap1,' ',B.Pacap2) as Nompac
                    From ".$wbasedato."_000007 A
                    Inner Join ".$wbasedato."_000011 B on  A.Paeide = B.Pacced
                      AND A.Paetid = B.Pactid
                    Where A.Paecol='".$wcodigolab."'
                      AND A.Paecoe='".$wcodigoes."' "  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num == 0){

                    $data['error']   = 1;
                    $data['mensaje'] = 'NO HAY PACIENTES ASIGNADOS AL ESTUDIO SELECCIONADO';
            }
            else {
                	 $data['html'] = "<option></option>";
                   while($row = mysql_fetch_assoc($res)){

                      $data['html'] .= "<option value='".$row['Idepac']."'>".$row['Nompac']."</option>";
                   }
            }  

            echo json_encode($data);
            return;
     }


  function consultarLaboratorios($wbasedato,$conex,$wemp_pmla){ 

    $strtipvar = array();
    $q  = " SELECT Labnit, Labnom"
         ."  From ".$wbasedato."_000001 ";

    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
    while($row = mysql_fetch_assoc($res))
         {
           $strtipvar[$row['Labnit']] = utf8_encode($row['Labnom']);
         }
    return $strtipvar;

  }


  function array_envia($array) 
  {

    $tmp = serialize($array);

    return $tmp;
  }

  
  function array_recibe($array) 
  {

    //$tmp = stripslashes($array);
    $tmp = unserialize($tmp);

    return $tmp;
  }


  function codificar($concepto_dec)
  {
	  return str_replace(".","__",$concepto_dec);
  }


  function decodificar($concepto_cod)
  {
	  return str_replace("__",".",$concepto_cod);
  }
  // **********************************         FIN PHP         ********************************************

  ?>

<html>
<head>
  <title>REPORTE DE PACIENTES POR ESTUDIO</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />   
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>  
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>    
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>      

    <!--< Plugin para el select con buscador > -->

    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <script type="text/javascript" src="../../../include/root/prettify.js"></script>
    <script src="../../../include/root/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>
    <script src="../../../include/root/jquery-ui-sliderAccess.js" type="text/javascript" ></script>


<script type="text/javascript">

   $(document).ready(function(){

	    //Configurar campos multiselect
	    $('#optlaboratorio,#optestudio,#optpaciente').multiselect({
	       numberDisplayed: 1,
	       selectedList:1,
	       multiple:false
	    }).multiselectfilter();

	    $(".accordionFiltros").accordion({
	            collapsible: true,
	            heightStyle: "content"
	    });

   });


   function Consultar(valor1,valor2,opcion){

    	 var wemp_pmla = $("#wemp_pmla").val();
    	 var wlaborato = $("#optlaboratorio").val();
    	 var westudio  = $("#optestudio").val();

    	 if ($("#optpaciente").val() !== null)
    	     var wpaciente = $("#optpaciente").val();
    	 else
    	     var wpaciente = '';

    	 var wtipo   = $("#wtipo").val();

    	 if  ($("#optlaboratorio").val()==null || $("#optestudio").val()==null){
    	      jAlert('Falta seleccionar informaci\u00f3n');
    	 }

       //Llamado ajax para consultar la tabla detalle
       $.post("rep_mov_pac_x_est.php",
               {
                  consultaAjax:   true,
                  accion:         'ConsultarDetalle',
                  wemp_pmla:      wemp_pmla,
                  westudio:       westudio.toString(),
                  wlaboratorio:   wlaborato.toString(),
                  wpaciente:      wpaciente.toString(),
                  wtipo:          wtipo
               }, function(data){
                  
                  $("#tblpaciente").show();
                  $("#tblpaciente").empty();
                  $("#tblpaciente").append(data);

                  // agregar la clase que convierte un div en un formato tipo acordión
                  $(".accordionFiltros").accordion({
  		               collapsible: true,
  		               heightStyle: "content"
  	              });

              });

	}

   // Consulta los Estudios segun el laboratorio seleccionado y lo agrega al campo optestudio 
   function cargarselestudio(valor,opcion){
      
       var wemp_pmla = $("#wemp_pmla").val();
       
       if (opcion =='1'){
          var wlabor  = valor.value;}
       else{
          var wlabor  = valor;}

       $.post("rep_mov_pac_x_est.php",
           {
              consultaAjax:   true,
              accion:         'ConsultarEstudios',
              wemp_pmla:      wemp_pmla,
              wcodigola:      wlabor
           }, function(data_json){
            
            if (data_json.error == 1)
            {
               jAlert(data_json.mensaje, 'Alerta');
            }
            else
            {         
               $('#optestudio').html(data_json.html);
               $('#optestudio').multiselect({
                             position: {
                              my: 'left bottom',
                              at: 'left top'                                
                             },
                             
                          }).multiselectfilter();
                                    
               $('#optestudio').multiselect("refresh");
            }

       },"json");

   }    


  // Consulta los Pacientes asignados a un Estudio específico y lo agrega al campo optpaciente
  function cargarselpaciente(valor) {
      
       var wemp_pmla = $("#wemp_pmla").val();
       var wlaborato = $("#optlaboratorio").val();

       $.post("rep_mov_pac_x_est.php",
           {
              consultaAjax:   true,
              accion:         'ConsultarPacientes',
              wemp_pmla:      wemp_pmla,
              wcodigoes:      valor.value,
              wcodigolab:     wlaborato.toString()
           }, function(data_json){
            
            if (data_json.error == 1)
            {
              jAlert(data_json.mensaje, 'Alerta');
            }
            else
            {         
              $('#optpaciente').html(data_json.html);
              $('#optpaciente').multiselect({
                             position: {
                              my: 'left bottom',
                              at: 'left top'                                
                             },
                             
                          }).multiselectfilter();
                                    
              $('#optpaciente').multiselect("refresh");
            }

       },"json");
   }


	function Activarfactura(obj)
	{    		
		if(document.getElementById('chkfactura').checked == true){
		   $('#txtfactura').show();
		   $('#txtfactura').focus();			
		}   
		else{
		   $('#txtfactura').hide();
		}
	}



	// Validación para movimientos con factor 1
	function valida_envio(form)
	{
		if(document.getElementById('wlaboratorio').value=="0") 
		{
			alert("Debe seleccionar un laboratorio");
			return false;
		}
		if(document.getElementById('westudio').value=="0") 
		{
			alert("Debe seleccionar un estudio");
			return false;
		}
		form.submit();
	}

	function Seleccionfactura()
	{
	    $("#wselfra").val('1');
	}

	// Validación para movimientos con factor 1
	function envia_form()
	{
		 document.getElementById('wenvio').value = '1'; 
		 document.forms.form1.submit();
	}

	// Vuelve a la página anterior llevando sus parámetros
	function retornar(wemp_pmla,wlaboratorio,westudio,wtipo,wdircie)
	{
		 $("#wselfra").val('0');
		 location.href = "rep_mov_pac_x_est.php?wemp_pmla="+wemp_pmla+"&wlaboratorio="+wlaboratorio+"&westudio="+westudio+"&wtipo="+wtipo+"&wdircie="+wdircie;
	}

	
	// Cierra la ventana
	function cerrar_ventana(cant_inic)
	{
		 window.close();
	}


	// Cierra la ventana
	function carga_laboratorio()
	{
		 document.getElementById('wenvio').value = '0'; 
		 document.form1.submit();
	}

     
</script>
<style type="text/css">

         .button{
          color: #1b2631;
          font-weight: normal;
          font-size: 12,75pt;
          width: 100px; height: 27px;
          background: rgb(199,199,199);
          background: -moz-linear-gradient(top,  rgba(199,199,199,1) 0%, rgba(193,193,193,1) 50%, rgba(184,184,184,1) 51%, rgba(224,224,224,1) 100%);
          background: -webkit-linear-gradient(top,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
          background: linear-gradient(to bottom,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
          filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c7c7c7', endColorstr='#e0e0e0',GradientType=0 );
          border: 1px solid #ccc;
          border-radius: 8px;
          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
         }

        .button:hover {background-color: #3e8e41}

        .button:active {
           background-color: rgb(169,169,169);
           box-shadow: 0 5px #666;
           transform: translateY(4px);
         }

        .ui-multiselect { 
          height:20px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left;font-size: 10pt; 
        } 

</style>  
</head>
<body>
 <?php 
      echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
      $wtitulo  ="REPORTE DE PACIENTES POR ESTUDIO";
      encabezado($wtitulo, $wactualiz, 'clinica');
      $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "dircie");
      $arr_lab   = consultarLaboratorios ($wbasedato,$conex,$wemp_pmla);
	    // Asignación de fecha y hora actual
	    $wfecha = date("Y-m-d");
	    $whora  = (string)date("H:i:s");
    ?>

<fieldset style="border: 0.5px solid #999999;font-size:8pt">
    <center>
    <table>
     <tr height="40px">
       <td ><font size="2">Tipo de Movimiento</font> </td>
       <td ><select name='wtipo' id='wtipo'>
       <option value='Mopcan'>INGRESO DE DATOS</option>
       <option value='Moprea'>VERIFICACION DE CUMPLIMIENTO</option>
       </select>
       </td>
       <td ><font size="2">Laboratorio</font></td>
       <td ><select id='optlaboratorio' name='optlaboratorio' multiple='multiple' onchange="cargarselestudio(this,'1')" style='width: 100px;'>
       <?php
        echo '<option ></option>';
        foreach( $arr_lab as $key => $val){
          echo '<option value="' . $key .'">'.$val.'</option>';
        }
       ?>
       </select>
       </td>
     </tr>
     <tr> 
      <td ><font size="2">Estudio</font></td>
      <td ><select id='optestudio' name='optestudio' multiple='multiple' onchange="cargarselpaciente(this)"; style='width: 100px;'></select>
      </td>
      <td ><font size="2">Paciente</font></td>
      <td ><select id='optpaciente' name='optpaciente' multiple='multiple' style='width: 100px;'></select></td>     
     </tr>
    </table>
    </center>    
    </br>
    <br>
    <center><table>
    <tr>
    <td>&nbsp;&nbsp;<input type='submit' id='Consultar' name='Consultar' class='button' value='Consultar'  onclick='Consultar(this,this,"1")'></td>
    <td>&nbsp;&nbsp;<input type='submit' id='Salir'     name='Salir'     class='button' value='Salir'      onclick='cerrarVentana()'></td>
    </tr>
    </table>
    </fieldset>
    <div id='tblpaciente' name='tblpaciente' style='border: 0.5px solid #999999;visibility:none;'>
    </div> 
</body>
</html>