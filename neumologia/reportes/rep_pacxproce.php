<html>
<head>
<title>MATRIX - [REPORTE PACIENTES X PROCEDIMIENTO]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_pacxproce.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_pacxproce.submit();
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
*                                             REPORTE PACIENTES X PROCEDIMIENTOS                                                           *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los pacientes x procedimiento                                                              |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : JULIO 2 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : JULIO 2 DE 2010.                                                                                            |
//DESCRIPCION			      : Este reporte es para ver los pacientes x procedimiento.                                                     |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//neumo_000003      : Tabla de Registro de Atención.                                                                                        |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 02-Julio-2010";

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
encabezado("Pacientes x Procedimiento",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 //Conexion base de datos
 

 


 //Forma
 echo "<form name='forma' action='rep_pacxproce.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_pacxproce' action='' method=post>";
  
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
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PACIENTES x PROCEDIMIENTO</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
      
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>CENTRO DE COSTOS</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>INGRESO</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PACIENTE</b></td>";
    echo "</tr>";
    
    $query = " SELECT siceproce,sicecco,siceing,siceape1,siceap2,sicenom"
            ."   FROM neumo_000003"
            ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
            ."  ORDER BY siceproce,sicecco,siceing";
           
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
   
    //echo $query;
    //echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    
    $entiant='';
    
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
	   
   	IF ($swtitulo=='SI')
	 {
      $entiant = $row1[0];
      echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>PROCEDIMIENTO : </b></font></td><td align=center colspan=2><font color=#000000 text size=2><b>".$entiant."</b></td></tr>"; 
      $swtitulo='NO';
 	   
	 }
	    	  
	IF ($entiant==$row1[0] )
	 {
	 	
	  $paci=$row1[3]." ".$row1[4]." ".$row1[5];
	  	
	  echo "<Tr bgcolor=".$wcf.">";
	  echo "<td align=center><font size=1>$row1[1]</font></td>";
      echo "<td align=center><font size=1>$row1[2]</font></td>";
      echo "<td align=center><font size=1>$paci</font></td>";

	  $totent=$totent+1;
	  $totgen=$totgen+1;
	 }
	ELSE 
	 {
	  echo "<tr>";
	  echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL PROCEDIMIENTO : </b></font></td>";
	  echo "<td align=center><font color=#000000 text size=2><b>".$entiant."</b></td>";
	  echo "<td align=center colspan=6><font color=#000000 text size=2><b>".$totent."</b></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "</tr>";
	  
	  $wcfant =$wcf;
	  $entiant=$row1[0];
	
	  $totent=0;
	  $totent =$totent+1;
	  $totgen=$totgen+1;
	  
	  $paci=$row1[3]." ".$row1[4]." ".$row1[5];
	  
	  echo "<tr>";
	  echo "</tr>";
	  echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>PROCEDIMIENTO : </b></font></td><td align=center colspan=2><font color=#000000 text size=2><b>".$entiant."</b></td></tr>"; 
      
	  echo "<Tr bgcolor=".$wcfant.">";
	  echo "<td align=center><font size=1>$row1[1]</font></td>";
      echo "<td align=center><font size=1>$row1[2]</font></td>";
      echo "<td align=center><font size=1>$paci</font></td>";
	  
	 }
	}

	echo "<tr>";
	echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL PROCEDIMIENTO : </b></font></td>";
	echo "<td align=center><font color=#000000 text size=2><b>".$entiant."</b></td>";
	echo "<td align=center colspan=1><font color=#000000 text size=2><b>".$totent."</b></td>";
	echo "</tr>";
	
    echo "<tr>";
    echo "</tr>";
	
    echo "<tr>";
	echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL GENERAL : </b></font></td>";
	echo "<td align=center colspan=2><font color=#000000 text size=2><b>".$totgen."</b></td>";
	echo "</tr>";
    
    echo "</table>";
	
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>