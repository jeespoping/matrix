<html>
<head>
<title>MATRIX - [REPORTE PLAN DE CRUCE DE VARIABLES GRUPO DE NUTRICIÓN]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_gennutrion.php'; 
	}
	
	function enter()
	{
		document.forms.rep_gennutrion.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE GENERAL DE NUTRICION	                                                               *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver en general totales de nutricion.                                                            |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JULIO 28 DE 2010.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :28 de JULIO de 2010.                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                       |
//pamec_000007      : Tabla de nutricion de U.C.I.                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 28-Julio-2010";

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
encabezado("Plan De Cruce De Variables Grupo De Nutrición",$wactualiz,"clinica");

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
 echo "<form name='rep_gennutrion' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_gennutrion' action='' method=post>";
  
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

   echo "<br>";
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>TOTALES GENERALES NUTRICIÓN</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   //Inicializo las variables
   $totpaci=0;
   $totdnute=0;
   $totsongas=0;
   $totsonyey=0;
   $totsongast=0;
   $totsonyeyunos=0;
   $totcommec=0;
   $totsonali=0;
   $totsonobs=0;
   $totsalacci=0;
   $totdcumplilav=0;
   $totcumlav=0;
   $totnpacdis=0;
   $totnumdepo=0;
   $totpacestre=0;
   $totdcumtoma=0;
   $totcoto=0;
   $totdhiper=0;
   $totpparen=0;
   
   
   
   //NUTRICION ENTERAL
   $query1 = " SELECT count(*),sum(ndianutent)"
            ."   FROM pamec_000007"
            ."  WHERE nnutrienetral='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   //echo $query."<br>";

   $row1 = mysql_fetch_array($err1);
   
   $totpaci=$row1[0];
   $totdnute=$row1[1];
   
   IF ($totpaci==0)
   {
   	$totpaci=1;
   	$totpaci1=0;
   }
   ELSE
   {
   	$totpaci1=$row1[0];
   }
   
   IF ($totdnute==0)
   {
   	$totdnute=1;
   	$totdnute1=0;
   }
   ELSE
   {
   	$totdnute1=$row1[1];
   }
   
   //UBICACION DE LA SONDA
   $query2 = " SELECT sum(nnumsogas)"
            ."   FROM pamec_000007"
            ."  WHERE ngastriconfir='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   //echo $query."<br>";

   $row2 = mysql_fetch_array($err2);
   
   $totsongas=$row2[0];
   
   IF ($totsongas=='')
   {
   	$totsongas=0;
   }
   
   $query3 = " SELECT sum(nnumsonyeyu)"
            ."   FROM pamec_000007"
            ."  WHERE nyeyunal='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err3 = mysql_query($query3,$conex);
   $num3 = mysql_num_rows($err3);
   //echo $query."<br>";

   $row3 = mysql_fetch_array($err3);
   
   $totsonyey=$row3[0];

   IF ($totsonyey=='')
   {
   	$totsonyey=0;
   }
   
   $query4 = " SELECT sum(nnumsongastro)"
            ."   FROM pamec_000007"
            ."  WHERE ngastros='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err4 = mysql_query($query4,$conex);
   $num4 = mysql_num_rows($err4);
   //echo $query."<br>";

   $row4 = mysql_fetch_array($err4);
   
   $totsongast=$row4[0];  
   
   IF ($totsongast=='')
   {
   	$totsongast=0;
   }
   
   $query5 = " SELECT sum(nnumsonyeyu)"
            ."   FROM pamec_000007"
            ."  WHERE nyeyunosto='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err5 = mysql_query($query5,$conex);
   $num5 = mysql_num_rows($err5);
   //echo $query."<br>";

   $row5 = mysql_fetch_array($err5);
   
   $totsonyeyunos=$row5[0];  
   
   IF ($totsonyeyunos=='')
   {
   	$totsonyeyunos=0;
   }
   
   //COMPLICACIONES MECANICAS
   
   $query6 = " SELECT sum(ndiaubicorrec)"
            ."   FROM pamec_000007"
            ."  WHERE nyeyunosto='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err6 = mysql_query($query6,$conex);
   $num6 = mysql_num_rows($err6);
   //echo $query."<br>";

   $row6 = mysql_fetch_array($err6);
   
   $totcommec=$row6[0];  
   
   $query71 = " SELECT sum(nnumsogas+nnumsonyey+nnumsongastro+nnumsonyeyu)"
             ."   FROM pamec_000007"
             ."  WHERE nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err71 = mysql_query($query71,$conex);
   $num71 = mysql_num_rows($err71);
   //echo $query."<br>";

   $row71 = mysql_fetch_array($err71);
   
   $totsonali=$row71[0];   
   
   IF ($totsonali==0)
   {
   	$totsonali=1;
   }
   
   $query7 = " SELECT count(*)"
             ."   FROM pamec_000007"
             ."  WHERE nobstrusonda='on'"
             ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err7 = mysql_query($query7,$conex);
   $num7 = mysql_num_rows($err7);
   //echo $query."<br>";

   $row7 = mysql_fetch_array($err7);
   
   $totsonobs=$row7[0];  
   
   $query8 = " SELECT count(*)"
             ."   FROM pamec_000007"
             ."  WHERE nsaliaccisonsa='on'"
             ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err8 = mysql_query($query8,$conex);
   $num8 = mysql_num_rows($err8);
   //echo $query."<br>";

   $row8 = mysql_fetch_array($err8);
   
   $totsalacci=$row8[0];  
   
   $query9 = " SELECT sum(ncumplelavsonsa4h)"
             ."   FROM pamec_000007"
             ."  WHERE nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err9 = mysql_query($query9,$conex);
   $num9 = mysql_num_rows($err9);
   //echo $query."<br>";

   $row9 = mysql_fetch_array($err9);
   
   $totdcumplilav=$row9[0]; 
   
   $query10= " SELECT sum(ncumplelavsonsa4h),count(*)"
            ."   FROM pamec_000007"
            ."  WHERE nobstrusonda='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err10 = mysql_query($query10,$conex);
   $num10 = mysql_num_rows($err10);
   //echo $query."<br>";

   $row10 = mysql_fetch_array($err10);
   
   IF ($row10[1]==0)
   {
   	$row10[1]=1;
   }
   
   $totcumlav=$row10[0]/$row10[1]; 
      

   //COMPLICACIONES GASTROINTESTINALES
   $query11= " SELECT count(*)"
            ."   FROM pamec_000007"
            ."  WHERE ndisteadbo='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err11 = mysql_query($query11,$conex);
   $num11 = mysql_num_rows($err11);
   //echo $query."<br>";

   $row11 = mysql_fetch_array($err11);
   
   $totnpacdis=$row11[0]; 
   
   $query12= " SELECT count(*)"
            ."   FROM pamec_000007"
            ."  WHERE nnumdeposi>=5"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err12 = mysql_query($query12,$conex);
   $num12 = mysql_num_rows($err12);
   //echo $query."<br>";

   $row12 = mysql_fetch_array($err12);
   
   $totnumdepo=$row12[0];    
   
   $query13= " SELECT count(*)"
            ."   FROM pamec_000007"
            ."  WHERE nestreni='on'"
            ."    AND nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err13 = mysql_query($query13,$conex);
   $num13 = mysql_num_rows($err13);
   //echo $query."<br>";

   $row13 = mysql_fetch_array($err13);
   
   $totpacestre=$row13[0];    

   $query14= " SELECT sum(ncumtomaresgas4h)"
            ."   FROM pamec_000007"
            ."  WHERE nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err14 = mysql_query($query14,$conex);
   $num14 = mysql_num_rows($err14);
   //echo $query."<br>";

   $row14 = mysql_fetch_array($err14);
   
   $totdcumtoma=$row14[0];  
      
   $query15= " SELECT sum(ncumpl30A45)"
            ."   FROM pamec_000007"
            ."  WHERE nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err15 = mysql_query($query15,$conex);
   $num15 = mysql_num_rows($err15);
   //echo $query."<br>";

   $row15 = mysql_fetch_array($err15);
   
   $totcoto=$row15[0];  

   $query16= " SELECT sum(nglice150)"
            ."   FROM pamec_000007"
            ."  WHERE nfecuci between '".$fec1."' and '".$fec2."'";
           
   $err16 = mysql_query($query16,$conex);
   $num16 = mysql_num_rows($err16);
   //echo $query."<br>";

   $row16 = mysql_fetch_array($err16);
   
   $totdhiper=$row16[0];
   
   $query17= " SELECT count(*)"
            ."   FROM pamec_000007"
            ."  WHERE nfecuci between '".$fec1."' and '".$fec2."'"
            ."    AND nnutriparenteral='on'";
           
   $err17 = mysql_query($query17,$conex);
   $num17 = mysql_num_rows($err17);
   //echo $query."<br>";

   $row17 = mysql_fetch_array($err17);
   
   $totpparen=$row17[0];   

   
   // Acá la tabla para la impresión
   echo "<table border=0 cellspacing=1 cellpadding=1 align=LEFT size='100'>";
   echo "<tr>";
   echo "<td align=center bgcolor=#FFFFFF><font size='2' text color=#003366><b>NUTRICIÓN ENTERAL</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=LEFT><font size=1 color='#000000'><b>Total de Pacientes Con Nutrición Enteral:</b></font></td>";  
   echo "<td aling=left><font size=1>&nbsp;$totpaci1</font></td>";
   echo "</tr>";
   echo "<tr>"; 
   echo "<td align=LEFT><font size=1 color='#000000'><b>Días de Nutrición Enteral:</b></font></td>";  
   echo "<td aling=left><font size=1>&nbsp;$totdnute1</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>UBICACIÓN DE LA SONDA</b></font></td>";  
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total Sondas Gástricas:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;$totsongas</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total Sondas Yeyunales:</b></font></td>";  
   echo "<td aling=left bgcolor=#FFFFFF><font size=1>&nbsp;$totsonyey</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total Sondas de Gastrostomia:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;$totsongast</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total Sondas Yeyunostomia:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;$totsonyeyunos</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>COMPLICACIONES MECANICAS</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Ubicación Correcta De La Sonda De Alimentación:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totcommec/$totdnute)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Sondas Obstruidas:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totsonobs/$totsonali)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Salida Accidental De La Sonda:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totsalacci/$totsonali)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Cumplimiento De Lavado De Sondas de Alimentación cada 4 Horas:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totdcumplilav/$totpaci)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Cumplimiento De Lavado De Sondas De Alimentación cada 4 Horas Por Sonda:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format($totcumlav)."</font></td>";
   echo "</tr>";

   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>COMPLICACIONES GASTROINTESTINALES</b></font></td>";  
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Distensión Abdominal:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totnpacdis/$totpaci)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Pacientes Con Más de 5 Deposiciones:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totnumdepo/$totpaci)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Estreñimiento:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totpacestre/$totpaci)*100)."</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Toma De Residuo Gástrico Cada 4 Horas:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totdcumtoma/$totdnute)*100)."</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>COMPLICACIONES OTORRINOLARINGOLÓGICAS</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Cumplimiento De La Posición De Cabecera Entre 30-45 Grados:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format(($totcoto/$totdnute)*100)."</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>COMPLICACIONES METABÓLICAS</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Hiperglicemia Mayor De 150MG/DL:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format($totdhiper/$totpaci)."</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>NUTRICIÓN PARENTERAL</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total De Pacientes Con Nutrión Parenteral:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;".number_format($totpparen)."</font></td>";
   echo "</tr>";
   
   
   echo "<tr>";
   echo "<br>";  
   echo "<br>";
   echo "</tr>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>