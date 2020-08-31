<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>Sistema de Informacion Programa CardioVascular PAF SURA</title> 
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
<div align="center"><strong><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Sistema de Informacion Programa CardioVascular
<br><td colspan=1 align=center></td><br>Archivo de Usuarios PAF SURA</font></strong></div> 

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
	$wversion="2015-09-01";
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



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//inicializo el criterio y recibo cualquier cadena que se desee buscar 
$criterio = ""; 
$txt_criterio = ""; 
if (isset($_GET["criterio"]))
{
 if ($_GET["criterio"]!="")
 { 
   $txt_criterio = $_GET["criterio"]; 
     $criterio = " where (Docaf like '%" . $txt_criterio . "%' or Apeaf1 like '%" . $txt_criterio . "%' or Apeaf2 like '%" . $txt_criterio . "%' or Nomaf like '%" . $txt_criterio . "%' or Tip_afiliado like '%" . $txt_criterio . "%' or Codips like '%" . $txt_criterio . "%') "; 
 }
}  

//$sql="SELECT socced,socap1,socap2,socnom,prfdes,socios_000001.id "
//    ." FROM socios_000001 LEFT  JOIN socios_000003 ON socced = proced LEFT  JOIN socios_000007 ON propro = prfcod  ".$criterio;

$sql="SELECT Docaf,Apeaf1,Apeaf2,Nomaf,Sexo,Fec_nacimiento, Tip_afiliado,  Telaf, Codips FROM pafsura_000098 ".$criterio;

$res=mysql_query($sql); 
$numeroRegistros=mysql_num_rows($res);
if($numeroRegistros<=0) 
{ 
    echo "<div align='center'>"; 
    echo "<font face='verdana' size='-2'>No se encontraron resultados</font>"; 
    echo "</div>"; 
}else{ 
    //////////elementos para el orden 
    if(!isset($id)) 
    { 
     $orden="Docaf"; 
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

$sql="SELECT Docaf,Apeaf1,Apeaf2,Nomaf,Sexo,Fec_nacimiento, Tip_afiliado, Telaf, Codips FROM pafsura_000098 ".$criterio
    ." ORDER BY ".$orden." LIMIT ".$limitInf.",".$tamPag; 
    
$res=mysql_query($sql); 
//////////fin consulta con limites 
echo "<center>";
echo "<div align='center'>"; 
echo "<font face='verdana' size='-2'>Resultados encontrados por criterio de busqueda: ".$numeroRegistros."<br>"; 
echo "ordenados por <b>".$orden."</b>"; 
if(isset($txt_criterio)){ 
    echo "<br>Criterio de busqueda o filtro: <b>".$txt_criterio."</b>"; 
} 
echo "</font></div>"; 


echo "<table align='center' width='80%' border='0' cellspacing='1' cellpadding='0'>"; 
echo "<tr><td colspan='6'><hr noshade></td></tr>"; 
 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Docaf&criterio=".$txt_criterio."'>Nro Documento</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Apeaf1&criterio=".$txt_criterio."'>1er Apellido</a></th>";
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Apeaf2&criterio=".$txt_criterio."'>2do Apellido</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Nomaf&criterio=".$txt_criterio."'>Nombres</a></th>"; 
//echo "<th bgcolor='#CCCCCC'><a class='ord' href='paf04.php' TARGET='_New'>Sexo <IMG SRC='Icon_05.ico' ALT='Genera Grafico por Sexo'></a></th>"; 
//echo "<th bgcolor='#CCCCCC'><a class='ord' href='paf05.php' TARGET='_New'>Fecha Nac/to<IMG SRC='Icon_05.ico' ALT='Genera Grafico por grupos de Edad'></a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Sexo&criterio=".$txt_criterio."'>Sexo</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Fec_nacimiento&criterio=".$txt_criterio."'>Fecha Nac/to</a></th>"; 

echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Tip_afiliado&criterio=".$txt_criterio."'>Tipo</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Telaf&criterio=".$txt_criterio."'>Telaf</a></th>";
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Codips&criterio=".$txt_criterio."'>Sede</a></th>"; 

     $usuario=explode("-",$user); 
     $query="select empresa from usuarios where codigo='".$usuario[1]."'";
     $r=mysql_query($query); 
     $reg=mysql_fetch_array($r);
     if ($reg["empresa"]<>"99")   // Si es un usuario de la empresa 99 no lo deja generar ordenes
       echo "<th bgcolor='#CCCCCC'><a class='ord' >Orden</a></th>"; 

echo "<th bgcolor='#CCCCCC'></th>"; 
while($registro=mysql_fetch_array($res)) 
{ 
?> 
   <!-- tabla de resultados --> 
   <!-- Si utilizo la sigiente linea definida asi, cada que den clic sobre la tabla muestra unos datos que yo defina con la fn 'muestra'
     <tr bgcolor="#CC6666" onMouseOver="this.style.backgroundColor='#FF9900';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#CC6666'"o"];" onClick="javascript:muestra('<?php echo "[".$registro["socced"]."] ".$registro["socap1"]." - ".$registro["socap2"]; ?>');"> 
   -->
    <tr bgcolor="#DDDDDD" onMouseOver="this.style.backgroundColor='#0099CC';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#DDDDDD'"o"];" > 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Docaf"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Apeaf1"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Apeaf2"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Nomaf"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Sexo"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Fec_nacimiento"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Tip_afiliado"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Telaf"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Codips"]; ?></b></font></td>

    <?php
     $wid=$registro["Docaf"];
 
     if ($reg["empresa"]<>"99")   // Si es un usuario de la empresa 99 no lo deja generar ordenes
       echo "<td><A HREF='paf01sura.php?wid=".$wid."&windicador=PrimeraVez&wproceso=Nuevo' TARGET='_blank'>Generar</A></td>";	     
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
       echo " <a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina+1)."&orden=".paf."&criterio=".$txt_criterio."'>"; 
       echo "<font face='verdana' size='-2'>siguiente</font></a>"; 
   }
}    
//////////fin de la paginacion 
?> 
  </td></tr> 
  </table> 

<hr noshade style="color:CC6666;height:1px"> 
<div align="center"><font face="verdana" size="-2"><a class="p" href="paf02sura.php">::Inicio::</a></font></div> 
<form action="paf02sura.php" method="get"> 

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
