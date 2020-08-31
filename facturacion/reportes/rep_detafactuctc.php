<html>
<head>
<title>MATRIX - [REPORTE DETALLE DE UNA FACTURA CTC - MEDICAMENTOS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_detafactuctc.php'; 
	}
	
	function enter()
	{
		document.forms.rep_detafactuctcctc.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PARA VER EL DETALLE DE UNA FACTURA CTC                                               *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver el detalle de una factura en unix.                                                         |
//AUTOR				          :Ing. Gustavo Alberto Avendaño Rivera.                                                                       |
//FECHA CREACION			  :MAYO 17 DE 2016.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :17 de Mayo de 2016.                                                                                      |
//TABLAS UTILIZADAS   :                                                                                                                    | 
//                                                                                                                                         |
//facarfac  en unix   : Tabla de Facturas x cargo.                                                                                         |
//facardet  en unix   : Tabla de Cargos.                                                                                                   |
//ivdrodet  en unix   : Tabla de detalle de cargos de Medicamentos.                                                                        |
//ivart     en unix   : Tabla de Maestro de Articulos.                                                                                     |
//ivuni     en unix   : Tabla de unidad de medida.                                                                                         |
//                                                                                                                                         |
//==========================================================================================================================================
include_once("root/comun.php");
$conex     = obtenerConexionBD("matrix");
$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz = "1.0 17-Mayo-2016";

$empresa='root';

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
encabezado("DETALLE DE MATERIALES Y MEDICAMENTOS DE UNA FACTURA - CTC",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_detafactuctc.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fte) or $fte=='' or !isset($fac) or $fac == '' )
  {
  	
  	echo "<form name='rep_detafactuctc' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

 	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";
 	 	
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Fuente Factura
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>FUENTE<i><br></font><bgcolor='#dddddd' aling=center><input type='input' size=5 maxlength=10 name='fte'></td>";
   echo "</Tr>";
   
   if (isset($fte))
   {
    $fte=$fte;
   }
   else 
   {
    $fte='';	
   }
  
      
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Nro Factura
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>FACTURA NUMERO<i><br></font><bgcolor='#dddddd' aling=center><input type='input' name='fac' size=9 maxlength=10 id='fac'></td>";
   echo "</Tr>";
   
   if (isset($fac))
   {
    $fac=$fac;
   }
   else 
   {
    $fac='';	
   }
      
     
  
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>"; //submit osea el boton de Generar o Aceptar
   echo "</tr>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
	
  $query_o3=" SELECT carfuo,carhis,carnum,carpac,pacinftip tip,carcep"
             ."  FROM cacar,inpacinf"
             ." WHERE carfue='$fte'"
             ."   AND cardoc=$fac"
             ."   AND carhis=pacinfhis"
             ."   AND carnum=pacinfnum"
			 ."   AND carfuo='01'"
			 ."   AND carhis is not null"
             ." GROUP BY 1,2,3,4,5,6"
			 ." UNION ALL "
			 ." SELECT carfuo,carhis,carnum,carpac,movtid tip,carcep"
             ."   FROM cacar,aymov"
             ."  WHERE carfue='$fte'"
             ."    AND cardoc=$fac"
             ."    AND carfuo=movfue"
             ."    AND carhis=movdoc"
			 ."    AND carfuo<>'01'"
			 ."    AND carhis is not null"
             ."  GROUP BY 1,2,3,4,5,6"
			 ." UNION ALL "
			 ." SELECT carfuo,0 carhis,0 carnum,carpac,' ' tip,' ' carcep"
             ."   FROM cacar"
             ."  WHERE carfue='$fte'"
             ."    AND cardoc=$fac"
             ."    AND carfuo<>'01'"
			 ."    AND carhis is null"
             ."  GROUP BY 1,2,3,4,5,6"
             ."  ORDER BY 1,2,3";
	
	$err_o = odbc_do($conex_o,$query_o3);
			
   $Num_Filas = 0;
   
   while (odbc_fetch_row($err_o))
	  {
	  	$Num_Filas++;
	  	
	  	$fuo    = odbc_result($err_o,1);//fuo
		$his    = odbc_result($err_o,2);//historia
		$num    = odbc_result($err_o,3);//ingreso
		$pac    = odbc_result($err_o,4);//paciente
		$tip    = odbc_result($err_o,5);//tipo indentificacion
		$cep    = odbc_result($err_o,6);//cedula paciente
	  }
  	
  	
   	echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>FACTURA</b></font></td>";
	echo "</tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fac."</b></font></td>";
	echo "</table>";
	
	echo "<br>";
	
	echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FUO-AYUDA</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>HISTORIA</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>INGRESO</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>PACIENTE</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TIPO_IDEN</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>IDENTIFICACION</b></font></td>";
	echo "</tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fuo."</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$his."</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$num."</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$pac."</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tip."</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>&nbsp;</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cep."</b></font></td>";
	echo "</table>";
	
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CASO NUMERO DE CTC</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>LOTE</font></td>";	
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANTIDAD</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>FACTURA</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR_LOTE</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR_FACTURA</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_PACIENTE</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TIPO_DOCUMENTO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DOCUMENTO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>FECHA_INGRESO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_PRESTADOR</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TIPO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DETALLE DEL CASO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>ESTADO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_MEDICAMENTO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>OBSERVACIONES</font></td>";
	echo "</tr>";


	
    $query_o1="SELECT cardetcan,movdoc,cardetvun,carfacval,carpac,pactid,pacced,egring,'HOSPITARIO' tipo,artnom"
	         ."  FROM famov,facarfac,cacar,facardet,inmegr,ivdrodet,ivart,inpaci"
             ." WHERE movfue='$fte'"
             ."   AND movdoc=$fac"
             ."   AND movfue=carfacfue"
             ."  AND movdoc=carfacdoc"
             ."  AND movfue=carfue"
             ."  AND movdoc=cardoc"
             ."  AND carfacreg=cardetreg"
             ."  AND cardetfue=drodetfue"
             ."  AND cardetdoc=drodetdoc"
             ."  AND cardetite=drodetite"
             ."  AND drodetart=artcod"
             ."  AND cardethis=pachis"
             ."  AND cardethis=egrhis"
             ."  AND cardetnum=egrnum"
             ."  AND egrhos='H'"
             ." union all"
             ." SELECT cardetcan,movdoc,cardetvun,carfacval,carpac,pactid,pacced,egring,'AMBULATORIO' tipo,artnom"
             ."   FROM famov,facarfac,cacar,facardet,inmegr,ivdrodet,ivart,inpaci"
			 ." WHERE movfue='$fte'"
             ."   AND movdoc=$fac"
             ."   AND movfue=carfacfue"
             ."   AND movdoc=carfacdoc"
             ."   AND movfue=carfue"
             ."   AND movdoc=cardoc"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetart=artcod"
             ."   AND cardethis=pachis"
             ."   AND cardethis=egrhis"
             ."   AND cardetnum=egrnum"
             ."   AND egrhos<>'H'"
             ." ORDER by 10"
             ." INTO temp tmppctc";
	
			   
	//echo $query_o."<br>";
	$err_o = odbc_do($conex_o,$query_o1);
	
	$query_o11="SELECT cardetcan*-1 cant,movdoc,cardetvun,carfacval,carpac,pactid,pacced,egring,tipo,artnom"
              ."  FROM tmppctc"
              ." WHERE carfacval<0"
              ." UNION ALL"
              ." SELECT cardetcan cant,movdoc,cardetvun,carfacval,carpac,pactid,pacced,egring,tipo,artnom"
              ."  FROM tmppctc"
              ." WHERE carfacval>=0"
              ." ORDER by 10"
              ." into temp tmppctc1";
		
	//echo $query_o."<br>";
	$err_o = odbc_do($conex_o,$query_o11);		  
			  

    $query_o12="SELECT sum(cant) cant,movdoc,cardetvun,sum(carfacval) val,carpac,pactid,pacced,egring,tipo,artnom"
              ."  FROM tmppctc1"
              ." GROUP BY 2,3,5,6,7,8,9,10"
              ." ORDER BY 10"
              ." into temp tmppctc2";
			  
    //echo $query_o."<br>";
	$err_o = odbc_do($conex_o,$query_o12);

     $query_o2="SELECT ' ' nctc,' ' lote,cant,movdoc,cardetvun,val,carpac,pactid,pacced,egring,'CLINICA LAS AMERICAS' nompres,tipo,' ' detcaso,' ' estado, artnom,' ' observa"
                ."  FROM tmppctc2"
                ." WHERE cant<>0"
	            ." order by 15";
				
	$err_o = odbc_do($conex_o,$query_o2);
			
   $Num_Filas = 0;
   
   while (odbc_fetch_row($err_o))
	  {
	  	$Num_Filas++;
	  	
	  	$nctc    = odbc_result($err_o,1);//Caso numero ctc
	  	$lote    = odbc_result($err_o,2);//Lote
		$cant    = odbc_result($err_o,3);//Cantidad articulo
		$doc     = odbc_result($err_o,4);//Número factura
		$vun     = odbc_result($err_o,5);//Valor_unitario
		$val     = odbc_result($err_o,6);//Valor_factura
		$pac     = odbc_result($err_o,7);//Paciente
	    $tid     = odbc_result($err_o,8);//Tipo documento
	    $ced     = odbc_result($err_o,9);//Documento
		$ing     = odbc_result($err_o,10);//Fecha_ingreso
		$nres    = odbc_result($err_o,11);//Nombre_responsable
		$tipo    = odbc_result($err_o,12);//Tipo
		$detc    = odbc_result($err_o,13);//Detalle Caso
		$est     = odbc_result($err_o,14);//Estado
		$artn    = odbc_result($err_o,15);//Nombre_articulo
		$obser   = odbc_result($err_o,16);//Observacion
		
      
    	    echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nctc."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$lote."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($cant)."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$doc."</font></td>";
			echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
			echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($val)."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$pac."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$tid."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$ced."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$ing."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nres."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$tipo."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$detc."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$est."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$artn."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$obser."</font></td>";

		    echo "</tr>";
	  }
  
 }
   
	 echo "<br>"; 
  
}
odbc_close($conex_o);
odbc_close_all();   
?>