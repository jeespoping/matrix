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
* 2012-07-11 Camilo Zapata, se corrigió el query para que tenga en cuenta la fecha data de la tabla 19 y así evitar inconsistencias con de las ventas con las facturas
* * 2012-07-11 Camilo Zapata, el filtro por centro de costos se habilito para que se activara solo en el caso de que el reporte
* 							se genere en farmpla.(linea 107)
* 2012-07-11 Camilo Zapata, se le dió el estilo correcto al reporte.
* 2012-06-15 Camilo Zapata, se hizo una correción en el query ppal(linea 165) para que filtre tambien las facturas por el cco consultados.
*/
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
  $key = substr($user,2,strlen($user));
  

  include_once("root/comun.php");
  

  echo "<form action='facturado_x_linea_x_tipo_de_iva.php' method=post>";

  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
  $wfecha=date("Y-m-d");   
  $wactualiz='2012-08-22';
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  encabezado("REPORTE DE FACTURACION", $wactualiz, "logo_".$wbasedato.".png");
  echo "<center><table border=2>";
  /*echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURACION</b></font></td></tr>";*/
  
  if (!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wtar))
   {
	echo "<tr class='fila1'>";  
    echo "<td align=center><b>Fecha Inicial: </b>";campoFechaDefecto( "wfecini", $wfecha );"</td>";
    echo "<td align=center><b>Fecha Final: </b>";campoFechaDefecto( "wfecfin", $wfecha );"</td>";
    echo "</tr>";
    
    //CENTRO DE COSTO
    $q =  " SELECT ccocod, ccodes "
		 ."   FROM ".$wbasedato."_000003 "
		 ."  ORDER BY 1 ";
			 	 
	$res = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());;
	$num = mysql_num_rows($res);
	    
	echo "<tr class='fila2'><td align=center>SELECCIONE LA SUCURSAL: ";
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
			 	 
	$res = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
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
      echo "<td align=center><b>Fecha Inicial (AAAA-MM-DD): </b>".$wfecini."</td>";
      echo "<td align=center><b>Fecha Final (AAAA-MM-DD): </b>".$wfecfin."</td>";
      echo "</tr>";
      echo "<tr class='fila2'>";
      echo "<td align=center colspan=2><b>SUCURSAL: </b>".$wcco."</td>";
      echo "</tr>";  
	  
      /*
      
      $hora = (string)date("H:i:s");
      echo "1er Query Tiempo 1 : ".$hora."<br>";
      
      
      //ACA CREO UNA TABLA TEMPORAL CON LAS FACTURAS DEL PERIODO
      $q = "  CREATE TEMPORARY TABLE if not exists tempo1 as "
          ."  SELECT fdenve "
          ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000019 "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fenffa = fdeffa "
          ."     AND fenfac = fdefac "
          ."   GROUP BY fdenve ";
      $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
      
      echo $q."<br>";
      
      
      $hora = (string)date("H:i:s");
      echo "Termino 1er Query Tiempo 1 : ".$hora."<br>";
      */
      
      /////////////////////////////////////////////////////////////////
	  // REPORTE DE VENTAS POR LINEA POR TIPO DE IVA
	  /////////////////////////////////////////////////////////////////
	  echo "<br>";
	  echo "<br>";
	  echo "<br>";
	  echo "<center><table border=2>";
	  echo "<tr class='encabezadotabla'><td align=center><font size=6 text><b>FACTURADO POR LINEA POR TIPO DE IVA</b></font></td></tr>";
      
	  /*
	  $hora = (string)date("H:i:s");
      echo "2do Query Tiempo 2 : ".$hora."<br>";
	  
	  
	  $q = "  SELECT grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004, tempo1 "
          ."   WHERE vennum = fdenve "
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."     AND vencco = '".$wccoe[0]."'"
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."   GROUP BY grucod, grudes, arttiv, artiva ";
          
      echo $q."<br>";
      */
      
      
      //$hora = (string)date("H:i:s");
      //echo "1er Query Tiempo 1 : ".$hora."<br>";
      $condi='';
      if($wbasedato=='farpmla')
		$condi= " AND fencco = '".$wccoe[0]."'";
      
      
      $q = "  SELECT grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      ."    FROM ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004, ".$wbasedato."_000018, ".$wbasedato."_000019 a "
          ."   WHERE fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
		  ."	 AND a.fecha_data=fenfec "
          ."     AND fdenve = vennum "
          ."     AND vennum = vdenum "
          ."     AND vdeart = artcod "
          ."     AND vencco = '".$wccoe[0]."'"
		  ."	".$condi.""
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."     AND fenfec between '".$wfecini."'"
          ."     AND '".$wfecfin."'"
          ."     AND fenffa = fdeffa "
          ."     AND fenfac = fdefac "
          ."     AND fenfac = vennfa "  // se agrega esta linea ya que no existia join entra la 16 y la 18 y sacaba otras ventas. 2009/07/09   tavo-juan david
          ."     AND vennfa <>''"  // se agrega esta linea ya que se necesita que traiga las ventas que tienen facturas. 2009/07/09   tavo-juan david 
          ."   GROUP BY grucod, grudes, arttiv, artiva ";    
      
      $err = mysql_query($q,$conex) or die("error ".mysql_errno()."_ en el query:".$q."_".mysql_error());
	  $num = mysql_num_rows($err);
	  
	  //$hora = (string)date("H:i:s");
      //echo "Termino 1er Query Tiempo 1 : ".$hora."<br>";
	  
	  echo "<table border=1>";
	  
	  echo "<tr class='encabezadotabla'>";
	  echo "<th align=CENTER><font size=2>LINEA</font></th>";
	  echo "<th align=CENTER><font size=2>DESCRIPCION</font></th>";
	  echo "<th align=CENTER><font size=2>TIPO IVA</font></th>";
	  echo "<th align=CENTER><font size=2>% IVA</font></th>";
	  echo "<th align=CENTER><font size=2>VENTA BRUTA</font></th>";
	  echo "<th align=CENTER><font size=2>VALOR IVA</font></th>";
	  echo "<th align=CENTE><font size=2>TOTAL FACTURADO</font></th>";
	  echo "</tr>";
	  
	  
	  $wtotvenbru = 0;
	  $wtotveniva = 0;
	  $wtotventot = 0;
	  
	  for ($i=0;$i<$num;$i++)
	     {
		  if(is_int($i/2))
			$wclass='fila2';
				else
					$wclass='fila1';
	      $row = mysql_fetch_array($err);
	      echo "<tr class='".$wclass."'>";
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
	  echo "<tr class='encabezadotabla'>";  
	  echo "<td colspan=4>TOTALES</td>";
	  echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	  echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	  echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	  echo "<tr>";
	   
	  echo "</table>"; 
	 } 
}
?>
</body>
</html>
