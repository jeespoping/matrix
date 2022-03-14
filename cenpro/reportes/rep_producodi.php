<html>
<head>
<title>Reporte De Productos Codificados</title>
 <!--<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" /> -->
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
		document.forms.rep_producodi.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php

$wemp_pmla= $_REQUEST['wemp_pmla'];

include_once("conex.php");
include_once("root/comun.php");
/*******************************************************************************************************************************************
*                                             REPORTE DE PRODUCTOS CODIFICADOS	  	                                                       *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los productos codificados de la central                                                    |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : SEPTIEMBRE 13 DE 2007.                                                                                      |
//FECHA ULTIMA ACTUALIZACION  : SEPTIEMBRE 13 DE 2007.                                                                                      |
//DESCRIPCION			      : Este reporte sirve para ver los productos codificados                                                       |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000051       : Tabla de Aplicaciones por Empresa.                                                                                    |
//".$empre1."_000001     : Tabla de Maestro de Tipos de Articulos.                                                                          |
//".$empre1."_000002     : Tabla de Maestro de Articulos de la Central.                                                                     |
//".$empre1."_000005     : Tabla de Kardex de Inventario.                                                                                   |
//".$empre1."_000007     : Tabla de Detalle de Movientos de Inventarios.                                                                    |
//".$empre1."_000008     : Tabla de Maestro de Articulos.                                                                                   |
//==========================================================================================================================================
//$wactualiz="Ver. 2007-09-17";
$wactualiz = '2022-02-16';
session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
	


$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("REPORTE DE PRODUCTOS CODIFICADOS ",$wactualiz, $wbasedato1);

 
 //echo '<div id="header">';
 //echo '<div id="logo">';
 //echo '<h1><a href="rep_producodi.php">REPORTE DE PRODUCTOS CODIFICADOS</a></h1>';
 //echo '<h2>CLINICA LAS AMERICAS <b>' . $wactualiz . '</h2>';
 //echo '</div>';
 //echo '</div></br></br></br></br></br>';
 
/////////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos

//$query = " SELECT Detapl,Detval"
//        ."   FROM ".$empresa."_000051"
//	    ."  WHERE Detemp='".$wemp_pmla."'";


//$err = mysql_query($query,$conex);
//$num = mysql_num_rows($err);
   
//$empre1="";
//$empre2="";

//for ($i=1;$i<=$num;$i++)
 //{ 
//  $row = mysql_fetch_array($err);
     
//  IF ($row[0] == 'cenmez')
//   {
//    $empre1=$row[1];
//   }	
//  else 
//   { 
//    if ($row[0] == 'movhos') 
//     {
//      $empre2=$row[1];	
 //    }
 //  }     
 //}
$empre1= consultarAliasPorAplicacion($conex, $wemp_pmla,"cenmez");
$empre2= consultarAliasPorAplicacion($conex,$wemp_pmla,"movhos");

echo "<center><table border=0 width=300>";

 if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '' or !isset($horaidia) or $horaidia=='' or !isset($horafdia) or $horafdia=='' or !isset($horainoche) or $horainoche=='' or !isset($horafnoche) or $horafnoche=='')
  {
   echo "<form name='rep_producodi' action='' method=post>";

   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
      
   echo '<table align=center cellspacing="10" >';
   
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Inicial &nbsp<i><br></font></td>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Final &nbsp<i><br></font></b></td>";
   echo "</Tr >";
   
$ayer=date("Y-m-d",time()-86400); /* time() da el timestamp actual, un día tiene 86400 segundos, tan sólo tenemos que restárselo al timestamp de hoy dependiendo de que dia necesitemos */ 
$dia=date("Y-m-d",time());
  if (!isset($fec1))
        $fec1=$ayer;
   	$cal="calendario('fec1','1')";
   	echo "<tr>";
	//echo "<td bgcolor='#dddddd' align=center><input type='date' name='fec1' size=10 maxlength=10  id='fec1' readonly='readonly' value=".$fec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
  echo "<td bgcolor='#dddddd' align=center><input type='date' max=".$dia." name='fec1' size=10 maxlength=10  id='fec1'  value=".$fec1." class=tipo3></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
   		

   if (!isset($fec2))
       $fec2=$ayer;
   	  $cal="calendario('fec2','1')";
	  //echo "<td bgcolor='#dddddd' align=center><input type='date' name='fec2' size=10 maxlength=10  id='fec2' readonly='readonly' value=".$fec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	  echo "<td bgcolor='#dddddd' align=center><input type='date' max=".$dia." name='fec2' size=10 maxlength=10  id='fec2'  value=".$fec2." class=tipo3></td>";
    ?>
	    <script type="text/javascript">//<![CDATA[
	       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	    //]]></script>
	  <?php	  
	  echo "</tr>";
  

   if (isset($horaidia))
    {
     $horaidia=$horaidia;
    }
   else
    {
   	 $horaidia='07:00:00';
    }

   if (isset($horafdia))
    {
   	 $horafdia=$horafdia;
    }
   else
    {
     $horafdia='18:59:59';
    }
   
   if (isset($horainoche))
    {
     $horainoche=$horainoche;
    }
   else
    {
   	 $horainoche='19:00:00';
    }

   if (isset($horafnoche))
    {
   	 $horafnoche=$horafnoche;
    }
   else
    {
     $horafnoche='06:59:59';
    }
   
   echo '<div id="page" align="center">';
   echo '<table align=center cellspacing="10" >';
   echo "<tr><td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=3><i>Turno Hora Inicial DIA &nbsp(HH:MM:SS):</font></b><INPUT TYPE='text' NAME='horaidia' id='searchinput' VALUE='".$horaidia."'>
	    <b><font text color=#003366 size=3> <i>Turno Hora Final DIA&nbsp<i>(HH:MM:SS):</font></b><INPUT TYPE='text' NAME='horafdia' id='searchinput' VALUE='".$horafdia."'></td></tr>";
   
   echo "<tr><td align=center bgcolor=#DDDDDD colspna=5><b><font text color=#003366 size=3> <i>Turno Hora Inicial NOCHE&nbsp<i>(HH:MM:SS):</font></b><INPUT TYPE='text' id='searchinput' NAME='horainoche' VALUE='".$horainoche."'>
	    <b><font text color=#003366 size=3> <i>Turno Hora Final NOCHE&nbsp<i>(HH:MM:SS):</font></b><INPUT TYPE='text' NAME='horafnoche' id='searchinput' VALUE='".$horafnoche."'></td></tr>";
   
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	
        //echo "<center><table border=1>";
	    echo "<center><table border=1>";
      echo "<tr><td align=center colspan=5 bgcolor=#FFFFFF><font text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
	    echo "<tr><td align=center colspan=5 bgcolor=#FFFFFF><font text color=#003366><b>HORA INICIAL DIA: <i>".$horaidia."</i>&nbsp&nbsp&nbspHORA FINAL DIA: <i>".$horafdia."</i></b></font></b></font></td></tr>";
	    echo "<tr><td align=center colspan=5 bgcolor=#FFFFFF><font text color=#003366><b>HORA INICIAL NOCHE: <i>".$horainoche."</i>&nbsp&nbsp&nbspHORA FINAL NOCHE: <i>".$horafnoche."</i></b></font></b></font></td></tr>";		
          
	    echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>CODIGO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>ARTICULO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>TOTAL CANTIDAD DIA</b></font></td>" .
		     "<td align=center bgcolor=#006699><font text color=#FFFFFF><b>TOTAL CANTIDAD NOCHE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>SALDO_ARTICULO</b></font></td></tr>";
	
		$hoy=date("Y-m-d");
			
		$quer1 = "CREATE TEMPORARY TABLE if not exists tempo1 as "
		         ."SELECT mdeart,artcom,sum(mdecan) as candia,0 as cannoche,karexi"
                 ."  FROM ".$empre1."_000001,".$empre1."_000002,".$empre1."_000005,".$empre1."_000007,".$empre1."_000008"
                 ." WHERE ".$empre1."_000001.tipcdo='on'"
                 ."   AND ".$empre1."_000001.tipcod=".$empre1."_000002.arttip"
                 ."   AND ".$empre1."_000002.artcod=".$empre1."_000005.karcod"
                 ."   AND ".$empre1."_000002.artcod=".$empre1."_000007.mdeart"
                 ."   AND ".$empre1."_000007.fecha_data between '".$fec1."' AND '".$fec2."'"
                 ."   AND ".$empre1."_000007.Hora_data between '".$horaidia."' AND '".$horafdia."'"
                 ."   AND ".$empre1."_000007.mdeest='on'"
                 ."   AND ".$empre1."_000007.mdecon=".$empre1."_000008.concod"
                 ."   AND ".$empre1."_000008.conind=-1"
                 ."   AND ".$empre1."_000008.concar='on'"
                 ." GROUP BY 1,2,4,5"
                 ." UNION "
                 ."SELECT mdeart,artcom,0 as candia,sum(mdecan) as cannoche,karexi"
                 ."  FROM ".$empre1."_000001,".$empre1."_000002,".$empre1."_000005,".$empre1."_000007,".$empre1."_000008"
                 ." WHERE ".$empre1."_000001.tipcdo='on'"
                 ."   AND ".$empre1."_000001.tipcod=".$empre1."_000002.arttip"
                 ."   AND ".$empre1."_000002.artcod=".$empre1."_000005.karcod"
                 ."   AND ".$empre1."_000002.artcod=".$empre1."_000007.mdeart"
                 ."   AND ".$empre1."_000007.fecha_data between '".$fec1."' AND '".$fec2."'"
                 ."   AND ".$empre1."_000007.Hora_data between '".$horainoche."' and '23:59:59'"
                 ."   AND ".$empre1."_000007.mdeest='on'"
                 ."   AND ".$empre1."_000007.mdecon=".$empre1."_000008.concod"
                 ."   AND ".$empre1."_000008.conind=-1"
                 ."   AND ".$empre1."_000008.concar='on'"
                 ." GROUP BY 1,2,3,5"
                 ." UNION "
                 ."SELECT mdeart,artcom,0 as candia,sum(mdecan) as cannoche,karexi"
                 ."  FROM ".$empre1."_000001,".$empre1."_000002,".$empre1."_000005,".$empre1."_000007,".$empre1."_000008"
                 ." WHERE ".$empre1."_000001.tipcdo='on'"
                 ."   AND ".$empre1."_000001.tipcod=".$empre1."_000002.arttip"
                 ."   AND ".$empre1."_000002.artcod=".$empre1."_000005.karcod"
                 ."   AND ".$empre1."_000002.artcod=".$empre1."_000007.mdeart"
                 ."   AND ".$empre1."_000007.fecha_data between '".$hoy."' AND '".$hoy."'"
                 ."   AND ".$empre1."_000007.Hora_data between '00:00:01' and '".$horafnoche."'"
                 ."   AND ".$empre1."_000007.mdeest='on'"
                 ."   AND ".$empre1."_000007.mdecon=".$empre1."_000008.concod"
                 ."   AND ".$empre1."_000008.conind=-1"
                 ."   AND ".$empre1."_000008.concar='on'"
                 ." GROUP BY 1,2,3,5"
                 ." ORDER BY 1,2";
                 //print_r ($quer1);
        //echo $quer1."<br>";
        $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
	 			
        $query1 = "SELECT mdeart,artcom,sum(candia),sum(cannoche),karexi"
                 ."  FROM tempo1"
                 ." GROUP BY 1,2,5"
                 ." ORDER BY 1,2";
	 
        //echo $query1."<br>";
		$err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	    //echo mysql_errno() ."=". mysql_error();
			
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
	   
	  echo "<tr  bgcolor=".$wcf."><td align=center>".$row1[0]."</td><td align=center>".$row1[1]."</td><td align=center>".$row1[2]."</td><td align=center>".$row1[3]."</td><td align=center>".$row1[4]."</td></tr>"; 
	  
	}
		
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
echo "<table border=0 align=center cellpadding='2' cellspacing='0' size=100%>"; 
echo "<tr><td align=center colspan=18><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
echo "</table>";
}
?>