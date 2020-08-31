<html>
<head>
<title>MATRIX - [REPORTE PROCESOS PRIORITARIOS x EVALUADOR]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_proceprioxevacn.php'; 
	}
	
	function enter()

	{
		document.forms.rep_proceprioxevacn.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PROCESOS PRIORITARIOS  X EVALUADOR                                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver en los procesos prioritarios X evaluador                                                   |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : MAYO 25 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : MAYO 25 DE 2010.                                                                                            |
//DESCRIPCION			      : Este reporte sirve para observar x evaluador a quienes a evaluado.                                          |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//uce_000001     : Tabla Procesos Priritarios.                                                                                              |                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 25-Mayo-2010";

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
encabezado("Procesos Prioritarios x Evaluador",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='cardio';
 //Conexion base de datos
 

 


 //Forma
 echo "<form name='forma' action='rep_proceprioxevacn.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_proceprioxevacn' action='' method=post>";
  
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
	
    echo "<center><table border=1 cellspacing=0 cellpadding=0>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366 size=2><b>CARDIOLOGIA</b></font></td></tr>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366 size=2><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
    echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF size=2><b>PERSONA EVALUADA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL EVALUADOS</b></font></td></tr>";
    	
    $query1 = "SELECT ppperso,ppperseva,count(*) as cant"
            ."   FROM ".$empre1."_000005 "
            ."  WHERE ppfecha between '".$fec1."' and '".$fec2."'"
            ."  GROUP by 1,2"
            ."  ORDER by 1,2"; 
				
	//echo $query1."<br>"; 
				 
	$err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
	
	//echo mysql_errno() ."=". mysql_error();

    $proceant=0;
    $swtitulo='SI';
    $toteva=0;
    $persant='';
    $ppant1='';

    $wcfant='';
    $ppant='';
    $evaant=0;
    
    $totevaf=0;
    
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
	     $persant = $row1[0];

	     echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>PERSONA QUE REALIZO LA EVALUACION : </b></font></td><td align=center colspan=3><font text size=3><b>".$persant."</b></td></tr>"; 
	     $swtitulo='NO';
	   }
	   
	   if ($persant==$row1[0] )
	    {
	  		 
	     echo "<tr  bgcolor=".$wcf."><td align=center><font text size=2>".$row1[1]."</td><td align=center><font text size=2>".number_format($row1[2])."</td></tr>"; 
	     
	     $toteva=$toteva+$row1[2];
	     $totevaf=$totevaf+$row1[2];
	    
	    }
	   else 
	    {
	     echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL PERSONA QUE EVALUO : </b></font></td><td align=center><font text size=3><b>".number_format($toteva)."</b></td></tr>";
	     echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
         echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>PERSONA QUE REALIZO LA EVALUACION : </b></font></td><td align=center colspan=3><font text size=3><b>".$row1[0]."</b></td></tr>"; 	     
	     echo "<tr  bgcolor=".$wcf."><td align=center><font text size=2>".$row1[1]."</td><td align=center><font text size=2>".number_format($row1[2])."</td></tr>"; 
         $toteva=0;
	     $persant=$row1[0];
	     $toteva=$toteva+$row1[2];
	     $totevaf=$totevaf+$row1[2];
	    }
	 }

	 
	 
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL PERSONA QUE EVALUO : </b></font></td><td align=center><font text size=3><b>".number_format($toteva)."</b></td></tr>";
	echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>"; 
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL GENERAL EVALUADOS : </b></font></td><td align=center colspan=1><font text size=3><b>".number_format($totevaf)."</b></td></tr>";
	
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>"; 
echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
echo "</table>";
}
?>