<html>
<head>
<title>MATRIX - [REPORTE PENDIENTES DE AUTORIZACIONES ADMISIONES X CAUSA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_pendxcausa.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_pendxcausa.submit();
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
*                                             REPORTE PENDIENTES DE AUTORIZACIONES X CAUSA                                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los pendientes de autorizaciones de admisiones y los NO                                    |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : JUNIO 17 DE 2010.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : JUNIO 17 DE 2010.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para observar en general las autorizaciones pendientes y no.                             |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000008      : Tabla de Pendientes de autorizaciones de admisiones.                                                                  |
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
encabezado("Total Pendientes y NO pendientes de Autorizaciones",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_pendxcausa.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or !isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_pendxcausa' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los par�metros de consulta';
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

 	
 	/////////////////////////////////////////////////////////////////////////// seleccion para los Responsables
    echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Responsable:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = " SELECT paresp,'-',Descripcion"
            ."   FROM urgen_000008,usuarios"
            ."  WHERE paresp = codigo"
            ." GROUP BY 1,2,3"
            ." ORDER BY 1,2,3";
           
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
	 echo "<option>".$row3[0]."-".$row3[2]."</option>";
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
  	
  	$tpp=explode('-',$pp);
  	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PENDIENTES Y NO PENDIENTES DE AUTORIZACIONES EN ADMISIONES Y SUS CAUSAS</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
      
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>COD_RESPONSABLE</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>NOMBRE_RESPONSABLE</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>CAUSA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>FECHA_GRABACION</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HISTORIA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>INGRESO</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>DIAS_RESPUESTA</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>HORAS_RESPUESTA</b></td>";
    echo "</tr>";
    
    IF ($tpp[0]=="TODOS")
    {
      $query = " SELECT Papendi,Paresp,Descripcion,Pacausas,urgen_000008.fecha_data,Pahist,Paingr,sum(DATEDIFF(Pafechresp,Pafecenv)),subtime(Pahorresp,Pahorenv)"
              ."   FROM urgen_000008,usuarios"
              ."  WHERE urgen_000008.fecha_data between '".$fec1."' and '".$fec2."'"
              ."    AND paresp = Codigo"
              ."  GROUP BY Papendi,Paresp,Descripcion,Pacausas,urgen_000008.fecha_data,Pahist,Paingr"
              ."  ORDER BY papendi,paresp,pacausas,urgen_000008.fecha_data,pahist,paingr";
    }
    ELSE
    {
      $query = " SELECT Papendi,Paresp,Descripcion,Pacausas,urgen_000008.fecha_data,Pahist,Paingr,sum(DATEDIFF(Pafechresp,Pafecenv)),subtime(Pahorresp,Pahorenv)"
              ."   FROM urgen_000008,usuarios"
              ."  WHERE urgen_000008.fecha_data between '".$fec1."' and '".$fec2."'"
              ."    AND paresp = '".$tpp[0]."'"
              ."    AND paresp = Codigo"
              ."  GROUP BY Papendi,Paresp,Descripcion,Pacausas,urgen_000008.fecha_data,Pahist,Paingr"
              ."  ORDER BY papendi,paresp,pacausas,urgen_000008.fecha_data,pahist,paingr";	       
    }
    
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
   
    //echo $query;
    //echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    
    $pendiant='';
    $hisant='';
    $ingant='';
    $resant='';
    $descant='';
    $fecant='';
    $cauant='';
    
    $totsi=0;
	$totno=0;
	
	
	$wcfant='';
	
	$horatotal='00:00:00';
	
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
      $pendiant = $row1[0];
      echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>PENDIENTE : </b></font></td><td align=center colspan=7><font color=#FF0000 text size=2>".$pendiant."</td></tr>"; 
      $swtitulo='NO';
 	   
	 }
	    	  
	if ($pendiant==$row1[0] )
	 {
	  echo "<Tr bgcolor=".$wcf.">";
      echo "<td align=center><font size=1>$row1[1]</font></td>";
      echo "<td align=center><font size=1>$row1[2]</font></td>";
      echo "<td align=center><font size=1>$row1[3]</font></td>";
      echo "<td align=center><font size=1>$row1[4]</font></td>";
      echo "<td align=center><font size=1>$row1[5]</font></td>";
	  echo "<td align=center><font size=1>$row1[6]</font></td>"; 
	  
	  IF ($row1[7]==0)
	  {
	   echo "<td align=center><font size=1>$row1[7]</font></td>";
	   echo "<td align=center><font size=1>$row1[8]</font></td>"; 
	  }
	  ELSE
	  {
	   $horatotal='00:00:00';
	   echo "<td align=center><font size=1>$row1[7]</font></td>";
	   echo "<td align=center><font size=1>$horatotal</font></td>"; 
	  }
	  
	   
	  IF ($row1[0]=='1-SI' )
	  {
	   $totsi=$totsi+1;
	  }
	  ELSE
	  {
	   $totno=$totno+1;
	  }
	 }
	else 
	 {
	  IF ($pendiant=='1-SI')
	  {	
	   echo "<tr>";
	   echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL PENDIENTES : </b></font></td>";
	   echo "<td align=center><font color=#FF0000 text size=2>".$pendiant."</td>";
	   echo "<td align=center colspan=6><font color=#FF0000 text size=2>".$totsi."</td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "</tr>";
	  }
 
	  $wcfant=$wcf;
	  $pendiant=$row1[0];

	  echo "<tr>";
	  echo "</tr>";
	  echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>PENDIENTE : </b></font></td><td align=center colspan=7><font color=#FF0000 text size=2>".$pendiant."</td></tr>"; 
	  
	  echo "<Tr bgcolor=".$wcfant.">";
      echo "<td align=center><font size=1>$row1[1]</font></td>";
      echo "<td align=center><font size=1>$row1[2]</font></td>";
      echo "<td align=center><font size=1>$row1[3]</font></td>";
      echo "<td align=center><font size=1>$row1[4]</font></td>";
      echo "<td align=center><font size=1>$row1[5]</font></td>";
	  echo "<td align=center><font size=1>$row1[6]</font></td>"; 
	  
	  IF ($row1[7]==0)
	  {
	   echo "<td align=center><font size=1>$row1[7]</font></td>";
	   echo "<td align=center><font size=1>$row1[8]</font></td>"; 
	  }
	  ELSE
	  {
	   $horatotal='00:00:00';
	   echo "<td align=center><font size=1>$row1[7]</font></td>";
	   echo "<td align=center><font size=1>$horatotal</font></td>"; 
	  }
	 }
	}

	$totno=$totno+1;
	
	echo "<tr>";
	echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL PENDIENTES : </b></font></td>";
	echo "<td align=center><font color=#FF0000 text size=2>".$pendiant."</td>";
	echo "<td align=center colspan=6><font color=#FF0000 text size=2>".$totno."</td>";
	echo "</tr>";
	echo "</table>"; 

	
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atr�s' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresi�n

}
?>