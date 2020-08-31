<html>
<head>
<title>MATRIX - [REPORTE PARA VER POR CARGO LOS EMPLEADOS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_porcargo.php'; 
	}
	
	function enter()
	{
		document.forms.rep_porcargo.submit();
	}
	
	function volveratras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");


/*******************************************************************************************************************************************
*                                             REPORTE PARA LOS EMPLEADOS POR CARGO                                                         *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los empleados por oficio con una palabra clave                                             |
//AUTOR				          :Ing. Gustavo Alberto Avendaño Rivera.                                                                       |
//FECHA CREACION			  :JUNIO 23 DE 2011.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :23 de Junio de 2011.                                                                                         |
//TABLAS UTILIZADAS   :                                                                                                                    |
//nomina_000004       : Tabla de hoja de vida de nomina.                                                                                                |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");
$wactualiz="1.0 23-Junio-2011";

$empresa='root';

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
encabezado("EMPLEADOS POR CARGO EN LA CLINICA",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_porcargo.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($ofi) or $ofi=='') 
 {
  	
  echo "<form name='rep_porcargo' action='' method=post>";
  
  //Cuerpo de la pagina
  echo "<table align='center' border=0>";

  //Ingreso de fecha de consulta
  echo '<span class="subtituloPagina2">';
  echo 'Ingrese los parámetros de consulta';
  echo "</span>";
  echo "<br>";
  echo "<br>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Oficio a buscar o todos
  
  echo "<Tr>";
  echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Nombre del cargo a Buscar o * Todos <i><br></font></td>";
  echo "</Tr>";
   
 if (isset($ofi))
   {
    $ofi=$ofi;
   }
  else 
   {
    $ofi='';	
   }
  
  echo "<td bgcolor='#dddddd' aling=center><input type='TEXT' name='ofi' size=60 maxlength=50 id='ofi' value='".$ofi."' ></td>";
   
  echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>";  //submit osea el boton de Generar o Aceptar
  echo "</tr>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
  $ofic=explode('-',$ofi);
  
  //echo "ofic:",$ofic[0];
  
  echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>EMPLEADOS POR CARGO EN LA CLINICA</b></font></td>";
  echo "</tr>";
  echo "<tr><td><br></td></tr>";
  echo "</table>";
  echo "<br>";
	   
  echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
  echo "<tr>";
  echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>NOMBRE1_EMPLEADO</font></td>"; 
  echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>NOMBRE2_EMPLEADO</font></td>";
  echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>APELLIDO1_EMPLEADO</font></td>"; 
  echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>APELLIDO2_EMPLEADO</font></td>";
  echo "<td align=CENTER bgcolor=#006699 ><font size='2' text color='#FFFFFF'><b>CARGO</font></td>";
  echo "</tr>";
	  
  $query1 = " SELECT hvnom1,hvnom2,hvap1,hvap2,hvoficio "
           ."   FROM nomina_000004 "
           ."  WHERE hvoficio like '%$ofic[0]%' "
           ."    AND hvacti    = 'on'"
           ." ORDER BY 5,1,2,3,4";
           
  $err1 = mysql_query($query1,$conex) or die("Imposible  :: ".mysql_error());
  $num1 = mysql_num_rows($err1);
  //echo $query1."<br>";
   
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

    echo "<Tr bgcolor=".$wcf.">";
	echo "<td align=center><font size=1>$row1[0]</font></td>";
	echo "<td align=center><font size=1>$row1[1]</font></td>";
	echo "<td align=center><font size=1>$row1[2]</font></td>";
    echo "<td align=center><font size=1>$row1[3]</font></td>";
    echo "<td align=center><font size=1>$row1[4]</font></td>";
      
  }
    
 }
   echo "</tr>";
   
   echo "</table>";
   
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "</tr>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='volveratras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
 
}
?>
