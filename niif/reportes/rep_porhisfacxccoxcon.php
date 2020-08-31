<html>
<head>
<title>Impresión % Historico Facturas Por Centro de Costos - Unix</title>
</body>
</html>

<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                           IMPRESION % HISTORICO FACTURAS X CENTRO DE COSTOS X CONCEPTO PARA NIIF - UNIX                  *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :rep_porhisfacxccoxcon.php                                                                                    |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Noviembre 11 DE 2014.                                                                                        |
//FECHA ULTIMA ACTUALIZACION  :11 de Noviembre de 2014.                                                                                     |
//DESCRIPCION			      :Este programa sirve para imprimir el % historico de facturas x centro de costos y concepto, después de       |
//                             generar en unix el programa de phisfxcon.4gl                                                                 |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//amepfxcconn        : Tabla de Porcentajes de facturacion x centro costo x concepto.                                                        | 
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 11-Noviembre-2014";

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
 echo "<form name='forma' action='rep_porhisfacxccoxcon.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' or !isset($tip) or $tip == '' or !isset($pg) or $pg=='-' )
  {
  	echo "<form name='rep_porhisfacxccoxcon' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

	echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>PORCENTAJE HISTORICO FACTURAS POR ENTIDAD-CENTRO DE COSTOS-CONCEPTO</td></tr>";
	
   // SELECT DE LA TABLA ANO - MES Y MESES_ATRAS , PARA SABER QUE ESTA GENERADO
	
   echo "<td align=CENTER colspan=2><size=3><b>PERIODOS GENERADOS<br>(AÑO-MES-MESES_ATRAS)</b><br></font></b><select name='pg' id='searchinput'>";
   
   $query = " SELECT cconano,cconmes,cconmatr"
           ."   FROM amepfxccon"
           ."  GROUP BY 1,2,3"
           ."  ORDER BY 1,2,3";
		   
   $err3 = odbc_do($conexi,$query);		

	while (odbc_fetch_row($err3))
   {
		echo "<option>".odbc_result($err3,1)."-".odbc_result($err3,2)."-".odbc_result($err3,3)."</option>";
   }
  
   echo "</select></td>";
   
   //CODIGO O NIT A BUSCAR
 	
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PORCENTAJE HISTORICO FACTURAS POR ENTIDAD-CENTRO DE COSTOS-CONCEPTOS</b></font></td>";
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

	$query2="SELECT cconemp,cconcer,cconcco,cconcon,cconvlrc,cconvlrt,cconporc"
	       ."  FROM amepfxccon,tmpempre"
		   ." WHERE cconano='".$tpg[0]."'"
		   ."   AND cconmes='".$tpg[1]."'"
		   ."   AND cconmatr='".$tpg[2]."'"
		   ."   AND cconcer=empcod"
		   ." ORDER BY 1,2,3,4";
		   
    $err_o2 = odbc_do($conexi,$query2);
  
   }  
   ELSE // $TIP == 'N' AND $CARG != '*'
   {
     IF (($tip == 'C') and ($carg != '*'))
     {
	  $query2="SELECT cconemp,cconcer,cconcco,cconcon,cconvlrc,cconvlrt,cconporc"
	       ."  FROM amepfxccon"
		   ." WHERE cconano='".$tpg[0]."'"
		   ."   AND cconmes='".$tpg[1]."'"
		   ."   AND cconmatr='".$tpg[2]."'"
		   ."   AND cconcer='".$carg."'"
		   ." ORDER BY 1,2,3,4";
		   
      $err_o2 = odbc_do($conexi,$query2);
	 }
	 ELSE
	 {
	  IF (($tip == 'T') or ($carg == '*'))
	  {
	    $query2="SELECT cconemp,cconcer,cconcco,cconcon,cconvlrc,cconvlrt,cconporc"
	          ."  FROM amepfxccon"
		      ." WHERE cconano='".$tpg[0]."'"
		      ."   AND cconmes='".$tpg[1]."'"
		      ."   AND cconmatr='".$tpg[2]."'"
			  ." ORDER BY 1,2,3,4";
		   
        $err_o2 = odbc_do($conexi,$query2);
	  }
	 }
   }
   
  echo "<table border=1 align=center size='100' cellpadding='0'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>TIP_EMP</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CODIGO_EMPRESA</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CCOSTO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CONCEPTO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VLR_TOTAL_CLINICA</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VALOR_TOTAL_FACT</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>PORCENTAJE HISTORICO</b></font></td>";
  echo "</tr>";
	
  $Num_Filas = 0;	

  while (odbc_fetch_row($err_o2))
   {
	$Num_Filas++;
	  	
	$emp1     = odbc_result($err_o2,1);//TIPO_EMPRESA
	$cer1     = odbc_result($err_o2,2);//CODIGO_EMPRESA
	$cco1     = odbc_result($err_o2,3);//CENTRO DE COSTOS
	$con1     = odbc_result($err_o2,4);//CONCEPTO
	$vlrtcli1 = odbc_result($err_o2,5);//VLR TOTAL CLINICA
	$vlrtfac1 = odbc_result($err_o2,6);//VLR TOTAL FACTURAS
	$pporc1   = odbc_result($err_o2,7);//PORCENTAJE HISTORICO
	 
    echo "<tr>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$emp1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:left;'>".$cer1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$cco1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$con1."</font></td>";   
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrtcli1,0,'.',',')."</font></td>";
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

