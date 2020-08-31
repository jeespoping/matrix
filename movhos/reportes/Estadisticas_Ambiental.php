<html>
<head>
  <title>Estadisticas GESTION AMBIENTAL</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	 document.forms.estadisticas_ambiental.submit();
	}
	
	function cerrarVentana()
	 {
      window.close();		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   *         ESTADISTICAS GESTION AMBIENTAL      *
   *     		  CONEX, FREE => OK		         *
   ***********************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  include_once("root/magenta.php");
  include_once("root/comun.php");
  
  $conex = obtenerConexionBD("matrix");
  
  $wactualiz="(Marzo 1 de 2010)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Marzo 1 de 2010                                                                                                             \\
//=========================================================================================================================================\\
//                                                                                                                                         \\
//                                                                                                                                         \\
//=========================================================================================================================================\\	                                                         
	                                                           
	                                                             
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//                                                                                                                                         \\
//                                                                                                                                         \\
//=========================================================================================================================================\\
//=========================================================================================================================================\\
	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
	                                                                                                       
  echo "<br>";				
  echo "<br>";
      		
  
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
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
       
  function indicadores_hospitalarios($wfecha_i, $wfecha_f, $wcco_aux)
    {
	 global $conex;
	 global $wbasedato;  
	 
	 global $wtotal_peso;
	 global $wcamas_disponibles;
	 global $wtotal_dias_cama_ocupada;
	 
	 global $wpoc;
	 
	    
	 $wpoc=Ocupacion_hospitalaria($wfecha_i, $wfecha_f, $wcco_aux);   //Pocentaje de Ocupación Clinica
		 
	 //Indicador de Generación promedio por cama
	 $windicador_generacion_residuos = (($wtotal_peso/$wcamas_disponibles)*$wpoc);
	 
	 //=======================================================================================================
	 //  A G U A 
	 //=======================================================================================================
	 //Traigo el total del consumo de AGUA
	 $q = " SELECT SUM(mampes) "
         ."   FROM ".$wbasedato."_000089 "
         ."  WHERE mamtit = 'A01' "                                     //Codigo del item AGUA                      
         ."    AND mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
         ."    AND mamest = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res); 
	 
	 $wtotal_agua=$row[0];
	 
	 //Indicador de Consumo de AGUA promedio por cama
	 $windicador_consumo_agua = (($wtotal_agua/$wcamas_disponibles)*$wpoc);
	 //=======================================================================================================
	 
	 //=======================================================================================================
	 //  E N E R G I A
	 //=======================================================================================================
	 //Traigo el total del consumo de ENERGIA
	 $q = " SELECT SUM(mampes) "
         ."   FROM ".$wbasedato."_000089 "
         ."  WHERE mamtit = 'E01' "                                     //Codigo del item AGUA                      
         ."    AND mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
         ."    AND mamest = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res); 
	 
	 $wtotal_energia=$row[0];
	 
	 //Indicador de Consumo de AGUA promedio por cama
	 $windicador_consumo_energia = (($wtotal_energia/$wcamas_disponibles)*$wpoc);
	}    
       
       

  function Ocupacion_hospitalaria($wfecha_i, $wfecha_f, $wcco_aux)
    {
	 global $conex;
	 global $wbasedato;
	 
	 global $wcamas_disponibles;
	 global $wtotal_dias_cama_ocupada;
	 
	 $wporcentaje_ocupacion_clinica=0;
	 
	 //Total dias cama disponible
	 $q = " SELECT SUM(ciedis), SUM(cieocu), SUM(cieing), SUM(cieegr), SUM(cieiye), ABS(DATEDIFF('".$wfecha_i."','".$wfecha_f."'))+1 "
         ."   FROM ".$wbasedato."_000038 "
         ."  WHERE fecha_data BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
         ."    AND cieser = '".$wcco_aux."'";  
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num = mysql_num_rows($res);
     
     if ($num > 0)
        {
	     $row = mysql_fetch_array($res); 
	     
	     $wcamas_disponibles       = $row[0]+$row[1];     //Disponibles + Ocupadas
	     
	     //$wtotal_dias_cama_ocupada = (($row[2]-$row[3])+$row[4]);                                    //Ocupadas dia anterior + ingresos - egresos + IngEgr Mismo dia
	     $wtotal_dias_cama_ocupada = $row[1]+$row[4];                                                  //Ocupadas + IngEgr Mismo dia
	     $wporcentaje_ocupacion_clinica = ($wtotal_dias_cama_ocupada/$wcamas_disponibles);             //% Ocupacion Clinica promedio
        }      
	 return $wporcentaje_ocupacion_clinica;    
	}         
       
       
  function peso_total_item($wfecha_i, $wfecha_f, $witem_aux, $wcco)
    {
	 global $conex;
	 global $wbasedato;
	 global $wcco;  
	    
	 $wcco_a = explode("-",$wcco);
	 
	 $q = " SELECT SUM(mampes) "
         ."   FROM ".$wbasedato."_000089 "
         ."  WHERE mamcco LIKE '".trim($wcco_a[0])."'"
         ."    AND mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
         ."    AND mamtit = '".trim($witem_aux)."'"
         ."    AND mamest = 'on' "; 
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $row = mysql_fetch_array($res);
     
     if ($row[0] > 0)
        return $row[0];
       else
          return 0;   
	}         
       
  function total_peso_clinica($wfecha_i, $wfecha_f, $wtipo)
    {
	 global $conex;
	 global $wbasedato; 
	    
	 $q = " SELECT SUM(mampes) "
         ."   FROM ".$wbasedato."_000089 "
         ."  WHERE mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
         ."    AND mamtit LIKE '".trim($wtipo)."'"
         ."    AND mamtit NOT IN ('A01','E01') "   //Diferente a Agua y Energia
         ."    AND mamest = 'on' "; 
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $row = mysql_fetch_array($res);
     
     if ($row[0] > 0)
        return $row[0];
       else
          return 0;    
	}    
  
  
  function reporte($wcco, $wtipo, $wfecha_i, $wfecha_f)
    {
	 global $conex;
	 global $wbasedato;   
	 global $wfec;
	 global $whora;
	 global $wusuario;
	 
	 global $wtotal_peso;
	 
	 global $wcamas_disponibles;
	 global $wpoc;
	 
	 if (trim($wcco) != "%")
	    {
		 //Este query es para cuando se solicite información de un solo centro de costo   
		 $q = " SELECT mamcco, cconom "
	         ."   FROM ".$wbasedato."_000089, ".$wbasedato."_000011 "
	         ."  WHERE mamcco = '".trim($wcco)."'"
	         ."    AND mamtit LIKE '".trim($wtipo)."'"
	         ."    AND mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
	         ."    AND mamest = 'on' "
	         ."    AND mamcco = ccocod "
	         ."  GROUP BY 1,2 "
	         ."  ORDER BY 1 ";
        }
       else
          {
	       //Este query lo hago diferente porque cuando se requieren las estadisticas de todos los cco's
	       //se supone que deben ser de todos los que sena internos osea la misma institucion
		   $q = " SELECT mamcco, cconom, ccohos "
		       ."   FROM ".$wbasedato."_000089, ".$wbasedato."_000011 "
		       ."  WHERE mamcco LIKE '".trim($wcco)."'"
		       ."    AND mamtit LIKE '".trim($wtipo)."'"
		       ."    AND mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
		       ."    AND mamest = 'on' "
		       ."    AND mamcco = ccocod "
		       ."    AND ccoint = 'on' "
		       ."  GROUP BY 1,2 "
		       ."  ORDER BY 1 ";
	      }     
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num = mysql_num_rows($res);
    
     if ($num > 0)
        {
         $wtotal_peso=total_peso_clinica($wfecha_i, $wfecha_f, $wtipo);    //Traigo el peso total de la clinica
         
         //============================================================================================================
         //TIPOS DE RESIDUOS O ITEMS
         //============================================================================================================
         //Traigo los Item`s del maestro para buscar cada uno por cada centro de costo
         $q = " SELECT ambcod, ambdes "
             ."   FROM ".$wbasedato."_000088 ";
         $res_item = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num_item = mysql_num_rows($res_item);    
         //============================================================================================================
         
         echo "<br>";
         
         echo "<table>";
         echo "<tr class='encabezadoTabla'>";
         echo "<th rowspan=2>Centro de Costo / Unidad</th>";
         echo "<th rowspan=2>Días Cama<br>Disponible</th>";
         //TITULOS con los nombres de los ITEMS o TIPOS DE RESIDUO
         for ($i=1;$i<=$num_item;$i++)
             {
	          $row_item = mysql_fetch_array($res_item);       //Tuplas de los Items   
	          echo "<th colspan=3>".$row_item[1]."</th>";  
             }    
         echo "</tr>";
         
         echo "<tr class='encabezadoTabla'>";
         for ($i=1;$i<=$num_item;$i++)
             {
	          echo "<th>Cant.</th>";
	          echo "<th>%</th>";
	          echo "<th>Generación<br>X Cama</th>";
	          
	          $wtotcan[$i]=0;
	          $wporc[$i]  =0; 
	          $wgencam[$i]=0;
             }
         echo "</tr>";     
         //===========================================================================================================
         
         //mysql_data_seek($res,0);  //Devuelvo el puntero
         //Por cada centro de costo, busco cada uno de los item.
         $wtot_cam_dis=0;
         
         for ($i=1;$i<=$num;$i++)
	       {
		    $row = mysql_fetch_array($res);                   //Tuplas de los centros de costo
		    
		    if (is_integer($i/2))
               $wclass="fila1";
              else
                 $wclass="fila2"; 
		    
		    $wcco_aux=$row[0];
		    $wccohos =$row[2];
		       
		    echo "<tr class=".$wclass.">";
		    echo "<td>".$row[0]." - ".$row[1]."</td>";                                                             //Centro de costo
		    if ($wccohos == "on")
		       {
			    indicadores_hospitalarios($wfecha_i, $wfecha_f, $wcco_aux);   
		        echo "<td align=center>".number_format($wcamas_disponibles,0,'.',',')."</td>";                     //Dias Cama disponible
		        
		        $wtot_cam_dis = $wtot_cam_dis + $wcamas_disponibles;
	           } 
		      else
		         echo "<td align=center>".number_format(0,0,'.',',')."</td>";                                      //Dias Cama disponible 
		    
		    //Busco cada uno de los item's
		    mysql_data_seek($res_item,0);  //Devuelvo el puntero de los items
		    for ($j=1; $j<=$num_item;$j++)
		        {
			     $row_item = mysql_fetch_array($res_item);                                                         //Tuplas de los Items
			        
			     $witem_aux=$row_item[0];
			     
			     //Traigo el peso total por cada item
			     $wpestotitem = peso_total_item($wfecha_i, $wfecha_f, $witem_aux, $wcco);
			     
			     //==================================================================
			     //Por cada item busco si tiene movimiento para cada centro de costo
			     $q = " SELECT SUM(mampes) "
			         ."   FROM ".$wbasedato."_000089 "
			         ."  WHERE mamcco = '".$wcco_aux."'"
			         ."    AND mamtit = '".$witem_aux."'"                           
			         ."    AND mamfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."'"
			         ."    AND mamest = 'on' ";
			     $res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                 $row_mov = mysql_fetch_array($res_mov);      //Tuplas del movimiento
                 //==================================================================
                 
                 if ($row_mov[0] > 0)
                    {
	                 $wtotcan[$j] = $wtotcan[$j] + $row_mov[0];
	                 $wporc[$j]   = $wporc[$j]   + ($row_mov[0]/$wpestotitem);
	                 if ($wccohos == "on")
	                    $wgencam[$j] = $wgencam[$j] + ($row_mov[0]/$wcamas_disponibles)*$wpoc;
	                   else
	                      $wgencam[$j] = $wgencam[$j] + 0;
	                 
	                 if ($wccohos=="on")
	                    {
		                 echo "<td align=center>".$row_mov[0]."</td>";                                                         //Peso
	                     echo "<td align=center>".number_format(($row_mov[0]/$wpestotitem)*100,2,'.',',')."</td>";             //% Porcentaje de participacion
	                     echo "<td align=center>".number_format(($row_mov[0]/$wcamas_disponibles)*$wpoc,2,'.',',')."</td>";    //Indice Generacion X Cama
                        }
	                   else
	                      {
		                   echo "<td align=center>".$row_mov[0]."</td>";                                                       //Peso
	                       echo "<td align=center>".number_format(($row_mov[0]/$wpestotitem)*100,2,'.',',')."</td>";           //% Porcentaje
	                       echo "<td align=center>".number_format(0,2,'.',',')."</td>";                                        //Indice Generacion X Cama 
	                      }     
	                }
	               else
 	                  {
		               echo "<td>&nbsp</td>";
		               echo "<td>&nbsp</td>"; 
		               echo "<td>&nbsp</td>"; 
		               
		               $wtotcan[$j] = $wtotcan[$j]+0;
	                   $wporc[$j]   = $wporc[$j]+0;
	                   $wgencam[$j] = $wgencam[$j]+0;
		              } 
		        }
			  echo "</tr>";      
		   }
		 //Totales  
		 echo "<tr class='encabezadoTabla'>";
		 echo "<th>Totales...</th>";
		 echo "<th>".number_format($wtot_cam_dis,0,'.',',')."</th>";
         for ($i=1;$i<=$num_item;$i++)
             {
	          echo "<th>".$wtotcan[$i]."</th>";
	          echo "<th>".number_format(($wporc[$i])*100,2,'.',',')."</th>"; 
	          echo "<th>".number_format(($wgencam[$i])*100,2,'.',',')."</th>";  
             }
         echo "</tr>"; 
           
		 echo "</table>";
		 
		 //indicadores_hospitalarios($wfecha_i, $wfecha_f, $wcco_aux);
		 
	    }
	}    
  //=====================================================================================================================================================================	

       
  encabezado("Gestión Ambiental", $wactualiz, 'clinica');
  
       
  //FORMA ================================================================
  echo "<form name='estadisticas_ambiental' action='Estadisticas_Ambiental.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  global $wgrabar;   
     
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
  if (!isset($wcco) or trim($wcco) == "" or !isset($wfec_i) or !isset($wfec_f) or !isset($witem) or trim($witem) == "")
     {     
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
			 	 
	  echo "<center><table>";  
	  echo "<tr><td align=right class=fila1><b>SELECCIONE LA UNIDAD EN LA QUE SE ENCUENTRA: </b></td>";
	  echo "<td align=center class=fila2><select name='wcco'>";
	  
	  if (isset($wcco))
	     echo "<option>".$wcco."</option>";
	    else
	       echo "<option>% - Todos</option>";      
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
          
      
      echo "<tr>";
      echo "<td align=right  class=fila1><b>Fecha Inicial a Consultar: </b></td>";
      echo "<td align=center class=fila2>";
      campofechaDefecto("wfec_i",$wfecha);
      echo "</td>";
      echo "</tr>";
      
      echo "<tr>";
      echo "<td align=right  class=fila1><b>Fecha Final a Consultar: </b></td>";
      echo "<td align=center class=fila2>";
      campofechaDefecto("wfec_f",$wfecha);
      echo "</td>";
      echo "</tr>";
        
      //Muestro todos los item's porque puede quererse generar un item especifico pero para todos los centros de costo, entonces para que no halla
      //que hacer recarga de la pantalla para traer los item's determinado por el centro de costo.
      $q = " SELECT ambcod, ambdes "
	      ."   FROM ".$wbasedato."_000088,".$wbasedato."_000090 "
	      ."  WHERE rcttip = ambcod "
	      ."  GROUP BY 1, 2 ";
	  $res_witem = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num_witem = mysql_num_rows($res_witem);
	  		 	 
	  echo "<tr><td align=right class=fila1><b>SELECCIONE EL TIPO DE RESIDUO O ITEM: </b></td>";
	  echo "<td align=center class=fila2><select name='witem'>";
	  
	  if (isset($witem) and trim($witem) != "")
	     echo "<option>".trim($witem)."</option>";
	    else
	       echo "<option></option>";
	       
	       
	  echo "<option>% - Todos</option>";    
	  for ($i=1;$i<=$num_witem;$i++)
	     {
	      $row_witem = mysql_fetch_array($res_witem); 
	      echo "<option>".$row_witem[0]." - ".$row_witem[1]."</option>";
         }
      echo "</select></td></tr>";
      
      //echo "<center><tr><td align=center colspan=4 bgcolor=cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else  //Esta setiado CCO y Fecha
       {
	    //echo "<input type='hidden' name='wcco'  value='".$wcco."'/>";
	    //echo "<input type='hidden' name='witem' value='".$witem."'/>";
	       
	    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        //ACA TRAIGO LOS DESTINOS DIGITADOS EN LA TABLA DE MATRIX
        $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
            ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
            ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
			 	 
        
        echo "<center><table>";
	    echo "<tr><td align=right class=fila1><b>SELECCIONE LA UNIDAD EN LA QUE SE ENCUENTRA: </b></td>";
	    echo "<td align=center class=fila2><select name='wcco' onchange='enter()'>";
	    if (isset($wcco))
	       echo "<option>".$wcco."</option>";
	    
	    echo "<option>% - Todos</option>";     
	    for ($i=1;$i<=$num;$i++)
	       {
	        $row = mysql_fetch_array($res); 
	        echo "<option>".$row[0]." - ".$row[1]."</option>";
           }
        echo "</select></td></tr>";
          
      
        echo "<tr>";
        echo "<td align=right  class=fila1><b>Fecha Inicial: </b></td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfec_i",$wfec_i);
        echo "</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<td align=right  class=fila1><b>Fecha Final: </b></td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfec_f",$wfec_f);
        echo "</td>";
        echo "</tr>";
        
        //Muestro todos los item's porque puede quererse generar un item especifico pero para todos los centros de costo, entonces para que no halla
        //que hacer recarga de la pantalla para traer los item's determinado por el centro de costo.
        $q = " SELECT ambcod, ambdes "
	        ."   FROM ".$wbasedato."_000088,".$wbasedato."_000090 "
	        ."  WHERE rcttip = ambcod "
	        ."  GROUP BY 1, 2 ";
	    $res_witem = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num_witem = mysql_num_rows($res_witem);
        
        echo "<tr><td align=right class=fila1><b>SELECCIONE EL TIPO DE RESIDUO O ITEM: </b></td>";
	    echo "<td align=center class=fila2><select name='witem'>";
	    if (isset($witem))
	       echo "<option>".$witem."</option>";   
	    for ($i=1;$i<=$num_witem;$i++)
	       {
	        $row_witem = mysql_fetch_array($res_witem); 
	        echo "<option>".$row_witem[0]." - ".$row_witem[1]."</option>";
           }
        echo "</select></td></tr>";
        
	    echo "</table>";
        
	    $wcco1 =explode("-",$wcco); 
	    $witem1=explode("-",$witem);   
	       
	    reporte($wcco1[0], $witem1[0], $wfec_i, $wfec_f);
	   }    
    
  echo "<table>";    
  echo "<center><tr><td align=center colspan=4 bgcolor=cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";     
  echo "</table>";
       
  echo "<br><br>";
  echo "<table>";     
  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";   
  echo "</table>";
    
} // if de register



include_once("free.php");

?>