<html>
<head>
<title>Reporte de ventas por domicilio</title>
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
		document.forms.rep_ventdomi.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}	
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE VENTAS POR DOMICILIO                                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver las ventas realizadas por sede de los domicilios.                                          |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : FEBRERO 22 DE 2008.                                                                                         |
//FECHA ULTIMA ACTUALIZACION  : FEBRERO 22 DE 2008.                                                                                         |
//DESCRIPCION			      : Este reporte sirve para ver las ventas realizadas por el domicilio de las diferentes sedes                  |
//TABLAS UTILIZADAS :                                                                                                                       |
//farstore_000016   : Tabla de Ventas.                                                                                                      |
//==========================================================================================================================================
$wactualiz="Ver. 2008-02-22";

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
 echo '<h1><a href="rep_ventdomi.php">REPORTE DE VENTAS POR DOMICILIO</a></h1>';
 echo '<h2>CLINICA LAS AMERICAS - FARMASTORE <b>' . $wactualiz . '</h2>';
 echo '</div>';
 echo '</div></br></br></br></br></br>';
 
 
/////////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos

$query = " SELECT Detapl,Detval"
	       ."   FROM ".$empresa."_000051"
	       ."  WHERE Detemp='".$wemp."'";
	 
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
   
$empre1="";
$empre2="";
$empre3="";

for ($i=1;$i<=$num;$i++)
 { 
  $row = mysql_fetch_array($err);
     
  IF ($row[0] == 'cenmez')
   {
    $empre1=$row[1];
   }	
  else 
   { 
    if ($row[0] == 'movhos') 
     {
      $empre2=$row[1];	
     }
    else 
    { 
     if ($row[0] == 'farmastore')
     {	
      $empre3=$row[1];	
     }
    }
   }     
 }

 
 if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '') 
  {
   echo "<form name='rep_ventdomi' action='' method=post>";

   echo '<table align=center cellspacing="10" >';
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
  
  echo "<Tr >";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Inicial de Venta&nbsp<i><br></font></td>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Final de Venta&nbsp<i><br></font></b></td>";
  echo "</Tr >";
   
  $hoy=date("Y-m-d");
  if (!isset($fec1))
        $fec1=$hoy;
   	$cal="calendario('fec1','1')";
   	echo "<tr>";
	echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='fec1' size=10 maxlength=10  id='fec1' readonly='readonly' value=".$fec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
   		

   if (!isset($fec2))
       $fec2=$hoy;
   	  $cal="calendario('fec2','1')";
	  echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='fec2' size=10 maxlength=10  id='fec2' readonly='readonly' value=".$fec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	  ?>
	    <script type="text/javascript">//<![CDATA[
	       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	    //]]></script>
	  <?php	  
	  echo "</tr>";

   
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	
    echo "<table border=0 align=center size=100%>";
	echo "<tr><td align=center colspan=9 bgcolor=#FFFFFF><font text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
	    
	echo "<Tr >";
    echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF>SEDE</font></td>";
    echo "<td bgcolor='#006699'align=center width=40%><font size=3 text color=#FFFFFF>NOMBRE_SEDE</font></td>";
    echo "<td bgcolor='#006699'align=center width=25%><font size=3 text color=#FFFFFF>CANTIDAD DE DOMICILIOS</font></td>";
    echo "<td bgcolor='#006699'align=center width=25%><font size=3 text color=#FFFFFF>COSTO_TOTAL</font></td>";
    echo "</Tr >";
	
	$query1 ="SELECT vencco,ccodes,count(*),sum(venvto)"
	        ."  FROM ".$empre3."_000016,".$empre3."_000003"
	        ." WHERE venfec between '".$fec1."' and '".$fec2."'"
            ."   AND venest = 'on' "
            ."   AND vencco = ccocod "
            ."   AND ventve='Domicilio' "
            ." GROUP BY 1,2"
	        ." ORDER BY 1 ";
	
	//echo $query1."<br>";     
          
	$err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
	
	//echo mysql_errno() ."=". mysql_error();
		
    $totalcan = 0;
    $totalvlr = 0;			
			
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
	   echo "<td  bgcolor='$wcf' align=center><font size=2>$row1[0]</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>$row1[1]</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>$row1[2]</font></td>";
	   echo "<td  bgcolor='$wcf' align=center><font size=2>".number_format($row1[3],0,'.',',')."</font></td>";
	   echo "<Tr >";

	   $totalcan = $totalcan + $row1[2];
	   $totalvlr = $totalvlr + $row1[3];
	   
	}
	echo "<Tr >";
	echo "<td bgcolor='#006699'align=center width=10%><font size=3 text color=#FFFFFF></font></td>";
    echo "<td bgcolor='#006699'align=center width=40%><font size=3 text color=#FFFFFF>TOTAL GENERAL</font></td>";	
	echo "<td bgcolor='#006699'align=center width=25%><font size=3 text color=#FFFFFF>".number_format($totalcan,0,'.',',')."</font></td>";
    echo "<td bgcolor='#006699'align=center width=25%><font size=3 text color=#FFFFFF>".number_format($totalvlr,0,'.',',')."</font></td>";
	echo "</Tr >";
		
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>"; 
echo "<tr><td align=center colspan=18><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
echo "</table>";
}
?>