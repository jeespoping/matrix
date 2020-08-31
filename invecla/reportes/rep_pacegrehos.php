<html>
<head>
<title>MATRIX - [PACIENTES EGRESADOS HOSPITALIZACION]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_pacegrehos.php'; 
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
//PROGRAMA				      :Reporte para ver los pacientes egresados por fecha.                                                          |
//AUTOR				          :Ing. Gabriel Agudelo.                                                                                        |
//FECHA CREACION			  :Noviembre 14 de 2017.                                                                                        |
//FECHA ULTIMA ACTUALIZACION  :Noviembre 14 de 2017.                                                                                        |
//TABLAS UTILIZADAS :                                                                                                                       |
//movhos_000033,movhos_000011 y movhos_000018                                                                                               |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 14-Noviembre-2017";

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
encabezado("Pacientes Egresados de Hospitalizacion",$wactualiz,"clinica");

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
 echo "<form name='rep_pacegrehos' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_pacegrehos' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PACIENTES EGRESADOS DE HOSPITALIZACION</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   $query = " select Historia_clinica,Num_ingreso,Servicio,Cconom,Fecha_egre_serv,Tipo_egre_serv,Ubihac,Ingfei "
           ."   from  movhos_000033,movhos_000011,movhos_000018,cliame_000101  "
           ."  where Fecha_egre_serv between '".$fec1."' and '".$fec2."'"
           ."    and  Tipo_egre_serv in ('ALTA','MUERTE MAYOR A 48 HORAS','MUERTE MENOR A 48 HORAS','MUERTE') "
           ."    and  Servicio = Ccocod "
		   ."    and  ccohos = 'on' "
		   ."    and  Historia_clinica = Inghis "
		   ."    and  Num_ingreso = Ingnin "
		   ."    and  Historia_clinica = ubihis "
		   ."    and  Num_ingreso = ubiing "
           ."    and  (Ubihac != ' ' or Ubihan != ' ' or Ubimue = 'on' ) ";
           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query."<br>";
      
      echo "<br>";
   
      echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='300' >";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>HISTORIA</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>INGRESO</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>CENTRO DE COSTOS</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>NOMBRE SERVICIO</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>FECHA EGRESO</b></td>";
      echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>TIPO EGRESO</b></td>";
	  echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>HABITACION</b></td>";
	  echo "<td bgcolor='#FFFFFF' align=center><font text color=#003366 size=2><b>FECHA INGRESO CLINICA</b></td>";
      echo "</tr>";
    $co = 0;
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);
	  $co++;
		if (is_int ($co/2))
		{
			$wcf="F8FBFC";  // color de fondo
		}
		else
		{
			$wcf="DFF8FF"; // color de fondo
		}
      echo "<tr bgcolor=".$wcf.">";
      echo "<td  align=center><font size=1>".$row2[0]."</font></td>";
      echo "<td  align=center><font size=1>".$row2[1]."</font></td>";
      echo "<td  align=center><font size=1>".$row2[2]."</font></td>";
      echo "<td  align=center><font size=1>".$row2[3]."</font></td>";
      echo "<td  align=center><font size=1>".$row2[4]."</font></td>";
      echo "<td  align=center><font size=1>".$row2[5]."</font></td>";
	  echo "<td  align=center><font size=1>".$row2[6]."</font></td>";
	  echo "<td  align=center><font size=1>".$row2[7]."</font></td>";
      echo "</tr >";
  
     } // cierre del for

   echo "</table>";   

   echo "<br>";
   
   echo "<table border=0 >";
   echo "<Tr >";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='3' color='#000000'><b>TOTAL EGRESOS: </b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=left width=65%><font size=3 color=#FF0000>&nbsp;<b>".number_format($num1)."</b></font></td>";
   echo "</tr >"; 
   echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>