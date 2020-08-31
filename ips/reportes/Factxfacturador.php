<html>
<head>
<title>MATRIX - [REPORTE DE FACTURAS POR FACTURADOR]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='Factxfacturador.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_pacxmedico.submit();
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
*                                             REPORTE DE FACTURAS POR FACTURADOR                                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : RREPORTE DE FACTURAS POR FACTURADOR                                                                    |
//AUTOR				          : Ing. Juan David Londoño.                                                                       |
//FECHA CREACION			  : Septiembre 07 DE 2010.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : Septiembre 07 DE 2010.                                                                                          |
//DESCRIPCION			      : Este reporte es para ver las facturas hechas por los facturadores entre fechas.                                                           |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//neumo_000003      : Tabla de Registro de Atención.                                                                                        |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 07-Septiembre-2010";

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
encabezado("Cantidad de Facturas Activas Hechas por Facturador",$wactualiz,"clinica");

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
 echo "<form name='forma' action='Factxfacturador.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 echo "<input type='HIDDEN' NAME= 'empresa' value='".$empresa."'>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='Factxfacturador' action='' method=post>";
  
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
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>CANTIDAD DE FACTURAS ACTIVAS x FACTURADOR</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";

    echo "<br>";
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbspCODIGO DEL FACTURADOR&nbsp</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbspNOMBRE DEL FACTURADOR&nbsp</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>&nbspCANTIDAD DE FACTURAS&nbsp</b></td>";
    echo "</tr>";
    
    $query = " SELECT count(*), seguridad "
            ."   FROM ".$empresa."_000018"
            ."  WHERE Fenfec between '".$fec1."' and '".$fec2."'"   
            ."  AND Fenest='on'" 
            ."  GROUP BY seguridad";
           
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
   
    //echo $query;
    //echo mysql_errno() ."=". mysql_error();

	for ($i=1;$i<=$num;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

    $row = mysql_fetch_array($err);
	$cod=explode('-',$row[1]);
    
   	  echo "<Tr bgcolor=".$wcf.">";
	  echo "<td align=left><font size=2>$cod[1]</font></td>";
	  
	  $query = " SELECT descripcion "
            ."   FROM usuarios "
            ."  WHERE codigo= '".$cod[1]."'";
	    $err1 = mysql_query($query,$conex);
	    $row1 = mysql_fetch_array($err1);
	    
	  echo "<td align=left><font size=2>$row1[0]</font></td>";
	  echo "<td align=left><font size=2>$row[0]</font></td>";
	  
	  	
	
	
     }
	}
	echo "</tr>";
	echo "</table>"; 

	
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión


?>