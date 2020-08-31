<html>
<head>
<title>Impresión % Historico Nota Crédito Aceptada - Unix</title>
<body>
<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                           IMPRESION % HISTORICO DE NOTAS CREDITO ACEPTADA PARA NIIF - UNIX                               *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :rep_porhisnotacredito.PHP                                                                                    |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Septiembre 25 DE 2014.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  :26 de Noviembre de 2014.                                                                                     |
//DESCRIPCION			      :Este programa sirve para imprimir el % historico de nota crédito acepatada por fecha, después de generar en  |
//                             en unix el programa de phisnotac.4gl                                                                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//amepnotac       : Tabla de Porcentajes de notas aceptadas en unix.                                                                        | 
//MODIFICACIONES  :                                                                                                                         |
//                 2014-11-26: Se pide que traiga el valor del tercero y el propio de las notas.                                            |
//                 2015-01-26: Se solicita otrs 2 columnas con el % historico tercero, y el % historico propio                              | 
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 26-Noviembre-2014";

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
 echo "<form name='forma' action='rep_porhisnotacredito.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' or !isset($tip) or $tip == '' or !isset($pg) or $pg=='-' )
  {
  	echo "<form name='rep_porhisnotacredito' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

	echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>PORCENTAJE HISTORICO NOTAS CRÉDITO ACEPTADAS</td></tr>";
	
   // SELECT DE LA TABLA ANO - MES Y MESES_ATRAS , PARA SABER QUE ESTA GENERADO
	
   echo "<td align=CENTER colspan=2><size=3><b>PERIODOS GENERADOS<br>(AÑO-MES-MESES_ATRAS)</b><br></font></b><select name='pg' id='searchinput'>";
   
   $query = " SELECT pano,pmes,pmat"
           ."   FROM amepnotac"
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PORCENTAJE HISTORICO NOTAS CREDITO ACEPTADAS</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>PERIODO: <i>".$tpg[0]."-".$tpg[1]."-".$tpg[2]."</i></font></td>";
   echo "</tr>";
   echo "</table>";
   
 
  IF (($tip == 'N') and ($carg != '*'))
  {
   $query1="SELECT empcod,empnit,empnom"
          ."  FROM inemp"
		  ." WHERE empnit='".$carg."'"
		  ." order by 1,2"
		  ." into temp tmpempre";
		  
    $err_o1 = odbc_do($conexi,$query1);	

	$query2="SELECT pemp,pcer,empnom,pcco,pcon,pvlrtnot,pvlrtern,pvlrpron,pvlrtfac,pporc"
	       ."  FROM amepnotac,tmpempre"
		   ." WHERE pano='".$tpg[0]."'"
		   ."   AND pmes='".$tpg[1]."'"
		   ."   AND pmat='".$tpg[2]."'"
		   ."   AND pcer=empcod"
	    // ."   AND pporc!=0"
		   ." ORDER BY 1,2,3,4,5";
		   
    $err_o2 = odbc_do($conexi,$query2);
  
   }  
   ELSE // $TIP == 'N' AND $CARG != '*'
   {
     IF (($tip == 'C') and ($carg != '*'))
     {
	  $query2="SELECT pemp,pcer,empnom,pcco,pcon,pvlrtnot,pvlrtern,pvlrpron,pvlrtfac,pporc"
	       ."  FROM amepnotac,inemp"
		   ." WHERE pano='".$tpg[0]."'"
		   ."   AND pmes='".$tpg[1]."'"
		   ."   AND pmat='".$tpg[2]."'"
		   ."   AND pcer='".$carg."'"
		   ."   AND pcer=empcod"
	 //    ."   AND pporc!=0"
		   ." ORDER BY 1,2,3,4,5";
		   
      $err_o2 = odbc_do($conexi,$query2);
	 }
	 ELSE
	 {
	  IF (($tip == 'T') or ($carg == '*'))
	  {
	    $query2="SELECT pemp,pcer,empnom,pcco,pcon,pvlrtnot,pvlrtern,pvlrpron,pvlrtfac,pporc"
	          ."  FROM amepnotac,inemp"
		      ." WHERE pano='".$tpg[0]."'"
		      ."   AND pmes='".$tpg[1]."'"
		      ."   AND pmat='".$tpg[2]."'"
		//    ."   AND pporc!=0"
			  ."   AND pemp='E'"
			  ."   AND pcer=empcod"
			  ." UNION ALL"
			  ." SELECT pemp,pcer,'PARTICULAR' empnom,pcco,pcon,pvlrtnot,pvlrtern,pvlrpron,pvlrtfac,pporc"
	          ."  FROM amepnotac"
		      ." WHERE pano='".$tpg[0]."'"
		      ."   AND pmes='".$tpg[1]."'"
		      ."   AND pmat='".$tpg[2]."'"
		//    ."   AND pporc!=0"
			  ."   AND pemp='P'"
			  ." ORDER BY 1,2,3,4,5";
		   
        $err_o2 = odbc_do($conexi,$query2);
	  }
	 }
   }
   
  echo "<table border=1 align=center size='100' cellpadding='0'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>TIP_EMP</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CODIGO_EMPRESA</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>NOMBRE_RESPONSABLES</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CCOSTO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>CONC</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VALOR_TOTAL NOTAS</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VALOR_TERCERO NOTAS</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VALOR_PROPIO NOTAS</b></font></td>";	
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>VALOR_TOTAL FACTURADO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>PORC HISTORICO TERCEROS</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>PORC HISTORICO PROPIO</b></font></td>";
  echo "<td align='CENTER' bgcolor='#FFFFFF' ><font size='2' text color='#003366'><b>PORC HISTORICO</b></font></td>";
  echo "</tr>";
	
  $Num_Filas = 0;	

  while (odbc_fetch_row($err_o2))
   {
	$Num_Filas++;
	  	
	$emp1     = odbc_result($err_o2,1);//TIPO_EMPRESA
	$cer1     = odbc_result($err_o2,2);//CODIGO_EMPRESA
	$nome1    = odbc_result($err_o2,3);//NOMBRE_RESPONSABLE
    $cco1     = odbc_result($err_o2,4);//CENTRO DE COSTOS
	$con1     = odbc_result($err_o2,5);//CONCEPTO
	$vlrtnot1 = odbc_result($err_o2,6);//VLR TOTAL   NOTAS
	$vlrtern1 = odbc_result($err_o2,7);//VLR TERCERO NOTAS
	$vlrpron1 = odbc_result($err_o2,8);//VLR PROPIO  NOTAS
	$vlrtfac1 = odbc_result($err_o2,9);//VLR TOTAL FACTURADO
	$pporc1   = odbc_result($err_o2,10);//PORCENTAJE HISTORICO
	 
    echo "<tr>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$emp1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:left;'>".$cer1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:left;'>".$nome1."</font></td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$cco1."</font></td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".$con1."</font></td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrtnot1,0,'.',',')."</td>";
    echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrtern1,2,'.',',')."</td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrpron1,2,'.',',')."</td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format($vlrtfac1,0,'.',',')."</td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format(($vlrtern1/$vlrtfac1)*100,2,'.',',')."%</td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:right;'>".number_format(($vlrpron1/$vlrtfac1)*100,2,'.',',')."%</td>";
	echo "<td bgcolor=#FFFFFF style='color:#003366; font-size:12px; text-align:center;'>".number_format($pporc1,2,'.',',')."%</td>";
	echo "</tr>";

   }
  echo "</table>";
 
   
 }
  // para cerrar la conexion con UNIX.
	odbc_close($conexi);
	odbc_close_all();
	
}
?>
</body>
</html>

