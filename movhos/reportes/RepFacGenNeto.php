<html>
<!--
<head>
  <title>REPORTE DE FACTURACION GENERAL NETO</title>
<SCRIPT LANGUAGE="JavaScript1.2">

function Seleccionar()
  {
	document.forma.submit();
  }
</SCRIPT>
</head>  //-->
<?php
include_once("conex.php");
 /*************************************************************************************
   *     REPORTE DE FACTURACION POR CENTRO DE COSTO Y POR EMPRESA                    *
   *                                                           *  
 *************************************************************************************/
//=================================================================================================================================
//PROGRAMA: RepFacGenNeto.php
//AUTOR: Juan C. Hernandez
  $wautor="Juan C. Hernandez";
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\ips\reportes\RepFacGenNeto.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
		//-------------------I------------------------I---------------------------------------------------------------------
		//	  FECHA          I     AUTOR              I   MODIFICACION
		//-------------------I------------------------I------------------------------------------------------------------------
		//  2008-10-01       I Juan C. Hernandez      I creación del script.
		//-------------------I------------------------I-----------------------------------------------------------------------
		//-------------------I------------------------I-----------------------------------------------------------------------
	
//FECHA ULTIMA ACTUALIZACION 	: 2008-10-01 
  $wactualiz="(2008-10-01)"; 

