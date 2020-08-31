<html>
<head>
<title>MATRIX - [REPORTE PENDIENTES DE AUTORIZACIONES ADMISIONES X ENTIDAD X CAUSA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_entidadxcausa.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_entidadxcausa.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PENDIENTES DE AUTORIZACIONES X ENTIDAD Y CAUSA                                       *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los pendientes de autorizaciones de admisiones X entidad y  causas                         |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : JUNIO 18 DE 2010.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : JUNIO 18 DE 2010.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para observar en general las autorizaciones pendientes x entidad x causa.                |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000008      : Tabla de Pendientes de autorizaciones de admisiones.                                                                  |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 16-Junio-2010";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Encabezado
encabezado("Total Pendientes de Autorizaciones x Entidad x Causa",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='urgen';
 //Conexion base de datos
 

 


 //Forma
 echo "<form name='forma' action='rep_entidadxcausa.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_entidadxcausa' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center' width=150>";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";

   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PENDIENTES Y NO PENDIENTES DE AUTORIZACIONES EN ADMISIONES Y SUS CAUSAS</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
      
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CAUSA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TOTAL</b></td>";
    echo "</tr>";
    
    $query = " SELECT Pacodres,panomres,Pacausas,urgen_000008.fecha_data,count(*) as cant"
            ."   FROM urgen_000008,usuarios"
            ."  WHERE urgen_000008.fecha_data between '".$fec1."' and '".$fec2."'"
            ."    AND Papendi like '1%'"
            ."    AND paresp = Codigo"
            ."  GROUP BY Pacodres,panomres,Pacausas,urgen_000008.fecha_data"
            ."  ORDER BY pacodres,pacausas,urgen_000008.fecha_data";
           
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
   
    //echo $query;
    
    //echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    $pcodant='';
    $totent=0;
    $totgen=0;
    
	$wcfant='';
	
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
      $pcodant = $row1[0];
      $pnomant = $row1[1];
      echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>ENTIDAD : </b></font></td><td align=center colspan=1><font color=#FF0000 text size=2>".$pcodant."</td><td align=center colspan=1><font color=#FF0000 text size=2>".$pnomant."</td></tr>"; 
      $swtitulo='NO';
 	   
	 }
	    	  
	if ($pcodant==$row1[0] )
	 {
	  echo "<Tr  bgcolor=".$wcf.">";
      echo "<td  align=center><font size=1>$row1[2]</font></td>";
      echo "<td  align=center><font size=1>$row1[3]</font></td>";
      echo "<td  align=center><font size=1>$row1[4]</font></td>";
      
      $totent=$totent+$row1[4];
      $totgen=$totgen+$row1[4];
      
     }
	else 
	 {
	   echo "<tr>";
	   echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL ENTIDAD : </b></font></td>";
	   echo "<td align=center><font color=#FF0000 text size=2>".$pcodant."</td>";
	   echo "<td align=center><font color=#FF0000 text size=2>".$totent."</td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "</tr>";
	  
	  $totent=0; 
	  $pcodant=$row1[0];
	  $pnomant=$row1[1];
      $wcfant=$wcf;
	  
	  echo "<tr>";
	  echo "</tr>";
	  echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>ENTIDAD : </b></font></td><td align=center colspan=1><font color=#FF0000 text size=2>".$pcodant."</td><td align=center colspan=1><font color=#FF0000 text size=2>".$pnomant."</td></tr>"; 
	  
	  echo "<Tr bgcolor=".$wcfant.">";
      
      echo "<td align=center><font size=1>$row1[2]</font></td>";
      echo "<td align=center><font size=1>$row1[3]</font></td>";
      echo "<td align=center><font size=1>$row1[4]</font></td>";
      
      $totent=$totent+$row1[4];
      $totgen=$totgen+$row1[4];
      
	 }
	}

	/*
	echo "<Tr bgcolor=".$wcfant.">";
    echo "<td align=center><font size=1>$row1[2]</font></td>";
    echo "<td align=center><font size=1>$row1[3]</font></td>";
    echo "<td align=center><font size=1>$row1[4]</font></td>";
    echo "<tr>";
	echo "</tr>";
	
	$totent=$totent+$row1[4];
    $totgen=$totgen+$row1[4];
	*/
	
	echo "<tr>";
	echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL ENTIDAD : </b></font></td>";
	echo "<td align=center><font color=#FF0000 text size=2>".$pcodant."</td>";
	echo "<td align=center colspan=4><font color=#FF0000 text size=2>".$totent."</td>";
	echo "</tr>";
	
	echo "<tr>";
	echo "<td align=center colspan=2 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL GENERAL : </b></font></td>";
	echo "<td align=center colspan=1><font color=#FF0000 text size=2>".$totgen."</td>";
	echo "</tr>";
	
	
	echo "</table>"; 

	
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>