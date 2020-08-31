<html>
<head>
<title>Actualizacion de personas a cargo e hijos</title>
</head>

<script>
    function ira()
    {
	 document.encuesta02b.wap1.focus();
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
		document.forms.encuesta02b.submit();   // Ojo para la funcion encuesta02b <> encuesta02b  (sencible a mayusculas)
	}

   
 	// Fn que solo deje digitar los nros del 0 al 9, y  el /
	function numeros(e)
	{
     key = e.keyCode || e.which;
     tecla = String.fromCharCode(key).toLowerCase();
     letras = "0123456789";
     especiales = [8,47];    // El 8 es para que la tecla <backspace> tambien la deje digitar
 
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
//PROGRAMA				      :Actualizacion de personas a cargo e hijos                                                                 
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Mayo 28 de 2015
//FECHA ULTIMA ACTUALIZACION  :Mayo 28 de 2015.                                                                             

$wactualiz="PROGRAMA: encuesta02b.php Ver. Mayo 28 de 2015   JairS";


session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");


Function validar_datos($aapex,$anomx,$anacx,$aparx,$wkte) 

 {  
   global $todok;
   global $msgerr;
   
   $todok = true;
   $msgerr = "";
   
	//******************************************************************************************************/
    //   Como todos los campos se deben llenar, entonces haremos la validacion por columnas por comodidad  */
	//******************************************************************************************************/
	//Validacion de los Apellidos
    $wenblanco = "";
	for ($i = 1; $i <= $wkte; $i++)
	{ 
	  if ( $aapex[$i] == "" )
	    $wenblanco = $wenblanco.$i.",";
	
	}	   
    if ($wenblanco  <> "")
    { 
      $todok = false;
      $msgerr=$msgerr." Faltan Apellidos por especificar en la(s) filas(s): ".$wenblanco;   
    }   

	//Validacion de los Nombres
    $wenblanco = "";
	for ($i = 1; $i <= $wkte; $i++)
	{ 
	  if ( $anomx[$i] == "" )
	    $wenblanco = $wenblanco.$i.",";
	
	}	   
    if ($wenblanco  <> "")
    { 
      $todok = false;
      $msgerr=$msgerr." Faltan Nombres por especificar en la(s) filas(s): ".$wenblanco;   
    }   

	//Validacion de las Fechas de Nacimiento
    $wenblanco = "";
	$wfechamala="";
	$wfechamayor="";
	$hoy = date("Y/m/d");  
	for ($i = 1; $i <= $wkte; $i++)
	{ 
	  if ( $anacx[$i] == "" )
	    $wenblanco = $wenblanco.$i.",";
	     
      // Chequeo la fecha con checkdate(mm,dd,aaaa) pero como la fecha viene yyyy/mm/dd entonces 
      if ( !checkdate(substr($anacx[$i],5,2), substr($anacx[$i],8,2), substr($anacx[$i],0,4)) )
	    $wfechamala = $wfechamala.$i.",";
	   
	  if ( $anacx[$i] >= $hoy ) 
	   $wfechamayor = $wfechamayor.$i.",";	        
    }     
	  	   
    if ($wenblanco  <> "")
    { 
      $todok = false;
      $msgerr=$msgerr." Faltan Fechas de Nacimiento por especificar en la(s) filas(s): ".$wenblanco;   
    }
    
	if ( $wfechamala  <> "")
    { 
      $todok = false;
      $msgerr=$msgerr."Fecha Invalida o formato de fecha invalido (AAAA/MM/DD) en la(s) filas(s): ".$wfechamala;   
     }
	if ( $wfechamayor  <> "")
    { 
      $todok = false;
      $msgerr=$msgerr."Hay Fechas mayores o iguales a la fecha actual en la(s) filas(s): ".$wfechamayor;  
	}
       	

	//Validacion de los Parentescos
    $wenblanco = "";
	for ($i = 1; $i <= $wkte; $i++)
	{ 
	  if ( $aparx[$i] == "" )
	    $wenblanco = $wenblanco.$i.",";
	
	}	   
    if ($wenblanco  <> "")
    { 
      $todok = false;
      $msgerr=$msgerr." Faltan Parentescos por especificar en la(s) filas(s): ".$wenblanco;   
    }   	
	
   //echo "<font size=2 text color=#CC0000>".$msgerr;   
   //return $todok;   
   
   return $msgerr;   
 }  



//$conex = mysql_connect('localhost','root','q6@nt6m') or die("No se realizo Conexion");
mysql_select_db("matrix") or die("No se selecciono la base de datos");  

//$conexN = odbc_connect('nomina','','') or die("No se realizo Conexion con la BD suministros en Informix");

echo "<form name='encuesta02b' action='encuesta02b.php' method=post>";  

 
  /*************************************************************************
    Supongamos que se van a capturar los datos de 3 personas dependientes  *
    automaticamente se generan 12 campos a capturar con los nombres asi:   *
   *************************************************************************
	  Apellidos Nombres FecNcto Parentesco
        $wap1     $wno1   $wna1   $wpa1
        $wap2     $wno2   $wna2   $wpa2
        $wap3     $wno3   $wna3   $wpa3
  *************************************************************************
  * Las columnas apellido, nombre y fecha se capturara como campos text y *
  * el parentesco con un combo de seleccion. con ciclos y la utilidad que *
  * maneja php VARIABLE $ VARIABLE $$ Se trabajara todo el script         *
  ************************************************************************/                                     
	
    echo "<center><table border=1>";

    echo "<td align=center bgcolor=#D3D3D3 colspan=4><b>";
	echo "<font text color='blue' size=2>PERFIL DE ASOCIADOS FONDO DE EMPLEADOS LAS AMERICAS<br>";
    echo "<font text color=#003366 size=2><b>DETALLE DE LAS PERSONAS A CARGO E HIJOS<br> ";
	echo "</font></b><br></td>";
	
    echo "<tr>";
	
 	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Apellidos</td>";
	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Nombres</td>";
	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Fecha Nacim/to<br>AAAA/MM/DD</td>";
	echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Parentesco</td>";
	
	echo "<tr>";
	
	//Solo se hace la primera vez que se corre el script si ya hay datos para la cedula digitada lleno las variables de la forma
	if ($windicador == "PrimeraVez")   
    {		
      $query = "SELECT pacced,pacape,pacnom,pacnac,pacpar FROM fondos_000098 Where pacced='".trim($wcod)."' Order by pacnro";
	  $resultado = mysql_query($query,$conex);  
      $nroreg = mysql_num_rows($resultado);
	  if ($nroreg > 0) 
      {
	    for ($i = 1; $i <= $nroreg; $i++)  		
	    { 
	  	  $registro = mysql_fetch_row($resultado);  	
		  $nomcam="wap".$i;    
		  $$nomcam=$registro[1]; 
	      $nomcam="wno".$i;    
  		  $$nomcam=$registro[2]; 
		  $nomcam="wna".$i;    
		  $$nomcam=$registro[3]; 
		  $nomcam="wpa".$i;    
		  $$nomcam=$registro[4]; 
		  $windicador = "SegundaVez";
          echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	    }
      }
	}
	
 /***********************************Captura TEXT Variable Variable en PHP ó sea $$  ***************************************************************/	
   
     
	 for ($i = 1; $i <= $wnro; $i++)
	 {
/*** APELLIDOS */ 
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";
	  
      $nam = 'wap'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam) )
		echo $i.": <input type=text name=".$nam."  size=20 maxlength=20 value='".$$nam."' onKeyUp='this.value = this.value.toUpperCase();' ><br>";
      else
        echo $i.": <input type=text name=".$nam."  size=20 maxlength=20  onKeyUp='this.value = this.value.toUpperCase();' ><br>";		  
