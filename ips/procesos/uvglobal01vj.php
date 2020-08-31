<html>
<head>
<title>ORDENES DE LABORATORIO</title>
</head>

<script type="text/javascript">
	function enter()
	{
	  document.forms.uvglobal01.submit();   // Ojo para la funcion uvglobal01 <> Uvglobal01  (sencible a mayusculas)
	}
</script>

<script>
function ira(){document.uvglobal01.wdoc.focus();} 
</script>
 
<body  onload=ira() BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==========================================================================================================================================
//PROGRAMA				      :Genera Ordenes de Laboratorio VI.                                                                 
//AUTOR				          :Jair Saldarriaga Orozco.                                                                                   
//FECHA CREACION			  :ENERO 16 DE 2008.                                                                                           
//FECHA ULTIMA ACTUALIZACION  :25 de Julio de 2008.              
//FECHA ULTIMA ACTUALIZACION  :04 de Septiembre de 2008.                                                                                        

function validar_datos($fe,$do,$fu,$fa,$ob,$fr,$ff,$ld,$li,$re) 
 {	
  //La fn recibe fecha,docmto,factura,observac,Fecha de recibo,fecha entrega,Lente Der,Lente Izq,Montura (referencia)
   global $todok;
   $todok = true;
    
   $query = "Select * From uvglobal_000041 Where Clidoc='".$do."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   $registro = mysql_fetch_row($resultado);  
   $wnombre = $registro[4];
   if ($nroreg < 1 )     //No encontro 
     $todok = false;
  
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($fe,5,2), substr($fe,8,2), substr($fe,0,4)) )
    $todok = false;
 
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( ($fr != '0000-00-00') and ($fr != '') )
    if ( !checkdate(substr($fr,5,2), substr($fr,8,2), substr($fr,0,4)) )
     $todok = false;

   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( ($ff != '0000-00-00') and ($ff != '') )
    if ( !checkdate(substr($ff,5,2), substr($ff,8,2), substr($ff,0,4)) )
     $todok = false;
      
   //if ( $fa == "" )    //Se quedo que pueden haber ordenes sin factura
   //  $todok = false;
     
   if ( $ob == "" )
     $todok = false;
     
   if ( $ld != "" ) 
   {
    $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
    $query=$query." WHERE Venffa = '".$fu."' And Vennfa = '".$fa."' And Vdenum = Vennum And Vdeart = Artcod";
    $query=$query." And Artcod = '".$ld."' And (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE');";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg == 0)   //No Encontro codigo de Lente Derecho
     $todok = false;
   }  
     
   if ( $li != "" ) 
   {
    $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
    $query=$query." WHERE Venffa = '".$fu."' And Vennfa = '".$fa."' And Vdenum = Vennum And Vdeart = Artcod";
    $query=$query." And Artcod = '".$li."' And (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE' );";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg == 0)   //No Encontro codigo de Lente Izquierdo
     $todok = false;
   }  
    
   if ( $re != "" )
   {
    $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
    $query=$query." WHERE Venffa = '".$fu."' And Vennfa = '".$fa."' And Vdenum = Vennum And Vdeart = Artcod";
    $query=$query." And Artcod = '".$re."' And mid(Artgru,1,2) = 'MT';";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);    	  
    if ($nroreg == 0)   //No Encontro codigo de la montura o referencia
     $todok = false;
   }
      	  
   return $todok;
 }  
 
session_start();
if(!isset($_SESSION['user']))
 	echo "error";
