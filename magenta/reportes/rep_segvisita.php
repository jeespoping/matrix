<html>
<head>
<title>Reporte de seguimiento de visitas x documento</title>
<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" />
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_segvisita.submit();
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PARA HACER SEGUIMIENTO A LAS VISITAS	                                               *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los seguimientos x paciente.                                                               |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : FEBRERO 25 DE 2008.                                                                                         |
//FECHA ULTIMA ACTUALIZACION  : FEBRERO 25 DE 2008.                                                                                         |
//DESCRIPCION			      : Este reporte sirve para ver por documento cuantas veces lo han visitado                                     |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//magenta_000014    : Tabla de visitas.                                                                                                     |
//==========================================================================================================================================
$wactualiz="Ver. 2008-02-22";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
	
 $empresa='magenta';

 

 

 
 echo '<div id="header">';
 echo '<div id="logo">';
 echo '<h1><a href="rep_segvisita.php">REPORTE SEGUIMIENTO A VISITAS x DOCUMENTO </a></h1>';
 echo '<h2>CLINICA LAS AMERICAS <b>' . $wactualiz . '</h2>';
 echo '</div>';
 echo '</div></br></br></br></br></br>';

$docu=explode('-',$docume);
 
$query = " SELECT repdoc,rephis,repnom,reping,repser,hora_data,repfvi"
	    ."   FROM ".$empresa."_000014"
	    ."  WHERE repdoc='".$docu[0]."'"
	    ."    AND repvis='on'"
	    ."  ORDER BY repfvi,repdoc";
	 
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
   
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
echo "<table border=0 align=center size=100%>";
echo "<tr><td align=center colspan=18 bgcolor=#FFFFFF><font text color=#003366 size=2><b>DOCUMENTO: <i>".$docume."</i></td></tr>";
		
echo "<Tr >";
echo "<td bgcolor='#006699'align=center width=10%><font size=2 text color=#FFFFFF>DOCUMENTO</font></td>";
echo "<td bgcolor='#006699'align=center width=10%><font size=2 text color=#FFFFFF>HISTORIA</font></td>";
echo "<td bgcolor='#006699'align=center width=40%><font size=2 text color=#FFFFFF>NOMBRE</font></td>";
echo "<td bgcolor='#006699'align=center width=10%><font size=2 text color=#FFFFFF>FECHA_INGRESO</font></td>";
echo "<td bgcolor='#006699'align=center width=10%><font size=2 text color=#FFFFFF>SERVICIO</font></td>";
echo "<td bgcolor='#006699'align=center width=10%><font size=2 text color=#FFFFFF>HORA_VISITA</font></td>";
echo "<td bgcolor='#006699'align=center width=10%><font size=2 text color=#FFFFFF>FECHA_VISITA</font></td>";
echo "</Tr >";
			 

for ($i=1;$i<=$num;$i++)
 {
  if (is_int ($i/2))
   {
   	$wcf="DDDDDD";  // color de fondo
   }
  else
   {
   	$wcf="CCFFFF"; // color de fondo
   }

  $row = mysql_fetch_array($err);
		
  echo "<Tr >";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[0]</font></td>";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[1]</font></td>";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[2]</font></td>";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[3]</font></td>";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[4]</font></td>";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[5]</font></td>";
  echo "<td  bgcolor='$wcf' align=center><font size=1>$row[6]</font></td>";
  echo "<Tr >";     
	       
}
		
echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
} // cierre del else donde empieza la impresión

?>