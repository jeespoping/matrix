<html>
<head>
<title>Reporte de Saldos X Aplicar X Paciente</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_salxaplixpac.submit();
	}
</script>


<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE SALDOS X APLICAR X PACIENTE	                                                   *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para saber los saldos pendientes de aplicar por paciente.                                            |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :Octubre 3 DE 2007.                                                                                           |
//FECHA ULTIMA ACTUALIZACION  :03 de Octubre de 2007.                                                                                       |
//DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacio y paciente que saldo tiene pendiente de aplicar.  |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
//costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
//clisur_000003     : Tabla de Centros de costos de clinica del sur.                                                                        |
//farstore_000003   : Tabla de Centros de costos de farmastore.                                                                             |
//root_000041       : Tabla de Tipos de requerimientos.                                                                                     |
//root_000042       : Tabla de Responsables por centro de costos.                                                                           |
//usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
//root_000040       : Tabla de Requerimientos.                                                                                              |
//root_000043       : Tabla de Clases.                                                                                                      |
//root_000049       : Tabla de Estados.                                                                                                     |
//==========================================================================================================================================
$wactualiz="Ver. 2007-10-05";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
 $empresa='root';

 

 


 echo "<center><table border=1>";
 echo "<tr><td align=center colspan=2 bgcolor=#006699><font size=6 text color=#FFFFFF><b> REPORTE DE SALDOS X APLICAR X PACIENTE </b></font><br><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";

