<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
 session_start();
 if(!session_is_registered("user"))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  include("conex.php");
  mysql_select_db("matrix");
  echo "<form action='diccionario de datos.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<center><table border=2>";
  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DICCIONARIO DE DATOS</b></font></td></tr>";
  
  if (!isset($wusu))
   {
	//SELECCIONO EL USUARIO O BASE DE DATOS
    $q =  " SELECT codigo, descripcion "
		 ."   FROM usuarios "
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
	echo "</select></td>";
    
    
	echo "<tr>";
    echo "<td align=center bgcolor=#cccccc ><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else 
     {
	  echo "<input type='HIDDEN' NAME= 'wusu' value='".$wusu."'>";
	  $wusue = explode("-",$wusu); 
	  
	  echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center ><b><font text color=".$wclfg.">USUARIO: </font></b>".$wusu."</td>";
      echo "</tr>";  
	  
      
      //ACA CREO UNA TABLA TEMPORAL CON LAS FACTURAS DEL PERIODO
      //$q = "  CREATE TEMPORARY TABLE if not exists tempo1 as "
      $q = "  SELECT Dic_formulario, nombre, det_formulario.descripcion, dic_descripcion, det_formulario.tipo, root_000033.descripcion "
          ."    FROM usuarios, root_000030, formulario, det_formulario, root_000033 "
          ."   WHERE usuarios.codigo     = '".$wusue[0]."'"
          ."     AND formulario.medico   = usuarios.codigo "
          ."     AND formulario.medico   = det_formulario.medico "
          ."     AND formulario.codigo   = det_formulario.codigo "
          ."     AND dic_usuario         = det_formulario.medico "
          ."     AND dic_formulario      = det_formulario.codigo "
          ."     AND dic_campo           = det_formulario.campo "
          ."     AND det_formulario.tipo = root_000033.codigo "
          ."   GROUP BY 1, 2, 3, 4, 5, 6 "
          ."   ORDER BY 1, 2, 3, 4, 5, 6 ";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	  	  
	  echo "<table border=1>";
	  
	  $row = mysql_fetch_array($err); 
	  
	  $i=1;
	  while ($i <= $num)
	     {
		  $wtabla=$row[0]; 
		  
		  echo "<tr><td colspan=4 bgcolor=CCCCFF><b>Formulario o Tabla: ".$wtabla." - ".$row[1]."</b></font></td></tr>";
		  
		  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CAMPO</font></th>";
		  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
		  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO</font></th>";  
		  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DATO</font></th>";
	      
		  while (($i<=$num) and ($wtabla==$row[0]))
	           {
		        echo "<tr>";
		        echo "<td>".$row[2]."</td>";
		        $row[3]=str_replace("<br>","",$row[3]);
		        echo "<td>".$row[3]."</td>";
		        echo "<td>".$row[4]."</td>";
		        echo "<td>".$row[5]."</td>";
		        echo "</tr>";
		        
		        $row = mysql_fetch_array($err);
		        $i=$i+1;
		       }
		  echo "<tr>";
	      echo "<td>&nbsp</td>";
	      echo "<td>&nbsp</td>";
	      echo "<td>&nbsp</td>";
	      echo "<td>&nbsp</td>";
	      echo "</tr>"; 
	      echo "<tr>";
	      echo "<td>&nbsp</td>";
	      echo "<td>&nbsp</td>";
	      echo "<td>&nbsp</td>";
	      echo "<td>&nbsp</td>";
	      echo "</tr>";  
	      
	      //echo "<br>";
		  //On
		  /////========================================================================
		  $q= " show index from ".$wusue[0]."_".$wtabla;
		  $err_idx = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		  $num_idx = mysql_num_rows($err_idx) or die (mysql_errno()." - ".mysql_error());
		  
		  if ($num_idx > 0)
		     {
		      echo "<tr>";
		      echo "<td>Indices:</td>";
		      echo "</tr>";
		      echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOMBRE</font></th>";
			  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO</font></th>";
			  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CAMPO</font></th>";  
			  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>LONGITUD</font></th>";
		      $row_idx = mysql_fetch_array($err_idx);
			  
		      for ($j=1;$j<=$num_idx;$j++)
			     {
				  $wnomidx=$row_idx[2]; 
				  echo "<tr>";
				  echo "<td>".$row_idx[2]."</td>";
				  echo "<td>".$row_idx[1]."</td>";
				  $wprimero="1";
			      while ($wnomidx==$row_idx[2])
			           {   
				        if ($wprimero == "2")
				           {
				            echo "<tr>";
				            echo "<td>&nbsp</td>";
				            echo "<td>&nbsp</td>";
			               } 
				        echo "<td>".$row_idx[4]."</td>";
				        echo "<td>".$row_idx[7]."</td>";
				        echo "</tr>";
				        $wprimero="2";
				        
				        $row_idx = mysql_fetch_array($err_idx);
			           } 
			     }       
	         }      
		  /////========================================================================
		    
	     }      
	 } 
	 echo "</table>"; 
}
?>
</body>
</html>