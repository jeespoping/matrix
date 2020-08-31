<head>
  <title>CONSULTA MOMENTOS DEL ALTA POR PACIENTE</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	 document.forms.conmomalt.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     }
</script>	
<?php
include_once("conex.php");

  /***********************************************
   *          CONSULTA MOMENTOS DEL ALTA         *
   *     		  CONEX, FREE => OK		         *
   ***********************************************
   *             Modificaciones
   *2019-02-15 Arleyda I.C. Migración realizada */

session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  

  include_once("root/comun.php");  
	                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2009-04-29)";                 // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                           
	               
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
	         
	      $winstitucion=$row[2];   
         }  
     }
    else
       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";      
  
  
  //=============================================================================================================   
    function recorrido_historia($fhis, $fing)
      {
	   global $wbasedato;
	   global $conex;
	   
	   
	   $q = " SELECT fecha_data, hora_data, eyrsor, eyrsde, eyrhor, eyrhde, eyrtip "
	       ."   FROM ".$wbasedato."_000017 "
	       ."  WHERE eyrhis = '".$fhis."'"
	       ."    AND eyring = '".$fing."'"
	       ."  ORDER BY 1, 2, 3 ";
	   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());     
	   $wnum = mysql_num_rows($res);  
	   
	   if ($wnum > 0)
	      {
		   echo "<br>";   
		   echo "<center><table>";   
		   
		   echo "<tr class=seccion1>";
		   echo "<th colspan=9><font size=4><b>MOVIMIENTOS HOSPITALARIOS QUE HA TENIDO EL PACIENTE</b></font></th>";
		   echo "</tr>";
		   
		   echo "<tr class=encabezadoTabla>";
		   echo "<th colspan=4>Servicio Origen</th>";
		   echo "<th colspan=4>Servicio Destino</th>";
		   echo "<th colspan=1 rowspan=2>Evento</th>";
		   echo "</tr>";
		   
		   echo "<tr class=encabezadoTabla>";
		   echo "<th>C.Costo</th>";
		   echo "<th>Habitacion</th>";
		   echo "<th>Fecha</th>";
		   echo "<th>Hora</th>";
		   echo "<th>C.Costo</th>";
		   echo "<th>Habitacion</th>";
		   echo "<th>Fecha</th>";
		   echo "<th>Hora</th>";
		   echo "</tr>";
		      
		   for ($i=1;$i<=$wnum;$i++)
		      {   
			   if ($i % 2 == 0)
			      $wcf = "FFFFFF";
			     else
			        $wcf = "99CCFF";    
			      
			   echo "<tr>";   
		       $row = mysql_fetch_array($res);  
		       
		       echo "<td bgcolor=".$wcf.">".$row[2]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[4]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[0]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[1]."</td>";
		       
		       echo "<td bgcolor=".$wcf.">".$row[3]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[5]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[0]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[1]."</td>";
		       echo "<td bgcolor=".$wcf.">".$row[6]."</td>";
	          }
	       echo "</table>";
	       echo "<br>";    
		  }          
      } 
    //=============================================================================================================     
  
    
  encabezado("CONSULTA MOMENTOS DEL ALTA",$wactualiz, "clinica");
  
  //FORMA ================================================================
  echo "<form name='altas' action='conmomalt.php' method=post>";
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  
  if (strpos($user,"-") > 0)
     $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 

  //===============================================================================================================================================
  //ACA COMIENZA EL MAIN DEL PROGRAMA   
  //===============================================================================================================================================
   if ( (!isset($whis) || trim($whis) == "" ) && (!isset($wced) || trim($wced) == "") )
     {     
	  echo "<center><table>";
	  echo "<tr class=seccion1>";
	  echo "<td><b>INTRODUZCA LA HISTORIA:</b>";
	  
	  ?>	    
	    <script>
	      function ira(){document.conmomalt.whis.focus();}
	    </script>
	  <?php    
      echo "<td align=center><INPUT TYPE='text' NAME='whis' SIZE=7></td>"; 
      echo "</tr>";
      echo "<tr  class=seccion1><td><b>NO. DE IDENTIFIACION:</b>";
      echo "<td align=center><INPUT TYPE='text' NAME='wced' SIZE=7></td>";
      echo "</table>";
      
      echo "<br>";
      echo "<center><table>";
	  echo "<tr><td align=center colspan=2></b><input type='submit' value='ENTRAR'></b></td></tr></center>";
	  echo "</table>";
     }
    else 
      { 
	   if (!isset($wver) or $wver != "on")
	      { 
	      	
	      	if( $whis != "" ){    
			   $q = " SELECT orihis, ubiing, pacno1, pacno2, pacap1, pacap2, ubifad, oriced "
			       ."   FROM root_000037, root_000036, ".$wbasedato."_000018 "
			       ."  WHERE oriori = '".$wemp_pmla."'"
			       ."    AND orihis = '".$whis."'"
			       ."    AND oriced = pacced "
			       ."    AND ubihis = orihis ";
	      	}
	      	else if( $wced != "" ){
	      		$q = " SELECT orihis, ubiing, pacno1, pacno2, pacap1, pacap2, ubifad, oriced "
			       ."   FROM root_000037, root_000036, ".$wbasedato."_000018 "
			       ."  WHERE oriori = '".$wemp_pmla."'"
			       ."    AND oriced = '".$wced."'"
			       ."    AND oriced = pacced "
			       ."    AND ubihis = orihis ";
	      	}
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $num = mysql_num_rows($res); 
		   
		   
		   if ($num > 0)
		     {
			  echo "<center><table>";  
			  echo "<tr class=encabezadoTabla>"; 
			  echo "<th>Historia</th>";
			  echo "<th>Ingreso</th>";
			  echo "<th>No. de Identificacion</th>";
			  echo "<th>Paciente</th>";
			  echo "<th colspan=2>Fecha de Egreso</th>";
			  echo "</tr>";
			  
			  echo "<tr></tr>";
			     
			  
			  for ($i=1;$i<=$num;$i++)
			     {
				  if (is_integer($i/2))
	                 $wclass="fila1";
	                else
	                   $wclass="fila2";   
			      $row = mysql_fetch_array($res);
			      
			      $wpac=$row[2]." ".$row[3]." ".$row[4]." ".$row[5];
			      
			      $wing=$row[1];
			      $wfeg=$row[6];
			      $wced=$row[7];
			      $whis=$row[0];
			      
			      echo "<tr class=".$wclass.">"; 
			      echo "<td align=center>".$whis."</td>";  
			      echo "<td align=center>".$wing."</td>";
			      echo "<td align=center>".$wced."</td>";
			      echo "<td align=center>".$wpac."</td>";
			      echo "<td align=center>".$wfeg."</td>";
//			      echo "<td align=center><font size=3><b><A href='conmomalt.php?whis=".$whis."&wing=".$wing."&wemp_pmla=".$wemp_pmla."&wpac=".$wpac."&wver=on '>Ver</A></b></font></td>";
			      echo "<td align=center><font size=3><b><A href='conmomalt.php?whis=".$whis."&wing=".$wing."&wemp_pmla=".$wemp_pmla."&wpac=".$wpac."&wver=on&wced=$wced '>Ver</A></b></font></td>";
			      echo "</tr>"; 
			     }
			  echo "</table>";  
			  echo "<center><table>"; 
		      echo "<td align=centercolspan=11><A href='conmomalt.php?wemp_pmla=".$wemp_pmla."'><b><font size=3 color=660099>Retornar</font></b></A></td>";
		      echo "</table>";
	         }
          }
         else
            {
	         $q = " SELECT ubifap, ubihap, ubifad, ubihad, ".$wbasedato."_000022.fecha_data, ".$wbasedato."_000022.hora_data, "
	             ."        cuefpa, cuehpa, cueobs, ubisac, ubihac, cueffa, cuehfa, cuefac "
	             ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000022 "
	             ."  WHERE ubihis = '".$whis."'"
	             ."    AND ubiing = '".$wing."'"
	             ."    AND ubihis = cuehis "
	             ."    AND ubiing = cueing ";
	         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		     $num = mysql_num_rows($res);
		     if ($num > 0)
		        {
			     $row = mysql_fetch_array($res);
			     
			     //$wcolor="33FFFF";
			     $wcolor="99FFFF";
			     
			     $wfap=$row[0];
			     $whap=$row[1];
			     $wfad=$row[2];
			     $whad=$row[3];
			     $wfok=$row[4];
			     $whok=$row[5];
			     $wfpa=$row[6];
			     $whpa=$row[7];
			     $wobs=$row[8];
			     $wseg=$row[9];
			     $wheg=$row[10];
			     $wffa=$row[11];
			     $whfa=$row[12];
			     $wfac=$row[13];
			     
			     $q = " SELECT cconom "
			         ."   FROM ".$wtabcco
			         ."  WHERE ccocod = '".$wseg."'";
			     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
			     $row = mysql_fetch_array($res);
			     
			     echo "<center><table>"; 
			     echo "<tr class=seccion1><td><b>Historia: </b>".$whis." - ".$wing."</td></tr>";
			     echo "<tr class=seccion1><td><b>No. de Identificacion: </b>".$wced."</td></tr>";
			     echo "<tr class=seccion1><td><b>Paciente: </b>".$wpac."</td></tr>";
			     echo "<tr class=seccion1><td><b>Servicio de egreso: </b>".$wseg." - ".$row[0]."</td></tr>";
			     echo "<tr class=seccion1><td><b>Habitacion de Egreso: </b>".$wheg."</td></tr>";
			     echo "</table>";
			     
			     recorrido_historia($whis,$wing);
			     
			     echo "<center><table>"; 
			     
			     echo "<tr class=encabezadoTabla>";
			     echo "<th colspan=2 rowspan=2>En Proceso de Alta</th>";
			     echo "<th colspan=2 rowspan=2>Devolución</th>";
			     echo "<th colspan=6>Facturacion</th>";
			     echo "<th colspan=2 rowspan=2>Salió de Caja</th>";
			     echo "<th colspan=2 rowspan=2>Alta Definitiva</thd>";
			     echo "</tr>";
			     
			     echo "<tr class=encabezadoTabla>";
			     echo "<th colspan=2>Entró</th>";
			     echo "<th colspan=4>Salió</th>";
			     echo "</tr>";
			     
			     
			     echo "<tr class=encabezadoTabla>";
			     echo "<th colspan=1>Fecha</th>";
			     echo "<th colspan=1>Hora</th>";
			     echo "<th colspan=1>Fecha</th>";
			     echo "<th colspan=1>Hora</th>";
			     echo "<th colspan=1>Fecha</th>";
			     echo "<th colspan=1>Hora</th>";
			     echo "<th colspan=1>Factura</th>";
			     echo "<th colspan=1>observaciones</th>";
			     echo "<th colspan=1>Fecha</th>";
			     echo "<th colspan=1>Hora</th>";
			     echo "<th colspan=1>Fecha</th>";
			     echo "<th colspan=1>Hora</th>";
			     echo "<th colspan=1>Fecha</th>";
			     echo "<th colspan=1>Hora</th>";
			     echo "</tr>";
			     
			     echo "<tr></tr>";
			     echo "<tr></tr>";
			     
			     $q= " SELECT fecha_data, max(hora_data) "
			        ."   FROM ".$wbasedato."_000035 "
			        ."  WHERE denhis = '".$whis."'"
			        ."    AND dening = '".$wing."'"
			        ."    AND fecha_data = '".$wfap."'"
			        ."  GROUP BY 1 "
			        ."  ORDER BY 1, 2 desc ";
			     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		         $num = mysql_num_rows($res);  
		         if ($num > 0)
		            {
			         $row = mysql_fetch_array($res);
			         
			         $wfdv=$row[0];
			         $whdv=$row[1];   
			        }
			       else
			          {
				       $wfdv="NO TUVO";
				       $whdv="DEVOLUCION";    
			          }        
			     
			     echo "<tr class=fila1>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wfap."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$whap."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wfdv."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$whdv."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wfok."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$whok."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wfac."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wobs."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wffa."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$whfa."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$wfpa."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$whpa."</td>"; 
			     echo "<td align=center bgcolor=".$wcolor.">".$wfad."</td>";
			     echo "<td align=center bgcolor=".$wcolor.">".$whad."</td>"; 
			     echo "</tr>";
			    }    
			    
			    echo "<td align=center colspan=14 class=link><A href='conmomalt.php?whis=".$whis."&wemp_pmla=".$wemp_pmla."'><b><font size=3 color=660099>Retornar</font></b></A></td>";
	        } 
       }      
	echo "</table>";   	  
	echo "</form>";
	  
    echo "<br>";
    echo "<center><table>"; 
    echo "<tr><td align=center colspan=14><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register

include_once("free.php");

?>