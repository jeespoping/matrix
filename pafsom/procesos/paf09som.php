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
		
function CalculaEdad( $fecha ) 
{
	// Como recibe la fecha en format dd/mm/YYYY Lo paso a formato YYYY-mm-dd
    $f = explode("/",$fecha);
    $fecha2 = $f[2]."-".$f[1]."-".$f[0];
	// Ahora calculo la edad actual (solo funciona si la edad esta en YYYY-mm-dd)
	list($Y,$m,$d) = explode("-",$fecha2);
	$Y = (int)$Y;
    return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
}



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//echo "<center><table border=1>";
echo "<table border=1>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>AUTORIZACION DE SERVICIOS PROGRAMA PAF SOM</font></b>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3><i>SOM - CLINICA LAS AMERICAS</font></b><br>";

     $query="SELECT Id,paffec,pafnom,pafape,pafced,paftel,pafran,paftip,pafdia,pafexa,pafcco,Seguridad,pafobs From pafsom_000001 WHERE Id =".$wid; 
     $resultado = mysql_query($query);
     $nroreg = mysql_num_rows($resultado);
     if ($nroreg > 0)      //  Encontro 
     {
      $registro = mysql_fetch_row($resultado); 
      echo "<tr><td align=left  colspan=1><b><font text color=#003366 size=3><i>Numero de orden: ".$wid."</font></b>";
      echo "<td align=left  colspan=1><b><font text color=#003366 size=3><i>Fecha: ".$registro[1]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nombres: ".$registro[2]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Apellidos: ".$registro[3]."</font></b><br>";
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Nro Identificacion: ".$registro[4]."</font></b>";
      echo "<td align=Left  colspan=1><b><font text color=#003366 size=4><i>Telefonos: ".$registro[5]."</font></b><br>";
      
      $query = "SELECT Excento_pago_moderador,Afi_fechanaci FROM pafsom_000002 Where Afi_identific='".$registro[4]."'";  
      $resultadoB = mysql_query($query);
      $nroreg = mysql_num_rows($resultadoB);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultadoB);
       if ( !empty($registroB[0]) )
         $wexento= "  (".$registroB[0].")";
       else  
         $wexento= ""; 
      } 
	  
       switch ($registro[6]) 
      {
       case "01": 
        $r="RANGO I  ".$wexento;
        break;
       case "02": 
        $r="RANGO II  ".$wexento;
        break;
       case "03": 
        $r="RANGO III  ".$wexento;
        break;
       Case  "04":
        $r="RANGO IV  ".$wexento;
        break;
      }
      echo "<tr><td align=Left  colspan=1><b><font text color=#003366 size=4><i>Rango: ".$r."<font text color=#000000 size=4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Edad:".CalculaEdad($registroB[1])."</font></b>";

       switch ($registro[7]) 
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
      
      $query = "SELECT codigo,descripcion FROM root_000011 Where codigo = '".$registro[8]."'";  
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Diagnostico: (".$registroB[0].") ".$registroB[1]."</font></b>";
      }
     
      $query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[9]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=2><b><font text color=#003366 size=3><i>Examen (Cups): (".$registroB[0].") ".$registroB[1]."</font></b>";
      }

      $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccocod='".$registro[10]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<tr><td align=left  colspan=1><b><font text color=#003366 size=3><i>Prestador: ".$registroB[1]."</font></b>";
      }

      $c1=explode('-',$registro[11]); 
      $query = "SELECT descripcion FROM usuarios WHERE codigo='".$c1[1]."'";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      if ($nroreg > 0)      //  Encontro 
      {
       $registroB = mysql_fetch_row($resultado);
       echo "<td align=left  colspan=1><b><font text color=#003366 size=3><i>Generador: ".$registroB[0]."</font></b>";
      }
      
      echo "<tr><td align=left colspan=2><b><font text color=#003366 size=3><i>Observaciones: ".substr($registro[12],0,120)."</font></b><tr>";
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