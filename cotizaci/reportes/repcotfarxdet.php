
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

echo "<center><table border=1>";
echo "<tr><td align=center bgcolor=#EBF5FB colspan=><b><font text color=#003366 size=4> <i>HISTORICO DE COTIZACIONES</font></b><br>";
echo "<tr><td align=center bgcolor=#EBF5FB colspan=1><b><font text color=#003366 size=2> <i>PROGRAMA: repcotfarxdet.php <br>AUTOR: AngelaO</font></b><br>";
echo "</table>";
echo "<br>";

/*$wmodcod = "C1CA04";
$wmodano = "2016";
$wmodusr = "1-9900568";
$wmoddes
$wmodpro
*/

$query = "   Select Fecha_data,Hora_data,modcod,modusr,modant,modact "
            ."    from cotizaci_000011"
			."    WHERE modusr = '1-".$wmodusr."'"
			."    and modano = '".$wmodano."'"
			."    and modcod = '".$wmodcod."'";	
  
  
  
 $resultado = mysql_query($query);
 $numregistro = mysql_num_rows($resultado);

echo "<center><table border=1>"; 
echo "<td align=center bgcolor=#EBF5FB size=3>" .$wmodcod."&nbsp-&nbsp" .$wmoddes."</font><b></td>";
echo "<td align=center bgcolor=#EBF5FB size=3>" .$wmodusr."&nbsp-&nbsp" .$wmodpro."</font><b></td>";
echo "</table>"; 

echo "<br>";
echo "<table border=1>";
echo "<tr>";

echo "<td colspan=1 align=center bgcolor=#EBF5FB><b>FECHA DE COTIZACION<b></td>";
echo "<td colspan=1 align=center bgcolor=#EBF5FB><b>VALOR ANTERIOR<b></td>";
echo "<td colspan=1 align=center bgcolor=#EBF5FB><b>VALOR ACTUAL<b></td>";

echo "</tr>";         

for ($i=0; $i<$numregistro; $i++)
{
	$row = mysql_fetch_array($resultado);

				echo "<tr>";
						echo "<td align=center color=#FFFFFF bgcolor=#F6DDCC>".$row[0]."-".$row[1]."</td>";
						//$row1= explode ("-", $row[2]);
						echo "<td align=center color=#FFFFFF bgcolor=#F6DDCC>".$row[4]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=#F6DDCC>".$row[5]."</td>";
						
				echo "</tr>";
}
echo "</table>";
echo "</HTML>";	
echo "</BODY>";


mysql_close($conex);
?>