else
{

 //$user = "1-uvla01";   //Temporal!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	

	
	mysql_select_db("matrix") or die("No se selecciono la base de datos");    

	    $query = "Select Cjecco From uvglobal_000030 Where Cjeusu = '".substr($user,2,80)."'";
	    $resultado = mysql_query($query);
	    $registro = mysql_fetch_row($resultado);  
	    $sede = $registro[0];
       
	     echo "<form name='uvglobal01' action='uvglobal01vj.php' method=post>";
        
        echo "<center><table border=1 >";
		echo "<tr><td colspan=1 rowspan=4  align=center><IMG SRC='/matrix/images/medical/pos/logo_uvglobal.png' ></td>";				
		echo "<tr><td colspan=5 align=center><b>UNIDAD VISUAL GLOBAL S.A.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$sede."<b></td></tr>";
		echo "<tr><td colspan=5 align=center><b>NIT. 811.017.919-1<b></td></tr>";
		echo "<tr><td colspan=5 align=center><b>ORDEN DE LABORATORIO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; NRO: &nbsp;&nbsp;&nbsp;";
		
		if (($wproceso == "Nuevo") or (!isset($wproceso)))
	    { 
	     //Tomo el consecutivo que sigue
	     $wedita="";
     	 $query = "Select carcon From uvglobal_000040 Where Carfue = 'OT' And Carest = 'on' And Carotr = 'on' ";
    	 $resultado = mysql_query($query);
		 $nroreg = mysql_num_rows($resultado);
		 $registro = mysql_fetch_row($resultado);  
		 $wconsecutivo = $registro[0];
    	 echo "<INPUT TYPE='text' NAME='wnro' size=10 color=#003366 VALUE='".$wconsecutivo."'></INPUT>";
    	}
    	else
    	$wedita="disabled";
    	 echo "<INPUT TYPE='text' NAME='wnro' size=10 color=#003366 VALUE='".$wnro."'></INPUT>";
    			
        echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Cedula:</font></b><br>";
        if (isset($wdoc))
        {
    	 echo "<INPUT TYPE='text' NAME='wdoc' size=10 VALUE='".$wdoc."' ></INPUT></td>";
     	 $query = "Select * From uvglobal_000041 Where Clidoc='".$wdoc."'";
    	 $resultado = mysql_query($query);
		 $nroreg = mysql_num_rows($resultado);
		 $registro = mysql_fetch_row($resultado);  
		 $wnombre = $registro[4];
		 $wtelefono = $registro[5];
         echo "<td align=center bgcolor=#DDDDDD colspan=3><b><font text color=990000 size=4> ".$wnombre."</font></b></td>";
         echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=990000 size=3> Tel: ".$wtelefono."</font></b></td>";
        } 
    	else
    	{
		 echo "<INPUT TYPE='text' NAME='wdoc' size=10 onchange='enter()'></INPUT></td>";
		 echo "<td align=center bgcolor=#DDDDDD colspan=3>";
		 echo "<td align=center bgcolor=#DDDDDD colspan=2>"; 
	    } 
		  
	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	     echo "<tr><td bgcolor=#cccccc align=center colspan=3><b><font text color=#003366 size=2> <i>Rango: </font></b><select name='wran'>";	
	     if (!isset($wran)) //No esta seteada
	     {
		  echo "<option>";
		  echo "<option value='1'>Rango 1";
		  echo "<option value='2'>Rango 2";
		  echo "<option value='3'>Rango 3";
	     } 
	     else              //Ya esta seteada
	     {    
		     
		 if  ($wran == "") 
       	  echo "<option selected >";
       	 else
          echo "<option>";
       	      
	     if ($wran == "1")
          echo "<option selected value='1'>Rango 1";
         else
          echo "<option value='1'>Rango 1";
          
   	     if ($wran == "2")
          echo "<option selected value='2'>Rango 2";
         else
          echo "<option value='2'>Rango 2";
          
   	     if ($wran == "3")
          echo "<option selected value='3'>Rango 3";
         else
          echo "<option value='3'>Rango 3";
         } 
         echo "</select></td>";
        

        
        echo "<td colspan=3 bgcolor=#cccccc align=center>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
	      if ((isset($wtus) and $wtus == "1") or !isset($wtus))
	      {
           echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1'>Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
           echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2'>Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";    
           echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3' CHECKED>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";    
           echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";    
          }
          else
          { 
	       if ($wtus == "2")
	       {   
	        echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1' >Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2' CHECKED>Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";   
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3'>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";  
            echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";      
           }
           else
           {
	        if ($wtus == "3")   
	        {
 	         echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1' >Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
             echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2' >Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>"; 
             echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3' CHECKED>Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";                  
             echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4'>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";    
            }
            else
            {
 	         echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='1' >Beneficiario.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
             echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='2' >Cotizante.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>"; 
             echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='3' >Particular.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";                 
             echo "<INPUT TYPE = 'Radio' NAME = 'wtus' VALUE ='4' CHECKED>Prepagada.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";    
            }    
           } 
          }
        
        echo "<tr>";
        echo "<td colspan=1 align=center><b>LENTES<b></td>";
        echo "<td colspan=1 align=center><b>ESFERA<b></td>";
        echo "<td colspan=1 align=center><b>CILINDRO<b></td>";
        echo "<td colspan=1 align=center><b>EJE<b></td>";
        echo "<td colspan=1 align=center><b>ADD<b></td>";
        echo "<td colspan=1 align=center><b>TIPO<b></td>";
        echo "</tr>";     
         	 
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Ojo Derecho:</font></b></td>";
        
     // CASO RARO si envio por el URL una variable que contiene el signo + este llega nulo 
     // por lo que siempre los vuelvo a tomar del registro  
     // if (($wproceso != "Nuevo") and  ( isset($wdat) ) )
     // if (($wproceso != "Nuevo") and  ( isset($wnro) ) )
     // {
     //  $query = "Select orddsi,ordisi From uvglobal_000133 where ordnro = ".$wnro;
     //  $resultado = mysql_query($query);
     //  $registro = mysql_fetch_row($resultado);  
     //  $wdsi = $registro[0];
     //  $wisi = $registro[1];  
     // } 
              
	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	     //  *** SIGNO DERECHO  ***
	     echo "<td bgcolor=#cccccc align=center colspan=1><select name='wdsi'>";	 
	     if (!isset($wdsi)) //No esta seteada
	     {
		  echo "<option selected>";
		  echo "<option value='-'>-";
	     } 
	     else              //Ya esta seteada
	     {   
		  if  ($wdsi == "") 
       	   echo "<option selected>";
       	  else
           echo "<option>";
       	      
   	      if ($wdsi == "-")
           echo "<option selected value='-'>-";
          else
           echo "<option value='-'>-";          
         } 
         echo "</select>";  
         
        
        //  *** ESFERA DERECHA  *** 
       	echo "<select name='wdes'>";	
        if (!isset($wdes)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wdes == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	
       	if (!isset($wdes)) //No esta seteada
       	 echo "<option value='N'>N";     	
       	else               //Ya esta seteada
       	 if  ($wdes == "N") 
       	  echo "<option selected value='N'>N";     
       	 else
       	  echo "<option value='N'>N";   
       	  	        	
       	 $i=0.25;
       	 while ($i <= 25.00 )
       	 {
	       //Formato con dos decimales coloca ceros 	 
	       $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);	
	       //Para que no imprima el cero a la izquierda
	       if (substr($k,0,1) == 0)
	         $k = " ".substr($k,1,4);
	         
	       if (!isset($wdes)) //No esta seteada 	 
       	    echo "<option value=".$k.">".$k; 
       	   else
       	    if ($wdes == $k)
       	     echo "<option selected value=".$k.">".$k; 
       	    else
       	     echo "<option value=".$k.">".$k; 
       	   $i = $i + 0.25;
         }      
         echo "</select></td>";	       
      
        //  *** CILINDRO DERECHO  ***  
        echo "<td bgcolor=#cccccc align=center colspan=1><font text color=#003366 size=4> <i> - </font></b><select name='wdci'>";	
        if (!isset($wdci)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wdci == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	               	
       	$i=0.25;
       	while ($i <= 10.00 )
       	{
	      $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);	  
	      //Para que no imprima el cero a la izquierda
	       if (substr($k,0,1) == 0)
	         $k = " ".substr($k,1,4);	
	         
	      if (!isset($wdci)) //No esta seteada 	 	 	
       	   echo "<option value=-".$k.">".$k; 
       	  else
       	   if ($wdci == $k)
       	    echo "<option selected value=".$k.">".$k; 
       	   else
       	    echo "<option value=".$k.">".$k;   
       	  $i = $i + 0.25;
       	}      
        echo "</select></td>";
           
        //  *** EJE DERECHO  *** 

        echo "<td bgcolor=#cccccc align=center colspan=1><select name='wdej'>";	  
        if (!isset($wdej)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wdej == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	    	
       	$i=1;
       	while ($i <= 180 )
       	{
	      if (!isset($wdej)) //No esta seteada 	
       	   echo "<option value=".$i.">".$i; 
       	  else               //Ya esta seteada
       	   if ($wdej == $i)
       	    echo "<option selected value=".$i.">".$i; 
       	   else
       	    echo "<option value=".$i.">".$i; 
       	    
       	  $i = $i + 1;
       	}      
        echo "</select></td>";

        //  *** ADD DERECHO  ***  
        echo "<td bgcolor=#cccccc align=center colspan=1><font text color=#003366 size=4> <i> + </font></b><select name='wdad'>";	
        if (!isset($wdad)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wdad == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	               	
       	$i=0.75;
       	while ($i <= 3.50 )
       	{
	      $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);	  
	      //Para que no imprima el cero a la izquierda
	       if (substr($k,0,1) == 0)
	         $k = " ".substr($k,1,4);	
	         
	      if (!isset($wdad)) //No esta seteada 	 	 	
       	   echo "<option value=-".$k.">".$k; 
       	  else
       	   if ($wdad == $k)
       	    echo "<option selected value=".$k.">".$k; 
       	   else
       	    echo "<option value=".$k.">".$k;   
       	  $i = $i + 0.25;
       	}      
        echo "</select></td>";

	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	     echo "<td bgcolor=#cccccc align=center colspan=1><select name='wdte'>";	
	     if (!isset($wdte)) //No esta seteada
	     {
		  echo "<option>";
		  echo "<option value='1'>Terminado";
		  echo "<option value='2'>Tallado";
	     } 
	     else              //Ya esta seteada
	     {    
	     if ($wdte == "")
          echo "<option selected>";
         else
          echo "<option>";
          
  	     if ($wdte == "1")
          echo "<option selected value='1'>Terminado";
         else
          echo "<option value='1'>Terminado";
          
   	     if ($wdte == "2")
          echo "<option selected value='2'>Tallado";
         else
          echo "<option value='2'>Tallado";
         } 
         echo "</select></td>";
        
           
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Ojo Izquierdo:</font></b></td>";
                     
	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	      echo "<td bgcolor=#cccccc align=center colspan=1><select name='wisi'>";	 
	     if (!isset($wisi)) //No esta seteada
	     {
		  echo "<option selected>";
		  echo "<option value='-'>-";	  
	     } 
	     else              //Ya esta seteada
	     {   
		  if  ($wisi == "") 
       	   echo "<option selected>";
       	  else
           echo "<option>";
       	      
   	      if ($wisi == "-")
           echo "<option selected value='-'>-";
          else
           echo "<option value='-'>-";          
         } 
         echo "</select>";  
       
        // *** ESFERA IZQUIERDA ****  
       	echo "<select name='wies'>";	
        if (!isset($wies)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wies == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	
       	if (!isset($wies)) //No esta seteada
       	 echo "<option value='N'>N";     	
       	else               //Ya esta seteada
       	 if  ($wies == "N") 
       	  echo "<option selected value='N'>N";     
       	 else
       	  echo "<option value='N'>N";   
       	  	        	
       	 $i=0.25;
       	 while ($i <= 25.00 )
       	 {
	       $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);	 
	       //Para que no imprima el cero a la izquierda
	       if (substr($k,0,1) == 0)
	         $k = " ".substr($k,1,4);	
	       if (!isset($wies)) //No esta seteada 	 
       	    echo "<option value=".$k.">".$k; 
       	   else
       	    if ($wies == $k)
       	     echo "<option selected value=".$k.">".$k; 
       	    else
       	     echo "<option value=".$k.">".$k; 
       	   $i = $i + 0.25;
         }      
         echo "</select></td>";	       

        //  *** CILINDRO IZQUIERDO  ***  
        echo "<td bgcolor=#cccccc align=center colspan=1><font text color=#003366 size=4> <i> - </font></b><select name='wici'>";	
        if (!isset($wici)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wici == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	               	
       	$i=0.25;
       	while ($i <= 10.00 )
       	{
	      $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);	  
	      //Para que no imprima el cero a la izquierda
	       if (substr($k,0,1) == 0)
	         $k = " ".substr($k,1,4);	
	         
	      if (!isset($wici)) //No esta seteada 	 	 	
       	   echo "<option value=-".$k.">".$k; 
       	  else
       	   if ($wici == $k)
       	    echo "<option selected value=".$k.">".$k; 
       	   else
       	    echo "<option value=".$k.">".$k;   
       	  $i = $i + 0.25;
       	}      
        echo "</select></td>";
        
        //  *** EJE IZQUIERDO  ****        
        echo "<td bgcolor=#cccccc align=center colspan=1><select name='wiej'>";	    	
        if (!isset($wiej)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wiej == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	  
       	$i=1;
       	while ($i <= 180 )
       	{
	      if (!isset($wiej)) //No esta seteada 	
       	   echo "<option value=".$i.">".$i; 
       	  else               //Ya esta seteada
       	   if ($wiej == $i)
       	    echo "<option selected value=".$i.">".$i; 
       	   else
       	    echo "<option value=".$i.">".$i; 
       	    
       	  $i = $i + 1;
       	}      
        echo "</select></td>";

         //  *** ADD IZQUIERDO  ***  
        echo "<td bgcolor=#cccccc align=center colspan=1><font text color=#003366 size=4> <i> + </font></b><select name='wiad'>";	
        if (!isset($wiad)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wiad == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	               	
       	$i=0.75;
       	while ($i <= 3.50 )
       	{
	      $k=str_pad(number_format($i,'2','.',''),5,"0",STR_PAD_LEFT);	  
	      //Para que no imprima el cero a la izquierda
	       if (substr($k,0,1) == 0)
	         $k = " ".substr($k,1,4);	
	         
	      if (!isset($wiad)) //No esta seteada 	 	 	
       	   echo "<option value=-".$k.">".$k; 
       	  else
       	   if ($wiad == $k)
       	    echo "<option selected value=".$k.">".$k; 
       	   else
       	    echo "<option value=".$k.">".$k;   
       	  $i = $i + 0.25;
       	}      
        echo "</select></td>";

        
   	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	     echo "<td bgcolor=#cccccc align=center colspan=1><select name='wite'>";	
	     if (!isset($wite)) //No esta seteada
	     {
		  echo "<option>";   
		  echo "<option value='1'>Terminado";
		  echo "<option value='2'>Tallado";
	     } 
	     else              //Ya esta seteada
	     {   
	     if ($wite == "")
          echo "<option selected>";
         else
          echo "<option>";
		      
	     if ($wite == "1")
          echo "<option selected value='1'>Terminado";
         else
          echo "<option value='1'>Terminado";
          
   	     if ($wite == "2")
          echo "<option selected value='2'>Tallado";
         else
          echo "<option value='2'>Tallado";
         } 
         echo "</select></td>";

/*
       //Como una AYUDA ADICIONAL lleno por defecto al entrar una orden nueva los campos 'Cod lente' con los facturados en la ultima factura

        if ( ($wproceso == "Nuevo") and (isset($wdoc)) and ( $wled == "") )
        {  
         //Busco la ultima factura de este documento	          	     	       
   	     $query = "Select fenval,fennpa,fenfac From uvglobal_000018 Where Fendpa = '".$wdoc."' Order by Fenfac Desc";
    	 $resultado = mysql_query($query);
    	 $nroreg = mysql_num_rows($resultado);
     	 if ($nroreg > 0)   //Encontro  
     	 { 
     	  $registro = mysql_fetch_row($resultado); 
     	  // busco el codigo de los lentes vendidos en la ultima factura de este documento (grupo de Lentes)
          $query = "SELECT 
        			Vdenum, Vdeart, Vdecan 
        		FROM 
        			uvglobal_000016, uvglobal_000017, uvglobal_000001 
        		WHERE  
        			Vennfa = '".$registro[2]."'         
        			And Vdenum = Vennum And Vdeart = Artcod             
                    And (SUBSTR(Artgru,1,2) = 'LO' or SUBSTR(Artgru,1,2) = 'LC' or  SUBSTR(Artgru,1,2) = 'LE'); ";     
                   
           $resultado = mysql_query($query, $conex);             		
    	   $nroreg = mysql_num_rows($resultado);
    	   if ($nroreg > 0)   //Encontro
    	   {
    	    $registro = mysql_fetch_row($resultado);  
    	    //Si cantidad = 2 coloco el codigo en el campo Lente Ojo Izq y el campo Ojo Der
    	    if ( $registro[2] == 2 )
    	    {
	    	 $wled = $registro[1];
	    	 $wlei = $registro[1];
            }  
           } 
          }
         } 
*/
             
    	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Cod. lente OD:</font></b>";
        if (isset($wled) and $wled <> "" and isset($wdoc) and isset($wffa) and  isset($wfac) )
        {
	      echo "<INPUT TYPE='text' NAME='wled' size=10 VALUE='".$wled."' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";  	  
     	  $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
          $query=$query." WHERE Venffa = '".$wffa."' And Vennfa = '".$wfac."' And Vdenum = Vennum And Vdeart = Artcod";
          $query=$query." And Artcod = '".$wled."' And (mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE');";
    	  $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");   	
    	  $nroreg = mysql_num_rows($resultado);
    	  if ($nroreg > 0)   //Encontro
    	  {
		   $registro = mysql_fetch_row($resultado);  
		   $wled = $registro[1];
           echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#006699 size=2> ".$registro[0]."</font></b></td>";
          }
          else
          {
       	   echo "<td align=center bgcolor=#DDDDDD colspan=4>"; 
	       echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR NO EXISTE CODIGO DEL LENTE EN ESTA FACTURA !!!!</MARQUEE></font>";				
	       echo "</b></td>";	          
      	  } 
      	 } 
   		   
    	else
    	{ 
		  echo "<INPUT TYPE='text' NAME='wled' size=10 onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
		  echo "<td align=center bgcolor=#DDDDDD colspan=4>";
	    } 

	     
    	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Cod. lente OI:</font></b>";
        if (isset($wlei) and $wlei <> "" and isset($wffa) and isset($wfac) )
        {
	      echo "<INPUT TYPE='text' NAME='wlei' size=10 VALUE='".$wlei."' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
     	  $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
          $query=$query." WHERE Venffa = '".$wffa."' And Vennfa = '".$wfac."' And Vdenum = Vennum And Vdeart = Artcod";
          $query=$query." And Artcod = '".$wlei."' And ( mid(Artgru,1,2) = 'LO' or mid(Artgru,1,2) = 'LC' or mid(Artgru,1,2) = 'LE' );";
    	  $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
    	  $nroreg = mysql_num_rows($resultado);
    	  if ($nroreg > 0)   //Encontro
    	  {
		   $registro = mysql_fetch_row($resultado);  
           echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#006699 size=2> ".$registro[0]."</font></b></td>";
          }
          else
          {
       	   echo "<td align=center bgcolor=#DDDDDD colspan=4>"; 
	       echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR NO EXISTE CODIGO DEL LENTE EN ESTA FACTURA !!!!</MARQUEE></font>";				
	       echo "</b></td>";	          
      	  } 
   		}   
    	else
    	{
		  echo "<INPUT TYPE='text' NAME='wlei' size=10 onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
		  echo "<td align=center bgcolor=#DDDDDD colspan=4>";
	    }
	    
	//Defino un ComboBox con los vendedores 
    echo "<tr><td align=CENTER colspan=6 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Vendedor del lente: </font></b>";
    
    if ($wedita=='disabled')
    {
     $query = "SELECT ordvel FROM uvglobal_000133 WHERE ordnro =$wnro ";
     $resultado = mysql_query($query);          // Ejecuto el query 
     $nroreg = mysql_num_rows($resultado);
     
     
     echo "<select name='wvel' ".$wedita." >";  //se agrega $wedita para saber si deja modificar o no ese campo con la propiedad disable. tavo 20081119. 
	 echo "<option></option>";                  //primera opcion en blanco 	
    
	 $Num_Filas = 0;
	 while ( $Num_Filas < $nroreg )
	  {
		$registro = mysql_fetch_row($resultado);
		echo "<option selected>".$registro[0]."</option>";
	    $Num_Filas++;		  
      }   
     echo "</select></td></tr>";
     
    }
    else
    {
     $query = "SELECT Cjeusu,descripcion FROM uvglobal_000030,usuarios WHERE Cjeusu = codigo Order BY Cjeusu";
     $resultado = mysql_query($query);          // Ejecuto el query 
     $nroreg = mysql_num_rows($resultado);
    
     echo "<select name='wvel' ".$wedita." >";  //se agrega $wedita para saber si deja modificar o no ese campo con la propiedad disable. tavo 20081119. 
	 echo "<option></option>";                  //primera opcion en blanco 	
    
	 $Num_Filas = 0;
	 while ( $Num_Filas < $nroreg )
	  {
		$registro = mysql_fetch_row($resultado);
  		if(substr($wvel,0,strpos($wvel,"-")) == $registro[0])
	      echo "<option selected>".$registro[0]."- ".$registro[1]."</option>";
	    else
	      echo "<option>".$registro[0]."- ".$registro[1]."</option>";
	    $Num_Filas++;		  
      }   
     echo "</select></td></tr>";	        
     }	    
	      	      
        echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>D.P.</font></b><br>";
        if (isset($wedp))
    	 echo "<INPUT TYPE='text' NAME='wedp' size=10 VALUE='".$wedp."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wedp' size=10></INPUT></td>";
    	 
 
    	echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#003366 size=2> <i>Tratamiento:</font></b><br>";
        if (isset($wtra))
    	 echo "<INPUT TYPE='text' NAME='wtra' size=60 VALUE='".$wtra."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wtra' size=60></INPUT></td>"; 
    	
    	 //  *** ALTURA BIFOCAL  *** 
    	echo "<td bgcolor=#cccccc align=center colspan=1><b><font text color=#003366 size=2> <i>Altura Bifocal en mm.</font></b><br><select name='wbif'>";	    	
        if (!isset($wbif)) //No esta seteada 
         echo "<option>";
       	else               //Ya esta seteada
       	 if  ($wbif == "") 
       	  echo "<option selected >";
       	 else
       	  echo "<option>"; 
       	               	
       	$i=10;
       	while ($i <= 25.00 )
       	{
	      if (!isset($wbif)) //No esta seteada 	 	 	
       	   echo "<option value=-".$i.">".$i; 
       	  else
       	   if ($wbif == $i)
       	    echo "<option selected value=".$i.">".$i; 
       	   else
       	    echo "<option value=".$i.">".$i;   
       	  $i = $i + 1;
       	}      
        echo "</select></td>";
             	 
 		echo "<tr><td colspan=6 bgcolor=#cccccc align=center>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor dos) or si no esta seteada
	    if ((isset($wmon) and $wmon == "2") or !isset($wmon))     //Son tres posibles selcciones,Empiezo por 2 porque quiero que esta quede
        {                                                         //predefinida cuando entre un registro nuevo
		 echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' >Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' CHECKED>Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";       
		 echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' >Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";       
	    } 
	    else
   	     if ((isset($wmon) and $wmon == "1") or !isset($wmon))
         {
		  echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' CHECKED>Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
		  echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' >Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>"; 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' >Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";             
	     } 
         else
	     {
	      echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '1' >Montura Propia<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";
		  echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '2' >Montura U.V.G.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";       
		  echo "<INPUT TYPE = 'Radio' NAME = 'wmon' VALUE = '3' CHECKED>Solo Lentes.<b>&nbsp;&nbsp;&nbsp;</b></INPUT>";       
	     } 
 
    	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Cod. Montura:</font></b>";
        if (isset($wref) and $wref <> "" and isset($wffa) and isset($wfac) )
        {
	      echo "<INPUT TYPE='text' NAME='wref' size=10 VALUE='".$wref."' onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
     	  
       	  $query="SELECT Artnom,Vdeart FROM uvglobal_000016, uvglobal_000017, uvglobal_000001";
          $query=$query." WHERE Venffa = '".$wffa."' And Vennfa = '".$wfac."' And Vdenum = Vennum And Vdeart = Artcod";
          $query=$query." And Artcod = '".$wref."' And mid(Artgru,1,2) = 'MT';";
    	  $resultado = mysql_query($query) or die (mysql_errno().":".mysql_error()."<br>");
    	  $nroreg = mysql_num_rows($resultado);
    	  
    	  if ($nroreg > 0)   //Encontro
    	  {
		   $registro = mysql_fetch_row($resultado);  
           echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=#006699 size=2> ".$registro[0]."</font></b></td>";
          }
          else
          {
       	   echo "<td align=center bgcolor=#DDDDDD colspan=4>"; 
	       echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR NO EXISTE CODIGO DE MONTURA EN ESTA FACTURA !!!!</MARQUEE></font>";				
	       echo "</b></td>";	          
      	  } 
   		}   
    	else
    	{
		  echo "<INPUT TYPE='text' NAME='wref' size=10 onkeyup='this.value=this.value.toUpperCase();' onchange='enter();'></INPUT></td>";
		  echo "<td align=center bgcolor=#DDDDDD colspan=4>";
	    }

    //Defino un ComboBox con los vendedores 
    echo "<tr><td align=CENTER colspan=6 bgcolor=#DDDDDD><b><font text color=#003366 size=3>Vendedor de la Montura: </font></b>";
   
    if ($wedita=='disabled')
    {
    $query = "SELECT ordvem FROM uvglobal_000133 WHERE ordnro = $wnro ";
    $resultado = mysql_query($query);          // Ejecuto el query 
    $nroreg = mysql_num_rows($resultado);
    
    echo "<select name='wvem' ".$wedita." >";  //$wedita para saber si deja o no modificar el campo de vendedores disable ver if wproceso,
	echo "<option></option>";                  //primera opcion en blanco 	
    
	$Num_Filas = 0;
	while ( $Num_Filas < $nroreg )
	 {
	  $registro = mysql_fetch_row($resultado);
	  echo "<option selected>".$registro[0]."</option>";
	  $Num_Filas++;
  	 }   
     echo "</select></td></tr>";
    
    }
    else
    {
     $query = "SELECT Cjeusu,descripcion FROM uvglobal_000030,usuarios WHERE Cjeusu = codigo Order BY Cjeusu";
     $resultado = mysql_query($query);          // Ejecuto el query 
     $nroreg = mysql_num_rows($resultado);
    
	 echo "<select name='wvem' ".$wedita." >";  //$wedita para saber si deja o no modificar el campo de vendedores disable ver if wproceso,
	 echo "<option></option>";                  //primera opcion en blanco 	
    
	 $Num_Filas = 0;
	 while ( $Num_Filas < $nroreg )
	  {
		$registro = mysql_fetch_row($resultado);
  		if(substr($wvem,0,strpos($wvem,"-")) == $registro[0])
	      echo "<option selected>".$registro[0]."- ".$registro[1]."</option>";
	    else
	      echo "<option>".$registro[0]."- ".$registro[1]."</option>";
	    $Num_Filas++;		  
      }   
     echo "</select></td></tr>";	        	 
    }    
    
		echo "<tr><td bgcolor=#cccccc align=center colspan=2><b><font text color=#003366 size=2> <i>Material: </font></b><select name='wmet'>";	 
		if (!isset($wmet))   //No esta seteada 
		{   
	     echo "<option>";			
         echo "<option value='1'>Metal";
         echo "<option value='2'>Pasta";
         echo "<option value='3'>Otro";
        }
        else
        {
	     if ($wmet == "")
	       echo "<option selected>";    
	     else
	        echo "<option>";   
	     if ($wmet == "1")
	       echo "<option selected value='1'>Metal";    
	     else
	        echo "<option value='1'>Metal";
	     if ($wmet == "2")     
	       echo "<option selected value='2'>Pasta";
	     else
	       echo "<option value='2'>Pasta";  
	     if ($wmet == "3")     
	       echo "<option selected value='3'>Otro";
	     else
	       echo "<option value='3'>Otro";  
        }   
        echo "</select></td>";
	         
   		echo "<td bgcolor=#cccccc align=center colspan=2><b><font text color=#003366 size=2> <i>Diseño: </font></b><select name='wcom'>";	
   		if (!isset($wcom))   //No esta seteada     	
   		{
	   	 echo "<option>";		
         echo "<option value='1'>Completa";
         echo "<option value='2'>Sem AA";
         echo "<option value='3'>AA";
        }
        else
        {    
	     if ($wcom == "")
	       echo "<option selected>";    
	     else
	        echo "<option>";           
         if ($wcom == "1")
          echo "<option selected value='1'>Completa";
         else
          echo "<option value='1'>Completa"; 
         if ($wcom == "2") 
          echo "<option selected value='2'>Sem AA";
         else
           echo "<option value='2'>Sem AA";
         if ($wcom == "3") 
          echo "<option selected value='3'>AA";
         else
           echo "<option value='3'>AA";
        }   
        echo "</select></td>";
           
        echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Color :</font></b>";
        if (isset($wcol))
    	 echo "<INPUT TYPE='text' NAME='wcol' size=20 VALUE='".$wcol."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wcol' size=20></INPUT></td>"; 
    	 
    	echo "<tr>";
        echo "<td colspan=1 align=center><b>ESTADO<b></td>";
        echo "<td colspan=1 align=center><b>BUENO<b></td>";
        echo "<td colspan=1 align=center><b>MALO<b></td>";
        echo "<td colspan=3 align=center><b>DESCRIPCION<b></td>";
        echo "</tr>";      
        
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Pintura:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
	    if ((isset($wpin) and $wpin == "1") or !isset($wpin))
        {
	     echo "<td colspan=1 bgcolor=#cccccc align=center>";     	
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 1 CHECKED></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 2></INPUT>";        
	    } 
		else 
        {
   	     echo "<td colspan=1 bgcolor=#cccccc align=center>";     	
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 1></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpin' VALUE = 2 CHECKED></INPUT>";        
        }
	               
        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde1))
    	 echo "<INPUT TYPE='text' NAME='wde1' size=40 VALUE='".$wde1."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wde1' size=40></INPUT></td>"; 
    	 
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Brazos:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if ((isset($wbra) and $wbra == "1") or !isset($wbra))
        {
     	 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 1 CHECKED></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 2></INPUT>";        
	    }
	    else
	    {
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 1 ></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wbra' VALUE = 2 CHECKED></INPUT>";        
        }   
	          
        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde2))
    	 echo "<INPUT TYPE='text' NAME='wde2' size=40 VALUE='".$wde2."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wde2' size=40></INPUT></td>";     	 
    	 
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Terminales:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if ((isset($wter) and $wter == "1") or !isset($wter))
        {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 1 CHECKED></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 2></INPUT>";        
	    }
	    else
	    {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 1 ></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wter' VALUE = 2 CHECKED></INPUT>";        
	    }      
       
        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde3))
    	 echo "<INPUT TYPE='text' NAME='wde3' size=40 VALUE='".$wde3."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wde3' size=40></INPUT></td>";     	     	 

    	            	
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Plaquetas:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if ((isset($wpla) and $wpla == "1") or !isset($wpla))
        {
     	 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 1 CHECKED></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 2></INPUT>";        
        }
        else
        {
     	 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 1 ></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wpla' VALUE = 2 CHECKED></INPUT>";        
        } 
        
        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde4))
    	 echo "<INPUT TYPE='text' NAME='wde4' size=40 VALUE='".$wde4."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wde4' size=40></INPUT></td>";     	     	 
    	 
        echo "<tr>";
        echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2> <i>Otro:</font></b></td>";
        // para que se sostenga la seleccion hecha despues de un submit entonces:
        // (Si esta seteada y con valor uno) or si no esta seteada
        if ((isset($wotr) and $wotr == "1") or !isset($wotr))
        {
     	 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 1 CHECKED></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 2></INPUT>";      
	    }
	    else
	    {
         echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 1 ></INPUT>";
		 echo "<td colspan=1 bgcolor=#cccccc align=center>";
		 echo "<INPUT TYPE = 'Radio' NAME = 'wotr' VALUE = 2 CHECKED></INPUT>";        
        }
       
        echo "<td align=center bgcolor=#DDDDDD colspan=3>";
        if (isset($wde5))
    	 echo "<INPUT TYPE='text' NAME='wde5' size=40 VALUE='".$wde5."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wde5' size=40></INPUT></td>";    
    	 
		echo "<tr><td align=center bgcolor=#DDDDDD colspan=5><b><font text color=#003366 size=2> <i>Observaciones:</font></b><br>";
        if (isset($wobs))
    	 echo "<INPUT TYPE='text' NAME='wobs' size=100 VALUE='".$wobs."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wobs' size=100></INPUT></td>";     	  	     	 

		echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2> <i>Caja Nro:</font></b><br>";
        if (isset($wcaj))
    	 echo "<INPUT TYPE='text' NAME='wcaj' size=10 VALUE='".$wcaj."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wcaj' size=10></INPUT></td>";    
    	 

        echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fte-Factura: </font></b>";
        if (isset($wfac) )
        {
	       echo "<INPUT TYPE='text' NAME='wffa' size=5  VALUE='".$wffa."'></INPUT>";
   	       echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$wfac."'></INPUT></td>";
   	       
   	       //Segun la sede tomo el prefijo
   	       //$c=explode('-',$sede);    
   	       //$query = "Select Ccopve From uvglobal_000003 Where Ccocod ='".$c[0]."'";
	       //$resultado = mysql_query($query);
	       //$registro = mysql_fetch_row($resultado);  
	       //$prefijo = $registro[0];

	       $query = "Select fenval,fennpa From uvglobal_000018 Where Fendpa = '".$wdoc."' And fenfac ='".$wfac."' And fenffa ='".$wffa."'";
    	   $resultado = mysql_query($query);
    	   $nroreg = mysql_num_rows($resultado);
    	   if ($nroreg > 0)   //Encontro
    	   {
		    $registro = mysql_fetch_row($resultado);  
            echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=990000 size=2> VALOR: ".$registro[0]."<b>&nbsp;&nbsp;&nbsp;</b> USUARIO: ".$registro[1]."</font></b></td>";
           }
           else
           {
	           
       	    echo "<td align=center bgcolor=#DDDDDD colspan=4>"; 
	        echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>NO EXISTE NRO DE FACTURA PARA ESTE DOCUMENTO!!!!</MARQUEE></font>";				
	        echo "</b></td>";	          
      	   }

   		}   
    	else
    	{	      
	     if (isset($wdoc) )
	     {
           //Busco la ultima factura de este documento	          	     	       
     	   $query = "Select fenval,fennpa,fenfac,fenffa From uvglobal_000018 Where Fendpa = '".$wdoc."' Order by Fenfac Desc";
    	   $resultado = mysql_query($query);
    	   $nroreg = mysql_num_rows($resultado);
     	   if ($nroreg > 0)   //Encontro
    	   {
		    $registro = mysql_fetch_row($resultado);  
		    echo "<INPUT TYPE='text' NAME='wffa' size=5 VALUE='".$registro[3]."'></INPUT>";
		    echo "<INPUT TYPE='text' NAME='wfac' size=10 VALUE='".$registro[2]."'></INPUT></td>";
            echo "<td align=center bgcolor=#DDDDDD colspan=4><b><font text color=990000 size=2> VALOR: ".$registro[0]."<b>&nbsp;&nbsp;&nbsp;</b> USUARIO: ".$registro[1]."</font></b></td>";
     
           }
           else
           {   
       	    echo "<td align=center bgcolor=#DDDDDD colspan=4>"; 
	        echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>NO EXISTE NRO DE FACTURA PARA ESTE DOCUMENTO!!!!</MARQUEE></font>";				
	        echo "</b></td>";	          
      	   }

         }
	    }
 	
    	echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha de la orden:</font></b><br>";
    	if (isset($wfec))
    	 echo "<INPUT TYPE='text' NAME='wfec' size=10 color=#003366 VALUE='".$wfec."'></INPUT></td>";
    	else
    	{
	     $wfecha = date("Y-m-d");
    	 echo "<INPUT TYPE='text' NAME='wfec' size=10 color=#003366 VALUE='".$wfecha."'></INPUT></td>";
    	}         
         
        echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha de recepcion:</font></b><br>";
        if (isset($wfre))
    	 echo "<INPUT TYPE='text' NAME='wfre' size=10 VALUE='".$wfre."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wfre' size=10></INPUT></td>";   
    	 
        echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2> <i>Fecha de entrega:</font></b><br>";
        if (isset($wfen))
    	 echo "<INPUT TYPE='text' NAME='wfen' size=10 VALUE='".$wfen."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='text' NAME='wfen' size=10></INPUT></td>";  
    	  	       	 
  	
       // $wproceso es una variable escondida que enviaremos a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

    if ($wproceso != "Consultar")  //PARA QUE NO MUESTRE EL BOTON <GRABAR> NI EL "Checkbox" SI ESTOY CONSULTANDO
    {
      // Boton grabar y variable 'wdat' en un checkbox para indicar que los datos ya estan completos para validar	       	     	 
    	echo "<tr><td align=center colspan=6 bgcolor=#cccccc size=10>";
    	echo "<input type='submit' value='Grabar'>";
       
    	 if (isset($wdat))
    	 echo "<INPUT TYPE = 'Checkbox' NAME='wdat' VALUE='".$wdat."'></INPUT></td>";
    	else
    	 echo "<INPUT TYPE='Checkbox' NAME='wdat' size=10></INPUT></td>";
    }   
        	 
    	
		echo "</center></table>";			
		

