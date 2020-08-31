<HTML>
<HEAD>
<TITLE>Programa para arreglar inconsistencias en el integrador</TITLE>
</HEAD>
<BODY>

<?php
include_once("conex.php");
/*
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
*/


//Conexion a SQL
mysql_select_db("matrix") or die("No se selecciono la base de datos");    
//Conexion a Informix 
$conexN = odbc_connect('Inventarios','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

   echo "<center><table border=0>";
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Procesando...</font></b><br>";
   
   echo "</table>";
   
   // Tabla temporal con los documentos con problemas de Integracion
   $query="Select * from itdrotmp order by tmpnum";
    $resultado = odbc_exec($conexN,$query);
    echo 
    $nroreg = odbc_num_rows($resultado);
    echo "A: ".$nroreg;
    if ($nroreg > 0)      //  Encontro 
    {
	 while ($registro = odbc_fetch_array($resultado))     
     {  	   
      
      $query="UPDATE itdro SET droest='S' Where dronum=".$registro["tmpnum"]
            ." And drofue='".$registro["tmpfue"]."' And drofec='".$registro["tmpfec"]."'"
            ." And drohis=".$registro["tmphis"]." And droccc='".$registro["tmpcco"]."'"
            ." And droart='".$registro["tmpart"]."' And drocan=".$registro["tmpdif"]; 
      echo "<br>";
      echo $query;
      
      //$resultado2 = odbc_exec($conexN,$query);              // Ejecuto el query en UNIX 
      
      
      echo "<br>";
      echo "Se Arreglo el documento: ".$registro["tmpnum"]." Articulo: ".$registro["tmpart"];  
      
     } 
     echo "<br>";
     echo "Ahora debe ejecutar el integrador!!!! ";  
    }
    else
    {
	 echo "<td colspan=4 align=center bgcolor='DDDDDD'><font text color=#003366 size=3>NO Hay registros para arreglar. </td>";     
    } 
    
    //odbc_close($conexN);
    mysql_close(); 
    echo "</table>";
 
 

echo "</BODY>";
echo "</HTML>";	
odbc_close($conexN);
odbc_close_all();
?>