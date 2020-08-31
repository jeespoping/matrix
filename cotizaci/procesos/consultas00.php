<?php
include_once("conex.php"); 


$conexN = odbc_connect('inventarios','','') or die("No se realizo Conexion con la BD suministros en Informix");
mysql_select_db("matrix") or die("No se selecciono la base de datos");   

?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>ejemplo de paginación de resultados</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<meta http-equiv="Pragma" content="no-cache" /> 
<style type="text/css"> 
<!-- 
a.p:link { 
    color: #0066FF; 
    text-decoration: none; 
} 
a.p:visited { 
    color: #0066FF; 
    text-decoration: none; 
} 
a.p:active { 
    color: #0066FF; 
    text-decoration: none; 
} 
a.p:hover { 
    color: #0066FF; 
    text-decoration: underline; 
} 
a.ord:link { 
    color: #000000; 
    text-decoration: none; 
} 
a.ord:visited { 
    color: #000000; 
    text-decoration: none; 
} 
a.ord:active { 
    color: #000000; 
    text-decoration: none; 
} 
a.ord:hover { 
    color: #000000; 
    text-decoration: underline; 
} 
--> 
</style> 
</head> 
<body bgcolor="#FFFFFF"> 
<script language="JavaScript"> 
function muestra(queCosa) 
{ 
    alert(queCosa); 
} 
</script> 
<div align="center"><strong><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Paginación 
de Resultados de una consulta SQL (sobre MySQL)<br><br><p><a href="http://www.lasamericas.com.co">www.lasamericas.com.co</a></p> </font></strong> </div> 
<hr noshade style="color:CC6666;height:1px"> 
<br> 
<?php 

/**********************/  
//$wemp="cotizaci";
$fecha = date("Y-m-d");  
$wano=substr($fecha,0,4);
$user="1-07013";          // El codigo asignado para entrar a matrix se deben asociar en la  
/**********************/  // tabla cotizaci_000005 al Codigo del Proveedor

   $query = "SELECT usunit FROM cotizaci_000005 Where usucod ='".substr($user,2,7)."'";   

   $resultado = mysql_query($query); 
   $nroreg = mysql_num_rows($resultado);
   if ( $nroreg > 0 )
	{	 
	 $registro = mysql_fetch_row($resultado); 	
	 $query2 = "SELECT procod,pronom FROM cppro WHERE procod = '".$registro[0]."'";	 
	 
     $resultado2 = odbc_do($conexN,$query2);            
     $wnit = odbc_result($resultado2,1);
     $wnompro = odbc_result($resultado2,2);   
    }



//inicializo el criterio y recibo cualquier cadena que se desee buscar 
$criterio = ""; 
$txt_criterio = ""; 
if (isset($HTTP_GET_VARS["criterio"]))
{
 if ($HTTP_GET_VARS["criterio"]!="")
 { 
   $txt_criterio = $HTTP_GET_VARS["criterio"]; 
   $criterio = " And (concod like '%" . $txt_criterio . "%' or connom like '%" . $txt_criterio . "%') "; 
 }
}  

   if (isset($wproceso) and $wproceso=="Nuevo")   //muestro todos los consumos del ultimo año
    $query = "SELECT concod,connom,conuni,conmes,conano FROM cotizaci_000001 ".$criterio." Order by connom";
   else
   {  	   
	// Muestro lo cotizado por el proveedor el año anterior y los consumos del ultimo año segun el query en 
	// UNIX "consumGral.sql" con el que poblamos la tabla cotizaci_000001 en Matrix   
    $wano2= (integer) $wano - 1;     
    $query = "SELECT concod,connom,conuni,conmes,conano FROM cotizaci_000003,cotizaci_000001 "
           ."Where (cotano = '".$wano2."' or cotano = '".$wano."') And cotnit = '".$wnit."' "
           .$criterio
           ."And cotcod = concod Group By concod,connom,conuni,conmes,conano Order by connom";
   }

