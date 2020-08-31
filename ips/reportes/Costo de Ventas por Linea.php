<html>
<head>
<title>MATRIX</title>
<script type="text/javascript">
function retornar(wemp_pmla,wfecini,wfecfin,bandera,wccoe,wtare)
{
	location.href = "Costo de Ventas por Linea.php?wemp_pmla="+wemp_pmla+"&wfecini="+wfecini+"&wfecfin="+wfecfin+"&bandera="+bandera+"&wcco="+wccoe+"&wtar="+wtare;	
}
</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
/* 2012-07-11 Camilo Zapata, se le adiciona al script en la linea 194 la posibilidad de armar un query diferente para farstore con el objetivo 
			  de que busque el  centro de costos en la tabla 16, esto, debido a que en la tabla 18 de farstore no se almancena el cco.*/
// ==========================================================================================================================================
// febrero 24 de 2012 :  Santiago Rivera Botero
// ==========================================================================================================================================
// - Se modifica query que utiliza la tabla temporal tempocv2 Usando la función cast para mejorar la velocidad del reporte
// - Se actualizan los estilos 
//================================================================================
function DescripcionGrup($codigo)
{
	global $conex;
	global $wbasedato;
	
	$q= "SELECT Grudes"
	   ."  FROM ".$wbasedato."_000004 "
	   ." WHERE Grucod = '".$codigo."' ";
	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());	
	$row = mysql_fetch_array($err);
	return $row[0];	
}

session_start();
if(!session_is_registered("user"))
	echo "error";
