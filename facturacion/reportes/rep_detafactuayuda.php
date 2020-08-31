<html>
<head>
<title>MATRIX - [REPORTE PARA VER EL DETALLE DE UNA FACTURA NO POS]</title>


<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_detafactuayuda.php'; 
	}
	
	function enter()
	{
		document.forms.rep_detafactuayuda.submit();
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
//FECHA CREACION			  :MARZO 29 DE 2012.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :02 de Abril de 2012.                                                                                        |
//                             06 de Febrero de 2013. solicitan cambios.                                                                   |                                                                                       |
//TABLAS UTILIZADAS   :                                                                                                                    | 
//                                                                                                                                         |
//facarfac  en unix   : Tabla de Empleados.                                                                                                |
//facardet  en unix   : Tabla de Empleados.                                                                                                |
//ivdrodet  en unix   : Tabla de Empleados.                                                                                                |
//ivart     en unix   : Tabla de Empleados.                                                                                                |
//ivuni     en unix   : Tabla de Empleados.                                                                                                |
//inmtra    en unix   : Tabla de pacientes en habitacion.                                                                                  |
//inhab     en unix   : Tabla de habitaciones.                                                                                             |
//                                                                                                                                         |
// Modificacion ver.1.4: 2014-02-24.                                                                                                       |
//                     :Sandra nury solicita campo nuevo valor reconocido, pide orden de las columnas asi: cantidad,vr_unitario,vr recargo |
//                      vr total,vr reconocido                                                                                             |
//        			   :2018-03-20.                                                                                                        |
//            ver1.5   :La jefe MONICA MARIA VELEZ MORALES en el requerimiento numero 18399 solicita que para los codigos 90122B dividir   |
//                      por 2 y el codigo 90122C dividir por 3.                                                                            |
//            ver1.6   :Piden que traiga el prefijo de la factura impresa.                                                                 |
//==========================================================================================================================================
include_once("root/comun.php");
$conex     = obtenerConexionBD("matrix");
$conex_o   = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
$wactualiz = "1.6 09-Abril-2019";

$empresa='root';

$usuariovalidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuariovalidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuariovalidado = false;
}

session_start();
//Encabezado
encabezado("CARGOS DE LA FACTURA",$wactualiz,"clinica");