$res=mysql_query($query); 
$numeroRegistros=mysql_num_rows($res); 
if($numeroRegistros<=0) 
{ 
    echo "<div align='center'>"; 
    echo "<font face='verdana' size='-2'>No se encontraron resultados</font>"; 
    echo "</div>"; 
}else{ 
    //////////elementos para el orden 
    if(!isset($orden)) 
    { 
       $orden="co_id"; 
    } 
    //////////fin elementos de orden 

    //////////calculo de elementos necesarios para paginacion 
    //tamaño de la pagina 
    $tamPag=25; 

    //pagina actual si no esta definida y limites 
    if(!isset($HTTP_GET_VARS["pagina"])) 
    { 
       $pagina=1; 
       $inicio=1; 
       $final=$tamPag; 
    }else{ 
       $pagina = $HTTP_GET_VARS["pagina"]; 
    } 
    //calculo del limite inferior 
    $limitInf=($pagina-1)*$tamPag; 

    //calculo del numero de paginas 
    $numPags=ceil($numeroRegistros/$tamPag); 
    if(!isset($pagina)) 
    { 
       $pagina=1; 
       $inicio=1; 
       $final=$tamPag; 
    }else{ 
       $seccionActual=intval(($pagina-1)/$tamPag); 
       $inicio=($seccionActual*$tamPag)+1; 

       if($pagina<$numPags) 
       { 
          $final=$inicio+$tamPag-1; 
       }else{ 
          $final=$numPags; 
       } 

       if ($final>$numPags){ 
          $final=$numPags; 
       } 
    } 

//////////fin de dicho calculo 

//////////creacion de la consulta con limites 
$query=$query.",concod ASC LIMIT ".$limitInf.",".$tamPag; 
$res=mysql_query($query); 

//////////fin consulta con limites 
echo "<div align='center'>"; 
echo "<font face='verdana' size='-2'>encontrados ".$numeroRegistros." resultados<br>"; 
echo "ordenados por <b>".$orden."</b>"; 
if(isset($txt_criterio)){ 
    echo "<br>Valor filtro: <b>".$txt_criterio."</b>"; 
} 
echo "</font></div>"; 
echo "<table align='center' width='80%' border='0' cellspacing='1' cellpadding='0'>"; 
echo "<tr><td colspan='3'><hr noshade></td></tr>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=concod&criterio=".$txt_criterio."'>Código</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=connom&criterio=".$txt_criterio."'>Nombre</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=conuni&criterio=".$txt_criterio."'>Unidad</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=conmes&criterio=".$txt_criterio."'>Mes</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=conano&criterio=".$txt_criterio."'>Año</a></th>"; 

while($registro=mysql_fetch_array($res)) 
{ 
?> 
   <!-- tabla de resultados --> 
    <tr bgcolor="#CC6666" onMouseOver="this.style.backgroundColor='#FF9900';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#CC6666'"o"];" onClick="javascript:muestra('<?php echo "[".$registro["co_id"]."] ".$registro["co_nombre"]." - ".$registro["co_pais"]; ?>');"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["concod"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["connom"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["conuni"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["conmes"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["conano"]; ?></b></font></td> 

    </tr> 
   <!-- fin tabla resultados --> 
<?php 
}//fin while 
echo "</table>"; 
}//fin if 
//////////a partir de aqui viene la paginacion 
?> 
    <br> 
    <table border="0" cellspacing="0" cellpadding="0" align="center"> 
    <tr><td align="center" valign="top"> 
<?php 
if (isset($pagina))
{
    if($pagina>1) 
    { 
       echo "<a class='p' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina=".($pagina-1)."&orden=".$orden."&criterio=".$txt_criterio."'>"; 
       echo "<font face='verdana' size='-2'>anterior</font>"; 
       echo "</a> "; 
    } 

    for($i=$inicio;$i<=$final;$i++) 
    { 
       if($i==$pagina) 
       { 
          echo "<font face='verdana' size='-2'><b>".$i."</b> </font>"; 
       }else{ 
          echo "<a class='p' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina=".$i."&orden=".$orden."&criterio=".$txt_criterio."'>"; 
          echo "<font face='verdana' size='-2'>".$i."</font></a> "; 
       } 
    } 
    if($pagina<$numPags) 
   { 
       echo " <a class='p' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina=".($pagina+1)."&orden=".$orden."&criterio=".$txt_criterio."'>"; 
       echo "<font face='verdana' size='-2'>siguiente</font></a>"; 
   }
}    
//////////fin de la paginacion 
?> 
  </td></tr> 
  </table> 

<hr noshade style="color:CC6666;height:1px"> 
<div align="center"><font face="verdana" size="-2"><a class="p" href="index.php">::Inicio::</a></font></div> 
<form action="busqueda.php" method="get"> 

<table border="0" cellspacing="0" cellpadding="0" align="center">         
Criterio de búsqueda: 
<input type="text" name="criterio" size="22" maxlength="150"> 
<input type="submit" value="Buscar"> 
</table> 

</form> 

</body> 
</html> 
<?php 
    mysql_close(); 
	odbc_close($conexN);
	odbc_close_all();
?> 
