<html>
<head>
<title>MATRIX</title>
</head>
<script type="text/javascript">
function retornar(wemp_pmla,wfecini,wfecfin,bandera,wccoe,wtare)
	{
		location.href = "Ventas X Linea X Tipo de IVA.php?wemp_pmla="+wemp_pmla+"&wfecini="+wfecini+"&wfecfin="+wfecfin+"&bandera="+bandera+"&wcco="+wccoe+"&wtar="+wtare;
		
    }

</script>


<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	
// ==========================================================================================================================================
/* 2012-08-22 se corrigieron los querys para que consulten la relacion entre la venta y la fecha correspondiente en la factura por medio de la 
//	tabla 19
// ==========================================================================================================================================
/* 2012-07-11 Camilo Zapata, se le adiciona al script en la linea 194 la posibilidad de armar un query diferente para farstore con el objetivo 
			  de que busque el  centro de costos en la tabla 16, esto, debido a que en la tabla 18 de farstore no se almancena el cco.*/
// ==========================================================================================================================================
// febrero 20 de 2012 :  Santiago Rivera Botero
// ==========================================================================================================================================
// - Se crean tablas temporales para mejorar la velocidad del reporte
// - Se actualizan los estilos 
//================================================================================
session_start();
if(!session_is_registered("user"))
	echo "error";
