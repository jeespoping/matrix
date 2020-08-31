<html>
<head>
<title>MATRIX - [REPORTE HALLAZGOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_hallazgo.php'; 
	}
	
	function enter()
	{
		document.forms.rep_hallazgo.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE HALLAZGOS GENERALES NEUMOLOGIA                                                       *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los hallazgo en neumologia.                                                                 |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JULIO 01 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :01 de Julio de 2010.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//neumo_000003      : Tabla de Autorizaciones hallazgo de admisiones.                                                                       |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 01-Julio-2010";

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
encabezado("Hallazgos Generales",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 
 //Forma
 echo "<form name='rep_hallazgo' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_hallazgo' action='' method=post>";
  
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
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";

    echo "<br>";
    echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
  }
 else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>HALLAZGOS GENERALES</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	

  $query1 =" SELECT sicediag,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicediag='on'"
           ."  GROUP BY sicediag";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query1."<br>";
      
   $query2 =" SELECT sicetera,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicetera='on'"
           ."  GROUP BY sicetera";
           
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   
   //echo $query1."<br>";
   
   $query3 =" SELECT sicedx,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicedx='on'"
           ."  GROUP BY sicedx";
           
   $err3 = mysql_query($query3,$conex);
   $num3 = mysql_num_rows($err3);
   
   //echo $query1."<br>";
   $query4 =" SELECT sicecontrol,count(*) as cant"
           ."   FROM neumo_000003"
           ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
           ."    AND sicecontrol='on'"
           ."  GROUP BY sicecontrol";
           
   $err4 = mysql_query($query4,$conex);
   $num4 = mysql_num_rows($err4);
   
   
   //echo $query."<br>";
      
   echo "<br>";
   
   echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='100'>";
   echo "<tr> ";
   echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>HALLAZGOS</b></td>";
   echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>TOTAL</b></td>";
   echo "</tr>";

   IF ($num1<>0)
   {
    $row1 = mysql_fetch_array($err1);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>DIAGNOSTICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>DIAGNOSTICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >";
   }
   
   IF ($num2<>0)
   {
    $row1 = mysql_fetch_array($err2);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>TERAPEUTICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";
   }
   ELSE
   {
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>TERAPEUTICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >";
   }
   
   IF ($num3<>0)
   {
    $row1 = mysql_fetch_array($err3);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>DX + TERAPEUTICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>DX + TERAPEUTICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }
   
   IF ($num4<>0)
   {
    $row1 = mysql_fetch_array($err4);
    echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>CONTROL MEDICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>$row1[1]</font></td>";
    echo "</tr >";   
   }
   ELSE
   {
   	echo "<Tr >";
    echo "<td bgcolor=#FFFFFF align=center><font text color=#000000 size=1><b>CONTROL MEDICO</b></td>";
    echo "<td bgcolor=#FFFFFF align=center><font size=1>0</font></td>";
    echo "</tr >"; 
   }

   echo "</table>";   

   echo "<br>";
   
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>