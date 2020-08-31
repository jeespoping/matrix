<html>
<head>
  	<title>MATRIX Procesos en la Base De Datos</title>
</head>

<BODY TEXT="#000066" BGCOLOR="FFFFFF">
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : clean1.php
	   Fecha de Liberación : 2007-05-04
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2007-06-12
	   
	   OBJETIVO GENERAL : 
	   Este programa permite deterninar que procesos estan corriendo en la base de datos.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2011-06-13
			Se modifica el script para agregarle or die functions a todos los precesos de  la base de datos.
			Adicionalmente se coloca la clausula set_time_limit para control del tiempo de ejecucion.
			
	   .2007-06-12
	   		Se modifico el programa para crear un archivo plano con el log de los procesos que entran a la base de datos.
	   		Adicionalmente se configuro para que el programa se refresque cada 5 segundos.
	   			
	   .2006-04-07
	   		Inicio del Programa a Produccion.

	   
***********************************************************************************************************************/
$name = date("Y_m_d_H");
$datafile="/var/www/matrix/respaldo/log_".$name.".txt"; 
//$datafile="./log_".$name.".txt"; 
$file = fopen($datafile,"a+");
echo "<form name='clean1' action='clean1.php' method=post>";
echo "<meta http-equiv='refresh' content='5;url=/matrix/root/procesos/clean1.php'>";
$registro = date("Y-m-d H:i:s ");
$registro .= "**** ERROR TIEMPO MAXIMO DE PROCESO SUPERADO ****".chr(13).chr(10);
//set_time_limit ( 15 ) or die(fwrite ($file,$registro));
$registro = date("Y-m-d H:i:s ");
$registro .= "**** NO SE REALIZO CONEXION SERVICIO ABAJO ****".chr(13).chr(10);
$conex = mysql_pconnect('localhost','root','q6@nt6m')
						or die(fwrite ($file,$registro));
mysql_select_db("matrix") or die("ERROR NO PUEDE CONECTARSE A MATRIX");
if(isset($ok1))
{
	for ($i=0;$i<$num;$i++)
	{
		if(isset($del[$i]))
		{
			$query = "kill ".$id[$i];
			$err1 = mysql_query($query,$conex) or die("ERROR CANCELANDO PROCESO");
		}
	}
	unset($ok1);
}
$query = "show full processlist ";
$err = mysql_query($query,$conex) or die("ERROR EJECUTANDO show full process list");
$num = mysql_num_rows($err);
$wcolor="#cccccc";
echo "<table border=0 align=center>";
	echo "<tr><td align=right colspan=2><font size=2>Powered by :  MATRIX</font></td></tr>";
echo "<tr><td align=center bgcolor=#000066 colspan=1><font color=#ffffff size=6><b>PROCESOS EN LA BASE DE DATOS Ver. 2011-06-13</font><font color=#33CCFF size=4>&nbsp;&nbsp;&nbsp;Ver. 2007-06-12</font></b></font></td></tr>";
echo "<tr><td  align=center bgcolor=".$wcolor."> Lista de Procesos Corriendo en la Base de Datos </td></tr>";
echo "</table><br><br>";  
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor=#999999 colspan=7><font color=#000066 size=3><b>DETALLE DE PROCESOS</b></font></td></tr>";
echo "<tr><td align=center bgcolor=#000066><font color=#ffffff >KILL</font></td><td align=center bgcolor=#000066><font color=#ffffff >ID</font></td><td align=center bgcolor=#000066><font color=#ffffff >BASE DE<BR> DATOS</font></td><td align=center bgcolor=#000066><font color=#ffffff >COMANDO</font></td><td align=center bgcolor=#000066><font color=#ffffff >TIEMPO<br> Segundos</font></td><td align=center bgcolor=#000066><font color=#ffffff >ESTADO</font></td><td align=center bgcolor=#000066><font color=#ffffff >INFORMACION</font></td></tr>";				
for ($i=0;$i<$num;$i++)
{
	$row = mysql_fetch_array($err);
	if($i % 2 == 0)
		$color="#dddddd";
	else
		$color="#cccccc";
	echo "<tr>";
	echo "<td   align=center bgcolor=".$color."><input type='checkbox' name='del[".$i."]'></td>";
	echo "<td   align=center bgcolor=".$color.">".$row[0]."</td>";
	echo "<td   align=center bgcolor=".$color.">".$row[3]."</td>";
	echo "<td   align=center bgcolor=".$color.">".$row[4]."</td>";
	echo "<td   align=center bgcolor=".$color.">".$row[5]."</td>";
	echo "<td bgcolor=".$color.">".$row[6]."</td>";
	echo "<td bgcolor=".$color.">".$row[7]."</td>";
	$registro = date("Y-m-d H:i:s ");
	for ($j=0;$j<8;$j++)
		$registro .= $row[$j]." ";	
	$registro .= chr(13).chr(10);
	fwrite ($file,$registro);
	echo "</tr>";
	echo "<input type='HIDDEN' name= 'num' value=".$num.">";
	echo "<input type='HIDDEN' name= 'id[".$i."]' value='".$row[0]."'>";
}
fclose ($file);
echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><font color=#000066 size=3><b>TOTAL PROCESOS : ".$num."</b></font></td></tr>";
echo "<tr><td align=center bgcolor=#CCCCCC colspan=7><b>CANCELAR</b><input type='checkbox' name='ok1'></td></tr>";
echo "<tr><td align=center bgcolor=#999999 colspan=7><input type='submit' value='Ok'></td></tr>";
echo "</table><br><br>"; 
?>
