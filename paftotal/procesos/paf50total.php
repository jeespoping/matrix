<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>Sistema de Informacion Programa CardioVascular SALUD TOTAL</title> 
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

 <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
	
<script language="JavaScript"> 
function muestra(queCosa) 
{ 
    alert(queCosa); 
} 
</script> 
<div align="center"><strong>
<font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Control de atenciones por Profesional
<br><td colspan=1 align=center><A HREF='paf51total.php?windicador=PrimeraVez&wproceso=Nuevo' TARGET='_blank' >Adicionar nuevo Registro</A></td>
</font></strong> 
</div> 

<hr noshade style="color:CC6666;height:1px"> 
<?php
include_once("conex.php"); 
/*****************************************************************
   Modificacion 27-10-2015  
     Se introdujo $_GET porque $HTTP_GET_VARS    quedó obsoleto  
     Se introdujo $_SERVER que $HTTP_SERVER_VARS quedó obsoleto  
 ****************************************************************
 * Escribe en el programa el autor y la version del Script
 * No recibe ningun parametro
 */
function pintarVersion()
{
	$wautor="Jair Saldarriaga O.";
	$wversion="2016-06-21";
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

// $user = "01-07012";        // PARA PRUEBAS LOCALES		
		


mysql_select_db("matrix") or die("No se selecciono la base de datos");    
//Conexion a Informix Creada en el "DSN de Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  


//inicializo el criterio y recibo cualquier cadena que se desee buscar 
$criterio = ""; 
$txt_criterio = ""; 
if (isset($_GET["criterio"]))
{
 if ($_GET["criterio"]!="")
 { 
   $txt_criterio = $_GET["criterio"]; 
     $criterio = " where (cedula like '%" . trim($txt_criterio) . "%' or Nom_Pac like '%" . trim($txt_criterio) . "%' or Nit_res like '%"
	             . trim($txt_criterio) ."%' or Cod_equ like '%" . trim($txt_criterio) . "%' or Id = '" . trim($txt_criterio) . "') "; 
 }
}  

// El criterio2 es la Fecha Seleccionada

if (!isset($wfec))    // Si no esta seteada entonces la inicializo con la fecha actual, 
   $wfec=date("Y-m-d");

if ( $criterio !="" )
  $criterio2=" And Fecha = '".$wfec."' And Activo='A' ";
else
  $criterio2=" Where Fecha = '".$wfec."' And Activo='A' ";

$sql="SELECT Fecha,Hi,Cedula,Nom_Pac,Nit_res,Cod_equ,Usuario FROM citapaft_000009 ".$criterio.$criterio2;
 
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
       $orden="Fecha,Hi "; 
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

$sql="SELECT Fecha,Hi,Cedula,Nom_Pac,Nit_res,Cod_equ,Usuario FROM citapaft_000009 ".$criterio.$criterio2
    ." ORDER BY ".$orden." LIMIT ".$limitInf.",".$tamPag;  

$res=mysql_query($sql); 
//////////fin consulta con limites 
echo "<div align='center'>"; 
echo "<font face='verdana' size='-2'>Resultados encontrados por criterio de busqueda: ".$numeroRegistros."<br>";
echo "Ordenados por <b>".$orden."</b>"; 
if(isset($txt_criterio)){ 
    echo "<br>Criterio de busqueda o filtro: <b>".$txt_criterio."</b>"; 
} 
echo "</font></div>"; 
echo "<table align='center' width='80%' border='1' cellspacing='1' cellpadding='0'>"; 

//echo "<tr><td colspan='6'><hr noshade></td></tr>";   // Hace una raya para colspan='6' 
echo "<hr noshade style='color:CC6666;height:1px'>";   // hace una raya del tamaño de la pantalla
  
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Id&criterio=".$txt_criterio."'>Historia</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Fecha&criterio=".$txt_criterio."'>Fecha</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Hi&criterio=".$txt_criterio."'>Hora</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Cedula&criterio=".$txt_criterio."'>Documento</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Nom_Pac&criterio=".$txt_criterio."'>Nombre del paciente</a></th>"; 
echo "<th bgcolor='#CC6666'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Nit_res&criterio=".$txt_criterio."'>Responsable</a></th>"; 
echo "<th bgcolor='#CC6666'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Cod_equ&criterio=".$txt_criterio."'>Profesional que atiende</a></th>"; 
echo "<th bgcolor='#CCCCCC'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=Usuario&criterio=".$txt_criterio."'>Usuario</a></th>"; 
echo "<th bgcolor='#CCCCCC'></th>"; 
echo "<th bgcolor='#CCCCCC'></th>"; 

while($registro=mysql_fetch_array($res)) 
{ 

 // TOMO EL NOMBRE DEL RESPONSABLE
 $sql = "SELECT Descripcion FROM citapaft_000002 WHERE Nit='".$registro["Nit_res"]."'"; 
 $rs=mysql_query($sql); 
 $rg = mysql_fetch_row($rs);  	
 $nomnit = $rg[0]; 
 
 // TOMO EL NOMBRE DEL MEDICO
 $sql = "SELECT Descripcion FROM citapaft_000010 WHERE codigo='".$registro["Cod_equ"]."' Group By Descripcion"; 
 $rs=mysql_query($sql); 
 $rg = mysql_fetch_row($rs);  	
 $nommed = $rg[0]; 
 
 // Con el documento busco el Nro de Historia  en UNIX 	  
  $query=" Select pachis From INPACI Where pacced='".$registro["Cedula"]."'"
        ." UNION ALL " 
        ." Select pachis From INPAC Where pacced='".$registro["Cedula"]."'";
   $resultado2 = odbc_do($conexN,$query) or die("No se encontraron registros");           
   $registro2 = odbc_fetch_row($resultado2);    
   $nrohis = odbc_result($resultado2,1);   
 
   ?> 
   <!-- tabla de resultados --> 
   <!-- Si utilizo la sigiente linea definida asi, cada que den clic sobre la tabla muestra unos datos que yo defina con la fn 'muestra'   
    <tr bgcolor="#DDDDDD" onMouseOver="this.style.backgroundColor='#0099CC';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#DDDDDD';" onClick="javascript:muestra('<?php echo "Para Consultar por Unidad o Examen digite el codigo como criterio."; ?>');" > 
    -->   
	
<tr><td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $nrohis; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Fecha"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Hi"]; ?></b></font></td> 
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Cedula"]; ?></b></font></td>
	<td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Nom_Pac"]; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Nit_res"]."-".$nomnit; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Cod_equ"]."-".$nommed; ?></b></font></td>
    <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#003366"><b><?php echo $registro["Usuario"]; ?></b></font></td>
    <?php	  
 
    $whor=substr($registro["Hi"],0,2).":".substr($registro["Hi"],2,2);   // En citas la hora esta sin formato, Le doy formato  hh:mm y la envio	
    echo "<td><A HREF='paf51total.php?whis=".$nrohis."&wfec=".$registro["Fecha"]."&whor=".$whor."&wced=".$registro["Cedula"]."&wmed=".$registro["Cod_equ"]."&wnom=".$registro["Nom_Pac"]."&windicador=PrimeraVez&wproceso=Modificar' TARGET='_blank' >Editar</A></td>";	     
     
    ?>
    </tr> 
   <!-- fin tabla resultados --> 
