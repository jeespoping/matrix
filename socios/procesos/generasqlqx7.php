<?php
include_once("conex.php"); 	
/*
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>".
        " index.php</FONT></H1>\n</CENTER>");
*/		
?>
<html>
<head>
<title>Generacion de Consultas por SQL</title>
</head>

<script type="text/javascript">
    function ira()
    {
	 document.generasqlqx7.wsql.focus();
	}

	function enter()
	{
	 document.forms.generasqlqx7.submit();
	}
	
	function vaciarCampos()
	
	{
	 document.forms.generasqlqx7.wsql.value = '';
    }

</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

<?php 	

  /************************************ L E A M E ***************************************************************************
   Para consultas en tablas grandes utilice:       Select TOP 100 campo1,campo2,campo3 
                                                   From NombreTabla
  
   Para crear tablas apartir de un Select utilice: Select campo1,campo2,campo3 INTO NombreTablaNew 
                                                   From NombreTabla Where...
  
   Todo query que genere registros genera un archivo plano en archivos/plano01.txt
  
   Ademas si la tabla tiene campo tipo TimeStamp borre el campo primero eso no se necesita para nada con la instruccion:
   ALTER TABLE NombreTabla DROP COLUMN SSMA_TimeStamp
 
   OJO Ademas tenga encuenta que tablas como ADMANT tiene campos con datos separdos por pipe | asi que cambie el seprador
  
  **************************************************************************************************************************/
  
 	

	//$conex = odbc_connect("queryx7LMLA","","") or die(odbc_errormsg());  //Laboratorio
	//$conex = odbc_connect("queryx7","","") or die(odbc_errormsg());  //Promotora
  
    // Abro el tag FORM ojo debe llevar "name" para poder ejecutar las funciones ira() y enter()								
    echo "<form name='generasqlqx7' action='generasqlqx7.php' method=post>";
  
  	  echo "<center><table border=0>";
	  echo "<tr><td align=center><b>UTILIDAD PARA BASE DE DATOS queryx7<b></td></tr>";
	  echo "<tr><td align=center>CON GENERACION DE ARCHIVOS PLANOS  JairS</td></tr>";
	  
// Si No hay instruccion O si el checkbox no esta confirmado  	** LO COMENTE PARA QUE ME VUELVA A PINTAR LOS CAMPOS **
//	if ((!isset($wsql)) or (!$confirmar)) 
//	{
	 
     echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Tablas: </font></b>";   
		 
