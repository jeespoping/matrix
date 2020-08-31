<html>
<head>
<title>Examenes Especiales</title>
<link href="/matrix/root/tavo.css" rel="stylesheet" type="text/css" />
</head>
<font face='arial'>

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_gesreque.submit();
	}
	
	function cerrar_ventana()
	{
		window.close()
	}
</script>


<?php
include_once("conex.php");

//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false">
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

/*******************************************************************************************************************************************
*                                                REPORTE DE EXAMENES ESPECIALES DE NUCLEAR                                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Examenes Especiales.                                                                                         |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :SEPTIEMBRE 25 DE 2007.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  :25 de Septiembre de 2007.                                                                                    |
//DESCRIPCION			      :Este programa sirve para imprimir y observar los examenes de nuclear por rango de fecha.                     |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//aymov             : Tabla de Moviemnto de ayudas diagnosticas.                                                                            |
//inexa             : Tabla de Examenes.                                                                                                    |
//aycardet          : Tabla de Detalles de movimiento.                                                                                      |
//inmegr            : Tabla de pacientes, para saber cuando fue egresado.                                                                   |
//==========================================================================================================================================
$wactualiz="Ver. 2007-09-26";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
 $empresa='root';

 

 

 
 echo "<center><table border=1>";
 echo "<tr><td align=center colspan=17 bgcolor=#006699><font size=5 text color=#FFFFFF><b> REPORTE DE EXAMENES ESPECIALES - MEDICINA NUCLEAR </b></font><br><font size=2 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

 
if (!isset($fec1) or $fec1 == '' )
  {
   echo "<form name='rep_exaesp' action='' method=post>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
   if (isset($fec1))
    {
     $fec1=$fec1;
    }
   else
    {
   	 $fec1='';
    }

    if (isset($fec2))
    {
     $fec2=$fec2;
    }
   else
    {
   	 $fec2='';
    }
    
   echo "<Tr >";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Cargo Inicial&nbsp(AAAAMMDD):<i><br></font></td>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Cargo Final&nbsp (AAAAMMDD):<i><br></font></b></td>";
   echo "</Tr >";
    
   echo "<Tr >"; 
   echo "<td bgcolor='#dddddd' align=center><INPUT TYPE='text' NAME='fec1' VALUE='".$fec1."'></td>";
   echo "<td bgcolor='#dddddd' align=center><INPUT TYPE='text' NAME='fec2' VALUE='".$fec2."'></td>";
   echo "</Tr>";
   
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
  }
 else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
  
   echo "<tr><td align=center colspan=17 bgcolor=#FFFFFF><font text color=#003366 size=2><b>FECHA : <i>".$fec1." a  <i>".$fec2."</td></tr>";
          
   echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>FUENTE</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>INGRESO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>FECHA</b></font></td>" .
	    "<td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>NOMBRES</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>APELLIDO_1</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>APELLIDO_2</b></font></td>" .
	    "<td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>EDAD</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>CEDULA</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>TELEFONOS</b></font></td>". 
	    "<td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>NOMBRE RESPONSABLE</b></font></td>".
	    "<td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>COD_MEDICO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>NOMBRE MEDICO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>DOSIS</b></font></td>".
	    "<td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>COMPLICACIONES SI - NO - CUAL?</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>DOSIS - 1 METRO(Mr/Hora)</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>CONTAMINACION SI - NO - DONDE?</b></font></td>".
	    "<td align=center bgcolor=#006699><font text color=#FFFFFF size=1><b>FECHA EGRESO</b></font></td></tr>";
   
   $ano=substr($fec1,0,4);
   $mes=substr($fec1,4,2);
   
   $conexi=odbc_connect('facturacion','','')
	    or die("No se realizo conexión con la BD Facturación");
	
   $quer1 =" SELECT movfue,movdoc,movfec,movnom,movape,movap2,year(today)-year(movnac) edad,movced,movtel,movres,cardetcod cod,exanom,movmed,movtip,movfec fecegr,comnuc,dosnuc,connuc"
          ."  FROM aymov,aycardet,inexa,amenuclear"
          ." WHERE aymov.movfue='MN' "
          ."   AND aymov.movano='".$ano."'"
          ."   AND aymov.movmes='".$mes."'"
          ."   AND aymov.movfec between '".$fec1."' and '".$fec2."'"
          ."   AND aymov.movanu='0'"
          ."   AND aymov.movfue=aycardet.cardetfue"
          ."   AND aymov.movdoc=aycardet.cardetdoc"
          ."   AND aycardet.cardetlin=1"
          ."   AND aycardet.cardetcod=inexa.exacod"
          ."   AND inexa.exagex='39'"
          ."   AND aymov.movtip <> 'I' "
          ."   AND aymov.movfue=amenuclear.fuenuc"
          ."   AND aymov.movdoc=amenuclear.ingnuc"
          ." UNION ALL "
          ." SELECT movfue,movdoc,movfec,movnom,movape,movap2,year(today)-year(movnac) edad,movced,movtel,movres,cardetcod cod,exanom,movmed,movtip,egregr fecegr,comnuc,dosnuc,connuc"
          ."   FROM aymov,facardet,inexa,amenuclear,outer inmegr"
          ."  WHERE movfue='MN'"
          ."    AND movano='".$ano."'"
          ."    AND movmes='".$mes."'"
          ."   AND aymov.movfec between '".$fec1."' and '".$fec2."'"
          ."    AND movanu='0'"
          ."    AND movfue=cardetfue"
          ."    AND movdoc=cardetdoc"
          ."    AND cardetlin=1"
          ."    AND cardetcod=exacod"
          ."    AND exagex='39'"
          ."    AND movtip = 'I' "
          ."    AND cardethis=egrhis"
          ."    AND cardetnum=egrnum"
          ."    AND movfue=fuenuc"
          ."    AND movdoc=ingnuc" 
          ." UNION ALL"
          ." SELECT movfue,movdoc,movfec,movnom,movape,movap2,year(today)-year(movnac) edad,movced,movtel,movres,cardetcod cod,exanom,movmed,movtip,movfec fecegr,comnuc,dosnuc,connuc"
          ."  FROM aymov,aycardet,inexa,amenuclear"
          ." WHERE aymov.movfue='MN' "
          ."   AND aymov.movano='".$ano."'"
          ."   AND aymov.movmes='".$mes."'"
          ."   AND aymov.movfec between '".$fec1."' and '".$fec2."'"
          ."   AND aymov.movanu='0'"
          ."   AND aymov.movfue=aycardet.cardetfue"
          ."   AND aymov.movdoc=aycardet.cardetdoc"
          ."   AND aycardet.cardetlin=1"
          ."   AND aycardet.cardetcod=inexa.exacod"
          ."   AND inexa.exagex='50'"
          ."   AND aymov.movtip <> 'I' "
          ."   AND aymov.movfue=amenuclear.fuenuc"
          ."   AND aymov.movdoc=amenuclear.ingnuc"
          ." UNION ALL "
          ." SELECT movfue,movdoc,movfec,movnom,movape,movap2,year(today)-year(movnac) edad,movced,movtel,movres,cardetcod cod,exanom,movmed,movtip,egregr fecegr,comnuc,dosnuc,connuc"
          ."   FROM aymov,facardet,inexa,amenuclear,outer inmegr"
          ."  WHERE movfue='MN'"
          ."    AND movano='".$ano."'"
          ."    AND movmes='".$mes."'"
          ."   AND aymov.movfec between '".$fec1."' and '".$fec2."'"
          ."    AND movanu='0'"
          ."    AND movfue=cardetfue"
          ."    AND movdoc=cardetdoc"
          ."    AND cardetlin=1"
          ."    AND cardetcod=exacod"
          ."    AND exagex='50'"
          ."    AND movtip = 'I' "
          ."    AND cardethis=egrhis"
          ."    AND cardetnum=egrnum"
          ."    AND movfue=fuenuc"
          ."    AND movdoc=ingnuc" 
          ."  ORDER BY 1,2,3,4"
          ." INTO TEMP tempo";
          
  //echo $quer1;
  //echo odbc_error() ."=". odbc_errormsg();
 
          
  $err4 = odbc_do($conexi,$quer1) or die("ERROR EN QUERY"); 
          
          
  $query1 = "SELECT movfue,movdoc,movfec,movnom,movape,movap2,edad,movced,movtel,movres,cod,exanom,movmed,mednom,sum(cardetcan) can,fecegr,comnuc,dosnuc,connuc"
          ."   FROM tempo,aycardet,outer inmed"
          ."  WHERE movfue=cardetfue"
          ."    AND movdoc=cardetdoc"
          ."    AND movtip<>'I'"
          ."    AND movmed=medcod"
          ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,16,17,18,19"
          ." UNION ALL "
          ." SELECT movfue,movdoc,movfec,movnom,movape,movap2,edad,movced,movtel,movres,cod,exanom,movmed,mednom,sum(cardetcan) can,fecegr,comnuc,dosnuc,connuc"
          ."   FROM tempo,facardet,outer inmed"
          ."  WHERE movfue=cardetfue"
          ."    AND movdoc=cardetdoc"
          ."    AND movtip='I'"
          ."    AND movmed=medcod"
          ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,16,17,18,19"
          ."  ORDER BY 11,12,1,2,3";
          
   //echo $query1;
          
        
   //echo odbc_error() ."=". odbc_errormsg();
 
   $err1 = odbc_do($conexi,$query1) or die("ERROR EN QUERY 2");
   $num1 = odbc_num_fields($err1);
      
   $row1=array();

   $i=1;
   $swtitulo='SI';
   $wexaant='0';
   $wexanant='';
   $wcodmant='0';
   $totexamen=0;
   $totgeneral=0;

   $primer=0;
      
   while(odbc_fetch_row($err1))
   {   
   	
    for ($j=1;$j<=$num1;$j++)
	{
	 $row1[$j-1] = odbc_result($err1,$j);
	}
   
  	if (is_int ($i/2))
	  {
	  	$wcf="DDDDDD";  // color de fondo
	  	$i++;
	  }
	else
	  {
	  	$wcf="CCFFFF"; // color de fondo
	  	$i++;
	  }
	   
	if ($swtitulo=='SI')
	 { 
	  if ($primer==0)
	   {
	    $wexaant = $row1[10];
	    $wexanant=$row1[11];
	    	
	    echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>EXAMEN : </b></font></td><td align=center colspan=1><font text color=#000000 size=1>".$row1[10]."</td><td align=left colspan=6><font text color=#000000 size=1>".$row1[11]."</td> </tr>"; 
	    $primer=1;
	    
	   }
	  else 
	  { 
	   echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>EXAMEN : </b></font></td><td align=center colspan=1><font text color=#000000 size=1>".$wexaant."</td><td align=left colspan=6><font text color=#000000 size=1>".$wexanant."</td> </tr>"; 
	  }
	  
	  $swtitulo='NO';

	  if ($wcodmant<>'0')
	  {
	  	echo "<tr bgcolor=".$wcfant."><td align=center><font text color=#000000 size=1>".$wfueant."</td><td align=center><font text color=#000000 size=1>".$wingant."</td><td align=center><font text color=#000000 size=1>".$wfecant."</td><td align=center><font text color=#000000 size=1>".$wnomant."</td><td align=center><font text color=#000000 size=1>".$wap1ant."</td>" .
	      "<td align=center><font text color=#000000 size=1>".$wap2ant."</td><td align=center><font text color=#000000 size=1>".$wedant."</td><td align=center><font text color=#000000 size=1>".$wcedant."</td><td align=center><font text color=#000000 size=1>".$wtelant."</td><td align=center><font text color=#000000 size=1>".$wresant."</td>" .
	      "<td align=center><font text color=#000000 size=1>".$wcodmant."</td><td align=center><font text color=#000000 size=1>".$wnmedant."</td><td align=center><font text color=#000000 size=1>".$wdosisant."</td>" .
	      "<td align=center>".$wcomnuca."</td><td align=center>".$wdosnuca."</td><td align=center>".$wconnuca."</td><td align=center><font text color=#000000 size=1>".$wfeceant."</td></tr>"; 
	  	$wcodmant='0';
	  	$totexamen=$totexamen+1;
	    $totgeneral=$totgeneral+1;
	  }
	 
	 }
	 
	if ($wexaant==$row1[10] )
	 {
	  
	 echo "<tr bgcolor=".$wcf."><td align=center><font text color=#000000 size=1>".$row1[0]."</td><td align=center><font text color=#000000 size=1>".$row1[1]."</td><td align=center><font text color=#000000 size=1>".$row1[2]."</td><td align=center><font text color=#000000 size=1>".$row1[3]."</td><td align=center><font text color=#000000 size=1>".$row1[4]."</td>" .
	      "<td align=center><font text color=#000000 size=1>".$row1[5]."</td><td align=center><font text color=#000000 size=1>".$row1[6]."</td><td align=center><font text color=#000000 size=1>".$row1[7]."</td><td align=center><font text color=#000000 size=1>".$row1[8]."</td><td align=center><font text color=#000000 size=1>".$row1[9]."</td>" .
	      "<td align=center><font text color=#000000 size=1>".$row1[12]."</td><td align=center><font text color=#000000 size=1>".$row1[13]."</td><td align=center><font text color=#000000 size=1>".$row1[14]."</td>" .
	      "<td align=center>".$row1[16]."</td><td align=center>".$row1[17]."</td><td align=center>".$row1[18]."</td><td align=center><font text color=#000000 size=1>".$row1[15]."</td></tr>"; 
	 $totexamen=$totexamen+1;
	 $totgeneral=$totgeneral+1;

	 $tit='NO';
	 }
	else 
	 {
	 echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>TOTAL EXAMEN : </b></font></td><td align=center colspan=1><font text color=#000000 size=1>".$wexaant."</td><td align=center colspan=6><font text color=#000000 size=1>".number_format($totexamen)."</td></tr>";
	 echo "<tr><td alinn=center colspan=17 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	 
	 $wfueant=$row1[0];
	 $wingant=$row1[1];
	 $wfecant=$row1[2];
	 $wnomant=$row1[3];
	 $wap1ant=$row1[4];
	 $wap2ant=$row1[5];
	 $wedant=$row1[6];
	 $wcedant=$row1[7];
	 $wtelant=$row1[8];
	 $wresant=$row1[9];
	 $wexaant=$row1[10];
	 $wexanant=$row1[11];
	 $wcodmant=$row1[12];
	 $wnmedant=$row1[13];
	 $wdosisant=$row1[14];
	 $wfeceant=$row1[15];
	 $wcomnuca=$row1[16];
	 $wdosnuca=$row1[17];
	 $wconnuca=$row1[18];
	 	 
	 $totexamen=0;
	 $swtitulo='SI';
	 $wcfant=$wcf;

	 $tit='SI';
	 }
   } 
   
   if ($tit=='NO')
    {
   	 echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>TOTAL EXAMEN : </b></font></td><td align=center colspan=1><font text color=#000000 size=1>".$wexaant."</td><td align=center colspan=6><font text color=#000000 size=1>".number_format($totexamen)."</td></tr>";
	}
   else 
    {
     echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>EXAMEN : </b></font></td><td align=right colspan=1><font text color=#000000 size=1>".$wexaant."</td><td align=left colspan=6><font text color=#000000 size=1>".$wexanant."</td> </tr>"; 
     echo "<tr bgcolor=".$wcfant."><td align=center><font text color=#000000 size=1>".$wfueant."</td><td align=center><font text color=#000000 size=1>".$wingant."</td><td align=center><font text color=#000000 size=1>".$wfecant."</td><td align=center><font text color=#000000 size=1>".$wnomant."</td><td align=center><font text color=#000000 size=1>".$wap1ant."</td>" .
	      "<td align=center><font text color=#000000 size=1>".$wap2ant."</td><td align=center><font text color=#000000 size=1>".$wedant."</td><td align=center><font text color=#000000 size=1>".$wcedant."</td><td align=center><font text color=#000000 size=1>".$wtelant."</td><td align=center><font text color=#000000 size=1>".$wresant."</td>" .
	      "<td align=center><font text color=#000000 size=1>".$wcodmant."</td><td align=center><font text color=#000000 size=1>".$wnmedant."</td><td align=center><font text color=#000000 size=1>".$wdosisant."</td>" .
	      "<td align=center>".$wcomnuca."</td><td align=center>".$wdosnuca."</td><td align=center>".$wconnuca."</td><td align=center><font text color=#000000 size=1>".$wfeceant."</td></tr>"; 
     
	 $totexamen=$totexamen+1;
	 $totgeneral=$totgeneral+1;
   	 echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>TOTAL EXAMEN : </b></font></td><td align=center colspan=1><font text color=#000000 size=1>".$wexaant."</td><td align=center colspan=6><font text color=#000000 size=1>".number_format($totexamen)."</td></tr>";
		 
    }
    echo "<tr><td alinn=center colspan=17 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
    echo "<tr><td align=center colspan=10 bgcolor=#006699><font text color=#FFFFFF size=1><b>TOTAL GENERAL : </b></font></td><td align=center colspan=7><font text color=#000000 size=1>".number_format($totgeneral)."</td></tr>";
    echo "</table>"; // cierra la tabla o cuadricula de la impresión   			
    
    echo "<table>";

    echo "<tr>"; 
    echo "<td><input type=button value='Cerrar_Ventana' onclick='cerrar_ventana()'></td>";
    echo "</tr>";
    echo "</table>"; 
    
	odbc_close($conexi);
	odbc_close_all();

	
 }// cierre del else donde empieza la impresión

}
?>