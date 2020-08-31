<html>
<head>
<title>Entrega de cuentas de cobro</title>
</head>

<script>
    function ira()
    {
	 document.ameenv02.went.focus();
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
		document.forms.ameenv02.submit();   // Ojo para la funcion ameenv02 <> ameenv02  (sencible a mayusculas)
	}

	function vaciarCampos()
	{document.forms.ameenv02.wenv.value = '';
	 document.forms.ameenv02.wfir.value = '';
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
//PROGRAMA				      :Actualizacion Control de Entrega de cuentas de cobro o cartas de envio                                                                  
//AUTOR				          :Jair  Saldarriaga Orozco.                                                                        
//FECHA CREACION			  :Enero 21 de 2013
//FECHA ULTIMA ACTUALIZACION  :Febrero 13 de 2013. Abril 1 de 2016 se muestra el nro de Guia                                                                                  

$wactualiz="PROGRAMA: ameenv02.php Ver. 2013-02-22   JairS";

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

Function validar_datos($ent,$des,$rec) 

{	    
   global $todok;   
   
   $todok = true;
   $msgerr = "";

   if (empty($ent))
   {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar usuario que entrega. ";   
   }

      
   if (empty($des))
   {
      $todok = false;
      $msgerr=$msgerr." Debe seleccionar el destino de la cuenta de cobro. ";   
   }
   
   if (empty($rec))
   {
      $todok = false; 
      $msgerr=$msgerr." Debe seleccionar usuario que recibe. ";
   }
   
   echo "<font size=3 text color=#CC0000>".$msgerr;   
   return $todok;   
}  



mysql_select_db("matrix") or die("No se selecciono la base de datos");  
$conexN = odbc_connect('Facturacion','','') or die("No se realizo Conexion con la BD facturacion en Informix");

echo "<form name='ameenv02' action='ameenv02.php' method=post>";  

echo "<center><table border=1>";
echo "<td align=center colspan=7 bgcolor=#99CCCC><font size=3 text color=#FF0000><b>Entrega de cuentas de cobro</b></font></tr>";

    echo "<tr>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Envio<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fec. Sello<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Entrega<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Destino<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>No Guia<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Empresa<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Verificar<b></td>";
    echo "</tr>"; 
    
    if ($windicador=="PrimeraVez")
    {
	 $windicador="SegundaVez";
	 $query="DELETE from ameTMPah";
	 $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
     $query="INSERT into ameTMPah SELECT envahnro ENVIO,envahent ENTREGA,envahDES DESTINO,envahfec RADICADO,envahrec RECIBE,'' FACTURA,envahgui GUIA"
           ." From ameenvah WHERE envahrad = 'P' ";
		
     $resultado = odbc_do($conexN,$query);            // Ejecuto el query llenando una tabla de paso para evitar un problema de concurrencia al estar
                                                      // por un lado grabando y por otro entregando (Me Queda la duda de Dos o mas entregando)
    } 
      
    $query="SELECT ENVIO, ENTREGA,DESTINO,RADICADO,GUIA"        
          ." From ameTMPah Order by ENVIO";
    $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
    
        $i = 1;
		while( odbc_fetch_row( $resultado ))
		{		
	     // color de fondo  
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="DDDDDD";  
	   	 else
	   	  $wcf="CCFFFF";    	
	 
		 echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultado, 1 )."</td>";
		 echo "<td colspan=1 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultado, 4 )."</td>";
		 
  	     $sql="Select usuahnom from ameusuah Where usuahcod='".odbc_result( $resultado, 2 )."'";
		 $resultadoB = odbc_exec($conexN,$sql);
		 if (odbc_fetch_row($resultadoB))         // Encontro 
           echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultadoB, 1 )."</td>";
         else
           echo "<td colspan=1 align=center bgcolor=".$wcf.">";
          
		 echo "<td colspan=1 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultado, 3 )."</td>";
 		 
 		/* QUIEN RECIBE INICIALMENTE
   	     $sql="Select usuahnom from ameusuah Where usuahcod='".odbc_result( $resultado, 5 )."'";
		 $resultadoB = odbc_exec($conexN,$sql);
		 if (odbc_fetch_row($resultadoB))         // Encontro 
           echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultadoB, 1 )."</td>";
         else
           echo "<td colspan=1 align=center bgcolor=".$wcf.">";
        */
		
		 echo "<td colspan=1 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultado, 5 )."</td>"; 	
		 
         // Muestro la empresa responsable de la cuenta de cobro 
         $sql="Select envencreg,envencnit,empnom From caenvenc,inemp"
             ." Where envencfue='80' And envencdoc=".odbc_result( $resultado, 1 )." And envencnit=empcod";
         $resultadoB = odbc_do($conexN,$sql);    // Ejecuto el query  
         if (odbc_fetch_row($resultadoB))        // Encontro 
          echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".odbc_result( $resultadoB, 3 )."</td>";
         else
          echo "<td colspan=1 align=center bgcolor=".$wcf.">";
  
