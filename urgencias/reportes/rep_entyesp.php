<html>
<head>
<title>MATRIX - [REPORTE PENDIENTES DE AUTORIZACIONES ADMISIONES X CAUSA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_entyesp.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_entyesp.submit();
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
*                     REPORTE DE REFERENCIA Y CONTRAREFERENCIA X FECHA - ENTIDAD - ESPECIALIDAD                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte referencia y contrareferencia x fecha - entidad - especialidad                                      |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Dic 14 de 2012.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : Dic 14 de 2012.                                                                                             |
//DESCRIPCION			      : Este reporte sirve para traer los datos de referencia y contrareferencia por entidad y especialidad.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000003      : Tabla de Referencia y Contrareferencia.                                                                               |
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
encabezado("REPORTE DE REF. Y CONTRAREF. POR ESPECIALIDAD Y ENTIDAD",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_entyesp.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or !isset($pp1) or $pp1=='-' or !isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_entyesp' action='' method=post>";
  
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

 	
 	/////////////////////////////////////////////////////////////////////////// seleccion para la Especialidad
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Especialidad:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = " SELECT Especialidad "
            ."   FROM urgen_000003 "
            ." GROUP BY 1 "
            ." ORDER BY 1 ";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if (!isset($pp))
    { 
     echo "<option></option>";
    }
    else 
    {
     echo "<option>".$tpp[0]."-".$tpp[2]."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	 {
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."</option>";
	 }
	echo "<option>TODOS</option>";
    echo "</select></td></tr>";
 	
  // echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
  // echo "</table>";
  // echo '</div>';
  // echo '</div>';
  // echo '</div>';
   
   /////////////////////////////////////////////////////////////////////////// seleccion para la Entidad
    echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Entidad:</B><br></font></b><select name='pp1' id='searchinput'>";

    $query = " SELECT Entidad "
            ." FROM urgen_000003 "
            ." GROUP BY 1 "
            ." ORDER BY 1 ";
           
    $err4 = mysql_query($query,$conex);
    $num4 = mysql_num_rows($err4);
    $epp=$pp1;
   
    if (!isset($epp))
    { 
     echo "<option></option>";
    }
    else 
    {
     echo "<option>".$epp[0]."</option>";
    } 
   
    for ($i=1;$i<=$num4;$i++)
	 {
	 $row4 = mysql_fetch_array($err4);
	 echo "<option>".$row4[0]."</option>";
	 }
	echo "<option>TODOS</option>";
    echo "</select></td>";
 	
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
  	
  	$tpp=explode('|',$pp);
	$epp=explode('-',$pp1);
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REFERENCIA Y CONTRAREFERENCIA URGENCIAS</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
	echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>ESPECIALIDAD: <i>".$tpp[0]."</i>&nbsp&nbsp&nbspENTIDAD: <i>".$epp[0]."</i></b></font></b></font></td>";
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
    
    IF ($tpp[0]=="TODOS" or $tpp[0]==" ")
		$tpp[0]="%";
	IF ($epp[0]=="TODOS" or $epp[0]==" ")
		$epp[0]="%";
		
  //                       0     1     2    3        4         5           6          7     8         9          10     11      12       13              14            15                           
      $query = " SELECT Codigo,Fecha,Hora,Nombre,Documento,Diagnostico,Especialidad,Edad,Entidad,Sem_Cotizadas,Rango,Autoriza,Acepto,Observaciones,Causa_Remision,Estado_paciente  "
              ."   FROM urgen_000003 "
              ."  WHERE Fecha between '".$fec1."' and '".$fec2."'"
			  ."    AND Entidad like '".$epp[0]."'"
              ."    AND Especialidad like '".$tpp[0]."'"
              ."  ORDER BY 7,9 ";
  
    
	 $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);
	// $row1 = mysql_fetch_array($err1);
	
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