<html>
<head>
<title>MATRIX - [Formulario Formato de Acciones_13]</title>

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
	 document.location.href='rep_tproceso13.php'; 
	}
	
  function enter()
	{
		document.forms.rep_tproceso13.submit();
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
//PROGRAMA				      : Reporte para ver el formulario de acciones correctivas tabla 13.                                            |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 14 DE 2011.                                                                                          |
//FECHA ULTIMA ACTUALIZACION  : 14 de AGOSTO de 2011.                                                                                       |
//TABLAS UTILIZADAS :                                                                                                                       |
//tabla_000013      : Tabla de acciones correctivas.                                                                                        |
//==========================================================================================================================================
include_once("root/comun.php");
$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 28-Diciembre-2012";

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
 echo "<form name='forma' action='rep_tproceso13.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 

if (!isset($cc) or $cc=='-' )
  {
  	echo "<form name='rep_acciocorrec' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

   /////////////////////////////////////////////////////////////////////////// seleccion para los procesos prioritarios
   echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Centro de Costos:</B><br></font></b><select name='cc' id='searchinput'>";

   $query20 = " SELECT Accconom"
             ."   FROM ".$empre1."_000013"
             ."  GROUP BY 1 "
             ."  ORDER BY 1 ";
           
   $err20 = mysql_query($query20,$conex);
   $num20 = mysql_num_rows($err20);
   $tcc  = $cc;
   
   $codemp = $tcc[0];
   
   if ($codemp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option>".$tcc."</option>";
    } 
   
   for ($i=1;$i<=$num20;$i++)
	{
	$row20 = mysql_fetch_array($err20);
	echo "<option>".$row20[0]."</option>";
	}
   echo "<option>TODOS</option>";	
   echo "</select></td>";
    
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
  ELSE
  {
 
   $tcc=$cc;	
  	
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>FORMATO DE ACCIONES</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='1' text color=#003366><b>Color Azul - Subclasificación=EFICAZ, Color Gris - Subclasificación=NO EFICAZ </b></font></td>";
   echo "</tr>";
   echo "</table>";
   
   IF ($tcc=='TODOS')
   {
   
   $query1=" SELECT `Accacion`,`Accco` , `Acconse` , `Accconom` , `Acfecseg11` , `Acresul11` , `Aceje11` , `Acefe11` , `Acrms11` , `Acpfs11` , `Acfecseg12` , `Acresul12` , `Aceje12` , `Acefe12` , `Acrms12` , `Acpfs12` , `Acfecseg13` , `Acresul13` , `Aceje13` , `Acefe13` , `Acrms13` , `Acpfs13` , `Acfecseg14` , `Acresul14` , `Aceje14` , `Acefe14` , `Acrms14` , `Acpfs14` , `Acfecseg15` , `Acresul15` , `Aceje15` , `Acefe15` , `Acrms15` , `Acpfs15` , `Acfecseg16` , `Acresul16` , `Aceje16` , `Acefe16` , `Acrms16` , `Acpfs16` , `Acfecseg17` , `Acresul17` , `Aceje17` , `Acefe17` , `Acrms17` , `Acpfs17` , `Acfecseg18` , `Acresul18` , `Aceje18` , `Acefe18` , `Acrms18` , `Acpfs18` , `Acfecseg19` , `Acresul19` , `Aceje19` , `Acefe19` , `Acrms19` , `Acpfs19` , `Acfecseg110` , `Acresul110` , `Aceje110` , `Acefe110` , `Acrms110` , `Acpfs110` , `Acclasi1` , `Acsubcla1` , `Acfecseg21` , `Acresul21` , `Aceje21` , `Acefe21` , `Acrms21` , `Acpfs21` , `Acfecseg22` , `Acresul22` , `Aceje22` , `Acefe22` , `Acrms22` , `Acpfs22` , `Acfecseg23` , `Acresul23` , `Aceje23` , `Acefe23` , `Acrms23` , `Acpfs23` , `Acfecseg24` , `Acresul24` , `Aceje24` , `Acefe24` , `Acrms24` , `Acpfs24` , `Acfecseg25` , `Acresul25` , `Aceje25` , `Acefe25` , `Acrms25` , `Acpfs25` , `Acfecseg26` , `Acresul26` , `Aceje26` , `Acefe26` , `Acrms26` , `Acpfs26` , `Acfecseg27` , `Acresul27` , `Aceje27` , `Acefe27` , `Acrms27` , `Acpfs27` , `Acfecseg28` , `Acresul28` , `Aceje28` , `Acefe28` , `Acrms28` , `Acpfs28` , `Acfecseg29` , `Acresul29` , `Aceje29` , `Acefe29` , `Acrms29` , `Acpfs29` , `Acfecseg210` , `Acresul210` , `Aceje210` , `Acefe210` , `Acrms210` , `Acpfs210` , `Acclasi2` , `Acsubcla2` , `Acfecseg31` , `Acresul31` , `Aceje31` , `Acefe31` , `Acrms31` , `Acpfs31` , `Acfecseg32` , `Acresul32` , `Aceje32` , `Acefe32` , `Acrms32` , `Acpfs32` , `Acfecseg33` , `Acresul33` , `Aceje33` , `Acefe33` , `Acrms33` , `Acpfs33` , `Acfecseg34` , `Acresul34` , `Aceje34` , `Acefe34` , `Acrms34` , `Acpfs34` , `Acfecseg35` , `Acresul35` , `Aceje35` , `Acefe35` , `Acrms35` , `Acpfs35` , `Acfecseg36` , `Acresul36` , `Aceje36` , `Acefe36` , `Acrms36` , `Acpfs36` , `Acfecseg37` , `Acresul37` , `Aceje37` , `Acefe37` , `Acrms37` , `Acpfs37` , `Acfecseg38` , `Acresul38` , `Aceje38` , `Acefe38` , `Acrms38` , `Acpfs38` , `Acfecseg39` , `Acresul39` , `Aceje39` , `Acefe39` , `Acrms39` , `Acpfs39` , `Acfecseg310` , `Acresul310` , `Aceje310` , `Acefe310` , `Acrms310` , `Acpfs310` , `Acclasi3` , `Acsubcla3` , `Acfecseg41` , `Acresul41` , `Aceje41` , `Acefe41` , `Acrms41` , `Acpfs41` , `Acfecseg42` , `Acresul42` , `Aceje42` , `Acefe42` , `Acrms42` , `Acpfs42` , `Acfecseg43` , `Acresul43` , `Aceje43` , `Acefe43` , `Acrms43` , `Acpfs43` , `Acfecseg44` , `Acresul44` , `Aceje44` , `Acefe44` , `Acrms44` , `Acpfs44` , `Acfecseg45` , `Acresul45` , `Aceje45` , `Acefe45` , `Acrms45` , `Acpfs45` , `Acfecseg46` , `Acresul46` , `Aceje46` , `Acefe46` , `Acrms46` , `Acpfs46` , `Acfecseg47` , `Acresul47` , `Aceje47` , `Acefe47` , `Acrms47` , `Acpfs47` , `Acfecseg48` , `Acresul48` , `Aceje48` , `Acefe48` , `Acrms48` , `Acpfs48` , `Acfecseg49` , `Acresul49` , `Aceje49` , `Acefe49` , `Acrms49` , `Acpfs49` , `Acfecseg410` , `Acresul410` , `Aceje410` , `Acefe410` , `Acrms410` , `Acpfs410` , `Acclasi4` , `Acsubcla4` , `Acfecseg51` , `Acresul51` , `Aceje51` , `Acefe51` , `Acrms51` , `Acpfs51` , `Acfecseg52` , `Acresul52` , `Aceje52` , `Acefe52` , `Acrms52` , `Acpfs52` , `Acfecseg53` , `Acresul53` , `Aceje53` , `Acefe53` , `Acrms53` , `Acpfs53` , `Acfecseg54` , `Acresul54` , `Aceje54` , `Acefe54` , `Acrms54` , `Acpfs54` , `Acfecseg55` , `Acresul55` , `Aceje55` , `Acefe55` , `Acrms55` , `Acpfs55` , `Acfecseg56` , `Acresul56` , `Aceje56` , `Acefe56` , `Acrms56` , `Acpfs56` , `Acfecseg57` , `Acresul57` , `Aceje57` , `Acefe57` , `Acrms57` , `Acpfs57` , `Acfecseg58` , `Acresul58` , `Aceje58` , `Acefe58` , `Acrms58` , `Acpfs58` , `Acfecseg59` , `Acresul59` , `Aceje59` , `Acefe59` , `Acrms59` , `Acpfs59` , `Acfecseg510` , `Acresul510` , `Aceje510` , `Acefe510` , `Acrms510` , `Acpfs510` , `Acclasi5` , `Acsubcla5` , `Acperseg` , `id`"
           ."  FROM proceso_000013 "  
           ." ORDER BY 2,4";
          
    //echo $query1."<br>";       
          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);       
   }
   ELSE
   {
   	$query1=" SELECT `Accacion`,`Accco` , `Acconse` , `Accconom` , `Acfecseg11` , `Acresul11` , `Aceje11` , `Acefe11` , `Acrms11` , `Acpfs11` , `Acfecseg12` , `Acresul12` , `Aceje12` , `Acefe12` , `Acrms12` , `Acpfs12` , `Acfecseg13` , `Acresul13` , `Aceje13` , `Acefe13` , `Acrms13` , `Acpfs13` , `Acfecseg14` , `Acresul14` , `Aceje14` , `Acefe14` , `Acrms14` , `Acpfs14` , `Acfecseg15` , `Acresul15` , `Aceje15` , `Acefe15` , `Acrms15` , `Acpfs15` , `Acfecseg16` , `Acresul16` , `Aceje16` , `Acefe16` , `Acrms16` , `Acpfs16` , `Acfecseg17` , `Acresul17` , `Aceje17` , `Acefe17` , `Acrms17` , `Acpfs17` , `Acfecseg18` , `Acresul18` , `Aceje18` , `Acefe18` , `Acrms18` , `Acpfs18` , `Acfecseg19` , `Acresul19` , `Aceje19` , `Acefe19` , `Acrms19` , `Acpfs19` , `Acfecseg110` , `Acresul110` , `Aceje110` , `Acefe110` , `Acrms110` , `Acpfs110` , `Acclasi1` , `Acsubcla1` , `Acfecseg21` , `Acresul21` , `Aceje21` , `Acefe21` , `Acrms21` , `Acpfs21` , `Acfecseg22` , `Acresul22` , `Aceje22` , `Acefe22` , `Acrms22` , `Acpfs22` , `Acfecseg23` , `Acresul23` , `Aceje23` , `Acefe23` , `Acrms23` , `Acpfs23` , `Acfecseg24` , `Acresul24` , `Aceje24` , `Acefe24` , `Acrms24` , `Acpfs24` , `Acfecseg25` , `Acresul25` , `Aceje25` , `Acefe25` , `Acrms25` , `Acpfs25` , `Acfecseg26` , `Acresul26` , `Aceje26` , `Acefe26` , `Acrms26` , `Acpfs26` , `Acfecseg27` , `Acresul27` , `Aceje27` , `Acefe27` , `Acrms27` , `Acpfs27` , `Acfecseg28` , `Acresul28` , `Aceje28` , `Acefe28` , `Acrms28` , `Acpfs28` , `Acfecseg29` , `Acresul29` , `Aceje29` , `Acefe29` , `Acrms29` , `Acpfs29` , `Acfecseg210` , `Acresul210` , `Aceje210` , `Acefe210` , `Acrms210` , `Acpfs210` , `Acclasi2` , `Acsubcla2` , `Acfecseg31` , `Acresul31` , `Aceje31` , `Acefe31` , `Acrms31` , `Acpfs31` , `Acfecseg32` , `Acresul32` , `Aceje32` , `Acefe32` , `Acrms32` , `Acpfs32` , `Acfecseg33` , `Acresul33` , `Aceje33` , `Acefe33` , `Acrms33` , `Acpfs33` , `Acfecseg34` , `Acresul34` , `Aceje34` , `Acefe34` , `Acrms34` , `Acpfs34` , `Acfecseg35` , `Acresul35` , `Aceje35` , `Acefe35` , `Acrms35` , `Acpfs35` , `Acfecseg36` , `Acresul36` , `Aceje36` , `Acefe36` , `Acrms36` , `Acpfs36` , `Acfecseg37` , `Acresul37` , `Aceje37` , `Acefe37` , `Acrms37` , `Acpfs37` , `Acfecseg38` , `Acresul38` , `Aceje38` , `Acefe38` , `Acrms38` , `Acpfs38` , `Acfecseg39` , `Acresul39` , `Aceje39` , `Acefe39` , `Acrms39` , `Acpfs39` , `Acfecseg310` , `Acresul310` , `Aceje310` , `Acefe310` , `Acrms310` , `Acpfs310` , `Acclasi3` , `Acsubcla3` , `Acfecseg41` , `Acresul41` , `Aceje41` , `Acefe41` , `Acrms41` , `Acpfs41` , `Acfecseg42` , `Acresul42` , `Aceje42` , `Acefe42` , `Acrms42` , `Acpfs42` , `Acfecseg43` , `Acresul43` , `Aceje43` , `Acefe43` , `Acrms43` , `Acpfs43` , `Acfecseg44` , `Acresul44` , `Aceje44` , `Acefe44` , `Acrms44` , `Acpfs44` , `Acfecseg45` , `Acresul45` , `Aceje45` , `Acefe45` , `Acrms45` , `Acpfs45` , `Acfecseg46` , `Acresul46` , `Aceje46` , `Acefe46` , `Acrms46` , `Acpfs46` , `Acfecseg47` , `Acresul47` , `Aceje47` , `Acefe47` , `Acrms47` , `Acpfs47` , `Acfecseg48` , `Acresul48` , `Aceje48` , `Acefe48` , `Acrms48` , `Acpfs48` , `Acfecseg49` , `Acresul49` , `Aceje49` , `Acefe49` , `Acrms49` , `Acpfs49` , `Acfecseg410` , `Acresul410` , `Aceje410` , `Acefe410` , `Acrms410` , `Acpfs410` , `Acclasi4` , `Acsubcla4` , `Acfecseg51` , `Acresul51` , `Aceje51` , `Acefe51` , `Acrms51` , `Acpfs51` , `Acfecseg52` , `Acresul52` , `Aceje52` , `Acefe52` , `Acrms52` , `Acpfs52` , `Acfecseg53` , `Acresul53` , `Aceje53` , `Acefe53` , `Acrms53` , `Acpfs53` , `Acfecseg54` , `Acresul54` , `Aceje54` , `Acefe54` , `Acrms54` , `Acpfs54` , `Acfecseg55` , `Acresul55` , `Aceje55` , `Acefe55` , `Acrms55` , `Acpfs55` , `Acfecseg56` , `Acresul56` , `Aceje56` , `Acefe56` , `Acrms56` , `Acpfs56` , `Acfecseg57` , `Acresul57` , `Aceje57` , `Acefe57` , `Acrms57` , `Acpfs57` , `Acfecseg58` , `Acresul58` , `Aceje58` , `Acefe58` , `Acrms58` , `Acpfs58` , `Acfecseg59` , `Acresul59` , `Aceje59` , `Acefe59` , `Acrms59` , `Acpfs59` , `Acfecseg510` , `Acresul510` , `Aceje510` , `Acefe510` , `Acrms510` , `Acpfs510` , `Acclasi5` , `Acsubcla5` , `Acperseg` , `id`"
           ."   FROM proceso_000013 "
           ."  WHERE Accconom = '".$tcc."'"  
           ."  ORDER BY 1,3";
          
    //echo $query1."<br>";       
          
    $err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);     
   }
    
     echo "<table border=0>";
     echo "<Tr>";

     echo "<td>";
     echo "<align=left><INPUT type=button value='NUEVO' onclick='javascript: abrirVentana(\"/matrix/det_registro.php?id=0&pos1=proceso&pos2=0&pos3=0&pos4=000013&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000013-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1\")'>";
     echo "</td>";
	 
	 echo "</Tr >";
	 echo "</table>";
	 
     echo "<table border=1 cellpadding='1' cellspacing='1' size='1350'>";
     echo "<tr>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>ACCION</b></td>";
	 echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CENTRO DE COSTOS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CONSECUTIVO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CODIGO Y NOMBRE CC</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>"; 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CLASIFICACION_1</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>SUBCLASIFICACION_1</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>"; 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CLASIFICACION_2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>SUBCLASIFICACION_2</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>"; 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CLASIFICACION_3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>SUBCLASIFICACION_3</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>"; 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CLASIFICACION_4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>SUBCLASIFICACION_4</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>"; 
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>RESULTADOS DE LAS ACCIONES</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EJECUTADAS</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>EFECTIVA</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>REQUIERE MAYOR SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PROXIMA FECHA DE SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>CLASIFICACION_5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>SUBCLASIFICACION_5</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1><b>PERSONA QUE REALIZA EL SEGUIMIENTO</b></td>";
     echo "<td bgcolor='#FFFFFF'align=center width=50><font text color=#003366 size=1></td>";
     echo "</tr>";      
          
	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);
	  
	  IF ( ($row2[64]=='2-EFICAZ') or ($row2[126]=='2-EFICAZ') or ($row2[188]=='2-EFICAZ') or ($row2[250]=='2-EFICAZ') or ($row2[312]=='2-EFICAZ') )
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
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[27]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[28]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[29]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[30]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[31]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[32]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[33]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[34]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[35]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[36]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[37]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[38]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[39]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[40]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[41]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[42]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[43]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[44]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[45]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[46]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[47]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[48]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[49]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[50]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[51]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[52]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[53]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[54]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[55]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[56]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[57]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[58]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[59]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[60]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[61]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[62]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[63]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[64]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1><b>$row2[65]</b></font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[66]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[67]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[68]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[69]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[70]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[71]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[72]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[73]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[74]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[75]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[76]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[77]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[78]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[79]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[80]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[81]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[82]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[83]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[84]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[85]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[86]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[87]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[88]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[89]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[90]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[91]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[92]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[93]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[94]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[95]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[96]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[97]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[98]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[99]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[100]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[101]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[102]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[103]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[104]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[105]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[106]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[107]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[108]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[109]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[110]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[111]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[112]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[113]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[114]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[115]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[116]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[117]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[118]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[119]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[120]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[121]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[122]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[123]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[124]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[125]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[126]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1><b>$row2[127]</b></font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[128]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[129]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[130]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[131]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[132]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[133]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[134]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[135]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[136]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[137]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[138]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[139]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[140]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[141]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[142]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[143]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[144]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[145]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[146]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[147]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[148]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[149]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[150]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[151]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[152]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[153]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[154]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[155]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[156]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[157]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[158]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[159]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[160]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[161]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[162]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[163]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[164]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[165]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[166]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[167]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[168]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[169]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[170]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[171]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[172]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[173]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[174]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[175]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[176]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[177]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[178]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[179]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[180]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[181]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[182]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[183]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[184]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[185]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[186]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[187]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[188]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1><b>$row2[189]</b></font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[190]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[191]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[192]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[193]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[194]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[195]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[196]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[197]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[198]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[199]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[200]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[201]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[202]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[203]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[204]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[205]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[206]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[207]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[208]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[209]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[210]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[211]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[212]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[213]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[214]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[215]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[216]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[217]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[218]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[219]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[220]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[221]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[222]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[223]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[224]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[225]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[226]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[227]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[228]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[229]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[230]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[231]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[232]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[233]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[234]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[235]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[236]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[237]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[238]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[239]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[240]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[241]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[242]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[243]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[244]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[245]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[246]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[247]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[248]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[249]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[250]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1><b>$row2[251]<b></font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[252]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[253]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[254]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[255]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[256]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[257]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[258]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[259]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[260]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[261]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[262]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[263]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[264]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[265]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[266]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[267]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[268]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[269]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[270]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[271]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[272]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[273]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[274]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[275]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[276]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[277]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[278]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[279]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[280]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[281]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[282]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[283]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[284]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[285]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[286]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[287]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[288]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[289]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[290]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[291]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[292]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[293]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[294]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[295]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[296]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[297]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[298]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[299]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[300]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[301]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[302]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[303]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[304]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[305]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[306]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[307]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[308]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[309]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[310]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[311]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[312]</font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1><b>$row2[313]<b></font></td>";
      echo "<td  bgcolor=#CCFFFF align=center><font size=1>$row2[314]</font></td>";
	 
      $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[315]."&pos1=proceso&pos2=0&pos3=0&pos4=000013&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000013-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1' target='new' >Editar</a>";
	  echo "<td align=left><font size=1>".$hyper."</td>";
      echo "</Tr >";
	  }
    ELSE
	 {
	  IF ( ($row2[65]=='3-NO EFICAZ') or ($row2[127]=='3-NO EFICAZ') or ($row2[189]=='3-NO EFICAZ') or ($row2[251]=='3-NO EFICAZ') or ($row2[313]=='3-NO EFICAZ') )
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
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[27]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[28]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[29]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[30]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[31]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[32]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[33]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[34]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[35]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[36]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[37]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[38]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[39]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[40]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[41]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[42]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[43]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[44]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[45]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[46]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[47]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[48]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[49]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[50]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[51]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[52]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[53]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[54]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[55]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[56]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[57]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[58]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[59]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[60]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[61]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[62]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[63]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[64]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1><b>$row2[65]</b></font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[66]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[67]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[68]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[69]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[70]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[71]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[72]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[73]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[74]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[75]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[76]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[77]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[78]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[79]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[80]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[81]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[82]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[83]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[84]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[85]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[86]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[87]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[88]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[89]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[90]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[91]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[92]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[93]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[94]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[95]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[96]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[97]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[98]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[99]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[100]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[101]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[102]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[103]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[104]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[105]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[106]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[107]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[108]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[109]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[110]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[111]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[112]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[113]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[114]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[115]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[116]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[117]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[118]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[119]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[120]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[121]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[122]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[123]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[124]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[125]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[126]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1><b>$row2[127]</b></font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[128]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[129]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[130]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[131]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[132]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[133]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[134]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[135]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[136]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[137]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[138]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[139]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[140]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[141]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[142]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[143]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[144]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[145]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[146]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[147]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[148]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[149]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[150]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[151]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[152]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[153]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[154]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[155]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[156]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[157]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[158]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[159]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[160]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[161]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[162]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[163]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[164]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[165]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[166]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[167]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[168]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[169]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[170]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[171]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[172]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[173]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[174]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[175]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[176]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[177]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[178]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[179]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[180]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[181]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[182]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[183]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[184]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[185]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[186]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[187]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[188]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1><b>$row2[189]</b></font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[190]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[191]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[192]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[193]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[194]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[195]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[196]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[197]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[198]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[199]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[200]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[201]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[202]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[203]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[204]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[205]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[206]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[207]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[208]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[209]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[210]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[211]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[212]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[213]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[214]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[215]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[216]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[217]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[218]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[219]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[220]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[221]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[222]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[223]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[224]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[225]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[226]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[227]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[228]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[229]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[230]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[231]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[232]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[233]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[234]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[235]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[236]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[237]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[238]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[239]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[240]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[241]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[242]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[243]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[244]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[245]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[246]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[247]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[248]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[249]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[250]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1><b>$row2[251]</b></font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[252]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[253]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[254]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[255]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[256]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[257]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[258]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[259]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[260]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[261]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[262]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[263]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[264]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[265]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[266]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[267]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[268]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[269]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[270]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[271]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[272]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[273]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[274]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[275]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[276]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[277]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[278]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[279]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[280]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[281]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[282]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[283]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[284]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[285]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[286]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[287]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[288]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[289]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[290]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[291]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[292]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[293]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[294]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[295]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[296]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[297]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[298]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[299]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[300]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[301]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[302]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[303]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[304]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[305]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[306]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[307]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[308]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[309]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[310]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[311]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[312]</font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1><b>$row2[313]</b></font></td>";
      echo "<td  bgcolor=#DDDDDD align=center><font size=1>$row2[314]</font></td>";
	 
      $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[315]."&pos1=proceso&pos2=0&pos3=0&pos4=000013&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000013-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1' target='new' >Editar</a>";
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
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[62]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[63]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[64]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[65]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[66]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[67]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[68]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[69]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[70]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[71]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[72]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[73]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[74]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[75]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[76]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[77]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[78]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[79]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[80]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[81]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[82]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[83]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[84]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[85]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[86]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[87]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[88]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[89]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[90]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[91]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[92]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[93]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[94]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[95]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[96]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[97]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[98]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[99]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[100]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[101]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[102]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[103]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[104]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[105]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[106]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[107]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[108]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[109]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[110]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[111]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[112]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[113]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[114]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[115]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[116]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[117]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[118]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[119]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[120]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[121]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[122]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[123]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[124]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[125]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[126]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[127]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[128]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[129]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[130]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[131]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[132]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[133]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[134]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[135]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[136]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[137]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[138]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[139]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[140]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[141]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[142]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[143]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[144]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[145]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[146]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[147]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[148]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[149]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[150]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[151]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[152]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[153]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[154]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[155]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[156]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[157]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[158]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[159]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[160]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[161]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[162]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[163]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[164]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[165]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[166]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[167]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[168]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[169]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[170]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[171]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[172]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[173]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[174]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[175]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[176]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[177]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[178]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[179]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[180]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[181]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[182]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[183]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[184]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[185]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[186]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[187]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[188]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[189]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[190]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[191]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[192]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[193]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[194]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[195]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[196]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[197]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[198]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[199]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[200]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[201]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[202]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[203]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[204]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[205]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[206]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[207]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[208]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[209]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[210]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[211]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[212]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[213]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[214]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[215]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[216]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[217]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[218]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[219]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[220]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[221]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[222]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[223]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[224]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[225]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[226]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[227]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[228]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[229]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[230]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[231]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[232]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[233]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[234]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[235]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[236]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[237]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[238]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[239]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[240]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[241]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[242]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[243]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[244]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[245]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[246]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[247]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[248]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[249]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[250]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[251]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[252]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[253]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[254]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[255]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[256]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[257]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[258]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[259]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[260]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[261]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[262]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[263]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[264]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[265]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[266]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[267]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[268]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[269]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[270]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[271]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[272]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[273]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[274]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[275]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[276]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[277]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[278]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[279]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[280]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[281]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[282]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[283]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[284]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[285]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[286]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[287]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[288]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[289]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[290]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[291]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[292]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[293]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[294]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[295]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[296]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[297]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[298]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[299]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[300]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[301]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[302]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[303]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[304]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[305]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[306]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[307]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[308]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[309]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[310]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[311]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[312]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[313]</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row2[314]</font></td>";
	 
      $hyper="<A HREF='/matrix/det_registro.php?id=".$row2[315]."&pos1=proceso&pos2=0&pos3=0&pos4=000013&pos5=0&pos6=proceso&tipo=P&Valor=&Form=000013-proceso-formato acciones correctivas&call=1&change=0&key=proceso&Pagina=1' target='new' >Editar</a>";
	  echo "<td align=left><font size=1>".$hyper."</td>";
      echo "</Tr >";
	  }
	 }	
      
  } // cierre del for
  echo "</table>";
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>