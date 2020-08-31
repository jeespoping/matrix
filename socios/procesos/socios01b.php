<html>
<head>
<title>Actualizacion de Socios</title>
</head>

<script>
    function ira()
    {
	 document.socios01.wced.focus();
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
		document.forms.socios01.submit();   // Ojo para la funcion socios01 <> socios01  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.socios01.wtid.value = '';
     document.forms.socios01.wreg.value = '';
     document.forms.socios01.wsex.value = '';
     document.forms.socios01.wap1.value = '';
     document.forms.socios01.wap2.value = '';
     document.forms.socios01.wnom.value = '';     
     document.forms.socios01.wnac.value = 'aaaa-mm-dd';
     document.forms.socios01.wida.value = '';
     document.forms.socios01.widc.value = '';   
     document.forms.socios01.widc.value = '';
	 document.forms.socios01.wdes.value = 'aaaa-mm-dd';
     document.forms.socios01.wcoa.value = '';
     document.forms.socios01.wcob.value = '';
     document.forms.socios01.wdir.value = '';
     document.forms.socios01.wdep.value = '';
     document.forms.socios01.wmun.value = '';     
     document.forms.socios01.wapa.value = '';
     document.forms.socios01.wtre.value = '';
     document.forms.socios01.wtof.value = '';   
     document.forms.socios01.wtce.value = '';
     document.forms.socios01.weci.value = '';
     document.forms.socios01.wpol.value = '';
     document.forms.socios01.wmon.value = '0';
     document.forms.socios01.wemp.value = '';
     document.forms.socios01.wvig.value = 'aaaa-mm-dd';
     document.forms.socios01.waso.value = '';     
     document.forms.socios01.wnum.value = '';
     document.forms.socios01.wfec.value = 'aaaa-mm-dd';
     document.forms.socios01.wtic.value = '0';      
     document.forms.socios01.wpri.value = '0';
     document.forms.socios01.wint.value = '0';
     document.forms.socios01.wacc.value = '0';
     document.forms.socios01.wdiv.value = 'aaaa-mm-dd';
     document.forms.socios01.wobs.value = '';
     document.forms.socios01.wact.value = '';
     document.forms.socios01.weac.value = '';
             
    }
    
 	// Fn que solo deja digitar los nros del 0 al 9, el . y el enter
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
 
</script>

<?php
include_once("conex.php");



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//==========================================================================================================================================
//PROGRAMA				      :Actualiza Socios PMLA.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Agosto 22 de 2011
//FECHA ULTIMA ACTUALIZACION  :Agosto 22 de 2011.                                                                             

$wactualiz="PROGRAMA: socios01.php Ver. 2009-11-06   JairS";

/*
session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");
*/

Function sumaDia($fecha,$dia)
{	
 // Esta funcion se invoca asi: sumaDia('2008-01-30',5) y produce una salida asi: 2008-02-04
 // o asi: sumaDia('2008-01-30',-1) y la salida sera: 2008-01-29
	list($year,$mon,$day) = explode('-',$fecha);
	return date('Y-m-d',mktime(0,0,0,$mon,$day+$dia,$year));	
 // Si quiero con otro formato 
    //list($day,$mon,$year) = explode('/',$fecha);	
    //return date('d/m/Y',mktime(0,0,0,$mon,$day+$dia,$year));			
} 

/**
 * Inserta de manera automatica un input con su respectivo botón para manejar fechas, con valor inicial
 * @param $nombreCampo
 * @return unknown_type
 */

Function validar_datos($ced,$ap1,$nom,$nac,$des,$coa,$cob,$acc) 
{	    
   global $msgerr;   
   
   $msgerr = "";
     
   if (empty($ced))
   {
      $msgerr=$msgerr." Debe existir nro de documento. ";   
   }
                  
   if (empty($ap1))
   {
      $msgerr=$msgerr." Debe existir 1er Apellido. ";   
   }   

   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($nac,5,2), substr($nac,8,2), substr($nac,0,4)) )
   {
     $msgerr=$msgerr." Fecha de nacimiento debe ser aaaa-mm-dd. ";   
   }                      
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($des,5,2), substr($des,8,2), substr($des,0,4)) )
   {
     $msgerr=$msgerr." Fecha socio desde debe ser aaaa-mm-dd. ";   
   }
   else
   {
	 $hoy = date("Y-m-d");  
     if ($des < $nac)
     {
      $msgerr=$msgerr." Fecha socio desde debe ser mayor a la de nacimiento.";   
     } 
   }  
   
   
   //Validar cuentas de correo electronicos con expresiones regulares
   if (!empty($coa))
   {
    if(!preg_match('/^[(a-z0-9\_\-\.)]+@[(a-z0-9\_\-\.)]+\.[(a-z)]{2,4}$/i',$coa))
    {
      $msgerr=$msgerr." Direccion de correo electronico 1 Invalido.";   
    }
   }
   
   if (!empty($cob))
   {
    if(!preg_match('/^[(a-z0-9\_\-\.)]+@[(a-z0-9\_\-\.)]+\.[(a-z)]{2,4}$/i',$cob))
    {
      $msgerr=$msgerr." Direccion de correo electronico 2 Invalido.";   
    }
   }
   
