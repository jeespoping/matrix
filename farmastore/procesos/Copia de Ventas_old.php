<head>
  <title>VENTAS AL PUBLICO - FARSTORE</title>
</head>
<body onload=ira()>
<script type="text/javascript">
	function enter()
	{
	   document.forms.ventas.submit();
	}
</script>

<?php
  /***************************************************
   *     PROGRAMA PARA LA GRABACION DE LAS VENTAS    *
   *                  DE FARMASTORE                  *
   ***************************************************/
   
//==================================================================================================================================
//PROGRAMA                   : farstore.php
//AUTOR                      : Juan Carlos Hern�ndez M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Abril 28 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Versi�n Abril 28 de 2005)"; 
//DESCRIPCION
//==================================================================================================================================
//Este programa se hace con el objetivo de registrar las ventas de la empresa FARMASTORE, en donde se pueda luego realizar una
//facturaci�n individual o por empresa y adem�s de tener en cuenta que luego de poder facturar se generen los RIPS, adem�s este
//programa tiene en cuenta la actualizaci�n del Inventario en l�nea, grabando tambi�n el movimiento de consumo en el inventario,
//El programa en general, tiene en cuenta el tipo de cliente, el responsable de la cuenta, las tarifas de los articulos seg�n la
//empresa y el centro de costo (sucursal). tambien se tiene en cuenta que si la venta es para un particular o el paciente de 
//empresa tiene que pagar salga una ventana en donde se le pide registrar un recibo de caja por el valor pagado.
//==================================================================================================================================
session_start();

if (!isset($user))
	{
	 if(!session_is_registered("user"))
		session_register("user");
	}

if(!session_is_registered("user"))
	echo "error";
