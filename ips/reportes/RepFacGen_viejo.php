<html>
<head>
  <title>REPORTE DE FACTURACION GENERAL</title>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
function Seleccionar()
{
	document.forma.submit();
}
</SCRIPT>
</head>
<?php
include_once("conex.php");
 /*************************************************************************************
   *     REPORTE DE FACTURACION POR CENTRO DE COSTO Y POR EMPRESA                    *
   *                      DE CLINICA DEL SUR                                         *  
 *************************************************************************************/
//=================================================================================================================================
//PROGRAMA: RepFacGen.php
//AUTOR: Gabriel Agudelo.
  $wautor="Gabriel Agudelo.";
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\RepFacGen.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
		//-------------------I------------------------I---------------------------------------------------------------------
		//	  FECHA          I     AUTOR              I   MODIFICACION
		//-------------------I------------------------I------------------------------------------------------------------------
		//  2006-09-25       I Gabriel Agudelo        I creación del script.
		//-------------------I------------------------I-----------------------------------------------------------------------
		//     I   I 
		//-------------------I------------------------I-----------------------------------------------------------------------
	
//FECHA ULTIMA ACTUALIZACION 	: 2006-09-25 2:00 pm
  $wactualiz="(2007-04-13)"; 

/*DESCRIPCION:Este reporte presenta la lista de facturas por centro(s) de costo(s) y por empresa(s) 

MODIFICACIONES: 
2007-02-28 Se agregaron los campos de hora para hacer cortes por dichos.
2007-04-13 Se modifico el query de consulta para que consultara por centro de costos.
		   Se limito la seleccion del centro de costos unicamente a los centros donde se generan facturas.
		   
TABLAS QUE UTILIZA:
 $wbasedato."_000003: Maestro de centro de costos, select
 $wbasedato."_000018: encabezado de factura, select
 $wbasedato."_000024: maestro de empresas, select

 INCLUDES: 
  conex.php = include para conexión mysql            

 VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wini= lleva el estado del documento, si se esta abriendo por primera vez o no, se incializa desde invocación de programa con 'S'
 $senal= Indica el mensaje de alerta que se debe presentar segun los errores
 $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
 $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
 $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
 $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
 $wfecha=date("Y-m-d");    
  $wfecini)= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $wemp = empresa
 $wtip = variable que nos dice si es por codigo o nit
 $resultado = 
 $bandera1= controla que sea la primera vez que entra en el ciclo para el codigo
 $bandera2= controla que sea la primera vez que entra en el ciclo para la empresa
 $j=1 sirve como variable de control para intercambiar colores          
=================================================================================================================================*/


session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 

$wbasedato='clisur';
$key = substr($user,2,strlen($user));


//$conex=Conectarse(); 


echo "<form action='RepFacGen.php' method=post name='forma'>";
  $wentidad="    CLINICA DEL SUR     ";
  $hora = (string)date("H:i:s");
  $wnomprog="-RepFacGen.php-";  //nombre del reporte
  $wcf1="003366";  // color del fondo   -- Azul mas claro
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wcf3="0099CC";  //COLOR DEL FONDO 3  -- GRIS MAS OSCURO
  $wcf4="99CCFF";  //COLOR DEL FONDO 4  -- AZUL
  $wcf5="00CCFF";
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
  $wfecha=date("Y-m-d");   
  
  echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