if (!$usuariovalidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 //Forma
 echo "<form name='forma' action='rep_detafactuayuda.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fte) or $fte=='' or !isset($fac) or $fac == '' or !isset($com) or $com=='')
  {
  	echo "<form name='rep_detafactuayuda' action='' method=post>";
  
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
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Nombre (C)linica o (A)nexo<i><br></font><bgcolor='#dddddd' aling=center><input type='TEXT' name='com' size=9 maxlength=10 id='com'></td>";
   echo "</Tr>";
   
   if (isset($com))
   {
    $com=$com;
   }
   else 
   {
    $com='';	
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
  	  $com=$com;	
  	 }
  	 ELSE
  	 {
  	  IF ($com=='a')
  	  {
  	   $com='A';
  	  }
  	  ELSE
  	  {
  	   $com='A';	
  	  }
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
	
	//echo $query_o3."<br>";
			
   $num_filas = 0;
   
   while (odbc_fetch_row($err_o))
	  {
	  	$num_filas++;
	  	
	  	$fuo    = odbc_result($err_o,1);//fuo
		$his    = odbc_result($err_o,2);//historia
		$num    = odbc_result($err_o,3);//ingreso
		$pac    = odbc_result($err_o,4);//paciente
		$tip    = odbc_result($err_o,5);//tipo indentificacion
		$cep    = odbc_result($err_o,6);//cedula paciente
	  	
	  }
  	
   	echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=center colspan='3' bgcolor=#FFFFFF><font size='3' text color=#003366><b>FACTURA</b></font></td>";
	echo "</tr>";
	echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$prefi."".$fac."</b></font></td>";
	echo "</table>";
	echo "<br>";
	echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FUO-AYUDA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>HISTORIA ING-AYUDA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>ING-HISTO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>NOMBRE PACIENTE&nbsp;&nbsp;&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>TIPO_IDEN&nbsp;&nbsp;</b></font></td>";
	echo "<td align=CENTER colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>IDENTIFICACION</b></font></td>";
	echo "</tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$fuo."</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$his."</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$num."</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$pac."</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$tip."</b></font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".$cep."</b></font></td>";
	
	echo "</table>";
	
	echo "<table border=1 align=center cellpadding='0' cellspacing='0' size=100%>";	
	echo "<tr>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CODIGO</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>ANEXO</font></td>"; 
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DESCRIPCION</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CANT</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VR_UNITARIO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VR_RECARGO</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VR_TOTAL</font></td>";
	echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VR_RECONOCIDO</font></td>";
	echo "</tr>";
		
    $query_o1="SELECT carfacfue fue20,carfacdoc doc20,cardetfue fue,cardetdoc doc,cardetcon con,conarc arc,conmul mul"
             ."  FROM aycarfac,aycardet,facon"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetcon=concod"
             ."   AND cardetcon not in ('0616','0626')"
             ." GROUP BY 1,2,3,4,5,6,7"
             ." UNION ALL "
             ."SELECT carfacfue fue20,carfacdoc doc20,cardetfue fue,cardetdoc doc,cardetcon con,conarc arc,conmul mul"
             ."  FROM facarfac,facardet,facon"
             ." WHERE carfacfue='$fte'"
             ."   AND carfacdoc=$fac"
             ."   AND carfacreg=cardetreg"
             ."   AND cardetcon=concod"
             ."   AND cardetcon not in ('0616','0626')"
             ." GROUP BY 1,2,3,4,5,6,7"
             ." ORDER BY 1,2,3,4"
             ." INTO temp tmpdetnpos";
	           
	//echo $query_o1."<br>";
	$err_o = odbc_do($conex_o,$query_o1);
	
 IF ($com=='A')
 {
 	
 	//echo "paso por VERDADERO: ",$com;
 	
  $query_o2="select fue20,doc20,fue,doc,con,arc,cardetcod cum,exaane cod,cupdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."  from tmpdetnpos,aycardet,inexa,aycarfac,incup"
           ." where arc='INEXATAR'"
           ."   and fue=cardetfue"
           ."   and doc=cardetdoc"
           ."   and con=cardetcon"
           ."   and cardetcod=exacod"
           ."   and exaane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is not null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,exaane cod,cupdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,inexa,facarfac,incup"
           ."  where arc='INEXATAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and arc='INEXATAR'"
           ."    and cardetcod=exacod"
           ."    and exaane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is not null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,' ' des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."  from tmpdetnpos,aycardet,inexa,aycarfac"
           ." where arc='INEXATAR'"
           ."   and fue=cardetfue"
           ."   and doc=cardetdoc"
           ."   and con=cardetcon"
           ."   and cardetcod=exacod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,' ' des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,inexa,facarfac"
           ."  where arc='INEXATAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and arc='INEXATAR'"
           ."    and cardetcod=exacod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,quinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inqui,aycardet,aycarfac"
           ."  where arc='INQUITAR'"
           ."    and cardetcod=quicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,quinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inqui,facardet,facarfac"
           ."  where arc='INQUITAR'"
           ."    and cardetcod=quicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,proane cod,cupdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,aycardet,aycarfac,incup"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and proane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and proane is not null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,proane cod,cupdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac,incup"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and proane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and proane is not null"       
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,' ' des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,aycardet,aycarfac"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and proane is null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,' ' des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and proane is null"       
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,' ' des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,facarfac"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod='0'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,alinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,faali,facon,aycardet,aycarfac"
           ."  where arc='FAALITAR'"
           ."    and cardetcod=alicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,alinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,faali,facon,facardet,facarfac"
           ."  where arc='FAALITAR'"
           ."    and cardetcod=alicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facon,aycardet,aycarfac"
           ."  where arc='FACONTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facon,facardet,facarfac"
           ."  where arc='FACONTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   AND artcum is not null"  
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   AND artcum is null"  
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   AND artcum is not null"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   AND artcum is null"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   and artcum is not null"  
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   and artcum is null"  
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    and cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and artcum is not null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    and cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and artcum is null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,facardet,facarfac"
           ."  where arc = ' '"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
		   ."    and cardetcod='0'"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,proane cod,cupdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac,incup"
		   ."  where arc = ' '"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
		   ."    and cardetcod<>'0'"
		   ."    and cardetcod=procod"
		   ."    and proane=cupcod"		  
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,aycardet,aycarfac"
           ."  where arc = ' '"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
		   ."    and cardetcod='0'"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,facardet,facarfac"
           ."  where arc = ' '"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,aycardet,aycarfac"
           ."  where arc = ' '"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,facardet,facarfac"
           ."  where arc is null"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
		   ."    and cardetcod='0'"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,proane cod,cupdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac,incup"
		   ."  where arc is null"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
		   ."    and cardetcod<>'0'"
		   ."    and cardetcod=procod"
		   ."    and proane=cupcod"
		   ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,aycardet,aycarfac"
           ."  where arc is null"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
		   ."    and cardetcod='0'"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,facardet,facarfac"
           ."  where arc is null"
		   ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,aycardet,aycarfac"
           ."  where arc is null"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,aycardet,aycarfac"
           ."  where arc = 'INTIP'"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardettar=tiptar"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,facardet,facarfac"
           ."  where arc = 'INTIP'"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardettar=tiptar"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." into temp cargtmp";
		   
		     //echo $query_o2."<br>";
 }
 ELSE  //$com=='A'
 {
 	//echo "paso por el ELSE : ",$com;
 	
  $query_o2="select fue20,doc20,fue,doc,con,arc,cardetcod cum,cupcod cod,exades des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."  from tmpdetnpos,aycardet,inexa,aycarfac,incup"
           ." where arc='INEXATAR'"
           ."   and fue=cardetfue"
           ."   and doc=cardetdoc"
           ."   and con=cardetcon"
           ."   and cardetcod=exacod"
           ."   and exaane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is not null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cupcod cod,exades des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facardet,inexa,facarfac,incup"
           ."  where arc='INEXATAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and arc='INEXATAR'"
           ."    and cardetcod=exacod"
           ."    and exaane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is not null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,exades des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."  from tmpdetnpos,aycardet,inexa,aycarfac"
           ." where arc='INEXATAR'"
           ."   and fue=cardetfue"
           ."   and doc=cardetdoc"
           ."   and con=cardetcon"
           ."   and cardetcod=exacod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,exades des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facardet,inexa,facarfac"
           ."  where arc='INEXATAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and arc='INEXATAR'"
           ."    and cardetcod=exacod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and exaane is null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,quinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,inqui,aycardet,aycarfac"
           ."  where arc='INQUITAR'"
           ."    and cardetcod=quicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,quinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,inqui,facardet,facarfac"
           ."  where arc='INQUITAR'"
           ."    and cardetcod=quicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cupcod cod,prodes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,inpro,aycardet,aycarfac,incup"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and proane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and proane is not null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cupcod cod,prodes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac,incup"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and proane=cupcod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and proane is not null"       
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,prodes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,inpro,aycardet,aycarfac"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   and proane is null"
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,prodes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod<>'0'"
           ."    and cardetcod=procod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and proane is null"       
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,' ' cod,' ' des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facardet,facarfac"
           ."  where arc='INPROTAR'"
		   ."    and cardetcod='0'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,alinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,faali,facon,aycardet,aycarfac"
           ."  where arc='FAALITAR'"
           ."    and cardetcod=alicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,alinom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,faali,facon,facardet,facarfac"
           ."  where arc='FAALITAR'"
           ."    and cardetcod=alicod"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,aycardet,aycarfac"
           ."  where arc='FACONTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,facardet,facarfac"
           ."  where arc='FACONTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   AND artcum is not null"  
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   AND artcum is null"  
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   AND artcum is not null"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,aycardet,ivdrodet,ivart,aycarfac"
           ."  where arc='IVARTTAR'"
           ."    and con <>'0035'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"
           ."   AND artcum is null"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   and artcum is not null"  
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    AND cardettot>=0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac" 
           ."   and artcum is null"  
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,artcum cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    and cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and artcum is not null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,'0' cum,drodetart cod,artnom des,sum(cardetcan*-1) can,sum(cardettot) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,facardet,ivdrodet,ivart,facarfac"
           ."  where arc='IVARTTAR'"
           ."    and fue=cardetfue"
           ."    and doc=cardetdoc"
           ."    and con=cardetcon"
           ."    and cardetfue=drodetfue"
           ."    and cardetdoc=drodetdoc"
           ."    and cardetite=drodetite"
           ."    and drodetart=artcod"
           ."    and cardettot<0"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."   and artcum is null"
           ." group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,facardet,facarfac"
           ."  where arc = ' '"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,aycardet,aycarfac"
           ."  where arc = ' '"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,procod cod,pronom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac"
		   ."  where arc = ' '"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
		   ."    and cardetcod<>'0'"
		   ."    and cardetcod=procod"
		   ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,facardet,facarfac"
           ."  where arc = ' '"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,aycardet,aycarfac"
           ."  where arc = ' '"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,facardet,facarfac"
           ."  where arc is null"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,con cum,con cod,connom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,aycardet,aycarfac"
           ."  where arc is null"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		    ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,procod cod,pronom des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval) vrec"
           ."   from tmpdetnpos,inpro,facardet,facarfac"
		   ."  where arc is null"
           ."    and con not in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
		   ."    and cardetcod<>'0'"
		   ."    and cardetcod=procod"
		   ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,facardet,facarfac"
           ."  where arc is null"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ."  UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,aycardet,aycarfac"
           ."  where arc is null"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcon=concod"
           ."    and cardettar=tiptar"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
		   ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,aycardet,aycarfac"
           ."  where arc = 'INTIP'"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardettar=tiptar"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." UNION ALL "
           ." select fue20,doc20,fue,doc,con,arc,cardetcod cum,cardetcod cod,tipdes des,sum(cardetcan*mul) can,sum(cardettot*mul) tot,sum(cardetrec) rec,sum(carfacval*mul) vrec"
           ."   from tmpdetnpos,facon,intip,facardet,facarfac"
           ."  where arc = 'INTIP'"
           ."    and con in ('0035','2009')"
           ."    and fue = cardetfue"
           ."    and doc = cardetdoc"
           ."    and con = cardetcon"
           ."    and cardetcod=tipcod"
           ."    and cardettar=tiptar"
           ."    and cardetcon=concod"
           ."   and cardetfac='S'"
           ."   and cardetreg=carfacreg"
           ."   and carfacfue='$fte'"
           ."   AND carfacdoc=$fac"   
           ."  group by 1,2,3,4,5,6,7,8,9"
           ." into temp cargtmp";
   	
 }
   
  //echo $query_o2."<br>";
  $err_o = odbc_do($conex_o,$query_o2);

  $query_o3=" select fue20,doc20,con,connom,cum,cod,des,sum(can) can,sum(tot) tot,sum(rec) rec,sum(vrec) vrec"
            ."  from cargtmp,facon"
            ." where con=concod"
            ." group by 1,2,3,4,5,6,7"
            ." order by 3,6"
            ." into temp carfinal";
               
  $err_o5 = odbc_do($conex_o,$query_o3);          

  $query_o5=" select fue20,doc20,con,connom,cum,cod,des,can,tot,(tot/can) vlruni,rec,vrec"
            ."  from carfinal"
            ." where can<>0"
            ." UNION ALL "
            ." select fue20,doc20,con,connom,cum,cod,des,can,0 tot,0 vlruni,0 rec,0 vrec"
            ."  from carfinal"
            ." where can=0"
            ." order by 3,6";
  
  $err_o = odbc_do($conex_o,$query_o5);
			
  $Num_Filas = 0;
   
  $cona='';
  $ncona='';
  $totcon=0;
  $tottal=0;
  $totcvrec=0;
  $totvrec=0;
  
   
   while (odbc_fetch_row($err_o))
	  {
	  	$Num_Filas++;
	  	
	  	$con     = odbc_result($err_o,3);//concepto
	  	$ncon    = odbc_result($err_o,4);//nombre_concepto
	  	$cum     = odbc_result($err_o,5);//codigo_cum
		$cod     = odbc_result($err_o,6);//codigo_examen,articulo,procedimiento etc
		$nombre  = odbc_result($err_o,7);//nombre_cups, o descripcion
	    $can     = odbc_result($err_o,8);//cantidad
		$valf    = odbc_result($err_o,9);//valor_total cargo    
		$vun     = odbc_result($err_o,10);//valor_unitario
		$rec     = odbc_result($err_o,11);//valor_recargo
        $vrec    = odbc_result($err_o,12);//valor_reconocido		
		
	    IF ($cona=='')
	    {
	      $cona =$con;
	      $ncona=$ncon;
	      $coda =$cod;
	      $nombrea=$nombre;
	      $cuma =$cum;
	      $cana =$can;
	      $vuna =$vun;
	      $valfa=$valf;
	      $reca =$rec;
		  $vreca=$vrec;
	      
          echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		  echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
          echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";		 
		  echo "</tr>"; 
	      
	      $totcon=0;
	      $tottal=0;
          $totvrec=0;
          $totcvrec=0;	      
	    }
		
	    IF ($cona==$con)
	    {
	     
	    IF (($con=='0035') or ($con=='2009'))
	     {
	     	
	     echo "<tr>";
		 echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cum."</font></td>";
		 echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cod."</font></td>";
		 echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$nombre."</font></td>";
		 echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		 echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		 echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($rec)."</font></td>";
		 echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
	     echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vrec)."</font></td>";
		 echo "</tr>";
		
	     $totcon=$totcon+$valf;
	     $tottal=$tottal+$valf;
		 $totvrec=$totvrec+$vrec;
	     $totcvrec=$totcvrec+$vrec;	
	     	
	      $query_oh="SELECT trahab,traing,trahoi,traegr,trahoe "
                   ."  FROM facarfac,facardet,inmtra,inhab"
                   ." WHERE carfacfue='$fte'"
                   ."   AND carfacdoc='$fac'"
                   ."   AND carfacreg=cardetreg"
                   ."   AND cardetcon='$con'"
                   ."   AND cardetdoc=tradoc"
                   ."   AND trahab=habcod"
                   ."   AND habtip='$cod'"
                   ." ORDER BY 2,1,3";
                   
          $err_oh = odbc_do($conex_o,$query_oh);
          
	      while (odbc_fetch_row($err_oh))
	      {
     	  	$hab     = odbc_result($err_oh,1);//habitacion
	  	    $fing    = odbc_result($err_oh,2);//fecha_ingreso
	  	    $hing    = odbc_result($err_oh,3);//hora_ingreso
		    $fegr    = odbc_result($err_oh,4);//fecha_egreso
		    $hegr    = odbc_result($err_oh,5);//hora_egreso
		    
		    echo "<tr>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'><b>HAB:&nbsp;&nbsp;</b>".$hab."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' text color='#003366'><b>FEC_ING:&nbsp;&nbsp;</b>".$fing."&nbsp;&nbsp;<b>HOR_ING:&nbsp;&nbsp;</b>".$hing."&nbsp;&nbsp; <b>FEC_EGR:&nbsp;&nbsp;</b>".$fegr."&nbsp;&nbsp;<b>HOR_EGR:&nbsp;&nbsp;</b>".$hegr."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
	        echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
	        echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
            echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";		
		    echo "</tr>";
		  }
	     }
         ELSE
         {
          
           	$query_oc="SELECT count(*)"
          	         ."  FROM facarfac,facardet"
          	         ." WHERE carfacfue='$fte'"
                     ."   AND carfacdoc='$fac'"
                     ."   AND carfacreg=cardetreg"
                     ."   AND cardetcon='$con'"
                     ."   AND cardetcco in ('1016','1191')";
                     
            $err_oc = odbc_do($conex_o,$query_oc);   

           	$cant   = odbc_result($err_oc,1);//cantidad
           	
           	IF ($cant>0)
           	{
           		
           	echo "<tr>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cum."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>0</font></td>";
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($rec)."</font></td>";
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
			echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vrec)."</font></td>";
			echo "</tr>";
		
	        $totcon=$totcon+$valf;
	        $tottal=$tottal+$valf;
			$totcvrec=$totcvrec+$vrec;
           	$totvrec=$totvrec+$vrec;	
           		
           	  $query_oc="SELECT cardetfue,cardetdoc,cardetfec,cardetcan,cardetvun,cardettot,cardetrec,carfacval"
          	         ."  FROM facarfac,facardet"
          	         ." WHERE carfacfue='$fte'"
                     ."   AND carfacdoc='$fac'"
                     ."   AND carfacreg=cardetreg"
                     ."   AND cardetcon='$con'"
                     ."   AND cardetcod='$cum'"
                     ."   AND cardetcco in ('1016','1191')"
                     ." ORDER BY 1,2";
                     
              $err_oc = odbc_do($conex_o,$query_oc);   
           		
           	while (odbc_fetch_row($err_oc))
	        {
	         $fuecar    = odbc_result($err_oc,1);//fuente
	         $doccar    = odbc_result($err_oc,2);//documento	
     	  	 $feccargo  = odbc_result($err_oc,3);//fecha_cargo
	  	     $cantreal  = odbc_result($err_oc,4);//cantidad_real
	  	     $vuni      = odbc_result($err_oc,5);//valor_unitario
	  	     $valtot    = odbc_result($err_oc,6);//valor_cargo
             $valreca   = odbc_result($err_oc,7);//valor_recargo
			 $valfac    = odbc_result($err_oc,8);//valor_facturado
	  	     
		     echo "<tr>";
 		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'><b>&nbsp;</b></font></td>";
		     echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' text color='#003366'><b>FUE_CAR:&nbsp;&nbsp;</b>&nbsp;&nbsp;".$fuecar."&nbsp;&nbsp;<b>CARGO:&nbsp;&nbsp;</b>&nbsp;&nbsp;".$doccar."&nbsp;&nbsp;<b>FECHA CARGO:&nbsp;&nbsp;</b>$feccargo</font></td>";
		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>$cantreal</font></td>";
		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($vuni)."</font></td>";
	         echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($valreca)."</font></td>";
			 echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($valtot)."</font></td>";
			 echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($valfac)."</font></td>";
		     echo "</tr>";
		    }
              
           }
           ELSE // else de cant>0
           {
		   	 IF (trim($cum) == '90122B')
			 {
			 // ECHO 'verdadero 90122B ',$cum;
			  $can=$can*2;
			  $vun=($valf/$can);
			 }
			 ELSE
			 {
			  //ECHO 'FALSO1 ',$cum;
			   IF ( trim($cum) == '90122C')
			   {
			  //  ECHO 'verdadero 90122C ',$cum;
			    $can=$can*3;
			    $vun=($valf/$can);
			   }
			 } 
			 
            echo "<tr>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cum."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
	  	    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($rec)."</font></td>";
			echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
	        echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vrec)."</font></td>";
  		    echo "</tr>";
           
  		    $totcon=$totcon+$valf;
	        $tottal=$tottal+$valf;
			$totcvrec=$totcvrec+$vrec;
            $totvrec=$totvrec+$vrec;
		  }		  
         }
	    }
	    ELSE //$cona=$con
	    {
	     
	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
		  echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>";
		  echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($totcvrec)."</b></font></td>";
		  echo "</tr>";	
		 		  
		 $cona='';
		  
		 IF ($cona=='')
	      {
	      	
	      $cona   =$con;
	      $ncona  =$ncon;
	      $coda   =$cod;
	      $nombrea=$nombre;
	      $cuma   =$cum;
	      $cana   =$can;
	      $vuna   =$vun;
	      $valfa  =$valf;
		  $vreca  =$vrec;
	 

	      echo "<tr>";
	      echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>CONCEPTO:</b></font></td>";
	      echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$con."</b></font></td>";
		  echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$ncon."</b></font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>"; 
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
		  echo "</tr>";	
	      
	     $totcon=0;
         $totcvrec=0;		 
	  
	     IF (($con=='0035') or ($con=='2009'))
	     {
	      echo "<tr>";
		  echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cum."</font></td>";
		  echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cod."</font></td>";
		  echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$nombre."</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		  echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
          echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($rec)."</font></td>";
		  echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
		  echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vrec)."</font></td>";
          echo "</tr>";
			      		
	     $totcon=$totcon+$valf;
	     $tottal=$tottal+$valf;
		 $totcvrec=$totcvrec+$vrec;
         $totvrec=$totvrec+$vrec;
	     	
	     $query_ohp="SELECT count(*) "
                   ."  FROM facarfac,facardet,inmtra,inhab"
                   ." WHERE carfacfue='$fte'"
                   ."   AND carfacdoc='$fac'"
                   ."   AND carfacreg=cardetreg"
                   ."   AND cardetcon='$cona'"
                   ."   AND cardetdoc=tradoc"
                   ."   AND trahab=habcod"
				   ."   AND trahis=cardethis"
                   ."   AND habtip='$cod'";
                   
                   
          $err_ohp = odbc_do($conex_o,$query_ohp);
		  
		 $conte   = odbc_result($err_ohp,1);//CONTEO
           	
         IF ($conte>0)
		 {
		  $query_oh="SELECT trahab,traing,trahoi,traegr,trahoe "
                   ."  FROM facarfac,facardet,inmtra,inhab"
                   ." WHERE carfacfue='$fte'"
                   ."   AND carfacdoc='$fac'"
                   ."   AND carfacreg=cardetreg"
                   ."   AND cardetcon='$cona'"
                   ."   AND cardetdoc=tradoc"
                   ."   AND trahab=habcod"
				   ."   AND trahis=cardethis"
                   ."   AND habtip='$cod'"
                   ." ORDER BY 2,1,3";
                   
          $err_oh = odbc_do($conex_o,$query_oh);
          
	      while (odbc_fetch_row($err_oh))
	      {
     	  	$hab     = odbc_result($err_oh,1);//habitacion
	  	    $fing    = odbc_result($err_oh,2);//fecha_ingreso
	  	    $hing    = odbc_result($err_oh,3);//hora_ingreso
		    $fegr    = odbc_result($err_oh,4);//fecha_egreso
		    $hegr    = odbc_result($err_oh,5);//hora_egreso
		    
		    echo "<tr>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'><b>HAB:&nbsp;&nbsp;</b>".$hab."</font></td>";
		    echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' text color='#003366'><b>FEC_ING:&nbsp;&nbsp;</b>".$fing."&nbsp;&nbsp;<b>HOR_ING:&nbsp;&nbsp;</b>".$hing."&nbsp;&nbsp; <b>FEC_EGR:&nbsp;&nbsp;</b>".$fegr."&nbsp;&nbsp;<b>HOR_EGR:&nbsp;&nbsp;</b>".$hegr."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
			echo "</tr>";
		  }
		 }
		 ELSE
		 {
		  $hab     = '';          //habitacion
	  	  $fing    = '    /  /  ';//fecha_ingreso
	  	  $hing    = '  :  ';     //hora_ingreso
		  $fegr    = '    /  /  ';//fecha_egreso
		  $hegr    = '  :  ';     //hora_egreso
		    
		  echo "<tr>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'><b>HAB:&nbsp;&nbsp;</b>".$hab."</font></td>";
		  echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' text color='#003366'><b>FEC_ING:&nbsp;&nbsp;</b>".$fing."&nbsp;&nbsp;<b>HOR_ING:&nbsp;&nbsp;</b>".$hing."&nbsp;&nbsp; <b>FEC_EGR:&nbsp;&nbsp;</b>".$fegr."&nbsp;&nbsp;<b>HOR_EGR:&nbsp;&nbsp;</b>".$hegr."</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		  echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		  echo "</tr>";
		 }
		  
		  
	     }
	     ELSE
         {
          	
          	//echo "paso por el if verdadero 2: ",$con;
            $query_oc="SELECT count(*)"
          	         ."  FROM facarfac,facardet"
          	         ." WHERE carfacfue='$fte'"
                     ."   AND carfacdoc='$fac'"
                     ."   AND carfacreg=cardetreg"
                     ."   AND cardetcon='$con'"
                     ."   AND cardetcco in ('1016','1191')";
                     
            $err_oc = odbc_do($conex_o,$query_oc);   

           	$cant   = odbc_result($err_oc,1);//cantidad
           	
           	IF ($cant>0)
           	{
           		
           	echo "<tr>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cum."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>0</font></td>";
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
            echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($rec)."</font></td>";		   
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
	        echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vrec)."</font></td>";
		    echo "</tr>";
		
	        $totcon=$totcon+$valf;
	        $tottal=$tottal+$valf;
			$totcvrec=$totcvrec+$vrec;
            $totvrec=$totvrec+$vrec;

          	$query_oc="SELECT cardetfue,cardetdoc,cardetfec,cardetcan,cardetvun,cardettot,cardetrec,carfacval"
          	         ."  FROM facarfac,facardet"
          	         ." WHERE carfacfue='$fte'"
                     ."   AND carfacdoc='$fac'"
                     ."   AND carfacreg=cardetreg"
                     ."   AND cardetcon='$con'"
                     ."   AND cardetcod='$cum'"
                     ."   AND cardetcco in ('1016','1191')"
                     ." ORDER BY 1,2";
                     
              $err_oc = odbc_do($conex_o,$query_oc);   
           		
           	while (odbc_fetch_row($err_oc))
	        {
	         $fuecar    = odbc_result($err_oc,1);//fuente
	         $doccar    = odbc_result($err_oc,2);//documento	
     	  	 $feccargo  = odbc_result($err_oc,3);//fecha_cargo
	  	     $cantreal  = odbc_result($err_oc,4);//cantidad_real
	  	     $vuni      = odbc_result($err_oc,5);//valor_unitario
	  	     $valtot    = odbc_result($err_oc,6);//valor_cargo
             $valreca   = odbc_result($err_oc,7);//valor_recargo
			 $valfac    = odbc_result($err_oc,8);//valor_facturado
	  	     
		     echo "<tr>";
 		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'><b>&nbsp;</b></font></td>";
		     echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' text color='#003366'><b>FUE_CAR:&nbsp;&nbsp;</b>&nbsp;&nbsp;".$fuecar."&nbsp;&nbsp;<b>CARGO:&nbsp;&nbsp;</b>&nbsp;&nbsp;".$doccar."&nbsp;&nbsp;<b>FECHA CARGO:&nbsp;&nbsp;</b>$feccargo</font></td>";
		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>$cantreal</font></td>";
		     echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($vuni)."</font></td>";
	         echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($valreca)."</font></td>";
			 echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($valtot)."</font></td>";
	         echo "<td align=CENTER bgcolor=#FFFFFF><font size='1' text color='#003366'>".number_format($valfac)."</font></td>";
		     echo "</tr>";
		    }
           }
           ELSE
           {
		     IF (trim($cum) == '90122B')
			 {
			 // ECHO 'verdadero 90122B ',$cum;
			  $can=$can*2;
			  $vun=($valf/$can);
			 }
			 ELSE
			 {
			  //ECHO 'FALSO1 ',$cum;
			   IF ( trim($cum) == '90122C')
			   {
			  //  ECHO 'verdadero 90122C ',$cum;
			    $can=$can*3;
			    $vun=($valf/$can);
			   }
			 }
		   
            echo "<tr>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cum."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$cod."</font></td>";
		    echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>".$nombre."</font></td>";
		    echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($can)."</font></td>";
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vun)."</font></td>";
            echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($rec)."</font></td>";	  	   
		    echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($valf)."</font></td>";
	        echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'>".number_format($vrec)."</font></td>";
  		    echo "</tr>";
           
  		    $totcon=$totcon+$valf;
	        $tottal=$tottal+$valf;
			$totcvrec=$totcvrec+$vrec;
            $totvrec=$totvrec+$vrec;
           }
         }
	    } 
	   } // ELSE $cona=$con
	  }
		
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>TOTAL CONCEPTO:</b></font></td>";
   echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$cona."</b></font></td>";
   echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".$ncona."</b></font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>"; 
   echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($totcon)."</b></font></td>";
   echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($totcvrec)."</b></font></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>TOTAL GENERAL:</b></font></td>";
   echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=LEFT   bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'><b>&nbsp;</font></td>";
   echo "<td align=CENTER bgcolor=#FFFFFF><font size='2' text color='#003366'>&nbsp;</font></td>"; 
   echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($tottal)."</b></font></td>";
   echo "<td align=RIGHT  bgcolor=#FFFFFF><font size='2' text color='#003366'><b>".number_format($totvrec)."</b></font></td>";
   echo "</tr>";	
  
 }
   
   echo "</table>";
  
 }
odbc_close($conex_o);
odbc_close_all();
?>