/*** NOMBRES */	
	  echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";
	  
      $nam = 'wno'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam) )
		echo "<input type=text name=".$nam."  size=20 maxlength=20 value='".$$nam."' onKeyUp='this.value = this.value.toUpperCase();' ><br>";
      else
        echo "<input type=text name=".$nam."  size=20 maxlength=20 onKeyUp='this.value = this.value.toUpperCase();'  ><br>";		  
/*** FECHA DE NCTO */	
  	  echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";
	  
      $nam = 'wna'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
      if( isset($$nam) )
		echo "<input type=text name=".$nam."  size=15 maxlength=15 value='".$$nam."' onkeypress='return numeros(event);'  ><br>";
      else
        echo "<input type=text name=".$nam."  size=15 maxlength=15 onkeypress='return numeros(event);'  ><br>";		  
/*** PARENTESCO */
      echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";  
	  $a=array(1=>"Padre",2=>"Madre",3=>"Esposa",4=>"Esposo",5=>"Hijo",6=>"Hermano",7=>"Tio",8=>"Suegra",9=>"Suegro",10=>"Abuelo",11=>"Abuela",12=>"Otro"); 
	 
	 $nam = 'wpa'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	  echo "<select name='".$nam."'>";	
	  echo "<option></option>";                // Primera en blanco 
	  if (isset($$nam)) //Si esta seteada
	  {
        for ($k = 1; $k <= count($a); $k++)
        {
		 if ( $$nam == $k )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$k."'>".$a[$k]."</option>";
		 else 
		  echo "<option value='".$k."'>".$a[$k]."</option>";
		}
	  }	
	  else          //No seteada o primera vez
	  {
       for ($k = 1; $k <= count($a); $k++)
         echo "<option value='".$k."'>".$a[$k]."</option>";
      }
	  echo "</select></td>";  
