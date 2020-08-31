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
  
  
  echo "<form action='Tarifas_matrix_vs_unix.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  
  echo "<center><table border=2>";
  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>COMPARATIVO TARIFAS POS MATRIX - UNIX</b></font></td></tr>";
  
  if (!isset($wtar) and !isset($wcco))
   {
	echo "<tr>";  
    //echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10></td>";
    //echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
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
      echo "<td bgcolor=".$wcf." align=center colspan=1><b><font text color=".$wclfg.">SUCURSAL: </font></b>".$wcco."</td>";
      echo "<td bgcolor=".$wcf." align=center colspan=1><b><font text color=".$wclfg.">TARIFA: </font></b>".$wtar."</td>";
      echo "</tr>";  
	  
	  
      $q="SELECT artcod, artnom, artuni, mtavac "
	    ."  FROM ".$wbasedato."_000026, ".$wbasedato."_000001 "
	    ." WHERE MID(mtatar,1,instr(mtatar,'-')-1) = '".$wtare[0]."'"
	    ."   AND MID(mtacco,1,instr(mtacco,'-')-1) = '".$wccoe[0]."'"
	    ."   AND MID(mtaart,1,instr(mtaart,'-')-1) = artcod "
	    ." GROUP BY artcod, artnom, artuni, mtavac "
	    ." ORDER BY artcod ";   
	   
	  $err = mysql_query($q,$conex);
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CODIGO</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DESCRIPCION</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>PRESENTACION</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR ACTUAL (MATRIX)</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>VALOR UNIX</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DIFERENCIA</font></th>";
	  echo "<th align=CENTER bgcolor=DDDDDD><font size=2>% DIFERENCIA</font></th>";
	  
	  for ($i=0;$i<$num;$i++)
	     {
	      $row = mysql_fetch_array($err);
	      echo "<tr>";
	      echo "<td><font size=2>".$row[0]."</font></td>";
	      echo "<td><font size=2>".$row[1]."</font></td>";
	      echo "<td><font size=2>".$row[2]."</font></td>";
	      echo "<td align=right><font size=2>".number_format($row[3],2,'.',',')."</font></td>";
	      
	      $q = " SELECT arttarval "
	          ."   FROM ivarttar "
	          ."  WHERE arttarcod = '".$row[0]."'"
	          ."    AND arttartar = '*'";
	          
	      $res_uni = odbc_do($conexunix,$q);
	      
	      if (odbc_fetch_row($res_uni))
	         {
	          echo "<td align=right><font size=2>".number_format(odbc_result($res_uni,1),2,'.',',')."</font></td>";
	          echo "<td align=center><font size=2>".number_format($row[3]-odbc_result($res_uni,1),2,'.',',')."</font></td>";
	          if ((100-(odbc_result($res_uni,1)/$row[3])*100) > 0)
	             $wbgcolor="00FFFF";
	            else
	               $wbgcolor="FFFFFF";
	          echo "<td align=center bgcolor = ".$wbgcolor."><font size=2>".number_format(100-((odbc_result($res_uni,1)/$row[3])*100),2,'.',',')." %</font></td>";
             } 
	        else
	           {
	            echo "<td align=right><font size=2>".number_format(0,2,'.',',')."</font></td>";
	            echo "<td align=center><font size=2>".number_format($row[3]-0,2,'.',',')."</font></td>";
	            echo "<td align=center><font size=2>".number_format(100-((0/$row[3])*100),2,'.',',')." %</font></td>";
               } 
	      echo "</tr>"; 
	     }
	  echo "</table>"; 
     } 
}
?>
</body>
</html>