<html>
<head>
<title>MATRIX - [REPORTE ORDENES MEDICAMENTOS DE CONTROL]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_ordenMedControl.php'; 
	}
	
	function enter()
	{
		document.forms.rep_ordenMedControl.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");
/*******************************************************************************************************************************************
*                                             REPORTE ORDENES MEDICAMENTOS DE CONTROL                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver las ordenes de los medicamentos de control y cantidades despachadas y devueltas.            |
//AUTOR				          :Ing. Gabriel Alonso Agudelo Zapata.                                                                          |
//FECHA CREACION			  :Noviembre 10 de 2020.                                                                                        |
//FECHA ULTIMA ACTUALIZACION  :Noviembre 10 de 2020.                                                                                        |
//TABLAS UTILIZADAS :                                                                                                                       |
//movhos_000133 y cliame_000106: Tabla de Autorizaciones pendientes de admisiones.                                                          |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 10-Nov 2020";

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

 $Hi = '07:00:00';
 $Hf = '19:00:00';
 $codart = 'aaaaaa';
session_start();
//Encabezado
encabezado("Ordenes de Medicamentos de Control",$wactualiz,"clinica");

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
 echo "<form name='rep_ordenMedControl' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
// if (!isset($fec1) or !isset($fec2) or !isset($Hi) or !isset($Hf) or !isset($codart))
 if (!isset($fec1) or !isset($Hi) or !isset($Hf) or !isset($codart))
  {
  	echo "<form name='rep_ordenMedControl' action='' method=post>";
  
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
 	echo "<td class='fila1' width=190>Fecha</td>";
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 /*	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";  */
	
	//Hora Inicial y Hora final
	echo  "<tr><td class='fila1'>Hora Inicial (hh:mm:ss)</td><td class='fila2' align='center'><input type='TEXT' name='Hi' size=10 maxlength=8></td></tr>";
 	echo  "<tr><td class='fila1'>Hora Final   (hh:mm:ss)</td><td class='fila2' align='center'><input type='TEXT' name='Hf' size=10 maxlength=8></td></tr>";
	echo  "<tr><td class='fila1'>Codigo Medicamento </td><td class='fila2' align='center'><input type='TEXT' name='codart' size=10 maxlength=7></td></tr>";
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
    $Hi = $_REQUEST['Hi'];
    $Hf = $_REQUEST['Hf'];
    $codart = $_REQUEST['codart'];
	$fecant = date("Y-m-d",strtotime($fec1."- 31 days")); 
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr><td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>ORDENES MEDICAMENTOS DE CONTROL</b></font></td></tr>";
  // echo "<tr><td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
   echo "<tr><td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA: <i>".$fec1."</i></b></font></b></font></td></tr>";
   echo "<tr><td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>HORA INICIAL: <i>".$Hi."</i>&nbsp&nbsp&nbspHORA FINAL: <i>".$Hf."</i></b></font></b></font></td></tr>";
   echo "<tr><td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>CODIGO ARTICULO: <i>".$codart."</i></b></font></b></font></td></tr>";
   echo "</table>";
  	   
   $query = " Select Tcarhis,Tcaring,Pacno1,Pacno2,Pacap1,Pacap2,Ctrart,Artcom,Ctrcan,Ctrcon 
			  from cliame_000106 d,movhos_000133 a,movhos_000026 b,cliame_000100 c
		      where d.Tcarfec = '".$fec1."'  
			  and  d.Tcarprocod = '".$codart."' 
			  and  d.Hora_data between '".$Hi."' and '".$Hf."'  
			  and  d.Tcarest = 'on'  
			  and  d.Tcarhis = a.Ctrhis 
			  and  d.Tcaring = a.Ctring 
			  and  d.Tcarprocod = a.Ctrart
			  and  Ctrest = 'on' 
			  and  Ctrart = Artcod	
			  and  Ctrfge =  '".$fec1."'
			  and  Ctrhis = Pachis 
			GROUP BY Ctrhis,Ctring,Ctrart,Artcom,Ctrcon 
			order by Ctrhis,Ctring,Ctrart";
           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
      
	  $bandera=0;
	  $despachado=0;
	  $devuelto=0;
	  $hist = "";
	  $ing = "";
	  $articulo = "";
	  $contador = 0;
	echo "<table border=0 cellspacing=2 cellpadding=2 align=center size='200'>";  
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row1 = mysql_fetch_array($err1);
	  
	  if ($row1[0] != $hist OR $row1[1] != $ing OR $row1[6] != $articulo)
	  {
		 $bandera = 0;
		 if ($contador > 0)
		 {
			 echo "<tr>";
		     echo "<td  bgcolor=#FFFFFF align=center><font size=2>Cantidades Grabadas y Devueltas</font></td>";
		     echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
		     echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
		     echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
		     echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
		     echo "<td  bgcolor=#FFFFFF align=center><font size=2>".$despachado."</font></td>";
			 echo "<td  bgcolor=#FFFFFF align=center><font size=2>".$devuelto."</font></td>";
			 echo "</tr> ";
		 }
	  }
	  if ($bandera == 0)
		{
		  $hist = $row1[0];
		  $ing  = $row1[1];
		  $articulo = $row1[6];
		  
		  $query2 = " select sum(Tcarcan) 
					 from cliame_000106 
					 where Tcarhis = '".$hist."' 
						  and  Tcaring = '".$ing."' 
						  and  Tcarprocod = '".$articulo."' 
						  and  Tcarfec = '".$fec1."' 
						  and  Hora_data between '".$Hi."' and '".$Hf."' 
						  and  Tcarest = 'on' 
						  and  Tcardev != 'on' ";
           
		   $err2 = mysql_query($query2,$conex);
		   $row2 = mysql_fetch_array($err2);
		   $despachado = $row2[0];
		   if (empty($despachado))
				$despachado = 0;
		  $query3 = " select sum(Tcarcan) 
					 from cliame_000106 
					 where Tcarhis = '".$hist."' 
						  and  Tcaring = '".$ing."' 
						  and  Tcarprocod = '".$articulo."' 
						  and  Tcarfec = '".$fec1."' 
						  and  Hora_data between '".$Hi."' and '".$Hf."' 
						  and  Tcarest = 'on' 
						  and  Tcardev = 'on' ";
		   $err3 = mysql_query($query3,$conex);
		   $row3 = mysql_fetch_array($err3);
		   $devuelto = $row3[0];
		   /*echo "<br>";
		   echo $query2;
		   echo "<br>";
		   echo $query3;*/
		   
		   if (empty($devuelto))
				$devuelto = 0;
		 
              echo "<tr> ";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>FECHA GRABACION</b></td>";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>CONSECUTIVO</b></td>";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>PACIENTE</b></td>";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>MEDICAMENTO</b></td>";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>CANTIDAD ORDENADA</b></td>";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>DESPACHADO</b></td>";
			  echo "<td bgcolor=#F0FFFF align=center><font text color=#000000 size=2><b>DEVUELTO</b></td>";
			  echo "</tr>";
			  $bandera = 1;
			  
		}
	  
		  echo "<tr>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=2>".$fec1."</font></td>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=2>$row1[9]</font></td>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=2>".$row1[2]." ".$row1[3]." ".$row1[4]." ".$row1[5]."</font></td>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=2>$row1[7]</font></td>";
		  echo "<td  bgcolor=#FFFFFF align=center><font size=2>$row1[8]</font></td>";
		  echo "</tr>";
		$contador = $contador + 1;
		
     } // cierre del for
    {
	  echo "<tr>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2>Cantidades Grabadas y Devueltas</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2></font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2>".$despachado."</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=2>".$devuelto."</font></td>";
	  echo "</tr> ";
    }
    echo "</table>";   

   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>