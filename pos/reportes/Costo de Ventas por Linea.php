<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
 session_start();
 if(!session_is_registered("user"))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  include("conex.php");
  mysql_select_db("matrix");
  echo "<form action='costo de ventas por linea.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE VENTAS</b></font></td></tr>";
  
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
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	
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
	  
      
      //ACA CREO UNA TABLA TEMPORAL CON LAS FACTURAS DEL PERIODO
      $q = "  CREATE TEMPORARY TABLE if not exists tempo1 as "
          ."  SELECT fenffa, fenfac, fenfec, fdenve, fenval "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000019 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fenffa = fdeffa "
          ."     AND fenfac = fdefac "
          ."   GROUP BY fenffa, fenfac, fenfec, fdenve, fenval ";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
      
      $q = "CREATE INDEX tempo1idx on tempo1 (vencon,vennmo)";
      $err = mysql_query($q,$conex);
      
      $q = "  CREATE TEMPORARY TABLE if not exists tempo2 as "
	      ."  SELECT fenffa, fenfac, fenfec, fenval, vencon, vennmo  "
	      ."    FROM tempo1, ".$wbasedato."_000016 "
          ."   WHERE vennum = fdenve "
          ."     AND vencco = '".$wccoe[0]."'"
          ."   GROUP BY 1,2,3,4,5,6 ";
      $err = mysql_query($q,$conex);
      
      $q = "CREATE INDEX tempo2idx on tempo2 (vencon,vennmo)";
      $err = mysql_query($q,$conex);
      
      
      //OJO OJO OJO OJO
      //CON ESTE PROCEDIMIENTO QUE SIGUE, SE HALLAN LOS ARTICULOS FACTURADOS QUE TIENEN UN GRUPO O LINEA DEFINIDA
      //SE DEBE ACTIVAR CUANDO EXISTAN DIFERENCIAS CON EL REPORTE DE FACTURADO VS COSTO POR FACTURA
      //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
      /*
      $q = " SELECT fenffa, fenfac, fenfec, fenval, vencon, vennmo, mdevto, mdeart, artgru "
          ."   FROM tempo2, ".$wbasedato."_000011, ".$wbasedato."_000001 "
          ."   WHERE vencon = mdecon "
          ."     AND vennmo = mdedoc "
          ."     AND mdeart = artcod "
          ."     AND mid(artgru,1,instr(artgru,'-')-1) not in (SELECT grucod FROM ".$wbasedato."_000004) ";
      $err_pru = mysql_query($q,$conex); 
      $num = mysql_num_rows($err_pru);   
          
      $wtot=0;
      for ($i=0;$i<$num;$i++)
          {
	       $row = mysql_fetch_array($err_pru);
	       echo "<tr>";
	       echo "<td>".$row[0]."</td>";
	       echo "<td>".$row[1]."</td>";
	       echo "<td>".$row[2]."</td>";
	       echo "<td>".$row[3]."</td>";
	       echo "<td>".$row[4]."</td>";
	       echo "<td>".$row[5]."</td>";
	       echo "<td>".$row[6]."</td>";
	       echo "<td>".$row[7]."</td>";
	       echo "<td>".$row[8]."</td>";
	       echo "</tr>";
	       
	       $wtot=$wtot+$row[6];
          }
      echo "<tr><td> Total : ".$wtot."</td></tr>";    
      */
      //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>    
      //HASTA ACA 
      
      /////////////////////////////////////////////////////////////////
	  // REPORTE DEL COSTO DE VENTAS POR LINEA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  
	  echo "<center><table border=2>";
	  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
      echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>COSTO DE LAS VENTAS POR LINEA</b></font></td></tr>";
	  
      $q = "  SELECT grucod, grudes, sum(mdevto)  "
	      ."    FROM tempo2, ".$wbasedato."_000011, ".$wbasedato."_000001, ".$wbasedato."_000004 "
          ."   WHERE vencon = mdecon "
          ."     AND vennmo = mdedoc "
          ."     AND mdeart = artcod "
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."   GROUP BY 1,2 "; 
          
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CODIGO</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO</font></th>";
	  
	  
	  $wtotcostot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[2],0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotcostot = $wtotcostot + $row[2];
	     }
	  echo "<tr>";  
	  echo "<td colspan=2>TOTAL</td>";
	  echo "<td align=right><font size=2>".number_format($wtotcostot,0,'.',',')."</font></td>";
	  echo "<tr>";
	   
	  echo "</table>"; 
	 } 
}
?>
</body>
</html>