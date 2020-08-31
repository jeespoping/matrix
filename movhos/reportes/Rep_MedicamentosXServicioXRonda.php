<head>
  <title>MEDICAMENTOS POR SERVICIO Y RONDA</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
    	
    </style>
  
</head>

<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>	<!-- Acordeon -->
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>

<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>	<!-- Autocomplete -->
<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>	<!-- Autocomplete -->
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<script type="text/javascript">

function intercalar(idElemento)
   {
    //$("#"+idElemento).toggle("normal");
    
    if ( document.getElementById(idElemento).style.display=='')
       {
    	document.getElementById(idElemento).style.display='none';
       }
      else
        {
	     document.getElementById(idElemento).style.display='';
        }
    
    //<!--$("#ex"+idElemento).toggle("normal"); -->
   }  
   
function enter()
	{
	 document.forms.separacionXRonda.submit();
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
   *     REPORTE PARA DISPENSACION POR SERVICIO Y RONDA    *
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
  include_once("movhos/movhos.inc.php");
  

  
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
  		
 
		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2011-10-20)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
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
	      
	  encabezado("Medicamentos por Ronda y C.Costo",$wactualiz, "clinica");  
     }
     
     
  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;
	    
	 
	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);
	 
	 $wfecha_actual=date("Y-m-d");
	 
	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    } 
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
		    else
		       $whora_par_actual=$whora_Actual; 
	     }
	     
	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }
	      
	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;
	     
	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;  
    }
	 
	 
  function pintarPaciente($res, $num)
     {
	  global $conex;
	  global $wbasedato;
	  global $wemp_pmla;
	  global $wfecha;
	  
	 
	  if ($num > 0)
	     {
		  $wclass="fila2";
		  
		  echo "<table align=center>";
		  echo "<tr class=encabezadoTabla>";
		  echo "<th>Historia</th>";
		  echo "<th>Ingreso</th>";
		  echo "<th>Habitación</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>Dosis</th>";
		  echo "<th>Condición</th>";
		  echo "<th>Acción</th>";
		  echo "</tr>";
		  for ($i=1; $i<=$num; $i++)
		     {
			  if ($wclass=="fila1")
			     $wclass="fila2";
				else
                  $wclass="fila1";
				   
			  $row = mysql_fetch_array($res);
			  echo "<tr class='".$wclass."'>";
			  echo "<td align=center>".$row[0]."</td>";
			  echo "<td align=center>".$row[1]."</td>";
			  echo "<td align=center>".$row[2]."</td>";
			  echo "<td>".$row[3]." ".$row[4]." ".$row[5]." ".$row[6]."</td>";
			  echo "<td>".$row[7]." ".$row[8]."</td>";
			  
			  $wcond="";
			  if ($row[9] != "")
			     {
				  $q = " SELECT condes, contip "
					  ."   FROM ".$wbasedato."_000042 "
					  ."  WHERE concod = '".$row[9]."'";
				  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				  $row1 = mysql_fetch_array($res1);
				  
				  $wcond=$row1[0]." - ".$row1[1];
				 }
			  echo "<td><b>".$wcond."</b></td>";
			  echo "<td align=center><A href='../procesos/perfilFarmacoterapeutico.php?wemp_pmla=".$wemp_pmla."&waccion=a&whistoria=".$row[0]."&wfecha=".$wfecha."' target=_blank> Ir al Perfil </A></td>";
			  echo "</tr>";
			 }
		  echo "</table>";
		 }
	 }
	 
	 
	 
  function detalleArticulo($wart, $wcco, $whora_par_actual, $wfecha, $wtim, $waprobado, $wronda, $wcondicion, $wAN, &$wmostrar, &$respac, &$numpac)
     {
	  global $conex;
	  global $wbasedato;
	  global $wemp_pmla;
	  
	  if ($wtim == "SF")
	     {
		  $q = " SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd "
			  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000054 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 F, " 
			  ."        root_000036, root_000037 "
			  ."  WHERE A.fecha_data = '".$wfecha."'"
			  ."    AND kadest       = 'on' "
			  ."    AND kadart       = '".$wart."'"
			  ."    AND kadori       = 'SF'"
			  ."    AND karhis       = kadhis "
			  ."    AND karing       = kading "
			  ."    AND kadsus      != 'on' "
			  ."    AND kadess      != 'on' "
			  ."    AND A.fecha_data = kadfec "
			  ."    AND kadper       = percod "
			  ."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
			  ."    AND karcco       = '*' "
			  ."    AND karhis       = habhis "
			  ."    AND karing       = habing "
			  ."    AND habcco       LIKE '".trim($wcco)."' "
			  ."    AND karhis       = orihis "
			  ."    AND karing       = oriing "
			  ."    AND oriori       = '".$wemp_pmla."'"
			  ."    AND oriced       = pacced "
			  ."    AND oritid       = pactid "
			  ."    AND kadare       = '".$waprobado."'"
			  ."    AND kadron       = '".$wronda."'"
			  ."    AND kadcnd       = '".$wcondicion."'";
			  
			  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
			     {
			      //Con las sigtes lineas se asegura que solo traiga
				  $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart "
						 ."                                                      AND (spauen-spausa) = 0) "
						 ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart)) ";
				 }
			  
			  $q = $q."  UNION ALL "
			  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que esten para la ronda indicada
			  //y que solo sean de dispensacion no de CM
			  ." SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd "
			  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000060 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 F, "
			  ."        root_000036, root_000037 "
			  ."  WHERE A.fecha_data = '".$wfecha."'"
			  ."    AND kadest       = 'on' "
			  ."    AND kadart       = '".$wart."'"
			  ."    AND kadori       = 'SF'"
			  ."    AND karhis       = kadhis "
			  ."    AND karing       = kading "
			  ."    AND kadsus      != 'on' "
			  ."    AND kadess      != 'on' "        //No Enviar en off, osea que si se envia
			  ."    AND A.fecha_data = kadfec "
			  ."    AND kadper       = percod "
			  ."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, peor si es cero es porque si
			  ."    AND karcco       = '*' "
			  ."    AND karhis       = habhis "
			  ."    AND karing       = habing "
			  ."    AND habcco       LIKE '".trim($wcco)."' "
			  ."    AND karhis       = orihis "
			  ."    AND karing       = oriing "
			  ."    AND oriori       = '".$wemp_pmla."'"
			  ."    AND oriced       = pacced "
			  ."    AND oritid       = pactid "
			  ."    AND kadare       = '".$waprobado."'"
			  ."    AND kadron       = '".$wronda."'"
			  ."    AND kadcnd       = '".$wcondicion."'";
			  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
			     {
			      //Con las sigtes lineas se asegura que solo traiga
				  $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart "
						 ."                                                      AND (spauen-spausa) = 0) "
						 ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart)) ";
				 }
			  $q = $q."  GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
		 }
		else
         {
		  $q = " SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd "
			  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000054 B,".$wbasedato."_000043 E, ".$wbasedato."_000020 D, root_000036, root_000037 "
			  ."  WHERE A.fecha_data = '".$wfecha."'"
			  ."    AND kadest       = 'on' "
			  ."    AND kadart       = '".$wart."'"
			  ."    AND kadori       = 'CM'"
			  ."    AND karhis       = kadhis "
			  ."    AND karing       = kading "
			  ."    AND kadsus      != 'on' "
			  ."    AND kadess      != 'on' "
			  ."    AND A.fecha_data = kadfec "
			  ."    AND kadper       = percod "
			  ."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, pero si es cero es porque si
			  ."    AND karcco       = '*' "
			  ."    AND karhis       = habhis "
			  ."    AND karing       = habing "
			  ."    AND habcco       LIKE '".trim($wcco)."' "
			  ."    AND karhis       = orihis "
			  ."    AND karing       = oriing "
			  ."    AND oriori       = '".$wemp_pmla."'"
			  ."    AND oriced       = pacced "
			  ."    AND oritid       = pactid "
			  ."    AND kadare       = '".$waprobado."'"
			  ."    AND kadron       = '".$wronda."'"
			  ."    AND kadcnd       = '".$wcondicion."'";
			  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
			     {
			      //Con las sigtes lineas se asegura que solo traiga
				  $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart "
						 ."                                                      AND (spauen-spausa) = 0) "
						 ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart)) ";
				 }
			  
			  $q = $q."  UNION ALL "
			  //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION que esten para la ronda indicada
			  //y que solo sean de dispensacion no de CM
			  ." SELECT karhis, karing, habcod, pacno1, pacno2, pacap1, pacap2, kadcfr, kadufr, kadcnd "
			  ."   FROM ".$wbasedato."_000053 A,".$wbasedato."_000060 B, ".$wbasedato."_000043 E, ".$wbasedato."_000020 D, root_000036, root_000037 "
			  ."  WHERE A.fecha_data = '".$wfecha."'"
			  ."    AND kadest       = 'on' "
			  ."    AND kadart       = '".$wart."'"
			  ."    AND kadori       = 'CM'"
			  ."    AND karhis       = kadhis "
			  ."    AND karing       = kading "
			  ."    AND kadsus      != 'on' "
			  ."    AND kadess      != 'on' "        //No Enviar en off, osea que si se envia
			  ."    AND A.fecha_data = kadfec "
			  ."    AND kadper       = percod "
			  ."    AND MOD(TIMESTAMPDIFF(HOUR,CONCAT(kadfin,' ',kadhin),CONCAT('$wfecha',' ','$whora_par_actual',':00:00')),perequ) = 0 "   //Resto la fecha actual de la fecha de inicio y el resultado que da en horas las divido por la periodicidad (perequ), si hay decimales es porque el medicamento no es de esa hora, peor si es cero es porque si
			  ."    AND karcco       = '*' "
			  ."    AND karhis       = habhis "
			  ."    AND karing       = habing "
			  ."    AND habcco       LIKE '".trim($wcco)."' "
			  ."    AND karhis       = orihis "
			  ."    AND karing       = oriing "
			  ."    AND oriori       = '".$wemp_pmla."'"
			  ."    AND oriced       = pacced "
			  ."    AND oritid       = pactid "
			  ."    AND kadare       = '".$waprobado."'"
			  ."    AND kadron       = '".$wronda."'"
			  ."    AND kadcnd       = '".$wcondicion."'";
			  if ($wAN=="on")  //Si 'AN' adiciono estas condiciones al query
			     {
			      //Con las sigtes lineas se asegura que solo traiga
				  $q = $q."    AND (CONCAT(kadhis,'',kading,'',kadart) IN    (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart "
						 ."                                                      AND (spauen-spausa) = 0) "
						 ."     OR CONCAT(kadhis,'',kading,'',kadart) NOT IN (SELECT CONCAT(spahis,'',spaing,'',spaart) "
						 ."                                                     FROM ".$wbasedato."_000004 "
						 ."                                                    WHERE spahis = kadhis "
						 ."                                                      AND spaing = kading "
						 ."                                                      AND spaart = kadart)) ";
				 }
			  $q = $q."  GROUP BY 1,2,3,4,5,6,7,8,9,10 ";
		 }		
	  $respac = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  
	  $numpac = mysql_num_rows($respac);
	  if ($numpac > 0)
	     $wmostrar="on";
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
	  
	  
	  hora_par();
	  //Seleccionar RONDA
	  echo "<center><table>";
      echo "<tr class=fila1><td align=center><font size=20>Seleccione Ronda : </font></td></tr>";
	  echo "</table>";
	  
	  echo "<center><table>";
	  echo "<tr><td align=rigth><select name='whora_par_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px'>";
	  echo "<option selected>".$whora_par_actual."</option>";    
	  echo "<option>2</option>";
	  echo "<option>4</option>";
	  echo "<option>6</option>";
	  echo "<option>8</option>";
	  echo "<option>10</option>";
	  echo "<option>12</option>";
	  echo "<option>14</option>";
	  echo "<option>16</option>";
	  echo "<option>18</option>";
	  echo "<option>20</option>";
	  echo "<option>22</option>";
	  echo "<option>00</option>";
	  echo "</select></td></tr>";
	  echo "</table>"; 
	  
	  
	  //=======================================================================================================
	  //Seleccionar CENTRO DE COSTO Origen
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ".$wbasedato."_000011.ccohos = 'off' "
          ."    AND ".$wbasedato."_000011.ccofac = 'on' "
		  ."    AND ".$wbasedato."_000011.ccotra = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
	      
	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Unidad Origen: </font></td></tr>";
	  echo "</table>";
	  echo "<br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wccoo' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px'>";
	  echo "<option>&nbsp</option>";
	  //echo "<option>* - Todos</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
	  echo "<br><br>";
	  //=======================================================================================================
	  
	  //=======================================================================================================
	  //Seleccionar CENTRO DE COSTO Destino
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND (".$wbasedato."_000011.ccohos = 'on' "
          ."     OR  ".$wbasedato."_000011.ccolac = 'on') ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Unidad Destino: </font></td></tr>";
	  echo "</table>";
	  echo "<br>";
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
	  //=======================================================================================================
	 }    
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
  
  
  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L 
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='separacionXRonda' action='Rep_MedicamentosXServicioXRonda.php' method=post>";
  
  $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  mostrar_empresa($wemp_pmla);
  
  if (!isset($wcco) or !isset($wccoo))
     {
      elegir_centro_de_costo();
     } 
	else
       {
	    echo "<input type='HIDDEN' name='wcco' VALUE='".$wcco."'>";
		echo "<input type='HIDDEN' name='wccoo' VALUE='".$wccoo."'>";
		echo "<input type='HIDDEN' name='whora_par_actual' VALUE='".$whora_par_actual."'>";
	    
	    $wcco1=explode("-",$wcco);
	    if (trim($wcco1[0])=="*")
	       $wcco1[0]="%";
	      else
	         $wcco1[0]=trim($wcco1[0]); 
			 
		$wccoo1=explode("-",$wccoo);
	    $wccoo1[0]=trim($wccoo1[0]);
	    
		if ($whora_par_actual < 13)
           $whora_par_act=$whora_par_actual." AM";
		  else
             $whora_par_act=$whora_par_actual." PM";
		
		echo '<table align=center>';
		echo "<tr class=seccion1>";
	    echo "<th align=center colspan=2><font size=3>Centro de Costos Origen: </font><b><font size=4>".$wccoo."</b></font></th>";
		echo "</tr>";
	    echo "<tr class=seccion1>";
	    echo "<th align=center colspan=2><font size=3>Centro de Costos Destino: </font><b><font size=4>".$wcco."</b></font></th>";
		echo "</tr><tr class=seccion1>";
	    echo "<th align=center><font size=3>Ronda: </font><b><font size=4>".$whora_par_act."</b></font></th>";
		echo "<th align=center><font size=3>Fecha y Hora: <b>".date("Y-m-d H:i:s")."</b></font></th>";
	    echo "</tr>";
	    echo "</table>";
        echo "<br>";
		
		echo "<center><table><p class=fila1>";
		echo "<br><center><font size=3><b>Este reporte muestra los medicamentos que cumplan con las siguientes condiciones: </b></font></center><br>";
		echo "1. Que tenga algo pendiente por enviar o grabar en las PDA's. <br>"; // para esa ronda. <br>";
		echo "2. Que este aprobado por el regente. <br>";
		echo "3. Que sea para enviar, es decir que la enfermera no haya colocado lo contrario en el Kardex. <br>";
		echo "4. Que el medicamento sea solicitado al servicio origén. <br>";
		echo "5. Que el medicamento sea de un paciente que este hospitalizado en el servicio destino.<br>";
		echo "6. Que el medicamento este programado para la ronda solicitada. <br>";
		echo "<b>Notas:</b><br>** Los medicamentos <b>'a necesidad'</b> que no pertenezcan a esta ronda no salén; salén siempre y cuando esten como enviables en esta ronda y que NO halla saldo pendiente de aplicar en el servicio destino. <br>";
		echo "                 ** Esta pantalla se actualiza automaticamente cada dos (2) minutos. <br>";
		echo "                 ** Para cambiar de Ronda o Centro de costo debe retornar y seleccionar. <br>";
		echo "                 ** A medida que se va dispensando la ronda, los medicamentos deben ir desapareciendo de este reporte. <br>";
		echo "</p></table></center>";
		
		
	    query_articulos_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim);
		$num = mysql_num_rows($res);
		
		echo "<center><table>"; 
		echo "<tr class=encabezadoTabla>";
		echo "<th colspan=2><font size=4>Medicamento</font></th>";
		echo "<th colspan=2><font size=4>Dosis<br>según Kardex</font></th>";
		echo "<th colspan=2><font size=4>Cantidad Unidades<br>según Perfil</font></th>";
		echo "<th colspan=1><font size=4>No Pos</font></th>";
		echo "<th colspan=1><font size=4>Es de<br>Control</font></th>";
		echo "</tr>";
		    
		$j=1;
		$wclass="fila2";
		for ($i=1;$i<=$num;$i++)   //Recorre cada uno de los medicamentos
		   {
		    $row = mysql_fetch_array($res);
		        
		    if ($wclass=="fila1")
			   $wclass="fila2";
	          else
	             $wclass="fila1"; 
	               
		    $wultRonda = $row[9];    //Ultima ronda grabada
			$wcondicion= $row[10];   //Condicion de administracion
			
			$wAN="off";              //Indica si la condicion es tipo 'AN'
			//Averiguo si el tipo de condicion es 'AN' A Necesidad
			$q = " SELECT COUNT(*) "
			    ."   FROM ".$wbasedato."_000042 "
				."  WHERE concod        = '".$wcondicion."'"
				."    AND UPPER(contip) = 'AN' ";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);
			if ($row1[0] > 0)  //Indica que la condicion si es 'AN'
			   $wAN="on";
			   
			$wmostrar="off";			   
			detalleArticulo($row[0], $wcco1[0], $whora_par_actual, $wfecha, $wccotim, $row[7], $wultRonda, $wcondicion, $wAN, &$wmostrar, &$respac, &$numpac);
			   
			if ($wmostrar == "on")
			   {	
				if ($row[3] > 0)                                    //Cantidad a dispensar
				   {
					echo "<tr class=".$wclass." onclick=javascript:intercalar('".$i.$row[0]."')>";   
					echo "<td>".$row[0]."</td>";                    //Codigo Medicamento
					echo "<td>".$row[1]."</td>";                    //Nombre Medicamento
					if ($row[7] == "on")                            //Indica si esta aprobado en el perfil
					   {
						echo "<td align=center>".$row[2]."</td>";   //Cantidad de Dosis
						echo "<td align=center>".$row[6]."</td>";   //Fraccion de la dosis
						echo "<td align=center>".$row[3]."</td>";   //Can. Unidades 
						echo "<td align=center>".$row[4]."</td>";   //Presentacion 
					   }
					  else
						 if ($wccotim == "SF")
							 echo "<td align=center colspan=4 bgcolor=FFFFCC>Sin Aprobar en el perfil</td>";
							else
							   if ($row[8] == "off")               //No confirmado. Es antibiotico
								  echo "<td align=center colspan=4 bgcolor=FFFFCC>Medicamento Sin Confirmar en el Kardex</td>";
								 else
									echo "<td align=center colspan=4 bgcolor=FFFFCC>Sin Aprobar en el perfil</td>";
								
					$wpos="";
						
					if (strtoupper(trim($row[11])) == "N")
					   $wpos="Si";
					echo "<td align=center>".$wpos."</td>";     //Es Pos
					$wctr="";
					$wctrl=explode("-",$row[12]);
					if (strtoupper(trim($wctrl[0])) == "CTR")
					   $wctr="Si";
					echo "<td align=center>".$wctr."</td>";     //Es de Control
					echo "</tr>";
					
					echo "<tr id='".$i.$row[0]."' style='display:none'>";
					echo "<td colspan=8 align=center>";
					   detalleArticulo($row[0], $wcco1[0], $whora_par_actual, $wfecha, $wccotim, $row[7], $wultRonda, $wcondicion, $wAN, &$wmostrar, &$respac, &$numpac);
					   pintarPaciente($respac, $numpac);
					echo "</td>";
					echo "<tr>";
				   }
			   }
		   } 
		echo "</table>";
				
				
		/////////////////////////////////////////////////////////////////////////////////////////////
		//MUESTRO TODOS LOS PACIENTES DE LA RONDA ESPECIFICADA
		/////////////////////////////////////////////////////////////////////////////////////////////
		query_pacientes_cco($wfecha, &$res, $wcco1[0], $wccoo1[0], &$wccotim);
		$num = mysql_num_rows($res);
		
		echo "<br><br>";
		echo "<center><table>"; 
		echo "<tr class=fila1>";
		echo "<th colspan=4><font size=4>PACIENTES CON MEDICAMENTOS EN ESTA RONDA</font></th>";
		echo "</tr>";
		echo "<tr class=encabezadoTabla>";
		echo "<th><font size=4>Habitacion</font></th>";
		echo "<th><font size=4>Historia</font></th>";
		echo "<th><font size=4>Ingreso</font></th>";
		echo "<th><font size=4>Paciente</font></th>";
		echo "</tr>";
		    
		$j=1;
		$wclass="fila2";
		for ($i=1;$i<=$num;$i++)   //Recorre cada uno de los medicamentos
		   {
		    $row = mysql_fetch_array($res);
		        
		    if ($wclass=="fila1")
			   $wclass="fila2";
	          else
	             $wclass="fila1"; 
	             
            $q = " SELECT habcod "
                ."   FROM ".$wbasedato."_000020 "
                ."  WHERE habhis = '".$row[0]."'"
                ."    AND habing = '".$row[1]."'";
			$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$row1 = mysql_fetch_array($res1);	
				   
		    echo "<tr class=".$wclass.">";
			echo "<td align=center>".$row1[0]."</td>";  //Habitacion
			echo "<td align=center>".$row[0]."</td>";   //Historia
			echo "<td align=center>".$row[1]."</td>";   //Ingreso
			echo "<td align=left>".$row[2]." ".$row[3]." ".$row[4]." ".$row[5]."</td>";   //Paciente
			echo "</td>";
			echo "</tr>";
		   } 
		echo "</table>";
		/////////////////////////////////////////////////////////////////////////////////////////////
		
		echo "<br><br>";
		echo "<table>";
		echo "<tr><td><A HREF='Rep_MedicamentosXServicioXRonda.php?wemp_pmla=".$wemp_pmla."&user=".$user."' class=tipo4V>Retornar</A></td></tr>";
		echo "</table>";	

        echo "<meta http-equiv='refresh' content='120;url=Rep_MedicamentosXServicioXRonda.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wccoo=".$wccoo."&whora_par_actual=".$whora_par_actual."'>";   		    		
	   }
	   
  echo "<br><br><br>";
  echo "<table>";   
  echo "<tr><td align=center colspan=4><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
} // if de register

?>
</body>