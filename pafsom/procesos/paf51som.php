<html>
<head>
<title>Sistema de Informacion Programa Inst Mujer SOM</title>

	<style type="text/css">
		.tipodrop{color:#000000;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;width:80em;text-align:left;height:2em;}
 	</style>
 	
</head>

<script>
    function ira()
    {
	 document.paf51som.word.focus();
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
		document.forms.paf51som.submit();   // Ojo para la funcion paf51som <> paf51som  (sencible a mayusculas)
	}
    
 	// Fn que solo deje digitar los nros del 0 al 9, el . y los : 
	function numeros(e)
	{
     key = e.keyCode || e.which;
     tecla = String.fromCharCode(key).toLowerCase();
     letras = " 0123456789";
     especiales = [8,37,39,46,58];
 
     tecla_especial = false
     for(var i in especiales)
	 {
      if(key == especiales[i])
	   {
         tecla_especial = true;
         break;
       }
     }
 
      if(letras.indexOf(tecla)==-1 && !tecla_especial)
        return false;
	}
 
</script>

<?php
include_once("conex.php");

//==========================================================================================================================================
//PROGRAMA				      :Control de atenciones por Profesional                                                                  
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Octubre 22 de 2015
//FECHA ULTIMA ACTUALIZACION  :Febrero 3 de 2016, 03/Mayo/2016 Adicione Link para la Agendar Cita                                                                           

$wactualiz="PROGRAMA: paf51som.php Ver. 2012-10-22   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" );
	
Function validar_datos($fec,$hor,$ced,$nom,$car,$ord,$cit,$con,$med,$fpr,$hpr ) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
 
   // Chequeo la fecha con checkdate(mm,dd,aaaa)  
    if ($fec > date("Y-m-d")) 
    {
     $todok = false;     
     $msgerr=$msgerr." Fecha de Atencion no puede ser mayor a la fecha actual. ";   
    } 
	
   if (empty($hor))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Hora de la atencion. ";   
   }
   else
   {
	 if ( strlen($hor)<>5)
     {
      $todok = false;
      $msgerr=$msgerr." El formato para la hora de  atencion debe ser HH:MM. ";   
     }
     else	 
	 {	 
      $pos = strpos($hor, ":");
	  if ( $pos<>2 )    // los dos puntos deben estar en la posicion 3
      {
        $todok = false;
        $msgerr=$msgerr." El formato para la hora de atencion debe ser HH:MM. ";   
      }
	 }
   }
   
   if (empty($ced))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Nro de documento. ";   
   }
                  
   if (empty($nom))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Nombre del paciente no puede ser nulo.";   
   }   
      
   if (empty($car))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe indicar si maneja Carnet.";   
   }  
      
   if (empty($ord))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe especificar el nro de orden.";   
   }   

   if (empty($cit))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe especificar el tipo de cita.";   
   }
   
   if (empty($con))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe especificar la conducata a seguir.";   
   } 
   
   if ( $con == 4 )
   {   
    if ($fpr <= date("Y-m-d")) 
    {
     $todok = false;     
     $msgerr=$msgerr." Fecha proximo Control debe ser mayor a la fecha actual. ";   
    } 
   
    if (empty($hpr))
    {
     $todok = false;
     $msgerr=$msgerr." Debe existir Hora del proximo control. ";   
    }
    else
    {
	 if (strlen($hpr)<>5)
     {
      $todok = false;
      $msgerr=$msgerr." El formato para la hora del proximo control debe ser HH:MM. ";   
     }
     else	 
	 {	 
      $pos = strpos($hpr, ":");
	  if ( $pos<>2 )    // los dos puntos deben estar en la posicion 3 (2 porque el string arranca en 0)
      {
        $todok = false;
        $msgerr=$msgerr." El formato para la hora del proximo control debe ser HH:MM. ";   
      }
	 }
    }
   }
   
   if (empty($med))
   {
      $todok = false; 
      $msgerr=$msgerr." El Campo Medico que atendio no puede ser nulo.";   
   }  
   
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  




mysql_select_db("matrix") or die("No se selecciono la base de datos");    
//Conexion a Informix Creada en el "DSN de Sistema"
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");  

echo "<form name='paf51som' action='paf51som.php' method=post>";  	

echo "<center><table border=1>";
 
