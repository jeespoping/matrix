<html>
<head>
<title>MATRIX - [REPORTE REFERENCIA Y CONTRAREFERENCIA POR CEDULA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_refced.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_refced.submit();
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

function segundos_tiempo($segundos){  
$minutos=$segundos/60;  
$horas=floor($minutos/60);  
$minutos2=$minutos%60;  
$segundos_2=$segundos%60%60%60;  
if($minutos2<10)$minutos2='0'.$minutos2;  
if($segundos_2<10)$segundos_2='0'.$segundos_2;  

if($segundos<60){ /* segundos */  
$resultado= round($segundos).' Segundos';  
}elseif($segundos>60 && $segundos<3600){/* minutos */  
$resultado= $minutos2.':'.$segundos_2.' Minutos';  
}else{/* horas */  
$resultado= $horas.':'.$minutos2.':'.$segundos_2.' Horas';  
}  
return $resultado;  
} 

/*******************************************************************************************************************************************
*                     REPORTE DE REFERENCIA Y CONTRAREFERENCIA X CEDULA                                                                    *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte referencia y contrareferencia x cedula                                                              |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Dic 28 de 2012.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : Dic 28 de 2012.                                                                                             |
//DESCRIPCION			      : Este reporte sirve para traer los datos de referencia y contrareferencia por cedula                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000003      : Tabla de Referencia y Contrareferencia.                                                                               |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 28-Dic-2012";

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
encabezado("REPORTE DE REF. Y CONTRAREF. POR CEDULA",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_refced.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or $pp == '' )
  {
  	echo "<form name='rep_refced' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de los datos para el reporte
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Cedula:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = " SELECT Documento,Left(Nombre,50) "
            ."   FROM urgen_000003 "
			." WHERE Fecha > '2012-05-29' "
            ." GROUP BY 1,2 "
            ." ORDER BY 1,2 ";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if (!isset($pp))
    { 
     echo "<option></option>";
    }
    else 
    {
     echo "<option>".$tpp[0]."-".$tpp[1]."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	 {
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."-".$row3[1]."</option>";
	 }
	echo "<option></option>";
    echo "</select></td></tr>";
 	
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
  	$tpp=explode('-',$pp);
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REFERENCIA Y CONTRAREFERENCIA URGENCIAS</b></font></td>";
    echo "</tr>";
	echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>CEDULA: <i>".$tpp[0]."</i>&nbsp&nbsp&nbspNOMBRE: <i>".$tpp[1]."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
    echo "<br>";
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CODIGO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HORA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DOCUMENTO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAGNOSTICO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ESPECIALIDAD</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>EDAD</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ENTIDAD</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>SEMANAS COTIZADAS</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>RANGO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>AUTORIZA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ACEPTO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CAUSA NO ACEPTACION</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CAUSA REMISION</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ESTADO DEL PACIENTE</b></td>";
    echo "</tr>";
    
   	
  //                       0     1     2    3        4         5           6          7     8         9          10     11      12       13              14            15                           
      $query = " SELECT Codigo,Fecha,Hora,Nombre,Documento,Diagnostico,Especialidad,Edad,Entidad,Sem_Cotizadas,Rango,Autoriza,Acepto,Observaciones,Causa_Remision,Estado_paciente  "
              ."   FROM urgen_000003 "
	          ."  WHERE Documento = '".$tpp[0]."'"
              ."  ORDER BY 4,6,8 ";
  
    
	 $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);

	$tot=0; 
	$totsi=0;
	$totno=0;
	
	
	for ($i=1;$i<=$num1;$i++)
	{
	 if (is_int ($i/2))
	  {
	    $wcf="F8FBFC";  // color de fondo
	  }
	 else
	  {
	    $wcf="DFF8FF"; // color de fondo
	  }

    $row1 = mysql_fetch_array($err1);
	   
   	  echo "<Tr bgcolor=".$wcf.">";
      echo "<td align=center><font size=1>$row1[0]</font></td>";
	  echo "<td align=center><font size=1>$row1[1]</font></td>";
      echo "<td align=center><font size=1>$row1[2]</font></td>";
      echo "<td align=center><font size=1>$row1[3]</font></td>";
      echo "<td align=center><font size=1>$row1[4]</font></td>";
      echo "<td align=center><font size=1>$row1[5]</font></td>";
	  echo "<td align=center><font size=1>$row1[6]</font></td>"; 
	  echo "<td align=center><font size=1>$row1[7]</font></td>";
	  echo "<td align=center><font size=1>$row1[8]</font></td>";
      echo "<td align=center><font size=1>$row1[9]</font></td>";
      echo "<td align=center><font size=1>$row1[10]</font></td>";
      echo "<td align=center><font size=1>$row1[11]</font></td>";
      echo "<td align=center><font size=1>$row1[12]</font></td>";
	  echo "<td align=center><font size=1>$row1[13]</font></td>"; 
	  echo "<td align=center><font size=1>$row1[14]</font></td>";
	  echo "<td align=center><font size=1>$row1[15]</font></td>"; 
	
	  $tot=$tot + 1;
	  if ($row1[12] == "on")
		$totsi = $totsi + 1;
	  else
	    $totno = $totno + 1;
	}
	echo "</table>"; 
	echo "<br>";
	echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
	echo "<tr><td align=center colspan=3 bgcolor=#006699><font text color=#FFFFFF size=4><b>CANTIDAD TOTAL: </b></font></td>";
	echo "<td align=center><font color=#FF0000 text size=4>".$tot."</td></tr>";
	echo "<tr><td align=center colspan=3 bgcolor=#006699><font text color=#FFFFFF size=4><b>ACEPTADOS: </b></font></td>";
	echo "<td align=center><font color=#FF0000 text size=4>".$totsi."</td></tr>";
	echo "<tr><td align=center colspan=3 bgcolor=#006699><font text color=#FFFFFF size=4><b>NO ACEPTADOS: </b></font></td>";
	echo "<td align=center><font color=#FF0000 text size=4>".$totno."</td></tr>";
	echo "</table>"; 

   echo "<br>";
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>