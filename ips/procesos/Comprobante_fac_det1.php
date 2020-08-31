<head>
  <title>COMPROBANTE DE CARTERA DETALLADO</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.comprobante_fac_det.submit();
	}
	
</script>
<script type="text/javascript">
	function enter1()
	{
	   document.forms.comprobante_fac_det.submit();
	   alert ("Pulse de nuevo la tecla ENTER");
	}
	
</script>

<?php
include_once("conex.php");
  /************************************************
   * PROGRAMA PARA comprobantes_cartera_detallado *
   ************************************************/
   
//==================================================================================================================================
//PROGRAMA                   : comprobantes_cartera_detallado.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Noviembre 20 de 2006
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Enero 5 de 2007"; 
//DESCRIPCION
//====================================================================================================================================\\
//Objetivo:                                                                                                                           \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\  
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\         

session_start();

if (!isset($user))
	{
	 if(!isset($_SESSION['user']))
		session_register("user");
	}

if(!isset($_SESSION['user']))
	echo "error";
else
{	   
  

//			      or die("No se ralizo Conexion");
  

 
  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");
  					    

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user)); 
  
  $wfecha=date("Y-m-d");   
  $hora = (string)date("H:i:s");	              
    
  echo "<form name='comprobante_fac_det' action='comprobante_fac_det1.php' method=post>";
  
  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  
  
  //===========================================================================================================================================
  function ver_comprobante_detallado($wfnum, $wfres)
    {
	 
	 global $wbasedato;
	 global $conex; 
	 global $wtotcomD;
	 global $wtotcomC; 
	 global $wtotdocD;
	 global $wtotdocC; 
	     
	 
	  //On   
	  //			 $hora = (string)date("H:i:s");
      //           echo "1er Query Tiempo 1 : ".$hora."<br>";
                 
	 
	 $row  = mysql_fetch_array($wfres); 
	 
	 //====================================================================================================================
	 // OJO ESTE PROCESO AYUDA A BUSCAR DESCUADRES -- LEERLO
	 //====================================================================================================================
	 //Este echo muestra cada una de las facturas procesadas, esto sirve para buscar descuadres cuando en este comprobante
	 //sale mas dinero que el comprobante resumido.
	 /* echo $row[1]." | ".$row[6]."<br>"; */
	 //====================================================================================================================
	 
	 
	 $i=1;
	 while ($i<=$wfnum)   
	    {
		 $wcta = $row[2];
		 
		 $wtotctaD=0;   //Total cuenta Debitos
		 $wtotctaC=0;   //Total cuenta Créditos
		 while ($i<=$wfnum and $wcta == $row[2]) 
		      {
			   if ($row[6] != 0)  //Si el valor es diferente cero
			      {   
				   //Con este procedimiento cambio el valor a absoluto y la naturaleza del valor de debito a credito o viceversa   
				   //Porque cuando el valor es negativo quiere decir que es un abono y por lo tanto los valores de estos conceptos
				   //deben mostrarse con naturaleza contraria.
				   if ($row[6] < 0)
				      {
				       if (trim($row[3])=="D")
				          $row[3]="C"; 
				         else 
					        if (trim($row[3])=="C") 
					           $row[3]="D"; 
				          
				       $row[6]=abs($row[6]);    //Paso el valor a absoluto   
			          }
			          
			       if ($i%2==0)
			          $wcolor="00FFFF";
			         else
			            $wcolor="";   
			        
			       $wnitter="";
			       $wnitnom="";     
			            
			       if ($row[5] != "" and $row[8]=="off")         //Si tiene tercero, lo busco en el maestro de terceros
				      {
					   $q= " SELECT mednom "
					      ."   FROM ".$wbasedato."_000051 "
					      ."  WHERE meddoc = '".$row[5]."'";
					   $rester = mysql_query($q,$conex);
	                   $rowter = mysql_fetch_array($rester);       
	                  
	                   if (trim($rowter[0]) != "")  //Nombre del tercero
	                      {
		                   $wnitter=$row[5];   
	                       $wnomter=$rowter[0];
                          } 
	                     else  //Si no existe busco el tercero en el maestro de empresas
	                        {
		                     $q= " SELECT empnom "
							    ."   FROM ".$wbasedato."_000024 "
							    ."  WHERE empnit = '".$row[5]."'";
							 $rester = mysql_query($q,$conex);
			                 $rowter = mysql_fetch_array($rester);   
			                 
			                 if (trim($rowter[0]) != "")
			                    {
				                 $wnitter=$row[5];     
			                     $wnomter=$rowter[0];
		                        } 
			                   else 
			                      {
				                   $wnitter=$row[5];       
	                               $wnomter="NO EXISTE"; 
                                  } 
                            }
				      }
				     else
                        {				    
	                     if ($row[8] == "on")   //Indica si se imprime el nombre del responsable de la factura particular o no
				            {
					         $wnitter=$row[9];    
					         $wnomter=$row[10];
					        }    
				           else
				              {
					           $wnitter="";    
					           $wnomter="";
					          }
				        }
			            
			            
			       if ($row[3]=="D")
			         {
				      echo "<tr>";   
				      echo "<td>".$row[0]."</td>";
				      echo "<td>".$row[1]."</td>";
					  echo "<td>".$row[2]."</td>";
					  echo "<td>&nbsp</td>";
					  echo "<td>".$row[4]."</td>";
					  //echo "<td>".$row[5]."</td>";
					  echo "<td>".$wnitter."</td>";
					  echo "<td>".$wnomter."</td>";
					  echo "<td align=right>".number_format($row[6],0,'.',',')."</td>";
					  echo "<td>&nbsp</td>";
					  echo "</tr>";
					  $wtotdocD=$wtotdocD+$row[6];
					  $wtotcomD=$wtotcomD+$row[6];
				     } 
				    else
				       if ($row[3]=="C")
				         {
					      echo "<tr>";   
						  echo "<td>".$row[0]."</td>";
						  echo "<td>".$row[1]."</td>";
						  echo "<td>".$row[2]."</td>";
						  echo "<td>&nbsp</td>";
						  echo "<td>".$row[4]."</td>";
						  echo "<td>".$wnitter."</td>";
						  echo "<td>".$wnomter."</td>";
						  echo "<td>&nbsp</td>";
						  echo "<td align=right>".number_format($row[6],0,'.',',')."</td>";
						  echo "</tr>";
						  $wtotdocC=$wtotdocC+$row[6];
						  $wtotcomC=$wtotcomC+$row[6];
						 }
				  } 	 
				 $row  = mysql_fetch_array($wfres); 
				 $i++; 
			 }
		}   
	}    
  
       
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA   
  //===========================================================================================================================================
  
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  
  //echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";
  echo "<p align=right><font size=1><b>Version: ".$wactualiz." &nbsp&nbsp&nbsp Autor: ".$wautor."</b></font></p>";
  //===========================================================================================================================================
  echo "<center><table border>";
  echo "<tr><td align=center rowspan=2 colspan=6><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=300 HEIGHT=100></td></tr>";
  echo "<tr><td align=center colspan=13 bgcolor=".$wcf2."><font size=5 text color=#FFFFFF><b>COMPROBANTE DE FACTURACION Y NOTAS DETALLADO</b></font></td></tr>";
  echo "</table>";
    
      if (((!isset($wfuecar) and !isset($wfec_i)) or (!isset($wfec_f)) or (!isset($wfue_com) and !isset($wfec_com)))) // and !isset($wgra_mov))
	     {
		  echo "<br>";   
		  echo "<center><table border>";   
		  
		  if (!isset($wfuecar)) $wfuecar="";
		  
		  echo "<tr>";   
	      $q =  " SELECT carfue, cardes "
	           ."   FROM ".$wbasedato."_000040, ".$wbasedato."_000078 "
		       ."  WHERE carest     = 'on' "
			   ."    AND carfue     = relfuecod "
			   ."    AND relfueest  = 'on' "
			   ."    AND (carndb    = 'on' "
			   ."     OR  carncr    = 'on' "
			   ."     OR  carfac    = 'on') "
			   ."  GROUP BY 1,2 "
			   ."  ORDER BY carfue ";  
			   
		  $res = mysql_query($q,$conex); 
		  $num = mysql_num_rows($res); 
			
		  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg.">Fuente cartera: </font></b><select name='wfuecar'>";
		  
		  if (isset($wfuecar))
		     echo "<option selected>".$wfuecar."</option>";  
		  
		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res); 
		      echo "<option>".$row[0]." - ".$row[1]."</option>";
		     }
		  echo "</select></td>";
		  
		  
		  //echo "<tr>";   
		  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fecha Inicial<br>(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='wfec_i' ></td>";     
		  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fecha Final<br>(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='wfec_f' ></td>";        
		  echo "</tr>";
		  echo "<tr>";
		  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fuente Contable Comprobante:</font></b><INPUT TYPE='text' NAME='wfue_com' ></td>";      
		  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Nro Dcto Comprobante:</font></b><INPUT TYPE='text' NAME='wdoc_com' ></td>";         
		  echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fecha del Comprobante:</font></b><INPUT TYPE='text' NAME='wfec_com' ></td>";     
		  echo "</tr>";
		  echo "</table>";
		 }
	    else
	       {
		     echo "<br><br>";  
		     echo "<center><table border=0>";  
		       
		     echo "<tr>";  
		     echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fuente de Cartera:</font></b>".$wfuecar."</td>";      
		     echo "<td align=center bgcolor=".$wcf." colspan=3><b><font text color=".$wclfg."> Fecha Inicial:</font></b>".$wfec_i." <b><font text color=".$wclfg.">&nbsp&nbsp&nbspFecha Final:</font></b>".$wfec_f."</td>";     
		  
		     //echo "<td align=center bgcolor=".$wcf." colspan=3><b><font text color=".$wclfg."> Fecha Inicial:</font></b>".$wfec_i." <b><font text color=".$wclfg.">&nbsp&nbsp&nbspFecha Final:</font></b>".$wfec_f."</td>";     
			 //echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fecha Final:</font></b>".$wfec_f."</td>";        
			 echo "</tr>";
			 echo "<tr>";
			 echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fuente Contable Comprobante:</font></b>".$wfue_com."</td>";      
			 echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Nro Dcto Comprobante:</font></b>".$wdoc_com."</td>";         
			 echo "<td align=left bgcolor=".$wcf." colspan=1><b><font text color=".$wclfg."> Fecha del Comprobante:</font></b>".$wfec_com."</td>";     
			 echo "</tr>";
			 echo "</table>";
			 echo "<br><br>";
		       
			 $wfuecar1=$wfuecar;
		       
		     $pos = strpos($wfuecar,"-");
		     $wfuecar = substr($wfuecar,0,$pos-1);  
		       
		     $q= " SELECT carncr, carndb "
			    ."   FROM ".$wbasedato."_000040 " 
			    ."  WHERE  carfue = '".$wfuecar."'"
			    ."    AND  carest = 'on' "
			    ."    AND (carndb    = 'on' "
			    ."     OR  carncr    = 'on' "
			    ."     OR  carfac    = 'on') "
			    ."  GROUP BY 1,2 ";  
			 $resfue = mysql_query($q,$conex);
		     $numfue = mysql_num_rows($resfue);
		     $rowfue = mysql_fetch_array($resfue); 
		     
		     //***************************************************************************************
		     //COMPROBANTE DE *** FACTURACION DETALLADO ***
		     //***************************************************************************************
		     //Si ambos son diferentes a 'on' indica que la fuente es de facturas
		     //Si alguno de los dos es off es porque corresponde a una fuente de notas
		     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")    //COMPROBANTE DE FACTURACION
		        {
			     //==============================================================================   
			     //ESTE ES EL QUERY BASE PARA EL COMPROBANTE DE FACTURACION   
			     //==============================================================================
			     $q= " CREATE TEMPORARY TABLE if not exists tempo1 as "
			        ." SELECT fenffa as fue, fenfac as doc, mid(fentip,1,instr(fentip,'-')-1) as tip"
				    ."   FROM ".$wbasedato."_000018 "
				    ."  WHERE fenffa = '".$wfuecar."'"
				    ."    AND fenfec between '".$wfec_i."' AND '".$wfec_f."'"
				    ."    AND fenest = 'on' "
				    ."  GROUP BY 1,2 ";  
				 $res = mysql_query($q,$conex);
				}    
			   else
	             { 
		          //***************************************************************************************   
	              //COMPROBANTE DE *** NOTAS DE CARTERA ***
	              //***************************************************************************************
			      //Si ambos son diferentes a 'off' indica que la fuente es de notas
			      //Si alguno de los dos es off es porque corresponde a una fuente de notas
			      if ($numfue > 0 and $rowfue[0] == "on" or $rowfue[1] == "on")    //COMPROBANTE DE NOTAS DE CARTERA
			         {
				      //==============================================================================   
				      //ESTE ES EL QUERY BASE PARA EL COMPROBANTE DE NOTAS DE CARTERA  
				      //==============================================================================
				      $q= " CREATE TEMPORARY TABLE if not exists tempo1 as "
				         ." SELECT renfue as fue, rennum as doc, mid(emptem,1,instr(emptem,'-')-1) as tip"
				         ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000018 "
				         ."  WHERE renfue = '".$wfuecar."'"
						 //."    AND rennum = '737' "   //On
				         ."    AND renfec between '".$wfec_i."' AND '".$wfec_f."'"
				         ."    AND renest = 'on' "
				         ."    AND renfue = rdefue "
				         ."    AND rennum = rdenum "
				         ."    AND rdeffa = fenffa "
				         ."    AND rdefac = fenfac "
				         ."    AND fencod = empcod "
				         ."  GROUP BY 1,2 ";  
				      $res = mysql_query($q,$conex);
				     }   
		         }   
			     echo "<center><table border=0>";
		         
			     echo "<th align=CENTER bgcolor=DDDDDD><font size=2>FUENTE</font></th>";
			     echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DOCUMENTO</font></th>";
		         echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CUENTA</font></th>";
				 echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOMBRE</font></th>";
				 echo "<th align=CENTER bgcolor=DDDDDD><font size=2>C.COSTO</font></th>";
				 echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NIT/CED</font></th>";
				 echo "<th align=CENTER bgcolor=DDDDDD><font size=2>NOMBRE</font></th>";
				 echo "<th align=CENTER bgcolor=DDDDDD><font size=2>DEBITOS</font></th>";
				 echo "<th align=CENTER bgcolor=DDDDDD><font size=2>CREDITOS</font></th>";
		         
				 ///$wtotcomD=0;       //Total comprobante Debitos
			     ///$wtotcomC=0;       //Total comprobante Creditos
				 
			     //=============================================================================================================================================================================
			     //=============================================================================================================================================================================
			     // ACA TRAIGO TODOS LOS DOCUMENTOS SELECCIONADOS
			     //=============================================================================================================================================================================
			     //=============================================================================================================================================================================
			     $q = " SELECT fue, doc, tip "
			         ."   FROM tempo1 "
					 ."  GROUP BY fue, doc, tip ";
			     $resdoc = mysql_query($q,$conex);
			     $numdoc = mysql_num_rows($resdoc);  
			     //=============================================================================================================================================================================  
			     
			     $wtotcomD=0;           //Total comprobante Debitos
			     $wtotcomC=0;           //Total comprobante Creditos
			     
				 for ($j=1;$j<=$numdoc;$j++)
				   {  
					 $wtotdocD=0;       //Total comprobante Debitos
			         $wtotdocC=0;       //Total comprobante Creditos
			       
				     $rowdoc = mysql_fetch_array($resdoc); 
				     
				     
				     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")
				        {
					     $q= " SELECT fenffa, fenfac, relfuecta, relfuenat, fencco, fennit, sum(fenval), empnom, relfuenit, fendpa as dre, fennpa as nre"
						    ."   FROM ".$wbasedato."_000018,".$wbasedato."_000078,".$wbasedato."_000024 "
						    ."  WHERE fenffa    = '".$rowdoc[0]."'"
						    ."    AND fenfac    = '".$rowdoc[1]."'"
						    ."    AND fenffa    = relfuecod "
						    ."    AND relfuetem = '".$rowdoc[2]."'"
						    ."    AND fenest    = 'on' "
						    ."    AND fenres    = empcod "
						    ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 "
						    ."  ORDER BY 1,2 ";
					     
						 $wnat1="D";
			             $wnat2="C"; 	    
						}
					   else 
					      {
						   //===================================================================================  
					       //Con este query se obtiene el comprobante resumido por ** fuente de notas **
					       $q= " SELECT renfue, rennum, relfuecta, relfuenat, rencco, empnit, renvca, empnom, relfuenit, fencod as dre, empnom as nre "
					          ."   FROM ".$wbasedato."_000020,".$wbasedato."_000078, ".$wbasedato."_000024, ".$wbasedato."_000021, ".$wbasedato."_000018 "
					          ."  WHERE renfue    = '".$rowdoc[0]."'"
					          ."    AND rennum    = '".$rowdoc[1]."'"
					          ."    AND renfue    = relfuecod "
					          ."    AND relfuetem = '".$rowdoc[2]."'"
					          ."    AND renest    = 'on' "
					          ."    AND renfue    = rdefue "
					          ."    AND rennum    = rdenum "
					          ."    AND rdeffa    = fenffa "
					          ."    AND rdefac    = fenfac "
					          ."    AND fencod    = empcod "
					          //."  GROUP BY 1, 2,3,4,5,6,7,8,9,10,11 "
							  ."  GROUP BY 1, 2,3,4,5,6,7,8,9,11 "
					          ."  ORDER BY 1, 2 "; 
					       $wnat1="C";
			               $wnat2="D";  

//On
echo $q."<br>";						   
					      }    
					 $res = mysql_query($q,$conex);
			         $num = mysql_num_rows($res); 
			         
					 //On
				 echo "numdoc : ".$num."<br>";
					 
			         ver_comprobante_detallado($num,$res);
			         
			         //=========================================================================================================================================================================
				     //=========================================================================================================================================================================
				     //CON ESTE PROCEDIMIENTO VERIFICO QUE LA RELACION CONCEPTO, CCO Y TIPO DE EMPRESA EXISTAN PARA CADA UNA DE LAS FACTURAS
				     //Y POR CADA CONCEPTO-CENTRO DE COSTOS DE LA FACTURA.
				     //=========================================================================================================================================================================
				     //=========================================================================================================================================================================
				     $q= " SELECT fdefue, fdedoc, fdecon, fdecco, '".$rowdoc[2]."'"
				        ."   FROM ".$wbasedato."_000065 "
				        ."  WHERE fdefue  = '".$rowdoc[0]."'"
				        ."    AND fdedoc  = '".$rowdoc[1]."'"
				        ."    AND fdeest  = 'on' ";
				     $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);
				     
				     if ($num > 0)
				        {
					     for ($i=1;$i<=$num;$i++)
					         {   
					          $row = mysql_fetch_array($res); 
					           
					          $q = " SELECT count(*) "
					              ."   FROM ".$wbasedato."_000077 "    
					              ."  WHERE relconcon = '".$row[2]."'"
					              ."    AND relconcco = '".$row[3]."'"
					              ."    AND relcontem = '".$row[4]."'";
					          $res_exi = mysql_query($q,$conex);
					          $row_exi = mysql_fetch_array($res_exi); 
					          
					          if ($row_exi[0] == 0)
					             echo "Falta relación del concepto: ".$row[2]." con el centro de costo: ".$row[3]." y el tipo de empresa: '".$row[4]."' para la factura: ".$row[0]."-".$row[1]."<br>";
					         }   
				        } 
				        
				        
				     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")
				        {   
					     //=========================================================================================================================================================================         
					     //para las cuentas de ** INGRESOS POR CONCEPTO **.   
					     //=========================================================================================================================================================================			        		     
					     $q=  " SELECT fdefue, fdedoc, relconcin, '".$wnat2."', fdecco, fdeter, sum(round(fdevco*((100-fdepte)/100))), '', relconnit, fendpa as dre, fennpa as nre "
					         ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000018 "
						     ."  WHERE fdefue    = '".$rowdoc[0]."'"
						     ."    AND fdedoc    = '".$rowdoc[1]."'"
						     ."    AND fdecon    = relconcon "
						     ."    AND fdecco    = relconcco " 
						     ."    AND relcontem = '".$rowdoc[2]."'"
						     ."    AND fdeest    = 'on' " 
						     ."    AND fdefue    = fenffa "
						     ."    AND fdedoc    = fenfac "
						     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
						 $res = mysql_query($q,$conex);
					     $num = mysql_num_rows($res);
					    
					     ver_comprobante_detallado($num,$res);
					     
					     //DESCUENTO DE INGRESOS 
					     $q=  " SELECT fdefue, fdedoc, relconcdi, '".$wnat1."', fdecco, fdeter, sum(round(fdevde*((100-fdepte)/100))), '', relconnit, fendpa as dre, fennpa as nre "
						     ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000018 "
						     ."  WHERE fdefue    = '".$rowdoc[0]."'"
						     ."    AND fdedoc    = '".$rowdoc[1]."'"
						     ."    AND fdecon    = relconcon "
						     ."    AND fdecco    = relconcco " 
						     ."    AND relcontem = '".$rowdoc[2]."'"
						     ."    AND fdeest    = 'on' " 
						     ."    AND fdefue    = fenffa "
						     ."    AND fdedoc    = fenfac "
						     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
						 $res = mysql_query($q,$conex);
					     $num = mysql_num_rows($res);
					     
					     
					     ver_comprobante_detallado($num,$res);
					     
					     //===========================================================================
						 //Con este query traigo el comprobante resumido por concepto de facturacion
						 //para las cuentas de ** TERCEROS POR CADA CONCEPTO **.
					     $q=  " SELECT fdefue, fdedoc, relconcte, '".$wnat2."', fdecco, fdeter, sum(round(fdevco*(fdepte/100))), '', relconnit, fendpa as dre, fennpa as nre "
						     ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000018 "
						     ."  WHERE fdefue    = '".$rowdoc[0]."'"
						     ."    AND fdedoc    = '".$rowdoc[1]."'"
						     ."    AND fdecon    = relconcon "
						     ."    AND fdecco    = relconcco " 
						     ."    AND relcontem = '".$rowdoc[2]."'"
						     ."    AND fdeest    = 'on' " 
						     ."    AND fdefue    = fenffa "
						     ."    AND fdedoc    = fenfac "
						     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
			             $res = mysql_query($q,$conex);
					     $num = mysql_num_rows($res);
					    
					     ver_comprobante_detallado($num,$res);
					     
					     //===========================================================================
						 //Con este query traigo el comprobante resumido por concepto de facturacion
						 //para las cuentas de ** DESCUENTO DE TERCEROS **.
					     $q=  " SELECT fdefue, fdedoc, relconcdt, '".$wnat1."', fdecco, fdeter, sum(round(fdevde*(fdepte/100))), '', relconnit, fendpa as dre, fennpa as nre "
						     ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000018 " 
						     ."  WHERE fdefue    = '".$rowdoc[0]."'"
						     ."    AND fdedoc    = '".$rowdoc[1]."'"
						     ."    AND fdecon    = relconcon "
						     ."    AND fdecco    = relconcco " 
						     ."    AND relcontem = '".$rowdoc[2]."'"
						     ."    AND fdeest    = 'on' " 
						     ."    AND fdefue    = fenffa "
						     ."    AND fdedoc    = fenfac "
						     ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
			             $res = mysql_query($q,$conex);
					     $num = mysql_num_rows($res);
					    
					     ver_comprobante_detallado($num,$res);
				        }
				       else  //Entra por aca cuando la fuente es de Notas de cartera
				          {   
						   //=========================================================================================================================================================================         
						   //para las cuentas de ** INGRESOS POR CONCEPTO **.   
						   //=========================================================================================================================================================================			        		     
						   $q=  " SELECT fdefue, fdedoc, relconcin, '".$wnat2."', fdecco, fdeter, sum(round(fdevco*((100-fdepte)/100))), '', relconnit, '' as dre, '' as nre "
						       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
						       ."  WHERE fdefue    = '".$rowdoc[0]."'"
						       ."    AND fdedoc    = '".$rowdoc[1]."'"
						       ."    AND fdecon    = relconcon "
							   ."    AND fdecco    = relconcco " 
							   ."    AND relcontem = '".$rowdoc[2]."'"
							   ."    AND fdeest    = 'on' " 
							   ."    AND fdefue    = carfue "
							   ."    AND carcfa    = 'on' "
							   ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
						   $res = mysql_query($q,$conex);
						   $num = mysql_num_rows($res);
						    
						   ver_comprobante_detallado($num,$res);
						     
						   //DESCUENTO DE INGRESOS 
						   $q=  " SELECT fdefue, fdedoc, relconcdi, '".$wnat1."', fdecco, fdeter, sum(round(fdevde*((100-fdepte)/100))), '', relconnit, '' as dre, '' as nre "
						       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
						       ."  WHERE fdefue    = '".$rowdoc[0]."'"
						       ."    AND fdedoc    = '".$rowdoc[1]."'"
						       ."    AND fdecon    = relconcon "
						       ."    AND fdecco    = relconcco " 
						       ."    AND relcontem = '".$rowdoc[2]."'"
						       ."    AND fdeest    = 'on' " 
						       ."    AND fdefue    = carfue "
							   ."    AND carcfa    = 'on' "
						       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
						   $res = mysql_query($q,$conex);
						   $num = mysql_num_rows($res);
						     
						     
						   ver_comprobante_detallado($num,$res);
						     
						   //===========================================================================
						   //Con este query traigo el comprobante resumido por concepto de facturacion
						   //para las cuentas de ** TERCEROS POR CADA CONCEPTO **.
						   $q=  " SELECT fdefue, fdedoc, relconcte, '".$wnat2."', fdecco, fdeter, sum(round(fdevco*(fdepte/100))), '', relconnit, '' as dre, '' as nre "
						       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 "
						       ."  WHERE fdefue    = '".$rowdoc[0]."'"
						       ."    AND fdedoc    = '".$rowdoc[1]."'"
						       ."    AND fdecon    = relconcon "
						       ."    AND fdecco    = relconcco " 
						       ."    AND relcontem = '".$rowdoc[2]."'"
						       ."    AND fdeest    = 'on' "
						       ."    AND fdefue    = carfue "
							   ."    AND carcfa    = 'on' " 
						       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
				           $res = mysql_query($q,$conex);
						   $num = mysql_num_rows($res);
						    
						   ver_comprobante_detallado($num,$res);
						     
						   //===========================================================================
						   //Con este query traigo el comprobante resumido por concepto de facturacion
						   //para las cuentas de ** DESCUENTO DE TERCEROS **.
						   $q=  " SELECT fdefue, fdedoc, relconcdt, '".$wnat1."', fdecco, fdeter, sum(round(fdevde*(fdepte/100))), '', relconnit, '' as dre, '' as nre "
						       ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000040 " 
						       ."  WHERE fdefue    = '".$rowdoc[0]."'"
						       ."    AND fdedoc    = '".$rowdoc[1]."'"
						       ."    AND fdecon    = relconcon "
						       ."    AND fdecco    = relconcco " 
						       ."    AND relcontem = '".$rowdoc[2]."'"
						       ."    AND fdeest    = 'on' "
						       ."    AND fdefue    = carfue "
							   ."    AND carcfa    = 'on' " 
						       ."  GROUP BY 1,2,3,4,5,6,8,9,10,11 ";
				           $res = mysql_query($q,$conex);
						   $num = mysql_num_rows($res);
						    
						   ver_comprobante_detallado($num,$res);
					      }
				         
				     
				     /*
				     //===========================================================================
					 //Con este query traigo el comprobante resumido por concepto de facturacion
					 //para las cuentas de **** IVA ****.
				     $q=  " SELECT fdefue, fdedoc, relconciv, '".$wnat2."', sum(fdevco) "
					     ."   FROM ".$wbasedato."_000065, ".$wbasedato."_000077, ".$wbasedato."_000004 "
					     ."  WHERE fdefue    = '".$rowdoc[0]."'"
					     ."    AND fdedoc    = '".$rowdoc[1]."'"
					     ."    AND fdecon    = relconcon "
					     ."    AND fdecco    = relconcco " 
					     ."    AND relcontem = '".$rowdoc[2]."'"
					     ."    AND fdeest    = 'on' " 
					     //."    AND relconest = 'on' "
					     ."    AND relconcon = grucod "
					     ."    AND gruabo    != 'on' "
					     ."  GROUP BY 1 ";
		             $res = mysql_query($q,$conex);
				     $num = mysql_num_rows($res);
				    
				     ver_comprobante_detallado($num,$res);
				     */
				     
				     if ($numfue > 0 and $rowfue[0] != "on" and $rowfue[1] != "on")    //COMPROBANTE DE FACTURACION
			            {
				         
					     echo "<tr>";
					     echo "<td colspan=7   bgcolor=CCCC33>Total Documento </td>";	
					     //On *** con esto puede sacar el total por doucmento para nalizarlo en excel echo "<td colspan=7 bgcolor=CCCC33>".$rowdoc[1]." | ".$wtotdocD."</td>";	
					     if ($wtotdocD!=$wtotdocC)
					        $wcolor="CC3300";
					       else
					          $wcolor="CCCC33"; 
					     echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotdocD,0,'.',',')."</td>";	 						     
					     echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotdocC,0,'.',',')."</td>";
					     echo "</tr>";
					     
	                    }
		               else
						  {  
						   //===========================================================================
						   //Con este query traigo el comprobante resumido por concepto de cartera
						   //para las cuentas de ** CONCEPTOS DE CARTERA **.
						   $q=  " SELECT rdefue, rdenum, concue, connat, rdecco, '', sum(rdevco), '', '', '', '' "
						       ."   FROM ".$wbasedato."_000021, ".$wbasedato."_000044 "
						       ."  WHERE rdefue = '".$rowdoc[0]."'"
						       ."    AND rdenum = '".$rowdoc[1]."'"
						       ."    AND mid(rdecon,1,instr(rdecon,'-')-1) = concod "
						       ."    AND rdefue = confue "
						       ."    AND rdeest = 'on' " 
						       ."    AND conest = 'on' "
						       ."  GROUP BY 1,2,3,4,5 ";
				           $res = mysql_query($q,$conex);
						   $num = mysql_num_rows($res);
						    
						   ver_comprobante_detallado($num,$res);	  
						
						   if ($wtotdocD!=$wtotdocC)
					        $wcolor="CC3300";
					       else
					          $wcolor="CCCC33"; 
						   	  						   
				           echo "<tr>";
					       echo "<td colspan=7   bgcolor=CCCC33>Total Documento de Notas</td>";	
					       echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotdocD,0,'.',',')."</td>";	 						     
					       echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotdocC,0,'.',',')."</td>";
					       echo "</tr>";
					       
			              }
			       } 
	            echo "<tr>";
			    echo "<td colspan=7   bgcolor=CCCC33>Total Comprobante ".$wfuecar1."</td>";	
			    if ($wtotcomD!=$wtotcomC)
			       $wcolor="CC3300";
			      else
			         $wcolor="CCCC33"; 
			    echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotcomD,0,'.',',')."</td>";	 						     
			    echo "<td align=right bgcolor=".$wcolor.">".number_format($wtotcomC,0,'.',',')."</td>";
			    echo "</tr>";     
		    }     
     
  echo "<tr><td align=center bgcolor=#cccccc colspan=13><input type='submit' value='OK'></td></tr>"; 
  echo "</table>";
 
}
?>
