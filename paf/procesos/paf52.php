<HTML>
<HEAD>
<TITLE>Control de la Atencion</TITLE>
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
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>CONTROL DE LA ATENCION</font></b>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=3><i>NUEVA EPS - CLINICA LAS AMERICAS</font></b><br>";

     $query="SELECT atefec,atehor,ateced,atenom,atehis,atecar,ateord,atecit,atecon,ateex1,ateex2,atemed,atefpr,atehpr,ateobs From paf_000003 "
           ." Where ateced = '".$wced."' And atefec = '".$wfec."' And atehor = '".$whor."'";
		   
     $resultado = mysql_query($query);
     $nroreg = mysql_num_rows($resultado);
     if ($nroreg > 0)      //  Encontro 
     {
      $registro = mysql_fetch_row($resultado); 
      echo "<td align=left  bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=3><i>Fecha/Hora: ".$registro[0]." ".$registro[1]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Cedula: ".$registro[2]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nombre: ".$registro[3]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nro Historia: ".$registro[4]."</font></b>";
	  echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Proximo Control: ".$registro[12]." Hora: ".$registro[13]."</font></b><br>";
     
      $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[9]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Examen 1 (Cups): (".$registroB[0].") ".$registroB[1]."</font></b>";
      }
	  if ($registro[10] <> "")
	  {
        $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[10]."'";
        $resultado = mysql_query($query);
        $nroreg = mysql_num_rows($resultado);
        if ($nroreg > 0)      //  Encontro 
        {
         $registroB = mysql_fetch_row($resultado);
         echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Examen 2 (Cups): (".$registroB[0].") ".$registroB[1]."</font></b>";
        }
	  }	
	
      // TOMO EL NOMBRE DEL MEDICO
        $query = "SELECT Descripcion FROM citaspaf_000010 WHERE codigo='".$registro[11]."' Group By Descripcion"; 
        $resultado=mysql_query($query); 
        $nroreg = mysql_num_rows($resultado);
        if ($nroreg > 0)      //  Encontro 
        {
          $registroB = mysql_fetch_row($resultado);
          echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Medico: ".$registroB[0]."</font></b>";
        }
      
      echo "<tr><td align=left colspan=2><b><font text color=#003366 size=3><i>Observaciones: ".substr($registro[14],0,120)."</font></b><tr>";
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