<?php
include_once("conex.php");



			     



echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";
  

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
$q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgpin, cfgmai, cfgdom "
    ."   FROM ".$wbasedato."_000049 "
    ."  WHERE cfgcco = '".$wcco."'";
    
$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
$row = mysql_fetch_array($res);

$wnit_pos  =$row[0];
$wnomemppos=$row[1];
$wtipregiva=$row[2];
$wtel_pos  =$row[3];
$wdir_pos  =$row[4];
$wpagintern=$row[5];
$wemail_pos=$row[6];
$wteldompos=$row[7];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


//$wempresa=explode("-",$wnomnit);

$wfuedoc1=explode("-",$wfuedoc);
if ($wfuedoc1 != "")
   $wfuedoc=$wfuedoc1[0];
	   
$wno_existe="off";   
    
/////////////////////////////////////
if ($wnrodoc != "")
   {
	$q = " SELECT cardes, carfpa "
	    ."   FROM ".$wbasedato."_000040 "
	    ."  WHERE carfue = '".$wfuedoc."'";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);
	
	if ($num > 0)
	   {
		$row = mysql_fetch_array($res);    
	    $wnomfue = $row[0];
	    $wfpa=$row[1];
	    
	    $q = " SELECT ".$wbasedato."_000020.fecha_data, rencod, rennom, renvca, rencaj, renusu, rencco, empnit "
	        ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000024 "
	        ."  WHERE renfue = '".$wfuedoc."'"
	        ."    AND rennum = ".$wnrodoc
	        ."    AND rencod = empcod ";
	        //."    AND rencco = '".$wcco."'";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
		
		if ($num > 0)
	       {
		    $row = mysql_fetch_array($res); 
		       
 		    $wfecdoc = $row[0];
 		    $wcodemp = $row[1];
 		    $wnomemp = $row[2];
 		    $wvaldoc = $row[3]; 
 		    $wcajdoc = $row[4];
 		    $wusudoc = $row[5];
 		    $wccodoc = $row[6];
 		    $wnitemp = $row[7];
 		   } 
 		  else
 		    $wno_existe="on";    
       } 
	       
    echo "<center><table border=0>";   
	 
    if ($wno_existe=="off") 
       {
	    //IMPRESION CON EL LOGO AL PRINCIPIO
		echo "<tr><td align=left colspan=3 rowspan=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=357 HEIGHT=140></td></tr>";
		echo "<tr><td colspan=2><font size=3><b>".$wnomfue."</b></font></td></tr>";
		echo "<tr><td colspan=2><font size=3><b>Nro: ".$wnrodoc."</b></font></td></tr>";
		echo "<tr><td colspan=2><font size=3><b>Fecha: ".$wfecdoc."</b></font></td></tr>";
		echo "<tr><td colspan=2><font size=3><b>Valor: ".number_format($wvaldoc,0,'.',',')."</b></font></td></tr>";
		
		echo "<tr><td align=center colspan=5><font size=3><b>".$wnomemppos."</b></font></td></tr>";
		echo "<tr><td align=center colspan=5><font size=3><b>Nit. : ".$wnit_pos."</b></font></td></tr>";
		
		echo "<br>";
		
		echo "<tr>&nbsp</tr><tr>&nbsp</tr><tr>&nbsp</tr>";
		echo "<tr>";
		echo "<td align=left ><font size=3><b>A favor de: ".trim($wnitemp)." - ".$wnomemp."</b></font></td>";
		echo "</tr>";
		
		
		echo "<tr><td colspan=5><b>=================================================================================</b></td></tr>";
		echo "<tr><td align=center colspan=5><b>D E T A L L E &nbsp&nbsp  D E &nbsp&nbsp  F A C T U R A S</b></td></tr>";
		echo "<tr><td colspan=5><b>=================================================================================</b></td></tr>";
		echo "</table>";
		
		echo "<left><table border=0>"; 
		
		echo "<th align=left>Factura</th>";
		echo "<th align=right>Vr Cancelado</th>";
		echo "<th align=left>Concepto</th>";
		echo "<th align=right>Vr Concepto</th>";
		echo "<th align=right>Vr Recibido</th>";
		
		$q = " SELECT rdefac, rdevca, rdecon, rdevco "
		    ."   FROM ".$wbasedato."_000021 "
		    ."  WHERE rdefue = '".trim($wfuedoc)."'"
		    ."    AND rdenum = ".$wnrodoc;
		    //."    AND rdecco = '".$wcco."'";
		        
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);
		if ($num > 0)
	       {
		    $wtotvca=0;
		    $wtotvco=0;   
		      
		    for ($i=1;$i<=$num;$i++)
	           { 
		        $row = mysql_fetch_array($res); 
		        echo "<tr>";   
		        echo "<td>".strtoupper($row[0])."</td>";                                                             //factura
		        echo "<td align=right>".number_format($row[1],0,'.',',')."</td>";                                    //Valor cancelado
		        if ($row[2] != "NA - NO APLICA")   echo "<td>".$row[2]."</td>";                                      //Concepto de cartera
		        if ($row[3] != 0 or $row[3] != "") echo "<td align=right>".number_format($row[3],0,'.',',')."</td>"; //Valor concepto
		        echo "<td align=right>".number_format(($row[1]+$row[3]),0,'.',',')."</td>";                          //Valor recibido
		        echo "</tr>"; 
		        
		        $wtotvca = $wtotvca + $row[1];
		        $wtotvco = $wtotvco + $row[3];
		       }    
	                
		    echo "<tr>&nbsp</tr>";   
	        echo "<tr><b>";
	        echo "<td><b>Totales: </b></td>";
	        echo "<td align=right><b>".number_format($wtotvca,0,'.',',')."</b></td>"; 
	        echo "<td><b>&nbsp</b></td>"; 
	        echo "<td align=right><b>".number_format($wtotvco,0,'.',',')."</b></td>"; 
	        echo "<td align=right><b>".number_format(($wtotvca+$wtotvco),0,'.',',')."</b></td>"; 
	        echo "</tr>";
	       }  
	      else
	         {
		      echo "<tr><tr><tr>";
	          echo "<tr><td colspan=5 align=center><b>EL DOCUMENTO NO PERTENECE AL CENTRO DE COSTO AL QUE USTED ESTA MATRICULADO</b></td></tr>"; 
	          echo "<tr><td colspan=5 align=center><b>POR LO QUE NO SE PUEDE MOSTRAR EL DETALLE LAS FACTURAS</b></td></tr>";
	          echo "<tr><tr><tr>";
	         } 
	       
	    if ($wfpa=='on')
	       {
		    $q = "SELECT rfpfpa, fpades, rfpdan, rfpobs,  rfpvfp "
		        ."  FROM ".$wbasedato."_000022, ".$wbasedato."_000023 "
		        ." WHERE rfpfue = '".trim($wfuedoc)."'"
		        ."   AND rfpnum = ".$wnrodoc
		        ."   AND rfpfpa = fpacod "
		        ."   AND rfpcco = '".$wcco."'";  
		        
		    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		    $num = mysql_num_rows($res);
		    if ($num > 0)
	           {    
		        echo "<tr><td colspan=5><b>=================================================================================</b></td></tr>";
				echo "<tr><td align=center colspan=5><b>F O R M A   D E   P A G O</b></td></tr>";
				echo "<tr><td colspan=5><b>=================================================================================</b></td></tr>";  
				
				echo "<th align=left colspan=2>Formas de Pago</th>";
				echo "<th align=left>Documento Anexo</th>";
				echo "<th align=left>Entidad</th>";
				echo "<th align=right>Valor</th>";
				
				for ($i=1;$i<=$num;$i++)
		           { 
			        $row = mysql_fetch_array($res);
			        
			        echo "<tr>";
			        echo "<td>".$row[0]."</td>";
			        echo "<td>".$row[1]."</td>";
			        
			        if ($row[2] != "" and $row[2] != " ") 
			           echo "<td>".$row[2]."</td>";
			          else
			             echo "<td>&nbsp</td>"; 
			        if ($row[3] != "" and $row[3] != " ") 
			           echo "<td>".$row[3]."</td>";
			          else
			             echo "<td>&nbsp</td>";     
			        echo "<td align=right>".number_format($row[4],0,'.',',')."</td>";
			        echo "</tr>";
		           } 
		         
		        echo "<tr><td colspan=5>&nbsp</td></tr>";
		        echo "<tr><td colspan=5>&nbsp</td></tr>";     
		        //echo "<tr><td colspan=5>&nbsp</td></tr>"; 
		       }    
	       }
	    $wfecha=date("Y-m-d");   
	    $hora = (string)date("H:i:s");
	    
	    echo "<tr><td colspan=5><b>=================================================================================</b></td></tr>";
	    
	    echo "<tr>";
	    echo "<td>Fecha: ".$wfecha."</td>";
	    echo "<td>Hora: ".$hora."</td>";  
	    echo "<td>Usuario: ".$wusudoc."</td>";
	    echo "<td>Caja: ".$wcajdoc."</td>";
	    echo "<td>Sucursal: ".$wccodoc."</td>";
	    echo "<tr><td colspan=5><b>-----------------------------------------------------------------------------------------------------------------------------------------------</b></td></tr>";   
       }
      else
         {
	      echo "<br><br><br><br><br><br>";
	      echo "<tr><td colspan=5 align=center><font size=5><b>!!! ATENCION !!!  EL DOCUMENTO NO EXISTE</b></font></td></tr>"; 
          echo "<br><br><br><br><br><br>";
          
          //echo "<form name='recibos_y_notas' action='recibos_y_notas.php' method=post>";
          
          echo "<tr>";
		  echo "<tr><td colspan=5 align=center><font size=5><b>PRESIONE (ALT+F4) </b></font></td></tr>";   
		  echo "</tr>";
         }    
  } 
echo "</table>";    
?>
