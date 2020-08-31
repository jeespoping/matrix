<html>
<head>
<title>MATRIX - [REPORTE EMPLEADOS QUE HABLAN UNA SEGUNDA LENGUA EXTRANJERA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_nomseglengua.php'; 
	}
	
	function enter()
	{
		document.forms.rep_nomseglengua.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
	
	
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE EMPLEADOS QUE HABLAS SEGUNDA LENGUA                                                  *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los empleados que hablan una segunda lengua.                                                |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JUNIO 22 DE 2011.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :22 de JUNIO de 2011.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//nomina_000004     : Tabla de hoja de vida en nomina.                                                                                      |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 22-Junio-2011";

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
encabezado("Empleados Que Hablan Una Segunda Lengua",$wactualiz,"clinica");

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
 echo "<form name='rep_nomseglengua' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>EMPLEADOS QUE HABLAN UNA SEGUNDA LENGUA EXTRANJERA</b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   //Inicializo las variables
   $totevento=0;
   $totdiascum=0;
   $totupp=0;
   $totpupp=0;
   $totliii=0;
   $totliv=0;
   
   //TOTAL EMPLEADOS CON SEGUNDA LENGUA HABLADA
   $query1 = " SELECT hvcodnom,hvnom1,hvnom2,hvap1,hvap2,hvoficio,hvcualseglengua"
            ."   FROM nomina_000004"
            ."  WHERE hvseglengua = '1-SI'"
            ."    AND hvseghabla  = 'on' "
            ."  ORDER BY 6,1,2";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   //echo $query."<br>";

   // Acá la tabla para la impresión
   echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "<tr>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>CODIGO NOMINA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PRIMER NOMBRE</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>SEGUNDO NOMBRE</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PRIMER APELLIDO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>SEGUNDO APELLIDO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>OFICIO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>CUALES LENGUAS EXTRANJERAS</b></td>";
   echo "</tr>";
   
   
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
     echo "<td align=center><font size=1>$row1[5]</font></td>";
     echo "<td align=center><font size=1>$row1[6]</font></td>";
   }
   echo "</tr>";
   echo "</table>";
   
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "</tr>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
}
?>