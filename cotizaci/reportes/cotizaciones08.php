<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");        

echo "<HEAD>";
echo "<TITLE>P.M.L.A.</TITLE>";
echo "</HEAD>";
echo "<BODY>";

echo "<br>";
echo "<center><table border=0>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#003366 size=4> <i>CONDICIONES FINANCIERAS</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#003366 size=2> <i>Programa: cotizaciones08.php Ver. 2010/01/10<br>AUTOR: JairS</font></b><br>";
echo "</table>";

echo "<br>";
echo "<center><table border=0>";
echo "<tr>";



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

$query = "SELECT prvcod,prvbpm,prvsgc,prvdf1,prvdi1,prvdf2,prvdi2,prvdf3,prvdi3,prvpla,prvobs"
        ." FROM cotizaci_000006 WHERE prvcod='".$wnit."'";  
$resultado = mysql_query($query);
$nroreg = mysql_num_rows($resultado);
if ($nroreg > 0)
{
 $numcam = mysql_num_fields($resultado);
 $registro = mysql_fetch_row($resultado);  
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Codigo: <b></td>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro[0]."<b></td>";
 echo "<tr>";
         $query = "SELECT descripcion FROM cotizaci_000005, usuarios"
                 ." WHERE usunit = '".$registro[0]."'" 
                 ." AND usucod = codigo";     
         $resultado2 = mysql_query($query);
         $nroreg2 = mysql_num_rows($resultado2);
         if ($nroreg2 > 0)
         {
	      $registro2 = mysql_fetch_row($resultado2);  	    
          echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Nombre: <b></td>";
          echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro2[0]."<b></td>";
          echo "<tr>";
         } 
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Descuento 1: <b></td>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro[3]."% a ".$registro[4]." Dias<b></td>";
 echo "<tr>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Descuento 2: <b></td>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro[5]."% - ".$registro[6]." Dias<b></td>";
 echo "<tr>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Descuento 3: <b></td>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro[7]."% - ".$registro[8]." Dias<b></td>";
 echo "<tr>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Plazo: <b></td>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro[9]." Dias<b></td>";
 echo "<tr>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><b>Observaciones: <b></td>";
 echo "<td colspan=2 align=left bgcolor=#DDDDDD><font text color=#003366 size=3><b>".$registro[10]."<b></td>";
}
else
{
 echo "<table border=1>";	 
 echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
 echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>Proveedor NO especifico condiciones financieras.</MARQUEE></font>";				
 echo "</td></tr></table><br><br>";	
}
echo "</tr>"; 
echo "</table>";
echo "</form>";
echo "</HTML>";   

?>


