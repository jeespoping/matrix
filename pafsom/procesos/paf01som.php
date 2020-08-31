<html> 
<head>
<title>Actualizacion Autorizaciones Programa Inst Mujer Programa SOM</title>

	<style type="text/css">
		.tipodrop{color:#000000;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;width:80em;text-align:left;height:2em;}
 	</style>
 	
</head>

<script>
    function ira()
    {
	 document.paf01som.wced.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    

<script type="text/javascript">

	function enter()
	{
		document.forms.paf01som.submit();   // Ojo para la funcion paf01som <> paf01som  (sencible a mayusculas)
	}

	function vaciarCampos()
	{document.forms.paf01som.wfec.value = 'yyyy-mm-dd';
	 document.forms.paf01som.wced.value = '';
     document.forms.paf01som.wap1.value = '';
	 document.forms.paf01som.wap2.value = '';
     document.forms.paf01som.wnom.value = '';
     document.forms.paf01som.wtel.value = '';
     document.forms.paf01som.wran.value = '';	
     document.forms.paf01som.wtip.value = '';	
     document.forms.paf01som.wdia.value = '';	
     document.forms.paf01som.wexa.value = '';	 
	 document.forms.paf01som.wrem.value = '';
	 document.forms.paf01som.wcco.value = '';
	 document.forms.paf01som.west.value = '';
	 document.forms.paf01som.word.value = '';
	 document.forms.paf01som.wser.value = '';
	 document.forms.paf01som.wran.value = '';	
     document.forms.paf01som.wcr1.value = '';    
     document.forms.paf01som.wcr2.value = '';     
    }
    
 	// Fn que solo deja digitar los nros del 0 al 9, el . y el enter
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
 
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Actualizacion Autorizaciones Programa Inst Mujer SOM                                                                 
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Octubre 20 de 2015
//FECHA ULTIMA ACTUALIZACION  :Octubre 20 de 2015.                                                                             

$wactualiz="PROGRAMA: paf01som.php Ver. 2015-20-15   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

Function validar_datos($fec,$ced,$ape,$nom,$cco,$dia,$exa,$rem,$fre,$ord,$fem,$fci,$hor,$fca,$cau ) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
 
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   /* if ($fec < date("Y-m-d")) 
   {
     $todok = false;     
     $msgerr=$msgerr." Fecha de autorizacion debe ser posterior a la fecha actual. ";   
    } 
   */
   
   if (empty($ced))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir nro de documento. ";   
   }
                  
   if (empty($ape))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Apellidos no puede ser nulo.";   
   }   

   if (empty($nom))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Nombres no puede ser nulo.";   
   }   

   if (empty($dia))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Diagnostico no puede ser nulo.";   
   }  
      
   if (empty($exa))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Examen no puede ser nulo.";   
   }  
 
   if (empty($rem))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir medico que remite.";   
   }
   
   if (empty($cco))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Unidad que presta el servicio no puede ser nulo.";   
   }   
   
    // Chequeo la fecha Vigencia en clinica 
	if (empty($fre) or ($fre == '0000-00-00') )
    {
      $todok = false; 
      $msgerr=$msgerr." La Fecha de Vigencia no puede ser nula.";   
    }	
	else
     if ($fre < $fec) 	
     {
      $todok = false;     
      $msgerr=$msgerr." Fecha de Vigencia no puede ser menor a la fecha de esta autorizacion.";
     }		 

