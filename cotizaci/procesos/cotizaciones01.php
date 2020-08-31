<html>
<head>
<title>Actualizacion de articulos por proveedor</title>
</head>

<script>
    function ira()
    {
	 document.cotizaciones01.wmar.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.cotizaciones01.submit();   // Ojo para la funcion cotizaciones01 <> cotizaciones01  (sencible a mayusculas)
	}

	function vaciarCampos()
	{
	 document.forms.cotizaciones01.wmar.value = '';
	 document.forms.cotizaciones01.wpre.value = '';
     document.forms.cotizaciones01.wbar.value = '';
     document.forms.cotizaciones01.wreg.value = '';
     document.forms.cotizaciones01.wvec.value = 'dd-mm-aaaa';
     document.forms.cotizaciones01.wcum.value = '';
     document.forms.cotizaciones01.wact.value = '';     
     document.forms.cotizaciones01.wcot.value = '';
     document.forms.cotizaciones01.wiva.value = '';
     document.forms.cotizaciones01.wsug.value = '';          
	 document.forms.cotizaciones01.wobs.value = '';
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
//PROGRAMA				      :Actualiza a cotizar por articulos por proveedor.                                                                   
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Noviembre 6 DE 2009
//FECHA ULTIMA ACTUALIZACION  :Octubre 17 DE 2017.                                                                             

$wactualiz="PROGRAMA: cotizaciones01.php Ver. 2017-10-03   JairS - AngelaO";

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

Function validar_datos($mar,$pre,$bar,$reg,$vec,$cum,$act,$cot,$iva,$sug,$obs) 
{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";
     
   if (empty($mar))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Marca Comercial y Laboratorio Fabricante.<br> ";
   }
                  
   if (empty($pre))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe existir Presentacion Comercial.<br> ";
   }   
                   
   if (empty($bar))
   {
      $todok = false;  
      $msgerr=$msgerr." Debe indicar si posee Codigo de identificación del medicamento.<br> ";
   }
                  
   if (empty($reg))
   {
      $todok = false;        
      $msgerr=$msgerr." Debe existir Registro INVIMA. <br>";
   }   
      
    // Chequeo la fecha con checkdate(mm,dd,aaaa)  
   if ( !checkdate(substr($vec,5,2), substr($vec,8,2), substr($vec,0,4)) )
   {
     $todok = false;     
     $msgerr=$msgerr." Fecha de Vencimiento del Registro debe ser aaaa-mm-dd.<br> ";
   }
   /* Solicitud de Fabio Ramirez
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
   */
   
   if (empty($cum))
   {
      $todok = false;
      $msgerr=$msgerr." Debe existir Codigo CUM.<br> ";
   }
              
   if (!is_numeric($act))
   {
      $todok = false;
      $msgerr=$msgerr." Precio anterior cotizado debe ser numerico.<br> ";
   }         
   else 
   {
     if ($act <= 0)
     {
      $todok = false;
      $msgerr=$msgerr." Precio anterior debe ser mayor a Cero. <br>";
     }
   }
        
   if (!is_numeric($cot))
   {
      $todok = false;
      $msgerr=$msgerr." Nuevo precio a cotizar debe ser numerico.<br> ";
   }   
 /*  else
   { 
	if ($act <> 0 )
	{
	 $liminf = $act - ($act * 0.50);
	 $limsup = $act + ($act * 0.50);
	 if (($cot < $liminf) or ($cot > $limsup))
     {
      $todok = false;
      $msgerr=$msgerr." Nuevo precio cotizado debe ser verificado. ";   
     } 
    } 
   }
*/        
   if (!is_numeric($iva))
   {
      $todok = false;
      $msgerr=$msgerr." Valor Iva debe ser numerico. <br>";
   }   
   else
   
     if ( ($iva <> 0) and ($iva <> 10)  and ($iva <> 19) )
     {
        $todok = false;
        $msgerr=$msgerr." Valor IVA debe ser 0, 10 o 19 porciento.<br> ";
     }

    //  if (($sug ==''))
    //{
    //    $todok = false;
    //    $msgerr=$msgerr." Debe seleccionar si el medicamento es regulado";
    //}


   if (!is_numeric($sug))
    {
       $todok = false;
       $msgerr=$msgerr." Debe seleccionar si es un Medicamento con precio regulado.<br> ";
    }
     
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  