/***/	 
	  echo "<tr>";
	  
	 } 
/***************************************************************************************************/	

	echo "<tr><td align=center bgcolor=#C0C0C0 colspan=4><b><font text color=#003366 size=2>";	
  	
   	echo "<input type='submit' value='Enviar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	//echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
	
	//Para no perder los parametros recibidos 
	echo "<INPUT TYPE = 'hidden' NAME='wnro' VALUE='".$wnro."'></INPUT>";
	echo "<INPUT TYPE = 'hidden' NAME='wcod' VALUE='".trim($wcod)."'></INPUT>";
 	
   	echo "</td></tr>";	
	 
	if ( $conf == "on" )   
    {
		
	  // VARIABLES como "Con Quien Vive" se capturan en 7 campos checkbox nombradas asi wcv1,wcv2,wcv3.. o "Que actividades de recreacion le gustaria encontar .."	
	  // que se capturan con 10 campos Text. nombradas asi wre1,wre2,wre3 entonces para validarlas las enviare en un arreglo
	  
	  For ($i = 1; $i <= $wnro; $i++)
	  {
	   $nam = 'wap'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrapx[$i] = $$nam; 
	  }
	  
	  For ($i = 1; $i <= $wnro; $i++)
	  {
	   $nam = 'wno'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrnox[$i] = $$nam; 
	  }
	  
	  For ($i = 1; $i <= $wnro; $i++)
	  {
	   $nam = 'wna'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrnax[$i] = $$nam; 
	  }
	  
	  For ($i = 1; $i <= $wnro; $i++)
	  {
	   $nam = 'wpa'.$i;              /* ==> ENTONCES EN $nam QUEDA EL 'NOMBRE' DE LA VARIABLE Y CON $$nam TOMO EL 'CONTENIDO' DE LA VARIABLE   */
	   $arrpax[$i] = $$nam; 
	  } 
	  
	  validar_datos($arrapx,$arrnox,$arrnax,$arrpax,$wnro); 
	  
	  if (strlen( $msgerr ) > 5 )    //Si retorna de la funcion con mensaje de Error entonces lo muestro
       print "<script>alert('$msgerr')</script>";
	  else
	  {
	     $anio = date("Y");    // Y=Devuelve 4 digitos (2015) y=devuelve 2 digitos(15)
	     $fecha = date("Y-m-d");
	     $hora = (string)date("H:i:s");
		 $westado="";
         for ($i = 1; $i <= $wnro; $i++)  			 
		 {  		
	 	   $ape="wap".$i;   
		   $nom="wno".$i; 
		   $nac="wna".$i; 
		   $par="wpa".$i; 
	 	   $query = "SELECT * FROM fondos_000098 Where pacced='".trim($wcod)."' AND pacnro='".$i."'";
	       $resultado = mysql_query($query,$conex);  
           $nroreg = mysql_num_rows($resultado);
	       if ($nroreg == 0)  
		   {
	         $query = "INSERT INTO fondos_000098 (Medico,Fecha_data,Hora_data,pacced,pacnro,pacape,pacnom,pacnac,pacpar,Seguridad,id) VALUES ('fondos','".$fecha."','".$hora."',";
		     $query=$query."'".trim($wcod)."',".$i.",'".trim($$ape)."','".trim($$nom)."','".$$nac."','".$$par."','".$key."','')";
		     $resultado = mysql_query($query,$conex);  	
		   }
		   else
		   {
	         $query="UPDATE fondos_000098 SET pacape='".$$ape."',pacnom='".$$nom."',pacnac='".$$nac."',pacpar='".$$par."'"
			       ." WHERE pacced='".trim($wcod)."' AND pacnro='".$i."'";
		     $resultado = mysql_query($query,$conex);  				   
		   }
		 }
		 
		  if ( $resultado )   //Esto podria estar dentro del FOR pero para que no muestre tantas veces 
		    print "<script>alert('Su informacion ha sido grabada correctamente!!!!')</script>";
		  else
		    print "<script>alert('Atencion!!! Se produjo un error al grabar la informacion')</script>";
	  }	
	}  
	
echo "</table>";
echo "</Form>";	  
//odbc_close($conexN);
//odbc_close_all();
  
?>
</BODY>
</html>