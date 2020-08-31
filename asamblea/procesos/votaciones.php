<?php
include_once("conex.php");
include_once("root/comun.php");
?>

<head>
  <title>VOTACIONES ASAMBLEAS HOLDING PMLA Ver. 2009-03-10</title>
</head>
<STYLE type='text/css'>
     H1.miclase {border-width: 1px; border: solid; text-align: center}

BODY            
{
font-family: verdana;
font-size: 10pt;
height: 1024px;
width: 100%;
}
</STYLE>
<BODY TEXT="#000000">
<script type="text/javascript">
	function enter()
	{
	   document.forms.votacion.submit();
	}
	
</script>
<?php


   /**************************************************
	*	            VOTACION ASAMBLEAS               *
	*				CONEX, FREE =>  OK				 *
	**************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	

    
	
	
	$wactualiz = "2018-08-16";
					    
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
       $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
         
    
    //====================================================================================================================================
    //COMIENZA LA FORMA      
    echo "<form name=votacion action='votaciones.php' method=post>";
    
    $wano=date("Y");
    $wfecha=date("Y-m-d"); 
    $hora = (string)date("H:i:s");
    
    // echo "<HR align=center></hr>";
    
    //===================================================================================================================================================
    // QUERY PRINCIPAL 
    //===================================================================================================================================================
    // ACA TRAIGO LA EMPRESA ACTIVA O QUE ESTA EN ASAMBLEA
    //===================================================================================================================================================
    $q = "  SELECT peremp, perano, permes, empdes, Empnas"
        ."    FROM asamblea_000003, asamblea_000004 "
	    ."   WHERE peract = 'on' "
	    ."     AND peremp = empcod "
	    ."     AND empact = 'on' ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
    
	if ($num > 0)
	   {
	    $rowemp = mysql_fetch_array($res); 
	    $wemp=$rowemp[0];
	    $wano=$rowemp[1];
	    $wmes=$rowemp[2];
	    $wnem=$rowemp[3];
	    $wnombreAsamblea=$rowemp['Empnas'];
       
	    $wcliame = consultarAliasPorAplicacion($conex, $wemp, "facturacion");
	   
	
		$logo = "";
		if ( $wemp == "01")
			$logo = "logo_promotora";
		else
			$logo = "logo_promotora";
	   
		encabezado("QUORUM ". $wnombreAsamblea,$wactualiz,$logo);
		
	    //$wvotacion = En esta variable tomo la votacion a consultar de todas las que sean posibles de consultar
	    
	    if (!isset($wvotacion) or trim($wvotacion) == "" or trim($wvotacion) == " " )
	       {     
	        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	        //ACA TRAIGO TODAS LAS POSIBLES VOTACIONES DE LA ASAMBLEA
			$q = " SELECT parcod, pardes "
		        ."   FROM asamblea_000002 "
		        ."  WHERE paremp = '".$wemp."'"
		        ."    AND paract = 'on' "
		        ."    AND parcie = 'on' "
		        ."  GROUP BY 1, 2 "
		        ."  ORDER BY 1, 2 ";
			$res = mysql_query($q,$conex);
		    $num = mysql_num_rows($res);
		    
		    if ($num > 0)
		       {
			    echo "<br>";
			    echo "<br>";
			    echo "<br>";
			    
			    echo "<center><table>";
			    //echo "<tr><td align=center colspan=13><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=240 HEIGHT=140></td></tr>";
			    echo "<tr><td align=center class='encabezadoTabla'><font size=5 text><b>RESULTADO DE VOTACION </b></font></td></tr>";
			    //echo "<tr><td align=center bgcolor=#fffffff><font size=2 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";   
				echo "<tr></tr>";
			    
				echo "<tr class='fila1'><td align=center><font size=5><b>SELECCIONE LA VOTACION QUE DESEA CONOCER: </b></font></td></tr>";
			    echo "<tr class='fila2'><td align=center><select name='wvotacion' onchange='enter()' onblur='enter()'>";
			    echo "<option>*-Todas</option>";    
				for ($i=1;$i<=$num;$i++)
				    {
					 $row = mysql_fetch_array($res); 
			         echo "<option>".$row[0]."-".$row[1]."</option>";
		            }
				echo "</select></td></tr>";
			    
				//echo "<center><tr><td align=center colspan=6 bgcolor=#cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
		       }
		      else
		         {
			      ?>	    
				   <script>
				     alert ("Todas las votaciones estan abiertas, no se pueden consultar los resultados");      
	               </script>
				  <?php   
		         }     	
			echo "</table>";
           }
          else
           { 
	        //===================================================================================================================================================    
		    // ACA TRAIGO TOTAL GENERAL DE ACCIONES DE LA EMPRESA
		    //===================================================================================================================================================
		    $q = "  SELECT count(*), sum(accvap) "
		        ."    FROM asamblea_000001 "
			    ."   WHERE accact = 'on' "
			    ."     AND accemp = '".$wemp."'";
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
			if ($num > 0)
			   {
				$rowtotacc = mysql_fetch_array($res); 
				
				echo "<br>";
				echo "<br>";
			    
				
			    echo "<center><table>";
			    //echo "<tr><td align=center colspan=13><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=240 HEIGHT=140></td></tr>";
			    echo "<tr class='encabezadoTabla'><td align=center><font size=5 text><b>".$wnem." - ".$wano."</b></font></td></tr>";
			    // echo "<tr><td align=center bgcolor=CCCCCC><font size=5 text color=#993399><b>QUORUM ASAMBLEA ".$wnem." - ".$wano."</b></font></td></tr>";
			    echo "</table>";
			    
			    
			    echo "<br>";
			    
			    echo "<center><table>";
			    
			    echo "<th class='encabezadoTabla' align=center>&nbsp</th>";
			    echo "<th class='encabezadoTabla' align=center><font size=5>Cantidad</font></th>";
			    echo "<th class='encabezadoTabla' align=center><font size=5>Total Participaci&oacute;n<br>o Acciones</font></th>";
			    echo "<th class='encabezadoTabla' align=center><font size=5>%</font></th>";
			    
			    echo "<tr class='fila1'>";
			    echo "<td align=left><font size=5><b>Total socios: </b></font></td>";
			    echo "<td align=right><font size=5><b>".$rowtotacc[0]."</b></font></td>";
			    echo "<td align=center><font size=5><b>".$rowtotacc[1]."</b></font></td>";
			    echo "<td align=right><font size=5><b>".number_format((($rowtotacc[1]/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
			    echo "</tr>";
			    
			    //Aca traigo los socios presentes
			    $q = " SELECT count(*), sum(accvap) "
		        	."   FROM asamblea_000001, asamblea_000005 "
		        	."  WHERE movemp = '".$wemp."'"
		        	."    AND movano = '".$wano."'"
		        	."    AND movmes = '".$wmes."'"
		        	."    AND movcac = acccod "
		        	."    AND Accemp = '".$wemp."'"
		        	."    AND movcpa = 'PR' "
		        	."    AND movdel = 'NO' "
		        	."    AND movval = 'S' ";    
			    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
			    
				if ($num > 0)
				   {
					$rowtotquorum  = mysql_fetch_array($res);    
					$wtotcanasiste = $rowtotquorum[0];
					$wtotporasiste = $rowtotquorum[1];
				    			    
					//Aca traigo los socios que delegaron en otro
				    $q = " SELECT count(*), sum(accvap) "
			        	."   FROM asamblea_000001, asamblea_000005 "
			        	."  WHERE movemp = '".$wemp."'"
			        	."    AND movano = '".$wano."'"
			        	."    AND movmes = '".$wmes."'"
			        	."    AND movcac = acccod "
			        	."    AND Accemp = '".$wemp."'"
			        	."    AND movcpa = 'PR' "
			        	."    AND movdel != 'NO' "
			        	."    AND movval = 'S' "; 
			        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    $num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error())    
				    if ($num > 0)
				       {
					    $rowtotquorum  = mysql_fetch_array($res);
					    $wtotcandelegados=$rowtotquorum[0];   
					    $wtotpordelegados=$rowtotquorum[1];
				       }
				      else
				         {
				          $wtotcandelegados=0; 
				          $wtotpordelegados=0;
			             }       
				        
				    echo "<tr class='fila2'>";
				    echo "<td align=left><font size=5><b>Socios Asistentes: </b></font></td>";
				    echo "<td align=right><font size=5><b>".$wtotcanasiste."</b></font></td>";
				    echo "<td align=center><font size=5><b>".$wtotporasiste."</b></font></td>";
				    echo "<td align=right><font size=5><b>".number_format((($wtotporasiste/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
				    echo "</tr>";
				    
				    echo "<tr class='fila1'>";
				    echo "<td align=left><font size=5><b>Socios que delegaron: </b></font></td>";
				    echo "<td align=right><font size=5><b>".$wtotcandelegados."</b></font></td>";
				    echo "<td align=center><font size=5><b>".$wtotpordelegados."</b></font></td>";
				    echo "<td align=right><font size=5><b>".number_format((($wtotpordelegados/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
				    echo "</tr>";
				    
				    echo "<tr class='encabezadoTabla'>";
				    echo "<td align=left><font size=5><b>Quorum: </b></font></td>";
				    echo "<td align=right><font size=5><b>".($wtotcanasiste+$wtotcandelegados)."</b></font></td>";
				    echo "<td align=center><font size=5><b>".($wtotporasiste+$wtotpordelegados)."</b></font></td>";
				    echo "<td align=right><font size=5><b>".number_format(((($wtotporasiste+$wtotpordelegados)/$rowtotacc[1])*100),4,'.',',')."</b></font></td>";
				    echo "</tr>";
				   } 
				 echo "</table>";  
	          } //Fin del then si hay accionistas con porcentaje de participacion
	        
	        //===================================================================================================================    
	        //ACA COMIENZA EL ANALISIS DE LAS VOTACIONES
	        //===================================================================================================================
	        echo "<br>";   
			echo "<br>";
				
	        echo "<center><table>";
		    //echo "<tr><td align=center colspan=13><img src='/matrix/images/medical/root/clinica.jpg' WIDTH=240 HEIGHT=140></td></tr>";
		    echo "<tr><td align=center class='encabezadoTabla'><font size=5 text><b>RESULTADO DE VOTACION </b></font></td></tr>";
		    echo "</table>";
			    
		    if ($wvotacion=="*-Todas")
		       $wvotacion[0]="%";
		      else
		         $wvotacion=explode("-",$wvotacion); 
	             
		         
		    //Aca traigo todos los socios asistentes y que delegaron con sumatoria respectiva de la participacion o acciones      
		    $q = " SELECT movcpa, pardes, movval, count(*), SUM(accvap), partiv, paresc "
		        ."   FROM asamblea_000002, asamblea_000005, asamblea_000001 "
		        ."  WHERE movemp = '".$wemp."'"
		        ."    AND movano = '".$wano."'"
		        ."    AND movmes = '".$wmes."'"
		        ."    AND movcpa = parcod "
		        ."    AND movemp = paremp "
		        ."    AND movcac = acccod "
		        ."    AND Accemp = '".$wemp."'"
		        ."    AND movcpa like '".trim($wvotacion[0])."'"
		        ."    AND parcie = 'on' "
		        ."  GROUP BY 1, 2, 3, 6, 7 "
		        ."  ORDER BY 1, 2, 3 ";    
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
		    
			if ($num > 0)
			   {
				$i=1;  
				$row=mysql_fetch_array($res); 
				while ($i <= $num)
				  {
				   $wvotaux=$row[0];
				   $wtipvot=explode("-",$row[5]);
				   $wescanos=$row[6];
				   
				   echo "<center><table border='6'>";
				   
				   if ($wtipvot[0]=="S" or $wtipvot[0]=="C")
				      {
					   echo "<tr class='encabezadoTabla'>";
				       echo "<td align=center colspan=5><font size=5><b>".$row[1]."</b></font></td>";
				       echo "</tr>";
				       echo "<br>";
		              }
		              
			       if ($wtipvot[0]=="S")
			          {
				       echo "<th class='encabezadoTabla' align=center><font size=5>&nbsp</font></th>";
				       echo "<th class='encabezadoTabla' align=center><font size=5>Votos</font></th>";
				       echo "<th class='encabezadoTabla' align=center><font size=5>Cantidad Participaci&oacute;n<br> o Propiedad</font></th>";
					   $wtotvot=0;
					   $wtotpor=0;
					   while ($i<=$num and $wvotaux==$row[0]) 
					     {
						  //Aca traigo la descripci�n de cada una de las opciones votadas.   
						  $q= " SELECT vpades "
						     ."   FROM asamblea_000006 "
						     ."  WHERE vpaemp = '".$wemp."'"
						     ."    AND vpacpa = '".$row[0]."'"
						     ."    AND vpacod = '".$row[2]."'";
						  $res_opc = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			              $num_opc = mysql_num_rows($res_opc);
			              if ($num_opc > 0)
			                 {
				              $row_opc=mysql_fetch_array($res_opc);
			                  $wopc = $row_opc[0];  
		                     } 
			                else
			                   $wopc="Opcion ".$row[2]; 
			              
							if ($fila_lista=='Fila1')
								$fila_lista = "Fila2";
							else
								$fila_lista = "Fila1";
							
						  echo "<tr class='".$fila_lista."'>";
						  echo "<td align=right><font size=5><b>".$wopc."</b></font></td>";
					      echo "<td align=right><font size=5><b>".$row[3]."</b></font></td>";
					      echo "<td align=right><font size=5><b>".number_format($row[4],4,'.',',')."</b></font></td>";
					      echo "</tr>";   
					      
					      $wtotvot=$wtotvot+$row[3];
					      $wtotpor=$wtotpor+$row[4];
					      
					      $row=mysql_fetch_array($res);
					     }
					   echo "<tr class='encabezadoTabla'>";
					   echo "<td align=left><font size=5<b>Total Votaci&oacute;n :</b></font></td>";
					   echo "<td align=right><font size=5><b>".$wtotvot."</b></font></td>";
					   echo "<td align=right><font size=5><b>".number_format($wtotpor,4,'.',',')."</b></font></td>";
					   echo "</tr>";
				      }
				      
				   if ($wtipvot[0]=="C")
				      {
					   //Con este query traigo el total de la votacion   
					   $q =  " SELECT movcpa, pardes, count(*), SUM(accvap) "
					        ."   FROM asamblea_000002, asamblea_000005, asamblea_000001 "
					        ."  WHERE movemp = '".$wemp."'"
					        ."    AND movano = '".$wano."'"
					        ."    AND movmes = '".$wmes."'"
					        ."    AND movcpa = parcod "
					        ."    AND movemp = paremp "
					        ."    AND movcac = acccod "
					        ."    AND Accemp = '".$wemp."'"
					        ."    AND movcpa like '".trim($wvotacion[0])."'"
					        ."    AND parcie = 'on' "
					        ."  GROUP BY 1, 2 "
					        ."  ORDER BY 1, 2 ";    
					   $restot = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					   $numtot = mysql_num_rows($restot);    //or die (mysql_errno()." - ".mysql_error())   
					   if ($numtot > 0)
			             {
				          $rowtot=mysql_fetch_array($restot);   
				          $wtot_votos=$rowtot[2];   //Total de votos numeral
				          $wtot_porce=$rowtot[3];   //Total de votacion porcentual con respecto a la asistencia o quorum
			             }    
					      
					   echo "<tr>";
					   echo "<td align=left class='fila1' colspan=3><font size=5><b>Total Votaci&oacute;n Nominal </b></font></td>";
					   echo "<td align=right class='fila2' colspan=1><font size=5><b>".(int)($wtot_votos)."</b></font></td>"; 
					   echo "</tr><tr>";
					   echo "<td align=left class='fila1' colspan=3><font size=5><b>Total Votaci&oacute;n Acciones </b></font></td>";
					   echo "<td align=right class='fila2' colspan=1><font size=5><b>".(int)($wtot_porce)."</b></font></td>";  
					   echo "</tr><tr>";
					   echo "<td align=left class='fila1' colspan=3><font size=5><b>% Acciones que Votaron (con respecto al total de acciones) </b></font></td>";
					   echo "<td align=right class='fila2' colspan=1><font size=5><b>".number_format(($wtot_porce/$rowtotacc[1])*100,4,'.',',')."</b></font></td>";  
					   echo "</tr><tr>";
					   echo "<td align=left class='fila1' colspan=3><font size=5><b>Nro Esca&ntilde;os </b></font></td>"; 
					   echo "<td align=right class='fila2' colspan=1><font size=5><b>".(int)($wescanos)."</b></font></td>";
					   echo "</tr><tr>";
					   echo "<td align=left class='fila1' colspan=3><font size=5><b>Cuociente (Total Votacion Acciones/Nro Esca&ntilde;os) </b></font></td>"; 
					   echo "<td align=right class='fila2' colspan=1><font size=5><b>".(int)($wtot_porce/$wescanos)."</b></font></td>";
					   echo "</tr>";
					   
					   $wcuociente=(int)($wtot_porce/$wescanos);
					   
					   echo "<th class='encabezadoTabla' align=center><font size=5>&nbsp</font></th>";
				       //echo "<th bgcolor=#993399 align=center><font size=5 color=FFFFFF>Cant. Votos</font></th>";
				       echo "<th class='encabezadoTabla' align=center><font size=5>Nro de Acciones</font></th>";
				       echo "<th class='encabezadoTabla' align=center><font size=5>Esca&ntilde;os<br>Por Cuociente</font></th>";
				       echo "<th class='encabezadoTabla' align=center><font size=5>Residuo</font></th>";
				       //echo "<th bgcolor=#993399 align=center><font size=5 color=FFFFFF>Esca�os<br>Por Residuo</font></th>";
					   while ($i<=$num and $wvotaux==$row[0]) 
					     {
						  $wtotescanos=$wescanos;
						       
						  //Aca traigo la descripci�n de cada una de las opciones votadas.   
						  $q= " SELECT vpades "
						     ."   FROM asamblea_000006 "
						     ."  WHERE vpaemp = '".$wemp."'"
						     ."    AND vpacpa = '".$row[0]."'"
						     ."    AND vpacod = '".$row[2]."'";
						  $res_opc = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			              $num_opc = mysql_num_rows($res_opc);
			              if ($num_opc > 0)
			                 {
				              $row_opc=mysql_fetch_array($res_opc);      
			                  $wopc = $row_opc[0];  
		                     } 
			                else
			                   $wopc="Opcion ".$row[2];
						  
							if ($fila_lista=='Fila1')
								$fila_lista = "Fila2";
							else
								$fila_lista = "Fila1";
						  echo "<tr class='".$fila_lista."'>";
						  echo "<td align=center><font size=5><b>".$wopc."</b></font></td>";
						  //echo "<td align=center bgcolor=#99CCFF><font size=5 color=993399><b>".$row[3]."</b></font></td>";                     //Votos
					      echo "<td align=center><font size=5><b>".$row[4]."</b></font></td>";                     //Acciones
					      echo "<td align=center><font size=5><b>".(int)($row[4]/$wcuociente)."</b></font></td>";  //Elegidos por cuociente
					      $wesc_cuo_pla=((int)($row[4]/$wcuociente));                               //Nro de esca�os por cuociente por plancha
					      $wtotescanos=$wtotescanos-((int)($row[4]/$wcuociente));                   //Total esca�os que quedan
					      if($wtotescanos>0)
					        {
						     $wres_pla=$row[4]-((int)($row[4]/$wcuociente)*$wcuociente);            //Residuo de la plancha
						     $wnro_esc_res=(int)(($row[4]-($wesc_cuo_pla*$wcuociente))/$wres_pla);  //Nro de esca�os por residuo
						        
					         echo "<td align=center><font size=5><b>".number_format($wres_pla,4,'.',',')."</b></font></td>";      //Residuo por plancha
					         //echo "<td align=right bgcolor=#99CCFF><font size=5 color=993399><b>".number_format($wnro_esc_res,0,'.',',')."</b></font></td>";  //Elegidos por residuo
					         $wtotescanos=$wtotescanos-((int)($row[4]/$wcuociente));
				            } 
					      echo "</tr>";   
					      $row=mysql_fetch_array($res);
					     }
					   echo "</tr>";     
					  }        
				   $i++;	   
				   echo "</table>"; 
				   echo "<br>";  
				   echo "<br>";
				  }	
			   }
			  else
			     {
			      ?>	    
				   <script>
				     alert ("No existen votos para ninguna de las votaciones");      
	               </script>
				  <?php   
		         }  
	      }	   	   
          
      } //Fin del then si hay empresa abierta o asamblea abierta
    
   echo "</form>";
   
}
include_once("free.php");
?>
