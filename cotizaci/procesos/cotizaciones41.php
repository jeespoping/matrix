<html>
<head>
<title>Recotizar o Modificar cotizaciones</title>
</head>

<script>
    function ira()
    {
	 document.cotizaciones41.wmar.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.cotizaciones41.submit();   // Ojo para la funcion cotizaciones41 <> cotizaciones1  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.cotizaciones41.wmar.value = '';
	 document.forms.cotizaciones41.wpre.value = '';
     document.forms.cotizaciones41.wbar.value = '';
     document.forms.cotizaciones41.wreg.value = '';
     document.forms.cotizaciones41.wvec.value = 'dd-mm-aaaa';
     document.forms.cotizaciones41.wcum.value = '';
     document.forms.cotizaciones41.wact.value = '';     
     document.forms.cotizaciones41.wcot.value = '';
     document.forms.cotizaciones41.wiva.value = '';
     document.forms.cotizaciones41.wsug.value = '';          
    }
    
 	// Fn que solo deja digitar los nros del 0 al 9, el . y el enter
	function teclado()  
	{ 
		if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) & event.keyCode != 46) event.returnValue = false;
	}
 
</script>

<?php
include_once("conex.php");

//========================================================================================================================================
//PROGRAMA				      :Recotiza o Modifica la cotizacion de un articulo del proveedor seleccionado y crea log de la modificacion  
//AUTOR				          :Jair Saldarriaga Orozco.                                                                                   
//FECHA CREACION			  :Enero 28 De 2015.                                                                                          
//FECHA ULTIMA ACTUALIZACION  :Enero 28 De 2015.                                                                                          
//========================================================================================================================================

$wactualiz="PROGRAMA: cotizaciones41.php Ver. 2015-01-28   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

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

Function validar_datos($mar,$pre,$bar,$reg,$vec,$cum,$act,$cot,$iva,$sug) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($mar))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Marca. ";   
   }
                  
   if (empty($pre))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir Presentacion. ";   
   }   
                   
   if (empty($bar))
   {
      $todok = false;  
      $msgerr=$msgerr." Debe existir Codigo de Barras. ";   
   }
                  
   if (empty($reg))
   {
      $todok = false;        
      $msgerr=$msgerr." Debe existir Registro INVIMA. ";   
   }   
      
    // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($vec,5,2), substr($vec,8,2), substr($vec,0,4)) )
   {
     $todok = false;     
     $msgerr=$msgerr." Fecha debe ser aaaa-mm-dd. ";   
   }
   else
   {
	 $hoy = date("Y-m-d");  
     $vectmp=sumaDia($hoy,90);
     if ($vec < $vectmp)
     {
      $todok = false;        
      $msgerr=$msgerr." Fecha debe tener un vencimiento mayor a 90 Dias. ";   
     } 
   }  
     
   if (empty($cum))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Codigo CUM. ";   
   }
              
   if (!is_numeric($act))
   {
      $todok = false;
      $msgerr=$msgerr." Precio anterior cotizado debe ser numerico. ";   
   }         
   else 
   {
     if ($act <= 0)
     {
      $todok = false;
      $msgerr=$msgerr." Precio anterior debe ser mayor a Cero. ";   
     }
   }
        
   if (!is_numeric($cot))
   {
      $todok = false;
      $msgerr=$msgerr." Nuevo precio a cotizar debe ser numerico. ";   
   }   
 
   if (!is_numeric($iva))
   {
      $todok = false;
      $msgerr=$msgerr." Valor Iva debe ser numerico. ";   
   }   
   else
   
     if ( ($iva <> 0) and ($iva <> 10)  and ($iva <> 16) )
     {
        $todok = false;
        $msgerr=$msgerr." Valor IVA debe ser 0, 10 o 16 porciento. ";   
     }   
       
   if (!is_numeric($sug))
   {
      $todok = false;
      $msgerr=$msgerr." Precio sugerido al publico debe ser numerico. ";   
   }   
     
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  



$conexN = odbc_connect('inventarios','','') or die("No se realizo Conexion con la BD suministros en Informix");

mysql_select_db("matrix") or die("No se selecciono la base de datos");    

echo "<form name='cotizaciones41' action='cotizaciones41.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=6 bgcolor=#006699><font size=3 text color=#FFFFFF><b>MODIFICACION DE COTIZACIONES AÑO: ".$wano." </b></font></tr>";
echo "<tr><td align=center colspan=6 bgcolor=#006699><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";