echo "<tr><td align=center colspan=2 bgcolor=#99CCCC><font size=3 text color=#FFFFFF><b>Control de atenciones</b></font>";


if ($windicador == "PrimeraVez") 
{
   $query = "SELECT  atefec,atehor,ateced,atenom,atehis,atecar,ateord,atecit,atecon,ateex1,ateex2,atemed,atefpr,atehpr,ateobs "
          . " FROM pafsom_000003 Where ateced = '".$wced."' And atefec = '".$wfec."' And atehor = '".$whor."'";   
	
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //  Encontro lleno los campos como estan grabados
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wfec=$registro[0];
	$whor=$registro[1];
    $wced=$registro[2];
    $wnom=$registro[3];
    $whis=$registro[4];
    $wcar=$registro[5];
    $word=$registro[6];
    $wcit=$registro[7];
    $wcon=$registro[8];
    $wex1=$registro[9];
    $wex2=$registro[10];
	$wmed=$registro[11];
	$wfpr=$registro[12];
	$whpr=$registro[13];
	$wobs=$registro[14];
   }
   else                   // NO Encontro lleno los campos con los datos recibidos por la URL y busco el nro de ORDEN y ...
   {
	   
    // Voy a la tabla de Ordenes PAF NUEVA EPS busco que nro de ORDEN tenia para este dia
    $query = "SELECT Id FROM pafsom_000001 Where pafced = '".$wced."' And paffec='".$wfec."'";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)      //  Encontro 
    {
     $registro = mysql_fetch_row($resultado);  
     $word=$registro[0];
    }		   
   
    // Si ya ha tenido alguna orden para cardio es de Control si no es Primera Vez
    $query = "SELECT Id FROM pafsom_000001 Where pafced = '".$wced."' And paffec < '".$wfec."'";
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)      //  Encontro 
    {
     $registro = mysql_fetch_row($resultado);  
     $wcit=1;
    }	
    else                 //  No encontro
    {
     $registro = mysql_fetch_row($resultado);  
     $wcit=2;
    }
   }	

}
 
////////  *** AQUI EMPIEZA LA CAPTURA DE LOS CAMPOS   *** /////////	 
  
  if (!isset($wfec))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec=date("Y-m-d");
    
    echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Fecha y hora de la Atencion:<br>";
   	$cal="calendario('wfec','1')";
	if ($wproceso=="Modificar")
	 echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly value=".$wfec." class=tipo3><button id='trigger4' disabled onclick=".$cal.">...</button>";
    else
	 echo "<input type='TEXT' name='wfec' size=10 maxlength=10  id='wfec' readonly value=".$wfec." class=tipo3><button id='trigger4' onclick=".$cal.">...</button>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec',button:'trigger4',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
	
	  echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
      if (isset($whor))

  	   if ($wproceso=="Modificar")	  
        echo "<INPUT TYPE='text' NAME='whor' size=8 maxlength=8  readonly VALUE='".$whor."' onkeypress='return numeros(event);' ></INPUT></td>";  
	   else
	    echo "<INPUT TYPE='text' NAME='whor' size=8 maxlength=8  VALUE='".$whor."' onkeypress='return numeros(event);' ></INPUT></td>";  
	
      else
       echo "<INPUT TYPE='text' NAME='whor' size=8 maxlength=8  onkeypress='return numeros(event);' ></INPUT></td>"; 
	
	


    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=3>Nro de Documento:</font></b><br>";
    if (isset($wced))
	  if ($wproceso=="Modificar")	  
       echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 readonly VALUE='".$wced."' onkeypress='return numeros(event);' ></INPUT></td>"; 
      else
	   echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 VALUE='".$wced."' onkeypress='return numeros(event);' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wced' size=30 maxlength=15 onkeypress='return numeros(event);'></INPUT></td>"; 


    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nombre del Paciente:</font></b><br>";
    if (isset($wnom))
     echo "<INPUT TYPE='text' NAME='wnom' size=50 maxlength=50 VALUE='".$wnom."' onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wnom' size=50 maxlength=50 onKeyUp='form.wnom.value=form.wnom.value.toUpperCase()'></INPUT></td>"; 

	
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nro de Historia:</font></b><br>";
    if (isset($whis))
     echo "<INPUT TYPE='text' NAME='whis' size=30 maxlength=20 VALUE='".$whis."' onkeypress='return numeros(event);'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='whis' size=30 maxlength=20 onkeypress='return numeros(event);'></INPUT></td>"; 

 
    echo "<tr><td align=CENTER colspan=1 bgcolor=#C0C0C0><b><font text color=#003366 size=2>Carnet: </font></b>"; 
    echo "<select name='wcar'>";
    if (isset($wcar))
    {
	 $c1=explode('-',$wcar);     // Del combo tomo el codigo porque de esta forma se almacena codigo- Nombre 
	 if($c1[0] == "S" ) 
	  echo "<option SELECTED>S- Si</option>";                
	 else
	  echo "<option>S- Si</option>";                         
	    
	 if($c1[0] == "N" ) 
	  echo "<option SELECTED>N- No</option>";            
	 else                                                     
      echo "<option>N- No</option>";                     
    }  
    else
	{		
	 echo "<option>S- Si  </option>";
	 echo "<option>N- No  </option>";

	}	
	 echo "</select>";

	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<b><font text color=#003366 size=2>Nro de la Orden: </font></b>";
    if (isset($word))
     echo "<INPUT TYPE='text' NAME='word' size=15 maxlength=15  VALUE='".$word."' onkeypress='return numeros(event);' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='word' size=15 maxlength=15  onkeypress='return numeros(event);' ></INPUT></td>"; 	 

 
	 $a=array(1=>"Se da de Alta",2=>"Se Hospitaliza",3=>"No Asitio",4=>"Asitio");     // De esta forma se almacena solo el codigo
	 echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Conducta a seguir: </font></b>";
	 echo "<select name='wcon'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wcon)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wcon == $i )    // ==> Ese Item es el seleccionado 
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
	 echo "</select>";

	 echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	 $a=array(1=>"De Control",2=>"De 1ra Vez"); 
	 echo "<b><font text color=#003366 size=2>Cita: </font></b>";
	 echo "<select name='wcit'>";
	 echo "<option></option>";                // Primera en blanco 
	 if (isset($wcit)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wcit == $i )    // ==> Ese Item es el seleccionado 
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
	 
