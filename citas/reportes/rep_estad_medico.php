<html>
<head>
<title>MATRIX - [REPORTE PARA LA ESTADISTICA DE LOS MEDICOS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_estad_medico.php'; 
	}
	
	function enter()
	{
		document.forms.rep_estad_medico.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

function pintar($med,$cant)
{
	  
	  echo "<tr>";  
	  echo "<td align=LEFT bgcolor=#FFFFFF width=100><font size='2' text color='#003366'><b>$med[0]</b></font></td>";  
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[0]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[1]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[2]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[3]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[4]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[5]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[6]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[7]</font></td>";
      echo "<td align=center bgcolor=#FFFFFF ><font size=1 text color='#003366'>$cant[8]</font></td>";
      echo "</tr>";
      
}
/*******************************************************************************************************************************************
*                                             REPORTE PARA LA ESTADISTICA DE LOS MEDICOS	                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver la estadistica de los examenes por medico.                                                        |
//AUTOR				          :Ing. Juan David Londoño.                                                                        |
//FECHA CREACION			  :ABRIL 21 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :21 de Abril de 2010.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//citasfi_000009      : Tabla de Citas.                                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 21-Abril-2010";

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
encabezado("INFORME ESTADISTICO MEDICO",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_estad_medico.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($te) or $te=='' or!isset($me) or $me=='' or !isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_estad_medico' action='' method=post>";
  
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

   /////////////////////////////////////////////////////////////////////////// seleccion para el tipo de examenes
   echo "<td class='fila2'colspan=2 width=190 >Tipo de Examen:<select name='te' id='searchinput'</td>"; 
  	
   $query = " SELECT Codigo, Descripcion "
           ."   FROM ".$empresa."_000011 "
           ."  WHERE Activo='A'" 
           ."  group by 1";
           
   $err2 = mysql_query($query,$conex);
   $num2 = mysql_num_rows($err2);
   $tte=$te;
   
   echo "<option></option>";
   for ($i=1;$i<=$num2;$i++)
	{
	$row2 = mysql_fetch_array($err2);
	echo "<option>".$row2[0]."-".$row2[1]."</option>";
	}
    echo "<option>%-TODOS</option>";
	echo "</select></td></tr>";
   
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
   echo "<option>%-TODOS</option>";
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
   // tipo de examen
   $tte=$te;
   $te1=explode('-',$te);
   
   // medico
   $tme=$me;
   $me1=explode('-',$me);
   
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>INFORME ESTADISTICO MEDICO</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "<tr><td><br></td></tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>MEDICO: <i>".$me."</i>&nbsp&nbsp&nbspTIPO DE EXAMEN: <i>".$te."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
	
   //query para traer los examenes
   $query = " SELECT Codigo, Descripcion "
           ."   FROM ".$empresa."_000011"
           ."  WHERE Activo='A' "
           ."    group by 1, 2 "
           ."    order by 1 ";
   $erre = mysql_query($query,$conex);
   $nume = mysql_num_rows($erre);
   
   echo "<br>";
   echo "<br>";
   echo "<br>";
   echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
   echo "<tr>"; 
   echo "<td>&nbsp</td>";
   for ($j=1;$j<=$nume;$j++)
   	{
	$rowe = mysql_fetch_array($erre);	
	echo "<td align=center bgcolor=#FFFFFF width=150><font size='2' text color='#003366'><b>$rowe[1]</b></font></td>"; 
	 }
	echo "</tr>";
	
   $quer1 = "CREATE TEMPORARY TABLE if not exists tmp1 as "
           ."   select Codigo, Descripcion "
           ."  from ".$empresa."_000010 " 
           ."   group by 1, 2";
   $err1 = mysql_query($quer1,$conex);
   
   $query = " SELECT Descripcion, Cod_exa, count(*)"
           ."   FROM ".$empresa."_000009, tmp1 "
           ."  WHERE Cod_exa  like '".$te1[0]."'" 
           ."    AND Cod_equ like '".$me1[0]."'"
           ."    AND Fecha between '".$fec1."' and '".$fec2."'"
           ."    AND Asistida= 'on'"
           ."    AND Codigo=Cod_equ" 
           ."    group by 1, 2 ";
        
   $err = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err);
   //echo $num1;
   //echo $query."<br>";
   $sw=1;
   $mda='';
   
   $arremd=Array();
   $arrecant=Array();
   $arremd[0]='';
   for ($k=0;$k<=8;$k++)
   	{
      $arrecant[$k]=0;
   	}

	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err);	
	  
	  if ($sw==1)
	  {
	  	$arremd[0]=$row2[0];
	  	$sw=0;
	  	$mda=$row2[0];
	  }
	  
	  if ($mda==$row2[0])
	  {
	  	if ($row2[1]=='001')
	  	{
	  		$arrecant[0]=$row2[2];
	  	}
	  	else if($row2[1]=='002')
	  	{
	  		$arrecant[1]=$row2[2];
	  	}
	  	else if($row2[1]=='003')
	  	{
	  		$arrecant[2]=$row2[2];
	  	}
	  	else if($row2[1]=='004')
	  	{
	  		$arrecant[3]=$row2[2];
	  	}
	 	else if($row2[1]=='005')
	  	{
	  		$arrecant[4]=$row2[2];
	  	}
	  	else if($row2[1]=='006')
	  	{
	  		$arrecant[5]=$row2[2];
	  	}
	  	else if($row2[1]=='007')
	  	{
	  		$arrecant[6]=$row2[2];
	  	}
	 	else if($row2[1]=='008')
	  	{
	  		$arrecant[7]=$row2[2];
	  	}
	  else if($row2[1]=='009')
	  	{
	  		$arrecant[8]=$row2[2];
	  	}

	  }
	  else
	  {
	  	pintar($arremd, $arrecant);
	  	$arremd=Array();
   		$arrecant=Array();
   		$arremd[0]='';
   		for ($k=0;$k<=8;$k++)
	   	{
	      $arrecant[$k]=0;
	   	}
	   	
	   	$arremd[0]=$row2[0];
	   	$mda=$row2[0];
	   	
	  	if ($mda==$row2[0])
	    {
		  	if ($row2[1]=='001')
		  	{
		  		$arrecant[0]=$row2[2];
		  	}
		  	else if($row2[1]=='002')
		  	{
		  		$arrecant[1]=$row2[2];
		  	}
		  	else if($row2[1]=='003')
		  	{
		  		$arrecant[2]=$row2[2];
		  	}
		  	else if($row2[1]=='004')
		  	{
		  		$arrecant[3]=$row2[2];
		  	}
		 	else if($row2[1]=='005')
		  	{
		  		$arrecant[4]=$row2[2];
		  	}
		  	else if($row2[1]=='006')
		  	{
		  		$arrecant[5]=$row2[2];
		  	}
		  	else if($row2[1]=='007')
		  	{
		  		$arrecant[6]=$row2[2];
		  	}
		 	else if($row2[1]=='008')
		  	{
		  		$arrecant[7]=$row2[2];
		  	}
		    else if($row2[1]=='009')
		  	{
		  		$arrecant[8]=$row2[2];
		  	}
	    }
	   	
	   	
	  }

	 
  } // cierre del for*/
  
   pintar($arremd, $arrecant);
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<br>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>