echo "<tr><td align=center colspan=6 bgcolor=#FFCC66><font size=3 text color=#FFFFFF><b>DATOS DEL PRODUCTO COTIZADO</b></font></tr>";

   if ($wtipo == 1)     // tabla con datos de consumos por año de Medicamentos
     $wtabla="cotizaci_000001";
   else
    if ($wtipo == 2)    // tabla con datos de consumos por año de Materiales
     $wtabla="cotizaci_000002";
    else                // tabla con datos de consumos por año de Antisepticos 
     $wtabla="cotizaci_000008";
       
   $query = "SELECT concod,connom,conuni,conmes,conano FROM ".$wtabla." Where concod = '".$wcod."' Order by connom";
  
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   $numcam = mysql_num_fields($resultado);   
   $registro = mysql_fetch_row($resultado);  	   

   echo "<tr><td colspan=2 align=left bgcolor=#FFCC33><b>Codigo: ".$registro[0]."<b></td>";
   echo "<td colspan=4 align=left bgcolor=#FFCC33><b>Descripcion: ".$registro[1]."<b></td>";
   echo "<tr><td colspan=2 align=left bgcolor=#FFCC33><b>Unidad: ".$registro[2]."<b></td>";
   echo "<td colspan=2 align=left bgcolor=#FFCC33><b>Consumo Mes: ".$registro[3]."<b></td>";
   echo "<td colspan=2 align=left bgcolor=#FFCC33><b>Consumo Año: ".$registro[4]."<b></td>";
   echo "<tr>";	

if ($windicador == "PrimeraVez") 
{
   if ($wtipo == 1)     //Medicamentos
    $query = "SELECT cotmar,cotpre,cotbar,cotreg,cotvec,cotcum,cotact,cotcot,cotiva,cotsug FROM cotizaci_000003 Where Id=".$wid;
   else
     if ($wtipo == 2)   //Materiales OJO: El query no es igual hay un campo cotcla que cambia por cotcum
       $query = "SELECT cotmar,cotpre,cotbar,cotreg,cotvec,cotcla,cotact,cotcot,cotiva,cotsug FROM cotizaci_000004 Where Id=".$wid;
     else               //Anstisepticos
       $query = "SELECT cotmar,cotpre,cotbar,cotreg,cotvec,cotcla,cotact,cotcot,cotiva,cotsug FROM cotizaci_000007 Where Id=".$wid;  
  
         
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   if ($nroreg > 0)      //    Va a modificar los datos de este año
   {
    $registro = mysql_fetch_row($resultado);  	   
    $wmar=$registro[0];
    $wpre=$registro[1];
    $wbar=$registro[2];
    $wreg=$registro[3];
    $wvec=$registro[4];
    $wcum=$registro[5];
    $wact=$registro[6];
    $wcot=$registro[7];
    $wiva=$registro[8];
    $wsug=$registro[9];
   } 
 	   
} 

echo "<tr><td align=center colspan=6 bgcolor=#FFCC66><font size=3 text color=#FFFFFF><b>PARAMETROS TECNICOS LEGALES DEL PRODUCTO</b></font></tr>";
    
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Marca:</font></b><br>";
    if (isset($wmar))
     echo "<INPUT TYPE='text' NAME='wmar' size=30 maxlength=60 VALUE='".$wmar."' onKeyUp='form.wmar.value=form.wmar.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wmar' size=30 maxlength=60 onKeyUp='form.wmar.value=form.wmar.value.toUpperCase()'></INPUT></td>"; 
      
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Presentacion:</font></b><br>";
    if (isset($wpre))
     echo "<INPUT TYPE='text' NAME='wpre' size=30 maxlength=60 VALUE='".$wpre."' onKeyUp='form.wpre.value=form.wpre.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wpre' size=30 maxlength=60 onKeyUp='form.wpre.value=form.wpre.value.toUpperCase()'></INPUT></td>";
     
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Codigo de barras:</font></b><br>";
    if (isset($wbar))
     echo "<INPUT TYPE='text' NAME='wbar' size=40 maxlength=30 VALUE='".$wbar."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wbar' size=40 maxlength=30 ></INPUT></td>"; 

    echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Registro INVIMA:</font></b><br>";
    if (isset($wreg))
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=20 VALUE='".$wreg."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=20></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Vencimiento del registro (aaaa-mm-dd):</font></b><br>";
    if (isset($wvec))
     echo "<INPUT TYPE='text' NAME='wvec' size=15 maxlength=10 VALUE='".$wvec."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wvec' size=15 maxlength=10></INPUT></td>";          
            
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Codigo CUM:</font></b><br>";
    if (isset($wcum))
     echo "<INPUT TYPE='text' NAME='wcum' size=30 maxlength=30 VALUE='".$wcum."' onKeyUp='form.wcum.value=form.wcum.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcum' size=30 maxlength=30 onKeyUp='form.wcum.value=form.wcum.value.toUpperCase()'></INPUT></td>";       
        
    echo "<tr><td align=center colspan=6 bgcolor=#FFCC66><font size=3 text color=#FFFFFF><b>PARAMETROS COMERCIALES</b></font></tr>";    
         
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Precio anterior cotizado.</font></b><br>";
    if (isset($wact))
     echo "<INPUT TYPE='text' NAME='wact' size=15 VALUE='".$wact."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wact' size=15 onkeypress='teclado()'></INPUT></td>";   
     
    $wano2= (integer) $wano + 1;
     
    echo "<td align=center bgcolor=#DDDDDD colspan=2><b><font text color=#003366 size=3>Nuevo precio a cotizar.</font></b><br>";
    if (isset($wcot))
     echo "<INPUT TYPE='text' NAME='wcot' size=15 VALUE='".$wcot."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcot' size=15 onkeypress='teclado()'></INPUT></td>";          

    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=3>Iva %</font></b><br>";
    if (isset($wiva))
     echo "<INPUT TYPE='text' NAME='wiva' size=10 maxlength=4 VALUE='".$wiva."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wiva' size=10 maxlength=4 onkeypress='teclado()'></INPUT></td>";  
     
    echo "<td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=3>Precio sugerido al publico:</font></b><br>";
    if (isset($wsug))
     echo "<INPUT TYPE='text' NAME='wsug' size=15 VALUE='".$wsug."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wsug' size=15 onkeypress='teclado()'></INPUT></td>";     
   
     
    // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

  	   if (isset($wano))
	     echo "<INPUT TYPE = 'hidden' NAME='wano' VALUE='".$wano."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wano'></INPUT>";                

	   if (isset($wid))
	     echo "<INPUT TYPE = 'hidden' NAME='wid' VALUE='".$wid."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wid'></INPUT>";                
	     	     
	   if (isset($wtipo))
	     echo "<INPUT TYPE = 'hidden' NAME='wtipo' VALUE='".$wtipo."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wtipo'></INPUT>";                
	     
	   if (isset($windicador))
	     echo "<INPUT TYPE = 'hidden' NAME='windicador' VALUE='".$windicador."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='windicador'></INPUT>";    
     
   	echo "<tr><td align=center colspan=10 bgcolor=#cccccc>";
   	echo "<input type='submit' value='Grabar'>";          
   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	echo "<tr><td align=center colspan=10 bgcolor=#cccccc>";
   	echo "<li><A HREF='cotizaciones40.php'>Regresar</A>";	
   	echo "</td></tr>";	
	
