<html>
<head>
<title>Datos comerciales del proveedor</title>
</head>

<script>
    function ira()
    {
	 document.cotizaciones04.wmar.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.cotizaciones04.submit();   // Ojo para la funcion cotizaciones04 <> cotizaciones04  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.cotizaciones04.wbpm.value = '';
	 document.forms.cotizaciones04.wbpmf.value = '0000-00-00';
	 document.forms.cotizaciones04.wcca.value = '';
	 document.forms.cotizaciones04.wccaf.value = '0000-00-00';
	 document.forms.cotizaciones04.wbpa.value = '';
	 document.forms.cotizaciones04.wbpaf.value = '0000-00-00';
	 document.forms.cotizaciones04.wsgc.value = '';
     document.forms.cotizaciones04.wdf1.value = '0';
     document.forms.cotizaciones04.wdi1.value = '0';
     document.forms.cotizaciones04.wdf2.value = '0';
     document.forms.cotizaciones04.wdi2.value = '0';
     document.forms.cotizaciones04.wdf3.value = '0';     
     document.forms.cotizaciones04.wdi3.value = '0';
     document.forms.cotizaciones04.wpla.value = '0';
     document.forms.cotizaciones04.wobs.value = 'Ninguna';          
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
//PROGRAMA				      :Actualiza informacion comercial del proveedor.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Diciembre 11 DE 2009
//FECHA ULTIMA ACTUALIZACION  :Diciembre 11 DE 2009.                                                                             

$wactualiz="PROGRAMA: cotizaciones04.php Ver. 2017-10-03   JairS - AngelaO";


session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");



Function validar_datos($bpm,$bpmf,$cca,$ccaf,$bpa,$bpaf,$sgc,$df1,$di1,$df2,$di2,$df3,$di3,$pla,$obs) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($bpm))
   {
      $todok = false;
      $msgerr=$msgerr." Buenas Practicas de Manufactura no puede ser nulo.<br>";
   }
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ($bpm == 'S')
   {
	   if ( !checkdate(substr($bpmf,5,2), substr($bpmf,8,2), substr($bpmf,0,4)) )
	   {
		 $todok = false;     
		 $msgerr=$msgerr." Fecha debe ser aaaa-mm-dd. (BPM)<br>";   
	   }
   }
   if (empty($cca))
   {
      $todok = false;
      $msgerr=$msgerr." Certificado de Capacidad de Almacenamiento y/o Acondicionamiento no puede ser nulo.<br>";
   }
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ($cca == 'S')
   {
	   if ( !checkdate(substr($ccaf,5,2), substr($ccaf,8,2), substr($ccaf,0,4)) )
	   {
		 $todok = false;     
		 $msgerr=$msgerr." Fecha debe ser aaaa-mm-dd. (CCAA)<br>";   
	   }
   }
   
   if (empty($bpa))
   {
      $todok = false;
      $msgerr=$msgerr." Buenas Practicas de Almacenamiento no puede ser nulo.<br>";
   }
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ($bpa == 'S')
   {   
	   if ( !checkdate(substr($bpaf,5,2), substr($bpaf,8,2), substr($bpaf,0,4)) )
	   {
		 $todok = false;     
		 $msgerr=$msgerr." Fecha debe ser aaaa-mm-dd. (BPA)<br>";   
	   }
   }
   if (empty($sgc))
   {
      $todok = false; 
      $msgerr=$msgerr." Sistema General de la Calidad no puede ser nulo.<br>";
   }                                    
     
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  

//$user="1-07013";         



$conexN = odbc_connect('inventarios','','') or die("No se realizo Conexion con la BD suministros en Informix");
mysql_select_db("matrix") or die("No se selecciono la base de datos");    

echo "<form name='cotizaciones04' action='cotizaciones04.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=6 bgcolor=#005588><font size=4 text color=#FFFFFF><b>INFORMACION FINANCIERA DEL PROVEEDOR</b></font></tr>";
echo "<tr><td align=center colspan=6 bgcolor=#005588><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";


echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=4 text color=#000000><b>DATOS GENERALES DEL PROVEEDOR</b></font></tr>";
   $query = "SELECT usunit FROM cotizaci_000005 Where usucod ='".substr($user,2,7)."'";   

   $resultado = mysql_query($query); 
   $nroreg = mysql_num_rows($resultado);
   if ( $nroreg > 0 )
	{	 
	 $registro = mysql_fetch_row($resultado); 	
	 $query2 = "SELECT procod,pronom FROM cppro WHERE procod = '".$registro[0]."'";	 
	 
     $resultado2 = odbc_do($conexN,$query2);            
     $wcod = odbc_result($resultado2,1);
     $wnom = odbc_result($resultado2,2);   
    }

   echo "<tr><td colspan=2 align=left bgcolor=#C3D5F7><font size=4><b>Codigo: ".$wcod."<b></td>";
   echo "<td colspan=4 align=left bgcolor=#C3D5F7><b>Nombre: ".$wnom."<b></td>";
   echo "<tr>";	

