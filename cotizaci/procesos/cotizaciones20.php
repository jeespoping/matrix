<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>".
        " index.php</FONT></H1>\n</CENTER>");
       
echo "<HTML>";
echo "<HEAD>";
echo "<TITLE>BIENVENIDA</TITLE>";
echo "</HEAD>";
echo "<BODY>";


 
$conexN = odbc_connect('inventarios','','') or die("No se realizo Conexion con la BD suministros en Informix");
mysql_select_db("matrix") or die("No se selecciono la base de datos");   

echo "<form name='cotizaciones20' action='cotizaciones20.php' method=post>"; 

/**********************/  
//$wemp="cotizaci";
$fecha = date("Y-m-d");  
//$wano=substr($fecha,0,4);   // ASI SERA NORMALMENTE SI SE DEJA COTIZAR HASTA EL 31 DE DIC

$wano=2019;                  //COMO PARA EL 2020 COTIZARAN TODO ENERO TOCO ASI.

//$user="1-07013";               // El codigo asignado para entrar a matrix se deben asociar 
/**********************/         // en la tabla cotizaci_000005 al Codigo del Proveedor


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

if ($wnit <> "")
{        	            	
   echo "<center><table border=0>";
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>ARTICULOS A COTIZAR POR PROVEEDOR</font></b><br>";
   //   echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>".substr($wnit,2,strlen($wnit))." - ".$wnompro."</font></b><br>";
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>".$wnit." - ".$wnompro."</font></b><br>";
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>PROGRAMA: cotizaciones20.php Ver. 2009/10/06 JairS</font></b><br>";
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><A HREF='cotizaciones20.php?wano=".$wano."&wnit=".$wnit."&windicador=PrimeraVez&wproceso=Nuevo'>Adicionar nuevo articulo</A></td>";
   
 if (isset($wproceso))
 {
  if ($wproceso == "Eliminar")
  {
   $query = "Delete From cotizaci_000007 WHERE cotano='".$wano."' and cotnit='".$wnit."' and cotcod='".$wcod."'";
    $resultado1 = mysql_query($query,$conex) or die("ERROR AL ELIMINAR  CODIGO: ".mysql_errno().": ".mysql_error());       
    if (!$resultado1)
	{
	  echo "<table border=1>";	 
	  echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Error, Al Eliminar el registro Nro ".$wid."</td></tr>";
	  echo "</td></tr></table><br><br>";
	}      
  }
 } 
   
   if (isset($wproceso) and $wproceso=="Nuevo")   //muestro todos los consumos del ultimo año
    $query = "SELECT concod,connom,conuni,conmes,conano FROM cotizaci_000008 Order by connom";
   else
   {  	   
	// Muestro lo cotizado por el proveedor el año anterior y los consumos del ultimo año segun el query en 
	// UNIX "consumGral.sql" con el que poblamos la tabla cotizaci_000008 en Matrix   
	
    $wano2= (integer) $wano - 1;    
            
    $query = "SELECT concod,connom,conuni,conmes,conano FROM cotizaci_000007,cotizaci_000008 "
           ."Where (cotano = '".$wano2."' or cotano = '".$wano."') And cotnit = '".$wnit."' "
           ."And cotcod = concod Group By concod,connom,conuni,conmes,conano Order by connom";
   }        
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   $numcam = mysql_num_fields($resultado);      
   
   echo "</table>";
   echo "<br>";
   echo "<table border=0>";
   echo "<tr>";
   echo "<td colspan=2 align=center  bgcolor=#DDDDDD><b>CODIGO<br>CLA<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>DESCRIPCION PRODUCTO<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>UNIDAD<br>MEDIDA<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CONSUMO<br>MES<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CONSUMO<br>ANUAL<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>MARCA<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>PRESENTACION<br>COMERCIAL<b></td>";
   //echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CODIGO DE<br>BARRAS<b></td>";
   //echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>REGISTRO<br>INVIMA</td>";
   //echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>VENCIMIENTO<br>R.INVIMA</td>";
   //echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>CLASIFICACION<br>DE RIESGO</td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>PRECIO<br>ACTUAL<b></td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>PRECIO<br>COTIZADO</td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>PORCENTAJE<br>DE IVA</td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ACTUALIZAR</td>";
   echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>ELIMINAR</td>";
   echo "</tr>"; 

		$i = 1;
		While ($i <= $nroreg)
		{		
	     // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    
	   	  
	   	  $wcc = "DDDDDD";  	
	   	    
		 $registro = mysql_fetch_row($resultado);  			
		 echo "<td colspan=2 align=center bgcolor=".$wcc."><font text color=#003366 size=3>".$registro[0]."</td>";
		 echo "<td colspan=2 align=LEFT   bgcolor=".$wcc."><font text color=#003366 size=3>".$registro[1]."</td>";
		 echo "<td colspan=2 align=center bgcolor=".$wcc."><font text color=#003366 size=3>".$registro[2]."</td>";
		 echo "<td colspan=2 align=center bgcolor=".$wcc."><font text color=#003366 size=3>".$registro[3]."</td>";
		 echo "<td colspan=2 align=center bgcolor=".$wcc."><font text color=#003366 size=3>".$registro[4]."</td>";
		 
         $query = "SELECT cotmar,cotpre,cotbar,cotreg,cotvec,cotcla,cotact,cotcot,cotiva,cotsug,id FROM cotizaci_000007";
         $query = $query." WHERE cotano='".$wano."' and cotnit='".$wnit."' and cotcod='".$registro[0]."'";
         $resultado2 = mysql_query($query);
         $nroreg2 = mysql_num_rows($resultado);
         if ($nroreg2 > 0)
         {
	         $registro2 = mysql_fetch_row($resultado2);  			
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[0]."</td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[1]."</td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[6]."</td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[7]."</td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[8]."</td>";
            // echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[9]."</td>";
            // echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$registro2[10]."</td>";
             $wid=$registro2[10];
        }
        else
        {
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
             echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
            // echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
            // echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3></td>";
             $wid=0;
        }                         		 
		 
	     echo "<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";
	     echo "<A HREF='cotizaciones21.php?wano=".$wano."&wnit=".$wnit."&wcod=".$registro[0]."&windicador=PrimeraVez&wproceso=Actualizar&wid=".$wid."'>Actualizar</A></td>";	     
 
	     echo "<td colspan=2 align=center color=#FFFFFF bgcolor=".$wcf.">";
         echo "<A HREF='cotizaciones20.php?wano=".$wano."&wnit=".$wnit."&wcod=".$registro[0]."&windicador=PrimeraVez&wproceso=Eliminar&wid=".$wid."'>Eliminar</A></td>";	     

         echo "</tr>";
 	              
          $i++; 
	    }		
 //}    

// echo "<a href=$_SERVER[PHP_SELF]>Recargar la Página</a>";
// echo "</body>";
// echo "</html>";

}
else
{
   echo "<center><table border=1>";	 
   echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
   echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, PROCESO DE REGISTRO INCOMPLETO!!!!</MARQUEE></font>";				
   echo "</td></tr></table></center>";	
}

echo "</table>";
echo "</HTML>";	
echo "</BODY>";
//odbc_close($conexN); 
mysql_close($conex);

odbc_close($conexN);
odbc_close_all();
?>
