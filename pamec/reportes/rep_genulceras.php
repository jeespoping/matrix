<html>
<head>
<title>MATRIX - [REPORTE PLAN DE CRUCE DE VARIABLES GRUPO DE ULCERAS POR PRESIÓN]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_genulceras.php'; 
	}
	
	function enter()
	{
		document.forms.rep_genulceras.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE GENERAL GRUPO DE ULCERAS POR PRESIÓN                                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver en general totales de ulceras por presión.                                                  |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :SEPTIEMBRE 24 DE 2010.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  :24 de SEPTIEMBRE de 2010.                                                                                    |
//TABLAS UTILIZADAS :                                                                                                                       |
//pamec_000005      : Tabla de grupo de ulceras por presión.                                                                                |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 24-Septiembre-2010";

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
encabezado("Plan De Cruce De Variables Grupo De Ulceras Por Presión",$wactualiz,"clinica");

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
 echo "<form name='rep_genulceras' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_genulceras' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>TOTALES GENERALES ULCERAS POR PRESIÓN</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   //Inicializo las variables
   $totdiauci=0;
   $totdiascum=0;
   $totupp=0;
   $totpupp=0;
   $totliii=0;
   $totliv=0;
   
   //TOTAL DIAS DE ESTANCIA EN UCI
   $query1 = " SELECT SUM(pupdeuci)"
            ."   FROM pamec_000005"
            ."  WHERE pupfecegrcli between '".$fec1."' and '".$fec2."'";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   //echo $query."<br>";

   $row1 = mysql_fetch_array($err1);
   
   $totdiauci=$row1[0];

   IF ($totdiauci=='')
   {
   	$totdiauci=0;
   }
   
   //TOTAL DIAS DE CUMPLIMIENTO
   $query2 = " SELECT sum(pupdiacumpli)"
            ."   FROM pamec_000005"
            ."  WHERE pupfecegrcli between '".$fec1."' and '".$fec2."'";
           
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   //echo $query."<br>";

   $row2 = mysql_fetch_array($err2);
   
   $totdiascum=$row2[0];

   IF ($totdiascum=='')
   {
   	$totdiascum=0;
   }
   
   //TOTAL UPP = ON
   $query3 = " SELECT sum(pupnumupp)"
            ."   FROM pamec_000005"
            ."  WHERE pupupp='on'"
            ."    AND pupfecegrcli between '".$fec1."' and '".$fec2."'";
           
   $err3 = mysql_query($query3,$conex);
   $num3 = mysql_num_rows($err3);
   //echo $query."<br>";

   $row3 = mysql_fetch_array($err3);
   
   $totupp=$row3[0];

   IF ($totupp=='')
   {
   	$totupp=0;
   }
   
   //TOTAL DE PACIENTES CON UPP=ON
   $query4 = " SELECT count(*)"
            ."   FROM pamec_000005"
            ."  WHERE pupupp='on'"
            ."    AND pupfecegrcli between '".$fec1."' and '".$fec2."'";
           
   $err4 = mysql_query($query4,$conex);
   $num4 = mysql_num_rows($err4);
   //echo $query."<br>";

   $row4 = mysql_fetch_array($err4);
   
   $totpupp=$row4[0];  
   
   IF ($totpupp=='')
   {
   	$totpupp=0;
   }
   
   //TOTAL DE LESION III
   $query5 = " SELECT COUNT(*)"
            ."   FROM pamec_000005"
            ."  WHERE pupfecegrcli between '".$fec1."' and '".$fec2."'"
            ."    AND (pupsacraiii  = 'on'"
            ."     OR pupoccipiiii  = 'on'"
            ."     OR pupnasaliii  = 'on'"
            ."     OR puppabauriiii = 'on'"
            ."     OR puplabiosiii  = 'on'"
            ."     OR pupescapulaiii= 'on'"
            ."     OR pupcodoiii    = 'on'"
            ."     OR pupgenitaliii = 'on'"
            ."     OR puptroncaiii   = 'on'"
            ."     OR pupmaleolariii= 'on'"
            ."     OR puptaloniii   = 'on'"
            ."     OR pupotraiii    = 'on')";
           
   $err5 = mysql_query($query5,$conex);
   $num5 = mysql_num_rows($err5);
   //echo $query5."<br>";

   $row5 = mysql_fetch_array($err5);
   
   $totliii=$row5[0];  
   
   IF ($totliii=='')
   {
   	$totliii=0;
   }
   
   //TOTAL DE LESION IV
   $query6 = " SELECT COUNT(*)"
            ."   FROM pamec_000005"
            ."  WHERE pupfecegrcli between '".$fec1."' and '".$fec2."'"
            ."    AND (pupsacraiv    = 'on'"
            ."     OR pupoccipiiv   = 'on'"
            ."     OR pupnasaliv   = 'on'"
            ."     OR puppabauriiv  = 'on'"
            ."     OR puplabiosiv   = 'on'"
            ."     OR pupescapulaiv = 'on'"
            ."     OR pupcodoiv     = 'on'"
            ."     OR pupgenitaliv  = 'on'"
            ."     OR puptroncaiv    = 'on'"
            ."     OR pupmaleolariv = 'on'"
            ."     OR puptaloniv    = 'on'"
            ."     OR pupotraiv     = 'on')";
           
   $err6 = mysql_query($query6,$conex);
   $num6 = mysql_num_rows($err6);
   //echo $query."<br>";

   $row6 = mysql_fetch_array($err6);
   
   $totliv=$row6[0];  
  
   IF ($totliv=='')
   {
   	$totliv=0;
   }
   
   // Acá la tabla para la impresión
   echo "<table border=0 cellspacing=1 cellpadding=1 align=center size='100'>";
   echo "<tr>";
   echo "<td align=center bgcolor=#FFFFFF><font size='2' text color=#003366><b>ULCERAS POR PRESIÓN</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TOTAL EN DIAS</b></font></td>";  
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=LEFT><font size=1 color='#000000'><b>Total De Días De Estancia En UCI:</b></font></td>";  
   echo "<td aling=left><font size=1>&nbsp;$totdiauci</font></td>";
   echo "</tr>";
   echo "<tr>"; 
   echo "<td align=LEFT><font size=1 color='#000000'><b>Total Días De Cumplimiento:</b></font></td>";  
   echo "<td aling=left><font size=1>&nbsp;$totdiascum</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>U.P.P.</b></font></td>";  
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total UPP on:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;$totupp</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total De Pacientes Con UPP on:</b></font></td>";  
   echo "<td aling=left bgcolor=#FFFFFF><font size=1>&nbsp;$totpupp</font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TOTALES LESIÓN III Y IV</b></font></td>";  
   echo "</tr>";

   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total Lesión III:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;$totliii</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>Total Lesión IV:</b></font></td>";  
   echo "<td align=left bgcolor=#FFFFFF><font size=1>&nbsp;$totliv</font></td>";
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