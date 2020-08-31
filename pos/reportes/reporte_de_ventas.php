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
  

  

  echo "<form action='reporte_de_ventas.php' method=post>";

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
      echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURADO POR TIPO DE IVA</b></font></td></tr>";
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
      
       
      /////////////////////////////////////////////////////////////////
	  // REPORTE POR FACTURADO POR TIPO DE IVA
	  /////////////////////////////////////////////////////////////////
      $q = "  SELECT fenffa, fenfac, fenfec, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)), SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes) + SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      ."    FROM tempo1, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fdenve = vennum "
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."     AND vencco = '".$wccoe[0]."'"
          ."   GROUP BY fenffa, fenfac, fenfec, arttiv, artiva ";
              
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>% IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA BRUTA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TOTAL FACTURADO</font></th>";
	  
	  
	  $wtotvenbru = 0;
	  $wtotveniva = 0;
	  $wtotventot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td><font size=2>".$row[3]."</font></td>";
	      echo "<td><font size=2>".$row[4]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[5],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[6],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[7],0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotvenbru = $wtotvenbru + $row[5];
	      $wtotveniva = $wtotveniva + $row[6];
	      $wtotventot = $wtotventot + $row[7];
	     }
	   echo "<tr>";  
	   echo "<td colspan=5>TOTALES</td>";
	   echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	  echo "</table>"; 
	  
	  
	  
	  /////////////////////////////////////////////////////////////////
	  // REPORTE POR FACTURA CON EL TOTAL FACTURADO Y COSTO DE LA VENTA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  
	  echo "<center><table border=2>";
	  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
      echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURAS Y COSTO ASOCIADO</b></font></td></tr>";
	  
	  $q = "  SELECT fenffa, fenfac, fenfec, fenval, sum(mdevto)  "
	      ."    FROM tempo1, ".$wbasedato."_000016, ".$wbasedato."_000011 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fdenve = vennum "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND vencon = mdecon "
          ."     AND vennmo = mdedoc "
          ."   GROUP BY 1,2,3,4 ";
                
      $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO VENTA</font></th>";
	  
	  
	  $wtotventot = 0;
	  $wtotcostot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[3],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[4],0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotventot = $wtotventot + $row[3];
	      $wtotcostot = $wtotcostot + $row[4];
	     }
	   echo "<tr>";  
	   echo "<td colspan=3>TOTAL</td>";
	   echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotcostot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	  echo "</table>"; 
	  
	  
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
	      ."    FROM tempo1, ".$wbasedato."_000016, ".$wbasedato."_000011, ".$wbasedato."_000001, ".$wbasedato."_000004 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fdenve = vennum "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND vencon = mdecon "
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
	  
	  
	  /////////////////////////////////////////////////////////////////
	  // REPORTE DE VENTAS POR LINEA POR TIPO DE IVA POR CADA FACTURA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<center><table border=2>";
	  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>VENTAS POR LINEA POR TIPO DE IVA PARA CADA FACTURA</b></font></td></tr>";
      
	  //$q = "  SELECT fenffa, fenfac, grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)), SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes) + SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	  $q = "  SELECT fenffa, fenfac, grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      ."    FROM tempo1, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fdenve = vennum "
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."   GROUP BY fenffa, fenfac, grucod, grudes, arttiv, artiva ";
          
      $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NRO FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>LINEA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>% IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA BRUTA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TOTAL FACTURADO</font></th>";
	  
	  
	  $wtotvenbru = 0;
	  $wtotveniva = 0;
	  $wtotventot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td><font size=2>".$row[3]."</font></td>";
	      echo "<td><font size=2>".$row[4]."</font></td>";
	      echo "<td><font size=2>".$row[5]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[6],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[7],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[6]+$row[7],0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotvenbru = $wtotvenbru + $row[6];
	      $wtotveniva = $wtotveniva + $row[7];
	      $wtotventot = $wtotventot + $row[6]+$row[7];
	     }
	   echo "<tr>";  
	   echo "<td colspan=6>TOTALES</td>";
	   echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	  echo "</table>"; 
	  
	  
	  /////////////////////////////////////////////////////////////////
	  // REPORTE DE VENTAS POR LINEA POR TIPO DE IVA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<center><table border=2>";
	  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>VENTAS POR LINEA POR TIPO DE IVA</b></font></td></tr>";
      
	  $q = "  SELECT grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      ."    FROM tempo1, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fdenve = vennum "
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."   GROUP BY grucod, grudes, arttiv, artiva ";
          
      $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>LINEA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>% IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA BRUTA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TOTAL FACTURADO</font></th>";
	  
	  
	  $wtotvenbru = 0;
	  $wtotveniva = 0;
	  $wtotventot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td><font size=2>".$row[3]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[4],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[5],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[4]+$row[5],0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotvenbru = $wtotvenbru + $row[4];
	      $wtotveniva = $wtotveniva + $row[5];
	      $wtotventot = $wtotventot + $row[4]+$row[5];
	     }
	  echo "<tr>";  
	  echo "<td colspan=4>TOTALES</td>";
	  echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	  echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	  echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	  echo "<tr>";
	   
	  echo "</table>"; 
	  
	  
	  /////////////////////////////////////////////////////////////////
	  // REPORTE DE FACTURAS TAL COMO QUEDO FACTURA ANTES DE MODIFCAR TARIFAS
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  
	  echo "<center><table border=2>";
	  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
      echo "<tr><td align=center bgcolor=".$wcf2."><font size=4 text color=#FFFFFF><b>REPORTE DE FACTURAS TAL COMO QUEDO FACTURADO ANTES DE MODIFICAR TARIFAS</b></font></td></tr>";
	  
	  //$q = "  SELECT fenffa, fenfac, vdepiv, sum(vdevun*vdecan), sum((vdevun*vdecan)*(vdepiv/100)) "
	  $q = "  SELECT fenffa, fenfac, vdepiv, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)) "
	      ."    FROM tempo1, ".$wbasedato."_000016, ".$wbasedato."_000017 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fdenve = vennum "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND vennum = vdenum "
          ."   GROUP BY 1,2,3 ";
          
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>% IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR VENTA SIN IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR IVA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR VENTA TOTAL</font></th>";
	  
	  $wtotbrutot = 0;
	  $wtotivatot = 0;
	  $wtottottot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[3],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[4],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format(($row[3]+$row[4]),0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotbrutot = $wtotbrutot + $row[3];
	      $wtotivatot = $wtotivatot + $row[4];
	      $wtottottot = $wtottottot + $row[3]+$row[4];
	      
	     }
	   echo "<tr>";  
	   echo "<td colspan=3>TOTAL</td>";
	   echo "<td align=right><font size=2>".number_format($wtotbrutot,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotivatot,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtottottot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	  echo "</table>"; 
	  
     } 
}
?>
</body>
</html>