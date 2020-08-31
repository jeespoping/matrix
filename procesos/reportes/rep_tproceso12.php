<html>
<head>
<title>MATRIX - [Formulario Formato de Acciones_12]</title>

<script language="JavaScript"> 

  function abrirVentana( url )
  {
   var ancho=screen.width;
   var alto=screen.availHeight;
   var v = window.open( url, '', 'scrollbars=1, width='+ancho+', height='+alto );
	   v.moveTo(0,0);
		
  }

  function inicio()
	{ 
	 document.location.href='rep_tproceso12.php'; 
	}
	
  function enter()
	{
		document.forms.rep_tproceso12.submit();
	}
	
  function VolverAtras()
	{
	 history.back(1)
	}
	
  function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE FORMULARIO DE ACCIONES CORRECTIVAS                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver el formulario de acciones correctivas tabla 12.                                             |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  : MARZO 23 DE 2011.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : 19 de Abril de 2011.                                                                                         |
//Modificacion                : Se solicita que el usuario 0102899 pueda trabajar con 2 CC 1080 y 1081                                      | 
//TABLAS UTILIZADAS :                                                                                                                       |
//tabla_000012      : Tabla de acciones correctivas.                                                                                        |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 03-Noviembre-2011";

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
encabezado("Formularios de Acciones",$wactualiz,"Clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 $empre1='proceso';
 
 //Forma
 echo "<form name='forma' action='rep_tproceso12.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 $hoy=date("Y-m-d");	
  	
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>FORMATO DE ACCIONES CORRECTIVAS</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='1' text color=#003366><b>Color Azul - Subclasificación=EFICAZ, Color Gris - Subclasificación=NO EFICAZ </b></font></td>";
   echo "</tr>";
   echo "</table>";
   
   $cod=explode('-',$user);
   
   $codi=substr($cod[1],0);

   IF ($codi=='0102899')
   {
   	$query = " SELECT ccostos"
            ."   FROM usuarios "
            ."  WHERE codigo= '".$codi."' "; 
           
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
    //echo $query."<br>";

    $row = mysql_fetch_array($err);
    
   	$query1=" SELECT `Accacion` ,`Accco` ,`Accconom` ,`Acconse`,`Acncac`,`Acres`,`Acftesele`,`Acotrfte`,`Ac1p`,`Ac2p`,`Ac3p`,`Ac4p`,`Ac5p`,`Acacc1`,`Acres1`,`Acfec1`,`Acacc2`,`Acres2`,`Acfec2`,`Acacc3`,`Acres3`,`Acfec3`,`Acacc4`,`Acres4`,`Acfec4`,`Acacc5`,`Acres5`,`Acfec5`,`id` " 
           ."   FROM `proceso_000012`"  
           ."  WHERE `Accco` LIKE '1080'"
           ."  UNION ALL "
           ." SELECT `Accacion` ,`Accco` ,`Accconom` ,`Acconse`,`Acncac`,`Acres`,`Acftesele`,`Acotrfte`,`Ac1p`,`Ac2p`,`Ac3p`,`Ac4p`,`Ac5p`,`Acacc1`,`Acres1`,`Acfec1`,`Acacc2`,`Acres2`,`Acfec2`,`Acacc3`,`Acres3`,`Acfec3`,`Acacc4`,`Acres4`,`Acfec4`,`Acacc5`,`Acres5`,`Acfec5`,`id` " 
           ."   FROM `proceso_000012`"  
           ."  WHERE `Accco` LIKE '1081'"
           ."  ORDER BY accco,acconse ";
          
    //echo $query1."<br>";       
          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);       
   	
   }
   ELSE
   {
    $query = " SELECT ccostos"
            ."   FROM usuarios "
            ."  WHERE codigo= '".$codi."' "; 
           
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
    //echo $query."<br>";

    $row = mysql_fetch_array($err);
   
    $query1=" SELECT `Accacion` ,`Accco` ,`Accconom` ,`Acconse`,`Acncac`,`Acres`,`Acftesele`,`Acotrfte`,`Ac1p`,`Ac2p`,`Ac3p`,`Ac4p`,`Ac5p`,`Acacc1`,`Acres1`,`Acfec1`,`Acacc2`,`Acres2`,`Acfec2`,`Acacc3`,`Acres3`,`Acfec3`,`Acacc4`,`Acres4`,`Acfec4`,`Acacc5`,`Acres5`,`Acfec5`,`id` " 
           ."   FROM ".$empre1."_000012" 
           ."  WHERE `Accco` LIKE '$row[0]'";
          
    //echo $query1."<br>";       
          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);       
   }
    
     echo "<table border=0>";
     echo "<Tr>";
	 echo "<td>";
     echo "<align=left><INPUT type=button value='NUEVO' onclick='javascript: abrirVentana(\"/matrix/det_registro.php?id=0&pos1=proceso&pos2=0&pos3=0&pos4=000012&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000012-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1&r[1]=".$row[1]."\")'>";
     echo "</td>";
	 
	 echo "</Tr >";
	 echo "</table>";
	 
     echo "<table border=1 cellpadding='1' cellspacing='1' size='1350'>";
     echo "<tr>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CENTRO DE COSTOS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CODIGO Y NOMBRE CC</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CONSECUTIVO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NO CONFORMIDAD A CORREGIR</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESPONSABLE DE LA ACCIÓN</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FUENTE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>OTRA FUENTE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>1 PORQUE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>2 PORQUE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>3 PORQUE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>4 PORQUE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>5 PORQUE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCIÓN 1</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESPONSABLE ACCION 1</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA ACCION 1</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCIÓN 2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESPONSABLE ACCION 2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA ACCION 2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCIÓN 3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESPONSABLE ACCION 3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA ACCION 3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCIÓN 4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESPONSABLE ACCION 4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA ACCION 4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCIÓN 5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESPONSABLE ACCION 5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA ACCION 5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1></td>";
     echo "</tr>";      
          
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);
	  
	  $query2 = "CREATE TEMPORARY TABLE if not exists tmp21 as "
	           ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla1 = '2-EFICAZ'"
               ."  UNION  "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla2 = '2-EFICAZ'"
               ."  UNION  "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla3 = '2-EFICAZ'"
               ."  UNION  "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla4 = '2-EFICAZ'"
               ."  UNION  "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla5 = '2-EFICAZ'";

      //echo $query2."<br>";           
           
      $err2 = mysql_query($query2,$conex);
      
      $query21 =" SELECT sum(can)"
               ."   FROM tmp21";
      
      $err21 = mysql_query($query21,$conex);
      
      $num2 = mysql_num_rows($err21);
      
      $row21 = mysql_fetch_array($err21);
      
      $q = "drop table tmp21";
      
      $errq = mysql_query($q,$conex);
      
      
      $query3 = "CREATE TEMPORARY TABLE if not exists tmp3 as "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla1 = '3-NO EFICAZ'"
               ."  UNION ALL "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla2 = '3-NO EFICAZ'"
               ."  UNION ALL "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla3 = '3-NO EFICAZ'"
               ."  UNION ALL "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla4 = '3-NO EFICAZ'"
               ."  UNION ALL "
               ." SELECT count(*) as can"
               ."   FROM ".$empre1."_000013" 
               ."  WHERE accco   = '".$row2[1]."' "
               ."    AND Acconse = '".$row2[3]."' "
               ."    AND acsubcla5 = '3-NO EFICAZ'";
                
           
      $err3 = mysql_query($query3,$conex);
      
      $query31 =" SELECT sum(can)"
               ."   FROM tmp3";
      
      $err31 = mysql_query($query31,$conex);
      
      $num3 = mysql_num_rows($err31);
      
      $row31 = mysql_fetch_array($err31);
      
      $q3 = "drop table tmp3";
      
      $errq = mysql_query($q3,$conex);
      
      //echo "row21:",$row21[0];
      
      IF ($row21[0]>0)
      {
       echo "<Tr >";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[0]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[1]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[2]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[3]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[4]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[5]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[6]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[7]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[8]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[9]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[10]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[11]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[12]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[13]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[14]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[15]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[16]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[17]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[18]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[19]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[20]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[21]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[22]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[23]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[24]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[25]</font></td>";
       echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[26]</font></td>";
       $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[27]."&pos1=proceso&pos2=0&pos3=0&pos4=000012&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000012-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1' target='new' >Editar</a>";
	   echo "<td align=left><font size=1>".$hyper."</td>";
      
       echo "</Tr >";	
      }
      ELSE
      {
      IF ($row31[0]>0)
      {
       echo "<Tr >";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[0]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[1]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[2]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[3]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[4]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[5]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[6]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[7]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[8]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[9]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[10]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[11]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[12]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[13]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[14]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[15]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[16]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[17]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[18]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[19]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[20]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[21]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[22]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[23]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[24]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[25]</font></td>";
       echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[26]</font></td>";
       $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[27]."&pos1=proceso&pos2=0&pos3=0&pos4=000012&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000012-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1' target='new' >Editar</a>";
	   echo "<td align=left><font size=1>".$hyper."</td>";
      
       echo "</Tr >";			
      }
      ELSE
      {
 	   echo "<Tr >";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[0]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[1]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[2]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[3]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[4]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[5]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[6]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[7]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[8]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[9]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[10]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[11]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[12]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[13]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[14]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[15]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[16]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[17]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[18]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[19]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[20]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[21]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[22]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[23]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[24]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[25]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[26]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[27]</font></td>";
       $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[28]."&pos1=proceso&pos2=0&pos3=0&pos4=000012&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000012-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1' target='new' >Editar</a>";
	   echo "<td align=left><font size=1>".$hyper."</td>";
      
       echo "</Tr >";
      }
     } 
      
  } // cierre del for
  echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  

}

?>