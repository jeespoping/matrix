<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066" onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.dicdatos.submit();
	}
	
</script>
<?php
 session_start();
 if(!session_is_registered("user"))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user)); 
  
  include("conex.php");
  mysql_select_db("matrix");
  echo "<form name=dicdatos action='diccionario de datos.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<center><table border=2>";
  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>REPORTES DE DOCUMENTACION AREA DE DESARROLLO</b></font></td></tr>";
  
  if (!isset($wusu))
   {
	//SELECCIONO EL USUARIO O BASE DE DATOS
    $q =  " SELECT codigo, descripcion "
		 ."   FROM usuarios "
		 ."  WHERE grupo != 'AMERICAS' "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	 
	  
	echo "<tr><td align=center bgcolor=".$wcf." >SELECCIONE EL USUARIO: ";
	echo "<select name='wusu'>";
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td></tr>";
    
	echo "<tr>";
	echo "<td align=center bgcolor=00FFFF>&nbsp</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align=center bgcolor=999999><b><font size=4>Seleccione la opción que desea imprimir</font></b></td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td align=center bgcolor=00FFFF>";
	echo "<input type='RADIO' name=op value=1 onclick='enter()'><b>Actividades Programadas</b>";
	echo "<input type='RADIO' name=op value=2 onclick='enter()'><b>Diccionario de Datos</b>";
	echo "<input type='RADIO' name=op value=3 onclick='enter()'><b>Requerimientos</b>";
	echo "<input type='RADIO' name=op value=4 onclick='enter()'><b>Programas documentados</b>";
	echo "<input type='RADIO' name=op value=5 onclick='enter()'><b>Tablas sin Indice Unico</b>";
	echo "<input type='RADIO' name=op value=6 onclick='enter()'><b>Todas las opciones</b>";
	echo "</td>";
	echo "</tr>";
    
	//echo "<tr>";
    //echo "<td align=center bgcolor=#cccccc ><input type='submit' value='OK'></td>";                                         //submit
    //echo "</tr>";
   }
  else 
     {
	  echo "<input type='HIDDEN' NAME= 'wusu' value='".$wusu."'>";
	  $wusue = explode("-",$wusu); 
	  
	  $q= "SELECT grupo, subcodigo "
	     ."  FROM usuarios, det_selecciones "
	     ." WHERE usuarios.codigo             = '".$wusue[0]."'"
	     ."   AND det_selecciones.codigo      = 'GRUPOS' "
	     ."   AND det_selecciones.descripcion = usuarios.grupo ";
	  $err = mysql_query($q,$conex);
	  $row = mysql_fetch_array($err);
	  $wnomgru=$row[0];   
	  $wcodgru=$row[1];
	     
	  echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center ><b><font text color=".$wclfg.">GRUPO: </font></b>".$wnomgru."</td>";
      echo "</tr>";
	  
	  echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center ><b><font text color=".$wclfg.">USUARIO: </font></b>".$wusu."</td>";
      echo "</tr>";  
	  
      if ($op==6)
         $t=3;
        else
           $t=1; 
         
      for ($y=1;$y<=$t;$y++)
         {
	      if ($t==3)
	         $op=$y;
	            
	      switch ($op)
	        {
	          case "1":  //Actividades Programadas
	             {
		          $q = "  SELECT grupo, descripcion, Fecha_terminacion, responsable, avance "
			           ."   FROM root_000026 "
			           ."  WHERE grupo = '".$wcodgru."-".$wnomgru."'"
			           ."  GROUP BY 1, 2, 3, 4, 5 "
			           ."  ORDER BY 1, 2, 4, 3 ";
			       $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				   $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
				  	  
				   echo "<table border=1>";  
				   
				   $row = mysql_fetch_array($err); 
				   
				   echo "<tr><td colspan=4 bgcolor=66CC99 align=CENTER><b>** A C T I V I D A D E S__P R O G R A M A D A S **</b></font></td></tr>";
				  
				   $i=1;
				   while ($i <= $num)
				     {
					  $wgrupo=$row[0]; 
					  
					  echo "<tr><td colspan=4 bgcolor=CCCCFF align=CENTER><b>Grupo: ".$wgrupo."</b></font></td></tr>";
					  
					  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
					  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TERMINA</font></th>";  
					  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>RESPONSABLE</font></th>";
					  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>AVANCE</font></th>";
				      
					  while (($i<=$num) and ($wgrupo==$row[0]))
				           {
					        echo "<tr>";
					        echo "<td>".$row[1]."</td>";
					        echo "<td>".$row[2]."</td>";
					        echo "<td>".$row[3]."</td>";
					        echo "<td>".$row[4]."</td>";
					        echo "</tr>";
					        
					        $row = mysql_fetch_array($err);
					        $i=$i+1;
					       }
					 }
				 }  
	             break;   
	          case "2":  //Diccionario de Datos
	             { ///////////
		           $q = "  SELECT formulario.codigo  , formulario.nombre      , det_formulario.descripcion, det_formulario.campo, "
			           ."         det_formulario.tipo, root_000033.descripcion, dic_comentario "
			           ."    FROM formulario, det_formulario, root_000033, root_000030 "
			           ."   WHERE formulario.medico          = '".$wusue[0]."'"
			           ."     AND formulario.medico          = det_formulario.medico "
			           ."     AND formulario.codigo          = det_formulario.codigo "
			           ."     AND det_formulario.tipo        = root_000033.codigo "
			           ."     AND dic_usuario                = formulario.medico "
			           ."     AND dic_formulario             = formulario.codigo "
			           ."     AND dic_campo                  = det_formulario.campo "
			           ."   GROUP BY 1, 2, 3, 4, 5, 6 "
			           ."   ORDER BY 1, 2, 4 ";
			       $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				   $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
				  	  
				   echo "</table>";
				   
				   echo "<table border=0>";
				   
				   $row = mysql_fetch_array($err); 
				   
				   echo "<tr><td bgcolor=FFCC66 align=CENTER><font size=5><b>** D I C C I O N A R I O &nbsp&nbsp D E &nbsp&nbsp D A T O S **</b></font></td></tr>";
				  
				   echo "</table>";
				   
				   $i=1;
				   $k=1;
				   while ($i <= $num)
				     {
					  echo "<br>";
					  echo "<br>";
					  echo "<br>";
					  echo "<br>";
					  echo "<br>";
					  echo "<br>";
					  
					  $wtabla=$row[0]; 
				      
					  echo "<table border=1>";
					  
					  echo "<tr><td colspan=5 bgcolor=FFCC66 align=CENTER><b>Formulario o Tabla: ".$wusue[0]."_".$wtabla." - ".$row[1]."</b></font></td></tr>";
					  
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>CAMPO</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>DESCRIPCION</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>TIPO</font></th>";  
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>DATO</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>COMENTARIOS</font></th>";
				      
					  while (($i<=$num) and ($wtabla==$row[0]))
				           {
					        if($i % 2 == 0)
							  //$color="#66CCFF";
							  $color="#FFFFFF";
							 else
								$color="#dddddd";    
					           
					        $q = "  SELECT dic_descripcion "
						        ."    FROM root_000030 "
						        ."   WHERE dic_usuario    = '".$wusue[0]."'"
			                    ."     AND dic_formulario = '".$row[0]."'"
			                    ."     AND dic_campo      = '".$row[3]."'";
			                $err1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());   
			                $num1 = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error()); 
			                if ($num1 > 0)
			                   {
				                $row1 = mysql_fetch_array($err1);   
			                    $wdicdes=str_replace("<br>","",$row1[0]);
			                    if ($wdicdes == "")
			                       $wdicdes="<font text color='44444'>** Falta en el diccionario **</font>"; 
			                   }
			                  else
			                     $wdicdes="<font text color='44444'>** Falta en el diccionario **</font>"; 
			                     
					           
					        echo "<tr>";
					        echo "<td bgcolor=".$color.">".$row[2]."</td>";
					        echo "<td bgcolor=".$color.">".$wdicdes."</td>";
					        echo "<td bgcolor=".$color.">".$row[4]."</td>";
					        echo "<td bgcolor=".$color.">".$row[5]."</td>";
					        if (trim($row[6])!="") echo "<td bgcolor=".$color.">".$row[6]."</td>";
					           else
					              echo "<td bgcolor=".$color.">&nbsp</td>";
					        echo "</tr>";
					        
					        $row = mysql_fetch_array($err);
					        $i=$i+1;
					       }
					  
					  $q= " show index from ".$wusue[0]."_".$wtabla;
					  $err_idx = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					  $num_idx = mysql_num_rows($err_idx) or die (mysql_errno()." - ".mysql_error());
					  
					  if ($num_idx > 0)
					     {
					      echo "<tr>";
					      echo "<td colspan=5 bgcolor=C0C0F0 align=CENTER><b> Indices de ".$wusue[0]."_".$wtabla."</b></td>";
					      echo "</tr>";
					      echo "<th align=CENTER bgcolor=C0C0F0 colspan=2><font size=2>NOMBRE</font></th>";
						  echo "<th align=CENTER bgcolor=C0C0F0><font size=2>TIPO</font></th>";
						  echo "<th align=CENTER bgcolor=C0C0F0><font size=2>CAMPO</font></th>";  
						  echo "<th align=CENTER bgcolor=C0C0F0><font size=2>LONGITUD</font></th>";
					      $row_idx = mysql_fetch_array($err_idx);
						  
					      $j=1;
					      while ($j <= $num_idx)
						     {
							  if($j % 2 == 0)
							    $color="#FFFFFF";
							   else
							      $color="#dddddd";    
							  $wnomidx=$row_idx[2]; 
							  
							  echo "<tr>";
							  echo "<td bgcolor=".$color." colspan=2>".$row_idx[2]."</td>";
							  if ($row_idx[1] == 0)
							     echo "<td bgcolor=".$color.">UNICO</td>";
							    else
							       echo "<td bgcolor=".$color.">DUPLICADO</td>"; 
							  $wprimero="1";
						      while ($j <= $num_idx and $wnomidx==$row_idx[2])
						           {   
							        if($j % 2 == 0)
									  //$color="#66CCFF";
									  $color="#FFFFFF";
									 else
										$color="#dddddd";    
							           
							        if ($wprimero == "2")
							           {
							            echo "<tr>";
							            echo "<td bgcolor=".$color." colspan=2>&nbsp</td>";
							            echo "<td bgcolor=".$color.">&nbsp</td>";
						               } 
							        echo "<td bgcolor=".$color.">".$row_idx[4]."</td>";
							        if ($row_idx[7] != "")
							           echo "<td bgcolor=".$color.">".$row_idx[7]."</td>";
							          else
							             echo "<td bgcolor=".$color.">&nbsp</td>"; 
							        echo "</tr>";
							        $wprimero="2";
							        
							        $row_idx = mysql_fetch_array($err_idx);
							        $j++;
						           } 
						     }
						 }      
					  $k++;  
					  echo "</table>";
				     }  
				   echo "<tr><td colspan=11 bgcolor=FFCC66><b>Total tablas: ".($k-1)."</b></font></td></tr>";	   
	              }  
	              break;   
	           case "3":  //Requerimientos
	             {
		          $q = "  SELECT reqcon, fecha_de_recibido, Nombre_requerimiento, grupo, clase, prioridad, analista, "
		              ."         programador, fecha_de_entrega_aprox, estado, porcentaje_cumplimiento "
			          ."    FROM root_000031 "
			          ."   WHERE grupo = '".$wcodgru."-".$wnomgru."'"
			          ."   GROUP BY 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11 "
			          ."   ORDER BY 7, 1, 2, 4, 3 ";
			       $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				   $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
				  	  
				   echo "<table border=1>";  
				   
				   $row = mysql_fetch_array($err); 
				  
				   echo "<tr><td colspan=11 bgcolor=FFCC66 align=CENTER><b>** R E Q U E R I M I E N T O S **</b></font></td></tr>";
				   
				   $i=1;
				   while ($i <= $num)
				     {
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>CONSECUTIVO</font></th>";   
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>FECHA_RECIBO</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>REQUERIMIENTO</font></th>";  
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>CLASE</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>PRIORIDAD</font></th>";
				      echo "<th align=CENTER bgcolor=FFCC66><font size=2>ANALISTA</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>PROGRAMADOR</font></th>";  
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>ENTREGA APROX.</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>ESTADO</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>AVANCE</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>&nbsp</font></th>";
					  
					  while (($i<=$num))
				           {
					        if($i % 2 == 0)
							  //$color="#66CCFF";
							  $color="#FFFFFF";
							 else
								$color="#dddddd";   
					           
					        echo "<tr>";
					        echo "<td bgcolor=".$color." align=center><font size=2>".$row[0]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[1]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[3]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[4]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[5]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[6]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[7]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[8]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[9]."</font></td>";
					        echo "<td bgcolor=".$color." align=center><font size=2>".$row[9]."</font></td>";
					        
					        $wnomreq=str_replace(" ","%",$row[1]);
					        $wnomreq=str_replace("d","%",$wnomreq);
					        
					        //echo "<td align=center bgcolor=".$color."><font size=2><b><A href='/matrix/registro.php?call=1"."&amp;Form=000031-root-C-Requerimientos"."&amp;Frm=0"."&amp;tipo=P"."&amp;key=root&amp;Vlr=Nombre_requerimiento=".$wnomreq."&amp;Valor=Nombre_requerimiento=".$wnomreq."'> Ver</A></b></font></td>";
					        echo "<td align=center bgcolor=".$color."><font size=2><b><A href='/matrix/registro.php?call=1"."&amp;Form=000031-root-C-Requerimientos"."&amp;Frm=0"."&amp;tipo=P"."&amp;key=root&amp;Vlr=reqcon=".$row[0]."&amp;Valor=reqcon=".$row[0]."'> Ver</A></b></font></td>";
					        echo "</tr>";
					        
					        $row = mysql_fetch_array($err);
					        $i=$i+1;
					       }
					 }
				   echo "<tr><td colspan=11 bgcolor=FFCC66><b>Total requerimientos: ".$num."</b></font></td></tr>";	 
				 } 
	             break;   
	          case "4":  //Documentacion de Programas
	             {
		          $q = "  SELECT consecutivo, fecha, grupo, nombre, autor, "
		              ."         descripcion, tablas, modificaciones "
			          ."    FROM root_000001 "
			          ."   WHERE grupo = '".$wcodgru."-".$wnomgru."'"
			          ."   GROUP BY 1, 2, 3, 4, 5, 6, 7, 8 "
			          ."   ORDER BY 1, 2, 4 ";
			          
			       $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				   $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
				  	  
				   echo "<table border=1>";  
				   
				   $row = mysql_fetch_array($err); 
				  
				   echo "<tr><td colspan=7 bgcolor=FFCC66 align=CENTER><b>** P R O G R A M A S **</b></font></td></tr>";
				   
				   $i=1;
				   while ($i <= $num)
				     {
					     //cccccc
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>CONSECUTIVO</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>FECHA CREACION</font></th>";  
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>NOMBRE</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>AUTOR</font></th>";
				      echo "<th align=CENTER bgcolor=FFCC66><font size=2>DESCRIPCION</font></th>";
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>TABLAS</font></th>";  
					  echo "<th align=CENTER bgcolor=FFCC66><font size=2>MODIFICACIONES</font></th>";
					   
					  while (($i<=$num))
				           {
					        if($i % 2 == 0)
							  //$color="#66CCFF";
							  $color="#FFFFFF";
							 else
								$color="#dddddd";   
					           
					        echo "<tr>";
					        echo "<td bgcolor=".$color." align=center><font size=2>".$row[0]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[1]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[3]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[4]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[5]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[6]."</font></td>";
					        echo "<td bgcolor=".$color." ><font size=2>".$row[7]."</font></td>";
					        
					        //registro.php?call=1&Form=000058-farstore-C-Maestro de Remitentes&Frm=0&tipo=P&key=farstore
					        
					        
					        $wnomreq=str_replace(" ","%",$row[1]);
					        $wnomreq=str_replace("d","%",$wnomreq);
					        
					        //echo "<td align=center bgcolor=".$color."><font size=2><b><A href='/matrix/registro.php?call=1"."&amp;Form=000031-root-C-Requerimientos"."&amp;Frm=0"."&amp;tipo=P"."&amp;key=root&amp;Vlr=Nombre_requerimiento=".$wnomreq."&amp;Valor=Nombre_requerimiento=".$wnomreq."'> Ver</A></b></font></td>";
					        echo "</tr>";
					        
					        $row = mysql_fetch_array($err);
					        $i=$i+1;
					       }
					 }
			      echo "<tr><td colspan=7 bgcolor=FFCC66><b>Total programas: ".$num."</b></font></td></tr>";	 		 
				 } /////// 
	             break;
	          
	          case "5":  //Falta Indice Unico
	             { ///////////
		           $q = "  SELECT formulario.codigo, Formulario.medico, Formulario.nombre "
			           ."    FROM formulario, det_formulario "
			           ."   WHERE formulario.medico = det_formulario.medico "
			           ."     AND formulario.codigo = det_formulario.codigo "
			           ."   GROUP BY 1, 2 "
			           ."   ORDER BY 2, 1 ";
			       $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				   $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
				  	  
				   echo "<table border=1>";
				   
				   $row = mysql_fetch_array($err); 
				   
				   echo "<tr><td bgcolor=FFCC66 align=CENTER><b>** T A B L A S  &nbsp&nbsp  S I N  &nbsp&nbsp  I N D I C E  &nbsp&nbsp  U N I C O **</b></font></td></tr>";
				  
				   $i=1;
				   $k=1;
				   while ($i <= $num)
				     {
					  $wtabla=$row[0]; 
					  $wunico="off";
					  
					  //==============================================================================
					  //ACA TRAIGO LOS INDICES DE CADA TABLA
					  $q= " show index from ".$row[1]."_".$wtabla;
					  $err_idx = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					  $num_idx = mysql_num_rows($err_idx) or die (mysql_errno()." - ".mysql_error());
					  
					  if ($num_idx > 0)
					     {
						   $row_idx = mysql_fetch_array($err_idx);
						  
					      $j=1;
					      while ($j <= $num_idx)
						     {
							   if ($row_idx[1] == 0 and $row_idx[2] != "PRIMARY") 
						          $wunico="on";   
						          
						       $row_idx = mysql_fetch_array($err_idx);
							   $j++;   
						     }
						   if ($wunico=="off")
						      echo "<tr><td>Tabla: ".$row[1]."_".$wtabla." : ".$row[2]."</td></tr>";  
						 }      
					  $k++; 
					  
					  $row = mysql_fetch_array($err);
					  $i=$i+1; 
				     }  
				   echo "<tr><td colspan=11 bgcolor=FFCC66><b>Total tablas: ".($k-1)."</b></font></td></tr>";	   
	              }  
	              break;                 
	        }
         }    
     } 
	 echo "</table>"; 
}
?>
</body>
</html>