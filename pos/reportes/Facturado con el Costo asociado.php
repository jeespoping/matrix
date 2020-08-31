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
  echo "<form action='Facturado con el Costo asociado.php' method=post>";

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
	  
      
      //$hora = (string)date("H:i:s");
      //echo "1er Query Tiempo 1 : ".$hora."<br>";
      
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
      
      //$hora = (string)date("H:i:s");
      //echo "Termino 1er Query Tiempo 1 : ".$hora."<br>";
      
	  /////////////////////////////////////////////////////////////////
	  // REPORTE POR FACTURA CON EL TOTAL FACTURADO Y COSTO DE LA VENTA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  
	  echo "<center><table border=2>";
	  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
      echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURAS Y COSTO ASOCIADO</b></font></td></tr>";
	  
      $q = "  CREATE TEMPORARY TABLE if not exists tempo2 as "
	      ."  SELECT fenffa, fenfac, fenfec, fenval, vencon, vennmo  "
	      ."    FROM tempo1, ".$wbasedato."_000016 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND vennum = fdenve "
          ."     AND vencco = '".$wccoe[0]."'"
          ."   GROUP BY 1,2,3,4,5,6 ";
      $err = mysql_query($q,$conex);
      
      $q = "CREATE INDEX tempo2idx on tempo2 (vencon,vennmo)";
      $err = mysql_query($q,$conex);
      
      $q = "  SELECT fenffa, fenfac, fenfec, fenval, sum(mdevto)  "
	      ."    FROM tempo2 left join ".$wbasedato."_000011 "
          ."      ON mdecon = vencon "
          ."     AND mdedoc = vennmo "
          ."   GROUP BY 1,2,3,4 "
          ."   ORDER BY  fenffa, fenfac, fenfec ";
              
      $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
	  //echo $num;
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FECHA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR FACTURA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>COSTO VENTA</font></th>";
	  
	  
	  $wtotventot = 0;
	  $wtotcostot = 0;
	  
	  $totbrumal =0;
	  $totivamal =0;

	  
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
			   	
		    	else 
		    	if (!isset ($conLet) or !isset ($conNum) or !$conLet==$facLet)
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
			     	 	$q = " SELECT venfec, vdenum, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)), sum(mdevto) "
	      				."    FROM  ".$wbasedato."_000016, ".$wbasedato."_000017 , ".$wbasedato."_000011  "
          				."     where vennfa='".$facLet."-".$vennfa."'"
          				."     AND venffa='".$row[0]."'"
          				."     AND vencco = '".$wccoe[0]."' "
          				."     AND vennum = vdenum "
          		        ."     and mdecon = vencon "
          				."     AND mdedoc = vennmo "
          				."     AND mdeart = vdeart "
          				."   GROUP BY vdenum  ";
						$err2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  					$num2 = mysql_num_rows($err2);
	  											

	  					if ($num2>0)
	  					{
	  					 for ($k=0;$k<$num2;$k++)
	     				 {
		     			  $row2 = mysql_fetch_array($err2);
		     			  echo "<tr>";
	      				  echo "<td bgcolor='#ff0000'><font size=2>".$row[0]."</font></td>";
	      				  echo "<td bgcolor='#ff0000'><font size=2>".$facLet."-".$vennfa."</font></td>";
	      				  echo "<td bgcolor='#ff0000'><font size=2>".$row2[0]."</font></td>";
	      				  echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($row2[2]+$row2[3],0,'.',',')."</font></td>";
	      				  echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($row2[4],0,'.',',')."</font></td>";
	    
	     				  echo "</tr>"; 
	      				  $wtotventot = $wtotventot + $row2[2]+$row2[3];
	      				  $wtotcostot = $wtotcostot + $row2[4];
	      													
	      				  $totbrumal = $totbrumal  + $row2[2]+$row2[3];
	     				  $totivamal =$totivamal + $row2[4]; 
	  					 }
	  					}else
  						 {
	  					    echo "<tr>";
	      					echo "<td bgcolor='#ff0000'><font size=2>".$row[0]."</font></td>";
	      					echo "<td bgcolor='#ff0000'><font size=2>".$facLet."-".$vennfa."</font></td>";
	      					echo "<td bgcolor='#ff0000'><font size=2>0</font></td>";
	      					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
	      					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
	     					echo "</tr>"; 
  						 }
  					 }
  									
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[3],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[4],0,'.',',')."</font></td>";
	      echo "</tr>"; 
	      
	      $wtotventot = $wtotventot + $row[3];
	      $wtotcostot = $wtotcostot + $row[4];
	      
	      $conLet=$facLet; //contenedor del valor anterior de letra
	       
	      $conNum=$facNum; //contenedor del valor anterior de numero
	      //echo $conNum."=".$facNum."-".$j."-".$res;
	     }
	   echo "<tr>";  
	   echo "<td colspan=3>TOTAL</td>";
	   echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotcostot,0,'.',',')."</font></td>";
	   echo "</tr>";
	   
	   echo "<tr>";
	   echo "<td bgcolor='#ff0000' colspan='3'><font size=2>Suma de facturas erroneas</font></td>";
	   echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($totbrumal,0,'.',',')."</font></td>";
	   echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($totivamal,0,'.',',')."</font></td>";
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