if ( isset($wmar) and $wmar<>'' and isset($wpre) and $wpre<>'')   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wmar,$wpre,$wbar,$wreg,$wvec,$wcum,$wact,$wcot,$wiva,$wsug);
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {
	   	     
     if ($wtipo == 1)    //Medicamentos
      $query = "SELECT cotano,cotnit,cotcod,cotact,cotcot,cotiva,cotsug FROM cotizaci_000003 Where Id=".$wid;
     else
       if ($wtipo == 2)  //Materiales       
        $query = "SELECT cotano,cotnit,cotcod,cotact,cotcot,cotiva,cotsug FROM cotizaci_000004 Where Id=".$wid;
       else              //Antisepticos
        $query = "SELECT cotano,cotnit,cotcod,cotact,cotcot,cotiva,cotsug FROM cotizaci_000007 Where Id=".$wid;
        
        
     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )  //Encontro
     {
	   $registro = mysql_fetch_row($resultado);    //Leo el registro
	   
	   if ($wtipo == 1)     //Medicamentos	 
	   
        $query = "Update cotizaci_000003 SET cotmar='".$wmar."',cotpre='".$wpre."',cotbar='".$wbar."',cotreg='".$wreg."'"
                .",cotvec='".$wvec."',cotcum='".$wcum."',cotact=".$wact.",cotcot=".$wcot.",cotiva=".$wiva.",cotsug=".$wsug
                ." Where Id=".$wid;
                
       else
        if ($wtipo == 2)    //Materiales    
         $query = "Update cotizaci_000004 SET cotmar='".$wmar."',cotpre='".$wpre."',cotbar='".$wbar."',cotreg='".$wreg."'"
                 .",cotvec='".$wvec."',cotcla='".$wcum."',cotact=".$wact.",cotcot=".$wcot.",cotiva=".$wiva.",cotsug=".$wsug
                 ." Where Id=".$wid;
        else                //Antisepticos  
          $query = "Update cotizaci_000007 SET cotmar='".$wmar."',cotpre='".$wpre."',cotbar='".$wbar."',cotreg='".$wreg."'"
                 .",cotvec='".$wvec."',cotcla='".$wcum."',cotact=".$wact.",cotcot=".$wcot.",cotiva=".$wiva.",cotsug=".$wsug
                 ." Where Id=".$wid;
        
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	   {
	      echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";	   
          //Grabo en la tabla de LOG el año de la cotizacion,proveedor,articulo, precio actual, precio cotizado, iva y 
          //precio sugerido como estaba antes de la modificacion, usuario
	      $fecha = date("Y-m-d");
          $hora = (string)date("H:i:s");		      
          $query = "INSERT INTO cotizaci_000010 VALUES ('cotizaci','".$fecha."','".$hora."','".$registro[0]."','".$registro[1]."','".$registro[2]
                  ."',".$registro[3].",".$registro[4].",".$registro[5].",".$registro[6].",'".$user."','C-cotizaci','')";  
          $resultado = mysql_query($query,$conex);     
           
       }   
	   else
	   {
	    echo "<table border=1>";	 
	    echo "<tr><td align=center colspan=100 bgcolor=#006699>";	 
	    echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, AL MODIFICAR DATOS!!!!</MARQUEE></font>";				
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
