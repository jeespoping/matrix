<html>
<head>
<title>Monturas Entregadas X Fecha</title>
<script type="text/javascript">
	//Redirecciona a la pagina inicial
	function inicioReporte(wemp_pmla,wfecini,wfecfin,wsede,wproveedor)
	{
	 	document.location.href='uvgmoenxfe.php?wemp_pmla='+wemp_pmla+'&wfecini='+wfecini+'&wfecfin='+wfecfin+'&wsede='+wsede+'&wproveedor='+wproveedor+'&bandera=1';
	}
</script>

</head>
<body>
<?php
include_once("conex.php");
/*BS'D
 * Monturas Entregadas X Fecha
 */
//=================================================================================================================================
//PROGRAMA: uvgmoenxfe.php
//AUTOR: 

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+------------------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 		|
//+-------------------+------------------------+------------------------------------------------+
//|  2011-09-19       | Mario Cadavid          | Se adicionó rango de fechas, selección de 		+
//|  proveedor, selección de todas las sedes para la consulta, y se aplicaron los css actuales	+
//+-------------------+------------------------+------------------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2011-09-19

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
$wactualiz = 'Sept. 19 de 2011';

//Llamo a la función para formar el encabezado del reporte llevándole Título, Fecha e Imagen o logo
encabezado("Monturas Entregadas X Fecha",$wactualiz,"logo_".$wbasedato);

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
  	$wnomprog="uvgmoenxfe.php";  //nombre del reporte
  	
  	echo "<br>";
  	echo "<form action='uvgmoenxfe.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";

    if (!isset($wfecini) or !isset($wfecfin) or !isset($wsede) or !isset($resultado))
  	{

		echo "<center><table border=0 cellpadding=2 cellspacing=2>";
  		 
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
		}	
  		
		//Fecha inicial de consulta	
  		echo "<tr>";
  		echo "<td class=fila2 align=center><b>Fecha inicial : <br></b>";
  		campoFechaDefecto("wfecini", $wfecini);
  		echo "</td>";
  		
  		//Fecha final de consulta
  		echo "<td class=fila2 align=center><b>Fecha final : <br></b>";
  		campoFechaDefecto("wfecfin", $wfecfin );
  		echo "</td>";
  		echo "</tr>";

		//Sede		
  		echo "<tr>";
  		echo "<td align=center class=fila2 align=center><b>Sede : <br></b>";
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

		//Proveedor		
  		echo "<td align=center class=fila2 align=center><b>Proveedor : <br></b>";
  		echo "<select name='wproveedor'>";
  		/*
		$q = "SELECT SUBSTRING(artprv,1,2), IFNULL(pronom,'') "
  		."     FROM ".$wbasedato."_000001 "
		." 			LEFT JOIN ".$wbasedato."_000006 "
		."				   ON SUBSTRING( artprv, 1, 2  ) = procod AND proest = 'on' "
		."	  WHERE artprv != '' "
		."	    AND artprv != '.' "
		."	    AND artprv != 'NO APLICA' "
  		."    GROUP BY 1 "
  		."    ORDER BY 1 ";
		*/
		$q = "SELECT procod, IFNULL(pronom,'') "
  		."      FROM ".$wbasedato."_000006 "
		."	   WHERE procod != '' "
		."	     AND proest = 'on' "
  		."     GROUP BY 1 "
  		."     ORDER BY 1 ";
  		$res2 = mysql_query($q,$conex)  or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());;
  		$num2 = mysql_num_rows($res2);
		echo "<option value='%'>Todos los proveedores</option>";
  		if ($num2 > 0 )
  		{
  			for ($i=1;$i<=$num2;$i++)
  			{
  				$row2 = mysql_fetch_array($res2);
				$selec = "";
				if($wproveedor==$row2[0]) $selec = " selected";
  				echo "<option value=".$row2[0].$selec.">".$row2[0]."-".$row2[1]."</option>";
  			}
  		}
  		echo "</select></td>";
  		echo "</tr>";

  		echo "<tr align='center'><td colspan=3>";
  		echo "<br><div align='center'><input type='submit' value='Consultar'> &nbsp; | &nbsp; <input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
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

  		//Consulto el proveedor
		$q=  "SELECT procod, pronom "
  		."    FROM ".$wbasedato."_000006 "
  		."    WHERE procod = '".$wproveedor."'";
  		$res2 = mysql_query($q,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());;
		$row2 = mysql_fetch_array($res2);
		if($row2[1] && $row2[1]!='') $wpronom = ' - '.$row2[1];
		else $wpronom = '';

		$sqlpro = "";
		if(isset($wproveedor) && $wproveedor!='%')
			$sqlpro = " AND SUBSTRING(ordref,1,2) LIKE '".$wproveedor."'";
		
		//Muestro los parámetros que se ingresaron en la consulta
		echo "<table border='0' cellspacing='2' cellpadding='0' align='center' size='300'>"; 
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
		echo "<td align=left colspan='2'><strong>&nbsp;Proveedor : </strong>".$wproveedor.$wpronom;
		if($wproveedor=='%')
			echo " - Todos los proveedores";
		echo "</td>";
		echo "</tr>";
		echo "</table>";

  		echo "</br>";

  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wsede' value='".$wsede."'>";
  		echo "<input type='HIDDEN' NAME= 'wproveedor' value='".$wproveedor."'>";
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wproveedor\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          
		
		//Consulta
		$query  = "select ordnro, Fendpa, Fennpa, ordref, Artnom, ordcaj, ordfen, Vdecan, Vdevun, ordfac, Karpro, Fenfec, ordvem from ".$wbasedato."_000133,".$wbasedato."_000018,".$wbasedato."_000016,".$wbasedato."_000017,".$wbasedato."_000001,".$wbasedato."_000007  ";
		$query .= " where ordmon = '2' ";
		$query .= "   and ordfen BETWEEN '".$wfecini."' AND '".$wfecfin."' ";
		$query .= "   and ordfac = fenfac ";
		$query .= "   and Fencco LIKE '".$wsede."' ";
		$query .= "   and ordref = artcod  ";
		$query .= "   and ordfac = vennfa  ";
		$query .= "   and vennum = vdenum  ";
		$query .= "   and ordref = vdeart ";
		$query .= "   and ordref = Karcod ";
		$query .= " ".$sqlpro." ";
		$query .= "   and Karcco LIKE '".$wsede."' ";
		$query .= "  group by 1,2,3,4,5,6,7,8,9,10 ";
		$query .= "  order by ordref,ordnro, Karpro DESC ";
		$err = mysql_query($query,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());;
		$num = mysql_num_rows($err);
		//echo $query;	
	
		if($num > 0)
		{
			echo "<table border='0' align='center'>";
			echo "<tr>";
			echo "<td class='encabezadoTabla'><b>Nro<br> de Orden</b></td>";
			echo "<td class='encabezadoTabla'><b>Nro<br> de Factura </b></td>";
			echo "<td class='encabezadoTabla'><b>Fecha<br> de Factura </b></td>";
			echo "<td class='encabezadoTabla'><b>Doc.<br>Paciente</b></td>";
			echo "<td class='encabezadoTabla'><b>Nombre<br>Paciente</b></td>";
			echo "<td class='encabezadoTabla'><b>Ref.<br>Montura</b></td>";
			echo "<td class='encabezadoTabla'><b>Descripcion</b></td>";
			echo "<td class='encabezadoTabla'><b>Caja </b></td>";
			echo "<td class='encabezadoTabla'><b>Fecha de <br>Entrega</b></td>";
			echo "<td align=center class='encabezadoTabla'><b>Vendedor<br>Montura</b></td>";
			echo "<td align=right class='encabezadoTabla'><b>Cantidad</b></td>";
			echo "<td align=right class='encabezadoTabla'><b>Vlr.<br>Unitario</b></td>";
			echo "<td align=right class='encabezadoTabla'><b>Vlr.<br>Total</b></td>";
			echo "<td align=right class='encabezadoTabla'><b>Costo<br>Promedio</b></td>";
			echo "</tr>"; 
			$t=array();
			$t[0] = 0;
			$t[1] = 0;
			$t[2] = 0;
			$tp=array();
			$clave="";

			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($clave != substr($row[3],0,2))
				{
					if($i > 0)
					{
						$qpro = " SELECT Procod, IFNULL(Pronom,'')
								 FROM ".$wbasedato."_000006
								WHERE Procod = '".$clave."'
								  AND Proest = 'on' ;";
						$respro = mysql_query($qpro, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qpro . " - " . mysql_error());
						$rspro = mysql_fetch_array($respro);
						$proveedor = $rspro[1];

						echo "<tr><td  class='encabezadoTabla' colspan=10 align=center><b>SUBTOTAL PROVEEDOR: ".$clave."-".$proveedor."</b></td><td class='encabezadoTabla' align=right><b>".number_format($tp[0],0,'.',',')."</b></td><td class='encabezadoTabla' align=right></td><td class='encabezadoTabla' align=right><b>".number_format($tp[1],0,'.',',')."</b></td><td class='encabezadoTabla' align=right><b>".number_format($tp[2],0,'.',',')."</b></td></tr>";
					}
					$tp[0] = 0;
					$tp[1] = 0;
					$tp[2] = 0;
					$clave=substr($row[3],0,2);
				}
				if($i % 2 == 0)
					$clase="fila1";
				else
					$clase="fila2";
				echo "<tr>";
				echo "<td class=".$clase.">".$row[0]."</td>";
				echo "<td class=".$clase.">".$row[9]."</td>";
				echo "<td class=".$clase.">".$row[11]."</td>";
				echo "<td class=".$clase.">".$row[1]."</td>";
				echo "<td class=".$clase.">".$row[2]."</td>";
				echo "<td class=".$clase.">".$row[3]."</td>";
				echo "<td class=".$clase.">".$row[4]."</td>";
				echo "<td class=".$clase.">".$row[5]."</td>";
				echo "<td class=".$clase.">".$row[6]."</td>";
				echo "<td class=".$clase.">".$row[12]."</td>";
				$t[0]+=$row[7];
				$tp[0]+=$row[7];
				echo "<td class=".$clase." align=right>".number_format($row[7],0,'.',',')."</td>";
				echo "<td class=".$clase." align=right>".number_format($row[8],0,'.',',')."</td>";
				$vtot=$row[7]*$row[8];
				$t[1]+=$vtot;
				$tp[1]+=$vtot;
				echo "<td class=".$clase." align=right>".number_format($vtot,0,'.',',')."</td>";
				$t[2]+=$row[10];
				$tp[2]+=$row[10];
				echo "<td class=".$clase." align=right>".number_format($row[10],0,'.',',')."</td>";
				echo "</tr>"; 
			}
			$qpro = " SELECT Procod, IFNULL(Pronom,'')
					 FROM ".$wbasedato."_000006
					WHERE Procod = '".$clave."'
					  AND Proest = 'on' ;";
			$respro = mysql_query($qpro, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $qpro . " - " . mysql_error());
			$rspro = mysql_fetch_array($respro);
			$proveedor = $rspro[1];

			echo "<tr><td  class='encabezadoTabla' colspan=10 align=center><b>SUBTOTAL PROVEEDOR: ".$clave."-".$proveedor."</b></td><td class='encabezadoTabla' align=right><b>".number_format($tp[0],0,'.',',')."</b></td><td class='encabezadoTabla' align=right></td><td class='encabezadoTabla' align=right><b>".number_format($tp[1],0,'.',',')."</b></td><td class='encabezadoTabla' align=right><b>".number_format($tp[2],0,'.',',')."</b></td></tr>";
			echo "<tr class='encabezadoTabla' height='31'><td colspan=10 align=center><b>TOTALES</b></td><td align=right><b>".number_format($t[0],0,'.',',')."</b></td><td align=right></td><td align=right><b>".number_format($t[1],0,'.',',')."</b></td><td align=right><b>".number_format($t[2],0,'.',',')."</b></td></tr>";
			echo "</table>"; 
		} 
		else 
		{
			echo "<div align='center'><b>No se encontraron registros con los criterios especificados</b></div>";
		}		

		//Botones "Retornar" y "Cerrar ventana"
		echo "<br /><p align='center'><input type='button' value='Retornar' onClick='javascript:inicioReporte(\"$wemp_pmla\",\"$wfecini\",\"$wfecfin\",\"$wsede\",\"$wproveedor\");'>&nbsp;|&nbsp;<input type='button' value='Cerrar ventana' onclick='javascript:cerrarVentana();'></p>";          

	}
}
liberarConexionBD($conex);
?>
</body>
</html>