/*
	 $resultado = odbc_tables($conex);
	 
	 echo "<select name='wtab' onchange='enter()'>";  
     $i = 1;
     While (odbc_fetch_into($resultado, $registro))           // En $registro[0] queda el nombre de la base de datos BDESE en 1 queda el propietario DBO 
	  {	                                                      // en 2 el nombre de la tabla y en la posocion 3 el tipo 
       if(odbc_result($resultado,"TABLE_TYPE")=="TABLE")
	   {		   
  		if($wtab == $registro[2])
	      echo "<option selected>".$registro[2]."</option>";
	    else
	      echo "<option>".$registro[2]."</option>"; 
	    $i++;
       }		
      }   
     odbc_free_result($resultado);
*/     
    echo "</select>";
	
	 echo "<P><B><LABEL>CONEXION:</B></LABEL>";
	 echo "<INPUT TYPE='RADIO' NAME='conexion' VALUE='1' CHECKED>Promotora";
	 echo "<INPUT TYPE='RADIO' NAME='conexion' VALUE='2'>Laboratorio";
	 echo "<INPUT TYPE='RADIO' NAME='conexion' VALUE='3'>Patologia";
	 echo "<INPUT TYPE='RADIO' NAME='conexion' VALUE='4'>Clisur</P>";
	 echo "<INPUT TYPE='RADIO' NAME='conexion' VALUE='5'>IDC</P>";
	 
	 if ($conexion == 1)
		$conex = odbc_connect("queryx7","","") or die(odbc_errormsg());  //Promotora
	 if ($conexion == 2)
		$conex = odbc_connect("queryx7LMLA","","") or die(odbc_errormsg());  //Laboratorio
	 if ($conexion == 3)
		$conex = odbc_connect("queryx7PAT","","") or die(odbc_errormsg());  //Patologia
	 if ($conexion == 4)
		$conex = odbc_connect("queryx7CS","","") or die(odbc_errormsg());  //Clisur conexion
	 if ($conexion == 5)
		$conex = odbc_connect("queryx7IDC","","") or die(odbc_errormsg());  //IDC
		
	echo "<b><font text color=#003366 size=2 align=center>  Separador de campos: </font></b>"; 
    if (isset($wsep))
     echo "<INPUT TYPE='text' NAME='wsep' size=3 maxlength=1 style='text-align:center' VALUE='".$wsep."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wsep' size=3 maxlength=1 style='text-align:center' VALUE='|'></INPUT></td>"; 
	
    if (isset($wtab))
    {
     $result = odbc_do( $conex,"Select * From ".$wtab );    // Al crear esta variable result puedo con las siguientes instruciones obtener:
     $fields =odbc_num_fields($result);                     // Nro de campos de cada registro en la tabla *
     $campos = odbc_num_fields($result);                    // Nro de campos columnas arrojados por el query     
    
        
     if (strlen($wsql) < 1)    // Si el campo instruccion Sql esta vacio por comodidad coloca automaticamente un select asi 
     {                                                                                                                     
       //$wsql="Select * From ".$wtab;                                                                                  
       $wsql="Select ";                                                                                                     
       for ($j=1;$j<$campos;$j++)                                                                                        
          $wsql=$wsql.odbc_field_name($result, $j).",";	                                                                
       $wsql=$wsql.odbc_field_name($result, $j)." From ".$wtab;                                                         
     }                                                                                                                   
    }       
	  echo "<tr>";
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Campos: </font></b>";   
      echo "<select name='wcam'>"; 
      for ($i=1; $i <= $fields; $i++) // y para cada campo tomo....
      {
	   $name  = odbc_field_name($result, $i);      //Nombre del campo $i
       $type  = odbc_field_type($result, $i);      //Tipo de dato del campo $i 
       $len   = odbc_field_len($result, $i);       //Longitud o tamaño del campo $i
       $flags = odbc_primarykeys($result, $i);     //Obtiene las claves primarias de una tabla  
 		if($wcam == $name)
	      echo "<option selected>".$name.";".$type.";".$len.";".$flags."</option>";
	    else
	      echo "<option>".$name."; ".strtoupper($type)."; ".$len."; ".$flags."</option>";
      }
      echo "</select>";
      
      if (isset($wtab))
        echo "<br><A HREF='subirplano.php?tabla=".$wtab."&wsep=".$wsep."'>Cargar Archivo plano01.txt desde ../fachce/archivos en ".$wtab."</td>";   
   
     echo "<tr>"; 								
     echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>INSTRUCCION SQL</font></b><br>";
     
     if (isset($wsql))
      echo "<textarea name='wsql' cols=80 rows=15>".stripslashes($wsql)."</textarea></td>";
     else
      echo "<textarea name='wsql' cols=80 rows=15></textarea></td>";
//	}
	
	
    echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";
    echo "<input type='submit' value='Ejecutar'>";
    //if(isset($confirmar))
	// echo "<input type='checkbox' name=confirmar checked>Confirmar</td></tr>";
	//else
	   echo "<input type='checkbox' name=confirmar>Confirmar</tr></td>";     
    
	echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";
	//if(isset($visualizar))
	// echo "<input type='checkbox' name=visualizar checked>Visualizar Resultados</td></tr>";
	//else
	 echo "<input type='checkbox' name=visualizar>Visualizar Resultados</td></tr>";     
	 
	echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";   
	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'></td>";  
    