else
{ 
	$key = substr($user,2,strlen($user));
	include("conex.php");
	include ("root/comun.php");
	mysql_select_db("matrix");
	echo "<form action='Costo de Ventas por Linea.php' method=post>";
	$wactualiz="2012-07-13";
	encabezado("Reporte de Ventas ",$wactualiz, "clinica");

	//$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	//$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	//$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	//$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   

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
	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<center><table>";
  
	if(!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or !isset($wtar) or isset($bandera))
    {
	
		if( !isset($wfecini ))
			$wfecini = date("Y-m-d"); 
	
		if( !isset($wfecfin ))
			$wfecfin = date("Y-m-d"); 	
		echo "<tr>";  
		echo "<td class='fila1' align=center><b>Fecha Inicial</b> ";
		campoFechaDefecto( "wfecini", $wfecini );
		echo "</td>";
		echo "<td class='fila1' align=center><b>Fecha Final</b> " ;
		campoFechaDefecto( "wfecfin", $wfecfin );
		echo "</td>";
		echo "</tr>";
    
		//CENTRO DE COSTO
		$q = " SELECT ccocod, ccodes "
			."   FROM ".$wbasedato."_000003 "
			."  ORDER BY 1 ";
			 	 
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
		echo "<tr><td align=center class='fila1'><b>SELECCIONE LA SUCURSAL</b> ";
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
		$q = " SELECT tarcod, tardes "
			."   FROM ".$wbasedato."_000025 "
			."  ORDER BY 1 ";
			 	 
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
	    
		echo "<td align=center class='fila1' ><b>SELECCIONE LA TARIFA</b>";
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
		echo "<td align=center class='fila1' colspan=2><input type='submit' value='OK'></td>";                                         
		echo "</tr>";
		echo "</table>";
    }
    else 
    {
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco); 
		echo "<input type='HIDDEN' NAME= 'wtar' value='".$wtar."'>";
		$wtare = explode("-",$wtar); 
		echo "<table>";
		echo "<tr>";  
		echo "<td class='fila1' align=center><b>Fecha Inicial (AAAA-MM-DD): </b>".$wfecini."</td>";
		echo "<td class='fila1' align=center><b>Fecha Final (AAAA-MM-DD): </b>".$wfecfin."</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class='fila1' align=center colspan=2><b>SUCURSAL: </b>".$wcco."</td>";
		echo "</tr>";  
		echo "</table>";
      
		$q = "DROP TEMPORARY TABLE IF EXISTS tempocv1 ";
		$rs = mysql_query($q, $conex);
		//aca creo una tabla temporal con las facturas del periodo
		if($wbasedato=='farstore')
		{
			$q = "  CREATE TEMPORARY TABLE if not exists tempocv1 "
				."(  INDEX idxfenffa(fenffa),INDEX idxfenfac(fenfac) )"
				."  SELECT fenffa, fenfac, fenfec, fdenve, fenval "
				."    FROM ".$wbasedato."_000018, ".$wbasedato."_000019, ".$wbasedato."_000016 "
				."   WHERE fenfec between '".$wfecini."' AND '".$wfecfin."' "
				."     AND fenfac = fdefac "	
				."     AND fenffa = fdeffa "
				."	   AND fenfac = vennfa "
				."	   AND fenffa = venffa "		
				."	   AND vencco = '".$wccoe[0]."' ";
		}else
			{
				$q = "  CREATE TEMPORARY TABLE if not exists tempocv1 "
					."(  INDEX idxfenffa(fenffa),INDEX idxfenfac(fenfac) )"
					."  SELECT fenffa, fenfac, fenfec, fdenve, fenval "
					."    FROM ".$wbasedato."_000018, ".$wbasedato."_000019 "
					."   WHERE fenfec between '".$wfecini."' AND '".$wfecfin."' "
					."     AND fenfac = fdefac "	
					."     AND fenffa = fdeffa "
					."	   AND Fencco = '".$wccoe[0]."' ";
			}
		
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
		$q = "DROP TEMPORARY TABLE IF EXISTS tempocv2 ";
		$rs = mysql_query($q, $conex);
		// obtenemos las ventas a las que pertenecen las factura que se encuentra en la tabla temporal tempocv1
		$q = "  CREATE TEMPORARY  TABLE if not exists tempocv2 "
			."(  INDEX idxvencon(vencon,vennmo) )"
			."  SELECT fenffa, fenfac, fenfec, fenval, vencon, vennmo, vennum  "
			."    FROM tempocv1, ".$wbasedato."_000016 "
			."   WHERE vennum = fdenve "
			."     AND vencco = '".$wccoe[0]."' "
			."   GROUP BY 1,2,3,4,5,6 ";
		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		
		//OJO OJO OJO OJO
		//CON ESTE PROCEDIMIENTO QUE SIGUE, SE HALLAN LOS ARTICULOS FACTURADOS QUE TIENEN UN GRUPO O LINEA DEFINIDA
		//SE DEBE ACTIVAR CUANDO EXISTAN DIFERENCIAS CON EL REPORTE DE FACTURADO VS COSTO POR FACTURA
		//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
		/*
		$q = " SELECT fenffa, fenfac, fenfec, fenval, vencon, vennmo, mdevto, mdeart, artgru "
			."   FROM tempocv2, ".$wbasedato."_000011, ".$wbasedato."_000001 "
			."   WHERE vencon = mdecon "
			."     AND vennmo = mdedoc "
			."     AND mdeart = artcod "
			."     AND mid(artgru,1,instr(artgru,'-')-1) not in (SELECT grucod FROM ".$wbasedato."_000004) ";
		$err_pru = mysql_query($q,$conex); 
		$num = mysql_num_rows($err_pru);   
          
		$wtot=0;
		for($i=0;$i<$num;$i++)
        {
	        $row = mysql_fetch_array($err_pru);
	        echo "<tr>";
			echo "<td>".$row[0]."</td>";
	        echo "<td>".$row[1]."</td>";
	        echo "<td>".$row[2]."</td>";
	        echo "<td>".$row[3]."</td>";
	        echo "<td>".$row[4]."</td>";
	        echo "<td>".$row[5]."</td>";
	        echo "<td>".$row[6]."</td>";
	        echo "<td>".$row[7]."</td>";
	        echo "<td>".$row[8]."</td>";
	        echo "</tr>";
	       
	        $wtot=$wtot+$row[6];
        }
        echo "<tr><td> Total : ".$wtot."</td></tr>";    
        */
        //>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>    
        //HASTA ACA 
      
        /////////////////////////////////////////////////////////////////
	    // REPORTE DEL COSTO DE VENTAS POR LINEA
	    /////////////////////////////////////////////////////////////////
	    echo "<br>";
		echo "<br>";
		echo "<br>";
		echo "<center><table>";
		echo "<tr><td align=center class='fila1'><font size=6 text><b>COSTO DE LAS VENTAS POR LINEA</b></font></td></tr>";
		echo "</table>";
		
		//consultamos los articulos de cada venta y totalizamos el valor sin iva por articulo 
		$q = "  SELECT grucod, grudes, sum(mdevto)  "
	      ."    FROM ".$wbasedato."_000011, ".$wbasedato."_000004, tempocv2, ".$wbasedato."_000001 "
          ."   WHERE vencon = mdecon "
          ."     AND CAST(vennmo AS CHAR) = mdedoc "
          ."     AND artcod = mdeart"
          ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
          ."   GROUP BY 1,2 "; 
    	$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());	
		$num = mysql_num_rows($err);
		
		echo "<table>";
		echo "<th align=CENTER class='encabezadoTabla'><font size=2>CODIGO</font></th>";
		echo "<th align=CENTER class='encabezadoTabla'><font size=2>DESCRIPCION</font></th>";
		echo "<th align=CENTER class='encabezadoTabla'><font size=2>COSTO</font></th>";
		$wtotcostot = 0;
		for($i=0;$i<$num;$i++)
	    {
			if($i % 2 == 0)
				$wclass="fila1";
			else
				$wclass="fila2";
			$row = mysql_fetch_array($err);
			echo "<tr>";
			echo "<td class='".$wclass."'><font size=2>".$row[0]."</font></td>";
			echo "<td class='".$wclass."'><font size=2>".$row[1]."</font></td>";
			echo "<td class='".$wclass."' align=right><font size=2>".number_format($row[2],0,'.',',')."</font></td>";
			echo "</tr>"; 
			$wtotcostot = $wtotcostot + $row[2];
	    }
		echo "<tr class='encabezadoTabla'>";  
		echo "<td colspan=2>TOTAL</td>";
		echo "<td align=right ><font size=2>".number_format($wtotcostot,0,'.',',')."</font></td>";
		echo "<tr>";
		echo "<table/>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td align=center width='150'>";
		$bandera=1;	
		echo "<input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$bandera."\", \"".$wccoe[0]."\",\"".$wtare[0]."\")'/>";
		echo "</td>";
		echo "<td align= center width='150'>";
		echo "<INPUT type='button' value='Cerrar' onClick='cerrarVentana();' style='width:100'>";
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	} 
}
?>
</body>
</html>
