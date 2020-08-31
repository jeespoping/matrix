<html>
<head>
<title>MATRIX</title>
<script type="text/javascript">

function retornar(wemp_pmla,wfecini,wfecfin,bandera,wcco)
	{
		location.href = "devTIvaTLinea.php?wemp_pmla="+wemp_pmla+"&wfecini="+wfecini+"&wfecfin="+wfecfin+"&bandera="+bandera+"&wcco="+wcco;
		
    }
	
function cerrar_ventana()
	{
		window.close();
    }	

</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
// ==========================================================================================================================================
// ==========================================================================================================================================
// Febrero 9 de 2012 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// - Se intercambiaron los resultados de devoluciones de notas simples con los de nota crédito ya que se encontraban trocados
// - Se actualizaron los estilos con los que se presenta el informe 

 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	include_once("root/comun.php");
	

	$titulo = "REPORTE DE DEVOLUCIONES";
	$wactualiz = "2012-01-30";
	encabezado($titulo,$wactualiz, "clinica");

    //consultamos la base de datos de la empresa correspondiente
	$q = " SELECT detapl, detval "
        ."    FROM root_000050, root_000051 "
        ."   WHERE empcod = '".$wemp_pmla."'"
        ."     AND empest = 'on' "
        ."     AND empcod = detemp ";
		
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
			
			if($row[0] == "facturacion")
	           $wbasedato=$row[1];
			
			if($row[0] == "farmastore")
	           $wbasedato=$row[1];	
        } 		
       
    }
    else
	{ 
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";		 
	}
  
	echo "<form action='devTIvaTLinea.php' method=post>";

	//$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
	//$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
	//$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
	//$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro   
    
    $wfecha=date("Y-m-d");   
  
    //$wbasedato='farstore';
    echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
    //echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
  
  
    //echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
    //echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE DEVOLUCIONES</b></font></td></tr>";
  
    if(!isset($wfecini) or !isset($wfecfin) or !isset($wcco) or isset($bandera))
	{
		/**********************************************Primera pagina**************************/
		if(!isset($wfecini ) && !isset($wfecfin ))
		{
			$wfecini = date("Y-m-d");
			$wfecfin = date("Y-m-d");
		}
	
		echo "<table align=center>";
		echo "<tr>";  
		echo "<td class=fila1 align=center><b><font>Fecha Inicial: </font></b>";
		campoFechaDefecto("wfecini", $wfecini);
		echo "</td>";
		echo "<td class=fila1 align=center><b><font>Fecha Final : </font></b>";
		campoFechaDefecto("wfecfin", $wfecfin);
		echo "</td>";
		echo "</tr>";
    
		//CENTRO DE COSTO
		$q =  " SELECT ccocod, ccodes "
			 ."   FROM ".$wbasedato."_000003 "
			 ."  ORDER BY 1 ";
			 	 
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
			
		echo "<tr><td align=center class=fila1 colspan=2><b><font>SELECCIONE LA SUCURSAL:</font></b>";
		echo "<select name='wcco'>";
		//echo "<option>&nbsp</option>";    
		for($i=1;$i<=$num;$i++)
		{ 
			$row = mysql_fetch_array($res);
			if(isset($wcco) && $row[0]==$wcco)
				echo "<option selected >".$row[0]."-".$row[1]."</option>";
			else			
				echo "<option>".$row[0]."-".$row[1]."</option>";
        }
		echo "</select></td>";
	
		
    
    
		/*SELECCIONAR TARIFA
		$q =  " SELECT tarcod, tardes "
			 ."   FROM ".$wbasedato."_000025 "
			 ."  ORDER BY 1 ";
					 
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
			
		echo "<td align=center bgcolor=".$wcf." >SELECCIONE LA TARIFA: ";
		echo "<select name='wtar'>";
		//echo "<option>&nbsp</option>";    
		for ($i=1;$i<=$num;$i++)
		   {
			$row = mysql_fetch_array($res); 
			echo "<option>".$row[0]."-".$row[1]."</option>";
		   }
		echo "</select></td></tr>";*/
	
		echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
		echo "<tr>";
		echo "<td align=center class=fila1 colspan=2><input type='submit' value='OK'></td>";                                         //submit
		echo "</tr>";
		echo "</table>";
    }
    else 
    {
		/**********************************************Segunda página**************************/
		/***********************************Consulto las devouciones sin nota credito********************/
		echo "<input type='HIDDEN' NAME= 'wcco' value='".$wcco."'>";
		$wccoe = explode("-",$wcco); 
		
		echo "<table align=center>";
		echo "<tr>";  
		echo "<td class=fila1 align=center><b><font text >Fecha Inicial: ".$wfecini."</font></b></td>";
		echo "<td class=fila1 align=center><b><font text >Fecha Final: ".$wfecfin."</font></b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td class=fila1 align=center colspan=2><b><font>SUCURSAL: ".$wcco."</font></b></td>";
		echo "<tr><td align=center colspan=2 class=fila1><font size=6><b>DEVOLUCIONES POR LINEA POR TIPO DE IVA</b></font></td></tr>";
		echo "</tr>";  
		echo "</table >";
		   
		echo "</BR></BR><center><table >";
        echo "<tr><td align=center colspan=2 class=fila1><font size=6 text><b>DEVOLUCIONES SIMPLES</b></font></td></tr>";
	    echo "</center></table >";
	    $table=date("Mdis").'1';
	   
	    $query = "  CREATE TEMPORARY TABLE if not exists $table as ";
	    $query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto, tranum ";
	    $query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011,  " .$wbasedato."_000055  ";
	    $query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='801' and mencco='$wccoe[0]' ";
	    $query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
	    $query= $query. "and tradev=mendoc and traven = Menfac and  tracco ='$wccoe[0]'and tratip <>'01-ANULACION' ";
	
        $err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
       	
		$q = "  SELECT menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), tranum, vennfa"
			."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
            ."   WHERE Menfec between '".$wfecini."'"
            ."     AND '".$wfecfin."'"
            ."     AND Menfac = vennum "
            ."     AND vennum = vdenum "
            ."     AND vdeart=Mdeart "
            ."     AND artcod=Mdeart "
            ."     AND Mencco = '".$wccoe[0]."'"
            ."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
            ."   GROUP BY venffa, vennum, grucod, grudes, arttiv, artiva "
            ."   ORDER BY Menfec, venffa, vennum";

        $err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	  	$num = mysql_num_rows($err); 
		
	  	if($num>0)
	  	{
			for($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
				
				for ($j=0;$j<12;$j++)
				{
					$vector[$i][$j]=$row[$j]; 	
				}
					

			}
			
			echo "<table align=center>";
	  
			echo "<th align=CENTER class=encabezadoTabla><font size=2>FUENTE</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>FACTURA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>Nº NOTA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>LINEA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>DESCRIPCION</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>TIPO IVA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>IVA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>VENTA BRUTA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>VALOR IVA</font></th>";
			echo "<th align=CENTER class=encabezadoTabla><font size=2>TOTAL FACTURADO</font></th>";
			
			
			$wtotvenbru = 0;
			$wtotveniva = 0;
			$wtotventot = 0;
			
			for($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";
					
				echo "<tr>";
				echo "<td class=".$wclass."><font size=2>".$vector[$i][2]."</font></td>";
				echo "<td class=".$wclass."><font size=2>".$vector[$i][11]."</font></td>";
				echo "<td ALIGN='CENTER' class=".$wclass."><font size=2>".$vector[$i][10]."</font></td>";
				echo "<td class=".$wclass."><font size=2>".$vector[$i][3]."</font></td>";
				echo "<td class=".$wclass."><font size=2>".$vector[$i][4]."</font></td>";
				echo "<td class=".$wclass."><font size=2>".$vector[$i][5]."</font></td>";
				echo "<td class=".$wclass."><font size=2>".$vector[$i][6]."</font></td>";
				echo "<td align=right class=".$wclass."><font size=2>".number_format($vector[$i][7],0,'.',',')."</font></td>";
				echo "<td align=right class=".$wclass."><font size=2>".number_format($vector[$i][8],0,'.',',')."</font></td>";
				echo "<td align=right class=".$wclass."><font size=2>".number_format($vector[$i][9],0,'.',',')."</font></td>";
				echo "</tr>"; 
		  
				$wtotvenbru = $wtotvenbru + $vector[$i][7];
				$wtotveniva = $wtotveniva + $vector[$i][8];
				$wtotventot = $wtotventot + $vector[$i][9];
			}

			echo "<tr class=encabezadoTabla>";  
			echo "<td colspan=7>TOTALES</td>";
			echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
			echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
			echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
		 
			echo "<tr>";
			
			echo "</table>";
		}
       
	    /***********************************Consulto las devouciones con nota credito********************/
        echo "</BR></BR><center><table>";
		echo "<tr><td align=center colspan=2 class=fila1 ><font size=6><b>DEVOLUCIONES CON NOTA CRÉDITO</b></font></td></tr>";
		echo "</center></table >";
        
		$table=date("Mdis").'2';
    	$query = "  CREATE TEMPORARY TABLE if not exists $table as ";
		$query= $query. "SELECT mendoc, menfac, menfec, mencco, mdeart, mdecan, mdevto ";
		$query= $query. "FROM " .$wbasedato."_000010, " .$wbasedato."_000011 ";
		$query= $query. "WHERE menfec BETWEEN '$wfecini' AND '$wfecfin' and mencon='801' and mencco='$wccoe[0]' ";
		$query= $query. "and mendoc = mdedoc and  mdecon = mencon ";
		$query= $query. "and Mendoc not in (select tradev FROM " .$wbasedato."_000055 where tradev = Mendoc and traven = Menfac and  tracco ='$wccoe[0]') ";
         	   
        
		$err = mysql_query($query,$conex)or die (mysql_errno()." - ".mysql_error());
	  
	  
	    $q = "  SELECT menfac, menfec, venffa, grucod, grudes, arttiv, artiva, SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan), SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0)), SUM(ROUND((Mdecan*vdevun/(1+(vdepiv/100))),0)-vdedes*Mdecan/vdecan) + SUM(ROUND((((Mdecan*vdevun/(1+(vdepiv/100)))-vdedes*Mdecan/vdecan)*(vdepiv/100)),0))"
	        ."    FROM $table, ".$wbasedato."_000001, ".$wbasedato."_000016, ".$wbasedato."_000017, ".$wbasedato."_000004 "
	        ."   WHERE Menfec between '".$wfecini."'"
			."     AND '".$wfecfin."'"
			."     AND Menfac = vennum "
			."     AND vennum = vdenum "
			."  AND vdeart=Mdeart "
			."  AND artcod=Mdeart "
			."     AND Mencco = '".$wccoe[0]."'"
			."     AND mid(artgru,1,instr(artgru,'-')-1) = grucod "
			."   GROUP BY venffa, vennum, grucod, grudes, arttiv, artiva "
			."   ORDER BY Menfec, venffa, vennum";
	  
	    $err = mysql_query($q,$conex);
	    $num = mysql_num_rows($err);
	 
	  	if($num>0)
	  	{
	    
			for($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_row($err);
		   	
				for($j=0;$j<10;$j++)
				{
				$vector[$i][$j]=$row[$j]; 	
				}
	   			

			}
	    
			echo "<table align=center>";
	  
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>FUENTE</font></th>";
	 		echo "<th align=CENTER class='encabezadoTabla'><font size=2>FACTURA</font></th>";
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>LINEA</font></th>";
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>DESCRIPCION</font></th>";
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>TIPO IVA</font></th>";
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>IVA</font></th>";
	 		echo "<th align=CENTER class='encabezadoTabla'><font size=2>VENTA BRUTA</font></th>";
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>VALOR IVA</font></th>";
	  		echo "<th align=CENTER class='encabezadoTabla'><font size=2>TOTAL FACTURADO</font></th>";
	  	
		
			$wtotvenbru = 0;
			$wtotveniva = 0;
			$wtotventot = 0;
	  	
			for($i=0;$i<$num;$i++)
			{
				$query= "SELECT fdefac ";
				$query= $query. "FROM " .$wbasedato."_000019  ";
				$query= $query. "WHERE fdeffa='".$vector[$i][2]."' and fdenve='".$vector[$i][0]."' ";
				
				$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
				$cantidad = mysql_num_rows($err);
				
				if($i % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";
			
				if($cantidad>=1)
				{
					$res= mysql_fetch_row($err);
				
					echo "<tr>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][2]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$res[0]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][3]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][4]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][5]."</font></td>";
					echo "<td class=".$wclass."><font size=2>".$vector[$i][6]."</font></td>";
					echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][7],0,'.',',')."</font></td>";
					echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][8],0,'.',',')."</font></td>";
					echo "<td class=".$wclass." align=right><font size=2>".number_format($vector[$i][9],0,'.',',')."</font></td>";
					echo "</tr>"; 
			  
					$wtotvenbru = $wtotvenbru + $vector[$i][7];
					$wtotveniva = $wtotveniva + $vector[$i][8];
					$wtotventot = $wtotventot + $vector[$i][9];
				}
			}
	  
	   
		    echo "<tr class='encabezadoTabla'>";  
			echo "<td colspan=6>TOTALES</td>";
			echo "<td align=right><font size=2>".number_format($wtotvenbru,0,'.',',')."</font></td>";
			echo "<td align=right><font size=2>".number_format($wtotveniva,0,'.',',')."</font></td>";
			echo "<td align=right><font size=2>".number_format($wtotventot,0,'.',',')."</font></td>";
			echo "<tr>";
		   
			echo "</table>";  
		}
		
	    $bandera=1;
	    echo "<br/>";
	    echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecini."\",\"".$wfecfin."\",\"".$bandera."\", \"".$wccoe[0]."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
		/***********************************Consulto las devouciones con nota credito********************/	 
	}
	 
}
?>
</body>
</html>
