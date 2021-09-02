<html>
<head>
<title>MATRIX - [Formulario de Evaluación Adherencia Aislamiento]</title>

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
	 document.location.href='rep_adhaislamientonew.php?wemp_pmla=<?=$wemp_pmla?>'; 
	}
	
  function enter()
	{
		document.forms.rep_adhaislamientonew.submit();
	}
	
  function cerrarVentana()
	{
	 window.close()
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                           REPORTE FORMULARIO DE ADHERENCIAS AISLAMIENTO NUEVO PROCESOS PRIORITARIOS CLINICA                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver el formulario de adherencia AISLAMIENTO procesos prioritarios de la clinica.               |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : SEPTIEMBRE 9 DE 2015.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : 9 de sseptiembre de 2015.                                                                                   |
//TABLAS UTILIZADAS :                                                                                                                       |
//cominf_000055     : Tabla de adherencias procesos prioritarios AISLAMIENTO de clinica-nuevos.                                             |
//MODIFICACIONES    :                                                                                                                       |
// 2017-07-07       : Pide Carolina de Invecla modificar el formulario,agregando 6 campos nuevos.                                           |
// 2018-02-12       : Pide Carolina de Invecla modificar el formulario,agregando 4 criterios nuevos para un total de 18 campos nuevos.      |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="2021-08-13";

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
$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
encabezado("Formulario de Adherencia Aislamiento",$wactualiz,$institucion->baseDeDatos);

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
  $empre1 = consultarAliasPorAplicacion($conex,$wemp_pmla,"invecla");
//  $empre1='cominf';
 
 //Forma
 echo "<form name='forma' action='rep_adhaislamientonew.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 $hoy=date("Y-m-d");	
  	
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>EVALUACIÓN ADHERENCIA AISLAMIENTO</b></font></td>";
   echo "</tr>";
   echo "</table>";
   
   $cod=explode('-',$user);
   
   $codi=substr($cod[1],0);

   $query = " SELECT ccostos"
           ."   FROM usuarios "
           ."  WHERE codigo= '".$codi."' "; 
           
   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
    //echo $query."<br>";

   $row = mysql_fetch_array($err);
   
   $query1=" SELECT ppaafecha,ppaahistoria,ppaacco,ppaacargevaluada,ppaadetcrite1,ppaacrite1,ppaaconta,ppaaconbata,ppaaconguantes,ppaaaero,Ppaaaemascari,ppaagotas,ppaagobata,ppaagoguantes,ppaagomascari,ppaaaeroconta,ppaaaeconbata,Ppaaaeconguantes,Ppaaaeconmascari,ppaaambpro,ppaaambata,ppaaamguantes,ppaaammascari,ppaavectores,ppaavectoldillo,ppaadetcrite3,ppaacrite3,ppaadetcrite4,ppaacrite4,ppaadetcrite5,ppaacrite5,Ppaadetcrite6,Ppaacrite6bata,Ppaacrite6guantes,Ppaacrite6mascqx,Ppaacrite6mascn95,Ppaacrite6toldi,Ppaacrite6seadecu,Ppaacrite6carne,Ppaadetcrite7,Ppaacrite7bata,Ppaacrite7guantes,Ppaacrite7mascqx,Ppaacrite7mascn97,Ppaacrite7toldi,Ppaadetcrite8,Ppaacrite8,Ppaadetcrite9,Ppaacrite9,ppaatotal,ppaaporce,ppaaevalua,ppaacargoeva,Ppaaobserva,`id` " 
          ."   FROM ".$empre1."_000055" 
          ."  WHERE `Ppaacco` LIKE '$row[0]%'";
          
    //echo $query1."<br>";       
          
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);       
  
     echo "<table border=0>";
     echo "<Tr>";
	 echo "<td>";
     echo "<align=left><INPUT type=button value='NUEVO' onclick='javascript: abrirVentana(\"/matrix/det_registro.php?id=0&pos1=cominf&pos2=0&pos3=0&pos4=000055&pos5=0&pos6=cominf&tipo=P&Valor=&Form=000055-cominf-C-PROCESO PRIORITARIO ADHERENCIA AISLAMIENTO&call=0&change=0&key=cominf&Pagina=1\")'>";
     echo "</td>";
	 
	 echo "</Tr >";
	 echo "</table>";
	 
     echo "<table border=1 cellpadding='1' cellspacing='1' size='1350'>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA EVALUACION</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>HISTORIA_CLINICA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>UNIDAD-CENTRO DE COSTOS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CARGOS PERSONA EVALUADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE AISLAMIENTO?</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE CRITERIO 1</b></td>";    
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO 1</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ES DE CONTACTO?</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>BATA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>GUANTES</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ES DE AEROSOLES?</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA N.95</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ES DE GOTAS?</b></td>"; 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>BATA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>GUANTES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA QUIRURGICA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ES DE AEROSOLES - CONTACTO?</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>BATA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>GUANTES</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA N.95</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ES DE AMBIENTE PROTEGIDO?</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>BATA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>GUANTES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA QUIRURGICA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ES DE VECTORES?</b></td>";	 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>TOLDILLO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO 3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO 4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CRITERIO 5</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 6</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>BATA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>GUANTES</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA QUIRURGICA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA N.95</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>TOLDILLO</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>SEÑALIZACIÓN ADECUADA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CARNÉ</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 7</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>BATA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>GUANTES</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA QUIRURGICA</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>MASCARILLA N.97</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>TOLDILLO</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 8</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>UNIDAD DE ORIGEN INFORMA A UNIDAD RECEPTORA SOBRE TIPO AISLAMIENTO</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>NOMBRE_CRITERIO 9</b></td>"; 
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ALERTAS</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>TOTAL</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PORCENTAJE</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PERSONA QUE REALIZO LA EVALUACIÓN</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CARGO DEL EVALUADOR</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>OBSERVACIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1></td>";
     echo "</tr>";      
       
	   
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);
	  
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
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[28]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[29]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[30]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[31]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[32]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[33]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[34]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[35]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[36]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[37]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[38]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[39]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[40]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[41]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[42]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[43]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[44]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[45]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[46]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[47]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[48]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[49]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[50]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[51]</font></td>";   
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[52]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[53]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[54]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[55]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[56]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[57]</font></td>";
       echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[58]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[59]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[60]</font></td>";
	   echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[61]</font></td>";
	   $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[62]."&pos1=cominf&pos2=0&pos3=0&pos4=000055&pos5=0&pos6=cominf&tipo=P&Valor=&Form=000055-cominf-C-PROCESO PRIORITARIO ADHERENCIA AISLAMIENTO&call=0&change=0&key=cominf&Pagina=1' target='new' >Editar</a>";
	   echo "<td align=left><font size=1>".$hyper."</td>";
       echo "</Tr >";
	  
    } // cierre del for
   echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
}
?>