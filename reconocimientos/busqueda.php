<?php
/************************************************************************************************************
 * Modificaciones:
 * ==========================================================================================================
 * 2020-11-13 Edwin MG	Se quita la conexión a la base de datos ya que se encontraba quemada
 ************************************************************************************************************/

include_once("conex.php"); 

?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>ejemplo de paginación de resultados</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<meta http-equiv="Pragma" content="no-cache" /> 
<style type="text/css"> 

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
de Resultados de una consulta SQL (sobre MySQL)<br><br><p><a href="http://www.pclandia.com">www.pclandia.com</a></p> </font></strong> </div> 
<hr noshade style="color:CC6666;height:1px"> 
<br> 
<?php 
//inicializo el criterio y recibo cualquier cadena que se desee buscar 
$criterio = ""; 
$txt_criterio = ""; 
if ($criterio <> "" )
{
 if ($HTTP_GET_VARS["criterio"]!="")
 { 
   $txt_criterio = $HTTP_GET_VARS["criterio"]; 
   $criterio = " where co_id like '%" . $txt_criterio . "%' or co_nombre like '%" . $txt_criterio . "%' or co_pais like '%" . $txt_criterio . "%'"; 
 } 
}

$sql="SELECT * FROM tpv.comercios ".$criterio; 
$res=mysql_query($sql); 
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
    $tamPag=5; 

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
$sql="SELECT * FROM tpv.comercios ".$criterio." ORDER BY ".$orden.",co_id ASC LIMIT ".$limitInf.",".$tamPag; 
$res=mysql_query($sql); 

//////////fin consulta con limites 
echo "<div align='center'>"; 
echo "<font face='verdana' size='-2'>encontrados ".$numeroRegistros." resultados<br>"; 
echo "ordenados por <b>".$orden."</b>"; 
if(isset($txt_criterio)){ 
    echo "<br>Valor filtro: <b>".$txt_criterio."</b>"; 
} 
echo "</font></div>"; 
echo "<table align='center' width='80%' border='0' cellspacing='1' cellpadding='0'>"; 
echo "<tr><td colspan='4'><hr noshade></td></tr>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=co_id&criterio=".$txt_criterio."'>Código</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=co_nombre&criterio=".$txt_criterio."'>Nombre</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$HTTP_SERVER_VARS["PHP_SELF"]."?pagina = ".$pagina."&orden=co_pais&criterio=".$txt_criterio."'>País</a></th>"; 
while($registro=mysql_fetch_array($res)) 
{ 
?> 
   <!-- tabla de resultados --> 
    <tr bgcolor="#CC6666" onMouseOver="this.style.backgroundColor='#FF9900';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#CC6666'"o"];" onClick="javascript:muestra('<?php echo "[".$registro["co_id"]."] ".$registro["co_nombre"]." - ".$registro["co_pais"]; ?>');"> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["co_id"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["co_nombre"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFCC"><b><?php echo $registro["co_pais"]; ?></b></font></td> 
    <td><A HREF='requerimientos00.php?wproceso=Cancelar&wnro=".$registro[0]."'>Actualizar</A></td>    
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
//////////fin de la paginacion 
?> 
    </td></tr> 
    </table> 
<hr noshade style="color:CC6666;height:1px"> 
<div align="center"><font face="verdana" size="-2"><a class="p" href="index.php">::Inicio::</a></font></div> 

<form action="busqueda.php" method="get"> 
Criterio de búsqueda: 
<input type="text" name="criterio" size="22" maxlength="150"> 
<input type="submit" value="Buscar"> 
</form> 

</body> 
</html> 
<?php 
    mysql_close(); 
?> 
