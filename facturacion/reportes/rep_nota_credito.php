<html>
<head>
<title>Impresión Nota credito o Debito - Unix</title>
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
*                                           IMPRESION DEL DETALLE DE LAS NOTAS CREDITO - UNIX                                              *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :Impresión Notas Credito.                                                                                     |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Diciembre 22 DE 2015.                                                                                        |
//FECHA ULTIMA ACTUALIZACION  :22 de Diciembre de 2015.                                                                                     |
//DESCRIPCION			      :Este programa sirve para imprimir la nota credito que se hace por unix.                                      |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cacar             : Tabla de facturas.                                                                                                    | 
//famov             : Tabla de facturas.                                                                                                    | 
//famovdet          : Tabla conceptos de facturas.                                                                                          |
//==========================================================================================================================================

include_once("root/comun.php");
include_once("root/montoescrito.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 22-Diciembre-2015";

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
 echo "<form name='forma' action='rep_nota_credito.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' )
  {
  	echo "<form name='rep_nota_credito' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";

 	//FUENTE Y NUMERO DE NOTA
 	echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>IMPRESION NOTA CREDITO O DEBITO</td></tr>";
	echo"<tr><td align=center bgcolor=#cccccc >FUENTE DE LA NOTA :</TD><td align=center bgcolor=#cccccc ><input style='text-transform: uppercase;' type='input' name='fue' ></td></tr>";
	echo"<tr><td align=center bgcolor=#cccccc >NUMERO DE LA NOTA :</TD><td align=center bgcolor=#cccccc ><input type='input' name='carg'></td></tr>";
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
			
  $Num_Filas = 0;	
  	
  echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";	
  echo "<td colspan='1' align=center><IMG SRC='/MATRIX/images/medical/paf/logo.png' WIDTH=280 HEIGHT=100></td>"; //trae el logo de la clinica.
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
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>Dir : ".$dir1."</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".$nom1."</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>NOTA CREDITO o DEBITO</b></font></td>";
	 echo "</tr>";
	 	 
	 echo "<tr>";
     echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>Conm. ".$tel1." ".$noc1."<b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>NIT. ".$nit1."</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>No.".$carg."</b<</font></td>";
	 echo "</tr>";
	 echo "</table>";
	 	
	}

	 echo "<br>";
	 echo "<br>";
		
	
	$query="SELECT count(*)"
         ."  FROM cacarotr"
         ." WHERE carotrfue='".$fue."'"
         ."   and carotrdoc='".$carg."'";
  		 
    $err_o = odbc_do($conexi,$query);
			
    $cant = odbc_result($err_o,1);//PARA SABER SI EXISTE INFORMACION EN CACAROTR
  
    IF ($cant==0)
	{
	 $query1="SELECT carfue,cardoc,carced,carres,enccau,caunom,carfec,' ' carotrfan,0 carotrdan"
           ."  FROM cacar,caenc,cacau"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carfue=encfue"
           ."   AND cardoc=encdoc"
           ."   AND enccau=caucod"
		   ."   AND carcon is null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9";
	}
	ELSE
	{	
	$query1="SELECT carfue,cardoc,carced,carres,enccau,caunom,carfec,' ' carotrfan,0 carotrdan"
           ."  FROM cacar,caenc,cacau"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carfue=encfue"
           ."   AND cardoc=encdoc"
           ."   AND enccau=caucod"
		   ."   AND carcon is null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
		   ." SELECT carfue,cardoc,carced,carres,enccau,caunom,carfec,carotrfan,carotrdan"
           ."  FROM cacar,caenc,cacau,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carfue=encfue"
           ."   AND cardoc=encdoc"
           ."   AND enccau=caucod"
		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrfan is not null"
		   ."   AND carotrccc is not null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL "
		   ." SELECT carfue,cardoc,carced,carres,enccau,caunom,carfec,' ' carotrfan,0 carotrdan"
           ."  FROM cacar,caenc,cacau,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carfue=encfue"
           ."   AND cardoc=encdoc"
           ."   AND enccau=caucod"
		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
           ."   AND carotrfan is null"
		   ."   AND carotrccc is null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
		   ." SELECT carfue,cardoc,carced,carres,enccau,caunom,carfec,' ' carotrfan,0 carotrdan"
           ."  FROM cacar,caenc,cacau,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carfue=encfue"
           ."   AND cardoc=encdoc"
           ."   AND enccau=caucod"
		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrfan is null"
		   ."   AND carotrccc is not null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
		   ." SELECT carfue,cardoc,carced,carres,enccau,caunom,carfec, carotrfan,carotrdan"
           ."  FROM cacar,caenc,cacau,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carfue=encfue"
           ."   AND cardoc=encdoc"
           ."   AND enccau=caucod"
		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrfan is not null"
		   ."   AND carotrccc is null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9";
           	
	//echo $query1."<br>";
	}
	
  $err_o1 = odbc_do($conexi,$query1);
			
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o1))
   {
	 $Num_Filas++;
	  	
	 $fue1    = odbc_result($err_o1,1);//FUENTE_NOTA
	 $doc1    = odbc_result($err_o1,2);//NOTA
	 $ced1    = odbc_result($err_o1,3);//CODIGO_RESPONSABLE
	 $res1    = odbc_result($err_o1,4);//NOMBRE_RESPONSABLE
	 $cau1    = odbc_result($err_o1,5);//CAUSA
	 $caun1   = odbc_result($err_o1,6);//NOMBRE_CAUSA
	 $fec1    = odbc_result($err_o1,7);//FECHA_NOTA
	 $fan1    = odbc_result($err_o1,8);//FUENTE_ANEXO
	 $dan1    = odbc_result($err_o1,9);//DOCUMENTO_ANEXO
	 
	 
	 echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>SEÑORES:</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$res1."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>NIT:</b>".$ced1."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>FECHA:</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$fec1."</font></td>";
	 echo "</tr>";
	
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>CAUSA : </b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$cau1."&nbsp;&nbsp;".$caun1."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
 	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>DOC-ANE: </b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$fan1."-".$dan1."</font></td>";
	 echo "</tr>";
	 echo "</table>";
	} 
	
 	 echo "<br>";
	 echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>|--DETALLE DE FACTURAS ---|-- DETALLE DE CONCEPTOS CONTABLES-----------------------------------------------------------------------------------|</b></font></td>";
	 echo "</tr>";
	 echo "</table>";
	 
	 echo "<br>";
	
	
 
	
	IF ($cant==0)
	{
	$query2="SELECT carfca,carfac,carval,' ' carcon,' ' cocnom,carvlc,' ' coccue,' ' carotrccc,' ' carotrnit"
           ."  FROM cacar"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carcon is null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
		   ." SELECT carfca,carfac,carval,carcon,cocnom,carvlc,coccue,' ' carotrccc,' ' carotrnit"
           ."  FROM cacar,cococ"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
  		   ."   AND carcon is not null"
		   ."   AND carcon=coccod"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9";
	}
	ELSE
	{	
    $query2="SELECT carfca,carfac,carval,' ' carcon,' ' cocnom,carvlc,' ' coccue,carotrccc,carotrnit"
           ."  FROM cacar,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
           ."   AND carcon is null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrccc is not null"
		   ."   AND carotrnit is not null"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
		   ." SELECT carfca,carfac,carval,carcon,cocnom,carvlc,coccue,' ' carotrccc,carotrnit"
           ."  FROM cacar,cococ,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
  		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrccc is null"
		   ."   AND carotrnit is not null"
		   ."   AND carcon=coccod"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL "
		   ." SELECT carfca,carfac,carval,carcon,cocnom,carvlc,coccue,carotrccc,' ' carotrnit"
           ."  FROM cacar,cococ,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
  		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrccc is not null"
		   ."   AND carotrnit is null"
		   ."   AND carcon=coccod"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9"
		   ." UNION ALL"
		   ." SELECT carfca,carfac,carval,carcon,cocnom,carvlc,coccue,' ' carotrccc,' ' carotrnit"
           ."  FROM cacar,cococ,cacarotr"
           ." WHERE carfue='".$fue."'"
           ."   AND cardoc='".$carg."'"
  		   ."   AND carcon is not null"
		   ."   AND carfue=carotrfue"
		   ."   AND cardoc=carotrdoc"
		   ."   AND carotrccc is null"
		   ."   AND carotrnit is null"
		   ."   AND carcon=coccod"
		   ." GROUP BY 1,2,3,4,5,6,7,8,9";
	}	   
		   
      	    
  //echo $query2."<br>";
  $err_o2 = odbc_do($conexi,$query2);
			
  $Num_Filas = 0;	
  $total1=0;
  $total2=0;
	
     echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>FACTURA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>VALOR</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>CONC</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>NOMBRE-CONCEPTO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>CUENTA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>C.C</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>NIT</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>VLR-CONCEPTO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>% DCTO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</b></font></td>";
 	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>TOTAL-NETO</b></font></td>";
	 echo "</tr>";	
	
  while (odbc_fetch_row($err_o2))
   {
	 $Num_Filas++;
	  	
	 $fca2    = odbc_result($err_o2,1);//FTE_FACTURA
	 $fac2    = odbc_result($err_o2,2);//FACTURA
	 $val2    = odbc_result($err_o2,3);//VLR_NOTA
	 $conc2   = odbc_result($err_o2,4);//CONC_CARTERA
	 $concn2  = odbc_result($err_o2,5);//NOMBRE_CONCEPTO_CARTERA
	 $vlrc2   = odbc_result($err_o2,6);//VALOR_CONCEPTO_CARTERA
	 $concue2 = odbc_result($err_o2,7);//CUENTA_CONCEPTO
	 $concc2  = odbc_result($err_o2,8);//CCOSTO_CONCEPTO
	 $connit2 = odbc_result($err_o2,9);//NIT_CONC_CARTERA
	 
	 
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>FA ".$fac2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".number_format($val2,0,'.',',')."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$conc2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$concn2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$concue2."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$concc2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$connit2."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".number_format($vlrc2,0,'.',',')."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>0</font></td>"; 
	 echo "</tr>"; 
	
	 
	 $total1=$total1+$val2;
	 $total2=$total2+$vlrc2;
	}
	
     echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>TOTALES</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".number_format($total1,0,'.',',')."</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".number_format($total2,0,'.',',')."</b></font></td>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";	 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>"; 
	 echo "</tr>"; 
	 echo "</table>";
	 
	 echo "<br>";

	 echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>--DETALLE DE CONCEPTOS FACTURACION---------------------------------------------------------------------------------------------------------------------</b></font></td>";
	 echo "</tr>";
	 echo "</table>";
	 
	 echo "<br>";

	
  $query3="SELECT movdetcon,connom,movdetcco,movdetnit,movdetdfa,movdetval,movdetvde,movdetval-movdetvde vlr"
         ."  FROM cacar,famovdet,facon"
         ." WHERE carfue='".$fue."'"
         ."   AND cardoc='".$carg."'"
         ."   AND carfue=movdetfue"
         ."   AND cardoc=movdetdoc"
         ."   AND movdetcon=concod"
         ."   AND carcon is null"
         ." UNION ALL"
         ." SELECT ' ' movdetcon,' ' connom,' ' movdetcco,' ' movdetnit,0 movdetdfa,0 movdetval,0 movdetvde,0 vlr"
         ."   FROM cacar"
         ." WHERE carfue='".$fue."'"
         ."   AND cardoc='".$carg."'"
         ."   AND carcon is not null"
		 ." ORDER BY 1,2,3,4,5,6,7,8";
      	    
  //echo $query1."<br>";
  $err_o3 = odbc_do($conexi,$query3);
			
  $Num_Filas = 0;
  $total3=0;
  $total4=0;
  $total5=0;
   
  echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>CPTO</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>DESCRIPCION</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>CCO</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>NIT</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>DIAS</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>VALOR</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>VLR-DCTO</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;&nbsp;&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>VALOR NETO</b></font></td>";
  echo "</tr>"; 
    
  
  while (odbc_fetch_row($err_o3))
   {
	 $Num_Filas++;
	  	
	 $con3    = odbc_result($err_o3,1);//CONCEPTO_FACTURA
	 $nomc3   = odbc_result($err_o3,2);//NOMBRE_CONC_FACTURA
	 $cco3    = odbc_result($err_o3,3);//CENTRO_COSTO
	 $nit3    = odbc_result($err_o3,4);//TERCERO
	 $dfa3    = odbc_result($err_o3,5);//DIAS
	 $valc3   = odbc_result($err_o3,6);//VALOR_CONC_FACTURA
	 $vde3    = odbc_result($err_o3,7)*1;//VALOR_DESCUENTO
	 $vlr3    = odbc_result($err_o3,8)*1;//VALOR_NETO
	 
	 
     echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$con3."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$nomc3."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$cco3."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".$nit3."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'>".number_format($dfa3,0,'.',',')."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".number_format($valc3,0,'.',',')."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".number_format($vde3,0,'.',',')."</font></td>"; 
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
	 echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'>".number_format($vlr3,0,'.',',')."</font></td>"; 
	 echo "</tr>"; 
	
	 	 	 
	 $total3=$total3+$valc3;
	 $total4=$total4+$vde3;
	 $total5=$total5+$vlr3;
    
   }
    
  echo "<tr>";  
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>TOTALES : </b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".number_format($total3,0,'.',',')."</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".number_format($total4,0,'.',',')."</b></font></td>"; 
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".number_format($total5,0,'.',',')."</b></font></td>";
  echo "</tr>"; 
  echo "</table>";
 
  
  $query4="SELECT count(*)"
         ."  FROM cacarobs"
         ." WHERE carobsfue='".$fue."'"
         ."   and carobsdoc='".$carg."'"
         ."   and carobsnum=1"; 
		 
  $err_o4 = odbc_do($conexi,$query4);
			
  $cant1 = odbc_result($err_o4,1);//CANTIDAD OBSERVACION 1
  
  IF ($cant1==0)
  {
   $linea1=' ';  
  }
  ELSE
  {
   $query10="SELECT carobsdes"
          ."  FROM cacarobs"
          ." WHERE carobsfue='".$fue."'"
          ."   and carobsdoc='".$carg."'"
          ."   and carobsnum=1"; 
		 
  $err_o10 = odbc_do($conexi,$query10);
			
  $linea1 = odbc_result($err_o10,1);//OBSERVACION 1
  }
  
  
  $query5="SELECT count(*)"
         ."  FROM cacarobs"
         ." WHERE carobsfue='".$fue."'"
         ."   and carobsdoc='".$carg."'"
         ."   and carobsnum=2"; 
		 
  $err_o5 = odbc_do($conexi,$query5);
			
  $cant2 = odbc_result($err_o5,1);//CANTIDAD OBSERVACION 2
  
  IF ($cant2==0)
  {
   $linea1=$linea1 . ' ';  
  }
  ELSE
  {
   $query11="SELECT carobsdes"
          ."  FROM cacarobs"
          ." WHERE carobsfue='".$fue."'"
          ."   and carobsdoc='".$carg."'"
          ."   and carobsnum=2"; 
		 
  $err_o11 = odbc_do($conexi,$query11);
			
  $linea1 = $linea1 . odbc_result($err_o11,1);//OBSERVACION 1 + OBSERVACION 2
  }
  
  //==========================================================================
  $query6="SELECT count(*)"
         ."  FROM cacarobs"
         ." WHERE carobsfue='".$fue."'"
         ."   and carobsdoc='".$carg."'"
         ."   and carobsnum=3"; 
		 
  $err_o6 = odbc_do($conexi,$query6);
			
  $cant3 = odbc_result($err_o6,1);//CANTIDAD OBSERVACION 3
  
  IF ($cant3==0)
  {
   $linea2=' ';  
  }
  ELSE
  {
   $query12="SELECT carobsdes"
          ."  FROM cacarobs"
          ." WHERE carobsfue='".$fue."'"
          ."   and carobsdoc='".$carg."'"
          ."   and carobsnum=3"; 
		 
  $err_o12 = odbc_do($conexi,$query12);
			
  $linea2 = odbc_result($err_o12,1);//OBSERVACION 3
  }
  
  $query7="SELECT count(*)"
         ."  FROM cacarobs"
         ." WHERE carobsfue='".$fue."'"
         ."   and carobsdoc='".$carg."'"
         ."   and carobsnum=4"; 
		 
  $err_o7 = odbc_do($conexi,$query7);
			
  $cant4 = odbc_result($err_o7,1);//CANTIDAD OBSERVACION 4
  
  IF ($cant4==0)
  {
   $linea2=$linea2 . ' ';  
  }
  ELSE
  {
   $query13="SELECT carobsdes"
          ."  FROM cacarobs"
          ." WHERE carobsfue='".$fue."'"
          ."   and carobsdoc='".$carg."'"
          ."   and carobsnum=4"; 
		 
  $err_o13 = odbc_do($conexi,$query13);
			
  $linea2 = $linea2 . odbc_result($err_o13,1);//OBSERVACION 3 + OBSERVACION 4
  }
  
  //==========================================================================
  
  $query8="SELECT count(*)"
         ."  FROM cacarobs"
         ." WHERE carobsfue='".$fue."'"
         ."   and carobsdoc='".$carg."'"
         ."   and carobsnum=5"; 
		 
  $err_o8 = odbc_do($conexi,$query8);
			
  $cant5 = odbc_result($err_o8,1);//CANTIDAD OBSERVACION 5
  
  IF ($cant5==0)
  {
   $linea3=' ';  
  }
  ELSE
  {
   $query14="SELECT carobsdes"
          ."  FROM cacarobs"
          ." WHERE carobsfue='".$fue."'"
          ."   and carobsdoc='".$carg."'"
          ."   and carobsnum=5"; 
		 
  $err_o14 = odbc_do($conexi,$query14);
			
  $linea3 = odbc_result($err_o14,1);//OBSERVACION 5
  }
  
  $query9="SELECT count(*)"
         ."  FROM cacarobs"
         ." WHERE carobsfue='".$fue."'"
         ."   and carobsdoc='".$carg."'"
         ."   and carobsnum=6"; 
		 
  $err_o9 = odbc_do($conexi,$query9);
			
  $cant6 = odbc_result($err_o9,1);//CANTIDAD OBSERVACION 6
  
  IF ($cant6==0)
  {
   $linea3=$linea3 . ' ';  
  }
  ELSE
  {
   $query15="SELECT carobsdes"
          ."  FROM cacarobs"
          ." WHERE carobsfue='".$fue."'"
          ."   and carobsdoc='".$carg."'"
          ."   and carobsnum=6"; 
		 
  $err_o15 = odbc_do($conexi,$query15);
			
  $linea3 = $linea3 . odbc_result($err_o15,1);//OBSERVACION 5 + OBSERVACION 6
  }
  
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
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Observaciones:</b>".$linea1."</font></td>";
  echo "</tr>";
  
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$linea2."</font></td>";
  echo "</tr>";
  
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$linea3."</font></td>";
  echo "</tr>"; 
  
  echo "</table>";
  
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  echo "<br>";
  
  echo "<table border=0 align=LEFT size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";  
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>Valor en letras: </b>".montoescrito($total1)."</font></td>"; //************************************
  echo "</tr>";  
  echo "</table>";
  
  echo "<br>";
  echo "<br>";
  echo "<br>";
  
  echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>MISION: Existimos para mejorar la salud de las personas y contribuir a la calidad de vida en el mundo</b></font></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'>-------------------------------------------------------------------------------------------------------------------</font></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Cra.80 Diagonal 75 B nro. 2 A 80 - 140  Conmutador: 3421010  Fax: 341 29 46 - Medellín - Colombía - Sur America.</b></font></td>";
  echo "</tr>";
  echo "</table>";
  
  odbc_close($conexi);
  odbc_close_all();
  
 }
}
?>