/*
    // Ahora para la varibale 'Verficar' una manera seria creando una variable tantas veces como registros devuelva el query. Ej: verif1, verif2, verif3.... 
    // y cada una llevarle como valor el nro de la cuenta de cobro. Esto funciona pero abajo para grabar se me complica 
       	 if (isset($_POST['verif'.$i]) && $_POST['verif'.$i]!='')	 
       	  echo "<td colspan=1 align=Center   bgcolor=".$wcf.">".$_POST['verif'.$i]."<input checked type=checkbox name='verif".$i."' value='".odbc_result( $resultado, 1 )."' ></td>";
       	 else 
		  echo "<td colspan=2 align=Center   bgcolor=".$wcf.">".$_POST['verif'.$i]."<input type=checkbox name='verif".$i."' value='".odbc_result( $resultado, 1 )."' ></td>";
*/		  

    // Pero por comodidad lo mejor es utilizar un arreglo y a cada posicion le llevo como valor el nro de la carta de cobro. Ej: verif[1]=, verif[2]=, verif[3]=....         
       	 if ($verif[$i]!='')	 
       	  echo "<td colspan=1 align=Center   bgcolor=".$wcf."><input checked type=checkbox name='verif[".$i."]' value='".odbc_result( $resultado, 1 )."' ></td>";
		 else 
       	  echo "<td colspan=2 align=Center   bgcolor=".$wcf."><input type=checkbox name='verif[".$i."]' value='".odbc_result( $resultado, 1 )."' ></td>";
		 
		 echo "</tr>";           
         $i++; 
         
	    }
	    
        $wnrocue=$i-1;
        echo "<td align=center colspan=7 bgcolor=#99CCCC><font size=3 text color=#003366>Nro de Cuentas de Cobro: ".$wnrocue."</font>";
	
    echo "<tr><td align=center colspan=4 bgcolor=#C0C0C0 ><b><font text color=#003366 size=2>Usuario que entrega:</font></b><br>";   
    $query = "SELECT usuahcod, usuahnom FROM ameusuah WHERE usuahtip = 'E' And usuahare='ADMDOC'  ORDER BY usuahcod";   
    echo "<select name='went'>"; 
    echo "<option></option>"; 
    
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    $i = 1;
    while (odbc_fetch_row($resultadoB))               // leo registro 
	  {
		$c1=explode('-',$went); 				  
  		if($c1[0] == odbc_result($resultadoB,1))
 	      echo "<option selected>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>";
	    else
	      echo "<option>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>"; 
	    $i++; 
      }   
     echo "</select></td>";
     
    echo "<td align=center colspan=3 bgcolor=#C0C0C0 ><b><font text color=#003366 size=2>Destino:</font></b><br>";   
    $query = "SELECT arccod, arcnom FROM aharc WHERE arcact = 'A' ORDER BY arcnom";   
    
    echo "<select name='wdes' OnBlur='enter()'>"; 
    echo "<option></option>"; 
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    $i = 1;
    while (odbc_fetch_row($resultadoB))               // leo registro 
    {   // COMO arccod NO ES DEL MISMO TIPO Y LONGITUD DEL STRING QUE QUEDA EN $c2[0] no lo comparo con ==            
        // SI NO QUE UTILIZO LA Fn substr_count QUE BUSCA EL STRING $c2[0] EN EL STGRING arccod SI DA 1 ...           
	    $c2=explode('-',$wdes); 	
		$s = substr_count(odbc_result($resultadoB,1), $c2[0]); 
  		if ( $s==1 )
 	     echo "<option selected>".odbc_result($resultadoB,1)."- ".(odbc_result($resultadoB,2))."</option>";
	    else
	     echo "<option>".odbc_result($resultadoB,1)."- ".(odbc_result($resultadoB,2))."</option>"; 
	    
	    $i++; 
    }   
    echo "</select></td>";
    
    echo "<tr><td align=center colspan=4 bgcolor=#C0C0C0 ><b><font text color=#003366 size=2>Usuario que recibe:</font></b><br>";   
    $query = "SELECT usuahcod, usuahnom FROM ameusuah WHERE usuahtip = 'R' And usuahare='".$c2[0]."' ORDER BY usuahcod";   
    
    echo "<select name='wrec'>"; 
    echo "<option></option>"; 
    
    $resultadoB = odbc_do($conexN,$query);            // Ejecuto el query  
    $i = 1;
    while (odbc_fetch_row($resultadoB))               // Leo registro 
	  {
		$c3=explode('-',$wrec); 				  
  		if($c3[0] == odbc_result($resultadoB,1))
 	      echo "<option selected>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>";
	    else
	      echo "<option>".odbc_result($resultadoB,1)."- ".odbc_result($resultadoB,2)."</option>"; 
	    $i++; 
      }   
     echo "</select></td>";
     
    echo "<td align=center colspan=3 bgcolor=#C0C0C0 ><b><font text color=#003366 size=2>Firma digital</font></b><br>";
    if (isset($wfir))
      echo "<INPUT TYPE='password' NAME='wfir' size=10 maxlength=8 VALUE='".$wfir."')' ></INPUT></td>"; 
    else
      echo "<INPUT TYPE='password' NAME='wfir' size=10 maxlength=8 ></INPUT></td>"; 

      
            
    echo "<center><table border=1>"; 
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "<input type='submit' value='Entregar'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
   	
   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

   	echo "<input type='button' name='BReset' onclick ='vaciarCampos()' value='Inicializar' id='BReset'>";
   	
   	echo "<tr><td align=center colspan=6 bgcolor=#C0C0C0>";
   	echo "</td></tr>";	


