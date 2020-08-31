<html>
<head>
<title>MATRIX - [REPORTE SEGUIMIENTOS REALIZADOS]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_seguireali.submit();
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
*                                             REPORTE SEGUIMIENTO REALIZADOS                                                               *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte de seguimientos realizados desarrollo organizacional.                                               |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : SEPTIEMBRE 7 DE 2011.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : SEPTIEMBRE 7 DE 2011.                                                                                       |
//DESCRIPCION			      : Este reporte sirve para observar por unidad cuantos seguimientos hay realizados.                            |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//proceso_000012      : Tabla de Acciones correctivas de los coordinadores.                                                                 |                                                                                                                                 
//proceso_000013      : Tabla de Acciones correctivas de Desarrollo.                                                                        |
//proceso_000015      : Tabla de Acciones correctivas del ccosto 1013.                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 07-Septiembre-2011";

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
encabezado("Seguimientos Realizados",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_seguireali.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_seguireali' action='' method=post>";
  
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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>SEGUIMIENTOS REALIZADOS</b></font></td>";
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
         
  $err2 = mysql_query($quer2,$conex);
    
  $quer211="DROP TABLE if exists tmp21 ";
  $err211 = mysql_query($quer211,$conex) or die("Imposible : ".mysql_error()); 
  
  $quer21 = "CREATE TABLE if not exists tmp21 as "
         ." SELECT accconom as nomcco,Accco as cco"
         ."   FROM ".$empre1."_000012"
         ."  GROUP BY 1,2"
         ."  ORDER BY 1";
    
   //echo $quer2."<br>";    
         
  $err21 = mysql_query($quer21,$conex);
  
  //Query para traer numero de pendientes por centro de costo de la accion nro 1
  
  $quer3 ="CREATE TEMPORARY TABLE if not exists tmp1 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul11) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg11 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul12) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg12 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul13) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg13 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul14) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg14 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul15) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg15 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul16) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg16 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul17) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg17 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul18) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg18 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul19) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg19 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul110) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg110 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul11) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg11 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul12) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg12 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul13) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg13 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul14) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg14 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul15) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg15 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul16) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg16 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul17) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg17 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul18) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg18 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul19) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg19 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul110) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg110 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";
        
     //echo $quer3."<br>";        
        
  $err3 = mysql_query($quer3,$conex); 
    
  $quer4 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp1"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
   //echo $quer4."<br>";  
         
  $err4 = mysql_query($quer4,$conex);

  
  //Query para traer numero de pendientes por centro de costo de la accion nro 2
  $quer5 ="CREATE TEMPORARY TABLE if not exists tmp2 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul21) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg21 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul22) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg22 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul23) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg23 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul24) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg24 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul25) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg25 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul26) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg26 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul27) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg27 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul28) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg28 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul29) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg29 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul210) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg210 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul21) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg21 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul22) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg22 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul23) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg23 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul24) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg24 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul25) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg25 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul26) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg26 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul27) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg27 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul28) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg28 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul29) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg29 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul210) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg210 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

        

  $err5 = mysql_query($quer5,$conex); 
    
  $quer6 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp2"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err6 = mysql_query($quer6,$conex);  
  
  
  //Query para traer numero de pendientes por centro de costo de la accion nro 3 
  
  $quer7 ="CREATE TEMPORARY TABLE if not exists tmp3 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul31) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg31 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul32) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg32 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul33) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg33 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul34) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg34 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul35) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg35 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul36) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg36 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul37) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg37 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul38) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg38 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul39) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg39 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul310) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg310 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul31) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg31 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul32) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg32 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul33) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg33 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul34) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg34 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul35) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg35 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul36) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg36 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul37) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg37 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul38) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg38 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul39) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg39 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul310) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg310 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

  $err7 = mysql_query($quer7,$conex); 
    
  $quer8 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp3"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err8 = mysql_query($quer8,$conex);  
  
   
  //Query para traer numero de pendientes por centro de costo de la accion nro 4
  
  $quer9 ="CREATE TEMPORARY TABLE if not exists tmp4 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul41) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg41 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul42) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg42 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul43) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg43 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul44) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg44 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul45) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg45 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul46) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg46 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul47) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg47 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul48) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg48 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul49) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg49 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul410) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg410 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul41) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg41 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul42) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg42 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul43) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg43 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul44) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg44 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul45) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg45 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul46) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg46 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul47) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg47 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul48) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg48 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul49) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg49 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul410) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg410 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

  $err9 = mysql_query($quer9,$conex); 
    
  $quer10 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp4"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err10 = mysql_query($quer10,$conex);    

  
  //Query para traer numero de pendientes por centro de costo de la accion nro 5
  $quer11 ="CREATE TEMPORARY TABLE if not exists tmp5 as "
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul51) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg51 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul52) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg52 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul53) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg53 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul54) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg54 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul55) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg55 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul56) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg56 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul57) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg57 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul58) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg58 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul59) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg59 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000013"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul510) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg510 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul51) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg51 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul52) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg52 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul53) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg53 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul54) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg54 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul55) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg55 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul56) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg56 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul57) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg57 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul58) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg58 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul59) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg59 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ." UNION ALL"
        ." SELECT nomcco,acconse,count(*) as cant"
        ."   FROM tmp, ".$empre1."_000015"
        ."  WHERE nomcco=accconom" 
        ."    AND cco   = accco"
        ."    AND CHAR_LENGTH(acresul510) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfecseg510 between '".$fec1."' and '".$fec2."'"
        ."  GROUP by 1,2"
        ."  ORDER BY 1,2";

  $err11 = mysql_query($quer11,$conex); 
    
  $quer12 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp5"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err12 = mysql_query($quer12,$conex);    
  
  
   echo "<table border=1 cellpadding='0' cellspacing='0' align=center size=100%>";
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF'align=center ><font text color=#000000 size=2><b>UNIDAD</b></td>";
   echo "<td bgcolor='#FFFFFF'align=center ><font text color=#000000 size=2><b>CANTIDAD REALIZADOS</b></td>";
   echo "</tr>";
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTOS ACCION 1</b></td>";
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
    $totcan=$totcan+$row4[1];
    $totxseg=$totxseg+$row4[1];
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTOS ACCION 1</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTOS ACCION 2</b></td>";
   echo "</tr>";
  
   $num6 = mysql_num_rows($err6);
   
   for ($j=1; $j <= $num6; $j++)
   {
    $row6 = mysql_fetch_array($err6);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row6[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row6[1]</font></td>";
    $totcan=$totcan+$row6[1]; 
    $totxseg=$totxseg+$row6[1]; 
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTOS ACCION 2</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTOS ACCION 3</b></td>";
   echo "</tr>";
   
   $num8 = mysql_num_rows($err8);
   
   for ($j=1; $j <= $num8; $j++)
   {
    $row8 = mysql_fetch_array($err8);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row8[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row8[1]</font></td>"; 
    $totcan=$totcan+$row8[1];  
    $totxseg=$totxseg+$row8[1];
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTOS ACCION 3</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTOS ACCION 4</b></td>";
   echo "</tr>";
   
   $num10 = mysql_num_rows($err10);
   
   for ($j=1; $j <= $num10; $j++)
   {
    $row10 = mysql_fetch_array($err10);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row10[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row10[1]</font></td>"; 
    $totcan=$totcan+$row10[1];  
    $totxseg=$totxseg+$row10[1];
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTOS ACCION 4</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   $totxseg=0;
   
   echo "<tr>";
   echo "<td bgcolor='#FFFFFF' align=center colspan='3'><font text color=#000000 size=1><b>SEGUIMIENTOS ACCION 5</b></td>";
   echo "</tr>";
   
   $num12 = mysql_num_rows($err12);
   
   for ($j=1; $j <= $num12; $j++)
   {
    $row12 = mysql_fetch_array($err12);
    
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row12[0]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$row12[1]</font></td>"; 
    $totcan=$totcan+$row12[1]; 
    $totxseg=$totxseg+$row12[1];     
   }
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL SEGUIMIENTOS ACCION 5</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totxseg</b></font></td>"; 
   echo "</tr >";
   
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>TOTAL GENERAL</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$totcan</b></font></td>"; 
   echo "</tr >";
    
   echo "</table>";
    
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>
