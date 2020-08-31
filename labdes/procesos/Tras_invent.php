<head>
  <title>REPORTE DE TRASLADOS DE INVENTARIOS</title>
</head>
<body BACKGROUND="/inetpub/wwwroot/matrix/images/medical/root/fondo de bebida de limón.gif">
<?php
include_once("conex.php");
  /****************************************************
   *	           IMPRIMIR LOS TRASLADOS             *
   *REALIZADOS EN LA UNIDAD DE SERVICIOS FARMACEUTICOS*
   *   				CONEX, FREE => OK				  *
   ****************************************************/
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

						or die("No se ralizo Conexion");
  

 
  $conexunix = odbc_pconnect('inventarios','invadm','1201')
  					    or die("No se ralizo Conexion con el Unix");
  					    
 // if ($conexunix == FALSE)
 //    echo "Fallo la conexión UNIX";
  		
 
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="(Versión Enero 19 de 2005)";                     // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                                           // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
                                                   
  echo "<br>";				
  echo "<br>";
      		
  echo "<form action='Tras_invent.php' method=post>";
  //echo "<center><table border=2 width=400>";
  echo "<center><table border=2 width=400 BACKGROUND='images/fondo de bebida de limón.gif'>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=6 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=4 text color=#CC0000><b>TRASLADOS DE INVENTARIOS PENDIENTES DE RECIBO</b></font></td></tr>";
  echo "<tr><td align=center colspan=240 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";
 
      
  if(!isset($wanoi) or !isset($wmesi) or !isset($wdiai) or !isset($wanof) or !isset($wmesf) or !isset($wdiaf) or !isset($wcco))
    {
	 //FECHA INICIAL
	 echo "<tr><td align=center colspan=3 bgcolor=#66CC99><b>FECHA INICIAL</b></td><td align=center colspan=3 bgcolor=#66CC99><b>FECHA FINAL</b></td></tr>";   
	 
	 //AÑO INICIAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Año:</b></font><select name='wanoi'>";
     for($f=2005;$f<2051;$f++)
       {
        if($f == $wanoi)
          echo "<option>".$f."</option>";
         else
            echo "<option>".$f."</option>";
       }
	   echo "</select>";
  
	 //MES INICIAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Mes :</b></font><select name='wmesi'>";
     for($f=1;$f<13;$f++)
       {
        if($f == $wmesi)
          if($f < 10)
            echo "<option>0".$f."</option>";
           else 
              echo "<option>".$f."</option>";
	     else
	        if($f < 10)
	          echo "<option>0".$f."</option>";
	         else
	            echo "<option>".$f."</option>";
	   }
	   echo "</select>";
	   
     //DIA INICIAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Dia :</b></font><select name='wdiai'>";
     for($f=1;$f<32;$f++)
       {
        echo "<option>".$f."</option>";
       } 
	   echo "</td></select></td>";
	   
	 
	 //AÑO FINAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Año:</b></font><select name='wanof'>";
     for($f=2005;$f<2051;$f++)
       {
        if($f == $wanof)
          echo "<option>".$f."</option>";
         else
            echo "<option>".$f."</option>";
       }
	   echo "</select>";
  
	 //MES FINAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Mes :</b></font><select name='wmesf'>";
     for($f=1;$f<13;$f++)
       {
        if($f == $wmesf)
          if($f < 10)
            echo "<option>0".$f."</option>";
           else 
              echo "<option>".$f."</option>";
	     else
	        if($f < 10)
	          echo "<option>0".$f."</option>";
	         else
	            echo "<option>".$f."</option>";
	   }
	   echo "</select>";
	   
     //DIA FINAL
     echo "<td bgcolor=#cccccc ><font size=4><b>Dia :</b></font><select name='wdiaf'>";
     for($f=1;$f<32;$f++)
       {
        echo "<option>".$f."</option>";
       } 
	   echo "</td></select></td></tr>";
	   
	   
	 //CENTROS DE COSTO
	 echo "<center><td bgcolor=#cccccc colspan = 6><font size=4><b>Centro de Costo :</b></font><select name='wcco'>";
	 $query = " SELECT ccocod, cconom "
             ."   FROM cocco "
             ."  ORDER BY ccocod ";
                        
     $res = odbc_do($conexunix,$query);
	             
     echo "<option selected>*- Todos los centros de costo </option>";
	 while(odbc_fetch_row($res))
	     {
	      echo "<option value>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	     }      
	 echo "</SELECT></td></tr></table><br><br>";  
      
     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
    } 
   else
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Ya estan todos los campos setiados o iniciados ===================================================================================
      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      {
	   	   
	   //FECHA INICIAL
	   echo "<tr><td colspan=2 align=center bgcolor=#66CC99><b>Fecha Inicial : : ".$wanoi."/".$wmesi."/".$wdiai."</b></td>";
	   echo "<td colspan=2 align=center bgcolor=#66CC99><b>Fecha Final : : ".$wanof."/".$wmesf."/".$wdiaf."</b></td></tr>";
       	   
	      
	      
	   //AÑO INICIAL
       echo "<input type='HIDDEN' name= 'wano' value='".$wanoi."'>"; 
  
	   //MES INICIAL
       echo "<input type='HIDDEN' name= 'wmes' value='".$wmesi."'>";
	   
       //DIA INICIAL
       echo "<input type='HIDDEN' name= 'wdia' value='".$wdiai."'>";
       
       //AÑO FINAL
       echo "<input type='HIDDEN' name= 'wano' value='".$wanof."'>"; 
  
	   //MES FINAL
       echo "<input type='HIDDEN' name= 'wmes' value='".$wmesf."'>";
	   
       //DIA FINAL
       echo "<input type='HIDDEN' name= 'wdia' value='".$wdiaf."'>";
       
       $wwcco = substr($wcco,0,strpos($wcco,"-")); 
       
       //CENTRO DE COSTO
       echo "<td colspan=4 align=center bgcolor=#66CC99><b>Centro de Costo: ".$wwcco."</b></td>";
       echo "<input type='HIDDEN' name= 'wwcco' value='".$wwcco."'>";       
       
       //Aca traigo los documentos de traslado para el centro de costo y fecha digitada
       //$fecha = date($wdia."-".$wmesfec."-".$wanofec,"%d-%m-%Y");
       $fechaI = date($wanoi."-".$wmesi."-".$wdiai,"%Y/%m/%d");
       $fechaF = date($wanof."-".$wmesf."-".$wdiaf,"%Y/%m/%d");
          
       $query = " SELECT movfue, movdoc, movcon, movser "
               ."   FROM ivmov "
               ."  WHERE movfec BETWEEN '".$fechaI."'"
               ."    AND '".$fechaF."'"
               ."    AND movse1 = '".$wwcco."'"
               ."    AND movanu = '0'"
               ."  ORDER BY movfue, movdoc ";
       
       echo "<tr>";
       echo "<th bgcolor=#fffffff>Fuente</th>";
       echo "<th bgcolor=#fffffff>Documento</th>";
       echo "<th bgcolor=#fffffff>Concepto</th>";
       echo "<th bgcolor=#fffffff colspan=2>Servicio Origen</th>";
       echo "</tr>";
                               
       $res = odbc_do($conexunix,$query);
	             
           	   
       echo "<option selected>. </option>";
	   while(odbc_fetch_row($res))
	      {
		    //Aca busco si el documento ya esta registrado en MATRIX, si si, entonces no lo vuelvo a mostrar
		    
		    $q = "  SELECT COUNT(*) AS can "
		        ."    FROM invetras_000001 "
		        ."   WHERE fuente    = '".odbc_result($res,1)."'"
		        ."     AND documento = '".odbc_result($res,2)."'"
		        ."     AND ok        = 'on' ";
		        
		    $res1 = mysql_query($q,$conex);
            $row = mysql_fetch_array($res1);
           
            if ($row[0] == 0 )    //Si es 0 indica que no ha sido procesado
               {
		        echo "<tr>";
		        echo "<td align=center><font size=3><b>".odbc_result($res,1)."</b></font></td>";
		        echo "<td align=center><font size=3><b>".odbc_result($res,2)."</b></font></td>";
		        echo "<td align=center><font size=3><b>".odbc_result($res,3)."</b></font></td>";
		        echo "<td align=center><font size=3><b>".odbc_result($res,4)."</b></font></td>";
		    
		        $wfue=odbc_result($res,1);
		        $wdoc=odbc_result($res,2);
		    	    
		        echo "<td align=center><font size=3><b><A href='tras_detalle.php?fuente=".$wfue."&amp;documento=".$wdoc."&amp;wanoi=".$wanoi."&amp;wmesi=".$wmesi."&amp;wdiai=".$wdiai."&amp;wanof=".$wanof."&amp;wmesf=".$wmesf."&amp;wdiaf=".$wdiaf."&amp;wcco=".$wcco."'>Detallar</A></b></font></td>";
		        echo "</tr>";
	           } 
	         
	      }      
	   echo "</SELECT></td></tr></table><br><br>";  
      } // else de todos los campos setiados
} // if de register

unset($wano);
unset($wmes);
unset($wdia);
unset($wcco);
unset($wwcco);
//odbc_close($conexunix);
echo "<br>";
echo "<font size=3><A href=Tras_invent.php"."> Retornar</A></font>";

include_once("free.php");
//odbc_close($conexunix);
?>