/*	 
    // Chequeo el nro de la Orden por Nueva EPS
 	if (empty($ord))
    {
      $todok = false; 
      $msgerr=$msgerr." El Nro de la Orden por Nueva EPS no puede ser nula.";   
    }	

	
    // Chequeo la fecha de emision de la orden  en NUEVA EPS
	if (empty($fem) or ($fem == '0000-00-00') )
    {
      $todok = false; 
      $msgerr=$msgerr." La Fecha de emision de la orden por Nueva EPS no puede ser nula.";   
    }
    else	
     if ($fem > $fec) 	
     {
      $todok = false;     
      $msgerr=$msgerr." Fecha de emision de Orden en Nueva EPS no puede ser mayor a la fecha de Vigencia. ";   
     }	
*/
	 
    // Chequeo la fecha de la cita   
    if  ( ($fci < $fre) and ($fci <> '0000-00-00') )
     {
      $todok = false;     
      $msgerr=$msgerr." Fecha de la cita no puede ser menor a la fecha de vigencia. ";   
     }	 

    // Chequeo la hora 
 	if ( empty($hor) and ($fci <> '0000-00-00')   )
    {
      $todok = false; 
      $msgerr=$msgerr." Debe existir hora de la cita con formato hh:mm am/pm.";   
    }
    else
	{	
      $pos = strpos($hor,":");
	  if ( ($pos == 0 )  and ($fci <> '0000-00-00') )
      {
       $todok = false; 
       $msgerr=$msgerr." El formato de la hora de la cita debe ser hh:mm am/pm.";   
      }		  
    }
	
	// Chequeo la fecha de cancelacion   
    if ( ($fca < $fci) and ($fca <> '0000-00-00')	)
     {
      $todok = false;     
      $msgerr=$msgerr." Fecha de cancelacion no puede ser menor a la fecha de la cita. ";   
     }	  

    if ( ($fca <> '0000-00-00') and ($fci <> '0000-00-00' ) )
     {
      $todok = false;     
      $msgerr=$msgerr." Si existe Fecha de cancelacion la fecha de la cita debe ser  0000-00-00  y la hora en blanco ";   
     }	  
	 
    // Chequeo la causa de cancelacion   
    if ( ($fca <> '0000-00-00') and empty($cau)	)
     {
      $todok = false;     
      $msgerr=$msgerr." Si existe Fecha de cancelacion debe seleccionar la cuasa. ";   
     }
     
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

echo "<form name='paf01som' action='paf01som.php' method=post>";  

// Almaceno el Id del registro enviado
$wid=trim($wid); 	

//$user = "01-07012";        // PARA PRUEBAS LOCALES

// Se obtiene el código del usuario
$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user)); 

$query = "SELECT surapri FROM pafsura_000099 Where surausr='".$wusuario."'";
$resultado = mysql_query($query);
$registro = mysql_fetch_row($resultado);  
	
if ($registro[0]=="2")
{
 $wmodifi=" Readonly ";   //Para Los campos Text
 $wmodif2=" disabled ";   //Para los campos Select y Button   
}
else
{
 $wmodifi=" ";
 $wmodif2=" "; 
}

echo "<center><table border=1>";
echo "<tr><td align=center colspan=2 bgcolor=#99CCCC><font size=3 text color=#FFFFFF><b>Actualizacion Autorizaciones Programa Inst mujer Programa SOM</b></font>";
if ($wproceso == "Modificar" )
 echo "<td align=center colspan=2 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Nro Orden: ".$wid."</b></font></tr>";
else
{
  $query = "SELECT Max(id) FROM pafsom_000001";
  $resultado = mysql_query($query);
  $registro = mysql_fetch_row($resultado);  	
  $nrorden=$registro[0];	
  echo "<td align=center colspan=2 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Nro Orden: ".(($nrorden) + 1) ."</b></font></tr>";  
}

