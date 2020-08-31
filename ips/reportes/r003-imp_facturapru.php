<?php
include_once("conex.php");
echo "<html>";
echo "<head>";

echo "<title>FACTURACION</title>";
echo "</head>";
echo "<body TEXT='#000066'>";

$wfec=date("Y-m-d");
$whor=(string)date("H:i:s");

$empresa=$wbasedato;
$wfenfac=explode("-",$wfactura);

$color="#dddddd";
$color1="#cccccc";
$color2="#999999";
$valido='off';
//version 2007-11-14
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	echo "<form name='r003-imp_factura' action='r003-imp_facturapru.php' method=post>";
	

	include_once("root/montoescrito.php");

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

	if(!isset($user))
		echo "Error Usuario NO Registrado";
	else
	{
		function restar_fec($fecha1,$fecha2)
		{
			$yyyy1=intval(substr($fecha1,6,4));
			$mm1  =intval(substr($fecha1,4,2));
			$dd1  =intval(substr($fecha1,0,2));
	 		$yyyy2=intval(substr($fecha2,6,4));
			$mm2  =intval(substr($fecha2,4,2));
			$dd2  =intval(substr($fecha2,0,2));

			$date1 = mktime(0,0,0,$mm1,$dd1,$yyyy1);
			$date2 = mktime(0,0,0,$mm2,$dd2,$yyyy2);
			$total_days = 0;
			while($date1 < $date2)
			{
				$total_days++;
				$date1 += 86400;
			}
			return $total_days;
		}

		function sumar_dias($fecha,$ndias)
    	{
			$dia=intval(substr($fecha,0,2));
			$mes=intval(substr($fecha,3,2));
			$ano=intval(substr($fecha,6,4));
			$segundos=mktime(0,0,0,$mes,$dia,$ano); 	  // calcular cuantos segundos han pasado desde 1970
			$suma_segundos=$segundos+($ndias*86400);      // el dia tiene 86400 segundos, sumare cantidad de dias introducido anteriormente
			$fecha_mas_dias=date("d-m-Y",$suma_segundos); // Pasar los segundos a formato fecha

			return $fecha_mas_dias;
        }

        function llevar_a_seg_de_YMD($fecha)
    	{
			$dia=intval(substr($fecha,8,2));
			$mes=intval(substr($fecha,5,2));
			$ano=intval(substr($fecha,0,4));

			$segundos=mktime(0,0,0,$mes,$dia,$ano);

			return $segundos;
        }

        function imp_lin_ctrl($wemp,$conex,$west,$westd,$wfue,$wfac1,$wfac2,$wusu,$wfec,$whor)
        {
        	if($west=="on")
        		$west="**COPIA**";
        	else
        	{
        		$west=" ";
        		$q= " UPDATE ".$wemp."_000018 SET fenimp = 'on' ".
        		 	"  WHERE fenffa = '".$wfue."'".
		         	"    AND fenfac = '".$wfac1."-".$wfac2."'";
                $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
        	}

        	$usu=explode("-",$wusu);
        	$wusu=$usu[1];

        	$query = "SELECT cjecco ".
	 	         	 "  FROM ".$wemp."_000030 ".
		         	 " WHERE cjeusu = '".$wusu."'";

			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    		$num = mysql_num_rows($err);

    		if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$wcco=$row[0];
			}

			if($westd=="off")
				$westd="** DOC. ANULADO**";
			else $westd=" ";

        	echo "<table width=99% align=center cellspacing=0 cellpadding=1 border=0>";
	  		echo "<tr><td align=left><font size=1>Fecha: ".$wfec." Hora: ".$whor." C.Costo: ".$wcco." ".$west." ".$westd."</font></td></tr>";
			echo "</table>";
        }

		if (isset($wfactura))
		{
			$wfact=explode("-",$wfactura);

		 	$query = "SELECT fenfac, fenfec, empnom, fennit, empdir, emptel, empdia, "
	 	            ."       fenval, fenviv, fencop, fencmo, fendes, fenabo, fenffa, "
	 	            ."		 fenhis, fening, fencod, fenimp, fendpa, fennpa, fenest, ".$empresa."_000018.seguridad "
	 	            ."  FROM ".$empresa."_000018, ".$empresa."_000024 "
		            ." WHERE fenffa = '".$wfact[0]."'"
		            ."   AND fenfac = '".$wfact[1]."-".$wfact[2]."'"
	                ."   AND empcod = fencod ";

			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    		$num = mysql_num_rows($err);

    		if($num > 0)
			{
				$row = mysql_fetch_array($err);
		     	$wfenfac=$row[0];
		     	$wfenfec=$row[1];
		     	$wfenres=$row[2];
		     	$wfennit=$row[3];
			 	$wempdir=$row[4];
			 	$wemptel=$row[5];
			 	$wempdia=$row[6];
			 	$wfenval=$row[7];
			 	$wfenviv=$row[8];
			 	$wfencop=$row[9];
			 	$wfencmo=$row[10];
			 	$wfendes=$row[11];
			 	$wfenabo=$row[12];
			 	$wfenffa=$row[13];
			 	$wfenhis=$row[14];
			 	$wfening=$row[15];
			 	$wfencod=$row[16];
			 	$wfenimp=$row[17];
				$wfendpa=$row[18];
				$wfennpa=$row[19];
				$wfenest=$row[20];
				$wfenseg=$row[21];
				
			 	$wfenfec=date("d-m-Y",llevar_a_seg_de_YMD($wfenfec));
			 	$wfenvec=sumar_dias($wfenfec,$wempdia);

			 	$query = "SELECT pacap1, pacap2, pacno1, pacno2, ingfei, pacdoc ".
	 	                 "  FROM ".$empresa."_000100, ".$empresa."_000101 ".
		         		 " WHERE pachis = '".$wfenhis."'".
		         		 "   AND ingnin = '".$wfening."'".
		         		 "   AND inghis = pachis";

				$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    			$num = mysql_num_rows($err);

    			if($num > 0)
				{
					$row = mysql_fetch_array($err);
		     		$wpacap1=$row[0];
		     		$wpacap2=$row[1];
		     		$wpacno1=$row[2];
		     		$wpacno2=$row[3];
			 		$wingfei=$row[4];
			 		$wpacdoc=$row[5];
			 		$wegrfee=$wingfei;

			 		$wingfei=date("d-m-Y",llevar_a_seg_de_YMD($wingfei));
			 		$wegrfee=date("d-m-Y",llevar_a_seg_de_YMD($wegrfee));
			 		$westancia=restar_fec($wingfei,$wegrfee);
				 	$valido='on';
				}

				if($wfencod == '01')
			 	  {
				   $wfenres=$wfennpa;
				   $wfennit=$wfendpa;
			 	  }		
			 	$wfenpac=$wpacno1." ".$wpacno2." ".$wpacap1." ".$wpacap2;
			 	$wfendoc=$wpacdoc;     
			 	

				echo "<table width=99% align=center border=0>";
				
				if ($empresa !='clisur')// para volverlo multiempresa
				{
					echo "<tr><td align=center rowspan=7><IMG width=190 height=80 src='/matrix/images/medical/pos/logo_".$empresa.".png'></td>";
				}
				else
				{
					echo "<tr><td align=center rowspan=7><IMG width=190 height=80 SRC='/matrix/images/medical/Citas/logo_citascs.png'></td>";
				}
				// para los datos de la factura en el encabeza
				$query =  " SELECT *
			                  FROM ".$empresa."_000049";
		        $err = mysql_query($query,$conex);
		        $num = mysql_num_rows($err);
		        $rowx = mysql_fetch_array($err);
		        
	  			echo "<tr>";
	  			echo "<td align=center><font size=2><b>".$rowx['Cfgnom']."</font><br><font size=1><b>NIT. ".$rowx['Cfgnit']."<br>".$rowx['Cfgdir']."<br>CONMUTADOR: ".$rowx['Cfgtel']."</b></font></td>";
	  			echo "<td rowspan=3 align=right><table align=right cellspacing=0 cellpadding=0 border=1 bordercolor=000000>";
		  			echo "<tr><td align=left colspan=2><font size=1><b>FACTURA DE VENTA No. &nbsp</b></font><br><font size=1><b>".$wfenfac."</b></td></tr>";
		    		echo "<tr><td align=center><font size=1><b>FECHA </b></font><br><font size=1><b>".$wfenfec."</b></font></td>";
		    		echo "<td bgcolor=".$color2." align=center><font size=1><b>VENCIMIENTO</b></font><br><font size=1><b>".$wfenvec."</b></font></td></tr>";
		    		echo "<tr>";
	  				echo "<td align=center><font size=1><b> DIA | MES | AÑO |</b></font></td>";
	  				echo "<td bgcolor=".$color2." align=center><font size=1><b> DIA | MES | AÑO |</b></font></td></tr>";
	  				echo "</tr>";
	  			echo "</table>";
	  			echo "</tr>";
	  			
	  			if ($empresa =='clisur')// para volverlo multiempresa
				{
					echo "<tr><td align=center><font size=1><b>Fax: 3310604</b></font></td></tr>";
					echo "<tr><td align=center><font size=1><b>ENVIGADO-COLOMBIA</b></font></td></tr>";
				}
	  			echo "</table>";

	  			echo "<table width=99% align=center border=1 bordercolor=000000 cellspacing=0>";
	  			echo "<tr>";
				echo "<td><font size=1><b>SEÑORES </font></b><br><font size=1>".$wfenres."</font></td>";
				echo "<td align=center><font size=1><b>NIT o C.C. </font></b><br><font size=1>".$wfennit."</font></td>";
				echo "<td><font size=1><b>DOMICILIO </font></b><br><font size=1>".$wempdir."</font></td>";
				echo "<td><font size=1><b>TELEFONO </font></b><br><font size=1>".$wemptel."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td rowspan=2><font size=1><b>POR SERVICIOS PRESTADOS AL PACIENTE </font></b><br><font size=1>".$wfenpac."</font></td>";
				echo "<td align=center><font size=1><b>FECHA INGRESO </font></b></td>";
				echo "<td align=center><font size=1><b>FECHA SALIDA </font></b></td>";
				echo "<td align=center><font size=1><b>HISTORIA </font></b></td></tr>";
				echo "<tr><td align=center><font size=1>".$wingfei."</font><br><font size=1><b> DIA | MES | AÑO |</b></font></td>";
				echo "<td align=center><font size=1>".$wegrfee."</font><br><font size=1><b> DIA | MES | AÑO |</b></font></td>";
				echo "<td align=center><font size=1>".$wfenhis."-".$wfening."</font></td>";
				echo "</tr>";
				//echo "<tr><td colspan=4><font size=1><b>TIPO DE ATENCION <br><br></font></b></td></tr>";
	  			echo "</table>";

	  			echo "<table width=99% align=center cellspacing=0 cellpadding=0 border=0>";
	  			//echo "<tr><td><br></td></tr>";
	  			echo "</table>";

	  			echo "<table width=99% align=center border=1 bordercolor=000000 cellspacing=0>";
	  			echo "<tr>";
	  			echo "<td colspan=4><table width=100% align=center border=0 bordercolor=000000 cellspacing=0>";
	  			echo "<tr><td align=left bgcolor=".$color2."><font size=1><b>CONCEPTO</b></font></td>";
	      		echo "<td align=left bgcolor=".$color2."><font size=1><b>DESCRIPCION</b></font></td>";
	      		echo "<td align=left bgcolor=".$color2."><font size=1><b>NIT. </b></font></td>";
	      		echo "<td align=left bgcolor=".$color2."><font size=1><b>TERCERO </b></font></td>";
	      		echo "<td align=right bgcolor=".$color2."><font size=1><b>CLINICA </b></font></td>";
	      		echo "<td align=right bgcolor=".$color2."><font size=1><b>TERCERO </b></font></td>";
	      		echo "<td align=right bgcolor=".$color2."><font size=1><b>TOTAL</b></font></td></tr>";

	    		$query = "SELECT fdecon,grudes,fdeter,fdepte,fdevco,grutip".
	 	        	 	 "  FROM ".$empresa."_000065, ".$empresa."_000004 ".
		         		 " WHERE fdefue = '".$wfenffa."'".
		         	 	 "   AND fdedoc = '".$wfenfac."'".
		         	 	 "   AND grucod = fdecon".
		         	 	 "   AND gruabo != 'on'";

				$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    			$num = mysql_num_rows($err);

    			$wacuter=0;
				$wacucli=0;
				$wacucpt=0;
				$wtotter=0;
				$wtotcli=0;
				$wtotcpt=0;
				$wtotpar=0;
				 
				$wsw="on";   //Este swiche sirve para saber cuando se imprime una factura de paquete, para que la impresión la haga solo en una linea.
				
				if($num > 0)
				{
					for ($i=1;$i<=$num;$i++)
	        		{
		        		$row = mysql_fetch_array($err);
					    
		        		if($row[5]=='C')   //si es compartido el concepto
		            	{
		            		$query = "SELECT mednom ".
	    	    	    		     "  FROM ".$empresa."_000051 ".
	        	    	    	 	 " WHERE meddoc= '".$row[2]."'";
                            $res = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    						$num1 = mysql_num_rows($res);

    						if($num1 > 0)
							{
								$row1 = mysql_fetch_array($res);
			     				$wmednom=$row1[0];
							}
							else
							{
								$wmednom="NO EXISTE";
							}

							$wvalter=$row[4]*($row[3]/100);
							$wvalcli=$row[4]-$wvalter;
		            	}
		            	else
		            	{
		            		$wvalter=0;
		            		$wvalcli=$row[4];
		            		$row[2]=" ";
		            		$wmednom=" ";
		            	}

		            	$wacuter=$wacuter+$wvalter;
			     		$wacucli=$wacucli+$wvalcli;
			     		$wacucpt=$wacucpt+$row[4];
			      		$wtotpar=$wtotpar+$row[4];			 // Acumula para el valor parcial

			      		
			      		//AVERIGUO SI LA HISTORIA SE FACTURO POR PAQUETE
			      	    $q= " SELECT movpaqcod, count(*) "
		      		       ."   FROM ".$wbasedato."_000066, ".$wbasedato."_000115 "
		      		       ."  WHERE Rcfffa    = '".$wfenffa."'"
		      		       ."    AND Rcffac    = '".$wfenfac."'"
		      		       ."    AND Rcfreg    = Movpaqreg "
		      		       ."    AND movpaqest = 'on' "
		      		       ."  GROUP BY 1 ";
			      		$res_paq = mysql_query($q,$conex) or die (mysql_errno().":".mysql_error());
			      		$num_paq = mysql_num_rows($res_paq);  
    					$row_paq = mysql_fetch_array($res_paq);
    					
    					if ($row_paq[1] == 0)  //Si la FACTURA e HISTORIA no son de paquetes
    					   {
			      		    echo "<tr>";
							echo "<td align=left  bgcolor='".$color."'><font size=1>".$row[0]."&nbsp&nbsp</font></td>";
					    	echo "<td align=left  bgcolor='".$color."'><font size=1>".$row[1]."&nbsp&nbsp</font></td>";
				    		echo "<td align=left  bgcolor='".$color."'><font size=1>".$row[2]."&nbsp&nbsp</font></td>";
				    		echo "<td align=left  bgcolor='".$color."'><font size=1>".$wmednom."&nbsp&nbsp</font></td>";
				     		echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wvalcli,2,'.',',')."&nbsp&nbsp</font></td>";
				     		echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wvalter,2,'.',',')."&nbsp&nbsp</font></td>";
				     		echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($row[4],2,'.',',')."&nbsp&nbsp</font></td>";
				     		echo "</tr>";
			     	       }
			     	  else                 //Si la FACTURA corresponde a un paquete solo imprimo este con el valor total.
		     	         {
			     	      if ($wsw=="on")   //Si entre es porque es un paquete.
			     	         { 
				     	      $q = " SELECT movpaqcod, paqnom, sum(rcfval)"
						          ."   FROM ".$wbasedato."_000066, ".$wbasedato."_000115, ".$wbasedato."_000018, ".$wbasedato."_000113 "
						          ."  WHERE rcfffa    = '".$wfenffa."'"
						          ."    AND rcffac    = '".$wfenfac."'"
						          ."    AND rcfreg    = movpaqreg "
						          ."    AND rcfffa    = fenffa "
						          ."    AND rcffac    = fenfac "
						          ."    AND paqcod    = movpaqcod "
						          ."    AND movpaqest = 'on' "
						          ."  GROUP BY 1, 2 ";
						      $res_paq = mysql_query($q,$conex);   
						      $num_paq = mysql_num_rows($res_paq);  
						   
						      if ($num_paq > 0)
						         {
						         	
						          $wtiene_paq="on";
						          $wtotpaq=0;
						          $totpaq=0;
						          for ($j=1;$j<=$num_paq;$j++)
						             {
							          $row_paq = mysql_fetch_array($res_paq);
							          
							          echo "<tr>";
									  echo "<td align=left  bgcolor='".$color."'><font size=1>".$row_paq[0]."&nbsp&nbsp</font></td>";
								      echo "<td align=left  bgcolor='".$color."'><font size=1>".$row_paq[1]."&nbsp&nbsp</font></td>";
							    	  echo "<td align=right bgcolor='".$color."' colspan=6><font size=1>".number_format($row_paq[2],2,'.',',')."&nbsp&nbsp</font></td>";
							    	  echo "</tr>";
							    	  
							    	  // total de la suma de los paquetes 2007-11-14
							    	  $totpaq=$totpaq+$row_paq[2];
							    	
							    	
							    	 // para traer los conceptos que no pertenecen al paquete pero que estan en la factura
				    	    
							    	  $q = " SELECT Tcarconcod, Grudes, fdeter, fdepte, Tcarvto, grutip"
						          ."   FROM ".$wbasedato."_000066, ".$wbasedato."_000106, ".$wbasedato."_000004, ".$wbasedato."_000065"
						          ."  WHERE rcfffa    = '".$wfenffa."'"
						          ."    AND rcffac    = '".$wfenfac."'" 
						          ."    AND fdefue = rcfffa" 
						          ."    AND fdedoc = rcffac" 
						          ."	AND grucod = fdecon" 
						          ."    AND ".$wbasedato."_000106.id =Rcfreg" 
						          ."    AND Tcarconcod = Grucod" 
						          ."    and  Tcartfa !='PAQUETE'" 
						          ."    and  Tcarfac ='S' " 
						          ."    AND Tcarest = 'on'" 
						          ."    AND Rcfest ='on'" 
						          ."    AND Fdeest ='on'" 
						          ."    AND Gruabo='off'" 
						          ."    AND Rcfreg  not in (SELECT Movpaqreg from ".$wbasedato."_000115 where Movpaqhis=".$wfenhis." and Movpaqing=".$wfening." and  Movpaqcod='".$row_paq[0]."' and Movpaqest='on')";
						      $res_reg = mysql_query($q,$conex);   
						      $num_reg = mysql_num_rows($res_reg);
						            }
							        
							     }
							    
					    	  $wsw="off";
					    	  
					    	    if( !isset($num_reg) ){
					    	  		$num_reg = 0;
					    	  	}
					    	  
					    	    $wacuter=0;
								$wacucli=0;
								$wacucpt=0;
								$wtotter=0;
								$wtotcli=0;
								$wtotcpt=0;
								$wtotpar=0;
					    	     if ($num_reg>0)
					    	     {
						    	     for ($x=1;$x<=$num_reg;$x++)
								             {
									          $row_reg = mysql_fetch_array($res_reg);
									          //echo $row_reg[5];
									          
									          if($row_reg[5]=='C')   //si es compartido el concepto
								            	{
								            		$query = "SELECT mednom ".
							    	    	    		     "  FROM ".$empresa."_000051 ".
							        	    	    	 	 " WHERE meddoc= '".$row_reg[2]."'";
						                            $res = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
						    						$num1 = mysql_num_rows($res);
						
						    						if($num1 > 0)
													{
														$row1 = mysql_fetch_array($res);
									     				$wmednom=$row1[0];
													}
													else
													{
														$wmednom="NO EXISTE";
													}
						
													$wvalter=$row_reg[4]*($row_reg[3]/100);
													$wvalcli=$row_reg[4]-$wvalter;
								            	}
								            	else
								            	{
								            		$wvalter=0;
								            		$wvalcli=$row_reg[4];
								            		$row_reg[2]=" ";
								            		$wmednom=" ";
								            	}
						
								            	$wacuter=$wacuter+$wvalter;
									     		$wacucli=$wacucli+$wvalcli;
									     		$wacucpt=$wacucpt+$row_reg[4];
									      		$wtotpar=$wtotpar+$row_reg[4];			 // Acumula para el valor parcial
									           
									           //aca se pintan los conceptos que no pertenecen al paquete pero que estan en la factura
									            echo "<tr>";
												echo "<td align=left  bgcolor='".$color."'><font size=1>".$row_reg[0]."&nbsp&nbsp</font></td>";
										    	echo "<td align=left  bgcolor='".$color."'><font size=1>".$row_reg[1]."&nbsp&nbsp</font></td>";
									    		echo "<td align=left  bgcolor='".$color."'><font size=1>".$row_reg[2]."&nbsp&nbsp</font></td>";
									    		echo "<td align=left  bgcolor='".$color."'><font size=1>".$wmednom."&nbsp&nbsp</font></td>";
									     		echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wvalcli,2,'.',',')."&nbsp&nbsp</font></td>";
									     		echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wvalter,2,'.',',')."&nbsp&nbsp</font></td>";
									     		echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($row_reg[4],2,'.',',')."&nbsp&nbsp</font></td>";
									     		echo "</tr>";
									     		
								             }
								             
								             //$wtotpaq=$wtotpar+$row_paq[2];
								             // el total de la suma de los paquetes mas el resto del valor de la factura 2007-11-14
								             $wtotpaq=$wtotpar+$totpaq;
								             
					    	     }
					    	     else
					    	     {
					    	     	if( !isset($totpaq) ){
					    	  			$totpaq = 0;
					    	  		}
					    	     	$wtotpaq=$wtotpar+$totpaq;
					    	     }
					    	   
				    	     }
				    	      
				    	 }     
			     	    
					    if((($i==26) and ($i<$num)))   // Numero total de cptos por factura
			     		{
				     		for ($m=$i;$m<=$num;$m++)
		        			   {
			        			$row = mysql_fetch_array($err);

	    	    				if($row[5]=='C')
	    	    				{
	        						$wvalter=$row[4]*($row[3]/100);
									$wvalcli=$row[4]-$wvalter;
	    	    				}
	    	    				else
	    	    				{
	    	    					$wvalter=0;
		            				$wvalcli=$row[4];
	    	    				}

								$wacuter=$wacuter+$wvalter;
			     				$wacucli=$wacucli+$wvalcli;
			     				$wacucpt=$wacucpt+$row[4];
	        					$wtotter=$wtotter+$wvalter;
			     				$wtotcli=$wtotcli+$wvalcli;
			     				$wtotcpt=$wtotcpt+$row[4];
			     				$wtotpar=$wtotpar+$row[4];		// Acumula para el valor parcial
			     			   }

	        				$i=$num;
			     			echo "<tr>";
			     			echo "<td colspan=2 align=right bgcolor=".$color."><font size=2><b>Otros Conceptos </b></font></td>";
							echo "<td colspan=2 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
							echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wtotcli,2,'.',',')."&nbsp&nbsp</font></td>";
			     			echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wtotter,2,'.',',')."&nbsp&nbsp</font></td>";
			     			echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wtotcpt,2,'.',',')."&nbsp&nbsp</font></td>";
				    		echo "</tr>";

				    		echo "<tr>";
			       			echo "<td colspan=4 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
			       			if ($wsw=="on")        //Solo imprime si No es paquete, si al pasar por aca esta en 'on' es porque no es paquete
			       			   {
			     				echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     				echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
		     			       }	
		     			      else
		     			         {
			     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
			     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
		     			         } 
			     			echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     			echo "</tr>";

			       			echo "<tr>";
			       			echo "<td colspan=2 align=right bgcolor=".$color."><font size=1><b>TOTAL GENERAL DE LOS SERVICIOS: </b></font></td>";
			       			echo "<td colspan=2 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
			       			if ($wsw=="on")        //Solo imprime si No es paquete, si al pasar por aca esta en 'on' es porque no es paquete
			       			   {
								echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacucli,2,'.',',')."&nbsp&nbsp</font></td>";
			     				echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacuter,2,'.',',')."&nbsp&nbsp</font></td>";
		     			       }	
		     			      else
		     			         {
			     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
			     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
		     			         }  
			     			echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacucpt,2,'.',',')."&nbsp&nbsp</font></td>";
				    		echo "</tr>";
			       		}
			       		else
			       		  {
			       			if($i==$num)
			       			{
			       				echo "<tr>";
			       				echo "<td colspan=4 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
			       				if ($wsw=="on")   //Solo imprime si No es paquete, si al pasar por aca esta en 'on' es porque no es paquete
			       			       {
			     					echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     					echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
		     				       }	
		     				      else
			     			         {
				     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
				     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
			     			         }  
			     				echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     				echo "</tr>";

			       				echo "<tr>";
			       				echo "<td colspan=2 align=right bgcolor=".$color."><font size=1><b>TOTAL GENERAL DE LOS SERVICIOS: </b></font></td>";
			       				echo "<td colspan=2 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
			       				if ($wsw=="on")   //Solo imprime si No es paquete, si al pasar por aca esta en 'on' es porque no es paquete
			       			       {
								    echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacucli,2,'.',',')."&nbsp&nbsp</font></td>";
			     				    echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacuter,2,'.',',')."&nbsp&nbsp</font></td>";
		     				       } 
		     				      else
			     			         {
				     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
				     				  echo "<td align=right bgcolor='".$color."'>&nbsp</td>";
				     				  
				     				  if (isset($wtotpaq)) $wacucpt=$wtotpaq;
			     			         }  
			     				echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacucpt,2,'.',',')."&nbsp&nbsp</font></td>";
				    			echo "</tr>";

			       				$m=6-$i;
			       				for ($n=1;$n<=$m;$n++)
			       				{
				       				echo "<tr><td colspan=7 bgcolor=".$color."><br></td></tr>";
			       				}
			       			}
			       		 }
	        		}
	        		echo "</table>";

	        		if ($wsw=='off')
				       $wtotpar=$wacucpt;
	        		
	        		$wsubtot=$wtotpar-$wfendes;
	        		$wcmocop=$wfencop+$wfencmo;
	        		$wvalnet=($wsubtot+$wfenviv)-$wfenabo-$wcmocop;
	        		$wvlrlet=montoescrito($wvalnet);

	        		echo "<tr><td colspan=2><font size=1><b>SON (LETRAS):</font></b><br><font size=1><b>&nbsp&nbsp&nbsp&nbsp$wvlrlet</font></b></td>";
	        		echo "<td rowspan=2><font size=1><b>PARCIAL<br>DESCUENTO<br>SUBTOTAL<br>IVA<br>ANT_EXC<br>COP_CMO_FRQ</font></b></td>";
	        		echo "<td align=right rowspan=2><font size=1><b>".number_format($wtotpar,2,'.',',')."<br>".number_format($wfendes,2,'.',',')."<br>".number_format($wsubtot,2,'.',',')."<br>".number_format($wfenviv,2,'.',',')."<br>".number_format($wfenabo,2,'.',',')."<br>".number_format($wcmocop,2,'.',',')."</font></b></td></tr>";
	        		echo "<tr><td align=center width=30%><font size=1><b>INFORMACION TRIBUTARIA</b><br>I.V.A REGIMEN COMUN<br>AGENTE RETENEDOR IVA</font></td>";
	        		echo "<td align=center width=40%><font size=1><b>NO SOMOS GRANDES CONTRIBUYENTES<br>NO SOMOS AUTORETENEDORES</font></b></td></tr>";
	        		echo "<tr><td align=center colspan=2><font size=1><b>FAVOR CANCELAR CON CHEQUE CRUZADO A FAVOR DE '".$rowx['Cfgnom']."'</font></b></td>";
	        		echo "<td><font size=2><b>NETO A PAGAR</font></b></td>";
	        		echo "<td align=right><font size=2><b>".number_format($wvalnet,2,'.',',')."</font></b></td></tr>";

					echo "<tr>";
					echo "<td colspan=4><table width=100% align=center border=1 bordercolor=000000 cellspacing=0>";
					echo "<td align=left width=22%><font size=1><b>ELABORADO POR <br><br><br><br><br>".$wfenseg."</font></b></td>";
					echo "<td align=left width=25%><font size=1><b>RECIBI CONFORME<br><br><br><br>____________________________<br>C.C.O NIT.</font></b></td>";
					echo "<td align=left width=22%><font size=1><b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspABREVIATURAS<br>ANT.: ANTICIPO<br>EXC.: EXCEDENTE<br>
					  	COP.: COPAGO<br>CMO.: CUOTA MODERADORA<br>FRQ.: FRANQUICIA</font></b></td>";
					  	
					 // query para traer el numero de orden (autorizacion) de la tabla 101 de clisur
					
					  $q = " SELECT Ingord"
				          ."   FROM ".$wbasedato."_000101"
				          ."  WHERE Inghis    = '".$wfenhis."'"
				          ."    AND Ingnin    = '".$wfening."'" 
				          ."    ";
				      $res_aut = mysql_query($q,$conex);   
				      $num_aut = mysql_num_rows($res_aut);	  	
					  $row_aut = mysql_fetch_array($res_aut);
					  //echo $row_aut[0];	 	
					  	//echo $num_aut;
					  	
					  	
					echo "<td align=left width=31%><font size=1><b>OBSERVACIONES: NUMERO DE AUTORIZACION: ".$row_aut[0]."<br><br><br><br><br></font></b></td>";
					echo "</table>";
					echo "</tr>";
					
					$factu=explode("-",$wfactura);
					
					$q = " SELECT Fenrln"
				          ."   FROM ".$wbasedato."_000018"
				          ."  WHERE Fenffa    = '".$factu[0]."'"
				          ."    AND Fenfac    = '".$factu[1]."-".$factu[2]."'";
				      $res = mysql_query($q,$conex);   
				      $num = mysql_num_rows($res);	  	
					  $rowr = mysql_fetch_array($res);
					  
					echo "<tr><td align=left colspan=4><font size=1>".$rowr[0]."</font></td></tr>";
			
					/*echo "<tr><td align=left colspan=4><font size=1>Factura Impresa por computador RESOLUCION DIAN No.110000225722 Autorizada del CS-1 al CS-9999999<br>
							   La presente factura se asimila para todos sus efectos a una letra de cambio de conformidad con el articulo 774 y 779 del codigo de comercio.</font></td></tr>";*/
				}
				else
				  {
					$wacucli=0;
					$wacuter=0;
					$wacucpt=0;
					$wtotpar=0;


					for ($i=1;$i<=7;$i++)
	        		  {
						echo "<tr><td colspan=7 bgcolor=".$color."><br></td></tr>";
	        		  }

	        		echo "<tr>";
			       	echo "<td colspan=4 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
			     	echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     	echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     	echo "<td align=right bgcolor='".$color."'><font size=1>____________&nbsp&nbsp</font></td>";
			     	echo "</tr>";

			       	echo "<tr>";
			       	echo "<td colspan=2 align=right bgcolor=".$color."><font size=1><b>TOTAL GENERAL DE LOS SERVICIOS: </b></font></td>";
			       	echo "<td colspan=2 align=right bgcolor='".$color."'><font size=1>&nbsp&nbsp</font></td>";
					echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacucli,2,'.',',')."&nbsp&nbsp</font></td>";
			     	echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacuter,2,'.',',')."&nbsp&nbsp</font></td>";
			     	echo "<td align=right bgcolor='".$color."'><font size=1>".number_format($wacucpt,2,'.',',')."&nbsp&nbsp</font></td>";
				    echo "</tr>";

				    echo "</table>";

				    $wsubtot=$wtotpar-$wfendes;
	        		$wcmocop=$wfencop+$wfencmo;
	        		$wvalnet=($wsubtot+$wfenviv)-$wfenabo-$wcmocop;
	        		$wvlrlet=montoescrito($wvalnet);

	        		echo "<tr><td colspan=2><font size=1><b>SON (LETRAS):</font></b><br><font size=1><b>&nbsp&nbsp&nbsp&nbsp$wvlrlet</font></b></td>";
	        		echo "<td rowspan=2><font size=1><b>PARCIAL<br>DESCUENTO<br>SUBTOTAL<br>IVA<br>ANT_EXC<br>COP_CMO_FRQ</font></b></td>";
	        		echo "<td align=right rowspan=2><font size=1><b>".number_format($wtotpar,2,'.',',')."<br>".number_format($wfendes,2,'.',',')."<br>".number_format($wsubtot,2,'.',',')."<br>".number_format($wfenviv,2,'.',',')."<br>".number_format($wfenabo,2,'.',',')."<br>".number_format($wcmocop,2,'.',',')."</font></b></td></tr>";
	        		echo "<tr><td align=center width=30%><font size=1><b>INFORMACION TRIBUTARIA</b><br>I.V.A REGIMEN COMUN<br>AGENTE RETENEDOR IVA</font></td>";
	        		echo "<td align=center width=40%><font size=1><b>NO SOMOS GRANDES CONTRIBUYENTES<br>NO SOMOS AUTORETENEDORES</font></b></td></tr>";
	        		echo "<tr><td align=center colspan=2><font size=1><b>FAVOR CANCELAR CON CHEQUE CRUZADO A FAVOR DE 'CLINICA DEL SUR S.A.'</font></b></td>";
	        		echo "<td><font size=2><b>NETO A PAGAR</font></b></td>";
	        		echo "<td align=right><font size=2><b>".number_format($wvalnet,2,'.',',')."</font></b></td></tr>";

					echo "<tr>";
					echo "<td colspan=4><table width=100% align=center border=1 bordercolor=000000 cellspacing=0>";
					echo "<td align=left width=22%><font size=1><b>ELABORADO POR <br><br><br><br><br>".$wfenseg."</font></b></td>";
					echo "<td align=left width=25%><font size=1><b>RECIBI CONFORME<br><br><br><br>____________________________<br>C.C.O NIT.</font></b></td>";
					echo "<td align=left width=22%><font size=1><b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspABREVIATURAS<br>ANT.: ANTICIPO<br>EXC.: EXCEDENTE<br>
					  	COP.: COPAGO<br>CMO.: CUOTA MODERADORA<br>FRQ.: FRANQUICIA</font></b></td>";
					
					// query para traer el numero de orden (autorizacion) de la tabla 101 de clisur
					
					  $q = " SELECT Ingord"
				          ."   FROM ".$wbasedato."_000101"
				          ."  WHERE Inghis    = '".$wfenhis."'"
				          ."    AND Ingnin    = '".$wfenfac."'" 
				          ."    ";
				      $res_aut = mysql_query($q,$conex);   
				      $num_aut = mysql_num_rows($res_aut);	  	
					  echo $q;	
					  	
					echo "<td align=left width=31%><font size=1><b>OBSERVACIONES: <br><br><br><br><br><br></font></b></td>";
					echo "</table>";
					echo "</tr>";


					$factu=explode("-",$wfactura);
					
					$q = " SELECT Fenrln"
				          ."   FROM ".$wbasedato."_000018"
				          ."  WHERE Fenffa    = '".$factu[0]."'"
				          ."    AND Fenfac    = '".$factu[1]."-".$factu[2]."'";
				      $res = mysql_query($q,$conex);   
				      $num = mysql_num_rows($res);	  	
					  $rowr = mysql_fetch_array($res);
					  
					echo "<tr><td align=left colspan=4><font size=1>".$rowr[0]."</font></td></tr>";
					/*echo "<tr><td align=left colspan=4><font size=1>Factura Impresa por computador RESOLUCION DIAN No.110000225722 Autorizada del CS-1 al CS-9999999<br>
							   La presente factura se asimila para todos sus efectos a una letra de cambio de conformidad con el articulo 774 y 779 del codigo de comercio.</font></td></tr>";*/

				  }
				  echo "</table>";
				  echo "</tr>";
				  imp_lin_ctrl($empresa,$conex,$wfenimp,$wfenest,$wfact[0],$wfact[1],$wfact[2],$wfenseg,$wfec,$whor);
			}
	  	}
	}
	echo "</body>";
	echo "</html>";
	include_once("free.php");
}
?>