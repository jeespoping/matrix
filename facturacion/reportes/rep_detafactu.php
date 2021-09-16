<html>
<head>
<title>MATRIX - [REPORTE PARA VER EL DETALLE DE UNA FACTURA]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_detafactu.php'; 
	}
	
	function enter()
	{
		document.forms.rep_detafactu.submit();
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php

/*******************************************************************************************************************************************
*                                             REPORTE PARA VER EL DETALLE DE UNA FACTURA                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver el detalle de una factura en unix.                                                         |
//AUTOR				          :Ing. Gustavo Alberto Avendaño Rivera.                                                                       |
//FECHA CREACION			  :MARZO 13 DE 2012.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :03 de Octubre de 2013.                                                                                      |
//TABLAS UTILIZADAS   :                                                                                                                    | 
//                                                                                                                                         |
//facarfac  en unix   : Tabla de Facturas x cargo.                                                                                         |
//facardet  en unix   : Tabla de Cargos.                                                                                                   |
//ivdrodet  en unix   : Tabla de detalle de cargos de Medicamentos.                                                                        |
//ivart     en unix   : Tabla de Maestro de Articulos.                                                                                     |
//ivuni     en unix   : Tabla de unidad de medida.                                                                                         |
//ameartmed en unix   : Tabla de Articulos con nombre generico apliado y presentación de nueva EPS.                                        |
//                                                                                                                                         |
// Moficacion 03-10-2013: Se solicita que por pantalla pida si quieren con ATC o no y sumarlo de la tabla cotizaci_000009. Gustavo Avendaño|
// Moficacion 08-11-2013: Adicionar el codigo y el nombre de la NUEVA EPS. Gustavo Avendaño                                                | 
// Moficacion 22-02-2016: Adicionar para que traiga los medicamentos que graban por ayuda. Gustavo Avendaño                                | 
// Moficacion 16-01-2017: Se pide adicionar campo con el codigo de clinica. Sandra Nury  REQ: 15003                                        | 
// Moficacion 09-04-2019: Se pide traer en el numero de la factura el prefijo de la factura.                                               | 
//==========================================================================================================================================
include_once("root/comun.php");
$conex     = obtenerConexionBD("matrix");
$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz = "1.5 09-Abril-2019";

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
encabezado("DETALLE DE MATERIALES Y MEDICAMENTOS DE UNA FACTURA",$wactualiz,"clinica");

if (!$usuarioValidado && !isset($_GET['automatizacion_pdfs']))
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{



 //Forma
 if (isset($_GET['automatizacion_pdfs'])){
	echo "<form name='forma' action='rep_detafactu.php?automatizacion_pdfs=' method='post'>";

 }
 else{
	echo "<form name='forma' action='rep_detafactu.php' method='post'>";
 }
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fte) or $fte=='' or !isset($fac) or $fac == '' or !isset($com) or $com=='' or !isset($atc) or $atc=='')
  {
  	
  	echo "<form name='rep_detafactu' action='' method=post>";
  
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
      
   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////Comercial o Generico
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Nombre (C)omercial o (G)enerico o (*) Todos dos<i><br></font><bgcolor='#dddddd' aling=center><input type='TEXT' name='com' size=9 maxlength=1 id='com'></td>";
   echo "</Tr>";
   
   if (isset($com))
   {
    $com=$com;
   }
   else 
   {
    $com='';	
   }
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////suma           ATC al CUM
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Desea ATC en el CUM (S)i o (N)o<i><br></font><bgcolor='#dddddd' aling=center><input type='TEXT' name='atc' size=9 maxlength=1 id='atc'></td>";
   echo "</Tr>";
   
   if (isset($atc))
   {
    $com=$atc;
   }
   else 
   {
    $atc='';	
   }
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////           CODIGO NEPS
  
   echo "<Tr>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Desea Codigo NUEVA EPS (S)i o (N)o<i><br></font><bgcolor='#dddddd' aling=center><input type='TEXT' name='cneps' size=9 maxlength=1 id='cneps'></td>";
   echo "</Tr>";
   
   if (isset($cneps))
   {
    $com=$cneps;
   }
   else 
   {
    $cneps='';	
   }  
  
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>"; //submit osea el boton de Generar o Aceptar
   echo "</tr>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
  	IF ($com=='c')
  	{
  	 $com='C';	
  	}
  	ELSE
  	{
  	 IF ($com=='C')
  	 {
  	  $com='C';
  	 }	
  	 ELSE
  	 {
  	  IF ($com=='g')
  	  {
  	   $com='G';
  	  }
  	  ELSE
  	  {
  	   IF ($com=='G')
  	   {
  	   	$com='G';	
  	   }
  	   ELSE
  	   {
  	    $com='*';	
  	   }
  	  }
  	 }
  	}

    IF ($atc=='s')
     {
	  $atc='S';
     }	 
  	 ELSE
	 {
	  IF ($atc=='S')
	   {
	    $atc='S';
	   }
	   ELSE
       {	   
	   $atc='N';
	   }
	 }
	 
	 
	 IF ($cneps=='s')
     {
	  $cneps='S';
     }	 
  	 ELSE
	 {
	  IF ($cneps=='S')
	   {
	    $cneps='S';
	   }
	   ELSE
       {	   
	   $cneps='N';
	   }
	 }
	
  $query1="SELECT count(*) cant"
         ."  FROM amefacele"
		 ." WHERE fue='$fte'"
		 ."   AND doc=$fac";
	
  $err1 = odbc_do($conex_o,$query1);	
  
  while (odbc_fetch_row($err1))
  {
    $canti= odbc_result($err1,1);//cantidad	 
  }	

   
  IF ($canti <> 0) 
  {
	$query2="SELECT prefi"
           ."  FROM amefacele"
           ." WHERE fue='$fte'"
		   ."   AND doc=$fac";
   $err2 = odbc_do($conex_o,$query2);	
   
   while (odbc_fetch_row($err2))
   {
    $prefi= odbc_result($err2,1);//prefijo factura
   }	   
  }
  ELSE
  {
   $prefi='';
  }
	
 	
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
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$prefi."".$fac."</b></font></td>";
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
	
	   
	
   IF ($cneps=='S')
   {
    IF ( $com=='*')
	{
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_NUEVA_EPS</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_CODIGO_NUEVA_EPS</font></td>";	
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CUM</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CLINICA</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_COMERCIAL</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_GENERICO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UNIDAD</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_UNI</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_FAC</font></td>";
	echo "</tr>";
	}
	ELSE
	{
	 IF ($com=='C')
	 {
	  echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	  echo "<tr>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_NUEVA_EPS</font></td>"; 
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_CODIGO_NUEVA_EPS</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CUM</font></td>"; 
      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CLINICA</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_COMERCIAL</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UNIDAD</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_UNI</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_FAC</font></td>";
	  echo "</tr>";	
	 }
	 ELSE
	 {
	  echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	  echo "<tr>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_NUEVA_EPS</font></td>"; 
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_CODIGO_NUEVA_EPS</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CUM</font></td>"; 
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CLINICA</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_GENERICO</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UNIDAD</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
  	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_UNI</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_FAC</font></td>";
	  echo "</tr>";	
	 }
	}
   }
   ELSE
   {
	IF ( $com=='*')
	{
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CUM</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CLINICA</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_COMERCIAL</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_GENERICO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UNIDAD</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_UNI</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_FAC</font></td>";
	echo "</tr>";
	}
	ELSE
	{
	 IF ($com=='C')
	 {
	  echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	  echo "<tr>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CUM</font></td>"; 
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CLINICA</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_COMERCIAL</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UNIDAD</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_UNI</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_FAC</font></td>";
	  echo "</tr>";	
	 }
	 ELSE
	 {
	  echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	  echo "<tr>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CUM</font></td>"; 
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO_CLINICA</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE_GENERICO</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>UNIDAD</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
  	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_UNI</font></td>";
	  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VLR_FAC</font></td>";
	  echo "</tr>";	

	 }
	}
   }	
	
    $query_o1="SELECT cardetcon con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(cardetcan*-1) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,facardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval<0"
             ."   AND cardetcon=concod"
             ."   AND artcum is not null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,'0' artcum,uninom,sum(cardetcan*-1) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,facardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval<0"
             ."   AND cardetcon=concod"
             ."   AND artcum is null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(cardetcan) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,facardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval>=0"
             ."   AND cardetcon=concod"
             ."   AND artcum is not null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,'0' artcum,uninom,sum(cardetcan) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,facardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval>=0"
             ."   AND cardetcon=concod"
             ."   AND artcum is null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
           	 ." UNION ALL "
			 ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(cardetcan*-1) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval<0"
             ."   AND cardetcon=concod"
             ."   AND artcum is not null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,'0' artcum,uninom,sum(cardetcan*-1) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval<0"
             ."   AND cardetcon=concod"
             ."   AND artcum is null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(cardetcan) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval>=0"
             ."   AND cardetcon=concod"
             ."   AND artcum is not null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,'0' artcum,uninom,sum(cardetcan) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM facarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval>=0"
             ."   AND cardetcon=concod"
             ."   AND artcum is null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
           	 ." UNION ALL "
			 ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(cardetcan*-1) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM aycarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval<0"
             ."   AND cardetcon=concod"
             ."   AND artcum is not null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,'0' artcum,uninom,sum(cardetcan*-1) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM aycarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval<0"
             ."   AND cardetcon=concod"
             ."   AND artcum is null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(cardetcan) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM aycarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval>=0"
             ."   AND cardetcon=concod"
             ."   AND artcum is not null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." UNION ALL "
             ." SELECT cardetcon con,connom,cod,nombre,gen,nomg,'0' artcum,uninom,sum(cardetcan) can,cardetvun,sum(carfacval) valf,codneps,nombreneps"
             ."  FROM aycarfac,aycardet,ivdrodet,ivart,ivuni,facon,outer ameartmed"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetfac='S'"
             ."   AND cardetfue=drodetfue"
             ."   AND cardetdoc=drodetdoc"
             ."   AND cardetite=drodetite"
             ."   AND drodetfac='S'"
             ."   AND drodetart=artcod"
             ."   AND artcod=cod"
             ."   AND artuni=unicod"
             ."   AND carfacval>=0"
             ."   AND cardetcon=concod"
             ."   AND artcum is null"
             ." GROUP by 1,2,3,4,5,6,7,8,10,12,13"
             ." ORDER by 1,4,5"
			 ." INTO temp tmpmed";
	           
	//echo $query_o."<br>";
	$err_o = odbc_do($conex_o,$query_o1);
	
	$query_o2="SELECT con,connom,cod,nombre,gen,nomg,artcum,uninom,sum(can) can,cardetvun,sum(valf) valf,codneps,nombreneps"
             ."  FROM tmpmed"
             ." GROUP BY 1,2,3,4,5,6,7,8,10,12,13"
             ." order by 1,4,5";
	
	$err_o = odbc_do($conex_o,$query_o2);
			
   $Num_Filas = 0;
   
   $cona='';
   $ncona='';
   $totcon=0;
   $tottal=0;
   
   
   while (odbc_fetch_row($err_o))
	  {
	  	$Num_Filas++;
	  	
	  	$con     = odbc_result($err_o,1);//concepto
	  	$ncon    = odbc_result($err_o,2);//nombre_concepto
		$cod     = odbc_result($err_o,3);//codigo_articulo
		$nombre  = odbc_result($err_o,4);//nombre_articulo
		$gen     = odbc_result($err_o,5);//codigo_generico
		$nomg    = odbc_result($err_o,6);//nombre_generico
		$cum     = odbc_result($err_o,7);//codigo_cum
	    $nomu    = odbc_result($err_o,8);//nombre_unidad_articulo
	    $can     = odbc_result($err_o,9);//cantidad
		$vun     = odbc_result($err_o,10);//valor_unitario
		$valf    = odbc_result($err_o,11);//valor_facturado
		$codn    = odbc_result($err_o,12);//codigo Nueva EPS
		$nomcn   = odbc_result($err_o,13);//Nombre codigo Nueva EPS
				
      
   $query = " SELECT Igatc"
           ."   FROM cotizaci_000009 "
           ."  WHERE Igcodigo= '".$cod."' "; 
           
   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
    //echo $query."<br>";

   $row = mysql_fetch_array($err);
	  
	
		IF ($gen=='')
		{
		  $nomg='&nbsp;';
		}
		
	    IF ($gen==' ')
		{
		  $nomg='&nbsp;';
		}
		
		IF ($cum=='')
		{
		  $cum=$cod;
		}
		
	    IF ($cum==' ')
		{
		  $cum=$cod;
		}
		
	    IF ($cona=='')
	    {
	      $cona=$con;
	      $ncona=$ncon;
	      $coda=$cod;
	      $nombrea=$nombre;
	      $gena=$gen;
	      $nomga=$nomg;
	      $cuma=$cum;
	      $nomua=$nomu;
	      $cana=$can;
	      $vuna=$vun;
	      $valfa=$valf;
		  $codna=$codn;
	      $nomcna=$nomcn;

	     IF ($cneps=='S')
         {
		  IF ($com=='*')
	      {
	       echo "<tr>";
	       echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	       echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 		   
		   echo "</tr>";
	      }
	      ELSE
	      {
	       echo "<tr>";
	       echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	       echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 		   
		   echo "</tr>"; 
	      }
         }
         ELSE
         {		 
		  IF ($com=='*')
	      {
	       echo "<tr>";
	       echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	       echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		   echo "</tr>";
	      }
	      ELSE
	      {
	       echo "<tr>";
	       echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	       echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		   echo "</tr>"; 
	      }
		 }
		 $totcon=0;
	     $tottal=0;	      
	      
	    }
		
	    IF ($cona==$con)
	    {
	    
		IF ($cneps=='S')
		{
		 IF ($com=='*')
	     {
	       IF ($atc=='N')
		   {
		    echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
            echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";		   
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		   	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		 }
	     ELSE
	     {
	      IF ($com=='C')
	      {
	       IF ($atc=='N')
		   {
	      	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		    echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
	      }
	      ELSE
	      {
	       IF ($atc=='N')
		   {
	      	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		   	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
	      }
	     }
		}
	    ELSE
		{
		 IF ($com=='*')
	     {
	       IF ($atc=='N')
		   {
		    echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		   	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		 }
	     ELSE
	     {
	      IF ($com=='C')
	      {
	       IF ($atc=='N')
		   {
	      	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		    echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
	      }
	      ELSE
	      {
	       IF ($atc=='N')
		   {
	      	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		   	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
	      }
	     }
		 
		} 
		 
		
	     $totcon=$totcon+$valf;
	     $tottal=$tottal+$valf;
	     
	    }
	    ELSE //$cona=$con
	    {
		IF ($cneps=='S')
		{
		 IF ($com=='*')
	     {	
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
		  echo "</tr>";
	     }
	     ELSE
	     {
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
		  echo "</tr>";
	     }
		  
		  $cona='';
		  
		  IF ($cona=='')
	      {
	      $cona=$con;
	      $ncona=$ncon;
	      $coda=$cod;
	      $nombrea=$nombre;
	      $gena=$gen;
	      $nomga=$nomg;
	      $cuma=$cum;
	      $nomua=$nomu;
	      $cana=$can;
	      $vuna=$vun;
	      $valfa=$valf;
		  $codna=$codn;
		  $nomcna=$nomcn;

	      IF ($com=='*')
	      {
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "</tr>";
	      }
	      ELSE
	      {
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "</tr>";	
	      }
	      $totcon=0;	      
	      
	      }
		
	    IF ($cona==$con)
	    {
	    
	     IF ($com=='*')
	     {
	      IF ($atc=='N')
		  {
		   echo "<tr>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>"; 
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
 		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		   echo "</tr>";
		  }
          ELSE
          {
		   echo "<tr>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>"; 
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
 		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		   echo "</tr>";	  
           }		  
		
	     }
	     ELSE
	     {
	      IF ($com=='C')
	      {
	       IF ($atc=='N')
		   {
		    echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>"; 
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		  	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>"; 
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		  }
	      ELSE
	      {
	       IF ($atc=='N')
		   {
	      	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>"; 
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		   	echo "<tr>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$codn."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomcn."</font></td>"; 
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
	      }
	     }
		
	     $totcon=$totcon+$valf;
	     $tottal=$tottal+$valf;
		  
	    }
		
		}
		ELSE // $codneps=='S'
		{
	     IF ($com=='*')
	     {	
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
		  echo "</tr>";
	     }
	     ELSE
	     {
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
		  echo "</tr>";
	     }
		  
		  $cona='';
		  
		  IF ($cona=='')
	      {
	      $cona=$con;
	      $ncona=$ncon;
	      $coda=$cod;
	      $nombrea=$nombre;
	      $gena=$gen;
	      $nomga=$nomg;
	      $cuma=$cum;
	      $nomua=$nomu;
	      $cana=$can;
	      $vuna=$vun;
	      $valfa=$valf;
		  $codna=$codn;
	      $nomcna=$nomcn;

	      IF ($com=='*')
	      {
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "</tr>";
	      }
	      ELSE
	      {
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "</tr>";	
	      }
	      $totcon=0;	      
	      
	      }
		
	    IF ($cona==$con)
	    {
	    
	     IF ($com=='*')
	     {
	      IF ($atc=='N')
		  {
		   echo "<tr>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
 		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		   echo "</tr>";
		  }
          ELSE
          {
		   echo "<tr>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
 		   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		   echo "</tr>";	  
           }		  
		
	     }
	     ELSE
	     {
	      IF ($com=='C')
	      {
	       IF ($atc=='N')
		   {
		    echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		  	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		  }
	      ELSE
	      {
	       IF ($atc=='N')
		   {
	      	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cum."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
		   ELSE
		   {
		   	echo "<tr>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".trim($cum)."-".trim($row[0])."</font></td>";
			echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomg."</font></td>";
 		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomu."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		    echo "</tr>";
		   }
	      }
	     }
		
	     $totcon=$totcon+$valf;
	     $tottal=$tottal+$valf;
		  
	    }
	   }
	  } 
	 }
  IF ($cneps=='S')
  {
   IF ($com=='*')
   {
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL GENERAL:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
	if(isset($_GET['automatizacion_pdfs'])){
		if($tottal === 0){
			echo 'no tiene detalle de materiales';
		}
	}
	else{
		echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($tottal)."</b></font></td>"; 
	} 
   echo "</tr>";
   }
   ELSE
   {
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL GENERAL:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
	if(isset($_GET['automatizacion_pdfs'])){
		if($tottal === 0){
			echo 'no tiene detalle de materiales';
		}
	}
	else{
		echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($tottal)."</b></font></td>"; 
	}      
	echo "</tr>";
   }
  }
  ELSE
  {  
   IF ($com=='*')
   {
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL GENERAL:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
	if(isset($_GET['automatizacion_pdfs'])){
		if($tottal === 0){
			echo 'no tiene detalle de materiales';
		}
	}
	else{
		echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($tottal)."</b></font></td>"; 
	}      
	echo "</tr>";
   }
   ELSE
   {
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>"; 
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL GENERAL:</b></font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</font></td>";
	if(isset($_GET['automatizacion_pdfs'])){
		if($tottal === 0){
			echo 'no tiene detalle de materiales';
		}
	}
	else{
		echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($tottal)."</b></font></td>"; 
	}        
	echo "</tr>";	
   }
  } 
  
}
   
	 echo "<br>"; 

  
 }
odbc_close($conex_o);
odbc_close_all();   
?>