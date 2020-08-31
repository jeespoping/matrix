<html>
<head>
<title>MATRIX - [REPORTE REGISTRO DIARIO DE DESINFECCION ALTO NIVEL]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_regdes01.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_regdes01.submit();
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
*                                             REPORTE SECUENCIA PARA LA TRAZABILIDAD DE EQUIPOS                                       *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver Registro diario de desinfeccion alto nivel en central de esterilizacion                                |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Febrero 22 de 2018.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : Febrero 22 de 2018.                                                                                          |
//DESCRIPCION			      : Este reporte sirve para observar en general el Registro diario de desinfeccion alto nivel.                                 |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cenest_000010      : Tabla de Registro diario de desinfeccion alto nivel                                                                |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 Febrero 22 de 2018.";

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
encabezado("Registro Diario de Desinfeccion Alto Nivel",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='cenest';
 //Conexion base de datos
 

 


 //Forma
 echo "<form name='forma' action='rep_regdes01.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 echo "<input type='HIDDEN' NAME= 'tabla' value='".$tabla."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_regdes01' action='' method=post>";
  
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

    $query = " SELECT Movcco "
            ."   FROM cenest_000010 "
            ."  WHERE 1 = 1 "
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
     echo "<option>".$tpp[0]."</option>";
    } 
   
    for ($i=1;$i<=$num3;$i++)
	 {
	 $row3 = mysql_fetch_array($err3);
	 echo "<option>".$row3[0]."</option>";
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
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>REPORTE SECUENCIA PARA LA TRAZABILIDAD DE EQUIPOS</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "</tr>";
	echo "<tr><br><td></td></tr>";
    echo "</table>";
      
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Fecha Sistema</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Hora Sistema</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Fecha Proceso</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Hora Proceso</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Modo de Medicion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Codigo</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Marca</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Serie</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Tipo de Indicador</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Concentracion Minima Efectiva</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Colorimetria</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Observaciones</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Centro de Costos</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Usuario</b></td>";
	echo "</tr>";
    
  	 
	 IF ($tpp[0]=="TODOS")
    {
		$query = " SELECT Fecha_data,Hora_data,Movfec,Horaproc,Movmedicion,Modcod,Movmarca,Movserie,Movtipind,Movconmin,
						  Movcolor,Movobs,Movcco,Seguridad "
				."   FROM ".$tabla." "
				."  WHERE Movfec between '".$fec1."' and '".$fec2."'"
				."  ORDER BY Movfec ";
    }
	ELSE
	{
		$query = " SELECT Fecha_data,Hora_data,Movfec,Horaproc,Movmedicion,Modcod,Movmarca,Movserie,Movtipind,Movconmin,
						  Movcolor,Movobs,Movcco,Seguridad "
				."   FROM ".$tabla." "
				."  WHERE Movfec between '".$fec1."' and '".$fec2."'"
				."    AND Movcco like '%".$tpp[0]."%'"
				."  ORDER BY Movfec ";
		
	}
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
     
    $swtitulo='SI';
      
	for ($i=1;$i<=$num1;$i++)
	{
		 if (is_int ($i/2))
		  {
		  // $wcf="DDDDDD";  // color de fondo
		   $wcf="EFF8FB";  // color de fondo
		  }
		 else
		  {
		   //$wcf="CCFFFF"; // color de fondo 
		   $wcf="A9E2F3"; // color de fondo
		  }

		$row1 = mysql_fetch_array($err1);
	   
   		    	  
	  echo "<Tr  bgcolor=".$wcf.">";
      echo "<td  align=center><font size=1>$row1[0]</font></td>";
      echo "<td  align=center><font size=1>$row1[1]</font></td>";
      echo "<td  align=center><font size=1>$row1[2]</font></td>";
	  echo "<td  align=center><font size=1>$row1[3]</font></td>";
      echo "<td  align=center><font size=1>$row1[4]</font></td>";
      echo "<td  align=center><font size=1>$row1[5]</font></td>";
	  echo "<td  align=center><font size=1>$row1[6]</font></td>";
      echo "<td  align=center><font size=1>$row1[7]</font></td>";
      echo "<td  align=center><font size=1>$row1[8]</font></td>";
	  echo "<td  align=center><font size=1>$row1[9]</font></td>";
      echo "<td  align=center><font size=1>$row1[10]</font></td>";
      echo "<td  align=center><font size=1>$row1[11]</font></td>";
	  echo "<td  align=center><font size=1>$row1[12]</font></td>";
	  echo "<td  align=center><font size=1>$row1[13]</font></td>";
      echo "</tr>";
	  
	}
   echo "</table>"; 
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>