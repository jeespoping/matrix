<head>
  <title>REPORTE ADMINISTRACION DE MEDICAMENTOS (HOJA DE MEDICAMENTOS)</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     }
     
    function enter()
	 {
	  document.forms.hojamed.submit();
	 } 
</script>
<?php
include_once("conex.php");
  /***************************************************
	*	          HOJA DE MEDICAMENTOS               *
	*	              POR HISOTRIA                   *
	*				CONEX, FREE => OK				 *
	*************************************************
	* Modificaciones 
	* 2019-03-15  Arleyda I.C. Migración realizada */
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

 	

 	include_once("root/comun.php");
  					    
  					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
    
    $wactualiz="(Abril 5 de 2011)";      
     
    //=======================================================================================================
	//FUNCIONES
	//=======================================================================================================
    
    function tomar_ronda($wron)
      {
	   $wronda=explode(":",$wron);
	   
	   if ((integer)$wronda[0] < 12)
	      {
		   if (strpos($wronda[1],"PM") > 0)
		      {
			   return $wronda[0]+12;   
			  }
			 else 
			    return $wronda[0];     
		  }
		 else
		    if ((integer)$wronda[0]==12)           
		       {
			    if (strpos($wronda[1],"PM") > 0)
			       return $wronda[0];                //Devuelve 12. que equivale a 12:00 PM osea del medio dia
			      else
			         return $wronda[0]-12; 
		       }
		      else       
		         return $wronda[0];
	 }    
        
	  
	 
	function elegir_centro_de_costo()   
     {
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $wcco;  
	  
	  
	  global $whora_par_actual;
	  global $whora_par_anterior;
	  
	  
	  echo "<center><table>";
      echo "<tr class=encabezadoTabla><td align=center><font size=20>HOJA DE MEDICAMENTOS</font></td></tr>";
	  echo "</table>";
	  
	  echo "<br><br>";
	  
	  //Seleccionar CENTRO DE COSTOS
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ".$wbasedato."_000011.ccohos = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Unidad : </font></td></tr>";
	  echo "</table>";
	  echo "<br><br><br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wcco' size='1' style=' font-size:40px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px' onchange='enter()'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
     } 
     
    
    function esdelStock($wart, $wcco)
	    {
		 global $conex;
		 global $wbasedato;  
		    
		 //=======================================================================================================
		 //Busco si el articulo hace parte del stock     Febrero 8 de 2011
		 //=======================================================================================================
		 $q = " SELECT COUNT(*) "
		     ."   FROM ".$wbasedato."_000091 "
		     ."  WHERE Arscco = '".trim($wcco)."' "
		     ."    AND Arscod = '".$wart."'"
		     ."    AND Arsest = 'on' ";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row = mysql_fetch_array($res);
		 //=======================================================================================================   
		  
		 if ($row[0] == 0)
		    return false;
		   else
		      return true; 
		}    	  
     
     
    function convertir_a_fraccion($wart,$wcan_apl,&$wuni_fra,$wcco)
      {
	   global $conex;
	   global $wbasedato;
	   global $wemp_pmla;

	   $ccoCM=ccoUnificadoCM(); //Se obtiene el Codigo de Central de Mezclas
	   $ccoSF=ccoUnificadoSF(); //Se obtiene el Codigo de Dispensacion
	        
	   $wdos_apl=0;    //Dosis
	   
	   $q = " SELECT deffra, deffru "
	       ."   FROM ".$wbasedato."_000059 "
	       ."  WHERE defcco in ('{$ccoSF}','{$ccoCM}')"
	       ."    AND defart = '".$wart."'"
	       ."    AND defest = 'on' ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
       $num = mysql_num_rows($res);
       if ($num > 0)
          {
           $row = mysql_fetch_array($res);     
	       $wcan_fra = $row[0];   //Cantidad de fracciones
	       $wuni_fra = $row[1];   //Unidad de la fracción
	       
	       //Si es el medicamento es del stock, no se hace la conversion, porque multiplicaria la cantidad aplicada por la fraccion de la 000059
	       if (!esdelStock($wart, $wcco))     //No es del Stock
	          {
	           $wdos_apl = $wcan_apl*$wcan_fra;
              }
             else
                $wdos_apl = $wcan_apl; 
	       
	       return $wdos_apl;
          }
         else
            return $wdos_apl; 
      }    
      
      
    function buscarSiEstaSuspendido($whis, $wing, $wart, $wfecha)
    {
	 global $user;
	 global $conex;
	 global $wbasedato;
	    
	    
	 $q = " SELECT COUNT(*)  "
	     ."   FROM ".$wbasedato."_000055 A "
	     ."  WHERE kauhis  = '".$whis."'"
	     ."    AND kauing  = '".$wing."'"
	     ."    AND kaufec  = '".$wfecha."'"
	     ."    AND kaudes  = '".$wart."'"
	     ."    AND kaumen  = 'Articulo suspendido' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res); 
	 
	 if ($row[0] > 0)  
	    return true;  //Indica que el articulo fue suspendido hace menos de dos horas, es decir que se puede aplicar, asi este suspendido
	   else
	      return false; //Indica que fue Suspendido hace mas de dos horas
	}       
      
    //=======================================================================================================
    
      
    
    //=======================================================================================================
    //=======================================================================================================
    //CON ESTO TRAIGO LA EMPRESA Y TODAS CAMPOS NECESARIOS DE LA EMPRESA
    $q = " SELECT empdes "
        ."   FROM root_000050 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res); 
  
    $wnominst=$row[0];
  
    //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
    $q = " SELECT detapl, detval "
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
           }  
      }
     else
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";            
    //=======================================================================================================
    //=======================================================================================================            
    
    echo "<form NAME=hojamed action='Hoja_medicamentos_enfermeria.php' method=post>";

    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";  
    
    if (!isset($wcco))
     {
      elegir_centro_de_costo();
     } 
    else 
       {
	    echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>"; 
	      
        if (!isset($whis) or !isset($wing))
	       {
		    $wcco1=explode("-",$wcco);
		       
		    encabezado("ADMINISTRACION DE MEDICAMENTOS (HOJA DE MEDICAMENTOS)", $wactualiz, 'clinica');   
		       
		    echo "<center><table></cenetr>";
	          
	        $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac "
		        ."   FROM ".$wbasedato."_000018, root_000037, root_000036 "
		        ."  WHERE ubihis  = orihis "
		        ."    AND ubiing  = oriing "
		        ."    AND oriori  = '".$wemp_pmla."'"
		        ."    AND oriced  = pacced "
		        ."    AND ubiald != 'on' "              //Solo los que estan activos
		        ."    AND ubisac  = '".$wcco1[0]."'"
		        ."  ORDER BY 1, 4, 5 ";    
		    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $wnr = mysql_num_rows($res);        
		            
		           
		    echo "<tr class=encabezadoTabla>";
		    echo "<th>Habitacion</th>";
		    echo "<th>Historia</th>";
		    echo "<th>Ingreso</th>";
		    echo "<th colspan=2>Paciente</th>";
		    echo "</tr>";
		           	   
		    $whabant = "";
		    for ($i=1;$i<=$wnr;$i++)
			   {
				if ($i % 2 == 0)
				   $wclass = "fila1";
				  else
				     $wclass = "fila2";   
				   
				$row = mysql_fetch_row($res);     
			    $whab    = $row[0];
			    $whis    = $row[1];
			    $wing    = $row[2];
			    $wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
			            	            
			    if ($whabant != $whab)
			       {
				    echo "<tr class=".$wclass.">";
				    echo "<td align=center>".$whab."</td>";
				    echo "<td align=center>".$whis."</td>";
				    echo "<td align=center>".$wing."</td>";
				    echo "<td align=left  >".$wpac."</td>";
				            
				    echo "<td align=center><A href='Hoja_medicamentos_enfermeria.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wemp_pmla=".$wemp_pmla."'>Imprimir</A></td>";
				    echo "</tr>";
				           
				    $whabant = $whab;
			       }
			   } 
			 
			 echo "</table>";
			 echo "<center><table>";
			 echo "<tr class=seccion1>";
			 echo "<td><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis' SIZE=10></td>";
			 echo "<td><b>Nro de Ingreso :</b><INPUT TYPE='text' NAME='wing' SIZE=10></td>";
			 echo "</tr>";
			 echo "<tr>&nbsp</tr>";
			 echo "<tr>&nbsp</tr>";
			 echo "<tr class=boton1><td align=center colspan=6></b><input type='submit' value='ACEPTAR'></b></td></tr></center>";
			 echo "</table>";
			 
			 echo "<center><font size=3><A href='Hoja_medicamentos_enfermeria.php?wemp_pmla=".$wemp_pmla."'> Retornar</A></font></center>";
	       }	       
		else 
		   {
		    /********************************* 
		    * TODOS LOS PARAMETROS ESTAN SET *
		    **********************************/
		    $wcco1=explode("-",$wcco);    
		    
		    //=======================================================================================================
		    //=======================================================================================================
		    //Traer Indicador del Kardex Electronico
		    $q = " SELECT ccokar "
		        ."   FROM ".$wbasedato."_000011 "
		        ."  WHERE ccocod = '".trim($wcco1[0])."'"
		        ."    AND ccoest = 'on' ";
		    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $row = mysql_fetch_array($res);
		    $wkar_ele = $row[0];        //Indica que tiene kardex electronico
		    //=======================================================================================================
		                          
		        
		    encabezado("REPORTE ADMINISTRACION DE MEDICAMENTOS A PACIENTES", $wactualiz, 'clinica');
	        
	        echo "<br>";
	        
	        echo "<center><table></center>";

	        $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac, cconom "
		        ."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wtabcco
		        ."  WHERE ubihis  = '".$whis."'"
		        ."    AND ubiing  = '".$wing."'"
		        ."    AND ubihis  = orihis "
		        //."    AND ubiing  = oriing "            //Se comenta para que traiga la informacion de cualquier ingreso del paciente, independiente que este activo al momneto de generarlo
		        ."    AND oriori  = '".$wemp_pmla."'"
		        ."    AND oriced  = pacced "
		        //."    AND ubiald != 'on' "              //Solo los que estan activos
		        ."    AND ubisac  = ccocod "
		        ."  ORDER BY 1, 4, 5 ";
		    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $wnr = mysql_num_rows($res); 
		    	    
		    $row     = mysql_fetch_row($res);     
			$whab    = $row[0];
			$whis    = $row[1];
			$wing    = $row[2];
			$wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
			$wser    = $row[9];
			$wnomser = $row[10];   
	        
	                
			echo "<tr class=seccion1>";  
			echo "<td colspan=10><b>HISTORIA N° : </b>".$whis." - ".$wing."</td>";
	        echo "<td colspan=10><b>SERVICIO : </b>".$wnomser."</td>";
	        echo "<td colspan=9 ><b>CAMA : </b>".$whab."</td>"; 
	        echo "</tr>";  
	        
	        echo "<tr class=seccion1>";
	        echo "<td colspan=29><b>PACIENTE : </b>".$wpac."</td>";
			echo "</tr>";
	       
	        
	        
	        //$q = " DROP TABLE TEMPO";
	        //$res3 = mysql_query($q,$conex);
	        $q = " CREATE TEMPORARY TABLE if not exists TEMPO as "
	            ." SELECT aplart, aplfec, aplron, aplcan, UPPER(apldes) apldes "
	            ."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026, ".$wbasedato."_000029 "
			    ."  WHERE aplhis                            = '".$whis."'"
			    ."    AND apling                            = '".$wing."'"
			    ."    AND aplest                            = 'on' "
			    ."    AND aplart                            = artcod "
			    ."    AND mid(artgru,1,instr(artgru,'-')-1) = gjugru "
			    ."    AND gjujus                            = 'on' "
			    //."  GROUP BY 2, 1, 3, 5 "
			    ."  UNION ALL"
			    ." SELECT aplart, aplfec, aplron, aplcan, apldes "
	            ."   FROM ".$wbasedato."_000015, ".$wcenmez."_000002 "
			    ."  WHERE aplhis                            = '".$whis."'"
			    ."    AND apling                            = '".$wing."'"
			    ."    AND aplest                            = 'on' "
			    ."    AND aplart                            = artcod "
			    ."    AND aplart                     NOT IN (SELECT artcod FROM ".$wbasedato."_000026) "
			    //."  GROUP BY 2, 1, 3, 5 "
			    ."  ORDER BY 2 desc, 1, 3 ";
			$res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;
			
			$q = " SELECT aplart, aplfec, aplron, SUM(aplcan), apldes "
	            ."   FROM TEMPO "
			    ."  GROUP BY 1, 2, 3, 5 "
			    ."  ORDER BY 2 desc, 1, 3 ";
			$res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;
			$wnr = mysql_num_rows($res3); 
			
			//Inicializo la MATRIZ a donde voy a llevar todo lo que le aplicaron al paciente en la estadia
		    for ($j=0;$j<=$wnr;$j++)
		       {
			    for ($l=0;$l<=23;$l++)     //24 horas que tiene el dia
		           {
		            $Mrondas[$j][$l]=0;    //Aca almaceno las cantidades de cada articulo segun la ronda
			       } 
			    $Afechas[$j]   =0;         //Aca llevo las fecha de aplicacion   
			    $Aarticulos[$j]=0;         //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
			    $Adesc[$j]     ="";        //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
			   }		 
				    
		    $j=1;
		    $i=1;
		    $row = mysql_fetch_row($res3);
		    
		    while ($j <= $wnr)
		       {
			    $wfec  = $row[1];
			    $wart  = $row[0];
			    $wdesc = $row[4];
			    
			    $Afechas[$i]    = $row[1];
			    $Aarticulos[$i] = $row[0];
			    $Adesc[$i]      = $row[4];
			  
			    while ($wart==$row[0] and $wfec==$row[1] and $wdesc==$row[4])
			        {
				     $wronda=(integer)tomar_ronda($row[2]);  
				     
				     if ($row[3]!=0)            //Si la cantidad es diferente a cero
				        {                       //articulo Cantidad aplicada
					     $wfrac=convertir_a_fraccion($wart,$row[3],$wuni_fra,$wcco1[0]);  
					     
					     if ($wfrac==0 or $wkar_ele != "on")                      //Si tiene Kardex Electronico (directo) en centro de costos _000011
					        {
						     $Mrondas[$i][$wronda]=$Mrondas[$i][$wronda]+$row[3];
						     if ($wkar_ele == "on")                               //Si tiene Kardex Electronico (directo) en centro de costos _000011
						        $Mrondas[$i]["fraccion"]="Sin Fracción";          
						       else
						          $Mrondas[$i]["fraccion"]=""; 
					        }
					       else //tiene fraccion
					         {
					          $Mrondas[$i][$wronda]=$Mrondas[$i][$wronda]+$wfrac;
					          $Mrondas[$i]["fraccion"]=$wuni_fra;  
				             } 
			         	} 
			           else
			              $i--; 
		         	 $row = mysql_fetch_row($res3);   
			         $j++;
			        } 
		        $i++;
		       }      
			    
		    $t=$i;
		    
		    echo "<tr class=encabezadoTabla>";    
	        echo "<th>FECHA</th>";
	        echo "<th>CODIGO</th>";
	        echo "<th>MEDICAMENTO</th>";
	        echo "<th>Unidad</th>";
	        $i=0;
	        while ($i <= 23)
	             {
		          if ($i < 12)      
		             echo "<th>".$i." AM"."</th>"; 
		            else 
		               echo "<th>".$i." PM"."</th>";
		          $i=$i+1;
	             }         
	        echo "<th>TOTAL</th>";
		    echo "</tr>";         
		    
		    $wfec="";
		    $i=1;
		    $k=1;
		    
		    $cont_serv=0;
		    while ($i < $t)   //Recorro la matriz
		         {
			      $wuni=".";  
			      $wnomart=$Adesc[$i]; 
			      if ($Aarticulos[$i] != "999")
			         {  
					  $q =  " SELECT artcom, artuni "
				           ."   FROM ".$wbasedato."_000026 "
				           ."  WHERE artcod = '".$Aarticulos[$i]."'";
					  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;
				      $wfilas = mysql_num_rows($res);
				      if ($wfilas==0)                                 //Si no existe en movhos lo busco en central de mezclas
				         {
					      $q =  " SELECT artcom, artuni "
				               ."   FROM ".$wcenmez."_000002 "
				               ."  WHERE artcod = '".$Aarticulos[$i]."'";
						  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;   
					     }       
	                  $row = mysql_fetch_array($res);
	                  
				      $wnomart = $row[0];
				      $wuni    = $row[1];
	                 } 
				  
	              //Traigo la cantidad de articulos(distintos) por cada FECHA
	              $q = " SELECT COUNT(DISTINCT(aplart)) "
	                  ."   FROM TEMPO "
			          ."  WHERE aplfec  = '".$Afechas[$i]."'";
				  $res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());;
			      $wfilas = mysql_fetch_array($res3);   
	              
	              if (is_integer(($i+$cont_serv)/2))
		             $wclass = "fila1";
		            else  
		               $wclass = "fila2";  
	              
	              echo "<tr class=".$wclass.">";
			      if ($wfec != $Afechas[$i])
			         {
				      echo "<tr><td bgcolor=DDDDDD colspan=29>&nbsp;</td></tr>";
				      echo "<tr class=".$wclass.">";
				      echo "<td rowspan=".$wfilas[0]." align=center><font size=2><b>".$Afechas[$i]."</b></td>";
			          $wfec = $Afechas[$i];
		             }
		             
		          $wsuspendido=false;   
		          $wsuspendido=buscarSiEstaSuspendido($whis, $wing, $Aarticulos[$i], $Afechas[$i]);
		             
		          echo "<td>".$Aarticulos[$i]."</td>";                                  //Codigo Articulo
			      echo "<td>".$wnomart."</td>";                                         //Nombre Articulo
			      echo "<td align=center>".$wuni."</td>";                               //Unidad de Medida
			      $j=0;
			      $wtotal=0;
			      while ($j <= 23)
			         {
				      if ($Mrondas[$i][$j] == 0)
				         echo "<td align=center>&nbsp;</td>";
				        else 
				           {
					        if ($j >= 12)
					           if ($j == 12)
					              $wmsg=$j." PM";
					             else
					                $wmsg=($j-12)." PM";
					          else
					             $wmsg=$j." AM";
					                
					        if ($wsuspendido)    
					           echo "<td align=center bgcolor='FEAAA4'><img src=/matrix/images/medical/movhos/plus.png alt='".$wmsg."'>&nbsp".$Mrondas[$i][$j]."<br>".$Mrondas[$i]["fraccion"]."</td>";
					          else
					             echo "<td align=center><img src=/matrix/images/medical/movhos/plus.png alt='".$wmsg."'>&nbsp".$Mrondas[$i][$j]."<br>".$Mrondas[$i]["fraccion"]."</td>"; 
				            $wtotal = $wtotal + $Mrondas[$i][$j];
			               }
				      $j++;
			         }
			      echo "<td align=right><b>".$wtotal."&nbsp;".$Mrondas[$i]["fraccion"]."</b></td>";
			      echo "</tr>";
		          $i++;   
		         }
		    echo "</table>";
		    
		    echo "<br><br>";
		    echo "<center><table>";
		    echo "<tr>";
		    echo "<td><font size=3><A href='Hoja_medicamentos_enfermeria.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'> Retornar</A></font></td>";
		    echo "</tr>";
		    echo "</table></center>";
		   }
		}
	   echo "<br>";
	   echo "<center><table>"; 
	   echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	   echo "</table></center>";
}
include_once("free.php");

/**
 * Se parametriza el centro de costos 1050 y 1051
 * @by: Marlon Osorio
 * @date: 2022/02/21
 * 
*/

?>
