<head>
  <title>CONSULTA DE CENTRAL POR DESTINO</title>
</head>
<body >
<script type="text/javascript">
   
    window.onload=function(){setTimeout("enter()",document.getElementById('wtiempo_refresh').value*1000)};
	//window.onload=function(){setTimeout("enter()",10000};
	
	function enter()
	 {
	  document.forms.central.submit();
	 }
	 
	function cerrarVentana()
	 {
      window.close();		  
     } 
	 
</script>
<?php
include_once("conex.php");


  /***************************************************
	*	           CENTRAL DE CAMILLEROS             *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']) and !isset($user))
	echo "error usuario no esta registrado";
else
{
	
  
    include_once("root/magenta.php");
    include_once("root/comun.php");
  
    $conex = obtenerConexionBD("matrix");
	
    

  	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
    $wactualiz="(Septiembre 29 de 2016)";                            // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	//=============================================================================================================================================															 
	//ACTUALIZACIONES
	/*Septiembre 29 de 2016
	Se toma el programa consulta_central_x_destino.php y se crea este programa pero para consultar por origen de la solicitud, se definen
	4 tipos de origen cirugia, urgencias, ayudas. hospitalizacion, con esto se muestran todas las solicitudes agrupadas por cada origen segun
	lo necesitado.
	*/
	//=============================================================================================================================================
            
  	$key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
       $wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
         
     $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'camilleros'); 
	 $wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");	 
          
          
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ACA SE COLOCA EL HORARIO DE ATENCION EN CENTRAL DE CAMILLEROS POR MATRIX
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$whora_atencion="LAS 24 HORAS DE LUNES A DOMINGO";
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	$wfecha=date("Y-m-d"); 
	$hora = (string)date("H:i:s");
	
	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));
  
	
	function seleccionar_destino()
	  {
	   global $conex;
	   global $worigen;
	   global $wcentral;
	   global $wcencam;
	   global $wemp_pmla;
	   global $wmovhos;
	   
	   
	   echo "<br><br><br>";
	   
	   //Seleccionar CENTRO DE COSTOS
	   echo "<center><table>"; 
	   echo "<tr class=fila1><td align=center><font size=5>Seleccione el origen de la solicitud: </font></td></tr>";
	   echo "</table>";
	   echo "<br><br><br>";
	   echo "<center><table>";
	   echo "<tr><td align=center><select name='worigen' onchange='enter()'>";
	   echo "<option> </option>"; 
	   echo "<option value='ccourg'>Urgencias</option>";
	   echo "<option value='ccocir'>Cirugia</option>";
	   echo "<option value='ccoayu'>Ayudas</option>";
	   echo "<option value='ccohos'>Hospitalario</option>";	
       echo "</select></td></tr>";
       echo "</table>";
	  }
	
	echo "<input type='HIDDEN' id=wemp_pmla value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' id=wcentral value='".$wcentral."'>";	
	
	$q = " SELECT nomcen, centre, cenvig, cenest, cenope, cenhop "
	    ."   FROM ".$wcencam."_000006 "
	    ."  WHERE codcen = '".$wcentral."'";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());   
	$num = mysql_num_rows($res);
	if ($num > 0)
	  {
	   $row = mysql_fetch_array($res);
	   $wnomcen               = $row[0];
	   $wtiempo_refresh       = $row[1];	//Tiempo en SEGUNDOS que tarda la pantalla en referescarse o actualizarse
	   $wvigencia_solicitudes = $row[2];    //Tiempo en HORAS que puede verse una solicitud que no se halla terminado
	   $westado_central       = $row[3];    //Estado de la central 'on': activa, 'off':inactiva
	   $wcodope               = $row[3];    //Codigo del operador de la central
	   $whorope               = $row[3];    //Hora hasta la que esta el operario
	   
	   encabezado("CONSULTA CENTRAL ".$wnomcen,$wactualiz, "clinica");
	  }		        
	 else
	    echo "<tr><td align=center bgcolor=#fffffff colspan=13><font size=3 text color=#CC0000><b>FALTA DEFINIR EN LA TABLA cencam_000006 EL CODIGO DE LA CENTRAL</b></font></td></tr>";
	    
	
		
    echo "<form name=central method=post>";
	
	if (isset($destino))
	    echo "<input type='HIDDEN' id=worigen value='".$worigen."'>";
		
		
	if ($westado_central=="on" and isset($worigen))   
	  { 
	   echo "<input type='HIDDEN' id=wtiempo_refresh value='".$wtiempo_refresh."'>";
	    
		
		//===================================================================================================================================================
	    // QUERY PRINCIPAL 
	    //===================================================================================================================================================
	    // ACA TRAIGO TODAS LAS SOLICITUDES HECHAS QUE NO TENGAN MAS DE DOS HORAS DE ESPERA
	    //===================================================================================================================================================
	    
		//Se consultan los centros de costos asociados a la variable $worigen, (ccourg, ccohos, ccoayu, ccocir) estas variable se igualan a on en la consulta.
		$q_cco = "SELECT Nombre
                    FROM ".$wcencam."_000004, ".$wmovhos."_000011
			       WHERE SUBSTRING_INDEX( Cco, '-', 1 ) = Ccocod
				     AND $worigen = 'on' ";
		$res_cco = mysql_query($q_cco,$conex) or die (mysql_errno()." - ".mysql_error()); 
		
		$array_cco = array();
		while($row_cco = mysql_fetch_array($res_cco)){
			
			$array_cco[$row_cco['Nombre']] = $row_cco['Nombre'];
			
		}
		
		//Concateno los centros de costos con el nombre que trae de la tabla 4 de cencam.
		$cco_origen = implode("','",$array_cco);
		
		
		$q = "  SELECT A.Hora_data, Origen, Motivo, Observacion, Destino, Solicito, Camillero, "
	        ."         Hora_llegada, Hora_Cumplimiento, A.Id, Habitacion, Observ_central, A.fecha_data "
		    ."    FROM ".$wcencam."_000003 A, ".$wcencam."_000001 B"
		    ."   WHERE Anulada           = 'No' "
		    ."     AND TIMESTAMPDIFF(HOUR,CONCAT(A.Fecha_data,' ',A.Hora_data),CONCAT('".$wfecha."',' ','".$hora."')) <= ".$wvigencia_solicitudes 
		    ."     AND Hora_cumplimiento = '00:00:00' "
		    ."     AND Motivo            = Descripcion "
		    ."     AND A.central         = '".$wcentral."'"
			."     AND A.Origen          in ('".$cco_origen."')"
		    ."   ORDER BY A.Fecha_data desc, A.Hora_data desc ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());  //or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);    //or die (mysql_errno()." - ".mysql_error());
	    
		echo "<center><table border=0>";
	    echo "<tr><td align=left bgcolor=#fffffff colspan=3><font size=2 text color=#CC0000><b>Hora: ".$hora."</b></font></td><td align=left bgcolor=#fffffff colspan=10><font size=2 text color=#CC0000><b>Cantidad de Solicitudes: ".$num."</b></font></td></tr>";
	    
	    echo "<tr class='encabezadoTabla'>";
	    echo "<th><font size=1>Fecha</font></th>";
	    echo "<th><font size=1>Hora</font></th>";
	    echo "<th><font size=1>Origen</font></th>";
	    echo "<th><font size=1>Motivo</font></th>";
	    echo "<th><font size=1>Habit</font></th>";
	    echo "<th><font size=1>Observacion</font></th>";
	    echo "<th><font size=1>Destino</font></th>";
	    echo "<th><font size=1>Solicitado por</font></th>";
	    echo "<th><font size=1>Camillero/Ubicación</font></th>";
	    echo "<th><font size=1>Lle</font></th>";
	    echo "<th><font size=1>Cum</font></th>";
	    echo "<th><font size=1>Anu</font></th>";
	    echo "<th><font size=1>Observ.Central</font></th>";
		echo "<th><font size=1>Nro de<br>Solicitud</font></th>";
	    echo "</tr>";
	    
	    for ($i=$num;$i>=1;$i--)
		   {
			$row = mysql_fetch_array($res); 
			
		    $a1=$row[0];
		    $a2=date("H:i:s");
		    $a3=((integer)substr($a2,0,2)-(integer)substr($a1,0,2))*60 + ((integer)substr($a2,3,2)-(integer)substr($a1,3,2)) + ((integer)substr($a2,6,2)-(integer)substr($a1,6,2))/60;
		    
		    //Aca configuro la presentacion de los colores segun el tiempo de respuesta
		    if ($a3 > 5)                 //$a3 > 5
		       {
		        $wcolor = "CCCCFF";      //Lila
		        $wcolorfor = "000000";   //Negro
	           }
		    if ($a3 > 2.5 and $a3 <= 5)  //$a3 > 2.5 and $a3 <= 5
		       {
		        $wcolor = "FFFF66";      //Amarillo  
		        $wcolorfor = "000000";   //Negro
	           } 
		    if ($a3 <= 2.5)              //$a3 <= 2.5 
		       { 
			    $wcolor = "99FFCC";      //Verde   
			    $wcolorfor = "000000";   //Negro
	           } 
	                        
			echo "<tr bgcolor=".$wcolor.">";
			echo "<td><font size=1 color=".$wcolorfor.">".$row[12]."</font></td>";                  // Fecha de solicitud
		    echo "<td><font size=1 color=".$wcolorfor.">".$row[0]."</font></td>";                   // Hora de solicitud
		    echo "<td><font size=1 color=".$wcolorfor.">".$row[1]."</font></td>";                   // Origen
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[2]."</font></td>";                   // Motivo
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[10]."</font></td>";                  // Habitacion
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[3]."</font></td>";                   // Observacion
	        echo "<td><font size=1 color=".$wcolorfor.">".$row[4]."</font></td>";                   // Destino
	        
			$wid = $row[9];
	        //============================================================================================================================
	        //TRAIGO EL NOMBRE DE QUIEN SOLICITO EL SERVICIO
	        $q = "  SELECT Descripcion "
	            ."    FROM usuarios "
	            ."   WHERE Codigo = '".$row[5]."'"
	            ."     AND Activo = 'A' ";
		    $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());;
		    $row1 = mysql_fetch_array($res2);
		    
		    if ($row1[0] == "")
		       $wnomusu = $row[5];                                                                        // Sin Nombre de Solicitado por
		      else 
		         $wnomusu = $row1[0];                                                                     // Solicitado por
		    echo "<td bgcolor=".$wcolor."><font size=1 color=".$wcolorfor.">".$wnomusu."</font></td>";    // Nombre de quien solicito el servicio
		    //============================================================================================================================
		    
		    //============================================================================================================================
		    //MUESTRO LOS CAMILLEROS 
			$wcodigo = substr($row[6],0,(strpos($row[6],"-")-1));    
			//Se cambia la tabla 2 por la tabla 7 de cencam ya que esta contiene los tipos de cama detallados y separados de los otros tipos.//09 oct 2013 Jonatan
			//El tipo lo utilizo para poder ordenar el query por nombre, trayendo primero el registro que selecciono el usuario
			$q = " SELECT tipcod, tipdes, 1 AS Tip "  
				."    FROM ".$wcencam."_000007 "
				."   WHERE tipcod = '".$wcodigo."'"				
				."     AND tipcen = '".$wcentral."'";
			$rescam = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$numcam = mysql_num_rows($rescam) or die (mysql_errno()." - ".mysql_error());
		
			echo "<td bgcolor=".$wcolor.">";
			echo "<SELECT id='wcamillero[".$i."]'>";
			if (trim($row[6]) == "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
			   echo "<option> </option>";
			for($j=0;$j<$numcam;$j++)
			   { 
				$rowcam = mysql_fetch_array($rescam);   
				echo "<option>".$rowcam[0]." - ".$rowcam[1]."</option>";
			   }
			if (trim($row[6]) != "")      //Si no viene ningún dato desde el registro de la tabla, muestro un nulo
			   echo "<option> </option>";
			echo "</SELECT></td>";
				
		    
		    //============================================================================================================================
		    //Evaluo si la LLEGADA ya habia sido dada, si no, evaluo si se dio dando click en checkbox, si no la muestro desmarcada
		    //Tiene hora de llegada pero no tiene asignado camillero, entonces coloco la hora de llegada en ceros
		    if (strpos($row[6],"-") > 0)   //Si hay camillero
			  {
			   if ($row[7] == "00:00:00")   //Si NO hay llegada
			      echo "<td align=center bgcolor=".$wcolor." title='LLEGADA'><INPUT TYPE=CHECKBOX ID='wllegada[".$i."]' disabled='disabled'></td>"; 
				  else
				     echo "<td align=center bgcolor=".$wcolor." title='LLEGADA'><INPUT TYPE=CHECKBOX ID='wllegada[".$i."]' CHECKED disabled='disabled'></td>"; 					  
			  }
			 else
				echo "<td align=center bgcolor=".$wcolor." title='LLEGADA'><INPUT TYPE=CHECKBOX ID='wllegada[".$i."]' disabled='disabled'></td>";
	        
			
			//============================================================================================================================
		    //Evaluo si el CUMPLIMIENTO ya habia sido dado, si no, evaluo si se dio dando click en checkbox, si no lo muestro desmarcado
		    echo "<td align=center bgcolor=".$wcolor." title='CUMPLIDO'><INPUT TYPE=CHECKBOX ID='wcumplimiento[".$i."]' disabled='disabled'></td>"; 
			
			//============================================================================================================================
		    //Evaluo si ha sido ANULADA la solicitud
		    if (strpos($row[6],"-") > 0 and $row[7] == "00:00:00")        //Tiene camillero pero NO llegada
		       echo "<td align=center bgcolor=".$wcolor." title='ANULAR'><INPUT TYPE=CHECKBOX ID='wanulada[".$i."]' disabled='disabled'></td>";
			  else  
			     if ($row[7] == "00:00:00")   //No Tiene llegada dejo anular
                    echo "<td align=center bgcolor=".$wcolor." title='ANULAR'><INPUT TYPE=CHECKBOX ID='wanulada[".$i."]' disabled='disabled'></td>"; 
				   else	                      //Tiene llegada NO dejo anular
				      echo "<td align=center bgcolor=".$wcolor." title='ANULAR'><INPUT TYPE=CHECKBOX ID='wanulada[".$i."]' disabled='disabled'></td>"; 
			
	        
	        //===========================================================================================================================
	        //Observacion de la central de camilleros
	        echo "<td align=left bgcolor=".$wcolor."><textarea ID='wobscc[".$i."]' rows=3 cols=30 readonly>".$row[11]."</textarea></td>";
			echo "<td align=center bgcolor=".$wcolor.">".$wid."</td>";

			echo "</tr>";
	       } //FIN DEL FOR
	   echo "</table></center>";
	   
	   echo "<HR align=center></hr>";  //Linea horizontal
	   
	   echo "<table border=1 align=right>";
	   echo "<caption bgcolor=#ffcc66>Convenciones</caption>";
	   echo "<tr><td colspan=3 bgcolor="."CCCCFF"."><font size=2 color='"."000000"."'>&nbsp Mas de cinco (5) minutos</font></td></tr>";      //Lila
	   echo "<tr><td colspan=3 bgcolor="."FFFF66"."><font size=2 color='"."000000"."'>&nbsp De 2.5 a 5 minutos</font></td></tr>";            //Amarillo
	   echo "<tr><td colspan=3 bgcolor="."99FFCC"."><font size=2 color='"."000000"."'>&nbsp Menos de 2.5 minutos</font></td></tr>";          //Verde  
	   echo "</table>";
	   
	   echo "<br><br>";
	   echo "<center><table>";
       echo "<tr><td><A HREF='Consulta_Central_X_Origen.php?wemp_pmla=".$wemp_pmla."&wcentral=".$wcentral."'>Retornar</A></td></tr>";
       echo "</table></center>";
	  }
     else
       {
	    if ($westado_central!="on")
		   {
			echo "<br><br>";   
			echo "<center><table>";
			echo "<tr><td align=center bgcolor=#fffffff><font size=5 text color=#CC0000><b>LA CENTRAL ESTA INACTIVA</b></font></td></tr>";
			echo "</table>";
			
		   }
		  else
             seleccionar_destino();
       } 

	echo "</form>";
	   
	echo "<br><br><br>";
	echo "<center><table>";
    echo "<tr><td align=center colspan=13><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";    
    echo "</table></center>";
}
?>
