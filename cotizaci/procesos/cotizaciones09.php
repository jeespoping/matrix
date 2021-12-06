<html>
<head>
<title>Consulta precios de un proveedor</title>
</head>

<script>
function ira(){document.cotizaciones09.wcodcup.focus();}
</script>

<body  onload=ira() BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//===============================================================================================================
//PROGRAMA				      :Analisis por proveedor                                                            
//AUTOR				          :Jair Saldarriaga Orozco.                                                          
//FECHA CREACION			  :Marzo 06 De 2013.                                                                 
//FECHA ULTIMA ACTUALIZACION  :Marzo 06 De 2013.                                                                 

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
	
  

  mysql_select_db("matrix") or die("No se selecciono la base de datos");   
    
// *********************************************
//$fecha = date("Y-m-d");  
//$wano=substr($fecha,0,4);

$wano="2021";
// *********************************************

if (!isset($nroreg))
{
        echo "<form name='cotizaciones09' action='cotizaciones09.php' method=post>";
        echo "<center><table border=1 >";
		//echo "<tr><td colspan=1 rowspan=4  align=center><IMG SRC='logo1.gif' ></td>";				
		echo "<tr><td colspan=3 align=center bgcolor=#cccccc><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=3 align=center bgcolor=#cccccc><b>DIRECCION DE INFORMATICA CLINICA<b></td></tr>";
		echo "<tr><td colspan=3 align=center bgcolor=#cccccc><b>ANALISIS POR PROVEEDOR POR TIPO DE ARTICULO (".$wano.")<b></td></tr>";
		
		
        echo "<tr><td colspan=3 bgcolor=#cccccc align=center>";
        if ($radio1=="" or $radio1==1)	//Si no esta seteado o esta en 1 lo chequeo
    	 echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE=1 CHECKED>Medicamentos<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
    	else
    	 echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE=1 >Medicamentos<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
    	
    	if ($radio1==2)	// Si esta en 2 lo chequeo 
          echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE=2 CHECKED>Materiales<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        else
          echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE=2 >Materiales<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
          
        if ($radio1==3)	// Si esta en 2 lo chequeo 
          echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE=3 CHECKED>Antisepticos<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
        else
          echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE=3 D>Antisepticos<b>&nbsp;&nbsp;&nbsp;</b></INPUT>"; 
          		        
        echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Codigo:</font></b><br>";
        if (isset($wcodcup))
    	 echo "<INPUT TYPE='text' NAME='wcodcup' size=10 VALUE='".$wcodcup."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wcodcup' size=10></INPUT></td>";
    	 
    	echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Nombre proveedor:</font></b><br>";
    	if (isset($wnomcup))
    	 echo "<INPUT TYPE='text' NAME='wnomcup' size=70 VALUE='".$wnomcup."'></INPUT></td>"; 
    	else
    	 echo "<INPUT TYPE='text' NAME='wnomcup' size=70></INPUT></td>";  

    	   	
    	echo "<tr><td align=center colspan=3 bgcolor=#cccccc size=10>";
    	echo "<input type='submit' value='Consultar'>";
    	echo "<input type='reset'  value='Restablecer'></tr></td>";
   	    	
		echo "</center></table>";			
		
    	//echo "<BR><BR>";
		//echo "<center><li><A HREF='SALIDA.php'>Salir del programa</A></center>";		
}

if ((isset($wcodcup) or isset($wnomcup) ))
///////////      Cuando ya hay datos digitados      /////////////////
{  
	     $blanco = 0;
         $parteI = "SELECT usunit, descripcion FROM cotizaci_000005, usuarios WHERE usucod = codigo";

         
         If ( $wcodcup != "" ) 
         {
           $parte1 = " AND usunit LIKE '".$wcodcup."'";
           $blanco = 1;
         }  
         Else
           $parte1 = "";
           
         if ( $wnomcup != "" )
         {
		  $parte2 = " AND descripcion LIKE '%".$wnomcup."%'";
  		  $blanco = 1;
  		 } 
 		 Else
  		  $parte2 = "";  
           
 		  
  		 $parteF = " ORDER BY descripcion";
   	  		  
  		 if ( $blanco == 1 )    // ' Al menos hubo un campo que se lleno
  		  $query = $parteI.$parte1.$parte2.$parteF;
  		 else
  		  $query = "SELECT usunit, descripcion FROM cotizaci_000005, usuarios WHERE usucod = codigo ORDER BY descripcion";
  		 		
	        echo"<center><table> ";
	        echo"<td bgcolor = #999999><b>CODIGO</td>";
	        echo"<td bgcolor = #999999><b>DESCRIPCION</td>";

		  $resultado = mysql_query($query);
		  $nroreg = mysql_num_rows($resultado);
		  $registro = mysql_fetch_row($resultado);
		  $nrocam= mysql_num_fields($resultado);
         
		  //Muestro el primer registro        
		   $wcodcup = $registro[0];
		   $wnomcup = $registro[1];	       
		  
          $cont = 0;
          for ($i=1;$i<=$nroreg;$i++)
          { 	
	        // color de fondo  
            if (is_int ($cont/2))  // Cuando la variable cont es par coloca este color
	          $wcf="DDDDDD";
	   	    else
	   	      $wcf="CCFFFF";           
	   	    echo "<tr bgcolor=".$wcf.">";
	   	    
			$cont++;
			for($j=1;$j<=$nrocam;$j++)
			  echo"<td>".$registro[$j-1]."</td>";
						
            echo "<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";
	        echo "<A HREF='cotizaciones10.php?wcod=".$registro[0]."&wano=".$wano."'>Detallar</A></td>";	     
            echo "<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";
	        echo "<A HREF='cotizaciones31.php?wcod=".$registro[0]."&wano=".$wano."&wtipo=".$radio1."'>Analisis</A></td>";	     


			$registro = mysql_fetch_row($resultado);
			   	    
			echo "</tr>";
		   }
		   echo "Registros seleccionados: ".$nroreg;
	   	   echo"</center></table>";	
	   	   	
	   	   echo "</Form>";	 		
	   	   Mysql_close($conex);    
	   	   
	   	   unset($nroreg);       
} 				
//   }
?>
</body>
</html>
