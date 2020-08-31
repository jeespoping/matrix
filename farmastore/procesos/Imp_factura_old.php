<?php
include_once("conex.php");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!! ATENCION ¡¡¡ SI SE HACE ALGUN CAMBIO EN ESTE PROGRAMA TAMBIEN SE DEBE DE HACER EN EL PROGRAMA copia_factura.php y VICEVERSA
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



			      or die("No se ralizo Conexion");


  
echo "</table>";
echo "<left><table border=0>";
 

//=======================================================================
//ACA TRAIGO EL NOMBRE DEL VENDEDOR
$q = " SELECT venusu, descripcion "
    ."   FROM farstore_000016, usuarios "
    ."  WHERE vennum = ".$wnrovta
    ."    AND venusu = codigo ";
$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
$num = mysql_num_rows($res);

if ($num > 0)
   {
	$row = mysql_fetch_array($res);
	$wcodusu = $row[0];
	$wnomusu = $row[1];
   }
//=======================================================================


echo "<tr><td colspan=5>&nbsp</td></tr>"; 

/////////////////////////////////////
if ($wnrofac > 0)
   {
	//IMPRESION CON EL LOGO AL PRINCIPIO
	echo "<tr><td align=center colspan=5><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=510 HEIGHT=200></td></tr>";
	echo "<tr><td colspan=5 align=center><font size=6>LAS AMERICAS FARMA STORE S.A. </font></td></tr>";
	echo "<tr><td colspan=5 align=center><font size=5>Nit. : 900016094-7 </font></td></tr>";
	echo "<tr><td colspan=5 align=center><font size=5>Tel. : 321-11-11  Cra 48 # 15 Sur 160</font></td></tr>";
	echo "<tr><td colspan=5 align=center><font size=5>REGIMEN COMUN </font></td></tr>";
	echo "<tr><td colspan=3 align=left><font size=5>Hora: ".$whora_tempo."</font></td>";
	echo "<td colspan=2 align=right><font size=5>Fecha: ".$wfecha_tempo."</font></td></tr>";
   
	echo "<tr><td colspan=5><font size=5>Factura de Venta Nro: ".$wnrofac."</font></td></tr>";   
	if ($wnrorec > 0)
       echo "<tr><td colspan=5><font size=5>Recibo Nro: ".$wnrorec."     Venta Nro: ".$wnrovta."</font></td></tr>";
    $pos = strpos($wnomnit,"-");
    $wemp = substr($wnomnit,0,$pos-1);   
  
    $pos1 = strpos($wnomnit,"-",$pos+1);
    $wnitemp = substr($wnomnit,$pos+1,$pos1-3); 
    
    $wnomemp = substr($wnomnit,$pos1+1,strlen($wnomnit)); 
    
	echo "<tr><td colspan=5><font size=5>Responsable: ".$wnomemp."</font></td></tr>";
	echo "<tr><td colspan=5><font size=5>Cédula/Nit: ".$wnitemp."</font></td></tr>";

	echo "<tr><td colspan=5><font size=5>Beneficiario: ".$wnompac."</font></td></tr>";
	echo "<tr><td colspan=5><font size=5>Cédula/Nit: ".$wdocpac."</font></td></tr>";

	//Si es un domicilio imprimo la dirección y el telefono del cliente
	if ($wtipven=="Domicilio")
	   {
		echo "<tr><td colspan=5><font size=5>Dirección: ".$wdirpac."</font></td></tr>";
	    echo "<tr><td colspan=5><font size=5>Telefono: ".$wte1pac."</font></td></tr>";
	   }

	echo "<tr><td colspan=5>&nbsp</td></tr>";
	echo "<th align=left bgcolor=DDDDDD><font size=5>Descripción</font></th>";
	echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>V/U</font></th>";
	echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>Cant.</font></th>";
	echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>% Iva</font></th>";
	echo "<th bgcolor=DDDDDD><font size=5>Total</font></th>";

	
	//ACA TRAIGO EL ENCABEZADO DE LA VENTA 
	$q = " SELECT venvto, venviv, vencop, vencmo, vendes, venrec "
	    ."   FROM farstore_000016 "
	    ."  WHERE vennum = ".$wnrovta;
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	  {
	   $row = mysql_fetch_array($res);
	   $wtotal=$row[0];
	   $wtotiva=$row[1];
	   $wtotcopcmo=$row[2]+$row[3];
	   $wtotdes=$row[4];
	   $wtotrec=$row[5];
	  }

	//ACA TRAIGO TODO EL DETALLE DE LA VENTA 
	//$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan)+((vdevun*vdecan)*(vdepiv/100)) "
	$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan) "
	    ."   FROM farstore_000017, farstore_000001, farstore_000002 "
	    ."  WHERE vdenum = ".$wnrovta
	    ."    AND vdeart = artcod "
	    ."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";
	     
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	 {
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res);
	      
	      echo "<tr>";
	      echo "<td align=left><font size=5>".substr($row[1],0,21)."</font></td>";
	      //echo "<td align=right><font size=5>".number_format($row[4]+($row[6]/$row[3]),0,'.',',')."</font></td>";
	      echo "<td align=right><font size=5>".number_format($row[4],0,'.',',')."</font></td>";
	      echo "<td align=right><font size=5>".$row[3]."</font></td>";
	      echo "<td align=right><font size=5>".$row[5]."</font></td>";
	      echo "<td align=right><font size=5>".number_format(($row[7]),0,'.',',')."</font></td>";
	      echo "</tr>";
	     }   
      echo "<tr><td colspan=4><font size=5><b>Total factura sin Iva</b></font></td>";
      echo "<td align=right><font size=5>".number_format(($wtotal-$wtotiva+$wtotdes),0,'.',',')."</font></td></tr>";  
      echo "<tr><td colspan=4><font size=5><b>Total Iva</b></font></td>";
      echo "<td align=right><font size=5>".number_format(($wtotiva),0,'.',',')."</font></td></tr>";  
	  
      if ($wtotdes > 0)  //Si tiene descuento lo imprimo
	     {
		  echo "<tr><td colspan=4><font size=5>Descuento: </font></td>";
	      echo "<td colspan=1 align=right><font size=5>".number_format($wtotdes,0,'.',',')."</font></td></tr>";   
         }
         
      if ($wtotrec > 0)  //Si tiene recargo lo imprimo
	     {
		  echo "<tr><td colspan=4><font size=5>Recargo: </font></td>";
	      echo "<td colspan=1 align=right><font size=5>".number_format($wtotrec,0,'.',',')."</font></td></tr>";   
         }   
      
      echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
	  //cho "<TR><TD colspan=5>__________________________________________________________________</TD></TR>";  
	  
      echo "<tr><td colspan=3><font size=6><b>Total a Pagar</b></font></td>";
      echo "<td align=right colspan=2><font size=6>".number_format(($wtotal),0,'.',',')."</font></td></tr>";
	  
	  
      if ($fk > 0)
        {
         if ($wcuotamod > 0)
	        {
	         echo "<tr><td colspan=4><font size=5>Cuota Mod. o Franquicia: </font></td>";
	         echo "<td colspan=1 align=right><font size=5>".number_format($wtotcopcmo,0,'.',',')."</font></td></tr>";
	        } 
		    
	     echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
	     //FORMA DE PAGO
	     echo "<tr><td colspan=5>&nbsp</td></tr>";
	     echo "<th bgcolor=DDDDDD align=left colspan=2><font size=5>F-PAGO</font></th>";
	     echo "<th bgcolor=DDDDDD align=right><font size=5>Doc.Anexo</font></th>";
	     echo "<th bgcolor=DDDDDD align=right><font size=5>Observ.</font></th>";
	     echo "<th bgcolor=DDDDDD align=right><font size=5>Valor</font></th>";
		  
	     for ($i=1;$i<=$fk;$i++)
	        {
		     if ($wvalfpa[$i] > 0)
		        {
		         echo "<tr>";
		         echo "<td align=left colspan=2><font size=5>".$wfpa[$i]."</font></td>";
		         echo "<td align=right><font size=5>".$wdocane[$i]."</font></td>";
		         echo "<td align=right><font size=5>".$wobsrec[$i]."</font></td>";
		         echo "<td align=right><font size=5>".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
		         echo "</tr>";
	            }
	        }
	     echo "<tr><td colspan=4><font size=5>Cambio: </font></td>";
	     echo "<td colspan=1 align=right><font size=5>".number_format($wvalcam,0,'.',',')."</font></td></tr>";
	     echo "<tr><td colspan=5>&nbsp</td></tr>";
	    }  
	  echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
      echo "<tr>";
      echo "<td colspan=3><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>"; 
      echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";
      //echo "<td colspan";
      
      //Si es un domicilio imprimo el mensajero
      if ($wtipven=="Domicilio")
	     echo "<tr><td colspan=5><font size=3>Mensajero: ".$wmensajero."</font></td></tr>";
      
      echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
      echo "<tr><td colspan=5 align=center><font size=5>Resolución Nro: 110000217293 del 21 de Julio de 2005, </font></td></tr>";    
      echo "<tr><td colspan=5 align=center><font size=5>factura 001 a la factura 100000.</font></td></tr>";    
      echo "<tr><td colspan=5 align=center><font size=5>Esta factura cambiaria de compraventa se asimila en </font></td></tr>";    
      echo "<tr><td colspan=5 align=center><font size=5>todos sus efectos a una letra de cambio, Art. 621 y </font></td></tr>";    
      echo "<tr><td colspan=5 align=center><font size=5>SS, 671 y SS 772, 773, 770 y SS del código de comercio.</font></td></tr>";    
      echo "<tr><td colspan=5 align=center><font size=5>Factura impresa por computador cumpliendo con los</font></td></tr>";    
      echo "<tr><td colspan=5 align=center><font size=5>requisitos del Art. 617 del E.T.</font></td></tr>";    
      echo "<tr><td colspan=5>&nbsp</td></tr>";
      echo "<tr><td colspan=5>&nbsp</td></tr>";
      
      echo "<tr>";
      echo "<tr><td colspan=5 align=center><font size=5><b>Visitenos en www.lafarmastore.com</b></font></td></tr>";
      echo "<tr><td colspan=5 align=center><font size=5><b>Para sus domicilios comuniquese al 321-59-00</b></font></td></tr>";
      echo "<tr>";
     }    		   
   }
  else  //No se genero factura
     {
	  if ($wcuotamod > 0)
	    {
		 //IMPRESION CON EL LOGO AL PRINCIPIO
		 echo "<tr><td align=center colspan=5><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=510 HEIGHT=200></td></tr>";
		 echo "<tr><td colspan=5 align=center><font size=6>LAS AMERICAS FARMA STORE S.A. </font></td></tr>";
		 echo "<tr><td colspan=5 align=center><font size=5>Nit. : 900016094-7 </font></td></tr>";
		 echo "<tr><td colspan=5 align=center><font size=5>Tel. : 321-11-11  Cra 48 # 15 Sur 160</font></td></tr>";
		 echo "<tr><td colspan=5 align=center><font size=5>REGIMEN COMUN </font></td></tr>";
		 echo "<tr><td colspan=3 align=left><font size=5>Hora: ".$whora_tempo."</font></td>";
		 echo "<td colspan=2 align=right><font size=5>Fecha: ".$wfecha_tempo."</font></td></tr>";
   
	     if ($wnrorec > 0)
	        echo "<tr><td colspan=4><font size=5>Recibo Nro: ".$wnrorec."     Venta Nro: ".$wnrovta."</font></td></tr>";
		 echo "<tr><td colspan=4><font size=5>Responsable: ".$wnomnit."</font></td></tr>";
		 echo "<tr><td colspan=4><font size=5>Cédula/Nit: ".$wemp."</font></td></tr>";

		 echo "<tr><td colspan=4><font size=5>Beneficiario: ".$wnompac."</font></td></tr>";
		 echo "<tr><td colspan=4><font size=5>Cédula/Nit: ".$wdocpac."</font></td></tr>";

         echo "<tr><td colspan=4>&nbsp</td></tr>";
		 echo "<th align=left bgcolor=DDDDDD colspan=3><font size=5>Descripción</font></th>";
		 echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>Cant.</font></th>";
		 
		 //ACA TRAIGO TODA LA VENTA 
		 //$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan)+((vdevun*vdecan)*(vdepiv/100)) "
		 $q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan) "
		     ."   FROM farstore_000017, farstore_000001, farstore_000002 "
		     ."  WHERE vdenum = ".$wnrovta
		     ."    AND vdeart = artcod "
		     ."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";
		    
		 $res = mysql_query($q,$conex);
		 $num = mysql_num_rows($res);

		 if ($num > 0)
		   {
		    for ($i=1;$i<=$num;$i++)
		       {
		        $row = mysql_fetch_array($res);
		      
                echo "<tr>";
		        echo "<td align=left colspan=2><font size=5>".substr($row[1],0,30)."</font></td>";
		        echo "<td align=right colspan=2><font size=5>".$row[3]."</font></td>";
		        echo "</tr>";
		       }   
	        echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
		  
		    if ($fk > 0)
		       {
		        if ($wcuotamod > 0)
			      {
			       echo "<tr><td colspan=3><font size=5>Cuota Mod. o Franquicia: </font></td>";
			       echo "<td colspan=1 align=right><font size=5>".number_format($wcuotamod,0,'.',',')."</font></td></tr>";
			      } 
				    
			    echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
			    //FORMA DE PAGO
			    echo "<tr><td colspan=4>&nbsp</td></tr>";
			    echo "<th bgcolor=DDDDDD align=left colspan=1><font size=5>F-PAGO</font></th>";
			    echo "<th bgcolor=DDDDDD align=right><font size=5>Doc.Anexo</font></th>";
			    echo "<th bgcolor=DDDDDD align=right><font size=5>Observ.</font></th>";
			    echo "<th bgcolor=DDDDDD align=right><font size=5>Valor</font></th>";
				  
			    for ($i=1;$i<=$fk;$i++)
			       {
			        echo "<tr>";
			        echo "<td align=left colspan=1><font size=5>".$wfpa[$i]."</font></td>";
			        echo "<td align=right><font size=5>".$wdocane[$i]."</font></td>";
			        echo "<td align=right><font size=5>".$wobsrec[$i]."</font></td>";
			        echo "<td align=right><font size=5>".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
			        echo "</tr>";
			       }
			    echo "<tr><td colspan=3><font size=5>Cambio: </font></td>";
			    echo "<td colspan=1 align=right><font size=5>".number_format($wvalcam,0,'.',',')."</font></td></tr>";
			    echo "<tr><td colspan=4>&nbsp</td></tr>";
			   }  
		    echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
		    echo "<tr>";
		    echo "<td colspan=2><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>"; 
		    echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";
		    echo "</tr>";
		    
		    echo "<tr>";
            echo "<tr><td colspan=5 align=center><font size=5><b>Visitenos en www.lafarmastore.com</b></font></td></tr>";
            echo "<tr><td colspan=5 align=center><font size=5><b>Para sus domicilios comuniquese al 321-59-00</b></font></td></tr>";
            echo "<tr>"; 
		   }
	    }
     } 
echo "</table>";    
?>