/****/    
    echo "<tr><td align=center bgcolor=#E6E6FA colspan=1><b><font text color=#003366 size=2>Criterio para examen 1:</font></b><br>";
    if (isset($wcr1))
     echo "<INPUT TYPE='text' NAME='wcr1' size=30 maxlength=15 VALUE='".$wcr1."' onKeyUp='form.wcr1.value=form.wcr1.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcr1' size=30 maxlength=15 onKeyUp='form.wcr1.value=form.wcr1.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 

   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Examen 1: </font></b><br>";   
   if ($wproceso == "Modificar" and $windicador=="PrimeraVez")  	
    $query = "SELECT codigo,nombre FROM root_000012 Where codigo='".$wex1."'";   
   else   
   {   
    if ( isset($wcr1) and $wcr1!="" )	   
     $query = "SELECT codigo,nombre FROM root_000012 Where ( codigo like '%".$wcr1."%' or nombre like '%".$wcr1."%' ) ORDER BY nombre";
    else
    {
     if ( isset($wex1) and $wex1!="" ) 	   
	 {
     $c3=explode('-',$wex1); 		
     $query = "SELECT codigo,nombre FROM root_000012 Where  codigo='".$c3[0]."'";
     }
	 else
	  $query = "SELECT codigo,nombre FROM root_000012 Where  codigo='$&#$#' ";	
    }
   }	
    
   echo "<select name='wex1' class='tipodrop'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c3=explode('-',$wex1); 				  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";   
	
