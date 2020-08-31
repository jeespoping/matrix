<head>
  <title>BUSQUEDA DE PACIENTES Y UTILIZACION DE CAMAS POR SERVICIO y HABITACION</title>
</head>
<body BACKGROUND="nubes.gif">
<?php
include_once("conex.php");
  /**********************************************************
   *       UTILIZACION DE CAMAS POR SERVICIO HABITACION     *
   *       PORCENTAJES DE UTILIZACION SERVICIO Y CLINICA    *
   *     				CONEX, FREE => OK				    *
   *********************************************************/
   
   //==================================================================================================================================================//
   // Este programa muestra la ocupación total de la clinica y de cada una de sus unidades de hospitalización, cirugia y urgencias, asi como de los 
   // pacientes que se les hace ingreso y solo vienen a realizarse examenes. Para los pacientes de Urgencias y Cirugia tiene en cuenta el dia anterior 
   // y el actual y que no se hallan facturado.
   // Además cuando se consulta por algún campo de la pantalla, trae los pacientes que fuerón dados de alta y que ya no se encuentran en la habitación,
   // pero que todavia figuran como activos en el sistema.Estos pacientes se identifican con el servicio de "Egresados"
   // También trae los pacientes que ingresan por algún servicio y se van a quedar hospitalizados, pero aún no se les ha asignado habitación, estos
   // pacientes se identifican con el servicio "Sin habitación"
   //==================================================================================================================================================//
   
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

						or die("No se ralizo Conexion");
  

 
  $conexunix = odbc_pconnect('informix','facadm','1201')
  					    or die("No se ralizo Conexion con el Unix");
  					    
 // if ($conexunix == FALSE)
 //    echo "Fallo la conexión UNIX";
  		
 
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Febrero 22 de 2005)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                                                   
  echo "<br>";				
  echo "<br>";
      		
  echo "<form action='Utilizacion_camas.php' method=post>";
  echo "<center><table border=2 BACKGROUND=.'nubes.gif'>";
  echo "<tr><td align=center colspan=8 bgcolor=#fffffff><font size=4 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
  echo "<tr><td align=center colspan=8 bgcolor=#fffffff><font size=3 text color=#CC0000><b>ACTUALIZAR MAESTRO DE ARTICULOS</b></font></td></tr>";
  echo "<tr><td align=center colspan=8 bgcolor=#fffffff><font size=1 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";
 
  $query = " SELECT artcod, gendes, farnom, gencon "
          ."   FROM ivart, ivgen, ivfar "
          ."  WHERE artcge = gencod "
          ."    AND genfar = farcod "
          ."    AND artact= 'S' ";
                        
  $res = odbc_do($conexunix,$query);
	
  $i=1;   
  while(odbc_fetch_row($res))
  	  {
	   $q =  " SELECT artcod, artnom "
	        ."   FROM ".$wbasedato."_000001 "
	        ."  WHERE artcod = '".odbc_result($res,1)."'";
	   $res1 = mysql_query($q,$conex);
	   $num1 = mysql_num_rows($res1);
	    
	   if ($num1 > 0)
	      {
	       $row1 = mysql_fetch_array($res1); 
	       
	       $q = " UPDATE ".$wbasedato."_000001 "
	           ."    SET artgen = '".odbc_result($res,2)."', "
	           ."        artffa = '".odbc_result($res,3)."', "
	           ."        artcon = '".odbc_result($res,4)."'  "
	           ."  WHERE artcod = '".odbc_result($res,1)."'  "; 
	       $res1 = mysql_query($q,$conex);
	       
	       echo "<tr>";
	       echo "<td>".odbc_result($res,1)."</td>";
	       echo "<td>".odbc_result($res,2)."</td>";
	       echo "<td>".odbc_result($res,3)."</td>";
	       echo "<td>".odbc_result($res,4)."</td>";
	       echo "</tr>";
	       $i=$i+1; 
          } 
         
	  }      
  echo "<tr><td colspn=4>Total registros: ".$i."</td></tr>";  
  echo "</table>";  
} // if de register

include_once("free.php");

?>
