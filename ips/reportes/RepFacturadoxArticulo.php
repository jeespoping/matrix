<html>
<head>
<title>REPORTE DE FACTURACIÓN POR ARTICULO</title>
<script type="text/javascript">

	//Envio del formulario
	function enviar(){  
		document.forma.submit();		
	}

	//Redirecciona a la pagina inicial
	function inicioReporte(wemp_pmla,wfecini,wfecfin,wsede,wgrupo,wsubgrupo)
	{
	 	document.location.href='RepFacturadoxArticulo.php?wemp_pmla='+wemp_pmla+'&wfecini='+wfecini+'&wfecfin='+wfecfin+'&wsede='+wsede+'&wgrupo='+wgrupo+'&wsubgrupo='+wsubgrupo+'&bandera=1';
	}

	//Consulta los subgrupos del grupo seleccionado y crea un campo selec para estos
	function consultarSubGrupos()
	{
		var contenedor = document.getElementById('cntSubgrupo');
		var imagen = document.getElementById('imagen1');
		
		var parametros = "consultaAjax=01&basedatos="+document.forms.forma.wbasedato.value+"&grupo=" + document.forms.forma.wgrupo.value;
		
		try{
		ajax=nuevoAjax();
		
		ajax.open("POST", "../../../include/root/comun.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		
		ajax.onreadystatechange=function() 
		{ 
			if (ajax.readyState==4 && ajax.status==200)
			{ 
				contenedor.innerHTML=ajax.responseText;
			} 
		}
		if ( !estaEnProceso(ajax) ) {
			ajax.send(null);
		}
		}catch(e){	}
	}

</script>

</head>
<body onload='javascript:consultarSubGrupos();'>
<?php
include_once("conex.php");
/*BS'D
 * REPORTE FACTURACION POR ARTICULO
 */
//=================================================================================================================================
//PROGRAMA: RepFacturadoxArticulo.php
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\IPS\Reportes\RepFacturadoxArticulo.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+------------------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 		|
//+-------------------+------------------------+------------------------------------------------+
//|  2008-09-01       | Mauricio Sánchez       | creación del script.					 		|
//+-------------------+------------------------+------------------------------------------------+
//|  2011-01-21       | Mario Cadavid          | Se modificó el query quitando una 				+
//|  consulta que habia en el LEFT JOIN y creando una tabla temporal basada en esta consulta.	+
//|  Estos se hizo debido que el reporte estaba muy lento, mejoró notablemente en rapidez.		+
//+-------------------+------------------------+------------------------------------------------+
//|  2011-09-19       | Mario Cadavid          | Se hizo un query para consultar las notas 		+
//|  crédito en ventas y en facturación, y en el ciclo que muestra la lista del reporte se 		+
//|  condicionó para que las notas crédito en ventas no las muestre pero las de facturación si, +
//|  estas notas crédito de facturación se muestran en una nueva columna del reporte llamada 	+
//|  Nota Crédito, también se adicionó una columna llamada Facturado neto para mostrar la 		+
//|  diferencia en facturación con la Nota Crédito	
//|  2012-08-15		 | Camilo Zapata			| se modifico el query y la pantalla para que 	+
//|												  tambien muestre el número de orden asociado +
//|												  al articulo									+
//+-------------------+------------------------+------------------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2012-08-15

/*DESCRIPCION:Este reporte presenta la lista de facturas por centro(s) de costo(s), por detalle y por procedimiento 

TABLAS QUE UTILIZA:
 clisur_000003 Maestro de centros de costos.
 clisur_000004 Maestro de conceptos.
 clisur_000018 Información basica de la factura.	
 clisur_000066 Relación entre conceptos y procedimientos.	
 clisur_000106 Procedimientos.
 
INCLUDES: 
  conex.php = include para conexión mysql            

VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");    
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $resultado = 
=================================================================================================================================*/
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

//Validación de usuario
$usuarioValidado = true;
if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;
$wactualiz = '2012-08-15';

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("REPORTE FACTURACION POR ARTICULOS Y SERVICIOS ",$wactualiz,"logo_".$wbasedato);

//Si el usuario no es válido se informa y no se abre el reporte
if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
} // Fin IF si el usuario no es válido
else //Si el usuario es válido comenzamos con el reporte
{  //Inicio ELSE reporte

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;
	
  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="RepFacturadoxArticulo.php";  //nombre del reporte
  	
  	echo "<br>";
  	echo "<form action='RepFacturadoxArticulo.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

    if (!isset($wfecini) or !isset($wfecfin) or !isset($wsede) or !isset($wgrupo) or !isset($wsubgrupo) or !isset($resultado))
  	{

		echo "<center><table border=0>";
  		 
		//Petición de ingreso de parametros
		echo "<tr>";
		echo "<td height='37' colspan='2'>";
		echo '<p align="left" class="titulo"><strong> &nbsp; Seleccione los datos a consultar &nbsp;  &nbsp; </strong></p>';
		echo "</td></tr>";

		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{  			
 			$wfecini=$wfecha;
  			$wfecfin=$wfecha;
  			$wsede="";
  			$wgrupo="";
  			$wsubgrupo="";
		}	
  		
		//Fecha inicial de consulta	
  		echo "<tr>";
  		echo "<td class=fila2 align=center><b>Fecha inicial : </b>";
  		campoFechaDefecto("wfecini", $wfecini);
  		echo "</td>";
  		
  		//Fecha final de consulta
  		echo "<td class=fila2 align=center><b>Fecha final : </b>";
  		campoFechaDefecto("wfecfin", $wfecfin );
  		echo "</td>";
  		echo "</tr>";

		//Sede		
  		echo "<tr>";
  		echo "<td align=center class=fila2 align=center colspan=2><b>Sede : </b>";
  		echo "<select name='wsede'>";
  		$q=  "SELECT ccocod, ccodes "
  		."    FROM ".$wbasedato."_000003 "
  		."    ORDER by 1";
  		$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());;
  		$num1 = mysql_num_rows($res1);
  		if ($num1 > 0 )
  		{
			echo "<option value='%'>Todas las sedes</option>";
  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
				$selec = "";
				if($wsede==$row1[0]) $selec = " selected";
  				echo "<option value=".$row1[0].$selec.">".$row1[0]."-".$row1[1]."</option>";
  			}
  		}
  		echo "</select></td>";
  		echo "</tr>";

  		//Grupos
  		echo "<tr>";
  		echo "<td align=center colspan=2 class=fila2><b>Grupo : </b>";
  		echo "<select name='wgrupo' style='width: 450px' onchange='javascript:consultarSubGrupos();'>";
  		$q2= "SELECT grucod, grudes "
  		."    FROM ".$wbasedato."_000004 "
  		."    WHERE gruest = 'on' AND Grutab = 'NO APLICA' "
  		."     order by grucod, grudes ";
  		$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());;
  		$num2 = mysql_num_rows($res2);
  		echo "<option value='%'>Todos los grupos</option>";
  		for ($i=1;$i<=$num2;$i++)
  		{
  			$row2 = mysql_fetch_array($res2);
			$selec = "";
			if($wgrupo==$row2[0]) $selec = " selected";
  			echo "<option value=".$row2[0].$selec.">".$row2[0]."-".$row2[1]."</option>";
  		}
  		echo "</select>";
  		
  		echo "</td>";
  		echo "</tr>";
  		
  		//Subgrupos -- Carga por ajax
  		echo "<tr>";
  		echo "<td align=center class=fila2 colspan='2'><b>Subgrupo : </b>";
  		echo "<span id='cntSubgrupo'>";
  		echo "<select name='wsubgrupo' style='width: 450px'>";
  		echo "<option value='%'>Todos los subgrupos</option>";
  		echo "</select>";		
  		echo "</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
  		
  		echo "<tr align='center'><td colspan=3>";
  		echo "<div align='center'><input type='submit' value='Consultar'> &nbsp; | &nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  		echo "</td></tr>";
  		
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    	echo "</table>";
    	echo "";
    	
  	} 
	else 
	{
  		//Consulto la sede
		$q=  "SELECT ccocod, ccodes "
  		."    FROM ".$wbasedato."_000003 "
  		."    WHERE ccocod = '".$wsede."'";
  		$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());;
		$row1 = mysql_fetch_array($res1);
		if($row1[1] && $row1[1]!='') $wsedenom = ' - '.$row1[1];
		else $wsedenom = '';

		// Consulto el grupo
		$q2= "SELECT grucod, grudes "
  		."    FROM ".$wbasedato."_000004 "
  		."    WHERE grucod = '".$wgrupo."'";
  		$res2 = mysql_query($q2,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q2 . " - " . mysql_error());;
		$row2 = mysql_fetch_array($res2);
		if($row2[1] && $row2[1]!='') $wgruponom = ' - '.$row2[1];
		else $wgruponom = '';

		// Consulto el grupo
		$q3= "SELECT Sgrcod, Sgrdes "
  		."    FROM ".$wbasedato."_000005 "
  		."    WHERE Sgrcod = '".$wsubgrupo."'";
  		$res3 = mysql_query($q3,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());;
		$row3 = mysql_fetch_array($res3);
		if($row3[1] && $row3[1]!='') $wsubgruponom = ' - '.$row3[1];
		else $wsubgruponom = '';

		//Muestro los parámetros que se ingresaron en la consulta
		echo "<table border=0 cellspacing=2 cellpadding=0 align=center size='300'>"; 
		echo "<tr class='fila2'>";
		echo "<td align=left><strong>&nbsp;Fecha inicial : </strong>".$wfecini."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "<td align=left><strong>&nbsp;Fecha final : </strong>".$wfecfin."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
		echo "<td align=left colspan='2'><strong>&nbsp;Sede : </strong>".$wsede.$wsedenom;
		if($wsede=='%')
			echo " - Todas las sedes";
		echo "</td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
		echo "<td align=left colspan='2'><strong>&nbsp;Grupo : </strong>".$wgrupo.$wgruponom;
		if($wgrupo=='%')
			echo " - Todos los grupos";
		echo "</td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
		echo "<td align=left colspan='2'><strong>&nbsp;Subgrupo : </strong>".$wsubgrupo.$wsubgruponom;
		if($wsubgrupo=='%')
			echo " - Todos los subgrupos";
		echo "</td>";
		echo "</tr>";
  		echo "<tr><td>&nbsp;</td></tr>";
  		echo "<tr><td align='center' class='fila1' colspan='2'><strong>SIN COPAGOS NI SUBSIDIOS</strong></td></tr>";
		echo "</table>";

  		echo "</br>";

  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wgrupo' value='".$wgrupo."'>";
  		echo "<input type='HIDDEN' NAME= 'wsede' value='".$wsede."'>";
  		echo "<input type='HIDDEN' NAME= 'wsubgrupo' value='".$wsubgrupo."'>";
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wgrupo\",\"$wsubgrupo\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          

		// Borra la tabla temporal
		$qdel = "DROP TABLE IF EXISTS ord";
		$resdel = mysql_query($qdel, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qdel . " - " . mysql_error());
		
		$qord =  " CREATE TEMPORARY TABLE IF NOT EXISTS ord "
				." (INDEX idxfac ( ordfac ) ) "
				." SELECT ordfac, ordffa, ordvel, ordvem, ordcaj, ordnro "
				." FROM ".$wbasedato."_000133 "
				." GROUP BY ordfac ";
		//echo $qord."<br />";	
		$resord = mysql_query($qord, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qord . " - " . mysql_error());
		
		// Consulto las fuentes de las notas crédito por ventas y por facturación - 2011-09-19
		$qfue =  " SELECT Carfue "
				."	 FROM ".$wbasedato."_000040 "
				."  WHERE Carncr = 'on'	"
				." 	  AND Carest = 'on' "
				."	ORDER BY 1 ASC ";
		$resfue = mysql_query($qfue,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qfue . " - " . mysql_error());
		$rowfue = mysql_fetch_row($resfue);
		//echo $qfue."<br>";

		$sql_fuentes = "";
		if($rowfue)
		{
			$sql_fuentes = "AND (";
			do
			{
				$sql_fuentes .= " Rdefue = '".$rowfue[0]."' OR";
			} while($rowfue = mysql_fetch_row($resfue));
			$sql_fuentes .= ") ";
			$sql_fuentes = str_replace("OR)",")",$sql_fuentes);
		}

		// Consulto las notas créditos -  2011-09-19
		$qnc =   "SELECT Rdefue, Rdefac, Rdevco "
				."  FROM ".$wbasedato."_000021 "
				." WHERE Fecha_data BETWEEN '".$wfecini."' AND '".$wfecfin."' "
				.$sql_fuentes
				."   AND Rdeest = 'on' "
				." GROUP BY 1,2 ";
		//echo $qnc."<br>";
		$resnc = mysql_query($qnc, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qnc . " - " . mysql_error());
		$rownc = mysql_fetch_array($resnc);

		// Creo un arreglo con las facturas que tienen nota crédito -  2011-09-19
		if($rownc)
		{
			do
			{
				$indice_nc = strtoupper($rownc[1]);
				$arr_nc[ $indice_nc ] = $rownc[0];
				$arr_vnc[ $indice_nc ] = $rownc[2];
				//echo $arr_nc[ $indice_nc ]."-".$rownc[1]." - ".$arr_vnc[ $indice_nc ]." <br> ";
			} while($rownc = mysql_fetch_row($resnc));
		}

		//Consulta principal del reporte
		$q = "SELECT  
				Artcod, Artnom, Vdecan, ROUND(SUM((Vdevun*Vdecan)-(Vdedes*(1+(Vdepiv /100)))),0) Valor,
      			( IFNULL(  CASE  WHEN Fdecon =  'MT' THEN ordvem END ,  ''  )  )Vendedor_montura,
				( IFNULL(  CASE  WHEN Fdecon IN ('LO','LE') THEN ordvel END ,  ''  )  )Vendedor_lente,
				Fenfac,
				ord.ordcaj,
				Fenfec Fecha_factura, Fdefue, Fdedoc, Fdecco, Fdecon, ord.ordnro
			 FROM 
				".$wbasedato."_000018 
				LEFT JOIN ord ON ord.ordffa = Fenffa AND ord.ordfac = Fenfac,
				".$wbasedato."_000065, ".$wbasedato."_000017, ".$wbasedato."_000016, ".$wbasedato."_000001, ".$wbasedato."_000004
			WHERE Fenfec BETWEEN '".$wfecini."' AND '".$wfecfin."' 
				AND Fenffa = Fdefue
				AND Fenffa = Venffa
				AND Fenfac = Fdedoc 
				AND Fenfac = Vennfa 
				AND Fdeest =  'on' 
				AND Fdecon LIKE '".$wgrupo."' 
				AND Fdecon = SUBSTRING_INDEX( Artgru,  '-', 1  )
				AND Vdeest =  'on' 
				AND Vdenum = Vennum 
				AND Vdeart = Artcod 
				AND Fencco LIKE '".$wsede."' 
				AND Fenest =  'on' 
				AND Grutab = 'NO APLICA'
				AND SUBSTRING( Artgru FROM INSTR( Artgru,  '-'  )  + 1  )  LIKE  '".$wsubgrupo."'
				AND Grucod = Fdecon			
			GROUP BY 
				Artcod, Fenfac
			ORDER BY 
				Fenfac, Artcod 
		";
		//echo $q."<br>";	
	
		$err = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());;
		$num = mysql_num_rows($err);
		
		//Variables acumuladoras
		$acumTotal = 0;
		$acumArticulos = 0;
		$facturado_neto = 0;
		$acumNotasCredito = 0;
		$acumTotalNeto = 0;
		
		if($num > 0)
		{
			//Variables de control
			$row = mysql_fetch_array($err);
			
			$cont1 = 1;

			echo "<table border=0 align=center>";
			$auxfac = $row[6];
			$confac = 0;
			while($cont1 <= $num)
			{
				//  Consulto las fuentes de las notas crédito en ventas - 2011-09-19
				$qfue =  " SELECT Ccofnc "
						."	 FROM ".$wbasedato."_000003 "
						."  WHERE Ccocod LIKE '".$row['Fdecco']."' "
						." 	  AND Ccoest = 'on' ";
				$resfue = mysql_query($qfue,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qfue . " - " . mysql_error());
				$rowfue = mysql_fetch_row($resfue);
				//echo $qfue."<br>";
				$fte_nc_vta = $rowfue[0];
				
				$indice_nc = strtoupper($row[6]);

				// Muestro la fila si la venta no tiene asociada una nota crédito en venta -  2011-09-19
				if( !isset( $arr_nc[ $indice_nc ] ) || $arr_nc[ $indice_nc ] != $fte_nc_vta)
				{
					$cont1 % 2 == 0 ? $class = 'fila1' : $class = 'fila2';
					
					if(isset( $arr_nc[ $indice_nc ] ) && $confac==0)
						$nota_credito = $arr_vnc[ $indice_nc ];
					else
						$nota_credito = 0;
				
					$facturado_neto = $row[3]-$nota_credito;

					//Muestra el titulo de cada columna
					if($cont1 == 1)
					{
						echo "<tr class='encabezadotabla'>";
						echo "<td align=center  rowspan=2><b>CODIGO ARTICULO</b></td>";
						echo "<td align=center  rowspan=2><b>NUMERO ORDEN</b></td>";
						echo "<td align=center  rowspan=2><b>FACTURA</b></td>";
						echo "<td align=center  rowspan=2 nowrap><b>FECHA FACTURA</b></td>";
						echo "<td align=center  rowspan=2><b>CAJA FISICA</b></td>";
						echo "<td align=center  rowspan=2><b>DESCRIPCION</b></td>";
						echo "<td align=center  rowspan=2><b>CANTIDAD</b></td>";
						echo "<td align=center  rowspan=2><b>VALOR</b></td>";
						echo "<td align=center  rowspan=2><b>NOTA CREDITO</b></td>";
						echo "<td align=center  rowspan=2><b>FACTURADO NETO</b></td>";
						echo "<td align=center  colspan=2><b>ASESOR</b></td>";
						echo "</tr>";	
						echo "<tr class='encabezadotabla'>";
						echo "<td align=center><b>MONTURA</b></td>";
						echo "<td align=center><b>LENTE</b></td>";					
						echo "</tr>";
					}				
					
					echo "<tr>";
					echo "<td align=center class=".$class.">".$row[0]."</td>";
					echo "<td align=center class=".$class.">".$row[13]."</td>";
					echo "<td align=center class=".$class.">".$row[6]."</td>";
					echo "<td align=center class=".$class.">".$row[8]."</td>";
					echo "<td align=center class=".$class.">".$row[7]."</td>";				
					echo "<td align=left class=".$class.">".strtoupper($row[1])."</td>";
					echo "<td align=center class=".$class.">".$row[2]."</td>";
					echo "<td align=right class=".$class.">".number_format($row[3],0,'.',',')."</td>";
					echo "<td align=right class=".$class.">".number_format($nota_credito,0,'.',',')."</td>";
					echo "<td align=right class=".$class.">".number_format($facturado_neto,0,'.',',')."</td>";
					echo "<td align=left class=".$class.">".$row[4]."</td>";
					echo "<td align=left class=".$class.">".$row[5]."</td>";
					
					$acumTotal += $row[3];
					$acumArticulos += $row[2];
					$acumNotasCredito += $nota_credito;
					$acumTotalNeto += $facturado_neto;
				}
				$cont1++;
				$row = mysql_fetch_array($err);
				if($auxfac == $row[6])
					$confac++;
				else
					$confac = 0;
				$auxfac = $row[6];
			}
			echo "<tr class='encabezadotabla'>";
			echo "<td align=center  colspan=6><b>Articulos encontrados</b></td>";
			echo "<td align=center><b>".number_format($acumArticulos,0,'.',',')."</b></td>";
			echo "<td align=right><b>".number_format($acumTotal,0,'.',',')."</b></td>";
			echo "<td align=right><b>".number_format($acumNotasCredito,0,'.',',')."</b></td>";
			echo "<td align=right><b>".number_format($acumTotalNeto,0,'.',',')."</b></td>";
			echo "<td align=center colspan=2><b>&nbsp;</b></td>";
			echo "</tr>";
			echo "</table>";
			
		} 
		else 
		{
			echo "<div align='center'><b>No se encontraron articulos facturados con los criterios especificados</b></div>";
		}		
		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wgrupo\",\"$wsubgrupo\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          

	}
}
liberarConexionBD($conex);
?>
</body>
</html>
