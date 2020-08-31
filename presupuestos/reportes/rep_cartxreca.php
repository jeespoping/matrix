<html>
<head>
<title>MATRIX - [REPORTE CARTERA DE PARTICULARES X RECAUDO]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_cartxreca.php'; 
	}
	
	function enter()

	{
	 document.forms.rep_cartxreca.submit();
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
*                                             REPORTE CARTERA X RECAUDO                                                                    *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver lo recaudado de cartera particulares x año mes                                             |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : SEPTIEMBRE 03 DE 2010.                                                                                      |
//FECHA ULTIMA ACTUALIZACION  : SEPTIEMBRE 03 DE 2010.                                                                                      |
//DESCRIPCION			      : Este reporte sirve para observar lo recaudado en notas credito y recibos de facturas particulares.          |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//casal             : Tabla de Saldos de cartera.                                                                                           |
//famov             : Tabla de Movimiento de Notas y Facturas.                                                                              |
//cacar             : Tabla de Movimiento de facturas, Notas y Recibos.                                                                     |
//                                                                                                                                          |
// Fecha_modificación :  2013-04-08 se adiciona el valor de las facturas que en el mes tuvieron recaudo con mas de 1 día.                   |                                                                                                                                      |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="2.0 08-Abril-2013";

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
encabezado("Recaudado x Meses de la Cartera de Particulares",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_cartxreca.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')
  {
  	echo "<form name='rep_cartxreca' action='' method=post>";
  
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
 	echo "<td class='fila2' align='center' width=150>";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";
 	
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de GENERAR o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
  	
  	$mesi="01";
 	$mesf=SUBSTR("$fec2",5,2);
 	$mesa=$mesf-01;
    
 	$anoi=SUBSTR("$fec2",0,4);
 	$anoa=$anoi-1;
 	
 	if ($mesf=='01')
 	{
 	 $mesa='12';
 	 $anoaa=$anoa;
 	}	
    else
    {
     $anoaa=$anoi; 	
    }	
 	
    
    switch ($mesa)  
	{
	 case "1":   
	  {
       $mesa='01';
       break;
      }	
    case "2":    
	  {
       $mesa='02';
       break;
      }	
   case "3":    
	  {
       $mesa='03';
       break;
      }	
    case "4":    
	  {
       $mesa='04';
       break;
      }	
      
      case "5":   
	  {
       $mesa='05';
       break;
      }	
      case "6":    
	  {
       $mesa='06';
       break;
      }	
      
      case "7":    
	  {
       $mesa='07';
       break;
      }	
      case "8":    
	  {
       $mesa='08';
       break;
      }	
      case "9":   
	  {
       $mesa='09';
       break;
      }	
	}
	
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
    echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='300'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>CARTERA DE PARTICULARES - RECAUDO</b></font></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
    echo "<tr>";
    echo "</table>";
      
    // Saldo del mes anterior de las facturas de particulares}
    $query1=" SELECT salfue ffue,saldoc ffac,salfec ffec, salval fval,salmes mes"
           ."  FROM casal"
           ." WHERE salfue = '20' "
           ."   AND salcco not in ('1660')"
           ."   AND salano = '".$anoa."'"
           ."   AND salmes = '12' "
           ."   AND salind = 'P' "
           ."  INTO TEMP tmpf";

   //echo odbc_error() ."=". odbc_errormsg();
 
   $err1 = odbc_do($conexi,$query1);

           
   // Saldo del mes anterior de las facturas de particulares}
   $query2=" SELECT salfue ffue,saldoc ffac,salfec ffec, salval fval,salmes mes"
          ."   FROM casal"
          ."  WHERE salfue = '20' "
          ."    AND salcco not in ('1660')"
          ."    AND salano ='".$anoaa."'"
          ."    AND salmes ='".$mesa."'"
          ."    AND salind = 'P' "
          ."   INTO TEMP tmpsa";
    
   //echo odbc_error() ."=". odbc_errormsg();
 
   $err2 = odbc_do($conexi,$query2);
   
   
   // Saldo del mes actual de las facturas de particulares}
   $query3=" SELECT salfue ffue,saldoc ffac,salfec ffec, salval fval,salmes mes"
          ."   FROM casal"
          ."  WHERE salfue = '20'"
          ."    AND salcco not in ('1660')"
          ."    AND salano = '".$anoi."'"
          ."    AND salmes = '".$mesf."'"
          ."    AND salind = 'P' "
          ."   INTO TEMP tmpac";

   $err3 = odbc_do($conexi,$query3);
   
   
   // Saldo del mes actual de las facturas del mismo mes}
   $query33=" SELECT salfue ffue,saldoc ffac,salfec ffec, salval fval,salmes mes"
          ."   FROM casal"
          ."  WHERE salfue = '20'"
          ."    AND salcco not in ('1660')"
          ."    AND salano = '".$anoi."'"
          ."    AND salmes = '".$mesf."'"
		  ."    AND salfec between '".$fec1."' and '".$fec2."'"
          ."    AND salind = 'P' "
          ."   INTO TEMP tmpsalm";

   $err33 = odbc_do($conexi,$query33);
   
   
    // FACTURADO ACUMULADO DE LOS MESES QUE NECESITO }
   $query4=" SELECT carfue ffue,cardoc ffac,carfec ffec, carval fval,movmes mes"
          ."   FROM famov,cacar"
          ."  WHERE movfue = '20'"
          ."    AND carcco not in ('1660')"
          ."    AND movano = '".$anoi."'"
          ."    AND movmes  between '".$mesi."' and '".$mesf."'"
          ."    AND movfue = carfue"
          ."    AND movdoc = cardoc"
          ."    AND carind = 'P'"
          ."    AND caranu = '0'"
          ."    AND movanu = '0'"
          ."    INTO TEMP tmpfa";
    
    $err4 = odbc_do($conexi,$query4);
    
    // FACTURADO DEL MISMO MES}
    $query5=" SELECT carfue ffue,cardoc ffac,carfec ffec, carval fval,movmes mes"
           ."   FROM famov,cacar"
           ."  WHERE movfue = '20'"
           ."    AND carcco not in ('1660')"
           ."    AND movano = '".$anoi."'"
           ."    AND movmes = '".$mesf."'"
           ."    AND movfue = carfue"
           ."    AND movdoc = cardoc"
           ."    AND carind = 'P'"
           ."    AND caranu = '0'"
           ."    AND movanu = '0'"
           ."   INTO TEMP tmpf1";

    $err5 = odbc_do($conexi,$query5);
	
	// Tomo todos los doctos que afectan la factura del mismo mes y que tengan mas de 0 dias
    // despues de haber generado la factura 

    $query55=" SELECT fval, sum(carval) valfac,carmes mes"
           ."   FROM tmpf1,cacar"
           ."  WHERE carfue in ('27','28','30','31')"
           ."    AND carfca =  ffue"
           ."    AND carfac =  ffac"
           ."    AND caranu =  '0'"
           ."    AND (carfec-ffec)>0"
           ."    AND carano = '".$anoi."' "
           ."    AND carmes = '".$mesf."' "
           ."    AND carfec between '".$fec1."' and '".$fec2."'"
           ."  GROUP BY 1,3"
           ."   INTO TEMP tmp55";

    $err55 = odbc_do($conexi,$query55);           

	// Tomo todos los doctos que afectan la factura y que tengan mas de 0 dias
    // despues de haber generado la factura }

    $query6=" SELECT carfca fca,carfac fac, ffec fecfac, carval valfac,carmes mes"
           ."   FROM tmpf,cacar"
           ."  WHERE carfue in ('27','28')"
           ."    AND carfca =  ffue"
           ."    AND carfac =  ffac"
           ."    AND caranu =  '0'"
           ."    AND (carfec-ffec)>0"
           ."    AND carano = '".$anoi."' "
           ."    AND carmes = '".$mesf."' "
           ."    AND carfec between '".$fec1."' and '".$fec2."'"
           ."  GROUP BY 1,2,3,4,5"
           ."   INTO TEMP tmp";

    $err6 = odbc_do($conexi,$query6);           

    // tomo los documentos que afectan la factura con su valor }

    $query7=" SELECT carfue,cardoc,carfca fca,carfac fac, ffec fecfac, carval valfac,carmes mes"
           ."   FROM tmpf,cacar"
           ."  WHERE carfue in ('30','31')"
           ."    AND carfca =  ffue"
           ."    AND carfac =  ffac"
           ."    AND caranu =  '0'"
           ."    AND (carfec-ffec)>0"
           ."    AND carano = '".$anoi."'"
           ."    AND carmes = '".$mesf."'"
           ."    AND carfec between '".$fec1."' and '".$fec2."'"
           ."  GROUP BY 1,2,3,4,5,6,7"
           ."   INTO TEMP tmp1";

     $err7 = odbc_do($conexi,$query7);           
           
           
           
  // Tomo todos los doctos que afectan la factura y que tengan mas de 1 dia
  // Despues de haber generado la factura }

   $query8="select carfca fca,carfac fac, ffec fecfac, carval valfac,carmes mes"
          ."  FROM tmpfa,cacar"
          ." WHERE carfue in ('27','28')"
          ."   AND carfca =  ffue"
          ."   AND carfac =  ffac"
          ."   AND caranu =  '0'"
          ."   AND (carfec-ffec)>0"
          ."   AND carano = '".$anoi."'"
          ."   AND carmes = '".$mesf."'"
          ."   AND carfec between '".$fec1."' and '".$fec2."'"
          ." GROUP BY 1,2,3,4,5"
          ." UNION ALL"
          ." SELECT fca,fac,fecfac,valfac,mes"
          ."   FROM tmp"
          ."   INTO TEMP tmp03";
           
   $err8 = odbc_do($conexi,$query8);           

   
 
   
   // tomo los documentos que afectan la factura con su valor }

   $query9=" SELECT carfue,cardoc,carfca fca,carfac fac, ffec fecfac, carval valfac,carmes mes"
           ."  FROM tmpfa,cacar"
           ." WHERE carfue in ('30','31')"
           ."   AND carfca =  ffue"
           ."   AND carfac =  ffac"
           ."   AND caranu =  '0'"
           ."   AND (carfec-ffec)>0"
          ."    AND carano = '".$anoi."'"
          ."    AND carmes = '".$mesf."'"
          ."    AND carfec between '".$fec1."' and '".$fec2."'"
          ."  GROUP BY 1,2,3,4,5,6,7"
          ." UNION ALL"
          ." SELECT carfue,cardoc,fca,fac, fecfac,valfac,mes"
          ."   FROM tmp1"
          ."   INTO TEMP tmp13";
   
   $err9 = odbc_do($conexi,$query9);           

   // VLR SALDOS FACTURAS DEL MES ANTERIOR 

   $query10=" SELECT mes,sum(fval)"
           ."   FROM tmpsa"
           ."  GROUP BY 1";
    
   $err10 = odbc_do($conexi,$query10);        
   
   // VLR FACTURADO EN EL MES 

   $query11="SELECT mes,sum(fval)"
           ."  FROM tmpf1"
           ." GROUP BY 1";
    
   $err11 = odbc_do($conexi,$query11);   
           
   // VLR FACTURADO EN EL MES CON MAS DE 1 DIA CARTERA. 

   $query555="SELECT mes,sum(valfac)"
           ."  FROM tmp55"
           ." GROUP BY 1";
    
   $err555 = odbc_do($conexi,$query555);   
		   
		   
   // VLR NOTAS CREDITO EN EL MES

   $query12="SELECT mes,sum(valfac)"
           ."  FROM tmp03"
           ." GROUP BY 1";
           
   $err12 = odbc_do($conexi,$query12);   
           

   // VLR TOTAL RECIBOS EN EL MES
   
   $query13=" SELECT mes,sum(valfac)"
             ."   FROM tmp13"
             ."  GROUP BY 1";
             
   $err13 = odbc_do($conexi,$query13);
    
	
	// VLR SALDOS FACTURAS DEL MISMO MES

   $query141=" SELECT mes,sum(fval)"
           ."   FROM tmpsalm"
           ."  GROUP BY 1";
   
    $err141 = odbc_do($conexi,$query141);
	
	
  // VLR SALDOS FACTURAS 

   $query14=" SELECT mes,sum(fval)"
           ."   FROM tmpac"
           ."  GROUP BY 1";
   
    $err14 = odbc_do($conexi,$query14);
   
  
  $num10 = odbc_num_fields($err10);
   
  $row10=array();
  
  for ($i=1;$i<=$num10;$i++)
   {	
	$row10[$i-1] = odbc_result($err10,$i);
   }

  $num11 = odbc_num_fields($err11);
   
  $row11=array();
  
  for ($i=1;$i<=$num11;$i++)
   {	
	$row11[$i-1] = odbc_result($err11,$i);
   }
   
  $num555 = odbc_num_fields($err555);
   
  $row555=array();
  
  for ($i=1;$i<=$num555;$i++)
   {	
	$row555[$i-1] = odbc_result($err555,$i);
   }
   
   
  $num12 = odbc_num_fields($err12);
   
  $row12=array();
  
  for ($i=1;$i<=$num12;$i++)
   {	
	$row12[$i-1] = odbc_result($err12,$i);
   }
   
  $num13 = odbc_num_fields($err13);
   
  $row13=array();
  
  for ($i=1;$i<=$num13;$i++)
   {	
	$row13[$i-1] = odbc_result($err13,$i);
   }
   
  $num141 = odbc_num_fields($err141);
   
  $row141=array();
  
  for ($i=1;$i<=$num141;$i++)
   {	
	$row141[$i-1] = odbc_result($err141,$i);
   }
   
   
  $num14 = odbc_num_fields($err14);
   
  $row14=array();
  
  for ($i=1;$i<=$num14;$i++)
   {	
	$row14[$i-1] = odbc_result($err14,$i);
   }
   
  echo "<table border=1 cellspacing=0 cellpadding=0 align=center size='300'>"; 
  echo "<tr>";
  echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2></td>";
  echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>MES</b></td>";
  echo "<td bgcolor='#FFFFFF' align=center><font text color=#000000 size=2><b>VALOR</b></td>";
  echo "</tr>";	
   
   
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR SALDOS FACTURAS DEL MES ANTERIOR</font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row10[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row10[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR FACTURADO EN EL MES</font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row11[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row11[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR FACTURADO EN EL MES RECAUDO > 1 DÍA</font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row555[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row555[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR NOTAS CREDITOS EN EL MES</font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row12[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row12[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR TOTAL RECIBOS EN EL MES</font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row13[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row13[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR SALDOS FACTURAS MISMO MES</font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row141[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row141[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  
  
  echo "<Tr >";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR SALDOS FACTURAS </font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row14[0]</b></font></td>";
  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format($row14[1])."</b></font></td>";
  echo "</tr>";
   
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  echo "<Tr >";
  echo "</tr>";
  
  echo "</table>";  
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
  } // cierre del else donde empieza la impresión

}
?>