if ( $conf == "on" )   //and isset($went) and $went<>'' and isset($wdes) and $wdes<>'' and isset($wrec) and $wrec<>''  )   
{
  ///////////              Cuando ya hay datos capturados todos los datos      //////////////////

  // invoco la funcion que valida los campos 
  validar_datos($went,$wdes,$wrec); 
  
  // Validacion adicional para la firma digital ==> si hasta aqui los datos estanOK
  if ($todok)   
  {
     // La firma aqui es obligatoria
     $c3=explode('-',$wrec);   
     $query = "SELECT Password FROM usuarios Where Codigo IN ('$c3[0]','01$c3[0]')";
     $resultadoC = mysql_query($query,$conex);     // Ejecuto el query 
     $nroreg = mysql_num_rows($resultadoC);
	 
	 
	
	$todok = false; 
	if($nroreg>0)
	{
		while($rows = mysql_fetch_array($resultadoC))
		{
			if($rows['Password']==$wfir)
			{
				$todok = true;
				break;				
			}
		}
	}
	
	if(!$todok)
	{
		echo "<font size=3 text color=#CC0000>La Firma digital no es valida."; 
	}
	 
     // if (mysql_fetch_row($resultadoC))         // Encontro 
     // {
	  // // Busca la firma digitada en la firma leida de la base de datos
	  // //$s = substr_count(mysql_result($resultadoC,0), $wfir); 
	  // //if ( $s==0 )  // No lo encontro
	  // if (mysql_result($resultadoC,0)!=$wfir)
	   // {
        // $todok = false; 
        // echo "<font size=3 text color=#CC0000>La Firma digital no es valida."; 
       // }
     // }
   }  
  
 
  
  if ($todok) 
  { 
    if (isset($went)) 
     $c1=explode('-',$went);     // De los combos tomo los codigos
    if (isset($wdes)) 
     $c2=explode('-',$wdes);     
    if (isset($wrec)) 
     $c3=explode('-',$wrec);  
     
    $fecha = date("Y-m-d");
    $hora = (string)date("H:i:s");	
     
    $wseleccion=0;
    $i=1;
	while($i <= $wnrocue )
	{		
	 if ($verif[$i]!='')	
     {          
      $query = "Update ameenvah SET envahent='".$c1[0]."',envahdes='".$c2[0]."',envahfre='".$fecha." ".$hora."',envahrec='".$c3[0]."',envahrad='N',"
               ." envahusr='".$user."' Where envahnro=".$verif[$i];
      $resultado = odbc_do($conexN,$query);            // Ejecuto el query  
      $wseleccion++;
	 } 
     $i++;
    }
    echo "<br><tr><td bgcolor=#33FFFF colspan=6 align=center>Se entregaron ".$wseleccion." Cartas de cobro.</td></tr>";
  }  
  else
  {
   echo "<table border=1>";	 
   echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";	 
   echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!!</MARQUEE></font>";				
   echo "</td></tr></table><br><br>";
  }
   
  echo "</center></table>";  
  
}

echo "</Form>";	

odbc_close($conexN);
odbc_close_all();    
?>
</BODY>
</html>