if ($windicador == "PrimeraVez") 
{
		
   $query = "SELECT prvbpm,prvbpmf,prvcca,prvccaf,prvbpa,prvbpaf,prvsgc,prvdf1,prvdi1,prvdf2,prvdi2,prvdf3,prvdi3,prvpla,prvobs FROM cotizaci_000006"
          ." Where prvcod = '".$wcod."'";
        
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //    Ya tiene registro => Va a modificar
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wbpm=$registro[0];
	$wbpmf=$registro[1];
	$wcca=$registro[2];
	$wccaf=$registro[3];
	$wbpa=$registro[4];
	$wbpaf=$registro[5];
	$wsgc=$registro[6];
    $wdf1=$registro[7];
    $wdi1=$registro[8];
    $wdf2=$registro[9];
    $wdi2=$registro[10];
    $wdf3=$registro[11];
    $wdi3=$registro[12];
    $wpla=$registro[13];
    $wobs=$registro[14];

   } 
   
} 

echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=4
 text color=#000000><b>PARAMETROS DE CALIDAD DEL PROVEEDOR</b></font></tr>";


	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	     echo "<tr><td bgcolor=#b3bbd6 align=center colspan=4><b><font text color=#000000 size=3> <i>Posee Buenas Prácticas de Manufactura (BPM):<br> </font></b><select name='wbpm'>";//#003366
	     if (!isset($wbpm)) //No esta seteada
	     {
		  echo "<option>";
		  echo "<option value='S'>Si";
		  echo "<option value='N'>No";

	     } 
	     else              //Ya esta seteada
	     {    
		     
		 if  ($wbpm == "") 
       	  echo "<option selected >";
       	 else
          echo "<option>";
       	      
	     if ($wbpm == "S")
          echo "<option selected value='S'>Si";
         else
          echo "<option value='S'>Si";
          
   	     if ($wbpm == "N")
          echo "<option selected value='N'>No";
         else
          echo "<option value='N'>No";
          
         } 
         echo "</select></td>";      

		echo "<td align=center bgcolor=#b3bbd6 colspan=2><b><font text color=#000000 size=2>Fecha de Vencimiento de las BPM (aaaa-mm-dd):</font></b><br>";
		if (isset($wbpmf))
		 echo "<INPUT TYPE='text' NAME='wbpmf' size=15 maxlength=10 VALUE='".$wbpmf."'></INPUT></td>"; 
		else
		 echo "<INPUT TYPE='text' NAME='wbpmf' size=15 maxlength=10></INPUT></td>";  

		 echo "<tr><td bgcolor=#b3bbd6 align=center colspan=4><b><font text color=#000000 size=3> <i>Posee Certificado de Capacidad <br>de Almacenamiento y/o Acondicionamiento (CCAA):<br> </font></b><select name='wcca'>";
	     if (!isset($wcca)) //No esta seteada
	     {
		  echo "<option>";
		  echo "<option value='S'>Si";
		  echo "<option value='N'>No";

	     } 
	     else              //Ya esta seteada
	     {    
		     
		 if  ($wcca == "") 
       	  echo "<option selected >";
       	 else
          echo "<option>";
       	      
	     if ($wcca == "S")
          echo "<option selected value='S'>Si";
         else
          echo "<option value='S'>Si";
          
   	     if ($wcca == "N")
          echo "<option selected value='N'>No";
         else
          echo "<option value='N'>No";
          
         } 
         echo "</select></td>";  
		 
		 echo "<td align=center bgcolor=#b3bbd6 colspan=2><b><font text color=#000000 size=2>Fecha de Vencimiento del CCAA (aaaa-mm-dd):</font></b><br>";
		if (isset($wccaf))
		 echo "<INPUT TYPE='text' NAME='wccaf' size=15 maxlength=10 VALUE='".$wccaf."'></INPUT></td>"; 
		else
		 echo "<INPUT TYPE='text' NAME='wccaf' size=15 maxlength=10></INPUT></td>";  
		 
		 echo "<tr><td bgcolor=#b3bbd6 align=center colspan=4><b><font text color=#000000 size=3> <i>Posee Buenas prácticas de Almacenamiento(BPA):<br> </font></b><select name='wbpa'>";
	     if (!isset($wbpa)) //No esta seteada
	     {
		  echo "<option>";
		  echo "<option value='S'>Si";
		  echo "<option value='N'>No";

	     } 
	     else              //Ya esta seteada
	     {    
		     
		 if  ($wbpa == "") 
       	  echo "<option selected >";
       	 else
          echo "<option>";
       	      
	     if ($wbpa == "S")
          echo "<option selected value='S'>Si";
         else
          echo "<option value='S'>Si";
          
   	     if ($wbpa == "N")
          echo "<option selected value='N'>No";
         else
          echo "<option value='N'>No";
          
         } 
         echo "</select></td>";  

		 echo "<td align=center bgcolor=#b3bbd6 colspan=2><b><font text color=#000000 size=2>Fecha de Vencimiento de las BPA (aaaa-mm-dd):</font></b><br>";
		if (isset($wbpaf))
		 echo "<INPUT TYPE='text' NAME='wbpaf' size=15 maxlength=10 VALUE='".$wbpaf."'></INPUT></td>"; 
		else
		 echo "<INPUT TYPE='text' NAME='wbpaf' size=15 maxlength=10></INPUT></td>"; 
	 
	     //Los if siguientes se hacen para que se sostenga el valor seleccionado despues de un submit
	     //Hay que hacer un proceso cuando todavia no esta seteado y cuando ya esta seteada
	     echo "<tr><td bgcolor=#b3bbd6 align=center colspan=6><b><font text color=#000000 size=3><i>Certificacion: </font></b><select name='wsgc'>";
	     if (!isset($wsgc)) //No esta seteada
	     {
		  echo "<option>";
		  echo "<option value='01'>Iso";
		  echo "<option value='02'>Icontec";
		  echo "<option value='03'>Otra";
		  echo "<option value='04'>Sin certificacion";		  
	     } 
	     else              //Ya esta seteada
	     {    
		     
		 if  ($wsgc == "") 
       	  echo "<option selected >";
       	 else
          echo "<option>";
       	      
	     if ($wsgc == "01")
          echo "<option selected value='01'>Iso";
         else
          echo "<option value='01'>Iso";
          
   	     if ($wsgc == "02")
          echo "<option selected value='02'>Icontec";
         else
          echo "<option value='02'>Icontec";
          
   	     if ($wsgc == "03")
          echo "<option selected value='03'>Otra";
         else
          echo "<option value='03'>Otra";          

   	     if ($wsgc == "04")
          echo "<option selected value='04'>Sin certificacion";
         else
          echo "<option value='04'>Sin certificacion";                                    
         } 
         echo "</select></td>";         

echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=4 text color=#000000><b>PARAMETROS COMERCIALES</b></font></tr>";
    
         
    echo "<tr><td align=center bgcolor=#b3bbd6 colspan=1><b><font text color=#000000 size=3>% de descuento 1</font></b><br>";
    if (isset($wdf1))
     echo "<INPUT TYPE='text' NAME='wdf1' size=15 maxlength=2 VALUE='".$wdf1."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdf1' size=15 maxlength=2 onkeypress='teclado()'></INPUT></td>";        
     
    echo "<td align=center bgcolor=#b3bbd6 colspan=2><b><font text color=#000000 size=3>Dias 1er descuento</font></b><br>";
    if (isset($wdi1))
     echo "<INPUT TYPE='text' NAME='wdi1' size=15 maxlength=3 VALUE='".$wdi1."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdi1' size=15 maxlength=3 onkeypress='teclado()'></INPUT></td>";          

    echo "<td align=center bgcolor=#b3bbd6 colspan=1><b><font text color=#000000 size=3>% de descuento 2</font></b><br>";
    if (isset($wdf2))
     echo "<INPUT TYPE='text' NAME='wdf2' size=15 maxlength=2 VALUE='".$wdf2."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdf2' size=15 maxlength=2 onkeypress='teclado()'></INPUT></td>";        
     
    echo "<td align=center bgcolor=#b3bbd6 colspan=2><b><font text color=#000000 size=3>Dias 2do descuento</font></b><br>";
    if (isset($wdi2))
     echo "<INPUT TYPE='text' NAME='wdi2' size=15 maxlength=3 VALUE='".$wdi2."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdi2' size=15 maxlength=3 onkeypress='teclado()'></INPUT></td>";   
     
    echo "<tr><td align=center bgcolor=#b3bbd6 colspan=1><b><font text color=#000000 size=3>% de descuento 3</font></b><br>";
    if (isset($wdf3))
     echo "<INPUT TYPE='text' NAME='wdf3' size=15 maxlength=2 VALUE='".$wdf3."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdf3' size=15 maxlength=2 onkeypress='teclado()'></INPUT></td>";        
     
    echo "<td align=center bgcolor=#b3bbd6 colspan=1><b><font text color=#000000 size=3>Dias 3er descuento</font></b><br>";
    if (isset($wdi3))
     echo "<INPUT TYPE='text' NAME='wdi3' size=15 maxlength=3 VALUE='".$wdi3."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdi3' size=15 maxlength=3 onkeypress='teclado()'></INPUT></td>";          

    echo "<td align=center bgcolor=#b3bbd6 colspan=4><b><font text color=#000000 size=3>Plazo en dias</font></b><br>";
    if (isset($wpla))
     echo "<INPUT TYPE='text' NAME='wpla' size=15 maxlength=3 VALUE='".$wpla."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wpla' size=15 maxlength=3 onkeypress='teclado()'></INPUT></td>";          

    echo "<tr><td align=center bgcolor=#b3bbd6 colspan=6><b><font text color=#000000 size=3>Observaciones:</font></b><br>";
    if (isset($wobs))
     echo "<INPUT TYPE='text' NAME='wobs' size=60 maxlength=60 VALUE='".$wobs."' onKeyUp='form.wobs.value=form.wobs.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wobs' size=80 maxlength=80 onKeyUp='form.wobs.value=form.wobs.value.toUpperCase()'></INPUT></td>";                   
     
    // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";     
	     
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
     
   	echo "<tr><td align=center colspan=6 bgcolor=#b3bbd6>";
   	echo "<input type='submit' value='Grabar'>";          
   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	echo "<tr><td align=center colspan=6 bgcolor=#b3bbd6>";
   	echo "</td></tr>";	
	
