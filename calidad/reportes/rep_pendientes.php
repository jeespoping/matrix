<html>
<head>
<title>MATRIX - [REPORTE PENDIENTES]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_pendientes.php'; 
	}
	
	function enter()
	{
		document.forms.rep_pendientes.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PENDIENTES AUTORIZACIONES EN ADMISIONES                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver las autorizaciones pendientes en admisiones x fecha.                                        |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JUNIO 16 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :16 de Junio de 2010.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000008      : Tabla de Autorizaciones pendientes de admisiones.                                                                     |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 15-Junio-2010";

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
encabezado("Pendientes de Autorizaciones en Admisiones",$wactualiz,"clinica");

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
 echo "<form name='rep_pendientes' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_pendientes' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PENDIENTES DE AUTORIZACIONES EN ADMISIONES</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   $query = " SELECT Pahist,Paingr,Paresp,Descripcion,urgen_000008.fecha_data,Pacausas"
           ."   FROM urgen_000008,usuarios"
           ."  WHERE urgen_000008.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Papendi like '1%'"
           ."    AND paresp = Codigo"
           ."  ORDER BY pahist,paingr,paresp,descripcion";
           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query."<br>";
      
      echo "<br>";
   
      echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='300'>";
      
      echo "<tr> ";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HISTORIA</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INGRESO</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>COD_RESPONSABLE</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE_RESPONSABLE</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA_GRABACION</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CAUSA</b></td>";
      echo "</tr>";
   
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);

      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[0]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[1]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[2]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[3]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[4]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[5]</font></td>";
      echo "</tr >";
  
     } // cierre del for

   echo "</table>";   

   echo "<br>";
   
   echo "<table border=0 >";
   echo "<Tr >";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='3' color='#000000'><b>TOTAL DE PENDIENTES: </b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=left width=65%><font size=3 color=#FF0000>&nbsp;<b>".number_format($num1)."</b></font></td>";
   echo "</tr >"; 
   echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>