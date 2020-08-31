<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  

  

  echo "<form action='cuadro_comparativo_costo_ventas.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  
  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>COMPARATIVO COSTO - VENTAS</b></font></td></tr>";
  
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wtar))
   {
	echo "<tr>";  
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10></td>";
    echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
    echo "</tr>";
    
    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	    
	echo "<tr><td align=center bgcolor=".$wcf." >SELECCIONE LA SUCURSAL: ";
	echo "<select name='wcco'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td>";
    
    
    //SELECCIONAR TARIFA
    $q =  " SELECT tarcod, tardes "
		 ."   FROM ".$wbasedato."_000025 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	    
	echo "<td align=center bgcolor=".$wcf." >SELECCIONE LA TARIFA: ";
	echo "<select name='wtar'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td></tr>";
	
    echo "<tr>";
    echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else 
     {
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco); 
	  
	  echo "<input type='HIDDEN' NAME= 'wtar' value='".$wtar."'>";
	  $wtare = explode("-",$wtar); 
	  
	  echo "<tr>";  
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
      echo "</tr>";  
	  
	  /*
      $q="SELECT artcod, artnom, artuni, karexi, mtavan, mtavac, karvuc, karpro, mtavac-karvuc, round((1-(karvuc/mtavac))*100,2) "
	    ."  FROM ".$wbasedato."_000026, ".$wbasedato."_000001, ".$wbasedato."_000007 "
	    ." WHERE MID(mtatar,1,instr(mtatar,'-')-1) = '".$wtare[0]."'"
	    ."   AND MID(mtacco,1,instr(mtacco,'-')-1) = '".$wccoe[0]."'"
	    ."   AND MID(mtaart,1,instr(mtaart,'-')-1) = artcod "
	    ."   AND MID(mtaart,1,instr(mtaart,'-')-1) = karcod "
	    ."   AND MID(mtacco,1,instr(mtacco,'-')-1) = karcco "
	    ." GROUP BY artcod, artnom, artuni, karexi, mtavan, mtavac, karvuc, karpro"
	    ." ORDER BY artcod ";   
	  */  
	    
	  $q="SELECT artcod, artnom, artuni, karexi, karvuc, karpro, sum(vdecan), sum((vdevun*vdecan)-vdedes) "
	    ."  FROM ".$wbasedato."_000001, ".$wbasedato."_000007, ".$wbasedato."_000016, ".$wbasedato."_000017 "
	    ." WHERE karcco = '".$wccoe[0]."'"
	    ."   AND karcod = artcod "
	    ."   AND venfec BETWEEN '".$wfecini."'"
	    ."   AND '".$wfecfin."'"
	    ."   AND vencco = karcco "
	    ."   AND vennum = vdenum "
	    ."   AND vdeart = karcod "
	    ." GROUP BY artcod, artnom, artuni, karexi, karvuc, karpro"
	    ." ORDER BY artcod ";    
	   
	  $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Codigo</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Descripción</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Presentacion</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Existencia</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Cantidad Vendida</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Vr Venta Prom.Unit.</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Vr Total Ventas</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Vr Ultima Compra</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VR Costo Promedio</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>Diferencia Valor Venta Prom. - Costo Promedio</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>% de utilidad Valor Prom. Vta VS Costo Promedio</font></th>";
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[3],2,'.',',')."</font></td>";
	      
	      /*
	      //SUMO EL TOTAL DE LAS VENTAS POR ARTICULO
	      $q="  SELECT sum(vdecan), sum((vdevun*vdecan)-vdedes) "
	        ."    FROM ".$wbasedato."_000016, ".$wbasedato."_000017 "
	        ."   WHERE venfec BETWEEN '".$wfecini."'"
	        ."     AND '".$wfecfin."'"
	        ."     AND vencco = '".$wccoe[0]."'"
	        ."     AND vennum = vdenum "
	        ."     AND vdeart = '".$row[0]."'";
	      $err1 = mysql_query($q,$conex); 
	      $row1 = mysql_fetch_array($err1);
	      */
	      
	      if ($row[6] > 0) 
	         echo "<td align=right><font size=2>".number_format($row[6],2,'.',',')."</font></td>";            //Cantidad vendida
	        else
	           echo "<td align=right><font size=2>".number_format(0,2,'.',',')."</font></td>";                 //Cantidad vendida
	           
	      if ($row[6] > 0)
	         echo "<td align=right><font size=2>".number_format(($row[7]/$row[6]),2,'.',',')."</font></td>"; //Valor Venta Promedio Unitario
	        else
	           echo "<td align=right><font size=2>".number_format(0,2,'.',',')."</font></td>";                 //Valor Venta Promedio Unitario
	      echo "<td align=right><font size=2>".number_format($row[7],2,'.',',')."</font></td>";               //Valor total ventas
	      echo "<td align=right><font size=2>".number_format($row[4],2,'.',',')."</font></td>";                //Costo Ultima Compra
	      echo "<td align=right><font size=2>".number_format($row[5],2,'.',',')."</font></td>";                //Costo Promedio
	      if ($row[6] > 0)
	         {
	          echo "<td align=right><font size=2>".number_format((($row[7]/$row[6])-$row[5]),2,'.',',')."</font></td>";    //Dif. Vta vs Costo Promedio
	          echo "<td align=center><font size=2>".number_format((((($row[7]/$row[6])-$row[5])/($row[7]/$row[6]))*100),2,'.',',')." %</font></td>"; //% de Utilidad. Vta vs Costo Promedio
             } 
	        else
	          { 
	           echo "<td align=right><font size=2>".number_format((0-$row[5]),2,'.',',')."</font></td>";    //Dif. Vta vs Costo Ult. Compra 
	           echo "<td align=center><font size=2>".number_format((0-$row[5]),2,'.',',')." %</font></td>"; //% Utilidad Vta vs Costo Promedio
              } 
	      
	      echo "</tr>"; 
	     }
	  echo "</table>"; 
     } 
}
?>
</body>
</html>