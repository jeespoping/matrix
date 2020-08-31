<html>
<head>
<title>MATRIX - [REPORTE HIJOS DE EMPLEADOS MENORES DE 12 AÑOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_nomhijosm12.php'; 
	}
	
	function enter()
	{
		document.forms.rep_nomhijosm12.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
	
	
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE HIJOS DE EMPLEADOS MENORES DE 12 AÑOS                                                *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver las estadisticas de las caidas en sistemas.                                                 |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JUNIO 22 DE 2011.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :22 de JUNIO de 2011.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//NOMINA_000004    : Tabla de Hoja de Vida.                                                                                                 |
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
encabezado("Hijos de Empleados Menores a 12 Años",$wactualiz,"clinica");

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
 echo "<form name='rep_nomhijosm12' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
 echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
 echo "<tr>";
 echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>HIJOS DE EMPLEADOS MENORES DE 12 AÑOS</b></font></td>";
 echo "</tr>";
 echo "</table>";
  	
 //Inicializo las variables
   $totevento=0;
   $totdiascum=0;
   $totupp=0;
   $totpupp=0;
   $totliii=0;
   $totliv=0;
   
 //TOTAL REPORTADOS
   $query1 = " SELECT hvcodnom,hvnom1,hvnom2,hvap1,hvap2,hinombre,hifecnac "
            ."   FROM nomina_000007,nomina_000004 "
            ."  WHERE hiempresa = hvempresa "
            ."    AND hicedula  = hvcedu    "
            ."    AND hicodigo  = hvcodnom  "
            ."    AND hvacti    = 'on'"
            ." ORDER BY 1,2,3,4,5";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   //echo $query."<br>";

   // Acá la tabla para la impresión
   echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "<tr>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>NOMBRE1_EMPLEADO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>NOMBRE2_EMPLEADO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>APELLIDO1_EMPLEADO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>APELLIDO2_EMPLEADO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>NOMBRES DE HIJO(A)</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>EDAD HIJO</b></td>";
   echo "</tr>";
   
   $wcfa="CCFFFF";
     
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

    // Fecha actual
    $dia=date("d");
    $mes=date("m");
    $ano=date("Y");
 
    // Fecha de nacimiento
    
    $dianaz=SUBSTR(".$row1[6].",9,2);
    $mesnaz=SUBSTR(".$row1[6].",6,2);
    $anonaz=SUBSTR(".$row1[6].",1,4);
 
    //si el mes es el mismo pero el día inferior aun no ha cumplido años, le quitaremos un año al actual
 
    if (($mesnaz == $mes) && ($dianaz > $dia)) 
    {
      $ano=($ano-1); 
    }
 
    //si el mes es superior al actual tampoco habrá cumplido años, por eso le quitamos un año al actual
 
    if ($mesnaz > $mes) 
    {
     $ano=($ano-1);
    }
 
    //ya no habría mas condiciones, ahora simplemente restamos los años y mostramos el resultado como su edad
 
    $edad=($ano-$anonaz);
    
    IF ($edad<=12)
    {
     IF ($wcfa==$wcf)
     {
       if ($wcf=="CCFFFF")
       {
       	$wcf="DDDDDD";
       }
       else
       {
       	$wcf="CCFFFF";	 	
       }
     }
     echo "<Tr bgcolor=".$wcf.">";
	 echo "<td align=center><font size=1>$row1[1]</font></td>";
	 echo "<td align=center><font size=1>$row1[2]</font></td>";
	 echo "<td align=center><font size=1>$row1[3]</font></td>";
     echo "<td align=center><font size=1>$row1[4]</font></td>";
     echo "<td align=center><font size=1>$row1[5]</font></td>";
     echo "<td align=center><font size=1>$edad</font></td>";
    
     $wcfa=$wcf;	
    }
    
   }
   echo "</tr>";
   
   echo "</table>";
   
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "</tr>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
 
}

?>