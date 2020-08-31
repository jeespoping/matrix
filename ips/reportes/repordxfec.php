<html>
<head>
<title>Reporte de ordenes por fechas</title>
<script type="text/javascript">
	function cerrarVentana()
	{
		window.close()
	}

	//Redirecciona a la pagina inicial
	function inicioReporte(wemp,wfecini,wfecfin,wsede,wordent,wordcan)
	{
	 	document.location.href='repordxfec.php?wemp='+wemp+'&wfecini='+wfecini+'&wfecfin='+wfecfin+'&wsede='+wsede+'&wordent='+wordent+'&wordcan='+wordcan+'&bandera=1';
	}
</script>

</head>
<body>
<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE DE ORDENES POR RANGO DE FECHA                                              *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver por rango de fecha las ordenes entregadas, sin entregar y canceladas y sin cancelar 	    |
//AUTOR				          : Mario Cadavid.                                                                       						|
//FECHA CREACION			  : SEPTIEMBRE 19 DE 2011.                                                                                      |
//FECHA ULTIMA ACTUALIZACION  : SEPTIEMBRE 19 DE 2011.                                                                                      |
//==========================================================================================================================================
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

$institucion = consultarInstitucionPorCodigo($conex, $wemp);
$wbasedato = $institucion->baseDeDatos;
$wentidad = $institucion->nombre;
$wactualiz = 'Sept. 19 de 2011';

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("REPORTE DE ORDENES POR FECHA",$wactualiz,"logo_".$wbasedato);

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

	$institucion = consultarInstitucionPorCodigo($conex, $wemp);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;
	
  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="repordxfec.php";  //nombre del reporte
  	
  	echo "<br>";
  	echo "<form action='repordxfec.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp' value='".$wemp."'>";
  	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

    if (!isset($wfecini) or !isset($wfecfin) or !isset($wsede) or !isset($resultado))
  	{

		echo "<center><table border=0 cellspacing=2 cellpadding=4>";
  		 
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
  			$wordent="";
  			$wordcan="";
		}	
  		
		//Fecha inicial de consulta	
  		echo "<tr>";
  		echo "<td class=fila2 align=right><b>Fecha inicial de entrega: </b></td>";
  		echo "<td class=fila2 align=left>";
		campoFechaDefecto("wfecini", $wfecini);
		echo "</td>";
  		echo "</tr>";
  		
  		//Fecha final de consulta
  		echo "<tr>";
  		echo "<td class=fila2 align=right><b>Fecha final entrega: </b></td>";
  		echo "<td class=fila2 align=left>";
  		campoFechaDefecto("wfecfin", $wfecfin );
  		echo "</td>";
  		echo "</tr>";

		//Sede		
  		echo "<tr>";
  		echo "<td class=fila2 align=right><b>Sede: </b></td>";
  		echo "<td class=fila2 align=left>";
  		echo "<select name='wsede'>";
  		$q=  "SELECT ccocod, ccodes "
  		."    FROM ".$wbasedato."_000003 "
  		."    ORDER by 1";
  		$res1 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
  		$num1 = mysql_num_rows($res1);
  		if ($num1 > 0 )
  		{
			echo "<option value='%'>Todas las sedes</option>";
  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
				$selec = "";
				if($wsede==$row1[0]."-".$row1[1]) $selec = " selected";
  				echo "<option value='".$row1[0]."-".$row1[1]."'".$selec.">".$row1[0]."-".$row1[1]."</option>";
  			}
  		}
  		echo "</select></td>";
  		echo "</tr>";
		
  		// Seleccion de entregadas o sin entregar
		echo "<tr>";
  		echo "<td class=fila2 align=center colspan='2'> <input type='radio' name='wordent' value='1'";
		if($wordent=='1') 
			echo "checked";
		echo "> Entregadas</b> &nbsp;&nbsp;&nbsp;&nbsp; <input type='radio' name='wordent' value='0'";
		if($wordent=='0') 
			echo "checked";
		echo "> Sin entregar</b> &nbsp;&nbsp;&nbsp;&nbsp; <input type='radio' name='wordent' value='%'";
		if($wordent=='%') 
			echo "checked";
		echo "> Todas</b></td>";
  		echo "</tr>";

  		// Seleccion de canceladas
		echo "<tr>";
  		echo "<td class=fila2 align=center colspan='2'> <input type='radio' name='wordcan' value='1'";
		if($wordcan=='1') 
			echo "checked";
		echo "> Facturas canceladas</b> &nbsp;&nbsp;&nbsp;&nbsp; <input type='radio' name='wordcan' value='0'";
		if($wordcan=='0') 
			echo "checked";
		echo "> Facturas sin cancelar</b> &nbsp;&nbsp;&nbsp;&nbsp; <input type='radio' name='wordcan' value='%'";
		if($wordcan=='%') 
			echo "checked";
		echo "> Todas</b></td>";
  		echo "</tr>";

  		echo "<tr align='center'><td colspan=2>";
  		echo "<br><div align='center'><input type='submit' value='Consultar'> &nbsp; | &nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  		echo "</td></tr>";
  		
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    	echo "</table>";
    	echo "";
    	
  	} 
	else 
	{
		//Muestro los parámetros que se ingresaron en la consulta
		echo "<table border=0 cellspacing=2 cellpadding=0 align=center size='300'>"; 
		echo "<tr class='fila2'>";
		echo "<td align=left><strong>&nbsp;Fecha inicial : </strong>".$wfecini."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "<td align=left><strong>&nbsp;Fecha final : </strong>".$wfecfin."&nbsp;&nbsp;&nbsp;&nbsp;</td>";
		echo "</tr>";
		echo "<tr class='fila2'>";
		echo "<td align=left colspan='2'><strong>&nbsp;Sede : </strong>".$wsede;
		if($wsede=='%')
			echo " - Todas las sedes";
		echo "</td>";
		echo "</tr>";

		$ordtipo = "";
		if(isset($wordent) && $wordent=='1')
			$ordtipo .= " Entregadas ";
		elseif(isset($wordent) && $wordent=='0')
			$ordtipo .= " Sin entregar  ";
		else
			$ordtipo .= " Entregadas y sin entregar ";

		if(isset($wordcan) && $wordcan=='1')
			$ordtipo .= " / Canceladas ";
		elseif(isset($wordcan) && $wordcan=='0')
			$ordtipo .= " / Sin cancelar  ";
		else
			$ordtipo .= " / Canceladas y sin cancelar ";

		if($ordtipo != "")
		{
			echo "<tr class='fila2'>";
			echo "<td align=left colspan='2'><strong>&nbsp;Tipo: </strong>".$ordtipo."</td>";
			echo "</tr>";
		}

		echo "</table>";

  		echo "</br>";

  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wsede' value='".$wsede."'>";
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		if(!isset($wordent))
			$wordent = '%';
		
		if(!isset($wordcan))
			$wordcan = '%';

		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wordent\",\"$wordcan\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          

		if(isset($wordent) && $wordent=='1')
			$sqlordent = " AND ordfen != '0000-00-00' ";
		elseif(isset($wordent) && $wordent=='0')
			$sqlordent = " AND ordfen = '0000-00-00' ";
		else
			$sqlordent = "";
		
		if(isset($wordcan) && $wordcan=='1')
			$sqlordcan = " AND fensal = 0 ";
		elseif(isset($wordcan) && $wordcan=='0')
			$sqlordcan = " AND fensal > 0 ";
		else
			$sqlordcan = "";

		$query1 = " SELECT ordfen,ordnro,orddoc,".$wbasedato."_000041.clinom,ordcco,ordffa,ordfac,fenfec, b.Fecha_data AS fecha "
				  ."  FROM ".$wbasedato."_000018 AS a, ".$wbasedato."_000133 AS b LEFT JOIN ".$wbasedato."_000041 "
				  ."    ON b.orddoc = ".$wbasedato."_000041.clidoc"          
				  ." WHERE b.Fecha_data between '".$wfecini."' and '".$wfecfin."'" 
				  ."   AND ordcco LIKE '".$wsede."'"
				  ."   AND ordffa = fenffa"
				  ."   AND ordfac = fenfac"
				  ." ".$sqlordent." "
				  ." ".$sqlordcan." "
				  ." GROUP BY 1,2,3,4,5,6,7,8"
				  ." ORDER BY ordcco,ordfen,ordnro";
		$err1 = mysql_query($query1,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query1 . " - " . mysql_error());
		$num1 = mysql_num_rows($err1);
		//echo $query1."<br>";
		
		$swtitulo='SI';
		$sedeant='';
		$ordant=0;
		$ordfena='';
		$ordnroa=0;
		$orddoca=0;
		$nombrea='';
		$ffaca='';
		$facta=0;
		$fecfa='';
		
		if($num1 > 0)
		{
			echo "<table border=0 align=center>";
			$numOrdenes = 0;
			$numOrdenesSede = 0;
			
			for ($i=1;$i<=$num1;$i++)
			{
			  $i % 2 == 0 ? $wcf = 'fila1' : $wcf = 'fila2';

			  $row1 = mysql_fetch_array($err1);

			  if ($swtitulo=='SI')
			  {
			    $sedeant = $row1[4];
			    echo "<tr><td align=center colspan=8 class='encabezadoTabla'><b>".$sedeant."</b></td></tr>"; 
			    $swtitulo='NO';
				echo "<tr class='encabezadoTabla'><td align=center>Nro orden</td><td align=center>Fecha orden</td><td align=center>Fecha entrega</td><td align=center> Documento paciente </td><td align=center> Nombre paciente </td><td align=center> Fuente factura </td><td align=center> Factura </td><td align=center> Fecha factura </td></tr>";
				 $ordnroa=0;
			  } 
			  
			  if ($sedeant==$row1[4] )
			  {
			   echo "<tr class='".$wcf."'><td align=center>".$row1[1]."</td><td align=center>".$row1['fecha']."</td><td align=center>".$row1[0]."</td><td align=left> ".$row1[2]."</td><td align=left> ".$row1[3]."</td><td align=center>".$row1[5]."</td><td align=center>".$row1[6]."</td><td align=center>".$row1[7]."</td></tr>"; 
		       $numOrdenesSede++;
			  }
			  else
			  {
			   echo "<tr class='encabezadoTabla'><td align='center' colspan='8'> Total ordenes ".$sedeant.": ".$numOrdenesSede."</td></tr>"; 
			   echo "<tr><td alinn=center colspan=8><b>&nbsp;</b></td></tr>";
			   $swtitulo='SI';
			   $ordfena=$row1[0];
			   $ordnroa=$row1[1];
			   $orddoca=$row1[2];
			   $nombrea=$row1[3];
			   $ffaca=$row1[5];
			   $facta=$row1[6];
			   $fecfa=$row1[7];
			   $numOrdenesSede = 0;
			  }	
		      $numOrdenes++;
			}
		    echo "<tr><td alinn=center colspan=8><b>&nbsp;</b></td></tr>";
		    echo "<tr class='encabezadoTabla'><td align='center' colspan='8' height='31'> Total ordenes: ".$numOrdenes."</td></tr>"; 
			echo "</table>";
		} 
		else 
		{
			echo "<div align='center'><b>No se encontraron ordenes con los criterios especificados</b></div>";
		}		

		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wordent\",\"$wordcan\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          

	}
}
liberarConexionBD($conex);
?>
</body>
</html>