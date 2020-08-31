<head>
  <title>CONSOLIDADO DE PRENDAS ENVIADAS Y RECIBIDAS</title>
</head>
<body>
<script type="text/javascript">
	function enter()
	{
	 document.forms.conpreenvrec.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");
  /***********************************************
   * CONSOLIDADO DE PRENDAS ENVIADAS Y RECIBIDAS *
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
 	
  

  

  include_once("root/comun.php");
 
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));
     
  
 	                                            // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="2011-04-18";                      // Aca se coloca la ultima fecha de actualizacion de este programa //
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
  
  encabezado("CONSOLIDADO DE PRENDAS ENVIADAS Y RECIBIDAS",$wactualiz, "clinica");  
       
  
  //==================================================================================================================================================
  //************************************************  E M P I E Z A N   L A S   F U N C I O N E S  ***************************************************
  //==================================================================================================================================================
  function imprimir_lavanderia($warrPrendas)
    {
     global $conex;
	 global $wbasedato;
	 
	 //print_r(array_keys($warrPrendas));
	 
	 $warr_movprendas=array_keys($warrPrendas);    //Trae todas las claves de un arreglo asociativo
	 $wcont=count($warrPrendas);                   //warrPrendas: En esta matriz estan todas las prendas que tuvieron movimiento

	 echo "<center><table>";
		 
	 echo "<tr class=encabezadoTabla>";
	 echo "<th>Código</th>";
	 echo "<th>Descripción</th>";
	 echo "<th>Ropa Sucia</th>";
	 echo "<th>Ropa Limpia</th>";
	 echo "<th>Saldos</th>";
	 echo "<th>% Cumplimiento</th>";
		
	 $wtot_sucia =0;
	 $wtot_limpia=0;
	 $wtot_saldo =0;
		 
	 for ($i=0;$i<$wcont;$i++)
		{
		 if (isset($wclass) and $wclass=="fila1")
		    $wclass="fila2";
		   else
			  $wclass="fila1";
				
		 if (                                                       //Si ninguno de los datos es mayor a cero no lo imprimo
		     ((isset($warrPrendas[$warr_movprendas[$i]][2])) and
		            ($warrPrendas[$warr_movprendas[$i]][2]) > 0) or
			 ((isset($warrPrendas[$warr_movprendas[$i]][3])) and
		            ($warrPrendas[$warr_movprendas[$i]][3]) > 0) or
			 ((isset($warrPrendas[$warr_movprendas[$i]][4])) and
		            ($warrPrendas[$warr_movprendas[$i]][4]) > 0)
			)
			{
			 echo "<tr class='".$wclass."'>";
			 if (isset($warrPrendas[$warr_movprendas[$i]][0])) echo "<td align=center>".$warrPrendas[$warr_movprendas[$i]][0]."</td>";       //Codigo prenda
			 if (isset($warrPrendas[$warr_movprendas[$i]][1])) echo "<td align=left  >".$warrPrendas[$warr_movprendas[$i]][1]."</td>";       //Descripcion prenda
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) echo "<td align=center>".$warrPrendas[$warr_movprendas[$i]][2]."</td>";       //Cantidad Sucia
			 if (isset($warrPrendas[$warr_movprendas[$i]][3])) echo "<td align=center>".$warrPrendas[$warr_movprendas[$i]][3]."</td>";       //Cantidad Limpia
			 if (isset($warrPrendas[$warr_movprendas[$i]][4])) echo "<td align=center>".$warrPrendas[$warr_movprendas[$i]][4]."</td>";       //Saldo prenda
			 if (isset($warrPrendas[$warr_movprendas[$i]][2]) and $warrPrendas[$warr_movprendas[$i]][2] > 0)                                 //Si hay ropa sucia, para que no de division por cero
				 echo "<td align=center>".number_format((($warrPrendas[$warr_movprendas[$i]][3]/$warrPrendas[$warr_movprendas[$i]][2])*100),0,'.',',')." %</td>";  //% de Cumplimiento (ropa limpia/ropa sucia)
				else
				   echo "<td align=center>0 %</td>";                                                                                         //% de Cumplimiento					
			 echo "</tr>";
				
			 if (isset($warrPrendas[$warr_movprendas[$i]][2])) $wtot_sucia=$wtot_sucia+$warrPrendas[$warr_movprendas[$i]][2];
			 if (isset($warrPrendas[$warr_movprendas[$i]][3])) $wtot_limpia=$wtot_limpia+$warrPrendas[$warr_movprendas[$i]][3];
			 if (isset($warrPrendas[$warr_movprendas[$i]][4])) $wtot_saldo=$wtot_saldo+$warrPrendas[$warr_movprendas[$i]][4];
		    }
		}
	 echo "<tr class='encabezadoTabla'>";
	 echo "<td colspan=2>Total </td>";
	 echo "<td align=center>".number_format($wtot_sucia,0,'.',',')."</td>";
	 echo "<td align=center>".number_format($wtot_limpia,0,'.',',')."</td>";
	 echo "<td align=center>".number_format($wtot_saldo,0,'.',',')."</td>";
	 if ($wtot_sucia > 0) 
	    echo "<td align=center>".number_format((($wtot_limpia/$wtot_sucia)*100),0,'.',',')." %</td>";
	   else
          echo "<td align=center>0 %</td>";		   
	 echo "</tr>";
		 
	 echo "</table></center>";
	 echo "<br><br>";
	}	
  //===============================================================================================================================================
  //*************************************************  T E R M I N A N   L A S   F U N C I O N E S  ***********************************************
  //===============================================================================================================================================
  
    
  //FORMA ================================================================
  echo "<form name='conpreenvrec' action='rep_ConsolidadoPrendasEnviadasyRecibidas.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if (!isset($wfec_i) or trim($wfec_i) == "" or !isset($wfec_f) or trim($wfec_f) == "" or !isset($wlav) or trim($wlav) == "" )
     {     
	  echo '<center><table><tr><td align=center><b>SELECCIONE LA LAVANDERIA:</b><br><br></td></tr></table>';

	  $wfecha = date("Y-m-d");
	  
	    //Parámetros de consulta del informe
  		if (!isset ($wfi))
 			$wfi=$wfecha;
  		if (!isset ($wff))
 			$wff=$wfecha;
  		if (!isset ($wl))
 			$wl="";
	  
	  echo "<center><table>";
	  
      $q = " SELECT lavcod, lavnom "
          ."   FROM ".$wbasedato."_000108 ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
			 	 
	  echo "<tr><td align=center colspan=2><select name='wlav'>";
	  echo "<option>% - Todas</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
		  if($wl != $row[0]." - ".$row[1])
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		  else
			echo "<option selected>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
	  echo "</table>";
	  
      echo "<br>";

	  echo "<center><table cellspacing=1>";
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
	  
	  echo "<center><table cellspacing=1 width='310'>";	    
	  echo "<tr><td align=center colspan=2></b><input type='submit' value='Consultar'></b></td><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr></center>";
	  echo "</table>";
     }
    else 
       {
	    $wlav1=explode("-",$wlav);
	   
	    $q = " SELECT mollav, lavnom, tcofac, molpre, predes, SUM(molcan), splsal "
		    ."   FROM ".$wbasedato."_000111 A, ".$wbasedato."_000110 B, ".$wbasedato."_000108, ".$wbasedato."_000101, ".$wbasedato."_000100, ".$wbasedato."_000103, ".$wbasedato."_000109 "
			."  WHERE enllav       LIKE '".trim($wlav1[0])."'"
			."    AND enlfec       BETWEEN '".$wfec_i."' AND '".$wfec_f."' "
			."    AND enllav       = mollav "
			."    AND enlcon       = molcon "
			."    AND enlron       = molron "
			."    AND enlest       = 'on' "
			."    AND enlest       = molest "
			."    AND enllav       = spllav "
			."    AND molpre       = splpre "
			."    AND enlcon       = concod "
			."    AND conrep      != 'on' "
			."    AND contco       = tcocod "
			."    AND molpre       = precod "
			."    AND A.fecha_data = B.fecha_data "
			."  GROUP BY 1, 2, 3, 4, 5, 7";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
		
		if ($num > 0)
		   {
		    $i=1;
			$row = mysql_fetch_array($res);
			$wlavaux=$row[0];
			
            mysql_data_seek ($res,0);
			
			echo "<center><table width='310'>";
			echo "<tr class=titulo>";
			echo "<th align=center><b>Fecha Inicial</b></th>";
			echo "<th align=center><b>Fecha Final</b></th>";
			echo "</tr>";

			echo "<tr class=fila2>";
			echo "<td align=center><b>".$wfec_i."</b></td>";
			echo "<td align=center><b>".$wfec_f."</b></td>";
			echo "</tr>";
			echo "</table></center>";
			echo "<br>";
			while ($i <= $num)
			    {
				 $qlav = " SELECT lavnom "
						."   FROM ".$wbasedato."_000108 "
						."  WHERE lavcod = '".$row[0]."' ";
				 $reslav = mysql_query($qlav,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlav." - ".mysql_error());
				 $rowlav = mysql_fetch_array($reslav);

				 echo "<center><table>";
				 echo "<tr class=subtituloPagina>";
				 echo "<td>Lavanderia: ".$row[0]." - ".$rowlav[0]."</td>";
				 echo "</tr>";
				 echo "</table></center>";
				
				 $wlavaux=$row[0];
				 while ($i <= $num and $wlavaux == $row[0])
				    {
					 $warrPrendas[$row[3]][0]=$row[3];             //Codigo prenda
					 $warrPrendas[$row[3]][1]=$row[4];             //Descripcion prenda
					 switch ($row[2])                 //Tipo de concepto
					    {
						  case "1":
						     {
							  $warrPrendas[$row[3]][2]=$row[5];    //Ropa sucia de cada prenda
							 }
							 break;
						  case "-1":
                             {
							  $warrPrendas[$row[3]][3]=$row[5];    //Ropa limpia de cada prenda
							 }
							 break;
                          default:
                             {

                             }
                             break;							 
						}
					 $warrPrendas[$row[3]][4]=$row[6];             //Saldo de la prenda	en la lavanderia
						
					 $row = mysql_fetch_array($res); 
					 $i++;
					}
				  imprimir_lavanderia($warrPrendas);	
				}
		   }
	   }
    
	echo "</form>";
	  
	if(isset($wfec_f) && isset($wfec_i) && isset($wlav))
	{
		echo "<center><table width='310'>"; 
		echo "<tr>";  
		echo "<td align=center><a href='rep_ConsolidadoPrendasEnviadasyRecibidas.php?wemp_pmla=".$wemp_pmla."&wfi=".$wfec_i."&wff=".$wfec_f."&wl=".$wlav."'><b>Retornar</b></a></td></tr><tr><td align=center><br><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td>"; 
		echo "</tr>";
		echo "</table>";
	}
	
    echo "<br>";
    echo "<center><table>"; 
    echo "<tr></tr>";
    echo "</table>";
    
} // if de register

include_once("free.php");

?>