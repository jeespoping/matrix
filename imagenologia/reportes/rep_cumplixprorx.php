<html>
<head>
<title>MATRIX - [REPORTE PROCESOS PRIORITARIOS]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_cumplixprorx.submit();
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
*                                             REPORTE PROCESOS PRIORITARIOS %CUMPLIMIENTO                                                  *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver promedio de cumplimiento por criterio deacurdo al proceso prioritario                      |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 22 DE 2011.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : AGOSTO 22 DE 2011.                                                                                          |
//DESCRIPCION			      : Este reporte sirve para observar por proceso prioritarios cumplimiento por criterio                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//rayosx_000001     : Tabla de Procesos Prioritarios.                                                                                       |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 22-Agosto-2011";

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
encabezado("Cumplimiento x Proceso Prioritario",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='rayosx';

 

 


 //Forma
 echo "<form name='forma' action='rep_cumplixprorx.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($pp) or $pp=='-' or !isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_cumplixprorx' action='' method=post>";
  
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
 	
   /////////////////////////////////////////////////////////////////////////// seleccion para los procesos prioritarios
   echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Proceso Prioritario:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'rayosx'" 
           ."    AND codigo LIKE '02'"
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
    
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
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
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PROCESO PRIORITARIO</b></font></td>";
   echo "</tr>";
   echo "<tr>";   
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366>$tpp</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
  $query = " SELECT ppproc,ppcrite1,ppcrite2,ppcrite3,ppcrite4,ppcrite5,ppcrite6,ppcrite7,ppcrite8,ppcrite9,ppcrite10,ppcrite11,ppcrite12,ppcrite13,ppcrite14,ppcrite15,ppcrite16,ppcrite17,ppcrite18,ppcrite19,ppcrite20,ppcrite21"
           ."   FROM ".$empre1."_000002"
           ."  WHERE ppproc = '".$tpp."'" 
           ."    AND ppfecha between '".$fec1."' and '".$fec2."'"
           ."  ORDER BY ppproc";
   
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
   
    $arrecrit=Array();
   
    for ($j=0; $j <=23; $j++)
    {
     $arrecrit[$j]=0;
    }
   
    for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);

      IF ($row2[1]=='on')
      {
      	$arrecrit[0]=$arrecrit[0]+1;
      } 	
      IF ($row2[2]=='on')
      {
      	$arrecrit[1]=$arrecrit[1]+1;
      } 	
      IF ($row2[3]=='on')
      {
      	$arrecrit[2]=$arrecrit[2]+1;
      }
	  IF ($row2[4]=='on')
      {
      	$arrecrit[3]=$arrecrit[3]+1;
      }
	  IF ($row2[5]=='on')
      {
      	$arrecrit[4]=$arrecrit[4]+1;
      }
	  IF ($row2[6]=='on')
      {
      	$arrecrit[5]=$arrecrit[5]+1;
      }
	  IF ($row2[7]=='on')
      {
      	$arrecrit[6]=$arrecrit[6]+1;
      }
	  IF ($row2[8]=='on')
      {
      	$arrecrit[7]=$arrecrit[7]+1;
      }
      IF ($row2[9]=='on')
      {
      	$arrecrit[8]=$arrecrit[8]+1;
      }
	  IF ($row2[10]=='on')
      {
      	$arrecrit[9]=$arrecrit[9]+1;
      }
	  IF ($row2[11]=='on')
      {
      	$arrecrit[10]=$arrecrit[10]+1;
      }
	  IF ($row2[12]=='on')
      {
      	$arrecrit[11]=$arrecrit[11]+1;
      }
	  IF ($row2[13]=='on')
      {
      	$arrecrit[12]=$arrecrit[12]+1;
      }
	  IF ($row2[14]=='on')
      {
      	$arrecrit[13]=$arrecrit[13]+1;
      }
	  IF ($row2[15]=='on')
      {
      	$arrecrit[14]=$arrecrit[14]+1;
      }
	  IF ($row2[16]=='on')
      {
      	$arrecrit[15]=$arrecrit[15]+1;
      }
	  IF ($row2[17]=='on')
      {
      	$arrecrit[16]=$arrecrit[16]+1;
      }
	  IF ($row2[18]=='on')
      {
      	$arrecrit[17]=$arrecrit[17]+1;
      }
	  IF ($row2[19]=='on')
      {
      	$arrecrit[18]=$arrecrit[18]+1;
      }
	  IF ($row2[20]=='on')
      {
      	$arrecrit[19]=$arrecrit[19]+1;
      }
	  IF ($row2[21]=='on')
      {
      	$arrecrit[20]=$arrecrit[20]+1;
      }
	  
        
  } // cierre del for
      
      echo "<table border=1 cellpadding='0' cellspacing='0' size='602'>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=2><font text color=#000000 size=1></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR1</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR2</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR3</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR4</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR5</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR6</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR7</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR8</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR9</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR10</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR11</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR12</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR13</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR14</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR15</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR16</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR17</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR18</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR19</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR20</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR21</td>";
      echo "</tr>";
      
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>CUMPLIMIENTO</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[0]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[1]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[2]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[3]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[4]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[5]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[6]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[7]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[8]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[9]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[10]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[11]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[12]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[13]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[14]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[15]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[16]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[17]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[18]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[19]/$num1)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[20]/$num1)*100)."%</b></font></td>";
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF width=45%><font size=1 color=#000000><b>TOTAL DEL PROCESO: </b></font></td>"; 
      echo "<td align=left bgcolor=#FFFFFF width=65%><font size=2 color=#000000>&nbsp;<b>$num1</b></font></td>";
      echo "</tr >"; 
      echo "</table>";
      
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>