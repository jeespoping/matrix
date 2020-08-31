<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>Sistema de Informacion Programa CardioVascular PAF SALUD TOTAL</title> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"> 
<meta http-equiv="Pragma" content="no-cache" /> 
<style type="text/css"> 
<!-- 
a.p:link { 
    color: #0066FF; 
    text-decoration: none; 
} tip
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
<div align="center"><strong>
<font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Sistema de Informacion Programa PAF SALUD TOTAL
<br><td colspan=1 align=center><A HREF='paf01total.php?windicador=PrimeraVez&wproceso=Nuevo' TARGET='_blank' >Adicionar nueva Orden</A></td>
</font></strong> 
</div> 

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
	echo "<table  border=0 align='Right'>" ;
	echo "<tr>" ;
	echo "<td colspan=1 align=center><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "<tr>" ;
	echo "<td colspan=1 align=center><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</table>" ;
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
     $criterio = " where (pafced like '%" . trim($txt_criterio) . "%' or pafap1 like '%" . trim($txt_criterio) . "%' or pafnom like '%" . trim($txt_criterio) . "%' or pafcco like '%"
	             . trim($txt_criterio) ."%' or pafexa like '%" . trim($txt_criterio) . "%' or Id = '" . trim($txt_criterio) . "') "; 
 }
}  

// $user = "01-07012";        // PARA PRUEBAS LOCALES

// Se obtiene el ccosto del usuario, si el cco es % muestra todo si no muestra solo 
// las ordenes a ese centro de costo
$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user)); 

$query = "SELECT suracco FROM pafsura_000099 Where surausr='".$wusuario."'";
$resultado = mysql_query($query);
$registro = mysql_fetch_row($resultado);  	
if ( $criterio !="" )
 $criterio2=" And pafcco Like '".$registro[0]."' ";
else
 $criterio2=" Where pafcco Like '".$registro[0]."' ";	

if ( $registro[0] != "%" )    //En las unidades que dan las citas usuarios con ccosto definido no ven las Ordenes de vigencias futuras
  $criterio2=$criterio2." And paffre <= '".date("Y-m-d")."' ";

if ( $wopcion == "on" )       //Para solo mostrar las ordenes que estan sin asignar fecha de cita
  $criterio2=$criterio2." And paffci='0000-00-00' ";	
  
if ( $wopcion2 == "on" )    //Para solo mostrar las ordenes PRIORITARIAS
  $criterio2=$criterio2." And  pafser='4' ";	
  
if ( $wopcion3 == "on" )    //Para solo mostrar aquien se ha llamado mas de 3 veces
  $criterio2=$criterio2." And  paflla='on' ";
  
$sql="SELECT * FROM paftotal_000001 Where pafest='X'";
$res=mysql_query($sql); 
$NumeroAnulados=mysql_num_rows($res);


$sql="SELECT paffre,pafced,pafap1,pafnom,pafcco,pafser,id,pafest,paffre,paffci,pafexa  FROM paftotal_000001 ".$criterio.$criterio2;
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
       $orden="paffre DESC,id DESC "; 
    } 
    //////////fin elementos de orden 

    //////////calculo de elementos necesarios para paginación 
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

$sql="SELECT paffre,pafced,pafap1,pafnom,pafcco,pafser,id,pafest,paffre,paffci,pafexa  FROM paftotal_000001 ".$criterio.$criterio2
    ." ORDER BY ".$orden." LIMIT ".$limitInf.",".$tamPag; 
    
$res=mysql_query($sql); 
//////////fin consulta con limites 
echo "<div align='center'>"; 
echo "<font face='verdana' size='-2'>Resultados encontrados por criterio de busqueda: ".$numeroRegistros." Total Ordenes Anuladas: ".$NumeroAnulados."<br>";
echo "Ordenados por <b>".$orden."</b>"; 
if(isset($txt_criterio)){ 
    echo "<br>Criterio de busqueda o filtro: <b>".$txt_criterio."</b>"; 
} 
echo "</font></div>"; 
echo "<table align='center' width='80%' border='1' cellspacing='1' cellpadding='0'>"; 

//echo "<tr><td colspan='6'><hr noshade></td></tr>";   // Hace una raya para colspan='6' 
echo "<hr noshade style='color:CC6666;height:1px'>";   // hace una raya del tamaño de la pantalla

echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=paffre&criterio=".$txt_criterio."'>Fecha</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=pafced&criterio=".$txt_criterio."'>Nro Documento</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=pafap1&criterio=".$txt_criterio."'>Apellidos</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=pafnom&criterio=".$txt_criterio."'>Nombres</a></th>"; 
echo "<th bgcolor='#CC6666'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=pafcco&criterio=".$txt_criterio."'>Unidad</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=pafser&criterio=".$txt_criterio."'>Tipo</a></th>"; 
echo "<th bgcolor='#CC6666'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=pafexa&criterio=".$txt_criterio."'>Examen</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=id&criterio=".$txt_criterio."'>Nro Orden</a></th>"; 
echo "<th bgcolor='#CCCCCC'></th>"; 
echo "<th bgcolor='#CCCCCC'></th>"; 

