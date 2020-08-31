<HTML>
<HEAD>
<TITLE>Orden de servicios PAF SURA</TITLE>
</HEAD>
<BODY>

<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

echo "<center><table border=1>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>AUTORIZACION DE SERVICIOS PROGRAMA PAF SURA</font></b>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>SURA EPS - CLINICA LAS AMERICAS</font></b><br>";

     $query="SELECT paffau,pafnom,pafap1,pafap2,pafced,paftel,pafdia,pafexa,pafcco,Seguridad,pafobs,paffci From pafsura_000001 WHERE Id =".$wid; 
     $resultado = mysql_query($query);
     $nroreg = mysql_num_rows($resultado);
     if ($nroreg > 0)      //  Encontro 
     {
      $registro = mysql_fetch_row($resultado); 
      echo "<tr><td align=left  colspan=1><b><font text color=#003366 size=3><i>Numero de orden: ".$wid."</font></b>";
      echo "<td align=left  colspan=1><b><font text color=#003366 size=3><i>Fecha: ".$registro[0]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nombres: ".$registro[1]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Apellidos: ".$registro[2]." ".$registro[3]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nro Identificacion: ".$registro[4]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Telefonos: ".$registro[5]."</font></b><br>";
      
      $query = "SELECT codigo,descripcion FROM root_000011 Where codigo = '".$registro[6]."'";  
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Diagnostico: (".$registroB[0].") ".$registroB[1]."</font></b>";
      }
     
      $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[7]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Examen (Cups): (".$registroB[0].") ".$registroB[1]."</font></b>";
      }

      $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccocod='".$registro[8]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=1><b><font text color=#003366 size=3><i>Unidad: ".$registroB[1]."</font></b>";
      }		
      $c1=explode('-',$registro[9]); 
      $query = "SELECT descripcion FROM usuarios WHERE codigo='".$c1[1]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<td align=left  colspan=2><b><font text color=#003366 size=3><i>Genera: ".$registroB[0]."</font></b></td>";
      }
      
	  echo "<tr><td align=Left  colspan=2><b><font text color=#FF0000 size=4><i>Fecha Cita: ".$registro[11]."</font></b>"; 
	  
      echo "<tr><td align=left colspan=2><b><font text color=#003366 size=3><i>Observaciones: ".substr($registro[10],0,120)."</font></b><tr>";
      echo "<tr><td align=left colspan=2><b><font text color=#003366 size=3><i>Firma y sello:</font></b>"; 
      echo "<br>"; 
      echo "<br>"; 
      echo "</tr>";
     
   }
echo "</table>";
Mysql_close(); 
echo "</BODY>";
echo "</HTML>";	
?>