if (!isset($habi) or $habi=='' or !isset($cco) or $cco=='' )
  {
   echo "<form name='rep_salxaplixpac' action='' method=post>";

   /////////////////////////////////////////////////////////////////////////////////////// seleccion para saber la Base de Datos

   $query = " SELECT Empcod,Empdes,Emptcc "
	       ."   FROM ".$empresa."_000050"
	       ."  WHERE Empest = 'on'"
	       ."    AND Empcod = '".$wemp."'"
	       ."  ORDER BY Empcod,Empdes,Emptcc";

   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
   
   for ($i=1;$i<=$num;$i++)
   { 
    $row = mysql_fetch_array($err);
    
    $tablacc = $row[2];
    $codemp  = $row[0];
   }
      
   ///////////////////////////////////////////////////////////////////////////////////////// seleccion para centro de costos dependiendo de la empresa
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Centro de Costos: <br></font></b><select name='cco' onchange='enter()'>";  
    
   $empre1=$empre1;
   $empre2=$empre2;
   
   switch ($tablacc)
	{
	 case "costosyp_000005":
	 {
	       $query1 =" SELECT Ccocod, Cconom "
	               ."   FROM costosyp_000005 "
	               ."  ORDER BY Ccocod,Cconom";
	       break;
	 }  
	 case "clisur_000003":
	 {
	       $query1 =" SELECT Ccocod, Ccodes "
			       ."   FROM clisur_000003 "
		           ."  ORDER BY Ccocod,Ccodes";
           break;
	 }
	 case "farstore_000003":
	 {
	      $query1 =" SELECT Ccocod, Ccodes "
	 	          ."   FROM farstore_000003 "
	 	          ."  ORDER BY Ccocod,Ccodes";
	      break;
	 }
	}
  
   $err1 = mysql_query($query1,$conex);
   $num1 = mysql_num_rows($err1);
   $Ccostos=explode('-',$cco);
   
   if ($codemp == "") // si no escoje empresa muestra blanco en centro de costos
    { 
     echo "<option></option>";
     echo "<option>9999-TODOS LOS CENTROS DE COSTOS</option>";
    }
   else 
    {
     echo "<option>".$Ccostos[0]."-".$Ccostos[1]."</option>";
     echo "<option>9999-TODOS LOS CENTROS DE COSTOS</option>";
     
     switch ($tablacc)
	 {
	 case "costosyp_000005":
	  {
	    $query1="SELECT Ccocod, Cconom "
	           ."  FROM costosyp_000005 "
	           ." ORDER BY Ccocod,Cconom";
	    break;
	  }  
	 case "clisur_000003":
	  {
	    $query1 = "SELECT Ccocod, Ccodes "
		        ."   FROM clisur_000003 "
		        ."  ORDER BY Ccocod,Ccodes";
        break;
	  }
	 case "farstore_000003":
	  {
	    $query1="SELECT Ccocod, Ccodes "
	 	       ."  FROM farstore_000003 "
	 	       ." ORDER BY Ccocod,Ccodes";
	    break;
	  }
	 }
    }
   
   for ($i=1;$i<=$num1;$i++)
   {
	$row1 = mysql_fetch_array($err1);
	echo "<option>".$row1[0]."-".$row1[1]."</option>";
   }
   
   echo "</select></td></tr>";
    
   /////////////////////////////////////////////////////////////////////////// seleccion para el centro de costos las habitaciones
   echo "<td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=3> Habitacion : <br></font></b><select name='habi'>";

   
   $Ccostos=explode('-',$cco);
   
   if ($Ccostos[0]=='9999')
    {
     $query = " SELECT habcod "
	        ."    FROM movhos_000020 ";
	}
   else 
    {  
     $query = " SELECT habcod "
	        ."    FROM movhos_000020 "
	        ."   WHERE habcco = '".$Ccostos[0]."' ";
    }        
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   
   if ($codemp == "")
    { 
   	 echo "<option></option>";
    }
   else 
    {
   	 echo "<option>".$habi[0]."</option>";
    } 
    
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}

   echo "<option>TODAS</option>";
   echo "</select></td>";
 	
   echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='Generar'></td>";          //submit osea el boton de OK o Aceptar
   echo "</tr>";
	
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	
	$Ccostos=explode('-',$cco);
	$hab=explode('-',$habi);
	
    $query = " SELECT Detapl,Detval"
	       ."   FROM ".$empresa."_000051"
	       ."  WHERE Detemp = '".$wemp."'";
	 
   $err = mysql_query($query,$conex);
   $num = mysql_num_rows($err);
   
   $empre1="";
   $empre2="";

   for ($i=1;$i<=$num;$i++)
    { 
     $row = mysql_fetch_array($err);
     
    IF ($row[0] == 'cenmez')
     {
      $empre1=$row[1];
     }	
    else 
     { 
      if ($row[0] == 'movhos') 
      {
      $empre2=$row[1];	
      }
     }     
    }

	
	   echo "<center><table border=1>";
		echo "<tr><td align='center' colspan='3' bgcolor='#FFFFFF'><font size='3' color='#003366'><b>".$Ccostos[0]."-".$Ccostos[1]."</b></font></td></tr>";
		echo "<tr><td align=center colspan=8 bgcolor=#FFFFFF><font text color=#003366><b>Habitacion : <i>".$hab[0]."</i></td></tr>";
		echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF><b>ARTICULO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>NOMBRE ARTICULO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF><b>TOTAL SALDO</b></font></td></tr>";
	
	/////////////////////////////////////////////////para cuando son todos los centros de costos
	if ($Ccostos[0]=="9999")
	 {
      /////////////////////////////////////////////////para cuando son todas las habitaciones
	  if ($hab[0]=="TODOS")
       {
        $quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
		        ." SELECT ubihac,ubisac,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000004,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                //."  AND ubisac='1187'
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."  FROM ".$empre2."_000018,".$empre2."_000030,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ." WHERE ubiald<>'on'"
                ."   AND ubihis=splhis"
                ."   AND ubiing=spling" 
                //." AND ubisac='1187'"
                ."   AND splart=artcod"
                ."   AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ." UNION "
                ." SELECT ubihac,ubisac,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018, ".$empre2."_000004,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                //."  AND ubisac='1187'"
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000030,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=splhis"
                ."    AND ubiing=spling"
                // ." AND ubisac='1187'"
                ."    AND splart=artcod"
                ."    AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  ORDER BY 5,6,1,3";
                
       $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
		
       switch ($tablacc)
	    {
	     case "costosyp_000005":
	     {
	       $query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1,costosyp_000005"
                   ."  WHERE ubisac = Ccocod"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2,1,6,4";
	     	
	       break;
	     }  
	     case "clisur_000003":
	     {
	     	$query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1,clisur_000003"
                   ."  WHERE ubisac = Ccocod"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2,1,6,4";
	     	
	       break;
	     }
	     case "farstore_000003":
	     {
	       	$query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1,farstore_000003"
                   ."  WHERE ubisac = Ccocod"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2,1,6,4";
	    	
	      break;
	     }
	    }
        
        $err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);
		//echo mysql_errno() ."=". mysql_error();

			
	   }
	  else /////////////////////////////////////////////////para cuando escoje una habitacion.
	   {
        $quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
		        ." SELECT ubihac,ubisac,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000004,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                ."    AND ubihac='".$hab[0]."' "
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."  FROM ".$empre2."_000018,".$empre2."_000030,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ." WHERE ubiald<>'on'"
                ."   AND ubihis=splhis"
                ."   AND ubiing=spling" 
                ."   AND ubihac='".$hab[0]."'"
                ."   AND splart=artcod"
                ."   AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ." UNION "
                ." SELECT ubihac,ubisac,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018, ".$empre2."_000004,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                ."    AND ubihac='".$hab[0]."'"
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000030,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=splhis"
                ."    AND ubiing=spling"
                ."    AND ubihac='".$hab[0]."'"
                ."    AND splart=artcod"
                ."    AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  ORDER BY 5,6,1,3";
                
       $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
		
       switch ($tablacc)
	    {
	     case "costosyp_000005":
	     {
	       $query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1,costosyp_000005"
                   ."  WHERE ubisac = Ccocod"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2";
	     	
	       break;
	     }  
	     case "clisur_000003":
	     {
	     	$query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1,clisur_000003"
                   ."  WHERE ubisac = Ccocod"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2";
	     	
	       break;
	     }
	     case "farstore_000003":
	     {
	       	$query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1,farstore_000003"
                   ."  WHERE ubisac = Ccocod"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2";
	    	
	      break;
	     }
	    }
        
        $err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);
		//echo mysql_errno() ."=". mysql_error();
	    
	   }
	  }  
    else // Este es cuando le doy algun centro de costos 
	 {  
	  if ($hab[0]=="TODAS")
       {
        $quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
		        ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000004,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                ."    AND ubisac='".$Ccostos[0]."'"
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."  FROM ".$empre2."_000018,".$empre2."_000030,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ." WHERE ubiald<>'on'"
                ."   AND ubihis=splhis"
                ."   AND ubiing=spling" 
                ."   AND ubisac='".$Ccostos[0]."'"
                ."   AND splart=artcod"
                ."   AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ." UNION "
                ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018, ".$empre2."_000004,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                ."    AND ubisac='".$Ccostos[0]."'"
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000030,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=splhis"
                ."    AND ubiing=spling"
                ."    AND ubisac='".$Ccostos[0]."'"
                ."    AND splart=artcod"
                ."    AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  ORDER BY 6,7,1,3";
                
        $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
		
        $query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                ."   FROM tempora1"
                ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                ."  ORDER BY 2,1,6,4";
	     	
	    $err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);
		//echo mysql_errno() ."=". mysql_error();
	
	   }
	 else /////////////////////////////////////////////////para cuando escoje una habitacion.
	   {
        $quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
		        ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000004,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                ."    AND ubihac='".$hab[0]."' "
                ."    AND ubisac='".$Ccostos[0]."'"
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."  FROM ".$empre2."_000018,".$empre2."_000030,".$empre2."_000026,".$empresa."_000036,".$empresa."_000037 "
                ." WHERE ubiald<>'on'"
                ."   AND ubihis=splhis"
                ."   AND ubiing=spling" 
                ."   AND ubihac='".$hab[0]."'"
                ."   AND ubisac='".$Ccostos[0]."'"
                ."   AND splart=artcod"
                ."   AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ." UNION "
                ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,spaart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018, ".$empre2."_000004,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=spahis"
                ."    AND ubiing=spaing"
                ."    AND ubihac='".$hab[0]."'"
                ."    AND ubisac='".$Ccostos[0]."'"
                ."    AND spaart=artcod"
                ."    AND (spauen-spausa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  UNION " 
                ." SELECT ubihac,ubisac,'".$Ccostos[1]."' as Cconom,splart as art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,(spauen-spausa) as sal"
                ."   FROM ".$empre2."_000018,".$empre2."_000030,".$empre1."_000002,".$empresa."_000036,".$empresa."_000037 "
                ."  WHERE ubiald<>'on'"
                ."    AND ubihis=splhis"
                ."    AND ubiing=spling"
                ."    AND ubihac='".$hab[0]."'"
                ."    AND ubisac='".$Ccostos[0]."'"
                ."    AND splart=artcod"
                ."    AND (spluen-splusa) > 0"
                ."    AND spahis=orihis"
                ."    AND spaing=oriing"
                ."    AND oriced=pacced"
                ."    AND oritid=pactid"
                ."  ORDER BY 5,6,1,3";
                
                echo $quer1;
                
       $err4 = mysql_query($quer1,$conex) or die("ERROR EN QUERY"); 
		
       
	       $query1 = "SELECT ubihac,ubisac,Cconom,art,artcom,ubihis,ubiing,pacno1,pacno2,pacap1,pacap2,sum(sal)"
                   ."   FROM tempora1"
                   ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11"
                   ."  ORDER BY 2,1,6,4";
	    
        $err1 = mysql_query($query1,$conex);
		$num1 = mysql_num_rows($err1);
		//echo mysql_errno() ."=". mysql_error();
	    
	   }
	 }
	
	$swtitulo1=='SI';
	$swtitulo2=='SI';
	$histoant=0; 
	$totxhis=0;
	
	for ($i=1;$i<=$num1;$i++)
	 {
	  if (is_int ($i/2))
	   {
	   	$wcf="DDDDDD";  // color de fondo
	   }
	  else
	   {
	   	$wcf="CCFFFF"; // color de fondo
	   }

	  $row2 = mysql_fetch_array($err1);

	  if ($swtitulo1=='SI')
	  {
	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>CENTRO DE COSTOS : </b></font></td><td align=center colspan=1>".$row2[1]."</td><font text color=#FFFFFF><b> - </b></font></td><td align=left colspan=1>".$row2[2]."</td></tr>"; 
	   $ccoant=$row2[1];
       $swtitulo1='NO';
	  
	  } 
	   
	  if ($swtitulo2=='SI')
	   {
	    $pacie=$row2[7]." ".$row2[8]." ".$row[9]." ".$row[10];
	   
	    $histoant = $row2[5];
	    
	    echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>HAB: </b></font></td><td align=left colspan=1>".$row2[0]."</td><font text color=#FFFFFF><b> HISTORIA </b></font></td><td align=center colspan=1>".$row2[5]."</td></td><font text color=#FFFFFF><b> - </b></font></td><td align=center colspan=1>".$row2[6]."</td>" .
	         "<font text color=#FFFFFF><b> PACIENTE : </b></font></td><td align=center colspan=1>".$pacie."</td></tr>"; 
	    
	    $swtitulo2='NO';	
	   }

	   if ($histoant == $row2[5])
	    {
	     echo "<tr  bgcolor=".$wcf."><td align=center>".$row2[3]."</td><td align=center>".$row2[4]."</td><td align=center>".$row2[11]."</td></tr>";
	     $totxhis=$totxhis+$row2[11];
	     $totxcco=$totxcco+$row2[11];	
	    }
	   else 
	    { 
	     echo "<tr><td alinn=center colspan=3 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	     echo "<tr><td align=center colspan=2 bgcolor=#006699><font text color=#FFFFFF><b>TOTAL PACIENTE : </b></font></td><td align=center>".number_format($totxhis)."</td></tr>";
	     echo "<tr><td alinn=center colspan=3 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
         
	     if ($ccoant=$row2[1])
	     {
	      echo "<tr  bgcolor=".$wcf."><td align=center>".$row2[3]."</td><td align=center>".$row2[4]."</td><td align=center>".$row2[11]."</td></tr>";
	      $totxhis=$totxhis+$row2[11];
	      $totxcco=$totxcco+$row2[11];	
	      $swtitulo2='SI';
	     }
	     else 
	     {
	      
	      echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>CENTRO DE COSTOS : </b></font></td><td align=center colspan=1>".$row2[1]."</td><font text color=#FFFFFF><b> - </b></font></td><td align=left colspan=1>".$row2[2]."</td></tr>"; 
	      $ccoant=$row2[1];
          $swtitulo1='NO';	
          $pacie=$row2[7]." ".$row2[8]." ".$row[9]." ".$row[10];
	   
	      $histoant = $row2[5];
	    
	      echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF><b>HAB: </b></font></td><td align=left colspan=1>".$row2[0]."</td><font text color=#FFFFFF><b> HISTORIA </b></font></td><td align=center colspan=1>".$row2[5]."</td></td><font text color=#FFFFFF><b> - </b></font></td><td align=center colspan=1>".$row2[6]."</td>" .
	           "<font text color=#FFFFFF><b> PACIENTE : </b></font></td><td align=center colspan=1>".$pacie."</td></tr>"; 
	      
	      $swtitulo2='NO';	
	      echo "<tr  bgcolor=".$wcf."><td align=center>".$row2[3]."</td><td align=center>".$row2[4]."</td><td align=center>".$row2[11]."</td></tr>";
	      $totxhis=$totxhis+$row2[11];
	      $totxcco=$totxcco+$row2[11];	
          
	     }
	    
	    }
	  
	 } // fin del for
	 
	 echo "<tr><td alinn=center colspan=3 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	 echo "<tr><td align=center colspan=2 bgcolor=#006699><font text color=#FFFFFF><b>TOTAL GENERAL : </b></font></td><td align=center>".number_format($totxcco)."</td></tr>";
	 echo "<tr><td alinn=center colspan=3 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	  
	  
   }// cierre del else donde empieza la impresión
	
 echo "</table>"; // cierra la tabla o cuadricula de la impresión
					    
 } 

?>