<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>Sistema de Informacion de Socios de PMLA</title> 
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
<div align="center"><strong><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Sistema de Informacion
Socios<br><td colspan=1 align=center></td><br><A HREF='socios01.php?windicador=PrimeraVez&wproceso=Nuevo' TARGET='_blank' >Adicionar nuevo socio</A> </font></strong> </div> 

	     

<hr noshade style="color:CC6666;height:1px"> 
<?php
include_once("conex.php"); 
/*****************************************************************
   Modificacion 04-04-2012  
     Se introdujo $_GET porque $HTTP_GET_VARS    quedó obsoleto  
     Se introdujo $_SERVER que $HTTP_SERVER_VARS quedó obsoleto  
 ****************************************************************
 * Escribe en el programa el autor y la version del Script
 * No recibe ningun parametro
 */
function pintarVersion()
{
	$wautor="Jair Saldarriaga O.";
	$wversion="2011-09-22";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	//echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	//echo "</tr>" ;
	echo "</table></br></br></br>" ;
}

pintarVersion(); //Escribe en el programa el autor y la version del Script.

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

/*    Para correrlo por fuera    
$user="07012";
*/



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//inicializo el criterio y recibo cualquier cadena que se desee buscar 
$criterio = ""; 
$txt_criterio = ""; 
if (isset($_GET["criterio"]))
{
 if ($_GET["criterio"]!="")
 { 
   $txt_criterio = $_GET["criterio"]; 
     $criterio = " where (socced like '%" . $txt_criterio . "%' or socap1 like '%" . $txt_criterio . "%' or socap2 like '%" . $txt_criterio . "%' or socnom like '%" . $txt_criterio . "%' or prfdes like '%" . $txt_criterio . "%') "; 
//   $criterio = "   AND (socced like '%" . $txt_criterio . "%' or socap1 like '%" . $txt_criterio . "%' or socap2 like '%" . $txt_criterio . "%' or socnom like '%" . $txt_criterio . "%' or prfdes like '%" . $txt_criterio . "%' )"; 

 }
}  
//$sql="SELECT socced,socap1,socap2,socnom,id FROM socios_000001 ".$criterio; 


//$sql="SELECT socced,socap1,socap2,socnom,prfdes,socios_000001.id "
//    ." FROM socios_000001 LEFT JOIN (socios_000003,socios_000007) ON (socced=proced AND propro=prfcod ) ".$criterio;

$sql="SELECT socced FROM socios_000001";
$res=mysql_query($sql); 
$numeroSocios=mysql_num_rows($res);

//  ***********************************************************************************************************************    
//  NOTA: TENGA EN CUENTA QUE SI SON 577 SOCIOS ESTE QUERY PUEDE DEVOLVER POR EJEMPLO 581 REGISTROS LO QUE SIGNIFICA QUE  *
//        HAY 4 SOCIOS QUE TINEN MAS DE 1 PROFESION                                                                       *
//  ***********************************************************************************************************************
$sql="SELECT socced,socap1,socap2,socnom,prfdes,socios_000001.id "
    ." FROM socios_000001 LEFT  JOIN socios_000003 ON socced = proced LEFT  JOIN socios_000007 ON propro = prfcod  ".$criterio;

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
       $orden="socced"; 
    } 
    //////////fin elementos de orden 

    //////////calculo de elementos necesarios para paginacion 
    //tamaño de la pagina 
    $tamPag=14; 

    //pagina actual si no esta definida y limites 
    if(!isset($_GET["pagina"])) 
    { 
       $pagina=1; 
       $inicio=1; 
       $final=$tamPag; 
    }else{ 
       $pagina = $_GET["pagina"]; 
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

$sql="SELECT socced,socap1,socap2,socnom,prfdes,socios_000001.id "
    ." FROM socios_000001 LEFT  JOIN socios_000003 ON socced = proced LEFT  JOIN socios_000007 ON propro = prfcod ".$criterio
    ." ORDER BY ".$orden.",socced ASC LIMIT ".$limitInf.",".$tamPag; 
    
$res=mysql_query($sql); 
//////////fin consulta con limites 
echo "<div align='center'>"; 
echo "<font face='verdana' size='-2'>Total Socios ".$numeroSocios." <br>"; 
echo "<font face='verdana' size='-2'>Resultados encontrados por profesion y criterio de busqueda: ".$numeroRegistros."<br>"; 
echo "ordenados por <b>Cedula</b>"; 
if(isset($txt_criterio)){ 
    echo "<br>Criterio de busqueda o filtro: <b>".$txt_criterio."</b>"; 
} 
echo "</font></div>"; 
echo "<table align='center' width='80%' border='0' cellspacing='1' cellpadding='0'>"; 
echo "<tr><td colspan='6'><hr noshade></td></tr>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=socced&criterio=".$txt_criterio."'>Cedula</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=socap1&criterio=".$txt_criterio."'>1erApellido</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=socap2&criterio=".$txt_criterio."'>2doApellido</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=socnom&criterio=".$txt_criterio."'>Nombres</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=prfdes&criterio=".$txt_criterio."'>Profesion</a></th>"; 
echo "<th bgcolor='#CCCCCC'></th>"; 
while($registro=mysql_fetch_array($res)) 
{ 
?> 
   <!-- tabla de resultados --> 
   <!-- Si utilizo la sigiente linea definida asi, cada que den clic sobre la tabla muestra unos datos que yo defina con la fn 'muestra'
     <tr bgcolor="#CC6666" onMouseOver="this.style.backgroundColor='#FF9900';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#CC6666'"o"];" onClick="javascript:muestra('<?php echo "[".$registro["socced"]."] ".$registro["socap1"]." - ".$registro["socap2"]; ?>');"> 
   -->
    <tr bgcolor="#DDDDDD" onMouseOver="this.style.backgroundColor='#0099CC';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#DDDDDD'"o"];" > 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["socced"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["socap1"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["socap2"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["socnom"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["prfdes"]; ?></b></font></td>

    <?php
     $wid=$registro[4];
     echo "<td><A HREF='socios01.php?wcod=".$registro[0]."&windicador=PrimeraVez&wproceso=Modificar&wid=".$wid."' TARGET='_blank' >Editar</A></td>";	     
    ?>
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
       echo "<a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina-1)."&orden=".$orden."&criterio=".$txt_criterio."'>"; 
       echo "<font face='verdana' size='-2'>anterior</font>"; 
       echo "</a> "; 
    } 

    for($i=$inicio;$i<=$final;$i++) 
    { 
       if($i==$pagina) 
       { 
          echo "<font face='verdana' size='-2'><b>".$i."</b> </font>"; 
       }else{ 
          echo "<a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".$i."&orden=".$orden."&criterio=".$txt_criterio."'>"; 
          echo "<font face='verdana' size='-2'>".$i."</font></a> "; 
       } 
    } 
    if($pagina<$numPags) 
   { 
       echo " <a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina+1)."&orden=".$orden."&criterio=".$txt_criterio."'>"; 
       echo "<font face='verdana' size='-2'>siguiente</font></a>"; 
   }
}    
//////////fin de la paginacion 
?> 
  </td></tr> 
  </table> 

<hr noshade style="color:CC6666;height:1px"> 
<div align="center"><font face="verdana" size="-2"><a class="p" href="socios00.php">::Inicio::</a></font></div> 
<form action="socios00.php" method="get"> 

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
?> 
