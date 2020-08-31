<html>
<head>
<title>MATRIX</title>
<script type="text/javascript">

function retornar(wemp_pmla,wfecha_i,wfecha_f,bandera,wcco0){		
	
	location.href = "rep_formapagxvta.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&bandera="+bandera+"&wcco0="+wcco0;
		
}

function cerrar_ventana(cant_inic){
	window.close();
}

function enter(){

	if (document.facturas.wcco0.selectedIndex==" "){
		   alert("Debe seleccionar un centro de costos.");
		   document.facturas.wcco0.wcentral.focus(); 
		}
		else 	
		   document.facturas.submit();
}	
function cambiar_celda(celda,filas){
	try{
		document.getElementById(celda).rowSpan = filas;
	}
	catch(e){
		alert(e);
	}
}
</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
/***************************************************
*	  REPORTE DE FACTURAS POR CENTRO DE COSTOS  *
          AUTOR: Ing. Santiago Rivera Botero
		  FECHA DE CREACIÓN : 2012-01-16
**************************************************/
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	
	$key = substr($user,2,strlen($user));
	

	include_once("root/comun.php");
	

	echo "<form action='rep_formapagxvta.php' name='facturas' method=post>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	$titulo = "Reporte Formas De Pago Por Venta";
	$wactualiz = "2012-01-23";
	encabezado($titulo,$wactualiz, "clinica"); 
	
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
		 
		 if($row[0] == "movhos")
			$wbasedato=$row[1];
		  
		 if(strtoupper($row[0]) == "HCE")
			$whce=$row[1];
		 
		 if($row[0] == "tabcco")
			$wtabcco=$row[1];
			
		 if($row[0] == "camilleros")
			$wcencam=$row[1];
		 
		 if($row[0] == "farpmla")
			$wfarmpla=$row[1];		
		}  
    }
        else
		    { 
             echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";  
	        }
	
	if(!isset($wfecha_i) or !isset($wfecha_f) or !isset($wcco0) or isset($bandera))
	{
		if(!isset($wfecha_i ) && !isset($wfecha_i ))
		{
			$wfecha_i = date("Y-m-d");
			$wfecha_f = date("Y-m-d");
		}
		$q = "SELECT Ccocod,Ccodes "
            ."  FROM ".$wfarmpla."_000003 ";
			
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		$num = mysql_num_rows($res);
		
		echo "<center><table border=0>";
		
		echo "<tr><td class=fila1 align=center><b>Fecha Inicial</b></td>";
		echo "<td class=fila1 align=center>";
		campoFechaDefecto("wfecha_i", $wfecha_i);
		echo "</td></tr>";
		echo "<tr><td class=fila1 align=center><b>Fecha Final</b></td>";
		echo "<td class=fila1 align=center>";
		campoFechaDefecto("wfecha_f", $wfecha_f);
		echo "</td></tr>";
		
		echo "<tr><td colspan=2 class=fila1 align=center><b> Centro de Costos</b></td></tr>";	
		echo "<tr><td colspan= 2 align =center class=fila1>";
		echo "<select name='wcco0' id='wcco0'>";
		echo "<option></option>";
		
		for($i = 1; $i <= $num; $i++) 
	    {
          $row = mysql_fetch_array($res);
		 
		  if(isset($wcco0) && $row[0]==$wcco0)
			  echo "<option selected value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
		  else
			  echo "<option value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
        }
		
		echo "</select>";
		echo "</td></tr>";
		echo "<tr><td class = fila1 colspan=2 align= center><input type=radio name=tipo value=resumido onclick='enter()' /> <b>Resumido</b> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <input type=radio name=tipo value=detallado onclick='enter()' /> <b>Detallado</b> </td></tr></center> ";
		echo "<tr><td colspan=2 align='center' ><br/>&nbsp; &nbsp;<input type='button' align='right' name='btn_cerrar' value='CERRAR' onclick='cerrar_ventana()'/></td></tr></table>";
		echo "</form>";	
	}
	else
	{
		$wcco1=explode("-",$wcco0);
		
		// realizamos la consulta de las facturas del centro de costos y la llevamos a una tabla temporal
		$q= "CREATE TEMPORARY TABLE	IF NOT EXISTS facturas "
		   ."( INDEX cod_idx( fencco(10) ) )"
		   ."SELECT C.fencco ,C.Fenfec ,B.Rennum ,F.Cardes ,E.Fpades ,A.Rfpvfp ,C.Fenfac ,C.Fenval, B.Renfue  "
           ."  FROM ".$wfarmpla."_000022 A, ".$wfarmpla."_000020 B, ".$wfarmpla."_000018 C, ".$wfarmpla."_000021 D, ".$wfarmpla."_000023 E, ".$wfarmpla."_000040 F "  
           ." WHERE D.Rdefac = C.Fenfac "
           ."   AND A.Rfpnum = D.Rdenum "
           ."   AND A.Rfpfue = D.Rdefue "
	       ."   AND A.Rfpcco = D.Rdecco "	 
           ."   AND B.Rennum = D.Rdenum "
	       ."   AND B.Renfue = D.Rdefue "
           ."   AND B.Rencco = D.Rdecco " 
	       ."   AND C.Fenfec BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
           ."   AND C.fencco = '".$wcco1[0]."' " 
           ."   AND A.Rfpfpa = E.Fpacod "
           ."   AND B.Renfue = F.Carfue "
		   ."   AND F.Carrec = 'on' " 
           ." ORDER BY Fenfac, B.Rennum, E.Fpades ";
	
		$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );

		// realizamos la consulta de la tabla temporal
		$q = "SELECT * FROM facturas ";
		
		$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error());
		$num = mysql_num_rows($res);
		
		//imprimimos los parametros los cuales el usuario elige	
		echo "<center>";
		echo "<table border=0>";
		
		echo "<tr class=encabezadoTabla>";
		echo "<td align=center >Centro Costo </td>";
		echo "<td  class=fila1 align='left'>".$wcco0."</td>";
		
	    echo "</tr>";
		echo "<tr class=encabezadoTabla>";
		echo "<td align=center >Fecha Inicial </td>";
		echo "<td  class=fila1 align='left'>".$wfecha_i."</td>";
		echo "</tr>";
		
		echo "<tr class=encabezadoTabla	>";
		
		echo "<td align=center >Fecha Final </td>";
		echo "<td  class=fila1 align='left'>".$wfecha_f."</td>";
		
		echo "</tr>";
		
		echo "<tr class=encabezadoTabla>";
		echo "<td  class=fila1 colspan='2' align='center'>".strtoupper($tipo)."</td>";
		echo "</tr>";
		
		echo "</table>";
		echo "</center>";
		
		
		
		//realizamos la impresion de las facturas si selecciona detallado
		echo "<br/>";
		
		if($tipo == "detallado")
		{
			echo "<center><table border=0>";
			
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Fecha <br/> Factura</td>";
			echo "<td align=center >Número De <br/> Recibo</td>";
			echo "<td align=center>Forma <br/>Pago</td>";
			echo "<td align=center>Valor<br/></td>";
			echo "<td align=center>Número De <br/> Factura</td>";
			echo "<td align=center>Valor<br> Factura</td>";
			echo "</tr>";
            $wvalor=0;
			$wvalorfactura=0;
			$fa="";
			$fa1="";
			$j=0;
			$rowspan=1;
			for ($i=0;$i<$num;$i++)
			{
				
				if($j % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";
					
				
				$row = mysql_fetch_array($res);
				
				if($fa == $row["Fenfac"])
				{
					$celda_anterior = $i -$rowspan;
					echo "<tr>";
					
					if($fa1 != $row["Fpades"])
					{
						echo "<td class=".$wclass2." align=right>".$row["Fpades"]."</td>";
						
					}
					echo "<td class=".$wclass2." align=right>".number_format($row["Rfpvfp"])."</td>";
					echo "</tr>";
	
					$id="a".$celda_anterior;
					$id2="b".$celda_anterior;
					$id3="c".$celda_anterior;
					$id4="d".$celda_anterior;
					$id5="e".$celda_anterior;
					$rowspan++;
					
					//llamamos la función javascript para cambiar el rowspan del primer el elemento de los que se encuentran repetidos
					echo "<script> cambiar_celda('".$id."','".$rowspan."'); </script>";
					echo "<script> cambiar_celda('".$id2."','".$rowspan."'); </script>";
					if($fa1 == $row["Fpades"])
					{
						echo "<script> cambiar_celda('".$id5."','".$rowspan."'); </script>";
					}
					echo "<script> cambiar_celda('".$id3."','".$rowspan."'); </script>";
					echo "<script> cambiar_celda('".$id4."','".$rowspan."'); </script>";
					
					$j++;	
				}
				else
				{
				    $rowspan=1;
				    $id2="b".$i;
				    $id="a".$i;
				    $id3="c".$i;
				    $id4="d".$i;
				    $id5="e".$i;
				  
				    echo "<tr>";
				    echo "<td id='".$id3."' class=".$wclass." align=right>".$row["Fenfec"]."</td>";
				    echo "<td id='".$id2."' class=".$wclass." align=right>". $row["Renfue"]." - ".$row["Rennum"]."</td>";
				    echo "<td id='".$id5."' class=".$wclass." align=right>".$row["Fpades"]."</td>";
				    echo "<td class=".$wclass." align=right>".number_format($row["Rfpvfp"])."</td>";
				    echo "<td id='".$id4."' class=".$wclass." align=right>".$row["Fenfac"]."</td>";
				    echo "<td id='".$id."' class=".$wclass." align=right rowspan=".$rowspan." >".number_format($row["Fenval"])."</td>";
				    echo "</tr>";
				    $fa=$row["Fenfac"];
				    $fa1 = $row["Fpades"];
			
				    $wvalorfactura+=$row["Fenval"];
				    $wclass2 = $wclass;	
				}
				
				$wvalor+=$row["Rfpvfp"];
				$j++;
			
			}
			echo "<tr class=encabezadoTabla><td colspan=3 align=center>Total:</td>  <td> ".number_format($wvalor)."</td><td ></td><td class=encabezadoTabla >".number_format($wvalorfactura)."</td></tr>";
			echo "</table></center>"; 
		}
		//totalizamos las facturas por forma de pago y las imprimimos si elige detallado o resumido
		echo "<br/>";
		$q = " SELECT Fpades, SUM(Rfpvfp) as Rfpvfp"
		    ."   FROM facturas "
			."  GROUP BY Fpades "
			."  ORDER BY Fpades ";

		$res = mysql_query( $q, $conex ) or die( mysql_errno()." - Error en el query: $q -".mysql_error() );
		$num = mysql_num_rows($res);
		
		echo "<center>";
		echo "<table align=0>";
		$total= 0;
		for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$wclass="fila1";
				else
					$wclass="fila2";
				$row = mysql_fetch_array($res);	
				echo "<tr>";
				echo "<td class=encabezadoTabla>".$row["Fpades"]."</td> <td align=right class=".$wclass.">".number_format($row["Rfpvfp"])."</td>";
				echo "</tr>";
			    $total +=  $row["Rfpvfp"];
			
			}
		echo "<tr>";
		echo "<td class=encabezadoTabla>Total</td> <td align=right class=encabezadoTabla><b>".number_format($total)."</b></td>";
		echo "</tr>";	
		echo "</table>";	
		echo "</center>";
		
		echo "<br/>";
	
		$bandera=1;
		
	 	echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
	
	}
	
}
?>
</body>	
</html>
