<html>
<head>
<title>MATRIX - [REPORTE SEGUIMIENTO TROMBOPROFILAXIS]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_segtrombopro.submit();
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
*                                             REPORTE SEGUIMIENTO TROMBOPROFILAXIS                                                         *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte estadistico por edades del seguimiento a la base de datos de tromboprofilaxis                       |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : ENERO 17 DE 2013.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : FEBRERO 12 DE 2013.                                                                                         |
//                            : Piden que también se tenag en cuenta cuantos pacientes por especialidad.                                    |
//DESCRIPCION			      : Este reporte sirve para observar por rango de edades los pacientes de tromboprofilaxis                      |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cuiint_000001      : Tabla de tromboprofilaxis.                                                                                           |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 12-Febrero-2013";

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
encabezado("Seguimiento a tromboprofilaxis x Edad",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='cuiint';

 

 


 //Forma
 echo "<form name='forma' action='rep_segtrombopro.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_segtrombopro' action='' method=post>";
  
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

   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
     
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>TROMBOPROFILAXIS</b></font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
  $query = " SELECT troedad"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
           ."  ORDER BY troedad";
   
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
	
	
  $query1 = " SELECT trosex,troedad"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
           ."  ORDER BY trosex,troedad";
   
    $err2 = mysql_query($query1,$conex);
    $num2 = mysql_num_rows($err2);
   
  // query para saber por entidad la cantidad de usuarios atendidos 
  $query3 = " SELECT troentidad,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
           ."  GROUP BY 1"
		   ."  ORDER BY troentidad";
   
    $err3 = mysql_query($query3,$conex);
    $num3 = mysql_num_rows($err3);
   
   // query para saber por servicio de atención la cantidad de usuarios atendidos
   
   $query4 = " SELECT troserhosp,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
           ."  GROUP BY 1"
		   ."  ORDER BY troserhosp";
   
    $err4 = mysql_query($query4,$conex);
    $num4 = mysql_num_rows($err4);
   
   // query para saber por diagnostico la cantidad de usuarios atendidos
    $query5 = " SELECT trodiag1,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodiag1 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodiag1";
   
    $err5 = mysql_query($query5,$conex);
    $num5 = mysql_num_rows($err5);
   
   // query para saber por diagnostico la cantidad de usuarios atendidos
    $query6 = " SELECT trodiag2,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodiag2 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodiag2";
   
    $err6 = mysql_query($query6,$conex);
    $num6 = mysql_num_rows($err6);
   
   // query para saber por diagnostico la cantidad de usuarios atendidos
    $query7 = " SELECT trodiag3,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodiag3 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodiag3";
   
    $err7 = mysql_query($query7,$conex);
    $num7 = mysql_num_rows($err7);
   
   // query para saber por diagnostico la cantidad de usuarios atendidos
   $query8 = " SELECT trodiag4,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodiag4 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodiag4";
   
    $err8 = mysql_query($query8,$conex);
    $num8 = mysql_num_rows($err8);
   
   // query para saber por diagnostico la cantidad de usuarios atendidos
    $query9 = " SELECT trodiag5,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodiag5 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodiag5";
   
    $err9 = mysql_query($query9,$conex);
    $num9 = mysql_num_rows($err9);
   
   // query para saber por antecedente patológico la cantidad de usuarios atendidos
    $query10 = " SELECT troantpat1,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND troantpat1 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY troantpat1";
   
    $err10 = mysql_query($query10,$conex);
    $num10 = mysql_num_rows($err10);
   
   // query para saber por antecedente patológico la cantidad de usuarios atendidos
    $query11 = " SELECT troantpat2,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND troantpat2 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY troantpat2";
   
    $err11 = mysql_query($query11,$conex);
    $num11 = mysql_num_rows($err11);
   
   // query para saber por antecedente patológico la cantidad de usuarios atendidos
    $query12 = " SELECT troantpat3,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND troantpat3 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY troantpat3";
   
    $err12 = mysql_query($query12,$conex);
    $num12 = mysql_num_rows($err12);
   
   
   // query para saber por antecedente patológico la cantidad de usuarios atendidos
    $query13 = " SELECT troantpat4,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND troantpat4 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY troantpat4";
   
    $err13 = mysql_query($query13,$conex);
    $num13 = mysql_num_rows($err13);
   
   
   // query para saber por antecedente patológico la cantidad de usuarios atendidos
    $query14 = " SELECT troantpat5,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND troantpat5 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY troantpat5";
   
    $err14 = mysql_query($query14,$conex);
    $num14 = mysql_num_rows($err14);
   
   
   // query para saber por medicamentos la cantidad de usuarios atendidos
    $query15 = " SELECT tromedica1,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND tromedica1 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY tromedica1";
   
    $err15 = mysql_query($query15,$conex);
    $num15 = mysql_num_rows($err15);
   
   
   // query para saber por medicamentos la cantidad de usuarios atendidos
    $query16 = " SELECT tromedica2,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND tromedica2 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY tromedica2";
   
    $err16 = mysql_query($query16,$conex);
    $num16 = mysql_num_rows($err16);
	
	// query para saber por medicamentos la cantidad de usuarios atendidos
    $query17 = " SELECT tromedica3,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND tromedica3 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY tromedica3";
   
    $err17 = mysql_query($query17,$conex);
    $num17 = mysql_num_rows($err17);
	
	
   
   // query para saber por dispositivos medicos la cantidad de usuarios atendidos
    $query20 = " SELECT trodismed1,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodismed1 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodismed1";
   
    $err20 = mysql_query($query20,$conex);
    $num20 = mysql_num_rows($err20);
   
   // query para saber por dispositivos medicos la cantidad de usuarios atendidos
    $query21 = " SELECT trodismed2,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodismed2 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodismed2";
   
    $err21= mysql_query($query21,$conex);
    $num21 = mysql_num_rows($err21);
	
	// query para saber por dispositivos medicos la cantidad de usuarios atendidos
    $query22 = " SELECT trodismed3,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND trodismed3 <> 'NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY trodismed3";
   
    $err22 = mysql_query($query22,$conex);
    $num22 = mysql_num_rows($err22);
	
	// query para saber por contraindicación la cantidad de usuarios atendidos
    $query23 = " SELECT trocontra,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."  GROUP BY 1"
		   ."  ORDER BY trocontra";
   
    $err23 = mysql_query($query23,$conex);
    $num23 = mysql_num_rows($err23);
	
	
	// query para saber por especialidad_1 la cantidad de usuarios atendidos
    $query24 = " SELECT Troesp1,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND Troesp1 <> '5-NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY 1";
   
    $err24 = mysql_query($query24,$conex);
    $num24 = mysql_num_rows($err24);
   
   // query para saber por especialidad_2 la cantidad de usuarios atendidos
    $query25 = " SELECT Troesp2,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND Troesp2 <> '5-NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY 1";
   
    $err25 = mysql_query($query25,$conex);
    $num25 = mysql_num_rows($err25);
   
   // query para saber por especialidad_3 la cantidad de usuarios atendidos
    $query26 = " SELECT Troesp3,count(*)"
           ."   FROM ".$empre1."_000001"
           ."  WHERE trofec between '".$fec1."' and '".$fec2."'"
		   ."    AND Troesp3 <> '5-NO APLICA'"
           ."  GROUP BY 1"
		   ."  ORDER BY 1";
   
    $err26 = mysql_query($query26,$conex);
    $num26 = mysql_num_rows($err26);
	
	
    $arretit=Array();
	
	$arretit[0]='< 10';
	$arretit[1]='10-20';
	$arretit[2]='21-30';
	$arretit[3]='31-40';
	$arretit[4]='41-50';
	$arretit[5]='51-60';
	$arretit[6]='61-70';
	$arretit[7]='71-80';
	$arretit[8]='81-90';
	$arretit[9]='> 90';
	
	$arrecan=Array();
	$arrecanf=Array();
	$arrecanm=Array();
   
    for ($j=0; $j <=9; $j++)
    {
     $arrecan[$j]=0;
	 $arrecanf[$j]=0;
	 $arrecanm[$j]=0;
	}
   
    for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);

      IF ($row2[0]< 11)
      {
      	$arrecan[0]=$arrecan[0]+1;
      } 	
      IF ($row2[0]>=11 and $row2[0]<21)
      {
      	$arrecan[1]=$arrecan[1]+1;
      } 	
      IF ($row2[0]>=21 and $row2[0]<31)
      {
      	$arrecan[2]=$arrecan[2]+1;
      }
	  IF ($row2[0]>=31 and $row2[0]<41)
      {
      	$arrecan[3]=$arrecan[3]+1;
      }
	  IF ($row2[0]>=41 and $row2[0]<51)
      {
      	$arrecan[4]=$arrecan[4]+1;
      }
	  IF ($row2[0]>=51 and $row2[0]<61)
      {
      	$arrecan[5]=$arrecan[5]+1;
      }
	  IF ($row2[0]>=61 and $row2[0]<71)
      {
      	$arrecan[6]=$arrecan[6]+1;
      }
	  IF ($row2[0]>=71 and $row2[0]<81)
      {
      	$arrecan[7]=$arrecan[7]+1;
      }
      IF ($row2[0]>=81 and $row2[0]<91)
      {
      	$arrecan[8]=$arrecan[8]+1;
      }
	  IF ($row2[0]>=91 )
      {
      	$arrecan[9]=$arrecan[9]+1;
      }
	  
	 } // cierre del for
      
	 for ($i=1;$i<=$num2;$i++)
	 {
	  $row = mysql_fetch_array($err2);

	  IF ($row[0]=='F') 
	  {
	  
       IF ($row[1]< 11)
       {
      	$arrecanf[0]=$arrecanf[0]+1;
       } 	
       IF ($row[1]>=11 and $row[1]<21)
       {
      	$arrecanf[1]=$arrecanf[1]+1;
       } 	
       IF ($row[1]>=21 and $row[1]<31)
       {
      	$arrecanf[2]=$arrecanf[2]+1;
       }
	   IF ($row[1]>=31 and $row[1]<41)
       {
      	$arrecanf[3]=$arrecanf[3]+1;
       }
	   IF ($row[1]>=41 and $row[1]<51)
       {
      	$arrecanf[4]=$arrecanf[4]+1;
       }
	   IF ($row[1]>=51 and $row[1]<61)
       {
      	$arrecanf[5]=$arrecanf[5]+1;
       }
	   IF ($row[1]>=61 and $row[1]<71)
       {
      	$arrecanf[6]=$arrecanf[6]+1;
       }
	   IF ($row[1]>=71 and $row[1]<81)
       {
      	$arrecanf[7]=$arrecanf[7]+1;
       }
       IF ($row[1]>=81 and $row[1]<91)
       {
      	$arrecanf[8]=$arrecanf[8]+1;
       }
	   IF ($row[1]>=91 )
       {
      	$arrecanf[9]=$arrecanf[9]+1;
       }
	  }
	  ELSE
	  {
	   IF ($row[1]< 10)
       {
      	$arrecanm[0]=$arrecanm[0]+1;
       } 	
       IF ($row[1]>9 and $row[1]<21)
       {
      	$arrecanm[1]=$arrecanm[1]+1;
       } 	
       IF ($row[1]>20 and $row[1]<31)
       {
      	$arrecanm[2]=$arrecanm[2]+1;
       }
	   IF ($row[1]>30 and $row[1]<41)
       {
      	$arrecanm[3]=$arrecanm[3]+1;
       }
	   IF ($row[1]>40 and $row[1]<51)
       {
      	$arrecanm[4]=$arrecanm[4]+1;
       }
	   IF ($row[1]>50 and $row[1]<61)
       {
      	$arrecanm[5]=$arrecanm[5]+1;
       }
	   IF ($row[1]>60 and $row[1]<71)
       {
      	$arrecanm[6]=$arrecanm[6]+1;
       }
	   IF ($row[1]>70 and $row[1]<81)
       {
      	$arrecanm[7]=$arrecanm[7]+1;
       }
       IF ($row[1]>80 and $row[1]<91)
       {
      	$arrecanm[8]=$arrecanm[8]+1;
       }
	   IF ($row[1]>90 )
       {
      	$arrecanm[9]=$arrecanm[9]+1;
       }
	  }
	 } // cierre del for 
	 
	  echo "<br >"; 
	  echo "<br >"; 
	  echo "<br >"; 
	  echo "<br >"; 
	  
      echo "<table border=1 cellpadding='0' cellspacing='0' size=102%>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=2%><font text color=#000000 size=1></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[0]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[1]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[2]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[3]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[4]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[5]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[6]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[7]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[8]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[9]</b></td>";
      echo "</tr>";
      
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>CANTIDAD</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[0])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[1])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[2])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[3])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[4])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[5])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[6])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[7])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[8])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecan[9])."</font></td>";
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF width=70%><font size=1 color=#000000><b>TOTAL SEGUIMIENTO: </b></font></td>"; 
      echo "<td align=left bgcolor=#FFFFFF width=30%><font size=2 color=#000000>&nbsp;<b>$num1</b></font></td>";
      echo "</tr >"; 
      echo "</table>";
      
	  echo "<br >"; 
	  echo "<br >"; 
	  echo "<br >"; 
	  echo "<br >"; 
  
      echo "<table border=1 cellpadding='0' cellspacing='0' size=102%>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=2%><font text color=#000000 size=1><b>SEXO</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[0]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=105><font text color=#000000 size=1><b>$arretit[1]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[2]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[3]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[4]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[5]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[6]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[7]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[8]</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=10%><font text color=#000000 size=1><b>$arretit[9]</b></td>";
      echo "</tr>";
  
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>MASCULINO</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[0])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[1])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[2])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[3])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[4])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[5])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[6])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[7])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[8])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanm[9])."</font></td>";
      echo "</tr >";
	  
	  echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>FEMENINO</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[0])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[1])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[2])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[3])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[4])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[5])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[6])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[7])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[8])."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrecanf[9])."</font></td>";
      echo "</tr >";
	  
      echo "</table>";
      
      echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF width=70%><font size=1 color=#000000><b>TOTAL SEGUIMIENTO x SEXO: </b></font></td>"; 
      echo "<td align=left bgcolor=#FFFFFF width=30%><font size=2 color=#000000>&nbsp;<b>$num2</b></font></td>";
      echo "</tr >"; 
      echo "</table>";
  
       echo "<br >"; 
	   echo "<br >"; 
	   echo "<br >"; 
	   echo "<br >"; 
	   
       echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
       echo "<tr>";
       echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ENTIDAD</b></td>";
	   echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	   echo "</tr >"; 
	   
  
    for ($i=1;$i<=$num3;$i++)
	 {
	   $row3 = mysql_fetch_array($err3);
	   
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row3[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row3[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for 
    echo "</table>";
	
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
  
     echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>SERVICIO DE HOSPITALIZACION</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
    
    for ($i=1;$i<=$num4;$i++)
	 {
	   $row4 = mysql_fetch_array($err4);
	   
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row4[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row4[1])."</font></td>";
	   echo "</tr >"; 
	   
	 } // cierre del for 
   echo "</table>";
   
   echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
  
     echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DIAGNOSTICO_1</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >";
	   
	for ($i=1;$i<=$num5;$i++)
	 {
	   $row5 = mysql_fetch_array($err5);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row5[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row5[1])."</font></td>";
	   echo "</tr >"; 
	   
	 } // cierre del for 
     echo "</table>";
    
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
	echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DIAGNOSTICO_2</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >"; 
	
	for ($i=1;$i<=$num6;$i++)
	 {
	   $row6 = mysql_fetch_array($err6);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row6[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row6[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for 
      echo "</table>";
  
    echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
	echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DIAGNOSTICO_3</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >";
	   
    for ($i=1;$i<=$num7;$i++)
	 {
	   $row7 = mysql_fetch_array($err7);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row7[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row7[1])."</font></td>";
	   echo "</tr >"; 
	   
      
	 } // cierre del for 
    echo "</table>";
   
    echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
    echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DIAGNOSTICO_4</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >";
   
    for ($i=1;$i<=$num8;$i++)
	 {
	   $row8 = mysql_fetch_array($err8);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row8[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row8[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for 
    echo "</table>";
	
	echo "<br >";
 	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
	echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DIAGNOSTICO_5</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >"; 
	
    for ($i=1;$i<=$num9;$i++)
	 {
	   $row9 = mysql_fetch_array($err9);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row9[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row9[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for 
     echo "</table>";
	 
	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >"; 
	 
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ANTECEDENTE PATOLOGICO_1</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >";
	 
    for ($i=1;$i<=$num10;$i++)
	 {
	   $row10 = mysql_fetch_array($err10);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row10[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row10[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for 
     echo "</table>";
	
     echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >"; 
	 
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ANTECEDENTE PATOLOGICO_2</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >";
	
    for ($i=1;$i<=$num11;$i++)
	 {
	   $row11 = mysql_fetch_array($err11);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row11[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row11[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for 
     echo "</table>";
	
	echo "<br >";
 	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
	echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ANTECEDENTE PATOLOGICO_3</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >";
  
    for ($i=1;$i<=$num12;$i++)
	 {
	   $row12 = mysql_fetch_array($err12);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row12[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row12[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for
    echo "</table>";
    
	echo "<br >";
 	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
	echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ANTECEDENTE PATOLOGICO_4</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >"; 
	
	for ($i=1;$i<=$num13;$i++)
	 {
	   $row13= mysql_fetch_array($err13);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row13[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row13[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for
    echo "</table>";
	
	echo "<br >";
 	echo "<br >"; 
	echo "<br >"; 
	echo "<br >"; 
	
	echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ANTECEDENTE PATOLOGICO_5</b></td>";
	echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr >"; 
  
    for ($i=1;$i<=$num14;$i++)
	 {
	   $row14= mysql_fetch_array($err14);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row14[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row14[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for
     echo "</table>";
  
     echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >"; 
  
     echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>MEDICAMENTOS_1</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
  
    for ($i=1;$i<=$num15;$i++)
	 {
	   $row15= mysql_fetch_array($err15);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row15[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row15[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for
     echo "</table>";
  
     echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
  
     echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>MEDICAMENTOS_2</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	   
    for ($i=1;$i<=$num16;$i++)
	 {
	   $row16= mysql_fetch_array($err16);
       echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row16[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row16[1])."</font></td>";
	   echo "</tr >"; 
	 } // cierre del for
      echo "</table>";
    
	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	 
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>MEDICAMENTOS_3</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	
	for ($i=1;$i<=$num17;$i++)
	 {
	   $row17= mysql_fetch_array($err17);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row17[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row17[1])."</font></td>";
	   echo "</tr >"; 
	   
      
	 } // cierre del for
     echo "</table>";
	
 	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DISPOSITIVOS MEDICOS_1</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	 
    for ($i=1;$i<=$num20;$i++)
	 {
	   $row20= mysql_fetch_array($err20);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row20[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row20[1])."</font></td>";
	   echo "</tr >"; 
	  
	 } // cierre del for
     echo "</table>";
	 
	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DISPOSITIVOS MEDICOS_2</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >";
	   
    for ($i=1;$i<=$num21;$i++)
	 {
	   $row21= mysql_fetch_array($err21);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row21[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row21[1])."</font></td>";
	   echo "</tr >"; 
	   
	 } // cierre del for
      echo "</table>";
	  
	  echo "<br >";
 	  echo "<br >"; 
	  echo "<br >"; 
	  echo "<br >";
	  
	  echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>DISPOSITIVOS MEDICOS_3</b></td>";
	  echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	  echo "</tr >"; 
	  
    for ($i=1;$i<=$num22;$i++)
	 {
	   $row22= mysql_fetch_array($err22);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row22[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row22[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for
     echo "</table>";
	 
	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	  
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>CONTRAINDICACION</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	  
    for ($i=1;$i<=$num23;$i++)
	 {
	   $row23= mysql_fetch_array($err23);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row23[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row23[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for
     echo "</table>";
  
     echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	  
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ESPECIALIDAD_1</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	  
    for ($i=1;$i<=$num24;$i++)
	 {
	   $row24= mysql_fetch_array($err24);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row24[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row24[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for
     echo "</table>";
	 
	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	  
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ESPECIALIDAD_2</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	  
    for ($i=1;$i<=$num25;$i++)
	 {
	   $row25= mysql_fetch_array($err25);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row25[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row25[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for
     echo "</table>";
	 
	 echo "<br >";
 	 echo "<br >"; 
	 echo "<br >"; 
	 echo "<br >";
	  
	 echo "<table border=1 cellpadding='0' cellspacing='0' size=100%>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=80%><font text color=#000000 size=1><b>ESPECIALIDAD_3</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=20%><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	 echo "</tr >"; 
	  
    for ($i=1;$i<=$num26;$i++)
	 {
	   $row26= mysql_fetch_array($err26);
	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row26[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($row26[1])."</font></td>";
	   echo "</tr >"; 
	   
       
	 } // cierre del for
     echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>