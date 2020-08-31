<html>
<head>
<title>MATRIX - [REPORTE DETALLE DE PROCESOS PRIORITARIOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_dedtpropri.php'; 
	}
	
	function enter()
	{
		document.forms.rep_detpropri.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DETALLE DE PROCESOS PRIORITARIOS	                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los procesos prioritarios < al 100%.                                                        |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :MARZO 24 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :24 de Marzo de 2010.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//pamec_000001      : Tabla de Procesos prioritarios U.C.I.                                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 24-Marzo-2010";

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
encabezado("Detalle de Proceso Prioritario",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 $empresa='pamec';
 

 //Forma
 echo "<form name='forma' action='rep_detpropri.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($pp) or $pp=='-' or !isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_detpropri' action='' method=post>";
  
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
           ."  WHERE medico LIKE 'pamec'" 
           ."    AND codigo LIKE '002'"
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
  	
   $query = " SELECT pphist,pping,ppfecha,ppperseva,ppcargoe,ppcubi,ppproc,ppcrite1,ppcrite2,ppcrite3,ppcrite4,ppcrite5,ppcrite6,ppcrite7,ppcrite8,ppcrite9,ppcrite10,ppcrite11,ppcrite12,ppcrite13,ppcrite14,ppcrite15,ppcrite16,ppcrite17,ppcrite18,ppcrite19,ppcrite20,ppcrite21,ppcrite22,ppcrite23,ppcrite24,ppcrite25,ppobserva,pppor"
           ."   FROM pamec_000001"
           ."  WHERE ppproc = '".$tpp."'" 
           ."    AND pppor < 100"
           ."    AND ppfecha between '".$fec1."' and '".$fec2."'"
           ."  ORDER BY ppfecha,ppperseva,pphist,pping";
           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query."<br>";

	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);

	  echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF width=50><font size='1' color='#000000'><b>HISTORIA:</b></font></td>";  
      echo "<td  bgcolor=#FFFFFF width=100><font size=1>&nbsp;$row2[0]</font></td>";
      echo "<td align=LEFT bgcolor=#FFFFFF width=50><font size='1' color='#000000'><b>INGRESO:</b></font></td>"; 
      echo "<td  bgcolor=#FFFFFF align=center width=100><font size=1>&nbsp;$row2[1]</font></td>";
      echo "<td align=LEFT bgcolor=#FFFFFF width=120><font size='1' color='#000000'><b>FECHA EVALUACION:</b></font></td>"; 
      echo "<td  bgcolor=#FFFFFF align=center width=70><font size=1>&nbsp;$row2[2]</font></td>";
      echo "</tr>";
      echo "</table>";

      echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>PERSONA EVALUADA:</b></font></td>";  
      echo "<td  bgcolor=#FFFFFF ><font size=1>&nbsp;$row2[3]</font></td>";
      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='1' color='#000000'><b>CARGO PERSONA EVALUADA:</b></font></td>";  
      echo "<td  bgcolor=#FFFFFF align=center ><font size=1>&nbsp;$row2[4]</font></td>";
      echo "</tr>";
      echo "</table>";
      
      echo "<table border=0 size=80%>";
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF ><font size=1 color='#000000'><b>CUBICULO: </b></font></td>";
      echo "<td  bgcolor=#FFFFFF ><font size=1><b>&nbsp;$row2[5]</b></font></td>";
      echo "</tr>";
      echo "</table>";
	  
      IF ($row2[7]=='on')
      {
      	$row2[7]='';
      } 	
	  IF ($row2[8]=='on')
      {
      	$row2[8]='';
      }
	  IF ($row2[9]=='on')
      {
      	$row2[9]='';
      }
	  IF ($row2[10]=='on')
      {
      	$row2[10]='';
      }
	  IF ($row2[11]=='on')
      {
      	$row2[11]='';
      }
	  IF ($row2[12]=='on')
      {
      	$row2[12]='';
      }
	  IF ($row2[13]=='on')
      {
      	$row2[13]='';
      }
      IF ($row2[14]=='on')
      {
      	$row2[14]='';
      }
	  IF ($row2[15]=='on')
      {
      	$row2[15]='';
      }
	  IF ($row2[16]=='on')
      {
      	$row2[16]='';
      }
	  IF ($row2[17]=='on')
      {
      	$row2[17]='';
      }
	  IF ($row2[18]=='on')
      {
      	$row2[18]='';
      }
	  IF ($row2[19]=='on')
      {
      	$row2[19]='';
      }
	  IF ($row2[20]=='on')
      {
      	$row2[20]='';
      }
	  IF ($row2[21]=='on')
      {
      	$row2[21]='';
      }
	  IF ($row2[22]=='on')
      {
      	$row2[22]='';
      }
	  IF ($row2[23]=='on')
      {
      	$row2[23]='';
      }
	  IF ($row2[24]=='on')
      {
      	$row2[24]='';
      }
	  IF ($row2[25]=='on')
      {
      	$row2[25]='';
      }
	  IF ($row2[26]=='on')
      {
      	$row2[26]='';
      }
	  IF ($row2[27]=='on')
      {
      	$row2[27]='';
      }
	  IF ($row2[28]=='on')
      {
      	$row2[28]='';
      }
	  IF ($row2[29]=='on')
      {
      	$row2[29]='';
      }
	  IF ($row2[30]=='on')
      {
      	$row2[30]='';
      }
	  IF ($row2[31]=='on')
      {
      	$row2[31]='';
      }      
      
      switch ($tp1[0])
      {
      	case '01':
      	 $row2[30]='';
      	 $row2[31]='';
      	break;
      	case '03':
      	 $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';	
      	break;	 
      	case '04':
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';		
      	break;	
      	case '05':
      	 $row2[18]='';
      	 $row2[19]='';	
         $row2[20]='';
         $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';	
      	break;	
      	case '06':
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';	
      	break;	
      	case '07':
      	 $row2[13]='';
      	 $row2[14]='';	
         $row2[15]='';
         $row2[16]='';
      	 $row2[17]='';	
      	 $row2[18]='';
      	 $row2[19]='';	
         $row2[20]='';
         $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';		
      	break;	
      	case '08':
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';	
      	break;	
      	case '09':
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';	      	
      	break;	
      	case '10':
      	 $row2[19]='';	
         $row2[20]='';
         $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';
      	break;	
      	case '11':
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';
      	break;	
      	case '12':
      	 $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';
      	break;	
      	case '13':
      	 $row2[16]='';
      	 $row2[17]='';	
      	 $row2[18]='';
      	 $row2[19]='';	
         $row2[20]='';
         $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';	
      	break;	
      	case '14':
      	 $row2[18]='';
      	 $row2[19]='';	
         $row2[20]='';
         $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';
      	break;	
      	case '15':
      	 $row2[20]='';	
         $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	 $row2[24]='';
      	 $row2[25]='';	
         $row2[26]='';
      	 $row2[27]='';
      	 $row2[28]='';
      	 $row2[29]='';	
         $row2[30]='';
      	 $row2[31]='';
      	break;
      }	 
      
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
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR22</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR23</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR24</td>";
      echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR25</td>";
      echo "</tr>";
      
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[7]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[8]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[9]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[10]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[11]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[12]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[13]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[14]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[15]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[16]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[17]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[18]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[19]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[20]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[21]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[22]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[23]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[24]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[25]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[26]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[27]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[28]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[29]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[30]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[31]</b></font></td>";
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>OBSERVACIONES:</b></font></td>"; 
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=left width=100%><font size=1>&nbsp;$row2[32]</font></td>";
      echo "</tr >"; 
      echo "</table>";
      
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>PORCENTAJE: </b></font></td>"; 
      echo "<td  bgcolor=#FFFFFF align=left width=65%><font size=2 color=#FF0000>&nbsp;<b>".number_format($row2[33])."%</b></font></td>";
      echo "</tr >"; 
      echo "</table>";
      
      echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<tr><td align=LEFT bgcolor=#FFFFFF><font size='1' color='#0000FF'><b>------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</b></font></tr>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "</Tr >"; 
      echo "</table>";
	
  } // cierre del for
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>