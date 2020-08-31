<html>
<head>
<title>Certificado Laboral</title>

<script language="JavaScript1.2">
//Evita usar el boton derecho del ratón
document.oncontextmenu = function(){return false}
//No permite seleccionar el contenido de una página 
function disableselect(e){
return false
}
function reEnable(){
return true
}
document.onselectstart=new Function ("return false")
if (window.sidebar){
document.onmousedown=disableselect
document.onclick=reEnable
}

//Borra el Portapapeles con el uso del teclado
if (document.layers)
document.captureEvents(Event.KEYPRESS)
function backhome(e){
window.clipboardData.clearData();
}

//Borra el Portapapeles con el uso del mouse
document.onkeydown=backhome
function click(){
if(event.button){
window.clipboardData.clearData();
}
}
document.onmousedown=click
//-->
</script>

</head>


<body TEXT="#000066" BGCOLOR="ffffff" onMouseOut="window.clipboardData.clearData(); return false" onMouseOver="window.clipboardData.clearData(); return false" >
<font face='arial'>

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_gesreque.submit();
	}
</script>

</body>
</html>

<?php
include_once("conex.php");
//<body TEXT="#000066" BGCOLOR="ffffff" oncontextmenu = "return false" onselectstart = "return false" ondragstart = "return false" >
// onkeydown="return false" deja inaviñitado el teclado.
//Esta instrucción sirve para apagar el mouse y que no deje hacer nada en la pagina, para que no copie alguna imagen etc etc.

function vfecha($efecha)    // Funcion para sacar el escrito de la fecha Ej: 15 de Julio de 2007 
 {
  $mes=array();
		
  $mes[1]='Enero';
  $mes[2]='Febrero';
  $mes[3]='Marzo';
  $mes[4]='Abril';
  $mes[5]='Mayo';
  $mes[6]='Junio';
  $mes[7]='Julio';
  $mes[8]='Agosto';
  $mes[9]='Septiembre';
  $mes[10]='Octubre';
  $mes[11]='Noviembre';
  $mes[12]='Diciembre';
	   
  $vfecha=substr($efecha,8,2)." de ".$mes[(integer)substr($efecha,5,2)]." de ". substr($efecha,0,4);
  
  return $vfecha; 
 }