//if ( isset($wbpm) and $wbpm<>'' and isset($wsgc) and $wsgc<>'')   
//---if ( isset($wsgc) and $wsgc<>'')
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wbpm,$wbpmf,$wcca,$wccaf,$wbpa,$wbpaf,$wsgc,$wdf1,$wdi1,$wdf2,$wdi2,$wdf3,$wdi3,$wpla,$wobs);
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	     
   
     $query = "SELECT prvcod FROM cotizaci_000006 Where prvcod = '".$wcod."'";

     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
	 	 
       $query = "Update cotizaci_000006 SET prvbpm='".$wbpm."',prvbpmf='".$wbpmf."',prvcca='".$wcca."',prvccaf='".$wccaf."',prvbpa='".$wbpa."',prvbpaf='".$wbpaf."',prvsgc='".$wsgc."',prvdf1=".$wdf1.",prvdi1=".$wdi1
               .",prvdf2=".$wdf2.",prvdi2=".$wdi2.",prvdf3=".$wdf3.",prvdi3=".$wdi3.",prvpla=".$wpla.",prvobs='".$wobs."'"
               ." Where prvcod = '".$wcod."'";
              
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";
	   else
	   {
	    echo "<table border=1>";	 
	    echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
	    echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, AL MODIFICAR DATOS!!!!</MARQUEE></font>";				
	    echo "</td></tr></table><br><br>";
	   }        
	   
     } 
     else
     {
	 
      $fecha = date("Y-m-d");
	  $hora = (string)date("H:i:s");		      
      $query = "INSERT INTO cotizaci_000006 VALUES ('cotizaci','".$fecha."','".$hora."','".$wcod."','".$wbpm."','".$wbpmf."','".$wcca."','".$wccaf."','".$wbpa."','".$wbpaf."','".$wsgc."',".$wdf1
              .",".$wdi1.",".$wdf2.",".$wdi2.",".$wdf3.",".$wdi3.",".$wpla.",'".$wobs."','C-cotizaci','')";  

	  $resultado = mysql_query($query,$conex);  
	  if ($resultado)
	   echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Adicionado</td></tr>";
	  else
	  {
	   echo "<table border=1>";	 
	   echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
	   echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, AL ADICIONAR DATOS!!!!</MARQUEE></font>";				
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
     echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
     echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!!</MARQUEE></font>";				
     echo "</td></tr></table><br><br>";
    }
         
   } 
   echo "</center></table>";  
  
}

if ($windicador == "PrimeraVez") 	
{
   //Si no controlo con esta variable siempre que le den editar en el programa que lo llama este entra modificando   
   $windicador = "SegundaVez";
   echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
}	


odbc_close($conexN);
odbc_close_all();    
?>
</BODY>
</html>