else
{ 
	$key = substr($user,2,strlen($user));
	include("conex.php");
	include ("root/comun.php");
	mysql_select_db("matrix");
	echo "<form action='Ventas X Linea X Tipo de IVA.php' method=post>";

    //$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
    //$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
    //$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
    //$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
	$wfecha=date("Y-m-d");

	//consultamos la base de datos de la empresa correspondiente
	$q = " SELECT detapl, detval "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp ";
		
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res); 
  
    if($num > 0 )
    {
	    for($i=1;$i<=$num;$i++)
	    {   
	        $row = mysql_fetch_array($res);
	      
	        if($row[0] == "cenmez")
	            $wcenmez=$row[1];
	         
	        if($row[0] == "afinidad")
	            $wafinidad=$row[1];
			  
			if(strtoupper($row[0]) == "HCE")
	            $whce=$row[1];
	         
	        if($row[0] == "tabcco")
	            $wtabcco=$row[1];
				
			if($row[0] == "camilleros")
	            $wcencam=$row[1];
			 
			if($row[0] == "farpmla")
	            $wbasedato=$row[1];

			if($row[0] == "farmastore")
	            $wbasedato=$row[1];
			 
			if($row[0] == "facturacion")
	            $wbasedato=$row[1];	
        }  
    }
    else
	{ 
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";  
	}	
    
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	$wactualiz="(2012-07-13)";                   // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
	encabezado("Ventas por Linea por Tipo de IVA ",$wactualiz, "clinica");
	
	if(!isset($wfecini) and !isset($wfecfin) and !isset($wcco) and !isset($wtar) or isset($bandera))
    { 
		if( !isset($wfecini ))
			$wfecini = date("Y-m-d"); 
	
		if( !isset($wfecfin ))
			$wfecfin = date("Y-m-d"); 
   
		echo "<center><table>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align='center'>Fecha inicial</td>";
		echo "<td align='center'>Fecha final</td>";
		echo "</tr>";
		echo "<tr class='fila1'>";
		echo "<td align='center'>";
		campoFechaDefecto( "wfecini", $wfecini );
		echo "</td>";
		echo "<td align='center'>";
		campoFechaDefecto( "wfecfin", $wfecfin );
		echo "</td>";
		echo "</tr>";
    
		//CENTRO DE COSTO
		$q =  " SELECT ccocod, ccodes "
			 ."   FROM ".$wbasedato."_000003 "
			 ."  ORDER BY 1 ";
			 	 
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());;
		$num = mysql_num_rows($res);
	    
		echo "<tr><td align=center class='fila1' ><b>SELECCIONE LA SUCURSAL:</b> ";
		echo "<select name='wcco'>";
		//echo "<option>&nbsp</option>";    
		for($i=1;$i<=$num;$i++)
	    {
			$row = mysql_fetch_array($res);
			if(isset($wcco) && $row[0]==$wcco)	
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else
				echo "<option>".$row[0]."-".$row[1]."</option>";	
        }
		echo "</select></td>";

		//SELECCIONAR TARIFA
		$q =  " SELECT tarcod, tardes "
			 ."   FROM ".$wbasedato."_000025 "
			 ."  ORDER BY 1 ";
			 	 
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());;
		$num = mysql_num_rows($res);
	    
		echo "<td align=center class='fila1' ><b>SELECCIONE LA TARIFA:</b> ";
		echo "<select name='wtar'>";
		//echo "<option>&nbsp</option>";    
		for($i=1;$i<=$num;$i++)
	    {
			$row = mysql_fetch_array($res);
			if(isset($wtar) && $row[0]==$wtar)		
				echo "<option selected>".$row[0]."-".$row[1]."</option>";
			else 
				echo "<option>".$row[0]."-".$row[1]."</option>";
        }
		echo "</select></td></tr>";
		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
		echo "<tr>";
		echo "<td align=center class='fila1' colspan=2><input type='submit' value='OK'></td>";                                         //submit
		echo "</tr>";
    }
    else 
    {
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco); 
		echo "<input type='HIDDEN' NAME= 'wtar' value='".$wtar."'>";
		$wtare = explode("-",$wtar); 
		echo "<center><table>";
		echo "<tr>";  
		echo "<td class=fila1 align=center><b><font >Fecha Inicial (AAAA-MM-DD): </font></b>".$wfecini."</td>";
		echo "<td class=fila1 align=center><b><font >Fecha Final (AAAA-MM-DD): </font></b>".$wfecfin."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class=fila1 align=center colspan=2><b><font >SUCURSAL: </font></b>".$wcco."</td>";
		echo "</tr>";  
		echo "</table></center>";
	  
		/////////////////////////////////////////////////////////////////
	    // REPORTE DE VENTAS POR LINEA POR TIPO DE IVA POR CADA FACTURA
	    /////////////////////////////////////////////////////////////////
		
		//se crean temporales para mejorar la velocidad del programa a partir de un query anterior que era muy pesado
		
		//creamos tabla temporal que contiene las facturas por centro de costos que se encuentren activas en un rango de fechas
		$qdt = "DROP TEMPORARY TABLE IF EXISTS tempo0";
		$rsdt= mysql_query($qdt, $conex);
		
		if($wbasedato=='farstore')
		{
			$q = "CREATE TEMPORARY TABLE if not exists tempo0"
		    ."(INDEX idxffa(fenffa),INDEX idxfac(fenfac) )"
		    ."( SELECT fenffa, fenfac, fenfec, fenval "
		    ."    FROM ".$wbasedato."_000018, ".$wbasedato."_000016  "
		    ."   WHERE fenfec between '".$wfecini."' AND '".$wfecfin."' "
			."     AND Fenest = 'on' "
			."	   AND venffa = fenffa "
			."	   AND vennfa = fenfac "
			."     AND vencco = '".$wccoe[0]."' )";	
		}else
			{
				$q = "CREATE TEMPORARY TABLE if not exists tempo0"
				."(INDEX idxffa(fenffa),INDEX idxfac(fenfac) )"
				."( SELECT fenffa, fenfac, fenfec, fenval "
				."    FROM ".$wbasedato."_000018  "
				."   WHERE fenfec between '".$wfecini."' AND '".$wfecfin."' "
				."     AND Fenest = 'on' "
				."     AND Fencco = '".$wccoe[0]."' )";	
			}
	
	    $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
		$qdt = "DROP TEMPORARY TABLE IF EXISTS tempo1";
		$rsdt= mysql_query($qdt, $conex);
        //obtenemos el numero de venta para las facturas que se encuentran en la tabla temporal tempo0
		$q =  "CREATE TEMPORARY TABLE if not exists tempo1"
	         ."(INDEX idxnum(vennum(20)) )"	
		     ."(SELECT vennum  "
             ."   FROM ".$wbasedato."_000016,tempo0 " 
		     ."  WHERE  vencco = '".$wccoe[0]."'  "
			 ."    AND  Vennfa = Fenfac  "
			 ."    AND  Venffa = Fenffa  )";	
	    $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
	    
		// se crea tabla temporal tempo2 con el detalle de cada venta que tiene una factura y por articulo 
		$q2="DROP TEMPORARY TABLE IF EXISTS tempo2";
		$rs=mysql_query($q2, $conex);
		$q = "CREATE TEMPORARY TABLE if not exists tempo2 "
		    ."(INDEX idxenum(vdenum),INDEX idxart(vdeart),INDEX idxgrucod(grucod) )"	
            ."( SELECT fenffa, fenfac, fenfec, fenval,vdecan,vdevun,vdepiv,vdedes,vdeart,vdenum,grucod,grudes,artcod,arttiv,artiva "
            ."    FROM tempo0,tempo1 ,".$wbasedato."_000019 a,".$wbasedato."_000017,".$wbasedato."_000004,".$wbasedato."_000001 " 
            ."   WHERE fenffa = fdeffa "
            ."     AND fenfac = fdefac "
		    ."     AND vennum = fdenve "
		    ."     AND vennum = vdenum "
			."	   AND a.fecha_data = fenfec"
			."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
			."     AND vdeart = artcod )";
		$err = mysql_query($q,$conex) or die (mysql_errno()." cccc- ".mysql_error());
		
		//consultamos tabla temporal tempo2 calculando los totales
	    $q = "SELECT fenffa, fenfac, grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	        ."  FROM  tempo2"
            ." GROUP BY fenffa, fenfac, grucod, grudes, arttiv, artiva ";
          
        $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	    $num = mysql_num_rows($err);
		
		echo "<br>";
	    echo "<br>";
	    echo "<br>";
	    echo "<table align='center' border=0>";  
	    echo "<th align=CENTER class=encabezadoTabla><font size=2>FUENTE</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>NRO FACTURA</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>LINEA</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>DESCRIPCION</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>TIPO IVA</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>% IVA</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>VENTA BRUTA</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>VALOR IVA</font></th>";
		echo "<th align=CENTER class=encabezadoTabla><font size=2>TOTAL FACTURADO</font></th>";

	    $wtotvenbru = 0;
	    $wtotveniva = 0;
	    $wtotventot = 0;
	    $totbrumal =0;
	    $totivamal =0;
	    $tottotmal =0;
		$m=0;
	    for($i=0;$i<$num;$i++)
	    {
		    $row = mysql_fetch_array($err);
		    $exp=explode('-', $row[1]);
		    $facLet=$exp[0]; // letra o primera parte de la factura
		    $facNum=$exp[1]; // numero o segunda parte de la factura
		   	$res=0;

		    if(isset ($conLet) and isset ($conNum) and $conLet==$facLet  and $conNum!=$facNum and $facNum!=$conNum+1)
			    //$res=$facNum-$conNum; se cambia porque generaba error en el consecutivo ej: 10 y seguia 100
		    	$res=1;
			else if(!isset ($conLet) or !isset ($conNum) or !$conLet==$facLet)
			{
				$query = " SELECT venviv "
						."   FROM ".$wbasedato."_000018, ".$wbasedato."_000019 a, ".$wbasedato."_000016 "
		    	        ."  WHERE fenfac= '".$facLet."-".($facNum-1)."' "  
					    ."    AND fenffa = fdeffa " 
						."    AND fenfac = fdefac "
						."	  AND a.fecha_data = fenfec"		
						."    AND fdenve = vennum "
						."    AND vencco = '".$wccoe[0]."' ";

		    	$err3 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
	  			$num3 = mysql_num_rows($err3);
				
		    	if ($num3<=0)
					$res=2;
		    }	
			for($j=1;$j<$res;$j++)
			{
				$vennfa=$facNum-$j;
			    $q = " SELECT  grucod, grudes, arttiv, artiva, SUM(ROUND((vdecan*vdevun/(1+(vdepiv/100))),0)-vdedes), SUM(ROUND((((vdecan*vdevun/(1+(vdepiv/100)))-vdedes)*(vdepiv/100)),0))"
	      			."   FROM ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
					."  WHERE vennfa='".$facLet."-".$vennfa."'"
					."    AND venffa='".$row[0]."'"
					."    AND vencco = '".$wccoe[0]."' "
					."    AND vennum = vdenum "
					."    AND vdeart = artcod "
					."    AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
					."   GROUP BY grucod, grudes, arttiv, artiva ";
					
				$err2 = mysql_query($q,$conex) ;
	  			$num2 = mysql_num_rows($err2);
				
				if($num2>0)
	  			{
					for($k=0;$k<$num2;$k++)
	     			{
		     			if($m % 2 == 0)
							$wclass="fila1";
						else
							$wclass="fila2";	
						$ver=$num2;		
		     			$row2 = mysql_fetch_array($err2);
		     			echo "<tr>";
	      				echo "<td class='".$wclass."'><font size=2>".$row[0]."</font></td>";
						echo "<td class='".$wclass."'><font size=2>".$facLet."-".$vennfa."</font></td>";
						echo "<td class='".$wclass."'><font size=2>".$row2[0]."</font></td>";
						echo "<td class='".$wclass."'><font size=2>".$row2[1]."</font></td>";
						echo "<td class='".$wclass."'><font size=2>".$row2[2]."</font></td>";
						echo "<td class='".$wclass."'><font size=2>".$row2[3]."</font></td>";
						echo "<td class='".$wclass."' align=right><font size=2>".number_format($row2[4],0,'.',',')."</font></td>";
						echo "<td class='".$wclass."' align=right><font size=2>".number_format($row2[5],0,'.',',')."</font></td>";
						echo "<td class='".$wclass."' align=right><font size=2>".number_format($row2[4]+$row2[5],0,'.',',')."</font></td>";
	      				echo "</tr>"; 
						$wtotvenbru = $wtotvenbru + $row2[4];
						$wtotveniva = $wtotveniva + $row2[5];
						$wtotventot = $wtotventot + $row2[4]+$row2[5];
	      				$totbrumal = $totbrumal  + $row2[4];
						$totivamal =$totivamal + $row2[5]; 
						$tottotmal = $tottotmal + $row2[4]+$row2[5];
						$m++;
	  				}	
	  			}
				else
  				{
	  				echo "<tr>";
					echo "<td bgcolor='#ff0000'><font size=2>".$row[0]."</font></td>";
					echo "<td bgcolor='#ff0000'><font size=2>".$facLet."-".$vennfa."</font></td>";
					echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
					echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
					echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
					echo "<td bgcolor='#ff0000'><font size=2>0</font></td>";
					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
					echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(0,0,'.',',')."</font></td>";
	     			echo "</tr>"; 
  				}
  			}
			
			if($m % 2 == 0)
				$wclass="fila1";
			else
				$wclass="fila2";
		    echo "<tr>";
			echo "<td class='".$wclass."'><font size=2>".$row[0]."</font></td>";
			echo "<td class='".$wclass."'><font size=2>".$row[1]."</font></td>";
			echo "<td class='".$wclass."'><font size=2>".$row[2]."</font></td>";
			echo "<td class='".$wclass."'><font size=2>".$row[3]."</font></td>";
			echo "<td class='".$wclass."'><font size=2>".$row[4]."</font></td>";
			echo "<td class='".$wclass."'><font size=2>".$row[5]."</font></td>";
			echo "<td class='".$wclass."' align=right><font size=2>".number_format($row[6],0,'.',',')."</font></td>";
			echo "<td class='".$wclass."' align=right><font size=2>".number_format($row[7],0,'.',',')."</font></td>";
			echo "<td class='".$wclass."' align=right><font size=2>".number_format($row[6]+$row[7],0,'.',',')."</font></td>";
			echo "</tr>"; 
	      
	        $wtotvenbru = $wtotvenbru + $row[6];
	        $wtotveniva = $wtotveniva + $row[7];
	        $wtotventot = $wtotventot + $row[6]+$row[7];
	        $conLet=$facLet; //contenedor del valor anterior de letra
	        $conNum=$facNum; //contenedor del valor anterior de numero
			$m++;
	    }
	    
		echo "<tr>";  
	    echo "<td colspan=6 class='encabezadoTabla'>TOTALES</td>";
	    echo "<td class='encabezadoTabla' align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
	    echo "<td class='encabezadoTabla' align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
	    echo "<td class='encabezadoTabla' align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
	    echo "<tr>";
	  
	    echo "<tr>";
		echo "<td bgcolor='#ff0000' colspan='2'><font size=2>Suma de facturas erroneas</font></td>";
		echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
		echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
		echo "<td bgcolor='#ff0000' ALIGN='center'><font size=2>--</font></td>";
		echo "<td bgcolor='#ff0000'><font size=2>0</font></td>";
		echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format(	$totbrumal,0,'.',',')."</font></td>";
		echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($totivamal ,0,'.',',')."</font></td>";
	    echo "<td align=right bgcolor='#ff0000'><font size=2>".number_format($tottotmal,0,'.',',')."</font></td>";
		echo "</tr>"; 
	    echo "</table>"; 
	    echo "<table align=center border='0'>"; 
	 	echo "<td bgcolor='#ff0000' width='20'><font size=2>&nbsp;</font></td>";
	    echo "<td  ALIGN=center><font size=2>=MALA</font></td>";
	    echo "</table></br></br>"; 
		
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td align=center width='150'>";
		$bandera=1;	
		echo "<input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$bandera."\", \"".$wccoe[0]."\",\"".$wtare[0]."\")'/>";
		echo "</td>";
		echo "<td align= center width='150'>";
		echo "<INPUT type='button' value='Cerrar' onClick='javascript:cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";


	} 
}
?>
</body>
</html>