/****/
    echo "<tr><td align=center bgcolor=#E6E6FA colspan=1><b><font text color=#003366 size=2>Criterio para examen 2:</font></b><br>";
    if (isset($wcr2))
     echo "<INPUT TYPE='text' NAME='wcr2' size=30 maxlength=15 VALUE='".$wcr2."' onKeyUp='form.wcr2.value=form.wcr2.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcr2' size=30 maxlength=15 onKeyUp='form.wcr2.value=form.wcr2.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 

   echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Examen 2: </font></b><br>";   
   if ($wproceso == "Modificar" and $windicador=="PrimeraVez")  	
    $query = "SELECT codigo,nombre FROM root_000012 Where codigo='".$wex2."'";   
   else   
   {   
    if ( isset($wcr2) and $wcr2!="" )	   
     $query = "SELECT codigo,nombre FROM root_000012 Where ( codigo like '%".$wcr2."%' or nombre like '%".$wcr2."%' ) ORDER BY nombre";
    else
    {
     if ( isset($wex2) and $wex2!="" ) 	   
	 {
     $c3=explode('-',$wex2); 		
     $query = "SELECT codigo,nombre FROM root_000012 Where  codigo='".$c3[0]."'";
     }
//	 else
//	 $query = "SELECT codigo,nombre FROM root_000012 Where  codigo='$&#$#' ";	
    }
   }	
  
   echo "<select name='wex2' class='tipodrop'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
	  {
		$registroB = mysql_fetch_row($resultadoB);  
		$c3=explode('-',$wex2); 				  
  		if($c3[0] == $registroB[0])
	      echo "<option selected>".$registroB[0]."- ".$registroB[1]."</option>";
	    else
	      echo "<option>".$registroB[0]."- ".$registroB[1]."</option>"; 
	    $i++; 
      }   
    echo "</select></td>";   
	

