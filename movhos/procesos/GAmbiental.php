<html>
<head>
  <title>GESTION AMBIENTAL</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	 document.forms.gambiental.submit();
	}
	
	function cerrarVentana()
	 {
      window.close();		  
     }
</script>	
<?php
include_once("conex.php");

  /***********************************************
   *              GESTION AMBIENTAL              *
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
  
  $wactualiz="(Julio 06 de 2012)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                   // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
            
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//DESCRIPCION  Abril 7 de 2009                                                                                                             \\
//=========================================================================================================================================\\
//                                                                                                                                         \\
//                                                                                                                                         \\
//=========================================================================================================================================\\	                                                         
	                                                           
	                                                             
//=========================================================================================================================================\\
//=========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//  Julio 6 de 2012 (Viviana Rodas) Se agregaron las funciones consultaCentrosCostos y dibujarSelect que listan 
// los centros de costos en orden alfabetico, de un grupo seleccionado y dibujarSelect que construye el select 
// de dichos centros de costos. //                                                                                                                                                    //                                                                                                                     \\
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
  

  //=====================================================================================================================================================================     
  // F U N C I O N E S
  //=====================================================================================================================================================================
  function grabar_servicio($wcco)
    {
	  global $conex;
	  global $wbasedato;   
	  global $wfec;
	  global $whora;
	  global $wgrabar;
	  global $wusuario;
	 
	  global $wpeso;
	  global $wcant;
	  global $num_item;
	  global $witem;  
	    
	  for ($i=1;$i<=$num_item;$i++)
	     {
		  $q = " DELETE FROM ".$wbasedato."_000089 "
		      ."  WHERE mamcco = '".trim($wcco)."'"
		      ."    AND mamtit = '".trim($witem[$i])."'" 
		      ."    AND mamfec = '".$wfec."'"
		      ."    AND mamest = 'on' ";
		  $resit = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      
		  //Aca hago los insert en la tablas
		  $q = " INSERT INTO ".$wbasedato."_000089 (   Medico       ,   Fecha_data,   Hora_data,   mamcco        ,   mamtit             ,   mampes       ,   mamcbo       ,   mamfec  ,   mamusu      , mamest,      Seguridad   ) "
		      ."                            VALUES ('".$wbasedato."','".$wfec."'  ,'".$whora."','".trim($wcco)."','".trim($witem[$i])."', ".$wpeso[$i]." , ".$wcant[$i]." ,'".$wfec."','".$wusuario."', 'on'  , 'C-".$wusuario."') ";
		  $err = mysql_query($q,$conex) or die (mysql_errno().$q." - ".mysql_error());
	     }
	}    
  
  
  
  function consultar_servicio($wcco)
    {
	 global $conex;
	 global $wbasedato;   
	 global $wfec;
	 global $whora;
	 global $wgrabar;
	 global $wusuario;
	 
	 global $wpeso;
	 global $wcant;
	 global $num_item;
	 global $witem;
	    
	 $q = " SELECT rcttip, ambdes "
         ."   FROM ".$wbasedato."_000090, ".$wbasedato."_000088 "
         ."  WHERE rctcco = '".$wcco."'"
         ."    AND rcttip = ambcod "
         ."    AND rctest = 'on' "
         ."    AND ambest = 'on' ";
     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $num_item = mysql_num_rows($res);
    
     if ($num_item > 0)
        {
         echo "<br><br>";
         echo "<table align=center>";
         
         echo "<tr class='encabezadoTabla'>";
         echo "<th colspan=2>Item Ambiental</th>";
         echo "<th>Peso</th>";
         echo "<th>Cantidad<br>Bolsas</th>";
         echo "</th>";
         echo "</tr>"; 
	     for ($i=1;$i<=$num_item;$i++)
	       {
		    if (is_integer($i/2))
               $wclass="fila1";
              else
                 $wclass="fila2";
                      
            $row = mysql_fetch_array($res);
           
            $witem[$i]=$row[0];
	      
            //Busco si el item ya tiene un registro grabado
            $q = " SELECT mampes, mamcbo "
                ."   FROM ".$wbasedato."_000089 "
                ."  WHERE mamcco = '".trim($wcco)."'"
                ."    AND mamtit = '".trim($witem[$i])."'" 
                ."    AND mamfec = '".$wfec."'"
                ."    AND mamest = 'on' ";
            $resit = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		    $numit = mysql_num_rows($resit);
		  
		    if ($numit > 0)
		       {
			    $rowit = mysql_fetch_array($resit);
			     
			    $wpeso[$i]=$rowit[0];
			    $wcant[$i]=$rowit[1];
		       }
		      else
		        {
			     $wpeso[$i]=0;   
			     $wcant[$i]=0;
			    }     
		   
            echo "<tr class=".$wclass.">";
	        //echo "<td>".$row[0]."</td>";                                              //Item Codigo
	        echo "<td><center><input type=text name='witem[".$i."]' value='".$witem[$i]."' size=3 DISABLED></td>";   //Peso
	        echo "<td>".$row[1]."</td>";                                              //Item Descripcion 
	        echo "<td><center><input type=text name='wpeso[".$i."]' value='".$wpeso[$i]."' size=3></td>";   //Peso
	        echo "<td><center><input type=text name='wcant[".$i."]' value='".$wcant[$i]."' size=3></td>";   //Peso
	        echo "</tr>";
	       
	        echo "<input type='HIDDEN' name='witem[".$i."]' value='".$witem[$i]."'>";
	       }
	     echo "</table>";
	    
	     echo "<input type='HIDDEN' name='num_item' value='".$num_item."'>";
	    
	     echo "<br><br>";  
	     echo "<table align=center>";  
	     echo "<tr>"; 
		 echo "<td align=center bgcolor=#cccccc colspan=4><input type='checkbox' name=wgrabar><input type='submit' value='Grabar'></td>";      
	     echo "</tr>";
	     echo "</table>";
	    }   
	}    
	
  //=====================================================================================================================================================================	

       
  encabezado("Gestión Ambiental", $wactualiz, 'clinica');
  
       
  //FORMA ================================================================
  echo "<form name='gambiental' action='GAmbiental.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='wcencam' value='".$wcencam."'>";
  
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  global $wgrabar;   
     
  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
  if (!isset($wcco) or trim($wcco) == "")
     {     
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
       //primer centro llamada a la funcion que lista los centros de costo     
	  $cco="Todos";
	  $sub="off";
	  $tod="";
		//$cco=" ";
	  $ipod="off";
	  $filtro="--";
	  $centrosCostos = consultaCentrosCostos($cco);
	  echo "<center><table align='center' border=0 >";
	  $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
	  echo $dib;
	  echo "</table></center>";
      
	  echo "<table align=center width=402>";
      echo "<tr>";
      echo "<td align=center class=fila1 width=150>Fecha a Registrar: </td>";
      echo "<td align=center class=fila2>";
      campofechaDefecto("wfec",$wfecha);
      echo "</td>";
      echo "</tr>";
        
	  echo "<center><tr><td align=center colspan=4 bgcolor=cccccc></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else  //Esta setiado CCO y Fecha
       {
	    
	    echo "<input type='hidden' name='wcco' value='".$wcco."'/>";
	       
	    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        		
		//*********************llamado a las funciones que listan los centros de costos y la que dibuja el select************************
		$cco="Todos";
		$sub="on";
		$tod="";
		//$cco=" ";
		$ipod="off";
		$filtro="--";
		$centrosCostos = consultaCentrosCostos($cco);
		echo "<center><table align='center' border=0 >";
		$dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		echo $dib;
		echo "</table></center>";
          
        echo "<table align=center width=402>";
        echo "<tr>";
        echo "<td align=center class=fila1 width=150>Fecha a Registrar: </td>";
        echo "<td align=center class=fila2>";
        campofechaDefecto("wfec",$wfec);
        echo "</td>";
        echo "</tr>";
	    echo "</table>";
        
	    $wcco1=explode("-",$wcco);   
	       
	    if ($wfecha==$wfec)
	       {
		    if (isset($wgrabar))
		       grabar_servicio($wcco1[0]); 
		   }    
		         
	    consultar_servicio($wcco1[0]);
         
        echo "<br><br>";
        echo "<table align=center>";     
        echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";   
        echo "</table>";
       }    
    
    
} // if de register



include_once("free.php");

?>
