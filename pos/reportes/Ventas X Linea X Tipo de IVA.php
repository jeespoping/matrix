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
  include ("root/comun.php");
  mysql_select_db("matrix");
  echo "<form action='Ventas X Linea X Tipo de IVA.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  		                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(2012-01-20)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  
  encabezado("Ventas por Linea por Tipo de IVA ",$wactualiz, "farmastore");
  
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wtar))
   {
    if( !isset($wfecini ))
	  { $wfecini = date("Y-m-d"); }
	
	if( !isset($wfecfin ))
	  { $wfecfin = date("Y-m-d"); }
   
    echo "<center><table>";
	echo "<tr class='encabezadotabla'>";
	echo "<td align='center'>Fecha inicial</td>";
	echo "<td align='center'>Fecha final</td>";
	echo "</tr>";
	echo "<tr class='fila1'>";
	echo "<td align='center'>";
	campoFechaDefecto( "wfecini", $wfecini );
	echo "</td>";
	echo "<td align='center'>";
	campoFechaDefecto( "wfecfin", $wfecfin );
	echo "</td>";
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
	  
	  echo "<center><table>";
	  echo "<tr>";  
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
      echo "</tr>";  
	  echo "</table></center>";
	  
      
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
	  
	  $q = " CREATE INDEX nve_idx on tempo1 (fdenve) ";
	  $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
      
      /////////////////////////////////////////////////////////////////
	  // REPORTE DE VENTAS POR LINEA POR TIPO DE IVA POR CADA FACTURA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<center><table border=1>";
	  //echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>VENTAS POR LINEA POR TIPO DE IVA PARA CADA FACTURA</b></font></td></tr>";
      
	  $q = "  SELECT fenffa, fenfac, grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      ."    FROM tempo1, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
          //."   WHERE fenfec between '".$wfecini."'"
          //."     AND '".$wfecfin."'"
          ."   WHERE vennum = fdenve "
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
	  $totbrumal =0;
	  $totivamal =0;
	  $tottotmal =0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      
		     $row = mysql_fetch_array($err);
		     
		     $exp=explode('-', $row[1]);
		     $facLet=$exp[0]; // letra o primera parte de la factura
		     $facNum=$exp[1]; // numero o segunda parte de la factura
		   		$res=0;

		    	if (isset ($conLet) and isset ($conNum) and $conLet==$facLet  and $conNum!=$facNum and $facNum!=$conNum+1)
			     			 //$res=$facNum-$conNum; se cambia porque generaba error en el consecutivo ej: 10 y seguia 100
		    	    $res=1;
			   	else if (!isset ($conLet) or !isset ($conNum) or !$conLet==$facLet)
			    {
				     	$query = " select venviv from ".$wbasedato."_000018, ".$wbasedato."_000019, ".$wbasedato."_000016 ";
		    				$query = $query. " where fenfac= '".$facLet."-".($facNum-1)."'  AND fenffa = fdeffa  AND fenfac = fdefac  AND fdenve = vennum AND vencco = '".$wccoe[0]."' ";

		    				$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
	  							$num3 = mysql_num_rows($err3);
		    				if ($num3<=0)
											$res=2;
		     	}

    			
			     	for ($j=1;$j<$res;$j++)
			     	{
				     	$vennfa=$facNum-$j;
			     	 $q = "  SELECT  grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      			."     FROM ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
          ."     where vennfa='".$facLet."-".$vennfa."'"
          ."     AND venffa='".$row[0]."'"
          ."     AND vencco = '".$wccoe[0]."' "
          ."     AND vennum = vdenum "
           ."     AND vdeart = artcod "
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."   GROUP BY grucod, grudes, arttiv, artiva ";
          

										$err2 = mysql_query($q,$conex) ;
	  							$num2 = mysql_num_rows($err2);

									if ($num2>0)
	  					{
	  							for ($k=0;$k<$num2;$k++)
	     					{
		     					$ver=$num2;
		     					
		     					$row2 = mysql_fetch_array($err2);
		     					echo "<tr>";
	
	      									echo "<td bgcolor='#ff0000'><font size=2>".$row[0]."</font></td>";
	      									echo "<td bgcolor='#ff0000'><font size=2>".$facLet."-".$vennfa."</font></td>";
	      									echo "<td bgcolor='#ff0000'><font size=2>".$row2[0]."</font></td>";
	      									echo "<td bgcolor='#ff0000'><font size=2>".$row2[1]."</font></td>";
	      									echo "<td bgcolor='#ff0000'><font size=2>".$row2[2]."</font></td>";
	      									echo "<td bgcolor='#ff0000'><font size=2>".$row2[3]."</font></td>";
	      									echo "<td bgcolor='#ff0000' align=right><font size=2>".number_format($row2[4],0,'.',',')."</font></td>";
	      									echo "<td bgcolor='#ff0000' align=right><font size=2>".number_format($row2[5],0,'.',',')."</font></td>";
	      									echo "<td bgcolor='#ff0000' align=right><font size=2>".number_format($row2[4]+$row2[5],0,'.',',')."</font></td>";
	      						echo "</tr>"; 
	      
	      
	      									$wtotvenbru = $wtotvenbru + $row2[4];
	      									$wtotveniva = $wtotveniva + $row2[5];
	      									$wtotventot = $wtotventot + $row2[4]+$row2[5];
	      									
	      										$totbrumal = $totbrumal  + $row2[4];
	     					 					$totivamal =$totivamal + $row2[5]; 
	     					 					$tottotmal = $tottotmal + $row2[4]+$row2[5];
	  										}
	  								}else
  									{
	  													echo "<tr>";
	      															echo "<td bgcolor='#ff0000'><font size=2>".$row[0]."</font></td>";
	      															echo "<td bgcolor='#ff0000'><font size=2>".$facLet."-".$vennfa."</font></td>";
	      															echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
	      															echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
	      															echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
	      															echo "<td bgcolor='#ff0000'><font size=2>0</font></td>";
	      															echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
	      															echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
	      															echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
	     														echo "</tr>"; 
  										}

  								}
		   
		     
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
	      
	      $conLet=$facLet; //contenedor del valor anterior de letra
	      $conNum=$facNum; //contenedor del valor anterior de numero
	     }
	  echo "<tr>";  
	  echo "<td colspan=6>TOTALES</td>";
	  echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	  echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	  echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	  echo "<tr>";
	  
	  echo "<tr>";
	      					echo "<td bgcolor='#ff0000' colspan='2'><font size=2>Suma de facturas erroneas</font></td>";
	      					echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
	      					echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
	      					echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
	      					echo "<td bgcolor='#ff0000'><font size=2>0</font></td>";
	      					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(	$totbrumal,0,'.',',')."</font></td>";
	      					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($totivamal ,0,'.',',')."</font></td>";
	      				echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($tottotmal,0,'.',',')."</font></td>";
	     							echo "</tr>"; 
	   
	  echo "</table>"; 
	  
	  echo "<table align=center border='0'>"; 
	 	echo "<td bgcolor='#ff0000' width='20'><font size=2>&nbsp;</font></td>";
	  echo "<td  ALIGN=center><font size=2>=MALA</font></td>";
	  echo "</table></br></br>"; 


	 } 
}
?>
</body>
</html>