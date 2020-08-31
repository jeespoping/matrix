<HTML>
<HEAD>
<TITLE>Orden de servicio</TITLE>
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
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>AUTORIZACION DE SERVICIOS PROGRAMA Inst Mujer SOM</font></b>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>COOMEVA - CLINICA LAS AMERICAS</font></b><br>";

echo "<form name='paf10som' action='paf10som.php' method=post>";  
  
     $query="SELECT * From pafsom_000001 WHERE Id =".$wid; 
     $resultado = mysql_query($query);
     $nroreg = mysql_num_rows($resultado);
     if ($nroreg > 0)      //  Encontro 
     {
      $registro = mysql_fetch_row($resultado); 
      echo "<tr><td align=left  colspan=1><b><font text color=#003366 size=3><i>Numero de orden: ".$wid."</font></b>";
      echo "<td align=left  colspan=1><b><font text color=#003366 size=3><i>Fecha: ".$registro[3]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nombres: ".$registro[6]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Apellidos: ".$registro[5]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nro Identificacion: ".$registro[4]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Telefonos: ".$registro[7]."</font></b><br>";
      
       switch ($registro[8]) 
      {
       case "01": 
        $r="RANGO I";
        break;
       case "02": 
        $r="RANGO II";
        break;
       case "03": 
        $r="RANGO III";
        break;
       Case  "04":
        $r="RANGO IV";
        break;
      }
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Rango: ".$r."</font></b>";

       switch ($registro[9]) 
      {
       case "01": 
        $t="COTIZANTE";
        break;
       case "02": 
        $t="BENEFICIARIO";
        break;
       case "03": 
        $t="ADICIONAL";
        break;
      }
      echo "<td align=Left colspan=1><b><font text color=#003366 size=4><i>Tipo de afiliado: ".$t."</font></b><br>";
      
      $query = "SELECT codigo,descripcion FROM root_000011 Where codigo = '".$registro[10]."'";  
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Diagnostico: (".$registroB[0].") ".$registroB[1]."</font></b>";
      }
     
      $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[11]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Examen (Cups): (".$registroB[0].") ".$registroB[1]."</font></b>";
      }

      $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccocod='".$registro[12]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=1><b><font text color=#003366 size=3><i>Prestador: ".$registroB[1]."</font></b>";
      }

      $c1=explode('-',$registro[15]); 
      $query = "SELECT descripcion FROM usuarios WHERE codigo='".$c1[1]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<td align=left  colspan=1><b><font text color=#003366 size=3><i>Generador: ".$registroB[0]."</font></b>";
      }
     
      echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Firma y sello:</font></b>"; 
      echo "<br>"; 
      echo "<br>"; 
      
      // $wid variable escondidas que enviaremos cada vez a travez del formulario	   	   	     
   	   if (isset($wid))
	     echo "<INPUT TYPE = 'hidden' NAME='wid' VALUE='".$wid."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wid'></INPUT>"; 

      
      echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	  echo "<input type='submit' value='Anular'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	  echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	  $f = date("Y-m-d");
   	  if ( $conf == "on")
   	  { $query="UPDATE pafsom_000001 SET pafest='X',pafanu='".$user."-".$f."' WHERE Id =".$wid;
	    $resultado = mysql_query($query,$conex);  
	    if ($resultado)
	     echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Anulado</td></tr>";
        else
	    {
	     echo "<table border=1>";	 
	     echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	     echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL ANULAR!!!!</MARQUEE></font>";				
	     echo "</td></tr></table><br><br>";
	    }
   	 }
   }
echo "</form>";
echo "</table>";
Mysql_close(); 
echo "</BODY>";
echo "</HTML>";	
?>