<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"> 
<html> 
<head> 
<title>Actualizacion Control de Entrega de cuentas de cobro</title> 

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

<div align="center"><strong><font color="#000000" size="2" face="Verdana, Arial, Helvetica, sans-serif">Actualizacion Control de Entrega de cuentas de cobro
 <br><td colspan=1 align=center></td><br><A HREF='ameenv01.php?windicador=PrimeraVez&wproceso=Grabar' TARGET='_blank' >Adicionar nuevo registro</A> </font></strong>
</div> 



<?php
include_once("conex.php"); 
/*****************************************************************
   Modificacion 04-04-2012  
     Se introdujo $_GET porque $HTTP_GET_VARS    quedó obsoleto  
     Se introdujo $_SERVER que $HTTP_SERVER_VARS quedó obsoleto  
 ****************************************************************/
 
function checkDateTime($data) 
{
//Chequea si un campo es tipo fecha	
    if (date('Y-m-d', strtotime($data)) == $data) {
        return true;
    } else {
        return false;
    }
} 
 
function pintarVersion()
{
//Escribe en el programa el autor y la version del Script
//No recibe ningun parametro	
	$wautor="Jair Saldarriaga O.";
	$wversion="2013-02-13";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	//echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table><br>";
	 
}

pintarVersion(); //Escribe en el programa el autor y la version del Script.

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");



mysql_select_db("matrix") or die("No se selecciono la base de datos");  
$conex = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");
  

//inicializo el criterio y recibo cualquier cadena que se desee buscar 
//AQUI SE CAMBIAN LOS NOMBRES DE LOS CAMPOS DE BUSQUEDA SEGUN LA TABLA A PAGINAR
$criterio = ""; 
$txt_criterio = ""; 
if (isset($_GET["criterio"]))
{
 if ($_GET["criterio"]!="")
 { 
   $txt_criterio = $_GET["criterio"]; 
   if ( is_numeric($txt_criterio) )
    $criterio = " AND ( envahnro = " . $txt_criterio . " or envahent = " . $txt_criterio . " or envahrec = " . $txt_criterio . ") "; 
   else 
   if ( checkDateTime($txt_criterio) )
    $criterio = " AND ( envahfec = '" . $txt_criterio . "')";
   else
    $criterio = " AND ( envahent like '%" . $txt_criterio . "%' or envahdes like '%" . $txt_criterio . "%' or envahrec like '%" . $txt_criterio . "%') "; 
  }
}

// COMO EL odbc_num_rows NO FUNCIONA SIEMPRE HAY QUE HACER UN QUERY PARA SABER CUANTOS REGISTROS
// TOTALES DEVUELVE Y SE SE VAN A MOSTRAR SEGUN EL CRITERIO DE BUSQUEDA
$sql = "SELECT count(*) as Contador FROM ameenvah WHERE envahnro > 0 ".$criterio." ";
$res = odbc_exec( $conex, $sql );
if ( odbc_fetch_into($res, $dato)) 
  $numeroRegistros = trim( $dato[0] );
else 
  exit( "Error en el volcado de datos" );

