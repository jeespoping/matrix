<html>
<head>
<title>Reporte Puntos x Medico Tratante</title>
<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" />
 <!-- UTF-8 is the recommended encoding for your pages -->
  <!--   <meta http-equiv="content-type" content="text/xml; charset=utf-8" />  -->
    <title>Zapatec DHTML Calendar</title>

  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    
  <!-- Loading Calendar JavaScript files -->
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>

  <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_puntosmed.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE PUNTOS DE LOS MEDICOS TRATANTES                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los puntos de los medicos tratantes por rango de fecha.                                    |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : ENERO 31 DE 2008.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : ENERO 31 DE 2008.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para ver los puntos de los medicos tratantes, es decir los puntos que tienen los medicos |
//                              por preescribir al paciente y no como cliente.                                                              |
//TABLAS UTILIZADAS :                                                                                                                       |
//farstore_000016   : Tabla de Ventas.                                                                                                      |
//farstore_000059   : Tabla de Puntos.                                                                                                      |
//farstore_000051   : Tabla de Maetros de medicos.                                                                                          |
//                                                                                                                                          |
// Se modifica agregandole el documento y cogiendo solo las tablas 16,59 y 51, se quita la tabla 50.  2008-04-04                            |
// Se modifica tomando la información pero de la tabla de saldos tabla 60 , asi lo solicita juan carlos hernandez.  2008-04-07              |
//==========================================================================================================================================
$wactualiz="Ver. 2008-04-07";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
	
 $empresa='root';

 

 


 echo '<div id="header">';
 echo '<div id="logo">';
 echo '<h1><a href="rep_puntosmed.php">REPORTE DE PUNTOS POR MEDICO TRATANTE</a></h1>';
 echo '<h2>CLINICA LAS AMERICAS - FARMASTORE <b>' . $wactualiz . '</h2>';
 echo '</div>';
 echo '</div></br></br></br></br></br>';
 
 

    echo "<table border=0 align=center size=100%>";
	//echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><font size=5 text color=#003366><b>TIPO EMPRESA: <i>".$codemp."</b></font></b></font></td></tr>";
    
	echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>CODIGO</font></td>";
    echo "<td bgcolor='#006699'align=center width=40%><font size=3 text color=#FFFFFF>NOMBRE DEL MEDICO</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>DOCUMENTO</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PUNTOS CAUSADOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PUNTOS REDIMIDOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>PUNTOS DEVUELTOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>SALDO</font></td>";
    echo "</Tr >";
    
	$query1 =" SELECT medcod,mednom,meddoc,salcau,salred,saldev,salsal "
            ."   FROM ".$empre3."_000060,".$empre3."_000051"
            ."  WHERE saldto = meddoc"
            ."    AND saldto != ''"
            ."    AND saldto != 'NO APLICA'"
            ."  ORDER BY 1,2,3,4,5 ";
    		
	
	//echo $query1."<br>";     
          
	$err1 = mysql_query($query1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query1." - ".mysql_error());
    $num1 = mysql_num_rows($err1);
	
	//echo mysql_errno() ."=". mysql_error();
		
    $total = 0;			
			
	for ($i=1;$i<=$num1;$i++)
	 {
	  if (is_int ($i/2))
	   {
	   	$wcf="DDDDDD";  // color de fondo
	   }
	  else
	   {
	   	$wcf="CCFFFF"; // color de fondo
	   }

	   $row1 = mysql_fetch_array($err1);
	   
	   echo "<Tr >";
	   echo "<td  bgcolor='$wcf' align=left><font size=2>$row1[0]</font></td>";
	   echo "<td  bgcolor='$wcf' align=left><font size=2>$row1[1]</font></td>";
	   echo "<td  bgcolor='$wcf' align=left><font size=2>$row1[2]</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row1[3],2,'.',',')."</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row1[4],2,'.',',')."</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row1[5],2,'.',',')."</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row1[6],2,'.',',')."</font></td>";
	   echo "<Tr >";

	   $total = $total + $row1[6];
	   
	}
	echo "<Tr >";
	echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF></font></td>";
    echo "<td bgcolor='#006699'align=center width=40%><font size=3 text color=#FFFFFF>TOTAL GENERAL</font></td>";	
	echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF></font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF></font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF></font></td>";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF></font></td>";
	echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>".number_format($total,3,'.',',')."</font></td>";
    echo "</Tr >";
		
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
 // } // cierre del else donde empieza la impresión
echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>"; 
echo "<tr><td align=center colspan=18><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
echo "</table>";
}
?>
