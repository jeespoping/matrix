<html>
<head>
<title>REPORTE FACTURADO POR ASESOR</title>

<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="JavaScript1.2">
	function onLoad(){	loadMenus();  }
	function Seleccionar(){  
		document.forma.submit();		
	}
	function calendario(id,vrl){
		if (vrl == "1"){
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecini',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		}
		if (vrl == "2"){
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecfin',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		}
	}	
</SCRIPT>

</head>

<!-- Inclusion del calendario -->
<link rel="stylesheet" href="../../zpcal/themes/winter.css" />
<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
<!-- Fin inclusión calendario -->

<?php
include_once("conex.php");
/*
 * REPORTE DE FACTURACION POR CENTRO DE COSTO X ASESOR X CONCEPTO 
 */
//=================================================================================================================================
//PROGRAMA: RepFacxAsexCon.php
//AUTOR: Juan Carlos Hernandez.
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepFacxAsexCon.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+-----------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 |
//+-------------------+------------------------+-----------------------------------------+
//|  2008-09-01       | Juan C. Hernandez M.   | creación del script.					 |
//+-------------------+------------------------+-----------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2008-09-01

/*DESCRIPCION:Este reporte muestra la cantidad de facturas realizadas por cada asesor de Unidad Visual Global, discriminando los
              lentes y las monturas y otros

TABLAS QUE UTILIZA:
 uvglobal_000003 Maestro de centros de costos.
 uvglobal_000004 Maestro de conceptos.
 uvglobal_000018 Información basica de la factura.	
 uvglobal_000066 Relación entre conceptos y procedimientos.	
 
INCLUDES: 
  conex.php = include para conexión mysql            

VARIABLES:
 $wemp_pmla= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");    
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $resultado = 
=================================================================================================================================*/

//Inicio
if(!isset($_SESSION['user'])){
	echo "error";
}else{
	
	

  	

  	
  	include_once("root/comun.php");
  
  if(!isset($wemp_pmla))
    {
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$conex = obtenerConexionBD("matrix");

	$institucion = ConsultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;
	
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  	
  	//$wentidad="CLINICA DEL SUR";
  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="RepFacxAsexCon.php";  //nombre del reporte
  	
  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";   //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec7";  //Fondo encabezado del detalle
  	$wclfg="003366";  //Color letra parametros
  	
  	echo "<form action='RepFacxAsexCon.php' method=post name='forma'>";
  	//echo "<input type='HIDDEN' NAME='wbasedato' value='".$wbasedato."'>";

  if (!isset($wfecini) or !isset($wfecfin) or !isset($wgrucod) or !isset($wccocod))
  	{
  		echo "<center><table border=0>";
  		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/ips/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
  		echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE FACTURACION POR ASESOR</b></font></td></tr>";
  		 
		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{
  			$wfecini=$wfecha;
  			$wfecfin=$wfecha;
  		}	
  		
		//Fecha inicial de consulta	
  		$cal="calendario('wfecini','1')";
  		echo "<tr>";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha inicial facturaci&oacute;n </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10>";
  		echo "<button id='trigger1' onclick=".$cal.">...</button>";
  		echo "</td>";
  		echo "</td>";
  		
  		//Fecha final de consulta
  		$cal="calendario('wfecfin','2')";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha final facturaci&oacute;n: </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10>";
  		echo "<button id='trigger2' onclick=".$cal.">...</button>";
  		echo "</td>";
  		echo "</tr>";

		//Centro de costos  		
  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Centro de costos:</font></b>";
  		echo "<select name='wccocod' style='width: 350px'>";
  		$q=  "SELECT ccocod, ccodes "
  		."    FROM ".$wbasedato."_000003 "
  		."    ORDER by 1";
  		$res1 = mysql_query($q,$conex);
  		$num1 = mysql_num_rows($res1);
  		if ($num1 > 0 )
  		{
  			echo "<option>%-Todos los centros de costos</option>";
  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
  				echo "<option>".$row1[0]." - ".$row1[1]." - ".$row1[2]."</option>";
  			}
  		}
  		echo "</select></td>";

  		//Conceptos
  		echo "<td align=center bgcolor=".$wcf." ><b><font text color=".$wclfg.">Concepto : </font></b>";
  		echo "<select name='wgrucod' style='width: 450px'>";
  		$q2= "SELECT grucod, grudes "
  		."    FROM ".$wbasedato."_000004 "
  		."    WHERE gruabo != 'on'  "
  		."     order by grucod, grudes ";
  		$res2 = mysql_query($q2,$conex);
  		$num2 = mysql_num_rows($res2);
  		echo "<option>%-Todos los conceptos</option>";
  		for ($i=1;$i<=$num2;$i++)
  		{
  			$row2 = mysql_fetch_array($res2);
  			echo "<option>".$row2[0]." - ".$row2[1]."</option>";
  		}
  		echo "</select></td>";
  		echo "</tr>";
  		
  		
  		echo "<tr>";
        echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";
        echo "</tr>";
    	echo "</table>";
    	
    	funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecini',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
    	funcionJavascript("Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfecfin',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});");
  	} 
   else 
      {
  		echo "<table border=0 width=100%>";
  		echo "<tr><td align=left><B>Facturacion:</B>".$wentidad."</td>";
  		echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
  		echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>";
  		echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
  		echo "</table>";
  		echo "<table border=0 align=center >";
  		echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";
  		
  		echo "<tr><td align=center><font size=4 text color=".$wcf2."><b>REPORTE DE FACTURACION POR ASESOR</b></font></td></tr>";
  		
  		echo "<tr><td align=center bgcolor=".$wcf2."><font size=8 text color=#FFFFFF><b>ESTA EN PRUEBA</b></font></td></tr>";
  		
        echo "</table></br>";

        echo "<table border=0 align=center >";
  		echo "<tr><td><B>Fecha inicial:</B> ".$wfecini."</td>";
  		echo "<td><B>Fecha final:</B> ".$wfecfin."</td>";
  		echo "<td><B>Concepto :</B> ".$wgrucod."</td></tr>";
  		echo "<tr><td colspan=3><B>Centro de Costo :</B> ".$wccocod."</td></tr>";
  		echo "</table>";

  		echo "<A href='RepFacxAsexCon.php?wfecini=".$wfecini."&wfecfin=".$wfecfin."&wgrucod=".$wgrucod."><center>VOLVER</center></A><br>";
  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wgrucod' value='".$wgrucod."'>";
  		
  		//Preparación de los parámetros
  		$vecCco=explode('-', $wccocod);
  		$vecGru=explode('-', $wgrucod);
  		//$vecPro=explode('-', $wprocod);
	
  		/*
     	//Se hace la consulta de las facturas afectadas segun las condiciones del reporte y las llevo a una tabla temporal
	  	$q = " CREATE TEMPORARY TABLE if not exists facturas as " 
	  	    ." SELECT fenffa, fenfac, fenfec, fenval, fenabo, fencop, fendes, fenvnc, fenvnd, fensal, fenrbo, fendev "
		    ."   FROM ".$wbasedato."_000018, ".$wbasedato."_000065 "
		    ."  WHERE fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
		    ."    AND fencco LIKE '".$vecCco[0]."'"
		    ."    AND fenest = 'on' "
		    ."    AND fenffa = fdefue "
		    ."    AND fenfac = fdedoc "
		    ."    AND fdecon LIKE '".$vecGru[0]."'"
		    ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12 ";
		$errfac = mysql_query($q,$conex) or die (mysql_errno()." - ".$q." - ".mysql_error());
		*/
		
		
		//********************************************************************************************************
		//Query en donde se trae el vendedor de Lente, Montura de acuerdo con el conpceto facturado,
		//cuando es diferente a estos conceptos se trae el vendedor en blanco y para cualquier
		//concepto diferente a los anteriores.
		//********************************************************************************************************
		$q = " CREATE TEMPORARY TABLE if not exists facturas as "
		    //Ventas del CONCEPTO DE LENTES OFTALMICOS y que la factura si tenga orden de laboratorio
		    ." SELECT ordvel VEN, fdecon CON, COUNT(*) CAN, SUM(fdevco-fdevde) VAL"
			."   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000133, ".$wbasedato."_000040 "
			."  WHERE fenffa = fdefue "
			."    AND fenfac = fdefac "
			."    AND fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
			."    AND fencco LIKE '".trim($vecCco[0])."'"
			."    AND fdecon = 'LO' "
			."    AND fenffa = ordffa "
			."    AND fenfac = ordfac "
			."    AND fenest = 'on' "
			."    AND fenffa = carfue "
			."    AND carfac = 'on' "
			."    AND carest = 'on' "
			."  GROUP BY 1,2 "
			."  UNION ALL "
			//Ventas del CONCEPTO DE LENTES ESPECIALES y que la factura si tenga orden de laboratorio
			." SELECT ordvel VEN, fdecon CON, COUNT(*) CAN, SUM(fdevco-fdevde) VAL"
			."   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000133, ".$wbasedato."_000040 "
			."  WHERE fenffa = fdefue "
			."    AND fenfac = fdefac "
			."    AND fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
			."    AND fencco LIKE '".trim($vecCco[0])."'"
			."    AND fdecon = 'LE' "
 			."    AND fenffa = ordffa "
			."    AND fenfac = ordfac "
			."    AND fenest = 'on' "
			."    AND fenffa = carfue "
			."    AND carfac = 'on' "
			."    AND carest = 'on' "
			."  GROUP BY 1,2 "
			."  UNION ALL "
			//Ventas del CONCEPTO DE MONTURAS y que la factura si tenga orden de laboratorio
			." SELECT ordvem VEN, fdecon CON, COUNT(*) CAN, SUM(fdevco-fdevde) VAL"
			."   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000133, ".$wbasedato."_000040 "
			."  WHERE fenffa = fdefue "
			."    AND fenfac = fdefac "
			."    AND fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
			."    AND fencco LIKE '".trim($vecCco[0])."'"
			."    AND fdecon = 'MT' "
			."    AND fenffa = ordffa "
			."    AND fenfac = ordfac "
			."    AND fenest = 'on' "
			."    AND fenffa = carfue "
			."    AND carfac = 'on' "
			."    AND carest = 'on' "
			."  GROUP BY 1,2 "
			."  UNION ALL "
			//Ventas de otros conceptos pero que la factura si tenga orden de laboratorio
			." SELECT '' VEN, fdecon CON, COUNT(*) CAN, SUM(fdevco-fdevde) VAL"
			."   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000133, ".$wbasedato."_000040, ".$wbasedato."_000004 "
			."  WHERE fenffa  = fdefue "
			."    AND fenfac  = fdefac "
			."    AND fenfec  BETWEEN '".$wfecini."' AND '".$wfecfin."'"
			."    AND fencco LIKE '".trim($vecCco[0])."'"
			."    AND fdecon  NOT IN ('LO','LE','MT') "
			."    AND fenffa  = ordffa "
			."    AND fenfac  = ordfac "
			."    AND fenest  = 'on' "
			."    AND fenffa = carfue "
			."    AND carfac = 'on' "
			."    AND carest = 'on' "
			."    AND fdecon  = grucod "
			."    AND gruabo != 'on' "
			."    AND grutab  = 'NO APLICA' "
			."  GROUP BY 1, 2 "
			."  UNION ALL "
			//Ventas de TODOS LOS CONCEPTOS pero que la factura NO tenga orden de laboratorio, por ende no se 
			//puede saber el vendedor y se suman todos los valores como si fuese en un mismo concepto.
			." SELECT '' VEN, fdecon CON, COUNT(*) CAN, SUM(fdevco-fdevde) VAL"
			."   FROM ".$wbasedato."_000018, ".$wbasedato."_000065, ".$wbasedato."_000040, ".$wbasedato."_000004 "
			."  WHERE fenffa = fdefue "
			."    AND fenfac = fdefac "
			."    AND fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."'"
			."    AND fenfac NOT IN ( SELECT ordfac FROM ".$wbasedato."_000133 WHERE fenffa = ordffa AND fenfac = ordfac) "
			."    AND fencco LIKE '".trim($vecCco[0])."'"
			."    AND fenest = 'on' "
			."    AND fenffa = carfue "
			."    AND carfac = 'on' "
			."    AND carest = 'on' "
			."    AND fdecon  = grucod "
			."    AND gruabo != 'on' "
			."    AND grutab  = 'NO APLICA' "
			."  GROUP BY 1,2 "
			."  ORDER BY 1,2 ";
	    $resfac = mysql_query($q,$conex) or die (mysql_errno()." - ".$q." - ".mysql_error());
	    
	    //On
			echo $q."<br>";
	    
	    $q = " SELECT VEN, CON, SUM(CAN), SUM(VAL) "
	        ."   FROM facturas "
	        ."  GROUP BY 1, 2 "
	        ."  ORDER BY 1, 2 ";
	    $resfac = mysql_query($q,$conex) or die (mysql_errno()." - ".$q." - ".mysql_error());    
	    $numfac = mysql_num_rows($resfac);
	    
	    $row = mysql_fetch_array($resfac);
	    
	    //Inicializo la matriz
	    for ($i=1;$i<=$numfac;$i++)
	       {
		    $wreporte[$i][0]='';   
		    for ($j=1;$j<=7;$j++)
		         { $wreporte[$i][$j]=0; }
	       }      
	    
        $i=1;
        $k=1;
		while ($i <= $numfac)  //Por cada Vendedor hago la busqueda de la informacion a imprimir
		  {
		    $wvendedor=$row[0];
		    
		    if ($row[0]=="")
		       $wreporte[$k][0]="Sin Vendedor";            //Llevo el Vendedor
		      else
		        $wreporte[$k][0]=$row[0];                  //Llevo el Vendedor
		    
		    $wvalotr=0;
		    while ($i<=$numfac and $wvendedor == $row[0])
		      {
			   switch ($row[1])
			     {
				  case "LO":
				      $wreporte[$k][1]=$row[2];            //Cantidad de lentes LO 
					  $wreporte[$k][2]=$row[3];            //Valor de lentes LO 
					  break;  
				  case "LE":
				      $wreporte[$k][3]=$row[2];            //Cantidad de lentes LE 
					  $wreporte[$k][4]=$row[3];            //Valor de lentes LE 
				      break;     
		          case "MT":
		              $wreporte[$k][5]=$row[2];            //Cantidad de lentes MT 
					  $wreporte[$k][6]=$row[3];            //Valor de lentes MT 
					  break;
		          default:
		              $wvalotr=$wvalotr+$row[3];           //Valor Otros Conceptos
		              break;
		         }
		       $row = mysql_fetch_array($resfac);
		       $i++;
		      }
		    $wreporte[$k][7]=$wvalotr;                      //Valor Otros
		    $k++;                                           //Da el numero real de vendedores a llevar a la matriz
		  }     
		   
	     //Imprimo la MATRIZ
	     echo "<center><table>";
	     
	     
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>ASESOR</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>CAN. <br>LENTES</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>VR <br>LENTES</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>CAN. <br>LENTES ESP.</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>VR. <br>LENTES ESP.</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>CAN.<br>MONTURAS</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>VR <br>MONTURAS</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>VR <br>OTROS</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>CAN. <br>FACTURAS</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>FACTURADO</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>ABONOS</th>";
	     echo "<th bgcolor=".$wcf2."><font size=3 text color=#FFFFFF>SALDO</th>";
	     
	     
	     //Inicializo el arreglo para generar los totales
	     for ($j=1;$j<=7;$j++)
	         { $wtot[$j]=0; }
               
	     //Hago la impresion y la suma total de todas las columnas  
	     for ($i=1;$i<=$k-1;$i++)
	        {
		     if (is_integer($i / 2))
			    $wcolor = "FFFFFF";
			   else
			      $wcolor = "00FFFF";     
		        
		     echo "<tr>";   
		     for ($j=0;$j<=7;$j++)
		        {
			     if ($j > 0)   
			        {
			         echo "<td align=right bgcolor=".$wcolor.">".number_format($wreporte[$i][$j],0,'.',',')."</td>";
			         $wtot[$j]=$wtot[$j]+$wreporte[$i][$j];
			        } 
			       else
			          echo "<td bgcolor=".$wcolor.">".$wreporte[$i][$j]."</td>"; 
			    }
		     echo "</tr>";  
		    }
		 
		 echo "<tr>";   
		 echo "<td bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>Totales: </b></td>";
		 for ($j=1;$j<=7;$j++)
		    {
			 echo "<td align=right bgcolor=".$wcf2."><font size=3 text color=#FFFFFF><b>".number_format($wtot[$j],0,'.',',')."</b></td>";   
		    }
		 echo "</tr>";      
		 echo "</table>";       
	  }
}

echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";

?>
</html>