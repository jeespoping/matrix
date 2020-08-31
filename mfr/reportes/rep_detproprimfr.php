<html>
<head>
<title>MATRIX - [REPORTE DETALLE DE PROCESOS PRIORITARIOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_detproprimfr.php'; 
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
//FECHA CREACION			  :MAYO 20 DE 2010.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  :20 de Mayo de 2010.                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//mfr_000001        : Tabla de Procesos prioritarios M.F.R.                                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 20-Mayo-2010";

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
 $empre1='mfr';
 

 //Forma
 echo "<form name='forma' action='rep_detproprimfr.php' method='post'>";
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
           ."  WHERE medico LIKE 'mfr'" 
           ."    AND codigo LIKE '001'"
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
  	
   $query = " SELECT pphist,pping,ppfecha,ppperseva,ppcargoe,ppproc,ppcrite1,ppcrite2,ppcrite3,ppcrite4,ppcrite5,ppcrite6,ppcrite7,ppcrite8,ppcrite9,ppcrite10,ppcrite11,ppcrite12,ppcrite13,ppcrite14,ppcrite15,ppcrite16,ppcrite17,ppcrite18,ppobserva,pppor"
           ."   FROM ".$empre1."_000001"
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
      
      IF ($row2[6]=='on')
      {
      	$row2[6]='';
      } 
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
	  
      
      switch ($tp1[0])
      {
      	case '01':
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
       	break;
      	
      	case '02':
         $row2[19]='';
      	 $row2[20]='';	
      	 $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
      	break;	
      	      	
      	case '03':
      	 $row2[20]='';	
      	 $row2[21]='';
      	 $row2[22]='';	
         $row2[23]='';
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
      echo "</tr>";
      
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[6]</b></font></td>";
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
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>OBSERVACIONES:</b></font></td>"; 
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=left width=100%><font size=1>&nbsp;$row2[24]</font></td>";
      echo "</tr >"; 
      echo "</table>";
      
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>PORCENTAJE: </b></font></td>"; 
      echo "<td  bgcolor=#FFFFFF align=left width=65%><font size=2 color=#FF0000>&nbsp;<b>".number_format($row2[25])."%</b></font></td>";
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