while($registro=mysql_fetch_array($res)) 
{ 

 // TOMO EL NOMBRE DEL CENTRO DE COSTO
 $sql = "SELECT cconom FROM costosyp_000005 WHERE ccocod='".$registro["pafcco"]."'"; 
 $rs=mysql_query($sql); 
 $rg = mysql_fetch_row($rs);  	
 $nomcco = $rg[0]; 
 
 // TOMO EL NOMBRE DEL EXAMEN
 $sql = "SELECT nombre FROM root_000012 Where codigo='".$registro["pafexa"]."'"; 
 $rs=mysql_query($sql); 
 $rg = mysql_fetch_row($rs);  	
 $nomexa = $rg[0]; 
 
 if (empty($registro["paffci"]) or  $registro["paffci"]=="0000-00-00") 
 { 
  /* PARA RESTAR FECHAS => LLEVO A SEGUNDOS Y LUEGO A DIAS  */
  $segundos=strtotime('now') - strtotime($registro["paffre"]) ;
  $diferencia_dias=intval($segundos/60/60/24);
  
  if ( $diferencia_dias > 14 )    // Con color rojo
  {
   ?> 
   <!-- tabla de resultados --> 
   <!-- Si utilizo la sigiente linea definida asi, cada que den clic sobre la tabla muestra unos datos que yo defina con la fn 'muestra'   
    <tr bgcolor="#DDDDDD" onMouseOver="this.style.backgroundColor='#0099CC';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#DDDDDD';" onClick="javascript:muestra('<?php echo "Para Consultar por Unidad o Examen digite el codigo como criterio."; ?>');" > 
    -->   
<tr><td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["paffre"]; 

    if ($registro["pafser"]=="4")	
      echo "</b></font></td> <td><font size='2' face='Verdana, Arial, Helvetica, sans-serif' color='#9370DB'><b>".$registro["pafced"]."</b></font></td>";   
    else
	  echo "</b></font></td> <td><font size='2' face='Verdana, Arial, Helvetica, sans-serif' color='#FF0000'><b>".$registro["pafced"]."</b></font></td>";   
    ?>  
 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["pafap1"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["pafnom"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["pafcco"]."-".$nomcco; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["pafser"]; ?></b></font></td>
	<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["pafexa"]."-".$nomexa; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF0000"><b><?php echo $registro["id"]; ?></b></font></td>
    <?php	  
  }	  
  else    // No tiene cita con Color Amarillo
  {
   ?> 
   <!-- tabla de resultados --> 
   <!-- Si utilizo la sigiente linea definida asi, cada que den clic sobre la tabla muestra unos datos que yo defina con la fn 'muestra'   
    <tr bgcolor="#DDDDDD" onMouseOver="this.style.backgroundColor='#0099CC';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#DDDDDD';" onClick="javascript:muestra('<?php echo "Para Consultar por Unidad o Examen digite el codigo como criterio."; ?>');"> 
    --> 
<tr><td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["paffre"]; 
    if ($registro["pafser"]=="4")	
      echo "</b></font></td> <td><font size='2' face='Verdana, Arial, Helvetica, sans-serif' color='#9370DB'><b>".$registro["pafced"]."</b></font></td>";   
    else
	  echo "</b></font></td> <td><font size='2' face='Verdana, Arial, Helvetica, sans-serif' color='#FF9900'><b>".$registro["pafced"]."</b></font></td>";   
    ?> 

    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["pafap1"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["pafnom"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["pafcco"]."-".$nomcco; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["pafser"]; ?></b></font></td>
	<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["pafexa"]."-".$nomexa; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FF9900"><b><?php echo $registro["id"]; ?></b></font></td>
    <?php
   }	
 }
 else   // Ya tienen cita Color azul oscuro
 { 
   ?> 
   <!-- tabla de resultados --> 
   <!-- Si utilizo la sigiente linea definida asi, cada que den clic sobre la tabla muestra unos datos que yo defina con la fn 'muestra'  
    <tr bgcolor="#DDDDDD" o onMouseOver="this.style.backgroundColor='#0099CC';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#DDDDDD';" onClick="javascript:muestra('<?php echo "Para Consultar por Unidad o Examen digite el codigo como criterio."; ?>');"> 
   --> 
<tr><td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["paffre"]; 
    if ($registro["pafser"]=="4")	
      echo "</b></font></td> <td><font size='2' face='Verdana, Arial, Helvetica, sans-serif' color='#9370DB'><b>".$registro["pafced"]."</b></font></td>";   
    else
	  echo "</b></font></td> <td><font size='2' face='Verdana, Arial, Helvetica, sans-serif' color='#003366'><b>".$registro["pafced"]."</b></font></td>";   
    ?> 

    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["pafap1"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["pafnom"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["pafcco"]."-".$nomcco; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["pafser"]; ?></b></font></td>
	<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["pafexa"]."-".$nomexa; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["id"]; ?></b></font></td>
    <?php 
 }
     if ($registro["pafest"] == "A") 
     {    
	   $wid=$registro[6];
       echo "<td><A HREF='paf01total.php?wid=".$wid."&windicador=PrimeraVez&wproceso=Modificar' TARGET='_blank' >Editar</A></td>";

	   // Se obtiene el código del usuario
       $pos = strpos($user,"-");
       $wusuario = substr($user,$pos+1,strlen($user)); 	  
       $query = "SELECT surapri FROM pafsura_000099 Where surausr='".$wusuario."'";
       $resultado = mysql_query($query);
       $registro = mysql_fetch_row($resultado);  
       if ($registro[0]=="1")    // Si prioridad es 1 permito anular
        echo "<td><A HREF='paf10total.php?wid=".$wid."' TARGET='_blank' >Anular</A></td>";	     	  
	 } 
     else
     {
      echo "<th bgcolor='#CCCCCC'>Anulada</th>"; 
      echo "<th bgcolor='#CCCCCC'></th>"; 
     } 
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
       echo "<a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina-1)."&orden=".$orden."&criterio=".$txt_criterio."&wopcion=".$wopcion."&wopcion2=".$wopcion2."&wopcion3=".$wopcion3."'>"; 
       echo "<font face='verdana' size='-2'>anterior</font>"; 
       echo "</a> "; 
    } 

    for($i=$inicio;$i<=$final;$i++) 
    { 
       if($i==$pagina) 
       { 
          echo "<font face='verdana' size='-2'><b>".$i."</b> </font>"; 
       }else{ 
          echo "<a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".$i."&orden=".$orden."&criterio=".$txt_criterio."&wopcion=".$wopcion."&wopcion2=".$wopcion2."&wopcion3=".$wopcion3."'>"; 
          echo "<font face='verdana' size='-2'>".$i."</font></a> "; 
       } 
    } 
    if($pagina<$numPags) 
   { 
       echo " <a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina+1)."&orden=".$orden."&criterio=".$txt_criterio."&wopcion=".$wopcion."&wopcion2=".$wopcion2."&wopcion3=".$wopcion3."'>"; 
       echo "<font face='verdana' size='-2'>siguiente</font></a>"; 
   }
} 
/*  
 echo "<table border='1'>"; 
 echo "<td><A HREF='datosagraficar.php?wind=1' TARGET='_blank' >Ordenes por Dx <IMG SRC='Icon_05.ico' ALT='Genera Grafico de Ordenes por Dx'></A></td>";	     
 echo "<td><A HREF='datosagraficar.php?wind=2' TARGET='_blank' >Ordenes por Examen <IMG SRC='Icon_05.ico' ALT='Genera Grafico de Ordenes por Examen'></A></td>";	     
 echo "<td><A HREF='datosagraficar.php?wind=3' TARGET='_blank' >Ordenes por Servicio <IMG SRC='Icon_05.ico' ALT='Genera Grafico de Ordenes por Servicio'></A></td>";	     
 echo "</table>";
 */
 
//////////fin de la paginacion 
?> 
  </td></tr> 
  </table> 

<hr noshade style="color:CC6666;height:1px"> 
<div align="center"><font face="verdana" size="-2"><a class="p" href="paf00total.php">::Inicio::</a></font></div> 
<form action="paf00total.php" method="get"> 
<center>
<table border="0" cellspacing="0" cellpadding="0" align="center">    
<?php
    echo "Solo Ordenes SIN Asignar: ";
   if ( $wopcion == "on" )
     echo "<INPUT TYPE='checkbox' NAME='wopcion' CHECKED  ></INPUT>"; 
   else
     echo "<INPUT TYPE='checkbox' NAME='wopcion' ></INPUT>"; 
 
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Solo Ordenes Prioritarias: ";
   if ( $wopcion2 == "on" )
     echo "<INPUT TYPE='checkbox' NAME='wopcion2' CHECKED  ></INPUT>"; 
   else
     echo "<INPUT TYPE='checkbox' NAME='wopcion2' ></INPUT>"; 
 
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Con Mas de 3 Llamadas: ";
   if ( $wopcion3 == "on" )
     echo "<INPUT TYPE='checkbox' NAME='wopcion3' CHECKED  ></INPUT>"; 
   else
     echo "<INPUT TYPE='checkbox' NAME='wopcion3' ></INPUT>"; 
 
?>	      
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Criterio de búsqueda: 
<input type="text" name="criterio" size="30" maxlength="150" > 
<input type="submit" value="Buscar"> 
<font color="#003366" size="-2" face="Verdana, Arial, Helvetica, sans-serif">  Unidad y Examen se buscan por codigo
</table> 
</center>
</form> 

</body> 
</html> 
<?php 
 mysql_close(); 
// Para que refresque cada  15 minutos (15*60 segundos) con los mismos datos seleccionados
echo "<meta http-equiv='refresh' content='900;url=paf00total.php'>";
?> 
