<html>
<head>
<title>MATRIX - [REPORTE MAESTRO DE TERCEROS POR NIT]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_materceros.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_materceros.submit();
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
*                     REPORTE MAESTRO DE TERCEROS POR NIT                                                                                  *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : REPORTE MAESTRO DE TERCEROS POR NIT                                                                         |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Ago 31 de 2016.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : Ago 31 de 2016.                                                                                             |
//DESCRIPCION			      : Este reporte sirve para traer los datos del maestro de terceros por Nit                                     |
//                                                                                                                                          |
//TABLAS UTILIZADAS           :                                                                                                             |
//tesorhon_000001             : Maestro de Terceros.                                                                                        | 
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 31-Ago-2016";

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
encabezado("MAESTRO DE TERCEROS POR NIT",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_materceros.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or $pp == '' )
  {
  	echo "<form name='rep_materceros' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de los datos para el reporte
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Tercero:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = " SELECT Nit,Left(Nombre,50) "
            ."   FROM tesorhon_000001 "
			." WHERE Fecha_data > '2015-01-01' "
            ." GROUP BY 1,2 "
            ." ORDER BY 1,2 ";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if (!isset($pp))
    { 
     echo "<option>%%%-Todos</option>";
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
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>MAESTRO DE TERCEROS</b></font></td>";
    echo "</tr>";
	echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>NIT: <i>".$tpp[0]."</i>&nbsp&nbsp&nbspNOMBRE: <i>".$tpp[1]."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
    echo "<br>";
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CODIGO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NIT</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIRECCION</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>TELEFONO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCOSTOS1</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC CC1</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCOSTOS2</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC CC2</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCOSTOS3</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC CC3</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCOSTOS4</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC CC4</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CCOSTOS5</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC CC5</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ESPECIALIDAD</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>VALOR CONTRATO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>AFC</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>VALOR AFC</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC AFC</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PV</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>VALOR PV</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>PORC PV</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>GRUPO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>LIQ ESPECIAL</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>OBSERVACION</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>ACTIVO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CORREO</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CONCEPTO</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INFORMACION CONTRATO</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>VALOR CONTRATO FIJO O VARIABLE</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAS DE PAGO</b></td>";
    echo "</tr>";
    
   	
  //                       0     1     2      3        4         5        6        7       8         9      10       11       12       13       14         15            16         17     18         19    20     21       22      23       24          25        26     27      28              29                  30                    31
      $query = " SELECT Codigo,Nit,Nombre,Direccion,Telefono,Ccosto1,Porcencc1,Ccosto2,Porcencc2,Ccosto3,Porcencc3,Ccosto4,Porcencc4,Ccosto5,Porcencc5,Especialidad,Valor_contrato,Afc,Valor_afc,Porcen_afc,Pv,Valor_pv,Porcen_pv,Grupo,Liq_especial,Observacion,Activo,Correo,Concepto,Informacion_contrato,Vr_contrato_fijo_variable,Dias_de_pago "
              ."   FROM tesorhon_000001 "
	          ."  WHERE Nit like '".$tpp[0]."'"
              ."  ORDER BY 1,2,3 ";
  
    
	 $err1 = mysql_query($query,$conex);
     $num1 = mysql_num_rows($err1);

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
	  echo "<td align=center><font size=1>$row1[16]</font></td>";
	  echo "<td align=center><font size=1>$row1[17]</font></td>";
      echo "<td align=center><font size=1>$row1[18]</font></td>";
      echo "<td align=center><font size=1>$row1[19]</font></td>";
      echo "<td align=center><font size=1>$row1[20]</font></td>";
      echo "<td align=center><font size=1>$row1[21]</font></td>";
	  echo "<td align=center><font size=1>$row1[22]</font></td>"; 
	  echo "<td align=center><font size=1>$row1[23]</font></td>";
	  echo "<td align=center><font size=1>$row1[24]</font></td>";
      echo "<td align=center><font size=1>$row1[25]</font></td>";
      echo "<td align=center><font size=1>$row1[26]</font></td>";
      echo "<td align=center><font size=1>$row1[27]</font></td>";
	  echo "<td align=center><font size=1>$row1[28]</font></td>";
	  echo "<td align=center><font size=1>$row1[29]</font></td>";
	  echo "<td align=center><font size=1>$row1[30]</font></td>";
	  echo "<td align=center><font size=1>$row1[31]</font></td>";
     
	}
	echo "</table>"; 
		
   echo "<br>";
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>