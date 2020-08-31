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
  

  

  echo "<form action='devLineaTIvaRes.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  //$wbasedato='farstore';
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE DEVOLUCIONES</b></font></td></tr>";
  
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) )
   {
/**********************************************Primera pagina**************************/
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
	echo "</select></td>";
    
    
    /*SELECCIONAR TARIFA
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
	echo "</select></td></tr>";*/
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	
    echo "<tr>";
    echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else 
     {
/**********************************************Segunda página**************************/
/***********************************Consulto las devoluciones sin nota credito********************/
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco); 
	
	 
	   echo "<tr>";  
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
      echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr>";
      echo "<td bgcolor=".$wcf." align=center colspan=2><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
      echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES POR LINEA POR TIPO DE IVA</b></font></td></tr>";
      echo "</tr>";  
      echo "</center></table >";
	   
      
       echo "</BR></BR><center><table border=2>";
       echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES SIMPLES</b></font></td></tr>";
	   echo "</center></table >";
	   
      $table=date("Mdis").'2';
    
			
      		$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
 			$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto ";
			$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011 ";
			$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='801' and mencco='$wccoe[0]' ";
			$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
			$query= $query. "and Mendoc not in (select tradev FROM " .$wbasedato."_000055 where tradev = Mendoc and traven = Menfac and  tracco ='$wccoe[0]') ";
         	   
        
       		$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
          
          
           $q = "  SELECT menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
	      ."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
          ."   WHERE Menfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND Menfac = vennum "
          ."     AND vennum = vdenum "
          ."  AND vdeart=Mdeart "
          ."  AND artcod=Mdeart "
          ."     AND Mencco = '".$wccoe[0]."'"
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
           ."   GROUP BY grucod, grudes, arttiv, artiva ";
      
          
           $err = mysql_query($q,$conex) ;
	  	   $num = mysql_num_rows($err) ;
	  	   
	  	      if ($num>0)
	  	      {
	 
	    
	  	for ($i=0;$i<$num;$i++)
	   	{
		   	$row = mysql_fetch_row($err);
		   	
		   	for ($j=0;$j<10;$j++)
		   	{
		   	$vector[$i][$j]=$row[$j]; 	
	   		}
	   			

		}
	    
			echo "<table border=1 align=center>";
	  
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>LINEA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO IVA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>IVA</font></th>";
	 		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA BRUTA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR IVA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TOTAL FACTURADO</font></th>";
	  	
		
		 $wtotvenbru = 0;
	  	 $wtotveniva = 0;
	  	 $wtotventot = 0;
	  	
	  	for ($i=0;$i<$num;$i++)
	   	{
 			$query= "SELECT fdefac ";
			$query= $query. "FROM " .$wbasedato."_000019  ";
			$query= $query. "WHERE fdeffa='".$vector[$i][2]."' and fdenve='".$vector[$i][0]."' ";
			
			$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
	  	   	$cantidad = mysql_num_rows($err);
	  	   	
	  	   	if ($cantidad>=1)
	  	   	{
		  	   $res= mysql_fetch_row($err);
		   	
	  	   		echo "<tr>";
	      		echo "<td><font size=2>".$vector[$i][3]."</font></td>";
	      		echo "<td><font size=2>".$vector[$i][4]."</font></td>";
	      		echo "<td><font size=2>".$vector[$i][5]."</font></td>";
	      		echo "<td><font size=2>".$vector[$i][6]."</font></td>";
	      		echo "<td align=right><font size=2>".number_format($vector[$i][7],0,'.',',')."</font></td>";
	      		echo "<td align=right><font size=2>".number_format($vector[$i][8],0,'.',',')."</font></td>";
	      		echo "<td align=right><font size=2>".number_format($vector[$i][9],0,'.',',')."</font></td>";
	      		echo "</tr>"; 
	      
	      		$wtotvenbru = $wtotvenbru + $vector[$i][7];
	      		$wtotveniva = $wtotveniva + $vector[$i][8];
	      		$wtotventot = $wtotventot + $vector[$i][9];
  	   		}
		}
	  
	   
	     echo "<tr>";  
	   echo "<td colspan=4>TOTALES</td>";
	   echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	    echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	  echo "</table>";  

          
      }
/***********************************Consulto las devouciones con nota credito********************/	 
       
/***********************************Consulto las devouciones con nota credito********************/
	 
	   
      
       echo "</BR></BR><center><table border=2>";
       echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>DEVOLUCIONES CON NOTA CREDITO</b></font></td></tr>";
	   echo "</center></table >";
	   
      $table=date("Mdis").'1';
    
			
      		$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
 			$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto ";
			$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011,  " .$wbasedato."_000055  ";
			$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='801' and mencco='$wccoe[0]' ";
			$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
			$query= $query. "and tradev=mendoc and traven = Menfac and  tracco ='$wccoe[0]'and tratip <>'01-ANULACION' ";
         	   
        
       		$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
       	
       	
 		 $q = "  SELECT menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
	      ."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
          ."   WHERE Menfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND Menfac = vennum "
          ."     AND vennum = vdenum "
          ."  AND vdeart=Mdeart "
          ."  AND artcod=Mdeart "
          ."     AND Mencco = '".$wccoe[0]."'"
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
           ."   GROUP BY grucod, grudes, arttiv, artiva ";
      
          
           $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  	   $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	 
	    
	  	for ($i=0;$i<$num;$i++)
	   	{
		   	$row = mysql_fetch_row($err);
		   	
		   	for ($j=0;$j<10;$j++)
		   	{
		   	$vector[$i][$j]=$row[$j]; 	
	   		}
	   			

		}
	    
			echo "<table border=1 align=center>";
	  
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>LINEA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TIPO IVA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>IVA</font></th>";
	 		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VENTA BRUTA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR IVA</font></th>";
	  		echo "<th align=CENTER bgcolor=DDDDDD><font size=2>TOTAL FACTURADO</font></th>";
	  	
		
		 $wtotvenbru = 0;
	  	 $wtotveniva = 0;
	  	 $wtotventot = 0;
	  	
	  	for ($i=0;$i<$num;$i++)
	   	{
		   	
	  	   		echo "<tr>";
	      		echo "<td><font size=2>".$vector[$i][3]."</font></td>";
	      		echo "<td><font size=2>".$vector[$i][4]."</font></td>";
	      		echo "<td><font size=2>".$vector[$i][5]."</font></td>";
	      		echo "<td><font size=2>".$vector[$i][6]."</font></td>";
	      		echo "<td align=right><font size=2>".number_format($vector[$i][7],0,'.',',')."</font></td>";
	      		echo "<td align=right><font size=2>".number_format($vector[$i][8],0,'.',',')."</font></td>";
	      		echo "<td align=right><font size=2>".number_format($vector[$i][9],0,'.',',')."</font></td>";
	      		echo "</tr>"; 
	      
	      		$wtotvenbru = $wtotvenbru + $vector[$i][7];
	      		$wtotveniva = $wtotveniva + $vector[$i][8];
	      		$wtotventot = $wtotventot + $vector[$i][9];
  	   		}

	  
	   
	     echo "<tr>";  
	   echo "<td colspan=4>TOTALES</td>";
	   echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	    echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	  echo "</table>";  
          
      
/***********************************Consulto las devouciones con nota credito********************/	 
       

	 }
}
?>
</body>
</html>