if ($windicador == "PrimeraVez") 
{
 if ($wproceso == "Modificar" )  	
  {
  
   $query = "SELECT Paffec,Pafced,Pafape,Pafnom,Paftel,Pafran,Paftip,Pafdia,Pafexa,pafrem,Pafcco,Pafest,Pafobs,paford,pafser,paffre,paffem,paffci,pafhor,paffca,pafcau,paflla"
           ." FROM pafsom_000001 Where id = '".$wid."'";
		   
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wfec=$registro[0];
    $wced=$registro[1];
	$wape=$registro[2];
    $wnom=$registro[3];
    $wtel=$registro[4];
    $wran=$registro[5];
    $wtip=$registro[6];
    $wdia=$registro[7];
    $wexa=$registro[8];
	$wrem=$registro[9];
    $wcco=$registro[10];
	
    $wobs=$registro[12];
	$word=$registro[13];
    $wser=$registro[14];
    $wfre=$registro[15];
    $wfem=$registro[16];
	$wfci=$registro[17];
    $whor=$registro[18];
	$wfca=$registro[19];
	$wcau=$registro[20];
    $wlla=$registro[21];
   }
  } 
  else
  {   
    $query = "SELECT Afi_identific,Afi_apellido1,Afi_apellido2,Afi_nombres,Afi_telres, Afi_paf_codigo,Afi_tip_cotiza_ FROM pafsom_000002 Where Afi_identific = '".$wid."'";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)      //  Encontro 
    {
     $registro = mysql_fetch_row($resultado);  	   
     $wfec=date("Y-m-d");
     $wced=$registro[0];
     $wape=$registro[1]." ".$registro[2];
     $wnom=$registro[3];
     $wtel=$registro[4];
     
       if ($registro[5] == "1" )
        $wran="01";
       else
        if ($registro[5] == "2" )
         $wran="02";
        else
         $wran="03"; 

       if ($registro[6] == "COTIZANTE" )
        $wtip="01";
       else
        if ($registro[6] == "BENEFICIARIO" )
         $wtip="02";
        else
         $wtip="03"; 
    } 
   } 
}
  If ($wproceso == "Modificar" )     // Cuando esta modificando no dejo cambiar la fecha de autorizacion es clave
   $w=" disabled ";
  else 	  
   $w=$wmodif2;

  if (!isset($wfec))    // Si no esta seteada entonces la inicializo con la fecha actual, OJO si va a copiar este campo cambie el trigger1 por triggerx
    $wfec=date("Y-m-d");
    
    echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha de la autorizacion:<br>";
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' Readonly value=".$wfec." class=tipo3><button id='trigger1' ".$w." onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
   
    echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=3>Nro de Documento:</font></b><br>";
    if (isset($wced))
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 ".$wmodifi." VALUE='".$wced."' onkeypress='teclado()' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 ".$wmodifi." onkeypress='teclado()' ></INPUT></td>"; 
 
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2></font></b><br>";

    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Apellidos:</font></b><br>";
    if (isset($wape))
     echo "<INPUT TYPE='text' NAME='wape' size=50 maxlength=20 ".$wmodifi." VALUE='".$wape."' onKeyUp='form.wape.value=form.wape.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wape' size=50 maxlength=20 ".$wmodifi." onKeyUp='form.wape.value=form.wape.value.toUpperCase()'></INPUT></td>"; 
   
   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nombres:</font></b><br>";
    if (isset($wnom))
     echo "<INPUT TYPE='text' NAME='wnom' size=50 maxlength=25 ".$wmodifi." VALUE='".$wnom."' onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wnom' size=50 maxlength=25 ".$wmodifi." onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 

    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Telefonos:</font></b><br>";
    if (isset($wtel))
     echo "<INPUT TYPE='text' NAME='wtel' size=30 maxlength=40  ".$wmodifi." VALUE='".$wtel."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wtel' size=30 maxlength=40  ".$wmodifi." onkeypress='teclado()'></INPUT></td>"; 

   
    echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Rango<br></font></b>"; 
    echo "<select name='wran' >";
    if (isset($wran))
    {
	 $c1=explode('-',$wran);     // Del combo tomo el codigo   
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Rango I</option>";                
	 else
	  echo "<option>01- Rango I</option>";                         
	    
	 if($c1[0] == "02" ) 
	  echo "<option SELECTED>02- Rango II</option>";            
	 else                                                     
      echo "<option>02- Rango II</option>";                     

     if($c1[0] == "03" )                                       
      echo "<option SELECTED>03- Rango III</option>"; 
     else                                                      
      echo "<option>03- Rango III</option>";    

     if($c1[0] == "04" )      
      echo "<option SELECTED>04- Rango IV</option>";        
     else  
      echo "<option>04- Rango IV</option>";  
    }  
    else
	{		
	 echo "<option>01- Rango I   </option>";
	 echo "<option>02- Rango II  </option>";
	 echo "<option>03- Rango III </option>";
	 echo "<option>04- Rango IV  </option>";
	}	
	 echo "</select></td>";  	
	 
	echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Tipo de Afiliado<br></font></b>"; 
    echo "<select name='wtip' >";
    if (isset($wtip))
    {
	 $c1=explode('-',$wtip);     // Del combo tomo el codigo
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Cotizante</option>";          
	 else
	  echo "<option>01- Cotizante</option>";    
	    
	 if($c1[0] == "02" ) 
	  echo "<option SELECTED>02- Beneficiario</option>";            
	 else      
      echo "<option>02- Beneficiario</option>";  

     if($c1[0] == "03" )      
      echo "<option SELECTED>03- Adicional</option>"; 
     else   
      echo "<option>03- Adicional</option>";    
    }  
    else
	{		
	 echo "<option>01- Cotizante</option>";
	 echo "<option>02- Beneficiario</option>";
	 echo "<option>03- Adicional</option>";
	}	
	 echo "</select></td>";  	
