<html>
<head>
<title>MATRIX - [REPORTE PARA LOS INDICADORES MENSUALES]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_indicadores.php'; 
	}
	
	function enter()
	{
		document.forms.rep_indicadores.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");


/*******************************************************************************************************************************************
*                                             REPORTE PARA LOS INDICADORES MENSUALES	                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver llos indicadores mensuales.                                                        |
//AUTOR				          :Ing. Juan David Londoño.                                                                        |
//FECHA CREACION			  :ABRIL 17 DE 2011.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :17 de Marzo de 2011.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//".$empresa."_000009      : Tabla de Citas.
//".$empresa."_000002      : Tabla de Empresas.                                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 17-Marzo-2011";

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
encabezado("INFORME DE INDICADORES",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_indicadores.php?empresa=".$empresa."&wemp_pmla=".$wemp_pmla."' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_indicadores' action='' method=post>";
  
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
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";
 	
   /////////////////////////////////////////////////////////////////////////// seleccion para el medico
   echo "<td class='fila2'colspan=2 width=190 >Medico:<select name='me' id='searchinput'</td>"; 
  	
   $query = " SELECT Codigo, Descripcion "
           ."   FROM ".$empresa."_000010 "
           ."  WHERE Activo='A'" 
           ."  group by 1";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tme=$me;
   
   echo "<option></option>";
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."-".$row3[1]."</option>";
	}
   echo "<option>*-INDICADOR</option>";
   echo "</select></td>";

 	echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>"; //submit osea el boton de OK o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
   
    echo "<input type='hidden' name='empresa' value='".$empresa."'>";
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
   // medico
   $tme=$me;
   $me1=explode('-',$me);
   
   if ($me=='*-INDICADOR')
   {
   	   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>INFORME DE INDICADORES</b></font></td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
	   echo "</tr>";
	   echo "<tr><td><br></td></tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>INDICADOR DE OPORTUNIDAD</i></b></font></td>";
	   echo "</tr>";
	   echo "</table>";
	   echo "<br>";
	   
	  
	   $query = " select (DATEDIFF(".$empresa."_000008.Fecha,".$empresa."_000008.Fecha_DATA)) "
	           ."   FROM ".$empresa."_000008, ".$empresa."_000009 "
	           ."  WHERE ".$empresa."_000008.Fecha between '".$fec1."' and '".$fec2."'" 
	           ."    AND ".$empresa."_000008.fecha=".$empresa."_000009.fecha"
	           ."    AND cedula=identificacion"
	           ."    and asistida = 'on'";
	           
	   $err = mysql_query($query,$conex);
	   $num = mysql_num_rows($err);
	   
	   $totd=0;
	   $totc=0;
	   
	   for ($i=1;$i<=$num;$i++)
	   	{
	   	
		   	$row = mysql_fetch_array($err);
		   //echo mysql_errno() ."=". mysql_error();
		   if ($row[0]<10)
		   {
			   	$totd=$totd+$row[0];
			    $totc=$totc+1;
		   }
		   
		 }
	   	   echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
		   echo "<tr>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>NUMERO DE DIAS</font></td>"; 
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$totd."</font></td>"; 
		   echo "</tr>";
		   echo "<tr>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>CANTIDAD DE CITAS</font></td>"; 
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$totc."</font></td>"; 
		   echo "</tr>";
		   $ind=($totd/$totc);
		   echo "<tr>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>INDICADOR</font></td>"; 
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($ind,2,'.',',')."</font></td>"; 
		   echo "</tr>";	
	  
   }
   else
   {
   	
   
	   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>INFORME DE INDICADORES</b></font></td>";
	   echo "</tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
	   echo "</tr>";
	   echo "<tr><td><br></td></tr>";
	   echo "<tr>";
	   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>MEDICO: <i>".$me."</i></b></font></td>";
	   echo "</tr>";
	   echo "</table>";
		
	   $query = " SELECT Codigo, descripcion "
	           ."   FROM ".$empresa."_000011 "
	           ."  group by 1";
	           
	   $err2 = mysql_query($query,$conex);
	   $num2 = mysql_num_rows($err2);
	   
	   for ($k=1;$k<=$num2;$k++)
	   	{
		   $row2 = mysql_fetch_array($err2);
	   		//query para traer las citas
	   		$query = " SELECT Descripcion, count(*)  "
		           ."   FROM ".$empresa."_000009, ".$empresa."_000002 "
		           ."  WHERE Fecha between '".$fec1."' and '".$fec2."'"
		           ."    AND Cod_equ='".$me1[0]."'"
		           ."    AND Cod_exa='".$row2[0]."'"
		           ."    AND Asistida='on'"
		           ."    AND Nit=Nit_res"
		           ."  GROUP BY 1"
		           ."  ORDER BY 1";
		   $err = mysql_query($query,$conex);
		   $num = mysql_num_rows($err);
		//echo mysql_errno() ."=". mysql_error();
		   //echo $query;
		   if ($num>0)
		   {
		   echo "<br>";
		   echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
		   echo "<tr>";
		   echo "<td align=CENTER colspan=2 bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$row2[0]."-".$row2[1]."</b></font></td>";
		   echo "</tr>";
		   echo "<tr>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>ENTIDAD</font></td>"; 
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>CANTIDAD</font></td>"; 
		   echo "</tr>";   
		   
		   $total=0;
		   
		   for ($j=1;$j<=$num;$j++)
		   	{
				$row = mysql_fetch_array($err);
					//echo $row[0]."-".$row[1]; 
					//echo $querye;
				 
		      //echo "<td align=LEFT bgcolor=#FFFFFF width=100><font size='2' text color='#003366'><b>$me</b></font></td>";  
		      echo "<tr>";  
			  echo "<td align=LEFT bgcolor=#FFFFFF ><font size=1 text color='#003366'>$row[0]</font></td>";
		      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$row[1]</font></td>";
		      echo "</tr>";
		      $total=$total+$row[1];
		   	
		   	}
		   	echo "<tr>";  
		    echo "<td align=center bgcolor=#FFFFFF ><font size=2 text color='#003366'>TOTAL</font></td>";
		    echo "<td align=center bgcolor=#FFFFFF ><font size=2 text color='#003366'>$total</font></td>";
		    echo "</tr>"; 
		   }
		 }// cierre del else donde empieza la impresión
   	}
	 echo "<br>"; 
	 echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
	 echo "<br>";
	 echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
	 echo "</table>";
   } 
  
  
}

?>