<?php 
}//fin while 
echo "</table>"; 
}//fin if 
//////////a partir de aqui viene la paginacion [ Y EN LA PAGINACION TAMBIEN ENVIO LA FECHA ]
?> 
    <br> 
    <table border="0" cellspacing="0" cellpadding="0" align="center"> 
    <tr><td align="center" valign="top"> 
<?php 
if (isset($pagina))
{
    if($pagina>1) 
    { 
       echo "<a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina-1)."&orden=".$orden."&criterio=".$txt_criterio."&wfec=".$wfec."'>"; 
       echo "<font face='verdana' size='-2'>anterior</font>"; 
       echo "</a> "; 
    } 

    for($i=$inicio;$i<=$final;$i++) 
    { 
       if($i==$pagina) 
       { 
          echo "<font face='verdana' size='-2'><b>".$i."</b> </font>"; 
       }else{ 
          echo "<a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".$i."&orden=".$orden."&criterio=".$txt_criterio."&wfec=".$wfec."'>"; 
          echo "<font face='verdana' size='-2'>".$i."</font></a> "; 
       } 
    } 
    if($pagina<$numPags) 
   { 
       echo " <a class='p' href='".$_SERVER["PHP_SELF"]."?pagina=".($pagina+1)."&orden=".$orden."&criterio=".$txt_criterio."&wfec=".$wfec."'>"; 
       echo "<font face='verdana' size='-2'>siguiente</font></a>"; 
   }
} 
 
//////////fin de la paginacion 
?> 
  </td></tr> 
  </table> 

<hr noshade style="color:CC6666;height:1px"> 
<div align="center"><font face="verdana" size="-2"><a class="p" href="paf50total.php">::Inicio::</a></font></div> 

<form action="paf50total.php" method="get"> 
<center>
<table border="0" cellspacing="0" cellpadding="0" align="center">         

<?php
  if (!isset($wfec))    // Si no esta seteada entonces la inicializo con la fecha actual, OJO si va a copiar este campo cambie el trigger1 por triggerx
    $wfec=date("Y-m-d");
	
//	 $wfec="2015-10-26";
    
    echo "<tr><font text color=#CC6666 size=3><b>Fecha a procesar: ";
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' Readonly value=".$wfec." class=tipo3><button id='trigger1'  onclick=".$cal.">...</button>";
?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
<tr>
Criterio de búsqueda: 
<input type="text" name="criterio" size="30" maxlength="150" > 
<input type="submit" value="Buscar"> 
<font color="#003366" size="-2" face="Verdana, Arial, Helvetica, sans-serif">  Responsable y Medico se buscan por codigo

</table> 
</center>
</form> 

</body> 
</html> 
<?php 
 mysql_close(); 
?> 
