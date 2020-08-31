<html>
<head>
<title>Impresión Carta de Cobro - Unix</title>
<style type="text/css">
BODY           
{   
    font-family: Verdana;
    font-size: 10pt;
    margin: 0px;
}
</style>
</body>
</html>

<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                           IMPRESION CARTA DE COBRO - UNIX                                                               *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :Impresión Carta de Cobro.                                                                                    |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Enero 26 DE 2016.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :26 de Enero de 2016.                                                                                         |
//DESCRIPCION			      :Este programa sirve para imprimir la CARTA DE COBRO de unix.                                                 |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cacar             : Tabla de facturas.                                                                                                    | 
//caenvenc          : Tabla Encabezado CARTA DE COBRO.                                                                                      | 
//caenvdet          : Tabla detalle de la CARTA DE COBRO (Carta de envio).                                                                  |
//                                                                                                                                          |
// Modificacion     :                                                                                                                       |
//                   2016-06-08 Se adiciona la tabla inpac, para buscar pacientes activos                                                   |
//                   2016-06-08 Se pide que se cambie la palabra CUENTA DE COBRO x CARTA DE COBRO                                           |
//==========================================================================================================================================

include_once("root/comun.php");
include_once("root/montoescrito.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.1 08-Junio-2016";

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

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 
 //Conexion base de datos
 $conexi=odbc_connect('facturacion','','')
	       or die("No se realizo conexión con la BD de Facturación");

	       //Forma
 echo "<form name='forma' action='rep_carta_cobro.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' )
  {
  	echo "<form name='rep_carta_cobro' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";

 	//FUENTE Y NUMERO DE CTA DE COBRO
 	echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>IMPRESION CARTA DE COBRO</td></tr>";
	echo"<tr><td align=center bgcolor=#cccccc >FUENTE DE CARTA DE COBRO :</TD><td align=center bgcolor=#cccccc ><input style='text-transform: uppercase;' type='input' name='fue' ></td></tr>";
	echo"<tr><td align=center bgcolor=#cccccc >NUMERO DE CARTA DE COBRO :</TD><td align=center bgcolor=#cccccc ><input type='input' name='carg'></td></tr>";
	echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>"; //submit osea el boton de GENERAR o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {	
   $fue = strtoupper($fue);
  	
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
  $query1="SELECT ciadir,cianom,cianit,ciatel,cianoc "
		  ." FROM sicia";
		    
  //echo $query1."<br>";
  $err_o1 = odbc_do($conexi,$query1);
	
  echo "<br>";
  echo "<br>"; 
  echo "<br>";
  echo "<br>"; 
  echo "<br>";
  echo "<br>"; 
  echo "<br>";
  echo "<br>"; 
  echo "<br>";
  echo "<br>"; 
  echo "<br>";
	
  $Num_Filas = 0;	
  	
  echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";	
  echo "<td colspan='1' align=center><IMG SRC='/MATRIX/images/medical/calidad/clnicanuevo.gif' WIDTH=300 HEIGHT=120></td>"; //trae el logo de la clinica.
  echo "</tr>";
  echo "</table>";
  
  while (odbc_fetch_row($err_o1))
   {
	 $Num_Filas++;
	  	
	 $dir1 = odbc_result($err_o1,1);//DIRECCION COMPAÑIA
	 $nom1 = odbc_result($err_o1,2);//NOMBRE COMPAÑIA
	 $nit1  = odbc_result($err_o1,3);//NIT COMPAÑIA
	 $tel1  = odbc_result($err_o1,4);//TELEFONO COMPAÑIA
	 $noc1  = odbc_result($err_o1,5);//NOMBRE CIUDAD
	 
	 echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
     echo "<td align=CENTER bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>".$nom1."</b></font></td>";
	 echo "</tr>";
	 	 
	 echo "<tr>";
     echo "<td align=CENTER bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>NIT. ".$nit1."</b></font></td>";
	 echo "</tr>";
	 echo "</table>";
	 	
	}

	 echo "<br>";
	 echo "<br>";
		
	$query="SELECT envencdoc,envencest,envenccse,envencfec,empnit,empdir,empdetraz empnom,munnom,envencreg,envencvto"
         ."   FROM caenvenc,inemp,inmun,inempdet"
         ."  WHERE envencfue='".$fue."'"
         ."    and envencdoc='".$carg."'"
		 ."    and envencnit=empcod"
		 ."    and empmun=muncod"
		 ."    and empcod=empdetcod";
  		 
			
  $err_o = odbc_do($conexi,$query);
			
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o))
   {
	 $Num_Filas++;
	  	
	 $doc     = odbc_result($err_o,1);//CUENTA COBRO
	 $est     = odbc_result($err_o,2);//ESTADO
	 $cons    = odbc_result($err_o,3);//CONSECUTIVO DE ENVIO
	 $fecha   = odbc_result($err_o,4);//FECHA
	 $nit     = odbc_result($err_o,5);//NIT
	 $dir     = odbc_result($err_o,6);//DIRECCION EMPRESA
	 $nomn    = odbc_result($err_o,7);//NOMBRE_NIT
	 $nomc    = odbc_result($err_o,8);//NOMBRE_CIUDAD
	 $cant    = odbc_result($err_o,9);//CANTIDAD REGISTROS
	 $vto     = odbc_result($err_o,10);//VALOR TOTAL
	 
	 
	 echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>CARTA DE COBRO Nro. </b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>".$doc."</b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>(".$est.")</b></font></td>";
	 echo "</tr>";
	
	 echo "<tr>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>Consecutivo de Envio Nro. </b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>".$cons."</b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='4' text color='#000000'>&nbsp;</font></td>";
	 echo "</tr>";
	 echo "</table>";
	} 
	
     $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
 
     $auxfecha=explode('-',$fecha);
 
     // date('d')." de ".$meses[date('n')-1]. " del ".date('Y') ;   esto es para el dia de hoy
	 	 	 
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
     echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 
	 echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'>MEDELLIN ,".$auxfecha[2]." de ".$meses[($auxfecha[1]*1)-1]." del ".$auxfecha[0]." </font></td>";
	 echo "</tr>";
	 echo "</table>";
	 
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";

	 
	 echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'>Señores</font></td>";
	 echo "</tr>";
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'><b>".$nomn." - ".$nit."</b></font></td>";
	 echo "</tr>";
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'>".$dir."</font></td>";
	 echo "</tr>";
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'>".$nomc."</font></td>";
	 echo "</tr>";
	 echo "</table>";
	 
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 
	 echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'>Adjuntamos a esta relación de envio los documentos, entre facturas y notas, que corresponda a servicios  prestados  a  pacientes  con</font></td>";
	 echo "</tr>";
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='4' text color='#000000'>responsabilidad por parte de ustedes y que detallamos a continuación:</font></td>";
	 echo "</tr>";
	 echo "</table>";
	
	

	$query2="SELECT envdetsec sec,envdetfan fan,envdetdan dan,carfec fec,carhis his,carpac pac,envdetval val,egrpol pol"
           ."  FROM caenvdet,cacar,inmegr"
           ." WHERE envdetfue='".$fue."'"
           ."   AND envdetdoc='".$carg."'"
           ."   AND envdetfan=carfue"
		   ."   AND envdetdan=cardoc"
		   ."   AND carfuo='01'"
           ."   AND carhis=egrhis"
           ."   AND carnum=egrnum"
           ." UNION ALL"
           ." SELECT envdetsec sec,envdetfan fan,envdetdan dan,carfec fec,carhis his,carpac pac,envdetval val,pacpol pol"
           ."  FROM caenvdet,cacar,inpac"
           ." WHERE envdetfue='".$fue."'"
           ."   AND envdetdoc='".$carg."'"
           ."   AND envdetfan=carfue"
		   ."   AND envdetdan=cardoc"
		   ."   AND carfuo='01'"
           ."   AND carhis=pachis"
           ."   AND carnum=pacnum"	
           ." UNION ALL"
           ." SELECT envdetsec sec,envdetfan fan,envdetdan dan,carfec fec,carhis his,carpac pac,envdetval val,movpol pol"
           ."  FROM caenvdet,cacar,aymov"
           ." WHERE envdetfue='".$fue."'"
           ."   AND envdetdoc='".$carg."'"
		   ."   AND envdetfan=carfue"
		   ."   AND envdetdan=cardoc"
           ."   AND carfuo<>'01'"
		   ."   AND carfuo=movfue"
           ."   AND carhis=movdoc"		   
		   ." order by 1,2,3,4,5,6,7,8";
		   
		   
      	    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);
			
  $Num_Filas = 0;	
  $total1=0;
  $total2=0;
	
     echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>FUENTE</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>DOCUMENTO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>FECHA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>HISTORIA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>PACIENTE</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>V A L O R </b></font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>POLIZA</b></font></td>";
	 echo "</tr>";	
	
  while (odbc_fetch_row($err_o2))
   {
	 $Num_Filas++;
	  	
	 $sec2    = odbc_result($err_o2,1);//SECUENCIA
	 $fan2    = odbc_result($err_o2,2);//FTE_FACTURA
	 $dan2    = odbc_result($err_o2,3);//FACTURA
	 $fec2    = odbc_result($err_o2,4);//FECHA
	 $his2    = odbc_result($err_o2,5);//HISTORIA
	 $pac2    = odbc_result($err_o2,6);//PACIENTE
	 $val2    = odbc_result($err_o2,7);//VALOR
	 $pol2    = odbc_result($err_o2,8);//POLIZA
	 
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>".$fan2."</b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>".$dan2."</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>".$fec2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>".$his2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>".$pac2."</font></td>"; 
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#000000'>".number_format($val2,0,'.',',')."</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#000000'>".$pol2."</font></td>"; 
	 echo "</tr>"; 
	
	}
	 echo "<br>";
     echo "<br>"; 
     echo "<br>";
     echo "<br>"; 
     echo "<br>";
     echo "<br>"; 
     echo "<br>";
     echo "<br>"; 
     echo "<br>";
     echo "<br>"; 
     echo "<br>";
	 
	 echo "<tr>";
	 echo "</tr>"; 
	 echo "<tr>";
	 echo "</tr>"; 
	 echo "<tr>";
	 echo "</tr>"; 
	 echo "<tr>";
	 echo "</tr>";
	 
     echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>NUMERO DE DOCUMENTOS:</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>".$cant."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>VALOR TOTAL:  $</b></font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>".number_format($vto,0,'.',',')."</b></font></td>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'>&nbsp;</font></td>";	 
	 echo "</tr>"; 
	
 	 echo "<tr>";
	 echo "</tr>"; 
	 echo "<tr>";
	 echo "</tr>"; 
	 echo "<tr>";
	 echo "</tr>"; 
	 echo "<tr>";
	 echo "</tr>";
	 echo "</table>";
	 
  //==========================================================================
  
  
 	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
	 echo "<br>";
 
  
  echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";  
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#000000'><b>VALOR EN LETRAS: </b>".montoescrito($vto)."</font></td>"; //************************************
  echo "</tr>";  
  echo "</table>";
  
  
  
  odbc_close($conexi);
  odbc_close_all();
  
 }
}
?>

