<html>
<head>
   <title>Reporte Diario de Incosistencias</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function cerrarVentana()
	{
	 window.close()
	}

   function enter()
	{
		document.forms.rep_diaincons.submit();
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE DIARIO DE INCONSISTENCIAS		                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver las inconsistencias diarias de devoluciones.                                               |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 29 DE 2007.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : AGOSTO 30 DE 2007.                                                                                          |
//DESCRIPCION			      : Este reporte sirve para verificar las inconsistencias diarias en las devoluciones                           |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000051       : Tabla de Aplicaciones por Empresa.                                                                                    |
//usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
//movhos_000002     : Tabla de Encabezado de Cargos.                                                                                        |
//movhos_000003     : Tabla de Detalle de Cargos.                                                                                           |
//movhos_000018     : Tabla de Ubicación Pacientes.                                                                                         |
//movhos_000026     : Tabla de Maestros de Articulos.                                                                                       |
//movhos_000028     : Tabla de Devoluciones.                                                                                                |
//cenpro_000002     : Tabla de Maestro de Articulos.                                                                                        |
//==========================================================================================================================================
$wactualiz="Ver. 2007-08-29";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
	
 $empresa='root';

 

 

 include_once("root/comun.php");

 encabezado("Reporte de Inconsistencias Diarias - Enfermeria", $wactualiz, 'clinica');
 
/////////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos

$query = " SELECT Detapl,Detval"
	       ."   FROM ".$empresa."_000051"
	       ."  WHERE Detemp='".$wemp."'";
	 
$err = mysql_query($query,$conex);
$num = mysql_num_rows($err);
   
$empre1="";
$empre2="";

for ($i=1;$i<=$num;$i++)
 { 
  $row = mysql_fetch_array($err);
     
  IF ($row[0] == 'cenmez')
   {
    $empre1=$row[1];
   }	
  else 
   { 
    if ($row[0] == 'movhos') 
     {
      $empre2=$row[1];	
     }
   }     
 }

 
 if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
   echo "<form name='rep_diaincons' action='' method=post>";
   echo '<table align=center>';
   
   echo "<tr class=seccion1>";
   echo "<td align=center>Fecha Inicial<br>";
   campoFecha("fec1");
   echo "</td>";
   echo "<td align=center>Fecha Final<br>";
   campoFecha("fec2");
   echo "</td>";
   echo "</tr>";
   
 
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	
        echo "<center><table>";
	    echo "<tr class=seccion1>";
	    echo "<td align=center colspan=9><b>FECHA INICIAL: </b>".$fec1."&nbsp&nbsp&nbsp<b>FECHA FINAL: </b>".$fec2."</td>";
	    echo "</tr>";		
		echo "<tr class=encabezadoTabla>";
		echo "<td align=center>FECHA</td>";
		echo "<td align=center>HABITACION</td>";
		echo "<td align=center>HISTORIA</td>";
		echo "<td align=center>INGRESO</td>";
		echo "<td align=center>COD ARTICULO</td>";
		echo "<td align=center>NOMBRE ARTICULO</td>";
	    echo "<td align=center>CANTIDAD FALTANTE</td>";
	    echo "<td align=center>CAUSA DEL FALTANTE</td>";
	    echo "<td align=center>INCONSISTENCIA EN LA VERIFICACION</td>";
	    echo "</tr>";
	
			
		$query1 = " SELECT fenfec,ubihac,fenhis,fening,fdeart,artcom,devcfs,devjus,devcff"
                  ."  FROM ".$empre2."_000002,".$empre2."_000003,".$empre2."_000026,".$empre2."_000028,".$empre2."_000018"
                  ." WHERE fenfec between '".$fec1."' and '".$fec2."'" 
                  ."   AND fdenum = fennum"
                  ."   AND fdeart = artcod"
  				  ."   AND fdenum = devnum"
  				  ."   AND fdelin = devlin"
  				  ."   AND fenhis = ubihis"
  				  ."   AND fening = ubiing"
  				  ."   AND devcfs > 0 "
                  ." UNION "
       			 ." SELECT fenfec,ubihac,fenhis,fening,fdeart,artcom,devcfs,devjus,devcff"
                 ."   FROM ".$empre2."_000002,".$empre2."_000003,".$empre1."_000002,".$empre2."_000028,".$empre2."_000018"
				 ."  WHERE fenfec between '".$fec1."' and '".$fec2."'"
				 ."    AND fdenum = fennum"
                 ."    AND fdeart = artcod"
                 ."    AND fdenum = devnum"
                 ."    AND fdelin = devlin"
                 ."    AND fenhis = ubihis"
				 ."    AND fening = ubiing"
				 ."    AND devcfs > 0 "
				 /*********************************************************************************************************************/
				 /* Noviembre 07 de 2013 se agrega este union para traiga los datos de contingencia (tabla movhos_00143). Jonatan Lopez
				 /*********************************************************************************************************************/
				 ." UNION "
				 ." SELECT fenfec,ubihac,fenhis,fening,fdeart,artcom,devcfs,devjus,devcff"
                  ."  FROM ".$empre2."_000002,".$empre2."_000143,".$empre2."_000026,".$empre2."_000028,".$empre2."_000018"
                  ." WHERE fenfec between '".$fec1."' and '".$fec2."'" 
                  ."   AND fdenum = fennum"
                  ."   AND fdeart = artcod"
  				  ."   AND fdenum = devnum"
  				  ."   AND fdelin = devlin"
  				  ."   AND fenhis = ubihis"
  				  ."   AND fening = ubiing"
  				  ."   AND devcfs > 0 "
				  ."   AND Fdeest = 'on'"
                  ." UNION "
       			 ." SELECT fenfec,ubihac,fenhis,fening,fdeart,artcom,devcfs,devjus,devcff"
                 ."   FROM ".$empre2."_000002,".$empre2."_000143,".$empre1."_000002,".$empre2."_000028,".$empre2."_000018"
				 ."  WHERE fenfec between '".$fec1."' and '".$fec2."'"
				 ."    AND fdenum = fennum"
                 ."    AND fdeart = artcod"
                 ."    AND fdenum = devnum"
                 ."    AND fdelin = devlin"
                 ."    AND fenhis = ubihis"
				 ."    AND fening = ubiing"
				 ."    AND devcfs > 0 "
				 ."    AND Fdeest = 'on'"
				 ."  ORDER BY fenfec,ubihac,fenhis,fening,fdeart";
		
			//echo $query1."<br>"; 
				 
		$err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
		//echo mysql_errno() ."=". mysql_error();
			
			
	for ($i=1;$i<=$num1;$i++)
	 {
	  if (is_int ($i/2))
	   {
	   	$wclass="fila1";
	   }
	  else
	   {
	   	$wclass="fila2";
	   }

	   $row1 = mysql_fetch_array($err1);
	   
	  echo "<tr class=".$wclass.">";
	  echo "<td align=center>".$row1[0]."</td>";
	  echo "<td align=center>".$row1[1]."</td>";
	  echo "<td align=center>".$row1[2]."</td>";
	  echo "<td align=center>".$row1[3]."</td>";
	  echo "<td align=center>".$row1[4]."</td>";
	  echo "<td align=center>".$row1[5]."</td>";
	  echo "<td align=center>".$row1[6]."</td>";
	  echo "<td align=center>".$row1[7]."</td>";
	  echo "<td align=center>".$row1[8]."</td>";
	  echo "</tr>"; 
	}
		
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
  echo "<table align=center>"; 
  echo "<tr><td align=center colspan=8><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
}
?>