/*DESCRIPCION:Este reporte presenta la lista de facturas con su valor neto (descontando las notas credito) por centro(s) de costo(s) y por empresa(s) 

MODIFICACIONES: 


		   
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
/*
 include_once("/root/comun.php");
 session_start();
 if(!isset($_SESSION['user']))
 	terminarEjecucion($MSJ_ERROR_SESION_CADUCADA);
 else
   { 
    if(!isset($wemp_pmla)){
	  terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
   }

  $key = substr($user,2,strlen($user));

  $conex = obtenerConexionBD("matrix");

  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

  $wbasedato = $institucion->baseDeDatos;
  $wentidad = $institucion->nombre;

  echo "<form action='RepFacGenNeto.php' method=post name='forma'>";

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  
  $hora = (string)date("H:i:s");
  $wnomprog="-RepFacGenNeto.php-";  //nombre del reporte
  $wcf1="003366";  // color del fondo   -- Azul mas claro
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
  $wcf3="0099CC";  //COLOR DEL FONDO 3  -- GRIS MAS OSCURO
  $wcf4="99CCFF";  //COLOR DEL FONDO 4  -- AZUL
  $wcf5="00CCFF";
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
  $wfecha=date("Y-m-d");   
  
  /*
if (!isset($wfecini) or !isset($wfecfin) or !isset($wccocod) or !isset($wemp) or !isset($wtip) or !isset($resultado))  
  {
    echo "<center><table border=2>";
	echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
	echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURAS</b></font></td></tr>";
	  
	//INGRESO DE VARIABLES PARA EL REPORTE//
	if (!isset ($bandera))
	  {
	   $wfecini=$wfecha;
	   $wfecfin=$wfecha;
	   $wccocod="%-Todos los centros de costos";
	  }
	
	echo "<tr>";  
	echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">FECHA INICIAL (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10></td>";
	echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">FECHA FINAL (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10></td>";
	echo "</tr>";
	echo "<tr>";  
	 
       
	//SELECCIONAR CENTRO DE COSTOS
	if (isset($wccocod))
	   {
		echo "<td align=center bgcolor=".$wcf." align=center><b><font text color=".$wclfg."> Centro de costos";
		
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
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
	
  } 
  /*
 //MUESTRA DE DATOS DEL REPORTE
 else
   {
    ////////////////////////////HORAS	
    $hori=$hi.":".$mi.":".$si;
    $horf=$hf.":".$mf.":".$sf;	
  
	echo "<table border=0 width=100%>";
	echo "<tr><td align=left><B>Facturacion:</B>$wentidad</td>";
	echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
	echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>"; 
	echo "<td align=right><B>Hora :</B> ".$hora."</td></tr>";
	echo "<tr><td></td><td align=right><B>Usuario :</B> ".$key."</td></tr>"; 
	echo "</table>";
	echo "<table border=0 align=center >";
	echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";
	
	echo "</table></br>";
	echo "<table border=0 align=center >";
    echo "<tr><td><B>Fecha inicial:</B> ".$wfecini."</td>";
	echo "<td><B>Fecha final:</B> ".$wfecfin."</td>";
	echo "<td><B>Estado :</B> ".$west."</td></tr>";
	
	echo "<tr><td><B>Centro de costos:</B> ".$wccocod."</td>";
	echo "<td><B>Empresa:</B> ".$wemp."</td>";
	echo "<td><B>clasificado por:</B> ".$wtip."</td></tr>";
	echo "</table></br>";
    echo "<A href='RepFacGenNeto.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wtip=".$wtip."&amp;wccocod=".$wccocod."&amp;wemp=".$wemp."&amp;bandera='1'><center>VOLVER</center></A><br><div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
    echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
    echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
    echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
 	echo "<input type='HIDDEN' NAME= 'wtip' value='".$wtip."'>";
    echo "<input type='HIDDEN' NAME= 'wccocod' value='".$wccocod."'>";
    echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	$aux1=$wccocod;
	/***********************************Consulto lo pedido ********************/

	/*
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
		  
		  $q = " SELECT ccocod, ccodes, empnit, fencod, fenres, fenffa, fenfac, fenfec, fendpa, fennpa, fenval+fenabo+fencmo+fencop, fenvnc, fenrbo, fensal  "
		      ."   FROM  ".$wbasedato."_000018,".$wbasedato."_000003 "
			  ."  WHERE  fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
			  ."    AND fencco like '".$wccostos[0]."' "
			  ."    AND fencod like '".$wempcod[0]."' "
			  ."    AND fenest = '".$west1[0]."' "
			  ."    AND fencco=ccocod "
			  ."    AND fencod=empcod "
			  ."  ORDER BY ccocod,empcod,empnit,fenfac ";
		  $err = mysql_query($q,$conex);
		  $num = mysql_num_rows($err);
		
		
		echo "<table border=0 align =center>";
		
		$row = mysql_fetch_array($err);
		
		while ($i<=$num) 
		   {
			$wccoaux = $row[0];
			echo "<tr>";
			echo "<td>Empresa : ".$row[2]."</td>";
			echo "<th>Factura</th>";
			echo "<th>Fecha</th>";
			echo "<th>Dcto</th>";
			echo "<th>Nombre Usuario</th>";
			echo "<th>Vlr Factura</th>";
			echo "<th>Vlr Nota Credito</th>";
			echo "<th>Vlr Recibo</th>";
			echo "<th>Facturado Neto</th>";
			echo "<th>Vlr Saldo</th>";
			
			while ($i>=$num and $wccoaux == $row[0])
			   {
				echo "<td>".$row[5]."-".$row[6]."</td>";    //Factura
				echo "<td>".$row[7]."</td>";                //Fecha Factura
				echo "<td>".$row[8]."</td>";                //Dcto Uusario
				echo "<td>".$row[9]."</td>";                //Nombre Usuario 
				echo "<td>".$row[10]."</td>";               //Valor Factura
				echo "<td>".$row[11]."</td>";               //Notas Credito
				echo "<td>".$row[12]."</td>";               //Recibos de Caja
				echo "<td>".$row[10]-$row[11]."</td>";      //Facturado menos notas credito
				echo "<td>".$row[13]."</td>";               //Saldo
				
				$wfactot=$wfactot+$row[10];
				$wfaccco=$wfaccco+$row[10];
				$wfacemp=$wfaccco+$row[10];
				
				$wncrtot=$wncrtot+$row[11];
				$wncrcco=$wncrcco+$row[11];
				$wncremp=$wncrcco+$row[11];
				
				$wrctot=$wrctot+$row[12];
				$wrccco=$wrccco+$row[12];
				$wrcemp=$wrccco+$row[12];
				
				$wsaltot=$wsaltot+$row[13];
				$wsalcco=$wsalcco+$row[13];
				$wsalemp=$wsalcco+$row[13];
				
			   }	       
		  }
  }*/
//liberarConexionBD($conex);
?>
</html>