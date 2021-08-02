<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
<title>MATRIX - [REPORTE INDICADOR ATENCION FISIATRIA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_atencionfisiatria.php'; 
	}
	
	function enter()
	{
		document.forms.rep_atencionfisiatria.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
	
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE INDICADOR ATENCION FISIATRIA                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para calcular el tiempo se demora el paciente entre el ingreso a la                                  |
//                             clinica y la atencion del medico.                                                                            |
//AUTOR				          :Ing. Gabriel Agudelo Zapata.                                                                                 |
//FECHA CREACION			  :SEPTIEMBRE 28 DE 2015.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  :SEPTIEMBRE 28 DE 2015.                                                                                       |
//TABLAS UTILIZADAS :                                                                                                                       |
//hce_000022,hce_000172,hce_000175,hce_000261,hce_000277,hce_000137      : Historia Clinica Electronica.                                    |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 28-Septiembre-2015";

$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");

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
function makeTimeFromSeconds( $total_seconds )
{

$horas              = floor ( $total_seconds / 3600 );
$minutes            = ( ( $total_seconds / 60 ) % 60 );
$seconds            = ( $total_seconds % 60 );
 
$time['horas']      = str_pad( $horas, 2, "0", STR_PAD_LEFT );
$time['minutes']    = str_pad( $minutes, 2, "0", STR_PAD_LEFT );
$time['seconds']    = str_pad( $seconds, 2, "0", STR_PAD_LEFT );
 
$time               = implode( ':', $time );
 
return $time;
}
//Encabezado
encabezado("Reporte para calcular el tiempo atencion paciente",$wactualiz,"clinica");

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
 echo "<form name='rep_atencionfisiatria' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_atencionfisiatria' action='' method=post>";
  
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
   //echo "<tr>";
   //echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REPORTE PARA CALCULAR TIEMPO DE ATENCION PACIENTE</b></font></td>";
   //echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	

   $query = " SELECT Mtrhis,Mtring,a.Fecha_data,a.Hora_data,b.Movdat, TIMEDIFF(b.Movdat,a.Hora_data) as tiempo,((UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',b.Movdat)))-(UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',a.hora_data)))) " 
           ."   FROM ".$whce."_000022 a,".$whce."_000172 b "
           ."  WHERE a.fecha_data between '".$fec1."' and '".$fec2."'"
		   ."    AND Mtrcci = '1075' "
           ."    AND Mtrhis = b.movhis "
           ."    AND Mtring = b.moving "
		   ."    AND movcon = 3 " 
		   ."    AND a.fecha_data = b.fecha_data " 
           ."    Union  " 
		   ." SELECT Mtrhis,Mtring,a.Fecha_data,a.Hora_data,b.Movdat, TIMEDIFF(b.Movdat,a.Hora_data) as tiempo,((UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',b.Movdat)))-(UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',a.hora_data)))) "
           ."   FROM ".$whce."_000022 a,".$whce."_000175 b "
           ."  WHERE a.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Mtrcci = '1075' "
		   ."    AND Mtrhis = b.movhis "
           ."    AND Mtring = b.moving "
		   ."    AND movcon = 3 " 
		   ."    AND a.fecha_data = b.fecha_data " 
           ."    Union  " 
		   ." SELECT Mtrhis,Mtring,a.Fecha_data,a.Hora_data,b.Movdat,TIMEDIFF(b.Movdat,a.Hora_data) as tiempo,((UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',b.Movdat)))-(UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',a.hora_data)))) "
           ."   FROM ".$whce."_000022 a,".$whce."_000261 b "
           ."  WHERE a.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Mtrcci = '1075' "
		   ."    AND Mtrhis = b.movhis "
           ."    AND Mtring = b.moving "
		   ."    AND movcon = 3 " 
		   ."    AND a.fecha_data = b.fecha_data " 
           ."    Union  " 
		   ." SELECT Mtrhis,Mtring,a.Fecha_data,a.Hora_data,b.Movdat,TIMEDIFF(b.Movdat,a.Hora_data) as tiempo,((UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',b.Movdat)))-(UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',a.hora_data))))"
           ."   FROM ".$whce."_000022 a,".$whce."_000277 b "
           ."  WHERE a.fecha_data between '".$fec1."' and '".$fec2."'"
		   ."    AND Mtrcci = '1075' "
		   ."    AND Mtrhis = b.movhis "
           ."    AND Mtring = b.moving "
		   ."    AND movcon = 3 " 
		   ."    AND a.fecha_data = b.fecha_data " 
           ."    Union  " 
		   ." SELECT Mtrhis,Mtring,a.Fecha_data,a.Hora_data,b.Movdat, TIMEDIFF(b.Movdat,a.Hora_data) as tiempo,((UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',b.Movdat)))-(UNIX_TIMESTAMP(CONCAT(a.fecha_data,' ',a.hora_data))))"
           ."   FROM ".$whce."_000022 a,".$whce."_000137 b "
           ."  WHERE a.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Mtrcci = '1075' "
		   ."    AND Mtrhis = b.movhis "
           ."    AND Mtring = b.moving "
		   ."    AND movcon = 3 "
		   ."    AND a.fecha_data = b.fecha_data " ;
    
		   
           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query."<br>";
      
      echo "<br>";
   
      echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='300'>";
      
      echo "<tr> ";
      echo "<td class='fila2' ><font text color=#000000 size=2><b>HISTORIA</b></td>";
      echo "<td class='fila2' ><font text color=#000000 size=2><b>INGRESO</b></td>";
      echo "<td class='fila2' ><font text color=#000000 size=2><b>FECHA</b></td>";
      echo "<td class='fila2' ><font text color=#000000 size=2><b>HORA INGRESO</b></td>";
      echo "<td class='fila2' ><font text color=#000000 size=2><b>HORA ATENCION</b></td>";
      echo "<td class='fila2' ><font text color=#000000 size=2><b>TIEMPO DEMORA PARA ATENCION</b></td>";
	   //echo "<td class='fila2' ><font text color=#000000 size=2><b>diferencia</b></td>";
      echo "</tr>";
    $suma=0;
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
	 // echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[6]</font></td>";
      echo "</tr >";
    $suma=$suma + $row2[6];
     } // cierre del for
	echo "</table>";   
	echo "<br>";
   if ($num1 > 0 )
	  $wproserseg=($suma/$num1);
   $wproser= makeTimeFromSeconds($wproserseg);					
   echo "<table border=2  align=center size='200'>";
   echo "<Tr >";
   echo "<td class='fila1'><font size='3' color='#000000'><b>TOTAL HISTORIAS EVALUADAS: </b></font></td>"; 
   echo "<td class='fila2' align='center' ><font size=3 >&nbsp;<b>".number_format($num1)."</b></font></td></tr>";
   echo "<tr>";
   echo "<td class='fila1'><font size='3' color='#000000'><b>PROMEDIO DEMORA EN LA ATENCION (EN MINUTOS):</b></font></td>";
   echo "<td class='fila2' align='center'><font size=3 >&nbsp;<b>".$wproser."</b></td>";
   echo "</tr>";
   echo "</table>";
   echo "<br>";
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>