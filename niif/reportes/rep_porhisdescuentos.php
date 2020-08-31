<html>
<head>
<title>Impresi�n % Historico de Recibos con Descuentos- Unix</title>
</body>
</html>

<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inavi�itado el teclado.
//Esta instrucci�n sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                           IMPRESION % HISTORICO DE RECIBOS CON DESCUENTO PARA NIIF - UNIX                                *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :rep_porhisdescuentos.php                                                                                     |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Septiembre 29 DE 2014.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  :29 de Septiembre de 2014.                                                                                    |
//DESCRIPCION			      :Este programa sirve para imprimir el % historico de recibos con descuento por fecha, despu�s de generar en   |
//                             en unix el programa de phisdescu.4gl                                                                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//amepdesc       : Tabla de Porcentajes de recibos con descuentos en unix.                                                                  | 
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 29-Septiembre-2014";

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
	       or die("No se realizo conexi�n con la BD de Facturaci�n");

	       //Forma
 echo "<form name='forma' action='rep_porhisdescuentos.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' or !isset($tip) or $tip == '' or !isset($pg) or $pg=='-' )
  {
  	echo "<form name='rep_porhisnotacredito' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los par�metros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

	echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>PORCENTAJE HISTORICO RECIBOS DE CAJA CON DESCUENTO</td></tr>";
	
   // SELECT DE LA TABLA ANO - MES Y MESES_ATRAS , PARA SABER QUE ESTA GENERADO
	
   echo "<td align=CENTER colspan=2><size=3><b>PERIODOS GENERADOS<br>(A�O-MES-MESES_ATRAS)</b><br></font></b><select name='pg' id='searchinput'>";
   
   $query = " SELECT pdescano,pdescmes,pdescmesatr"
           ."   FROM amepdesc"
           ."  GROUP BY 1,2,3"
           ."  ORDER BY 1,2,3";
		   
   $err3 = odbc_do($conexi,$query);		

	while (odbc_fetch_row($err3))
   {
		echo "<option>".odbc_result($err3,1)."-".odbc_result($err3,2)."-".odbc_result($err3,3)."</option>";
   }
  
   echo "</select></td>";
   
   //FUENTE Y CARGO DE AYUDAS
 	
	//echo"<tr><td align='CENTER' bgcolor=#cccccc >(C)ODIGO o (N)IT o (T)Todos:</td><td align='CENTER' bgcolor=#cccccc ><input type='input' name='tip'></td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >(C)ODIGO o (N)IT o (T)Todos:</td><td align='CENTER' bgcolor=#cccccc ><select name='tip'><option value='C'>Codigo</option><option value='N'>Nit</option><option value='T'>Todos</option></select></td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >CODIGO,NIT o (*) Todos:</td><td align='CENTER' bgcolor=#cccccc ><input type='input' name='carg'></td></tr>";
	echo "<tr><td align='CENTER' colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>"; //submit osea el boton de GENERAR o Aceptar
    echo "</table>";

    echo '</div>';
    echo '</div>';
    echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {	
  
   $tpg=explode('-',$pg);
   
   $carg = strtoupper($carg);
   $tip  = strtoupper($tip);
   
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PORCENTAJE HISTORICO DE RECIBOS CON DESCUENTOS</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>PERIODO: <i>".$tpg[0]."-".$tpg[1]."-".$tpg[2]."</i></font></td>";
   echo "</tr>";
   echo "</table>";
   
 
  IF (($tip == 'N') and ($carg != '*'))
  {
   $query1="SELECT empcod,empnit"
          ."  FROM inemp"
		  ." WHERE empnit='".$carg."'"
		  ." order by 1,2"
		  ." into temp tmpempre";
		  
    $err_o1 = odbc_do($conexi,$query1);	

	$query2="SELECT pdesemp,pdescres,pvtotdesc,pvtotfact,pdescpor"
	       ."  FROM amepdesc,tmpempre"
		   ." WHERE pdescano='".$tpg[0]."'"
		   ."   AND pdescmes='".$tpg[1]."'"
		   ."   AND pdescmesatr='".$tpg[2]."'"
		   ."   AND pdescres=empcod"
		   ."   AND pdescpor != 0"
		   ." ORDER BY 1,2,3,4";
		   
    $err_o2 = odbc_do($conexi,$query2);
  
   }  
   ELSE // $TIP == 'N' AND $CARG != '*'
   {
     IF (($tip == 'C') and ($carg != '*'))
     {
	  $query2="SELECT pdesemp,pdescres,pvtotdesc,pvtotfact,pdescpor"
	       ."  FROM amepdesc"
		   ." WHERE pdescano='".$tpg[0]."'"
		   ."   AND pdescmes='".$tpg[1]."'"
		   ."   AND pdescmesatr='".$tpg[2]."'"
		   ."   AND pdescres='".$carg."'"
		   ."   AND pdescpor != 0"
		   ." ORDER BY 1,2,3,4";
		   
      $err_o2 = odbc_do($conexi,$query2);
	 }
	 ELSE
	 {
	  IF (($tip == 'T') or ($carg == '*'))
	  {
	    $query2="SELECT pdesemp,pdescres,pvtotdesc,pvtotfact,pdescpor"
	          ."  FROM amepdesc"
		      ." WHERE pdescano='".$tpg[0]."'"
		      ."   AND pdescmes='".$tpg[1]."'"
		      ."   AND pdescmesatr='".$tpg[2]."'"
			  ."   AND pdescpor != 0"
		      ." ORDER BY 1,2,3,4";
		   
        $err_o2 = odbc_do($conexi,$query2);
	  }
	 }
   }
   
  echo "<table border=1 align=center size='100' cellpadding='0'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>TIP_EMP</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CODIGO_EMPRESA</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VLR_TOTAL_DESCUENTO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VLR_TOTAL_FACTURADO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>PORCENTAJE HISTORICO</b></font></td>";
  echo "</tr>";
	
  $Num_Filas = 0;	

  while (odbc_fetch_row($err_o2))
   {
	$Num_Filas++;
	  	
	$emp1     = odbc_result($err_o2,1);//TIPO_EMPRESA
	$cer1     = odbc_result($err_o2,2);//CODIGO_EMPRESA
	$vlrtdes1 = odbc_result($err_o2,3);//VLR TOTAL DESCUENTOS
	$vlrtfac1 = odbc_result($err_o2,4);//VLR TOTAL FACTURADO
	$pporc1   = odbc_result($err_o2,5);//PORCENTAJE HISTORICO
	 
    echo "<tr>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$emp1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:left;'>".$cer1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrtdes1,0,'.',',')."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrtfac1,0,'.',',')."</font></td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".number_format($pporc1,2,'.',',')."%</font></td>";
	echo "</tr>";

   }
  echo "</table>";
 
  // para cerrar la conexion con UNIX.
  //odbc_close( $conexi );
  //odbc_close_all();
  
 }
 
odbc_close($conexi);
odbc_close_all();
}
?>

