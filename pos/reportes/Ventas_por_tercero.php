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
* 2012-07-11 Camilo Zapata, el filtro por centro de costos se habilito para que se activara solo en el caso de que el reporte
* 							se genere en farmpla.(linea 107)
* 2012-07-11 Camilo Zapata, se le dió el estilo correcto al reporte.
* 2012-06-15 Camilo Zapata, agregó el filtro del cco para las facturas, evitando así que dos facturas iguales que pertenezcan a distintos centros de costos influyan en los resultados.
*/
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  

  include_once("root/comun.php");
  

  echo "<form action='Ventas_por_tercero.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  $wactualiz='2012-07-12';
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  encabezado("REPORTE DE VENTAS FACTURADAS", $wactualiz, "logo_".$wbasedato);
  echo "<center><table border=2 width='100%'>";
  /*echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE VENTAS FACTURADAS</b></font></td></tr>";*/
  
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wemp))
   {
	echo "<tr class='fila1'>";  
    echo "<th align='center' colspan='1'><b>Fecha Inicial: ";campoFechaDefecto( "wfecini", $wfecha ); echo "</b></th>";
    echo "<th align='center' colspan='1'><b>Fecha Final : ";campoFechaDefecto( "wfecfin", $wfecha ); echo "</b></th>";
    echo "</tr>";
    
    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());;
	$num = mysql_num_rows($res);
	 
	  
	echo "<tr class='fila2'><td align=center colspan=1>SELECCIONE LA SUCURSAL: ";
	echo "<select name='wcco'>";
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."</option>";
       }
	echo "</select></td>";
    
    
	//SELECCIONAR EMPRESA
    $q =  " SELECT empcod, empnom, temdes "
		 ."   FROM ".$wbasedato."_000024, ".$wbasedato."_000029 "
		 ."  WHERE trim(mid(emptem,1,instr(emptem,'-')-1)) = temcod "
		 ."  ORDER BY 3,1 ";
	$res = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());;
	$num = mysql_num_rows($res);
	    
	echo "<td align=center>SELECCIONE LA EMPRESA: ";
	echo "<select name='wemp'>";
	echo "<option>% - Todas las empresas</option>";
	for ($i=1;$i<=$num;$i++)
	   {
	    $row = mysql_fetch_array($res); 
	    echo "<option>".$row[0]."-".$row[1]."- <b>[&nbsp&nbsp&nbsp&nbsp".$row[2]."&nbsp&nbsp&nbsp&nbsp]</b></option>";
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
	  
	  echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
	  $wempe = explode("-",$wemp); 
	  
	  echo "<tr class='fila1'>";  
      echo "<td align=center><b>Fecha Inicial (AAAA-MM-DD): </b>".$wfecini."</td>";
      echo "<td align=center><b>Fecha Final (AAAA-MM-DD): </b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr class='fila2'>";
      echo "<td align=center colspan=2><b>SUCURSAL: </b>".$wcco."</td>";
      echo "<tr><td align=center colspan=2 class='encabezadotabla'><font size=6><b>VENTAS FACTURADAS POR TERCERO</b></font></td></tr>";
      echo "</tr>";  
	  
	  $condi='';
	  if($wbasedato=='farpmla')
		$condi="AND fencco = '".$wccoe[0]."'";
      
      //$hora = (string)date("H:i:s");
      //echo "1er Query Tiempo 1 : ".$hora."<br>";
      
      //ACA CREO UNA TABLA TEMPORAL CON LAS FACTURAS DEL PERIODO
      //$q = "  CREATE TEMPORARY TABLE if not exists tempo1 as "
      $q = "  SELECT fenffa, fenfac, fenfec, fenval, fentip, fenres, empnit, empnom, fencod, fencco "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000024, ".$wbasedato."_000016 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fenres = empcod "
          ."     AND fenffa = '21' "
          ."     AND fenfac = vennfa "   //
          ."     AND vencco = '".$wccoe[0]."'" //
		  ."	 ".$condi."" //
          ."     AND fencod like '".trim($wempe[0])."'"
          ."     AND fenest = 'on' "
          //."     AND ventcl = emptem "
          ."   GROUP BY fenffa, fenfac, fenfec, fenval, fentip, fenres, empnit, empnom, fencod "
          ."   ORDER BY fentip, fenres, fenfec, fenffa, fenfac ";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  $num = mysql_num_rows($err) or die (mysql_errno()." - ".mysql_error());
	  	  
	  //$hora = (string)date("H:i:s");
      //echo "Termino 1er Query Tiempo 1 : ".$hora."<br>";
	  
      echo "<table border=1>";
	  
	  $wtotgen=0;
	  $wtotncgen=0;
	  
	  $row = mysql_fetch_array($err); 
	  
	  $i=0;
	  while ($i <= $num)
	     {
		  $wtottem=0;   
		  $wtotnctem=0; 
		  $wtipemp=$row[4];
		  
		  echo "<tr class='encabezadotabla'><td colspan=9><b>Tipo de empresa: ".$wtipemp."</b></font></td></tr>";   
	      
		  while (($i<=$num) and ($wtipemp==$row[4]))
	           {
		        $wtotemp=0;  
		        $wtotncemp=0;  
		        $wempresa=$row[5];   
		        $wnomemp=$row[6]."-".$row[7];
		        if ($wtipemp != "01-PARTICULAR") 
		           { 
		            echo "<tr class='fila1'><td colspan=9><b>Empresa: ".$row[6]."-".$row[7]."<b></td></tr>";   
					echo "<tr class='encabezadotabla'>";
		            echo "<th align=CENTER><font size=2>FECHA</font></th>";
			        echo "<th align=CENTER><font size=2>FUENTE</font></th>";
			        echo "<th align=CENTER><font size=2>FACTURA</font></th>";
			        echo "<th align=CENTER><font size=2>VENTA NRO</font></th>";
			        echo "<th align=CENTER><font size=2>NIT/CEDULA</font></th>";
			        echo "<th align=CENTER><font size=2>NOMBRE CLIENTE</font></th>";
			        echo "<th align=CENTER><font size=2>VALOR</font></th>";
			        echo "<th align=CENTER><font size=2>NOTA CREDITO</font></th>";
			        echo "<th align=CENTER><font size=2>VALOR N.C.</font></th>";
					echo "</tr>";
			       }
		           
		        while (($i<=$num) and ($wtipemp==$row[4]) and ($wempresa==$row[5]))
		             {  
					 
					 if(is_int($i/2))
						$wclass='fila2';
						else
							$wclass='fila1';
			          if ($wtipemp != "01-PARTICULAR")    
			             {
				          echo "<tr class='".$wclass."'>";
		                  echo "<td>".$row[2]."</td>";
		                  echo "<td>".$row[0]."</td>";
		                  echo "<td>".$row[1]."</td>";
		                  
		                  
		                  //ACA TRAIGO EL NUMERO DE VENTA DE LA FACTURA, CUANDO ESTA ES INDIVIDUAL
		                   $q = " SELECT vennum "
		                      ."   FROM ".$wbasedato."_000016, ".$wbasedato."_000024 "
		                      ."  WHERE vennfa = '".$row[1]."'"
							  ."	AND vencco = '".$row[9]."'"
		                      ."    AND vencod = empcod "
		                      ."    AND ventcl = emptem "
		                      ."    AND empfac = 'on' ";
		                  $err_vta = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		                  $num_vta = mysql_num_rows($err_vta);
		                  if ($num_vta > 0)
		                     {
			                  $row_vta = mysql_fetch_array($err_vta);   
		                      echo "<td>".$row_vta[0]."</td>";
	                         } 
		                    else
		                       echo "<td>&nbsp</td>";
		                        
		                  //Traigo el nombre del cliente por cada factura
		                  //Si el codigo de la empresa y el codigo del responsable son iguales traigo el nombre del cliente
		                  //desde la tabla de clientes, si no, debe ser una venta a empleados entonces traigo el nombre de 
		                  //la tabla de empresas.
		                  if ($row[5] == $row[8])   
		                     {
			                  $q = " SELECT clinom, clidoc "
			                      ."   FROM ".$wbasedato."_000016,".$wbasedato."_000041 "
			                      ."  WHERE vennfa = '".$row[1]."'"
								  ."	AND vencco = '".$row[9]."'"
			                      ."    AND vennit = clidoc ";
		                     }
		                    else
		                       {
			                    $q = " SELECT empnom, empnit "
			                      ."   FROM ".$wbasedato."_000016,".$wbasedato."_000024 "
			                      ."  WHERE vennfa = '".$row[1]."'"
			                      ."    AND vencod = empcod "
								   ."	AND vencco = '".$row[9]."'"
			                      ."    AND ventcl = emptem ";    
			                   }         
		                  $err_nom = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		                  $num_nom = mysql_num_rows($err_nom);
		                  
		                  if ($num_nom != "")
		                     {
		                      $row_nom = mysql_fetch_array($err_nom); 
		                      echo "<td>".$row_nom[1]."</td>";
			                  echo "<td>".$row_nom[0]."</td>";
		                     }
		                    else
		                       {
		                        echo "<td>&nbsp</td>"; 
		                        echo "<td>&nbsp</td>"; 
	                           } 
		                  echo "<td ALIGN=RIGHT>".number_format($row[3],0,'.',',')."</td>";
		                  
		                  
		                  //ACA BUSCO SI LA FACTURA TIENE NOTA CREDITO
		                  $q = " SELECT tranum, renvca "
		                      ."   FROM ".$wbasedato."_000019,".$wbasedato."_000055,".$wbasedato."_000020,".$wbasedato."_000016 "
		                      ."  WHERE fdeffa = '21' "
		                      ."    AND fdefac = '".$row[1]."'"
		                      ."    AND fdenve = traven "
		                      ."    AND trafue = renfue "
		                      ."    AND tranum = rennum "
		                      ."    AND vennum = traven "
		                      ."    AND vencco = rencco "
							  ."	AND tracco = vencco";
							
		                  $err_nc = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		                  $num_nc = mysql_num_rows($err_nc);
		                  
		                  if ($num_nc == 0)    
		                     {
		                      echo "<td>&nbsp</td>";
		                      echo "<td>&nbsp</td>";
		                      
		                      $wtotncemp = $wtotncemp + 0;
		              		  $wtotnctem = $wtotnctem + 0;
		                      $wtotncgen = $wtotncgen + 0;
	                         }
		                    else
		                       {
			                    $row_nc = mysql_fetch_array($err_nc); 
			                    echo "<td ALIGN=CENTER>".$row_nc[0]."</td>"; 
			                    echo "<td ALIGN=RIGHT>".number_format($row_nc[1],0,'.',',')."</td>";  
			                    
			                    $wtotncemp = $wtotncemp + $row_nc[1];
		              			$wtotnctem = $wtotnctem + $row_nc[1];
		              			$wtotncgen = $wtotncgen + $row_nc[1];
		                       }     
		                  echo "</tr>";
	                     } 
			            
		              $wtotemp = $wtotemp + $row[3];
		              $wtottem = $wtottem + $row[3];
		              $wtotgen = $wtotgen + $row[3];
		              
		              $row = mysql_fetch_array($err);
		              $i=$i+1;
		             }
		        
		        if ($wtipemp != "01-PARTICULAR") 
		           {      
			        echo "<tr class='encabezadotabla'>";
				    echo "<td colspan=6><b>Total empresa: ".$wnomemp."</b></td>";
				    echo "<td ALIGN=RIGHT><b>".number_format($wtotemp,0,'.',',')."</b></td>";
				    echo "<td>&nbsp</td>";
				    echo "<td ALIGN=RIGHT><b>".number_format($wtotncemp,0,'.',',')."</b></td>";
				    
				    echo "</tr>";
			       } 
			   }    
	        echo "<tr class='encabezadotabla'>";
			echo "<td colspan=6 ><b>Total tipo de empresa: ".$wtipemp."</b></td>";
			echo "<td ALIGN=RIGHT><b>".number_format($wtottem,0,'.',',')."</b></td>";
			echo "<td>&nbsp</td>";
			echo "<td ALIGN=RIGHT><b>".number_format($wtotnctem,0,'.',',')."</b></td>";
			echo "</tr>"; 
			echo "<tr><td colspan=9 bgcolor=CCCCCC>&nbsp</td></tr>";
		 }
	  echo "<tr class='encabezadotabla'>";
	  echo "<td colspan=6><b>Total general"."</b></td>";
	  echo "<td ALIGN=RIGHT ><b>".number_format($wtotgen,0,'.',',')."</b></td>";
	  echo "<td >&nbsp</td>";
	  echo "<td ALIGN=RIGHT><b>".number_format($wtotncgen,0,'.',',')."</b></td>";
	  echo "</tr>";    
	  echo "</table>"; 
	 } 
}
?>
</body>
</html>
