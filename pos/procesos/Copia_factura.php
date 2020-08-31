<?php
include_once("conex.php");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// !!! ATENCION ¡¡¡ SI SE HACE ALGUN CAMBIO EN ESTE PROGRAMA TAMBIEN SE DEBE DE HACER EN EL PROGRAMA imp_factura.php y VICEVERSA
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



			     



$wfecha=date("Y-m-d");
$hora = (string)date("H:i:s");
$wcf="DDDDDD";                //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";               //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF";              //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366";              //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

echo "<form name='copia_factura' action='Copia_factura.php' method=post>";

echo "<center><table border=2>";


echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>"; 


$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user));

///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
//ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE cjeusu = '".$wusuario."'"
       ."    AND cjeest = 'on' ";
       
  $res = mysql_query($q,$conex);
  $num = mysql_num_rows($res);
  if ($num > 0)
     {
      $row = mysql_fetch_array($res);
      
      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0])); 
      
      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));
     }
///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////

if (!isset($wfecini) or !isset($wfecfin))
   {
	echo "<center><table border=2>";
	echo "<tr align=center>"; 
    echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
    echo "<tr><td align=center colspan=2 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REIMPRESION DE FACTURAS</b></font></td></tr>";
    echo "<tr>";  
    echo "<td bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecha." SIZE=10></td>";
    echo "<td bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecha." SIZE=10></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";
    echo "<td align=center colspan=4 bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit
    echo "</tr>";
   }
  else  
     {
	  if (!isset($wnrovta) or !isset($wfuefac) or !isset($wnrofac))
	     { 
		  $q = "SELECT vennum, venffa, vennfa, venvto, ventve, vennmo "
              ."  FROM ".$wbasedato."_000016 "
              ." WHERE venfec between '".$wfecini."' AND '".$wfecfin."'"
              ."   AND vencaj = '".$wcaja."'"
              ."   AND venest = 'on' "; 
          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
          $num = mysql_num_rows($res);

          if ($num > 0)
             {
	          echo "<center><table border=2>";
	          echo "<tr align=center>"; 
              echo "<tr><td align=center rowspan=2 colspan=3><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
              echo "<tr><td align=center colspan=4 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REIMPRESION DE FACTURAS</b></font></td></tr>";
              echo "<tr>";  
              echo "<td align=center bgcolor=".$wcf." colspan=7><b><font text color=".$wclfg.">CAJA: </font></b>".$wcaja."</td>";
              echo "</tr>";
              echo "<tr>";
              echo "<td align=center bgcolor=".$wcf." colspan=4><b><font text color=".$wclfg.">Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
              echo "<td align=center bgcolor=".$wcf." colspan=3><b><font text color=".$wclfg.">Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
              echo "<tr><td align=center colspan=7 bgcolor=".$wcf2."><font size=4 text color=#FFFFFF><b>DOCUMENTOS DEL PERIODO</b></font></td></tr>";
              echo "</tr>";   
	             
              echo "<th bgcolor=".$wcf.">Mvto Nro</th>";
	          echo "<th bgcolor=".$wcf.">Venta Nro</th>";
	          echo "<th bgcolor=".$wcf.">Fuente Fra</th>";
	          echo "<th bgcolor=".$wcf.">Factura Nro</th>";
	          echo "<th bgcolor=".$wcf.">Valor Venta</th>";
	          echo "<th bgcolor=".$wcf." colspan=2 align=left>Tipo Venta</th>";
	          
	          $wtotalven=0;
              for ($i=1;$i<=$num;$i++)
                  {
	               $row = mysql_fetch_array($res);   
	               echo "<tr>";
	               echo "<td align=center>".$row[5]."</td>";
	               echo "<td align=center>".$row[0]."</td>";
	               echo "<td align=center>".$row[1]."</td>";
	               echo "<td align=center>".$row[2]."</td>";
	               echo "<td align=right>".number_format(($row[3]),0,'.',',')."</td>";
	               echo "<td>".$row[4]."</td>";
	               echo "<td align=center><font size=3><A href='copia_factura.php?wnrovta=".$row[0]."&amp;wfuefac=".$row[1]."&amp;wnrofac=".$row[2]."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wbasedato=".$wbasedato."'> Seleccionar</A></font></td>";
	               echo "</tr>";
	               
	               $wtotalven=$wtotalven+$row[3];
                  }
               echo "<th bgcolor=".$wcf." colspan=4 align=right>Total Ventas</th>";   
               echo "<th bgcolor=".$wcf." align=right>".number_format($wtotalven,2,'.',',')."</th>";   
               echo "<th bgcolor=".$wcf." colspan=2 align=right></th>"; 
               
             } 
            else
               { 
	            echo "<input type='HIDDEN' name= 'wcaja' value='".$wcaja."'>";   
                echo "<br><br><br>";
                echo "<tr><td align=center colspan=4 bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>NO EXISTE MOVIMIENTO PARA ESTA CAJA EN EL PERIODO SELECCIONADO</b></font></td></tr>";   
                echo "<td align=center colspan=4 bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit
               } 
          echo "</table>";      
         }  
       else
          { 
	        //=======================================================================   
	        //ACA ESTA SELECCIONADA LA FACTURA A IMPRIMIR  
	        //=======================================================================
	        echo "</table>";
	        echo "<left><table>";
	        
	        
	        //ACA TRAIGO SI EL RESPONSABLE DE LA FACTURA ES UNA EMPRESA Y SI SI ENTONCES VERIFICO QUE SI LA EMPRESA SE FACTURA EN BLOQUE.
	        if ($wnrofac != "")
	           {
	            $q = "    SELECT empfac "
	                ."      FROM ".$wbasedato."_000018, ".$wbasedato."_000024 "
	                ."     WHERE fenfac = '".$wnrofac."'"
	                ."       AND fencod = empcod "
	                ."       AND empfac = 'off' ";
	            $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error()); 
	            $num = mysql_num_rows($res);
	            if ($num > 0)
	               {   
	                $row = mysql_fetch_array($res);
	                $wfacablocada=$row[0];
                   }
                  else
                     $wfacablocada="";  
               }
              else
                 $wfacablocada="";  
	        
	        //ACA TRAIGO LA FACTURA, EL RECIBO Y LA VENTA
			$q = "         SELECT rdenum, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
			if ($wnrofac != "") //Para verificar que la factura si exista y no imprima un documento descuadrado
			   $q = $q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000041, ".$wbasedato."_000018 ";
			  else
			     $q = $q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000041 "; 
			$q = $q."       WHERE vennum = '".$wnrovta."'";
			$q = $q."         AND venusu = codigo ";
			$q = $q."         AND vennum = rdevta ";
			$q = $q."         AND vennit = clidoc ";
			$q = $q."         AND vencod = empcod ";
			$q = $q."         AND ventcl = emptem ";
			if ($wnrofac != "")
			   {
			    $q = $q."     AND vennfa = fenfac ";
                $q = $q."     AND venvto = fenval "; 
               } 
			$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$num = mysql_num_rows($res);
 
			if ($num > 0)
			   {
				$row = mysql_fetch_array($res);
				$wnrorec      = $row[0];
				$wfecha_tempo = $row[1];
				$whora_tempo  = $row[2];
				$wdocpac      = $row[3];
				$wnompac      = $row[4];
				$wtelpac      = $row[5];
				$wdirpac      = $row[6];
				$wmaipac      = $row[7];
				$wnomnit      = $row[8]."-".$row[9]."-".$row[10];
				$wcodusu      = $row[11];
				$wnomusu      = $row[12];
				$wtipven      = $row[13];
				$wcuotamod    = $row[14];
				$wcaja        = $row[15];
				$wmensajero   = $row[16];
				$wtipcli      = $row[17];
			   }
			  else
			     {
				  //ACA TRAIGO EL RECIBO Y LA VENTA
				  $q = "          SELECT rdenum, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
				  if ($wnrofac != "")
				      $q = $q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000021, ".$wbasedato."_000018 ";
				     else
				        $q = $q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000021 ";
				  $q = $q."        WHERE vennum = '".$wnrovta."'";
				  $q = $q."          AND vennum = rdevta ";
				  $q = $q."          AND venusu = codigo ";
				  $q = $q."          AND vennit = clidoc ";
				  $q = $q."          AND vencod = empcod ";
				  $q = $q."          AND ventcl = emptem ";
				  if ($wnrofac != "")
				     {
				      $q = $q."      AND vennfa = fenfac ";
	                  $q = $q."      AND venvto = fenval "; 
	                 }
				  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				  $num = mysql_num_rows($res);

				  if ($num > 0)
				     {
					  $row = mysql_fetch_array($res); 
					  $wnrorec      = $row[0];
	                  $wfecha_tempo = $row[1];
					  $whora_tempo  = $row[2];
					  $wdocpac      = $row[3];
					  $wnompac      = $row[4];
					  $wtelpac      = $row[5];
					  $wdirpac      = $row[6];
					  $wmaipac      = $row[7];
					  $wnomnit      = $row[8]."-".$row[9]."-".$row[10];
					  $wcodusu      = $row[11];
					  $wnomusu      = $row[12];
					  $wtipven      = $row[13];
					  $wcuotamod    = $row[14];
					  $wcaja        = $row[15];
					  $wmensajero   = $row[16];
					  $wtipcli      = $row[17];   
				     } 
				    else
				       {
					    //ACA TRAIGO LA VENTA
						$q = "       SELECT 0, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
						if ($wnrofac != "" and $wfacablocada=="on")
						   $q=$q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000018 ";
						  else
						     $q=$q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041 "; 
						$q=$q."       WHERE vennum = '".$wnrovta."'";
						$q=$q."         AND venusu = codigo ";
						$q=$q."         AND vennit = clidoc ";
						$q=$q."         AND vencod = empcod ";
						$q=$q."         AND ventcl = emptem ";
						//if ($wnrofac != "" and $wtipfac == "01-PARTICULAR")   or $wfacablocada=="off"
						if ($wnrofac != "" and $wfacablocada=="on")
					       {
					        $q = $q."   AND vennfa = fenfac ";
		                    $q = $q."   AND venvto = fenval "; 
		                    //$q = $q."   AND fentip = '01-PARTICULAR'";
		                   }
		                   
		                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						$num = mysql_num_rows($res);

						if ($num > 0)
						   {
						    $row = mysql_fetch_array($res); 
						    $wnrorec      = $row[0];
			                $wfecha_tempo = $row[1];
						    $whora_tempo  = $row[2];
						    $wdocpac      = $row[3];
						    $wnompac      = $row[4];
						    $wtelpac      = $row[5];
						    $wdirpac      = $row[6];
						    $wmaipac      = $row[7];
						    $wnomnit      = $row[8]."-".$row[9]."-".$row[10];
						    $wcodusu      = $row[11];
						    $wnomusu      = $row[12];
						    $wtipven      = $row[13];
						    $wcuotamod    = $row[14];
						    $wcaja        = $row[15];
						    $wmensajero   = $row[16];
						    $wtipcli      = $row[17];   
						   }
						  else
						     {
							  //ACA TRAIGO LA VENTA
							  $q = "          SELECT 0, ".$wbasedato."_000016.fecha_data, ".$wbasedato."_000016.hora_data, clidoc, clinom, clite1, clidir, climai, empcod, empnit, empnom, venusu, descripcion, ventve, vencmo, vencaj, venmsj, ventcl ";
							  if ($wnrofac != "" or $wfacablocada=="off")
							      $q = $q."     FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041, ".$wbasedato."_000018 ";
							     else
							        $q = $q."   FROM ".$wbasedato."_000016, usuarios, ".$wbasedato."_000024, ".$wbasedato."_000041 ";
							  $q = $q."        WHERE vennum = '".$wnrovta."'";
							  $q = $q."          AND venusu = codigo ";
							  $q = $q."          AND clidoc = '9999' ";
							  $q = $q."          AND vencod = empcod ";
							  $q = $q."          AND ventcl = emptem ";
							  if ($wnrofac != "" or $wfacablocada=="off")
							     {
							      $q = $q."      AND vennfa = fenfac ";
				                  $q = $q."      AND venvto = fenval "; 
				                 }
							  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							  $num = mysql_num_rows($res);

							  if ($num > 0)
							     {
							      $row = mysql_fetch_array($res); 
							      $wnrorec      = $row[0];
					              $wfecha_tempo = $row[1];
								  $whora_tempo  = $row[2];
								  $wdocpac      = $row[3];
								  $wnompac      = $row[4];
								  $wtelpac      = $row[5];
								  $wdirpac      = $row[6];
								  $wmaipac      = $row[7];
								  $wnomnit      = $row[8]."-".$row[9]."-".$row[10];
								  $wcodusu      = $row[11];
								  $wnomusu      = $row[12];
								  $wtipven      = $row[13];
								  $wcuotamod    = $row[14];
								  $wcaja        = $row[15];
								  $wmensajero   = $row[16];
								  $wtipcli      = $row[17];
								 }   
							 }   
					   } 
				 }     
			//=======================================================================
	        
			echo "<tr><td colspan=5>&nbsp</td></tr>"; 
			
			//============================================================================================================================
			//============================================================================================================================
			//SI num=0 INDICA QUE EL DOCUMENTO NO EXISTE O ESTA DESCUADRADO !!!!!!!!!!!! NO EXISTE O ESTA DESCUADRADO !!!!!!!!!!!!!
			//============================================================================================================================
			
			if ($num == 0)
			   {
				echo "<tr><td colspan=5>&nbsp</td></tr>"; 
				echo "<tr><td colspan=5>&nbsp</td></tr>"; 
				echo "<tr><td colspan=5>&nbsp</td></tr>"; 
			    echo "<tr><td colspan=5><font size=5><B>!!!!! ATENCION !!!!! EL DOCUMENTO NO EXISTE O PRESENTA ALGUN DESCUADRE</B></font></td></tr>";
			    echo "<tr><td colspan=5><font size=5><B>DEBE REALIZARLE NOTA CREDITO A ESTE DOCUMENTO Y VOLVER A REALIZAR LA VENTA</B></font></td></tr>";
			    echo "<tr><td colspan=5>&nbsp</td></tr>"; 
				echo "<tr><td colspan=5>&nbsp</td></tr>"; 
				echo "<tr><td colspan=5>&nbsp</td></tr>"; 
		       } 
			  else
			   {
			
				$pos = strpos($wnomnit,"-");
				$wemp = substr($wnomnit,0,$pos-1);   
				
				$pos1 = strpos($wnomnit,"-",$pos+1);
				$wnitemp = substr($wnomnit,$pos+1,$pos1-3); 
				    
				$wnomemp = substr($wnomnit,$pos1+1,strlen($wnomnit));
				  
				$wempresa=explode("-",$wnomnit); 
				
				
	            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				//ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
				$q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom "
				    ."   FROM ".$wbasedato."_000049 "
				    ."  WHERE cfgcco = '".$wcco."'";
				    
				$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$row = mysql_fetch_array($res);

				$wnit_pos  =$row[0];
				$wnomemppos=$row[1];
				$wtipregiva=$row[2];
				$wtel_pos  =$row[3];
				$wdir_pos  =$row[4];
				if ($row[9] > $wfecha_tempo)
				   {
				    $wnrores=$row[8];  //Nro Resolucion Anterior
				    $wfecres=$row[5];  //Fecha Resolucion Anterior
				    $wfacini=$row[6];  //Factura Inicial Anterior 
				    $wfacfin=$row[7];  //Factura Final Anterior 
				   }
				  else
				     {
					  $wnrores=$row[13];  //Nro Resolucion Actual
					  $wfecres=$row[10];  //Fecha Resolucion Actual
					  $wfacini=$row[11];  //Factura Inicial Anterior 
					  $wfacfin=$row[12];  //Factura Final Anterior 
				     }
				$wpagintern=$row[14];
				$wemail_pos=$row[15];
				$wteldompos=$row[16];
				////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		                    
	            
				/////////////////////////////////////
				if ($wnrofac != "")
				   {
					//IMPRESION CON EL LOGO AL PRINCIPIO
					echo "<tr><td align=center colspan=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=510 HEIGHT=200></td></tr>";
					echo "<tr><td colspan=5 align=center><font size=6>".$wnomemppos."</font></td></tr>";
					echo "<tr><td colspan=5 align=center><font size=5>Nit. : ".$wnit_pos."</font></td></tr>";
					echo "<tr><td colspan=5 align=center><font size=5>Tel. : ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
					echo "<tr><td colspan=5 align=center><font size=5>".$wtipregiva."</font></td></tr>";
					echo "<tr><td colspan=3 align=left><font size=5>Hora: ".$whora_tempo."</font></td>";
					echo "<td colspan=2 align=right><font size=5>Fecha: ".$wfecha_tempo."</font></td></tr>";
				   
					echo "<tr><td colspan=5><font size=5>Factura de Venta Nro: ".$wnrofac."</font></td></tr>";   
					if ($wnrorec > 0)
				       echo "<tr><td colspan=5><font size=5>Recibo Nro: ".$wnrorec."     Venta Nro: ".$wnrovta."</font></td></tr>";
				      else
				         echo "<tr><td colspan=5><font size=5>Venta Nro: ".$wnrovta."</font></td></tr>"; 
				    
				    /////////////////////////////////////////////////////////////////////////////////////////////////          
				    //Aca averiguo si el responsable de la cuenta es una empresa de promotora, osea por nomina
				    $q = "SELECT temche, temdes "
				        ."  FROM ".$wbasedato."_000016, ".$wbasedato."_000029 "
				        ." WHERE vennum = '".$wnrovta."'"
				        ."   AND mid(ventcl,1,instr(ventcl,'-')-1) = temcod ";
				    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					if ($num > 0)
					   {
					    $row = mysql_fetch_array($res);
					    if ($row[0] == 'on')     
					       echo "<tr><td colspan=5><font size=5>Tipo de Responsable: ".$row[1]."</font></td></tr>";
				       }      
				         
				    echo "<tr><td colspan=5><font size=5>Responsable: ".$wempresa[2]."</font></td></tr>";
					echo "<tr><td colspan=5><font size=5>Cédula/Nit: ".$wempresa[1]."</font></td></tr>";

					echo "<tr><td colspan=5><font size=5>Beneficiario: ".$wnompac."</font></td></tr>";
					echo "<tr><td colspan=5><font size=5>Cédula/Nit: ".$wdocpac."</font></td></tr>";

					$q = "SELECT vmppro "
					    ."  FROM ".$wbasedato."_000050 "
					    ." WHERE vmpvta = '".$wnrovta."'";
					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);
					
					if ($num > 0)
					   {
						$row = mysql_fetch_array($res);   
					    $wprograma=$row[0]; 
					    
					    echo "<tr><td colspan=4><font size=5>Programa: ".$wprograma."</font></td></tr>";  
				       } 
					
					//Si es un domicilio imprimo la dirección y el telefono del cliente
					if ($wtipven=="Domicilio")
					   {
						echo "<tr><td colspan=5><font size=5>Dirección: ".$wdirpac."</font></td></tr>";
					    echo "<tr><td colspan=5><font size=5>Telefono: ".$wtelpac."</font></td></tr>";
					   }

					echo "<tr><td colspan=5>&nbsp</td></tr>";
					echo "<th align=left bgcolor=DDDDDD><font size=5>Descripción</font></th>";
					echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>V/U</font></th>";
					echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>Cant.</font></th>";
					echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>% Iva</font></th>";
					echo "<th bgcolor=DDDDDD><font size=5>Total</font></th>";

					//ACA TRAIGO EL ENCABEZADO DE LA VENTA 
					$q = " SELECT venvto, venviv, vencop, vencmo, vendes, venrec "
					    ."   FROM ".$wbasedato."_000016 "
					    ."  WHERE vennum = '".$wnrovta."'";
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
					$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan), vdedes "
					    ."   FROM ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000002 "
					    ."  WHERE vdenum = '".$wnrovta."'"
					    ."    AND vdeart = artcod "
					    ."    AND mid(artuni,1,locate('-',artuni)-1) = unicod ";
					     
					$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					$num = mysql_num_rows($res);

					if ($num > 0)
					 {
				      $wtotvenexclui=0;		 
					  $wtotvenconiva=0;
					  $wtotdes=0; 
					  for ($i=1;$i<=$num;$i++)
					     {
					      $row = mysql_fetch_array($res);
					      
					      echo "<tr>";
					      if ($row[8] > 0)   //Si tiene descuento el articulo
					         echo "<td align=left><font size=5>** ".substr($row[1],0,21)."</font></td>";           //Descripcion 
					        else
					           echo "<td align=left><font size=5>".$row[1]."</font></td>";            //Descripcion  
					           //On
					           //echo "<td align=left><font size=5>".substr($row[1],0,21)."</font></td>";            //Descripcion  
					      echo "<td align=right><font size=5>".number_format($row[4],0,'.',',')."</font></td>";    //Valor unitario con IVA
					      echo "<td align=right><font size=5>".$row[3]."</font></td>";                             //Cantidad
					      echo "<td align=right><font size=5>".$row[5]."</font></td>";                             //% IVA  
					      echo "<td align=right><font size=5>".number_format(($row[7]),0,'.',',')."</font></td>";  //Valor total con iva
					      echo "</tr>";
					      
					      $wtotvenconiva=$wtotvenconiva+$row[7];
					      $wtotdes=$wtotdes+($row[8] + round(($row[8]*($row[5]/100))));
					      
					      
					      if ($row[5] == 0)     //Iva cero (0) entonces es excuido
					         $wtotvenexclui=$wtotvenexclui+$row[7];
					     } 
					    
					  if ($wtotvenexclui > 0)    
					     $wtotvenexclui=$wtotvenexclui-$wtotdes;
					  
					     
					  echo "<tr><td colspan=4>&nbsp</td>";
					  echo "<td align=right>-----------------------</td></tr>";
					       
					  echo "<tr><td colspan=4><font size=5><b>Sub-Total</b></font></td>";
				      echo "<td align=right><font size=5>".number_format($wtotvenconiva,0,'.',',')."</font></td></tr>";                  //Total venta con IVA 
					     
				      echo "<tr><td colspan=4><font size=5><b>** Descuento: </b></font></td>";
					  echo "<td colspan=1 align=right><font size=5>".number_format(round($wtotdes),0,'.',',')."</font></td></tr>";       //Descuento antes de IVA
				               
				      if ($wtotrec > 0)  //Si tiene recargo lo imprimo
					     {
						  echo "<tr><td colspan=4><font size=5>Recargo: </font></td>";
					      echo "<td colspan=1 align=right><font size=5>".number_format($wtotrec,0,'.',',')."</font></td></tr>";          //Recargo
				         }   
				      
				      echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
					  
				      echo "<tr><td colspan=3><font size=5><b>Valor Total</b></font></td>";
				      echo "<td align=right colspan=2><font size=5><b>".number_format(($wtotal),0,'.',',')."</b></font></td></tr>";      //Total a pagar Neto
		  				  
					  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				      // RESUMEN DE IVA
				      ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				      echo "<tr><td colspan=5>&nbsp</td></tr>";
				      echo "<tr><td colspan=5>&nbsp</td></tr>";
				      echo "<tr>";
				      echo "<th bgcolor=DDDDDD align=CENTER colspan=5><font size=5>RESUMEN DE IVA</font></th>";
				      echo "</tr>";
				      
				      echo "<th bgcolor=DDDDDD align=LEFT colspan=2><font size=5><b>Tipo</b></font></th>";
					  echo "<th bgcolor=DDDDDD align=right><font size=5><b>Compra</b></font></th>";
					  echo "<th bgcolor=DDDDDD align=right><font size=5><b>IVA</b></font></th>";
					  echo "<th bgcolor=DDDDDD>&nbsp</th>";
				      
					  //GRAVADO
					  echo "<tr>";
				      echo "<td colspan=2><font size=5><b>Gravado</b></font></td>";
				      $wgravado=$wtotal-$wtotiva-$wtotvenexclui;
				      echo "<td align=right colspan=1><font size=5>".number_format(($wgravado),0,'.',',')."</font></td>";  //Total Gravado
				      echo "<td align=right colspan=1><font size=5>".number_format(($wtotiva),0,'.',',')."</font></td>";                         //Total Iva
					  echo "</tr>";
					  
					  //EXCLUIDO
					  echo "<tr>";
				      echo "<td colspan=2><font size=5><b>Excluido</b></font></td>";
				      echo "<td align=right colspan=1><font size=5>".number_format($wtotvenexclui,0,'.',',')."</font></td>";  //Total Excluido
				      echo "<td align=right colspan=1><font size=5>".number_format(0,0,'.',',')."</font></td>";               //Total Iva
					  echo "</tr>";
					  
					  echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>"; 
					  
					  //TOTALES
					  echo "<tr>";
				      echo "<td colspan=2><font size=5><b>Total</b></font></td>";
				      echo "<td align=right colspan=1><font size=5>".number_format(($wgravado+$wtotvenexclui),0,'.',',')."</font></td>";  //Total Gravado
				      echo "<td align=right colspan=1><font size=5>".number_format(($wtotiva),0,'.',',')."</font></td>";                         //Total Iva
					  echo "</tr>";
					  
					  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				      
					  //Aca la cantidad de formas de pago que tiene el recibo de caja
				      $q = " SELECT count(*) "
					      ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022 "
					      ."  WHERE rdenum = ".$wnrorec
					      ."    AND rdevta = '".$wnrovta."'"
					      ."    AND rdefue = rfpfue "
					      ."    AND rdenum = rfpnum "
					      ."    AND rdeest = 'on' "
					      ."    AND rfpest = 'on' "
					      ."    AND rdecco = '".$wcco."'"
					      ."    AND rdecco = rfpcco ";
					  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					  $num = mysql_num_rows($res);
					  $row = mysql_fetch_array($res);
					  $fk=$row[0];
					  
					  if ($fk > 0)
					     {
						  //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LAS FORMAS DE PAGO   
						  $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades "
					          ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
					          ."  WHERE rdenum = ".$wnrorec
					          ."    AND rdevta = '".$wnrovta."'"
					          ."    AND rdefue = rfpfue "
					          ."    AND rdenum = rfpnum "
					          ."    AND rdeest = 'on' "
					          ."    AND rfpest = 'on' "
					          ."    AND rfpfpa = fpacod "
					          ."    AND rdecco = '".$wcco."'"
					          ."    AND rdecco = rfpcco ";
					          
					      $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					      $num = mysql_num_rows($res);   
						     
					      for ($i=1;$i<=$num;$i++)
						      {
							   $row = mysql_fetch_array($res);
							      
							   $wfpa[$i] = $row[0];
							   $wdocane[$i] = $row[1];
							   $wobsrec[$i] = $row[2];
							   $wvalfpa[$i] = $row[3];
							   $wfpades[$i] = $row[4];
						      }
					     }
					  				  
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
					     echo "<th bgcolor=DDDDDD align=left colspan=2><font size=5>FORMA DE PAGO</font></th>";
					     echo "<th bgcolor=DDDDDD align=right><font size=5>Doc.Anexo</font></th>";
					     echo "<th bgcolor=DDDDDD align=right><font size=5>Observ.</font></th>";
					     echo "<th bgcolor=DDDDDD align=right><font size=5>Valor</font></th>";
						  
					     for ($i=1;$i<=$fk;$i++)
					        {
						     if ($wvalfpa[$i] > 0)
						        {
						         echo "<tr>";
						         echo "<td align=left colspan=2><font size=5>".$wfpa[$i]." - ".$wfpades[$i]."</font></td>";
						         echo "<td align=right><font size=5>".$wdocane[$i]."</font></td>";
						         echo "<td align=right><font size=5>".$wobsrec[$i]."</font></td>";
						         echo "<td align=right><font size=5>".number_format($wvalfpa[$i],0,'.',',')."</font></td>";
						         echo "</tr>";
					            }
					        }
					     echo "<tr><td colspan=5>&nbsp</td></tr>";
					    }  
					  echo "<TR><TD colspan=5 align=center>_________________________________________________________________________________________________________</TD></TR>";  
				      echo "<tr>";
				      echo "<td colspan=3><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>"; 
				      echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";
				      
				      //Si es un domicilio imprimo el mensajero
				      if ($wtipven=="Domicilio")
					     echo "<tr><td colspan=5><font size=3>Mensajero: ".$wmensajero."</font></td></tr>";
				      
					  if ($wtipcli != "01-PARTICULAR")
		                 {
			              echo "<tr><td colspan=5>&nbsp</td></tr>";
			              echo "<tr><td colspan=5>&nbsp</td></tr>";
			              echo "<tr><td colspan=5>&nbsp</td></tr>";
			              echo "<TR><TD colspan=5 align=center>_____________________________________________________</TD></TR>";    
		                  echo "<tr><td colspan=5 align=center><font size=5>Firma del Cliente</font></td></tr>";
	                     }  
	             				     
				      echo "<tr><td colspan=5>&nbsp</td></tr>";
				      echo "<tr><td colspan=5 align=center><font size=5>Resolución Nro: ".$wnrores." del ".$wfecres.", </font></td></tr>";    
				      echo "<tr><td colspan=5 align=center><font size=5>factura ".$wfacini." a la factura ".$wfacfin.".</font></td></tr>";    
				      echo "<tr><td colspan=5 align=center><font size=5>Esta factura de venta se asimila en </font></td></tr>";    
				      echo "<tr><td colspan=5 align=center><font size=5>todos sus efectos a una letra de cambio, Art. 621 y </font></td></tr>";    
				      echo "<tr><td colspan=5 align=center><font size=5>SS, 671 y SS 772, 773, 770 y SS del código de comercio.</font></td></tr>";    
				      echo "<tr><td colspan=5 align=center><font size=5>Factura impresa por computador cumpliendo con los</font></td></tr>";    
				      echo "<tr><td colspan=5 align=center><font size=5>requisitos del Art. 617 del E.T.</font></td></tr>";    
				      echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
				      echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
				      
				      echo "<tr>";
				      if ($wpagintern != "" and $wpagintern != "NO APLICA")    //Pagina de Internet
				         echo "<tr><td colspan=5 align=center><font size=5><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
				      if ($wemail_pos != "" and $wemail_pos != "NO APLICA")    //Email
				         echo "<tr><td colspan=5 align=center><font size=5><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
				      if ($wteldompos != "" and $wteldompos != "NO APLICA")    //Telefono domicilio
				         echo "<tr><td colspan=5 align=center><font size=5><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
				      echo "<tr>";
				     }    		   
				   }
				  else  //No se genero factura
				     {
					  //if ($wcuotamod > 0)
					  //  {
						 //IMPRESION CON EL LOGO AL PRINCIPIO
						 echo "<tr><td align=center colspan=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=510 HEIGHT=200></td></tr>";
						 echo "<tr><td colspan=5 align=center><font size=6>".$wnomemppos."</font></td></tr>";
						 echo "<tr><td colspan=5 align=center><font size=5>Nit. : ".$wnit_pos."</font></td></tr>";
						 echo "<tr><td colspan=5 align=center><font size=5>Tel.: ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
						 echo "<tr><td colspan=5 align=center><font size=5>".$wtipregiva."</font></td></tr>";
						 echo "<tr><td colspan=3 align=left><font size=5>Hora: ".$whora_tempo."</font></td>";
						 echo "<td colspan=2 align=right><font size=5>Fecha: ".$wfecha_tempo."</font></td></tr>";
				   
					     if ($wnrorec > 0)
					        echo "<tr><td colspan=4><font size=5>Recibo Nro: ".$wnrorec."     Venta Nro: ".$wnrovta."</font></td></tr>";
					       else
					          echo "<tr><td colspan=4><font size=5>Venta Nro: ".$wnrovta."</font></td></tr>";
		          
					     /////////////////////////////////////////////////////////////////////////////////////////////////          
					     //Aca averiguo si el responsable de la cuenta es una empresa de promotora, osea por nomina
					     $q = "SELECT temche, temdes "
					         ."  FROM ".$wbasedato."_000016, ".$wbasedato."_000029 "
					         ." WHERE vennum = '".$wnrovta."'"
					         ."   AND mid(ventcl,1,instr(ventcl,'-')-1) = temcod ";
					     $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						 $num = mysql_num_rows($res);
						 if ($num > 0)
						    {
						     $row = mysql_fetch_array($res);
						     if ($row[0] == 'on')     
						        echo "<tr><td colspan=5><font size=5>Tipo de Responsable: ".$row[1]."</font></td></tr>";
					        }      
					          
						 echo "<tr><td colspan=4><font size=5>Responsable: ".$wempresa[2]."</font></td></tr>";
						 echo "<tr><td colspan=4><font size=5>Cédula/Nit: ".$wempresa[1]."</font></td></tr>";

						 echo "<tr><td colspan=4><font size=5>Beneficiario: ".$wnompac."</font></td></tr>";
						 echo "<tr><td colspan=4><font size=5>Cédula/Nit: ".$wdocpac."</font></td></tr>";

						 //AVERIGUO SI EL CLIENTE ESTA AFILIADO A ALGUN PROGRAMA
						 $q = "SELECT vmppro "
			                 ."  FROM ".$wbasedato."_000050 "
						     ." WHERE vmpvta = '".$wnrovta."'";
						 $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						 $num = mysql_num_rows($res);
						  if ($num > 0)
						     {
							  $row = mysql_fetch_array($res);   
						      $wprograma=$row[0]; 
						    
						      echo "<tr><td colspan=4><font size=5>Programa: ".$wprograma."</font></td></tr>";  
					         } 
						 
				         echo "<tr><td colspan=4>&nbsp</td></tr>";
						 echo "<th align=left bgcolor=DDDDDD colspan=3><font size=5>Descripción</font></th>";
						 echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>Cant.</font></th>";
						 
						 //ACA TRAIGO TODA LA VENTA 
						 //$q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan)+((vdevun*vdecan)*(vdepiv/100)) "
						 $q = " SELECT vdeart, artnom, unides, vdecan, vdevun, vdepiv, (vdevun*vdecan)*(vdepiv/100), (vdevun*vdecan) "
						     ."   FROM ".$wbasedato."_000017, ".$wbasedato."_000001, ".$wbasedato."_000002 "
						     ."  WHERE vdenum = '".$wnrovta."'"
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
					        echo "<TR><TD colspan=5>_________________________________________________________________________________________________________</TD></TR>";  
						  
					        //Aca la cantidad de formas de pago que tiene el recibo de caja
					        $q = " SELECT count(*) "
						        ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022 "
						        ."  WHERE rdenum = ".$wnrorec
						        ."    AND rdevta = '".$wnrovta."'"
						        ."    AND rdefue = rfpfue "
						        ."    AND rdenum = rfpnum "
						        ."    AND rdeest = 'on' "
						        ."    AND rfpest = 'on' "
						        ."    AND rdecco = '".$wcco."'"
					            ."    AND rdecco = rfpcco ";
						    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						    $num = mysql_num_rows($res);
						    $row = mysql_fetch_array($res);
						    $fk=$row[0];
					        
						    if ($fk > 0)
						       {
							    //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LAS FORMAS DE PAGO   
						        $q = " SELECT rfpfpa, rfpdan, rfpobs, rfpvfp, fpades "
					                ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000022, ".$wbasedato."_000023 "
					                ."  WHERE rdenum = ".$wnrorec
					                ."    AND rdevta = '".$wnrovta."'"
					                ."    AND rdefue = rfpfue "
					                ."    AND rdenum = rfpnum "
					                ."    AND rdeest = 'on' "
					                ."    AND rfpest = 'on' "
					                ."    AND rfpfpa = fpacod "
					                ."    AND rdecco = '".$wcco."'"
					                ."    AND rdecco = rfpcco ";
					                
					            $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
					            $num = mysql_num_rows($res);   
						     
						        for ($i=1;$i<=$fk;$i++)
						          {
							       $row = mysql_fetch_array($res);
	 						     
	                               $wfpa[$i] = $row[0];
							       $wdocane[$i] = $row[1];
							       $wobsrec[$i] = $row[2];
							       $wvalfpa[$i] = $row[3];
							       $wfpades[$i] = $row[4];
						          }
						          
					            if ($wcuotamod > 0)
							      {
							       echo "<tr><td colspan=3><font size=5>Cuota Mod. o Franquicia: </font></td>";
							       echo "<td colspan=1 align=right><font size=5>".number_format($wcuotamod,0,'.',',')."</font></td></tr>";
							      } 
								    
							    echo "<TR><TD colspan=5>_________________________________________________________________________________________________________</TD></TR>";     
							    //FORMA DE PAGO
							    echo "<tr><td colspan=4>&nbsp</td></tr>";
							    echo "<th bgcolor=DDDDDD align=left colspan=1><font size=5>FORMA DE PAGO</font></th>";
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
							    //echo "<tr><td colspan=3><font size=5>Cambio: </font></td>";
							    //echo "<td colspan=1 align=right><font size=5>".number_format($wvalcam,2,'.',',')."</font></td></tr>";
							    echo "<tr><td colspan=4>&nbsp</td></tr>";
							   }  
						    echo "<TR><TD colspan=5>_________________________________________________________________________________________________________</TD></TR>";  
						    echo "<tr>";
						    echo "<td colspan=2><font size=3>Vend: ".$wcodusu." - ".$wnomusu."</font></td>"; 
						    echo "<td colspan=2><font size=3>Caja: ".$wcaja."</font></td>";
						    echo "</tr>";
						    
						    echo "<tr>";
				            if ($wpagintern != "" and $wpagintern != "NO APLICA")        //Pagina de Internet
						       echo "<tr><td colspan=5 align=center><font size=5><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
						    if ($wemail_pos != "" and $wemail_pos != "NO APLICA")  //Email
						       echo "<tr><td colspan=5 align=center><font size=5><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
						    if ($wteldompos != "" and $wteldompos != "NO APLICA")        //Telefono domicilio
			       			   echo "<tr><td colspan=5 align=center><font size=5><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
				            echo "</tr>"; 
						   }
					    //}
				     } 
		      } //Fin del else de num=0, que indica que no existe el documento o esta descuadrado
          }		     
	  }
echo "</table>";    
echo "</form>";
?>
