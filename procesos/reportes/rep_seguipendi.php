<html>
<head>
<title>MATRIX - [REPORTE SEGUIMIENTOS PENDIENTES]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_seguipendi.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
	}
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE SEGUIMIENTO A PACIENTES                                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte de seguimientos pendientes desarrollo organizacional.                                               |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : AGOSTO 1 DE 2011.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : AGOSTO 1 DE 2011.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para observar por unidad cuantos seguimientos pendientes.                                |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//proceso_000012      : Tabla de Acciones correctivas de los coordinadores.                                                                 |                                                                                                                                 
//proceso_000013      : Tabla de Acciones correctivas de Desarrollo.                                                                        |
//proceso_000015      : Tabla de Acciones correctivas del ccosto 1013.                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 02-Agosto-2011";

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
encabezado("Seguimientos Pendientes",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_seguipendi.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_seguipendi' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";
 	
    echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
    echo "</table>";
    echo '</div>';
    echo '</div>';
    echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
   
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>SEGUIMIENTOS PENDIENTES</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
  
  $quer20="DROP TABLE if exists tmp ";
  $err20 = mysql_query($quer20,$conex) or die("Imposible : ".mysql_error()); 
    
  $quer2 = "CREATE TABLE if not exists tmp as "
         ." SELECT accconom as nomcco,Accco as cco"
         ."   FROM ".$empre1."_000013"
         ."  GROUP BY 1,2"
         ."  ORDER BY 1";
    
   //echo $quer2."<br>";    
         
  $err2 = mysql_query($quer2,$conex) or die("Imposible : ".mysql_error());
    
  
  $quer211="DROP TABLE if exists tmp21 ";
  $err211 = mysql_query($quer211,$conex) or die("Imposible : ".mysql_error()); 
  
  
  $quer21 = "CREATE TABLE if not exists tmp21 as "
         ." SELECT accconom as nomcco,Accco as cco"
         ."   FROM ".$empre1."_000012"
         ."  GROUP BY 1,2"
         ."  ORDER BY 1";
    
   //echo $quer2."<br>";    
         
  $err21 = mysql_query($quer21,$conex) or die("Imposible : ".mysql_error());
  
  //Query para traer numero de pendientes por centro de costo de la accion nro 1
  
  $quer3 ="CREATE TEMPORARY TABLE if not exists tmp1 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul11) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms11='01-SI'"
        ."    AND acpfs11 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul12) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms12='01-SI'"
        ."    AND acpfs12 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul13) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms13='01-SI'"
        ."    AND acpfs13 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul14) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms14='01-SI'"
        ."    AND acpfs14 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul15) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms15='01-SI'"
        ."    AND acpfs15 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul16) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms16='01-SI'"
        ."    AND acpfs16 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul17) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms17='01-SI'"
        ."    AND acpfs17 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul18) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms18='01-SI'"
        ."    AND acpfs18 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul19) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms19='01-SI'"
        ."    AND acpfs19 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul110) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms110='01-SI'"
        ."    AND acpfs110 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul11) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms11='01-SI'"
        ."    AND acpfs11 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul12) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms12='01-SI'"
        ."    AND acpfs12 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul13) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms13='01-SI'"
        ."    AND acpfs13 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul14) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms14='01-SI'"
        ."    AND acpfs14 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul15) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms15='01-SI'"
        ."    AND acpfs15 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul16) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms16='01-SI'"
        ."    AND acpfs16 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul17) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms17='01-SI'"
        ."    AND acpfs17 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul18) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms18='01-SI'"
        ."    AND acpfs18 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul19) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms19='01-SI'"
        ."    AND acpfs19 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul110) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms110='01-SI'"
        ."    AND acpfs110 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";
        
     //echo $quer3."<br>";        
        
  $err3 = mysql_query($quer3,$conex) or die("Imposible : ".mysql_error()); 
    
  $quer4 ="SELECT nomcco,acconse,sum(cant)"
         ."  FROM tmp1"
         ." GROUP BY 1,2"
         ." ORDER BY 1,2";
         
   //echo $quer4."<br>";  
         
  $err4 = mysql_query($quer4,$conex) or die("Imposible : ".mysql_error());

  
  //Query para traer numero de pendientes por centro de costo de la accion nro 2
  $quer5 ="CREATE TEMPORARY TABLE if not exists tmp2 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul21) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms21='01-SI'"
        ."    AND acpfs21 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul22) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms22='01-SI'"
        ."    AND acpfs22 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul23) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms23='01-SI'"
        ."    AND acpfs23 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul24) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms24='01-SI'"
        ."    AND acpfs24 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul25) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms25='01-SI'"
        ."    AND acpfs25 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul26) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms26='01-SI'"
        ."    AND acpfs26 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul27) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms27='01-SI'"
        ."    AND acpfs27 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul28) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms28='01-SI'"
        ."    AND acpfs28 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul29) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms29='01-SI'"
        ."    AND acpfs29 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul210) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms210='01-SI'"
        ."    AND acpfs210 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul21) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms21='01-SI'"
        ."    AND acpfs21 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul22) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms22='01-SI'"
        ."    AND acpfs22 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul23) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms23='01-SI'"
        ."    AND acpfs23 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul24) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms24='01-SI'"
        ."    AND acpfs24 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul25) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms25='01-SI'"
        ."    AND acpfs25 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul26) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms26='01-SI'"
        ."    AND acpfs26 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul27) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms27='01-SI'"
        ."    AND acpfs27 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul28) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms28='01-SI'"
        ."    AND acpfs28 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul29) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms29='01-SI'"
        ."    AND acpfs29 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul210) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms210='01-SI'"
        ."    AND acpfs210 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

        

  $err5 = mysql_query($quer5,$conex); 
    
  $quer6 ="SELECT nomcco,acconse,sum(cant)"
         ."  FROM tmp2"
         ." GROUP BY 1,2"
         ." ORDER BY 1,2";
         
  $err6 = mysql_query($quer6,$conex);  
  
  
  //Query para traer numero de pendientes por centro de costo de la accion nro 3 
  
  $quer7 ="CREATE TEMPORARY TABLE if not exists tmp3 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul31) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms31='01-SI'"
        ."    AND acpfs31 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul32) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms32='01-SI'"
        ."    AND acpfs32 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul33) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms33='01-SI'"
        ."    AND acpfs33 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul34) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms34='01-SI'"
        ."    AND acpfs34 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul35) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms35='01-SI'"
        ."    AND acpfs35 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul36) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms36='01-SI'"
        ."    AND acpfs36 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul37) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms37='01-SI'"
        ."    AND acpfs37 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul38) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms38='01-SI'"
        ."    AND acpfs38 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul39) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms39='01-SI'"
        ."    AND acpfs39 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul310) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms310='01-SI'"
        ."    AND acpfs310 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul31) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms31='01-SI'"
        ."    AND acpfs31 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul32) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms32='01-SI'"
        ."    AND acpfs32 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul33) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms33='01-SI'"
        ."    AND acpfs33 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul34) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms34='01-SI'"
        ."    AND acpfs34 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul35) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms35='01-SI'"
        ."    AND acpfs35 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul36) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms36='01-SI'"
        ."    AND acpfs36 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul37) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms37='01-SI'"
        ."    AND acpfs37 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul38) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms38='01-SI'"
        ."    AND acpfs38 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul39) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms39='01-SI'"
        ."    AND acpfs39 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul310) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms310='01-SI'"
        ."    AND acpfs310 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

  $err7 = mysql_query($quer7,$conex); 
    
  $quer8 ="SELECT nomcco,acconse,sum(cant)"
         ."  FROM tmp3"
         ." GROUP BY 1,2"
         ." ORDER BY 1,2";
         
  $err8 = mysql_query($quer8,$conex);  
  
   
  //Query para traer numero de pendientes por centro de costo de la accion nro 4
  
  $quer9 ="CREATE TEMPORARY TABLE if not exists tmp4 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul41) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms41='01-SI'"
        ."    AND acpfs41 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul42) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms42='01-SI'"
        ."    AND acpfs42 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul43) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms43='01-SI'"
        ."    AND acpfs43 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul44) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms44='01-SI'"
        ."    AND acpfs44 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul45) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms45='01-SI'"
        ."    AND acpfs45 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul46) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms46='01-SI'"
        ."    AND acpfs46 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul47) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms47='01-SI'"
        ."    AND acpfs47 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul48) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms48='01-SI'"
        ."    AND acpfs48 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul49) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms49='01-SI'"
        ."    AND acpfs49 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul410) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms410='01-SI'"
        ."    AND acpfs410 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul41) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms41='01-SI'"
        ."    AND acpfs41 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul42) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms42='01-SI'"
        ."    AND acpfs42 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul43) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms43='01-SI'"
        ."    AND acpfs43 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul44) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms44='01-SI'"
        ."    AND acpfs44 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul45) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms45='01-SI'"
        ."    AND acpfs45 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul46) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms46='01-SI'"
        ."    AND acpfs46 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul47) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms47='01-SI'"
        ."    AND acpfs47 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul48) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms48='01-SI'"
        ."    AND acpfs48 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul49) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms49='01-SI'"
        ."    AND acpfs49 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul410) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms410='01-SI'"
        ."    AND acpfs410 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

  $err9 = mysql_query($quer9,$conex); 
    
  $quer10 ="SELECT nomcco,acconse,sum(cant)"
         ."  FROM tmp4"
         ." GROUP BY 1,2"
         ." ORDER BY 1,2";
         
  $err10 = mysql_query($quer10,$conex);    

  
  //Query para traer numero de pendientes por centro de costo de la accion nro 5
  $quer11 ="CREATE TEMPORARY TABLE if not exists tmp5 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul51) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms51='01-SI'"
        ."    AND acpfs51 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul52) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms52='01-SI'"
        ."    AND acpfs52 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul53) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms53='01-SI'"
        ."    AND acpfs53 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul54) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms54='01-SI'"
        ."    AND acpfs54 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul55) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms55='01-SI'"
        ."    AND acpfs55 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul56) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms56='01-SI'"
        ."    AND acpfs56 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul57) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms57='01-SI'"
        ."    AND acpfs57 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul58) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms58='01-SI'"
        ."    AND acpfs58 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul59) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms59='01-SI'"
        ."    AND acpfs59 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul510) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms510='01-SI'"
        ."    AND acpfs510 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul51) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms51='01-SI'"
        ."    AND acpfs51 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul52) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms52='01-SI'"
        ."    AND acpfs52 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul53) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms53='01-SI'"
        ."    AND acpfs53 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul54) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms54='01-SI'"
        ."    AND acpfs54 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul55) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms55='01-SI'"
        ."    AND acpfs55 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul56) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms56='01-SI'"
        ."    AND acpfs56 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul57) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms57='01-SI'"
        ."    AND acpfs57 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul58) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms58='01-SI'"
        ."    AND acpfs58 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul59) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms59='01-SI'"
        ."    AND acpfs59 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul510) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acrms510='01-SI'"
        ."    AND acpfs510 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

  $err11 = mysql_query($quer11,$conex); 
    
  $quer12 ="SELECT nomcco,acconse,sum(cant)"
         ."  FROM tmp5"
         ." GROUP BY 1,2"
         ." ORDER BY 1,2";
         
  $err12 = mysql_query($quer12,$conex);    
  

  //Query para traer numero de acciones pendientes por centro de costo 
  $quer13="CREATE TEMPORARY TABLE if not exists tmp6 as "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc1) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND Acfec1 between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1>=".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc2) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND Acfec2 between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1>=".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc3) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND Acfec3 between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1>=".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc4) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND Acfec4 between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1>=".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc5) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND Acfec5 between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1>=".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc1) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND ".$empre1."_000012.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1<".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc2) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND ".$empre1."_000012.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1<".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc3) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND ".$empre1."_000012.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1<".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc4) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND ".$empre1."_000012.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1<".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ." UNION ALL "
           ." SELECT nomcco,acconse,count(*) as cant"
           ."   FROM tmp21, ".$empre1."_000012"
           ."  WHERE nomcco=accconom" 
           ."    AND cco   = accco"
           ."    AND CHAR_LENGTH(Acacc5) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
           ."    AND ".$empre1."_000012.fecha_data between '".$fec1."' and '".$fec2."'"
           ."    AND Acfec1<".$empre1."_000012.fecha_data"
           ."  GROUP by 1,2"
           ."  ORDER BY 1,2";
           
   //echo $quer13."<br>";     

   $err13 = mysql_query($quer13,$conex);         
   
   $quer14 ="SELECT nomcco,acconse,sum(cant)"
         ."  FROM tmp6"
         ." GROUP BY 1,2"
         ." ORDER BY 1,2";
         
   $err14 = mysql_query($quer14,$conex);   
   

   echo "<table border=1 cellpadding='0' cellspacing='0' align=center size=100%>";
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF'align=center ><font text color=#000000 size=2><b>UNIDAD</b></td>";
   echo "<td bgcolor='#FFFFFF'align=center ><font text color=#000000 size=2><b>CONSECUTIVO</b></td>";
   echo "<td bgcolor='#FFFFFF'align=center ><font text color=#000000 size=2><b>CANTIDAD PENDIENTES</b></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTO ACCION 1</b></td>";
   echo "</tr>";
   
   $num4 = mysql_num_rows($err4);

   $totxseg=0;
   $totcan=0;
   
   for ($j=1; $j <= $num4; $j++)
   {
    $row4 = mysql_fetch_array($err4);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row4[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row4[1]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row4[2]</font></td>"; 
    $totcan=$totcan+$row4[2];
    $totxseg=$totxseg+$row4[2];
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTO ACCION 1</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTO ACCION 2</b></td>";
   echo "</tr>";
  
   $num6 = mysql_num_rows($err6);
   
   for ($j=1; $j <= $num6; $j++)
   {
    $row6 = mysql_fetch_array($err6);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row6[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row6[1]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row6[2]</font></td>";
    $totcan=$totcan+$row6[2]; 
    $totxseg=$totxseg+$row6[2]; 
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTO ACCION 2</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTO ACCION 3</b></td>";
   echo "</tr>";
   
   $num8 = mysql_num_rows($err8);
   
   for ($j=1; $j <= $num8; $j++)
   {
    $row8 = mysql_fetch_array($err8);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row8[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row8[1]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row8[2]</font></td>"; 
    $totcan=$totcan+$row8[2];  
    $totxseg=$totxseg+$row8[2];
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTO ACCION 3</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTO ACCION 4</b></td>";
   echo "</tr>";
   
   $num10 = mysql_num_rows($err10);
   
   for ($j=1; $j <= $num10; $j++)
   {
    $row10 = mysql_fetch_array($err10);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row10[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row10[1]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row10[2]</font></td>"; 
    $totcan=$totcan+$row10[2];  
    $totxseg=$totxseg+$row10[2];
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTO ACCION 4</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTO ACCION 5</b></td>";
   echo "</tr>";
   
   $num12 = mysql_num_rows($err12);
   
   for ($j=1; $j <= $num12; $j++)
   {
    $row12 = mysql_fetch_array($err12);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row12[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row12[1]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row12[2]</font></td>"; 
    $totcan=$totcan+$row12[2]; 
    $totxseg=$totxseg+$row12[2];     
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTO ACCION 5</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>ACCIONES PENDIENTES</b></td>";
   echo "</tr>";
   
   $num14 = mysql_num_rows($err14);
   
   for ($j=1; $j <= $num14; $j++)
   {
    $row14 = mysql_fetch_array($err14);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row14[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row14[1]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row14[2]</font></td>"; 
    $totcan=$totcan+$row14[2];
    $totxseg=$totxseg+$row14[2];  
   }
   echo "</tr >";

   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL ACCIONES PENDIENTES</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL GENERAL</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1>&nbsp;</font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totcan</b></font></td>"; 
   echo "</tr >";
    
   echo "</table>";
    
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>