$conexN = odbc_connect('inventarios','','') or die("No se realizo Conexion con la BD suministros en Informix");

mysql_select_db("matrix") or die("No se selecciono la base de datos");    

echo "<form name='cotizaciones01' action='cotizaciones01.php' method=post>";  
		
echo "<center><table border=1>";
echo "<tr><td align=center colspan=6 bgcolor=#005588><font size=3 text color=#FFFFFF><b>ARTICULOS A COTIZAR POR PROVEEDOR</b></font></tr>";
echo "<tr><td align=center colspan=6 bgcolor=#005588><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";


echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=4 text color=#000000><b>DATOS DEL MEDICAMENTO A COTIZAR</b></font></tr>";
   $query = "SELECT concod,connom,conuni,conmes,conano FROM cotizaci_000001 Where concod = '".$wcod."' Order by connom";
   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);
   $numcam = mysql_num_fields($resultado);   
   $registro = mysql_fetch_row($resultado);  	   

   echo "<tr><td colspan=2 align=left bgcolor=#C3D5F7><b>Codigo: ".$registro[0]."<b></td>";
   echo "<td colspan=4 align=left bgcolor=#C3D5F7><b>Descripcion: ".$registro[1]."<b></td>";
   echo "<tr><td colspan=2 align=left bgcolor=#C3D5F7><font size=5><b>Unidad: ".$registro[2]."<b></td>";
   echo "<td colspan=2 align=left bgcolor=#C3D5F7><b>Consumo Mes: ".$registro[3]."<b></td>";
   echo "<td colspan=2 align=left bgcolor=#C3D5F7><b>Consumo Año: ".$registro[4]."<b></td>";
   echo "<tr>";	

if ($windicador == "PrimeraVez") 
{
		
   $query = "SELECT cotmar,cotpre,cotbar,cotreg,cotvec,cotcum,cotact,cotcot,cotiva,cotsug,cotobs FROM cotizaci_000003"
          ." Where cotano = '".$wano."' And cotnit ='".$wnit."' And cotcod = '".$wcod."'";
        
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
	$wobs=$registro[10];

   } 
   else   //NO tiene datos este año entonces Muestro los datos del año pasado
   {
	$wano2= (integer) $wano - 1;   
    $query = "SELECT cotmar,cotpre,cotbar,cotreg,cotvec,cotcum,cotact,cotcot,cotiva,cotsug,cotobs FROM cotizaci_000003"
          ." Where cotano = '".$wano2."' And cotnit ='".$wnit."' And cotcod = '".$wcod."'";
        
    $resultado = mysql_query($query);
    $nroreg = mysql_num_rows($resultado);
    if ($nroreg > 0)
    {
     $registro = mysql_fetch_row($resultado);
     $wmar=$registro[0];
     $wpre=$registro[1];
     $wbar=$registro[2];
     $wreg=$registro[3];
     $wvec=$registro[4];
     $wcum=$registro[5];
     $wact=$registro[7];
     $wcot=$registro[7];
     $wiva=0;
     $wsug=$registro[9];
	 $wobs=$registro[10];

    } 	   	   
   }	   
   
} 

echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=4 text color=#000000><b>PARAMETROS TECNICOS DEL MEDICAMENTO</b></font></tr>";
echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=3 text color=#000000><b>Recuerde que la presentación comercial es en el siguiente orden: Forma farmacéutica, Concentración, Unidad de Empaque</b></font></tr>";
echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=3 text color=#000000><b>Tenga en cuenta que el valor a cotizar es de acuerdo a la unidad de medida que presenta el medicamento</b></font></tr>";

    echo "<tr><td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Marca Comercial y Laboratorio Fabricante:</font></b><br>";
    if (isset($wmar))
     echo "<TEXTAREA cols='10' rows='5'  NAME='wmar' style='width:300px; height:50px' size=60 maxlength=80 VALUE='".$wmar."' onKeyUp='form.wmar.value=form.wmar.value.toUpperCase()'>".$wmar."</TEXTAREA></td>"; 
    else
     echo "<TEXTAREA cols='10' rows='5' NAME='wmar' style='width:300px; height:50px' size=60 maxlength=80 onKeyUp='form.wmar.value=form.wmar.value.toUpperCase()'>".$wmar."</TEXTAREA></td>"; 
      
    echo "<td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Presentación Comercial:</font></b><br>";
    if (isset($wpre))
     echo "<INPUT TYPE='text' NAME='wpre' size=30 maxlength=60 VALUE='".$wpre."' onKeyUp='form.wpre.value=form.wpre.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wpre' size=30 maxlength=60 onKeyUp='form.wpre.value=form.wpre.value.toUpperCase()'></INPUT></td>";
     
    echo "<td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Posee Código de Identificación del Medicamento, cual:?</font></b><br>";
    if (isset($wbar)) {
        $a = array(1 => "No Tiene", 2 => "Código de Barras Lineales", 3 => "Datamatrix", 4 => "Código QR", 5 => "PDF417");

        // echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Medicamento Regulado:</font></b><br>";
        echo "<select name='wbar'>";
        echo "<option></option>";                // Primera en blanco
        if (isset($wbar)) //Si esta seteada
        {
            for ($i = 1; $i <= count($a); $i++) {
                if ($wbar == $i)    // ==> Ese Item es el seleccionado
                    echo "<option SELECTED value='" . $i . "'>" . $a[$i] . "</option>";
                else
                    echo "<option value='" . $i . "'>" . $a[$i] . "</option>";
            }
            echo "</select>";
        } else          //No seteada o primera vez
        {
            for ($i = 1; $i <= count($a); $i++)
                echo "<option value='" . $i . "'>" . $a[$i] . "</option>";

            echo "</select>";
        }
    }

    //  echo "<INPUT TYPE='text' NAME='wbar' size=40 maxlength=30 VALUE='".$wbar."'></INPUT></td>";
    else {
        $a = array(1 => "No Tiene", 2 => "Código de Barras Lineales", 3 => "Datamatrix", 4 => "Código QR", 5 => "PDF417");

        // echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Medicamento Regulado:</font></b><br>";
        echo "<select name='wbar'>";
        echo "<option></option>";                // Primera en blanco
        if (isset($wbar)) //Si esta seteada
        {
            for ($i = 1; $i <= count($a); $i++) {
                if ($wbar == $i)    // ==> Ese Item es el seleccionado
                    echo "<option SELECTED value='" . $i . "'>" . $a[$i] . "</option>";
                else
                    echo "<option value='" . $i . "'>" . $a[$i] . "</option>";
            }
            echo "</select>";
        } else          //No seteada o primera vez
        {
            for ($i = 1; $i <= count($a); $i++)
                echo "<option value='" . $i . "'>" . $a[$i] . "</option>";

            echo "</select>";
        }

         }
        //echo "<INPUT TYPE='text' NAME='wbar' size=40 maxlength=30 ></INPUT></td>";

    echo "<tr><td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Registro INVIMA:</font></b><br>";
    if (isset($wreg))
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=20 VALUE='".$wreg."' ></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wreg' size=30 maxlength=20></INPUT></td>"; 
     
    echo "<td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Vencimiento del Registro (aaaa-mm-dd):</font></b><br>";
    if (isset($wvec))
     echo "<INPUT TYPE='text' NAME='wvec' size=15 maxlength=10 VALUE='".$wvec."'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wvec' size=15 maxlength=10></INPUT></td>";          
            
    echo "<td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Codigo CUM:</font></b><br>";
    if (isset($wcum))
     echo "<INPUT TYPE='text' NAME='wcum' size=30 maxlength=30 VALUE='".$wcum."' onKeyUp='form.wcum.value=form.wcum.value.toUpperCase()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcum' size=30 maxlength=30 onKeyUp='form.wcum.value=form.wcum.value.toUpperCase()'></INPUT></td>";       
        
    echo "<tr><td align=center colspan=6 bgcolor=#C3D5F7><font size=4 text color=#000000><b>PARAMETROS COMERCIALES</b></font></tr>";
         
    echo "<tr><td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Precio anterior cotizado.</font></b><br>";
    if (isset($wact))
     echo "<INPUT TYPE='text' NAME='wact' size=15 VALUE='".$wact."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wact' size=15 onkeypress='teclado()'></INPUT></td>";   
     
    $wano2= (integer) $wano + 1;
     
    echo "<td align=center bgcolor=#8991AF colspan=2><b><font text color=#003366 size=3>Nuevo precio a cotizar.</font></b><br>";
    if (isset($wcot))
     echo "<INPUT TYPE='text' NAME='wcot' size=15 VALUE='".$wcot."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wcot' size=15 onkeypress='teclado()'></INPUT></td>";          

    echo "<td align=center bgcolor=#8991AF colspan=1><b><font text color=#003366 size=3>Iva %</font></b><br>";
    if (isset($wiva))
     echo "<INPUT TYPE='text' NAME='wiva' size=10 maxlength=4 VALUE='".$wiva."' onkeypress='teclado()'></INPUT></td>"; 
    else
     echo "<INPUT TYPE='text' NAME='wiva' size=10 maxlength=4 onkeypress='teclado()'></INPUT></td>";  
     
    echo "<td align=center bgcolor=#8991AF colspan=1><b><font text color=#003366 size=3>Medicamento con Precio Regulado (Control directo):</font></b><br>";
    if (isset($wsug))

        {

        $a=array(1=>"Si",2=>"No");

       // echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Medicamento Regulado:</font></b><br>";
        echo "<select name='wsug'>";
        echo "<option></option>";                // Primera en blanco
        if (isset($wsug)) //Si esta seteada
        {
            for ($i = 1; $i <= count($a); $i++)
            {
                if ( $wsug == $i )    // ==> Ese Item es el seleccionado
                    echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
                else
                    echo "<option value='".$i."'>".$a[$i]."</option>";
            }
            echo "</select>";
        }
        else          //No seteada o primera vez
        {
            for ($i = 1; $i <= count($a); $i++)
                echo "<option value='".$i."'>".$a[$i]."</option>";

            echo "</select>";
        }


//   en la base de datos va a aparecer 1 para SI y 2 para NO


        }

    else

    {

        $a=array(1=>"Si",2=>"No");

        // echo "<td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Medicamento Regulado:</font></b><br>";
        echo "<select name='wsug'>";
        echo "<option></option>";                // Primera en blanco
        if (isset($wsug)) //Si esta seteada
        {
            for ($i = 1; $i <= count($a); $i++)
            {
                if ( $wsug == $i )    // ==> Ese Item es el seleccionado
                    echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
                else
                    echo "<option value='".$i."'>".$a[$i]."</option>";
            }
            echo "</select>";
        }
        else          //No seteada o primera vez
        {
            for ($i = 1; $i <= count($a); $i++)
                echo "<option value='".$i."'>".$a[$i]."</option>";

            echo "</select>";
        }


//   en la base de datos va a aparecer 1 para SI y 2 para NO


    }
	
	echo "<tr><td align=center bgcolor=#8991AF colspan=10><b><font text color=#003366 size=3>Observacion:</font></b><br>";
	if (isset($wobs))
     echo "<TEXTAREA cols='10' rows='5'  NAME='wobs' style='width:300px; height:50px' size=60 maxlength=80 VALUE='".$wobs."' onKeyUp='form.wobs.value=form.wobs.value.toUpperCase()'>".$wobs."</TEXTAREA></td>"; 
    else
     echo "<TEXTAREA cols='10' rows='5' NAME='wobs' style='width:300px; height:50px' size=60 maxlength=80 onKeyUp='form.wobs.value=form.wobs.value.toUpperCase()'>".$wobs."</TEXTAREA></td>"; 
     
    // $wproceso y wnit son variables escondidas que enviaremos cada vez a travez del formulario	   	   	     
	   if (isset($wproceso))
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso' VALUE='".$wproceso."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wproceso'></INPUT>";   

  	   if (isset($wano))
	     echo "<INPUT TYPE = 'hidden' NAME='wano' VALUE='".$wano."'></INPUT>"; 
	   else
	     echo "<INPUT TYPE = 'hidden' NAME='wano'></INPUT>";                

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
     
   	echo "<tr><td align=center colspan=10 bgcolor=#005588>";
   	echo "<input type='submit' value='Grabar'>";          
   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	echo "<tr><td align=center colspan=10 bgcolor=#8991AF>";
   	echo "<li><A HREF='cotizaciones00.php' style='color: aliceblue'>Regresar</A>";
   	echo "</td></tr>";	
	
