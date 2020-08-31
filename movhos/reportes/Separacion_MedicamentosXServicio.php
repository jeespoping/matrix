<head>
  <title>SEPARACION MEDICAMENTOS</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
    	
    </style>
  
</head>

<script type="text/javascript">
function enter()
	{
	 document.forms.separacion.submit();
	}
	
function cerrarVentana()
	 {
      window.close()		  
     }
     
</script>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *     REPORTE PARA DISPENSACION POR ARTICULO            *
   *     			 CONEX, FREE => OK				       *
   *********************************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  include_once("root/comun.php");
  

  
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
  		
 
		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2010-08-24)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                                                   
  echo "<br>";				
  echo "<br>";
  
  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  function mostrar_empresa($wemp_pmla)
     {  
	  global $user;   
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;   
	     
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
	         }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
	  encabezado("Separación de Medicamentos ",$wactualiz, "clinica");  
     }
     
     
  function elegir_centro_de_costo()   
     {
	  global $user;   
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz; 
	  global $wcco;  
	  
	  global $whora_par_actual;
	  global $whora_par_sigte;
	  global $whora_par_anterior;
	  
	  
	  //echo "<center><table>";
      //echo "<tr class=encabezadoTabla><td align=center><font size=20>SEPARACION MEDICAMENTOS X SERVICIO</font></td></tr>";
	  //echo "</table>";
	  
	  //Seleccionar CENTRO DE COSTOS
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND (".$wbasedato."_000011.ccohos = 'on' "
          ."     OR  ".$wbasedato."_000011.ccolac = 'on') ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Unidad : </font></td></tr>";
	  echo "</table>";
	  echo "<br><br><br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wcco' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px' onchange='enter()'>";
	  echo "<option>&nbsp</option>";
	  echo "<option>* - Todos</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
     }    
	 
  function query_articulos($wfecha, $res, $wcco)
     {
	  global $conex;
	  global $wbasedato;
	  global $wcenmez;
	  global $wemp_pmla;
	  
      //Traigo los Kardex GENERADOS con articulos de DISPENSACION 
	  $q = " SELECT kadart, artcom, SUM(kadcfr), SUM(kadcdi/deffra), kaduma, artubi, kadufr "
	      ."   FROM ".$wbasedato."_000053 D,".$wbasedato."_000054 A, ".$wbasedato."_000026 B,".$wbasedato."_000059 C " //,".$wbasedato."_000011 E "
	      ."  WHERE D.fecha_data  = '".$wfecha."'"  
	      ."    AND kadest        = 'on' "
	      ."    AND kadart        = artcod "
	      ."    AND kadori        = 'SF' "
	      ."    AND karhis        = kadhis "
	      ."    AND karing        = kading "
	      ."    AND kadsus       != 'on' "
	      ."    AND karcon        = 'on' "
	      //."    AND karcco  = ccocod "
          //."    AND ccolac  = 'on' "
          ."    AND D.fecha_data  =    kadfec "
          ."    AND karcco        LIKE '".$wcco."'"
          ."    AND kadart        =    defart "
          ."    AND karcco        =    kadcco "
          ."    AND defcco        LIKE '%' "
          ."  GROUP BY 1, 2, 5, 6, 7 "
	      ."  UNION "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION 
	      ." SELECT kadart, artcom, SUM(kadcfr), SUM(kadcdi/deffra), kaduma, artubi, kadufr "
	      ."   FROM ".$wbasedato."_000053 D,".$wbasedato."_000060 A, ".$wbasedato."_000026 B,".$wbasedato."_000059 C " //,".$wbasedato."_000011 E "
	      ."  WHERE kadfec       =   '".$wfecha."'"  
	      ."    AND kadest       =    'on' "
	      ."    AND kadart       =    artcod "
	      ."    AND kadori       =    'SF' "
	      ."    AND karhis       =    kadhis "
	      ."    AND karing       =    kading "
	      ."    AND kadsus      != 'on' "
	      ."    AND karcon       =    'on' "
	      //."    AND karcco       =    ccocod "
          //."    AND ccolac       =    'on' "
          ."    AND D.fecha_data =    kadfec "
          ."    AND karcco       LIKE '".$wcco."'"
          ."    AND kadart       =    defart "
          ."    AND karcco       =    kadcco "
          ."    AND defcco       LIKE '%' "
          ."  GROUP BY 1, 2, 5, 6, 7 " 
	      ."  ORDER BY 6, 2 ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}      
    
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
  
  
  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L 
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='separacion' action='Separacion_MedicamentosXServicio.php' method=post>";
  
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  mostrar_empresa($wemp_pmla);
  
  if (!isset($wcco))
     {
      //hora_par();
      elegir_centro_de_costo();
     } 
	else
       {
	    echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
	    
	    $wcco1=explode("-",$wcco);
	    if (trim($wcco1[0])=="*")
	       $wcco1[0]="%";
	      else
	         $wcco1[0]=trim($wcco1[0]); 
	    
	    query_articulos($wfecha, &$res, $wcco1[0]);
		$num = mysql_num_rows($res);
		    
		 /*   if ($num == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
		       {
			    $dia = time()-(1*24*60*60); //Te resta un dia (2*24*60*60) te resta dos y //asi...

			    //$dia_fin = date('d-m-Y', $dia); //Formatea dia 
				$wayer = date('Y-m-d', $dia); //Formatea dia 
				
				query_articulos($whis, $wing, $wayer, &$res, $wcco1[0]);
				$num = mysql_num_rows($res);
			   } 
		 */	
		echo "<center><table>"; 
		echo "<tr class=encabezadoTabla>";
		echo "<th colspan=2><font size=4>Medicamento</font></th>";
		echo "<th colspan=2><font size=4>Dosis</font></th>";
		echo "<th colspan=2><font size=4>Cantidad Unidades</font></th>";
		//echo "<th><font size=4>Unidad</font></th>";
		echo "</tr>";
		    
		$j=1;
		for ($i=1;$i<=$num;$i++)   //Recorre cada uno de los medicamentos
		   {
		    $row = mysql_fetch_array($res);
		        
		    if (is_integer($i/2))
	           $wclass="fila1";
	          else
	             $wclass="fila2"; 
	                  
	        echo "<tr class=".$wclass.">";   
		    echo "<td><font size=4>".$row[0]."</font></td>";               //Codigo Medicamento
		    echo "<td><font size=4>".$row[1]."</font></td>";               //Nombre Medicamento
			echo "<td align=center><font size=4>".$row[2]."</font></td>";  //Cantidad de Dosis
			echo "<td align=center><font size=4>".$row[6]."</font></td>";  //Fraccion de la dosis
			echo "<td align=center><font size=4>".$row[3]."</font></td>";  //Can. Unidades 
			echo "<td align=center><font size=4>".$row[4]."</font></td>";  //Presentacion   
		    echo "</tr>";
	       } 
		echo "</table>";
				
		echo "<br><br>";
		echo "<table>";
		echo "<tr><td><A HREF='Separacion_MedicamentosXServicio.php?wemp_pmla=".$wemp_pmla."&user=".$user."' class=tipo4V>Retornar</A></td></tr>";
		echo "</table>";	  
	   }
	   
  echo "<br><br><br>";
  echo "<table>";   
  echo "<tr><td align=center colspan=4><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
  
} // if de register

?>
</body>