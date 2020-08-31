<html>
<head>
<title>MATRIX - [CITAS VIDEOENDOSCOPIA INACTIVAS O CANCELADAS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_citasenI.php'; 
	}
	
	function enter()
	{
		document.forms.rep_citasenI.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE CITAS VIDEOENDOSCOPIA INACTIVAS O CANCELADAS                                         *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver las citas inactivas o canceladas de videoendoscopia.                                       |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 8 DE 2011.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : 8 de AGOSTO de 2011.                                                                                        |
//TABLAS UTILIZADAS :                                                                                                                       |
//citasen_000009      		  : Tabla de citas de los medicos de VIDEOENDOSCOPIA.                                                           |
//citasen_000002 			  : Tabla de Empresas.																							|
//citasen_000010      		  : Tabla de Horarios de VIDEOENDOSCOPIA.                                                                       |
//citasen_000011 			  : Tabla de procedimientos de VIDEOENDOSCOPIA.				   													|
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 9-Agosto-2011";

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
encabezado("Citas de Videoendoscopia Inactivas o Canceladas",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 $empre1='citasen';
 

 //Forma
 echo "<form name='forma' action='rep_citasenI.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_citasenI' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>CITAS DE VIDEOENDOSCOPIA INACTIVAS O CANCELADAS</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   $query = "SELECT ".$empre1."_000010.descripcion AS medico, ".$empre1."_000011.descripcion AS examen, ".$empre1."_000009.fecha, ".$empre1."_000009.hi, ".$empre1."_000009.hf, ".$empre1."_000009.nom_pac, ".$empre1."_000009.telefono, ".$empre1."_000009.nit_res, ".$empre1."_000002.descripcion AS empresa, ".$empre1."_000009.edad, ".$empre1."_000009.comentario" 
           ."  FROM ".$empre1."_000009, ".$empre1."_000010, ".$empre1."_000011, ".$empre1."_000002" 
           ." WHERE ".$empre1."_000009.fecha BETWEEN  '".$fec1."' and '".$fec2."'"
           ."  AND ".$empre1."_000009.cod_equ = ".$empre1."_000010.codigo" 
           ."  AND ".$empre1."_000009.cod_equ = ".$empre1."_000011.cod_equipo" 
           ."  AND ".$empre1."_000009.cod_exa = ".$empre1."_000011.codigo" 
           ."  AND ".$empre1."_000009.nit_res = ".$empre1."_000002.nit" 
           ."  AND ".$empre1."_000009.activo =  'I'" 
           ." GROUP  BY 1 , 2, 3, 4, 5, 6, 7, 8, 9, 10, 11" 
           ." UNION  ALL"  
           ." SELECT ".$empre1."_000010.descripcion AS medico, ".$empre1."_000011.descripcion AS examen, ".$empre1."_000009.fecha, ".$empre1."_000009.hi, ".$empre1."_000009.hf, ".$empre1."_000009.nom_pac, ".$empre1."_000009.telefono, ".$empre1."_000009.nit_res, ".$empre1."_000002.descripcion AS empresa, ".$empre1."_000009.edad, ".$empre1."_000009.comentario" 
           ."   FROM ".$empre1."_000009, ".$empre1."_000010, ".$empre1."_000011, ".$empre1."_000002" 
           ."  WHERE ".$empre1."_000009.fecha BETWEEN  '".$fec1."' and '".$fec2."'"
           ."    AND ".$empre1."_000009.cod_equ = ".$empre1."_000010.codigo" 
           ."    AND ".$empre1."_000009.cod_equ = ".$empre1."_000011.cod_equipo" 
           ."    AND ".$empre1."_000009.cod_exa = ".$empre1."_000011.codigo" 
           ."    AND ".$empre1."_000009.nit_res = ".$empre1."_000002.nit" 
           ."    AND ".$empre1."_000009.nom_pac LIKE  '%cance%'" 
           ."  GROUP  BY 1 , 2, 3, 4, 5, 6, 7, 8, 9, 10, 11" 
           ."  ORDER  BY 3, 1";

           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query."<br>";

	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);

	  echo "<table border=1 cellpadding='0' cellspacing='0' size='600'>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>MEDICO</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>EXAMEN</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>FECHA</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>HORA_INICIAL</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>HORA_FINAL</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>NOMBRE_PACIENTE</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>TELEFONO</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>NIT</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>EMPRESA</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=2><b>EDAD</b></td>";
      echo "</tr>";
      
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[0]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[1]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[2]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[3]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[4]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[5]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[6]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[7]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[8]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[9]</font></td>";
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='2' color='#000000'><b>OBSERVACIONES:</b></font></td>"; 
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=left width=100%><font size=1>&nbsp;$row2[10]</font></td>";
      echo "</tr >"; 
      echo "</table>";
      
      echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<tr><td align=LEFT bgcolor=#FFFFFF><font size='1' color='#0000FF'><b>------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</b></font></tr>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "</Tr >"; 
      echo "</table>";
	
  } // cierre del for
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>