/*   if ( $acc < 1)
    {
      $msgerr=$msgerr." Nro de Acciones ordinarias debe ser mayor a cero.";   
    }
   
   
   if (strlen($msgerr) > 0)
     echo "<script language='javascript'>alert('".$msgerr."');</script>"; 
*/

    return $msgerr; 
}

echo "<form name='socios01' action='socios01.php' method=post>";  

echo "<center><table border=1>";
echo "<tr><td align=center colspan=6 bgcolor=#006699><font size=3 text color=#FFFFFF><b>Socios de Promotora Medica Las Americas</b></font></tr>";
//echo "<tr><td align=center colspan=6 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

if ($windicador == "PrimeraVez") 
{
 if ($wproceso == "Modificar" )  	
 {
   $query = "SELECT * FROM socios_000001 Where socced = '".$wcod."'";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro 
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wced=$registro[3];
    $wtid=$registro[4];
    $wreg=$registro[5];
    $wsex=$registro[6];
    $wap1=$registro[7];
    $wap2=$registro[8];
    $wnom=$registro[9];
    $wnac=$registro[10];
    $wida=$registro[11];
    $widb=$registro[12];
    $widc=$registro[13];
    $wdes=$registro[14];
    $wcoa=$registro[15];
    $wcob=$registro[16];
    $wdir=$registro[17];
    $wdep=$registro[18];
    $wmun=$registro[19];
    $wapa=$registro[20];
    $wtre=$registro[21];
    $wtof=$registro[22];
    $wtce=$registro[23];
    $weci=$registro[24];
    $wpol=$registro[25];
    $wmon=$registro[26];
    $wemp=$registro[27];
    $wvig=$registro[28];
    $waso=$registro[29];
    $wnum=$registro[30];
    $wfec=$registro[31];
    $wtic=$registro[32];
    $wpri=$registro[33];
    $wint=$registro[34];
    $wacc=$registro[35];
    $wdiv=$registro[36];
    $wobs=$registro[37];
    $wact=$registro[38];
    $weac=$registro[39];
   }
 }  
 else
 {
  if ($wproceso == "Nuevo") 
   {
     $wnum = "0";
     $wfec = "2010-12-31";
     $wtic = "0";      
     $wpri = "0";
     $wint = "0";
     $wacc = "0";
     $wdiv = "2010-12-31";
     $wobs = ".";
     $wact = "A";
     $weac = "A";
   }
 } 
}

// Capturo el tipo de usuario para saber que puede hacer
  $user="07012";
        $query = "SELECT usrtip FROM socios_000012 Where usrcod = '".$user."'";
        $resultado = mysql_query($query,$conex);
        $nroreg = mysql_num_rows($resultado);
        if ( $nroreg > 0 )                   //Encontro
        {
	      $registro = mysql_fetch_row($resultado);
	      $wtipousr=$registro[0];
        }	      

echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>DATOS BASICOS DEL SOCIO</b></font></tr>";
 if ($wproceso=="Modificar")
 {
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Nro de Documento:</font></b><br>";
    if (isset($wced))
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 READONLY VALUE='".$wced."' onKeyUp='form.wced.value=form.wced.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 READONLY onKeyUp='form.wced.value=form.wced.value.toUpperCase()'></INPUT></td>"; 
 }
 else
 {
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Nro de Documento:</font></b><br>";
    if (isset($wced))
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 VALUE='".$wced."' onKeyUp='form.wced.value=form.wced.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 onKeyUp='form.wced.value=form.wced.value.toUpperCase()'></INPUT></td>"; 
 }   
     
    echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Tipo de Documento<br></font></b>"; 
    echo "<select name='wtid'>";
    if (isset($wtid))
    {
	 $c1=explode('-',$wtid);     // Del combo tomo el codigo
	 if($c1[0] == "01" ) 
	  echo "<option SELECTED>01- Nit</option>";          
	 else
	  echo "<option>01- Nit</option>";    
	    
	 if($c1[0]  == "02" ) 
	  echo "<option SELECTED>02- Cedula</option>";            
	 else      
      echo "<option>02- Cedula</option>";  

     if($c1[0]  == "03" )      
      echo "<option SELECTED>03- Cedula Extrangeria</option>"; 
     else   
      echo "<option>03- Cedula Extrangeria</option>";    

     if($c1[0]  == "04" )      
      echo "<option SELECTED>04- Tarjeta de Identidad</option>";        
     else  
      echo "<option>04- Tarjeta de Identidad</option>";  
    }  
    else
	{		
	 echo "<option>01- Nit</option>";
	 echo "<option>02- Cedula</option>";
	 echo "<option>03- Cedula Extrangeria</option>";
	 echo "<option>04- Tarjeta de Identidad</option>";
	}	
	 echo "</select></td>";  	
		
      
    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Nro de Registro:</font></b><br>";
    if (isset($wreg))
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=15 VALUE='".$wreg."' onKeyUp='form.wreg.value=form.wreg.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=15 onKeyUp='form.wreg.value=form.wreg.value.toUpperCase()'></INPUT></td>";
     
  
    echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Sexo<br></font></b>"; 
    echo "<select name='wsex'>";
    if (isset($wsex))
    {$c2=explode('-',$wsex);     // Del combo tomo el codigo
	 if($c2[0] == "M" ) 
	  echo "<option SELECTED>M- Masculino</option>";          
	 else
	  echo "<option>M- Masculino</option>";    
	    
	 if($c2[0] == "F" ) 
	  echo "<option SELECTED>F- Femenino</option>";            
	 else      
      echo "<option>F- Femenino</option>";  
    }  
    else
	{		
	 echo "<option>M- Masculino</option>";
	 echo "<option>F- Femenino</option>";
	}	
	 echo "</select></td>";  
  
  if (isset($wced))
  {	 	 
   if (file_exists ("../fotos/".$wced.".jpg"))
   {
   ?>
    <td align=center bgcolor=#DDDDDD colspan=1><a href='javascript:window.open("<?php echo "../fotos/".$wced.".jpg" ?>",  "_blank", "align=CENTER, height=150, width=100, left=400, top=30, status=yes, toolabar=no, //scrollbar=yes, menubar=no");;void(0)' target="_self">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp<img src='/MATRIX/zpmenu/themes/icon/transparent/blue/about.gif' alt='Fotos del Socio' title='Foto del Socio' border='0' width='16' height='16'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp</a>
   <?php  
   }  
   else
    echo "<td align=center bgcolor=#DDDDDD colspan=1>";
  }
  else
   echo "<td align=center bgcolor=#DDDDDD colspan=1>";
  
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>1er Apellido:</font></b><br>";
    if (isset($wap1))
     echo "<INPUT TYPE='text' NAME='wap1' size=30 maxlength=20 VALUE='".$wap1."' onKeyUp='form.wap1.value=form.wap1.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wap1' size=30 maxlength=20 onKeyUp='form.wap1.value=form.wap1.value.toUpperCase()'></INPUT></td>"; 

    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>2do Apellido:</font></b><br>";
    if (isset($wap2))
     echo "<INPUT TYPE='text' NAME='wap2' size=30 maxlength=20 VALUE='".$wap2."' onKeyUp='form.wap2.value=form.wap2.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wap2' size=30 maxlength=20 onKeyUp='form.wap2.value=form.wap2.value.toUpperCase()'></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Nombres:</font></b><br>";
    if (isset($wnom))
     echo "<INPUT TYPE='text' NAME='wnom' size=30 maxlength=25 VALUE='".$wnom."' onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wnom' size=30 maxlength=25 onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 

  if (!isset($wnac))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wnac=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha de nacimiento<br></font></b>";   
   	$cal="calendario('wnac','1')";
	echo "<input type='TEXT' name='wnac' size=10 maxlength=10  id='wnac'  value=".$wnac." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wnac',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php


    echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>1er Idioma<br></font></b>"; 
    echo "<select name='wida'>";
    if (isset($wida))
    {$c3=explode('-',$wida);     // Del combo tomo el codigo
	 if($c3[0] == "" ) 
	  echo "<option SELECTED></option>";          
	 else
	  echo "<option></option>"; 
	  
	 if($c3[0] == "01" ) 
	  echo "<option SELECTED>01- Ingles</option>";          
	 else
	  echo "<option>01- Ingles</option>"; 
	  
 	 if($c3[0] == "02" ) 
	  echo "<option SELECTED>02- Frances</option>";          
	 else
	  echo "<option>02- Frances</option>";    
	 if($c3[0] == "03" ) 
	  echo "<option SELECTED>03- Aleman</option>";          
	 else
	  echo "<option>03- Aleman</option>"; 
	  
 	 if($c3[0] == "04" ) 
	  echo "<option SELECTED>04- Portugues</option>";          
	 else
	  echo "<option>04- Portugues</option>";  
	  
 	 if($c3[0] == "05" ) 
	  echo "<option SELECTED>05- Mandarin</option>";          
	 else
	  echo "<option>05- Mandarin</option>";    	
	  	  
 	 if($c3[0] == "06" ) 
	  echo "<option SELECTED>06- Arabe</option>";          
	 else
	  echo "<option>06- Arabe</option>";    	
	  
 	 if($c3[0] == "07" ) 
	  echo "<option SELECTED>07- Otro</option>";          
	 else
	  echo "<option>07- Otro</option>";    	  	      
    }  
    else
	{		
	 echo "<option></option>"; 
	 echo "<option>01- Ingles</option>"; 
	 echo "<option>02- Frances</option>";    
	 echo "<option>03- Aleman</option>"; 
	 echo "<option>04- Portugues</option>"; 
	 echo "<option>05- Mandarin</option>";    	
	 echo "<option>06- Arabe</option>";   
	 echo "<option>07- Otro</option>";  	    
	}	
	 echo "</select></td>";      

   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>2do Idioma<br></font></b>"; 
    echo "<select name='widb'>";
    if (isset($widb))
    {$c4=explode('-',$widb);     // Del combo tomo el codigo
	 if($c4[0] == "" ) 
	  echo "<option SELECTED></option>";          
	 else
	  echo "<option></option>"; 
	  
	 if($c4[0] == "01" ) 
	  echo "<option SELECTED>01- Ingles</option>";          
	 else
	  echo "<option>01- Ingles</option>"; 
	  
 	 if($c4[0] == "02" ) 
	  echo "<option SELECTED>02- Frances</option>";          
	 else
	  echo "<option>02- Frances</option>";    
	 if($c4[0] == "03" ) 
	  echo "<option SELECTED>03- Aleman</option>";          
	 else
	  echo "<option>03- Aleman</option>"; 
	  
 	 if($c4[0] == "04" ) 
	  echo "<option SELECTED>04- Portugues</option>";          
	 else
	  echo "<option>04- Portugues</option>";  
	  
 	 if($c4[0] == "05" ) 
	  echo "<option SELECTED>05- Mandarin</option>";          
	 else
	  echo "<option>05- Mandarin</option>";    	
	  	  
 	 if($c4[0] == "06" ) 
	  echo "<option SELECTED>06- Arabe</option>";          
	 else
	  echo "<option>06- Arabe</option>";    	
	  
 	 if($c4[0] == "07" ) 
	  echo "<option SELECTED>07- Otro</option>";          
	 else
	  echo "<option>07- Otro</option>";    	  	      
    }  
    else
	{		
	 echo "<option></option>"; 
	 echo "<option>01- Ingles</option>"; 
	 echo "<option>02- Frances</option>";    
	 echo "<option>03- Aleman</option>"; 
	 echo "<option>04- Portugues</option>"; 
	 echo "<option>05- Mandarin</option>";    	
	 echo "<option>06- Arabe</option>";   
	 echo "<option>07- Otro</option>";  	    
	}	
	 echo "</select></td>";  
	 
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>3er Idioma<br></font></b>"; 
    echo "<select name='widc'>";
    if (isset($widc))
    {$c5=explode('-',$widc);     // Del combo tomo el codigo
	 if($c5[0] == "" ) 
	  echo "<option SELECTED></option>";          
	 else
	  echo "<option></option>"; 
	  
	 if($c5[0] == "01" ) 
	  echo "<option SELECTED>01- Ingles</option>";          
	 else
	  echo "<option>01- Ingles</option>"; 
	  
 	 if($c5[0] == "02" ) 
	  echo "<option SELECTED>02- Frances</option>";          
	 else
	  echo "<option>02- Frances</option>";    
	 if($c5[0] == "03" ) 
	  echo "<option SELECTED>03- Aleman</option>";          
	 else
	  echo "<option>03- Aleman</option>"; 
	  
 	 if($c5[0] == "04" ) 
	  echo "<option SELECTED>04- Portugues</option>";          
	 else
	  echo "<option>04- Portugues</option>";  
	  
 	 if($c5[0] == "05" ) 
	  echo "<option SELECTED>05- Mandarin</option>";          
	 else
	  echo "<option>05- Mandarin</option>";    	
	  	  
 	 if($c5[0] == "06" ) 
	  echo "<option SELECTED>06- Arabe</option>";          
	 else
	  echo "<option>06- Arabe</option>";    	
	  
 	 if($c5[0] == "07" ) 
	  echo "<option SELECTED>07- Otro</option>";          
	 else
	  echo "<option>07- Otro</option>";    	  	      
    }  
    else
	{		
	 echo "<option></option>"; 
	 echo "<option>01- Ingles</option>"; 
	 echo "<option>02- Frances</option>";    
	 echo "<option>03- Aleman</option>"; 
	 echo "<option>04- Portugues</option>"; 
	 echo "<option>05- Mandarin</option>";    	
	 echo "<option>06- Arabe</option>";   
	 echo "<option>07- Otro</option>";  	    
	}	
	 echo "</select></td>";      	     	 	      
     

  if (!isset($wdes))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wdes=date("Y-m-d");
    
    echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Socio desde:<br></font></b>";   
   	$cal="calendario('wdes','1')";
	echo "<input type='TEXT' name='wdes' size=10 maxlength=10  id='wdes' readonly='readonly' value=".$wdes." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wdes',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
   
	echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>OTROS DATOS</b></font></tr>";

     echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Correo Electronico 1:</font></b><br>";
    if (isset($wcoa))
     echo "<INPUT TYPE='text' NAME='wcoa' size=40 maxlength=50 VALUE='".$wcoa."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcoa' size=40 maxlength=50 ></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Correo Electronico 2:</font></b><br>";
    if (isset($wcob))
     echo "<INPUT TYPE='text' NAME='wcob' size=40 maxlength=50 VALUE='".$wcob."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcob' size=40 maxlength=50 ></INPUT></td>"; 

    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Direccion:</font></b><br>";
    if (isset($wdir))
     echo "<INPUT TYPE='text' NAME='wdir' size=60 maxlength=80 VALUE='".$wdir."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wdir' size=60 maxlength=80 ></INPUT></td>"; 
     
   echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Departamento:</font></b><br>";   
   
   //if ( $wproceso == "Modificar" )
   // $query = "SELECT codigo,descripcion FROM root_000002 WHERE codigo = '".$wdep."'";
   //else 	   
   $query = "SELECT codigo,descripcion FROM root_000002 ORDER BY descripcion";   
   echo "<select name='wdep'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  { $c6=explode('-',$wdep);     // Del combo tomo el codigo 			  
		$registroB = mysql_fetch_row($resultadoB); 
  		if($c6[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";
    
   echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Municipio:</font></b><br>";   
   //if ( $wproceso == "Modificar" )
   // $query = "SELECT codigo,nombre FROM root_000006 WHERE codigo = '".$wmun."'";
   //else 	   
   $query = "SELECT codigo,nombre FROM root_000006 ORDER BY nombre ";   
   echo "<select name='wmun' >"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  { $c7=explode('-',$wmun);     // Del combo tomo el codigo
		$registroB = mysql_fetch_row($resultadoB);  			  
  		if($c7[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";
    
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Apartado Aereo:</font></b><br>";
    if (isset($wapa))
     echo "<INPUT TYPE='text' NAME='wapa' size=25 maxlength=25 VALUE='".$wapa."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wapa' size=25 maxlength=25 ></INPUT></td>"; 
     
 
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Telefono Residencia:</font></b><br>";
    if (isset($wtre))
     echo "<INPUT TYPE='text' NAME='wtre' size=15 maxlength=15 VALUE='".$wtre."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wtre' size=15 maxlength=15 ></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Telefono Oficina:</font></b><br>";
    if (isset($wtof))
     echo "<INPUT TYPE='text' NAME='wtof' size=20 maxlength=20 VALUE='".$wtof."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wtof' size=20 maxlength=20 ></INPUT></td>"; 

    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Telefono Celular</font></b><br>";
    if (isset($wtce))
     echo "<INPUT TYPE='text' NAME='wtce' size=15 maxlength=15 VALUE='".$wtce."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wtce' size=15 maxlength=15 ></INPUT></td>"; 
     
    echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Estado Civil<br></font></b>"; 
    echo "<select name='weci'>";
    if (isset($weci))
    {$c8=explode('-',$weci);     // Del combo tomo el codigo
	 if($c8[0] == "S" ) 
	  echo "<option SELECTED>S- Soltero(a)</option>";          
	 else
	  echo "<option>S- Soltero(a)</option>";    
	    
	 if($c8[0] == "C" ) 
	  echo "<option SELECTED>C- Casado(a)</option>";            
	 else      
      echo "<option>C- Casado(a)</option>";  
      
     if($c8[0] == "V" ) 
	  echo "<option SELECTED>V- Viudo(a)</option>";            
	 else      
      echo "<option>V- Viudo(a)</option>";  

     if($c8[0] == "U" ) 
	  echo "<option SELECTED>U- Union Libre</option>";            
	 else      
      echo "<option>U- Union Libre</option>";  
            
     if($c8[0] == "D" ) 
	  echo "<option SELECTED>D- Separado(a)</option>";            
	 else      
      echo "<option>D- Separado(a)</option>";  
    }  
    else
	{		
	 echo "<option>S- Soltero</option>";
	 echo "<option>C- Casado</option>";
	 echo "<option>V- Viudo(a)</option>";
	 echo "<option>U- Union Libre</option>";
	 echo "<option>D- Separado(a)</option>";  
	}	
	 echo "</select></td>";  	     
    
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Poliza Nro:</font></b><br>";
    if (isset($wpol))
     echo "<INPUT TYPE='text' NAME='wpol' size=15 maxlength=15 VALUE='".$wpol."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wpol' size=15 maxlength=15 ></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Monto:</font></b><br>";
    if (isset($wmon))
     echo "<INPUT TYPE='text' NAME='wmon' size=15 maxlength=15 VALUE='".$wmon."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wmon' size=15 maxlength=15 ></INPUT></td>"; 

    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Empresa que expide</font></b><br>";
    if (isset($wemp))
     echo "<INPUT TYPE='text' NAME='wemp' size=15 maxlength=15 VALUE='".$wemp."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wemp' size=15 maxlength=15 ></INPUT></td>";
     
  if (!isset($wvig))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wvig=date("Y-m-d");
    
    echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Vence:<br></font></b>";   
   	$cal="calendario('wvig','1')";
	echo "<input type='TEXT' name='wvig' size=10 maxlength=10  id='wvig' readonly='readonly' value=".$wvig." class=tipo3><button id='trigger3' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wvig',button:'trigger3',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Asociaciones a las que pertenece</font></b><br>";
    if (isset($waso))
     echo "<INPUT TYPE='text' NAME='waso' size=40 maxlength=60 VALUE='".$waso."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='waso' size=40 maxlength=60 ></INPUT></td>";

    echo "<tr><td align=center colspan=6 bgcolor=#6699CC><font size=3 text color=#FFFFFF><b>LIBRO DE ACCIONES</b></font></tr>";  
    
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Nro de Registro</font></b><br>";
    if (isset($wnum))
     echo "<INPUT TYPE='text' NAME='wnum' size=15 VALUE='".$wnum."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wnum' size=15 ></INPUT></td>";   
 
 //     ****************************     APARTIR DE AQUI CAPTURAMOS LOS DATOS DE LAS ACCIONES  **************************** //
         
    if (!isset($wfec))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec=date("Y-m-d");
    
  echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha del titulo:<br></font></b>";     
  if ($wtipousr=="A")  // Puede modificar datos del Acciones
  {  
   	$cal="calendario('wfec','1')";
	echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3><button id='trigger4' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
  }
  else
    echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly='readonly' value=".$wfec." class=tipo3>;

    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Titulos cancelados</font></b><br>";
    if (isset($wtic))
     echo "<INPUT TYPE='text' NAME='wtic' size=15 VALUE='".$wtic."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wtic' size=15 onkeypress='teclado()'></INPUT></td>";          

    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Acciones Privilegiadas</font></b><br>";
    if (isset($wpri))
     echo "<INPUT TYPE='text' NAME='wpri' size=15 VALUE='".$wpri."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wpri' size=15 onkeypress='teclado()'></INPUT></td>";          

    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Intereses pagados</font></b><br>";
    if (isset($wint))
     echo "<INPUT TYPE='text' NAME='wint' size=10 maxlength=4 VALUE='".$wint."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wint' size=10 maxlength=4 onkeypress='teclado()'></INPUT></td>";  
     
    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Acciones Ordinarias</font></b><br>";
    if (isset($wacc))
     echo "<INPUT TYPE='text' NAME='wacc' size=15 VALUE='".$wacc."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wacc' size=15 onkeypress='teclado()'></INPUT></td>";    
     
     
  if (!isset($wdiv))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wdiv=date("Y-m-d");
    
    echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Ultimo dividendo pagado:<br></font></b>";   
   	$cal="calendario('wdiv','1')";
	echo "<input type='TEXT' name='wdiv' size=10 maxlength=10  id='wdiv' readonly='readonly' value=".$wdiv." class=tipo3><button id='trigger5' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wdiv',button:'trigger5',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=2>Descripcion u observaciones</font></b><br>";
    if (isset($wobs))
     echo "<textarea name='wobs' cols=50 rows=3>".$wobs."</textarea></td>";
    else
     echo "<textarea name='wobs' cols=50 rows=3></textarea></td>";
   
    echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Socio&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acciones<br>Estado:&nbsp;&nbsp;&nbsp;</font></b>"; 
    echo "<select name='wact'>";
    if (isset($wact))
    {$c9=explode('-',$wact);     // Del combo tomo el codigo
	 if($c9[0] == "A" ) 
	  echo "<option SELECTED>A- Activo</option>";          
	 else
	  echo "<option>A- Activo</option>"; 
	  
 	 if($c9[0] == "I" ) 
	  echo "<option SELECTED>I- Inactivo</option>";          
	 else
	  echo "<option>I- Inactivo</option>";    
    }  
    else
	{		
	 echo "<option>A- Activo</option>";
	 echo "<option>I- Inactivo</option>";
	}	
	 echo "</select>";  
	 
	
    echo "&nbsp;&nbsp;&nbsp;<select disabled='disabled' name='weac' >";
    if (isset($weac))
    {$c10=explode('-',$weac);     // Del combo tomo el codigo
     if($c10[0] == "A" ) 
	  echo "<option SELECTED>A- Activas</option>";          
	 else
	  echo "<option>A- Activas</option>"; 
	  
 	 if($c10[0] == "I" ) 
	  echo "<option SELECTED>I- Inactivas</option>";          
	 else
	  echo "<option>I- Inactivas</option>";    
    }  
    else
	{		
	 echo "<option>A- Activas</option>";
	 echo "<option>I- Inactivas</option>";
	}	
	 echo "</select></td>";  

	      
    // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

	   if (isset($wnit))
	     echo "<INPUT TYPE = 'hidden' NAME='wnit' VALUE='".$wnit."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wnit'></INPUT>";                
	     	     
	   if (isset($wcod))
	     echo "<INPUT TYPE = 'hidden' NAME='wcod' VALUE='".$wcod."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wcod'></INPUT>";                
	     
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
     
   	echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";
   	echo "<input type='submit' value='Grabar'>";          
   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	echo "<tr><td align=center colspan=6 bgcolor=#cccccc>";
   	
   	if ($wproceso == "Modificar") 
   	{
?>
<!--

  1ra Forma boton con tag input pero no acepta imagenes 
    <input type="button" value="Grupo Familiar" onclick="javascript:window.open('socios01.php?wcod= <?php echo $r; ?>','SOCIOS',' Top=200, Left=150, width=300, height=300, resizable=YES, Scrollbars=YES')">
  2do Forma no es un boton sino una imagen tiene el problema es que abre varias ventanas si lo intento abrir varias veces
    <a href='javascript:window.open("socios01.php?wcod= <?phpPHP print ($r); ?>&msg=<?php echo $msg?> ", "_blank", "height=600, width=1100, left=100, top=50, status=yes, toolabar=no, //scrollbar=yes, menubar=no");;void(0)' target="_self"><img src='consulting.gif' alt='Report Completed' title='Report Completed' border='0' width='16' height='16'></a>
  3ra Forma un tag tipo boton permite imagen y no abre varias ventanas si se invoca varias veces

  -->
  <BUTTON TYPE=BUTTON value="Grupo Familiar" onclick="javascript:window.open('socios02.php?wcod= <?phpPHP echo $wcod; ?>&windicador=PrimeraVez&wproceso=Nuevo','socios',' Top=200, Left=150, width=670, height=300, resizable=YES, Scrollbars=YES')">Grupo Familiar <IMG SRC="/MATRIX/zpmenu/themes/icon/transparent/blue/forum.gif" ALT="Grupo Familiar del Socio"></BUTTON>
  <BUTTON TYPE=BUTTON value="Informacion Academica" onclick="javascript:window.open('socios03.php?wcod= <?phpPHP echo $wcod; ?>&windicador=PrimeraVez&wproceso=Nuevo','socios',' Top=200, Left=100, width=900, height=350, resizable=YES, Scrollbars=YES')">Informacion Academica <IMG SRC="/MATRIX/zpmenu/themes/icon/transparent/blue/consulting.gif" ALT="Informacion academica del Socio"></BUTTON>
  <BUTTON TYPE=BUTTON value="Especialidades" onclick="javascript:window.open('socios04.php?wcod= <?phpPHP echo $wcod; ?>&windicador=PrimeraVez&wproceso=Nuevo','socios',' Top=200, Left=100, width=900, height=300, resizable=YES, Scrollbars=YES')">Especialidades <IMG SRC="/MATRIX/zpmenu/themes/icon/transparent/blue/news.gif" ALT="Especialidades"></BUTTON>

  <!--
  <BUTTON TYPE=BUTTON value="Vinculacion a Sociedades" onclick="javascript:window.open('socios01.php?wcod= <?phpPHP echo $wcod; ?>','socios',' Top=200, Left=150, width=300, height=300, resizable=YES, Scrollbars=YES')">Vinculacion a Sociedades <IMG SRC="/MATRIX/zpmenu/themes/icon/transparent/blue/services.gif" ALT="Vinculacion a sociedades"></BUTTON>
 -->  
<?php 
    if($c10[0] == "A" ) //Si las acciones ACTIVAS se muestra este botton NOTESE que apesar de ser solo una intruccion va { .. } por ser HTML
    {
?> 
      <BUTTON TYPE=BUTTON value="Mvto de acciones" onclick="javascript:window.open('socios06.php?wcod= <?phpPHP echo $wcod; ?>&windicador=PrimeraVez&wproceso=Nuevo','socios',' Top=250, Left=150, width=770, height=320, resizable=YES, Scrollbars=YES')">Mvto de Acciones <IMG SRC="/MATRIX/zpmenu/themes/icon/transparent/blue/buy.gif" ALT="Mvto de Acciones"></BUTTON>
<?php  }

   }
   
//    echo "<li><A HREF='socios00.php'>Regresar</A>";		
   	echo "</td></tr>";	
	
if ( isset($wced) and $wced<>'' and isset($wap1) and $wap1<>''  and ($windicador <> "PrimeraVez"))
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wced,$wap1,$wnom,$wnac,$wdes,$wcoa,$wcob,$wacc);
  
  if ($msgerr == "" ) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {   
     $query = "SELECT socap1,socap2,socnom "
             ." FROM socios_000001 Where socced = '".$wcod."'";
     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                   //Encontro
     {
  	        $c1=explode("-",$wtid);
	        $query = "Update socios_000001 SET soctid='".$c1[0]."',socreg='".$wreg."',socsex='".$c2[0]."'"
               .",socap1='".$wap1."',socap2='".$wap2."',socnom='".$wnom."',socnac='".$wnac."',socida='".$c3[0]."',socidb='".$c4[0]."'"
               .",socidc='".$c5[0]."',socdes='".$wdes."',soccoa='".$wcoa."',soccob='".$wcob."',socdir='".$wdir."',socdep='".$c6[0]."'"
               .",socmun='".$c7[0]."',socapa='".$wapa."',soctre='".$wtre."',soctof='".$wtof."',soctce='".$wtce."',soceci='".$c8[0]."'"
               .",socpol='".$wpol."',socmon='".$wmon."',socemp='".$wemp."',socvig='".$wvig."',socaso='".$waso."'"
               .",socnum='".$wnum."',socfec='".$wfec."',soctic=".$wtic.",socpri=".$wpri.",socint=".$wint.",socacc=".$wacc.",socdiv='".$wdiv."'"
               .",socobs='".$wobs."',socact='".$c9[0]."',soceac='".$c10[0]."'"
               ." Where socced = '".$wcod."'";

       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	   {
	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";
	    // Grabo en el archivo de Log
        $fecha = date("Y-m-d");
        $hora = (string)date("H:i:s");
	    $query = "INSERT INTO socios_000011 VALUES ('socios','".$fecha."','".$hora."','".$user."','Maestro Socios','Modifico','".$wced."','C-".$user."','')";
	    $resultado2 = mysql_query($query,$conex);
       } 
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
	  
      $query = "INSERT INTO socios_000001 VALUES ('socios','".$fecha."','".$hora."','".$wced."','".$c1[0]."','".$wreg."','".$c2[0]
              ."','".$wap1."','".$wap2."','".$wnom."','".$wnac."','".$wida."','".$widb."','".$widc."','".$wdes."','".$wcoa              
              ."','".$wcob."','".$wdir."','".$wdep."','".$wmun."','".$wapa."','".$wtre."','".$wtof."','".$wtce."','".$c8[0]  
              ."','".$wpol."','".$wmon."','".$wemp."','".$wvig."','".$waso."','".$wnum."','".$wfec."',".$wtic.",".$wpri
              .",".$wint.",".$wacc.",'".$wdiv."','".$wobs."','".$c9[0]."','".$c10[0]."','C-".$user."','')";  

	  $resultado = mysql_query($query,$conex);
	  if ($resultado)
      {
   	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Adicionado</td></tr>";
   	    $query = "INSERT INTO socios_000011 VALUES ('socios','".$fecha."','".$hora."','".$user."','Maestro Socios','Ingreso','".$wced."','C-".$user."','')";
	    $resultado2 = mysql_query($query,$conex);
      } 
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
     echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1> Error: ".$msgerr."</MARQUEE></font>";	
     //echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!!</MARQUEE></font>";	
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
?>
</BODY>
</html>