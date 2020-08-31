<html>
<head>
<title>MATRIX - [REPORTE DE LLAMADAS A SOPORTE]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_llamadas.php'; 
	}
	
	function enter()
	{
		document.forms.rep_llamadas.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
	
	
</script>

<?php
include_once("conex.php");

function RestarHoras($horaini,$horafin)
    {
	 $horai=substr($horaini,0,2);
	 $mini=substr($horaini,3,2);
	 $segi=substr($horaini,6,2);

	 $horaf=substr($horafin,0,2);
	 $minf=substr($horafin,3,2);
	 $segf=substr($horafin,6,2);

	 $ini=((($horai*60)*60)+($mini*60)+$segi);
	 $fin=((($horaf*60)*60)+($minf*60)+$segf);

	 IF ($fin>$ini)
	 {
	  $dif=$fin-$ini;
	 }
	 ELSE
	 {
	  $horaf=24;
	  $minf=00;
	  $segf=00;
	  $esp=((($horaf*60)*60)+($minf*60)+$segf);
	 	
	  $dif=($esp-$ini)+$fin;	
	 }
	  
	 $difh=floor($dif/3600);
	 $difm=floor(($dif-($difh*3600))/60);
	 $difs=$dif-($difm*60)-($difh*3600);
	 return date("H:i:s",mktime($difh,$difm,$difs));
   }

/*******************************************************************************************************************************************
*                                             REPORTE LLAMADAS A SOPORTE                                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver las llamadas registradas de soporte.                                                       |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : ENERO 29 DE 2013.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : 29 de ENERO de 201                                                                                         |
//TABLAS UTILIZADAS :                                                                                                                      |
//equipos_000003    : Tabla de llamadas.                                                                                                   |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 29-Enero-2013";

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
encabezado("Estadisticas Llamadas a Soporte",$wactualiz,"clinica");

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
 echo "<form name='rep_llamadas' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_llamadas' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>ESTADISTICAS LLAMADAS A SOPORTE</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  	
   //Inicializo las variables
   $totevento=0;
   $totdiascum=0;
   $totupp=0;
   $totpupp=0;
   $totliii=0;
   $totliv=0;
   
   //TOTAL REPORTADOS
   $query1 = " SELECT COUNT(*)"
            ."   FROM equipos_000003"
            ."  WHERE fecha_llamada between '".$fec1."' and '".$fec2."'";
           
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   //echo $query."<br>";

   $row1 = mysql_fetch_array($err1);
   
   $totevento=$row1[0];

   IF ($totevento=='')
   {
   	$totevento=0;
   }
   
   //DETALLE DE LAS LLAMADAS
   $query2 = " SELECT conexion,DATEDIFF(fecha_respuesta,fecha_llamada) as dias,fecha_llamada,hora_llamada,fecha_respuesta,hora_respuesta,obje_llamada,unidad,usuario_reporta,usuario_atiende,Tipo_llamada"
            ."   FROM equipos_000003"
            ."  WHERE fecha_llamada between '".$fec1."' and '".$fec2."'"
            ."  ORDER BY 11,1,2,3,4";
           
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   //echo $query."<br>";

   //TOTAL x CONEXION
   $query3 = " SELECT conexion,count(*) as cant"
            ."   FROM equipos_000003"
            ."  WHERE fecha_llamada between '".$fec1."' and '".$fec2."'"
            ." GROUP BY 1"
            ." ORDER BY 1";
           
   $err3 = mysql_query($query3,$conex);
   $num3 = mysql_num_rows($err3);
   //echo $query."<br>";
   
   //TOTAL x TIPO DE LLAMADA
   $query4 = " SELECT tipo_llamada,count(*) as cant"
            ."   FROM equipos_000003"
            ."  WHERE fecha_llamada between '".$fec1."' and '".$fec2."'"
            ." GROUP BY 1"
            ." ORDER BY 1";
           
   $err4 = mysql_query($query4,$conex);
   $num4 = mysql_num_rows($err4);
   //echo $query4."<br>";
   
   
   
   // Acá la tabla para la impresión
   echo "<br>";
   echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "<tr>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO_DE_LLAMADA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CONEXION</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA_LLAMADA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA_LLAMADA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA_RESPUESTA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA_RESPUESTA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TOTAL_DIAS</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TOTAL_TIEMPO</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOTIVO DE LA LLAMADA Y SOLUCION</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>UNIDAD_DE_QUIEN_REPORTA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO_QUE_REPORTA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO_QUE_ATIENDE</b></td>";
   echo "</tr>";
   
   
   for ($i=1;$i<=$num2;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

    $row2 = mysql_fetch_array($err2);
    
    $horas=RestarHoras($row2[3],$row2[5]);

    IF ($horas<"23:59:59")
     {
      IF ($row2[1]=1)	
      {
       $row2[1]=0;
      }
     }
    
	$obje=substr($row2[6],0,19);
	
    echo "<Tr bgcolor=".$wcf.">";
	echo "<td align=center><font size=1>$row2[10]</font></td>";
	echo "<td align=center><font size=1>$row2[0]</font></td>";
	echo "<td align=center><font size=1>$row2[2]</font></td>";
	echo "<td align=center><font size=1>$row2[3]</font></td>";
    echo "<td align=center><font size=1>$row2[4]</font></td>";
    echo "<td align=center><font size=1>$row2[5]</font></td>";
    echo "<td align=center><font size=1>$row2[1]</font></td>";
    echo "<td align=center><font size=1>$horas</font></td>";
    echo "<td align=center><font size=1>$obje ...</font></td>";
	echo "<td align=center><font size=1>$row2[7]</font></td>";
	echo "<td align=center><font size=1>$row2[8]</font></td>";
	echo "<td align=center><font size=1>$row2[9]</font></td>";
   }
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=center><font size=1 color='#000000'><b>TOTAL DE LLAMADAS:</b></font></td>";  
   echo "<td aling=center><font size=1>&nbsp;$totevento</font></td>";
   echo "</tr>";
   echo "</table>";
   
   echo "<br>";
   
   echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "<tr>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CONEXION</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TOTAL X CONEXION</b></td>";
   echo "</tr>";
   
  for ($i=1;$i<=$num3;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

    $row3 = mysql_fetch_array($err3);
    
    echo "<Tr bgcolor=".$wcf.">";
	echo "<td align=center><font size=1>$row3[0]</font></td>";
	echo "<td align=center><font size=1>$row3[1]</font></td>";
	
   }
   echo "<tr>";
   echo "</table>";
   
   echo "<br>";
   
   echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "<tr>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO_DE_LLAMADA</b></td>";
   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TOTAL X TIPO_DE_LLAMADA</b></td>";
   echo "</tr>";
   
  for ($i=1;$i<=$num4;$i++)
	{
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

    $row4 = mysql_fetch_array($err4);
    
    echo "<Tr bgcolor=".$wcf.">";
	echo "<td align=center><font size=1>$row4[0]</font></td>";
	echo "<td align=center><font size=1>$row4[1]</font></td>";
	
   }
   echo "<tr>";
   echo "</table>";  
   
   echo "<br>";
   
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";
   echo "</tr>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>