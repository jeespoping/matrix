<html>
<head>
<title>MATRIX - [REPORTE ACCIONES]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_acciocorrec.submit();
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
*                                             REPORTE ACCIONES CORRECTIVAS                                                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte de acciones correctivas desarrollo organizacional.                                                  |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : JULIO 21 DE 2011.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  : JULIO 21 DE 2011.                                                                                           |
//DESCRIPCION			      : Este reporte sirve para observar por unidad cuantas acciones correctivas hace en un rango de fecha.         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//proceso_000012      : Tabla de Acciones correctivas de los coordinadores.                                                                 |                                                                                                                                 
//proceso_000013      : Tabla de Acciones correctivas de Desarrollo.                                                                        |
//proceso_000015      : Tabla de Acciones correctivas del ccosto 1013.                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 26-Julio-2011";

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
encabezado("Estado Acciones",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_acciocorrec.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($cc) or $cc=='-' or !isset($fec1) or !isset($fec2))
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
 	
   /////////////////////////////////////////////////////////////////////////// seleccion para los procesos prioritarios
   echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Centro de Costos:</B><br></font></b><select name='cc' id='searchinput'>";

   $query20 = " SELECT accconom"
           ."   FROM ".$empre1."_000012"
           ." GROUP BY 1 "
           ." ORDER BY 1 ";
           
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
 else // Cuando ya estan todos los datos escogidos
  {
   $tcc=$cc;
   
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>ESTADO ACCIONES</b></font></td>";
   echo "</tr>";
   echo "<tr>";   
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>CENTRO DE COSTOS : </b>$tcc</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
   
   for ($i=1; $i <= 7; $i++)
   {
    $arretotal[$i] = 0;
   }
   
   
   IF ($tcc=='TODOS')
   {
	
   // Query para traer todos los centros de costos de la tabla 12 
   $quer1 = " SELECT accconom"
           ."   FROM ".$empre1."_000012"
           ."  GROUP BY accconom"
           ."  ORDER BY 1";

   $err = mysql_query($quer1,$conex);
   $num = mysql_num_rows($err);       
	
   $arrecco     = Array();
   $arrenoac    = Array();
   $arreacnew   = Array();
   $arrenoacnej = Array();
   $arreaceje   = Array();
   $arreacpro   = Array();
   $arreacnoefi = Array();
   $arreacefi   = Array();
  
  for ($j=1; $j <= $num; $j++)
   {
    $row = mysql_fetch_array($err);
    
    $arrecco[$j]    = $row[0];
    $arrenoac[$j]   = 0;
    $arreacnew[$j]  = 0;
    $arrenoacnej[$j]= 0;
    $arreaceje[$j]  = 0;
    $arreacpro[$j]  = 0;
    $arreacnoefi[$j]= 0;
    $arreacefi[$j]  = 0;
     
   }
  
  $quer20="DROP TABLE if exists tmp ";
  $err20 = mysql_query($quer20,$conex) or die("Imposible : ".mysql_error()); 

   
  $quer2 = "CREATE TABLE if not exists tmp as "
         ." SELECT accconom as nomcco,accco as cco"
         ."   FROM ".$empre1."_000012"
         ."  GROUP BY 1,2"
         ."  ORDER BY 1";
    
   //echo $quer2."<br>";    
         
  $err2 = mysql_query($quer2,$conex) or die("Imposible : ".mysql_error()); 
    
  
  //Query para traer numero de acciones por centro de costo
  
  $quer30 ="CREATE TEMPORARY TABLE if not exists tmp10 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."    AND cco   = accco"
        ."  WHERE CHAR_LENGTH(acacc1) > 2" //CHAR_LENGTH(acacc1) sirve para ver el total de caracteres  
        ."  GROUP by 1";
        
  $err30 = mysql_query($quer30,$conex) or die("Imposible : ".mysql_error()); 
        
      //  ." UNION ALL"
        
  $quer31="CREATE TEMPORARY TABLE if not exists tmp11 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."    AND cco   = accco"
        ."  WHERE CHAR_LENGTH(acacc2) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";
    
  $err31 = mysql_query($quer31,$conex) or die("Imposible : ".mysql_error());      
        
  $quer32="CREATE TEMPORARY TABLE if not exists tmp12 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE CHAR_LENGTH(acacc3) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";
        
  $err32 = mysql_query($quer32,$conex) or die("Imposible : ".mysql_error());      
      
        
  $quer33="CREATE TEMPORARY TABLE if not exists tmp13 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE CHAR_LENGTH(acacc4) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";
        
        
  $err33 = mysql_query($quer33,$conex) or die("Imposible : ".mysql_error());      

                
  $quer34="CREATE TEMPORARY TABLE if not exists tmp14 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE CHAR_LENGTH(acacc5) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";
   
  $err34 = mysql_query($quer34,$conex) or die("Imposible : ".mysql_error());      
     
    
     //echo $quer3."<br>";        
        
  $quer3="CREATE TEMPORARY TABLE if not exists tmp1 as "
        ." SELECT nomcco,cant"
        ."   FROM tmp10"
        ." UNION ALL"
        ." SELECT nomcco,cant"
        ."   FROM tmp11"
        ." UNION ALL"
        ." SELECT nomcco,cant"
        ."   FROM tmp12"
        ." UNION ALL"
        ." SELECT nomcco,cant"
        ."   FROM tmp13"
        ." UNION ALL"
        ." SELECT nomcco,cant"
        ."   FROM tmp14";


  $err3 = mysql_query($quer3,$conex) or die("Imposible : ".mysql_error());      
  
    
  $quer4 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp1"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  //echo $quer4."<br>";  
         
  $err4 = mysql_query($quer4,$conex) or die("Imposible : ".mysql_error());

  $num4 = mysql_num_rows($err4);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row4 = mysql_fetch_array($err4);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row4[0]) 
     {
      $arrenoac[$k]   = $row4[1];
     }
    }  
    
   }
   
  
   //Query para traer el total de acciones nuevas por centro de costos
 $quer50 ="CREATE TEMPORARY TABLE if not exists tmp20 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."    AND cco   = accco"
        ."  WHERE CHAR_LENGTH(acacc1) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND acfec1  between '".$fec1."' and '".$fec2."'" 
        ."  GROUP by 1";

  $err50 = mysql_query($quer50,$conex); 
        
        
 $quer51 ="CREATE TEMPORARY TABLE if not exists tmp21 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE acfec2  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc2) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";
        
   $err51 = mysql_query($quer51,$conex); 
       
        
 $quer52 ="CREATE TEMPORARY TABLE if not exists tmp22 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE acfec3  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc3) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";

  $err52 = mysql_query($quer52,$conex); 
        
        
 $quer53 ="CREATE TEMPORARY TABLE if not exists tmp23 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE acfec4  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc4) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1"; 
        
   $err53 = mysql_query($quer53,$conex); 
       
               
 $quer54 ="CREATE TEMPORARY TABLE if not exists tmp24 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000012"
        ."     ON nomcco=accconom"
        ."    AND cco   = accco" 
        ."  WHERE acfec5  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc5) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1";
        
  $err54 = mysql_query($quer54,$conex); 

  $quer5 ="CREATE TEMPORARY TABLE if not exists tmp2 as "
         ."SELECT nomcco,cant"
         ."  FROM tmp20"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp21"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp22"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp23"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp24";
         

  $err5 = mysql_query($quer5,$conex); 
    
  $quer6 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp2"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err6 = mysql_query($quer6,$conex);  
  
  $num6 = mysql_num_rows($err6);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row6 = mysql_fetch_array($err6);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row6[0]) 
     {
      $arreacnew[$k]   = $row6[1];
     }
    }  
    
   }
  
  
   
  //Query para traer de la tabla 13 las acciones clasificadas 1-NO EJECUTADAS
  $quer70 ="CREATE TEMPORARY TABLE if not exists tmp30 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
  $err70 = mysql_query($quer70,$conex); 
    
        
  $quer71 ="CREATE TEMPORARY TABLE if not exists tmp31 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
  $err71 = mysql_query($quer71,$conex); 
    
  $quer72 ="CREATE TEMPORARY TABLE if not exists tmp32 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
  $err72 = mysql_query($quer72,$conex); 
    
  $quer73 ="CREATE TEMPORARY TABLE if not exists tmp33 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
   $err73 = mysql_query($quer73,$conex); 
     
        
  $quer74 ="CREATE TEMPORARY TABLE if not exists tmp34 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
  $err74 = mysql_query($quer74,$conex); 
    
        
  $quer75 ="CREATE TEMPORARY TABLE if not exists tmp35 as "
         ." SELECT nomcco,count(*) as cant"
         ."   FROM tmp left join ".$empre1."_000015"
         ."     ON nomcco=accconom" 
         ."  WHERE acclasi1 = '1-NO EJECUTADAS'"
         ."  GROUP by 1";
  $err75 = mysql_query($quer75,$conex); 
     
        
   $quer76 ="CREATE TEMPORARY TABLE if not exists tmp36 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
     $err76= mysql_query($quer76,$conex); 
     
        
   $quer77 ="CREATE TEMPORARY TABLE if not exists tmp37 as "
         ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
      $err77 = mysql_query($quer77,$conex); 
    
        
   $quer78 ="CREATE TEMPORARY TABLE if not exists tmp38 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";
   $err78 = mysql_query($quer78,$conex); 
      
        
   $quer79 ="CREATE TEMPORARY TABLE if not exists tmp39 as "
         ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '1-NO EJECUTADAS'"
        ."  GROUP by 1";        
  $err79 = mysql_query($quer79,$conex); 

  $quer7 ="CREATE TEMPORARY TABLE if not exists tmp3 as "
         ."SELECT nomcco,cant"
         ."  FROM tmp30"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp31"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp32"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp33"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp34"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp35"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp36"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp37"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp38"
         ." UNION ALL "
         ."SELECT nomcco,cant"
         ."  FROM tmp39"
         ."  ORDER BY nomcco";

  $err7 = mysql_query($quer7,$conex) or die("Imposible : ".mysql_error()); 
    
  $quer8 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp3"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err8 = mysql_query($quer8,$conex);  
  
  $num8 = mysql_num_rows($err8);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row8 = mysql_fetch_array($err8);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row8[0]) 
     {
      $arrenoacnej[$k] = $row8[1];
     }
    }  
    
   }
  
  
  $query90="DROP TABLE if exists tmp4 ";
  $err90 = mysql_query($query90,$conex);  
  
  
  //Query para traer de la tabla 13 las acciones clasificadas 2-EJECUTADAS
  $quer9 ="CREATE TABLE if not exists tmp4 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '2-EJECUTADAS'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err9 = mysql_query($quer9,$conex); 
    
  $quer10 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp4"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err10 = mysql_query($quer10,$conex);    

  $num10 = mysql_num_rows($err10);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row10 = mysql_fetch_array($err10);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row10[0]) 
     {
      $arreaceje[$k] = $row10[1];
     }
    }  
    
   }
  
  //Query para traer de la tabla 13 las acciones subclasificadas 4-EN PROCESO
  $quer11 ="CREATE TEMPORARY TABLE if not exists tmp5 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '4-EN PROCESO'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err11 = mysql_query($quer11,$conex); 
    
  $quer12 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp5"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err12 = mysql_query($quer12,$conex);    
  

  $num12 = mysql_num_rows($err12);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row12 = mysql_fetch_array($err12);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row12[0]) 
     {
      $arreacpro[$k] = $row12[1];
     }
    }  
    
   }
  
  
  //Query para traer de la tabla 13 las acciones subclasificadas 3-NO EFICAZ
  $quer13 ="CREATE TEMPORARY TABLE if not exists tmp6 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '3-NO EFICAZ'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err13 = mysql_query($quer13,$conex); 
    
  $quer14 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp6"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err14 = mysql_query($quer14,$conex);    

  $num14 = mysql_num_rows($err14);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row14 = mysql_fetch_array($err14);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row14[0]) 
     {
      $arreacnoefi[$k] = $row14[1];
     }
    }  
    
   }
  
  //Query para traer de la tabla 13 las acciones subclasificadas 2-EFICAZ
  $quer15 ="CREATE TEMPORARY TABLE if not exists tmp7 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '2-EFICAZ'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '2-EFICAZ'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '2-EFICAZ'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err15 = mysql_query($quer15,$conex); 
    
  $quer16 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp7"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err16 = mysql_query($quer16,$conex);    
  
  $num16 = mysql_num_rows($err16);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row16 = mysql_fetch_array($err16);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row16[0]) 
     {
      $arreacefi[$k] = $row16[1];
     }
    }  
    
   }
 
}
ELSE // IF DE TODOS
{  

  $query1 = " SELECT Accconom "
           ."   FROM ".$empre1."_000012"
           ."  WHERE Accconom = '".$tcc."'" 
           ."  GROUP BY Accconom"
           ."  ORDER BY Accconom";
   
  //echo $query1."<br>";    
    
  $err = mysql_query($query1,$conex);
  $num = mysql_num_rows($err);
   
  $arrecco     = Array();
  $arrenoac    = Array();
  $arreacnew   = Array();
  $arrenoacnej = Array();
  $arreaceje   = Array();
  $arreacpro   = Array();
  $arreacnoefi = Array();
  $arreacefi   = Array();
  
  for ($j=1; $j <= $num; $j++)
   {
    $row = mysql_fetch_array($err);
    
    $arrecco[$j]    = $row[0];
    $arrenoac[$j]   = 0;
    $arreacnew[$j]  = 0;
    $arrenoacnej[$j]= 0;
    $arreaceje[$j]  = 0;
    $arreacpro[$j]  = 0;
    $arreacnoefi[$j]= 0;
    $arreacefi[$j]  = 0;
     
   }
   
  $query90="DROP TABLE if exists tmp111 ";
  $err90 = mysql_query($query90,$conex);  
  
  $quer2 = "CREATE TABLE if not exists tmp111 as "
         ." SELECT accconom as nomcco"
         ."   FROM ".$empre1."_000012"
         ."  WHERE Accconom = '".$tcc."'" 
         ."  GROUP BY accconom"
         ."  ORDER BY 1";
    
    // echo $quer2."<br>";    
  $err2 = mysql_query($quer2,$conex);
    
 //Query para traer numero de acciones por centro de costo
  
  $quer3 ="CREATE TEMPORARY TABLE if not exists tmp1111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE CHAR_LENGTH(acacc1) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'" 
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE CHAR_LENGTH(acacc2) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE CHAR_LENGTH(acacc3) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE CHAR_LENGTH(acacc4) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"        
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE CHAR_LENGTH(acacc5) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

   //  echo $quer3."<br>";
        
  $err3 = mysql_query($quer3,$conex); 
    
  $quer4 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp1111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
   //  echo $quer4."<br>";
        
  $err4 = mysql_query($quer4,$conex);       

  $num4 = mysql_num_rows($err4);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row4 = mysql_fetch_array($err4);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row4[0]) 
     {
      $arrenoac[$k]   = $row4[1];
     }
    }  
   }
  
  
  //Query para traer el total de acciones nuevas por centro de costos
  $quer5 ="CREATE TEMPORARY TABLE if not exists tmp2111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE ".$empre1."_000012.acfec1  between '".$fec1."' and '".$fec2."'"
        ."    AND Accconom = '".$tcc."'"
        ."    AND CHAR_LENGTH(acacc1) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE ".$empre1."_000012.acfec2  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc2) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE ".$empre1."_000012.acfec3  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc3) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE ".$empre1."_000012.acfec4  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc4) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"        
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000012"
        ."     ON nomcco=accconom" 
        ."  WHERE ".$empre1."_000012.acfec5  between '".$fec1."' and '".$fec2."'"
        ."    AND CHAR_LENGTH(acacc5) > 2" //CHAR_LENGTH(acresul11) sirve para ver el total de caracteres  
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by nomcco"
        ."  ORDER BY nomcco";

  $err5 = mysql_query($quer5,$conex); 
    
  $quer6 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp2111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err6 = mysql_query($quer6,$conex);  
  $num6 = mysql_num_rows($err6);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row6 = mysql_fetch_array($err6);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row6[0]) 
     {
      $arreacnew[$k]   = $row6[1];
     }
    }  
    
   }
  //Query para traer de la tabla 13 las acciones clasificadas 1-NO EJECUTADAS
  $quer7 ="CREATE TEMPORARY TABLE if not exists tmp3111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '1-NO EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"       
        ."  ORDER BY nomcco";

  $err7 = mysql_query($quer7,$conex); 
    
  $quer8 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp3111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err8 = mysql_query($quer8,$conex);  
  $num8 = mysql_num_rows($err8);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row8 = mysql_fetch_array($err8);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row8[0]) 
     {
      $arrenoacnej[$k] = $row8[1];
     }
    }  
    
   } 
  
  //Query para traer de la tabla 13 las acciones clasificadas 2-EJECUTADAS
  $quer9 ="CREATE TEMPORARY TABLE if not exists tmp4111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi1 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi2 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi3 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi4 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acclasi5 = '2-EJECUTADAS'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"        
        ."  ORDER BY nomcco";

  $err9 = mysql_query($quer9,$conex); 
    
  $quer10 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp4111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err10 = mysql_query($quer10,$conex);    
  $num10 = mysql_num_rows($err10);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row10 = mysql_fetch_array($err10);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row10[0]) 
     {
      $arreaceje[$k] = $row10[1];
     }
    }  
   }
    
  //Query para traer de la tabla 13 las acciones subclasificadas 4-EN PROCESO
  $quer11 ="CREATE TEMPORARY TABLE if not exists tmp5111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '4-EN PROCESO'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err11 = mysql_query($quer11,$conex); 
    
  $quer12 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp5111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err12 = mysql_query($quer12,$conex);    
  $num12 = mysql_num_rows($err12);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row12 = mysql_fetch_array($err12);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row12[0]) 
     {
      $arreacpro[$k] = $row12[1];
     }
    }  
    
   } 

  //Query para traer de la tabla 13 las acciones subclasificadas 3-NO EFICAZ
  $quer13 ="CREATE TEMPORARY TABLE if not exists tmp6111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '3-NO EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err13 = mysql_query($quer13,$conex); 
    
  $quer14 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp6111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err14 = mysql_query($quer14,$conex);    
  $num14 = mysql_num_rows($err14);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row14 = mysql_fetch_array($err14);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row14[0]) 
     {
      $arreacnoefi[$k] = $row14[1];
     }
    }  
    
   }
  
  //Query para traer de la tabla 13 las acciones subclasificadas 2-EFICAZ
  $quer15 ="CREATE TEMPORARY TABLE if not exists tmp7111 as "
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000013"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla1 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla2 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla3 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla4 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ." UNION ALL"
        ." SELECT nomcco,count(*) as cant"
        ."   FROM tmp111 left join ".$empre1."_000015"
        ."     ON nomcco=accconom" 
        ."  WHERE acsubcla5 = '2-EFICAZ'"
        ."    AND Accconom = '".$tcc."'"
        ."  GROUP by 1"
        ."  ORDER BY nomcco";

  $err15 = mysql_query($quer15,$conex); 
    
  $quer16 ="SELECT nomcco,sum(cant)"
         ."  FROM tmp7111"
         ." GROUP BY 1"
         ." ORDER BY 1";
         
  $err16 = mysql_query($quer16,$conex);  
  $num16 = mysql_num_rows($err16);    
  
  for ($j=1; $j <= $num; $j++)
   {
    $row16 = mysql_fetch_array($err16);
    
    for ($k=1; $k <= $num; $k++)
    {
     IF ($arrecco[$k]==$row16[0]) 
     {
      $arreacefi[$k] = $row16[1];
     }
    }  
    
   }
    
 } 

      echo "<table border=1 cellpadding='0' cellspacing='0' size='140'>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>UNIDAD</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>Nro AC</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>AC Nuevas</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>%</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>Nro AC NO Ejecutadas</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>%</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>Nro AC Ejecutadas</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>%</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>Nro AC En Procesos</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>%</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>Nro AC NO eficaces</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>%</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>Nro AC Eficaces</b></td>";
      echo "<td bgcolor='#FFFFFF'align=center width=100><font text color=#000000 size=2><b>%</b></td>";
      echo "</tr>";


  for ($i=1;$i<=$num;$i++)
   {
	 
    echo "<Tr>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>$arrecco[$i]</font></td>"; 
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrenoac[$i])."</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacnew[$i])."</font></td>";
    
    IF ($arrenoac[$i] == 0)
    {
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>0%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrenoacnej[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>0%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreaceje[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>0%</font></td>";
    }
    ELSE
    {
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arreacnew[$i]/$arrenoac[$i])*100)."%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arrenoacnej[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrenoacnej[$i]/$arrenoac[$i])*100)."%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreaceje[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arreaceje[$i]/$arrenoac[$i])*100)."%</font></td>";
    }
    
    IF ($arreaceje[$i] == 0)
    {
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacpro[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>0%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacnoefi[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>0%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacefi[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>0%</font></td>";
    }
    ELSE
    {
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacpro[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arreacpro[$i]/$arreaceje[$i])*100)."%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacnoefi[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arreacnoefi[$i]/$arreaceje[$i])*100)."%</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($arreacefi[$i])."</font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arreacefi[$i]/$arreaceje[$i])*100)."%</font></td>";
    }
    
    $arretotal[1]=$arretotal[1]+$arrenoac[$i];
    $arretotal[2]=$arretotal[2]+$arreacnew[$i];
    $arretotal[3]=$arretotal[3]+$arrenoacnej[$i];
    $arretotal[4]=$arretotal[4]+$arreaceje[$i];
    $arretotal[5]=$arretotal[5]+$arreacpro[$i];
    $arretotal[6]=$arretotal[6]+$arreacnoefi[$i];
    $arretotal[7]=$arretotal[7]+$arreacefi[$i];
    
   } 
   echo "</tr >";
      
   echo "<Tr>";
   echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>TOTAL GENERAL</b></font></td>"; 
   echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[1])."</b></font></td>";
   echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[2])."</b></font></td>";
   
   IF ($arretotal[1] == 0)
    {
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>0%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[3])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>0%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[4])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>0%</b></font></td>";
    }
   ELSE
   {
   	 echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format(($arretotal[2]/$arretotal[1])*100)."%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[3])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format(($arretotal[3]/$arretotal[1])*100)."%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[4])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format(($arretotal[4]/$arretotal[1])*100)."%</b></font></td>"; 
   }
   
  IF ($arretotal[4] == 0)
    {
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[5])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>0%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[6])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>0%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[7])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>0%</b></font></td>";
    }
    ELSE
    {
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[5])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format(($arretotal[5]/$arretotal[4])*100)."%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[6])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format(($arretotal[6]/$arretotal[4])*100)."%</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format($arretotal[7])."</b></font></td>";
     echo "<td  bgcolor=#FFFFFF align=center><font color=#003366 size=2><b>".number_format(($arretotal[7]/$arretotal[4])*100)."%</b></font></td>";
    }
   
  $query90="DROP TABLE if exists tmp111 ";
  $err90 = mysql_query($query90,$conex);  
 
  $query90="DROP TABLE if exists tmp4 ";
  $err90 = mysql_query($query90,$conex);   
   
   echo "</table>";
    
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>