if($numeroRegistros<=0) 
{ 
    echo "<div align='center'>"; 
    echo "<font face='verdana' size='-2'>No se encontraron resultados</font>"; 
    echo "</div>"; 
}
else
{ 
    //////////elementos para el orden 
    if(!isset($orden)) 
       $orden="envahnro"; 
    //////////fin elementos de orden 

 //////////calculo de elementos necesarios para paginacion /////////////////
    
    //tamaño de la pagina 
    $tamPag=20; 

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
    $limitInf=(($pagina-1)*$tamPag); 

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
  ////////// Fin de dicho calculo ////////////////////


////////// CREACION DE LA CONSULTA CON LIMITES y CON LOS CAMPOS QUE SE DESEAN VER EN EL ENCABEZADO DE LA TABLA HTML

// Ojo tenga encuenta que el programa muestra en cada pagina como primer registro el ultimo de la pagina anterior 
//     por estar el query asi:  rowid Between $limitInf And $limitInf+$tamPag
 
/* EL HECHO DE EN INFORMIX NO MANEJAR limit O first AQUI TOCA PAGINAR CON rowid POR LO QUE SI DOY
   UN ORDER EL LO HACE SOBRE LOS REGISTROS ABARCADOS EN LA PAGINA OSEA $limitInf And $limitInf+$tamPag
   LO MISMO PASA CON LAS BUSQUEDAS BUSCA SOBRE LOS REGISTROS QUE ESTAN EN LA PAGINA.
   
   ASI QUE TOCO UTILIZAR UNA TABLA TEMPORAL ASI: 
*/   
 
$sql="Select envahnro ENVIO,envahfec FEC_SELLO,envahent ENTREGA,envahDES DESTINO,envahrec RECIBE"; 
$sql=$sql." From ameenvah WHERE envahnro > 0 ".$criterio." Order by $orden DESC INTO TEMP tmp";   // El order by va aqui para que el rowid de tabla temporal
$resultadoB = odbc_exec( $conex, $sql );                                                          // quedara con ese orden      

$sql="Select ENVIO,FEC_SELLO,ENTREGA,DESTINO,RECIBE"; 
$sql=$sql." From tmp WHERE rowid Between $limitInf And $limitInf+$tamPag ";                       // Aqui ya no necesitare dar order by 

$res = odbc_exec( $conex, $sql );
$numCampos = odbc_num_fields($res);
echo "<Table align='center'>"; 
echo "<font face='verdana' size='-2'>";
echo "<br>Total registros: <b>".$numeroRegistros."</b>"; 
echo "<br>Ordenados por <b>$orden</b>"; 
if(isset($txt_criterio))
  echo "<br>Criterio de busqueda o filtro: <b>".$txt_criterio."</b>"; 
echo "</font></table>"; 

echo "<table align='center' width='80%' border='0' cellspacing='1' cellpadding='0'>"; 
echo "<tr><td colspan='6'><hr noshade></td></tr>"; 
echo "<th bgcolor='silver'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=".odbc_field_name( $res,1)."&criterio=".$txt_criterio."'>".odbc_field_name( $res,1)."</a></th>"; 
echo "<th bgcolor='silver'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=".odbc_field_name( $res,2)."&criterio=".$txt_criterio."'>".odbc_field_name( $res,2)."</a></th>"; 
echo "<th bgcolor='#DDDDDD'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=".odbc_field_name( $res,3)."&criterio=".$txt_criterio."'>".odbc_field_name( $res,3)."</a></th>"; 
echo "<th bgcolor='silver'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=".odbc_field_name( $res,4)."&criterio=".$txt_criterio."'>".odbc_field_name( $res,4)."</a></th>"; 
echo "<th bgcolor='#DDDDDD'><a class='ord' href='".$_SERVER["PHP_SELF"]."?pagina = ".$pagina."&orden=".odbc_field_name( $res,5)."&criterio=".$txt_criterio."'>".odbc_field_name( $res,5)."</a></th>"; 
echo "<th bgcolor='#DDDDDD'></th>"; 

//  tabla de resultados 
while(odbc_fetch_row($res)) 
{ 
?> 
    <!-- Si quiero que al dar click muestre el mensaje con los datos, la linea queda asi
    <tr bgcolor="#CC6666" onMouseOver="this.style.backgroundColor='#FF9900';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='#CC6666'"o"];" onClick="javascript:muestra('<?php echo "[".odbc_result( $res, 1 )."] ".$i." - ".odbc_result( $res, 3 ); ?>');"> 
    -->
   <tr bgcolor="silver" onMouseOver="this.style.backgroundColor='#FF9900';this.style.cursor='hand';" onMouseOut="this.style.backgroundColor='silver'"o"];" > 
   
<?php      for($i=1; $i <= $numCampos; $i++)
        {
	     if (($i==3) or ($i==5) )
	     {
		   $sql="Select usuahnom from ameusuah Where usuahcod='".odbc_result( $res, $i )."'";
		   $resultadoB = odbc_exec( $conex, $sql );
		   if (odbc_fetch_row($resultadoB))         // Encontro 
            echo "<td nowrap>".odbc_result( $resultadoB, 1)."</td>";      
	       else
	        echo "<td nowrap></td>";      
         }
         else
           echo "<td nowrap>".odbc_result( $res, $i )."</td>";
        }
        
        $query="Select envahrad From ameenvah Where envahnro=".odbc_result( $res, 1 );
        $resultadoC = odbc_do($conex,$query);    // Ejecuto el query  
        if (odbc_result( $resultadoC, 1 ) == "P")
		{
         echo "<td><A HREF='ameenv01.php?wid=".number_format(odbc_result( $res, 1 ),0,'', '')."&windicador=PrimeraVez&wproceso=Modificar'  target='_blank'>Editar</A></td>";	     
         echo "<td><A HREF='ameenv01.php?wid=".number_format(odbc_result( $res, 1 ),0,'', '')."&windicador=PrimeraVez&wproceso=Borrar'  target='_blank'>Borrar</A></td>";	     
		} 
        else 
         echo "<td nowrap>Ok</td>";     

     
     echo "</tr>"; 
// fin tabla resultados 

}//fin while 


echo "</table>"; 
} //fin if de encontro registros 

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


<div align="center"><font face="verdana" size="-2"><a class="p" href="ameenv00.php">::Inicio::</a></font></div> 
<form action="ameenv00.php" method="get"> 

<table border="0" cellspacing="0" cellpadding="0" align="center">         
Criterio de busqueda: 
<input type="text" name="criterio" size="22" maxlength="150"> 
<input type="submit" value="Buscar/Refrescar"> 
</table> 

</form> 

</body> 
</html> 
<?php 
//odbc_close($conex); 
odbc_close($conex);
odbc_close_all();   
?> 
