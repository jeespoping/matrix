<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

echo "<HTML>";
echo "<HEAD>";
echo "<TITLE>CLINICA LAS AMERICAS</TITLE>";
echo "</HEAD>";
echo "<BODY>";



mysql_select_db("matrix") or die("No se selecciono la base de datos");   
$conexN = odbc_connect('inventarios','','') or die("No se realizo Conexion con la BD suministros en Informix");

echo "<center><table border=0>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>DETALLE DE PRECIOS POR PROVEEDOR</font></b><br>";
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>PROGRAMA: cotizaciones31.php Ver. 2013/02/28<br>AUTOR: JairS</font></b><br>";
$fecha = date("Y-m-d");
$hora = (string)date("H:i:s");	     
echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Fecha-Hora: ".$fecha." - ".$hora."</font></b><br>";
echo "</table>";

         $query = "SELECT descripcion FROM cotizaci_000005, usuarios"
                 ." WHERE usunit = '".$wcod."'" 
                 ." AND usucod = codigo";     
         $resultado2 = mysql_query($query);
         $nroreg2 = mysql_num_rows($resultado2);
         if ($nroreg2 > 0)
         {
	      $registro2 = mysql_fetch_row($resultado2);  
	      $wpro = $registro2[0];
         }
	     else	    
          $wpro = "";
 
    // PARA EVITAR NULOS  busco si hay compras en el periodo
  		  $query="Select Count(*) As Total"
                ." From ivmov,ivmovdet"
                 ." Where movfue='06' and movano='".$wano."'"
                 ." And movmes between '01' and '12'"
                 ." And movcon='001'"
                 ." And movnit='".$wcod."'"
                 ." And movfue=movdetfue"
                 ." And movdoc=movdetdoc AND movanu='0' ";
          $resultadoB = odbc_exec($conexN,$query);
	      $totalper=odbc_result($resultadoB,1);
	      
  If  ($totalper>0)
  {               
    // Busco el total de las compras en periodo
  		  $query="Select SUM(movdettot+movdetiva)"
                ." From ivmov,ivmovdet"
                 ." Where movfue='06' and movano='".$wano."'"
                 ." And movmes between '01' and '12'"
                 ." And movcon='001'"
                 ." And movnit='".$wcod."'"
                 ." And movfue=movdetfue"
                 ." And movdoc=movdetdoc AND movanu='0' ";
         
          $resultadoB = odbc_exec($conexN,$query);
		  if (odbc_fetch_row($resultadoB))            // Encontro 
		    $wtot=odbc_result($resultadoB,1);
		  else
		    $wtot=0;       
   }
   else
    $wtot=0;
 
    // PARA EVITAR NULOS  busco si hay compras en el periodo Anterior
  		  $query="Select Count(*) As Total"
                ." From ivmov,ivmovdet"
                 ." Where movfue='06' and movano='".($wano-1)."'"
                 ." And movmes between '01' and '12'"
                 ." And movcon='001'"
                 ." And movnit='".$wcod."'"
                 ." And movfue=movdetfue"
                 ." And movdoc=movdetdoc AND movanu='0' ";
          $resultadoB = odbc_exec($conexN,$query);
	      $totalant=odbc_result($resultadoB,1);
	      
  If  ($totalant>0)
  {               
  // Busco el total de las compras en el periodo Anterior
  		  $query="Select SUM(movdettot+movdetiva)"
                ." From ivmov,ivmovdet"
                 ." Where movfue='06' and movano='".($wano-1)."'"
                 ." And movmes between '01' and '12'"
                 ." And movcon='001'"
                 ." And movnit='".$wcod."'"
                 ." And movfue=movdetfue"
                 ." And movdoc=movdetdoc AND movanu='0' ";
         
          $resultadoB = odbc_exec($conexN,$query);
		  if (odbc_fetch_row($resultadoB))              // Encontro 
		    $wtotant=odbc_result($resultadoB,1);
		  else
		    $wtotant=0;                  
  }
  else
   $wtotant=0;  
   
//for($j=1;$j<=3;$j++)              // ESTE REPORTE SE EJECUTABA 3 VECES POR PROVEEDOR 1:Medicamentos 2:Materiales 3:Antisepticos 
//{                                  // PERO ESTABA MARCANDO ERROR EN ALGUNOS PROVEEDORES QUE EXEDE EL MAXIMO TIEMPO DE EJECUCION ==> Toco de uno

  if ($wtipo=='1')    // Medicamentos
  {
   $tabla="cotizaci_000003";
   $titulo="MEDICAMENTOS";
  }
  else
   if ($wtipo=='2')    // Materiales
   {
    $tabla="cotizaci_000004";
    $titulo="MATERIALES";
   }
   else      // Antisepticos
   {
    $tabla="cotizaci_000007";
    $titulo="ANTISEPTICOS";
   }