if (!isset($wfecini) or !isset($wfecfin) or !isset($wccocod) or !isset($wemp) or !isset($wtip) or !isset($resultado))  
{
      echo "<center><table border=2>";
	 // echo "<tr><td align=center rowspan=2><img src='/reportes1/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
	  echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
	  echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURAS</b></font></td></tr>";
	  
	//INGRESO DE VARIABLES PARA EL REPORTE//
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
		 $wccocod="%-Todos los centros de costos";
	}
	
	echo "<tr>";  
	echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">FECHA INICIAL DE FACTURACION (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10></td>";
	echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">FECHA FINAL DE FACTURACION  (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10></td>";
	echo "</tr>";
	echo "<tr>";  
	echo "<td bgcolor=".$wcf."  align=center><b><font text color=".$wclfg.">HORA INICIAL DE FACTURACION (HH:MM:SS): </font></b>";
    
    echo "<select name='hi'>";
    for ($i=0;$i<24;$i++)
	   {

		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
   
	echo "<select name='mi'>";
	for ($i=0;$i<60;$i++)
	   {

		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
    
	echo "<select name='si'>";
	for ($i=0;$i<60;$i++)
	   {

		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
    
    
	echo "<td bgcolor=".$wcf."  align=center><b><font text color=".$wclfg.">HORA FINAL DE FACTURACION  (HH:MM:SS): </font></b>";
	echo "<select name='hf'>";
    for ($i=0;$i<24;$i++)
	   {

		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
   
	echo "<select name='mf'>";
	for ($i=0;$i<60;$i++)
	   {

		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
    
	echo "<select name='sf'>";
	for ($i=0;$i<60;$i++)
	   {

		  if ($i<10)
		   	echo "<option>0".$i."</option>";
		   else
		      echo "<option>".$i."</option>";
		}
    echo "</select>";
	echo "</tr>";
	echo "<tr>";  
	
	//SELECCIONAR CENTRO DE COSTOS
	if (isset($wccocod))
	   {
		echo "<td align=center bgcolor=".$wcf." align=center><b><font text color=".$wclfg."> Centro de costos";
		echo"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		echo"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		echo"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		echo"&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
		echo" <br>que genero factura:</font></b><select name='wccocod'>";   
  		// este query se modifico limitandolo solo para los centros de costos donde se generan facturas (2007-04-13)
  		$q= "   SELECT ccocod, ccodes "
 	       ."     FROM ".$wbasedato."_000003, ".$wbasedato."_000040 "
 	       ."    WHERE Ccoffa=Carfue"
 	       ."      AND Carfac='on'"
 	       ."    ORDER by 1"; 	   
		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);  
		
  		if ($num1 > 0 )
      	  {
      		echo "<option selected>".$wccocod."</option>"; 
      		if ($wccocod!='%-Todos los centros de costos')
			   echo "<option>%-Todos los centros de costos</option>";
		    for ($i=1;$i<=$num1;$i++)
	           {
	            $row1 = mysql_fetch_array($res1); 
	            echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
	           } 
          }
     	echo "</select></td>"; 
	  }
	
	  
	$wemp='% - Todas las empresas';
	  
	//SELECCIONAR EMPRESA
	if (isset($wemp))
	   {
		 echo "<td align=center bgcolor=".$wcf."  ><b><font text color=".$wclfg."> Responsable: <br></font></b><select name='wemp'>";   
		 $q= "   SELECT empcod, empnit, empnom "
	        ."     FROM ".$wbasedato."_000024 "
	        ."    WHERE empcod = empres "
	        ."    order by 2"; 
		 $res1 = mysql_query($q,$conex);
		 $num1 = mysql_num_rows($res1);   
		  	
	     if ($num1 > 0 )
	        {
		     echo "<option selected>".$wemp."</option>"; 
		     if ($wemp!='% - Todas las empresas')
			    echo "<option>% - Todas las empresas</option>";
				   
	   		 for ($i=1;$i<=$num1;$i++)
		       	{
			     $row1 = mysql_fetch_array($res1); 
	  		     echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
	       		} 
		    } 
	     echo "</select></td>"; 
	   }
	echo "</tr>";
	
	//SELECCIONAR tipo de reporte
		    
	echo "<tr><td align=center bgcolor=".$wcf." COLSPAN='1'><font text color=".$wclfg."><b>PARAMETROS DEL REPORTE: </b></font>";
	echo "<select name='wtip'>";
	
	if (isset ($wtip))
	   {
      	if ($wtip=='CODIGO')
			{
				echo "<option>CODIGO</option>";
				echo "<option>NIT</option>";
			}
		if ($wtip=='NIT')
			{
				echo "<option>NIT</option>";
				echo "<option>CODIGO</option>";
			}
	   }
	else
		{
			echo "<option>CODIGO</option>";
			echo "<option>NIT</option>";
		}
	echo "</select>";    		
	echo "<font text color=".$wclfg."><b> EMPRESA</b></td>";
	echo "<td align=center bgcolor=".$wcf." ><font text color=".$wclfg."><b>ESTADO : </b></font>";
	echo "<select name='west'>";
			echo "<option>on-Activo</option>";
			echo "<option>off-Anulado</option>";
	echo "</select></td></tr>";
	
	
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";	
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    echo "<tr><td align=center bgcolor=".$wcf." COLSPAN='2'><font text color=".$wclfg." ><b>";
	echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE DETALLADO&nbsp;&nbsp;&nbsp;&nbsp;";      						
	echo "<input type='radio' name='vol' value='NO'  onclick='Seleccionar()' > DESPLEGAR REPORTE RESUMIDO&nbsp;&nbsp;";                //submit
    echo "</font></b></td></tr>";
	echo "<tr ><td align=center COLSPAN='2' bgcolor=".$wcf."  ><font text color=".$wclfg." ><b>***EL RANGO DE HORAS UNICAMENTE ES APLICABLE PARA UN SOLO DIA***</font></b></td></tr>";
	echo "</table></br>";	   
} 

//MUESTRA DE DATOS DEL REPORTE
else
  {
  ////////////////////////////HORAS	
  $hori=$hi.":".$mi.":".$si;
  $horf=$hf.":".$mf.":".$sf;	
  
	echo "<table border=0 width=100%>";
	echo "<tr><td align=left><B>Facturacion:</B>  CLINICA DEL SUR</td>";
	echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
	echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>"; 
	echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	echo "<tr><td></td><td align=right><B>Usuario :</B> ".$key."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>"; 
	echo "</table>";
	echo "<table border=0 align=center >";
	echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";
	
		if ($vol=='SI')
		  	echo "<tr><td><B>REPORTE DE FACTURAS GENERAL DETALLADO</B></td></tr>";
		else
		    echo "<tr><td><B>REPORTE DE FACTURAS GENERAL RESUMIDO</B></td></tr>";	
	echo "</table></br>";
	echo "<table border=0 align=center >";
    echo "<tr><td><B>Fecha inicial:</B> ".$wfecini."</td>";
	echo "<td><B>Fecha final:</B> ".$wfecfin."</td>";
	echo "<td><B>Estado :</B> ".$west."</td></tr>";
	
	if ($hori!="00:00:00" or $horf!="00:00:00")
	{
		echo "<tr><td><B>Hora inicial:</B> ".$hori."</td>";
		echo "<td><B>Hora final:</B> ".$horf."</td>";
	}
	
	echo "<tr><td><B>Centro de costos:</B> ".$wccocod."</td>";
	echo "<td><B>Empresa:</B> ".$wemp."</td>";
	echo "<td><B>clasificado por:</B> ".$wtip."</td></tr>";
	echo "</table></br>";
    echo "<A href='RepFacGen.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtip=".$wtip."&amp;wccocod=".$wccocod."&amp;wemp=".$wemp."&amp;bandera='1'><center>VOLVER</center></A><br>";
    echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
    echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
    echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
 	echo "<input type='HIDDEN' NAME= 'wtip' value='".$wtip."'>";
    echo "<input type='HIDDEN' NAME= 'wccocod' value='".$wccocod."'>";
    echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	$aux1=$wccocod;
/***********************************Consulto lo pedido ********************/

// si la empresa es diferente a todas las empresas, la meto en el vector solo
// si es todas las empresas meto todas en un vector para luego preguntarlas en un for 
$pr=explode('-', $west);
$west1[0]=trim ($pr[0]);
if ($wemp !='% - Todas las empresas')
	{

		$print=explode('-', $wemp);
		$wempcod[0]=trim ($print[0]);
		$wempnit[0]=trim ($print[1]);
		$empnom[0]=trim ($print[2]);
	//	$empresa[0]=$empCod[0]." - ".$empNit[0]." - ".$empNom[0];
		$num2=1;

	}	
else
	{ 
		$wempcod='%';
		$wempnit='%';
		$num2=2; 
	}

// si el centro de costos es diferente a todas los centros de costos, la meto en el vector solo
// si son todos los costos los empresas meto todas en un vector para luego preguntarlas en un for 
	
if ($wccocod !='%-Todos los centros de costos')
	{
     $wcco=explode('-', $wccocod);
	 $wccostos[0]=trim ($wcco[0]);
	 $descrip[0]=trim ($wcco[1]);
	 $costos1[0]=$wccostos[0]." - ".$descrip[0];
	 $num1=1;
    }	
   else
      { 
	   $wccostos='%';
	   $num1=2;
	  }
// este query fue el que se modifico para que tomara tambien los centros de costos (2007-04-13)
	if ($hori=="00:00:00" or $horf=="00:00:00")
	{
		$q = " SELECT ccocod, ccodes, empcod, empnom, empnit,fenffa, fenfac, fenval+fenabo+fencmo+fencop+fendes, fenfec, fensal "
			."   FROM  ".$wbasedato."_000018,".$wbasedato."_000003,".$wbasedato."_000024 "
			." 	WHERE  fenfec between '".$wfecini."'"
			."    AND '".$wfecfin."'"
			."     AND fencco like '".$wccostos[0]."' "
		    //."     AND fencco like '".$wcco[0]."' "
		    ."    AND fencod like '".$wempcod[0]."' "
		    ."    AND fenest = '".$west1[0]."' "
		    ."    AND fencco=ccocod "
		    ."    AND fencod=empcod "
		    ."  ORDER BY ccocod,empcod,empnit,fenfac ";
		$err = mysql_query($q,$conex);
		
	}else
		{
			
		// este query fue el que se modifico para que tomara tambien los centros de costos (2007-04-13)
			$q = " SELECT ccocod, ccodes, empcod, empnom, empnit,fenffa, fenfac, fenval+fenabo+fencmo+fencop+fendes, fenfec, fensal "
			."   FROM  ".$wbasedato."_000018,".$wbasedato."_000003,".$wbasedato."_000024 "
			." 	WHERE  fenfec between '".$wfecini."'"
			."    AND '".$wfecfin."'"
			." 	AND  ".$wbasedato."_000018.Hora_data between '".$hori."'"
			."    AND '".$horf."'"
			."     AND fencco like '".$wccostos[0]."' "
		    //."     AND fencco like '".$wcco[0]."' "
		    
		    ."    AND fencod like '".$wempcod[0]."' "
		    ."    AND fenest = '".$west1[0]."' "
		    ."    AND fencco=ccocod "
		    ."    AND fencod=empcod "
		    ."  ORDER BY ccocod,empcod,empnit,fenfac ";
		$err = mysql_query($q,$conex);
		
		}
	$num = mysql_num_rows($err);
	
	$i=0;
	$cuenta=0;
	$wtotal = 0;
	$wtotsal= 0;
	$ccostos=' ';
	$wemptot = 0;
	$wempsal = 0;
	$bandera1=0;
	$bandera2=0;
	$wtotfac=0;
	$wsaldo=0;
	$wtotgenfac=0;
	$wtotgensal=0;
	$coloresumido='#DDDDDD';
	$j=1;
	$k=1;
	$wcf6=1;
	echo "<table border=0 align =center>";
	$i=1;
   
	while ($i <= $num) 
		{
			$row = mysql_fetch_array($err);
			
			if ($bandera1==0) 
				{
					$wccocod=$row[0];
					$wccodes=$row[1];
					if ($vol!='SI')
						echo "<th align=right bgcolor=$wcf2 colspan='5'><font size=2 color='FFFFFF'>&nbsp;&nbsp;&nbsp;&nbsp;TOTAL FACTURADO &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  TOTAL SALDO</font></th>";
					 			
				}
			if ($bandera2==0)
			 	{
		  			$wempcod=$row[2];
		  			$wempnom=$row[3];
		  			$wempnit=$row[4];
		 		}
		 	if (($wempcod!=$row[2]) or ($wempcod=$row[2] and $wccocod!=$row[0]))
		 		{
			 		if ($num2==1 and $num1!=1)
					  {
						$wemptot=$wemptot + $wtotfac;
						$wempsal=$wempsal + $wsaldo;
				  	  }
				  	else
				  	  {
					  	if ($vol=='SI')
					  		{
					 			echo "<th align=left bgcolor=$wcf2 colspan='3'><font size=2 color='FFFFFF'>TOTAL EMPRESA</font></th>";
					 			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
					 			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wsaldo,0,'.',',')."</font></th></tr>"; 	
				 			}
				 		else
				 			{
					 			if (is_int ($j/2))
					 				{
	   									$coloresumido='#DDDDDD';
	   									$j=$j+1;
   									}
   								else
   									{	
	   									$coloresumido='#FFFFFF';
	   									$j=$j+1;
   									}
   								if ($wtip=='CODIGO')
		  							{
										echo "<th align=left bgcolor=$coloresumido colspan='3'><font size=2 ><b>Empresa: ".$wempcod." - ".$wempnom."</b></font></th>";
									}
								if ($wtip=='NIT')
									{
										echo "<th align=left bgcolor=$coloresumido colspan='3'><font size=2 ><b>Empresa: ".$wempnit." - ".$wempnom."</b></font></th>";
									}
				 				
				 				echo "<th align=right bgcolor=$coloresumido><font size=2 >".number_format($wtotfac,0,'.',',')."</font></th>";
				 				echo "<th align=right bgcolor=$coloresumido><font size=2 >".number_format($wsaldo,0,'.',',')."</font></th></tr>"; 	
			 				}
						
					}
		    			$wtotal = $wtotal+$wtotfac;
						$wtotsal = $wtotsal+$wsaldo;
		    			$wtotfac=0;
		    			$wsaldo=0;
	    			  
		 		}		
			if ($wccocod!=$row[0])
				{	
					if (($num2==1 and $num1==2) and ($vol!="SI"))
						{	
							if (is_int ($j/2))
					 				{
	   									$coloresumido='#DDDDDD';
	   									$j=$j+1;
   									}
   								else
   									{	
	   									$coloresumido='#FFFFFF';
	   									$j=$j+1;
   									}
							echo "<tr><th align=left bgcolor=$coloresumido colspan='3'><font size=2 >".$wccocod." - ".$wccodes."</font></th>";   
							echo "<th align=right bgcolor=$coloresumido ><font size=2>".number_format($wtotal,0,'.',',')."</font></th>";
		        			echo "<th align=right bgcolor=$coloresumido ><font size=2 >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".number_format($wtotsal,0,'.',',')."</font></th></tr>";						
						}
					else
						{
							echo "<tr><th align=left bgcolor=$wcf3 colspan='3'><font size=2 color='FFFFFF'>TOTAL CENTRO DE COSTOS </font></th>";
							echo "<th align=right bgcolor=$wcf3><font color='FFFFFF' size=2>".number_format($wtotal,0,'.',',')."</font></th>";
		        			echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($wtotsal,0,'.',',')."</font></th></tr>";						
						}
		        	
					$wtotal=0;
					$wtotsal=0;
				}
			if (($bandera1==0) or ($wccocod!=$row[0]))
				{   
					$waux=$wccocod;
					$wccocod=$row[0];
					$wccodes=$row[1];
					$bandera1=1;
					$pinto=0;
					if (($num2==1 and $num1==2) and ($vol!="SI"))
						{
							$wcf6=1;
						}
					else
						{	
							echo "<tr><th align=left bgcolor=$wcf3 colspan='5'><font size=2 color='FFFFFF'>Centro de Costos ".$wccocod." - ".$wccodes."</font></th></tr>";   
						}
				}
			 
			if ($num2==1 and $num1==2)
				echo " ";
		 	else
		 	{
		 	if (($bandera2==0) or ($wempcod!=$row[2]) or ($wempcod=$row[2] and $waux!=$row[0]) )			 	
		 		{	
				 	$wempcod=$row[2];
		  			$wempnom=$row[3];
		  			$wempnit=$row[4];
		  			$bandera2=1;
		  			$pinto=0;
		  			$waux=$row[0];
		  		if ($vol=='SI')
		  			{
		  				if ($wtip=='CODIGO')
		  					{
								echo "<tr><td colspan=9 bgcolor=$wcf2><font color='FFFFFF'><b>Empresa: ".$wempcod." - ".$wempnom."</b></font></td></tr>";   
							}
						if ($wtip=='NIT')
							{
								echo "<tr><td colspan=9 bgcolor=$wcf2><font color='FFFFFF'><b>Empresa: ".$wempnit." - ".$wempnom."</b></font></td></tr>";   
							}
					}
		  			
	  			}
	  		}
			  	
							if ($vol=='SI')
								{	
									if (is_int ($k/2))
										{
	   										$color='#DDDDDD';
	   										$k=$k+1;
   										}
   									else
   										{
	   										$color='#FFFFFF';						   								
	   										$k=$k+1;
   										}
	   								if ($pinto==0)
			  							{
								  			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>FUENTE FACTURA</font></th>";
						        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>NRO FACTURA</font></th>";
						        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>FECHA FACTURA</font></th>";
						        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>VLR FACTURA</font></th>";
						        			echo "<th align=CENTER bgcolor=#ffcc66><font size=2>SALDO FACTURA</font></th>";			        				
											$pinto=1;
					   					}														
	   		                 	
								   		echo '<tr>';
										echo "<th align=right bgcolor=".$color."><font size=2>".$row[5]."</font></th>";
										echo "<th align=right bgcolor=".$color."><font size=2>".$row[6]."</font></th>";
										echo "<th align=right bgcolor=".$color."><font size=2>".$row[8]."</font></th>";
										echo "<th align=right bgcolor=".$color."><font size=2>".number_format($row[7],0,'.',',')."</font></th>";
										echo "<th align=right bgcolor=".$color."><font size=2>".number_format($row[9],0,'.',',')."</font></th>";
										echo '</tr>';
								}
				$wtotgenfac=$wtotgenfac + $row[7];
				$wtotgensal=$wtotgensal + $row[9];
				$wtotfac = $wtotfac+$row[7];
				$wsaldo = $wsaldo+$row[9];
				$i= $i + 1;
		}
	if ($wtotfac==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningún documento en el rango de fechas seleccionado</td><tr>";
		}
	else
		{	
		if ($num2==1 and $num1!=1)
			{
				$wtotal = $wtotal+$wtotfac;
				$wtotsal = $wtotsal+$wsaldo;
				if ($vol!="SI")
					{	
						if (is_int ($j/2))
			 				{
								$coloresumido='#DDDDDD';
								$j=$j+1;
							}
   						else
   							{	
	   							$coloresumido='#FFFFFF';
	   							$j=$j+1;
   							}
						echo "<tr><th align=left bgcolor=$coloresumido colspan='3'><font size=2 >".$wccocod." - ".$wccodes."</font></th>";   
						echo "<th align=right bgcolor=$coloresumido ><font size=2>".number_format($wtotal,0,'.',',')."</font></th>";
	        			echo "<th align=right bgcolor=$coloresumido ><font size=2 >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".number_format($wtotsal,0,'.',',')."</font></th></tr>";						
					}
				else
					{	
						echo "<tr><th align=left bgcolor=$wcf3 colspan='3'><font size=2 color='FFFFFF'>TOTAL CENTRO DE COSTOS </font></th>";
				    	echo "<th align=right bgcolor=$wcf3><font color='FFFFFF' size=2>".number_format($wtotal,0,'.',',')."</font></th>";
				    	echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($wtotsal,0,'.',',')."</font></th></tr>";												
			    	}
				$wemptot=$wemptot + $wtotfac;
				$wempsal=$wempsal + $wsaldo;
				echo "<th align=left bgcolor=$wcf2 colspan='3'><font size=2 color=ffffff>TOTAL GENERAL EMPRESA</font></th>";
				echo "<th align=right bgcolor=$wcf2><font size=2 color=ffffff>".number_format($wemptot,0,'.',',')."</font></th>";
				echo "<th align=right bgcolor=$wcf2><font size=2 color=ffffff>".number_format($wempsal,0,'.',',')."</font></th></tr>";
			    
         	}
         else
         	{
	         	$wtotal = $wtotal+$wtotfac;
				$wtotsal = $wtotsal+$wsaldo;
				if ($vol=='SI')
					{
						echo "<th align=left bgcolor=$wcf2 colspan='3'><font size=2 color='FFFFFF'>TOTAL EMPRESA</font></th>";
					 	echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
					 	echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wsaldo,0,'.',',')."</font></th></tr>"; 	
			 		}
				 		else
				 			{
					 			if (is_int ($j/2))
					 				{
	   									$coloresumido='#DDDDDD';
	   									$j=$j+1;
   									}
   								else
   									{	
	   									$coloresumido='#FFFFFF';
	   									$j=$j+1;
   									}
   								if ($wtip=='CODIGO')
		  							{
										echo "<th align=left bgcolor=$coloresumido colspan='3'><font size=2 ><b>Empresa: ".$wempcod." - ".$wempnom."</b></font></th>";
									}
								if ($wtip=='NIT')
									{
										echo "<th align=left bgcolor=$coloresumido colspan='3'><font size=2 ><b>Empresa: ".$wempnit." - ".$wempnom."</b></font></th>";
									}
				 				echo "<th align=right bgcolor=$coloresumido><font size=2 >".number_format($wtotfac,0,'.',',')."</font></th>";
				 				echo "<th align=right bgcolor=$coloresumido><font size=2 >".number_format($wsaldo,0,'.',',')."</font></th></tr>"; 	
			 				}
				
			
				echo "<tr><th align=left bgcolor=$wcf3 colspan='3'><font size=2 color='FFFFFF'>TOTAL CENTRO DE COSTOS </font></th>";
		    	echo "<th align=right bgcolor=$wcf3><font color='FFFFFF' size=2>".number_format($wtotal,0,'.',',')."</font></th>";
				echo "<th align=right bgcolor=$wcf3><font size=2 color='FFFFFF'>".number_format($wtotsal,0,'.',',')."</font></th></tr>";												
		    	
			}
			
		}
		 
		if ($num1==2 and $num2==2 and $wtotgenfac != 0)
			{	
				echo "<th align=left bgcolor=$wcf2 colspan='3'><font size=2 color='FFFFFF'>TOTAL GENERAL</font></th>";
				echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotgenfac,0,'.',',')."</font></th>";
				echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotgensal,0,'.',',')."</font></th></tr>"; 	
			}
    echo "</table>";
	echo "</br><center><A href='RepFacGen.php?wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtip=".$wtip."&amp;wccocod=".$aux1."&amp;wemp=".$wemp."&amp;bandera='1'>VOLVER</A></center>";   
}
}
?>
</body>
</html>