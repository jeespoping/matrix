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
echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>DETALLE DE PRECIOS POR PROVEEDOR</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>PROGRAMA: cotizaciones10.php Ver. 2010/02/09<br>AUTOR: JairS</font></b><br>";
$fecha = date("Y-m-d");
$hora = (string)date("H:i:s");	     
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Fecha-Hora: ".$fecha." - ".$hora."</font></b><br>";
echo "</table>";

         $query = "SELECT descripcion FROM cotizaci_000005, usuarios"
                 ." WHERE usunit = '".$wcod."'" 
                 ." AND usucod = codigo";     
         $resultado2 = mysql_query($query);
         $nroreg2 = mysql_num_rows($resultado2);
         if ($nroreg2 > 0)
         {
	      $registro2 = mysql_fetch_row($resultado2);  
	      $wpro = $registro2[0];
         }
	     else	    
          $wpro = "";

 $query="SELECT cotcod,connom,cotact,cotcot,cotmar,cotpre,conuni,conmes,conano"
       ." FROM cotizaci_000003, cotizaci_000001"
       ." WHERE cotano=".$wano." AND cotcod = concod AND cotnit = '".$wcod."'"
       ." GROUP  BY cotcod, connom, cotact, cotcot, cotmar, cotpre, conuni, conmes, conano  ORDER  BY connom";          
 $resultado = mysql_query($query);
 $nroreg = mysql_num_rows($resultado);
 if ($nroreg > 0)
 {
  $registro = mysql_fetch_row($resultado);  	
  echo "<center>";  
  echo "<font text color=#CC0000 size=4><A HREF='cotizaciones08.php?wnit=".$wcod."' TARGET='_New'>".$wcod." - ".$wpro."</A></td>";
  
//  echo "<b><font text color=#CC0000 size=4> <i>Proveedor: ".$wcod." - ".$wpro."</font></b><br>";
  echo "</center>";
 
  echo "<center><table border=0>";
  echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>MEDICAMENTOS COTIZADOS</font></b><br>";
  echo "</table>";
 
  echo "<br>";
  echo "<table border=0>";
  echo "<tr>";

  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Linea<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Articulo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Unidad<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cons. Mes<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cons. Año<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Actual<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Cotizado<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>% Incremento<b></td>";
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
	   	    
	   	  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$i."</td>"; 
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
		  
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[6]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[7]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[8]."</td>";
		  
 		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";

		  $incremento=((($registro[3]/$registro[2]) - 1)*100);
		  echo "<td colspan=2 align=center   bgcolor=".$wcf."><font text color=#003366 size=3>".$incremento."</td>";  
		  
 		  echo "<td colspan=2 align=Letf bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";
  
       echo "</tr>";         
          
          $registro = mysql_fetch_row($resultado);  		
          $i++; 
	    }		 
	    
  echo "</table>";
}
  
  
 $query="SELECT cotcod,connom,cotact,cotcot,cotmar,cotpre,conuni,conmes,conano"
       ." FROM cotizaci_000004, cotizaci_000002"
       ." WHERE cotano=".$wano." AND cotcod = concod AND cotnit = '".$wcod."'"
       ." GROUP  BY cotcod, connom, cotact, cotcot, cotmar, cotpre, conuni, conmes, conano  ORDER  BY connom";          
 $resultado = mysql_query($query);
 $nroreg = mysql_num_rows($resultado);
 if ($nroreg > 0)
 {
  $registro = mysql_fetch_row($resultado);  	
  echo "<center>";
  echo "<font text color=#CC0000 size=4><A HREF='cotizaciones08.php?wnit=".$wcod."' TARGET='_New'>".$wcod." - ".$wpro."</A></td>";
  echo "</center>";
 
  echo "<center><table border=0>";
  echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>DISPOSITIVOS MEDICOS COTIZADOS</font></b><br>";
  echo "</table>";
 
  echo "<br>";
  echo "<table border=0>";
  echo "<tr>";

  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Linea<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Articulo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Unidad<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cons. Mes<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cons. Año<b></td>";  
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Actual<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Cotizado<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>% Incremento<b></td>";
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
	   	    
	   	  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$i."</td>"; 
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
		  
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[6]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[7]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[8]."</td>";
		  
 		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";

		  $incremento=((($registro[3]/$registro[2]) - 1)*100);
		  echo "<td colspan=2 align=center   bgcolor=".$wcf."><font text color=#003366 size=3>".$incremento."</td>";  
		  
 		  echo "<td colspan=2 align=Letf bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";
  
       echo "</tr>";         
          
          $registro = mysql_fetch_row($resultado);  		
          $i++; 
	    }		 
	    
  echo "</table>";
} 
  

 $query="SELECT cotcod,connom,cotact,cotcot,cotmar,cotpre,conuni,conmes,conano"
       ." FROM cotizaci_000007, cotizaci_000008"
       ." WHERE cotano=".$wano." AND cotcod = concod AND cotnit = '".$wcod."'"
       ." GROUP  BY cotcod, connom, cotact, cotcot, cotmar, cotpre, conuni, conmes, conano  ORDER  BY connom";          
 $resultado = mysql_query($query);
 $nroreg = mysql_num_rows($resultado);
 if ($nroreg > 0)
 {
  $registro = mysql_fetch_row($resultado);  	
  echo "<center>";
  echo "<font text color=#CC0000 size=4><A HREF='cotizaciones08.php?wnit=".$wcod."' TARGET='_New'>".$wcod." - ".$wpro."</A></td>";
  echo "</center>";
 
  echo "<center><table border=0>";
  echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>ANTISEPTICOS</font></b><br>";
  echo "</table>";
 
  echo "<br>";
  echo "<table border=0>";
  echo "<tr>";

  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Linea<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Articulo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Unidad<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cons. Mes<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cons. Año<b></td>";  
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Actual<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Vlr Cotizado<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>%Incremento<b></td>";
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
	   	    
	   	  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$i."</td>"; 
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[0]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[1]."</td>";
		  
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[6]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[7]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[8]."</td>";
		  
 		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[2]."</td>";
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[3]."</td>";

		  $incremento=((($registro[3]/$registro[2]) - 1)*100);
		  echo "<td colspan=2 align=center   bgcolor=".$wcf."><font text color=#003366 size=3>".$incremento."</td>";  
		  
 		  echo "<td colspan=2 align=Letf bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[4]."</td>";
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".$registro[5]."</td>";
  
       echo "</tr>";         
          
          $registro = mysql_fetch_row($resultado);  		
          $i++; 
	    }		 
	    
  echo "</table>";
} 
    

  
echo "</HTML>";	
echo "</BODY>";
mysql_close($conex);
?>
