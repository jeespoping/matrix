<html>
<head>
<title>MATRIX - [REPORTE PACIENTES X MEDICO]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_pacxmedico.php'; 
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
*                                             REPORTE PACIENTES X MEDICO                                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver los pacientes x entidad                                                                    |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : Agosto 26 DE 2010.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : Agosto 26 DE 2010.                                                                                          |
//DESCRIPCION			      : Este reporte es para ver los pacientes x entidad.                                                           |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//neumo_000003      : Tabla de Registro de Atenci�n.                                                                                        |
//                                                                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 27-Agosto-2010";

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
encabezado("Pacientes x Medico",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_pacxmedico.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or !isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_pacxmedico' action='' method=post>";
  
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
 	
 	/////////////////////////////////////////////////////////////////////////// seleccion para los Medicos
    echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Medico:</B><br></font></b><select name='pp' id='searchinput'>";

    $query = " SELECT concat(subcodigo,'-',descripcion)"
            ."   FROM det_selecciones"
            ."  WHERE medico LIKE 'neumo'" 
            ."    AND codigo LIKE '003'"
            ."    AND activo =  'A'";
           
    $err3 = mysql_query($query,$conex);
    $num3 = mysql_num_rows($err3);
    $tpp=$pp;
   
    if ($codemp == '')
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
    echo "</select></td>";
 	
 	echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='OK'></td></tr>";          //submit osea el boton de OK o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
  	
   $tpp=$pp;
   $tp1=explode('-',$pp);
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PACIENTES x MEDICO</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
      
    echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>CENTRO DE COSTOS</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>INGRESO</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PACIENTE</b></td>";
    echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=2><b>PROCEDIMIENTO</b></td>";
    echo "</tr>";
    
    $query = " SELECT sicemd,sicecco,siceing,siceape1,siceap2,sicenom,siceproce"
            ."   FROM neumo_000003"
            ."  WHERE sicefec between '".$fec1."' and '".$fec2."'"
            ."    AND sicemd = '".$tpp."'" 
            ."  ORDER BY sicemd,sicecco,siceproce,siceing";
           
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
   
    //echo $query;
    //echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    
    $entiant='';
    $resant='';
    $proceant='';
    
    $totent=0;
	
	$wcfant='';
	
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
	   
   	IF ($swtitulo=='SI')
	 {
      $entiant = $row1[0];
      echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>MEDICO : </b></font></td><td align=center colspan=1><font color=#000000 text size=2><b>".$entiant."</b></td><td align=center colspan=1><font color=#000000 text size=2><b></b></td></tr>"; 
      $swtitulo='NO';
 	   
	 }
	    	  
	IF ($entiant==$row1[0] )
	 {
	  $paci=$row1[3]." ".$row1[4]." ".$row1[5];	
	  echo "<Tr bgcolor=".$wcf.">";
	  echo "<td align=center><font size=1>$row1[1]</font></td>";
	  echo "<td align=center><font size=1>$row1[2]</font></td>";
	  echo "<td align=center><font size=1>$paci</font></td>";
      echo "<td align=center><font size=1>$row1[6]</font></td>";
	  $totent=$totent+1;
	 }
	ELSE 
	 {
	  echo "<tr>";
	  echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL MEDICO : </b></font></td>";
	  echo "<td align=center><font color=#000000 text size=2><b>".$entiant."</b></td>";
	  echo "<td align=center colspan=6><font color=#000000 text size=2><b>".$totent."</b></td>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "</tr>";
	  echo "<tr>";
	  echo "</tr>";
	  
	  $wcfant =$wcf;
	  $entiant=$row1[0];
	  $totent=0;
	  $totent =$totent+1;
	  
	  $paci=$row1[3]." ".$row1[4]." ".$row1[5];

	  echo "<tr>";
	  echo "</tr>";
	  echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>MEDICO : </b></font></td><td align=center colspan=1><font color=#000000 text size=2><b>".$entiant."</b></td><td align=center colspan=1><font color=#000000 text size=2><b></b></td></tr>"; 
      
	  echo "<Tr bgcolor=".$wcfant.">";
	  echo "<td align=center><font size=1>$row1[1]</font></td>";
	  echo "<td align=center><font size=1>$row1[2]</font></td>";
	  echo "<td align=center><font size=1>$paci</font></td>";

     }
	}

	echo "<tr>";
	echo "<td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL MEDICO : </b></font></td>";
	echo "<td align=center><font color=#000000 text size=2><b>".$entiant."</b></td>";
	echo "<td align=center colspan=6><font color=#000000 text size=2><b>".$totent."</b></td>";
	echo "</tr>";
	echo "</table>"; 

	
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atr�s' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresi�n

}
?>