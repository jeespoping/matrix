<html>
<head>
  <title>Reporte de Saldos X Aplicar X Paciente</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:30pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
    	
  </style>
    
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.salxaplixpac.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
</script>


<?php
include_once("conex.php");

/**
* REPORTE DE SALDOS X APLICAR X PACIENTE	                                                   *
*/
// ==========================================================================================================================================
// PROGRAMA				      :Reporte para saber los saldos pendientes de aplicar por paciente.                                             |
// AUTOR				      :Ing. Gustavo Alberto Avendano Rivera.                                                                         |
// FECHA CREACION			  :Octubre 3 DE 2007.                                                                                            |
// FECHA ULTIMA ACTUALIZACION :03 de Octubre de 2007.                                                                                        |
// DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacio y paciente que saldo tiene pendiente de aplicar.   |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// clisur_000003     : Tabla de Centros de costos de clinica del sur.                                                                        |
// farstore_000003   : Tabla de Centros de costos de farmastore.                                                                             |
// root_000041       : Tabla de Tipos de requerimientos.                                                                                     |
// root_000042       : Tabla de Responsables por centro de costos.                                                                           |
// usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
// root_000040       : Tabla de Requerimientos.                                                                                              |
// root_000043       : Tabla de Clases.                                                                                                      |
// root_000049       : Tabla de Estados.                                                                                                     |
// ==========================================================================================================================================
$wactualiz = "2010-09-22";