/*****/	
   echo "<tr><td align=center bgcolor=#E6E6FA colspan=1><b><font text color=#003366 size=2>Criterio para el medico:</font></b><br>";
    if (isset($wcr3))
     echo "<INPUT TYPE='text' NAME='wcr3' size=30 maxlength=15 ".$wmodifi." VALUE='".$wcr3."' onKeyUp='form.wcr3.value=form.wcr3.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcr3' size=30 maxlength=15 ".$wmodifi." onKeyUp='form.wcr3.value=form.wcr3.value.toUpperCase()' OnBlur='enter()'></INPUT></td>"; 

   echo "<td align=center bgcolor=#C0C0C0 colspan=2><b><font text color=#003366 size=2>Medico que atendio:</font></b><br>";   
   if ($wproceso == "Modificar" and $windicador=="PrimeraVez")  
    $query = "SELECT codigo,Descripcion FROM citasom_000010 WHERE codigo='".$wmed."' Group By codigo,Descripcion"; 	         
   else   
   {   
    if ( isset($wcr3) and $wcr3!=""  )
     $query = "SELECT codigo,Descripcion FROM citasom_000010 Where ( codigo like '%".$wcr3."%' or Descripcion like '%".$wcr3."%' ) Group By codigo,Descripcion ORDER BY Descripcion";  
    else
	{   
     if ( isset($wmed) and $wmed!="" )
	 {	 
	  $c3=explode('-',$wmed); 		
      $query = "SELECT codigo,Descripcion FROM citasom_000010 WHERE codigo='".$c3[0]."' Group By codigo,Descripcion"; 
	 } 
	 else   
      $query = "SELECT codigo,Descripcion FROM citasom_000010 WHERE codigo='$&#$#' Group By codigo,Descripcion";  
    }
   } 
 
   echo "<select name='wmed' class='tipodrop'>"; 
   echo "<option></option>"; 
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c4=explode('-',$wmed); 				  
  	  if( trim($c4[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."-".$registroB[1]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."-".$registroB[1]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   

/****/    
   if (!isset($wfpr))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfpr=date("Y-m-d");

    echo "<tr><td bgcolor=#C0C0C0 align=center colspan=1><font text color=#003366 size=3>Proximo Control: FECHA - HORA <br>";
   	$cal="calendario('wfpr','1')";
	echo "<input type='TEXT' name='wfpr' size=10 maxlength=10  id='wfpr' readonly='readonly' value=".$wfpr." class=tipo3 OnChange='enter()'><button id='trigger2' ".$wmodif2." onclick=".$cal.">...</button>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfpr',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    if (isset($whpr))
     echo "<INPUT TYPE='text' NAME='whpr' size=8 maxlength=8  VALUE='".$whpr."'  onkeypress='return numeros(event);' OnBlur='enter()'></INPUT>";  
    else
     echo "<INPUT TYPE='text' NAME='whpr' size=8 maxlength=8  onkeypress='return numeros(event);' OnBlur='enter()' ></INPUT>"; 	
	
    // Link al programa de Agenda Citas	
	$pos = strpos($wmed, "-");   // Al entrar por primera vez y van al Link no esta la variable $wmed con Codigo-Nombre solo tiene el Codigo, => busco el Nombre y completo la variable
	if ($pos === false) 
	{ 
     $query = "SELECT Descripcion FROM citasom_000010 WHERE codigo='".$wmed."'";
	 $resultadoB = mysql_query($query);            // Ejecuto el query 
	 $registroB = mysql_fetch_row($resultadoB);  
	 $wmed = $wmed."-".$registroB[0];
	}
	echo "<br><A HREF='//mx.lasamericas.com.co/matrix/citas/procesos/000001_prx5.php?empresa=citasom&wfec=".$wfpr."&wequ=".$wmed."' TARGET='_blank' >Agendar Cita</A>";
    
	echo "</td>";
	
    echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Observaciones:</font></b><br>";
    if (isset($wobs))
     echo "<TEXTAREA NAME='wobs' COLS='60' ROWS='3'>".$wobs."</TEXTAREA></td>"; 
    else
     echo "<TEXTAREA NAME='wobs' COLS='60' ROWS='3'></TEXTAREA></td>"; 
     
     
     // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   
	                    
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
/*	 
       if (isset($wmed))
	     echo "<INPUT TYPE = 'hidden' NAME='wmed' VALUE='".$wmed."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wmed'></INPUT>"; 
*/	 
   	echo "<tr><td align=center colspan=2 bgcolor=#C0C0C0>";
   	echo "<input type='submit' value='Grabar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   		
    echo "<A HREF='paf52.php?wced=".$wced."&wfec=".$wfec."&whor=".$whor."' TARGET='_blank' >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;imprimir</A>";	     

   	echo "</td></tr>";	


if ( $conf == "on" and isset($wfec) and $wfec<>'' and isset($whor) and $whor<>'' and isset($wced) and $wced<>'' and $windicador <> "PrimeraVez" )   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////


  // invoco la funcion que valida los campos 
  validar_datos($wfec,$whor,$wced,$wnom,$wcar,$word,$wcit,$wcon,$wmed,$wfpr,$whpr); 
               
  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	 
    if (isset($wex1)) 
     $c1=explode('-',$wex1);     
    if (isset($wex2)) 
     $c2=explode('-',$wex2);     
    if (isset($wmed)) 
     $c3=explode('-',$wmed);    
     
    $query = "SELECT  atefec,atehor,ateced,atenom,atehis,atecar,ateord,atecit,atecon,ateex1,ateex2,atemed,atefpr,atehpr,ateobs "
           ." FROM pafsom_000003 Where ateced = '".$wced."' And atefec = '".$wfec."' And atehor = '".$whor."'";
		
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)      //  Encontro 
    {	 	 
       $query = "Update pafsom_000003 SET atenom='".$wnom."',atehis='".$whis."',atecar='".$wcar."',ateord='".$word. "',atecit='".$wcit
               ."',atecon='".$wcon."',ateex1='".$c1[0]."',ateex2='".$c2[0]."',atemed='".$c3[0]."',atefpr='".$wfpr."',atehpr='".$whpr."',ateobs='".$wobs."'"
               ." Where ateced = '".$wced."' And atefec = '".$wfec."' And atehor = '".$whor."'";
		                                                             
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	   {
	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";
         // echo "<script language='javascript'>alert('Registro Modificado');</script>"; 
        //  echo "<script>alert('Registro Modificado');</script>";
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
      $query = "INSERT INTO pafsom_000003 VALUES ('paf','".$fecha."','".$hora."','".$wfec."','".$whor."','".$wced."','".$wnom."','".$whis
              ."','".$wcar."','".$word."','".$wcit."','".$wcon."','".$c1[0]."','".$c2[0]."','".$c3[0]."','".$wfpr."','".$whpr."','".$wobs."','".$user."','')";  
            
      $resultado = mysql_query($query,$conex);  
	  if ($resultado)
	   echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Adicionado</td></tr>";
	  else
	  {
	   echo "<table border=1>";	 
	   echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
	   echo "<font size=3 text color=#FF0000><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, AL ADICIONAR DATOS, POSIBLEMENTE YA EXISTE ESTE REGISTRO!!!!</MARQUEE></font>";				
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
