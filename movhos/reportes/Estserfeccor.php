<html><head><title>MATRIX</title></head><body BGCOLOR=""><BODY TEXT="#000066"><center><table border=0 align=center><tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>DIAS DE ESTANCIA POR SERVICIO</font></a></tr></td><tr><td align=center bgcolor="#cccccc"><font size=2> <b> Estserfeccor.php</b></font></tr></td></table></center><?php
include_once("conex.php"); session_start(); if(!isset($_SESSION['user'])) echo "error"; else {   $key = substr($user,2,strlen($user));  
  
  include_once("root/comun.php");  
  $wactualiz="Septiembre 21 de 2010)";    function mostrar_empresa($wemp_pmla)     {  	  global $user;   	  global $conex;	  global $wcenmez;	  global $wafinidad;	  global $wbasedato;	  global $wtabcco;	  global $winstitucion;	  global $wactualiz;   	     	  //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    	  $q = " SELECT detapl, detval, empdes "	      ."   FROM root_000050, root_000051 "	      ."  WHERE empcod = '".$wemp_pmla."'"	      ."    AND empest = 'on' "	      ."    AND empcod = detemp "; 	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	  $num = mysql_num_rows($res); 	  	  if ($num > 0 )	     {		  for ($i=1;$i<=$num;$i++)		     {   		      $row = mysql_fetch_array($res);		      		      if ($row[0] == "cenmez")		         $wcenmez=$row[1];		         		      if ($row[0] == "afinidad")		         $wafinidad=$row[1];		         		      if ($row[0] == "movhos")		         $wbasedato=$row[1];		         		      if ($row[0] == "tabcco")		         $wtabcco=$row[1];	         }  	     }	    else	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";	  	  $winstitucion=$row[2];	 }       echo "<form action='Estserfeccor.php?wemp_pmla=".$wemp_pmla."' method=post>";  mostrar_empresa($wemp_pmla);  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";    encabezado("DIAS DE ESTANCIA POR SERVICIO",$wactualiz, "clinica");  $wfecha = date("Y-m-d");  echo "<center><table>";  echo "<tr>";  echo "<td align=right  class=fila1><b>Fecha de Corte: </b></td>";  echo "<td align=center class=fila2>";  if (!isset($wfec))     campofechaSubmit("wfec",$wfecha);    else        campofechaSubmit("wfec",$wfec);  echo "</td>";  echo "</tr>";  echo "</table>";  echo "<br>";    $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");  $wcostosyp = consultarAliasPorAplicacion($conex, $wemp_pmla, "COSTOS");  if (isset($wfec))     {	  $q = " SELECT ".$wmovhos."_000067.habcco,".$wcostosyp."_000005.cconom,SUM(DATEDIFF(".$wmovhos."_000067.fecha_data,".$wmovhos."_000018.fecha_data))   "	      ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000067, ".$wtabcco	      ."  WHERE ".$wbasedato."_000067.fecha_data = '".$wfec."' "	      ."    AND ".$wbasedato."_000067.habhis     = ".$wbasedato."_000018.ubihis   "	      ."    AND ".$wbasedato."_000067.habing     = ".$wbasedato."_000018.ubiing   "	      ."    AND ".$wbasedato."_000067.habest     = 'on' "	      ."    AND ".$wbasedato."_000067.habhis    <> '' "	      ."    AND ".$wbasedato."_000067.habcco     = ".$wtabcco.".ccocod "	      ."  GROUP BY 1,2";	  $res = mysql_query($q,$conex);	  $num = mysql_num_rows($res);	  echo "<center><table border=1>";	  echo "<td bgcolor=#cccccc colspan=2><b>Centro de Costo</b></td>";	  echo "<td bgcolor=#cccccc><b>D�as</b></td>";	  echo "</tr>"; 	  for ($i=0;$i<$num;$i++)	     {		  if (is_integer($i/2))		      $wclass="fila1";		     else		        $wclass="fila2";						  $row = mysql_fetch_array($res);		  echo "<tr class=".$wclass.">";		  echo "<td>".$row[0]."</td>";		  echo "<td>".$row[1]."</td>";		  echo "<td>".$row[2]."</td>";		  echo "</tr>"; 		 }	  echo "</table>";     }  }?></body></html>