else
{	            
  include("conex.php");
			      or die("No se ralizo Conexion");
  mysql_select_db("matrix");
 
  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");
  					    

  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user)); 
  
  	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versi�n Abril 28 de 2005)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	                                                           
  $wfecha=date("Y-m-d");   
  $hora = (string)date("H:i:s");	              
    
  echo "<form name='ventas' action='Ventas.php' method=post>";
  
  echo "<input type='HIDDEN' name= 'wini' value='".$wini."'>";
  
  if ($wini == "S")  //'S' Indica que se esta iniciando una venta
     {
      $wfecha_tempo=$wfecha;
      $whora_tempo=$hora;
      
      include("/farmastore/cierre.php");    //Se hace el cierre en la primera venta del mes siguiente
      
      //Esto lo hago para indicar que la venta anterior ya termino, entonces inicializo las siguientes variables
      if (isset($wterm_vta) and ($wterm_vta=="S"))
         {
	      //$wfecha_bor=date("Y-m-d");   
	         
	      //BORRO LOS REGISTROS DE LA TABLA DE VENTAS TEMPORALES
	      //$q = "  DELETE FROM farstore_000034 "
	      //     ."  WHERE temusu = '".$wusuario."'"
	      //     ."    AND temfec = '".$wfecha_bor."'"
	      //     ."    AND temsuc = '".$wcco."'"
	      //     ."    AND temcaj = '".$wcaja."'";
	      //$res = mysql_query($q,$conex);    
	         
	      
	      unset($wnrovta);
		  unset($wtipcli);
		  unset($wempresa);
		  unset($wdocpac);
		  unset($wnompac);
		  unset($wtelpac);
		  unset($wdirpac);
		  unset($wmaipac);
		  unset($wcuotamod);
		  unset($wtipven);
		  unset($wmensajero);
		  unset($wdesemp);
		  unset($wdesart);
		  unset($wrecemp);
		  unset($wtotdes);
		  unset($wtotrec);
		 } 
	 }
    else
      {
       echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";   
	   echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>"; 
      } 
  
  //ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin "
       ."   FROM farstore_000030 "
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
      
      $wtiping = $row[2];
     }
    else
       echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";
     
  $wcol=10;  //Numero de columnas que se tienen o se muestran en pantalla   
  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LAS OPCIONES DE RECIBOS DE DINERO - FORMAS DE PAGO
  function formasdepago($fk,$fconex,$fwcf,$fwcol,$fwclfg,$wfpa,$wdocane,$wobsrec,$wvalfpa,$wtotventot)
      {
	    global $fk;
	    
	    echo $fk;
	    
	    for ($j=1;$j<=$fk;$j++)
	        {  
		      echo "<tr>";
				       
		      $q =  " SELECT fpacod, fpades "
			       ."   FROM farstore_000023 "
			       ."  ORDER BY fpacod ";     
				
			  $res = mysql_query($q,$fconex); // or die (mysql_errno()." - ".mysql_error());;
			  $num = mysql_num_rows($res);    // or die (mysql_errno()." - ".mysql_error());;
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //FORMA DE PAGO
			  echo "<td align=left bgcolor=".$fwcf." colspan=".($fwcol-8)."><b><font text color=".$fwclfg.">Forma de pago: </font></b><select name='wfpa[".$j."]'>";
			  if (isset($wfpa[$j]))
			     echo "<option selected>".$wfpa[$j]."</option>";    
			  for ($i=1;$i<=$num;$i++)
			     {
			      $row = mysql_fetch_array($res); 
			      echo "<option>".$row[0]." - ".$row[1]."</option>";
			     }
			  echo "</select></td>";
				
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //DOCUMENTO ANEXO
			  if (isset($wdocane[$j])) //Si ya fue digitado el documento anexo
			     echo "<td bgcolor=".$fwcf." colspan=".($fwcol-8)."><b><font text color=".$fwclfg.">Dcto Anexo: </font></b><INPUT TYPE='text' NAME='wdocane[".$j."]' VALUE='".$wdocane[$j]."'></td>";  //wdocane
			    else 
			       echo "<td bgcolor=".$fwcf." colspan=".($fwcol-8)."><b><font text color=".$fwclfg.">Dcto Anexo: </font></b><INPUT TYPE='text' NAME='wdocane[".$j."]' ></td>";                        //wdocane
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //OBSERVACIONES
			  if (isset($wobsrec[$j])) //Si ya fue digitado la observacion
			     echo "<td bgcolor=".$fwcf." colspan=".($fwcol-7)."><b><font text color=".$fwclfg.">Observ.: </font></b><INPUT TYPE='text' NAME='wobsrec[".$j."]' VALUE='".$wobsrec[$j]."'></td>";     //wobsrec
			    else 
			       echo "<td bgcolor=".$fwcf." colspan=".($fwcol-7)."><b><font text color=".$fwclfg.">Observ.: </font></b><INPUT TYPE='text' NAME='wobsrec[".$j."]' ></td>";                           //wobsrec     
			    
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //Con la siguiente instrucci�n en Javascript se ubica el cursor en el ultimo campo del valor de la forma de pago osea en: $wvalfpa[$j] : en el VALOR         
			  //$wvalfpa ==> Valor forma de pago
			  ?>	    
			    <script>
			      //function ira(){document.ventas.elements.length;}
			      //function ira(){document.ventas.elements[document.ventas.elements.length-1].focus();}
			      function ira(){document.ventas.elements[document.ventas.elements.length-4].focus();}
			    </script>
			  <?php
				
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //VALOR
			  if (isset($wvalfpa[$j]) & $wvalfpa > 0 ) //Si ya fue digitado el valor y es mayor a cero
			     {
				  $wpagado=0;   
			      for ($y=1;$y<=$j;$y++)
			          $wpagado=$wpagado+$wvalfpa[$y];
			      
			      $wvalfpa[$j]=str_replace(",","",$wvalfpa[$j]); //Esto se hace para quitarle el formato que trae el n�mero
			      echo "<td bgcolor=".$fwcf." colspan=".($fwcol-9)."><b><font text color=".$fwclfg.">Valor: </font></b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' VALUE='".number_format($wvalfpa[$j],2,'.',',')."' SIZE=15></td>";       //wvalfpa
			      if (($wtotventot-$wpagado) > 0 )
			         echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Saldo: </font></b>".number_format(($wtotventot-$wpagado),2,'.',',')."</td>";            //wtotventot-wtotfpa
			        else 
			           echo "<td bgcolor=".$fwcf." colspan=1><b><font text color=".$fwclfg.">Saldo: </font></b>".number_format((0),2,'.',',')."</td>";                             //wtotventot-wtotfpa
			     } 
			    else
			       echo "<td bgcolor=".$fwcf." colspan=".($fwcol-7)."><b><font text color=".$fwclfg.">Valor: </font></b><INPUT TYPE='text' NAME='wvalfpa[".$j."]' SIZE=15></td>";  //wvalfpa     
			       
			  echo "</tr>"; 
			}
	  }
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
  	  
	  
  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////   
  //FUNCION PARA MOSTRAR LOS ARTICULOS SELECCIONADOS PARA LA VENTA  
  function mostrar($fwusuario,$fwfecha_tempo,$fwhora_tempo,$fwcco,$fwcaja,$fconex,$fwini,$fwdocpac,$fwnompac,$fwtelpac,$fwdirpac,$fwmaipac,$fwcol,$fwtipcli,$fwcuotamod,$fwempresa,$fwventa,$fwtipven,$fwmensajero,$fwdesemp,$fwrecemp,$fwdesart)   
       {
	     global $wtotventot;  
	     global $wtotveniva;  
	     global $wcf; 
	     global $wcf2;
	     global $wclfa;
         global $wclfg;
         global $wtotdes;
         global $wtotrec;
         
         ///////////////////////////////////////////////////////////////////////   
	     //ACA TRAIGO TODO LO QUE HAY PENDIENTE DE FACTURAR EN ESTE CAJA   
		 $q = " SELECT temart, temdes, tempre, temcan, temvun, tempiv, temiva, temtot, id, temdem, temrem, temdar "
	         ."   FROM farstore_000034 "
	         ."  WHERE temusu = '".$fwusuario."'"
	         ."    AND temfec = '".$fwfecha_tempo."'"
	         ."    AND temhor = '".$fwhora_tempo."'"
	         ."    AND temsuc = '".$fwcco."'"
	         ."    AND temcaj = '".$fwcaja."'"
	         ."  ORDER BY id ";
	     $res = mysql_query($q,$fconex);
	     $num = mysql_num_rows($res);
	    
	     //$res = mysql_query($q,$fconex) or die (mysql_errno()." - ".mysql_error());
	     //$num = mysql_num_rows($res) or die (mysql_errno()." - ".mysql_error());

	     if ($num > 0)
	        {
		     $wtotveniva=0;
	         $wtotventot=0;
	         $wtotdes=0;
	         $wtotrec=0;
	         $wtotbase_dev_iva=0;
	         
	         echo "<tr><td colspan=".$fwcol.">&nbsp</td></tr>";
	  
	         echo "<tr><td align=center colspan=".$fwcol." bgcolor=".$wcf2."><font size=5 text color=".$wclfa."><b>DETALLE DE VENTA</b></font></td></tr>";
		     echo "<tr>";
		     echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Articulo</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Descripci�n</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Presentaci�n</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Cantidad</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Valor Unit.</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">% Iva</th></font>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Valor Iva.</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">Total</font></th>";
			 echo "<th bgcolor=".$wcf2."><font text color=".$wclfa.">&nbsp</font></th>";
			 echo "</tr>"; 
			 
			 for ($i=1;$i<=$num;$i++)
	            {   
	             $row = mysql_fetch_array($res);   
		       
		         echo "<tr>";
			     echo "<td align=center>".$row[0]."</td>";                         //articulo
			     echo "<td align=LEFT>".$row[1]."</td>";                           //Descripcion
			     echo "<td align=center>".$row[2]."</td>";                         //Unidad
	             echo "<td align=center>".$row[3]."</td>";                         //Cantidad
			     echo "<td align=RIGHT>".number_format($row[4],0,'.',',')."</td>"; //Valor unitario
			     echo "<td align=RIGHT>".number_format($row[5],0,'.',',')."</td>"; //Porcentaje de iva
			     echo "<td align=RIGHT>".number_format($row[6],0,'.',',')."</td>"; //Valor iva
			     echo "<td align=RIGHT>".number_format($row[7],0,'.',',')."</td>"; //Total articulo
			     
			     if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de eliminar mientras no se haya grabado la venta definitiva
			        echo "<td align=center><font size=3><A href='Ventas.php?wid=".$row[8]."&amp;wborrar=S"."&amp;wini=".$fwini."&amp;wfecha_tempo=".$fwfecha_tempo."&amp;whora_tempo=".$fwhora_tempo."&amp;wdocpac=".$fwdocpac."&amp;wnompac=".$fwnompac."&amp;wtelpac=".$fwtelpac."&amp;wdirpac=".$fwdirpac."&amp;wmaipac=".$fwmaipac."&amp;wtipcli=".$fwtipcli."&amp;wcuotamod=".$fwcuotamod."&amp;wempresa=".$fwempresa."&amp;wtipven=".$fwtipven."&amp;wmensajero=".$fwmensajero."&amp;wdesemp=".$fwdesemp."&amp;wrecemp=".$fwrecemp."'> Eliminar</A></font></td>";
			     echo "<tr>";
			    
			     
			     $wtotveniva=$wtotveniva+$row[6]; //Sumo el iva
			                                      //Sumo el valor con dscto    Sumo valor unit con recargo
			     $wtotventot=$wtotventot        + $row[7] - ($row[7]*$row[9])+($row[7]*$row[10]) - ($row[7]*$row[11]);   //Total - dscto empresa + recargo - descto articulo
			     
			     $wtotdes=$wtotdes+($row[7]*$row[9]);        //Le sumo al Total descuento el descuento empresa
			     $wtotdes=$wtotdes+($row[7]*$row[11]);       //Le sumo al Total descuento el descuento articulo
			     $wtotrec=$wtotrec+($row[7]*$row[10]);       //Le sumo al Total recargo el recargo empresa
			     
			     if ($row[5] > 0)
			        //$wtotbase_dev_iva=(integer)($wtotbase_dev_iva+($row[4]/(1+($row[5]/100))));   
			        $wtotbase_dev_iva=(integer)($wtotbase_dev_iva+($row[4]-$row[6]));   
			    }
	          echo "<tr>";
	          echo "<td align=RIGHT bgcolor=".$wcf2." colspan=".($fwcol-6)."><font text color=".$wclfa."><b>TOTALES &nbsp &nbsp</b></font></td>"; 
	          echo "<td align=CENTER bgcolor=".$wcf2."><font text color=".$wclfa.">Descto: <br>".number_format($wtotdes,0,'.',',')."</font></td>";
	          echo "<td align=CENTER bgcolor=".$wcf2."><font text color=".$wclfa.">Recargo: <br>".number_format($wtotrec,0,'.',',')."</font></td>"; 
	          echo "<td align=RIGHT bgcolor=".$wcf2."><font text color=".$wclfa.">".number_format($wtotveniva,0,'.',',')."</font></td>";
	          echo "<td align=RIGHT bgcolor=".$wcf2."><font text color=".$wclfa.">".number_format($wtotventot,0,'.',',')."</font></td>";
	          echo "<td align=CENTER bgcolor=".$wcf2."><font text color=".$wclfa.">Base Dev. Iva: <br>".number_format($wtotbase_dev_iva,0,'.',',')."</font></td>";
	          echo "</tr>";
	          
	          if (!isset($fwventa) or $fwventa == "N" )  //Solo da la opcion de Grabar Venta mientras no se haya grabado la venta definitiva
	             echo "<TR><td align=center bgcolor=#cccccc colspan=".$fwcol."><font size=3><A href='Ventas.php?wventa=S"."&amp;wini=".$fwini."&amp;wtipcli=".$fwtipcli."&amp;wfecha_tempo=".$fwfecha_tempo."&amp;whora_tempo=".$fwhora_tempo."&amp;wcuotamod=".$fwcuotamod."&amp;wempresa=".$fwempresa."&amp;wdocpac=".$fwdocpac."&amp;wnompac=".$fwnompac."&amp;wtelpac=".$fwtelpac."&amp;wdirpac=".$fwdirpac."&amp;wmaipac=".$fwmaipac."&amp;wtipven=".$fwtipven."&amp;wmensajero=".$fwmensajero."&amp;wdesemp=".$fwdesemp."&amp;wrecemp=".$fwrecemp."'> Grabar Venta</A></font></TD></TR>";
	        }   
		  ///////////////////////////////////////////////////////////////////////  
	   }    
       
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA   
  //===========================================================================================================================================
  
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
       
  echo "<p align=right><font size=1><b>Autor: ".$wautor."</b></font></p>";
  //=======================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA
  echo "<center><table border>";
  echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/farmastore/logo farmastore.png' WIDTH=340 HEIGHT=100></td></tr>";
  echo "<tr><td align=center colspan=".$wcol." bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>VENTAS AL PUBLICO</b></font></td></tr>";
  echo "<tr>";
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //NUMERO DE VENTA
  if (isset($wnrovta))
     echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Venta Nro: <br></font></b><INPUT TYPE='text' NAME='wnrovta' SIZE=8 VALUE=".$wnrovta."></td>";
    else 
       echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Venta Nro: <br></font></b><INPUT TYPE='text' NAME='wnrovta' SIZE=8></td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA DE LA VENTA
  //echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Fecha: </font></b><INPUT TYPE='text' NAME='wfecvta' VALUE=".$wfecha."></td>";
  echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg.">Fecha: </font></b>".$wfecha."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SUCURSAL
  echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg.">Sucursal: </font></b>".$wnomcco."</td>";
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CAJA
  echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg.">Caja: </font></b>".$wnomcaj."</td>";
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE EMPRESA
  echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg.">Tipo de Cliente: </font></b><select name='wtipcli' onchange='enter()'>";
  
  if (isset($wtipcli))
     {
      $q =  " SELECT temcod, temdes "
           ."   FROM farstore_000029 "
           ."  WHERE temcod not in (mid('".$wtipcli."',1,instr('".$wtipcli."','-')-1)) " 
	       ."  ORDER BY temcod ";
	 }  
    else
       { 
        $q =  " SELECT temcod, temdes "
             ."   FROM farstore_000029 "
	         ."  ORDER BY temcod ";
	   }      
  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
  if (isset($wtipcli))
     echo "<option selected>".$wtipcli."</option>";    
  for ($i=1;$i<=$num;$i++)
     {
      $row = mysql_fetch_array($res); 
      echo "<option>".$row[0]."-".$row[1]."</option>";
     }
  echo "</select></td>";
  
  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////   function ira(){document.ventas.wvalfpa[0].focus();}
  //TIPO DE VENTA
  echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg.">Tipo de Venta: </font></b><select name='wtipven' onchange='enter()'>";
  
  echo $wtipven;  //////////////////
  
  if (isset($wtipven))
     if ($wtipven == "Directa")
        {
         echo "<option selected>".$wtipven."</option>";  
         echo "<option>Domicilio</option>";
        }  
       else
          {
           echo "<option selected>".$wtipven."</option>";  
           echo "<option>Directa</option>";
          }  
    else  
       {
        echo "<option>Directa</option>";
        echo "<option>Domicilio</option>";
       } 
  echo "</select></td></tr>";
  
  if (isset($wtipven) and ($wtipven <> "Directa"))
     {
      ////////////////////////////////////////////////////////////////////////////////////////////////////////////
	  //MENSAJERO
	  if (isset($wmensajero))
	     {
		  $q =  " SELECT msjcod, msjnom "
		       ."   FROM farstore_000035 "
		       ."  WHERE msjcod <> '".$wmensajero."'"
		       ."    AND msjest = 'on'"
		       ."  ORDER BY msjcod ";
		 }
	    else
	       {
		    $q =  " SELECT msjcod, msjnom "
		         ."   FROM farstore_000035 "
		         ."  WHERE msjest = 'on'"
		         ."  ORDER BY msjcod ";
	       }
	   
	  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
	  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
	  echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg.">Mensajero: <br></font></b><select name='wmensajero'>";
	  
	  if (isset($wmensajero))
	     {
		  $q= "   SELECT count(*) FROM farstore_000035 "
	         ."    WHERE msjcod = (mid('".$wmensajero."',1,instr('".$wmensajero."','-')-1)) "  
	         ."      AND msjest = 'on'";
	         
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      $row1 = mysql_fetch_array($res1);
	      if ($row1[0] > 0)
		     echo "<option selected>".$wmensajero."</option>";    
	     } 
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
	     }
	  echo "</select></td>";
     }
    else
       $wmensajero=""; 
	  
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RESPONSABLES
  if (isset($wtipcli))
     {
	  $q =  " SELECT empcod, empnit, empnom "
	       ."   FROM farstore_000024 "
	       ."  WHERE emptem = '".$wtipcli."'"
	       ."  ORDER BY empcod ";
     }
    else
       {
	    $q =  " SELECT empcod, empnit, empnom "
	         ."   FROM farstore_000024 "
	         ."  ORDER BY empcod ";
       }
        
  $res = mysql_query($q,$conex); // or die (mysql_errno()." - ".mysql_error());;
  $num = mysql_num_rows($res);   // or die (mysql_errno()." - ".mysql_error());;
  echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wempresa'>";
  if (isset($wempresa))
     {
	  //Este query lo hago para saber si la empresa que esta en pantalla corresponde al tipo de cliente o empresa seleccionado en el campo anterior
	  //Si si corresponde la muestro, si no, solo muestra las seleccionadas en el query anterior   
      $q= "   SELECT count(*) FROM farstore_000024 "
         ."    WHERE empcod = (mid('".$wempresa."',1,instr('".$wempresa."','-')-1)) "  
         ."      AND emptem = '".$wtipcli."'";
      $res1 = mysql_query($q,$conex);
      $num1 = mysql_num_rows($res1);   
      $row1 = mysql_fetch_array($res1);
      if ($row1[0] > 0)
	     echo "<option selected>".$wempresa."</option>";    
     } 
  for ($i=1;$i<=$num;$i++)
     {
      $row = mysql_fetch_array($res); 
      echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
     }
  echo "</select></td>";
  
  //////////////////////////////////////////////////////////////////
  //DOCUMENTO DEL CLIENTE
  if (isset($wdocpac)) //Si ya fue digitado el documento del cliente
     {
      if ($wdocpac != "9999")
         {
	      $q= "SELECT clidoc, clinom, clitel, clidir, climai "
	         ."  FROM farstore_000041 "
	         ." WHERE clidoc = '".$wdocpac."'";
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      if ($num1 > 0)
	         {
		      $row1 = mysql_fetch_array($res1);
	          if (isset($wnompac) and $wnompac == "CLIENTE PARTICULAR") $wnompac=$row1[1];  //Si el Nombre esta setiado y es diferente al almacenado
	          if (isset($wtelpac) and $wtelpac == "SIN DATO") $wtelpac=$row1[2];            //Si el Telefono esta setiado y es diferente al almacenado
	          if (isset($wdirpac) and $wdirpac == "SIN DATO") $wdirpac=$row1[3];            //Si la Direccion esta setiada y es diferente a la almacenada
	          if (isset($wmaipac) and $wmaipac == "SIN DATO") $wmaipac=$row1[4];            //Si la Direccion esta setiada y es diferente a la almacenada
	          ///$wini="N";
	         }
	      echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg."> Documento: </font></b><INPUT TYPE='text' NAME='wdocpac' VALUE='".$wdocpac."'></td>";   //wdocpac     
         }
        else 
           echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg."> Documento: </font></b><INPUT TYPE='text' NAME='wdocpac' VALUE='".$wdocpac."'></td>";   //wdocpac     
     } 
    else 
        echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg."> Documento: </font></b><INPUT TYPE='text' NAME='wdocpac' VALUE='9999'></td>";              //wdocpac
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //NOMBRE DEL CLIENTE     
  if (isset($wnompac)) //Si ya fue digitado el nombre del cliente    
     echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-3)."><b><font text color=".$wclfg."> Nombre: </font></b><INPUT TYPE='text' NAME='wnompac' SIZE=60 VALUE='".$wnompac."'></td>";          //wnompac
    else 
       echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-3)."><b><font text color=".$wclfg."> Nombre: </font></b><INPUT TYPE='text' NAME='wnompac' SIZE=60 VALUE='CLIENTE PARTICULAR'></td>";  //wnompac
  echo "</tr>";
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //TELEFONO DEL CLIENTE
  echo "<tr>";
  if (isset($wtelpac)) //Si ya fue digitado el telefono del cliente
     {
      if ($wtelpac != "SIN DATO")
         {
	      $q= "SELECT clidoc, clinom, clitel, clidir, climai "
	         ."  FROM farstore_000041 "
	         ." WHERE clitel = '".$wtelpac."'";
	      $res1 = mysql_query($q,$conex);
	      $num1 = mysql_num_rows($res1);   
	      if ($num1 > 0)
	         {
	          $row1 = mysql_fetch_array($res1);
	          if (isset($wnompac) and $wnompac == "CLIENTE PARTICULAR") $wnompac=$row1[1];  //Si el Nombre esta setiado y es diferente al almacenado
	          if (isset($wdocpac) and $wdocpac == "9999") $wdocpac=$row1[0];                //Si el Documento esta setiado y es diferente al almacenado
	          if (isset($wdirpac) and $wdirpac == "SIN DATO") $wdirpac=$row1[3];            //Si la Direccion esta setiada y es diferente a la almacenada
	          if (isset($wmaipac) and $wmaipac == "SIN DATO") $wmaipac=$row1[4];            //Si la Direccion esta setiada y es diferente a la almacenada
	          
	          if ($wini=="S")
	             {
		          $wini="N";   
	              echo "<meta http-equiv='refresh' content='0;url=ventas.php?wfecha_tempo=".$wfecha_tempo."&amp;whora_tempo=".$whora_tempo."&amp;wnompac=".$wnompac."&amp;wdocpac=".$wdocpac."&amp;wtelpac=".$wtelpac."&amp;wdirpac=".$wdirpac."&amp;wmaipac=".$wmaipac."&amp;wini=".$wini."&amp;wtipcli=".$wtipcli."&amp;wcuotamod=".$wcuotamod."&amp;wempresa=".$wempresa."&amp;wtipven=".$wtipven."&amp;wmensajero=".$wmensajero.">";
	             }
	         }
	      echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Telefono1 : <br></font></b><INPUT TYPE='text' NAME='wtelpac' SIZE=9 VALUE='".$wtelpac."'></td>";            //wtelpac  
         }
        else 
           echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Telefono2 : <br></font></b><INPUT TYPE='text' NAME='wtelpac' SIZE=9 VALUE='".$wtelpac."'></td>";            //wtelpac
     }
    else 
       echo "<td align=left bgcolor=".$wcf."><b><font text color=".$wclfg."> Telefono3 : <br></font></b><INPUT TYPE='text' NAME='wtelpac' SIZE=9 VALUE='SIN DATO'></td>";                    //wdirpac
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DIRECCION DEL CLIENTE     
  if (isset($wdirpac)) //Si ya fue digitado el nombre del cliente    
     echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg."> Direcci�n: </font></b><INPUT TYPE='text' NAME='wdirpac' VALUE='".$wdirpac."'></td>";       //wdirpac
    else 
       echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-8)."><b><font text color=".$wclfg."> Direcci�n: </font></b><INPUT TYPE='text' NAME='wdirpac' VALUE='SIN DATO'></td>";         //wdirpac
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //E-MAIL DEL CLIENTE     
  if (isset($wmaipac)) //Si ya fue digitado el nombre del cliente    
     echo "<td bgcolor=".$wcf." colspan=".($wcol-6)."><b><font text color=".$wclfg."> E-Mail: </font></b><INPUT TYPE='text' NAME='wmaipac' SIZE=40 VALUE='".$wmaipac."'></td>";  //wmaipac
    else 
       echo "<td bgcolor=".$wcf." colspan=".($wcol-6)."><b><font text color=".$wclfg."> E-Mail: </font></b><INPUT TYPE='text' NAME='wmaipac' SIZE=40 VALUE='SIN DATO'></td>";    //wmaipac     
  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CUOTA MODERADORA     
  if (isset($wcuotamod)) //Si ya fue digitado el nombre del cliente    
     echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-5)."><b><font text color=".$wclfg."> Franquicia o Cuota Moderadora: </font></b><INPUT TYPE='text' NAME='wcuotamod' VALUE='".$wcuotamod."'></td>";          //wnompac
    else 
       echo "<td align=left bgcolor=".$wcf." colspan=".($wcol-5)."><b><font text color=".$wclfg."> Franquicia o Cuota Moderadora: </font></b><INPUT TYPE='text' NAME='wcuotamod' VALUE='0'></td>";            //wnompac          
  echo "</tr>";
  
  if (!isset($wventa) or ($wventa=="N"))
     echo "<tr><td colspan=".$wcol.">&nbsp</td></tr>";  //Solo muestra esta linea antes de realizar la venta efectiva
	  
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //ACA EVALUO CUANDO SE HACE LA VENTA
  if (!isset($wventa) or ($wventa=="N"))
     {
	  echo "<tr><td align=center colspan=".$wcol." bgcolor=".$wcf2."><font size=3 text color=#ffffff><b>BUSQUEDA DE ARTICULOS</b></font></td></tr>";
	  echo "<tr>";
	  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //BUSQUEDA POR CODIGO O DESCRIPCION 
	  if ($wtiping=="C")   //Evaluo si el ingreso de articulos o busqueda se hace por codigo o descripcion
	     {
	      echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Codigo      </font></b><input type='radio' name='wcons' VALUE=codart checked SIZE=2 ></td>";                //wcons
	      echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Descripci�n </font></b><input type='radio' name='wcons' VALUE=desart SIZE=2 ></td>";                        //wcons 
	     }
	    else
	       {
	        echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Codigo      </font></b><input type='radio' name='wcons' VALUE=codart SIZE=2 ></td>";                             //wcons
	        echo "<td bgcolor=".$wcf2."><b><font text color=".$wclfa."> Descripci�n </font></b><input type='radio' name='wcons' VALUE=desart checked SIZE=2 ></td>";                     //wcons 
	       }  
	  //Siempre que utilice esta opcion de javascript, se debe cargar la funcion ira() arriba en el BODY
	  ?>	    
	    <script>
	      function ira(){document.ventas.wdato.focus();}
	    </script>
	  <?php
	  echo "<td bgcolor=#fffffff> <INPUT TYPE='text' NAME='wdato'></td>";                                                                  //wdato

	  if (!isset($wdato) or ($wdato == ""))
	     echo "<td align=center bgcolor=#cccccc colspan=1><input type='submit' value='Consultar'></td>";                                   //submit 
		
	  if (isset($wempresa))
	     {	 
	      $pos = strpos($wempresa,"-");
	      $wemp = substr($wempresa,0,$pos-1);   
	      
	      $pos1 = strpos($wempresa,"-",$pos+1);
	      $wnitemp = substr($wempresa,$pos+1,$pos1-1);    
	     }
	     
	  //ACA ELIMINO EL REGISTRO SELECCIONADO
	  if (isset($wborrar) and ($wborrar == 'S'))
	     {
	      $q="  DELETE FROM farstore_000034 "
	        ."   WHERE id = ".$wid;
	      $res = mysql_query($q,$conex);
	      $wborrar='N';
	     }   //fin del if $wborrar  
	     
	  //////////////////////////////////////////////////////////////////////////////////////////////   
	  //ACA TRAIGO LOS ARTICULOS QUE TENGAN TARIFA EN EL CONCEPTO DE VENTAS
	  if (isset($wcons) and !isset($wcan) and $wdato != "")
	     {
		  if ($wcons == "codart")
		     {
			  //==============================================================================================================   
			  //VERIFICO QUE EL CODIGO DIGITADO SEA EXTERNO O NO  ============================================================  
	          $q= "  SELECT axpart "
	             ."    FROM farstore_000009 "
	             ."   WHERE axpcpr = '".$wdato."'"
	             ."     AND axpest = 'on' ";
	             
	          $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		      $num = mysql_num_rows($res);
		      if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
		         {
			      $row = mysql_fetch_array($res);   
			      
			      $pos = strpos($row[0],"-");
	              $wdato = substr($row[0],0,$pos); 
			     }   
			  //==============================================================================================================   
			     
			  $q =  " SELECT artcod, artnom, mtavac, mtavan,  karexi, mtafec, artiva "
			       ."   FROM farstore_000001, farstore_000026, farstore_000024, farstore_000007 "
			       ."  WHERE artcod                            = '".$wdato."'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".$wemp."'"
			       ."    AND karcco                            = '".$wcco."'"
			       ."    AND karcod                            = artcod "
			       ."    AND mtafec                            <= '".$wfecha."'"  //Trae el valor actual
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
			       ."  UNION "
			       ." SELECT artcod, artnom, mtavac, mtavan, karexi, mtafec, artiva "
			       ."   FROM farstore_000001, farstore_000026, farstore_000024, farstore_000007 "
			       ."  WHERE artcod                            = '".$wdato."'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".$wemp."'"
			       ."    AND karcco                            = '".$wcco."'"
			       ."    AND karcod                            = artcod "
			       ."    AND mtafec                            > '".$wfecha."'"   //Trae el valor anterior
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
			       ."  ORDER BY artcod ";
			 }
	      if ($wcons == "desart")
		     {
			  $q =  " SELECT artcod, artnom, mtavac, mtavan, karexi, mtafec, artiva "
			       ."   FROM farstore_000001, farstore_000026, farstore_000024, farstore_000007 "
			       ."  WHERE artnom                            like '%".$wdato."%'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".$wemp."'"
			       ."    AND karcco                            = '".$wcco."'"
			       ."    AND karcod                            = artcod "
			       ."    AND mtafec                            <= '".$wfecha."'"
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
				   ."  UNION "
				   ." SELECT artcod, artnom, mtavac, mtavan, karexi, mtafec, artiva "
			       ."   FROM farstore_000001, farstore_000026, farstore_000024, farstore_000007 "
			       ."  WHERE artnom                            like '%".$wdato."%'"
			       ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
			       ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
			       ."    AND empcod                            = '".$wemp."'"
			       ."    AND karcco                            = '".$wcco."'"
			       ."    AND karcod                            = artcod "
			       ."    AND mtafec                            > '".$wfecha."'"
			       ."    AND artest                            = 'on' "
			       ."    AND mtaest                            = 'on' "
				   ."  ORDER BY artnom ";
			 }   
		  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		  $num = mysql_num_rows($res);
	     
		  //echo "<td>".$num."</td>"; 
		  
		  if ($num > 0) //El articulo existe y tiene tarifa, entra por el then
		     {
			  echo "<td align=center colspan=".($wcol-5)."><select name='warticulo'>";                                                //warticulo
			  for ($i=1;$i<=$num;$i++)
			     {
				  $row = mysql_fetch_array($res); 
				  
				  //=========================================================================================
				  //Esto lo hago para colocar todas las descripciones del mismo tama�o, osea de 60 caracteres
			      $j= 60-strlen($row[1]);
			      for ($k=1;$k<=$j;$k++)
			          $row[1]=$row[1].'&nbsp';
			          
			      //EL 1 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO         
			      $wporiva = 1+((integer)$row[6]/100);
			      if ($wfecha < $row[5])   //Aca evaluo si tomo el valor anterior o el actual
			           $wval = $row[3]; //*$wporiva;    //Valor anterior
			          else
			             $wval = $row[2]; //*$wporiva;  //Valor actual 
			      //=========================================================================================
			      echo "<option>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,0,'.',',')." | ".$row[4]."</option>";
			     }
			  echo "</select></td>";
		      ?>	    
		        <script>
		          function ira(){document.ventas.wcan.focus();}
		          function ira(){document.ventas.wcan.select();}  //Deja seleccionado el valor por defecto
		        </script>
		      <?php
		      
		      echo "<td bgcolor=".$wcf.">Cantidad <INPUT TYPE='text' NAME='wcan' VALUE=1 onkeypress='if ((event.keyCode < 48 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></td>";    //wcan
			  echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                                         //submit 
		      echo "</tr>";
			  
		      $wventa="N";
		      $wdesemp=0;
		      $wrecemp=0;
		      $wdesart=0;
		      mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
		     }
	        else  //Si el articulo no existe o no tiene tarifa para la empresa seleccionada
	           {
		        ///========================================================================================================
		        ///TARIFA DE COBRO POR GRUPO    
		        ///========================================================================================================
		        ///Si no encontro tarifa para el articulo busco si existe tarifa o % de utilidad para el grupo del articulo 
		        if ($wcons=="codart")
		           {
			        //VERIFICO QUE EL CODIGO DIGITADO SEA EXTERNO O NO  ============================================================  
			        $q= "  SELECT axpcpr "
			           ."    FROM farstore_000009 "
			           ."   WHERE axpcpr = '".$wdato."'"
			           ."     AND axpest = 'on' ";
			        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				    $num = mysql_num_rows($res);
				    if ($num > 0)    //Si entra aca es porque el codigo digitado es externo. Entonces traigo el interno
				       {
					    $row = mysql_fetch_array($res);   
			            $wdato=$row[0];
			            $whomolo="S";
		               }
		              else
		                 $whomolo="N";
			           
			        $q= "  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
			           ."    FROM farstore_000001, farstore_000007, farstore_000027, farstore_000024 "
			           ."   WHERE artcod                             = '".$wdato."'"
			           ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
			           ."     AND empcod                             = '".$wemp."'"
			           ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
			           ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
			           ."     AND artcod                             = karcod "
			           ."     AND karcco                             = '".$wcco."'"
			           ."     AND tgrest                             = 'on' "
			           ."     AND artest                             = 'on' "
			           ."     AND tgrfec                            <= '".$wfecha."'"
			           ."     AND tgrpac                             > 0 "
			           ."   UNION "
			           ."  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
			           ."    FROM farstore_000001, farstore_000007, farstore_000027, farstore_000024 "
			           ."   WHERE artcod                             = '".$wdato."'"
			           ."     AND artgru                             = tgrgru "
			           ."     AND empcod                             = '".$wemp."'"
			           ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
			           ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
			           ."     AND artcod                             = karcod "
			           ."     AND karcco                             = '".$wcco."'"
			           ."     AND tgrest                             = 'on' "
			           ."     AND artest                             = 'on' "
			           ."     AND tgrfec                             > '".$wfecha."'"
			           ."     AND tgrpan                             > 0 "
			           ."  ORDER BY artnom "; 
			       }    
		        if ($wcons=="desart")
		           {
			        $q= "  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
			           ."    FROM farstore_000001, farstore_000007, farstore_000027, farstore_000024 "
			           ."   WHERE artnom                             like '".$wdato."'"
			           ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
			           ."     AND empcod                             = '".$wemp."'"
			           ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
			           ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
			           ."     AND artcod                             = karcod "
			           ."     AND karcco                             = '".$wcco."'"
			           ."     AND tgrest                             = 'on' "
			           ."     AND artest                             = 'on' "
			           ."     AND tgrfec                            <= '".$wfecha."'"
			           ."     AND tgrpac                             > 0 "
			           ."   UNION "
			           ."  SELECT artcod, artnom, (karpro+(karpro*(tgrpac/100))), (karpro+(karpro*(tgrpan/100))), karexi, tgrfec "
			           ."    FROM farstore_000001, farstore_000007, farstore_000027, farstore_000024 "
			           ."   WHERE artnom                             like '".$wdato."'"
			           ."     AND artgru                             = tgrgru "
			           ."     AND empcod                             = '".$wemp."'"
			           ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
			           ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
			           ."     AND artcod                             = karcod "
			           ."     AND karcco                             = '".$wcco."'"
			           ."     AND tgrest                             = 'on' "
			           ."     AND artest                             = 'on' "
			           ."     AND tgrfec                             > '".$wfecha."'"
			           ."     AND tgrpan                             > 0 "
			           ."  ORDER BY artnom "; 
		           }    
		           
		        $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				$num = mysql_num_rows($res);
			     
				if ($num > 0) //El articulo existe y tiene tarifa, entra por el then
				   {
				    echo "<td align=center colspan=".($wcol-5)."><select name='warticulo'>";                         //warticulo
				    //echo "<option>&nbsp</option>";   
				    for ($i=1;$i<=$num;$i++)
				       {
				         $row = mysql_fetch_array($res); 
					     //=========================================================================================
						 //Esto lo hago para colocar todas las descripciones del mismo tama�o, osea de 60 caracteres
					     $j= 60-strlen($row[1]);
					     for ($k=1;$k<=$j;$k++)
					         $row[1]=$row[1].'&nbsp';
					         
					     if ($wfecha < $row[5])   //Aca evaluo si tomo el valor anterior o el actual
				            $wval = $row[3];      //Valor anterior
				           else
				              $wval = $row[2];    //Valor actual
					     //=========================================================================================
					     echo "<option>".$row[0]." | ".$row[1]." | "."$ ".number_format($wval,2,'.',',')." | ".$row[4]."</option>";
					   }
					echo "</select></td>";
				    ?>	    
				      <script>
				        function ira(){document.ventas.wcan.focus();}
				        function ira(){document.ventas.wcan.select();}
				      </script>
				    <?php
					echo "<td bgcolor=".$wcf."><BLINK>Cantidad <INPUT TYPE='text' NAME='wcan' VALUE=1 onkeypress='if ((event.keyCode < 48 || event.keyCode > 57) & event.keyCode != 13) event.returnValue = false'></BLINK></td>";    //wcan
					echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                            //submit 
				    echo "</tr>";
					  
				    $wventa="N";
				    $wdesemp=0;
		            $wrecemp=0;
		            $wdesart=0;
		            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
			       } 
			      else
			         { 
				      //===========================================================================================
				      //Aca hago la busqueda del motivo por el cual NO sale el articulo al momento de irlo a vender
				      //===========================================================================================
				      
				      if ($wcons=="codart")
		                 {
			              $q =  " SELECT count(*) "
						       ."   FROM farstore_000001 "
						       ."  WHERE artcod                            = '".$wdato."'"
						       ."    AND artest                            = 'on' ";
						  $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
						  $num = mysql_num_rows($res);
					      $row = mysql_fetch_array($res); 
					      
					      if ($row[0] == 0) 
					         if ($whomolo == "S")
						        echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No existe o Esta inactivo en el Maestro de Articulos</TD>";     
						       else
						          echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No ha sido homologado</TD>";      
					        else
						       {
							    $q =  " SELECT count(*) "
						             ."   FROM farstore_000026, farstore_000024 "
						             ."  WHERE mid(mtatar,1,instr(mtatar,'-')-1) = '".$wdato."'"
						             ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) "
						             ."    AND empcod                            = '".$wemp."'"
						             ."    AND mid(mtacco,1,instr(mtacco,'-')-1) = '".$wcco."'"
						             ."    AND mtaest                            = 'on' ";
							    $res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
							    $num = mysql_num_rows($res);
							    $row = mysql_fetch_array($res); 
							    
							    if ($row[0] == 0)   
						           echo "<td bgcolor=#99FFCC colspan=".($wcol-5).">El Articulo No tiene tarifa para la sucursal y responsable seleccionado</TD>";
					           }   
					      }     
				      //===========================================================================================   
			          echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                        //submit 
			          $wventa="N";
			          $wdesemp=0;
		              $wrecemp=0;
		              $wdesart=0;
		              mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
		             }
	           }     
		 }
	   else
	       //===========================================================================================================================
	       //===========================================================================================================================
	       //ACA ESTAN LOS DATOS SETIADOS   
	       //===========================================================================================================================
	       //===========================================================================================================================
		   {
			if (isset($warticulo))
			   {
				$wini="N";   
				echo "<input type='HIDDEN' name= 'wini' value='N'>";                                            //wini
		        echo "<input type='HIDDEN' name= 'wfecha_tempo' value='".$wfecha_tempo."'>";                    //wfecha_tempo
		        echo "<input type='HIDDEN' name= 'whora_tempo' value='".$whora_tempo."'>";                      //whora_tempo
				   
				$pos = strpos($warticulo,"|");
		        $wart = substr($warticulo,0,$pos-1); 
		        
		        $q =  " SELECT artcod, artnom, unides, mtavac, artiva, karexi, karpro, mtavan, mtafec, emppdt, empprt, mtapde "
				     ."   FROM farstore_000001, farstore_000026, farstore_000024, farstore_000002, farstore_000007 "
				     ."  WHERE artcod                            = '".$wart."'"
				     ."    AND artcod                            = mid(mtaart,1,instr(mtaart,'-')-1) "
				     ."    AND artest                            = 'on' "
				     ."    AND mtaest                            = 'on' "
				     ."    AND unicod                            = mid(artuni,1,instr(artuni,'-')-1) "
				     ."    AND karcco                            = '".$wcco."'"
			         ."    AND karcod                            = artcod "
			         ."    AND karexi                           >= ".$wcan
			         ."    AND empcod                            = '".$wemp."'"
			         ."    AND mid(mtatar,1,instr(mtatar,'-')-1) = mid(emptar,1,instr(emptar,'-')-1) ";
				
			    $res = mysql_query($q,$conex); //or die (mysql_errno()." - ".mysql_error());
			    $num = mysql_num_rows($res);   //or die (mysql_errno()." - ".mysql_error());
			    
			    if ($num > 0)
			       {
	                $row = mysql_fetch_array($res); 
			        $wart    = $row[0];
			        $wdes    = $row[1];
			        $wuni    = $row[2];
			        $wvac    = $row[3];
			        $wporiva = $row[4];
			        $wcospro = $row[6];
			        $wvan    = $row[7];
			        $wfeccam = $row[8];
			        $wdesemp = ($row[9]/100);
	                $wrecemp = ($row[10]/100);
	                $wdesart = ($row[11]/100);
			        
			        if ($wfecha < $wfeccam)   //Aca evaluo si tomo el valor anterior o el actual
			           $wval = $wvan;
			          else
			             $wval = $wvac;
				           
			            
			        //////////////////////////////////////////////////////////////////////////////////////////////////////////////      
			        //CALCULO DEL IVA ============================================================================================     
			        //EL 1 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO     
			        //$wvaliva = (integer)($wcan*$wval*($wporiva/100));
			        //$wvaltot = (integer)(($wcan*$wval)+($wcan*$wval*($wporiva/100)));
			        if ($wporiva > 0)
			     	   $wvaliva = (integer)(($wcan*$wval)-(($wcan*$wval)/(1+($wporiva/100))));
			     	  else
			     	     $wvaliva=0; 
			     	$wvaltot = (integer)($wcan*$wval);
			     	
			     	if ($wcan > 0)
			           {		    
				        //Si entra por aca es porque ya se valido y por ende puede grabar el articulo en la tabla TEMPORAL
	     	            $q= " INSERT INTO farstore_000034 (Medico    ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    ,  temrem    ,  temdar    , Seguridad) "
		                   ."                      VALUES ('farstore','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.",".$wdesart.", 'C-".$wusuario."')";
		                //$res2 = mysql_query($q,$conex);
		                $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	                   } 
		            
		            $wventa="N";
		            mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
		           }
		           else  //Si el articulo no tiene la cantidad digitada con tarifa POR ARTICULO, busco la cantidad pero con tarifa por grupo
	                  {
		               $q= "  SELECT artcod, artnom, unides, (karpro+(karpro*(tgrpac/100))), artiva, karexi, karpro, (karpro+(karpro*(tgrpan/100))), tgrfec, emppdt, empprt "
				          ."    FROM farstore_000001, farstore_000027, farstore_000024, farstore_000002, farstore_000007 "
				          ."   WHERE artcod                             = '".$wart."'"
				          ."     AND mid(artgru,1,instr(artgru,'-')-1)  = mid(tgrgru,1,instr(tgrgru,'-')-1) "
				          ."     AND empcod                             = '".$wemp."'"
				          ."     AND mid(tgrcod,1,instr(tgrcod,'-')-1)  = mid(emptar,1,instr(emptar,'-')-1) "
				          ."     AND mid(tgrcco,1,instr(tgrcco,'-')-1)  = '".$wcco."'"
				          ."     AND artcod                             = karcod "
				          ."     AND karcco                             = '".$wcco."'"
				          ."     AND tgrest                             = 'on' "
				          ."     AND artest                             = 'on' "
				          ."     AND karexi                            >= ".$wcan;    
					   	
					   $res = mysql_query($q,$conex); //or die (mysql_errno()." - ".mysql_error());
					   $num = mysql_num_rows($res);   //or die (mysql_errno()." - ".mysql_error());
					    
					   if ($num > 0)
					      {
			               $row = mysql_fetch_array($res); 
					       $wart    = $row[0];
					       $wdes    = $row[1];
					       $wuni    = $row[2];
					       $wvac    = $row[3];
					       $wporiva = $row[4];
					       $wcospro = $row[6];
					       $wvan    = $row[7];
					       $wfeccam = $row[8];
					       $wdesemp = ($row[9]/100);
			               $wrecemp = ($row[10]/100);
					        
					       if ($wfecha < $wfeccam)   //Aca evaluo si tomo el valor anterior o el actual
					          $wval = $wvan;
					         else
					            $wval = $wvac;
					       //////////////////////////////////////////////////////////////////////////////////////////////////////////////      
			               //CALCULO DEL IVA ============================================================================================    
					       //EL 1 DE AGOSTO SE CAMBIA LA FORMA DE CALCULAR EL IVA DEBIDO A QUE EL VALOR DE LA TARIFA YA LO TIENE INCLUIDO             
					       //$wvaliva = $wcan*$wval*($wporiva/100);
					       //$wvaltot = (($wcan*$wval)+($wcan*$wval*($wporiva/100)));
					       if ($wporiva > 0)
					          $wvaliva = (integer)(($wcan*$wval)-(($wcan*$wval)/(1+($wporiva/100))));
					         else
					            $wvaliva=0; 
					       $wvaltot = (integer)($wcan*$wval);
					       
					       			    
						   //Si entra por aca es porque ya se valido y por ende puede grabar el articulo en la tabla TEMPORAL
			     	       $q= " INSERT INTO farstore_000034 (Medico    ,   Fecha_data ,   Hora_data,   temusu      ,   temfec           ,   temhor          ,   temsuc  ,   temcaj   ,   temtcl     ,   temres  ,   temdcl     ,   temncl     ,   temart ,    temdes  ,   tempre  ,  temcan ,  temvun ,  tempiv    ,  temiva    ,  temtot     , temcmo      ,  temcpr    ,  temdem    , temrem     , Seguridad) "
				               ."                     VALUES ('farstore','".$wfecha."' ,'".$hora."' ,'".$wusuario."','".$wfecha_tempo."' ,'".$whora_tempo."' ,'".$wcco."','".$wcaja."','".$wtipcli."','".$wemp."','".$wdocpac."','".$wnompac."','".$wart."','".$wdes."','".$wuni."',".$wcan.",".$wval.",".$wporiva.",".$wvaliva.",".$wvaltot.",".$wcuotamod.",".$wcospro.",".$wdesemp.",".$wrecemp.", 'C-".$wusuario."')";
				           //$res2 = mysql_query($q,$conex);
				           $res2 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
				            
				           $wventa="N";
				           $wdesart=0;
				           mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
				          }   
		                 else 
		                    {
		                     ////===========================================================================================================================   
				             ////===========================================================================================================================
				             ////===========================================================================================================================
				             echo "<td bgcolor=#99FFCC colspan=".($wcol-6).">No se tiene disponible la cantidad solicitada o NO tiene asignada unidad de medida</TD>";  
				             echo "<td align=center bgcolor=#cccccc><input type='submit' value='OK'></td>";                        //submit 
				             $wventa="N";
				             $wdesemp=0;
		      				 $wrecemp=0;
		      				 $wdesart=0;
		      				 mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
			                } 
	                  } 
	           } // fin del if isset($warticulo)  
			  else
			     if ($wini == 'N') //Aca entra porque no digito nada pero ya ha digitado otro u otros articulos
			        {
				     $wventa="N";
				     $wdesemp=0;
		             $wrecemp=0; 
		             $wdesart=0;
		             mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
				    } 
		   }
	   echo "</tr>";   
	 } //Fin del then del if de $wventa = 'N' 	   
    else
       {
	    //=================================================================================================================   
	    //=================================================================================================================
	    //ACA SE GRABA LA VENTA !!!!!!!!!!!
	    //=================================================================================================================
	    
	    if ($wini == "N")
	    {
		$wdesart=0;    
	    mostrar($wusuario,$wfecha_tempo,$whora_tempo,$wcco,$wcaja,$conex,$wini,$wdocpac,$wnompac,$wtelpac,$wdirpac,$wmaipac,$wcol,$wtipcli,$wcuotamod,$wempresa,$wventa,$wtipven,$wmensajero,$wdesemp,$wrecemp,$wdesart);
	      
	    $WSINCUOTA="N";                                     //Indica que el responsable es una empresa pero no se le cobra nada al paciente
        if ($wtipcli=="01-PARTICULAR") 
           include("/farmastore/Grabar_venta.php");   
	      else  //Cuando entre por aca pregunto si la cuota moderadora es mayor a cero
	         {
		      if ($wcuotamod > 0 and $wtipcli <> "01-PARTICULAR")   
		         include("/farmastore/Grabar_venta.php");    
	            else 
			       if ($wcuotamod == 0 and $wtipcli <> "01-PARTICULAR")   
			          {
				       $WSINCUOTA="S";                     //Si entra por aca Indica que el responsable es una empresa pero no se le cobra nada al paciente   
		               include("/farmastore/Grabar_venta.php"); 
		               $fk=0;
		              } 
		     }
		echo "<input type='HIDDEN' name= 'wventa' value='".$wventa."'>";      //Envio la venta como "S"
	    echo "<input type='HIDDEN' name= 'fk' value='".$fk."'>";              //Contador de formas de pago que han digitado
        }
       }  
   echo "</table>";       
   $wdato="";	   
   $wcodart="";
   $wdesart="";
   unset($wcodart);
   unset($wdesart);  
   unset($wdato);  
   //echo "<meta http-equiv='refresh' content='0;url=Ventas.php?'>";
   echo "<br><br>";	   
   echo "<TR align=left><td align=left bgcolor=#cccccc><font size=3><A href='copia_factura.php?wcaja=".$wcaja."'> Imprimir Copia de Factura</A></font></TD></TR>";
}
?>
