<html>
<head>
<title>MATRIX - [REPORTE SECUENCIA PARA LA TRAZABILIDAD DE EQUIPOS ]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_trazaequi01.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_trazaequi01.submit();
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
//PROGRAMA				      : Reporte para ver la trazabilidad de los equipos en central de esterilizacion                                |
//AUTOR				          : Ing. Gabriel Agudelo.                                                                                       |
//FECHA CREACION			  : Agosto 23 DE 2016.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : Agosto 23 DE 2016.                                                                                          |
//DESCRIPCION			      : Este reporte sirve para observar en general la trazabilidad de los equipos.                                 |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//urgen_000008      : Tabla de Pendientes de autorizaciones de admisiones.                                                                  |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 Agosto 23 DE 2016.";

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
encabezado("Trazabilidad de Equipos",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_trazaequi01.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 echo "<input type='HIDDEN' NAME= 'tabla' value='".$tabla."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_trazaequi01' action='' method=post>";
  
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

   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
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
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Codigo</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Marca</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Serie</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Lavado Manual</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Lavado Lavadora</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Prueba Buena</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Prueba Mala</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Secado Manual</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Secado Aire Comprimido</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Desinfeccion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Esterilizacion</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Sala Procedimiento</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Nombre</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Historia Clinica</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Prestamo</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Unidad Clinica</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Anaquelaje</b></td>";
    echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Mantenimiento</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Observacion</b></td>";
	echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>Usuario</b></td>";
    echo "</tr>";
    
    $query = " SELECT Fecha_data,Hora_data,Movfec,Horaproc,Movcod,Movmarca,Movserie,Movlavmanual,Movlavlavadora,
                      Movprubuena,Movprumala,Movsecmanual,Movsecaircomp,Movdesinfeccion,Movesterilizacion,Movdissalpro,
					  Movdissalpronom,Movdissalprohis,Movdispres,Movdisprescco,Movdisanaq,Movdismant,Movdismantobs,Seguridad "
            ."   FROM ".$tabla." "
            ."  WHERE Movfec between '".$fec1."' and '".$fec2."'"
            ."  ORDER BY Movfec ";
           
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
      echo "<td  align=center><font size=1>$row1[14]</font></td>";
	  echo "<td  align=center><font size=1>$row1[15]</font></td>";
      echo "<td  align=center><font size=1>$row1[16]</font></td>";
      echo "<td  align=center><font size=1>$row1[17]</font></td>";
	  echo "<td  align=center><font size=1>$row1[18]</font></td>";
      echo "<td  align=center><font size=1>$row1[19]</font></td>";
      echo "<td  align=center><font size=1>$row1[20]</font></td>";
	  echo "<td  align=center><font size=1>$row1[21]</font></td>";
	  echo "<td  align=center><font size=1>$row1[22]</font></td>";
	  echo "<td  align=center><font size=1>$row1[23]</font></td>";
      echo "</tr>";
	  
	}
   echo "</table>"; 
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>