/*****************************************************************************************/
    
    // Si ya hay instruccion y el checkbox esta confirmado genero archivo plano y si esta marcado visualizar resultados tambien 
    if ( (isset($wsql)) and (isset($confirmar)) )
    {
	  $query=strtolower($wsql);            // Paso toda la instruccion a minuscula 
	  
      $pos1 = strpos($query, "delete");    // Busco palabras reservadas que no generan un snapshot con tuplas de registros
      $pos2 = strpos($query, "drop");
      $pos3 = strpos($query, "update");  
      $pos4 = strpos($query, "truncate table"); 
      $pos5 = strpos($query, "alter table"); 
      $pos6 = strpos($query, "insert"); 
      $pos7 = strpos($query, "create"); 
      $pos8 = strpos($query, "into");  
 
      if ( ($pos1 === false) and ($pos2 === false) and ($pos3 === false) and ($pos4 === false)  and ($pos5 === false) and ($pos6 === false) and ($pos7 === false) and ($pos8 === false) )
      {
	      
	      // Ruta para el archivo plano 
	      $ruta=  "archivos/";  
	      //Si la ruta destino NO existe lo crea con permisos
		  if (!file_exists($ruta)) 			
		    mkdir($ruta,0777);
	      	       
   		  // Abro el archivo
   		  $archivo = fopen($ruta."plano01.txt","w"); 
   		  // Ejecuto el Query
   		  echo "<tr><td align=center colspan=1 bgcolor=#F79F81>";
   		  $query=stripslashes($wsql);                           //Como el string pasa por el navegador quito las barras del string con comillas escapadas
          $resultado = odbc_exec($conex,$query) ;
          echo "</td>";
          // Nro de campos columnas arrojados por el query
          $campos = odbc_num_fields($resultado);            
          $i = 1;
          
          While (odbc_fetch_into($resultado, $registro) )
	      { 
	         //Genero Archivo Plano
             $LineaDatos="";
           	 for ($j=0;$j<$campos;$j++)
           	 {
			  
			  //(Ojo los campos se numeran desde 1 pero los datos en el arreglo quedan desde 0)	 
           	  $type = strtolower(odbc_field_type($resultado, $j+1));   // Tomo el tipo del campo  		   
           	  $name = strtolower(odbc_field_name($resultado, $j+1));   // Tomo el nombre del campo			  
			  
           	    if (($type == "ntext")  or ($type == "nvarchar"))         // Si es campo MEMO o STRING Reemplazo  
           	    {                                                         
           		 $nuevostr = str_replace(chr(13), "", $registro[$j] );   // los (CR) Carriage return y los (LF) Line Feed salto de linea  
	           	 $nuevostr = str_replace(chr(10), ";", $nuevostr );      // osea que la combinacion CRLF se cambia por espacio y punto coma
	           	 $LineaDatos=$LineaDatos.$nuevostr.$wsep;
				 //echo "Tipo: ".$type." Nombre: ".$name."<br>"; 
	            }
                else
                {
                 $LineaDatos=$LineaDatos.$registro[$j].$wsep;
                }
           
              
             }
             fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
             
			//Visualizo la tabla
			if ( isset($visualizar))
			{
             		 
			 if ($i==1)   //Si es el primer registro definino la tabla y coloco los nombres de los campos como titulos
			 {
			  echo "<center><table border=0>";
              for ($j=1;$j<=$campos;$j++)             
                echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>".odbc_field_name($resultado, $j)."<b></td>";	
			  echo "<tr>";	
			 }
			 
			 // color de la linea de datos  
             if (is_int ($i/2))    // Cuando la variable $i es par coloca este color
              $wcf="DDDDDD";   
             else
              $wcf="CCFFFF";  
  	
             for ($j=0;$j<$campos;$j++)   	      			
               echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$registro[$j]."</td>";
			 echo "<tr>";	
			 
			}	  
			 
           $i++;
		  }
          fclose($archivo);
          
     	  echo "<center><A href=".$ruta."plano01.txt"." ><b>Presione Clic Derecho Para Bajar el Archivo Plano ../fachce/archivos/plano01.txt) </A></b>";
    	  echo "<br><b>Registros generados: ".($i-1)."</b>";

    	  if ( isset($visualizar))
		  {
		   echo "</Table>";
           echo "<META  HTTP-EQUIV='Window-target' CONTENT='_top'>";	    // Para que regrese a la parte superior
          }
		  
      }
	  else
	  {
	   $query=stripslashes($wsql);      //Como el string pasa por el navegador quito las barras del string con comillas escapadas
       $resultado = odbc_exec($conex,$query);
       if ($resultado)
       {
	    echo "<tr><td align=center colspan=1 bgcolor=#F5DEB3>";
	    echo "Se ejecuto la Instruccion SQL con exito.....";
	    echo "</td>";
       }
      }
     }
	 
    echo "</form>";
?>
</body>
</html>