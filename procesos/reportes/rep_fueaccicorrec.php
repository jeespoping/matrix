<html>
<head>
<title>MATRIX - [REPORTE FUENTES DE ACCIONES]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_fueaccicorrec.submit();
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
*                                             REPORTE DE FUENTES DE ACCIONES CORRECTIVAS                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte de fuentes de acciones correctivas desarrollo organizacional.                                       |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 29 DE 2011.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : AGOSTO 29 DE 2011.                                                                                          |
//DESCRIPCION			      : Este reporte sirve para observar por Fuente cuantas acciones correctivas hace en un rango de fecha.         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//proceso_000012      : Tabla de Acciones correctivas de los coordinadores.                                                                 |                                                                                                                                 
//proceso_000015      : Tabla de Acciones correctivas del ccosto 1013.                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 29-Agosto-2011";

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
encabezado("Estado Acciones",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='proceso';

 

 


 //Forma
 echo "<form name='forma' action='rep_fueaccicorrec.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_fueaccicorrec' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>FUENTES DE ACCIONES</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
   
  //Query para traer numero de acciones por fuente de accion
  $quer3 ="CREATE TEMPORARY TABLE if not exists tmp1111 as "
        ." SELECT acftesele,count(*) as cant"
        ."   FROM ".$empre1."_000012"
        ."  WHERE CHAR_LENGTH(acacc1) > 2" //CHAR_LENGTH(acacc1) sirve para ver el total de caracteres  
        ."    AND Acfec1  between '".$fec1."' and '".$fec2."'" 
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT acftesele,count(*) as cant"
        ."   FROM ".$empre1."_000012"
        ."  WHERE CHAR_LENGTH(acacc2) > 2" //CHAR_LENGTH(acacc2) sirve para ver el total de caracteres  
        ."    AND Acfec2  between '".$fec1."' and '".$fec2."'" 
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT acftesele,count(*) as cant"
        ."   FROM ".$empre1."_000012"
        ."  WHERE CHAR_LENGTH(acacc3) > 2" //CHAR_LENGTH(acacc3) sirve para ver el total de caracteres  
        ."    AND Acfec3  between '".$fec1."' and '".$fec2."'" 
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT acftesele,count(*) as cant"
        ."   FROM ".$empre1."_000012"
        ."  WHERE CHAR_LENGTH(acacc4) > 2" //CHAR_LENGTH(acacc4) sirve para ver el total de caracteres  
        ."    AND Acfec4  between '".$fec1."' and '".$fec2."'" 
        ."  GROUP by 1"        
        ." UNION ALL"
        ." SELECT acftesele,count(*) as cant"
        ."   FROM ".$empre1."_000012"
        ."  WHERE CHAR_LENGTH(acacc5) > 2" //CHAR_LENGTH(acacc5) sirve para ver el total de caracteres  
        ."    AND Acfec5  between '".$fec1."' and '".$fec2."'" 
        ."  GROUP by 1"
        ."  ORDER BY acftesele";
   //  echo $quer3."<br>";
        
  $err3 = mysql_query($quer3,$conex); 
    
  $quer4 ="SELECT acftesele,sum(cant)"
         ."  FROM tmp1111"
         ." GROUP BY 1"
         ." ORDER BY 1";
   //  echo $quer4."<br>";
        
  $err4 = mysql_query($quer4,$conex);  
  $num4 = mysql_num_rows($err4);     

  //Query para traer el total de acciones nuevas por centro de costos
  $quer5 = "SELECT sum(cant)"
         ."  FROM tmp1111";
  //  echo $quer4."<br>";
        
  $err5 = mysql_query($quer5,$conex);       
  $num5 = mysql_num_rows($err5);
  $row5 = mysql_fetch_array($err5);
  
  //TITULOS DEL REPORTE
  echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='140'>";
  echo "<tr>";
  echo "<td bgcolor='#FFFFFF' align=center width=100><font text color=#000000 size=2><b>FUENTE</b></td>";
  echo "<td bgcolor='#FFFFFF' align=center width=100><font text color=#000000 size=2><b>NUMERO</b></td>";
  echo "<td bgcolor='#FFFFFF' align=center width=100><font text color=#000000 size=2><b>%</b></td>";
  echo "</tr>";

  
  for ($i=1;$i<=$num4;$i++)
   {
   	$row4 = mysql_fetch_array($err4);
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row4[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row4[1])."</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($row4[1]/$row5[0])*100)."%</font></td>";
   } 
   echo "</tr >";
      
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>TOTAL</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row5[0])."</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>100%</font></td>";
   echo "</tr >";
   
   echo "</table>";
    
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>