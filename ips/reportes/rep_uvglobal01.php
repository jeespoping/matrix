<html>
<head>
<title>Reporte de las Ordenes Entregadas</title>
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
	function cerrarVentana()
	{
	 window.close()
	}
</script>
	

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_uvglobal01.submit();
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE POR RANGO DE FECHA DE ENTREGA DE ORDENES                                             *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver por rango de fecha de entrega las ordenes de los pacientes.                                |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : SEPTIEMBRE 29 DE 2008.                                                                                      |
//FECHA ULTIMA ACTUALIZACION  : SEPTIEMBRE 29 DE 2008.                                                                                      |
//DESCRIPCION			      : Este reporte sirve para verificar las ordenes entregadas                                                    |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//uvglobal_000133   : Tabla de ordenes.                                                                                                     |
//uvglobal_000041   : Tabla de Pacientes.                                                                                                   |
//==========================================================================================================================================
$wactualiz="Ver. 2008-09-29";

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
 echo '<h1><a href="rep_diaincons.php">REPORTE DE ORDENES ENTREGADAS POR FECHA</a></h1>';
 echo '<h2>UNIDAD VISUAL <b>' . $wactualiz . '</h2>';
 echo '</div>';
 echo '</div></br></br></br></br></br>';
 
 
/////////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos

$query = " SELECT empbda"
	    ."   FROM ".$empresa."_000050"
	    ."  WHERE empcod='".$wemp."'";
	 
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
   
$empre1="";

for ($i=1;$i<=$num;$i++)
 { 
  $row = mysql_fetch_array($err);
     
  IF ($row[0] == 'UVGLOBAL')
   {
    $empre1='uvglobal';
   }	
 }

 if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '' or !isset($cco) or $cco=='-' or $cco=='' )
  {
  echo "<form name='rep_uvglobal01' action='' method=post>";
  echo '<table align=center cellspacing="10" >';
  
  ///////////////////////////////////////////////////////////////////////////////////////// seleccion para el centro de costos o sede
  echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366><B>Sede:</B></br></font></b><select name='cco' id='searchinput'>";  
    
  $query1="SELECT Ccocod, Ccodes "
         ."  FROM uvglobal_000003 "
         ." ORDER BY Ccocod,ccodes";
  
  $err1 = mysql_query($query1,$conex);
  $num1 = mysql_num_rows($err1);
  $Ccostos=explode('-',$cco);
   
  for ($i=1;$i<=$num1;$i++)
   {
	$row1 = mysql_fetch_array($err1);
	echo "<option>".$row1[0]."-".$row1[1]."</option>";
   }
  echo "<option>TODOS</option>";
  echo "</select></td></tr>";
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
   
  echo "<Tr >";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Inicial de Entrega&nbsp<i><br></font></td>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Final de Entrega&nbsp<i><br></font></b></td>";
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
	
        echo "<center><table border=1>";
        echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><font text color=#003366><b>FECHA INICIAL DE ENTREGA: <i>".$fec1."</i></b></font></b></font></td></tr>";
	    echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><font text color=#003366><b>FECHA FINAL DE ENTREGA: <i>".$fec2."</i></b></font></b></font></td></tr>";
        echo "<tr><td align=center colspan=7 bgcolor=#FFFFFF><font text color=#003366><b>SEDE: <i>".$cco."</i></b></font></b></font></td></tr>";
	    echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FECHA_ENTREGA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>NRO_ORDEN</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>DOCUMENTO_PACIENTE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOMBRES_PACIENTE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FTE_FACT</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FACTURA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>FECHA_FACTURA</b></font></td></tr>";
			
	if ($cco=='TODOS')
	{
		
	$query1 = " SELECT ordfen,ordnro,orddoc,".$empre1."_000041.clinom,ordcco,ordffa,ordfac,fenfec "
              ."  FROM ".$empre1."_000018,".$empre1."_000133 LEFT JOIN ".$empre1."_000041"
              ."    ON ".$empre1."_000133.orddoc = ".$empre1."_000041.clidoc"
              ." WHERE ordfen between '".$fec1."' and '".$fec2."'"
              ."   AND ordffa = fenffa"
              ."   AND ordfac = fenfac" 
              ." GROUP BY 1,2,3,4,5,6,7,8"
              ." ORDER BY ordcco,ordfen,ordnro";
		
	}
	else
	{
	$query1 = " SELECT ordfen,ordnro,orddoc,".$empre1."_000041.clinom,ordcco,ordffa,ordfac,fenfec "
              ."  FROM ".$empre1."_000018,".$empre1."_000133 LEFT JOIN ".$empre1."_000041"
              ."    ON ".$empre1."_000133.orddoc = ".$empre1."_000041.clidoc"          
	          ." WHERE ordfen between '".$fec1."' and '".$fec2."'" 
              ."   AND ordcco = '".$cco."'"
              ."   AND ordffa = fenffa"
              ."   AND ordfac = fenfac"          
              ." GROUP BY 1,2,3,4,5,6,7,8"
              ." ORDER BY ordcco,ordfen,ordnro";

	}
	              
	//	echo $query1."<br>"; 
				 
	$err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
	
	//echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    $sedeant='';
    $ordant=0;
    $ordfena='';
    $ordnroa=0;
    $orddoca=0;
    $nombrea='';
    $ffaca='';
	$facta=0;
	$fecfa='';
    
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

	  if ($swtitulo=='SI')
	  {
       $sedeant = $row1[4];
	   echo "<tr><td align=center colspan=2 bgcolor=#006699><font text color=#FFFFFF><b>SEDE : </b></font></td><td align=center colspan=5 bgcolor=#006699><font text color=#FFFFFF><b>".$sedeant."</b></font></td></tr>"; 
	   $swtitulo='NO';
 	   
	   if ($ordnroa<>0)
	    {
	     echo "<tr  bgcolor=".$wcfant."><td align=center>".$ordfena."</td><td align=center>".$ordnroa."</td><td align=center>".$orddoca."</td><td align=center>".$nombrea."</td><td align=center>".$ffaca."</td><td align=center>".$facta."</td><td align=center>".$fecfa."</td></tr>";
	     $ordnroa=0;
	    }
	   
	   } 
	  
	  if ($sedeant==$row1[4] )
	  {
	   echo "<tr  bgcolor=".$wcf."><td align=center>".$row1[0]."</td><td align=center>".$row1[1]."</td><td align=center>".$row1[2]."</td><td align=center>".$row1[3]."</td><td align=center>".$row1[5]."</td><td align=center>".$row1[6]."</td><td align=center>".$row1[7]."</td></tr>"; 
	  }
	  else
	  {
	   echo "<tr><td alinn=center colspan=8 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	   $swtitulo='SI';
	   $wcfant=$wcf;
	   $ordfena=$row1[0];
	   $ordnroa=$row1[1];
	   $orddoca=$row1[2];
	   $nombrea=$row1[3];
	   $ffaca=$row1[5];
	   $facta=$row1[6];
	   $fecfa=$row1[7];
	   
	  }	
	}
	
  echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
  
}
echo "<tr><td align=center colspan=8><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
?>