session_start();
if (!isset($_SESSION['user']))
{
    echo "error";
} 
else
{
    $empresa = 'root';

    

    

    include_once("/root/comun.php");

    $wactualiz="(2010-09-23)";
    
    //==============================================================================================================================================================================================
    // F U N C I O N E S
    //==============================================================================================================================================================================================
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
	      
	  encabezado("REPORTE DE SALDOS X APLICAR X PACIENTE",$wactualiz, "clinica");
     }
    
    function elegir_centro_de_costo()   
     {
	  global $conex;
	  global $wbasedato;
	  global $wtabcco;
	  global $wcco;  
	  
	  
	  global $whora_par_actual;
	  global $whora_par_anterior;
	  
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
	  echo "<tr><td align=center><select name='wcco' size='1' style=' font-size:30px; font-family:Verdana, Arial, Helvetica, sans-serif; height:30px' onchange='enter()'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
     } 
     
     
    function query_articulos($whis, $wing, $wfecha, &$resart, $wart, &$wnom, &$wsus)
     {
	  global $conex;
	  global $wbasedato;
	  global $wcenmez;
	  global $wemp_pmla;
	  
	  	  
	  //Traigo los Kardex GENERADOS con articulos de DISPENSACION 
	  $q = " SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
	      ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C " 
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadart  = '".$wart."'"
	      ." UNION "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION 
	      ." SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
	      ."   FROM ".$wbasedato."_000060 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C " 
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadart  = '".$wart."'";
	  $resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($resart); 
	  
	  if ($num==0)
	     {
		  //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS
	      $q = " SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
	      	  ."   FROM ".$wbasedato."_000054 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C " 
		      ."  WHERE kadhis  = '".$whis."'"
		      ."    AND kading  = '".$wing."'"
		      ."    AND kadfec  = '".$wfecha."'"  
		      ."    AND kadest  = 'on' "
		      ."    AND kadart  = artcod "
		      ."    AND kadori  = 'CM' "
		      ."    AND kadper  = percod "
		      ."    AND kadart  = '".$wart."'"
		      ." UNION "   
			  //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS
		      ." SELECT kadhin, percan, peruni, kadcfr, kadufr, artcom, kadfin, kadcnd, kadobs, kadsus "
		      ."   FROM ".$wbasedato."_000060 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C " 
		      ."  WHERE kadhis  = '".$whis."'"
		      ."    AND kading  = '".$wing."'"
		      ."    AND kadfec  = '".$wfecha."'"  
		      ."    AND kadest  = 'on' "
		      ."    AND kadart  = artcod "
		      ."    AND kadori  = 'CM' "
		      ."    AND kadper  = percod "
		      ."    AND kadart  = '".$wart."'";
		  $resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
		  $num = mysql_num_rows($resart);
	     }    
	  
	  $row = mysql_fetch_array($resart);
	  $wsus = $row[9];                             //Aca se identifica si el medicamento esta SUSPENDIDO
	  if ($num > 0) mysql_data_seek ($resart,0); 
	     
	  //Si es igual a 0 es porque el articulo ya no esta en el kardex (pudo ser suspendido), entonces entro a buscar el nombre del articulo
	  if ($num == 0)
	     {
		  $q = " SELECT artcom "
		      ."   FROM ".$wbasedato."_000026 "
		      ."  WHERE artcod = '".$wart."'";
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
		  $num = mysql_num_rows($res);
		  
		  if ($num==0)   //Si entra articulo NO existe en la 000026
		     {
			  $q = " SELECT artcom "
			      ."   FROM ".$wcenmez."_000002 "
			      ."  WHERE artcod = '".$wart."'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
			 }
		     
		   $row = mysql_fetch_array($res);
		   $num = mysql_num_rows($res);
		   
		   if ($num > 0)   //Si entra es porque encontro el nombre
		      $wnom=$row[0];
		     else
	            $wnom="Articulo No existe";
		 }       
	 }      
	//==============================================================================================================================================================================================
	//==============================================================================================================================================================================================
	
	
	//==============================================================================================================================================================================================
	// P R I N C I P A L
	//==============================================================================================================================================================================================
	$wfecha = date("Y-m-d");
     
    echo "<form NAME=salxaplixpac action='rep_salxaplixpac1.php' method=post>";
    
    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";   
    
    mostrar_empresa($wemp_pmla);
    
    if (!isset($wcco))
     {
      elegir_centro_de_costo();
     } 
    else // Cuando ya estan todos los datos escogidos
       {
        $wcco1 = explode('-', $wcco);
        
        echo '<center><h2 class="seccion1"><b>CENTRO DE COSTOS:'.$wcco1[0].'-'.$wcco1[1].'</b></h2></center>'; 
        
        ////////////////////
        ////////////////////
        
        $q = " CREATE TEMPORARY TABLE if not exists TEMPO as "
            ." SELECT ubihac, ubisac, spaart as art, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, (spauen-spausa) as sal, artuni "
            ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000004, ".$wbasedato."_000026, root_000036, root_000037, ".$wbasedato."_000029 "
            ."  WHERE ubiald                              <> 'on' "
            ."    AND ubihis                               = spahis "
            ."    AND ubiing                               = spaing " 
            ."    AND spaart                               = artcod "
            ."    AND (spauen-spausa)                      > 0 "
            ."    AND spahis                               = orihis "
            ."    AND spaing                               = oriing "
            ."    AND oriced                               = pacced "
            ."    AND oritid                               = pactid "
            ."    AND ubisac                            LIKE '".trim($wcco1[0])."'"
            ."    AND mid(artgru,1,instr(artgru,'-')-1)    = gjugru "
            ."  UNION "
            ." SELECT ubihac, ubisac, spaart as art, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, (spauen-spausa) as sal, artuni "
            ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000004, ".$wcenmez."_000002, root_000036, root_000037 "
            ."  WHERE ubiald         <> 'on' "
            ."    AND ubihis          = spahis "
            ."    AND ubiing          = spaing " 
            ."    AND spaart          = artcod "
            ."    AND (spauen-spausa) > 0 "
            ."    AND spahis          = orihis "
            ."    AND spaing          = oriing "
            ."    AND oriced          = pacced "
            ."    AND oritid          = pactid "
            ."    AND ubisac          = '".trim($wcco1[0])."'"
            ."  ORDER BY 5,6,1,3";
		//On
        echo $q."<br>";		
			
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

         $q = " SELECT ubihac, ubisac, cconom, art, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, sum(sal), artuni "
             ."   FROM TEMPO, ".$wbasedato."_000011"
             ."  WHERE ubisac  = Ccocod"
             ."    AND ubihis <> '' "
             ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,12 "
             ."  ORDER BY 2,1,6,4";
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num = mysql_num_rows($res); 
        
         echo "<table align=center>";
         
         $row = mysql_fetch_array($res);
         if ($num > 0) mysql_data_seek ($res,1);
         
         $i=1;
         while ($i<=$num)
            {
             $whab = $row[0];
             $wpac = $row[6]." ".$row[7]." ".$row[8]." ".$row[9];
             $whis = $row[4];
             $wing = $row[5];

             echo "<tr></tr>";
             echo "<tr class=encabezadoTabla>";
             echo "<th align=center colspan=1><b>Hab: <font color='FFFF00' size=5>".$whab."</font></b></th>";
             echo "<th align=center colspan=1><b>Historía: ".$whis." - ".$wing."</b></th>";
             echo "<th align=center colspan=9><b>Paciente: ".$wpac."</b></th>";
             echo "</tr>";
             echo "<tr bgcolor='BDBDBD'>";
	         echo "<th align=center colspan=2><b>Articulo</b></th>";
	         echo "<th align=center><b>Unidad</b></th>";
	         echo "<th align=center colspan=2><b>Dosis</b></th>";
	         echo "<th align=center><b>Fecha Inicio</b></th>";
	         echo "<th align=center><b>Hora Inicio</b></th>";
	         echo "<th align=center><b>Frecuencia</b></th>";
	         echo "<th align=center><b>Saldo</b></th>";
	         echo "<th align=center><b>Condición</b></th>";
	         echo "<th align=center><b>Observaciones</b></th>";
	         echo "</tr>";
             
             while ($whis==$row[4] and $i<=$num)
                {  
	             //==========================================================================================================   
	             query_articulos($whis, $wing, $wfecha, &$resart, $row[3], &$wnom, &$wsus);
			     $numart = mysql_num_rows($resart);
			    
			     if ($numart == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
			        {
				     $dia = time()-(1*24*60*60); //Te resta un dia. (2*24*60*60) te resta dos y //asi...
	                 $wayer = date('Y-m-d', $dia); //Formatea dia 
					
	                 query_articulos($whis, $wing, $wayer, &$resart, $row[3], &$wnom, &$wsus);
				     $numart = mysql_num_rows($resart);
				    } 
				    
				 if ($numart > 0)
				   {
					$rowart = mysql_fetch_array($resart);   
					$whin=$rowart[0];                      // Hora Inicio  
					$wfre=$rowart[1]."&nbsp".$rowart[2];   // Frecuencia 
					$wdos=$rowart[3];                      // Dosis
					$wfra=$rowart[4];                      // Fraccion de la dosis
					$wnom=$rowart[5];                      // Descripcion del articulo
					$wfin=$rowart[6];                      // Fecha de Inicio
					$wobs=$rowart[8];                      // Observaciones
					
					if ($rowart[7] != "")
					   {
						$q = " SELECT condes "
					        ."   FROM ".$wbasedato."_000042 "
					        ."  WHERE concod = '".$rowart[7]."'";
					    $rescnd = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					      
					    $rowcnd = mysql_fetch_array($rescnd);
					      
					    $wcnd = $rowcnd[0];                //Condicion
				       }
				      else
				         $wcnd=""; 	   
			       }
			      else
			        {
					 $whin="";   // Hora Inicio  
					 $wfre="";   // Frecuencia 
					 $wdos="";   // Dosis
					 $wfra="";   // Fraccion de la dosis
					 //$wnom="";   // Descripcion del articulo
					 $wfin="";
					 $wcnd="";
					 $wobs="";
			        }    
	             //==========================================================================================================
	                
	             if (is_int ($i / 2))
	                {$wclass = "fila1";} 
	               else
	                 {$wclass = "fila2";}
                 
	             if ($wsus=="on")
	                {
		             echo "<tr class=fondoVioleta>";
			         echo "<td align=center>".$row[3]."</td>";                     //Codigo Articulo
			         echo "<td>".$wnom."</td>";                                    //Nombre Articulo
			         echo "<td>".$row[11]."</td>";                                 //Unidad Medida
			         echo "<td align=center colspan=5><b>Suspendido</b></td>";     //Suspendido
			         echo "<td align=center>".$row[10]."</td>";                    //Saldo
			         echo "<td colspan=2>&nbsp</td>";                              //Espacio en blanco
			         echo "</tr>";
	                }
	               else
	                  {               
			           echo "<tr class=".$wclass.">";
			           echo "<td align=center>".$row[3]."</td>";    //Codigo Articulo
			           echo "<td>".$wnom."</td>";                   //Nombre Articulo
			           echo "<td>".$row[11]."</td>";                //Unidad Medida
			           echo "<td align=right>".$wdos."</td>";       //Dosis
			           echo "<td>".$wfra."</td>";                   //Fraccion de la dosis
			           echo "<td align=center>".$wfin."</td>";      //Fecha de Inicio
			           echo "<td align=center>".$whin."</td>";      //Hora de Inicio
			           echo "<td align=right>C / ".$wfre."S</td>";  //Frecuencia
			           echo "<td align=center>".$row[10]."</td>";   //Saldo
			           echo "<td align=center>".$wcnd."</td>";      //Condicion
			           echo "<td align=left  >".$wobs."</td>";      //Observacion
			           echo "</tr>";
		              } 
	             
	             $row = mysql_fetch_array($res);
	             
	             $i++;
	            } 
	        } // fin del for
	        
	        echo "<br><br><br>";
		    echo "<center><table>";
		    echo "<tr><td><A HREF='rep_salxaplixpac.php?wemp_pmla=".$wemp_pmla."' class=tipo3V>Retornar</A></td></tr>";
		    echo "</table>";
	        
        } // cierre del else donde empieza la impresión
        echo "</table>"; // cierra la tabla o cuadricula de la impresión
        
        echo "<br>";
        echo "<center><table>";
        echo "<tr><td align=center colspan=8><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
        echo "</table>";
    } 

    ?>