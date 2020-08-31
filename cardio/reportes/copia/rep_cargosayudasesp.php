<html>
<head>
<title>Impresión Cargos de Ayudas Diagnosticas Especial- Unix</title>
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
*                                           IMPRESION DEL DETALLE DE LOS EXAMENES DE AYUDAS DIAGNOSTICAS ESPECIAL - UNIX                            *
********************************************************************************************************************************************/
//==========================================================================================================================================
//PROGRAMA				      :Impresión cargos ayudas.                                                                                     |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Septiembre 22 DE 2014.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  :22 de Septiembre de 2014.                                                                                    |
//DESCRIPCION			      :Este programa sirve para imprimir el detalle del cargo de ayudas diagnosticas Especial sin tercero.          |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000041       : Tabla de Pacientes.                                                                                                   | 
//uvglobal_000001   : Tabla Maestro de articulos.                                                                                           | 
//uvglobal_000133   : Tabla donde se ingresa la orden del laboratorio.                                                                      |
//uvglobal_000085   : Tabla del profesional.                                                                                                |
//uvglobal_000041   : Tabla de Factura.                                                                                                     |
//==========================================================================================================================================

include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 22-Septiembre-2014";

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
 echo "<form name='forma' action='rep_cargosayudasesp.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($carg) or $carg == '' )
  {
  	echo "<form name='rep_cargosayudasesp' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//FUENTE Y CARGO DE AYUDAS
 	echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>IMPRESION CARGO DE AYUDAS DIAGNOSTICAS</td></tr>";
	echo"<tr><td align=center bgcolor=#cccccc >FUENTE DEL CARGO :</TD><td align=center bgcolor=#cccccc ><input style='text-transform: uppercase;' type='input' name='fue' ></td></tr>";
	echo"<tr><td align=center bgcolor=#cccccc >NUMERO DEL CARGO :</TD><td align=center bgcolor=#cccccc ><input type='input' name='carg'></td></tr>";
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
  echo "<tr><td colspan='1' align=center><IMG SRC='/MATRIX/images/medical/calidad/clnicanuevo.gif' WIDTH=280 HEIGHT=100></td>"; //trae el logo de la clinica.
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
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".$nom1."</b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>INGRESO</b></font></td>";
	 echo "</tr>";
	 	 
	 echo "<tr>";
     echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>Conm. ".$tel1." ".$noc1."<b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>".$nit1."</b></font></td>";
	 echo "<td align=CENTER bgcolor=#FFFFFF ><font size='3' text color='#003366'><b>Nro.".$carg."</b<</font></td>";
	 echo "</tr>";
	 echo "</table>";
	 	
	}

	echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>--------------------------------------------------------------------------------------------------------------</font></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=LEFT   bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>PACIENTE</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font></td>";
	echo "<td aling=CENTER bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>NUMERO:</b></font></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	echo "<tr>";
	echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>--------------------------------------------------------------------------------------------------------------</font></td>";
	echo "</tr>";
	echo "</table>";
	
  $query2="SELECT movdoc,movape,movap2,movnom,movtid,movced,movfec,movdir,movtel,YEAR(movfec)-YEAR(movnac) edad,movsex,movotrtus,barnom,movotrest,
                  movotrniv,movotrzon,movtse,movotrpad,mednom,dianom,movcer,movres,movtar,tarnom,movpol"
		  ." FROM aymov,aymovotr,inmed,india,intar,outer inbar "
		  ." WHERE movfue='".$fue."'"
          ."  and movdoc='".$carg."'"
          ."  and movanu='0'"
		  ."  AND movap2 is not null"
          ."  and movfue=movotrfue"
          ."  and movdoc=movotrdoc"
          ."  and movotrbar=barcod"
          ."  and movmed=medcod"
          ."  and movdia=diacod"
         // ."  and barcod<>'0000001' "
          ."  and movtar=tarcod "
		  ." UNION ALL "
		  ." SELECT movdoc,movape,' ' movap2,movnom,movtid,movced,movfec,movdir,movtel,YEAR(movfec)-YEAR(movnac) edad,movsex,movotrtus,barnom,movotrest,
                    movotrniv,movotrzon,movtse,movotrpad,mednom,dianom,movcer,movres,movtar,tarnom,movpol"
		  ." FROM aymov,aymovotr,inmed,india,intar,outer inbar "
		  ." WHERE movfue='".$fue."'"
          ."  and movdoc='".$carg."'"
          ."  and movanu='0'"
		  ."  AND movap2 is null"
          ."  and movfue=movotrfue"
          ."  and movdoc=movotrdoc"
          ."  and movotrbar=barcod"
          ."  and movmed=medcod"
          ."  and movdia=diacod"
         // ."  and barcod<>'0000001' "
          ."  and movtar=tarcod ";
		    
  //echo $query1."<br>";
  $err_o2 = odbc_do($conexi,$query2);
			
  $Num_Filas = 0;	
	
  while (odbc_fetch_row($err_o2))
   {
	 $Num_Filas++;
	  	
	 $doc2   = odbc_result($err_o2,1);//NUMERO INGRESO
	 $ape2   = odbc_result($err_o2,2);//APELLIDO_1 PACIENTE
	 $ap22   = odbc_result($err_o2,3);//APELLIDO2_2 PACIENTE
	 $nom2   = odbc_result($err_o2,4);//NOMBRES_PACIENTE
	 $tid2   = odbc_result($err_o2,5);//TIPO_IDENTIFICACION
	 $ced2   = odbc_result($err_o2,6);//IDENTIFICACION
	 $fec2   = odbc_result($err_o2,7);//FECHA_CARGO
	 $dir2   = odbc_result($err_o2,8);//DIRECCION_PACIENTE
	 $tel2   = odbc_result($err_o2,9);//TELEFONO_PACIENTE
	 $edad2  = odbc_result($err_o2,10);//EDAD
	 $sex2   = odbc_result($err_o2,11);//SEXO
	 $tipu2  = odbc_result($err_o2,12);//TIPO_USUARIO
	 $barr2  = odbc_result($err_o2,13);//NOMBRE_BARRIO
	 $estr2  = odbc_result($err_o2,14);//ESTRATO
	 $nivel2 = odbc_result($err_o2,15);//NIVEL
	 $zona2  = odbc_result($err_o2,16);//ZONA
	 $tips2  = odbc_result($err_o2,17);//TIPO_SERVICIO
	 $nomp2  = odbc_result($err_o2,18);//NOMBRE_PADRES
	 $nomm2  = odbc_result($err_o2,19);//NOMBRE_MEDICO
	 $nomd2  = odbc_result($err_o2,20);//NOMBRE_DIAGNOSTICO
	 $codr2  = odbc_result($err_o2,21);//CODIGO_RESPONSABLE
	 $nomr2  = odbc_result($err_o2,22);//NOMBRE_RESPONSABLE
	 $codt2  = odbc_result($err_o2,23);//CODIGO_TARIFA
	 $nomt2  = odbc_result($err_o2,24);//NOMBRE_TARIFA
	 $pol2   = odbc_result($err_o2,25);//POLIZA

	 
	 $nompac=$ape2." ".$ap22." ".$nom2;
	 $identifi=$tid2."-".$ced2;
	 
	 echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NOMBRE</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$nompac."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>IDENTIF.</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$identifi."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>HISTORIA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$ced2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>FECHA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$fec2."</font></td>";
	 echo "</tr>";
	
	 
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DIRECCION</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$dir2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TELEFONO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$tel2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>EDAD</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$edad2." AÑOS</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>SEXO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$sex2."</font></td>";
	 echo "</tr>";
	 
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TIPO-USUARIO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$tipu2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>BARRIO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$barr2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>ESTRATO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$estr2." </font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>NIVEL</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$nivel2."</font></td>";
	 echo "</tr>";
	 
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>ZONA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$zona2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TIPO-SER</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$tips2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>PADRES</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$nomp2."</font></td>";
	 echo "</tr>";
	
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>MEDICO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$nomm2."</font></td>";
	 echo "</tr>";
	 
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DIAGNOSTICO</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$nomd2."</font></td>";
	 echo "</tr>";
	 echo "</table>";

	 echo "<table border=0 align=center size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
     echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>--------------------------------------------------------------------------------------------------------------</font></td>";
	 echo "</tr>";
	 echo "</table>";
	 
	 echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>DATOS DEL RESPONSABLE:</b></font></td>";
	 echo "</tr>";
	 echo "</table>";
	 
	 $tarifa=$codt2." ".$nomt2;
	 
	 echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>EMPRESA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$nomr2."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>CED/NIT</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$codr2."</font></td>";
	 echo "</tr>";
	
	 echo "<tr>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TARIFA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$tarifa."</font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>POLIZA</b></font></td>";
	 echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>:".$pol2."</font></td>";
	 echo "</tr>";
	 echo "</table>";
   }

   echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>--------------------------------------------------------------------------------------------------------------</font></td>";
   echo "</tr>";
   echo "</table>";
   
   
  $query3="SELECT cardetcon,connom,cardetcod cod,cupdes des,cardetcan,sum(cardettot)"
         ."  FROM aycardet,facon,incup,inexa"
         ." WHERE cardetfue='".$fue."'"
         ."   and cardetdoc='".$carg."'"
         ."   and cardetcod=exacod "
         ."   and exaane=cupcod "
         ."   and cardetcon=concod"
         ."   and cardetfac='S'"
         ."   and cardetanu='0'"
		 ." GROUP BY 1,2,3,4,5"
         ." UNION ALL "
         ."SELECT cardetcon,connom,drodetart cod,artnom des,cardetcan,sum(cardettot)"
         ."  FROM aycardet,facon,ivdrodet,ivart"
         ." WHERE cardetfue='".$fue."'"
         ."   and cardetdoc='".$carg."'"
         ."   and cardetcon=concod"
         ."   and cardetfue=drodetfue"
         ."   and cardetdoc=drodetdoc"
         ."   and cardetite=drodetite"
         ."   and drodetart=artcod"
         ."   and cardetfac='S'"
         ."   and cardetanu='0'"
         ." GROUP BY 1,2,3,4,5"
		 ." UNION ALL "
         ."SELECT cardetcon,connom,cardetcod cod,cupdes des,cardetcan,SUM(cardettot)"
         ."  FROM facardet,facon,incup,inexa"
         ." WHERE cardetfue='".$fue."'"
         ."   and cardetdoc='".$carg."'"
         ."   and cardetcod=exacod "
         ."   and exaane=cupcod "
         ."   and cardetcon=concod"
         ."   and cardetfac='S'"
         ."   and cardetanu='0'"
		 ." GROUP BY 1,2,3,4,5"
         ." UNION ALL "
         ."SELECT cardetcon,connom,drodetart cod,artnom des,cardetcan,SUM(cardettot)"
         ."  FROM facardet,facon,ivdrodet,ivart"
         ." WHERE cardetfue='".$fue."'"
         ."   and cardetdoc='".$carg."'"
         ."   and cardetcon=concod"
         ."   and cardetfue=drodetfue"
         ."   and cardetdoc=drodetdoc"
         ."   and cardetite=drodetite"
         ."   and drodetart=artcod"
         ."   and cardetfac='S'"
         ."   and cardetanu='0'"
		 ." GROUP BY 1,2,3,4,5";
   
  $err_o3 = odbc_do($conexi,$query3);
			
  $Num_Filas = 0;	
	
  echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>RELACION DE EXAMENES</b></font></td>";
  echo "</tr>";
  echo "</table>";
	
  $total=0;
  
  echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Cpto</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Descripción</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Codigo</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Descripción</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Can.</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>Valor</b></font></td>";
  echo "</tr>";
  
  $totmed=0;
  $totexa=0;
  while (odbc_fetch_row($err_o3))
   {
	$Num_Filas++;
	  	
	$con3   = odbc_result($err_o3,1);//CONCEPTO
	$nomc3  = odbc_result($err_o3,2);//NOMBRE CONCEPTO
	$cod3   = odbc_result($err_o3,3);//CODIGO EXAMEN
	$des3   = odbc_result($err_o3,4);//NOMBRE EXAMEN
	$can3   = odbc_result($err_o3,5);//CANTIDAD
	$tot3   = odbc_result($err_o3,7);//TOTAL_CARGO
	 
    echo "<tr>";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$con3."</font></td>";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$nomc3."</font></td>";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$cod3."</font></td>";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$des3."</font></td>";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".$can3."</font></td>";
    echo "<td aling=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>".number_format($tot3,0,'.',',')."</font></td>";
    echo "</tr>";

    IF ($con3=='0616' or $con3=='0626')
    {
     $totmed=$totmed+$tot3;	
    }
    ELSE
    {
     $totexa=$totexa+$tot3;	
    }
    
    $total = $total + $tot3;
    
    
   }
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>TOTAL</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>&nbsp;</font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($total,0,'.',',')."</b></font></td>";
   
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
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR MEDICAMENTOS</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totmed,0,'.',',')."</b></font></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>EXAMEN / PROCEDIMIENTO</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($totexa,0,'.',',')."</b></font></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>VALOR RECONOCIDO</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>".number_format($total,0,'.',',')."</b></font></td>";
  echo "</tr>"; 
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=RIGHT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>A PAGAR</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>&nbsp;</b></font></td>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'><b>0</b></font></td>";
  echo "</tr>"; 
  echo "</table>";
 
  $query4="SELECT atedoc"
         ."  FROM msate"
         ." WHERE atefue='".$fue."'"
         ."   and atedto='".$carg."'";
         
  $err_o4 = odbc_do($conexi,$query4);
			
  $Num_Filas = 0;	
  
  while (odbc_fetch_row($err_o4))
   {
	$Num_Filas++;
	  	
	$rips4   = odbc_result($err_o4,1);//CONSECUTIVO DEL RIPS
	
	echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr>";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>-------------------------------------------------------RIP: $rips4----------------------------------</font></td>";
    echo "</tr>";
    echo "</table>";
   }
  
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";
  echo "<tr>";
  echo "</tr>";

  IF ($fue=='CN' or $fue=='cn')
  {  
   echo "<table border=0 align=CENTER size='100'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=CENTER bgcolor=#FFFFFF ><font size='1' text color='#003366'><b>Por manejo interno de distribución de honorarios entre los médicos de la unidad, el valor correspondiente al examen se divide en partes iguales para los médicos.</b></font></td>";
   echo "</tr>";
   echo "</table>";
  }

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
  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='1' text color='#003366'><b>MISION: Existimos para mejorar la salud de las personas y contribuir a la calidad de vida en el mundo</b></font></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=LEFT bgcolor=#FFFFFF ><font size='2' text color='#003366'>--------------------------------------------------------------------------------------------------------------</font></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td align=CENTER bgcolor=#FFFFFF ><font size='1' text color='#003366'><b>Cra.80 Diagonal 75 B nro. 2 A 80 - 140  Conmutador: 3421010  Fax: 341 29 46 - Medellín - Colombía - Sur America.</b></font></td>";
  echo "</tr>";
  echo "</table>";
  
  // para cerrar la conexion con UNIX.
  liberarConexionOdbc( $conexi );
  odbc_close_all();
  
 }
}
?>

