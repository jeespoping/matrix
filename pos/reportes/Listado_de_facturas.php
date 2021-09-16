<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
/*
* HISTORIAL DE ACTAULIZACIONES:
* 2012-07-11 Camilo Zapata, se creo el puente con la tabla 19 para que no haya incosistencias entre las fechas de las facturas y sus respectivas ventas.
* 2012-07-11 Camilo Zapata, se le dió el estilo correcto al reporte.
* 2012-06-22 Camilo Zapata, se agregó el filtro de fenfec en todos los querys que involucran a la tabla 18 para que verifique las fechas de las facturas y el filtro de cco
*/
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  

  include_once("root/comun.php");
  

  echo "<form action='Listado_de_facturas.php' method=post>";
  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  $wactualiz='2012-08-22';
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  encabezado("REPORTE DE VENTAS", $wactualiz, "logo_".$wbasedato);
  echo "<center><table border=2>";
 /* echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE VENTAS</b></font></td></tr>";*/
  
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wtar))
   {
	echo "<tr class='fila1'>";  
    echo "<td align=center><b>Fecha Inicial: ";campoFechaDefecto( "wfecini", $wfecha );echo"</b></td>";
    echo "<td align=center><b>Fecha Final: ";campoFechaDefecto( "wfecfin", $wfecha ); echo"</b></td>";
    echo "</tr>";
    
    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);
	    
	echo "<tr class='fila2'><td align=center >SELECCIONE LA SUCURSAL: ";
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
	    
	echo "<td align=center>SELECCIONE LA TARIFA: ";
	echo "<select name='wtar'>";
	//echo "<option>&nbsp</option>";    
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td></tr>";
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	
    echo "<tr class='fila1'>";
    echo "<td align=center colspan=2><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else 
     {
	  echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
	  $wccoe = explode("-",$wcco); 
	  
	  echo "<input type='HIDDEN' NAME= 'wtar' value='".$wtar."'>";
	  $wtare = explode("-",$wtar); 
	  
	  echo "<tr class='fila1'>";  
      echo "<td align=center><b>Fecha Inicial (AAAA-MM-DD):</b>".$wfecini."</td>";
      echo "<td align=center><b>Fecha Final (AAAA-MM-DD):</b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr class='fila2'>";
      echo "<td align=center colspan=2><b>SUCURSAL: </b>".$wcco."</td>";
      echo "</tr>";  
	  
	  $qaux = "DROP TABLE IF EXISTS tempo1";
	$err = mysql_query($qaux, $conex);
      
      //ACA CREO UNA TABLA TEMPORAL CON LAS FACTURAS DEL PERIODO
      $q = "  CREATE TEMPORARY TABLE if not exists tempo1 as "
          ."  SELECT fenffa, fenfac, fenfec, fdenve, fenval"
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000019 a"
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
		  ."     AND a.fecha_data = fenfec"
          ."     AND fenffa = fdeffa "
		  ."     AND fencco = '".$wccoe[0]."'"
          ."     AND fenfac = fdefac "
		  ."	 AND fenest ='on'"
          ."   GROUP BY fenffa, fenfac, fenfec, fdenve, fenval ";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
      
      
	  ///////////////////////////////////////////////////////////////////////
	  // REPORTE DE FACTURAS TAL COMO QUEDO FACTURA ANTES DE MODIFCAR TARIFAS
	  ///////////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  
	  echo "<center><table border=2>";
	  //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
      echo "<tr class='encabezadotabla'><td align=center><font size=4 text><b>LISTADO DE FACTURAS</b></font></td></tr>";
	  
	  $q = "  SELECT fenffa, fenfac, vdepiv, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)) "
	      ."    FROM tempo1 a, ".$wbasedato."_000016, ".$wbasedato."_000017 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND a.fdenve = vennum "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND vennum = vdenum "
          ."   GROUP BY 1,2,3 "
          ."   ORDER BY fenffa, fenfac ";;
          
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err);
	  echo "<table border=1>";
	  
	  echo "<tr class='encabezadotabla'>";
	  echo "<th align=CENTER><font size=2>FUENTE</font></th>";
	  echo "<th align=CENTER><font size=2>FACTURA</font></th>";
	  echo "<th align=CENTER><font size=2>% IVA</font></th>";
	  echo "<th align=CENTER><font size=2>VALOR VENTA SIN IVA</font></th>";
	  echo "<th align=CENTER><font size=2>VALOR IVA</font></th>";
	  echo "<th align=CENTER><font size=2>VALOR VENTA TOTAL</font></th>";
	  echo "</tr>";
	  
	  $wtotbrutot = 0;
	  $wtotivatot = 0;
	  $wtottottot = 0;
	  $totbrumal =0;
	  $totivamal =0;
	  $tottotmal =0;
	  
	  for ($i=0;$i<$num;$i++)
	  {
		if(is_int($i/2))
			$wclass='fila2';
				else
					$wclass='fila1';
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
		    				$query = $query. " where fenfac= '".$facLet."-".($facNum-1)."'  AND fenffa = fdeffa  AND fenfac = fdefac AND fdenve = vennum AND vencco = '".$wccoe[0]."' AND fencco = '".$wccoe[0]."'";

		    				$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
	  							$num3 = mysql_num_rows($err3);
		    				if ($num3<=0)
											$res=2;
		     	}

			     		for ($j=1;$j<$res;$j++)
			     		{
				     				$vennfa=$facNum-$j;
			     	 			$q = "  SELECT vdepiv, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0)) "
	      							."    FROM  ".$wbasedato."_000016, ".$wbasedato."_000017 "
									."   WHERE vennfa='".$facLet."-".$vennfa."'"
									."     AND venffa='".$row[0]."'"
									."     AND vencco = '".$wccoe[0]."' "
									."     AND vennum = vdenum "
									."   GROUP BY 1 ";
														$err2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  											$num2 = mysql_num_rows($err2);
	  											//$ver=$q;
	  											
	  											if ($num2>0)
	  											{
													for ($k=0;$k<$num2;$k++)
														{
		     												$row2 = mysql_fetch_array($err2);
		     												echo "<tr class='".$wclass."'>";
	      														echo "<td><font size=2>".$row[0]."</font></td>";
	      														echo "<td><font size=2>".$facLet."-".$vennfa."</font></td>";
	      														echo "<td><font size=2>".$row2[0]."</font></td>";
	      														echo "<td align=right><font size=2>".number_format($row2[1],0,'.',',')."</font></td>";
	      														echo "<td align=right><font size=2>".number_format($row2[2],0,'.',',')."</font></td>";
	      														echo "<td align=right><font size=2>".number_format(($row2[1]+$row2[2]),0,'.',',')."</font></td>";
	     													echo "</tr>"; 
	      													$wtotbrutot = $wtotbrutot + $row2[1];
	      													$wtotivatot = $wtotivatot + $row2[2];
	     					 								$wtottottot = $wtottottot + $row2[1]+$row2[2];
	     					 								
	     					 								$totbrumal = $totbrumal  + $row2[1];
	     					 								$totivamal =$totivamal + $row2[2]; 
	     					 								$tottotmal = $tottotmal + $row2[1]+$row2[2];
	  													}
  												}else
  												{
	  													echo "<tr class='".$wclass."'>";
	      															echo "<td><font size=2>".$row[0]."</font></td>";
	      															echo "<td><font size=2>".$facLet."-".$vennfa."</font></td>";
	      															echo "<td><font size=2>0</font></td>";
	      															echo "<td align=right><font size=2>".number_format(0,0,'.',',')."</font></td>";
	      															echo "<td align=right><font size=2>".number_format(0,0,'.',',')."</font></td>";
	      															echo "<td align=right><font size=2>".number_format(0,0,'.',',')."</font></td>";
	     														echo "</tr>"; 
  												}
  									}
		   						
	   				
	      echo "<tr class='".$wclass."'>";
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
	      
	      $conLet=$facLet; //contenedor del valor anterior de letra
	      $conNum=$facNum; //contenedor del valor anterior de numero
	      
	     }
	   echo "<tr class='encabezadotabla'>";  
	   echo "<td colspan=3>TOTAL</td>";
	   echo "<td align=right><font size=2>".number_format($wtotbrutot,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtotivatot,0,'.',',')."</font></td>";
	   echo "<td align=right><font size=2>".number_format($wtottottot,0,'.',',')."</font></td>";
	   echo "<tr>";
	   
	   echo "<tr>";
	      					echo "<td bgcolor='#ff0000' colspan='2'><font size=2>Suma de facturas erroneas</font></td>";
	      					echo "<td bgcolor='#ff0000'><font size=2>0</font></td>";
	      					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($totbrumal,0,'.',',')."</font></td>";
	      					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($totivamal ,0,'.',',')."</font></td>";
	      				echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($tottotmal,0,'.',',')."</font></td>";
	     							echo "</tr>"; 
	   
	  echo "</table></br></br>"; 
	  
	   echo "<table align=center border='0'>"; 
	 	echo "<td bgcolor='#ff0000' width='20'><font size=2>&nbsp;</font></td>";
	  echo "<td  ALIGN=center><font size=2>=MALA</font></td>";
	  echo "</table></br></br>"; 
     } 
}
?>
</body>
</html>