// Busco si el proveedor cotizo medicamentos en este periodo            
$query="SELECT cotcot,cotmar,cotpre"
       ." FROM ".$tabla
       ." WHERE cotano='".$wano."' AND cotnit = '".$wcod."'";
$resultadoB = mysql_query($query);
$nroreg = mysql_num_rows($resultadoB);

if ( ($nroreg > 0) And ($wtotant>0) And ($totalper>0) )       
{          
  echo "<center>";  
  echo "<font text color=#CC0000 size=4><A HREF='cotizaciones08.php?wnit=".$wcod."' TARGET='_New'>".$wcod." - ".$wpro."</A></td>";
  
//  echo "<b><font text color=#CC0000 size=4> <i>Proveedor: ".$wcod." - ".$wpro."</font></b><br>";
  echo "</center>";
  echo "<center><table border=0>";
  echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=4> <i>ANALISIS DE ".$titulo." COTIZADOS PERIODO ".$wano."</font></b><br>";
  echo "</table>";
 
  echo "<br>";
  echo "<table border=0>";
  echo "<tr>";

  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Linea<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Codigo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Articulo<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>unidad<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Valor<br>Cotizado<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Precio Ult.<br>Compra<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Incremento<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Cantidad<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Valor<br>Unitario<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Valor<br>Total<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Valor<br>Neto<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Ponderacion<br>Aumento<b></td>";
  echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Valor<br>Incremento<b></td>";
  echo "</tr>"; 
  
  // Selecciono todas compras de articulos a este proveedor en el periodo
        $query="Select movdetart,artnom,movdetuni,sum(movdetcan) can,sum(movdettot) tot,sum(movdetiva) iva "
       ." From ivmov,ivmovdet,ivart "
       ." where movfue='06' and movano='".$wano."' "
       ." And movmes between '01' and '12' "
       ." And movcon = '001' "
       ." And movnit = '".$wcod."' "
       ." And movfue=movdetfue"
       ." And movdoc=movdetdoc"
       ." And movdetart=artcod"
       ." AND movanu='0'"
       ." Group by 1,2,3"
  //     ." Order By 1";        
       . " UNION ALL "
  // Selecciono todas las devoluciones *(-1) de articulos a este proveedor en el periodo
       ."Select movdetart,artnom,movdetuni,sum(movdetcan) can,sum(movdettot*(-1)) tot,0 iva"
       ." From ivmov,ivmovdet,ivart "
       ." where movfue='07' and movano='".$wano."' "
       ." And movmes between '01' and '12' "
  //   ." And movcon = '111' "
       ." And movnit = '".$wcod."' "
       ." And movfue=movdetfue"
       ." And movdoc=movdetdoc"
       ." And movdetart=artcod"
       ." AND movanu='0'"
       ." Group by 1,2,3"
       ." INTO TEMP tmp".$j;
       $resultado = odbc_do($conexN,$query);            // Ejecuto el query 
        
       $query="Select movdetart,artnom,movdetuni,sum(can),sum(tot),sum(iva) "
             ." From tmp".$j
             ." Group by 1,2,3"      
             ." Order By 1";               
           
       $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
       

		   
		$tvlrvlr=0;
		$tvlrnet=0;
		$tvlrpon=0;
		$tvlraum=0;   

        $i = 1;
		while( odbc_fetch_row( $resultado ))
		{			   	    
				     
		  // color de fondo  
	      if (is_int ($i/2))  // Cuando la variable $k es par coloca este color
	       $wcf="DDDDDD";  
	   	  else
	   	   $wcf="CCFFFF";    	
	   	    
	   	  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".$i."</td>";                           // Nro de Linea 
		  echo "<td colspan=2 align=center bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result( $resultado, 1 )."</td>"; // Codigo
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result( $resultado, 2 )."</td>";   // Nombre
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result( $resultado, 3 )."</td>";   // Unidad
		  
		  $query="SELECT cotcot,cotmar,cotpre,conuni,conmes,conano"
                ." FROM ".$tabla.", cotizaci_000001"
                ." WHERE cotcod='".odbc_result( $resultado, 1 )."' AND cotano='".$wano."' AND cotcod = concod AND cotnit = '".$wcod."'"
                ." GROUP  BY cotcot, cotmar, cotpre, conuni, conmes, conano ";          
          $resultadoB = mysql_query($query);
          $nroreg = mysql_num_rows($resultadoB);
          if ($nroreg > 0)
          { $registro = mysql_fetch_row($resultadoB);
            $wvlr=$registro[0]; 
	      }
	      else
		    $wvlr=0;
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format($wvlr,2)."</td>";       // Vlr Cotizado
	      
		  
	      // Busco el ultimo precio con el que le compre al proveedor el articulo 
	      $query="Select movfec,movdetpre"
                 ." From ivmov,ivmovdet"
                 ." Where movfue='06' and movano='".$wano."'"
                 ." And movmes between '01' and '12'"
                 ." And movcon='001'"
                 ." And movnit='".$wcod."'"
                 ." And movfue=movdetfue"
                 ." And movdoc=movdetdoc AND movanu='0' "
                 ." And movdetart='".odbc_result( $resultado, 1 )."'"
                 ." Order By movfec DESC";
                
          $resultadoB = odbc_exec($conexN,$query);
		  if (odbc_fetch_row($resultadoB))         // Encontro 
		  {
	        echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result( $resultadoB, 2 )."</td>"; // Vlr Ultima Compra
	        $wvlr1=( ($wvlr - odbc_result($resultadoB,2) ) / odbc_result($resultadoB,2) )*100;   // Calculo el incremento sobre el ultimo precio de compra
    		echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format($wvlr1,2)."</td>";
          }
	      else
	      { $wvlr1=0;
		    echo "<td colspan=2 align=Left bgcolor=".$wvlr1."></td>";  
		    echo "<td colspan=2 align=Left bgcolor=".$wvlr1."></td>";  
	      }  
		    
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".odbc_result( $resultado,4)."</td>";   // Cantidad
		  
		  $wvlr2=odbc_result( $resultado,5)/odbc_result( $resultado,4);   //Valor unitario
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format($wvlr2,2)."</td>";
		  
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format(odbc_result($resultado,5),2)."</td>";
  		  $tvlrvlr=$tvlrvlr+odbc_result($resultado,5);                   //Valor total

		  $wvlr3=odbc_result( $resultado,5)+odbc_result( $resultado,6);
		  $tvlrnet=$tvlrnet+$wvlr3;                                     //Valor Neto = valor total + Valor iva
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format($wvlr3,2)."</td>";
		  $wvlr4=($wvlr3/$wtot);
		  $tvlrpon=$tvlrpon+$wvlr4;                                     //Ponderacion del aumento
		  echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format($wvlr4,4)."</td>";
		  if ( ($wvlr==0) or ($wvlr1<0) ) 
		    $wvlr5=0;
		  else
	        $wvlr5=($wvlr1/100)*$wvlr3;                             
	      $tvlraum=$tvlraum+$wvlr5;                                    //Valor del aumento
	      echo "<td colspan=2 align=Left bgcolor=".$wcf."><font text color=#003366 size=3>".number_format($wvlr5,2)."</td>";
          echo "</tr>";         
          
          $i++; 
	    }
	    
	    echo "<tr>";
        echo "<td colspan=18 align=center bgcolor=#DDDDDD><b>TOTAL PERIODO:<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($tvlrvlr,2)."<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($tvlrnet,2)."<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($tvlrpon,4)."<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($tvlraum,2)."<b></td>";
	    echo "<tr>";
        echo "<td colspan=14 align=center bgcolor=#DDDDDD><b>TOTAL PERIODO ANTERIOR:<b></td>";
             $wr=(($tvlrnet-$wtotant)/$wtotant)*100;
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($wr,2)."<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b> Porcentaje de Crecimiento<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($wtotant,2)."<b></td>";
        echo "<td colspan=4 align=center bgcolor=#DDDDDD></td>";
        echo "<tr>";
        echo "<td colspan=14 align=center bgcolor=#DDDDDD></td>";
             $wr=(($tvlraum)/$tvlrnet)*100;
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>".number_format($wr,2)."<b></td>";
        echo "<td colspan=2 align=center bgcolor=#DDDDDD><b>Porcentaje de aumento<b></td>";
        echo "<td colspan=8 align=center bgcolor=#DDDDDD></td>";
	    echo "<tr>";
	    
  echo "</table><br><br>";
  
}
else
{
  echo "<center><table border=0>";
  echo "<tr><td align=center bgcolor=#DDDDDD colspan=>";
  echo "<b><font text color='RED' size=4><i>PROVEEDOR NO TIENE ".$titulo." COTIZADOS PARA ESTE PERIODO O SIN COMPRAS EN EL ANTERIOR</font></b><br>";
  echo "</table><br><br>";
} // del if ( ($nroreg > 0) And ($wtotant>0) And ($totalper>0) )      

//} // Del For $j



echo "</HTML>";	
echo "</BODY>";
mysql_close($conex);
odbc_close($conexN);
odbc_close_all();
?>