if (isset($wnro) and isset($wfec) and isset($wdoc) and isset($wfac) and isset($wdat) )
///////////        CUANDO YA HAY DATOS DIGITADOS       ///////////////
{  	
	// invoco la funcion que valida los campos
	validar_datos($wfec,$wdoc,$wffa,$wfac,$wobs,$wfre,$wfen,$wled,$wlei,$wref);
	

	
	if ($todok) 
	{ 
	 if ($wproceso == "Nuevo")
	 {	
	  //Por si otro usuario utilizo el consecutivo inicial antes de grabar actualizo el nro de consecutivo 
	  // (Aunque con el submit que siempre hace creo que no se necesita)
	  $query = "Select carcon From uvglobal_000040 Where Carfue = 'OT' And Carest = 'on' And Carotr = 'on' ";
      $resultado = mysql_query($query);
      $nroreg = mysql_num_rows($resultado);
      $registro = mysql_fetch_row($resultado);  
      $wnro = $registro[0];
      
      $fecha = date("Y-m-d");
	  $hora = (string)date("H:i:s");
      $query1 = "INSERT INTO uvglobal_000133 (medico,fecha_data,hora_data,ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,"
      ."orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,"
      ."ordpin,ordde1,ordbra,ordde2,ordter,ordde3,ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,"
      ."ordvem,ordcco,seguridad) "
      ."VALUES ('uvglobal','".$fecha."','".$hora."',".$wnro.",'".$wdoc."','".$wran."','".$wtus."','".$wdsi."','".$wdes."',"
      ."'".$wdci."','".$wdej."','".$wdad."','".$wdte."','".$wisi."','".$wies."','".$wici."','".$wiej."','".$wiad."','".$wite."',"
      ."'".$wled."','".$wlei."','".$wedp."','".$wtra."','".$wbif."','".$wmon."','".$wref."','".$wmet."','".$wcom."','".$wcol."',"
      ."'".$wpin."','".$wde1."','".$wbra."','".$wde2."','".$wter."','".$wde3."','".$wpla."','".$wde4."','".$wotr."','".$wde5."',"
      ."'".$wobs."','".$wcaj."','".$wffa."','".$wfac."','".$wfec."','".$wfre."','".$wfen."','".$wvel."','".$wvem."','".$sede."','C-uvglobal')";

	  $resultado = mysql_query($query1,$conex) or die("ERROR AL GRABAR CODIGO: ".mysql_errno().": ".mysql_error());  //ADICIONO
	  if ($resultado)
	  {
	   // echo "<center>"; 	  
       // echo "Adicion Ok!<br>";
       // echo "</center>";
       
	   //Actualizo el nro de orden
	   $var1 = 1 + (integer)($wnro);
	   $query = "Update uvglobal_000040 SET Carcon = ". $var1." Where Carfue = 'OT' And Carest = 'on' And Carotr = 'on'";
   	   $resultado = mysql_query($query) or die("ERROR AL ACTUALIZAR NRO DE ORDEN CODIGO: ".mysql_errno().": ".mysql_error());  
   	   
   	   //Para que regrese a un script especifico
   	   echo "<script language='javascript'>";   	   
   	   echo "document.location.href = 'uvglobal00pru.php';";
   	   echo "</script>";
   	   
      } 
     } 
     else
     {
        if ( isset($wdat) )		
        {	   
	     //Modifico
	     $query1 = "UPDATE uvglobal_000133 SET "
	     ."orddoc='".$wdoc."',ordran='".$wran."',ordtus='".$wtus."',orddsi='".$wdsi."',orddes='".$wdes."',orddci='".$wdci."',orddej='".$wdej."',"
	     ."orddad='".$wdad."',orddte='".$wdte."',ordisi='".$wisi."',ordies='".$wies."',ordici='".$wici."',ordiej='".$wiej."',ordiad='".$wiad."',"
	     ."ordite='".$wite."',ordled='".$wled."',ordlei='".$wlei."',ordedp='".$wedp."',ordtra='".$wtra."',ordbif='".$wbif."',ordmon='".$wmon."',"
	     ."ordref='".$wref."',ordmet='".$wmet."',ordcom='".$wcom."',ordcol='".$wcol."',ordpin='".$wpin."',ordde1='".$wde1."',ordbra='".$wbra."',"
	     ."ordde2='".$wde2."',ordter='".$wter."',ordde3='".$wde3."',ordpla='".$wpla."',ordde4='".$wde4."',ordotr='".$wotr."',ordde5='".$wde5."',"
	     ."ordobs='".$wobs."',ordcaj='".$wcaj."',ordffa='".$wffa."',ordfac='".$wfac."',ordfec='".$wfec."',ordfre='".$wfre."',ordfen='".$wfen."',"
	     ."ordcco='".$sede."' WHERE ordnro = ".$wnro;
	     $resultado = mysql_query($query1,$conex) or die("ERROR AL MODIFICAR CODIGO: ".mysql_errno().": ".mysql_error());  //MODIFICO
	   
         echo "<center>";
	     echo "Modificacion Ok!<br>";
	     echo "</center>";
        }
     }
    }
      
	else
	{
     //Para controlar que no muestre este mensaje por el submit que se hace la primera vez al digitar 
     //la cedula o el documento por el <autoenter> Entonces coloco un campo adicional de "Datos completos"
     if ( isset($wdat) AND ($wproceso != "Consultar") )		
     {
	  echo "<center><table border=1>";	 
	  echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
	  echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR EN LOS DATOS DIGITADOS, MINIMO DEBE TENER OBSERVACIONES!!!!</MARQUEE></font>";				
	  echo "</td></tr></table></center>";
     } 
	}	  
	
}    // De los datos digitados   
 				
}    // De la sesion

if ($wproceso == "Consultar")
{
 echo "<center>";
 echo "<li><A HREF='uvglobal03.php'>Regresar</A>";  
 echo "</center>";
 echo "</form>";
}
 else
{ 
 echo "<center>";
 echo "<li><A HREF='uvglobal00pru.php'>Regresar</A>";  
 echo "</center>";
 echo "</form>";
}	
	
?>
</body>
</html>
