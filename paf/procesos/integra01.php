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
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

//Forma
echo "<form name='integra01' action='integra01.php' method=post>";  
 
 if (!isset($whis) or !isset($wing))
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Programa para arreglar inconsistencias en el integrador<br></font></b></td>";   
	echo "</tr>";
	
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Nro de Historia:</font></b><br>";
    if (isset($whis))
     echo "<INPUT TYPE='text' NAME='whis' size=30 maxlength=20 VALUE='".$whis."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='whis' size=30 maxlength=20 ></INPUT></td>"; 

    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Nro de Ingreso:</font></b><br>";
    if (isset($wing))
     echo "<INPUT TYPE='text' NAME='wing' size=30 maxlength=20 VALUE='".$wing."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wing' size=30 maxlength=20></INPUT></td>"; 

   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Arreglar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos
 {
   echo "<center><table border=0>";
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4><i>Procesando...</font></b><br>";
   
   echo "</table>";
   
   // Query que devuelve los documentos con problemas de Integracion
   $query="Select fennum from movhos_000002" 
         ." Where fenhis=".$whis 
         ." And   fening=".$wing 
         ." And   fenues = 'I' " 
         ." And   fendoc = '0' "; 
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)      //  Encontro 
    {
	 while ($registro = mysql_fetch_row($resultado))     
     {  	   
      $wdocumento=$registro[0];
      
/*    $query="UPDATE movhos_000003 set fdeubi='US' Where fdenum=".$wdocumento;
      $resultado2 = mysql_query($query);                   // Ejecuto el query en MATRIX

      $query="UPDATE movhos_000002 set fenues='S' Where fenhis=".$whis." And fening=".$wing." And fenues='I' And fendoc='0' "; 
      $resultado2 = mysql_query($query);                  // Ejecuto el query en MATRIX
*/      
      $query="UPDATE itdro SET droest='S' Where dronum=".$wdocumento; 
      $resultado2 = odbc_do($conexN,$query);              // Ejecuto el query en UNIX 
      echo "<br>";
      echo "Se Arreglo el documento: ".$wdocumento;  
      
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
 }   
 

echo "</BODY>";
echo "</HTML>";	
odbc_close($conexN);
odbc_close_all();
?>