<HTML>
<HEAD>
<TITLE>Reporte General de Ordenes</TITLE>
	<style type="text/css">
		.tipodrop{color:#000000;background:#FFFFFF;font-size:8pt;font-family:Arial;font-weight:normal;width:60em;text-align:left;height:2em;}
 	</style>
</HEAD>
<BODY>

 <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>    
  
<?php
include_once("conex.php");

session_start();
if(!isset($_SESSION['user']))
    die ("<br>\n<br>\n".
        " <H1>Para entrar correctamente a la aplicacion debe".
        " hacerlo por la pagina <FONT COLOR='RED'>" .
        " index.php</FONT></H1>\n</CENTER>");

function UltimoDia($anho,$mes)
{ 
   if (((fmod($anho,4)==0) and (fmod($anho,100)!=0)) or (fmod($anho,400)==0)) { 
       $dias_febrero = 29; 
   } else { 
       $dias_febrero = 28; 
   }
    
   switch($mes) { 
       case "01": return 31; break; 
       case "02": return $dias_febrero; break; 
       case "03": return 31; break; 
       case "04": return 30; break; 
       case "05": return 31; break; 
       case "06": return 30; break; 
       case "07": return 31; break; 
       case "08": return 31; break; 
       case "09": return 30; break; 
       case "10": return 31; break; 
       case "11": return 30; break; 
       case "12": return 31; break; 
   } 
} 



mysql_select_db("matrix") or die("No se selecciono la base de datos");    

//Forma
echo "<form name='paf13som' action='paf13som.php' method=post>";  
 
 if (!isset($wfec1) or !isset($wfec2))
 {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
    echo "<tr>";
	echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Reporte General de Ordenes <br></font></b>";   
	echo "</tr>";

	
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con la fecha actual
    $wfec1 = date("Y-m-d");
   
    echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha de autorizacion Inicial<br></font></b>";   
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo en la misma Inicial
    $wfec2=$wfec1;
  
    echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha de Autorizacion Final  <br></font></b>";   
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' readonly='readonly' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php


/****/	

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Unidad que presta el Servicio:</font></b><br>";   
   $query = "SELECT ccocod,cconom FROM costosyp_000005 Order By cconom"; 	         
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);

   echo "<select name='wcco' class='tipodrop'>"; 
   echo "<option>%%%-Todos</option>"; 
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c1=explode('-',$wcco); 				  
  	  if( trim($c1[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   	
	
/*****/	

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Examen:</font></b><br>";   
   $query = "SELECT codigo,nombre FROM root_000012  Order By nombre"; 	         
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);

   echo "<select name='wexa' class='tipodrop'>"; 
   echo "<option>%%%-Todos</option>"; 
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c1=explode('-',$wexa); 				  
  	  if( trim($c1[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   	
   
/****/	   
   
   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Diagnostico:</font></b><br>";   
   $query = "SELECT codigo,Descripcion FROM root_000011 Order By Descripcion"; 	         
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);

   echo "<select name='wdia' class='tipodrop'>"; 
   echo "<option>%%%-Todos</option>"; 
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c1=explode('-',$wdia); 				  
  	  if( trim($c1[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   

/****/	

   echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Medico que ordena:</font></b><br>";   
   $query = "SELECT Meduma,Medno1,Medno2,Medap1,Medap2,Meddoc FROM movhos_000048 Where Meduma <> '' GROUP BY Meddoc Order By Medno1,Medap1 "; 	         
   $resultadoB = mysql_query($query);            // Ejecuto el query 
   $nroreg = mysql_num_rows($resultadoB);

   echo "<select name='wmed' class='tipodrop'>"; 
   echo "<option>%%%-Todos</option>"; 
   $i = 1;
   While ($i <= $nroreg)
   {
	  $registroB = mysql_fetch_row($resultadoB);  
	  $c1=explode('-',$wmed); 				  
  	  if( trim($c1[0]) == trim($registroB[0]) )
	    echo "<option selected>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  else
	    echo "<option>".$registroB[0]."- ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</option>"; 
	  $i++; 
   }   
   echo "</select></td>";   
   
/****/

	 $a=array(1=>"Ambulatorio",2=>"Hospitalario",3=>"Urgencias",4=>"Prioritario"); 
	 echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>Tipo de servicio:</font></b><br>";
	 echo "<select name='wtip'>";
	  echo "<option>%%%-Todos</option>";            
	 if (isset($wtip)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wtip == $i )    // ==> Ese Item es el seleccionado 
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

/*****/
   	  $a=array(1=>"Sin Asignacion",2=>"Asignadas",3=>"Todas");  
      echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>Estado de la cita</b><br>";	  	
	  if (isset($wcit))  
	  {
	    for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wcit ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wcit' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wcit' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= count($a); $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wcit' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		} 	  
	  }	 
/*****/ 

   	  $a=array(1=>"Activas",2=>"Anuladas",3=>"Todas");  
      echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>Estado de la Orden</b><br>";	  	
	  if (isset($west))  
	  {
	    for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $west ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'west' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'west' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= count($a); $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'west' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		} 	  
	  }	 
	  
/****/

   	  $a=array(1=>"Con Observaciones",2=>"Sin Observaciones",3=>"Todas");  
      echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2><b>Observaciones</b><br>";	  	
	  if (isset($wobs))  
	  {
	    for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $wobs ==  $i ) 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wobs' VALUE=".$i." CHECKED>".$a[$i]."</INPUT>";
		 else 
		  echo "<INPUT TYPE = 'Radio' NAME = 'wobs' VALUE=".$i.">".$a[$i]."</INPUT>";
		 
		 echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		}
	  }	
	  else   //Ninguna seleccionada
	  {
        for ($i = 1; $i <= count($a); $i++)
        { 
	      echo "<INPUT TYPE = 'Radio' NAME = 'wobs' VALUE=".$i.">".$a[$i]."</INPUT>";
		  echo "&nbsp;&nbsp;&nbsp;&nbsp;";   
		} 	  
	  }	 

/****/
    echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><font text color=#003366 size=2>";	
   	echo "<input type=checkbox name=conf><b>Con mas de 3 LLamadas</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
/****/
 
   echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit o sea el boton de Generar o Aceptar
   echo "</tr>";
   echo "</table>";
   
 }	
 else      // Cuando ya estan todos los datos escogidos     **********************************************************************
 {


	echo "<center><table border=0>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>Reporte General de Ordenes </font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>Periodo: ".$wfec1." Al ".$wfec2."</font></b><br>";
    echo "<tr><td align=center bgcolor=#DDDDDD colspan=><b><font text color=#003366 size=2><i>PROGRAMA: paf13som.php Ver. 2015/11/26<br>AUTOR: JairS</font></b><br>";
    echo "</table>";

    echo "<br>";
    echo "<table border=0>";
    echo "<tr>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD></td>";	
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Nro Orden<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fecha<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Documento<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Apellidos<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Nombres<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Telefonos<b></td>";	
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Rango<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Tipo Afil<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Diagnostico<b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Examen<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Medico<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Unidad<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Estado<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Tipo Serv.<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>F. Vigencia<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Orden NEPS<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>F. Emision<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>F. Cita<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Hora<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>F. Cancelacion<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Causa<b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Observaciones<b></td>";
		
    echo "</tr>"; 
	
	// TOMO EL CODIGO DEL Dx	
	if (isset($wdia))  
     $c1=explode('-',$wdia);
     // TOMO EL CODIGO DEL Examen	
	if (isset($wexa))  
     $c2=explode('-',$wexa);
    // TOMO EL CODIGO DEL Medico	
	if (isset($wmed))  
     $c3=explode('-',$wmed);
     // TOMO EL CODIGO DE LA Unidad	
	if (isset($wcco))  
     $c4=explode('-',$wcco);
    // TOMO EL CODIGO DEL Tipo de Servicio	
	if (isset($wtip))  
     $c5=explode('-',$wtip);
 
    $wtotgrl = 0; //0	1      2      3       4     5     6      7       8     9       10     11     12    13     14     15     16     17     18     19     20     21
    $query="SELECT Id,PAFFEC,pafced,pafape,pafnom,paftel,pafran,paftip,pafdia,pafexa,pafrem,pafcco,pafest,pafser,paffre,paford,paffem,paffci,pafhor,paffca,pafcau,pafobs"
          ." FROM pafsom_000001"
		  ." WHERE PAFFEC BETWEEN '".$wfec1."' AND '".$wfec2."'"
		  ."   AND pafcco  LIKE '".$c4[0]."'"
		  ."   AND pafexa  LIKE '".$c2[0]."'"
		  ."   AND pafdia  LIKE '".$c1[0]."'"
		  ."   AND pafrem  LIKE '".$c3[0]."'"
		  ."   AND pafser  LIKE '".$c5[0]."'";
		  if ( $wcit == "1" )
		     $query=$query."   AND paffci = '0000-00-00' ";
		  else
		   if ( $wcit == "2" )
			 $query=$query."   AND paffci <> '0000-00-00' ";
			 
		  if ( $west == "1" )
		     $query=$query."   AND pafest = 'A' ";
		  else
		   if ( $west == "2" )
			 $query=$query."   AND pafest <> 'A' ";	
			 
		  if ( $wobs == "1" )
		     $query=$query."   AND   length(pafobs)> 1 ";
		  else
		   if ( $wobs == "2" )
			 $query=$query."   AND   length(pafobs)< 1 ";		
	 
          if ( $conf == "on" )	 
            $query=$query."   AND paflla='on' ";
  			 
          $query=$query." Order by Id";



   $resultado = mysql_query($query);
   $nroreg = mysql_num_rows($resultado);

   if ($nroreg > 0)
   { 
     //Generare archivo plano en esta ruta
     //$ruta=  "../archivos";   
     // para que lo genere en un subdirectorio apartir de la ruta donde estan los fuentes
     //$archivo = fopen($ruta."/ordenesneps.txt","w"); 
     $archivo = fopen("ordenesneps.txt","w"); 
  
    $LineaDatos="Nro Orden|Fecha|Documento|Apellidos|Nombres|Telefonos|Rango|Tipo Afil|Diagnostico|Examen|Medico|Unidad|Estado|Tipo Serv|F. Vigencia|"
	           ."Orden Entidad|F. Emision|F. Cita|Hora|F. Cancelacion|Causa|Observaciones";  
    fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 

    $k = 1;
    While ($k <= $nroreg)
    {        
        $registro = mysql_fetch_row($resultado);  	 // Leo 1er registro
       // color de la fila
          if (is_int ($k/2))  // Cuando la variable $i es para coloca este color
            $wcf="DDDDDD";  
          else
            $wcf="CCFFFF";

        $wid=$registro[0];
        echo "<td><A HREF='paf01som.php?wid=".$wid."&windicador=PrimeraVez&wproceso=Modificar' TARGET='_blank' >Editar</A></td>";	

          $LineaDatos="";
          for ($i = 0; $i <=6; $i++)  	
          {
            echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[$i]."</td>";
            $LineaDatos=$LineaDatos.$registro[$i]."|";
          }

         switch ($registro[7])
		 { 
             case "01":
              $wtipafil = "Cotizante";
              break;
             case "02":
              $wtipafil = "Beneficiario";
              break;
             case "03":
              $wtipafil = "Adicional";
              break;
         }	
         echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$wtipafil."</td>";		
         $LineaDatos=$LineaDatos.$wtipafil."|";
 
       // TOMO EL NOMBRE DEL DX
        $query = "SELECT codigo,descripcion FROM root_000011 Where codigo='".$registro[8]."'"; 
        $resultadoB=mysql_query($query); 
        if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>(".$registroB[0].") ".$registroB[1]."</td>";	
         $LineaDatos=$LineaDatos.$registroB[0]."-".$registroB[1]."|"; 
        }
        else
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'></td>";	
         $LineaDatos=$LineaDatos."|"; 
        }  

 	   // TOMO EL NOMBRE DEL EXAMEN 1  
		$query = "SELECT codigo,nombre FROM root_000012 Where codigo ='".$registro[9]."'";
        $resultadoB=mysql_query($query); 
        if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro 
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>(".$registroB[0].") ".$registroB[1]."</td>";	
         $LineaDatos=$LineaDatos.$registroB[0]."-".$registroB[1]."|"; 
        }
        else
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'></td>";	
         $LineaDatos=$LineaDatos."|"; 
        }  
        // TOMO EL NOMBRE DEL MEDICO
        $query = "SELECT Meduma,Medno1,Medno2,Medap1,Medap2 FROM movhos_000048 Where Meduma='".$registro[10]."' And Meduma <> '' ";
        $resultadoB=mysql_query($query); 
        if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>(".$registroB[0].") ".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."</td>";	
         $LineaDatos=$LineaDatos.$registroB[0]."-".$registroB[1]." ".$registroB[2]." ".$registroB[3]." ".$registroB[4]."|"; 
        }
        else
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'></td>";	
         $LineaDatos=$LineaDatos."|"; 
        }  	  	   
        // TOMO EL NOMBRE DE LA UNIDAD o CCOSTO  
	    $query = "SELECT ccocod,cconom FROM costosyp_000005 WHERE ccocod = '".$registro[11]."'";
        $resultadoB=mysql_query($query); 
        if ( $registroB = mysql_fetch_row($resultadoB) )      //  Encontro 
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>(".$registroB[0].") ".$registroB[1]."</td>";	
         $LineaDatos=$LineaDatos.$registroB[0]."-".$registroB[1]."|"; 
        }
        else
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'></td>";	
         $LineaDatos=$LineaDatos."|"; 
        }  
		
        echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[12]."</td>";     
        $LineaDatos=$LineaDatos.$registro[12]."|";
  
        switch ($registro[13])
		 { 
             case "1":
              $wtipserv = "Ambulatorio";
              break;
             case "2":
              $wtipserv = "Hospitalario";
              break;
             case "3":
              $wtipserv = "Urgencias";
              break;
            case "4":
              $wtipserv = "Prioritario";
              break;
         }	
        echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$wtipserv."</td>";	
		$LineaDatos=$LineaDatos.$wtipserv."|";

        for ($i = 14; $i <=19; $i++)  	
        {
         echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[$i]."</td>";
         $LineaDatos=$LineaDatos.$registro[$i]."|";
        }
  	   
       
         $wcausa=""; 
         switch ($registro[20])
		 { 
             case "1":
              $wcausa = "Por decision medica";
              break;
             case "2":
              $wcausa = "Identificacion equivocada";
              break;
             case "3":
              $wcausa = "Inasistencia del usuario";
              break;
            case "4":
              $wcausa = "Orden duplicada";
              break;
            case "5":
              $wcausa = "Suspencion del Tra/to";
              break;
            case "6":
              $wcausa = "Otras";
              break;
         }	
		echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$wcausa."</td>";
        $LineaDatos=$LineaDatos.$wcausa."|";

        echo "<td colspan=1 align=center bgcolor='".$wcf."'><font text color=#003366 size=1>".$registro[21]."</td>";


        // ****************************************************************************************************************
        // Si el campo texto o memo trae saltos de linea dados por ejemplo con <Enter> con esta instruccion se las quito
        // pues al llevarlo a Excel los coloca en otra fila
        // $cadena = eregi_replace("[\n|\r|\n\r]", " ", $registro[21]);     
        $cadena = preg_replace("[\n|\r|\n\r]", " ", $registro[21]);     
        $LineaDatos=$LineaDatos.$cadena."|";
        //******************************************************************************************************************

        fwrite($archivo, $LineaDatos.chr(13).chr(10) ); 
    	echo "<tr>"; 	
			
		$k=$k+1;
    }
    fclose($archivo);
	//Si en el href lo hago a la variable $ruta me mostrara todos los
	//archivos generados alli, pero si $ruta tiene el path completo
	//con el archivo generado lo bajaria directamente y no mostraria
	//otros archivos. OJO no hago el siguiente HREF con <td para que
    //lo coloque en la parte superior del reporte
	echo "<li><A href='ordenesneps.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";

   }  	
   echo "<td colspan=21 align=center bgcolor='#CC99CC'><font text color=#003366 size=1>TOTAL ORDENES: </td>";
   echo "<td colspan=1 align=LEFT   bgcolor='#CC99CC'><font text color=#003366 size=1>".$nroreg."</td>";
   echo "</tr>";
   echo "</table>"; 
 }  
echo "</Form>"; 
echo "</BODY>";
echo "</HTML>";	

?>