//---if ( isset($wmar) and $wmar<>'' and isset($wpre) and $wpre<>'')
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////
   
  // invoco la funcion que valida los campos 
  validar_datos($wmar,$wpre,$wbar,$wreg,$wvec,$wcum,$wact,$wcot,$wiva,$wsug,$wobs);
  

  if ($todok) 
  { 
   if ($windicador <> "PrimeraVez") 	  
   {	     
   
     // Adicione cotcot para tomar el valor cotizado   
     $query = "SELECT cotano,cotnit,cotcod,cotcot "
             ." FROM cotizaci_000003 Where cotano = '".$wano."' And cotnit = '".$wnit."' And cotcod = '".$wcod."'";

     $resultado = mysql_query($query,$conex);
     $nroreg = mysql_num_rows($resultado);
     $numcam = mysql_num_fields($resultado);      
     if ( $nroreg > 0 )                       //Encontro
     {
		 
	   // Tomo el valor cotizado antes de modificar  
	      $registro = mysql_fetch_row($resultado);
          $wcotant  = $registro[3];		  
	   // *****************************************
	   
       $query = "Update cotizaci_000003 SET cotmar='".$wmar."',cotpre='".$wpre."',cotbar='".$wbar."',cotreg='".$wreg."'"
               .",cotvec='".$wvec."',cotcum='".$wcum."',cotact=".$wact.",cotcot=".$wcot.",cotiva=".$wiva.",cotsug=".$wsug.",cotobs='".$wobs
               ."' Where cotano = '".$wano."' And cotnit = '".$wnit."' And cotcod = '".$wcod."'";
           
       $resultado = mysql_query($query,$conex);  
	   if ($resultado)
	   {	
	     
		 // Si cambio el valor cotizado grabo en el archivo de log
		 if ($wcotant <> $wact)
		 {
			$fecha = date("Y-m-d");
	        $hora = (string)date("H:i:s");		   
            $query = "INSERT INTO cotizaci_000011 VALUES ('cotizaci','".$fecha."','".$hora."','".$user."',".$wcotant.",".$wcot.",'".$wano."','".$wnit."','".$wcod."','C-cotizaci','')";  
		    $resultado = mysql_query($query,$conex);  
         }
	     // ******************************************************
	   
	    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Registro Modificado</td></tr>";		
	   }	
	   else
	   {
	    echo "<table border=1>";	 
	    echo "<tr><td align=center colspan=100 bgcolor=#8991AF>";
	    echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#006699 LOOP=-1>ERROR, AL MODIFICAR DATOS!!!!</MARQUEE></font>";				
	    echo "</td></tr></table><br><br>";
	   }        
	   
     } 
     else
     {
	 
      $fecha = date("Y-m-d");
	  $hora = (string)date("H:i:s");		      
      $query = "INSERT INTO cotizaci_000003 VALUES ('cotizaci','".$fecha."','".$hora."','".$wano."','".$wnit."','".$wcod."','".$wmar
              ."','".$wpre."','".$wbar."','".$wreg."','".$wvec."','".$wcum."',".$wact.",".$wcot.",".$wiva.",".$wsug.",'".$wobs."','C-cotizaci','')";  

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
?>
</BODY>
</html>