/*****************************************************************************************************************************************************/
    echo "<tr><td align=center bgcolor=#E6E6FA colspan=1><b><font text color=#003366 size=2>Criterio para diagnostico:</font></b><br>";
    if (isset($wcr1))
     echo "<INPUT TYPE='text' NAME='wcr1' size=30 maxlength=15 ".$wmodifi." VALUE='".$wcr1."' onKeyUp='form.wcr1.value=form.wcr1.value.toUpperCase()' OnBlur='enter()' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcr1' size=30 maxlength=15 ".$wmodifi." onKeyUp='form.wcr1.value=form.wcr1.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 
	 
   echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Diagnostico:</font></b><br>";   
    
   if ($wproceso == "Modificar" and $windicador=="PrimeraVez")  	
    $query = "SELECT codigo,descripcion FROM root_000011 Where codigo='".$wdia."'";   
   else   
   {
    if ( isset($wcr1) and $wcr1!=""  ) 
     $query = "SELECT codigo,descripcion FROM root_000011 Where ( codigo like '%".$wcr1."%' or descripcion like '%".$wcr1."%' ) ORDER BY descripcion";   
    else
	{	
	 if ( isset($wdia) and $wdia!=""  ) 	
	 {
	  $c2=explode('-',$wdia); 	
      $query = "SELECT codigo,descripcion FROM root_000011 Where codigo like '".$c2[0]."'";
	 }
	 else
	  $query = "SELECT codigo,descripcion FROM root_000011 Where codigo='$&#$#'";
    }
   } 
    
   echo "<select name='wdia'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c2=explode('-',$wdia); 				  
  		if($c2[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }
    echo "</select></td>";
/****************************************************************************************************************************************************************/    
    echo "<tr><td align=center bgcolor=#E6E6FA colspan=1><b><font text color=#003366 size=2>Criterio para examen:</font></b><br>";
    if (isset($wcr2))
     echo "<INPUT TYPE='text' NAME='wcr2' size=30 maxlength=15 ".$wmodifi." VALUE='".$wcr2."' onKeyUp='form.wcr2.value=form.wcr2.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcr2' size=30 maxlength=15 ".$wmodifi." onKeyUp='form.wcr2.value=form.wcr2.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 

   echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Examen autorizado:</font></b><br>";   
   if ($wproceso == "Modificar" and $windicador=="PrimeraVez")  	
    $query = "SELECT codigo,nombre FROM root_000012 Where codigo='".$wexa."'";   
   else   
   {   
    if ( isset($wcr2) and $wcr2!=""  )
     $query = "SELECT codigo,nombre FROM root_000012 Where ( codigo like '%".$wcr2."%' or nombre like '%".$wcr2."%' ) ORDER BY nombre";
    else
	{   
     if ( isset($wexa) and $wexa!="" )
	 {	 
	  $c3=explode('-',$wexa); 		
      $query = "SELECT codigo,nombre FROM root_000012 Where codigo like '".$c3[0]."'";
	 } 
	 else   
      $query = "SELECT codigo,nombre FROM root_000012 Where codigo='$&#$#'";
    }
   } 

   echo "<select name='wexa' class='tipodrop'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c3=explode('-',$wexa); 				  
  	  if( trim($c3[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   

   /***********************************************************************************************************************/	
   echo "<tr><td align=center bgcolor=#E6E6FA colspan=1><b><font text color=#003366 size=2>Criterio para el medico:</font></b><br>";
    if (isset($wcr3))
     echo "<INPUT TYPE='text' NAME='wcr3' size=30 maxlength=15 ".$wmodifi." VALUE='".$wcr3."' onKeyUp='form.wcr3.value=form.wcr3.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcr3' size=30 maxlength=15 ".$wmodifi." onKeyUp='form.wcr3.value=form.wcr3.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 

  //Como en esta tabla un medico puede tener varios codigos agrupo por documento
   echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Medico:</font></b><br>";   
   if ($wproceso == "Modificar" and $windicador=="PrimeraVez")  	
    $query = "SELECT Meduma,Medno1,Medno2,Medap1,Medap2,Meddoc FROM movhos_000048 Where Meduma='".$wrem."' And Meduma <> '' Group by Meddoc Order By Medno1,Medap1 ";   
   else   
   {   
    if ( isset($wcr3) and $wcr3!=""  )
     $query = "SELECT Meduma,Medno1,Medno2,Medap1,Medap2,Meddoc FROM movhos_000048 Where Meduma <> '' And ( meduma like '%".$wcr3."%' or medno1 like '%".$wcr3."%' or medno2 like '%".$wcr3."%'  or medap1 like '%".$wcr3."%' or medap2 like '%".$wcr3."%') Group by Meddoc ORDER BY Medno1,Medap1";
    else
	{   
     if ( isset($wrem) and $wrem!="" )
	 {	 
	  $c3=explode('-',$wrem); 		
      $query = "SELECT Meduma,Medno1,Medno2,Medap1,Medap2,Meddoc FROM movhos_000048 Where Meduma like '".$c3[0]."'  And Meduma <> '' Group by Meddoc Order By Medno1,Medap1 ";
	 } 
	 else   
      $query = "SELECT Meduma,Medno1,Medno2,Medap1,Medap2,Meddoc FROM movhos_000048 Where Meduma='$&#$#'  And Meduma <> '' Group by Meddoc Order By Medno1,Medap1 ";
    }
   } 
  
   echo "<select name='wrem' class='tipodrop'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c4=explode('-',$wrem); 				  
  	  if( trim($c4[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   
	  
/****************************************************************************************************************************************************************/       
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Unidad que presta el servicio:</font></b><br>"; 
   
   $query = "SELECT suracco FROM pafsura_000099 Where surausr='".$wusuario."'";
   $resultado = mysql_query($query);
   $registro = mysql_fetch_row($resultado); 
   // Para que no me cambien en las unidades este campo, Como no me funciono el 'Disabled' en los campos SELECT porque en el submid me lo borra entonces
   // lo hago segun el cco del usuario en la tabla 99 de cco y prioridad
   
   $wmostrar=$registro[0];
   
   $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccoclas = 'PR' AND ccoest = 'on' AND ccocod LIKE '".$registro[0]."' ORDER BY cconom";   
   echo "<select name='wcco' >"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c3=explode('-',$wcco); 				  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";     
/***********************************************************************************************************************************/	
    echo "<td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Tipo de Servicio<br></font></b>"; 
    echo "<select name='wser' >";
    if (isset($wser))
    {
	 $c1=explode('-',$wser);     // Del combo tomo el codigo
	 if($c1[0] == "1" ) 
	  echo "<option SELECTED>1- Ambulatorio</option>";          
	 else
	  echo "<option>1- Ambulatorio</option>";    
	    
	 if($c1[0] == "2" ) 
	  echo "<option SELECTED>2- Hospitalario</option>";            
	 else      
      echo "<option>2- Hospitalario</option>";  

     if($c1[0] == "3" )      
      echo "<option SELECTED>3- Urgencias</option>"; 
     else   
      echo "<option>3- Urgencias</option>";    
  
     if($c1[0] == "4" )      
      echo "<option SELECTED>4- Prioritario</option>"; 
     else   
      echo "<option>4- Prioritario</option>";    
    }  
    else
	{		
	 echo "<option>1- Ambulatorio</option>";
	 echo "<option>2- Hospitalario</option>";
	 echo "<option>3- Urgencias</option>";
	 echo "<option>4- Prioritario</option>";
	}	
	 echo "</select></td>";  	

  
    echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Observaciones:</font></b><br>";
    if (isset($wobs))
     echo "<TEXTAREA NAME='wobs' COLS='60' ROWS='3'>".$wobs."</TEXTAREA></td>"; 
    else
     echo "<TEXTAREA NAME='wobs' COLS='60' ROWS='3'></TEXTAREA></td>"; 
 
    if (!isset($wfre))   // Si no esta seteada entonces la inicializo 
     $wfre="0000-00-00";
    
    echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha de Vigencia de la Orden:<br>";
   	$cal="calendario('wfre','1')";
	echo "<input type='TEXT' name='wfre' size=10 maxlength=10  id='wfre' readonly='readonly' value=".$wfre." class=tipo3><button id='trigger2' ".$wmodif2." onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfre',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

	echo "<td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=3>Nro de orden Entidad: </font>";
    if (isset($word))
     echo "<INPUT TYPE='text' NAME='word' size=30 maxlength=50 ".$wmodifi." VALUE='".$word."' onkeypress='teclado()' ></INPUT>"; 
    else
     echo "<INPUT TYPE='text' NAME='word' size=30 maxlength=50 ".$wmodifi." onkeypress='teclado()' ></INPUT>"; 
	
    if (!isset($wfem))   // Si no esta seteada entonces la inicializo 
     $wfem="0000-00-00";
	
	echo "<br><font text color=#003366 size=3>Fecha emision de la Orden: ";
   	$cal="calendario('wfem','1')";
	echo "<input type='TEXT' name='wfem' size=10 maxlength=10  id='wfem' readonly='readonly' value=".$wfem." class=tipo3><button id='trigger3' ".$wmodif2." onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfem',button:'trigger3',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php	

    if (!isset($wfci))   // Si no esta seteada entonces la inicializo 
     $wfci="0000-00-00";
	
	echo "<td bgcolor=#C0C0C0 align=center colspan=1><font text color=#FF0000 size=3>Fecha de asignacion cita: &nbsp;&nbsp;&nbsp;&nbsp; Hora (hh:mm am/pm )";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>";
   	$cal="calendario('wfci','1')";
	echo "<input type='TEXT' name='wfci' size=10 maxlength=10  id='wfci' class=tipo3 value=".$wfci."><button id='trigger4' onclick=".$cal.">...</button>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfci',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php	
	
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if (isset($whor))
     echo "<INPUT TYPE='text' NAME='whor' size=8 maxlength=8  VALUE='".$whor."' onKeyUp='form.whor.value=form.whor.value.toUpperCase()' ></INPUT>"; 
    else
     echo "<INPUT TYPE='text' NAME='whor' size=8 maxlength=8  onKeyUp='form.wcr2.value=form.wcr2.value.toUpperCase()' ></INPUT>"; 

    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if ( $wlla == "on" )    
     echo "<INPUT TYPE = 'checkbox' NAME = 'wlla' CHECKED>Mas de 3 llamadas<br><font text color=#d100b5 size=4></INPUT></td>";
    else 
     echo "<INPUT TYPE = 'checkbox' NAME = 'wlla' >Mas de 3 llamadas<br><font text color=#d100b5 size=4></INPUT></td>";
   	
       
    if (!isset($wfca))   // Si no esta seteada entonces la inicializo 
     $wfca="0000-00-00";
	
	echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha de cancelacion cita:<br>";
   	$cal="calendario('wfca','1')";
	echo "<input type='TEXT' name='wfca' size=10 maxlength=10  id='wfca' class=tipo3 value=".$wfca."><button id='trigger5' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfca',button:'trigger5',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php	
	
	/***************************************************************************************************/	 
	 $a=array(1=>"1- Por decision medica",2=>"2- Identificacion equivocada",3=>"3- Inasistencia del usuario",4=>"4- Orden duplicada",5=>"5- Suspencion del Tra/to",6=>"6- Otras"); 
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Causa de cancelacion:</font></b><br>";
	 echo "<select name='wcau' >";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wcau)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wcau == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
	 }	
	 else          //no seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
     }
	 echo "</select></td>";  
	 
	 
    // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

	   if (isset($wid))
	     echo "<INPUT TYPE = 'hidden' NAME='wid' VALUE='".$wid."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wid'></INPUT>"; 
	                    
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
	
   	echo "<td align=center colspan=3 bgcolor=#E6E6FA>";
   	if ( $wmostrar <> "%%")	
	{	
	 echo "<input type='submit' value='Grabar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	 echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	 echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	}
   	if ($wproceso == "Modificar" )  	
      echo "<A HREF='paf09som.php?wid=".$wid."' TARGET='_blank' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;imprimir</A>";	     

   	echo "</td>";	


if ( $conf == "on" and isset($wape) and $wape<>'' and isset($wexa) and $wexa<>'' and isset($wcco) and $wcco<>'' and $windicador <> "PrimeraVez" )   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////

  // invoco la funcion que valida los campos 
  validar_datos($wfec,$wced,$wape,$wnom,$wcco,$wdia,$wexa,$wrem,$wfre,$word,$wfem,$wfci,$whor,$wfca,$wcau); 
  
  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	 
    if (isset($wran)) 
     $c1=explode('-',$wran);     // De los combos tradicionales tomo los codigos, el combo de causas de  cancelacion NO viene directo
    if (isset($wtip)) 
     $c2=explode('-',$wtip);     
    if (isset($wdia)) 
     $c3=explode('-',$wdia);     
    if (isset($wexa)) 
     $c4=explode('-',$wexa);     
    if (isset($wcco)) 
     $c5=explode('-',$wcco);     
    if (isset($wser)) 
     $c6=explode('-',$wser);
 
    if (isset($wrem)) 
     $c7=explode('-',$wrem); 
     
     
   // Busco la fecha del ultimo examen igual al solicitado si es menor a 90 dias mensaje informativo
     $query = "SELECT paffec,Id FROM pafsom_000001 Where pafced = '".$wced."' And pafexa='".$c4[0]."' Order by paffec DESC";
     $resultadoB = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultadoB);
     if (( $nroreg > 0 ) and ($wproceso == "Nuevo"))              //Encontro y esta adicionando una orden nueva
     { $registroB = mysql_fetch_row($resultadoB);
       $ultfec=$registroB[0];    
       $wfec90= date("Y-m-d", strtotime("$ultfec +90 day"));
       if ( $wfec < $wfec90 ) 
         echo "<font size=3 text color=#0000FF>ATENCION!!! tiene un examen igual antes de 90 dias en la orden Nro ".$registroB[1];   
     }  
     
     $query = "SELECT * FROM pafsom_000001 Where paffec = '".$wfec."' And pafced = '".$wced."' And pafexa='".$c4[0]."'";     // And paford = '".$word."'";
     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
	   $fecha = date("Y-m-d");
	   $hora = (string)date("H:i:s");
       $query = "Update pafsom_000001 SET pafape='".$wape."',pafnom='".$wnom."',paftel='".$wtel."',pafran='".$c1[0]."'"
               .",paftip='".$c2[0]."',pafdia='".$c3[0]."',pafrem='".$c7[0]."',pafcco='".$c5[0]."',pafest='A', pafobs='".$wobs."'"
			   .",pafser='".$c6[0]."',paffre='".$wfre."',paffem='".$wfem."',paffci='".$wfci."',pafhor='".$whor."',paffca='".$wfca."'"
			   .",pafcau='".$wcau."',paflla='".$wlla."',Fecha_data='".$fecha."',Hora_data='".$hora."' "     
               ." Where paffec='".$wfec."' And pafced='".$wced."' And pafexa='".$c4[0]."' And paford='".$word."'";
                                                                
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	   {
	    echo "<br><tr><td bgcolor=#33FFFF colspan=3 align=center>Registro Modificado</td></tr>";
       // echo "<script language='javascript'>alert('Registro Modificado');</script>"; 
        //echo "<script>alert('Registro Modificado');</script>";
       }  
	  else
	   {
	    echo "<table border=1>";	 
	    echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	    echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL MODIFICAR DATOS!!!!</MARQUEE></font>";				
	    echo "</td></tr></table><br><br>";
	   }
	  
	  /*
  	    echo "<table border=1>";	 
	    echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	    echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ATENCION!!!! LAS ORDENES NO SE PUEDEN MODIFICAR</MARQUEE></font>";				
	    echo "</td></tr></table><br><br>";
	  */
	   
     } 
     else
     {
  
      $fecha = date("Y-m-d");
	  $hora = (string)date("H:i:s");		      
      $query = "INSERT INTO pafsom_000001 (Medico,Fecha_data,Hora_data,Paffec,Pafced,Pafape,Pafnom,Paftel,Pafran,Paftip,Pafdia,Pafexa,pafrem,"
	          ."Pafcco,Pafest,Pafobs,paford,pafser,paffre,paffem,paffci,pafhor,paffca,pafcau,paflla,Seguridad,id)"
	          ." VALUES ('pafsom','".$fecha."','".$hora."','".$wfec."','".$wced."','".$wape."','".$wnom."','".$wtel."','".$c1[0]."','".$c2[0]
              ."','".$c3[0]."','".$c4[0]."','".$c7[0]."','".$c5[0]."','A','".$wobs."','".$word."','".$c6[0]."','".$wfre."','".$wfem
              ."','".$wfci."','".$whor."','".$wfca."','".$wcau."','".$wlla."','".$user."','')";			  
        
               $resultado = mysql_query($query,$conex);  
	  if ($resultado)
	   {
			echo "<br><tr><td bgcolor=#33FFFF colspan=3 align=center>Registro Adicionado";
		   
			//Despues de adicionado permito imprimir
			$query = "SELECT Id FROM pafsom_000001 Where paffec = '".$wfec."' And pafced = '".$wced."' And pafexa='".$c4[0]."'";     // And paford = '".$word."'";
			$resultadoB = mysql_query($query,$conex);
			$nroreg = mysql_num_rows($resultadoB);
			if ( $nroreg > 0 )                   //Encontro
			{
			 $registroB = mysql_fetch_row($resultadoB);
			 $ultid=$registroB[0];
			 echo "<td align=center colspan=1 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Nro Orden: ".$registroB[0]."</td></b></font>";  
			 echo "<td align=center colspan=1 bgcolor=#99CCCC><A HREF='paf09som.php?wid=".$ultid."' TARGET='_blank' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;imprimir</A></td></tr>";	
			} 
	 
       }
	  else
	  {
	   echo "<table border=1>";	 
	   echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	   echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL ADICIONAR DATOS, POSIBLEMENTE YA TIENE LA AUTORIZACION PARA ESTA FECHA!!!!</MARQUEE></font>";				
	   echo "</td></tr></table><br><br>";
	  }  	   
     }
     
    } 
   }
   else
   {
	if ($windicador <> "PrimeraVez") 	     
	{
     echo "<table border=1>";	 
     echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
     echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!!</MARQUEE></font>";				
     echo "</td></tr></table><br><br>";
    }
         
   } 
   echo "</center></table>";  
  
}

if ($windicador == "PrimeraVez" ) 	
{
   //Si no controlo con esta variable siempre que le den editar en el programa que lo llama este entra modificando   
   $windicador = "SegundaVez";
   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
}
echo "</Form>";	    
?>
</BODY>
</html>
