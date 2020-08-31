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
  

  

  
  
  $conexunix = odbc_pconnect('informix','facadm','1201')
  					    or die("No se ralizo Conexion con el Unix");
  
  
  echo "<form action='mayor_valor_coomeva.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  
  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>MAYOR VALOR COBRADO A COOMEVA</b></font></td></tr>";
  
  if (!isset($wfecini) and !isset($wfecfin) and !isset($wtar) and !isset($wcco) and !isset($wemp))
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
	    
	echo "<tr><td align=center bgcolor=".$wcf." colspan=2>SELECCIONE LA SUCURSAL: ";
	echo "<select name='wcco'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td></tr>";
    
    
	//EMPRESA
    $q =  " SELECT empcod, empnom "
		 ."   FROM ".$wbasedato."_000024 "
		 ."  WHERE empcod = empres "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	    
	echo "<tr><td align=center bgcolor=".$wcf." colspan=2>SELECCIONE LA EMPRESA: ";
	echo "<select name='wemp'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td></tr>";
	
	
    //SELECCIONAR TARIFA
    $q =  " SELECT tarcod, tardes "
		 ."   FROM ".$wbasedato."_000025 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	    
	echo "<td align=center bgcolor=".$wcf." colspan=2>SELECCIONE LA TARIFA: ";
	echo "<select name='wtar'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td></tr>";
	
	echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">Imprime los articulos que tengan diferencia cero (0), (S/N) </font></b><INPUT TYPE='text' NAME='wcero' value='N' SIZE=10></td>";
	
    echo "<tr>";
    echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else 
     {
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco); 
	  
	  echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
	  $wempe = explode("-",$wemp);
	  
	  echo "<input type='HIDDEN' NAME= 'wtar' value='".$wtar."'>";
	  $wtare = explode("-",$wtar); 
	  
	  $wcero=strtoupper($wcero);
	  
	  echo "<tr>";  
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
      echo "</tr>";
	  echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=1><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
      echo "<td bgcolor=".$wcf." align=center colspan=1><b><font text color=".$wclfg.">TARIFA: </font></b>".$wtar."</td>";
      echo "</tr>";  
	  
	  $q=" SELECT fenfac, vdeart, artnom, artuni, vdecan, vdevun, mtavac "
	    ."   FROM ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000018, ".$wbasedato."_000026 "
	    ."  WHERE venfec between '".$wfecini."' AND '".$wfecfin."'"
	    ."    AND vencod = '".$wempe[0]."'"
	    ."    AND vennum = vdenum "
	    ."    AND vdeart = artcod "
	    ."    AND mtatar = '".$wtar."'"
	    ."    AND mtacco = '".$wcco."'"
	    ."    AND mid(mtaart,1,instr(mtaart,'-')-1) = artcod "
	    ."    AND vennfa = fenfac ";
	  $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err);  
      
      	  
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD>FACTURA</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>ARTICULO</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>DESCRIPCION</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>PRESENTACION</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>CANTIDAD</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>VR UNIT.FACTURADO</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>VALOR FACTURADO</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>VR UNIT. ACTUAL</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>VALOR ACTUAL</th>";
	  echo "<th align=CENTER bgcolor=DDDDDD>DIFERENCIA</th>";
	  
	  $wtotfac=0;
	  $wtotact=0;
	  $wtotdif=0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      
	      $wtotfac=$wtotfac+($row[4]*$row[5]);
	      $wtotact=$wtotact+($row[4]*$row[6]);
	      $wdif=($row[4]*($row[5]-$row[6]));
	      $wtotdif=$wtotdif+$wdif;
	      
	      if ($wcero == "S")
	         {
		      echo "<tr>";
		      echo "<td>".$row[0]."</td>";
		      echo "<td>".$row[1]."</td>";
		      echo "<td>".$row[2]."</td>";
		      echo "<td>".$row[3]."</td>";
		      echo "<td align=right>".$row[4]."</td>";
		      echo "<td align=right>".number_format($row[5],2,'.',',')."</td>";
		      echo "<td align=right>".number_format(($row[4]*$row[5]),2,'.',',')."</td>";
		      echo "<td align=right>".number_format($row[6],2,'.',',')."</td>";
		      echo "<td align=right>".number_format(($row[4]*$row[6]),2,'.',',')."</td>";
		      echo "<td align=right>".number_format(($row[4]*($row[5]-$row[6])),2,'.',',')."</td>";
		      echo "</tr>";    
	         }    
	        else
	           {
		        if ($wdif != 0)
		           {
			        echo "<tr>";
			        echo "<td>".$row[0]."</font></td>";
			        echo "<td>".$row[1]."</font></td>";
			        echo "<td>".$row[2]."</font></td>";
			        echo "<td>".$row[3]."</font></td>";
			        echo "<td align=right>".$row[4]."</font></td>";
			        echo "<td align=right>".number_format($row[5],2,'.',',')."</td>";
			        echo "<td align=right>".number_format(($row[4]*$row[5]),2,'.',',')."</td>";
			        echo "<td align=right>".number_format($row[6],2,'.',',')."</td>";
			        echo "<td align=right>".number_format(($row[4]*$row[6]),2,'.',',')."</td>";
			        echo "<td align=right>".number_format(($row[4]*($row[5]-$row[6])),2,'.',',')."</td>";
			        echo "</tr>"; 
		           } 
	           }    
	     }
	     
	  echo "<tr>";
	  echo "<td colspan=6 bgcolor=CCCCFF><b>Totales: </b></td>";
	  echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtotfac,0,'.',',')."</b></td>";
	  echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>&nbsp</b></td>";
	  echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtotact,0,'.',',')."</b></td>";
	  echo "<td bgcolor=CCCCFF ALIGN=RIGHT><b>".number_format($wtotdif,0,'.',',')."</b></td>";
	  echo "</tr>"; 
	  echo "<tr><td colspan=10 bgcolor=CCCCCC>&nbsp</td></tr>";
	  echo "</table>"; 
     } 
}
?>
</body>
</html>