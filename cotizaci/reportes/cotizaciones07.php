<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
        
echo "<HTML>";
echo "<HEAD>";
echo "<TITLE>CLINICA LAS AMERICAS</TITLE>";
echo "</HEAD>";
echo "<BODY>";



mysql_select_db("matrix") or die("No se selecciono la base de datos");   

echo "<center><table border=0>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>DETALLE DE PRECIOS POR ARTICULO</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>PROGRAMA: cotizaciones07.php Ver. 2010/02/09<br>AUTOR: JairS</font></b><br>";
$fecha = date("Y-m-d");
$hora = (string)date("H:i:s");	     
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Fecha-Hora: ".$fecha." - ".$hora."</font></b><br>";
echo "</table>";


 if ($wtipo==1)
 {
  $query ="SELECT cotcod CODIGO, connom NOMBRE, cotcot COTIZADO, cotnit NIT,"
         ." cotmar MARCA, cotpre PRESENTACION, cotact ANTERIOR" 
         ." FROM cotizaci_000003, cotizaci_000001 "
         ." WHERE cotano = '".$wano."'" 
         ." AND cotcod = '".$wcod."'"
         ." AND cotcod = concod" 
         ." GROUP  BY cotcod, connom, cotcot, cotnit, cotmar, cotpre, cotact" 
         ." ORDER  BY cotcod, cotcot"; 
 }
 else
 {
  if ($wtipo==2)
  {
   $query ="SELECT cotcod CODIGO, connom NOMBRE, cotcot COTIZADO, cotnit NIT,"
         ." cotmar MARCA, cotpre PRESENTACION, cotact ANTERIOR" 
         ." FROM cotizaci_000004, cotizaci_000002 "
         ." WHERE cotano = '".$wano."'" 
         ." AND cotcod = '".$wcod."'"
         ." AND cotcod = concod" 
         ." GROUP  BY cotcod, connom, cotcot, cotnit, cotmar, cotpre, cotact" 
         ." ORDER  BY cotcod, cotcot"; 
  }                 
  else   
  {
   $query ="SELECT cotcod CODIGO, connom NOMBRE, cotcot COTIZADO, cotnit NIT,"
         ." cotmar MARCA, cotpre PRESENTACION, cotact ANTERIOR" 
         ." FROM cotizaci_000007, cotizaci_000008 "
         ." WHERE cotano = '".$wano."'" 
         ." AND cotcod = '".$wcod."'"
         ." AND cotcod = concod" 
         ." GROUP  BY cotcod, connom, cotcot, cotnit, cotmar, cotpre, cotact" 
         ." ORDER  BY cotcod, cotcot"; 
  } 
 }
  
 $resultado = mysql_query($query);
 $nroreg = mysql_num_rows($resultado);
 if ($nroreg > 0)
 {
   $registro = mysql_fetch_row($resultado);  	
   echo "<center>";
   echo "<b><font text color=#CC0000 size=2> <i>Articulo: ".$registro[0]." - ".$registro[1]."</font></b><br>";
   echo "</center>";
 }  

echo "<br>";
echo "<table border=0>";
echo "<tr>";

echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Actual<b></td>";
echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Cotizado<b></td>";
echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>% Incremento<b></td>";
echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Nit.<b></td>";
echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Proveedor<b></td>";
echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Marca<b></td>";
echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Presentacion<b></td>";
echo "</tr>";         

 		$i = 1;
		While ($i <= $nroreg)
		{			   	    
				     
		  // color de fondo  
	      if (is_int ($i/2))  // Cuando la variable $k es par coloca este color
	       $wcf="DDDDDD";  
	   	  else
	   	   $wcf="CCFFFF";    	
	   	     
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[6]."</td>";
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		  $incremento=((($registro[2]/$registro[6]) - 1)*100);
		  echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$incremento."</td>";    

		  echo "<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";
		  echo "<A HREF='cotizaciones08.php?wnit=".$registro[3]."' TARGET='_New'>".$registro[3]."</A></td>";	     
      		 
		  //echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";
		  
         $query = "SELECT descripcion FROM cotizaci_000005, usuarios"
                 ." WHERE usunit = '".$registro[3]."'" 
                 ." AND usucod = codigo";     
         $resultado2 = mysql_query($query);
         $nroreg2 = mysql_num_rows($resultado2);
         if ($nroreg2 > 0)
         {
           $registro2 = mysql_fetch_row($resultado2);  	
		   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[0]."</td>";
	     }  
		 else
		   echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3></td>";
		 
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";		
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";		    
         echo "</tr>";         
          
          $registro = mysql_fetch_row($resultado);  		
          $i++; 
	    }		

echo "</table>";
echo "</HTML>";	
echo "</BODY>";


mysql_close($conex);
?>