/*******************************************************************************************************************************************
*                                                CERTIFICADO LABORAL            		                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Certicado Laboral para el laboratorio médico.                                                                |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :FEBRERO 4 DE 2008.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :20 de Marzo de 2013.                                                                                         |
//DESCRIPCION			      :Este programa sirve para que cada empleado con su codigo pueda imprimir su certificado laboral.              |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//nomina_000001     : Tabla de Nomina, donde se encuentra el escrito de la carta laboral.                                                   |
//root_000050       : Tabla de Empresas.                                                                                                    |
//noper             : Tabla de personal.                                                                                                    |
//noofi             : Tabla de oficios de los empleados.                                                                                    |
//nocot             : Tabla donde esta el nombre de los contratos.                                                                          |
//==========================================================================================================================================
$wactualiz="Ver. 2013-03-20";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
 $empresa='root';

 

 include_once("root/montoescrito.php");  //para llamar la funcion de monto escrito.
 


if (!isset($empre) or $empre=='' )
  {
  	
   echo "<center><table border=1>";
   echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=6 text color=#FFFFFF><b> CERTIFICADO LABORAL - LABORATORIO CLINICO </b></font><br><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";	
   echo "<form name='rep_certilab' action='' method=post>";

   /////////////////////////////////////////////////////////////////////////////////////// seleccion para Empresa para saber de donde es el empleado
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Empresa: <br></font></b><select name='empre' onchange='enter()'>";

   $query = " SELECT Empcod,Empdes"
	       ."   FROM ".$empresa."_000050"
	       ."  WHERE Empest='on'"
	       ."  ORDER BY Empcod,Empdes";

   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
   $emp=explode('-',$empre); 
   
   $codemp = $emp[0];
      
   if ($codemp == "")
    { 
     echo "<option></option>";
     $codemp = "";   	 
    }
   else 
    {
 	 echo "<option>".$emp[0]."-".$emp[1]."</option>";
   	} 
   

   for ($i=1;$i<=$num;$i++)
	{
	 $row = mysql_fetch_array($err);
	 echo "<option>".$row[0]."-".$row[1]."</option>";
	}
   
   echo "</select></td>";
    
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
   echo "</tr>";
     
  }  
 else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION

   $emp=explode('-',$empre); 
   
   $codemp = $emp[0];

   $query2 =" SELECT Encabezado,Piepag1,Piepag2,Piepag3"
	         ." FROM nomina_000001"; 
   //echo mysql_errno() ."=". mysql_error();
	         
   $err2 = mysql_query($query2,$conex);
   $num2 = mysql_num_rows($err2);
   $row2 = mysql_fetch_array($err2);
   $Carta=ucfirst($row2[0]);
   $Cartap=explode('-',$row2[1]);
   $Cartap1=ucfirst($Cartap[0]);
   $Cartap2=$Cartap[1];
   $CartaL=ucfirst($row2[2]);
   $CartaA=ucfirst($row2[3]);
   
   
   switch ($codemp)  //Para hacer la conexion por ODBC, dependiendo de la empresa.
	{
	 case "01":    // Clinica Las Americas
	  {
	   $conexi=odbc_connect('nomina','','')	
	       or die("No se realizo conexión con la BD Nomina - Clinica las Americas");
	 	   
	   break;
	  }   
	 case "02":    // Clinica Del Sur
	  {
	   $conexi=odbc_connect('nomsur','','')
           or die("No se realizo conexión con la BD Nomina - Clinica del Sur");
           	 	
       break;
	  }
	 case "03":    // FarmaStore
	  {
	   $conexi=odbc_connect('nomsto','','')
	       or die("No se realizo conexión con la BD Nomina - FarmaStore");
	   
       break;
	  }
	 case "04":    // Patologia las Americas
	  {
	   $conexi=odbc_connect('nompat','','')
	       or die("No se realizo conexión con la BD Nomina - Patologia");
	   
       break;
	  }
	 case "05":    // Laboratorio las Americas
	  {
	   $conexi=odbc_connect('nomlab','','')
	       or die("No se realizo conexión con la BD Nomina - Laboratorio");
	   
       break;
	  }
	}
   ///////////////////////////////////////////////////////////////////////////////////////// codigo del empleado dependiendo la empresa
   
   $cod=explode('-',$user); //Aca traigo de la variable global $user el codigo del empleado por el cual ingreso al matrix.
   
   //$cod[1]='L00007';
   
   $codi=substr($cod[1], 1);
 
   /*
   $quer11=" SELECT count(*)"
          ."   FROM nonovpre"
          ."  WHERE novprecod='".$codi."'"
          ."    AND novprecon='0030'";
          
   $err11 = odbc_do($conexi,$quer11);        
   $cant = odbc_result($err11,$i);
   }
   */
   
   $i=1;
   
   $cant = 0;
   
   
  IF ($cant>0)
   {
   $query1=" SELECT perap1,perap2,perno1,perno2,perced,perfin,ofinom,perhco,cotnom,perbme,novpreval*2"
          ."   FROM noper,noofi,nocot,nonovpre"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND percod=novprecod"
          ."    AND novprecon='0030'"
          ."    AND perap2 is not null"
          ."    AND perno2 is not null"
          ." UNION ALL"
          ." SELECT perap1,' ' perap2,perno1,perno2,perced,perfin,ofinom,perhco,cotnom,perbme,novpreval*2"
          ."   FROM noper,noofi,nocot,nonovpre"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND percod=novprecod"
          ."    AND novprecon='0030'"
          ."    AND perap2 is null"
          ."    AND perno2 is not null"
          ." union all"
          ." SELECT perap1,perap2,perno1,' ' perno2,perced,perfin,ofinom,perhco,cotnom,perbme,novpreval*2"
          ."   FROM noper,noofi,nocot,nonovpre"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND percod=novprecod"
          ."    AND novprecon='0030'"
          ."    AND perap2 is not null"
          ."    AND perno2 is null"
          ." union all"
          ." SELECT perap1,' ' perap2,perno1,' ' perno2,perced,perfin,ofinom,perhco,cotnom,perbme,novpreval*2"
          ."   FROM noper,noofi,nocot,nonovpre"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND percod=novprecod"
          ."    AND novprecon='0030'"
          ."    AND perap2 is null"
          ."    AND perno2 is null";
    }
    ELSE
    {
   $query1=" SELECT perap1,perap2,perno1,perno2,perced,perfin,ofinom,perhco,cotnom,perbme,0"
          ."   FROM noper,noofi,nocot"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND perap2 is not null"
          ."    AND perno2 is not null"
          ." UNION ALL"
          ." SELECT perap1,' ' perap2,perno1,perno2,perced,perfin,ofinom,perhco,cotnom,perbme,0"
          ."   FROM noper,noofi,nocot"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND perap2 is null"
          ."    AND perno2 is not null"
          ." union all"
          ." SELECT perap1,perap2,perno1,' ' perno2,perced,perfin,ofinom,perhco,cotnom,perbme,0"
          ."   FROM noper,noofi,nocot"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND perap2 is not null"
          ."    AND perno2 is null"
          ." union all"
          ." SELECT perap1,' ' perap2,perno1,' ' perno2,perced,perfin,ofinom,perhco,cotnom,perbme,0"
          ."   FROM noper,noofi,nocot"
          ."  WHERE percod='".$codi."'"
          ."    AND perofi=oficod"
          ."    AND peretr='A'"
          ."    AND percot=cotcod"
          ."    AND perap2 is null"
          ."    AND perno2 is null"; 
    }
    
   //echo odbc_error() ."=". odbc_errormsg();
 
   $err1 = odbc_do($conexi,$query1);
   $num1 = odbc_num_fields($err1);
   
   $row1=array();
   for ($i=1;$i<=$num1;$i++)
	{
	 $row1[$i-1] = odbc_result($err1,$i);
	}
   
   $preval=$row1[10];
   
   
   IF ($preval=='')
    {
     $preval=0;	
    }
	
   $ced=$row1[4];
   $fin=$row1[5];
   $ofin=$row1[6];
   $hco=$row1[7];
   $cnom=$row1[8];
   $bme=$row1[9]+$preval;
   
   $nombreempl=$row1[2]." ".$row1[3]." ".$row1[0]." ".$row1[1];
   
   $hoy=date("Y-m-d");
  	
    echo "<center><table border=0>";  //border=0 no muestra la cuadricula en 1 si.
    echo "<tr><td colspan='3' align=center><IMG SRC='/MATRIX/images/medical/laboratorio/logolabora.jpg' WIDTH=300 HEIGHT=170></td>"; //trae el logo del laboratorio.
    echo "<tr><td align=center colspan='3' bgcolor=#FFFFFF><font size='2' color='#000066'><b>Nit. 800.209.804-6</b></font></td></tr>";
    echo "<tr><td align=left colspan='3' bgcolor='#FFFFFF'><font size='2' color='#000000'>".vfecha($hoy)."</font></td></tr>";
	echo "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	echo "<tr><td align=center colspan='3' bgcolor='#FFFFFF'><font size='5' color='#000000'><b>LA COORDINACION DE NOMINA</b></font></td></tr>";
	echo "<tr><td align=center colspan='3' bgcolor='#FFFFFF'><font size='5' color='#000000'><b>HACE CONSTAR</b></font></td></tr>";
	echo "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='2' color='#000000'>".$Carta."</font></td></tr>";
    echo "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Empleado</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".$nombreempl."</b></font></td></tr>";
    echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Cedula de Ciudadania</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".$ced."</b></font></td></tr>";	
    echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Fecha de Inicio del Contrato Actual</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".vfecha($fin)."</b></font></td></tr>";
    echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Cargo Actual</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".$ofin."</b></font></td></tr>";  		
    echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Tipo de Contrato</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".$cnom."</b></font></td></tr>";
    echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Horas Laboradas al Mes</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".$hco."</b></font></td></tr>";
    echo "<tr><td align=left bgcolor=#FFFFFF><font size='2' color='#000000'>Salario Básico</td><td align=center><font size='2' color='#000000'>:</td><td align=left><font size='2' color='#000000'><b>".number_format($bme,0,'.',',')."</b></font></td></tr>";
    echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='2' color='#000000'><b>".montoescrito($bme)."</font></td></tr>";
    //echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='2' color='#000000'>".$Cartap1."</font></td></tr>";
    //echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='2' color='#000000'>".$Cartap2."</font></td></tr>";
    echo "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
    echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='2' color='#000000'>".$CartaL."</font></td></tr>"; 
    echo "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
    echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='2' color='#000000'>".$CartaA."</font></td></tr>";
    echo "<tr><td colspan='3' align=left><IMG SRC='/MATRIX/images/medical/laboratorio/FirmaLucia.jpg' WIDTH=230 HEIGHT=120></td>"; //trae una imagen de firma digitalizada.
    echo "<tr><td align=left colspan='3' bgcolor=#FFFFFF><font size='1' color='#000000'><b>Para verificar la validez de este certificado,favor comunicarse con la extensión 1677 de nomina.</b></font></td></tr>";
    echo "<tr><td alinn=center colspan='3' bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
    echo "<tr><td align=center colspan='3' bgcolor=#FFFFFF><font size='2' color='#000066'><b>Diagonal 75B No. 2A-80 · PBX(574) 444 90 92, Opc 1, Teléfono 342 10 10 Ext 1123· Fax: (574)340 05 10 - (574)347 24 65</b></font></td></tr>";
    echo "<tr><td align=center colspan='3' bgcolor=#FFFFFF><font size='2' color='#000066'><b>www.lablasamericas.com.co· e-mail: atencionusuarios@lablasamericas.com.co</b></font></td></tr>";
    echo "<tr><td align=center colspan='3' bgcolor=#FFFFFF><font size='2' color='#000066'><b>Medellin - Colombia</font></b></td></tr>";
    echo "</table>"; // cierra la tabla o cuadricula de la impresión   

	odbc_close($conexi);
	odbc_close_all();
	
  } // cierre del else donde empieza la impresión
}

// Se liberan recursos y se cierra la conexión
//odbc_free_result($err1);
//odbc_close($conexi);
?>

